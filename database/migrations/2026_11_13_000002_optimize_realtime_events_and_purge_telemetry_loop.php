<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Bersihkan 84.000+ data telemetri loop dari realtime_events & route_metrics
        if (Schema::hasTable('realtime_events')) {
            DB::table('realtime_events')
                ->where('event_type', 'telemetri_route')
                ->delete();
        }

        if (Schema::hasTable('route_metrics')) {
            DB::table('route_metrics')
                ->where('uri', 'like', '/realtime/read%')
                ->delete();
        }

        // 2. Tambahkan composite index untuk mempercepat query getPendingEvents()
        if (Schema::hasTable('realtime_events')) {
            $driver = DB::getDriverName();
            if ($driver === 'sqlite') {
                Schema::table('realtime_events', function (Blueprint $table) {
                    $table->index(['role', 'read_at', 'id']);
                    $table->index(['user_id', 'read_at', 'id']);
                });
            } else {
                $existingIndexes = collect(DB::select('SHOW INDEX FROM realtime_events'))->pluck('Key_name')->unique()->toArray();

                Schema::table('realtime_events', function (Blueprint $table) use ($existingIndexes) {
                    if (! in_array('realtime_events_role_read_at_id_index', $existingIndexes, true)) {
                        $table->index(['role', 'read_at', 'id'], 'realtime_events_role_read_at_id_index');
                    }

                    if (! in_array('realtime_events_user_id_read_at_id_index', $existingIndexes, true)) {
                        $table->index(['user_id', 'read_at', 'id'], 'realtime_events_user_id_read_at_id_index');
                    }
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('realtime_events')) {
            $driver = DB::getDriverName();
            if ($driver === 'sqlite') {
                Schema::table('realtime_events', function (Blueprint $table) {
                    $table->dropIndex(['role', 'read_at', 'id']);
                    $table->dropIndex(['user_id', 'read_at', 'id']);
                });
            } else {
                $existingIndexes = collect(DB::select('SHOW INDEX FROM realtime_events'))->pluck('Key_name')->unique()->toArray();

                Schema::table('realtime_events', function (Blueprint $table) use ($existingIndexes) {
                    if (in_array('realtime_events_role_read_at_id_index', $existingIndexes, true)) {
                        $table->dropIndex('realtime_events_role_read_at_id_index');
                    }

                    if (in_array('realtime_events_user_id_read_at_id_index', $existingIndexes, true)) {
                        $table->dropIndex('realtime_events_user_id_read_at_id_index');
                    }
                });
            }
        }
    }
};
