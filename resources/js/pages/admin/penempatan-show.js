/**
 * Admin Penempatan Show Module
 * CSP Compliant (no inline scripts or inline event handlers)
 */

document.addEventListener('DOMContentLoaded', () => {
    function openPindahModal() {
        const modal = document.getElementById('modal-pindah-pembimbing');
        if (modal) modal.style.display = 'flex';
    }

    function closePindahModal() {
        const modal = document.getElementById('modal-pindah-pembimbing');
        if (modal) modal.style.display = 'none';
    }

    function openBatalModal() {
        const modal = document.getElementById('modal-batalkan-penempatan');
        if (modal) modal.style.display = 'flex';
    }

    function closeBatalModal() {
        const modal = document.getElementById('modal-batalkan-penempatan');
        if (modal) modal.style.display = 'none';
    }

    function togglePenempatanDropdown(e) {
        if (e) e.stopPropagation();
        const menu = document.getElementById('menuPenempatanDropdown');
        const trigger = document.getElementById('btnPenempatanDropdown');
        if (menu) {
            menu.classList.toggle('show');
            if (trigger) {
                trigger.classList.toggle('is-active', menu.classList.contains('show'));
            }
        }
    }

    function closePenempatanDropdown() {
        const menu = document.getElementById('menuPenempatanDropdown');
        const trigger = document.getElementById('btnPenempatanDropdown');
        if (menu) menu.classList.remove('show');
        if (trigger) trigger.classList.remove('is-active');
    }

    document.addEventListener('click', (e) => {
        const target = e.target;

        const dropdownBtn = target.closest('#btnPenempatanDropdown');
        if (dropdownBtn) {
            e.preventDefault();
            togglePenempatanDropdown(e);
            return;
        }

        const openPindahBtn = target.closest('[data-action="open-pindah-modal"]');
        if (openPindahBtn) {
            e.preventDefault();
            closePenempatanDropdown();
            openPindahModal();
            return;
        }

        const closePindahBtn = target.closest('[data-action="close-pindah-modal"]');
        if (closePindahBtn) {
            e.preventDefault();
            closePindahModal();
            return;
        }

        const openBatalBtn = target.closest('[data-action="open-batal-modal"]');
        if (openBatalBtn) {
            e.preventDefault();
            closePenempatanDropdown();
            openBatalModal();
            return;
        }

        const closeBatalBtn = target.closest('[data-action="close-batal-modal"]');
        if (closeBatalBtn) {
            e.preventDefault();
            closeBatalModal();
            return;
        }

        // Click outside dropdown
        const wrap = document.querySelector('.penempatan-dropdown-wrap');
        if (wrap && !wrap.contains(target)) {
            closePenempatanDropdown();
        }
    });

    window.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closePindahModal();
            closeBatalModal();
            closePenempatanDropdown();
        }
    });

    document.querySelectorAll('.modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', function (e) {
            if (e.target === this) {
                closePindahModal();
                closeBatalModal();
            }
        });
    });

    // Custom File Picker Preview & Full Clickable Dropzone for Surat Selesai
    function setupFilePicker(inputId, previewId) {
        const input = document.getElementById(inputId);
        const preview = document.getElementById(previewId);
        if (!input || !preview) return;

        const dropzoneLabel = input.closest('.upload-dropzone-panel') || document.querySelector(`label[for="${inputId}"]`);
        if (dropzoneLabel) {
            dropzoneLabel.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    input.click();
                }
            });

            ['dragenter', 'dragover'].forEach(eventName => {
                dropzoneLabel.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    dropzoneLabel.classList.add('is-dragover');
                });
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropzoneLabel.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    dropzoneLabel.classList.remove('is-dragover');
                });
            });

            dropzoneLabel.addEventListener('drop', (e) => {
                if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length > 0) {
                    input.files = e.dataTransfer.files;
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                }
            });
        }

        input.addEventListener('change', () => {
            if (!input.files || input.files.length === 0) {
                preview.style.display = 'none';
                preview.textContent = '';
                preview.className = preview.classList.contains('dynamic-file-chip')
                    ? 'dynamic-file-chip'
                    : (preview.classList.contains('compact-file-preview') ? 'compact-file-preview' : 'file-picker-preview');
                return;
            }

            const file = input.files[0];
            const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);

            if (!file.name.toLowerCase().endsWith('.pdf')) {
                preview.style.display = 'inline-flex';
                preview.className = preview.classList.contains('dynamic-file-chip')
                    ? 'dynamic-file-chip has-error'
                    : (preview.classList.contains('compact-file-preview') ? 'compact-file-preview has-error' : 'file-picker-preview has-error');
                preview.textContent = `⚠️ Berkas "${file.name}" bukan PDF. Harap pilih berkas .pdf`;
                return;
            }

            if (file.size > 2 * 1024 * 1024) {
                preview.style.display = 'inline-flex';
                preview.className = preview.classList.contains('dynamic-file-chip')
                    ? 'dynamic-file-chip has-error'
                    : (preview.classList.contains('compact-file-preview') ? 'compact-file-preview has-error' : 'file-picker-preview has-error');
                preview.textContent = `⚠️ Ukuran (${fileSizeMB} MB) melebihi batas 2.0 MB`;
                return;
            }

            preview.style.display = 'inline-flex';
            preview.className = preview.classList.contains('dynamic-file-chip')
                ? 'dynamic-file-chip'
                : (preview.classList.contains('compact-file-preview') ? 'compact-file-preview has-file' : 'file-picker-preview has-file');
            preview.textContent = `✓ ${file.name} (${fileSizeMB} MB)`;
        });
    }

    setupFilePicker('inputSuratSelesai', 'previewInputSuratSelesai');
    setupFilePicker('file_surat_kel_show', 'previewFileNameKelShow');
});
