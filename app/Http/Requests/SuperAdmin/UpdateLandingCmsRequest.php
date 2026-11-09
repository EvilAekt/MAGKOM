<?php

declare(strict_types=1);

namespace App\Http\Requests\SuperAdmin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateLandingCmsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $section = (string) $this->input('section', 'hero');

        $commonRules = [
            'section' => ['nullable', 'string', 'in:hero,header_seo,kuota,tahapan,faq,footer,kebijakan,theme'],
            'action_type' => ['nullable', 'string', 'in:draft,preview,publish'],
        ];

        if ($section === 'header_seo') {
            return array_merge($commonRules, [
                'meta_title' => ['required', 'string', 'max:255'],
                'meta_desc' => ['required', 'string', 'max:500'],
                'header_brand_name' => ['required', 'string', 'max:50'],
                'header_brand_sub' => ['required', 'string', 'max:100'],
                'header_nav_json' => ['nullable', 'string'],
                'nav_items' => ['nullable', 'array'],
                'nav_items.*.label' => ['nullable', 'string'],
                'nav_items.*.url' => ['nullable', 'string'],
            ]);
        }

        if ($section === 'kuota') {
            return array_merge($commonRules, [
                'kuota_heading' => ['required', 'string', 'max:255'],
                'kuota_subheading' => ['required', 'string', 'max:500'],
                'kuota_q1_label' => ['required', 'string', 'max:100'],
                'kuota_pill_smk' => ['required', 'string', 'max:100'],
                'kuota_pill_mhs' => ['required', 'string', 'max:100'],
                'kuota_q2_label' => ['required', 'string', 'max:100'],
                'kuota_empty_note' => ['nullable', 'string', 'max:255'],
                'kuota_q3_label' => ['required', 'string', 'max:100'],
                'kuota_pill_mandiri' => ['required', 'string', 'max:100'],
                'kuota_pill_tim' => ['required', 'string', 'max:100'],
                'kuota_team_label' => ['required', 'string', 'max:150'],
                'kuota_team_note' => ['required', 'string', 'max:500'],
                'kuota_gauge_desc' => ['required', 'string', 'max:255'],
                'kuota_legend_tersedia' => ['required', 'string', 'max:50'],
                'kuota_legend_hampir' => ['required', 'string', 'max:50'],
                'kuota_legend_penuh' => ['required', 'string', 'max:50'],
                'kuota_btn_tersedia' => ['required', 'string', 'max:100'],
                'kuota_btn_waiting' => ['required', 'string', 'max:100'],
                'kuota_btn_tutup' => ['required', 'string', 'max:100'],
            ]);
        }

        if ($section === 'tahapan') {
            return array_merge($commonRules, [
                'tahapan_heading' => ['required', 'string', 'max:255'],
                'tahapan_summary' => ['required', 'string', 'max:2000'],
                'step1_title' => ['required', 'string', 'max:100'],
                'step1_desc' => ['required', 'string', 'max:500'],
                'step2_title' => ['required', 'string', 'max:100'],
                'step2_desc' => ['required', 'string', 'max:500'],
                'step3_title' => ['required', 'string', 'max:100'],
                'step3_desc' => ['required', 'string', 'max:500'],
                'step4_title' => ['required', 'string', 'max:100'],
                'step4_desc' => ['required', 'string', 'max:500'],
                'tahapan_btn_pdf' => ['nullable', 'string', 'max:100'],
                'tahapan_btn_peta' => ['nullable', 'string', 'max:100'],
            ]);
        }

        if ($section === 'faq') {
            $hasVisualFaq = $this->has('faq_categories') && is_array($this->input('faq_categories'));

            return array_merge($commonRules, [
                'faq_heading' => ['required', 'string', 'max:255'],
                'faq_json' => [$hasVisualFaq ? 'nullable' : 'required', 'string'],
                'faq_categories' => ['nullable', 'array'],
            ]);
        }

        if ($section === 'footer') {
            return array_merge($commonRules, [
                'footer_alamat' => ['required', 'string', 'max:500'],
                'footer_telepon' => ['required', 'string', 'max:100'],
                'footer_email' => ['required', 'string', 'email', 'max:150'],
                'footer_jam_layanan' => ['required', 'string', 'max:255'],
                'footer_copyright' => ['required', 'string', 'max:255'],
                'footer_medsos_json' => ['nullable', 'string'],
                'footer_medsos' => ['nullable', 'array'],
                'footer_tautan_json' => ['nullable', 'string'],
                'footer_tautan' => ['nullable', 'array'],
                'footer_akun_json' => ['nullable', 'string'],
                'footer_akun' => ['nullable', 'array'],
            ]);
        }

        if ($section === 'kebijakan') {
            $hasVisualKebijakan = $this->has('kebijakan_sections') && is_array($this->input('kebijakan_sections'));

            return array_merge($commonRules, [
                'kebijakan_badge' => ['required', 'string', 'max:150'],
                'kebijakan_title' => ['required', 'string', 'max:255'],
                'kebijakan_updated_at' => ['required', 'string', 'max:255'],
                'kebijakan_sections_json' => [$hasVisualKebijakan ? 'nullable' : 'required', 'string'],
                'kebijakan_sections' => ['nullable', 'array'],
            ]);
        }

        if ($section === 'theme') {
            return array_merge($commonRules, [
                'theme_primary_color' => ['required', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
                'theme_navy_dark' => ['required', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
                'theme_accent_color' => ['required', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
                'theme_gold_color' => ['required', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
                'theme_bg_color' => ['required', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
                'theme_font_display' => ['required', 'string', 'in:Lexend,Plus Jakarta Sans,Inter,Montserrat,Poppins'],
                'theme_font_body' => ['required', 'string', 'in:Source Sans 3,Inter,Roboto,Open Sans,Nunito Sans'],
                'theme_border_radius' => ['required', 'string', 'in:0px,6px,12px,16px,24px'],
                'theme_btn_style' => ['required', 'string', 'in:rounded,pill,sharp'],
            ]);
        }

        // Default: Section 'hero'
        return array_merge($commonRules, [
            'hero_kicker' => ['nullable', 'string', 'max:100'],
            'hero_title' => ['required', 'string', 'max:255'],
            'hero_year' => ['nullable', 'string', 'max:50'],
            'hero_period_info' => ['nullable', 'string', 'max:255'],
            'hero_cta_text' => ['nullable', 'string', 'max:50'],
            'label_persyaratan' => ['nullable', 'string', 'max:100'],
            'persyaratan_umum' => ['nullable', 'string', 'max:2000'],
            'label_dokumen' => ['nullable', 'string', 'max:100'],
            'dokumen_pendaftaran' => ['nullable', 'string', 'max:2000'],
            'hero_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'panduan_pdf' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
        ]);
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'hero_image.max' => 'Ukuran file foto banner maksimal 2 MB.',
            'hero_image.mimes' => 'Format file foto banner harus berupa JPG, PNG, atau WEBP.',
            'panduan_pdf.max' => 'Ukuran file dokumen panduan maksimal 5 MB.',
            'panduan_pdf.mimes' => 'Format file dokumen panduan harus berupa PDF.',
        ];
    }
}
