<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\RoleEnum;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PenempatanManualRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() || $this->user()?->isSuperAdmin();
    }

    protected function prepareForValidation(): void
    {
        $tglMulai = $this->input('tgl_mulai');
        $tglSelesai = $this->input('tgl_selesai');
        $bulanMulai = $this->input('bulan_mulai');
        $durasiBulan = $this->input('durasi_bulan');

        $updates = [];

        // 1. Normalisasi bulan_mulai dari tgl_mulai jika bulan_mulai kosong
        if (empty($bulanMulai) && ! empty($tglMulai)) {
            try {
                $updates['bulan_mulai'] = Carbon::parse($tglMulai)->startOfMonth()->toDateString();
            } catch (\Exception $e) {
                // Biarkan validasi default menangkap format tanggal tidak valid
            }
        } elseif (! empty($bulanMulai) && empty($tglMulai)) {
            $updates['tgl_mulai'] = $bulanMulai;
        }

        // 2. Normalisasi durasi_bulan jika kosong namun tgl_mulai dan tgl_selesai ada
        if (empty($durasiBulan) && ! empty($tglMulai) && ! empty($tglSelesai)) {
            try {
                $start = Carbon::parse($tglMulai);
                $end = Carbon::parse($tglSelesai);
                $months = max(1, min(6, (int) round($start->floatDiffInMonths($end))));
                $updates['durasi_bulan'] = $months;
            } catch (\Exception $e) {
                $updates['durasi_bulan'] = 3;
            }
        } elseif (empty($durasiBulan)) {
            $updates['durasi_bulan'] = 3; // Default 3 bulan
        }

        if (! empty($updates)) {
            $this->merge($updates);
        }
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
            'tgl_mulai' => ['nullable', 'date'],
            'tgl_selesai' => ['nullable', 'date'],
            'password' => ['nullable', 'string', 'min:8'],
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
            $rules['jurusan_id'] = ['required', 'exists:jurusan,id'];
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
            'jurusan_id.required' => 'Jurusan asal wajib dipilih.',
        ];
    }
}
