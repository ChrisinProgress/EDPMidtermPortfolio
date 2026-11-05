/* A decorative, bounded entrance. Content remains usable if scripts fail. */
(() => {
  "use strict";
  const root = document.documentElement;
  const reducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)");
  if (reducedMotion.matches) return;
  const started = performance.now();
  let complete = false;
  let safetyTimer = 0;
  const skipEvents = ["pointerdown", "keydown", "wheel", "touchstart"];
  const finish = () => {
    if (complete) return;
    complete = true;
    const label = document.querySelector("[data-entrance-percent]");
    if (label) label.textContent = "100%";
    window.clearTimeout(safetyTimer);
    root.classList.add("intro-revealing");
    root.classList.remove("intro-pending");
    let revealed = false;
    const revealComplete = () => { if (revealed) return; revealed = true; root.classList.remove("intro-revealing"); };
    document.querySelector("[data-site-entrance]")?.addEventListener("transitionend", revealComplete, { once: true });
    window.setTimeout(revealComplete, reducedMotion.matches ? 0 : 750);
    skipEvents.forEach(name => window.removeEventListener(name, finish));
  };
  root.classList.add("intro-pending");
  const updateProgress = () => {
    if (complete) return;
    const label = document.querySelector("[data-entrance-percent]");
    if (label) label.textContent = Math.min(99, Math.round((performance.now() - started) / 10.5)) + "%";
    requestAnimationFrame(updateProgress);
  };
  requestAnimationFrame(updateProgress);
  skipEvents.forEach(name => window.addEventListener(name, finish, { passive: true }));
  // Never wait for all project images or for a third-party service to load.
  safetyTimer = window.setTimeout(finish, 1800);
  const ready = () => window.setTimeout(finish, Math.max(0, 1050 - (performance.now() - started)));
  if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", ready, { once: true });
  else ready();
  window.addEventListener("pageshow", event => { if (event.persisted) finish(); });
  reducedMotion.addEventListener("change", event => { if (event.matches) finish(); });
})();
