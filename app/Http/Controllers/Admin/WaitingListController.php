<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\StatusPendaftaranEnum;
use App\Enums\TipeAuditEnum;
use App\Http\Controllers\Controller;
use App\Models\BulanMagang;
use App\Models\Pendaftaran;
use App\Services\AuditService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class WaitingListController extends Controller
{
    public function __construct(
        protected AuditService $auditService
    ) {}

    /**
     * Menampilkan daftar tunggu calon peserta magang berdasarkan waktu submit (FIFO) (BR-05).
     */
    public function index(Request $request): View
    {
        $waitingList = Pendaftaran::with(['user.profil', 'bulanMagang', 'kuota.jurusan'])
            ->where('status', StatusPendaftaranEnum::WaitingList)
            ->orderBy('diajukan_at', 'asc')
            ->paginate(20);

        return view('admin.waiting-list.index', [
            'waitingList' => $waitingList,
        ]);
    }

    /**
     * Menutup antrean waiting list secara massal untuk bulan magang yang sudah dimulai (AC-04-7).
     */
    public function tutupAntrean(BulanMagang $bulanMagang, Request $request): RedirectResponse
    {
        $hariIni = today('Asia/Jakarta');
        $tglMulaiBulan = Carbon::parse($bulanMagang->bulan_mulai . '-01', 'Asia/Jakarta');

        if ($hariIni->lt($tglMulaiBulan)) {
            return back()->with('error', 'Penutupan antrean massal hanya dapat dilakukan untuk bulan magang yang sudah dimulai.');
        }

        $admin = $request->user();
        $catatanOtomatis = 'Periode pelaksanaan magang telah dimulai. Antrean waiting list ditutup secara otomatis.';

        $jumlahDitutup = DB::transaction(function () use ($bulanMagang, $admin, $catatanOtomatis) {
            $antrean = Pendaftaran::where('bulan_magang_id', $bulanMagang->id)
                ->where('status', StatusPendaftaranEnum::WaitingList)
                ->lockForUpdate()
                ->get();

            foreach ($antrean as $pendaftaran) {
                $pendaftaran->update([
                    'status' => StatusPendaftaranEnum::Ditolak,
                    'catatan' => $catatanOtomatis,
                    'verifikator_id' => $admin->id,
                    'diputuskan_at' => now(),
                ]);

                $this->auditService->catat(
                    tipe: TipeAuditEnum::Aksi,
                    tabel: 'pendaftaran',
                    idData: $pendaftaran->id,
                    nilaiLama: ['status' => StatusPendaftaranEnum::WaitingList->value],
                    nilaiBaru: ['status' => StatusPendaftaranEnum::Ditolak->value],
                    detail: [
                        'aksi' => 'Penutupan antrean waiting list massal awal bulan',
                        'bulan_magang_id' => $bulanMagang->id,
                        'admin_id' => $admin->id,
                    ]
                );
            }

            return $antrean->count();
        });

        return redirect()->route('admin.waiting-list.index')
            ->with('success', "Sebanyak {$jumlahDitutup} pendaftaran waiting list untuk bulan {$bulanMagang->bulan_mulai} berhasil ditutup.");
    }
}
