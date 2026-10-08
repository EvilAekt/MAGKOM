<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PenilaianDetail extends Model
{
    use HasFactory;

    protected $table = 'penilaian_detail';

    protected $fillable = [
        'penilaian_id',
        'rubrik_id',
        'skor',
        'bobot_snapshot',
    ];

    protected $casts = [
        'skor' => 'integer',
        'bobot_snapshot' => 'integer',
    ];

    public function penilaian(): BelongsTo
    {
        return $this->belongsTo(Penilaian::class, 'penilaian_id');
    }

    public function rubrik(): BelongsTo
    {
        return $this->belongsTo(Rubrik::class, 'rubrik_id');
    }
}
