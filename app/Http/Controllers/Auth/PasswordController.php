<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Enums\TipeAuditEnum;
use App\Http\Controllers\Controller;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PasswordController extends Controller
{
    public function __construct(
        protected AuditService $auditService
    ) {}

    /**
     * Menampilkan form ganti kata sandi wajib staf (AC-11-5, BR-15).
     */
    public function showGanti(): View
    {
        return view('auth.ganti-password');
    }

    /**
     * Memproses penggantian kata sandi (AC-11-5, BR-15).
     */
    public function updateGanti(Request $request): RedirectResponse
    {
        $user = $request->user();

        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed', 'regex:/^(?=.*[a-zA-Z])(?=.*\d)/'],
        ], [
            'password.regex' => 'Kata sandi baru harus mengandung kombinasi huruf dan angka.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
        ]);

        // 1. Cek kata sandi lama cocok
        if (! Hash::check($request->input('current_password'), $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'Kata sandi lama yang Anda masukkan tidak sesuai.',
            ]);
        }

        // 2. Cegah penggunaan kata sandi yang persis sama dengan yang lama (AC-11-5)
        if (Hash::check($request->input('password'), $user->password)) {
            throw ValidationException::withMessages([
                'password' => 'Kata sandi baru tidak boleh sama persis dengan kata sandi sebelumnya.',
            ]);
        }

        // 3. Simpan kata sandi baru dan matikan flag must_change_password
        $user->forceFill([
            'password' => Hash::make($request->input('password')),
            'must_change_password' => false,
        ])->save();

        // 4. Catat jejak audit
        $this->auditService->catat(
            tipe: TipeAuditEnum::Auth,
            tabel: 'users',
            idData: $user->id,
            detail: [
                'aksi' => 'Pengguna berhasil mengganti kata sandi wajib',
                'user_id' => $user->id,
            ]
        );

        // Arahkan sesuai peran pengguna
        if ($user->isSuperAdmin()) {
            return redirect()->route('superadmin.bulan.index')->with('success', 'Kata sandi berhasil diperbarui.');
        }

        if ($user->isAdmin()) {
            return redirect()->route('admin.pendaftaran.index')->with('success', 'Kata sandi berhasil diperbarui.');
        }

        if ($user->isPembimbing()) {
            return redirect()->route('pembimbing.tugas.index')->with('success', 'Kata sandi berhasil diperbarui.');
        }

        return redirect()->route('peserta.pendaftaran.index')->with('success', 'Kata sandi berhasil diperbarui.');
    }
}
