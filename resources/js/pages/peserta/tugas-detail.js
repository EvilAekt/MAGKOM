/**
 * =========================================================================
 * MAGKOM PESERTA: DETAIL TUGAS & CLASSROOM TURN-IN LOGIC
 * Mengelola Dropdown Tambah Berkas, Modal Link, Modal File (Upload & Recent)
 * =========================================================================
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Elements Referensi
    const formProgres = document.getElementById('formProgresTugas');
    const btnToggleDropdown = document.getElementById('btnToggleDropdown');
    const menuDropdown = document.getElementById('menuTambahBerkas');
    const btnSerahkan = document.getElementById('btnSerahkanTugas');
    const attachmentsList = document.getElementById('attachmentsList');
    const privateCommentInput = document.getElementById('privateCommentInput');

    // Hidden form inputs
    const formFileInput = document.getElementById('formFileInput');
    const formLinkInput = document.getElementById('formLinkInput');
    const formRecentPathInput = document.getElementById('formRecentPathInput');
    const formCatatanInput = document.getElementById('formCatatanInput');

    // Modal Link Elements
    const modalLink = document.getElementById('modalTambahLink');
    const inputUrlLink = document.getElementById('inputUrlLink');
    const btnSubmitLink = document.getElementById('btnSubmitLink');

    // Modal File Elements
    const modalFile = document.getElementById('modalSisipkanBerkas');
    const tabBtnUpload = document.getElementById('tabBtnUpload');
    const tabBtnRecent = document.getElementById('tabBtnRecent');
    const tabPanelUpload = document.getElementById('tabPanelUpload');
    const tabPanelRecent = document.getElementById('tabPanelRecent');
    const dropzoneBox = document.getElementById('fileDropzoneBox');
    const modalFileInput = document.getElementById('modalFileInput');
    const btnBrowse = document.getElementById('btnBrowseFile');
    const btnInsertRecent = document.getElementById('btnInsertRecent');

    // State Internal
    let activeItem = {
        type: null, // 'file' | 'link' | 'recent'
        name: null,
        meta: null,
        value: null,
    };
    let tempSelectedRecent = null;

    // Helper: Perbarui Tombol Serahkan & UI List
    function updateStateUI() {
        if (!attachmentsList || !btnSerahkan) return;

        attachmentsList.innerHTML = '';

        if (activeItem.type) {
            const card = document.createElement('div');
            card.className = 'attached-item-card';

            let iconSvg = '';
            if (activeItem.type === 'link') {
                iconSvg = `<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                </svg>`;
            } else {
                iconSvg = `<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                </svg>`;
            }

            card.innerHTML = `
                <div class="attached-item-left">
                    <div class="attached-item-icon">${iconSvg}</div>
                    <div class="attached-item-info">
                        <div class="attached-item-name" title="${activeItem.name}">${activeItem.name}</div>
                        <div class="attached-item-meta">${activeItem.meta || ''}</div>
                    </div>
                </div>
                <button type="button" class="attached-item-remove" id="btnRemoveAttachment" title="Hapus lampiran">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            `;

            attachmentsList.appendChild(card);

            // Pasang listener hapus
            card.querySelector('#btnRemoveAttachment')?.addEventListener('click', clearAttachment);

            // Aktifkan tombol Serahkan
            btnSerahkan.classList.remove('disabled');
            btnSerahkan.classList.add('active');
            btnSerahkan.removeAttribute('disabled');
        } else {
            // Nonaktifkan tombol Serahkan jika belum ada lampiran
            btnSerahkan.classList.remove('active');
            btnSerahkan.classList.add('disabled');
            btnSerahkan.setAttribute('disabled', 'disabled');
        }
    }

    function clearAttachment() {
        activeItem = { type: null, name: null, meta: null, value: null };
        if (formFileInput) formFileInput.value = '';
        if (formLinkInput) formLinkInput.value = '';
        if (formRecentPathInput) formRecentPathInput.value = '';
        if (modalFileInput) modalFileInput.value = '';
        updateStateUI();
    }

    // =====================================================================
    // 2. DROPDOWN TOGGLE (+ Tambah Berkas)
    // =====================================================================
    btnToggleDropdown?.addEventListener('click', (e) => {
        e.stopPropagation();
        menuDropdown?.classList.toggle('show');
    });

    document.addEventListener('click', (e) => {
        if (!menuDropdown?.contains(e.target) && !btnToggleDropdown?.contains(e.target)) {
            menuDropdown?.classList.remove('show');
        }
    });

    // Action Dropdown Items
    document.querySelectorAll('[data-action="open-modal-link"]').forEach((el) => {
        el.addEventListener('click', () => {
            menuDropdown?.classList.remove('show');
            openModal(modalLink);
            if (inputUrlLink) {
                inputUrlLink.focus();
                inputUrlLink.select();
            }
        });
    });

    document.querySelectorAll('[data-action="open-modal-file"]').forEach((el) => {
        el.addEventListener('click', () => {
            menuDropdown?.classList.remove('show');
            openModal(modalFile);
            switchTab('upload');
        });
    });

    // =====================================================================
    // 3. MODAL UTILITIES
    // =====================================================================
    function openModal(modalEl) {
        if (!modalEl) return;
        modalEl.classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function closeModal(modalEl) {
        if (!modalEl) return;
        modalEl.classList.remove('show');
        document.body.style.overflow = '';
    }

    // Pasang tombol close & backdrop click untuk semua modal
    document.querySelectorAll('.classroom-modal-backdrop').forEach((backdrop) => {
        backdrop.addEventListener('click', (e) => {
            if (e.target === backdrop) {
                closeModal(backdrop);
            }
        });

        backdrop.querySelectorAll('[data-action="close-modal"]').forEach((btn) => {
            btn.addEventListener('click', () => {
                closeModal(backdrop);
            });
        });
    });

    // =====================================================================
    // 4. MODAL LINK LOGIC
    // =====================================================================
    inputUrlLink?.addEventListener('input', () => {
        const val = inputUrlLink.value.trim();
        if (val && isValidHttpUrl(val)) {
            btnSubmitLink?.removeAttribute('disabled');
        } else {
            btnSubmitLink?.setAttribute('disabled', 'disabled');
        }
    });

    btnSubmitLink?.addEventListener('click', () => {
        const urlVal = inputUrlLink.value.trim();
        if (!urlVal || !isValidHttpUrl(urlVal)) {
            alert('Silakan masukkan format URL yang valid (contoh: https://...)');
            return;
        }

        // Simpan state Link
        activeItem = {
            type: 'link',
            name: urlVal,
            meta: 'Tautan Tugas / Demo',
            value: urlVal,
        };

        if (formLinkInput) formLinkInput.value = urlVal;
        if (formFileInput) formFileInput.value = '';
        if (formRecentPathInput) formRecentPathInput.value = '';

        closeModal(modalLink);
        updateStateUI();
    });

    function isValidHttpUrl(string) {
        let url;
        try {
            url = new URL(string);
        } catch (_) {
            return false;
        }
        return url.protocol === 'http:' || url.protocol === 'https:';
    }

    // =====================================================================
    // 5. MODAL FILE LOGIC (TABS, UPLOAD & RECENT)
    // =====================================================================
    function switchTab(tabName) {
        if (tabName === 'upload') {
            tabBtnUpload?.classList.add('active');
            tabBtnRecent?.classList.remove('active');
            tabPanelUpload?.classList.add('active');
            tabPanelRecent?.classList.remove('active');
        } else {
            tabBtnRecent?.classList.add('active');
            tabBtnUpload?.classList.remove('active');
            tabPanelRecent?.classList.add('active');
            tabPanelUpload?.classList.remove('active');
        }
    }

    tabBtnUpload?.addEventListener('click', () => switchTab('upload'));
    tabBtnRecent?.addEventListener('click', () => switchTab('recent'));

    // Trigger browse
    btnBrowse?.addEventListener('click', () => modalFileInput?.click());
    dropzoneBox?.addEventListener('click', (e) => {
        if (e.target !== btnBrowse) {
            modalFileInput?.click();
        }
    });

    // Drag and Drop
    ['dragenter', 'dragover'].forEach((eventName) => {
        dropzoneBox?.addEventListener(eventName, (e) => {
            e.preventDefault();
            e.stopPropagation();
            dropzoneBox.classList.add('drag-over');
        });
    });

    ['dragleave', 'drop'].forEach((eventName) => {
        dropzoneBox?.addEventListener(eventName, (e) => {
            e.preventDefault();
            e.stopPropagation();
            dropzoneBox.classList.remove('drag-over');
        });
    });

    dropzoneBox?.addEventListener('drop', (e) => {
        const dt = e.dataTransfer;
        const files = dt?.files;
        if (files && files.length > 0) {
            handleSelectedFile(files[0]);
        }
    });

    modalFileInput?.addEventListener('change', (e) => {
        const input = e.target;
        if (input.files && input.files[0]) {
            handleSelectedFile(input.files[0]);
        }
    });

    function handleSelectedFile(file) {
        // Validasi ukuran maks 5MB
        if (file.size > 5 * 1024 * 1024) {
            alert('Ukuran berkas melebihi batas maksimal 5MB.');
            return;
        }

        const sizeFormatted = file.size > 1048576
            ? (file.size / 1048576).toFixed(2) + ' MB'
            : (file.size / 1024).toFixed(1) + ' KB';

        activeItem = {
            type: 'file',
            name: file.name,
            meta: `Berkas Komputer • ${sizeFormatted}`,
            value: file,
        };

        // Salin file ke form input utama menggunakan DataTransfer API
        if (formFileInput) {
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            formFileInput.files = dataTransfer.files;
        }
        if (formLinkInput) formLinkInput.value = '';
        if (formRecentPathInput) formRecentPathInput.value = '';

        closeModal(modalFile);
        updateStateUI();
    }

    // Seleksi Berkas Recent
    const recentCards = document.querySelectorAll('.recent-file-card');
    recentCards.forEach((card) => {
        card.addEventListener('click', () => {
            recentCards.forEach((c) => c.classList.remove('selected'));
            card.classList.add('selected');
            tempSelectedRecent = {
                path: card.dataset.filePath,
                name: card.dataset.fileName,
                size: card.dataset.fileSize,
            };
            btnInsertRecent?.removeAttribute('disabled');
        });
    });

    btnInsertRecent?.addEventListener('click', () => {
        if (!tempSelectedRecent) return;

        activeItem = {
            type: 'recent',
            name: tempSelectedRecent.name,
            meta: `Riwayat Berkas • ${tempSelectedRecent.size}`,
            value: tempSelectedRecent.path,
        };

        if (formRecentPathInput) formRecentPathInput.value = tempSelectedRecent.path;
        if (formFileInput) formFileInput.value = '';
        if (formLinkInput) formLinkInput.value = '';

        closeModal(modalFile);
        updateStateUI();
    });

    // Toggle Private Comment Area
    const btnTogglePrivateComment = document.getElementById('btnTogglePrivateComment');
    const wrapPrivateComment = document.getElementById('wrapPrivateComment');

    if (privateCommentInput && privateCommentInput.value.trim().length > 0) {
        if (wrapPrivateComment) wrapPrivateComment.style.display = 'block';
        if (btnTogglePrivateComment) btnTogglePrivateComment.style.display = 'none';
    }

    btnTogglePrivateComment?.addEventListener('click', () => {
        if (wrapPrivateComment) wrapPrivateComment.style.display = 'block';
        if (btnTogglePrivateComment) btnTogglePrivateComment.style.display = 'none';
        privateCommentInput?.focus();
    });

    // =====================================================================
    // 6. SUBMISSION FORM PROGRESS
    // =====================================================================
    btnSerahkan?.addEventListener('click', () => {
        if (!activeItem.type) return;

        // Sinkronisasi komentar pribadi ke formCatatanInput
        if (privateCommentInput && formCatatanInput) {
            const comment = privateCommentInput.value.trim();
            formCatatanInput.value = comment || 'Mengumpulkan progres pekerjaan tugas.';
        }

        btnSerahkan.classList.add('disabled');
        btnSerahkan.setAttribute('disabled', 'disabled');
        btnSerahkan.innerHTML = `
            <span style="display:inline-block;width:14px;height:14px;border:2px solid #fff;border-top-color:transparent;border-radius:50%;animation:spinClassroom 0.6s linear infinite;margin-right:8px;"></span>
            <span>Menyerahkan...</span>
        `;

        formProgres?.submit();
    });
});

