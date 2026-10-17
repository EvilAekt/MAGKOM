<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\StatusPenilaianEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Penilaian extends Model
{
    use HasFactory;

    protected $table = 'penilaian';

    protected $fillable = [
        'penempatan_id',
        'pembimbing_id',
        'nilai_akhir',
        'predikat',
        'catatan',
        'status',
    ];

    protected $casts = [
        'nilai_akhir' => 'float',
        'status' => StatusPenilaianEnum::class,
    ];

    public function penempatan(): BelongsTo
    {
        return $this->belongsTo(Penempatan::class, 'penempatan_id');
    }

    public function pembimbing(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pembimbing_id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(PenilaianDetail::class, 'penilaian_id');
    }

    public function riwayats(): HasMany
    {
        return $this->hasMany(PenilaianRiwayat::class, 'penilaian_id')->latest('waktu');
    }

    /**
     * Alias untuk relasi riwayat (tunggal) agar kompatibel dengan pemanggilan singular.
     */
    public function riwayat(): HasMany
    {
        return $this->riwayats();
    }

    public function isFinal(): bool
    {
        return $this->status === StatusPenilaianEnum::Final;
    }
}
