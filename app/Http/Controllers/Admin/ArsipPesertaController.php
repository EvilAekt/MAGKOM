<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\TipeAuditEnum;
use App\Http\Controllers\Controller;
use App\Models\Penempatan;
use App\Services\ArsipPesertaService;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ArsipPesertaController extends Controller
{
    public function __construct(
        protected ArsipPesertaService $arsipService,
        protected AuditService $auditService
    ) {}

    /**
     * Menampilkan daftar arsip peserta purna magang (selesai, nonaktif, mengundurkan diri) untuk Administrasi.
     */
    public function index(Request $request): View
    {
        $arsipList = $this->arsipService->getArsipPaginated($request, 10);
        $stats = $this->arsipService->getStatistikArsip();
        $tahunList = $this->arsipService->getTahunFilterList();

        return view('admin.arsip.index', [
            'arsipList' => $arsipList,
            'tahunList' => $tahunList,
            'totalArsip' => $stats['total_arsip'],
            'totalSelesai' => $stats['total_selesai'],
            'totalNonaktif' => $stats['total_nonaktif'],
            'totalMengundurkanDiri' => $stats['total_mengundurkan_diri'],
        ]);
    }

    /**
     * Menampilkan detail rekam jejak historis peserta magang secara baca-saja untuk Administrasi.
     */
    public function show(Penempatan $penempatan): View
    {
        if (! in_array($penempatan->status, $this->arsipService->getArsipStatuses(), true)) {
            abort(404, 'Data penempatan bukan merupakan arsip.');
        }

        $penempatan = $this->arsipService->loadDetailArsip($penempatan);

        $this->auditService->catat(
            tipe: TipeAuditEnum::AksesData,
            tabel: 'penempatan',
            idData: $penempatan->id,
            nilaiLama: null,
            nilaiBaru: null,
            detail: [
                'aksi' => 'Melihat arsip historis permanen peserta magang (Administrasi)',
                'peserta_id' => $penempatan->peserta_id,
            ],
            user: request()->user()?->id
        );

        return view('admin.arsip.show', [
            'penempatan' => $penempatan,
        ]);
    }
}
