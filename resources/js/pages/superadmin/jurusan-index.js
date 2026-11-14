/**
 * Modul Interaksi Master Jurusan SuperAdmin
 * - Penanganan modal tambah & edit jurusan via event delegation
 * - Pengisian form aman berbasis atribut HTML5 data-* (kebal newline & kutip)
 * - Sistem Animasi Universal (Scroll-Craft + GSAP Paritas Pembimbing)
 */
import { initSuperAdminPageMotion } from '../../services/superadmin-motion.js';

document.addEventListener('DOMContentLoaded', () => {
    initSuperAdminPageMotion();

    const modalTambah = document.getElementById('modalTambahJurusan');
    const modalEdit = document.getElementById('modalEditJurusan');
    const modalToggle = document.getElementById('modalKonfirmasiToggleJurusan');
    const editNama = document.getElementById('editNamaJurusan');
    const editKategori = document.getElementById('editKategoriJurusan');
    const formEdit = document.getElementById('formEditJurusan');

    document.addEventListener('click', (event) => {
        // Trigger Buka Modal Tambah
        const triggerTambah = event.target.closest('.btn-trigger-tambah-jurusan') || event.target.closest('#btnOpenTambahJurusan');
        if (triggerTambah) {
            event.preventDefault();
            if (modalTambah) modalTambah.style.display = 'flex';
            return;
        }

        // Trigger Buka Modal Edit
        const triggerEdit = event.target.closest('.btn-trigger-edit-jurusan');
        if (triggerEdit) {
            event.preventDefault();
            const id = triggerEdit.dataset.id;
            const nama = triggerEdit.dataset.nama || '';
            const kategori = triggerEdit.dataset.kategori || 'smk';
            const actionUrl = triggerEdit.dataset.actionUrl || `/superadmin/jurusan/${id}`;

            if (editNama) editNama.value = nama;
            if (editKategori) editKategori.value = kategori;
            if (formEdit) formEdit.action = actionUrl;
            if (modalEdit) modalEdit.style.display = 'flex';
            return;
        }

        // Trigger Buka Modal Konfirmasi Toggle Jurusan
        const triggerToggle = event.target.closest('.btn-trigger-toggle-jurusan');
        if (triggerToggle) {
            event.preventDefault();
            const nama = triggerToggle.dataset.nama || 'Jurusan';
            const kategori = triggerToggle.dataset.kategori || 'SMK / Mahasiswa';
            const isAktif = triggerToggle.dataset.statusAktif === '1';
            const actionUrl = triggerToggle.dataset.actionUrl;

            const formToggle = document.getElementById('formToggleJurusan');
            const namaEl = document.getElementById('toggleJurusanNama');
            const kategoriEl = document.getElementById('toggleJurusanKategori');
            const titleEl = document.getElementById('modalToggleJurusanTitle');
            const msgEl = document.getElementById('toggleJurusanMessage');
            const consequenceEl = document.getElementById('toggleJurusanConsequence');
            const boxAlert = document.getElementById('toggleJurusanBoxAlert');
            const iconWrap = document.getElementById('toggleJurusanIconWrap');
            const submitBtn = document.getElementById('btnSubmitToggleJurusan');
            const submitText = document.getElementById('btnSubmitToggleJurusanText');

            if (formToggle && actionUrl) formToggle.action = actionUrl;
            if (namaEl) namaEl.textContent = nama;
            if (kategoriEl) kategoriEl.textContent = kategori;

            if (isAktif) {
                if (titleEl) titleEl.textContent = 'Konfirmasi Penonaktifan Jurusan';
                if (msgEl) msgEl.textContent = 'Apakah Anda yakin ingin menonaktifkan program studi / jurusan ini?';
                if (consequenceEl) consequenceEl.textContent = '• Pastikan tidak ada alokasi kuota dan penempatan aktif. Jurusan tidak akan muncul pada formulir pendaftaran peserta baru.';
                if (boxAlert) { boxAlert.style.background = '#FFF1F2'; boxAlert.style.borderColor = '#FECDD3'; }
                if (iconWrap) { iconWrap.style.background = '#FEE2E2'; iconWrap.style.color = '#DC2626'; }
                if (submitBtn) submitBtn.style.background = '#DC2626';
                if (submitText) submitText.textContent = 'Ya, Nonaktifkan Jurusan';
            } else {
                if (titleEl) titleEl.textContent = 'Konfirmasi Pengaktifan Jurusan';
                if (msgEl) msgEl.textContent = 'Apakah Anda yakin ingin mengaktifkan kembali program studi / jurusan ini?';
                if (consequenceEl) consequenceEl.textContent = '• Jurusan akan kembali aktif dan dapat dipilih oleh calon peserta magang pada saat pendaftaran online.';
                if (boxAlert) { boxAlert.style.background = '#F0FDF4'; boxAlert.style.borderColor = '#BBF7D0'; }
                if (iconWrap) { iconWrap.style.background = '#DCFCE7'; iconWrap.style.color = '#16A34A'; }
                if (submitBtn) submitBtn.style.background = '#16A34A';
                if (submitText) submitText.textContent = 'Ya, Aktifkan Jurusan';
            }

            if (modalToggle) modalToggle.style.display = 'flex';
            return;
        }

        // Trigger Tutup Modal
        const dismissBtn = event.target.closest('[data-modal-dismiss]');
        if (dismissBtn) {
            event.preventDefault();
            const targetId = dismissBtn.dataset.modalDismiss;
            const targetModal = document.getElementById(targetId);
            if (targetModal) targetModal.style.display = 'none';
            return;
        }

        // Tutup jika klik backdrop
        if (event.target === modalTambah) {
            modalTambah.style.display = 'none';
        }
        if (event.target === modalEdit) {
            modalEdit.style.display = 'none';
        }
        if (event.target === modalToggle) {
            modalToggle.style.display = 'none';
        }
    });

    // Keyboard ESC untuk menutup modal
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            if (modalTambah) modalTambah.style.display = 'none';
            if (modalEdit) modalEdit.style.display = 'none';
            if (modalToggle) modalToggle.style.display = 'none';
        }
    });
});

