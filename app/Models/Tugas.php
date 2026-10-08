<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\KompleksitasEnum;
use App\Enums\StatusTugasEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tugas extends Model
{
    use HasFactory;

    protected $table = 'tugas';

    protected $fillable = [
        'penempatan_id',
        'judul',
        'deskripsi',
        'kategori',
        'kompleksitas',
        'deadline',
        'status',
    ];

    protected $casts = [
        'kompleksitas' => KompleksitasEnum::class,
        'status' => StatusTugasEnum::class,
        'deadline' => 'date',
    ];

    protected $appends = [
        'terlambat',
    ];

    /**
     * BR-16: Aksesor runtime untuk tugas terlambat.
     * Tugas dianggap terlambat jika deadline < hari ini dan status belum selesai.
     * Tidak disimpan di basis data.
     */
    public function getTerlambatAttribute(): bool
    {
        if ($this->status === StatusTugasEnum::Selesai) {
            return false;
        }

        if (! $this->deadline) {
            return false;
        }

        return $this->deadline->lt(Carbon::today());
    }

    /**
     * Scope untuk memfilter tugas yang terlambat secara query SQL.
     */
    public function scopeTerlambat(Builder $query): Builder
    {
        return $query->where('status', '!=', StatusTugasEnum::Selesai->value)
            ->whereDate('deadline', '<', Carbon::today());
    }

    public function penempatan(): BelongsTo
    {
        return $this->belongsTo(Penempatan::class, 'penempatan_id');
    }

    public function progresLogs(): HasMany
    {
        return $this->hasMany(ProgresLog::class, 'tugas_id')->latest();
    }
}
