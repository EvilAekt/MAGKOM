<?php

declare(strict_types=1);

namespace App\Services\Peserta;

use App\Models\Logbook;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class LogbookAttachmentService
{
    protected string $disk = 'private';

    /**
     * Menyimpan berkas lampiran PDF logbook ke direktori privat.
     */
    public function storeAttachment(UploadedFile $file, int $penempatanId): string
    {
        return $file->store("logbook/{$penempatanId}", $this->disk);
    }

    /**
     * Menghapus berkas lampiran dari disk privat jika ada.
     */
    public function deleteAttachment(?string $filePath): bool
    {
        if (! $filePath) {
            return false;
        }

        if (Storage::disk($this->disk)->exists($filePath)) {
            return Storage::disk($this->disk)->delete($filePath);
        }

        return false;
    }

    /**
     * Menghapus berkas hanya jika tidak lagi direferensikan oleh rekaman logbook lain.
     */
    public function deleteIfUnreferenced(?string $filePath, int $excludeLogbookId = 0): bool
    {
        if (! $filePath) {
            return false;
        }

        $isReferenced = Logbook::where('file_laporan', $filePath)
            ->where('id', '!=', $excludeLogbookId)
            ->exists();

        if (! $isReferenced) {
            return $this->deleteAttachment($filePath);
        }

        return false;
    }

    /**
     * Mengalirkan berkas lampiran PDF resmi dari penyimpanan privat.
     */
    public function downloadResponse(Logbook $logbook): Response
    {
        $filePath = $logbook->file_laporan;

        if (! $filePath || ! Storage::disk($this->disk)->exists($filePath)) {
            abort(404, 'Berkas lampiran logbook tidak ditemukan.');
        }

        $tglStr = $logbook->tanggal ? $logbook->tanggal->toDateString() : 'kegiatan';

        return Storage::disk($this->disk)->response(
            $filePath,
            "lampiran-logbook-{$tglStr}.pdf",
            ['Content-Type' => 'application/pdf']
        );
    }
}
