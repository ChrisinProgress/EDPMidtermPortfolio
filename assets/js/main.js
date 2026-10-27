(() => {
  "use strict";

  document.documentElement.classList.add("js");

  const siteHeader = document.querySelector("[data-site-header]");
  const menuToggle = document.querySelector("[data-menu-toggle]");
  const navigation = document.querySelector("[data-navigation]");
  const navLinks = Array.from(document.querySelectorAll("[data-nav-link]"));
  const sections = navLinks.map((link) => document.getElementById(link.hash.slice(1))).filter(Boolean);

  const setMenuOpen = (isOpen) => {
    if (!menuToggle || !navigation) return;
    menuToggle.setAttribute("aria-expanded", String(isOpen));
    menuToggle.setAttribute("aria-label", isOpen ? "Close navigation" : "Open navigation");
    navigation.classList.toggle("is-open", isOpen);
    document.body.classList.toggle("menu-open", isOpen);
    if (isOpen) navigation.querySelector("a[href]")?.focus({ preventScroll: true });
  };

  if (menuToggle && navigation) {
    setMenuOpen(false);
    menuToggle.addEventListener("click", () => {
      setMenuOpen(menuToggle.getAttribute("aria-expanded") !== "true");
    });

    navigation.addEventListener("click", (event) => {
      if (event.target.closest("a[href^='#']")) setMenuOpen(false);
    });

    document.addEventListener("pointerdown", (event) => {
      if (!navigation.contains(event.target) && !menuToggle.contains(event.target)) {
        setMenuOpen(false);
      }
    });

    document.addEventListener("keydown", (event) => {
      if (event.key === "Escape" && menuToggle.getAttribute("aria-expanded") === "true") {
        setMenuOpen(false);
        menuToggle.focus();
      }
    });
  }

  const updateScrollState = () => {
    siteHeader?.classList.toggle("is-scrolled", window.scrollY > 12);
    if (!sections.length) return;

    const headerHeight = siteHeader?.getBoundingClientRect().height || 0;
    const marker = headerHeight + 100;
    let activeSection = sections[0];

    sections.forEach((section) => {
      if (section.getBoundingClientRect().top <= marker) activeSection = section;
    });

    if (window.scrollY + window.innerHeight >= document.documentElement.scrollHeight - 4) {
      activeSection = sections[sections.length - 1];
    }

    navLinks.forEach((link) => {
      const isActive = link.getAttribute("href") === `#${activeSection.id}`;
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
  window.addEventListener("resize", () => {
    if (window.innerWidth >= 900) setMenuOpen(false);
    scheduleScrollUpdate();
  });
  window.addEventListener("load", scheduleScrollUpdate, { once: true });
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
    const itemLabel = carousel.kind === "credential" ? "Certificate" : "Image";
    if (lightboxStatus) lightboxStatus.textContent = `${label} · ${itemLabel} ${carousel.index + 1} of ${carousel.slides.length}`;
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
        const label = (carousel.kind === "credential" ? sourcePreview?.dataset.previewTitle : sourcePreview?.dataset.previewLabel) || `Image ${index + 1}`;
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
    lightbox.querySelector("[data-lightbox-close]")?.addEventListener("click", closeLightbox);
    lightbox.addEventListener("close", restoreLightboxFocus);
    lightbox.addEventListener("click", (event) => {
      if (event.target !== lightbox) return;
      const bounds = lightbox.getBoundingClientRect();
      if (event.clientX < bounds.left || event.clientX > bounds.right || event.clientY < bounds.top || event.clientY > bounds.bottom) {
        closeLightbox();
      }
    });
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

  document.querySelectorAll("[data-carousel]").forEach((element) => {
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
  });

  const credentialCards = Array.from(document.querySelectorAll("[data-credential-card]"));
  if (credentialCards.length) {
    const credentialGallery = {
      kind: "credential",
      slides: credentialCards,
      index: 0,
      setIndex(nextIndex) {
        this.index = ((nextIndex % this.slides.length) + this.slides.length) % this.slides.length;
      },
    };

    credentialCards.forEach((card, index) => {
      card.querySelectorAll("[data-preview], [data-open-credential]").forEach((trigger) => {
        trigger.addEventListener("click", (event) => {
          if (event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;
          credentialGallery.setIndex(index);
          if (openLightbox(credentialGallery, trigger)) event.preventDefault();
        });
      });
    });
  }

  const contactForm = document.querySelector("[data-contact-form]");
  if (!contactForm || typeof window.fetch !== "function" || typeof window.FormData !== "function") return;

  const formStatus = contactForm.querySelector("[data-form-status]");
  const submitButton = contactForm.querySelector("[data-submit-button]");
  const submitLabel = submitButton?.querySelector("[data-submit-label]");
  const defaultSubmitLabel = submitLabel?.textContent || "Send message";
  const fieldNames = ["name", "email", "message"];
  let isSubmitting = false;
  contactForm.noValidate = true;

  const getField = (name) => contactForm.elements.namedItem(name);
  const setFieldError = (name, message) => {
    const field = getField(name);
    const error = contactForm.querySelector(`[data-field-error='${name}']`);
    if (field) field.setAttribute("aria-invalid", message ? "true" : "false");
    if (error) error.textContent = message;
  };

  const setFormStatus = (message, type = "") => {
    if (!formStatus) return;
    formStatus.textContent = message;
    formStatus.classList.toggle("is-success", type === "success");
    formStatus.classList.toggle("is-error", type === "error");
  };

  const setSubmitting = (isBusy) => {
    isSubmitting = isBusy;
    contactForm.setAttribute("aria-busy", String(isBusy));
    if (submitButton) submitButton.disabled = isBusy;
    if (submitLabel) submitLabel.textContent = isBusy ? "Sending…" : defaultSubmitLabel;
  };

  const focusFirstError = () => {
    const firstInvalidField = fieldNames.map(getField).find((field) => field?.getAttribute("aria-invalid") === "true");
    if (!firstInvalidField) return;
    firstInvalidField.focus({ preventScroll: true });
    firstInvalidField.closest(".form-field")?.scrollIntoView({ block: "center", behavior: "instant" });
  };

  fieldNames.forEach((name) => {
    getField(name)?.addEventListener("input", () => setFieldError(name, ""));
  });

  contactForm.addEventListener("submit", async (event) => {
    event.preventDefault();
    if (isSubmitting) return;

    fieldNames.forEach((name) => setFieldError(name, ""));
    setFormStatus("");
    const values = Object.fromEntries(fieldNames.map((name) => [name, String(getField(name)?.value || "").trim()]));
    const errors = {};

    if (!values.name) errors.name = "Please enter your name.";
    else if (values.name.length > 100) errors.name = "Please keep your name to 100 characters or fewer.";
    if (!values.email) errors.email = "Please enter your email address.";
    else if (values.email.length > 254 || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(values.email)) errors.email = "Please enter a valid email address.";
    if (!values.message) errors.message = "Please enter a message.";
    else if (values.message.length < 10) errors.message = "Please write a message of at least 10 characters.";
    else if (values.message.length > 5000) errors.message = "Please keep your message to 5,000 characters or fewer.";

    if (Object.keys(errors).length) {
      Object.entries(errors).forEach(([name, message]) => setFieldError(name, message));
      setFormStatus("Please check the highlighted fields and try again.", "error");
      focusFirstError();
      return;
    }

    const formData = new FormData(contactForm);
    Object.entries(values).forEach(([name, value]) => formData.set(name, value));
    setSubmitting(true);
    setFormStatus("Sending your message…");

    try {
      const response = await fetch(contactForm.action, {
        method: contactForm.method.toUpperCase() || "POST",
        body: formData,
        headers: { Accept: "application/json" },
        credentials: "same-origin",
      });
      let result;
      try {
        result = await response.json();
      } catch {
        throw new Error("We couldn’t confirm that your message was sent. Please try again later.");
      }

      if (!result || typeof result !== "object") {
        throw new Error("We couldn’t confirm that your message was sent. Please try again later.");
      }

      if (!response.ok || result.success !== true) {
        if (result.errors && typeof result.errors === "object") {
          fieldNames.forEach((name) => {
            const error = result.errors[name];
            if (typeof error === "string") setFieldError(name, error);
            else if (Array.isArray(error) && typeof error[0] === "string") setFieldError(name, error[0]);
          });
        }
        setFormStatus(typeof result.message === "string" ? result.message : "Your message could not be sent. Please try again later.", "error");
        focusFirstError();
        return;
      }

      const csrfField = getField("csrfToken");
      const csrfToken = typeof result.csrfToken === "string" ? result.csrfToken : csrfField?.value;
      contactForm.reset();
      if (csrfField && csrfToken !== undefined) csrfField.value = csrfToken;
      fieldNames.forEach((name) => setFieldError(name, ""));
      setFormStatus(typeof result.message === "string" ? result.message : "Thank you! Your message has been sent.", "success");
    } catch (error) {
      const message = error instanceof TypeError ? "We couldn’t reach the server to confirm delivery. Please check your connection and try again." : error.message;
      setFormStatus(message || "We couldn’t confirm that your message was sent. Please try again later.", "error");
    } finally {
      setSubmitting(false);
    }
  });
})();
