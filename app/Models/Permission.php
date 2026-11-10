<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Permission extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'permissions';

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'nama_modul',
        'slug',
        'kategori',
        'deskripsi',
        'is_system',
        'urutan',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'is_system' => 'boolean',
        'urutan' => 'integer',
    ];

    /**
     * Relasi ke pemetaan role.
     *
     * @return HasMany<RolePermission>
     */
    public function rolePermissions(): HasMany
    {
        return $this->hasMany(RolePermission::class, 'permission_id');
    }

    /**
     * Mendapatkan daftar role string yang memiliki hak akses ini.
     *
     * @return array<int, string>
     */
    public function getAssignedRoles(): array
    {
        return $this->rolePermissions->pluck('role')->toArray();
    }
}
