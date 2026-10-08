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
        'tanggal',
        'kegiatan',
        'file_laporan',
        'link_laporan',
        'status',
        'catatan_pembimbing',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'status' => StatusLogbookEnum::class,
    ];

    public function penempatan(): BelongsTo
    {
        return $this->belongsTo(Penempatan::class, 'penempatan_id');
    }
}
