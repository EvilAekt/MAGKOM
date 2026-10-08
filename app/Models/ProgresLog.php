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
        'catatan',
        'file',
        'tgl_lapor',
        'status_review',
        'feedback',
    ];

    protected $casts = [
        'status_review' => StatusReviewProgresEnum::class,
        'tgl_lapor' => 'date',
    ];

    public function tugas(): BelongsTo
    {
        return $this->belongsTo(Tugas::class, 'tugas_id');
    }
}
