<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bidang extends Model
{
    use HasFactory;

    protected $table = 'bidang';

    protected $fillable = [
        'nama_bidang',
        'deskripsi',
        'aktif',
    ];

    protected $casts = [
        'aktif' => 'boolean',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'bidang_id');
    }

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('aktif', true);
    }
}
