<?php

declare(strict_types=1);

namespace App\Actions\Pembimbing\Tugas;

use App\Enums\RealtimeEventTypeEnum;
use App\Enums\StatusReviewProgresEnum;
use App\Enums\TipeAuditEnum;
use App\Models\ProgresLog;
use App\Services\AuditService;
use App\Services\RealtimeEventService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class ReviewTugasProgresAction
{
    public function __construct(
        protected AuditService $auditService
    ) {}

    /**
     * Melakukan evaluasi dan review terhadap progres penugasan mentee (AC-08-4).
     *
     * @param  array{status_review: string, feedback?: ?string}  $validated
     */
    public function execute(ProgresLog $progres, array $validated): ProgresLog
    {
        $tugas = $progres->tugas;
        Gate::authorize('reviewProgres', $tugas);

        $nilaiLama = $progres->only(['status_review', 'feedback']);

        DB::transaction(function () use ($progres, $validated, $nilaiLama) {
            $progres->update([
                'status_review' => $validated['status_review'],
                'feedback' => $validated['feedback'] ?? null,
            ]);

            $this->auditService->catat(
                tipe: TipeAuditEnum::Aksi,
                tabel: 'progres_log',
                idData: $progres->id,
                nilaiLama: $nilaiLama,
                nilaiBaru: $validated,
                detail: ['aksi' => 'review_progres_tugas']
            );
        });

        // Notifikasi SSE Real-time ke Peserta strictly SETELAH commit (AC-08-4)
        $pesertaId = $tugas->penempatan?->peserta_id;
        if ($pesertaId) {
            DB::afterCommit(function () use ($progres, $tugas, $pesertaId) {
                $isRevisi = $progres->status_review === StatusReviewProgresEnum::PerluRevisi;
                $statusLabel = $isRevisi ? 'Perlu Revisi' : 'Diterima';
                $pesan = $isRevisi && filled($progres->feedback)
                    ? "Pembimbing meminta revisi untuk tugas \"{$tugas->judul}\": {$progres->feedback}"
                    : "Laporan progres untuk tugas \"{$tugas->judul}\" telah diperiksa oleh pembimbing ({$statusLabel}).";

                try {
                    app(RealtimeEventService::class)->publish(
                        RealtimeEventTypeEnum::ProgresDireviu,
                        [
                            'title' => "Evaluasi Progres Tugas: {$statusLabel}",
                            'message' => $pesan,
                            'status_review' => is_object($progres->status_review) ? $progres->status_review->value : $progres->status_review,
                            'feedback' => $progres->feedback,
                            'tugas_id' => $tugas->id,
                            'action_url' => route('peserta.tugas.show', $tugas->id),
                            'badge_target' => $isRevisi ? 'tugas-revisi-badge' : null,
                        ],
                        $pesertaId
                    );
                } catch (\Throwable $e) {
                    Log::warning("Gagal mengirim notifikasi SSE ProgresDireviu untuk progres {$progres->id}: {$e->getMessage()}");
                }
            });
        }

        return $progres;
    }
}
