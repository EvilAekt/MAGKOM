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

            // Buka panel inspector kanan otomatis jika sedang tertutup (SS 3 -> SS 4)
            const inspectorEl = document.getElementById('wp-site-inspector');
            const toggleInspectorBtn = document.getElementById('btn-toggle-inspector');
            if (inspectorEl && inspectorEl.style.display === 'none') {
                inspectorEl.style.display = 'flex';
                toggleInspectorBtn?.classList.add('is-active');
            }

            // Posisikan Floating Toolbar tepat di atas kanvas
            if (floatingToolbar && toolbarBlockName) {
                toolbarBlockName.textContent = `Blok: ${blockTitle}`;
                floatingToolbar.style.display = 'flex';
            }
        },
        onInlineTextChanged: (fieldName, value, blockId) => {
            inspector.updateFieldFromInline(fieldName, value);
            editorState.mutate('inline_text_edit', (doc) => {
                doc.content = inspector.getSerializedState();
                doc.blocks = blockManager.getLayout();
            }, { target: fieldName, recordHistory: true });
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
    const inspector = new Inspector(bridge, initialGlobalStyles, presets, () => {
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

    // 6. INSIALISASI ROLLBACK SATU LANGKAH
    const rollbackBtn = document.getElementById('btn-rollback');
    rollbackBtn?.addEventListener('click', async (e) => {
        if (e) e.preventDefault();
        const confirmed = window.confirm('PERINGATAN: Apakah Anda yakin ingin mengembalikan tampilan Landing Page publik ke versi sebelum publikasi terakhir? Aksi ini akan langsung memulihkan backup terakhir di sisi pengunjung publik.');
        if (!confirmed) return;

        rollbackBtn.disabled = true;
        showToast('Memulihkan backup versi publik sebelumnya...', true);

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
                try {
                    sessionStorage.setItem('magkom_editor_flash', JSON.stringify({
                        type: 'success',
                        message: data.message || '✓ Berhasil Rollback! Tampilan publik dikembalikan ke versi sebelumnya.',
                    }));
                } catch (storageErr) {}

                window.alert(data.message || '✓ Berhasil Rollback! Tampilan publik berhasil dikembalikan ke versi sebelum publikasi terakhir.');
                window.location.reload();
            } else {
                rollbackBtn.disabled = false;
                showToast(data.message || 'Tidak ada rekaman backup versi sebelumnya yang dapat dipulihkan.', false);
                window.alert(data.message || 'Tidak ada rekaman backup versi sebelumnya yang dapat dipulihkan.');
            }
        } catch (err) {
            console.error(err);
            rollbackBtn.disabled = false;
            showToast('Terjadi kesalahan jaringan saat rollback.', false);
            window.alert('Terjadi kesalahan jaringan saat rollback.');
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
            if (selectedPage === 'kebijakan-privasi') {
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
    document.getElementById('btn-block-up')?.addEventListener('click', () => {
        blockManager.moveBlock(activeSelectedBlockId, 'up');
    });
    document.getElementById('btn-block-down')?.addEventListener('click', () => {
        blockManager.moveBlock(activeSelectedBlockId, 'down');
    });
    document.getElementById('btn-toolbar-duplicate')?.addEventListener('click', () => {
        blockManager.duplicateBlock(activeSelectedBlockId);
    });
    document.getElementById('btn-toolbar-delete')?.addEventListener('click', () => {
        blockManager.deleteBlock(activeSelectedBlockId);
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

    // 12. PUBLISH DRAFT (DIRECT PUBLISH)
    const publishBtn = document.getElementById('btn-publish');
    publishBtn?.addEventListener('click', async (e) => {
        if (e) e.preventDefault();
        const confirmed = window.confirm('Apakah Anda yakin ingin mempublikasikan seluruh perubahan draf ini ke versi live publik? Seluruh pengunjung umum akan langsung melihat versi baru ini.');
        if (!confirmed) return;

        publishBtn.disabled = true;
        const spinner = publishBtn.querySelector('.btn-spinner');
        if (spinner) spinner.style.display = 'inline-block';
        showToast('Menyimpan perubahan dan mempublikasikan...', true);

        // 1. Simpan perubahan draf terakhir yang ada di memori terlebih dahulu agar data sinkron
        try {
            const saveSuccess = await editorState.saveDraft(false, true);
            if (!saveSuccess) {
                showToast('Gagal menyimpan draf sebelum publikasi. Penerbitan dibatalkan agar perubahan tidak hilang.', false);
                publishBtn.disabled = false;
                if (spinner) spinner.style.display = 'none';
                return;
            }
        } catch (saveErr) {
            console.error('Gagal menyimpan draf sebelum publikasi:', saveErr);
            showToast('Gagal menyimpan draf terbaru. Publikasi dibatalkan.', false);
            publishBtn.disabled = false;
            if (spinner) spinner.style.display = 'none';
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
                try {
                    sessionStorage.setItem('magkom_editor_flash', JSON.stringify({
                        type: 'success',
                        message: '✓ Berhasil Diterbitkan! Seluruh perubahan telah aktif di halaman publik.',
                    }));
                } catch (storageErr) {}

                window.alert(data.message || '✓ Berhasil Diterbitkan! Seluruh elemen draf berhasil dipublikasikan ke versi live publik.');
                window.location.reload();
            } else {
                publishBtn.disabled = false;
                if (spinner) spinner.style.display = 'none';
                showToast('Gagal mempublikasikan: ' + (data.message || 'Error server'), false);
                window.alert('Gagal mempublikasikan: ' + (data.message || 'Terjadi kesalahan pada server.'));
            }
        } catch (err) {
            console.error(err);
            publishBtn.disabled = false;
            if (spinner) spinner.style.display = 'none';
            showToast('Terjadi kesalahan jaringan saat mempublikasikan.', false);
            window.alert('Terjadi kesalahan jaringan saat mempublikasikan.');
        }
    });

    // 13. TOAST NOTIFIKASI HELPER
    let toastTimeout = null;
    function showToast(message, isSuccess = true) {
        let activeToastEl = document.getElementById('wp-toast');
        if (!activeToastEl) {
            activeToastEl = document.createElement('div');
            activeToastEl.id = 'wp-toast';
            activeToastEl.className = 'wp-toast';
            activeToastEl.innerHTML = '<span id="wp-toast-icon" class="wp-toast-icon">✓</span><span id="wp-toast-msg" class="wp-toast-msg"></span>';
            document.body.appendChild(activeToastEl);
        }

        const msgEl = document.getElementById('wp-toast-msg');
        const iconEl = document.getElementById('wp-toast-icon');

        if (msgEl) msgEl.textContent = message;
        if (iconEl) iconEl.textContent = isSuccess ? '✓' : '⚠️';

        activeToastEl.className = `wp-toast ${isSuccess ? 'toast-success' : 'toast-error'}`;
        activeToastEl.style.display = 'flex';

        if (toastTimeout) clearTimeout(toastTimeout);
        toastTimeout = setTimeout(() => {
            activeToastEl.style.display = 'none';
        }, 3500);
    }
    window.__wpShowToast = showToast;
}
