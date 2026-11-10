/**
 * Block Management Engine - Mengelola Struktur Blok, List View, dan Operasi Blok
 * MAGKOM WordPress.org-Style Visual Site Editor
 */

export class BlockManager {
    constructor(initialLayout = [], blockRegistry = {}, canvasBridge, onLayoutChange) {
        this.layout = Array.isArray(initialLayout) && initialLayout.length > 0
            ? [...initialLayout]
            : [];
        this.registry = blockRegistry;
        this.bridge = canvasBridge;
        this.onLayoutChange = onLayoutChange;
        this.activeBlockId = null;

        this.initListView();
    }

    setLayout(newLayout) {
        this.layout = [...newLayout];
        this.renderListView();
    }

    getLayout() {
        return this.layout;
    }

    getActiveBlock() {
        return this.layout.find(b => b.id === this.activeBlockId || b.type === this.activeBlockId);
    }

    setActiveBlock(blockIdOrType) {
        const found = this.layout.find(b => b.id === blockIdOrType || b.type === blockIdOrType);
        this.activeBlockId = found ? found.id : blockIdOrType;

        // Beri tanda visual di List View tree
        document.querySelectorAll('.wp-listview-item').forEach(item => {
            const match = item.getAttribute('data-block-id') === this.activeBlockId ||
                          item.getAttribute('data-block-type') === blockIdOrType;
            item.classList.toggle('is-selected', match);
        });
    }

    /**
     * Tambah blok baru ke struktur halaman
     */
    addBlock(type, targetIndex = null) {
        const reg = this.registry[type];
        if (!reg) return null;

        // Periksa batasan blok non-repeatable (misal Hero, Footer, Kuota, Tahapan)
        if (reg.repeatable === false) {
            const existing = this.layout.some(b => b.type === type);
            if (existing) {
                alert(`Blok "${reg.name}" bersifat unik dan sudah ada di halaman.`);
                return null;
            }
        }

        const newId = `block-${type}-${Date.now().toString(36)}`;
        const newBlock = {
            id: newId,
            type: type,
            title: reg.name,
            visible: true,
            locked: reg.required ?? false,
            user_locked: false,
            order: this.layout.length + 1,
            content: { ...(reg.default_content || {}) },
            styles: { ...(reg.default_styles || {}) },
        };

        if (targetIndex !== null && targetIndex >= 0 && targetIndex < this.layout.length) {
            this.layout.splice(targetIndex + 1, 0, newBlock);
        } else {
            // Sisipkan sebelum footer jika ada footer di akhir
            const footerIdx = this.layout.findIndex(b => b.type === 'footer');
            if (footerIdx !== -1) {
                this.layout.splice(footerIdx, 0, newBlock);
            } else {
                this.layout.push(newBlock);
            }
        }

        this.recalculateOrders();
        this.renderListView();
        this.notifyChange();
        this.setActiveBlock(newId);

        if (this.bridge) {
            this.bridge.reorderBlocks(this.layout);
            this.bridge.scrollToBlock(type, newId);
            this.bridge.selectBlockInCanvas(type, newId);
        }

        return newBlock;
    }

    /**
     * Duplikasi blok yang dipilih
     */
    duplicateBlock(blockId) {
        const index = this.layout.findIndex(b => b.id === blockId || b.type === blockId);
        if (index === -1) return null;

        const source = this.layout[index];
        const reg = this.registry[source.type];

        // Validasi: Blok wajib sistem atau non-repeatable tidak boleh diduplikasi
        if (reg && (reg.repeatable === false || reg.required)) {
            alert(`Blok "${source.title}" bersifat unik atau wajib dan tidak dapat diduplikasi.`);
            return null;
        }

        const newId = `block-${source.type}-${Date.now().toString(36)}`;
        const clone = {
            ...JSON.parse(JSON.stringify(source)),
            id: newId,
            title: `${source.title} (Salinan)`,
            order: index + 2,
            locked: false,
            user_locked: false,
        };

        this.layout.splice(index + 1, 0, clone);
        this.recalculateOrders();
        this.renderListView();
        this.notifyChange();
        this.setActiveBlock(newId);

        if (this.bridge) {
            this.bridge.reorderBlocks(this.layout);
            this.bridge.scrollToBlock(source.type, newId);
            this.bridge.selectBlockInCanvas(source.type, newId);
        }

        return clone;
    }

    /**
     * Hapus blok dengan proteksi System Locked dan User Locked
     */
    deleteBlock(blockId) {
        const index = this.layout.findIndex(b => b.id === blockId || b.type === blockId);
        if (index === -1) return false;

        const block = this.layout[index];
        const reg = this.registry[block.type];

        // 1. Proteksi System Locked (Hero, Footer)
        if ((reg && reg.required) || block.locked) {
            alert(`Blok "${block.title}" adalah bagian utama halaman (System Locked) dan tidak boleh dihapus.`);
            return false;
        }

        // 2. Proteksi User Locked
        if (block.user_locked) {
            alert(`Blok "${block.title}" sedang dikunci (User Locked). Buka kunci terlebih dahulu untuk menghapus.`);
            return false;
        }

        if (!confirm(`Hapus blok "${block.title}" dari Landing Page?`)) {
            return false;
        }

        this.layout.splice(index, 1);
        this.recalculateOrders();
        this.renderListView();
        this.notifyChange();

        if (this.bridge) {
            this.bridge.reorderBlocks(this.layout);
        }

        return true;
    }

    /**
     * Pindah posisi blok (Move Up / Move Down)
     */
    moveBlock(blockId, direction) {
        const index = this.layout.findIndex(b => b.id === blockId || b.type === blockId);
        if (index === -1) return false;

        const block = this.layout[index];
        if (block.user_locked) {
            alert(`Blok "${block.title}" sedang dikunci dan tidak dapat dipindahkan posisinya.`);
            return false;
        }

        const targetIndex = direction === 'up' ? index - 1 : index + 1;
        if (targetIndex < 0 || targetIndex >= this.layout.length) {
            return false;
        }

        const targetBlock = this.layout[targetIndex];
        if (targetBlock.user_locked) {
            alert(`Blok sasaran "${targetBlock.title}" sedang dikunci dan posisinya tidak dapat digeser.`);
            return false;
        }

        // Tukar posisi elemen
        this.layout[index] = targetBlock;
        this.layout[targetIndex] = block;

        this.recalculateOrders();
        this.renderListView();
        this.notifyChange();

        if (this.bridge) {
            this.bridge.reorderBlocks(this.layout);
            this.bridge.scrollToBlock(block.type, block.id);
        }

        return true;
    }

    /**
     * Ubah status terlihat / tersembunyi (Toggle Visibility)
     */
    toggleVisibility(blockId) {
        const block = this.layout.find(b => b.id === blockId || b.type === blockId);
        if (!block) return;

        block.visible = !block.visible;
        this.renderListView();
        this.notifyChange();

        if (this.bridge) {
            this.bridge.setBlockVisibility(block.type, block.visible, block.id);
        }
    }

    /**
     * Ubah status penguncian blok (Lock / Unlock)
     */
    toggleLock(blockId) {
        const block = this.layout.find(b => b.id === blockId || b.type === blockId);
        if (!block) return;

        const reg = this.registry[block.type];
        if ((reg && reg.required) || block.locked) {
            alert(`Blok "${block.title}" adalah blok sistem wajib dan status pengunciannya tidak dapat diubah.`);
            return;
        }

        block.user_locked = !Boolean(block.user_locked);
        this.renderListView();
        this.notifyChange();
    }

    recalculateOrders() {
        this.layout.forEach((b, idx) => {
            b.order = idx + 1;
        });
    }

    notifyChange() {
        if (typeof this.onLayoutChange === 'function') {
            this.onLayoutChange(this.layout);
        }
    }

    /**
     * Inisialisasi tampilan List View pohon struktur halaman
     */
    initListView() {
        this.renderListView();
    }

    renderListView() {
        const treeEl = document.getElementById('wp-listview-tree');
        if (!treeEl) return;

        treeEl.innerHTML = '';

        this.layout.forEach((block, idx) => {
            const reg = this.registry[block.type] || {};
            const isRequired = Boolean(reg.required ?? (reg.locked ?? false));
            const isUserLocked = Boolean(block.user_locked);
            const isLocked = isRequired || isUserLocked;
            const icon = reg.icon || '🔷';

            const li = document.createElement('li');
            li.className = `wp-listview-item ${block.visible ? '' : 'is-hidden'} ${block.id === this.activeBlockId ? 'is-selected' : ''}`;
            li.setAttribute('data-block-id', block.id);
            li.setAttribute('data-block-type', block.type);
            li.setAttribute('data-order', block.order);
            li.setAttribute('draggable', !isUserLocked ? 'true' : 'false');
            li.setAttribute('role', 'treeitem');

            let lockBadgeHtml = '';
            if (isRequired) {
                lockBadgeHtml = `<span class="wp-listview-badge wp-badge-system-locked" title="Terkunci oleh sistem (Wajib)">🔒 Sistem</span>`;
            } else if (isUserLocked) {
                lockBadgeHtml = `<span class="wp-listview-badge wp-badge-user-locked" title="Terkunci oleh Super Admin">🔒 Terkunci</span>`;
            }

            let reusableBadgeHtml = '';
            if (block.reusable_id) {
                reusableBadgeHtml = `<span class="wp-listview-badge wp-badge-reusable" title="Reusable Block tersinkronisasi">🧩 Reusable</span>`;
            }

            li.innerHTML = `
                <span class="wp-drag-handle ${isUserLocked ? 'is-locked' : ''}" title="${isUserLocked ? 'Terkunci - buka kunci untuk memindahkan' : 'Tarik untuk mengatur urutan'}">⠿</span>
                <span class="wp-listview-icon">${icon}</span>
                <div class="wp-listview-title-wrap">
                    <span class="wp-listview-title">${block.title}</span>
                    ${reusableBadgeHtml}
                    ${lockBadgeHtml}
                </div>
                <div class="wp-listview-actions">
                    <button type="button" class="wp-listview-btn btn-toggle-visibility" title="${block.visible ? 'Sembunyikan' : 'Tampilkan'}">
                        <span class="eye-icon">${block.visible ? '👁️' : '👁️‍🗨️'}</span>
                    </button>
                    <button type="button" class="wp-listview-btn btn-toggle-lock" title="${isRequired ? 'Blok sistem wajib tidak dapat diubah status kuncinya' : (isUserLocked ? 'Buka Kunci Blok' : 'Kunci Blok')}" ${isRequired ? 'disabled' : ''}>
                        <span>${isLocked ? '🔒' : '🔓'}</span>
                    </button>
                    <button type="button" class="wp-listview-btn btn-move-up" title="Pindah ke Atas" ${idx === 0 || isUserLocked ? 'disabled' : ''}>
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="18 15 12 9 6 15"></polyline></svg>
                    </button>
                    <button type="button" class="wp-listview-btn btn-move-down" title="Pindah ke Bawah" ${idx === this.layout.length - 1 || isUserLocked ? 'disabled' : ''}>
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </button>
                    <button type="button" class="wp-listview-btn btn-duplicate" title="Duplikasi Blok Ini" ${(reg && reg.repeatable === false) || isRequired ? 'disabled' : ''}>
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                    </button>
                    ${!isRequired ? `
                    <button type="button" class="wp-listview-btn btn-delete text-danger" title="Hapus Blok" ${isUserLocked ? 'disabled' : ''}>
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                    </button>
                    ` : ''}
                </div>
            `;

            // 1. Klik item -> seleksi & scroll kanvas
            li.addEventListener('click', (e) => {
                if (e.target.closest('.wp-listview-actions')) return;
                this.setActiveBlock(block.id);
                if (this.bridge) {
                    this.bridge.scrollToBlock(block.type, block.id);
                    this.bridge.selectBlockInCanvas(block.type, block.id);
                }
            });

            // 2. Action buttons
            li.querySelector('.btn-toggle-visibility')?.addEventListener('click', (e) => {
                e.stopPropagation();
                this.toggleVisibility(block.id);
            });
            li.querySelector('.btn-toggle-lock')?.addEventListener('click', (e) => {
                e.stopPropagation();
                this.toggleLock(block.id);
            });
            li.querySelector('.btn-move-up')?.addEventListener('click', (e) => {
                e.stopPropagation();
                this.moveBlock(block.id, 'up');
            });
            li.querySelector('.btn-move-down')?.addEventListener('click', (e) => {
                e.stopPropagation();
                this.moveBlock(block.id, 'down');
            });
            li.querySelector('.btn-duplicate')?.addEventListener('click', (e) => {
                e.stopPropagation();
                this.duplicateBlock(block.id);
            });
            li.querySelector('.btn-delete')?.addEventListener('click', (e) => {
                e.stopPropagation();
                this.deleteBlock(block.id);
            });

            // 3. HTML5 Drag and Drop Reordering
            li.addEventListener('dragstart', (e) => {
                if (isUserLocked) {
                    e.preventDefault();
                    return;
                }
                e.dataTransfer.setData('text/plain', block.id);
                li.classList.add('is-dragging');
            });

            li.addEventListener('dragend', () => {
                li.classList.remove('is-dragging');
            });

            li.addEventListener('dragover', (e) => {
                e.preventDefault();
                li.classList.add('is-drag-over');
            });

            li.addEventListener('dragleave', () => {
                li.classList.remove('is-drag-over');
            });

            li.addEventListener('drop', (e) => {
                e.preventDefault();
                li.classList.remove('is-drag-over');
                const draggedId = e.dataTransfer.getData('text/plain');
                if (draggedId && draggedId !== block.id) {
                    this.handleDropReorder(draggedId, block.id);
                }
            });

            treeEl.appendChild(li);
        });

        const countEl = document.getElementById('wp-listview-count');
        if (countEl) {
            countEl.textContent = `${this.layout.length} Blok`;
        }
    }

    handleDropReorder(draggedId, targetId) {
        const fromIdx = this.layout.findIndex(b => b.id === draggedId);
        const toIdx = this.layout.findIndex(b => b.id === targetId);

        if (fromIdx === -1 || toIdx === -1 || fromIdx === toIdx) return;

        const draggedBlock = this.layout[fromIdx];
        if (draggedBlock.user_locked) {
            alert(`Blok "${draggedBlock.title}" sedang dikunci dan posisinya tidak dapat dipindahkan.`);
            return;
        }

        const targetBlock = this.layout[toIdx];
        if (targetBlock.user_locked) {
            alert(`Blok target "${targetBlock.title}" sedang dikunci.`);
            return;
        }

        const [moved] = this.layout.splice(fromIdx, 1);
        this.layout.splice(toIdx, 0, moved);

        this.recalculateOrders();
        this.renderListView();
        this.notifyChange();

        if (this.bridge) {
            this.bridge.reorderBlocks(this.layout);
        }
    }

    /**
     * Cek apakah blok merupakan Reusable Block
     */
    isReusable(blockId) {
        const b = this.layout.find(item => item.id === blockId || item.type === blockId);
        return Boolean(b && b.reusable_id);
    }

    /**
     * Menandai blok sebagai Reusable Block
     */
    markAsReusable(blockId, reusableId, title = null) {
        const b = this.layout.find(item => item.id === blockId || item.type === blockId);
        if (!b) return;
        b.reusable_id = reusableId;
        if (title) b.title = title;
        this.renderListView();
        this.notifyChange();
    }

    /**
     * Mengonversi instance Reusable Block menjadi blok reguler mandiri (Detach)
     */
    detachReusable(blockId) {
        const b = this.layout.find(item => item.id === blockId || item.type === blockId);
        if (!b) return;
        delete b.reusable_id;
        this.renderListView();
        this.notifyChange();
    }
}
