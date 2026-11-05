(() => {
 'use strict';
 const reduced=matchMedia('(prefers-reduced-motion: reduce)');
 document.querySelectorAll('[data-toolkit-collection]').forEach(section=>{
  const filter=section.querySelector('[data-toolkit-select]');
  const list=section.querySelector('[data-toolkit-list]');
  const items=[...section.querySelectorAll('[data-toolkit-item]')];
  const viewport=section.querySelector('[data-toolkit-viewport]');
  const row=section.querySelector('[data-toolkit-row]');
  const strip=section.querySelector('[data-toolkit-strip]');
  const controls=section.querySelector('[data-toolkit-filter-controls]');
  const results=section.querySelector('[data-toolkit-results]');
  if(!viewport||!controls||!list||!filter) return;
  let category='all',paused=false,period=0,resizeFrame=0;
  const offset=()=>row.classList.contains('is-moving')?Math.abs(new DOMMatrixReadOnly(getComputedStyle(strip).transform).m41)%(period||1):viewport.scrollLeft;
  const configure=(position=0)=>{
   row.classList.remove('is-moving');strip.querySelector('[data-toolkit-repeat]')?.remove();
   if(category==='all'&&!paused&&!reduced.matches&&viewport.clientWidth){
    period=list.getBoundingClientRect().width+(parseFloat(getComputedStyle(strip).columnGap)||18);
    const copy=list.cloneNode(true);copy.removeAttribute('data-toolkit-list');copy.removeAttribute('aria-label');copy.dataset.toolkitRepeat='';copy.setAttribute('aria-hidden','true');copy.inert=true;
    copy.querySelectorAll('[data-toolkit-item]').forEach(item=>{item.removeAttribute('data-toolkit-item');item.removeAttribute('data-slide-card');});strip.append(copy);
    const duration=Math.max(55,period/34);strip.style.setProperty('--toolkit-period',period+'px');strip.style.setProperty('--toolkit-duration',duration+'s');strip.style.setProperty('--toolkit-delay',-(position/period*duration)+'s');viewport.scrollLeft=0;row.classList.add('is-moving');
   }else viewport.scrollLeft=Math.max(0,position);
   viewport.dispatchEvent(new Event('sliderchange'));
  };
  const pause=()=>{if(category!=='all'||paused||reduced.matches)return;const position=offset();paused=true;configure(position);};
  section.querySelectorAll('[data-slide-prev],[data-slide-next]').forEach(b=>b.addEventListener('click',pause,true));
  viewport.addEventListener('beforebrowse',pause);viewport.addEventListener('focusin',pause);viewport.addEventListener('pointerdown',pause);
  const resume=()=>{if(category==='all'&&paused&&!section.contains(document.activeElement)){const position=offset();paused=false;configure(position);}};
  section.addEventListener('focusout',()=>requestAnimationFrame(resume));section.addEventListener('pointerleave',resume);
  filter.addEventListener('change',()=>{
   category=filter.value;paused=false;
   items.forEach(item=>{item.hidden=category!=='all'&&item.dataset.toolkitCategory!==category;});
   results.textContent=filter.selectedOptions[0].textContent;
   viewport.setAttribute('aria-label','Tools: '+results.textContent);configure(0);
  });
  let observedWidth=-1;
  new ResizeObserver(()=>{if(viewport.clientWidth===observedWidth)return;observedWidth=viewport.clientWidth;cancelAnimationFrame(resizeFrame);resizeFrame=requestAnimationFrame(()=>configure(offset()));}).observe(viewport);
  new IntersectionObserver(([entry])=>row.classList.toggle('is-offscreen',!entry.isIntersecting)).observe(row);
  reduced.addEventListener('change',()=>configure(offset()));
  document.addEventListener('visibilitychange',()=>row.classList.toggle('is-offscreen',document.hidden));
  controls.hidden=false;configure();
 });
})();
