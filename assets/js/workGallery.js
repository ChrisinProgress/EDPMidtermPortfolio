(() => {
  "use strict";
  const gallery = document.querySelector("[data-work-gallery]");
  if (!gallery) return;
  const cards = Array.from(gallery.querySelectorAll("[data-project-card]"));
  const picker = gallery.querySelector("[data-gallery-picker]");
  const picks = Array.from(gallery.querySelectorAll("[data-project-pick]"));
  const neighbours = Array.from(gallery.querySelectorAll("[data-project-neighbour]"));
  const filters = Array.from(document.querySelectorAll("[data-filter]"));
  const previous = gallery.querySelector("[data-project-prev]");
  const next = gallery.querySelector("[data-project-next]");
  const status = gallery.querySelector("[data-gallery-status]");
  const position = gallery.querySelector("[data-gallery-position]");
  const total = gallery.querySelector("[data-gallery-total]");
  const results = document.querySelector("[data-results-count]");
  const stage = gallery.querySelector("[data-work-stage]");
  if (!cards.length || !picker || !stage || picks.length !== cards.length) return;
  let category = "all";
  let collection = cards;
  let selectedId = cards[0].dataset.projectId;
  let animation;

  const showProject = (id, { animate = true, focusPick = false } = {}) => {
    const index = collection.findIndex(card => card.dataset.projectId === id);
    if (index < 0) return;
    selectedId = id;
    cards.forEach(card => {
      const active = card.dataset.projectId === id;
      card.hidden = !active;
      card.classList.toggle("is-featured", active);
    });
    picks.forEach(pick => {
      const active = pick.dataset.projectPick === id;
      pick.classList.toggle("is-selected", active);
      pick.setAttribute("aria-pressed", String(active));
      pick.closest("[data-gallery-item]").hidden = !collection.some(card => card.dataset.projectId === pick.dataset.projectPick);
    });
    neighbours.forEach(neighbour => {
      const neighbourIndex = collection.findIndex(card => card.dataset.projectId === neighbour.dataset.projectNeighbour);
      let offset = neighbourIndex - index;
      if (offset > collection.length / 2) offset -= collection.length;
      if (offset < -collection.length / 2) offset += collection.length;
      const visible = neighbourIndex >= 0 && offset !== 0 && Math.abs(offset) <= 2;
      neighbour.hidden = !visible;
      if (visible) {
        neighbour.style.setProperty("--offset", offset);
        neighbour.style.setProperty("--distance", Math.abs(offset));
        neighbour.style.zIndex = String(3 - Math.abs(offset));
      }
    });
    const title = collection[index].querySelector("h3")?.textContent || "Project";
    position.textContent = `${String(index + 1).padStart(2, "0")} / ${String(collection.length).padStart(2, "0")}`;
    status.textContent = `${title}. Project ${index + 1} of ${collection.length}.`;
    previous.disabled = collection.length < 2;
    next.disabled = collection.length < 2;
    if (animate && !window.matchMedia("(prefers-reduced-motion: reduce)").matches) {
      animation?.cancel();
      animation = collection[index].animate([
        { opacity: 0, transform: "translateY(14px) scale(.985)" },
        { opacity: 1, transform: "translateY(0) scale(1)" },
      ], { duration: 380, easing: "cubic-bezier(.2,.7,.2,1)" });
    }
    if (focusPick) {
      const pick = picks.find(item => item.dataset.projectPick === id);
      pick?.focus({ preventScroll: true });
      pick?.scrollIntoView({ block: "nearest", inline: "nearest", behavior: "instant" });
    } else {
      const pick = picks.find(item => item.dataset.projectPick === id);
      if (pick) picker.scrollTo({
        left: pick.offsetLeft - picker.offsetLeft - (picker.clientWidth - pick.offsetWidth) / 2,
        behavior: window.matchMedia("(prefers-reduced-motion: reduce)").matches ? "instant" : "smooth",
      });
    }
  };
  const moveProject = (direction, focusPick = false) => {
    const index = collection.findIndex(card => card.dataset.projectId === selectedId);
    const target = collection[(index + direction + collection.length) % collection.length];
    if (target) showProject(target.dataset.projectId, { focusPick });
  };
  const applyCategory = selectedCategory => {
    category = selectedCategory;
    collection = cards.filter(card => category === "all" || card.dataset.category === category);
    filters.forEach(filter => {
      const active = filter.dataset.filter === category;
      filter.classList.toggle("is-active", active);
      filter.setAttribute("aria-pressed", String(active));
    });
    const label = category === "technical" ? "technical " : category === "design" ? "digital design " : "";
    results.textContent = `${collection.length} ${label}projects`;
    total.textContent = `${collection.length} works`;
    const target = collection.find(card => card.dataset.projectId === selectedId) || collection[0];
    if (target) showProject(target.dataset.projectId, { animate: false });
  };
  previous.addEventListener("click", () => moveProject(-1));
  next.addEventListener("click", () => moveProject(1));
  picks.forEach(pick => pick.addEventListener("click", () => showProject(pick.dataset.projectPick)));
  neighbours.forEach(neighbour => neighbour.addEventListener("click", () => {
    showProject(neighbour.dataset.projectNeighbour);
    stage.focus({ preventScroll: true });
  }));
  filters.forEach(filter => filter.addEventListener("click", () => applyCategory(filter.dataset.filter)));
  gallery.addEventListener("keydown", event => {
    if (event.target.closest("[data-carousel], a, input, textarea, select")) return;
    const inPicker = Boolean(event.target.closest("[data-gallery-picker]"));
    const inNeighbour = Boolean(event.target.closest("[data-project-neighbour]"));
    if (event.key === "ArrowLeft" || event.key === "ArrowRight") {
      event.preventDefault();
      moveProject(event.key === "ArrowRight" ? 1 : -1, inPicker);
      if (inNeighbour) stage.focus({ preventScroll: true });
    } else if (event.key === "Home" || event.key === "End") {
      event.preventDefault();
      const card = event.key === "Home" ? collection[0] : collection[collection.length - 1];
      if (card) showProject(card.dataset.projectId, { focusPick: inPicker });
      if (inNeighbour) stage.focus({ preventScroll: true });
    }
  });
  gallery.classList.add("is-enhanced");
  applyCategory("all");
})();
