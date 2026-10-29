(() => {
  "use strict";
  document.querySelectorAll("[data-credential-collection]").forEach(section => {
    const cards = Array.from(section.querySelectorAll("[data-credential-card]"));
    if (!cards.length) return;
    section.classList.add("is-exhibition");
    if (typeof IntersectionObserver === "function") {
      const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => entry.target.classList.toggle("is-in-view", entry.isIntersecting));
      }, { threshold: .2, rootMargin: "-6% 0px -8% 0px" });
      cards.forEach(card => observer.observe(card));
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
