(() => {
  "use strict";

  document.documentElement.classList.add("js");

  const siteHeader = document.querySelector("[data-site-header]");
  const menuToggle = document.querySelector("[data-menu-toggle]");
  const navigation = document.querySelector("[data-navigation]");
  const navLinks = Array.from(document.querySelectorAll("[data-nav-link]"));
  const sections = navLinks.map(link => document.getElementById(link.hash.slice(1))).filter(Boolean);
  const mobileMenu = window.matchMedia("(max-width: 899px)");
  let menuOpen = false;
  let sectionIntent, intentTimer;

  function clearSectionIntent() {
    clearTimeout(intentTimer);
    sectionIntent = null;
  }

  function trackSectionIntent() {
    clearSectionIntent();
    const section = sections.find(item => "#" + item.id === location.hash && item.getClientRects().length);
    if (section) {
      sectionIntent = section;
      intentTimer = setTimeout(() => {
        clearSectionIntent();
        scheduleScrollUpdate();
      }, 1000);
    }
    scheduleScrollUpdate();
  }

  const setMenuOpen = (isOpen) => {
    if (!menuToggle || !navigation) return;
    menuOpen = isOpen && mobileMenu.matches;
    isOpen = menuOpen;
    menuToggle.setAttribute("aria-expanded", String(isOpen));
    menuToggle.setAttribute("aria-label", isOpen ? "Close navigation" : "Open navigation");
    navigation.classList.toggle("is-open", isOpen);
    navigation.inert = mobileMenu.matches && !isOpen;
    document.body.classList.toggle("menu-open", isOpen);
  };

  if (menuToggle && navigation) {
    setMenuOpen(false);
    menuToggle.addEventListener("click", () => {
      setMenuOpen(!menuOpen);
      if (menuOpen) {
        const focusTarget = navigation.querySelector("[aria-current]") || navigation.querySelector("a[href]");
        focusTarget?.focus({ preventScroll: true });
      }
    });
    mobileMenu.addEventListener("change", () => {
      if (mobileMenu.matches && navigation.contains(document.activeElement)) menuToggle.focus({ preventScroll: true });
      setMenuOpen(false);
    });

    navigation.addEventListener("click", (event) => {
      const link = event.target.closest("a[href]");
      if (!menuOpen || !link) return;
      setMenuOpen(false);
      if (!link.getAttribute("href").startsWith("#")) return;
      const section = document.getElementById(link.hash.slice(1));
      if (!section) return;
      if (!section.hasAttribute("tabindex")) {
        section.tabIndex = -1;
        section.addEventListener("blur", () => section.removeAttribute("tabindex"), { once: true });
      }
      section.focus({ preventScroll: true });
    });

    document.addEventListener("pointerdown", (event) => {
      if (menuOpen && !navigation.contains(event.target) && !menuToggle.contains(event.target)) {
        if (navigation.contains(document.activeElement)) menuToggle.focus({ preventScroll: true });
        setMenuOpen(false);
      }
    });

    document.addEventListener("keydown", (event) => {
      if (event.key === "Escape" && menuOpen) {
        setMenuOpen(false);
        menuToggle.focus();
      }
    });
  }

  const updateScrollState = () => {
    siteHeader?.classList.toggle("is-scrolled", window.scrollY > 12);
    let activeHash = "";
    if (document.body.classList.contains("project-detail-page")) {
      activeHash = "#projects";
    } else if (document.body.classList.contains("portfolio-page") && sections.length) {
      const marker = (siteHeader?.getBoundingClientRect().height || 0) + 100;
      let activeSection = sections[0];
      for (const section of sections) {
        if (section.getBoundingClientRect().top <= marker) activeSection = section;
      }
      if (window.scrollY > marker && window.scrollY + window.innerHeight >= document.documentElement.scrollHeight - 4) {
        activeSection = sections[sections.length - 1];
      }
      activeHash = "#" + (sectionIntent || activeSection).id;
    }

    navLinks.forEach((link) => {
      const isActive = link.hash === activeHash;
      link.classList.toggle("is-active", isActive);
      if (isActive) link.setAttribute("aria-current", "location");
      else link.removeAttribute("aria-current");
    });
  };

  let scrollFramePending = false;
  const scheduleScrollUpdate = () => {
    if (scrollFramePending) return;
    scrollFramePending = true;
    window.requestAnimationFrame(() => {
      updateScrollState();
      scrollFramePending = false;
    });
  };

  window.addEventListener("scroll", scheduleScrollUpdate, { passive: true });
  window.addEventListener("resize", scheduleScrollUpdate);
  window.addEventListener("hashchange", trackSectionIntent);
  document.addEventListener("portfolio:view-changed", trackSectionIntent);
  for (const name of ["wheel", "touchstart"]) {
    window.addEventListener(name, () => { clearSectionIntent(); scheduleScrollUpdate(); }, { passive: true });
  }
  window.addEventListener("load", scheduleScrollUpdate, { once: true });
  trackSectionIntent();
  updateScrollState();

  const filterButtons = Array.from(document.querySelectorAll("[data-filter]"));
  const projectCards = Array.from(document.querySelectorAll("[data-project-card]"));
  const resultsCount = document.querySelector("[data-results-count]");
  const workGallery = document.querySelector("[data-work-gallery]");

  const applyFilter = (category) => {
    let visibleCount = 0;
    projectCards.forEach((card) => {
      const isVisible = category === "all" || card.dataset.category === category;
      card.hidden = !isVisible;
      if (isVisible) visibleCount += 1;
    });

    filterButtons.forEach((button) => {
      const isActive = button.dataset.filter === category;
      button.classList.toggle("is-active", isActive);
      button.setAttribute("aria-pressed", String(isActive));
    });

    if (resultsCount) {
      const categoryLabel = category === "technical" ? "technical " : category === "design" ? "digital design " : "";
      resultsCount.textContent = `Showing ${visibleCount} ${categoryLabel}${visibleCount === 1 ? "project" : "projects"}`;
    }
    scheduleScrollUpdate();
  };

  filterButtons.forEach((button) => {
    button.addEventListener("click", () => {
      if (!workGallery?.classList.contains("is-enhanced")) applyFilter(button.dataset.filter);
    });
  });
  if (filterButtons.length && projectCards.length) applyFilter("all");

  const lightbox = document.querySelector("[data-lightbox]");
  const lightboxImage = lightbox?.querySelector("[data-lightbox-image]");
  const lightboxTitle = lightbox?.querySelector("[data-lightbox-title]");
  const lightboxStatus = lightbox?.querySelector("[data-lightbox-status]");
  const lightboxPicker = lightbox?.querySelector("[data-lightbox-picker]");
  let lightboxState = null;

  const updateLightbox = () => {
    if (!lightboxState || !lightboxImage) return;
    const { carousel } = lightboxState;
    const preview = carousel.slides[carousel.index]?.querySelector("[data-preview]");
    const previewImage = preview?.querySelector("img");
    if (!preview || !previewImage) return;

    const title = preview.dataset.previewTitle || "Project preview";
    const label = preview.dataset.previewLabel || `Image ${carousel.index + 1}`;
    const source = preview.dataset.previewSrc || preview.getAttribute("href") || previewImage.currentSrc || previewImage.src;
    lightboxImage.src = source;
    lightboxImage.alt = previewImage.alt || `${title} — ${label}`;
    if (lightboxTitle) lightboxTitle.textContent = title;
    if (lightboxStatus) lightboxStatus.textContent = `${label} · Image ${carousel.index + 1} of ${carousel.slides.length}`;
    lightboxPicker?.querySelectorAll("[data-lightbox-choice]").forEach((choice, index) => {
      choice.setAttribute("aria-pressed", String(index === carousel.index));
    });
  };

  const openLightbox = (carousel, preview) => {
    if (!lightbox || !lightboxImage) return false;
    lightboxState = { carousel, returnFocus: preview };
    if (lightboxPicker) {
      const choices = carousel.slides.map((slide, index) => {
        const sourcePreview = slide.querySelector("[data-preview]");
        const sourceImage = sourcePreview?.querySelector("img");
        const label = sourcePreview?.dataset.previewLabel || `Image ${index + 1}`;
        const button = document.createElement("button");
        button.type = "button";
        button.className = "lightbox-choice";
        button.dataset.lightboxChoice = String(index);
        button.setAttribute("aria-label", `Show ${label}`);
        const thumbnail = document.createElement("img");
        thumbnail.src = sourceImage?.currentSrc || sourceImage?.src || sourcePreview?.getAttribute("href") || "";
        thumbnail.alt = "";
        const text = document.createElement("span");
        text.textContent = label;
        button.append(thumbnail, text);
        button.addEventListener("click", () => {
          carousel.setIndex(index);
          updateLightbox();
        });
        return button;
      });
      lightboxPicker.replaceChildren(...choices);
    }
    updateLightbox();
    setMenuOpen(false);
    document.body.classList.add("lightbox-open");
    if (typeof lightbox.showModal === "function") lightbox.showModal();
    else lightbox.setAttribute("open", "");
    lightbox.querySelector("[data-lightbox-close]")?.focus();
    return true;
  };

  const restoreLightboxFocus = () => {
    document.body.classList.remove("lightbox-open");
    if (!lightboxState) return;
    const { carousel, returnFocus } = lightboxState;
    const activePreview = carousel.slides[carousel.index]?.querySelector("[data-preview]");
    const focusTarget = returnFocus.closest("[data-slide]")?.hidden ? activePreview : returnFocus;
    lightboxState = null;
    focusTarget?.focus({ preventScroll: true });
  };

  const closeLightbox = () => {
    if (!lightbox?.hasAttribute("open")) return;
    if (typeof lightbox.close === "function") lightbox.close();
    else {
      lightbox.removeAttribute("open");
      restoreLightboxFocus();
    }
  };

  const moveLightbox = (direction) => {
    if (!lightboxState) return;
    const { carousel } = lightboxState;
    carousel.setIndex(carousel.index + direction);
    updateLightbox();
  };

  if (lightbox) {
    lightbox.addEventListener("close", restoreLightboxFocus);
    lightbox.addEventListener("keydown", (event) => {
      if (event.key === "ArrowLeft" || event.key === "ArrowRight") {
        event.preventDefault();
        moveLightbox(event.key === "ArrowRight" ? 1 : -1);
      } else if (event.key === "Escape" && typeof lightbox.showModal !== "function") {
        event.preventDefault();
        closeLightbox();
      }
    });
  }

  function initializeCarousel(element) {
    const slides = Array.from(element.querySelectorAll("[data-slide]"));
    if (!slides.length) return;
    const dots = Array.from(element.querySelectorAll("[data-carousel-dot]"));
    const previousButton = element.querySelector("[data-carousel-prev]");
    const nextButton = element.querySelector("[data-carousel-next]");
    const status = element.querySelector("[data-carousel-status]");
    const position = element.querySelector("[data-carousel-position]");
    const carousel = {
      slides,
      index: 0,
      setIndex(nextIndex) {
        this.index = ((nextIndex % slides.length) + slides.length) % slides.length;
        slides.forEach((slide, index) => {
          const isActive = index === this.index;
          slide.hidden = !isActive;
          slide.classList.toggle("is-active", isActive);
        });
        dots.forEach((dot, index) => {
          const dotIndex = Number(dot.dataset.slideIndex ?? index);
          const isActive = dotIndex === this.index;
          dot.classList.toggle("is-active", isActive);
          if (isActive) dot.setAttribute("aria-current", "true");
          else dot.removeAttribute("aria-current");
        });
        if (position) position.textContent = `${this.index + 1} / ${slides.length}`;
        if (status) status.textContent = `Image ${this.index + 1} of ${slides.length}`;
      },
    };

    carousel.setIndex(0);
    if (previousButton) previousButton.disabled = slides.length < 2;
    if (nextButton) nextButton.disabled = slides.length < 2;
    previousButton?.addEventListener("click", () => carousel.setIndex(carousel.index - 1));
    nextButton?.addEventListener("click", () => carousel.setIndex(carousel.index + 1));
    dots.forEach((dot, index) => {
      dot.addEventListener("click", () => carousel.setIndex(Number(dot.dataset.slideIndex ?? index)));
    });

    const galleryLink = element.closest("[data-project-card]")?.querySelector("[data-open-gallery]");
    galleryLink?.addEventListener("click", (event) => {
      if (event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;
      if (openLightbox(carousel, galleryLink)) event.preventDefault();
    });

    element.addEventListener("keydown", (event) => {
      if (event.target.closest("input, textarea, select, [contenteditable='true']")) return;
      if (event.key !== "ArrowLeft" && event.key !== "ArrowRight") return;
      event.preventDefault();
      const focusWasOnPreview = Boolean(event.target.closest("[data-preview]"));
      carousel.setIndex(carousel.index + (event.key === "ArrowRight" ? 1 : -1));
      if (focusWasOnPreview) slides[carousel.index].querySelector("[data-preview]")?.focus({ preventScroll: true });
    });

    let pointerStart = null;
    let suppressPreviewUntil = 0;
    element.addEventListener("pointerdown", (event) => {
      if (event.pointerType === "mouse" || !event.isPrimary) return;
      if (event.target.closest("[data-carousel-prev], [data-carousel-next], [data-carousel-dot]")) return;
      pointerStart = { id: event.pointerId, x: event.clientX, y: event.clientY };
    }, { passive: true });

    element.addEventListener("pointerup", (event) => {
      if (!pointerStart || pointerStart.id !== event.pointerId) return;
      const deltaX = event.clientX - pointerStart.x;
      const deltaY = event.clientY - pointerStart.y;
      pointerStart = null;
      if (Math.abs(deltaX) < 45 || Math.abs(deltaX) <= Math.abs(deltaY) * 1.2) return;
      suppressPreviewUntil = performance.now() + 600;
      carousel.setIndex(carousel.index + (deltaX < 0 ? 1 : -1));
    }, { passive: true });
    element.addEventListener("pointercancel", () => { pointerStart = null; }, { passive: true });

    element.addEventListener("click", (event) => {
      const preview = event.target.closest("[data-preview]");
      if (performance.now() < suppressPreviewUntil) {
        event.preventDefault();
        event.stopPropagation();
        return;
      }
      if (!preview) return;
      if (event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;
      const slideIndex = slides.findIndex((slide) => slide.contains(preview));
      if (slideIndex < 0) return;
      carousel.setIndex(slideIndex);
      if (openLightbox(carousel, preview)) event.preventDefault();
    });
  }
  document.querySelectorAll("[data-carousel]").forEach(initializeCarousel);
  document.addEventListener("portfolio:project-mounted", event => {
    event.target.querySelectorAll("[data-carousel]").forEach(initializeCarousel);
    scheduleScrollUpdate();
  });
  document.addEventListener("portfolio:project-unmounting", () => {
    closeLightbox();
    scheduleScrollUpdate();
  });

  const contactForm = document.querySelector("[data-native-contact]");
  if (!contactForm) return;
  const contactStatus = contactForm.querySelector("[data-form-status]");
  const submitButton = contactForm.querySelector("[data-submit-button]");
  const statusKey = "portfolio-contact-return";
  const currentUrl = new URL(window.location.href);
  let submitting = false;
  const textFields = [
    { field: contactForm.elements.namedItem("name"), minimum: 1, error: "Enter your name." },
    { field: contactForm.elements.namedItem("message"), minimum: 10, error: "Enter at least 10 characters for your message." },
  ];
  textFields.forEach(({ field }) => field.addEventListener("input", () => field.setCustomValidity("")));
  if (currentUrl.searchParams.has("contact-return") && currentUrl.hash === "#contact") {
    try {
      if (sessionStorage.getItem(statusKey) === "pending") {
        sessionStorage.removeItem(statusKey);
        contactStatus.textContent = "FormSubmit accepted your inquiry.";
      }
    } catch {}
    currentUrl.searchParams.delete("contact-return");
    history.replaceState(history.state, "", currentUrl);
  }

  contactForm.addEventListener("submit", event => {
    if (event.defaultPrevented || submitting) {
      event.preventDefault();
      return;
    }
    const invalid = textFields.find(({ field, minimum }) => field.value.trim().length < minimum);
    if (invalid) {
      event.preventDefault();
      invalid.field.setCustomValidity(invalid.error);
      invalid.field.reportValidity();
      return;
    }
    if (location.protocol === "https:" && !["localhost", "127.0.0.1", "[::1]"].includes(location.hostname)) {
      try {
        sessionStorage.setItem(statusKey, "pending");
        const next = new URL("index.php?contact-return=1#contact", location.href);
        const redirect = document.createElement("input");
        redirect.type = "hidden";
        redirect.name = "_next";
        redirect.value = next.href;
        contactForm.append(redirect);
      } catch {} // FormSubmit supplies its own confirmation page when storage is unavailable.
    }
    submitting = true;
    submitButton.disabled = true;
    contactForm.setAttribute("aria-busy", "true");
    contactStatus.textContent = "Opening FormSubmit to verify your inquiry…";
  });
  window.addEventListener("pageshow", event => {
    if (!event.persisted || !submitting) return;
    try { sessionStorage.removeItem(statusKey); } catch {}
    submitting = false;
    submitButton.disabled = false;
    contactForm.elements.namedItem("_next")?.remove();
    contactForm.removeAttribute("aria-busy");
    contactStatus.textContent = "";
  });
})();
