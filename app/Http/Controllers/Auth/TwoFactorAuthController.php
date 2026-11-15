<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Enums\RoleEnum;
use App\Enums\TipeAuditEnum;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuditService;
use App\Services\TwoFactorAuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class TwoFactorAuthController extends Controller
{
    public function __construct(
        protected TwoFactorAuthService $twoFactorService,
        protected AuditService $auditService
    ) {}

    /**
     * Menampilkan halaman tantangan 2FA saat login (AUTH-002).
     */
    public function showChallenge(Request $request): View|RedirectResponse
    {
        if (! $request->session()->has('auth.2fa.user_id')) {
            return redirect()->route('login');
        }

        return view('auth.two-factor-challenge');
    }

    /**
     * Memverifikasi kode TOTP atau Recovery Code pada tantangan login (AUTH-002).
     */
    public function verifyChallenge(Request $request): RedirectResponse
    {
        $userId = $request->session()->get('auth.2fa.user_id');

        if (! $userId) {
            return redirect()->route('login');
        }

        $user = User::find($userId);
        if (! $user || ! $user->hasEnabledTwoFactor()) {
            $request->session()->forget(['auth.2fa.user_id', 'auth.2fa.remember']);

            return redirect()->route('login');
        }

        // 1. Rate Limiting Proteksi Brute-Force (Maks 5 percobaan per menit per pengguna)
        $throttleKey = "2fa_challenge:{$user->id}:{$request->ip()}";
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return back()->withErrors([
                'code' => "Terlalu banyak percobaan kode verifikasi yang salah. Silakan coba lagi dalam {$seconds} detik.",
            ]);
        }

        $code = $request->input('code');
        $recoveryCode = $request->input('recovery_code');
        $isValid = false;
        $isRecovery = false;

        // 2. Verifikasi Kode TOTP atau Recovery Code
        if (! empty($code)) {
            $isValid = $this->twoFactorService->verifyKey((string) $user->two_factor_secret, (string) $code);
        } elseif (! empty($recoveryCode)) {
            $isValid = $user->verifyRecoveryCode((string) $recoveryCode);
            $isRecovery = true;
        }

        if (! $isValid) {
            RateLimiter::hit($throttleKey, 60);

            $this->auditService->catat(
                tipe: TipeAuditEnum::Auth,
                tabel: 'users',
                idData: $user->id,
                nilaiLama: null,
                nilaiBaru: null,
                detail: [
                    'aksi' => '2fa.challenge_failed',
                    'is_recovery' => $isRecovery,
                    'ip_address' => $request->ip(),
                ],
                user: $user,
                channel: 'security-auth',
                severity: 'warning'
            );

            return back()->withErrors([
                $isRecovery ? 'recovery_code' : 'code' => 'Kode autentikasi yang Anda masukkan tidak valid.',
            ]);
        }

        // 3. Verifikasi Berhasil: Login resmi & bersihkan sesi 2FA
        RateLimiter::clear($throttleKey);
        $remember = (bool) $request->session()->get('auth.2fa.remember', false);
        $request->session()->forget(['auth.2fa.user_id', 'auth.2fa.remember']);

        Auth::login($user, $remember);
        $request->session()->regenerate();

        $this->auditService->catat(
            tipe: TipeAuditEnum::Auth,
            tabel: 'users',
            idData: $user->id,
            nilaiLama: null,
            nilaiBaru: null,
            detail: [
                'aksi' => '2fa.login_verified',
                'method' => $isRecovery ? 'recovery_code' : 'totp',
                'ip_address' => $request->ip(),
            ],
            user: $user,
            channel: 'auth',
            severity: 'info'
        );

        $targetDashboard = match ($user->role) {
            RoleEnum::SuperAdmin => route('superadmin.dashboard'),
            RoleEnum::Admin => route('admin.dashboard'),
            RoleEnum::Pembimbing => route('pembimbing.dashboard'),
            RoleEnum::Peserta => route('peserta.dashboard'),
        };

        return redirect()->intended($targetDashboard);
    }

    /**
     * Menampilkan dashboard pengaturan 2FA SuperAdmin.
     */
    public function showSettings(Request $request): View
    {
        $user = $request->user();

        return view('superadmin.security.two-factor', [
            'user' => $user,
            'enabled' => $user->hasEnabledTwoFactor(),
        ]);
    }

    /**
     * Memulai proses enrollment 2FA (Generate Secret Key & QR Code URL).
     */
    public function enable(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($user->hasEnabledTwoFactor()) {
            return redirect()->route('superadmin.2fa.settings')
                ->with('info', 'Autentikasi dua faktor sudah aktif pada akun Anda.');
        }

        // Generate secret baru jika belum ada
        $secret = $this->twoFactorService->generateSecretKey();
        $request->session()->put('auth.2fa.enroll_secret', $secret);

        $qrCodeUrl = $this->twoFactorService->getQRCodeUrl(
            company: 'MAGKOM Diskominfo',
            holder: $user->email,
            secret: $secret
        );

        return view('superadmin.security.two-factor-enroll', [
            'secret' => $secret,
            'qrCodeUrl' => $qrCodeUrl,
            'user' => $user,
        ]);
    }

    /**
     * Mengonfirmasi aktivasi 2FA dengan verifikasi kode pertama dari aplikasi authenticator.
     */
    public function confirm(Request $request): RedirectResponse|View
    {
        $user = $request->user();
        $secret = (string) $request->session()->get('auth.2fa.enroll_secret');

        if (empty($secret)) {
            return redirect()->route('superadmin.2fa.settings');
        }

        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ], [
            'code.required' => 'Masukkan kode 6-digit dari aplikasi authenticator.',
            'code.size' => 'Kode harus terdiri dari 6 angka.',
        ]);

        $isValid = $this->twoFactorService->verifyKey($secret, (string) $request->input('code'));
        if (! $isValid) {
            return back()->withErrors([
                'code' => 'Kode verifikasi tidak sesuai. Pastikan jam pada perangkat Anda akurat.',
            ]);
        }

        // Simpan secret terenkripsi dan aktifkan status konfirmasi
        $user->forceFill([
            'two_factor_secret' => $secret,
            'two_factor_confirmed_at' => now(),
        ])->save();

        $request->session()->forget('auth.2fa.enroll_secret');

        // Buat 8 single-use recovery codes
        $recoveryCodes = $user->generateRecoveryCodes();

        $this->auditService->catat(
            tipe: TipeAuditEnum::Auth,
            tabel: 'users',
            idData: $user->id,
            nilaiLama: null,
            nilaiBaru: ['two_factor_enabled' => true],
            detail: [
                'aksi' => '2fa.enrolled_and_confirmed',
                'ip_address' => $request->ip(),
            ],
            user: $user,
            channel: 'security-auth',
            severity: 'info'
        );

        return view('superadmin.security.two-factor-recovery-codes', [
            'recoveryCodes' => $recoveryCodes,
        ]);
    }

    /**
     * Menonaktifkan 2FA (memerlukan konfirmasi kata sandi saat ini).
     */
    public function disable(Request $request): RedirectResponse
    {
        $user = $request->user();

        $request->validate([
            'current_password' => ['required', 'string'],
        ], [
            'current_password.required' => 'Kata sandi saat ini wajib diisi untuk menonaktifkan 2FA.',
        ]);

        if (! Hash::check((string) $request->input('current_password'), (string) $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'Kata sandi yang Anda masukkan tidak sesuai.',
            ]);
        }

        $user->disableTwoFactor();

        $this->auditService->catat(
            tipe: TipeAuditEnum::Auth,
            tabel: 'users',
            idData: $user->id,
            nilaiLama: ['two_factor_enabled' => true],
            nilaiBaru: ['two_factor_enabled' => false],
            detail: [
                'aksi' => '2fa.disabled',
                'ip_address' => $request->ip(),
            ],
            user: $user,
            channel: 'security-auth',
            severity: 'warning'
        );

        return redirect()->route('superadmin.2fa.settings')
            ->with('success', 'Autentikasi dua faktor berhasil dinonaktifkan.');
    }

    /**
     * Menghasilkan ulang recovery codes baru.
     */
    public function regenerateRecoveryCodes(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        $request->validate([
            'current_password' => ['required', 'string'],
        ]);

        if (! Hash::check((string) $request->input('current_password'), (string) $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'Kata sandi yang Anda masukkan tidak sesuai.',
            ]);
        }

        $recoveryCodes = $user->generateRecoveryCodes();

        $this->auditService->catat(
            tipe: TipeAuditEnum::Auth,
            tabel: 'users',
            idData: $user->id,
            nilaiLama: null,
            nilaiBaru: null,
            detail: [
                'aksi' => '2fa.recovery_codes_regenerated',
                'ip_address' => $request->ip(),
            ],
            user: $user,
            channel: 'security-auth',
            severity: 'info'
        );

        return view('superadmin.security.two-factor-recovery-codes', [
            'recoveryCodes' => $recoveryCodes,
        ]);
    }
}
