<?php

declare(strict_types=1);

namespace App\Actions\Admin\Penempatan;

use App\Enums\JalurEnum;
use App\Enums\RealtimeEventTypeEnum;
use App\Enums\StatusPendaftaranEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\SumberPenempatanEnum;
use App\Enums\TipeAuditEnum;
use App\Exceptions\Domain\StatusSudahBerubahException;
use App\Models\Bidang;
use App\Models\Pendaftaran;
use App\Models\Penempatan;
use App\Models\User;
use App\Services\AuditService;
use App\Services\RealtimeEventService;
use App\Support\Pengaturan;
use App\Support\PeriodeMagang;
use Illuminate\Support\Facades\DB;

class AssignPesertaAction
{
    public function __construct(
        protected AuditService $auditService,
        protected RealtimeEventService $realtimeService
    ) {}

    /**
     * Menetapkan pembimbing lapangan dan bidang dinas untuk pendaftaran yang telah diterima (Tahap 2 Penempatan).
     *
     * @throws StatusSudahBerubahException
     */
    public function execute(
        Pendaftaran $pendaftaran,
        User $admin,
        User $pembimbing,
        ?Bidang $bidang = null
    ): Penempatan {
        return DB::transaction(function () use ($pendaftaran, $admin, $pembimbing, $bidang) {
            // 1. Kunci baris pendaftaran terlebih dahulu untuk memvalidasi status dan mengambil user_id terkini
            $p = Pendaftaran::whereKey($pendaftaran->id)->lockForUpdate()->firstOrFail();

            if ($p->status !== StatusPendaftaranEnum::Diterima) {
                throw new StatusSudahBerubahException('Penetapan penempatan hanya dapat dilakukan untuk pendaftaran yang telah berstatus Diterima.');
            }

            // 2. Kunci baris parent User peserta untuk serialisasi alur penempatan dan mencegah double assignment
            $peserta = User::query()
                ->whereKey($p->user_id)
                ->lockForUpdate()
                ->firstOrFail();

            // 3. Verifikasi ulang hubungan user_id pendaftaran dengan peserta yang terkunci
            if ((int) $p->user_id !== (int) $peserta->id) {
                throw new StatusSudahBerubahException('Inkonsistensi data: identitas peserta tidak sesuai dengan berkas pendaftaran.');
            }

            // 4. Periksa ulang status penempatan aktif setelah memperoleh lock peserta
            $sudahAdaPenempatan = $p->penempatan()->exists()
                || Penempatan::where('peserta_id', $peserta->id)
                    ->where('status', StatusPenempatanEnum::Aktif)
                    ->exists();

            if ($sudahAdaPenempatan) {
                throw new StatusSudahBerubahException('Peserta ini sudah memiliki data penempatan aktif.');
            }

            // 5. Cek batas beban pembimbing (BR-13)
            $batasBeban = Pengaturan::int('maks_peserta_aktif_per_pembimbing', 0);
            if ($batasBeban > 0) {
                $bebanAktif = Penempatan::where('pembimbing_id', $pembimbing->id)
                    ->where('status', StatusPenempatanEnum::Aktif)
                    ->count();

                if ($bebanAktif >= $batasBeban) {
                    throw new \RuntimeException("Pembimbing {$pembimbing->nama} telah mencapai batas beban maksimum ({$batasBeban} peserta aktif).");
                }
            }

            // Hitung rentang periode magang (BR-08)
            [$mulai, $selesai] = PeriodeMagang::hitung($p->bulanMagang->bulan_mulai, $p->durasi_bulan);

            // Tentukan bidang: input form, default dari relasi jurusan, atau dari bidang pembimbing
            $bidangId = $bidang?->id
                ?? $p->kuota?->jurusan?->bidang?->first()?->id
                ?? $pembimbing->bidang_id
                ?? $p->kuota?->jurusan?->bidang_id;

            $penempatan = Penempatan::create([
                'pendaftaran_id' => $p->id,
                'peserta_id' => $p->user_id,
                'kuota_id' => $p->kuota_id,
                'slot_khusus_id' => null,
                'bidang_id' => $bidangId,
                'pembimbing_id' => $pembimbing->id,
                'jalur' => JalurEnum::Umum,
                'sumber' => SumberPenempatanEnum::Online,
                'dibuat_oleh' => $admin->id,
                'tgl_mulai' => $mulai->toDateString(),
                'tgl_selesai' => $selesai->toDateString(),
                'file_surat_diterima' => $p->file_surat_diterima,
                'status' => StatusPenempatanEnum::Aktif,
            ]);

            // Jika pendaftaran rombongan kelompok, buatkan record penempatan untuk seluruh rekan anggota tim
            if ($p->kelompok_magang_id && $p->kelompokMagang) {
                foreach ($p->kelompokMagang->anggota as $anggota) {
                    if ($anggota->user_id && (int) $anggota->user_id !== (int) $p->user_id) {
                        $sudahAda = Penempatan::where('peserta_id', $anggota->user_id)
                            ->where('status', StatusPenempatanEnum::Aktif)
                            ->exists();

                        if (! $sudahAda) {
                            Penempatan::create([
                                'pendaftaran_id' => $p->id,
                                'peserta_id' => $anggota->user_id,
                                'kuota_id' => $p->kuota_id,
                                'slot_khusus_id' => null,
                                'bidang_id' => $bidangId,
                                'pembimbing_id' => $pembimbing->id,
                                'jalur' => JalurEnum::Umum,
                                'sumber' => SumberPenempatanEnum::Online,
                                'dibuat_oleh' => $admin->id,
                                'tgl_mulai' => $mulai->toDateString(),
                                'tgl_selesai' => $selesai->toDateString(),
                                'file_surat_diterima' => $p->file_surat_diterima,
                                'status' => StatusPenempatanEnum::Aktif,
                            ]);

                            $this->realtimeService->publish(
                                RealtimeEventTypeEnum::StatusPendaftaran,
                                [
                                    'title' => 'Penempatan Magang Ditetapkan',
                                    'message' => 'Pembimbing lapangan tim Anda telah ditugaskan. Selamat datang di MAGKOM Diskominfo Sleman.',
                                    'status' => 'penempatan_aktif',
                                    'action_url' => route('peserta.dashboard'),
                                ],
                                $anggota->user_id
                            );
                        }
                    }
                }
            }

            // Catat jejak audit
            $this->auditService->catat(
                tipe: TipeAuditEnum::Aksi,
                tabel: 'penempatan',
                idData: $penempatan->id,
                nilaiLama: null,
                nilaiBaru: ['status' => StatusPenempatanEnum::Aktif->value],
                detail: [
                    'pendaftaran_id' => $p->id,
                    'admin_id' => $admin->id,
                    'pembimbing_id' => $pembimbing->id,
                    'bidang_id' => $bidangId,
                    'aksi' => 'Menetapkan pembimbing lapangan dan bidang dinas penempatan peserta (Tahap 2)',
                ],
                user: $admin->id
            );

            // Notifikasi Real-time SSE ke peserta bahwa penempatan & surat diterima siap
            $this->realtimeService->publish(
                RealtimeEventTypeEnum::StatusPendaftaran,
                [
                    'title' => 'Penempatan Magang Ditetapkan',
                    'message' => 'Pembimbing lapangan Anda telah ditugaskan. Surat keterangan diterima sudah dapat diunduh.',
                    'status' => 'penempatan_aktif',
                    'action_url' => route('peserta.dashboard'),
                ],
                $p->user_id
            );

            return $penempatan;
        }, 3);
    }
}
