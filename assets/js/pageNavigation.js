(() => {
  "use strict";
  const base = document.getElementById("main-content");
  if (!base) return;
  const routes = { project: "project-detail-page", legal: "legal-page", portfolio: "portfolio-page" };
  const baseKind = base.matches(".policy-page") ? "legal" : base.matches(".project-detail") ? "project" : "portfolio";
  const baseTitle = document.title;
  const directory = new URL(".", location.href).pathname;
  const session = performance.timeOrigin;
  const reduced = matchMedia("(prefers-reduced-motion: reduce)");
  const pages = new Map();
  const gallery = base.querySelector("[data-work-gallery]");
  let current, opener, request, requestedUrl, transition;
  let revision = 0;
  let returning = false;

  function cancelNavigation() {
    request?.abort();
    request = null;
    requestedUrl = null;
    transition?.cancel();
    return ++revision;
  }

  function routeKind(url) {
    if (url.origin !== location.origin || new URL(".", url).pathname !== directory) return null;
    const file = url.pathname.slice(directory.length);
    if (["privacy.php", "terms.php", "licenses.php"].includes(file)) return "legal";
    if (file === "project.php" && url.searchParams.has("project")) return "project";
    return null;
  }

  function fadeIn(element, duration) {
    if (reduced.matches) return;
    transition = element.animate(
      [{ opacity: .35, translate: "0 10px" }, { opacity: 1, translate: "0 0" }],
      { duration, easing: "ease-out" }
    );
  }

  function notifyView(page, phase) {
    const eventType = page.kind === "project" ? "project" : "policy";
    page.main.dispatchEvent(new Event("portfolio:" + eventType + "-" + phase, { bubbles: true }));
  }

  function removeCurrent() {
    if (!current) return;
    notifyView(current, "unmounting");
    current.main.remove();
    current = null;
  }

  function setPageClass(kind) {
    document.body.classList.remove(...Object.values(routes));
    document.body.classList.add(routes[kind]);
  }

  function routeState() {
    const state = history.state;
    return state?.portfolioSession === session ? state : null;
  }

  function positionView(main, url, scrollY) {
    const target = url.hash ? document.getElementById(url.hash.slice(1)) : null;
    if (scrollY === undefined && target && main.contains(target)) {
      target.scrollIntoView({ block: "start", behavior: "instant" });
    } else {
      window.scrollTo({ top: scrollY ?? 0, behavior: "instant" });
    }
  }

  async function openPage(url, kind, push, scrollY) {
    const key = url.origin + url.pathname + url.search;
    if (push && key === requestedUrl) return;
    const navigation = cancelNavigation();
    if (current?.url === key) {
      positionView(current.main, url, scrollY);
      return;
    }
    const controller = new AbortController();
    request = controller;
    requestedUrl = key;
    const active = current?.main || base;
    active.setAttribute("aria-busy", "true");
    try {
      let page = pages.get(key);
      if (!page) {
        const response = await fetch(url, { signal: controller.signal });
        if (!response.ok) throw new Error("Page unavailable");
        const documentView = new DOMParser().parseFromString(await response.text(), "text/html");
        const main = documentView.querySelector(kind === "project" ? "main.project-detail" : "main.policy-page");
        if (!main || (kind === "project" && !main.querySelector("[data-project-title]"))) throw new Error("Content unavailable");
        page = { title: documentView.title, main };
        pages.set(key, page);
      }
      if (navigation !== revision) return;
      if (push) {
        const depth = routeState()?.portfolioDepth || 0;
        history.scrollRestoration = "manual";
        history.replaceState({ ...history.state, portfolioSession: session, portfolioDepth: depth, viewScrollY: window.scrollY }, "");
        history.pushState({ portfolioSession: session, portfolioDepth: depth + 1 }, "", url);
        history.scrollRestoration = "manual";
      }
      removeCurrent();
      base.hidden = true;
      base.id = "portfolio-base";
      const main = document.importNode(page.main, true);
      current = { url: key, kind, main };
      base.after(main);
      setPageClass(kind);
      document.title = page.title;
      positionView(main, url, scrollY);
      main.tabIndex = -1;
      main.focus({ preventScroll: true });
      notifyView(current, "mounted");
      document.dispatchEvent(new Event("portfolio:view-changed"));
      if (kind !== "project") fadeIn(main, 180);
    } catch (error) {
      if (navigation === revision && error.name !== "AbortError") window.location.assign(url);
    } finally {
      if (controller === request) {
        request = null;
        requestedUrl = null;
      }
      if (!request || active !== (current?.main || base)) active.removeAttribute("aria-busy");
    }
  }

  function showBase(scrollY) {
    cancelNavigation();
    if (!current) return;
    const project = current.kind === "project";
    removeCurrent();
    setPageClass(baseKind);
    document.title = baseTitle;
    base.id = "main-content";
    base.hidden = false;
    const url = new URL(location.href);
    positionView(base, url, scrollY);
    if (scrollY !== undefined) {
      const card = opener?.closest("[data-project-card]");
      const focus = card && !card.classList.contains("is-flipped") ? card.querySelector("[data-project-flip]") : opener;
      focus?.focus({ preventScroll: true });
    } else {
      const target = url.hash && document.getElementById(url.hash.slice(1));
      if (target && base.contains(target)) {
        target.tabIndex = -1;
        target.focus({ preventScroll: true });
      }
    }
    window.dispatchEvent(new HashChangeEvent("hashchange"));
    document.dispatchEvent(new Event("portfolio:view-changed"));
    fadeIn(project && gallery ? gallery : base, project ? 160 : 180);
  }

  document.addEventListener("click", event => {
    if (event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;
    const link = event.target.closest("a[href]");
    if (!link || link.target) return;
    const href = link.getAttribute("href");
    const url = new URL(link.href);
    const kind = routeKind(url);
    if (!href.startsWith("#") && (kind === "legal" || (kind === "project" && link.matches(".project-view-link")))) {
      event.preventDefault();
      if (url.href === location.href) return;
      if (!current) opener = link;
      openPage(url, kind, true);
      return;
    }
    const backLink = link.matches(".policy-return, .collection-return, .detail-end .button");
    const target = url.hash && document.getElementById(url.hash.slice(1));
    const isPortfolioLink = url.origin === location.origin && new URL(".", url).pathname === directory &&
      (url.pathname === directory + "index.php" || url.pathname === directory);
    const state = routeState();
    const depth = state?.portfolioDepth || 0;
    if (current && depth && href.startsWith("#") && target && current.main.contains(target) && href !== "#main-content") {
      event.preventDefault();
      if (url.href !== location.href) {
        history.replaceState({ ...state, viewScrollY: window.scrollY }, "");
        history.pushState({ ...state, portfolioDepth: depth + 1, viewScrollY: undefined }, "", url);
      }
      positionView(current.main, url);
      return;
    }
    const goesToBaseSection = (isPortfolioLink || href.startsWith("#")) && target && base.contains(target);
    if (current && depth && baseKind === "portfolio" && ((backLink && isPortfolioLink) || goesToBaseSection)) {
      event.preventDefault();
      if (returning) return;
      if (backLink) {
        returning = true;
        history.go(-depth);
      } else {
        history.replaceState({ ...state, viewScrollY: window.scrollY }, "");
        history.pushState({ portfolioSession: session, portfolioDepth: 0 }, "", new URL(url.hash, new URL(directory, location.origin)));
        showBase();
      }
    } else if (baseKind !== "portfolio" && isPortfolioLink && (backLink || url.hash)) {
      event.preventDefault();
      url.searchParams.set("portfolio-return", "1");
      window.location.assign(url);
    }
  });

  window.addEventListener("popstate", () => {
    returning = false;
    const state = routeState();
    if (state?.portfolioDepth) {
      const url = new URL(location.href);
      openPage(url, routeKind(url), false, state.viewScrollY);
    } else {
      showBase(state?.viewScrollY);
    }
  });
})();
