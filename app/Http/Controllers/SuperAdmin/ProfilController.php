<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StaffProfilRequest;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfilController extends Controller
{
    /**
     * Menampilkan halaman profil SuperAdmin.
     */
    public function show(Request $request): View
    {
        $user = $request->user();

        // Statistik eksekutif tata kelola
        $totalStafAktif = User::staf()->where('status_aktif', true)->count();
        $totalAuditTrail = AuditLog::count();

        return view('superadmin.profil.show', [
            'user' => $user,
            'totalStaf' => $totalStafAktif,
            'totalStafAktif' => $totalStafAktif,
            'totalAudit' => $totalAuditTrail,
            'totalAuditTrail' => $totalAuditTrail,
        ]);
    }

    /**
     * Memproses pembaruan data profil dan pasfoto SuperAdmin.
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

        return redirect()->route('superadmin.profil.show')
            ->with('success', 'Profil Super Administrator berhasil diperbarui.');
    }
}
