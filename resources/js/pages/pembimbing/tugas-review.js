/**
 * MAGKOM - Portal Pembimbing: Tugas Review & Evaluation Workspace
 * Menangani Tab Switching, Dinamika Form Evaluasi (Outcome-Based Mentoring),
 * Template Snippet Arahan, dan Navigasi Mentee Cepat.
 */

export function initPembimbingTugasReview() {
    // 1. Tab Switching: Kiriman Peserta vs Instruksi Tugas
    const tabBtnSubmission = document.getElementById('tabBtnSubmission');
    const tabBtnInstruction = document.getElementById('tabBtnInstruction');
    const panelSubmission = document.getElementById('panelSubmission');
    const panelInstruction = document.getElementById('panelInstruction');

    if (tabBtnSubmission && tabBtnInstruction && panelSubmission && panelInstruction) {
        tabBtnSubmission.addEventListener('click', () => {
            tabBtnSubmission.classList.add('active');
            tabBtnSubmission.setAttribute('aria-selected', 'true');
            tabBtnInstruction.classList.remove('active');
            tabBtnInstruction.setAttribute('aria-selected', 'false');

            panelSubmission.classList.remove('d-none');
            panelInstruction.classList.add('d-none');
        });

        tabBtnInstruction.addEventListener('click', () => {
            tabBtnInstruction.classList.add('active');
            tabBtnInstruction.setAttribute('aria-selected', 'true');
            tabBtnSubmission.classList.remove('active');
            tabBtnSubmission.setAttribute('aria-selected', 'false');

            panelInstruction.classList.remove('d-none');
            panelSubmission.classList.add('d-none');
        });
    }

    // 2. Sidebar Collapse Toggle
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

    // 3. Dynamic Evaluation Form Rules (Wajib feedback jika perlu revisi)
    const radioOptions = document.querySelectorAll('input[name="status_review"]');
    const feedbackInput = document.getElementById('feedbackInput');
    const feedbackReqBadge = document.getElementById('feedbackReqBadge');
    const radioPillDiterima = document.getElementById('radioPillDiterima');
    const radioPillRevisi = document.getElementById('radioPillRevisi');

    function syncEvaluationState() {
        const selectedRadio = document.querySelector('input[name="status_review"]:checked');
        if (!selectedRadio) return;

        const val = selectedRadio.value;

        if (radioPillDiterima && radioPillRevisi) {
            if (val === 'diterima') {
                radioPillDiterima.classList.add('selected-diterima');
                radioPillRevisi.classList.remove('selected-revisi');
            } else if (val === 'perlu_revisi') {
                radioPillRevisi.classList.add('selected-revisi');
                radioPillDiterima.classList.remove('selected-diterima');
            }
        }

        if (val === 'perlu_revisi') {
            if (feedbackReqBadge) feedbackReqBadge.style.display = 'inline';
            if (feedbackInput) {
                feedbackInput.setAttribute('required', 'required');
                feedbackInput.placeholder = 'Wajib memberikan arahan bagian apa saja yang perlu diperbaiki...';
            }
        } else {
            if (feedbackReqBadge) feedbackReqBadge.style.display = 'none';
            if (feedbackInput) {
                feedbackInput.removeAttribute('required');
                feedbackInput.placeholder = 'Tuliskan catatan apresiasi atau instruksi lanjutan (opsional)...';
            }
        }
    }

    radioOptions.forEach((radio) => {
        radio.addEventListener('change', syncEvaluationState);
    });
    syncEvaluationState();

    // 4. Quick Template Snippet Inserter
    const snippetSelect = document.getElementById('snippetSelect');
    if (snippetSelect && feedbackInput) {
        snippetSelect.addEventListener('change', (e) => {
            const val = e.target.value;
            if (!val) return;

            if (feedbackInput.value.trim() === '') {
                feedbackInput.value = val;
            } else {
                feedbackInput.value = feedbackInput.value.trim() + '\n' + val;
            }
            feedbackInput.focus();
            snippetSelect.value = '';
        });
    }

    // 5. Keyboard Shortcut Navigasi Mentee Cepat (< dan >)
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

    // 6. Mentee / Sibling Tugas Dropdown Selector
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

