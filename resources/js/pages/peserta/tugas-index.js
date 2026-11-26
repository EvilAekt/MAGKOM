/**
 * Modul Interaksi Classroom Tugas Index Peserta
 * Menangani filter 3-tab (Semua Tugas, Selesai, Lewat Tenggat), accordion toggle, dan empty state.
 */

export function initTugasIndex() {
    // Accordion Toggle Handler
    const btnToggle = document.getElementById('btnToggleAssignment');
    const accordionBody = document.getElementById('assignmentAccordionBody');

    if (btnToggle && accordionBody) {
        btnToggle.addEventListener('click', (e) => {
            e.preventDefault();
            const isCollapsed = accordionBody.classList.toggle('is-collapsed');
            btnToggle.classList.toggle('collapsed', isCollapsed);
            btnToggle.setAttribute('aria-expanded', isCollapsed ? 'false' : 'true');
        });
    }

    const tabsContainer = document.querySelector('.classroom-tabs');
    const tipeTabsContainer = document.querySelector('.classroom-tipe-tabs');
    const assignmentList = document.getElementById('assignmentList');
    const emptyState = document.getElementById('filterEmptyState');
    const emptyTitle = document.getElementById('filterEmptyTitle');
    const emptyDesc = document.getElementById('filterEmptyDesc');

    if (!tabsContainer || !assignmentList) {
        return;
    }

    const cards = Array.from(assignmentList.querySelectorAll('.assignment-card'));
    let activeStatusFilter = 'all';
    let activeTipeFilter = 'all';

    function applyCombinedFilters() {
        let visibleCount = 0;

        cards.forEach((card) => {
            const status = card.getAttribute('data-status');
            const tipe = card.getAttribute('data-tipe') || 'individu';
            const isOverdue = card.getAttribute('data-is-overdue') === '1';

            let matchStatus = false;
            if (activeStatusFilter === 'all') {
                matchStatus = true;
            } else if (activeStatusFilter === 'tugas') {
                matchStatus = (status !== 'selesai');
            } else if (activeStatusFilter === 'selesai') {
                matchStatus = (status === 'selesai');
            } else if (activeStatusFilter === 'terlambat') {
                matchStatus = isOverdue;
            }

            const matchTipe = activeTipeFilter === 'all' || tipe === activeTipeFilter;
            const shouldShow = matchStatus && matchTipe;

            if (shouldShow) {
                card.style.display = 'flex';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        if (emptyState) {
            if (visibleCount === 0) {
                emptyState.style.display = 'block';
                if (activeStatusFilter === 'terlambat') {
                    if (emptyTitle) emptyTitle.textContent = 'Tidak Ada Tugas Lewat Tenggat';
                    if (emptyDesc) emptyDesc.textContent = 'Bagus sekali! Seluruh tugas Anda berjalan sesuai tenggat waktu yang ditentukan.';
                } else if (activeStatusFilter === 'tugas') {
                    if (emptyTitle) emptyTitle.textContent = 'Tidak Ada Tugas Aktif';
                    if (emptyDesc) emptyDesc.textContent = 'Hebat! Anda tidak memiliki tugas aktif yang perlu dikerjakan saat ini.';
                } else if (activeStatusFilter === 'selesai') {
                    if (emptyTitle) emptyTitle.textContent = 'Belum Ada Tugas Selesai';
                    if (emptyDesc) emptyDesc.textContent = 'Tugas yang telah selesai dan diverifikasi oleh pembimbing akan muncul pada daftar ini.';
                } else if (activeTipeFilter === 'kelompok') {
                    if (emptyTitle) emptyTitle.textContent = 'Belum Ada Tugas Tim';
                    if (emptyDesc) emptyDesc.textContent = 'Pembimbing belum memberikan tugas tim untuk tim Anda.';
                } else if (activeTipeFilter === 'individu') {
                    if (emptyTitle) emptyTitle.textContent = 'Belum Ada Tugas Individu';
                    if (emptyDesc) emptyDesc.textContent = 'Pembimbing belum memberikan tugas individu khusus untuk Anda.';
                } else {
                    if (emptyTitle) emptyTitle.textContent = 'Tidak Ada Tugas Ditemukan';
                    if (emptyDesc) emptyDesc.textContent = 'Tidak ada penugasan yang sesuai dengan filter yang Anda pilih.';
                }
            } else {
                emptyState.style.display = 'none';
            }
        }
    }

    if (tipeTabsContainer) {
        tipeTabsContainer.addEventListener('click', (e) => {
            const btn = e.target.closest('.tipe-pill');
            if (!btn) return;
            e.preventDefault();

            tipeTabsContainer.querySelectorAll('.tipe-pill').forEach((pill) => {
                pill.classList.remove('active');
                pill.setAttribute('aria-selected', 'false');
            });
            btn.classList.add('active');
            btn.setAttribute('aria-selected', 'true');

            activeTipeFilter = btn.getAttribute('data-tipe-filter') || 'all';
            if (activeTipeFilter === 'all') {
                activeStatusFilter = 'all';
                tabsContainer.querySelectorAll('.tab-pill').forEach((pill) => {
                    pill.classList.remove('active');
                    pill.setAttribute('aria-selected', 'false');
                });
            }
            applyCombinedFilters();
        });
    }

    tabsContainer.addEventListener('click', (e) => {
        const btn = e.target.closest('.tab-pill');
        if (!btn) return;

        e.preventDefault();

        const clickedFilter = btn.getAttribute('data-filter') || 'all';
        const isAlreadyActive = btn.classList.contains('active');

        tabsContainer.querySelectorAll('.tab-pill').forEach((pill) => {
            pill.classList.remove('active');
            pill.setAttribute('aria-selected', 'false');
        });

        if (isAlreadyActive) {
            activeStatusFilter = 'all';
        } else {
            btn.classList.add('active');
            btn.setAttribute('aria-selected', 'true');
            activeStatusFilter = clickedFilter;
        }

        applyCombinedFilters();
    });
}

// Inisialisasi saat DOM siap
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initTugasIndex);
} else {
    initTugasIndex();
}

