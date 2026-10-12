<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SurveiKepuasan extends Model
{
    use HasFactory;

    protected $table = 'survei_kepuasan';

    protected $fillable = [
        'penempatan_id',
        'skor_mentoring',
        'skor_tugas',
        'skor_fasilitas',
        'skor_nps',
        'umpan_balik',
        'saran',
    ];

    protected $casts = [
        'skor_mentoring' => 'integer',
        'skor_tugas' => 'integer',
        'skor_fasilitas' => 'integer',
        'skor_nps' => 'integer',
    ];

    public function penempatan(): BelongsTo
    {
        return $this->belongsTo(Penempatan::class, 'penempatan_id');
    }
}
