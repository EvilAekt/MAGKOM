<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Jumlah percobaan pengiriman ulang jika koneksi SMTP gagal sementara (MAIL-001).
     */
    public int $tries = 3;

    /**
     * Batas waktu eksekusi pengiriman per job (detik).
     */
    public int $timeout = 30;

    /**
     * Jeda waktu sebelum percobaan ulang berikutnya (detik).
     */
    public int $backoff = 60;

    /**
     * Inisialisasi notifikasi dengan token pemulihan kata sandi.
     */
    public function __construct(
        public string $token
    ) {}

    /**
     * Saluran pengiriman notifikasi (Email).
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Representasi pesan email HTML bertema resmi MAGKOM Diskominfo.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $email = method_exists($notifiable, 'getEmailForPasswordReset')
            ? (string) $notifiable->getEmailForPasswordReset()
            : (string) ($notifiable->email ?? '');

        $resetUrl = route('password.reset', [
            'token' => $this->token,
            'email' => $email,
        ]);

        $expireMinutes = (int) config('auth.passwords.users.expire', 60);

        return (new MailMessage)
            ->subject('Permintaan Reset Password - Sistem Magang Diskominfo (MAGKOM)')
            ->view('emails.reset-password', [
                'url' => $resetUrl,
                'user' => $notifiable,
                'expire' => $expireMinutes,
            ]);
    }
}
