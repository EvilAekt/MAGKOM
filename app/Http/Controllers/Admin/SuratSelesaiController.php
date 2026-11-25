<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\StatusPenempatanEnum;
use App\Enums\TipeAuditEnum;
use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;
use App\Models\Penempatan;
use App\Services\AuditService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;

class SuratSelesaiController extends Controller
{
    public function __construct(
        protected AuditService $auditService
    ) {}

    /**
     * Menampilkan daftar peserta magang yang telah menyelesaikan masa magang
     * terbagi ke dalam tab Peserta Individu dan Rombongan Kelompok.
     */
    public function index(Request $request): View
    {
        $this->authorize('manage', Penempatan::class);

        $activeTab = (string) $request->input('tab', 'semua');
        if (! in_array($activeTab, ['semua', 'individu', 'kelompok'], true)) {
            $activeTab = 'semua';
        }

        // 1. Hitung counter badge dan KPI stats untuk Semua, Individu, dan Kelompok
        $individuBaseQuery = Penempatan::where('status', StatusPenempatanEnum::Selesai)
            ->where(function (Builder $q) {
                $q->whereDoesntHave('pendaftaran')
                    ->orWhereHas('pendaftaran', function (Builder $sq) {
                        $sq->where('tipe', '!=', 'kelompok')
                            ->whereNull('kelompok_magang_id');
                    });
            });

        $totalIndividu = (clone $individuBaseQuery)->count();
        $individuSudah = (clone $individuBaseQuery)->whereNotNull('file_surat_selesai')->count();
        $individuBelum = $totalIndividu - $individuSudah;

        $kelompokBaseQuery = Pendaftaran::where(function (Builder $q) {
            $q->where('tipe', 'kelompok')->orWhereNotNull('kelompok_magang_id');
        })
            ->whereHas('penempatans', function (Builder $q) {
                $q->where('status', StatusPenempatanEnum::Selesai);
            });

        $totalKelompok = (clone $kelompokBaseQuery)->count();
        $kelompokSudah = (clone $kelompokBaseQuery)
            ->whereHas('penempatans', function (Builder $q) {
                $q->where('status', StatusPenempatanEnum::Selesai)->whereNotNull('file_surat_selesai');
            })
            ->count();
        $kelompokBelum = $totalKelompok - $kelompokSudah;

        $totalSemua = $totalIndividu + $totalKelompok;
        $totalPesertaSelesai = Penempatan::where('status', StatusPenempatanEnum::Selesai)->count();

        $kpiStats = [
            'total_entri' => $totalSemua,
            'belum_diunggah' => $individuBelum + $kelompokBelum,
            'sudah_terbit' => $individuSudah + $kelompokSudah,
            'total_peserta_selesai' => $totalPesertaSelesai,
        ];

        // 2. Deteksi mode masuk terfokus (Two-Tier Workspace ala Pembimbing Tugas)
        $masukIndividuId = $request->input('masuk_individu');
        $masukPenempatan = null;
        if ($activeTab === 'individu' && ! empty($masukIndividuId)) {
            $masukPenempatan = Penempatan::where('status', StatusPenempatanEnum::Selesai)
                ->where(function (Builder $q) {
                    $q->whereDoesntHave('pendaftaran')
                        ->orWhereHas('pendaftaran', function (Builder $sq) {
                            $sq->where('tipe', '!=', 'kelompok')
                                ->whereNull('kelompok_magang_id');
                        });
                })
                ->with(['peserta.profil.jurusan', 'bidang', 'pembimbing', 'pendaftaran'])
                ->find($masukIndividuId);
        }

        $masukKelompokId = $request->input('masuk_kelompok');
        $masukPendaftaran = null;
        if ($activeTab === 'kelompok' && ! empty($masukKelompokId)) {
            $masukPendaftaran = Pendaftaran::where(function (Builder $q) {
                $q->where('tipe', 'kelompok')->orWhereNotNull('kelompok_magang_id');
            })
                ->whereHas('penempatans', function (Builder $q) {
                    $q->where('status', StatusPenempatanEnum::Selesai);
                })
                ->with([
                    'kelompokMagang.ketua.profil',
                    'kelompokMagang.anggota.user.profil.jurusan',
                    'penempatans' => function ($q) {
                        $q->where('status', StatusPenempatanEnum::Selesai)
                            ->with(['peserta.profil.jurusan', 'bidang', 'pembimbing']);
                    },
                    'penempatans.bidang',
                    'penempatans.pembimbing',
                ])
                ->find($masukKelompokId);
        }

        // 3. Query data sesuai tab aktif
        $perPage = (int) $request->input('per_page', 10);
        if (! in_array($perPage, [5, 10, 25], true)) {
            $perPage = 10;
        }

        if ($activeTab === 'semua') {
            $individuRows = $this->buildIndividuQuery($request)
                ->latest('tgl_ditetapkan_selesai')
                ->latest('id')
                ->get()
                ->map(fn (Penempatan $p): object => (object) [
                    'type' => 'individu',
                    'sort_ts' => $p->tgl_ditetapkan_selesai?->timestamp ?? $p->created_at?->timestamp ?? 0,
                    'sort_id' => $p->id,
                    'model' => $p,
                ]);

            $kelompokRows = $this->buildKelompokQuery($request)
                ->latest('id')
                ->get()
                ->map(fn (Pendaftaran $p): object => (object) [
                    'type' => 'kelompok',
                    'sort_ts' => $p->penempatans->first()?->tgl_ditetapkan_selesai?->timestamp ?? $p->created_at?->timestamp ?? 0,
                    'sort_id' => $p->id,
                    'model' => $p,
                ]);

            $merged = $individuRows->concat($kelompokRows)
                ->sortByDesc(fn (object $row): array => [$row->sort_ts, $row->sort_id])
                ->values();

            $currentPage = LengthAwarePaginator::resolveCurrentPage();
            $items = new LengthAwarePaginator(
                $merged->forPage($currentPage, $perPage)->values(),
                $merged->count(),
                $perPage,
                $currentPage,
                [
                    'path' => $request->url(),
                    'query' => $request->query(),
                ]
            );
        } elseif ($activeTab === 'individu') {
            $items = $this->buildIndividuQuery($request)
                ->latest('tgl_ditetapkan_selesai')
                ->latest('id')
                ->paginate($perPage)
                ->withQueryString();
        } else {
            $items = $this->buildKelompokQuery($request)
                ->latest('id')
                ->paginate($perPage)
                ->withQueryString();
        }

        // Catat jejak audit trail
        $aksiAudit = 'Melihat daftar manajemen Surat Selesai Magang';
        if ($masukPenempatan) {
            $aksiAudit = 'Membuka workspace fokus Surat Selesai Individu ID: '.$masukPenempatan->id;
        } elseif ($masukPendaftaran) {
            $aksiAudit = 'Membuka workspace fokus Surat Selesai Kelompok ID: '.$masukPendaftaran->id;
        }

        $this->auditService->catat(
            tipe: TipeAuditEnum::AksesData,
            tabel: 'penempatan',
            idData: $masukPenempatan?->id ?? ($masukPendaftaran?->id ?? 0),
            nilaiLama: null,
            nilaiBaru: null,
            detail: [
                'aksi' => $aksiAudit,
                'tab' => $activeTab,
                'masuk_individu' => $masukPenempatan?->id,
                'masuk_kelompok' => $masukPendaftaran?->id,
                'admin_id' => $request->user()?->id,
            ],
            user: $request->user()?->id
        );

        return view('admin.surat-selesai.index', [
            'activeTab' => $activeTab,
            'totalSemua' => $totalSemua,
            'totalIndividu' => $totalIndividu,
            'totalKelompok' => $totalKelompok,
            'kpiStats' => $kpiStats,
            'items' => $items,
            'masukPenempatan' => $masukPenempatan,
            'masukPendaftaran' => $masukPendaftaran,
        ]);
    }

    /**
     * Query builder penempatan individu berstatus selesai beserta filter pencarian & status berkas.
     */
    protected function buildIndividuQuery(Request $request): Builder
    {
        $query = Penempatan::query()
            ->where('status', StatusPenempatanEnum::Selesai)
            ->where(function (Builder $q) {
                $q->whereDoesntHave('pendaftaran')
                    ->orWhereHas('pendaftaran', function (Builder $sq) {
                        $sq->where('tipe', '!=', 'kelompok')
                            ->whereNull('kelompok_magang_id');
                    });
            })
            ->with(['peserta.profil.jurusan', 'bidang', 'pembimbing']);

        if ($request->filled('q')) {
            $keyword = trim((string) $request->input('q'));
            $query->where(function (Builder $q) use ($keyword) {
                $q->whereHas('peserta', function (Builder $u) use ($keyword) {
                    $u->where('nama', 'like', "%{$keyword}%")
                        ->orWhere('email', 'like', "%{$keyword}%");
                })->orWhereHas('peserta.profil', function (Builder $p) use ($keyword) {
                    $p->where('institusi', 'like', "%{$keyword}%")
                        ->orWhere('nim_nisn', 'like', "%{$keyword}%");
                });
            });
        }

        if ($request->filled('status_surat')) {
            $statusSurat = (string) $request->input('status_surat');
            if ($statusSurat === 'sudah') {
                $query->whereNotNull('file_surat_selesai');
            } elseif ($statusSurat === 'belum') {
                $query->whereNull('file_surat_selesai');
            }
        }

        return $query;
    }

    /**
     * Query builder pendaftaran kelompok berstatus selesai beserta filter pencarian & status berkas.
     */
    protected function buildKelompokQuery(Request $request): Builder
    {
        $query = Pendaftaran::query()
            ->where(function (Builder $q) {
                $q->where('tipe', 'kelompok')->orWhereNotNull('kelompok_magang_id');
            })
            ->whereHas('penempatans', function (Builder $q) {
                $q->where('status', StatusPenempatanEnum::Selesai);
            })
            ->with([
                'kelompokMagang.ketua.profil',
                'kelompokMagang.anggota.user.profil.jurusan',
                'penempatans' => function ($q) {
                    $q->where('status', StatusPenempatanEnum::Selesai)
                        ->with(['peserta.profil.jurusan', 'bidang', 'pembimbing']);
                },
                'penempatans.bidang',
                'penempatans.pembimbing',
            ]);

        if ($request->filled('q')) {
            $keyword = trim((string) $request->input('q'));
            $query->where(function (Builder $q) use ($keyword) {
                $q->whereHas('kelompokMagang', function (Builder $k) use ($keyword) {
                    $k->where('nama_kelompok', 'like', "%{$keyword}%")
                        ->orWhereHas('ketua.profil', function (Builder $p) use ($keyword) {
                            $p->where('institusi', 'like', "%{$keyword}%");
                        });
                })->orWhereHas('penempatans.peserta', function (Builder $u) use ($keyword) {
                    $u->where('nama', 'like', "%{$keyword}%")
                        ->orWhereHas('profil', function (Builder $p) use ($keyword) {
                            $p->where('institusi', 'like', "%{$keyword}%");
                        });
                });
            });
        }

        if ($request->filled('status_surat')) {
            $statusSurat = (string) $request->input('status_surat');
            if ($statusSurat === 'sudah') {
                $query->whereHas('penempatans', function (Builder $q) {
                    $q->where('status', StatusPenempatanEnum::Selesai)->whereNotNull('file_surat_selesai');
                });
            } elseif ($statusSurat === 'belum') {
                $query->whereHas('penempatans', function (Builder $q) {
                    $q->where('status', StatusPenempatanEnum::Selesai)->whereNull('file_surat_selesai');
                });
            }
        }

        return $query;
    }
}
