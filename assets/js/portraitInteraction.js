(() => {
  "use strict";
  const reduced = window.matchMedia('(prefers-reduced-motion: reduce)');
  document.querySelectorAll('[data-student-id]').forEach(card => {
    let bounce;
    card.addEventListener('click', () => {
      const flipped = card.classList.toggle('is-flipped');
      card.querySelectorAll('.cjc-logo').forEach(logo => {
        logo.getAnimations().forEach(animation => animation.cancel());
        if (!reduced.matches) logo.animate([{transform:'scale(.85) rotate(-12deg)'},{transform:'scale(1.08) rotate(4deg)',offset:.65},{transform:'scale(1) rotate(0deg)'}],{duration:600,easing:'ease-out'});
      });
      bounce?.cancel();
      if (!reduced.matches) {
        bounce = card.animate([
          { translate: '0 0' }, { translate: '0 18px', offset: .18 },
          { translate: '0 -17px', offset: .39 }, { translate: '0 9px', offset: .59 },
          { translate: '0 -5px', offset: .77 }, { translate: '0 0' }
        ], { duration: 850, easing: 'ease-out' });
      }
      card.setAttribute('aria-pressed', String(flipped));
      card.setAttribute('aria-label', flipped ? 'Return to the front of Christopher’s portfolio ID' : 'Turn Christopher’s portfolio ID over');
      card.querySelector('.id-front').setAttribute('aria-hidden', String(flipped));
      card.querySelector('.id-back').setAttribute('aria-hidden', String(!flipped));
    });
  });
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
