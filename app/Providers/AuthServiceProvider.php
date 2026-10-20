<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\Dokumen;
use App\Models\Logbook;
use App\Models\Pendaftaran;
use App\Models\Penempatan;
use App\Models\Penilaian;
use App\Models\Tugas;
use App\Policies\DokumenPolicy;
use App\Policies\LogbookPolicy;
use App\Policies\PendaftaranPolicy;
use App\Policies\PenempatanPolicy;
use App\Policies\PenilaianPolicy;
use App\Policies\TugasPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Pendaftaran::class => PendaftaranPolicy::class,
        Dokumen::class => DokumenPolicy::class,
        Penempatan::class => PenempatanPolicy::class,
        Tugas::class => TugasPolicy::class,
        Logbook::class => LogbookPolicy::class,
        Penilaian::class => PenilaianPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
