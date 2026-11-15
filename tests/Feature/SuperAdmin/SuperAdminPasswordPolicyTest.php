<?php

declare(strict_types=1);

namespace Tests\Feature\SuperAdmin;

use App\Enums\RoleEnum;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SuperAdminPasswordPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected User $pembimbing;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PermissionSeeder::class);

        $this->superAdmin = User::factory()->create([
            'role' => RoleEnum::SuperAdmin,
            'password' => Hash::make('SuperAdminOldPassword123!'),
            'email_verified_at' => now(),
            'must_change_password' => false,
            'status_aktif' => true,
        ]);

        $this->pembimbing = User::factory()->create([
            'role' => RoleEnum::Pembimbing,
            'password' => Hash::make('Pembimbing123!'),
            'email_verified_at' => now(),
            'must_change_password' => false,
            'status_aktif' => true,
        ]);
    }

    public function test_superadmin_password_change_rejects_passwords_under_12_characters(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->put(route('superadmin.akun.password.update'), [
                'current_password' => 'SuperAdminOldPassword123!',
                'password' => 'Short123!',
                'password_confirmation' => 'Short123!',
            ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_superadmin_password_change_accepts_strong_12_character_password(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->put(route('superadmin.akun.password.update'), [
                'current_password' => 'SuperAdminOldPassword123!',
                'password' => 'SuperSecure2026!#',
                'password_confirmation' => 'SuperSecure2026!#',
            ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('superadmin.akun.index'));

        $this->superAdmin->refresh();
        $this->assertTrue(Hash::check('SuperSecure2026!#', $this->superAdmin->password));
    }

    public function test_non_superadmin_can_use_standard_8_character_password(): void
    {
        $response = $this->actingAs($this->pembimbing)
            ->post(route('password.ganti.update'), [
                'current_password' => 'Pembimbing123!',
                'password' => 'Pass1234',
                'password_confirmation' => 'Pass1234',
            ]);

        $response->assertSessionHasNoErrors();
    }
}
