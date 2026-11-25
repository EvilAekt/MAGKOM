<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\TipeAuditEnum;
use App\Models\Pengaturan as PengaturanModel;
use App\Models\User;
use App\Services\LandingPage\BlockSchemaValidator;
use App\Services\LandingPage\LandingPageDocumentService;
use App\Services\LandingPage\LegacyCmsAdapter;
use App\Support\Pengaturan;
use Carbon\CarbonImmutable;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LandingCmsStagingService
{
    public function __construct(
        protected AuditService $auditService
    ) {}

    /**
     * Memeriksa apakah ada draf perubahan CMS yang aktif/tertunda.
     */
    public function hasActiveDraft(): bool
    {
        $isActive = Pengaturan::getLive('landing_draft_active');
        if ($isActive === '1') {
            return true;
        }

        return PengaturanModel::where('kunci', 'LIKE', 'draft_%')->exists();
    }

    /**
     * Mengambil metadata status draf kontainer saat ini.
     *
     * @return array{active: bool, updated_at: ?string, author_name: ?string, total_draft_keys: int}
     */
    public function getDraftMetadata(): array
    {
        $hasActive = $this->hasActiveDraft();
        $totalDraftKeys = PengaturanModel::where('kunci', 'LIKE', 'draft_%')->count();

        return [
            'active' => $hasActive,
            'updated_at' => Pengaturan::getLive('landing_draft_updated_at'),
            'author_name' => Pengaturan::getLive('landing_draft_author_name'),
            'total_draft_keys' => $totalDraftKeys,
        ];
    }

    /**
     * Menyimpan data draf untuk section tertentu ke dalam kontainer.
     *
     * @param  array<string, mixed>  $data
     */
    public function saveDraft(string $section, array $data, ?UploadedFile $foto = null, ?UploadedFile $pdf = null, ?User $user = null): void
    {
        DB::transaction(function () use ($section, $data, $foto, $pdf, $user) {
            foreach ($data as $key => $value) {
                $draftKey = 'draft_'.$key;

                if (is_array($value)) {
                    Pengaturan::setJson($draftKey, $value);
                } else {
                    Pengaturan::set($draftKey, (string) ($value ?? ''));
                }
            }

            // Kelola file foto banner sementara untuk draf
            if ($foto !== null) {
                $oldDraftImage = Pengaturan::getLive('draft_landing_hero_image');
                if ($oldDraftImage && Storage::disk('public')->exists($oldDraftImage)) {
                    Storage::disk('public')->delete($oldDraftImage);
                }

                $draftImagePath = $foto->store('landing/draft', 'public');
                Pengaturan::set('draft_landing_hero_image', $draftImagePath);
            }

            // Kelola file panduan PDF sementara untuk draf
            if ($pdf !== null) {
                $oldDraftPdf = Pengaturan::getLive('draft_landing_panduan_pdf');
                if ($oldDraftPdf && Storage::disk('public')->exists($oldDraftPdf)) {
                    Storage::disk('public')->delete($oldDraftPdf);
                }

                $rawOriginalName = basename($pdf->getClientOriginalName());
                $safeOriginalName = preg_replace('/[^a-zA-Z0-9_\-\. ]/', '_', $rawOriginalName);
                if (empty($safeOriginalName) || ! str_ends_with(strtolower($safeOriginalName), '.pdf')) {
                    $safeOriginalName = 'Buku-Panduan-Magang.pdf';
                }

                $draftPdfPath = $pdf->store('landing/draft', 'public');
                Pengaturan::set('draft_landing_panduan_pdf', $draftPdfPath);
                Pengaturan::set('draft_landing_panduan_pdf_name', $safeOriginalName);
            }

            $waktuSekarang = CarbonImmutable::now('Asia/Jakarta')->translatedFormat('d F Y H:i').' WIB';
            $authorName = $user ? $user->name : 'Super Admin';

            Pengaturan::set('landing_draft_active', '1');
            Pengaturan::set('landing_draft_updated_at', $waktuSekarang);
            Pengaturan::set('landing_draft_author_name', $authorName);

            Pengaturan::clearCache();

            $this->auditService->catat(
                tipe: TipeAuditEnum::Aksi,
                tabel: 'pengaturan',
                idData: 0,
                detail: [
                    'aksi' => "Menyimpan draf perubahan CMS section {$section} ke kontainer staging",
                    'superadmin_id' => $user?->id,
                    'section' => $section,
                ]
            );
        });
    }

    /**
     * Mempublikasikan seluruh draf yang ada ke versi live (produksi).
     */
    public function publishDraft(?User $user = null): int
    {
        return Cache::lock('landing_cms_publish_lock', 10)->block(3, function () use ($user) {
            return DB::transaction(function () use ($user) {
                // 1. Simpan backup penuh dari seluruh kunci live saat ini sebelum ditimpa
                $currentLiveKeys = PengaturanModel::where('kunci', 'LIKE', 'landing_%')
                    ->orWhere('kunci', 'LIKE', 'kebijakan_privasi_%')
                    ->get();
                $backupSettings = [];
                foreach ($currentLiveKeys as $liveItem) {
                    // Abaikan meta draf, backup, dan riwayat revisi
                    if (! str_starts_with($liveItem->kunci, 'landing_draft_') && $liveItem->kunci !== 'landing_live_backup' && $liveItem->kunci !== 'landing_revisions') {
                        $backupSettings[$liveItem->kunci] = $liveItem->nilai;
                    }
                }

                $liveDoc = null;
                try {
                    $liveDoc = app(LandingPageDocumentService::class)->getDocument(draft: false);
                } catch (\Throwable $e) {
                    // Abaikan jika konteks service tidak tersedia
                }

                $backupSnapshot = [
                    'settings' => $backupSettings,
                    'document' => $liveDoc,
                    'created_at' => CarbonImmutable::now('Asia/Jakarta')->toIso8601String(),
                    'author_id' => $user?->id,
                    'author_name' => $user ? $user->name : 'Super Admin',
                ];
                Pengaturan::set('landing_live_backup', json_encode($backupSnapshot, JSON_UNESCAPED_UNICODE));

                $drafts = PengaturanModel::where('kunci', 'LIKE', 'draft_%')->get();
                $countPublished = 0;

                foreach ($drafts as $draft) {
                    $originalKey = substr($draft->kunci, 6); // Hapus awalan 'draft_'

                    // Tangani file foto jika ada di draf
                    if ($originalKey === 'landing_hero_image') {
                        $draftImagePath = $draft->nilai;
                        if ($draftImagePath && Storage::disk('public')->exists($draftImagePath)) {
                            $oldLiveImage = Pengaturan::getLive('landing_hero_image');
                            if ($oldLiveImage && Storage::disk('public')->exists($oldLiveImage) && $oldLiveImage !== $draftImagePath && ! str_starts_with($oldLiveImage, 'landing/media/')) {
                                Storage::disk('public')->delete($oldLiveImage);
                            }

                            $newLivePath = 'landing/'.basename($draftImagePath);
                            if ($draftImagePath !== $newLivePath) {
                                Storage::disk('public')->copy($draftImagePath, $newLivePath);
                            }
                            $draft->nilai = $newLivePath;
                        }
                    }

                    // Tangani file PDF jika ada di draf
                    if ($originalKey === 'landing_panduan_pdf') {
                        $draftPdfPath = $draft->nilai;
                        if ($draftPdfPath && Storage::disk('public')->exists($draftPdfPath)) {
                            $oldLivePdf = Pengaturan::getLive('landing_panduan_pdf');
                            if ($oldLivePdf && Storage::disk('public')->exists($oldLivePdf) && $oldLivePdf !== $draftPdfPath && ! str_starts_with($oldLivePdf, 'landing/media/')) {
                                Storage::disk('public')->delete($oldLivePdf);
                            }

                            $newLivePath = 'landing/'.basename($draftPdfPath);
                            if ($draftPdfPath !== $newLivePath) {
                                Storage::disk('public')->copy($draftPdfPath, $newLivePath);
                            }
                            $draft->nilai = $newLivePath;
                        }
                    }

                    Pengaturan::set($originalKey, $draft->nilai ?? '');
                    $draft->delete();
                    $countPublished++;
                }

                $draftVer = (int) Pengaturan::getLive('draft_landing_document_version', '1');
                Pengaturan::set('landing_document_version', (string) max(1, $draftVer));

                Pengaturan::set('landing_draft_active', '0');
                Pengaturan::delete('landing_draft_updated_at');
                Pengaturan::delete('landing_draft_author_name');
                Pengaturan::clearCache();

                // Pastikan dokumen live landing_page_document sinkron dengan kunci hero dan tahapan yang baru diterbitkan
                $liveDocRaw = PengaturanModel::where('kunci', 'landing_page_document')->value('nilai');
                if ($liveDocRaw !== null && $liveDocRaw !== '') {
                    $liveDoc = json_decode($liveDocRaw, true);
                    if (is_array($liveDoc) && isset($liveDoc['blocks']) && is_array($liveDoc['blocks'])) {
                        $docChanged = false;
                        $liveHeroImage = PengaturanModel::where('kunci', 'landing_hero_image')->value('nilai');
                        $liveHeroSlidesRaw = PengaturanModel::where('kunci', 'landing_hero_slides')->value('nilai');
                        $liveHeroSlides = $liveHeroSlidesRaw ? json_decode($liveHeroSlidesRaw, true) : null;
                        $liveLabelPersyaratan = PengaturanModel::where('kunci', 'landing_label_persyaratan')->value('nilai');
                        $livePersyaratanUmum = PengaturanModel::where('kunci', 'landing_persyaratan_umum')->value('nilai');
                        $liveLabelDokumen = PengaturanModel::where('kunci', 'landing_label_dokumen')->value('nilai');
                        $liveDokumenPendaftaran = PengaturanModel::where('kunci', 'landing_dokumen_pendaftaran')->value('nilai');
                        $liveTahapanHeading = PengaturanModel::where('kunci', 'landing_tahapan_heading')->value('nilai');
                        $liveTahapanSummary = PengaturanModel::where('kunci', 'landing_tahapan_summary')->value('nilai');
                        $liveTahapanStepsRaw = PengaturanModel::where('kunci', 'landing_tahapan_steps')->value('nilai');
                        $liveTahapanSteps = $liveTahapanStepsRaw ? json_decode($liveTahapanStepsRaw, true) : null;
                        $liveTahapanBtnPdf = PengaturanModel::where('kunci', 'landing_tahapan_btn_pdf')->value('nilai');
                        $liveTahapanBtnPeta = PengaturanModel::where('kunci', 'landing_tahapan_btn_peta')->value('nilai');
                        $liveFaqHeading = PengaturanModel::where('kunci', 'landing_faq_heading')->value('nilai');
                        $liveFaqDataRaw = PengaturanModel::where('kunci', 'landing_faq_data')->value('nilai');
                        $liveFaqData = $liveFaqDataRaw ? json_decode($liveFaqDataRaw, true) : null;

                        foreach ($liveDoc['blocks'] as &$b) {
                            if (($b['type'] ?? '') === 'hero') {
                                if ($liveHeroImage && ($b['content']['image'] ?? '') !== $liveHeroImage) {
                                    $b['content']['image'] = $liveHeroImage;
                                    $docChanged = true;
                                }
                                if (is_array($liveHeroSlides)) {
                                    $b['content']['slides'] = $liveHeroSlides;
                                    if (! empty($liveHeroSlides[0]['image']) && is_string($liveHeroSlides[0]['image'])) {
                                        $b['content']['image'] = (string) $liveHeroSlides[0]['image'];
                                    }
                                    $docChanged = true;
                                }
                                if ($liveLabelPersyaratan !== null && ($b['content']['label_persyaratan'] ?? '') !== $liveLabelPersyaratan) {
                                    $b['content']['label_persyaratan'] = $liveLabelPersyaratan;
                                    $docChanged = true;
                                }
                                if ($livePersyaratanUmum !== null && ($b['content']['persyaratan_umum'] ?? '') !== $livePersyaratanUmum) {
                                    $b['content']['persyaratan_umum'] = $livePersyaratanUmum;
                                    $docChanged = true;
                                }
                                if ($liveLabelDokumen !== null && ($b['content']['label_dokumen'] ?? '') !== $liveLabelDokumen) {
                                    $b['content']['label_dokumen'] = $liveLabelDokumen;
                                    $docChanged = true;
                                }
                                if ($liveDokumenPendaftaran !== null && ($b['content']['dokumen_pendaftaran'] ?? '') !== $liveDokumenPendaftaran) {
                                    $b['content']['dokumen_pendaftaran'] = $liveDokumenPendaftaran;
                                    $docChanged = true;
                                }
                            }
                            if (($b['type'] ?? '') === 'tahapan') {
                                if ($liveTahapanHeading !== null && ($b['content']['heading'] ?? '') !== $liveTahapanHeading) {
                                    $b['content']['heading'] = $liveTahapanHeading;
                                    $docChanged = true;
                                }
                                if ($liveTahapanSummary !== null && ($b['content']['summary'] ?? '') !== $liveTahapanSummary) {
                                    $b['content']['summary'] = $liveTahapanSummary;
                                    $docChanged = true;
                                }
                                if (is_array($liveTahapanSteps) && count($liveTahapanSteps) > 0) {
                                    $b['content']['steps'] = $liveTahapanSteps;
                                    $docChanged = true;
                                }
                                if ($liveTahapanBtnPdf !== null && ($b['content']['btn_pdf_text'] ?? '') !== $liveTahapanBtnPdf) {
                                    $b['content']['btn_pdf_text'] = $liveTahapanBtnPdf;
                                    $docChanged = true;
                                }
                                if ($liveTahapanBtnPeta !== null && ($b['content']['btn_peta_text'] ?? '') !== $liveTahapanBtnPeta) {
                                    $b['content']['btn_peta_text'] = $liveTahapanBtnPeta;
                                    $docChanged = true;
                                }
                            }
                            if (($b['type'] ?? '') === 'faq') {
                                if ($liveFaqHeading !== null && ($b['content']['heading'] ?? '') !== $liveFaqHeading) {
                                    $b['content']['heading'] = $liveFaqHeading;
                                    $docChanged = true;
                                }
                                if (is_array($liveFaqData) && count($liveFaqData) > 0) {
                                    $b['content']['items'] = $liveFaqData;
                                    $docChanged = true;
                                }
                            }
                        }
                        unset($b);
                        if ($docChanged) {
                            Pengaturan::setJson('landing_page_document', $liveDoc);
                        }
                    }
                }

                Pengaturan::clearCache();

                $this->auditService->catat(
                    tipe: TipeAuditEnum::Aksi,
                    tabel: 'pengaturan',
                    idData: 0,
                    detail: [
                        'aksi' => 'Mempublikasikan seluruh draf kontainer CMS ke live publik',
                        'superadmin_id' => $user?->id,
                        'jumlah_elemen' => $countPublished,
                    ]
                );

                return $countPublished;
            });
        });
    }

    /**
     * Membuang (discard) seluruh draf yang belum dipublikasikan dan mengembalikan editor ke versi live.
     */
    public function discardDraft(?User $user = null): int
    {
        return DB::transaction(function () use ($user) {
            $draftImage = Pengaturan::getLive('draft_landing_hero_image');
            if ($draftImage && Storage::disk('public')->exists($draftImage)) {
                Storage::disk('public')->delete($draftImage);
            }

            $draftPdf = Pengaturan::getLive('draft_landing_panduan_pdf');
            if ($draftPdf && Storage::disk('public')->exists($draftPdf)) {
                Storage::disk('public')->delete($draftPdf);
            }

            $drafts = PengaturanModel::where('kunci', 'LIKE', 'draft_%')->get();
            $countDeleted = 0;
            foreach ($drafts as $draft) {
                Pengaturan::delete($draft->kunci);
                $countDeleted++;
            }

            Pengaturan::set('landing_draft_active', '0');
            Pengaturan::delete('landing_draft_updated_at');
            Pengaturan::delete('landing_draft_author_name');

            Pengaturan::clearCache();

            $this->auditService->catat(
                tipe: TipeAuditEnum::Aksi,
                tabel: 'pengaturan',
                idData: 0,
                detail: [
                    'aksi' => 'Membuang (discard) seluruh draf kontainer CMS',
                    'superadmin_id' => $user?->id,
                    'jumlah_dihapus' => $countDeleted,
                ]
            );

            return $countDeleted;
        });
    }

    /**
     * Dapatkan susunan pohon blok (Block Tree Layout) saat ini.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getBlocksLayout(): array
    {
        $default = LandingBlockRegistry::getDefaultLayout();
        $layout = Pengaturan::json('landing_blocks_layout', $default);

        if (empty($layout) || ! is_array($layout)) {
            return $default;
        }

        return LandingBlockRegistry::sanitizeLayout($layout);
    }

    /**
     * Simpan susunan blok ke kontainer draf.
     *
     * @param  array<mixed>  $layout
     */
    public function saveBlocksLayout(array $layout, ?User $user = null): array
    {
        $sanitized = LandingBlockRegistry::sanitizeLayout($layout);

        $this->saveSiteEditorDraft([
            'landing_blocks_layout' => $sanitized,
        ], $user);

        return $sanitized;
    }

    /**
     * Memulihkan (rollback 1 langkah) versi live ke cadangan otomatis sebelum publikasi terakhir.
     */
    public function rollbackToBackup(?User $user = null): int
    {
        return Cache::lock('landing_cms_rollback_lock', 10)->block(3, function () use ($user) {
            $backupJson = Pengaturan::getLive('landing_live_backup');
            if (! $backupJson) {
                return 0;
            }

            $decoded = json_decode($backupJson, true);
            if (! is_array($decoded) || empty($decoded)) {
                return 0;
            }

            $backupSettings = [];
            $backupDocument = null;

            if (isset($decoded['settings']) && is_array($decoded['settings'])) {
                $backupSettings = $decoded['settings'];
                $backupDocument = $decoded['document'] ?? null;
            } else {
                $backupSettings = $decoded;
            }

            if (empty($backupSettings) && empty($backupDocument)) {
                return 0;
            }

            return DB::transaction(function () use ($backupSettings, $backupDocument, $user) {
                $count = 0;
                foreach ($backupSettings as $kunci => $nilai) {
                    $liveKey = str_starts_with($kunci, 'draft_') ? substr($kunci, 6) : $kunci;
                    Pengaturan::set($liveKey, (string) ($nilai ?? ''));
                    $count++;
                }

                if ($backupDocument !== null && is_array($backupDocument)) {
                    Pengaturan::setJson('landing_page_document', $backupDocument);
                    LegacyCmsAdapter::syncDocumentToLegacySettings($backupDocument, draft: false);
                }

                $currVer = (int) Pengaturan::getLive('landing_document_version', '1');
                Pengaturan::set('landing_document_version', (string) ($currVer + 1));

                Pengaturan::clearCache();

                $this->auditService->catat(
                    tipe: TipeAuditEnum::Aksi,
                    tabel: 'pengaturan',
                    idData: 0,
                    detail: [
                        'aksi' => 'Melakukan rollback tampilan publik ke backup sebelum publikasi terakhir',
                        'superadmin_id' => $user?->id,
                        'jumlah_elemen' => $count,
                    ]
                );

                return $count;
            });
        });
    }

    /**
     * Validasi tanda tangan berkas (magic bytes) untuk mencegah spoofing ekstensi.
     *
     * @return array{valid: bool, error: ?string}
     */
    public function validateFileSignature(UploadedFile $file): array
    {
        $path = $file->getRealPath();
        if (! $path || ! file_exists($path)) {
            return ['valid' => false, 'error' => 'Berkas tidak valid atau tidak dapat dibaca.'];
        }

        $clientMime = $file->getMimeType() ?: '';
        $clientExt = strtolower($file->getClientOriginalExtension());

        // 1. Pengecekan PDF
        if ($clientExt === 'pdf' || $clientMime === 'application/pdf') {
            $handle = @fopen($path, 'rb');
            if (! $handle) {
                return ['valid' => false, 'error' => 'Gagal membaca berkas PDF.'];
            }
            $header = fread($handle, 5);
            fclose($handle);

            if ($header !== '%PDF-') {
                return [
                    'valid' => false,
                    'error' => 'Berkas yang diunggah bukan dokumen PDF yang valid (magic bytes mismatch).',
                ];
            }

            return ['valid' => true, 'error' => null];
        }

        // 2. Larang Berkas Berbasis SVG Secara Tegas (Stored XSS & XML Injection Protection)
        $sampleContent = @file_get_contents($path, false, null, 0, 4096) ?: '';
        $isSvgExtOrMime = ($clientExt === 'svg' || str_contains($clientMime, 'svg'));
        $containsSvgTags = (stripos($sampleContent, '<svg') !== false || stripos($sampleContent, 'xmlns="http://www.w3.org/2000/svg"') !== false);

        if ($isSvgExtOrMime || $containsSvgTags) {
            return [
                'valid' => false,
                'error' => 'Format berkas SVG tidak diizinkan demi keamanan sistem (Stored XSS Protection). Harap gunakan format gambar raster PNG, JPG, atau WEBP.',
            ];
        }

        // 3. Pengecekan Gambar Raster (JPEG, PNG, WEBP)
        if (in_array($clientExt, ['jpg', 'jpeg', 'png', 'webp'], true) || str_starts_with($clientMime, 'image/')) {
            $imgInfo = @getimagesize($path);
            if ($imgInfo === false) {
                return [
                    'valid' => false,
                    'error' => 'Berkas gambar rusak atau bukan gambar raster yang valid.',
                ];
            }

            // Pastikan format gambar sesuai dengan tipe mime yang didukung
            $allowedImageTypes = [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_WEBP];
            if (! in_array($imgInfo[2], $allowedImageTypes, true)) {
                return [
                    'valid' => false,
                    'error' => 'Tipe gambar tidak didukung. Harap unggah berkas PNG, JPEG, atau WEBP.',
                ];
            }

            return ['valid' => true, 'error' => null];
        }

        return ['valid' => true, 'error' => null];
    }

    /**
     * Ambil data berkas media terdaftar.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getMediaLibrary(?string $type = null, ?string $search = null, bool $withUsage = false): array
    {
        $raw = Pengaturan::getLive('landing_media_library');
        $media = [];

        if ($raw) {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                $media = $decoded;
            }
        }

        // Filter media yang berkas fisiknya benar-benar ada di storage publik
        $media = array_values(array_filter($media, function ($item) {
            return ! empty($item['path']) && Storage::disk('public')->exists($item['path']);
        }));

        // Normalisasi URL secara dinamis agar selalu sesuai origin/host request saat ini (mencegah hardcoded host mismatch)
        foreach ($media as &$item) {
            $item['url'] = asset('storage/'.ltrim((string) $item['path'], '/'));
        }
        unset($item);

        // Filter berdasarkan jenis jika diminta (image atau pdf)
        if ($type) {
            $media = array_values(array_filter($media, function ($item) use ($type) {
                if ($type === 'image') {
                    return str_starts_with((string) ($item['mime_type'] ?? ''), 'image/');
                }
                if ($type === 'pdf') {
                    return ($item['mime_type'] ?? '') === 'application/pdf';
                }

                return true;
            }));
        }

        // Filter pencarian nama berkas
        if ($search) {
            $term = strtolower(trim($search));
            $media = array_values(array_filter($media, function ($item) use ($term) {
                $name = strtolower((string) ($item['filename'] ?? ''));
                $orig = strtolower((string) ($item['original_name'] ?? ''));

                return str_contains($name, $term) || str_contains($orig, $term);
            }));
        }

        return $media;
    }

    /**
     * Tambahkan berkas baru ke Media Library terpusat.
     *
     * @param  array<string, mixed>|string|null  $metadata
     * @return array<string, mixed>
     */
    public function addMedia(UploadedFile $file, array|string|null $metadata = [], ?string $caption = null, ?User $user = null): array
    {
        $altText = '';
        $capText = $caption ?? '';
        $title = '';
        $description = '';

        if (is_array($metadata)) {
            $altText = (string) ($metadata['alt_text'] ?? '');
            $capText = (string) ($metadata['caption'] ?? $caption ?? '');
            $title = (string) ($metadata['title'] ?? '');
            $description = (string) ($metadata['description'] ?? '');
        } else {
            $altText = (string) ($metadata ?? '');
        }

        $sigCheck = $this->validateFileSignature($file);
        if (! $sigCheck['valid']) {
            throw new \InvalidArgumentException($sigCheck['error'] ?? 'Berkas tidak valid.');
        }

        $mime = $file->getMimeType() ?: 'application/octet-stream';
        $size = $file->getSize();
        $originalName = basename($file->getClientOriginalName());
        $safeOriginalName = preg_replace('/[^a-zA-Z0-9_\-\. ]/', '_', $originalName);

        // Ekstraksi dimensi gambar jika relevan
        $dimensions = null;
        if (str_starts_with($mime, 'image/')) {
            $info = @getimagesize($file->getRealPath());
            if ($info && isset($info[0], $info[1])) {
                $dimensions = "{$info[0]} x {$info[1]}";
            }
        }

        $path = $file->store('landing/media', 'public');
        $url = asset('storage/'.ltrim($path, '/'));

        $mediaId = 'med-'.time().'-'.bin2hex(random_bytes(3));
        $item = [
            'id' => $mediaId,
            'filename' => basename($path),
            'original_name' => $safeOriginalName,
            'path' => $path,
            'url' => $url,
            'mime_type' => $mime,
            'size_bytes' => $size,
            'size_formatted' => round($size / 1024, 1).' KB',
            'dimensions' => $dimensions,
            'alt_text' => $altText,
            'caption' => $capText,
            'title' => $title,
            'description' => $description,
            'uploaded_by' => $user?->name ?? 'Super Admin',
            'created_at' => CarbonImmutable::now('Asia/Jakarta')->translatedFormat('d M Y H:i').' WIB',
            'usage_count' => 0,
            'usage_locations' => [],
            'is_in_use' => false,
        ];

        $list = $this->getMediaLibrary(withUsage: false);
        array_unshift($list, $item);

        Pengaturan::set('landing_media_library', json_encode($list, JSON_UNESCAPED_UNICODE));

        $this->auditService->catat(
            tipe: TipeAuditEnum::Aksi,
            tabel: 'pengaturan',
            idData: 0,
            detail: [
                'aksi' => "Mengunggah media {$safeOriginalName} ke Media Library",
                'superadmin_id' => $user?->id,
                'media_id' => $mediaId,
                'mime_type' => $mime,
                'size_bytes' => $size,
            ]
        );

        return $item;
    }

    /**
     * Hapus berkas dari Media Library.
     *
     * @return array{success: bool, message: string}
     */
    public function deleteMedia(string $id, ?User $user = null): array
    {
        $list = $this->getMediaLibrary();
        $targetIndex = null;
        $targetItem = null;

        foreach ($list as $idx => $item) {
            if ($item['id'] === $id) {
                $targetIndex = $idx;
                $targetItem = $item;
                break;
            }
        }

        if ($targetIndex === null || ! $targetItem) {
            return ['success' => false, 'message' => 'Berkas media tidak ditemukan.'];
        }

        // Hapus fisik jika ada di storage
        if (Storage::disk('public')->exists($targetItem['path'])) {
            Storage::disk('public')->delete($targetItem['path']);
        }

        array_splice($list, $targetIndex, 1);
        Pengaturan::set('landing_media_library', json_encode($list, JSON_UNESCAPED_UNICODE));

        $this->auditService->catat(
            tipe: TipeAuditEnum::Aksi,
            tabel: 'pengaturan',
            idData: 0,
            detail: [
                'aksi' => "Menghapus media {$targetItem['filename']} dari Media Library",
                'superadmin_id' => $user?->id,
                'media_id' => $id,
            ]
        );

        return ['success' => true, 'message' => 'Berkas media berhasil dihapus dari sistem.'];
    }

    /**
     * Dapatkan nomor versi dokumen draf atau live saat ini.
     */
    public function getDocumentVersion(bool $draft = true): int
    {
        $key = $draft ? 'draft_landing_document_version' : 'landing_document_version';

        return max(1, (int) Pengaturan::getLive($key, '1'));
    }

    /**
     * Menyimpan batch draf dari WordPress-style Site Editor (konten teks, token warna, dan parameter dimensi pixel).
     *
     * @param  array<string, mixed>  $data
     * @return array{document_version: int, updated_at: string, author_name: string}
     */
    public function saveSiteEditorDraft(array $data, ?User $user = null): array
    {
        return DB::transaction(function () use ($data, $user) {
            foreach ($data as $key => $value) {
                // Lewati parameter kontrol versi
                if (in_array($key, ['expected_version', 'document_version', 'force'], true)) {
                    continue;
                }

                // Pastikan key diawali dengan 'landing_' jika bukan kunci khusus kebijakan_privasi_
                $cleanKey = (str_starts_with($key, 'landing_') || str_starts_with($key, 'kebijakan_privasi_')) ? $key : 'landing_'.$key;
                $draftKey = 'draft_'.$cleanKey;

                if (is_array($value)) {
                    // Sanitasi khusus layout blok jika ada
                    if ($cleanKey === 'landing_blocks_layout') {
                        $value = LandingBlockRegistry::sanitizeLayout($value);
                    }
                    if ($cleanKey === 'landing_page_document') {
                        // Sinkronkan flat keys dari request data ke blok dokumen terkait sebelum validasi
                        if (isset($value['blocks']) && is_array($value['blocks'])) {
                            foreach ($value['blocks'] as &$b) {
                                if (($b['type'] ?? '') === 'hero') {
                                    if (isset($data['landing_hero_title'])) {
                                        $b['content']['title'] = (string) $data['landing_hero_title'];
                                    }
                                    if (isset($data['landing_hero_kicker'])) {
                                        $b['content']['kicker'] = (string) $data['landing_hero_kicker'];
                                    }
                                    if (isset($data['landing_hero_period_info'])) {
                                        $b['content']['period_info'] = (string) $data['landing_hero_period_info'];
                                    }
                                    if (isset($data['landing_hero_cta_text'])) {
                                        $b['content']['cta_primary_text'] = (string) $data['landing_hero_cta_text'];
                                    }
                                    if (isset($data['landing_hero_cta_primary_url'])) {
                                        $b['content']['cta_primary_url'] = (string) $data['landing_hero_cta_primary_url'];
                                    }
                                    if (isset($data['landing_hero_cta_secondary_text'])) {
                                        $b['content']['cta_secondary_text'] = (string) $data['landing_hero_cta_secondary_text'];
                                    }
                                    if (isset($data['landing_hero_cta_secondary_url'])) {
                                        $b['content']['cta_secondary_url'] = (string) $data['landing_hero_cta_secondary_url'];
                                    }
                                    if (isset($data['landing_hero_image'])) {
                                        $b['content']['image'] = (string) $data['landing_hero_image'];
                                    }
                                    if (isset($data['landing_hero_slides']) && is_array($data['landing_hero_slides'])) {
                                        $b['content']['slides'] = $data['landing_hero_slides'];
                                        if (! empty($data['landing_hero_slides'][0]['image'])) {
                                            $b['content']['image'] = (string) $data['landing_hero_slides'][0]['image'];
                                        }
                                    }
                                    if (isset($data['landing_hero_overlay_active'])) {
                                        $b['content']['overlay_active'] = (bool) $data['landing_hero_overlay_active'];
                                    }
                                    if (isset($data['landing_hero_year'])) {
                                        $b['content']['year'] = (string) $data['landing_hero_year'];
                                    }
                                    if (isset($data['landing_label_persyaratan'])) {
                                        $b['content']['label_persyaratan'] = (string) $data['landing_label_persyaratan'];
                                    }
                                    if (isset($data['landing_persyaratan_umum'])) {
                                        $b['content']['persyaratan_umum'] = (string) $data['landing_persyaratan_umum'];
                                    }
                                    if (isset($data['landing_label_dokumen'])) {
                                        $b['content']['label_dokumen'] = (string) $data['landing_label_dokumen'];
                                    }
                                    if (isset($data['landing_dokumen_pendaftaran'])) {
                                        $b['content']['dokumen_pendaftaran'] = (string) $data['landing_dokumen_pendaftaran'];
                                    }
                                }
                                if (($b['type'] ?? '') === 'tahapan') {
                                    if (isset($data['landing_tahapan_heading'])) {
                                        $b['content']['heading'] = (string) $data['landing_tahapan_heading'];
                                    }
                                    if (isset($data['landing_tahapan_summary'])) {
                                        $b['content']['summary'] = (string) $data['landing_tahapan_summary'];
                                    }
                                    if (isset($data['landing_tahapan_steps']) && is_array($data['landing_tahapan_steps'])) {
                                        $b['content']['steps'] = $data['landing_tahapan_steps'];
                                    }
                                    if (isset($data['landing_tahapan_btn_pdf'])) {
                                        $b['content']['btn_pdf_text'] = (string) $data['landing_tahapan_btn_pdf'];
                                    }
                                    if (isset($data['landing_tahapan_btn_peta'])) {
                                        $b['content']['btn_peta_text'] = (string) $data['landing_tahapan_btn_peta'];
                                    }
                                }
                                if (($b['type'] ?? '') === 'faq') {
                                    if (isset($data['landing_faq_heading'])) {
                                        $b['content']['heading'] = (string) $data['landing_faq_heading'];
                                    }
                                    if (isset($data['landing_faq_data']) && is_array($data['landing_faq_data'])) {
                                        $b['content']['items'] = $data['landing_faq_data'];
                                    }
                                }
                            }
                            unset($b);
                        }

                        $validator = app(BlockSchemaValidator::class);
                        $value = $validator->validateAndSanitize($value);
                    }
                    if ($cleanKey === 'landing_global_styles') {
                        $validator = app(BlockSchemaValidator::class);
                        $value = $validator->sanitizeGlobalStyles($value);
                        LegacyCmsAdapter::syncDocumentToLegacySettings(['global_styles' => $value], draft: true);
                    }
                    Pengaturan::setJson($draftKey, $value);

                    if ($cleanKey === 'landing_page_document') {
                        LegacyCmsAdapter::syncDocumentToLegacySettings($value, draft: true);
                    }
                } else {
                    Pengaturan::set($draftKey, (string) ($value ?? ''));
                }
            }

            if (! isset($data['landing_page_document']) && ! isset($data['page_document'])) {
                $modernDoc = LegacyCmsAdapter::legacySettingsToDocument(draft: true);
                Pengaturan::setJson('draft_landing_page_document', $modernDoc);
            }

            // Pastikan draft_landing_hero_image dan blok hero di draft_landing_page_document selalu selaras
            $draftHeroImg = Pengaturan::getLive('draft_landing_hero_image');
            if ($draftHeroImg) {
                $draftDocRaw = PengaturanModel::getNilai('draft_landing_page_document');
                if ($draftDocRaw !== null && $draftDocRaw !== '') {
                    $draftDoc = json_decode($draftDocRaw, true);
                    if (is_array($draftDoc) && isset($draftDoc['blocks']) && is_array($draftDoc['blocks'])) {
                        $docChanged = false;
                        foreach ($draftDoc['blocks'] as &$b) {
                            if (($b['type'] ?? '') === 'hero') {
                                if (($b['content']['image'] ?? '') !== $draftHeroImg) {
                                    $b['content']['image'] = $draftHeroImg;
                                    $docChanged = true;
                                }
                            }
                        }
                        unset($b);
                        if ($docChanged) {
                            Pengaturan::setJson('draft_landing_page_document', $draftDoc);
                        }
                    }
                }
            }

            // Naikkan versi dokumen draf secara incremental
            $currentVer = (int) Pengaturan::getLive('draft_landing_document_version', '1');
            $newVer = $currentVer + 1;
            Pengaturan::set('draft_landing_document_version', (string) $newVer);

            $waktuSekarang = CarbonImmutable::now('Asia/Jakarta')->translatedFormat('d F Y H:i').' WIB';
            $authorName = $user ? $user->name : 'Super Admin';

            Pengaturan::set('landing_draft_active', '1');
            Pengaturan::set('landing_draft_updated_at', $waktuSekarang);
            Pengaturan::set('landing_draft_author_name', $authorName);

            Pengaturan::clearCache();

            $this->auditService->catat(
                tipe: TipeAuditEnum::Aksi,
                tabel: 'pengaturan',
                idData: 0,
                detail: [
                    'aksi' => 'Menyimpan draf perubahan WordPress Site Editor Landing Page ke kontainer staging',
                    'superadmin_id' => $user?->id,
                    'total_keys_updated' => count($data),
                    'document_version' => $newVer,
                ]
            );

            return [
                'document_version' => $newVer,
                'updated_at' => $waktuSekarang,
                'author_name' => $authorName,
            ];
        });
    }
}
