<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\RoleEnum;
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
            'keputusan' => ['required', Rule::in(['terima', 'tolak'])],
            'catatan' => [
                Rule::requiredIf(fn () => $this->input('keputusan') === 'tolak'),
                'nullable',
                'string',
                Rule::when(
                    fn () => $this->input('keputusan') === 'tolak',
                    ['min:10']
                ),
                'max:1000',
            ],
            'pembimbing_id' => [
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
            'keputusan.in' => 'Pilihan keputusan verifikasi hanya menerima atau menolak berkas.',
            'catatan.required' => 'Alasan penolakan wajib disertakan saat menolak pendaftaran.',
            'catatan.min' => 'Alasan penolakan minimal berisi 10 karakter sesuai standar verifikasi (AC-04-3).',
            'pembimbing_id.exists' => 'Pembimbing yang dipilih tidak valid atau sedang tidak aktif.',
        ];
    }
}
