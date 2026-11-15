/**
 * Peserta Pendaftaran Detail Module
 * Handles Document Previews and File Upload Dropzones
 * CSP Compliant (no inline scripts or inline event handlers)
 */

document.addEventListener('DOMContentLoaded', () => {
    // --- Document Preview Modal ---
    let previewTimer = null;

    function handlePreviewLoaded() {
        if (previewTimer) {
            clearTimeout(previewTimer);
            previewTimer = null;
        }
        const loading = document.getElementById('previewLoading');
        if (loading) loading.style.display = 'none';
    }

    function handlePreviewError() {
        if (previewTimer) {
            clearTimeout(previewTimer);
            previewTimer = null;
        }
        const loading = document.getElementById('previewLoading');
        const iframe = document.getElementById('previewIframe');
        const imgWrapper = document.getElementById('previewImageWrapper');
        const errBox = document.getElementById('previewError');

        if (loading) loading.style.display = 'none';
        if (iframe) iframe.style.display = 'none';
        if (imgWrapper) imgWrapper.style.display = 'none';
        if (errBox) errBox.style.display = 'flex';
    }

    function openDocPreview(previewUrl, downloadUrl, title, mime) {
        const modal = document.getElementById('docPreviewModal');
        const modalTitle = document.getElementById('previewModalTitle');
        const openNewTabBtn = document.getElementById('previewOpenNewTabBtn');
        const downloadBtn = document.getElementById('previewDownloadBtn');
        const fallbackBtn = document.getElementById('previewFallbackDownloadBtn');
        const loading = document.getElementById('previewLoading');
        const iframe = document.getElementById('previewIframe');
        const imgWrapper = document.getElementById('previewImageWrapper');
        const img = document.getElementById('previewImage');
        const errBox = document.getElementById('previewError');

        if (!modal) return;

        if (previewTimer) {
            clearTimeout(previewTimer);
            previewTimer = null;
        }

        loading.style.display = 'flex';
        iframe.style.display = 'none';
        iframe.onload = null;
        iframe.src = 'about:blank';
        imgWrapper.style.display = 'none';
        if (img) {
            img.onload = null;
            img.onerror = null;
            img.removeAttribute('src');
        }
        errBox.style.display = 'none';

        if (modalTitle) modalTitle.textContent = title || 'Pratinjau Dokumen';
        if (openNewTabBtn) openNewTabBtn.href = previewUrl;
        if (downloadBtn) downloadBtn.href = downloadUrl;
        if (fallbackBtn) fallbackBtn.href = downloadUrl;

        const isPdf = (mime && mime.toLowerCase().includes('pdf')) || (!mime && previewUrl.toLowerCase().includes('pdf'));

        if (isPdf) {
            const finalPdfUrl = previewUrl.includes('#') ? previewUrl : (previewUrl + '#toolbar=0&navpanes=0');
            iframe.onload = function () {
                handlePreviewLoaded();
            };
            iframe.style.display = 'block';
            iframe.src = finalPdfUrl;

            previewTimer = setTimeout(function () {
                handlePreviewLoaded();
            }, 600);
        } else {
            img.onload = function () {
                handlePreviewLoaded();
            };
            img.onerror = function () {
                handlePreviewError();
            };
            img.src = previewUrl;
            imgWrapper.style.display = 'flex';

            previewTimer = setTimeout(function () {
                handlePreviewLoaded();
            }, 700);
        }

        modal.classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function closeDocPreview() {
        if (previewTimer) {
            clearTimeout(previewTimer);
            previewTimer = null;
        }
        const modal = document.getElementById('docPreviewModal');
        const iframe = document.getElementById('previewIframe');
        const img = document.getElementById('previewImage');

        if (modal) {
            modal.classList.remove('open');
            document.body.style.overflow = '';
        }
        if (iframe) {
            iframe.onload = null;
            iframe.src = 'about:blank';
        }
        if (img) {
            img.onload = null;
            img.onerror = null;
            img.removeAttribute('src');
        }
    }

    // --- Dropzone & File Selection ---
    function toggleDropzone(key) {
        const wrapper = document.getElementById('dropzone_wrapper_' + key);
        if (wrapper) {
            wrapper.style.display = (wrapper.style.display === 'none' || wrapper.style.display === '') ? 'flex' : 'none';
        }
    }

    function resetFileSelection(key) {
        const input = document.getElementById('file_input_' + key);
        if (input) input.value = '';

        const bar = document.getElementById('selected_bar_' + key);
        if (bar) bar.style.display = 'none';
    }

    function handleFileSelected(input, key, maksMb) {
        const file = input.files[0];
        if (!file) return;

        const allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png'];
        const ext = file.name.split('.').pop().toLowerCase();
        const maxBytes = maksMb * 1024 * 1024;

        if (!allowedExtensions.includes(ext)) {
            const msg = 'Format berkas tidak diizinkan. Hanya file PDF, JPG, JPEG, atau PNG yang diterima.';
            if (window.MagKomToast) window.MagKomToast.warning(msg); else alert(msg);
            input.value = '';
            resetFileSelection(key);
            return;
        }

        if (file.size > maxBytes) {
            const msg = `Ukuran berkas (${(file.size / (1024 * 1024)).toFixed(2)} MB) melebihi batas maksimal ${maksMb} MB.`;
            if (window.MagKomToast) window.MagKomToast.warning(msg); else alert(msg);
            input.value = '';
            resetFileSelection(key);
            return;
        }

        const bar = document.getElementById('selected_bar_' + key);
        const nameSpan = document.getElementById('selected_name_' + key);
        const sizeSpan = document.getElementById('selected_size_' + key);

        if (nameSpan) nameSpan.textContent = file.name;
        if (sizeSpan) sizeSpan.textContent = `(${(file.size / 1024).toFixed(1)} KB)`;
        if (bar) bar.style.display = 'flex';
    }

    // --- Drag and Drop Listeners ---
    document.querySelectorAll('.pemberkasan-dropzone-interactive').forEach(dz => {
        dz.addEventListener('dragover', (e) => {
            e.preventDefault();
            e.stopPropagation();
            dz.classList.add('dragover');
        });

        dz.addEventListener('dragleave', (e) => {
            e.preventDefault();
            e.stopPropagation();
            dz.classList.remove('dragover');
        });

        dz.addEventListener('drop', (e) => {
            e.preventDefault();
            e.stopPropagation();
            dz.classList.remove('dragover');

            const key = dz.dataset.dropzoneKey;
            const maksMb = parseFloat(dz.dataset.maxMb) || 2;
            const files = e.dataTransfer.files;

            if (files && files.length > 0 && key) {
                const input = document.getElementById('file_input_' + key);
                if (input) {
                    input.files = files;
                    handleFileSelected(input, key, maksMb);
                }
            }
        });
    });

    // --- Delegated Click Listeners ---
    document.addEventListener('click', (e) => {
        const target = e.target;

        // Open Doc Preview
        const previewBtn = target.closest('[data-action="open-doc-preview"]');
        if (previewBtn) {
            e.preventDefault();
            openDocPreview(
                previewBtn.dataset.previewUrl,
                previewBtn.dataset.downloadUrl,
                previewBtn.dataset.title,
                previewBtn.dataset.mime
            );
            return;
        }

        // Close Doc Preview
        const closeDocBtn = target.closest('[data-action="close-doc-preview"]');
        if (closeDocBtn) {
            e.preventDefault();
            closeDocPreview();
            return;
        }

        // Toggle dropzone
        const toggleDzBtn = target.closest('[data-action="toggle-dropzone"]');
        if (toggleDzBtn) {
            e.preventDefault();
            toggleDropzone(toggleDzBtn.dataset.key);
            return;
        }

        // Trigger file input
        const triggerInputBtn = target.closest('[data-action="trigger-file-input"]');
        if (triggerInputBtn) {
            e.preventDefault();
            const input = document.getElementById('file_input_' + triggerInputBtn.dataset.key);
            if (input) input.click();
            return;
        }

        // Reset file selection
        const resetBtn = target.closest('[data-action="reset-file-selection"]');
        if (resetBtn) {
            e.preventDefault();
            resetFileSelection(resetBtn.dataset.key);
            return;
        }

        // Modal backdrop click
        const modal = document.getElementById('docPreviewModal');
        if (target === modal) {
            closeDocPreview();
            return;
        }
    });

    // File input change listener
    document.addEventListener('change', (e) => {
        if (e.target.matches('.doc-file-input')) {
            const key = e.target.dataset.key;
            const maksMb = parseFloat(e.target.dataset.maxMb) || 2;
            handleFileSelected(e.target, key, maksMb);
        }
    });

    // Form submit handlers (spinner feedback)
    document.addEventListener('submit', (e) => {
        const form = e.target;
        if (form.matches('.form-upload-berkas')) {
            const key = form.dataset.uploadKey;
            const btn = document.getElementById('btn_submit_' + key);
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = `<span style="display:inline-block;width:12px;height:12px;border:2px solid #fff;border-top-color:transparent;border-radius:50%;animation:spin 0.6s linear infinite;margin-right:6px;"></span> Mengunggah...`;
            }
        } else if (form.id === 'formAjukanPendaftaran') {
            const btn = document.getElementById('btnAjukanPendaftaran');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = `<span style="display:inline-block;width:14px;height:14px;border:2px solid #fff;border-top-color:transparent;border-radius:50%;animation:spin 0.6s linear infinite;margin-right:8px;"></span> Mengajukan...`;
            }
        }
    });

    window.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' || e.key === 'Esc') {
            closeDocPreview();
        }
    });
});
