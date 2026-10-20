<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Enums\TipeAuditEnum;
use App\Http\Controllers\Controller;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\Pengaturan;
use App\Models\Rubrik;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\View\View;

class PengaturanController extends Controller
{
    public function __construct(
        protected AuditService $auditService
    ) {}

    /**
     * Menampilkan daftar konfigurasi sistem (AC-11-7, PRD 8.6).
     */
    public function index(): View
    {
        $pengaturan = Pengaturan::all()->pluck('nilai', 'kunci');

        $predikatBatasRaw = $pengaturan['predikat_batas'] ?? null;
        $predikatBatas = $predikatBatasRaw ? json_decode($predikatBatasRaw, true) : null;
        if (! is_array($predikatBatas)) {
            $predikatBatas = [
                'A' => 85,
                'B' => 70,
                'C' => 55,
                'D' => 40,
            ];
        }

        return view('superadmin.pengaturan.index', [
            'pengaturan' => $pengaturan,
            'predikatBatas' => $predikatBatas,
        ]);
    }

    /**
     * Memperbarui parameter pengaturan sistem (AC-11-7, PRD 8.6).
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'masa_tenggang_bulan' => ['required', 'integer', 'min:1', 'max:12'],
            'kuota_umum_maks_per_bulan' => ['required', 'integer', 'min:1', 'max:100'],
            'slot_khusus_default' => ['nullable', 'integer', 'min:0', 'max:50'],
            'maks_peserta_aktif_per_pembimbing' => ['nullable', 'integer', 'min:1', 'max:50'],
            'logbook_maks_mundur_hari' => ['required', 'integer', 'min:1', 'max:30'],
            'berkas_maks_mb' => ['required', 'integer', 'min:1', 'max:10'],
            'deadline_mendekati_hari' => ['required', 'integer', 'min:1', 'max:14'],
            'retensi_audit_akses_data_hari' => ['nullable', 'integer', 'min:30', 'max:1825'],
            'batas_predikat_a' => ['nullable', 'integer', 'min:1', 'max:100'],
            'batas_predikat_b' => ['nullable', 'integer', 'min:1', 'max:100'],
            'batas_predikat_c' => ['nullable', 'integer', 'min:1', 'max:100'],
            'batas_predikat_d' => ['nullable', 'integer', 'min:1', 'max:100'],
            'predikat_batas' => ['nullable'],
            'fitur_sertifikat_aktif' => ['nullable', 'boolean'],
            'fitur_survei_aktif' => ['nullable', 'boolean'],
            'fitur_analitik_aktif' => ['nullable', 'boolean'],
        ]);

        $settingsToSave = [
            'masa_tenggang_bulan' => (string) $validated['masa_tenggang_bulan'],
            'kuota_umum_maks_per_bulan' => (string) $validated['kuota_umum_maks_per_bulan'],
            'maks_peserta_aktif_per_pembimbing' => ! empty($validated['maks_peserta_aktif_per_pembimbing']) ? (string) $validated['maks_peserta_aktif_per_pembimbing'] : null,
            'logbook_maks_mundur_hari' => (string) $validated['logbook_maks_mundur_hari'],
            'berkas_maks_mb' => (string) $validated['berkas_maks_mb'],
            'deadline_mendekati_hari' => (string) $validated['deadline_mendekati_hari'],
            'retensi_audit_akses_data_hari' => (string) ($validated['retensi_audit_akses_data_hari'] ?? '365'),
            'fitur_sertifikat_aktif' => $request->boolean('fitur_sertifikat_aktif') ? '1' : '0',
            'fitur_survei_aktif' => $request->boolean('fitur_survei_aktif') ? '1' : '0',
            'fitur_analitik_aktif' => $request->boolean('fitur_analitik_aktif') ? '1' : '0',
        ];

        if (isset($validated['slot_khusus_default'])) {
            $settingsToSave['slot_khusus_default'] = (string) $validated['slot_khusus_default'];
        }

        $anyPredikat = isset($validated['batas_predikat_a']) || isset($validated['batas_predikat_b']) || isset($validated['batas_predikat_c']) || isset($validated['batas_predikat_d']);
        if ($anyPredikat) {
            if (! isset($validated['batas_predikat_a'], $validated['batas_predikat_b'], $validated['batas_predikat_c'], $validated['batas_predikat_d'])) {
                return back()->withInput()->withErrors([
                    'batas_predikat_a' => 'Seluruh batas ambang predikat (A, B, C, D) wajib diisi lengkap.',
                ]);
            }

            $limA = (int) $validated['batas_predikat_a'];
            $limB = (int) $validated['batas_predikat_b'];
            $limC = (int) $validated['batas_predikat_c'];
            $limD = (int) $validated['batas_predikat_d'];

            if (! ($limA > $limB && $limB > $limC && $limC > $limD)) {
                return back()->withInput()->withErrors([
                    'batas_predikat_a' => 'Skala batas ambang predikat harus berurutan secara logis: A > B > C > D.',
                ]);
            }

            $settingsToSave['predikat_batas'] = json_encode([
                'A' => $limA,
                'B' => $limB,
                'C' => $limC,
                'D' => $limD,
            ]);
        } elseif ($request->filled('predikat_batas')) {
            $raw = $request->input('predikat_batas');
            $decoded = is_array($raw) ? $raw : json_decode((string) $raw, true);

            if (! is_array($decoded) || ! isset($decoded['A'], $decoded['B'], $decoded['C'], $decoded['D'])) {
                return back()->withInput()->withErrors([
                    'predikat_batas' => 'Format predikat batas harus berupa JSON valid dengan kunci A, B, C, dan D.',
                ]);
            }

            $pA = (int) $decoded['A'];
            $pB = (int) $decoded['B'];
            $pC = (int) $decoded['C'];
            $pD = (int) $decoded['D'];

            if (! ($pA > $pB && $pB > $pC && $pC > $pD)) {
                return back()->withInput()->withErrors([
                    'predikat_batas' => 'Skala batas ambang predikat harus berurutan secara logis: A > B > C > D.',
                ]);
            }

            $settingsToSave['predikat_batas'] = json_encode([
                'A' => $pA,
                'B' => $pB,
                'C' => $pC,
                'D' => $pD,
            ]);
        }

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
                'aksi' => 'Memperbarui parameter konfigurasi sistem (AC-11-7)',
                'superadmin_id' => $request->user()->id,
            ]
        );

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
        $rubrik = Rubrik::all(['id', 'kriteria', 'bobot', 'urutan', 'aktif'])->toArray();
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
                'rubrik' => $rubrik,
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
