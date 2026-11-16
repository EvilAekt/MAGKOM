/**
 * Admin Pendaftaran Show Module
 * CSP Compliant (no inline scripts or inline event handlers)
 */

document.addEventListener('DOMContentLoaded', () => {
    // --- Keputusan Tab Switching ---
    function switchActionTab(action) {
        const input = document.getElementById('keputusan-input');
        if (input) input.value = action;

        const panels = ['terima', 'perlu_perbaikan', 'tolak'];
        panels.forEach(p => {
            const panel = document.getElementById('action-panel-' + p);
            const btn = document.getElementById('tab-btn-' + p);
            if (!panel || !btn) return;

            if (p === action) {
                panel.style.display = 'flex';
                btn.className = 'adm-tab-pill active-' + p;
            } else {
                panel.style.display = 'none';
                btn.className = 'adm-tab-pill';
            }
        });

        const catatanTerima = document.getElementById('catatan_terima');
        if (catatanTerima) {
            catatanTerima.disabled = (action !== 'terima');
        }

        const catatanPerbaikan = document.getElementById('catatan_perbaikan');
        if (catatanPerbaikan) {
            catatanPerbaikan.disabled = (action !== 'perlu_perbaikan');
            catatanPerbaikan.required = (action === 'perlu_perbaikan');
        }

        const catatanTolak = document.getElementById('catatan_tolak');
        if (catatanTolak) {
            catatanTolak.disabled = (action !== 'tolak');
            catatanTolak.required = (action === 'tolak');
        }
    }

    // Inisialisasi awal tab aksi (default: terima)
    switchActionTab('terima');

    // --- Document Preview Modal ---
    let previewTimer = null;

    function handlePreviewLoaded() {
        if (previewTimer) {
            clearTimeout(previewTimer);
            previewTimer = null;
        }
        const loading = document.getElementById('previewLoading');
        if (loading) loading.style.display = 'none';
    }

    function handlePreviewError() {
        if (previewTimer) {
            clearTimeout(previewTimer);
            previewTimer = null;
        }
        const loading = document.getElementById('previewLoading');
        const iframe = document.getElementById('previewIframe');
        const imgWrapper = document.getElementById('previewImageWrapper');
        const errBox = document.getElementById('previewError');

        if (loading) loading.style.display = 'none';
        if (iframe) iframe.style.display = 'none';
        if (imgWrapper) imgWrapper.style.display = 'none';
        if (errBox) errBox.style.display = 'flex';
    }

    function openDocPreview(previewUrl, downloadUrl, title, mime) {
        const modal = document.getElementById('docPreviewModal');
        const modalTitle = document.getElementById('previewModalTitle');
        const modalBadge = document.getElementById('previewModalBadge');
        const openNewTabBtn = document.getElementById('previewOpenNewTabBtn');
        const downloadBtn = document.getElementById('previewDownloadBtn');
        const fallbackBtn = document.getElementById('previewFallbackDownloadBtn');
        const loading = document.getElementById('previewLoading');
        const iframe = document.getElementById('previewIframe');
        const imgWrapper = document.getElementById('previewImageWrapper');
        const img = document.getElementById('previewImage');
        const errBox = document.getElementById('previewError');
        const iconContainer = document.getElementById('previewModalIcon');

        if (!modal) return;

        if (previewTimer) {
            clearTimeout(previewTimer);
            previewTimer = null;
        }

        loading.style.display = 'flex';
        iframe.style.display = 'none';
        iframe.onload = null;
        iframe.src = 'about:blank';
        imgWrapper.style.display = 'none';
        if (img) {
            img.onload = null;
            img.onerror = null;
            img.removeAttribute('src');
        }
        errBox.style.display = 'none';

        modalTitle.textContent = title || 'Pratinjau Dokumen';
        openNewTabBtn.href = previewUrl;
        downloadBtn.href = downloadUrl;
        if (fallbackBtn) fallbackBtn.href = downloadUrl;

        const isPdf = (mime && mime.toLowerCase().includes('pdf')) || (!mime && previewUrl.toLowerCase().includes('pdf'));

        if (isPdf) {
            modalBadge.textContent = 'PDF';
            modalBadge.style.background = '#FEE2E2';
            modalBadge.style.color = '#991B1B';
            if (iconContainer) {
                iconContainer.style.background = '#FEE2E2';
                iconContainer.style.color = '#DC2626';
            }
            const finalPdfUrl = previewUrl.includes('#') ? previewUrl : (previewUrl + '#toolbar=0&navpanes=0');
            iframe.onload = function () {
                handlePreviewLoaded();
            };
            iframe.style.display = 'block';
            iframe.src = finalPdfUrl;

            previewTimer = setTimeout(function () {
                handlePreviewLoaded();
            }, 600);
        } else {
            modalBadge.textContent = 'Gambar';
            modalBadge.style.background = 'var(--blue-tint, #EFF6FF)';
            modalBadge.style.color = '#0641CC';
            if (iconContainer) {
                iconContainer.style.background = 'var(--blue-tint, #EFF6FF)';
                iconContainer.style.color = '#0641CC';
            }
            img.onload = function () {
                handlePreviewLoaded();
            };
            img.onerror = function () {
                handlePreviewError();
            };
            img.src = previewUrl;
            imgWrapper.style.display = 'flex';

            previewTimer = setTimeout(function () {
                handlePreviewLoaded();
            }, 700);
        }

        modal.classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function closeDocPreview() {
        if (previewTimer) {
            clearTimeout(previewTimer);
            previewTimer = null;
        }
        const modal = document.getElementById('docPreviewModal');
        const iframe = document.getElementById('previewIframe');
        const img = document.getElementById('previewImage');

        if (modal) {
            modal.classList.remove('open');
            document.body.style.overflow = '';
        }
        if (iframe) {
            iframe.onload = null;
            iframe.src = 'about:blank';
        }
        if (img) {
            img.onload = null;
            img.onerror = null;
            img.removeAttribute('src');
        }
    }

    // --- Delegated Event Listeners ---
    document.addEventListener('click', (e) => {
        const target = e.target;

        // Switch Keputusan Tab
        const actionTabBtn = target.closest('[data-action="switch-action-tab"]');
        if (actionTabBtn) {
            e.preventDefault();
            switchActionTab(actionTabBtn.dataset.tab);
            return;
        }

        // Open Doc Preview
        const docBtn = target.closest('[data-action="open-doc-preview"]');
        if (docBtn) {
            e.preventDefault();
            openDocPreview(
                docBtn.dataset.previewUrl,
                docBtn.dataset.downloadUrl,
                docBtn.dataset.title,
                docBtn.dataset.mime
            );
            return;
        }

        // Close Doc Preview
        const closeDocBtn = target.closest('[data-action="close-doc-preview"]');
        if (closeDocBtn) {
            e.preventDefault();
            closeDocPreview();
            return;
        }

        const modal = document.getElementById('docPreviewModal');
        if (target === modal) {
            closeDocPreview();
            return;
        }
    });

    // Form confirmation delegation
    document.addEventListener('submit', (e) => {
        const submitter = e.submitter;
        if (submitter && submitter.hasAttribute('data-confirm')) {
            const message = submitter.getAttribute('data-confirm');
            if (!confirm(message)) {
                e.preventDefault();
            }
        }
    });

    window.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeDocPreview();
        }
    });
});
