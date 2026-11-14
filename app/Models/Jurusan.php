<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class Jurusan extends Model
{
    use HasFactory;

    protected $table = 'jurusan';

    public const CACHE_KEY_AKTIF = 'jurusan_aktif_list';

    public const CACHE_TTL = 7200; // 2 jam

    protected $fillable = [
        'nama_jurusan',
        'kategori',
        'aktif',
    ];

    protected $casts = [
        'aktif' => 'boolean',
    ];

    /**
     * Cache invalidation transaksional via DB::afterCommit().
     */
    protected static function booted(): void
    {
        static::saved(function (self $jurusan) {
            DB::afterCommit(function () {
                Cache::forget(self::CACHE_KEY_AKTIF);
            });
        });

        static::deleted(function (self $jurusan) {
            DB::afterCommit(function () {
                Cache::forget(self::CACHE_KEY_AKTIF);
            });
        });
    }

    /**
     * Mengambil daftar seluruh jurusan aktif yang ter-cache.
     *
     * @return Collection<int, self>
     */
    public static function cachedAktif(): Collection
    {
        return Cache::remember(self::CACHE_KEY_AKTIF, self::CACHE_TTL, function () {
            return self::where('aktif', true)->orderBy('nama_jurusan')->get();
        });
    }

    /**
     * Relasi ke Bidang yang menerima jurusan ini.
     */
    public function bidang(): BelongsToMany
    {
        return $this->belongsToMany(Bidang::class, 'bidang_jurusan', 'jurusan_id', 'bidang_id')->withTimestamps();
    }

    /**
     * Scope jurusan yang aktif.
     */
    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('aktif', true);
    }

    /**
     * Scope filter berdasarkan kategori ('smk' atau 'mahasiswa').
     */
    public function scopeKategori(Builder $query, string $kategori): Builder
    {
        return $query->where('kategori', $kategori);
    }
}
