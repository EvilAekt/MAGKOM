<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class KeputusanPendaftaranRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() || $this->user()?->isSuperAdmin();
    }

    public function rules(): array
    {
        return [
            'keputusan' => ['required', Rule::in(['terima', 'tolak', 'perlu_perbaikan'])],
            'catatan' => [
                Rule::requiredIf(fn () => in_array($this->input('keputusan'), ['tolak', 'perlu_perbaikan'], true)),
                'nullable',
                'string',
                'max:1000',
            ],
            'pembimbing_id' => [
                Rule::requiredIf(fn () => $this->input('keputusan') === 'terima'),
                'nullable',
                Rule::exists('users', 'id')->where(function ($query) {
                    $query->where('role', RoleEnum::Pembimbing->value)
                        ->where('status_aktif', true);
                }),
            ],
            'bidang_id' => ['nullable', 'exists:bidang,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'keputusan.required' => 'Keputusan verifikasi wajib dipilih.',
            'catatan.required' => 'Catatan wajib disertakan saat menolak atau meminta perbaikan berkas.',
            'pembimbing_id.required' => 'Pembimbing lapangan wajib ditentukan saat menerima pendaftaran.',
            'pembimbing_id.exists' => 'Pembimbing yang dipilih tidak valid atau sedang tidak aktif.',
        ];
    }
}
