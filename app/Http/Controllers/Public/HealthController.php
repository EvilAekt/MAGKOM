<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class HealthController extends Controller
{
    /**
     * Memeriksa kesiapan aplikasi, database, dan media penyimpanan (NFR-O1).
     */
    public function __invoke(): JsonResponse
    {
        $dbOk = false;
        $storageOk = false;

        try {
            DB::connection()->getPdo();
            $dbOk = true;
        } catch (Throwable $e) {
            $dbOk = false;
        }

        try {
            // Gunakan status ter-cache selama 60 detik untuk mencegah disk-thrashing & DoS I/O
            $storageOk = (bool) Cache::remember('health_storage_probe_status', 60, function () {
                $probeFile = 'health_probe_'.uniqid().'.tmp';
                Storage::disk('private')->put($probeFile, 'ok');
                $exists = Storage::disk('private')->exists($probeFile);
                if ($exists) {
                    Storage::disk('private')->delete($probeFile);
                }

                return $exists;
            });
        } catch (Throwable $e) {
            $storageOk = false;
        }

        $allOk = $dbOk && $storageOk;

        return response()->json([
            'status' => $allOk ? 'ok' : 'degraded',
            'database' => $dbOk,
            'storage' => $storageOk,
            'timestamp' => now('Asia/Jakarta')->toIso8601String(),
        ], $allOk ? 200 : 503);
    }
}
