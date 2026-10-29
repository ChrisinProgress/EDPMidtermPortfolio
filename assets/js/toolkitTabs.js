/* Retained entry-point filename; all tools now share a single continuous ribbon. */
(() => {
  "use strict";
  const reducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)");
  document.querySelectorAll("[data-toolkit-collection]").forEach(section => {
    const row = section.querySelector("[data-toolkit-row]");
    const viewport = section.querySelector("[data-toolkit-viewport]");
    const strip = section.querySelector("[data-toolkit-strip]");
    const list = section.querySelector("[data-toolkit-list]");
    const toggle = section.querySelector("[data-toolkit-motion-toggle]");
    const label = section.querySelector("[data-toolkit-motion-label]");
    if (!row || !viewport || !strip || !list || !toggle || !label) return;

    let paused = false;
    let period = 0;
    let duration = 90;
    const currentOffset = () => {
      const transform = getComputedStyle(strip).transform;
      if (transform === "none") return viewport.scrollLeft;
      const matrix = new DOMMatrixReadOnly(transform);
      return Math.abs(matrix.m41) % (period || 1);
    };
    const setControls = () => {
      toggle.hidden = reducedMotion.matches;
      toggle.setAttribute("aria-pressed", String(paused));
      toggle.setAttribute("aria-label", (paused ? "Play" : "Pause") + " toolkit motion");
      label.textContent = paused ? "Play motion" : "Pause motion";
    };
    const configure = () => {
      const offset = row.classList.contains("is-moving") ? currentOffset() : viewport.scrollLeft;
      row.classList.remove("is-moving");
      strip.querySelectorAll("[data-toolkit-repeat]").forEach(copy => copy.remove());
      const gap = parseFloat(getComputedStyle(strip).columnGap) || 16;
      period = list.getBoundingClientRect().width + gap;
      duration = Math.max(60, period / 34);
      if (!(period > 0)) return;
      strip.style.setProperty("--toolkit-loop-width", period + "px");
      strip.style.setProperty("--toolkit-loop-duration", duration + "s");
      if (!reducedMotion.matches && !paused) {
        const copy = list.cloneNode(true);
        copy.removeAttribute("data-toolkit-list");
        copy.dataset.toolkitRepeat = "";
        copy.setAttribute("aria-hidden", "true");
        copy.inert = true;
        copy.querySelectorAll("[data-toolkit-item]").forEach(item => item.removeAttribute("data-toolkit-item"));
        strip.append(copy);
        viewport.scrollLeft = 0;
        strip.style.setProperty("--toolkit-loop-delay", (-((offset % period) / period) * duration) + "s");
        row.classList.add("is-moving");
      } else {
        viewport.scrollLeft = Math.min(offset, viewport.scrollWidth - viewport.clientWidth);
      }
      setControls();
    };
    const pauseForBrowsing = () => {
      if (paused || reducedMotion.matches) return;
      paused = true;
      configure();
    };
    toggle.addEventListener("click", () => { paused = !paused; configure(); });
    viewport.addEventListener("focus", pauseForBrowsing);
    viewport.addEventListener("pointerdown", event => { if (event.pointerType !== "mouse") pauseForBrowsing(); });
    const updateVisibility = () => section.classList.toggle("is-background", document.hidden);
    document.addEventListener("visibilitychange", updateVisibility);
    updateVisibility();
    if (typeof ResizeObserver === "function") new ResizeObserver(configure).observe(viewport);
    else window.addEventListener("resize", configure);
    if (typeof IntersectionObserver === "function") {
      new IntersectionObserver(entries => row.classList.toggle("is-offscreen", !entries[0].isIntersecting), { threshold: 0 }).observe(row);
    }
    reducedMotion.addEventListener("change", configure);
    configure();
  });
})();
