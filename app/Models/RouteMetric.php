<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RouteMetric extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'route_metrics';

    protected $fillable = [
        'method',
        'route_name',
        'uri',
        'status_code',
        'duration_ms',
        'memory_mb',
        'user_id',
        'ip',
        'created_at',
    ];

    protected $casts = [
        'duration_ms' => 'float',
        'memory_mb' => 'float',
        'status_code' => 'integer',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Prune metrics older than given days or beyond a rolling limit.
     */
    public static function pruneOld(int $days = 7, int $keepMax = 5000): int
    {
        $deletedTime = static::where('created_at', '<', now()->subDays($days))->delete();

        // If table still exceeds $keepMax, keep only the latest $keepMax
        $totalCount = static::count();
        $deletedExcess = 0;

        if ($totalCount > $keepMax) {
            $cutoffId = static::orderBy('id', 'desc')
                ->skip($keepMax)
                ->value('id');

            if ($cutoffId) {
                $deletedExcess = static::where('id', '<=', $cutoffId)->delete();
            }
        }

        return $deletedTime + $deletedExcess;
    }
}
