<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class JurusanRequest extends FormRequest
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
        if ($this->has('nama_jurusan') && is_string($this->nama_jurusan)) {
            $this->merge([
                'nama_jurusan' => trim($this->nama_jurusan),
            ]);
        }
    }

    /**
     * Aturan validasi master jurusan.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $jurusan = $this->route('jurusan');
        $jurusanId = is_object($jurusan) ? $jurusan->id : $jurusan;

        return [
            'nama_jurusan' => [
                'required',
                'string',
                'max:255',
                Rule::unique('jurusan', 'nama_jurusan')->ignore($jurusanId),
            ],
            'kategori' => ['required', Rule::in(['smk', 'mahasiswa'])],
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
            'nama_jurusan.required' => 'Nama program studi / jurusan wajib diisi.',
            'nama_jurusan.unique' => 'Nama jurusan ini sudah terdaftar dalam sistem.',
            'nama_jurusan.max' => 'Nama jurusan maksimal 255 karakter.',
            'kategori.required' => 'Kategori pendidikan wajib dipilih.',
            'kategori.in' => 'Kategori pendidikan harus SMK atau Perguruan Tinggi (Mahasiswa).',
        ];
    }
}
