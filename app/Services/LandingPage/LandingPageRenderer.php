<?php

declare(strict_types=1);

namespace App\Services\LandingPage;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Throwable;

/**
 * Dynamic Block Renderer resmi untuk Landing Page MAGKOM.
 * Bertanggung jawab menyusun dan me-render blok berdasarkan Page Document secara aman,
 * menggunakan strict allowlist, isolasi error per blok, dan dukungan visibilitas.
 */
class LandingPageRenderer
{
    /**
     * Me-render seluruh Page Document menjadi string HTML.
     *
     * @param  array<string, mixed>  $document
     * @param  array<string, mixed>  $context  Data kontekstual sistem (matriks kuota, bulan, dll.)
     * @param  bool  $isEditorMode  Apakah sedang dalam mode editor canvas (iframe)
     */
    public function renderDocument(array $document, array $context = [], bool $isEditorMode = false): string
    {
        $blocks = $document['blocks'] ?? [];
        $globalStyles = $document['global_styles'] ?? [];
        $globalCss = ! empty($globalStyles) ? $this->generateGlobalCss($globalStyles) : '';

        return $globalCss.$this->renderBlocks($blocks, $context, $isEditorMode);
    }

    /**
     * Me-render kumpulan blok terurut menjadi HTML string.
     *
     * @param  array<int, array<string, mixed>>  $blocks
     * @param  array<string, mixed>  $context
     */
    public function renderBlocks(array $blocks, array $context = [], bool $isEditorMode = false): string
    {
        // Urutkan blok berdasarkan 'order'
        usort($blocks, fn ($a, $b) => ($a['order'] ?? 0) <=> ($b['order'] ?? 0));

        $html = '';
        foreach ($blocks as $block) {
            $html .= $this->renderBlock($block, $context, $isEditorMode);
        }

        return $html;
    }

    /**
     * Me-render satu unit Block Instance.
     *
     * @param  array<string, mixed>  $block
     * @param  array<string, mixed>  $context
     */
    public function renderBlock(array $block, array $context = [], bool $isEditorMode = false): string
    {
        $type = (string) ($block['type'] ?? '');
        $visible = isset($block['visible']) ? (bool) $block['visible'] : true;

        // Jika blok disembunyikan dan bukan mode editor, jangan render sama sekali ke pengunjung publik
        if (! $visible && ! $isEditorMode) {
            return '';
        }

        // Strict Allowlist: pastikan tipe blok terdaftar
        if (! BlockRegistry::has($type)) {
            Log::warning("LandingPageRenderer: Tipe blok '{$type}' tidak terdaftar di BlockRegistry. Blok diabaikan.");

            return "<!-- [MAGKOM Block Engine: Blok tipe '{$type}' tidak dikenal / diabaikan] -->\n";
        }

        $def = BlockRegistry::get($type);
        $viewName = $def ? $def->rendererView : null;

        if (! $viewName || ! View::exists($viewName)) {
            Log::error("LandingPageRenderer: View '{$viewName}' untuk tipe blok '{$type}' tidak ditemukan.");

            return "<!-- [MAGKOM Block Engine: View '{$viewName}' tidak ditemukan] -->\n";
        }

        // Gabungkan konten dan gaya dengan nilai bawaan dari registry
        $content = array_merge($def->defaultContent, is_array($block['content'] ?? null) ? $block['content'] : []);
        $styles = array_merge($def->defaultStyles, is_array($block['styles'] ?? null) ? $block['styles'] : []);
        $settings = is_array($block['settings'] ?? null) ? $block['settings'] : [];

        // Sinkronisasi Reusable Block: Jika blok mereferensikan Reusable Block sentral, sinkronkan konten & gayanya
        $reusableId = ! empty($block['reusable_id']) ? (string) $block['reusable_id'] : null;
        if ($reusableId) {
            $reusableDef = app(ReusableBlockService::class)->getById($reusableId);
            if ($reusableDef) {
                if (is_array($reusableDef['content'] ?? null)) {
                    $content = array_merge($content, $reusableDef['content']);
                }
                if (is_array($reusableDef['styles'] ?? null)) {
                    $styles = array_merge($styles, $reusableDef['styles']);
                }
            }
        }

        $blockId = (string) ($block['id'] ?? 'block-'.$type.'-'.uniqid());

        $blockPayload = array_merge($context, [
            'block' => $block,
            'blockId' => $blockId,
            'blockType' => $type,
            'blockLabel' => $def->label,
            'content' => $content,
            'styles' => $styles,
            'settings' => $settings,
            'visible' => $visible,
            'order' => (int) ($block['order'] ?? 1),
            'isEditorMode' => $isEditorMode,
        ]);

        try {
            $renderedHtml = View::make($viewName, $blockPayload)->render();
            $scopedCss = $this->generateScopedCss($blockId, $styles);

            return $scopedCss.$renderedHtml;
        } catch (Throwable $e) {
            // Isolasi kegagalan: jangan biarkan error di satu blok meruntuhkan seluruh halaman publik
            Log::error("LandingPageRenderer: Gagal me-render blok ID '{$block['id']}' ({$type}): ".$e->getMessage(), [
                'exception' => $e,
            ]);

            return "<!-- [MAGKOM Block Engine: Gagal memuat section {$def->label}] -->\n";
        }
    }

    /**
     * Menghasilkan CSS terisolasi (Scoped CSS) khusus untuk satu instance blok.
     * Menggunakan selector unik [data-wp-block-id="..."] dengan breakpoint responsive terpadu.
     *
     * @param  array<string, mixed>  $styles
     */
    public function generateScopedCss(string $blockId, array $styles): string
    {
        $isNested = isset($styles['desktop']) || isset($styles['tablet']) || isset($styles['mobile']);
        $desktop = is_array($styles['desktop'] ?? null) ? $styles['desktop'] : ($isNested ? [] : $styles);
        $tablet = is_array($styles['tablet'] ?? null) ? $styles['tablet'] : [];
        $mobile = is_array($styles['mobile'] ?? null) ? $styles['mobile'] : [];

        $rules = [];

        // 1. Aturan Dasar Desktop (Prioritas: Block-specific styles)
        $desktopProps = [];
        if (isset($desktop['padding_y'])) {
            $pY = (int) $desktop['padding_y'];
            $desktopProps[] = "--block-padding-y: {$pY}px;";
            $desktopProps[] = "padding-top: {$pY}px !important;";
            $desktopProps[] = "padding-bottom: {$pY}px !important;";
        }
        if (isset($desktop['margin_y'])) {
            $mY = (int) $desktop['margin_y'];
            $desktopProps[] = "--block-margin-y: {$mY}px;";
            $desktopProps[] = "margin-top: {$mY}px !important;";
            $desktopProps[] = "margin-bottom: {$mY}px !important;";
        }
        $fontSize = $desktop['font_size'] ?? ($desktop['title_size'] ?? null);
        if ($fontSize !== null) {
            $fSize = (int) $fontSize;
            $desktopProps[] = "--block-font-size: {$fSize}px;";
            $desktopProps[] = "--block-title-size: {$fSize}px;";
        }
        if (isset($desktop['desc_size'])) {
            $dSize = (int) $desktop['desc_size'];
            $desktopProps[] = "--block-desc-size: {$dSize}px;";
        }
        if (isset($desktop['card_radius'])) {
            $cRad = (int) $desktop['card_radius'];
            $desktopProps[] = "--block-card-radius: {$cRad}px;";
        }
        if (isset($desktop['card_padding'])) {
            $cPad = (int) $desktop['card_padding'];
            $desktopProps[] = "--block-card-padding: {$cPad}px;";
        }
        if (isset($desktop['section_gap'])) {
            $sGap = (int) $desktop['section_gap'];
            $desktopProps[] = "--block-section-gap: {$sGap}px;";
        }
        if (isset($desktop['image_height'])) {
            $imgH = (int) $desktop['image_height'];
            $desktopProps[] = "--block-image-height: {$imgH}px;";
        }
        if (isset($desktop['height'])) {
            $h = (int) $desktop['height'];
            $desktopProps[] = "height: {$h}px !important;";
        }
        $bg = $desktop['bg_color'] ?? ($desktop['background'] ?? null);
        if (! empty($bg) && preg_match('/^#[a-fA-F0-9]{3,8}$/', (string) $bg)) {
            $desktopProps[] = "--block-bg-color: {$bg};";
            $desktopProps[] = "background-color: {$bg} !important;";
        }
        if (! empty($desktop['text_color']) && preg_match('/^#[a-fA-F0-9]{3,8}$/', (string) $desktop['text_color'])) {
            $desktopProps[] = "--block-text-color: {$desktop['text_color']};";
            $desktopProps[] = "color: {$desktop['text_color']} !important;";
        }
        if (! empty($desktop['text_align']) && in_array($desktop['text_align'], ['left', 'center', 'right'], true)) {
            $desktopProps[] = "--block-text-align: {$desktop['text_align']};";
            $desktopProps[] = "text-align: {$desktop['text_align']} !important;";
        }

        if (! empty($desktopProps)) {
            $rules[] = "[data-wp-block-id=\"{$blockId}\"] { ".implode(' ', $desktopProps).' }';
            if (isset($desktop['title_size']) || isset($desktop['font_size'])) {
                $sizeVal = $desktop['font_size'] ?? $desktop['title_size'];
                $rules[] = "[data-wp-block-id=\"{$blockId}\"] .hero-title, [data-wp-block-id=\"{$blockId}\"] h1, [data-wp-block-id=\"{$blockId}\"] h2, [data-wp-block-id=\"{$blockId}\"] h3 { font-size: {$sizeVal}px !important; }";
            }
            if (isset($desktop['desc_size'])) {
                $rules[] = "[data-wp-block-id=\"{$blockId}\"] .hero-period-info, [data-wp-block-id=\"{$blockId}\"] .section-desc-sub, [data-wp-block-id=\"{$blockId}\"] p { font-size: {$desktop['desc_size']}px !important; }";
            }
            if (isset($desktop['card_radius'])) {
                $rules[] = "[data-wp-block-id=\"{$blockId}\"] .bidang-kuota-card, [data-wp-block-id=\"{$blockId}\"] .tahapan-card, [data-wp-block-id=\"{$blockId}\"] .faq-card-modern, [data-wp-block-id=\"{$blockId}\"] .hero-box { border-radius: {$desktop['card_radius']}px !important; }";
            }
            if (isset($desktop['image_height'])) {
                $rules[] = "[data-wp-block-id=\"{$blockId}\"] .hero-box-banner, [data-wp-block-id=\"{$blockId}\"] img.hero-box-banner { height: {$desktop['image_height']}px !important; }";
            }
        }

        // 2. Override Tablet (max-width: 1023px) — Mewarisi nilai Desktop jika tidak ditentukan
        $tabletProps = [];
        if (isset($tablet['padding_y'])) {
            $pY = (int) $tablet['padding_y'];
            $tabletProps[] = "--block-padding-y: {$pY}px;";
            $tabletProps[] = "padding-top: {$pY}px !important;";
            $tabletProps[] = "padding-bottom: {$pY}px !important;";
        }
        if (isset($tablet['margin_y'])) {
            $mY = (int) $tablet['margin_y'];
            $tabletProps[] = "--block-margin-y: {$mY}px;";
            $tabletProps[] = "margin-top: {$mY}px !important;";
            $tabletProps[] = "margin-bottom: {$mY}px !important;";
        }
        $tabFontSize = $tablet['font_size'] ?? ($tablet['title_size'] ?? null);
        if ($tabFontSize !== null) {
            $tF = (int) $tabFontSize;
            $tabletProps[] = "--block-font-size: {$tF}px;";
            $tabletProps[] = "--block-title-size: {$tF}px;";
        }
        if (isset($tablet['desc_size'])) {
            $tabletProps[] = "--block-desc-size: {$tablet['desc_size']}px;";
        }
        if (isset($tablet['card_radius'])) {
            $tabletProps[] = "--block-card-radius: {$tablet['card_radius']}px;";
        }
        if (isset($tablet['image_height'])) {
            $tabletProps[] = "--block-image-height: {$tablet['image_height']}px;";
        }
        if (isset($tablet['height'])) {
            $tabletProps[] = "height: {$tablet['height']}px !important;";
        }
        $tabBg = $tablet['bg_color'] ?? ($tablet['background'] ?? null);
        if (! empty($tabBg) && preg_match('/^#[a-fA-F0-9]{3,8}$/', (string) $tabBg)) {
            $tabletProps[] = "--block-bg-color: {$tabBg};";
            $tabletProps[] = "background-color: {$tabBg} !important;";
        }
        if (! empty($tablet['text_color']) && preg_match('/^#[a-fA-F0-9]{3,8}$/', (string) $tablet['text_color'])) {
            $tabletProps[] = "--block-text-color: {$tablet['text_color']};";
            $tabletProps[] = "color: {$tablet['text_color']} !important;";
        }
        if (! empty($tablet['text_align']) && in_array($tablet['text_align'], ['left', 'center', 'right'], true)) {
            $tabletProps[] = "--block-text-align: {$tablet['text_align']};";
            $tabletProps[] = "text-align: {$tablet['text_align']} !important;";
        }

        if (! empty($tabletProps)) {
            $rules[] = "@media (max-width: 1023px) { [data-wp-block-id=\"{$blockId}\"] { ".implode(' ', $tabletProps).' } }';
            if ($tabFontSize !== null) {
                $rules[] = "@media (max-width: 1023px) { [data-wp-block-id=\"{$blockId}\"] .hero-title, [data-wp-block-id=\"{$blockId}\"] h1, [data-wp-block-id=\"{$blockId}\"] h2, [data-wp-block-id=\"{$blockId}\"] h3 { font-size: {$tabFontSize}px !important; } }";
            }
            if (isset($tablet['image_height'])) {
                $rules[] = "@media (max-width: 1023px) { [data-wp-block-id=\"{$blockId}\"] .hero-box-banner, [data-wp-block-id=\"{$blockId}\"] img.hero-box-banner { height: {$tablet['image_height']}px !important; } }";
            }
        }

        // 3. Override Mobile (max-width: 767px) — Mewarisi nilai Tablet / Desktop jika tidak ditentukan
        $mobileProps = [];
        if (isset($mobile['padding_y'])) {
            $pY = (int) $mobile['padding_y'];
            $mobileProps[] = "--block-padding-y: {$pY}px;";
            $mobileProps[] = "padding-top: {$pY}px !important;";
            $mobileProps[] = "padding-bottom: {$pY}px !important;";
        }
        if (isset($mobile['margin_y'])) {
            $mY = (int) $mobile['margin_y'];
            $mobileProps[] = "--block-margin-y: {$mY}px;";
            $mobileProps[] = "margin-top: {$mY}px !important;";
            $mobileProps[] = "margin-bottom: {$mY}px !important;";
        }
        $mobFontSize = $mobile['font_size'] ?? ($mobile['title_size'] ?? null);
        if ($mobFontSize !== null) {
            $mF = (int) $mobFontSize;
            $mobileProps[] = "--block-font-size: {$mF}px;";
            $mobileProps[] = "--block-title-size: {$mF}px;";
        }
        if (isset($mobile['desc_size'])) {
            $mobileProps[] = "--block-desc-size: {$mobile['desc_size']}px;";
        }
        if (isset($mobile['card_radius'])) {
            $mobileProps[] = "--block-card-radius: {$mobile['card_radius']}px;";
        }
        if (isset($mobile['image_height'])) {
            $mobileProps[] = "--block-image-height: {$mobile['image_height']}px;";
        }
        if (isset($mobile['height'])) {
            $mobileProps[] = "height: {$mobile['height']}px !important;";
        }
        $mobBg = $mobile['bg_color'] ?? ($mobile['background'] ?? null);
        if (! empty($mobBg) && preg_match('/^#[a-fA-F0-9]{3,8}$/', (string) $mobBg)) {
            $mobileProps[] = "--block-bg-color: {$mobBg};";
            $mobileProps[] = "background-color: {$mobBg} !important;";
        }
        if (! empty($mobile['text_color']) && preg_match('/^#[a-fA-F0-9]{3,8}$/', (string) $mobile['text_color'])) {
            $mobileProps[] = "--block-text-color: {$mobile['text_color']};";
            $mobileProps[] = "color: {$mobile['text_color']} !important;";
        }
        if (! empty($mobile['text_align']) && in_array($mobile['text_align'], ['left', 'center', 'right'], true)) {
            $mobileProps[] = "--block-text-align: {$mobile['text_align']};";
            $mobileProps[] = "text-align: {$mobile['text_align']} !important;";
        }

        if (! empty($mobileProps)) {
            $rules[] = "@media (max-width: 767px) { [data-wp-block-id=\"{$blockId}\"] { ".implode(' ', $mobileProps).' } }';
            if ($mobFontSize !== null) {
                $rules[] = "@media (max-width: 767px) { [data-wp-block-id=\"{$blockId}\"] .hero-title, [data-wp-block-id=\"{$blockId}\"] h1, [data-wp-block-id=\"{$blockId}\"] h2, [data-wp-block-id=\"{$blockId}\"] h3 { font-size: {$mobFontSize}px !important; } }";
            }
            if (isset($mobile['image_height'])) {
                $rules[] = "@media (max-width: 767px) { [data-wp-block-id=\"{$blockId}\"] .hero-box-banner, [data-wp-block-id=\"{$blockId}\"] img.hero-box-banner { height: {$mobile['image_height']}px !important; } }";
            }
        }

        if (empty($rules)) {
            return '';
        }

        return "<style id=\"scoped-style-{$blockId}\">\n".implode("\n", $rules)."\n</style>\n";
    }

    /**
     * Menghasilkan CSS token global (:root) dari konfigurasi Global Styles dokumen.
     *
     * @param  array<string, mixed>  $globalStyles
     */
    public function generateGlobalCss(array $globalStyles): string
    {
        $props = [];

        // Colors
        if (! empty($globalStyles['colors']) && is_array($globalStyles['colors'])) {
            foreach ($globalStyles['colors'] as $token => $hex) {
                if (is_string($hex) && preg_match('/^#[a-fA-F0-9]{3,8}$/', $hex)) {
                    $props[] = "--wp-color-{$token}: {$hex};";
                }
            }
        }

        // Typography
        if (! empty($globalStyles['typography']) && is_array($globalStyles['typography'])) {
            if (! empty($globalStyles['typography']['heading_font'])) {
                $hFont = $globalStyles['typography']['heading_font'];
                $props[] = "--wp-font-heading: '{$hFont}', sans-serif;";
                $props[] = "--wp-font-display: '{$hFont}', sans-serif;";
            }
            if (! empty($globalStyles['typography']['body_font'])) {
                $bFont = $globalStyles['typography']['body_font'];
                $props[] = "--wp-font-body: '{$bFont}', sans-serif;";
                $props[] = "--wp-font-sans: '{$bFont}', sans-serif;";
            }
            $baseSize = $globalStyles['typography']['base_size_px'] ?? ($globalStyles['typography']['base_font_size'] ?? null);
            if ($baseSize !== null) {
                $props[] = "--wp-base-font-size: {$baseSize}px;";
                $props[] = "--wp-font-size-base: {$baseSize}px;";
            }
            if (! empty($globalStyles['typography']['line_height'])) {
                $props[] = "--wp-line-height: {$globalStyles['typography']['line_height']};";
                $props[] = "--wp-line-height-base: {$globalStyles['typography']['line_height']};";
            }
        }

        // Layout
        if (! empty($globalStyles['layout']) && is_array($globalStyles['layout'])) {
            $containerW = $globalStyles['layout']['container_max_width_px'] ?? ($globalStyles['layout']['container_width'] ?? null);
            if ($containerW !== null) {
                $props[] = "--wp-container-max-w: {$containerW}px;";
            }
            $secGap = $globalStyles['layout']['section_gap_px'] ?? ($globalStyles['layout']['section_spacing'] ?? null);
            if ($secGap !== null) {
                $props[] = "--wp-section-gap-y: {$secGap}px;";
            }
            $cardRad = $globalStyles['layout']['card_radius_px'] ?? ($globalStyles['layout']['card_radius'] ?? null);
            if ($cardRad !== null) {
                $props[] = "--wp-card-border-radius: {$cardRad}px;";
            }
            $shadow = $globalStyles['layout']['card_shadow'] ?? ($globalStyles['layout']['shadow'] ?? null);
            if (! empty($shadow)) {
                $shadowMap = [
                    'none' => 'none',
                    'soft' => '0 4px 12px rgba(0, 0, 0, 0.06)',
                    'medium' => '0 8px 24px rgba(0, 0, 0, 0.09)',
                    'strong' => '0 16px 32px rgba(0, 0, 0, 0.12)',
                ];
                $props[] = '--wp-card-shadow: '.($shadowMap[$shadow] ?? $shadow).';';
            }
        }

        // Buttons
        $buttons = $globalStyles['buttons'] ?? ($globalStyles['button'] ?? null);
        if (isset($buttons) && is_array($buttons)) {
            if (! empty($buttons['primary_bg'])) {
                $props[] = "--wp-btn-primary-bg: {$buttons['primary_bg']};";
            }
            if (! empty($buttons['primary_text'])) {
                $props[] = "--wp-btn-primary-text: {$buttons['primary_text']};";
            }
            $btnRad = $buttons['radius_px'] ?? ($buttons['border_radius'] ?? null);
            if ($btnRad !== null) {
                $props[] = "--wp-radius-button: {$btnRad}px;";
                $props[] = "--wp-btn-border-radius: {$btnRad}px;";
            }
            $padY = $buttons['padding_y_px'] ?? ($buttons['padding_y'] ?? null);
            if ($padY !== null) {
                $props[] = "--wp-btn-padding-y: {$padY}px;";
            }
            $padX = $buttons['padding_x_px'] ?? ($buttons['padding_x'] ?? null);
            if ($padX !== null) {
                $props[] = "--wp-btn-padding-x: {$padX}px;";
            }
        }

        if (empty($props)) {
            return '';
        }

        return "<style id=\"wp-global-styles\">\n:root {\n  ".implode("\n  ", $props)."\n}\n</style>\n";
    }
}
