<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\TipeAuditEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use RuntimeException;

class AuditLog extends Model
{
    use HasFactory;

    protected $table = 'audit_log';

    public $timestamps = false;

    protected $fillable = [
        'request_id',
        'user_id',
        'tipe',
        'aksi',
        'entitas',
        'entitas_id',
        'nilai_lama',
        'nilai_baru',
        'detail',
        'metode',
        'rute',
        'status_http',
        'durasi_ms',
        'ip',
        'user_agent',
        'waktu',
    ];

    protected $casts = [
        'tipe' => TipeAuditEnum::class,
        'nilai_lama' => 'array',
        'nilai_baru' => 'array',
        'detail' => 'array',
        'waktu' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Scope filter berdasarkan tipe audit.
     */
    public function scopeTipe(Builder $query, TipeAuditEnum $tipe): Builder
    {
        return $query->where('tipe', $tipe->value);
    }

    /**
     * Scope filter rentang waktu.
     */
    public function scopeRentangWaktu(Builder $query, string $dari, string $sampai): Builder
    {
        return $query->whereBetween('waktu', [$dari, $sampai]);
    }

    /**
     * Penegakan Append-Only: Tolak update dan delete di level Eloquent (NFR-A4).
     */
    protected static function booted(): void
    {
        static::updating(function () {
            throw new RuntimeException('Tabel audit_log bersifat append-only dan tidak dapat diubah (UPDATE dilarang).');
        });

        static::deleting(function () {
            throw new RuntimeException('Tabel audit_log bersifat append-only dan tidak dapat dihapus melalui aplikasi runtime.');
        });
    }
}
