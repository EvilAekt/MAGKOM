<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\JenjangEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JenisDokumen extends Model
{
    use HasFactory;

    protected $table = 'jenis_dokumen';

    protected $fillable = [
        'kode',
        'nama',
        'jenjang',
        'wajib',
        'aktif',
    ];

    protected $casts = [
        'jenjang' => JenjangEnum::class,
        'wajib' => 'boolean',
        'aktif' => 'boolean',
    ];

    public function dokumen(): HasMany
    {
        return $this->hasMany(Dokumen::class, 'jenis_dokumen_id');
    }

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('aktif', true);
    }

    public function scopeUntukJenjang(Builder $query, JenjangEnum|string $jenjang): Builder
    {
        $val = $jenjang instanceof JenjangEnum ? $jenjang->value : $jenjang;

        return $query->where('jenjang', $val);
    }
}
