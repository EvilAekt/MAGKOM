<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KelompokAnggota extends Model
{
    use HasFactory;

    protected $table = 'kelompok_anggota';

    protected $fillable = [
        'kelompok_magang_id',
        'nama',
        'nim_nisn',
        'no_hp',
        'email',
    ];

    /**
     * Relasi ke kelompok magang induk.
     */
    public function kelompokMagang(): BelongsTo
    {
        return $this->belongsTo(KelompokMagang::class, 'kelompok_magang_id');
    }
}
