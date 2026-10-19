<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\StatusKetersediaanEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BulanMagang extends Model
{
    use HasFactory;

    protected $table = 'bulan_magang';

    protected $fillable = [
        'nama_bulan',
        'bulan_mulai',
        'tgl_buka',
        'tgl_tutup',
        'override_status',
        'aktif',
    ];

    protected $casts = [
        'bulan_mulai' => 'date',
        'tgl_buka' => 'date',
        'tgl_tutup' => 'date',
        'override_status' => StatusKetersediaanEnum::class,
        'aktif' => 'boolean',
    ];

    /**
     * Relasi ke kuota umum per jurusan.
     */
    public function kuota(): HasMany
    {
        return $this->hasMany(Kuota::class, 'bulan_magang_id');
    }

    /**
     * Scope query hanya bulan magang aktif.
     */
    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('aktif', true);
    }
}
