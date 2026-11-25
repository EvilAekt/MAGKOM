<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Penempatan;

use App\Models\Penempatan;
use Illuminate\Foundation\Http\FormRequest;

class UploadSuratSelesaiRequest extends FormRequest
{
    /**
     * Tentukan otorisasi permintaan: hanya Admin yang berhak mengunggah surat selesai (SEC-12).
     */
    public function authorize(): bool
    {
        return (bool) $this->user()?->can('manage', Penempatan::class);
    }

    /**
     * Aturan validasi pengunggahan berkas surat selesai magang.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'file_surat' => ['required', 'file', 'mimes:pdf', 'max:2048'],
        ];
    }

    /**
     * Pesan validasi khusus berbahasa Indonesia yang ramah pengguna.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'file_surat.required' => 'Berkas surat selesai magang wajib dipilih.',
            'file_surat.file' => 'Berkas yang diunggah harus berupa file yang valid.',
            'file_surat.mimes' => 'Format berkas harus berupa dokumen PDF.',
            'file_surat.max' => 'Ukuran berkas surat selesai tidak boleh melebihi 2MB.',
        ];
    }
}
