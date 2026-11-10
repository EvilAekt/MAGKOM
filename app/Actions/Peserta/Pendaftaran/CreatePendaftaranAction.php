<?php

declare(strict_types=1);

namespace App\Actions\Peserta\Pendaftaran;

use App\Enums\StatusPendaftaranEnum;
use App\Enums\TipeAuditEnum;
use App\Exceptions\Domain\PendaftaranAktifGandaException;
use App\Models\Pendaftaran;
use App\Models\User;
use App\Services\AuditService;
use App\Services\QuotaAllocationService;
use Illuminate\Support\Facades\DB;
use Throwable;

class CreatePendaftaranAction
{
    public function __construct(
        protected AuditService $auditService,
        protected QuotaAllocationService $quotaService
    ) {}

    /**
     * Membuat draf pendaftaran magang baru dengan perlindungan pessimistic lock pada baris pengguna (BR-17).
     *
     * @param  array<string, mixed>  $validatedData
     *
     * @throws PendaftaranAktifGandaException|Throwable
     */
    public function execute(User $user, array $validatedData): Pendaftaran
    {
        return DB::transaction(function () use ($user, $validatedData) {
            // 1. Parent-row locking pada user peserta untuk mencegah race condition (BR-17)
            /** @var User $lockedUser */
            $lockedUser = User::whereKey($user->id)->lockForUpdate()->firstOrFail();

            // 2. Verifikasi ulang ketiadaan pendaftaran aktif di dalam transaksi terkunci
            $this->quotaService->pastikanTidakAdaPendaftaranAktif($lockedUser->id);

            // 3. Tentukan tipe pendaftaran (mandiri vs kelompok)
            $payload = $validatedData;
            $payload['user_id'] = $lockedUser->id;
            $payload['status'] = StatusPendaftaranEnum::BerkasBelumLengkap;

            if ($lockedUser->kelompokMagang) {
                $payload['tipe'] = 'kelompok';
                $payload['kelompok_magang_id'] = $lockedUser->kelompokMagang->id;
            } else {
                $payload['tipe'] = 'mandiri';
                $payload['kelompok_magang_id'] = null;
            }

            // 4. Simpan pendaftaran baru
            $pendaftaran = Pendaftaran::create($payload);

            // 5. Catat jejak audit
            $this->auditService->catat(
                tipe: TipeAuditEnum::Aksi,
                tabel: 'pendaftaran',
                idData: $pendaftaran->id,
                nilaiBaru: $pendaftaran->toArray(),
                detail: ['aksi' => 'Membuat draf pengajuan pendaftaran magang']
            );

            return $pendaftaran;
        });
    }
}
