(function () {
  const THEME_KEY = "portfolio-theme";
  const VIEW_KEY = "portfolio-projects-view";

  function getPref(key, fallback) {
    try {
      return localStorage.getItem(key) || fallback;
    } catch {
      return fallback;
    }
  }

  function setPref(key, value) {
    try {
      localStorage.setItem(key, value);
    } catch {
      /* ignore */
    }
  }

  function initTheme() {
    const root = document.documentElement;
    let stored = null;
    try {
      stored = localStorage.getItem(THEME_KEY);
    } catch {
      stored = null;
    }
    const prefersLight =
      window.matchMedia && window.matchMedia("(prefers-color-scheme: light)").matches;
    const initial =
      stored === "light" || stored === "dark" ? stored : prefersLight ? "light" : "dark";
    root.setAttribute("data-theme", initial);
    updateThemeButtons(initial);

    document.querySelectorAll("[data-theme-toggle]").forEach((btn) => {
      btn.addEventListener("click", () => {
        const next = root.getAttribute("data-theme") === "dark" ? "light" : "dark";
        root.setAttribute("data-theme", next);
        setPref(THEME_KEY, next);
        updateThemeButtons(next);
      });
    });
  }

  function updateThemeButtons(theme) {
    document.querySelectorAll("[data-theme-toggle]").forEach((btn) => {
      btn.setAttribute("aria-label", theme === "dark" ? "Светла тема" : "Тъмна тема");
      btn.textContent = theme === "dark" ? "☀" : "☾";
    });
  }

  function initProjectsView() {
    const body = document.body;
    const stored = getPref(VIEW_KEY, "cards");
    const mode = stored === "table" ? "table" : "cards";
    body.setAttribute("data-projects-view", mode);
    syncViewButtons(mode);

    // Само бутоните в тулбара — не <body data-projects-view>, иначе слушателите се дублират и toggle чупи.
    document.querySelectorAll("button[data-projects-view]").forEach((btn) => {
      btn.addEventListener("click", () => {
        const v = btn.getAttribute("data-projects-view");
        if (v !== "cards" && v !== "table") return;
        body.setAttribute("data-projects-view", v);
        setPref(VIEW_KEY, v);
        syncViewButtons(v);
      });
    });
  }

  function syncViewButtons(mode) {
    document.querySelectorAll("button[data-projects-view]").forEach((btn) => {
      const v = btn.getAttribute("data-projects-view");
      btn.setAttribute("aria-pressed", v === mode ? "true" : "false");
    });
  }

  function initSmoothNav() {
    document.querySelectorAll('a[href^="#"]').forEach((a) => {
      a.addEventListener("click", (e) => {
        const id = a.getAttribute("href");
        if (!id || id === "#") return;
        const el = document.querySelector(id);
        if (el) {
          e.preventDefault();
          el.scrollIntoView({ behavior: "smooth", block: "start" });
        }
      });
    });
  }

  function initYear() {
    const y = document.getElementById("year");
    if (y) y.textContent = String(new Date().getFullYear());
  }

  function initLightbox() {
    const lb = document.getElementById("lightbox");
    if (!lb) return;
    const img = lb.querySelector(".lightbox-img");
    const cap = lb.querySelector(".lightbox-caption");
    const closeBtn = lb.querySelector(".lightbox-close");
    const prevBtn = lb.querySelector(".lightbox-prev");
    const nextBtn = lb.querySelector(".lightbox-next");
    /** @type {HTMLElement[]} */
    let activeItems = [];
    let index = 0;

    function groupItems(el) {
      const g = el.getAttribute("data-lightbox-group") || "default";
      return Array.from(document.querySelectorAll('[data-lightbox-group="' + g + '"]')).sort(
        (a, b) =>
          Number(a.getAttribute("data-lightbox-index") || 0) -
          Number(b.getAttribute("data-lightbox-index") || 0)
      );
    }

    function srcAt(list, i) {
      const el = list[i];
      return el
        ? {
            src: el.getAttribute("data-full") || el.querySelector("img")?.src,
            text: el.getAttribute("data-caption") || "",
          }
        : null;
    }

    function show(list, i) {
      if (!list.length) return;
      activeItems = list;
      index = (i + list.length) % list.length;
      const data = srcAt(list, index);
      if (!data || !data.src) return;
      img.src = data.src;
      img.alt = data.text;
      cap.textContent = data.text;
      lb.classList.add("is-open");
      lb.setAttribute("aria-hidden", "false");
      document.body.style.overflow = "hidden";
    }

    function hide() {
      lb.classList.remove("is-open");
      lb.setAttribute("aria-hidden", "true");
      document.body.style.overflow = "";
      img.removeAttribute("src");
      activeItems = [];
    }

    function step(delta) {
      if (!activeItems.length) return;
      show(activeItems, index + delta);
    }

    document.querySelectorAll("[data-lightbox-group]").forEach((el) => {
      el.addEventListener("click", () => {
        const list = groupItems(el);
        const i = list.indexOf(el);
        show(list, i >= 0 ? i : 0);
      });
      el.addEventListener("keydown", (e) => {
        if (e.key === "Enter" || e.key === " ") {
          e.preventDefault();
          const list = groupItems(el);
          const i = list.indexOf(el);
          show(list, i >= 0 ? i : 0);
        }
      });
    });

    closeBtn?.addEventListener("click", hide);
    lb.addEventListener("click", (e) => {
      if (e.target === lb) hide();
    });
    prevBtn?.addEventListener("click", () => step(-1));
    nextBtn?.addEventListener("click", () => step(1));

    document.addEventListener("keydown", (e) => {
      if (!lb.classList.contains("is-open")) return;
      if (e.key === "Escape") hide();
      if (e.key === "ArrowLeft") step(-1);
      if (e.key === "ArrowRight") step(1);
    });
  }

  document.addEventListener("DOMContentLoaded", () => {
    initTheme();
    initProjectsView();
    initSmoothNav();
    initYear();
    initLightbox();
  });
})();
