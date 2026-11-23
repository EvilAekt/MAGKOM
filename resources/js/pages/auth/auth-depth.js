/**
 * Auth Depth Interactions & 3D Camera Network
 * Dinas Komunikasi dan Informatika Kabupaten Sleman - MagKom
 */
import { initPasswordStrengthMeter } from '../../components/password-strength.js';

/**
 * Initializes the 3D canvas network background
 * @param {HTMLCanvasElement} cv
 * @returns {() => void} destroy function
 */
export function startNetwork(cv) {
  if (!cv) return () => {};

  const ctx = cv.getContext('2d');
  const host = cv.parentElement;
  if (!ctx || !host) return () => {};

  const reduce = !!(window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches);
  let W = 1;
  let H = 1;
  let FL = 800;
  let pts = [];
  let packets = [];
  let t = 0;
  let rafId = 0;
  let ro = null;

  const cam = { yaw: 0, pitch: 0, ty: 0, tp: 0 };
  const mouse = { x: -9999, y: -9999, sx: -9999, sy: -9999, pw: 0, nx: 0, ny: 0, active: false };
  const BX = 950;
  const BY = 620;
  const BZ = 950;
  const D = 950;
  const LINK = 340;
  const NEAR = 70;
  const MR = 230;

  const rnd = (a, b) => a + Math.random() * (b - a);

  const build = () => {
    W = Math.max(1, cv.offsetWidth);
    H = Math.max(1, cv.offsetHeight);
    const dpr = Math.min(window.devicePixelRatio || 1, 2);
    cv.width = Math.round(W * dpr);
    cv.height = Math.round(H * dpr);
    ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
    FL = Math.max(W, H) * 0.85;

    const n = 120;
    pts = [];
    for (let i = 0; i < n; i++) {
      pts.push({
        x: rnd(-BX, BX),
        y: rnd(-BY, BY),
        z: rnd(-BZ, BZ),
        vx: rnd(-0.5, 0.5),
        vy: rnd(-0.35, 0.35),
        vz: rnd(-0.5, 0.5),
        gold: Math.random() < 0.1,
        sx: 0,
        sy: 0,
        sz: 0,
        ok: false,
        near: 0,
      });
    }

    packets = [];
    for (let k = 0; k < 10; k++) {
      packets.push({ a: -1, b: -1, p: Math.random() });
    }
  };

  const pickLink = (pk) => {
    for (let tries = 0; tries < 30; tries++) {
      const a = Math.floor(Math.random() * pts.length);
      const A = pts[a];
      const cand = [];
      for (let j = 0; j < pts.length; j++) {
        if (j === a) continue;
        const B = pts[j];
        const dx = A.x - B.x;
        const dy = A.y - B.y;
        const dz = A.z - B.z;
        if (dx * dx + dy * dy + dz * dz < LINK * LINK) {
          cand.push(j);
        }
      }
      if (cand.length) {
        pk.a = a;
        pk.b = cand[Math.floor(Math.random() * cand.length)];
        pk.p = 0;
        return;
      }
    }
    pk.a = -1;
  };

  const frame = () => {
    t += 0.01;
    mouse.pw += ((mouse.active ? 1 : 0) - mouse.pw) * 0.08;
    if (mouse.active) {
      if (mouse.sx < -999) {
        mouse.sx = mouse.x;
        mouse.sy = mouse.y;
      }
      mouse.sx += (mouse.x - mouse.sx) * 0.18;
      mouse.sy += (mouse.y - mouse.sy) * 0.18;
    }
    const tx = mouse.active ? mouse.nx : 0;
    const ty = mouse.active ? mouse.ny : 0;
    cam.ty = tx * 0.42;
    cam.tp = ty * 0.2;
    cam.yaw += (cam.ty - cam.yaw) * 0.05;
    cam.pitch += (cam.tp - cam.pitch) * 0.05;
    const yaw = Math.max(-0.5, Math.min(0.5, cam.yaw + Math.sin(t * 0.35) * 0.05));
    const pitch = Math.max(-0.26, Math.min(0.26, cam.pitch + Math.cos(t * 0.27) * 0.03));
    const cy = Math.cos(yaw);
    const sy = Math.sin(yaw);
    const cp = Math.cos(pitch);
    const sp = Math.sin(pitch);
    const hw = W / 2;
    const hh = H / 2;

    for (let i = 0; i < pts.length; i++) {
      const p = pts[i];
      p.x += p.vx;
      p.y += p.vy;
      p.z += p.vz;
      if (p.x > BX || p.x < -BX) p.vx = -p.vx;
      if (p.y > BY || p.y < -BY) p.vy = -p.vy;
      if (p.z > BZ || p.z < -BZ) p.vz = -p.vz;

      const x1 = p.x * cy - p.z * sy;
      const z1 = p.x * sy + p.z * cy;
      const y2 = p.y * cp - z1 * sp;
      const z2 = p.y * sp + z1 * cp + D;

      if (z2 < NEAR) {
        p.ok = false;
        continue;
      }

      const k = FL / z2;
      let ox = hw + x1 * k;
      let oy = hh + y2 * k;
      p.near = 0;

      if (mouse.pw > 0.01) {
        const dx = mouse.sx - ox;
        const dy = mouse.sy - oy;
        const d = Math.sqrt(dx * dx + dy * dy);
        if (d < MR && d > 1) {
          const w = (1 - d / MR) * mouse.pw;
          const f = w * w * 0.55;
          ox += dx * f;
          oy += dy * f;
          p.near = w;
        }
      }
      p.sx = ox;
      p.sy = oy;
      p.sz = z2;
      p.ok = true;
    }

    ctx.clearRect(0, 0, W, H);
    ctx.lineWidth = 1;

    for (let i = 0; i < pts.length; i++) {
      const a = pts[i];
      if (!a.ok) continue;
      for (let j = i + 1; j < pts.length; j++) {
        const b = pts[j];
        if (!b.ok) continue;
        const dx = a.x - b.x;
        const dy = a.y - b.y;
        const dz = a.z - b.z;
        const d2 = dx * dx + dy * dy + dz * dz;
        if (d2 < LINK * LINK) {
          const d = Math.sqrt(d2);
          const z = (a.sz + b.sz) / 2;
          const fog = Math.max(0.06, Math.min(1, 1.25 - z / 1900));
          ctx.strokeStyle = 'rgba(255,255,255,' + ((1 - d / LINK) * 0.42 * fog).toFixed(3) + ')';
          ctx.beginPath();
          ctx.moveTo(a.sx, a.sy);
          ctx.lineTo(b.sx, b.sy);
          ctx.stroke();
        }
      }
    }

    for (let i = 0; i < packets.length; i++) {
      const pk = packets[i];
      if (pk.a < 0) {
        pickLink(pk);
        continue;
      }
      const A = pts[pk.a];
      const B = pts[pk.b];
      pk.p += 0.016;
      if (pk.p >= 1) {
        pickLink(pk);
        continue;
      }
      if (!A.ok || !B.ok) continue;
      const dx = A.x - B.x;
      const dy = A.y - B.y;
      const dz = A.z - B.z;
      if (dx * dx + dy * dy + dz * dz > LINK * LINK) {
        pickLink(pk);
        continue;
      }
      const x = A.sx + (B.sx - A.sx) * pk.p;
      const y = A.sy + (B.sy - A.sy) * pk.p;
      const z = A.sz + (B.sz - A.sz) * pk.p;
      const r = Math.max(1.5, Math.min(5, 3.2 * 900 / z));
      ctx.fillStyle = 'rgba(255,195,0,0.28)';
      ctx.beginPath();
      ctx.arc(x, y, r * 2.6, 0, 6.283);
      ctx.fill();
      ctx.fillStyle = '#FFC300';
      ctx.beginPath();
      ctx.arc(x, y, r, 0, 6.283);
      ctx.fill();
    }

    if (mouse.pw > 0.02) {
      for (let i = 0; i < pts.length; i++) {
        const p = pts[i];
        if (!p.ok || p.near < 0.15) continue;
        ctx.strokeStyle = 'rgba(255,195,0,' + (p.near * 0.8).toFixed(3) + ')';
        ctx.beginPath();
        ctx.moveTo(mouse.sx, mouse.sy);
        ctx.lineTo(p.sx, p.sy);
        ctx.stroke();
      }
    }

    const order = [];
    for (let i = 0; i < pts.length; i++) {
      if (pts[i].ok) order.push(pts[i]);
    }
    order.sort((u, v) => v.sz - u.sz);

    for (let i = 0; i < order.length; i++) {
      const p = order[i];
      const fog = Math.max(0.12, Math.min(1, 1.3 - p.sz / 1900));
      const r = Math.max(0.8, Math.min(7, 3.4 * 900 / p.sz));
      let near = 0;
      if (mouse.active) {
        const dx = p.sx - mouse.x;
        const dy = p.sy - mouse.y;
        const d = Math.sqrt(dx * dx + dy * dy);
        if (d < 130) near = 1 - d / 130;
      }
      if (p.gold || near > 0.4) {
        ctx.fillStyle = 'rgba(255,195,0,' + fog.toFixed(3) + ')';
      } else {
        ctx.fillStyle = 'rgba(255,255,255,' + (fog * 0.9).toFixed(3) + ')';
      }
      ctx.beginPath();
      ctx.arc(p.sx, p.sy, r * (1 + near * 0.8), 0, 6.283);
      ctx.fill();
    }

    if (mouse.active) {
      const g = ctx.createRadialGradient(mouse.sx, mouse.sy, 0, mouse.sx, mouse.sy, 130);
      g.addColorStop(0, 'rgba(255,195,0,0.2)');
      g.addColorStop(1, 'rgba(255,195,0,0)');
      ctx.fillStyle = g;
      ctx.beginPath();
      ctx.arc(mouse.sx, mouse.sy, 130, 0, 6.283);
      ctx.fill();
      ctx.strokeStyle = 'rgba(255,195,0,0.7)';
      ctx.beginPath();
      ctx.arc(mouse.sx, mouse.sy, 20, 0, 6.283);
      ctx.stroke();
    }
  };

  const onMove = (e) => {
    const r = cv.getBoundingClientRect();
    if (!r.width || !r.height) return;
    mouse.x = (e.clientX - r.left) * W / r.width;
    mouse.y = (e.clientY - r.top) * H / r.height;
    mouse.nx = (mouse.x / W - 0.5) * 2;
    mouse.ny = (mouse.y / H - 0.5) * 2;
    mouse.active = true;
  };

  const onLeave = () => {
    mouse.active = false;
  };

  host.addEventListener('pointermove', onMove);
  host.addEventListener('pointerleave', onLeave);

  build();

  if (typeof ResizeObserver !== 'undefined') {
    ro = new ResizeObserver(() => {
      build();
      if (reduce) frame();
    });
    ro.observe(cv);
  }

  if (reduce) {
    frame();
    return () => {
      if (ro) ro.disconnect();
      host.removeEventListener('pointermove', onMove);
      host.removeEventListener('pointerleave', onLeave);
    };
  }

  const loop = () => {
    frame();
    rafId = requestAnimationFrame(loop);
  };
  loop();

  return () => {
    if (rafId) cancelAnimationFrame(rafId);
    if (ro) ro.disconnect();
    host.removeEventListener('pointermove', onMove);
    host.removeEventListener('pointerleave', onLeave);
  };
}

/**
 * Initializes form interactivity (password toggle, fixed captcha, tabs)
 */
export function initAuthInteractions() {
  // Password Visibility Toggle
  const pwButtons = document.querySelectorAll('[data-action="toggle-password"]');
  pwButtons.forEach((btn) => {
    btn.addEventListener('click', () => {
      const targetId = btn.getAttribute('data-target') || 'sandi';
      const input = document.getElementById(targetId);
      if (!input) return;

      const isPassword = input.type === 'password';
      input.type = isPassword ? 'text' : 'password';

      const showIcon = btn.querySelector('.pw-icon-show');
      const hideIcon = btn.querySelector('.pw-icon-hide');

      if (isPassword) {
        if (showIcon) showIcon.style.display = 'none';
        if (hideIcon) hideIcon.style.display = 'block';
        btn.setAttribute('aria-label', 'Sembunyikan kata sandi');
      } else {
        if (showIcon) showIcon.style.display = 'block';
        if (hideIcon) hideIcon.style.display = 'none';
        btn.setAttribute('aria-label', 'Tampilkan kata sandi');
      }
    });
  });

  // Dynamic Captcha Refresh & Image Reload
  const captchaImages = document.querySelectorAll('[data-captcha-img]');
  const captchaRefreshButtons = document.querySelectorAll('[data-action="refresh-captcha"]');

  const reloadCaptcha = () => {
    const timestamp = Date.now();
    captchaImages.forEach((img) => {
      img.src = '/captcha/image?t=' + timestamp;
    });
  };

  captchaRefreshButtons.forEach((btn) => {
    btn.addEventListener('click', reloadCaptcha);
  });

  captchaImages.forEach((img) => {
    img.addEventListener('click', reloadCaptcha);
  });

  // Register Type Toggle (Individu vs Kelompok)
  const tabInd = document.querySelector('[data-reg-type="ind"]');
  const tabKel = document.querySelector('[data-reg-type="kel"]');
  const hiddenTipe = document.getElementById('tipe_pendaftaran');
  const kelompokSection = document.getElementById('section-kelompok-wrapper');
  const regTypeContainer = tabInd ? tabInd.closest('.reg-type-container') : null;
  const isTypeLocked = regTypeContainer?.getAttribute('data-locked') === 'true' || tabInd?.hasAttribute('disabled') || tabKel?.hasAttribute('disabled');

  // Dynamic Group Roster Controls (PRD F-03A & BR-18)
  const rosterContainer = document.getElementById('roster-anggota-container');
  const btnAddAnggota = document.getElementById('btn-add-anggota');
  const rosterMaxHint = document.getElementById('roster-max-hint');

  const updateRosterState = () => {
    if (!rosterContainer) return;
    const isLockedCount = rosterContainer.getAttribute('data-locked-count') === 'true';
    const maxRekan = parseInt(rosterContainer.getAttribute('data-max-rekan') || '3', 10);
    const minRekan = parseInt(rosterContainer.getAttribute('data-min-rekan') || '1', 10);
    const maxTotal = parseInt(rosterContainer.getAttribute('data-max-total') || '4', 10);

    const cards = Array.from(rosterContainer.querySelectorAll('.card-anggota-item'));
    const visibleCards = cards.filter(c => c.style.display !== 'none');
    const currentCount = visibleCards.length;

    // Update labels, names, and buttons for visible cards
    visibleCards.forEach((card, idx) => {
      card.setAttribute('data-anggota-index', idx);
      const titleSpan = card.querySelector('.card-anggota-title');
      if (titleSpan) {
        titleSpan.textContent = `Rekan Anggota #${idx + 1}`;
      }

      const removeBtn = card.querySelector('[data-action="remove-anggota"]');
      if (removeBtn) {
        if (isLockedCount) {
          removeBtn.style.display = 'none';
        } else {
          // First card cannot be removed if only 1 member remains
          removeBtn.style.display = (idx === 0 && currentCount <= minRekan) ? 'none' : 'inline-flex';
        }
      }

      // Re-index input names
      const nameInp = card.querySelector('input[placeholder*="Nama"]');
      const nimInp = card.querySelector('input[placeholder*="NIM"]');
      const hpInp = card.querySelector('input[placeholder*="WhatsApp"]');
      const emailInp = card.querySelector('input[placeholder*="Email"]');
      const passInp = card.querySelector('input[type="password"]:not([placeholder*="Ulangi"])');
      const passConfInp = card.querySelector('input[placeholder*="Ulangi"]');

      if (nameInp) nameInp.setAttribute('name', `anggota[${idx}][nama]`);
      if (nimInp) nimInp.setAttribute('name', `anggota[${idx}][nim_nisn]`);
      if (hpInp) hpInp.setAttribute('name', `anggota[${idx}][no_hp]`);
      if (emailInp) emailInp.setAttribute('name', `anggota[${idx}][email]`);
      if (passInp) passInp.setAttribute('name', `anggota[${idx}][password]`);
      if (passConfInp) passConfInp.setAttribute('name', `anggota[${idx}][password_confirmation]`);

      card.querySelectorAll('input').forEach(inp => {
        inp.disabled = false;
      });
    });

    // Hidden cards are disabled
    cards.filter(c => c.style.display === 'none').forEach(card => {
      card.querySelectorAll('input').forEach(inp => {
        inp.disabled = true;
      });
    });

    // Update target count attribute
    rosterContainer.setAttribute('data-target-anggota', String(currentCount));

    // Update hidden redirect_jumlah and jumlah_kelompok
    const totalTim = currentCount + 1; // 1 Ketua + N Rekan
    const redirectJumlah = document.querySelector('input[name="redirect_jumlah"]');
    if (redirectJumlah) redirectJumlah.value = String(totalTim);
    const jumlahKelompok = document.querySelector('input[name="jumlah_kelompok"]');
    if (jumlahKelompok) jumlahKelompok.value = String(totalTim);

    // Update add button state and hint
    if (btnAddAnggota) {
      if (isLockedCount) {
        btnAddAnggota.style.display = 'none';
      } else if (currentCount >= maxRekan) {
        btnAddAnggota.disabled = true;
        btnAddAnggota.style.cursor = 'not-allowed';
      } else {
        btnAddAnggota.disabled = false;
        btnAddAnggota.style.cursor = 'pointer';
      }
    }

    if (rosterMaxHint) {
      rosterMaxHint.style.display = (!isLockedCount && currentCount >= maxRekan) ? 'block' : 'none';
    }

    // Trigger live quota update
    window.dispatchEvent(new CustomEvent('roster:changed', { detail: { totalMembers: totalTim } }));
  };

  if (btnAddAnggota && rosterContainer) {
    btnAddAnggota.addEventListener('click', (e) => {
      e.preventDefault();
      const isLockedCount = rosterContainer.getAttribute('data-locked-count') === 'true';
      if (isLockedCount) return;

      const maxRekan = parseInt(rosterContainer.getAttribute('data-max-rekan') || '3', 10);
      const cards = Array.from(rosterContainer.querySelectorAll('.card-anggota-item'));
      const hiddenCard = cards.find(c => c.style.display === 'none');

      if (hiddenCard) {
        hiddenCard.style.display = 'flex';
        hiddenCard.classList.remove('is-entering');
        void hiddenCard.offsetWidth; // Trigger reflow for animation
        hiddenCard.classList.add('is-entering');
        updateRosterState();

        const firstInput = hiddenCard.querySelector('input');
        if (firstInput) {
          firstInput.focus();
        }
      }
    });

    rosterContainer.addEventListener('click', (e) => {
      const removeBtn = e.target.closest('[data-action="remove-anggota"]');
      if (!removeBtn) return;
      e.preventDefault();

      const isLockedCount = rosterContainer.getAttribute('data-locked-count') === 'true';
      if (isLockedCount) return;

      const card = removeBtn.closest('.card-anggota-item');
      if (card) {
        card.querySelectorAll('input').forEach(inp => {
          inp.value = '';
        });
        card.style.display = 'none';
        rosterContainer.appendChild(card);
        updateRosterState();
      }
    });
  }

  if (tabInd && tabKel) {
    const activateInd = () => {
      if (isTypeLocked && tabInd.getAttribute('aria-pressed') !== 'true') return;
      tabInd.setAttribute('aria-pressed', 'true');
      tabKel.setAttribute('aria-pressed', 'false');
      if (hiddenTipe) hiddenTipe.value = 'mandiri';
      const redirectTipe = document.querySelector('input[name="redirect_tipe"]');
      if (redirectTipe) redirectTipe.value = 'mandiri';
      const redirectJumlah = document.querySelector('input[name="redirect_jumlah"]');
      if (redirectJumlah) redirectJumlah.value = '1';
      if (kelompokSection) {
        kelompokSection.style.display = 'none';
        kelompokSection.querySelectorAll('input').forEach(inp => {
          inp.disabled = true;
        });
      }

      tabInd.style.border = '1px solid #003566';
      tabInd.style.background = '#FFFFFF';
      tabInd.style.color = '#003566';
      tabInd.style.fontWeight = '700';
      const indSvg = tabInd.querySelector('svg');
      if (indSvg) indSvg.setAttribute('stroke', '#003566');

      tabKel.style.border = '1px solid transparent';
      tabKel.style.background = 'transparent';
      tabKel.style.color = '#4A5560';
      tabKel.style.fontWeight = '600';
      const kelSvg = tabKel.querySelector('svg');
      if (kelSvg) kelSvg.setAttribute('stroke', '#4A5560');

      window.dispatchEvent(new CustomEvent('roster:changed', { detail: { totalMembers: 1 } }));
    };

    const activateKel = () => {
      if (isTypeLocked && tabKel.getAttribute('aria-pressed') !== 'true') return;
      tabInd.setAttribute('aria-pressed', 'false');
      tabKel.setAttribute('aria-pressed', 'true');
      if (hiddenTipe) hiddenTipe.value = 'kelompok';
      const redirectTipe = document.querySelector('input[name="redirect_tipe"]');
      if (redirectTipe) redirectTipe.value = 'kelompok';

      if (kelompokSection) {
        kelompokSection.style.display = 'flex';
        const namaKel = kelompokSection.querySelector('#nama_kelompok');
        if (namaKel) namaKel.disabled = false;
        const roster = kelompokSection.querySelector('#roster-anggota-container');
        const targetCount = parseInt(roster?.getAttribute('data-target-anggota') || '1', 10);
        const cards = kelompokSection.querySelectorAll('.card-anggota-item');
        cards.forEach((card, idx) => {
          const isVis = idx < targetCount;
          card.style.display = isVis ? 'flex' : 'none';
          card.querySelectorAll('input').forEach(inp => {
            inp.disabled = !isVis;
          });
        });
        updateRosterState();
      }

      tabKel.style.border = '1px solid #003566';
      tabKel.style.background = '#FFFFFF';
      tabKel.style.color = '#003566';
      tabKel.style.fontWeight = '700';
      const kelSvg = tabKel.querySelector('svg');
      if (kelSvg) kelSvg.setAttribute('stroke', '#003566');

      tabInd.style.border = '1px solid transparent';
      tabInd.style.background = 'transparent';
      tabInd.style.color = '#4A5560';
      tabInd.style.fontWeight = '600';
      const indSvg = tabInd.querySelector('svg');
      if (indSvg) indSvg.setAttribute('stroke', '#4A5560');
    };

    if (!isTypeLocked) {
      tabInd.addEventListener('click', activateInd);
      tabKel.addEventListener('click', activateKel);
    }

    // Initial state based on hidden input
    if (hiddenTipe && hiddenTipe.value === 'kelompok') {
      activateKel();
    } else if (tabInd.getAttribute('aria-pressed') === 'true') {
      activateInd();
    }
  }
}

/**
 * Cascading Dropdowns: Periode Bulan -> Jenjang -> Penempatan (Bidang) -> Jurusan
 */
export function initCascadingEducationFields() {
  const bulanSelect = document.getElementById('bulan_magang');
  const jenjangSelect = document.getElementById('jenjang');
  const bidangSelect = document.getElementById('bidang');
  const jurusanSelect = document.getElementById('jurusan');
  const matrixScript = document.getElementById('register-penempatan-matrix');
  const legacyScript = document.getElementById('register-jurusan-data');
  const hintBidang = document.getElementById('hint-bidang');
  const hintJurusan = document.getElementById('hint-jurusan');
  const liveQuotaCard = document.getElementById('live-quota-status-card');
  const lqcTitle = document.getElementById('lqc-title');
  const lqcDesc = document.getElementById('lqc-desc');
  const lqcIcon = document.getElementById('lqc-icon');

  if (!jenjangSelect || !jurusanSelect) return;

  // Jika field pendidikan berstatus locked/disabled dari kuota landing page, pertahankan
  if (jurusanSelect.getAttribute('data-locked') === 'true' || jurusanSelect.disabled) {
    return;
  }

  let matrixPenempatan = {};
  if (matrixScript) {
    try {
      matrixPenempatan = JSON.parse(matrixScript.textContent || '{}');
    } catch (e) {
      matrixPenempatan = {};
    }
  }

  let masterJurusan = [];
  if (legacyScript) {
    try {
      masterJurusan = JSON.parse(legacyScript.textContent || '[]');
    } catch (e) {
      masterJurusan = [];
    }
  }

  const initialBidangId = bidangSelect ? (bidangSelect.getAttribute('data-initial-bidang') || bidangSelect.value) : '';
  const initialJurusanId = jurusanSelect.getAttribute('data-initial-jurusan') || jurusanSelect.value;

  const getActiveMonthId = () => {
    if (bulanSelect && bulanSelect.value && bulanSelect.value !== '') {
      return String(bulanSelect.value);
    }
    const monthKeys = Object.keys(matrixPenempatan).filter(k => k !== 'default');
    return monthKeys.length > 0 ? monthKeys[0] : 'default';
  };

  const updateLiveQuotaCard = () => {
    if (!liveQuotaCard) return;

    const selectedBidangId = bidangSelect ? String(bidangSelect.value || '').trim() : '';
    const selectedJurusanId = String(jurusanSelect.value || '').trim();

    if (!selectedBidangId || !selectedJurusanId) {
      liveQuotaCard.style.display = 'none';
      return;
    }

    const monthId = getActiveMonthId();
    const jenjang = (jenjangSelect.value || '').toLowerCase().trim();
    const bidangList = matrixPenempatan[monthId]?.[jenjang] || matrixPenempatan['default']?.[jenjang] || [];
    const currentBidang = bidangList.find(b => String(b.id) === selectedBidangId);

    const hiddenJumlah = document.querySelector('input[name="redirect_jumlah"], input[name="jumlah_kelompok"]');
    const tipeHidden = document.getElementById('tipe_pendaftaran');
    const isKelompok = tipeHidden && tipeHidden.value === 'kelompok';
    const memberCount = isKelompok ? Math.max(2, parseInt(hiddenJumlah?.value || '2', 10)) : 1;

    let sisa = currentBidang ? (parseInt(currentBidang.sisa, 10) || 0) : 0;
    if (currentBidang && Array.isArray(currentBidang.jurusan)) {
      const matchJ = currentBidang.jurusan.find(j => String(j.id) === selectedJurusanId);
      if (matchJ && matchJ.sisa !== undefined) {
        sisa = parseInt(matchJ.sisa, 10) || 0;
      }
    }

    const namaBulan = (bulanSelect && bulanSelect.options[bulanSelect.selectedIndex])
      ? bulanSelect.options[bulanSelect.selectedIndex].text.replace(/\s*\(.*?\)/g, '').trim()
      : 'Periode Magang';

    if (sisa >= memberCount && sisa > 0) {
      liveQuotaCard.className = 'auth-live-quota-card is-tersedia';
      if (lqcIcon) {
        lqcIcon.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"></path></svg>';
      }
      if (lqcTitle) {
        lqcTitle.textContent = `Kuota Tersedia (${sisa} Kursi Tersisa)`;
      }
    } else {
      liveQuotaCard.className = 'auth-live-quota-card is-penuh';
      if (lqcIcon) {
        lqcIcon.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#D97706" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>';
      }
      if (lqcTitle) {
        if (sisa > 0 && sisa < memberCount) {
          lqcTitle.textContent = `Sisa ${sisa} Kursi — Kelompok Masuk Waiting List`;
        } else {
          lqcTitle.textContent = 'Kuota Penuh (Otomatis Masuk Waiting List)';
        }
      }
    }

    liveQuotaCard.style.display = 'flex';
  };

  const updateJurusan = (preferredJurusanVal) => {
    const monthId = getActiveMonthId();
    const selectedJenjang = (jenjangSelect.value || '').toLowerCase().trim();
    const selectedBidangId = bidangSelect ? String(bidangSelect.value || '').trim() : '';

    jurusanSelect.innerHTML = '';
    const defaultOpt = document.createElement('option');
    defaultOpt.value = '';

    if (!selectedBidangId) {
      defaultOpt.textContent = '-- Pilih Bidang Peminatan / Penempatan Terlebih Dahulu --';
      jurusanSelect.appendChild(defaultOpt);
      if (hintJurusan) {
        hintJurusan.textContent = 'Pilih bidang penempatan terlebih dahulu.';
      }
      updateLiveQuotaCard();
      return;
    }

    defaultOpt.textContent = '-- Pilih Jurusan / Program Studi --';
    jurusanSelect.appendChild(defaultOpt);

    const bidangList = matrixPenempatan[monthId]?.[selectedJenjang] || matrixPenempatan['default']?.[selectedJenjang] || [];
    const currentBidang = bidangList.find(b => String(b.id) === selectedBidangId);

    let listJurusan = [];
    if (currentBidang && Array.isArray(currentBidang.jurusan) && currentBidang.jurusan.length > 0) {
      listJurusan = currentBidang.jurusan;
    } else {
      listJurusan = masterJurusan.filter(j => {
        const jenjangMatch = !selectedJenjang || (j.kategori || '').toLowerCase() === selectedJenjang;
        const bidangMatch = !selectedBidangId || (Array.isArray(j.bidang_ids) && j.bidang_ids.includes(selectedBidangId));
        return jenjangMatch && bidangMatch;
      });
    }

    const currentSelectedVal = preferredJurusanVal !== undefined ? preferredJurusanVal : jurusanSelect.value;
    let matchFound = false;

    listJurusan.forEach(j => {
      const opt = document.createElement('option');
      opt.value = j.id;
      // Aturan Khusus: Jurusan HANYA menampilkan nama jurusannya saja tanpa sisa kursi
      opt.textContent = j.nama;
      if (currentSelectedVal && String(j.id) === String(currentSelectedVal)) {
        opt.selected = true;
        matchFound = true;
      }
      jurusanSelect.appendChild(opt);
    });

    if (!matchFound && currentSelectedVal) {
      jurusanSelect.value = '';
    }

    if (hintJurusan) {
      hintJurusan.textContent = 'Pilihan jurusan menyesuaikan bidang penempatan di atas.';
    }

    updateLiveQuotaCard();
  };

  const updateBidang = (preferredBidangVal) => {
    if (!bidangSelect) return;

    const monthId = getActiveMonthId();
    const selectedJenjang = (jenjangSelect.value || '').toLowerCase().trim();

    bidangSelect.innerHTML = '';
    const defaultOpt = document.createElement('option');
    defaultOpt.value = '';

    if (!selectedJenjang) {
      defaultOpt.textContent = '-- Pilih Jenjang Pendidikan Terlebih Dahulu --';
      bidangSelect.appendChild(defaultOpt);
      if (hintBidang) {
        hintBidang.textContent = 'Pilih jenjang pendidikan formal Anda terlebih dahulu.';
      }
      updateJurusan();
      return;
    }

    defaultOpt.textContent = '-- Pilih Bidang Peminatan / Penempatan --';
    bidangSelect.appendChild(defaultOpt);

    const bidangList = matrixPenempatan[monthId]?.[selectedJenjang] || matrixPenempatan['default']?.[selectedJenjang] || [];

    const currentSelectedVal = preferredBidangVal !== undefined ? preferredBidangVal : bidangSelect.value;
    let matchFound = false;

    if (bidangList.length > 0) {
      bidangList.forEach(b => {
        const opt = document.createElement('option');
        opt.value = b.id;
        // Penempatan menampilkan kuota yang tersedia
        const sisaKursi = parseInt(b.sisa, 10) || 0;
        opt.textContent = sisaKursi > 0
          ? `${b.nama} (Tersedia ${sisaKursi} Kursi)`
          : `${b.nama} (Kuota Penuh)`;

        if (currentSelectedVal && String(b.id) === String(currentSelectedVal)) {
          opt.selected = true;
          matchFound = true;
        }
        bidangSelect.appendChild(opt);
      });
    }

    if (!matchFound && currentSelectedVal) {
      bidangSelect.value = '';
    }

    if (hintBidang) {
      hintBidang.textContent = 'Pilih bidang penempatan yang tersedia kuotanya.';
    }

    // Lanjutkan cascading ke Jurusan
    const targetJurusanId = jurusanSelect.value || initialJurusanId;
    updateJurusan(targetJurusanId);
  };

  if (bulanSelect) {
    bulanSelect.addEventListener('change', () => {
      updateBidang(bidangSelect ? bidangSelect.value : '');
    });
  }

  jenjangSelect.addEventListener('change', () => {
    updateBidang();
  });

  if (bidangSelect) {
    bidangSelect.addEventListener('change', () => {
      updateJurusan();
    });
  }

  jurusanSelect.addEventListener('change', () => {
    updateLiveQuotaCard();
  });

  // Listener untuk perubahan tipe pendaftaran (Individu vs Kelompok) agar kartu live menyesuaikan
  const typeContainer = document.querySelector('.reg-type-container');
  if (typeContainer) {
    typeContainer.addEventListener('click', (e) => {
      const btn = e.target.closest('[data-reg-type]');
      if (btn) {
        setTimeout(() => updateLiveQuotaCard(), 50);
      }
    });
  }

  window.addEventListener('roster:changed', () => {
    updateLiveQuotaCard();
  });

  // Inisialisasi awal saat halaman dimuat
  if (jenjangSelect.value) {
    updateBidang(initialBidangId);
    if (initialJurusanId) {
      updateJurusan(initialJurusanId);
    }
  } else if (initialBidangId) {
    updateBidang(initialBidangId);
  }
}

/**
 * Modal Kebijakan Privasi & Syarat Ketentuan
 */
export function initPrivacyTermsModal() {
  const modal = document.getElementById('modalPrivasiSyarat');
  if (!modal) return;

  const openBtns = document.querySelectorAll('[data-action="open-modal-privasi"]');
  const closeBtns = modal.querySelectorAll('[data-action="close-modal-privasi"]');
  const tabBtnPrivasi = document.getElementById('modalTabBtnPrivasi');
  const tabBtnSyarat = document.getElementById('modalTabBtnSyarat');
  const contentPrivasi = document.getElementById('modalContentPrivasi');
  const contentSyarat = document.getElementById('modalContentSyarat');

  const openModal = (defaultTab = 'privasi') => {
    modal.style.display = 'flex';
    modal.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
    switchTab(defaultTab);
  };

  const closeModal = () => {
    modal.style.display = 'none';
    modal.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
  };

  const switchTab = (tab) => {
    if (!tabBtnPrivasi || !tabBtnSyarat || !contentPrivasi || !contentSyarat) return;

    if (tab === 'syarat') {
      contentPrivasi.style.display = 'none';
      contentSyarat.style.display = 'flex';

      tabBtnSyarat.style.border = '1px solid #003566';
      tabBtnSyarat.style.background = '#FFFFFF';
      tabBtnSyarat.style.color = '#003566';
      tabBtnSyarat.style.fontWeight = '700';

      tabBtnPrivasi.style.border = '1px solid transparent';
      tabBtnPrivasi.style.background = 'transparent';
      tabBtnPrivasi.style.color = '#4A5560';
      tabBtnPrivasi.style.fontWeight = '600';
    } else {
      contentSyarat.style.display = 'none';
      contentPrivasi.style.display = 'flex';

      tabBtnPrivasi.style.border = '1px solid #003566';
      tabBtnPrivasi.style.background = '#FFFFFF';
      tabBtnPrivasi.style.color = '#003566';
      tabBtnPrivasi.style.fontWeight = '700';

      tabBtnSyarat.style.border = '1px solid transparent';
      tabBtnSyarat.style.background = 'transparent';
      tabBtnSyarat.style.color = '#4A5560';
      tabBtnSyarat.style.fontWeight = '600';
    }
  };

  openBtns.forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      openModal('privasi');
    });
  });

  closeBtns.forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      closeModal();
    });
  });

  modal.addEventListener('click', (e) => {
    if (e.target === modal) {
      closeModal();
    }
  });

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && modal.style.display === 'flex') {
      closeModal();
    }
  });

  if (tabBtnPrivasi) {
    tabBtnPrivasi.addEventListener('click', () => switchTab('privasi'));
  }
  if (tabBtnSyarat) {
    tabBtnSyarat.addEventListener('click', () => switchTab('syarat'));
  }
}

/**
 * Peringatan Akun Terkunci Sementara (Live Lockout Countdown)
 */
export function initLockoutCountdown() {
  const banner = document.getElementById('lockoutBanner');
  if (!banner) return;

  const timerDisplay = document.getElementById('lockoutTimerDisplay');
  const submitBtn = document.querySelector('button[type="submit"].btn-main');

  let seconds = parseInt(banner.getAttribute('data-lockout-seconds') || '60', 10);
  if (isNaN(seconds) || seconds <= 0) return;

  if (submitBtn) {
    submitBtn.disabled = true;
    submitBtn.style.opacity = '0.6';
    submitBtn.style.cursor = 'not-allowed';
  }

  const intervalId = setInterval(() => {
    seconds -= 1;
    if (timerDisplay) {
      timerDisplay.textContent = seconds.toString();
    }

    if (seconds <= 0) {
      clearInterval(intervalId);
      banner.style.background = '#F0FDF4';
      banner.style.border = '1.5px solid #86EFAC';
      banner.style.color = '#166534';
      banner.innerHTML = `
        <div style="display: flex; align-items: center; gap: 8px; font-weight: 800; font-size: 15px">
          <svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#16A34A" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
          <span>Waktu Tunggu Selesai</span>
        </div>
        <p style="margin: 4px 0 0; font-size: 13.5px; line-height: 1.5; color: #166534">
          Anda sekarang dapat mencoba masuk kembali ke akun Anda.
        </p>
      `;

      if (submitBtn) {
        submitBtn.disabled = false;
        submitBtn.style.opacity = '1';
        submitBtn.style.cursor = 'pointer';
      }
    }
  }, 1000);
}

/**
 * Auto-capitalization & live caret preservation for CAPTCHA inputs
 */
export function initAutoCapitalizeCaptcha() {
  const inputs = document.querySelectorAll('input[name="captcha"], .captcha-input');
  inputs.forEach((input) => {
    if (!input.hasAttribute('autocapitalize')) {
      input.setAttribute('autocapitalize', 'characters');
    }
    input.setAttribute('spellcheck', 'false');

    input.addEventListener('input', (event) => {
      const target = event.target;
      if (!target || typeof target.value !== 'string') return;

      const start = target.selectionStart;
      const end = target.selectionEnd;
      const originalValue = target.value;
      const upperValue = originalValue.toUpperCase();

      if (originalValue !== upperValue) {
        target.value = upperValue;
        if (start !== null && end !== null) {
          target.setSelectionRange(start, end);
        }
      }
    });
  });
}

/**
 * Penanganan Login Asinkron (Zero-Reload / AJAX Fetch)
 * - Mencegah halaman reload saat kata sandi atau CAPTCHA salah
 * - Menjaga isi input email tetap utuh
 * - Refresh otomatis gambar CAPTCHA
 * - Efek animasi getar (shake) pada input password
 */
export function initAjaxLoginForm() {
  const loginForm = document.querySelector('form[action*="/login"]');
  if (!loginForm) return;

  const submitBtn = loginForm.querySelector('button[type="submit"]');
  const alertContainer = document.getElementById('dynamicAuthAlert');
  const passwordInput = document.getElementById('sandi');
  const captchaInput = document.getElementById('kode');
  const captchaImg = document.querySelector('[data-captcha-img]');

  loginForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    const originalBtnText = submitBtn ? submitBtn.innerText : 'Masuk';
    if (submitBtn) {
      submitBtn.disabled = true;
      submitBtn.innerText = 'Memverifikasi...';
    }

    const formData = new FormData(loginForm);
    const actionUrl = loginForm.getAttribute('action');

    try {
      const response = await fetch(actionUrl, {
        method: 'POST',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json'
        },
        body: formData
      });

      const data = await response.json().catch(() => ({}));

      if (response.ok && data.success) {
        window.location.href = data.redirect || '/dashboard';
        return;
      }

      // Refresh Captcha Otomatis tanpa reload halaman
      if (captchaImg) {
        captchaImg.src = '/captcha/image?t=' + Date.now();
      }
      if (captchaInput) {
        captchaInput.value = '';
      }

      // Efek getar pada input password & kursor tetap fokus
      if (passwordInput) {
        passwordInput.classList.add('shake-error');
        setTimeout(() => passwordInput.classList.remove('shake-error'), 500);
        passwordInput.focus();
        passwordInput.select();
      }

      // Tampilkan Alert Error Dinamis tanpa reload halaman (Email tetap ada)
      if (alertContainer) {
        alertContainer.style.display = 'block';

        if (response.status === 429 || data.locked) {
          const seconds = data.lockout_seconds || 60;
          alertContainer.innerHTML = `
            <div id="lockoutBanner" data-lockout-seconds="${seconds}" style="margin-bottom: 20px; padding: 16px; border-radius: 12px; background: #FEF2F2; border: 1.5px solid #F87171; color: #991B1B; display: flex; flex-direction: column; gap: 8px">
              <div style="display: flex; align-items: center; gap: 8px; font-weight: 800; font-size: 15px">
                <svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                <span>Akses Akun Terkunci Sementara</span>
              </div>
              <p style="margin: 0; font-size: 13.5px; line-height: 1.5; color: #7F1D1D">
                Terlalu banyak percobaan masuk yang gagal. Demi keamanan akun, silakan tunggu <strong id="lockoutTimerDisplay" style="color: #991B1B; font-size: 14px">${seconds}</strong> detik sebelum mencoba kembali.
              </p>
              <div style="margin-top: 4px; padding-top: 8px; border-top: 1px dashed #FCA5A5; display: flex; align-items: center; justify-content: space-between">
                <span style="font-size: 12.5px; color: #7F1D1D">Lupa kata sandi akun Anda?</span>
                <a href="/forgot-password" style="font-size: 13px; font-weight: 700; color: #991B1B; text-decoration: underline">Pulihkan Akun →</a>
              </div>
            </div>
          `;
          initLockoutCountdown();
        } else {
          const errorMsg = (data.errors && typeof data.errors === 'object')
            ? Object.values(data.errors).flat()[0]
            : (data.message || 'Kombinasi email dan kata sandi yang Anda masukkan tidak sesuai.');

          alertContainer.innerHTML = `
            <div style="margin-bottom: 20px; padding: 12px 16px; border-radius: 10px; background: #FEE2E2; border: 1px solid #FCA5A5; color: #991B1B; font-size: 13.5px; line-height: 1.5; display: flex; align-items: center; gap: 8px">
              <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
              <span>${errorMsg}</span>
            </div>
          `;
        }
      }

    } catch (err) {
      // Fallback jika fetch terganggu
      loginForm.submit();
    } finally {
      if (submitBtn) {
        submitBtn.disabled = false;
        submitBtn.innerText = originalBtnText;
      }
    }
  });
}

/**
 * Realtime Password Confirmation Matching Feedback (Register page)
 */
export function initPasswordConfirmationMatch() {
  const pwInput = document.getElementById('sandi');
  const confirmInput = document.getElementById('sandi_confirmation');
  const hint = document.getElementById('hint-sandi-confirmation');

  if (!pwInput || !confirmInput || !hint) return;

  const defaultHintText = 'Masukkan ulang kata sandi yang sama untuk konfirmasi.';
  const defaultBorderColor = '#AAAAAA';

  const updateMatchFeedback = () => {
    const val = pwInput.value;
    const confirmVal = confirmInput.value;

    if (!confirmVal) {
      hint.textContent = defaultHintText;
      hint.style.color = '#5F6B76';
      confirmInput.style.borderColor = defaultBorderColor;
      return;
    }

    if (val === confirmVal) {
      hint.textContent = '✓ Konfirmasi kata sandi cocok.';
      hint.style.color = '#16A34A';
      confirmInput.style.borderColor = '#86EFAC';
    } else {
      hint.textContent = 'Kata sandi belum cocok.';
      hint.style.color = '#DC2626';
      confirmInput.style.borderColor = '#FCA5A5';
    }
  };

  pwInput.addEventListener('input', updateMatchFeedback);
  confirmInput.addEventListener('input', updateMatchFeedback);
}

/**
 * Main initializer for Auth Depth pages
 */
export function initAuthDepth() {
  const canvas = document.querySelector('canvas[data-auth-canvas]');
  if (canvas) {
    startNetwork(canvas);
  }
  initAuthInteractions();
  initCascadingEducationFields();
  initPrivacyTermsModal();
  initLockoutCountdown();
  initAutoCapitalizeCaptcha();
  initPasswordStrengthMeter();
  initAjaxLoginForm();
  initPasswordConfirmationMatch();
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initAuthDepth);
} else {
  initAuthDepth();
}
