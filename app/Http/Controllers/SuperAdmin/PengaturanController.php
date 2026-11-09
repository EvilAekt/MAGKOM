<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Enums\TipeAuditEnum;
use App\Http\Controllers\Controller;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\Pengaturan;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PengaturanController extends Controller
{
    public function __construct(
        protected AuditService $auditService
    ) {}

    /**
     * Menampilkan daftar konfigurasi sistem.
     */
    public function index(): View
    {
        $kunciOperasional = [
            'kuota_umum_maks_per_bulan',
            'slot_khusus_default',
            'maks_peserta_aktif_per_pembimbing',
            'maks_anggota_kelompok',
            'durasi_magang_maks_bulan',
            'logbook_maks_mundur_hari',
            'berkas_maks_mb',
            'deadline_mendekati_hari',
            'sla_verifikasi_hari',
            'masa_tenggang_bulan',
            'retensi_audit_akses_data_hari',
            'fitur_analitik_aktif',
            'fitur_pendaftaran_kelompok_aktif',
        ];

        $pengaturan = Pengaturan::all()->pluck('nilai', 'kunci');

        $totalParameter = count($kunciOperasional);
        $kuotaBulanan = (int) ($pengaturan['kuota_umum_maks_per_bulan'] ?? 12);
        $rasioMentee = (int) ($pengaturan['maks_peserta_aktif_per_pembimbing'] ?? 20);
        $masaTenggang = (int) ($pengaturan['masa_tenggang_bulan'] ?? 2);

        return view('superadmin.pengaturan.index', [
            'pengaturan' => $pengaturan,
            'totalParameter' => $totalParameter,
            'kuotaBulanan' => $kuotaBulanan,
            'rasioMentee' => $rasioMentee,
            'masaTenggang' => $masaTenggang,
        ]);
    }

    /**
     * Memperbarui parameter pengaturan sistem.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kuota_umum_maks_per_bulan' => ['required', 'integer', 'min:1', 'max:100'],
            'slot_khusus_default' => ['nullable', 'integer', 'min:0', 'max:50'],
            'maks_peserta_aktif_per_pembimbing' => ['nullable', 'integer', 'min:1', 'max:50'],
            'maks_anggota_kelompok' => ['nullable', 'integer', 'min:1', 'max:10'],
            'durasi_magang_maks_bulan' => ['nullable', 'integer', 'min:1', 'max:12'],
            'logbook_maks_mundur_hari' => ['required', 'integer', 'min:1', 'max:30'],
            'berkas_maks_mb' => ['required', 'integer', 'min:1', 'max:20'],
            'deadline_mendekati_hari' => ['required', 'integer', 'min:1', 'max:14'],
            'sla_verifikasi_hari' => ['nullable', 'integer', 'min:1', 'max:30'],
            'masa_tenggang_bulan' => ['required', 'integer', 'min:1', 'max:12'],
            'retensi_audit_akses_data_hari' => ['nullable', 'integer', 'min:30', 'max:1825'],
            'fitur_analitik_aktif' => ['nullable', 'boolean'],
            'fitur_pendaftaran_kelompok_aktif' => ['nullable', 'boolean'],
        ]);

        $settingsToSave = [
            'kuota_umum_maks_per_bulan' => (string) $validated['kuota_umum_maks_per_bulan'],
            'maks_peserta_aktif_per_pembimbing' => ! empty($validated['maks_peserta_aktif_per_pembimbing']) ? (string) $validated['maks_peserta_aktif_per_pembimbing'] : '20',
            'logbook_maks_mundur_hari' => (string) $validated['logbook_maks_mundur_hari'],
            'berkas_maks_mb' => (string) $validated['berkas_maks_mb'],
            'deadline_mendekati_hari' => (string) $validated['deadline_mendekati_hari'],
            'masa_tenggang_bulan' => (string) $validated['masa_tenggang_bulan'],
            'retensi_audit_akses_data_hari' => (string) ($validated['retensi_audit_akses_data_hari'] ?? '365'),
            'fitur_analitik_aktif' => $request->boolean('fitur_analitik_aktif') ? '1' : '0',
        ];

        if (isset($validated['slot_khusus_default'])) {
            $settingsToSave['slot_khusus_default'] = (string) $validated['slot_khusus_default'];
        }

        if (isset($validated['maks_anggota_kelompok'])) {
            $settingsToSave['maks_anggota_kelompok'] = (string) $validated['maks_anggota_kelompok'];
        }

        if (isset($validated['durasi_magang_maks_bulan'])) {
            $settingsToSave['durasi_magang_maks_bulan'] = (string) $validated['durasi_magang_maks_bulan'];
        }

        if (isset($validated['sla_verifikasi_hari'])) {
            $settingsToSave['sla_verifikasi_hari'] = (string) $validated['sla_verifikasi_hari'];
        }

        if ($request->has('fitur_pendaftaran_kelompok_aktif')) {
            $settingsToSave['fitur_pendaftaran_kelompok_aktif'] = $request->boolean('fitur_pendaftaran_kelompok_aktif') ? '1' : '0';
        }

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
                    'aksi' => 'Memperbarui parameter konfigurasi sistem',
                    'superadmin_id' => $request->user()->id,
                ]
            );
        });

        return redirect()->route('superadmin.pengaturan.index')
            ->with('success', 'Konfigurasi sistem berhasil diperbarui.');
    }

    /**
     * Memicu pemangkasan log akses data secara langsung oleh SuperAdmin (NFR-A5).
     */
    public function triggerPurgeAudit(Request $request): RedirectResponse
    {
        Artisan::call('magkom:audit-purge');
        $output = Artisan::output();

        return redirect()->route('superadmin.pengaturan.index')
            ->with('success', 'Pemangkasan log akses data berhasil dijalankan: '.trim($output));
    }

    /**
     * Mengekspor snapshot konfigurasi master data dan sistem ke file JSON terstruktur (PRD F-11, Bab 10.2).
     */
    public function exportMasterSnapshotJson(Request $request)
    {
        $superadmin = $request->user();

        $pengaturan = Pengaturan::all()->pluck('nilai', 'kunci')->toArray();
        $jurusan = Jurusan::all(['id', 'nama_jurusan', 'kategori', 'aktif'])->toArray();
        $bidang = Bidang::all(['id', 'nama_bidang', 'deskripsi', 'aktif'])->toArray();
        $bulanMagang = BulanMagang::all(['id', 'bulan_mulai', 'tgl_buka', 'tgl_tutup', 'override_status', 'aktif'])->toArray();

        $payloadData = [
            'app' => 'MagKom Sleman',
            'version' => '1.1.0',
            'exported_at' => now()->toIso8601String(),
            'exported_by' => [
                'id' => $superadmin->id,
                'nama' => $superadmin->nama,
                'email' => $superadmin->email,
            ],
            'snapshot' => [
                'pengaturan' => $pengaturan,
                'jurusan' => $jurusan,
                'bidang' => $bidang,
                'bulan_magang' => $bulanMagang,
            ],
        ];

        $jsonString = json_encode($payloadData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        $checksum = hash('sha256', (string) $jsonString);
        $payloadData['sha256_checksum'] = $checksum;

        $finalJson = json_encode($payloadData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        $this->auditService->catat(
            tipe: TipeAuditEnum::AksesData,
            tabel: 'pengaturan',
            idData: null,
            nilaiLama: null,
            nilaiBaru: null,
            detail: [
                'aksi' => 'Ekspor snapshot master konfigurasi sistem (JSON)',
                'superadmin_id' => $superadmin->id,
                'checksum' => $checksum,
            ]
        );

        $fileName = 'magkom-master-snapshot-'.date('Ymd-His').'.json';

        return response($finalJson, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
