/**
 * Modul Interaksi Manajemen Staf Dinas SuperAdmin
 * - Penanganan modal tambah, edit, dan reset sandi via event delegation
 * - Toggling dinamis bidang penempatan sesuai peran (Admin vs Pembimbing)
 * - Pengisian form aman berbasis atribut HTML5 data-* (kebal newline & kutip)
 * - Sistem Animasi Universal (Scroll-Craft + GSAP Paritas Pembimbing)
 */
import { MagKomToast } from '../../components/toast.js';
import { initSuperAdminPageMotion } from '../../services/superadmin-motion.js';
import { initPasswordStrengthMeter } from '../../components/password-strength.js';

export function togglePasswordVisibility(fieldId, btn) {
    const input = document.getElementById(fieldId);
    if (!input || !btn) return;

    const isPassword = input.type === 'password';
    input.type = isPassword ? 'text' : 'password';

    if (isPassword) {
        btn.innerHTML = `
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"></path>
            </svg>
        `;
        btn.setAttribute('aria-label', 'Sembunyikan kata sandi');
    } else {
        btn.innerHTML = `
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
            </svg>
        `;
        btn.setAttribute('aria-label', 'Lihat kata sandi');
    }
}

export function toggleBidangField(mode) {
    if (mode === 'tambah') {
        const roleEl = document.getElementById('tambahRole');
        const wrapEl = document.getElementById('wrapBidangTambah');
        const selectEl = document.getElementById('tambahBidang');
        const gridEl = document.getElementById('gridRoleBidangTambah');
        if (roleEl && wrapEl && selectEl) {
            if (roleEl.value === 'admin') {
                wrapEl.style.display = 'none';
                selectEl.required = false;
                selectEl.value = '';
                if (gridEl) gridEl.style.gridTemplateColumns = '1fr';
            } else {
                wrapEl.style.display = 'block';
                selectEl.required = true;
                if (gridEl) gridEl.style.gridTemplateColumns = '1fr 1fr';
            }
        }
    } else if (mode === 'edit') {
        const roleEl = document.getElementById('editRole');
        const wrapEl = document.getElementById('wrapBidangEdit');
        const selectEl = document.getElementById('editBidang');
        if (roleEl && wrapEl && selectEl) {
            if (roleEl.value === 'admin') {
                wrapEl.style.display = 'none';
                selectEl.required = false;
                selectEl.value = '';
            } else {
                wrapEl.style.display = 'block';
                selectEl.required = true;
            }
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    initSuperAdminPageMotion();
    initPasswordStrengthMeter();

    const modalTambah = document.getElementById('modalTambahStaf');
    const modalReset = document.getElementById('modalResetSandi');
    const modalEdit = document.getElementById('modalEditStaf');
    const modalToggle = document.getElementById('modalKonfirmasiToggleStaf');
    const modalSandiBaru = document.getElementById('modalKonfirmasiSandiBaru');

    const resetNama = document.getElementById('resetNamaStaf');
    const formReset = document.getElementById('formResetSandi');

    const editNama = document.getElementById('editNama');
    const editEmail = document.getElementById('editEmail');
    const editRole = document.getElementById('editRole');
    const editBidang = document.getElementById('editBidang');
    const formEdit = document.getElementById('formEditStaf');

    const tambahRole = document.getElementById('tambahRole');
    if (tambahRole) {
        tambahRole.addEventListener('change', () => toggleBidangField('tambah'));
    }
    if (editRole) {
        editRole.addEventListener('change', () => toggleBidangField('edit'));
    }

    // Inisialisasi awal form tambah
    toggleBidangField('tambah');

    document.addEventListener('click', async (event) => {
        // Trigger Toggle Password Visibility (Mata)
        const toggleBtn = event.target.closest('.toggle-password-btn');
        if (toggleBtn) {
            event.preventDefault();
            const targetId = toggleBtn.dataset.toggleTarget;
            if (targetId) {
                togglePasswordVisibility(targetId, toggleBtn);
            }
            return;
        }

        // Trigger Salin Kredensial Akun Staf
        const copyBtn = event.target.closest('.sa-btn-copy-kredensial');
        if (copyBtn) {
            event.preventDefault();

            const nama = copyBtn.dataset.nama || 'Staf';
            const email = copyBtn.dataset.email || '-';
            const role = copyBtn.dataset.role || 'Staf Internal';
            const sandi = copyBtn.dataset.sandi || '';
            const status = copyBtn.dataset.status || 'aktif';
            const loginUrl = copyBtn.dataset.loginUrl || window.location.origin + '/login';

            let statusText = 'Demi keamanan akun dinas, Anda wajib mengganti kata sandi ini saat pertama kali login ke portal MagKom.';
            if (status === 'changed') {
                statusText = 'Staf telah memperbarui kata sandi pribadinya di portal. Kata sandi awal ini sudah kadaluwarsa/tidak berlaku lagi untuk login.';
            }

            const sandiDisplay = sandi ? sandi : '(Tidak tersimpan / Akun lama)';

            const textToCopy = `*AKUN STAF INTERNAL DISKOMINFO SLEMAN (MAGKOM)*
Nama: ${nama}
Email: ${email}
Peran Akses: ${role}
Kata Sandi Awal: ${sandiDisplay}
Tautan Login: ${loginUrl}

*Catatan:* ${statusText}`;

            try {
                if (navigator.clipboard && window.isSecureContext) {
                    await navigator.clipboard.writeText(textToCopy);
                } else {
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

                const originalHTML = copyBtn.innerHTML;
                copyBtn.classList.add('is-copied');
                copyBtn.innerHTML = `
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    <span>Tersalin!</span>
                `;

                setTimeout(() => {
                    copyBtn.classList.remove('is-copied');
                    copyBtn.innerHTML = originalHTML;
                }, 2500);

                if (MagKomToast) {
                    MagKomToast.show({
                        type: 'success',
                        title: 'Kredensial Disalin!',
                        message: `Format akun untuk ${nama} berhasil disalin ke clipboard.`,
                    });
                }
            } catch (err) {
                console.error('Gagal menyalin kredensial staf:', err);
            }
            return;
        }

        // Trigger Buka Modal Tambah
        const triggerTambah = event.target.closest('.btn-trigger-tambah-staf') || event.target.closest('#btnOpenTambahStaf');
        if (triggerTambah) {
            event.preventDefault();
            toggleBidangField('tambah');
            if (modalTambah) modalTambah.style.display = 'flex';
            return;
        }

        // Trigger Buka Modal Edit
        const triggerEdit = event.target.closest('.btn-trigger-edit-staf');
        if (triggerEdit) {
            event.preventDefault();
            const id = triggerEdit.dataset.id;
            const nama = triggerEdit.dataset.nama || '';
            const email = triggerEdit.dataset.email || '';
            const role = triggerEdit.dataset.role || 'pembimbing';
            const bidangId = triggerEdit.dataset.bidangId || '';
            const actionUrl = triggerEdit.dataset.actionUrl || `/superadmin/staf/${id}`;

            if (editNama) editNama.value = nama;
            if (editEmail) editEmail.value = email;
            if (editRole) editRole.value = role;
            if (editBidang) editBidang.value = bidangId;
            toggleBidangField('edit');
            if (formEdit) formEdit.action = actionUrl;
            if (modalEdit) modalEdit.style.display = 'flex';
            return;
        }

        // Trigger Buka Modal Reset Sandi
        const triggerReset = event.target.closest('.btn-trigger-reset-staf');
        if (triggerReset) {
            event.preventDefault();
            const id = triggerReset.dataset.id;
            const nama = triggerReset.dataset.nama || '';
            const actionUrl = triggerReset.dataset.actionUrl || `/superadmin/staf/${id}/reset-password`;

            if (resetNama) resetNama.textContent = nama;
            if (formReset) formReset.action = actionUrl;
            if (modalReset) modalReset.style.display = 'flex';
            return;
        }

        // Trigger Buka Modal Konfirmasi Sandi Baru (BR-15 Instant Regenerate)
        const triggerSandiBaru = event.target.closest('.btn-trigger-sandi-baru');
        if (triggerSandiBaru) {
            event.preventDefault();
            const nama = triggerSandiBaru.dataset.nama || 'Staf';
            const email = triggerSandiBaru.dataset.email || '-';
            const actionUrl = triggerSandiBaru.dataset.actionUrl;

            const formSandiBaru = document.getElementById('formSandiBaru');
            const namaEl = document.getElementById('sandiBaruNama');
            const emailEl = document.getElementById('sandiBaruEmail');

            if (formSandiBaru && actionUrl) formSandiBaru.action = actionUrl;
            if (namaEl) namaEl.textContent = nama;
            if (emailEl) emailEl.textContent = email;
            if (modalSandiBaru) modalSandiBaru.style.display = 'flex';
            return;
        }

        // Trigger Buka Modal Konfirmasi Toggle Status Staf (Nonaktifkan / Aktifkan)
        const triggerToggle = event.target.closest('.btn-trigger-toggle-staf');
        if (triggerToggle) {
            event.preventDefault();
            const id = triggerToggle.dataset.id;
            const nama = triggerToggle.dataset.nama || 'Staf';
            const email = triggerToggle.dataset.email || '-';
            const isAktif = triggerToggle.dataset.statusAktif === '1';
            const actionUrl = triggerToggle.dataset.actionUrl || `/superadmin/staf/${id}/toggle`;

            const formToggle = document.getElementById('formToggleStaf');
            const titleEl = document.getElementById('modalToggleStafTitle');
            const namaEl = document.getElementById('toggleStafNama');
            const emailEl = document.getElementById('toggleStafEmail');
            const msgEl = document.getElementById('toggleStafMessage');
            const boxAlert = document.getElementById('toggleStafBoxAlert');
            const iconWrap = document.getElementById('toggleStafIconWrap');
            const consequenceEl = document.getElementById('toggleStafConsequence');
            const submitBtn = document.getElementById('btnSubmitToggleStaf');
            const submitText = document.getElementById('btnSubmitToggleText');

            if (formToggle) formToggle.action = actionUrl;
            if (namaEl) namaEl.textContent = nama;
            if (emailEl) emailEl.textContent = email;

            if (isAktif) {
                if (titleEl) titleEl.textContent = 'Konfirmasi Penonaktifan Staf';
                if (msgEl) msgEl.textContent = 'Apakah Anda yakin ingin menonaktifkan akun staf berikut?';
                if (consequenceEl) consequenceEl.textContent = '• Sesi aktif staf akan dicabut seketika dan staf tidak dapat login ke portal hingga diaktifkan kembali.';
                if (boxAlert) { boxAlert.style.background = '#FFF1F2'; boxAlert.style.borderColor = '#FECDD3'; }
                if (iconWrap) { iconWrap.style.background = '#FEE2E2'; iconWrap.style.color = '#DC2626'; }
                if (submitBtn) { submitBtn.style.background = '#DC2626'; }
                if (submitText) submitText.textContent = 'Ya, Nonaktifkan Staf';
            } else {
                if (titleEl) titleEl.textContent = 'Konfirmasi Pengaktifan Staf';
                if (msgEl) msgEl.textContent = 'Apakah Anda yakin ingin mengaktifkan kembali akun staf berikut?';
                if (consequenceEl) consequenceEl.textContent = '• Akun staf akan dipulihkan dan staf dapat kembali login ke portal internal MagKom.';
                if (boxAlert) { boxAlert.style.background = '#F0FDF4'; boxAlert.style.borderColor = '#BBF7D0'; }
                if (iconWrap) { iconWrap.style.background = '#DCFCE7'; iconWrap.style.color = '#16A34A'; }
                if (submitBtn) { submitBtn.style.background = '#16A34A'; }
                if (submitText) submitText.textContent = 'Ya, Aktifkan Staf';
            }

            if (modalToggle) modalToggle.style.display = 'flex';
            return;
        }

        // Trigger Tutup Modal via data-modal-dismiss
        const dismissBtn = event.target.closest('[data-modal-dismiss]');
        if (dismissBtn) {
            event.preventDefault();
            const targetId = dismissBtn.dataset.modalDismiss;
            const targetModal = document.getElementById(targetId);
            if (targetModal) targetModal.style.display = 'none';
            return;
        }

        // Tutup jika klik backdrop
        if (event.target === modalTambah) modalTambah.style.display = 'none';
        if (event.target === modalReset) modalReset.style.display = 'none';
        if (event.target === modalEdit) modalEdit.style.display = 'none';
        if (event.target === modalToggle) modalToggle.style.display = 'none';
        if (event.target === modalSandiBaru) modalSandiBaru.style.display = 'none';
    });

    // Keyboard ESC untuk menutup modal
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            if (modalTambah) modalTambah.style.display = 'none';
            if (modalReset) modalReset.style.display = 'none';
            if (modalEdit) modalEdit.style.display = 'none';
            if (modalToggle) modalToggle.style.display = 'none';
            if (modalSandiBaru) modalSandiBaru.style.display = 'none';
        }
    });
});

