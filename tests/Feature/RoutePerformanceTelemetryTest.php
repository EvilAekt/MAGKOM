<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\RealtimeEventTypeEnum;
use App\Models\RealtimeEvent;
use App\Models\RouteMetric;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoutePerformanceTelemetryTest extends TestCase
{
    use RefreshDatabase;

    private function createSuperAdmin(): User
    {
        $superadmin = User::factory()->create([
            'role' => 'superadmin',
            'status_aktif' => true,
        ]);
        assert($superadmin instanceof User);

        return $superadmin;
    }

    public function test_middleware_records_route_performance_to_database(): void
    {
        $superadmin = $this->createSuperAdmin();

        $this->actingAs($superadmin);

        $response = $this->get(route('superadmin.health.index'));

        $response->assertOk();

        // Verify route_metrics recorded this request
        $this->assertDatabaseHas('route_metrics', [
            'method' => 'GET',
            'route_name' => 'superadmin.health.index',
            'status_code' => 200,
        ]);

        $latestMetric = RouteMetric::latest('id')->first();
        $this->assertNotNull($latestMetric);
        $this->assertGreaterThan(0, $latestMetric->duration_ms);
        $this->assertEquals($superadmin->id, $latestMetric->user_id);
    }

    public function test_middleware_publishes_sse_realtime_event_on_termination(): void
    {
        $superadmin = $this->createSuperAdmin();

        $this->actingAs($superadmin);

        $response = $this->get(route('superadmin.health.index'));
        $response->assertOk();

        // Verify outbox has telemetri_route event for role superadmin
        $this->assertDatabaseHas('realtime_events', [
            'event_type' => RealtimeEventTypeEnum::TelemetriRoute->value,
            'role' => 'superadmin',
        ]);

        $event = RealtimeEvent::where('event_type', RealtimeEventTypeEnum::TelemetriRoute->value)->latest('id')->first();
        $this->assertNotNull($event);
        $this->assertEquals('GET', $event->payload['method']);
        $this->assertEquals('superadmin.health.index', $event->payload['route_name']);
        $this->assertArrayHasKey('duration_ms', $event->payload);
        $this->assertArrayHasKey('time', $event->payload);
    }

    public function test_health_monitor_renders_canvas_and_telemetry_kpis(): void
    {
        $superadmin = $this->createSuperAdmin();

        // Seed some route metrics
        RouteMetric::create([
            'method' => 'GET',
            'route_name' => 'peserta.dashboard',
            'uri' => '/peserta/dashboard',
            'status_code' => 200,
            'duration_ms' => 24.50,
            'memory_mb' => 12.40,
            'user_id' => $superadmin->id,
            'created_at' => now()->subMinutes(5),
        ]);

        RouteMetric::create([
            'method' => 'POST',
            'route_name' => 'peserta.logbook.store',
            'uri' => '/peserta/logbook',
            'status_code' => 200,
            'duration_ms' => 65.20,
            'memory_mb' => 14.10,
            'user_id' => $superadmin->id,
            'created_at' => now()->subMinutes(2),
        ]);

        $response = $this->actingAs($superadmin)->get(route('superadmin.health.index'));

        $response->assertOk();
        $response->assertSee('routeLatencyCanvas');
        $response->assertSee('Zero-Dependency Native HTML5 Canvas 2D');
        $response->assertSee('Rata-Rata Latensi');
        $response->assertSee('P95 Tail Latency');
        $response->assertSee('Total Sampel Request');
        $response->assertSee('peserta.logbook.store');
    }

    public function test_route_metric_prune_removes_old_records(): void
    {
        // 1. Create older than 7 days
        RouteMetric::create([
            'method' => 'GET',
            'route_name' => 'old.route',
            'uri' => '/old',
            'status_code' => 200,
            'duration_ms' => 30.0,
            'created_at' => now()->subDays(10),
        ]);

        // 2. Create recent
        RouteMetric::create([
            'method' => 'GET',
            'route_name' => 'new.route',
            'uri' => '/new',
            'status_code' => 200,
            'duration_ms' => 15.0,
            'created_at' => now(),
        ]);

        $this->assertEquals(2, RouteMetric::count());

        $pruned = RouteMetric::pruneOld(days: 7, keepMax: 100);

        $this->assertEquals(1, $pruned);
        $this->assertEquals(1, RouteMetric::count());
        $this->assertDatabaseHas('route_metrics', ['route_name' => 'new.route']);
        $this->assertDatabaseMissing('route_metrics', ['route_name' => 'old.route']);
    }

    public function test_middleware_ignores_stream_routes(): void
    {
        $superadmin = $this->createSuperAdmin();

        $this->actingAs($superadmin);

        // Access realtime stream
        $response = $this->get('/realtime/stream');

        // Should not record realtime/stream into route_metrics
        $this->assertDatabaseMissing('route_metrics', [
            'uri' => '/realtime/stream',
        ]);
    }
}
