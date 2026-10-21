<?php

use App\Models\BulanMagang;
use App\Services\BulanStatusService;
use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$bulan = BulanMagang::first();
echo 'Raw override_status: ';
var_dump($bulan->getAttributes()['override_status']);
echo 'Cast override_status: ';
var_dump($bulan->override_status);

$service = app(BulanStatusService::class);
$st = $service->status($bulan);
echo 'Service status: ';
var_dump($st);
