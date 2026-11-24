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

        $activeTab = (string) $request->input('tab', 'individu');
        if (! in_array($activeTab, ['individu', 'kelompok'], true)) {
            $activeTab = 'individu';
        }

        // 1. Hitung counter badge untuk kedua kategori
        $totalIndividu = Penempatan::where('status', StatusPenempatanEnum::Selesai)
            ->where(function (Builder $q) {
                $q->whereDoesntHave('pendaftaran')
                    ->orWhereHas('pendaftaran', function (Builder $sq) {
                        $sq->where('tipe', '!=', 'kelompok')
                            ->whereNull('kelompok_magang_id');
                    });
            })
            ->count();

        $totalKelompok = Pendaftaran::where(function (Builder $q) {
            $q->where('tipe', 'kelompok')->orWhereNotNull('kelompok_magang_id');
        })
            ->whereHas('penempatans', function (Builder $q) {
                $q->where('status', StatusPenempatanEnum::Selesai);
            })
            ->count();

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
        if ($activeTab === 'individu') {
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

            // Pencarian Teks Kontekstual Individu
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

            // Filter Status Berkas
            if ($request->filled('status_surat')) {
                $statusSurat = (string) $request->input('status_surat');
                if ($statusSurat === 'sudah') {
                    $query->whereNotNull('file_surat_selesai');
                } elseif ($statusSurat === 'belum') {
                    $query->whereNull('file_surat_selesai');
                }
            }

            $items = $query->latest('tgl_ditetapkan_selesai')
                ->latest('id')
                ->paginate(10)
                ->withQueryString();
        } else {
            // Tab Kelompok: Tampilkan daftar pendaftaran kelompok yang memiliki penempatan selesai
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

            // Pencarian Teks Kontekstual Kelompok
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

            // Filter Status Berkas Kelompok
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

            $items = $query->latest('id')
                ->paginate(10)
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
            'totalIndividu' => $totalIndividu,
            'totalKelompok' => $totalKelompok,
            'items' => $items,
            'masukPenempatan' => $masukPenempatan,
            'masukPendaftaran' => $masukPendaftaran,
        ]);
    }
}
