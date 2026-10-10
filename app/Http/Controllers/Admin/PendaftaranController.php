<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

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
use App\Models\User;
use App\Services\AuditService;
use App\Services\PlacementService;
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
            $search = '%' . $rawSearch . '%';
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

        // AC-04-1: Pagination 25 per halaman
        $pendaftaranList = $query->paginate(25)->withQueryString();
        $bulanList = BulanMagang::orderBy('bulan_mulai', 'desc')->get();
        $jurusanList = Jurusan::where('aktif', true)->orderBy('nama_jurusan')->get();

        return view('admin.pendaftaran.index', [
            'pendaftaranList' => $pendaftaranList,
            'bulanList' => $bulanList,
            'jurusanList' => $jurusanList,
            'statusEnum' => StatusPendaftaranEnum::cases(),
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

        return view('admin.pendaftaran.show', [
            'pendaftaran' => $pendaftaran,
            'pembimbingList' => $pembimbingList,
            'bidangList' => $bidangList,
            'sisaKuota' => $sisaKuota,
        ]);
    }

    public function putuskan(KeputusanPendaftaranRequest $request, Pendaftaran $pendaftaran): RedirectResponse
    {
        $admin = $request->user();
        $keputusan = $request->input('keputusan');

        try {
            if ($keputusan === 'terima') {
                $pembimbing = User::find($request->integer('pembimbing_id'));
                $bidang = $request->filled('bidang_id') ? Bidang::find($request->integer('bidang_id')) : null;

                $this->placementService->terima($pendaftaran, $admin, $pembimbing, $bidang);

                return redirect()->route('admin.pendaftaran.show', $pendaftaran)
                    ->with('success', 'Pendaftaran berhasil diterima dan peserta resmi ditempatkan.');
            }

            if ($keputusan === 'waiting_list') {
                // AC-04-4: Opsi Waiting List ditolak jika kuota umum belum penuh
                $kuota = $pendaftaran->kuota;
                if ($kuota && ($kuota->jumlah - $kuota->terisi) > 0) {
                    return back()->with('error', 'Keputusan Waiting List ditolak (AC-04-4): Kuota umum jurusan ini belum penuh.');
                }

                if (! in_array($pendaftaran->status, [StatusPendaftaranEnum::Diajukan, StatusPendaftaranEnum::PerluPerbaikan], true)) {
                    throw new StatusSudahBerubahException();
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
            return back()->with('error', 'Gagal memproses keputusan: ' . $e->getMessage());
        }

        return back();
    }
}
