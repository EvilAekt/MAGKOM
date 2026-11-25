/**
 * WordPress.org Gutenberg Site Editor - Main Application Orchestrator
 * Integrasi Lengkap: Canvas Bridge, Block Manager, Inspector, Media Library, Editor State, dan Revision Manager
 */

import { CanvasBridge } from './canvas-bridge.js';
import { BlockManager } from './block-manager.js';
import { Inspector } from './inspector.js';
import { MediaLibrary } from './media-library.js';
import { EditorState } from './editor-state.js';

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initEditorApp);
} else {
    initEditorApp();
}

function initEditorApp() {
    const iframeEl = document.getElementById('wp-preview-iframe');
    const toastEl = document.getElementById('wp-toast');
    const frameContainer = document.getElementById('wp-canvas-frame-container');
    const floatingToolbar = document.getElementById('wp-floating-toolbar');
    const toolbarBlockName = document.getElementById('wp-toolbar-block-name');

    // Baca data awal dari container tersembunyi
    const initialDataEl = document.getElementById('wp-editor-initial-data');

    // 0. Cek apakah ada notifikasi sukses dari aksi sebelumnya yang tersimpan di sessionStorage (pasca-reload)
    try {
        const storedFlash = sessionStorage.getItem('magkom_editor_flash');
        if (storedFlash) {
            sessionStorage.removeItem('magkom_editor_flash');
            const flashData = JSON.parse(storedFlash);
            if (flashData && flashData.message) {
                setTimeout(() => {
                    showToast(flashData.message, flashData.type !== 'error');
                }, 400);
            }
        }
    } catch (e) {
        console.warn('Gagal membaca sessionStorage flash:', e);
    }

    let initialLayout = [];
    let initialDocument = {};
    let initialRegistry = {};
    let initialMedia = [];
    let initialFaq = [];
    let initialVersion = 1;

    let editorState = null;

    if (initialDataEl) {
        try {
            initialLayout = JSON.parse(initialDataEl.getAttribute('data-layout') || '[]');
            initialDocument = JSON.parse(initialDataEl.getAttribute('data-document') || '{}');
            initialRegistry = JSON.parse(initialDataEl.getAttribute('data-registry') || '{}');
            initialMedia = JSON.parse(initialDataEl.getAttribute('data-media') || '[]');
            initialFaq = JSON.parse(initialDataEl.getAttribute('data-faq') || '[]');
            initialVersion = parseInt(initialDataEl.getAttribute('data-version') || '1', 10);
        } catch (e) {
            console.error('Error parsing initial editor data:', e);
        }
    }

    const initialGlobalStyles = initialDocument.global_styles || initialRegistry.global_styles || {};
    const presets = initialRegistry.presets || {};

    let activeSelectedBlock = initialLayout && initialLayout.length > 0 ? initialLayout[0].type : 'hero';
    let activeSelectedBlockId = initialLayout && initialLayout.length > 0 ? initialLayout[0].id : 'block-hero-1';

    function updateFloatingToolbarState(selectedBlockId) {
        if (!floatingToolbar || !blockManager) return;
        const caps = blockManager.getBlockCapabilities(selectedBlockId);
        if (!caps.exists) {
            floatingToolbar.style.display = 'none';
            return;
        }

        if (toolbarBlockName) {
            toolbarBlockName.textContent = `Blok: ${caps.title}`;
        }

        const btnUp = document.getElementById('btn-block-up');
        const btnDown = document.getElementById('btn-block-down');
        const btnDuplicate = document.getElementById('btn-toolbar-duplicate');
        const btnDelete = document.getElementById('btn-toolbar-delete');

        if (btnUp) {
            btnUp.disabled = !caps.canMoveUp;
            btnUp.title = caps.canMoveUp ? 'Pindah ke Atas' : 'Sudah di posisi paling atas';
            btnUp.classList.toggle('is-disabled', !caps.canMoveUp);
        }

        if (btnDown) {
            btnDown.disabled = !caps.canMoveDown;
            btnDown.title = caps.canMoveDown ? 'Pindah ke Bawah' : 'Sudah di posisi paling bawah';
            btnDown.classList.toggle('is-disabled', !caps.canMoveDown);
        }

        if (btnDuplicate) {
            btnDuplicate.disabled = !caps.canDuplicate;
            btnDuplicate.title = caps.canDuplicate
                ? 'Duplikasi Blok'
                : (caps.isRequired || caps.reg?.repeatable === false ? 'Blok unik/wajib tidak dapat diduplikasi' : 'Duplikasi Blok');
            btnDuplicate.classList.toggle('is-disabled', !caps.canDuplicate);
        }

        if (btnDelete) {
            btnDelete.disabled = !caps.canDelete;
            btnDelete.title = caps.canDelete
                ? 'Hapus Blok'
                : (caps.isCoreSection || caps.isRequired ? 'Bagian utama (Core Section) tidak dapat dihapus' : 'Blok dikunci');
            btnDelete.classList.toggle('is-disabled', !caps.canDelete);
        }

        floatingToolbar.style.display = 'flex';
    }

    // 1. INSIALISASI CANVAS BRIDGE
    const bridge = new CanvasBridge(iframeEl, {
        onReady: () => {
            console.log('[WP Site Editor] Kanvas iframe terhubung dan siap.');
            if (blockManager) {
                bridge.reorderBlocks(blockManager.getLayout());
                updateFloatingToolbarState(activeSelectedBlockId);
            }
        },
        onBlockSelected: (blockName, blockTitle, rect, blockId) => {
            activeSelectedBlock = blockName;
            const resolvedBlock = blockManager.findBlock(blockId || blockName);
            activeSelectedBlockId = resolvedBlock ? resolvedBlock.id : (blockId || `block-${blockName}`);

            if (editorState) {
                editorState.selectedBlockId = activeSelectedBlockId;
            }

            blockManager.setActiveBlock(activeSelectedBlockId);
            inspector.setActiveBlock(blockName, blockTitle, activeSelectedBlockId);

            // Buka panel inspector kanan otomatis jika sedang tertutup (SS 3 -> SS 4)
            const inspectorEl = document.getElementById('wp-site-inspector');
            const toggleInspectorBtn = document.getElementById('btn-toggle-inspector');
            if (inspectorEl && inspectorEl.style.display === 'none') {
                inspectorEl.style.display = 'flex';
                toggleInspectorBtn?.classList.add('is-active');
            }

            // Posisikan Floating Toolbar tepat di atas kanvas & perbarui state tombol
            if (floatingToolbar && toolbarBlockName) {
                if (blockName === 'kebijakan') {
                    floatingToolbar.style.display = 'none';
                } else {
                    toolbarBlockName.textContent = `Blok: ${blockTitle}`;
                    floatingToolbar.style.display = 'flex';
                    updateFloatingToolbarState(activeSelectedBlockId);
                }
            } else {
                updateFloatingToolbarState(activeSelectedBlockId);
            }
        },
        onInlineTextChanged: (fieldName, value, blockId) => {
            inspector.updateFieldFromInline(fieldName, value);
            if (!editorState) return;
            editorState.mutate('inline_text_edit', (doc) => {
                doc.content = inspector.getSerializedState();
                doc.blocks = blockManager.getLayout();
            }, { target: fieldName, recordHistory: true });
        },
        onHeroSlideActivated: (slideIndex) => {
            if (inspector && typeof inspector.highlightSlideCard === 'function') {
                inspector.highlightSlideCard(slideIndex);
            }
        },
    });

    // 2. INSIALISASI BLOCK MANAGER
    const blockManager = new BlockManager(initialLayout, initialRegistry, bridge, (newLayout) => {
        if (!editorState) return;
        editorState.mutate('block_structure_change', (doc) => {
            doc.blocks = newLayout;
            doc.content = inspector.getSerializedState();
        });
    });

    // 3. INSIALISASI INSPECTOR
    const inspector = new Inspector(bridge, initialGlobalStyles, presets, () => {
        if (!editorState) return;
        editorState.mutate('inspector_field_change', (doc) => {
            doc.content = inspector.getSerializedState();
            doc.global_styles = inspector.globalStyles;
            doc.blocks = blockManager.getLayout();
        }, { groupWithPrevious: true });
    }, initialFaq);
    inspector.setBlockManager(blockManager);

    // 4. INSIALISASI CENTRAL EDITOR STATE (TASK 5)
    const initialFullDoc = {
        version: initialVersion,
        blocks: blockManager.getLayout(),
        global_styles: inspector.globalStyles,
        content: inspector.getSerializedState(),
    };

    editorState = new EditorState(initialFullDoc, {
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

    // 6. INSIALISASI ROLLBACK SATU LANGKAH (IN-PAGE MAGKOM CONFIRMATION MODAL - REF SS 1)
    const rollbackBtn = document.getElementById('btn-rollback');
    const rollbackModal = document.getElementById('wp-rollback-modal');
    const closeRollbackBtn = document.getElementById('btn-close-rollback-modal');
    const cancelRollbackBtn = document.getElementById('btn-cancel-rollback-modal');
    const confirmRollbackBtn = document.getElementById('btn-confirm-rollback-modal');

    function openRollbackModal() {
        if (!rollbackModal) return;
        rollbackModal.classList.add('is-open');
        if (confirmRollbackBtn) {
            confirmRollbackBtn.disabled = false;
            confirmRollbackBtn.classList.remove('is-loading');
        }
    }

    function closeRollbackModal() {
        if (!rollbackModal) return;
        rollbackModal.classList.remove('is-open');
        if (confirmRollbackBtn) {
            confirmRollbackBtn.disabled = false;
            confirmRollbackBtn.classList.remove('is-loading');
        }
    }

    rollbackBtn?.addEventListener('click', (e) => {
        if (e) e.preventDefault();
        openRollbackModal();
    });

    closeRollbackBtn?.addEventListener('click', closeRollbackModal);
    cancelRollbackBtn?.addEventListener('click', closeRollbackModal);
    rollbackModal?.addEventListener('click', (e) => {
        if (e.target === rollbackModal) closeRollbackModal();
    });

    confirmRollbackBtn?.addEventListener('click', async () => {
        if (!confirmRollbackBtn) return;
        confirmRollbackBtn.disabled = true;
        confirmRollbackBtn.classList.add('is-loading');

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        try {
            const res = await fetch('/superadmin/site-editor/rollback', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                }
            });

            const data = await res.json().catch(() => ({}));

            if (res.ok && data.success) {
                closeRollbackModal();
                showToast(
                    'Rollback Berhasil',
                    data.message || 'Tampilan publik berhasil dikembalikan ke versi sebelum publikasi terakhir. Memuat ulang kanvas...',
                    'info',
                    5000
                );
                setTimeout(() => {
                    window.location.reload();
                }, 1200);
            } else {
                closeRollbackModal();
                showToast(
                    'Gagal Rollback',
                    data.message || 'Tidak ada rekaman backup versi sebelumnya yang dapat dipulihkan.',
                    'error',
                    6000
                );
            }
        } catch (err) {
            console.error(err);
            closeRollbackModal();
            showToast(
                'Kesalahan Jaringan',
                'Terjadi kesalahan komunikasi dengan server saat melakukan rollback.',
                'error',
                6000
            );
        }
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

    // Multi-Page Canvas Selector (Landing Page / Kebijakan Privasi)
    const pageSelector = document.getElementById('select-canvas-page');
    const previewExternalBtn = document.getElementById('btn-preview-external');
    const viewLiveSiteBtn = document.getElementById('btn-view-live-site');
    const basePreviewUrl = previewExternalBtn ? previewExternalBtn.getAttribute('href').split('?')[0] : '/superadmin/landing/preview';
    const baseLiveUrl = viewLiveSiteBtn ? viewLiveSiteBtn.getAttribute('href').replace(/\/kebijakan-privasi\/?$/, '') : '/';

    if (pageSelector && iframeEl) {
        pageSelector.addEventListener('change', (e) => {
            const selectedPage = e.target.value;
            const currentSrc = new URL(iframeEl.src, window.location.origin);

            if (selectedPage === 'landing') {
                currentSrc.searchParams.delete('page');
                if (previewExternalBtn) previewExternalBtn.setAttribute('href', basePreviewUrl);
                if (viewLiveSiteBtn) viewLiveSiteBtn.setAttribute('href', baseLiveUrl || '/');
            } else {
                currentSrc.searchParams.set('page', selectedPage);
                if (previewExternalBtn) previewExternalBtn.setAttribute('href', `${basePreviewUrl}?page=${encodeURIComponent(selectedPage)}`);
                if (viewLiveSiteBtn) viewLiveSiteBtn.setAttribute('href', `${baseLiveUrl.replace(/\/$/, '')}/${selectedPage}`);
            }
            currentSrc.searchParams.set('editor_mode', '1');
            iframeEl.src = currentSrc.toString();

            // Buka section terkait di Inspector
            if (selectedPage === 'kebijakan-privasi') {
                if (floatingToolbar) floatingToolbar.style.display = 'none';
                inspector.setActiveBlock('kebijakan', 'Kebijakan Privasi', 'block-kebijakan-1');
                showToast('Beralih ke Kanvas Halaman Kebijakan Privasi.', true);
            } else {
                inspector.setActiveBlock('hero', 'Hero Banner', 'block-hero-1');
                showToast('Beralih ke Kanvas Beranda Utama.', true);
            }
        });
    }

    // 8. LIST VIEW DRAWER NAVIGATION
    const listViewDrawer = document.getElementById('wp-list-view-drawer');
    const listViewBtn = document.getElementById('btn-list-view');
    const closeListViewBtn = document.getElementById('btn-close-list-view');

    function openListView() {
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

    listViewBtn?.addEventListener('click', toggleListView);
    closeListViewBtn?.addEventListener('click', closeListView);

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    // 9. FLOATING TOOLBAR ACTIONS (CANVAS - SUPER MINIMALIS)
    const btnBlockUp = document.getElementById('btn-block-up');
    const btnBlockDown = document.getElementById('btn-block-down');
    const btnToolbarDuplicate = document.getElementById('btn-toolbar-duplicate');
    const btnToolbarDelete = document.getElementById('btn-toolbar-delete');

    function updateFloatingToolbarState(blockId) {
        const layout = blockManager.getLayout();
        const idx = layout.findIndex(b => b.id === blockId || b.type === blockId);
        const block = idx !== -1 ? layout[idx] : null;
        const reg = block ? (blockManager.registry[block.type] || {}) : {};
        const coreBlocks = ['hero', 'footer', 'kuota', 'tahapan', 'faq'];

        const isUserLocked = Boolean(block?.user_locked);
        const isSystemProtected = Boolean(
            (reg && (reg.required || reg.repeatable === false)) ||
            block?.locked ||
            (block && coreBlocks.includes(block.type))
        );

        if (btnBlockUp) btnBlockUp.disabled = !block || idx <= 0 || isUserLocked;
        if (btnBlockDown) btnBlockDown.disabled = !block || idx >= layout.length - 1 || isUserLocked;
        if (btnToolbarDuplicate) {
            btnToolbarDuplicate.disabled = !block || isSystemProtected;
            btnToolbarDuplicate.title = isSystemProtected ? 'Blok inti bersifat unik dan tidak dapat diduplikasi' : 'Duplikasi Blok';
        }
        if (btnToolbarDelete) {
            btnToolbarDelete.disabled = !block || isSystemProtected || isUserLocked;
            btnToolbarDelete.title = isSystemProtected ? 'Blok inti halaman tidak dapat dihapus (gunakan ikon mata di Struktur Halaman)' : 'Hapus Blok';
        }
    }

    btnBlockUp?.addEventListener('click', () => {
        const moved = blockManager.moveBlock(activeSelectedBlockId, 'up');
        if (moved) {
            updateFloatingToolbarState(activeSelectedBlockId);
        }
    });
    btnBlockDown?.addEventListener('click', () => {
        const moved = blockManager.moveBlock(activeSelectedBlockId, 'down');
        if (moved) {
            updateFloatingToolbarState(activeSelectedBlockId);
        }
    });
    btnToolbarDuplicate?.addEventListener('click', () => {
        const cloned = blockManager.duplicateBlock(activeSelectedBlockId);
        if (cloned) {
            activeSelectedBlockId = cloned.id;
            activeSelectedBlock = cloned.type;
            updateFloatingToolbarState(activeSelectedBlockId);
        }
    });
    btnToolbarDelete?.addEventListener('click', () => {
        const deleted = blockManager.deleteBlock(activeSelectedBlockId);
        if (deleted) {
            const nextBlock = blockManager.getLayout()[0];
            if (nextBlock) {
                activeSelectedBlock = nextBlock.type;
                activeSelectedBlockId = nextBlock.id;
                blockManager.setActiveBlock(activeSelectedBlockId);
                inspector.setActiveBlock(activeSelectedBlock, nextBlock.title, activeSelectedBlockId);
                updateFloatingToolbarState(activeSelectedBlockId);
            } else {
                if (floatingToolbar) floatingToolbar.style.display = 'none';
            }
        }
    });
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
        showToast('Menyimpan draf perubahan...', true);
        editorState.saveDraft(false);
    });

    // 12. PUBLISH DRAFT (IN-PAGE MAGKOM CONFIRMATION MODAL - REF SS 1)
    const publishBtn = document.getElementById('btn-publish');
    const publishModal = document.getElementById('wp-publish-modal');
    const closePublishBtn = document.getElementById('btn-close-publish-modal');
    const cancelPublishBtn = document.getElementById('btn-cancel-publish-modal');
    const confirmPublishBtn = document.getElementById('btn-confirm-publish-modal');

    function openPublishModal() {
        if (!publishModal) return;
        publishModal.classList.add('is-open');
        if (confirmPublishBtn) {
            confirmPublishBtn.disabled = false;
            confirmPublishBtn.classList.remove('is-loading');
        }
    }

    function closePublishModal() {
        if (!publishModal) return;
        publishModal.classList.remove('is-open');
        if (confirmPublishBtn) {
            confirmPublishBtn.disabled = false;
            confirmPublishBtn.classList.remove('is-loading');
        }
    }

    publishBtn?.addEventListener('click', (e) => {
        if (e) e.preventDefault();
        openPublishModal();
    });

    closePublishBtn?.addEventListener('click', closePublishModal);
    cancelPublishBtn?.addEventListener('click', closePublishModal);
    publishModal?.addEventListener('click', (e) => {
        if (e.target === publishModal) closePublishModal();
    });

    confirmPublishBtn?.addEventListener('click', async () => {
        if (!confirmPublishBtn) return;
        confirmPublishBtn.disabled = true;
        confirmPublishBtn.classList.add('is-loading');

        // 1. Simpan perubahan draf terakhir yang ada di memori terlebih dahulu agar data sinkron
        try {
            if (editorState) {
                const saveSuccess = await editorState.saveDraft(false, true);
                if (!saveSuccess) {
                    closePublishModal();
                    showToast('Gagal Menyimpan Draf', 'Penerbitan dibatalkan agar perubahan Anda tidak hilang.', 'error');
                    return;
                }
            }
        } catch (saveErr) {
            console.error('Gagal menyimpan draf sebelum publikasi:', saveErr);
            closePublishModal();
            showToast('Gagal Menyimpan Draf', 'Publikasi dibatalkan karena kesalahan sinkronisasi.', 'error');
            return;
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        try {
            const res = await fetch('/superadmin/site-editor/publish', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
            });

            const data = await res.json().catch(() => ({}));

            if (res.ok && data.success) {
                closePublishModal();

                if (editorState) {
                    editorState.lastSavedAt = editorState.getFormattedTimestamp();
                    editorState.setDirtyStatus('clean');
                }

                showToast(
                    'Perubahan Berhasil Disimpan',
                    data.message || 'Halaman landing page publik berhasil diperbarui. Seluruh draf kini telah aktif dan dapat langsung diakses oleh calon peserta magang dan masyarakat umum.',
                    'success',
                    6500
                );

                // Sinkronkan kembali iframe kanvas pratinjau agar menampilkan versi live terbaru
                const iframe = document.getElementById('wp-preview-iframe');
                if (iframe) {
                    try {
                        const currentUrl = new URL(iframe.src, window.location.origin);
                        currentUrl.searchParams.set('ts', Date.now().toString());
                        iframe.src = currentUrl.toString();
                    } catch (e) {
                        if (iframe.contentWindow) {
                            try {
                                iframe.contentWindow.location.reload();
                            } catch (err) {
                                // ignore cross-origin error if any
                            }
                        }
                    }
                }
            } else {
                closePublishModal();
                showToast('Gagal Mempublikasikan', data.message || 'Terjadi kesalahan server saat mempublikasikan draf.', 'error', 6000);
            }
        } catch (err) {
            console.error(err);
            closePublishModal();
            showToast('Kesalahan Jaringan', 'Terjadi gangguan koneksi saat mempublikasikan halaman.', 'error', 6000);
        }
    });

    // 13. SISTEM TOAST NOTIFIKASI MENGAMBANG MODERN (REFERENSI GAMBAR 2 - DS-MAGKOM)
    function showToast(title, message, type = 'success', duration = 5000) {
        let container = document.getElementById('wp-toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'wp-toast-container';
            container.className = 'toast-floating-container alerts-container';
            container.setAttribute('aria-live', 'polite');
            container.setAttribute('aria-atomic', 'true');
            document.body.appendChild(container);
        }

        // Dukung pemanggilan dengan 2 parameter: showToast('Pesan saja', true/false)
        if (typeof message === 'boolean' || typeof message === 'undefined') {
            const isSuccess = message !== false;
            type = isSuccess ? 'success' : 'error';
            message = title;
            title = isSuccess ? 'Perubahan Berhasil Disimpan' : 'Terjadi Kesalahan';
        }

        const toast = document.createElement('div');
        toast.className = 'app-toast';
        toast.setAttribute('role', 'alert');

        let iconSvg = '';
        let badgeClass = 'success';

        if (type === 'error' || type === 'danger') {
            badgeClass = 'error';
            iconSvg = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>';
        } else if (type === 'info') {
            badgeClass = 'info';
            iconSvg = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>';
        } else if (type === 'warning') {
            badgeClass = 'warning';
            iconSvg = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>';
        } else {
            badgeClass = 'success';
            iconSvg = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path><polyline points="9 12 11 14 15 10"></polyline></svg>';
        }

        const toastHeader = document.createElement('div');
        toastHeader.style.cssText = 'display: flex; align-items: center; justify-content: space-between; width: 100%; gap: 8px;';

        const toastTitle = document.createElement('div');
        toastTitle.className = 'toast-title alert-title';
        toastTitle.textContent = title;

        const toastTime = document.createElement('span');
        toastTime.style.cssText = 'font-size: 11px; color: #94a3b8; font-weight: 500; white-space: nowrap; margin-left: auto;';
        toastTime.textContent = 'Baru saja';

        toastHeader.appendChild(toastTitle);
        toastHeader.appendChild(toastTime);

        const toastDesc = document.createElement('div');
        toastDesc.className = 'toast-desc alert-desc';
        toastDesc.textContent = message;

        const toastContent = document.createElement('div');
        toastContent.className = 'toast-content alert-body';
        toastContent.appendChild(toastHeader);
        toastContent.appendChild(toastDesc);

        const badgeEl = document.createElement('div');
        badgeEl.className = `toast-icon-badge ${badgeClass}`;
        badgeEl.innerHTML = iconSvg;

        const closeBtn = document.createElement('button');
        closeBtn.type = 'button';
        closeBtn.className = 'toast-close-btn alert-close-btn';
        closeBtn.setAttribute('aria-label', 'Tutup notifikasi');
        closeBtn.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>';

        toast.appendChild(badgeEl);
        toast.appendChild(toastContent);
        toast.appendChild(closeBtn);

        function dismissToast() {
            toast.classList.add('toast-hiding');
            setTimeout(() => {
                toast.remove();
            }, 300);
        }

        closeBtn.addEventListener('click', dismissToast);

        let autoDismissTimer = setTimeout(dismissToast, duration);
        toast.addEventListener('mouseenter', () => clearTimeout(autoDismissTimer));
        toast.addEventListener('mouseleave', () => {
            autoDismissTimer = setTimeout(dismissToast, 2000);
        });

        container.appendChild(toast);
    }
    window.__wpShowToast = showToast;
}
