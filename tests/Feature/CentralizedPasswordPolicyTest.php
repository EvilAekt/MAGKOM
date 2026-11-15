<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CentralizedPasswordPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected User $superadmin;

    protected User $admin;

    protected User $peserta;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PermissionSeeder::class);

        $this->superadmin = User::factory()->create([
            'role' => RoleEnum::SuperAdmin,
            'password' => Hash::make('SuperAdminOldPassword123!'),
            'status_aktif' => true,
            'email_verified_at' => now(),
            'must_change_password' => false,
        ]);

        $this->admin = User::factory()->create([
            'role' => RoleEnum::Admin,
            'password' => Hash::make('AdminOldPassword123!'),
            'status_aktif' => true,
            'email_verified_at' => now(),
            'must_change_password' => false,
        ]);

        $this->peserta = User::factory()->create([
            'role' => RoleEnum::Peserta,
            'password' => Hash::make('PesertaPass123!'),
            'status_aktif' => true,
            'email_verified_at' => now(),
            'must_change_password' => false,
        ]);
    }

    /**
     * Memastikan pembuatan staf menolak password di bawah 12 karakter atau tanpa simbol/huruf besar.
     */
    public function test_staff_creation_rejects_weak_password(): void
    {
        $response = $this->actingAs($this->superadmin)
            ->post(route('superadmin.staf.store'), [
                'nama' => 'Staff Baru Admin',
                'email' => 'staff.baru@diskominfo.go.id',
                'role' => RoleEnum::Admin->value,
                'password' => 'Weak1234', // Kurang dari 12 karakter & tanpa simbol
            ]);

        $response->assertSessionHasErrors('password');
    }

    /**
     * Memastikan pembuatan staf menerima password kuat 12 karakter.
     */
    public function test_staff_creation_accepts_strong_12_char_password(): void
    {
        $response = $this->actingAs($this->superadmin)
            ->post(route('superadmin.staf.store'), [
                'nama' => 'Staff Resmi Admin',
                'email' => 'staff.resmi@diskominfo.go.id',
                'role' => RoleEnum::Admin->value,
                'password' => 'StaffKuat2026!#',
            ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('users', [
            'email' => 'staff.resmi@diskominfo.go.id',
        ]);
    }

    /**
     * Memastikan reset kata sandi staf oleh SuperAdmin menolak password lemah.
     */
    public function test_superadmin_reset_staff_password_rejects_weak_password(): void
    {
        $response = $this->actingAs($this->superadmin)
            ->post(route('superadmin.staf.reset-password', $this->admin), [
                'password_baru' => 'Ganti123', // Hanya 8 karakter
            ]);

        $response->assertSessionHasErrors('password_baru');
    }

    /**
     * Memastikan ganti kata sandi oleh SuperAdmin menolak password di bawah 12 karakter.
     */
    public function test_superadmin_change_password_enforces_12_chars(): void
    {
        $response = $this->actingAs($this->superadmin)
            ->put(route('superadmin.akun.password.update'), [
                'current_password' => 'SuperAdminOldPassword123!',
                'password' => 'Pendek123!',
                'password_confirmation' => 'Pendek123!',
            ]);

        $response->assertSessionHasErrors('password');
    }

    /**
     * Memastikan registrasi peserta menolak password di bawah 8 karakter atau tanpa kombinasi angka.
     */
    public function test_registration_rejects_substandard_password(): void
    {
        $response = $this->post(route('register'), [
            'nama' => 'Pendaftar Baru',
            'email' => 'pendaftar.baru@gmail.com',
            'no_telp' => '081234567890',
            'password' => 'hanyahuruf',
            'password_confirmation' => 'hanyahuruf',
            'captcha' => 'DEMO',
        ]);

        $response->assertSessionHasErrors('password');
    }
}
