<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Enums\TipeAuditEnum;
use App\Http\Controllers\Controller;
use App\Services\AuditService;
use App\Support\PasswordPolicy;
use Illuminate\Http\JsonResponse;
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
    public function updateGanti(Request $request): RedirectResponse|JsonResponse
    {
        $user = $request->user();
        $isSuperAdmin = $user && method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin();
        $passwordRules = $isSuperAdmin ? PasswordPolicy::staffRules(confirmed: true) : PasswordPolicy::pesertaRules(confirmed: true);

        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => $passwordRules,
        ], array_merge([
            'password.min' => $isSuperAdmin ? 'Kata sandi baru SuperAdmin minimal 12 karakter dengan huruf besar, huruf kecil, angka, dan simbol.' : 'Kata sandi baru minimal 8 karakter.',
            'password.regex' => 'Kata sandi baru harus mengandung kombinasi huruf dan angka.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
        ], PasswordPolicy::messages()));

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

        // 3. Simpan kata sandi baru dan matikan flag must_change_password serta hapus password_awal
        $user->forceFill([
            'password' => Hash::make($request->input('password')),
            'must_change_password' => false,
            'password_awal' => null,
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

        $targetRedirect = match (true) {
            $user->isSuperAdmin() => route('superadmin.dashboard'),
            $user->isAdmin() => route('admin.pendaftaran.index'),
            $user->isPembimbing() => route('pembimbing.tugas.index'),
            default => route('peserta.pendaftaran.index'),
        };

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Kata sandi berhasil diperbarui.',
                'redirect' => $targetRedirect,
            ]);
        }

        return redirect()->to($targetRedirect)->with('success', 'Kata sandi berhasil diperbarui.');
    }
}
