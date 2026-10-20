<?php

namespace App\Providers;

use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Dokumen;
use App\Models\Jurusan;
use App\Models\Kuota;
use App\Models\Logbook;
use App\Models\ModulDinamis;
use App\Models\ModulDinamisData;
use App\Models\Pendaftaran;
use App\Models\Penempatan;
use App\Models\Pengaturan;
use App\Models\Penilaian;
use App\Models\Rubrik;
use App\Models\SlotKhusus;
use App\Models\Tugas;
use App\Models\User;
use App\Observers\AuditableObserver;
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
            User::class,
            Pendaftaran::class,
            Dokumen::class,
            Penempatan::class,
            Kuota::class,
            BulanMagang::class,
            SlotKhusus::class,
            Tugas::class,
            Logbook::class,
            Penilaian::class,
            Pengaturan::class,
            Jurusan::class,
            Bidang::class,
            Rubrik::class,
            ModulDinamis::class,
            ModulDinamisData::class,
        ];

        foreach ($observedModels as $modelClass) {
            if (class_exists($modelClass)) {
                $modelClass::observe(AuditableObserver::class);
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
