<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use App\Services\LoginRateLimiterService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class DistributedBruteForceTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'email' => 'korban.bruteforce@diskominfo.go.id',
            'password' => Hash::make('PasswordValid123!'),
            'status_aktif' => true,
            'email_verified_at' => now(),
            'must_change_password' => false,
        ]);
    }

    /**
     * Uji 1: Percobaan berulang dari satu IP yang sama dibatasi.
     */
    public function test_single_ip_brute_force_is_throttled(): void
    {
        $ip = '192.168.1.50';

        // 3 kali percobaan salah dari IP yang sama
        for ($i = 0; $i < 3; $i++) {
            $response = $this->withServerVariables(['REMOTE_ADDR' => $ip])
                ->post(route('login'), [
                    'email' => $this->user->email,
                    'password' => 'WrongPassword!',
                ]);

            if ($i < 2) {
                $response->assertSessionHasErrors('email');
                $response->assertSessionMissing('lockout_seconds');
            }
        }

        // Percobaan ke-3 memicu lockout
        $response->assertSessionHas('lockout_seconds');

        // Percobaan ke-4 langsung ditolak (status 429 Too Many Requests atau lockout session)
        $blockedResponse = $this->withServerVariables(['REMOTE_ADDR' => $ip])
            ->post(route('login'), [
                'email' => $this->user->email,
                'password' => 'AnyPassword',
            ]);

        $this->assertTrue(
            $blockedResponse->status() === 429 || $blockedResponse->getSession()->has('lockout_seconds')
        );
    }

    /**
     * Uji 2: Serangan Distributed Brute-force terhadap akun yang sama dari BANYAK IP berbeda tetap dibatasi (Layer 1).
     */
    public function test_distributed_multi_ip_attack_on_same_account_is_blocked(): void
    {
        // Penyerang menggunakan 6 IP berbeda untuk menyerang 1 akun yang sama
        $distinctIps = [
            '10.0.0.1',
            '10.0.0.2',
            '10.0.0.3',
            '10.0.0.4',
            '10.0.0.5',
            '10.0.0.6',
        ];

        // 5 percobaan pertama dari 5 IP berbeda (1 per IP, total 5 percobaan pada akun)
        for ($i = 0; $i < 5; $i++) {
            $this->withServerVariables(['REMOTE_ADDR' => $distinctIps[$i]])
                ->post(route('login'), [
                    'email' => $this->user->email,
                    'password' => 'DistributedGuess'.$i,
                ]);
        }

        // Pada percobaan dari IP ke-6 (IP baru yang belum pernah mengirim request),
        // Layer 1 (Per-Account) HARUS memblokir request karena ambang batas akun (5x salah) terlampaui!
        $response = $this->withServerVariables(['REMOTE_ADDR' => $distinctIps[5]])
            ->post(route('login'), [
                'email' => $this->user->email,
                'password' => 'AnotherGuess',
            ]);

        $this->assertTrue(
            $response->status() === 429 || $response->getSession()->has('lockout_seconds')
        );
    }

    /**
     * Uji 3: Akun berbeda tidak saling mengunci secara tidak sengaja (Isolasi antar akun).
     */
    public function test_different_accounts_do_not_lock_each_other(): void
    {
        $userLain = User::factory()->create([
            'email' => 'pengguna.lain@diskominfo.go.id',
            'password' => Hash::make('PasswordLain123!'),
            'status_aktif' => true,
            'email_verified_at' => now(),
            'must_change_password' => false,
        ]);

        $attackerIp = '172.16.0.10';

        // Serang user pertama hingga terkunci dari IP ini
        for ($i = 0; $i < 3; $i++) {
            $this->withServerVariables(['REMOTE_ADDR' => $attackerIp])
                ->post(route('login'), [
                    'email' => $this->user->email,
                    'password' => 'WrongPass',
                ]);
        }

        // Pengguna kedua dari IP berbeda TIDAK boleh terpengaruh dan dapat login sukses
        $cleanIp = '172.16.0.20';
        $response = $this->withServerVariables(['REMOTE_ADDR' => $cleanIp])
            ->post(route('login'), [
                'email' => $userLain->email,
                'password' => 'PasswordLain123!',
            ]);

        $response->assertSessionHasNoErrors();
        $this->assertAuthenticatedAs($userLain);
    }

    /**
     * Uji 4: Pengguna yang sah dapat login sukses setelah lock dibersihkan (cooldown selesai).
     */
    public function test_valid_user_can_login_after_throttle_cleared(): void
    {
        $ip = '192.168.1.99';

        // Buat 3 percobaan salah hingga terkunci
        for ($i = 0; $i < 3; $i++) {
            $this->withServerVariables(['REMOTE_ADDR' => $ip])
                ->post(route('login'), [
                    'email' => $this->user->email,
                    'password' => 'BadPass',
                ]);
        }

        // Bersihkan throttle (simulasi cooldown 60 detik berlalu sepenuhnya)
        Cache::flush();
        app(LoginRateLimiterService::class)->clear($this->user->email, $ip);
        RateLimiter::clear($this->user->email.'|'.$ip);

        // Login dengan password benar berhasil
        $successResponse = $this->withServerVariables(['REMOTE_ADDR' => $ip])
            ->post(route('login'), [
                'email' => $this->user->email,
                'password' => 'PasswordValid123!',
            ]);

        $successResponse->assertSessionHasNoErrors();
        $this->assertAuthenticatedAs($this->user);
    }
}
