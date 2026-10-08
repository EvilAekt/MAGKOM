<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\JalurEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\SumberPenempatanEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Penempatan extends Model
{
    use HasFactory;

    protected $table = 'penempatan';

    protected $fillable = [
        'pendaftaran_id',
        'peserta_id',
        'kuota_id',
        'slot_khusus_id',
        'bidang_id',
        'pembimbing_id',
        'jalur',
        'sumber',
        'dibuat_oleh',
        'tgl_mulai',
        'tgl_selesai',
        'tgl_ditetapkan_selesai',
        'tgl_nonaktif',
        'status',
    ];

    protected $casts = [
        'jalur' => JalurEnum::class,
        'sumber' => SumberPenempatanEnum::class,
        'status' => StatusPenempatanEnum::class,
        'tgl_mulai' => 'date',
        'tgl_selesai' => 'date',
        'tgl_ditetapkan_selesai' => 'date',
        'tgl_nonaktif' => 'date',
    ];

    public function pendaftaran(): BelongsTo
    {
        return $this->belongsTo(Pendaftaran::class, 'pendaftaran_id');
    }

    public function peserta(): BelongsTo
    {
        return $this->belongsTo(User::class, 'peserta_id');
    }

    public function pembimbing(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pembimbing_id');
    }

    public function kuota(): BelongsTo
    {
        return $this->belongsTo(Kuota::class, 'kuota_id');
    }

    public function slotKhusus(): BelongsTo
    {
        return $this->belongsTo(SlotKhusus::class, 'slot_khusus_id');
    }

    public function bidang(): BelongsTo
    {
        return $this->belongsTo(Bidang::class, 'bidang_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    public function tugas(): HasMany
    {
        return $this->hasMany(Tugas::class, 'penempatan_id');
    }

    public function logbooks(): HasMany
    {
        return $this->hasMany(Logbook::class, 'penempatan_id');
    }

    public function penilaian(): HasOne
    {
        return $this->hasOne(Penilaian::class, 'penempatan_id');
    }

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('status', StatusPenempatanEnum::Aktif);
    }
}
