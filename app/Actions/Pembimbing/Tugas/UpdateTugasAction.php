<?php

declare(strict_types=1);

namespace App\Actions\Pembimbing\Tugas;

use App\Enums\StatusTugasEnum;
use App\Enums\TipeAuditEnum;
use App\Models\Tugas;
use App\Services\AuditService;
use App\Services\Pembimbing\TugasAttachmentService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class UpdateTugasAction
{
    public function __construct(
        protected AuditService $auditService,
        protected TugasAttachmentService $attachmentService
    ) {}

    /**
     * Memperbarui informasi atau status penugasan secara transaksional dan aman (AC-08).
     *
     * @param  array<string, mixed>  $validated
     * @return array{
     *     success: bool,
     *     tugas: Tugas,
     *     message: string,
     *     status: int
     * }
     */
    public function execute(Tugas $tugas, array $validated, ?UploadedFile $fileLampiran): array
    {
        Gate::authorize('update', $tugas);

        if ($tugas->status === StatusTugasEnum::Selesai) {
            return [
                'success' => false,
                'tugas' => $tugas,
                'message' => 'Tugas yang telah berstatus selesai tidak dapat diedit kembali.',
                'status' => 422,
            ];
        }

        $nilaiLama = $tugas->only([
            'judul',
            'deskripsi',
            'link_referensi',
            'nama_file_lampiran',
            'kategori',
            'kompleksitas',
            'deadline',
            'status',
        ]);

        $payload = $validated;
        unset($payload['file_lampiran']);

        $oldFilePath = $tugas->file_lampiran;
        $newFilePath = null;

        if ($fileLampiran !== null) {
            $uploaded = $this->attachmentService->storeAttachment($fileLampiran, "tugas/{$tugas->id}");
            $newFilePath = $uploaded['path'];
            $payload['file_lampiran'] = $newFilePath;
            $payload['nama_file_lampiran'] = $uploaded['name'];
            $payload['ukuran_file_lampiran'] = $uploaded['size'];
        }

        try {
            DB::transaction(function () use ($tugas, $payload, $nilaiLama) {
                $tugas->update($payload);

                $this->auditService->catat(
                    tipe: TipeAuditEnum::Aksi,
                    tabel: 'tugas',
                    idData: $tugas->id,
                    nilaiLama: $nilaiLama,
                    nilaiBaru: $tugas->only([
                        'judul',
                        'deskripsi',
                        'link_referensi',
                        'nama_file_lampiran',
                        'kategori',
                        'kompleksitas',
                        'deadline',
                        'status',
                    ]),
                    detail: ['aksi' => 'update_tugas']
                );
            });
        } catch (\Throwable $e) {
            // Bersihkan file baru jika transaksi gagal
            if ($newFilePath !== null) {
                $this->attachmentService->cleanupOrphanFile($newFilePath);
            }
            throw $e;
        }

        // Hapus berkas lama secara aman HANYA setelah transaksi berhasil dan HANYA jika tidak dipakai tugas lain
        if ($newFilePath !== null && $oldFilePath !== null) {
            $this->attachmentService->safeDeleteOldAttachment($oldFilePath, $tugas->id);
        }

        return [
            'success' => true,
            'tugas' => $tugas->fresh(),
            'message' => 'Data tugas berhasil diperbarui.',
            'status' => 200,
        ];
    }
}
