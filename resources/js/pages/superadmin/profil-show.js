/**
 * Modul Profil SuperAdmin
 * - Penanganan upload dan preview avatar pasfoto
 * - Validasi ukuran berkas client-side (maksimal 2 MB)
 * - State tombol submit loading
 */
document.addEventListener('DOMContentLoaded', () => {
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
            if (file.size > 2 * 1024 * 1024) {
                if (window.MagKomToast) {
                    window.MagKomToast.warning('Ukuran Pasfoto Terlalu Besar', 'Ukuran berkas pasfoto melebihi batas maksimal 2 MB.');
                } else {
                    alert('Ukuran berkas pasfoto melebihi batas maksimal 2 MB.');
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
        if (confirm('Apakah Anda yakin ingin menghapus foto profil dan kembali menggunakan inisial bawaan?')) {
            if (inputHapusFoto) inputHapusFoto.value = '1';
            if (inputFoto) inputFoto.value = '';
            if (previewImg) {
                previewImg.src = '';
                previewImg.style.display = 'none';
            }
            if (fallbackIcon) {
                fallbackIcon.style.display = 'flex';
            }
            if (selectedFileName) selectedFileName.textContent = '(Foto akan dihapus saat Anda menekan tombol Simpan)';
        }
    });

    document.getElementById('formProfilStaff')?.addEventListener('submit', function () {
        const btn = document.getElementById('btnSimpanProfil');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = `
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="animation: spin 1s linear infinite;">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" style="opacity: 0.25;"></circle>
                    <path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" style="opacity: 0.75;"></path>
                </svg>
                <span>Menyimpan Data...</span>
            `;
        }
    });
});

