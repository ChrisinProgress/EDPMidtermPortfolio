(() => {
  "use strict";

  const root = document.documentElement;
  const reducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)");
  const progress = document.querySelector("[data-scroll-progress]");
  const zoomingElements = new Set();
  let scrollFrame = 0;

  const updateProgress = () => {
    scrollFrame = 0;
    if (!progress) return;
    const scrollRange = document.documentElement.scrollHeight - window.innerHeight;
    const proportion = scrollRange > 0 ? Math.min(1, Math.max(0, window.scrollY / scrollRange)) : 0;
    progress.style.transform = `scaleX(${proportion})`;
    if (!reducedMotion.matches) {
      zoomingElements.forEach(element => {
        const bounds = element.getBoundingClientRect();
        const distance = Math.min(1, Math.abs(bounds.top + bounds.height / 2 - window.innerHeight / 2) / (window.innerHeight * .8));
        element.style.setProperty("--scroll-zoom", (1 - distance * .03).toFixed(4));
      });
    }
  };

  const scheduleProgress = () => {
    if (scrollFrame || document.hidden) return;
    scrollFrame = window.requestAnimationFrame(updateProgress);
  };

  if (progress) {
    window.addEventListener("scroll", scheduleProgress, { passive: true });
    window.addEventListener("resize", scheduleProgress);
    window.addEventListener("load", scheduleProgress, { once: true });
    window.addEventListener("pageshow", scheduleProgress);
    if (typeof ResizeObserver === "function") {
      new ResizeObserver(scheduleProgress).observe(document.body);
    }
    updateProgress();
  }

  const updateVisibility = () => {
    root.classList.toggle("motion-paused", document.hidden);
    if (!document.hidden) scheduleProgress();
  };
  document.addEventListener("visibilitychange", updateVisibility);
  updateVisibility();

  // Individual translate leaves existing card hover and gallery transforms intact.
  const candidates = Array.from(new Set(document.querySelectorAll([
    ".hero-copy", ".hero-visual", ".section-heading", ".toolkit-heading-row",
    ".toolkit-group", ".toolkit-category", ".education-card", ".education-entry",
    ".about-visual", ".about-copy", ".skill-card", ".credential-panel", ".contact-copy", ".contact-form",
    ".footer-top", ".footer-socials", "[data-reveal]",
  ].join(",")))).filter((element) => !element.closest("[data-work-gallery]") &&
    (!element.closest("[data-project-card]") || element.classList.contains("project-cover-card")));

  if (!candidates.length || reducedMotion.matches || typeof IntersectionObserver !== "function") return;

  let observer;
  const reveal = (element) => {
    element.classList.add("is-revealed");
  };

  try {
    observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          reveal(entry.target);
          if (entry.target.matches(".about-visual, .skill-card, .credential-panel")) zoomingElements.add(entry.target);
        } else {
          zoomingElements.delete(entry.target);
          entry.target.style.removeProperty("--scroll-zoom");
          if (!entry.target.contains(document.activeElement)) entry.target.classList.remove("is-revealed");
        }
      });
      scheduleProgress();
    }, { rootMargin: "0px", threshold: 0 });

    const siblingCounts = new WeakMap();
    candidates.forEach((element) => {
      const siblingIndex = siblingCounts.get(element.parentElement) || 0;
      siblingCounts.set(element.parentElement, siblingIndex + 1);
      element.style.setProperty("--reveal-delay", `${Math.min(siblingIndex, 4) * 45}ms`);
      element.classList.add("motion-reveal");
      observer.observe(element);
    });
    root.classList.add("motion-ready");

    document.addEventListener("focusin", (event) => {
      let element = event.target.closest?.(".motion-reveal");
      while (element) {
        reveal(element);
        element = element.parentElement?.closest(".motion-reveal");
      }
    });

    reducedMotion.addEventListener("change", () => {
      if (!reducedMotion.matches) return;
      observer.disconnect();
      zoomingElements.forEach(element => element.style.removeProperty("--scroll-zoom"));
      zoomingElements.clear();
      candidates.forEach(reveal);
    });
  } catch {
    // An unsupported observer must never make any content inaccessible.
    observer?.disconnect();
    candidates.forEach(reveal);
    root.classList.remove("motion-ready");
  }
})();
