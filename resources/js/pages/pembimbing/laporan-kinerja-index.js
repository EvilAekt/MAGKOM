/**
 * Laporan Peserta Bimbingan - Portal Pembimbing MagKom
 * Modul Popover Ekspor Rekap Cepat (Excel, CSV, PDF)
 */

import { initScrollCraft } from '../../services/scroll-craft.js';

document.addEventListener('DOMContentLoaded', () => {
    initScrollCraft();
    initLaporanExportPopover();
});

function initLaporanExportPopover() {
    const triggerBtn = document.getElementById('btnToggleExportPopover');
    const popover = document.getElementById('admExportPopover');
    const cancelBtn = document.getElementById('btnCancelExportPopover');
    const downloadBtn = document.getElementById('btnDoExportDownload');
    const dropdownWrap = document.getElementById('admExportDropdownWrap');

    if (!triggerBtn || !popover) return;

    let isOpen = false;

    function togglePopover(show = null) {
        isOpen = show !== null ? show : !isOpen;
        if (isOpen) {
            popover.style.display = 'flex';
            if (dropdownWrap) dropdownWrap.classList.add('is-open');
            triggerBtn.setAttribute('aria-expanded', 'true');
        } else {
            popover.style.display = 'none';
            if (dropdownWrap) dropdownWrap.classList.remove('is-open');
            triggerBtn.setAttribute('aria-expanded', 'false');
        }
    }

    // Toggle button click
    triggerBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        togglePopover();
    });

    // Cancel button click
    if (cancelBtn) {
        cancelBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            togglePopover(false);
        });
    }

    // Click outside to close
    document.addEventListener('click', (e) => {
        if (isOpen && !popover.contains(e.target) && !triggerBtn.contains(e.target)) {
            togglePopover(false);
        }
    });

    // Escape key to close
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && isOpen) {
            togglePopover(false);
            triggerBtn.focus();
        }
    });

    // Radio format selection cards
    const formatCards = document.querySelectorAll('.adm-format-card');
    formatCards.forEach((card) => {
        card.addEventListener('click', () => {
            const radio = card.querySelector('input[type="radio"]');
            if (radio) {
                radio.checked = true;
                updateFormatSelection(radio.value);
            }
        });
    });

    function updateFormatSelection(format) {
        formatCards.forEach((c) => {
            const r = c.querySelector('input[type="radio"]');
            if (r && r.value === format) {
                c.classList.add('adm-format-selected');
            } else {
                c.classList.remove('adm-format-selected');
            }
        });

        if (downloadBtn) {
            const downloadIcon = `<svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="margin-right: 0.35rem;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>`;
            if (format === 'xlsx') {
                downloadBtn.innerHTML = `${downloadIcon}<span>Unduh Excel (.xlsx)</span>`;
            } else if (format === 'csv') {
                downloadBtn.innerHTML = `${downloadIcon}<span>Unduh CSV (.csv)</span>`;
            } else if (format === 'pdf') {
                downloadBtn.innerHTML = `${downloadIcon}<span>Cetak Dokumen (.pdf)</span>`;
            }
        }
    }

    // Download action
    if (downloadBtn) {
        downloadBtn.addEventListener('click', () => {
            const selectedRadio = document.querySelector('input[name="popover_export_format"]:checked');
            const format = selectedRadio ? selectedRadio.value : 'xlsx';

            const exportBaseUrl = popover.getAttribute('data-export-url') || '';
            const printBaseUrl = popover.getAttribute('data-print-url') || '';

            togglePopover(false);

            if (format === 'pdf' && printBaseUrl) {
                window.open(printBaseUrl, '_blank');
                return;
            }

            if (exportBaseUrl) {
                const currentUrl = new URL(window.location.href);
                const exportUrl = new URL(exportBaseUrl, window.location.origin);

                // Copy active search params
                currentUrl.searchParams.forEach((val, key) => {
                    if (key !== 'page' && key !== 'per_page') {
                        exportUrl.searchParams.set(key, val);
                    }
                });

                exportUrl.searchParams.set('format', format);
                window.location.href = exportUrl.toString();
            }
        });
    }
}

