<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Enums\StatusKetersediaanEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\SuperAdmin\UpdateLandingCmsRequest;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\User;
use App\Services\BulanStatusService;
use App\Services\LandingCmsService;
use App\Services\LandingPage\LandingPageDocumentService;
use App\Services\LandingPage\LandingPageRenderer;
use App\Support\Pengaturan;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LandingCmsController extends Controller
{
    public function __construct(
        protected LandingCmsService $cmsService
    ) {}

    /**
     * Menampilkan antarmuka CMS pengelolaan Landing Page (Task E & Staging Sandbox).
     */
    public function index(Request $request): View|RedirectResponse
    {
        if ($request->boolean('redirect_to_canvas') || (! $request->has('legacy') && ! app()->runningUnitTests())) {
            return redirect()->route('superadmin.site-editor.index')
                ->with('info', 'CMS Pengelolaan Konten Publik kini terpusat sepenuhnya di Canvas Editor.');
        }
        $draftMeta = $this->cmsService->getDraftMetadata();
        $cmsData = $this->cmsService->getCmsData();

        return view('superadmin.landing.index', [
            'cmsData' => $cmsData,
            'draftMeta' => $draftMeta,
        ]);
    }

    /**
     * Menyimpan perubahan konten CMS berdasarkan bagian (Section), mendukung simpan draf dan publikasi langsung.
     */
    public function update(UpdateLandingCmsRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        $section = (string) $request->input('section', 'hero');
        $actionType = (string) $request->input('action_type', 'publish');

        $result = $this->cmsService->updateSection(
            section: $section,
            validated: $request->validated(),
            request: $request,
            user: $user,
            actionType: $actionType
        );

        if ($result['type'] === 'back') {
            return back()->withInput()->with($result['status'], $result['message']);
        }

        return redirect()->route($result['route'] ?? 'superadmin.landing.index', $result['params'] ?? [])
            ->with($result['status'], $result['message']);
    }

    /**
     * Mempublikasikan seluruh draf kontainer ke live publik.
     */
    public function publishDraft(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        $count = $this->cmsService->publishDraft($user);

        return redirect()->route('superadmin.landing.index')
            ->with('success', "Berhasil mempublikasikan {$count} elemen draf ke versi live publik.");
    }

    /**
     * Membuang (discard) seluruh draf kontainer dan mengembalikan editor ke versi live.
     */
    public function discardDraft(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        $this->cmsService->discardDraft($user);

        return redirect()->route('superadmin.landing.index')
            ->with('success', 'Seluruh draf perubahan berhasil dibuang. Editor dikembalikan ke versi live saat ini.');
    }

    /**
     * Menampilkan Pratinjau Interaktif Penuh (Full Interactive Live Preview) Landing Page.
     */
    public function preview(Request $request, BulanStatusService $bulanStatusService): View
    {
        // Aktifkan mode pratinjau untuk request ini
        Pengaturan::setPreviewMode(true);

        $page = $request->query('page', 'landing');

        if ($page === 'peta-alur') {
            return view('public.peta-alur-interaktif', [
                'isPreview' => true,
                'editor_mode' => $request->boolean('editor_mode'),
            ]);
        }

        if ($page === 'kebijakan-privasi') {
            return view('public.kebijakan-privasi', [
                'isPreview' => true,
                'editor_mode' => $request->boolean('editor_mode'),
            ]);
        }

        $sekarang = CarbonImmutable::now('Asia/Jakarta');

        $semuaBulan = BulanMagang::with(['kuota' => function ($query) {
            $query->where('aktif', true);
        }])
            ->where('aktif', true)
            ->orderBy('bulan_mulai', 'asc')
            ->get();

        $bulanTampil = $semuaBulan->filter(function (BulanMagang $bulan) use ($sekarang, $bulanStatusService) {
            return $bulanStatusService->tampilDiLanding($bulan, $sekarang);
        })->values();

        $semuaJurusan = Jurusan::with('bidang')
            ->where('aktif', true)
            ->orderBy('kategori', 'asc')
            ->orderBy('nama_jurusan', 'asc')
            ->get();

        $jurusanSmk = $semuaJurusan->filter(fn (Jurusan $j) => $j->kategori === 'smk');
        $jurusanMahasiswa = $semuaJurusan->filter(fn (Jurusan $j) => $j->kategori === 'mahasiswa');

        $matriksKetersediaan = [];

        foreach ($semuaJurusan as $jurusan) {
            $matriksKetersediaan[$jurusan->id] = [];

            foreach ($bulanTampil as $bulan) {
                $kuota = $bulan->kuota->firstWhere('jurusan_id', $jurusan->id);
                $status = $bulanStatusService->status($bulan, $kuota, $sekarang);
                $sisaKuota = $kuota ? max(0, $kuota->jumlah - $kuota->terisi) : 0;
                $isWaitingList = ($status === StatusKetersediaanEnum::Tersedia && $sisaKuota === 0);

                $totalKuota = $kuota ? (int) $kuota->jumlah : 0;
                $terisiKuota = $kuota ? (int) $kuota->terisi : 0;
                $persenTerisi = $totalKuota > 0 ? (int) min(100, round(($terisiKuota / $totalKuota) * 100)) : 0;

                $matriksKetersediaan[$jurusan->id][$bulan->id] = [
                    'bulan' => $bulan,
                    'kuota' => $kuota,
                    'total_kuota' => $totalKuota,
                    'terisi' => $terisiKuota,
                    'persen_terisi' => $persenTerisi,
                    'status' => $status,
                    'sisa_kuota' => $sisaKuota,
                    'is_waiting_list' => $isWaitingList,
                    'dapat_mendaftar' => ($status === StatusKetersediaanEnum::Tersedia),
                ];
            }
        }

        // Ambil semua bidang aktif beserta relasi jurusan aktif
        $semuaBidang = Bidang::with(['jurusan' => function ($q) {
            $q->where('aktif', true)->orderBy('nama_jurusan', 'asc');
        }])
            ->where('aktif', true)
            ->orderBy('nama_bidang', 'asc')
            ->get();

        $bidangSmk = $semuaBidang->filter(fn (Bidang $b) => $b->jurusan->contains('kategori', 'smk'))->values();
        $bidangMahasiswa = $semuaBidang->filter(fn (Bidang $b) => $b->jurusan->contains('kategori', 'mahasiswa'))->values();

        $matriksBidang = [];
        foreach ($semuaBidang as $bidang) {
            $matriksBidang[$bidang->id] = [];
            foreach (['smk', 'mahasiswa'] as $jenjang) {
                $matriksBidang[$bidang->id][$jenjang] = [];
                $jurusanJenjang = $bidang->jurusan->filter(fn (Jurusan $j) => $j->kategori === $jenjang);
                $jurusanIds = $jurusanJenjang->pluck('id');

                foreach ($bulanTampil as $bulan) {
                    $kuotaList = $bulan->kuota->whereIn('jurusan_id', $jurusanIds);
                    $total = (int) $kuotaList->sum('jumlah');
                    $terisi = (int) $kuotaList->sum('terisi');
                    $sisa = max(0, $total - $terisi);
                    $persen = $total > 0 ? (int) min(100, round(($terisi / $total) * 100)) : 0;

                    $bulanStatus = $bulanStatusService->status($bulan, null, $sekarang);
                    $isAvailable = ($bulanStatus === StatusKetersediaanEnum::Tersedia);
                    $status = $isAvailable ? StatusKetersediaanEnum::Tersedia : StatusKetersediaanEnum::Tutup;

                    $jurusanDetail = [];
                    foreach ($jurusanJenjang as $j) {
                        $k = $bulan->kuota->firstWhere('jurusan_id', $j->id);
                        $jTotal = $k ? (int) $k->jumlah : 0;
                        $jTerisi = $k ? (int) $k->terisi : 0;
                        $jSisa = max(0, $jTotal - $jTerisi);
                        $jPersen = $jTotal > 0 ? (int) min(100, round(($jTerisi / $jTotal) * 100)) : 0;

                        if ($isAvailable && $jSisa > 0) {
                            $badgeClass = ($jTotal > 0 && ($jSisa / $jTotal) <= 0.34) ? 'status-hampir' : 'status-tersedia';
                            $badgeLabel = ($jTotal > 0 && ($jSisa / $jTotal) <= 0.34) ? 'Tinggal sedikit' : 'Tersedia';
                            $barClass = ($jTotal > 0 && ($jSisa / $jTotal) <= 0.34) ? 'bar-hampir' : 'bar-tersedia';
                        } elseif ($isAvailable && $jSisa === 0) {
                            $badgeClass = 'status-penuh';
                            $badgeLabel = 'Penuh';
                            $barClass = 'bar-penuh';
                        } else {
                            $badgeClass = 'status-penuh';
                            $badgeLabel = 'Tutup';
                            $barClass = 'bar-penuh';
                        }

                        $jurusanDetail[$j->id] = [
                            'id' => $j->id,
                            'nama' => $j->nama_jurusan,
                            'total' => $jTotal,
                            'terisi' => $jTerisi,
                            'sisa' => $jSisa,
                            'persen' => $jPersen,
                            'is_available' => $isAvailable,
                            'badge_label' => $badgeLabel,
                            'badge_class' => $badgeClass,
                            'bar_class' => $barClass,
                        ];
                    }

                    $matriksBidang[$bidang->id][$jenjang][$bulan->id] = [
                        'bulan' => $bulan,
                        'total_kuota' => $total,
                        'terisi' => $terisi,
                        'persen_terisi' => $persen,
                        'sisa_kuota' => $sisa,
                        'is_available' => $isAvailable,
                        'status' => $status,
                        'jurusan_list' => $jurusanJenjang,
                        'jurusan_detail' => $jurusanDetail,
                    ];
                }
            }
        }

        return view('public.landing', [
            'bulanTampil' => $bulanTampil,
            'semuaBidang' => $semuaBidang,
            'bidangSmk' => $bidangSmk,
            'bidangMahasiswa' => $bidangMahasiswa,
            'matriksBidang' => $matriksBidang,
            'jurusanSmk' => $jurusanSmk,
            'jurusanMahasiswa' => $jurusanMahasiswa,
            'matriksKetersediaan' => $matriksKetersediaan,
            'sekarang' => $sekarang,
            'isPreview' => true,
            'document' => app(LandingPageDocumentService::class)->getDocument(true),
            'renderer' => app(LandingPageRenderer::class),
        ]);
    }

    /**
     * Menghapus foto banner kustom dan kembali ke foto default sistem.
     */
    public function destroyFoto(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        $this->cmsService->destroyFoto($user);

        return redirect()->route('superadmin.landing.index', ['tab' => 'hero'])
            ->with('success', 'Foto banner berhasil direset ke gambar bawaan sistem.');
    }

    /**
     * Menghapus dokumen PDF kustom dan kembali ke generator PDF bawaan sistem.
     */
    public function destroyPanduan(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        $this->cmsService->destroyPanduan($user);

        return redirect()->route('superadmin.landing.index', ['tab' => 'hero'])
            ->with('success', 'Dokumen panduan berhasil direset ke generator PDF resmi bawaan sistem.');
    }
}
