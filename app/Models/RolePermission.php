<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RolePermission extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'role_permissions';

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'role',
        'permission_id',
    ];

    /**
     * Relasi ke master permission.
     *
     * @return BelongsTo<Permission, RolePermission>
     */
    public function permission(): BelongsTo
    {
        return $this->belongsTo(Permission::class, 'permission_id');
    }
}
