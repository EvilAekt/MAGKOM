(function () {
  'use strict';
  var TABS = ['ringkasan', 'seleksi', 'bimbingan', 'sistem'];
  var KEY = 'magkom_superadmin_active_tab';

  function applyDataV() {
    document.querySelectorAll('[data-v]').forEach(function (el) {
      el.style.setProperty('--v', el.getAttribute('data-v'));
    });
  }

  function select(name, focus) {
    TABS.forEach(function (t) {
      var btn = document.getElementById('btn-tab-' + t);
      var panel = document.getElementById('tab-' + t);
      if (!btn || !panel) return;
      var on = t === name;
      btn.setAttribute('aria-selected', on ? 'true' : 'false');
      btn.tabIndex = on ? 0 : -1;
      panel.hidden = !on;
      if (on && focus) btn.focus();
    });
    try { localStorage.setItem(KEY, name); } catch (e) {}
    history.replaceState(null, '', name === 'ringkasan' ? location.pathname + location.search : '#' + name);
  }

  function initial() {
    var h = location.hash.replace('#', '');
    if (TABS.indexOf(h) > -1) return h;
    try { var s = localStorage.getItem(KEY); if (TABS.indexOf(s) > -1) return s; } catch (e) {}
    return 'ringkasan';
  }

  // Backward compatibility for existing callers or external buttons
  window.switchDashboardTab = function (tabId) {
    var cleanId = tabId.replace('tab-', '');
    select(cleanId, false);
  };

  document.addEventListener('DOMContentLoaded', function () {
    applyDataV();

    var list = document.querySelector('[role="tablist"]');
    if (list) {
      list.addEventListener('click', function (e) {
        var b = e.target.closest('[role="tab"]');
        if (b) select(b.id.replace('btn-tab-', ''), false);
      });
      list.addEventListener('keydown', function (e) {
        var activeEl = document.activeElement;
        if (!activeEl || !activeEl.id) return;
        var i = TABS.indexOf(activeEl.id.replace('btn-tab-', ''));
        if (i < 0) return;
        var next = {
          ArrowRight: (i + 1) % TABS.length,
          ArrowLeft: (i + TABS.length - 1) % TABS.length,
          Home: 0,
          End: TABS.length - 1
        }[e.key];
        if (next === undefined) return;
        e.preventDefault();
        select(TABS[next], true);
      });
    }

    document.querySelectorAll('select[data-auto-submit]').forEach(function (s) {
      s.addEventListener('change', function () { s.form.submit(); });
    });

    // Time filter pills interactivity (Gentelella Network Activities)
    var pillButtons = document.querySelectorAll('.gt-pill-btn');
    pillButtons.forEach(function (btn) {
      btn.addEventListener('click', function () {
        pillButtons.forEach(function (b) { b.classList.remove('active'); });
        this.classList.add('active');
        var period = this.getAttribute('data-period');
        window.dispatchEvent(new CustomEvent('magkom:trend-filter-changed', { detail: { period: period } }));
      });
    });

    select(initial(), false);
  });
})();

