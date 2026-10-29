(() => {
  "use strict";
  const gallery = document.querySelector("[data-work-gallery]");
  if (!gallery) return;
  const cards = Array.from(gallery.querySelectorAll("[data-project-card]"));
  const neighbours = Array.from(gallery.querySelectorAll("[data-project-neighbour]"));
  const choice = gallery.querySelector("[data-gallery-select]");
  const stage = gallery.querySelector("[data-work-stage]");
  const position = gallery.querySelector("[data-gallery-position]");
  const status = gallery.querySelector("[data-gallery-status]");
  const results = document.querySelector("[data-results-count]");
  const filters = Array.from(document.querySelectorAll("[data-filter]"));
  if (!cards.length || !choice || !stage) return;
  const options = Array.from(choice.options).map(option => option.cloneNode(true));
  const reduced = window.matchMedia("(prefers-reduced-motion: reduce)");
  const pointerHover = window.matchMedia("(hover: hover) and (pointer: fine)");
  let collection = cards;
  let selected = cards[0].dataset.projectId;
  let currentAnimation;
  const flipCard = (card, flipped, focus = false) => {
    const front = card.querySelector(".project-flip-front");
    const back = card.querySelector(".project-flip-back");
    card.classList.toggle("is-flipped", flipped);
    front.setAttribute("aria-expanded", String(flipped));
    back.inert = !flipped;
    front.inert = flipped;
    if (focus) {
      const target = flipped ? back.querySelector("a") : front;
      const restoreFocus = () => {
        if (!card.hidden && card.classList.contains("is-flipped") === flipped) target.focus({ preventScroll: true });
      };
      restoreFocus();
      // A face can remain unfocusable until its 3D rotation has completed.
      if (document.activeElement !== target) {
        card.querySelector(".project-flip-inner").addEventListener("transitionend", restoreFocus, { once: true });
        window.setTimeout(restoreFocus, reduced.matches ? 0 : 740);
      }
    }
  };
  cards.forEach(card => {
    const flipSurface = card.querySelector(".project-flip-card");
    flipCard(card, false);
    card.querySelector("[data-project-flip]").addEventListener("click", () => flipCard(card, true, true));
    card.querySelector("[data-project-unflip]").addEventListener("click", () => flipCard(card, false, true));
    flipSurface.addEventListener("pointerenter", () => {
      if (pointerHover.matches) flipCard(card, true);
    });
    flipSurface.addEventListener("pointerleave", () => {
      if (!card.querySelector(".project-flip-back").contains(document.activeElement)) flipCard(card, false);
    });
    card.addEventListener("focusout", () => queueMicrotask(() => {
      if (!card.contains(document.activeElement)) flipCard(card, false);
    }));
    card.addEventListener("keydown", event => {
      if (event.key === "Escape" && card.classList.contains("is-flipped")) {
        event.preventDefault();
        flipCard(card, false, true);
      }
    });
  });
  const showProject = (id, animate = true) => {
    const index = collection.findIndex(card => card.dataset.projectId === id);
    if (index < 0) return;
    selected = id;
    cards.forEach(card => {
      const active = card.dataset.projectId === id;
      if (!active) flipCard(card, false);
      card.hidden = !active;
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
    choice.value = id;
    position.textContent = String(index + 1).padStart(2, "0") + " / " + String(collection.length).padStart(2, "0");
    status.textContent = collection[index].querySelector("h3").textContent + ". Project " + (index + 1) + " of " + collection.length + ".";
    currentAnimation?.cancel();
    if (animate && !reduced.matches) currentAnimation = collection[index].animate(
      [{ opacity: .2, translate: "0 12px" }, { opacity: 1, translate: "0 0" }],
      { duration: 420, easing: "cubic-bezier(.2,.7,.2,1)" }
    );
  };
  const applyCategory = category => {
    collection = cards.filter(card => category === "all" || card.dataset.category === category);
    choice.replaceChildren(...options.filter(option => category === "all" || option.dataset.optionCategory === category).map(option => option.cloneNode(true)));
    filters.forEach(filter => {
      const active = filter.dataset.filter === category;
      filter.classList.toggle("is-active", active);
      filter.setAttribute("aria-pressed", String(active));
    });
    results.textContent = collection.length + (category === "technical" ? " technical projects" : category === "design" ? " digital design projects" : " projects");
    const target = collection.find(card => card.dataset.projectId === selected) || collection[0];
    if (target) showProject(target.dataset.projectId, false);
  };
  neighbours.forEach(neighbour => neighbour.addEventListener("click", () => {
    showProject(neighbour.dataset.projectNeighbour);
    stage.focus({ preventScroll: true });
  }));
  choice.addEventListener("change", () => showProject(choice.value));
  filters.forEach(filter => filter.addEventListener("click", () => applyCategory(filter.dataset.filter)));
  gallery.addEventListener("keydown", event => {
    if (event.target.closest("a, select, [data-project-flip], [data-project-unflip]")) return;
    const index = collection.findIndex(card => card.dataset.projectId === selected);
    let targetIndex;
    if (event.key === "ArrowRight") targetIndex = (index + 1) % collection.length;
    else if (event.key === "ArrowLeft") targetIndex = (index + collection.length - 1) % collection.length;
    else if (event.key === "Home") targetIndex = 0;
    else if (event.key === "End") targetIndex = collection.length - 1;
    else return;
    event.preventDefault();
    showProject(collection[targetIndex].dataset.projectId);
    stage.focus({ preventScroll: true });
  });
  gallery.classList.add("is-enhanced");
  applyCategory("all");
})();
