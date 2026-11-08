/**
 * WordPress.org Gutenberg Canvas Runtime Engine for MAGKOM Landing Page
 * Active ONLY inside iframe when ?editor_mode=1 is present
 */

(function () {
    'use strict';

    // Pastikan halaman berada di dalam iframe
    if (window.self === window.top) {
        return;
    }

    const PARENT_ORIGIN = window.location.origin;

    document.addEventListener('DOMContentLoaded', () => {
        initCanvasSelection();
        initInlineEditing();
        initMessageListener();

        // Pastikan container main mendukung urutan fleksibel
        const mainEl = document.querySelector('main');
        if (mainEl) {
            mainEl.style.display = 'flex';
            mainEl.style.flexDirection = 'column';
        }

        // Beritahu parent window bahwa kanvas siap menerima perintah
        window.parent.postMessage({ type: 'WP_CANVAS_READY' }, PARENT_ORIGIN);
    });

    /**
     * Inisialisasi seleksi visual blok (Outline biru khas WordPress)
     */
    function initCanvasSelection() {
        // Suntikkan style visual seleksi ke dalam iframe
        const styleEl = document.createElement('style');
        styleEl.id = 'wp-canvas-selection-styles';
        styleEl.textContent = `
            [data-wp-block] {
                position: relative;
                transition: outline 0.15s ease, box-shadow 0.15s ease, opacity 0.2s ease;
                cursor: pointer;
            }
            [data-wp-block]:hover {
                outline: 2px dashed #007cba !important;
                outline-offset: -2px;
            }
            [data-wp-block].is-wp-selected {
                outline: 2px solid #007cba !important;
                outline-offset: -2px;
                box-shadow: 0 0 0 1px rgba(0, 124, 186, 0.4);
            }
            .wp-block-chip-label {
                position: absolute;
                top: 6px;
                left: 6px;
                background: #007cba;
                color: #ffffff;
                font-size: 11px;
                font-weight: 700;
                padding: 3px 8px;
                border-radius: 4px;
                z-index: 999;
                pointer-events: none;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                display: none;
                align-items: center;
                gap: 5px;
                box-shadow: 0 2px 5px rgba(0,0,0,0.15);
            }
            [data-wp-block].is-wp-selected .wp-block-chip-label {
                display: inline-flex;
            }
            [data-wp-block].is-wp-hidden {
                opacity: 0.45 !important;
                filter: grayscale(40%);
            }
            [data-wp-block].is-wp-hidden::after {
                content: '⚠️ BAGIAN TERSEMBUNYI DARI PENGUNJUNG UMUM';
                position: absolute;
                top: 40px;
                left: 12px;
                background: #f59e0b;
                color: #1e1e1e;
                font-size: 11px;
                font-weight: 700;
                padding: 3px 8px;
                border-radius: 3px;
                z-index: 998;
                pointer-events: none;
            }
            [data-wp-editable] {
                outline: none;
                transition: background-color 0.15s ease;
                border-radius: 2px;
            }
            [data-wp-editable]:hover {
                background-color: rgba(0, 124, 186, 0.08);
            }
            [data-wp-editable]:focus {
                background-color: rgba(0, 124, 186, 0.14);
                outline: 1px dashed #007cba;
            }
            .wp-canvas-add-handle {
                position: absolute;
                bottom: -14px;
                left: 50%;
                transform: translateX(-50%);
                width: 28px;
                height: 28px;
                border-radius: 50%;
                background: #007cba;
                color: #ffffff;
                border: 2px solid #ffffff;
                display: none;
                align-items: center;
                justify-content: center;
                font-size: 16px;
                font-weight: 700;
                cursor: pointer;
                z-index: 999;
                box-shadow: 0 2px 6px rgba(0,0,0,0.2);
                transition: transform 0.15s ease, background-color 0.15s ease;
            }
            .wp-canvas-add-handle:hover {
                background: #006ba1;
                transform: translateX(-50%) scale(1.15);
            }
            [data-wp-block].is-wp-selected .wp-canvas-add-handle {
                display: flex;
            }
        `;
        document.head.appendChild(styleEl);

        // Tambahkan label chip dan handle (+) ke setiap blok
        document.querySelectorAll('[data-wp-block]').forEach(blockEl => {
            const blockName = blockEl.getAttribute('data-wp-block');
            const blockTitle = blockEl.getAttribute('data-wp-block-name') || blockName;
            const blockId = blockEl.getAttribute('data-wp-block-id') || `block-${blockName}`;

            const chip = document.createElement('span');
            chip.className = 'wp-block-chip-label';
            chip.innerHTML = `<span>BLOK: ${blockTitle}</span> <span>⚙️</span>`;
            blockEl.prepend(chip);

            // Tombol tambah blok cepat (+) di bawah blok terpilih
            const handle = document.createElement('button');
            handle.type = 'button';
            handle.className = 'wp-canvas-add-handle';
            handle.title = 'Tambah blok di bawah bagian ini';
            handle.innerHTML = '+';
            handle.addEventListener('click', (e) => {
                e.stopPropagation();
                window.parent.postMessage({
                    type: 'WP_INSERT_BLOCK_REQUEST',
                    afterBlockId: blockId,
                }, PARENT_ORIGIN);
            });
            blockEl.appendChild(handle);

            blockEl.addEventListener('click', (e) => {
                // Jangan picu reload link jika sedang dalam editor
                if (e.target.closest('a')) {
                    e.preventDefault();
                }

                selectBlock(blockEl);
            });
        });
    }

    /**
     * Pilih blok dan kirim notifikasi ke parent window
     */
    function selectBlock(blockEl) {
        document.querySelectorAll('[data-wp-block]').forEach(el => el.classList.remove('is-wp-selected'));
        blockEl.classList.add('is-wp-selected');

        const block = blockEl.getAttribute('data-wp-block');
        const blockTitle = blockEl.getAttribute('data-wp-block-name') || block;
        const blockId = blockEl.getAttribute('data-wp-block-id') || `block-${block}`;
        const rect = blockEl.getBoundingClientRect();

        window.parent.postMessage({
            type: 'WP_BLOCK_SELECTED',
            block: block,
            blockTitle: blockTitle,
            blockId: blockId,
            rect: {
                top: rect.top,
                bottom: rect.bottom,
                left: rect.left,
                width: rect.width,
            }
        }, PARENT_ORIGIN);
    }

    /**
     * Inisialisasi inline text editing (contenteditable)
     */
    function initInlineEditing() {
        document.querySelectorAll('[data-wp-editable]').forEach(el => {
            el.setAttribute('contenteditable', 'true');
            el.setAttribute('spellcheck', 'false');

            el.addEventListener('input', () => {
                const fieldName = el.getAttribute('data-wp-editable');
                const textContent = el.innerText.trim();

                window.parent.postMessage({
                    type: 'WP_INLINE_TEXT_CHANGED',
                    field: 'landing_' + fieldName,
                    value: textContent,
                }, PARENT_ORIGIN);
            });
        });
    }

    /**
     * Listener pesan dari parent window
     */
    function initMessageListener() {
        window.addEventListener('message', (event) => {
            // Strict Origin & Source Validation (mencegah pesan tak dikenal atau dari tab lain)
            if (event.origin !== PARENT_ORIGIN) return;
            if (event.source !== window.parent) return;

            const data = event.data;
            if (!data || !data.type) return;

            // 1. UPDATE DYNAMIC CSS PROPERTY (GLOBAL)
            if (data.type === 'WP_UPDATE_STYLE') {
                document.documentElement.style.setProperty(data.property, data.value);
            }

            // 1b. UPDATE BATCH GLOBAL STYLES
            if (data.type === 'WP_UPDATE_GLOBAL_STYLES' && data.styles) {
                applyGlobalStylesToRoot(data.styles);
            }

            // 1c. UPDATE SCOPED BLOCK STYLE (PER DEVICE BREAKPOINT)
            if (data.type === 'WP_UPDATE_BLOCK_STYLE' && data.blockId) {
                updateScopedBlockStyle(data.blockId, data.property, data.value, data.device || 'desktop');
            }

            // 1d. UPDATE BATCH SCOPED BLOCK STYLES
            if (data.type === 'WP_UPDATE_BLOCK_STYLES' && data.blockId && data.styles) {
                applyBatchScopedBlockStyles(data.blockId, data.styles);
            }

            // 1e. SET BLOCK SETTINGS (ID, ANCHOR, CUSTOM CSS, VISIBILITY)
            if (data.type === 'WP_SET_BLOCK_SETTINGS' && data.blockId && data.settings) {
                applyBlockSettings(data.blockId, data.settings);
            }

            // 2. SCROLL TO BLOCK
            if (data.type === 'WP_SCROLL_TO_BLOCK') {
                const targetEl = (data.blockId ? document.querySelector(`[data-wp-block-id="${data.blockId}"]`) : null)
                    || document.querySelector(`[data-wp-block="${data.block}"]`);
                if (targetEl) {
                    targetEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    selectBlock(targetEl);
                }
            }

            // 3. TRIGGER SELECT BLOCK
            if (data.type === 'WP_TRIGGER_SELECT_BLOCK') {
                const targetEl = (data.blockId ? document.querySelector(`[data-wp-block-id="${data.blockId}"]`) : null)
                    || document.querySelector(`[data-wp-block="${data.block}"]`);
                if (targetEl) {
                    selectBlock(targetEl);
                }
            }

            // 4. REORDER BLOCKS (CSS Flex Order)
            if (data.type === 'WP_REORDER_BLOCKS' && Array.isArray(data.layout)) {
                data.layout.forEach(b => {
                    const el = document.querySelector(`[data-wp-block-id="${b.id}"]`)
                        || document.querySelector(`[data-wp-block="${b.type}"]`);
                    if (el) {
                        el.style.order = b.order;
                        el.classList.toggle('is-wp-hidden', !b.visible);
                    }
                });
            }

            // 5. SET BLOCK VISIBILITY
            if (data.type === 'WP_SET_BLOCK_VISIBILITY') {
                const el = (data.blockId ? document.querySelector(`[data-wp-block-id="${data.blockId}"]`) : null)
                    || document.querySelector(`[data-wp-block="${data.block}"]`);
                if (el) {
                    el.classList.toggle('is-wp-hidden', !data.visible);
                }
            }

            // 6. UPDATE HERO IMAGE
            if (data.type === 'WP_UPDATE_HERO_IMAGE' && data.url) {
                const heroImg = document.querySelector('#beranda img') || document.querySelector('.hero-box-banner');
                if (heroImg) {
                    heroImg.src = data.url;
                }
            }

            // 7. SET TEXT MANUALLY
            if (data.type === 'WP_SET_TEXT') {
                const target = document.querySelector(`[data-wp-editable="${data.field}"]`);
                if (target) {
                    target.innerText = data.text;
                }
            }

            // 8. RESET BLOCK STYLES
            if (data.type === 'WP_RESET_BLOCK_STYLES') {
                if (data.blockId) {
                    resetSingleBlockStyles(data.blockId);
                } else {
                    document.documentElement.removeAttribute('style');
                    document.querySelectorAll('style[id^="scoped-style-"]').forEach(s => s.remove());
                }
            }
        });
    }

    /**
     * Menerapkan objek Global Styles ke :root kanvas
     */
    function applyGlobalStylesToRoot(globalStyles) {
        if (!globalStyles || typeof globalStyles !== 'object') return;
        const root = document.documentElement;

        if (globalStyles.colors) {
            const colors = globalStyles.colors;
            if (colors.primary) {
                root.style.setProperty('--navy', colors.primary);
                root.style.setProperty('--wp-color-primary', colors.primary);
            }
            if (colors.primary_hover) {
                root.style.setProperty('--navy-dark', colors.primary_hover);
                root.style.setProperty('--wp-color-primary-hover', colors.primary_hover);
            }
            if (colors.secondary) {
                root.style.setProperty('--secondary', colors.secondary);
                root.style.setProperty('--wp-color-secondary', colors.secondary);
            }
            if (colors.accent) {
                root.style.setProperty('--accent', colors.accent);
                root.style.setProperty('--wp-color-accent', colors.accent);
            }
            if (colors.background) {
                root.style.setProperty('--background', colors.background);
                root.style.setProperty('--wp-color-background', colors.background);
            }
            if (colors.card) {
                root.style.setProperty('--card', colors.card);
                root.style.setProperty('--wp-color-card', colors.card);
            }
            if (colors.text) {
                root.style.setProperty('--foreground', colors.text);
                root.style.setProperty('--wp-color-text', colors.text);
            }
            if (colors.text_muted) {
                root.style.setProperty('--muted-text', colors.text_muted);
                root.style.setProperty('--wp-color-text-muted', colors.text_muted);
            }
            if (colors.border) {
                root.style.setProperty('--border', colors.border);
                root.style.setProperty('--wp-color-border', colors.border);
            }
            if (colors.success) {
                root.style.setProperty('--gold', colors.success);
                root.style.setProperty('--wp-color-success', colors.success);
            }
        }

        if (globalStyles.typography) {
            const typo = globalStyles.typography;
            if (typo.heading_font) {
                root.style.setProperty('--font-display', `'${typo.heading_font}', sans-serif`);
                root.style.setProperty('--wp-font-display', `'${typo.heading_font}', sans-serif`);
            }
            if (typo.body_font) {
                root.style.setProperty('--font-sans', `'${typo.body_font}', sans-serif`);
                root.style.setProperty('--wp-font-sans', `'${typo.body_font}', sans-serif`);
            }
            if (typo.base_size_px) {
                root.style.setProperty('--wp-base-font-size', `${typo.base_size_px}px`);
            }
            if (typo.line_height) {
                root.style.setProperty('--wp-line-height', String(typo.line_height));
            }
        }

        if (globalStyles.layout) {
            const layout = globalStyles.layout;
            if (layout.container_max_width_px) {
                root.style.setProperty('--wp-container-max-w', `${layout.container_max_width_px}px`);
            }
            if (layout.section_gap_px) {
                root.style.setProperty('--wp-section-gap-y', `${layout.section_gap_px}px`);
            }
            if (layout.card_radius_px !== undefined) {
                root.style.setProperty('--border-radius-card', `${layout.card_radius_px}px`);
                root.style.setProperty('--wp-card-border-radius', `${layout.card_radius_px}px`);
            }
            if (layout.card_shadow) {
                const shadowMap = {
                    none: 'none',
                    soft: '0 4px 12px rgba(0, 0, 0, 0.06)',
                    medium: '0 8px 24px rgba(0, 0, 0, 0.09)',
                    strong: '0 16px 32px rgba(0, 0, 0, 0.12)',
                };
                root.style.setProperty('--wp-card-shadow', shadowMap[layout.card_shadow] || layout.card_shadow);
            }
        }

        if (globalStyles.buttons) {
            const btns = globalStyles.buttons;
            if (btns.radius_px !== undefined) {
                root.style.setProperty('--radius-button', `${btns.radius_px}px`);
                root.style.setProperty('--wp-radius-button', `${btns.radius_px}px`);
            }
            if (btns.padding_y_px) {
                root.style.setProperty('--wp-btn-padding-y', `${btns.padding_y_px}px`);
            }
            if (btns.padding_x_px) {
                root.style.setProperty('--wp-btn-padding-x', `${btns.padding_x_px}px`);
            }
            if (btns.primary_bg) {
                root.style.setProperty('--wp-btn-primary-bg', btns.primary_bg);
            }
            if (btns.primary_text) {
                root.style.setProperty('--wp-btn-primary-text', btns.primary_text);
            }
        }
    }

    /**
     * Update scoped block style tag di <head>
     */
    function updateScopedBlockStyle(blockId, property, value, device = 'desktop') {
        const styleId = `scoped-style-${blockId}`;
        let styleEl = document.getElementById(styleId);
        if (!styleEl) {
            styleEl = document.createElement('style');
            styleEl.id = styleId;
            document.head.appendChild(styleEl);
        }

        if (!styleEl._styles) {
            styleEl._styles = { desktop: {}, tablet: {}, mobile: {} };
        }

        if (value === null || value === '' || value === undefined) {
            delete styleEl._styles[device][property];
        } else {
            styleEl._styles[device][property] = value;
        }

        renderScopedStyleSheet(styleEl, blockId);
    }

    function applyBatchScopedBlockStyles(blockId, styles) {
        const styleId = `scoped-style-${blockId}`;
        let styleEl = document.getElementById(styleId);
        if (!styleEl) {
            styleEl = document.createElement('style');
            styleEl.id = styleId;
            document.head.appendChild(styleEl);
        }

        styleEl._styles = {
            desktop: { ...(styles.desktop || {}) },
            tablet: { ...(styles.tablet || {}) },
            mobile: { ...(styles.mobile || {}) },
        };

        ['padding_y', 'margin_y', 'font_size', 'text_align', 'bg_color', 'text_color', 'card_radius', 'height'].forEach(k => {
            if (styles[k] !== undefined) {
                styleEl._styles.desktop[k] = styles[k];
            }
        });

        renderScopedStyleSheet(styleEl, blockId);
    }

    function renderScopedStyleSheet(styleEl, blockId) {
        const d = styleEl._styles.desktop || {};
        const t = styleEl._styles.tablet || {};
        const m = styleEl._styles.mobile || {};

        function buildDeclarations(obj) {
            const rules = [];
            if (obj.padding_y !== undefined && obj.padding_y !== '') {
                rules.push(`--block-padding-y: ${obj.padding_y}px;`);
                rules.push(`padding-top: ${obj.padding_y}px !important;`);
                rules.push(`padding-bottom: ${obj.padding_y}px !important;`);
            }
            if (obj.margin_y !== undefined && obj.margin_y !== '') {
                rules.push(`--block-margin-y: ${obj.margin_y}px;`);
                rules.push(`margin-top: ${obj.margin_y}px !important;`);
                rules.push(`margin-bottom: ${obj.margin_y}px !important;`);
            }
            if (obj.font_size !== undefined && obj.font_size !== '') {
                rules.push(`--block-font-size: ${obj.font_size}px;`);
            }
            if (obj.text_align) {
                rules.push(`--block-text-align: ${obj.text_align};`);
                rules.push(`text-align: ${obj.text_align} !important;`);
            }
            if (obj.bg_color) {
                rules.push(`--block-bg-color: ${obj.bg_color};`);
                rules.push(`background-color: ${obj.bg_color} !important;`);
            }
            if (obj.text_color) {
                rules.push(`--block-text-color: ${obj.text_color};`);
                rules.push(`color: ${obj.text_color} !important;`);
            }
            if (obj.card_radius !== undefined && obj.card_radius !== '') {
                rules.push(`--block-card-radius: ${obj.card_radius}px;`);
            }
            if (obj.height !== undefined && obj.height !== '') {
                rules.push(`height: ${obj.height}px !important; min-height: ${obj.height}px !important;`);
            }
            return rules.join(' ');
        }

        let css = '';
        const desktopRules = buildDeclarations(d);
        if (desktopRules) {
            css += `[data-wp-block-id="${blockId}"] { ${desktopRules} }\n`;
        }

        const tabletRules = buildDeclarations(t);
        if (tabletRules) {
            css += `@media (max-width: 1023px) { [data-wp-block-id="${blockId}"] { ${tabletRules} } }\n`;
        }

        const mobileRules = buildDeclarations(m);
        if (mobileRules) {
            css += `@media (max-width: 767px) { [data-wp-block-id="${blockId}"] { ${mobileRules} } }\n`;
        }

        styleEl.textContent = css;
    }

    function resetSingleBlockStyles(blockId) {
        const styleId = `scoped-style-${blockId}`;
        const styleEl = document.getElementById(styleId);
        if (styleEl) {
            styleEl.remove();
        }
        const blockEl = document.querySelector(`[data-wp-block-id="${blockId}"]`);
        if (blockEl) {
            blockEl.style.removeProperty('--block-padding-y');
            blockEl.style.removeProperty('--block-margin-y');
            blockEl.style.removeProperty('--block-font-size');
            blockEl.style.removeProperty('--block-text-align');
            blockEl.style.removeProperty('--block-bg-color');
            blockEl.style.removeProperty('--block-text-color');
            blockEl.style.removeProperty('--block-card-radius');
        }
    }

    function applyBlockSettings(blockId, settings) {
        const blockEl = document.querySelector(`[data-wp-block-id="${blockId}"]`);
        if (!blockEl) return;

        if (settings.html_anchor) {
            const cleanAnchor = settings.html_anchor.replace(/[^a-zA-Z0-9_\-]/g, '');
            if (cleanAnchor) {
                blockEl.id = cleanAnchor;
            }
        }

        if (blockEl._customClasses) {
            blockEl._customClasses.forEach(c => blockEl.classList.remove(c));
        }
        if (settings.custom_css_class) {
            const classes = settings.custom_css_class.split(/\s+/).filter(Boolean);
            blockEl._customClasses = classes;
            classes.forEach(c => blockEl.classList.add(c));
        }

        blockEl.classList.toggle('hide-on-desktop', settings.visible_desktop === false);
        blockEl.classList.toggle('hide-on-tablet', settings.visible_tablet === false);
        blockEl.classList.toggle('hide-on-mobile', settings.visible_mobile === false);
    }
})();
