<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ZeroReloadAuthFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['captcha.enabled' => false]);
        config(['services.turnstile.enabled' => false]);
    }

    public function test_login_via_json_returns_422_with_field_errors_on_wrong_password(): void
    {
        $user = User::factory()->create([
            'email' => 'peserta@test.com',
            'password' => Hash::make('Password123'),
            'role' => RoleEnum::Peserta,
            'status_aktif' => true,
        ]);

        $response = $this->postJson(route('login'), [
            'email' => 'peserta@test.com',
            'password' => 'WrongPassword999',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'errors' => ['email'],
            ]);
    }

    public function test_register_via_json_returns_422_with_field_errors_when_validation_fails(): void
    {
        $response = $this->postJson(route('register'), [
            'nama' => 'Budi Santoso',
            'email' => 'budi@test.com',
            'password' => 'Pass123',
            'password_confirmation' => 'BedaPassword999',
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => ['password'],
            ]);
    }

    public function test_register_via_json_returns_200_and_redirect_on_success(): void
    {
        $response = $this->postJson(route('register'), [
            'nama' => 'Budi Santoso',
            'email' => 'budi.santoso@test.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
            'tipe_pendaftaran' => 'mandiri',
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'redirect' => route('peserta.dashboard'),
            ]);

        $this->assertAuthenticated();
    }

    public function test_forgot_password_via_json_returns_200(): void
    {
        $response = $this->postJson(route('password.email'), [
            'email' => 'siapapun@gmail.com',
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'message',
            ]);
    }

    public function test_reset_password_via_json_returns_422_with_field_errors_on_invalid_token(): void
    {
        $response = $this->postJson(route('password.update'), [
            'token' => 'invalid-token-12345',
            'email' => 'peserta@test.com',
            'password' => 'PasswordBaru123',
            'password_confirmation' => 'PasswordBaru123',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'errors' => ['email'],
            ]);
    }

    public function test_admin_update_password_via_json_returns_422_on_wrong_current_password(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@kominfo.go.id',
            'password' => Hash::make('SandiLama#123'),
            'role' => RoleEnum::Admin,
            'status_aktif' => true,
        ]);

        $response = $this->actingAs($admin)->putJson(route('admin.pengaturan.password.update'), [
            'current_password' => 'SandiSalah#999',
            'password' => 'SandiBaru#123',
            'password_confirmation' => 'SandiBaru#123',
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => ['current_password'],
            ]);
    }

    public function test_admin_update_password_via_json_returns_200_on_success(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin2@kominfo.go.id',
            'password' => Hash::make('SandiLama#123'),
            'role' => RoleEnum::Admin,
            'status_aktif' => true,
        ]);

        $response = $this->actingAs($admin)->putJson(route('admin.pengaturan.password.update'), [
            'current_password' => 'SandiLama#123',
            'password' => 'SandiBaru#123',
            'password_confirmation' => 'SandiBaru#123',
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'redirect' => route('admin.pengaturan.index'),
            ]);
    }

    public function test_pembimbing_update_password_via_json_returns_200_on_success(): void
    {
        $pembimbing = User::factory()->create([
            'email' => 'pembimbing@kominfo.go.id',
            'password' => Hash::make('SandiLama#123'),
            'role' => RoleEnum::Pembimbing,
            'status_aktif' => true,
        ]);

        $response = $this->actingAs($pembimbing)->putJson(route('pembimbing.pengaturan.password.update'), [
            'current_password' => 'SandiLama#123',
            'password' => 'SandiBaru#123',
            'password_confirmation' => 'SandiBaru#123',
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'redirect' => route('pembimbing.pengaturan.index'),
            ]);
    }

    public function test_peserta_update_password_via_json_returns_200_on_success(): void
    {
        $peserta = User::factory()->create([
            'email' => 'peserta_update@test.com',
            'password' => Hash::make('SandiLama123'),
            'role' => RoleEnum::Peserta,
            'status_aktif' => true,
        ]);

        $response = $this->actingAs($peserta)->putJson(route('peserta.pengaturan.password.update'), [
            'current_password' => 'SandiLama123',
            'password' => 'SandiBaruBaru123',
            'password_confirmation' => 'SandiBaruBaru123',
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'redirect' => route('peserta.pengaturan.index'),
            ]);
    }

    public function test_superadmin_update_password_via_json_returns_200_on_success(): void
    {
        $superadmin = User::factory()->create([
            'email' => 'superadmin@kominfo.go.id',
            'password' => Hash::make('SandiLama#123'),
            'role' => RoleEnum::SuperAdmin,
            'status_aktif' => true,
        ]);

        $response = $this->actingAs($superadmin)->putJson(route('superadmin.akun.password.update'), [
            'current_password' => 'SandiLama#123',
            'password' => 'SandiBaru#123',
            'password_confirmation' => 'SandiBaru#123',
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'redirect' => route('superadmin.akun.index'),
            ]);
    }
}
