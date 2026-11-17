/**
 * Admin Pendaftaran Show Module
 * CSP Compliant (no inline scripts or inline event handlers)
 */

document.addEventListener('DOMContentLoaded', () => {
    // --- Keputusan Tab Switching ---
    function switchActionTab(action) {
        const input = document.getElementById('keputusan-input');
        if (input) input.value = action;

        const panels = ['terima', 'tolak'];
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

        const catatanTolak = document.getElementById('catatan_tolak');
        if (catatanTolak) {
            catatanTolak.disabled = (action !== 'tolak');
            catatanTolak.required = (action === 'tolak');
        }
    }

    // Inisialisasi awal tab aksi (default: terima)
    switchActionTab('terima');

    // --- Helper Animasi Material Floating Label untuk Field Catatan (Paritas Pembimbing) ---
    function initFloatingBox(textareaId, boxId) {
        const textarea = document.getElementById(textareaId);
        const box = document.getElementById(boxId) || textarea?.closest('.adm-floating-field');
        if (!textarea || !box) return;

        function updateState() {
            const hasValue = textarea.value.trim().length > 0;
            box.classList.toggle('has-value', hasValue);
        }

        textarea.addEventListener('focus', () => {
            box.classList.add('is-focused');
            box.classList.remove('has-error');
        });

        textarea.addEventListener('blur', () => {
            box.classList.remove('is-focused');
            updateState();
        });

        textarea.addEventListener('input', () => {
            updateState();
            const minLength = textarea.id === 'catatan_tolak' ? 10 : 1;
            if (textarea.value.trim().length >= minLength) {
                box.classList.remove('has-error');
            }
        });

        // Inisialisasi awal (jika terdapat old value dari server)
        updateState();
    }

    // Inisialisasi floating box untuk kedua catatan keputusan
    initFloatingBox('catatan_terima', 'box_catatan_terima');
    initFloatingBox('catatan_tolak', 'box_catatan_tolak');

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

        // Close Decision Confirm Modal
        if (target.closest('#confirmModalCloseBtn') || target.closest('#confirmModalCancelBtn')) {
            e.preventDefault();
            closeConfirmModal();
            return;
        }

        if (target === modalConfirm) {
            closeConfirmModal();
            return;
        }
    });

    // --- Decision Confirmation Modal Management ---
    const modalConfirm = document.getElementById('modalKonfirmasiKeputusanAdmin');
    const iconWrap = document.getElementById('confirmModalIconWrap');
    const iconSvg = document.getElementById('confirmModalIconSvg');
    const titleEl = document.getElementById('confirmModalTitle');
    const subtitleEl = document.getElementById('confirmModalSubtitle');
    const calloutEl = document.getElementById('confirmModalCallout');
    const questionEl = document.getElementById('confirmModalQuestion');
    const consequenceEl = document.getElementById('confirmModalConsequence');
    const executeBtn = document.getElementById('confirmModalExecuteBtn');
    const executeText = document.getElementById('confirmModalExecuteText');
    const decisionForm = document.getElementById('decision-form');
    let isConfirmedByModal = false;

    function setModalVariant(variant) {
        const variants = ['variant-primary', 'variant-success', 'variant-warning', 'variant-danger'];
        variants.forEach(v => {
            iconWrap?.classList.remove(v);
            calloutEl?.classList.remove(v);
            executeBtn?.classList.remove(v);
        });

        const targetClass = 'variant-' + variant;
        iconWrap?.classList.add(targetClass);
        calloutEl?.classList.add(targetClass);
        executeBtn?.classList.add(targetClass);
    }

    function openConfirmModal(action) {
        if (!modalConfirm) return;

        if (action === 'tolak') {
            setModalVariant('danger');
            if (titleEl) titleEl.textContent = 'Konfirmasi Penolakan Pendaftaran';
            if (subtitleEl) subtitleEl.textContent = 'Keputusan Final Verifikasi Dokumen';
            if (questionEl) questionEl.textContent = 'Apakah Anda yakin MENOLAK pengajuan pendaftaran ini?';
            if (consequenceEl) {
                consequenceEl.textContent = '• Pengajuan pendaftaran akan ditolak secara permanen dan calon peserta tidak dapat melanjutkan proses seleksi periode ini.';
            }
            if (executeText) executeText.textContent = 'Ya, Tolak Pengajuan';
            if (iconSvg) {
                iconSvg.innerHTML = '<path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line>';
            }
        } else {
            setModalVariant('success');
            if (titleEl) titleEl.textContent = 'Konfirmasi Penerimaan Berkas';
            if (subtitleEl) subtitleEl.textContent = 'Verifikasi Dokumen Calon Peserta Magang';
            if (questionEl) questionEl.textContent = 'Apakah Anda yakin menerima berkas calon peserta ini?';
            if (consequenceEl) {
                consequenceEl.textContent = '• Status pendaftaran akan berubah menjadi Diterima dan kuota bidang akan dialokasikan untuk penempatan.';
            }
            if (executeText) executeText.textContent = 'Ya, Terima Berkas';
            if (iconSvg) {
                iconSvg.innerHTML = '<polyline points="20 6 9 17 4 12"></polyline>';
            }
        }

        modalConfirm.style.display = 'flex';
        requestAnimationFrame(() => {
            modalConfirm.classList.add('is-open');
        });
        document.body.style.overflow = 'hidden';
    }

    function closeConfirmModal() {
        if (!modalConfirm) return;
        modalConfirm.classList.remove('is-open');
        setTimeout(() => {
            if (!modalConfirm.classList.contains('is-open')) {
                modalConfirm.style.display = 'none';
                document.body.style.overflow = '';
            }
        }, 200);
    }

    // Eksekusi Submit Saat Konfirmasi Disetujui
    if (executeBtn && decisionForm) {
        executeBtn.addEventListener('click', () => {
            isConfirmedByModal = true;
            closeConfirmModal();
            if (typeof decisionForm.requestSubmit === 'function') {
                decisionForm.requestSubmit();
            } else {
                decisionForm.submit();
            }
        });
    }

    // Intersepsi Form Keputusan
    if (decisionForm) {
        decisionForm.addEventListener('submit', (e) => {
            if (isConfirmedByModal) {
                isConfirmedByModal = false;
                return;
            }

            e.preventDefault();

            const action = document.getElementById('keputusan-input')?.value || 'terima';

            if (action === 'tolak') {
                const catatanTolak = document.getElementById('catatan_tolak');
                const boxTolak = document.getElementById('box_catatan_tolak');
                if (!catatanTolak || catatanTolak.value.trim().length < 10) {
                    if (boxTolak) {
                        boxTolak.classList.add('has-error');
                    }
                    if (catatanTolak) {
                        catatanTolak.focus();
                        catatanTolak.reportValidity();
                    }
                    return;
                }
            }

            openConfirmModal(action);
        });
    }

    window.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeDocPreview();
            closeConfirmModal();
        }
    });
});
