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
        return [
            'peserta_id' => [
                'required',
                Rule::exists('users', 'id')->where(function ($query) {
                    $query->where('role', RoleEnum::Peserta->value);
                }),
            ],
            'slot_khusus_id' => ['required', 'exists:slot_khusus,id'],
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
    }

    public function messages(): array
    {
        return [
            'peserta_id.required' => 'Calon peserta wajib dipilih.',
            'slot_khusus_id.required' => 'Alokasi slot khusus wajib dipilih.',
            'pembimbing_id.required' => 'Pembimbing lapangan wajib ditentukan.',
            'bulan_mulai.required' => 'Bulan mulai magang wajib diisi.',
            'durasi_bulan.required' => 'Durasi magang wajib ditentukan.',
        ];
    }
}
