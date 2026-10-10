<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Enums\JalurEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusPendaftaranEnum;
use App\Enums\StatusPenempatanEnum;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\BulanMagang;
use App\Models\Kuota;
use App\Models\Pendaftaran;
use App\Models\Penempatan;
use App\Models\PesertaProfil;
use App\Models\SlotKhusus;
use App\Models\User;
use App\Support\Pengaturan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard eksekutif SuperAdmin MagKom (PRD 9.2 & 4.3).
     */
    public function index(Request $request): View
    {
        // 1. Metrik Eksekutif Utama
        $totalPesertaAktif = Penempatan::where('status', StatusPenempatanEnum::Aktif)->count();
        $totalAlumni = Penempatan::where('status', StatusPenempatanEnum::Selesai)->count();
        $totalPesertaMundur = Penempatan::where('status', StatusPenempatanEnum::MengundurkanDiri)->count();
        $totalAdmin = User::where('role', RoleEnum::Admin)->where('status_aktif', true)->count();
        $totalPembimbing = User::where('role', RoleEnum::Pembimbing)->where('status_aktif', true)->count();

        // Sebaran Jalur Peserta Aktif
        $totalPesertaJalurUmum = Penempatan::where('status', StatusPenempatanEnum::Aktif)
            ->where('jalur', JalurEnum::Umum)
            ->count();
        $totalPesertaJalurKhusus = Penempatan::where('status', StatusPenempatanEnum::Aktif)
            ->where('jalur', JalurEnum::Khusus)
            ->count();

        // Antrean Pendaftaran yang Membutuhkan Atensi
        $totalAntreanDiajukan = Pendaftaran::where('status', StatusPendaftaranEnum::Diajukan)->count();
        $totalWaitingList = Pendaftaran::where('status', StatusPendaftaranEnum::WaitingList)->count();

        // 2. Bulan Aktif & Utilisasi Kuota vs Slot Khusus
        $bulanAktif = BulanMagang::where('aktif', true)
            ->whereDate('bulan_mulai', '>=', now()->startOfMonth())
            ->orderBy('bulan_mulai', 'asc')
            ->first() ?? BulanMagang::where('aktif', true)->orderBy('bulan_mulai', 'desc')->first();

        $slotKhusus = null;
        $totalKuotaUmum = 0;
        $totalKuotaTerisi = 0;

        if ($bulanAktif) {
            // Pencocokan slot khusus berdasarkan tanggal bulan_mulai
            $slotKhusus = SlotKhusus::whereDate('bulan_mulai', $bulanAktif->bulan_mulai)
                ->where('aktif', true)
                ->first();
            $totalKuotaUmum = Kuota::where('bulan_magang_id', $bulanAktif->id)->where('aktif', true)->sum('jumlah');
            $totalKuotaTerisi = Kuota::where('bulan_magang_id', $bulanAktif->id)->where('aktif', true)->sum('terisi');
        }

        // 3. Utilisasi Beban Pembimbing Lapangan (BR-13)
        $maksBeban = Pengaturan::get('maks_peserta_aktif_per_pembimbing');
        $pembimbingList = User::where('role', RoleEnum::Pembimbing)
            ->where('status_aktif', true)
            ->withCount(['penempatanBimbingan as mentee_aktif_count' => function ($q) {
                $q->where('status', StatusPenempatanEnum::Aktif);
            }])
            ->orderBy('nama')
            ->get();

        // 4. Demografi Jenjang & 5 Asal Kampus/Sekolah Teratas (PRD 4.3)
        $jenjangRaw = PesertaProfil::select('jenjang', DB::raw('count(*) as total'))
            ->groupBy('jenjang')
            ->pluck('total', 'jenjang')
            ->toArray();

        $topInstitusi = PesertaProfil::select('institusi', DB::raw('count(*) as total'))
            ->groupBy('institusi')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        // 5. Jejak Audit Mutasi 5 Terakhir (AC-13-6)
        $auditTerbaru = AuditLog::with('user')
            ->orderBy('waktu', 'desc')
            ->orderBy('id', 'desc')
            ->take(5)
            ->get();

        return view('superadmin.dashboard', [
            'totalPesertaAktif' => $totalPesertaAktif,
            'totalAlumni' => $totalAlumni,
            'totalPesertaMundur' => $totalPesertaMundur,
            'totalAdmin' => $totalAdmin,
            'totalPembimbing' => $totalPembimbing,
            'totalPesertaJalurUmum' => $totalPesertaJalurUmum,
            'totalPesertaJalurKhusus' => $totalPesertaJalurKhusus,
            'totalAntreanDiajukan' => $totalAntreanDiajukan,
            'totalWaitingList' => $totalWaitingList,
            'bulanAktif' => $bulanAktif,
            'slotKhusus' => $slotKhusus,
            'totalKuotaUmum' => $totalKuotaUmum,
            'totalKuotaTerisi' => $totalKuotaTerisi,
            'maksBeban' => $maksBeban,
            'pembimbingList' => $pembimbingList,
            'jenjangRaw' => $jenjangRaw,
            'topInstitusi' => $topInstitusi,
            'auditTerbaru' => $auditTerbaru,
        ]);
    }
}
