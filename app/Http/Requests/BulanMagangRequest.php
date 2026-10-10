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
            'tgl_buka' => ['required', 'date', 'before:tgl_tutup'],
            'tgl_tutup' => ['required', 'date', 'after:tgl_buka'],
            'override_status' => [
                'nullable',
                Rule::in(array_map(fn ($case) => $case->value, StatusKetersediaanEnum::cases())),
            ],
            'aktif' => ['sometimes', 'boolean'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->filled('bulan_mulai') && $this->filled('tgl_tutup')) {
                try {
                    $bulanMulai = \Carbon\Carbon::parse($this->input('bulan_mulai'))->startOfMonth();
                    $tglTutup = \Carbon\Carbon::parse($this->input('tgl_tutup'))->startOfDay();

                    if ($tglTutup->gt($bulanMulai)) {
                        $validator->errors()->add(
                            'tgl_tutup',
                            'Tanggal tutup pendaftaran tidak boleh melewati tanggal awal dimulainya periode magang (' . $bulanMulai->format('d/m/Y') . ') sesuai aturan instansi (Bab 8.2).'
                        );
                    }
                } catch (\Exception $e) {
                    // Ditangani oleh aturan dasar date
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'bulan_mulai.required' => 'Bulan mulai wajib ditentukan.',
            'bulan_mulai.unique' => 'Bulan magang untuk periode ini sudah pernah dibuat.',
            'tgl_buka.required' => 'Tanggal buka pendaftaran wajib diisi.',
            'tgl_buka.before' => 'Tanggal buka pendaftaran harus sebelum tanggal tutup pendaftaran.',
            'tgl_tutup.required' => 'Tanggal tutup pendaftaran wajib diisi.',
            'tgl_tutup.after' => 'Tanggal tutup pendaftaran harus setelah tanggal buka pendaftaran.',
        ];
    }
}
