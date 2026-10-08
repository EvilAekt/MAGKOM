<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$bulan = \App\Models\BulanMagang::first();
echo "Raw override_status: ";
var_dump($bulan->getAttributes()['override_status']);
echo "Cast override_status: ";
var_dump($bulan->override_status);

$service = app(\App\Services\BulanStatusService::class);
$st = $service->status($bulan);
echo "Service status: ";
var_dump($st);
