/* Retained filename: All is a ribbon; selected categories are still grids. */
(() => {
  "use strict";
  const reducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)");
  document.querySelectorAll("[data-toolkit-collection]").forEach(section => {
    const row = section.querySelector("[data-toolkit-row]");
    const viewport = section.querySelector("[data-toolkit-viewport]");
    const strip = section.querySelector("[data-toolkit-strip]");
    const list = section.querySelector("[data-toolkit-list]");
    const toggle = section.querySelector("[data-toolkit-motion-toggle]");
    const motionLabel = section.querySelector("[data-toolkit-motion-label]");
    const controls = section.querySelector("[data-toolkit-filter-controls]");
    const results = section.querySelector("[data-toolkit-results]");
    const filters = Array.from(section.querySelectorAll("[data-toolkit-filter]"));
    const items = Array.from(list?.querySelectorAll("[data-toolkit-item]") || []);
    if (!row || !viewport || !strip || !list || !toggle || !motionLabel || !controls || !results || !items.length) return;

    let activeCategory = "all";
    let paused = false;
    let period = 0;
    let duration = 90;
    let allOffset = 0;
    let observedWidth = -1;
    let resizeFrame = 0;
    const currentOffset = () => {
      const transform = getComputedStyle(strip).transform;
      if (transform === "none") return viewport.scrollLeft;
      const matrix = new DOMMatrixReadOnly(transform);
      return Math.abs(matrix.m41) % (period || 1);
    };
    const removeRepeats = () => strip.querySelectorAll("[data-toolkit-repeat]").forEach(copy => copy.remove());
    const setControls = () => {
      toggle.hidden = activeCategory !== "all" || reducedMotion.matches;
      toggle.setAttribute("aria-pressed", String(paused));
      toggle.setAttribute("aria-label", (paused ? "Play" : "Pause") + " toolkit motion");
      motionLabel.textContent = paused ? "Play motion" : "Pause motion";
      filters.forEach(button => {
        const selected = button.dataset.toolkitFilter === activeCategory;
        button.classList.toggle("is-active", selected);
        button.setAttribute("aria-pressed", String(selected));
      });
    };
    const configure = (offset = activeCategory === "all" ? currentOffset() : allOffset) => {
      row.classList.remove("is-moving");
      removeRepeats();
      if (activeCategory !== "all") {
        viewport.scrollLeft = 0;
        setControls();
        return;
      }
      const gap = parseFloat(getComputedStyle(strip).columnGap) || 16;
      period = list.getBoundingClientRect().width + gap;
      duration = Math.max(60, period / 34);
      if (!(period > 0)) { setControls(); return; }
      allOffset = offset % period;
      strip.style.setProperty("--toolkit-loop-width", period + "px");
      strip.style.setProperty("--toolkit-loop-duration", duration + "s");
      if (!reducedMotion.matches && !paused) {
        const copy = list.cloneNode(true);
        copy.removeAttribute("data-toolkit-list");
        copy.removeAttribute("aria-label");
        copy.dataset.toolkitRepeat = "";
        copy.setAttribute("aria-hidden", "true");
        copy.inert = true;
        copy.querySelectorAll("[data-toolkit-item]").forEach(item => item.removeAttribute("data-toolkit-item"));
        strip.append(copy);
        viewport.scrollLeft = 0;
        strip.style.setProperty("--toolkit-loop-delay", (-((allOffset / period) * duration)) + "s");
        row.classList.add("is-moving");
      } else {
        viewport.scrollLeft = Math.max(0, Math.min(allOffset, viewport.scrollWidth - viewport.clientWidth));
      }
      setControls();
    };
    const selectCategory = button => {
      const category = button.dataset.toolkitFilter;
      if (!category) return;
      if (category === activeCategory) {
        if (category === "all" && paused) { paused = false; configure(currentOffset()); }
        return;
      }
      if (activeCategory === "all") allOffset = currentOffset();
      activeCategory = category;
      if (category === "all") paused = false;
      section.classList.toggle("is-filtered", category !== "all");
      let visibleCount = 0;
      items.forEach(item => {
        const visible = category === "all" || item.dataset.toolkitCategory === category;
        item.hidden = !visible;
        if (visible) visibleCount++;
      });
      const categoryLabel = button.dataset.toolkitLabel || category;
      viewport.setAttribute("aria-label", category === "all" ? "My tools and technologies" : `Tools: ${categoryLabel}`);
      if (category === "all") viewport.tabIndex = 0;
      else viewport.removeAttribute("tabindex");
      results.textContent = category === "all" ? `All ${visibleCount} tools` : `${visibleCount} tools · ${categoryLabel}`;
      configure(allOffset);
    };
    filters.forEach(button => button.addEventListener("click", () => selectCategory(button)));
    const pauseForBrowsing = () => {
      if (activeCategory !== "all" || paused || reducedMotion.matches) return;
      const offset = currentOffset();
      paused = true;
      configure(offset);
    };
    toggle.addEventListener("click", () => {
      if (activeCategory !== "all") return;
      const offset = currentOffset();
      paused = !paused;
      configure(offset);
    });
    viewport.addEventListener("focus", pauseForBrowsing);
    viewport.addEventListener("pointerdown", event => { if (event.pointerType !== "mouse") pauseForBrowsing(); });
    const updateVisibility = () => section.classList.toggle("is-background", document.hidden);
    document.addEventListener("visibilitychange", updateVisibility);
    updateVisibility();
    const resize = () => {
      const width = viewport.getBoundingClientRect().width;
      if (Math.abs(width - observedWidth) < 1) return;
      observedWidth = width;
      if (resizeFrame) window.cancelAnimationFrame(resizeFrame);
      resizeFrame = window.requestAnimationFrame(() => { resizeFrame = 0; configure(); });
    };
    if (typeof ResizeObserver === "function") new ResizeObserver(resize).observe(viewport);
    else window.addEventListener("resize", resize);
    if (typeof IntersectionObserver === "function") {
      new IntersectionObserver(entries => row.classList.toggle("is-offscreen", !entries[0].isIntersecting), { threshold: 0 }).observe(row);
    }
    reducedMotion.addEventListener("change", () => configure());
    configure();
    controls.hidden = false;
  });
})();
