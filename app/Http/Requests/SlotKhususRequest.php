<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\SlotKhusus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class SlotKhususRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSuperAdmin() ?? false;
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
            'bulan_mulai.unique' => 'Slot khusus untuk bulan ini sudah ada.',
            'jumlah.required' => 'Jumlah slot khusus wajib diisi.',
            'jumlah.min' => 'Jumlah slot khusus tidak boleh negatif.',
        ];
    }
}
