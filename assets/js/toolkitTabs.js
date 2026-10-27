(() => {
  "use strict";

  document.querySelectorAll("[data-toolkit-tabs]").forEach((section) => {
    const tabs = Array.from(section.querySelectorAll("[data-toolkit-tab]"));
    const panels = Array.from(section.querySelectorAll("[data-toolkit-panel]"));
    if (!tabs.length || tabs.length !== panels.length) return;

    const activate = (index, focus = false) => {
      const activeTab = tabs[index];
      if (!activeTab) return;
      const category = activeTab.dataset.toolkitTab;
      tabs.forEach((tab) => {
        const selected = tab === activeTab;
        tab.setAttribute("aria-selected", String(selected));
        tab.tabIndex = selected ? 0 : -1;
      });
      panels.forEach((panel) => {
        panel.hidden = panel.dataset.toolkitPanel !== category;
      });
      if (focus) activeTab.focus({ preventScroll: true });
    };

    panels.forEach((panel) => {
      const tab = tabs.find((item) => item.dataset.toolkitTab === panel.dataset.toolkitPanel);
      panel.setAttribute("role", "tabpanel");
      panel.setAttribute("aria-labelledby", tab.id);
      panel.tabIndex = 0;
    });
    tabs.forEach((tab, index) => {
      tab.addEventListener("click", () => activate(index));
      tab.addEventListener("keydown", (event) => {
        let next = index;
        if (event.key === "ArrowRight") next = (index + 1) % tabs.length;
        else if (event.key === "ArrowLeft") next = (index + tabs.length - 1) % tabs.length;
        else if (event.key === "Home") next = 0;
        else if (event.key === "End") next = tabs.length - 1;
        else return;
        event.preventDefault();
        activate(next, true);
      });
    });
    activate(0);
    section.classList.add("is-tabbed");
  });
})();
