<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\RoleEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\TipeAuditEnum;
use App\Models\AuditLog;
use App\Models\User;
use App\Services\DocumentStorageService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AnonimkanPesertaCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'magkom:anonimkan-peserta {user_id : ID pengguna peserta yang akan dianonimkan} {--alasan=Permohonan hak subjek data UU PDP : Alasan pelaksanaan anonimisasi}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Melakukan anonimisasi permanen data pribadi (PII) akun peserta magang untuk kepatuhan hak subjek data UU PDP';

    /**
     * Execute the console command.
     */
    public function handle(DocumentStorageService $storageService): int
    {
        $userId = (int) $this->argument('user_id');
        $alasan = (string) ($this->option('alasan') ?? 'Permohonan hak subjek data UU PDP');

        $user = User::find($userId);

        if (! $user) {
            $this->error("Peserta dengan ID {$userId} tidak ditemukan dalam database.");
            return Command::FAILURE;
        }

        if ($user->role !== RoleEnum::Peserta) {
            $this->error("Pengguna ID {$userId} memiliki role [{$user->role->value}], bukan peserta magang. Anonimisasi dibatalkan.");
            return Command::FAILURE;
        }

        // Pastikan tidak ada penempatan yang sedang aktif berjalan
        $isAktif = $user->penempatanPeserta()
            ->where('status', StatusPenempatanEnum::Aktif)
            ->exists();

        if ($isAktif) {
            $this->error("Peserta ID {$userId} sedang aktif menjalani magang. Batalkan atau selesaikan penempatan magang terlebih dahulu.");
            return Command::FAILURE;
        }

        $this->warn("Akan melakukan anonimisasi akun peserta: #{$user->id} ({$user->nama})");

        DB::transaction(function () use ($user, $alasan, $storageService) {
            // 1. Scrubbing data profil & hapus foto fisik jika ada
            if ($user->profil) {
                if ($user->profil->foto && Storage::disk('public')->exists($user->profil->foto)) {
                    Storage::disk('public')->delete($user->profil->foto);
                }

                $user->profil->update([
                    'foto' => null,
                    'no_hp' => '080000000000',
                    'nim_nisn' => 'ANON-' . $user->id,
                    'nama_kontak_darurat' => null,
                    'no_kontak_darurat' => null,
                    'hubungan_kontak_darurat' => null,
                ]);
            }

            // 2. Pembersihan seluruh berkas fisik pendaftaran dari private disk
            foreach ($user->pendaftaran as $pendaftaran) {
                $storageService->hapusBerkasPendaftaran($pendaftaran);
            }

            // 3. Scrubbing data akun kredensial
            $user->nama = "Peserta Teranonimisasi #{$user->id}";
            $user->email = "anonymized_{$user->id}@magkom.internal";
            $user->password = null;
            $user->provider = null;
            $user->provider_id = null;
            $user->status_aktif = false;
            $user->remember_token = null;
            $user->save();

            // 4. Pencatatan audit trail forensik append-only
            AuditLog::create([
                'request_id' => (string) Str::uuid(),
                'user_id' => null,
                'tipe' => TipeAuditEnum::Aksi,
                'aksi' => 'Anonimisasi Data Peserta UU PDP',
                'entitas' => 'User',
                'entitas_id' => $user->id,
                'nilai_lama' => null,
                'nilai_baru' => [
                    'target_user_id' => $user->id,
                    'alasan' => $alasan,
                ],
                'detail' => [
                    'alasan' => $alasan,
                    'executor' => 'CLI magkom:anonimkan-peserta',
                ],
                'metode' => 'CLI',
                'rute' => 'artisan:magkom:anonimkan-peserta',
                'status_http' => 200,
                'durasi_ms' => 0,
                'ip' => '127.0.0.1',
                'user_agent' => 'Symfony/Console',
                'waktu' => now(),
            ]);
        });

        $this->info("Berhasil melakukan anonimisasi akun peserta #{$user->id} sesuai mandat UU PDP.");
        return Command::SUCCESS;
    }
}
