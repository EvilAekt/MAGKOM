<?php

declare(strict_types=1);

namespace App\Queries\Admin\Kuota;

use App\Enums\RoleEnum;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class KuotaIndexQuery
{
    /**
     * Membangun seluruh data teragregasi untuk halaman kontrol kuota admin.
     *
     * @return array{
     *     bulanList: Collection<int, BulanMagang>,
     *     selectedBulan: ?BulanMagang,
     *     jurusanList: Collection<int, Jurusan>,
     *     bidangList: \Illuminate\Support\Collection<int, array<string, mixed>>,
     *     bidangJsonData: \Illuminate\Support\Collection<int, array<string, mixed>>,
     *     totalKapasitas: int,
     *     totalTerisi: int,
     *     globalPersen: int,
     *     isPendaftaranBuka: bool,
     *     isDraft: bool,
     *     isTerjadwal: bool,
     *     sisaHariTutup: int
     * }
     */
    public function getIndexData(int $selectedBulanId = 0): array
    {
        $bulanList = BulanMagang::orderBy('bulan_mulai', 'asc')
            ->get();

        if ($selectedBulanId === 0) {
            $selectedBulanId = (int) (($bulanList->firstWhere('aktif', true) ?? $bulanList->first())?->id ?? 0);
        }

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
                'alias' => 'Aplikasi Informatika (EGOV)',
                'inisial' => 'A',
                'bg_color' => '#EEF2FF',
                'text_color' => '#4F46E5',
                'durasi' => '1–6 bulan',
            ],
            'Informasi dan Komunikasi Publik (IKP)' => [
                'alias' => 'Informasi & Komunikasi Publik (IKP)',
                'inisial' => 'IKP',
                'bg_color' => '#FDF2F8',
                'text_color' => '#DB2777',
                'durasi' => '1–6 bulan',
            ],
            'Infrastruktur Teknologi Informasi (ITI)' => [
                'alias' => 'Infrastruktur TI & Persandian (ITI)',
                'inisial' => 'ITI',
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
            $persen = $kapasitas > 0 ? (int) min(100, round(($terisi / $kapasitas) * 100)) : 0;

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
        $globalPersen = $totalKapasitas > 0 ? (int) min(100, round(($totalTerisi / $totalKapasitas) * 100)) : 0;

        // Compute registration status flags (draft, scheduled, open, closed) and remaining open days
        $isDraft = $selectedBulan ? ! $selectedBulan->aktif : false;
        $isPendaftaranBuka = false;
        $isTerjadwal = false;
        $sisaHariTutup = 0;

        if ($selectedBulan && $selectedBulan->aktif && $selectedBulan->tgl_buka && $selectedBulan->tgl_tutup) {
            $now = now();
            $tglBuka = Carbon::parse($selectedBulan->tgl_buka)->startOfDay();
            $tglTutup = Carbon::parse($selectedBulan->tgl_tutup)->endOfDay();
            $isPendaftaranBuka = $now->between($tglBuka, $tglTutup);
            $isTerjadwal = ! $isPendaftaranBuka && $tglBuka->isFuture();

            if ($tglTutup->isFuture()) {
                $sisaHariTutup = (int) $now->diffInDays($tglTutup, false);
            }
        }

        // Siapkan mapping JSON bidang untuk interaksi real-time di modal
        $bidangJsonData = $bidangList->map(function ($b) {
            $pembimbingCount = max(2, $b['pembimbing_count']);
            $kapasitasBimbingan = $pembimbingCount * 6;
            $kuotaTerpasang = (int) $b['kapasitas'];
            $sisaKapasitas = max(0, $kapasitasBimbingan - $kuotaTerpasang);

            return [
                'id' => $b['id'],
                'nama' => $b['nama'],
                'nama_lengkap' => $b['nama_lengkap'],
                'pembimbing_count' => $pembimbingCount,
                'kapasitas_bimbingan' => $kapasitasBimbingan,
                'kuota_terpasang' => $kuotaTerpasang,
                'sisa_kapasitas' => $sisaKapasitas,
                'jurusans' => $b['jurusan_list']->map(fn ($j) => [
                    'id' => $j->id,
                    'nama' => $j->nama_jurusan,
                    'kategori' => $j->kategori,
                ])->values(),
                'jenjang' => $b['jenjang_badges'],
            ];
        })->values();

        return [
            'bulanList' => $bulanList,
            'selectedBulan' => $selectedBulan,
            'jurusanList' => $jurusanList,
            'bidangList' => $bidangList,
            'bidangJsonData' => $bidangJsonData,
            'totalKapasitas' => $totalKapasitas,
            'totalTerisi' => $totalTerisi,
            'globalPersen' => $globalPersen,
            'isPendaftaranBuka' => $isPendaftaranBuka,
            'isDraft' => $isDraft,
            'isTerjadwal' => $isTerjadwal,
            'sisaHariTutup' => $sisaHariTutup,
        ];
    }
}
