/**
 * =========================================================================
 * MAGKOM — INTERAKSI HALAMAN EDIT TUGAS MENTEE PEMBIMBING
 * Vanilla JavaScript (ES Modules) — Mengacu pada pola Classroom Workspace.
 * =========================================================================
 */

document.addEventListener('DOMContentLoaded', () => {
    initFloatingLabels();
    initKategoriDropdown();
    initFileUpload();
    initLinkAttachment();
    initDeadlineCalendar();
    initFormattingToolbar();
    initFormValidation();
});

/**
 * 1. Material Floating Label
 */
function initFloatingLabels() {
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

    const petunjukEditor = document.getElementById('editorPetunjukVisual');
    const petunjukBox = petunjukEditor?.closest('.tugas-instruction-box');

    function updatePetunjukFloatingState() {
        if (!petunjukEditor || !petunjukBox) return;
        const text = petunjukEditor.innerText.trim();
        petunjukBox.classList.toggle('has-value', text.length > 0);
    }

    if (petunjukEditor && petunjukBox) {
        petunjukEditor.addEventListener('focus', () => {
            petunjukBox.classList.add('is-focused');
        });

        petunjukEditor.addEventListener('blur', () => {
            petunjukBox.classList.remove('is-focused');
            updatePetunjukFloatingState();
        });

        petunjukEditor.addEventListener('input', () => {
            updatePetunjukFloatingState();
            const hiddenTextarea = document.getElementById('inputPetunjuk');
            if (hiddenTextarea) {
                hiddenTextarea.value = petunjukEditor.innerHTML;
            }
        });

        updatePetunjukFloatingState();
    }
}

/**
 * 2. Kategori Dropdown Listbox
 */
function initKategoriDropdown() {
    const triggerBtn = document.getElementById('btnTriggerKategori');
    const menuList = document.getElementById('menuKategoriList');
    const triggerText = document.getElementById('btnTriggerKategoriText');
    const nativeSelect = document.getElementById('selectKategori');

    if (!triggerBtn || !menuList || !nativeSelect) return;

    function openDropdown() {
        triggerBtn.setAttribute('aria-expanded', 'true');
        menuList.classList.add('is-open');
    }

    function closeDropdown() {
        triggerBtn.setAttribute('aria-expanded', 'false');
        menuList.classList.remove('is-open');
    }

    triggerBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        const isOpen = menuList.classList.contains('is-open');
        if (isOpen) {
            closeDropdown();
        } else {
            openDropdown();
        }
    });

    document.addEventListener('click', (e) => {
        if (!triggerBtn.contains(e.target) && !menuList.contains(e.target)) {
            closeDropdown();
        }
    });

    const options = menuList.querySelectorAll('.tugas-custom-select-option');
    options.forEach((opt) => {
        opt.addEventListener('click', () => {
            const val = opt.getAttribute('data-value');
            nativeSelect.value = val;
            nativeSelect.dispatchEvent(new Event('change', { bubbles: true }));

            if (triggerText) {
                triggerText.textContent = val;
            }
            triggerBtn.classList.remove('is-placeholder');

            options.forEach((o) => {
                o.classList.remove('is-selected');
                o.setAttribute('aria-selected', 'false');
                const check = o.querySelector('.option-check');
                if (check) check.remove();
            });

            opt.classList.add('is-selected');
            opt.setAttribute('aria-selected', 'true');
            const checkSvg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
            checkSvg.setAttribute('class', 'option-check');
            checkSvg.setAttribute('width', '16');
            checkSvg.setAttribute('height', '16');
            checkSvg.setAttribute('viewBox', '0 0 24 24');
            checkSvg.setAttribute('fill', 'none');
            checkSvg.setAttribute('stroke', 'currentColor');
            checkSvg.setAttribute('stroke-width', '2.5');
            checkSvg.innerHTML = '<polyline points="20 6 9 17 4 12"></polyline>';
            opt.appendChild(checkSvg);

            closeDropdown();
        });
    });
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text ?? '';
    return div.innerHTML;
}

/**
 * 3. File Upload Modal & Attached List
 */
function initFileUpload() {
    const btnTrigger = document.getElementById('btnTriggerUploadFile');
    const modal = document.getElementById('modalUploadFileDialog');
    const btnCancel = document.getElementById('btnCancelUploadFile');
    const btnSubmit = document.getElementById('btnSubmitUploadFile');
    const btnBrowse = document.getElementById('btnBrowseFile');
    const dropzone = document.getElementById('tugasDropzoneArea');
    const modalFileInfo = document.getElementById('modalSelectedFileInfo');
    const modalFileName = document.getElementById('modalFileNameTxt');
    const modalFileSize = document.getElementById('modalFileSizeTxt');
    const btnRemoveModal = document.getElementById('btnRemoveModalFile');
    const realInput = document.getElementById('realFileInput');
    const attachedContainer = document.getElementById('attachedFilesContainer');

    if (!btnTrigger || !modal || !realInput) return;

    let pendingFile = null;

    btnTrigger.addEventListener('click', () => {
        modal.classList.add('is-open');
    });

    function closeModal() {
        modal.classList.remove('is-open');
        pendingFile = null;
        if (modalFileInfo) modalFileInfo.style.display = 'none';
        if (dropzone) dropzone.style.display = 'flex';
        if (btnSubmit) btnSubmit.disabled = true;
    }

    if (btnCancel) btnCancel.addEventListener('click', closeModal);
    modal.addEventListener('click', (e) => {
        if (e.target === modal) closeModal();
    });

    if (btnBrowse) {
        btnBrowse.addEventListener('click', (e) => {
            e.stopPropagation();
            const tempInput = document.createElement('input');
            tempInput.type = 'file';
            tempInput.accept = realInput.accept;
            tempInput.onchange = () => {
                if (tempInput.files && tempInput.files[0]) {
                    handleFileSelection(tempInput.files[0]);
                }
            };
            tempInput.click();
        });
    }

    if (dropzone) {
        dropzone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropzone.classList.add('is-dragover');
        });
        dropzone.addEventListener('dragleave', () => {
            dropzone.classList.remove('is-dragover');
        });
        dropzone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropzone.classList.remove('is-dragover');
            if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files[0]) {
                handleFileSelection(e.dataTransfer.files[0]);
            }
        });
    }

    function handleFileSelection(file) {
        pendingFile = file;
        if (modalFileName) modalFileName.textContent = file.name;
        if (modalFileSize) modalFileSize.textContent = (file.size / 1024 / 1024).toFixed(2) + ' MB';
        if (dropzone) dropzone.style.display = 'none';
        if (modalFileInfo) modalFileInfo.style.display = 'flex';
        if (btnSubmit) btnSubmit.disabled = false;
    }

    if (btnRemoveModal) {
        btnRemoveModal.addEventListener('click', () => {
            pendingFile = null;
            if (modalFileInfo) modalFileInfo.style.display = 'none';
            if (dropzone) dropzone.style.display = 'flex';
            if (btnSubmit) btnSubmit.disabled = true;
        });
    }

    if (btnSubmit) {
        btnSubmit.addEventListener('click', () => {
            if (pendingFile) {
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(pendingFile);
                realInput.files = dataTransfer.files;
                renderAttachedFile(pendingFile.name, (pendingFile.size / 1024).toFixed(1) + ' KB');
                closeModal();
            }
        });
    }

    function renderAttachedFile(name, sizeText) {
        if (!attachedContainer) return;
        attachedContainer.innerHTML = `
            <div class="tugas-attached-card" id="cardFileAttachment">
                <div class="tugas-attached-left">
                    <div class="tugas-file-badge" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                    </div>
                    <div class="tugas-attached-info">
                        <p class="tugas-attached-name" title="${escapeHtml(name)}">${escapeHtml(name)}</p>
                        <p class="tugas-attached-meta">${escapeHtml(sizeText)} &bull; Berkas Baru</p>
                    </div>
                </div>
                <button type="button" class="tugas-btn-remove-attachment" id="btnRemoveAttachedFile" title="Hapus berkas ini" aria-label="Hapus berkas ini">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
        `;

        const btnRemove = document.getElementById('btnRemoveAttachedFile');
        if (btnRemove) {
            btnRemove.addEventListener('click', () => {
                realInput.value = '';
                attachedContainer.innerHTML = '';
            });
        }
    }
}

/**
 * 4. Link Attachment Modal & Attached List
 */
function initLinkAttachment() {
    const btnTrigger = document.getElementById('btnTriggerAddLink');
    const modal = document.getElementById('modalAddLinkDialog');
    const btnCancel = document.getElementById('btnCancelAddLink');
    const btnSubmit = document.getElementById('btnSubmitAddLink');
    const inputUrl = document.getElementById('inputModalLinkUrl');
    const realInput = document.getElementById('realLinkInput');
    const attachedContainer = document.getElementById('attachedLinksContainer');

    if (!btnTrigger || !modal || !realInput) return;

    // Render initial link if present
    if (realInput.value && realInput.value.trim().length > 0) {
        renderAttachedLink(realInput.value.trim());
    }

    btnTrigger.addEventListener('click', () => {
        if (inputUrl) inputUrl.value = realInput.value || '';
        modal.classList.add('is-open');
        if (inputUrl) inputUrl.focus();
    });

    function closeModal() {
        modal.classList.remove('is-open');
    }

    if (btnCancel) btnCancel.addEventListener('click', closeModal);
    modal.addEventListener('click', (e) => {
        if (e.target === modal) closeModal();
    });

    if (inputUrl) {
        inputUrl.addEventListener('input', () => {
            const val = inputUrl.value.trim();
            if (btnSubmit) btnSubmit.disabled = val.length === 0;
        });
    }

    if (btnSubmit) {
        btnSubmit.addEventListener('click', () => {
            const url = inputUrl?.value.trim() || '';
            if (url) {
                realInput.value = url;
                renderAttachedLink(url);
                closeModal();
            }
        });
    }

    function renderAttachedLink(url) {
        if (!attachedContainer) return;

        let displayDomain = 'Tautan Referensi Eksternal';
        try {
            const parsed = new URL(url);
            displayDomain = parsed.hostname + ' &bull; Tautan Referensi';
        } catch (e) {
            // Fallback jika bukan URL standar
        }

        attachedContainer.innerHTML = `
            <div class="tugas-attached-card" id="cardLinkAttachment">
                <div class="tugas-attached-left">
                    <div class="tugas-file-badge badge-link" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
                        </svg>
                    </div>
                    <div class="tugas-attached-info">
                        <a href="${escapeHtml(url)}" target="_blank" rel="noopener noreferrer" class="tugas-attached-link-name" title="${escapeHtml(url)}">
                            <span>${escapeHtml(url)}</span>
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="tugas-external-icon" aria-hidden="true">
                                <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                                <polyline points="15 3 21 3 21 9"></polyline>
                                <line x1="10" y1="14" x2="21" y2="3"></line>
                            </svg>
                        </a>
                        <p class="tugas-attached-meta">${displayDomain}</p>
                    </div>
                </div>
                <button type="button" class="tugas-btn-remove-attachment" id="btnRemoveAttachedLink" title="Hapus tautan ini" aria-label="Hapus tautan ini">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
        `;

        const btnRemove = document.getElementById('btnRemoveAttachedLink');
        if (btnRemove) {
            btnRemove.addEventListener('click', () => {
                realInput.value = '';
                attachedContainer.innerHTML = '';
            });
        }
    }
}

/**
 * 5. Deadline Calendar Popup
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
    const daysIndo = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

    const today = new Date();
    today.setHours(0, 0, 0, 0);

    let viewYear = today.getFullYear();
    let viewMonth = today.getMonth();
    let selectedDate = null;

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
            const monthName = months[dateObj.getMonth()];
            displayText.textContent = `${dayName}, ${dd} ${monthName} ${yyyy}`;
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

        const firstDayOfMonth = new Date(viewYear, viewMonth, 1).getDay();
        const daysInMonth = new Date(viewYear, viewMonth + 1, 0).getDate();
        const prevMonthLastDate = new Date(viewYear, viewMonth, 0).getDate();

        for (let i = firstDayOfMonth - 1; i >= 0; i--) {
            const dateNum = prevMonthLastDate - i;
            const btn = createDateBtn(dateNum, true);
            calDatesGrid.appendChild(btn);
        }

        for (let day = 1; day <= daysInMonth; day++) {
            const thisDate = new Date(viewYear, viewMonth, day);
            const isSelected = selectedDate && thisDate.getTime() === selectedDate.getTime();
            const btn = createDateBtn(day, false, isSelected, thisDate);
            calDatesGrid.appendChild(btn);
        }
    }

    function createDateBtn(num, isOutside, isSelected = false, dateObj = null) {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'tugas-cal-date-cell';
        btn.textContent = num;

        if (isOutside) {
            btn.classList.add('is-outside');
            btn.disabled = true;
        } else {
            if (isSelected) btn.classList.add('is-selected');

            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                commitDateSelection(dateObj, true);
            });
        }
        return btn;
    }
}

/**
 * 6. Rich Formatting Toolbar (Bold, Italic, Underline, List, Clear)
 */
function initFormattingToolbar() {
    const editor = document.getElementById('editorPetunjukVisual');
    const btnBold = document.getElementById('toolBtnBold');
    const btnItalic = document.getElementById('toolBtnItalic');
    const btnUnderline = document.getElementById('toolBtnUnderline');
    const btnList = document.getElementById('toolBtnList');
    const btnClear = document.getElementById('toolBtnClear');

    if (!editor) return;

    function exec(command, value = null) {
        editor.focus();
        document.execCommand(command, false, value);
        syncHidden();
    }

    function syncHidden() {
        const hidden = document.getElementById('inputPetunjuk');
        if (hidden) hidden.value = editor.innerHTML;
    }

    if (btnBold) btnBold.addEventListener('click', () => exec('bold'));
    if (btnItalic) btnItalic.addEventListener('click', () => exec('italic'));
    if (btnUnderline) btnUnderline.addEventListener('click', () => exec('underline'));
    if (btnList) btnList.addEventListener('click', () => exec('insertUnorderedList'));
    if (btnClear) btnClear.addEventListener('click', () => exec('removeFormat'));
}

/**
 * 7. Form Validation & Submission Sync
 */
function initFormValidation() {
    const form = document.getElementById('formEditTugasClassroom');
    const editor = document.getElementById('editorPetunjukVisual');
    const hiddenDeskripsi = document.getElementById('inputPetunjuk');

    if (!form) return;

    form.addEventListener('submit', () => {
        if (editor && hiddenDeskripsi) {
            hiddenDeskripsi.value = editor.innerHTML;
        }
    });
}

