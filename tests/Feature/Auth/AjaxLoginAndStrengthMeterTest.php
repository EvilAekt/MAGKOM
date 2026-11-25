<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Enums\RoleEnum;
use App\Models\User;
use App\Support\PasswordPolicy;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class AjaxLoginAndStrengthMeterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PermissionSeeder::class);
    }

    public function test_login_via_json_returns_422_on_invalid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'peserta.test@gmail.com',
            'password' => Hash::make('PasswordBenar123!'),
            'role' => RoleEnum::Peserta,
            'status_aktif' => true,
        ]);

        $response = $this->postJson(route('login'), [
            'email' => 'peserta.test@gmail.com',
            'password' => 'PasswordSalah123!',
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
        ]);
        $response->assertJsonStructure(['success', 'message', 'errors']);
    }

    public function test_login_via_json_returns_200_with_redirect_on_success(): void
    {
        $user = User::factory()->create([
            'email' => 'peserta.sukses@gmail.com',
            'password' => Hash::make('PasswordBenar123!'),
            'role' => RoleEnum::Peserta,
            'status_aktif' => true,
            'email_verified_at' => now(),
            'must_change_password' => false,
        ]);

        $response = $this->postJson(route('login'), [
            'email' => 'peserta.sukses@gmail.com',
            'password' => 'PasswordBenar123!',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
        $response->assertJsonStructure(['success', 'redirect']);
    }

    public function test_staff_policy_accepts_8_character_complex_password(): void
    {
        $validator = Validator::make(
            ['password' => 'Staff8!#'],
            ['password' => PasswordPolicy::staffRules(confirmed: false)]
        );

        $this->assertTrue($validator->passes());
    }

    public function test_staff_policy_rejects_passwords_under_8_characters(): void
    {
        $validator = Validator::make(
            ['password' => 'Staf7!#'],
            ['password' => PasswordPolicy::staffRules(confirmed: false)]
        );

        $this->assertTrue($validator->fails());
    }

    public function test_auth_views_render_password_strength_meter_and_alert_wrapper(): void
    {
        $loginRes = $this->get(route('login'));
        $loginRes->assertOk();
        $loginRes->assertSee('id="dynamicAuthAlert"', false);

        $registerRes = $this->get(route('register'));
        $registerRes->assertOk();
        $registerRes->assertSee('class="pw-strength-wrapper"', false);

        $resetRes = $this->get(route('password.reset', ['token' => 'dummy-token-123']));
        $resetRes->assertOk();
        $resetRes->assertSee('class="pw-strength-wrapper"', false);
    }
}
