<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kuota extends Model
{
    use HasFactory;

    protected $table = 'kuota';

    protected $fillable = [
        'bulan_magang_id',
        'jurusan_id',
        'jumlah',
        'terisi',
        'aktif',
    ];

    protected $casts = [
        'bulan_magang_id' => 'integer',
        'jurusan_id' => 'integer',
        'jumlah' => 'integer',
        'terisi' => 'integer',
        'aktif' => 'boolean',
    ];

    /**
     * Relasi ke periode bulan magang.
     */
    public function bulanMagang(): BelongsTo
    {
        return $this->belongsTo(BulanMagang::class, 'bulan_magang_id');
    }

    /**
     * Relasi ke master jurusan.
     */
    public function jurusan(): BelongsTo
    {
        return $this->belongsTo(Jurusan::class, 'jurusan_id');
    }

    /**
     * Aksesor sisa kuota (minimal 0).
     */
    public function getSisaAttribute(): int
    {
        return max(0, $this->jumlah - $this->terisi);
    }

    /**
     * Scope kuota aktif.
     */
    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('aktif', true);
    }
}
