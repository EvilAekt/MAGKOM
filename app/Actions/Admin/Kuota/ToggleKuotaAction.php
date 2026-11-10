<?php

declare(strict_types=1);

namespace App\Actions\Admin\Kuota;

use App\Enums\StatusPendaftaranEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\TipeAuditEnum;
use App\Models\Kuota;
use App\Models\Pendaftaran;
use App\Models\Penempatan;
use App\Services\AuditService;
use Illuminate\Support\Facades\DB;

class ToggleKuotaAction
{
    public function __construct(
        protected AuditService $auditService
    ) {}

    /**
     * Mengubah status aktif kuota jurusan dengan penegakan BR-12 & AC-05-6 secara transaksional.
     *
     * @return array{success: bool, message: string, kuota: Kuota}
     */
    public function execute(Kuota $kuota): array
    {
        return DB::transaction(function () use ($kuota) {
            /** @var Kuota $locked */
            $locked = Kuota::whereKey($kuota->id)->lockForUpdate()->firstOrFail();
            $targetAktif = ! $locked->aktif;

            // Penegakan Aturan Bisnis BR-12 & AC-05-6 jika hendak menonaktifkan kuota
            if (! $targetAktif) {
                $hasActivePendaftaran = Pendaftaran::where('kuota_id', $locked->id)
                    ->whereIn('status', [
                        StatusPendaftaranEnum::Diajukan,
                        StatusPendaftaranEnum::WaitingList,
                        StatusPendaftaranEnum::PerluPerbaikan,
                        StatusPendaftaranEnum::BerkasBelumLengkap,
                        StatusPendaftaranEnum::Diterima,
                    ])
                    ->exists();

                if ($hasActivePendaftaran) {
                    return [
                        'success' => false,
                        'message' => 'Penonaktifan ditolak (BR-12, AC-05-6): Kuota jurusan masih memiliki pendaftaran aktif.',
                        'kuota' => $locked,
                    ];
                }

                $hasActivePenempatan = Penempatan::where('kuota_id', $locked->id)
                    ->where('status', StatusPenempatanEnum::Aktif)
                    ->exists();

                if ($hasActivePenempatan) {
                    return [
                        'success' => false,
                        'message' => 'Penonaktifan ditolak (BR-12, AC-05-6): Kuota jurusan masih memiliki penempatan peserta aktif.',
                        'kuota' => $locked,
                    ];
                }
            }

            $nilaiLama = ['aktif' => $locked->aktif];
            $locked->aktif = $targetAktif;
            $locked->save();

            $this->auditService->catat(
                tipe: TipeAuditEnum::Aksi,
                tabel: 'kuota',
                idData: $locked->id,
                nilaiLama: $nilaiLama,
                nilaiBaru: ['aktif' => $locked->aktif],
                detail: ['aksi' => 'Mengubah status aktif kuota jurusan']
            );

            $statusText = $locked->aktif ? 'diaktifkan' : 'dinonaktifkan';

            return [
                'success' => true,
                'message' => "Kuota jurusan berhasil {$statusText}.",
                'kuota' => $locked,
            ];
        });
    }
}

