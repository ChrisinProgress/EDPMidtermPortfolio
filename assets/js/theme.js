(() => {
  "use strict";

  const documentRoot = document.documentElement;
  const storageKey = "cjcPortfolioTheme";
  const reducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)");
  let desiredTheme = "light";
  let activeTransition = null;
  let transitionVersion = 0;
  let cleanupTimer = 0;

  const applyTheme = (theme) => {
    const isDark = theme === "dark";
    documentRoot.dataset.theme = isDark ? "dark" : "light";
    documentRoot.classList.add("theme-ready");

    const themeColor = document.querySelector('meta[name="theme-color"]');
    themeColor?.setAttribute("content", isDark ? "#0e1524" : "#ffffff");

    const themeToggle = document.querySelector("[data-theme-toggle]");
    const actionLabel = isDark ? "Switch to light mode" : "Switch to dark mode";
    themeToggle?.setAttribute("aria-pressed", String(isDark));
    themeToggle?.setAttribute("aria-label", actionLabel);
    themeToggle?.setAttribute("title", actionLabel);
    window.dispatchEvent(new CustomEvent("portfolio:themechange", { detail: { theme: isDark ? "dark" : "light" } }));
  };

  const clearAnimationState = () => {
    window.clearTimeout(cleanupTimer);
    documentRoot.classList.remove("theme-changing", "theme-view-transition");
  };

  const changeTheme = (theme, { persist = false, animate = false, event = null } = {}) => {
    desiredTheme = theme === "dark" ? "dark" : "light";
    const version = ++transitionVersion;
    activeTransition?.skipTransition();
    activeTransition = null;
    clearAnimationState();

    if (persist) {
      try {
        window.localStorage.setItem(storageKey, desiredTheme);
      } catch {
        // The current page still switches when storage is unavailable.
      }
    }

    if (!animate || reducedMotion.matches) {
      applyTheme(desiredTheme);
      return;
    }

    documentRoot.classList.add("theme-changing");
    cleanupTimer = window.setTimeout(() => {
      if (version === transitionVersion) clearAnimationState();
    }, 700);

    if (typeof document.startViewTransition !== "function") {
      applyTheme(desiredTheme);
      return;
    }

    const toggleBounds = document.querySelector("[data-theme-toggle]")?.getBoundingClientRect();
    const isPointerClick = event && event.detail > 0;
    const x = isPointerClick ? event.clientX : (toggleBounds ? toggleBounds.left + toggleBounds.width / 2 : window.innerWidth / 2);
    const y = isPointerClick ? event.clientY : (toggleBounds ? toggleBounds.top + toggleBounds.height / 2 : 40);
    const radius = Math.hypot(Math.max(x, window.innerWidth - x), Math.max(y, window.innerHeight - y));
    documentRoot.style.setProperty("--theme-origin-x", `${x}px`);
    documentRoot.style.setProperty("--theme-origin-y", `${y}px`);
    documentRoot.style.setProperty("--theme-reveal-radius", `${Math.ceil(radius)}px`);
    documentRoot.classList.add("theme-view-transition");

    try {
      const transition = document.startViewTransition(() => {
        if (version === transitionVersion) applyTheme(desiredTheme);
      });
      activeTransition = transition;
      transition.finished.catch(() => {}).finally(() => {
        if (version !== transitionVersion) return;
        activeTransition = null;
        clearAnimationState();
      });
    } catch {
      documentRoot.classList.remove("theme-view-transition");
      applyTheme(desiredTheme);
    }
  };

  // Apply a saved choice before styles paint. First-time visitors see the white theme.
  let savedTheme = "light";
  try {
    if (window.localStorage.getItem(storageKey) === "dark") savedTheme = "dark";
  } catch {
    // The toggle remains usable when the browser blocks persistent storage.
  }
  desiredTheme = savedTheme;
  applyTheme(desiredTheme);

  document.addEventListener("DOMContentLoaded", () => {
    applyTheme(documentRoot.dataset.theme);
    document.querySelector("[data-theme-toggle]")?.addEventListener("click", (event) => {
      changeTheme(desiredTheme === "dark" ? "light" : "dark", { persist: true, animate: true, event });
    });
  }, { once: true });

  window.addEventListener("storage", (event) => {
    if (event.key === storageKey || event.key === null) {
      changeTheme(event.key === storageKey && event.newValue === "dark" ? "dark" : "light", { animate: true });
    }
  });

  reducedMotion.addEventListener("change", () => {
    if (!reducedMotion.matches) return;
    ++transitionVersion;
    activeTransition?.skipTransition();
    activeTransition = null;
    clearAnimationState();
    applyTheme(desiredTheme);
  });
})();
