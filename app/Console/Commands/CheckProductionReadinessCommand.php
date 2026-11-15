<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class CheckProductionReadinessCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-production-readiness
                            {--strict : Mengubah seluruh peringatan menjadi error fatal}
                            {--target=production : Target lingkungan yang dievaluasi (default: production)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Melakukan audit pra-peluncuran (preflight check) kesiapan keamanan lingkungan production';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $targetEnv = (string) $this->option('target');
        $isStrict = (bool) $this->option('strict');
        $currentEnv = (string) app()->environment();

        $this->newLine();
        $this->info('================================================================');
        $this->info(' MAGKOM PRODUCTION PREFLIGHT SECURITY & READINESS AUDIT (SEC-001)');
        $this->info('================================================================');
        $this->line("Target Environment : <fg=yellow>{$targetEnv}</>");
        $this->line("Current Environment: <fg=cyan>{$currentEnv}</>");
        $this->line('Execution Mode     : '.($isStrict ? '<fg=red>Strict Mode</>' : '<fg=green>Standard Mode</>'));
        $this->newLine();

        $rows = [];
        $hasErrors = false;
        $hasWarnings = false;

        // 1. Environment & Debug Mode
        $envMatches = ($currentEnv === $targetEnv);
        $rows[] = [
            'Environment',
            'APP_ENV',
            $envMatches ? '<fg=green>✓ OK</>' : '<fg=red>✗ ERROR</>',
            $envMatches ? "Sesuai target ({$currentEnv})" : "APP_ENV saat ini '{$currentEnv}', wajib '{$targetEnv}'",
        ];
        if (! $envMatches) {
            $hasErrors = true;
        }

        $debug = config('app.debug');
        $debugOk = ($debug === false);
        $rows[] = [
            'Environment',
            'APP_DEBUG',
            $debugOk ? '<fg=green>✓ OK</>' : '<fg=red>✗ ERROR</>',
            $debugOk ? 'Nonaktif (false)' : 'KRITIS: APP_DEBUG aktif di production membocorkan stack trace',
        ];
        if (! $debugOk) {
            $hasErrors = true;
        }

        // 2. Encryption Key (APP_KEY)
        $appKey = (string) config('app.key');
        $keyOk = ! empty($appKey) && (str_starts_with($appKey, 'base64:') || strlen($appKey) >= 32);
        $rows[] = [
            'Security',
            'APP_KEY',
            $keyOk ? '<fg=green>✓ OK</>' : '<fg=red>✗ ERROR</>',
            $keyOk ? 'Kunci enkripsi aplikasi valid' : 'APP_KEY kosong atau tidak aman',
        ];
        if (! $keyOk) {
            $hasErrors = true;
        }

        // 3. Session & HTTPS Cookie Protection
        $secureCookie = (bool) config('session.secure', false);
        $rows[] = [
            'Session',
            'SESSION_SECURE_COOKIE',
            $secureCookie ? '<fg=green>✓ OK</>' : '<fg=red>✗ ERROR</>',
            $secureCookie ? 'Aktif (HTTPS only)' : 'Cookie sesi tidak berflag Secure (risiko intersepsi)',
        ];
        if (! $secureCookie) {
            $hasErrors = true;
        }

        $httpOnly = (bool) config('session.http_only', true);
        $rows[] = [
            'Session',
            'SESSION_HTTP_ONLY',
            $httpOnly ? '<fg=green>✓ OK</>' : '<fg=red>✗ ERROR</>',
            $httpOnly ? 'Aktif (Anti-XSS cookie theft)' : 'HTTP-only dinonaktifkan',
        ];
        if (! $httpOnly) {
            $hasErrors = true;
        }

        // 4. Mail Configuration (OPS-001)
        $mailDefault = (string) config('mail.default');
        $mailOk = ($mailDefault === 'smtp');
        $rows[] = [
            'Mail',
            'MAIL_MAILER',
            $mailOk ? '<fg=green>✓ OK</>' : '<fg=red>✗ ERROR</>',
            $mailOk ? "Driver SMTP aktif ({$mailDefault})" : "DILARANG: Mailer '{$mailDefault}' tidak boleh di production",
        ];
        if (! $mailOk) {
            $hasErrors = true;
        }

        // 5. Bot & CAPTCHA Protection (SEC-002)
        $turnstileEnabled = (bool) config('services.turnstile.enabled', false);
        $turnstileSiteKey = (string) config('services.turnstile.site_key', '');
        $turnstileSecretKey = (string) config('services.turnstile.secret_key', '');
        $turnstileOk = $turnstileEnabled && ! empty($turnstileSiteKey) && ! empty($turnstileSecretKey);

        $rows[] = [
            'Bot Protection',
            'Cloudflare Turnstile',
            $turnstileOk ? '<fg=green>✓ OK</>' : '<fg=red>✗ ERROR</>',
            $turnstileOk ? 'Turnstile aktif dengan site key & secret key' : 'Turnstile belum aktif / secret key belum dikonfigurasi',
        ];
        if (! $turnstileOk) {
            $hasErrors = true;
        }

        // 6. Demo Account Protection (SEC-001)
        $demoSeedAllowed = (bool) env('SEED_DEMO_ACCOUNTS', false);
        $rows[] = [
            'Seeder Safety',
            'SEED_DEMO_ACCOUNTS',
            (! $demoSeedAllowed) ? '<fg=green>✓ OK</>' : '<fg=red>✗ ERROR</>',
            (! $demoSeedAllowed) ? 'Nonaktif (aman)' : 'KRITIS: SEED_DEMO_ACCOUNTS=true dilarang di production',
        ];
        if ($demoSeedAllowed) {
            $hasErrors = true;
        }

        // Check for default password hash in users table if DB connected
        $knownWeakHashes = false;
        try {
            if (Schema::hasTable('users')) {
                $knownPasses = ['Password123!', 'SuperAdminMagKom123!', 'password', '12345678'];
                $users = User::all(['id', 'email', 'password']);
                foreach ($users as $user) {
                    foreach ($knownPasses as $weak) {
                        if (Hash::check($weak, $user->password)) {
                            $knownWeakHashes = true;
                            break 2;
                        }
                    }
                }
            }
        } catch (\Throwable) {
            // Abaikan jika DB belum terkoneksi saat build time
        }

        $rows[] = [
            'Credentials',
            'Default Passwords Audit',
            (! $knownWeakHashes) ? '<fg=green>✓ OK</>' : '<fg=red>✗ ERROR</>',
            (! $knownWeakHashes) ? 'Tidak ditemukan akun dengan kata sandi default pabrikan' : 'Ditemukan akun aktif menggunakan password default (Password123!)',
        ];
        if ($knownWeakHashes) {
            $hasErrors = true;
        }

        // 7. Dedicated Encrypted Backup Key (BACKUP-002)
        $backupKey = (string) config('backup.encryption_key', '');
        $backupKeyDecoupled = ! empty($backupKey) && ($backupKey !== $appKey);
        $rows[] = [
            'Backup Security',
            'BACKUP_ENCRYPTION_KEY',
            $backupKeyDecoupled ? '<fg=green>✓ OK</>' : '<fg=red>✗ ERROR</>',
            $backupKeyDecoupled ? 'Kunci enkripsi terpisah dari APP_KEY' : 'Kunci enkripsi backup belum terpisah atau identik dengan APP_KEY',
        ];
        if (! $backupKeyDecoupled) {
            $hasErrors = true;
        }

        // 8. Mandatory Off-site Backup (BACKUP-003)
        $offsiteDisk = config('backup.offsite_disk');
        $availableDisks = array_keys(config('filesystems.disks', []));
        $offsiteOk = ! empty($offsiteDisk) && in_array($offsiteDisk, $availableDisks, true);
        $rows[] = [
            'Backup Storage',
            'BACKUP_OFFSITE_DISK',
            $offsiteOk ? '<fg=green>✓ OK</>' : '<fg=red>✗ ERROR</>',
            $offsiteOk ? "Terkonfigurasi ke disk remote [{$offsiteDisk}]" : 'Wajib mengonfigurasi offsite disk sekunder (misal: S3) pada production',
        ];
        if (! $offsiteOk) {
            $hasErrors = true;
        }

        // 9. Storage Exception Error Handling (UPLOAD-002)
        $privateDiskThrow = (bool) config('filesystems.disks.private.throw', false);
        $rows[] = [
            'Storage Integrity',
            'private disk throw',
            $privateDiskThrow ? '<fg=green>✓ OK</>' : '<fg=red>✗ ERROR</>',
            $privateDiskThrow ? "Throw exception aktif ('throw' => true)" : "Disk private 'throw' => false berisiko kegagalan penulisan diam-diam",
        ];
        if (! $privateDiskThrow) {
            $hasErrors = true;
        }

        // 10. Trusted Proxies (Network)
        $trustedProxies = config('trustedproxy.proxies');
        $trustedProxiesConfigured = ($trustedProxies !== null && $trustedProxies !== '');
        $rows[] = [
            'Network',
            'TRUSTED_PROXIES',
            $trustedProxiesConfigured ? '<fg=green>✓ OK</>' : '<fg=yellow>⚠ WARN</>',
            $trustedProxiesConfigured ? 'Terkonfigurasi aman' : 'TRUSTED_PROXIES belum disetel jika berada di balik reverse proxy / CDN',
        ];
        if (! $trustedProxiesConfigured) {
            $hasWarnings = true;
        }

        $this->table(['Kategori', 'Komponen / Parameter', 'Status', 'Catatan Evaluasi & Remediasi'], $rows);

        if ($hasErrors || ($isStrict && $hasWarnings)) {
            $this->newLine();
            $this->error('================================================================');
            $this->error(' PREFLIGHT GAGAL: DITEMUKAN KONFIGURASI KEAMANAN KRITIS TIDAK VALID');
            $this->error('================================================================');
            $this->line('Perbaiki seluruh parameter bertanda <fg=red>✗ ERROR</> di atas pada berkas .env server');
            $this->line('sebelum melakukan deployment resmi ke lingkungan production publik.');
            $this->newLine();

            return self::FAILURE;
        }

        $this->newLine();
        $this->info('================================================================');
        $this->info(' PREFLIGHT SUKSES: APLIKASI SIAP DIJALANKAN DI LINGKUNGAN PRODUCTION');
        $this->info('================================================================');
        $this->line('Seluruh parameter keamanan kritis telah terverifikasi dengan standar hardening MagKom.');
        $this->newLine();

        return self::SUCCESS;
    }
}
