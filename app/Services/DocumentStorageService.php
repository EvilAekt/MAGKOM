<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Dokumen;
use App\Models\JenisDokumen;
use App\Models\Pendaftaran;
use App\Support\Pengaturan;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentStorageService
{
    /**
     * MIME types yang diizinkan berdasarkan inspeksi magic bytes.
     */
    private const ALLOWED_MIMES = [
        'application/pdf',
        'image/jpeg',
        'image/png',
    ];

    /**
     * Menyimpan berkas ke private disk dan mencatat ke database.
     *
     * @throws ValidationException
     */
    public function simpan(Pendaftaran $pendaftaran, JenisDokumen $jenisDokumen, UploadedFile $file): Dokumen
    {
        // 1. Validasi MIME type berbasis magic bytes
        $realMime = $file->getMimeType();
        if (! in_array($realMime, self::ALLOWED_MIMES, true)) {
            throw ValidationException::withMessages([
                'file' => 'Format berkas tidak diizinkan. Hanya berkas PDF, JPG, dan PNG yang diterima.',
            ]);
        }

        // 2. Validasi batas ukuran berkas
        $maksMb = Pengaturan::int('berkas_maks_mb', 2);
        $maksBytes = $maksMb * 1024 * 1024;
        if ($file->getSize() > $maksBytes) {
            throw ValidationException::withMessages([
                'file' => "Ukuran berkas melebihi batas maksimal ({$maksMb} MB).",
            ]);
        }

        // 3. Hitung SHA-256 hash integritas berkas
        $hashSha256 = hash_file('sha256', $file->getRealPath());

        // 4. Cari apakah sudah ada dokumen untuk jenis ini pada pendaftaran terkait
        $dokumenLama = Dokumen::where('pendaftaran_id', $pendaftaran->id)
            ->where('jenis_dokumen_id', $jenisDokumen->id)
            ->first();

        // 5. Simpan file baru ke disk privat
        $folder = "documents/{$pendaftaran->id}";
        $path = $file->store($folder, 'private');

        // Hapus file fisik lama jika ada
        if ($dokumenLama && Storage::disk('private')->exists($dokumenLama->path)) {
            Storage::disk('private')->delete($dokumenLama->path);
        }

        // 6. Simpan atau perbarui data ke database
        return Dokumen::updateOrCreate(
            [
                'pendaftaran_id' => $pendaftaran->id,
                'jenis_dokumen_id' => $jenisDokumen->id,
            ],
            [
                'path' => $path,
                'nama_asli' => $file->getClientOriginalName(),
                'mime' => $realMime,
                'ukuran' => $file->getSize(),
                'hash' => $hashSha256,
            ]
        );
    }

    /**
     * Mengunduh berkas dari disk privat.
     */
    public function unduh(Dokumen $dokumen): StreamedResponse
    {
        if (! Storage::disk('private')->exists($dokumen->path)) {
            abort(404, 'Berkas fisik tidak ditemukan di penyimpanan server.');
        }

        return Storage::disk('private')->download($dokumen->path, $dokumen->nama_asli);
    }

    /**
     * Menyajikan berkas dari disk privat secara inline untuk pratinjau browser/modal.
     */
    public function pratinjau(Dokumen $dokumen): \Symfony\Component\HttpFoundation\Response
    {
        if (! Storage::disk('private')->exists($dokumen->path)) {
            abort(404, 'Berkas fisik tidak ditemukan di penyimpanan server.');
        }

        return Storage::disk('private')->response(
            $dokumen->path,
            $dokumen->nama_asli,
            [
                'Content-Type' => $dokumen->mime,
                'Content-Disposition' => 'inline; filename="' . $dokumen->nama_asli . '"',
                'X-Content-Type-Options' => 'nosniff',
            ],
            'inline'
        );
    }

    /**
     * Menghapus seluruh berkas fisik pendaftaran dari disk privat (Kepatuhan UU PDP Hak Lupa).
     */
    public function hapusBerkasPendaftaran(Pendaftaran $pendaftaran): void
    {
        $folder = "documents/{$pendaftaran->id}";
        if (Storage::disk('private')->exists($folder)) {
            Storage::disk('private')->deleteDirectory($folder);
        }

        foreach ($pendaftaran->dokumen as $dokumen) {
            if (Storage::disk('private')->exists($dokumen->path)) {
                Storage::disk('private')->delete($dokumen->path);
            }
            $dokumen->delete();
        }
    }
}
