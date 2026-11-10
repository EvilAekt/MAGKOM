<?php

declare(strict_types=1);

namespace App\Actions\Peserta\Pendaftaran;

use App\Enums\JenjangEnum;
use App\Enums\RealtimeEventTypeEnum;
use App\Enums\StatusPendaftaranEnum;
use App\Enums\TipeAuditEnum;
use App\Models\JenisDokumen;
use App\Models\Pendaftaran;
use App\Models\User;
use App\Services\AuditService;
use App\Services\RealtimeEventService;
use DomainException;
use Illuminate\Support\Facades\DB;
use Throwable;

class AjukanPendaftaranAction
{
    public function __construct(
        protected AuditService $auditService,
        protected RealtimeEventService $realtimeEventService
    ) {}

    /**
     * Mengajukan berkas pendaftaran peserta setelah seluruh dokumen wajib terunggah.
     *
     * @throws DomainException|Throwable
     */
    public function execute(User $user, Pendaftaran $pendaftaran): Pendaftaran
    {
        return DB::transaction(function () use ($user, $pendaftaran) {
            // 1. Kunci baris pendaftaran untuk mencegah pengajuan konkruen berulang
            /** @var Pendaftaran $lockedPendaftaran */
            $lockedPendaftaran = Pendaftaran::whereKey($pendaftaran->id)->lockForUpdate()->firstOrFail();

            // 2. Pastikan status memungkinkan pengajuan (BerkasBelumLengkap atau PerluPerbaikan)
            if (! in_array($lockedPendaftaran->status, [StatusPendaftaranEnum::BerkasBelumLengkap, StatusPendaftaranEnum::PerluPerbaikan], true)) {
                throw new DomainException("Pendaftaran tidak dapat diajukan karena status saat ini adalah: {$lockedPendaftaran->status->label()}.");
            }

            // 3. Validasi kelengkapan seluruh dokumen wajib (mandiri vs kelompok)
            $profil = $user->profil;
            $profil?->loadMissing(['jurusan.bidang']);
            $jenjang = $profil?->jenjang ?? JenjangEnum::Smk;

            $dokumenWajibList = JenisDokumen::where('aktif', true)
                ->where('jenjang', $jenjang->value)
                ->where('wajib', true)
                ->get();

            $dokumenWajibIds = $dokumenWajibList->pluck('id');
            $isKelompok = ($lockedPendaftaran->tipe === 'kelompok' || $lockedPendaftaran->kelompok_magang_id !== null);

            if (! $isKelompok) {
                $terunggahIds = $lockedPendaftaran->dokumen()->whereNull('kelompok_anggota_id')->pluck('jenis_dokumen_id');
                $belumLengkap = $dokumenWajibIds->diff($terunggahIds);

                if ($belumLengkap->isNotEmpty()) {
                    throw new DomainException('Semua berkas persyaratan wajib harus diunggah sebelum pengajuan dapat dikirim.');
                }
            } else {
                // 3a. Dokumen kolektif dan dokumen ketua
                $terunggahKetuaIds = $lockedPendaftaran->dokumen()->whereNull('kelompok_anggota_id')->pluck('jenis_dokumen_id');
                if ($dokumenWajibIds->diff($terunggahKetuaIds)->isNotEmpty()) {
                    throw new DomainException('Berkas persyaratan umum dan berkas Ketua Tim harus dilengkapi terlebih dahulu.');
                }

                // 3b. Dokumen individu untuk seluruh anggota tim (CV & Transkrip / Kartu Pelajar)
                $lockedPendaftaran->loadMissing('kelompokMagang.anggota');
                $jenisIndividuWajibIds = $dokumenWajibList
                    ->whereIn('kode', ['cv', 'transkrip', 'kartu_pelajar'])
                    ->pluck('id');

                foreach ($lockedPendaftaran->kelompokMagang?->anggota ?? [] as $anggota) {
                    $terunggahAnggotaIds = $lockedPendaftaran->dokumen()
                        ->where('kelompok_anggota_id', $anggota->id)
                        ->pluck('jenis_dokumen_id');

                    if ($jenisIndividuWajibIds->diff($terunggahAnggotaIds)->isNotEmpty()) {
                        throw new DomainException("Berkas persyaratan untuk anggota kelompok '{$anggota->nama}' belum lengkap (CV & Transkrip Nilai wajib diunggah).");
                    }
                }
            }

            // 4. Update status pendaftaran menjadi Diajukan
            $nilaiLama = ['status' => $lockedPendaftaran->status->value];

            $lockedPendaftaran->update([
                'status' => StatusPendaftaranEnum::Diajukan,
                'diajukan_at' => now(),
            ]);

            // 5. Catat jejak audit
            $this->auditService->catat(
                tipe: TipeAuditEnum::Aksi,
                tabel: 'pendaftaran',
                idData: $lockedPendaftaran->id,
                nilaiLama: $nilaiLama,
                nilaiBaru: ['status' => StatusPendaftaranEnum::Diajukan->value],
                detail: ['aksi' => 'Peserta mengajukan berkas lengkap pendaftaran']
            );

            // 6. Notifikasi Real-time SSE ke Admin dijadwalkan pasca-commit (TASK-SSE-05)
            $userName = $user->nama ?? 'Peserta';
            $nomorPendaftaran = $lockedPendaftaran->nomor_pendaftaran;
            $pId = $lockedPendaftaran->id;

            DB::afterCommit(function () use ($userName, $nomorPendaftaran, $pId) {
                $this->realtimeEventService->publish(
                    RealtimeEventTypeEnum::PendaftaranBaru,
                    [
                        'title' => 'Pendaftaran Baru Masuk',
                        'message' => "Berkas dari {$userName} siap diverifikasi.",
                        'nomor_pendaftaran' => $nomorPendaftaran,
                        'action_url' => route('admin.pendaftaran.show', $pId),
                        'badge_target' => '#badge-antrean-menunggu',
                    ],
                    null,
                    'admin'
                );
            });

            return $lockedPendaftaran;
        });
    }
}
