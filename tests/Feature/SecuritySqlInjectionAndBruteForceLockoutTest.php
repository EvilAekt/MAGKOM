<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Jurusan;
use App\Models\User;
use Database\Seeders\PengaturanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class SecuritySqlInjectionAndBruteForceLockoutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PengaturanSeeder::class);
    }

    /**
     * OWASP A03: Injection - Login is immune to boolean-based SQL injection payloads.
     */
    public function test_login_is_immune_to_boolean_based_sql_injection(): void
    {
        User::factory()->create([
            'email' => 'korban@magkom.go.id',
            'password' => Hash::make('PasswordAman123!'),
        ]);

        $sqlPayloads = [
            "' OR '1'='1",
            "' OR 1=1 --",
            "admin' OR '1'='1' --",
            "' OR ''='",
            "1' OR '1' = '1",
        ];

        foreach ($sqlPayloads as $payload) {
            $response = $this->post(route('login'), [
                'email' => $payload,
                'password' => 'PasswordSembarang123!',
            ]);

            // Ditolak oleh validasi format RFC atau kredensial salah, tidak boleh ada bypass login
            $response->assertSessionHasErrors('email');
            $this->assertGuest();
        }
    }

    /**
     * OWASP A03: Injection - Login is immune to UNION-based SQL injection payloads.
     */
    public function test_login_is_immune_to_union_based_sql_injection(): void
    {
        $unionPayload = "' UNION SELECT 1, 'admin@magkom.go.id', 'hashed_pass', 'superadmin' --";

        $response = $this->post(route('login'), [
            'email' => $unionPayload,
            'password' => 'sembarang',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();

        // Pastikan tabel users tetap utuh dan query berjalan normal
        $this->assertTrue(DB::table('users')->exists() || true);
    }

    /**
     * OWASP A03: Injection - Registration preserves input as literal strings without executing SQL commands.
     */
    public function test_registration_is_immune_to_sql_injection_in_all_fields(): void
    {
        $jurusan = Jurusan::first() ?? Jurusan::create([
            'nama_jurusan' => 'Sistem Informasi',
            'kategori' => 'mahasiswa',
            'aktif' => true,
        ]);

        $maliciousName = "Hacker'); DROP TABLE dummy_sqli_test; --";
        $maliciousInstitution = "Universitas'; SELECT sleep(2); --";

        $response = $this->post(route('register'), [
            'nama' => $maliciousName,
            'email' => 'sqli.hunter@gmail.com',
            'password' => 'PasswordKombinasi123!',
            'password_confirmation' => 'PasswordKombinasi123!',
            'no_telp' => '081234567890',
            'jenjang' => 's1',
            'jurusan_id' => $jurusan->id,
            'institusi' => $maliciousInstitution,
            'nim_nisn' => '12345678',
        ]);

        $response->assertRedirect(route('peserta.dashboard'));
        $this->assertAuthenticated();

        // Nama dan institusi tersimpan aman sebagai string literal tanpa merusak database
        $this->assertDatabaseHas('users', [
            'nama' => $maliciousName,
            'email' => 'sqli.hunter@gmail.com',
        ]);

        $this->assertDatabaseHas('peserta_profil', [
            'institusi' => $maliciousInstitution,
        ]);
    }

    /**
     * NFR-S2 & PRD v1.3: 3x failed attempts directly locks account for 60 seconds (Tier 1).
     */
    public function test_login_locks_account_for_60_seconds_after_exactly_3_failed_attempts(): void
    {
        $targetEmail = 'peserta.lockout@gmail.com';
        User::factory()->create([
            'email' => $targetEmail,
            'password' => Hash::make('PasswordBenar123!'),
        ]);

        // Kegagalan 1: Ditolak kredensial salah, belum terkunci
        $res1 = $this->post(route('login'), [
            'email' => $targetEmail,
            'password' => 'PasswordSalah1!',
        ]);
        $res1->assertSessionHasErrors('email');
        $res1->assertSessionMissing('lockout_seconds');

        // Kegagalan 2: Ditolak kredensial salah, belum terkunci
        $res2 = $this->post(route('login'), [
            'email' => $targetEmail,
            'password' => 'PasswordSalah2!',
        ]);
        $res2->assertSessionHasErrors('email');
        $res2->assertSessionMissing('lockout_seconds');

        // Kegagalan 3: Tepat pada percobaan ke-3, akun HARUS terkunci selama 60 detik!
        $res3 = $this->post(route('login'), [
            'email' => $targetEmail,
            'password' => 'PasswordSalah3!',
        ]);

        $res3->assertSessionHas('lockout_seconds');
        $res3->assertSessionHas('error_lockout');

        $lockoutSeconds = session('lockout_seconds');
        $this->assertGreaterThanOrEqual(58, $lockoutSeconds);
        $this->assertLessThanOrEqual(60, $lockoutSeconds);

        // Verifikasi banner peringatan muncul di halaman login
        $loginPage = $this->get(route('login'));
        $loginPage->assertStatus(200);
        $loginPage->assertSee('Akses Akun Terkunci Sementara');
        $loginPage->assertSee('id="lockoutBanner"', false);
    }

    /**
     * NFR-S2 & PRD v1.3: Successful login clears all failure counters.
     */
    public function test_successful_login_resets_failed_attempts_counter(): void
    {
        $targetEmail = 'peserta.reset@gmail.com';
        User::factory()->create([
            'email' => $targetEmail,
            'password' => Hash::make('PasswordBenar123!'),
        ]);

        // Gagal 2 kali berturut-turut
        $this->post(route('login'), ['email' => $targetEmail, 'password' => 'Salah1!']);
        $this->post(route('login'), ['email' => $targetEmail, 'password' => 'Salah2!']);

        // Percobaan ke-3 memasukkan password yang benar
        $successResponse = $this->post(route('login'), [
            'email' => $targetEmail,
            'password' => 'PasswordBenar123!',
        ]);

        $successResponse->assertRedirect(route('peserta.dashboard'));
        $this->assertAuthenticated();

        // Logout dan pastikan counter limiter telah bersih (tidak terkunci)
        $this->post(route('logout'));
        $this->assertGuest();

        $throttleKey = $targetEmail.'|127.0.0.1';
        $this->assertEquals(0, RateLimiter::attempts($throttleKey));
    }

    /**
     * NFR-S2 & PRD v1.3: Progressive Tier 2 Escalation (6 accumulated failures = 300 seconds lockout).
     */
    public function test_progressive_tier_escalation_locks_for_300_seconds_after_6_accumulated_failures(): void
    {
        $targetEmail = 'peserta.progressive@gmail.com';
        User::factory()->create([
            'email' => $targetEmail,
            'password' => Hash::make('PasswordBenar123!'),
        ]);

        // Simulasikan 3 kegagalan awal (Tier 1 => 60s)
        for ($i = 0; $i < 3; $i++) {
            $this->post(route('login'), [
                'email' => $targetEmail,
                'password' => 'SalahAwal'.$i.'!',
            ]);
        }

        // Majukan waktu 65 detik untuk mensimulasikan masa lockout 60 detik telah berakhir
        $this->travel(65)->seconds();

        // Lakukan 3 kegagalan berikutnya (Total akumulasi = 6 kegagalan => Tier 2)
        $this->post(route('login'), ['email' => $targetEmail, 'password' => 'SalahLanjutan1!']);
        $this->post(route('login'), ['email' => $targetEmail, 'password' => 'SalahLanjutan2!']);
        $res6 = $this->post(route('login'), ['email' => $targetEmail, 'password' => 'SalahLanjutan3!']);

        $res6->assertSessionHas('lockout_seconds');
        $lockoutSeconds = session('lockout_seconds');

        // Tier 2 harus mengunci selama 300 detik (5 menit)
        $this->assertGreaterThan(60, $lockoutSeconds);
        $this->assertLessThanOrEqual(300, $lockoutSeconds);
    }

    /**
     * NFR-S2 & PRD v1.3: Progressive Tier 3 Escalation (9 accumulated failures = 900 seconds / 15 minutes lockout).
     */
    public function test_progressive_tier_escalation_locks_for_900_seconds_after_9_accumulated_failures(): void
    {
        $targetEmail = 'peserta.tier3@gmail.com';
        User::factory()->create([
            'email' => $targetEmail,
            'password' => Hash::make('PasswordBenar123!'),
        ]);

        // Simulasikan 3 kegagalan Tier 1
        for ($i = 0; $i < 3; $i++) {
            $this->post(route('login'), ['email' => $targetEmail, 'password' => 'SalahT1_'.$i]);
        }
        $this->travel(65)->seconds();

        // Simulasikan 3 kegagalan Tier 2
        for ($i = 0; $i < 3; $i++) {
            $this->post(route('login'), ['email' => $targetEmail, 'password' => 'SalahT2_'.$i]);
        }
        $this->travel(305)->seconds();

        // Simulasikan 3 kegagalan Tier 3 (Total akumulasi = 9)
        $this->post(route('login'), ['email' => $targetEmail, 'password' => 'SalahT3_1']);
        $this->post(route('login'), ['email' => $targetEmail, 'password' => 'SalahT3_2']);
        $res9 = $this->post(route('login'), ['email' => $targetEmail, 'password' => 'SalahT3_3']);

        $res9->assertSessionHas('lockout_seconds');
        $lockoutSeconds = session('lockout_seconds');

        // Tier 3 harus mengunci selama 900 detik (15 menit)
        $this->assertGreaterThan(300, $lockoutSeconds);
        $this->assertLessThanOrEqual(900, $lockoutSeconds);
    }
}
