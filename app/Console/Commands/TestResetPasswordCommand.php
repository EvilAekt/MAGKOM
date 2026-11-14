<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\RoleEnum;
use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Password;
use Throwable;

class TestResetPasswordCommand extends Command
{
    /**
     * Nama dan signature perintah console.
     *
     * @var string
     */
    protected $signature = 'auth:test-reset 
                            {email : Alamat email akun peserta yang akan diuji} 
                            {--no-send : Hanya buat dan cetak tautan reset tanpa mengirim email via SMTP}';

    /**
     * Deskripsi perintah console.
     *
     * @var string
     */
    protected $description = 'Uji diagnostik alur lupa password peserta: periksa akun, buat token resmi, cetak tautan reset, dan uji kirim via SMTP';

    /**
     * Eksekusi perintah console.
     */
    public function handle(): int
    {
        $this->info('================================================================');
        $this->info('  MAGKOM AUTH DIAGNOSTIC: UJI FITUR LUPA & RESET PASSWORD       ');
        $this->info('================================================================');

        $email = strtolower(trim((string) $this->argument('email')));

        $this->line("1. Memeriksa keberadaan akun dengan email: <comment>{$email}</comment>");

        $user = User::where('email', $email)->first();

        if (! $user) {
            $this->error("❌ Akun dengan email '{$email}' TIDAK DITEMUKAN di database!");
            $this->warn('Petunjuk: Pastikan email sudah terdaftar melalui formulir registrasi peserta.');

            return self::FAILURE;
        }

        $this->table(
            ['ID', 'Nama', 'Email', 'Role', 'Status Aktif'],
            [[
                $user->id,
                $user->nama,
                $user->email,
                $user->role->value,
                $user->status_aktif ? 'Aktif (1)' : 'Non-aktif (0)',
            ]]
        );

        if ($user->role !== RoleEnum::Peserta) {
            $this->warn("⚠️ Perhatian: Role akun ini adalah '{$user->role->value}'. Kebijakan sistem membatasi reset password mandiri hanya untuk role 'peserta'.");
        }

        if (! $user->status_aktif) {
            $this->error('❌ Akun ini berstatus non-aktif. Sistem tidak akan mengirimkan email pemulihan untuk akun non-aktif.');

            return self::FAILURE;
        }

        $this->newLine();
        $this->line('2. Menghasilkan token pemulihan password resmi...');

        /** @var string $token */
        $token = Password::broker()->createToken($user);

        $resetUrl = route('password.reset', [
            'token' => $token,
            'email' => $user->email,
        ]);

        $expireMinutes = (int) config('auth.passwords.users.expire', 60);

        $this->info('✓ Token berhasil dibuat dan disimpan di tabel password_reset_tokens!');
        $this->newLine();

        $this->alert("🔗 TAUTAN RESET PASSWORD LANGSUNG (Berlaku {$expireMinutes} Menit):\n{$resetUrl}");
        $this->comment('Anda dapat langsung menyalin tautan di atas ke browser untuk menguji formulir reset password.');

        if ($this->option('no-send')) {
            $this->info('Opsi --no-send aktif: Pengiriman email dilewati.');

            return self::SUCCESS;
        }

        $this->newLine();
        $this->line('3. Menyiapkan pengiriman email via SMTP transport...');
        $this->table(
            ['Pengaturan', 'Nilai'],
            [
                ['Mailer Default', (string) Config::get('mail.default')],
                ['SMTP Host', (string) Config::get('mail.mailers.smtp.host')],
                ['SMTP Port', (string) Config::get('mail.mailers.smtp.port')],
                ['Enkripsi', (string) Config::get('mail.mailers.smtp.encryption')],
                ['Pengirim (From)', (string) Config::get('mail.from.address')],
            ]
        );

        try {
            $this->comment("Sedang menghubungi server SMTP untuk mengirim email ke <comment>{$user->email}</comment>...");
            $user->notify(new ResetPasswordNotification($token));
            $this->info('✓ Email BERHASIL dikirimkan ke server SMTP (Status: Accepted by Mail Transport)!');
            $this->line('💡 Tips: Jika email belum muncul di Kotak Masuk Utama, periksa folder SPAM/JUNK atau tab PROMOTIONS.');
        } catch (Throwable $e) {
            $this->error('❌ Gagal mengirimkan email via SMTP:');
            $this->error($e->getMessage());
            $this->warn('Periksa kredensial MAIL_USERNAME, MAIL_PASSWORD (Google App Password), serta koneksi internet.');

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
