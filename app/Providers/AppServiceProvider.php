<?php

namespace App\Providers;

use App\Enums\RoleEnum;
use App\Enums\StatusLogbookEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\StatusReviewProgresEnum;
use App\Models\Logbook;
use App\Models\Penempatan;
use App\Models\ProgresLog;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        // View Composer untuk Sidebar Dashboard Pembimbing (B10)
        View::composer('layouts.dashboard', function ($view) {
            $user = auth()->user();
            if ($user && $user->role === RoleEnum::Pembimbing) {
                $badgeData = Cache::remember('pembimbing_badge_' . $user->id, 60, function () use ($user) {
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
    }
}
