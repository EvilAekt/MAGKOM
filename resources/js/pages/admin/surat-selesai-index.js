/**
 * Surat Selesai Magang - Admin Controller Script
 * Two-Tier Focused Workspace Interactions & Compact File Input Preview
 */

document.addEventListener('DOMContentLoaded', () => {
    initSuratSelesaiWorkspace();
});

export function initSuratSelesaiWorkspace() {
    initFileInputPreview('file_surat_ind', 'previewFileNameInd');
    initFileInputPreview('file_surat_kel', 'previewFileNameKel');
}

/**
 * Memberikan feedback nama dan ukuran berkas PDF yang dipilih secara dinamis,
 * mendukung drag & drop ke seluruh area card dropzone,
 * serta memvalidasi batas ukuran maksimal 2MB di sisi browser.
 */
function initFileInputPreview(inputId, previewId) {
    const fileInput = document.getElementById(inputId);
    const previewBox = document.getElementById(previewId);

    if (!fileInput || !previewBox) return;

    const dropzoneLabel = fileInput.closest('.upload-dropzone-panel') || document.querySelector(`label[for="${inputId}"]`);
    if (dropzoneLabel) {
        dropzoneLabel.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                fileInput.click();
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
                fileInput.files = e.dataTransfer.files;
                fileInput.dispatchEvent(new Event('change', { bubbles: true }));
            }
        });
    }

    fileInput.addEventListener('change', () => {
        if (!fileInput.files || fileInput.files.length === 0) {
            previewBox.style.display = 'none';
            previewBox.textContent = '';
            previewBox.className = previewBox.classList.contains('dynamic-file-chip')
                ? 'dynamic-file-chip'
                : 'compact-file-preview';
            return;
        }

        const file = fileInput.files[0];
        const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);

        // Validasi ekstensi PDF
        if (!file.name.toLowerCase().endsWith('.pdf')) {
            previewBox.style.display = 'inline-flex';
            previewBox.className = previewBox.classList.contains('dynamic-file-chip')
                ? 'dynamic-file-chip has-error'
                : 'compact-file-preview has-error';
            previewBox.textContent = `⚠️ Berkas "${file.name}" bukan PDF. Harap pilih berkas .pdf`;
            return;
        }

        // Validasi ukuran berkas maksimal 2MB
        if (file.size > 2 * 1024 * 1024) {
            previewBox.style.display = 'inline-flex';
            previewBox.className = previewBox.classList.contains('dynamic-file-chip')
                ? 'dynamic-file-chip has-error'
                : 'compact-file-preview has-error';
            previewBox.textContent = `⚠️ Ukuran berkas (${fileSizeMB} MB) melebihi batas maksimal 2.0 MB!`;
            return;
        }

        // Tampilan sukses
        previewBox.style.display = 'inline-flex';
        previewBox.className = previewBox.classList.contains('dynamic-file-chip')
            ? 'dynamic-file-chip'
            : 'compact-file-preview has-file';
        previewBox.textContent = `✓ ${file.name} (${fileSizeMB} MB)`;
    });
}
