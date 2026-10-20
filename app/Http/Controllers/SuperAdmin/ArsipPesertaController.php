<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Enums\StatusPenempatanEnum;
use App\Enums\TipeAuditEnum;
use App\Http\Controllers\Controller;
use App\Models\Penempatan;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ArsipPesertaController extends Controller
{
    /**
     * Menampilkan daftar arsip peserta magang selesai atau nonaktif (AC-12-4, PRD 9.2).
     */
    public function index(Request $request): View
    {
        $query = Penempatan::with(['peserta.profil', 'pembimbing', 'bidang', 'kuota.jurusan', 'penilaian'])
            ->whereIn('status', [StatusPenempatanEnum::Selesai, StatusPenempatanEnum::Nonaktif, StatusPenempatanEnum::MengundurkanDiri])
            ->orderBy('tgl_selesai', 'desc');

        if ($request->filled('search')) {
            $search = '%'.$request->input('search').'%';
            $query->whereHas('peserta', function ($q) use ($search) {
                $q->where('nama', 'like', $search)
                    ->orWhere('email', 'like', $search)
                    ->orWhereHas('profil', function ($qp) use ($search) {
                        $qp->where('institusi', 'like', $search)
                            ->orWhere('nim_nisn', 'like', $search);
                    });
            });
        }

        if ($request->filled('tahun')) {
            $query->whereYear('tgl_selesai', $request->input('tahun'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $arsipList = $query->paginate(15)->withQueryString();

        $driver = DB::connection()->getDriverName();
        $yearExpr = $driver === 'sqlite' ? 'strftime("%Y", tgl_selesai)' : 'YEAR(tgl_selesai)';

        $tahunList = Penempatan::whereIn('status', [StatusPenempatanEnum::Selesai, StatusPenempatanEnum::Nonaktif, StatusPenempatanEnum::MengundurkanDiri])
            ->whereNotNull('tgl_selesai')
            ->selectRaw("DISTINCT {$yearExpr} as tahun")
            ->orderBy('tahun', 'desc')
            ->pluck('tahun')
            ->filter();

        return view('superadmin.arsip.index', [
            'arsipList' => $arsipList,
            'tahunList' => $tahunList,
        ]);
    }

    /**
     * Menampilkan detail rekam jejak arsip peserta magang secara baca-saja (AC-12-4, C9).
     */
    public function show(Penempatan $penempatan): View
    {
        $penempatan->load([
            'peserta.profil.jurusan',
            'pembimbing.bidang',
            'bidang',
            'kuota.jurusan',
            'slotKhusus',
            'tugas.progresLogs',
            'logbooks',
            'penilaian.details.rubrik',
            'pendaftaran.dokumen',
        ]);

        app(AuditService::class)->catat(
            tipe: TipeAuditEnum::AksesData,
            tabel: 'penempatan',
            idData: $penempatan->id,
            nilaiLama: null,
            nilaiBaru: null,
            detail: [
                'aksi' => 'Melihat arsip historis permanen peserta magang (C9)',
                'peserta_id' => $penempatan->peserta_id,
            ],
            user: request()->user()?->id
        );

        return view('superadmin.arsip.show', [
            'penempatan' => $penempatan,
        ]);
    }
}
