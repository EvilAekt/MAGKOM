<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\TipeAuditEnum;
use App\Services\AuditService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasPermission
{
    public function __construct(
        protected AuditService $auditService
    ) {}

    /**
     * Menegakkan pemeriksaan otorisasi RBAC dinamis pada backend (RBAC-001).
     *
     * @param  string  ...$permissions  Satu atau lebih slug hak akses yang diizinkan (OR logic)
     */
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        $user = $request->user();

        if (! $user) {
            return $request->expectsJson()
                ? response()->json(['message' => 'Unauthenticated.'], 401)
                : redirect()->guest(route('login'));
        }

        // Jika tidak ada permission yang didefinisikan pada rute, izinkan lewat
        if (empty($permissions)) {
            return $next($request);
        }

        // Periksa apakah pengguna memiliki salah satu dari permission yang disyaratkan
        $hasAnyPermission = false;
        foreach ($permissions as $slug) {
            if ($user->hasPermission($slug)) {
                $hasAnyPermission = true;
                break;
            }
        }

        if (! $hasAnyPermission) {
            $requiredPerms = implode(', ', $permissions);

            // Catat insiden pelanggaran hak akses ke audit trail
            $this->auditService->catat(
                tipe: TipeAuditEnum::Auth,
                tabel: 'permissions',
                idData: null,
                detail: [
                    'aksi' => 'AKSES_DITOLAK_RBAC_DINAMIS',
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                    'role' => $user->role->value,
                    'required_permissions' => $permissions,
                    'url' => $request->fullUrl(),
                    'method' => $request->method(),
                    'ip_address' => $request->ip(),
                ]
            );

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => "Akses ditolak: Anda tidak memiliki wewenang modul [{$requiredPerms}].",
                ], 403);
            }

            abort(403, "Akses ditolak: Peran akun Anda saat ini tidak memiliki hak akses untuk modul [{$requiredPerms}]. Hubungi SuperAdmin jika Anda membutuhkan penyesuaian hak akses.");
        }

        return $next($request);
    }
}
