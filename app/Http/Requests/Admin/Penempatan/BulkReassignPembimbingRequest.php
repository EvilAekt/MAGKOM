<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Penempatan;

use App\Enums\RoleEnum;
use App\Models\Penempatan;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BulkReassignPembimbingRequest extends FormRequest
{
    /**
     * Tentukan otorisasi permintaan: hanya Admin berhak mengelola bulk reassign (SEC-12).
     */
    public function authorize(): bool
    {
        return (bool) $this->user()?->can('manage', Penempatan::class);
    }

    /**
     * Aturan validasi pengalihan pembimbing massal.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'penempatan_ids' => ['required', 'array', 'min:1'],
            'penempatan_ids.*' => ['required', 'integer', 'distinct', 'exists:penempatan,id'],
            'pembimbing_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where(function ($query) {
                    $query->where('role', RoleEnum::Pembimbing->value)
                        ->where('status_aktif', true);
                }),
            ],
        ];
    }

    /**
     * Pesan validasi khusus.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'penempatan_ids.required' => 'Pilih minimal satu penempatan peserta untuk dialihkan.',
            'penempatan_ids.min' => 'Pilih minimal satu penempatan peserta untuk dialihkan.',
            'penempatan_ids.*.distinct' => 'Terdapat duplikasi ID penempatan peserta pada daftar pilihan.',
            'penempatan_ids.*.exists' => 'Satu atau lebih data penempatan peserta tidak ditemukan.',
            'pembimbing_id.required' => 'Pilih pembimbing baru tujuan.',
            'pembimbing_id.exists' => 'Pembimbing baru yang dipilih tidak valid atau sedang tidak aktif.',
        ];
    }
}
