<?php

declare(strict_types=1);

namespace App\Http\ViewComposers;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class PesertaLayoutViewComposer
{
    /**
     * Bind data ke view layout peserta.
     */
    public function compose(View $view): void
    {
        $user = Auth::user();

        if (! $user instanceof User || $user->role !== RoleEnum::Peserta) {
            $view->with('isDiterima', false);

            return;
        }

        // Cache hasil pengecekan penempatan peserta selama 60 detik per-user untuk mencegah redundant queries
        $isDiterima = Cache::remember('peserta_layout_diterima_'.$user->id, 60, function () use ($user) {
            return $user->hasPenempatanMagang();
        });

        $view->with('isDiterima', $isDiterima);
    }
}
