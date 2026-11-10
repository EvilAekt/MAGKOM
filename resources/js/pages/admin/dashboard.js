/**
 * ==============================================================================
 * MagKom Sleman - Dashboard Administrasi Bento Grid & GSAP Motion Engine
 * Resources: resources/js/pages/admin/dashboard.js
 * ==============================================================================
 */

import gsap from 'gsap';

/**
 * Cek apakah animasi diizinkan (WCAG Accessiblity & Initial Load Guard)
 */
function shouldAnimateAdminDashboard() {
  const prefersReducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  if (prefersReducedMotion) return false;

  const currentPath = window.location.pathname;
  let lastPath = null;
  try {
    lastPath = sessionStorage.getItem('magkom_admin_dash_motion_path');
  } catch (e) {
    lastPath = null;
  }

  if (lastPath === currentPath) {
    return false; // Interaksi in-page: jangan mengulang animasi penuh
  }

  try {
    sessionStorage.setItem('magkom_admin_dash_motion_path', currentPath);
  } catch (e) {}

  return true;
}

/**
 * Inisialisasi Kalender Jadwal Pendaftaran Bulanan
 */
export function initAdminDashboardCalendar() {
  const calHeaderTitle = document.getElementById('admCalTitle');
  const calDaysGrid = document.getElementById('admCalGrid');
  const btnPrev = document.getElementById('admCalPrevBtn');
  const btnNext = document.getElementById('admCalNextBtn');

  if (!calHeaderTitle || !calDaysGrid || !btnPrev || !btnNext) {
    return;
  }

  let currentYear = parseInt(calDaysGrid.getAttribute('data-initial-year') || '2026', 10);
  let currentMonth = parseInt(calDaysGrid.getAttribute('data-initial-month') || '11', 10) - 1; // 0-indexed (0=Jan)

  const monthNamesId = [
    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
  ];

  const now = new Date();
  const realTodayYear = now.getFullYear();
  const realTodayMonth = now.getMonth();
  const realTodayDate = now.getDate();

  function renderCalendar() {
    calHeaderTitle.textContent = `${monthNamesId[currentMonth]} ${currentYear}`;

    // Hari pertama bulan (0 = Minggu, 1 = Senin, ..., 6 = Sabtu)
    const firstDayRaw = new Date(currentYear, currentMonth, 1).getDay();
    // Konversi ke Senin sebagai indeks 0 (Senin = 0, ..., Minggu = 6)
    const firstDayIndex = (firstDayRaw + 6) % 7;

    const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
    const daysInPrevMonth = new Date(currentYear, currentMonth, 0).getDate();

    let gridHtml = '';

    // 1. Hari-hari bulan sebelumnya (Dimmed)
    for (let i = firstDayIndex - 1; i >= 0; i--) {
      const prevDate = daysInPrevMonth - i;
      gridHtml += `<div class="adm-cal-cell day-dimmed">${prevDate}</div>`;
    }

    // 2. Hari-hari bulan berjalan
    for (let date = 1; date <= daysInMonth; date++) {
      const dayOfWeekRaw = new Date(currentYear, currentMonth, date).getDay();
      const isSunday = (dayOfWeekRaw === 0);
      const isToday = (currentYear === realTodayYear && currentMonth === realTodayMonth && date === realTodayDate);

      let classes = 'adm-cal-cell day-normal';
      if (isSunday) classes += ' day-sunday';
      if (isToday) classes += ' day-today';

      gridHtml += `<div class="${classes}">${date}</div>`;
    }

    // 3. Hari-hari bulan berikutnya untuk melengkapi baris terakhir kelipatan 7
    const totalCellsSoFar = firstDayIndex + daysInMonth;
    const remainingCells = (7 - (totalCellsSoFar % 7)) % 7;
    for (let nextDate = 1; nextDate <= remainingCells; nextDate++) {
      gridHtml += `<div class="adm-cal-cell day-dimmed">${nextDate}</div>`;
    }

    calDaysGrid.innerHTML = gridHtml;
  }

  btnPrev.addEventListener('click', () => {
    currentMonth--;
    if (currentMonth < 0) {
      currentMonth = 11;
      currentYear--;
    }
    renderCalendar();
  });

  btnNext.addEventListener('click', () => {
    currentMonth++;
    if (currentMonth > 11) {
      currentMonth = 0;
      currentYear++;
    }
    renderCalendar();
  });

  renderCalendar();
}

/**
 * Inisialisasi Animasi Berkelas GSAP Motion (Mirip Dashboard Pembimbing)
 */
export function initAdminDashboardMotion() {
  if (!shouldAnimateAdminDashboard()) return;

  const masterTl = gsap.timeline({
    defaults: { ease: 'power2.out' }
  });

  // 1. Fade & Slide-Up Halus pada Seluruh Kartu Bento
  const cards = document.querySelectorAll(
    '.adm-kpi-card, .adm-chart-card, .adm-gauge-card, .adm-highlight-card, .adm-calendar-card, .adm-alert-card'
  );
  if (cards.length) {
    masterTl.fromTo(cards,
      { opacity: 0, y: 14 },
      { opacity: 1, y: 0, duration: 0.45, stagger: 0.05 },
      0
    );
  }

  // 2. Smooth Rolling Counter pada Seluruh Angka Bernilai
  const countElements = document.querySelectorAll('.adm-kpi-number, .alert-big-number, .roster-big-val');
  countElements.forEach((el) => {
    const rawText = el.textContent.trim().replace(',', '.');
    const rawVal = parseFloat(rawText);
    const isDecimal = rawText.includes('.');

    if (!isNaN(rawVal) && rawVal > 0) {
      const counterObj = { val: 0 };
      el.textContent = isDecimal ? '0.0' : '0';

      masterTl.to(counterObj, {
        val: rawVal,
        duration: 0.85,
        ease: 'power2.out',
        onUpdate: () => {
          el.textContent = isDecimal
            ? counterObj.val.toFixed(1).replace('.', ',')
            : Math.round(counterObj.val).toString();
        }
      }, 0.1);
    }
  });

  // 3. Semi-Circle SVG Radial Gauge Arc Sweep
  const gaugeFill = document.querySelector('.adm-gauge-svg .gauge-fill');
  const gaugePercentEl = document.querySelector('.gauge-center-percent');
  if (gaugeFill) {
    const targetOffsetAttr = gaugeFill.getAttribute('stroke-dashoffset');
    const targetOffset = targetOffsetAttr !== null ? parseFloat(targetOffsetAttr) : 0;
    const gaugePerimeter = 188.5; // PI * 60

    masterTl.fromTo(gaugeFill,
      { strokeDashoffset: gaugePerimeter },
      { strokeDashoffset: targetOffset, duration: 1.05, ease: 'power2.out' },
      0.15
    );

    if (gaugePercentEl) {
      const rawText = gaugePercentEl.textContent.replace('%', '').trim();
      const targetPercent = parseInt(rawText, 10);
      if (!isNaN(targetPercent)) {
        const gaugeCounter = { val: 0 };
        gaugePercentEl.textContent = '0%';

        masterTl.to(gaugeCounter, {
          val: targetPercent,
          duration: 1.05,
          ease: 'power2.out',
          onUpdate: () => {
            gaugePercentEl.textContent = `${Math.round(gaugeCounter.val)}%`;
          }
        }, 0.15);
      }
    }
  }

  // 4. Pertumbuhan Vertikal Batang Grafik Kuota (Bar Rise Up)
  const stackedBars = document.querySelectorAll('.adm-bars-container .adm-stacked-bar');
  if (stackedBars.length) {
    masterTl.fromTo(stackedBars,
      { scaleY: 0, opacity: 0.3 },
      { scaleY: 1, opacity: 1, duration: 0.7, stagger: 0.04, transformOrigin: 'bottom center' },
      0.2
    );
  }

  // 5. Segmented Dashes Wave Entrance (Roster Banner & Alert Cards)
  const filledDashes = document.querySelectorAll(
    '.roster-dash.dash-filled-navy, .roster-dash.dash-filled-white, .matrix-dot.dot-filled, .matrix-dot.dot-filled-white, .matrix-dot.dot-filled-dark'
  );
  if (filledDashes.length) {
    masterTl.fromTo(filledDashes,
      { scale: 0.6, opacity: 0.2 },
      { scale: 1, opacity: 1, duration: 0.3, stagger: 0.015 },
      0.3
    );
  }

  return masterTl;
}

// Inisialisasi saat DOM siap
if (typeof document !== 'undefined') {
  document.addEventListener('DOMContentLoaded', () => {
    initAdminDashboardCalendar();
    initAdminDashboardMotion();
  });
}
