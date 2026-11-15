<?php

namespace App\Providers;

use App\Enums\RoleEnum;
use App\Enums\StatusLogbookEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\StatusReviewProgresEnum;
use App\Http\ViewComposers\PesertaLayoutViewComposer;
use App\Models\Logbook;
use App\Models\Penempatan;
use App\Models\ProgresLog;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use RuntimeException;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Proteksi Pengiriman Email Production (OPS-001): Cegah driver log berjalan di production
        Event::listen(MessageSending::class, function (): void {
            if ($this->app->isProduction() && config('mail.default') === 'log') {
                throw new RuntimeException(
                    "FATAL: Pengiriman email di lingkungan production dilarang menggunakan driver 'log'. Konfigurasikan MAIL_MAILER=smtp dengan kredensial resmi pada berkas .env server."
                );
            }
        });

        // Daftarkan Custom Pagination MagKom (Cegah Tailwind SVG Blowup)
        Paginator::defaultView('vendor.pagination.default');
        Paginator::defaultSimpleView('vendor.pagination.simple-default');

        // View Composer untuk Sidebar Dashboard Pembimbing (B10)
        View::composer('layouts.dashboard', function ($view) {
            $user = auth()->user();
            if ($user && $user->role === RoleEnum::Pembimbing) {
                $badgeData = Cache::remember('pembimbing_badge_'.$user->id, 60, function () use ($user) {
                    $penempatanIds = Penempatan::where('pembimbing_id', $user->id)
                        ->where('status', StatusPenempatanEnum::Aktif)
                        ->pluck('id');

                    $tugasReview = ProgresLog::whereHas('tugas', function ($q) use ($penempatanIds) {
                        $q->whereIn('penempatan_id', $penempatanIds);
                    })->where('status_review', StatusReviewProgresEnum::Menunggu)->count();

                    $logbookReview = Logbook::whereIn('penempatan_id', $penempatanIds)
                        ->where('status', StatusLogbookEnum::Terkirim)
                        ->count();

                    return [
                        'sidebarBadgeTugas' => $tugasReview,
                        'sidebarBadgeLogbook' => $logbookReview,
                    ];
                });

                $view->with($badgeData);
            }
        });

        // View Composer untuk Layout Peserta (PERF-P2-03)
        View::composer('layouts.peserta', PesertaLayoutViewComposer::class);
    }
}
