<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\DocumentVerificationService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class VerifikasiDokumenController extends Controller
{
    public function __invoke(string $kodeUnik, DocumentVerificationService $service, Request $request): Response|View
    {
        $dokumen = $service->verify($kodeUnik);

        if (! $dokumen) {
            return response()->view('public.verifikasi-dokumen', [
                'dokumen' => null,
                'kodeUnik' => $kodeUnik,
            ], 404);
        }

        return view('public.verifikasi-dokumen', [
            'dokumen' => $dokumen,
            'kodeUnik' => $kodeUnik,
        ]);
    }
}
