<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Enums\RoleEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\TipeAuditEnum;
use App\Http\Controllers\Controller;
use App\Models\Bidang;
use App\Models\Penempatan;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StafController extends Controller
{
    public function __construct(
        protected AuditService $auditService
    ) {}

    /**
     * Menampilkan daftar akun staf (Admin & Pembimbing) (AC-11-3).
     */
    public function index(Request $request): View
    {
        $query = User::with('bidang')
            ->withCount(['penempatanBimbingan as mentee_aktif_count' => function ($q) {
                $q->where('status', StatusPenempatanEnum::Aktif);
            }])
            ->whereIn('role', [RoleEnum::Admin->value, RoleEnum::Pembimbing->value])
            ->orderBy('nama', 'asc');

        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }

        if ($request->filled('status_aktif')) {
            $query->where('status_aktif', filter_var($request->input('status_aktif'), FILTER_VALIDATE_BOOLEAN));
        }

        if ($request->filled('search')) {
            $search = '%' . $request->input('search') . '%';
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', $search)
                  ->orWhere('email', 'like', $search);
            });
        }

        $stafList = $query->paginate(15);
        $maksPeserta = \App\Support\Pengaturan::int('maks_peserta_aktif_per_pembimbing', 0);

        return view('superadmin.staf.index', [
            'stafList' => $stafList,
            'bidangList' => Bidang::aktif()->get(),
            'maksPeserta' => $maksPeserta,
        ]);
    }

    /**
     * Membuat akun staf baru oleh SuperAdmin (AC-11-3).
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', Rule::in([RoleEnum::Admin->value, RoleEnum::Pembimbing->value])],
            'bidang_id' => ['nullable', 'exists:bidang,id'],
            'password' => ['required', 'string', 'min:8', 'regex:/^(?=.*[a-zA-Z])(?=.*\d)/'],
        ], [
            'password.regex' => 'Kata sandi harus mengandung kombinasi huruf dan angka.',
        ]);

        $staf = new User();
        $staf->nama = $validated['nama'];
        $staf->email = $validated['email'];
        $staf->password = Hash::make($validated['password']);
        $staf->role = RoleEnum::from($validated['role']);
        $staf->bidang_id = ! empty($validated['bidang_id']) ? (int) $validated['bidang_id'] : null;
        $staf->email_verified_at = now();
        $staf->must_change_password = true;
        $staf->status_aktif = true;
        $staf->dibuat_oleh = $request->user()->id;
        $staf->save();

        $this->auditService->catat(
            tipe: TipeAuditEnum::Aksi,
            tabel: 'users',
            idData: $staf->id,
            nilaiBaru: [
                'nama' => $staf->nama,
                'email' => $staf->email,
                'role' => $staf->role->value,
                'status_aktif' => true,
                'must_change_password' => true,
            ],
            detail: [
                'aksi' => 'Membuat akun staf baru (' . $staf->role->label() . ')',
                'superadmin_id' => $request->user()->id,
            ]
        );

        return redirect()->route('superadmin.staf.index')
            ->with('success', 'Akun staf berhasil dibuat. Serahkan kata sandi awal kepada staf secara manual.');
    }

    /**
     * Memperbarui data profil staf oleh SuperAdmin (AC-11-1).
     */
    public function update(User $staf, Request $request): RedirectResponse
    {
        if ($staf->isSuperAdmin()) {
            return back()->with('error', 'Akun SuperAdmin tidak dapat diubah melalui menu ini.');
        }

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($staf->id)],
            'role' => ['required', Rule::in([RoleEnum::Admin->value, RoleEnum::Pembimbing->value])],
            'bidang_id' => ['nullable', 'exists:bidang,id'],
        ]);

        $nilaiLama = [
            'nama' => $staf->nama,
            'email' => $staf->email,
            'role' => $staf->role->value,
            'bidang_id' => $staf->bidang_id,
        ];

        $staf->nama = $validated['nama'];
        $staf->email = $validated['email'];
        $staf->role = RoleEnum::from($validated['role']);
        $staf->bidang_id = ! empty($validated['bidang_id']) ? (int) $validated['bidang_id'] : null;
        $staf->save();

        $this->auditService->catat(
            tipe: TipeAuditEnum::Aksi,
            tabel: 'users',
            idData: $staf->id,
            nilaiLama: $nilaiLama,
            nilaiBaru: [
                'nama' => $staf->nama,
                'email' => $staf->email,
                'role' => $staf->role->value,
                'bidang_id' => $staf->bidang_id,
            ],
            detail: [
                'aksi' => 'Memperbarui data profil staf (' . $staf->role->label() . ')',
                'superadmin_id' => $request->user()->id,
            ]
        );

        return redirect()->route('superadmin.staf.index')
            ->with('success', "Data profil staf {$staf->nama} berhasil diperbarui.");
    }

    /**
     * Mereset kata sandi staf oleh SuperAdmin (AC-11-4).
     */
    public function resetPassword(User $staf, Request $request): RedirectResponse
    {
        if ($staf->isSuperAdmin() || $staf->isPeserta()) {
            return back()->with('error', 'Reset kata sandi via menu ini hanya berlaku untuk Admin dan Pembimbing.');
        }

        $validated = $request->validate([
            'password_baru' => ['required', 'string', 'min:8', 'regex:/^(?=.*[a-zA-Z])(?=.*\d)/'],
        ], [
            'password_baru.regex' => 'Kata sandi harus mengandung kombinasi huruf dan angka.',
        ]);

        $staf->update([
            'password' => Hash::make($validated['password_baru']),
            'must_change_password' => true,
            'remember_token' => null,
        ]);

        if (\Illuminate\Support\Facades\Schema::hasTable('sessions')) {
            \Illuminate\Support\Facades\DB::table('sessions')->where('user_id', $staf->id)->delete();
        }

        $this->auditService->catat(
            tipe: TipeAuditEnum::Aksi,
            tabel: 'users',
            idData: $staf->id,
            detail: [
                'aksi' => 'Reset kata sandi staf secara manual dan invalidasi sesi aktif (SEC-04)',
                'staf_email' => $staf->email,
                'superadmin_id' => $request->user()->id,
            ]
        );

        return redirect()->route('superadmin.staf.index')
            ->with('success', 'Kata sandi staf berhasil direset. Sesi aktif pengguna telah dicabut dan wajib mengganti kata sandi saat login berikutnya.');
    }

    /**
     * Mengubah status aktif akun staf (AC-11-4, SEC-03, SEC-16).
     */
    public function toggleAktif(User $staf, Request $request): RedirectResponse
    {
        if ($staf->id === $request->user()->id) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
        }

        // Penegakan SEC-16: Penjaga SuperAdmin Terakhir
        if ($staf->isSuperAdmin()) {
            $activeSuperAdminCount = User::where('role', RoleEnum::SuperAdmin)
                ->where('status_aktif', true)
                ->count();

            if ($activeSuperAdminCount <= 1) {
                return back()->with('error', 'Penonaktifan ditolak (SEC-16): Sistem harus memiliki minimal satu akun SuperAdmin aktif.');
            }
        }

        $statusLama = $staf->status_aktif;
        $statusBaru = ! $statusLama;

        // Penegakan Aturan Bisnis BR-12 & AC-11-2 jika hendak menonaktifkan staf pembimbing
        if (! $statusBaru && $staf->role === RoleEnum::Pembimbing) {
            $hasActiveMentees = Penempatan::where('pembimbing_id', $staf->id)
                ->where('status', StatusPenempatanEnum::Aktif)
                ->exists();

            if ($hasActiveMentees) {
                return back()->with('error', 'Penonaktifan ditolak (BR-12): Pembimbing masih memiliki penempatan peserta aktif. Pindahkan penempatan terlebih dahulu.');
            }

            $activePembimbingCount = User::where('role', RoleEnum::Pembimbing)
                ->where('status_aktif', true)
                ->count();

            if ($activePembimbingCount <= 1) {
                return back()->with('error', 'Penonaktifan ditolak (BR-12): Tidak dapat menonaktifkan satu-satunya pembimbing aktif dalam sistem.');
            }
        }

        $staf->status_aktif = $statusBaru;
        if (! $statusBaru) {
            $staf->remember_token = null; // Revoke remember token (SEC-03)
            if (\Illuminate\Support\Facades\Schema::hasTable('sessions')) {
                \Illuminate\Support\Facades\DB::table('sessions')->where('user_id', $staf->id)->delete();
            }
        }
        $staf->save();

        $this->auditService->catat(
            tipe: TipeAuditEnum::Aksi,
            tabel: 'users',
            idData: $staf->id,
            nilaiLama: ['status_aktif' => $statusLama],
            nilaiBaru: ['status_aktif' => $statusBaru],
            detail: [
                'aksi' => $statusBaru ? 'Mengaktifkan kembali akun staf' : 'Menonaktifkan akun staf dan invalidasi sesi aktif (SEC-03)',
                'superadmin_id' => $request->user()->id,
            ]
        );

        $statusLabel = $statusBaru ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('superadmin.staf.index')
            ->with('success', "Akun staf {$staf->nama} berhasil {$statusLabel}.");
    }
}
