(() => {
  "use strict";
  const reducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)");
  document.querySelectorAll("[data-about-photo-collection]").forEach(collection => {
    const button = collection.querySelector("[data-about-photo-stack]");
    const photos = Array.from(collection.querySelectorAll("[data-about-photo]"));
    const status = collection.querySelector("[data-about-photo-status]");
    if (!button || photos.length < 2 || !status) return;
    let current = 0;
    let turning = false;
    const announce = () => {
      const photo = photos[current];
      button.setAttribute("aria-label", `${photo.dataset.photoDescription}. Photo ${current + 1} of ${photos.length}. Show next photo.`);
      const label = document.createElement("span");
      label.textContent = photo.dataset.photoLabel;
      status.replaceChildren(`${String(current + 1).padStart(2, "0")} / ${String(photos.length).padStart(2, "0")} `, label);
    };
    button.addEventListener("click", async () => {
      if (turning) return;
      turning = true;
      button.setAttribute("aria-busy", "true");
      const previous = photos[current];
      const next = photos[(current + 1) % photos.length];
      // Keep the current image on screen until the next supplied photograph is ready.
      try { if (!next.complete) await next.decode(); } catch { /* Local image remains the graceful fallback. */ }
      current = (current + 1) % photos.length;
      previous.classList.remove("is-current");
      next.classList.add("is-current");
      announce();
      if (reducedMotion.matches) {
        turning = false;
        button.removeAttribute("aria-busy");
        return;
      }
      previous.classList.add("is-leaving");
      let fallback;
      const finish = () => {
        previous.removeEventListener("animationend", finish);
        window.clearTimeout(fallback);
        previous.classList.remove("is-leaving");
        turning = false;
        button.removeAttribute("aria-busy");
      };
      previous.addEventListener("animationend", finish, { once: true });
      fallback = window.setTimeout(finish, 750);
    });
    announce();
  });
})();
