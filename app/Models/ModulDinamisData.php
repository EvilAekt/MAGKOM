<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ModulDinamisData extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'modul_dinamis_data';

    protected $fillable = [
        'modul_id',
        'data',
        'files_metadata',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'data' => 'array',
        'files_metadata' => 'array',
    ];

    public function modul(): BelongsTo
    {
        return $this->belongsTo(ModulDinamis::class, 'modul_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
