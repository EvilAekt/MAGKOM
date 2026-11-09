<?php

declare(strict_types=1);

namespace App\Services\Pembimbing;

use App\Models\Tugas;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TugasAttachmentService
{
    /**
     * Simpan file lampiran tugas ke disk privat.
     *
     * @return array{path: string, name: string, size: int}
     */
    public function storeAttachment(UploadedFile $file, string $folder): array
    {
        $path = $file->store($folder, 'private');

        return [
            'path' => $path,
            'name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
        ];
    }

    /**
     * Hapus berkas lampiran lama hanya jika tidak ada tugas lain yang masih mereferensikannya (AC-08).
     * Mencegah terhapusnya file bersama dalam satu batch penugasan.
     */
    public function safeDeleteOldAttachment(?string $oldFilePath, ?int $excludeTugasId = null): bool
    {
        if (! $oldFilePath || ! Storage::disk('private')->exists($oldFilePath)) {
            return false;
        }

        $query = Tugas::where('file_lampiran', $oldFilePath);
        if ($excludeTugasId !== null) {
            $query->where('id', '!=', $excludeTugasId);
        }

        $isStillReferenced = $query->exists();

        if ($isStillReferenced) {
            Log::info("SafeDelete: Berkas {$oldFilePath} tidak dihapus karena masih digunakan tugas lain dalam batch.");

            return false;
        }

        try {
            return Storage::disk('private')->delete($oldFilePath);
        } catch (\Throwable $e) {
            Log::warning("SafeDelete: Gagal menghapus berkas {$oldFilePath}: {$e->getMessage()}");

            return false;
        }
    }

    /**
     * Bersihkan berkas baru yang baru diunggah jika transaksi database mengalami kegagalan (rollback).
     */
    public function cleanupOrphanFile(?string $filePath): void
    {
        if ($filePath && Storage::disk('private')->exists($filePath)) {
            try {
                Storage::disk('private')->delete($filePath);
                Log::info("RollbackCleanup: Berkas orphan {$filePath} berhasil dibersihkan dari penyimpanan privat.");
            } catch (\Throwable $e) {
                Log::error("RollbackCleanup: Gagal membersihkan berkas orphan {$filePath}: {$e->getMessage()}");
            }
        }
    }
}
