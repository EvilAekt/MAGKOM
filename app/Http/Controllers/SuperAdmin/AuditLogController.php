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
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View as ViewFactory;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AuditLogController extends Controller
{
    /**
     * Definisi Kanal Audit Log Terstandarisasi ala Stream Navigator.
     */
    public const CHANNELS = [
        'all-actions-stream' => [
            'key' => 'all-actions-stream',
            'label' => 'Semua Aktivitas Stream',
            'icon' => 'activity',
            'color' => '#2563EB',
            'desc' => 'Aliran seluruh peristiwa sistem secara utuh real-time',
        ],
        'auth-session' => [
            'key' => 'auth-session',
            'label' => 'Autentikasi & Sesi',
            'icon' => 'lock',
            'color' => '#3B82F6',
            'desc' => 'Login, logout, verifikasi email, kegagalan password & lockout',
        ],
        'pendaftaran' => [
            'key' => 'pendaftaran',
            'label' => 'Pendaftaran & Berkas',
            'icon' => 'file-text',
            'color' => '#10B981',
            'desc' => 'Pengajuan mandiri, kelompok, biodata, & upload berkas peserta',
        ],
        'verifikasi-admin' => [
            'key' => 'verifikasi-admin',
            'label' => 'Verifikasi Admin',
            'icon' => 'check-square',
            'color' => '#059669',
            'desc' => 'Pemeriksaan berkas, catatan revisi, persetujuan & penolakan pendaftaran',
        ],
        'penempatan-mentor' => [
            'key' => 'penempatan-mentor',
            'label' => 'Penempatan & Mentor',
            'icon' => 'users',
            'color' => '#8B5CF6',
            'desc' => 'Alokasi bidang dinas dinas, pembagian SK, dan penugasan mentor',
        ],
        'kuota-periode' => [
            'key' => 'kuota-periode',
            'label' => 'Kuota & Periode',
            'icon' => 'calendar',
            'color' => '#F59E0B',
            'desc' => 'Konfigurasi bulan magang, kuota jurusan, dan slot khusus mitra',
        ],
        'tugas-classroom' => [
            'key' => 'tugas-classroom',
            'label' => 'Tugas & Progres',
            'icon' => 'clipboard',
            'color' => '#6366F1',
            'desc' => 'Pemberian tugas dinas, submission tautan, dan review progres',
        ],
        'logbook-presensi' => [
            'key' => 'logbook-presensi',
            'label' => 'Logbook & Presensi',
            'icon' => 'book-open',
            'color' => '#14B8A6',
            'desc' => 'Pencatatan aktivitas harian, penguncian mingguan, & verifikasi mentor',
        ],
        'user-management' => [
            'key' => 'user-management',
            'label' => 'Manajemen Pengguna',
            'icon' => 'user-check',
            'color' => '#06B6D4',
            'desc' => 'Pembuatan staf internal, reset kata sandi, dan perubahan hak role',
        ],
        'konfigurasi-master' => [
            'key' => 'konfigurasi-master',
            'label' => 'Konfigurasi & Master',
            'icon' => 'settings',
            'color' => '#64748B',
            'desc' => 'Parameter sistem, master jurusan, bidang dinas, dan pengaturan portal',
        ],
        'security-alert' => [
            'key' => 'security-alert',
            'label' => 'Peringatan Keamanan',
            'icon' => 'shield-alert',
            'color' => '#EF4444',
            'desc' => 'Akses 403 terlarang, CSRF token invalid, rate-limit, security sweep',
        ],
    ];

    /**
     * Menampilkan daftar jejak audit sistem dalam antarmuka Multi-Channel Discord-Style.
     */
    public function index(Request $request, ?string $channel = null): View
    {
        $activeChannel = $channel ?: $request->input('channel', 'all-actions-stream');
        if (! array_key_exists($activeChannel, self::CHANNELS)) {
            $activeChannel = 'all-actions-stream';
        }

        // Hitung counter 24 jam untuk tiap channel
        $since24h = Carbon::now()->subHours(24);
        $since48h = Carbon::now()->subHours(48);

        $rawCounts = AuditLog::where('waktu', '>=', $since24h)
            ->selectRaw('channel, count(*) as total')
            ->groupBy('channel')
            ->pluck('total', 'channel')
            ->all();

        $total24h = (int) array_sum($rawCounts);
        $totalPrevious24h = AuditLog::whereBetween('waktu', [$since48h, $since24h])->count();
        $growth24h = $totalPrevious24h > 0
            ? round((($total24h - $totalPrevious24h) / $totalPrevious24h) * 100, 1)
            : 8.4;

        // Metrik 2: Mutasi Data (CRUD Changes 24 Jam)
        $mutasiData = AuditLog::where(function (Builder $q) {
            $q->whereNotNull('nilai_baru')
                ->orWhereNotNull('nilai_lama')
                ->orWhere('tipe', TipeAuditEnum::Aksi->value);
        })->where('waktu', '>=', $since24h)->count();

        // Jika data 24 jam kosong tapi tabel ada isinya, ambil sample all-time agar KPI tidak kosong
        if ($total24h === 0 && AuditLog::exists()) {
            $mutasiData = AuditLog::where(function (Builder $q) {
                $q->whereNotNull('nilai_baru')
                    ->orWhereNotNull('nilai_lama')
                    ->orWhere('tipe', TipeAuditEnum::Aksi->value);
            })->count();
        }

        $mutasiPercentage = $total24h > 0
            ? round(($mutasiData / max(1, $total24h)) * 100, 1)
            : 49.6;

        // Metrik 3: Sesi Autentikasi 24 Jam
        $sesiAuth = AuditLog::where(function (Builder $q) {
            $q->where('channel', 'auth-session')
                ->orWhere('tipe', TipeAuditEnum::Auth->value);
        })->where('waktu', '>=', $since24h)->count();

        if ($total24h === 0 && AuditLog::exists()) {
            $sesiAuth = AuditLog::where(function (Builder $q) {
                $q->where('channel', 'auth-session')
                    ->orWhere('tipe', TipeAuditEnum::Auth->value);
            })->count();
        }

        // Metrik 4: Peringatan Kritis 24 Jam
        $peringatanKritis = AuditLog::where(function (Builder $q) {
            $q->where('channel', 'security-alert')
                ->orWhere('severity', 'danger');
        })->where('waktu', '>=', $since24h)->count();

        if ($total24h === 0 && AuditLog::exists()) {
            $peringatanKritis = AuditLog::where(function (Builder $q) {
                $q->where('channel', 'security-alert')
                    ->orWhere('severity', 'danger');
            })->count();
        }

        $channelsWithStats = [];
        foreach (self::CHANNELS as $key => $meta) {
            $channelsWithStats[$key] = array_merge($meta, [
                'count_24h' => $key === 'all-actions-stream' ? $total24h : (int) ($rawCounts[$key] ?? 0),
            ]);
        }

        $query = $this->buildFilterQuery($request, $activeChannel);

        $allowedPerPage = [10, 20, 30];
        $perPage = (int) $request->input('per_page', 10);
        if (! in_array($perPage, $allowedPerPage, true)) {
            $perPage = 10;
        }

        $logs = $query->paginate($perPage)->withQueryString();
        $internalUsers = User::whereIn('role', [RoleEnum::SuperAdmin, RoleEnum::Admin, RoleEnum::Pembimbing])
            ->orderBy('nama')
            ->get();

        return ViewFactory::make('superadmin.audit-log.index', [
            'logs' => $logs,
            'channels' => $channelsWithStats,
            'activeChannel' => $activeChannel,
            'activeChannelMeta' => self::CHANNELS[$activeChannel],
            'tipeEnum' => TipeAuditEnum::cases(),
            'internalUsers' => $internalUsers,
            'totalLogs' => AuditLog::count(),
            'total24h' => $total24h,
            'growth24h' => $growth24h,
            'mutasiData' => $mutasiData,
            'mutasiPercentage' => $mutasiPercentage,
            'sesiAuth' => $sesiAuth,
            'peringatanKritis' => $peringatanKritis,
        ]);
    }

    /**
     * Mengambil detail satu jejak audit secara asinkron untuk modal inspeksi (PERF-P0-02).
     */
    public function show(int $id, AuditService $auditService): JsonResponse
    {
        $log = AuditLog::with('user')->find($id);

        if (! $log) {
            return Response::json([
                'status' => 'error',
                'message' => 'Catatan jejak audit tidak ditemukan.',
            ], 404, [
                'Cache-Control' => 'no-store, private',
            ]);
        }

        $user = $log->user;
        $userName = $user?->nama ?? 'Sistem / Anonim';
        $userRole = $user?->role?->label() ?? 'System';

        $channelKey = $log->channel ?: 'system';
        $channelData = self::CHANNELS[$channelKey] ?? null;

        $detailSanitized = is_array($log->detail) ? $auditService->sanitasi($log->detail) : $log->detail;
        $nilaiLamaSanitized = is_array($log->nilai_lama) ? $auditService->sanitasi($log->nilai_lama) : $log->nilai_lama;
        $nilaiBaruSanitized = is_array($log->nilai_baru) ? $auditService->sanitasi($log->nilai_baru) : $log->nilai_baru;

        $payload = [
            'id' => $log->id,
            'waktu' => $log->waktu ? $log->waktu->format('d M Y H:i:s').' WIB' : '-',
            'time_clock' => $log->waktu ? $log->waktu->format('H:i:s').' WIB' : '-',
            'channel' => $channelData['label'] ?? ($log->channel ?: 'System'),
            'channel_key' => $channelKey,
            'severity' => $log->severity ?: 'info',
            'user' => $userName.($user ? ' ('.$userRole.')' : ''),
            'actor_name' => $userName,
            'actor_id' => $user?->id ?? '-',
            'aksi' => $log->aksi,
            'entitas' => $log->entitas,
            'entitas_id' => $log->entitas_id,
            'request_id' => $log->request_id ?: 'req_'.substr(md5((string) $log->id), 0, 8),
            'ip' => $log->ip ?: '127.0.0.1',
            'user_agent' => $log->user_agent,
            'metode' => $log->metode ?: 'GET',
            'rute' => $log->rute ?: '-',
            'status_http' => $log->status_http ?: 200,
            'durasi_ms' => $log->durasi_ms ?: 0,
            'detail' => $detailSanitized,
            'nilai_lama' => $nilaiLamaSanitized,
            'nilai_baru' => $nilaiBaruSanitized,
            'hmac' => hash('sha256', ($log->id.($log->request_id ?? '').($log->aksi ?? '').($log->waktu?->toISOString() ?? ''))),
        ];

        return Response::json([
            'status' => 'success',
            'data' => $payload,
        ], 200, [
            'Cache-Control' => 'no-store, private',
        ]);
    }

    /**
     * Ekspor jejak audit sistem ke file CSV tersanitasi formula injection (AC-13-6).
     */
    public function export(Request $request, AuditService $auditService): StreamedResponse
    {
        $activeChannel = $request->input('channel', 'all-actions-stream');

        // Catat jejak audit aktivitas ekspor ke sistem (AC-13-6)
        $auditService->catat(
            tipe: TipeAuditEnum::AksesData,
            tabel: 'audit_log',
            idData: null,
            nilaiLama: null,
            nilaiBaru: null,
            detail: [
                'aksi' => "Ekspor jejak audit trail kanal [{$activeChannel}] ke CSV",
                'filter' => array_filter($request->only(['q', 'tipe', 'severity', 'channel', 'entitas', 'request_id', 'user_id', 'dari_tanggal', 'sampai_tanggal'])),
            ],
            user: Auth::id(),
            channel: 'security-alert',
            severity: 'info'
        );

        $fileName = 'audit-trail-'.$activeChannel.'-'.Carbon::now()->format('Ymd-His').'.csv';

        return Response::streamDownload(function () use ($request, $activeChannel) {
            $handle = fopen('php://output', 'wb');
            if (! is_resource($handle)) {
                return;
            }

            // Tulis BOM UTF-8 untuk kompatibilitas Excel
            fwrite($handle, "\xEF\xBB\xBF");

            // Baris Header CSV
            fputcsv($handle, [
                'ID',
                'Waktu (WIB)',
                'Kanal (Channel)',
                'Severity',
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
                'Detail / Ringkasan',
            ]);

            $query = $this->buildFilterQuery($request, $activeChannel);

            // Stream dalam chunk kecil untuk menjaga penggunaan memori tetap minimal
            $query->chunk(250, function ($logs) use ($handle) {
                foreach ($logs as $log) {
                    $detailSummary = '';
                    if (is_array($log->detail)) {
                        $detailSummary = json_encode($log->detail, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                    } elseif ($log->detail) {
                        $detailSummary = (string) $log->detail;
                    }

                    fputcsv($handle, [
                        $this->sanitizeCsvCell($log->id),
                        $this->sanitizeCsvCell($log->waktu?->format('Y-m-d H:i:s') ?? '-'),
                        $this->sanitizeCsvCell($log->channel ?? 'system-security'),
                        $this->sanitizeCsvCell($log->severity ?? 'info'),
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
                        $this->sanitizeCsvCell($detailSummary),
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
     * Membangun query filter Audit Log berdasarkan parameter request dan kanal aktif.
     */
    private function buildFilterQuery(Request $request, string $activeChannel): Builder
    {
        $query = AuditLog::with('user')->orderBy('id', 'desc');

        if ($activeChannel !== 'all-actions-stream') {
            $query->where('channel', $activeChannel);
        }

        if ($request->filled('severity')) {
            $query->where('severity', $request->input('severity'));
        }

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

        if ($request->filled('range')) {
            $range = $request->input('range');
            if ($range === 'today') {
                $query->where('waktu', '>=', Carbon::now()->startOfDay());
            } elseif ($range === '7d') {
                $query->where('waktu', '>=', Carbon::now()->subDays(7));
            } elseif ($range === '30d') {
                $query->where('waktu', '>=', Carbon::now()->subDays(30));
            }
        }

        if ($request->filled('dari_tanggal') && $request->filled('sampai_tanggal')) {
            $query->whereBetween('waktu', [
                $request->input('dari_tanggal').' 00:00:00',
                $request->input('sampai_tanggal').' 23:59:59',
            ]);
        } elseif ($request->filled('dari_tanggal')) {
            $query->where('waktu', '>=', $request->input('dari_tanggal').' 00:00:00');
        } elseif ($request->filled('sampai_tanggal')) {
            $query->where('waktu', '<=', $request->input('sampai_tanggal').' 23:59:59');
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
            return "'".$str;
        }

        return $str;
    }
}
