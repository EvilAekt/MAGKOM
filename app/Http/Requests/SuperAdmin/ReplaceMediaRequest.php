<?php

declare(strict_types=1);

namespace App\Http\Requests\SuperAdmin;

use App\Services\LandingCmsStagingService;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class ReplaceMediaRequest extends FormRequest
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
            'file' => [
                'required',
                'file',
                'mimes:jpeg,jpg,png,webp,svg,pdf',
                'max:5120', // Maksimal 5 MB
            ],
        ];
    }

    /**
     * Pesan kesalahan khusus untuk aturan validasi.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'file.required' => 'Pilih berkas baru sebagai pengganti.',
            'file.file' => 'Berkas pengganti yang diunggah tidak valid.',
            'file.mimes' => 'Format berkas hanya boleh berupa JPG, PNG, WEBP, SVG, atau PDF.',
            'file.max' => 'Ukuran berkas maksimal adalah 5 MB.',
        ];
    }

    /**
     * Konfigurasi validator dengan pengecekan magic bytes signature.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $file = $this->file('file');
            if (! $file || ! $file->isValid()) {
                return;
            }

            $stagingService = app(LandingCmsStagingService::class);
            $sigCheck = $stagingService->validateFileSignature($file);

            if (! $sigCheck['valid']) {
                $validator->errors()->add('file', $sigCheck['error'] ?? 'Berkas pengganti tidak valid atau rusak.');
            }
        });
    }
}
