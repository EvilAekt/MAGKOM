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

  // 1. Slide-Over Drawer Management (Jejak Audit & Inspeksi)
  function getBackdrop() {
    var backdrop = document.querySelector('.cv-drawer-backdrop');
    if (!backdrop) {
      backdrop = document.createElement('div');
      backdrop.className = 'cv-drawer-backdrop';
      backdrop.setAttribute('data-action', 'close-drawer');
      document.body.appendChild(backdrop);
    }
    return backdrop;
  }

  function openDrawer(drawerSelector, triggerEl) {
    var drawer = document.querySelector(drawerSelector);
    if (!drawer) return;

    var backdrop = getBackdrop();

    if (triggerEl) {
      var mappings = [
        { attr: 'data-user', target: '.cv-dyn-user' },
        { attr: 'data-ip', target: '.cv-dyn-ip' },
        { attr: 'data-action-name', target: '.cv-dyn-action' },
        { attr: 'data-waktu', target: '.cv-dyn-waktu' }
      ];

      mappings.forEach(function (m) {
        var val = triggerEl.getAttribute(m.attr);
        if (val !== null) {
          var el = drawer.querySelector(m.target);
          if (el) el.textContent = val;
        }
      });
    }

    backdrop.classList.add('active');
    drawer.classList.add('active');
    document.body.style.overflow = 'hidden';
  }

  function closeAllDrawers() {
    document.querySelectorAll('.cv-slide-drawer.active').forEach(function (d) {
      d.classList.remove('active');
    });
    var backdrop = document.querySelector('.cv-drawer-backdrop');
    if (backdrop) {
      backdrop.classList.remove('active');
    }
    document.body.style.overflow = '';
  }

  // 2. Client-Side SVG Trend Chart Curve Builder
  function buildCurvePath(points) {
    if (!points || !points.length) return '';
    var path = 'M ' + points[0].x + ' ' + points[0].y;
    for (var i = 0; i < points.length - 1; i++) {
      var p0 = points[i];
      var p1 = points[i + 1];
      var cx1 = p0.x + (p1.x - p0.x) / 2;
      var cy1 = p0.y;
      var cx2 = cx1;
      var cy2 = p1.y;
      path += ' C ' + cx1 + ' ' + cy1 + ', ' + cx2 + ' ' + cy2 + ', ' + p1.x + ' ' + p1.y;
    }
    return path;
  }

  function updateTrendChart(period) {
    var wrapper = document.querySelector('.gt-chart-wrapper');
    if (!wrapper) return;

    var rawData = wrapper.getAttribute('data-trend-datasets');
    if (!rawData) return;

    var datasets;
    try {
      datasets = JSON.parse(rawData);
    } catch (e) {
      return;
    }

    var list = datasets[period];
    if (!list || !list.length) return;

    var total = 0;
    var maxVal = 1;
    list.forEach(function (item) {
      total += item.count;
      if (item.count > maxVal) maxVal = item.count;
    });

    var countPoints = list.length;
    var stepX = countPoints > 1 ? (740 / (countPoints - 1)) : 740;
    var primaryPoints = [];
    var secondaryPoints = [];

    list.forEach(function (item, idx) {
      var x = 35 + (idx * stepX);
      var ratio = maxVal > 0 ? (item.count / maxVal) : 0;
      var y = 150 - (ratio * 120);
      primaryPoints.push({ x: x, y: y, count: item.count, label: item.bulan });

      var secRatio = ratio * 0.72;
      var secY = 150 - (secRatio * 120);
      secondaryPoints.push({ x: x, y: secY });
    });

    var primaryPath = buildCurvePath(primaryPoints);
    var secondaryPath = buildCurvePath(secondaryPoints);
    var lastPt = primaryPoints[primaryPoints.length - 1];
    var areaPath = primaryPath + ' L ' + lastPt.x + ' 155 L ' + primaryPoints[0].x + ' 155 Z';

    // Update SVG Elements
    var areaEl = wrapper.querySelector('path[fill*="gtTealGradient"]');
    if (areaEl) areaEl.setAttribute('d', areaPath);

    var secCurveEl = wrapper.querySelector('.gt-curve-secondary');
    if (secCurveEl) secCurveEl.setAttribute('d', secondaryPath);

    var primCurveEl = wrapper.querySelector('.gt-curve-primary');
    if (primCurveEl) primCurveEl.setAttribute('d', primaryPath);

    // Update Hero Number
    var heroNum = document.querySelector('.gt-hero-stat__number');
    if (heroNum) heroNum.textContent = total.toLocaleString();

    // Update Axis & Points
    var svg = wrapper.querySelector('.gt-chart-svg');
    if (svg) {
      // Remove old dynamic points & text labels
      svg.querySelectorAll('.gt-dyn-point, .gt-dyn-label').forEach(function (el) {
        el.remove();
      });

      primaryPoints.forEach(function (pt) {
        var circle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
        circle.setAttribute('cx', pt.x);
        circle.setAttribute('cy', pt.y);
        circle.setAttribute('r', '3.5');
        circle.setAttribute('class', 'gt-chart-point gt-dyn-point');
        circle.setAttribute('stroke-width', '2');
        svg.appendChild(circle);

        var text = document.createElementNS('http://www.w3.org/2000/svg', 'text');
        text.setAttribute('class', 'gt-chart-axis-text gt-dyn-label');
        text.setAttribute('x', pt.x);
        text.setAttribute('y', '172');
        text.setAttribute('text-anchor', 'middle');
        text.textContent = pt.label;
        svg.appendChild(text);
      });
    }
  }

  // 3. Document Ready Initialization
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

    // Global Drawer Trigger Listener
    document.addEventListener('click', function (e) {
      var trigger = e.target.closest('[data-action]');
      if (!trigger) return;

      var action = trigger.getAttribute('data-action');
      if (action === 'open-drawer') {
        e.preventDefault();
        var target = trigger.getAttribute('data-drawer');
        if (target) openDrawer(target, trigger);
      } else if (action === 'close-drawer') {
        e.preventDefault();
        closeAllDrawers();
      }
    });

    // Escape Key to Close Drawers
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' || e.keyCode === 27) {
        closeAllDrawers();
      }
    });

    // Time filter pills interactivity (Gentelella Network Activities)
    var pillButtons = document.querySelectorAll('.gt-pill-btn');
    pillButtons.forEach(function (btn) {
      btn.addEventListener('click', function () {
        pillButtons.forEach(function (b) { b.classList.remove('active'); });
        this.classList.add('active');
        var period = this.getAttribute('data-period');
        updateTrendChart(period);
        window.dispatchEvent(new CustomEvent('magkom:trend-filter-changed', { detail: { period: period } }));
      });
    });

    select(initial(), false);
  });
})();

