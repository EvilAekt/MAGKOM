<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\RealtimeEventTypeEnum;
use App\Models\RealtimeEvent;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class RealtimeEventService
{
    /**
     * Mempublikasikan event real-time ke tabel outbox SSE.
     *
     * @param  array<string, mixed>  $payload
     */
    public function publish(
        RealtimeEventTypeEnum $type,
        array $payload,
        ?int $userId = null,
        ?string $role = null
    ): RealtimeEvent {
        return RealtimeEvent::create([
            'user_id' => $userId,
            'role' => $role,
            'event_type' => $type->value,
            'payload' => $payload,
        ]);
    }

    /**
     * Mengambil daftar event baru yang relevan bagi pengguna tertentu sejak lastEventId.
     *
     * @return Collection<int, RealtimeEvent>
     */
    public function getPendingEvents(User $user, int $lastEventId = 0, int $limit = 20): Collection
    {
        return RealtimeEvent::forUser($user)
            ->where('id', '>', $lastEventId)
            ->orderBy('id', 'asc')
            ->limit($limit)
            ->get();
    }

    /**
     * Menandai event tertentu sebagai telah dibaca oleh pengguna.
     */
    public function markAsRead(int $eventId, User $user): bool
    {
        $event = RealtimeEvent::forUser($user)->find($eventId);

        if (! $event) {
            return false;
        }

        return (bool) $event->update([
            'read_at' => now(),
        ]);
    }

    /**
     * Menghapus event outbox yang telah melampaui batas retensi hari (default 7 hari).
     */
    public function pruneOldEvents(int $days = 7): int
    {
        return RealtimeEvent::where('created_at', '<', now()->subDays($days))->delete();
    }
}
