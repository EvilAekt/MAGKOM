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
            storageKey: 'magkom_site_editor_recovery_v1',
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
        this.initRecoveryModalEvents();
        this.initShortcutsModalEvents();

        // Periksa apakah ada local recovery yang tersimpan
        this.checkLocalRecovery(options.initialUpdatedAt);
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
                responsive_styles: doc.responsive_styles || {},
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
        mutationFn(this.currentDocument);

        if (recordHistory && !this.isApplyingHistory) {
            this.pushHistory(actionType, target, immediate);
        }

        // Evaluasi Dirty State secara akurat
        this.evaluateDirty();

        // Simpan snapshot ke local recovery storage
        this.saveToRecoveryStorage();

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
        if (!statusPill) return;

        // Reset semua class status
        statusPill.className = 'wp-save-status-pill';

        switch (this.dirtyStatus) {
            case 'clean':
                statusPill.classList.add('status-clean');
                statusPill.textContent = this.lastSavedAt
                    ? `✓ Tersimpan (${this.lastSavedAt})`
                    : '✓ Tersinkronisasi';
                break;
            case 'dirty':
                statusPill.classList.add('status-dirty');
                statusPill.textContent = '⚠️ Perubahan Belum Disimpan';
                break;
            case 'saving':
                statusPill.classList.add('status-saving');
                statusPill.textContent = '⏳ Menyimpan draf...';
                break;
            case 'saved':
                statusPill.classList.add('status-saved');
                statusPill.textContent = meta.updatedAt
                    ? `✓ Tersimpan (${meta.updatedAt})`
                    : '✓ Draf Berhasil Disimpan';
                break;
            case 'error':
                statusPill.classList.add('status-error');
                statusPill.textContent = '❌ Gagal Menyimpan Draf';
                break;
            case 'conflict':
                statusPill.classList.add('status-conflict');
                statusPill.textContent = '⚠️ Konflik Perubahan Server';
                break;
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
        this.saveToRecoveryStorage();
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
        this.saveToRecoveryStorage();
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
                responsive_styles: this.currentDocument.responsive_styles || {},
                blocks: this.currentDocument.blocks || [],
            },
            landing_global_styles: this.currentDocument.global_styles || {},
            landing_responsive_styles: this.currentDocument.responsive_styles || {},
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

                // Bersihkan local recovery storage karena server sudah tersinkronisasi
                this.clearRecoveryStorage();

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

    saveToRecoveryStorage() {
        try {
            const recoveryData = {
                document: this.currentDocument,
                version: this.documentVersion,
                timestamp: Date.now(),
                formattedTime: new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }),
            };
            localStorage.setItem(this.options.storageKey, JSON.stringify(recoveryData));
        } catch (e) {
            // Tangani kuota penuh tanpa crash
            console.warn('[EditorState] LocalStorage recovery quota exceeded or unavailable:', e);
        }
    }

    clearRecoveryStorage() {
        try {
            localStorage.removeItem(this.options.storageKey);
        } catch (e) {
            // Abaikan
        }
    }

    checkLocalRecovery(serverUpdatedAt) {
        try {
            const raw = localStorage.getItem(this.options.storageKey);
            if (!raw) return;

            const data = JSON.parse(raw);
            if (!data || !data.document) return;

            // Periksa expiration (maksimal 7 hari)
            const ageDays = (Date.now() - (data.timestamp || 0)) / (1000 * 60 * 60 * 24);
            if (ageDays > 7) {
                this.clearRecoveryStorage();
                return;
            }

            // Bandingkan isi recovery dengan server awal
            const localCanonical = this.normalizeDocumentForComparison(data.document);
            const serverCanonical = this.normalizeDocumentForComparison(this.savedDocument);

            if (localCanonical !== serverCanonical) {
                this.showRecoveryModal(data, serverUpdatedAt);
            }
        } catch (e) {
            console.error('[EditorState] Error checking recovery storage:', e);
        }
    }

    showRecoveryModal(recoveryData, serverUpdatedAt) {
        const modal = document.getElementById('wp-recovery-modal');
        if (!modal) return;

        const timeEl = document.getElementById('wp-recovery-time');
        const serverTimeEl = document.getElementById('wp-recovery-server-time');

        if (timeEl) {
            const d = new Date(recoveryData.timestamp);
            timeEl.textContent = `${d.toLocaleDateString('id-ID')} pukul ${d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })} WIB`;
        }
        if (serverTimeEl) {
            serverTimeEl.textContent = serverUpdatedAt || 'Versi Aktif Terakhir';
        }

        modal.style.display = 'flex';
        this.pendingRecoveryData = recoveryData;
    }

    closeRecoveryModal() {
        const modal = document.getElementById('wp-recovery-modal');
        if (modal) modal.style.display = 'none';
        this.pendingRecoveryData = null;
    }

    initRecoveryModalEvents() {
        document.getElementById('btn-close-recovery-modal')?.addEventListener('click', () => {
            this.closeRecoveryModal();
        });

        document.getElementById('btn-recovery-discard')?.addEventListener('click', () => {
            this.clearRecoveryStorage();
            this.closeRecoveryModal();
            if (typeof this.options.onNotifyToast === 'function') {
                this.options.onNotifyToast('Draf pemulihan lokal dibuang.', true);
            }
        });

        document.getElementById('btn-recovery-server')?.addEventListener('click', () => {
            this.closeRecoveryModal();
        });

        document.getElementById('btn-recovery-restore')?.addEventListener('click', () => {
            if (this.pendingRecoveryData && this.pendingRecoveryData.document) {
                this.currentDocument = this.deepClone(this.pendingRecoveryData.document);
                this.recordInitialSnapshot();
                this.setDirtyStatus('dirty');
                this.updateHistoryButtons();

                if (typeof this.options.onHistoryApply === 'function') {
                    this.options.onHistoryApply(this.currentDocument, 'recovery_restore');
                }

                this.closeRecoveryModal();
                if (typeof this.options.onNotifyToast === 'function') {
                    this.options.onNotifyToast('Draf lokal berhasil dipulihkan ke editor.', true);
                }
            }
        });
    }

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

            // 6. Shift + ? (Buka bantuan shortcut)
            if (e.shiftKey && (e.key === '?' || e.key === '/')) {
                if (!inInput) {
                    e.preventDefault();
                    this.openShortcutsModal();
                }
            }
        });
    }

    handleEscapeKey() {
        // 1. Tutup modal yang sedang terbuka
        const recoveryModal = document.getElementById('wp-recovery-modal');
        if (recoveryModal && recoveryModal.style.display !== 'none') {
            this.closeRecoveryModal();
            return;
        }

        const shortcutsModal = document.getElementById('wp-shortcuts-modal');
        if (shortcutsModal && shortcutsModal.style.display !== 'none') {
            this.closeShortcutsModal();
            return;
        }

        const mediaModal = document.getElementById('wp-media-modal');
        if (mediaModal && mediaModal.style.display !== 'none') {
            document.getElementById('btn-close-media-modal')?.click();
            return;
        }

        const revisionsModal = document.getElementById('wp-revisions-modal');
        if (revisionsModal && revisionsModal.style.display !== 'none') {
            document.getElementById('btn-close-revisions-modal')?.click();
            return;
        }

        const patternModal = document.getElementById('wp-pattern-preview-modal');
        if (patternModal && patternModal.style.display !== 'none') {
            document.getElementById('btn-close-pattern-preview')?.click();
            return;
        }

        const createReusableModal = document.getElementById('wp-create-reusable-modal');
        if (createReusableModal && createReusableModal.style.display !== 'none') {
            document.getElementById('btn-close-create-reusable')?.click();
            return;
        }

        const reusableUsageModal = document.getElementById('wp-reusable-usage-modal');
        if (reusableUsageModal && reusableUsageModal.style.display !== 'none') {
            document.getElementById('btn-close-reusable-usage')?.click();
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

    initShortcutsModalEvents() {
        document.getElementById('btn-open-shortcuts')?.addEventListener('click', () => {
            this.openShortcutsModal();
        });
        document.getElementById('btn-close-shortcuts-modal')?.addEventListener('click', () => {
            this.closeShortcutsModal();
        });
        document.getElementById('btn-close-shortcuts-footer')?.addEventListener('click', () => {
            this.closeShortcutsModal();
        });
    }

    openShortcutsModal() {
        const modal = document.getElementById('wp-shortcuts-modal');
        if (modal) modal.style.display = 'flex';
    }

    closeShortcutsModal() {
        const modal = document.getElementById('wp-shortcuts-modal');
        if (modal) modal.style.display = 'none';
    }
}

