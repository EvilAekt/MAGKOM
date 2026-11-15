/**
 * Admin Penempatan Index Module
 * CSP Compliant (no inline scripts or inline event handlers)
 */

document.addEventListener('DOMContentLoaded', () => {
    // --- Tabs Management ---
    function switchPenempatanTab(tabName) {
        const sudahPanel = document.getElementById('panel-penempatan-sudah');
        const belumPanel = document.getElementById('panel-penempatan-belum');
        const btnSudah = document.getElementById('tab-btn-sudah');
        const btnBelum = document.getElementById('tab-btn-belum');
        const iconSudah = document.getElementById('tab-icon-sudah');
        const iconBelum = document.getElementById('tab-icon-belum');
        const badgeSudah = document.getElementById('tab-badge-sudah');
        const badgeBelum = document.getElementById('tab-badge-belum');

        if (tabName === 'belum') {
            if (sudahPanel) sudahPanel.style.display = 'none';
            if (belumPanel) belumPanel.style.display = 'block';

            if (btnSudah) {
                btnSudah.style.background = 'transparent';
                btnSudah.style.color = '#64748B';
                btnSudah.style.fontWeight = '600';
                btnSudah.style.boxShadow = 'none';
                btnSudah.style.borderColor = 'transparent';
                btnSudah.setAttribute('aria-selected', 'false');
            }
            if (iconSudah) iconSudah.style.color = '#64748B';
            if (badgeSudah) {
                badgeSudah.style.background = '#E2E8F0';
                badgeSudah.style.color = '#475569';
            }

            if (btnBelum) {
                btnBelum.style.background = '#FFFFFF';
                btnBelum.style.color = '#B45309';
                btnBelum.style.fontWeight = '700';
                btnBelum.style.boxShadow = '0 1px 3px rgba(0, 0, 0, 0.08), 0 1px 2px rgba(0, 0, 0, 0.04)';
                btnBelum.style.borderColor = 'rgba(226, 232, 240, 0.8)';
                btnBelum.setAttribute('aria-selected', 'true');
            }
            if (iconBelum) iconBelum.style.color = '#D97706';
            if (badgeBelum) {
                badgeBelum.style.background = '#FEF3C7';
                badgeBelum.style.color = '#B45309';
            }
        } else {
            if (sudahPanel) sudahPanel.style.display = 'block';
            if (belumPanel) belumPanel.style.display = 'none';

            if (btnSudah) {
                btnSudah.style.background = '#FFFFFF';
                btnSudah.style.color = '#0641CC';
                btnSudah.style.fontWeight = '700';
                btnSudah.style.boxShadow = '0 1px 3px rgba(0, 0, 0, 0.08), 0 1px 2px rgba(0, 0, 0, 0.04)';
                btnSudah.style.borderColor = 'rgba(226, 232, 240, 0.8)';
                btnSudah.setAttribute('aria-selected', 'true');
            }
            if (iconSudah) iconSudah.style.color = '#0641CC';
            if (badgeSudah) {
                badgeSudah.style.background = '#EFF6FF';
                badgeSudah.style.color = '#0641CC';
            }

            if (btnBelum) {
                btnBelum.style.background = 'transparent';
                btnBelum.style.color = '#64748B';
                btnBelum.style.fontWeight = '600';
                btnBelum.style.boxShadow = 'none';
                btnBelum.style.borderColor = 'transparent';
                btnBelum.setAttribute('aria-selected', 'false');
            }
            if (iconBelum) iconBelum.style.color = '#64748B';
            if (badgeBelum) {
                badgeBelum.style.background = '#E2E8F0';
                badgeBelum.style.color = '#475569';
            }
        }

        try {
            const url = new URL(window.location.href);
            url.searchParams.set('tab', tabName);
            window.history.replaceState({}, '', url.toString());
        } catch (e) {
            // fallback
        }
    }

    // --- Modal Tetapkan Penempatan ---
    function openTetapkanModal(pendaftaranId, namaPeserta, namaJurusan, defaultBidangId, defaultBidangNama) {
        const modal = document.getElementById('modal-tetapkan-penempatan');
        const infoEl = document.getElementById('tetapkan-peserta-info');
        const idInput = document.getElementById('tetapkan-pendaftaran-id');
        const bidangInput = document.getElementById('tetapkan-bidang-id');
        const bidangDisplay = document.getElementById('tetapkan-bidang-display');

        if (infoEl) infoEl.innerText = 'Peserta: ' + namaPeserta + ' (' + namaJurusan + ')';
        if (idInput) idInput.value = pendaftaranId;
        if (bidangInput && defaultBidangId) bidangInput.value = defaultBidangId;
        if (bidangDisplay) bidangDisplay.innerText = defaultBidangNama || 'Dinas Komunikasi dan Informatika';

        if (modal) {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    }

    function closeTetapkanModal() {
        const modal = document.getElementById('modal-tetapkan-penempatan');
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }
    }

    // --- Modal Penempatan Manual ---
    function openManualModal() {
        const modal = document.getElementById('modal-penempatan-manual');
        if (modal) {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    }

    function closeManualModal() {
        const modal = document.getElementById('modal-penempatan-manual');
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }
    }

    function syncManualDates() {
        const tglMulaiEl = document.getElementById('input-tgl-mulai');
        const durasiEl = document.getElementById('select-durasi-bulan');
        const tglSelesaiEl = document.getElementById('input-tgl-selesai');
        const bulanMulaiEl = document.getElementById('input-bulan-mulai');

        if (!tglMulaiEl || !tglMulaiEl.value) return;

        const startDate = new Date(tglMulaiEl.value);
        if (isNaN(startDate.getTime())) return;

        const y = startDate.getFullYear();
        const m = String(startDate.getMonth() + 1).padStart(2, '0');
        if (bulanMulaiEl) bulanMulaiEl.value = `${y}-${m}-01`;

        const durasi = durasiEl ? parseInt(durasiEl.value, 10) || 3 : 3;
        const endDate = new Date(startDate);
        endDate.setMonth(endDate.getMonth() + durasi);
        const ey = endDate.getFullYear();
        const em = String(endDate.getMonth() + 1).padStart(2, '0');
        const ed = String(endDate.getDate()).padStart(2, '0');
        if (tglSelesaiEl) tglSelesaiEl.value = `${ey}-${em}-${ed}`;
    }

    function switchPesertaMode(mode) {
        const btnTerdaftar = document.getElementById('btn-mode-terdaftar');
        const btnBaru = document.getElementById('btn-mode-baru');
        const panelTerdaftar = document.getElementById('panel-peserta-terdaftar');
        const panelBaru = document.getElementById('panel-peserta-baru');
        const inputMode = document.getElementById('input-mode-peserta');
        const selectPeserta = document.getElementById('select-peserta-id');

        if (!inputMode) return;
        inputMode.value = mode;

        if (mode === 'baru') {
            if (btnBaru) {
                btnBaru.style.background = '#0641CC';
                btnBaru.style.color = '#FFFFFF';
            }
            if (btnTerdaftar) {
                btnTerdaftar.style.background = 'transparent';
                btnTerdaftar.style.color = 'var(--neutral-700, #334155)';
            }
            if (panelTerdaftar) panelTerdaftar.style.display = 'none';
            if (panelBaru) panelBaru.style.display = 'flex';
            if (selectPeserta) selectPeserta.removeAttribute('required');
        } else {
            if (btnTerdaftar) {
                btnTerdaftar.style.background = '#0641CC';
                btnTerdaftar.style.color = '#FFFFFF';
            }
            if (btnBaru) {
                btnBaru.style.background = 'transparent';
                btnBaru.style.color = 'var(--neutral-700, #334155)';
            }
            if (panelTerdaftar) panelTerdaftar.style.display = 'block';
            if (panelBaru) panelBaru.style.display = 'none';
            if (selectPeserta) selectPeserta.setAttribute('required', 'required');
        }
    }

    // --- Modal Pindah Pembimbing ---
    function openPindahModal(penempatanId, namaPeserta, pembimbingId) {
        const modal = document.getElementById('modal-pindah-pembimbing');
        const nameEl = document.getElementById('pindah-peserta-nama');
        const formEl = document.getElementById('form-pindah-pembimbing');
        const selectEl = document.getElementById('select-pembimbing-baru');

        if (nameEl) nameEl.innerText = 'Peserta: ' + namaPeserta;
        if (formEl) formEl.action = '/admin/penempatan/' + penempatanId + '/pindah-pembimbing';
        if (selectEl && pembimbingId) selectEl.value = pembimbingId;
        if (modal) {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    }

    function closePindahModal() {
        const modal = document.getElementById('modal-pindah-pembimbing');
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }
    }

    // --- Modal Batal Penempatan ---
    function openBatalModal(penempatanId, namaPeserta) {
        const modal = document.getElementById('modal-batal-penempatan');
        const nameEl = document.getElementById('batal-peserta-nama');
        const formEl = document.getElementById('form-batal-penempatan');

        if (nameEl) nameEl.innerText = 'Peserta: ' + namaPeserta;
        if (formEl) formEl.action = '/admin/penempatan/' + penempatanId + '/batalkan';
        if (modal) {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    }

    function closeBatalModal() {
        const modal = document.getElementById('modal-batal-penempatan');
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }
    }

    // --- Surat Preview Modal ---
    function openSuratPreview(previewUrl, downloadUrl, title) {
        const modal = document.getElementById('modal-surat-preview');
        const iframe = document.getElementById('surat-iframe');
        const titleEl = document.getElementById('surat-modal-title');
        const downloadEl = document.getElementById('surat-modal-download');

        if (titleEl) titleEl.innerText = title;
        if (downloadEl) downloadEl.href = downloadUrl;
        if (iframe) iframe.src = previewUrl;
        if (modal) {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    }

    function closeSuratPreview() {
        const modal = document.getElementById('modal-surat-preview');
        const iframe = document.getElementById('surat-iframe');

        if (iframe) iframe.src = 'about:blank';
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }
    }

    // --- Bulk Operations ---
    function updateBulkUI() {
        const checkedBoxes = document.querySelectorAll('.penempatan-checkbox:checked');
        const count = checkedBoxes.length;
        const floatingBar = document.getElementById('floating-bulk-bar');
        const countBadge = document.getElementById('bulk-selected-count');

        if (countBadge) countBadge.innerText = count;

        if (floatingBar) {
            floatingBar.style.display = count > 0 ? 'flex' : 'none';
        }
    }

    function toggleAllPenempatan(checked) {
        const checkboxes = document.querySelectorAll('.penempatan-checkbox');
        checkboxes.forEach(cb => cb.checked = checked);
        const masterCb = document.getElementById('checkAllPenempatan');
        if (masterCb) masterCb.checked = checked;
        updateBulkUI();
    }

    function openBulkReassignModal() {
        const checkedBoxes = document.querySelectorAll('.penempatan-checkbox:checked');
        if (checkedBoxes.length === 0) {
            const msg = 'Pilih minimal satu penempatan peserta terlebih dahulu.';
            if (window.MagKomToast) {
                window.MagKomToast.warning(msg);
            } else {
                alert(msg);
            }
            return;
        }

        const container = document.getElementById('bulk-ids-container');
        if (container) {
            container.innerHTML = '';
            checkedBoxes.forEach(cb => {
                const hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = 'penempatan_ids[]';
                hidden.value = cb.value;
                container.appendChild(hidden);
            });
        }

        const countEl = document.getElementById('bulk-modal-count');
        if (countEl) countEl.innerText = checkedBoxes.length;

        const modal = document.getElementById('modal-bulk-reassign');
        if (modal) {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    }

    function closeBulkReassignModal() {
        const modal = document.getElementById('modal-bulk-reassign');
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }
    }

    // --- Export Popover ---
    function toggleAdmExportPopover(forceOpen = null) {
        const popover = document.getElementById('admExportPopover');
        const trigger = document.getElementById('btnToggleExportPopover');
        const wrap = trigger ? trigger.closest('.adm-export-dropdown-wrap') : null;
        if (!popover) return;

        const shouldOpen = forceOpen !== null ? forceOpen : popover.style.display === 'none';
        if (shouldOpen) {
            popover.style.display = 'flex';
            if (trigger) trigger.setAttribute('aria-expanded', 'true');
            if (wrap) wrap.classList.add('is-open');
        } else {
            popover.style.display = 'none';
            if (trigger) trigger.setAttribute('aria-expanded', 'false');
            if (wrap) wrap.classList.remove('is-open');
        }
    }

    function handleAdmExportFormatChange(format) {
        const cards = document.querySelectorAll('.adm-format-card');
        cards.forEach(c => c.classList.remove('adm-format-selected'));

        const radio = document.getElementById('radioFormat' + format.charAt(0).toUpperCase() + format.slice(1));
        if (radio) radio.checked = true;

        const card = document.getElementById('cardFormat' + format.charAt(0).toUpperCase() + format.slice(1));
        if (card) card.classList.add('adm-format-selected');

        const btnText = document.getElementById('btnExportDownloadText');
        if (btnText) {
            if (format === 'xlsx') {
                btnText.textContent = 'Unduh Excel (.xlsx)';
            } else if (format === 'csv') {
                btnText.textContent = 'Unduh CSV (.csv)';
            } else if (format === 'pdf') {
                btnText.textContent = 'Unduh PDF (.pdf)';
            }
        }
    }

    function triggerPenempatanExport(forcedFormat = null) {
        let format = forcedFormat;
        if (!format) {
            const selectedRadio = document.querySelector('input[name="popover_export_format"]:checked');
            format = selectedRadio ? selectedRadio.value : 'xlsx';
        }

        const popover = document.getElementById('admExportPopover');
        const exportBase = popover?.dataset.exportUrl || window.location.origin + '/admin/penempatan/ekspor-surat-diterima';

        const currentUrl = new URL(window.location.href);
        const exportUrl = new URL(exportBase, window.location.origin);

        currentUrl.searchParams.forEach((val, key) => {
            if (key !== 'page') {
                exportUrl.searchParams.set(key, val);
            }
        });

        exportUrl.searchParams.set('format', format);
        toggleAdmExportPopover(false);
        window.location.href = exportUrl.toString();
    }

    // Auto-open manual modal if validation errors exist
    const manualModal = document.getElementById('modal-penempatan-manual');
    if (manualModal && manualModal.dataset.autoOpen === 'true') {
        openManualModal();
        if (manualModal.dataset.autoMode === 'baru') {
            switchPesertaMode('baru');
        }
    }

    // --- Delegated Click Listeners ---
    document.addEventListener('click', (e) => {
        const target = e.target;

        // Clipboard Copy Credentials
        const copyBtn = target.closest('[data-action="copy-credentials"]');
        if (copyBtn) {
            e.preventDefault();
            const textToCopy = `Email: ${copyBtn.dataset.email}\nPassword: ${copyBtn.dataset.password}`;
            navigator.clipboard.writeText(textToCopy).then(() => {
                copyBtn.innerText = 'Tersalin!';
                setTimeout(() => {
                    copyBtn.innerText = 'Salin Kredensial';
                }, 2000);
            }).catch(err => {
                console.error('Failed to copy', err);
            });
            return;
        }

        // Export popover actions
        const toggleExpBtn = target.closest('#btnToggleExportPopover');
        if (toggleExpBtn) {
            e.preventDefault();
            e.stopPropagation();
            toggleAdmExportPopover();
            return;
        }

        const selectFmtBtn = target.closest('[data-action="select-export-format"]');
        if (selectFmtBtn) {
            handleAdmExportFormatChange(selectFmtBtn.dataset.format);
            return;
        }

        const cancelExpBtn = target.closest('[data-action="cancel-export-popover"]');
        if (cancelExpBtn) {
            e.preventDefault();
            toggleAdmExportPopover(false);
            return;
        }

        const doExpBtn = target.closest('[data-action="do-export-download"]');
        if (doExpBtn) {
            e.preventDefault();
            triggerPenempatanExport();
            return;
        }

        // Tabs
        const tabBtn = target.closest('[data-action="switch-tab"]');
        if (tabBtn) {
            e.preventDefault();
            switchPenempatanTab(tabBtn.dataset.tab);
            return;
        }

        // Manual penempatan modal
        const openManBtn = target.closest('[data-action="open-manual-modal"]');
        if (openManBtn) {
            e.preventDefault();
            openManualModal();
            return;
        }

        const closeManBtn = target.closest('[data-action="close-manual-modal"]');
        if (closeManBtn) {
            e.preventDefault();
            closeManualModal();
            return;
        }

        const switchModeBtn = target.closest('[data-action="switch-peserta-mode"]');
        if (switchModeBtn) {
            e.preventDefault();
            switchPesertaMode(switchModeBtn.dataset.mode);
            return;
        }

        // Modal tetapkan penempatan
        const openTetapkanBtn = target.closest('[data-action="open-tetapkan-modal"]');
        if (openTetapkanBtn) {
            e.preventDefault();
            openTetapkanModal(
                openTetapkanBtn.dataset.pendaftaranId,
                openTetapkanBtn.dataset.pesertaNama,
                openTetapkanBtn.dataset.jurusanNama,
                openTetapkanBtn.dataset.bidangId,
                openTetapkanBtn.dataset.bidangNama
            );
            return;
        }

        const closeTetapkanBtn = target.closest('[data-action="close-tetapkan-modal"]');
        if (closeTetapkanBtn) {
            e.preventDefault();
            closeTetapkanModal();
            return;
        }

        // Modal pindah pembimbing
        const openPindahBtn = target.closest('[data-action="open-pindah-modal"]');
        if (openPindahBtn) {
            e.preventDefault();
            openPindahModal(
                openPindahBtn.dataset.penempatanId,
                openPindahBtn.dataset.pesertaNama,
                openPindahBtn.dataset.pembimbingId
            );
            return;
        }

        const closePindahBtn = target.closest('[data-action="close-pindah-modal"]');
        if (closePindahBtn) {
            e.preventDefault();
            closePindahModal();
            return;
        }

        // Modal batal penempatan
        const openBatalBtn = target.closest('[data-action="open-batal-modal"]');
        if (openBatalBtn) {
            e.preventDefault();
            openBatalModal(
                openBatalBtn.dataset.penempatanId,
                openBatalBtn.dataset.pesertaNama
            );
            return;
        }

        const closeBatalBtn = target.closest('[data-action="close-batal-modal"]');
        if (closeBatalBtn) {
            e.preventDefault();
            closeBatalModal();
            return;
        }

        // Surat preview modal
        const openSuratBtn = target.closest('[data-action="open-surat-preview"]');
        if (openSuratBtn) {
            e.preventDefault();
            openSuratPreview(
                openSuratBtn.dataset.previewUrl,
                openSuratBtn.dataset.downloadUrl,
                openSuratBtn.dataset.title || 'Pratinjau Surat'
            );
            return;
        }

        const closeSuratBtn = target.closest('[data-action="close-surat-preview"]');
        if (closeSuratBtn) {
            e.preventDefault();
            closeSuratPreview();
            return;
        }

        // Bulk reassign
        const openBulkBtn = target.closest('[data-action="open-bulk-reassign"]');
        if (openBulkBtn) {
            e.preventDefault();
            openBulkReassignModal();
            return;
        }

        const cancelBulkBtn = target.closest('[data-action="cancel-bulk-selection"]');
        if (cancelBulkBtn) {
            e.preventDefault();
            toggleAllPenempatan(false);
            return;
        }

        const closeBulkBtn = target.closest('[data-action="close-bulk-reassign"]');
        if (closeBulkBtn) {
            e.preventDefault();
            closeBulkReassignModal();
            return;
        }

        // Click outside popover to close
        const popover = document.getElementById('admExportPopover');
        const trigger = document.getElementById('btnToggleExportPopover');
        if (popover && popover.style.display !== 'none') {
            if (!popover.contains(target) && trigger && !trigger.contains(target)) {
                toggleAdmExportPopover(false);
            }
        }
    });

    // Delegated Change Listeners
    document.addEventListener('change', (e) => {
        if (e.target.id === 'checkAllPenempatan') {
            toggleAllPenempatan(e.target.checked);
        } else if (e.target.classList.contains('penempatan-checkbox')) {
            updateBulkUI();
        } else if (e.target.id === 'select-durasi-bulan' || e.target.id === 'input-tgl-mulai') {
            syncManualDates();
        }
    });

    // Keyboard & Backdrop
    window.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeSuratPreview();
            closeManualModal();
            closePindahModal();
            closeBatalModal();
            closeTetapkanModal();
            closeBulkReassignModal();
            toggleAdmExportPopover(false);
        }
    });

    document.querySelectorAll('.modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', function (e) {
            if (e.target === this) {
                closeSuratPreview();
                closeManualModal();
                closePindahModal();
                closeBatalModal();
                closeTetapkanModal();
                closeBulkReassignModal();
            }
        });
    });
});
