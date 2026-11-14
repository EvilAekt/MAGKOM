<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class Bidang extends Model
{
    use HasFactory;

    protected $table = 'bidang';

    public const CACHE_KEY_AKTIF = 'bidang_aktif_list';

    public const CACHE_TTL = 7200; // 2 jam

    protected $fillable = [
        'nama_bidang',
        'deskripsi',
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
        static::saved(function (self $bidang) {
            DB::afterCommit(function () {
                Cache::forget(self::CACHE_KEY_AKTIF);
            });
        });

        static::deleted(function (self $bidang) {
            DB::afterCommit(function () {
                Cache::forget(self::CACHE_KEY_AKTIF);
            });
        });
    }

    /**
     * Mengambil daftar seluruh bidang aktif yang ter-cache.
     *
     * @return Collection<int, self>
     */
    public static function cachedAktif(): Collection
    {
        return Cache::remember(self::CACHE_KEY_AKTIF, self::CACHE_TTL, function () {
            return self::where('aktif', true)->orderBy('nama_bidang')->get();
        });
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'bidang_id');
    }

    public function jurusan(): BelongsToMany
    {
        return $this->belongsToMany(Jurusan::class, 'bidang_jurusan', 'bidang_id', 'jurusan_id')->withTimestamps();
    }

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('aktif', true);
    }
}
