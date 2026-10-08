<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PenilaianRiwayat extends Model
{
    use HasFactory;

    protected $table = 'penilaian_riwayat';

    public $timestamps = false;

    protected $fillable = [
        'penilaian_id',
        'diubah_oleh',
        'nilai_lama',
        'nilai_baru',
        'alasan',
        'waktu',
    ];

    protected $casts = [
        'nilai_lama' => 'array',
        'nilai_baru' => 'array',
        'waktu' => 'datetime',
    ];

    public function penilaian(): BelongsTo
    {
        return $this->belongsTo(Penilaian::class, 'penilaian_id');
    }

    public function pengubah(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diubah_oleh');
    }
}
