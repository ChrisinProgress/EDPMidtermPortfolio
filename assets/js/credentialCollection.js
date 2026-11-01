(() => {
  "use strict";
  document.querySelectorAll("[data-credential-collection]").forEach(section => {
    const cards = Array.from(section.querySelectorAll("[data-credential-card]"));
    if (!cards.length) return;
    section.classList.add("is-exhibition");

    const timeline = section.querySelector(".credential-timeline");
    const reducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)");
    let railTop = 0;
    let railHeight = 1;
    let nodeCenters = [];
    let scrollFrame = 0;
    const updateRail = () => {
      scrollFrame = 0;
      if (!timeline) return;
      const readingPosition = window.innerHeight * .64 - timeline.getBoundingClientRect().top;
      const progress = reducedMotion.matches ? 1 : Math.min(1, Math.max(0, (readingPosition - railTop) / railHeight));
      timeline.style.setProperty("--credential-scroll", progress.toFixed(4));
      cards.forEach((card, index) => card.classList.toggle("is-reached", reducedMotion.matches || readingPosition >= nodeCenters[index]));
    };
    const scheduleRail = () => {
      if (scrollFrame || document.hidden) return;
      scrollFrame = window.requestAnimationFrame(updateRail);
    };
    const measureRail = () => {
      if (!timeline) return;
      const timelineTop = timeline.getBoundingClientRect().top;
      nodeCenters = cards.map(card => {
        const node = card.querySelector(".credential-node");
        const bounds = node.getBoundingClientRect();
        return bounds.top + bounds.height / 2 - timelineTop;
      });
      railTop = nodeCenters[0];
      railHeight = Math.max(1, nodeCenters[nodeCenters.length - 1] - railTop);
      timeline.style.setProperty("--credential-rail-top", railTop + "px");
      timeline.style.setProperty("--credential-rail-height", railHeight + "px");
      scheduleRail();
    };
    if (timeline) {
      window.addEventListener("scroll", scheduleRail, { passive: true });
      window.addEventListener("resize", measureRail);
      window.addEventListener("pageshow", measureRail);
      document.addEventListener("visibilitychange", scheduleRail);
      reducedMotion.addEventListener("change", scheduleRail);
      if (typeof ResizeObserver === "function") new ResizeObserver(measureRail).observe(timeline);
      document.fonts?.ready.then(measureRail);
      measureRail();
    }

    const dialog = section.querySelector("[data-credential-dialog]");
    const title = dialog?.querySelector("[data-credential-dialog-title]");
    const body = dialog?.querySelector("[data-credential-dialog-body]");
    const close = dialog?.querySelector("[data-credential-dialog-close]");
    if (!dialog || !title || !body || !close || typeof dialog.showModal !== "function") return;
    let returnFocus = null;

    cards.forEach(card => {
      const details = card.querySelector("[data-credential-details]");
      const trigger = details?.querySelector("[data-credential-detail-trigger]");
      const content = details?.querySelector("[data-credential-detail-content]");
      if (!trigger || !content) return;
      trigger.setAttribute("aria-haspopup", "dialog");
      trigger.addEventListener("click", event => {
        if (event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;
        event.preventDefault();
        returnFocus = trigger;
        title.textContent = card.querySelector("h3")?.textContent || "Certification details";
        const copy = content.cloneNode(true);
        copy.querySelectorAll("img").forEach(image => { image.loading = "eager"; });
        body.replaceChildren(copy);
        dialog.showModal();
        document.body.classList.add("credential-detail-open");
        dialog.scrollTop = 0;
        close.focus({ preventScroll: true });
      });
    });
    close.addEventListener("click", () => dialog.close());
    dialog.addEventListener("click", event => {
      if (event.target !== dialog) return;
      const bounds = dialog.getBoundingClientRect();
      if (event.clientX < bounds.left || event.clientX > bounds.right || event.clientY < bounds.top || event.clientY > bounds.bottom) dialog.close();
    });
    dialog.addEventListener("close", () => {
      document.body.classList.remove("credential-detail-open");
      returnFocus?.focus({ preventScroll: true });
      returnFocus = null;
    });
  });
})();
