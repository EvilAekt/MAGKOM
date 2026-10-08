<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\StatusLogbookEnum;
use Illuminate\Foundation\Http\FormRequest;
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
            'catatan_pembimbing' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
