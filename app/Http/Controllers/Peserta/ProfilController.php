<?php

declare(strict_types=1);

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Http\Requests\PesertaProfilRequest;
use App\Models\Jurusan;
use App\Models\PesertaProfil;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfilController extends Controller
{
    public function show(Request $request): View
    {
        $user = $request->user();
        $profil = $user->profil;
        $jurusanList = Jurusan::where('aktif', true)->orderBy('nama_jurusan')->get();

        return view('peserta.profil.show', [
            'user' => $user,
            'profil' => $profil,
            'jurusanList' => $jurusanList,
        ]);
    }

    public function simpan(PesertaProfilRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        $profil = PesertaProfil::updateOrCreate(
            ['user_id' => $user->id],
            $validated
        );

        return redirect()->route('peserta.profil.show')
            ->with('success', 'Biodata profil peserta berhasil disimpan.');
    }
}
