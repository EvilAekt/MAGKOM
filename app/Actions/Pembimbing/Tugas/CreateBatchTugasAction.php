<?php

declare(strict_types=1);

namespace App\Actions\Pembimbing\Tugas;

use App\Enums\RealtimeEventTypeEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\StatusTugasEnum;
use App\Enums\TipeAuditEnum;
use App\Models\KelompokMagang;
use App\Models\Penempatan;
use App\Models\Tugas;
use App\Models\User;
use App\Services\AuditService;
use App\Services\Pembimbing\TugasAttachmentService;
use App\Services\RealtimeEventService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CreateBatchTugasAction
{
    public function __construct(
        protected AuditService $auditService,
        protected TugasAttachmentService $attachmentService
    ) {}

    /**
     * Membuat tugas baru secara atomik untuk satu atau beberapa penempatan mentee (AC-08).
     *
     * @param  Collection<int, Penempatan>|array<int, Penempatan>  $penempatans
     * @param  array<string, mixed>  $validated
     * @return array{
     *     tugasList: Collection<int, Tugas>,
     *     statusPublikasi: string,
     *     jadwalPublikasi: ?Carbon,
     *     batchId: string,
     *     count: int,
     *     message: string
     * }
     */
    public function execute(
        Collection|array $penempatans,
        array $validated,
        ?UploadedFile $fileLampiran,
        User $user
    ): array {
        // 1. Penegakan hak akses & isolasi SuperAdmin (AC-08-5) untuk setiap mentee
        foreach ($penempatans as $penempatan) {
            Gate::authorize('create', [Tugas::class, $penempatan]);
        }

        // 2. Tentukan status dan jadwal publikasi
        $aksiPublikasi = $validated['aksi_publikasi'] ?? 'sekarang';
        $statusPublikasi = match ($aksiPublikasi) {
            'terjadwal' => 'terjadwal',
            'draft' => 'draft',
            default => 'dipublikasikan',
        };

        $jadwalPublikasi = null;
        if ($statusPublikasi === 'terjadwal') {
            $tglPub = $validated['tgl_publikasi'] ?? null;
            $wktPub = $validated['waktu_publikasi'] ?? '08:00';
            if ($tglPub) {
                $jadwalPublikasi = Carbon::parse("{$tglPub} {$wktPub}", 'Asia/Jakarta');
            }
        }

        // 3. Penanganan berkas lampiran fisik (disimpan 1 kali untuk seluruh batch)
        $batchId = (string) Str::uuid();
        $filePath = null;
        $fileName = null;
        $fileSize = null;

        if ($fileLampiran !== null) {
            $uploaded = $this->attachmentService->storeAttachment($fileLampiran, "tugas/{$batchId}");
            $filePath = $uploaded['path'];
            $fileName = $uploaded['name'];
            $fileSize = $uploaded['size'];
        }

        $createdIds = [];

        // 4. Transaksi database atomik dengan penanganan rollback berkas orphan
        try {
            DB::transaction(function () use (
                $penempatans,
                $batchId,
                $validated,
                $filePath,
                $fileName,
                $fileSize,
                $statusPublikasi,
                $jadwalPublikasi,
                $aksiPublikasi,
                &$createdIds
            ) {
                foreach ($penempatans as $penempatan) {
                    $penempatan->loadMissing('bidang');
                    $kategoriVal = ! empty($validated['kategori'])
                        ? $validated['kategori']
                        : ($penempatan->bidang?->nama_bidang ?? 'Umum');

                    $tugas = Tugas::create([
                        'batch_id' => $batchId,
                        'penempatan_id' => $penempatan->id,
                        'judul' => $validated['judul'],
                        'deskripsi' => $validated['deskripsi'] ?? null,
                        'link_referensi' => $validated['link_referensi'] ?? null,
                        'file_lampiran' => $filePath,
                        'nama_file_lampiran' => $fileName,
                        'ukuran_file_lampiran' => $fileSize,
                        'kategori' => $kategoriVal,
                        'kompleksitas' => $validated['kompleksitas'],
                        'deadline' => $validated['deadline'],
                        'status' => StatusTugasEnum::Belum,
                        'status_publikasi' => $statusPublikasi,
                        'jadwal_publikasi' => $jadwalPublikasi,
                    ]);

                    $this->auditService->catat(
                        tipe: TipeAuditEnum::Aksi,
                        tabel: 'tugas',
                        idData: $tugas->id,
                        nilaiLama: null,
                        nilaiBaru: $tugas->only([
                            'batch_id',
                            'judul',
                            'kategori',
                            'kompleksitas',
                            'deadline',
                            'link_referensi',
                            'nama_file_lampiran',
                            'status_publikasi',
                            'jadwal_publikasi',
                        ]),
                        detail: ['aksi' => 'buat_tugas', 'aksi_publikasi' => $aksiPublikasi]
                    );

                    $createdIds[] = $tugas->id;
                }
            });
        } catch (\Throwable $e) {
            // Bersihkan file baru jika transaksi gagal
            $this->attachmentService->cleanupOrphanFile($filePath);
            throw $e;
        }

        // 5. Pemuatan relasi batch optimal tanpa N+1 query
        $createdTugasList = Tugas::with('penempatan.peserta')->whereIn('id', $createdIds)->get();

        // 6. Notifikasi real-time dipublikasikan strictly SETELAH transaksi berhasil commit (AC-08)
        if ($statusPublikasi === 'dipublikasikan') {
            DB::afterCommit(function () use ($createdTugasList) {
                foreach ($createdTugasList as $tugas) {
                    $pesertaId = $tugas->penempatan?->peserta_id;
                    if ($pesertaId) {
                        try {
                            app(RealtimeEventService::class)->publish(
                                RealtimeEventTypeEnum::TugasBaru,
                                [
                                    'title' => 'Tugas Magang Baru',
                                    'message' => "Pembimbing memberikan tugas: {$tugas->judul}.",
                                    'deadline' => $tugas->deadline?->format('d/m/Y H:i'),
                                    'action_url' => route('peserta.tugas.index'),
                                ],
                                $pesertaId
                            );
                        } catch (\Throwable $e) {
                            Log::warning("Gagal mengirim notifikasi SSE tugas {$tugas->id}: {$e->getMessage()}");
                        }
                    }
                }
            });
        }

        $count = $createdTugasList->count();
        $message = match ($statusPublikasi) {
            'terjadwal' => "Tugas berhasil dijadwalkan untuk {$count} mentee pada ".$jadwalPublikasi?->format('d/m/Y H:i').' WIB.',
            'draft' => "Draf tugas untuk {$count} mentee berhasil disimpan.",
            default => "Tugas baru berhasil diberikan kepada {$count} mentee.",
        };

        return [
            'tugasList' => $createdTugasList,
            'statusPublikasi' => $statusPublikasi,
            'jadwalPublikasi' => $jadwalPublikasi,
            'batchId' => $batchId,
            'count' => $count,
            'message' => $message,
        ];
    }

    /**
     * Membuat Tugas Tim (tipe = kelompok) secara atomik untuk satu atau beberapa KelompokMagang.
     * Tugas diberikan ke Nama Tim (bukan dibebankan ke ketua), dan seluruh anggota aktif tim otomatis
     * terdaftar di tugas_peserta agar dapat melihat dan mengerjakan tugas tim tersebut.
     *
     * @param  Collection<int, KelompokMagang>|array<int, KelompokMagang>  $kelompokList
     * @param  array<string, mixed>  $validated
     * @return array{
     *     tugasList: Collection<int, Tugas>,
     *     statusPublikasi: string,
     *     jadwalPublikasi: ?Carbon,
     *     batchId: string,
     *     count: int,
     *     message: string
     * }
     */
    public function executeForKelompok(
        Collection|array $kelompokList,
        array $validated,
        ?UploadedFile $fileLampiran,
        User $user
    ): array {
        if ($user->isSuperAdmin()) {
            abort(403, 'SuperAdmin hanya memiliki akses lihat saja.');
        }

        $aksiPublikasi = $validated['aksi_publikasi'] ?? 'sekarang';
        $statusPublikasi = match ($aksiPublikasi) {
            'terjadwal' => 'terjadwal',
            'draft' => 'draft',
            default => 'dipublikasikan',
        };

        $jadwalPublikasi = null;
        if ($statusPublikasi === 'terjadwal') {
            $tglPub = $validated['tgl_publikasi'] ?? null;
            $wktPub = $validated['waktu_publikasi'] ?? '08:00';
            if ($tglPub) {
                $jadwalPublikasi = Carbon::parse("{$tglPub} {$wktPub}", 'Asia/Jakarta');
            }
        }

        $batchId = (string) Str::uuid();
        $filePath = null;
        $fileName = null;
        $fileSize = null;

        if ($fileLampiran !== null) {
            $uploaded = $this->attachmentService->storeAttachment($fileLampiran, "tugas/{$batchId}");
            $filePath = $uploaded['path'];
            $fileName = $uploaded['name'];
            $fileSize = $uploaded['size'];
        }

        $createdIds = [];

        try {
            DB::transaction(function () use (
                $kelompokList,
                $batchId,
                $validated,
                $filePath,
                $fileName,
                $fileSize,
                $statusPublikasi,
                $jadwalPublikasi,
                $aksiPublikasi,
                $user,
                &$createdIds
            ) {
                foreach ($kelompokList as $kelompok) {
                    $kelompok->loadMissing(['anggota', 'ketua']);
                    $memberIds = collect([$kelompok->ketua_user_id])
                        ->merge($kelompok->anggota->pluck('user_id'))
                        ->filter()
                        ->unique()
                        ->values();

                    $activePenempatans = Penempatan::with('bidang')
                        ->whereIn('peserta_id', $memberIds)
                        ->where('status', StatusPenempatanEnum::Aktif)
                        ->when(! $user->isAdmin(), fn ($q) => $q->where('pembimbing_id', $user->id))
                        ->get();

                    foreach ($activePenempatans as $pen) {
                        Gate::authorize('create', [Tugas::class, $pen]);
                    }

                    $firstBidang = $activePenempatans->first()?->bidang?->nama_bidang;
                    $kategoriVal = ! empty($validated['kategori'])
                        ? $validated['kategori']
                        : ($firstBidang ?? 'Umum');

                    $tugas = Tugas::create([
                        'batch_id' => $batchId,
                        'penempatan_id' => null,
                        'tipe' => 'kelompok',
                        'kelompok_magang_id' => $kelompok->id,
                        'judul' => $validated['judul'],
                        'deskripsi' => $validated['deskripsi'] ?? null,
                        'link_referensi' => $validated['link_referensi'] ?? null,
                        'file_lampiran' => $filePath,
                        'nama_file_lampiran' => $fileName,
                        'ukuran_file_lampiran' => $fileSize,
                        'kategori' => $kategoriVal,
                        'kompleksitas' => $validated['kompleksitas'],
                        'deadline' => $validated['deadline'],
                        'status' => StatusTugasEnum::Belum,
                        'status_publikasi' => $statusPublikasi,
                        'jadwal_publikasi' => $jadwalPublikasi,
                    ]);

                    // Daftarkan seluruh anggota aktif tim (ketua + anggota) agar semua bisa melihat tugas tim
                    $penByUser = $activePenempatans->keyBy('peserta_id');
                    foreach ($memberIds as $memberId) {
                        $penMember = $penByUser->get($memberId);
                        if (! $penMember) {
                            continue;
                        }
                        $tugas->tugasPeserta()->create([
                            'penempatan_id' => $penMember->id,
                            'user_id' => $memberId,
                        ]);
                    }

                    $this->auditService->catat(
                        tipe: TipeAuditEnum::Aksi,
                        tabel: 'tugas',
                        idData: $tugas->id,
                        nilaiLama: null,
                        nilaiBaru: $tugas->only([
                            'batch_id',
                            'tipe',
                            'kelompok_magang_id',
                            'judul',
                            'kategori',
                            'kompleksitas',
                            'deadline',
                            'status_publikasi',
                            'jadwal_publikasi',
                        ]),
                        detail: [
                            'aksi' => 'buat_tugas_tim',
                            'aksi_publikasi' => $aksiPublikasi,
                            'kelompok_magang_id' => $kelompok->id,
                        ]
                    );

                    $createdIds[] = $tugas->id;
                }
            });
        } catch (\Throwable $e) {
            $this->attachmentService->cleanupOrphanFile($filePath);
            throw $e;
        }

        $createdTugasList = Tugas::with(['kelompokMagang', 'pesertaDitugaskan'])->whereIn('id', $createdIds)->get();

        if ($statusPublikasi === 'dipublikasikan') {
            DB::afterCommit(function () use ($createdTugasList) {
                foreach ($createdTugasList as $tugas) {
                    $namaTim = $tugas->kelompokMagang?->nama_kelompok ?? 'Tim';
                    foreach ($tugas->pesertaDitugaskan as $peserta) {
                        try {
                            app(RealtimeEventService::class)->publish(
                                RealtimeEventTypeEnum::TugasBaru,
                                [
                                    'title' => 'Tugas Tim Baru',
                                    'message' => "Pembimbing memberikan tugas tim ({$namaTim}): {$tugas->judul}.",
                                    'deadline' => $tugas->deadline?->format('d/m/Y H:i'),
                                    'action_url' => route('peserta.tugas.index'),
                                ],
                                $peserta->id
                            );
                        } catch (\Throwable $e) {
                            Log::warning("Gagal mengirim notifikasi SSE tugas tim {$tugas->id}: {$e->getMessage()}");
                        }
                    }
                }
            });
        }

        $count = $createdTugasList->count();
        $message = match ($statusPublikasi) {
            'terjadwal' => "Tugas tim berhasil dijadwalkan untuk {$count} tim pada ".$jadwalPublikasi?->format('d/m/Y H:i').' WIB.',
            'draft' => "Draf tugas tim untuk {$count} tim berhasil disimpan.",
            default => "Tugas tim baru berhasil diberikan kepada {$count} tim.",
        };

        return [
            'tugasList' => $createdTugasList,
            'statusPublikasi' => $statusPublikasi,
            'jadwalPublikasi' => $jadwalPublikasi,
            'batchId' => $batchId,
            'count' => $count,
            'message' => $message,
        ];
    }
}
