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
     * Mengirim tautan reset kata sandi dengan respon seragam anti-enumeration (AC-02-6).
     */
    public function sendResetLink(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $email = $request->input('email');
        $user = User::where('email', $email)->first();

        // Pesan seragam wajib (AC-02-6)
        $pesanSeragam = 'Jika alamat email terdaftar sebagai peserta aktif, tautan pemulihan kata sandi telah dikirimkan ke kotak masuk Anda.';

        // Hanya kirimkan email jika user ada, status aktif, dan rolenya Peserta (staf dilarang reset lewat flow ini)
        if ($user && $user->status_aktif && $user->role === RoleEnum::Peserta) {
            Password::broker()->sendResetLink(['email' => $email]);

            $this->auditService->catat(
                tipe: TipeAuditEnum::Auth,
                tabel: 'users',
                idData: $user->id,
                detail: [
                    'aksi' => 'Permintaan tautan reset kata sandi peserta',
                    'email' => $email,
                ]
            );
        }

        return back()->with('status', $pesanSeragam);
    }

    /**
     * Menampilkan formulir pembuatan kata sandi baru (AC-02-6).
     */
    public function showReset(Request $request, string $token): View
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->input('email'),
        ]);
    }

    /**
     * Memproses reset kata sandi peserta (AC-02-6).
     */
    public function reset(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed', 'regex:/^(?=.*[a-zA-Z])(?=.*\d)/'],
        ], [
            'password.regex' => 'Kata sandi baru harus mengandung kombinasi huruf dan angka.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $status = Password::broker()->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                // Pastikan bukan akun staf yang di-reset lewat token
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
                    ]
                );
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('success', 'Kata sandi Anda berhasil diperbarui. Silakan masuk.');
        }

        return back()->withInput($request->only('email'))
            ->withErrors(['email' => __($status)]);
    }
}
