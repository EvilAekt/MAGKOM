<?php

declare(strict_types=1);

namespace App\Actions\Peserta\Logbook;

use App\Enums\RealtimeEventTypeEnum;
use App\Enums\StatusLogbookEnum;
use App\Enums\TipeAuditEnum;
use App\Models\Logbook;
use App\Models\User;
use App\Services\AuditService;
use App\Services\Peserta\LogbookAttachmentService;
use App\Services\RealtimeEventService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Throwable;

class UpdateLogbookAction
{
    public function __construct(
        protected AuditService $auditService,
        protected LogbookAttachmentService $attachmentService,
        protected RealtimeEventService $realtimeEventService
    ) {}

    /**
     * Memperbarui entri logbook mingguan peserta secara transaksional dengan perlindungan berkas dan audit trail.
     *
     * @param  array<string, mixed>  $validatedData
     *
     * @throws Throwable
     */
    public function execute(
        User $user,
        Logbook $logbook,
        array $validatedData,
        ?UploadedFile $file = null
    ): Logbook {
        $newFilePath = null;
        $oldFilePath = $logbook->file_laporan;

        try {
            return DB::transaction(function () use ($user, $logbook, $validatedData, $file, &$newFilePath, $oldFilePath) {
                /** @var Logbook $lockedLogbook */
                $lockedLogbook = Logbook::whereKey($logbook->id)->lockForUpdate()->firstOrFail();

                // 1. Simpan berkas baru jika diunggah
                if ($file instanceof UploadedFile) {
                    $newFilePath = $this->attachmentService->storeAttachment($file, $lockedLogbook->penempatan_id);
                }

                $mingguKe = $validatedData['minggu_ke'] ?? $lockedLogbook->minggu_ke;
                $nilaiLama = $lockedLogbook->only(['minggu_ke', 'kegiatan', 'file_laporan', 'link_laporan', 'status']);

                $updatePayload = [
                    'minggu_ke' => $mingguKe,
                    'kegiatan' => $validatedData['kegiatan'],
                    'link_laporan' => $validatedData['link_laporan'] ?? null,
                    'status' => StatusLogbookEnum::Terkirim, // Reset ke status terkirim setelah revisi
                ];

                if ($newFilePath !== null) {
                    $updatePayload['file_laporan'] = $newFilePath;
                }

                $lockedLogbook->update($updatePayload);

                // 2. Catat jejak audit
                $this->auditService->catat(
                    tipe: TipeAuditEnum::Aksi,
                    tabel: 'logbook',
                    idData: $lockedLogbook->id,
                    nilaiLama: $nilaiLama,
                    nilaiBaru: $lockedLogbook->only(['minggu_ke', 'kegiatan', 'file_laporan', 'link_laporan', 'status']),
                    detail: ['aksi' => 'update_logbook_mingguan']
                );

                // 3. Pasca-commit: Hapus berkas lama secara aman jika diganti dan kirim notifikasi realtime SSE
                $pembimbingId = $lockedLogbook->penempatan?->pembimbing_id;
                $pesertaNama = $user->nama ?? 'Peserta';
                $tglFormatted = $lockedLogbook->tanggal ? $lockedLogbook->tanggal->translatedFormat('d F Y') : '';

                DB::afterCommit(function () use ($newFilePath, $oldFilePath, $lockedLogbook, $pembimbingId, $pesertaNama, $tglFormatted) {
                    if ($newFilePath !== null && $oldFilePath && $oldFilePath !== $newFilePath) {
                        $this->attachmentService->deleteIfUnreferenced($oldFilePath, $lockedLogbook->id);
                    }

                    if ($pembimbingId) {
                        $this->realtimeEventService->publish(
                            RealtimeEventTypeEnum::LogbookBaru,
                            [
                                'title' => 'Revisi Logbook Masuk',
                                'message' => "{$pesertaNama} telah memperbarui logbook tanggal {$tglFormatted}.",
                                'action_url' => route('pembimbing.dashboard'),
                                'badge_target' => 'antrean-logbook-badge',
                            ],
                            $pembimbingId
                        );
                    }
                });

                return $lockedLogbook;
            });
        } catch (Throwable $e) {
            // Bersihkan file baru jika transaksi gagal
            if ($newFilePath) {
                $this->attachmentService->deleteAttachment($newFilePath);
            }
            throw $e;
        }
    }
}
