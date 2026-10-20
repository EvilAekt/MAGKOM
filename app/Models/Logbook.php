<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\StatusLogbookEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Logbook extends Model
{
    use HasFactory;

    protected $table = 'logbook';

    protected $fillable = [
        'penempatan_id',
        'minggu_ke',
        'tanggal',
        'kegiatan',
        'file_laporan',
        'link_laporan',
        'status',
        'catatan_pembimbing',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'minggu_ke' => 'integer',
        'status' => StatusLogbookEnum::class,
    ];

    /**
     * Dapatkan nomor minggu magang (dengan fallback otomatis jika kolom di DB bernilai null).
     */
    public function getMingguKeAttribute($value): ?int
    {
        if ($value !== null) {
            return (int) $value;
        }

        if ($this->penempatan && $this->penempatan->tgl_mulai && $this->tanggal) {
            return (int) floor($this->penempatan->tgl_mulai->diffInDays($this->tanggal) / 7) + 1;
        }

        return null;
    }

    /**
     * Dapatkan label representasi minggu magang (misal: "Minggu ke-1").
     */
    public function getLabelMingguAttribute(): string
    {
        $minggu = $this->minggu_ke;
        if ($minggu) {
            return "Minggu ke-{$minggu}";
        }

        return 'Minggu ke-1';
    }

    public function penempatan(): BelongsTo
    {
        return $this->belongsTo(Penempatan::class, 'penempatan_id');
    }
}
