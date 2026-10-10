<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Support\Pengaturan;
use Illuminate\Foundation\Http\FormRequest;

class UnggahDokumenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isPeserta() ?? false;
    }

    public function rules(): array
    {
        $maksKb = Pengaturan::int('berkas_maks_mb', 2) * 1024;

        return [
            'jenis_dokumen_id' => ['required', 'exists:jenis_dokumen,id'],
            'file' => [
                'required',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                "max:{$maksKb}",
            ],
        ];
    }

    public function messages(): array
    {
        $maksMb = Pengaturan::int('berkas_maks_mb', 2);

        return [
            'jenis_dokumen_id.required' => 'Jenis dokumen persyaratan wajib dipilih.',
            'jenis_dokumen_id.exists' => 'Jenis dokumen tidak valid.',
            'file.required' => 'Berkas dokumen wajib diunggah.',
            'file.mimes' => 'Format berkas hanya boleh berupa PDF, JPG, JPEG, atau PNG.',
            'file.max' => "Ukuran berkas melebihi batas maksimal {$maksMb} MB.",
        ];
    }
}
