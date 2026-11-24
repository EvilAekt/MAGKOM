<?php

declare(strict_types=1);

namespace App\Actions\Admin\Kuota;

use App\Enums\StatusPendaftaranEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\TipeAuditEnum;
use App\Models\BulanMagang;
use App\Models\Pendaftaran;
use App\Models\Penempatan;
use App\Services\AuditService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UpdateBulanMagangAction
{
    public function __construct(
        protected AuditService $auditService
    ) {}

    /**
     * Memperbarui jadwal bulan magang aktif secara transaksional dengan audit trail dan penegakan BR-12.
     *
     * @param  array<string, mixed>  $validated
     *
     * @throws ValidationException
     */
    public function execute(BulanMagang $bulanMagang, array $validated, ?string $statusAwal = null): BulanMagang
    {
        return DB::transaction(function () use ($bulanMagang, $validated, $statusAwal) {
            /** @var BulanMagang $locked */
            $locked = BulanMagang::whereKey($bulanMagang->id)->lockForUpdate()->firstOrFail();

            if ($statusAwal) {
                $targetAktif = ($statusAwal === 'dibuka');

                // Penegakan Aturan Bisnis BR-12 jika hendak menonaktifkan/men-draftkan bulan magang
                if (! $targetAktif && $locked->aktif) {
                    $hasActivePendaftaran = Pendaftaran::where('bulan_magang_id', $locked->id)
                        ->whereIn('status', [
                            StatusPendaftaranEnum::Diajukan,
                            StatusPendaftaranEnum::WaitingList,
                            StatusPendaftaranEnum::PerluPerbaikan,
                            StatusPendaftaranEnum::BerkasBelumLengkap,
                            StatusPendaftaranEnum::Diterima,
                        ])
                        ->exists();

                    if ($hasActivePendaftaran) {
                        throw ValidationException::withMessages([
                            'status_awal' => 'Penonaktifan ditolak: Periode bulan magang masih memiliki pendaftaran aktif.',
                        ]);
                    }

                    $hasActivePenempatan = Penempatan::where('status', StatusPenempatanEnum::Aktif)
                        ->where(function ($q) use ($locked) {
                            $q->whereHas('kuota', fn ($qk) => $qk->where('bulan_magang_id', $locked->id))
                                ->orWhereHas('pendaftaran', fn ($qp) => $qp->where('bulan_magang_id', $locked->id));
                        })
                        ->exists();

                    if ($hasActivePenempatan) {
                        throw ValidationException::withMessages([
                            'status_awal' => 'Penonaktifan ditolak: Periode bulan magang masih memiliki penempatan peserta aktif.',
                        ]);
                    }
                }

                $validated['aktif'] = $targetAktif;
            }

            $nilaiLama = $locked->toArray();
            $locked->update($validated);

            $this->auditService->catat(
                tipe: TipeAuditEnum::Aksi,
                tabel: 'bulan_magang',
                idData: $locked->id,
                nilaiLama: $nilaiLama,
                nilaiBaru: $locked->toArray(),
                detail: ['aksi' => 'Memperbarui jadwal bulan magang dari kontrol kuota']
            );

            return $locked;
        });
    }
}
