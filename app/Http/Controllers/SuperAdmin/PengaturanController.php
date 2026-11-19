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
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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
        ];

        $pengaturan = Pengaturan::all()->pluck('nilai', 'kunci');

        $totalParameter = count($kunciOperasional);
        $kuotaBulanan = (int) ($pengaturan['kuota_umum_maks_per_bulan'] ?? 12);
        $rasioMentee = (int) ($pengaturan['maks_peserta_aktif_per_pembimbing'] ?? 20);
        $masaTenggang = (int) ($pengaturan['masa_tenggang_bulan'] ?? 2);

        $heroImageSetting = $pengaturan['landing_hero_image'] ?? null;
        $hasCustomHeroImage = ! empty($heroImageSetting) && Storage::disk('public')->exists($heroImageSetting);
        $heroImageUrl = $hasCustomHeroImage
            ? asset('storage/'.$heroImageSetting)
            : asset('images/hero_dokumentasi_diskominfo.png');

        $panduanPdfSetting = $pengaturan['landing_panduan_pdf'] ?? null;
        $hasCustomPanduanPdf = ! empty($panduanPdfSetting) && Storage::disk('public')->exists($panduanPdfSetting);
        $panduanPdfName = $pengaturan['landing_panduan_pdf_name'] ?? 'Buku-Panduan-Pendaftaran-Magang-Diskominfo-Sleman.pdf';
        $panduanPdfSize = $hasCustomPanduanPdf ? round(Storage::disk('public')->size($panduanPdfSetting) / 1024, 1) : null;

        return view('superadmin.pengaturan.index', [
            'pengaturan' => $pengaturan,
            'totalParameter' => $totalParameter,
            'kuotaBulanan' => $kuotaBulanan,
            'rasioMentee' => $rasioMentee,
            'masaTenggang' => $masaTenggang,
            'hasCustomHeroImage' => $hasCustomHeroImage,
            'heroImageUrl' => $heroImageUrl,
            'hasCustomPanduanPdf' => $hasCustomPanduanPdf,
            'panduanPdfName' => $panduanPdfName,
            'panduanPdfSize' => $panduanPdfSize,
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
            'hero_image' => ['nullable', 'file', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'panduan_pdf' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
        ]);

        $settingsToSave = [
            'kuota_umum_maks_per_bulan' => (string) $validated['kuota_umum_maks_per_bulan'],
            'maks_peserta_aktif_per_pembimbing' => ! empty($validated['maks_peserta_aktif_per_pembimbing']) ? (string) $validated['maks_peserta_aktif_per_pembimbing'] : '20',
            'logbook_maks_mundur_hari' => (string) $validated['logbook_maks_mundur_hari'],
            'berkas_maks_mb' => (string) $validated['berkas_maks_mb'],
            'deadline_mendekati_hari' => (string) $validated['deadline_mendekati_hari'],
            'masa_tenggang_bulan' => (string) $validated['masa_tenggang_bulan'],
            'retensi_audit_akses_data_hari' => (string) ($validated['retensi_audit_akses_data_hari'] ?? '365'),
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

        // Proses unggah aset konten publik jika ada
        $uploadedAssets = [];

        if ($request->hasFile('hero_image')) {
            $heroFile = $request->file('hero_image');
            $oldHero = Pengaturan::getNilai('landing_hero_image');
            $newHeroPath = $heroFile->store('landing/hero', 'public');

            if ($oldHero && Storage::disk('public')->exists($oldHero) && $oldHero !== $newHeroPath) {
                Storage::disk('public')->delete($oldHero);
            }

            Pengaturan::setNilai('landing_hero_image', $newHeroPath);
            $this->syncHeroBlockInDocument($newHeroPath);
            $uploadedAssets[] = 'Foto Hero Banner';
        }

        if ($request->hasFile('panduan_pdf')) {
            $pdfFile = $request->file('panduan_pdf');
            $oldPdf = Pengaturan::getNilai('landing_panduan_pdf');
            $newPdfPath = $pdfFile->store('landing/panduan', 'public');

            if ($oldPdf && Storage::disk('public')->exists($oldPdf) && $oldPdf !== $newPdfPath) {
                Storage::disk('public')->delete($oldPdf);
            }

            Pengaturan::setNilai('landing_panduan_pdf', $newPdfPath);
            Pengaturan::setNilai('landing_panduan_pdf_name', $pdfFile->getClientOriginalName());
            $uploadedAssets[] = 'Berkas Panduan PDF';
        }

        if (! empty($uploadedAssets)) {
            Pengaturan::clearCache();

            $this->auditService->catat(
                tipe: TipeAuditEnum::Aksi,
                tabel: 'pengaturan',
                idData: 0,
                detail: [
                    'aksi' => 'Memperbarui aset konten publik landing page: '.implode(', ', $uploadedAssets),
                    'superadmin_id' => $request->user()->id,
                ]
            );
        }

        $pesanSukses = ! empty($uploadedAssets)
            ? 'Konfigurasi sistem dan aset konten publik ('.implode(', ', $uploadedAssets).') berhasil diperbarui.'
            : 'Konfigurasi sistem berhasil diperbarui.';

        return redirect()->route('superadmin.pengaturan.index')
            ->with('success', $pesanSukses);
    }

    /**
     * Mereset foto hero banner kembali ke foto default Diskominfo Sleman.
     */
    public function resetHeroImage(Request $request): RedirectResponse
    {
        $oldHero = Pengaturan::getNilai('landing_hero_image');
        if ($oldHero && Storage::disk('public')->exists($oldHero)) {
            Storage::disk('public')->delete($oldHero);
        }

        Pengaturan::deleteKunci('landing_hero_image');
        $this->syncHeroBlockInDocument(null);
        Pengaturan::clearCache();

        $this->auditService->catat(
            tipe: TipeAuditEnum::Aksi,
            tabel: 'pengaturan',
            idData: 0,
            detail: [
                'aksi' => 'Mereset foto hero banner landing page ke default bawaan sistem',
                'superadmin_id' => $request->user()->id,
            ]
        );

        return redirect()->route('superadmin.pengaturan.index')
            ->with('success', 'Foto hero banner berhasil dikembalikan ke default Diskominfo Sleman.');
    }

    /**
     * Mereset berkas panduan PDF kembali ke generator PDF otomatis sistem.
     */
    public function resetPanduanPdf(Request $request): RedirectResponse
    {
        $oldPdf = Pengaturan::getNilai('landing_panduan_pdf');
        if ($oldPdf && Storage::disk('public')->exists($oldPdf)) {
            Storage::disk('public')->delete($oldPdf);
        }

        Pengaturan::deleteKunci('landing_panduan_pdf');
        Pengaturan::deleteKunci('landing_panduan_pdf_name');
        Pengaturan::clearCache();

        $this->auditService->catat(
            tipe: TipeAuditEnum::Aksi,
            tabel: 'pengaturan',
            idData: 0,
            detail: [
                'aksi' => 'Mereset berkas panduan PDF landing page ke generator PDF resmi sistem',
                'superadmin_id' => $request->user()->id,
            ]
        );

        return redirect()->route('superadmin.pengaturan.index')
            ->with('success', 'Berkas panduan PDF berhasil dikembalikan ke generator PDF otomatis sistem.');
    }

    /**
     * Menyinkronkan path gambar hero ke dalam landing_page_document JSON jika ada.
     */
    private function syncHeroBlockInDocument(?string $imagePath): void
    {
        foreach (['landing_page_document', 'draft_landing_page_document'] as $docKey) {
            $raw = Pengaturan::getNilai($docKey);
            if (! empty($raw)) {
                $doc = json_decode($raw, true);
                if (is_array($doc) && isset($doc['blocks']) && is_array($doc['blocks'])) {
                    $changed = false;
                    foreach ($doc['blocks'] as &$block) {
                        if (($block['type'] ?? '') === 'hero') {
                            $block['content']['image'] = $imagePath;
                            $changed = true;
                        }
                    }
                    unset($block);

                    if ($changed) {
                        Pengaturan::setNilai($docKey, json_encode($doc, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
                    }
                }
            }
        }
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
    public function exportMasterSnapshotJson(Request $request): Response
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
