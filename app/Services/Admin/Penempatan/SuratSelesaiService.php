<?php

declare(strict_types=1);

namespace App\Services\Admin\Penempatan;

use App\Enums\RealtimeEventTypeEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\TipeAuditEnum;
use App\Models\Penempatan;
use App\Models\User;
use App\Services\AuditService;
use App\Services\RealtimeEventService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SuratSelesaiService
{
    public function __construct(
        protected AuditService $auditService,
        protected RealtimeEventService $realtimeService
    ) {}

    /**
     * Mengunggah Surat Keterangan Selesai Magang dan mendistribusikannya secara otomatis
     * ke seluruh anggota kelompok jika penempatan bertipe kelompok.
     *
     * @return array{jumlah_penerima: int, is_kelompok: bool, nama_kelompok: ?string, path: string}
     *
     * @throws InvalidArgumentException
     */
    public function unggahDanDistribusikan(
        Penempatan $penempatan,
        UploadedFile $file,
        User $admin
    ): array {
        if ($penempatan->status !== StatusPenempatanEnum::Selesai) {
            throw new InvalidArgumentException('Surat selesai magang hanya dapat diunggah jika status peserta telah dinyatakan selesai magang.');
        }

        return DB::transaction(function () use ($penempatan, $file, $admin) {
            // 1. Simpan berkas fisik baru ke storage lokal
            $path = $file->store('surat-selesai', 'local');

            $isKelompok = $penempatan->isKelompok();
            /** @var Collection<int, Penempatan> $targetPenempatans */
            $targetPenempatans = $isKelompok
                ? $penempatan->getRekanKelompokPenempatan()
                : new Collection([$penempatan]);

            $namaKelompok = $penempatan->pendaftaran?->kelompokMagang?->nama_kelompok;
            $now = now();
            $jumlahPenerima = 0;

            /** @var Penempatan $target */
            foreach ($targetPenempatans as $target) {
                // Hapus berkas lama target jika sebelumnya sudah punya berkas berbeda
                if ($target->file_surat_selesai &&
                    $target->file_surat_selesai !== $path &&
                    Storage::disk('local')->exists($target->file_surat_selesai)) {
                    // Hanya hapus fisik jika file tersebut tidak digunakan oleh penempatan lain
                    $masihDipakai = Penempatan::where('file_surat_selesai', $target->file_surat_selesai)
                        ->where('id', '!=', $target->id)
                        ->exists();

                    if (! $masihDipakai) {
                        Storage::disk('local')->delete($target->file_surat_selesai);
                    }
                }

                // Perbarui data penempatan peserta
                $target->update([
                    'file_surat_selesai' => $path,
                    'tgl_upload_surat_selesai' => $now,
                ]);

                $jumlahPenerima++;

                // Publikasikan notifikasi Realtime SSE jika peserta memiliki akun login
                if ($target->peserta_id) {
                    $this->realtimeService->publish(
                        RealtimeEventTypeEnum::StatusPendaftaran,
                        [
                            'title' => 'Surat Keterangan Selesai Magang Terbit',
                            'message' => 'Surat keterangan selesai magang resmi Anda telah diterbitkan oleh Diskominfo Sleman.',
                            'status' => 'surat_selesai_terbit',
                            'action_url' => route('peserta.dashboard'),
                        ],
                        $target->peserta_id
                    );
                }

                // Catat jejak audit trail untuk setiap peserta yang menerima dokumen
                $this->auditService->catat(
                    tipe: TipeAuditEnum::Aksi,
                    tabel: 'penempatan',
                    idData: $target->id,
                    nilaiLama: null,
                    nilaiBaru: [
                        'file_surat_selesai' => $path,
                        'tgl_upload_surat_selesai' => $now->toDateTimeString(),
                    ],
                    detail: [
                        'aksi' => 'unggah_surat_selesai',
                        'admin_id' => $admin->id,
                        'peserta_id' => $target->peserta_id,
                        'is_kelompok' => $isKelompok,
                        'nama_kelompok' => $namaKelompok,
                        'file_path' => $path,
                    ],
                    user: $admin->id
                );
            }

            return [
                'jumlah_penerima' => $jumlahPenerima,
                'is_kelompok' => $isKelompok,
                'nama_kelompok' => $namaKelompok,
                'path' => $path,
            ];
        }, 3);
    }

    /**
     * Mengalirkan berkas Surat Keterangan Selesai Magang untuk diunduh peserta atau admin.
     */
    public function downloadResponse(Penempatan $penempatan, ?User $user = null): BinaryFileResponse
    {
        if (! $penempatan->file_surat_selesai || ! Storage::disk('local')->exists($penempatan->file_surat_selesai)) {
            abort(404, 'Berkas surat selesai magang belum diunggah atau tidak ditemukan.');
        }

        $namaTarget = $user?->nama ?? $penempatan->peserta?->nama ?? 'Peserta';
        $namaFile = 'Surat_Keterangan_Selesai_Magang_'.Str::slug($namaTarget).'.pdf';

        return response()->download(
            Storage::disk('local')->path($penempatan->file_surat_selesai),
            $namaFile,
            ['Content-Type' => 'application/pdf']
        );
    }

    /**
     * Menghapus berkas Surat Keterangan Selesai Magang yang telah diunggah
     * dan mensinkronisasikan penghapusan ke seluruh anggota kelompok jika bertipe kelompok.
     *
     * @return array{jumlah_terdampak: int, is_kelompok: bool, nama_kelompok: ?string}
     */
    public function hapusSuratSelesai(Penempatan $penempatan, User $admin): array
    {
        return DB::transaction(function () use ($penempatan, $admin) {
            $isKelompok = $penempatan->isKelompok();
            /** @var Collection<int, Penempatan> $targetPenempatans */
            $targetPenempatans = $isKelompok
                ? $penempatan->getRekanKelompokPenempatan()
                : new Collection([$penempatan]);

            $targetIds = $targetPenempatans->pluck('id')->all();
            $namaKelompok = $penempatan->pendaftaran?->kelompokMagang?->nama_kelompok;
            $oldPaths = $targetPenempatans
                ->pluck('file_surat_selesai')
                ->filter()
                ->unique()
                ->values();

            $jumlahTerdampak = 0;

            /** @var Penempatan $target */
            foreach ($targetPenempatans as $target) {
                $oldPath = $target->file_surat_selesai;

                $target->update([
                    'file_surat_selesai' => null,
                    'tgl_upload_surat_selesai' => null,
                ]);

                $jumlahTerdampak++;

                $this->auditService->catat(
                    tipe: TipeAuditEnum::Aksi,
                    tabel: 'penempatan',
                    idData: $target->id,
                    nilaiLama: [
                        'file_surat_selesai' => $oldPath,
                    ],
                    nilaiBaru: [
                        'file_surat_selesai' => null,
                        'tgl_upload_surat_selesai' => null,
                    ],
                    detail: [
                        'aksi' => 'hapus_surat_selesai',
                        'admin_id' => $admin->id,
                        'peserta_id' => $target->peserta_id,
                        'is_kelompok' => $isKelompok,
                        'nama_kelompok' => $namaKelompok,
                        'deleted_path' => $oldPath,
                    ],
                    user: $admin->id
                );
            }

            foreach ($oldPaths as $oldPath) {
                $masihDipakai = Penempatan::where('file_surat_selesai', $oldPath)
                    ->whereNotIn('id', $targetIds)
                    ->exists();

                if (! $masihDipakai && Storage::disk('local')->exists($oldPath)) {
                    Storage::disk('local')->delete($oldPath);
                }
            }

            return [
                'jumlah_terdampak' => $jumlahTerdampak,
                'is_kelompok' => $isKelompok,
                'nama_kelompok' => $namaKelompok,
            ];
        }, 3);
    }
}
