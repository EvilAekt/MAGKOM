<?php

namespace Tests;

use App\Support\Pengaturan;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Cache;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function tearDown(): void
    {
        Pengaturan::setPreviewMode(false);
        Cache::flush();
        parent::tearDown();
    }
}
