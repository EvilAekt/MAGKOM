<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProgresLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'catatan' => ['required', 'string', 'min:5', 'max:3000'],
            'file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,zip', 'max:5120'],
        ];
    }
}
