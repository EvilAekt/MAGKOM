<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

\App\Models\BulanMagang::query()->update(['override_status' => null]);
(new \Database\Seeders\PeriodeKuotaSeeder())->run();

$tkj = \App\Models\Jurusan::where('nama_jurusan', 'like', '%TKJ%')->first();
$bulanNov = \App\Models\BulanMagang::where('bulan_mulai', '2026-11-01')->first();
if ($tkj && $bulanNov) {
    \App\Models\Kuota::where('bulan_magang_id', $bulanNov->id)
        ->where('jurusan_id', $tkj->id)
        ->update(['jumlah' => 5, 'terisi' => 5]);
}

echo "Reset berhasil!\n";
