<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ValidateProductionEmailConfigCommand extends Command
{
    /**
     * Signature perintah console.
     *
     * @var string
     */
    protected $signature = 'email:validate-config 
                            {--strict : Jalankan validasi standar production meskipun di environment local/testing}';

    /**
     * Deskripsi perintah console.
     *
     * @var string
     */
    protected $description = 'Validasi kesiapan dan kelayakan konfigurasi email / SMTP untuk lingkungan production (OPS-001)';

    /**
     * Eksekusi perintah console.
     */
    public function handle(): int
    {
        $this->info('================================================================');
        $this->info('  MAGKOM PRODUCTION EMAIL & SMTP READINESS CHECKER              ');
        $this->info('================================================================');

        $isProduction = app()->isProduction() || $this->option('strict');
        $environment = app()->environment();

        $this->line("Environment aktif : <comment>{$environment}</comment>");
        $this->line('Mode validasi     : '.($isProduction ? '<fg=yellow>Production / Strict</>' : '<fg=green>Development (Relaxed)</>'));
        $this->newLine();

        $mailer = (string) config('mail.default', '');
        $host = (string) config('mail.mailers.smtp.host', '');
        $port = config('mail.mailers.smtp.port');
        $username = config('mail.mailers.smtp.username');
        $password = config('mail.mailers.smtp.password');
        $encryption = config('mail.mailers.smtp.encryption');
        $fromAddress = (string) config('mail.from.address', '');
        $fromName = (string) config('mail.from.name', '');

        $errors = [];
        $warnings = [];

        // 1. Cek driver mailer
        if ($isProduction && in_array($mailer, ['log', 'array', 'null'], true)) {
            $errors[] = "Driver mailer diatur ke '{$mailer}'. Lingkungan production dilarang keras menggunakan driver 'log'/'array' karena verifikasi email peserta akan gagal terkirim.";
        }

        // 2. Jika menggunakan SMTP (atau di production yang mensyaratkan SMTP/layanan transactional)
        if ($mailer === 'smtp' || $isProduction) {
            if (empty($host) || in_array($host, ['127.0.0.1', 'localhost'], true)) {
                if ($isProduction) {
                    $errors[] = "MAIL_HOST bernilai '{$host}'. Production membutuhkan alamat host SMTP relay atau penyedia transactional nyata.";
                } else {
                    $warnings[] = "MAIL_HOST mengarah ke loopback/localhost ({$host}). Pastikan Mailpit atau Mailtrap aktif di port tersebut.";
                }
            }

            if (empty($port) || ! is_numeric($port)) {
                $errors[] = 'MAIL_PORT belum ditentukan atau tidak valid (umumnya 587 untuk TLS/STARTTLS, 465 untuk SSL, atau 25 untuk internal relay).';
            }

            if (empty($username)) {
                if ($isProduction && $port != 25) {
                    $errors[] = 'MAIL_USERNAME kosong. Server SMTP production membutuhkan akun autentikasi.';
                } else {
                    $warnings[] = 'MAIL_USERNAME tidak diisi (hanya diizinkan jika menggunakan unauthenticated internal SMTP relay port 25).';
                }
            }

            if (empty($password)) {
                if ($isProduction && $port != 25) {
                    $errors[] = 'MAIL_PASSWORD kosong. Server SMTP production membutuhkan kata sandi / app password.';
                }
            }

            if ($host === 'smtp.gmail.com') {
                $warnings[] = 'Menggunakan smtp.gmail.com. Untuk instansi pemerintah disarankan menggunakan SMTP relay resmi Pemda/Diskominfo atau Mailgun/AWS SES agar terhindar dari pembatasan kuota dan pemblokiran akun personal.';
            }
        }

        // 3. Validasi pengirim (From Address & Name)
        if (empty($fromAddress) || ! filter_var($fromAddress, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "MAIL_FROM_ADDRESS ('{$fromAddress}') tidak valid. Wajib menggunakan format alamat email resmi.";
        } elseif (str_ends_with($fromAddress, '@example.com')) {
            $errors[] = 'MAIL_FROM_ADDRESS masih menggunakan domain default @example.com.';
        }

        if (empty($fromName)) {
            $warnings[] = 'MAIL_FROM_NAME belum diatur. Penerima akan melihat nama pengirim default.';
        }

        // Tampilkan hasil
        $this->table(
            ['Parameter', 'Nilai Saat Ini', 'Status'],
            [
                ['MAIL_MAILER', $mailer ?: '(kosong)', $mailer === 'smtp' ? '✓ OK' : ($isProduction ? '✗ ERROR' : '⚠ Dev Only')],
                ['MAIL_HOST', $host ?: '(kosong)', ! empty($host) && ! in_array($host, ['127.0.0.1', 'localhost']) ? '✓ OK' : '⚠ Evaluasi'],
                ['MAIL_PORT', (string) $port ?: '(kosong)', ! empty($port) ? '✓ OK' : '✗ ERROR'],
                ['MAIL_USERNAME', ! empty($username) ? '[TERSEDIA]' : '(kosong)', ! empty($username) ? '✓ OK' : '⚠ Periksa'],
                ['MAIL_PASSWORD', ! empty($password) ? '[TERSEDIA]' : '(kosong)', ! empty($password) ? '✓ OK' : '⚠ Periksa'],
                ['MAIL_ENCRYPTION', (string) $encryption ?: '(null)', '✓ OK'],
                ['MAIL_FROM_ADDRESS', $fromAddress ?: '(kosong)', filter_var($fromAddress, FILTER_VALIDATE_EMAIL) ? '✓ OK' : '✗ ERROR'],
                ['MAIL_FROM_NAME', $fromName ?: '(kosong)', ! empty($fromName) ? '✓ OK' : '⚠ Periksa'],
            ]
        );

        $this->newLine();

        if (! empty($warnings)) {
            $this->warn('Peringatan:');
            foreach ($warnings as $warn) {
                $this->line(" ⚠ {$warn}");
            }
            $this->newLine();
        }

        if (! empty($errors)) {
            $this->error('GAGAL: Konfigurasi email tidak memenuhi kriteria kelayakan production:');
            foreach ($errors as $err) {
                $this->line(" ✗ {$err}");
            }
            $this->newLine();

            return self::FAILURE;
        }

        $this->info('✓ SELURUH VALIDASI EMAIL & SMTP MEMENUHI STANDAR PRODUCTION.');

        return self::SUCCESS;
    }
}
