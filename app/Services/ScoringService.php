<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\StatusPenilaianEnum;
use App\Enums\TipeAuditEnum;
use App\Models\Penempatan;
use App\Models\Pengaturan;
use App\Models\Penilaian;
use App\Models\PenilaianDetail;
use App\Models\PenilaianRiwayat;
use App\Models\Rubrik;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ScoringService
{
    public function __construct(
        protected AuditService $auditService
    ) {}

    /**
     * Hitung predikat huruf berdasarkan batas skor dari pengaturan sistem.
     */
    public function hitungPredikat(float $nilaiAkhir): string
    {
        $batasDefault = ['A' => 85, 'B' => 70, 'C' => 55, 'D' => 40];
        $settingRaw = Pengaturan::get('predikat_batas');

        $batas = $batasDefault;
        if (is_string($settingRaw)) {
            $decoded = json_decode($settingRaw, true);
            if (is_array($decoded)) {
                $batas = $decoded;
            }
        } elseif (is_array($settingRaw)) {
            $batas = $settingRaw;
        }

        if ($nilaiAkhir >= (float) ($batas['A'] ?? 85)) {
            return 'A';
        }
        if ($nilaiAkhir >= (float) ($batas['B'] ?? 70)) {
            return 'B';
        }
        if ($nilaiAkhir >= (float) ($batas['C'] ?? 55)) {
            return 'C';
        }
        if ($nilaiAkhir >= (float) ($batas['D'] ?? 40)) {
            return 'D';
        }

        return 'E';
    }

    /**
     * Simpan penilaian sebagai DRAFT.
     *
     * @param  array<int, int>  $skorRubrik  [rubrik_id => skor]
     */
    public function simpanDraft(
        Penempatan $penempatan,
        User $pembimbing,
        array $skorRubrik,
        ?string $catatan = null
    ): Penilaian {
        $this->pastikanPembimbingBerhak($penempatan, $pembimbing);

        return DB::transaction(function () use ($penempatan, $pembimbing, $skorRubrik, $catatan) {
            $penilaian = Penilaian::firstOrNew(['penempatan_id' => $penempatan->id]);

            if ($penilaian->exists && $penilaian->status === StatusPenilaianEnum::Final) {
                throw new InvalidArgumentException('Penilaian berstatus final tidak dapat diubah sebagai draft. Gunakan alur revisi.');
            }

            $penilaian->pembimbing_id = $pembimbing->id;
            $penilaian->status = StatusPenilaianEnum::Draft;
            $penilaian->catatan = $catatan;

            // Hitung nilai parsial jika ada
            $kalkulasi = $this->kalkulasiNilaiTerbobot($skorRubrik);
            $penilaian->nilai_akhir = $kalkulasi['nilai_akhir'];
            $penilaian->predikat = $kalkulasi['predikat'];
            $penilaian->save();

            $this->simpanDetailSkor($penilaian, $kalkulasi['details']);

            $this->auditService->catat(
                tipe: TipeAuditEnum::Aksi,
                tabel: 'penilaian',
                idData: $penilaian->id,
                nilaiLama: null,
                nilaiBaru: ['nilai_akhir' => $penilaian->nilai_akhir, 'status' => 'draft'],
                detail: ['aksi' => 'simpan_draft_penilaian']
            );

            return $penilaian->load('details.rubrik');
        });
    }

    /**
     * Simpan penilaian sebagai FINAL (AC-10).
     * Seluruh rubrik aktif wajib dinilai dan total bobot 100%.
     *
     * @param  array<int, int>  $skorRubrik  [rubrik_id => skor]
     */
    public function simpanFinal(
        Penempatan $penempatan,
        User $pembimbing,
        array $skorRubrik,
        ?string $catatan = null
    ): Penilaian {
        $this->pastikanPembimbingBerhak($penempatan, $pembimbing);

        $rubrikAktif = Rubrik::where('aktif', true)->get();
        if ($rubrikAktif->isEmpty()) {
            throw new InvalidArgumentException('Tidak ada instrumen rubrik aktif yang dapat dinilai.');
        }

        // Validasi kelengkapan seluruh rubrik aktif
        foreach ($rubrikAktif as $rubrik) {
            if (! isset($skorRubrik[$rubrik->id])) {
                throw new InvalidArgumentException("Skor untuk kriteria '{$rubrik->kriteria}' wajib diisi sebelum finalisasi.");
            }
        }

        return DB::transaction(function () use ($penempatan, $pembimbing, $skorRubrik, $catatan) {
            $penilaian = Penilaian::firstOrNew(['penempatan_id' => $penempatan->id]);

            if ($penilaian->exists && $penilaian->status === StatusPenilaianEnum::Final) {
                throw new InvalidArgumentException('Penilaian sudah berstatus final. Gunakan alur revisi nilai final dengan alasan.');
            }

            $kalkulasi = $this->kalkulasiNilaiTerbobot($skorRubrik);

            $penilaian->pembimbing_id = $pembimbing->id;
            $penilaian->nilai_akhir = $kalkulasi['nilai_akhir'];
            $penilaian->predikat = $kalkulasi['predikat'];
            $penilaian->catatan = $catatan;
            $penilaian->status = StatusPenilaianEnum::Final;
            $penilaian->save();

            $this->simpanDetailSkor($penilaian, $kalkulasi['details']);

            $this->auditService->catat(
                tipe: TipeAuditEnum::Aksi,
                tabel: 'penilaian',
                idData: $penilaian->id,
                nilaiLama: null,
                nilaiBaru: [
                    'nilai_akhir' => $penilaian->nilai_akhir,
                    'predikat' => $penilaian->predikat,
                    'status' => 'final',
                ],
                detail: ['aksi' => 'finalisasi_penilaian']
            );

            return $penilaian->load('details.rubrik');
        });
    }

    /**
     * Revisi penilaian final dengan rekam jejak audit trail riwayat (AC-10-6).
     *
     * @param  array<int, int>  $skorRubrik  [rubrik_id => skor]
     */
    public function revisiFinal(
        Penilaian $penilaian,
        User $pembimbing,
        array $skorRubrik,
        ?string $catatan,
        string $alasan
    ): Penilaian {
        $this->pastikanPembimbingBerhak($penilaian->penempatan, $pembimbing);

        if ($penilaian->status !== StatusPenilaianEnum::Final) {
            throw new InvalidArgumentException('Hanya penilaian berstatus final yang dapat direvisi.');
        }

        $alasanTrimmed = trim($alasan);
        if (mb_strlen($alasanTrimmed) < 10) {
            throw new InvalidArgumentException('Alasan perubahan nilai final wajib diisi minimal 10 karakter.');
        }

        $rubrikAktif = Rubrik::where('aktif', true)->get();
        foreach ($rubrikAktif as $rubrik) {
            if (! isset($skorRubrik[$rubrik->id])) {
                throw new InvalidArgumentException("Skor untuk kriteria '{$rubrik->kriteria}' wajib diisi.");
            }
        }

        return DB::transaction(function () use ($penilaian, $pembimbing, $skorRubrik, $catatan, $alasanTrimmed) {
            $penilaian->loadMissing('details.rubrik');

            // Snapshot nilai lama
            $snapshotLama = [
                'nilai_akhir' => $penilaian->nilai_akhir,
                'predikat' => $penilaian->predikat,
                'catatan' => $penilaian->catatan,
                'details' => $penilaian->details->map(fn ($d) => [
                    'rubrik_id' => $d->rubrik_id,
                    'kriteria' => $d->rubrik->kriteria ?? '',
                    'skor' => $d->skor,
                    'bobot_snapshot' => $d->bobot_snapshot,
                ])->toArray(),
            ];

            $kalkulasi = $this->kalkulasiNilaiTerbobot($skorRubrik);

            $penilaian->nilai_akhir = $kalkulasi['nilai_akhir'];
            $penilaian->predikat = $kalkulasi['predikat'];
            $penilaian->catatan = $catatan;
            $penilaian->save();

            $this->simpanDetailSkor($penilaian, $kalkulasi['details']);

            // Snapshot nilai baru
            $snapshotBaru = [
                'nilai_akhir' => $penilaian->nilai_akhir,
                'predikat' => $penilaian->predikat,
                'catatan' => $penilaian->catatan,
                'details' => $kalkulasi['details'],
            ];

            // Simpan riwayat perubahan
            PenilaianRiwayat::create([
                'penilaian_id' => $penilaian->id,
                'diubah_oleh' => $pembimbing->id,
                'nilai_lama' => $snapshotLama,
                'nilai_baru' => $snapshotBaru,
                'alasan' => $alasanTrimmed,
                'waktu' => now(),
            ]);

            $this->auditService->catat(
                tipe: TipeAuditEnum::Aksi,
                tabel: 'penilaian',
                idData: $penilaian->id,
                nilaiLama: $snapshotLama,
                nilaiBaru: [
                    'nilai_baru' => $snapshotBaru,
                    'alasan' => $alasanTrimmed,
                ],
                detail: ['aksi' => 'revisi_penilaian_final']
            );

            return $penilaian->load('details.rubrik', 'riwayats');
        });
    }

    /**
     * Hitung total skor terbobot dan detail snapshot.
     *
     * @param  array<int, int>  $skorRubrik
     * @return array{nilai_akhir: float, predikat: string, details: array<int, array{rubrik_id: int, skor: int, bobot_snapshot: int}>}
     */
    protected function kalkulasiNilaiTerbobot(array $skorRubrik): array
    {
        $rubriks = Rubrik::whereIn('id', array_keys($skorRubrik))->get()->keyBy('id');
        $totalTerbobot = 0.0;
        $details = [];

        foreach ($skorRubrik as $rubrikId => $skorRaw) {
            $skor = (int) $skorRaw;
            if ($skor < 0 || $skor > 100) {
                throw new InvalidArgumentException("Skor harus berada di rentang 0 sampai 100 (diberikan: {$skor}).");
            }

            $rubrik = $rubriks->get($rubrikId);
            $bobot = $rubrik ? (int) $rubrik->bobot : 0;

            $totalTerbobot += ($skor * $bobot) / 100.0;

            $details[] = [
                'rubrik_id' => (int) $rubrikId,
                'skor' => $skor,
                'bobot_snapshot' => $bobot,
            ];
        }

        $nilaiAkhir = round($totalTerbobot, 2);
        $predikat = $this->hitungPredikat($nilaiAkhir);

        return [
            'nilai_akhir' => $nilaiAkhir,
            'predikat' => $predikat,
            'details' => $details,
        ];
    }

    /**
     * Simpan / sinkronkan detail skor ke penilaian_detail.
     *
     * @param  array<int, array{rubrik_id: int, skor: int, bobot_snapshot: int}>  $details
     */
    protected function simpanDetailSkor(Penilaian $penilaian, array $details): void
    {
        foreach ($details as $detail) {
            PenilaianDetail::updateOrCreate(
                [
                    'penilaian_id' => $penilaian->id,
                    'rubrik_id' => $detail['rubrik_id'],
                ],
                [
                    'skor' => $detail['skor'],
                    'bobot_snapshot' => $detail['bobot_snapshot'],
                ]
            );
        }
    }

    /**
     * Penegakan batasan hak akses (AC-10-7: SuperAdmin STRICTLY READ-ONLY).
     */
    protected function pastikanPembimbingBerhak(Penempatan $penempatan, User $user): void
    {
        if ($user->isSuperAdmin()) {
            throw new AuthorizationException('SuperAdmin hanya memiliki izin read-only dan dilarang memanipulasi penilaian.');
        }

        if ($user->id !== $penempatan->pembimbing_id && ! $user->isAdmin()) {
            throw new AuthorizationException('Hanya pembimbing yang ditugaskan yang berhak memberi penilaian.');
        }
    }
}
