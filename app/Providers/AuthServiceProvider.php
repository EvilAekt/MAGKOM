<?php

namespace App\Providers;

use App\Models\Dokumen;
use App\Models\Logbook;
use App\Models\Pendaftaran;
use App\Models\Penempatan;
use App\Models\Tugas;
use App\Policies\DokumenPolicy;
use App\Policies\LogbookPolicy;
use App\Policies\PendaftaranPolicy;
use App\Policies\PenempatanPolicy;
use App\Policies\TugasPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

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
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // RBAC-001: Dynamic permission checks via Gate
        Gate::before(function ($user, $ability) {
            if ($user && method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
                return true;
            }
        });

        Gate::define('permission', function ($user, string $slug) {
            return $user && method_exists($user, 'hasPermission') && $user->hasPermission($slug);
        });
    }
}
