<?php

declare(strict_types=1);

namespace App\Services;

use Closure;
use Throwable;

abstract class AuditedService
{
    public function __construct(protected AuditService $audit) {}

    /**
     * Membungkus eksekusi method service untuk mencatat durasi, argumen, dan hasil ke audit_log (AC-13-3).
     */
    protected function jalankan(string $metode, array $argumen, Closure $aksi): mixed
    {
        $mulai = hrtime(true);

        try {
            $hasil = $aksi();
            $this->audit->service(static::class, $metode, $argumen, 'ok', $this->hitungDurasiMs($mulai));

            return $hasil;
        } catch (Throwable $e) {
            $this->audit->service(static::class, $metode, $argumen, 'gagal:'.class_basename($e), $this->hitungDurasiMs($mulai));
            throw $e;
        }
    }

    /**
     * Konversi hrtime nanodetik ke milidetik.
     */
    private function hitungDurasiMs(int|float $mulai): int
    {
        return (int) round((hrtime(true) - $mulai) / 1e6);
    }
}
