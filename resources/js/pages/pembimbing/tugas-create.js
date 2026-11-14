/**
 * =========================================================================
 * MAGKOM — INTERAKSI HALAMAN BUAT TUGAS BARU PEMBIMBING
 * Vanilla JavaScript (ES Modules) — Mengacu pada mockup Designnnn.
 * =========================================================================
 */

document.addEventListener('DOMContentLoaded', () => {
    initFloatingLabels();
    initMenteeSelector();
    initKategoriDropdown();
    initFileUpload();
    initLinkAttachment();
    initDeadlineCalendar();
    initJadwalCalendar();
    initPublicationSplitButton();
    initFormattingToolbar();
    initFormValidation();
});

/**
 * 0. Inisialisasi Material Floating Label
 * Mendukung animasi smooth saat input judul & petunjuk fokus atau berisi teks ala Google Classroom
 */
function initFloatingLabels() {
    // 1. Floating label untuk Judul Penugasan
    const judulInput = document.getElementById('inputJudul');
    const judulBox = judulInput?.closest('.tugas-field-filled');

    function updateJudulFloatingState() {
        if (!judulInput || !judulBox) return;
        const hasValue = judulInput.value.trim().length > 0;
        judulBox.classList.toggle('has-value', hasValue);
    }

    if (judulInput && judulBox) {
        judulInput.addEventListener('focus', () => {
            judulBox.classList.add('is-focused');
        });

        judulInput.addEventListener('blur', () => {
            judulBox.classList.remove('is-focused');
            updateJudulFloatingState();
        });

        judulInput.addEventListener('input', () => {
            updateJudulFloatingState();
        });

        updateJudulFloatingState();
    }

    // 2. Floating label untuk Petunjuk (opsional)
    const petunjukEditor = document.getElementById('editorPetunjukVisual');
    const petunjukBox = petunjukEditor?.closest('.tugas-instruction-box');
    const petunjukLabel = petunjukBox?.querySelector('.tugas-instruction-label');

    function updatePetunjukFloatingState() {
        if (!petunjukEditor || !petunjukBox) return;
        const text = petunjukEditor.innerText.trim();
        const hasValue = text.length > 0;
        petunjukBox.classList.toggle('has-value', hasValue);
    }

    if (petunjukEditor && petunjukBox) {
        petunjukEditor.addEventListener('focus', () => {
            petunjukBox.classList.add('is-focused');
        });

        petunjukEditor.addEventListener('blur', () => {
            setTimeout(() => {
                if (!petunjukBox.contains(document.activeElement)) {
                    petunjukBox.classList.remove('is-focused');
                }
                updatePetunjukFloatingState();
            }, 50);
        });

        petunjukEditor.addEventListener('input', () => {
            updatePetunjukFloatingState();
        });

        if (petunjukLabel) {
            petunjukLabel.addEventListener('click', () => {
                petunjukEditor.focus();
            });
        }

        petunjukBox.addEventListener('click', (e) => {
            if (!e.target.closest('.tugas-format-toolbar')) {
                petunjukEditor.focus();
            }
        });

        updatePetunjukFloatingState();
    }
}

/**
 * 1. Modal Pemilihan Mentee ("Tugaskan ke")
 */
function initMenteeSelector() {
    const triggerBtn = document.getElementById('btnOpenMenteeDialog');
    const modal = document.getElementById('modalMenteeDialog');
    const cancelBtn = document.getElementById('btnCancelMentee') || document.getElementById('btnCancelMenteeDialog');
    const confirmBtn = document.getElementById('btnConfirmMentee');
    const chkSemua = document.getElementById('chkSemuaPeserta');
    const menteeCheckboxes = modal ? modal.querySelectorAll('.js-chk-mentee, .chk-mentee') : [];
    const searchInput = document.getElementById('inputSearchMentee');
    const triggerText = document.getElementById('txtSelectedMenteeSummary');
    const hiddenContainer = document.getElementById('hiddenMenteeInputsContainer') || document.getElementById('hiddenPenempatanIdsContainer');

    if (!triggerBtn || !modal) return;

    // Buka Modal
    triggerBtn.addEventListener('click', () => {
        modal.classList.add('is-active');
        if (searchInput) {
            searchInput.value = '';
            filterMenteeList('');
            searchInput.focus();
        }
    });

    // Tutup Modal
    const closeModal = () => modal.classList.remove('is-active');
    if (cancelBtn) cancelBtn.addEventListener('click', closeModal);
    modal.addEventListener('click', (e) => {
        if (e.target === modal) closeModal();
    });

    // Filter Pencarian Mentee
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            filterMenteeList(e.target.value.toLowerCase().trim());
        });
    }

    function filterMenteeList(query) {
        const rows = modal.querySelectorAll('.js-mentee-item, .tugas-mentee-row:not(.is-all)');
        rows.forEach(row => {
            const name = (row.dataset.nama || row.dataset.menteeName || '').toLowerCase();
            const campus = (row.dataset.univ || row.dataset.menteeCampus || '').toLowerCase();
            const match = name.includes(query) || campus.includes(query);
            row.style.display = match ? 'flex' : 'none';
        });
    }

    // Toggle Semua Peserta
    if (chkSemua) {
        chkSemua.addEventListener('change', () => {
            const isChecked = chkSemua.checked;
            menteeCheckboxes.forEach(chk => {
                chk.checked = isChecked;
            });
            updateSemuaCheckboxState();
        });
    }

    // Individual Mentee Checkbox Change
    menteeCheckboxes.forEach(chk => {
        chk.addEventListener('change', () => {
            updateSemuaCheckboxState();
        });
    });

    function updateSemuaCheckboxState() {
        const total = menteeCheckboxes.length;
        const checkedCount = Array.from(menteeCheckboxes).filter(c => c.checked).length;
        if (chkSemua) {
            chkSemua.checked = (total > 0 && checkedCount === total);
            chkSemua.indeterminate = (checkedCount > 0 && checkedCount < total);
        }
    }

    // Konfirmasi Pilihan Mentee
    if (confirmBtn) {
        confirmBtn.addEventListener('click', () => {
            applyMenteeSelection();
            closeModal();
        });
    }

    function applyMenteeSelection() {
        const selected = Array.from(menteeCheckboxes).filter(c => c.checked);
        const total = menteeCheckboxes.length;

        // Bersihkan hidden input lama
        if (hiddenContainer) {
            hiddenContainer.innerHTML = '';
        }

        if (selected.length === 0) {
            if (triggerText) triggerText.textContent = 'Pilih peserta magang';
            return;
        }

        // Generate hidden inputs
        selected.forEach(chk => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'penempatan_ids[]';
            input.value = chk.value;
            hiddenContainer.appendChild(input);
        });

        // Sinkronkan ke select fallback jika ada
        const fallbackSelect = document.getElementById('select_penempatan');
        if (fallbackSelect && selected.length > 0) {
            fallbackSelect.value = selected[0].value;
        }

        // Update trigger text
        if (triggerText) {
            if (selected.length === total && total > 1) {
                triggerText.textContent = `Semua peserta (${total} mentee)`;
            } else if (selected.length === 1) {
                triggerText.textContent = selected[0].dataset.nama || selected[0].dataset.name || '1 Mentee Terpilih';
            } else {
                const firstName = selected[0].dataset.nama || selected[0].dataset.name || 'Mentee';
                triggerText.textContent = `${firstName} + ${selected.length - 1} lainnya`;
            }
        }
    }

    // Inisialisasi awal jika ada preselected
    updateSemuaCheckboxState();
    applyMenteeSelection();
}

/**
 * 1b. Inisialisasi Custom Select Kategori Bidang ala Google Classroom (Topik) & MAGKOM
 */
function initKategoriDropdown() {
    const trigger = document.getElementById('btnTriggerKategori');
    const menu = document.getElementById('menuKategoriList');
    const triggerText = document.getElementById('btnTriggerKategoriText');
    const hiddenSelect = document.getElementById('selectKategori');
    const options = menu ? menu.querySelectorAll('.tugas-custom-select-option') : [];

    if (!trigger || !menu || !hiddenSelect) return;

    function openMenu() {
        menu.classList.add('is-open');
        trigger.classList.add('is-open');
        trigger.setAttribute('aria-expanded', 'true');
    }

    function closeMenu() {
        menu.classList.remove('is-open');
        trigger.classList.remove('is-open');
        trigger.setAttribute('aria-expanded', 'false');
    }

    trigger.addEventListener('click', (e) => {
        e.stopPropagation();
        if (menu.classList.contains('is-open')) {
            closeMenu();
        } else {
            openMenu();
        }
    });

    options.forEach(opt => {
        opt.addEventListener('click', (e) => {
            e.stopPropagation();
            const val = opt.dataset.value;
            if (!val) return;

            // Update display text & remove placeholder class
            if (triggerText) triggerText.textContent = val;
            trigger.classList.remove('is-placeholder');

            // Update hidden select
            hiddenSelect.value = val;
            hiddenSelect.dispatchEvent(new Event('change', { bubbles: true }));

            // Update selected class & checkmark icon
            options.forEach(o => {
                const isSelected = o === opt;
                o.classList.toggle('is-selected', isSelected);
                o.setAttribute('aria-selected', isSelected ? 'true' : 'false');
                const check = o.querySelector('.option-check');
                if (check) check.remove();
                if (isSelected) {
                    const checkSvg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
                    checkSvg.setAttribute('class', 'option-check');
                    checkSvg.setAttribute('width', '16');
                    checkSvg.setAttribute('height', '16');
                    checkSvg.setAttribute('viewBox', '0 0 24 24');
                    checkSvg.setAttribute('fill', 'none');
                    checkSvg.setAttribute('stroke', 'currentColor');
                    checkSvg.setAttribute('stroke-width', '2.5');
                    checkSvg.setAttribute('stroke-linecap', 'round');
                    checkSvg.setAttribute('stroke-linejoin', 'round');
                    checkSvg.innerHTML = '<polyline points="20 6 9 17 4 12"></polyline>';
                    o.appendChild(checkSvg);
                }
            });

            closeMenu();
        });
    });

    document.addEventListener('click', (e) => {
        if (!menu.contains(e.target) && e.target !== trigger) {
            closeMenu();
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && menu.classList.contains('is-open')) {
            closeMenu();
            trigger.focus();
        }
    });
}

/**
 * 2. Modal & Dropzone Upload File
 */
function initFileUpload() {
    const triggerBtn = document.getElementById('btnTriggerUploadFile');
    const modal = document.getElementById('modalUploadFileDialog');
    const cancelBtn = document.getElementById('btnCancelUploadFile');
    const submitBtn = document.getElementById('btnSubmitUploadFile');
    const dropzone = document.getElementById('tugasDropzoneArea') || document.getElementById('modalDropzone');
    const browseBtn = document.getElementById('btnBrowseFile');
    const modalFileInput = document.getElementById('inputModalFile');
    const realFileInput = document.getElementById('realFileInput');
    const previewContainer = document.getElementById('attachedFilesContainer');
    const counterBadge = document.getElementById('lampiranCountMeta');

    const fileInfoBox = document.getElementById('modalSelectedFileInfo') || document.getElementById('dropzoneFileView');
    const fileNameEl = document.getElementById('modalFileNameTxt') || document.getElementById('stagedFileName');
    const fileSizeEl = document.getElementById('modalFileSizeTxt') || document.getElementById('stagedFileSize');
    const removeStagedBtn = document.getElementById('btnRemoveModalFile') || document.getElementById('btnRemoveStagedFile');

    let stagedFile = null;

    if (!triggerBtn || !modal) return;

    const openModal = () => {
        stagedFile = null;
        updateModalPreview();
        modal.classList.add('is-active');
    };

    const closeModal = () => modal.classList.remove('is-active');

    triggerBtn.addEventListener('click', openModal);
    if (cancelBtn) cancelBtn.addEventListener('click', closeModal);
    modal.addEventListener('click', (e) => {
        if (e.target === modal) closeModal();
    });

    if (browseBtn) {
        browseBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            if (modalFileInput) {
                modalFileInput.click();
            } else if (realFileInput) {
                const tempInput = document.createElement('input');
                tempInput.type = 'file';
                tempInput.accept = realFileInput.accept;
                tempInput.onchange = () => {
                    if (tempInput.files && tempInput.files.length > 0) {
                        handleFileSelection(tempInput.files[0]);
                    }
                };
                tempInput.click();
            }
        });
    }

    if (dropzone) {
        dropzone.addEventListener('click', (e) => {
            if (e.target.closest('#btnBrowseFile') || e.target.closest('#btnRemoveModalFile') || e.target.closest('#btnRemoveStagedFile')) {
                return;
            }
            if (browseBtn) {
                browseBtn.click();
            } else if (modalFileInput) {
                modalFileInput.click();
            }
        });

        ['dragenter', 'dragover'].forEach(eventName => {
            dropzone.addEventListener(eventName, (e) => {
                e.preventDefault();
                dropzone.classList.add('drag-over');
            });
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropzone.addEventListener(eventName, (e) => {
                e.preventDefault();
                dropzone.classList.remove('drag-over');
            });
        });

        dropzone.addEventListener('drop', (e) => {
            const files = e.dataTransfer?.files;
            if (files && files.length > 0) {
                handleFileSelection(files[0]);
            }
        });
    }

    if (modalFileInput) {
        modalFileInput.addEventListener('change', () => {
            if (modalFileInput.files && modalFileInput.files.length > 0) {
                handleFileSelection(modalFileInput.files[0]);
            }
        });
    }

    function handleFileSelection(file) {
        const allowedExts = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'csv', 'ppt', 'pptx', 'zip', 'png', 'jpg', 'jpeg'];
        const ext = file.name.split('.').pop().toLowerCase();
        const maxBytes = 10 * 1024 * 1024; // 10MB

        if (!allowedExts.includes(ext)) {
            alert('Format berkas tidak didukung. Harap unggah PDF, DOCX, XLSX, PPTX, ZIP, atau Gambar.');
            return;
        }

        if (file.size > maxBytes) {
            alert('Ukuran berkas melebihi batas maksimum 10 MB.');
            return;
        }

        stagedFile = file;
        updateModalPreview();
    }

    function updateModalPreview() {
        const promptView = document.getElementById('dropzonePromptView');

        if (stagedFile) {
            if (promptView) promptView.style.display = 'none';
            if (fileInfoBox) fileInfoBox.style.display = 'flex';
            if (fileNameEl) fileNameEl.textContent = stagedFile.name;
            if (fileSizeEl) fileSizeEl.textContent = formatBytes(stagedFile.size);
            if (submitBtn) submitBtn.disabled = false;
        } else {
            if (promptView) promptView.style.display = 'flex';
            if (fileInfoBox) fileInfoBox.style.display = 'none';
            if (submitBtn) submitBtn.disabled = true;
        }
    }

    if (removeStagedBtn) {
        removeStagedBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            stagedFile = null;
            if (modalFileInput) modalFileInput.value = '';
            if (realFileInput) realFileInput.value = '';
            updateModalPreview();
        });
    }

    // Submit File dari Modal ke Form Nyata
    if (submitBtn && realFileInput) {
        submitBtn.addEventListener('click', () => {
            if (!stagedFile) return;

            // Transfer staged file ke input file nyata menggunakan DataTransfer
            const dt = new DataTransfer();
            dt.items.add(stagedFile);
            realFileInput.files = dt.files;

            renderAttachedFileCard(stagedFile);
            closeModal();
        });
    }

    function renderAttachedFileCard(file) {
        if (!previewContainer) return;

        const ext = file.name.split('.').pop().toUpperCase();
        previewContainer.innerHTML = `
            <div class="tugas-attached-card" id="cardFileAttachment">
                <div class="tugas-attached-left">
                    <div class="tugas-file-badge">${ext}</div>
                    <div class="tugas-attached-info">
                        <p class="tugas-attached-name" title="${escapeHtml(file.name)}">${escapeHtml(file.name)}</p>
                        <p class="tugas-attached-meta">${formatBytes(file.size)} · Lampiran Dokumen</p>
                    </div>
                </div>
                <button type="button" class="tugas-btn-remove-attachment" id="btnDeleteAttachedFile" title="Hapus Berkas">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
                </button>
            </div>
        `;

        const deleteBtn = document.getElementById('btnDeleteAttachedFile');
        if (deleteBtn) {
            deleteBtn.addEventListener('click', () => {
                previewContainer.innerHTML = '';
                if (realFileInput) realFileInput.value = '';
                updateAttachmentMetaCount();
            });
        }

        updateAttachmentMetaCount();
    }

    function updateAttachmentMetaCount() {
        if (!counterBadge) return;
        let count = 0;
        if (document.getElementById('cardFileAttachment')) count++;
        if (document.getElementById('cardLinkAttachment')) count++;
        counterBadge.textContent = `(opsional) · ${count} lampiran`;
    }
}

/**
 * 3. Modal Tambah Link Referensi
 */
function initLinkAttachment() {
    const triggerBtn = document.getElementById('btnTriggerAddLink');
    const modal = document.getElementById('modalAddLinkDialog');
    const cancelBtn = document.getElementById('btnCancelAddLink');
    const submitBtn = document.getElementById('btnSubmitAddLink');
    const inputUrl = document.getElementById('inputModalLinkUrl');
    const realInput = document.getElementById('realLinkInput');
    const linkPreviewContainer = document.getElementById('attachedLinksContainer');
    const counterBadge = document.getElementById('lampiranCountMeta');

    if (!triggerBtn || !modal) return;

    const openModal = () => {
        if (inputUrl) {
            inputUrl.value = realInput?.value || '';
            validateUrl();
        }
        modal.classList.add('is-active');
        inputUrl?.focus();
    };

    const closeModal = () => modal.classList.remove('is-active');

    triggerBtn.addEventListener('click', openModal);
    if (cancelBtn) cancelBtn.addEventListener('click', closeModal);
    modal.addEventListener('click', (e) => {
        if (e.target === modal) closeModal();
    });

    if (inputUrl) {
        inputUrl.addEventListener('input', validateUrl);
    }

    function validateUrl() {
        const val = (inputUrl?.value || '').trim();
        const isValid = /^(http:\/\/|https:\/\/).+/.test(val);
        const errHint = document.getElementById('linkErrorHint');

        if (val.length === 0) {
            if (errHint) errHint.textContent = '*Wajib diisi';
            if (submitBtn) submitBtn.disabled = true;
        } else if (!isValid) {
            if (errHint) errHint.textContent = '*Harus diawali http:// atau https://';
            if (submitBtn) submitBtn.disabled = true;
        } else {
            if (errHint) errHint.textContent = '';
            if (submitBtn) submitBtn.disabled = false;
        }

        return isValid;
    }

    if (submitBtn && realInput) {
        submitBtn.addEventListener('click', () => {
            const url = (inputUrl?.value || '').trim();
            if (!validateUrl()) return;

            realInput.value = url;
            renderLinkCard(url);
            closeModal();
        });
    }

    function renderLinkCard(url) {
        if (!linkPreviewContainer) return;

        linkPreviewContainer.innerHTML = `
            <div class="tugas-attached-card" id="cardLinkAttachment">
                <div class="tugas-attached-left">
                    <div class="tugas-file-badge badge-link">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                    </div>
                    <div class="tugas-attached-info">
                        <p class="tugas-attached-name" title="${escapeHtml(url)}">${escapeHtml(url)}</p>
                        <p class="tugas-attached-meta">Tautan Referensi Eksternal</p>
                    </div>
                </div>
                <button type="button" class="tugas-btn-remove-attachment" id="btnDeleteAttachedLink" title="Hapus Tautan">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
                </button>
            </div>
        `;

        const deleteBtn = document.getElementById('btnDeleteAttachedLink');
        if (deleteBtn) {
            deleteBtn.addEventListener('click', () => {
                linkPreviewContainer.innerHTML = '';
                if (realInput) realInput.value = '';
                updateAttachmentMetaCount();
            });
        }

        updateAttachmentMetaCount();
    }

    function updateAttachmentMetaCount() {
        if (!counterBadge) return;
        let count = 0;
        if (document.getElementById('cardFileAttachment')) count++;
        if (document.getElementById('cardLinkAttachment')) count++;
        counterBadge.textContent = `(opsional) · ${count} lampiran`;
    }

    // Inisialisasi awal jika ada old value
    if (realInput && realInput.value) {
        renderLinkCard(realInput.value);
    }
}

/**
 * 4. Popup Kalender Tenggat (Date Picker)
 */
function initDeadlineCalendar() {
    const triggerBtn = document.getElementById('btnTriggerDeadline');
    const popup = document.getElementById('popupCalendarDeadline');
    const hiddenInput = document.getElementById('realDeadlineInput');
    const displayText = document.getElementById('txtSelectedDeadlineDisplay');

    const calMonthLabel = document.getElementById('calHeaderMonth');
    const calYearLabel = document.getElementById('calHeaderYear');
    const calDatesGrid = document.getElementById('calDatesGrid');

    const btnPrevMonth = document.getElementById('calPrevMonth');
    const btnNextMonth = document.getElementById('calNextMonth');
    const btnClear = document.getElementById('calBtnClear');
    const btnCancel = document.getElementById('calBtnCancel');
    const btnOk = document.getElementById('calBtnOk');

    if (!triggerBtn || !popup) return;

    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    const monthFull = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    const daysIndo = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

    const today = new Date();
    today.setHours(0, 0, 0, 0);

    let viewYear = today.getFullYear();
    let viewMonth = today.getMonth(); // 0-indexed
    let selectedDate = null;

    // Load initial date from hidden input
    if (hiddenInput && hiddenInput.value) {
        const parts = hiddenInput.value.split('-');
        if (parts.length === 3) {
            selectedDate = new Date(parseInt(parts[0]), parseInt(parts[1]) - 1, parseInt(parts[2]));
            viewYear = selectedDate.getFullYear();
            viewMonth = selectedDate.getMonth();
        }
    }

    triggerBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        renderCalendar();
        popup.classList.add('is-active');
        positionCalendarPopup();
    });

    function positionCalendarPopup() {
        const rect = triggerBtn.getBoundingClientRect();
        const popupWidth = Math.min(358, window.innerWidth - 32);
        let left = rect.left;
        if (left + popupWidth > window.innerWidth - 16) {
            left = window.innerWidth - popupWidth - 16;
        }
        if (left < 16) left = 16;

        const popupHeight = popup.offsetHeight || 330;
        const spaceBelow = window.innerHeight - rect.bottom - 16;
        const spaceAbove = rect.top - 16;

        let top;
        if (spaceBelow < popupHeight && spaceAbove > spaceBelow) {
            top = Math.max(16, rect.top - popupHeight - 8);
        } else {
            top = Math.max(16, Math.min(rect.bottom + 8, window.innerHeight - popupHeight - 16));
        }

        popup.style.top = `${top}px`;
        popup.style.left = `${left}px`;
        popup.style.width = `${popupWidth}px`;
    }

    window.addEventListener('resize', () => {
        if (popup.classList.contains('is-active')) {
            positionCalendarPopup();
        }
    });

    document.addEventListener('click', (e) => {
        if (!popup.contains(e.target) && e.target !== triggerBtn && !triggerBtn.contains(e.target)) {
            popup.classList.remove('is-active');
        }
    });

    if (btnPrevMonth) {
        btnPrevMonth.addEventListener('click', () => {
            viewMonth--;
            if (viewMonth < 0) {
                viewMonth = 11;
                viewYear--;
            }
            renderCalendar();
        });
    }

    if (btnNextMonth) {
        btnNextMonth.addEventListener('click', () => {
            viewMonth++;
            if (viewMonth > 11) {
                viewMonth = 0;
                viewYear++;
            }
            renderCalendar();
        });
    }

    function commitDateSelection(dateObj, shouldClose = true) {
        if (!dateObj) return;
        selectedDate = dateObj;

        const yyyy = dateObj.getFullYear();
        const mm = String(dateObj.getMonth() + 1).padStart(2, '0');
        const dd = String(dateObj.getDate()).padStart(2, '0');
        const isoDate = `${yyyy}-${mm}-${dd}`;

        if (hiddenInput) {
            hiddenInput.value = isoDate;
            hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
            hiddenInput.dispatchEvent(new Event('input', { bubbles: true }));
        }

        if (displayText) {
            const dayName = daysIndo[dateObj.getDay()];
            const mName = months[dateObj.getMonth()];
            displayText.textContent = `${dayName}, ${dd} ${mName} ${yyyy}`;
        }

        triggerBtn?.closest('.tugas-field-filled')?.classList.add('has-value');

        renderCalendar();

        if (shouldClose) {
            setTimeout(() => {
                popup.classList.remove('is-active');
            }, 180);
        }
    }

    if (btnCancel) {
        btnCancel.addEventListener('click', () => {
            popup.classList.remove('is-active');
        });
    }

    if (btnClear) {
        btnClear.addEventListener('click', () => {
            selectedDate = null;
            if (hiddenInput) {
                hiddenInput.value = '';
                hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
                hiddenInput.dispatchEvent(new Event('input', { bubbles: true }));
            }
            if (displayText) displayText.textContent = 'Pilih batas tanggal';
            triggerBtn?.closest('.tugas-field-filled')?.classList.remove('has-value');
            popup.classList.remove('is-active');
            renderCalendar();
        });
    }

    if (btnOk) {
        btnOk.addEventListener('click', () => {
            if (selectedDate) {
                commitDateSelection(selectedDate, true);
            } else {
                popup.classList.remove('is-active');
            }
        });
    }

    function renderCalendar() {
        if (calMonthLabel) calMonthLabel.textContent = months[viewMonth];
        if (calYearLabel) calYearLabel.textContent = viewYear;
        if (!calDatesGrid) return;

        calDatesGrid.innerHTML = '';

        const firstDayIndex = new Date(viewYear, viewMonth, 1).getDay(); // 0 is Sunday
        const daysInCurrentMonth = new Date(viewYear, viewMonth + 1, 0).getDate();
        const daysInPrevMonth = new Date(viewYear, viewMonth, 0).getDate();

        // Prev month days
        for (let i = firstDayIndex - 1; i >= 0; i--) {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'tugas-cal-date-btn';
            btn.textContent = String(daysInPrevMonth - i);
            btn.disabled = true;
            calDatesGrid.appendChild(btn);
        }

        // Current month days
        for (let day = 1; day <= daysInCurrentMonth; day++) {
            const dateObj = new Date(viewYear, viewMonth, day);
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'tugas-cal-date-btn';
            btn.textContent = String(day);

            // Cek apakah tanggal lampau
            if (dateObj < today) {
                btn.disabled = true;
            }

            // Cek apakah hari ini
            if (dateObj.getTime() === today.getTime()) {
                btn.classList.add('is-today');
            }

            // Cek apakah terpilih
            if (selectedDate && dateObj.getTime() === selectedDate.getTime()) {
                btn.classList.add('is-selected');
            }

            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                commitDateSelection(dateObj, true);
            });

            calDatesGrid.appendChild(btn);
        }
    }
}

/**
 * 4b. Popup Kalender Jadwal Publikasi
 */
function initJadwalCalendar() {
    const triggerBtn = document.getElementById('btnTriggerJadwalDate');
    const popup = document.getElementById('popupCalendarJadwal');
    const hiddenInput = document.getElementById('inputTglPublikasi');
    const displayText = document.getElementById('txtSelectedJadwalDisplay');

    const calMonthLabel = document.getElementById('calJadwalHeaderMonth');
    const calYearLabel = document.getElementById('calJadwalHeaderYear');
    const calDatesGrid = document.getElementById('calJadwalDatesGrid');

    const btnPrevMonth = document.getElementById('calJadwalPrevMonth');
    const btnNextMonth = document.getElementById('calJadwalNextMonth');
    const btnClear = document.getElementById('calJadwalBtnClear');
    const btnCancel = document.getElementById('calJadwalBtnCancel');
    const btnOk = document.getElementById('calJadwalBtnOk');

    if (!triggerBtn || !popup) return;

    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    const daysIndo = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

    const today = new Date();
    today.setHours(0, 0, 0, 0);

    let viewYear = today.getFullYear();
    let viewMonth = today.getMonth();
    let selectedDate = new Date(today);

    if (hiddenInput && hiddenInput.value) {
        const parts = hiddenInput.value.split('-');
        if (parts.length === 3) {
            selectedDate = new Date(parseInt(parts[0]), parseInt(parts[1]) - 1, parseInt(parts[2]));
            viewYear = selectedDate.getFullYear();
            viewMonth = selectedDate.getMonth();
        }
    }

    triggerBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        renderCalendar();
        popup.classList.add('is-active');
        positionCalendarPopup();
    });

    function positionCalendarPopup() {
        const rect = triggerBtn.getBoundingClientRect();
        const popupWidth = Math.min(358, window.innerWidth - 32);
        let left = rect.left;
        if (left + popupWidth > window.innerWidth - 16) {
            left = window.innerWidth - popupWidth - 16;
        }
        if (left < 16) left = 16;

        const popupHeight = popup.offsetHeight || 330;
        const spaceBelow = window.innerHeight - rect.bottom - 16;
        const spaceAbove = rect.top - 16;

        let top;
        if (spaceBelow < popupHeight && spaceAbove > spaceBelow) {
            top = Math.max(16, rect.top - popupHeight - 8);
        } else {
            top = Math.max(16, Math.min(rect.bottom + 8, window.innerHeight - popupHeight - 16));
        }

        popup.style.top = `${top}px`;
        popup.style.left = `${left}px`;
        popup.style.width = `${popupWidth}px`;
    }

    window.addEventListener('resize', () => {
        if (popup.classList.contains('is-active')) {
            positionCalendarPopup();
        }
    });

    document.addEventListener('click', (e) => {
        if (!popup.contains(e.target) && e.target !== triggerBtn && !triggerBtn.contains(e.target)) {
            popup.classList.remove('is-active');
        }
    });

    if (btnPrevMonth) {
        btnPrevMonth.addEventListener('click', () => {
            viewMonth--;
            if (viewMonth < 0) {
                viewMonth = 11;
                viewYear--;
            }
            renderCalendar();
        });
    }

    if (btnNextMonth) {
        btnNextMonth.addEventListener('click', () => {
            viewMonth++;
            if (viewMonth > 11) {
                viewMonth = 0;
                viewYear++;
            }
            renderCalendar();
        });
    }

    function commitDateSelection(dateObj, shouldClose = true) {
        if (!dateObj) return;
        selectedDate = dateObj;

        const yyyy = dateObj.getFullYear();
        const mm = String(dateObj.getMonth() + 1).padStart(2, '0');
        const dd = String(dateObj.getDate()).padStart(2, '0');
        const isoDate = `${yyyy}-${mm}-${dd}`;

        if (hiddenInput) {
            hiddenInput.value = isoDate;
            hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
            hiddenInput.dispatchEvent(new Event('input', { bubbles: true }));
        }

        if (displayText) {
            const dayName = daysIndo[dateObj.getDay()];
            const mName = months[dateObj.getMonth()];
            displayText.textContent = `${dayName}, ${dd} ${mName} ${yyyy}`;
        }

        renderCalendar();

        if (shouldClose) {
            setTimeout(() => {
                popup.classList.remove('is-active');
            }, 180);
        }
    }

    if (btnCancel) {
        btnCancel.addEventListener('click', () => {
            popup.classList.remove('is-active');
        });
    }

    if (btnClear) {
        btnClear.addEventListener('click', () => {
            selectedDate = null;
            if (hiddenInput) {
                hiddenInput.value = '';
                hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
                hiddenInput.dispatchEvent(new Event('input', { bubbles: true }));
            }
            if (displayText) displayText.textContent = 'Pilih tanggal publikasi';
            popup.classList.remove('is-active');
            renderCalendar();
        });
    }

    if (btnOk) {
        btnOk.addEventListener('click', () => {
            if (selectedDate) {
                commitDateSelection(selectedDate, true);
            } else {
                popup.classList.remove('is-active');
            }
        });
    }

    function renderCalendar() {
        if (calMonthLabel) calMonthLabel.textContent = months[viewMonth];
        if (calYearLabel) calYearLabel.textContent = viewYear;
        if (!calDatesGrid) return;

        calDatesGrid.innerHTML = '';

        const firstDayIndex = new Date(viewYear, viewMonth, 1).getDay();
        const daysInCurrentMonth = new Date(viewYear, viewMonth + 1, 0).getDate();
        const daysInPrevMonth = new Date(viewYear, viewMonth, 0).getDate();

        for (let i = firstDayIndex - 1; i >= 0; i--) {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'tugas-cal-date-btn';
            btn.textContent = String(daysInPrevMonth - i);
            btn.disabled = true;
            calDatesGrid.appendChild(btn);
        }

        for (let day = 1; day <= daysInCurrentMonth; day++) {
            const dateObj = new Date(viewYear, viewMonth, day);
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'tugas-cal-date-btn';
            btn.textContent = String(day);

            if (dateObj < today) {
                btn.disabled = true;
            }

            if (dateObj.getTime() === today.getTime()) {
                btn.classList.add('is-today');
            }

            if (selectedDate && dateObj.getTime() === selectedDate.getTime()) {
                btn.classList.add('is-selected');
            }

            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                commitDateSelection(dateObj, true);
            });

            calDatesGrid.appendChild(btn);
        }
    }
}

/**
 * 5. Split Button Opsi Tugaskan & Modal Jadwal
 */
function initPublicationSplitButton() {
    const mainBtn = document.getElementById('btnSubmitTugaskanDirect');
    const caretBtn = document.getElementById('btnToggleSplitMenu');
    const menu = document.getElementById('menuSplitOptions');
    const form = document.getElementById('formBuatTugasClassroom') || document.getElementById('formBuatTugas');
    const hiddenAksi = document.getElementById('realAksiPublikasi');

    const menuTugaskan = document.getElementById('menuOptTugaskanSekarang');
    const menuJadwalkan = document.getElementById('menuOptJadwalkan');
    const menuSimpanDraf = document.getElementById('menuOptSimpanDraf');
    const menuHapusDraf = document.getElementById('menuOptHapusDraf');

    const modalJadwal = document.getElementById('modalJadwalkanDialog');
    const cancelJadwal = document.getElementById('btnCancelJadwalkan');
    const submitJadwal = document.getElementById('btnConfirmJadwalkan');

    if (!mainBtn || !form) return;

    // Toggle Menu Opsi
    if (caretBtn && menu) {
        caretBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            menu.classList.toggle('is-open');
        });

        document.addEventListener('click', (e) => {
            if (!menu.contains(e.target) && e.target !== caretBtn) {
                menu.classList.remove('is-open');
            }
        });
    }

    // Aksi 1: Tugaskan Sekarang (Tombol Utama)
    mainBtn.addEventListener('click', (e) => {
        e.preventDefault();
        executeAjaxSubmit('sekarang');
    });

    if (menuTugaskan) {
        menuTugaskan.addEventListener('click', () => {
            menu.classList.remove('is-open');
            executeAjaxSubmit('sekarang');
        });
    }

    // Aksi 2: Buka Modal Jadwal
    if (menuJadwalkan && modalJadwal) {
        menuJadwalkan.addEventListener('click', () => {
            menu.classList.remove('is-open');
            modalJadwal.classList.add('is-active');
        });
    }

    if (cancelJadwal && modalJadwal) {
        cancelJadwal.addEventListener('click', () => {
            modalJadwal.classList.remove('is-active');
        });
        modalJadwal.addEventListener('click', (e) => {
            if (e.target === modalJadwal) {
                modalJadwal.classList.remove('is-active');
            }
        });
    }

    if (submitJadwal && modalJadwal) {
        submitJadwal.addEventListener('click', () => {
            const tglPub = document.getElementById('inputTglPublikasi')?.value;
            if (!tglPub) {
                alert('Silakan pilih tanggal publikasi tugas.');
                return;
            }
            modalJadwal.classList.remove('is-active');
            executeAjaxSubmit('terjadwal');
        });
    }

    // Aksi 3: Simpan Draf
    if (menuSimpanDraf) {
        menuSimpanDraf.addEventListener('click', () => {
            menu.classList.remove('is-open');
            executeAjaxSubmit('draft', true);
        });
    }

    // Aksi 4: Hapus Draf (Kosongkan Formulir)
    if (menuHapusDraf) {
        menuHapusDraf.addEventListener('click', () => {
            menu.classList.remove('is-open');
            if (confirm('Apakah Anda yakin ingin mengosongkan draf formulir tugas ini?')) {
                form.reset();
                const editor = document.getElementById('editorPetunjukVisual');
                if (editor) editor.innerHTML = '';
                const textarea = document.getElementById('inputPetunjuk');
                if (textarea) textarea.value = '';
                const hiddenDeadline = document.getElementById('realDeadlineInput');
                if (hiddenDeadline) hiddenDeadline.value = '';
                const judulBox = document.getElementById('inputJudul')?.closest('.tugas-field-filled');
                if (judulBox) judulBox.classList.remove('has-value', 'is-focused');
                const petunjukBox = document.getElementById('editorPetunjukVisual')?.closest('.tugas-instruction-box');
                if (petunjukBox) petunjukBox.classList.remove('has-value', 'is-focused');
                const attachedFiles = document.getElementById('attachedFilesContainer');
                if (attachedFiles) attachedFiles.innerHTML = '';
                const attachedLinks = document.getElementById('attachedLinksContainer');
                if (attachedLinks) attachedLinks.innerHTML = '';
                const lampiranMeta = document.getElementById('lampiranCountMeta');
                if (lampiranMeta) lampiranMeta.textContent = '(opsional) · 0 lampiran';
                
                // Reset Kategori Bidang Dropdown ke status awal (belum memilih)
                const hiddenKat = document.getElementById('selectKategori');
                const triggerKat = document.getElementById('btnTriggerKategori');
                const katTriggerText = document.getElementById('btnTriggerKategoriText');
                if (katTriggerText) katTriggerText.textContent = 'Pilih kategori bidang';
                if (triggerKat) triggerKat.classList.add('is-placeholder');
                if (hiddenKat) hiddenKat.value = '';
                const katOptions = document.querySelectorAll('#menuKategoriList .tugas-custom-select-option');
                katOptions.forEach(opt => {
                    opt.classList.remove('is-selected');
                    opt.setAttribute('aria-selected', 'false');
                    const check = opt.querySelector('.option-check');
                    if (check) check.remove();
                });

                showClassroomToast('Draf formulir telah dikosongkan.', 'info');
            }
        });
    }

    /**
     * Eksekusi Pengiriman Formulir Asinkron (AJAX Fetch — Tanpa Reload Halaman)
     */
    async function executeAjaxSubmit(aksi, isDraft = false) {
        if (!validateBeforeSubmit(isDraft)) {
            return;
        }

        hiddenAksi.value = aksi;
        const formData = new FormData(form);

        const originalBtnContent = mainBtn.innerHTML;
        mainBtn.classList.add('is-loading');
        mainBtn.disabled = true;

        const actionText = aksi === 'terjadwal' 
            ? 'Menjadwalkan...' 
            : (aksi === 'draft' ? 'Menyimpan...' : 'Menugaskan...');

        mainBtn.innerHTML = `
            <span class="tugas-btn-spinner" aria-hidden="true"></span>
            <span>${actionText}</span>
        `;

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            });

            const data = await response.json().catch(() => ({}));

            if (response.ok && data.success) {
                mainBtn.classList.remove('is-loading');
                mainBtn.classList.add('is-success');
                mainBtn.innerHTML = `
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    <span>Tersimpan!</span>
                `;

                showClassroomToast(data.message || 'Tugas berhasil diproses!', 'success');

                // Redirect halus ke index tugas setelah jeda singkat agar toast terlihat
                setTimeout(() => {
                    const fallbackUrl = document.querySelector('.tugas-btn-cancel')?.getAttribute('href') || '/pembimbing/tugas';
                    window.location.href = data.redirect_url || fallbackUrl;
                }, 850);
            } else {
                mainBtn.classList.remove('is-loading');
                mainBtn.disabled = false;
                mainBtn.innerHTML = originalBtnContent;

                let errorMsg = data.message || 'Gagal menyimpan tugas. Harap periksa isian formulir.';
                if (data.errors) {
                    const firstKey = Object.keys(data.errors)[0];
                    if (firstKey && data.errors[firstKey][0]) {
                        errorMsg = data.errors[firstKey][0];
                    }
                }
                showClassroomToast(errorMsg, 'error');
            }
        } catch (err) {
            console.error('[Tugas Create] AJAX submit error:', err);
            mainBtn.classList.remove('is-loading');
            mainBtn.disabled = false;
            mainBtn.innerHTML = originalBtnContent;
            showClassroomToast('Terjadi gangguan koneksi saat mengirim tugas. Silakan periksa jaringan dan coba lagi.', 'error');
        }
    }
}

/**
 * 6. Toolbar Format Deskripsi & Instruksi (Visual Rich Text Editor)
 */
function initFormattingToolbar() {
    const editor = document.getElementById('editorPetunjukVisual');
    const textarea = document.getElementById('inputPetunjuk');
    const btnBold = document.getElementById('toolBtnBold');
    const btnItalic = document.getElementById('toolBtnItalic');
    const btnUnderline = document.getElementById('toolBtnUnderline');
    const btnList = document.getElementById('toolBtnList');
    const btnClear = document.getElementById('toolBtnClear');

    if (!editor || !textarea) return;

    // Sinkronisasi konten awal dari textarea ke visual editor jika belum terisi
    if (textarea.value && !editor.innerHTML.trim()) {
        editor.innerHTML = textarea.value;
    }

    // Fungsi elastis menambah tinggi editor saat mengetik (Auto-Expand ala Classroom)
    function adjustEditorHeight() {
        editor.style.height = 'auto';
        const newHeight = Math.max(140, editor.scrollHeight);
        editor.style.height = `${newHeight}px`;
    }

    // Fungsi sinkronisasi konten editor ke textarea form
    function syncToTextarea() {
        const text = editor.innerText.trim();
        if (!text) {
            textarea.value = '';
        } else {
            textarea.value = editor.innerHTML;
        }
    }

    editor.addEventListener('input', () => {
        syncToTextarea();
        adjustEditorHeight();
    });
    editor.addEventListener('blur', syncToTextarea);

    // Jalankan auto-expand awal
    adjustEditorHeight();

    // Fungsi pembaruan status aktif tombol toolbar (Bold, Italic, Underline, List)
    function updateToolbarButtonStates() {
        if (!editor) return;
        try {
            if (btnBold) {
                btnBold.classList.toggle('is-active', Boolean(document.queryCommandState('bold')));
            }
            if (btnItalic) {
                btnItalic.classList.toggle('is-active', Boolean(document.queryCommandState('italic')));
            }
            if (btnUnderline) {
                btnUnderline.classList.toggle('is-active', Boolean(document.queryCommandState('underline')));
            }
            if (btnList) {
                btnList.classList.toggle('is-active', Boolean(document.queryCommandState('insertOrderedList') || document.queryCommandState('insertUnorderedList')));
            }
        } catch (_) {
            // Abaikan queryCommandState error pada browser tertentu
        }
    }

    editor.addEventListener('focus', updateToolbarButtonStates);
    editor.addEventListener('keyup', updateToolbarButtonStates);
    editor.addEventListener('mouseup', updateToolbarButtonStates);

    document.addEventListener('selectionchange', () => {
        if (editor && (document.activeElement === editor || editor.contains(document.activeElement))) {
            updateToolbarButtonStates();
        }
    });

    // Mencegah blur saat tombol toolbar diklik agar seleksi teks pengguna tidak hilang
    const toolbarButtons = [btnBold, btnItalic, btnUnderline, btnList, btnClear];
    toolbarButtons.forEach(btn => {
        if (btn) {
            btn.addEventListener('mousedown', (e) => {
                e.preventDefault();
            });
        }
    });

    if (btnBold) {
        btnBold.addEventListener('click', () => {
            document.execCommand('bold', false, null);
            syncToTextarea();
            updateToolbarButtonStates();
        });
    }

    if (btnItalic) {
        btnItalic.addEventListener('click', () => {
            document.execCommand('italic', false, null);
            syncToTextarea();
            updateToolbarButtonStates();
        });
    }

    if (btnUnderline) {
        btnUnderline.addEventListener('click', () => {
            document.execCommand('underline', false, null);
            syncToTextarea();
            updateToolbarButtonStates();
        });
    }

    if (btnList) {
        btnList.addEventListener('click', () => {
            document.execCommand('insertOrderedList', false, null);
            syncToTextarea();
            updateToolbarButtonStates();
        });
    }

    if (btnClear) {
        btnClear.addEventListener('click', () => {
            document.execCommand('removeFormat', false, null);
            syncToTextarea();
            updateToolbarButtonStates();
        });
    }
}

/**
 * 7. Validasi Frontend Sebelum Submit
 */
function initFormValidation() {
    // Dipanggil di dalam event listener submit
}

function validateBeforeSubmit(isDraft = false) {
    const editor = document.getElementById('editorPetunjukVisual');
    const textarea = document.getElementById('inputPetunjuk');
    if (editor && textarea) {
        const text = editor.innerText.trim();
        textarea.value = text ? editor.innerHTML : '';
    }

    const judul = document.getElementById('inputJudul')?.value.trim();
    const petunjuk = document.getElementById('inputPetunjuk')?.value.trim();
    const deadline = document.getElementById('realDeadlineInput')?.value;
    const penempatanInputs = document.querySelectorAll('input[name="penempatan_ids[]"]');

    if (penempatanInputs.length === 0) {
        showClassroomToast('Minimal satu peserta magang harus dipilih pada parameter "Tugaskan ke".', 'error');
        document.getElementById('btnOpenMenteeDialog')?.focus();
        return false;
    }

    if (!judul) {
        showClassroomToast('Judul Penugasan wajib diisi.', 'error');
        document.getElementById('inputJudul')?.focus();
        return false;
    }

    if (!isDraft && !petunjuk) {
        showClassroomToast('Petunjuk tugas wajib diisi.', 'error');
        editor?.focus();
        return false;
    }

    if (!isDraft && !deadline) {
        showClassroomToast('Tenggat waktu wajib ditentukan.', 'error');
        document.getElementById('btnTriggerDeadline')?.focus();
        return false;
    }

    const kategori = document.getElementById('selectKategori')?.value;
    if (!isDraft && !kategori) {
        showClassroomToast('Kategori Bidang tugas wajib dipilih.', 'error');
        document.getElementById('btnTriggerKategori')?.focus();
        return false;
    }

    return true;
}

/**
 * Helper Utility
 */
function formatBytes(bytes) {
    if (bytes === 0) return '0 B';
    const k = 1024;
    const sizes = ['B', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

/**
 * Toast Notification ala Google Material 3 / Classroom
 */
function showClassroomToast(message, type = 'success') {
    let container = document.getElementById('tugasToastContainer');
    if (!container) {
        container = document.createElement('div');
        container.id = 'tugasToastContainer';
        container.className = 'tugas-toast-container';
        document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = `tugas-toast tugas-toast--${type}`;

    const icon = type === 'success'
        ? `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>`
        : `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>`;

    toast.innerHTML = `
        <div class="tugas-toast-icon">${icon}</div>
        <div class="tugas-toast-text">${escapeHtml(message)}</div>
        <button type="button" class="tugas-toast-close" aria-label="Tutup notifikasi">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    `;

    container.appendChild(toast);

    requestAnimationFrame(() => {
        toast.classList.add('is-show');
    });

    const closeBtn = toast.querySelector('.tugas-toast-close');
    const dismiss = () => {
        toast.classList.remove('is-show');
        setTimeout(() => toast.remove(), 320);
    };

    if (closeBtn) closeBtn.addEventListener('click', dismiss);
    setTimeout(dismiss, 5000);
}
