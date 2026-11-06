/**
 * MAGKOM - Portal Pembimbing: Tugas Review & Evaluation Workspace
 * Kontrol Aksi Terpadu (Tandai Selesai & Kembalikan), Validasi Umpan Balik,
 * serta Navigasi Mentee Cepat.
 */

export function initPembimbingTugasReview() {
    // 1. Sidebar Collapse Toggle
    const btnToggleSidebar = document.getElementById('btnToggleSidebar');
    const bodyLayout = document.querySelector('.review-body-layout');

    if (btnToggleSidebar && bodyLayout) {
        btnToggleSidebar.addEventListener('click', () => {
            bodyLayout.classList.toggle('is-collapsed');
            const isCollapsed = bodyLayout.classList.contains('is-collapsed');
            btnToggleSidebar.setAttribute('aria-expanded', isCollapsed ? 'false' : 'true');
            btnToggleSidebar.title = isCollapsed ? 'Buka Panel Evaluasi' : 'Tutup Panel Evaluasi';
        });
    }

    // 2. Kontrol Aksi Terpadu: Validasi Pengembalian Tugas dengan Catatan Umpan Balik
    const formActionKembalikan = document.getElementById('formActionKembalikan');
    const feedbackInput = document.getElementById('feedbackInput');
    const hiddenFeedbackKembalikan = document.getElementById('hiddenFeedbackKembalikan');

    if (formActionKembalikan && feedbackInput && hiddenFeedbackKembalikan) {
        formActionKembalikan.addEventListener('submit', (e) => {
            const feedbackVal = feedbackInput.value.trim();
            if (feedbackVal.length < 5) {
                e.preventDefault();
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
