<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('magkom:nonaktifkan-peserta')
            ->dailyAt('00:01')
            ->timezone('Asia/Jakarta')
            ->withoutOverlapping();

        // Otomatisasi penutupan waiting list awal bulan magang (AC-04-7)
        $schedule->command('magkom:tutup-waiting-list')
            ->dailyAt('00:05')
            ->timezone('Asia/Jakarta')
            ->withoutOverlapping();

        // Pemangkasan berkala log akses data (PRD 10.3 NFR-A5)
        $schedule->command('magkom:audit-purge')
            ->monthlyOn(1, '02:00')
            ->timezone('Asia/Jakarta')
            ->withoutOverlapping();

        // Pembersihan keamanan berkala & higienitas sistem (PRD 8.4, 10.3)
        $schedule->command('magkom:security-sweep')
            ->dailyAt('03:00')
            ->timezone('Asia/Jakarta')
            ->withoutOverlapping();
    }


    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
