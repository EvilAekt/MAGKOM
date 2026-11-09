<?php

declare(strict_types=1);

namespace App\Actions\Pembimbing\Tugas;

use App\Enums\StatusTugasEnum;
use App\Enums\TipeAuditEnum;
use App\Models\Tugas;
use App\Services\AuditService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class MarkTugasCompletedAction
{
    public function __construct(
        protected AuditService $auditService
    ) {}

    /**
     * Menandai penugasan sebagai selesai oleh pembimbing (A-22).
     */
    public function execute(Tugas $tugas): Tugas
    {
        Gate::authorize('update', $tugas);

        $nilaiLama = ['status' => $tugas->status->value];

        DB::transaction(function () use ($tugas, $nilaiLama) {
            $tugas->update(['status' => StatusTugasEnum::Selesai]);

            $this->auditService->catat(
                tipe: TipeAuditEnum::Aksi,
                tabel: 'tugas',
                idData: $tugas->id,
                nilaiLama: $nilaiLama,
                nilaiBaru: ['status' => StatusTugasEnum::Selesai->value],
                detail: ['aksi' => 'selesaikan_tugas_oleh_pembimbing']
            );
        });

        return $tugas;
    }
}
