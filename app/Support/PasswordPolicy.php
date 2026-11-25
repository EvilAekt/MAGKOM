<?php

declare(strict_types=1);

namespace App\Support;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Validation\Rules\Password;

class PasswordPolicy
{
    /**
     * Menghasilkan aturan validasi kata sandi untuk peran staf kedinasan (SuperAdmin, Admin, Pembimbing).
     * Mensyaratkan minimal 8 karakter dengan huruf besar, huruf kecil, angka, dan simbol (AUTH-001).
     *
     * @return array<int, mixed>
     */
    public static function staffRules(bool $confirmed = true, bool $required = true): array
    {
        $rules = [
            $required ? 'required' : 'nullable',
            'string',
        ];

        if ($confirmed) {
            $rules[] = 'confirmed';
        }

        $rules[] = Password::min(8)
            ->letters()
            ->mixedCase()
            ->numbers()
            ->symbols();

        return $rules;
    }

    /**
     * Menghasilkan aturan validasi kata sandi untuk peserta magang / pendaftar umum.
     * Mensyaratkan minimal 8 karakter dengan kombinasi huruf dan angka.
     *
     * @return array<int, mixed>
     */
    public static function pesertaRules(bool $confirmed = true, bool $required = true): array
    {
        $rules = [
            $required ? 'required' : 'nullable',
            'string',
            'min:8',
            'regex:/^(?=.*[a-zA-Z])(?=.*\d)/',
        ];

        if ($confirmed) {
            $rules[] = 'confirmed';
        }

        return $rules;
    }

    /**
     * Menghasilkan aturan validasi kata sandi berdasarkan model pengguna.
     *
     * @return array<int, mixed>
     */
    public static function forUser(?User $user, bool $confirmed = true, bool $required = true): array
    {
        if ($user && ($user->isSuperAdmin() || $user->role === RoleEnum::Admin || $user->role === RoleEnum::Pembimbing)) {
            return self::staffRules($confirmed, $required);
        }

        return self::pesertaRules($confirmed, $required);
    }

    /**
     * Menghasilkan aturan validasi kata sandi saat reset password berdasarkan alamat email target.
     *
     * @return array<int, mixed>
     */
    public static function forEmail(string $email, bool $confirmed = true): array
    {
        $user = User::where('email', strtolower(trim($email)))->first();

        return self::forUser($user, $confirmed, true);
    }

    /**
     * Kumpulan pesan validasi kustom bahasa Indonesia yang seragam untuk kebijakan kata sandi.
     *
     * @return array<string, string>
     */
    public static function messages(): array
    {
        return [
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 12 karakter untuk staf kedinasan atau 8 karakter untuk peserta.',
            'password.regex' => 'Kata sandi harus mengandung kombinasi huruf dan angka.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'password_baru.required' => 'Kata sandi baru wajib diisi.',
            'password_baru.min' => 'Kata sandi baru minimal 12 karakter untuk akun staf kedinasan.',
            'password_baru.regex' => 'Kata sandi baru harus mengandung kombinasi huruf dan angka.',
        ];
    }
}
