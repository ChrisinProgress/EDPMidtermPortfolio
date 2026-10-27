(() => {
  "use strict";

  const reducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)");

  document.querySelectorAll("[data-toolkit-carousel]").forEach((carousel) => {
    const track = carousel.querySelector("[data-toolkit-track]");
    const items = Array.from(carousel.querySelectorAll("[data-toolkit-item]"));
    const previous = carousel.querySelector("[data-toolkit-prev]");
    const next = carousel.querySelector("[data-toolkit-next]");
    const pagination = carousel.querySelector("[data-toolkit-pagination]");
    const status = carousel.querySelector("[data-toolkit-status]");
    if (!track || !items.length || !previous || !next || !pagination) return;

    let stride = 1;
    let visibleCount = items.length;
    let maximum = 0;
    let pageOffsets = [0];
    let dots = [];
    let scrollFrame = 0;
    let resizeFrame = 0;
    let settleTimer = 0;
    let requestedPosition = null;

    const clamp = (position) => Math.max(0, Math.min(position, maximum));

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

    const updateStatus = () => {
      if (!status) return;
      const first = Math.min(items.length - 1, Math.round(clamp(track.scrollLeft) / stride));
      const last = Math.min(items.length, first + visibleCount);
      const message = `Tools ${first + 1}\u2013${last} of ${items.length}`;
      if (status.textContent !== message) status.textContent = message;
    };

    const settle = () => {
      requestedPosition = null;
      updateControls();
      updateStatus();
    };

    const scheduleSettle = () => {
      window.clearTimeout(settleTimer);
      settleTimer = window.setTimeout(settle, 160);
    };

    const moveTo = (position) => {
      requestedPosition = clamp(position);
      track.scrollTo({
        left: requestedPosition,
        behavior: reducedMotion.matches ? "auto" : "smooth",
      });
      scheduleSettle();
    };

    const moveBy = (distance) => moveTo((requestedPosition ?? track.scrollLeft) + distance);

    const measure = () => {
      // Preserve the leading tool when the responsive card count changes.
      const leadingItem = Math.round(clamp(track.scrollLeft) / stride);
      const firstBounds = items[0].getBoundingClientRect();
      const nextBounds = items[1]?.getBoundingClientRect();
      stride = nextBounds ? nextBounds.left - firstBounds.left : firstBounds.width;
      if (stride <= 0 || track.clientWidth <= 0) return;
      const gap = Math.max(0, stride - firstBounds.width);
      visibleCount = Math.max(1, Math.min(items.length, Math.floor((track.clientWidth + gap + 1) / stride)));
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
        dot.className = "toolkit-page-dot";
        dot.dataset.toolkitPage = String(index);
        dot.setAttribute("aria-label", `Show toolkit page ${index + 1} of ${pageOffsets.length}`);
        if (track.id) dot.setAttribute("aria-controls", track.id);
        dot.addEventListener("click", () => moveTo(offset));
        pagination.append(dot);
        return dot;
      });
      pagination.hidden = pageOffsets.length < 2;
      carousel.classList.toggle("has-overflow", maximum > 2);
      requestedPosition = null;
      track.scrollTo({ left: clamp(leadingItem * stride), behavior: "instant" });
      updateControls();
      updateStatus();
      if (focusedPage >= 0 && !pagination.hidden) {
        dots[Math.min(focusedPage, dots.length - 1)].focus({ preventScroll: true });
      }
    };

    previous.addEventListener("click", () => moveBy(-visibleCount * stride));
    next.addEventListener("click", () => moveBy(visibleCount * stride));

    track.addEventListener("keydown", (event) => {
      if (event.altKey || event.ctrlKey || event.metaKey || event.shiftKey) return;
      if (event.target !== track && event.target.closest("a, button, input, select, textarea, [contenteditable]")) return;
      switch (event.key) {
        case "ArrowLeft": moveBy(-stride); break;
        case "ArrowRight": moveBy(stride); break;
        case "Home": moveTo(0); break;
        case "End": moveTo(maximum); break;
        default: return;
      }
      event.preventDefault();
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
