<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\RoleEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PenempatanManualRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() || $this->user()?->isSuperAdmin();
    }

    public function rules(): array
    {
        $mode = $this->input('mode', 'terdaftar');
        $jalur = $this->input('jalur', 'khusus');

        $rules = [
            'mode' => ['nullable', 'in:terdaftar,baru'],
            'jalur' => ['nullable', 'in:khusus,umum'],
            'pembimbing_id' => [
                'required',
                Rule::exists('users', 'id')->where(function ($query) {
                    $query->where('role', RoleEnum::Pembimbing->value)
                        ->where('status_aktif', true);
                }),
            ],
            'bidang_id' => ['nullable', 'exists:bidang,id'],
            'bulan_mulai' => ['required', 'date'],
            'durasi_bulan' => ['required', 'integer', 'min:1', 'max:6'],
        ];

        // Validasi Alokasi Jalur
        if ($jalur === 'umum') {
            $rules['kuota_id'] = ['required', 'exists:kuota,id'];
            $rules['slot_khusus_id'] = ['nullable'];
        } else {
            $rules['slot_khusus_id'] = ['required', 'exists:slot_khusus,id'];
            $rules['kuota_id'] = ['nullable'];
        }

        // Validasi Mode Peserta
        if ($mode === 'baru') {
            $rules['nama'] = ['required', 'string', 'max:100'];
            $rules['email'] = ['required', 'email', 'max:255', 'unique:users,email'];
            $rules['nim_nisn'] = ['required', 'string', 'max:30'];
            $rules['institusi'] = ['required', 'string', 'max:100'];
            $rules['jurusan_id'] = ['nullable', 'exists:jurusan,id'];
            $rules['jenjang'] = ['required', 'in:smk,d3,s1'];
            $rules['no_hp'] = ['nullable', 'string', 'max:20'];
            $rules['peserta_id'] = ['nullable'];
        } else {
            $rules['peserta_id'] = [
                'required',
                Rule::exists('users', 'id')->where(function ($query) {
                    $query->where('role', RoleEnum::Peserta->value);
                }),
            ];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'peserta_id.required' => 'Calon peserta terdaftar wajib dipilih.',
            'slot_khusus_id.required' => 'Alokasi slot khusus wajib dipilih untuk jalur khusus.',
            'kuota_id.required' => 'Alokasi kuota jurusan wajib dipilih untuk jalur umum.',
            'pembimbing_id.required' => 'Pembimbing lapangan wajib ditentukan.',
            'bulan_mulai.required' => 'Bulan mulai magang wajib diisi.',
            'durasi_bulan.required' => 'Durasi magang wajib ditentukan.',
            'nama.required' => 'Nama peserta baru wajib diisi.',
            'email.required' => 'Email peserta baru wajib diisi.',
            'email.unique' => 'Email tersebut sudah terdaftar di sistem.',
            'nim_nisn.required' => 'NIM / NISN wajib diisi.',
            'institusi.required' => 'Asal institusi / kampus / sekolah wajib diisi.',
        ];
    }
}
