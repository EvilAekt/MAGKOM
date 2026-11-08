/**
 * WordPress Site Inspector - Logika Kontrol Panel Kanan
 * Mengelola Dynamic Block Forms, Scoped Responsive Styling, Advanced Settings, Global Styles, dan Presets
 */

export class Inspector {
    constructor(canvasBridge, initialResponsiveStyles = {}, initialGlobalStyles = {}, presets = {}, onStateDirty) {
        this.bridge = canvasBridge;
        this.blockManager = null;
        this.onStateDirty = onStateDirty;

        this.activeBlock = 'hero';
        this.activeBlockId = 'block-hero-1';
        this.activeStyleDevice = 'desktop'; // 'desktop' | 'tablet' | 'mobile'

        this.state = {};
        this.responsiveStyles = {
            desktop: { ...(initialResponsiveStyles.desktop || {}) },
            tablet: { ...(initialResponsiveStyles.tablet || {}) },
            mobile: { ...(initialResponsiveStyles.mobile || {}) },
        };

        this.presets = presets || {};
        this.globalStyles = this.normalizeGlobalStyles(initialGlobalStyles);

        this.mediaLibrary = null;

        this.initMainTabs();
        this.initSubtabs();
        this.initResponsiveStyleTabs();
        this.initGlobalStyleControls();
        this.initPresets();
        this.initScopedStyleControls();
        this.initAdvancedSettingsControls();
        this.initContentInputs();
    }

    normalizeGlobalStyles(initial) {
        const defaults = {
            colors: {
                primary: '#1B3860',
                primary_hover: '#122846',
                secondary: '#F1F5F9',
                accent: '#4F46E5',
                background: '#FFFFFF',
                card: '#FFFFFF',
                text: '#0F172A',
                text_muted: '#64748B',
                border: '#E2E8F0',
                success: '#F59E0B',
            },
            typography: {
                heading_font: 'Lexend',
                body_font: 'Source Sans 3',
                base_size_px: 16,
                line_height: 1.5,
            },
            layout: {
                container_max_width_px: 1280,
                section_gap_px: 64,
                card_radius_px: 12,
                card_shadow: 'soft',
            },
            buttons: {
                radius_px: 8,
                padding_y_px: 10,
                padding_x_px: 20,
                primary_bg: '#1B3860',
                primary_text: '#FFFFFF',
            },
        };

        return {
            colors: { ...defaults.colors, ...(initial?.colors || {}) },
            typography: { ...defaults.typography, ...(initial?.typography || {}) },
            layout: { ...defaults.layout, ...(initial?.layout || {}) },
            buttons: { ...defaults.buttons, ...(initial?.buttons || {}) },
        };
    }

    setBlockManager(blockManager) {
        this.blockManager = blockManager;
    }

    setMediaLibrary(mediaLibrary) {
        this.mediaLibrary = mediaLibrary;
        document.getElementById('btn-change-hero-image')?.addEventListener('click', () => {
            if (this.mediaLibrary) {
                this.mediaLibrary.open('image', (selectedMedia) => {
                    this.setHeroImageFromMedia(selectedMedia);
                });
            }
        });

        const handlePdfPick = () => {
            if (this.mediaLibrary) {
                this.mediaLibrary.open('pdf', (selectedPdf) => {
                    this.setPanduanPdfFromMedia(selectedPdf);
                });
            }
        };

        document.getElementById('btn-change-panduan-pdf')?.addEventListener('click', handlePdfPick);
        document.getElementById('btn-choose-pdf-media')?.addEventListener('click', handlePdfPick);
        document.getElementById('btn-page-choose-pdf')?.addEventListener('click', handlePdfPick);

        const handlePdfReset = async () => {
            if (confirm('Apakah Anda yakin ingin mereset berkas panduan PDF kembali ke generator otomatis bawaan sistem?')) {
                await this.resetPanduanPdf();
            }
        };

        document.getElementById('btn-reset-panduan-pdf')?.addEventListener('click', handlePdfReset);
        document.getElementById('btn-page-reset-pdf')?.addEventListener('click', handlePdfReset);
    }

    initMainTabs() {
        const tabButtons = document.querySelectorAll('.wp-tab-btn');
        tabButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                tabButtons.forEach(b => {
                    b.classList.remove('is-active');
                    b.setAttribute('aria-selected', 'false');
                });
                btn.classList.add('is-active');
                btn.setAttribute('aria-selected', 'true');

                const targetTab = btn.getAttribute('data-tab');
                document.querySelectorAll('.wp-tab-pane').forEach(pane => pane.style.display = 'none');
                
                const activePane = document.getElementById(`wp-tab-content-${targetTab}`);
                if (activePane) {
                    activePane.style.display = 'block';
                }
            });
        });
    }

    initSubtabs() {
        const subtabBtns = document.querySelectorAll('.wp-subtab-btn');
        subtabBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                subtabBtns.forEach(b => b.classList.remove('is-active'));
                btn.classList.add('is-active');

                const target = btn.getAttribute('data-subtab');
                document.querySelectorAll('.wp-subpane').forEach(pane => pane.style.display = 'none');

                const targetPane = document.getElementById(`wp-subpane-${target}`);
                if (targetPane) {
                    targetPane.style.display = 'block';
                }
            });
        });
    }

    initResponsiveStyleTabs() {
        const deviceBtns = document.querySelectorAll('.wp-style-device-btn');
        deviceBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                deviceBtns.forEach(b => b.classList.remove('is-active'));
                btn.classList.add('is-active');

                this.activeStyleDevice = btn.getAttribute('data-device-style') || 'desktop';
                this.syncScopedControlsForActiveBlock();
            });
        });
    }

    // =========================================================================
    // TASK 3 - TASK A: DYNAMIC BLOCK INSPECTOR (CONTENT, STYLE, ADVANCED)
    // =========================================================================

    /**
     * Set aktifkan blok dari kanvas atau list view ke inspector
     */
    setActiveBlock(blockName, blockTitle, blockId = null) {
        this.activeBlock = blockName;
        this.activeBlockId = blockId || `block-${blockName}`;

        const titleEl = document.getElementById('wp-active-block-title');
        const descEl = document.getElementById('wp-active-block-subtitle');
        const iconEl = document.getElementById('wp-active-block-icon');

        if (titleEl) {
            titleEl.textContent = blockTitle || blockName.toUpperCase();
        }

        const blockInfo = {
            hero: { desc: 'Section utama dengan background gambar dan konten utama.', icon: '🔷' },
            informasi: { desc: 'Persyaratan umum magang dan dokumen administrasi wajib.', icon: '📋' },
            kuota: { desc: 'Tabel ketersediaan kuota jurusan dan formasi magang aktif.', icon: '📊' },
            tahapan: { desc: 'Pohon 4 checkpoint alur seleksi pendaftaran dan kelulusan.', icon: '🔄' },
            faq: { desc: 'Daftar tanya jawab akordeon terstruktur per kategori.', icon: '❓' },
            footer: { desc: 'Alamat kantor dinas, jam layanan, medsos, dan hak cipta.', icon: '🏢' },
            cta_banner: { desc: 'Kotak banner promosi ajakan mendaftar magang.', icon: '📢' },
            features: { desc: '3 kartu sorot keunggulan program magang kominfo.', icon: '⭐' },
            spacer: { desc: 'Pemisah jarak kosong vertikal antar section.', icon: '↕️' },
            kebijakan: { desc: 'Kebijakan privasi dan perlindungan data UU PDP No. 27/2022.', icon: '🔒' },
        };

        const info = blockInfo[blockName] || { desc: 'Bagian konten landing page.', icon: '🔷' };
        if (descEl) descEl.textContent = info.desc;
        if (iconEl) iconEl.textContent = info.icon;

        // Tampilkan hanya form akordeon yang sesuai di subpane General
        document.querySelectorAll('#wp-subpane-general .wp-accordion').forEach(acc => {
            const isMatch = acc.classList.contains(`block-form-${blockName}`);
            acc.style.display = isMatch ? 'block' : 'none';
            if (isMatch) acc.open = true;
        });

        // Beralih ke tab Block jika sedang di tab Page
        const blockTabBtn = document.querySelector('.wp-tab-btn[data-tab="block"]');
        if (blockTabBtn && !blockTabBtn.classList.contains('is-active')) {
            blockTabBtn.click();
        }

        // Sinkronisasi data konten blok jika ada di BlockManager
        this.syncContentInputsForActiveBlock();

        // Sinkronisasi scoped styles ke kontrol style
        this.syncScopedControlsForActiveBlock();

        // Sinkronisasi advanced settings
        this.syncAdvancedControlsForActiveBlock();
    }

    getActiveBlockData() {
        if (!this.blockManager) return null;
        return this.blockManager.layout.find(b => b.id === this.activeBlockId || b.type === this.activeBlockId);
    }

    syncContentInputsForActiveBlock() {
        const block = this.getActiveBlockData();
        if (!block) return;

        const content = block.content || {};
        const generalPane = document.getElementById('wp-subpane-general');
        if (!generalPane) return;

        // Cari input khusus block field di dalam accordion blok terkait
        const activeAccordion = generalPane.querySelector(`.block-form-${block.type}`);
        if (!activeAccordion) return;

        activeAccordion.querySelectorAll('[data-block-field]').forEach(input => {
            const fieldKey = input.getAttribute('data-block-field');
            if (content[fieldKey] !== undefined) {
                input.value = content[fieldKey];
            }
        });
    }

    syncScopedControlsForActiveBlock() {
        const block = this.getActiveBlockData();
        const dev = this.activeStyleDevice;
        const styles = block ? (block.styles || {}) : {};
        const devStyles = styles[dev] || {};

        // 1. Scoped Sliders (padding_y, margin_y, font_size, card_radius, height)
        document.querySelectorAll('.wp-scoped-slider').forEach(slider => {
            const prop = slider.getAttribute('data-block-style');
            const unit = slider.getAttribute('data-unit') || 'px';

            let val = devStyles[prop];
            // Inheritance: jika tablet belum diset, fallback ke desktop
            if (val === undefined && dev !== 'desktop') {
                val = (styles.desktop && styles.desktop[prop] !== undefined) ? styles.desktop[prop] : styles[prop];
            }
            if (val === undefined) {
                val = styles[prop] !== undefined ? styles[prop] : slider.value;
            }

            slider.value = val;
            const badge = slider.closest('.wp-field-group')?.querySelector('.wp-pixel-badge');
            if (badge) {
                badge.textContent = `${val}${unit}`;
            }
        });

        // 2. Alignment Buttons
        const alignVal = devStyles.text_align || (styles.desktop?.text_align || styles.text_align || 'left');
        document.querySelectorAll('.wp-scoped-align-btn').forEach(btn => {
            btn.classList.toggle('is-active', btn.getAttribute('data-align') === alignVal);
        });

        // 3. Scoped Color Overrides
        const bgVal = devStyles.bg_color || styles.bg_color || '';
        const bgPicker = document.getElementById('color-block-bg');
        const bgHex = document.getElementById('hex-block-bg');
        if (bgPicker && bgHex) {
            bgPicker.value = bgVal || '#ffffff';
            bgHex.textContent = bgVal ? bgVal.toUpperCase() : 'Inherit';
        }

        const textVal = devStyles.text_color || styles.text_color || '';
        const textPicker = document.getElementById('color-block-text');
        const textHex = document.getElementById('hex-block-text');
        if (textPicker && textHex) {
            textPicker.value = textVal || '#0f172a';
            textHex.textContent = textVal ? textVal.toUpperCase() : 'Inherit';
        }
    }

    syncAdvancedControlsForActiveBlock() {
        const block = this.getActiveBlockData();
        if (!block) return;

        // ID Badge
        const idBadge = document.getElementById('display-block-id');
        if (idBadge) {
            idBadge.textContent = block.id;
        }

        const settings = block.settings || {};

        // HTML Anchor
        const anchorInput = document.getElementById('input-block-html-anchor');
        if (anchorInput) {
            anchorInput.value = settings.html_anchor || '';
        }

        // Custom CSS Class
        const classInput = document.getElementById('input-block-css-class');
        if (classInput) {
            classInput.value = settings.custom_css_class || '';
        }

        // Responsive Visibility Checkboxes
        const visDesktop = document.getElementById('vis-desktop');
        const visTablet = document.getElementById('vis-tablet');
        const visMobile = document.getElementById('vis-mobile');
        if (visDesktop) visDesktop.checked = settings.visible_desktop !== false;
        if (visTablet) visTablet.checked = settings.visible_tablet !== false;
        if (visMobile) visMobile.checked = settings.visible_mobile !== false;

        // Lock Badge & Button
        const lockBadge = document.getElementById('display-block-lock-badge');
        const lockBtn = document.getElementById('btn-toggle-block-lock');
        const isSystemLocked = Boolean(block.locked);
        const isUserLocked = Boolean(block.user_locked);

        if (lockBadge) {
            if (isSystemLocked) {
                lockBadge.className = 'wp-listview-badge wp-badge-system-locked';
                lockBadge.textContent = '🔒 Wajib Sistem';
            } else if (isUserLocked) {
                lockBadge.className = 'wp-listview-badge wp-badge-user-locked';
                lockBadge.textContent = '🔒 Terkunci (Admin)';
            } else {
                lockBadge.className = 'wp-listview-badge wp-badge-unlocked';
                lockBadge.textContent = '🔓 Dapat Dimodifikasi';
            }
        }

        if (lockBtn) {
            lockBtn.disabled = isSystemLocked;
            lockBtn.textContent = isUserLocked ? 'Buka Kunci' : 'Kunci Blok';
        }
    }

    // =========================================================================
    // TASK 3 - TASK C & D: SCOPED STYLES & RESPONSIVE BREAKPOINT CONTROLS
    // =========================================================================

    initScopedStyleControls() {
        // 1. Scoped Sliders
        document.querySelectorAll('.wp-scoped-slider').forEach(slider => {
            const prop = slider.getAttribute('data-block-style');
            const unit = slider.getAttribute('data-unit') || 'px';

            slider.addEventListener('input', (e) => {
                const val = parseInt(e.target.value, 10);
                const badge = slider.closest('.wp-field-group')?.querySelector('.wp-pixel-badge');
                if (badge) {
                    badge.textContent = `${val}${unit}`;
                }

                this.setScopedBlockStyle(prop, val);
            });
        });

        // 2. Alignment Toggles
        document.querySelectorAll('.wp-scoped-align-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.wp-scoped-align-btn').forEach(b => b.classList.remove('is-active'));
                btn.classList.add('is-active');

                const align = btn.getAttribute('data-align');
                this.setScopedBlockStyle('text_align', align);
            });
        });

        // 3. Scoped Colors
        const bgPicker = document.getElementById('color-block-bg');
        bgPicker?.addEventListener('input', (e) => {
            const val = e.target.value.toUpperCase();
            const hexEl = document.getElementById('hex-block-bg');
            if (hexEl) hexEl.textContent = val;
            this.setScopedBlockStyle('bg_color', val);
        });

        document.getElementById('btn-reset-block-bg')?.addEventListener('click', () => {
            const hexEl = document.getElementById('hex-block-bg');
            if (hexEl) hexEl.textContent = 'Inherit';
            this.setScopedBlockStyle('bg_color', null);
        });

        const textPicker = document.getElementById('color-block-text');
        textPicker?.addEventListener('input', (e) => {
            const val = e.target.value.toUpperCase();
            const hexEl = document.getElementById('hex-block-text');
            if (hexEl) hexEl.textContent = val;
            this.setScopedBlockStyle('text_color', val);
        });

        document.getElementById('btn-reset-block-text')?.addEventListener('click', () => {
            const hexEl = document.getElementById('hex-block-text');
            if (hexEl) hexEl.textContent = 'Inherit';
            this.setScopedBlockStyle('text_color', null);
        });
    }

    setScopedBlockStyle(property, value) {
        const block = this.getActiveBlockData();
        if (!block) return;

        if (!block.styles) {
            block.styles = {};
        }

        const dev = this.activeStyleDevice;
        if (!block.styles[dev]) {
            block.styles[dev] = {};
        }

        if (value === null || value === undefined) {
            delete block.styles[dev][property];
            if (dev === 'desktop') {
                delete block.styles[property];
            }
        } else {
            block.styles[dev][property] = value;
            if (dev === 'desktop') {
                block.styles[property] = value;
            }
        }

        // Live preview ke iframe canvas via Bridge
        if (this.bridge) {
            this.bridge.updateBlockStyle(block.id, property, value, dev);
        }

        this.notifyDirty();
    }

    // =========================================================================
    // TASK 3 - TASK A (ADVANCED): ADVANCED SETTINGS & SECURITY VALIDATIONS
    // =========================================================================

    initAdvancedSettingsControls() {
        // Copy Block ID
        document.getElementById('btn-copy-block-id')?.addEventListener('click', () => {
            if (this.activeBlockId) {
                navigator.clipboard.writeText(this.activeBlockId).then(() => {
                    const btn = document.getElementById('btn-copy-block-id');
                    if (btn) {
                        const origText = btn.textContent;
                        btn.textContent = 'Tersalin!';
                        setTimeout(() => { btn.textContent = origText; }, 1500);
                    }
                }).catch(() => {});
            }
        });

        // HTML Anchor with regex validation ^[a-zA-Z0-9_\-]*$
        const anchorInput = document.getElementById('input-block-html-anchor');
        anchorInput?.addEventListener('input', (e) => {
            const raw = e.target.value;
            const sanitized = raw.replace(/[^a-zA-Z0-9_\-]/g, '');
            if (raw !== sanitized) {
                e.target.value = sanitized;
            }
            this.setBlockSetting('html_anchor', sanitized);
        });

        // Custom CSS Class with regex validation ^[a-zA-Z0-9_\-\s]*$
        const classInput = document.getElementById('input-block-css-class');
        classInput?.addEventListener('input', (e) => {
            const raw = e.target.value;
            const sanitized = raw.replace(/[^a-zA-Z0-9_\-\s]/g, '');
            if (raw !== sanitized) {
                e.target.value = sanitized;
            }
            this.setBlockSetting('custom_css_class', sanitized);
        });

        // Responsive Visibility Checkboxes
        ['desktop', 'tablet', 'mobile'].forEach(dev => {
            const cb = document.getElementById(`vis-${dev}`);
            cb?.addEventListener('change', (e) => {
                this.setBlockSetting(`visible_${dev}`, e.target.checked);
            });
        });

        // Lock Toggle Button in Inspector
        document.getElementById('btn-toggle-block-lock')?.addEventListener('click', () => {
            if (this.blockManager) {
                this.blockManager.toggleLock(this.activeBlockId);
                this.syncAdvancedControlsForActiveBlock();
                this.notifyDirty();
            }
        });

        // Reset Block Styles Button
        document.getElementById('btn-reset-block-styles')?.addEventListener('click', () => {
            if (confirm(`Kembalikan seluruh pengaturan gaya blok ini ke bawaan sistem?`)) {
                this.resetActiveBlockStyles();
            }
        });
    }

    setBlockSetting(key, value) {
        const block = this.getActiveBlockData();
        if (!block) return;

        if (!block.settings) {
            block.settings = {};
        }

        block.settings[key] = value;

        if (this.bridge) {
            this.bridge.setBlockSettings(block.id, block.settings);
        }

        this.notifyDirty();
    }

    resetActiveBlockStyles() {
        const block = this.getActiveBlockData();
        if (!block) return;

        block.styles = {};

        if (this.bridge) {
            this.bridge.resetStyles(block.id);
        }

        this.syncScopedControlsForActiveBlock();
        this.notifyDirty();
    }

    // =========================================================================
    // TASK 3 - TASK B & E: GLOBAL STYLES & PRESETS ENGINE
    // =========================================================================

    initGlobalStyleControls() {
        // 1. Global Colors
        document.querySelectorAll('[data-global-color]').forEach(picker => {
            const key = picker.getAttribute('data-global-color');
            const prop = picker.getAttribute('data-property');
            const hexEl = picker.nextElementSibling;

            // Set initial picker value from this.globalStyles.colors
            if (this.globalStyles.colors[key]) {
                picker.value = this.globalStyles.colors[key];
                if (hexEl) hexEl.textContent = this.globalStyles.colors[key];
            }

            picker.addEventListener('input', (e) => {
                const val = e.target.value.toUpperCase();
                if (hexEl) hexEl.textContent = val;

                this.globalStyles.colors[key] = val;

                if (prop && this.bridge) {
                    this.bridge.updateStyle(prop, val);
                }

                // Sync to legacy keys if applicable
                if (key === 'primary') this.state['landing_theme_primary_color'] = val;
                if (key === 'primary_hover') this.state['landing_theme_navy_dark'] = val;
                if (key === 'accent') this.state['landing_theme_accent_color'] = val;
                if (key === 'background') this.state['landing_theme_bg_color'] = val;
                if (key === 'success') this.state['landing_theme_gold_color'] = val;

                this.notifyDirty();
            });
        });

        // 2. Global Typography
        document.querySelectorAll('[data-global-typography]').forEach(input => {
            const key = input.getAttribute('data-global-typography');
            const prop = input.getAttribute('data-property');
            const unit = input.getAttribute('data-unit') || '';

            input.addEventListener('input', (e) => {
                let val = e.target.value;
                if (key === 'base_size_px') val = parseInt(val, 10);
                if (key === 'line_height') val = parseFloat(val);

                this.globalStyles.typography[key] = val;

                const badge = input.closest('.wp-field-group')?.querySelector('.wp-pixel-badge');
                if (badge) {
                    badge.textContent = `${val}${unit}`;
                }

                if (prop && this.bridge) {
                    const formatted = (key === 'heading_font' || key === 'body_font')
                        ? `'${val}', sans-serif`
                        : `${val}${unit}`;
                    this.bridge.updateStyle(prop, formatted);
                }

                if (key === 'heading_font') this.state['landing_theme_font_display'] = val;
                if (key === 'body_font') this.state['landing_theme_font_body'] = val;

                this.notifyDirty();
            });
        });

        // 3. Global Layout
        document.querySelectorAll('[data-global-layout]').forEach(input => {
            const key = input.getAttribute('data-global-layout');
            const prop = input.getAttribute('data-property');
            const unit = input.getAttribute('data-unit') || '';

            input.addEventListener('input', (e) => {
                let val = e.target.value;
                if (key.endsWith('_px')) val = parseInt(val, 10);

                this.globalStyles.layout[key] = val;

                const badge = input.closest('.wp-field-group')?.querySelector('.wp-pixel-badge');
                if (badge) {
                    badge.textContent = `${val}${unit}`;
                }

                if (prop && this.bridge) {
                    if (key === 'card_shadow') {
                        const shadowMap = {
                            none: 'none',
                            soft: '0 4px 12px rgba(0, 0, 0, 0.06)',
                            medium: '0 8px 24px rgba(0, 0, 0, 0.09)',
                            strong: '0 16px 32px rgba(0, 0, 0, 0.12)',
                        };
                        this.bridge.updateStyle(prop, shadowMap[val] || val);
                    } else {
                        this.bridge.updateStyle(prop, `${val}${unit}`);
                    }
                }

                if (key === 'container_max_width_px') this.state['landing_container_max_width_px'] = val;
                if (key === 'section_gap_px') this.state['landing_section_gap_px'] = val;
                if (key === 'card_radius_px') this.state['landing_card_radius_px'] = val;

                this.notifyDirty();
            });
        });

        // 4. Global Buttons
        document.querySelectorAll('[data-global-button]').forEach(input => {
            const key = input.getAttribute('data-global-button');
            const prop = input.getAttribute('data-property');
            const unit = input.getAttribute('data-unit') || '';

            input.addEventListener('input', (e) => {
                let val = e.target.value;
                if (key.endsWith('_px')) val = parseInt(val, 10);

                this.globalStyles.buttons[key] = val;

                const badge = input.closest('.wp-field-group')?.querySelector('.wp-pixel-badge');
                if (badge) {
                    badge.textContent = `${val}${unit}`;
                }

                const hexEl = input.nextElementSibling;
                if (hexEl && input.type === 'color') {
                    hexEl.textContent = val.toUpperCase();
                }

                if (prop && this.bridge) {
                    this.bridge.updateStyle(prop, `${val}${unit}`);
                }

                this.notifyDirty();
            });
        });
    }

    initPresets() {
        // Color Presets
        document.querySelectorAll('.wp-preset-card[data-preset-type="color"]').forEach(card => {
            card.addEventListener('click', () => {
                const key = card.getAttribute('data-preset-key');
                this.applyColorPreset(key);
            });
        });

        // Pill Presets (Buttons, Cards, Typography)
        document.querySelectorAll('.wp-preset-pill-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const type = btn.getAttribute('data-preset-type');
                const key = btn.getAttribute('data-preset-key');

                const siblings = btn.closest('.wp-preset-btn-row')?.querySelectorAll('.wp-preset-pill-btn');
                siblings?.forEach(s => s.classList.remove('is-active'));
                btn.classList.add('is-active');

                if (type === 'button') this.applyButtonPreset(key);
                if (type === 'card') this.applyCardPreset(key);
                if (type === 'typography') this.applyTypographyPreset(key);
            });
        });
    }

    applyColorPreset(presetKey) {
        const presets = {
            magkom_official: {
                primary: '#1B3860',
                primary_hover: '#122846',
                secondary: '#F1F5F9',
                accent: '#F59E0B',
                background: '#FFFFFF',
                card: '#FFFFFF',
                text: '#0F172A',
                text_muted: '#64748B',
                border: '#E2E8F0',
                success: '#16A34A',
            },
            navy_professional: {
                primary: '#0F172A',
                primary_hover: '#020617',
                secondary: '#F8FAFC',
                accent: '#3B82F6',
                background: '#FFFFFF',
                card: '#F8FAFC',
                text: '#0F172A',
                text_muted: '#475569',
                border: '#CBD5E1',
                success: '#10B981',
            },
            blue_modern: {
                primary: '#1E40AF',
                primary_hover: '#1E3A8A',
                secondary: '#EFF6FF',
                accent: '#0284C7',
                background: '#FFFFFF',
                card: '#FFFFFF',
                text: '#0F172A',
                text_muted: '#64748B',
                border: '#DBEAFE',
                success: '#059669',
            },
            neutral_government: {
                primary: '#334155',
                primary_hover: '#1E293B',
                secondary: '#F1F5F9',
                accent: '#64748B',
                background: '#F8FAFC',
                card: '#FFFFFF',
                text: '#1E293B',
                text_muted: '#64748B',
                border: '#E2E8F0',
                success: '#0D9488',
            },
        };

        const chosen = presets[presetKey];
        if (!chosen) return;

        this.globalStyles.colors = { ...this.globalStyles.colors, ...chosen };

        // Update color pickers UI
        document.querySelectorAll('[data-global-color]').forEach(picker => {
            const k = picker.getAttribute('data-global-color');
            if (chosen[k]) {
                picker.value = chosen[k];
                const hexEl = picker.nextElementSibling;
                if (hexEl) hexEl.textContent = chosen[k];
            }
        });

        // Live preview ke iframe canvas via Bridge
        if (this.bridge) {
            this.bridge.updateGlobalStyles(this.globalStyles);
        }

        // Active state badge
        document.querySelectorAll('.wp-preset-card[data-preset-type="color"]').forEach(c => {
            c.classList.toggle('is-active', c.getAttribute('data-preset-key') === presetKey);
        });

        this.notifyDirty();
    }

    applyButtonPreset(presetKey) {
        const presets = {
            rounded: { radius_px: 8, padding_y_px: 10, padding_x_px: 20 },
            pill: { radius_px: 9999, padding_y_px: 12, padding_x_px: 24 },
            sharp: { radius_px: 2, padding_y_px: 10, padding_x_px: 18 },
        };

        const chosen = presets[presetKey];
        if (!chosen) return;

        this.globalStyles.buttons = { ...this.globalStyles.buttons, ...chosen };

        const sliderRadius = document.getElementById('slider-global-btn-radius');
        if (sliderRadius) {
            sliderRadius.value = chosen.radius_px;
            const badge = document.getElementById('display-global-btn-radius');
            if (badge) badge.textContent = `${chosen.radius_px}px`;
        }

        if (this.bridge) {
            this.bridge.updateGlobalStyles(this.globalStyles);
        }

        this.notifyDirty();
    }

    applyCardPreset(presetKey) {
        const presets = {
            standard: { card_radius_px: 12, card_shadow: 'soft' },
            soft: { card_radius_px: 20, card_shadow: 'medium' },
            minimal: { card_radius_px: 4, card_shadow: 'none' },
        };

        const chosen = presets[presetKey];
        if (!chosen) return;

        this.globalStyles.layout = { ...this.globalStyles.layout, ...chosen };

        const sliderRadius = document.getElementById('slider-global-card-radius');
        if (sliderRadius) {
            sliderRadius.value = chosen.card_radius_px;
            const badge = document.getElementById('display-global-card-radius');
            if (badge) badge.textContent = `${chosen.card_radius_px}px`;
        }

        const selectShadow = document.getElementById('select-global-card-shadow');
        if (selectShadow) {
            selectShadow.value = chosen.card_shadow;
        }

        if (this.bridge) {
            this.bridge.updateGlobalStyles(this.globalStyles);
        }

        this.notifyDirty();
    }

    applyTypographyPreset(presetKey) {
        const presets = {
            official: { heading_font: 'Lexend', body_font: 'Source Sans 3', base_size_px: 16, line_height: 1.5 },
            modern: { heading_font: 'Plus Jakarta Sans', body_font: 'Inter', base_size_px: 16, line_height: 1.55 },
            compact: { heading_font: 'Inter', body_font: 'Inter', base_size_px: 15, line_height: 1.45 },
        };

        const chosen = presets[presetKey];
        if (!chosen) return;

        this.globalStyles.typography = { ...this.globalStyles.typography, ...chosen };

        const selectHeading = document.getElementById('global-font-heading');
        if (selectHeading) selectHeading.value = chosen.heading_font;

        const selectBody = document.getElementById('global-font-body');
        if (selectBody) selectBody.value = chosen.body_font;

        if (this.bridge) {
            this.bridge.updateGlobalStyles(this.globalStyles);
        }

        this.notifyDirty();
    }

    // =========================================================================
    // GENERAL CONTENT INPUTS & SERIALIZATION
    // =========================================================================

    initContentInputs() {
        // Standard inputs with data-field (legacy CMS key format)
        document.querySelectorAll('[data-field]:not([type="range"]):not([type="color"])').forEach(input => {
            const field = input.getAttribute('data-field');
            if (field) {
                if (input.type === 'checkbox') {
                    this.state[field] = input.checked ? '1' : '0';
                } else {
                    this.state[field] = input.value;
                }
            }

            input.addEventListener('input', (e) => {
                if (field) {
                    if (input.type === 'checkbox') {
                        this.state[field] = input.checked ? '1' : '0';
                    } else {
                        this.state[field] = e.target.value;
                    }
                }
                this.notifyDirty();
            });
        });

        // Inputs with data-block-field (modern block content)
        document.querySelectorAll('[data-block-field]').forEach(input => {
            const key = input.getAttribute('data-block-field');
            input.addEventListener('input', (e) => {
                const block = this.getActiveBlockData();
                if (block) {
                    if (!block.content) block.content = {};
                    block.content[key] = e.target.value;
                }
                this.notifyDirty();
            });
        });

        this.initTahapanStepInputs();
        this.initKebijakanSectionsInputs();
    }

    initTahapanStepInputs() {
        const stepInputs = document.querySelectorAll('.wp-tahapan-step-title, .wp-tahapan-step-desc');
        stepInputs.forEach(input => {
            input.addEventListener('input', () => {
                this.syncTahapanSteps();
            });
        });
    }

    syncTahapanSteps() {
        const steps = [];
        for (let i = 0; i < 4; i++) {
            const num = String(i + 1).padStart(2, '0');
            const titleEl = document.getElementById(`input-step-title-${i}`);
            const descEl = document.getElementById(`input-step-desc-${i}`);
            const title = titleEl?.value?.trim() || `Pos ${num}`;
            const desc = descEl?.value?.trim() || '';
            steps.push({
                step: num,
                title: title,
                badge: title.toUpperCase(),
                desc: desc
            });
        }
        this.state['landing_tahapan_steps'] = steps;
        this.notifyDirty();
    }

    initKebijakanSectionsInputs() {
        const container = document.getElementById('wp-kebijakan-sections-container');
        const btnAdd = document.getElementById('btn-add-kebijakan-pasal');

        if (container) {
            container.addEventListener('input', (e) => {
                if (e.target.matches('.wp-pasal-judul, .wp-pasal-konten, .wp-pasal-items, .wp-pasal-highlight')) {
                    this.syncKebijakanSections();
                }
            });

            container.addEventListener('click', (e) => {
                const removeBtn = e.target.closest('.btn-remove-pasal');
                if (removeBtn) {
                    const card = removeBtn.closest('.wp-kebijakan-item-card');
                    if (card && container.children.length > 1) {
                        card.remove();
                        this.reindexKebijakanCards();
                        this.syncKebijakanSections();
                    } else if (container.children.length <= 1) {
                        alert('Kebijakan Privasi minimal harus memiliki 1 pasal.');
                    }
                }
            });
        }

        if (btnAdd && container) {
            btnAdd.addEventListener('click', () => {
                const currentCount = container.querySelectorAll('.wp-kebijakan-item-card').length;
                const nextNum = currentCount + 1;
                const newCard = document.createElement('div');
                newCard.className = 'wp-card-subpanel wp-kebijakan-item-card';
                newCard.setAttribute('data-index', currentCount);
                newCard.style.cssText = 'background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 8px; padding: 0.75rem; margin-bottom: 0.75rem; position: relative;';
                newCard.innerHTML = `
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                        <span class="wp-pasal-badge" style="font-weight: 700; font-size: 0.75rem; color: #1E40AF;">Pasal ${nextNum}</span>
                        <button type="button" class="btn-remove-pasal wp-icon-btn wp-btn-danger" data-index="${currentCount}" style="width: 22px; height: 22px; font-size: 11px;" title="Hapus Pasal Ini">✕</button>
                    </div>
                    <div class="wp-field-group" style="margin-bottom: 0.4rem;">
                        <label style="font-size: 0.75rem;">Judul Pasal</label>
                        <input type="text" class="wp-text-input wp-pasal-judul" placeholder="Contoh: Judul Pasal Baru">
                    </div>
                    <div class="wp-field-group" style="margin-bottom: 0.4rem;">
                        <label style="font-size: 0.75rem;">Isi Narasi Pasal</label>
                        <textarea class="wp-textarea-input wp-pasal-konten" rows="3" placeholder="Uraian isi pasal baru"></textarea>
                    </div>
                    <div class="wp-field-group" style="margin-bottom: 0.4rem;">
                        <label style="font-size: 0.75rem;">Butir Poin (Opsional, Per Baris)</label>
                        <textarea class="wp-textarea-input wp-pasal-items" rows="2" placeholder="Poin 1\nPoin 2"></textarea>
                    </div>
                    <div class="wp-field-group" style="margin-bottom: 0;">
                        <label style="font-size: 0.75rem;">Catatan Khusus / Highlight (Opsional)</label>
                        <input type="text" class="wp-text-input wp-pasal-highlight" placeholder="Teks sorotan khusus">
                    </div>
                `;
                container.appendChild(newCard);
                this.syncKebijakanSections();
            });
        }
    }

    reindexKebijakanCards() {
        const cards = document.querySelectorAll('.wp-kebijakan-item-card');
        cards.forEach((card, idx) => {
            card.setAttribute('data-index', idx);
            const badge = card.querySelector('.wp-pasal-badge');
            if (badge) badge.textContent = `Pasal ${idx + 1}`;
            const removeBtn = card.querySelector('.btn-remove-pasal');
            if (removeBtn) removeBtn.setAttribute('data-index', idx);
        });
    }

    syncKebijakanSections() {
        const sections = [];
        const cards = document.querySelectorAll('.wp-kebijakan-item-card');
        cards.forEach((card, idx) => {
            const judul = card.querySelector('.wp-pasal-judul')?.value?.trim() || '';
            const konten = card.querySelector('.wp-pasal-konten')?.value?.trim() || '';
            const itemsRaw = card.querySelector('.wp-pasal-items')?.value || '';
            const highlight = card.querySelector('.wp-pasal-highlight')?.value?.trim() || '';

            const items = itemsRaw.split('\n').map(s => s.trim()).filter(s => s.length > 0);
            const sectionData = {
                nomor: idx + 1,
                judul: judul,
                konten: konten
            };
            if (items.length > 0) sectionData.items = items;
            if (highlight.length > 0) sectionData.highlight = highlight;

            sections.push(sectionData);
        });
        this.state['kebijakan_privasi_sections'] = sections;
        this.notifyDirty();
    }

    updateFieldFromInline(fieldName, value) {
        this.state[fieldName] = value;
        const matchingInput = document.querySelector(`[data-field="${fieldName}"]`);
        if (matchingInput) {
            matchingInput.value = value;
        }

        const block = this.getActiveBlockData();
        if (block) {
            if (!block.content) block.content = {};
            const cleanKey = fieldName.replace(/^landing_/, '');
            block.content[cleanKey] = value;
        }

        this.notifyDirty();
    }

    setHeroImageFromMedia(mediaItem) {
        if (!mediaItem) return;

        this.state['landing_hero_image'] = mediaItem.path;
        const thumbEl = document.getElementById('wp-hero-banner-thumb');
        if (thumbEl) {
            thumbEl.src = mediaItem.url;
        }

        const block = this.getActiveBlockData();
        if (block && block.type === 'hero') {
            if (!block.content) block.content = {};
            block.content.image = mediaItem.path;
        }

        if (this.bridge) {
            this.bridge.updateHeroImage(mediaItem.url);
        }

        this.notifyDirty();
    }

    setPanduanPdfFromMedia(mediaItem) {
        if (!mediaItem) return;

        this.state['landing_panduan_pdf'] = mediaItem.path;
        this.state['landing_panduan_pdf_name'] = mediaItem.original_name || mediaItem.title || 'Panduan-Magang.pdf';

        const nameEl = document.getElementById('wp-pdf-active-name');
        const descEl = document.getElementById('wp-pdf-status-desc');
        const pageNameEl = document.getElementById('wp-page-pdf-name');
        const pageDescEl = document.getElementById('wp-page-pdf-status');
        const resetBtns = [
            document.getElementById('btn-reset-panduan-pdf'),
            document.getElementById('btn-page-reset-pdf')
        ];

        if (nameEl) nameEl.textContent = this.state['landing_panduan_pdf_name'];
        if (pageNameEl) pageNameEl.textContent = this.state['landing_panduan_pdf_name'];
        if (descEl) descEl.textContent = 'Berkas Kustom Diunggah';
        if (pageDescEl) pageDescEl.textContent = 'Berkas Kustom Diunggah';
        resetBtns.forEach(btn => { if (btn) btn.style.display = 'inline-flex'; });

        this.notifyDirty();
    }

    async resetPanduanPdf() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        try {
            const res = await fetch('/superadmin/site-editor/reset-panduan', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            const data = await res.json();
            if (data.success) {
                this.state['landing_panduan_pdf'] = '';
                this.state['landing_panduan_pdf_name'] = 'Panduan-Resmi-Magang.pdf';

                const nameEl = document.getElementById('wp-pdf-active-name');
                const descEl = document.getElementById('wp-pdf-status-desc');
                const pageNameEl = document.getElementById('wp-page-pdf-name');
                const pageDescEl = document.getElementById('wp-page-pdf-status');
                const resetBtns = [
                    document.getElementById('btn-reset-panduan-pdf'),
                    document.getElementById('btn-page-reset-pdf')
                ];

                if (nameEl) nameEl.textContent = 'Panduan-Resmi-Magang.pdf';
                if (pageNameEl) pageNameEl.textContent = 'Panduan-Resmi-Magang.pdf';
                if (descEl) descEl.textContent = 'Generator PDF Bawaan Sistem';
                if (pageDescEl) pageDescEl.textContent = 'Generator PDF Bawaan Sistem';
                resetBtns.forEach(btn => { if (btn) btn.style.display = 'none'; });

                this.notifyDirty();
                return true;
            }
        } catch (e) {
            console.error('Failed to reset PDF:', e);
        }
        return false;
    }

    notifyDirty() {
        if (typeof this.onStateDirty === 'function') {
            this.onStateDirty();
        }
    }

    getSerializedState() {
        return {
            ...this.state,
            landing_global_styles: this.globalStyles,
            global_styles: this.globalStyles,
            landing_responsive_styles: this.responsiveStyles,
        };
    }

    applySnapshot(snapshot) {
        if (!snapshot || typeof snapshot !== 'object') return;

        if (snapshot.landing_global_styles || snapshot.global_styles) {
            this.globalStyles = this.normalizeGlobalStyles(snapshot.landing_global_styles || snapshot.global_styles);
            if (this.bridge) {
                this.bridge.updateGlobalStyles(this.globalStyles);
            }
        }

        Object.keys(snapshot).forEach(key => {
            this.state[key] = snapshot[key];

            const input = document.querySelector(`[data-field="${key}"]`);
            if (input) {
                if (input.type === 'checkbox') {
                    input.checked = snapshot[key] === '1' || snapshot[key] === true;
                } else {
                    input.value = snapshot[key];
                }
            }
        });

        if (snapshot.landing_responsive_styles) {
            this.responsiveStyles = { ...snapshot.landing_responsive_styles };
        }

        this.syncScopedControlsForActiveBlock();
    }
}
