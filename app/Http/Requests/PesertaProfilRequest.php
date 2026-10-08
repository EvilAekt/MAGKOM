<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\JenjangEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PesertaProfilRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isPeserta() ?? false;
    }

    public function rules(): array
    {
        return [
            'nim_nisn' => ['required', 'string', 'max:20'],
            'institusi' => ['required', 'string', 'max:150'],
            'jurusan_id' => ['required', 'exists:jurusan,id'],
            'jenjang' => [
                'required',
                Rule::in(array_map(fn ($case) => $case->value, JenjangEnum::cases())),
            ],
            'no_hp' => ['required', 'string', 'max:20', 'regex:/^[0-9\+\-\s]+$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'nim_nisn.required' => 'NIM / NISN wajib diisi.',
            'institusi.required' => 'Nama sekolah / universitas wajib diisi.',
            'jurusan_id.required' => 'Jurusan wajib dipilih.',
            'jurusan_id.exists' => 'Jurusan tidak valid.',
            'jenjang.required' => 'Jenjang pendidikan wajib dipilih.',
            'no_hp.required' => 'Nomor WhatsApp / telepon wajib diisi.',
            'no_hp.regex' => 'Format nomor WhatsApp / telepon tidak valid.',
        ];
    }
}
