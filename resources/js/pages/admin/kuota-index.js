/**
 * Admin Kuota Index Management Module
 * CSP Compliant (no inline scripts or inline event handlers)
 */

document.addEventListener('DOMContentLoaded', () => {
    const bidangDataEl = document.getElementById('magkom-bidang-master-data');
    let masterBidangData = [];
    if (bidangDataEl) {
        try {
            masterBidangData = JSON.parse(bidangDataEl.textContent || '[]');
        } catch (e) {
            console.error('Failed to parse master bidang data', e);
        }
    }

    let activeJurusanIds = [];
    let currentBidangKapasitasMax = 18;
    let currentBidangJurusans = [];

    // --- Ubah Bulan Modal ---
    function openUbahBulanModal() {
        const modal = document.getElementById('modalUbahBulan');
        if (modal) {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';

            const tglBuka = document.getElementById('ubahBulanTglBuka');
            const tglTutup = document.getElementById('ubahBulanTglTutup');
            const tglMulai = document.getElementById('ubahBulanMulai');

            if (tglBuka && tglTutup) {
                tglBuka.addEventListener('change', function () {
                    if (this.value) {
                        tglTutup.min = this.value;
                        if (tglTutup.value && tglTutup.value < this.value) {
                            tglTutup.value = this.value;
                        }
                    }
                });
            }

            if (tglTutup && tglMulai) {
                tglTutup.addEventListener('change', function () {
                    if (this.value) {
                        const nextDay = new Date(this.value);
                        nextDay.setDate(nextDay.getDate() + 1);
                        const isoStr = nextDay.toISOString().split('T')[0];
                        tglMulai.min = isoStr;
                        if (tglMulai.value && tglMulai.value <= this.value) {
                            tglMulai.value = isoStr;
                        }
                    }
                });
            }
        }
    }

    function closeUbahBulanModal() {
        const modal = document.getElementById('modalUbahBulan');
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }
    }

    function setStatusUbahBulan(status) {
        const input = document.getElementById('statusUbahInput');
        const btnDraft = document.getElementById('btnUbahStatusDraft');
        const btnDibuka = document.getElementById('btnUbahStatusDibuka');
        const iconDraft = document.getElementById('checkIconUbahDraft');
        const iconDibuka = document.getElementById('checkIconUbahDibuka');

        if (input) input.value = status;

        if (status === 'draft') {
            if (btnDraft) btnDraft.classList.add('active');
            if (btnDibuka) btnDibuka.classList.remove('active');
            if (iconDraft) iconDraft.style.display = 'inline-flex';
            if (iconDibuka) iconDibuka.style.display = 'none';
        } else {
            if (btnDraft) btnDraft.classList.remove('active');
            if (btnDibuka) btnDibuka.classList.add('active');
            if (iconDraft) iconDraft.style.display = 'none';
            if (iconDibuka) iconDibuka.style.display = 'inline-flex';
        }
    }

    // --- Buat Bulan Modal ---
    function openBuatBulanModal() {
        const modal = document.getElementById('modalBuatBulan');
        if (modal) {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';

            const tglBuka = document.getElementById('buatBulanTglBuka');
            const tglTutup = document.getElementById('buatBulanTglTutup');
            const tglMulai = document.getElementById('buatBulanMulai');

            if (tglBuka && tglTutup) {
                tglBuka.addEventListener('change', function () {
                    if (this.value) {
                        tglTutup.min = this.value;
                        if (tglTutup.value && tglTutup.value < this.value) {
                            tglTutup.value = this.value;
                        }
                    }
                });
            }

            if (tglTutup && tglMulai) {
                tglTutup.addEventListener('change', function () {
                    if (this.value) {
                        const nextDay = new Date(this.value);
                        nextDay.setDate(nextDay.getDate() + 1);
                        const isoStr = nextDay.toISOString().split('T')[0];
                        tglMulai.min = isoStr;
                        if (tglMulai.value && tglMulai.value <= this.value) {
                            tglMulai.value = isoStr;
                        }
                    }
                });
            }
        }
    }

    function closeBuatBulanModal() {
        const modal = document.getElementById('modalBuatBulan');
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }
    }

    function setStatusAwal(status) {
        const input = document.getElementById('statusAwalInput');
        const btnDraft = document.getElementById('btnStatusDraft');
        const btnDibuka = document.getElementById('btnStatusDibuka');
        const iconDraft = document.getElementById('checkIconDraft');
        const iconDibuka = document.getElementById('checkIconDibuka');

        if (input) input.value = status;

        if (status === 'draft') {
            if (btnDraft) btnDraft.classList.add('active');
            if (btnDibuka) btnDibuka.classList.remove('active');
            if (iconDraft) iconDraft.style.display = 'inline-flex';
            if (iconDibuka) iconDibuka.style.display = 'none';
        } else {
            if (btnDraft) btnDraft.classList.remove('active');
            if (btnDibuka) btnDibuka.classList.add('active');
            if (iconDraft) iconDraft.style.display = 'none';
            if (iconDibuka) iconDibuka.style.display = 'inline-flex';
        }
    }

    // --- Tambah Kuota Modal ---
    function updateHiddenJurusanInputs() {
        const hiddenWrap = document.getElementById('hiddenJurusanInputs');
        if (!hiddenWrap) return;
        hiddenWrap.innerHTML = '';
        activeJurusanIds.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'jurusan_ids[]';
            input.value = id;
            hiddenWrap.appendChild(input);
        });

        if (activeJurusanIds.length > 0) {
            const singleInput = document.createElement('input');
            singleInput.type = 'hidden';
            singleInput.name = 'jurusan_id';
            singleInput.value = activeJurusanIds[0];
            hiddenWrap.appendChild(singleInput);
        }
    }

    function toggleJurusanChip(jurusanId, element) {
        const idx = activeJurusanIds.indexOf(jurusanId);
        const checkSpan = element.querySelector('.chip-check');

        if (idx > -1) {
            activeJurusanIds.splice(idx, 1);
            element.classList.add('inactive');
            if (checkSpan) checkSpan.style.display = 'none';
        } else {
            activeJurusanIds.push(jurusanId);
            element.classList.remove('inactive');
            if (checkSpan) checkSpan.style.display = 'inline';
        }

        updateHiddenJurusanInputs();
    }

    function onBidangSelected(bidangId) {
        const bidang = masterBidangData.find(b => b.id == bidangId);
        const container = document.getElementById('jurusanChipsContainer');
        const miniInfo = document.getElementById('infoBidangKapasitasMini');
        if (!bidang || !container) return;

        if (miniInfo) {
            miniInfo.innerText = `${bidang.pembimbing_count} Pembimbing • Sisa: ${bidang.sisa_kapasitas} kursi`;
        }

        container.innerHTML = '';
        activeJurusanIds = [];

        if (bidang.jurusans && bidang.jurusans.length > 0) {
            bidang.jurusans.forEach(j => {
                activeJurusanIds.push(j.id);
                const chip = document.createElement('button');
                chip.type = 'button';
                chip.className = 'chip-jurusan';
                chip.id = 'chipJurusan_' + j.id;
                chip.setAttribute('data-action', 'toggle-jurusan-chip');
                chip.setAttribute('data-jurusan-id', j.id);
                chip.innerHTML = `<span class="chip-check">✓ </span><span>${j.nama}</span>`;
                container.appendChild(chip);
            });
        } else {
            container.innerHTML = '<span style="font-size: 0.8125rem; color: #64748B;">Belum ada kejuruan terikat di bidang ini.</span>';
        }

        updateHiddenJurusanInputs();
        checkKapasitasLimit();
    }

    function toggleExtraJurusanSelect() {
        const select = document.getElementById('extraJurusanSelect');
        if (!select) return;
        select.style.display = select.style.display === 'none' ? 'inline-block' : 'none';
        if (select.style.display !== 'none') select.focus();
    }

    function addExtraJurusan(selectEl) {
        const jurId = parseInt(selectEl.value);
        if (!jurId) return;

        const opt = selectEl.options[selectEl.selectedIndex];
        const jurNama = opt.getAttribute('data-nama') || opt.text;
        const container = document.getElementById('jurusanChipsContainer');

        if (!activeJurusanIds.includes(jurId)) {
            activeJurusanIds.push(jurId);
            const chip = document.createElement('button');
            chip.type = 'button';
            chip.className = 'chip-jurusan';
            chip.id = 'chipJurusan_' + jurId;
            chip.setAttribute('data-action', 'toggle-jurusan-chip');
            chip.setAttribute('data-jurusan-id', jurId);
            chip.innerHTML = `<span class="chip-check">✓ </span><span>${jurNama}</span>`;
            if (container) container.appendChild(chip);
            updateHiddenJurusanInputs();
        }

        selectEl.value = '';
        selectEl.style.display = 'none';
    }

    function toggleJenjang(jenjangCode) {
        const pill = document.getElementById('pillJenjang' + jenjangCode);
        const input = document.getElementById('inputJenjang' + jenjangCode);
        if (!pill || !input) return;

        const checkSpan = pill.querySelector('.jenjang-check');
        const isActive = pill.classList.contains('active');

        if (isActive) {
            pill.classList.remove('active');
            input.disabled = true;
            if (checkSpan) checkSpan.style.display = 'none';
        } else {
            pill.classList.add('active');
            input.disabled = false;
            input.value = jenjangCode;
            if (checkSpan) checkSpan.style.display = 'inline';
        }
    }

    function checkKapasitasLimit() {
        const bidangSelect = document.getElementById('tambahKuotaBidangSelect');
        const jumlahInput = document.getElementById('tambahKuotaJumlahInput');
        const boxWarning = document.getElementById('boxWarningKapasitas');
        const textDetail = document.getElementById('textWarningKapasitasDetail');

        if (!bidangSelect || !jumlahInput || !boxWarning || !textDetail) return;

        const bidangId = bidangSelect.value;
        const bidang = masterBidangData.find(b => b.id == bidangId);
        const inputVal = parseInt(jumlahInput.value) || 0;

        if (!bidang) {
            boxWarning.style.display = 'none';
            return;
        }

        if (inputVal > bidang.sisa_kapasitas) {
            boxWarning.style.display = 'flex';
            textDetail.innerText = `Kapasitas ${bidang.nama} ${bidang.kapasitas_bimbingan} peserta (${bidang.pembimbing_count} Pembimbing x 6) dan kuota terpasang sudah ${bidang.kuota_terpasang}, sisa kapasitas ${bidang.sisa_kapasitas}. Kurangi jumlah kuota yang ditambahkan atau tambah Pembimbing aktif di bidang ini.`;
        } else {
            boxWarning.style.display = 'none';
        }
    }

    function openTambahKuotaModal() {
        const modal = document.getElementById('modalTambahKuota');
        if (modal) {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
            const bidangSelect = document.getElementById('tambahKuotaBidangSelect');
            if (bidangSelect && bidangSelect.value) {
                onBidangSelected(bidangSelect.value);
            }
        }
    }

    function closeTambahKuotaModal() {
        const modal = document.getElementById('modalTambahKuota');
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }
    }

    // --- Edit Bidang Modal ---
    function recalcTotalKapasitasBidang() {
        const inputs = document.querySelectorAll('.input-kuota-jurusan');
        let total = 0;
        inputs.forEach(inp => {
            total += parseInt(inp.value) || 0;
        });

        const displayTotal = document.getElementById('totalKapasitasBidangDisplay');
        if (displayTotal) displayTotal.innerText = total;

        const warnBox = document.getElementById('boxWarningKapasitasBidang');
        const warnTotal = document.getElementById('warnTotalKapasitas');
        const warnMax = document.getElementById('warnMaxKapasitas');

        if (total > currentBidangKapasitasMax) {
            if (warnBox) warnBox.style.display = 'flex';
            if (warnTotal) warnTotal.innerText = total;
            if (warnMax) warnMax.innerText = currentBidangKapasitasMax;
        } else {
            if (warnBox) warnBox.style.display = 'none';
        }
    }

    function setStatusBidang(status) {
        const inputStatus = document.getElementById('inputStatusBidang');
        const btnBuka = document.getElementById('btnBidangBuka');
        const btnTutup = document.getElementById('btnBidangTutup');
        const iconBuka = document.getElementById('checkIconBidangBuka');
        const iconTutup = document.getElementById('checkIconBidangTutup');
        const wrapAlloc = document.getElementById('wrapJurusanAllocSection');
        const noticeTutup = document.getElementById('boxBidangTutupNotice');
        const submitBtn = document.getElementById('btnSubmitEditBidang');

        if (inputStatus) inputStatus.value = status;

        if (status === 'tutup') {
            if (btnBuka) btnBuka.classList.remove('active');
            if (btnTutup) btnTutup.classList.add('active');
            if (iconBuka) iconBuka.style.display = 'none';
            if (iconTutup) iconTutup.style.display = 'inline-flex';
            if (wrapAlloc) wrapAlloc.style.display = 'none';
            if (noticeTutup) noticeTutup.style.display = 'block';
            if (submitBtn) submitBtn.disabled = false;
            const warnBox = document.getElementById('boxWarningKapasitasBidang');
            if (warnBox) warnBox.style.display = 'none';
            const displayTotal = document.getElementById('totalKapasitasBidangDisplay');
            if (displayTotal) displayTotal.innerText = '0';
        } else {
            if (btnBuka) btnBuka.classList.add('active');
            if (btnTutup) btnTutup.classList.remove('active');
            if (iconBuka) iconBuka.style.display = 'inline-flex';
            if (iconTutup) iconTutup.style.display = 'none';
            if (wrapAlloc) wrapAlloc.style.display = 'block';
            if (noticeTutup) noticeTutup.style.display = 'none';
            recalcTotalKapasitasBidang();
        }
    }

    function openEditBidangModal(bidangId, namaBidang, jurusans, kapasitasBimbingan, pembimbingCount) {
        const modal = document.getElementById('modalEditBidang');
        const form = document.getElementById('formEditBidang');
        const title = document.getElementById('editBidangTitle');
        const listWrap = document.getElementById('jurusanAllocList');
        const maxDisplay = document.getElementById('maxKapasitasBimbinganDisplay');
        const countDisplay = document.getElementById('pembimbingCountDisplay');

        currentBidangKapasitasMax = kapasitasBimbingan || 18;
        currentBidangJurusans = jurusans || [];

        if (maxDisplay) maxDisplay.innerText = currentBidangKapasitasMax;
        if (countDisplay) countDisplay.innerText = pembimbingCount || 2;
        if (title) title.innerText = 'Ubah Kuota: ' + namaBidang;
        if (form) form.action = '/admin/kuota/bidang/' + bidangId;

        let totalAwal = 0;
        if (listWrap) {
            listWrap.innerHTML = '';
            if (!jurusans || jurusans.length === 0) {
                listWrap.innerHTML = '<p style="color: #64748B; font-size: 0.85rem; text-align: center; padding: 1.5rem 0;">Belum ada kejuruan terikat di bidang ini.</p>';
            } else {
                jurusans.forEach(j => {
                    totalAwal += (parseInt(j.jumlah) || 0);
                    const row = document.createElement('div');
                    row.style = 'display: flex; justify-content: space-between; align-items: center; gap: 1rem; padding: 0.65rem 0.85rem; background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 8px;';
                    row.innerHTML = `
                        <div style="flex: 1;">
                            <strong style="font-size: 0.875rem; color: #0F172A; display: block;">${j.nama}</strong>
                            <small style="color: #64748B; font-size: 0.725rem;">Terisi: ${j.terisi} kursi</small>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <label style="font-size: 0.75rem; color: #475569; font-weight: 600;">Kuota:</label>
                            <input type="number" 
                                   name="alokasi[${j.id}]" 
                                   class="input-kuota-jurusan"
                                   value="${j.jumlah}" 
                                   min="${j.terisi}" 
                                   max="100" 
                                   required 
                                   style="width: 75px; height: 36px; padding: 0 0.5rem; text-align: center; font-weight: 700; border: 1px solid #CBD5E1; border-radius: 6px;">
                        </div>
                    `;
                    listWrap.appendChild(row);
                });
            }
        }

        if (totalAwal > 0) {
            setStatusBidang('buka');
        } else {
            setStatusBidang('tutup');
        }

        if (modal) {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    }

    function closeEditBidangModal() {
        const modal = document.getElementById('modalEditBidang');
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }
    }

    // --- Delegated Event Listeners ---
    document.addEventListener('click', (e) => {
        const target = e.target;

        // Modal triggers
        const openUbahBtn = target.closest('[data-action="open-ubah-bulan"]');
        if (openUbahBtn) {
            e.preventDefault();
            openUbahBulanModal();
            return;
        }

        const closeUbahBtn = target.closest('[data-action="close-ubah-bulan"]');
        if (closeUbahBtn) {
            e.preventDefault();
            closeUbahBulanModal();
            return;
        }

        const openBuatBtn = target.closest('[data-action="open-buat-bulan"]');
        if (openBuatBtn) {
            e.preventDefault();
            openBuatBulanModal();
            return;
        }

        const closeBuatBtn = target.closest('[data-action="close-buat-bulan"]');
        if (closeBuatBtn) {
            e.preventDefault();
            closeBuatBulanModal();
            return;
        }

        const openTambahBtn = target.closest('[data-action="open-tambah-kuota"]');
        if (openTambahBtn) {
            e.preventDefault();
            openTambahKuotaModal();
            return;
        }

        const closeTambahBtn = target.closest('[data-action="close-tambah-kuota"]');
        if (closeTambahBtn) {
            e.preventDefault();
            closeTambahKuotaModal();
            return;
        }

        const openEditBidangBtn = target.closest('[data-action="open-edit-bidang"]');
        if (openEditBidangBtn) {
            e.preventDefault();
            let jurusans = [];
            try {
                jurusans = JSON.parse(openEditBidangBtn.dataset.jurusans || '[]');
            } catch (err) {
                console.error('Invalid jurusans JSON on button', err);
            }
            openEditBidangModal(
                openEditBidangBtn.dataset.bidangId,
                openEditBidangBtn.dataset.bidangNama,
                jurusans,
                parseInt(openEditBidangBtn.dataset.kapasitasBimbingan) || 18,
                parseInt(openEditBidangBtn.dataset.pembimbingCount) || 2
            );
            return;
        }

        const closeEditBidangBtn = target.closest('[data-action="close-edit-bidang"]');
        if (closeEditBidangBtn) {
            e.preventDefault();
            closeEditBidangModal();
            return;
        }

        // Status toggles
        const setStatusUbahBtn = target.closest('[data-action="set-status-ubah-bulan"]');
        if (setStatusUbahBtn) {
            e.preventDefault();
            setStatusUbahBulan(setStatusUbahBtn.dataset.status);
            return;
        }

        const setStatusAwalBtn = target.closest('[data-action="set-status-awal"]');
        if (setStatusAwalBtn) {
            e.preventDefault();
            setStatusAwal(setStatusAwalBtn.dataset.status);
            return;
        }

        const setStatusBidangBtn = target.closest('[data-action="set-status-bidang"]');
        if (setStatusBidangBtn) {
            e.preventDefault();
            setStatusBidang(setStatusBidangBtn.dataset.status);
            return;
        }

        // Jurusan chips & Jenjang pills
        const toggleJurusanBtn = target.closest('[data-action="toggle-jurusan-chip"]');
        if (toggleJurusanBtn) {
            e.preventDefault();
            const jurId = parseInt(toggleJurusanBtn.dataset.jurusanId);
            if (jurId) toggleJurusanChip(jurId, toggleJurusanBtn);
            return;
        }

        const toggleExtraJurusanBtn = target.closest('[data-action="toggle-extra-jurusan"]');
        if (toggleExtraJurusanBtn) {
            e.preventDefault();
            toggleExtraJurusanSelect();
            return;
        }

        const toggleJenjangBtn = target.closest('[data-action="toggle-jenjang"]');
        if (toggleJenjangBtn) {
            e.preventDefault();
            toggleJenjang(toggleJenjangBtn.dataset.jenjang);
            return;
        }
    });

    // Delegated Change / Input Listeners
    document.addEventListener('change', (e) => {
        if (e.target.matches('[data-action="auto-submit-form"]')) {
            if (e.target.form) e.target.form.submit();
        } else if (e.target.id === 'tambahKuotaBidangSelect') {
            onBidangSelected(e.target.value);
        } else if (e.target.id === 'extraJurusanSelect') {
            addExtraJurusan(e.target);
        }
    });

    document.addEventListener('input', (e) => {
        if (e.target.id === 'tambahKuotaJumlahInput') {
            checkKapasitasLimit();
        } else if (e.target.classList.contains('input-kuota-jurusan')) {
            recalcTotalKapasitasBidang();
        }
    });

    // Escape listener
    window.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeUbahBulanModal();
            closeBuatBulanModal();
            closeTambahKuotaModal();
            closeEditBidangModal();
        }
    });

    // Backdrop click listener
    document.querySelectorAll('.modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', function (e) {
            if (e.target === this) {
                closeUbahBulanModal();
                closeBuatBulanModal();
                closeTambahKuotaModal();
                closeEditBidangModal();
            }
        });
    });
});
