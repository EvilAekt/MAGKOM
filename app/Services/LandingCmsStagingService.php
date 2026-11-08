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

            // Catat revisi draf section
            $this->createRevision("Menyimpan draf section {$section}", $user, 'draft');

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
                            if ($oldLiveImage && Storage::disk('public')->exists($oldLiveImage) && $oldLiveImage !== $draftImagePath) {
                                Storage::disk('public')->delete($oldLiveImage);
                            }

                            $newLivePath = 'landing/'.basename($draftImagePath);
                            Storage::disk('public')->move($draftImagePath, $newLivePath);
                            $draft->nilai = $newLivePath;
                        }
                    }

                    // Tangani file PDF jika ada di draf
                    if ($originalKey === 'landing_panduan_pdf') {
                        $draftPdfPath = $draft->nilai;
                        if ($draftPdfPath && Storage::disk('public')->exists($draftPdfPath)) {
                            $oldLivePdf = Pengaturan::getLive('landing_panduan_pdf');
                            if ($oldLivePdf && Storage::disk('public')->exists($oldLivePdf) && $oldLivePdf !== $draftPdfPath) {
                                Storage::disk('public')->delete($oldLivePdf);
                            }

                            $newLivePath = 'landing/'.basename($draftPdfPath);
                            Storage::disk('public')->move($draftPdfPath, $newLivePath);
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

                // Catat revisi publikasi
                $this->createRevision("Publikasi {$countPublished} elemen draf ke versi live publik", $user, 'published');

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

            // Catat revisi pembuangan draf
            $this->createRevision("Membuang {$countDeleted} elemen draf CMS", $user, 'discard');

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
     * Dapatkan nilai override gaya responsif saat ini (desktop, tablet, mobile).
     *
     * @return array<string, mixed>
     */
    public function getResponsiveStyles(): array
    {
        return Pengaturan::json('landing_responsive_styles', [
            'desktop' => [],
            'tablet' => [],
            'mobile' => [],
        ]);
    }

    /**
     * Simpan override gaya responsif ke kontainer draf.
     *
     * @param  array<string, mixed>  $styles
     */
    public function saveResponsiveStyles(array $styles, ?User $user = null): array
    {
        $clean = [
            'desktop' => is_array($styles['desktop'] ?? null) ? $styles['desktop'] : [],
            'tablet' => is_array($styles['tablet'] ?? null) ? $styles['tablet'] : [],
            'mobile' => is_array($styles['mobile'] ?? null) ? $styles['mobile'] : [],
        ];

        $this->saveSiteEditorDraft([
            'landing_responsive_styles' => $clean,
        ], $user);

        return $clean;
    }

    /**
     * Ambil daftar riwayat versi (Revision History).
     *
     * @return array<int, array<string, mixed>>
     */
    public function getRevisions(): array
    {
        $raw = Pengaturan::getLive('landing_revisions');
        if (! $raw) {
            return [];
        }

        $decoded = json_decode($raw, true);

        return is_array($decoded) ? $decoded : [];
    }

    /**
     * Ambil rekaman revisi spesifik berdasarkan ID revisi.
     *
     * @return array<string, mixed>|null
     */
    public function getRevisionById(string $revisionId): ?array
    {
        $revisions = $this->getRevisions();
        foreach ($revisions as $rev) {
            if (($rev['id'] ?? null) === $revisionId) {
                return $rev;
            }
        }

        return null;
    }

    /**
     * Terapkan kebijakan retensi revisi (Revision Retention Policy).
     * - Milestone publikasi (status: published, rollback) selalu dipertahankan.
     * - Draf dan pemulihan non-milestone dibatasi hingga $maxDrafts versi terakhir.
     *
     * @param  array<int, array<string, mixed>>  $revisions
     * @return array<int, array<string, mixed>>
     */
    public function pruneRevisions(array $revisions, int $maxDrafts = 30): array
    {
        $pruned = [];
        $draftCount = 0;

        foreach ($revisions as $rev) {
            $status = $rev['status'] ?? 'draft';

            if (in_array($status, ['published', 'rollback'], true)) {
                $pruned[] = $rev;

                continue;
            }

            if ($draftCount < $maxDrafts) {
                $pruned[] = $rev;
                $draftCount++;
            }
        }

        return $pruned;
    }

    /**
     * Buat snapshot revisi baru lengkap dengan Page Document dan legacy setting.
     *
     * @param  array<string, mixed>|null  $customDoc
     * @return array<string, mixed>
     */
    public function createRevision(string $summary, ?User $user = null, string $status = 'draft', ?array $customDoc = null): array
    {
        $revisions = $this->getRevisions();

        // Ambil snapshot seluruh kunci pengaturan landing
        $keys = PengaturanModel::where('kunci', 'LIKE', ($status === 'draft' || $status === 'restored' ? 'draft_landing_%' : 'landing_%'))->get();
        $snapshot = [];
        foreach ($keys as $k) {
            if (! str_starts_with($k->kunci, 'landing_draft_') && $k->kunci !== 'landing_live_backup' && $k->kunci !== 'landing_revisions') {
                $snapshot[$k->kunci] = $k->nilai;
            }
        }

        $docSnapshot = $customDoc;
        if ($docSnapshot === null) {
            try {
                $docSnapshot = app(LandingPageDocumentService::class)->getDocument(draft: ($status === 'draft' || $status === 'restored'));
            } catch (\Throwable $e) {
                $docSnapshot = null;
            }
        }

        $revId = 'rev-'.time().'-'.bin2hex(random_bytes(3));
        $waktu = CarbonImmutable::now('Asia/Jakarta')->translatedFormat('d M Y H:i:s').' WIB';
        $author = $user ? $user->name : 'Super Admin';
        $docVersion = $this->getDocumentVersion($status === 'draft' || $status === 'restored');

        $newRevision = [
            'id' => $revId,
            'document_id' => 'landing_page_document',
            'summary' => $summary,
            'author_id' => $user?->id,
            'author_name' => $author,
            'created_at' => $waktu,
            'timestamp' => CarbonImmutable::now('Asia/Jakarta')->timestamp,
            'status' => $status,
            'schema_version' => '1.1',
            'document_version' => $docVersion,
            'document' => $docSnapshot,
            'snapshot' => $snapshot,
        ];

        array_unshift($revisions, $newRevision);

        // Terapkan retensi revisi
        $revisions = $this->pruneRevisions($revisions, 30);

        Pengaturan::set('landing_revisions', json_encode($revisions, JSON_UNESCAPED_UNICODE));

        return $newRevision;
    }

    /**
     * Pulihkan snapshot revisi tertentu ke dalam kontainer draf aktif (tidak langsung menimpa live publik).
     */
    public function restoreRevision(string $revisionId, ?User $user = null): bool
    {
        $targetRev = $this->getRevisionById($revisionId);

        if (! $targetRev || (empty($targetRev['snapshot']) && empty($targetRev['document']))) {
            return false;
        }

        return DB::transaction(function () use ($targetRev, $user) {
            // Pulihkan snapshot settings ke draf
            if (! empty($targetRev['snapshot']) && is_array($targetRev['snapshot'])) {
                foreach ($targetRev['snapshot'] as $kunci => $nilai) {
                    if (in_array($kunci, ['draft_landing_document_version', 'landing_document_version'], true)) {
                        continue;
                    }
                    $draftKey = str_starts_with($kunci, 'draft_') ? $kunci : 'draft_'.$kunci;
                    Pengaturan::set($draftKey, (string) ($nilai ?? ''));
                }
            }

            // Pulihkan struktur Page Document jika ada
            if (! empty($targetRev['document']) && is_array($targetRev['document'])) {
                Pengaturan::setJson('draft_landing_page_document', $targetRev['document']);
                LegacyCmsAdapter::syncDocumentToLegacySettings($targetRev['document'], draft: true);
            }

            // Naikkan document_version draf
            $currVer = (int) Pengaturan::getLive('draft_landing_document_version', '1');
            $newVer = $currVer + 1;
            Pengaturan::set('draft_landing_document_version', (string) $newVer);

            $waktuSekarang = CarbonImmutable::now('Asia/Jakarta')->translatedFormat('d F Y H:i').' WIB';
            $authorName = $user ? $user->name : 'Super Admin';

            Pengaturan::set('landing_draft_active', '1');
            Pengaturan::set('landing_draft_updated_at', $waktuSekarang);
            Pengaturan::set('landing_draft_author_name', $authorName);

            Pengaturan::clearCache();

            // Catat revisi pemulihan baru
            $this->createRevision("Memulihkan draf dari revisi {$targetRev['id']}", $user, 'restored', $targetRev['document'] ?? null);

            $this->auditService->catat(
                tipe: TipeAuditEnum::Aksi,
                tabel: 'pengaturan',
                idData: 0,
                detail: [
                    'aksi' => "Memulihkan draf dari revisi {$targetRev['id']} ({$targetRev['summary']})",
                    'superadmin_id' => $user?->id,
                    'revision_id' => $targetRev['id'],
                    'document_version' => $newVer,
                ]
            );

            return true;
        });
    }

    /**
     * Memulihkan (rollback) versi live ke backup yang dibuat sebelum publikasi terakhir atau snapshot revisi spesifik.
     */
    public function rollbackToBackup(?User $user = null, ?string $backupRevisionId = null): int
    {
        return Cache::lock('landing_cms_rollback_lock', 10)->block(3, function () use ($user, $backupRevisionId) {
            $backupSettings = [];
            $backupDocument = null;

            if ($backupRevisionId !== null) {
                $targetRev = $this->getRevisionById($backupRevisionId);
                if (! $targetRev) {
                    return 0;
                }
                $backupSettings = $targetRev['snapshot'] ?? [];
                $backupDocument = $targetRev['document'] ?? null;
            } else {
                $backupJson = Pengaturan::getLive('landing_live_backup');
                if (! $backupJson) {
                    return 0;
                }

                $decoded = json_decode($backupJson, true);
                if (! is_array($decoded) || empty($decoded)) {
                    return 0;
                }

                if (isset($decoded['settings']) && is_array($decoded['settings'])) {
                    $backupSettings = $decoded['settings'];
                    $backupDocument = $decoded['document'] ?? null;
                } else {
                    $backupSettings = $decoded;
                }
            }

            if (empty($backupSettings) && empty($backupDocument)) {
                return 0;
            }

            return DB::transaction(function () use ($backupSettings, $backupDocument, $backupRevisionId, $user) {
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

                $summary = $backupRevisionId !== null
                    ? "Rollback versi live ke revisi {$backupRevisionId} ({$count} elemen)"
                    : "Rollback versi live ke backup ({$count} elemen)";

                // Catat revisi rollback
                $this->createRevision($summary, $user, 'rollback', $backupDocument);

                $this->auditService->catat(
                    tipe: TipeAuditEnum::Aksi,
                    tabel: 'pengaturan',
                    idData: 0,
                    detail: [
                        'aksi' => 'Melakukan rollback tampilan publik ke backup sebelum publikasi terakhir',
                        'superadmin_id' => $user?->id,
                        'jumlah_elemen' => $count,
                        'backup_revision_id' => $backupRevisionId,
                    ]
                );

                return $count;
            });
        });
    }

    /**
     * Dapatkan detail penggunaan berkas media di landing page (baik live maupun draf).
     *
     * @return array<int, array{location: string, type: string, scope: string, block_id?: string}>
     */
    public function getMediaUsage(string $pathOrFilename): array
    {
        $term = basename($pathOrFilename);
        if ($term === '') {
            return [];
        }

        $usages = [];

        // 1. Hero Image
        $heroLive = (string) Pengaturan::getLive('landing_hero_image');
        if ($heroLive !== '' && str_contains($heroLive, $term)) {
            $usages[] = [
                'location' => 'Hero Banner (Live Publik)',
                'type' => 'setting',
                'scope' => 'live',
            ];
        }
        $heroDraft = (string) Pengaturan::getLive('draft_landing_hero_image');
        if ($heroDraft !== '' && str_contains($heroDraft, $term)) {
            $usages[] = [
                'location' => 'Hero Banner (Draf Sandbox)',
                'type' => 'setting',
                'scope' => 'draft',
            ];
        }

        // 2. Panduan PDF
        $pdfLive = (string) Pengaturan::getLive('landing_panduan_pdf');
        if ($pdfLive !== '' && str_contains($pdfLive, $term)) {
            $usages[] = [
                'location' => 'Buku Panduan PDF (Live Publik)',
                'type' => 'setting',
                'scope' => 'live',
            ];
        }
        $pdfDraft = (string) Pengaturan::getLive('draft_landing_panduan_pdf');
        if ($pdfDraft !== '' && str_contains($pdfDraft, $term)) {
            $usages[] = [
                'location' => 'Buku Panduan PDF (Draf Sandbox)',
                'type' => 'setting',
                'scope' => 'draft',
            ];
        }

        // 3. Page Document & Block Instances (Live & Draft)
        try {
            $docService = app(LandingPageDocumentService::class);

            // Draft Document
            $draftDoc = $docService->getDocument(draft: true);
            if (isset($draftDoc['blocks']) && is_array($draftDoc['blocks'])) {
                foreach ($draftDoc['blocks'] as $block) {
                    $blockJson = json_encode($block);
                    if ($blockJson && str_contains($blockJson, $term)) {
                        $blockName = $block['label'] ?? $block['type'] ?? $block['id'];
                        $usages[] = [
                            'location' => "Blok {$blockName} ({$block['id']}) (Draf)",
                            'type' => 'block',
                            'scope' => 'draft',
                            'block_id' => $block['id'] ?? null,
                        ];
                    }
                }
            }

            // Live Document
            $liveDoc = $docService->getDocument(draft: false);
            if (isset($liveDoc['blocks']) && is_array($liveDoc['blocks'])) {
                foreach ($liveDoc['blocks'] as $block) {
                    $blockJson = json_encode($block);
                    if ($blockJson && str_contains($blockJson, $term)) {
                        $blockName = $block['label'] ?? $block['type'] ?? $block['id'];
                        $usages[] = [
                            'location' => "Blok {$blockName} ({$block['id']}) (Live)",
                            'type' => 'block',
                            'scope' => 'live',
                            'block_id' => $block['id'] ?? null,
                        ];
                    }
                }
            }
        } catch (\Throwable $e) {
            // Abaikan jika service belum terikat di test tertentu
        }

        // 4. Legacy Blocks Layout
        $layoutLive = (string) Pengaturan::getLive('landing_blocks_layout');
        if ($layoutLive !== '' && str_contains($layoutLive, $term)) {
            $already = false;
            foreach ($usages as $u) {
                if ($u['scope'] === 'live' && $u['type'] === 'block') {
                    $already = true;
                    break;
                }
            }
            if (! $already) {
                $usages[] = [
                    'location' => 'Blok Layout (Live Publik)',
                    'type' => 'layout',
                    'scope' => 'live',
                ];
            }
        }

        $layoutDraft = (string) Pengaturan::getLive('draft_landing_blocks_layout');
        if ($layoutDraft !== '' && str_contains($layoutDraft, $term)) {
            $already = false;
            foreach ($usages as $u) {
                if ($u['scope'] === 'draft' && $u['type'] === 'block') {
                    $already = true;
                    break;
                }
            }
            if (! $already) {
                $usages[] = [
                    'location' => 'Blok Layout (Draf Sandbox)',
                    'type' => 'layout',
                    'scope' => 'draft',
                ];
            }
        }

        return $usages;
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

        // 2. Pengecekan Gambar (JPEG, PNG, WEBP, SVG)
        if (in_array($clientExt, ['jpg', 'jpeg', 'png', 'webp', 'svg'], true) || str_starts_with($clientMime, 'image/')) {
            if ($clientExt === 'svg' || $clientMime === 'image/svg+xml') {
                $fullContent = @file_get_contents($path);
                if (! $fullContent || (! str_contains($fullContent, '<svg') && ! str_contains($fullContent, '<?xml'))) {
                    return [
                        'valid' => false,
                        'error' => 'Berkas SVG tidak valid atau tidak memiliki tag SVG/XML.',
                    ];
                }

                // Proteksi SVG XSS & XXE: Larang skrip, iframe, foreignObject, inline event handlers, dan entity doctype
                $dangerousPatterns = [
                    '/<script\b[^>]*>/is',
                    '/<iframe\b[^>]*>/is',
                    '/<foreignObject\b[^>]*>/is',
                    '/<!ENTITY/i',
                    '/on[a-zA-Z]+\s*=/i',
                    '/href\s*=\s*["\']\s*javascript:/i',
                ];

                foreach ($dangerousPatterns as $pattern) {
                    if (preg_match($pattern, $fullContent)) {
                        return [
                            'valid' => false,
                            'error' => 'Berkas SVG memuat skrip berbahaya atau elemen interaktif yang dilarang (SVG XSS Protection).',
                        ];
                    }
                }

                return ['valid' => true, 'error' => null];
            }

            $imgInfo = @getimagesize($path);
            if ($imgInfo === false) {
                return [
                    'valid' => false,
                    'error' => 'Berkas gambar rusak atau tidak valid.',
                ];
            }

            return ['valid' => true, 'error' => null];
        }

        return ['valid' => true, 'error' => null];
    }

    /**
     * Ambil data Media Library terdaftar (termasuk status penggunaan).
     *
     * @return array<int, array<string, mixed>>
     */
    public function getMediaLibrary(?string $type = null, ?string $search = null, bool $withUsage = true): array
    {
        $raw = Pengaturan::getLive('landing_media_library');
        $media = [];

        if ($raw) {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                $media = $decoded;
            }
        }

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

        // Filter pencarian nama/alt text/title/description
        if ($search) {
            $term = strtolower(trim($search));
            $media = array_values(array_filter($media, function ($item) use ($term) {
                $name = strtolower((string) ($item['filename'] ?? ''));
                $orig = strtolower((string) ($item['original_name'] ?? ''));
                $alt = strtolower((string) ($item['alt_text'] ?? ''));
                $title = strtolower((string) ($item['title'] ?? ''));
                $desc = strtolower((string) ($item['description'] ?? ''));

                return str_contains($name, $term)
                    || str_contains($orig, $term)
                    || str_contains($alt, $term)
                    || str_contains($title, $term)
                    || str_contains($desc, $term);
            }));
        }

        if ($withUsage) {
            foreach ($media as &$item) {
                $pathOrName = $item['path'] ?? $item['filename'] ?? '';
                $usages = $this->getMediaUsage($pathOrName);
                $item['usage_count'] = count($usages);
                $item['usage_locations'] = array_column($usages, 'location');
                $item['is_in_use'] = count($usages) > 0;
            }
            unset($item);
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
        $url = Storage::disk('public')->url($path);

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
     * Perbarui metadata media (alt text, caption, title, description).
     *
     * @param  array<string, mixed>  $data
     */
    public function updateMediaMetadata(string $id, array $data): bool
    {
        $list = $this->getMediaLibrary(withUsage: false);
        $updated = false;

        foreach ($list as &$item) {
            if ($item['id'] === $id) {
                if (array_key_exists('alt_text', $data)) {
                    $item['alt_text'] = (string) $data['alt_text'];
                }
                if (array_key_exists('caption', $data)) {
                    $item['caption'] = (string) $data['caption'];
                }
                if (array_key_exists('title', $data)) {
                    $item['title'] = (string) $data['title'];
                }
                if (array_key_exists('description', $data)) {
                    $item['description'] = (string) $data['description'];
                }
                $updated = true;
                break;
            }
        }
        unset($item);

        if ($updated) {
            Pengaturan::set('landing_media_library', json_encode($list, JSON_UNESCAPED_UNICODE));
        }

        return $updated;
    }

    /**
     * Periksa apakah suatu berkas media sedang digunakan di landing page atau blok.
     */
    public function isMediaInUse(string $pathOrFilename): bool
    {
        return count($this->getMediaUsage($pathOrFilename)) > 0;
    }

    /**
     * Mengganti berkas fisik media lama dengan berkas baru tanpa merusak referensi draf.
     *
     * @return array{success: bool, message: string, item?: array<string, mixed>}
     */
    public function replaceMedia(string $id, UploadedFile $newFile, ?User $user = null): array
    {
        $list = $this->getMediaLibrary(withUsage: false);
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

        $sigCheck = $this->validateFileSignature($newFile);
        if (! $sigCheck['valid']) {
            return ['success' => false, 'message' => $sigCheck['error'] ?? 'Berkas baru tidak valid.'];
        }

        $oldMime = $targetItem['mime_type'] ?? '';
        $newMime = $newFile->getMimeType() ?: 'application/octet-stream';

        // Validasi kesesuaian kategori: image ke image, pdf ke pdf
        $isOldImage = str_starts_with($oldMime, 'image/');
        $isNewImage = str_starts_with($newMime, 'image/');
        if ($isOldImage && ! $isNewImage) {
            return ['success' => false, 'message' => 'Berkas pengganti harus berupa gambar yang valid.'];
        }
        if ($oldMime === 'application/pdf' && $newMime !== 'application/pdf') {
            return ['success' => false, 'message' => 'Berkas pengganti harus berupa dokumen PDF yang valid.'];
        }

        // Ekstraksi dimensi baru jika gambar
        $dimensions = null;
        if ($isNewImage) {
            $info = @getimagesize($newFile->getRealPath());
            if ($info && isset($info[0], $info[1])) {
                $dimensions = "{$info[0]} x {$info[1]}";
            }
        }

        $oldPath = $targetItem['path'];
        $oldOriginalName = $targetItem['original_name'] ?? $targetItem['filename'];

        // Simpan berkas baru
        $newPath = $newFile->store('landing/media', 'public');
        $newUrl = Storage::disk('public')->url($newPath);

        $newOriginalName = preg_replace('/[^a-zA-Z0-9_\-\. ]/', '_', basename($newFile->getClientOriginalName()));
        $newSize = $newFile->getSize();

        // Perbarui item metadata
        $targetItem['filename'] = basename($newPath);
        $targetItem['original_name'] = $newOriginalName;
        $targetItem['path'] = $newPath;
        $targetItem['url'] = $newUrl;
        $targetItem['mime_type'] = $newMime;
        $targetItem['size_bytes'] = $newSize;
        $targetItem['size_formatted'] = round($newSize / 1024, 1).' KB';
        $targetItem['dimensions'] = $dimensions;
        $targetItem['updated_at'] = CarbonImmutable::now('Asia/Jakarta')->translatedFormat('d M Y H:i').' WIB';

        $list[$targetIndex] = $targetItem;
        Pengaturan::set('landing_media_library', json_encode($list, JSON_UNESCAPED_UNICODE));

        // Perbarui referensi di draf secara otomatis jika ada yang merujuk berkas lama
        $oldTerm = basename($oldPath);
        $newTerm = basename($newPath);

        // 1. draft_landing_hero_image
        $draftHero = (string) Pengaturan::getLive('draft_landing_hero_image');
        if (str_contains($draftHero, $oldTerm)) {
            Pengaturan::set('draft_landing_hero_image', str_replace($oldTerm, $newTerm, $draftHero));
        }

        // 2. draft_landing_panduan_pdf
        $draftPdf = (string) Pengaturan::getLive('draft_landing_panduan_pdf');
        if (str_contains($draftPdf, $oldTerm)) {
            Pengaturan::set('draft_landing_panduan_pdf', str_replace($oldTerm, $newTerm, $draftPdf));
        }

        // 3. draft_landing_page_document
        try {
            $docService = app(LandingPageDocumentService::class);
            $draftDoc = $docService->getDocument(draft: true);
            $draftDocJson = json_encode($draftDoc);
            if ($draftDocJson && str_contains($draftDocJson, $oldTerm)) {
                $updatedDocJson = str_replace($oldTerm, $newTerm, $draftDocJson);
                $updatedDoc = json_decode($updatedDocJson, true);
                if (is_array($updatedDoc)) {
                    $docService->saveDraftDocument($updatedDoc, user: $user);
                }
            }
        } catch (\Throwable $e) {
            // Lanjutkan jika dokumen draf tidak tersedia
        }

        // 4. draft_landing_blocks_layout
        $draftLayout = (string) Pengaturan::getLive('draft_landing_blocks_layout');
        if ($draftLayout !== '' && str_contains($draftLayout, $oldTerm)) {
            Pengaturan::set('draft_landing_blocks_layout', str_replace($oldTerm, $newTerm, $draftLayout));
        }

        // Bersihkan berkas fisik lama HANYA jika berkas lama tidak lagi digunakan di versi live
        $liveHero = (string) Pengaturan::getLive('landing_hero_image');
        $livePdf = (string) Pengaturan::getLive('landing_panduan_pdf');
        $liveLayout = (string) Pengaturan::getLive('landing_blocks_layout');
        $isUsedInLiveDoc = false;
        try {
            $liveDoc = app(LandingPageDocumentService::class)->getDocument(draft: false);
            $liveDocJson = json_encode($liveDoc) ?: '';
            $isUsedInLiveDoc = str_contains($liveDocJson, $oldTerm);
        } catch (\Throwable $e) {
            // Lanjutkan
        }

        $isStillUsedInLive = str_contains($liveHero, $oldTerm)
            || str_contains($livePdf, $oldTerm)
            || $isUsedInLiveDoc
            || str_contains($liveLayout, $oldTerm);

        if (! $isStillUsedInLive && Storage::disk('public')->exists($oldPath)) {
            Storage::disk('public')->delete($oldPath);
        }

        $this->auditService->catat(
            tipe: TipeAuditEnum::Aksi,
            tabel: 'pengaturan',
            idData: 0,
            detail: [
                'aksi' => "Mengganti berkas media {$oldOriginalName} dengan {$newOriginalName}",
                'superadmin_id' => $user?->id,
                'media_id' => $id,
                'old_path' => $oldPath,
                'new_path' => $newPath,
            ]
        );

        $usages = $this->getMediaUsage($targetItem['path']);
        $targetItem['usage_count'] = count($usages);
        $targetItem['usage_locations'] = array_column($usages, 'location');
        $targetItem['is_in_use'] = count($usages) > 0;

        return [
            'success' => true,
            'message' => 'Berkas media berhasil diganti.',
            'item' => $targetItem,
        ];
    }

    /**
     * Hapus berkas dari Media Library dengan proteksi file in-use.
     *
     * @return array{success: bool, message: string, usage_locations?: array<int, string>}
     */
    public function deleteMedia(string $id, ?User $user = null): array
    {
        $list = $this->getMediaLibrary(withUsage: false);
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

        // Cek apakah berkas sedang digunakan
        $usages = $this->getMediaUsage($targetItem['path']);
        if (count($usages) > 0) {
            $locations = array_column($usages, 'location');
            $detail = implode(', ', array_slice($locations, 0, 3));
            if (count($locations) > 3) {
                $detail .= ' dan '.(count($locations) - 3).' lokasi lainnya';
            }

            return [
                'success' => false,
                'message' => "Gagal menghapus: berkas ini sedang digunakan pada: {$detail}.",
                'usage_locations' => $locations,
            ];
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

            // Catat revisi snapshot secara otomatis
            $this->createRevision('Pembaruan draf via WordPress Site Editor', $user, 'draft');

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
