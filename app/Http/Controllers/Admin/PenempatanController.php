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

        $penempatanList = $query->paginate(15);
        $pembimbingList = User::where('role', RoleEnum::Pembimbing)->where('status_aktif', true)->orderBy('nama')->get();

        return view('admin.penempatan.index', [
            'penempatanList' => $penempatanList,
            'statusEnum' => StatusPenempatanEnum::cases(),
            'pembimbingList' => $pembimbingList,
        ]);
    }

    /**
     * Alur penempatan manual jalur khusus (SuperAdmin & Admin) (F-06, F-07).
     */
    public function storeManual(PenempatanManualRequest $request): RedirectResponse
    {
        $admin = $request->user();
        $peserta = User::findOrFail($request->integer('peserta_id'));
        $slotKhusus = SlotKhusus::findOrFail($request->integer('slot_khusus_id'));
        $pembimbing = User::findOrFail($request->integer('pembimbing_id'));
        $bidang = $request->filled('bidang_id') ? Bidang::find($request->integer('bidang_id')) : null;

        try {
            $penempatan = $this->placementService->tempatkanManual(
                peserta: $peserta,
                slotKhusus: $slotKhusus,
                pembimbing: $pembimbing,
                bidang: $bidang,
                bulanMulai: $request->input('bulan_mulai'),
                durasiBulan: $request->integer('durasi_bulan'),
                admin: $admin
            );

            return redirect()->route('admin.penempatan.index')
                ->with('success', 'Peserta jalur khusus berhasil ditempatkan secara manual.');
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

