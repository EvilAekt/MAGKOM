<?php

declare(strict_types=1);

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Http\Requests\PesertaProfilRequest;
use App\Models\Jurusan;
use App\Models\PesertaProfil;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfilController extends Controller
{
    public function show(Request $request): View
    {
        $user = $request->user();
        $profil = $user->profil;
        $jurusanList = Jurusan::where('aktif', true)->orderBy('nama_jurusan')->get();

        $penempatan = \App\Models\Penempatan::with(['bidang', 'pembimbing'])
            ->where('peserta_id', $user->id)
            ->where('status', \App\Enums\StatusPenempatanEnum::Aktif)
            ->first();

        $checklist = [
            'data_diri' => !empty($user->nama || $user->name) && !empty($user->email),
            'foto'      => !empty($profil?->foto),
            'akademik'  => !empty($profil?->institusi) && !empty($profil?->nim_nisn) && !empty($profil?->jenjang) && !empty($profil?->jurusan_id),
            'kontak'    => !empty($profil?->no_hp ?? $user->no_telp),
            'darurat'   => !empty($profil?->nama_kontak_darurat) && !empty($profil?->no_kontak_darurat),
        ];

        $totalWajib = count($checklist);
        $terisi = count(array_filter($checklist));
        $persentaseLengkap = (int) round(($terisi / $totalWajib) * 100);

        return view('peserta.profil.show', [
            'user'              => $user,
            'profil'            => $profil,
            'jurusanList'       => $jurusanList,
            'penempatan'        => $penempatan,
            'persentaseLengkap' => $persentaseLengkap,
            'checklist'         => $checklist,
        ]);
    }

    public function simpan(PesertaProfilRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();
        $profil = $user->profil;

        // Tangani unggah atau penghapusan berkas foto profil
        if ($request->boolean('hapus_foto')) {
            if ($profil?->foto && Storage::disk('public')->exists($profil->foto)) {
                Storage::disk('public')->delete($profil->foto);
            }
            $validated['foto'] = null;
        } elseif ($request->hasFile('foto')) {
            if ($profil?->foto && Storage::disk('public')->exists($profil->foto)) {
                Storage::disk('public')->delete($profil->foto);
            }
            $file = $request->file('foto');
            $extension = $file->getClientOriginalExtension() ?: 'jpg';
            $filename = 'peserta_' . $user->id . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
            $path = $file->storeAs('profil-peserta', $filename, 'public');
            $validated['foto'] = $path;
        } else {
            // Hindari menimpa kolom foto jika tidak ada file baru yang diunggah
            unset($validated['foto']);
        }

        unset($validated['hapus_foto']);

        $profil = PesertaProfil::updateOrCreate(
            ['user_id' => $user->id],
            $validated
        );

        return redirect()->route('peserta.profil.show')
            ->with('success', 'Biodata profil peserta berhasil disimpan.');
    }
}
