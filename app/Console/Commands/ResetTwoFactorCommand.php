<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\TipeAuditEnum;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Console\Command;

class ResetTwoFactorCommand extends Command
{
    /**
     * Nama dan signature perintah console (AUTH-002).
     *
     * @var string
     */
    protected $signature = 'auth:2fa-reset 
                            {email : Alamat email pengguna SuperAdmin yang akan direset 2FA-nya}
                            {--force : Lewati konfirmasi interaktif}';

    /**
     * Deskripsi perintah console.
     *
     * @var string
     */
    protected $description = 'Mereset (menonaktifkan) autentikasi dua faktor untuk akun SuperAdmin dalam situasi darurat (account recovery)';

    /**
     * Eksekusi perintah console.
     */
    public function handle(AuditService $auditService): int
    {
        $email = (string) $this->argument('email');
        $user = User::where('email', $email)->first();

        if (! $user) {
            $this->error("Pengguna dengan email '{$email}' tidak ditemukan.");

            return Command::FAILURE;
        }

        if (! $user->hasEnabledTwoFactor()) {
            $this->info("Pengguna '{$email}' tidak memiliki 2FA yang aktif.");

            return Command::SUCCESS;
        }

        $this->warn('========================================================');
        $this->warn('  EMERGENCY RECOVERY: RESET 2FA AUTENTIKASI DUA FAKTOR  ');
        $this->warn('========================================================');
        $this->line("Nama Pengguna : {$user->nama}");
        $this->line("Email         : {$user->email}");
        $this->line("Role          : {$user->role->value}");

        if (! $this->option('force')) {
            if (! $this->confirm("Apakah Anda yakin ingin menonaktifkan 2FA untuk '{$email}'?", false)) {
                $this->comment('Operasi dibatalkan.');

                return Command::FAILURE;
            }
        }

        $user->disableTwoFactor();

        $auditService->catat(
            tipe: TipeAuditEnum::Auth,
            tabel: 'users',
            idData: $user->id,
            nilaiLama: ['two_factor_enabled' => true],
            nilaiBaru: ['two_factor_enabled' => false],
            detail: [
                'aksi' => '2fa.emergency_cli_reset',
                'reset_by' => 'cli_administrator',
            ],
            user: $user,
            channel: 'security-auth',
            severity: 'warning'
        );

        $this->info("✓ 2FA berhasil dinonaktifkan untuk pengguna '{$email}'. Pengguna kini dapat login kembali menggunakan kata sandi.");

        return Command::SUCCESS;
    }
}
