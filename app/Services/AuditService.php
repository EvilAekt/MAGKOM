<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\TipeAuditEnum;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class AuditService
{
    /**
     * Kunci sensitif yang wajib disensor (masking) otomatis (AC-13-5, NFR-A3).
     */
    private const KUNCI_RAHASIA = [
        'password',
        'password_confirmation',
        'token',
        'remember_token',
        'captcha',
        'cf-turnstile-response',
        'g-recaptcha-response',
        'secret',
        'api_key',
        'app_key',
    ];

    /**
     * Data penanda pembacaan akses data untuk request saat ini.
     *
     * @var array{entitas: string, filter: array<string, mixed>, jumlah: int}|null
     */
    private ?array $dataAksesPending = null;

    /**
     * Catat peristiwa autentikasi (tipe: auth).
     */
    public function auth(User|int|null $user, string $aksi, ?array $detail = null): AuditLog
    {
        $userId = $user instanceof User ? $user->id : $user;

        return $this->simpan([
            'user_id' => $userId,
            'tipe' => TipeAuditEnum::Auth,
            'aksi' => $aksi,
            'detail' => $detail ? $this->sanitasi($detail) : null,
        ]);
    }

    /**
     * Catat mutasi bisnis (tipe: aksi).
     * Wajib dipanggil di dalam transaksi database aksi bisnis terkait (AC-13-7).
     */
    public function aksi(
        User|int|null $user,
        string $aksi,
        ?Model $entitas = null,
        ?array $nilaiLama = null,
        ?array $nilaiBaru = null
    ): AuditLog {
        $userId = $user instanceof User ? $user->id : $user;

        return $this->simpan([
            'user_id' => $userId,
            'tipe' => TipeAuditEnum::Aksi,
            'aksi' => $aksi,
            'entitas' => $entitas ? class_basename($entitas) : null,
            'entitas_id' => $entitas ? $entitas->getKey() : null,
            'nilai_lama' => $nilaiLama ? $this->sanitasi($nilaiLama) : null,
            'nilai_baru' => $nilaiBaru ? $this->sanitasi($nilaiBaru) : null,
        ]);
    }

    /**
     * Helper umum pencatatan audit log mutasi/aksi.
     */
    public function catat(
        TipeAuditEnum $tipe,
        string $tabel,
        int|string|null $idData = null,
        ?array $nilaiLama = null,
        ?array $nilaiBaru = null,
        ?array $detail = null,
        User|int|null $user = null
    ): AuditLog {
        $userId = $user instanceof User ? $user->id : ($user ?? Auth::id());

        return $this->simpan([
            'user_id' => $userId,
            'tipe' => $tipe,
            'aksi' => $detail['aksi'] ?? "mutasi.{$tabel}",
            'entitas' => $tabel,
            'entitas_id' => $idData,
            'nilai_lama' => $nilaiLama ? $this->sanitasi($nilaiLama) : null,
            'nilai_baru' => $nilaiBaru ? $this->sanitasi($nilaiBaru) : null,
            'detail' => $detail ? $this->sanitasi($detail) : null,
        ]);
    }

    /**
     * Catat pemanggilan method Service (tipe: service).
     */
    public function service(
        string $serviceClass,
        string $metode,
        array $argumen,
        string $status,
        int $durasiMs
    ): AuditLog {
        $userId = Auth::id();

        return $this->simpan([
            'user_id' => $userId,
            'tipe' => TipeAuditEnum::Service,
            'aksi' => class_basename($serviceClass) . '@' . $metode,
            'detail' => [
                'service' => $serviceClass,
                'metode' => $metode,
                'argumen' => $this->sanitasi($argumen),
                'status' => $status,
            ],
            'durasi_ms' => $durasiMs,
        ]);
    }

    /**
     * Tandai bahwa request saat ini mengakses pembacaan data sensitif.
     */
    public function tandaiAkses(string $entitas, array $filter = [], int $jumlah = 0): void
    {
        $this->dataAksesPending = [
            'entitas' => $entitas,
            'filter' => $this->sanitasi($filter),
            'jumlah' => $jumlah,
        ];
    }

    /**
     * Menulis log akses data (tipe: akses_data) via terminable middleware.
     */
    public function tulisAksesData(Request $request, Response $response): ?AuditLog
    {
        if ($this->dataAksesPending === null) {
            return null;
        }

        $userId = $request->user()?->id;

        $log = $this->simpan([
            'user_id' => $userId,
            'tipe' => TipeAuditEnum::AksesData,
            'aksi' => 'akses.' . $this->dataAksesPending['entitas'],
            'entitas' => $this->dataAksesPending['entitas'],
            'detail' => [
                'filter' => $this->dataAksesPending['filter'],
                'jumlah_baris' => $this->dataAksesPending['jumlah'],
            ],
            'status_http' => $response->getStatusCode(),
        ]);

        $this->dataAksesPending = null;

        return $log;
    }

    /**
     * Catat aksi otomatis yang dilakukan oleh sistem / scheduler cron.
     */
    public function sistem(string $aksi, ?Model $entitas = null, ?array $detail = null): AuditLog
    {
        return $this->simpan([
            'user_id' => null,
            'tipe' => TipeAuditEnum::Aksi,
            'aksi' => 'sistem.' . $aksi,
            'entitas' => $entitas ? class_basename($entitas) : null,
            'entitas_id' => $entitas ? $entitas->getKey() : null,
            'detail' => $detail ? $this->sanitasi($detail) : null,
        ]);
    }

    /**
     * Sanitasi rekursif untuk menyamarkan kata sandi dan token rahasia (AC-13-5).
     */
    public function sanitasi(mixed $nilai): mixed
    {
        return match (true) {
            $nilai instanceof Model => [
                'model' => class_basename($nilai),
                'id' => $nilai->getKey(),
            ],
            $nilai instanceof UploadedFile => [
                'nama_file' => $nilai->getClientOriginalName(),
                'ukuran' => $nilai->getSize(),
                'mime' => $nilai->getClientMimeType(),
            ],
            is_array($nilai) => collect($nilai)->map(function ($val, $key) {
                if (in_array(strtolower((string) $key), self::KUNCI_RAHASIA, true)) {
                    return '[DISAMARKAN]';
                }
                return $this->sanitasi($val);
            })->all(),
            is_scalar($nilai) || $nilai === null => $nilai,
            default => get_debug_type($nilai),
        };
    }

    /**
     * Eksekusi insert log ke database.
     *
     * @param array<string, mixed> $data
     */
    private function simpan(array $data): AuditLog
    {
        $request = request();

        $requestId = $request instanceof Request && $request->attributes->has('request_id')
            ? (string) $request->attributes->get('request_id')
            : (string) Str::uuid();

        $ip = $request instanceof Request ? (string) $request->ip() : '127.0.0.1';
        $metode = $request instanceof Request ? $request->method() : 'CLI';
        $rute = $request instanceof Request ? $request->path() : 'console';
        $userAgent = $request instanceof Request ? substr((string) $request->userAgent(), 0, 255) : 'Artisan CLI';

        return AuditLog::create(array_merge([
            'request_id' => $requestId,
            'metode' => $metode,
            'rute' => $rute,
            'ip' => $ip,
            'user_agent' => $userAgent,
            'waktu' => now('Asia/Jakarta'),
        ], $data));
    }
}
