<?php

declare(strict_types=1);

namespace App\Http\Controllers\Peserta;

use App\Enums\StatusPenempatanEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePasswordSettingRequest;
use App\Models\Penempatan;
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
     * Menampilkan halaman Pengaturan / Akun Saya peserta.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $profil = $user->profil;

        // Ambil penempatan aktif jika ada
        $penempatan = Penempatan::with(['bidang', 'pembimbing'])
            ->where('peserta_id', $user->id)
            ->where('status', StatusPenempatanEnum::Aktif)
            ->first();

        // Metadata sesi & format waktu
        $terakhirMasuk = Carbon::now()->translatedFormat('l, d F Y - H:i');

        // Preferensi tema tersimpan di session / cookie
        $temaAktif = $request->hasSession() ? $request->session()->get('tema_tampilan', 'terang') : ($request->cookie('tema_tampilan') ?? 'terang');
        $bahasaAktif = $request->hasSession() ? $request->session()->get('bahasa_portal', 'id') : 'id';

        return view('peserta.pengaturan.index', [
            'user' => $user,
            'profil' => $profil,
            'penempatan' => $penempatan,
            'terakhirMasuk' => $terakhirMasuk,
            'temaAktif' => $temaAktif,
            'bahasaAktif' => $bahasaAktif,
        ]);
    }

    /**
     * Memproses penggantian kata sandi mandiri peserta.
     */
    public function updatePassword(UpdatePasswordSettingRequest $request): RedirectResponse
    {
        $user = $request->user();

        // 1. Cek kata sandi lama cocok
        if (! Hash::check($request->input('current_password'), $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'Kata sandi saat ini yang Anda masukkan tidak sesuai.',
            ]);
        }

        // 2. Cegah penggunaan kata sandi yang persis sama dengan yang lama
        if (Hash::check($request->input('password'), $user->password)) {
            throw ValidationException::withMessages([
                'password' => 'Kata sandi baru tidak boleh sama persis dengan kata sandi saat ini.',
            ]);
        }

        // 3. Simpan kata sandi baru
        $user->forceFill([
            'password' => Hash::make($request->input('password')),
            'must_change_password' => false,
        ])->save();

        // 4. Catat peristiwa audit log keamanan
        $this->auditService->auth($user, 'ganti_password_mandiri', [
            'keterangan' => 'Peserta mengganti kata sandi melalui menu Pengaturan Akun',
            'ip' => $request->ip(),
        ]);

        return redirect()->route('peserta.pengaturan.index')
            ->with('success', 'Kata sandi berhasil diperbarui.');
    }

    /**
     * Memproses penyimpanan preferensi tema dan bahasa portal peserta.
     */
    public function updatePreferensi(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'bahasa' => ['nullable', 'string', 'in:id,en'],
            'tema' => ['nullable', 'string', 'in:terang,gelap,sistem'],
        ]);

        $tema = $validated['tema'] ?? 'terang';
        $bahasa = $validated['bahasa'] ?? 'id';

        if ($request->hasSession()) {
            $request->session()->put('tema_tampilan', $tema);
            $request->session()->put('bahasa_portal', $bahasa);
        }

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json(['success' => true, 'tema' => $tema, 'bahasa' => $bahasa])
                ->withCookie(cookie()->forever('tema_tampilan', $tema))
                ->withCookie(cookie()->forever('bahasa_portal', $bahasa));
        }

        return redirect()->route('peserta.pengaturan.index')
            ->with('success', 'Preferensi berhasil disimpan.')
            ->withCookie(cookie()->forever('tema_tampilan', $tema))
            ->withCookie(cookie()->forever('bahasa_portal', $bahasa));
    }
}
