/**
 * Modul Interaksi Master Bidang SuperAdmin
 * - Penanganan modal tambah & edit bidang via event delegation
 * - Pengisian form aman berbasis atribut HTML5 data-* (kebal newline & kutip)
 * - Sistem Animasi Universal (Scroll-Craft + GSAP Paritas Pembimbing)
 */
import { initSuperAdminPageMotion } from '../../services/superadmin-motion.js';

document.addEventListener('DOMContentLoaded', () => {
    initSuperAdminPageMotion();

    const modalTambah = document.getElementById('modalTambahBidang');
    const modalEdit = document.getElementById('modalEditBidang');
    const modalToggle = document.getElementById('modalKonfirmasiToggleBidang');
    const editNama = document.getElementById('editNamaBidang');
    const editDeskripsi = document.getElementById('editDeskripsiBidang');
    const formEdit = document.getElementById('formEditBidang');

    document.addEventListener('click', (event) => {
        // Trigger Buka Modal Tambah
        const triggerTambah = event.target.closest('.btn-trigger-tambah-bidang') || event.target.closest('#btnOpenTambahBidang');
        if (triggerTambah) {
            event.preventDefault();
            if (modalTambah) modalTambah.style.display = 'flex';
            return;
        }

        // Trigger Buka Modal Edit
        const triggerEdit = event.target.closest('.btn-trigger-edit-bidang');
        if (triggerEdit) {
            event.preventDefault();
            const id = triggerEdit.dataset.id;
            const nama = triggerEdit.dataset.nama || '';
            const deskripsi = triggerEdit.dataset.deskripsi || '';
            const actionUrl = triggerEdit.dataset.actionUrl || `/superadmin/bidang/${id}`;

            if (editNama) editNama.value = nama;
            if (editDeskripsi) editDeskripsi.value = deskripsi;
            if (formEdit) formEdit.action = actionUrl;
            if (modalEdit) modalEdit.style.display = 'flex';
            return;
        }

        // Trigger Buka Modal Konfirmasi Toggle Bidang
        const triggerToggle = event.target.closest('.btn-trigger-toggle-bidang');
        if (triggerToggle) {
            event.preventDefault();
            const nama = triggerToggle.dataset.nama || 'Bidang';
            const isAktif = triggerToggle.dataset.statusAktif === '1';
            const actionUrl = triggerToggle.dataset.actionUrl;

            const formToggle = document.getElementById('formToggleBidang');
            const namaEl = document.getElementById('toggleBidangNama');
            const titleEl = document.getElementById('modalToggleBidangTitle');
            const msgEl = document.getElementById('toggleBidangMessage');
            const consequenceEl = document.getElementById('toggleBidangConsequence');
            const boxAlert = document.getElementById('toggleBidangBoxAlert');
            const iconWrap = document.getElementById('toggleBidangIconWrap');
            const submitBtn = document.getElementById('btnSubmitToggleBidang');
            const submitText = document.getElementById('btnSubmitToggleBidangText');

            if (formToggle && actionUrl) formToggle.action = actionUrl;
            if (namaEl) namaEl.textContent = nama;

            if (isAktif) {
                if (titleEl) titleEl.textContent = 'Konfirmasi Penonaktifan Bidang';
                if (msgEl) msgEl.textContent = 'Apakah Anda yakin ingin menonaktifkan bidang dinas ini?';
                if (consequenceEl) consequenceEl.textContent = '• Bidang yang dinonaktifkan tidak akan muncul pada pilihan pendaftaran magang baru maupun penugasan pembimbing.';
                if (boxAlert) { boxAlert.style.background = '#FFF1F2'; boxAlert.style.borderColor = '#FECDD3'; }
                if (iconWrap) { iconWrap.style.background = '#FEE2E2'; iconWrap.style.color = '#DC2626'; }
                if (submitBtn) submitBtn.style.background = '#DC2626';
                if (submitText) submitText.textContent = 'Ya, Nonaktifkan Bidang';
            } else {
                if (titleEl) titleEl.textContent = 'Konfirmasi Pengaktifan Bidang';
                if (msgEl) msgEl.textContent = 'Apakah Anda yakin ingin mengaktifkan kembali bidang dinas ini?';
                if (consequenceEl) consequenceEl.textContent = '• Bidang akan kembali tersedia untuk dipilih pada pendaftaran peserta dan penempatan operasional.';
                if (boxAlert) { boxAlert.style.background = '#F0FDF4'; boxAlert.style.borderColor = '#BBF7D0'; }
                if (iconWrap) { iconWrap.style.background = '#DCFCE7'; iconWrap.style.color = '#16A34A'; }
                if (submitBtn) submitBtn.style.background = '#16A34A';
                if (submitText) submitText.textContent = 'Ya, Aktifkan Bidang';
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

        // Tutup jika klik latar belakang (backdrop)
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

