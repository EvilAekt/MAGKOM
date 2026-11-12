<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Database\Seeders\PengaturanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class AuthForgotPasswordAndResetTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PengaturanSeeder::class);
    }

    /**
     * Helper to create a student user with appropriate defaults.
     */
    protected function createStudentUser(array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'email' => 'mahasiswa@magkom.go.id',
            'password' => Hash::make('PasswordLama123!'),
            'role' => RoleEnum::Peserta,
            'status_aktif' => true,
        ], $attributes));
    }

    /**
     * TASK 1: Forgot Password UI renders successfully.
     */
    public function test_forgot_password_page_can_be_rendered(): void
    {
        $response = $this->get(route('password.request'));

        $response->assertStatus(200);
        $response->assertViewIs('auth.forgot-password');
        $response->assertSee('Lupa Password?');
        $response->assertSee('Masukkan alamat email yang terdaftar pada akun Anda.');
        $response->assertSee('Kirim Tautan Reset');
        $response->assertSee('Kembali ke Login');
        $response->assertSee(route('password.email'));
    }

    /**
     * TASK 2 & 4: Sending reset link for valid student sends ResetPasswordNotification.
     */
    public function test_send_reset_link_with_registered_peserta_email_sends_notification(): void
    {
        Notification::fake();

        $user = $this->createStudentUser([
            'email' => 'peserta.aktif@magkom.go.id',
        ]);

        $response = $this->post(route('password.email'), [
            'email' => 'peserta.aktif@magkom.go.id',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status', 'Jika alamat email terdaftar pada sistem, kami akan mengirimkan instruksi untuk mengatur ulang password.');

        Notification::assertSentTo(
            $user,
            ResetPasswordNotification::class,
            function (ResetPasswordNotification $notification) use ($user) {
                // Ensure notification mail message renders properly
                $mailMessage = $notification->toMail($user);
                $this->assertSame('Permintaan Reset Password - Sistem Magang Diskominfo (MAGKOM)', $mailMessage->subject);
                $this->assertSame('emails.reset-password', $mailMessage->view);
                $this->assertStringContainsString('/reset-password/', $mailMessage->viewData['url']);
                $this->assertSame(60, $mailMessage->viewData['expire']);

                return true;
            }
        );

        // Verify token exists in database
        $this->assertTrue(
            DB::table('password_reset_tokens')->where('email', 'peserta.aktif@magkom.go.id')->exists()
        );
    }

    /**
     * Anti-Enumeration: Unregistered email receives identical generic response and sends no email.
     */
    public function test_send_reset_link_with_unregistered_email_returns_generic_message_and_sends_no_notification(): void
    {
        Notification::fake();

        $response = $this->post(route('password.email'), [
            'email' => 'tidakada.di.database@contoh.id',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status', 'Jika alamat email terdaftar pada sistem, kami akan mengirimkan instruksi untuk mengatur ulang password.');

        Notification::assertNothingSent();
    }

    /**
     * Role Isolation: Non-peserta (staff/admin) cannot request reset via public student forgot password.
     */
    public function test_send_reset_link_with_staff_email_returns_generic_message_and_sends_no_notification(): void
    {
        Notification::fake();

        $admin = User::factory()->create([
            'email' => 'admin.diskominfo@magkom.go.id',
            'role' => RoleEnum::Admin,
            'status_aktif' => true,
        ]);

        $response = $this->post(route('password.email'), [
            'email' => 'admin.diskominfo@magkom.go.id',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status', 'Jika alamat email terdaftar pada sistem, kami akan mengirimkan instruksi untuk mengatur ulang password.');

        Notification::assertNothingSent();
    }

    /**
     * Validation: Invalid email format is rejected with validation error.
     */
    public function test_send_reset_link_validates_email_format(): void
    {
        $response = $this->post(route('password.email'), [
            'email' => 'bukan-email-valid',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    /**
     * TASK 3: Reset Password UI renders successfully with token and email.
     */
    public function test_reset_password_page_can_be_rendered_with_token(): void
    {
        $user = $this->createStudentUser();
        $token = Password::broker()->createToken($user);

        $response = $this->get(route('password.reset', [
            'token' => $token,
            'email' => $user->email,
        ]));

        $response->assertStatus(200);
        $response->assertViewIs('auth.reset-password');
        $response->assertSee('Buat Password Baru');
        $response->assertSee('Minimal 8 karakter dengan kombinasi huruf dan angka.');
        $response->assertSee('Simpan Password Baru');
        $response->assertSee('Kembali ke Login');
        $response->assertSee($user->email);
    }

    /**
     * TASK 2: Password can be reset successfully with valid token and password.
     */
    public function test_password_can_be_reset_with_valid_token_and_strong_password(): void
    {
        $user = $this->createStudentUser([
            'email' => 'reset.sukses@magkom.go.id',
            'password' => Hash::make('PasswordLama123!'),
        ]);

        $token = Password::broker()->createToken($user);

        $response = $this->post(route('password.update'), [
            'token' => $token,
            'email' => 'reset.sukses@magkom.go.id',
            'password' => 'PasswordBaru123!',
            'password_confirmation' => 'PasswordBaru123!',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('status', 'Password Anda berhasil diperbarui. Silakan login dengan password baru.');

        // User password updated in database
        $this->assertTrue(Hash::check('PasswordBaru123!', $user->fresh()->password));

        // Token consumed/invalidated
        $this->assertFalse(
            DB::table('password_reset_tokens')->where('email', 'reset.sukses@magkom.go.id')->exists()
        );

        // Can log in with new password
        $loginResponse = $this->post(route('login'), [
            'email' => 'reset.sukses@magkom.go.id',
            'password' => 'PasswordBaru123!',
        ]);
        $loginResponse->assertRedirect(route('peserta.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Security: Reset token cannot be reused once consumed.
     */
    public function test_reset_token_cannot_be_reused_once_used(): void
    {
        $user = $this->createStudentUser([
            'email' => 'token.reused@magkom.go.id',
            'password' => Hash::make('PasswordLama123!'),
        ]);

        $token = Password::broker()->createToken($user);

        // First attempt succeeds
        $this->post(route('password.update'), [
            'token' => $token,
            'email' => 'token.reused@magkom.go.id',
            'password' => 'PasswordBaru123!',
            'password_confirmation' => 'PasswordBaru123!',
        ])->assertRedirect(route('login'));

        // Second attempt with the same token must fail
        $secondAttempt = $this->post(route('password.update'), [
            'token' => $token,
            'email' => 'token.reused@magkom.go.id',
            'password' => 'PasswordBaruLagi999!',
            'password_confirmation' => 'PasswordBaruLagi999!',
        ]);

        $secondAttempt->assertSessionHasErrors(['email']);
        $this->assertFalse(Hash::check('PasswordBaruLagi999!', $user->fresh()->password));
    }

    /**
     * Security: Password reset fails with invalid or malformed token.
     */
    public function test_password_reset_fails_with_invalid_token(): void
    {
        $user = $this->createStudentUser([
            'email' => 'invalid.token@magkom.go.id',
            'password' => Hash::make('PasswordLama123!'),
        ]);

        $response = $this->post(route('password.update'), [
            'token' => 'token-palsu-ngawur-1234567890',
            'email' => 'invalid.token@magkom.go.id',
            'password' => 'PasswordBaru123!',
            'password_confirmation' => 'PasswordBaru123!',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertTrue(Hash::check('PasswordLama123!', $user->fresh()->password));
    }

    /**
     * Security: Password validation enforces minimum 8 chars, letter+number regex, and confirmation.
     */
    public function test_password_reset_fails_with_weak_password(): void
    {
        $user = $this->createStudentUser();
        $token = Password::broker()->createToken($user);

        // Too short (< 8 chars)
        $shortResponse = $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'Abc1!',
            'password_confirmation' => 'Abc1!',
        ]);
        $shortResponse->assertSessionHasErrors(['password']);

        // Only letters (no digits)
        $lettersResponse = $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'HanyaHurufSaja',
            'password_confirmation' => 'HanyaHurufSaja',
        ]);
        $lettersResponse->assertSessionHasErrors(['password']);

        // Only numbers (no letters)
        $numbersResponse = $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => '1234567890',
            'password_confirmation' => '1234567890',
        ]);
        $numbersResponse->assertSessionHasErrors(['password']);

        // Mismatched confirmation
        $mismatchResponse = $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'PasswordBaru123!',
            'password_confirmation' => 'BedaPassword123!',
        ]);
        $mismatchResponse->assertSessionHasErrors(['password']);
    }
}
