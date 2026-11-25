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
        return this.findBlock(this.activeBlockId);
    }

    findBlockIndex(blockIdOrType) {
        if (!blockIdOrType) return -1;
        // 1. Exact ID match
        let idx = this.layout.findIndex(b => b.id === blockIdOrType);
        if (idx !== -1) return idx;
        // 2. Exact Type match
        idx = this.layout.findIndex(b => b.type === blockIdOrType);
        if (idx !== -1) return idx;
        // 3. Prefix / Partial match (misal 'block-hero-1' atau 'block-hero' cocok ke tipe 'hero')
        idx = this.layout.findIndex(b => {
            return blockIdOrType === `block-${b.type}` ||
                blockIdOrType.startsWith(`block-${b.type}-`) ||
                b.id.startsWith(blockIdOrType) ||
                blockIdOrType.startsWith(b.id);
        });
        return idx;
    }

    findBlock(blockIdOrType) {
        const idx = this.findBlockIndex(blockIdOrType);
        return idx !== -1 ? this.layout[idx] : null;
    }

    getBlockCapabilities(blockIdOrType) {
        const idx = this.findBlockIndex(blockIdOrType);
        if (idx === -1) {
            return {
                exists: false,
                canMoveUp: false,
                canMoveDown: false,
                canDuplicate: false,
                canDelete: false,
                isLocked: false,
                isRequired: false,
                title: '',
                type: '',
            };
        }

        const block = this.layout[idx];
        const reg = this.registry[block.type] || {};
        const isRequired = Boolean(reg.required ?? (reg.locked ?? false));
        const isUserLocked = Boolean(block.user_locked);
        const coreBlocks = ['hero', 'footer', 'kuota', 'tahapan', 'faq'];
        const isCoreSection = coreBlocks.includes(block.type);

        const canMoveUp = idx > 0 && !isUserLocked;
        const canMoveDown = idx < this.layout.length - 1 && !isUserLocked;
        const canDuplicate = !isUserLocked && !isRequired && reg.repeatable !== false;
        const canDelete = !isUserLocked && !isRequired && !block.locked && !isCoreSection;

        return {
            exists: true,
            index: idx,
            totalBlocks: this.layout.length,
            block: block,
            title: block.title,
            type: block.type,
            canMoveUp,
            canMoveDown,
            canDuplicate,
            canDelete,
            isLocked: isRequired || isUserLocked,
            isUserLocked,
            isRequired,
            isCoreSection,
        };
    }

    setActiveBlock(blockIdOrType) {
        const found = this.findBlock(blockIdOrType);
        this.activeBlockId = found ? found.id : blockIdOrType;

        // Beri tanda visual di List View tree
        document.querySelectorAll('.wp-listview-item').forEach(item => {
            const itemBlockId = item.getAttribute('data-block-id');
            const itemBlockType = item.getAttribute('data-block-type');
            const match = itemBlockId === this.activeBlockId ||
                itemBlockType === blockIdOrType ||
                (found && itemBlockId === found.id);
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
        const index = this.findBlockIndex(blockId);
        if (index === -1) return null;

        const source = this.layout[index];
        const reg = this.registry[source.type];

        // Validasi: Blok wajib sistem atau non-repeatable tidak boleh diduplikasi
        if (reg && (reg.repeatable === false || reg.required)) {
            if (typeof window.__wpShowToast === 'function') {
                window.__wpShowToast('Aksi Dibatasi', `Blok "${source.title}" bersifat unik atau wajib dan tidak dapat diduplikasi.`, 'warning');
            } else {
                alert(`Blok "${source.title}" bersifat unik atau wajib dan tidak dapat diduplikasi.`);
            }
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

        if (typeof window.__wpShowToast === 'function') {
            window.__wpShowToast('Blok Diduplikasi', `Blok "${clone.title}" berhasil ditambahkan.`, 'success');
        }

        return clone;
    }

    /**
     * Hapus blok dengan proteksi System Locked dan User Locked
     */
    deleteBlock(blockId) {
        const index = this.findBlockIndex(blockId);
        if (index === -1) return false;

        const block = this.layout[index];
        const reg = this.registry[block.type];

        // 1. Proteksi System Locked & Core Sections (Hero, Footer, Kuota, Tahapan, FAQ)
        const coreBlocks = ['hero', 'footer', 'kuota', 'tahapan', 'faq'];
        if ((reg && reg.required) || block.locked || coreBlocks.includes(block.type)) {
            if (typeof window.__wpShowToast === 'function') {
                window.__wpShowToast('Bagian Utama Halaman', `Blok "${block.title}" adalah bagian utama halaman (Core Section) dan tidak dapat dihapus. Anda dapat menggunakan opsi sembunyikan (ikon mata) jika tidak ingin menampilkannya.`, 'warning');
            } else {
                alert(`Blok "${block.title}" adalah bagian utama halaman (Core Section) dan tidak dapat dihapus. Anda dapat menggunakan opsi sembunyikan (ikon mata) jika tidak ingin menampilkannya.`);
            }
            return false;
        }

        // 2. Proteksi User Locked
        if (block.user_locked) {
            if (typeof window.__wpShowToast === 'function') {
                window.__wpShowToast('Aksi Dibatasi', `Blok "${block.title}" sedang dikunci (User Locked). Buka kunci terlebih dahulu untuk menghapus.`, 'warning');
            } else {
                alert(`Blok "${block.title}" sedang dikunci (User Locked). Buka kunci terlebih dahulu untuk menghapus.`);
            }
            return false;
        }

        if (!confirm(`Hapus blok "${block.title}" dari Landing Page?`)) {
            return false;
        }

        const removedTitle = block.title;
        this.layout.splice(index, 1);
        this.recalculateOrders();
        this.renderListView();
        this.notifyChange();

        if (this.bridge) {
            this.bridge.reorderBlocks(this.layout);
        }

        if (typeof window.__wpShowToast === 'function') {
            window.__wpShowToast('Blok Dihapus', `Blok "${removedTitle}" berhasil dihapus dari halaman.`, 'info');
        }

        return true;
    }

    /**
     * Pindah posisi blok (Move Up / Move Down)
     */
    moveBlock(blockId, direction) {
        const index = this.findBlockIndex(blockId);
        if (index === -1) return false;

        const block = this.layout[index];
        if (block.user_locked) {
            if (typeof window.__wpShowToast === 'function') {
                window.__wpShowToast('Aksi Dibatasi', `Blok "${block.title}" sedang dikunci dan tidak dapat dipindahkan posisinya.`, 'warning');
            } else {
                alert(`Blok "${block.title}" sedang dikunci dan tidak dapat dipindahkan posisinya.`);
            }
            return false;
        }

        const targetIndex = direction === 'up' ? index - 1 : index + 1;
        if (targetIndex < 0 || targetIndex >= this.layout.length) {
            return false;
        }

        const targetBlock = this.layout[targetIndex];
        if (targetBlock.user_locked) {
            if (typeof window.__wpShowToast === 'function') {
                window.__wpShowToast('Aksi Dibatasi', `Blok sasaran "${targetBlock.title}" sedang dikunci dan posisinya tidak dapat digeser.`, 'warning');
            } else {
                alert(`Blok sasaran "${targetBlock.title}" sedang dikunci dan posisinya tidak dapat digeser.`);
            }
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

        if (typeof window.__wpShowToast === 'function') {
            window.__wpShowToast('Posisi Blok Diperbarui', `Blok "${block.title}" berhasil dipindahkan ke ${direction === 'up' ? 'atas' : 'bawah'}.`, 'info');
        }

        return true;
    }

    /**
     * Ubah status terlihat / tersembunyi (Toggle Visibility)
     */
    toggleVisibility(blockId) {
        const block = this.findBlock(blockId);
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
        const block = this.findBlock(blockId);
        if (!block) return;

        const reg = this.registry[block.type];
        if ((reg && reg.required) || block.locked) {
            if (typeof window.__wpShowToast === 'function') {
                window.__wpShowToast('Aksi Dibatasi', `Blok "${block.title}" adalah blok sistem wajib dan status pengunciannya tidak dapat diubah.`, 'warning');
            } else {
                alert(`Blok "${block.title}" adalah blok sistem wajib dan status pengunciannya tidak dapat diubah.`);
            }
            return;
        }

        block.user_locked = !Boolean(block.user_locked);
        this.renderListView();
        this.notifyChange();

        if (typeof window.__wpShowToast === 'function') {
            window.__wpShowToast('Status Kunci Diperbarui', `Blok "${block.title}" ${block.user_locked ? 'berhasil dikunci' : 'berhasil dibuka kuncinya'}.`, 'info');
        }
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

    getBlockSvg(type, size = 14) {
        switch (type) {
            case 'hero':
                return `<svg width="${size}" height="${size}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>`;
            case 'informasi':
                return `<svg width="${size}" height="${size}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>`;
            case 'kuota':
                return `<svg width="${size}" height="${size}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>`;
            case 'tahapan':
                return `<svg width="${size}" height="${size}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="4.5" r="2.5"/><circle cx="6" cy="19.5" r="2.5"/><circle cx="18" cy="19.5" r="2.5"/><path d="M12 7v5l-4.5 5"/><path d="M12 12l4.5 5"/></svg>`;
            case 'faq':
                return `<svg width="${size}" height="${size}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>`;
            case 'footer':
                return `<svg width="${size}" height="${size}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 21h18"/><path d="M5 21V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16"/><line x1="9" y1="7" x2="9" y2="7.01"/><line x1="15" y1="7" x2="15" y2="7.01"/><line x1="9" y1="11" x2="9" y2="11.01"/><line x1="15" y1="11" x2="15" y2="11.01"/><line x1="9" y1="15" x2="9" y2="15.01"/><line x1="15" y1="15" x2="15" y2="15.01"/></svg>`;
            case 'kebijakan':
                return `<svg width="${size}" height="${size}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg>`;
            case 'cta_banner':
                return `<svg width="${size}" height="${size}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m3 11 18-5v12L3 14v-3z"/><path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"/></svg>`;
            case 'features':
                return `<svg width="${size}" height="${size}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>`;
            case 'spacer':
                return `<svg width="${size}" height="${size}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="7 8 12 3 17 8"/><polyline points="7 16 12 21 17 16"/><line x1="12" y1="3" x2="12" y2="21"/></svg>`;
            default:
                return `<svg width="${size}" height="${size}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>`;
        }
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
            const iconSvg = this.getBlockSvg(block.type, 14);

            const li = document.createElement('li');
            li.className = `wp-listview-item ${block.visible ? '' : 'is-hidden'} ${block.id === this.activeBlockId ? 'is-selected' : ''}`;
            li.setAttribute('data-block-id', block.id);
            li.setAttribute('data-block-type', block.type);
            li.setAttribute('data-order', block.order);
            li.setAttribute('draggable', !isUserLocked ? 'true' : 'false');
            li.setAttribute('role', 'treeitem');

            let lockBadgeHtml = '';
            if (isRequired) {
                lockBadgeHtml = `<span class="wp-listview-badge wp-badge-system-locked" title="Terkunci oleh sistem (Wajib)"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg> Sistem</span>`;
            } else if (isUserLocked) {
                lockBadgeHtml = `<span class="wp-listview-badge wp-badge-user-locked" title="Terkunci oleh Super Admin"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg> Terkunci</span>`;
            }

            const eyeSvg = block.visible
                ? `<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>`
                : `<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" y1="2" x2="22" y2="22"/></svg>`;

            const lockBtnSvg = isLocked
                ? `<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>`
                : `<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 9.9-1"/></svg>`;

            li.innerHTML = `
                <span class="wp-drag-handle ${isUserLocked ? 'is-locked' : ''}" title="${isUserLocked ? 'Terkunci - buka kunci untuk memindahkan' : 'Tarik untuk mengatur urutan'}">⠿</span>
                <span class="wp-listview-icon">${iconSvg}</span>
                <div class="wp-listview-title-wrap">
                    <span class="wp-listview-title">${block.title}</span>
                    ${lockBadgeHtml}
                </div>
                <div class="wp-listview-actions">
                    <button type="button" class="wp-listview-btn btn-toggle-visibility" title="${block.visible ? 'Sembunyikan' : 'Tampilkan'}">
                        <span class="eye-icon">${eyeSvg}</span>
                    </button>
                    <button type="button" class="wp-listview-btn btn-toggle-lock" title="${isRequired ? 'Blok sistem wajib tidak dapat diubah status kuncinya' : (isUserLocked ? 'Buka Kunci Blok' : 'Kunci Blok')}" ${isRequired ? 'disabled' : ''}>
                        <span class="lock-icon">${lockBtnSvg}</span>
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
        const fromIdx = this.findBlockIndex(draggedId);
        const toIdx = this.findBlockIndex(targetId);

        if (fromIdx === -1 || toIdx === -1 || fromIdx === toIdx) return;

        const draggedBlock = this.layout[fromIdx];
        if (draggedBlock.user_locked) {
            if (typeof window.__wpShowToast === 'function') {
                window.__wpShowToast('Aksi Dibatasi', `Blok "${draggedBlock.title}" sedang dikunci dan posisinya tidak dapat dipindahkan.`, 'warning');
            } else {
                alert(`Blok "${draggedBlock.title}" sedang dikunci dan posisinya tidak dapat dipindahkan.`);
            }
            return;
        }

        const targetBlock = this.layout[toIdx];
        if (targetBlock.user_locked) {
            if (typeof window.__wpShowToast === 'function') {
                window.__wpShowToast('Aksi Dibatasi', `Blok target "${targetBlock.title}" sedang dikunci.`, 'warning');
            } else {
                alert(`Blok target "${targetBlock.title}" sedang dikunci.`);
            }
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

        if (typeof window.__wpShowToast === 'function') {
            window.__wpShowToast('Urutan Blok Diperbarui', `Blok "${moved.title}" berhasil dipindahkan.`, 'info');
        }
    }
}
