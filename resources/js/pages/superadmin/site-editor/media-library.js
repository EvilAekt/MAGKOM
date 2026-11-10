/**
 * Media Library Manager - Pengelolaan Berkas Foto, PDF, dan Picker Modal
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
        this.sidebarEl = document.getElementById('wp-media-detail-sidebar');
        this.selectActionBtn = document.getElementById('btn-select-media-action');

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

        // Action button "Gunakan Media Ini"
        this.selectActionBtn?.addEventListener('click', () => {
            if (!this.selectedMedia) return;

            if (typeof this.customSelectCallback === 'function') {
                this.customSelectCallback(this.selectedMedia);
            } else if (typeof this.onMediaSelected === 'function') {
                this.onMediaSelected(this.selectedMedia);
            }

            this.close();
        });

        // Save media metadata (alt text, caption, title, description)
        document.getElementById('btn-save-media-meta')?.addEventListener('click', () => {
            this.saveMetadata();
        });

        // Replace media button & input
        const replaceInput = document.getElementById('wp-replace-file-input');
        document.getElementById('btn-replace-media')?.addEventListener('click', () => {
            if (!this.selectedMedia) return;
            replaceInput?.click();
        });

        replaceInput?.addEventListener('change', (e) => {
            if (e.target.files && e.target.files[0]) {
                this.replaceActiveMedia(e.target.files[0]);
            }
        });

        // Delete media button
        document.getElementById('btn-delete-media')?.addEventListener('click', () => {
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
        formData.append('alt_text', file.name.replace(/\.[^/.]+$/, ''));
        formData.append('title', file.name);

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
                // Beralih kembali ke tab browse
                document.querySelector('.wp-modal-tab[data-media-tab="browse"]')?.click();
                if (typeof this.onNotifyToast === 'function') {
                    this.onNotifyToast('Berkas berhasil diunggah ke Media Library.', true);
                }
            } else {
                alert('Gagal mengunggah berkas: ' + (data.message || 'Error validasi format/ukuran.'));
            }
        })
        .catch(err => {
            console.error(err);
            alert('Terjadi kesalahan jaringan saat mengunggah berkas.');
        })
        .finally(() => {
            if (spinner) spinner.style.display = 'none';
            const fileInput = document.getElementById('wp-media-file-input');
            if (fileInput) fileInput.value = '';
        });
    }

    replaceActiveMedia(file) {
        if (!this.selectedMedia) return;

        const spinner = document.getElementById('wp-upload-spinner');
        if (spinner) spinner.style.display = 'flex';

        const formData = new FormData();
        formData.append('file', file);

        fetch(`/superadmin/site-editor/media/${this.selectedMedia.id}/replace`, {
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
                const idx = this.mediaList.findIndex(m => m.id === this.selectedMedia.id);
                if (idx !== -1) {
                    this.mediaList[idx] = data.item;
                }
                this.renderGrid();
                this.selectItem(data.item);
                if (typeof this.onNotifyToast === 'function') {
                    this.onNotifyToast('Berkas media berhasil diganti dengan berkas baru.', true);
                }
            } else {
                alert('Gagal mengganti berkas: ' + (data.message || 'Format atau ukuran tidak valid.'));
            }
        })
        .catch(err => {
            console.error(err);
            alert('Terjadi kesalahan jaringan saat mengganti berkas.');
        })
        .finally(() => {
            if (spinner) spinner.style.display = 'none';
            const replaceInput = document.getElementById('wp-replace-file-input');
            if (replaceInput) replaceInput.value = '';
        });
    }

    renderGrid() {
        if (!this.gridEl) return;

        this.gridEl.innerHTML = '';

        let filtered = this.mediaList;
        if (this.activeFilterType === 'image') {
            filtered = filtered.filter(m => (m.mime_type || '').startsWith('image/'));
        } else if (this.activeFilterType === 'pdf') {
            filtered = filtered.filter(m => (m.mime_type || '') === 'application/pdf');
        }

        if (this.activeSearchTerm) {
            filtered = filtered.filter(m => {
                const name = (m.original_name || '').toLowerCase();
                const alt = (m.alt_text || '').toLowerCase();
                const title = (m.title || '').toLowerCase();
                const desc = (m.description || '').toLowerCase();
                return name.includes(this.activeSearchTerm)
                    || alt.includes(this.activeSearchTerm)
                    || title.includes(this.activeSearchTerm)
                    || desc.includes(this.activeSearchTerm);
            });
        }

        if (filtered.length === 0) {
            this.gridEl.innerHTML = `
                <div style="grid-column: 1 / -1; padding: 30px; text-align: center; color: var(--wp-admin-subtle);">
                    <p>Tidak ada berkas media yang cocok dengan pencarian.</p>
                </div>
            `;
            return;
        }

        filtered.forEach(item => {
            const isImage = (item.mime_type || '').startsWith('image/');
            const card = document.createElement('div');
            card.className = `wp-media-card ${this.selectedMedia && this.selectedMedia.id === item.id ? 'is-selected' : ''}`;
            card.setAttribute('data-id', item.id);

            if (isImage) {
                card.innerHTML = `
                    <img src="${item.url}" alt="${item.alt_text || item.original_name}" loading="lazy">
                    <span class="wp-media-card-name">${item.original_name}</span>
                `;
            } else {
                card.innerHTML = `
                    <span style="font-size: 28px;">📄</span>
                    <span class="wp-media-card-name">${item.original_name}</span>
                `;
            }

            card.addEventListener('click', () => {
                this.selectItem(item);
            });

            this.gridEl.appendChild(card);
        });
    }

    selectItem(item) {
        this.selectedMedia = item;

        // Tandai card aktif
        document.querySelectorAll('.wp-media-card').forEach(c => {
            c.classList.toggle('is-selected', c.getAttribute('data-id') === item.id);
        });

        // Buka sidebar detail
        if (this.sidebarEl) {
            this.sidebarEl.style.display = 'flex';

            const thumbEl = document.getElementById('wp-sidebar-thumb');
            const pdfEl = document.getElementById('wp-sidebar-pdf-preview');
            const isImage = (item.mime_type || '').startsWith('image/');

            if (isImage && thumbEl) {
                thumbEl.src = item.url;
                thumbEl.style.display = 'block';
                if (pdfEl) pdfEl.style.display = 'none';
            } else if (pdfEl) {
                pdfEl.style.display = 'block';
                if (thumbEl) thumbEl.style.display = 'none';
            }

            const filenameEl = document.getElementById('wp-sidebar-filename');
            const filesizeEl = document.getElementById('wp-sidebar-filesize');
            const dimEl = document.getElementById('wp-sidebar-dimensions');
            const filedateEl = document.getElementById('wp-sidebar-filedate');
            const downloadLink = document.getElementById('wp-sidebar-download-link');
            const altInput = document.getElementById('wp-sidebar-alt');
            const capInput = document.getElementById('wp-sidebar-caption');
            const titleInput = document.getElementById('wp-sidebar-title');
            const descInput = document.getElementById('wp-sidebar-description');
            const idInput = document.getElementById('wp-sidebar-media-id');
            const usageListEl = document.getElementById('wp-sidebar-usage-list');

            if (filenameEl) filenameEl.textContent = item.original_name;
            if (filesizeEl) filesizeEl.textContent = item.size_formatted || `${Math.round(item.size_bytes / 1024)} KB`;

            if (dimEl) {
                if (item.dimensions) {
                    dimEl.textContent = `${item.dimensions} px`;
                    dimEl.style.display = 'inline-block';
                } else {
                    dimEl.style.display = 'none';
                }
            }

            if (filedateEl) filedateEl.textContent = item.created_at || '';
            if (downloadLink) {
                downloadLink.href = item.url;
                downloadLink.setAttribute('download', item.original_name);
            }

            // Tampilkan rincian status penggunaan (Usage Tracking)
            if (usageListEl) {
                usageListEl.innerHTML = '';
                const usages = Array.isArray(item.usage_locations) ? item.usage_locations : [];
                if (usages.length > 0) {
                    usages.forEach(loc => {
                        const badge = document.createElement('span');
                        badge.className = `wp-usage-badge ${loc.includes('Live') ? 'is-live' : ''}`;
                        badge.textContent = loc;
                        usageListEl.appendChild(badge);
                    });
                } else {
                    usageListEl.innerHTML = '<span class="wp-usage-empty">Belum digunakan di Landing Page</span>';
                }
            }

            if (altInput) altInput.value = item.alt_text || '';
            if (capInput) capInput.value = item.caption || '';
            if (titleInput) titleInput.value = item.title || '';
            if (descInput) descInput.value = item.description || '';
            if (idInput) idInput.value = item.id;
        }

        if (this.selectActionBtn) {
            this.selectActionBtn.removeAttribute('disabled');
        }
    }

    saveMetadata() {
        if (!this.selectedMedia) return;

        const altText = document.getElementById('wp-sidebar-alt')?.value || '';
        const caption = document.getElementById('wp-sidebar-caption')?.value || '';
        const title = document.getElementById('wp-sidebar-title')?.value || '';
        const description = document.getElementById('wp-sidebar-description')?.value || '';

        fetch(`/superadmin/site-editor/media/${this.selectedMedia.id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': this.csrfToken,
            },
            body: JSON.stringify({
                alt_text: altText,
                caption: caption,
                title: title,
                description: description,
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                this.selectedMedia.alt_text = altText;
                this.selectedMedia.caption = caption;
                this.selectedMedia.title = title;
                this.selectedMedia.description = description;
                if (typeof this.onNotifyToast === 'function') {
                    this.onNotifyToast('Metadata media berhasil disimpan.', true);
                }
            } else {
                alert('Gagal menyimpan metadata: ' + (data.message || 'Error'));
            }
        })
        .catch(err => {
            console.error(err);
            alert('Terjadi kesalahan jaringan.');
        });
    }

    deleteActiveMedia() {
        if (!this.selectedMedia) return;

        // Peringatan jika sedang digunakan
        if (this.selectedMedia.is_in_use || (this.selectedMedia.usage_count && this.selectedMedia.usage_count > 0)) {
            const locs = (this.selectedMedia.usage_locations || []).join('\n - ');
            alert(`Berkas ini TIDAK DAPAT DIHAPUS karena sedang digunakan pada:\n - ${locs}\n\nLepaskan berkas dari halaman sebelum menghapusnya.`);
            return;
        }

        if (!confirm(`Hapus berkas "${this.selectedMedia.original_name}" secara permanen dari sistem?`)) {
            return;
        }

        fetch(`/superadmin/site-editor/media/${this.selectedMedia.id}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': this.csrfToken,
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                this.mediaList = this.mediaList.filter(m => m.id !== this.selectedMedia.id);
                this.selectedMedia = null;
                if (this.sidebarEl) this.sidebarEl.style.display = 'none';
                if (this.selectActionBtn) this.selectActionBtn.setAttribute('disabled', 'disabled');
                this.renderGrid();
                if (typeof this.onNotifyToast === 'function') {
                    this.onNotifyToast(data.message || 'Berkas berhasil dihapus.', true);
                }
            } else {
                alert(data.message || 'Gagal menghapus berkas.');
            }
        })
        .catch(err => {
            console.error(err);
            alert('Terjadi kesalahan jaringan.');
        });
    }
}
