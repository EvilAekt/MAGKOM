<?php

declare(strict_types=1);

namespace App\Services\LandingPage;

/**
 * Value Object / DTO yang merepresentasikan spesifikasi resmi sebuah jenis blok pada Landing Page.
 */
class BlockDefinition
{
    /**
     * @param  string  $type  Tipe unik blok (contoh: 'hero', 'kuota', 'faq')
     * @param  string  $label  Nama human-readable blok
     * @param  string  $description  Deskripsi fungsi blok
     * @param  string  $icon  Emoji atau nama ikon
     * @param  string  $category  Kategori blok ('Layout', 'Konten', 'Ekstra')
     * @param  int  $schemaVersion  Versi skema blok
     * @param  array<string, mixed>  $defaultContent  Konten bawaan blok
     * @param  array<string, mixed>  $defaultStyles  Gaya visual bawaan (desktop, tablet, mobile)
     * @param  array<int, string>  $responsiveSupport  Daftar properti style yang mendukung override responsive
     * @param  array<int, string>  $allowedOperations  Daftar operasi yang diizinkan (move_up, move_down, duplicate, delete, hide)
     * @param  array<string, mixed>  $inspectorSchema  Definisi skema kontrol inspector
     * @param  string  $rendererView  Nama view Blade parsial resmi untuk rendering
     * @param  bool  $locked  Apakah blok terkunci (tidak boleh dihapus dari halaman)
     * @param  bool  $repeatable  Apakah blok boleh ditambahkan lebih dari satu kali
     */
    public function __construct(
        public readonly string $type,
        public readonly string $label,
        public readonly string $description,
        public readonly string $icon,
        public readonly string $category,
        public readonly int $schemaVersion,
        public readonly array $defaultContent,
        public readonly array $defaultStyles,
        public readonly array $responsiveSupport,
        public readonly array $allowedOperations,
        public readonly array $inspectorSchema,
        public readonly string $rendererView,
        public readonly bool $locked = false,
        public readonly bool $repeatable = false
    ) {}

    /**
     * Mengubah definisi ke representasi array asosiatif.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'label' => $this->label,
            'description' => $this->description,
            'icon' => $this->icon,
            'category' => $this->category,
            'schema_version' => $this->schemaVersion,
            'default_content' => $this->defaultContent,
            'default_styles' => $this->defaultStyles,
            'responsive_support' => $this->responsiveSupport,
            'allowed_operations' => $this->allowedOperations,
            'inspector_schema' => $this->inspectorSchema,
            'renderer_view' => $this->rendererView,
            'locked' => $this->locked,
            'repeatable' => $this->repeatable,
        ];
    }
}
