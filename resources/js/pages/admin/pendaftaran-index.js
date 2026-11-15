/**
 * Admin Pendaftaran Index Module
 * CSP Compliant (no inline scripts or inline event handlers)
 */

document.addEventListener('DOMContentLoaded', () => {
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
            if (key !== 'page') {
                exportUrl.searchParams.set(key, val);
            }
        });

        exportUrl.searchParams.set('format', format);
        toggleAdmExportPopoverPendaftaran(false);
        window.location.href = exportUrl.toString();
    }

    document.addEventListener('click', (e) => {
        const target = e.target;

        const toggleBtn = target.closest('#btnToggleExportPopoverPendaftaran');
        if (toggleBtn) {
            e.preventDefault();
            e.stopPropagation();
            toggleAdmExportPopoverPendaftaran();
            return;
        }

        const selectFmt = target.closest('[data-action="select-pendaftaran-export-format"]');
        if (selectFmt) {
            handleAdmExportPendaftaranFormatChange(selectFmt.dataset.format);
            return;
        }

        const cancelBtn = target.closest('[data-action="cancel-pendaftaran-export"]');
        if (cancelBtn) {
            e.preventDefault();
            toggleAdmExportPopoverPendaftaran(false);
            return;
        }

        const doExportBtn = target.closest('[data-action="do-pendaftaran-export"]');
        if (doExportBtn) {
            e.preventDefault();
            triggerPendaftaranExport();
            return;
        }

        const popover = document.getElementById('admExportPopoverPendaftaran');
        const trigger = document.getElementById('btnToggleExportPopoverPendaftaran');
        if (popover && popover.style.display !== 'none') {
            if (!popover.contains(target) && trigger && !trigger.contains(target)) {
                toggleAdmExportPopoverPendaftaran(false);
            }
        }
    });

    window.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            toggleAdmExportPopoverPendaftaran(false);
        }
    });
});
