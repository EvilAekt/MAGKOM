<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\StatusLogbookEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class ReviewLogbookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', new Enum(StatusLogbookEnum::class)],
            'catatan_pembimbing' => [
                Rule::requiredIf(fn () => $this->input('status') === StatusLogbookEnum::PerluRevisi->value),
                'nullable',
                'string',
                'min:5',
                'max:2000',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'catatan_pembimbing.required' => 'Catatan pembimbing wajib diisi ketika meminta peserta merevisi logbook harian (AC-09-4).',
            'catatan_pembimbing.min' => 'Catatan pembimbing untuk evaluasi revisi minimal berisi 5 karakter.',
        ];
    }
}
