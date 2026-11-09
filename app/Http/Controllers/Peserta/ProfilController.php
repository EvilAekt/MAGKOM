<?php

declare(strict_types=1);

namespace App\Http\Controllers\Peserta;

use App\Enums\StatusPendaftaranEnum;
use App\Enums\StatusPenempatanEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\PesertaProfilRequest;
use App\Models\Jurusan;
use App\Models\Penempatan;
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
        $user->loadMissing(['kelompokMagang.anggota']);
        $profil = $user->profil;
        $jurusanList = Jurusan::where('aktif', true)->orderBy('nama_jurusan')->get();

        $kelompokMagang = $user->kelompokMagang;
        if (! $kelompokMagang) {
            $kelompokMagang = $user->pendaftaran()
                ->whereNotNull('kelompok_magang_id')
                ->with('kelompokMagang.anggota')
                ->latest()
                ->first()?->kelompokMagang;
        }

        $penempatan = Penempatan::with(['bidang', 'pembimbing'])
            ->where('peserta_id', $user->id)
            ->where('status', StatusPenempatanEnum::Aktif)
            ->first();

        $pendaftaranAktif = $user->pendaftaran()
            ->whereIn('status', [
                StatusPendaftaranEnum::Diajukan,
                StatusPendaftaranEnum::WaitingList,
                StatusPendaftaranEnum::Diterima,
            ])
            ->latest()
            ->first();

        // Data registrasi akademik (Institusi, NIM/NISN, Jenjang, Jurusan) terkunci permanen jika profil sudah ada
        $isAkademikLocked = (bool) ($profil && $profil->exists && ! empty($profil->institusi));

        $kelengkapan = $profil?->hitungKelengkapan() ?? [
            'checklist' => [
                'data_diri' => ! empty($user->nama || $user->name) && ! empty($user->email),
                'foto' => false,
                'akademik' => false,
                'kontak' => false,
                'darurat' => false,
            ],
            'persentase' => 0,
            'is_lengkap' => false,
        ];

        $checklist = $kelengkapan['checklist'];
        $persentaseLengkap = $kelengkapan['persentase'];

        return view('peserta.profil.show', [
            'user' => $user,
            'profil' => $profil,
            'jurusanList' => $jurusanList,
            'penempatan' => $penempatan,
            'persentaseLengkap' => $persentaseLengkap,
            'checklist' => $checklist,
            'kelompokMagang' => $kelompokMagang,
            'isAkademikLocked' => $isAkademikLocked,
        ]);
    }

    public function simpan(PesertaProfilRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();
        $profil = $user->profil;

        // Data registrasi awal peserta (Institusi, NIM/NISN, Jurusan, Jenjang) bersifat permanen / immutable
        if ($profil && $profil->exists && ! empty($profil->institusi)) {
            unset($validated['institusi'], $validated['nim_nisn'], $validated['jurusan_id'], $validated['jenjang']);
        }

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
            $filename = 'peserta_'.$user->id.'_'.time().'_'.bin2hex(random_bytes(4)).'.'.$extension;
            $path = $file->storeAs('profil-peserta', $filename, 'public');
            $validated['foto'] = $path;
        } else {
            // Hindari menimpa kolom foto jika tidak ada file baru yang diunggah
            unset($validated['foto']);
        }

        unset($validated['hapus_foto']);

        if ($request->boolean('persetujuan_wali')) {
            $validated['persetujuan_wali_at'] = now();
        }
        unset($validated['persetujuan_wali']);

        if (isset($validated['email'])) {
            $newEmail = trim($validated['email']);
            if ($newEmail !== '' && $newEmail !== $user->email) {
                $user->email = $newEmail;
                $user->save();
            }
            unset($validated['email']);
        }

        $profil = PesertaProfil::updateOrCreate(
            ['user_id' => $user->id],
            $validated
        );

        return redirect()->route('peserta.profil.show')
            ->with('success', 'Biodata profil peserta berhasil disimpan.');
    }
}
