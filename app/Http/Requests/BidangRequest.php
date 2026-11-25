<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BidangRequest extends FormRequest
{
    /**
     * Otorisasi hanya untuk Administrator (SuperAdmin read-only sesuai SEC-12).
     */
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /**
     * Menyiapkan data sebelum validasi (sanitasi trim spasi).
     */
    protected function prepareForValidation(): void
    {
        $mergeData = [];

        if ($this->has('nama_bidang') && is_string($this->nama_bidang)) {
            $mergeData['nama_bidang'] = trim($this->nama_bidang);
        }

        if ($this->has('deskripsi') && is_string($this->deskripsi)) {
            $trimmed = trim($this->deskripsi);
            $mergeData['deskripsi'] = $trimmed !== '' ? $trimmed : null;
        }

        if (! empty($mergeData)) {
            $this->merge($mergeData);
        }
    }

    /**
     * Aturan validasi master bidang dinas.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $bidang = $this->route('bidang');
        $bidangId = is_object($bidang) ? $bidang->id : $bidang;

        return [
            'nama_bidang' => [
                'required',
                'string',
                'max:255',
                Rule::unique('bidang', 'nama_bidang')->ignore($bidangId),
            ],
            'deskripsi' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Pesan kesalahan validasi dalam Bahasa Indonesia yang ramah pengguna.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nama_bidang.required' => 'Nama bidang dinas wajib diisi.',
            'nama_bidang.unique' => 'Nama bidang dinas ini sudah terdaftar dalam sistem.',
            'nama_bidang.max' => 'Nama bidang dinas maksimal 255 karakter.',
            'deskripsi.max' => 'Deskripsi tugas dan fungsi bidang maksimal 1000 karakter.',
        ];
    }
}
