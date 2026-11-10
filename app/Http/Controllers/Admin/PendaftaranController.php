<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\Pendaftaran\PutuskanPendaftaranAction;
use App\Enums\StatusPendaftaranEnum;
use App\Enums\TipeAuditEnum;
use App\Exceptions\Domain\KuotaPenuhException;
use App\Exceptions\Domain\StatusSudahBerubahException;
use App\Http\Controllers\Controller;
use App\Http\Requests\KeputusanPendaftaranRequest;
use App\Models\Pendaftaran;
use App\Queries\Admin\Pendaftaran\PendaftaranIndexQuery;
use App\Services\AuditService;
use App\Services\DocumentArchiveService;
use App\Services\SuratDiterimaPdfService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class PendaftaranController extends Controller
{
    public function __construct(
        protected PendaftaranIndexQuery $indexQuery,
        protected PutuskanPendaftaranAction $putuskanAction,
        protected AuditService $auditService
    ) {}

    /**
     * Menampilkan daftar antrean verifikasi pendaftaran magang dengan filter dan statistik.
     */
    public function index(Request $request): View
    {
        return view('admin.pendaftaran.index', [
            'pendaftaranList' => $this->indexQuery->paginate($request),
            'bulanList' => $this->indexQuery->getBulanList(),
            'jurusanList' => $this->indexQuery->getJurusanList(),
            'statusEnum' => StatusPendaftaranEnum::cases(),
            'queueStats' => $this->indexQuery->getQueueStats(),
        ]);
    }

    /**
     * Menampilkan detail pengajuan pendaftaran, berkas, serta opsi keputusan admin.
     */
    public function show(Pendaftaran $pendaftaran): View
    {
        $showData = $this->indexQuery->getShowData($pendaftaran);

        return view('admin.pendaftaran.show', [
            'pendaftaran' => $pendaftaran,
            'pembimbingList' => $showData['pembimbingList'],
            'bidangList' => $showData['bidangList'],
            'sisaKuota' => $showData['sisaKuota'],
            'duplikasiNimCount' => $showData['duplikasiNimCount'],
        ]);
    }

    /**
     * Memproses keputusan verifikasi berkas pendaftaran (terima atau tolak).
     */
    public function putuskan(KeputusanPendaftaranRequest $request, Pendaftaran $pendaftaran): RedirectResponse
    {
        $admin = $request->user();
        $keputusan = $request->input('keputusan');

        try {
            $result = $this->putuskanAction->execute(
                $admin,
                $pendaftaran,
                $keputusan,
                $request->input('catatan'),
                $request->file('file_surat_diterima'),
                $request->filled('pembimbing_id') ? (int) $request->input('pembimbing_id') : null,
                $request->filled('bidang_id') ? (int) $request->input('bidang_id') : null
            );

            return redirect()->route('admin.pendaftaran.show', $pendaftaran)
                ->with('success', $result['message']);
        } catch (StatusSudahBerubahException $e) {
            // AC-04-5: Pesan ramah saat terjadi bentrok keputusan bersamaan
            return back()->with('error', 'Status pendaftaran sudah berubah, muat ulang halaman (AC-04-5).');
        } catch (KuotaPenuhException $e) {
            // AC-04-6: Pesan saat kuota ternyata penuh saat diterima
            return back()->with('error', 'Kuota umum telah terisi penuh. Pengajuan tidak dapat diterima.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memproses keputusan: '.$e->getMessage());
        }
    }

    /**
     * Mengunduh bundel seluruh berkas pendaftaran peserta dalam arsip ZIP terstruktur (PRD F-03, Bab 8.4, NFR-S6).
     */
    public function downloadBundelZip(Request $request, DocumentArchiveService $archiveService): BinaryFileResponse|RedirectResponse
    {
        $query = Pendaftaran::with(['user.profil', 'dokumen.jenisDokumen', 'bulanMagang', 'kuota.jurusan']);

        if ($request->filled('pendaftaran_ids')) {
            $ids = (array) $request->input('pendaftaran_ids');
            $query->whereIn('id', $ids);
        } elseif ($request->filled('bulan_magang_id')) {
            $query->where('bulan_magang_id', $request->integer('bulan_magang_id'));
        } elseif ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        } else {
            // Default: ambil pendaftaran berstatus diterima atau diajukan (maksimal 50 entri per bundel untuk keselamatan memori)
            $query->whereIn('status', [StatusPendaftaranEnum::Diterima, StatusPendaftaranEnum::Diajukan]);
        }

        $pendaftarans = $query->latest('id')->take(50)->get();

        if ($pendaftarans->isEmpty()) {
            return back()->with('error', 'Tidak ada data pendaftaran yang memenuhi kriteria untuk diunduh berkasnya.');
        }

        $this->auditService->catat(
            tipe: TipeAuditEnum::AksesData,
            tabel: 'dokumen',
            idData: null,
            nilaiLama: null,
            nilaiBaru: null,
            detail: [
                'aksi' => 'Unduh bundel arsip ZIP berkas pendaftaran peserta',
                'jumlah_pendaftar' => $pendaftarans->count(),
            ]
        );

        $zipPath = $archiveService->createRegistrationBatchZip($pendaftarans);
        $downloadName = 'bundel-berkas-magkom-'.date('Ymd-His').'.zip';

        return response()->download($zipPath, $downloadName, [
            'Content-Type' => 'application/zip',
            'X-Content-Type-Options' => 'nosniff',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Mengunduh surat keterangan diterima yang telah diunggah admin atau fallback ke PDF generator.
     */
    public function unduhSuratDiterima(Pendaftaran $pendaftaran, SuratDiterimaPdfService $pdfService): Response|BinaryFileResponse
    {
        $filePath = $pendaftaran->file_surat_diterima ?: $pendaftaran->penempatan?->file_surat_diterima;
        if ($filePath && Storage::disk('local')->exists($filePath)) {
            $pesertaNama = Str::slug($pendaftaran->user?->nama ?? 'peserta');

            return Storage::disk('local')->download($filePath, "surat-keterangan-diterima-{$pesertaNama}.pdf");
        }

        if ($pendaftaran->penempatan) {
            return $pdfService->generate($pendaftaran->penempatan);
        }

        abort(404, 'Berkas surat keterangan diterima belum tersedia.');
    }

    /**
     * Menyajikan pratinjau surat keterangan diterima yang telah diunggah admin atau fallback ke PDF stream.
     */
    public function pratinjauSuratDiterima(Pendaftaran $pendaftaran, SuratDiterimaPdfService $pdfService): Response|BinaryFileResponse
    {
        $filePath = $pendaftaran->file_surat_diterima ?: $pendaftaran->penempatan?->file_surat_diterima;
        if ($filePath && Storage::disk('local')->exists($filePath)) {
            return response()->file(Storage::disk('local')->path($filePath), [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="surat-keterangan-diterima.pdf"',
            ]);
        }

        if ($pendaftaran->penempatan) {
            return $pdfService->stream($pendaftaran->penempatan);
        }

        abort(404, 'Berkas surat keterangan diterima belum tersedia.');
    }
}
