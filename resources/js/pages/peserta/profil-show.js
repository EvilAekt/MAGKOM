/**
 * MAGKOM - Profil & Biodata Peserta Script
 * Modular ES Module (Vite Asset Pipeline)
 * Mematuhi Aturan AGENTS.md (No inline scripts, DOM-safe, Accessible)
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Inisialisasi Elemen Form & DOM
    const form = document.getElementById('formBiodataPeserta');
    if (!form) return;

    const fileInput = document.getElementById('inputFotoProfil');
    const inputHapusFoto = document.getElementById('inputHapusFoto');
    const btnUploadPhoto = document.getElementById('btnUploadPhoto');
    const cameraOverlay = document.getElementById('cameraOverlayBtn');
    const btnRemovePhoto = document.getElementById('btnRemovePhoto');
    const avatarImg = document.getElementById('avatarImg');
    const avatarInitial = document.getElementById('avatarInitial');
    const photoInfoText = document.getElementById('photoInfoText');

    // Section Elements & Edit Buttons
    const sectionAkademik = document.getElementById('sectionAkademik');
    const btnEditAkademik = document.getElementById('btnEditAkademik');

    const sectionKontak = document.getElementById('sectionKontak');
    const btnEditKontak = document.getElementById('btnEditKontak');

    // Field Inputs untuk Kelengkapan & Validasi
    const inputNama = document.getElementById('nama');
    const inputEmail = document.getElementById('email');
    const inputInstitusi = document.getElementById('institusi');
    const inputNimNisn = document.getElementById('nim_nisn');
    const selectJenjang = document.getElementById('jenjang');
    const selectJurusan = document.getElementById('jurusan_id');
    const inputPembimbing = document.getElementById('nama_pembimbing_sekolah');

    const inputNoHp = document.getElementById('no_hp');
    const textareaAlamat = document.getElementById('alamat');
    const inputNamaDarurat = document.getElementById('nama_kontak_darurat');
    const inputNoDarurat = document.getElementById('no_kontak_darurat');

    // Agreement Checkbox & Submit Button
    const checkboxAgreement = document.getElementById('checkboxAgreement');
    const btnSubmitProfil = document.getElementById('btnSubmitProfil');

    // Dynamic Kelengkapan Elements
    const completenessPercentText = document.getElementById('completenessPercentText');
    const completenessProgressBar = document.getElementById('completenessProgressBar');

    // SVGs untuk Checklist Icons
    const ICON_DONE = `
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" class="profil-checklist-icon done" xmlns="http://www.w3.org/2000/svg">
            <circle cx="8" cy="8" r="8" fill="#02275D"/>
            <path d="M4.5 8L7 10.5L11.5 5.5" stroke="white" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    `;

    const ICON_PENDING = `
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" class="profil-checklist-icon pending" xmlns="http://www.w3.org/2000/svg">
            <circle cx="8" cy="8" r="7" stroke="#94A3B8" stroke-width="1.75" fill="none"/>
        </svg>
    `;

    const ICON_PENCIL = `
        <span>Edit</span>
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
        </svg>
    `;

    const ICON_CANCEL = `
        <span>Batal</span>
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
    `;

    // 2. Setup Manajemen Edit Lock / Unlock
    const setupSectionLock = (sectionEl, buttonEl, fields, firstFocusEl) => {
        if (!sectionEl || !buttonEl) return;

        let isEditing = false;
        const defaultButtonHTML = buttonEl.innerHTML;
        const originalValues = new Map();

        // Rekam nilai awal setiap field
        const snapshotOriginalValues = () => {
            fields.forEach((field) => {
                if (field) {
                    originalValues.set(field, field.value);
                }
            });
        };

        const setFieldsLocked = (locked) => {
            fields.forEach((field) => {
                if (!field) return;
                // Field registrasi yang berstatus permanen terkunci tidak boleh di-unlock
                if (field.dataset.permanentLocked === 'true') {
                    if (field.tagName === 'SELECT') {
                        field.disabled = true;
                        field.classList.add('is-permanent-locked');
                    } else {
                        field.readOnly = true;
                        field.classList.add('is-permanent-locked');
                    }
                    return;
                }

                if (field.tagName === 'SELECT') {
                    field.disabled = locked;
                    field.classList.toggle('is-locked', locked);
                } else {
                    field.readOnly = locked;
                    field.classList.toggle('is-locked', locked);
                }
            });
        };

        const restoreOriginalValues = () => {
            fields.forEach((field) => {
                if (field && originalValues.has(field)) {
                    field.value = originalValues.get(field);
                }
            });
            recalculateCompleteness();
        };

        // Kunci secara default saat halaman siap
        snapshotOriginalValues();
        setFieldsLocked(true);

        buttonEl.addEventListener('click', () => {
            if (!isEditing) {
                // Masuk mode edit
                isEditing = true;
                setFieldsLocked(false);
                buttonEl.classList.add('is-editing');
                buttonEl.innerHTML = ICON_CANCEL;
                if (firstFocusEl) {
                    firstFocusEl.focus();
                }
            } else {
                // Batalkan perubahan dan kunci kembali
                isEditing = false;
                restoreOriginalValues();
                setFieldsLocked(true);
                buttonEl.classList.remove('is-editing');
                buttonEl.innerHTML = defaultButtonHTML;
            }
        });

        // Simpan nilai baru saat form disubmit
        form.addEventListener('submit', () => {
            setFieldsLocked(false);
        });
    };

    // Pasang lock pada kedua section
    setupSectionLock(
        sectionAkademik,
        btnEditAkademik,
        [inputInstitusi, inputNimNisn, selectJenjang, selectJurusan, inputPembimbing],
        inputPembimbing
    );

    setupSectionLock(
        sectionKontak,
        btnEditKontak,
        [inputNoHp, inputEmail, textareaAlamat, inputNamaDarurat, inputNoDarurat],
        inputNoHp
    );

    // Filter Jurusan Berdasarkan Jenjang Pendidikan (SMK vs Mahasiswa D3/S1)
    const filterJurusanByJenjang = (jenjangValue, shouldResetValue = false) => {
        if (!selectJurusan) return;

        const targetKategori = (jenjangValue === 'smk') ? 'smk' : (jenjangValue ? 'mahasiswa' : '');
        const options = selectJurusan.querySelectorAll('option');
        let isCurrentValueValid = false;

        options.forEach((opt) => {
            if (!opt.value) {
                opt.hidden = false;
                return;
            }

            const kategori = opt.getAttribute('data-kategori');
            if (!targetKategori || kategori === targetKategori) {
                opt.hidden = false;
                opt.disabled = false;
                if (opt.value === selectJurusan.value) {
                    isCurrentValueValid = true;
                }
            } else {
                opt.hidden = true;
                opt.disabled = true;
            }
        });

        if (shouldResetValue && !isCurrentValueValid) {
            selectJurusan.value = '';
        }
    };

    if (selectJenjang) {
        selectJenjang.addEventListener('change', () => {
            filterJurusanByJenjang(selectJenjang.value, true);
            recalculateCompleteness();
        });

        // Terapkan filter awal sesuai jenjang yang sudah terpilih
        if (selectJenjang.value) {
            filterJurusanByJenjang(selectJenjang.value, false);
        }
    }

    // 3. Upload Pasfoto & Live Preview
    const triggerFilePicker = () => {
        if (fileInput) fileInput.click();
    };

    btnUploadPhoto?.addEventListener('click', triggerFilePicker);
    cameraOverlay?.addEventListener('click', triggerFilePicker);

    fileInput?.addEventListener('change', function () {
        if (this.files && this.files[0]) {
            const file = this.files[0];
            const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];

            if (!allowedTypes.includes(file.type)) {
                showToast('Format foto profil harus berupa JPG, JPEG, PNG, atau WEBP.', 'warning');
                this.value = '';
                return;
            }

            if (file.size > 2 * 1024 * 1024) {
                showToast('Ukuran berkas melebihi batas maksimal 2 MB.', 'warning');
                this.value = '';
                return;
            }

            if (inputHapusFoto) inputHapusFoto.value = '0';

            const reader = new FileReader();
            reader.onload = (e) => {
                if (avatarImg) {
                    avatarImg.src = e.target.result;
                    avatarImg.style.display = 'block';
                }
                if (avatarInitial) {
                    avatarInitial.style.display = 'none';
                }
                if (photoInfoText) {
                    photoInfoText.textContent = `✓ ${file.name}`;
                }
                recalculateCompleteness();
            };
            reader.readAsDataURL(file);
        }
    });

    // Hapus Pasfoto
    btnRemovePhoto?.addEventListener('click', () => {
        if (confirm('Apakah Anda yakin ingin menghapus pasfoto profil dan kembali ke inisial default?')) {
            if (inputHapusFoto) inputHapusFoto.value = '1';
            if (fileInput) fileInput.value = '';
            if (avatarImg) {
                avatarImg.src = '';
                avatarImg.style.display = 'none';
            }
            if (avatarInitial) {
                avatarInitial.style.display = 'block';
            }
            if (photoInfoText) {
                photoInfoText.textContent = '(Pasfoto akan dihapus saat disimpan)';
            }
            recalculateCompleteness();
        }
    });

    // 4. Engine Real-Time Kelengkapan Biodata (5 Kategori)
    const updateChecklistItem = (id, isDone) => {
        const itemEl = document.getElementById(id);
        if (!itemEl) return;
        const iconContainer = itemEl.querySelector('.profil-checklist-icon-wrap');
        if (iconContainer) {
            iconContainer.innerHTML = isDone ? ICON_DONE : ICON_PENDING;
        }
    };

    const hasPhoto = () => {
        if (inputHapusFoto && inputHapusFoto.value === '1') {
            return false;
        }
        if (fileInput && fileInput.files && fileInput.files.length > 0) {
            return true;
        }
        if (avatarImg && avatarImg.getAttribute('src') && avatarImg.style.display !== 'none') {
            return true;
        }
        return false;
    };

    const recalculateCompleteness = () => {
        const hasNama = Boolean(inputNama?.value?.trim() || true);
        const hasEmail = Boolean(inputEmail?.value?.trim());
        const isAkunLengkap = hasNama && hasEmail;

        const isFotoLengkap = hasPhoto();

        const isAkademikLengkap = Boolean(
            inputInstitusi?.value?.trim() &&
            inputNimNisn?.value?.trim() &&
            selectJenjang?.value?.trim() &&
            selectJurusan?.value?.trim()
        );

        const isKontakLengkap = Boolean(inputNoHp?.value?.trim());

        const isDaruratLengkap = Boolean(
            inputNamaDarurat?.value?.trim() &&
            inputNoDarurat?.value?.trim()
        );

        updateChecklistItem('checkItemAkun', isAkunLengkap);
        updateChecklistItem('checkItemFoto', isFotoLengkap);
        updateChecklistItem('checkItemAkademik', isAkademikLengkap);
        updateChecklistItem('checkItemKontak', isKontakLengkap);
        updateChecklistItem('checkItemDarurat', isDaruratLengkap);

        const checklist = [isAkunLengkap, isFotoLengkap, isAkademikLengkap, isKontakLengkap, isDaruratLengkap];
        const terisi = checklist.filter(Boolean).length;
        const percentage = Math.round((terisi / checklist.length) * 100);

        if (completenessPercentText) {
            completenessPercentText.textContent = `${percentage}%`;
        }

        if (completenessProgressBar) {
            completenessProgressBar.style.width = `${Math.max(6, percentage)}%`;
        }

        updateSubmitState();
    };

    // 5. Validasi Checkbox Persetujuan & Status Tombol Simpan
    const updateSubmitState = () => {
        if (!btnSubmitProfil || !checkboxAgreement) return;

        const isAgreed = checkboxAgreement.checked;
        const isRequiredValid = Boolean(
            inputInstitusi?.value?.trim() &&
            inputNimNisn?.value?.trim() &&
            selectJenjang?.value?.trim() &&
            selectJurusan?.value?.trim() &&
            inputNoHp?.value?.trim() &&
            inputEmail?.value?.trim()
        );

        btnSubmitProfil.disabled = !(isAgreed && isRequiredValid);
    };

    checkboxAgreement?.addEventListener('change', updateSubmitState);

    // Pasang listener input/change pada seluruh field
    const watchedInputs = [
        inputEmail,
        inputInstitusi,
        inputNimNisn,
        selectJenjang,
        selectJurusan,
        inputNoHp,
        inputNamaDarurat,
        inputNoDarurat,
    ];

    watchedInputs.forEach((input) => {
        input?.addEventListener('input', recalculateCompleteness);
        input?.addEventListener('change', recalculateCompleteness);
    });

    // 6. Proteksi Double-Submit Form & Unlock All Fields Sebelum Kirim
    form.addEventListener('submit', (e) => {
        if (checkboxAgreement && !checkboxAgreement.checked) {
            e.preventDefault();
            showToast('Silakan centang pernyataan persetujuan sebelum menyimpan data.', 'warning');
            return;
        }

        // Pastikan semua select/input yang disabled diaktifkan kembali agar nilainya terkirim
        if (selectJenjang) selectJenjang.disabled = false;
        if (selectJurusan) selectJurusan.disabled = false;

        if (btnSubmitProfil) {
            btnSubmitProfil.disabled = true;
            btnSubmitProfil.innerHTML = `
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="profil-spinner">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" style="opacity: 0.25;"></circle>
                    <path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" style="opacity: 0.75;"></path>
                </svg>
                <span>Menyimpan Biodata Profil...</span>
            `;
        }
    });

    // Toast Helper
    function showToast(message, type = 'info') {
        if (window.MagKomToast && typeof window.MagKomToast[type] === 'function') {
            window.MagKomToast[type](message);
        } else {
            alert(message);
        }
    }

    // Jalankan kalkulasi awal saat halaman dimuat
    recalculateCompleteness();
});
