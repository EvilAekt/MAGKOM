<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Enums\TipeAuditEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\SlotKhususRequest;
use App\Models\SlotKhusus;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SlotKhususController extends Controller
{
    public function __construct(
        protected AuditService $auditService
    ) {}

    public function index(): View
    {
        $slotList = SlotKhusus::with('creator')
            ->orderBy('bulan_mulai', 'desc')
            ->paginate(12);

        return view('superadmin.slot-khusus.index', [
            'slotList' => $slotList,
        ]);
    }

    public function store(SlotKhususRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['dibuat_oleh'] = $request->user()->id;

        $slot = SlotKhusus::create($validated);

        $this->auditService->catat(
            tipe: TipeAuditEnum::Aksi,
            tabel: 'slot_khusus',
            idData: $slot->id,
            nilaiBaru: $slot->toArray(),
            detail: ['aksi' => 'Membuat alokasi slot khusus internal baru']
        );

        return redirect()->route('superadmin.slot-khusus.index')
            ->with('success', 'Slot khusus internal berhasil dibuat.');
    }

    public function update(SlotKhususRequest $request, SlotKhusus $slotKhusus): RedirectResponse
    {
        $nilaiLama = $slotKhusus->toArray();
        $validated = $request->validated();

        $slotKhusus->update($validated);

        $this->auditService->catat(
            tipe: TipeAuditEnum::Aksi,
            tabel: 'slot_khusus',
            idData: $slotKhusus->id,
            nilaiLama: $nilaiLama,
            nilaiBaru: $slotKhusus->toArray(),
            detail: ['aksi' => 'Memperbarui alokasi slot khusus internal']
        );

        return redirect()->route('superadmin.slot-khusus.index')
            ->with('success', 'Slot khusus internal berhasil diperbarui.');
    }

    public function toggleAktif(Request $request, SlotKhusus $slotKhusus): RedirectResponse
    {
        $nilaiLama = ['aktif' => $slotKhusus->aktif];
        $slotKhusus->aktif = ! $slotKhusus->aktif;
        $slotKhusus->save();

        $this->auditService->catat(
            tipe: TipeAuditEnum::Aksi,
            tabel: 'slot_khusus',
            idData: $slotKhusus->id,
            nilaiLama: $nilaiLama,
            nilaiBaru: ['aktif' => $slotKhusus->aktif],
            detail: ['aksi' => 'Mengubah status aktif slot khusus']
        );

        $statusText = $slotKhusus->aktif ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('superadmin.slot-khusus.index')
            ->with('success', "Slot khusus berhasil {$statusText}.");
    }
}
