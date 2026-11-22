<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\RoleEnum;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAturanBimbinganRequest extends FormRequest
{
    /**
     * Memastikan hanya role Pembimbing yang dapat memperbarui aturan bimbingan dan berkas.
     */
    public function authorize(): bool
    {
        return $this->user()?->role === RoleEnum::Pembimbing;
    }

    /**
     * Aturan validasi parameter pelaksanaan, tenggat tugas, dan dokumen magang.
     * Catatan: Parameter batas mundur logbook telah dihilangkan dari antarmuka.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'berkas_maks_mb' => ['required', 'integer', 'min:1', 'max:20'],
            'deadline_mendekati_hari' => ['required', 'integer', 'min:1', 'max:14'],
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
            'berkas_maks_mb.required' => 'Batas ukuran maksimal berkas upload wajib diisi.',
            'berkas_maks_mb.integer' => 'Batas ukuran maksimal berkas harus berupa angka.',
            'berkas_maks_mb.min' => 'Batas ukuran berkas minimal 1 MB.',
            'berkas_maks_mb.max' => 'Batas ukuran berkas maksimal 20 MB.',
            'deadline_mendekati_hari.required' => 'Peringatan mendekati deadline tugas wajib diisi.',
            'deadline_mendekati_hari.integer' => 'Peringatan mendekati deadline tugas harus berupa angka.',
            'deadline_mendekati_hari.min' => 'Peringatan deadline tugas minimal 1 hari.',
            'deadline_mendekati_hari.max' => 'Peringatan deadline tugas maksimal 14 hari.',
        ];
    }
}
