/**
 * MAGKOM: Logbook Module Pembimbing (ES Modules)
 * Menangani Focused Mentee View (Master-Detail Scope), Pure Card View (100% Kartu),
 * Animasi Fluida Masuk/Keluar, dan Modal Pemberian Catatan Pembimbing (Aksi Tunggal).
 */

import { initScrollCraft } from '../../services/scroll-craft.js';

document.addEventListener('DOMContentLoaded', () => {
    initScrollCraft();
    const streamContainer = document.getElementById('menteeStreamContainer');
    const filterCard = document.getElementById('logbookFilterCard');
    const defaultTitleText = document.getElementById('defaultTitleText');
    const menteeUnifiedHeader = document.getElementById('menteeUnifiedHeader');
    const focusedMenteeAvatar = document.getElementById('focusedMenteeAvatar');
    const focusedMenteeName = document.getElementById('focusedMenteeName');
    const focusedMenteeInst = document.getElementById('focusedMenteeInst');
    const btnBack = document.getElementById('btnBackToMentees');

    function getInitials(name) {
        if (!name) return 'M';
        return name
            .split(' ')
            .filter(Boolean)
            .map((w) => w.charAt(0))
            .slice(0, 2)
            .join('')
            .toUpperCase();
    }

    let currentFocusedMenteeId = null;

    // Deteksi awal jika ada parameter penempatan_id di URL
    const urlParams = new URLSearchParams(window.location.search);
    const initialMenteeId = urlParams.get('penempatan_id');
    if (initialMenteeId) {
        currentFocusedMenteeId = String(initialMenteeId);
    }

    // 1. Accordion Expand/Collapse Drawer Logbook Mentee
    function toggleMenteeDrawer(card) {
        const headerBtn = card.querySelector('[data-action="focus-mentee"]');
        const drawerId = headerBtn ? headerBtn.getAttribute('aria-controls') : null;
        const drawer = drawerId ? document.getElementById(drawerId) : null;
        const isExpanded = card.classList.contains('is-expanded');

        if (isExpanded) {
            card.classList.remove('is-expanded');
            if (headerBtn) headerBtn.setAttribute('aria-expanded', 'false');
            if (drawer) drawer.hidden = true;
        } else {
            card.classList.add('is-expanded');
            if (headerBtn) headerBtn.setAttribute('aria-expanded', 'true');
            if (drawer) {
                drawer.hidden = false;
                const rows = drawer.querySelectorAll('.classroom-task-row');
                rows.forEach((row, idx) => {
                    row.style.animation = 'none';
                    void row.offsetWidth; // Reflow
                    row.style.animation = `fadeInCascade 0.3s cubic-bezier(0.16, 1, 0.3, 1) ${idx * 0.04}s both`;
                });
            }
        }
    }

    function resetToAllMentees(updateHistory = true) {
        currentFocusedMenteeId = null;

        // A. Kembalikan semua kartu mentee ke daftar normal
        if (streamContainer) {
            streamContainer.classList.remove('is-focused');
            const allCards = streamContainer.querySelectorAll('.mentee-classroom-card');
            allCards.forEach((card) => {
                card.classList.remove('is-active-focus', 'is-expanded');
                const headerBtn = card.querySelector('[data-action="focus-mentee"]');
                if (headerBtn) headerBtn.setAttribute('aria-expanded', 'false');
                const drawerId = headerBtn ? headerBtn.getAttribute('aria-controls') : null;
                const drawer = drawerId ? document.getElementById(drawerId) : null;
                if (drawer) drawer.hidden = true;
            });
        }

        // B. Munculkan Kembali Kartu Filter
        if (filterCard) {
            filterCard.classList.remove('d-none');
        }

        // C. Kembalikan ke Header Default Panel
        if (menteeUnifiedHeader) menteeUnifiedHeader.classList.add('d-none');
        if (defaultTitleText) defaultTitleText.classList.remove('d-none');

        // D. Kembalikan Nilai Dropdown Filter ke Default
        const selectPen = document.getElementById('selectPenempatan');
        if (selectPen) {
            selectPen.value = '';
        }

        // E. Bersihkan parameter penempatan_id dari URL
        if (updateHistory) {
            try {
                const currentUrl = new URL(window.location.href);
                currentUrl.searchParams.delete('penempatan_id');
                window.history.pushState({}, '', currentUrl.toString());
            } catch {
                // Ignore history errors
            }
        }
    }

    // Event Listener Klik Kartu Mentee untuk Expand/Collapse Logbook Mentee
    const focusButtons = document.querySelectorAll('[data-action="focus-mentee"]');
    focusButtons.forEach((btn) => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const card = btn.closest('.mentee-classroom-card');
            if (card) {
                toggleMenteeDrawer(card);
            }
        });
    });

    // Event Listener Tombol "Semua Mentee" untuk Kembali ke Daftar
    if (btnBack) {
        btnBack.addEventListener('click', (e) => {
            e.preventDefault();
            resetToAllMentees(true);
        });
    }

    // Tangani navigasi History Back/Forward Browser
    window.addEventListener('popstate', (e) => {
        const state = e.state;
        if (state && state.penempatan_id) {
            focusOnMentee(state.penempatan_id, state.menteeName, state.menteeInst, false);
        } else {
            resetToAllMentees(false);
        }
    });

    // 2. Inline Note Editor Toggle & Cancel Handlers
    document.addEventListener('click', (e) => {
        // Toggle Buka / Tutup Form Inline Catatan
        const toggleBtn = e.target.closest('[data-action="toggle-inline-note"]');
        if (toggleBtn) {
            e.preventDefault();
            const logId = toggleBtn.getAttribute('data-log-id');
            const editor = document.getElementById(`inline-note-form-${logId}`);
            const notesView = document.getElementById(`notes-view-${logId}`);

            if (!editor) return;

            const isHidden = editor.hidden;
            if (isHidden) {
                // Tampilkan form editor inline
                editor.hidden = false;
                if (notesView) {
                    notesView.style.display = 'none';
                }
                const textarea = editor.querySelector('.logbook-note-clean-textarea') || editor.querySelector('textarea');
                const fieldContainer = editor.querySelector('.logbook-note-field-filled');

                if (textarea && fieldContainer) {
                    if (textarea.value.trim().length > 0) {
                        fieldContainer.classList.add('has-value');
                    } else {
                        fieldContainer.classList.remove('has-value');
                    }
                    textarea.focus();
                }
            } else {
                // Sembunyikan form editor inline
                editor.hidden = true;
                if (notesView) {
                    notesView.style.display = '';
                }
            }
            return;
        }

        // Batal / Tutup Form Inline Catatan
        const cancelBtn = e.target.closest('[data-action="cancel-inline-note"]');
        if (cancelBtn) {
            e.preventDefault();
            const logId = cancelBtn.getAttribute('data-log-id');
            const editor = document.getElementById(`inline-note-form-${logId}`);
            const notesView = document.getElementById(`notes-view-${logId}`);

            if (editor) {
                editor.hidden = true;
            }
            if (notesView) {
                notesView.style.display = '';
            }
            return;
        }
    });

    // 3. Listener Real-time Input Textarea Catatan untuk State Floating Label (.has-value)
    document.addEventListener('input', (e) => {
        if (e.target && e.target.matches('.logbook-note-clean-textarea')) {
            const fieldContainer = e.target.closest('.logbook-note-field-filled');
            if (fieldContainer) {
                if (e.target.value.trim().length > 0) {
                    fieldContainer.classList.add('has-value');
                } else {
                    fieldContainer.classList.remove('has-value');
                }
            }
        }
    });
});
