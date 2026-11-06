(() => {
  "use strict";
  const supported = matchMedia("(hover: hover) and (pointer: fine) and (prefers-reduced-motion: no-preference) and (forced-colors: none)");
  const overlay = document.createElement("div");
  overlay.className = "pointer-decoration";
  overlay.setAttribute("aria-hidden", "true");
  overlay.innerHTML = `
    <div class="pointer-follower"><div class="pointer-halo"><div class="pointer-press">
      <span class="pointer-face pointer-face--gold">
        <svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.15"/><path d="M6 4a10 10 0 0 1 10-1" stroke="#d97706" stroke-width="1.5" stroke-linecap="round"/></svg>
      </span>
      <span class="pointer-face pointer-face--red">
        <svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.2"/><circle cx="12" cy="12" r="5.7" stroke="#991b1b" stroke-width=".8"/><path d="M12 5.4a1.35 1.35 0 1 1-1.35 1.35c0-.84.95-1.74 1.95-1.82-.4.32-.5.47-.6.47Z" fill="currentColor" id="pointer-tomoe"/><use href="#pointer-tomoe" transform="rotate(120 12 12)"/><use href="#pointer-tomoe" transform="rotate(240 12 12)"/></svg>
      </span>
    </div></div></div>
    <div class="pointer-ripple"><div class="pointer-ripple-ring"></div></div>`;
  const follower = overlay.querySelector(".pointer-follower");
  const press = overlay.querySelector(".pointer-press");
  const ripple = overlay.querySelector(".pointer-ripple");
  const ring = ripple.firstElementChild;
  const controls = "a[href],button:not([disabled]),input:not([disabled]),select:not([disabled]),[role=button],[data-student-id],[data-id-clip],[data-id-lanyard]";
  let x = 0, y = 0, targetX = 0, targetY = 0, lastFrame = 0, frame = 0;
  let visible = false;
  let previousTarget, excluded;
  let pressEffect, rippleEffect;

  function place(element, left, top) {
    element.style.transform = "translate3d(" + left + "px," + top + "px,0)";
  }

  function follow(now) {
    const ease = 1 - Math.exp(-Math.min(now - lastFrame, 40) / 26);
    lastFrame = now;
    x += (targetX - x) * ease;
    y += (targetY - y) * ease;
    const moving = Math.hypot(targetX - x, targetY - y) > .2;
    place(follower, moving ? x : targetX, moving ? y : targetY);
    frame = moving ? requestAnimationFrame(follow) : 0;
  }

  function hide() {
    visible = false;
    overlay.classList.remove("is-visible", "is-interactive");
    cancelAnimationFrame(frame);
    frame = 0;
    pressEffect?.cancel();
    rippleEffect?.cancel();
  }

  window.addEventListener("pointermove", event => {
    if (!supported.matches || event.pointerType !== "mouse" || !(event.target instanceof Element)) {
      hide();
      return;
    }
    const target = event.target;
    if (target !== previousTarget) {
      previousTarget = target;
      const cursor = getComputedStyle(target).cursor;
      excluded = cursor === "text" || cursor === "vertical-text" || cursor.endsWith("resize") ||
        target.closest("textarea,[contenteditable],input:not([type=checkbox],[type=radio],[type=button],[type=submit],[type=range])");
    }
    if (excluded) {
      hide();
      return;
    }
    const host = target.closest("dialog[open]") || document.body;
    if (overlay.parentElement !== host) host.append(overlay);
    targetX = event.clientX;
    targetY = event.clientY;
    overlay.classList.toggle("is-interactive", !!target.closest(controls) && !target.closest("[inert]"));
    if (!visible) {
      x = targetX;
      y = targetY;
      place(follower, x, y);
      overlay.classList.add("is-visible");
      visible = true;
    } else if (!frame) {
      lastFrame = performance.now();
      frame = requestAnimationFrame(follow);
    }
  }, { passive: true });

  window.addEventListener("pointerdown", event => {
    if (!visible || event.pointerType !== "mouse" || event.button !== 0) return;
    place(ripple, event.clientX, event.clientY);
    pressEffect?.cancel();
    rippleEffect?.cancel();
    pressEffect = press.animate([{ scale: 1 }, { scale: .83, offset: .18 }, { scale: 1 }], {
      duration: 450, easing: "ease-out",
    });
    rippleEffect = ring.animate([{ scale: .8, opacity: .8 }, { scale: 2.15, opacity: 0 }], {
      duration: 450, easing: "ease-out",
    });
  }, { passive: true });

  document.documentElement.addEventListener("pointerleave", hide);
  window.addEventListener("blur", hide);
  document.addEventListener("visibilitychange", () => { if (document.hidden) hide(); });
  document.addEventListener("close", event => { if (event.target === overlay.parentElement) hide(); }, true);
  supported.addEventListener("change", hide);
})();
