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

  // 3. Dynamic SuperAdmin Calendar Widget (Universal Dynamic Calendar Engine)
  function initSuperAdminCalendar() {
    var grid = document.getElementById('saCalendarGrid');
    var monthTitle = document.getElementById('saCalMonthTitle');
    var monthTriggerBtn = document.getElementById('saCalMonthTriggerBtn');
    var monthPopover = document.getElementById('saCalMonthPopover');
    var monthGrid = document.getElementById('saCalMonthGrid');
    var yearVal = document.getElementById('saCalYearVal');
    var yearTriggerBtn = document.getElementById('saCalYearTriggerBtn');
    var yearPopover = document.getElementById('saCalYearPopover');
    var yearGrid = document.getElementById('saCalYearGrid');
    var yearDecBtn = document.getElementById('saCalYearDecBtn');
    var yearIncBtn = document.getElementById('saCalYearIncBtn');
    var prevBtn = document.getElementById('saCalPrevBtn');
    var nextBtn = document.getElementById('saCalNextBtn');
    if (!grid || !monthTitle) return;

    var MONTH_NAMES = [
      'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
      'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];

    var MONTH_SHORT_NAMES = [
      'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
      'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'
    ];

    var realToday = new Date();
    var realYear = realToday.getFullYear();
    var realMonth = realToday.getMonth();
    var realDate = realToday.getDate();

    var viewYear = realYear;
    var viewMonth = realMonth;
    var popoverBaseYear = viewYear;
    var selectedDate = null;

    function renderGrid() {
      grid.innerHTML = '';
      var isCurrentMonthView = (viewYear === realYear && viewMonth === realMonth);

      var firstDay = new Date(viewYear, viewMonth, 1).getDay();
      var startOffset = (firstDay === 0) ? 6 : (firstDay - 1);

      var daysInCurrentMonth = new Date(viewYear, viewMonth + 1, 0).getDate();
      var daysInPrevMonth = new Date(viewYear, viewMonth, 0).getDate();

      var frag = document.createDocumentFragment();

      // 1. Previous month trailing days
      for (var p = startOffset; p > 0; p--) {
        var prevSpan = document.createElement('span');
        prevSpan.className = 'sa-cal-date date-muted';
        prevSpan.tabIndex = -1;
        prevSpan.textContent = daysInPrevMonth - p + 1;
        frag.appendChild(prevSpan);
      }

      // 2. Current month active days
      for (var d = 1; d <= daysInCurrentMonth; d++) {
        var dayCol = (startOffset + d - 1) % 7;
        var isWeekend = (dayCol === 5 || dayCol === 6);
        var isToday = (isCurrentMonthView && d === realDate);
        var isSelected = selectedDate && (selectedDate.year === viewYear && selectedDate.month === viewMonth && selectedDate.date === d);

        var btn = document.createElement('button');
        btn.type = 'button';

        var classes = ['sa-cal-date'];
        if (isWeekend) classes.push('date-weekend');
        if (isToday) classes.push('date-today');
        if (isSelected) classes.push('date-selected');

        btn.className = classes.join(' ');
        btn.setAttribute('data-date', d);
        btn.setAttribute('aria-label', 'Tanggal ' + d + ' ' + MONTH_NAMES[viewMonth] + ' ' + viewYear + (isToday ? ' (Hari ini)' : ''));
        btn.textContent = d;

        btn.addEventListener('click', (function (dayNum) {
          return function () {
            var isAlreadySelected = selectedDate &&
              selectedDate.year === viewYear &&
              selectedDate.month === viewMonth &&
              selectedDate.date === dayNum;

            if (isAlreadySelected) {
              selectedDate = null;
              this.classList.remove('date-selected', 'sa-date-pop');
            } else {
              selectedDate = { year: viewYear, month: viewMonth, date: dayNum };
              grid.querySelectorAll('.sa-cal-date').forEach(function (el) {
                el.classList.remove('date-selected', 'sa-date-pop');
              });
              this.classList.add('date-selected', 'sa-date-pop');
            }
          };
        })(d));

        btn.addEventListener('dblclick', function (e) {
          e.preventDefault();
          selectedDate = null;
          grid.querySelectorAll('.sa-cal-date').forEach(function (el) {
            el.classList.remove('date-selected', 'sa-date-pop');
          });
        });

        frag.appendChild(btn);
      }

      // 3. Next month leading days
      var totalCells = startOffset + daysInCurrentMonth;
      var remaining = (7 - (totalCells % 7)) % 7;
      for (var n = 1; n <= remaining; n++) {
        var nextSpan = document.createElement('span');
        nextSpan.className = 'sa-cal-date date-muted';
        nextSpan.tabIndex = -1;
        nextSpan.textContent = n;
        frag.appendChild(nextSpan);
      }

      grid.appendChild(frag);
    }

    function renderYearPopover() {
      if (!yearGrid) return;
      yearGrid.innerHTML = '';
      var startYear = popoverBaseYear - 2;
      var endYear = popoverBaseYear + 3;

      for (var y = startYear; y <= endYear; y++) {
        var yBtn = document.createElement('button');
        yBtn.type = 'button';
        yBtn.className = 'sa-cal-year-option-btn' + (y === viewYear ? ' active' : '');
        yBtn.textContent = y;
        yBtn.addEventListener('click', (function (targetYear) {
          return function (e) {
            e.stopPropagation();
            viewYear = targetYear;
            closeYearPopover();
            updateCalendar();
          };
        })(y));
        yearGrid.appendChild(yBtn);
      }
    }

    function closeYearPopover() {
      if (yearPopover) {
        yearPopover.hidden = true;
      }
      if (yearTriggerBtn) {
        yearTriggerBtn.setAttribute('aria-expanded', 'false');
      }
    }

    function renderMonthPopover() {
      if (!monthGrid) return;
      monthGrid.innerHTML = '';

      for (var m = 0; m < 12; m++) {
        var mBtn = document.createElement('button');
        mBtn.type = 'button';
        mBtn.className = 'sa-cal-month-option-btn' + (m === viewMonth ? ' active' : '');
        mBtn.textContent = MONTH_SHORT_NAMES[m];
        mBtn.setAttribute('data-month', m);
        mBtn.setAttribute('title', MONTH_NAMES[m]);
        mBtn.setAttribute('aria-label', 'Pilih Bulan ' + MONTH_NAMES[m]);
        mBtn.addEventListener('click', (function (targetMonth) {
          return function (e) {
            e.stopPropagation();
            viewMonth = targetMonth;
            closeMonthPopover();
            updateCalendar();
          };
        })(m));
        monthGrid.appendChild(mBtn);
      }
    }

    function closeMonthPopover() {
      if (monthPopover) {
        monthPopover.hidden = true;
      }
      if (monthTriggerBtn) {
        monthTriggerBtn.setAttribute('aria-expanded', 'false');
      }
    }

    function toggleMonthPopover() {
      if (!monthPopover) return;
      var isHidden = monthPopover.hidden;
      if (isHidden) {
        closeYearPopover();
        renderMonthPopover();
        monthPopover.hidden = false;
        if (monthTriggerBtn) monthTriggerBtn.setAttribute('aria-expanded', 'true');
      } else {
        closeMonthPopover();
      }
    }

    function toggleYearPopover() {
      if (!yearPopover) return;
      var isHidden = yearPopover.hidden;
      if (isHidden) {
        closeMonthPopover();
        popoverBaseYear = viewYear;
        renderYearPopover();
        yearPopover.hidden = false;
        if (yearTriggerBtn) yearTriggerBtn.setAttribute('aria-expanded', 'true');
      } else {
        closeYearPopover();
      }
    }

    function updateCalendar() {
      monthTitle.textContent = MONTH_NAMES[viewMonth];
      if (yearVal) yearVal.textContent = viewYear;
      renderGrid();
    }

    if (prevBtn) {
      prevBtn.addEventListener('click', function () {
        viewMonth--;
        if (viewMonth < 0) {
          viewMonth = 11;
          viewYear--;
        }
        updateCalendar();
      });
    }

    if (nextBtn) {
      nextBtn.addEventListener('click', function () {
        viewMonth++;
        if (viewMonth > 11) {
          viewMonth = 0;
          viewYear++;
        }
        updateCalendar();
      });
    }

    if (monthTriggerBtn) {
      monthTriggerBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        toggleMonthPopover();
      });
    }

    if (yearTriggerBtn) {
      yearTriggerBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        toggleYearPopover();
      });
    }

    if (yearDecBtn) {
      yearDecBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        popoverBaseYear -= 6;
        renderYearPopover();
      });
    }

    if (yearIncBtn) {
      yearIncBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        popoverBaseYear += 6;
        renderYearPopover();
      });
    }

    document.addEventListener('click', function (e) {
      if (monthPopover && !monthPopover.hidden) {
        if (!monthPopover.contains(e.target) && (!monthTriggerBtn || !monthTriggerBtn.contains(e.target))) {
          closeMonthPopover();
        }
      }
      if (yearPopover && !yearPopover.hidden) {
        if (!yearPopover.contains(e.target) && (!yearTriggerBtn || !yearTriggerBtn.contains(e.target))) {
          closeYearPopover();
        }
      }
    });

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' || e.keyCode === 27) {
        closeMonthPopover();
        closeYearPopover();
      }
    });

    updateCalendar();
  }

  // 4. Document Ready Initialization
  document.addEventListener('DOMContentLoaded', function () {
    applyDataV();
    initSuperAdminCalendar();

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

