<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\CaptchaService;
use Illuminate\Http\Response;

class CaptchaController extends Controller
{
    public function __construct(
        protected CaptchaService $captchaService
    ) {}

    /**
     * Sajikan stream gambar SVG biner Captcha dinamis dengan header anti-cache.
     */
    public function image(): Response
    {
        $svg = $this->captchaService->renderSvg();

        return response($svg, 200, [
            'Content-Type' => 'image/svg+xml; charset=utf-8',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0, post-check=0, pre-check=0',
            'Pragma' => 'no-cache',
            'Expires' => 'Sat, 01 Jan 2000 00:00:00 GMT',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
