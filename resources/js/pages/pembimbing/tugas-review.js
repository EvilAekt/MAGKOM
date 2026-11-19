/**
 * MAGKOM - Portal Pembimbing: Tugas Review & Evaluation Workspace
 * Kontrol Aksi Terpadu (Tandai Selesai & Kembalikan), Validasi Umpan Balik,
 * serta Navigasi Mentee Cepat.
 */

export function initPembimbingTugasReview() {
    // 1. Sidebar Collapse & Restore Toggle (TASK-04)
    const btnToggleSidebar = document.getElementById('btnToggleSidebar');
    const btnRestoreSidebar = document.getElementById('btnRestoreSidebar');
    const bodyLayout = document.querySelector('.review-body-layout');

    if (btnToggleSidebar && bodyLayout) {
        btnToggleSidebar.addEventListener('click', () => {
            bodyLayout.classList.add('is-collapsed');
            btnToggleSidebar.setAttribute('aria-expanded', 'false');
        });
    }

    if (btnRestoreSidebar && bodyLayout) {
        btnRestoreSidebar.addEventListener('click', () => {
            bodyLayout.classList.remove('is-collapsed');
            if (btnToggleSidebar) {
                btnToggleSidebar.setAttribute('aria-expanded', 'true');
            }
        });
    }

    // 2. Split Button Evaluasi: Tandai Selesai + Sub-Tombol Kembalikan Tugas
    const formTandaiSelesai = document.getElementById('formTandaiSelesai');
    const btnSubmitSelesaiDirect = document.getElementById('btnSubmitSelesaiDirect');
    const btnToggleReviewSplitMenu = document.getElementById('btnToggleReviewSplitMenu');
    const reviewSplitMenu = document.getElementById('reviewSplitMenu');
    const menuOptTandaiSelesai = document.getElementById('menuOptTandaiSelesai');
    const menuOptKembalikanTugas = document.getElementById('menuOptKembalikanTugas');

    const formActionKembalikan = document.getElementById('formActionKembalikan');
    const feedbackInput = document.getElementById('feedbackInput');
    const hiddenFeedbackKembalikan = document.getElementById('hiddenFeedbackKembalikan');
    const feedbackFieldBox = document.getElementById('feedbackFieldBox') || feedbackInput?.closest('.review-feedback-field');

    // Floating Label Box Interactivity (Google Classroom Style)
    function updateFeedbackFloatingState() {
        if (!feedbackInput || !feedbackFieldBox) return;
        const hasVal = feedbackInput.value.trim().length > 0;
        feedbackFieldBox.classList.toggle('has-value', hasVal);
    }

    if (feedbackInput && feedbackFieldBox) {
        feedbackInput.addEventListener('focus', () => {
            feedbackFieldBox.classList.add('is-focused');
            feedbackFieldBox.classList.remove('has-error');
        });

        feedbackInput.addEventListener('blur', () => {
            feedbackFieldBox.classList.remove('is-focused');
            updateFeedbackFloatingState();
        });

        feedbackInput.addEventListener('input', () => {
            updateFeedbackFloatingState();
            if (feedbackInput.value.trim().length >= 5) {
                feedbackFieldBox.classList.remove('has-error');
            }
        });

        updateFeedbackFloatingState();
    }

    // Aksi Langsung Tandai Selesai (Tombol Utama & Menu Item)
    const triggerTandaiSelesai = () => {
        if (!formTandaiSelesai) return;
        openReviewModal('selesai');
    };

    if (btnSubmitSelesaiDirect) {
        btnSubmitSelesaiDirect.addEventListener('click', triggerTandaiSelesai);
    }
    if (menuOptTandaiSelesai) {
        menuOptTandaiSelesai.addEventListener('click', () => {
            if (reviewSplitMenu) reviewSplitMenu.classList.remove('is-open');
            triggerTandaiSelesai();
        });
    }

    // Toggle Dropdown Menu Split Button
    if (btnToggleReviewSplitMenu && reviewSplitMenu) {
        btnToggleReviewSplitMenu.addEventListener('click', (e) => {
            e.stopPropagation();
            const isOpen = reviewSplitMenu.classList.contains('is-open');
            if (isOpen) {
                reviewSplitMenu.classList.remove('is-open');
                btnToggleReviewSplitMenu.setAttribute('aria-expanded', 'false');
            } else {
                reviewSplitMenu.classList.add('is-open');
                btnToggleReviewSplitMenu.setAttribute('aria-expanded', 'true');
            }
        });

        document.addEventListener('click', (e) => {
            if (!reviewSplitMenu.contains(e.target) && !btnToggleReviewSplitMenu.contains(e.target)) {
                reviewSplitMenu.classList.remove('is-open');
                btnToggleReviewSplitMenu.setAttribute('aria-expanded', 'false');
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && reviewSplitMenu.classList.contains('is-open')) {
                reviewSplitMenu.classList.remove('is-open');
                btnToggleReviewSplitMenu.setAttribute('aria-expanded', 'false');
                btnToggleReviewSplitMenu.focus();
            }
        });
    }

    // Modal Konfirmasi Review Tugas (Eliminasi alert/confirm native browser)
    const modalReview = document.getElementById('modalKonfirmasiReviewTugas');
    const modalIconWrap = document.getElementById('reviewModalIconWrap');
    const modalIconSvg = document.getElementById('reviewModalIconSvg');
    const modalTitle = document.getElementById('reviewModalTitle');
    const modalSubtitle = document.getElementById('reviewModalSubtitle');
    const modalAvatar = document.getElementById('reviewModalAvatar');
    const modalCallout = document.getElementById('reviewModalCallout');
    const modalImpactIcon = document.getElementById('reviewModalImpactIcon');
    const modalQuestion = document.getElementById('reviewModalQuestion');
    const modalFeedbackBox = document.getElementById('reviewModalFeedbackBox');
    const modalFeedbackText = document.getElementById('reviewModalFeedbackText');
    const modalConsequence = document.getElementById('reviewModalConsequence');
    const modalCancelBtn = document.getElementById('reviewModalCancelBtn');
    const modalCloseBtn = document.getElementById('reviewModalCloseBtn');
    const modalConfirmBtn = document.getElementById('reviewModalConfirmBtn');
    const modalConfirmText = document.getElementById('reviewModalConfirmText');
    let activeReviewAction = null; // 'kembalikan' | 'selesai' | 'submitting'

    function setModalVariant(variant) {
        const variants = ['variant-danger', 'variant-warning', 'variant-success', 'variant-primary'];
        variants.forEach(v => {
            modalIconWrap?.classList.remove(v);
            modalCallout?.classList.remove(v);
            modalConfirmBtn?.classList.remove(v);
            modalAvatar?.classList.remove(v);
        });

        const targetClass = 'variant-' + variant;
        modalIconWrap?.classList.add(targetClass);
        modalCallout?.classList.add(targetClass);
        modalConfirmBtn?.classList.add(targetClass);
        modalAvatar?.classList.add(targetClass);
    }

    function openReviewModal(action, feedbackVal = '') {
        if (!modalReview) return;
        activeReviewAction = action;

        if (action === 'kembalikan') {
            setModalVariant('danger');
            if (modalTitle) modalTitle.textContent = 'Konfirmasi Pengembalian Tugas';
            if (modalSubtitle) modalSubtitle.textContent = 'Evaluasi Hasil Penugasan Peserta';
            if (modalQuestion) modalQuestion.textContent = 'Apakah Anda yakin ingin mengembalikan tugas ini kepada peserta untuk diperbaiki?';
            if (modalConsequence) {
                modalConsequence.textContent = 'Status tugas akan diubah menjadi Perlu Perbaikan dan peserta wajib mengirimkan revisi perbaikan.';
            }
            if (modalImpactIcon) {
                modalImpactIcon.innerHTML = '<circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line>';
            }
            if (modalConfirmText) modalConfirmText.textContent = 'Ya, Kembalikan Tugas';
            if (modalIconSvg) {
                modalIconSvg.innerHTML = '<polyline points="1 4 1 10 7 10"></polyline><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path>';
            }
            if (feedbackVal) {
                if (modalFeedbackText) modalFeedbackText.textContent = feedbackVal;
                if (modalFeedbackBox) modalFeedbackBox.style.display = 'block';
            } else {
                if (modalFeedbackBox) modalFeedbackBox.style.display = 'none';
            }
        } else if (action === 'selesai') {
            setModalVariant('primary');
            if (modalTitle) modalTitle.textContent = 'Konfirmasi Penyelesaian Tugas';
            if (modalSubtitle) modalSubtitle.textContent = 'Persetujuan Hasil Penugasan Peserta';
            if (modalQuestion) modalQuestion.textContent = 'Apakah Anda yakin seluruh hasil kerja tugas ini telah tuntas dan disetujui?';
            if (modalConsequence) {
                modalConsequence.textContent = 'Tugas ini akan ditandai tuntas selesai dan tidak dapat diubah kembali menjadi draft atau perlu perbaikan.';
            }
            if (modalImpactIcon) {
                modalImpactIcon.innerHTML = '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline>';
            }
            if (modalConfirmText) modalConfirmText.textContent = 'Ya, Tandai Selesai';
            if (modalIconSvg) {
                modalIconSvg.innerHTML = '<polyline points="20 6 9 17 4 12"></polyline>';
            }
            if (modalFeedbackBox) modalFeedbackBox.style.display = 'none';
        }

        modalReview.style.display = 'flex';
        requestAnimationFrame(() => {
            modalReview.classList.add('is-open');
        });
        document.body.style.overflow = 'hidden';
        if (modalConfirmBtn) modalConfirmBtn.focus();
    }

    function closeReviewModal() {
        if (!modalReview) return;
        modalReview.classList.remove('is-open');
        setTimeout(() => {
            if (!modalReview.classList.contains('is-open')) {
                modalReview.style.display = 'none';
                document.body.style.overflow = '';
                if (activeReviewAction !== 'submitting') {
                    activeReviewAction = null;
                }
            }
        }, 200);
    }

    if (modalCancelBtn) modalCancelBtn.addEventListener('click', closeReviewModal);
    if (modalCloseBtn) modalCloseBtn.addEventListener('click', closeReviewModal);
    if (modalReview) {
        modalReview.addEventListener('click', (e) => {
            if (e.target === modalReview) {
                closeReviewModal();
            }
        });
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && modalReview && modalReview.classList.contains('is-open')) {
            closeReviewModal();
        }
    });

    if (modalConfirmBtn) {
        modalConfirmBtn.addEventListener('click', () => {
            if (activeReviewAction === 'kembalikan') {
                if (formActionKembalikan && feedbackInput && hiddenFeedbackKembalikan) {
                    hiddenFeedbackKembalikan.value = feedbackInput.value.trim();
                    activeReviewAction = 'submitting';
                    closeReviewModal();
                    formActionKembalikan.submit();
                }
            } else if (activeReviewAction === 'selesai') {
                if (formTandaiSelesai) {
                    activeReviewAction = 'submitting';
                    closeReviewModal();
                    formTandaiSelesai.submit();
                }
            }
        });
    }

    // Aksi Sub-Tombol Kembalikan Tugas dari Dropdown Menu
    if (menuOptKembalikanTugas && formActionKembalikan && feedbackInput && hiddenFeedbackKembalikan) {
        menuOptKembalikanTugas.addEventListener('click', () => {
            if (reviewSplitMenu) {
                reviewSplitMenu.classList.remove('is-open');
                if (btnToggleReviewSplitMenu) {
                    btnToggleReviewSplitMenu.setAttribute('aria-expanded', 'false');
                }
            }

            // Jika panel samping evaluasi sedang collapsed, buka kembali
            if (bodyLayout && bodyLayout.classList.contains('is-collapsed')) {
                bodyLayout.classList.remove('is-collapsed');
                if (btnToggleSidebar) {
                    btnToggleSidebar.setAttribute('aria-expanded', 'true');
                }
            }

            const feedbackVal = feedbackInput.value.trim();
            if (feedbackVal.length < 5) {
                if (feedbackFieldBox) {
                    feedbackFieldBox.classList.add('has-error');
                }
                feedbackInput.focus();
                feedbackInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                return;
            }

            openReviewModal('kembalikan', feedbackVal);
        });
    }

    // Validasi Submit Form Kembalikan dari Tombol Sidebar (Jika diklik dari sidebar)
    if (formActionKembalikan && feedbackInput && hiddenFeedbackKembalikan) {
        formActionKembalikan.addEventListener('submit', (e) => {
            if (activeReviewAction === 'submitting') {
                return;
            }
            e.preventDefault();
            const feedbackVal = feedbackInput.value.trim();
            if (feedbackVal.length < 5) {
                if (feedbackFieldBox) {
                    feedbackFieldBox.classList.add('has-error');
                }
                feedbackInput.focus();
                feedbackInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                return false;
            }

            openReviewModal('kembalikan', feedbackVal);
        });
    }

    // 3. Keyboard Shortcut Navigasi Mentee Cepat (< dan > atau [ dan ])
    const prevMenteeLink = document.getElementById('prevMenteeLink');
    const nextMenteeLink = document.getElementById('nextMenteeLink');

    document.addEventListener('keydown', (e) => {
        // Jangan picu navigasi jika pengguna sedang mengetik di textarea atau input
        if (['INPUT', 'TEXTAREA', 'SELECT'].includes(document.activeElement.tagName)) {
            return;
        }

        if ((e.altKey && e.key === 'ArrowLeft') || e.key === '[') {
            if (prevMenteeLink && !prevMenteeLink.classList.contains('disabled')) {
                e.preventDefault();
                prevMenteeLink.click();
            }
        } else if ((e.altKey && e.key === 'ArrowRight') || e.key === ']') {
            if (nextMenteeLink && !nextMenteeLink.classList.contains('disabled')) {
                e.preventDefault();
                nextMenteeLink.click();
            }
        }
    });

    // 4. Mentee / Sibling Tugas Dropdown Selector
    const btnMenteeDropdown = document.getElementById('btnMenteeDropdown');
    const menteeDropdownMenu = document.getElementById('menteeDropdownMenu');

    if (btnMenteeDropdown && menteeDropdownMenu) {
        btnMenteeDropdown.addEventListener('click', (e) => {
            e.stopPropagation();
            const isOpen = menteeDropdownMenu.classList.contains('show');
            if (isOpen) {
                menteeDropdownMenu.classList.remove('show');
                btnMenteeDropdown.setAttribute('aria-expanded', 'false');
            } else {
                menteeDropdownMenu.classList.add('show');
                btnMenteeDropdown.setAttribute('aria-expanded', 'true');
            }
        });

        document.addEventListener('click', (e) => {
            if (!menteeDropdownMenu.contains(e.target) && !btnMenteeDropdown.contains(e.target)) {
                menteeDropdownMenu.classList.remove('show');
                btnMenteeDropdown.setAttribute('aria-expanded', 'false');
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && menteeDropdownMenu.classList.contains('show')) {
                menteeDropdownMenu.classList.remove('show');
                btnMenteeDropdown.setAttribute('aria-expanded', 'false');
                btnMenteeDropdown.focus();
            }
        });
    }
}

// Inisialisasi saat DOM siap
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initPembimbingTugasReview);
} else {
    initPembimbingTugasReview();
}
