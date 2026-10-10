<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Enums\RoleEnum;
use App\Enums\TipeAuditEnum;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AuditLogController extends Controller
{
    /**
     * Menampilkan daftar jejak audit sistem (strictly read-only) (AC-13-6).
     */
    public function index(Request $request): View
    {
        $query = $this->buildFilterQuery($request);

        $logs = $query->paginate(25)->withQueryString();
        $internalUsers = User::whereIn('role', [RoleEnum::SuperAdmin, RoleEnum::Admin, RoleEnum::Pembimbing])
            ->orderBy('nama')
            ->get();

        return view('superadmin.audit-log.index', [
            'logs' => $logs,
            'tipeEnum' => TipeAuditEnum::cases(),
            'internalUsers' => $internalUsers,
        ]);
    }

    /**
     * Ekspor jejak audit sistem ke file CSV dengan stream download (AC-13-6).
     */
    public function export(Request $request, AuditService $auditService): StreamedResponse
    {
        // Catat jejak audit aktivitas ekspor ke sistem (AC-13-6)
        $auditService->catat(
            tipe: TipeAuditEnum::AksesData,
            tabel: 'audit_log',
            idData: null,
            nilaiLama: null,
            nilaiBaru: null,
            detail: [
                'aksi' => 'Ekspor jejak audit trail ke CSV',
                'filter' => array_filter($request->only(['q', 'tipe', 'entitas', 'request_id', 'user_id', 'dari_tanggal', 'sampai_tanggal'])),
            ],
            user: Auth::id()
        );

        $fileName = 'audit-trail-' . now()->format('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($request) {
            $handle = fopen('php://output', 'wb');

            // Tulis BOM UTF-8 untuk kompatibilitas Excel
            fwrite($handle, "\xEF\xBB\xBF");

            // Baris Header CSV
            fputcsv($handle, [
                'ID',
                'Waktu (WIB)',
                'Request ID',
                'Pengguna',
                'Role',
                'Tipe',
                'Aksi',
                'Entitas',
                'Entitas ID',
                'IP Address',
                'Metode HTTP',
                'Rute',
                'Status HTTP',
                'Durasi (ms)',
            ]);

            $query = $this->buildFilterQuery($request);

            // Stream dalam chunk kecil untuk menjaga penggunaan memori tetap minimal
            $query->chunk(250, function ($logs) use ($handle) {
                foreach ($logs as $log) {
                    fputcsv($handle, [
                        $this->sanitizeCsvCell($log->id),
                        $this->sanitizeCsvCell($log->waktu?->format('Y-m-d H:i:s') ?? '-'),
                        $this->sanitizeCsvCell($log->request_id ?? '-'),
                        $this->sanitizeCsvCell($log->user?->nama ?? 'Sistem / Anonim'),
                        $this->sanitizeCsvCell($log->user?->role?->label() ?? '-'),
                        $this->sanitizeCsvCell($log->tipe?->value ?? '-'),
                        $this->sanitizeCsvCell($log->aksi ?? '-'),
                        $this->sanitizeCsvCell($log->entitas ?? '-'),
                        $this->sanitizeCsvCell($log->entitas_id ?? '-'),
                        $this->sanitizeCsvCell($log->ip ?? '-'),
                        $this->sanitizeCsvCell($log->metode ?? '-'),
                        $this->sanitizeCsvCell($log->rute ?? '-'),
                        $this->sanitizeCsvCell($log->status_http ?? '-'),
                        $this->sanitizeCsvCell($log->durasi_ms ?? '-'),
                    ]);
                }
            });

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0',
            'Pragma' => 'no-cache',
        ]);
    }

    /**
     * Membangun query filter Audit Log berdasarkan parameter request.
     */
    private function buildFilterQuery(Request $request): Builder
    {
        $query = AuditLog::with('user')->orderBy('id', 'desc');

        if ($request->filled('q')) {
            $keyword = trim((string) $request->input('q'));
            $query->where(function (Builder $sub) use ($keyword) {
                $sub->where('aksi', 'like', "%{$keyword}%")
                    ->orWhere('entitas', 'like', "%{$keyword}%")
                    ->orWhere('request_id', 'like', "%{$keyword}%")
                    ->orWhere('ip', 'like', "%{$keyword}%")
                    ->orWhereHas('user', function (Builder $u) use ($keyword) {
                        $u->where('nama', 'like', "%{$keyword}%")
                            ->orWhere('email', 'like', "%{$keyword}%");
                    });
            });
        }

        if ($request->filled('tipe')) {
            $tipeEnum = TipeAuditEnum::tryFrom($request->input('tipe'));
            if ($tipeEnum) {
                $query->where('tipe', $tipeEnum->value);
            }
        }

        if ($request->filled('entitas')) {
            $query->where('entitas', $request->input('entitas'));
        }

        if ($request->filled('request_id')) {
            $query->where('request_id', $request->input('request_id'));
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->integer('user_id'));
        }

        if ($request->filled('dari_tanggal') && $request->filled('sampai_tanggal')) {
            $query->whereBetween('waktu', [
                $request->input('dari_tanggal') . ' 00:00:00',
                $request->input('sampai_tanggal') . ' 23:59:59',
            ]);
        } elseif ($request->filled('dari_tanggal')) {
            $query->where('waktu', '>=', $request->input('dari_tanggal') . ' 00:00:00');
        } elseif ($request->filled('sampai_tanggal')) {
            $query->where('waktu', '<=', $request->input('sampai_tanggal') . ' 23:59:59');
        }

        return $query;
    }

    /**
     * Sanitasi sel CSV untuk mencegah Formula Injection (OWASP).
     */
    private function sanitizeCsvCell(mixed $value): string
    {
        if ($value === null) {
            return '';
        }

        $str = (string) $value;

        if ($str !== '' && in_array($str[0], ['=', '+', '-', '@', "\t", "\r"], true)) {
            return "'" . $str;
        }

        return $str;
    }
}

