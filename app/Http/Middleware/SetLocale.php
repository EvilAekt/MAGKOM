<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Daftar kode bahasa yang didukung oleh sistem MagKom.
     *
     * @var array<string>
     */
    protected const SUPPORTED_LOCALES = ['id', 'en'];

    /**
     * Tangani permintaan masuk dan atur locale aplikasi berdasarkan preferensi pengguna.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = null;

        if ($request->hasSession() && $request->session()->has('bahasa_portal')) {
            $locale = $request->session()->get('bahasa_portal');
        } elseif ($request->hasCookie('bahasa_portal')) {
            $locale = $request->cookie('bahasa_portal');
        }

        if (! is_string($locale) || ! in_array($locale, self::SUPPORTED_LOCALES, true)) {
            $locale = config('app.locale', 'id');
        }

        App::setLocale($locale);
        Carbon::setLocale($locale);

        view()->share('bahasaAktif', $locale);

        return $next($request);
    }
}
