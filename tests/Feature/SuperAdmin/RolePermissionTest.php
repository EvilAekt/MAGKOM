<?php

declare(strict_types=1);

namespace Tests\Feature\SuperAdmin;

use App\Enums\RoleEnum;
use App\Models\Permission;
use App\Models\RolePermission;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RolePermissionTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected User $admin;

    protected User $pembimbing;

    protected User $peserta;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PermissionSeeder::class);

        $this->superAdmin = User::factory()->create([
            'role' => RoleEnum::SuperAdmin,
            'status_aktif' => true,
        ]);

        $this->admin = User::factory()->create([
            'role' => RoleEnum::Admin,
            'status_aktif' => true,
        ]);

        $this->pembimbing = User::factory()->create([
            'role' => RoleEnum::Pembimbing,
            'status_aktif' => true,
        ]);

        $this->peserta = User::factory()->create([
            'role' => RoleEnum::Peserta,
            'status_aktif' => true,
        ]);
    }

    public function test_superadmin_can_view_role_and_permission_matrix(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->get(route('superadmin.role.index'));

        $response->assertOk();
        $response->assertSee('Hak Akses dan Role Pengguna');
        $response->assertSee('Cakupan: <strong>4 Role Sistem</strong>', false);
        $response->assertSee('SUPERADMIN');
        $response->assertSee('ADMINISTRASI');
        $response->assertSee('PEMBIMBING');
        $response->assertSee('PESERTA MAGANG');
        $response->assertSee('Tambah Hak Akses');
        $response->assertSee('Matriks Wewenang &amp; Hak Akses Modul Dinamis', false);
        $response->assertSee('Portal Publik &amp; Registrasi Calon Peserta', false);
        $response->assertDontSee('<th style="text-align: center; width: 80px;">Aksi</th>', false);
    }

    public function test_non_superadmin_is_forbidden_to_access_role_matrix(): void
    {
        $this->actingAs($this->admin)
            ->get(route('superadmin.role.index'))
            ->assertForbidden();

        $this->actingAs($this->pembimbing)
            ->get(route('superadmin.role.index'))
            ->assertForbidden();

        $this->actingAs($this->peserta)
            ->get(route('superadmin.role.index'))
            ->assertForbidden();
    }

    public function test_superadmin_can_store_new_custom_permission(): void
    {
        $payload = [
            'nama_modul' => 'Manajemen Sertifikat Elektronik',
            'slug' => 'sertifikat.elektronik',
            'kategori' => 'Penilaian',
            'deskripsi' => 'Pengelolaan penerbitan dan penandatanganan sertifikat kelulusan digital.',
            'roles' => ['superadmin', 'admin'],
        ];

        $response = $this->actingAs($this->superAdmin)
            ->post(route('superadmin.role.permission.store'), $payload);

        $response->assertRedirect(route('superadmin.role.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('permissions', [
            'nama_modul' => 'Manajemen Sertifikat Elektronik',
            'slug' => 'sertifikat.elektronik',
            'is_system' => false,
        ]);

        $permission = Permission::where('slug', 'sertifikat.elektronik')->firstOrFail();

        $this->assertDatabaseHas('role_permissions', [
            'role' => 'superadmin',
            'permission_id' => $permission->id,
        ]);

        $this->assertDatabaseHas('role_permissions', [
            'role' => 'admin',
            'permission_id' => $permission->id,
        ]);

        $this->assertDatabaseMissing('role_permissions', [
            'role' => 'peserta',
            'permission_id' => $permission->id,
        ]);

        // Verifikasi audit log
        $this->assertDatabaseHas('audit_log', [
            'aksi' => 'TAMBAH_HAK_AKSES_MODUL',
            'entitas' => 'permissions',
            'entitas_id' => $permission->id,
        ]);
    }

    public function test_superadmin_can_update_role_matrix_globally(): void
    {
        $permission = Permission::where('slug', 'kuota.manajemen')->firstOrFail();

        // Awalnya: superadmin dan admin punya akses, pembimbing dan peserta tidak punya
        $this->assertTrue($this->admin->hasPermission('kuota.manajemen'));
        $this->assertFalse($this->pembimbing->hasPermission('kuota.manajemen'));

        // Superadmin memberikan hak kuota.manajemen ke pembimbing juga
        $matrixPayload = [
            'matrix' => [
                $permission->id => [
                    'superadmin' => '1',
                    'admin' => '1',
                    'pembimbing' => '1',
                ],
            ],
        ];

        $response = $this->actingAs($this->superAdmin)
            ->put(route('superadmin.role.matrix.update'), $matrixPayload);

        $response->assertRedirect(route('superadmin.role.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('role_permissions', [
            'role' => 'pembimbing',
            'permission_id' => $permission->id,
        ]);

        // Verifikasi bahwa user pembimbing sekarang memiliki izin kuota.manajemen secara general
        $this->assertTrue($this->pembimbing->hasPermission('kuota.manajemen'));
    }

    public function test_cannot_delete_core_system_permission(): void
    {
        $systemPermission = Permission::where('is_system', true)->firstOrFail();

        $response = $this->actingAs($this->superAdmin)
            ->delete(route('superadmin.role.permission.destroy', $systemPermission));

        $response->assertForbidden();

        $this->assertDatabaseHas('permissions', [
            'id' => $systemPermission->id,
        ]);
    }

    public function test_superadmin_can_delete_custom_permission(): void
    {
        /** @var Permission $customPermission */
        $customPermission = Permission::create([
            'nama_modul' => 'Modul Kustom Sementara',
            'slug' => 'kustom.sementara',
            'kategori' => 'Umum',
            'is_system' => false,
        ]);

        RolePermission::create([
            'role' => 'admin',
            'permission_id' => $customPermission->id,
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->delete(route('superadmin.role.permission.destroy', $customPermission));

        $response->assertRedirect(route('superadmin.role.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('permissions', [
            'id' => $customPermission->id,
        ]);

        $this->assertDatabaseMissing('role_permissions', [
            'permission_id' => $customPermission->id,
        ]);
    }
}
