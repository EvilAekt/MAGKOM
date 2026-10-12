<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DokumenVerifikasi extends Model
{
    use HasFactory;

    protected $table = 'dokumen_verifikasi';

    protected $fillable = [
        'kode_unik',
        'tipe_dokumen',
        'penempatan_id',
        'nomor_dokumen',
        'tanggal_terbit',
        'status_keabsahan',
        'metadata',
    ];

    protected $casts = [
        'tanggal_terbit' => 'date',
        'metadata' => 'array',
    ];

    public function penempatan(): BelongsTo
    {
        return $this->belongsTo(Penempatan::class, 'penempatan_id');
    }
}
