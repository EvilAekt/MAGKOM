<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jurusan extends Model
{
    use HasFactory;

    protected $table = 'jurusan';

    protected $fillable = [
        'nama_jurusan',
        'kategori',
        'aktif',
    ];

    protected $casts = [
        'aktif' => 'boolean',
    ];

    /**
     * Scope jurusan yang aktif.
     */
    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('aktif', true);
    }

    /**
     * Scope filter berdasarkan kategori ('smk' atau 'mahasiswa').
     */
    public function scopeKategori(Builder $query, string $kategori): Builder
    {
        return $query->where('kategori', $kategori);
    }
}
