<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RealtimeEvent extends Model
{
    use HasFactory;

    protected $table = 'realtime_events';

    protected $fillable = [
        'user_id',
        'role',
        'event_type',
        'payload',
        'read_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'read_at' => 'datetime',
    ];

    /**
     * Relasi ke penerima spesifik (jika event ditujukan personal).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Scope untuk menyaring event yang relevan bagi user yang sedang login.
     * Mengambil event yang ditujukan khusus ke user_id ATAU broadcast ke perannya.
     */
    public function scopeForUser(Builder $query, User $user): Builder
    {
        $roleValue = is_object($user->role) && enum_exists(get_class($user->role))
            ? $user->role->value
            : (string) $user->role;

        return $query->where(function (Builder $q) use ($user, $roleValue) {
            $q->where('user_id', $user->id)
                ->orWhere(function (Builder $sub) use ($roleValue) {
                    $sub->whereNull('user_id')
                        ->where('role', $roleValue);
                });
        });
    }

    /**
     * Scope untuk event yang belum dibaca.
     */
    public function scopeUnread(Builder $query): Builder
    {
        return $query->whereNull('read_at');
    }
}
