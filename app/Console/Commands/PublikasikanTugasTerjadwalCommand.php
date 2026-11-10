<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\RealtimeEventTypeEnum;
use App\Enums\TipeAuditEnum;
use App\Models\Tugas;
use App\Services\AuditService;
use App\Services\RealtimeEventService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PublikasikanTugasTerjadwalCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'magkom:publikasikan-tugas-terjadwal';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mempublikasikan tugas bimbingan yang telah dijadwalkan secara otomatis ketika waktu publikasi tercapai';

    /**
     * Execute the console command.
     */
    public function handle(AuditService $auditService, RealtimeEventService $realtimeEventService): int
    {
        $waktuSekarang = now('Asia/Jakarta');

        $tugasTerjadwalList = Tugas::with('penempatan.peserta')
            ->where('status_publikasi', 'terjadwal')
            ->where('jadwal_publikasi', '<=', $waktuSekarang)
            ->get();

        if ($tugasTerjadwalList->isEmpty()) {
            $this->info('Tidak ada tugas terjadwal yang siap dipublikasikan.');

            return Command::SUCCESS;
        }

        $totalDipublikasikan = 0;

        foreach ($tugasTerjadwalList as $tugas) {
            DB::transaction(function () use ($tugas, $auditService, $realtimeEventService, &$totalDipublikasikan) {
                $nilaiLama = ['status_publikasi' => $tugas->status_publikasi];

                $tugas->update([
                    'status_publikasi' => 'dipublikasikan',
                ]);

                $auditService->catat(
                    tipe: TipeAuditEnum::Aksi,
                    tabel: 'tugas',
                    idData: $tugas->id,
                    nilaiLama: $nilaiLama,
                    nilaiBaru: ['status_publikasi' => 'dipublikasikan'],
                    detail: ['aksi' => 'publikasikan_tugas_terjadwal_otomatis']
                );

                if ($tugas->penempatan?->peserta_id) {
                    $realtimeEventService->publish(
                        RealtimeEventTypeEnum::TugasBaru,
                        [
                            'title' => 'Tugas Magang Baru',
                            'message' => "Tugas terjadwal telah aktif: {$tugas->judul}.",
                            'deadline' => $tugas->deadline?->format('d/m/Y H:i'),
                            'action_url' => route('peserta.tugas.index'),
                        ],
                        $tugas->penempatan->peserta_id
                    );
                }

                $totalDipublikasikan++;
            });
        }

        $this->info("Total {$totalDipublikasikan} tugas terjadwal berhasil dipublikasikan.");

        return Command::SUCCESS;
    }
}
