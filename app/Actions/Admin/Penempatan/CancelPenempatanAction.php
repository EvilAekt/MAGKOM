<?php

declare(strict_types=1);

namespace App\Actions\Admin\Penempatan;

use App\Enums\StatusPendaftaranEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\TipeAuditEnum;
use App\Models\Pendaftaran;
use App\Models\Penempatan;
use App\Models\User;
use App\Services\AuditService;
use App\Services\QuotaAllocationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CancelPenempatanAction
{
    public function __construct(
        protected AuditService $auditService,
        protected QuotaAllocationService $quotaAllocationService
    ) {}

    /**
     * Membatalkan penerimaan atau mencatat pengunduran diri peserta (AC-07-5, BR-07).
     *
     * @throws \RuntimeException
     */
    public function execute(Penempatan $penempatan, User $admin, string $alasan): Penempatan
    {
        return DB::transaction(function () use ($penempatan, $admin, $alasan) {
            $p = Penempatan::whereKey($penempatan->id)->lockForUpdate()->firstOrFail();

            if ($p->status !== StatusPenempatanEnum::Aktif) {
                throw new \RuntimeException('Hanya penempatan aktif yang dapat dibatalkan atau dicatat pengunduran dirinya.');
            }

            $hariIni = today('Asia/Jakarta');
            $tglMulai = Carbon::parse($p->tgl_mulai);
            $sebelumMulai = $hariIni->lt($tglMulai);

            if ($sebelumMulai) {
                // Sebelum tanggal mulai: kembalikan kuota via QuotaAllocationService & akun peserta tetap aktif
                if ($p->kuota_id) {
                    $this->quotaAllocationService->lepasKuotaUmum((int) $p->kuota_id);
                } elseif ($p->slot_khusus_id) {
                    $this->quotaAllocationService->lepasKuotaKhusus((int) $p->slot_khusus_id);
                }

                if ($p->pendaftaran_id) {
                    Pendaftaran::whereKey($p->pendaftaran_id)->update([
                        'status' => StatusPendaftaranEnum::Dibatalkan,
                        'catatan' => $alasan,
                    ]);
                }
            } else {
                // Sejak tanggal mulai: kuota hangus (tidak dikembalikan), akun peserta dinonaktifkan
                $p->peserta()->update(['status_aktif' => false]);
            }

            $statusLama = $p->status->value;
            $p->update(['status' => StatusPenempatanEnum::MengundurkanDiri]);

            $this->auditService->catat(
                tipe: TipeAuditEnum::Aksi,
                tabel: 'penempatan',
                idData: $p->id,
                nilaiLama: ['status' => $statusLama],
                nilaiBaru: ['status' => StatusPenempatanEnum::MengundurkanDiri->value],
                detail: [
                    'aksi' => $sebelumMulai ? 'Pembatalan sebelum tanggal mulai (kuota dikembalikan)' : 'Pengunduran diri setelah mulai (kuota hangus, akun nonaktif)',
                    'alasan' => $alasan,
                    'admin_id' => $admin->id,
                ],
                user: $admin->id
            );

            return $p;
        });
    }
}
