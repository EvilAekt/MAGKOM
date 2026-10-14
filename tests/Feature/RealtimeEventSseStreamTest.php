<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\RealtimeEventTypeEnum;
use App\Enums\RoleEnum;
use App\Models\RealtimeEvent;
use App\Models\User;
use App\Services\RealtimeEventService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RealtimeEventSseStreamTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_from_sse_stream(): void
    {
        $response = $this->get('/realtime/stream');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_receives_valid_sse_stream_headers(): void
    {
        $user = User::factory()->create(['role' => RoleEnum::Admin]);

        $response = $this->actingAs($user)->get('/realtime/stream');

        $response->assertStatus(200);
        $this->assertEquals('text/event-stream; charset=UTF-8', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('no-store', (string) $response->headers->get('Cache-Control'));
        $this->assertEquals('no', $response->headers->get('X-Accel-Buffering'));
    }


    public function test_sse_stream_delivers_pending_event_for_user(): void
    {
        $user = User::factory()->create(['role' => RoleEnum::Peserta]);

        $service = app(RealtimeEventService::class);
        $event = $service->publish(
            RealtimeEventTypeEnum::StatusPendaftaran,
            ['title' => 'Diterima', 'message' => 'Selamat'],
            $user->id
        );

        $response = $this->actingAs($user)->get('/realtime/stream');

        $response->assertStatus(200);
        $content = $response->streamedContent();

        $this->assertStringContainsString('event: status_pendaftaran', $content);
        $this->assertStringContainsString('id: ' . $event->id, $content);
        $this->assertStringContainsString('"title":"Diterima"', $content);
    }

    public function test_sse_stream_filters_by_last_event_id(): void
    {
        $user = User::factory()->create(['role' => RoleEnum::Admin]);
        $service = app(RealtimeEventService::class);

        $event1 = $service->publish(
            RealtimeEventTypeEnum::PendaftaranBaru,
            ['title' => 'Event Pertama'],
            null,
            'admin'
        );

        $event2 = $service->publish(
            RealtimeEventTypeEnum::PendaftaranBaru,
            ['title' => 'Event Kedua'],
            null,
            'admin'
        );

        // Request dengan Last-Event-ID mengabaikan event 1
        $response = $this->actingAs($user)
            ->withHeaders(['Last-Event-ID' => (string) $event1->id])
            ->get('/realtime/stream');

        $content = $response->streamedContent();

        $this->assertStringNotContainsString('Event Pertama', $content);
        $this->assertStringContainsString('Event Kedua', $content);
        $this->assertStringContainsString('id: ' . $event2->id, $content);
    }

    public function test_sse_stream_isolates_user_events_anti_idor(): void
    {
        $userA = User::factory()->create(['role' => RoleEnum::Peserta]);
        $userB = User::factory()->create(['role' => RoleEnum::Peserta]);

        $service = app(RealtimeEventService::class);
        $service->publish(
            RealtimeEventTypeEnum::StatusPendaftaran,
            ['title' => 'Rahasia User A'],
            $userA->id
        );

        // User B membuka stream tidak boleh melihat notifikasi milik User A
        $response = $this->actingAs($userB)->get('/realtime/stream');
        $content = $response->streamedContent();

        $this->assertStringNotContainsString('Rahasia User A', $content);
    }

    public function test_mark_read_endpoint_updates_read_at_timestamp(): void
    {
        $user = User::factory()->create(['role' => RoleEnum::Peserta]);
        $service = app(RealtimeEventService::class);

        $event = $service->publish(
            RealtimeEventTypeEnum::StatusPendaftaran,
            ['title' => 'Pemberitahuan'],
            $user->id
        );

        $this->assertNull($event->read_at);

        $response = $this->actingAs($user)->postJson("/realtime/read/{$event->id}");

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $event->refresh();
        $this->assertNotNull($event->read_at);
    }
}
