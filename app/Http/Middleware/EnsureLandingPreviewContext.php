<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\RoleEnum;
use App\Support\Pengaturan;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureLandingPreviewContext
{
    /**
     * Mengaktifkan mode pratinjau draf CMS jika request meminta pratinjau dan user adalah Super Admin.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $isSuperAdmin = $request->user()?->role === RoleEnum::SuperAdmin;
        $wantsPreview = $request->has('preview') && $isSuperAdmin;

        if ($request->routeIs('superadmin.landing.preview')) {
            $wantsPreview = true;
        }

        Pengaturan::setPreviewMode($wantsPreview);
        view()->share('isPreview', $wantsPreview);

        $response = $next($request);

        // Reset kembali setelah request selesai agar tidak bocor ke request berikutnya
        Pengaturan::setPreviewMode(false);
        view()->share('isPreview', false);

        return $response;
    }
}
