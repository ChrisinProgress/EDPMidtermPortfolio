(() => {
  "use strict";
  const reduced = window.matchMedia('(prefers-reduced-motion: reduce)');
  const scene = document.querySelector('[data-id-scene]');
  if (scene) {
    const motion = scene.querySelector('[data-id-motion]');
    const lanyard = scene.querySelector('[data-id-lanyard]');
    const clip = scene.querySelector('[data-id-clip]');
    const card = scene.querySelector('[data-student-id]');
    const front = scene.querySelector('[data-id-front]');
    const back = scene.querySelector('[data-id-back]');
    const flip = scene.querySelector('[data-id-flip]');
    const photo = scene.querySelector('[data-id-photo]');
    let frame = 0;
    let pose = { x: 0, y: 0, sway: 0 };
    let gesture;
    let interacted = false;
    let suppressClick = false;
    let hovering = false;
    let pixelSelected = false;
    let photoPointerType;

    function setPose(x = 0, y = 0, sway = 0) {
      pose = { x, y, sway };
      const vertical = Math.max(8, motion.offsetTop + y);
      motion.style.setProperty('--id-x', x + 'px');
      motion.style.setProperty('--id-y', y + 'px');
      motion.style.setProperty('--id-sway', sway + 'deg');
      if (x === 0 && y === 0) {
        scene.style.removeProperty('--id-lace-size');
        scene.style.removeProperty('--id-lace-angle');
        return;
      }
      scene.style.setProperty('--id-lace-size', Math.hypot(x, vertical) + 'px');
      scene.style.setProperty('--id-lace-angle', -Math.atan2(x, vertical) + 'rad');
    }

    function animatePose(duration, render, finish = setPose) {
      cancelAnimationFrame(frame);
      const start = performance.now();
      function tick(now) {
        const progress = Math.min(1, (now - start) / duration);
        render(progress);
        frame = progress < 1 ? requestAnimationFrame(tick) : 0;
        if (!frame) finish();
      }
      frame = requestAnimationFrame(tick);
    }

    function sway(amplitude, duration) {
      animatePose(duration, progress => {
        const swing = amplitude * Math.sin(progress * Math.PI * 6) * (1 - progress) ** 2;
        setPose(swing, 0, -swing * .42);
      });
    }

    function retract() {
      if (reduced.matches) {
        setPose();
        return;
      }
      const released = pose;
      animatePose(740, progress => {
        const spring = Math.exp(-6 * progress) * Math.cos(9 * progress) * (1 - progress);
        setPose(released.x * spring, released.y * spring, released.sway * spring);
      }, () => sway(Math.min(10, 2 + Math.hypot(released.x, released.y) / 12), 2100));
    }

    function enter() {
      scene.classList.remove('is-awaiting-intro');
      if (interacted || reduced.matches) return;
      [clip, lanyard].forEach(part => part.animate([{ opacity: 0 }, { opacity: 1 }], 500));
      card.animate([{ opacity: 0 }, { opacity: 1 }], 900);
      animatePose(900, progress => {
        const remaining = (1 - progress) ** 3;
        setPose(0, -28 * remaining, 0);
      }, () => sway(10, 2400));
    }

    const root = document.documentElement;
    if (root.matches('.intro-pending, .intro-revealing') && !reduced.matches) {
      scene.classList.add('is-awaiting-intro');
      document.addEventListener('portfolio:intro-ready', enter, { once: true });
    } else enter();
    reduced.addEventListener('change', () => {
      if (reduced.matches && !gesture) {
        cancelAnimationFrame(frame);
        setPose();
      }
    });

    motion.addEventListener('pointerdown', event => {
      if (gesture || !event.isPrimary || event.button !== 0) return;
      suppressClick = false;
      if (event.target.closest('[data-id-flip]')) return;
      if (event.pointerType !== 'mouse' && !clip.contains(event.target)) return;
      interacted = true;
      gesture = { pointer: event.pointerId, x: event.clientX, y: event.clientY, moved: false };
    });
    window.addEventListener('pointermove', event => {
      if (gesture?.pointer !== event.pointerId) return;
      const dx = event.clientX - gesture.x;
      const dy = event.clientY - gesture.y;
      if (!gesture.moved) {
        if (Math.hypot(dx, dy) < 7) return;
        gesture.moved = true;
        gesture.start = pose;
        cancelAnimationFrame(frame);
        motion.setPointerCapture(event.pointerId);
        motion.classList.add('is-dragging');
      }
      const bounds = scene.getBoundingClientRect();
      const limit = Math.max(9, Math.min(68, bounds.left + 4, window.innerWidth - bounds.right + 4));
      const x = Math.max(-limit, Math.min(limit, gesture.start.x + dx * .68));
      const y = Math.max(-23, Math.min(105, gesture.start.y + dy * .72));
      setPose(x, y, x * .09);
    });
    function release(event) {
      if (gesture?.pointer !== event.pointerId) return;
      const { moved } = gesture;
      gesture = undefined;
      if (!moved) return;
      motion.classList.remove('is-dragging');
      suppressClick = true;
      retract();
    }
    window.addEventListener('pointerup', release);
    window.addEventListener('pointercancel', release);
    motion.addEventListener('lostpointercapture', release);

    function updatePhoto() {
      photo.classList.toggle('is-pixel', hovering || pixelSelected);
      photo.setAttribute('aria-pressed', String(pixelSelected));
    }
    photo.addEventListener('pointerenter', event => {
      if (event.pointerType === 'mouse' && !gesture?.moved) {
        hovering = true;
        updatePhoto();
      }
    });
    photo.addEventListener('pointerleave', () => {
      hovering = false;
      updatePhoto();
    });
    photo.addEventListener('pointerdown', event => { photoPointerType = event.pointerType; });
    photo.addEventListener('click', event => {
      if (event.detail && (suppressClick || photoPointerType === 'mouse')) return;
      pixelSelected = !pixelSelected;
      updatePhoto();
    });
    motion.addEventListener('click', event => {
      if ((suppressClick && event.detail) || event.target.closest('[data-id-photo], [data-id-clip]')) return;
      const flipped = card.classList.toggle('is-flipped');
      if (flipped && front.contains(document.activeElement)) flip.focus({ preventScroll: true });
      front.inert = flipped;
      back.inert = !flipped;
      front.setAttribute('aria-hidden', String(flipped));
      back.setAttribute('aria-hidden', String(!flipped));
      flip.setAttribute('aria-pressed', String(flipped));
    });
  }
  const brief = document.querySelector('[data-project-brief]');
  const message = document.querySelector('#contact-message');
  const templates = {
    website: 'Hi Christopher! I would like help with a website or web system.\n\nMy goal: \nFeatures I need: \nPreferred timeline: ',
    design: 'Hi Christopher! I would like help with a design project.\n\nWhat I need designed: \nAudience and style: \nPreferred timeline: ',
    collaboration: 'Hi Christopher! I would like to collaborate.\n\nAbout the opportunity: \nHow we could work together: '
  };
  let previousTemplate = '';
  brief?.addEventListener('change', () => {
    if (!message) return;
    if (!message.value.trim() || message.value === previousTemplate) {
      message.value = templates[brief.value] || '';
      previousTemplate = message.value;
    }
  });
})();
