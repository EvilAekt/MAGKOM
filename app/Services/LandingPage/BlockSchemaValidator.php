<?php

declare(strict_types=1);

namespace App\Services\LandingPage;

use Illuminate\Validation\ValidationException;

/**
 * Validator server-side ketat untuk Page Document dan Block Instances.
 * Mencegah data rusak, injeksi XSS, skrip berbahaya, dan inkonsistensi struktur.
 */
class BlockSchemaValidator
{
    /**
     * Memvalidasi seluruh Page Document.
     * Mengembalikan dokumen yang telah disanitasi dan dinormalisasi.
     *
     * @param  array<string, mixed>  $document
     * @return array<string, mixed>
     *
     * @throws ValidationException
     */
    public function validateAndSanitize(array $document): array
    {
        return $this->validateDocument($document);
    }

    public function validateDocument(array $document): array
    {
        $errors = [];

        $schemaVersion = $document['schema_version'] ?? 1;
        if (! is_numeric($schemaVersion) || (int) $schemaVersion < 1) {
            $schemaVersion = 1;
        }
        $document['schema_version'] = (int) $schemaVersion;

        if (empty($document['page_id']) || ! is_string($document['page_id'])) {
            $document['page_id'] = 'landing-magkom';
        }

        if (! isset($document['blocks']) || ! is_array($document['blocks'])) {
            $errors['blocks'][] = 'Dokumen wajib memiliki daftar blok (blocks array).';
        }

        if (! empty($errors)) {
            throw ValidationException::withMessages($errors);
        }

        $validatedBlocks = [];
        $seenIds = [];
        $order = 1;

        foreach ($document['blocks'] as $index => $rawBlock) {
            if (! is_array($rawBlock)) {
                $errors["blocks.{$index}"][] = 'Item blok harus berupa array asosiatif.';

                continue;
            }

            try {
                $cleanBlock = $this->validateBlock($rawBlock);
                $cleanBlock['order'] = $order++;

                if (isset($seenIds[$cleanBlock['id']])) {
                    $errors["blocks.{$index}.id"][] = "Duplikasi Block ID terdeteksi: '{$cleanBlock['id']}'. Setiap blok wajib memiliki ID unik.";
                } else {
                    $seenIds[$cleanBlock['id']] = true;
                }

                $validatedBlocks[] = $cleanBlock;
            } catch (ValidationException $e) {
                foreach ($e->errors() as $field => $messages) {
                    $errors["blocks.{$index}.{$field}"] = $messages;
                }
            }
        }

        // Pastikan blok wajib (locked) tetap ada
        $hasHero = false;
        $hasFooter = false;
        foreach ($validatedBlocks as $b) {
            if ($b['type'] === 'hero') {
                $hasHero = true;
            }
            if ($b['type'] === 'footer') {
                $hasFooter = true;
            }
        }

        if (! $hasHero) {
            $errors['blocks.hero'][] = 'Blok wajib Hero Banner tidak boleh dihilangkan dari dokumen.';
        }
        if (! $hasFooter) {
            $errors['blocks.footer'][] = 'Blok wajib Footer & Kontak tidak boleh dihilangkan dari dokumen.';
        }

        if (! empty($errors)) {
            throw ValidationException::withMessages($errors);
        }

        // 5. Validasi Global Styles Dokumen
        $globalStyles = is_array($document['global_styles'] ?? null)
            ? $this->sanitizeGlobalStyles($document['global_styles'])
            : BlockRegistry::getDefaultGlobalStyles();

        return [
            'schema_version' => (int) $document['schema_version'],
            'page_id' => (string) $document['page_id'],
            'meta' => is_array($document['meta'] ?? null) ? $document['meta'] : [],
            'global_styles' => $globalStyles,
            'blocks' => $validatedBlocks,
        ];
    }

    /**
     * Memvalidasi dan menyaring satu item Block Instance.
     *
     * @param  array<string, mixed>  $block
     * @return array<string, mixed>
     *
     * @throws ValidationException
     */
    public function validateBlock(array $block): array
    {
        $errors = [];

        // 1. Validasi Tipe Blok
        $type = (string) ($block['type'] ?? '');
        if (empty($type)) {
            $errors['type'][] = 'Tipe blok wajib diisi.';
        } elseif (! BlockRegistry::has($type)) {
            $errors['type'][] = "Tipe blok '{$type}' tidak terdaftar pada Block Registry resmi.";
        }

        $def = BlockRegistry::get($type);

        // 2. Validasi ID Blok
        $id = (string) ($block['id'] ?? '');
        if (empty($id)) {
            $errors['id'][] = 'ID blok wajib diisi.';
        } elseif (! preg_match('/^[a-zA-Z0-9_\-]+$/', $id) || strlen($id) > 64) {
            $errors['id'][] = 'ID blok hanya boleh memuat huruf, angka, strip (-), dan garis bawah (_), maksimal 64 karakter.';
        }

        // 3. Validasi Konten
        $rawContent = is_array($block['content'] ?? null) ? $block['content'] : [];
        $cleanContent = $this->sanitizeContent($type, $rawContent, $def?->defaultContent ?? []);

        // 4. Validasi Gaya (Styles)
        $rawStyles = is_array($block['styles'] ?? null) ? $block['styles'] : [];
        $cleanStyles = $this->sanitizeStyles($rawStyles, $def?->defaultStyles ?? []);

        // 5. Validasi Pengaturan Lanjutan (Settings: CSS class, HTML anchor, responsive visibility)
        $rawSettings = is_array($block['settings'] ?? null) ? $block['settings'] : [];
        $cleanSettings = $this->sanitizeSettings($rawSettings);

        if (! empty($errors)) {
            throw ValidationException::withMessages($errors);
        }

        $cleanBlock = [
            'id' => $id,
            'type' => $type,
            'version' => (int) ($block['version'] ?? ($def?->schemaVersion ?? 1)),
            'visible' => isset($block['visible']) ? (bool) $block['visible'] : true,
            'locked' => $def ? $def->locked : false,
            'user_locked' => (bool) ($block['user_locked'] ?? false),
            'order' => max(1, (int) ($block['order'] ?? 1)),
            'content' => $cleanContent,
            'styles' => $cleanStyles,
            'settings' => $cleanSettings,
        ];

        if (! empty($block['reusable_id'])) {
            $cleanBlock['reusable_id'] = (string) $block['reusable_id'];
        }

        return $cleanBlock;
    }

    /**
     * Sanitasi dan validasi konten per tipe blok.
     *
     * @param  array<string, mixed>  $content
     * @param  array<string, mixed>  $defaultContent
     * @return array<string, mixed>
     */
    protected function sanitizeContent(string $type, array $content, array $defaultContent): array
    {
        $merged = array_merge($defaultContent, $content);
        $clean = [];

        foreach ($merged as $key => $val) {
            if (is_string($val)) {
                // Validasi URL field (mencegah javascript: dan skema berbahaya)
                if (str_contains($key, 'url')) {
                    $clean[$key] = $this->sanitizeUrl($val);
                } else {
                    // Cegah XSS dan skrip berbahaya
                    $sanitized = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $val);
                    $clean[$key] = strip_tags((string) $sanitized, '<br><strong><em><b><i><p>');
                }
            } elseif (is_array($val)) {
                // Nested repeaters (seperti steps pada tahapan atau items pada features)
                $clean[$key] = $this->sanitizeNestedArray($val);
            } elseif (is_bool($val) || is_numeric($val)) {
                $clean[$key] = $val;
            } else {
                $clean[$key] = $defaultContent[$key] ?? null;
            }
        }

        return $clean;
    }

    /**
     * Sanitasi URL aman (mencegah javascript: dan skema asing).
     */
    protected function sanitizeUrl(string $url): string
    {
        $trimmed = trim($url);
        if ($trimmed === '' || $trimmed === '#') {
            return $trimmed;
        }

        if (preg_match('/^(javascript|data|vbscript):/i', $trimmed)) {
            throw ValidationException::withMessages([
                'url' => 'Skema URL tidak aman atau dilarang (XSS protection).',
            ]);
        }

        if (str_starts_with($trimmed, '#') || str_starts_with($trimmed, '/')) {
            return $trimmed;
        }

        if (str_starts_with($trimmed, 'http://') || str_starts_with($trimmed, 'https://') || str_starts_with($trimmed, 'mailto:') || str_starts_with($trimmed, 'tel:')) {
            return $trimmed;
        }

        // Jika skema mencurigakan, kembalikan anchor aman
        return '#';
    }

    /**
     * Sanitasi array bertingkat (nested repeater).
     *
     * @param  array<mixed>  $items
     * @return array<mixed>
     */
    protected function sanitizeNestedArray(array $items): array
    {
        $cleanItems = [];
        foreach ($items as $item) {
            if (is_array($item)) {
                $cleanSub = [];
                foreach ($item as $subKey => $subVal) {
                    if (is_string($subVal)) {
                        $cleanSub[$subKey] = strip_tags(trim($subVal), '<br><strong><em><b><i>');
                    } else {
                        $cleanSub[$subKey] = $subVal;
                    }
                }
                $cleanItems[] = $cleanSub;
            } elseif (is_string($item)) {
                $cleanItems[] = strip_tags(trim($item), '<br><strong><em><b><i>');
            } else {
                $cleanItems[] = $item;
            }
        }

        return $cleanItems;
    }

    /**
     * Sanitasi dan pembatasan nilai gaya visual (styles).
     *
     * @param  array<string, mixed>  $styles
     * @param  array<string, mixed>  $defaultStyles
     * @return array<string, array<string, mixed>>
     */
    protected function sanitizeStyles(array $styles, array $defaultStyles): array
    {
        $devices = ['desktop', 'tablet', 'mobile'];
        $cleanStyles = [];

        foreach ($devices as $device) {
            $deviceStyles = is_array($styles[$device] ?? null) ? $styles[$device] : [];
            if ($device === 'desktop' && empty($deviceStyles)) {
                foreach ($styles as $k => $v) {
                    if (! in_array($k, $devices, true)) {
                        $deviceStyles[$k] = $v;
                    }
                }
            }
            $defaultDevStyles = is_array($defaultStyles[$device] ?? null) ? $defaultStyles[$device] : [];
            $merged = array_merge($defaultDevStyles, $deviceStyles);
            $cleanDev = [];

            foreach ($merged as $prop => $val) {
                if (is_numeric($val)) {
                    $numVal = (int) $val;
                    // Terapkan batasan batas nilai angka wajar
                    if (str_contains($prop, 'padding') || str_contains($prop, 'margin') || str_contains($prop, 'gap')) {
                        $cleanDev[$prop] = max(0, min(300, $numVal));
                    } elseif (str_contains($prop, 'size')) {
                        $cleanDev[$prop] = max(10, min(120, $numVal));
                    } elseif (str_contains($prop, 'height')) {
                        $cleanDev[$prop] = max(0, min(1200, $numVal));
                    } elseif (str_contains($prop, 'radius')) {
                        $cleanDev[$prop] = max(0, min(60, $numVal));
                    } else {
                        $cleanDev[$prop] = $numVal;
                    }
                } elseif (is_string($val)) {
                    if (str_starts_with($val, '#') && preg_match('/^#[a-fA-F0-9]{3,8}$/', $val)) {
                        $cleanDev[$prop] = $val;
                    } elseif (in_array($val, ['left', 'center', 'right', 'justify'], true)) {
                        $cleanDev[$prop] = $val;
                    } else {
                        $cleanDev[$prop] = htmlspecialchars(trim($val), ENT_QUOTES, 'UTF-8');
                    }
                } else {
                    $cleanDev[$prop] = $defaultDevStyles[$prop] ?? null;
                }
            }

            $cleanStyles[$device] = $cleanDev;
        }

        // Buat alias flat ke root untuk kemudahan akses backward compatibility
        foreach ($cleanStyles['desktop'] ?? [] as $k => $v) {
            if (! isset($cleanStyles[$k])) {
                $cleanStyles[$k] = $v;
            }
        }

        return $cleanStyles;
    }

    /**
     * Sanitasi pengaturan lanjutan blok (Advanced Settings).
     *
     * @param  array<string, mixed>  $settings
     * @return array<string, mixed>
     */
    public function sanitizeSettings(array $settings): array
    {
        $clean = [
            'custom_css_class' => '',
            'html_anchor' => '',
            'hide_desktop' => (bool) ($settings['hide_desktop'] ?? false),
            'hide_tablet' => (bool) ($settings['hide_tablet'] ?? false),
            'hide_mobile' => (bool) ($settings['hide_mobile'] ?? false),
            'visible_desktop' => isset($settings['visible_desktop']) ? (bool) $settings['visible_desktop'] : true,
            'visible_tablet' => isset($settings['visible_tablet']) ? (bool) $settings['visible_tablet'] : true,
            'visible_mobile' => isset($settings['visible_mobile']) ? (bool) $settings['visible_mobile'] : true,
        ];

        // Validasi Custom CSS Class (hanya karakter aman, cegah injection)
        if (! empty($settings['custom_css_class'])) {
            $stripped = strip_tags((string) $settings['custom_css_class']);
            $classStr = trim((string) preg_replace('/[^a-zA-Z0-9_\-\s]/', '', $stripped));
            $clean['custom_css_class'] = substr($classStr, 0, 100);
        }

        // Validasi HTML Anchor (untuk in-page navigation hash)
        if (! empty($settings['html_anchor'])) {
            $strippedAnchor = strip_tags((string) $settings['html_anchor']);
            $anchorStr = trim((string) preg_replace('/[^a-zA-Z0-9_\-]/', '', ltrim($strippedAnchor, '#')));
            $clean['html_anchor'] = substr($anchorStr, 0, 50);
        }

        return $clean;
    }

    /**
     * Sanitasi dan validasi palet warna, tipografi, dan dimensi Global Styles.
     *
     * @param  array<string, mixed>  $globalStyles
     * @return array<string, mixed>
     */
    public function sanitizeGlobalStyles(array $globalStyles): array
    {
        $defaults = BlockRegistry::getDefaultGlobalStyles();
        $clean = $defaults;

        // 1. Sanitasi Palet Warna Global
        if (isset($globalStyles['colors']) && is_array($globalStyles['colors'])) {
            foreach ($globalStyles['colors'] as $token => $hex) {
                if (is_string($hex) && ! empty($hex)) {
                    $trimmed = trim($hex);
                    // Normalisasi 3 digit (#ABC -> #AABBCC)
                    if (preg_match('/^#([a-fA-F0-9])([a-fA-F0-9])([a-fA-F0-9])$/', $trimmed, $m)) {
                        $trimmed = '#'.$m[1].$m[1].$m[2].$m[2].$m[3].$m[3];
                    }
                    if (preg_match('/^#[a-fA-F0-9]{6}$/i', $trimmed)) {
                        $clean['colors'][$token] = strtoupper($trimmed);
                    }
                }
            }
        }

        // 2. Sanitasi Tipografi Global
        if (isset($globalStyles['typography']) && is_array($globalStyles['typography'])) {
            $allowedHeadings = ['Lexend', 'Plus Jakarta Sans', 'Inter', 'Montserrat', 'Poppins'];
            $allowedBodies = ['Source Sans 3', 'Plus Jakarta Sans', 'Inter', 'Roboto', 'Open Sans', 'Nunito Sans'];

            if (! empty($globalStyles['typography']['heading_font']) && in_array($globalStyles['typography']['heading_font'], $allowedHeadings, true)) {
                $clean['typography']['heading_font'] = $globalStyles['typography']['heading_font'];
            }
            if (! empty($globalStyles['typography']['body_font']) && in_array($globalStyles['typography']['body_font'], $allowedBodies, true)) {
                $clean['typography']['body_font'] = $globalStyles['typography']['body_font'];
            }

            $baseSize = $globalStyles['typography']['base_size_px'] ?? ($globalStyles['typography']['base_font_size'] ?? null);
            if ($baseSize !== null && is_numeric($baseSize)) {
                $clean['typography']['base_size_px'] = max(12, min(24, (int) $baseSize));
                $clean['typography']['base_font_size'] = $clean['typography']['base_size_px'];
            }

            $lineHeight = $globalStyles['typography']['line_height'] ?? null;
            if ($lineHeight !== null && is_numeric($lineHeight)) {
                $clean['typography']['line_height'] = max(1.0, min(2.5, (float) $lineHeight));
            }
        }

        // 3. Sanitasi Tata Letak Global
        if (isset($globalStyles['layout']) && is_array($globalStyles['layout'])) {
            $containerW = $globalStyles['layout']['container_max_width_px'] ?? ($globalStyles['layout']['container_width'] ?? null);
            if ($containerW !== null && is_numeric($containerW)) {
                $clean['layout']['container_max_width_px'] = max(960, min(1920, (int) $containerW));
                $clean['layout']['container_width'] = $clean['layout']['container_max_width_px'];
            }

            $gap = $globalStyles['layout']['section_gap_px'] ?? ($globalStyles['layout']['section_spacing'] ?? null);
            if ($gap !== null && is_numeric($gap)) {
                $clean['layout']['section_gap_px'] = max(0, min(200, (int) $gap));
                $clean['layout']['section_spacing'] = $clean['layout']['section_gap_px'];
            }

            $cardRad = $globalStyles['layout']['card_radius_px'] ?? ($globalStyles['layout']['card_radius'] ?? null);
            if ($cardRad !== null && is_numeric($cardRad)) {
                $clean['layout']['card_radius_px'] = max(0, min(32, (int) $cardRad));
                $clean['layout']['card_radius'] = $clean['layout']['card_radius_px'];
            }

            $shadow = $globalStyles['layout']['card_shadow'] ?? ($globalStyles['layout']['shadow'] ?? null);
            if (! empty($shadow) && in_array($shadow, ['none', 'soft', 'medium', 'strong', 'card', 'elevated'], true)) {
                $clean['layout']['card_shadow'] = $shadow;
                $clean['layout']['shadow'] = $shadow;
            } else {
                $clean['layout']['card_shadow'] = 'soft';
            }
        }

        // 4. Sanitasi Tombol Global
        $buttons = $globalStyles['buttons'] ?? ($globalStyles['button'] ?? null);
        if (isset($buttons) && is_array($buttons)) {
            foreach (['primary_bg', 'primary_text', 'secondary_bg', 'secondary_text'] as $btnColor) {
                if (! empty($buttons[$btnColor]) && preg_match('/^#[a-fA-F0-9]{3,8}$/', $buttons[$btnColor])) {
                    $clean['buttons'][$btnColor] = strtoupper($buttons[$btnColor]);
                    $clean['button'][$btnColor] = strtoupper($buttons[$btnColor]);
                }
            }

            $btnRadius = $buttons['radius_px'] ?? ($buttons['border_radius'] ?? null);
            if ($btnRadius !== null && is_numeric($btnRadius)) {
                $clean['buttons']['radius_px'] = max(0, min(40, (int) $btnRadius));
                $clean['button']['border_radius'] = $clean['buttons']['radius_px'];
            }

            $padY = $buttons['padding_y_px'] ?? ($buttons['padding_y'] ?? null);
            if ($padY !== null && is_numeric($padY)) {
                $clean['buttons']['padding_y_px'] = max(4, min(30, (int) $padY));
                $clean['button']['padding_y'] = $clean['buttons']['padding_y_px'];
            }

            $padX = $buttons['padding_x_px'] ?? ($buttons['padding_x'] ?? null);
            if ($padX !== null && is_numeric($padX)) {
                $clean['buttons']['padding_x_px'] = max(8, min(60, (int) $padX));
                $clean['button']['padding_x'] = $clean['buttons']['padding_x_px'];
            }
        }

        return $clean;
    }
}
