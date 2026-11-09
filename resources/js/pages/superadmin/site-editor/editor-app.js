/**
 * WordPress.org Gutenberg Site Editor - Main Application Orchestrator
 * Integrasi Lengkap: Canvas Bridge, Block Manager, Inspector, Media Library, Editor State, dan Revision Manager
 */

import { CanvasBridge } from './canvas-bridge.js';
import { BlockManager } from './block-manager.js';
import { Inspector } from './inspector.js';
import { MediaLibrary } from './media-library.js';
import { RevisionManager } from './revision-manager.js';
import { EditorState } from './editor-state.js';

document.addEventListener('DOMContentLoaded', () => {
    initEditorApp();
});

function initEditorApp() {
    const iframeEl = document.getElementById('wp-preview-iframe');
    const toastEl = document.getElementById('wp-toast');
    const frameContainer = document.getElementById('wp-canvas-frame-container');
    const floatingToolbar = document.getElementById('wp-floating-toolbar');
    const toolbarBlockName = document.getElementById('wp-toolbar-block-name');
    const inserterDrawer = document.getElementById('wp-inserter-drawer');

    // Baca data awal dari container tersembunyi
    const initialDataEl = document.getElementById('wp-editor-initial-data');
    let initialLayout = [];
    let initialDocument = {};
    let initialRegistry = {};
    let initialPatterns = {};
    let initialCategories = [];
    let initialReusableBlocks = [];
    let initialRevisions = [];
    let initialMedia = [];
    let initialResponsiveStyles = {};
    let initialVersion = 1;

    if (initialDataEl) {
        try {
            initialLayout = JSON.parse(initialDataEl.getAttribute('data-layout') || '[]');
            initialDocument = JSON.parse(initialDataEl.getAttribute('data-document') || '{}');
            initialRegistry = JSON.parse(initialDataEl.getAttribute('data-registry') || '{}');
            initialPatterns = JSON.parse(initialDataEl.getAttribute('data-patterns') || '{}');
            initialCategories = JSON.parse(initialDataEl.getAttribute('data-categories') || '[]');
            initialReusableBlocks = JSON.parse(initialDataEl.getAttribute('data-reusable-blocks') || '[]');
            initialRevisions = JSON.parse(initialDataEl.getAttribute('data-revisions') || '[]');
            initialMedia = JSON.parse(initialDataEl.getAttribute('data-media') || '[]');
            initialResponsiveStyles = JSON.parse(initialDataEl.getAttribute('data-responsive-styles') || '{}');
            initialVersion = parseInt(initialDataEl.getAttribute('data-version') || '1', 10);
        } catch (e) {
            console.error('Error parsing initial editor data:', e);
        }
    }

    const initialGlobalStyles = initialDocument.global_styles || initialRegistry.global_styles || {};
    const presets = initialRegistry.presets || {};

    let activeSelectedBlock = 'hero';
    let activeSelectedBlockId = 'block-hero-1';

    // 1. INSIALISASI CANVAS BRIDGE
    const bridge = new CanvasBridge(iframeEl, {
        onReady: () => {
            console.log('[WP Site Editor] Kanvas iframe terhubung dan siap.');
            if (blockManager) {
                bridge.reorderBlocks(blockManager.getLayout());
            }
        },
        onBlockSelected: (blockName, blockTitle, rect, blockId) => {
            activeSelectedBlock = blockName;
            activeSelectedBlockId = blockId || `block-${blockName}`;

            if (editorState) {
                editorState.selectedBlockId = activeSelectedBlockId;
            }

            blockManager.setActiveBlock(activeSelectedBlockId);
            inspector.setActiveBlock(blockName, blockTitle, activeSelectedBlockId);

            // Posisikan Floating Toolbar tepat di atas kanvas
            if (floatingToolbar && toolbarBlockName) {
                toolbarBlockName.textContent = `Blok: ${blockTitle}`;
                floatingToolbar.style.display = 'flex';

                // Update tombol Reusable pada toolbar
                const isReusable = blockManager.isReusable(activeSelectedBlockId);
                const btnMakeReusable = document.getElementById('btn-toolbar-make-reusable');
                const btnDetachReusable = document.getElementById('btn-toolbar-detach-reusable');
                if (btnMakeReusable) btnMakeReusable.style.display = isReusable ? 'none' : 'inline-flex';
                if (btnDetachReusable) btnDetachReusable.style.display = isReusable ? 'inline-flex' : 'none';
            }
        },
        onInlineTextChanged: (fieldName, value, blockId) => {
            inspector.updateFieldFromInline(fieldName, value);
            editorState.mutate('inline_text_edit', (doc) => {
                doc.content = inspector.getSerializedState();
                doc.blocks = blockManager.getLayout();
            }, { target: fieldName, recordHistory: true });
        },
        onInsertBlockRequest: (afterBlockId) => {
            openInserter('blocks');
        },
    });

    // 2. INSIALISASI BLOCK MANAGER
    const blockManager = new BlockManager(initialLayout, initialRegistry, bridge, (newLayout) => {
        editorState.mutate('block_structure_change', (doc) => {
            doc.blocks = newLayout;
            doc.content = inspector.getSerializedState();
        });
    });

    // 3. INSIALISASI INSPECTOR
    const inspector = new Inspector(bridge, initialResponsiveStyles, initialGlobalStyles, presets, () => {
        editorState.mutate('inspector_field_change', (doc) => {
            doc.content = inspector.getSerializedState();
            doc.global_styles = inspector.globalStyles;
            doc.responsive_styles = inspector.responsiveStyles;
            doc.blocks = blockManager.getLayout();
        }, { groupWithPrevious: true });
    });
    inspector.setBlockManager(blockManager);

    // 4. INSIALISASI CENTRAL EDITOR STATE (TASK 5)
    const initialFullDoc = {
        version: initialVersion,
        blocks: blockManager.getLayout(),
        global_styles: inspector.globalStyles,
        responsive_styles: inspector.responsiveStyles,
        content: inspector.getSerializedState(),
    };

    const editorState = new EditorState(initialFullDoc, {
        documentVersion: initialVersion,
        initialUpdatedAt: initialDataEl?.getAttribute('data-updated-at'),
        initialDirty: initialDataEl?.getAttribute('data-save-status') === 'draft',
        selectedBlockId: activeSelectedBlockId,
        onNotifyToast: (msg, isSuccess) => showToast(msg, isSuccess),
        onDeleteSelectedBlock: (blockId) => {
            if (blockManager) {
                blockManager.deleteBlock(blockId);
            }
        },
        onDeselectBlock: () => {
            if (floatingToolbar) floatingToolbar.style.display = 'none';
            if (blockManager) blockManager.setActiveBlock(null);
        },
        onHistoryApply: (docSnapshot, type) => {
            // Terapkan snapshot ke Kanvas, BlockManager, dan Inspector
            if (docSnapshot.blocks) {
                blockManager.setLayout(docSnapshot.blocks);
                bridge.reorderBlocks(docSnapshot.blocks);
            }
            if (docSnapshot.global_styles) {
                inspector.globalStyles = docSnapshot.global_styles;
                inspector.applyGlobalStylesToBridge(docSnapshot.global_styles);
            }
            if (docSnapshot.responsive_styles) {
                inspector.responsiveStyles = docSnapshot.responsive_styles;
            }
            if (docSnapshot.content) {
                inspector.applySnapshot(docSnapshot.content);
            }
        },
    });

    // 5. INSIALISASI MEDIA LIBRARY
    const mediaLibrary = new MediaLibrary(initialMedia, (selectedMedia) => {
        inspector.setHeroImageFromMedia(selectedMedia);
        showToast(`Media "${selectedMedia.original_name}" berhasil diterapkan.`, true);
        editorState.mutate('apply_media', (doc) => {
            doc.content = inspector.getSerializedState();
            doc.blocks = blockManager.getLayout();
        });
    }, (msg, isSuccess) => {
        showToast(msg, isSuccess);
    });

    inspector.setMediaLibrary(mediaLibrary);

    // 6. INSIALISASI REVISION & ROLLBACK MANAGER (TASK 6)
    const revisionManager = new RevisionManager(initialRevisions, {
        onNotifyToast: (msg, isSuccess) => {
            showToast(msg, isSuccess);
        },
    });

    // Pasang tombol topbar Undo & Redo ke EditorState
    document.getElementById('btn-undo')?.addEventListener('click', () => {
        editorState.undo();
    });
    document.getElementById('btn-redo')?.addEventListener('click', () => {
        editorState.redo();
    });

    // 7. VIEWPORT SWITCHER (DESKTOP / TABLET / MOBILE)
    const viewportButtons = document.querySelectorAll('.wp-viewport-btn');
    viewportButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            viewportButtons.forEach(b => b.classList.remove('is-active'));
            btn.classList.add('is-active');

            const device = btn.getAttribute('data-device');
            editorState.currentViewport = device;
            frameContainer.className = `wp-canvas-frame-container device-${device}`;
        });
    });

    // Multi-Page Canvas Selector (Landing Page / Peta Alur / Kebijakan Privasi)
    const pageSelector = document.getElementById('select-canvas-page');
    if (pageSelector && iframeEl) {
        pageSelector.addEventListener('change', (e) => {
            const selectedPage = e.target.value;
            const currentSrc = new URL(iframeEl.src, window.location.origin);

            if (selectedPage === 'landing') {
                currentSrc.searchParams.delete('page');
            } else {
                currentSrc.searchParams.set('page', selectedPage);
            }
            currentSrc.searchParams.set('editor_mode', '1');
            iframeEl.src = currentSrc.toString();

            // Buka section terkait di Inspector
            if (selectedPage === 'peta-alur') {
                inspector.setActiveBlock('tahapan', 'Peta Alur & Tahapan', 'block-tahapan-1');
                showToast('Beralih ke Kanvas Peta Alur Interaktif.', true);
            } else if (selectedPage === 'kebijakan-privasi') {
                inspector.setActiveBlock('kebijakan', 'Kebijakan Privasi', 'block-kebijakan-1');
                showToast('Beralih ke Kanvas Halaman Kebijakan Privasi.', true);
            } else {
                inspector.setActiveBlock('hero', 'Hero Banner', 'block-hero-1');
                showToast('Beralih ke Kanvas Beranda Utama.', true);
            }
        });
    }

    // 8. INSERTER & LIST VIEW DRAWER NAVIGATION
    const toggleInserterBtn = document.getElementById('btn-toggle-inserter');
    const closeInserterBtn = document.getElementById('btn-close-inserter');
    const listViewDrawer = document.getElementById('wp-list-view-drawer');
    const listViewBtn = document.getElementById('btn-list-view');
    const closeListViewBtn = document.getElementById('btn-close-list-view');
    const listViewAddBlockBtn = document.getElementById('btn-listview-add-block');

    function openInserter(tabKey = 'blocks') {
        closeListView();
        if (!inserterDrawer) return;
        inserterDrawer.style.display = 'flex';
        toggleInserterBtn?.classList.add('is-active');

        // Beralih ke tab drawer yang diminta
        document.querySelectorAll('.wp-nav-tab').forEach(t => {
            t.classList.toggle('is-active', t.getAttribute('data-drawer-tab') === tabKey);
        });
        document.querySelectorAll('.wp-drawer-pane').forEach(p => {
            p.style.display = p.id === `wp-drawer-pane-${tabKey}` ? 'flex' : 'none';
        });
    }

    function closeInserter() {
        if (inserterDrawer) inserterDrawer.style.display = 'none';
        toggleInserterBtn?.classList.remove('is-active');
    }

    function openListView() {
        closeInserter();
        if (!listViewDrawer) return;
        listViewDrawer.style.display = 'flex';
        listViewBtn?.classList.add('is-active');
    }

    function closeListView() {
        if (listViewDrawer) listViewDrawer.style.display = 'none';
        listViewBtn?.classList.remove('is-active');
    }

    function toggleListView() {
        if (listViewDrawer && listViewDrawer.style.display !== 'none') {
            closeListView();
        } else {
            openListView();
        }
    }

    toggleInserterBtn?.addEventListener('click', () => {
        if (inserterDrawer && inserterDrawer.style.display !== 'none') {
            closeInserter();
        } else {
            openInserter('blocks');
        }
    });

    closeInserterBtn?.addEventListener('click', closeInserter);

    listViewBtn?.addEventListener('click', toggleListView);
    closeListViewBtn?.addEventListener('click', closeListView);
    listViewAddBlockBtn?.addEventListener('click', () => {
        closeListView();
        openInserter('blocks');
    });

    // Navigasi tab drawer
    document.querySelectorAll('.wp-nav-tab').forEach(tab => {
        tab.addEventListener('click', () => {
            document.querySelectorAll('.wp-nav-tab').forEach(t => t.classList.remove('is-active'));
            tab.classList.add('is-active');

            const target = tab.getAttribute('data-drawer-tab');
            document.querySelectorAll('.wp-drawer-pane').forEach(p => {
                p.style.display = p.id === `wp-drawer-pane-${target}` ? 'flex' : 'none';
            });
        });
    });

    // Inserter Search Box Filtering
    const searchInput = document.getElementById('wp-inserter-search');
    searchInput?.addEventListener('input', (e) => {
        const query = e.target.value.toLowerCase().trim();
        const blockCards = document.querySelectorAll('.wp-block-card');
        blockCards.forEach(card => {
            const title = card.querySelector('span')?.textContent.toLowerCase() || '';
            const type = card.getAttribute('data-block-type') || '';
            card.style.display = (title.includes(query) || type.includes(query)) ? 'flex' : 'none';
        });
    });

    // Inserter Block Click -> Tambah blok ke halaman
    document.querySelectorAll('.wp-block-card[data-block-type]').forEach(card => {
        card.addEventListener('click', () => {
            const blockType = card.getAttribute('data-block-type');
            if (blockType) {
                const added = blockManager.addBlock(blockType);
                if (added) {
                    showToast(`Blok "${added.title}" ditambahkan ke halaman.`, true);
                    closeInserter();
                }
            }
        });
    });

    // Inserter Action Buttons (Image / PDF Picker)
    document.querySelector('.wp-block-card[data-action="open-media"]')?.addEventListener('click', () => {
        mediaLibrary.open('image', (selectedMedia) => {
            inspector.setHeroImageFromMedia(selectedMedia);
            showToast(`Media "${selectedMedia.original_name}" berhasil diterapkan.`, true);
            editorState.mutate('apply_media', (doc) => {
                doc.content = inspector.getSerializedState();
                doc.blocks = blockManager.getLayout();
            });
        });
    });
    document.querySelector('.wp-block-card[data-action="open-media-pdf"]')?.addEventListener('click', () => {
        mediaLibrary.open('pdf', (selectedPdf) => {
            inspector.setPanduanPdfFromMedia(selectedPdf);
            showToast(`Buku panduan PDF disetel ke "${selectedPdf.original_name}".`, true);
            editorState.mutate('apply_media_pdf', (doc) => {
                doc.content = inspector.getSerializedState();
                doc.blocks = blockManager.getLayout();
            });
        });
    });

    // Quick Media Drawer Button
    document.getElementById('btn-open-full-media-modal')?.addEventListener('click', () => {
        mediaLibrary.open();
    });
    document.getElementById('btn-quick-upload')?.addEventListener('click', () => {
        mediaLibrary.open();
        document.querySelector('.wp-modal-tab[data-media-tab="upload"]')?.click();
    });

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    // =========================================================================
    // A. PATTERN LIBRARY INTERACTIONS (TASK 7)
    // =========================================================================

    // Filter Kategori Pola
    const catFilterBtns = document.querySelectorAll('.wp-cat-filter-btn');
    catFilterBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            catFilterBtns.forEach(b => b.classList.remove('is-active'));
            btn.classList.add('is-active');

            const filterCat = btn.getAttribute('data-pattern-filter');
            const patternCards = document.querySelectorAll('.wp-pattern-card');
            patternCards.forEach(card => {
                const cardCat = card.getAttribute('data-category');
                if (filterCat === 'all' || cardCat === filterCat) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });

    // Pratinjau Pola (Modal Preview)
    const patternPreviewModal = document.getElementById('wp-pattern-preview-modal');
    const closePatternPreviewBtn = document.getElementById('btn-close-pattern-preview');
    const cancelPatternPreviewBtn = document.getElementById('btn-cancel-pattern-preview');
    const confirmInsertPatternBtn = document.getElementById('btn-confirm-insert-pattern');

    function closePatternPreview() {
        if (patternPreviewModal) patternPreviewModal.style.display = 'none';
    }

    closePatternPreviewBtn?.addEventListener('click', closePatternPreview);
    cancelPatternPreviewBtn?.addEventListener('click', closePatternPreview);

    document.querySelectorAll('.btn-preview-pattern').forEach(btn => {
        btn.addEventListener('click', () => {
            const patKey = btn.getAttribute('data-pattern-key');
            fetch(`/superadmin/site-editor/patterns/${patKey}/preview`, {
                headers: { 'Accept': 'application/json' }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success && data.pattern) {
                    const pat = data.pattern;
                    document.getElementById('pattern-preview-name').textContent = pat.name;
                    document.getElementById('pattern-preview-desc').textContent = pat.description || '';
                    document.getElementById('pattern-preview-badge').textContent = pat.category;

                    const blocksListEl = document.getElementById('pattern-preview-blocks-list');
                    if (blocksListEl) {
                        blocksListEl.innerHTML = (pat.blocks || []).map(b => `
                            <div class="wp-pattern-preview-block-item">
                                <span class="wp-badge-block-type">${(b.type || '').toUpperCase()}</span>
                                <span class="wp-preview-block-title">${b.title || b.type}</span>
                            </div>
                        `).join('');
                    }

                    if (confirmInsertPatternBtn) {
                        confirmInsertPatternBtn.setAttribute('data-pattern-key', pat.key || patKey);
                    }

                    if (patternPreviewModal) patternPreviewModal.style.display = 'flex';
                }
            })
            .catch(err => {
                console.error(err);
                showToast('Gagal memuat pratinjau pola.', false);
            });
        });
    });

    // Sisipkan Pola dari Modal Pratinjau
    confirmInsertPatternBtn?.addEventListener('click', () => {
        const patKey = confirmInsertPatternBtn.getAttribute('data-pattern-key');
        if (patKey) {
            executeInsertPattern(patKey);
            closePatternPreview();
        }
    });

    // Sisipkan Pola langsung dari Kartu Inserter
    document.querySelectorAll('.btn-insert-pattern').forEach(btn => {
        btn.addEventListener('click', () => {
            const patKey = btn.getAttribute('data-pattern-key');
            if (patKey) {
                executeInsertPattern(patKey);
            }
        });
    });

    function executeInsertPattern(patKey) {
        showToast('Menyisipkan pola ke halaman...', true);
        fetch('/superadmin/site-editor/patterns/insert', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ key: patKey })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success && data.document) {
                const updatedBlocks = data.document.blocks || [];
                blockManager.setLayout(updatedBlocks);
                bridge.reorderBlocks(updatedBlocks);
                editorState.mutate('insert_pattern', (doc) => {
                    doc.blocks = updatedBlocks;
                });
                showToast(data.message || 'Pola berhasil disisipkan.', true);
                closeInserter();
            } else {
                showToast(data.message || 'Gagal menyisipkan pola.', false);
            }
        })
        .catch(err => {
            console.error(err);
            showToast('Terjadi kesalahan jaringan saat menyisipkan pola.', false);
        });
    }

    // Hapus Pola Kustom
    document.querySelectorAll('.btn-delete-custom-pattern').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            const patId = btn.getAttribute('data-pattern-id');
            if (!confirm('Apakah Anda yakin ingin menghapus pola kustom ini?')) return;

            fetch(`/superadmin/site-editor/patterns/custom/${patId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    btn.closest('.wp-pattern-card')?.remove();
                    showToast(data.message || 'Pola kustom berhasil dihapus.', true);
                } else {
                    showToast(data.message || 'Gagal menghapus pola kustom.', false);
                }
            })
            .catch(err => {
                console.error(err);
                showToast('Gagal menghapus pola kustom.', false);
            });
        });
    });

    // =========================================================================
    // B. REUSABLE BLOCKS ENGINE (TASK 7)
    // =========================================================================

    // Sisipkan Reusable Block ke Halaman
    document.querySelectorAll('.btn-insert-reusable').forEach(btn => {
        btn.addEventListener('click', () => {
            const reusableId = btn.getAttribute('data-reusable-id');
            if (!reusableId) return;

            showToast('Menyisipkan Reusable Block...', true);
            fetch('/superadmin/site-editor/reusable-blocks/insert', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ reusable_id: reusableId })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success && data.document) {
                    const updatedBlocks = data.document.blocks || [];
                    blockManager.setLayout(updatedBlocks);
                    bridge.reorderBlocks(updatedBlocks);
                    editorState.mutate('insert_reusable_block', (doc) => {
                        doc.blocks = updatedBlocks;
                    });
                    showToast(data.message || 'Reusable Block berhasil disisipkan.', true);
                    closeInserter();
                } else {
                    showToast(data.message || 'Gagal menyisipkan Reusable Block.', false);
                }
            })
            .catch(err => {
                console.error(err);
                showToast('Terjadi kesalahan jaringan.', false);
            });
        });
    });

    // Buat Reusable Block dari Blok Terpilih (Modal)
    const createReusableModal = document.getElementById('wp-create-reusable-modal');
    const closeCreateReusableBtn = document.getElementById('btn-close-create-reusable');
    const cancelCreateReusableBtn = document.getElementById('btn-cancel-create-reusable');
    const confirmCreateReusableBtn = document.getElementById('btn-confirm-create-reusable');

    function closeCreateReusableModal() {
        if (createReusableModal) createReusableModal.style.display = 'none';
    }

    closeCreateReusableBtn?.addEventListener('click', closeCreateReusableModal);
    cancelCreateReusableBtn?.addEventListener('click', closeCreateReusableModal);

    document.getElementById('btn-toolbar-make-reusable')?.addEventListener('click', () => {
        const activeBlock = blockManager.getActiveBlock();
        if (!activeBlock) {
            showToast('Pilih salah satu blok terlebih dahulu.', false);
            return;
        }

        const typeEl = document.getElementById('reusable-source-block-type');
        if (typeEl) typeEl.textContent = (activeBlock.title || activeBlock.type).toUpperCase();

        const titleInput = document.getElementById('reusable-title-input');
        if (titleInput) titleInput.value = `${activeBlock.title || activeBlock.type} (Bersama)`;

        if (createReusableModal) createReusableModal.style.display = 'flex';
    });

    confirmCreateReusableBtn?.addEventListener('click', () => {
        const activeBlock = blockManager.getActiveBlock();
        const titleInput = document.getElementById('reusable-title-input');
        const catInput = document.getElementById('reusable-category-input');

        const title = titleInput?.value.trim();
        if (!title) {
            alert('Silakan masukkan nama Reusable Block.');
            return;
        }

        confirmCreateReusableBtn.setAttribute('disabled', 'disabled');

        fetch('/superadmin/site-editor/reusable-blocks', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                title: title,
                category: catInput?.value.trim() || 'Umum',
                block_type: activeBlock.type,
                content: activeBlock.content || {},
                styles: activeBlock.styles || {}
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success && data.reusable_block) {
                const newRb = data.reusable_block;
                blockManager.markAsReusable(activeSelectedBlockId, newRb.id, newRb.title);

                // Update Toolbar
                const makeBtn = document.getElementById('btn-toolbar-make-reusable');
                const detachBtn = document.getElementById('btn-toolbar-detach-reusable');
                if (makeBtn) makeBtn.style.display = 'none';
                if (detachBtn) detachBtn.style.display = 'inline-flex';

                closeCreateReusableModal();
                showToast(`Reusable Block "${newRb.title}" berhasil dibuat!`, true);
            } else {
                showToast(data.message || 'Gagal membuat Reusable Block.', false);
            }
        })
        .catch(err => {
            console.error(err);
            showToast('Terjadi kesalahan jaringan.', false);
        })
        .finally(() => {
            confirmCreateReusableBtn.removeAttribute('disabled');
        });
    });

    // Pisahkan Reusable Block (Detach / Convert to Regular)
    document.getElementById('btn-toolbar-detach-reusable')?.addEventListener('click', () => {
        if (!confirm('Pisahkan blok ini dari Reusable Block bersama? Perubahan selanjutnya pada blok ini tidak akan memengaruhi blok lainnya.')) {
            return;
        }

        fetch(`/superadmin/site-editor/reusable-blocks/${activeSelectedBlockId}/detach`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success && data.document) {
                blockManager.detachReusable(activeSelectedBlockId);
                const updatedBlocks = data.document.blocks || [];
                blockManager.setLayout(updatedBlocks);
                bridge.reorderBlocks(updatedBlocks);

                // Update Toolbar
                const makeBtn = document.getElementById('btn-toolbar-make-reusable');
                const detachBtn = document.getElementById('btn-toolbar-detach-reusable');
                if (makeBtn) makeBtn.style.display = 'inline-flex';
                if (detachBtn) detachBtn.style.display = 'none';

                editorState.mutate('detach_reusable_block', (doc) => {
                    doc.blocks = updatedBlocks;
                });
                showToast('Blok berhasil dipisahkan menjadi blok reguler.', true);
            } else {
                showToast(data.message || 'Gagal memisahkan Reusable Block.', false);
            }
        })
        .catch(err => {
            console.error(err);
            showToast('Gagal memisahkan Reusable Block.', false);
        });
    });

    // Modal Info Penggunaan Reusable Block (Usage Tracking)
    const reusableUsageModal = document.getElementById('wp-reusable-usage-modal');
    const closeReusableUsageBtn = document.getElementById('btn-close-reusable-usage');
    const closeReusableUsageFooterBtn = document.getElementById('btn-close-reusable-usage-footer');

    function closeUsageModal() {
        if (reusableUsageModal) reusableUsageModal.style.display = 'none';
    }

    closeReusableUsageBtn?.addEventListener('click', closeUsageModal);
    closeReusableUsageFooterBtn?.addEventListener('click', closeUsageModal);

    document.querySelectorAll('.btn-view-reusable-usage').forEach(btn => {
        btn.addEventListener('click', () => {
            const reusableId = btn.getAttribute('data-reusable-id');
            const card = btn.closest('.wp-reusable-card');
            const title = card?.querySelector('.wp-reusable-title')?.textContent || 'Reusable Block';

            document.getElementById('reusable-usage-title').textContent = title;
            const itemsContainer = document.getElementById('reusable-usage-items');

            if (itemsContainer) {
                itemsContainer.innerHTML = '<p class="wp-loading-note">Memuat lokasi pemakaian...</p>';
            }

            if (reusableUsageModal) reusableUsageModal.style.display = 'flex';

            fetch('/superadmin/site-editor/reusable-blocks', {
                headers: { 'Accept': 'application/json' }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success && Array.isArray(data.reusable_blocks)) {
                    const found = data.reusable_blocks.find(b => b.id === reusableId);
                    if (found && Array.isArray(found.usages) && found.usages.length > 0) {
                        itemsContainer.innerHTML = found.usages.map(u => `
                            <div class="wp-usage-item">
                                <span class="wp-usage-icon">📍</span>
                                <div>
                                    <strong>${u.location}</strong>
                                    <small>Blok ID: ${u.block_id} · Urutan: ke-${u.order}</small>
                                </div>
                            </div>
                        `).join('');
                    } else {
                        itemsContainer.innerHTML = '<p class="wp-empty-note">Blok ini saat ini belum disisipkan ke halaman draf maupun live.</p>';
                    }
                }
            })
            .catch(err => {
                console.error(err);
                if (itemsContainer) itemsContainer.innerHTML = '<p class="wp-error-note">Gagal memuat informasi penggunaan.</p>';
            });
        });
    });

    // Hapus Reusable Block (Delete Protection)
    document.querySelectorAll('.btn-delete-reusable').forEach(btn => {
        btn.addEventListener('click', () => {
            const reusableId = btn.getAttribute('data-reusable-id');
            if (!confirm('Apakah Anda yakin ingin menghapus Reusable Block ini?')) return;

            fetch(`/superadmin/site-editor/reusable-blocks/${reusableId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    btn.closest('.wp-reusable-card')?.remove();
                    showToast(data.message || 'Reusable Block berhasil dihapus.', true);
                } else {
                    alert(data.message || 'Gagal menghapus Reusable Block karena masih digunakan.');
                    showToast('Gagal menghapus: blok masih digunakan.', false);
                }
            })
            .catch(err => {
                console.error(err);
                showToast('Gagal menghapus Reusable Block.', false);
            });
        });
    });

    // 9. FLOATING TOOLBAR ACTIONS (CANVAS)
    document.getElementById('btn-toolbar-add')?.addEventListener('click', () => {
        openInserter('blocks');
    });
    document.getElementById('btn-toolbar-list')?.addEventListener('click', () => {
        openListView();
    });
    document.getElementById('btn-toolbar-duplicate')?.addEventListener('click', () => {
        blockManager.duplicateBlock(activeSelectedBlockId);
    });
    document.getElementById('btn-toolbar-delete')?.addEventListener('click', () => {
        blockManager.deleteBlock(activeSelectedBlockId);
    });
    document.getElementById('btn-block-up')?.addEventListener('click', () => {
        blockManager.moveBlock(activeSelectedBlockId, 'up');
    });
    document.getElementById('btn-block-down')?.addEventListener('click', () => {
        blockManager.moveBlock(activeSelectedBlockId, 'down');
    });
    document.getElementById('btn-block-reset')?.addEventListener('click', () => {
        if (confirm('Kembalikan gaya blok ini ke bawaan sistem?')) {
            bridge.resetStyles();
            showToast('Gaya blok dikembalikan ke default.', true);
        }
    });

    // 10. INSPECTOR TOGGLE
    const toggleInspectorBtn = document.getElementById('btn-toggle-inspector');
    const closeInspectorBtn = document.getElementById('btn-close-inspector');
    const inspectorEl = document.getElementById('wp-site-inspector');

    function toggleInspector() {
        if (!inspectorEl) return;
        const isHidden = inspectorEl.style.display === 'none';
        inspectorEl.style.display = isHidden ? 'flex' : 'none';
        toggleInspectorBtn?.classList.toggle('is-active', isHidden);
    }

    toggleInspectorBtn?.addEventListener('click', toggleInspector);
    closeInspectorBtn?.addEventListener('click', () => {
        if (inspectorEl) inspectorEl.style.display = 'none';
        toggleInspectorBtn?.classList.remove('is-active');
    });

    // 11. SAVE DRAFT (MANUAL CLICK & SHORTCUT CTRL+S)
    document.getElementById('btn-save-draft')?.addEventListener('click', () => {
        editorState.saveDraft(false);
    });

    // 12. PUBLISH DRAFT
    const publishBtn = document.getElementById('btn-publish');
    publishBtn?.addEventListener('click', () => {
        if (!confirm('Apakah Anda yakin ingin MEMPUBLIKASIKAN seluruh perubahan ini ke publik? Sistem akan otomatis mencadangkan versi sebelumnya untuk opsi Rollback.')) {
            return;
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        publishBtn?.setAttribute('disabled', 'disabled');
        const spinner = publishBtn?.querySelector('.btn-spinner');
        if (spinner) spinner.style.display = 'inline-block';

        fetch('/superadmin/site-editor/publish', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken || '',
                'Accept': 'application/json',
            },
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, true);
                editorState.clearRecoveryStorage();
                setTimeout(() => {
                    window.location.reload();
                }, 600);
            } else {
                showToast('Gagal mempublikasikan: ' + (data.message || 'Error server'), false);
            }
        })
        .catch(err => {
            console.error(err);
            showToast('Terjadi kesalahan jaringan saat mempublikasikan.', false);
        })
        .finally(() => {
            publishBtn?.removeAttribute('disabled');
            const spinner = publishBtn?.querySelector('.btn-spinner');
            if (spinner) spinner.style.display = 'none';
        });
    });

    // 13. TOAST NOTIFIKASI HELPER
    let toastTimeout = null;
    function showToast(message, isSuccess = true) {
        if (!toastEl) return;
        const msgEl = document.getElementById('wp-toast-msg');
        const iconEl = document.getElementById('wp-toast-icon');

        if (msgEl) msgEl.textContent = message;
        if (iconEl) iconEl.textContent = isSuccess ? '✓' : '⚠️';

        toastEl.className = `wp-toast ${isSuccess ? 'toast-success' : 'toast-error'}`;
        toastEl.style.display = 'flex';

        if (toastTimeout) clearTimeout(toastTimeout);
        toastTimeout = setTimeout(() => {
            toastEl.style.display = 'none';
        }, 3000);
    }
}
