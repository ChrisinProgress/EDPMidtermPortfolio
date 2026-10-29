(() => {
  "use strict";
  const fineHover = window.matchMedia("(hover: hover) and (pointer: fine)");
  document.querySelectorAll("[data-portrait-interactive]").forEach(portrait => {
    let toggled = false;
    let pointerType = "";
    const reveal = pixel => {
      portrait.classList.toggle("is-pixel", pixel);
      portrait.setAttribute("aria-pressed", String(pixel));
    };
    portrait.addEventListener("pointerenter", event => {
      if (fineHover.matches && event.pointerType !== "touch") reveal(true);
    });
    portrait.addEventListener("pointerleave", () => {
      toggled = false;
      reveal(false);
    });
    portrait.addEventListener("pointerdown", event => { pointerType = event.pointerType; });
    portrait.addEventListener("click", event => {
      if (event.detail === 0 || pointerType === "touch" || !fineHover.matches) {
        toggled = !toggled;
        reveal(toggled);
      }
    });
    portrait.addEventListener("blur", () => { toggled = false; reveal(false); });
    portrait.addEventListener("keydown", event => {
      if (event.key === "Escape") { toggled = false; reveal(false); }
    });
  });
})();
