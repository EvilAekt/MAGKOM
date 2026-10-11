<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\StatusReviewProgresEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class ReviewProgresRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status_review' => ['required', new Enum(StatusReviewProgresEnum::class)],
            'feedback' => [
                Rule::requiredIf(fn () => $this->input('status_review') === StatusReviewProgresEnum::PerluRevisi->value),
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
            'feedback.required' => 'Umpan balik wajib diisi ketika meminta peserta merevisi laporan progres (AC-08-4).',
            'feedback.min' => 'Umpan balik evaluasi revisi minimal berisi 5 karakter.',
        ];
    }
}
