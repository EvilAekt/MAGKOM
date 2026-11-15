<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class QueuedVerifyEmailNotification extends VerifyEmail implements ShouldQueue
{
    use Queueable;

    /**
     * Jumlah percobaan pengiriman ulang jika relay SMTP sibuk/gagal sementara (MAIL-001).
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
}
