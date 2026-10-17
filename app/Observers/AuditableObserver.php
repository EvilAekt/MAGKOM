<?php

declare(strict_types=1);

namespace App\Observers;

use App\Enums\TipeAuditEnum;
use App\Models\ModulDinamisData;
use App\Services\AuditService;
use Illuminate\Database\Eloquent\Model;

class AuditableObserver
{
    public function __construct(protected AuditService $auditService) {}

    public function created(Model $model): void
    {
        if ($model->getTable() === 'audit_log' || $model instanceof ModulDinamisData) {
            return;
        }

        $this->auditService->catat(
            tipe: TipeAuditEnum::Aksi,
            tabel: $model->getTable(),
            idData: (int) $model->getKey(),
            nilaiLama: null,
            nilaiBaru: $model->getAttributes(),
            detail: ['aksi' => "Penambahan data {$model->getTable()} #{$model->getKey()}"]
        );
    }

    public function updated(Model $model): void
    {
        if ($model->getTable() === 'audit_log' || $model instanceof ModulDinamisData) {
            return;
        }

        $changes = $model->getChanges();
        $original = array_intersect_key($model->getOriginal(), $changes);

        $this->auditService->catat(
            tipe: TipeAuditEnum::Aksi,
            tabel: $model->getTable(),
            idData: (int) $model->getKey(),
            nilaiLama: $original,
            nilaiBaru: $changes,
            detail: ['aksi' => "Pembaruan data {$model->getTable()} #{$model->getKey()}"]
        );
    }

    public function deleted(Model $model): void
    {
        if ($model->getTable() === 'audit_log' || $model instanceof ModulDinamisData) {
            return;
        }

        $this->auditService->catat(
            tipe: TipeAuditEnum::Aksi,
            tabel: $model->getTable(),
            idData: (int) $model->getKey(),
            nilaiLama: $model->getOriginal(),
            nilaiBaru: null,
            detail: ['aksi' => "Penghapusan data {$model->getTable()} #{$model->getKey()}"],
            severity: 'warning'
        );
    }
}
