<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Pengaturan extends Model
{
    use HasFactory;

    protected $table = 'pengaturan';

    protected $fillable = [
        'kunci',
        'nilai',
    ];

    /**
     * Cache key prefix.
     */
    private const CACHE_PREFIX = 'pengaturan_key_';

    /**
     * Mengambil nilai pengaturan dengan cache.
     */
    public static function getNilai(string $kunci, ?string $default = null): ?string
    {
        return Cache::rememberForever(self::CACHE_PREFIX . $kunci, function () use ($kunci, $default) {
            $setting = self::where('kunci', $kunci)->first();
            return $setting ? $setting->nilai : $default;
        });
    }

    /**
     * Helper umum untuk mengambil nilai pengaturan.
     */
    public static function get(string $kunci, mixed $default = null): mixed
    {
        $val = self::getNilai($kunci);
        return $val !== null ? $val : $default;
    }

    /**
     * Mengambil nilai sebagai integer.
     */
    public static function getInt(string $kunci, int $default = 0): int
    {
        $val = self::getNilai($kunci);
        return $val !== null ? (int) $val : $default;
    }

    /**
     * Mengambil nilai JSON sebagai array.
     *
     * @return array<string, mixed>
     */
    public static function getJson(string $kunci, array $default = []): array
    {
        $val = self::getNilai($kunci);
        if ($val === null) {
            return $default;
        }

        $decoded = json_decode($val, true);
        return is_array($decoded) ? $decoded : $default;
    }

    /**
     * Menghapus cache saat data disimpan atau dihapus.
     */
    protected static function booted(): void
    {
        static::saved(function (self $pengaturan) {
            Cache::forget(self::CACHE_PREFIX . $pengaturan->kunci);
        });

        static::deleted(function (self $pengaturan) {
            Cache::forget(self::CACHE_PREFIX . $pengaturan->kunci);
        });
    }

    /**
     * Bersihkan cache pengaturan kunci tertentu atau seluruhnya.
     */
    public static function clearCache(?string $kunci = null): void
    {
        if ($kunci) {
            Cache::forget(self::CACHE_PREFIX . $kunci);
        } else {
            Cache::forget('pengaturan_all');
            foreach (self::pluck('kunci') as $k) {
                Cache::forget(self::CACHE_PREFIX . $k);
            }
        }
    }
}
