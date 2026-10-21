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
     * Definisi 15 Kanal Audit Log ala Discord Stream (Master Plan Spec 2.1).
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
        'penilaian-sk' => [
            'key' => 'penilaian-sk',
            'label' => 'Penilaian & Sertifikat',
            'icon' => 'award',
            'color' => '#EC4899',
            'desc' => 'Input nilai rubrik, konversi predikat, penerbitan sertifikat digital',
        ],
        'survei-evaluasi' => [
            'key' => 'survei-evaluasi',
            'label' => 'Survei & Mutu Alumni',
            'icon' => 'smile',
            'color' => '#84CC16',
            'desc' => 'Kuesioner kepuasan, evaluasi fasilitas, dan kritik saran alumni',
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
            'desc' => 'Parameter sistem, master jurusan, bidang dinas, dan rubrik penilaian',
        ],
        'backup-database' => [
            'key' => 'backup-database',
            'label' => 'Cadangan Basis Data',
            'icon' => 'database',
            'color' => '#F97316',
            'desc' => 'Pemicuan snapshot backup SQL, download arsip, & disaster recovery',
        ],
        'security-alert' => [
            'key' => 'security-alert',
            'label' => 'Peringatan Keamanan',
            'icon' => 'shield-alert',
            'color' => '#EF4444',
            'desc' => 'Akses 403 terlarang, CSRF token invalid, rate-limit, security sweep',
        ],
        'dynamic-module' => [
            'key' => 'dynamic-module',
            'label' => 'Modul Kustom Dinamis',
            'icon' => 'layers',
            'color' => '#A855F7',
            'desc' => 'Pembuatan modul baru dan mutasi data baris pada modul kustom',
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
        $since24h = now()->subHours(24);
        $rawCounts = AuditLog::where('waktu', '>=', $since24h)
            ->selectRaw('channel, count(*) as total')
            ->groupBy('channel')
            ->pluck('total', 'channel')
            ->all();

        $total24h = (int) array_sum($rawCounts);

        $channelsWithStats = [];
        foreach (self::CHANNELS as $key => $meta) {
            $channelsWithStats[$key] = array_merge($meta, [
                'count_24h' => $key === 'all-actions-stream' ? $total24h : (int) ($rawCounts[$key] ?? 0),
            ]);
        }

        $query = $this->buildFilterQuery($request, $activeChannel);

        $logs = $query->paginate(30)->withQueryString();
        $internalUsers = User::whereIn('role', [RoleEnum::SuperAdmin, RoleEnum::Admin, RoleEnum::Pembimbing])
            ->orderBy('nama')
            ->get();

        return view('superadmin.audit-log.index', [
            'logs' => $logs,
            'channels' => $channelsWithStats,
            'activeChannel' => $activeChannel,
            'activeChannelMeta' => self::CHANNELS[$activeChannel],
            'tipeEnum' => TipeAuditEnum::cases(),
            'internalUsers' => $internalUsers,
            'totalLogs' => AuditLog::count(),
            'total24h' => $total24h,
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

        $fileName = 'audit-trail-'.$activeChannel.'-'.now()->format('Ymd-His').'.csv';

        return response()->streamDownload(function () use ($request, $activeChannel) {
            $handle = fopen('php://output', 'wb');

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
