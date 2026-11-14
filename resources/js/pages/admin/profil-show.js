/**
 * MagKom - Modul Interaktif Halaman Profil Staf Administrator
 * Menangani preview avatar, scroll & focus tombol Edit,
 * validasi checkbox persetujuan, dan proteksi submit ganda.
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Inisialisasi Elemen Avatar & Upload
    const inputFoto = document.getElementById('inputFotoProfil');
    const btnPilihFoto = document.getElementById('btnPilihFoto');
    const btnPilihFotoOverlay = document.getElementById('btnPilihFotoOverlay');
    const btnHapusFoto = document.getElementById('btnHapusFoto');
    const inputHapusFoto = document.getElementById('inputHapusFoto');
    const previewImg = document.getElementById('avatarPreviewImg');
    const fallbackIcon = document.getElementById('avatarFallbackIcon');
    const selectedFileName = document.getElementById('selectedFileName');

    const triggerUpload = () => inputFoto?.click();
    btnPilihFoto?.addEventListener('click', triggerUpload);
    btnPilihFotoOverlay?.addEventListener('click', triggerUpload);

    inputFoto?.addEventListener('change', function () {
        if (this.files && this.files[0]) {
            const file = this.files[0];

            // Validasi ukuran maksimal 2 MB
            if (file.size > 2 * 1024 * 1024) {
                if (window.MagKomToast) {
                    window.MagKomToast.warning('Ukuran Berkas Terlalu Besar', 'Ukuran pasfoto melebihi batas maksimal 2 MB.');
                } else {
                    alert('Ukuran pasfoto melebihi batas maksimal 2 MB.');
                }
                this.value = '';
                return;
            }

            // Validasi format file
            const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
            if (!validTypes.includes(file.type)) {
                if (window.MagKomToast) {
                    window.MagKomToast.warning('Format Tidak Didukung', 'Silakan unggah pasfoto dalam format JPG, PNG, atau WEBP.');
                } else {
                    alert('Silakan unggah pasfoto dalam format JPG, PNG, atau WEBP.');
                }
                this.value = '';
                return;
            }

            if (inputHapusFoto) inputHapusFoto.value = '0';
            if (selectedFileName) selectedFileName.textContent = '✓ ' + file.name;

            const reader = new FileReader();
            reader.onload = function (e) {
                if (previewImg) {
                    previewImg.src = e.target.result;
                    previewImg.style.display = 'block';
                }
                if (fallbackIcon) {
                    fallbackIcon.style.display = 'none';
                }
            };
            reader.readAsDataURL(file);
        }
    });

    btnHapusFoto?.addEventListener('click', function () {
        if (confirm('Apakah Anda yakin ingin menghapus pasfoto profil dan kembali menggunakan inisial bawaan?')) {
            if (inputHapusFoto) inputHapusFoto.value = '1';
            if (inputFoto) inputFoto.value = '';
            if (previewImg) {
                previewImg.src = '';
                previewImg.style.display = 'none';
            }
            if (fallbackIcon) {
                fallbackIcon.style.display = 'block';
            }
            if (selectedFileName) {
                selectedFileName.textContent = 'Foto akan dihapus saat disimpan';
                selectedFileName.style.color = '#EF4444';
            }
        }
    });

    // 2. Tombol Edit Scroll & Smooth Focus
    const btnEditIdentitas = document.getElementById('btnEditIdentitas');
    const btnEditKontak = document.getElementById('btnEditKontak');
    const inputNama = document.getElementById('nama');
    const inputNoTelp = document.getElementById('no_telp');

    btnEditIdentitas?.addEventListener('click', () => {
        if (inputNama) {
            inputNama.scrollIntoView({ behavior: 'smooth', block: 'center' });
            inputNama.focus();
        }
    });

    btnEditKontak?.addEventListener('click', () => {
        if (inputNoTelp) {
            inputNoTelp.scrollIntoView({ behavior: 'smooth', block: 'center' });
            inputNoTelp.focus();
        }
    });

    // 3. Checkbox Persetujuan & Tombol Submit
    const checkPersetujuan = document.getElementById('checkPersetujuan');
    const btnSubmitProfil = document.getElementById('btnSubmitProfil');
    const formProfilStaff = document.getElementById('formProfilStaff');

    checkPersetujuan?.addEventListener('change', function () {
        if (btnSubmitProfil) {
            btnSubmitProfil.disabled = !this.checked;
        }
    });

    // 4. Proteksi Double Submit
    formProfilStaff?.addEventListener('submit', function () {
        if (btnSubmitProfil) {
            btnSubmitProfil.disabled = true;
            btnSubmitProfil.innerHTML = `
                <svg class="profil-spinner" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10" stroke-opacity="0.25"></circle>
                    <path d="M12 2a10 10 0 0 1 10 10" stroke-linecap="round"></path>
                </svg>
                <span>Menyimpan...</span>
            `;
        }
    });
});
