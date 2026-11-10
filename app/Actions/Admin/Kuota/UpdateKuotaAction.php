<?php

declare(strict_types=1);

namespace App\Actions\Admin\Kuota;

use App\Enums\TipeAuditEnum;
use App\Models\Kuota;
use App\Services\AuditService;
use Illuminate\Support\Facades\DB;

class UpdateKuotaAction
{
    public function __construct(
        protected AuditService $auditService
    ) {}

    /**
     * Memperbarui alokasi kuota jurusan secara transaksional dengan row-level lock dan audit trail.
     *
     * @param  array<string, mixed>  $validatedData
     */
    public function execute(Kuota $kuota, array $validatedData): Kuota
    {
        return DB::transaction(function () use ($kuota, $validatedData) {
            /** @var Kuota $locked */
            $locked = Kuota::whereKey($kuota->id)->lockForUpdate()->firstOrFail();
            $nilaiLama = $locked->toArray();

            $locked->update($validatedData);

            $this->auditService->catat(
                tipe: TipeAuditEnum::Aksi,
                tabel: 'kuota',
                idData: $locked->id,
                nilaiLama: $nilaiLama,
                nilaiBaru: $locked->toArray(),
                detail: ['aksi' => 'Memperbarui alokasi kuota jurusan']
            );

            return $locked;
        });
    }
}

