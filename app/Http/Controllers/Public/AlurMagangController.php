<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Jurusan;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class AlurMagangController extends Controller
{
    /**
     * Menampilkan halaman interaktif peta alur magang & panduan komprehensif.
     */
    public function __invoke(Request $request): View
    {
        // Ambil jurusan aktif untuk kebutuhan simulator jalur peserta
        $semuaJurusan = Jurusan::where('aktif', true)
            ->orderBy('kategori', 'asc')
            ->orderBy('nama_jurusan', 'asc')
            ->get();

        $jurusanSmk = $semuaJurusan->filter(fn (Jurusan $j) => $j->kategori === 'smk');
        $jurusanMahasiswa = $semuaJurusan->filter(fn (Jurusan $j) => $j->kategori === 'mahasiswa');

        return view('public.panduan-alur', compact('jurusanSmk', 'jurusanMahasiswa'));
    }

    /**
     * Menampilkan halaman penuh mandiri simulator peta alur magang (100vw x 100vh).
     */
    public function petaInteraktif(Request $request): View
    {
        return view('public.peta-alur-interaktif');
    }
}
