<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\RoleEnum;
use App\Enums\StatusPendaftaranEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\KeputusanPendaftaranRequest;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Pendaftaran;
use App\Models\User;
use App\Services\PlacementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PendaftaranController extends Controller
{
    public function __construct(
        protected PlacementService $placementService
    ) {}

    public function index(Request $request): View
    {
        $query = Pendaftaran::with(['user.profil', 'bulanMagang', 'kuota.jurusan', 'penempatan'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('bulan_magang_id')) {
            $query->where('bulan_magang_id', $request->integer('bulan_magang_id'));
        }

        $pendaftaranList = $query->paginate(15);
        $bulanList = BulanMagang::orderBy('bulan_mulai', 'desc')->get();

        return view('admin.pendaftaran.index', [
            'pendaftaranList' => $pendaftaranList,
            'bulanList' => $bulanList,
            'statusEnum' => StatusPendaftaranEnum::cases(),
        ]);
    }

    public function show(Pendaftaran $pendaftaran): View
    {
        $pendaftaran->load(['user.profil.jurusan', 'bulanMagang', 'kuota.jurusan', 'dokumen.jenisDokumen']);

        $pembimbingList = User::where('role', RoleEnum::Pembimbing)
            ->where('status_aktif', true)
            ->orderBy('nama')
            ->get();

        $bidangList = Bidang::where('aktif', true)->orderBy('nama_bidang')->get();

        return view('admin.pendaftaran.show', [
            'pendaftaran' => $pendaftaran,
            'pembimbingList' => $pembimbingList,
            'bidangList' => $bidangList,
        ]);
    }

    public function putuskan(KeputusanPendaftaranRequest $request, Pendaftaran $pendaftaran): RedirectResponse
    {
        $admin = $request->user();
        $keputusan = $request->input('keputusan');

        if ($keputusan === 'terima') {
            $pembimbing = User::find($request->integer('pembimbing_id'));
            $bidang = $request->filled('bidang_id') ? Bidang::find($request->integer('bidang_id')) : null;

            try {
                $penempatan = $this->placementService->terima($pendaftaran, $admin, $pembimbing, $bidang);
                return redirect()->route('admin.pendaftaran.show', $pendaftaran)
                    ->with('success', 'Pendaftaran berhasil diterima dan peserta resmi ditempatkan.');
            } catch (\Exception $e) {
                return back()->with('error', 'Gagal memproses penerimaan: ' . $e->getMessage());
            }
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

        return back();
    }
}
