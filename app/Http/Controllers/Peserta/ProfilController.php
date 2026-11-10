<?php

declare(strict_types=1);

namespace App\Http\Controllers\Peserta;

use App\Actions\Peserta\Profil\UpdatePesertaProfilAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\PesertaProfilRequest;
use App\Queries\Peserta\Profil\PesertaProfilQuery;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfilController extends Controller
{
    public function __construct(
        protected PesertaProfilQuery $profilQuery,
        protected UpdatePesertaProfilAction $updateAction
    ) {}

    /**
     * Menampilkan halaman biodata profil 360° peserta magang.
     */
    public function show(Request $request): View
    {
        $viewData = $this->profilQuery->getShowData($request->user());

        return view('peserta.profil.show', $viewData);
    }

    /**
     * Menyimpan pembaruan biodata dan foto profil peserta secara transaksional.
     */
    public function simpan(PesertaProfilRequest $request): RedirectResponse
    {
        $this->updateAction->execute(
            user: $request->user(),
            validatedData: $request->validated(),
            fotoFile: $request->file('foto'),
            hapusFoto: $request->boolean('hapus_foto'),
            persetujuanWali: $request->boolean('persetujuan_wali')
        );

        return redirect()->route('peserta.profil.show')
            ->with('success', 'Biodata profil peserta berhasil disimpan.');
    }
}
