/**
 * MAGKOM - Profil & Data Staf Administrasi (ES Module)
 * Bertanggung jawab menangani preview pasfoto, validasi client-side,
 * toggle persetujuan data, dan feedback submit.
 */

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('formProfilStaff');
    const inputFoto = document.getElementById('inputFotoProfil');
    const inputHapusFoto = document.getElementById('inputHapusFoto');
    const btnUpload = document.getElementById('btnUploadPhoto');
    const cameraOverlay = document.getElementById('cameraOverlayBtn');
    const btnRemovePhoto = document.getElementById('btnRemovePhoto');
    const avatarImg = document.getElementById('avatarImg');
    const avatarInitial = document.getElementById('avatarInitial');
    const photoInfoText = document.getElementById('photoInfoText');
    const checkKonfirmasi = document.getElementById('checkKonfirmasiProfil');
    const btnSubmit = document.getElementById('btnSimpanProfil');

    if (!form) return;

    // 1. Pemicu dialog pemilihan file
    const triggerFileInput = () => {
        if (inputFoto) inputFoto.click();
    };

    if (btnUpload) {
        btnUpload.addEventListener('click', triggerFileInput);
    }

    if (cameraOverlay) {
        cameraOverlay.addEventListener('click', triggerFileInput);
    }

    // 2. Penanganan pemilihan berkas pasfoto
    if (inputFoto) {
        inputFoto.addEventListener('change', (e) => {
            const file = e.target.files && e.target.files[0];
            if (!file) return;

            // Validasi ukuran: Maksimal 2 MB
            const maxBytes = 2 * 1024 * 1024;
            if (file.size > maxBytes) {
                alert('Ukuran pasfoto terlalu besar. Maksimal ukuran berkas adalah 2 MB.');
                inputFoto.value = '';
                return;
            }

            // Validasi format
            const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
            if (!validTypes.includes(file.type)) {
                alert('Format berkas tidak didukung. Harap gunakan JPG, PNG, atau WEBP.');
                inputFoto.value = '';
                return;
            }

            // Reset status hapus foto
            if (inputHapusFoto) {
                inputHapusFoto.value = '0';
            }

            // Render pratinjau instan via FileReader
            const reader = new FileReader();
            reader.onload = (event) => {
                if (avatarImg) {
                    avatarImg.src = event.target.result;
                    avatarImg.style.display = 'block';
                }
                if (avatarInitial) {
                    avatarInitial.style.display = 'none';
                }
                if (photoInfoText) {
                    photoInfoText.textContent = `${file.name} (${(file.size / 1024).toFixed(0)} KB)`;
                }
                if (btnRemovePhoto) {
                    btnRemovePhoto.style.display = 'inline-block';
                    btnRemovePhoto.textContent = 'Batalkan Foto Baru';
                }
            };
            reader.readAsDataURL(file);
        });
    }

    // 3. Penanganan hapus pasfoto
    if (btnRemovePhoto) {
        btnRemovePhoto.addEventListener('click', () => {
            if (inputFoto && inputFoto.value) {
                // Batalkan foto yang baru dipilih
                inputFoto.value = '';
                if (inputHapusFoto) inputHapusFoto.value = '0';
                if (photoInfoText) photoInfoText.textContent = '';
                
                // Kembalikan ke state awal
                const originalUrl = avatarImg?.getAttribute('data-original-url');
                if (originalUrl) {
                    if (avatarImg) {
                        avatarImg.src = originalUrl;
                        avatarImg.style.display = 'block';
                    }
                    if (avatarInitial) avatarInitial.style.display = 'none';
                    btnRemovePhoto.textContent = 'Hapus Pasfoto';
                } else {
                    if (avatarImg) {
                        avatarImg.src = '';
                        avatarImg.style.display = 'none';
                    }
                    if (avatarInitial) avatarInitial.style.display = 'block';
                    btnRemovePhoto.style.display = 'none';
                }
            } else {
                // Konfirmasi hapus foto yang sudah tersimpan di server
                const confirmDelete = confirm('Apakah Anda yakin ingin menghapus pasfoto profil kedinasan Anda?');
                if (!confirmDelete) return;

                if (inputHapusFoto) inputHapusFoto.value = '1';
                if (avatarImg) {
                    avatarImg.src = '';
                    avatarImg.style.display = 'none';
                }
                if (avatarInitial) {
                    avatarInitial.style.display = 'block';
                }
                if (photoInfoText) {
                    photoInfoText.textContent = 'Pasfoto akan dihapus saat Anda menekan tombol simpan.';
                }
                btnRemovePhoto.style.display = 'none';
            }
        });
    }

    // 4. Checkbox Konfirmasi Kebenaran Data (Toggle disabled tombol submit)
    if (checkKonfirmasi && btnSubmit) {
        const toggleSubmitState = () => {
            btnSubmit.disabled = !checkKonfirmasi.checked;
        };

        checkKonfirmasi.addEventListener('change', toggleSubmitState);
        // Inisialisasi awal
        toggleSubmitState();
    }

    // 5. Submit loading state & pencegahan double-submission
    form.addEventListener('submit', () => {
        if (btnSubmit) {
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = `
                <svg class="profil-spinner" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10" stroke-opacity="0.25"></circle>
                    <path d="M4 12a8 8 0 018-8"></path>
                </svg>
                <span>Menyimpan Perubahan...</span>
            `;
        }
    });
});

