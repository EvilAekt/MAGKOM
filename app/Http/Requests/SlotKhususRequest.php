<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\SlotKhusus;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class SlotKhususRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /**
     * Menyiapkan data sebelum divalidasi, termasuk normalisasi bulan_mulai ke tanggal 1.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('bulan_mulai') && is_string($this->bulan_mulai)) {
            try {
                $normalized = Carbon::parse($this->bulan_mulai)->startOfMonth()->toDateString();
                $this->merge(['bulan_mulai' => $normalized]);
            } catch (\Throwable) {
                // Biarkan validator date menangani format yang tidak valid
            }
        }
    }

    public function rules(): array
    {
        $slotId = $this->route('slot_khusu')?->id ?? $this->route('slot_khusus')?->id ?? $this->route('slot');

        return [
            'bulan_mulai' => [
                'required',
                'date',
                Rule::unique('slot_khusus', 'bulan_mulai')->ignore($slotId),
            ],
            'jumlah' => ['required', 'integer', 'min:0', 'max:100'],
            'aktif' => ['sometimes', 'boolean'],
            'nama_instansi' => ['nullable', 'string', 'max:150'],
            'keterangan' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $slotId = $this->route('slot_khusu')?->id ?? $this->route('slot_khusus')?->id ?? $this->route('slot');
            if ($slotId) {
                $slot = SlotKhusus::find($slotId);
                $jumlahBaru = (int) $this->input('jumlah');

                if ($slot && $jumlahBaru < $slot->terisi) {
                    $validator->errors()->add(
                        'jumlah',
                        "Jumlah slot khusus ({$jumlahBaru}) tidak boleh lebih kecil dari slot yang telah terpakai ({$slot->terisi})."
                    );
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'bulan_mulai.required' => 'Bulan mulai wajib diisi.',
            'bulan_mulai.date' => 'Format bulan mulai tidak valid.',
            'bulan_mulai.unique' => 'Alokasi slot khusus untuk bulan ini sudah ada dalam sistem.',
            'jumlah.required' => 'Jumlah slot khusus wajib diisi.',
            'jumlah.integer' => 'Jumlah slot khusus harus berupa bilangan bulat.',
            'jumlah.min' => 'Jumlah slot khusus tidak boleh negatif.',
            'jumlah.max' => 'Jumlah slot khusus maksimal 100 kursi per bulan.',
        ];
    }
}
