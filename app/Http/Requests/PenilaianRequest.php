<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PenilaianRequest extends FormRequest
{
    public function authorize(): bool
    {
        $penempatan = $this->route('penempatan');
        if ($penempatan) {
            return $this->user()?->can('manage', [\App\Models\Penilaian::class, $penempatan]) ?? false;
        }

        return true;
    }

    public function rules(): array
    {
        return [
            'skor' => ['required', 'array', 'min:1'],
            'skor.*' => ['required', 'integer', 'min:0', 'max:100'],
            'catatan' => ['nullable', 'string', 'max:3000'],
            'is_final' => ['nullable', 'boolean'],
            'alasan' => ['nullable', 'string', 'min:10'],
        ];
    }

    public function messages(): array
    {
        return [
            'skor.required' => 'Skor kriteria penilaian wajib disertakan.',
            'skor.*.min' => 'Skor tidak boleh bernilai kurang dari 0.',
            'skor.*.max' => 'Skor tidak boleh melebihi 100.',
            'alasan.min' => 'Alasan perubahan nilai final minimal 10 karakter.',
        ];
    }
}
