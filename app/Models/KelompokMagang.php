<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KelompokMagang extends Model
{
    use HasFactory;

    protected $table = 'kelompok_magang';

    protected $fillable = [
        'ketua_user_id',
        'nama_kelompok',
    ];

    /**
     * Relasi ke ketua kelompok (User).
     */
    public function ketua(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ketua_user_id');
    }

    /**
     * Relasi ke seluruh anggota tim.
     */
    public function anggota(): HasMany
    {
        return $this->hasMany(KelompokAnggota::class, 'kelompok_magang_id');
    }

    /**
     * Relasi ke riwayat pendaftaran magang tim ini.
     */
    public function pendaftaran(): HasMany
    {
        return $this->hasMany(Pendaftaran::class, 'kelompok_magang_id');
    }
}
