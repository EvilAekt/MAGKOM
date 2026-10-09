<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\JenjangEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PesertaProfil extends Model
{
    use HasFactory;

    protected $table = 'peserta_profil';

    protected $fillable = [
        'user_id',
        'nim_nisn',
        'institusi',
        'jurusan_id',
        'jenjang',
        'no_hp',
        'nama_kontak_darurat',
        'no_kontak_darurat',
        'hubungan_kontak_darurat',
    ];

    protected $casts = [
        'jenjang' => JenjangEnum::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function jurusan(): BelongsTo
    {
        return $this->belongsTo(Jurusan::class, 'jurusan_id');
    }
}
