<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StaffProfilRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna berhak mengirimkan request ini.
     */
    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->isStaf();
    }

    /**
     * Aturan validasi pembaruan profil staf.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:100'],
            'no_telp' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s()]+$/'],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'hapus_foto' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Pesan validasi kustom dalam Bahasa Indonesia.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nama.required' => 'Nama lengkap wajib diisi.',
            'nama.max' => 'Nama lengkap maksimal 100 karakter.',
            'no_telp.max' => 'Nomor telepon/WhatsApp maksimal 20 karakter.',
            'no_telp.regex' => 'Format nomor telepon/WhatsApp tidak valid.',
            'foto.image' => 'Berkas pasfoto harus berupa file gambar.',
            'foto.mimes' => 'Format pasfoto harus berupa JPEG, PNG, JPG, atau WebP.',
            'foto.max' => 'Ukuran berkas pasfoto maksimal 2 MB.',
        ];
    }
}
