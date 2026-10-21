<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\BulanMagang;
use App\Services\PlacementService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class TutupWaitingListExpiredCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'magkom:tutup-waiting-list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menutup antrean waiting list secara otomatis saat bulan magang telah dimulai (AC-04-7)';

    /**
     * Execute the console command.
     */
    public function handle(PlacementService $placementService): int
    {
        $hariIni = now('Asia/Jakarta')->startOfDay();

        $bulanMagangBerjalan = BulanMagang::where('aktif', true)
            ->get()
            ->filter(function ($bulan) use ($hariIni) {
                $tglMulai = Carbon::parse($bulan->bulan_mulai)->timezone('Asia/Jakarta')->startOfMonth();

                return $hariIni->gte($tglMulai);
            });

        $totalDitutup = 0;
        foreach ($bulanMagangBerjalan as $bulan) {
            $ditutup = $placementService->tutupWaitingListKadaluarsa($bulan);
            $totalDitutup += $ditutup;
            if ($ditutup > 0) {
                $this->info("Bulan {$bulan->bulan_mulai}: {$ditutup} antrean waiting list berhasil ditutup.");
            }
        }

        $this->info("Total {$totalDitutup} antrean waiting list kadaluarsa berhasil diproses.");

        return Command::SUCCESS;
    }
}
