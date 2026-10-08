<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Enums\TipeAuditEnum;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    /**
     * Menampilkan daftar jejak audit sistem (strictly read-only) (AC-13-6).
     */
    public function index(Request $request): View
    {
        $query = AuditLog::with('user')->orderBy('id', 'desc');

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

        $logs = $query->paginate(25)->withQueryString();

        return view('superadmin.audit-log.index', [
            'logs' => $logs,
            'tipeEnum' => TipeAuditEnum::cases(),
        ]);
    }
}
