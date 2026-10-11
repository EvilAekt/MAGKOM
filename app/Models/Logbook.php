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
     * Dapatkan label representasi minggu magang (misal: "Minggu ke-1").
     */
    public function getLabelMingguAttribute(): string
    {
        if ($this->minggu_ke) {
            return "Minggu ke-{$this->minggu_ke}";
        }

        if ($this->penempatan && $this->penempatan->tgl_mulai && $this->tanggal) {
            $diffWeeks = (int) $this->penempatan->tgl_mulai->diffInWeeks($this->tanggal) + 1;
            return "Minggu ke-{$diffWeeks}";
        }

        return "Minggu ke-1";
    }

    public function penempatan(): BelongsTo
    {
        return $this->belongsTo(Penempatan::class, 'penempatan_id');
    }
}
