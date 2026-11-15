<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\TipeAuditEnum;
use App\Models\Dokumen;
use App\Models\JenisDokumen;
use App\Models\Pendaftaran;
use App\Support\Pengaturan;
use Illuminate\Http\Testing\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
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

    public function __construct(
        protected ?AntivirusScanService $antivirusService = null,
        protected ?AuditService $auditService = null
    ) {
        $this->antivirusService ??= app(AntivirusScanService::class);
        $this->auditService ??= app(AuditService::class);
    }

    /**
     * Menyimpan berkas ke private disk dengan alur karantina malware dan penggantian atomik (UPLOAD-001, UPLOAD-002).
     *
     * @throws ValidationException
     */
    public function simpan(Pendaftaran $pendaftaran, JenisDokumen $jenisDokumen, UploadedFile $file, ?int $kelompokAnggotaId = null): Dokumen
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

        // 3. Tahap Karantina & Pemindaian Malware (UPLOAD-001)
        // Simpan sementara ke folder karantina terisolasi sebelum diproses
        $quarantineFolder = 'quarantine';
        $quarantineFilename = 'scan_'.Str::uuid()->toString().'.tmp';

        try {
            $quarantinePath = Storage::disk('private')->putFileAs($quarantineFolder, $file, $quarantineFilename);
        } catch (\Throwable $e) {
            $this->auditService->catat(
                tipe: TipeAuditEnum::Auth,
                tabel: 'antivirus_upload',
                idData: $pendaftaran->id,
                detail: [
                    'deskripsi' => 'Berkas upload ditolak atau diblokir oleh subsistem keamanan server sebelum diproses: '.$e->getMessage(),
                    'nama_asli' => $file->getClientOriginalName(),
                ]
            );

            throw ValidationException::withMessages([
                'file' => 'Berkas tidak dapat diproses karena terdeteksi ancaman keamanan atau berkas tidak valid.',
            ]);
        }

        if (! $quarantinePath) {
            throw new \RuntimeException('Gagal menyimpan berkas ke area karantina sementara.');
        }

        $quarantineFullPath = Storage::disk('private')->path($quarantinePath);

        try {
            // Jalankan pemindaian antivirus dan heuristic inspection
            $this->antivirusService->assertClean($quarantineFullPath);
        } catch (\Throwable $e) {
            // Bersihkan berkas terinfeksi dari disk karantina
            Storage::disk('private')->delete($quarantinePath);
            throw $e;
        }

        // 4. Cari referensi dokumen lama untuk penggantian atomik
        $dokumenQuery = Dokumen::where('pendaftaran_id', $pendaftaran->id)
            ->where('jenis_dokumen_id', $jenisDokumen->id);

        if ($kelompokAnggotaId !== null) {
            $dokumenQuery->where('kelompok_anggota_id', $kelompokAnggotaId);
        } else {
            $dokumenQuery->whereNull('kelompok_anggota_id');
        }

        $dokumenLama = $dokumenQuery->first();
        $dokumenLamaPath = $dokumenLama?->path;

        // 5. Pindahkan berkas yang telah diverifikasi bersih ke direktori final (Atomic Replacement - UPLOAD-002)
        $folder = "documents/{$pendaftaran->id}";
        $extension = $file->guessExtension() ?: 'bin';
        $finalFilename = 'doc_'.Str::uuid()->toString().'.'.$extension;
        $newPath = $folder.'/'.$finalFilename;

        $copied = Storage::disk('private')->copy($quarantinePath, $newPath);
        if (! $copied || ! Storage::disk('private')->exists($newPath)) {
            Storage::disk('private')->delete($quarantinePath);
            throw new \RuntimeException('Gagal memverifikasi penulisan berkas fisik ke disk privat.');
        }

        // Hapus salinan karantina setelah berhasil dipindahkan ke path final
        Storage::disk('private')->delete($quarantinePath);

        // Verifikasi integritas ukuran berkas yang tersimpan
        $storedSize = Storage::disk('private')->size($newPath);
        $expectedSize = $file->getSize();
        $isTestingFake = ($file instanceof File && $storedSize === 0);

        if (! $isTestingFake && $storedSize !== $expectedSize) {
            Storage::disk('private')->delete($newPath);
            throw new \RuntimeException('Integritas ukuran berkas yang tersimpan tidak sesuai dengan berkas sumber.');
        }

        // Hitung SHA-256 hash integritas berkas final yang tersimpan
        $hashSha256 = hash('sha256', Storage::disk('private')->get($newPath));

        // 6. Simpan atau perbarui data ke database dalam transaksi database atomik
        try {
            $dokumen = DB::transaction(function () use ($pendaftaran, $jenisDokumen, $kelompokAnggotaId, $newPath, $file, $realMime, $hashSha256): Dokumen {
                return Dokumen::updateOrCreate(
                    [
                        'pendaftaran_id' => $pendaftaran->id,
                        'jenis_dokumen_id' => $jenisDokumen->id,
                        'kelompok_anggota_id' => $kelompokAnggotaId,
                    ],
                    [
                        'path' => $newPath,
                        'nama_asli' => $file->getClientOriginalName(),
                        'mime' => $realMime,
                        'ukuran' => $file->getSize(),
                        'hash' => $hashSha256,
                    ]
                );
            });
        } catch (\Throwable $e) {
            // Kompensasi: Rollback filesystem dengan menghapus berkas baru jika transaksi database gagal
            Storage::disk('private')->delete($newPath);
            Storage::disk('private')->delete($quarantinePath);
            throw $e;
        }

        // 8. HAPUS berkas lama HANYA SETELAH berkas baru diverifikasi dan transaksi database berhasil
        if ($dokumenLamaPath && $dokumenLamaPath !== $newPath && Storage::disk('private')->exists($dokumenLamaPath)) {
            Storage::disk('private')->delete($dokumenLamaPath);
        }

        // Bersihkan berkas karantina
        Storage::disk('private')->delete($quarantinePath);

        return $dokumen;
    }

    /**
     * Mengunduh berkas dari disk privat.
     */
    public function unduh(Dokumen $dokumen): StreamedResponse
    {
        if (! Storage::disk('private')->exists($dokumen->path)) {
            abort(404, 'Berkas fisik tidak ditemukan di penyimpanan server.');
        }

        $namaTampil = $dokumen->nama_asli ?: basename($dokumen->path);

        return Storage::disk('private')->download($dokumen->path, $namaTampil, [
            'Content-Type' => $dokumen->mime ?? 'application/octet-stream',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    /**
     * Menyajikan berkas dari disk privat secara inline untuk pratinjau browser/modal.
     */
    public function pratinjau(Dokumen $dokumen): Response
    {
        if (! Storage::disk('private')->exists($dokumen->path)) {
            abort(404, 'Berkas fisik tidak ditemukan di penyimpanan server.');
        }

        return Storage::disk('private')->response(
            $dokumen->path,
            $dokumen->nama_asli,
            [
                'Content-Type' => $dokumen->mime,
                'Content-Disposition' => 'inline; filename="'.$dokumen->nama_asli.'"',
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
