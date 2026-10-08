<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\StatusKetersediaanEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BulanMagangRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSuperAdmin() ?? false;
    }

    public function rules(): array
    {
        $bulanId = $this->route('bulan')?->id ?? $this->route('bulan');

        return [
            'bulan_mulai' => [
                'required',
                'date',
                Rule::unique('bulan_magang', 'bulan_mulai')->ignore($bulanId),
            ],
            'tgl_buka' => ['required', 'date', 'before_or_equal:tgl_tutup'],
            'tgl_tutup' => ['required', 'date', 'after_or_equal:tgl_buka'],
            'override_status' => [
                'nullable',
                Rule::in(array_map(fn ($case) => $case->value, StatusKetersediaanEnum::cases())),
            ],
            'aktif' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'bulan_mulai.required' => 'Bulan mulai wajib ditentukan.',
            'bulan_mulai.unique' => 'Bulan magang untuk periode ini sudah pernah dibuat.',
            'tgl_buka.required' => 'Tanggal buka pendaftaran wajib diisi.',
            'tgl_buka.before_or_equal' => 'Tanggal buka tidak boleh melewati tanggal tutup.',
            'tgl_tutup.required' => 'Tanggal tutup pendaftaran wajib diisi.',
            'tgl_tutup.after_or_equal' => 'Tanggal tutup harus setelah atau sama dengan tanggal buka.',
        ];
    }
}
