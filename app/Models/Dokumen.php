<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\URL;

class Dokumen extends Model
{
    use HasFactory;

    protected $table = 'dokumen';

    protected $fillable = [
        'pendaftaran_id',
        'jenis_dokumen_id',
        'path',
        'nama_asli',
        'mime',
        'ukuran',
        'hash',
    ];

    protected $casts = [
        'ukuran' => 'integer',
    ];

    public function pendaftaran(): BelongsTo
    {
        return $this->belongsTo(Pendaftaran::class, 'pendaftaran_id');
    }

    public function jenisDokumen(): BelongsTo
    {
        return $this->belongsTo(JenisDokumen::class, 'jenis_dokumen_id');
    }

    /**
     * Menghasilkan tautan unduh privat bertanda tangan sementara (default 5 menit).
     */
    public function getSignedDownloadUrl(int $minutes = 5): string
    {
        return URL::temporarySignedRoute(
            'berkas.download',
            now()->addMinutes($minutes),
            ['dokumen' => $this->id]
        );
    }
}
