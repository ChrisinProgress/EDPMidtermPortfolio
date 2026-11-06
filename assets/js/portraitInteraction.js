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
    const flipIcon = flip.querySelector('.icon');
    const photo = scene.querySelector('[data-id-photo]');
    let frame = 0;
    let anchorY = motion.offsetTop;
    let pose = { x: 0, y: 0, sway: 0 };
    let gesture;
    let interacted = false;
    let suppressClick = false;
    let hovering = false;
    let pixelSelected = false;
    let photoPointerType;
    let flipEffect;
    const clamp = (value, min, max) => Math.max(min, Math.min(max, value));

    function boundSwing(angle, x, { left, right, height }) {
      const degrees = 180 / Math.PI;
      const clockwise = Math.atan2(x - left, height) * degrees;
      const counterclockwise = Math.atan2(right - x, height) * degrees;
      return clamp(angle, -Math.min(7, counterclockwise), Math.min(7, clockwise));
    }

    function setPose(x = 0, y = 0, sway = 0) {
      y = Math.max(14 - anchorY, y);
      pose = { x, y, sway };
      const vertical = anchorY + y;
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

    new ResizeObserver(() => {
      anchorY = motion.offsetTop;
      setPose(pose.x, pose.y, pose.sway);
    }).observe(scene);

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

    function retract({ vx, vy, lastMove, limits }) {
      if (reduced.matches) {
        setPose();
        return;
      }
      const released = pose;
      const momentum = Math.exp(-Math.max(0, performance.now() - lastMove) / 90);
      // Evaluate an underdamped spring from the release position and velocity.
      const spring = (position, velocity, time, damping, frequency) =>
        Math.exp(-damping * time) * (position * Math.cos(frequency * time)
          + (velocity + damping * position) / frequency * Math.sin(frequency * time));
      animatePose(2200, progress => {
        const time = progress * 2.2;
        const x = clamp(spring(released.x, vx * momentum, time, 6, 13), limits.left, limits.right);
        const y = clamp(spring(released.y, vy * momentum, time, 6, 13), limits.up, limits.down);
        const swing = spring(released.sway, -vx * momentum * .16, time, 2.5, 10);
        setPose(x, y, boundSwing(swing, x, limits));
      });
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
      if (reduced.matches) flipEffect?.cancel();
      if (reduced.matches && !gesture) {
        cancelAnimationFrame(frame);
        setPose();
      }
    });

    scene.addEventListener('pointerdown', event => {
      if (gesture || !event.isPrimary || event.button !== 0) return;
      suppressClick = false;
      if (event.target.closest('button, a, input') ||
          !event.target.closest('[data-student-id], [data-id-clip], [data-id-lanyard]')) return;
      interacted = true;
      const bounds = scene.getBoundingClientRect();
      const inset = (bounds.width - motion.offsetWidth) / 2;
      gesture = {
        pointer: event.pointerId, x: event.clientX, y: event.clientY, moved: false,
        vx: 0, vy: 0, lastMove: performance.now(),
        limits: {
          left: -clamp(bounds.left + inset - 10, 0, 80),
          right: clamp(document.documentElement.clientWidth - bounds.right + inset - 10, 0, 80),
          up: 14 - anchorY,
          down: 112,
          height: motion.offsetHeight,
        },
      };
    });
    window.addEventListener('pointermove', event => {
      if (gesture?.pointer !== event.pointerId) return;
      const dx = event.clientX - gesture.x;
      const dy = event.clientY - gesture.y;
      if (!gesture.moved) {
        if (Math.hypot(dx, dy) < 8) return;
        gesture.moved = true;
        gesture.start = pose;
        cancelAnimationFrame(frame);
        scene.setPointerCapture(event.pointerId);
        scene.classList.add('is-dragging');
      }
      const { start, limits } = gesture;
      const resist = (origin, distance, min, max) => {
        const room = Math.max(1, distance < 0 ? origin - min : max - origin);
        return clamp(origin + distance / (1 + Math.abs(distance) / room), min, max);
      };
      const x = resist(start.x, dx, limits.left, limits.right);
      const y = resist(start.y, dy, limits.up, limits.down);
      const now = performance.now();
      const elapsed = Math.max(8, now - gesture.lastMove) / 1000;
      gesture.vx = clamp((x - pose.x) / elapsed, -550, 550);
      gesture.vy = clamp((y - pose.y) / elapsed, -550, 550);
      gesture.lastMove = now;
      setPose(x, y, boundSwing(-x * .1, x, limits));
    });
    function release(event) {
      if (!gesture || ('pointerId' in event && gesture.pointer !== event.pointerId)) return;
      const released = gesture;
      gesture = undefined;
      if (!released.moved) return;
      if (scene.hasPointerCapture(released.pointer)) scene.releasePointerCapture(released.pointer);
      scene.classList.remove('is-dragging');
      suppressClick = true;
      retract(released);
    }
    window.addEventListener('pointerup', release);
    window.addEventListener('pointercancel', release);
    scene.addEventListener('lostpointercapture', release);
    window.addEventListener('blur', release);
    window.addEventListener('pagehide', event => {
      release(event);
      cancelAnimationFrame(frame);
      setPose();
    });

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
      if (flipEffect?.playState === 'running') return;
      const flipped = card.classList.toggle('is-flipped');
      if (flipped && front.contains(document.activeElement)) flip.focus({ preventScroll: true });
      front.inert = flipped;
      back.inert = !flipped;
      front.setAttribute('aria-hidden', String(flipped));
      back.setAttribute('aria-hidden', String(!flipped));
      flip.setAttribute('aria-pressed', String(flipped));
      if (event.target.closest('[data-id-flip]') && !reduced.matches) {
        const style = getComputedStyle(motion);
        const from = flipped ? 0 : 180;
        const to = flipped ? 180 : 0;
        flipEffect = flipIcon.animate([
          { transform: 'rotate(' + from + 'deg) scale(1)', filter: 'none' },
          { transform: 'rotate(' + (from + (to - from) * .24) + 'deg) scale(.9)', filter: 'drop-shadow(0 0 4px var(--red))', offset: .24 },
          { transform: 'rotate(' + to + 'deg) scale(1)', filter: 'none' },
        ], {
          duration: parseFloat(style.getPropertyValue('--id-flip-duration')),
          easing: style.getPropertyValue('--id-flip-ease').trim(),
        });
      }
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
      message.value = templates[brief.selectedOptions[0]?.dataset.brief] || '';
      previousTemplate = message.value;
    }
  });
})();
