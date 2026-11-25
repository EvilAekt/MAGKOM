<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\StatusReviewProgresEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgresLog extends Model
{
    use HasFactory;

    protected $table = 'progres_log';

    protected $fillable = [
        'tugas_id',
        'user_id',
        'catatan',
        'file',
        'link',
        'tgl_lapor',
        'status_review',
        'feedback',
    ];

    protected $casts = [
        'status_review' => StatusReviewProgresEnum::class,
        'tgl_lapor' => 'date',
    ];

    protected $appends = [
        'links',
    ];

    /**
     * Aksesor runtime untuk mendapatkan daftar tautan kiriman progres tugas.
     * Mendukung data warisan (URL tunggal), JSON array string, atau pemisah baris baru.
     *
     * @return array<int, string>
     */
    public function getLinksAttribute(): array
    {
        if (blank($this->link)) {
            return [];
        }

        $raw = trim((string) $this->link);
        if ($raw === '') {
            return [];
        }

        if (str_starts_with($raw, '[') && str_ends_with($raw, ']')) {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                return array_values(array_filter($decoded, fn ($item) => is_string($item) && trim($item) !== ''));
            }
        }

        if (str_contains($raw, "\n")) {
            return array_values(array_filter(array_map('trim', explode("\n", $raw))));
        }

        return [$raw];
    }

    public function tugas(): BelongsTo
    {
        return $this->belongsTo(Tugas::class, 'tugas_id');
    }

    /**
     * Peserta (anggota tim) yang mengunggah laporan progres ini.
     */
    public function pelapor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
