<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\KompleksitasEnum;
use App\Enums\StatusTugasEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class TugasRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'penempatan_id' => ['required', 'exists:penempatan,id'],
            'judul' => ['required', 'string', 'max:150'],
            'deskripsi' => ['nullable', 'string'],
            'kategori' => ['required', 'string', 'max:50'],
            'kompleksitas' => ['required', new Enum(KompleksitasEnum::class)],
            'deadline' => ['required', 'date'],
            'status' => ['nullable', new Enum(StatusTugasEnum::class)],
        ];
    }
}
