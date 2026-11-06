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
  const controls = "a[href],button:not([disabled]),input:not([disabled]),select:not([disabled]),[role=button],[role=option]:not([aria-disabled=true]),[data-student-id],[data-id-clip],[data-id-lanyard]";
  const canUseTopLayer = typeof overlay.showPopover === "function";
  document.body.append(overlay);
  let x = 0, y = 0, targetX = 0, targetY = 0, lastFrame = 0, frame = 0;
  let visible = false, pointerPresent = false, selecting = false, hitFrame = 0;
  let activeDialog;
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
    if (!visible) return;
    visible = false;
    overlay.classList.remove("is-visible", "is-interactive");
    cancelAnimationFrame(frame);
    frame = 0;
    pressEffect?.cancel();
    rippleEffect?.cancel();
  }

  function liftAbove(dialog) {
    if (dialog === activeDialog && (!dialog || !canUseTopLayer || overlay.matches(":popover-open"))) return;
    activeDialog = dialog;
    if (!canUseTopLayer) return;
    if (overlay.matches(":popover-open")) overlay.hidePopover();
    if (dialog) {
      // A manual popover paints above native dialogs without changing the viewport origin.
      overlay.setAttribute("popover", "manual");
      overlay.showPopover();
    } else {
      overlay.removeAttribute("popover");
    }
  }

  function updateTarget(target) {
    if (!supported.matches || selecting || !(target instanceof Element)) {
      hide();
      return;
    }
    const dialog = target.closest("dialog[open]") || document.querySelector("dialog[open]");
    liftAbove(dialog);
    if (target !== previousTarget) {
      previousTarget = target;
      const cursor = getComputedStyle(target).cursor;
      excluded = cursor === "text" || cursor === "vertical-text" || cursor.endsWith("resize") ||
        target.closest("textarea,[contenteditable],input:not([type=checkbox],[type=radio],[type=button],[type=submit],[type=range])");
    }
    if (excluded || (dialog && !canUseTopLayer)) {
      hide();
      return;
    }
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
  }

  function scheduleHitTest() {
    if (!pointerPresent || hitFrame || !supported.matches) return;
    hitFrame = requestAnimationFrame(() => {
      hitFrame = 0;
      if (pointerPresent) updateTarget(document.elementFromPoint(targetX, targetY));
    });
  }

  function leave() {
    pointerPresent = false;
    selecting = false;
    previousTarget = null;
    hide();
  }

  window.addEventListener("pointermove", event => {
    if (event.pointerType !== "mouse" || !supported.matches) {
      leave();
      return;
    }
    pointerPresent = true;
    if (selecting && !(event.buttons & 1)) selecting = false;
    targetX = event.clientX;
    targetY = event.clientY;
    updateTarget(event.target);
  }, { passive: true });

  window.addEventListener("pointerdown", event => {
    if (event.pointerType !== "mouse") { leave(); return; }
    if (!visible || event.button !== 0) return;
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

  document.addEventListener("selectstart", () => { selecting = true; hide(); });
  window.addEventListener("pointerup", () => { if (selecting) { selecting = false; scheduleHitTest(); } });
  window.addEventListener("scroll", scheduleHitTest, {capture:true, passive:true});
  window.addEventListener("resize", scheduleHitTest, {passive:true});
  document.addEventListener("portfolio:dropdown-changed", scheduleHitTest);
  const dialogObserver = new MutationObserver(scheduleHitTest);
  document.querySelectorAll("dialog").forEach(dialog => {
    dialogObserver.observe(dialog, {attributes:true, attributeFilter:["open"]});
  });
  document.documentElement.addEventListener("pointerleave", leave);
  window.addEventListener("blur", leave);
  document.addEventListener("visibilitychange", () => { if (document.hidden) leave(); });
  supported.addEventListener("change", leave);
})();
