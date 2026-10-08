<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Kuota;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class KuotaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() || $this->user()?->isSuperAdmin();
    }

    public function rules(): array
    {
        $kuotaId = $this->route('kuota')?->id ?? $this->route('kuota');

        return [
            'bulan_magang_id' => ['required', 'exists:bulan_magang,id'],
            'jurusan_id' => [
                'required',
                'exists:jurusan,id',
                Rule::unique('kuota', 'jurusan_id')
                    ->where('bulan_magang_id', $this->input('bulan_magang_id'))
                    ->ignore($kuotaId),
            ],
            'jumlah' => ['required', 'integer', 'min:0', 'max:500'],
            'aktif' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * Validasi kustom tambahan untuk mencegah penurunan kuota di bawah jumlah terisi (AC-05-3).
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $routeKuota = $this->route('kuota');
            $kuota = $routeKuota instanceof Kuota ? $routeKuota : Kuota::find($routeKuota);
            $jumlahBaru = (int) $this->input('jumlah');

            if ($kuota && $jumlahBaru < $kuota->terisi) {
                $validator->errors()->add(
                    'jumlah',
                    "Jumlah kuota ({$jumlahBaru}) tidak boleh lebih kecil dari jumlah peserta yang telah diterima ({$kuota->terisi})."
                );
            }
        });
    }

    public function messages(): array
    {
        return [
            'bulan_magang_id.required' => 'Periode bulan magang wajib dipilih.',
            'bulan_magang_id.exists' => 'Periode bulan magang tidak valid.',
            'jurusan_id.required' => 'Jurusan wajib dipilih.',
            'jurusan_id.exists' => 'Jurusan tidak valid.',
            'jurusan_id.unique' => 'Kuota untuk jurusan dan bulan magang ini sudah terdaftar.',
            'jumlah.required' => 'Jumlah kuota wajib diisi.',
            'jumlah.min' => 'Jumlah kuota tidak boleh bernilai negatif.',
        ];
    }
}
