<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\Penempatan\AssignPesertaAction;
use App\Actions\Admin\Penempatan\BulkReassignPembimbingAction;
use App\Actions\Admin\Penempatan\CancelPenempatanAction;
use App\Actions\Admin\Penempatan\CreateManualPenempatanAction;
use App\Actions\Admin\Penempatan\ReassignPembimbingAction;
use App\Enums\JenjangEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusPendaftaranEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\StatusTugasEnum;
use App\Enums\TipeAuditEnum;
use App\Exports\Admin\Penempatan\RekapSuratDiterimaExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Penempatan\BatalkanPenempatanRequest;
use App\Http\Requests\Admin\Penempatan\BulkReassignPembimbingRequest;
use App\Http\Requests\Admin\Penempatan\PindahPembimbingRequest;
use App\Http\Requests\Admin\Penempatan\UploadSuratSelesaiRequest;
use App\Http\Requests\PenempatanManualRequest;
use App\Http\Requests\TetapkanPenempatanRequest;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\Kuota;
use App\Models\Pendaftaran;
use App\Models\Penempatan;
use App\Models\SlotKhusus;
use App\Models\User;
use App\Queries\Admin\Penempatan\PenempatanAntreanQuery;
use App\Queries\Admin\Penempatan\PenempatanIndexQuery;
use App\Services\Admin\Penempatan\SuratPenerimaanService;
use App\Services\Admin\Penempatan\SuratSelesaiService;
use App\Services\AuditService;
use App\Services\PlacementService;
use App\Services\SuratDiterimaPdfService;
use App\Services\XlsxExportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class PenempatanController extends Controller
{
    public function __construct(
        protected PlacementService $placementService,
        protected AuditService $auditService,
        protected XlsxExportService $xlsxExportService,
        protected PenempatanIndexQuery $indexQuery,
        protected PenempatanAntreanQuery $antreanQuery,
        protected AssignPesertaAction $assignPesertaAction,
        protected CreateManualPenempatanAction $createManualAction,
        protected ReassignPembimbingAction $reassignAction,
        protected BulkReassignPembimbingAction $bulkReassignAction,
        protected CancelPenempatanAction $cancelAction,
        protected SuratPenerimaanService $suratPenerimaanService,
        protected RekapSuratDiterimaExport $rekapExport,
        protected ?SuratSelesaiService $suratSelesaiService = null
    ) {
        $this->suratSelesaiService = $suratSelesaiService ?? app(SuratSelesaiService::class);
    }

    public function index(Request $request): View
    {
        $penempatanList = $this->indexQuery->getPaginated($request);

        $pembimbingList = User::where('role', RoleEnum::Pembimbing)
            ->where('status_aktif', true)
            ->with('bidang')
            ->withCount(['penempatanBimbingan as mentee_aktif_count' => function ($q) {
                $q->where('status', StatusPenempatanEnum::Aktif);
            }])
            ->orderBy('nama')
            ->get();

        $rawTipe = strtolower(trim((string) $request->input('tipe', 'semua')));
        $activeTipe = match ($rawTipe) {
            'individu', 'mandiri' => 'individu',
            'kelompok', 'tim' => 'kelompok',
            default => 'semua',
        };

        $activeTab = $request->input('tab', 'sudah');
        if ($activeTipe === 'semua' || ! in_array($activeTab, ['sudah', 'belum'], true)) {
            $activeTab = 'sudah';
        }

        $antreanBelumDitempatkan = $this->antreanQuery->getPaginated($request, 5);
        $totalBelumDitempatkan = $this->antreanQuery->getTotalBelumDitempatkan();
        $sudahCategoryCounts = $this->indexQuery->getCategoryCounts();
        $belumCategoryCounts = $this->antreanQuery->getCategoryCounts();

        $categoryStats = [
            'semua' => $sudahCategoryCounts['semua'],
            'individu' => $sudahCategoryCounts['individu'] + $belumCategoryCounts['individu'],
            'kelompok' => $sudahCategoryCounts['kelompok'] + $belumCategoryCounts['kelompok'],
        ];

        $tabStats = [
            'sudah' => $sudahCategoryCounts[$activeTipe] ?? $sudahCategoryCounts['semua'],
            'belum' => $belumCategoryCounts[$activeTipe] ?? $belumCategoryCounts['semua'],
        ];

        $bulanList = BulanMagang::orderBy('bulan_mulai', 'desc')->get();
        $slotKhususList = SlotKhusus::where('aktif', true)->orderBy('bulan_mulai')->get();
        $bidangList = Bidang::cachedAktif();
        $jurusanList = Jurusan::cachedAktif();
        $kuotaList = Kuota::with(['bulanMagang', 'jurusan'])->where('aktif', true)->get();
        $pesertaList = User::where('role', RoleEnum::Peserta)
            ->where('status_aktif', true)
            ->whereDoesntHave('penempatanPeserta', function ($q) {
                $q->where('status', StatusPenempatanEnum::Aktif);
            })
            ->with(['profil' => function ($q) {
                $q->select('id', 'user_id', 'institusi');
            }])
            ->select('id', 'nama', 'email')
            ->orderBy('nama')
            ->get();

        $penempatanStats = $this->indexQuery->getStatistics($totalBelumDitempatkan);

        return view('admin.penempatan.index', [
            'penempatanList' => $penempatanList,
            'statusEnum' => StatusPenempatanEnum::cases(),
            'pembimbingList' => $pembimbingList,
            'slotKhususList' => $slotKhususList,
            'bidangList' => $bidangList,
            'jurusanList' => $jurusanList,
            'kuotaList' => $kuotaList,
            'pesertaList' => $pesertaList,
            'penempatanStats' => $penempatanStats,
            'antreanBelumDitempatkan' => $antreanBelumDitempatkan,
            'bulanList' => $bulanList,
            'activeTab' => $activeTab,
            'activeTipe' => $activeTipe,
            'categoryStats' => $categoryStats,
            'tabStats' => $tabStats,
        ]);
    }

    /**
     * Menetapkan pembimbing dan bidang dinas untuk peserta yang telah diterima (Tahap 2 Penempatan).
     */
    public function tetapkan(TetapkanPenempatanRequest $request): RedirectResponse
    {
        $this->authorize('manage', Penempatan::class);

        $admin = $request->user();
        $pendaftaran = Pendaftaran::findOrFail($request->integer('pendaftaran_id'));
        $pembimbing = User::findOrFail($request->integer('pembimbing_id'));
        $bidang = $request->filled('bidang_id')
            ? Bidang::find($request->integer('bidang_id'))
            : ($pendaftaran->kuota?->jurusan?->bidang?->first() ?? $pembimbing->bidang);

        try {
            $this->assignPesertaAction->execute($pendaftaran, $admin, $pembimbing, $bidang);

            $pesertaNama = $pendaftaran->user?->nama ?? 'Peserta';
            $pembimbingNama = $pembimbing->nama;

            return redirect()->route('admin.penempatan.index')
                ->with('success', "Penetapan pembimbing lapangan {$pembimbingNama} untuk {$pesertaNama} berhasil disimpan.");
        } catch (\Throwable $e) {
            return redirect()->route('admin.penempatan.index', ['tab' => 'belum'])
                ->with('error', 'Gagal menetapkan penempatan: '.$e->getMessage());
        }
    }

    /**
     * Menampilkan detail peserta antrean lolos berkas sebelum penetapan pembimbing.
     */
    public function showAntrean(Pendaftaran $pendaftaran): View|RedirectResponse
    {
        $this->authorize('viewAny', Penempatan::class);

        if ($pendaftaran->status !== StatusPendaftaranEnum::Diterima) {
            return redirect()->route('admin.penempatan.index', ['tab' => 'belum'])
                ->with('error', 'Detail antrean hanya tersedia untuk pendaftaran yang sudah diterima.');
        }

        $existingPenempatan = $pendaftaran->penempatan()->first();
        if ($existingPenempatan) {
            return redirect()->route('admin.penempatan.show', $existingPenempatan->id)
                ->with('info', 'Peserta ini sudah memiliki penempatan aktif.');
        }

        $pendaftaran->load([
            'user.profil.jurusan',
            'bulanMagang',
            'kuota.jurusan.bidang',
            'kelompokMagang.ketua.profil.jurusan',
            'kelompokMagang.anggota.user.profil.jurusan',
            'dokumen',
        ]);

        $this->auditService->catat(
            tipe: TipeAuditEnum::AksesData,
            tabel: 'pendaftaran',
            idData: $pendaftaran->id,
            nilaiLama: null,
            nilaiBaru: null,
            detail: [
                'aksi' => 'Melihat detail peserta antrean penempatan',
                'peserta_id' => $pendaftaran->user_id,
            ],
            user: request()->user()?->id
        );

        $bidangPeserta = $pendaftaran->kuota?->jurusan?->bidang?->first();

        $pembimbingQuery = User::where('role', RoleEnum::Pembimbing)
            ->where('status_aktif', true)
            ->with('bidang')
            ->withCount(['penempatanBimbingan as mentee_aktif_count' => function ($q) {
                $q->where('status', StatusPenempatanEnum::Aktif);
            }])
            ->orderBy('nama');

        if ($bidangPeserta) {
            $pembimbingBidang = (clone $pembimbingQuery)
                ->where('bidang_id', $bidangPeserta->id)
                ->get();

            $pembimbingList = $pembimbingBidang->isNotEmpty()
                ? $pembimbingBidang
                : $pembimbingQuery->get();
        } else {
            $pembimbingList = $pembimbingQuery->get();
        }

        return view('admin.penempatan.antrean-show', [
            'pendaftaran' => $pendaftaran,
            'pembimbingList' => $pembimbingList,
            'anggotaTimProfil' => $this->buildAnggotaTimProfilList($pendaftaran),
        ]);
    }

    /**
     * Menampilkan detail lengkap penempatan peserta individual (A9).
     */
    public function show(Penempatan $penempatan): View
    {
        $this->authorize('view', $penempatan);

        $penempatan->load([
            'peserta.profil.jurusan',
            'pembimbing',
            'bidang',
            'slotKhusus',
            'kuota.jurusan',
            'tugas.progresLogs',
            'logbooks',
            'pendaftaran.dokumen',
            'pendaftaran.kelompokMagang.ketua.profil.jurusan',
            'pendaftaran.kelompokMagang.anggota.user.profil.jurusan',
        ]);

        $this->auditService->catat(
            tipe: TipeAuditEnum::AksesData,
            tabel: 'penempatan',
            idData: $penempatan->id,
            nilaiLama: null,
            nilaiBaru: null,
            detail: [
                'aksi' => 'Melihat detail individual penempatan peserta (A9)',
                'peserta_id' => $penempatan->peserta_id,
            ],
            user: request()->user()?->id
        );

        $pembimbingList = User::where('role', RoleEnum::Pembimbing)
            ->where('status_aktif', true)
            ->withCount(['penempatanBimbingan as mentee_aktif_count' => function ($q) {
                $q->where('status', StatusPenempatanEnum::Aktif);
            }])
            ->orderBy('nama')
            ->get();

        $tugasCount = $penempatan->tugas->count();
        $tugasSelesaiCount = $penempatan->tugas->filter(function ($t) {
            return $t->status === StatusTugasEnum::Selesai;
        })->count();

        $logbookCount = $penempatan->logbooks->count();

        $anggotaTimProfil = ($penempatan->isKelompok() && $penempatan->pendaftaran)
            ? $this->buildAnggotaTimProfilList($penempatan->pendaftaran)
            : collect();

        return view('admin.penempatan.show', [
            'penempatan' => $penempatan,
            'pembimbingList' => $pembimbingList,
            'tugasCount' => $tugasCount,
            'tugasSelesaiCount' => $tugasSelesaiCount,
            'logbookCount' => $logbookCount,
            'anggotaTimProfil' => $anggotaTimProfil,
        ]);
    }

    /**
     * Alur penempatan manual peserta (SuperAdmin & Admin) (F-06, F-07, AC-07-3).
     */
    public function storeManual(PenempatanManualRequest $request): RedirectResponse
    {
        $this->authorize('manage', Penempatan::class);

        $admin = $request->user();
        $mode = $request->input('mode', 'terdaftar');
        $jalur = $request->input('jalur', 'khusus');
        $pembimbing = User::findOrFail($request->integer('pembimbing_id'));
        $bidang = $request->filled('bidang_id') ? Bidang::find($request->integer('bidang_id')) : null;

        try {
            if ($mode === 'baru') {
                $penempatan = $this->createManualAction->executeBaru(
                    userData: [
                        'nama' => (string) $request->input('nama'),
                        'email' => (string) $request->input('email'),
                    ],
                    profilData: [
                        'nim_nisn' => (string) $request->input('nim_nisn'),
                        'institusi' => (string) $request->input('institusi'),
                        'jurusan_id' => $request->filled('jurusan_id') ? $request->integer('jurusan_id') : null,
                        'jenjang' => (string) $request->input('jenjang'),
                        'no_hp' => $request->input('no_hp'),
                    ],
                    pembimbing: $pembimbing,
                    bidang: $bidang,
                    bulanMulai: (string) $request->input('bulan_mulai'),
                    durasiBulan: $request->integer('durasi_bulan'),
                    jalur: (string) $jalur,
                    slotKhususId: $request->filled('slot_khusus_id') ? $request->integer('slot_khusus_id') : null,
                    kuotaId: $request->filled('kuota_id') ? $request->integer('kuota_id') : null,
                    admin: $admin
                );
            } else {
                $peserta = User::findOrFail($request->integer('peserta_id'));
                $slotKhusus = SlotKhusus::findOrFail($request->integer('slot_khusus_id'));

                $penempatan = $this->createManualAction->executeTerdaftar(
                    peserta: $peserta,
                    slotKhusus: $slotKhusus,
                    pembimbing: $pembimbing,
                    bidang: $bidang,
                    bulanMulai: (string) $request->input('bulan_mulai'),
                    durasiBulan: $request->integer('durasi_bulan'),
                    admin: $admin
                );
            }

            $redirect = redirect()->route('admin.penempatan.index');
            if ($mode === 'baru') {
                $plainPassword = $penempatan->plain_password ?? 'Magkom2026!';
                $namaPeserta = $penempatan->peserta?->nama ?? 'Peserta';
                $emailPeserta = $penempatan->peserta?->email ?? '';

                return $redirect->with('success', "Penempatan peserta {$namaPeserta} berhasil diproses. Akun peserta telah aktif dibuat.")
                    ->with('akun_baru_dibuat', true)
                    ->with('nama_peserta', $namaPeserta)
                    ->with('email_peserta', $emailPeserta)
                    ->with('password_baru', $plainPassword);
            }

            return $redirect->with('success', 'Penempatan peserta berhasil diproses secara manual.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memproses penempatan manual: '.$e->getMessage());
        }
    }

    /**
     * Mengunduh dokumen resmi Surat Keterangan Diterima Magang PDF (AC-16-1, F-16).
     */
    public function unduhSuratDiterima(Penempatan $penempatan, SuratDiterimaPdfService $pdfService): Response
    {
        $this->authorize('view', $penempatan);

        return $this->suratPenerimaanService->downloadResponse($penempatan, $pdfService);
    }

    /**
     * Menyajikan pratinjau dokumen resmi Surat Keterangan Diterima Magang PDF secara inline stream (AC-16-1, F-16).
     */
    public function pratinjauSuratDiterima(Penempatan $penempatan, SuratDiterimaPdfService $pdfService): Response
    {
        $this->authorize('view', $penempatan);

        return $this->suratPenerimaanService->streamResponse($penempatan, $pdfService);
    }

    /**
     * Memindahkan pembimbing peserta magang (AC-07-4).
     */
    public function pindahPembimbing(Penempatan $penempatan, PindahPembimbingRequest $request): RedirectResponse
    {
        $pembimbingBaru = User::findOrFail($request->integer('pembimbing_id'));

        try {
            $this->reassignAction->execute(
                penempatan: $penempatan,
                pembimbingBaru: $pembimbingBaru,
                admin: $request->user()
            );

            return redirect()->route('admin.penempatan.index')
                ->with('success', "Pembimbing berhasil dialihkan ke {$pembimbingBaru->nama}.");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Memindahkan pembimbing untuk banyak penempatan peserta sekaligus (Bulk Reassign) (AC-07-4, BR-13, A-12).
     */
    public function bulkReassignPembimbing(BulkReassignPembimbingRequest $request): RedirectResponse
    {
        $admin = $request->user();
        $pembimbingBaru = User::where('id', $request->integer('pembimbing_id'))
            ->where('role', RoleEnum::Pembimbing)
            ->where('status_aktif', true)
            ->firstOrFail();

        $ids = (array) $request->input('penempatan_ids');

        try {
            $count = $this->bulkReassignAction->execute($ids, $pembimbingBaru, $admin);

            return redirect()->route('admin.penempatan.index')
                ->with('success', "Sebanyak {$count} penempatan peserta berhasil dialihkan ke {$pembimbingBaru->nama}.");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Membatalkan penerimaan atau mencatat pengunduran diri peserta magang (AC-07-5, BR-07).
     */
    public function batalkan(Penempatan $penempatan, BatalkanPenempatanRequest $request): RedirectResponse
    {
        try {
            $this->cancelAction->execute(
                penempatan: $penempatan,
                admin: $request->user(),
                alasan: (string) $request->input('alasan')
            );

            return redirect()->route('admin.penempatan.index')
                ->with('success', 'Status penerimaan/penempatan peserta berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Ekspor rekapitulasi data surat keterangan diterima magang (F-16).
     * Mendukung format XLSX resmi bergaris rapi (default pada UI), CSV sanitasi, dan PDF.
     */
    public function exportRekapSuratDiterima(Request $request): Response
    {
        $this->authorize('export', Penempatan::class);

        $records = $this->indexQuery->buildQuery($request)->get();

        return $this->rekapExport->export($records, $request, $request->user());
    }

    /**
     * Mengunggah Surat Keterangan Selesai Magang dan mendistribusikannya ke seluruh anggota kelompok
     * jika peserta merupakan bagian dari pendaftaran kelompok.
     */
    public function uploadSuratSelesai(Penempatan $penempatan, UploadSuratSelesaiRequest $request): RedirectResponse
    {
        if ($penempatan->status !== StatusPenempatanEnum::Selesai) {
            return back()->withErrors(['file_surat' => 'Surat selesai magang hanya dapat diunggah jika status peserta telah dinyatakan selesai magang.']);
        }

        try {
            $service = $this->suratSelesaiService ?? app(SuratSelesaiService::class);
            $result = $service->unggahDanDistribusikan(
                penempatan: $penempatan,
                file: $request->file('file_surat'),
                admin: $request->user()
            );

            if ($result['is_kelompok']) {
                $pesan = "Surat keterangan selesai magang berhasil diunggah dan otomatis didistribusikan ke seluruh {$result['jumlah_penerima']} anggota tim (".($result['nama_kelompok'] ?? 'Tim').').';
            } else {
                $pesan = 'Surat keterangan selesai magang untuk peserta '.($penempatan->peserta?->nama ?? 'Peserta').' berhasil diunggah.';
            }

            return back()->with('success', $pesan);
        } catch (\Throwable $e) {
            return back()->withErrors(['file_surat' => 'Gagal mengunggah surat selesai: '.$e->getMessage()]);
        }
    }

    /**
     * Mengunduh file Surat Keterangan Selesai Magang yang telah diunggah oleh admin.
     */
    public function unduhSuratSelesai(Penempatan $penempatan): BinaryFileResponse
    {
        $this->authorize('view', $penempatan);

        $service = $this->suratSelesaiService ?? app(SuratSelesaiService::class);

        return $service->downloadResponse($penempatan);
    }

    /**
     * Menghapus berkas Surat Keterangan Selesai Magang yang keliru atau perlu ditarik kembali.
     */
    public function hapusSuratSelesai(Penempatan $penempatan): RedirectResponse
    {
        $this->authorize('manage', Penempatan::class);

        try {
            $service = $this->suratSelesaiService ?? app(SuratSelesaiService::class);
            $result = $service->hapusSuratSelesai(
                penempatan: $penempatan,
                admin: request()->user()
            );

            if ($result['is_kelompok']) {
                $pesan = "Berkas surat selesai magang berhasil dihapus untuk seluruh {$result['jumlah_terdampak']} anggota tim (".($result['nama_kelompok'] ?? 'Tim').').';
            } else {
                $pesan = 'Berkas surat selesai magang untuk peserta '.($penempatan->peserta?->nama ?? 'Peserta').' berhasil dihapus.';
            }

            return back()->with('success', $pesan);
        } catch (\Throwable $e) {
            return back()->withErrors(['file_surat' => 'Gagal menghapus surat selesai: '.$e->getMessage()]);
        }
    }

    /**
     * Menyusun koleksi terstruktur profil & biodata seluruh anggota tim (Ketua + Anggota).
     *
     * @return Collection<int, array<string, mixed>>
     */
    protected function buildAnggotaTimProfilList(Pendaftaran $pendaftaran): Collection
    {
        $kelompok = $pendaftaran->kelompokMagang;
        if (! $kelompok) {
            return collect();
        }

        $ketua = $kelompok->ketua ?? $pendaftaran->user;
        $ketuaProfil = $ketua?->profil;
        $defaultJurusan = $pendaftaran->kuota?->jurusan?->nama_jurusan ?? $ketuaProfil?->jurusan?->nama_jurusan ?? '-';

        $formatMember = function (
            string $key,
            bool $isKetua,
            ?User $memberUser,
            ?string $fallbackNama,
            ?string $fallbackNim,
            ?string $fallbackNoHp,
            ?string $fallbackEmail
        ) use ($ketuaProfil, $defaultJurusan): array {
            $profil = $memberUser?->profil;
            $rawNama = $memberUser?->nama ?? $fallbackNama ?? 'Peserta';
            $cleanNama = str_replace(' (Peserta Magang)', '', (string) $rawNama);
            $jenjangVal = $profil?->jenjang;
            $jenjangLabel = $jenjangVal instanceof JenjangEnum
                ? $jenjangVal->label()
                : ($jenjangVal ? strtoupper((string) $jenjangVal) : '-');

            $kelengkapan = $profil?->hitungKelengkapan() ?? [
                'persentase' => 0,
                'is_lengkap' => false,
            ];

            $kontakDarurat = '-';
            if ($profil?->nama_kontak_darurat) {
                $kontakDarurat = $profil->nama_kontak_darurat
                    .' ('.($profil->hubungan_kontak_darurat ?? 'Wali').')'
                    .' — '.($profil->no_kontak_darurat ?? '-');
            }

            return [
                'key' => $key,
                'user_id' => $memberUser?->id,
                'is_ketua' => $isKetua,
                'peran' => $isKetua ? 'Ketua Tim' : 'Anggota Tim',
                'nama' => $cleanNama,
                'email' => $memberUser?->email ?? $fallbackEmail ?? '-',
                'nim_nisn' => $profil?->nim_nisn ?? $fallbackNim ?? '-',
                'institusi' => $profil?->institusi ?? $ketuaProfil?->institusi ?? 'Institusi belum diisi',
                'jenjang_label' => $jenjangLabel,
                'jurusan_nama' => $profil?->jurusan?->nama_jurusan ?? $defaultJurusan,
                'no_hp' => $profil?->no_hp ?? $memberUser?->no_telp ?? $fallbackNoHp ?? '-',
                'nama_pembimbing_sekolah' => $profil?->nama_pembimbing_sekolah ?? '-',
                'alamat' => $profil?->alamat ?? '-',
                'kontak_darurat' => $kontakDarurat,
                'foto_url' => $profil?->foto_url ?? $memberUser?->foto_url,
                'kelengkapan_persen' => (int) ($kelengkapan['persentase'] ?? 0),
                'is_profil_lengkap' => (bool) ($kelengkapan['is_lengkap'] ?? false),
                'status_aktif' => (bool) ($memberUser?->status_aktif ?? false),
            ];
        };

        $items = collect([
            $formatMember(
                'ketua',
                true,
                $ketua,
                $ketua?->nama,
                $ketuaProfil?->nim_nisn,
                $ketuaProfil?->no_hp ?? $ketua?->no_telp,
                $ketua?->email
            ),
        ]);

        foreach ($kelompok->anggota as $anggota) {
            if ($ketua && (int) $anggota->user_id === (int) $ketua->id) {
                continue;
            }

            $items->push($formatMember(
                'anggota-'.$anggota->id,
                false,
                $anggota->user,
                $anggota->nama,
                $anggota->nim_nisn,
                $anggota->no_hp,
                $anggota->email
            ));
        }

        return $items;
    }
}
