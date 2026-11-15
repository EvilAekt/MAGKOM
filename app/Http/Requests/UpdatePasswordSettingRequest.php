<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdatePasswordSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $isSuperAdmin = $this->user()?->isSuperAdmin() ?? false;

        $passwordRules = $isSuperAdmin
            ? ['required', 'string', 'confirmed', Password::min(12)->letters()->mixedCase()->numbers()->symbols()]
            : ['required', 'string', 'min:8', 'confirmed', 'regex:/^(?=.*[a-zA-Z])(?=.*\d)/'];

        return [
            'current_password' => ['required', 'string'],
            'password' => $passwordRules,
        ];
    }

    public function messages(): array
    {
        $isSuperAdmin = $this->user()?->isSuperAdmin() ?? false;

        return [
            'current_password.required' => 'Kata sandi saat ini wajib diisi.',
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => $isSuperAdmin ? 'Kata sandi baru SuperAdmin minimal 12 karakter.' : 'Kata sandi baru minimal harus 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
            'password.regex' => 'Kata sandi baru harus mengandung kombinasi huruf dan angka.',
        ];
    }
}
