/**
 * MagKom SuperAdmin - Manajemen Slot Khusus & Kredensial Peserta Jalur Khusus
 * Mematuhi SEC-01 & SEC-02 (Anti-XSS, Secure Clipboard Handling)
 */

import { MagKomToast } from '../../components/toast.js';
import { initSuperAdminPageMotion } from '../../services/superadmin-motion.js';

document.addEventListener('DOMContentLoaded', () => {
    initSuperAdminPageMotion();
    initCredentialCopyHandlers();
    initSlotModalHandlers();
});

function initSlotModalHandlers() {
    document.addEventListener('click', (event) => {
        // 1. Pemicu Buka Modal Tambah Slot
        const triggerTambah = event.target.closest('#btnOpenTambahSlot') || event.target.closest('.btn-trigger-tambah-slot');
        if (triggerTambah) {
            event.preventDefault();
            const m = document.getElementById('modalTambahSlot');
            if (m) m.style.display = 'flex';
            return;
        }

        // 2. Pemicu Buka Modal Konfirmasi Toggle Slot
        const triggerToggle = event.target.closest('.btn-trigger-toggle-slot');
        if (triggerToggle) {
            event.preventDefault();
            const instansi = triggerToggle.dataset.instansi || 'Instansi Mitra';
            const jumlah = triggerToggle.dataset.jumlah || '0';
            const isAktif = triggerToggle.dataset.statusAktif === '1';
            const actionUrl = triggerToggle.dataset.actionUrl;

            const modalToggle = document.getElementById('modalKonfirmasiToggleSlot');
            const formToggle = document.getElementById('formToggleSlot');
            const instansiEl = document.getElementById('toggleSlotInstansi');
            const detailEl = document.getElementById('toggleSlotDetail');
            const titleEl = document.getElementById('modalToggleSlotTitle');
            const msgEl = document.getElementById('toggleSlotMessage');
            const consequenceEl = document.getElementById('toggleSlotConsequence');
            const boxAlert = document.getElementById('toggleSlotBoxAlert');
            const iconWrap = document.getElementById('toggleSlotIconWrap');
            const submitBtn = document.getElementById('btnSubmitToggleSlot');
            const submitText = document.getElementById('btnSubmitToggleSlotText');

            if (formToggle && actionUrl) formToggle.action = actionUrl;
            if (instansiEl) instansiEl.textContent = instansi;
            if (detailEl) detailEl.textContent = `Alokasi Kuota: ${jumlah} Kursi`;

            if (isAktif) {
                if (titleEl) titleEl.textContent = 'Konfirmasi Penonaktifan Slot';
                if (msgEl) msgEl.textContent = 'Apakah Anda yakin ingin menonaktifkan alokasi slot khusus ini?';
                if (consequenceEl) consequenceEl.textContent = '• Slot yang dinonaktifkan tidak dapat digunakan untuk pembuatan akun peserta jalur kemitraan baru.';
                if (boxAlert) { boxAlert.style.background = '#FFF1F2'; boxAlert.style.borderColor = '#FECDD3'; }
                if (iconWrap) { iconWrap.style.background = '#FEE2E2'; iconWrap.style.color = '#DC2626'; }
                if (submitBtn) submitBtn.style.background = '#DC2626';
                if (submitText) submitText.textContent = 'Ya, Nonaktifkan Slot';
            } else {
                if (titleEl) titleEl.textContent = 'Konfirmasi Pengaktifan Slot';
                if (msgEl) msgEl.textContent = 'Apakah Anda yakin ingin mengaktifkan kembali slot khusus ini?';
                if (consequenceEl) consequenceEl.textContent = '• Alokasi kuota mitra ini akan kembali aktif dan siap menerima penempatan peserta khusus.';
                if (boxAlert) { boxAlert.style.background = '#F0FDF4'; boxAlert.style.borderColor = '#BBF7D0'; }
                if (iconWrap) { iconWrap.style.background = '#DCFCE7'; iconWrap.style.color = '#16A34A'; }
                if (submitBtn) submitBtn.style.background = '#16A34A';
                if (submitText) submitText.textContent = 'Ya, Aktifkan Slot';
            }

            if (modalToggle) modalToggle.style.display = 'flex';
            return;
        }

        // 3. Pemicu Tutup Modal via data-modal-dismiss
        const dismissBtn = event.target.closest('[data-modal-dismiss]');
        if (dismissBtn) {
            event.preventDefault();
            const targetId = dismissBtn.dataset.modalDismiss;
            const targetModal = document.getElementById(targetId);
            if (targetModal) targetModal.style.display = 'none';
            return;
        }

        // 4. Klik Backdrop
        const modalTambah = document.getElementById('modalTambahSlot');
        const modalToggleSlot = document.getElementById('modalKonfirmasiToggleSlot');
        if (event.target === modalTambah) modalTambah.style.display = 'none';
        if (event.target === modalToggleSlot) modalToggleSlot.style.display = 'none';
    });

    // ESC Key Listener
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            const m1 = document.getElementById('modalTambahSlot');
            const m2 = document.getElementById('modalKonfirmasiToggleSlot');
            if (m1) m1.style.display = 'none';
            if (m2) m2.style.display = 'none';
        }
    });
}

function initCredentialCopyHandlers() {
    document.addEventListener('click', async (event) => {
        const button = event.target.closest('.sa-btn-copy-kredensial');
        if (!button) return;

        event.preventDefault();

        const nama = button.dataset.nama || 'Peserta';
        const email = button.dataset.email || '-';
        const sandi = button.dataset.sandi || '';
        const status = button.dataset.status || 'aktif';
        const loginUrl = button.dataset.loginUrl || window.location.origin + '/login';

        let statusText = 'Demi keamanan data, Anda wajib mengganti kata sandi ini saat pertama kali masuk ke portal.';
        if (status === 'changed') {
            statusText = 'Peserta telah memperbarui kata sandi pribadinya di portal. Kata sandi awal ini sudah kadaluwarsa/tidak berlaku lagi untuk login.';
        }

        const sandiDisplay = sandi ? sandi : '(Tidak tersimpan / Akun lama)';

        const textToCopy = `*AKUN PORTAL MAGANG KOMINFO (MAGKOM)*
Nama: ${nama}
Email: ${email}
Kata Sandi Awal: ${sandiDisplay}
Tautan Login: ${loginUrl}

*Catatan:* ${statusText}`;

        try {
            if (navigator.clipboard && window.isSecureContext) {
                await navigator.clipboard.writeText(textToCopy);
            } else {
                // Fallback untuk insecure context / environment test
                const textArea = document.createElement('textarea');
                textArea.value = textToCopy;
                textArea.style.position = 'fixed';
                textArea.style.left = '-999999px';
                textArea.style.top = '-999999px';
                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();
                document.execCommand('copy');
                textArea.remove();
            }

            // Visual feedback pada tombol
            const originalHTML = button.innerHTML;
            button.classList.add('is-copied');
            button.innerHTML = `
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                <span>Tersalin!</span>
            `;

            setTimeout(() => {
                button.classList.remove('is-copied');
                button.innerHTML = originalHTML;
            }, 2500);

            MagKomToast.show({
                type: 'success',
                title: 'Kredensial Berhasil Disalin',
                message: `Format akun untuk ${nama} siap ditempel (paste) ke WhatsApp / Messenger.`,
                duration: 4000
            });
        } catch (err) {
            console.error('Gagal menyalin kredensial:', err);
            MagKomToast.show({
                type: 'error',
                title: 'Gagal Menyalin',
                message: 'Tidak dapat mengakses clipboard browser.',
                duration: 4000
            });
        }
    });
}

