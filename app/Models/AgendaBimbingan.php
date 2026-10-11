<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgendaBimbingan extends Model
{
    use HasFactory;

    protected $table = 'agenda_bimbingan';

    protected $fillable = [
        'penempatan_id',
        'pembimbing_id',
        'judul_kegiatan',
        'tipe',
        'tgl_bimbingan',
        'jam_mulai',
        'lokasi_ruang',
        'notulensi_arahan',
        'status',
    ];

    protected $casts = [
        'tgl_bimbingan' => 'date',
    ];

    public function penempatan(): BelongsTo
    {
        return $this->belongsTo(Penempatan::class, 'penempatan_id');
    }

    public function pembimbing(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pembimbing_id');
    }
}
