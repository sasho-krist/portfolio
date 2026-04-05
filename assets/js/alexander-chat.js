(function () {
  "use strict";

  var root = document.getElementById("alex-chat");
  if (!root) return;

  var cfgEl = document.getElementById("alex-chat-config");
  var cfg = {};
  if (cfgEl && cfgEl.textContent) {
    try {
      cfg = JSON.parse(cfgEl.textContent);
    } catch (e) {
      cfg = {};
    }
  }

  var api = typeof cfg.api === "string" ? cfg.api : "api/chat.php";
  var csrf = typeof cfg.csrf === "string" ? cfg.csrf : "";
  var lang = cfg.lang === "en" ? "en" : "bg";
  var t = cfg.strings || {};

  var fab = root.querySelector(".alex-chat-fab");
  var panel = root.querySelector(".alex-chat-panel");
  var closeBtn = root.querySelector(".alex-chat-close");
  var form = root.querySelector(".alex-chat-form");
  var input = root.querySelector(".alex-chat-input");
  var messagesEl = root.querySelector(".alex-chat-messages");
  var sendBtn = root.querySelector(".alex-chat-send");

  function esc(s) {
    var d = document.createElement("div");
    d.textContent = s;
    return d.innerHTML;
  }

  function appendBubble(role, text) {
    var wrap = document.createElement("div");
    wrap.className = "alex-chat-bubble alex-chat-bubble--" + role;
    wrap.innerHTML = "<p>" + esc(text).replace(/\n/g, "<br />") + "</p>";
    messagesEl.appendChild(wrap);
    messagesEl.scrollTop = messagesEl.scrollHeight;
  }

  function setOpen(open) {
    if (!panel || !fab) return;
    panel.hidden = !open;
    fab.setAttribute("aria-expanded", open ? "true" : "false");
    root.classList.toggle("alex-chat--open", open);
    if (open && input) {
      input.focus();
    }
  }

  function errMsg(code) {
    var map = {
      csrf: t.error_csrf,
      empty: t.error_empty,
      long: t.error_long,
      rate: t.error_rate,
      config: t.error_config,
      network: t.error_network,
      ssl: t.error_ssl,
      auth: t.auth,
      forbidden: t.forbidden,
      openai_rate: t.openai_rate,
      quota: t.quota,
      model: t.model,
      api: t.error_api,
    };
    return map[code] || t.error_generic || "Error.";
  }

  if (fab) {
    fab.addEventListener("click", function () {
      var isHidden = panel && panel.hidden;
      setOpen(!!isHidden);
    });
  }
  if (closeBtn) {
    closeBtn.addEventListener("click", function () {
      setOpen(false);
    });
  }
  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape" && panel && !panel.hidden) {
      setOpen(false);
    }
  });

  var suggestionsWrap = root.querySelector(".alex-chat-suggestions-wrap");
  var suggestionsToggle = root.querySelector(".alex-chat-suggestions-toggle");

  if (suggestionsWrap && suggestionsToggle) {
    suggestionsToggle.addEventListener("click", function () {
      var collapsed = suggestionsWrap.classList.toggle(
        "alex-chat-suggestions-wrap--collapsed"
      );
      suggestionsToggle.setAttribute("aria-expanded", collapsed ? "false" : "true");
      var show = suggestionsToggle.getAttribute("data-label-show") || "";
      var hide = suggestionsToggle.getAttribute("data-label-hide") || "";
      suggestionsToggle.textContent = collapsed ? show : hide;
    });
  }

  function sendMessage(msg) {
    msg = (msg || "").trim();
    if (!msg) return;
    appendBubble("user", msg);
    if (input) input.value = "";
    if (sendBtn) sendBtn.disabled = true;
    appendBubble("assistant", t.loading || "…");

    fetch(api, {
      method: "POST",
      headers: { "Content-Type": "application/json", Accept: "application/json" },
      body: JSON.stringify({ message: msg, csrf: csrf, lang: lang }),
    })
      .then(function (res) {
        return res.json().then(function (data) {
          return { ok: res.ok, status: res.status, data: data };
        });
      })
      .then(function (r) {
        var last = messagesEl.lastElementChild;
        if (last && last.classList.contains("alex-chat-bubble--assistant")) {
          last.remove();
        }
        if (r.ok && r.data && r.data.ok && r.data.reply) {
          appendBubble("assistant", r.data.reply);
        } else {
          var code =
            r.data && r.data.error ? String(r.data.error) : "generic";
          var errText = errMsg(code);
          if (
            r.data &&
            r.data.openai_hint &&
            code !== "quota"
          ) {
            errText += "\n\n" + String(r.data.openai_hint);
          }
          if (r.data && r.data.retry_after && t.retry_after_hint) {
            errText +=
              "\n\n" +
              String(t.retry_after_hint).replace("%d", String(r.data.retry_after));
          }
          appendBubble("assistant", errText);
        }
      })
      .catch(function () {
        var last = messagesEl.lastElementChild;
        if (last && last.classList.contains("alex-chat-bubble--assistant")) {
          last.remove();
        }
        appendBubble("assistant", errMsg("network"));
      })
      .finally(function () {
        if (sendBtn) sendBtn.disabled = false;
      });
  }

  root.addEventListener("click", function (e) {
    var chip = e.target && e.target.closest
      ? e.target.closest(".alex-chat-suggestion")
      : null;
    if (!chip || !root.contains(chip)) return;
    e.preventDefault();
    sendMessage(chip.textContent);
  });

  if (form && input) {
    form.addEventListener("submit", function (e) {
      e.preventDefault();
      sendMessage(input.value);
    });
  }
})();
