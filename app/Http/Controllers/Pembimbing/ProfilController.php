<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pembimbing;

use App\Enums\StatusPenempatanEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\StaffProfilRequest;
use App\Models\Penempatan;
use App\Support\Pengaturan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfilController extends Controller
{
    /**
     * Menampilkan halaman profil Pembimbing.
     */
    public function show(Request $request): View
    {
        $user = $request->user()->loadMissing('bidang');

        // Statistik bimbingan
        $menteeAktifCount = Penempatan::where('pembimbing_id', $user->id)
            ->where('status', StatusPenempatanEnum::Aktif)
            ->count();

        $menteeSelesaiCount = Penempatan::where('pembimbing_id', $user->id)
            ->where('status', StatusPenempatanEnum::Selesai)
            ->count();

        $maksPeserta = Pengaturan::int('maks_peserta_aktif_per_pembimbing', 5);

        return view('pembimbing.profil.show', [
            'user' => $user,
            'bidang' => $user->bidang,
            'menteeAktifCount' => $menteeAktifCount,
            'menteeSelesaiCount' => $menteeSelesaiCount,
            'maksPeserta' => $maksPeserta,
        ]);
    }

    /**
     * Memproses pembaruan data profil dan pasfoto Pembimbing.
     */
    public function update(StaffProfilRequest $request): RedirectResponse
    {
        $user = $request->user();

        // Tangani unggah atau penghapusan pasfoto
        if ($request->boolean('hapus_foto')) {
            if ($user->foto && Storage::disk('public')->exists($user->foto)) {
                Storage::disk('public')->delete($user->foto);
            }
            $user->foto = null;
        } elseif ($request->hasFile('foto')) {
            if ($user->foto && Storage::disk('public')->exists($user->foto)) {
                Storage::disk('public')->delete($user->foto);
            }
            $file = $request->file('foto');
            $extension = $file->getClientOriginalExtension() ?: 'jpg';
            $filename = 'staf_'.$user->id.'_'.time().'_'.bin2hex(random_bytes(4)).'.'.$extension;
            $path = $file->storeAs('profil-staf', $filename, 'public');
            $user->foto = $path;
        }

        $user->nama = $request->validated('nama');
        $user->no_telp = $request->validated('no_telp');
        $user->save();

        return redirect()->route('pembimbing.profil.show')
            ->with('success', 'Profil Pembimbing Lapangan berhasil diperbarui.');
    }
}
