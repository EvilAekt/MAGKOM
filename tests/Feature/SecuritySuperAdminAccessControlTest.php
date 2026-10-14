<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Models\User;
use Database\Seeders\PengaturanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecuritySuperAdminAccessControlTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $admin;
    protected User $pembimbing;
    protected User $peserta;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PengaturanSeeder::class);

        $this->superAdmin = User::factory()->create([
            'role' => RoleEnum::SuperAdmin,
            'status_aktif' => true,
            'must_change_password' => false,
        ]);

        $this->admin = User::factory()->create([
            'role' => RoleEnum::Admin,
            'status_aktif' => true,
            'must_change_password' => false,
        ]);

        $this->pembimbing = User::factory()->create([
            'role' => RoleEnum::Pembimbing,
            'status_aktif' => true,
            'must_change_password' => false,
        ]);

        $this->peserta = User::factory()->create([
            'role' => RoleEnum::Peserta,
            'status_aktif' => true,
            'must_change_password' => false,
        ]);
    }

    /**
     * UNATHENTICATED TEST: Tamu tanpa login ditolak dan diarahkan ke halaman login.
     */
    public function test_guests_cannot_access_superadmin_routes(): void
    {
        $superAdminRoutes = [
            route('superadmin.dashboard'),
            route('superadmin.pengaturan.index'),
            route('superadmin.staf.index'),
            route('superadmin.audit-log.index'),
            route('superadmin.backup.index'),
            route('superadmin.slot-khusus.index'),
        ];

        foreach ($superAdminRoutes as $url) {
            $this->get($url)->assertRedirect(route('login'));
        }
    }

    /**
     * VERTICAL PRIVILEGE ESCALATION: Peserta dilarang mengakses rute SuperAdmin (403).
     */
    public function test_peserta_is_forbidden_from_superadmin_routes(): void
    {
        $this->actingAs($this->peserta)
            ->get(route('superadmin.dashboard'))
            ->assertForbidden();

        $this->actingAs($this->peserta)
            ->get(route('superadmin.pengaturan.index'))
            ->assertForbidden();

        $this->actingAs($this->peserta)
            ->get(route('superadmin.staf.index'))
            ->assertForbidden();

        $this->actingAs($this->peserta)
            ->get(route('superadmin.audit-log.index'))
            ->assertForbidden();
    }

    /**
     * VERTICAL PRIVILEGE ESCALATION: Pembimbing dilarang mengakses rute SuperAdmin (403).
     */
    public function test_pembimbing_is_forbidden_from_superadmin_routes(): void
    {
        $this->actingAs($this->pembimbing)
            ->get(route('superadmin.dashboard'))
            ->assertForbidden();

        $this->actingAs($this->pembimbing)
            ->get(route('superadmin.pengaturan.index'))
            ->assertForbidden();

        $this->actingAs($this->pembimbing)
            ->get(route('superadmin.backup.index'))
            ->assertForbidden();
    }

    /**
     * ROLE SEPARATION: Admin operasional dilarang mengakses modul eksklusif SuperAdmin (403).
     */
    public function test_admin_is_forbidden_from_exclusive_superadmin_routes(): void
    {
        $this->actingAs($this->admin)
            ->get(route('superadmin.dashboard'))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->get(route('superadmin.pengaturan.index'))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->get(route('superadmin.staf.index'))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->get(route('superadmin.backup.index'))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->get(route('superadmin.audit-log.index'))
            ->assertForbidden();
    }

    /**
     * SUPERADMIN READ-ONLY ENFORCEMENT (SEC-12): SuperAdmin ditolak mengubah data pembimbing.
     */
    public function test_superadmin_is_strictly_read_only_on_mentor_mutating_actions(): void
    {
        // Mencoba POST pada route pembimbing -> wajib 403 Forbidden
        $this->actingAs($this->superAdmin)
            ->post(route('pembimbing.logbook.bulk-review'), [])
            ->assertForbidden();

        $this->actingAs($this->superAdmin)
            ->post(route('pembimbing.tugas.store'), [])
            ->assertForbidden();
    }

    /**
     * STATE-BASED ACCESS CONTROL: Akun yang harus ganti password wajib diarahkan ke ganti password.
     */
    public function test_user_with_must_change_password_is_redirected(): void
    {
        $pendingSuperAdmin = User::factory()->create([
            'role' => RoleEnum::SuperAdmin,
            'status_aktif' => true,
            'must_change_password' => true,
        ]);

        $this->actingAs($pendingSuperAdmin)
            ->get(route('superadmin.dashboard'))
            ->assertRedirect(route('password.ganti'));
    }

    /**
     * STATE-BASED ACCESS CONTROL: Akun non-aktif diputus sesinya dan diarahkan ke login (SEC-03).
     */
    public function test_inactive_staff_account_is_logged_out_and_redirected(): void
    {
        $inactiveSuperAdmin = User::factory()->create([
            'role' => RoleEnum::SuperAdmin,
            'status_aktif' => false,
            'must_change_password' => false,
        ]);

        $this->actingAs($inactiveSuperAdmin)
            ->get(route('superadmin.dashboard'))
            ->assertRedirect(route('login'));
    }
}
