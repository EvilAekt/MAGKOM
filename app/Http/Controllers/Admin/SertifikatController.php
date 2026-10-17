<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\StatusPenempatanEnum;
use App\Enums\StatusPenilaianEnum;
use App\Http\Controllers\Controller;
use App\Models\Penempatan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class SertifikatController extends Controller
{
    /**
     * Menampilkan direktori alumni magang berstatus selesai dan bernilai final (AC-12, BR-10).
     */
    public function index(Request $request): View
    {
        if (! \App\Support\Pengaturan::bool('fitur_sertifikat_aktif', true)) {
            abort(404, 'Fitur penerbitan sertifikat saat ini dinonaktifkan oleh administrator.');
        }

        $query = Penempatan::with(['peserta.profil', 'pembimbing', 'bidang', 'penilaian'])
            ->where('status', StatusPenempatanEnum::Selesai)
            ->whereHas('penilaian', function ($q) {
                $q->where('status', StatusPenilaianEnum::Final);
            });

        // Filter pencarian
        if ($keyword = $request->input('search')) {
            $query->where(function ($q) use ($keyword) {
                $q->whereHas('peserta', function ($sub) use ($keyword) {
                    $sub->where('nama', 'like', "%{$keyword}%")
                        ->orWhere('email', 'like', "%{$keyword}%");
                })->orWhereHas('peserta.profil', function ($sub) use ($keyword) {
                    $sub->where('institusi', 'like', "%{$keyword}%")
                        ->orWhere('nim_nisn', 'like', "%{$keyword}%");
                });
            });
        }

        // Filter bidang
        if ($bidangId = $request->input('bidang_id')) {
            $query->where('bidang_id', $bidangId);
        }

        $alumniList = $query->orderBy('tgl_selesai', 'desc')->paginate(10)->withQueryString();

        // Metrik statistik sertifikat
        $totalSelesai = Penempatan::where('status', StatusPenempatanEnum::Selesai)->count();
        $totalBersertifikat = Penempatan::where('status', StatusPenempatanEnum::Selesai)
            ->whereHas('penilaian', fn ($q) => $q->where('status', StatusPenilaianEnum::Final))
            ->count();
        $daftarBidang = \App\Models\Bidang::where('aktif', true)->get();

        return view('admin.sertifikat.index', [
            'alumniList' => $alumniList,
            'totalSelesai' => $totalSelesai,
            'totalBersertifikat' => $totalBersertifikat,
            'daftarBidang' => $daftarBidang,
        ]);
    }

    /**
     * Mencetak / mengunduh dokumen resmi Sertifikat Kelulusan Magang berformat PDF.
     */
    public function cetak(Penempatan $penempatan, Request $request): Response|View
    {
        if (! \App\Support\Pengaturan::bool('fitur_sertifikat_aktif', true)) {
            abort(404, 'Fitur penerbitan sertifikat saat ini dinonaktifkan oleh administrator.');
        }

        // Validasi syarat kelulusan (status selesai & nilai final)
        if ($penempatan->status !== StatusPenempatanEnum::Selesai) {
            abort(403, 'Sertifikat hanya dapat diterbitkan bagi peserta yang telah menyelesaikan magang (Status: Selesai).');
        }

        $penempatan->load(['peserta.profil', 'pembimbing.profil', 'bidang', 'penilaian']);

        if (! $penempatan->penilaian || $penempatan->penilaian->status !== StatusPenilaianEnum::Final) {
            abort(403, 'Sertifikat belum dapat dicetak karena evaluasi nilai magang belum difinalisasi oleh pembimbing.');
        }

        $tahun = $penempatan->tgl_selesai?->format('Y') ?? date('Y');
        $nomorSertifikat = '500.12/SRT/' . str_pad((string) $penempatan->id, 4, '0', STR_PAD_LEFT) . '/DISKOMINFO/' . $tahun;

        $verificationService = app(\App\Services\DocumentVerificationService::class);
        $dokumenVerifikasi = $verificationService->getOrCreateVerification(
            $penempatan,
            'sertifikat',
            $nomorSertifikat
        );
        $qrCodeSvg = $verificationService->generateQrCodeSvg(
            $verificationService->getVerificationUrl($dokumenVerifikasi->kode_unik),
            75
        );

        if ($request->query('preview') === '1') {
            return view('pdf.sertifikat-magang', [
                'penempatan' => $penempatan,
                'peserta' => $penempatan->peserta,
                'profil' => $penempatan->peserta?->profil,
                'penilaian' => $penempatan->penilaian,
                'nomorSertifikat' => $nomorSertifikat,
                'dokumenVerifikasi' => $dokumenVerifikasi,
                'qrCodeSvg' => $qrCodeSvg,
            ]);
        }

        $pdf = Pdf::loadView('pdf.sertifikat-magang', [
            'penempatan' => $penempatan,
            'peserta' => $penempatan->peserta,
            'profil' => $penempatan->peserta?->profil,
            'penilaian' => $penempatan->penilaian,
            'nomorSertifikat' => $nomorSertifikat,
            'dokumenVerifikasi' => $dokumenVerifikasi,
            'qrCodeSvg' => $qrCodeSvg,
        ])->setPaper('a4', 'landscape');

        $namaPeserta = $penempatan->peserta?->nama ?? 'alumni';
        $namaFile = 'sertifikat-magang-' . str_replace(' ', '-', strtolower($namaPeserta)) . '.pdf';

        return $pdf->download($namaFile);
    }
}
