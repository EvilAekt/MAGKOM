/**
 * Media Picker (Simple File Picker & Uploader)
 * Ramping tanpa form metadata, tanpa replace, tanpa pelacak pemakaian.
 */

export class MediaLibrary {
    constructor(initialMedia = [], onMediaSelected, onNotifyToast) {
        this.mediaList = Array.isArray(initialMedia) ? [...initialMedia] : [];
        this.onMediaSelected = onMediaSelected;
        this.customSelectCallback = null;
        this.onNotifyToast = onNotifyToast;
        this.selectedMedia = null;
        this.activeFilterType = '';
        this.activeSearchTerm = '';
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        this.modalEl = document.getElementById('wp-media-modal');
        this.gridEl = document.getElementById('wp-media-library-grid');
        this.selectActionBtn = document.getElementById('btn-select-media-action');
        this.deleteActionBtn = document.getElementById('btn-delete-selected-media');
        this.statusTextEl = document.getElementById('wp-media-modal-status');

        this.initEvents();
        this.renderGrid();
    }

    initEvents() {
        // Modal tabs (Browse vs Upload)
        const tabs = document.querySelectorAll('.wp-modal-tab');
        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                tabs.forEach(t => t.classList.remove('is-active'));
                tab.classList.add('is-active');

                const target = tab.getAttribute('data-media-tab');
                document.querySelectorAll('.wp-modal-pane').forEach(p => p.style.display = 'none');
                const targetPane = document.getElementById(`wp-media-pane-${target}`);
                if (targetPane) targetPane.style.display = 'flex';
            });
        });

        // Close buttons
        document.getElementById('btn-close-media-modal')?.addEventListener('click', () => this.close());
        document.getElementById('btn-cancel-media-modal')?.addEventListener('click', () => this.close());

        // Type filter chips
        document.querySelectorAll('.wp-filter-chip').forEach(chip => {
            chip.addEventListener('click', () => {
                document.querySelectorAll('.wp-filter-chip').forEach(c => c.classList.remove('is-active'));
                chip.classList.add('is-active');
                this.activeFilterType = chip.getAttribute('data-media-type-filter') || '';
                this.renderGrid();
            });
        });

        // Search input
        const searchInput = document.getElementById('wp-media-search-input');
        searchInput?.addEventListener('input', (e) => {
            this.activeSearchTerm = e.target.value.toLowerCase().trim();
            this.renderGrid();
        });

        // File upload trigger button
        const fileInput = document.getElementById('wp-media-file-input');
        document.getElementById('btn-browse-file')?.addEventListener('click', () => {
            fileInput?.click();
        });

        fileInput?.addEventListener('change', (e) => {
            if (e.target.files && e.target.files[0]) {
                this.uploadFile(e.target.files[0]);
                e.target.value = '';
            }
        });

        // Drag & Drop upload on dropzone
        const dropzone = document.getElementById('wp-upload-dropzone');
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
                if (e.dataTransfer.files && e.dataTransfer.files[0]) {
                    this.uploadFile(e.dataTransfer.files[0]);
                }
            });
        }

        // Action button "Gunakan Berkas Ini"
        this.selectActionBtn?.addEventListener('click', () => {
            if (!this.selectedMedia) return;

            if (typeof this.customSelectCallback === 'function') {
                this.customSelectCallback(this.selectedMedia);
            } else if (typeof this.onMediaSelected === 'function') {
                this.onMediaSelected(this.selectedMedia);
            }

            this.close();
        });

        // Delete button
        this.deleteActionBtn?.addEventListener('click', () => {
            this.deleteActiveMedia();
        });
    }

    open(typeFilter = '', customCallback = null) {
        this.customSelectCallback = typeof customCallback === 'function' ? customCallback : null;

        if (typeFilter) {
            this.activeFilterType = typeFilter;
            document.querySelectorAll('.wp-filter-chip').forEach(chip => {
                chip.classList.toggle('is-active', chip.getAttribute('data-media-type-filter') === typeFilter);
            });
        }

        if (this.modalEl) {
            this.modalEl.style.display = 'flex';
        }

        // Default ke tab Browse
        document.querySelector('.wp-modal-tab[data-media-tab="browse"]')?.click();
        this.fetchMedia();
    }

    close() {
        if (this.modalEl) {
            this.modalEl.style.display = 'none';
        }
        this.customSelectCallback = null;
    }

    fetchMedia() {
        fetch('/superadmin/site-editor/media', {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': this.csrfToken,
            }
        })
            .then(res => res.json())
            .then(data => {
                if (data.success && Array.isArray(data.media)) {
                    this.mediaList = data.media;
                    this.renderGrid();
                    if (this.selectedMedia) {
                        const refreshed = this.mediaList.find(m => m.id === this.selectedMedia.id);
                        if (refreshed) {
                            this.selectItem(refreshed);
                        }
                    }
                }
            })
            .catch(err => console.error('Gagal mengambil pustaka media:', err));
    }

    uploadFile(file) {
        const spinner = document.getElementById('wp-upload-spinner');
        if (spinner) spinner.style.display = 'flex';

        const formData = new FormData();
        formData.append('file', file);

        fetch('/superadmin/site-editor/media/upload', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': this.csrfToken,
            },
            body: formData,
        })
            .then(res => res.json())
            .then(data => {
                if (data.success && data.item) {
                    this.mediaList.unshift(data.item);
                    this.renderGrid();
                    this.selectItem(data.item);
                    this.toast('Berkas berhasil diunggah.', true);

                    // Otomatis pindah ke tab Browse
                    document.querySelector('.wp-modal-tab[data-media-tab="browse"]')?.click();
                } else {
                    this.toast(data.message || 'Gagal mengunggah berkas.', false);
                }
            })
            .catch(err => {
                console.error('Upload error:', err);
                this.toast('Terjadi kesalahan jaringan saat mengunggah berkas.', false);
            })
            .finally(() => {
                if (spinner) spinner.style.display = 'none';
            });
    }

    renderGrid() {
        if (!this.gridEl) return;

        let filtered = [...this.mediaList];

        if (this.activeFilterType === 'image') {
            filtered = filtered.filter(m => (m.mime_type || '').startsWith('image/'));
        } else if (this.activeFilterType === 'pdf') {
            filtered = filtered.filter(m => (m.mime_type || '') === 'application/pdf');
        }

        if (this.activeSearchTerm) {
            filtered = filtered.filter(m => {
                const name = (m.original_name || m.filename || '').toLowerCase();
                return name.includes(this.activeSearchTerm);
            });
        }

        if (filtered.length === 0) {
            this.gridEl.innerHTML = `
                <div class="wp-empty-media-msg" style="grid-column: 1 / -1; text-align: center; padding: 40px 16px; color: var(--wp-admin-subtle);">
                    <p style="font-weight: 500; font-size: 13px;">Tidak ada berkas yang sesuai filter.</p>
                </div>
            `;
            return;
        }

        this.gridEl.innerHTML = filtered.map(item => {
            const isSelected = this.selectedMedia && this.selectedMedia.id === item.id;
            const isImage = (item.mime_type || '').startsWith('image/');
            const displayName = item.original_name || item.filename || 'Berkas';

            return `
                <div class="wp-media-card ${isSelected ? 'is-selected' : ''}" data-media-id="${item.id}" title="${displayName}">
                    ${isImage
                    ? `<img src="${item.url}" alt="${displayName}" loading="lazy" onerror="this.onerror=null;this.src='/images/gedung-kominfo.jpg';">`
                    : `<div class="wp-pdf-icon-card" style="font-size: 26px;">📄</div>`
                }
                    <span class="wp-media-card-name">${displayName}</span>
                </div>
            `;
        }).join('');

        this.gridEl.querySelectorAll('.wp-media-card').forEach(card => {
            card.addEventListener('click', () => {
                const id = card.getAttribute('data-media-id');
                const item = this.mediaList.find(m => m.id === id);
                if (item) {
                    this.selectItem(item);
                }
            });
        });
    }

    selectItem(item) {
        this.selectedMedia = item;

        this.gridEl?.querySelectorAll('.wp-media-card').forEach(c => {
            c.classList.toggle('is-selected', c.getAttribute('data-media-id') === item.id);
        });

        if (this.selectActionBtn) {
            this.selectActionBtn.removeAttribute('disabled');
        }
        if (this.deleteActionBtn) {
            this.deleteActionBtn.style.display = 'inline-block';
        }
        if (this.statusTextEl) {
            this.statusTextEl.textContent = `Terpilih: ${item.original_name || item.filename} (${item.size_formatted || ''})`;
        }
    }

    deleteActiveMedia() {
        if (!this.selectedMedia) return;

        const name = this.selectedMedia.original_name || this.selectedMedia.filename;
        if (!confirm(`Hapus berkas "${name}"?`)) return;

        const id = this.selectedMedia.id;
        fetch(`/superadmin/site-editor/media/${id}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': this.csrfToken,
            }
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    this.mediaList = this.mediaList.filter(m => m.id !== id);
                    this.selectedMedia = null;
                    if (this.selectActionBtn) this.selectActionBtn.setAttribute('disabled', 'disabled');
                    if (this.deleteActionBtn) this.deleteActionBtn.style.display = 'none';
                    if (this.statusTextEl) this.statusTextEl.textContent = 'Pilih berkas yang ingin digunakan.';
                    this.renderGrid();
                    this.toast('Berkas berhasil dihapus.', true);
                } else {
                    this.toast(data.message || 'Gagal menghapus berkas.', false);
                }
            })
            .catch(err => {
                console.error('Delete error:', err);
                this.toast('Gagal menghapus berkas.', false);
            });
    }

    toast(msg, isSuccess) {
        if (typeof this.onNotifyToast === 'function') {
            this.onNotifyToast(msg, isSuccess);
        }
    }
}
