<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\RoleEnum;
use Illuminate\Foundation\Http\FormRequest;

class UpdateKebijakanMagangRequest extends FormRequest
{
    /**
     * Memastikan hanya role Administrasi (admin) yang dapat memperbarui kebijakan kuota.
     */
    public function authorize(): bool
    {
        return $this->user()?->role === RoleEnum::Admin;
    }

    /**
     * Aturan validasi parameter kebijakan kuota dan pendaftaran magang.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'kuota_umum_maks_per_bulan' => ['required', 'integer', 'min:1', 'max:100'],
            'slot_khusus_default' => ['required', 'integer', 'min:0', 'max:50'],
            'maks_peserta_aktif_per_pembimbing' => ['nullable', 'integer', 'min:1', 'max:50'],
            'maks_anggota_kelompok' => ['required', 'integer', 'min:1', 'max:10'],
            'durasi_magang_maks_bulan' => ['required', 'integer', 'min:1', 'max:12'],
            'sla_verifikasi_hari' => ['required', 'integer', 'min:1', 'max:30'],
        ];
    }

    /**
     * Pesan validasi dalam Bahasa Indonesia.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'kuota_umum_maks_per_bulan.required' => 'Batas kuota umum publik per bulan wajib diisi.',
            'kuota_umum_maks_per_bulan.integer' => 'Batas kuota umum publik per bulan harus berupa angka.',
            'kuota_umum_maks_per_bulan.min' => 'Batas kuota umum publik minimal 1 orang.',
            'kuota_umum_maks_per_bulan.max' => 'Batas kuota umum publik maksimal 100 orang.',
            'slot_khusus_default.required' => 'Default alokasi slot khusus wajib diisi.',
            'slot_khusus_default.integer' => 'Default alokasi slot khusus harus berupa angka.',
            'slot_khusus_default.min' => 'Default alokasi slot khusus minimal 0 orang.',
            'slot_khusus_default.max' => 'Default alokasi slot khusus maksimal 50 orang.',
            'maks_peserta_aktif_per_pembimbing.integer' => 'Beban maksimal mentee per pembimbing harus berupa angka.',
            'maks_peserta_aktif_per_pembimbing.min' => 'Beban maksimal mentee minimal 1 orang.',
            'maks_peserta_aktif_per_pembimbing.max' => 'Beban maksimal mentee maksimal 50 orang.',
            'maks_anggota_kelompok.required' => 'Batas maksimal anggota kelompok wajib diisi.',
            'maks_anggota_kelompok.integer' => 'Batas maksimal anggota kelompok harus berupa angka.',
            'maks_anggota_kelompok.min' => 'Batas maksimal anggota kelompok minimal 1 orang.',
            'maks_anggota_kelompok.max' => 'Batas maksimal anggota kelompok maksimal 10 orang.',
            'durasi_magang_maks_bulan.required' => 'Durasi magang maksimal wajib diisi.',
            'durasi_magang_maks_bulan.integer' => 'Durasi magang maksimal harus berupa angka.',
            'durasi_magang_maks_bulan.min' => 'Durasi magang maksimal minimal 1 bulan.',
            'durasi_magang_maks_bulan.max' => 'Durasi magang maksimal maksimal 12 bulan.',
            'sla_verifikasi_hari.required' => 'Target batas waktu SLA verifikasi berkas wajib diisi.',
            'sla_verifikasi_hari.integer' => 'Target batas waktu SLA verifikasi berkas harus berupa angka.',
            'sla_verifikasi_hari.min' => 'Target batas waktu SLA verifikasi berkas minimal 1 hari kerja.',
            'sla_verifikasi_hari.max' => 'Target batas waktu SLA verifikasi berkas maksimal 30 hari kerja.',
        ];
    }
}
