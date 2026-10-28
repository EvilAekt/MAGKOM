<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Enums\RoleEnum;
use App\Enums\TipeAuditEnum;
use App\Http\Controllers\Controller;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\User;
use App\Rules\CaptchaRule;
use App\Rules\TurnstileRule;
use App\Services\AuditService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function __construct(
        protected AuditService $auditService
    ) {}

    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $turnstileEnabled = (bool) config('services.turnstile.enabled', false);
        $captchaEnabled = (bool) config('captcha.enabled', true);

        $rules = [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];

        if ($turnstileEnabled) {
            $rules['cf-turnstile-response'] = ['required', new TurnstileRule(isRegistration: false)];
        }

        $shouldValidateCaptcha = $captchaEnabled && (! app()->runningUnitTests() || $request->has('captcha') || config('captcha.strict_testing', false));
        if ($shouldValidateCaptcha) {
            $rules['captcha'] = ['required', 'string', 'size:4', new CaptchaRule];
        }

        $credentials = $request->validate($rules);

        $loginSuccess = Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']], $request->boolean('remember'));

        // Fallback khusus lingkungan lokal (development): Berikan fleksibilitas login jika mencoba variasi password umum
        if (! $loginSuccess && app()->isLocal() && $credentials['email'] === 'superadmin@magkom.go.id') {
            $allowedDevPasswords = ['Password123!', 'SuperAdminMagKom123!', 'password'];
            if (in_array($credentials['password'], $allowedDevPasswords, true)) {
                $superadminUser = User::where('email', 'superadmin@magkom.go.id')->first();
                if ($superadminUser) {
                    $superadminUser->password = Hash::make($credentials['password']);
                    $superadminUser->save();
                    $loginSuccess = Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']], $request->boolean('remember'));
                }
            }
        }

        if ($loginSuccess) {
            $request->session()->regenerate();
            $user = Auth::user();

            $this->auditService->catat(
                tipe: TipeAuditEnum::Aksi,
                tabel: 'users',
                idData: $user->id,
                nilaiLama: null,
                nilaiBaru: ['email' => $user->email, 'role' => $user->role->value],
                detail: ['aksi' => 'login_berhasil']
            );

            $targetDashboard = match ($user->role) {
                RoleEnum::SuperAdmin => route('superadmin.dashboard'),
                RoleEnum::Admin => route('admin.dashboard'),
                RoleEnum::Pembimbing => route('pembimbing.dashboard'),
                RoleEnum::Peserta => route('peserta.dashboard'),
            };

            $intended = session()->get('url.intended');
            if ($intended && (str_contains($intended, '/realtime/stream') || str_contains($intended, '/realtime/'))) {
                session()->forget('url.intended');

                return redirect()->to($targetDashboard);
            }

            return redirect()->intended($targetDashboard);
        }

        $this->auditService->catat(
            tipe: TipeAuditEnum::Aksi,
            tabel: 'users',
            idData: null,
            nilaiLama: null,
            nilaiBaru: ['email_percobaan' => $request->input('email')],
            detail: ['aksi' => 'login_gagal_kredensial_salah']
        );

        return back()->withErrors([
            'email' => 'Kombinasi email dan kata sandi yang Anda masukkan tidak sesuai.',
        ])->onlyInput('email');
    }

    public function showRegister(Request $request): View
    {
        $selectedBulan = $request->filled('bulan') ? BulanMagang::find($request->query('bulan')) : null;
        $selectedJurusan = $request->filled('jurusan') ? Jurusan::with('bidang')->find($request->query('jurusan')) : null;
        $selectedTipe = $request->query('tipe', 'mandiri');
        $selectedJumlah = (int) $request->query('jumlah', 1);

        return view('auth.register', compact('selectedBulan', 'selectedJurusan', 'selectedTipe', 'selectedJumlah'));
    }

    public function register(Request $request): RedirectResponse
    {
        $turnstileEnabled = (bool) config('services.turnstile.enabled', false);
        $captchaEnabled = (bool) config('captcha.enabled', true);

        if ($request->has('username') && ! $request->has('nama')) {
            $request->merge(['nama' => $request->input('username')]);
        }

        $rules = [
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'no_telp' => ['sometimes', 'required', 'string', 'max:20', 'regex:/^[0-9\+\-\s]+$/'],
            'password' => ['required', 'string', Password::min(8)],
        ];

        if ($turnstileEnabled) {
            $rules['cf-turnstile-response'] = ['required', new TurnstileRule(isRegistration: true)];
        }

        $shouldValidateCaptcha = $captchaEnabled && (! app()->runningUnitTests() || $request->has('captcha') || config('captcha.strict_testing', false));
        if ($shouldValidateCaptcha) {
            $rules['captcha'] = ['required', 'string', 'size:4', new CaptchaRule];
        }

        $validated = $request->validate($rules);

        $user = new User;
        $user->nama = $validated['nama'];
        $user->email = $validated['email'];
        $user->no_telp = $validated['no_telp'] ?? $request->input('no_telp');
        $user->password = Hash::make($validated['password']);
        $user->role = RoleEnum::Peserta;
        $user->status_aktif = true;
        $user->email_verified_at = now();
        $user->save();

        event(new Registered($user));

        Auth::login($user);

        $this->auditService->catat(
            tipe: TipeAuditEnum::Aksi,
            tabel: 'users',
            idData: $user->id,
            nilaiLama: null,
            nilaiBaru: ['nama' => $user->nama, 'email' => $user->email, 'no_telp' => $user->no_telp, 'role' => $user->role->value],
            detail: ['aksi' => 'registrasi_peserta_baru']
        );

        if ($request->filled('redirect_bulan') || $request->filled('redirect_jurusan')) {
            session([
                'pendaftaran_preferensi' => [
                    'bulan_id' => $request->input('redirect_bulan'),
                    'jurusan_id' => $request->input('redirect_jurusan'),
                    'tipe' => $request->input('redirect_tipe', 'mandiri'),
                    'jumlah' => (int) $request->input('redirect_jumlah', 1),
                ],
            ]);
        }

        return redirect()->route('peserta.profil.show')
            ->with('success', 'Pendaftaran akun berhasil. Silakan lengkapi profil biodata Anda.');
    }

    public function logout(Request $request): RedirectResponse
    {
        $userId = Auth::id();

        if ($userId) {
            $this->auditService->catat(
                tipe: TipeAuditEnum::Aksi,
                tabel: 'users',
                idData: $userId,
                nilaiLama: null,
                nilaiBaru: null,
                detail: ['aksi' => 'logout']
            );
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('landing');
    }
}
