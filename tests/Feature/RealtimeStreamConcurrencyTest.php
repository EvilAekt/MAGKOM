<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class RealtimeStreamConcurrencyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PermissionSeeder::class);
    }

    public function test_unauthenticated_request_to_sse_is_redirected_to_login(): void
    {
        $response = $this->get('/realtime/stream');
        $response->assertRedirect('/login');
    }

    public function test_inactive_user_cannot_access_sse_stream(): void
    {
        $user = User::factory()->create([
            'status_aktif' => false,
            'email_verified_at' => now(),
            'must_change_password' => false,
        ]);

        $response = $this->actingAs($user)->get('/realtime/stream');
        $response->assertRedirect('/login');
    }

    public function test_user_must_change_password_is_redirected(): void
    {
        $user = User::factory()->create([
            'status_aktif' => true,
            'email_verified_at' => now(),
            'must_change_password' => true,
        ]);

        $response = $this->actingAs($user)->get('/realtime/stream');
        $response->assertRedirect(route('password.ganti'));
    }

    public function test_authenticated_user_can_initiate_sse_stream(): void
    {
        $user = User::factory()->create([
            'status_aktif' => true,
            'email_verified_at' => now(),
            'must_change_password' => false,
        ]);

        $response = $this->actingAs($user)->get('/realtime/stream');

        $response->assertOk();
        $this->assertStringContainsString('text/event-stream', (string) $response->headers->get('Content-Type'));
    }

    public function test_concurrent_sse_stream_request_returns_429_too_many_requests(): void
    {
        $user = User::factory()->create([
            'status_aktif' => true,
            'email_verified_at' => now(),
            'must_change_password' => false,
        ]);

        // Simulasikan koneksi pertama yang sedang aktif memegang lock
        $lockKey = "sse:stream:user:{$user->id}";
        Cache::put($lockKey, true, 30);

        // Request kedua saat stream pertama masih aktif
        $response = $this->actingAs($user)->getJson('/realtime/stream');

        $response->assertStatus(429);
        $response->assertHeader('Retry-After', '10');
        $response->assertJson([
            'error' => 'Too Many Concurrent SSE Connections',
        ]);

        // Setelah lock dilepas (koneksi pertama selesai)
        Cache::forget($lockKey);

        // Request berikutnya berhasil memperoleh koneksi
        $nextResponse = $this->actingAs($user)->get('/realtime/stream');
        $nextResponse->assertOk();
    }
}
