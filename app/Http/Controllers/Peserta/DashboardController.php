<?php

declare(strict_types=1);

namespace App\Http\Controllers\Peserta;

use App\Enums\StatusLogbookEnum;
use App\Enums\StatusPendaftaranEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\StatusPenilaianEnum;
use App\Enums\StatusTugasEnum;
use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;
use App\Models\Penempatan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard utama terpadu untuk peserta magang.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $profil = $user->profil;

        // Ambil pendaftaran terbaru peserta
        $pendaftaran = Pendaftaran::with([
            'bulanMagang',
            'kuota.jurusan',
            'dokumen.jenisDokumen',
            'penempatan.pembimbing',
            'penempatan.bidang',
        ])
            ->where('user_id', $user->id)
            ->latest()
            ->first();

        // Ambil penempatan magang jika sudah ditempatkan
        $penempatan = Penempatan::with([
            'pembimbing',
            'bidang',
            'tugas.progresLogs',
            'logbooks',
            'penilaian.details.rubrik',
        ])
            ->where('peserta_id', $user->id)
            ->latest()
            ->first();

        // Tentukan fase perjalanan magang peserta
        $fase = 'onboarding';
        if ($penempatan && $penempatan->status === StatusPenempatanEnum::Selesai) {
            $fase = 'selesai';
        } elseif ($penempatan && $penempatan->status === StatusPenempatanEnum::Aktif) {
            $fase = 'aktif';
        } elseif ($pendaftaran) {
            $fase = 'seleksi';
        }

        // Statistik & KPI Tugas
        $totalTugas = $penempatan?->tugas->count() ?? 0;
        $tugasSelesai = $penempatan?->tugas->where('status', StatusTugasEnum::Selesai)->count() ?? 0;
        $tugasBelum = $penempatan?->tugas->whereIn('status', [StatusTugasEnum::Belum, StatusTugasEnum::Proses])->count() ?? 0;
        $tugasMendesak = $penempatan
            ? $penempatan->tugas()
                ->whereIn('status', [StatusTugasEnum::Belum, StatusTugasEnum::Proses])
                ->orderBy('deadline', 'asc')
                ->take(4)
                ->get()
            : collect();

        // Statistik & KPI Logbook
        $totalLogbook = $penempatan?->logbooks->count() ?? 0;
        $logbookPerluRevisi = $penempatan?->logbooks->where('status', StatusLogbookEnum::PerluRevisi)->count() ?? 0;
        $logbookTerbaru = $penempatan
            ? $penempatan->logbooks()->latest('tanggal')->take(4)->get()
            : collect();

        // Sisa Hari Magang
        $sisaHari = null;
        $tglSelesai = $penempatan?->tgl_selesai ?? $penempatan?->tanggal_selesai;
        if ($penempatan && $tglSelesai) {
            $diff = now()->startOfDay()->diffInDays(Carbon::parse($tglSelesai)->startOfDay(), false);
            $sisaHari = max(0, (int) $diff);
        }

        // Data Penilaian
        $penilaian = $penempatan?->penilaian;
        $isFinalNilai = $penilaian && $penilaian->status === StatusPenilaianEnum::Final;

        return view('peserta.dashboard', [
            'user'               => $user,
            'profil'             => $profil,
            'pendaftaran'        => $pendaftaran,
            'penempatan'         => $penempatan,
            'fase'               => $fase,
            'totalTugas'         => $totalTugas,
            'tugasSelesai'       => $tugasSelesai,
            'tugasBelum'         => $tugasBelum,
            'tugasMendesak'      => $tugasMendesak,
            'totalLogbook'       => $totalLogbook,
            'logbookPerluRevisi' => $logbookPerluRevisi,
            'logbookTerbaru'     => $logbookTerbaru,
            'sisaHari'           => $sisaHari,
            'penilaian'          => $penilaian,
            'isFinalNilai'       => $isFinalNilai,
        ]);
    }
}
