/**
 * =========================================================================
 * MAGKOM PESERTA: MODUL LOGBOOK & JURNAL MINGGUAN
 * Mengelola accordion, pencarian, dan sistem upload berkas/link
 * yang selaras dengan antarmuka pengumpulan tugas Google Classroom.
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

    // State lampiran existing untuk Edit
    let editExistingFileName = null;

    window.openTambahModal = function (preSelectMinggu = null) {
        if (!modalTambah) return;
        modalTambah.classList.add('is-open');
        document.body.style.overflow = 'hidden';

        if (preSelectMinggu && selectMinggu) {
            selectMinggu.value = preSelectMinggu;
            window.onMingguChange(preSelectMinggu);
        } else if (selectMinggu && selectMinggu.value) {
            window.onMingguChange(selectMinggu.value);
        }

        renderAttachments('tambah');
    };

    window.closeTambahModal = function () {
        if (!modalTambah) return;
        modalTambah.classList.remove('is-open');
        document.body.style.overflow = '';
        closeAllDropdowns();
    };

    window.openEditModal = function (id, mingguKe, tanggal, kegiatan, link = '', fileName = '') {
        if (!modalEdit || !formEdit) return;

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

        editExistingFileName = fileName || null;

        modalEdit.classList.add('is-open');
        document.body.style.overflow = 'hidden';

        renderAttachments('edit');
    };

    window.closeEditModal = function () {
        if (!modalEdit) return;
        modalEdit.classList.remove('is-open');
        document.body.style.overflow = '';
        closeAllDropdowns();
    };

    // Close modalls on backdrop click & ESC
    window.addEventListener('click', (e) => {
        if (e.target === modalTambah) window.closeTambahModal();
        if (e.target === modalEdit) window.closeEditModal();
    });

    window.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            const subModalOpen = document.querySelector('.classroom-modal-backdrop.show');
            if (subModalOpen) {
                closeLogbookSubmodal(subModalOpen);
            } else {
                window.closeTambahModal();
                window.closeEditModal();
            }
        }
    });

    // -------------------------------------------------------------------------
    // 5. DROPDOWN "+ Tambah Berkas" (Link & File)
    // -------------------------------------------------------------------------
    const btnTambahToggle = document.getElementById('btnTambahToggleDropdown');
    const menuTambahDropdown = document.getElementById('menuTambahDropdown');
    const btnEditToggle = document.getElementById('btnEditToggleDropdown');
    const menuEditDropdown = document.getElementById('menuEditDropdown');

    btnTambahToggle?.addEventListener('click', (e) => {
        e.stopPropagation();
        menuTambahDropdown?.classList.toggle('show');
        menuEditDropdown?.classList.remove('show');
    });

    btnEditToggle?.addEventListener('click', (e) => {
        e.stopPropagation();
        menuEditDropdown?.classList.toggle('show');
        menuTambahDropdown?.classList.remove('show');
    });

    function closeAllDropdowns() {
        menuTambahDropdown?.classList.remove('show');
        menuEditDropdown?.classList.remove('show');
    }

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.btn-dropdown-wrapper')) {
            closeAllDropdowns();
        }
    });

    // -------------------------------------------------------------------------
    // 6. SUB-MODAL LOGBOOK (Link & File - Classroom Style)
    // -------------------------------------------------------------------------
    let currentUploadContext = 'tambah'; // 'tambah' | 'edit'

    const modalLink = document.getElementById('modalLogbookLink');
    const inputUrl = document.getElementById('inputLogbookUrl');
    const btnSubmitLink = document.getElementById('btnSubmitLogbookLink');

    const modalFile = document.getElementById('modalLogbookFile');
    const submodalFileInput = document.getElementById('submodalLogbookFileInput');
    const btnBrowse = document.getElementById('btnBrowseLogbookFile');
    const dropzoneBox = document.getElementById('logbookDropzoneBox');

    function openLogbookSubmodal(modalEl) {
        if (!modalEl) return;
        modalEl.classList.add('show');
    }

    function closeLogbookSubmodal(modalEl) {
        if (!modalEl) return;
        modalEl.classList.remove('show');
    }

    // Pasang tombol close untuk submodals
    document.querySelectorAll('[data-action="close-logbook-submodal"]').forEach((btn) => {
        btn.addEventListener('click', () => {
            const backdrop = btn.closest('.classroom-modal-backdrop');
            closeLogbookSubmodal(backdrop);
        });
    });

    document.querySelectorAll('.classroom-modal-backdrop').forEach((backdrop) => {
        backdrop.addEventListener('click', (e) => {
            if (e.target === backdrop) {
                closeLogbookSubmodal(backdrop);
            }
        });
    });

    // Action Trigger: Buka Modal Link
    document.querySelectorAll('[data-action="open-logbook-link"]').forEach((btn) => {
        btn.addEventListener('click', () => {
            currentUploadContext = btn.getAttribute('data-context') || 'tambah';
            closeAllDropdowns();

            const targetLinkInput = document.getElementById(`${currentUploadContext}_link_laporan`);
            if (inputUrl) {
                inputUrl.value = targetLinkInput?.value || '';
                validateUrlInput();
            }

            openLogbookSubmodal(modalLink);
            setTimeout(() => inputUrl?.focus(), 100);
        });
    });

    // Action Trigger: Buka Modal File
    document.querySelectorAll('[data-action="open-logbook-file"]').forEach((btn) => {
        btn.addEventListener('click', () => {
            currentUploadContext = btn.getAttribute('data-context') || 'tambah';
            closeAllDropdowns();
            if (submodalFileInput) submodalFileInput.value = '';
            openLogbookSubmodal(modalFile);
        });
    });

    // Validasi URL
    function validateUrlInput() {
        const val = (inputUrl?.value || '').trim();
        const isValid = /^https?:\/\/.+/i.test(val);
        if (isValid) {
            btnSubmitLink?.removeAttribute('disabled');
        } else {
            btnSubmitLink?.setAttribute('disabled', 'disabled');
        }
    }

    inputUrl?.addEventListener('input', validateUrlInput);

    btnSubmitLink?.addEventListener('click', () => {
        const val = (inputUrl?.value || '').trim();
        if (!/^https?:\/\/.+/i.test(val)) {
            alert('Silakan masukkan URL yang valid diawali dengan https://');
            return;
        }

        const targetLinkInput = document.getElementById(`${currentUploadContext}_link_laporan`);
        if (targetLinkInput) {
            targetLinkInput.value = val;
        }

        closeLogbookSubmodal(modalLink);
        renderAttachments(currentUploadContext);
    });

    // File Dropzone & Browse Handlers
    btnBrowse?.addEventListener('click', () => submodalFileInput?.click());
    dropzoneBox?.addEventListener('click', (e) => {
        if (e.target !== btnBrowse) {
            submodalFileInput?.click();
        }
    });

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
            handleSelectedLogbookFile(files[0]);
        }
    });

    submodalFileInput?.addEventListener('change', (e) => {
        const input = e.target;
        if (input.files && input.files[0]) {
            handleSelectedLogbookFile(input.files[0]);
        }
    });

    function handleSelectedLogbookFile(file) {
        const isPdf = file.type === 'application/pdf' || file.name.toLowerCase().endsWith('.pdf');
        if (!isPdf) {
            alert('Format berkas tidak valid. Harap mengunggah berkas PDF.');
            return;
        }

        const maxBytes = 2 * 1024 * 1024; // 2MB
        if (file.size > maxBytes) {
            alert('Ukuran berkas melebihi 2MB. Silakan kompresi berkas terlebih dahulu.');
            return;
        }

        const targetFileInput = document.getElementById(`${currentUploadContext}_file_laporan`);
        if (targetFileInput) {
            try {
                const dt = new DataTransfer();
                dt.items.add(file);
                targetFileInput.files = dt.files;
            } catch (_) {
                // Fallback jika DataTransfer tidak didukung
            }
        }

        if (currentUploadContext === 'edit') {
            editExistingFileName = null; // Menimpa file lama
        }

        closeLogbookSubmodal(modalFile);
        renderAttachments(currentUploadContext);
    }

    // -------------------------------------------------------------------------
    // 7. Render Kartu Lampiran Terpilih (.attached-item-card)
    // -------------------------------------------------------------------------
    function renderAttachments(context) {
        const container = document.getElementById(`${context}AttachmentsList`);
        const fileInput = document.getElementById(`${context}_file_laporan`);
        const linkInput = document.getElementById(`${context}_link_laporan`);

        if (!container) return;
        container.innerHTML = '';

        // 1. Cek Berkas File
        const hasNewFile = fileInput?.files && fileInput.files.length > 0;
        const hasExistingFile = context === 'edit' && editExistingFileName;

        if (hasNewFile || hasExistingFile) {
            const fileName = hasNewFile ? fileInput.files[0].name : editExistingFileName;
            const fileSize = hasNewFile ? `${(fileInput.files[0].size / 1024).toFixed(0)} KB` : 'Berkas Laporan PDF';

            const fileCard = document.createElement('div');
            fileCard.className = 'attached-item-card';
            fileCard.innerHTML = `
                <div class="attached-item-left">
                    <div class="attached-item-icon" style="background: #FEE2E2; color: #DC2626;">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="attached-item-info">
                        <div class="attached-item-name" title="${escapeHtml(fileName)}">${escapeHtml(fileName)}</div>
                        <div class="attached-item-meta">${escapeHtml(fileSize)}</div>
                    </div>
                </div>
                <button type="button" class="attached-item-remove" title="Hapus berkas" data-action="remove-file">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            `;

            fileCard.querySelector('[data-action="remove-file"]')?.addEventListener('click', () => {
                if (fileInput) fileInput.value = '';
                if (context === 'edit') editExistingFileName = null;
                renderAttachments(context);
            });

            container.appendChild(fileCard);
        }

        // 2. Cek Tautan Link
        const linkVal = (linkInput?.value || '').trim();
        if (linkVal) {
            const linkCard = document.createElement('div');
            linkCard.className = 'attached-item-card';
            linkCard.innerHTML = `
                <div class="attached-item-left">
                    <div class="attached-item-icon" style="background: #EFF6FF; color: #2563EB;">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                        </svg>
                    </div>
                    <div class="attached-item-info">
                        <div class="attached-item-name" title="${escapeHtml(linkVal)}">${escapeHtml(linkVal)}</div>
                        <div class="attached-item-meta">Tautan Dokumen / Repositori</div>
                    </div>
                </div>
                <button type="button" class="attached-item-remove" title="Hapus tautan" data-action="remove-link">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            `;

            linkCard.querySelector('[data-action="remove-link"]')?.addEventListener('click', () => {
                if (linkInput) linkInput.value = '';
                renderAttachments(context);
            });

            container.appendChild(linkCard);
        }
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // -------------------------------------------------------------------------
    // 8. Pencarian Realtime
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
