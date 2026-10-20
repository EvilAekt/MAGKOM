<?php

use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\Kuota;
use Database\Seeders\PeriodeKuotaSeeder;
use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

BulanMagang::query()->update(['override_status' => null]);
(new PeriodeKuotaSeeder)->run();

$tkj = Jurusan::where('nama_jurusan', 'like', '%TKJ%')->first();
$bulanNov = BulanMagang::where('bulan_mulai', '2026-11-01')->first();
if ($tkj && $bulanNov) {
    Kuota::where('bulan_magang_id', $bulanNov->id)
        ->where('jurusan_id', $tkj->id)
        ->update(['jumlah' => 5, 'terisi' => 5]);
}

echo "Reset berhasil!\n";
