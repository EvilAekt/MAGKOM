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

        $today = Carbon::today();

        // Evaluasi kedisiplinan mentee aktif (Early Warning Mentee Pasif & Overdue BR-09, BR-16)
        $allAktifMentees = Penempatan::with(['tugas', 'logbooks'])
            ->where('status', StatusPenempatanEnum::Aktif)
            ->where(function ($q) use ($user) {
                if (! $user->isAdmin() && ! $user->isSuperAdmin()) {
                    $q->where('pembimbing_id', $user->id);
                }
            })
            ->get();

        $perhatianIds = [];
        foreach ($allAktifMentees as $mentee) {
            $hasOverdueTask = $mentee->tugas->contains(function ($t) use ($today) {
                return $t->deadline && Carbon::parse($t->deadline)->isPast() && $t->status !== StatusTugasEnum::Selesai;
            });

            $logbookBolong = false;
            if ($mentee->tgl_mulai && $mentee->tgl_mulai->isPast()) {
                $lastLogbook = $mentee->logbooks->sortByDesc('tanggal')->first();
                if ($lastLogbook && $lastLogbook->tanggal) {
                    $daysSinceLogbook = Carbon::parse($lastLogbook->tanggal)->diffInDays($today, false);
                    if ($daysSinceLogbook >= 3) {
                        $logbookBolong = true;
                    }
                } else {
                    $daysSinceStart = $mentee->tgl_mulai->diffInDays($today, false);
                    if ($daysSinceStart >= 3) {
                        $logbookBolong = true;
                    }
                }
            }

            if ($hasOverdueTask || $logbookBolong) {
                $perhatianIds[] = $mentee->id;
            }
        }

        if ($tab === 'selesai') {
            $query->where('status', StatusPenempatanEnum::Selesai);
        } elseif ($tab === 'perhatian') {
            $query->whereIn('id', $perhatianIds);
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
        $countPerhatian = count($perhatianIds);

        $data = [
            'penempatanList' => $penempatanList,
            'currentTab' => $tab,
            'countAktif' => $countAktif,
            'countPerhatian' => $countPerhatian,
            'countSelesai' => $countSelesai,
            'perhatianIds' => $perhatianIds,
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

        // Metrik Chart Visualisasi (B9)
        $tugasDalamPengerjaan = $penempatan->tugas->where('status', StatusTugasEnum::Proses)->count();
        $tugasBelumDimulai = $penempatan->tugas->where('status', StatusTugasEnum::Belum)->count();
        $tugasTerlambat = $penempatan->tugas->filter(fn ($t) => $t->terlambat)->count();

        $tugasStats = [
            'total' => $totalTugas,
            'selesai' => $tugasSelesai,
            'dalam_pengerjaan' => $tugasDalamPengerjaan,
            'belum_dimulai' => $tugasBelumDimulai,
            'terlambat' => $tugasTerlambat,
        ];
        $completionRate = $totalTugas > 0 ? (int) round(($tugasSelesai / $totalTugas) * 100) : 0;

        $daftarMinggu = $penempatan->getDaftarMingguMagang();
        $logbookPerMinggu = array_slice($daftarMinggu, 0, 12);

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
            'tugasStats' => $tugasStats,
            'completionRate' => $completionRate,
            'logbookPerMinggu' => $logbookPerMinggu,
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
