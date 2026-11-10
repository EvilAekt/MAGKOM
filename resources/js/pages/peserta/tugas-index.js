/**
 * Modul Interaksi Classroom Tugas Index Peserta
 * Menangani filter 3-tab (Semua Tugas, Selesai, Lewat Tenggat) dan empty state.
 */

export function initTugasIndex() {
    const tabsContainer = document.querySelector('.classroom-tabs');
    const assignmentList = document.getElementById('assignmentList');
    const emptyState = document.getElementById('filterEmptyState');
    const emptyTitle = document.getElementById('filterEmptyTitle');
    const emptyDesc = document.getElementById('filterEmptyDesc');

    if (!tabsContainer || !assignmentList) {
        return;
    }

    const cards = Array.from(assignmentList.querySelectorAll('.assignment-card'));

    tabsContainer.addEventListener('click', (e) => {
        const btn = e.target.closest('.tab-pill');
        if (!btn) return;

        e.preventDefault();

        // Update active tab styling
        tabsContainer.querySelectorAll('.tab-pill').forEach((pill) => {
            pill.classList.remove('active');
            pill.setAttribute('aria-selected', 'false');
        });
        btn.classList.add('active');
        btn.setAttribute('aria-selected', 'true');

        const filter = btn.getAttribute('data-filter') || 'all';
        let visibleCount = 0;

        cards.forEach((card) => {
            const status = card.getAttribute('data-status');
            const isOverdue = card.getAttribute('data-is-overdue') === '1';

            let shouldShow = false;
            if (filter === 'all') {
                shouldShow = true;
            } else if (filter === 'selesai') {
                shouldShow = (status === 'selesai');
            } else if (filter === 'terlambat') {
                shouldShow = isOverdue;
            }

            if (shouldShow) {
                card.style.display = 'flex';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        // Tampilkan filter empty state bila tidak ada kartu yang cocok
        if (emptyState) {
            if (visibleCount === 0) {
                emptyState.style.display = 'block';
                if (filter === 'terlambat') {
                    if (emptyTitle) emptyTitle.textContent = 'Tidak Ada Tugas Lewat Tenggat';
                    if (emptyDesc) emptyDesc.textContent = 'Bagus sekali! Seluruh tugas Anda berjalan sesuai tenggat waktu yang ditentukan.';
                } else if (filter === 'selesai') {
                    if (emptyTitle) emptyTitle.textContent = 'Belum Ada Tugas Selesai';
                    if (emptyDesc) emptyDesc.textContent = 'Tugas yang telah selesai dan diverifikasi oleh pembimbing akan muncul pada daftar ini.';
                } else {
                    if (emptyTitle) emptyTitle.textContent = 'Tidak Ada Tugas Ditemukan';
                    if (emptyDesc) emptyDesc.textContent = 'Tidak ada penugasan yang sesuai dengan filter yang Anda pilih.';
                }
            } else {
                emptyState.style.display = 'none';
            }
        }
    });
}

// Inisialisasi saat DOM siap
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initTugasIndex);
} else {
    initTugasIndex();
}

