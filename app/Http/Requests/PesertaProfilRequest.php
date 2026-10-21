<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\JenjangEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PesertaProfilRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isPeserta() ?? false;
    }

    public function rules(): array
    {
        $rules = [
            'nim_nisn' => ['required', 'string', 'max:20'],
            'institusi' => ['required', 'string', 'max:150'],
            'jurusan_id' => ['required', 'exists:jurusan,id'],
            'jenjang' => [
                'required',
                Rule::in(array_map(fn ($case) => $case->value, JenjangEnum::cases())),
            ],
            'no_hp' => ['required', 'string', 'max:20', 'regex:/^[0-9\+\-\s]+$/'],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'hapus_foto' => ['nullable', 'boolean'],
            'nama_kontak_darurat' => ['nullable', 'string', 'max:100'],
            'no_kontak_darurat' => ['nullable', 'string', 'max:20', 'regex:/^[0-9\+\-\s]+$/'],
            'hubungan_kontak_darurat' => ['nullable', 'string', 'max:50'],
            'persetujuan_wali' => ['nullable'],
        ];

        if ($this->input('jenjang') === JenjangEnum::Smk->value && $this->has('persetujuan_wali')) {
            $rules['persetujuan_wali'] = ['accepted'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'nim_nisn.required' => 'NIM / NISN wajib diisi.',
            'institusi.required' => 'Nama sekolah / universitas wajib diisi.',
            'jurusan_id.required' => 'Jurusan wajib dipilih.',
            'jurusan_id.exists' => 'Jurusan tidak valid.',
            'jenjang.required' => 'Jenjang pendidikan wajib dipilih.',
            'no_hp.required' => 'Nomor WhatsApp / telepon wajib diisi.',
            'no_hp.regex' => 'Format nomor WhatsApp / telepon tidak valid.',
            'foto.image' => 'Berkas foto profil harus berupa gambar.',
            'foto.mimes' => 'Format foto profil harus berupa JPG, JPEG, PNG, atau WEBP.',
            'foto.max' => 'Ukuran berkas foto profil tidak boleh melebihi 2 MB.',
            'no_kontak_darurat.regex' => 'Format nomor kontak darurat tidak valid.',
            'persetujuan_wali.required' => 'Calon peserta jenjang SMK wajib menyetujui pernyataan persetujuan orang tua / wali sesuai UU PDP.',
            'persetujuan_wali.accepted' => 'Pernyataan persetujuan orang tua / wali harus disetujui.',
        ];
    }
}
