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

class ReviewLogbookAction
{
    public function __construct(
        protected AuditService $auditService,
        protected RealtimeEventService $realtimeEventService
    ) {}

    /**
     * Memproses review catatan dan status logbook oleh pembimbing secara transaksional.
     */
    public function execute(
        User $user,
        Logbook $logbook,
        ?string $catatan,
        ?StatusLogbookEnum $status = null
    ): Logbook {
        return DB::transaction(function () use ($user, $logbook, $catatan, $status) {
            /** @var Logbook $lockedLogbook */
            $lockedLogbook = Logbook::whereKey($logbook->id)
                ->lockForUpdate()
                ->firstOrFail();

            $nilaiLama = $lockedLogbook->only(['status', 'catatan_pembimbing']);

            $updatePayload = [
                'catatan_pembimbing' => $catatan,
            ];

            if ($status !== null) {
                $updatePayload['status'] = $status;
            }

            $lockedLogbook->update($updatePayload);

            // 1. Jejak Audit
            $this->auditService->catat(
                tipe: TipeAuditEnum::Aksi,
                tabel: 'logbook',
                idData: $lockedLogbook->id,
                nilaiLama: $nilaiLama,
                nilaiBaru: $updatePayload,
                detail: ['aksi' => 'beri_catatan_logbook', 'pembimbing_id' => $user->id]
            );

            // 2. Notifikasi Real-time SSE ke Peserta pasca-commit
            $pesertaId = $lockedLogbook->penempatan?->peserta_id;
            if ($pesertaId) {
                $isRevisi = $lockedLogbook->status === StatusLogbookEnum::PerluRevisi;
                $isDisetujui = $lockedLogbook->status === StatusLogbookEnum::Disetujui;
                $tglFormatted = $lockedLogbook->tanggal ? $lockedLogbook->tanggal->translatedFormat('d F Y') : '';

                if ($isRevisi) {
                    $title = 'Logbook Memerlukan Revisi';
                    $message = filled($lockedLogbook->catatan_pembimbing)
                        ? "Pembimbing memberikan catatan revisi logbook: {$lockedLogbook->catatan_pembimbing}"
                        : "Catatan logbook tanggal {$tglFormatted} memerlukan revisi.";
                    $badgeTarget = 'logbook-revisi-badge';
                } elseif ($isDisetujui) {
                    $title = 'Logbook Harian Disetujui';
                    $message = filled($catatan)
                        ? "Pembimbing memberikan catatan: {$catatan}"
                        : "Catatan logbook tanggal {$tglFormatted} telah dinilai ({$lockedLogbook->status->label()}).";
                    $badgeTarget = null;
                } else {
                    $title = 'Catatan Logbook Diperbarui';
                    $message = filled($catatan)
                        ? "Pembimbing memberikan catatan: {$catatan}"
                        : "Catatan logbook tanggal {$tglFormatted} telah diperbarui oleh pembimbing.";
                    $badgeTarget = null;
                }

                $statusVal = $lockedLogbook->status instanceof StatusLogbookEnum
                    ? $lockedLogbook->status->value
                    : (string) ($lockedLogbook->status?->value ?? $lockedLogbook->status);

                DB::afterCommit(function () use ($pesertaId, $title, $message, $statusVal, $lockedLogbook, $badgeTarget) {
                    $this->realtimeEventService->publish(
                        RealtimeEventTypeEnum::LogbookDireviu,
                        [
                            'title' => $title,
                            'message' => $message,
                            'status' => $statusVal,
                            'catatan_pembimbing' => $lockedLogbook->catatan_pembimbing,
                            'logbook_id' => $lockedLogbook->id,
                            'action_url' => route('peserta.logbook.index'),
                            'badge_target' => $badgeTarget,
                        ],
                        (int) $pesertaId
                    );
                });
            }

            return $lockedLogbook;
        });
    }
}
