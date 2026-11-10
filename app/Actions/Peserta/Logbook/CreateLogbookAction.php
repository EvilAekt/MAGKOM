<?php

declare(strict_types=1);

namespace App\Actions\Peserta\Logbook;

use App\Enums\RealtimeEventTypeEnum;
use App\Enums\StatusLogbookEnum;
use App\Enums\TipeAuditEnum;
use App\Models\Logbook;
use App\Models\Penempatan;
use App\Models\User;
use App\Services\AuditService;
use App\Services\Peserta\LogbookAttachmentService;
use App\Services\RealtimeEventService;
use Carbon\Carbon;
use DomainException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Throwable;

class CreateLogbookAction
{
    public function __construct(
        protected AuditService $auditService,
        protected LogbookAttachmentService $attachmentService,
        protected RealtimeEventService $realtimeEventService
    ) {}

    /**
     * Menyimpan entri logbook mingguan baru secara transaksional dengan proteksi konkurensi (pessimistic lock).
     *
     * @param  array<string, mixed>  $validatedData
     *
     * @throws DomainException|Throwable
     */
    public function execute(
        User $user,
        Penempatan $penempatan,
        array $validatedData,
        ?UploadedFile $file = null
    ): Logbook {
        $storedFilePath = null;

        try {
            return DB::transaction(function () use ($user, $penempatan, $validatedData, $file, &$storedFilePath) {
                // 1. Pessimistic lock pada penempatan untuk mencegah race-condition pembuatan logbook
                /** @var Penempatan $lockedPenempatan */
                $lockedPenempatan = Penempatan::where('id', $penempatan->id)->lockForUpdate()->firstOrFail();

                // 2. Hitung minggu ke jika tidak dikirim dari form
                $mingguKe = $validatedData['minggu_ke'] ?? null;
                if (! $mingguKe && $lockedPenempatan->tgl_mulai) {
                    $tglInput = Carbon::parse($validatedData['tanggal']);
                    $diffDays = $lockedPenempatan->tgl_mulai->diffInDays($tglInput);
                    $mingguKe = (int) floor($diffDays / 7) + 1;
                }

                $tglAwalMinggu = null;
                $tglAkhirMinggu = null;
                if ($mingguKe && $lockedPenempatan->tgl_mulai) {
                    $tglAwalMinggu = $lockedPenempatan->tgl_mulai->copy()->startOfDay()->addDays(((int) $mingguKe - 1) * 7);
                    $tglAkhirMinggu = $tglAwalMinggu->copy()->addDays(6)->endOfDay();
                }

                // 3. Validasi keunikan tanggal dan minggu di dalam transaksi terkunci
                $cekQuery = Logbook::where('penempatan_id', $lockedPenempatan->id)
                    ->where(function ($q) use ($validatedData, $mingguKe, $tglAwalMinggu, $tglAkhirMinggu) {
                        $q->whereDate('tanggal', $validatedData['tanggal']);
                        if ($mingguKe) {
                            $q->orWhere('minggu_ke', $mingguKe);
                            if ($tglAwalMinggu && $tglAkhirMinggu) {
                                $q->orWhereBetween('tanggal', [$tglAwalMinggu->toDateString(), $tglAkhirMinggu->toDateString()]);
                            }
                        }
                    });

                if ($cekQuery->exists()) {
                    $pesan = $mingguKe
                        ? "Catatan logbook kegiatan untuk Minggu ke-{$mingguKe} atau tanggal tersebut sudah pernah dicatat sebelumnya."
                        : 'Catatan logbook kegiatan untuk tanggal tersebut sudah pernah dicatat sebelumnya.';

                    throw new DomainException($pesan);
                }

                // 4. Simpan berkas lampiran jika diunggah
                if ($file instanceof UploadedFile) {
                    $storedFilePath = $this->attachmentService->storeAttachment($file, $lockedPenempatan->id);
                }

                // 5. Buat rekaman logbook
                $logbook = Logbook::create([
                    'penempatan_id' => $lockedPenempatan->id,
                    'minggu_ke' => $mingguKe,
                    'tanggal' => $validatedData['tanggal'],
                    'kegiatan' => $validatedData['kegiatan'],
                    'file_laporan' => $storedFilePath,
                    'link_laporan' => $validatedData['link_laporan'] ?? null,
                    'status' => StatusLogbookEnum::Terkirim,
                ]);

                // 6. Catat jejak audit
                $this->auditService->catat(
                    tipe: TipeAuditEnum::Aksi,
                    tabel: 'logbook',
                    idData: $logbook->id,
                    nilaiLama: null,
                    nilaiBaru: [
                        'penempatan_id' => $lockedPenempatan->id,
                        'minggu_ke' => $logbook->minggu_ke,
                        'tanggal' => $logbook->tanggal->toDateString(),
                    ],
                    detail: ['aksi' => 'isi_logbook_mingguan']
                );

                // 7. Notifikasi Real-time SSE ke Pembimbing dijadwalkan after commit (TASK-SSE-06)
                $pembimbingId = $lockedPenempatan->pembimbing_id;
                if ($pembimbingId) {
                    $senderName = $user->nama ?? 'Peserta';
                    DB::afterCommit(function () use ($pembimbingId, $senderName) {
                        $this->realtimeEventService->publish(
                            RealtimeEventTypeEnum::LogbookBaru,
                            [
                                'title' => 'Setoran Logbook Baru',
                                'message' => "{$senderName} menyerahkan catatan logbook baru.",
                                'action_url' => route('pembimbing.dashboard'),
                            ],
                            $pembimbingId
                        );
                    });
                }

                return $logbook;
            });
        } catch (Throwable $e) {
            // Bersihkan file orphan jika transaksi database gagal
            if ($storedFilePath) {
                $this->attachmentService->deleteAttachment($storedFilePath);
            }
            throw $e;
        }
    }
}
