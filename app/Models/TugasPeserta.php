<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TugasPeserta extends Model
{
    use HasFactory;

    protected $table = 'tugas_peserta';

    protected $fillable = [
        'tugas_id',
        'penempatan_id',
        'user_id',
    ];

    /**
     * Relasi ke tugas induk.
     */
    public function tugas(): BelongsTo
    {
        return $this->belongsTo(Tugas::class, 'tugas_id');
    }

    /**
     * Relasi ke penempatan peserta.
     */
    public function penempatan(): BelongsTo
    {
        return $this->belongsTo(Penempatan::class, 'penempatan_id');
    }

    /**
     * Relasi ke akun user peserta yang ditugaskan.
     *
     * @return BelongsTo<User, TugasPeserta>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi alias ke peserta (User) yang ditugaskan.
     *
     * @return BelongsTo<User, TugasPeserta>
     */
    public function peserta(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
