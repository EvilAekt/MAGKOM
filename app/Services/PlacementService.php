<?php

declare(strict_types=1);

namespace App\Services;

use App\Actions\Admin\Penempatan\AssignPesertaAction;
use App\Actions\Admin\Penempatan\BulkReassignPembimbingAction;
use App\Actions\Admin\Penempatan\CancelPenempatanAction;
use App\Actions\Admin\Penempatan\CreateManualPenempatanAction;
use App\Actions\Admin\Penempatan\ReassignPembimbingAction;
use App\Enums\RealtimeEventTypeEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusPendaftaranEnum;
use App\Enums\TipeAuditEnum;
use App\Exceptions\Domain\StatusSudahBerubahException;
use App\Models\Bidang;
use App\Models\Jurusan;
use App\Models\Kuota;
use App\Models\Pendaftaran;
use App\Models\Penempatan;
use App\Models\SlotKhusus;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class PlacementService extends AuditedService
{
    public function __construct(
        protected AuditService $auditService,
        protected QuotaAllocationService $quotaAllocationService,
        protected ?RealtimeEventService $realtimeService = null,
        protected ?AssignPesertaAction $assignPesertaAction = null,
        protected ?CreateManualPenempatanAction $createManualPenempatanAction = null,
        protected ?ReassignPembimbingAction $reassignPembimbingAction = null,
        protected ?BulkReassignPembimbingAction $bulkReassignPembimbingAction = null,
        protected ?CancelPenempatanAction $cancelPenempatanAction = null
    ) {
        parent::__construct($auditService);
        $this->realtimeService = $realtimeService ?? app(RealtimeEventService::class);
        $this->assignPesertaAction = $assignPesertaAction ?? app(AssignPesertaAction::class);
        $this->createManualPenempatanAction = $createManualPenempatanAction ?? app(CreateManualPenempatanAction::class);
        $this->reassignPembimbingAction = $reassignPembimbingAction ?? app(ReassignPembimbingAction::class);
        $this->bulkReassignPembimbingAction = $bulkReassignPembimbingAction ?? app(BulkReassignPembimbingAction::class);
        $this->cancelPenempatanAction = $cancelPenempatanAction ?? app(CancelPenempatanAction::class);
    }

    /**
     * Menerima dan meloloskan berkas pendaftaran calon peserta magang (Tahap 1 Verifikasi).
     *
     * @throws StatusSudahBerubahException
     */
    public function terimaBerkas(
        Pendaftaran $pendaftaran,
        User $admin,
        ?string $catatan = null,
        ?UploadedFile $fileSurat = null
    ): Pendaftaran {
        return $this->jalankan(__FUNCTION__, func_get_args(), function () use ($pendaftaran, $admin, $catatan, $fileSurat) {
            return DB::transaction(function () use ($pendaftaran, $admin, $catatan, $fileSurat) {
                // 1. Kunci baris pendaftaran untuk mencegah konkurensi verifikasi
                $p = Pendaftaran::whereKey($pendaftaran->id)->lockForUpdate()->firstOrFail();

                if (! in_array($p->status, [StatusPendaftaranEnum::Diajukan, StatusPendaftaranEnum::WaitingList, StatusPendaftaranEnum::PerluPerbaikan], true)) {
                    throw new StatusSudahBerubahException;
                }

                // 2. Kunci dan ambil jatah kuota umum secara atomik (AC-05-4)
                $this->quotaAllocationService->ambilKuotaUmum($p->kuota_id);

                // 3. Simpan berkas surat keterangan diterima yang diunggah jika ada
                $filePath = null;
                if ($fileSurat) {
                    $filePath = $fileSurat->store('surat_diterima', 'local');
                }

                // 4. Update status pendaftaran menjadi Diterima
                $statusLama = $p->status->value;
                $updateData = [
                    'status' => StatusPendaftaranEnum::Diterima,
                    'verifikator_id' => $admin->id,
                    'diputuskan_at' => now(),
                    'catatan' => $catatan,
                ];
                if ($filePath) {
                    $updateData['file_surat_diterima'] = $filePath;
                    $updateData['surat_diterima_uploaded_at'] = now();
                }
                $p->update($updateData);

                // 5. Catat jejak audit
                $this->auditService->catat(
                    tipe: TipeAuditEnum::Aksi,
                    tabel: 'pendaftaran',
                    idData: $p->id,
                    nilaiLama: ['status' => $statusLama],
                    nilaiBaru: ['status' => StatusPendaftaranEnum::Diterima->value],
                    detail: [
                        'admin_id' => $admin->id,
                        'aksi' => 'Menerima dan meloloskan berkas pendaftaran calon peserta (Tahap 1)',
                        'catatan' => $catatan,
                        'file_surat_diterima' => $filePath,
                    ]
                );

                // 6. Notifikasi Real-time SSE (TASK-SSE-05)
                $this->realtimeService->publish(
                    RealtimeEventTypeEnum::StatusPendaftaran,
                    [
                        'title' => 'Selamat! Berkas Pendaftaran Diterima',
                        'message' => 'Berkas pendaftaran magang Anda telah disetujui. Penugasan pembimbing sedang diproses oleh dinas.',
                        'status' => 'diterima',
                        'action_url' => route('peserta.dashboard'),
                    ],
                    $p->user_id
                );

                $this->realtimeService->publish(
                    RealtimeEventTypeEnum::KuotaBerubah,
                    [
                        'title' => 'Pembaruan Kuota Magang',
                        'message' => 'Kuota jurusan telah dialokasikan untuk peserta baru.',
                        'kuota_id' => $p->kuota_id,
                    ],
                    null,
                    'admin'
                );

                return $p;
            }, 3);
        });
    }

    /**
     * Menetapkan pembimbing lapangan dan bidang dinas untuk peserta yang telah diterima (Tahap 2 Penempatan).
     *
     * @throws StatusSudahBerubahException
     */
    public function tetapkanPenempatan(
        Pendaftaran $pendaftaran,
        User $admin,
        User $pembimbing,
        ?Bidang $bidang = null
    ): Penempatan {
        return $this->jalankan(__FUNCTION__, func_get_args(), function () use ($pendaftaran, $admin, $pembimbing, $bidang) {
            return $this->assignPesertaAction->execute($pendaftaran, $admin, $pembimbing, $bidang);
        });
    }

    /**
     * Menerima pendaftaran calon peserta magang secara langsung (backward-compatibility wrapper).
     *
     * @throws StatusSudahBerubahException
     */
    public function terima(
        Pendaftaran $pendaftaran,
        User $admin,
        ?User $pembimbing = null,
        ?Bidang $bidang = null
    ): Penempatan {
        return DB::transaction(function () use ($pendaftaran, $admin, $pembimbing, $bidang) {
            $p = $this->terimaBerkas($pendaftaran, $admin);

            $pembimbingTerpilih = $pembimbing ?? User::where('role', RoleEnum::Pembimbing)
                ->where('status_aktif', true)
                ->firstOrFail();

            return $this->tetapkanPenempatan($p, $admin, $pembimbingTerpilih, $bidang);
        });
    }

    /**
     * Menolak pengajuan pendaftaran magang dengan catatan alasan (F-04).
     */
    public function tolak(Pendaftaran $pendaftaran, User $admin, string $alasan): Pendaftaran
    {
        return $this->jalankan(__FUNCTION__, func_get_args(), function () use ($pendaftaran, $admin, $alasan) {
            return DB::transaction(function () use ($pendaftaran, $admin, $alasan) {
                $p = Pendaftaran::whereKey($pendaftaran->id)->lockForUpdate()->firstOrFail();

                if (! in_array($p->status, [StatusPendaftaranEnum::Diajukan, StatusPendaftaranEnum::WaitingList], true)) {
                    throw new StatusSudahBerubahException;
                }

                $nilaiLama = $p->toArray();

                $p->update([
                    'status' => StatusPendaftaranEnum::Ditolak,
                    'catatan' => $alasan,
                    'verifikator_id' => $admin->id,
                    'diputuskan_at' => now(),
                ]);

                $this->auditService->catat(
                    tipe: TipeAuditEnum::Aksi,
                    tabel: 'pendaftaran',
                    idData: $p->id,
                    nilaiLama: $nilaiLama,
                    nilaiBaru: $p->toArray(),
                    detail: ['alasan' => $alasan, 'aksi' => 'Menolak pengajuan pendaftaran']
                );

                // Notifikasi Real-time SSE ke peserta (TASK-SSE-05)
                $this->realtimeService->publish(
                    RealtimeEventTypeEnum::StatusPendaftaran,
                    [
                        'title' => 'Pendaftaran Tidak Diterima',
                        'message' => 'Mohon maaf, pengajuan pendaftaran Anda belum dapat diterima.',
                        'status' => 'ditolak',
                        'alasan' => $alasan,
                        'action_url' => route('peserta.dashboard'),
                    ],
                    $p->user_id
                );

                return $p;
            });
        });
    }

    /**
     * Meminta peserta memperbaiki berkas persyaratan (F-04).
     */
    public function mintaPerbaikan(Pendaftaran $pendaftaran, User $admin, string $catatan): Pendaftaran
    {
        return $this->jalankan(__FUNCTION__, func_get_args(), function () use ($pendaftaran, $admin, $catatan) {
            return DB::transaction(function () use ($pendaftaran, $admin, $catatan) {
                $p = Pendaftaran::whereKey($pendaftaran->id)->lockForUpdate()->firstOrFail();

                if (! in_array($p->status, [StatusPendaftaranEnum::Diajukan, StatusPendaftaranEnum::WaitingList], true)) {
                    throw new StatusSudahBerubahException;
                }

                $nilaiLama = $p->toArray();

                $p->update([
                    'status' => StatusPendaftaranEnum::PerluPerbaikan,
                    'catatan' => $catatan,
                    'verifikator_id' => $admin->id,
                ]);

                $this->auditService->catat(
                    tipe: TipeAuditEnum::Aksi,
                    tabel: 'pendaftaran',
                    idData: $p->id,
                    nilaiLama: $nilaiLama,
                    nilaiBaru: $p->toArray(),
                    detail: ['catatan' => $catatan, 'aksi' => 'Meminta revisi/perbaikan berkas pendaftaran']
                );

                // Notifikasi Real-time SSE ke peserta (TASK-SSE-05)
                $this->realtimeService->publish(
                    RealtimeEventTypeEnum::StatusPendaftaran,
                    [
                        'title' => 'Permintaan Perbaikan Berkas',
                        'message' => 'Verifikator meminta perbaikan berkas pendaftaran Anda. Silakan cek catatan revisi.',
                        'status' => 'perlu_perbaikan',
                        'catatan' => $catatan,
                        'action_url' => route('peserta.pendaftaran.detail', $p->id),
                    ],
                    $p->user_id
                );

                return $p;
            });
        });
    }

    /**
     * Penempatan manual jalur khusus (SuperAdmin / Admin) (F-06, F-07).
     */
    public function tempatkanManual(
        User $peserta,
        SlotKhusus $slotKhusus,
        User $pembimbing,
        ?Bidang $bidang,
        CarbonInterface|string $bulanMulai,
        int $durasiBulan,
        User $admin
    ): Penempatan {
        return $this->jalankan(__FUNCTION__, func_get_args(), function () use (
            $peserta, $slotKhusus, $pembimbing, $bidang, $bulanMulai, $durasiBulan, $admin
        ) {
            return $this->createManualPenempatanAction->executeTerdaftar(
                $peserta, $slotKhusus, $pembimbing, $bidang, $bulanMulai, $durasiBulan, $admin
            );
        });
    }

    /**
     * Penempatan manual dengan pendaftaran peserta baru langsung (AC-07-3, F-07).
     * Membuat akun user role peserta (email terverifikasi, must_change_password = true), profil, dan penempatan.
     * Mendukung jalur khusus (via slot khusus) atau jalur umum (via kuota jurusan).
     */
    public function tempatkanPesertaBaruManual(
        array $userData,
        array $profilData,
        User $pembimbing,
        ?Bidang $bidang,
        CarbonInterface|string $bulanMulai,
        int $durasiBulan,
        string $jalur,
        ?int $slotKhususId,
        ?int $kuotaId,
        User $admin
    ): Penempatan {
        return $this->jalankan(__FUNCTION__, func_get_args(), function () use (
            $userData, $profilData, $pembimbing, $bidang, $bulanMulai, $durasiBulan, $jalur, $slotKhususId, $kuotaId, $admin
        ) {
            return $this->createManualPenempatanAction->executeBaru(
                $userData, $profilData, $pembimbing, $bidang, $bulanMulai, $durasiBulan, $jalur, $slotKhususId, $kuotaId, $admin
            );
        });
    }

    /**
     * Memindahkan pembimbing peserta magang aktif (AC-07-4, BR-13, A-12).
     */
    public function pindahPembimbing(Penempatan $penempatan, User $pembimbingBaru, User $admin): Penempatan
    {
        return $this->jalankan(__FUNCTION__, func_get_args(), function () use ($penempatan, $pembimbingBaru, $admin) {
            return $this->reassignPembimbingAction->execute($penempatan, $pembimbingBaru, $admin);
        });
    }

    /**
     * Memindahkan pembimbing peserta magang secara massal (bulk reassign).
     *
     * @param  array<int, int|string>  $ids
     */
    public function alihkanBatch(array $ids, User $pembimbingBaru, User $admin): int
    {
        return $this->jalankan(__FUNCTION__, func_get_args(), function () use ($ids, $pembimbingBaru, $admin) {
            return $this->bulkReassignPembimbingAction->execute($ids, $pembimbingBaru, $admin);
        });
    }

    /**
     * Membatalkan penerimaan atau mencatat pengunduran diri peserta (AC-07-5, BR-07).
     */
    public function batalkanPenempatan(Penempatan $penempatan, User $admin, string $alasan): Penempatan
    {
        return $this->jalankan(__FUNCTION__, func_get_args(), function () use ($penempatan, $admin, $alasan) {
            return $this->cancelPenempatanAction->execute($penempatan, $admin, $alasan);
        });
    }
}
