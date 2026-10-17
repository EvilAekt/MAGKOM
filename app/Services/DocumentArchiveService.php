<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Pendaftaran;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;

class DocumentArchiveService
{
    /**
     * Mengemas seluruh berkas pendaftaran calon peserta ke dalam sebuah file arsip ZIP terstruktur (PRD F-03, Bab 8.4, NFR-S6).
     *
     * @param Collection<int, Pendaftaran> $pendaftaranList
     * @return string Path absolut ke file ZIP sementara yang dihasilkan
     * @throws \RuntimeException Jika ZipArchive gagal diinisialisasi
     */
    public function createRegistrationBatchZip(Collection $pendaftaranList): string
    {
        $tempDir = storage_path('app/temp');
        File::ensureDirectoryExists($tempDir);

        $zipFileName = 'bundel-berkas-pendaftaran-' . date('Ymd-His') . '-' . Str::random(6) . '.zip';
        $zipFilePath = $tempDir . DIRECTORY_SEPARATOR . $zipFileName;

        $zip = new ZipArchive();
        $openResult = $zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        if ($openResult !== true) {
            throw new \RuntimeException("Gagal membuat arsip ZIP berkas pendaftaran (Kode error: {$openResult}).");
        }

        $privateDisk = Storage::disk('private');

        foreach ($pendaftaranList as $pendaftaran) {
            $peserta = $pendaftaran->user;
            $nim = $peserta?->profil?->nim_nisn ?? 'TANPA-NIM';
            $nama = Str::slug($peserta?->nama ?? 'peserta', '_');
            
            // Nama folder sanitasi untuk mencegah Zip Slip / Path Traversal
            $folderName = preg_replace('/[^A-Za-z0-9_\-]/', '_', "{$nim}_{$nama}");

            // Muat relasi dokumen berkas
            $dokumens = $pendaftaran->dokumen()->with('jenisDokumen')->get();

            if ($dokumens->isEmpty()) {
                // Buat placeholder catatan berkas kosong
                $zip->addFromString(
                    "{$folderName}/README.txt",
                    "Peserta: {$peserta?->nama}\nNIM: {$nim}\nStatus: Berkas belum lengkap atau belum diunggah."
                );
                continue;
            }

            foreach ($dokumens as $dok) {
                $jenisSlug = Str::slug($dok->jenisDokumen?->nama_jenis ?? 'berkas', '_');
                $originalName = $dok->nama_asli ?? $dok->nama_file_asli ?? 'file.pdf';
                $ext = pathinfo($originalName, PATHINFO_EXTENSION) ?: 'pdf';
                $cleanFileName = "{$folderName}/{$jenisSlug}.{$ext}";

                $storagePath = $dok->path;
                if ($storagePath && $privateDisk->exists($storagePath)) {
                    $fileContent = $privateDisk->get($storagePath);
                    if ($fileContent !== null) {
                        $zip->addFromString($cleanFileName, $fileContent);
                    }
                } else {
                    // Masukkan penanda berkas fisik belum sinkron
                    $zip->addFromString(
                        "{$folderName}/{$jenisSlug}_INFO.txt",
                        "Nama asli: {$originalName}\nUkuran: {$dok->ukuran} bytes\nStatus: Berkas fisik di storage privat sedang diarsipkan."
                    );
                }
            }
        }

        $zip->close();

        return $zipFilePath;
    }
}
