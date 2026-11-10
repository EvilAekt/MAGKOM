<?php

declare(strict_types=1);

namespace App\Http\Controllers\Peserta;

use App\Enums\TipeAuditEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\UnggahDokumenRequest;
use App\Models\Dokumen;
use App\Models\JenisDokumen;
use App\Models\Pendaftaran;
use App\Services\AuditService;
use App\Services\DocumentStorageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BerkasController extends Controller
{
    public function __construct(
        protected DocumentStorageService $documentService,
        protected AuditService $auditService
    ) {}

    /**
     * Mengunggah berkas persyaratan pendaftaran ke disk privat.
     */
    public function unggah(UnggahDokumenRequest $request, Pendaftaran $pendaftaran): RedirectResponse
    {
        $this->authorize('update', $pendaftaran);

        $jenisDokumen = JenisDokumen::findOrFail($request->integer('jenis_dokumen_id'));
        $file = $request->file('file');
        $kelompokAnggotaId = $request->filled('kelompok_anggota_id') ? $request->integer('kelompok_anggota_id') : null;

        if ($kelompokAnggotaId !== null) {
            $isAnggotaValid = $pendaftaran->kelompok_magang_id !== null
                && $pendaftaran->kelompokMagang?->anggota()->where('id', $kelompokAnggotaId)->exists();

            if (! $isAnggotaValid) {
                abort(403, 'Anggota kelompok tidak valid atau tidak terdaftar pada kelompok magang ini.');
            }
        }

        $dokumenQuery = Dokumen::where('pendaftaran_id', $pendaftaran->id)
            ->where('jenis_dokumen_id', $jenisDokumen->id);

        if ($kelompokAnggotaId !== null) {
            $dokumenQuery->where('kelompok_anggota_id', $kelompokAnggotaId);
        } else {
            $dokumenQuery->whereNull('kelompok_anggota_id');
        }

        $dokumenLama = $dokumenQuery->first();

        $nilaiLama = $dokumenLama ? [
            'nama_asli' => $dokumenLama->nama_asli,
            'hash' => $dokumenLama->hash,
            'ukuran' => $dokumenLama->ukuran,
            'mime' => $dokumenLama->mime,
        ] : null;

        $dokumen = $this->documentService->simpan($pendaftaran, $jenisDokumen, $file, $kelompokAnggotaId);

        $this->auditService->catat(
            tipe: TipeAuditEnum::Aksi,
            tabel: 'dokumen',
            idData: $dokumen->id,
            nilaiLama: $nilaiLama,
            nilaiBaru: [
                'pendaftaran_id' => $pendaftaran->id,
                'jenis_dokumen_id' => $jenisDokumen->id,
                'kelompok_anggota_id' => $kelompokAnggotaId,
                'nama_asli' => $dokumen->nama_asli,
                'ukuran' => $dokumen->ukuran,
                'mime' => $dokumen->mime,
                'hash' => $dokumen->hash,
            ],
            detail: ['aksi' => $dokumenLama ? 'Mengganti berkas persyaratan magang (replace)' : 'Mengunggah berkas persyaratan magang ke private disk']
        );

        return back()->with('success', "Berkas {$jenisDokumen->nama} berhasil diunggah.");
    }

    /**
     * Mengunduh berkas fisik privat dengan verifikasi otorisasi dan signed URL (AC-03-9).
     */
    public function unduh(Request $request, Dokumen $dokumen): StreamedResponse
    {
        $this->authorize('view', $dokumen);

        return $this->documentService->unduh($dokumen);
    }

    /**
     * Menyajikan pratinjau inline berkas fisik privat dengan verifikasi otorisasi dan signed URL (AC-03-9).
     */
    public function pratinjau(Request $request, Dokumen $dokumen): Response
    {
        $this->authorize('view', $dokumen);

        return $this->documentService->pratinjau($dokumen);
    }
}
