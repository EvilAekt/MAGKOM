<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\RoleEnum;
use App\Enums\StatusPendaftaranEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\TipeAuditEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\KuotaRequest;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\Kuota;
use App\Models\Pendaftaran;
use App\Models\Penempatan;
use App\Services\AuditService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KuotaController extends Controller
{
    public function __construct(
        protected AuditService $auditService
    ) {}

    public function index(Request $request): View
    {
        $bulanList = BulanMagang::where('aktif', true)
            ->orderBy('bulan_mulai', 'asc')
            ->get();

        $selectedBulanId = $request->integer('bulan_magang_id', $bulanList->first()?->id ?? 0);
        $selectedBulan = BulanMagang::with(['kuota.jurusan'])->find($selectedBulanId);

        $jurusanList = Jurusan::where('aktif', true)
            ->orderBy('kategori', 'asc')
            ->orderBy('nama_jurusan', 'asc')
            ->get();

        // Ambil master bidang dan susun data kuota teragregasi per bidang
        $rawBidang = Bidang::where('aktif', true)
            ->with([
                'jurusan' => fn ($q) => $q->where('aktif', true)->orderBy('nama_jurusan'),
                'users' => fn ($q) => $q->where('role', RoleEnum::Pembimbing)->where('status_aktif', true),
            ])
            ->get();

        // Konfigurasi visual avatar & tema per bidang sesuai desain UI
        $themeMap = [
            'Aplikasi Informatika & E-Government (EGOV)' => [
                'alias' => 'Aplikasi Informatika',
                'inisial' => 'A',
                'bg_color' => '#EEF2FF',
                'text_color' => '#4F46E5',
                'durasi' => '1–6 bulan',
            ],
            'Informasi dan Komunikasi Publik (IKP)' => [
                'alias' => 'Statistik',
                'inisial' => 'S',
                'bg_color' => '#FDF2F8',
                'text_color' => '#DB2777',
                'durasi' => '1–6 bulan',
            ],
            'Infrastruktur Teknologi Informasi (ITI)' => [
                'alias' => 'Persandian',
                'inisial' => 'P',
                'bg_color' => '#ECFDF5',
                'text_color' => '#059669',
                'durasi' => '3–6 bulan',
            ],
            'Sekretariat Dinas' => [
                'alias' => 'Sekretariat',
                'inisial' => 'S',
                'bg_color' => '#FFFBEB',
                'text_color' => '#D97706',
                'durasi' => '1–6 bulan',
            ],
        ];

        $bidangList = $rawBidang->map(function ($bidang) use ($selectedBulan, $themeMap) {
            $nama = $bidang->nama_bidang;
            $theme = $themeMap[$nama] ?? null;

            if (! $theme) {
                if (stripos($nama, 'Aplikasi') !== false || stripos($nama, 'Aptika') !== false) {
                    $theme = $themeMap['Aplikasi Informatika & E-Government (EGOV)'];
                } elseif (stripos($nama, 'Statistik') !== false || stripos($nama, 'IKP') !== false || stripos($nama, 'Informasi') !== false) {
                    $theme = $themeMap['Informasi dan Komunikasi Publik (IKP)'];
                } elseif (stripos($nama, 'Persandian') !== false || stripos($nama, 'ITI') !== false || stripos($nama, 'Infrastruktur') !== false) {
                    $theme = $themeMap['Infrastruktur Teknologi Informasi (ITI)'];
                } elseif (stripos($nama, 'Sekretariat') !== false) {
                    $theme = $themeMap['Sekretariat Dinas'];
                } else {
                    $theme = [
                        'alias' => $nama,
                        'inisial' => strtoupper(substr($nama, 0, 1)),
                        'bg_color' => '#EFF6FF',
                        'text_color' => '#2563EB',
                        'durasi' => '1–6 bulan',
                    ];
                }
            }

            $jurusanInBidang = $bidang->jurusan;
            $jurusanIds = $jurusanInBidang->pluck('id')->toArray();

            // Hitung kuota yang terikat pada jurusan-jurusan di bawah bidang ini
            $kuotaRows = $selectedBulan
                ? $selectedBulan->kuota->whereIn('jurusan_id', $jurusanIds)
                : collect();

            $kapasitas = (int) $kuotaRows->sum('jumlah');
            $terisi = (int) $kuotaRows->sum('terisi');
            $persen = $kapasitas > 0 ? min(100, round(($terisi / $kapasitas) * 100)) : 0;

            // Kategori jenjang yang bernaung di bidang ini
            $hasSmk = $jurusanInBidang->contains('kategori', 'smk');
            $hasMahasiswa = $jurusanInBidang->contains('kategori', 'mahasiswa');
            $jenjangBadges = [];
            if ($hasSmk) {
                $jenjangBadges[] = 'SMK';
            }
            if ($hasMahasiswa) {
                $jenjangBadges[] = 'D3';
                $jenjangBadges[] = 'S1';
            }
            if (empty($jenjangBadges)) {
                $jenjangBadges = ['SMK', 'D3', 'S1'];
            }

            // Kapasitas pembimbing
            $pembimbingCount = $bidang->users->count();
            $kapasitasBimbingan = max(10, $pembimbingCount * 3);

            return [
                'model' => $bidang,
                'id' => $bidang->id,
                'nama' => $theme['alias'],
                'nama_lengkap' => $nama,
                'inisial' => $theme['inisial'],
                'bg_color' => $theme['bg_color'],
                'text_color' => $theme['text_color'],
                'durasi' => $theme['durasi'],
                'jurusan_list' => $jurusanInBidang,
                'jurusan_names' => $jurusanInBidang->pluck('nama_jurusan')->implode(', ') ?: 'Seluruh Kejuruan Terdaftar',
                'jenjang_badges' => $jenjangBadges,
                'kapasitas' => $kapasitas,
                'terisi' => $terisi,
                'persen' => $persen,
                'kuota_rows' => $kuotaRows,
                'pembimbing_count' => $pembimbingCount,
                'kapasitas_bimbingan' => $kapasitasBimbingan,
            ];
        });

        // Hitung total kapasitas & terisi
        $totalKapasitas = $selectedBulan ? (int) $selectedBulan->kuota->sum('jumlah') : 0;
        $totalTerisi = $selectedBulan ? (int) $selectedBulan->kuota->sum('terisi') : 0;
        $globalPersen = $totalKapasitas > 0 ? min(100, round(($totalTerisi / $totalKapasitas) * 100)) : 0;

        // Hitung status buka & sisa hari pendaftaran
        $isPendaftaranBuka = false;
        $sisaHariTutup = 0;
        if ($selectedBulan && $selectedBulan->tgl_buka && $selectedBulan->tgl_tutup) {
            $now = now();
            $tglBuka = Carbon::parse($selectedBulan->tgl_buka)->startOfDay();
            $tglTutup = Carbon::parse($selectedBulan->tgl_tutup)->endOfDay();
            $isPendaftaranBuka = $now->between($tglBuka, $tglTutup);
            if ($tglTutup->isFuture()) {
                $sisaHariTutup = (int) $now->diffInDays($tglTutup, false);
            }
        }

        return view('admin.kuota.index', [
            'bulanList' => $bulanList,
            'selectedBulan' => $selectedBulan,
            'jurusanList' => $jurusanList,
            'bidangList' => $bidangList,
            'totalKapasitas' => $totalKapasitas,
            'totalTerisi' => $totalTerisi,
            'globalPersen' => $globalPersen,
            'isPendaftaranBuka' => $isPendaftaranBuka,
            'sisaHariTutup' => $sisaHariTutup,
        ]);
    }

    public function store(KuotaRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $kuota = Kuota::create($validated);

        $this->auditService->catat(
            tipe: TipeAuditEnum::Aksi,
            tabel: 'kuota',
            idData: $kuota->id,
            nilaiBaru: $kuota->toArray(),
            detail: ['aksi' => 'Menambahkan kuota jurusan pada bulan magang']
        );

        return redirect()->route('admin.kuota.index', ['bulan_magang_id' => $kuota->bulan_magang_id])
            ->with('success', 'Kuota jurusan berhasil ditambahkan.');
    }

    public function update(KuotaRequest $request, Kuota $kuota): RedirectResponse
    {
        $nilaiLama = $kuota->toArray();
        $validated = $request->validated();

        $kuota->update($validated);

        $this->auditService->catat(
            tipe: TipeAuditEnum::Aksi,
            tabel: 'kuota',
            idData: $kuota->id,
            nilaiLama: $nilaiLama,
            nilaiBaru: $kuota->toArray(),
            detail: ['aksi' => 'Memperbarui alokasi kuota jurusan']
        );

        return redirect()->route('admin.kuota.index', ['bulan_magang_id' => $kuota->bulan_magang_id])
            ->with('success', 'Alokasi kuota jurusan berhasil diperbarui.');
    }

    public function toggleAktif(Request $request, Kuota $kuota): RedirectResponse
    {
        $targetAktif = ! $kuota->aktif;

        // Penegakan Aturan Bisnis BR-12 & AC-05-6 jika hendak menonaktifkan kuota
        if (! $targetAktif) {
            $hasActivePendaftaran = Pendaftaran::where('kuota_id', $kuota->id)
                ->whereIn('status', [
                    StatusPendaftaranEnum::Diajukan,
                    StatusPendaftaranEnum::WaitingList,
                    StatusPendaftaranEnum::PerluPerbaikan,
                    StatusPendaftaranEnum::BerkasBelumLengkap,
                    StatusPendaftaranEnum::Diterima,
                ])
                ->exists();

            if ($hasActivePendaftaran) {
                return back()->with('error', 'Penonaktifan ditolak (BR-12, AC-05-6): Kuota jurusan masih memiliki pendaftaran aktif.');
            }

            $hasActivePenempatan = Penempatan::where('kuota_id', $kuota->id)
                ->where('status', StatusPenempatanEnum::Aktif)
                ->exists();

            if ($hasActivePenempatan) {
                return back()->with('error', 'Penonaktifan ditolak (BR-12, AC-05-6): Kuota jurusan masih memiliki penempatan peserta aktif.');
            }
        }

        $nilaiLama = ['aktif' => $kuota->aktif];
        $kuota->aktif = $targetAktif;
        $kuota->save();

        $this->auditService->catat(
            tipe: TipeAuditEnum::Aksi,
            tabel: 'kuota',
            idData: $kuota->id,
            nilaiLama: $nilaiLama,
            nilaiBaru: ['aktif' => $kuota->aktif],
            detail: ['aksi' => 'Mengubah status aktif kuota jurusan']
        );

        $statusText = $kuota->aktif ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('admin.kuota.index', ['bulan_magang_id' => $kuota->bulan_magang_id])
            ->with('success', "Kuota jurusan berhasil {$statusText}.");
    }

    /**
     * Membuat bulan magang baru langsung dari modal halaman kuota.
     */
    public function storeBulan(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'bulan_mulai' => ['required', 'date'],
            'tgl_buka' => ['required', 'date'],
            'tgl_tutup' => ['required', 'date', 'after_or_equal:tgl_buka'],
        ], [
            'bulan_mulai.required' => 'Bulan mulai magang wajib diisi.',
            'tgl_buka.required' => 'Tanggal buka pendaftaran wajib diisi.',
            'tgl_tutup.required' => 'Tanggal tutup pendaftaran wajib diisi.',
            'tgl_tutup.after_or_equal' => 'Tanggal tutup harus setelah atau sama dengan tanggal buka.',
        ]);

        $bulan = BulanMagang::create([
            'bulan_mulai' => $validated['bulan_mulai'],
            'tgl_buka' => $validated['tgl_buka'],
            'tgl_tutup' => $validated['tgl_tutup'],
            'aktif' => true,
        ]);

        $this->auditService->catat(
            tipe: TipeAuditEnum::Aksi,
            tabel: 'bulan_magang',
            idData: $bulan->id,
            nilaiBaru: $bulan->toArray(),
            detail: ['aksi' => 'Membuat bulan magang baru dari kontrol kuota']
        );

        return redirect()->route('admin.kuota.index', ['bulan_magang_id' => $bulan->id])
            ->with('success', 'Bulan magang baru berhasil dibuat.');
    }

    /**
     * Memperbarui jadwal bulan magang aktif langsung dari modal halaman kuota.
     */
    public function updateBulan(Request $request, BulanMagang $bulanMagang): RedirectResponse
    {
        $validated = $request->validate([
            'bulan_mulai' => ['required', 'date'],
            'tgl_buka' => ['required', 'date'],
            'tgl_tutup' => ['required', 'date', 'after_or_equal:tgl_buka'],
        ], [
            'bulan_mulai.required' => 'Bulan mulai magang wajib diisi.',
            'tgl_buka.required' => 'Tanggal buka pendaftaran wajib diisi.',
            'tgl_tutup.required' => 'Tanggal tutup pendaftaran wajib diisi.',
            'tgl_tutup.after_or_equal' => 'Tanggal tutup harus setelah atau sama dengan tanggal buka.',
        ]);

        $nilaiLama = $bulanMagang->toArray();
        $bulanMagang->update($validated);

        $this->auditService->catat(
            tipe: TipeAuditEnum::Aksi,
            tabel: 'bulan_magang',
            idData: $bulanMagang->id,
            nilaiLama: $nilaiLama,
            nilaiBaru: $bulanMagang->toArray(),
            detail: ['aksi' => 'Memperbarui jadwal bulan magang dari kontrol kuota']
        );

        return redirect()->route('admin.kuota.index', ['bulan_magang_id' => $bulanMagang->id])
            ->with('success', 'Jadwal bulan magang berhasil diperbarui.');
    }

    /**
     * Memperbarui alokasi kuota per bidang (mengalokasikan ke jurusan-jurusan di bawahnya).
     */
    public function updateBidangKuota(Request $request, Bidang $bidang): RedirectResponse
    {
        $bulanMagangId = $request->integer('bulan_magang_id');
        $alokasi = $request->input('alokasi', []); // array of [jurusan_id => jumlah]

        if (! $bulanMagangId) {
            return back()->with('error', 'Bulan magang tidak valid.');
        }

        foreach ($alokasi as $jurusanId => $jumlah) {
            $jumlah = max(0, (int) $jumlah);
            $kuota = Kuota::firstOrNew([
                'bulan_magang_id' => $bulanMagangId,
                'jurusan_id' => (int) $jurusanId,
            ]);

            // Cegah penurunan di bawah peserta yang sudah diterima
            if ($kuota->exists && $jumlah < $kuota->terisi) {
                return back()->with('error', "Jumlah kuota untuk kejuruan tidak boleh lebih kecil dari peserta yang sudah diterima ({$kuota->terisi}).");
            }

            $kuota->jumlah = $jumlah;
            $kuota->aktif = true;
            $kuota->save();
        }

        $this->auditService->catat(
            tipe: TipeAuditEnum::Aksi,
            tabel: 'kuota',
            idData: $bidang->id,
            nilaiBaru: ['alokasi' => $alokasi],
            detail: ['aksi' => "Memperbarui kuota bidang {$bidang->nama_bidang}"]
        );

        return redirect()->route('admin.kuota.index', ['bulan_magang_id' => $bulanMagangId])
            ->with('success', "Alokasi kuota bidang {$bidang->nama_bidang} berhasil diperbarui.");
    }
}
