(() => {
  "use strict";

  document.querySelectorAll("[data-credential-collection]").forEach((section) => {
    const filters = Array.from(section.querySelectorAll("[data-credential-filter]"));
    const cards = Array.from(section.querySelectorAll("[data-credential-card]"));
    const controls = section.querySelector("[data-credential-filters]");
    const status = section.querySelector("[data-credentials-status]");
    if (!filters.length || !cards.length || !controls) return;

    const select = (category) => {
      let shown = 0;
      filters.forEach((filter) => {
        const active = filter.dataset.credentialFilter === category;
        filter.setAttribute("aria-pressed", String(active));
        filter.classList.toggle("is-active", active);
      });
      cards.forEach((card) => {
        const visible = category === "all" || card.dataset.credentialIssuer === category;
        card.hidden = !visible;
        if (visible) shown++;
      });
      if (status) status.textContent = category === "all"
        ? `${shown} credentials`
        : `${shown} of ${cards.length} credentials`;
    };

    filters.forEach((filter) => {
      filter.addEventListener("click", () => select(filter.dataset.credentialFilter));
    });
    controls.hidden = false;
    select("all");
    section.classList.add("is-filterable");
  });
})();
