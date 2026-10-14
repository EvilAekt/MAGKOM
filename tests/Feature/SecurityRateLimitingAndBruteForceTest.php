<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\PengaturanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityRateLimitingAndBruteForceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PengaturanSeeder::class);
    }

    /**
     * NFR-S2 & OWASP A07: Login rate limiting blocks brute-force after 5 attempts.
     */
    public function test_login_rate_limiter_blocks_brute_force_after_threshold(): void
    {
        $user = User::factory()->create([
            'email' => 'peserta.target@example.com',
            'password' => 'PasswordBenar123!',
        ]);

        // 5 percobaan login gagal berturut-turut
        for ($i = 0; $i < 5; $i++) {
            $response = $this->post(route('login'), [
                'email' => 'peserta.target@example.com',
                'password' => 'PasswordSalah!',
            ]);
            $response->assertSessionHasErrors('email');
        }

        // Percobaan ke-6 harus di-throttle (HTTP 429 Too Many Requests)
        $blockedResponse = $this->post(route('login'), [
            'email' => 'peserta.target@example.com',
            'password' => 'PasswordSalah!',
        ]);

        $blockedResponse->assertStatus(429);
    }

    /**
     * OWASP A01: Broken Access Control - Tamu ditolak dari API internal.
     */
    public function test_guest_is_forbidden_or_redirected_from_all_staff_portals(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
        $this->get(route('pembimbing.dashboard'))->assertRedirect(route('login'));
        $this->get(route('superadmin.dashboard'))->assertRedirect(route('login'));
        $this->get(route('peserta.dashboard'))->assertRedirect(route('login'));
    }

    /**
     * OWASP A02: Cryptographic Failures - User passwords must be hashed using argon2id/bcrypt.
     */
    public function test_user_passwords_are_properly_hashed(): void
    {
        $user = User::factory()->create([
            'password' => 'PlaintextSecret123!',
        ]);

        $rawPasswordInDb = \Illuminate\Support\Facades\DB::table('users')
            ->where('id', $user->id)
            ->value('password');

        $this->assertNotEquals('PlaintextSecret123!', $rawPasswordInDb);
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('PlaintextSecret123!', $rawPasswordInDb));
    }
}
