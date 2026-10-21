<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\TipeAuditEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
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
        'channel',
        'severity',
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

    public function scopeChannel(Builder $query, string $channel): Builder
    {
        return $query->where('channel', $channel);
    }

    public function scopeSeverity(Builder $query, string $severity): Builder
    {
        return $query->where('severity', $severity);
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
        static::creating(function (AuditLog $log) {
            if (empty($log->request_id)) {
                $log->request_id = (string) Str::uuid();
            }
            if (empty($log->waktu)) {
                $log->waktu = now('Asia/Jakarta');
            }
            if (empty($log->channel)) {
                $log->channel = 'all-actions-stream';
            }
            if (empty($log->severity)) {
                $log->severity = 'info';
            }
            if (empty($log->ip)) {
                $log->ip = request()->ip() ?: '127.0.0.1';
            }
            if (empty($log->metode)) {
                $log->metode = request()->method() ?: 'GET';
            }
            if (empty($log->rute)) {
                $log->rute = request()->path() ?: '/';
            }
            if (empty($log->user_agent)) {
                $log->user_agent = substr((string) (request()->userAgent() ?: 'MagKom System'), 0, 255);
            }
        });

        static::updating(function () {
            throw new RuntimeException('Tabel audit_log bersifat append-only dan tidak dapat diubah (UPDATE dilarang).');
        });

        static::deleting(function () {
            throw new RuntimeException('Tabel audit_log bersifat append-only dan tidak dapat dihapus melalui aplikasi runtime.');
        });
    }
}
