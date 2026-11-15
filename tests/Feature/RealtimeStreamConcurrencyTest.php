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
        config(['services.realtime.sse_enabled' => true]);
        $this->seed(PermissionSeeder::class);
    }

    protected function tearDown(): void
    {
        config(['services.realtime.sse_enabled' => true]);
        parent::tearDown();
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

    public function test_sse_feature_toggle_can_disable_sse_rendering(): void
    {
        config(['services.realtime.sse_enabled' => false]);

        $user = User::factory()->create([
            'status_aktif' => true,
            'email_verified_at' => now(),
            'must_change_password' => false,
        ]);

        $view = $this->actingAs($user)->blade('<x-sse-toast-listener />');
        $view->assertSee('data-sse-enabled="false"', false);
        $view->assertSee('data-poll-url', false);

        // Saat SSE dinonaktifkan, endpoint stream mengembalikan status 503 dengan tautan fallback
        $streamResponse = $this->actingAs($user)->getJson('/realtime/stream');
        $streamResponse->assertStatus(503);
        $streamResponse->assertJson([
            'error' => 'SSE Disabled',
        ]);

        // Fallback polling tetap dapat diakses dan merespons cepat tanpa loop
        $pollResponse = $this->actingAs($user)->getJson('/realtime/poll');
        $pollResponse->assertOk();
        $pollResponse->assertJsonStructure([
            'status',
            'last_event_id',
            'polling_interval_ms',
            'events',
        ]);

        config(['services.realtime.sse_enabled' => true]);
        $viewEnabled = $this->actingAs($user)->blade('<x-sse-toast-listener />');
        $viewEnabled->assertSee('data-sse-enabled="true"', false);
    }

    public function test_unauthenticated_request_to_poll_is_redirected_or_forbidden(): void
    {
        $response = $this->get('/realtime/poll');
        $response->assertRedirect('/login');
    }
}
