<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JalurEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\StatusPenilaianEnum;
use App\Enums\SumberPenempatanEnum;
use App\Models\AuditLog;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\Kuota;
use App\Models\Logbook;
use App\Models\Penempatan;
use App\Models\Penilaian;
use App\Models\PesertaProfil;
use App\Models\Rubrik;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ReleaseTwoFeaturesTest extends TestCase
{
    use DatabaseTransactions;

    protected User $superAdmin;
    protected User $admin;
    protected User $pembimbing;
    protected User $peserta;
    protected Bidang $bidang;
    protected Jurusan $jurusan;
    protected Penempatan $penempatan;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('private');
        Carbon::setTestNow(Carbon::parse('2026-10-08 10:00:00', 'Asia/Jakarta'));

        $this->bidang = Bidang::firstOrCreate(
            ['nama_bidang' => 'Layanan Informatika'],
            ['aktif' => true]
        );

        $this->jurusan = Jurusan::firstOrCreate(
            ['nama_jurusan' => 'Sistem Informasi'],
            ['kategori' => 'mahasiswa', 'aktif' => true]
        );

        $this->superAdmin = new User();
        $this->superAdmin->nama = 'Super Admin R2';
        $this->superAdmin->email = 'superadmin.r2@kominfo.test';
        $this->superAdmin->password = 'secret123';
        $this->superAdmin->role = RoleEnum::SuperAdmin;
        $this->superAdmin->email_verified_at = now();
        $this->superAdmin->status_aktif = true;
        $this->superAdmin->save();

        $this->admin = new User();
        $this->admin->nama = 'Admin Pengelola R2';
        $this->admin->email = 'admin.r2@kominfo.test';
        $this->admin->password = 'secret123';
        $this->admin->role = RoleEnum::Admin;
        $this->admin->email_verified_at = now();
        $this->admin->status_aktif = true;
        $this->admin->save();

        $this->pembimbing = new User();
        $this->pembimbing->nama = 'Pembimbing Lapangan R2';
        $this->pembimbing->email = 'pembimbing.r2@kominfo.test';
        $this->pembimbing->password = 'secret123';
        $this->pembimbing->role = RoleEnum::Pembimbing;
        $this->pembimbing->email_verified_at = now();
        $this->pembimbing->status_aktif = true;
        $this->pembimbing->save();

        $this->peserta = new User();
        $this->peserta->nama = 'Peserta Uji R2';
        $this->peserta->email = 'peserta.r2@kampus.test';
        $this->peserta->password = 'secret123';
        $this->peserta->role = RoleEnum::Peserta;
        $this->peserta->email_verified_at = now();
        $this->peserta->status_aktif = true;
        $this->peserta->save();

        PesertaProfil::create([
            'user_id' => $this->peserta->id,
            'nim_nisn' => '22000181',
            'institusi' => 'Universitas Gadjah Mada',
            'jenjang' => 's1',
            'jurusan_id' => $this->jurusan->id,
            'no_hp' => '08123456789',
        ]);

        $this->penempatan = Penempatan::create([
            'peserta_id' => $this->peserta->id,
            'pembimbing_id' => $this->pembimbing->id,
            'bidang_id' => $this->bidang->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'tgl_mulai' => Carbon::parse('2026-10-01'),
            'tgl_selesai' => Carbon::parse('2026-12-31'),
            'status' => StatusPenempatanEnum::Aktif,
        ]);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    /**
     * Test F-15 (Lampiran Logbook Harian): Validasi link HTTPS, berkas PDF, disk privat, dan unduhan aman.
     */
    public function test_f15_lampiran_logbook_pdf_dan_link_https(): void
    {
        // 1. Skema selain HTTPS (misal http://) harus ditolak
        $invalidHttp = $this->actingAs($this->peserta)->postJson(route('peserta.logbook.store'), [
            'tanggal' => Carbon::today()->toDateString(),
            'kegiatan' => 'Mengerjakan analisis data trafik web portal',
            'link_laporan' => 'http://insecure-site.test/project',
        ]);
        $invalidHttp->assertStatus(422);
        $invalidHttp->assertJsonValidationErrors(['link_laporan']);

        // 2. Berkas bukan PDF harus ditolak
        $invalidFile = UploadedFile::fake()->create('laporan.docx', 500, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        $resInvalidFile = $this->actingAs($this->peserta)->postJson(route('peserta.logbook.store'), [
            'tanggal' => Carbon::today()->toDateString(),
            'kegiatan' => 'Mengerjakan analisis data trafik web portal',
            'file_laporan' => $invalidFile,
        ]);
        $resInvalidFile->assertStatus(422);
        $resInvalidFile->assertJsonValidationErrors(['file_laporan']);

        // 3. Simpan logbook dengan lampiran PDF valid dan link HTTPS valid
        $validPdf = UploadedFile::fake()->create('laporan-harian.pdf', 800, 'application/pdf');
        $storeRes = $this->actingAs($this->peserta)->post(route('peserta.logbook.store'), [
            'tanggal' => Carbon::today()->toDateString(),
            'kegiatan' => 'Mengerjakan analisis data trafik web portal dan konfigurasi DNS',
            'link_laporan' => 'https://github.com/EvilAekt/MAGKOM/commit/xyz',
            'file_laporan' => $validPdf,
        ], ['Accept' => 'application/json']);
        $storeRes->assertStatus(201);

        $logbook = Logbook::where('penempatan_id', $this->penempatan->id)
            ->whereDate('tanggal', Carbon::today())
            ->firstOrFail();

        $this->assertNotNull($logbook->file_laporan);
        Storage::disk('private')->assertExists($logbook->file_laporan);
        $this->assertEquals('https://github.com/EvilAekt/MAGKOM/commit/xyz', $logbook->link_laporan);

        // 4. Unduh berkas lampiran dari disk privat oleh peserta yang bersangkutan
        $downloadRes = $this->actingAs($this->peserta)->get(route('peserta.logbook.lampiran', $logbook));
        $downloadRes->assertStatus(200);
        $downloadRes->assertHeader('content-type', 'application/pdf');

        // 5. User lain tidak berhak mengunduh lampiran (HTTP 403)
        $userLain = new User();
        $userLain->nama = 'Orang Asing';
        $userLain->email = 'asing@test.com';
        $userLain->password = 'secret';
        $userLain->role = RoleEnum::Peserta;
        $userLain->email_verified_at = now();
        $userLain->status_aktif = true;
        $userLain->save();

        $forbiddenRes = $this->actingAs($userLain)->get(route('peserta.logbook.lampiran', $logbook));
        $forbiddenRes->assertStatus(403);
    }

    /**
     * Test F-16 (Surat Keterangan Diterima Magang PDF): Ekspor PDF resmi dan otorisasi.
     */
    public function test_f16_surat_keterangan_diterima_pdf_dan_otorisasi(): void
    {
        // 1. Peserta mengunduh surat miliknya
        $pesertaRes = $this->actingAs($this->peserta)->get(route('peserta.penempatan.surat-diterima', $this->penempatan));
        $pesertaRes->assertStatus(200);
        $pesertaRes->assertHeader('content-type', 'application/pdf');

        // 2. Admin dapat mengunduh surat penerimaan peserta
        $adminRes = $this->actingAs($this->admin)->get(route('admin.penempatan.surat-diterima', $this->penempatan));
        $adminRes->assertStatus(200);
        $adminRes->assertHeader('content-type', 'application/pdf');

        // 3. User lain ditolak mengunduh surat penerimaan orang lain
        $userLain = new User();
        $userLain->nama = 'Peserta Lain';
        $userLain->email = 'pesertalain@test.com';
        $userLain->password = 'secret';
        $userLain->role = RoleEnum::Peserta;
        $userLain->email_verified_at = now();
        $userLain->status_aktif = true;
        $userLain->save();

        $resForbidden = $this->actingAs($userLain)->get(route('peserta.penempatan.surat-diterima', $this->penempatan));
        $resForbidden->assertStatus(403);

        // 4. Verifikasi aksi tercatat di audit log
        $this->assertDatabaseHas('audit_log', [
            'entitas' => 'penempatan',
            'entitas_id' => $this->penempatan->id,
        ]);
    }

    /**
     * Test F-17 (Laporan & Ekspor Data): Sanitasi CSV Formula Injection, filter, dan pembatasan RBAC SuperAdmin pada nilai.
     */
    public function test_f17_laporan_ekspor_csv_dan_anti_formula_injection(): void
    {
        // Masukkan data peserta dengan karakter formula berbahaya (=CMD, +SUM, -DIFF, @LOOKUP)
        $pesertaHacker = new User();
        $pesertaHacker->nama = '=cmd|"/C calc"!A0';
        $pesertaHacker->email = 'calc@hacker.test';
        $pesertaHacker->password = 'secret';
        $pesertaHacker->role = RoleEnum::Peserta;
        $pesertaHacker->save();

        PesertaProfil::create([
            'user_id' => $pesertaHacker->id,
            'nim_nisn' => '+628999',
            'institusi' => '@GoogleSheetFormula',
            'jenjang' => 's1',
            'jurusan_id' => $this->jurusan->id,
            'no_hp' => '-08123',
        ]);

        $penempatanHacker = Penempatan::create([
            'peserta_id' => $pesertaHacker->id,
            'pembimbing_id' => $this->pembimbing->id,
            'bidang_id' => $this->bidang->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'tgl_mulai' => Carbon::parse('2026-10-01'),
            'tgl_selesai' => Carbon::parse('2026-12-31'),
            'status' => StatusPenempatanEnum::Aktif,
        ]);

        // 1. Ekspor Penempatan oleh Admin
        $resPenempatan = $this->actingAs($this->admin)->get(route('admin.laporan.penempatan'));
        $resPenempatan->assertStatus(200);
        $resPenempatan->assertHeader('content-type', 'text/csv; charset=UTF-8');

        $csvContent = $resPenempatan->streamedContent();

        // Formula injection harus dinetralkan dengan awalan kutip tunggal (')
        $this->assertStringContainsString("'=cmd", $csvContent, 'Formula injection = diawali petik');
        $this->assertStringContainsString("'+628999", $csvContent, 'Formula injection + diawali petik');
        $this->assertStringContainsString("'@GoogleSheetFormula", $csvContent, 'Formula injection @ diawali petik');

        // 2. Ekspor Nilai: Admin biasa DITOLAK (AC-17-1, A-20)
        $adminNilaiRes = $this->actingAs($this->admin)->get(route('admin.laporan.nilai'));
        $adminNilaiRes->assertStatus(403);

        // 3. Ekspor Nilai: SuperAdmin DIIZINKAN (AC-17-1)
        $superAdminNilaiRes = $this->actingAs($this->superAdmin)->get(route('admin.laporan.nilai'));
        $superAdminNilaiRes->assertStatus(200);
        $superAdminNilaiRes->assertHeader('content-type', 'text/csv; charset=UTF-8');

        // 4. Verifikasi bahwa ekspor tercatat di audit log (AC-13-4)
        $this->assertDatabaseHas('audit_log', [
            'entitas' => 'penempatan',
        ]);
        $this->assertDatabaseHas('audit_log', [
            'entitas' => 'penilaian',
        ]);
    }

    /**
     * Test F-14 (Captcha Cloudflare Turnstile): Token valid, invalid, fail-closed registrasi, dan fail-open login.
     */
    public function test_f14_turnstile_captcha_verifikasi_dan_fail_mode(): void
    {
        Config::set('services.turnstile.enabled', true);
        Config::set('services.turnstile.secret_key', '0x4AAAAAAATestDummySecretKey');

        Http::fake([
            '*challenges.cloudflare.com/*' => function ($request) {
                $token = $request['response'] ?? null;
                if ($token === 'invalid_bot_token') {
                    return Http::response(['success' => false, 'error-codes' => ['invalid-input-response']], 200);
                }
                if ($token === 'valid_human_token') {
                    return Http::response(['success' => true], 200);
                }
                // Token down / error
                return Http::response([], 500);
            },
        ]);

        // 1. Registrasi dengan token invalid -> ditolak (AC-14-1)
        $invalidReg = $this->post(route('register'), [
            'nama' => 'Pendaftar Baru Bot',
            'email' => 'bot@tester.test',
            'password' => 'secret1234',
            'password_confirmation' => 'secret1234',
            'cf-turnstile-response' => 'invalid_bot_token',
        ]);
        $invalidReg->assertSessionHasErrors(['cf-turnstile-response']);

        // 2. Registrasi dengan token valid -> sukses (AC-14-1)
        $validReg = $this->post(route('register'), [
            'nama' => 'Pendaftar Baru Valid',
            'email' => 'valid.person@tester.test',
            'password' => 'secret1234',
            'password_confirmation' => 'secret1234',
            'cf-turnstile-response' => 'valid_human_token',
        ]);
        $validReg->assertRedirect(route('peserta.profil.show'));
        $this->assertDatabaseHas('users', ['email' => 'valid.person@tester.test']);

        // Logout user agar kembali berstatus guest sebelum uji registrasi saat service down
        \Illuminate\Support\Facades\Auth::logout();

        // 3. Layanan Cloudflare down (timeout / network error):
        // AC-14-2: Registrasi DITOLAK saat captcha down (Fail-Closed)
        $failClosedReg = $this->post(route('register'), [
            'nama' => 'Pendaftar Baru Saat Down',
            'email' => 'failclosed@tester.test',
            'password' => 'secret1234',
            'password_confirmation' => 'secret1234',
            'cf-turnstile-response' => 'token_any',
        ]);
        $failClosedReg->assertSessionHasErrors(['cf-turnstile-response']);

        // AC-14-2: Login DIIZINKAN saat captcha down dengan perlindungan rate limit (Fail-Open)
        $failOpenLogin = $this->post(route('login'), [
            'email' => 'valid.person@tester.test',
            'password' => 'secret1234',
            'cf-turnstile-response' => 'token_any',
        ]);
        $failOpenLogin->assertRedirect(route('peserta.pendaftaran.index'));
        $this->assertAuthenticated();
    }
}
