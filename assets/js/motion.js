(() => {
  "use strict";

  const root = document.documentElement;
  const reducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)");
  const progress = document.querySelector("[data-scroll-progress]");
  let scrollFrame = 0;

  const updateProgress = () => {
    scrollFrame = 0;
    if (!progress) return;
    const scrollRange = document.documentElement.scrollHeight - window.innerHeight;
    const proportion = scrollRange > 0 ? Math.min(1, Math.max(0, window.scrollY / scrollRange)) : 0;
    progress.style.transform = `scaleX(${proportion})`;
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
    ".hero-copy", ".hero-visual", ".section-heading", ".toolkit-heading",
    ".toolkit-group", ".toolkit-category", ".education-card", ".education-entry",
    ".skill-card", ".credential-card", ".contact-copy", ".contact-form",
    ".footer-top", ".footer-socials", "[data-reveal]",
  ].join(",")))).filter((element) => !element.closest("[data-work-gallery]") &&
    (!element.closest("[data-project-card]") || element.classList.contains("project-cover-card")));

  if (!candidates.length || reducedMotion.matches || typeof IntersectionObserver !== "function") return;

  let observer;
  const reveal = (element) => {
    element.classList.add("is-revealed");
    observer?.unobserve(element);
  };

  try {
    observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) reveal(entry.target);
      });
    }, { rootMargin: "0px 0px -24px 0px", threshold: .06 });

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
      candidates.forEach(reveal);
    });
  } catch {
    // An unsupported observer must never make any content inaccessible.
    observer?.disconnect();
    candidates.forEach(reveal);
    root.classList.remove("motion-ready");
  }
})();
