/**
 * =========================================================================
 * MAGKOM PESERTA: MODUL LOGBOOK & JURNAL MINGGUAN
 * Mengelola accordion, pencarian, dan sistem upload berkas/link
 * ala Pembimbing (Single Header, Selector Bulat & Attached Cards).
 * =========================================================================
 */

export function initLogbookIndex() {
    // -------------------------------------------------------------------------
    // 1. Accordion Toggle
    // -------------------------------------------------------------------------
    window.toggleCard = function (id) {
        const card = document.getElementById(`logbook-card-${id}`);
        if (!card) return;
        card.classList.toggle('is-expanded');
    };

    // -------------------------------------------------------------------------
    // 2. Char Counter
    // -------------------------------------------------------------------------
    window.updateCharCount = function (textarea, counterId) {
        const counter = document.getElementById(counterId);
        if (!counter) return;
        const len = (textarea.value || '').trim().length;
        if (len < 10) {
            counter.textContent = `${len} karakter (kurang ${10 - len} karakter)`;
            counter.style.color = '#DC2626';
        } else {
            counter.textContent = `${len} karakter (memenuhi syarat min. 10)`;
            counter.style.color = '#16A34A';
        }
    };

    // -------------------------------------------------------------------------
    // 3. Selection change minggu magang (Tambah)
    // -------------------------------------------------------------------------
    window.onMingguChange = function (mingguVal) {
        const select = document.getElementById('tambah_minggu_ke');
        const label = document.getElementById('labelRentangMinggu');
        const formFields = document.getElementById('formFieldsLogbook');
        const placeholder = document.getElementById('placeholderPilihMinggu');
        const alertSudahDiisi = document.getElementById('alertMingguSudahDiisi');
        const btnSubmit = document.getElementById('btnSubmitTambah');

        if (!mingguVal || mingguVal === '') {
            if (label) label.textContent = '';
            if (formFields) formFields.style.display = 'none';
            if (alertSudahDiisi) alertSudahDiisi.style.display = 'none';
            if (placeholder) placeholder.style.display = 'block';
            if (btnSubmit) btnSubmit.disabled = true;
            return;
        }

        const selectedOpt = select ? select.options[select.selectedIndex] : null;
        const periode = selectedOpt ? selectedOpt.getAttribute('data-periode') : '';
        const terisi = selectedOpt ? selectedOpt.getAttribute('data-terisi') === '1' : false;

        if (label) {
            label.textContent = periode ? `Periode: ${periode}` : '';
        }

        if (placeholder) placeholder.style.display = 'none';

        if (terisi) {
            if (formFields) formFields.style.display = 'none';
            if (alertSudahDiisi) alertSudahDiisi.style.display = 'block';
            if (btnSubmit) btnSubmit.disabled = true;
        } else {
            if (alertSudahDiisi) alertSudahDiisi.style.display = 'none';
            if (formFields) formFields.style.display = 'block';

            const textarea = document.getElementById('tambah_kegiatan');
            const textLen = (textarea?.value || '').trim().length;
            if (btnSubmit) btnSubmit.disabled = textLen < 10;
        }
    };

    // -------------------------------------------------------------------------
    // 4. Modal Utama (Tambah & Edit)
    // -------------------------------------------------------------------------
    const modalTambah = document.getElementById('modalTambahLogbook');
    const selectMinggu = document.getElementById('tambah_minggu_ke');
    const modalEdit = document.getElementById('modalEditLogbook');
    const formEdit = document.getElementById('formEditLogbook');
    const inputEditMinggu = document.getElementById('edit_minggu_ke');
    const inputEditTanggal = document.getElementById('edit_tanggal');
    const textareaEditKegiatan = document.getElementById('edit_kegiatan');
    const inputEditLink = document.getElementById('edit_link_laporan');
    const inputEditFile = document.getElementById('edit_file_laporan');
    const subtitleEdit = document.getElementById('modalEditSubtitle');

    // Context penunjuk target submodal ('tambah' atau 'edit')
    let currentAttachmentContext = 'tambah';
    let editExistingFileName = null;

    const MAX_FILE_SIZE = 2 * 1024 * 1024; // 2MB

    function updateLampiranMeta(context) {
        const metaEl = document.getElementById(`${context}LampiranMeta`);
        if (!metaEl) return;

        const fileInput = document.getElementById(`${context}_file_laporan`);
        const linkInput = document.getElementById(`${context}_link_laporan`);
        let count = 0;

        if (fileInput && fileInput.files && fileInput.files.length > 0) {
            count++;
        } else if (context === 'edit' && editExistingFileName) {
            count++;
        }

        if (linkInput && (linkInput.value || '').trim().length > 0) {
            count++;
        }

        if (count > 0) {
            metaEl.textContent = `(opsional) · ${count} lampiran terpasang`;
        } else {
            metaEl.textContent = '(opsional) · PDF (maks. 2MB) atau Link HTTPS';
        }
    }

    function renderFileCard(context, fileName, fileSizeText, onRemove) {
        const container = document.getElementById(`${context}AttachedFilesContainer`);
        if (!container) return;
        container.innerHTML = '';

        const card = document.createElement('div');
        card.className = 'logbook-attached-card';

        const info = document.createElement('div');
        info.className = 'logbook-attached-info';

        const icon = document.createElement('div');
        icon.className = 'logbook-attached-icon';
        icon.innerHTML = `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>`;

        const texts = document.createElement('div');
        texts.className = 'logbook-attached-texts';

        const title = document.createElement('div');
        title.className = 'logbook-attached-title';
        title.textContent = fileName;

        const sub = document.createElement('div');
        sub.className = 'logbook-attached-sub';
        sub.textContent = fileSizeText;

        texts.appendChild(title);
        texts.appendChild(sub);
        info.appendChild(icon);
        info.appendChild(texts);

        const btnRemove = document.createElement('button');
        btnRemove.type = 'button';
        btnRemove.className = 'btn-remove-logbook-attachment';
        btnRemove.title = 'Hapus berkas lampiran';
        btnRemove.innerHTML = '&times;';
        btnRemove.addEventListener('click', () => {
            container.innerHTML = '';
            if (typeof onRemove === 'function') onRemove();
            updateLampiranMeta(context);
        });

        card.appendChild(info);
        card.appendChild(btnRemove);
        container.appendChild(card);
        updateLampiranMeta(context);
    }

    function renderLinkCard(context, linkUrl, onRemove) {
        const container = document.getElementById(`${context}AttachedLinksContainer`);
        if (!container) return;
        container.innerHTML = '';

        const card = document.createElement('div');
        card.className = 'logbook-attached-card';

        const info = document.createElement('div');
        info.className = 'logbook-attached-info';

        const icon = document.createElement('div');
        icon.className = 'logbook-attached-icon';
        icon.innerHTML = `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>`;

        const texts = document.createElement('div');
        texts.className = 'logbook-attached-texts';

        const title = document.createElement('div');
        title.className = 'logbook-attached-title';
        title.textContent = linkUrl;

        const sub = document.createElement('div');
        sub.className = 'logbook-attached-sub';
        sub.textContent = 'Tautan Laporan (HTTPS)';

        texts.appendChild(title);
        texts.appendChild(sub);
        info.appendChild(icon);
        info.appendChild(texts);

        const btnRemove = document.createElement('button');
        btnRemove.type = 'button';
        btnRemove.className = 'btn-remove-logbook-attachment';
        btnRemove.title = 'Hapus tautan lampiran';
        btnRemove.innerHTML = '&times;';
        btnRemove.addEventListener('click', () => {
            container.innerHTML = '';
            if (typeof onRemove === 'function') onRemove();
            updateLampiranMeta(context);
        });

        card.appendChild(info);
        card.appendChild(btnRemove);
        container.appendChild(card);
        updateLampiranMeta(context);
    }

    window.openTambahModal = function (preSelectMinggu = null) {
        if (!modalTambah) return;
        currentAttachmentContext = 'tambah';
        modalTambah.classList.add('is-open');
        document.body.style.overflow = 'hidden';

        if (preSelectMinggu && selectMinggu) {
            selectMinggu.value = preSelectMinggu;
            window.onMingguChange(preSelectMinggu);
        } else if (selectMinggu && selectMinggu.value) {
            window.onMingguChange(selectMinggu.value);
        }

        const tambahFile = document.getElementById('tambah_file_laporan');
        if (tambahFile) tambahFile.value = '';
        const filesContainer = document.getElementById('tambahAttachedFilesContainer');
        if (filesContainer) filesContainer.innerHTML = '';

        const tambahLink = document.getElementById('tambah_link_laporan');
        const linksContainer = document.getElementById('tambahAttachedLinksContainer');
        if (linksContainer) {
            linksContainer.innerHTML = '';
            if (tambahLink && tambahLink.value) {
                renderLinkCard('tambah', tambahLink.value, () => {
                    tambahLink.value = '';
                });
            }
        }
        updateLampiranMeta('tambah');
    };

    window.closeTambahModal = function () {
        if (!modalTambah) return;
        modalTambah.classList.remove('is-open');
        document.body.style.overflow = '';
    };

    window.openEditModal = function (id, mingguKe, tanggal, kegiatan, link = '', fileName = '') {
        if (!modalEdit || !formEdit) return;
        currentAttachmentContext = 'edit';

        formEdit.action = `/peserta/logbook/${id}`;
        if (subtitleEdit) {
            subtitleEdit.textContent = `Memperbarui laporan logbook untuk Minggu ke-${mingguKe}`;
        }
        if (inputEditMinggu) inputEditMinggu.value = mingguKe;
        if (inputEditTanggal) inputEditTanggal.value = tanggal;
        if (textareaEditKegiatan) {
            textareaEditKegiatan.value = kegiatan;
            window.updateCharCount(textareaEditKegiatan, 'edit_char_count');
        }
        if (inputEditLink) inputEditLink.value = link || '';
        if (inputEditFile) inputEditFile.value = '';

        const editFilesContainer = document.getElementById('editAttachedFilesContainer');
        if (editFilesContainer) editFilesContainer.innerHTML = '';
        const editLinksContainer = document.getElementById('editAttachedLinksContainer');
        if (editLinksContainer) editLinksContainer.innerHTML = '';

        editExistingFileName = fileName || null;
        if (editExistingFileName) {
            renderFileCard('edit', editExistingFileName, 'Berkas tersimpan saat ini (PDF)', () => {
                editExistingFileName = null;
                if (inputEditFile) inputEditFile.value = '';
            });
        }

        if (link) {
            renderLinkCard('edit', link, () => {
                if (inputEditLink) inputEditLink.value = '';
            });
        }

        updateLampiranMeta('edit');
        modalEdit.classList.add('is-open');
        document.body.style.overflow = 'hidden';
    };

    window.closeEditModal = function () {
        if (!modalEdit) return;
        modalEdit.classList.remove('is-open');
        document.body.style.overflow = '';
    };

    // Close modals on backdrop click & ESC
    window.addEventListener('click', (e) => {
        if (e.target === modalTambah) window.closeTambahModal();
        if (e.target === modalEdit) window.closeEditModal();
    });

    window.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            window.closeTambahModal();
            window.closeEditModal();
            closeSubmodalUpload();
            closeSubmodalLink();
        }
    });

    // -------------------------------------------------------------------------
    // 5. SUB-MODAL POPUP DIALOGS (UPLOAD BERKAS & TAMBAH LINK)
    // -------------------------------------------------------------------------
    const modalUploadDialog = document.getElementById('modalLogbookUploadFileDialog');
    const modalLinkDialog = document.getElementById('modalLogbookAddLinkDialog');

    const dropzoneArea = document.getElementById('logbookDropzoneArea');
    const btnBrowse = document.getElementById('btnLogbookBrowseFile');
    const stagedFileInput = document.getElementById('modalStagedFileInput');
    const submodalFileInfo = document.getElementById('submodalFileInfo');
    const submodalFileName = document.getElementById('submodalFileName');
    const submodalFileSize = document.getElementById('submodalFileSize');
    const btnSubmodalRemoveFile = document.getElementById('btnSubmodalRemoveFile');
    const submodalFileError = document.getElementById('submodalFileError');
    const btnSubmodalConfirmFile = document.getElementById('btnSubmodalConfirmFile');

    const inputSubmodalLinkUrl = document.getElementById('inputSubmodalLinkUrl');
    const submodalLinkFieldFilled = document.getElementById('submodalLinkFieldFilled');
    const submodalLinkError = document.getElementById('submodalLinkError') || document.getElementById('linkErrorHint');
    const btnSubmodalConfirmLink = document.getElementById('btnSubmodalConfirmLink');

    let stagedFileObj = null;

    function openSubmodalUpload(context) {
        currentAttachmentContext = context;
        stagedFileObj = null;
        if (stagedFileInput) stagedFileInput.value = '';
        if (submodalFileInfo) submodalFileInfo.style.display = 'none';
        if (submodalFileError) {
            submodalFileError.style.display = 'none';
            submodalFileError.textContent = '';
        }
        if (btnSubmodalConfirmFile) btnSubmodalConfirmFile.disabled = true;
        modalUploadDialog?.classList.add('is-active');
    }

    function closeSubmodalUpload() {
        modalUploadDialog?.classList.remove('is-active');
    }

    function openSubmodalLink(context) {
        currentAttachmentContext = context;
        const currentVal = document.getElementById(`${context}_link_laporan`)?.value || '';
        if (inputSubmodalLinkUrl) {
            inputSubmodalLinkUrl.value = currentVal;
            if (currentVal.trim().length > 0) {
                submodalLinkFieldFilled?.classList.add('has-value');
            } else {
                submodalLinkFieldFilled?.classList.remove('has-value');
            }
        }
        validateLinkInput();
        modalLinkDialog?.classList.add('is-active');
        setTimeout(() => inputSubmodalLinkUrl?.focus(), 50);
    }

    function closeSubmodalLink() {
        modalLinkDialog?.classList.remove('is-active');
    }

    // Bind triggers to open submodals
    document.getElementById('btnTambahTriggerUploadFile')?.addEventListener('click', () => openSubmodalUpload('tambah'));
    document.getElementById('btnTambahTriggerAddLink')?.addEventListener('click', () => openSubmodalLink('tambah'));
    document.getElementById('btnEditTriggerUploadFile')?.addEventListener('click', () => openSubmodalUpload('edit'));
    document.getElementById('btnEditTriggerAddLink')?.addEventListener('click', () => openSubmodalLink('edit'));

    // Bind close buttons on submodals
    document.querySelectorAll('[data-action="close-submodal"]').forEach((btn) => {
        btn.addEventListener('click', () => {
            closeSubmodalUpload();
            closeSubmodalLink();
        });
    });

    modalUploadDialog?.addEventListener('click', (e) => {
        if (e.target === modalUploadDialog) closeSubmodalUpload();
    });

    modalLinkDialog?.addEventListener('click', (e) => {
        if (e.target === modalLinkDialog) closeSubmodalLink();
    });

    // File Dropzone & Browse Handlers
    function handleSelectedFile(file) {
        if (!file) return;

        const isPdf = file.type === 'application/pdf' || file.name.toLowerCase().endsWith('.pdf');
        if (!isPdf) {
            if (submodalFileError) {
                submodalFileError.textContent = 'Format berkas tidak valid. Hanya berkas PDF yang diperbolehkan.';
                submodalFileError.style.display = 'block';
            }
            if (btnSubmodalConfirmFile) btnSubmodalConfirmFile.disabled = true;
            return;
        }

        if (file.size > MAX_FILE_SIZE) {
            if (submodalFileError) {
                submodalFileError.textContent = 'Ukuran berkas melebihi 2MB. Silakan pilih berkas yang lebih kecil.';
                submodalFileError.style.display = 'block';
            }
            if (btnSubmodalConfirmFile) btnSubmodalConfirmFile.disabled = true;
            return;
        }

        if (submodalFileError) {
            submodalFileError.style.display = 'none';
            submodalFileError.textContent = '';
        }

        stagedFileObj = file;
        if (submodalFileName) submodalFileName.textContent = file.name;
        if (submodalFileSize) submodalFileSize.textContent = `${(file.size / 1024).toFixed(0)} KB · Berkas PDF siap diterapkan`;
        if (submodalFileInfo) submodalFileInfo.style.display = 'flex';
        if (btnSubmodalConfirmFile) btnSubmodalConfirmFile.disabled = false;
    }

    dropzoneArea?.addEventListener('click', () => stagedFileInput?.click());
    btnBrowse?.addEventListener('click', (e) => {
        e.stopPropagation();
        stagedFileInput?.click();
    });

    dropzoneArea?.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropzoneArea.classList.add('drag-over');
    });

    dropzoneArea?.addEventListener('dragleave', () => {
        dropzoneArea.classList.remove('drag-over');
    });

    dropzoneArea?.addEventListener('drop', (e) => {
        e.preventDefault();
        dropzoneArea.classList.remove('drag-over');
        if (e.dataTransfer?.files?.length > 0) {
            handleSelectedFile(e.dataTransfer.files[0]);
        }
    });

    stagedFileInput?.addEventListener('change', () => {
        if (stagedFileInput.files?.length > 0) {
            handleSelectedFile(stagedFileInput.files[0]);
        }
    });

    btnSubmodalRemoveFile?.addEventListener('click', () => {
        stagedFileObj = null;
        if (stagedFileInput) stagedFileInput.value = '';
        if (submodalFileInfo) submodalFileInfo.style.display = 'none';
        if (btnSubmodalConfirmFile) btnSubmodalConfirmFile.disabled = true;
    });

    btnSubmodalConfirmFile?.addEventListener('click', () => {
        if (!stagedFileObj) return;

        const targetInput = document.getElementById(`${currentAttachmentContext}_file_laporan`);
        if (targetInput) {
            try {
                const dt = new DataTransfer();
                dt.items.add(stagedFileObj);
                targetInput.files = dt.files;
            } catch {
                // Fallback jika DataTransfer tidak didukung
            }
        }

        if (currentAttachmentContext === 'edit') {
            editExistingFileName = null;
        }

        renderFileCard(
            currentAttachmentContext,
            stagedFileObj.name,
            `${(stagedFileObj.size / 1024).toFixed(0)} KB · PDF`,
            () => {
                if (targetInput) targetInput.value = '';
                if (currentAttachmentContext === 'edit') editExistingFileName = null;
            }
        );

        closeSubmodalUpload();
    });

    // Link Submodal Validation & Handlers
    function validateLinkInput() {
        const val = (inputSubmodalLinkUrl?.value || '').trim();
        const isValidHttps = val.startsWith('https://') && val.length > 8;

        if (submodalLinkFieldFilled) {
            submodalLinkFieldFilled.classList.toggle('has-value', val.length > 0);
        }

        if (val.length === 0) {
            if (submodalLinkError) {
                submodalLinkError.textContent = '*Wajib diisi';
                submodalLinkError.style.display = 'block';
            }
            if (btnSubmodalConfirmLink) btnSubmodalConfirmLink.disabled = true;
            return false;
        }

        if (!isValidHttps) {
            if (submodalLinkError) {
                submodalLinkError.textContent = '*Wajib menggunakan https:// (contoh: https://drive.google.com/...)';
                submodalLinkError.style.display = 'block';
            }
            if (btnSubmodalConfirmLink) btnSubmodalConfirmLink.disabled = true;
            return false;
        }

        if (submodalLinkError) {
            submodalLinkError.style.display = 'none';
            submodalLinkError.textContent = '';
        }
        if (btnSubmodalConfirmLink) btnSubmodalConfirmLink.disabled = false;
        return true;
    }

    inputSubmodalLinkUrl?.addEventListener('input', validateLinkInput);

    btnSubmodalConfirmLink?.addEventListener('click', () => {
        if (!validateLinkInput()) return;

        const linkVal = inputSubmodalLinkUrl.value.trim();
        const targetInput = document.getElementById(`${currentAttachmentContext}_link_laporan`);
        if (targetInput) {
            targetInput.value = linkVal;
        }

        renderLinkCard(currentAttachmentContext, linkVal, () => {
            if (targetInput) targetInput.value = '';
        });

        closeSubmodalLink();
    });

    // -------------------------------------------------------------------------
    // 6. Pencarian Realtime
    // -------------------------------------------------------------------------
    const searchInput = document.getElementById('inputSearchLogbook');
    const btnClearSearch = document.getElementById('btnClearSearch');
    const resultCount = document.getElementById('searchResultCount');
    const emptySearchState = document.getElementById('searchEmptyState');
    const searchQueryText = document.getElementById('searchQueryText');
    const cardsContainer = document.getElementById('logbookCardsContainer');

    window.handleSearchLogbook = function (query) {
        if (!cardsContainer) return;
        const q = (query || '').toLowerCase().trim();
        const cards = Array.from(cardsContainer.querySelectorAll('.weekly-card'));

        if (btnClearSearch) {
            btnClearSearch.style.display = q ? 'inline-flex' : 'none';
        }

        let matchCount = 0;
        cards.forEach((card) => {
            const minggu = card.getAttribute('data-minggu') || '';
            const content = card.textContent.toLowerCase();

            const isMatch = !q || minggu === q || content.includes(q);
            card.style.display = isMatch ? 'block' : 'none';
            if (isMatch) matchCount++;
        });

        if (resultCount) {
            if (q) {
                resultCount.style.display = 'inline-block';
                resultCount.textContent = `${matchCount} ditemukan`;
            } else {
                resultCount.style.display = 'none';
            }
        }

        if (emptySearchState) {
            if (matchCount === 0 && q) {
                emptySearchState.style.display = 'block';
                if (searchQueryText) searchQueryText.textContent = query;
            } else {
                emptySearchState.style.display = 'none';
            }
        }
    };

    window.clearSearchLogbook = function () {
        if (searchInput) {
            searchInput.value = '';
            window.handleSearchLogbook('');
        }
    };
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initLogbookIndex);
} else {
    initLogbookIndex();
}
