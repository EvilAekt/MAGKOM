<?php

declare(strict_types=1);

namespace App\Services;

use App\Services\LandingPage\BlockRegistry;

class LandingBlockRegistry
{
    /**
     * Dapatkan daftar registrasi seluruh blok resmi MAGKOM.
     * Menggunakan BlockRegistry formal sebagai Single Source of Truth.
     *
     * @return array<string, array<string, mixed>>
     */
    public static function getAllBlocks(): array
    {
        $all = [];
        foreach (BlockRegistry::getAll() as $type => $def) {
            $all[$type] = [
                'type' => $def->type,
                'name' => $def->label,
                'category' => $def->category,
                'icon' => $def->icon,
                'description' => $def->description,
                'required' => $def->locked,
                'can_delete' => ! $def->locked,
                'default_content' => $def->defaultContent,
                'default_styles' => $def->defaultStyles,
            ];
        }

        return $all;
    }

    /**
     * Dapatkan susunan layout standar bawaan (default block tree) MAGKOM.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function getDefaultLayout(): array
    {
        $doc = BlockRegistry::getDefaultDocument();
        $layout = [];

        foreach ($doc['blocks'] as $b) {
            $def = BlockRegistry::get($b['type']);
            $layout[] = [
                'id' => $b['id'],
                'type' => $b['type'],
                'title' => $def ? $def->label : ucfirst($b['type']),
                'visible' => $b['visible'],
                'order' => $b['order'],
                'content' => $b['content'],
                'styles' => $b['styles'],
            ];
        }

        return $layout;
    }

    /**
     * Dapatkan pola (Patterns) siap pakai untuk Landing Page MAGKOM.
     *
     * @return array<string, array<string, mixed>>
     */
    public static function getPatterns(): array
    {
        return [
            'hero_split' => [
                'id' => 'pattern-hero-split',
                'name' => 'Hero Banner Split (Standar Sleman)',
                'category' => 'Hero',
                'description' => 'Hero dengan judul besar di kiri, tombol pendaftaran, dan foto gedung/aktivitas di kanan.',
                'blocks' => [
                    [
                        'type' => 'hero',
                        'title' => 'Hero Banner',
                        'content' => array_merge(self::getAllBlocks()['hero']['default_content'], [
                            'title' => "Program Magang\nKominfo Sleman",
                            'overlay_active' => true,
                        ]),
                    ],
                ],
            ],
            'hero_centered' => [
                'id' => 'pattern-hero-centered',
                'name' => 'Hero Minimalis Tengah',
                'category' => 'Hero',
                'description' => 'Hero terfokus dengan perataan teks di tengah dan tombol aksi kembar.',
                'blocks' => [
                    [
                        'type' => 'hero',
                        'title' => 'Hero Tengah',
                        'content' => array_merge(self::getAllBlocks()['hero']['default_content'], [
                            'title' => 'Tingkatkan Kompetensi Digital Anda',
                            'subtitle' => 'Bergabung dalam ekosistem pemerintahan cerdas Smart Regency Sleman.',
                        ]),
                    ],
                ],
            ],
            'informasi_persyaratan' => [
                'id' => 'pattern-informasi-persyaratan',
                'name' => 'Informasi & Berkas Administrasi',
                'category' => 'Informasi',
                'description' => 'Dua kolom kartu berisi rincian syarat akademik dan berkas wajib pendaftaran.',
                'blocks' => [
                    [
                        'type' => 'informasi',
                        'title' => 'Persyaratan & Berkas',
                        'content' => self::getAllBlocks()['informasi']['default_content'],
                    ],
                ],
            ],
            'keunggulan_features' => [
                'id' => 'pattern-features-trio',
                'name' => 'Tiga Kartu Keunggulan Program',
                'category' => 'Konten',
                'description' => 'Tiga kartu sorotan fasilitas magang: mentor ASN, fasilitas kantor, sertifikat resmi.',
                'blocks' => [
                    [
                        'type' => 'features',
                        'title' => 'Keunggulan Program',
                        'content' => self::getAllBlocks()['features']['default_content'],
                    ],
                ],
            ],
            'cta_section' => [
                'id' => 'pattern-cta-box',
                'name' => 'Kotak Ajakan Daftar (CTA)',
                'category' => 'Konten',
                'description' => 'Banner aksen indigo dengan tombol pendaftaran kuota cepat.',
                'blocks' => [
                    [
                        'type' => 'cta_banner',
                        'title' => 'Ajakan Pendaftaran',
                        'content' => self::getAllBlocks()['cta_banner']['default_content'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Sanitasi struktur array layout blok dari input client.
     *
     * @param  array<mixed>  $rawLayout
     * @return array<int, array<string, mixed>>
     */
    public static function sanitizeLayout(array $rawLayout): array
    {
        $allBlocks = self::getAllBlocks();
        $clean = [];
        $order = 1;

        foreach ($rawLayout as $item) {
            if (! is_array($item)) {
                continue;
            }

            $type = (string) ($item['type'] ?? '');
            if (! isset($allBlocks[$type])) {
                continue; // Abaikan tipe blok asing / tidak terdaftar
            }

            $id = (string) ($item['id'] ?? 'block-'.$type.'-'.uniqid());
            $title = (string) ($item['title'] ?? $allBlocks[$type]['name']);
            $visible = isset($item['visible']) ? (bool) $item['visible'] : true;

            // Content sanitization: strip dangerous tags
            $content = is_array($item['content'] ?? null) ? $item['content'] : $allBlocks[$type]['default_content'];
            $cleanContent = [];
            foreach ($content as $cKey => $cVal) {
                if (is_string($cVal)) {
                    // Tolak javascript: URLs dan script tags
                    $sanitized = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $cVal);
                    $sanitized = preg_replace('/javascript:/i', '', (string) $sanitized);
                    $cleanContent[$cKey] = strip_tags((string) $sanitized, '<br><strong><em><b><i><p>');
                } elseif (is_array($cVal)) {
                    $cleanContent[$cKey] = $cVal;
                } else {
                    $cleanContent[$cKey] = $cVal;
                }
            }

            // Styles sanitization
            $styles = is_array($item['styles'] ?? null) ? $item['styles'] : $allBlocks[$type]['default_styles'];

            $clean[] = [
                'id' => preg_replace('/[^a-zA-Z0-9_\-]/', '', $id),
                'type' => $type,
                'title' => htmlspecialchars($title, ENT_QUOTES, 'UTF-8'),
                'visible' => $visible,
                'order' => $order++,
                'content' => $cleanContent,
                'styles' => $styles,
            ];
        }

        // Pastikan hero dan footer tetap ada jika tidak sengaja terhapus
        $hasHero = false;
        $hasFooter = false;
        foreach ($clean as $b) {
            if ($b['type'] === 'hero') {
                $hasHero = true;
            }
            if ($b['type'] === 'footer') {
                $hasFooter = true;
            }
        }

        if (! $hasHero) {
            array_unshift($clean, [
                'id' => 'block-hero-1',
                'type' => 'hero',
                'title' => 'Hero Banner',
                'visible' => true,
                'order' => 1,
                'content' => $allBlocks['hero']['default_content'],
                'styles' => $allBlocks['hero']['default_styles'],
            ]);
        }

        if (! $hasFooter) {
            $clean[] = [
                'id' => 'block-footer-1',
                'type' => 'footer',
                'title' => 'Footer & Kontak',
                'visible' => true,
                'order' => count($clean) + 1,
                'content' => $allBlocks['footer']['default_content'],
                'styles' => $allBlocks['footer']['default_styles'],
            ];
        }

        // Re-index order
        foreach ($clean as $idx => &$bl) {
            $bl['order'] = $idx + 1;
        }

        return $clean;
    }
}
