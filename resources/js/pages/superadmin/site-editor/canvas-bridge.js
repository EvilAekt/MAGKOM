/**
 * Canvas Bridge - Menghubungkan Editor Parent dengan Iframe Pratinjau
 * Protokol Komunikasi Dual-Way Window PostMessage dengan Validasi Origin & Message Type
 */

export class CanvasBridge {
    constructor(iframeElement, handlers = {}) {
        this.iframe = iframeElement;
        this.handlers = handlers;
        this.targetOrigin = window.location.origin;
        this.isReady = false;

        // Allowlist tipe pesan yang sah
        this.allowedMessageTypes = [
            'WP_CANVAS_READY',
            'WP_BLOCK_SELECTED',
            'WP_INLINE_TEXT_CHANGED',
            'WP_INSERT_BLOCK_REQUEST',
            'WP_BLOCK_MOVED',
            'WP_DUPLICATE_BLOCK_REQUEST',
            'WP_DELETE_BLOCK_REQUEST',
        ];

        this.initListener();
    }

    initListener() {
        window.addEventListener('message', (event) => {
            // 1. Strict Origin Validation
            if (event.origin !== this.targetOrigin) {
                return;
            }

            // 2. Strict Event Source Validation (Hanya terima pesan dari iframe anak terdaftar)
            if (this.iframe && this.iframe.contentWindow && event.source !== this.iframe.contentWindow) {
                return;
            }

            const data = event.data;
            if (!data || !data.type || !this.allowedMessageTypes.includes(data.type)) {
                return;
            }

            // Handshake siap
            if (data.type === 'WP_CANVAS_READY') {
                this.isReady = true;
                if (typeof this.handlers.onReady === 'function') {
                    this.handlers.onReady();
                }
            }

            // Blok dipilih di kanvas
            if (data.type === 'WP_BLOCK_SELECTED') {
                if (typeof this.handlers.onBlockSelected === 'function') {
                    this.handlers.onBlockSelected(data.block, data.blockTitle, data.rect, data.blockId);
                }
            }

            // Teks diedit secara inline (contenteditable)
            if (data.type === 'WP_INLINE_TEXT_CHANGED') {
                if (typeof this.handlers.onInlineTextChanged === 'function') {
                    this.handlers.onInlineTextChanged(data.field, data.value, data.blockId);
                }
            }

            // Permintaan sisip blok dari tombol plus di kanvas
            if (data.type === 'WP_INSERT_BLOCK_REQUEST') {
                if (typeof this.handlers.onInsertBlockRequest === 'function') {
                    this.handlers.onInsertBlockRequest(data.afterBlockId);
                }
            }
        });
    }

    /**
     * Kirim pembaruan CSS Custom Property langsung ke kanvas
     */
    updateStyle(property, value) {
        if (!this.iframe || !this.iframe.contentWindow) return;

        this.iframe.contentWindow.postMessage({
            type: 'WP_UPDATE_STYLE',
            property: property,
            value: value,
        }, this.targetOrigin);
    }

    /**
     * Kirim pembaruan susunan blok (reorder) ke kanvas
     */
    reorderBlocks(blocksLayout) {
        if (!this.iframe || !this.iframe.contentWindow) return;

        this.iframe.contentWindow.postMessage({
            type: 'WP_REORDER_BLOCKS',
            layout: blocksLayout,
        }, this.targetOrigin);
    }

    /**
     * Kirim perintah ubah visibilitas blok
     */
    setBlockVisibility(blockType, isVisible, blockId) {
        if (!this.iframe || !this.iframe.contentWindow) return;

        this.iframe.contentWindow.postMessage({
            type: 'WP_SET_BLOCK_VISIBILITY',
            block: blockType,
            blockId: blockId,
            visible: isVisible,
        }, this.targetOrigin);
    }

    /**
     * Gulirkan kanvas ke blok tertentu
     */
    scrollToBlock(blockName, blockId = null) {
        if (!this.iframe || !this.iframe.contentWindow) return;

        this.iframe.contentWindow.postMessage({
            type: 'WP_SCROLL_TO_BLOCK',
            block: blockName,
            blockId: blockId,
        }, this.targetOrigin);
    }

    /**
     * Set aktifkan seleksi blok di kanvas dari parent
     */
    selectBlockInCanvas(blockName, blockId = null) {
        if (!this.iframe || !this.iframe.contentWindow) return;

        this.iframe.contentWindow.postMessage({
            type: 'WP_TRIGGER_SELECT_BLOCK',
            block: blockName,
            blockId: blockId,
        }, this.targetOrigin);
    }

    /**
     * Kembalikan gaya ke default
     */
    resetStyles(blockId = null) {
        if (!this.iframe || !this.iframe.contentWindow) return;

        this.iframe.contentWindow.postMessage({
            type: 'WP_RESET_BLOCK_STYLES',
            blockId: blockId,
        }, this.targetOrigin);
    }

    /**
     * Kirim pembaruan gaya scoped spesifik untuk blok tertentu
     */
    updateBlockStyle(blockId, property, value, device = 'desktop') {
        if (!this.iframe || !this.iframe.contentWindow || !blockId) return;

        this.iframe.contentWindow.postMessage({
            type: 'WP_UPDATE_BLOCK_STYLE',
            blockId: blockId,
            property: property,
            value: value,
            device: device,
        }, this.targetOrigin);
    }

    /**
     * Kirim pembaruan batch gaya scoped untuk satu blok
     */
    updateBlockStyles(blockId, styles) {
        if (!this.iframe || !this.iframe.contentWindow || !blockId) return;

        this.iframe.contentWindow.postMessage({
            type: 'WP_UPDATE_BLOCK_STYLES',
            blockId: blockId,
            styles: styles,
        }, this.targetOrigin);
    }

    /**
     * Kirim pembaruan batch Global Styles ke kanvas (:root)
     */
    updateGlobalStyles(stylesObj) {
        if (!this.iframe || !this.iframe.contentWindow || !stylesObj) return;

        this.iframe.contentWindow.postMessage({
            type: 'WP_UPDATE_GLOBAL_STYLES',
            styles: stylesObj,
        }, this.targetOrigin);
    }

    /**
     * Kirim pembaruan pengaturan lanjutan (ID, class, anchor, visibility) pada blok
     */
    setBlockSettings(blockId, settings) {
        if (!this.iframe || !this.iframe.contentWindow || !blockId) return;

        this.iframe.contentWindow.postMessage({
            type: 'WP_SET_BLOCK_SETTINGS',
            blockId: blockId,
            settings: settings,
        }, this.targetOrigin);
    }

    /**
     * Perbarui gambar background hero di kanvas
     */
    updateHeroImage(imageUrl) {
        if (!this.iframe || !this.iframe.contentWindow) return;

        this.iframe.contentWindow.postMessage({
            type: 'WP_UPDATE_HERO_IMAGE',
            url: imageUrl,
        }, this.targetOrigin);
    }
}
