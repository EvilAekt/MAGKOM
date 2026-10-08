<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Enums\TipeAuditEnum;
use App\Http\Controllers\Controller;
use App\Services\AuditService;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VerificationController extends Controller
{
    public function __construct(
        protected AuditService $auditService
    ) {}

    /**
     * Menampilkan pemberitahuan verifikasi email (AC-02-3).
     */
    public function notice(Request $request): View|RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('peserta.pendaftaran.index');
        }

        return view('auth.verify-email');
    }

    /**
     * Memproses verifikasi email lewat tautan bertanda tangan (AC-02-4).
     */
    public function verify(EmailVerificationRequest $request): RedirectResponse
    {
        if (! $request->user()->hasVerifiedEmail()) {
            $request->fulfill();

            $this->auditService->catat(
                tipe: TipeAuditEnum::Auth,
                tabel: 'users',
                idData: $request->user()->id,
                detail: [
                    'aksi' => 'Email pengguna berhasil diverifikasi',
                    'email' => $request->user()->email,
                ]
            );
        }

        return redirect()->route('peserta.profil.show')
            ->with('success', 'Email Anda berhasil diverifikasi. Silakan lengkapi profil Anda.');
    }

    /**
     * Mengirim ulang email verifikasi dengan rate limiting (AC-02-4).
     */
    public function resend(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('peserta.pendaftaran.index');
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    }
}
