<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\RoleEnum;
use App\Enums\StatusPenempatanEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\PenempatanManualRequest;
use App\Models\Bidang;
use App\Models\Penempatan;
use App\Models\SlotKhusus;
use App\Models\User;
use App\Services\PlacementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PenempatanController extends Controller
{
    public function __construct(
        protected PlacementService $placementService
    ) {}

    public function index(Request $request): View
    {
        $query = Penempatan::with(['peserta.profil', 'pembimbing', 'bidang', 'kuota.jurusan', 'slotKhusus'])
            ->orderBy('tgl_mulai', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('pembimbing_id')) {
            $query->where('pembimbing_id', $request->integer('pembimbing_id'));
        }

        if ($request->filled('bidang_id')) {
            $query->where('bidang_id', $request->integer('bidang_id'));
        }

        if ($request->filled('q')) {
            $search = '%' . trim((string) $request->input('q')) . '%';
            $query->whereHas('peserta', function ($qu) use ($search) {
                $qu->where('nama', 'like', $search)
                    ->orWhere('email', 'like', $search)
                    ->orWhereHas('profil', fn ($qp) => $qp->where('institusi', 'like', $search));
            });
        }

        $penempatanList = $query->paginate(15)->withQueryString();
        $pembimbingList = User::where('role', RoleEnum::Pembimbing)
            ->where('status_aktif', true)
            ->withCount(['penempatanBimbingan as mentee_aktif_count' => function ($q) {
                $q->where('status', StatusPenempatanEnum::Aktif);
            }])
            ->orderBy('nama')
            ->get();

        $slotKhususList = SlotKhusus::where('aktif', true)->orderBy('bulan_mulai')->get();
        $bidangList = Bidang::where('aktif', true)->orderBy('nama_bidang')->get();
        $jurusanList = \App\Models\Jurusan::where('aktif', true)->orderBy('nama_jurusan')->get();
        $kuotaList = \App\Models\Kuota::with(['bulanMagang', 'jurusan'])->where('aktif', true)->get();
        $pesertaList = User::where('role', RoleEnum::Peserta)
            ->where('status_aktif', true)
            ->whereDoesntHave('penempatanPeserta', function ($q) {
                $q->where('status', StatusPenempatanEnum::Aktif);
            })
            ->orderBy('nama')
            ->get();

        $penempatanStats = [
            'total' => Penempatan::count(),
            'aktif' => Penempatan::where('status', StatusPenempatanEnum::Aktif)->count(),
            'selesai' => Penempatan::where('status', StatusPenempatanEnum::Selesai)->count(),
            'khusus' => Penempatan::whereNotNull('slot_khusus_id')->count(),
        ];

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
        ]);
    }

    /**
     * Alur penempatan manual peserta (SuperAdmin & Admin) (F-06, F-07, AC-07-3).
     */
    public function storeManual(PenempatanManualRequest $request): RedirectResponse
    {
        $admin = $request->user();
        $mode = $request->input('mode', 'terdaftar');
        $jalur = $request->input('jalur', 'khusus');
        $pembimbing = User::findOrFail($request->integer('pembimbing_id'));
        $bidang = $request->filled('bidang_id') ? Bidang::find($request->integer('bidang_id')) : null;

        try {
            if ($mode === 'baru') {
                $penempatan = $this->placementService->tempatkanPesertaBaruManual(
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

                $penempatan = $this->placementService->tempatkanManual(
                    peserta: $peserta,
                    slotKhusus: $slotKhusus,
                    pembimbing: $pembimbing,
                    bidang: $bidang,
                    bulanMulai: (string) $request->input('bulan_mulai'),
                    durasiBulan: $request->integer('durasi_bulan'),
                    admin: $admin
                );
            }

            return redirect()->route('admin.penempatan.index')
                ->with('success', 'Penempatan peserta berhasil diproses secara manual.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memproses penempatan manual: ' . $e->getMessage());
        }
    }

    /**
     * Mengunduh dokumen resmi Surat Keterangan Diterima Magang PDF (AC-16-1, F-16).
     */
    public function unduhSuratDiterima(Penempatan $penempatan, \App\Services\SuratDiterimaPdfService $pdfService)
    {
        $this->authorize('view', $penempatan);

        return $pdfService->generate($penempatan);
    }

    /**
     * Menyajikan pratinjau dokumen resmi Surat Keterangan Diterima Magang PDF secara inline stream (AC-16-1, F-16).
     */
    public function pratinjauSuratDiterima(Penempatan $penempatan, \App\Services\SuratDiterimaPdfService $pdfService)
    {
        $this->authorize('view', $penempatan);

        return $pdfService->stream($penempatan);
    }

    /**
     * Memindahkan pembimbing peserta magang (AC-07-4).
     */
    public function pindahPembimbing(Penempatan $penempatan, Request $request): RedirectResponse
    {
        $request->validate([
            'pembimbing_id' => ['required', 'exists:users,id'],
        ]);

        $pembimbingBaru = User::findOrFail($request->integer('pembimbing_id'));

        try {
            $this->placementService->pindahPembimbing(
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
     * Membatalkan penerimaan atau mencatat pengunduran diri peserta magang (AC-07-5, BR-07).
     */
    public function batalkan(Penempatan $penempatan, Request $request): RedirectResponse
    {
        $request->validate([
            'alasan' => ['required', 'string', 'min:10', 'max:1000'],
        ], [
            'alasan.min' => 'Alasan pembatalan/pengunduran diri minimal 10 karakter.',
        ]);

        try {
            $this->placementService->batalkanPenempatan(
                penempatan: $penempatan,
                admin: $request->user(),
                alasan: $request->input('alasan')
            );

            return redirect()->route('admin.penempatan.index')
                ->with('success', 'Status penerimaan/penempatan peserta berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}

