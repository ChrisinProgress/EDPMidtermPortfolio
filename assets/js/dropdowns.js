(() => {
  "use strict";
  const desktop = matchMedia("(hover: hover) and (pointer: fine) and (forced-colors: none)");
  let active;
  const signal = () => document.dispatchEvent(new Event("portfolio:dropdown-changed"));
  const entries = Array.from(document.querySelectorAll("select")).map(select => {
    const trigger = document.createElement("button");
    const value = document.createElement("span");
    const popup = document.createElement("div");
    const labels = Array.from(select.labels || []);
    const label = select.getAttribute("aria-label") || labels.map(node => node.textContent.trim()).join(" ") || select.id;
    trigger.type = "button";
    trigger.className = "portfolio-select-trigger";
    trigger.setAttribute("role", "combobox");
    trigger.setAttribute("aria-label", label);
    trigger.setAttribute("aria-expanded", "false");
    trigger.setAttribute("aria-controls", select.id + "-listbox");
    trigger.setAttribute("aria-haspopup", "listbox");
    trigger.append(value);
    trigger.hidden = true;
    select.after(trigger);
    popup.className = "portfolio-select-list";
    popup.id = select.id + "-listbox";
    popup.setAttribute("role", "listbox");
    popup.setAttribute("aria-label", label);
    popup.hidden = true;
    document.body.append(popup);

    let options, items, index, prefix = "", typedAt = 0;
    const sync = () => {
      value.textContent = select.selectedOptions[0]?.label || "";
      trigger.disabled = select.disabled;
    };
    const entry = { trigger, popup, close, place, refresh };
    function refresh() {
      const enhanced = desktop.matches;
      const focused = document.activeElement === (enhanced ? select : trigger);
      if (!enhanced && active === entry) close();
      select.hidden = enhanced;
      trigger.hidden = !enhanced;
      sync();
      if (focused) (enhanced ? trigger : select).focus({ preventScroll: true });
    }
    function place() {
      const rect = trigger.getBoundingClientRect();
      const viewport = document.documentElement;
      if (!rect.width || rect.bottom < 0 || rect.top > viewport.clientHeight) { close(); return; }
      const gutter = 8, gap = 4;
      const below = viewport.clientHeight - rect.bottom - gutter - gap;
      const above = rect.top - gutter - gap;
      const width = Math.min(Math.max(rect.width, 190), viewport.clientWidth - gutter * 2);
      popup.style.width = width + "px";
      const rise = below < popup.scrollHeight && above > below;
      popup.style.left = Math.max(gutter, Math.min(rect.left, viewport.clientWidth - width - gutter)) + "px";
      popup.style.maxHeight = Math.max(0, rise ? above : below) + "px";
      popup.style.top = (rise ? rect.top - gap - popup.offsetHeight : rect.bottom + gap) + "px";
    }
    function highlight(next, reveal) {
      if (!items[next]) return;
      items[index]?.classList.remove("is-highlighted");
      index = next;
      const item = items[index];
      item.classList.add("is-highlighted");
      trigger.setAttribute("aria-activedescendant", item.id);
      if (reveal) {
        if (item.offsetTop < popup.scrollTop) popup.scrollTop = item.offsetTop;
        else if (item.offsetTop + item.offsetHeight > popup.scrollTop + popup.clientHeight) {
          popup.scrollTop = item.offsetTop + item.offsetHeight - popup.clientHeight;
        }
      }
    }
    function open(fallback = 0) {
      active?.close();
      options = Array.from(select.options).filter(option => !option.disabled && !option.hidden);
      if (!options.length) return;
      items = options.map((option, i) => {
        const item = document.createElement("div");
        item.id = popup.id + "-" + i;
        item.dataset.index = i;
        item.setAttribute("role", "option");
        item.setAttribute("aria-selected", String(option.selected));
        item.textContent = option.label;
        return item;
      });
      popup.replaceChildren(...items);
      popup.scrollTop = 0;
      popup.hidden = false;
      trigger.setAttribute("aria-expanded", "true");
      active = entry;
      place();
      if (active !== entry) return;
      const selected = options.findIndex(option => option.selected);
      highlight(selected < 0 ? (fallback < 0 ? items.length - 1 : 0) : selected, true);
      signal();
    }
    function close() {
      if (active !== entry) return;
      popup.hidden = true;
      trigger.setAttribute("aria-expanded", "false");
      trigger.removeAttribute("aria-activedescendant");
      prefix = "";
      active = null;
      signal();
    }
    function choose(next) {
      const option = options[next];
      if (option && !option.selected) {
        select.selectedIndex = option.index;
        select.dispatchEvent(new Event("input", { bubbles: true }));
        select.dispatchEvent(new Event("change", { bubbles: true }));
      }
      close();
    }
    trigger.addEventListener("click", () => {
      if (active === entry) close();
      else {
        trigger.focus({ preventScroll: true });
        open();
      }
    });
    trigger.addEventListener("blur", close);
    trigger.addEventListener("keydown", event => {
      const key = event.key;
      const opened = active === entry;
      if (key === "Tab") { close(); return; }
      if (key === "Escape" && !opened) return;
      const printable = key.length === 1 && key !== " " && !event.ctrlKey && !event.metaKey && !event.altKey;
      if (!printable && !["ArrowDown", "ArrowUp", "Home", "End", "Escape", "Enter", " "].includes(key)) return;
      event.preventDefault();
      if (key === "Escape") { close(); return; }
      if (!opened) open(key === "ArrowUp" ? -1 : 0);
      if (active !== entry) return;
      if (key === "Home") highlight(0, true);
      else if (key === "End") highlight(items.length - 1, true);
      else if (opened && key === "ArrowDown") highlight(Math.min(index + 1, items.length - 1), true);
      else if (opened && key === "ArrowUp") highlight(Math.max(index - 1, 0), true);
      else if (opened && (key === "Enter" || key === " ")) choose(index);
      else if (printable) {
        const letter = key.toLocaleLowerCase();
        const now = performance.now();
        prefix = now - typedAt < 600 && prefix !== letter ? prefix + letter : letter;
        typedAt = now;
        const start = prefix.length === 1 ? index + 1 : index;
        for (let offset = 0; offset < items.length; offset++) {
          const candidate = (start + offset) % items.length;
          if (options[candidate].label.toLocaleLowerCase().startsWith(prefix)) { highlight(candidate, true); break; }
        }
      }
    });
    popup.addEventListener("mousedown", event => {
      // Keep focus on the combobox while selecting an option.
      event.preventDefault();
    });
    popup.addEventListener("pointermove", event => {
      const item = event.target.closest("[data-index]");
      if (item && event.pointerType === "mouse") highlight(Number(item.dataset.index), false);
    }, { passive: true });
    popup.addEventListener("click", event => {
      const item = event.target.closest("[data-index]");
      if (item) choose(Number(item.dataset.index));
    });
    labels.forEach(node => node.addEventListener("click", event => {
      if (desktop.matches) { event.preventDefault(); trigger.focus(); }
    }));
    select.addEventListener("change", sync);
    select.addEventListener("portfolio:select-updated", sync);
    select.form?.addEventListener("reset", () => queueMicrotask(sync));
    return entry;
  });
  if (!entries.length) return;
  const refresh = () => entries.forEach(entry => entry.refresh());
  desktop.addEventListener("change", refresh);
  refresh();
  document.addEventListener("pointerdown", event => {
    if (active && !active.trigger.contains(event.target) && !active.popup.contains(event.target)) active.close();
  });
  window.addEventListener("resize", () => active?.place(), { passive: true });
  window.addEventListener("scroll", event => {
    if (active && !active.popup.contains(event.target)) active.place();
  }, { capture: true, passive: true });
  window.addEventListener("blur", () => active?.close());
  document.addEventListener("portfolio:view-changed", () => active?.close());
})();
