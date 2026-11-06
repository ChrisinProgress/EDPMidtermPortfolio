(() => {
 'use strict';
 const reduced = matchMedia('(prefers-reduced-motion: reduce)');
 const afterIntro=callback=>{
  const root=document.documentElement;
  const pending=()=>root.classList.contains('intro-pending')||root.classList.contains('intro-revealing');
  if(!pending()){callback();return;}
  const observer=new MutationObserver(()=>{if(!pending()){observer.disconnect();callback();}});
  observer.observe(root,{attributes:true,attributeFilter:['class']});
  return () => observer.disconnect();
 };
 const tabs = [...document.querySelectorAll('[data-about-tab]')];
 const panels = [...document.querySelectorAll('[data-about-panel]')];
 let selectedAboutTab;
 let panelTransition;
 function selectTab(key, focus = false) {
  const tab = tabs.find(t => t.dataset.aboutTab === key);
  if (!tab) return;
  if (selectedAboutTab !== key) {
   tabs.forEach(t => { const active = t === tab; t.setAttribute('aria-selected', String(active)); t.tabIndex = active ? 0 : -1; });
   panels.forEach(panel => { panel.hidden = panel.dataset.aboutPanel !== key; });
   const panel = panels.find(item => item.dataset.aboutPanel === key);
   panelTransition?.cancel();
   if (selectedAboutTab && !reduced.matches) {
    panelTransition = panel.animate([{opacity:.55, translate:'0 5px'},{opacity:1, translate:'0 0'}], {duration:250,easing:'ease-out'});
   }
   selectedAboutTab = key;
   panel.dispatchEvent(new Event('abouttabchange', {bubbles:true}));
  }
  if (focus) tab.focus({preventScroll:true});
 }
 if(tabs.length) {
  document.querySelector('[data-about-tabs]').hidden = false;
  selectTab('expertise');
  tabs.forEach((tab,index) => {
   tab.addEventListener('click', () => selectTab(tab.dataset.aboutTab));
   tab.addEventListener('keydown', e => {
    let next;
    if(e.key === 'ArrowRight') next=(index+1)%tabs.length;
    if(e.key === 'ArrowLeft') next=(index+tabs.length-1)%tabs.length;
    if(e.key === 'Home') next=0;
    if(e.key === 'End') next=tabs.length-1;
    if(next !== undefined) {e.preventDefault();selectTab(tabs[next].dataset.aboutTab,true);}
   });
  });
  const revealHash = () => {
   const key = location.hash.slice(1);
   if(['credentials','materials','toolkit'].includes(key)) {
    selectTab(key);
    requestAnimationFrame(() => document.getElementById(key)?.scrollIntoView({behavior:reduced.matches?'instant':'smooth',block:'start'}));
   }
  };
  window.addEventListener('hashchange',revealHash);
  document.addEventListener('click', e => {
   const a=e.target.closest('a[href^="#"]');
   if(a && ['#credentials','#materials','#toolkit'].includes(a.hash)) selectTab(a.hash.slice(1));
  });
  revealHash();
 }
 const typingLeadIn = 290;
 const sectionTypingInterval = 55;
 const projectTypingInterval = 60;
 const reveal = (element, duration, delay = 0, distance = 10, scale = 1) => {
  if (element && !reduced.matches) return element.animate(
   [{ opacity: 0, translate: '0 ' + distance + 'px', ...(scale !== 1 && { scale }) },
    { opacity: 1, translate: '0 0', ...(scale !== 1 && { scale: 1 }) }],
   { duration, delay, easing: 'cubic-bezier(.22,.8,.25,1)', fill: 'backwards' }
  );
 };
 function typeCharacters(length, render, complete, interval = 60) {
  let count = 0;
  const timer = setInterval(() => {
   render(++count);
   if (count >= length) {
    clearInterval(timer);
    complete();
   }
  }, interval);
  return () => clearInterval(timer);
 }
 afterIntro(() => {
  const header = document.querySelector('[data-site-header]');
  reveal(header, 700, 0, -10);
  reveal(header?.querySelector('.wordmark'), 600);
  if (matchMedia('(min-width: 900px)').matches) {
   header?.querySelectorAll('[data-nav-link]').forEach((link, index) => reveal(link, 450, index * 100));
  }
  document.dispatchEvent(new Event('portfolio:intro-ready'));
 });
 const typingParts=[...document.querySelectorAll('[data-typing-part]')];
 if(typingParts.length && !reduced.matches) {
  const phrases=typingParts.map(p=>p.textContent);
  const heading = typingParts[0].closest('h1');
  const copy = heading.closest('.hero-copy');
  const sentences = [...copy.querySelectorAll('[data-hero-sentence]')];
  let timer, cancelTyping, finished=false;
  const finish=()=>{
   if (finished) return;
   finished = true;
   clearTimeout(timer);
   cancelTyping?.();
   typingParts.forEach((p,i)=>p.textContent=phrases[i]);
   heading.classList.add('typing-done');
   copy.classList.remove('is-introducing');
   sentences.forEach((sentence, index) => {
    if (!reduced.matches) sentence.animate([{ opacity: 0 }, { opacity: 1 }], {
     duration: 650, delay: index * 330, easing: 'ease-out', fill: 'backwards'
    });
   });
   reveal(copy.querySelector('.hero-actions'), 600, 880);
  };
  const start=()=>{
   if(finished || reduced.matches){finish();return;}
   copy.classList.add('is-introducing');
   typingParts.forEach(p=>p.textContent='');
   reveal(heading.querySelector('.hero-greeting'), 600, 110);
   timer=setTimeout(() => {
    cancelTyping=typeCharacters(phrases.join('').length, remaining=>{
     typingParts.forEach((p,i)=>{p.textContent=phrases[i].slice(0,Math.max(0,remaining));remaining-=phrases[i].length;});
    },finish);
   }, typingLeadIn);
  };
  afterIntro(start);
  reduced.addEventListener('change',()=>{if(reduced.matches) finish();});
 }
 const dock=document.querySelector('[data-social-dock]');
 const socialSlot=document.querySelector('[data-social-slot]');
 if(dock&&socialSlot){
  let floating=false,frame=0,flight;
  const moveDock=next=>{
   if(next===floating)return;
   const focused=dock.contains(document.activeElement)?document.activeElement:null;
   const from=dock.getBoundingClientRect();flight?.cancel();floating=next;
   dock.classList.toggle('is-floating',next);
   (next?document.body:socialSlot).append(dock);focused?.focus({preventScroll:true});
   const to=dock.getBoundingClientRect();
   if(!reduced.matches)flight=dock.animate([{transform:'translate('+ (from.left-to.left)+'px,'+(from.top-to.top)+'px)',opacity:.65},{transform:'translate(0,0)',opacity:1}],{duration:460,easing:'cubic-bezier(.22,.8,.25,1)'});
  };
  const update=()=>{frame=0;const top=document.querySelector('[data-site-header]')?.getBoundingClientRect().bottom||80;moveDock(socialSlot.getBoundingClientRect().bottom<top+8);};
  const queue=()=>{if(!frame)frame=requestAnimationFrame(update);};
  const enter=()=>{
   update();
   dock.querySelectorAll('a').forEach((link,index) => {
    reveal(link,500,index*45);
    link.style.setProperty('--social-pulse-delay', (1.1 + index * .3) + 's');
   });
   dock.classList.add('is-ready');
  };
  afterIntro(enter);
  window.addEventListener('scroll',queue,{passive:true});window.addEventListener('resize',queue);window.addEventListener('pageshow',queue);
 }

 document.querySelectorAll('[data-media-image]').forEach(img=>{
  const fallback=()=>{
   img.hidden=true;
   const slot=img.closest('.media-slot');
   const backup=slot?.querySelector('.media-fallback');
   if(backup){backup.hidden=false;slot.classList.add('is-placeholder');slot.removeAttribute('data-media-preview');if(slot.tagName==='A'){slot.removeAttribute('href');slot.removeAttribute('aria-label');}}
   else {const span=document.createElement('span');span.className='tool-logo-fallback';span.textContent=img.closest('[data-toolkit-item]')?.querySelector('.toolkit-item-label')?.textContent.slice(0,2)||'◇';img.after(span);}
  };
  img.addEventListener('error',fallback,{once:true});if(img.complete && !img.naturalWidth) fallback();
 });
 const closingPreviews = new WeakMap();
 function closePreview(preview, button) {
  if (!preview.hasAttribute('open') || closingPreviews.has(preview)) return;
  const finish = () => {
   if (!preview.hasAttribute('open')) return;
   if (typeof preview.close === 'function') preview.close();
   else {
    preview.removeAttribute('open');
    preview.dispatchEvent(new Event('close'));
   }
  };
  if (reduced.matches) {
   finish();
   return;
  }
  const duration = 260;
  const appearance = getComputedStyle(preview);
  const animation = preview.animate(
   [{opacity:appearance.opacity, scale:'1', transform:appearance.transform}, {opacity:0, scale:'.98', transform:appearance.transform}],
   {duration,easing:'ease-in',fill:'forwards'}
  );
  const effects = [animation];
  if (button) {
   const transform = getComputedStyle(button).transform;
   effects.push(button.animate(
    [{transform}, {transform:'scale(.94)', offset:.26}, {transform}],
    {duration,easing:'ease',fill:'forwards'}
   ));
   const icon = button.querySelector('.icon');
   if (icon) effects.push(icon.animate(
    [{transform:getComputedStyle(icon).transform}, {transform:'rotate(90deg)'}],
    {duration,easing:'ease',fill:'forwards'}
   ));
  }
  closingPreviews.set(preview, effects);
  animation.finished.then(() => { if (closingPreviews.get(preview) === effects) finish(); }, () => {});
 }
 document.addEventListener('click', event => {
  const preview = event.target.closest('[data-preview-dialog][open]');
  if (!preview) return;
  const button = event.target.closest('[data-preview-dismiss]');
  if (button) {
   closePreview(preview, button);
  } else if (event.target === preview) {
   const bounds = preview.getBoundingClientRect();
   if (preview.matches('[data-material-dialog], [data-artwork-dialog]') ||
       event.clientX < bounds.left || event.clientX > bounds.right ||
       event.clientY < bounds.top || event.clientY > bounds.bottom) closePreview(preview);
  }
 });
 document.addEventListener('cancel', event => {
  if (!event.target.matches('[data-preview-dialog]')) return;
  event.preventDefault();
  closePreview(event.target);
 }, true);
 document.addEventListener('close', event => {
  closingPreviews.get(event.target)?.forEach(animation => animation.cancel());
  closingPreviews.delete(event.target);
 }, true);

 const dialog = document.querySelector('[data-material-dialog]');
 if (dialog) {
  const image = dialog.querySelector('[data-material-full]');
  const zoom = dialog.querySelector('[data-material-zoom]');
  const close = dialog.querySelector('[data-material-close]');
  const stage = dialog.querySelector('.material-image-stage');
  let opener, infographic;

  function setZoom(active) {
   dialog.classList.toggle('is-zoomed', active);
   zoom.setAttribute('aria-pressed', String(active));
   const label = active ? 'Fit infographic to screen' : 'Zoom in on infographic';
   zoom.setAttribute('aria-label', label);
   zoom.title = label;
   if (infographic) image.setAttribute('aria-label', label + ': ' + image.alt);
   stage.scrollTop = 0;
   stage.scrollLeft = 0;
  }

  function toggleZoom() {
   if (!closingPreviews.has(dialog) && infographic) setZoom(!dialog.classList.contains('is-zoomed'));
  }

  document.querySelector('[data-about-explorer]')?.addEventListener('click', event => {
   const link = event.target.closest('[data-media-preview][href]');
   if (!link || event.button || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
   event.preventDefault();
   opener = link;
   const preview = link.querySelector('img');
   infographic = Boolean(link.closest('.material-card--infographic'));
   dialog.classList.toggle('can-zoom', infographic);
   dialog.setAttribute('aria-label', link.dataset.previewTitle || preview.alt || 'Image preview');
   const source = link.href;
   image.src = preview.currentSrc || preview.src;
   if (image.src !== source) {
    const original = new Image();
    original.src = source;
    original.decode().then(() => {
     if (dialog.open && opener === link) image.src = source;
    }, () => {}); // The fitted thumbnail remains usable if the original fails.
   }
   image.alt = preview.alt;
   zoom.hidden = !infographic;
   if (infographic) {
    image.tabIndex = 0;
    image.setAttribute('role', 'button');
   } else {
    ['tabindex','role','aria-label'].forEach(attribute => image.removeAttribute(attribute));
   }
   setZoom(false);
   dialog.showModal();
   close.focus({preventScroll:true});
  });
  image.addEventListener('click', toggleZoom);
  zoom.addEventListener('click', toggleZoom);
  dialog.addEventListener('keydown', event => {
   if (event.target === image && infographic && (event.key === 'Enter' || event.key === ' ')) {
    event.preventDefault();
    toggleZoom();
   }
  });
  dialog.addEventListener('close', () => {
   opener?.focus({preventScroll:true});
  });
 }
 // Shared manual sliders: swipe, keyboard, scrollbar, or the two controls.
 document.querySelectorAll('[data-slider]').forEach(section=>{
  const viewport=section.querySelector('[data-slider-viewport]');
  const controls=section.querySelector('[data-slider-controls]');
  if(!viewport||!controls) return;
  const prev=controls.querySelector('[data-slide-prev]');
  const next=controls.querySelector('[data-slide-next]');
  let frame;
  const update=()=>{
   frame=0;
   const moving=viewport.closest('[data-toolkit-row]')?.classList.contains('is-moving');
   prev.disabled=!moving&&viewport.scrollLeft<=2;
   next.disabled=!moving&&viewport.scrollWidth-viewport.clientWidth-viewport.scrollLeft<=2;
  };
  const queue=()=>{if(!frame)frame=requestAnimationFrame(update);};
  const move=direction=>{
   viewport.dispatchEvent(new Event('beforebrowse'));
   const card=[...section.querySelectorAll('[data-slide-card]')].find(c=>!c.hidden);
   if(!card) return;
   const gap=parseFloat(getComputedStyle(card.parentElement).columnGap)||24;
   viewport.scrollBy({left:direction*(card.getBoundingClientRect().width+gap),behavior:reduced.matches?'instant':'smooth'});
  };
  prev.addEventListener('click',()=>move(-1));next.addEventListener('click',()=>move(1));
  viewport.addEventListener('slidergo',e=>move(e.detail));
  viewport.addEventListener('scroll',queue,{passive:true});
  viewport.addEventListener('sliderchange',queue);
  viewport.addEventListener('keydown',e=>{
   if(e.target!==viewport) return;
   if(e.key==='ArrowLeft'||e.key==='ArrowRight'){e.preventDefault();move(e.key==='ArrowRight'?1:-1);}
   if(e.key==='Home'||e.key==='End'){e.preventDefault();viewport.scrollTo({left:e.key==='Home'?0:viewport.scrollWidth,behavior:reduced.matches?'instant':'smooth'});}
  });
  new ResizeObserver(queue).observe(viewport);
  controls.hidden=false;update();
 });
 // Keep the visible gallery controls beside the About tabs.
 const aboutControls=document.querySelector('[data-about-slider-controls]');
 if(aboutControls){
  const previous=aboutControls.querySelector('[data-about-prev]'),next=aboutControls.querySelector('[data-about-next]');
  const active=()=>document.querySelector('[data-about-panel]:not([hidden]) [data-slider-viewport]');
  const update=()=>{const v=active();if(!v)return;const moving=v.closest('[data-toolkit-row]')?.classList.contains('is-moving');previous.disabled=!moving&&v.scrollLeft<=2;next.disabled=!moving&&v.scrollWidth-v.clientWidth-v.scrollLeft<=2;};
  previous.addEventListener('click',()=>active()?.dispatchEvent(new CustomEvent('slidergo',{detail:-1})));
  next.addEventListener('click',()=>active()?.dispatchEvent(new CustomEvent('slidergo',{detail:1})));
  document.querySelectorAll('[data-about-panel] [data-slider-viewport]').forEach(v=>{v.addEventListener('scroll',update,{passive:true});v.addEventListener('sliderchange',update);new ResizeObserver(update).observe(v);});
  aboutControls.closest('[data-about-explorer]').addEventListener('abouttabchange', () => requestAnimationFrame(update));
  aboutControls.hidden=false;update();
 }
 // Play the same sequence for a direct project URL or an in-page project visit.
 function animateProject(page) {
  const detailTitle = page.querySelector('[data-project-title]');
  if (!detailTitle) return;
  const title = detailTitle.textContent;
  detailTitle.setAttribute('aria-label', title);
  const visual = document.createElement('span');
  visual.setAttribute('aria-hidden', 'true');
  visual.append(...detailTitle.childNodes);
  detailTitle.append(visual);
  const walker = document.createTreeWalker(visual, NodeFilter.SHOW_TEXT);
  const segments = [];
  while (walker.nextNode()) segments.push(walker.currentNode);
  const parts = segments.map(node => {
   const value = node.data;
   const prefix = document.createTextNode(value);
   const suffix = document.createElement('span');
   suffix.className = 'project-typing-remainder';
   node.replaceWith(prefix, suffix);
   return { value, prefix, suffix };
  });
  const projectCaret = document.createElement('span');
  projectCaret.className = 'typing-cursor heading-typing-cursor project-typing-caret';
  projectCaret.setAttribute('aria-hidden', 'true');

  function renderTitle(count) {
   let caretAfter = parts[0].prefix;
   for (const part of parts) {
    const shown = Math.min(count, part.value.length);
    part.prefix.data = part.value.slice(0, shown);
    part.suffix.textContent = part.value.slice(shown);
    if (shown) caretAfter = part.prefix;
    count -= shown;
   }
   if (caretAfter.nextSibling !== projectCaret) caretAfter.after(projectCaret);
  }

  const lifecycle = new AbortController();
  const entrance = [], afterTitle = [];
  let startTimer, cancelTyping, cancelIntro, finished = false, skipEntrance = false;
  function finishTitle() {
   if (finished) return;
   finished = true;
   clearTimeout(startTimer);
   cancelTyping?.();
   renderTitle(title.length);
   if (!skipEntrance) afterTitle.forEach(animation => animation.play());
  }

  function finishEntrance() {
   skipEntrance = true;
   cancelIntro?.();
   finishTitle();
   entrance.forEach(animation => animation.cancel());
   lifecycle.abort();
  }
  function startEntrance() {
   if (finished) return;
   if (reduced.matches) { finishEntrance(); return; }
   const find = selector => page.querySelector(selector);
   const queue = (selector, duration, delay, stagger = 0, scale = 1) => {
    page.querySelectorAll(selector).forEach((element, index) => {
     const animation = reveal(element, duration, delay + index * stagger, scale === 1 ? 12 : 0, scale);
     animation.pause();
     animation.currentTime = 0;
     entrance.push(animation);
     afterTitle.push(animation);
    });
   };
   const stagger = 65;
   entrance.push(reveal(page, 300), reveal(find('.collection-return'), 300, 40),
    reveal(find('.detail-project-type'), 300, 60));
   queue('[data-project-description]', 350, 0);
   queue('.detail-meta > div:first-child > dt', 300, 190);
   queue('.detail-technologies > span', 250, 255, stagger);
   queue('.detail-resource-actions .button', 250, 280, stagger);
   queue('.detail-gallery-heading', 300, 360);
   queue('.detail-meta > div:nth-child(2)', 250, 360);
   queue('.detail-stage', 400, 425, 0, .98);
   queue('.detail-screen-item', 250, 490, stagger);
   queue('.detail-return-button', 300, 620 + Math.max(0, page.querySelectorAll('.detail-screen-item').length - 1) * stagger);
   renderTitle(0);
   startTimer = setTimeout(() => {
    cancelTyping = typeCharacters(title.length, renderTitle, finishTitle, projectTypingInterval);
   }, 150);
  }
  if (reduced.matches) { finishEntrance(); return; }
  const options = {signal:lifecycle.signal};
  window.addEventListener('pagehide', finishEntrance, options);
  reduced.addEventListener('change', event => { if (event.matches) finishEntrance(); }, options);
  page.addEventListener('portfolio:project-unmounting', finishEntrance, options);
  page.addEventListener('focusin', event => { if (event.target !== page) finishEntrance(); }, options);
  page.addEventListener('pointerdown', event => { if (event.target.closest('a,button')) finishEntrance(); }, options);
  cancelIntro = afterIntro(startEntrance);
 }
 const initialProject = document.querySelector('main.project-detail');
 if (initialProject) animateProject(initialProject);
 document.addEventListener('portfolio:project-mounted', event => animateProject(event.target));
 const feature=document.querySelector('.feature-showcase');
 if(feature){
  const updateFeature=()=>{const r=feature.getBoundingClientRect();const visible=Math.max(0,Math.min(r.bottom,innerHeight)-Math.max(r.top,0))/Math.min(r.height,innerHeight);feature.style.setProperty('--feature-presence',reduced.matches?1:Math.min(1,visible/0.72));};
  let f=0;const queueFeature=()=>{if(!f)f=requestAnimationFrame(()=>{f=0;updateFeature();});};
  feature.classList.add('feature-responsive');window.addEventListener('scroll',queueFeature,{passive:true});window.addEventListener('resize',queueFeature);reduced.addEventListener('change',queueFeature);updateFeature();
 }
 // Repeat the heading typing effect whenever a section comes into view.
 const headingStates=new Map();
 const finishHeading=state=>{state.cancelTyping?.();state.nodes.forEach(n=>n.node.textContent=n.text);state.heading.style.minHeight='';state.running=false;};
 const headingObserver=new IntersectionObserver(entries=>entries.forEach(entry=>{
  const state=headingStates.get(entry.target);if(!state)return;
  if(!entry.isIntersecting||reduced.matches){finishHeading(state);return;}
  if(state.running)return;
  state.heading.style.minHeight=state.heading.getBoundingClientRect().height+'px';
  state.nodes.forEach(n=>n.node.textContent='');state.running=true;
  state.cancelTyping=typeCharacters(state.length,position=>{
   let left=position;state.nodes.forEach(n=>{n.node.textContent=n.text.slice(0,Math.max(0,left));left-=n.text.length;});
  },()=>finishHeading(state),sectionTypingInterval);
 }),{threshold:.25});
 function registerHeading(heading) {
  if(heading.closest('dialog'))return;
  heading.setAttribute('aria-label',heading.textContent.trim());
  const wrapper=document.createElement('span');wrapper.setAttribute('aria-hidden','true');while(heading.firstChild)wrapper.append(heading.firstChild);heading.append(wrapper);
  const walker=document.createTreeWalker(wrapper,NodeFilter.SHOW_TEXT);const nodes=[];let node;
  while(node=walker.nextNode())nodes.push({node,text:node.textContent});
  const caret=document.createElement('span');caret.className='typing-cursor heading-typing-cursor';caret.setAttribute('aria-hidden','true');heading.append(caret);
  headingStates.set(heading,{heading,nodes,length:nodes.reduce((n,t)=>n+t.text.length,0),running:false});
 }
 document.querySelectorAll('[data-type-heading]').forEach(registerHeading);
 afterIntro(()=>headingStates.forEach(state=>headingObserver.observe(state.heading)));
 reduced.addEventListener('change',()=>{if(reduced.matches)headingStates.forEach(finishHeading);});
 // Highlight policy contents without adding another animation or moving focus.
 function observePolicySections(page) {
  const links = [...page.querySelectorAll('.policy-index a[href^="#"]')];
  if (!links.length) return;
  const observer = new IntersectionObserver(entries => {
   const visible = entries.find(entry => entry.isIntersecting);
   if (visible) links.forEach(link => {
    if (link.hash === '#' + visible.target.id) link.setAttribute('aria-current', 'location');
    else link.removeAttribute('aria-current');
   });
  }, {rootMargin:'-15% 0px -65% 0px'});
  links.forEach(link => {
   const section = page.querySelector(link.hash);
   if (section) observer.observe(section);
  });
  page.addEventListener('portfolio:policy-unmounting', () => observer.disconnect(), {once:true});
 }
 const initialPolicy = document.querySelector('main.policy-page');
 if (initialPolicy) observePolicySections(initialPolicy);
 document.addEventListener('portfolio:policy-mounted', event => {
  const page = event.target;
  const heading = page.querySelector('[data-type-heading]');
  if (heading) {
   registerHeading(heading);
   headingObserver.observe(heading);
   page.addEventListener('portfolio:policy-unmounting', () => {
    headingObserver.unobserve(heading);
    finishHeading(headingStates.get(heading));
    headingStates.delete(heading);
   }, {once:true});
  }
  observePolicySections(page);
 });
 // Image-only artwork rows, moving in opposite directions.
 const artwork=document.querySelector('[data-artwork-showcase]');
 if(artwork){
  const rows=[...artwork.querySelectorAll('[data-artwork-row]')];
  rows.forEach(row=>{
   const track=row.querySelector('[data-artwork-track]');
   const sequence=row.querySelector('[data-artwork-sequence]');
   const configure=()=>{
    track.querySelectorAll('[data-artwork-repeat]').forEach(c=>c.remove());
    if(reduced.matches) return;
    const period=sequence.getBoundingClientRect().width;
    if(!period) return;
    row.style.setProperty('--artwork-period',period+'px');
    row.style.setProperty('--artwork-duration',Math.max(34,period/36)+'s');
    for(let i=0;i<Math.ceil(row.clientWidth/period)+1;i++){
     const copy=sequence.cloneNode(true);copy.removeAttribute('data-artwork-sequence');copy.dataset.artworkRepeat='';copy.setAttribute('aria-hidden','true');copy.querySelectorAll('a').forEach(a=>a.tabIndex=-1);track.append(copy);
    }
   };
   new ResizeObserver(configure).observe(row);reduced.addEventListener('change',configure);configure();
  });
  new IntersectionObserver(([entry])=>artwork.classList.toggle('is-offscreen',!entry.isIntersecting)).observe(artwork);
  const preview=artwork.querySelector('[data-artwork-dialog]');let origin;
  artwork.addEventListener('click',e=>{
   const a=e.target.closest('[data-artwork]');if(!a||e.ctrlKey||e.metaKey||e.shiftKey)return;
   e.preventDefault();origin=a;const img=preview.querySelector('[data-artwork-full]');img.src=a.href;img.alt=a.querySelector('img').alt;artwork.classList.add('is-previewing');preview.showModal();
  });
  preview.addEventListener('close',()=>{
   artwork.classList.remove('is-previewing');
   if(origin?.closest('[data-artwork-repeat]')){const href=origin.getAttribute('href');origin=[...artwork.querySelectorAll('[data-artwork-sequence] a')].find(a=>a.getAttribute('href')===href);}
   origin?.focus({preventScroll:true});
  });
 }
})();
