<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\StatusKetersediaanEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
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
     * Accessor nama_bulan dengan graceful fallback ke bulan_mulai jika bernilai null.
     */
    protected function namaBulan(): Attribute
    {
        return Attribute::make(
            get: function (?string $value): string {
                if (! empty($value)) {
                    return $value;
                }
                if ($this->bulan_mulai) {
                    return Carbon::parse($this->bulan_mulai)->locale('id')->translatedFormat('F Y');
                }

                return 'Periode #'.$this->id;
            }
        );
    }

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
