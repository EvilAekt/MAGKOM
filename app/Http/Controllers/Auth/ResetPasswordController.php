<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Enums\RoleEnum;
use App\Enums\TipeAuditEnum;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ResetPasswordController extends Controller
{
    public function __construct(
        protected AuditService $auditService
    ) {}

    /**
     * Menampilkan form permohonan reset kata sandi (AC-02-6).
     */
    public function showForgot(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Mengirim tautan reset kata sandi dengan respon seragam anti-enumeration (AC-02-6 & TASK 2).
     */
    public function sendResetLink(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email:rfc'],
        ], [
            'email.email' => 'Format email tidak valid. Harap masukkan alamat email lengkap (contoh: nama@gmail.com).',
        ]);

        $email = strtolower(trim((string) $request->input('email')));
        $user = User::where('email', $email)->first();

        // Pesan respons generik seragam anti-user-enumeration (TASK 2 & AC-02-6)
        $pesanSeragam = 'Jika alamat email terdaftar pada sistem, kami akan mengirimkan instruksi untuk mengatur ulang password.';

        // Hanya kirimkan email jika user ada, status aktif, dan rolenya Peserta (mahasiswa magang)
        if ($user && $user->status_aktif && $user->role === RoleEnum::Peserta) {
            try {
                Password::broker()->sendResetLink(['email' => $email]);

                $this->auditService->catat(
                    tipe: TipeAuditEnum::Auth,
                    tabel: 'users',
                    idData: $user->id,
                    detail: [
                        'aksi' => 'Permintaan tautan reset kata sandi peserta',
                        'email' => $email,
                        'ip_address' => $request->ip(),
                        'user_agent' => substr((string) $request->userAgent(), 0, 255),
                    ]
                );

                if (config('app.debug')) {
                    Log::info("[Auth] Tautan reset password berhasil diproses untuk email: {$email}");
                }
            } catch (\Throwable $e) {
                Log::error("[Auth] Gagal mengirimkan email reset password via SMTP: {$e->getMessage()}", [
                    'email' => $email,
                    'exception' => $e,
                ]);

                $this->auditService->catat(
                    tipe: TipeAuditEnum::Auth,
                    tabel: 'users',
                    idData: $user->id,
                    detail: [
                        'aksi' => 'Permintaan tautan reset kata sandi gagal kirim SMTP',
                        'email' => $email,
                        'error' => $e->getMessage(),
                    ]
                );
            }
        }

        return back()->with('status', $pesanSeragam);
    }

    /**
     * Menampilkan formulir pembuatan kata sandi baru (AC-02-6 & TASK 3).
     */
    public function showReset(Request $request, string $token): View
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->input('email'),
        ]);
    }

    /**
     * Memproses reset kata sandi peserta (AC-02-6 & TASK 2).
     */
    public function reset(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'string', 'email:rfc'],
            'password' => ['required', 'string', 'min:8', 'confirmed', 'regex:/^(?=.*[a-zA-Z])(?=.*\d)/'],
        ], [
            'email.email' => 'Format email tidak valid.',
            'password.min' => 'Kata sandi baru minimal 8 karakter.',
            'password.regex' => 'Kata sandi baru harus mengandung kombinasi huruf dan angka.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $credentials = [
            'email' => strtolower(trim((string) $request->input('email'))),
            'password' => $request->input('password'),
            'password_confirmation' => $request->input('password_confirmation'),
            'token' => $request->input('token'),
        ];

        $status = Password::broker()->reset(
            $credentials,
            function (User $user, string $password) use ($request) {
                // Keamanan ketat: hanya izinkan akun mahasiswa/peserta magang melalui public reset
                if ($user->role !== RoleEnum::Peserta) {
                    return;
                }

                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                $this->auditService->catat(
                    tipe: TipeAuditEnum::Auth,
                    tabel: 'users',
                    idData: $user->id,
                    detail: [
                        'aksi' => 'Reset kata sandi peserta berhasil dilakukan via token email',
                        'email' => $user->email,
                        'ip_address' => $request->ip(),
                        'user_agent' => substr((string) $request->userAgent(), 0, 255),
                    ]
                );
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('status', 'Password Anda berhasil diperbarui. Silakan login dengan password baru.');
        }

        return back()->withInput($request->only('email'))
            ->withErrors(['email' => __($status)]);
    }
}
