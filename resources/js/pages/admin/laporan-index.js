/**
 * Admin Laporan Index Module
 * CSP Compliant (no inline scripts or inline event handlers)
 */

document.addEventListener('DOMContentLoaded', () => {
    // --- Pendaftaran Popover ---
    function toggleAdmExportPopoverPendaftaran(forceOpen = null) {
        const popover = document.getElementById('admExportPopoverPendaftaran');
        const trigger = document.getElementById('btnToggleExportPopoverPendaftaran');
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

    function handleAdmExportPendaftaranFormatChange(format) {
        const cards = document.querySelectorAll('#admExportPopoverPendaftaran .adm-format-card');
        cards.forEach(c => c.classList.remove('adm-format-selected'));

        const radio = document.getElementById('radioFormatPendaftaran' + format.charAt(0).toUpperCase() + format.slice(1));
        if (radio) radio.checked = true;

        const card = document.getElementById('cardFormatPendaftaran' + format.charAt(0).toUpperCase() + format.slice(1));
        if (card) card.classList.add('adm-format-selected');

        const btnText = document.getElementById('btnExportDownloadTextPendaftaran');
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

    function triggerPendaftaranExport(forcedFormat = null) {
        let format = forcedFormat;
        if (!format) {
            const selectedRadio = document.querySelector('input[name="popover_export_pendaftaran_format"]:checked');
            format = selectedRadio ? selectedRadio.value : 'xlsx';
        }

        const popover = document.getElementById('admExportPopoverPendaftaran');
        const exportBase = popover?.dataset.exportUrl || window.location.origin + '/admin/laporan/pendaftaran';

        const currentUrl = new URL(window.location.href);
        const exportUrl = new URL(exportBase, window.location.origin);

        currentUrl.searchParams.forEach((val, key) => {
            if (key !== 'page' && key !== 'tab') {
                exportUrl.searchParams.set(key, val);
            }
        });

        exportUrl.searchParams.set('format', format);
        toggleAdmExportPopoverPendaftaran(false);
        window.location.href = exportUrl.toString();
    }

    // --- Penempatan Popover ---
    function toggleAdmExportPopoverPenempatan(forceOpen = null) {
        const popover = document.getElementById('admExportPopoverPenempatan');
        const trigger = document.getElementById('btnToggleExportPopoverPenempatan');
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

    function handleAdmExportPenempatanFormatChange(format) {
        const cards = document.querySelectorAll('#admExportPopoverPenempatan .adm-format-card');
        cards.forEach(c => c.classList.remove('adm-format-selected'));

        const radio = document.getElementById('radioFormatPenempatan' + format.charAt(0).toUpperCase() + format.slice(1));
        if (radio) radio.checked = true;

        const card = document.getElementById('cardFormatPenempatan' + format.charAt(0).toUpperCase() + format.slice(1));
        if (card) card.classList.add('adm-format-selected');

        const btnText = document.getElementById('btnExportDownloadTextPenempatan');
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
            const selectedRadio = document.querySelector('input[name="popover_export_penempatan_format"]:checked');
            format = selectedRadio ? selectedRadio.value : 'xlsx';
        }

        const popover = document.getElementById('admExportPopoverPenempatan');
        const exportBase = popover?.dataset.exportUrl || window.location.origin + '/admin/laporan/penempatan';

        const currentUrl = new URL(window.location.href);
        const exportUrl = new URL(exportBase, window.location.origin);

        currentUrl.searchParams.forEach((val, key) => {
            if (key !== 'page' && key !== 'tab') {
                exportUrl.searchParams.set(key, val);
            }
        });

        exportUrl.searchParams.set('format', format);
        toggleAdmExportPopoverPenempatan(false);
        window.location.href = exportUrl.toString();
    }

    // --- Delegated Event Listeners ---
    document.addEventListener('click', (e) => {
        const target = e.target;

        // Pendaftaran Popover
        const triggerPdt = target.closest('#btnToggleExportPopoverPendaftaran');
        if (triggerPdt) {
            e.preventDefault();
            e.stopPropagation();
            toggleAdmExportPopoverPendaftaran();
            return;
        }

        const selectFmtPdt = target.closest('[data-action="select-pendaftaran-export-format"]');
        if (selectFmtPdt) {
            handleAdmExportPendaftaranFormatChange(selectFmtPdt.dataset.format);
            return;
        }

        const cancelPdt = target.closest('[data-action="cancel-pendaftaran-export"]');
        if (cancelPdt) {
            e.preventDefault();
            toggleAdmExportPopoverPendaftaran(false);
            return;
        }

        const doExportPdt = target.closest('[data-action="do-pendaftaran-export"]');
        if (doExportPdt) {
            e.preventDefault();
            triggerPendaftaranExport();
            return;
        }

        // Penempatan Popover
        const triggerPen = target.closest('#btnToggleExportPopoverPenempatan');
        if (triggerPen) {
            e.preventDefault();
            e.stopPropagation();
            toggleAdmExportPopoverPenempatan();
            return;
        }

        const selectFmtPen = target.closest('[data-action="select-penempatan-export-format"]');
        if (selectFmtPen) {
            handleAdmExportPenempatanFormatChange(selectFmtPen.dataset.format);
            return;
        }

        const cancelPen = target.closest('[data-action="cancel-penempatan-export"]');
        if (cancelPen) {
            e.preventDefault();
            toggleAdmExportPopoverPenempatan(false);
            return;
        }

        const doExportPen = target.closest('[data-action="do-penempatan-export"]');
        if (doExportPen) {
            e.preventDefault();
            triggerPenempatanExport();
            return;
        }

        // Click outside popovers
        const popoverPdt = document.getElementById('admExportPopoverPendaftaran');
        const triggerPdtEl = document.getElementById('btnToggleExportPopoverPendaftaran');
        if (popoverPdt && popoverPdt.style.display !== 'none') {
            if (!popoverPdt.contains(target) && triggerPdtEl && !triggerPdtEl.contains(target)) {
                toggleAdmExportPopoverPendaftaran(false);
            }
        }

        const popoverPen = document.getElementById('admExportPopoverPenempatan');
        const triggerPenEl = document.getElementById('btnToggleExportPopoverPenempatan');
        if (popoverPen && popoverPen.style.display !== 'none') {
            if (!popoverPen.contains(target) && triggerPenEl && !triggerPenEl.contains(target)) {
                toggleAdmExportPopoverPenempatan(false);
            }
        }
    });

    window.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            toggleAdmExportPopoverPendaftaran(false);
            toggleAdmExportPopoverPenempatan(false);
        }
    });
});
