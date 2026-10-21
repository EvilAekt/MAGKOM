<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\RoleEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TetapkanPenempatanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() || $this->user()?->isSuperAdmin();
    }

    public function rules(): array
    {
        return [
            'pendaftaran_id' => [
                'required',
                'integer',
                'exists:pendaftaran,id',
            ],
            'pembimbing_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where(function ($query) {
                    $query->where('role', RoleEnum::Pembimbing->value)
                        ->where('status_aktif', true);
                }),
            ],
            'bidang_id' => [
                'nullable',
                'integer',
                'exists:bidang,id',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'pendaftaran_id.required' => 'Data pendaftaran peserta wajib dipilih.',
            'pendaftaran_id.exists' => 'Data pendaftaran peserta tidak ditemukan di sistem.',
            'pembimbing_id.required' => 'Pembimbing lapangan wajib dipilih.',
            'pembimbing_id.exists' => 'Pembimbing lapangan yang dipilih tidak valid atau sedang tidak aktif.',
            'bidang_id.exists' => 'Bidang dinas yang dipilih tidak valid.',
        ];
    }
}
