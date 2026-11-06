<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\StatusLogbookEnum;
use App\Models\Logbook;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class ReviewLogbookRequest extends FormRequest
{
    public function authorize(): bool
    {
        $logbook = $this->route('logbook');
        if ($logbook instanceof Logbook) {
            return $this->user()?->can('review', $logbook) ?? false;
        }

        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('catatan') && ! $this->has('catatan_pembimbing')) {
            $this->merge([
                'catatan_pembimbing' => $this->input('catatan'),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'catatan_pembimbing' => [
                'required',
                'string',
                'min:3',
                'max:2000',
            ],
            'status' => ['sometimes', 'nullable', new Enum(StatusLogbookEnum::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'catatan_pembimbing.required' => 'Catatan pembimbing wajib diisi saat memberikan catatan pada logbook peserta.',
            'catatan_pembimbing.min' => 'Catatan pembimbing minimal berisi 3 karakter.',
            'catatan_pembimbing.max' => 'Catatan pembimbing maksimal berisi 2000 karakter.',
        ];
    }
}
