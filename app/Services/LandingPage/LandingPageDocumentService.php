<?php

declare(strict_types=1);

namespace App\Services\LandingPage;

use App\Enums\TipeAuditEnum;
use App\Models\Pengaturan as PengaturanModel;
use App\Models\User;
use App\Services\AuditService;
use App\Support\Pengaturan;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Service pusat pengelolaan Page Document Landing Page MAGKOM.
 * Mengelola siklus hidup dokumen (Live, Draft, Validasi, Normalisasi, Staging, dan Adaptasi Legacy).
 */
class LandingPageDocumentService
{
    public function __construct(
        protected BlockSchemaValidator $validator,
        protected LegacyCmsAdapter $legacyAdapter,
        protected AuditService $auditService
    ) {}

    /**
     * Mengambil dokumen live versi produksi.
     * Jika belum ada di database, otomatis melakukan fallback ke konversi data legacy.
     *
     * @return array<string, mixed>
     */
    public function getLiveDocument(): array
    {
        $raw = PengaturanModel::getNilai('landing_page_document');
        if ($raw !== null && $raw !== '') {
            $decoded = json_decode($raw, true);
            if (is_array($decoded) && isset($decoded['blocks'])) {
                try {
                    return $this->validator->validateDocument($decoded);
                } catch (ValidationException) {
                    // Jika data JSON di DB korup, fallback ke konversi legacy
                }
            }
        }

        // Fallback aman ke legacy adapter
        return $this->legacyAdapter->convertLegacyToDocument(draft: false);
    }

    /**
     * Mengambil dokumen draf staging.
     * Jika draf aktif, ambil dari draft_landing_page_document; jika tidak, kembalikan versi live.
     *
     * @return array<string, mixed>
     */
    public function getDraftDocument(): array
    {
        if (! $this->hasActiveDraft()) {
            return $this->getLiveDocument();
        }

        $raw = PengaturanModel::getNilai('draft_landing_page_document');
        if ($raw !== null && $raw !== '') {
            $decoded = json_decode($raw, true);
            if (is_array($decoded) && isset($decoded['blocks'])) {
                try {
                    return $this->validator->validateDocument($decoded);
                } catch (ValidationException) {
                    // Fallback
                }
            }
        }

        // Jika ada draf aktif di legacy keys tapi belum ada draft_landing_page_document
        return $this->legacyAdapter->convertLegacyToDocument(draft: true);
    }

    /**
     * Mengambil dokumen berdasarkan status pratinjau (draft atau live).
     *
     * @return array<string, mixed>
     */
    public function getDocument(bool $draft = false): array
    {
        return $draft ? $this->getDraftDocument() : $this->getLiveDocument();
    }

    /**
     * Memeriksa apakah terdapat draf perubahan yang sedang aktif.
     */
    public function hasActiveDraft(): bool
    {
        $activeFlag = Pengaturan::getLive('landing_draft_active');
        if ($activeFlag === '1') {
            return true;
        }

        return PengaturanModel::where('kunci', 'LIKE', 'draft_%')->exists();
    }

    /**
     * Menyimpan Page Document ke dalam kontainer draf (draft_landing_page_document).
     * Secara otomatis menyinkronkan legacy keys agar Form Builder tetap konsisten.
     *
     * @param  array<string, mixed>  $document
     * @return array<string, mixed> Dokumen yang telah divalidasi dan dinormalisasi
     *
     * @throws ValidationException
     */
    public function saveDraftDocument(array $document, ?User $user = null): array
    {
        $cleanDoc = $this->validator->validateDocument($document);

        DB::transaction(function () use ($cleanDoc, $user) {
            // 1. Simpan dokumen draf utama
            Pengaturan::setJson('draft_landing_page_document', $cleanDoc);

            // 2. Simpan juga draft_landing_blocks_layout untuk kompatibilitas frontend inspector
            $layout = [];
            foreach ($cleanDoc['blocks'] as $b) {
                $layout[] = [
                    'id' => $b['id'],
                    'type' => $b['type'],
                    'title' => BlockRegistry::get($b['type'])?->label ?? ucfirst($b['type']),
                    'visible' => $b['visible'],
                    'order' => $b['order'],
                    'content' => $b['content'],
                    'styles' => $b['styles'],
                ];
            }
            Pengaturan::setJson('draft_landing_blocks_layout', $layout);

            // 3. Sinkronisasi legacy keys dua arah
            $this->legacyAdapter->syncLegacyKeysFromDocument($cleanDoc, draft: true);

            // 4. Update status draf
            $waktuSekarang = CarbonImmutable::now('Asia/Jakarta')->translatedFormat('d F Y H:i').' WIB';
            $authorName = $user ? $user->name : 'Super Admin';

            Pengaturan::set('landing_draft_active', '1');
            Pengaturan::set('landing_draft_updated_at', $waktuSekarang);
            Pengaturan::set('landing_draft_author_name', $authorName);

            Pengaturan::clearCache();

            // 5. Catat audit trail
            $this->auditService->catat(
                tipe: TipeAuditEnum::Aksi,
                tabel: 'pengaturan',
                idData: 0,
                detail: [
                    'aksi' => 'Menyimpan draf Page Document Landing Page baru',
                    'superadmin_id' => $user?->id,
                    'total_blocks' => count($cleanDoc['blocks']),
                ]
            );
        });

        return $cleanDoc;
    }

    /**
     * Mempublikasikan Page Document draf ke live produksi.
     */
    public function publishDocument(?User $user = null): int
    {
        return DB::transaction(function () use ($user) {
            $draftDoc = $this->getDraftDocument();

            // Backup live saat ini
            $currentLiveDoc = PengaturanModel::getNilai('landing_page_document');
            if ($currentLiveDoc !== null) {
                Pengaturan::set('landing_page_document_backup', $currentLiveDoc);
            }

            // Promosikan ke live
            Pengaturan::setJson('landing_page_document', $draftDoc);

            // Sinkronisasi layout live
            $layout = [];
            foreach ($draftDoc['blocks'] as $b) {
                $layout[] = [
                    'id' => $b['id'],
                    'type' => $b['type'],
                    'title' => BlockRegistry::get($b['type'])?->label ?? ucfirst($b['type']),
                    'visible' => $b['visible'],
                    'order' => $b['order'],
                    'content' => $b['content'],
                    'styles' => $b['styles'],
                ];
            }
            Pengaturan::setJson('landing_blocks_layout', $layout);

            // Sinkronisasi legacy live keys
            $this->legacyAdapter->syncLegacyKeysFromDocument($draftDoc, draft: false);

            // Bersihkan seluruh draf
            PengaturanModel::where('kunci', 'LIKE', 'draft_%')->delete();
            Pengaturan::set('landing_draft_active', '0');
            Pengaturan::delete('landing_draft_updated_at');
            Pengaturan::delete('landing_draft_author_name');

            Pengaturan::clearCache();

            $this->auditService->catat(
                tipe: TipeAuditEnum::Aksi,
                tabel: 'pengaturan',
                idData: 0,
                detail: [
                    'aksi' => 'Mempublikasikan Page Document ke live publik',
                    'superadmin_id' => $user?->id,
                    'total_blocks' => count($draftDoc['blocks']),
                ]
            );

            return count($draftDoc['blocks']);
        });
    }

    /**
     * Membuang draf Page Document dan mengembalikan ke versi live.
     */
    public function discardDraftDocument(?User $user = null): int
    {
        return DB::transaction(function () use ($user) {
            PengaturanModel::where('kunci', 'LIKE', 'draft_%')->delete();
            Pengaturan::set('landing_draft_active', '0');
            Pengaturan::delete('landing_draft_updated_at');
            Pengaturan::delete('landing_draft_author_name');

            Pengaturan::clearCache();

            $this->auditService->catat(
                tipe: TipeAuditEnum::Aksi,
                tabel: 'pengaturan',
                idData: 0,
                detail: [
                    'aksi' => 'Membuang draf Page Document',
                    'superadmin_id' => $user?->id,
                ]
            );

            return 1;
        });
    }

    /**
     * Menghasilkan dokumen standar bawaan.
     *
     * @return array<string, mixed>
     */
    public function createDefaultDocument(): array
    {
        return BlockRegistry::getDefaultDocument();
    }

    /**
     * Mengambil satu blok spesifik berdasarkan ID unik.
     *
     * @param  array<string, mixed>  $document
     * @return array<string, mixed>|null
     */
    public function getBlockById(array $document, string $id): ?array
    {
        foreach ($document['blocks'] ?? [] as $block) {
            if (($block['id'] ?? '') === $id) {
                return $block;
            }
        }

        return null;
    }

    /**
     * Mengambil seluruh blok terurut dari sebuah dokumen.
     *
     * @param  array<string, mixed>  $document
     * @return array<int, array<string, mixed>>
     */
    public function getBlocks(array $document): array
    {
        $blocks = $document['blocks'] ?? [];
        usort($blocks, fn ($a, $b) => ($a['order'] ?? 0) <=> ($b['order'] ?? 0));

        return $blocks;
    }

    /**
     * Mengambil seluruh blok dengan tipe tertentu.
     *
     * @param  array<string, mixed>  $document
     * @return array<int, array<string, mixed>>
     */
    public function getBlocksByType(array $document, string $type): array
    {
        $matches = [];
        foreach ($document['blocks'] ?? [] as $block) {
            if (($block['type'] ?? '') === $type) {
                $matches[] = $block;
            }
        }

        return $matches;
    }

    /**
     * Menormalisasi dan memvalidasi dokumen raw.
     *
     * @param  array<string, mixed>  $rawDocument
     * @return array<string, mixed>
     *
     * @throws ValidationException
     */
    public function normalizeDocument(array $rawDocument): array
    {
        return $this->validator->validateDocument($rawDocument);
    }

    /**
     * Menambahkan blok baru ke dalam dokumen draf.
     *
     * @param  string  $type  Tipe blok resmi dari BlockRegistry
     * @param  int|null  $targetOrder  Urutan target untuk disisipkan (opsional)
     * @param  array<string, mixed>  $customContent  Konten kustom (opsional)
     * @param  array<string, mixed>  $customStyles  Gaya kustom (opsional)
     * @return array{block: array<string, mixed>, document: array<string, mixed>}
     *
     * @throws ValidationException
     */
    public function addBlock(string $type, ?int $targetOrder = null, array $customContent = [], array $customStyles = [], ?User $user = null): array
    {
        if (! BlockRegistry::has($type)) {
            throw ValidationException::withMessages([
                'type' => "Tipe blok '{$type}' tidak terdaftar pada Block Registry resmi.",
            ]);
        }

        $def = BlockRegistry::get($type);
        $doc = $this->getDraftDocument();

        // Periksa batasan non-repeatable (misal hero dan footer)
        if (! $def->repeatable && count($this->getBlocksByType($doc, $type)) > 0) {
            throw ValidationException::withMessages([
                'type' => "Blok bertipe '{$def->label}' bersifat unik dan sudah ada di halaman.",
            ]);
        }

        $newId = 'block-'.$type.'-'.bin2hex(random_bytes(4));
        $newBlock = [
            'id' => $newId,
            'type' => $type,
            'version' => $def->schemaVersion,
            'visible' => true,
            'locked' => $def->locked,
            'user_locked' => false,
            'order' => 1,
            'content' => array_merge($def->defaultContent, $customContent),
            'styles' => array_merge($def->defaultStyles, $customStyles),
            'settings' => [],
        ];

        $blocks = $doc['blocks'] ?? [];

        if ($targetOrder !== null && $targetOrder >= 1) {
            $insertIdx = min(count($blocks), max(0, $targetOrder));
            array_splice($blocks, $insertIdx, 0, [$newBlock]);
        } else {
            $footerIdx = -1;
            foreach ($blocks as $idx => $b) {
                if (($b['type'] ?? '') === 'footer') {
                    $footerIdx = $idx;
                    break;
                }
            }

            if ($footerIdx !== -1) {
                array_splice($blocks, $footerIdx, 0, [$newBlock]);
            } else {
                $blocks[] = $newBlock;
            }
        }

        foreach ($blocks as $idx => &$b) {
            $b['order'] = $idx + 1;
        }
        unset($b);

        $doc['blocks'] = $blocks;
        $savedDoc = $this->saveDraftDocument($doc, $user);
        $savedBlock = $this->getBlockById($savedDoc, $newId) ?? $newBlock;

        return [
            'block' => $savedBlock,
            'document' => $savedDoc,
        ];
    }

    /**
     * Menduplikasi blok yang ada di dokumen draf.
     *
     * @param  string  $blockId  ID blok sumber
     * @return array{block: array<string, mixed>, document: array<string, mixed>}
     *
     * @throws ValidationException
     */
    public function duplicateBlock(string $blockId, ?User $user = null): array
    {
        $doc = $this->getDraftDocument();
        $blocks = $doc['blocks'] ?? [];

        $sourceIndex = -1;
        $sourceBlock = null;
        foreach ($blocks as $idx => $b) {
            if (($b['id'] ?? '') === $blockId) {
                $sourceIndex = $idx;
                $sourceBlock = $b;
                break;
            }
        }

        if ($sourceIndex === -1 || $sourceBlock === null) {
            throw ValidationException::withMessages([
                'id' => "Blok dengan ID '{$blockId}' tidak ditemukan pada dokumen.",
            ]);
        }

        $type = (string) ($sourceBlock['type'] ?? '');
        $def = BlockRegistry::get($type);

        if ($def && ! $def->repeatable) {
            throw ValidationException::withMessages([
                'block' => "Blok bertipe '{$def->label}' bersifat unik dan tidak dapat diduplikasi.",
            ]);
        }

        $cloneId = 'block-'.$type.'-'.bin2hex(random_bytes(4));
        $cloneBlock = $sourceBlock;
        $cloneBlock['id'] = $cloneId;
        $cloneBlock['locked'] = $def ? $def->locked : false;
        $cloneBlock['user_locked'] = false;

        array_splice($blocks, $sourceIndex + 1, 0, [$cloneBlock]);

        foreach ($blocks as $idx => &$b) {
            $b['order'] = $idx + 1;
        }
        unset($b);

        $doc['blocks'] = $blocks;
        $savedDoc = $this->saveDraftDocument($doc, $user);
        $savedBlock = $this->getBlockById($savedDoc, $cloneId) ?? $cloneBlock;

        return [
            'block' => $savedBlock,
            'document' => $savedDoc,
        ];
    }

    /**
     * Menghapus blok dari dokumen draf dengan proteksi system locked dan user locked.
     *
     * @param  string  $blockId  ID blok yang akan dihapus
     * @return array{deleted_id: string, document: array<string, mixed>}
     *
     * @throws ValidationException
     */
    public function deleteBlock(string $blockId, ?User $user = null): array
    {
        $doc = $this->getDraftDocument();
        $blocks = $doc['blocks'] ?? [];

        $targetIndex = -1;
        $targetBlock = null;
        foreach ($blocks as $idx => $b) {
            if (($b['id'] ?? '') === $blockId) {
                $targetIndex = $idx;
                $targetBlock = $b;
                break;
            }
        }

        if ($targetIndex === -1 || $targetBlock === null) {
            throw ValidationException::withMessages([
                'id' => "Blok dengan ID '{$blockId}' tidak ditemukan.",
            ]);
        }

        $type = (string) ($targetBlock['type'] ?? '');
        $def = BlockRegistry::get($type);

        // 1. Proteksi System Locked
        if (($def && $def->locked) || ! empty($targetBlock['locked'])) {
            throw ValidationException::withMessages([
                'block' => "Blok '{$def?->label}' adalah bagian wajib halaman (System Locked) dan tidak boleh dihapus.",
            ]);
        }

        // 2. Proteksi User Locked
        if (! empty($targetBlock['user_locked'])) {
            throw ValidationException::withMessages([
                'block' => "Blok '{$def?->label}' saat ini terkunci (User Locked). Buka kunci terlebih dahulu sebelum menghapus.",
            ]);
        }

        array_splice($blocks, $targetIndex, 1);

        foreach ($blocks as $idx => &$b) {
            $b['order'] = $idx + 1;
        }
        unset($b);

        $doc['blocks'] = $blocks;
        $savedDoc = $this->saveDraftDocument($doc, $user);

        return [
            'deleted_id' => $blockId,
            'document' => $savedDoc,
        ];
    }

    /**
     * Menyusun ulang urutan blok dalam dokumen draf berdasarkan array ID terurut.
     *
     * @param  array<int, string>  $orderedBlockIds  Daftar seluruh ID blok dalam urutan baru
     * @return array{document: array<string, mixed>}
     *
     * @throws ValidationException
     */
    public function reorderBlocks(array $orderedBlockIds, ?User $user = null): array
    {
        $doc = $this->getDraftDocument();
        $blocks = $doc['blocks'] ?? [];

        $blockMap = [];
        $originalIndexMap = [];
        foreach ($blocks as $idx => $b) {
            $id = (string) ($b['id'] ?? '');
            $blockMap[$id] = $b;
            $originalIndexMap[$id] = $idx;
        }

        // Validasi seluruh ID yang dikirim
        foreach ($orderedBlockIds as $newIdx => $id) {
            if (! isset($blockMap[$id])) {
                throw ValidationException::withMessages([
                    'order' => "ID blok '{$id}' tidak ditemukan dalam dokumen.",
                ]);
            }

            // Proteksi User Locked: blok terkunci tidak boleh dipindahkan posisinya
            $b = $blockMap[$id];
            if (! empty($b['user_locked']) && $originalIndexMap[$id] !== $newIdx) {
                $label = BlockRegistry::get($b['type'])?->label ?? $b['type'];
                throw ValidationException::withMessages([
                    'order' => "Blok '{$label}' sedang terkunci (User Locked) dan posisinya tidak dapat dipindahkan.",
                ]);
            }
        }

        if (count($orderedBlockIds) !== count($blocks)) {
            throw ValidationException::withMessages([
                'order' => 'Jumlah blok dalam permintaan penyusunan ulang tidak sesuai dengan jumlah blok dokumen.',
            ]);
        }

        $newBlocks = [];
        foreach ($orderedBlockIds as $idx => $id) {
            $b = $blockMap[$id];
            $b['order'] = $idx + 1;
            $newBlocks[] = $b;
        }

        $doc['blocks'] = $newBlocks;
        $savedDoc = $this->saveDraftDocument($doc, $user);

        return [
            'document' => $savedDoc,
        ];
    }

    /**
     * Menggeser posisi satu blok ke atas atau ke bawah.
     *
     * @param  string  $direction  'up' | 'down'
     * @return array{document: array<string, mixed>}
     *
     * @throws ValidationException
     */
    public function moveBlock(string $blockId, string $direction, ?User $user = null): array
    {
        $doc = $this->getDraftDocument();
        $blocks = $doc['blocks'] ?? [];

        $currentIndex = -1;
        foreach ($blocks as $idx => $b) {
            if (($b['id'] ?? '') === $blockId) {
                $currentIndex = $idx;
                break;
            }
        }

        if ($currentIndex === -1) {
            throw ValidationException::withMessages([
                'id' => "Blok dengan ID '{$blockId}' tidak ditemukan.",
            ]);
        }

        $targetIndex = $direction === 'up' ? $currentIndex - 1 : $currentIndex + 1;
        if ($targetIndex < 0 || $targetIndex >= count($blocks)) {
            return ['document' => $doc];
        }

        if (! empty($blocks[$currentIndex]['user_locked'])) {
            throw ValidationException::withMessages([
                'block' => 'Blok ini sedang terkunci dan tidak dapat dipindahkan posisinya.',
            ]);
        }

        $temp = $blocks[$currentIndex];
        $blocks[$currentIndex] = $blocks[$targetIndex];
        $blocks[$targetIndex] = $temp;

        foreach ($blocks as $idx => &$b) {
            $b['order'] = $idx + 1;
        }
        unset($b);

        $doc['blocks'] = $blocks;
        $savedDoc = $this->saveDraftDocument($doc, $user);

        return [
            'document' => $savedDoc,
        ];
    }

    /**
     * Mengubah visibilitas blok (Hide / Show) pada dokumen draf.
     *
     * @return array{block: array<string, mixed>, document: array<string, mixed>}
     *
     * @throws ValidationException
     */
    public function toggleBlockVisibility(string $blockId, ?bool $visible = null, ?User $user = null): array
    {
        $doc = $this->getDraftDocument();
        $blocks = $doc['blocks'] ?? [];

        $targetBlock = null;
        foreach ($blocks as &$b) {
            if (($b['id'] ?? '') === $blockId) {
                $b['visible'] = $visible !== null ? $visible : ! ($b['visible'] ?? true);
                $targetBlock = $b;
                break;
            }
        }
        unset($b);

        if ($targetBlock === null) {
            throw ValidationException::withMessages([
                'id' => "Blok dengan ID '{$blockId}' tidak ditemukan.",
            ]);
        }

        $doc['blocks'] = $blocks;
        $savedDoc = $this->saveDraftDocument($doc, $user);
        $updatedBlock = $this->getBlockById($savedDoc, $blockId) ?? $targetBlock;

        return [
            'block' => $updatedBlock,
            'document' => $savedDoc,
        ];
    }

    /**
     * Mengubah status penguncian blok (Lock / Unlock).
     *
     * @return array{block: array<string, mixed>, document: array<string, mixed>}
     *
     * @throws ValidationException
     */
    public function toggleBlockLock(string $blockId, ?bool $locked = null, ?User $user = null): array
    {
        $doc = $this->getDraftDocument();
        $blocks = $doc['blocks'] ?? [];

        $targetBlock = null;
        foreach ($blocks as &$b) {
            if (($b['id'] ?? '') === $blockId) {
                $type = (string) ($b['type'] ?? '');
                $def = BlockRegistry::get($type);

                // Blok sistem wajib (hero, footer) tidak boleh diubah status kuncinya
                if (($def && $def->locked) || ! empty($b['locked'])) {
                    throw ValidationException::withMessages([
                        'block' => "Blok '{$def?->label}' adalah blok sistem wajib dan status pengunciannya bersifat permanen.",
                    ]);
                }

                $b['user_locked'] = $locked !== null ? $locked : ! (! empty($b['user_locked']));
                $targetBlock = $b;
                break;
            }
        }
        unset($b);

        if ($targetBlock === null) {
            throw ValidationException::withMessages([
                'id' => "Blok dengan ID '{$blockId}' tidak ditemukan.",
            ]);
        }

        $doc['blocks'] = $blocks;
        $savedDoc = $this->saveDraftDocument($doc, $user);
        $updatedBlock = $this->getBlockById($savedDoc, $blockId) ?? $targetBlock;

        return [
            'block' => $updatedBlock,
            'document' => $savedDoc,
        ];
    }

    /**
     * Menyisipkan Pola Desain (Pattern) ke dalam dokumen draf.
     * Setiap blok di dalam pola diinstansiasi menjadi Block Instance independen dengan ID unik baru.
     *
     * @return array{inserted_blocks: array<int, array<string, mixed>>, document: array<string, mixed>}
     *
     * @throws ValidationException
     */
    public function insertPattern(string $patternKeyOrId, ?int $targetOrder = null, ?User $user = null): array
    {
        $newBlocks = PatternRegistry::instantiateBlocks($patternKeyOrId);

        if (empty($newBlocks)) {
            throw ValidationException::withMessages([
                'pattern' => "Pola '{$patternKeyOrId}' tidak memiliki blok yang dapat disisipkan.",
            ]);
        }

        $doc = $this->getDraftDocument();
        $blocks = $doc['blocks'] ?? [];

        // Tentukan indeks penyisipan
        if ($targetOrder !== null && $targetOrder >= 1) {
            $insertIdx = min(count($blocks), max(0, $targetOrder - 1));
            array_splice($blocks, $insertIdx, 0, $newBlocks);
        } else {
            // Sisipkan tepat sebelum footer jika ada
            $footerIdx = -1;
            foreach ($blocks as $idx => $b) {
                if (($b['type'] ?? '') === 'footer') {
                    $footerIdx = $idx;
                    break;
                }
            }

            if ($footerIdx !== -1) {
                array_splice($blocks, $footerIdx, 0, $newBlocks);
            } else {
                foreach ($newBlocks as $nb) {
                    $blocks[] = $nb;
                }
            }
        }

        // Susun ulang indeks order secara berurutan
        foreach ($blocks as $idx => &$b) {
            $b['order'] = $idx + 1;
        }
        unset($b);

        $doc['blocks'] = $blocks;
        $savedDoc = $this->saveDraftDocument($doc, $user);

        return [
            'inserted_blocks' => $newBlocks,
            'document' => $savedDoc,
        ];
    }

    /**
     * Menyisipkan Reusable Block ke dalam dokumen draf.
     *
     * @return array{block: array<string, mixed>, document: array<string, mixed>}
     *
     * @throws ValidationException
     */
    public function insertReusableBlock(string $reusableId, ?int $targetOrder = null, ?User $user = null): array
    {
        $reusableService = app(ReusableBlockService::class);
        $reusableDef = $reusableService->getById($reusableId);

        if (! $reusableDef) {
            throw ValidationException::withMessages([
                'reusable_id' => "Reusable Block '{$reusableId}' tidak ditemukan.",
            ]);
        }

        $type = (string) ($reusableDef['block_type'] ?? 'cta_banner');
        $def = BlockRegistry::get($type);

        $newId = 'block-reusable-'.bin2hex(random_bytes(4));
        $newBlock = [
            'id' => $newId,
            'type' => $type,
            'reusable_id' => $reusableId,
            'title' => $reusableDef['title'] ?? ($def ? $def->label : ucfirst($type)),
            'version' => $def ? $def->schemaVersion : 1,
            'visible' => true,
            'locked' => false,
            'user_locked' => false,
            'order' => 1,
            'content' => $reusableDef['content'] ?? ($def ? $def->defaultContent : []),
            'styles' => $reusableDef['styles'] ?? ($def ? $def->defaultStyles : []),
            'settings' => [],
        ];

        $doc = $this->getDraftDocument();
        $blocks = $doc['blocks'] ?? [];

        if ($targetOrder !== null && $targetOrder >= 1) {
            $insertIdx = min(count($blocks), max(0, $targetOrder - 1));
            array_splice($blocks, $insertIdx, 0, [$newBlock]);
        } else {
            $footerIdx = -1;
            foreach ($blocks as $idx => $b) {
                if (($b['type'] ?? '') === 'footer') {
                    $footerIdx = $idx;
                    break;
                }
            }

            if ($footerIdx !== -1) {
                array_splice($blocks, $footerIdx, 0, [$newBlock]);
            } else {
                $blocks[] = $newBlock;
            }
        }

        foreach ($blocks as $idx => &$b) {
            $b['order'] = $idx + 1;
        }
        unset($b);

        $doc['blocks'] = $blocks;
        $savedDoc = $this->saveDraftDocument($doc, $user);
        $savedBlock = $this->getBlockById($savedDoc, $newId) ?? $newBlock;

        return [
            'block' => $savedBlock,
            'document' => $savedDoc,
        ];
    }

    /**
     * Mengonversi instance Reusable Block pada dokumen menjadi blok reguler mandiri (Detach).
     *
     * @return array{block: array<string, mixed>, document: array<string, mixed>}
     *
     * @throws ValidationException
     */
    public function convertReusableToRegularBlock(string $blockId, ?User $user = null): array
    {
        $doc = $this->getDraftDocument();
        $blocks = $doc['blocks'] ?? [];

        $targetBlock = null;
        foreach ($blocks as &$b) {
            if (($b['id'] ?? '') === $blockId) {
                if (empty($b['reusable_id'])) {
                    throw ValidationException::withMessages([
                        'block' => "Blok dengan ID '{$blockId}' bukan merupakan Reusable Block.",
                    ]);
                }

                $reusableService = app(ReusableBlockService::class);
                $reusableDef = $reusableService->getById((string) $b['reusable_id']);

                if ($reusableDef) {
                    $b['content'] = $reusableDef['content'] ?? ($b['content'] ?? []);
                    $b['styles'] = $reusableDef['styles'] ?? ($b['styles'] ?? []);
                }

                unset($b['reusable_id']);
                $targetBlock = $b;
                break;
            }
        }
        unset($b);

        if ($targetBlock === null) {
            throw ValidationException::withMessages([
                'id' => "Blok dengan ID '{$blockId}' tidak ditemukan.",
            ]);
        }

        $doc['blocks'] = $blocks;
        $savedDoc = $this->saveDraftDocument($doc, $user);
        $savedBlock = $this->getBlockById($savedDoc, $blockId) ?? $targetBlock;

        return [
            'block' => $savedBlock,
            'document' => $savedDoc,
        ];
    }
}
