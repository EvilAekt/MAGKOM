<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\StatusPendaftaranEnum;
use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WaitingListController extends Controller
{
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
}
