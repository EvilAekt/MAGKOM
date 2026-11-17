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
use App\Services\LoginRateLimiterService;
use App\Support\PasswordPolicy;
use App\Support\Pengaturan;
use Carbon\CarbonImmutable;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function __construct(
        protected AuditService $auditService,
        protected BulanStatusService $bulanStatusService,
        protected ?LoginRateLimiterService $loginRateLimiter = null
    ) {
        $this->loginRateLimiter ??= app(LoginRateLimiterService::class);
    }

    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        // 1. Normalisasi email (huruf kecil) dan captcha (huruf besar/kapital)
        $merges = [
            'email' => strtolower(trim((string) $request->input('email'))),
        ];

        if ($request->has('captcha')) {
            $merges['captcha'] = strtoupper(trim((string) $request->input('captcha')));
        }

        $request->merge($merges);

        $turnstileEnabled = (bool) config('services.turnstile.enabled', false);
        $captchaEnabled = (bool) config('captcha.enabled', true);

        // 2. Validasi ketat format email RFC
        $rules = [
            'email' => ['required', 'string', 'email:rfc'],
            'password' => ['required', 'string'],
        ];

        $shouldValidateTurnstile = $turnstileEnabled && (! app()->runningUnitTests() || $request->has('cf-turnstile-response') || config('services.turnstile.strict_testing', false));
        if ($shouldValidateTurnstile) {
            $rules['cf-turnstile-response'] = ['required', new TurnstileRule(isRegistration: false)];
        }

        $shouldValidateCaptcha = $captchaEnabled && (! app()->runningUnitTests() || $request->has('captcha') || config('captcha.strict_testing', false));
        if ($shouldValidateCaptcha) {
            $rules['captcha'] = ['required', 'string', 'size:4', new CaptchaRule];
        }

        $credentials = $request->validate($rules, [
            'email.email' => 'Format email tidak valid. Harap masukkan alamat email lengkap (contoh: nama@gmail.com).',
        ]);

        $normalizedEmail = $credentials['email'];
        $clientIp = $request->ip();
        $throttleKey = $normalizedEmail.'|'.$clientIp;
        $failCounterKey = 'login_fails:'.$throttleKey;

        // 3. Pengecekan status Multi-Layer Rate Limiter (SEC-003: Pair, Account, IP, Global)
        $lockStatus = $this->loginRateLimiter->checkLockout($normalizedEmail, $clientIp);
        if ($lockStatus['locked'] || RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = max($lockStatus['seconds'], RateLimiter::availableIn($throttleKey));

            $this->auditService->catat(
                tipe: TipeAuditEnum::Auth,
                tabel: 'users',
                idData: null,
                detail: [
                    'aksi' => 'login_diblokir_rate_limit',
                    'email' => $normalizedEmail,
                    'ip_address' => $clientIp,
                    'user_agent' => substr((string) $request->userAgent(), 0, 255),
                    'lockout_seconds' => $seconds,
                    'rate_layer' => $lockStatus['layer'] ?? 'pair',
                    'keterangan' => $lockStatus['reason'] ?? 'Akun dibekukan sementara karena salah password berulang kali.',
                ]
            );

            return back()->with([
                'lockout_seconds' => $seconds,
                'error_lockout' => 'Akses akun dibekukan sementara karena terlalu banyak percobaan masuk yang gagal. Silakan tunggu hitung mundur selesai sebelum mencoba kembali.',
            ])->withErrors([
                'email' => 'Akses akun dibekukan sementara karena terlalu banyak percobaan masuk yang gagal.',
            ])->onlyInput('email');
        }

        // 4. Autentikasi murni
        $loginSuccess = Auth::attempt(
            ['email' => $normalizedEmail, 'password' => $credentials['password']],
            $request->boolean('remember')
        );

        if ($loginSuccess) {
            $this->loginRateLimiter->clear($normalizedEmail, $clientIp);
            RateLimiter::clear($throttleKey);
            RateLimiter::clear($failCounterKey);
            $request->session()->regenerate();
            $user = Auth::user();

            // Intersepsi Otentikasi Dua Faktor (AUTH-002: Two-Factor Challenge)
            if ($user && method_exists($user, 'hasEnabledTwoFactor') && $user->hasEnabledTwoFactor()) {
                $remember = $request->boolean('remember');
                Auth::logout();
                $request->session()->put('auth.2fa.user_id', $user->id);
                $request->session()->put('auth.2fa.remember', $remember);

                return redirect()->route('two-factor.challenge');
            }

            $this->auditService->catat(
                tipe: TipeAuditEnum::Aksi,
                tabel: 'users',
                idData: $user->id,
                nilaiLama: null,
                nilaiBaru: ['email' => $user->email, 'role' => $user->role->value],
                detail: [
                    'aksi' => 'login_berhasil',
                    'ip_address' => $clientIp,
                    'user_agent' => substr((string) $request->userAgent(), 0, 255),
                ]
            );

            if ($user->role === RoleEnum::Peserta) {
                $preferensiJurusanId = $request->input('redirect_jurusan');
                $preferensiBulanId = $request->input('redirect_bulan');
                $preferensiBidangId = $request->input('redirect_bidang');

                if ($preferensiBulanId || $preferensiJurusanId || $preferensiBidangId) {
                    session([
                        'pendaftaran_preferensi' => [
                            'bulan_id' => $preferensiBulanId,
                            'bidang_id' => $preferensiBidangId,
                            'jurusan_id' => $preferensiJurusanId,
                            'tipe' => $request->input('redirect_tipe', 'mandiri'),
                            'jumlah' => (int) $request->input('redirect_jumlah', 1),
                        ],
                    ]);
                }
            }

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

        // 5. Tangani Kegagalan Autentikasi (SEC-003: Multi-Layer Throttling)
        $this->loginRateLimiter->recordFailure($normalizedEmail, $clientIp);

        // Hitung akumulasi kegagalan untuk eskalasi penalti (Tiered Progressive Backoff)
        $currentFails = (int) RateLimiter::attempts($failCounterKey) + 1;
        RateLimiter::hit($failCounterKey, 1800); // Simpan riwayat akumulasi kegagalan selama 30 menit

        // Tentukan durasi pembekuan berdasarkan tingkat pelanggaran:
        // - Tier 1: 3x salah => 60 detik (1 menit)
        // - Tier 2: 6x salah => 300 detik (5 menit)
        // - Tier 3: 9x salah ke atas => 900 detik (15 menit)
        $lockoutDuration = match (true) {
            $currentFails >= 9 => 900,
            $currentFails >= 6 => 300,
            default => 60,
        };

        // Jika tepat mencapai kelipatan 3 kegagalan, kunci dengan durasi eskalasi penuh
        if ($currentFails % 3 === 0) {
            RateLimiter::clear($throttleKey);
            for ($i = 0; $i < 3; $i++) {
                RateLimiter::hit($throttleKey, $lockoutDuration);
            }
        } else {
            RateLimiter::hit($throttleKey, $lockoutDuration);
        }

        // Cek kembali status lockout multi-layer
        $lockStatusAfter = $this->loginRateLimiter->checkLockout($normalizedEmail, $clientIp);
        if ($lockStatusAfter['locked'] || RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = max($lockStatusAfter['seconds'], RateLimiter::availableIn($throttleKey));

            $this->auditService->catat(
                tipe: TipeAuditEnum::Auth,
                tabel: 'users',
                idData: null,
                detail: [
                    'aksi' => 'login_terkunci_baru',
                    'email' => $normalizedEmail,
                    'ip_address' => $clientIp,
                    'akumulasi_gagal' => $currentFails,
                    'lockout_seconds' => $seconds,
                    'rate_layer' => $lockStatusAfter['layer'] ?? 'pair',
                ]
            );

            return back()->with([
                'lockout_seconds' => $seconds,
                'error_lockout' => 'Anda telah salah memasukkan kata sandi sebanyak 3 kali. Akses akun dibekukan sementara selama '.$seconds.' detik.',
            ])->withErrors([
                'email' => 'Akses akun dibekukan sementara karena Anda telah salah memasukkan kata sandi sebanyak 3 kali.',
            ])->onlyInput('email');
        }

        $this->auditService->catat(
            tipe: TipeAuditEnum::Aksi,
            tabel: 'users',
            idData: null,
            nilaiLama: null,
            nilaiBaru: ['email_percobaan' => $normalizedEmail],
            detail: [
                'aksi' => 'login_gagal_kredensial_salah',
                'percobaan_ke' => $currentFails,
                'sisa_kesempatan' => max(0, 3 - ($currentFails % 3)),
                'ip_address' => $clientIp,
                'user_agent' => substr((string) $request->userAgent(), 0, 255),
            ]
        );

        return back()->withErrors([
            'email' => 'Kombinasi email dan kata sandi yang Anda masukkan tidak sesuai.',
        ])->onlyInput('email');
    }

    public function showRegister(Request $request): View
    {
        $sekarang = CarbonImmutable::now('Asia/Jakarta');
        $selectedBulan = $request->filled('bulan') ? BulanMagang::find($request->query('bulan')) : null;
        $selectedBidang = $request->filled('bidang') ? Bidang::find($request->query('bidang')) : null;
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

        $matriksPenempatan = [];
        foreach ($semuaBulanTersedia as $bulan) {
            $bId = (string) $bulan->id;
            $matriksPenempatan[$bId] = [
                'nama_bulan' => $bulan->nama_bulan,
                'smk' => [],
                'mahasiswa' => [],
            ];

            foreach ($bidangList as $bidang) {
                foreach (['smk', 'mahasiswa'] as $jenjang) {
                    $jurusanJenjang = $bidang->jurusan->filter(fn (Jurusan $j) => strtolower((string) $j->kategori) === $jenjang);
                    if ($jurusanJenjang->isEmpty()) {
                        continue;
                    }

                    $jurusanIds = $jurusanJenjang->pluck('id');
                    $kuotaList = $bulan->kuota->whereIn('jurusan_id', $jurusanIds);
                    $total = (int) $kuotaList->sum('jumlah');
                    $terisi = (int) $kuotaList->sum('terisi');
                    $sisa = max(0, $total - $terisi);

                    $jurusanDetail = [];
                    foreach ($jurusanJenjang as $j) {
                        $k = $bulan->kuota->firstWhere('jurusan_id', $j->id);
                        $jTotal = $k ? (int) $k->jumlah : 0;
                        $jTerisi = $k ? (int) $k->terisi : 0;
                        $jSisa = max(0, $jTotal - $jTerisi);

                        $jurusanDetail[] = [
                            'id' => (string) $j->id,
                            'nama' => $j->nama_jurusan,
                            'sisa' => $jSisa,
                            'total' => $jTotal,
                        ];
                    }

                    $matriksPenempatan[$bId][$jenjang][] = [
                        'id' => (string) $bidang->id,
                        'nama' => $bidang->nama_bidang,
                        'total' => $total,
                        'sisa' => $sisa,
                        'is_tersedia' => $sisa > 0,
                        'jurusan' => $jurusanDetail,
                    ];
                }
            }
        }

        // Fallback 'default' bila belum ada data bulan yang aktif
        $matriksPenempatan['default'] = [
            'nama_bulan' => 'Periode Aktif Terdekat',
            'smk' => [],
            'mahasiswa' => [],
        ];
        foreach ($bidangList as $bidang) {
            foreach (['smk', 'mahasiswa'] as $jenjang) {
                $jurusanJenjang = $bidang->jurusan->filter(fn (Jurusan $j) => strtolower((string) $j->kategori) === $jenjang);
                if ($jurusanJenjang->isEmpty()) {
                    continue;
                }

                $jurusanDetail = [];
                $totalSisa = 0;
                $totalKapasitas = 0;
                foreach ($jurusanJenjang as $j) {
                    $totalSisa += (int) ($j->total_sisa_kuota ?? 0);
                    $totalKapasitas += (int) ($j->total_kapasitas ?? 0);
                    $jurusanDetail[] = [
                        'id' => (string) $j->id,
                        'nama' => $j->nama_jurusan,
                        'sisa' => (int) ($j->total_sisa_kuota ?? 0),
                        'total' => (int) ($j->total_kapasitas ?? 0),
                    ];
                }

                $matriksPenempatan['default'][$jenjang][] = [
                    'id' => (string) $bidang->id,
                    'nama' => $bidang->nama_bidang,
                    'total' => $totalKapasitas,
                    'sisa' => $totalSisa,
                    'is_tersedia' => $totalSisa > 0,
                    'jurusan' => $jurusanDetail,
                ];
            }
        }

        $oauthRegisterData = $request->session()->get('oauth_register_data');
        $maksAnggotaKelompok = Pengaturan::int('maks_anggota_kelompok', 4);
        $fiturKelompokAktif = Pengaturan::bool('fitur_pendaftaran_kelompok_aktif', true);

        return view('auth.register', compact(
            'selectedBulan',
            'selectedBidang',
            'selectedJurusan',
            'selectedTipe',
            'selectedJumlah',
            'selectedSisaKuota',
            'selectedTotalKuota',
            'jurusanList',
            'bidangList',
            'semuaBulanTersedia',
            'matriksPenempatan',
            'oauthRegisterData',
            'maksAnggotaKelompok',
            'fiturKelompokAktif'
        ));
    }

    public function register(Request $request): RedirectResponse
    {
        // 1. Normalisasi email (huruf kecil) dan captcha (huruf besar/kapital)
        $merges = [
            'email' => strtolower(trim((string) $request->input('email'))),
        ];

        if ($request->has('captcha')) {
            $merges['captcha'] = strtoupper(trim((string) $request->input('captcha')));
        }

        $request->merge($merges);

        $turnstileEnabled = (bool) config('services.turnstile.enabled', false);
        $captchaEnabled = (bool) config('captcha.enabled', true);

        if ($request->has('username') && ! $request->has('nama')) {
            $request->merge(['nama' => $request->input('username')]);
        }

        $tipe = $request->input('tipe_pendaftaran', $request->input('redirect_tipe', 'mandiri'));
        $isKelompok = $tipe === 'kelompok';

        if ($isKelompok && ! Pengaturan::bool('fitur_pendaftaran_kelompok_aktif', true)) {
            return back()->withErrors([
                'tipe_pendaftaran' => 'Pendaftaran melalui jalur kelompok saat ini sedang dinonaktifkan oleh administrator.',
            ])->withInput();
        }

        $hasConfirmation = $request->has('password_confirmation');
        $passwordRules = PasswordPolicy::pesertaRules(confirmed: $hasConfirmation);

        $rules = [
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email:rfc', 'max:255', 'unique:users,email'],
            'no_telp' => ['sometimes', 'required', 'string', 'max:20', 'regex:/^[0-9\+\-\s]+$/'],
            'password' => $passwordRules,
            'tipe_pendaftaran' => ['nullable', 'string', 'in:mandiri,kelompok'],
            'jenjang' => ['nullable', 'string', 'in:smk,d3,s1,mahasiswa'],
            'jurusan_id' => ['nullable', 'integer', 'exists:jurusan,id'],
            'bidang_id' => ['nullable', 'integer', 'exists:bidang,id'],
            'bulan_magang_id' => ['nullable', 'integer', 'exists:bulan_magang,id'],
            'institusi' => ['nullable', 'string', 'max:150'],
            'nim_nisn' => ['nullable', 'string', 'max:50'],
            'persetujuan_wali' => ['nullable', 'boolean'],
        ];

        if ($request->input('jenjang') === 'smk' && $request->has('persetujuan_wali')) {
            $rules['persetujuan_wali'] = ['accepted'];
        }

        if ($isKelompok) {
            $rawAnggota = $request->input('anggota', []);
            if (is_array($rawAnggota)) {
                $filteredAnggota = array_values(array_filter($rawAnggota, function ($item) {
                    if (! is_array($item)) {
                        return false;
                    }
                    foreach ($item as $val) {
                        if (trim((string) $val) !== '') {
                            return true;
                        }
                    }

                    return false;
                }));
                $request->merge(['anggota' => $filteredAnggota]);
            }

            $rules['nama_kelompok'] = ['required', 'string', 'max:150'];

            $maxAnggotaTotal = Pengaturan::int('maks_anggota_kelompok', 4);
            $maxAnggotaTambahan = max(1, $maxAnggotaTotal - 1);

            $targetJumlah = (int) ($request->input('redirect_jumlah') ?: $request->input('jumlah_kelompok', 0));
            if ($targetJumlah >= 2 && $targetJumlah <= $maxAnggotaTotal) {
                $rules['anggota'] = ['required', 'array', 'size:'.($targetJumlah - 1)];
            } else {
                $rules['anggota'] = ['required', 'array', 'min:1', 'max:'.$maxAnggotaTambahan];
            }

            $rules['anggota.*.nama'] = ['required', 'string', 'max:255'];
            $rules['anggota.*.nim_nisn'] = ['required', 'string', 'max:50'];
            $rules['anggota.*.no_hp'] = ['required', 'string', 'max:30'];
            $rules['anggota.*.email'] = ['required', 'email:rfc', 'max:255'];
        }

        $shouldValidateTurnstile = $turnstileEnabled && (! app()->runningUnitTests() || $request->has('cf-turnstile-response') || config('services.turnstile.strict_testing', false));
        if ($shouldValidateTurnstile) {
            $rules['cf-turnstile-response'] = ['required', new TurnstileRule(isRegistration: true)];
        }

        $shouldValidateCaptcha = $captchaEnabled && (! app()->runningUnitTests() || $request->has('captcha') || config('captcha.strict_testing', false));
        if ($shouldValidateCaptcha) {
            $rules['captcha'] = ['required', 'string', 'size:4', new CaptchaRule];
        }

        $validated = $request->validate($rules, [
            'email.email' => 'Alamat email tidak valid. Gunakan format email resmi (contoh: nama@gmail.com).',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.regex' => 'Kata sandi harus mengandung kombinasi huruf dan angka.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'nama_kelompok.required' => 'Nama kelompok wajib diisi untuk pendaftaran rombongan tim.',
            'anggota.required' => 'Data anggota kelompok wajib diisi.',
            'anggota.size' => 'Jumlah anggota tim harus :size orang (sesuai kuota rombongan yang dipilih).',
            'anggota.min' => 'Minimal anggota kelompok adalah 1 orang rekan.',
            'anggota.max' => 'Maksimal anggota kelompok adalah :max orang rekan.',
            'anggota.*.nama.required' => 'Nama lengkap seluruh anggota tim wajib diisi.',
            'anggota.*.nim_nisn.required' => 'NIM / NISN seluruh anggota tim wajib diisi.',
            'anggota.*.no_hp.required' => 'Nomor WhatsApp seluruh anggota tim wajib diisi.',
            'anggota.*.email.required' => 'Alamat email seluruh anggota tim wajib diisi.',
        ]);

        $oauthData = $request->session()->get('oauth_register_data');

        $user = DB::transaction(function () use ($validated, $request, $isKelompok, $oauthData) {
            $user = new User;
            $user->nama = $validated['nama'];
            $user->email = $validated['email'];
            $user->no_telp = $validated['no_telp'] ?? $request->input('no_telp');
            $user->password = Hash::make($validated['password']);
            $user->role = RoleEnum::Peserta;
            $user->status_aktif = true;
            $user->email_verified_at = now();

            if (is_array($oauthData) && isset($oauthData['email'], $oauthData['provider'], $oauthData['provider_id'])) {
                if (strtolower(trim((string) $oauthData['email'])) === $user->email) {
                    $user->provider = (string) $oauthData['provider'];
                    $user->provider_id = (string) $oauthData['provider_id'];
                }
            }

            $user->save();

            if (! empty($validated['jurusan_id']) && ! empty($validated['jenjang'])) {
                $jenjangNormalized = match ($validated['jenjang']) {
                    'mahasiswa' => 's1',
                    default => $validated['jenjang'],
                };

                $profilData = [
                    'user_id' => $user->id,
                    'jenjang' => $jenjangNormalized,
                    'jurusan_id' => (int) $validated['jurusan_id'],
                    'institusi' => $validated['institusi'] ?? 'Institusi Belum Diatur',
                    'nim_nisn' => $validated['nim_nisn'] ?? '-',
                    'no_hp' => $user->no_telp ?? '-',
                ];

                if ($jenjangNormalized === 'smk' && $request->boolean('persetujuan_wali')) {
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

        // Anti-Session Fixation: Regenerasi sesi pengguna yang baru login
        $request->session()->regenerate();
        $request->session()->forget('oauth_register_data');

        $this->auditService->catat(
            tipe: TipeAuditEnum::Aksi,
            tabel: 'users',
            idData: $user->id,
            nilaiLama: null,
            nilaiBaru: ['nama' => $user->nama, 'email' => $user->email, 'no_telp' => $user->no_telp, 'role' => $user->role->value],
            detail: [
                'aksi' => 'registrasi_peserta_baru',
                'tipe' => $isKelompok ? 'kelompok' : 'mandiri',
                'ip_address' => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 255),
            ]
        );

        $preferensiJurusanId = $request->input('redirect_jurusan') ?: ($validated['jurusan_id'] ?? null);
        $preferensiBulanId = $request->input('redirect_bulan') ?: $request->input('bulan_magang_id');
        $preferensiBidangId = $request->input('redirect_bidang') ?: $request->input('bidang_id');

        if ($preferensiBulanId || $preferensiJurusanId || $preferensiBidangId) {
            session([
                'pendaftaran_preferensi' => [
                    'bulan_id' => $preferensiBulanId,
                    'bidang_id' => $preferensiBidangId,
                    'jurusan_id' => $preferensiJurusanId,
                    'tipe' => $isKelompok ? 'kelompok' : 'mandiri',
                    'jumlah' => $isKelompok ? (count($validated['anggota'] ?? []) + 1) : 1,
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
