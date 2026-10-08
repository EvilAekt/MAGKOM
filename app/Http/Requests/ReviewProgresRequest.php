<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\StatusReviewProgresEnum;
use Illuminate\Foundation\Http\FormRequest;
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
            'feedback' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
