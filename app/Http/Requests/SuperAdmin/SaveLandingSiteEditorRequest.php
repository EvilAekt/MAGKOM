<?php

declare(strict_types=1);

namespace App\Http\Requests\SuperAdmin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SaveLandingSiteEditorRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna memiliki otorisasi untuk melakukan permintaan ini.
     */
    public function authorize(): bool
    {
        return $this->user()?->isSuperAdmin() ?? false;
    }

    /**
     * Dapatkan aturan validasi yang berlaku untuk permintaan ini.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Kontrol Versi & Concurrency Draf
            'document_version' => ['sometimes', 'nullable', 'integer', 'min:1'],
            'expected_version' => ['sometimes', 'nullable', 'integer', 'min:1'],
            'force' => ['sometimes', 'nullable', 'boolean'],

            // Kontrol Dimensi & Spasi Pixel Eksplisit
            'landing_container_max_width_px' => ['sometimes', 'nullable', 'integer', 'between:960,1600'],
            'landing_hero_title_size_px' => ['sometimes', 'nullable', 'integer', 'between:24,72'],
            'landing_hero_desc_size_px' => ['sometimes', 'nullable', 'integer', 'between:12,24'],
            'landing_hero_padding_y_px' => ['sometimes', 'nullable', 'integer', 'between:20,200'],
            'landing_hero_image_height_px' => ['sometimes', 'nullable', 'integer', 'between:250,700'],
            'landing_section_gap_px' => ['sometimes', 'nullable', 'integer', 'between:24,140'],
            'landing_card_radius_px' => ['sometimes', 'nullable', 'integer', 'between:0,32'],
            'landing_card_padding_px' => ['sometimes', 'nullable', 'integer', 'between:12,48'],
            'landing_faq_title_size_px' => ['sometimes', 'nullable', 'integer', 'between:14,24'],
            'landing_footer_padding_y_px' => ['sometimes', 'nullable', 'integer', 'between:32,140'],

            // Token Tema & Palet Warna WordPress Style
            'landing_theme_primary_color' => ['sometimes', 'nullable', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'landing_theme_navy_dark' => ['sometimes', 'nullable', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'landing_theme_accent_color' => ['sometimes', 'nullable', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'landing_theme_gold_color' => ['sometimes', 'nullable', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'landing_theme_bg_color' => ['sometimes', 'nullable', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'landing_theme_font_display' => ['sometimes', 'nullable', 'string', 'in:Lexend,Plus Jakarta Sans,Inter,Montserrat,Poppins'],
            'landing_theme_font_body' => ['sometimes', 'nullable', 'string', 'in:Source Sans 3,Inter,Roboto,Open Sans,Nunito Sans'],

            // Konten Teks & Inline WYSIWYG
            'landing_header_brand_name' => ['sometimes', 'nullable', 'string', 'max:50'],
            'landing_header_brand_sub' => ['sometimes', 'nullable', 'string', 'max:100'],
            'landing_hero_kicker' => ['sometimes', 'nullable', 'string', 'max:100'],
            'landing_hero_title' => ['sometimes', 'nullable', 'string', 'max:255'],
            'landing_hero_year' => ['sometimes', 'nullable', 'string', 'max:50'],
            'landing_hero_period_info' => ['sometimes', 'nullable', 'string', 'max:255'],
            'landing_hero_cta_text' => ['sometimes', 'nullable', 'string', 'max:50'],
            'landing_label_persyaratan' => ['sometimes', 'nullable', 'string', 'max:100'],
            'landing_persyaratan_umum' => ['sometimes', 'nullable', 'string', 'max:2000'],
            'landing_label_dokumen' => ['sometimes', 'nullable', 'string', 'max:100'],
            'landing_dokumen_pendaftaran' => ['sometimes', 'nullable', 'string', 'max:2000'],

            'landing_kuota_heading' => ['sometimes', 'nullable', 'string', 'max:255'],
            'landing_kuota_subheading' => ['sometimes', 'nullable', 'string', 'max:500'],
            'landing_tahapan_heading' => ['sometimes', 'nullable', 'string', 'max:255'],
            'landing_tahapan_summary' => ['sometimes', 'nullable', 'string', 'max:2000'],
            'landing_faq_heading' => ['sometimes', 'nullable', 'string', 'max:255'],

            'landing_footer_alamat' => ['sometimes', 'nullable', 'string', 'max:500'],
            'landing_footer_telepon' => ['sometimes', 'nullable', 'string', 'max:100'],
            'landing_footer_email' => ['sometimes', 'nullable', 'email', 'max:150'],
            'landing_footer_jam_layanan' => ['sometimes', 'nullable', 'string', 'max:255'],
            'landing_footer_copyright' => ['sometimes', 'nullable', 'string', 'max:255'],

            // Konten Tambahan Hero Sesuai UI WordPress Builder
            'landing_hero_cta_primary_url' => ['sometimes', 'nullable', 'string', 'max:255', 'regex:/^(#|\/|https?:\/\/)/i'],
            'landing_hero_cta_secondary_text' => ['sometimes', 'nullable', 'string', 'max:100'],
            'landing_hero_cta_secondary_url' => ['sometimes', 'nullable', 'string', 'max:255', 'regex:/^(#|\/|https?:\/\/)/i'],
            'landing_hero_overlay_active' => ['sometimes', 'nullable'],

            // Array Data Blok & Responsive Tree
            'landing_page_document' => ['sometimes', 'nullable', 'array'],
            'page_document' => ['sometimes', 'nullable', 'array'],
            'landing_blocks_layout' => ['sometimes', 'nullable', 'array'],
            'landing_global_styles' => ['sometimes', 'nullable', 'array'],
            'global_styles' => ['sometimes', 'nullable', 'array'],
            'landing_responsive_styles' => ['sometimes', 'nullable', 'array'],
            'landing_header_nav' => ['sometimes', 'nullable', 'array'],
            'landing_tahapan_steps' => ['sometimes', 'nullable', 'array'],
            'landing_faq_data' => ['sometimes', 'nullable', 'array'],
            'landing_footer_medsos' => ['sometimes', 'nullable', 'array'],
            'landing_footer_tautan' => ['sometimes', 'nullable', 'array'],
            'landing_footer_akun' => ['sometimes', 'nullable', 'array'],

            // Dokumen Panduan PDF & Navigasi
            'landing_panduan_pdf' => ['sometimes', 'nullable', 'string', 'max:255'],
            'landing_panduan_pdf_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'landing_tahapan_btn_pdf' => ['sometimes', 'nullable', 'string', 'max:100'],
            'landing_tahapan_btn_peta' => ['sometimes', 'nullable', 'string', 'max:100'],

            // Kebijakan Privasi & Perlindungan Data
            'kebijakan_privasi_badge' => ['sometimes', 'nullable', 'string', 'max:150'],
            'kebijakan_privasi_title' => ['sometimes', 'nullable', 'string', 'max:255'],
            'kebijakan_privasi_updated_at' => ['sometimes', 'nullable', 'string', 'max:255'],
            'kebijakan_privasi_sections' => ['sometimes', 'nullable', 'array'],
            'kebijakan_privasi_sections.*.nomor' => ['sometimes', 'nullable', 'integer'],
            'kebijakan_privasi_sections.*.id' => ['sometimes', 'nullable', 'string', 'max:100'],
            'kebijakan_privasi_sections.*.judul' => ['sometimes', 'nullable', 'string', 'max:255'],
            'kebijakan_privasi_sections.*.title' => ['sometimes', 'nullable', 'string', 'max:255'],
            'kebijakan_privasi_sections.*.konten' => ['sometimes', 'nullable', 'string'],
            'kebijakan_privasi_sections.*.content' => ['sometimes', 'nullable', 'string'],
            'kebijakan_privasi_sections.*.items' => ['sometimes', 'nullable', 'array'],
            'kebijakan_privasi_sections.*.highlight' => ['sometimes', 'nullable', 'string'],
        ];
    }

    /**
     * Dapatkan pesan kesalahan untuk aturan validasi yang ditentukan.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'landing_container_max_width_px.between' => 'Lebar kontainer maksimal harus antara 960 px hingga 1600 px.',
            'landing_hero_title_size_px.between' => 'Ukuran font judul hero harus antara 24 px hingga 72 px.',
            'landing_hero_desc_size_px.between' => 'Ukuran font deskripsi hero harus antara 12 px hingga 24 px.',
            'landing_hero_padding_y_px.between' => 'Jarak vertikal padding hero harus antara 20 px hingga 200 px.',
            'landing_hero_image_height_px.between' => 'Tinggi foto banner harus antara 250 px hingga 700 px.',
            'landing_section_gap_px.between' => 'Jarak antar bagian (gap) harus antara 24 px hingga 140 px.',
            'landing_card_radius_px.between' => 'Sudut lengkung kartu (border radius) harus antara 0 px hingga 32 px.',
            'landing_card_padding_px.between' => 'Padding dalam kartu harus antara 12 px hingga 48 px.',
            'landing_faq_title_size_px.between' => 'Ukuran font pertanyaan FAQ harus antara 14 px hingga 24 px.',
            'landing_footer_padding_y_px.between' => 'Jarak vertikal padding footer harus antara 32 px hingga 140 px.',
        ];
    }
}
