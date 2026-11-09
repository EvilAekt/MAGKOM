<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Enums\RoleEnum;
use App\Enums\TipeAuditEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\SuperAdmin\StorePermissionRequest;
use App\Http\Requests\SuperAdmin\UpdateRoleMatrixRequest;
use App\Models\Permission;
use App\Models\RolePermission;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function __construct(
        protected AuditService $auditService
    ) {}

    /**
     * Menampilkan daftar role dan matriks hak akses dinamis (SA-02 Role, PRD Bab 3.1 & 3.3).
     */
    public function index(Request $request): View
    {
        // Hitung pengguna per role
        $roleCounts = [
            RoleEnum::SuperAdmin->value => User::where('role', RoleEnum::SuperAdmin)->count(),
            RoleEnum::Admin->value => User::where('role', RoleEnum::Admin)->count(),
            RoleEnum::Pembimbing->value => User::where('role', RoleEnum::Pembimbing)->count(),
            RoleEnum::Peserta->value => User::where('role', RoleEnum::Peserta)->count(),
        ];

        $totalPengguna = User::count();

        // Ambil semua permissions beserta relasi rolePermissions yang aktif
        $permissions = Permission::with('rolePermissions')
            ->orderBy('urutan')
            ->orderBy('id')
            ->get();

        return view('superadmin.role.index', [
            'roleCounts' => $roleCounts,
            'totalPengguna' => $totalPengguna,
            'permissions' => $permissions,
        ]);
    }

    /**
     * Menyimpan hak akses / modul baru ke sistem.
     */
    public function storePermission(StorePermissionRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $roles = $validated['roles'] ?? [];
        unset($validated['roles']);

        DB::transaction(function () use ($validated, $roles): void {
            // Tentukan urutan tertinggi
            $maxUrutan = (int) Permission::max('urutan');
            $validated['urutan'] = $maxUrutan + 1;
            $validated['is_system'] = false;

            /** @var Permission $permission */
            $permission = Permission::create($validated);

            foreach ($roles as $role) {
                RolePermission::create([
                    'role' => $role,
                    'permission_id' => $permission->id,
                ]);
            }

            // Bersihkan cache wewenang role
            $this->clearPermissionsCache();

            // Catat ke audit trail
            $this->auditService->catat(
                tipe: TipeAuditEnum::Aksi,
                tabel: 'permissions',
                idData: $permission->id,
                nilaiBaru: [
                    'nama_modul' => $permission->nama_modul,
                    'slug' => $permission->slug,
                    'kategori' => $permission->kategori,
                    'assigned_roles' => $roles,
                ],
                detail: [
                    'aksi' => 'TAMBAH_HAK_AKSES_MODUL',
                ]
            );
        });

        return redirect()->route('superadmin.role.index')
            ->with('success', 'Hak akses modul baru berhasil ditambahkan.');
    }

    /**
     * Memperbarui matriks hak akses seluruh role (General/Global update).
     */
    public function updateMatrix(UpdateRoleMatrixRequest $request): RedirectResponse
    {
        $matrixData = $request->input('matrix', []);
        $allRoles = [
            RoleEnum::SuperAdmin->value,
            RoleEnum::Admin->value,
            RoleEnum::Pembimbing->value,
            RoleEnum::Peserta->value,
        ];

        DB::transaction(function () use ($matrixData, $allRoles): void {
            $allPermissions = Permission::all();

            foreach ($allPermissions as $permission) {
                $permissionId = (int) $permission->id;
                $submittedRolesForPerm = isset($matrixData[$permissionId]) && is_array($matrixData[$permissionId])
                    ? array_keys($matrixData[$permissionId])
                    : [];

                // Core Invariant Protection: SuperAdmin tidak boleh kehilangan hak pada modul sistem kritis
                if ($permission->is_system && in_array($permission->slug, [
                    'keamanan.audit-trail',
                    'staf.manajemen',
                    'konfigurasi.parameter',
                ], true)) {
                    if (! in_array(RoleEnum::SuperAdmin->value, $submittedRolesForPerm, true)) {
                        $submittedRolesForPerm[] = RoleEnum::SuperAdmin->value;
                    }
                }

                // Ambil relasi eksisting
                $existingRoles = RolePermission::where('permission_id', $permissionId)->pluck('role')->toArray();

                // Role yang perlu ditambah
                $rolesToAdd = array_diff($submittedRolesForPerm, $existingRoles);
                foreach ($rolesToAdd as $role) {
                    if (in_array($role, $allRoles, true)) {
                        RolePermission::firstOrCreate([
                            'role' => $role,
                            'permission_id' => $permissionId,
                        ]);
                    }
                }

                // Role yang perlu dicabut
                $rolesToRemove = array_diff($existingRoles, $submittedRolesForPerm);
                if (! empty($rolesToRemove)) {
                    RolePermission::where('permission_id', $permissionId)
                        ->whereIn('role', $rolesToRemove)
                        ->delete();
                }
            }

            // Bersihkan cache wewenang
            $this->clearPermissionsCache();

            // Catat ke audit log
            $this->auditService->catat(
                tipe: TipeAuditEnum::Aksi,
                tabel: 'role_permissions',
                idData: null,
                detail: [
                    'aksi' => 'UPDATE_MATRIKS_HAK_AKSES_ROLE',
                    'keterangan' => 'Pembaruan general matriks hak akses seluruh role pengguna.',
                ]
            );
        });

        return redirect()->route('superadmin.role.index')
            ->with('success', 'Matriks hak akses role berhasil diperbarui secara menyeluruh.');
    }

    /**
     * Menghapus hak akses modul kustom (non-system).
     */
    public function destroyPermission(Permission $permission): RedirectResponse
    {
        if ($permission->is_system) {
            abort(403, 'Modul bawaan sistem inti tidak dapat dihapus.');
        }

        $nama = $permission->nama_modul;
        $id = $permission->id;

        DB::transaction(function () use ($permission, $id, $nama): void {
            $permission->delete();

            $this->clearPermissionsCache();

            $this->auditService->catat(
                tipe: TipeAuditEnum::Aksi,
                tabel: 'permissions',
                idData: $id,
                nilaiLama: [
                    'nama_modul' => $nama,
                ],
                detail: [
                    'aksi' => 'HAPUS_HAK_AKSES_MODUL',
                ]
            );
        });

        return redirect()->route('superadmin.role.index')
            ->with('success', "Hak akses modul '{$nama}' berhasil dihapus.");
    }

    /**
     * Membersihkan cache hak akses untuk semua role.
     */
    private function clearPermissionsCache(): void
    {
        $roles = [
            RoleEnum::SuperAdmin->value,
            RoleEnum::Admin->value,
            RoleEnum::Pembimbing->value,
            RoleEnum::Peserta->value,
        ];

        foreach ($roles as $role) {
            Cache::forget("role_perms_{$role}");
        }
    }
}
