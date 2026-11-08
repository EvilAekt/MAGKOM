/**
 * Revision & History Manager - Mengelola Riwayat Versi, Autosave, Undo/Redo, dan Rollback
 */

export class RevisionManager {
    constructor(initialRevisions = [], handlers = {}) {
        this.revisions = Array.isArray(initialRevisions) ? [...initialRevisions] : [];
        this.handlers = handlers; // { onRestore, onSaveDraft, onNotifyToast, onStatusChange }
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        // Client-side Undo/Redo History Stack
        this.undoStack = [];
        this.redoStack = [];
        this.isApplyingHistory = false;

        // Autosave debounce timer
        this.autosaveTimer = null;
        this.autosaveDelayMs = 1800; // 1.8 detik debounce
        this.isSaving = false;

        this.initButtons();
    }

    initButtons() {
        // Modal open & close
        document.getElementById('btn-open-revisions')?.addEventListener('click', () => {
            this.openModal();
        });
        document.getElementById('btn-close-revisions-modal')?.addEventListener('click', () => {
            this.closeModal();
        });
        document.getElementById('btn-cancel-revisions-modal')?.addEventListener('click', () => {
            this.closeModal();
        });

        // Topbar Rollback
        document.getElementById('btn-rollback')?.addEventListener('click', () => {
            this.executeRollback();
        });
    }

    /**
     * Catat snapshot lokal untuk Undo/Redo
     */
    recordSnapshot(snapshot) {
        if (this.isApplyingHistory) return;

        // Bersihkan redo stack saat ada perubahan baru
        this.redoStack = [];
        this.undoStack.push(JSON.parse(JSON.stringify(snapshot)));

        // Batasi ukuran stack memori (maks 50 langkah)
        if (this.undoStack.length > 50) {
            this.undoStack.shift();
        }

        this.updateButtonsState();
        this.triggerAutosave();
    }

    undo() {
        if (this.undoStack.length <= 1) return;

        this.isApplyingHistory = true;
        const current = this.undoStack.pop();
        this.redoStack.push(current);

        const previous = this.undoStack[this.undoStack.length - 1];
        if (previous && typeof this.handlers.onRestoreSnapshot === 'function') {
            this.handlers.onRestoreSnapshot(previous);
        }

        this.updateButtonsState();
        this.isApplyingHistory = false;
    }

    redo() {
        if (this.redoStack.length === 0) return;

        this.isApplyingHistory = true;
        const next = this.redoStack.pop();
        this.undoStack.push(next);

        if (next && typeof this.handlers.onRestoreSnapshot === 'function') {
            this.handlers.onRestoreSnapshot(next);
        }

        this.updateButtonsState();
        this.isApplyingHistory = false;
    }

    updateButtonsState() {
        const btnUndo = document.getElementById('btn-undo');
        const btnRedo = document.getElementById('btn-redo');

        if (btnUndo) {
            btnUndo.disabled = this.undoStack.length <= 1;
        }
        if (btnRedo) {
            btnRedo.disabled = this.redoStack.length === 0;
        }
    }

    /**
     * Trigger autosave otomatis dengan debouncing
     */
    triggerAutosave() {
        if (typeof this.handlers.onStatusChange === 'function') {
            this.handlers.onStatusChange('dirty');
        }

        if (this.autosaveTimer) {
            clearTimeout(this.autosaveTimer);
        }

        this.autosaveTimer = setTimeout(() => {
            if (typeof this.handlers.onAutosaveExecute === 'function') {
                this.handlers.onAutosaveExecute();
            }
        }, this.autosaveDelayMs);
    }

    /**
     * Buka modal daftar riwayat revisi
     */
    openModal() {
        const modal = document.getElementById('wp-revisions-modal');
        if (modal) modal.style.display = 'flex';

        this.fetchRevisions();
    }

    closeModal() {
        const modal = document.getElementById('wp-revisions-modal');
        if (modal) modal.style.display = 'none';
    }

    fetchRevisions() {
        fetch('/superadmin/site-editor/revisions', {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': this.csrfToken,
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success && Array.isArray(data.revisions)) {
                this.revisions = data.revisions;
                this.renderRevisionsList();
            }
        })
        .catch(err => console.error(err));
    }

    renderRevisionsList() {
        const listEl = document.getElementById('wp-revisions-list');
        if (!listEl) return;

        listEl.innerHTML = '';

        if (this.revisions.length === 0) {
            listEl.innerHTML = `
                <div style="padding: 24px; text-align: center; color: var(--wp-admin-subtle);">
                    <p>Belum ada riwayat revisi tersimpan.</p>
                </div>
            `;
            return;
        }

        this.revisions.forEach(rev => {
            const item = document.createElement('div');
            item.className = 'wp-revision-item';

            let badgeClass = 'badge-draft';
            let badgeLabel = 'Draf Sandbox';

            if (rev.status === 'published') {
                badgeClass = 'badge-published';
                badgeLabel = 'Publikasi Live';
            } else if (rev.status === 'rollback') {
                badgeClass = 'badge-rollback';
                badgeLabel = 'Rollback Live';
            } else if (rev.status === 'restored') {
                badgeClass = 'badge-restored';
                badgeLabel = 'Pemulihan Draf';
            } else if (rev.status === 'discard') {
                badgeClass = 'badge-draft';
                badgeLabel = 'Draf Dibuang';
            }

            const metaDiv = document.createElement('div');
            metaDiv.className = 'wp-revision-meta';

            const badgeSpan = document.createElement('span');
            badgeSpan.className = `wp-rev-badge ${badgeClass}`;
            badgeSpan.textContent = badgeLabel;

            const summaryEl = document.createElement('strong');
            summaryEl.textContent = rev.summary || 'Pembaruan Tampilan';

            const infoEl = document.createElement('small');
            const author = rev.author_name || 'Super Admin';
            const time = rev.created_at || '';
            const docVer = rev.document_version ? ` · v${rev.document_version}` : '';
            infoEl.textContent = `Oleh: ${author} · ${time}${docVer}`;

            metaDiv.appendChild(badgeSpan);
            metaDiv.appendChild(summaryEl);
            metaDiv.appendChild(infoEl);

            const actionsDiv = document.createElement('div');
            actionsDiv.className = 'wp-revision-actions';

            const btnPreview = document.createElement('button');
            btnPreview.type = 'button';
            btnPreview.className = 'wp-btn wp-btn-secondary wp-btn-sm btn-preview-rev';
            btnPreview.textContent = 'Pratinjau';
            btnPreview.title = 'Buka pratinjau snapshot arsip ini di tab baru';
            btnPreview.addEventListener('click', () => {
                window.open(`/superadmin/site-editor/revisions/${encodeURIComponent(rev.id)}/preview`, '_blank');
            });

            const btnRestore = document.createElement('button');
            btnRestore.type = 'button';
            btnRestore.className = 'wp-btn wp-btn-secondary wp-btn-sm btn-restore-rev';
            btnRestore.textContent = 'Pulihkan Versi Ini';
            btnRestore.title = 'Pulihkan snapshot arsip ini ke draf kerja aktif';
            btnRestore.addEventListener('click', () => {
                this.restoreRevision(rev.id);
            });

            actionsDiv.appendChild(btnPreview);
            actionsDiv.appendChild(btnRestore);

            item.appendChild(metaDiv);
            item.appendChild(actionsDiv);

            listEl.appendChild(item);
        });
    }

    restoreRevision(revId) {
        if (!confirm('Pulihkan versi draf ini? Perubahan lokal yang belum disimpan akan digantikan dengan versi terpilih.')) {
            return;
        }

        fetch(`/superadmin/site-editor/revisions/${revId}/restore`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': this.csrfToken,
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                this.closeModal();
                if (typeof this.handlers.onNotifyToast === 'function') {
                    this.handlers.onNotifyToast('Revisi berhasil dipulihkan.', true);
                }
                // Reload editor window untuk menyelaraskan kanvas dan form
                setTimeout(() => {
                    window.location.reload();
                }, 400);
            } else {
                alert(data.message || 'Gagal memulihkan revisi.');
            }
        })
        .catch(err => {
            console.error(err);
            alert('Terjadi kesalahan jaringan.');
        });
    }

    executeRollback() {
        if (!confirm('PERINGATAN: Apakah Anda yakin ingin mengembalikan tampilan Landing Page publik ke versi sebelum publikasi terakhir? Aksi ini akan langsung memulihkan backup terakhir di sisi pengunjung publik.')) {
            return;
        }

        fetch('/superadmin/site-editor/rollback', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': this.csrfToken,
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                if (typeof this.handlers.onNotifyToast === 'function') {
                    this.handlers.onNotifyToast(data.message, true);
                }
                setTimeout(() => {
                    window.location.reload();
                }, 800);
            } else {
                alert(data.message || 'Gagal melakukan rollback.');
            }
        })
        .catch(err => {
            console.error(err);
            alert('Terjadi kesalahan jaringan saat rollback.');
        });
    }
}

