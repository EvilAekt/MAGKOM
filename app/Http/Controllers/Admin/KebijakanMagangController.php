<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\TipeAuditEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateKebijakanMagangRequest;
use App\Models\Pengaturan;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class KebijakanMagangController extends Controller
{
    public function __construct(
        protected AuditService $auditService
    ) {}

    /**
     * Menampilkan halaman konfigurasi kebijakan kuota, pendaftaran, dan kapasitas magang.
     */
    public function index(): View
    {
        $kunciKebijakan = [
            'kuota_umum_maks_per_bulan',
            'slot_khusus_default',
            'maks_peserta_aktif_per_pembimbing',
            'maks_anggota_kelompok',
            'durasi_magang_maks_bulan',
            'sla_verifikasi_hari',
        ];

        $pengaturan = Pengaturan::whereIn('kunci', $kunciKebijakan)->pluck('nilai', 'kunci');

        return view('admin.kebijakan.index', [
            'pengaturan' => $pengaturan,
            'kuotaUmumMaksPerBulan' => (int) ($pengaturan['kuota_umum_maks_per_bulan'] ?? 12),
            'slotKhususDefault' => (int) ($pengaturan['slot_khusus_default'] ?? 3),
            'maksPesertaAktifPerPembimbing' => (int) ($pengaturan['maks_peserta_aktif_per_pembimbing'] ?? 20),
            'maksAnggotaKelompok' => (int) ($pengaturan['maks_anggota_kelompok'] ?? 4),
            'durasiMagangMaksBulan' => (int) ($pengaturan['durasi_magang_maks_bulan'] ?? 6),
            'slaVerifikasiHari' => (int) ($pengaturan['sla_verifikasi_hari'] ?? 5),
        ]);
    }

    /**
     * Memperbarui parameter kebijakan kuota dan kapasitas magang oleh Admin.
     */
    public function update(UpdateKebijakanMagangRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $settingsToSave = [
            'kuota_umum_maks_per_bulan' => (string) $validated['kuota_umum_maks_per_bulan'],
            'slot_khusus_default' => (string) $validated['slot_khusus_default'],
            'maks_peserta_aktif_per_pembimbing' => ! empty($validated['maks_peserta_aktif_per_pembimbing']) ? (string) $validated['maks_peserta_aktif_per_pembimbing'] : '20',
            'maks_anggota_kelompok' => (string) $validated['maks_anggota_kelompok'],
            'durasi_magang_maks_bulan' => (string) $validated['durasi_magang_maks_bulan'],
            'sla_verifikasi_hari' => (string) $validated['sla_verifikasi_hari'],
        ];

        DB::transaction(function () use ($settingsToSave, $request) {
            $nilaiLama = [];
            $nilaiBaru = [];

            foreach ($settingsToSave as $kunci => $nilai) {
                $record = Pengaturan::firstOrNew(['kunci' => $kunci]);
                $nilaiLama[$kunci] = $record->nilai;

                $record->nilai = $nilai;
                $record->save();

                $nilaiBaru[$kunci] = $record->nilai;
            }

            Pengaturan::clearCache();

            $this->auditService->catat(
                tipe: TipeAuditEnum::Aksi,
                tabel: 'pengaturan',
                idData: 0,
                nilaiLama: $nilaiLama,
                nilaiBaru: $nilaiBaru,
                detail: [
                    'aksi' => 'Memperbarui kebijakan kuota dan kapasitas magang oleh Admin',
                    'admin_id' => $request->user()?->id,
                ],
                channel: 'settings',
                severity: 'info'
            );
        });

        return redirect()->route('admin.kebijakan.index')
            ->with('success', 'Kebijakan kuota, pendaftaran, dan kapasitas magang berhasil diperbarui.');
    }
}
