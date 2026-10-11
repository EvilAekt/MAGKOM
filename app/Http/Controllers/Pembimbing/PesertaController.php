<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pembimbing;

use App\Enums\StatusPenempatanEnum;
use App\Enums\StatusTugasEnum;
use App\Http\Controllers\Controller;
use App\Models\Penempatan;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PesertaController extends Controller
{
    /**
     * Daftar direktori peserta bimbingan (Mentee Hub) - F-08, F-09, F-10.
     */
    public function index(Request $request): View|JsonResponse
    {
        $user = $request->user();
        $tab = $request->input('tab', 'aktif');

        $query = Penempatan::with(['peserta.profil.jurusan', 'bidang', 'tugas', 'logbooks', 'penilaian'])
            ->where(function ($q) use ($user) {
                if (! $user->isAdmin() && ! $user->isSuperAdmin()) {
                    $q->where('pembimbing_id', $user->id);
                }
            });

        if ($tab === 'selesai') {
            $query->where('status', StatusPenempatanEnum::Selesai);
        } else {
            $query->where('status', StatusPenempatanEnum::Aktif);
        }

        if ($request->filled('q')) {
            $search = '%' . $request->input('q') . '%';
            $query->whereHas('peserta', function ($q) use ($search) {
                $q->where('nama', 'like', $search)
                    ->orWhere('email', 'like', $search)
                    ->orWhereHas('profil', function ($qp) use ($search) {
                        $qp->where('institusi', 'like', $search)
                            ->orWhere('nim_nisn', 'like', $search)
                            ->orWhereHas('jurusan', function ($qj) use ($search) {
                                $qj->where('nama_jurusan', 'like', $search);
                            });
                    });
            });
        }

        $penempatanList = $query->latest('tgl_mulai')->paginate(15)->withQueryString();

        // Hitung akumulasi tab counter
        $baseCounter = Penempatan::where(function ($q) use ($user) {
            if (! $user->isAdmin() && ! $user->isSuperAdmin()) {
                $q->where('pembimbing_id', $user->id);
            }
        });

        $countAktif = (clone $baseCounter)->where('status', StatusPenempatanEnum::Aktif)->count();
        $countSelesai = (clone $baseCounter)->where('status', StatusPenempatanEnum::Selesai)->count();

        $data = [
            'penempatanList' => $penempatanList,
            'currentTab' => $tab,
            'countAktif' => $countAktif,
            'countSelesai' => $countSelesai,
        ];

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        }

        return view('pembimbing.peserta.index', $data);
    }

    /**
     * Profil 360° mentee bimbingan dan pelacak kinerja menyeluruh.
     */
    public function show(Request $request, Penempatan $penempatan): View|JsonResponse
    {
        $user = $request->user();

        // Proteksi IDOR ketat (NFR-S7): Hanya pembimbing terkait yang berhak melihat
        if (! $user->isAdmin() && ! $user->isSuperAdmin() && $penempatan->pembimbing_id !== $user->id) {
            abort(403, 'Anda tidak memiliki hak otorisasi untuk mengakses profil mentee ini.');
        }

        $penempatan->load([
            'peserta.profil.jurusan',
            'bidang',
            'pembimbing',
            'pendaftaran',
            'tugas.progresLogs',
            'logbooks',
            'penilaian.details.rubrik',
            'penilaian.riwayats.pengubah',
        ]);

        // Hitung metrik tugas
        $totalTugas = $penempatan->tugas->count();
        $tugasSelesai = $penempatan->tugas->where('status', StatusTugasEnum::Selesai)->count();
        $tugasAktif = $totalTugas - $tugasSelesai;

        // Hitung metrik logbook
        $totalLogbook = $penempatan->logbooks->count();
        $logbookPerluRevisi = $penempatan->logbooks->where('status.value', 'perlu_revisi')->count();
        $logbookDisetujui = $penempatan->logbooks->where('status.value', 'disetujui')->count();

        // Hitung durasi dan progres waktu magang
        $progresHari = 0;
        $totalHariMagang = 1;
        $sisaHari = 0;

        if ($penempatan->tgl_mulai && $penempatan->tgl_selesai) {
            $totalHariMagang = max(1, $penempatan->tgl_mulai->diffInDays($penempatan->tgl_selesai));
            $hariBerjalan = Carbon::today()->diffInDays($penempatan->tgl_mulai, false);
            if ($hariBerjalan < 0) {
                $progresHari = 0;
            } else {
                $progresHari = min(100, (int) round(($hariBerjalan / $totalHariMagang) * 100));
            }
            $sisaHari = Carbon::today()->diffInDays($penempatan->tgl_selesai, false);
        }

        $data = [
            'penempatan' => $penempatan,
            'totalTugas' => $totalTugas,
            'tugasSelesai' => $tugasSelesai,
            'tugasAktif' => $tugasAktif,
            'totalLogbook' => $totalLogbook,
            'logbookPerluRevisi' => $logbookPerluRevisi,
            'logbookDisetujui' => $logbookDisetujui,
            'progresHari' => $progresHari,
            'totalHariMagang' => $totalHariMagang,
            'sisaHari' => $sisaHari,
        ];

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        }

        return view('pembimbing.peserta.show', $data);
    }
}
