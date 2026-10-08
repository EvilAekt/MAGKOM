<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rubrik extends Model
{
    use HasFactory;

    protected $table = 'rubrik';

    protected $fillable = [
        'kriteria',
        'bobot',
        'aktif',
    ];

    protected $casts = [
        'bobot' => 'integer',
        'aktif' => 'boolean',
    ];

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('aktif', true);
    }
}
