<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        \App\Models\Pendaftaran::class => \App\Policies\PendaftaranPolicy::class,
        \App\Models\Dokumen::class => \App\Policies\DokumenPolicy::class,
        \App\Models\Penempatan::class => \App\Policies\PenempatanPolicy::class,
        \App\Models\Tugas::class => \App\Policies\TugasPolicy::class,
        \App\Models\Logbook::class => \App\Policies\LogbookPolicy::class,
        \App\Models\Penilaian::class => \App\Policies\PenilaianPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
