<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\KompleksitasEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TugasTemplate extends Model
{
    use HasFactory;

    protected $table = 'tugas_template';

    protected $fillable = [
        'pembimbing_id',
        'judul',
        'deskripsi',
        'kategori',
        'kompleksitas',
        'link_referensi',
        'is_global',
    ];

    protected $casts = [
        'kompleksitas' => KompleksitasEnum::class,
        'is_global' => 'boolean',
    ];

    /**
     * Relasi ke pembimbing pembuat template.
     */
    public function pembimbing(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pembimbing_id');
    }

    /**
     * Scope template yang dapat diakses oleh pembimbing tertentu (miliknya atau template global kedinasan).
     */
    public function scopeAccessibleBy(Builder $query, int $pembimbingId): Builder
    {
        return $query->where('pembimbing_id', $pembimbingId)
            ->orWhere('is_global', true);
    }
}
