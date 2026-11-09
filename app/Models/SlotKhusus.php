<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SlotKhusus extends Model
{
    use HasFactory;

    protected $table = 'slot_khusus';

    protected $fillable = [
        'bulan_mulai',
        'jumlah',
        'terisi',
        'aktif',
        'dibuat_oleh',
    ];

    protected $casts = [
        'bulan_mulai' => 'date',
        'jumlah' => 'integer',
        'terisi' => 'integer',
        'aktif' => 'boolean',
        'dibuat_oleh' => 'integer',
    ];

    /**
     * Relasi ke SuperAdmin yang membuat slot khusus.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    /**
     * Relasi ke penempatan peserta magang di slot khusus ini.
     */
    public function penempatan(): HasMany
    {
        return $this->hasMany(Penempatan::class, 'slot_khusus_id');
    }

    /**
     * Aksesor sisa slot khusus (minimal 0).
     */
    public function getSisaAttribute(): int
    {
        return max(0, $this->jumlah - $this->terisi);
    }

    /**
     * Aksesor alias jumlah_slot untuk kompatibilitas atribut.
     */
    public function getJumlahSlotAttribute(): int
    {
        return (int) $this->jumlah;
    }

    /**
     * Aksesor alias terpakai untuk kompatibilitas atribut.
     */
    public function getTerpakaiAttribute(): int
    {
        return (int) $this->terisi;
    }

    /**
     * Scope slot aktif.
     */
    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('aktif', true);
    }
}
