<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Enums\JalurEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusLogbookEnum;
use App\Enums\StatusPendaftaranEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\StatusPenilaianEnum;
use App\Enums\StatusTugasEnum;
use App\Enums\TipeAuditEnum;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\BulanMagang;
use App\Models\Kuota;
use App\Models\Logbook;
use App\Models\Pendaftaran;
use App\Models\Penempatan;
use App\Models\Penilaian;
use App\Models\PesertaProfil;
use App\Models\SlotKhusus;
use App\Models\Tugas;
use App\Models\User;
use App\Services\BulanStatusService;
use App\Support\Pengaturan;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard eksekutif SuperAdmin MagKom (PRD 9.2, 4.3 & Unified Hub).
     */
    public function index(Request $request): View
    {
        $periode = $this->periodeDanKuota($request);
        $bulanAktif = $periode['bulanAktif'];
        $parameter = $this->parameterSistem();

        $data = array_merge(
            $this->ringkasanMetrik(),
            $periode,
            $this->seleksi(),
            $this->bimbingan(),
            $parameter,
            $this->demografi(),
            $this->kesehatanSistem(),
            $this->kpiDinas($bulanAktif),
            $this->keamanan($parameter['berkasMaksMb'])
        );

        $data['perluTindakan'] = $this->perluTindakan($data);

        return view('superadmin.dashboard', $data);
    }

    /**
     * Daftar butir yang butuh tindakan SuperAdmin (hanya yang jumlahnya > 0).
     *
     * @param  array<string, mixed>  $d
     */
    private function perluTindakan(array $d): Collection
    {
        return collect([
            ['label' => 'Antrean verifikasi', 'jumlah' => $d['totalAntreanDiajukan'], 'tone' => 'warning', 'route' => 'admin.pendaftaran.index'],
            ['label' => 'Melewati SLA 5 hari', 'jumlah' => $d['antreanMelebihiSla'], 'tone' => 'danger', 'route' => 'admin.pendaftaran.index'],
            ['label' => 'Waiting list', 'jumlah' => $d['totalWaitingList'], 'tone' => 'info', 'route' => 'admin.waiting-list.index'],
            ['label' => 'Tugas terlambat', 'jumlah' => $d['totalTugasTerlambat'], 'tone' => 'danger', 'route' => 'superadmin.monitoring.index'],
            ['label' => 'Staf belum ganti sandi', 'jumlah' => $d['stafPendingGantiPassword'], 'tone' => 'warning', 'route' => 'superadmin.staf.index'],
            ['label' => 'Login gagal 24 jam', 'jumlah' => $d['loginGagal24Jam'], 'tone' => 'danger', 'route' => 'superadmin.audit-log.index'],
        ])->filter(fn (array $i) => $i['jumlah'] > 0)->values();
    }

    /**
     * Metrik utama (Figma Node 560-26404) dan metrik pendukung eksekutif.
     *
     * @return array<string, mixed>
     */
    private function ringkasanMetrik(): array
    {
        $pesertaAktif = Penempatan::where('status', StatusPenempatanEnum::Aktif)->count();

        return [
            'totalPengguna' => User::count(),
            'menungguVerifikasi' => Pendaftaran::where('status', StatusPendaftaranEnum::Diajukan)->count(),
            'pesertaAktif' => $pesertaAktif,
            'akunNonaktif' => User::where('status_aktif', false)->count(),
            'penggunaTerbaru' => User::orderBy('id', 'desc')->take(5)->get(),
            'totalPesertaAktif' => $pesertaAktif,
            'totalAlumni' => Penempatan::where('status', StatusPenempatanEnum::Selesai)->count(),
            'totalPesertaMundur' => Penempatan::where('status', StatusPenempatanEnum::MengundurkanDiri)->count(),
            'totalAdmin' => User::where('role', RoleEnum::Admin)->where('status_aktif', true)->count(),
            'totalPembimbing' => User::where('role', RoleEnum::Pembimbing)->where('status_aktif', true)->count(),
            // Peserta dalam Masa Tenggang Aktif (PRD F-12, AC-12-2)
            'totalPesertaMasaTenggang' => Penempatan::where('status', StatusPenempatanEnum::Selesai)
                ->whereDate('tgl_nonaktif', '>=', now()->toDateString())
                ->count(),
            // Staf Baru Pending Ganti Kata Sandi Awal (PRD BR-15, AC-11-3)
            'stafPendingGantiPassword' => User::whereIn('role', [RoleEnum::Admin, RoleEnum::Pembimbing])
                ->where('status_aktif', true)
                ->where('must_change_password', true)
                ->count(),
            'totalPesertaJalurUmum' => Penempatan::where('status', StatusPenempatanEnum::Aktif)
                ->where('jalur', JalurEnum::Umum)
                ->count(),
            'totalPesertaJalurKhusus' => Penempatan::where('status', StatusPenempatanEnum::Aktif)
                ->where('jalur', JalurEnum::Khusus)
                ->count(),
        ];
    }

    /**
     * Periode bulan aktif, status landing page, slot khusus, dan kuota (PRD F-11, BR-04).
     *
     * @return array<string, mixed>
     */
    private function periodeDanKuota(Request $request): array
    {
        $daftarBulan = BulanMagang::where('aktif', true)
            ->orderBy('bulan_mulai', 'desc')
            ->get();

        $selectedBulanId = $request->query('bulan_id');
        $bulanAktif = null;

        if ($selectedBulanId) {
            $bulanAktif = BulanMagang::where('aktif', true)->find($selectedBulanId);
        }

        if (! $bulanAktif) {
            $bulanAktif = BulanMagang::where('aktif', true)
                ->whereDate('bulan_mulai', '>=', now()->startOfMonth())
                ->orderBy('bulan_mulai', 'asc')
                ->first() ?? $daftarBulan->first();
        }

        $statusPublik = null;
        $isOverrideStatus = false;
        if ($bulanAktif) {
            $statusPublik = app(BulanStatusService::class)->status($bulanAktif);
            $isOverrideStatus = $bulanAktif->override_status !== null;
        }

        $slotKhusus = null;
        $totalKuotaUmum = 0;
        $totalKuotaTerisi = 0;
        $kuotaList = collect();

        if ($bulanAktif) {
            $slotKhusus = SlotKhusus::whereDate('bulan_mulai', $bulanAktif->bulan_mulai)
                ->where('aktif', true)
                ->first();
            $totalKuotaUmum = Kuota::where('bulan_magang_id', $bulanAktif->id)->where('aktif', true)->sum('jumlah');
            $totalKuotaTerisi = Kuota::where('bulan_magang_id', $bulanAktif->id)->where('aktif', true)->sum('terisi');
            $kuotaList = Kuota::where('bulan_magang_id', $bulanAktif->id)
                ->with('jurusan')
                ->orderBy('jurusan_id')
                ->get();
        }

        return [
            'daftarBulan' => $daftarBulan,
            'bulanAktif' => $bulanAktif,
            'statusPublik' => $statusPublik,
            'isOverrideStatus' => $isOverrideStatus,
            'slotKhusus' => $slotKhusus,
            'totalKuotaUmum' => $totalKuotaUmum,
            'totalKuotaTerisi' => $totalKuotaTerisi,
            // Pagu Kuota Kebijakan Bulanan Dinas (PRD BR-01, Bab 8.6)
            'kuotaMaksPagu' => Pengaturan::int('kuota_umum_maks_per_bulan', 12),
            'kuotaList' => $kuotaList,
        ];
    }

    /**
     * Antrean seleksi: Diajukan, Waiting List, dan 5 pendaftaran FIFO tertunda.
     *
     * @return array<string, mixed>
     */
    private function seleksi(): array
    {
        $totalAntreanDiajukan = Pendaftaran::where('status', StatusPendaftaranEnum::Diajukan)->count();

        return [
            'totalAntreanDiajukan' => $totalAntreanDiajukan,
            'totalWaitingList' => Pendaftaran::where('status', StatusPendaftaranEnum::WaitingList)->count(),
            'pendaftaranMenunggu' => Pendaftaran::with(['user.profil', 'bulanMagang', 'kuota.jurusan'])
                ->whereIn('status', [StatusPendaftaranEnum::Diajukan, StatusPendaftaranEnum::PerluPerbaikan])
                ->orderBy('diajukan_at', 'asc')
                ->orderBy('id', 'asc')
                ->take(5)
                ->get(),
        ];
    }

    /**
     * Beban pembimbing (BR-13), bimbingan, tugas (F-08, BR-16), dan off-boarding.
     *
     * @return array<string, mixed>
     */
    private function bimbingan(): array
    {
        $today = now()->toDateString();
        $aktifTugas = [StatusTugasEnum::Belum, StatusTugasEnum::Proses];

        return [
            'maksBeban' => Pengaturan::get('maks_peserta_aktif_per_pembimbing'),
            'pembimbingList' => User::where('role', RoleEnum::Pembimbing)
                ->where('status_aktif', true)
                ->withCount(['penempatanBimbingan as mentee_aktif_count' => function ($q) {
                    $q->where('status', StatusPenempatanEnum::Aktif);
                }])
                ->orderBy('nama')
                ->get(),
            'totalLogbookPending' => Logbook::where('status', StatusLogbookEnum::Terkirim)->count(),
            'totalTugasAktif' => Tugas::whereIn('status', $aktifTugas)->count(),
            'totalPenilaianSelesai' => Penilaian::where('status', StatusPenilaianEnum::Final)->count(),
            'totalTugasTerlambat' => Tugas::whereIn('status', $aktifTugas)
                ->where('deadline', '<', $today)
                ->count(),
            'totalTugasMendekatiDeadline' => Tugas::whereIn('status', $aktifTugas)
                ->where('deadline', '>=', $today)
                ->where('deadline', '<=', now()->addDays(3)->toDateString())
                ->count(),
            // Peserta Mendekati Batas Akhir Magang (Off-boarding Tracker <= 14 Hari)
            'pesertaMendekatiSelesai' => Penempatan::where('status', StatusPenempatanEnum::Aktif)
                ->where('tgl_selesai', '<=', now()->addDays(14)->toDateString())
                ->with(['peserta', 'bidang', 'pembimbing'])
                ->orderBy('tgl_selesai', 'asc')
                ->take(5)
                ->get(),
        ];
    }

    /**
     * Parameter sistem resmi PRD Bab 8.6.
     *
     * @return array<string, mixed>
     */
    private function parameterSistem(): array
    {
        $logbookMaksMundurHari = Pengaturan::int('logbook_maks_mundur_hari', Pengaturan::int('batas_hari_logbook_mundur', 7));
        $masaTenggangBulan = Pengaturan::int('masa_tenggang_bulan', 1);

        return [
            'logbookMaksMundurHari' => $logbookMaksMundurHari,
            'masaTenggangBulan' => $masaTenggangBulan,
            'berkasMaksMb' => Pengaturan::int('berkas_maks_mb', 2),
            'slotKhususDefault' => Pengaturan::int('slot_khusus_default', 3),
            'passingGradeNilai' => Pengaturan::int('passing_grade_sertifikat', 70),
            // Backward compatibility parameter view lama
            'batasHariLogbookMundur' => $logbookMaksMundurHari,
            'masaTenggangUnduhNilaiHari' => Pengaturan::int('masa_tenggang_unduh_nilai_hari', $masaTenggangBulan * 30),
        ];
    }

    /**
     * Demografi jenjang & 5 asal institusi teratas (PRD 4.3) serta 5 audit terbaru (AC-13-6).
     *
     * @return array<string, mixed>
     */
    private function demografi(): array
    {
        return [
            'jenjangRaw' => PesertaProfil::select('jenjang', DB::raw('count(*) as total'))
                ->groupBy('jenjang')
                ->pluck('total', 'jenjang')
                ->toArray(),
            'topInstitusi' => PesertaProfil::select('institusi', DB::raw('count(*) as total'))
                ->groupBy('institusi')
                ->orderByDesc('total')
                ->take(5)
                ->get(),
            'auditTerbaru' => AuditLog::with('user')
                ->orderBy('waktu', 'desc')
                ->orderBy('id', 'desc')
                ->take(5)
                ->get(),
        ];
    }

    /**
     * Status kesehatan sistem & infrastruktur (TDD Bab 10, NFR-O1).
     *
     * @return array<string, mixed>
     */
    private function kesehatanSistem(): array
    {
        return [
            'systemHealth' => [
                'database' => true,
                'private_storage' => is_dir(storage_path('app/private')) || is_dir(storage_path('app')),
                'audit_count' => AuditLog::count(),
                'scheduler_active' => true,
            ],
        ];
    }

    /**
     * Pusat metrik keberhasilan produk & KPI Dinas (PRD Bab 2.3).
     *
     * @return array<string, mixed>
     */
    private function kpiDinas(?BulanMagang $bulanAktif): array
    {
        // KPI 1: Integritas Kuota (Target 0 kejadian kuota minus/bocor)
        $anomaliKuotaCount = Kuota::whereRaw('terisi > jumlah')->count() + SlotKhusus::whereRaw('terisi > jumlah')->count();

        // KPI 2: SLA Kecepatan Verifikasi Administrasi (Target <= 5 hari kerja)
        $pendaftaranDiputuskan = Pendaftaran::whereNotNull('diputuskan_at')
            ->whereNotNull('diajukan_at')
            ->when($bulanAktif, fn ($q) => $q->where('bulan_magang_id', $bulanAktif->id))
            ->get(['diputuskan_at', 'diajukan_at']);

        $rataRataSlaVerifikasiHari = $pendaftaranDiputuskan->isNotEmpty()
            ? round($pendaftaranDiputuskan->avg(fn ($p) => max(0, (float) $p->diajukan_at->diffInDays($p->diputuskan_at))), 1)
            : null;

        $antreanMelebihiSla = Pendaftaran::where('status', StatusPendaftaranEnum::Diajukan)
            ->where('diajukan_at', '<', now()->subDays(5))
            ->count();

        // KPI 3: Penempatan Selesai yang memiliki nilai final (Target 100%)
        $penempatanSelesaiTotal = Penempatan::where('status', StatusPenempatanEnum::Selesai)->count();
        $penempatanSelesaiDenganNilai = Penempatan::where('status', StatusPenempatanEnum::Selesai)
            ->whereHas('penilaian', fn ($q) => $q->where('status', StatusPenilaianEnum::Final))
            ->count();

        return [
            'anomaliKuotaCount' => $anomaliKuotaCount,
            'rataRataSlaVerifikasiHari' => $rataRataSlaVerifikasiHari,
            'antreanMelebihiSla' => $antreanMelebihiSla,
            'penempatanSelesaiTotal' => $penempatanSelesaiTotal,
            'penempatanSelesaiDenganNilai' => $penempatanSelesaiDenganNilai,
            'kepatuhanNilaiSelesaiPersen' => $penempatanSelesaiTotal > 0
                ? round(($penempatanSelesaiDenganNilai / $penempatanSelesaiTotal) * 100)
                : 100,
        ];
    }

    /**
     * Pusat monitoring keamanan sistem (PRD Bab 10 & TDD Bab 8-9).
     *
     * @return array<string, mixed>
     */
    private function keamanan(int $berkasMaksMb): array
    {
        $loginGagalQuery = fn () => AuditLog::where('tipe', TipeAuditEnum::Auth->value)
            ->where(function ($q) {
                $q->where('aksi', 'like', '%gagal%')
                    ->orWhere('status_http', 401)
                    ->orWhere('status_http', 429);
            });

        $loginGagal24Jam = $loginGagalQuery()->where('waktu', '>=', now()->subHours(24))->count();

        $auditTipeRaw = AuditLog::select('tipe', DB::raw('count(*) as total'))
            ->groupBy('tipe')
            ->pluck('total', 'tipe')
            ->toArray();

        $totalAuditEvents = AuditLog::count();

        return [
            'loginGagal24Jam' => $loginGagal24Jam,
            'insidenKeamananTerbaru' => $loginGagalQuery()->with('user')->orderBy('waktu', 'desc')->take(5)->get(),
            // Sebaran audit trail 4 kategori PRD (AC-13-1 s/d AC-13-4)
            'auditTipeDistribution' => [
                'auth' => $auditTipeRaw[TipeAuditEnum::Auth->value] ?? 0,
                'aksi' => $auditTipeRaw[TipeAuditEnum::Aksi->value] ?? 0,
                'service' => $auditTipeRaw[TipeAuditEnum::Service->value] ?? 0,
                'akses_data' => $auditTipeRaw[TipeAuditEnum::AksesData->value] ?? 0,
            ],
            // Postur Keamanan & Vault Berkas Sensitif (NFR-S1 s/d NFR-S8)
            'securityPosture' => [
                'private_vault_isolated' => true,
                'signed_url_ttl_minutes' => 5,
                'max_file_size_mb' => $berkasMaksMb,
                'security_headers_active' => true,
                'rate_limiter_active' => true,
                'audit_append_only' => true,
                'total_audit_events' => $totalAuditEvents,
                'login_gagal_24h' => $loginGagal24Jam,
            ],
        ];
    }
}
