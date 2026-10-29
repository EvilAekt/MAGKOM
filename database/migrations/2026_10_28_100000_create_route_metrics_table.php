<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('route_metrics', function (Blueprint $table) {
            $table->id();
            $table->string('method', 10);
            $table->string('route_name', 150)->nullable();
            $table->string('uri', 255);
            $table->unsignedSmallInteger('status_code');
            $table->decimal('duration_ms', 8, 2);
            $table->decimal('memory_mb', 8, 2)->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('ip', 45)->nullable();
            $table->timestamp('created_at')->useCurrent();

            // Indexes for performant telemetry aggregations and rolling window queries
            $table->index('created_at');
            $table->index(['route_name', 'created_at']);
            $table->index(['status_code', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('route_metrics');
    }
};
