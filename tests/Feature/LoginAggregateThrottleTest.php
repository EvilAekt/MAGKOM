<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\PengaturanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class LoginAggregateThrottleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PengaturanSeeder::class);
        RateLimiter::clear('login');
    }

    public function test_login_throttles_individual_account_after_three_attempts(): void
    {
        User::factory()->create([
            'email' => 'victim@example.com',
            'password' => 'ValidPassword123!',
        ]);

        for ($i = 0; $i < 3; $i++) {
            $response = $this->post(route('login'), [
                'email' => 'victim@example.com',
                'password' => 'WrongPassword!',
            ]);
            $response->assertSessionHasErrors('email');
        }

        $blocked = $this->post(route('login'), [
            'email' => 'victim@example.com',
            'password' => 'WrongPassword!',
        ]);
        $blocked->assertStatus(429);
    }

    public function test_login_throttles_aggregate_ip_spray_after_twenty_attempts(): void
    {
        // Password spraying attack: 20 different emails from the same IP (127.0.0.1)
        for ($i = 1; $i <= 20; $i++) {
            $email = "spray_user_{$i}@example.com";
            $response = $this->post(route('login'), [
                'email' => $email,
                'password' => 'WrongPassword!',
            ]);
            $response->assertSessionHasErrors('email');
        }

        // Attempt 21 from the same IP must be rejected with HTTP 429 even though the email is brand new
        $sprayedResponse = $this->post(route('login'), [
            'email' => 'spray_user_21@example.com',
            'password' => 'WrongPassword!',
        ]);

        $sprayedResponse->assertStatus(429);
    }

    public function test_reset_password_route_throttles_after_six_attempts(): void
    {
        for ($i = 0; $i < 6; $i++) {
            $response = $this->post(route('password.update'), [
                'token' => 'invalid-token',
                'email' => "user{$i}@example.com",
                'password' => 'NewPassword123!',
                'password_confirmation' => 'NewPassword123!',
            ]);
            $this->assertNotEquals(429, $response->getStatusCode());
        }

        $blocked = $this->post(route('password.update'), [
            'token' => 'invalid-token',
            'email' => 'user_blocked@example.com',
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ]);

        $blocked->assertStatus(429);
    }
}
