/**
 * MAGKOM: Logbook Module Pembimbing (ES Modules)
 * Menangani Focused Mentee View (Master-Detail Scope), Pure Card View (100% Kartu),
 * Animasi Fluida Masuk/Keluar, dan Modal Pemberian Catatan Pembimbing (Aksi Tunggal).
 */

document.addEventListener('DOMContentLoaded', () => {
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

    // 1. Focused Mentee View Actions (Master-Detail Scope)
    function focusOnMentee(menteeId, menteeName, menteeInst, updateHistory = true) {
        currentFocusedMenteeId = String(menteeId);

        // A. Perbarui Tampilan Kartu Stream Mentee
        if (streamContainer) {
            streamContainer.classList.add('is-focused');
            const allCards = streamContainer.querySelectorAll('.mentee-classroom-card');
            allCards.forEach((card) => {
                const headerBtn = card.querySelector('[data-action="focus-mentee"]');
                const drawerId = headerBtn ? headerBtn.getAttribute('aria-controls') : null;
                const drawer = drawerId ? document.getElementById(drawerId) : null;

                if (card.dataset.menteeId === String(menteeId)) {
                    card.classList.add('is-active-focus', 'is-expanded');
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
                } else {
                    card.classList.remove('is-active-focus', 'is-expanded');
                    if (headerBtn) headerBtn.setAttribute('aria-expanded', 'false');
                    if (drawer) drawer.hidden = true;
                }
            });
        }

        // B. Sembunyikan Kartu Filter Saat Masuk ke Detail Mentee
        if (filterCard) {
            filterCard.classList.add('d-none');
        }

        // C. Beralih ke Header Terpadu Identitas Mentee (Selaras Modul Tugas)
        if (defaultTitleText) defaultTitleText.classList.add('d-none');
        if (menteeUnifiedHeader) menteeUnifiedHeader.classList.remove('d-none');
        if (focusedMenteeAvatar) focusedMenteeAvatar.textContent = getInitials(menteeName);
        if (focusedMenteeName) focusedMenteeName.textContent = menteeName;
        if (focusedMenteeInst) focusedMenteeInst.textContent = menteeInst || '';

        // D. Sinkronisasi Dropdown Filter Jika Tersedia
        const selectPen = document.getElementById('selectPenempatan');
        if (selectPen) {
            selectPen.value = String(menteeId);
        }

        // E. Perbarui URL browser dengan parameter penempatan_id
        if (updateHistory) {
            try {
                const currentUrl = new URL(window.location.href);
                currentUrl.searchParams.set('penempatan_id', menteeId);
                window.history.pushState({ penempatan_id: menteeId, menteeName, menteeInst }, '', currentUrl.toString());
            } catch {
                // Ignore history errors
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

    // Event Listener Klik Kartu Mentee untuk Masuk ke Logbook Mentee
    const focusButtons = document.querySelectorAll('[data-action="focus-mentee"]');
    focusButtons.forEach((btn) => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const menteeId = btn.getAttribute('data-mentee-id');
            const menteeName = btn.getAttribute('data-mentee-name');
            const menteeInst = btn.getAttribute('data-mentee-inst');

            if (menteeId) {
                focusOnMentee(menteeId, menteeName, menteeInst, true);
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

    // 2. Modal Pemberian Catatan Pembimbing
    const catatanModal = document.getElementById('modal-catatan-logbook');
    const formCatatan = document.getElementById('form-catatan-logbook');
    const modalSubtitle = document.getElementById('catatan-modal-subtitle');
    const modalKegiatan = document.getElementById('catatan-modal-kegiatan');
    const catatanTextarea = document.getElementById('modal-catatan-textarea');

    const openButtons = document.querySelectorAll('.btn-open-catatan-modal');
    openButtons.forEach((btn) => {
        btn.addEventListener('click', () => {
            const logId = btn.getAttribute('data-log-id');
            const pesertaNama = btn.getAttribute('data-peserta-nama');
            const tanggal = btn.getAttribute('data-tanggal');
            const kegiatan = btn.getAttribute('data-kegiatan');
            const catatan = btn.getAttribute('data-catatan');

            if (formCatatan) {
                formCatatan.action = `/pembimbing/logbook/${logId}/review`;
            }

            if (modalSubtitle) {
                modalSubtitle.textContent = `Peserta: ${pesertaNama} • Tanggal: ${tanggal}`;
            }

            if (modalKegiatan) {
                modalKegiatan.textContent = kegiatan || 'Tidak ada uraian kegiatan';
            }

            if (catatanTextarea) {
                catatanTextarea.value = catatan || '';
            }

            if (catatanModal) {
                catatanModal.classList.add('show');
                catatanModal.style.display = 'flex';
                if (catatanTextarea) {
                    catatanTextarea.focus();
                }
            }
        });
    });

    // 3. Modal Close Handlers
    const closeButtons = document.querySelectorAll('[data-modal-close]');
    closeButtons.forEach((btn) => {
        btn.addEventListener('click', () => {
            const targetSelector = btn.getAttribute('data-modal-close');
            const targetModal = document.querySelector(targetSelector);
            if (targetModal) {
                targetModal.classList.remove('show');
                targetModal.style.display = 'none';
            }
        });
    });

    window.addEventListener('click', (e) => {
        if (e.target && e.target.classList.contains('modal-overlay')) {
            e.target.classList.remove('show');
            e.target.style.display = 'none';
        }
    });

    window.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && catatanModal && catatanModal.classList.contains('show')) {
            catatanModal.classList.remove('show');
            catatanModal.style.display = 'none';
        }
    });
});
