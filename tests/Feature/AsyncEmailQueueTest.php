<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\QueuedVerifyEmailNotification;
use App\Notifications\ResetPasswordNotification;
use Database\Seeders\PermissionSeeder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class AsyncEmailQueueTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PermissionSeeder::class);
    }

    public function test_reset_password_notification_implements_should_queue_with_retry_parameters(): void
    {
        $notification = new ResetPasswordNotification('sample-token-123');

        $this->assertInstanceOf(ShouldQueue::class, $notification);
        $this->assertEquals(3, $notification->tries);
        $this->assertEquals(30, $notification->timeout);
        $this->assertEquals(60, $notification->backoff);
    }

    public function test_queued_verify_email_notification_implements_should_queue_with_retry_parameters(): void
    {
        $notification = new QueuedVerifyEmailNotification;

        $this->assertInstanceOf(ShouldQueue::class, $notification);
        $this->assertEquals(3, $notification->tries);
        $this->assertEquals(30, $notification->timeout);
        $this->assertEquals(60, $notification->backoff);
    }

    public function test_password_reset_request_pushes_notification_to_queue(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'peserta.test@example.com',
            'status_aktif' => true,
        ]);

        $response = $this->post(route('password.email'), [
            'email' => 'peserta.test@example.com',
        ]);

        $response->assertSessionHas('status');

        Notification::assertSentTo(
            $user,
            ResetPasswordNotification::class,
            function (ResetPasswordNotification $notification) {
                return $notification instanceof ShouldQueue;
            }
        );
    }

    public function test_email_verification_resend_pushes_notification_to_queue(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'email_verified_at' => null,
            'status_aktif' => true,
        ]);

        $response = $this->actingAs($user)->post(route('verification.send'));
        $response->assertSessionHas('status', 'verification-link-sent');

        Notification::assertSentTo(
            $user,
            QueuedVerifyEmailNotification::class,
            function (QueuedVerifyEmailNotification $notification) {
                return $notification instanceof ShouldQueue;
            }
        );
    }

    public function test_unverified_user_registered_event_queues_email_verification(): void
    {
        Notification::fake();

        $unverifiedUser = User::factory()->create([
            'email_verified_at' => null,
            'status_aktif' => true,
        ]);

        event(new \Illuminate\Auth\Events\Registered($unverifiedUser));

        Notification::assertSentTo(
            $unverifiedUser,
            QueuedVerifyEmailNotification::class,
            function (QueuedVerifyEmailNotification $notification) {
                return $notification instanceof ShouldQueue;
            }
        );
    }
}
