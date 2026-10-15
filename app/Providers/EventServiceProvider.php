<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        $observedModels = [
            \App\Models\User::class,
            \App\Models\Pendaftaran::class,
            \App\Models\Dokumen::class,
            \App\Models\Penempatan::class,
            \App\Models\Kuota::class,
            \App\Models\BulanMagang::class,
            \App\Models\SlotKhusus::class,
            \App\Models\Tugas::class,
            \App\Models\Logbook::class,
            \App\Models\Penilaian::class,
            \App\Models\Pengaturan::class,
            \App\Models\Jurusan::class,
            \App\Models\Bidang::class,
            \App\Models\Rubrik::class,
            \App\Models\ModulDinamis::class,
            \App\Models\ModulDinamisData::class,
        ];

        foreach ($observedModels as $modelClass) {
            if (class_exists($modelClass)) {
                $modelClass::observe(\App\Observers\AuditableObserver::class);
            }
        }
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
