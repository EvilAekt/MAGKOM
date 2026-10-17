<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pembimbing;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePasswordSettingRequest;
use App\Services\AuditService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PengaturanController extends Controller
{
    public function __construct(
        protected AuditService $auditService
    ) {}

    /**
     * Menampilkan halaman Pengaturan Akun Pembimbing.
     */
    public function index(Request $request): View
    {
        $user = $request->user()->loadMissing('bidang');

        $terakhirMasuk = Carbon::now()->translatedFormat('l, d F Y - H:i');
        $temaAktif = $request->hasSession() ? $request->session()->get('tema_tampilan', 'terang') : ($request->cookie('tema_tampilan') ?? 'terang');
        $bahasaAktif = $request->hasSession() ? $request->session()->get('bahasa_portal', 'id') : 'id';

        return view('pembimbing.pengaturan.index', [
            'user' => $user,
            'terakhirMasuk' => $terakhirMasuk,
            'temaAktif' => $temaAktif,
            'bahasaAktif' => $bahasaAktif,
        ]);
    }

    /**
     * Memproses penggantian kata sandi mandiri Pembimbing.
     */
    public function updatePassword(UpdatePasswordSettingRequest $request): RedirectResponse
    {
        $user = $request->user();

        if (! Hash::check($request->input('current_password'), $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'Kata sandi saat ini yang Anda masukkan tidak sesuai.',
            ]);
        }

        if (Hash::check($request->input('password'), $user->password)) {
            throw ValidationException::withMessages([
                'password' => 'Kata sandi baru tidak boleh sama persis dengan kata sandi saat ini.',
            ]);
        }

        $user->forceFill([
            'password' => Hash::make($request->input('password')),
            'must_change_password' => false,
        ])->save();

        $this->auditService->auth($user, 'ganti_password_mandiri', [
            'keterangan' => 'Pembimbing mengganti kata sandi melalui menu Pengaturan Akun',
            'ip' => $request->ip(),
        ]);

        return redirect()->route('pembimbing.pengaturan.index')
            ->with('success', 'Kata sandi akun Pembimbing berhasil diperbarui.');
    }

    /**
     * Memproses penyimpanan preferensi tema dan bahasa portal Pembimbing.
     */
    public function updatePreferensi(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'bahasa' => ['nullable', 'string', 'in:id,en'],
            'tema'   => ['nullable', 'string', 'in:terang,gelap,sistem'],
        ]);

        $tema = $validated['tema'] ?? 'terang';
        $bahasa = $validated['bahasa'] ?? 'id';

        if ($request->hasSession()) {
            $request->session()->put('tema_tampilan', $tema);
            $request->session()->put('bahasa_portal', $bahasa);
        }

        return redirect()->route('pembimbing.pengaturan.index')
            ->with('success', 'Preferensi antarmuka berhasil disimpan.')
            ->withCookie(cookie()->forever('tema_tampilan', $tema))
            ->withCookie(cookie()->forever('bahasa_portal', $bahasa));
    }
}
