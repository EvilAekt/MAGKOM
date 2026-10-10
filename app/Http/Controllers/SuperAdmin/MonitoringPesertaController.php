<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Enums\RoleEnum;
use App\Enums\StatusPenempatanEnum;
use App\Http\Controllers\Controller;
use App\Models\Jurusan;
use App\Models\Penempatan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MonitoringPesertaController extends Controller
{
    /**
     * Menampilkan daftar peserta magang yang sedang aktif menjalani bimbingan (PRD 3.3, AC-07-7).
     */
    public function index(Request $request): View
    {
        $query = Penempatan::with([
            'peserta.profil.jurusan',
            'pembimbing',
            'bidang',
            'kuota.bulanMagang',
            'kuota.jurusan',
            'pendaftaran.bulanMagang',
        ])
        ->where('status', StatusPenempatanEnum::Aktif)
        ->orderBy('tgl_mulai', 'desc');

        if ($request->filled('pembimbing_id')) {
            $query->where('pembimbing_id', $request->integer('pembimbing_id'));
        }

        if ($request->filled('jurusan_id')) {
            $jurusanId = $request->integer('jurusan_id');
            $query->whereHas('peserta.profil', function ($q) use ($jurusanId) {
                $q->where('jurusan_id', $jurusanId);
            });
        }

        if ($request->filled('search')) {
            $search = '%' . trim((string) $request->input('search')) . '%';
            $query->where(function ($q) use ($search) {
                $q->whereHas('peserta', function ($qp) use ($search) {
                    $qp->where('nama', 'like', $search)
                        ->orWhere('email', 'like', $search)
                        ->orWhereHas('profil', function ($qprof) use ($search) {
                            $qprof->where('nim_nisn', 'like', $search)
                                ->orWhere('institusi', 'like', $search);
                        });
                });
            });
        }

        $penempatanList = $query->paginate(15)->withQueryString();

        $pembimbingList = User::where('role', RoleEnum::Pembimbing)
            ->where('status_aktif', true)
            ->orderBy('nama')
            ->get();

        $jurusanList = Jurusan::where('aktif', true)->orderBy('nama_jurusan')->get();

        return view('superadmin.monitoring.index', [
            'penempatanList' => $penempatanList,
            'pembimbingList' => $pembimbingList,
            'jurusanList' => $jurusanList,
        ]);
    }

    /**
     * Menampilkan detail pemantauan bimbingan peserta aktif secara baca-saja (AC-08-5, AC-10-7).
     */
    public function show(Penempatan $penempatan): View
    {
        $penempatan->load([
            'peserta.profil.jurusan',
            'pembimbing',
            'bidang',
            'kuota.bulanMagang',
            'kuota.jurusan',
            'pendaftaran.bulanMagang',
            'tugas.progresLogs',
            'logbooks',
            'penilaian.details.rubrik',
            'penilaian.riwayat',
        ]);

        return view('superadmin.monitoring.show', [
            'penempatan' => $penempatan,
        ]);
    }
}
