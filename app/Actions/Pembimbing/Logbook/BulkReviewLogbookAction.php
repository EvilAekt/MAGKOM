<?php

declare(strict_types=1);

namespace App\Actions\Pembimbing\Logbook;

use App\Enums\RealtimeEventTypeEnum;
use App\Enums\StatusLogbookEnum;
use App\Enums\TipeAuditEnum;
use App\Models\Logbook;
use App\Models\User;
use App\Services\AuditService;
use App\Services\RealtimeEventService;
use Illuminate\Support\Facades\DB;

class BulkReviewLogbookAction
{
    public function __construct(
        protected AuditService $auditService,
        protected RealtimeEventService $realtimeEventService
    ) {}

    /**
     * Memproses persetujuan massal logbook berstatus terkirim secara atomik dalam transaksi database.
     *
     * @param  array<int, int>  $logbookIds
     */
    public function execute(User $user, array $logbookIds, string $catatan): int
    {
        return DB::transaction(function () use ($user, $logbookIds, $catatan) {
            $uniqueIds = array_values(array_unique(array_filter(array_map('intval', $logbookIds))));
            sort($uniqueIds);

            $logbooks = Logbook::with('penempatan')
                ->whereIn('id', $uniqueIds)
                ->where('status', StatusLogbookEnum::Terkirim)
                ->orderBy('id', 'asc')
                ->lockForUpdate()
                ->get();

            if ($logbooks->isEmpty()) {
                return 0;
            }

            foreach ($logbooks as $lb) {
                $nilaiLama = $lb->only(['status', 'catatan_pembimbing']);

                $lb->update([
                    'status' => StatusLogbookEnum::Disetujui,
                    'catatan_pembimbing' => $catatan,
                ]);

                $this->auditService->catat(
                    tipe: TipeAuditEnum::Aksi,
                    tabel: 'logbook',
                    idData: $lb->id,
                    nilaiLama: $nilaiLama,
                    nilaiBaru: [
                        'status' => StatusLogbookEnum::Disetujui->value,
                        'catatan_pembimbing' => $catatan,
                    ],
                    detail: ['aksi' => 'bulk_review_logbook', 'pembimbing_id' => $user->id]
                );
            }

            // Notifikasi Real-time SSE per mentee unik dijadwalkan pasca commit
            $pesertaIds = $logbooks->pluck('penempatan.peserta_id')->filter()->unique()->values()->all();

            DB::afterCommit(function () use ($pesertaIds, $catatan) {
                foreach ($pesertaIds as $pId) {
                    $this->realtimeEventService->publish(
                        RealtimeEventTypeEnum::LogbookDireviu,
                        [
                            'title' => 'Logbook Harian Disetujui',
                            'message' => 'Catatan logbook Anda telah disetujui secara massal oleh pembimbing lapangan.',
                            'status' => StatusLogbookEnum::Disetujui->value,
                            'catatan_pembimbing' => $catatan,
                            'action_url' => route('peserta.logbook.index'),
                        ],
                        (int) $pId
                    );
                }
            });

            return $logbooks->count();
        });
    }
}
