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
        if (confirm('Apakah Anda yakin seluruh hasil kerja tugas ini telah tuntas dan disetujui?')) {
            formTandaiSelesai.submit();
        }
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
                alert('Catatan umpan balik wajib diisi minimal 5 karakter ketika mengembalikan tugas kepada peserta untuk diperbaiki.');
                feedbackInput.focus();
                feedbackInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                return;
            }

            if (confirm('Apakah Anda yakin ingin mengembalikan tugas ini kepada peserta untuk diperbaiki?')) {
                hiddenFeedbackKembalikan.value = feedbackVal;
                formActionKembalikan.submit();
            }
        });
    }

    // Validasi Submit Form Kembalikan dari Tombol Sidebar (Jika diklik dari sidebar)
    if (formActionKembalikan && feedbackInput && hiddenFeedbackKembalikan) {
        formActionKembalikan.addEventListener('submit', (e) => {
            const feedbackVal = feedbackInput.value.trim();
            if (feedbackVal.length < 5) {
                e.preventDefault();
                if (feedbackFieldBox) {
                    feedbackFieldBox.classList.add('has-error');
                }
                alert('Catatan umpan balik wajib diisi minimal 5 karakter ketika mengembalikan tugas kepada peserta untuk diperbaiki.');
                feedbackInput.focus();
                return false;
            }

            if (!confirm('Apakah Anda yakin ingin mengembalikan tugas ini kepada peserta untuk diperbaiki?')) {
                e.preventDefault();
                return false;
            }

            hiddenFeedbackKembalikan.value = feedbackVal;
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
