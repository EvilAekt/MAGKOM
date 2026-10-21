<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\RealtimeEventTypeEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusPendaftaranEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\TipeAuditEnum;
use App\Exceptions\Domain\KuotaPenuhException;
use App\Exceptions\Domain\StatusSudahBerubahException;
use App\Http\Controllers\Controller;
use App\Http\Requests\KeputusanPendaftaranRequest;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\Pendaftaran;
use App\Models\PesertaProfil;
use App\Models\User;
use App\Services\AuditService;
use App\Services\DocumentArchiveService;
use App\Services\PlacementService;
use App\Services\RealtimeEventService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PendaftaranController extends Controller
{
    public function __construct(
        protected PlacementService $placementService,
        protected AuditService $auditService
    ) {}

    public function index(Request $request): View
    {
        $query = Pendaftaran::with(['user.profil', 'bulanMagang', 'kuota.jurusan', 'penempatan']);

        $sort = $request->input('sort', 'fifo');
        if ($sort === 'terbaru') {
            $query->orderBy('created_at', 'desc')->orderBy('id', 'desc');
        } elseif ($sort === 'terlama') {
            $query->orderBy('created_at', 'asc')->orderBy('id', 'asc');
        } else {
            // AC-04-1 & BR-05: FIFO antrean verifikasi berdasarkan waktu pengajuan berkas
            $query->orderByRaw('COALESCE(diajukan_at, created_at) ASC')->orderBy('id', 'asc');
        }

        if ($request->filled('q')) {
            $rawSearch = trim((string) $request->input('q'));
            $cleanId = ltrim(str_ireplace(['#', 'REG-'], '', $rawSearch), '0');
            $escapedSearch = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $rawSearch);
            $search = '%'.$escapedSearch.'%';
            $query->where(function ($q) use ($search, $cleanId) {
                if ($cleanId !== '' && is_numeric($cleanId)) {
                    $q->orWhere('id', (int) $cleanId);
                }
                $q->orWhereHas('user', function ($qu) use ($search) {
                    $qu->where('nama', 'like', $search)
                        ->orWhere('email', 'like', $search)
                        ->orWhereHas('profil', function ($qp) use ($search) {
                            $qp->where('institusi', 'like', $search)
                                ->orWhere('nim_nisn', 'like', $search);
                        });
                });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('bulan_magang_id')) {
            $query->where('bulan_magang_id', $request->integer('bulan_magang_id'));
        }

        if ($request->filled('jurusan_id')) {
            $jurusanId = $request->integer('jurusan_id');
            $query->where(function ($q) use ($jurusanId) {
                $q->whereHas('kuota', fn ($qk) => $qk->where('jurusan_id', $jurusanId))
                    ->orWhereHas('user.profil', fn ($qp) => $qp->where('jurusan_id', $jurusanId));
            });
        }

        if ($request->filled('jalur')) {
            $jalur = $request->input('jalur');
            $query->whereHas('penempatan', fn ($qp) => $qp->where('jalur', $jalur));
        }

        // SLA Watchdog Filter (PRD 2.3 & F-04: target verifikasi <= 5 hari kerja)
        if ($request->filled('sla')) {
            $sla = $request->input('sla');
            if ($sla === 'breach' || $sla === 'terlambat') {
                $query->where('status', StatusPendaftaranEnum::Diajukan)
                    ->whereRaw('COALESCE(diajukan_at, created_at) <= ?', [now()->subDays(5)]);
            } elseif ($sla === 'mendekati') {
                $query->where('status', StatusPendaftaranEnum::Diajukan)
                    ->whereRaw('COALESCE(diajukan_at, created_at) <= ?', [now()->subDays(3)])
                    ->whereRaw('COALESCE(diajukan_at, created_at) > ?', [now()->subDays(5)]);
            } elseif ($sla === 'aman') {
                $query->where('status', StatusPendaftaranEnum::Diajukan)
                    ->whereRaw('COALESCE(diajukan_at, created_at) > ?', [now()->subDays(3)]);
            }
        }

        // AC-04-1: Pagination 25 per halaman
        $pendaftaranList = $query->paginate(25)->withQueryString();
        $bulanList = BulanMagang::orderBy('bulan_mulai', 'desc')->get();
        $jurusanList = Jurusan::where('aktif', true)->orderBy('nama_jurusan')->get();

        $queueStats = [
            'total' => Pendaftaran::count(),
            'diajukan' => Pendaftaran::where('status', StatusPendaftaranEnum::Diajukan)->count(),
            'perlu_perbaikan' => Pendaftaran::where('status', StatusPendaftaranEnum::PerluPerbaikan)->count(),
            'waiting_list' => Pendaftaran::where('status', StatusPendaftaranEnum::WaitingList)->count(),
            'diterima' => Pendaftaran::where('status', StatusPendaftaranEnum::Diterima)->count(),
            'ditolak' => Pendaftaran::where('status', StatusPendaftaranEnum::Ditolak)->count(),
            'sla_warning' => Pendaftaran::where('status', StatusPendaftaranEnum::Diajukan)
                ->whereRaw('COALESCE(diajukan_at, created_at) <= ?', [now()->subDays(3)])
                ->count(),
            'sla_breach' => Pendaftaran::where('status', StatusPendaftaranEnum::Diajukan)
                ->whereRaw('COALESCE(diajukan_at, created_at) <= ?', [now()->subDays(5)])
                ->count(),
            'sla_mendekati' => Pendaftaran::where('status', StatusPendaftaranEnum::Diajukan)
                ->whereRaw('COALESCE(diajukan_at, created_at) <= ?', [now()->subDays(3)])
                ->whereRaw('COALESCE(diajukan_at, created_at) > ?', [now()->subDays(5)])
                ->count(),
        ];

        return view('admin.pendaftaran.index', [
            'pendaftaranList' => $pendaftaranList,
            'bulanList' => $bulanList,
            'jurusanList' => $jurusanList,
            'statusEnum' => StatusPendaftaranEnum::cases(),
            'queueStats' => $queueStats,
        ]);
    }

    public function show(Pendaftaran $pendaftaran): View
    {
        $pendaftaran->load(['user.profil.jurusan', 'bulanMagang', 'kuota.jurusan', 'dokumen.jenisDokumen']);

        $pembimbingList = User::where('role', RoleEnum::Pembimbing)
            ->where('status_aktif', true)
            ->withCount(['penempatanBimbingan as mentee_aktif_count' => function ($q) {
                $q->where('status', StatusPenempatanEnum::Aktif);
            }])
            ->orderBy('nama')
            ->get();

        $bidangList = Bidang::where('aktif', true)->orderBy('nama_bidang')->get();

        // Hitung sisa kuota jurusan untuk bulan magang pendaftaran terkait (AC-04-4)
        $kuota = $pendaftaran->kuota;
        $sisaKuota = $kuota ? max(0, $kuota->jumlah - $kuota->terisi) : 0;

        // Deteksi duplikasi NIM/NISN lintas akun (SEC-15)
        $profil = $pendaftaran->user?->profil;
        $duplikasiNimCount = 0;
        if ($profil && ! empty($profil->nim_nisn)) {
            $duplikasiNimCount = PesertaProfil::where('nim_nisn', $profil->nim_nisn)
                ->where('user_id', '!=', $pendaftaran->user_id)
                ->count();
        }

        return view('admin.pendaftaran.show', [
            'pendaftaran' => $pendaftaran,
            'pembimbingList' => $pembimbingList,
            'bidangList' => $bidangList,
            'sisaKuota' => $sisaKuota,
            'duplikasiNimCount' => $duplikasiNimCount,
        ]);
    }

    public function putuskan(KeputusanPendaftaranRequest $request, Pendaftaran $pendaftaran): RedirectResponse
    {
        $admin = $request->user();
        $keputusan = $request->input('keputusan');

        try {
            if ($keputusan === 'terima') {
                $this->placementService->terimaBerkas($pendaftaran, $admin, $request->input('catatan'));

                if ($request->filled('pembimbing_id')) {
                    $pembimbing = User::findOrFail($request->input('pembimbing_id'));
                    $bidang = $request->filled('bidang_id') ? Bidang::find($request->input('bidang_id')) : null;
                    $this->placementService->tetapkanPenempatan($pendaftaran, $admin, $pembimbing, $bidang);

                    return redirect()->route('admin.pendaftaran.show', $pendaftaran)
                        ->with('success', 'Pendaftaran berhasil diterima dan langsung ditempatkan kepada pembimbing.');
                }

                return redirect()->route('admin.pendaftaran.show', $pendaftaran)
                    ->with('success', 'Berkas pendaftaran berhasil diterima. Silakan tetapkan pembimbing lapangan di menu Penempatan Peserta.');
            }

            if ($keputusan === 'waiting_list') {
                // AC-04-4: Opsi Waiting List ditolak jika kuota umum belum penuh
                $kuota = $pendaftaran->kuota;
                if ($kuota && ($kuota->jumlah - $kuota->terisi) > 0) {
                    return back()->with('error', 'Keputusan Waiting List ditolak (AC-04-4): Kuota umum jurusan ini belum penuh.');
                }

                if (! in_array($pendaftaran->status, [StatusPendaftaranEnum::Diajukan, StatusPendaftaranEnum::PerluPerbaikan], true)) {
                    throw new StatusSudahBerubahException;
                }

                $pendaftaran->update([
                    'status' => StatusPendaftaranEnum::WaitingList,
                    'verifikator_id' => $admin->id,
                    'diputuskan_at' => now(),
                    'catatan' => $request->input('catatan'),
                ]);

                $this->auditService->catat(
                    tipe: TipeAuditEnum::Aksi,
                    tabel: 'pendaftaran',
                    idData: $pendaftaran->id,
                    nilaiLama: ['status' => StatusPendaftaranEnum::Diajukan->value],
                    nilaiBaru: ['status' => StatusPendaftaranEnum::WaitingList->value],
                    detail: [
                        'aksi' => 'Memindahkan pendaftaran ke antrean Waiting List karena kuota penuh',
                        'admin_id' => $admin->id,
                        'catatan' => $request->input('catatan'),
                    ]
                );

                // Notifikasi Real-time SSE ke peserta (TASK-SSE-05)
                app(RealtimeEventService::class)->publish(
                    RealtimeEventTypeEnum::StatusPendaftaran,
                    [
                        'title' => 'Dialihkan ke Waiting List',
                        'message' => 'Kuota umum saat ini telah penuh. Pengajuan Anda masuk ke dalam antrean Waiting List.',
                        'status' => 'waiting_list',
                        'action_url' => route('peserta.dashboard'),
                    ],
                    $pendaftaran->user_id
                );

                return redirect()->route('admin.pendaftaran.show', $pendaftaran)
                    ->with('success', 'Pendaftaran berhasil dialihkan ke antrean Waiting List.');

            }

            if ($keputusan === 'tolak') {
                $this->placementService->tolak($pendaftaran, $admin, $request->input('catatan'));

                return redirect()->route('admin.pendaftaran.show', $pendaftaran)
                    ->with('success', 'Pengajuan pendaftaran berhasil ditolak.');
            }

            if ($keputusan === 'perlu_perbaikan') {
                $this->placementService->mintaPerbaikan($pendaftaran, $admin, $request->input('catatan'));

                return redirect()->route('admin.pendaftaran.show', $pendaftaran)
                    ->with('success', 'Permintaan perbaikan berkas telah dikirimkan ke peserta.');
            }
        } catch (StatusSudahBerubahException $e) {
            // AC-04-5: Pesan ramah saat terjadi bentrok keputusan bersamaan
            return back()->with('error', 'Status pendaftaran sudah berubah, muat ulang halaman (AC-04-5).');
        } catch (KuotaPenuhException $e) {
            // AC-04-6: Arahkan ke Waiting List bila kuota ternyata penuh saat diterima
            return back()->with('error', 'Kuota umum telah terisi penuh. Silakan alihkan pendaftaran ke antrean Waiting List (AC-04-6).');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memproses keputusan: '.$e->getMessage());
        }

        return back();
    }

    /**
     * Mengunduh bundel seluruh berkas pendaftaran peserta dalam arsip ZIP terstruktur (PRD F-03, Bab 8.4, NFR-S6).
     */
    public function downloadBundelZip(Request $request, DocumentArchiveService $archiveService)
    {
        $query = Pendaftaran::with(['user.profil', 'dokumen.jenisDokumen', 'bulanMagang', 'kuota.jurusan']);

        if ($request->filled('pendaftaran_ids')) {
            $ids = (array) $request->input('pendaftaran_ids');
            $query->whereIn('id', $ids);
        } elseif ($request->filled('bulan_magang_id')) {
            $query->where('bulan_magang_id', $request->integer('bulan_magang_id'));
        } elseif ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        } else {
            // Default: ambil pendaftaran berstatus diterima atau diajukan (maksimal 50 entri per bundel untuk keselamatan memori)
            $query->whereIn('status', [StatusPendaftaranEnum::Diterima, StatusPendaftaranEnum::Diajukan]);
        }

        $pendaftarans = $query->latest('id')->take(50)->get();

        if ($pendaftarans->isEmpty()) {
            return back()->with('error', 'Tidak ada data pendaftaran yang memenuhi kriteria untuk diunduh berkasnya.');
        }

        $this->auditService->catat(
            tipe: TipeAuditEnum::AksesData,
            tabel: 'dokumen',
            idData: null,
            nilaiLama: null,
            nilaiBaru: null,
            detail: [
                'aksi' => 'Unduh bundel arsip ZIP berkas pendaftaran peserta',
                'jumlah_pendaftar' => $pendaftarans->count(),
            ]
        );

        $zipPath = $archiveService->createRegistrationBatchZip($pendaftarans);
        $downloadName = 'bundel-berkas-magkom-'.date('Ymd-His').'.zip';

        return response()->download($zipPath, $downloadName, [
            'Content-Type' => 'application/zip',
            'X-Content-Type-Options' => 'nosniff',
        ])->deleteFileAfterSend(true);
    }
}
