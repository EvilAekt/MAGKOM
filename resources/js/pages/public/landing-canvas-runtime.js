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
        initHeroSliderControls();

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
        `;
        document.head.appendChild(styleEl);

        // Tambahkan label chip ke setiap blok
        document.querySelectorAll('[data-wp-block]').forEach(blockEl => {
            const blockName = blockEl.getAttribute('data-wp-block');
            const blockTitle = blockEl.getAttribute('data-wp-block-name') || blockName;
            const blockId = blockEl.getAttribute('data-wp-block-id') || `block-${blockName}`;

            const chip = document.createElement('span');
            chip.className = 'wp-block-chip-label';
            chip.innerHTML = `<span>BLOK: ${blockTitle}</span> <span>⚙️</span>`;
            blockEl.prepend(chip);

            blockEl.addEventListener('click', (e) => {
                // Cegah link eksternal mereload kanvas
                if (e.target.closest('a')) {
                    e.preventDefault();
                }

                // Pilih blok hanya jika belum terpilih agar tidak memicu re-render inspector berulang
                if (!blockEl.classList.contains('is-wp-selected')) {
                    selectBlock(blockEl);
                }
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
                const heroImgs = document.querySelectorAll('#heroFullImg, .hero-mobile-img, #beranda img');
                heroImgs.forEach(img => {
                    img.src = data.url;
                });
                const dotDesktop2 = document.querySelector('#heroSliderDotsDesktop [data-slide-index="1"]');
                if (dotDesktop2) {
                    dotDesktop2.click();
                }
                const dotMobile4 = document.querySelector('#heroSliderDotsMobile [data-slide-index="3"]');
                if (dotMobile4) {
                    dotMobile4.click();
                }
            }

            // 6b. UPDATE MULTI-SLIDE HERO BANNER (DINAMIS N SLIDES)
            if (data.type === 'WP_UPDATE_HERO_SLIDES' && Array.isArray(data.slides)) {
                updateCanvasHeroSlides(data.slides);
            }

            // 6b2. NAVIGASI SLIDER HERO KE SLIDE TERTENTU
            if (data.type === 'WP_NAVIGATE_HERO_SLIDE' && typeof data.slideIndex === 'number') {
                navigateHeroSlider(data.slideIndex);
            }

            // 6c. UPDATE FAQ ACCORDION DATA (CATEGORIES & Q&A)
            if (data.type === 'WP_UPDATE_FAQ_DATA' && Array.isArray(data.faqData)) {
                updateCanvasFaq(data.faqData);
            }

            // 6d. UPDATE TAHAPAN DATA (HEADING, SUMMARY, 4 POS CHECKS)
            if (data.type === 'WP_UPDATE_TAHAPAN_DATA' && data.tahapanData) {
                updateCanvasTahapan(data.tahapanData);
            }

            // 7. SET TEXT MANUALLY
            if (data.type === 'WP_SET_TEXT') {
                const fieldKey = data.field ? data.field.replace(/^landing_/, '') : '';
                const selector = `[data-wp-editable="${data.field}"]` + (fieldKey ? `, [data-wp-editable="${fieldKey}"]` : '');
                const targets = document.querySelectorAll(selector);

                targets.forEach(target => {
                    if (target.tagName.toLowerCase() === 'ul') {
                        const lines = (data.text || '').split('\n').map(l => l.trim()).filter(l => l.length > 0);
                        target.innerHTML = lines.map(line => {
                            const temp = document.createElement('div');
                            temp.textContent = line;
                            return `<li>${temp.innerHTML}</li>`;
                        }).join('');
                    } else if (target.classList.contains('hero-subhead')) {
                        const rawText = (data.text || '').trim();
                        let lines = rawText.split('\n');
                        if (lines.length === 1 && rawText.includes(' ')) {
                            const spaceIdx = rawText.indexOf(' ');
                            const w1 = rawText.substring(0, spaceIdx);
                            const w2 = rawText.substring(spaceIdx + 1);
                            const t1 = document.createElement('span');
                            t1.textContent = w1;
                            const t2 = document.createElement('span');
                            t2.textContent = w2;
                            target.innerHTML = `${t1.innerHTML}<br><span class="text-accent">${t2.innerHTML}</span>`;
                        } else if (lines.length > 1) {
                            const t1 = document.createElement('span');
                            t1.textContent = lines[0];
                            const t2 = document.createElement('span');
                            t2.textContent = lines.slice(1).join(' ');
                            target.innerHTML = `${t1.innerHTML}<br><span class="text-accent">${t2.innerHTML}</span>`;
                        } else {
                            target.textContent = rawText;
                        }
                    } else {
                        target.innerText = data.text;
                    }
                });
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

    function updateCanvasHeroSlides(slides) {
        const track = document.getElementById('heroSliderTrack');
        if (!track || !Array.isArray(slides)) return;

        const escapeText = (t) => {
            const d = document.createElement('div');
            d.textContent = t || '';
            return d.innerHTML;
        };

        // Hapus elemen banner lama
        track.querySelectorAll('[data-slide-role="banner"]').forEach(el => el.remove());

        // Tambahkan Desktop Slides
        slides.forEach((slide, sIdx) => {
            let sType = slide.type;
            if (!sType) {
                if (slide.image && (!slide.title || slide.title.trim() === '') && (!slide.description || slide.description.trim() === '')) {
                    sType = 'banner';
                } else if (!slide.image && (slide.title || slide.description)) {
                    sType = 'announcement';
                } else {
                    sType = 'photo_text';
                }
            }

            const dSlide = document.createElement('div');
            dSlide.className = 'hero-slide-item hero-slide-desktop';
            dSlide.setAttribute('data-slide-role', 'banner');
            dSlide.setAttribute('data-slide-id', slide.id || `slide-${sIdx + 1}`);
            dSlide.setAttribute('data-slide-type', sType);

            const resolvedUrl = slide.image
                ? (slide.image.startsWith('http') || slide.image.startsWith('/') ? slide.image : `/storage/${slide.image.replace(/^\/+/, '')}`)
                : '/images/hero_dokumentasi_diskominfo.png';

            const badgeHtml = slide.badge ? `<span class="hero-badge-doc">${escapeText(slide.badge)}</span>` : '';
            const ctaHtml = slide.cta_text ? `<div class="hero-doc-cta-wrap"><a href="${escapeText(slide.cta_url || '#kuota')}" class="btn-hero-cta btn-hero-doc-cta">${escapeText(slide.cta_text)}</a></div>` : '';

            if (sType === 'banner') {
                const bannerInner = slide.cta_url
                    ? `<a href="${escapeText(slide.cta_url)}" class="hero-banner-link"><img src="${resolvedUrl}" alt="${escapeText(slide.title || 'Banner Slide')}" class="hero-full-img" loading="lazy"></a>`
                    : `<img src="${resolvedUrl}" alt="${escapeText(slide.title || 'Banner Slide')}" class="hero-full-img" loading="lazy">`;

                dSlide.innerHTML = `
                    <div class="hero-full-image-slide hero-slide-pure-banner" id="heroFullImageSlide_${sIdx}">
                        ${bannerInner}
                    </div>
                `;
            } else if (sType === 'announcement') {
                dSlide.innerHTML = `
                    <div class="hero-slide-announcement-desktop" id="heroFullImageSlide_${sIdx}">
                        <div class="hero-announcement-content">
                            ${badgeHtml}
                            <h2 class="hero-doc-title">${escapeText(slide.title || '')}</h2>
                            ${slide.description ? `<p class="hero-doc-desc">${escapeText(slide.description)}</p>` : ''}
                            ${ctaHtml}
                        </div>
                    </div>
                `;
            } else {
                dSlide.innerHTML = `
                    <div class="hero-full-image-slide" id="heroFullImageSlide_${sIdx}">
                        <img src="${resolvedUrl}" alt="${escapeText(slide.title || '')}" class="hero-full-img" loading="lazy">
                        <div class="hero-slide-scrim" aria-hidden="true"></div>
                        <div class="hero-slide-caption-desktop">
                            ${badgeHtml}
                            <h2 class="hero-doc-title">${escapeText(slide.title || '')}</h2>
                            ${slide.description ? `<p class="hero-doc-desc">${escapeText(slide.description)}</p>` : ''}
                            ${ctaHtml}
                        </div>
                    </div>
                `;
            }
            track.appendChild(dSlide);
        });

        // Tambahkan Mobile Slides
        slides.forEach((slide, sIdx) => {
            let sType = slide.type;
            if (!sType) {
                if (slide.image && (!slide.title || slide.title.trim() === '') && (!slide.description || slide.description.trim() === '')) {
                    sType = 'banner';
                } else if (!slide.image && (slide.title || slide.description)) {
                    sType = 'announcement';
                } else {
                    sType = 'photo_text';
                }
            }

            const mSlide = document.createElement('div');
            mSlide.className = 'hero-slide-item hero-slide-mobile';
            mSlide.setAttribute('data-slide-role', 'banner');
            mSlide.setAttribute('data-slide-id', slide.id || `slide-${sIdx + 1}`);
            mSlide.setAttribute('data-slide-type', sType);

            const resolvedUrl = slide.image
                ? (slide.image.startsWith('http') || slide.image.startsWith('/') ? slide.image : `/storage/${slide.image.replace(/^\/+/, '')}`)
                : '/images/hero_dokumentasi_diskominfo.png';

            const badgeHtml = slide.badge ? `<span class="hero-badge-doc">${escapeText(slide.badge)}</span>` : '';

            if (sType === 'banner') {
                const mobileBannerInner = slide.cta_url
                    ? `<a href="${escapeText(slide.cta_url)}" class="hero-mobile-photo-container hero-mobile-banner-link"><img src="${resolvedUrl}" alt="${escapeText(slide.title || 'Banner Slide')}" class="hero-mobile-img" loading="lazy"></a>`
                    : `<div class="hero-mobile-photo-container"><img src="${resolvedUrl}" alt="${escapeText(slide.title || 'Banner Slide')}" class="hero-mobile-img" loading="lazy"></div>`;

                mSlide.innerHTML = `
                    <div class="hero-mobile-card hero-mobile-card-photo hero-mobile-card-pure-banner">
                        ${mobileBannerInner}
                    </div>
                `;
            } else if (sType === 'announcement') {
                mSlide.innerHTML = `
                    <div class="hero-mobile-card hero-mobile-card-announcement">
                        <div class="hero-mobile-announcement-body">
                            ${badgeHtml}
                            <h3 class="hero-mobile-photo-title">${escapeText(slide.title || '')}</h3>
                            ${slide.description ? `<p class="hero-mobile-photo-desc">${escapeText(slide.description)}</p>` : ''}
                            ${slide.cta_text ? `<div class="hero-mobile-cta-wrap-doc"><a href="${escapeText(slide.cta_url || '#kuota')}" class="btn-hero-cta btn-hero-doc-cta">${escapeText(slide.cta_text)}</a></div>` : ''}
                        </div>
                    </div>
                `;
            } else {
                mSlide.innerHTML = `
                    <div class="hero-mobile-card hero-mobile-card-photo">
                        <div class="hero-mobile-photo-container">
                            <img src="${resolvedUrl}" alt="${escapeText(slide.title || '')}" class="hero-mobile-img" loading="lazy">
                        </div>
                        <div class="hero-mobile-photo-caption">
                            ${badgeHtml}
                            <h3 class="hero-mobile-photo-title">${escapeText(slide.title || '')}</h3>
                            ${slide.description ? `<p class="hero-mobile-photo-desc">${escapeText(slide.description)}</p>` : ''}
                            ${slide.cta_text ? `<div class="hero-mobile-cta-wrap-doc"><a href="${escapeText(slide.cta_url || '#kuota')}" class="btn-hero-cta btn-hero-doc-cta">${escapeText(slide.cta_text)}</a></div>` : ''}
                        </div>
                    </div>
                `;
            }
            track.appendChild(mSlide);
        });

        // Update Dots Desktop
        const desktopDots = document.getElementById('heroSliderDotsDesktop');
        if (desktopDots) {
            let dotsHtml = '<button type="button" class="hero-dot-btn active" data-slide-index="0" aria-label="Ke slide 1 (Informasi Pendaftaran)"></button>';
            slides.forEach((s, idx) => {
                dotsHtml += `<button type="button" class="hero-dot-btn" data-slide-index="${idx + 1}" aria-label="Ke slide ${idx + 2} (${escapeText(s.title || '')})"></button>`;
            });
            desktopDots.innerHTML = dotsHtml;
        }

        // Update Dots Mobile
        const mobileDots = document.getElementById('heroSliderDotsMobile');
        if (mobileDots) {
            let dotsHtml = `
                <button type="button" class="hero-dot-btn active" data-slide-index="0" aria-label="Ke slide 1 (Pendaftaran)"></button>
                <button type="button" class="hero-dot-btn" data-slide-index="1" aria-label="Ke slide 2 (Persyaratan)"></button>
                <button type="button" class="hero-dot-btn" data-slide-index="2" aria-label="Ke slide 3 (Dokumen)"></button>
            `;
            slides.forEach((s, idx) => {
                dotsHtml += `<button type="button" class="hero-dot-btn" data-slide-index="${idx + 3}" aria-label="Ke slide ${idx + 4} (${escapeText(s.title || '')})"></button>`;
            });
            mobileDots.innerHTML = dotsHtml;
        }

        // Hubungkan kembali interaksi kontrol slider di kanvas
        initHeroSliderControls();
    }

    let currentHeroCanvasSlide = 0;

    function getHeroCanvasTotalSlides() {
        const track = document.getElementById('heroSliderTrack');
        if (!track) return 0;
        const isMobile = window.innerWidth < 768;
        const slides = track.querySelectorAll(isMobile ? '.hero-slide-mobile' : '.hero-slide-desktop');
        return slides.length;
    }

    function setHeroCanvasSlide(targetIndex) {
        const track = document.getElementById('heroSliderTrack');
        if (!track) return;
        const total = getHeroCanvasTotalSlides();
        if (total <= 1) return;

        if (targetIndex < 0) {
            currentHeroCanvasSlide = total - 1;
        } else if (targetIndex >= total) {
            currentHeroCanvasSlide = 0;
        } else {
            currentHeroCanvasSlide = targetIndex;
        }

        track.style.transform = `translateX(-${currentHeroCanvasSlide * 100}%)`;

        const isMobile = window.innerWidth < 768;
        const dotsContainer = isMobile
            ? document.getElementById('heroSliderDotsMobile')
            : document.getElementById('heroSliderDotsDesktop');

        if (dotsContainer) {
            dotsContainer.querySelectorAll('.hero-dot-btn').forEach(btn => {
                const bIdx = parseInt(btn.getAttribute('data-slide-index'), 10);
                if (bIdx === currentHeroCanvasSlide) {
                    btn.classList.add('active');
                } else {
                    btn.classList.remove('active');
                }
            });
        }

        const bannerOffset = isMobile ? 3 : 1;
        if (currentHeroCanvasSlide >= bannerOffset) {
            const bannerIdx = currentHeroCanvasSlide - bannerOffset;
            window.parent.postMessage({
                type: 'WP_HERO_SLIDE_ACTIVATED',
                slideIndex: bannerIdx,
            }, PARENT_ORIGIN);
        }
    }

    function initHeroSliderControls() {
        const btnPrev = document.getElementById('btnHeroSidePrev') || document.getElementById('btnHeroPrev');
        const btnNext = document.getElementById('btnHeroSideNext') || document.getElementById('btnHeroNext');

        if (btnPrev && !btnPrev.__canvasBound) {
            btnPrev.__canvasBound = true;
            btnPrev.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                setHeroCanvasSlide(currentHeroCanvasSlide - 1);
            });
        }

        if (btnNext && !btnNext.__canvasBound) {
            btnNext.__canvasBound = true;
            btnNext.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                setHeroCanvasSlide(currentHeroCanvasSlide + 1);
            });
        }

        document.querySelectorAll('.hero-slider-dots .hero-dot-btn').forEach(btn => {
            if (!btn.__canvasBound) {
                btn.__canvasBound = true;
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    const targetIdx = parseInt(btn.getAttribute('data-slide-index'), 10);
                    if (!isNaN(targetIdx)) {
                        setHeroCanvasSlide(targetIdx);
                    }
                });
            }
        });
    }

    function navigateHeroSlider(heroSlideIndex) {
        if (typeof heroSlideIndex !== 'number' || isNaN(heroSlideIndex)) return;
        const isMobile = window.innerWidth < 768;
        const targetTrackIdx = isMobile ? heroSlideIndex + 3 : heroSlideIndex + 1;
        setHeroCanvasSlide(targetTrackIdx);
    }

    function updateCanvasFaq(faqCategories) {
        const stackContainer = document.getElementById('faqCategoryAccordion');
        if (!stackContainer || !Array.isArray(faqCategories)) return;

        const escapeText = (t) => {
            const d = document.createElement('div');
            d.textContent = t || '';
            return d.innerHTML;
        };

        let html = '';
        faqCategories.forEach((cat, cIdx) => {
            const catId = cat.id || `cat-${cIdx + 1}`;
            const catName = cat.nama || `Kategori ${cIdx + 1}`;
            const isFirst = cIdx === 0;

            let itemsHtml = '';
            (cat.items || []).forEach(item => {
                itemsHtml += `
                    <div class="faq-sub-qa-item">
                        <button type="button" class="faq-sub-question-btn" aria-expanded="false">
                            <span>${escapeText(item.q || '')}</span>
                            <svg class="faq-sub-qa-toggle-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                        </button>
                        <div class="faq-sub-answer-pane" style="display: none;">
                            ${escapeText(item.a || '')}
                        </div>
                    </div>
                `;
            });

            html += `
                <div class="faq-category-item ${isFirst ? 'is-expanded' : ''}" data-category-id="${escapeText(catId)}">
                    <button type="button" class="faq-category-header-btn" aria-expanded="${isFirst ? 'true' : 'false'}" aria-controls="faq-drawer-${escapeText(catId)}">
                        <span>${escapeText(catName)}</span>
                        <svg class="faq-category-icon-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </button>
                    <div id="faq-drawer-${escapeText(catId)}" class="faq-questions-drawer" role="region" style="${isFirst ? 'display: block;' : 'display: none;'}">
                        ${itemsHtml}
                    </div>
                </div>
            `;
        });

        stackContainer.innerHTML = html;

        // Pasang interaksi klik accordion kanvas
        const categoryItems = stackContainer.querySelectorAll('.faq-category-item');
        categoryItems.forEach(function (catItem) {
            const headerBtn = catItem.querySelector('.faq-category-header-btn');
            const drawer = catItem.querySelector('.faq-questions-drawer');
            const subQaItems = catItem.querySelectorAll('.faq-sub-qa-item');

            if (headerBtn && drawer) {
                headerBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    const isCurrentlyExpanded = catItem.classList.contains('is-expanded');

                    categoryItems.forEach(function (otherItem) {
                        if (otherItem !== catItem) {
                            otherItem.classList.remove('is-expanded');
                            const otherBtn = otherItem.querySelector('.faq-category-header-btn');
                            const otherDrawer = otherItem.querySelector('.faq-questions-drawer');
                            if (otherBtn) otherBtn.setAttribute('aria-expanded', 'false');
                            if (otherDrawer) otherDrawer.style.display = 'none';
                        }
                    });

                    if (isCurrentlyExpanded) {
                        catItem.classList.remove('is-expanded');
                        headerBtn.setAttribute('aria-expanded', 'false');
                        drawer.style.display = 'none';
                    } else {
                        catItem.classList.add('is-expanded');
                        headerBtn.setAttribute('aria-expanded', 'true');
                        drawer.style.display = 'block';
                    }
                });
            }

            subQaItems.forEach(function (qaItem) {
                const qBtn = qaItem.querySelector('.faq-sub-question-btn');
                const aPane = qaItem.querySelector('.faq-sub-answer-pane');

                if (qBtn && aPane) {
                    qBtn.addEventListener('click', function (e) {
                        e.preventDefault();
                        e.stopPropagation();

                        const isCurrentlyOpen = qaItem.classList.contains('is-open');

                        subQaItems.forEach(function (otherQa) {
                            if (otherQa !== qaItem) {
                                otherQa.classList.remove('is-open');
                                const otherBtn = otherQa.querySelector('.faq-sub-question-btn');
                                const otherPane = otherQa.querySelector('.faq-sub-answer-pane');
                                if (otherBtn) otherBtn.setAttribute('aria-expanded', 'false');
                                if (otherPane) otherPane.style.display = 'none';
                            }
                        });

                        if (isCurrentlyOpen) {
                            qaItem.classList.remove('is-open');
                            qBtn.setAttribute('aria-expanded', 'false');
                            aPane.style.display = 'none';
                        } else {
                            qaItem.classList.add('is-open');
                            qBtn.setAttribute('aria-expanded', 'true');
                            aPane.style.display = 'block';
                        }
                    });
                }
            });
        });
    }

    function updateCanvasTahapan(tahapanData) {
        if (!tahapanData || typeof tahapanData !== 'object') return;

        const escapeText = (t) => {
            const d = document.createElement('div');
            d.textContent = t || '';
            return d.innerHTML;
        };

        // 1. Update Heading
        if (tahapanData.heading !== undefined) {
            const headingEl = document.querySelector('.tahapan-main-heading');
            if (headingEl) {
                const raw = (tahapanData.heading || '').trim();
                if (raw.includes('Tahapan Pendaftaran Magang')) {
                    headingEl.innerHTML = escapeText(raw).replace('Tahapan Pendaftaran Magang', '<strong>Tahapan Pendaftaran Magang</strong>');
                } else {
                    headingEl.textContent = raw;
                }
            }
        }

        // 2. Update Summary Lines
        if (tahapanData.summary !== undefined) {
            const summaryContainer = document.querySelector('.tahapan-summary-list');
            if (summaryContainer) {
                const lines = (tahapanData.summary || '')
                    .replace(/\r/g, '')
                    .split('\n')
                    .map(l => l.trim())
                    .filter(l => l.length > 0);

                let summaryHtml = '';
                lines.forEach(line => {
                    const parts = line.split(' ');
                    const first = parts[0] || '';
                    const rest = parts.slice(1).join(' ');
                    summaryHtml += `<span class="tahapan-summary-item"><strong>${escapeText(first)}</strong> ${escapeText(rest)}</span>`;
                });
                summaryContainer.innerHTML = summaryHtml;
            }
        }

        // 3. Update 4 Milestone Step Cards
        if (Array.isArray(tahapanData.steps)) {
            const cards = document.querySelectorAll('.tahapan-cards-list .tahapan-card-item');
            tahapanData.steps.forEach((step, idx) => {
                if (cards[idx]) {
                    const titleEl = cards[idx].querySelector('.tahapan-card-title');
                    const descEl = cards[idx].querySelector('.tahapan-card-desc');
                    if (titleEl && step.title !== undefined) {
                        titleEl.textContent = step.title;
                    }
                    if (descEl && step.desc !== undefined) {
                        descEl.textContent = step.desc;
                    }
                }

                // Update SVG checkpoint aria-labels
                const dCheckpoint = document.querySelector(`#tahapanSvgDesktop [data-checkpoint-index="${idx}"]`);
                if (dCheckpoint) {
                    dCheckpoint.setAttribute('aria-label', `Tahap ${idx + 1} ${step.title || ''}`);
                }
                const mCheckpoint = document.querySelector(`#tahapanSvgMobile [data-checkpoint-index="${idx}"]`);
                if (mCheckpoint) {
                    mCheckpoint.setAttribute('aria-label', `Tahap ${idx + 1} ${step.title || ''}`);
                }
            });
        }
    }
})();
