/**
 * Central Editor State Management - MAGKOM WordPress-Style Visual Site Editor
 * Mengelola: Single Source of Truth, Dirty State, Autosave, Undo/Redo, Draft Recovery, dan Keyboard Shortcuts.
 */

export class EditorState {
    constructor(initialDocument = {}, options = {}) {
        this.options = {
            autosaveDelay: 1500, // 1.5 detik debounce autosave
            historyGroupingWindow: 1000, // 1 detik grouping untuk inline typing
            maxHistory: 50, // Maksimal 50 langkah undo/redo
            ...options,
        };

        // 1. Central State Properties
        this.documentVersion = options.documentVersion || 1;
        this.currentDocument = this.deepClone(initialDocument);
        this.savedDocument = this.deepClone(initialDocument);
        this.selectedBlockId = options.selectedBlockId || null;
        this.currentViewport = 'desktop';

        // 2. History Stack (Undo / Redo)
        this.undoStack = [];
        this.redoStack = [];
        this.isApplyingHistory = false;
        this.lastHistoryAction = null;
        this.lastHistoryTarget = null;
        this.lastHistoryTime = 0;

        // 3. State Status Machine: 'clean' | 'dirty' | 'saving' | 'saved' | 'error' | 'conflict'
        this.dirtyStatus = options.initialDirty ? 'dirty' : 'clean';
        this.hasUserInteracted = !!options.initialDirty;
        this.notifBarTimeout = null;
        this.isSaving = false;
        this.lastSavedAt = options.initialUpdatedAt || null;
        this.saveError = null;

        // 4. Concurrency & Sequencing
        this.saveSequence = 0;
        this.pendingSaveAfterCurrent = false;
        this.autosaveTimer = null;
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        // Catat snapshot awal ke undo stack
        this.recordInitialSnapshot();

        // Inisialisasi status pill & keyboard shortcuts
        this.updateStatusPill();
        this.updateHistoryButtons();
        this.initKeyboardShortcuts();
    }

    deepClone(obj) {
        if (!obj || typeof obj !== 'object') return obj;
        try {
            return JSON.parse(JSON.stringify(obj));
        } catch (e) {
            console.error('Deep clone error:', e);
            return {};
        }
    }

    normalizeDocumentForComparison(doc) {
        if (!doc) return '';
        // Buat representasi kanonik terurut untuk perbandingan presisi
        try {
            const canonical = {
                blocks: (doc.blocks || []).map(b => ({
                    id: b.id,
                    type: b.type,
                    label: b.label,
                    order: b.order,
                    is_visible: b.is_visible !== false,
                    is_locked: !!b.is_locked,
                    content: b.content || {},
                    styles: b.styles || {},
                })),
                global_styles: doc.global_styles || {},
                content: doc.content || {},
            };
            return JSON.stringify(canonical);
        } catch (e) {
            return JSON.stringify(doc);
        }
    }

    // ========================================================
    // MUTATION API
    // ========================================================

    /**
     * Mutasi dokumen dengan kontrol riwayat dan status perubahan terpusat.
     */
    mutate(actionType, mutationFn, options = {}) {
        if (typeof mutationFn !== 'function') return;

        const {
            recordHistory = true,
            target = null,
            immediate = false,
        } = options;

        // Eksekusi mutasi pada currentDocument
        this.hasUserInteracted = true;
        mutationFn(this.currentDocument);

        if (recordHistory && !this.isApplyingHistory) {
            this.pushHistory(actionType, target, immediate);
        }

        // Evaluasi Dirty State secara akurat
        this.evaluateDirty();

        // Jadwalkan autosave jika ada perubahan
        if (this.dirtyStatus === 'dirty') {
            this.triggerAutosave();
        }

        // Panggil listener eksternal
        if (typeof this.options.onStateChange === 'function') {
            this.options.onStateChange(this.currentDocument, actionType);
        }
    }

    evaluateDirty() {
        if (this.dirtyStatus === 'saving') return;

        const currentCanonical = this.normalizeDocumentForComparison(this.currentDocument);
        const savedCanonical = this.normalizeDocumentForComparison(this.savedDocument);

        if (currentCanonical === savedCanonical) {
            this.setDirtyStatus('clean');
        } else {
            this.setDirtyStatus('dirty');
        }
    }

    setDirtyStatus(status, meta = {}) {
        this.dirtyStatus = status;
        this.updateStatusPill(meta);

        if (typeof this.options.onStatusChange === 'function') {
            this.options.onStatusChange(status, meta);
        }
    }

    updateStatusPill(meta = {}) {
        const statusPill = document.getElementById('wp-save-status');
        const notifBar = document.getElementById('wp-top-notification-bar');
        const notifIcon = document.getElementById('wp-top-notification-icon');
        const notifText = document.getElementById('wp-top-notification-text');

        let statusClass = 'status-clean';
        let statusText = 'Tersinkronisasi';
        let statusIcon = '✓';
        let autoHideDelay = null;

        switch (this.dirtyStatus) {
            case 'clean':
                statusClass = 'status-clean';
                statusIcon = '✓';
                statusText = this.lastSavedAt
                    ? `✓ Tersimpan (${this.lastSavedAt})`
                    : '✓ Tersinkronisasi';
                autoHideDelay = 3500;
                break;
            case 'dirty':
                statusClass = 'status-dirty';
                statusIcon = '⚠️';
                statusText = 'Perubahan Terdeteksi (Draf Belum Disimpan)';
                autoHideDelay = null; // Tetap tampil selama ada perubahan belum disimpan
                break;
            case 'saving':
                statusClass = 'status-saving';
                statusIcon = '⏳';
                statusText = 'Menyimpan Draf...';
                autoHideDelay = null;
                break;
            case 'saved':
                statusClass = 'status-saved';
                statusIcon = '✓';
                statusText = meta.updatedAt
                    ? `✓ Tersimpan (${meta.updatedAt})`
                    : (this.lastSavedAt ? `✓ Tersimpan (${this.lastSavedAt})` : '✓ Draf Berhasil Disimpan');
                autoHideDelay = 4000;
                break;
            case 'error':
                statusClass = 'status-error';
                statusIcon = '❌';
                statusText = '❌ Gagal Menyimpan Draf';
                autoHideDelay = 6000;
                break;
            case 'conflict':
                statusClass = 'status-conflict';
                statusIcon = '⚠️';
                statusText = '⚠️ Konflik Perubahan Server';
                autoHideDelay = 6000;
                break;
        }

        // 1. Update legacy pill jika masih ada di DOM
        if (statusPill) {
            statusPill.className = 'wp-save-status-pill ' + statusClass;
            statusPill.textContent = statusText;
        }

        // 2. Update Floating Top Notification Bar
        if (notifBar && notifText) {
            if (this.notifBarTimeout) {
                clearTimeout(this.notifBarTimeout);
                this.notifBarTimeout = null;
            }

            notifBar.className = 'wp-top-notification-bar ' + statusClass;
            notifText.textContent = statusText;
            if (notifIcon) notifIcon.textContent = statusIcon;

            // Jangan tampilkan notifikasi saat kondisi clean awal sebelum ada interaksi apa pun
            if (this.dirtyStatus === 'clean' && !this.hasUserInteracted) {
                notifBar.style.display = 'none';
            } else {
                notifBar.style.display = 'inline-flex';
                notifBar.style.opacity = '1';

                if (autoHideDelay) {
                    this.notifBarTimeout = setTimeout(() => {
                        notifBar.style.opacity = '0';
                        setTimeout(() => {
                            if (notifBar.style.opacity === '0') {
                                notifBar.style.display = 'none';
                            }
                        }, 300);
                    }, autoHideDelay);
                }
            }
        }
    }

    // ========================================================
    // UNDO / REDO ENGINE
    // ========================================================

    recordInitialSnapshot() {
        this.undoStack = [this.deepClone(this.currentDocument)];
        this.redoStack = [];
    }

    pushHistory(actionType, target = null, immediate = false) {
        const now = Date.now();
        const canGroup = !immediate &&
            this.lastHistoryAction === actionType &&
            this.lastHistoryTarget === target &&
            (now - this.lastHistoryTime) < this.options.historyGroupingWindow;

        if (canGroup && this.undoStack.length > 0) {
            // Update snapshot puncak yang sedang diedit berturut-turut (grouping)
            this.undoStack[this.undoStack.length - 1] = this.deepClone(this.currentDocument);
            this.lastHistoryTime = now;
            return;
        }

        // Potong redo stack saat ada aksi baru
        this.redoStack = [];

        this.undoStack.push(this.deepClone(this.currentDocument));

        if (this.undoStack.length > this.options.maxHistory) {
            this.undoStack.shift();
        }

        this.lastHistoryAction = actionType;
        this.lastHistoryTarget = target;
        this.lastHistoryTime = now;

        this.updateHistoryButtons();
    }

    canUndo() {
        return this.undoStack.length > 1;
    }

    canRedo() {
        return this.redoStack.length > 0;
    }

    undo() {
        if (!this.canUndo()) return false;

        this.isApplyingHistory = true;

        // Pindahkan kondisi saat ini ke redo stack
        const current = this.undoStack.pop();
        this.redoStack.push(current);

        // Ambil snapshot sebelumnya
        const previous = this.undoStack[this.undoStack.length - 1];
        this.currentDocument = this.deepClone(previous);

        this.evaluateDirty();
        this.updateHistoryButtons();

        if (typeof this.options.onHistoryApply === 'function') {
            this.options.onHistoryApply(this.currentDocument, 'undo');
        }

        this.isApplyingHistory = false;
        return true;
    }

    redo() {
        if (!this.canRedo()) return false;

        this.isApplyingHistory = true;

        const next = this.redoStack.pop();
        this.undoStack.push(next);
        this.currentDocument = this.deepClone(next);

        this.evaluateDirty();
        this.updateHistoryButtons();

        if (typeof this.options.onHistoryApply === 'function') {
            this.options.onHistoryApply(this.currentDocument, 'redo');
        }

        this.isApplyingHistory = false;
        return true;
    }

    updateHistoryButtons() {
        const btnUndo = document.getElementById('btn-undo');
        const btnRedo = document.getElementById('btn-redo');

        if (btnUndo) {
            btnUndo.disabled = !this.canUndo();
        }
        if (btnRedo) {
            btnRedo.disabled = !this.canRedo();
        }
    }

    // ========================================================
    // AUTOSAVE & PERSISTENCE ENGINE
    // ========================================================

    triggerAutosave() {
        if (this.autosaveTimer) {
            clearTimeout(this.autosaveTimer);
        }

        this.autosaveTimer = setTimeout(() => {
            this.saveDraft(true);
        }, this.options.autosaveDelay);
    }

    /**
     * Menyimpan draf ke backend dengan proteksi konkurensi dan request sequencing.
     */
    async saveDraft(isAutosave = false, force = false) {
        // Jika sedang ada request penyimpanan aktif di jaringan, tandai pending
        if (this.isSaving) {
            this.pendingSaveAfterCurrent = true;
            return;
        }

        // Jika autosave dipanggil saat status sudah clean, lewati
        if (isAutosave && this.dirtyStatus === 'clean') {
            return;
        }

        if (this.autosaveTimer) {
            clearTimeout(this.autosaveTimer);
            this.autosaveTimer = null;
        }

        this.isSaving = true;
        this.saveSequence++;
        const currentReqSeq = this.saveSequence;

        this.setDirtyStatus('saving');

        const saveBtn = document.getElementById('btn-save-draft');
        if (!isAutosave && saveBtn) {
            saveBtn.disabled = true;
            const spinner = saveBtn.querySelector('.btn-spinner');
            if (spinner) spinner.style.display = 'inline-block';
        }

        const payload = {
            expected_version: this.documentVersion,
            force: force ? 1 : 0,
            landing_blocks_layout: this.currentDocument.blocks || [],
            landing_page_document: {
                schema_version: '1.1',
                global_styles: this.currentDocument.global_styles || {},
                blocks: this.currentDocument.blocks || [],
            },
            landing_global_styles: this.currentDocument.global_styles || {},
            ...this.currentDocument.content,
        };

        try {
            const response = await fetch('/superadmin/site-editor/save-draft', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify(payload),
            });

            const data = await response.json();

            // Jika respons sudah basi (ketinggalan oleh request yang lebih baru), abaikan
            if (currentReqSeq !== this.saveSequence) {
                console.warn('[EditorState] Response request basi diabaikan.');
                return;
            }

            if (response.status === 409 || data.conflict) {
                // Terdeteksi konflik versi server
                this.setDirtyStatus('conflict');
                if (typeof this.options.onNotifyToast === 'function') {
                    this.options.onNotifyToast('Konflik: Draf telah diperbarui di sesi lain. Simpan paksa atau muat ulang.', false);
                }
                return;
            }

            if (data.success) {
                this.documentVersion = data.document_version || (this.documentVersion + 1);
                this.lastSavedAt = data.updated_at || 'Baru saja';
                this.savedDocument = this.deepClone(this.currentDocument);
                this.saveError = null;

                this.setDirtyStatus('saved', { updatedAt: this.lastSavedAt });

                if (!isAutosave && typeof this.options.onNotifyToast === 'function') {
                    this.options.onNotifyToast(data.message || 'Draf berhasil disimpan ke Sandbox.', true);
                }

                // Beri waktu 2.5 detik lalu ubah status saved menjadi clean
                setTimeout(() => {
                    if (this.dirtyStatus === 'saved') {
                        this.setDirtyStatus('clean');
                    }
                }, 2500);
            } else {
                this.setDirtyStatus('error');
                this.saveError = data.message || 'Gagal menyimpan draf';
                if (!isAutosave && typeof this.options.onNotifyToast === 'function') {
                    this.options.onNotifyToast(`Gagal menyimpan: ${this.saveError}`, false);
                }
            }
        } catch (error) {
            console.error('[EditorState] Kesalahan jaringan autosave:', error);
            if (currentReqSeq === this.saveSequence) {
                this.setDirtyStatus('error');
                this.saveError = 'Kesalahan koneksi jaringan';
                if (!isAutosave && typeof this.options.onNotifyToast === 'function') {
                    this.options.onNotifyToast('Terjadi kesalahan jaringan saat menyimpan draf.', false);
                }
            }
        } finally {
            this.isSaving = false;

            if (!isAutosave && saveBtn) {
                saveBtn.disabled = false;
                const spinner = saveBtn.querySelector('.btn-spinner');
                if (spinner) spinner.style.display = 'none';
            }

            // Jika ada mutasi selama request berjalan, lakukan save berikutnya
            if (this.pendingSaveAfterCurrent) {
                this.pendingSaveAfterCurrent = false;
                this.triggerAutosave();
            }
        }
    }

    // ========================================================
    // DRAFT RECOVERY ENGINE
    // ========================================================

    // ========================================================
    // KEYBOARD SHORTCUTS
    // ========================================================

    isTypingInInput(target) {
        if (!target) return false;
        const tagName = target.tagName ? target.tagName.toUpperCase() : '';
        return tagName === 'INPUT' ||
            tagName === 'TEXTAREA' ||
            tagName === 'SELECT' ||
            target.isContentEditable;
    }

    initKeyboardShortcuts() {
        window.addEventListener('keydown', (e) => {
            const isCtrlOrCmd = e.ctrlKey || e.metaKey;
            const key = e.key.toLowerCase();
            const target = e.target;
            const inInput = this.isTypingInInput(target);

            // 1. Ctrl + S (Simpan Draf) -> Selalu diizinkan walau sedang fokus di input
            if (isCtrlOrCmd && key === 's') {
                e.preventDefault();
                this.saveDraft(false);
                return;
            }

            // 2. Ctrl + Z (Undo) -> Cegah default HANYA jika bukan sedang mengetik di input text
            if (isCtrlOrCmd && key === 'z' && !e.shiftKey) {
                if (!inInput) {
                    e.preventDefault();
                    this.undo();
                }
                return;
            }

            // 3. Ctrl + Y atau Ctrl + Shift + Z (Redo) -> Cegah default jika bukan di input text
            if ((isCtrlOrCmd && key === 'y') || (isCtrlOrCmd && e.shiftKey && key === 'z')) {
                if (!inInput) {
                    e.preventDefault();
                    this.redo();
                }
                return;
            }

            // 4. Delete / Backspace (Hapus blok terpilih) -> HANYA jika TIDAK sedang mengetik di input text
            if ((e.key === 'Delete' || e.key === 'Backspace') && !inInput) {
                if (this.selectedBlockId && typeof this.options.onDeleteSelectedBlock === 'function') {
                    e.preventDefault();
                    this.options.onDeleteSelectedBlock(this.selectedBlockId);
                }
                return;
            }

            // 5. Escape (Batalkan seleksi / Tutup panel/modal)
            if (e.key === 'Escape') {
                e.preventDefault();
                this.handleEscapeKey();
                return;
            }
        });
    }

    handleEscapeKey() {
        // 1. Tutup modal media jika terbuka
        const mediaModal = document.getElementById('wp-media-modal');
        if (mediaModal && mediaModal.style.display !== 'none') {
            document.getElementById('btn-close-media-modal')?.click();
            return;
        }

        // 2. Tutup drawer inserter atau list view
        const inserter = document.getElementById('wp-inserter-drawer');
        if (inserter && inserter.style.display !== 'none') {
            document.getElementById('btn-close-inserter')?.click();
            return;
        }

        const listView = document.getElementById('wp-list-view-drawer');
        if (listView && listView.style.display !== 'none') {
            document.getElementById('btn-close-list-view')?.click();
            return;
        }

        // 3. Batalkan seleksi blok aktif
        if (typeof this.options.onDeselectBlock === 'function') {
            this.options.onDeselectBlock();
        }
    }
}

