<?php

declare(strict_types=1);

namespace App\Actions\Peserta\Pendaftaran;

use App\Enums\StatusPendaftaranEnum;
use App\Enums\TipeAuditEnum;
use App\Models\Pendaftaran;
use App\Models\User;
use App\Services\AuditService;
use DomainException;
use Illuminate\Support\Facades\DB;
use Throwable;

class BatalkanPendaftaranAction
{
    public function __construct(
        protected AuditService $auditService
    ) {}

    /**
     * Membatalkan pengajuan pendaftaran mandiri oleh peserta secara transaksional dengan pessimistic lock (AC-03-9).
     *
     * @throws DomainException|Throwable
     */
    public function execute(User $user, Pendaftaran $pendaftaran): Pendaftaran
    {
        return DB::transaction(function () use ($pendaftaran) {
            /** @var Pendaftaran $lockedPendaftaran */
            $lockedPendaftaran = Pendaftaran::whereKey($pendaftaran->id)->lockForUpdate()->firstOrFail();

            // Pastikan status pendaftaran masih mengizinkan pembatalan mandiri
            if (! in_array($lockedPendaftaran->status, [
                StatusPendaftaranEnum::BerkasBelumLengkap,
                StatusPendaftaranEnum::Diajukan,
                StatusPendaftaranEnum::PerluPerbaikan,
                StatusPendaftaranEnum::WaitingList,
            ], true)) {
                throw new DomainException("Pengajuan dengan status {$lockedPendaftaran->status->label()} tidak dapat dibatalkan secara mandiri.");
            }

            $nilaiLama = ['status' => $lockedPendaftaran->status->value];

            $lockedPendaftaran->update([
                'status' => StatusPendaftaranEnum::Dibatalkan,
            ]);

            $this->auditService->catat(
                tipe: TipeAuditEnum::Aksi,
                tabel: 'pendaftaran',
                idData: $lockedPendaftaran->id,
                nilaiLama: $nilaiLama,
                nilaiBaru: ['status' => StatusPendaftaranEnum::Dibatalkan->value],
                detail: ['aksi' => 'Peserta membatalkan pengajuan pendaftaran magang']
            );

            return $lockedPendaftaran;
        });
    }
}
