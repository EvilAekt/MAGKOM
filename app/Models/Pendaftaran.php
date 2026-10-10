<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\StatusPendaftaranEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pendaftaran extends Model
{
    use HasFactory;

    protected $table = 'pendaftaran';

    protected $fillable = [
        'user_id',
        'bulan_magang_id',
        'kuota_id',
        'durasi_bulan',
        'status',
        'verifikator_id',
        'catatan',
        'diajukan_at',
        'diputuskan_at',
    ];

    protected $casts = [
        'status' => StatusPendaftaranEnum::class,
        'durasi_bulan' => 'integer',
        'diajukan_at' => 'datetime',
        'diputuskan_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function bulanMagang(): BelongsTo
    {
        return $this->belongsTo(BulanMagang::class, 'bulan_magang_id');
    }

    public function kuota(): BelongsTo
    {
        return $this->belongsTo(Kuota::class, 'kuota_id');
    }

    public function verifikator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verifikator_id');
    }

    public function dokumen(): HasMany
    {
        return $this->hasMany(Dokumen::class, 'pendaftaran_id');
    }

    public function penempatan(): HasOne
    {
        return $this->hasOne(Penempatan::class, 'pendaftaran_id');
    }

    /**
     * Accessor nomor pendaftaran terformat (#REG-XXXXX).
     */
    public function getNomorPendaftaranAttribute(): string
    {
        return '#REG-' . str_pad((string) $this->id, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Scope pendaftaran yang masih aktif (BR-17).
     * Pendaftaran dianggap aktif jika statusnya belum final (bukan ditolak).
     */
    public function scopeSedangAktif(Builder $query): Builder
    {
        return $query->whereIn('status', [
            StatusPendaftaranEnum::BerkasBelumLengkap->value,
            StatusPendaftaranEnum::Diajukan->value,
            StatusPendaftaranEnum::PerluPerbaikan->value,
            StatusPendaftaranEnum::WaitingList->value,
            StatusPendaftaranEnum::Diterima->value,
        ]);
    }
}
