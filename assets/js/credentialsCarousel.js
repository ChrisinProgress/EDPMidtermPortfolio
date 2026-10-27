(() => {
  "use strict";

  const reducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)");

  document.querySelectorAll("[data-credentials-carousel]").forEach((carousel) => {
    const track = carousel.querySelector("[data-credentials-track]");
    const cards = Array.from(carousel.querySelectorAll("[data-credential-card]"));
    const previous = carousel.querySelector("[data-credentials-prev]");
    const next = carousel.querySelector("[data-credentials-next]");
    const pagination = carousel.querySelector("[data-credentials-pagination]");
    const status = carousel.querySelector("[data-credentials-status]");
    const controls = carousel.querySelector("[data-credentials-controls]");
    if (!track || !cards.length || !previous || !next || !pagination) return;

    let stride = 1;
    let visibleCount = cards.length;
    let maximum = 0;
    let pageOffsets = [0];
    let dots = [];
    let scrollFrame = 0;
    let resizeFrame = 0;
    let settleTimer = 0;
    let requestedPosition = null;

    const clamp = (position) => Math.max(0, Math.min(position, maximum));
    const firstVisible = (position) => Math.min(cards.length - 1, Math.round(clamp(position) / stride));
    const rangeLabel = (position) => {
      const first = firstVisible(position) + 1;
      const last = Math.min(cards.length, first + visibleCount - 1);
      return first === last ? `${first} of ${cards.length}` : `${first}\u2013${last} of ${cards.length}`;
    };

    const updateControls = () => {
      const position = clamp(track.scrollLeft);
      previous.disabled = position <= 2;
      next.disabled = position >= maximum - 2;
      const currentPage = pageOffsets.reduce((nearest, offset, index) => (
        Math.abs(offset - position) < Math.abs(pageOffsets[nearest] - position) ? index : nearest
      ), 0);
      dots.forEach((dot, index) => {
        if (index === currentPage) dot.setAttribute("aria-current", "true");
        else dot.removeAttribute("aria-current");
      });
    };

    const settle = () => {
      requestedPosition = null;
      updateControls();
      if (status) {
        const message = rangeLabel(track.scrollLeft);
        if (status.textContent !== message) status.textContent = message;
      }
    };

    const scheduleSettle = () => {
      window.clearTimeout(settleTimer);
      settleTimer = window.setTimeout(settle, 160);
    };

    const moveTo = (position) => {
      requestedPosition = clamp(position);
      track.scrollTo({
        left: requestedPosition,
        behavior: reducedMotion.matches ? "instant" : "smooth",
      });
      scheduleSettle();
    };

    const moveBy = (distance) => moveTo((requestedPosition ?? track.scrollLeft) + distance);

    const measure = () => {
      const leadingCard = firstVisible(track.scrollLeft);
      const firstBounds = cards[0].getBoundingClientRect();
      const nextBounds = cards[1]?.getBoundingClientRect();
      const measuredStride = nextBounds ? nextBounds.left - firstBounds.left : firstBounds.width;
      if (measuredStride <= 0 || track.clientWidth <= 0) return;
      stride = measuredStride;
      const gap = Math.max(0, stride - firstBounds.width);
      visibleCount = Math.max(1, Math.min(cards.length, Math.floor((track.clientWidth + gap + 1) / stride)));
      maximum = Math.max(0, track.scrollWidth - track.clientWidth);
      pageOffsets = [0];
      const pageWidth = visibleCount * stride;
      for (let position = pageWidth; position < maximum - 2; position += pageWidth) {
        pageOffsets.push(position);
      }
      if (maximum > 2) pageOffsets.push(maximum);

      const focusedPage = dots.indexOf(document.activeElement);
      pagination.replaceChildren();
      dots = pageOffsets.map((offset, index) => {
        const dot = document.createElement("button");
        dot.type = "button";
        dot.className = "credential-page-dot";
        dot.dataset.credentialsPage = String(index);
        dot.setAttribute("aria-label", `Show credentials ${rangeLabel(offset)}`);
        if (track.id) dot.setAttribute("aria-controls", track.id);
        dot.addEventListener("click", () => moveTo(offset));
        pagination.append(dot);
        return dot;
      });

      const overflowing = maximum > 2;
      previous.hidden = !overflowing;
      next.hidden = !overflowing;
      pagination.hidden = !overflowing;
      if (controls) controls.hidden = !overflowing;
      carousel.classList.toggle("has-overflow", overflowing);
      requestedPosition = null;
      track.scrollTo({ left: clamp(leadingCard * stride), behavior: "instant" });
      settle();
      if (focusedPage >= 0 && overflowing) {
        dots[Math.min(focusedPage, dots.length - 1)].focus({ preventScroll: true });
      }
    };

    previous.addEventListener("click", () => moveBy(-visibleCount * stride));
    next.addEventListener("click", () => moveBy(visibleCount * stride));

    track.addEventListener("keydown", (event) => {
      if (event.defaultPrevented || event.altKey || event.ctrlKey || event.metaKey || event.shiftKey) return;
      if (event.target.closest("button, input, select, textarea, [contenteditable]")) return;
      const focusedLink = event.target.closest("a[data-preview], a[data-open-credential]");
      if (event.target !== track && !focusedLink) return;
      if (focusedLink) {
        const currentCard = cards.indexOf(focusedLink.closest("[data-credential-card]"));
        if (currentCard < 0) return;
        let targetCard = currentCard;
        switch (event.key) {
          case "ArrowLeft": targetCard = Math.max(0, currentCard - 1); break;
          case "ArrowRight": targetCard = Math.min(cards.length - 1, currentCard + 1); break;
          case "Home": targetCard = 0; break;
          case "End": targetCard = cards.length - 1; break;
          default: return;
        }
        event.preventDefault();
        const selector = focusedLink.hasAttribute("data-open-credential") ? "[data-open-credential]" : "[data-preview]";
        cards[targetCard].querySelector(selector)?.focus({ preventScroll: true });
        if (event.key === "Home") moveTo(0);
        else if (event.key === "End") moveTo(maximum);
        return;
      }
      switch (event.key) {
        case "ArrowLeft": moveBy(-stride); break;
        case "ArrowRight": moveBy(stride); break;
        case "Home": moveTo(0); break;
        case "End": moveTo(maximum); break;
        default: return;
      }
      event.preventDefault();
    });

    // Reveal a tabbed-to card along the carousel axis without moving the page.
    track.addEventListener("focusin", (event) => {
      const card = event.target.closest("[data-credential-card]");
      if (!card || !track.contains(card)) return;
      const bounds = card.getBoundingClientRect();
      const viewport = track.getBoundingClientRect();
      let position = track.scrollLeft;
      if (bounds.left < viewport.left - 1) position += bounds.left - viewport.left;
      else if (bounds.right > viewport.right + 1) position += bounds.right - viewport.right;
      else return;
      requestedPosition = null;
      track.scrollTo({ left: clamp(position), behavior: "instant" });
      settle();
    });

    track.addEventListener("scroll", () => {
      if (!scrollFrame) {
        scrollFrame = window.requestAnimationFrame(() => {
          updateControls();
          scrollFrame = 0;
        });
      }
      scheduleSettle();
    }, { passive: true });
    track.addEventListener("scrollend", settle);
    track.addEventListener("pointerdown", () => { requestedPosition = null; }, { passive: true });
    track.addEventListener("wheel", () => { requestedPosition = null; }, { passive: true });

    const scheduleMeasure = () => {
      if (resizeFrame) return;
      resizeFrame = window.requestAnimationFrame(() => {
        resizeFrame = 0;
        measure();
      });
    };
    if ("ResizeObserver" in window) new ResizeObserver(scheduleMeasure).observe(track);
    else window.addEventListener("resize", scheduleMeasure, { passive: true });

    measure();
    carousel.classList.add("is-enhanced");
  });
})();
