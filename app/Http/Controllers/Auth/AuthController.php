<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Enums\RoleEnum;
use App\Enums\StatusKetersediaanEnum;
use App\Enums\TipeAuditEnum;
use App\Http\Controllers\Controller;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\KelompokMagang;
use App\Models\Kuota;
use App\Models\PesertaProfil;
use App\Models\User;
use App\Rules\CaptchaRule;
use App\Rules\TurnstileRule;
use App\Services\AuditService;
use App\Services\BulanStatusService;
use Carbon\CarbonImmutable;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function __construct(
        protected AuditService $auditService,
        protected BulanStatusService $bulanStatusService
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
            'email' => ['required', 'string'],
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

        $inputEmail = $credentials['email'];
        if ($inputEmail === 'admin') {
            $inputEmail = 'admin@tim.magkom.go.id';
        }

        $loginSuccess = Auth::attempt(['email' => $inputEmail, 'password' => $credentials['password']], $request->boolean('remember'));

        // Fallback khusus lingkungan lokal (development): Berikan fleksibilitas login jika mencoba variasi password umum
        if (! $loginSuccess && app()->isLocal()) {
            if ($credentials['email'] === 'admin' || $credentials['email'] === 'admin@tim.magkom.go.id') {
                $timUser = User::where('email', 'admin@tim.magkom.go.id')->first();
                if ($timUser && in_array($credentials['password'], ['admin', 'password', 'Password123!'], true)) {
                    $timUser->password = Hash::make($credentials['password']);
                    $timUser->save();
                    $loginSuccess = Auth::attempt(['email' => 'admin@tim.magkom.go.id', 'password' => $credentials['password']], $request->boolean('remember'));
                }
            } elseif ($credentials['email'] === 'superadmin@magkom.go.id') {
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
        $sekarang = CarbonImmutable::now('Asia/Jakarta');
        $selectedBulan = $request->filled('bulan') ? BulanMagang::find($request->query('bulan')) : null;
        $selectedJurusan = $request->filled('jurusan') ? Jurusan::with('bidang')->find($request->query('jurusan')) : null;
        $selectedTipe = $request->query('tipe', 'mandiri');
        $selectedJumlah = (int) $request->query('jumlah', 1);

        $selectedKuota = ($selectedBulan && $selectedJurusan)
            ? Kuota::where('bulan_magang_id', $selectedBulan->id)
                ->where('jurusan_id', $selectedJurusan->id)
                ->where('aktif', true)
                ->first()
            : null;
        $selectedSisaKuota = $selectedKuota ? $selectedKuota->sisa : 0;
        $selectedTotalKuota = $selectedKuota ? (int) $selectedKuota->jumlah : 0;

        $semuaBulanTersedia = BulanMagang::with(['kuota' => fn ($q) => $q->where('aktif', true)])
            ->where('aktif', true)
            ->get()
            ->filter(fn ($b) => $this->bulanStatusService->status($b, null, $sekarang) === StatusKetersediaanEnum::Tersedia);

        $jurusanList = Jurusan::with('bidang')
            ->where('aktif', true)
            ->orderBy('kategori', 'asc')
            ->orderBy('nama_jurusan', 'asc')
            ->get();

        $jurusanList->each(function (Jurusan $jurusan) use ($semuaBulanTersedia) {
            $totalSisa = 0;
            $totalKapasitas = 0;
            foreach ($semuaBulanTersedia as $b) {
                $k = $b->kuota->firstWhere('jurusan_id', $jurusan->id);
                if ($k) {
                    $totalSisa += $k->sisa;
                    $totalKapasitas += (int) $k->jumlah;
                }
            }
            $jurusan->total_sisa_kuota = $totalSisa;
            $jurusan->total_kapasitas = $totalKapasitas;
            $jurusan->status_kuota = $totalSisa > 0 ? 'tersedia' : 'penuh';
        });

        $bidangList = Bidang::with(['jurusan' => function ($q) {
            $q->where('aktif', true)->orderBy('nama_jurusan');
        }])->where('aktif', true)->orderBy('nama_bidang')->get();

        return view('auth.register', compact(
            'selectedBulan',
            'selectedJurusan',
            'selectedTipe',
            'selectedJumlah',
            'selectedSisaKuota',
            'selectedTotalKuota',
            'jurusanList',
            'bidangList'
        ));
    }

    public function register(Request $request): RedirectResponse
    {
        $turnstileEnabled = (bool) config('services.turnstile.enabled', false);
        $captchaEnabled = (bool) config('captcha.enabled', true);

        if ($request->has('username') && ! $request->has('nama')) {
            $request->merge(['nama' => $request->input('username')]);
        }

        $isKelompok = $request->input('tipe_pendaftaran') === 'kelompok' || $request->input('redirect_tipe') === 'kelompok';

        $rules = [
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'no_telp' => ['sometimes', 'required', 'string', 'max:20', 'regex:/^[0-9\+\-\s]+$/'],
            'password' => ['required', 'string', Password::min(8)],
            'tipe_pendaftaran' => ['nullable', 'string', 'in:mandiri,kelompok'],
            'jenjang' => ['nullable', 'string', 'in:smk,d3,s1'],
            'jurusan_id' => ['nullable', 'integer', 'exists:jurusan,id'],
            'institusi' => ['nullable', 'string', 'max:150'],
            'nim_nisn' => ['nullable', 'string', 'max:50'],
            'persetujuan_wali' => ['nullable', 'boolean'],
        ];

        if ($request->input('jenjang') === 'smk' && $request->has('persetujuan_wali')) {
            $rules['persetujuan_wali'] = ['accepted'];
        }

        if ($isKelompok) {
            $rules['nama_kelompok'] = ['required', 'string', 'max:150'];

            $targetJumlah = (int) ($request->input('redirect_jumlah') ?: $request->input('jumlah_kelompok', 0));
            if ($targetJumlah >= 2 && $targetJumlah <= 4) {
                $rules['anggota'] = ['required', 'array', 'size:'.($targetJumlah - 1)];
            } else {
                $rules['anggota'] = ['required', 'array', 'min:1', 'max:3'];
            }

            $rules['anggota.*.nama'] = ['required', 'string', 'max:255'];
            $rules['anggota.*.nim_nisn'] = ['required', 'string', 'max:50'];
            $rules['anggota.*.no_hp'] = ['required', 'string', 'max:30'];
            $rules['anggota.*.email'] = ['required', 'email', 'max:255'];
        }

        if ($turnstileEnabled) {
            $rules['cf-turnstile-response'] = ['required', new TurnstileRule(isRegistration: true)];
        }

        $shouldValidateCaptcha = $captchaEnabled && (! app()->runningUnitTests() || $request->has('captcha') || config('captcha.strict_testing', false));
        if ($shouldValidateCaptcha) {
            $rules['captcha'] = ['required', 'string', 'size:4', new CaptchaRule];
        }

        $validated = $request->validate($rules);

        $user = DB::transaction(function () use ($validated, $request, $isKelompok) {
            $user = new User;
            $user->nama = $validated['nama'];
            $user->email = $validated['email'];
            $user->no_telp = $validated['no_telp'] ?? $request->input('no_telp');
            $user->password = Hash::make($validated['password']);
            $user->role = RoleEnum::Peserta;
            $user->status_aktif = true;
            $user->email_verified_at = now();
            $user->save();

            if (! empty($validated['jurusan_id']) && ! empty($validated['jenjang'])) {
                $profilData = [
                    'user_id' => $user->id,
                    'jenjang' => $validated['jenjang'],
                    'jurusan_id' => (int) $validated['jurusan_id'],
                    'institusi' => $validated['institusi'] ?? 'Institusi Belum Diatur',
                    'nim_nisn' => $validated['nim_nisn'] ?? '-',
                    'no_hp' => $user->no_telp ?? '-',
                ];

                if ($validated['jenjang'] === 'smk' && $request->boolean('persetujuan_wali')) {
                    $profilData['persetujuan_wali_at'] = now();
                }

                PesertaProfil::create($profilData);
            }

            if ($isKelompok && ! empty($validated['nama_kelompok'])) {
                $kelompok = KelompokMagang::create([
                    'ketua_user_id' => $user->id,
                    'nama_kelompok' => $validated['nama_kelompok'],
                ]);

                if (! empty($validated['anggota'])) {
                    foreach ($validated['anggota'] as $anggotaData) {
                        $kelompok->anggota()->create([
                            'nama' => $anggotaData['nama'],
                            'nim_nisn' => $anggotaData['nim_nisn'] ?? null,
                            'no_hp' => $anggotaData['no_hp'] ?? null,
                            'email' => $anggotaData['email'] ?? null,
                        ]);
                    }
                }
            }

            return $user;
        });

        event(new Registered($user));

        Auth::login($user);

        $this->auditService->catat(
            tipe: TipeAuditEnum::Aksi,
            tabel: 'users',
            idData: $user->id,
            nilaiLama: null,
            nilaiBaru: ['nama' => $user->nama, 'email' => $user->email, 'no_telp' => $user->no_telp, 'role' => $user->role->value],
            detail: ['aksi' => 'registrasi_peserta_baru', 'tipe' => $isKelompok ? 'kelompok' : 'mandiri']
        );

        $preferensiJurusanId = $request->input('redirect_jurusan') ?: ($validated['jurusan_id'] ?? null);
        $preferensiBulanId = $request->input('redirect_bulan');

        if ($preferensiBulanId || $preferensiJurusanId) {
            session([
                'pendaftaran_preferensi' => [
                    'bulan_id' => $preferensiBulanId,
                    'jurusan_id' => $preferensiJurusanId,
                    'tipe' => $isKelompok ? 'kelompok' : $request->input('redirect_tipe', 'mandiri'),
                    'jumlah' => $isKelompok ? (count($validated['anggota'] ?? []) + 1) : (int) $request->input('redirect_jumlah', 1),
                ],
            ]);
        }

        $suksesMsg = $isKelompok
            ? 'Pendaftaran akun kelompok berhasil! Selamat datang '.($user->nama_tampil).'. Silakan lengkapi profil ketua tim.'
            : 'Pendaftaran akun berhasil. Silakan lengkapi profil biodata Anda.';

        return redirect()->route('peserta.dashboard')
            ->with('success', $suksesMsg);
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
