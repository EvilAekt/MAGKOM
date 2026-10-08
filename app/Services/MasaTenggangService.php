<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\StatusPenempatanEnum;
use App\Enums\StatusPenilaianEnum;
use App\Enums\TipeAuditEnum;
use App\Models\Penempatan;
use App\Models\Pengaturan;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class MasaTenggangService
{
    public function __construct(
        protected AuditService $auditService
    ) {}

    /**
     * Hitung tanggal nonaktif berdasarkan pengaturan masa tenggang bulan (BR-07).
     */
    public function hitungTanggalNonaktif(?CarbonInterface $tglSelesai = null): Carbon
    {
        $baseDate = $tglSelesai ? Carbon::instance($tglSelesai) : Carbon::today();
        $bulanTenggang = (int) (Pengaturan::get('masa_tenggang_bulan') ?? 1);
        if ($bulanTenggang < 1) {
            $bulanTenggang = 1;
        }

        return $baseDate->copy()->addMonths($bulanTenggang);
    }

    /**
     * Tandai penempatan sebagai selesai oleh pembimbing (BR-10).
     * Wajib memvalidasi bahwa penilaian telah berstatus 'final'.
     */
    public function tandaiSelesai(Penempatan $penempatan, User $pembimbing): Penempatan
    {
        if ($pembimbing->isSuperAdmin()) {
            throw new AuthorizationException('SuperAdmin hanya memiliki izin read-only dan dilarang mengubah status bimbingan.');
        }

        if ($pembimbing->id !== $penempatan->pembimbing_id && ! $pembimbing->isAdmin()) {
            throw new AuthorizationException('Hanya pembimbing yang ditugaskan yang dapat menyelesaikan masa magang.');
        }

        if ($penempatan->status !== StatusPenempatanEnum::Aktif) {
            throw new InvalidArgumentException("Penempatan dengan status '{$penempatan->status->label()}' tidak dapat diselesaikan.");
        }

        // BR-10: Wajib memiliki penilaian berstatus FINAL
        $penilaian = $penempatan->penilaian;
        if (! $penilaian || $penilaian->status !== StatusPenilaianEnum::Final) {
            throw new InvalidArgumentException('Penempatan tidak dapat diselesaikan karena penilaian akhir belum berstatus final.');
        }

        return DB::transaction(function () use ($penempatan) {
            $now = Carbon::today();
            $tglNonaktif = $this->hitungTanggalNonaktif($now);

            $penempatan->update([
                'status' => StatusPenempatanEnum::Selesai,
                'tgl_ditetapkan_selesai' => $now,
                'tgl_nonaktif' => $tglNonaktif,
            ]);

            $this->auditService->catat(
                tipe: TipeAuditEnum::Aksi,
                tabel: 'penempatan',
                idData: $penempatan->id,
                nilaiLama: ['status' => 'aktif'],
                nilaiBaru: [
                    'status' => 'selesai',
                    'tgl_ditetapkan_selesai' => $now->toDateString(),
                    'tgl_nonaktif' => $tglNonaktif->toDateString(),
                ],
                detail: ['aksi' => 'selesai_magang']
            );

            return $penempatan->fresh();
        });
    }

    /**
     * Menonaktifkan akun & penempatan peserta yang masa tenggangnya telah kedaluwarsa (BR-07).
     * Idempotent dan dijalankan oleh command harian.
     *
     * @return int Jumlah penempatan yang dinonaktifkan
     */
    public function nonaktifkanPesertaKedaluwarsa(): int
    {
        $today = Carbon::today();

        $penempatanKedaluwarsa = Penempatan::where('status', StatusPenempatanEnum::Selesai)
            ->whereNotNull('tgl_nonaktif')
            ->whereDate('tgl_nonaktif', '<=', $today)
            ->get();

        $totalDinonaktifkan = 0;

        foreach ($penempatanKedaluwarsa as $penempatan) {
            DB::transaction(function () use ($penempatan, &$totalDinonaktifkan) {
                $penempatan->update([
                    'status' => StatusPenempatanEnum::Nonaktif,
                ]);

                // Nonaktifkan user jika tidak memiliki penempatan aktif lainnya
                $peserta = $penempatan->peserta;
                if ($peserta) {
                    $adaPenempatanAktifLain = Penempatan::where('peserta_id', $peserta->id)
                        ->where('status', StatusPenempatanEnum::Aktif)
                        ->exists();

                    if (! $adaPenempatanAktifLain) {
                        $peserta->status_aktif = false;
                        $peserta->save();
                    }
                }

                $this->auditService->catat(
                    tipe: TipeAuditEnum::Aksi,
                    tabel: 'penempatan',
                    idData: $penempatan->id,
                    nilaiLama: ['status' => 'selesai'],
                    nilaiBaru: ['status' => 'nonaktif'],
                    detail: ['aksi' => 'nonaktifkan_peserta_kedaluwarsa']
                );

                $totalDinonaktifkan++;
            });
        }

        return $totalDinonaktifkan;
    }
}
