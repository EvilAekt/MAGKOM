/**
 * MAGKOM - Portal Pembimbing: Tugas & Progres Magang Index
 * Menangani Focused Mentee View (Master-Detail Scope), Animasi Fluida JS/CSS,
 * dan switcher mode tampilan (Kartu Classroom vs Tabel Administratif Asli).
 */

import { initScrollCraft } from '../../services/scroll-craft.js';

export function initPembimbingTugasIndex() {
    initScrollCraft();
    const streamContainer = document.getElementById('menteeStreamContainer');
    const tableContainer = document.getElementById('classroomTableContainer');
    const menteesSummaryView = document.getElementById('tableMenteesSummaryView');
    const menteeTasksView = document.getElementById('tableMenteeTasksView');
    const mainTugasTable = document.getElementById('mainTugasTable');
    const btnCard = document.getElementById('btnViewCard');
    const btnTable = document.getElementById('btnViewTable');
    const btnBack = document.getElementById('btnBackToMentees');
    const defaultTitleText = document.getElementById('defaultTitleText');
    const menteeUnifiedHeader = document.getElementById('menteeUnifiedHeader');
    const focusedMenteeAvatar = document.getElementById('focusedMenteeAvatar');
    const focusedMenteeName = document.getElementById('focusedMenteeName');
    const focusedMenteeInst = document.getElementById('focusedMenteeInst');

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

    let currentViewMode = 'card';
    let currentFocusedMenteeId = null;

    // Deteksi awal jika ada penempatan_id di URL
    const urlParams = new URLSearchParams(window.location.search);
    const initialMenteeId = urlParams.get('penempatan_id');
    if (initialMenteeId) {
        currentFocusedMenteeId = String(initialMenteeId);
    }

    // 1. Switcher Mode Tampilan (Kartu Classroom vs Tabel Administratif)
    function switchTaskView(mode) {
        currentViewMode = mode;
        try {
            localStorage.setItem('pembimbing_task_view_mode', mode);
        } catch {
            // Abaikan storage error
        }

        if (mode === 'table') {
            if (streamContainer) streamContainer.style.display = 'none';
            if (tableContainer) tableContainer.style.display = 'block';

            if (btnCard) {
                btnCard.classList.remove('active');
                btnCard.setAttribute('aria-pressed', 'false');
            }
            if (btnTable) {
                btnTable.classList.add('active');
                btnTable.setAttribute('aria-pressed', 'true');
            }

            // Sinkronkan sub-view tabel:
            // Jika ada mentee fokus: tampilkan tabel tugas mentee tersebut
            // Jika belum ada mentee fokus: tampilkan tabel daftar mentee bimbingan
            if (currentFocusedMenteeId) {
                if (menteesSummaryView) {
                    menteesSummaryView.classList.add('d-none');
                    menteesSummaryView.style.display = 'none';
                }
                if (menteeTasksView) {
                    menteeTasksView.classList.remove('d-none');
                    menteeTasksView.style.display = 'block';
                }
                if (tableContainer) tableContainer.classList.add('is-focused');
                if (mainTugasTable) mainTugasTable.classList.add('table-focused-mentee');

                const tableRows = tableContainer ? tableContainer.querySelectorAll('tbody tr.classroom-table-row') : [];
                tableRows.forEach((row) => {
                    if (row.dataset.menteeId === String(currentFocusedMenteeId)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            } else {
                if (menteesSummaryView) {
                    menteesSummaryView.classList.remove('d-none');
                    menteesSummaryView.style.display = 'block';
                }
                if (menteeTasksView) {
                    menteeTasksView.classList.add('d-none');
                    menteeTasksView.style.display = 'none';
                }
                if (tableContainer) tableContainer.classList.remove('is-focused');
                if (mainTugasTable) mainTugasTable.classList.remove('table-focused-mentee');
            }
        } else {
            if (tableContainer) tableContainer.style.display = 'none';
            if (streamContainer) streamContainer.style.display = 'flex';

            if (btnTable) {
                btnTable.classList.remove('active');
                btnTable.setAttribute('aria-pressed', 'false');
            }
            if (btnCard) {
                btnCard.classList.add('active');
                btnCard.setAttribute('aria-pressed', 'true');
            }
        }
    }

    if (btnCard && !btnCard.dataset.initialized) {
        btnCard.dataset.initialized = 'true';
        btnCard.addEventListener('click', () => switchTaskView('card'));
    }

    if (btnTable && !btnTable.dataset.initialized) {
        btnTable.dataset.initialized = 'true';
        btnTable.addEventListener('click', () => switchTaskView('table'));
    }

    // 2. Accordion Expand/Collapse Drawer Tugas Mentee
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

        // A. Reset tampilan Kartu Stream Mentee
        if (streamContainer) {
            streamContainer.classList.remove('is-focused');
            const allCards = streamContainer.querySelectorAll('.mentee-classroom-card');
            allCards.forEach((card, idx) => {
                card.classList.remove('is-active-focus', 'is-expanded');
                const headerBtn = card.querySelector('[data-action="focus-mentee"]');
                const drawerId = headerBtn ? headerBtn.getAttribute('aria-controls') : null;
                const drawer = drawerId ? document.getElementById(drawerId) : null;

                if (headerBtn) headerBtn.setAttribute('aria-expanded', 'false');
                if (drawer) drawer.hidden = true;

                card.style.animation = 'none';
                void card.offsetWidth;
                card.style.animation = `fadeInUpBounce 0.25s cubic-bezier(0.16, 1, 0.3, 1) ${idx * 0.03}s both`;
            });
        }

        // B. Reset tampilan Tabel Administratif Asli
        if (menteesSummaryView) {
            menteesSummaryView.classList.remove('d-none');
            menteesSummaryView.style.display = 'block';
        }
        if (menteeTasksView) {
            menteeTasksView.classList.add('d-none');
            menteeTasksView.style.display = 'none';
        }
        if (tableContainer) {
            tableContainer.classList.remove('is-focused');
            if (mainTugasTable) mainTugasTable.classList.remove('table-focused-mentee');
            const tableRows = tableContainer.querySelectorAll('tbody tr.classroom-table-row');
            tableRows.forEach((row) => {
                row.style.display = '';
            });
        }

        // C. Kembalikan ke Header Default Panel
        if (menteeUnifiedHeader) menteeUnifiedHeader.classList.add('d-none');
        if (defaultTitleText) defaultTitleText.classList.remove('d-none');

        // Kembalikan nilai dropdown filter ke default
        const selectPen = document.getElementById('selectPenempatan');
        if (selectPen) {
            selectPen.value = '';
        }

        // Munculkan kembali kartu filter saat melihat semua mentee
        const filterCard = document.getElementById('tugasFilterCard');
        if (filterCard) {
            filterCard.classList.remove('d-none');
        }

        // D. Perbarui URL browser tanpa penempatan_id
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

    // 3. Event Delegation untuk klik toggle drawer mentee
    if (!window._pembimbingTugasDelegationInitialized) {
        window._pembimbingTugasDelegationInitialized = true;
        document.addEventListener('click', function (e) {
            const focusBtn = e.target.closest('[data-action="focus-mentee"]');
            if (!focusBtn) return;

            e.preventDefault();
            const card = focusBtn.closest('.mentee-classroom-card');
            if (card) {
                toggleMenteeDrawer(card);
            }
        });
    }

    // 4. Handler tombol "Semua Mentee" (Kembali)
    if (btnBack && !btnBack.dataset.initialized) {
        btnBack.dataset.initialized = 'true';
        btnBack.addEventListener('click', function (e) {
            e.preventDefault();
            resetToAllMentees(true);
        });
    }

    // 5. Sinkronisasi navigasi Back/Forward browser via popstate
    if (!window._pembimbingTugasPopstateInitialized) {
        window._pembimbingTugasPopstateInitialized = true;
        window.addEventListener('popstate', function () {
            const params = new URLSearchParams(window.location.search);
            const penempatanId = params.get('penempatan_id');

            if (penempatanId) {
                const targetCard = document.querySelector(`.mentee-classroom-card[data-mentee-id="${penempatanId}"]`);
                const menteeName = targetCard?.dataset?.menteeName || 'Mentee';
                const menteeInst = targetCard?.dataset?.menteeInst || '';
                focusOnMentee(penempatanId, menteeName, menteeInst, false);
            } else {
                resetToAllMentees(false);
            }
        });
    }

    // 6. Pulihkan preferensi tampilan (Kartu vs Tabel) dari localStorage jika ada
    let savedMode = 'card';
    try {
        savedMode = localStorage.getItem('pembimbing_task_view_mode') || 'card';
    } catch {
        savedMode = 'card';
    }

    if (savedMode === 'table') {
        switchTaskView('table');
    } else {
        switchTaskView('card');
    }

    // Jika mode fokus aktif sejak render awal server, sinkronkan filter baris tabel
    if (initialMenteeId && tableContainer) {
        if (menteesSummaryView) {
            menteesSummaryView.classList.add('d-none');
            menteesSummaryView.style.display = 'none';
        }
        if (menteeTasksView) {
            menteeTasksView.classList.remove('d-none');
            menteeTasksView.style.display = 'block';
        }
        tableContainer.classList.add('is-focused');
        if (mainTugasTable) mainTugasTable.classList.add('table-focused-mentee');
        const tableRows = tableContainer.querySelectorAll('tbody tr.classroom-table-row');
        tableRows.forEach((row) => {
            if (row.dataset.menteeId === String(initialMenteeId)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    // 7. Event Delegation: Double Click & Keyboard Access pada Card Baris Tugas
    if (!window._pembimbingTaskRowDblClickInitialized) {
        window._pembimbingTaskRowDblClickInitialized = true;

        // A. Double Click untuk membuka detail tugas
        document.addEventListener('dblclick', function (e) {
            const taskRow = e.target.closest('.classroom-task-row[data-task-url]');
            if (!taskRow) return;

            // Jika double-click dilakukan pada elemen interactive native (misal link judul atau tombol), biarkan native handler
            if (e.target.closest('a, button')) return;

            const taskUrl = taskRow.getAttribute('data-task-url');
            if (taskUrl) {
                window.location.href = taskUrl;
            }
        });

        // B. Aksesibilitas Keyboard: Tekan tombol Enter pada card yang difokuskan
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                const taskRow = e.target.closest('.classroom-task-row[data-task-url]');
                // Pastikan target fokus langsung adalah baris card (bukan anchor link judul di dalamnya)
                if (taskRow && e.target === taskRow) {
                    e.preventDefault();
                    const taskUrl = taskRow.getAttribute('data-task-url');
                    if (taskUrl) {
                        window.location.href = taskUrl;
                    }
                }
            }
        });
    }
}

// Inisialisasi saat DOM siap
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initPembimbingTugasIndex);
} else {
    initPembimbingTugasIndex();
}
