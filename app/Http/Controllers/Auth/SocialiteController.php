<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Enums\RoleEnum;
use App\Enums\TipeAuditEnum;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\AbstractProvider;
use Throwable;

class SocialiteController extends Controller
{
    /**
     * Provider OAuth yang didukung sistem (F-18: Google).
     *
     * @var array<int, string>
     */
    protected array $supportedProviders = ['google'];

    public function __construct(
        protected AuditService $auditService
    ) {}

    /**
     * Mengarahkan pengguna ke halaman autentikasi pihak ketiga (Google).
     */
    public function redirect(string $provider): RedirectResponse
    {
        if (! in_array($provider, $this->supportedProviders, true)) {
            abort(404, 'Penyedia login sosial tidak didukung.');
        }

        /** @var AbstractProvider $driver */
        $driver = Socialite::driver($provider);

        return $driver->redirect();
    }

    /**
     * Menangani callback autentikasi dari penyedia pihak ketiga.
     * Menerapkan aturan AC-18-1 dan NFR-S9:
     * 1. Khusus untuk role Peserta (staf dilarang menggunakan login sosial).
     * 2. Pencegahan pembajakan akun (anti-hijacking): akun lokal yang belum terverifikasi ditolak penautannya.
     */
    public function callback(string $provider, Request $request): RedirectResponse
    {
        if (! in_array($provider, $this->supportedProviders, true)) {
            abort(404, 'Penyedia login sosial tidak didukung.');
        }

        try {
            /** @var \Laravel\Socialite\Contracts\User $oauthUser */
            $oauthUser = Socialite::driver($provider)->user();
        } catch (Throwable $e) {
            $this->auditService->catat(
                tipe: TipeAuditEnum::Aksi,
                tabel: 'users',
                idData: null,
                nilaiLama: null,
                nilaiBaru: ['provider' => $provider, 'error' => $e->getMessage()],
                detail: ['aksi' => 'login_oauth_gagal_provider']
            );

            return redirect()->route('login')
                ->withErrors(['email' => 'Gagal melakukan autentikasi dengan Google. Silakan coba kembali.']);
        }

        $email = $oauthUser->getEmail();
        $providerId = (string) $oauthUser->getId();

        if (empty($email)) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Akun Google Anda tidak menyediakan alamat email yang valid.']);
        }

        // Kasus 1: Akun sudah pernah ditautkan dengan provider & provider_id ini
        $userByProvider = User::where('provider', $provider)
            ->where('provider_id', $providerId)
            ->first();

        if ($userByProvider) {
            if (! $userByProvider->status_aktif) {
                return redirect()->route('login')
                    ->withErrors(['email' => 'Akun Anda dinonaktifkan. Silakan hubungi administrator.']);
            }

            if ($userByProvider->role !== RoleEnum::Peserta) {
                return redirect()->route('login')
                    ->withErrors(['email' => 'Login sosial hanya diizinkan untuk akun peserta.']);
            }

            Auth::login($userByProvider);
            $request->session()->regenerate();

            $this->auditService->catat(
                tipe: TipeAuditEnum::Aksi,
                tabel: 'users',
                idData: $userByProvider->id,
                nilaiLama: null,
                nilaiBaru: ['email' => $userByProvider->email, 'provider' => $provider],
                detail: ['aksi' => 'login_berhasil_oauth']
            );

            $intended = session()->get('url.intended');
            if ($intended && (str_contains($intended, '/realtime/stream') || str_contains($intended, '/realtime/'))) {
                session()->forget('url.intended');
            }

            return redirect()->intended(route('peserta.pendaftaran.index'));
        }

        // Kasus 2: Cari akun berdasarkan email yang sama
        $existingUser = User::where('email', $email)->first();

        if ($existingUser) {
            // Validasi peran: hanya role Peserta yang diperbolehkan login via sosial (AC-18-1)
            if ($existingUser->role !== RoleEnum::Peserta) {
                $this->auditService->catat(
                    tipe: TipeAuditEnum::Aksi,
                    tabel: 'users',
                    idData: $existingUser->id,
                    nilaiLama: null,
                    nilaiBaru: ['email' => $existingUser->email, 'role' => $existingUser->role->value],
                    detail: ['aksi' => 'login_oauth_ditolak_bukan_peserta']
                );

                return redirect()->route('login')
                    ->withErrors(['email' => 'Login sosial hanya diizinkan untuk akun peserta. Akun staf harus masuk menggunakan kata sandi.']);
            }

            if (! $existingUser->status_aktif) {
                return redirect()->route('login')
                    ->withErrors(['email' => 'Akun Anda dinonaktifkan. Silakan hubungi administrator.']);
            }

            // Anti-hijacking (NFR-S9): Tolak penautan akun jika email belum terverifikasi
            if ($existingUser->email_verified_at === null) {
                $this->auditService->catat(
                    tipe: TipeAuditEnum::Aksi,
                    tabel: 'users',
                    idData: $existingUser->id,
                    nilaiLama: null,
                    nilaiBaru: ['email' => $email, 'provider' => $provider],
                    detail: [
                        'aksi' => 'login_oauth_ditolak_anti_hijacking',
                        'alasan' => 'email_belum_terverifikasi',
                    ]
                );

                return redirect()->route('login')
                    ->withErrors([
                        'email' => 'Akun dengan email ini belum diverifikasi. Untuk keamanan akun dan pencegahan pembajakan, silakan verifikasi email Anda terlebih dahulu atau masuk dengan kata sandi.',
                    ]);
            }

            // Tautkan akun jika email sudah terverifikasi
            $existingUser->provider = $provider;
            $existingUser->provider_id = $providerId;
            $existingUser->save();

            Auth::login($existingUser);
            $request->session()->regenerate();

            $this->auditService->catat(
                tipe: TipeAuditEnum::Aksi,
                tabel: 'users',
                idData: $existingUser->id,
                nilaiLama: null,
                nilaiBaru: ['email' => $existingUser->email, 'provider' => $provider, 'provider_id' => $providerId],
                detail: ['aksi' => 'login_berhasil_oauth_taut_akun']
            );

            $intended = session()->get('url.intended');
            if ($intended && (str_contains($intended, '/realtime/stream') || str_contains($intended, '/realtime/'))) {
                session()->forget('url.intended');
            }

            return redirect()->intended(route('peserta.pendaftaran.index'))
                ->with('success', 'Akun Google Anda berhasil ditautkan ke akun MagKom.');
        }

        // Kasus 3: Belum ada akun dengan email ini -> Buat akun Peserta baru
        $name = $oauthUser->getName() ?: 'Peserta Magang';

        $newUser = new User;
        $newUser->nama = (string) $name;
        $newUser->email = $email;
        $newUser->password = null;
        $newUser->provider = $provider;
        $newUser->provider_id = $providerId;
        $newUser->role = RoleEnum::Peserta;
        $newUser->status_aktif = true;
        $newUser->email_verified_at = now();
        $newUser->save();

        event(new Registered($newUser));

        Auth::login($newUser);
        $request->session()->regenerate();

        $this->auditService->catat(
            tipe: TipeAuditEnum::Aksi,
            tabel: 'users',
            idData: $newUser->id,
            nilaiLama: null,
            nilaiBaru: [
                'nama' => $newUser->nama,
                'email' => $newUser->email,
                'role' => $newUser->role->value,
                'provider' => $provider,
                'provider_id' => $providerId,
            ],
            detail: ['aksi' => 'registrasi_oauth_berhasil']
        );

        return redirect()->route('peserta.profil.show')
            ->with('success', 'Pendaftaran berhasil menggunakan akun Google. Silakan lengkapi profil biodata Anda.');
    }
}
