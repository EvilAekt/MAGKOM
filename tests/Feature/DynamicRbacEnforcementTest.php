<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Models\Permission;
use App\Models\RolePermission;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class DynamicRbacEnforcementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $pembimbing;

    protected User $superadmin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PermissionSeeder::class);

        $this->admin = User::factory()->create([
            'role' => RoleEnum::Admin,
            'status_aktif' => true,
            'email_verified_at' => now(),
            'must_change_password' => false,
        ]);

        $this->pembimbing = User::factory()->create([
            'role' => RoleEnum::Pembimbing,
            'status_aktif' => true,
            'email_verified_at' => now(),
            'must_change_password' => false,
        ]);

        $this->superadmin = User::factory()->create([
            'role' => RoleEnum::SuperAdmin,
            'status_aktif' => true,
            'email_verified_at' => now(),
            'must_change_password' => false,
        ]);
    }

    /**
     * Memastikan Admin dapat mengakses rute pendaftaran saat permission seleksi.verifikasi-berkas aktif.
     */
    public function test_admin_can_access_pendaftaran_when_permission_granted(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.pendaftaran.index'));

        $response->assertOk();
    }

    /**
     * Memastikan Admin ditolak dengan HTTP 403 saat permission seleksi.verifikasi-berkas dicabut dari matriks.
     */
    public function test_admin_is_forbidden_when_permission_revoked_from_matrix(): void
    {
        // 1. Cabut permission 'seleksi.verifikasi-berkas' dari role Admin di database
        $permission = Permission::where('slug', 'seleksi.verifikasi-berkas')->firstOrFail();
        RolePermission::where('role', RoleEnum::Admin->value)
            ->where('permission_id', $permission->id)
            ->delete();

        // 2. Bersihkan cache wewenang (seperti yang dilakukan RoleController)
        Cache::forget('role_perms_admin');

        // 3. Akses rute pendaftaran harus ditolak 403
        $response = $this->actingAs($this->admin)->get(route('admin.pendaftaran.index'));

        $response->assertForbidden();

        // 4. Pastikan tercatat di audit log
        $this->assertDatabaseHas('audit_log', [
            'tipe' => 'auth',
            'entitas' => 'permissions',
        ]);
    }

    /**
     * Memastikan pemulihan permission di matriks langsung mengembalikan hak akses Admin.
     */
    public function test_admin_regains_access_when_permission_restored(): void
    {
        $permission = Permission::where('slug', 'seleksi.verifikasi-berkas')->firstOrFail();

        // Cabut
        RolePermission::where('role', RoleEnum::Admin->value)->where('permission_id', $permission->id)->delete();
        Cache::forget('role_perms_admin');
        $this->actingAs($this->admin)->get(route('admin.pendaftaran.index'))->assertForbidden();

        // Pulihkan
        RolePermission::create([
            'role' => RoleEnum::Admin->value,
            'permission_id' => $permission->id,
        ]);
        Cache::forget('role_perms_admin');

        $this->actingAs($this->admin)->get(route('admin.pendaftaran.index'))->assertOk();
    }

    /**
     * Memastikan SuperAdmin selalu memiliki akses administratif penuh terlepas dari konfigurasi role_permissions.
     */
    public function test_superadmin_retains_full_administrative_access(): void
    {
        // Meskipun seluruh role_permissions dihapus
        RolePermission::query()->delete();
        Cache::flush();

        $response = $this->actingAs($this->superadmin)->get(route('admin.pendaftaran.index'));

        $response->assertOk();
    }
}
