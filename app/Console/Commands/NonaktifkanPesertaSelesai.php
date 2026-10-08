<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\MasaTenggangService;
use Illuminate\Console\Command;

class NonaktifkanPesertaSelesai extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'magkom:nonaktifkan-peserta';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menonaktifkan penempatan dan akun peserta yang telah melewati batas masa tenggang (BR-07)';

    /**
     * Execute the console command.
     */
    public function handle(MasaTenggangService $masaTenggangService): int
    {
        $this->info('Memulai pengecekan masa tenggang peserta magang...');

        $total = $masaTenggangService->nonaktifkanPesertaKedaluwarsa();

        $this->info("Pengecekan selesai. Sebanyak {$total} penempatan peserta berhasil dinonaktifkan.");

        return Command::SUCCESS;
    }
}
