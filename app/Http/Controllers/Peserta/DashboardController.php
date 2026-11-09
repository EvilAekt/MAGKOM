<?php

declare(strict_types=1);

namespace App\Http\Controllers\Peserta;

use App\Enums\StatusLogbookEnum;
use App\Enums\StatusPenempatanEnum;
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
        $user->loadMissing(['kelompokMagang.anggota']);
        $profil = $user->profil;

        // Ambil pendaftaran terbaru peserta
        $pendaftaran = Pendaftaran::with([
            'bulanMagang',
            'kuota.jurusan',
            'dokumen.jenisDokumen',
            'penempatan.pembimbing',
            'penempatan.bidang',
            'kelompokMagang.anggota',
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

        // Statistik & KPI Tugas (hanya tugas yang sudah dipublikasikan)
        $publishedTugas = $penempatan?->tugas->filter(fn ($t) => $t->status_publikasi === 'dipublikasikan') ?? collect();
        $totalTugas = $publishedTugas->count();
        $tugasSelesai = $publishedTugas->where('status', StatusTugasEnum::Selesai)->count();
        $tugasBelum = $publishedTugas->whereIn('status', [StatusTugasEnum::Belum, StatusTugasEnum::Proses])->count();
        $tugasLewatTenggat = $publishedTugas->filter(fn ($t) => $t->deadline && now()->gt(Carbon::parse($t->deadline)->endOfDay()) && $t->status !== StatusTugasEnum::Selesai)->count();
        $tugasMendesak = $penempatan
            ? $penempatan->tugas()
                ->dipublikasikan()
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

        // Data Mingguan & Perjalanan Magang
        $daftarMinggu = $penempatan?->getDaftarMingguMagang() ?? [];
        $mingguAktif = $penempatan?->minggu_aktif ?? 3;
        $totalMinggu = $penempatan?->total_minggu ?? 13;
        $logbookSudahDiisi = collect($daftarMinggu)->whereIn('status', ['disetujui', 'terkirim'])->count();
        // Logbook belum diisi dihitung dari minggu yang sudah/sedang berjalan (<= minggu aktif) yang belum diisi
        $logbookBelumDiisi = collect($daftarMinggu)
            ->where('minggu_ke', '<=', $mingguAktif)
            ->whereNotIn('status', ['disetujui', 'terkirim'])
            ->count();

        // Info Perlu Perhatian (Logbook Minggu Berjalan)
        $mingguAktifItem = collect($daftarMinggu)->firstWhere('minggu_ke', $mingguAktif);
        // Batas pengiriman logbook hari Senin berikutnya (+1 hari dari akhir minggu berjalan)
        $batasLogbookMingguAktif = $mingguAktifItem ? Carbon::parse($mingguAktifItem['tgl_akhir'])->addDay()->isoFormat('D MMM') : '9 Nov';
        $isMingguAktifBelumDiisi = $mingguAktifItem ? ($mingguAktifItem['status'] === 'belum_diisi') : true;

        // Nama Pembimbing Bersih (Tanpa embel-embel jabatan di dalam kurung)
        $namaPembimbingClean = preg_replace('/\s*\(.*?\)/', '', (string) ($penempatan?->pembimbing?->nama ?? 'Budi Santoso, S.Kom'));

        // Tanggal-tanggal kegiatan untuk Kalender (Logbook & Deadline Tugas)
        $calendarEventDates = [];
        if ($penempatan) {
            foreach ($penempatan->logbooks as $lb) {
                if ($lb->tanggal) {
                    $calendarEventDates[Carbon::parse($lb->tanggal)->format('Y-m-d')] = true;
                }
            }
            foreach ($publishedTugas as $tg) {
                if ($tg->deadline) {
                    $calendarEventDates[Carbon::parse($tg->deadline)->format('Y-m-d')] = true;
                }
            }
        }

        // Sisa Hari Magang
        $sisaHari = null;
        $tglSelesai = $penempatan?->tgl_selesai;
        if ($penempatan && $tglSelesai) {
            $diff = now()->startOfDay()->diffInDays(Carbon::parse($tglSelesai)->startOfDay(), false);
            $sisaHari = max(0, (int) $diff);
        }

        return view('peserta.dashboard', [
            'user' => $user,
            'profil' => $profil,
            'pendaftaran' => $pendaftaran,
            'penempatan' => $penempatan,
            'fase' => $fase,
            'totalTugas' => $totalTugas,
            'tugasSelesai' => $tugasSelesai,
            'tugasBelum' => $tugasBelum,
            'tugasLewatTenggat' => $tugasLewatTenggat,
            'tugasMendesak' => $tugasMendesak,
            'totalLogbook' => $totalLogbook,
            'logbookPerluRevisi' => $logbookPerluRevisi,
            'logbookTerbaru' => $logbookTerbaru,
            'daftarMinggu' => $daftarMinggu,
            'mingguAktif' => $mingguAktif,
            'totalMinggu' => $totalMinggu,
            'logbookSudahDiisi' => $logbookSudahDiisi,
            'logbookBelumDiisi' => $logbookBelumDiisi,
            'batasLogbookMingguAktif' => $batasLogbookMingguAktif,
            'isMingguAktifBelumDiisi' => $isMingguAktifBelumDiisi,
            'calendarEventDates' => $calendarEventDates,
            'namaPembimbingClean' => $namaPembimbingClean,
            'sisaHari' => $sisaHari,
            'kelompokMagang' => $user->kelompokMagang ?? $pendaftaran?->kelompokMagang,
        ]);
    }
}
