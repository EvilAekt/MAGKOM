<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\Penempatan\AssignPesertaAction;
use App\Actions\Admin\Penempatan\BulkReassignPembimbingAction;
use App\Actions\Admin\Penempatan\CancelPenempatanAction;
use App\Actions\Admin\Penempatan\CreateManualPenempatanAction;
use App\Actions\Admin\Penempatan\ReassignPembimbingAction;
use App\Enums\RoleEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\StatusTugasEnum;
use App\Enums\TipeAuditEnum;
use App\Exports\Admin\Penempatan\RekapSuratDiterimaExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Penempatan\BatalkanPenempatanRequest;
use App\Http\Requests\Admin\Penempatan\BulkReassignPembimbingRequest;
use App\Http\Requests\Admin\Penempatan\PindahPembimbingRequest;
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
use App\Services\AuditService;
use App\Services\PlacementService;
use App\Services\SuratDiterimaPdfService;
use App\Services\XlsxExportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
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
        protected RekapSuratDiterimaExport $rekapExport
    ) {}

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

        $activeTab = $request->input('tab', 'sudah');
        if (! in_array($activeTab, ['sudah', 'belum'], true)) {
            $activeTab = 'sudah';
        }

        $antreanBelumDitempatkan = $this->antreanQuery->getPaginated($request, 5);
        $totalBelumDitempatkan = $this->antreanQuery->getTotalBelumDitempatkan();

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

            return redirect()->route('admin.penempatan.index')
                ->with('success', 'Penetapan pembimbing lapangan untuk '.$pendaftaran->user?->nama.' berhasil disimpan.');
        } catch (\Throwable $e) {
            return redirect()->route('admin.penempatan.index')
                ->with('error', 'Gagal menetapkan penempatan: '.$e->getMessage());
        }
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

        return view('admin.penempatan.show', [
            'penempatan' => $penempatan,
            'pembimbingList' => $pembimbingList,
            'tugasCount' => $tugasCount,
            'tugasSelesaiCount' => $tugasSelesaiCount,
            'logbookCount' => $logbookCount,
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
}
