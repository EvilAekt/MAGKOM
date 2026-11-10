/**
 * MagKom SuperAdmin - Manajemen Slot Khusus & Kredensial Peserta Jalur Khusus
 * Mematuhi SEC-01 & SEC-02 (Anti-XSS, Secure Clipboard Handling)
 */

import { MagKomToast } from '../../components/toast.js';

document.addEventListener('DOMContentLoaded', () => {
    initCredentialCopyHandlers();
});

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

