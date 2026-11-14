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
        $profil?->loadMissing(['jurusan.bidang']);

        // Ambil pendaftaran terbaru peserta
        $pendaftaran = Pendaftaran::with([
            'bulanMagang',
            'kuota.jurusan.bidang',
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
        $mingguAktif = $penempatan?->minggu_aktif ?? 0;
        $totalMinggu = $penempatan?->total_minggu ?? 0;
        $logbookSudahDiisi = collect($daftarMinggu)->whereIn('status', ['disetujui', 'terkirim'])->count();
        // Logbook belum diisi dihitung dari minggu yang sudah/sedang berjalan (<= minggu aktif) yang belum diisi
        $logbookBelumDiisi = collect($daftarMinggu)
            ->where('minggu_ke', '<=', $mingguAktif)
            ->whereNotIn('status', ['disetujui', 'terkirim'])
            ->count();

        // Info Perlu Perhatian (Logbook Minggu Berjalan)
        $mingguAktifItem = collect($daftarMinggu)->firstWhere('minggu_ke', $mingguAktif);
        // Batas pengiriman logbook hari Senin berikutnya (+1 hari dari akhir minggu berjalan)
        $batasLogbookMingguAktif = $mingguAktifItem ? Carbon::parse($mingguAktifItem['tgl_akhir'])->addDay()->isoFormat('D MMM') : null;
        $isMingguAktifBelumDiisi = $mingguAktifItem ? ($mingguAktifItem['status'] === 'belum_diisi') : false;

        // Nama Pembimbing Bersih (Tanpa embel-embel jabatan di dalam kurung)
        $namaPembimbingClean = $penempatan?->pembimbing?->nama ? preg_replace('/\s*\(.*?\)/', '', (string) $penempatan->pembimbing->nama) : null;

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

        // Hitung durasi bulan yang dipilih peserta (1, 2, 3, 6 bulan)
        $durasiBulan = $penempatan?->durasi_bulan
            ?? (int) ($pendaftaran?->durasi_bulan ?? 3);

        // Siklus Waktu & Sisa Hari Magang (Sesuai Durasi Bulan Pilihan Peserta)
        $today = now()->startOfDay();
        $tglMulai = $penempatan?->tgl_mulai ? Carbon::parse($penempatan->tgl_mulai)->startOfDay() : null;
        $tglSelesai = $penempatan?->tgl_selesai ? Carbon::parse($penempatan->tgl_selesai)->startOfDay() : null;

        $isBelumMulai = false;
        $hariMenujuMulai = 0;
        $totalHariMagang = max(1, $durasiBulan * 30);
        $hariBerjalan = 0;
        $sisaHari = null;
        $progresHariPct = 0;

        if ($penempatan && $tglMulai && $tglSelesai) {
            $totalHariMagang = max(1, (int) $tglMulai->diffInDays($tglSelesai));

            if ($today->lt($tglMulai)) {
                // Magang belum dimulai (Pra-Magang)
                $isBelumMulai = true;
                $hariMenujuMulai = max(1, (int) $today->diffInDays($tglMulai));
                $sisaHari = $totalHariMagang; // Sisa hari masih utuh sesuai durasi pilihan peserta
                $progresHariPct = 0;
            } elseif ($today->gt($tglSelesai)) {
                // Magang telah selesai
                $sisaHari = 0;
                $hariBerjalan = $totalHariMagang;
                $progresHariPct = 100;
            } else {
                // Magang sedang berjalan aktif
                $hariBerjalan = max(0, (int) $tglMulai->diffInDays($today));
                $sisaHari = max(0, (int) $today->diffInDays($tglSelesai));
                $progresHariPct = min(100, (int) round(($hariBerjalan / $totalHariMagang) * 100));
            }
        }

        // Ekstraksi Informasi Bidang Terpilih
        $bidangTerpilih = $penempatan?->bidang?->nama_bidang
            ?? $pendaftaran?->penempatan?->bidang?->nama_bidang
            ?? $pendaftaran?->kuota?->jurusan?->bidang?->pluck('nama_bidang')->filter()->implode(', ')
            ?? $profil?->jurusan?->bidang?->pluck('nama_bidang')->filter()->implode(', ')
            ?? 'Menyesuaikan Jurusan';

        if (empty($bidangTerpilih)) {
            $bidangTerpilih = 'Menyesuaikan Jurusan';
        }

        // Kelengkapan Biodata Profil Peserta (Strict 100%)
        $kelengkapanProfil = $profil?->hitungKelengkapan() ?? [
            'checklist' => [],
            'persentase' => 0,
            'is_lengkap' => false,
        ];
        $isProfilLengkap = $kelengkapanProfil['is_lengkap'];
        $persentaseProfil = $kelengkapanProfil['persentase'];

        return view('peserta.dashboard', [
            'user' => $user,
            'profil' => $profil,
            'isProfilLengkap' => $isProfilLengkap,
            'persentaseProfil' => $persentaseProfil,
            'pendaftaran' => $pendaftaran,
            'penempatan' => $penempatan,
            'bidangTerpilih' => $bidangTerpilih,
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
            'durasiBulan' => $durasiBulan,
            'totalHariMagang' => $totalHariMagang,
            'isBelumMulai' => $isBelumMulai,
            'hariMenujuMulai' => $hariMenujuMulai,
            'progresHariPct' => $progresHariPct,
            'kelompokMagang' => $user->kelompokMagang ?? $pendaftaran?->kelompokMagang,
        ]);
    }
}
