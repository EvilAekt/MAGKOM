<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ModulDinamisField extends Model
{
    use HasFactory;

    protected $table = 'modul_dinamis_field';

    protected $fillable = [
        'modul_id',
        'nama_kolom',
        'label',
        'tipe_data',
        'opsi_pilihan',
        'relasi_tabel',
        'relasi_kolom_tampil',
        'wajib',
        'tampil_di_tabel',
        'dapat_dicari',
        'dapat_difilter',
        'placeholder',
        'bantuan_teks',
        'urutan',
    ];

    protected $casts = [
        'opsi_pilihan' => 'array',
        'wajib' => 'boolean',
        'tampil_di_tabel' => 'boolean',
        'dapat_dicari' => 'boolean',
        'dapat_difilter' => 'boolean',
        'urutan' => 'integer',
    ];

    public function modul(): BelongsTo
    {
        return $this->belongsTo(ModulDinamis::class, 'modul_id');
    }
}
