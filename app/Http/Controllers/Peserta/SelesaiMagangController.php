<?php

declare(strict_types=1);

namespace App\Http\Controllers\Peserta;

use App\Enums\StatusPenempatanEnum;
use App\Http\Controllers\Controller;
use App\Models\Penempatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SelesaiMagangController extends Controller
{
    /**
     * Menampilkan halaman status selesai magang dan unduhan surat keterangan selesai individual.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $penempatan = Penempatan::with(['bidang', 'pembimbing', 'pendaftaran'])
            ->where('peserta_id', $user->id)
            ->latest('id')
            ->first();

        $isSelesai = $penempatan && $penempatan->status === StatusPenempatanEnum::Selesai;
        $hasSurat = $penempatan && ! empty($penempatan->file_surat_selesai) && Storage::disk('local')->exists($penempatan->file_surat_selesai);

        return view('peserta.selesai-magang.index', [
            'penempatan' => $penempatan,
            'isSelesai' => $isSelesai,
            'hasSurat' => $hasSurat,
        ]);
    }

    /**
     * Mengunduh dokumen Surat Keterangan Selesai Magang milik peserta yang sedang login (IDOR-Safe).
     */
    public function unduh(Request $request): BinaryFileResponse
    {
        $user = $request->user();

        // Pastikan dokumen diambil secara ketat berdasarkan peserta_id user yang sedang login (Cegah IDOR)
        $penempatan = Penempatan::where('peserta_id', $user->id)
            ->latest('id')
            ->first();

        if (! $penempatan) {
            abort(404, 'Data penempatan magang tidak ditemukan.');
        }

        if ($penempatan->status !== StatusPenempatanEnum::Selesai) {
            abort(403, 'Surat keterangan selesai magang belum dapat diunduh karena masa magang Anda belum berstatus selesai.');
        }

        if (! $penempatan->file_surat_selesai || ! Storage::disk('local')->exists($penempatan->file_surat_selesai)) {
            abort(404, 'Berkas surat selesai magang belum diunggah oleh administrator instansi.');
        }

        $namaFileDownload = 'Surat_Keterangan_Selesai_Magang_'.Str::slug($user->nama).'.pdf';

        return response()->download(
            Storage::disk('local')->path($penempatan->file_surat_selesai),
            $namaFileDownload,
            ['Content-Type' => 'application/pdf']
        );
    }
}
