<?php

declare(strict_types=1);

namespace App\Services\LandingPage;

use App\Enums\TipeAuditEnum;
use App\Models\User;
use App\Services\AuditService;
use App\Support\Pengaturan;
use Carbon\CarbonImmutable;
use Illuminate\Validation\ValidationException;

/**
 * Service pusat pengelolaan Reusable Blocks (Blok Tersinkronisasi / Shared Blocks) Landing Page MAGKOM.
 * Memungkinkan satu blok didefinisikan secara terpusat dan seluruh perubahannya otomatis tersinkron ke semua instance.
 */
class ReusableBlockService
{
    public function __construct(
        protected AuditService $auditService
    ) {}

    /**
     * Mengambil seluruh definisi Reusable Blocks terdaftar.
     *
     * @param  bool  $withUsage  Apakah menyertakan informasi jumlah pemakaian (usage tracking)
     * @return array<int, array<string, mixed>>
     */
    public function getAll(bool $withUsage = true): array
    {
        $raw = Pengaturan::getLive('landing_reusable_blocks');
        $blocks = [];

        if ($raw) {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                $blocks = $decoded;
            }
        }

        if ($withUsage) {
            foreach ($blocks as &$item) {
                $id = (string) ($item['id'] ?? '');
                $usages = $this->getUsage($id);
                $item['usage_count'] = count($usages);
                $item['usages'] = $usages;
                $item['is_in_use'] = count($usages) > 0;
            }
            unset($item);
        }

        return array_values($blocks);
    }

    /**
     * Mengambil satu definisi Reusable Block berdasarkan ID.
     *
     * @return array<string, mixed>|null
     */
    public function getById(string $id): ?array
    {
        $all = $this->getAll(withUsage: false);

        foreach ($all as $item) {
            if (($item['id'] ?? '') === $id) {
                return $item;
            }
        }

        return null;
    }

    /**
     * Membuat definisi Reusable Block baru.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     *
     * @throws ValidationException
     */
    public function create(array $data, ?User $user = null): array
    {
        $title = trim((string) ($data['title'] ?? ''));
        $blockType = trim((string) ($data['block_type'] ?? ''));
        $category = trim((string) ($data['category'] ?? 'Umum'));
        $content = is_array($data['content'] ?? null) ? $data['content'] : [];
        $styles = is_array($data['styles'] ?? null) ? $data['styles'] : [];

        if ($title === '') {
            throw ValidationException::withMessages(['title' => 'Nama Reusable Block tidak boleh kosong.']);
        }

        if (! BlockRegistry::has($blockType)) {
            throw ValidationException::withMessages(['block_type' => "Tipe blok '{$blockType}' tidak terdaftar di Block Registry."]);
        }

        $id = 'reusable-'.bin2hex(random_bytes(4));

        // Pencegahan Circular Reference (Task F)
        $this->assertNoCircularReference($id, $content);

        $all = $this->getAll(withUsage: false);

        $newItem = [
            'id' => $id,
            'title' => htmlspecialchars($title, ENT_QUOTES, 'UTF-8'),
            'category' => htmlspecialchars($category, ENT_QUOTES, 'UTF-8'),
            'block_type' => $blockType,
            'content' => $content,
            'styles' => $styles,
            'created_by' => $user?->name ?? 'Super Admin',
            'created_at' => CarbonImmutable::now('Asia/Jakarta')->toIso8601String(),
            'updated_at' => CarbonImmutable::now('Asia/Jakarta')->toIso8601String(),
        ];

        $all[] = $newItem;
        Pengaturan::setJson('landing_reusable_blocks', $all);

        $this->auditService->catat(
            tipe: TipeAuditEnum::Aksi,
            tabel: 'pengaturan',
            idData: 0,
            detail: [
                'aksi' => "Membuat Reusable Block baru '{$title}' ({$id})",
                'superadmin_id' => $user?->id,
                'reusable_id' => $id,
                'block_type' => $blockType,
            ]
        );

        $newItem['usage_count'] = 0;
        $newItem['usages'] = [];
        $newItem['is_in_use'] = false;

        return $newItem;
    }

    /**
     * Memperbarui konten atau gaya dari definisi Reusable Block yang ada.
     * Seluruh halaman yang merujuk blok ini akan otomatis tersinkronisasi.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>|null
     *
     * @throws ValidationException
     */
    public function update(string $id, array $data, ?User $user = null): ?array
    {
        $all = $this->getAll(withUsage: false);
        $targetIndex = null;
        $targetItem = null;

        foreach ($all as $idx => $item) {
            if (($item['id'] ?? '') === $id) {
                $targetIndex = $idx;
                $targetItem = $item;
                break;
            }
        }

        if ($targetIndex === null || ! $targetItem) {
            return null;
        }

        if (isset($data['title'])) {
            $newTitle = trim((string) $data['title']);
            if ($newTitle === '') {
                throw ValidationException::withMessages(['title' => 'Nama Reusable Block tidak boleh kosong.']);
            }
            $targetItem['title'] = htmlspecialchars($newTitle, ENT_QUOTES, 'UTF-8');
        }

        if (isset($data['category'])) {
            $targetItem['category'] = htmlspecialchars(trim((string) $data['category']), ENT_QUOTES, 'UTF-8');
        }

        if (isset($data['content']) && is_array($data['content'])) {
            // Pencegahan Circular Reference (Task F)
            $this->assertNoCircularReference($id, $data['content']);
            $targetItem['content'] = $data['content'];
        }

        if (isset($data['styles']) && is_array($data['styles'])) {
            $targetItem['styles'] = $data['styles'];
        }

        $targetItem['updated_at'] = CarbonImmutable::now('Asia/Jakarta')->toIso8601String();
        $all[$targetIndex] = $targetItem;

        Pengaturan::setJson('landing_reusable_blocks', $all);

        $usages = $this->getUsage($id);
        $usageCount = count($usages);

        $this->auditService->catat(
            tipe: TipeAuditEnum::Aksi,
            tabel: 'pengaturan',
            idData: 0,
            detail: [
                'aksi' => "Memperbarui definisi Reusable Block '{$targetItem['title']}' ({$id}), memengaruhi {$usageCount} instance",
                'superadmin_id' => $user?->id,
                'reusable_id' => $id,
                'affected_instances' => $usageCount,
            ]
        );

        $targetItem['usage_count'] = $usageCount;
        $targetItem['usages'] = $usages;
        $targetItem['is_in_use'] = $usageCount > 0;

        return $targetItem;
    }

    /**
     * Menghapus definisi Reusable Block dengan proteksi keterpakaian (Delete Protection).
     *
     * @return array{success: bool, message: string, usage_count?: int, usages?: array<int, array<string, mixed>>}
     */
    public function delete(string $id, ?User $user = null): array
    {
        $all = $this->getAll(withUsage: false);
        $targetIndex = null;
        $targetItem = null;

        foreach ($all as $idx => $item) {
            if (($item['id'] ?? '') === $id) {
                $targetIndex = $idx;
                $targetItem = $item;
                break;
            }
        }

        if ($targetIndex === null || ! $targetItem) {
            return [
                'success' => false,
                'message' => 'Reusable Block tidak ditemukan.',
            ];
        }

        // 1. Safety Guard: Periksa apakah masih digunakan pada Page Document aktif
        $usages = $this->getUsage($id);
        if (count($usages) > 0) {
            $locations = [];
            foreach ($usages as $u) {
                $locations[] = "{$u['location']} (Urutan {$u['order']})";
            }
            $locList = implode(', ', array_slice($locations, 0, 3));
            if (count($locations) > 3) {
                $locList .= ' dan '.(count($locations) - 3).' lokasi lainnya';
            }

            return [
                'success' => false,
                'message' => "Gagal menghapus: Reusable Block '{$targetItem['title']}' masih digunakan pada: {$locList}. Ubah blok tersebut menjadi blok reguler terlebih dahulu sebelum menghapus definisi bersama.",
                'usage_count' => count($usages),
                'usages' => $usages,
            ];
        }

        array_splice($all, $targetIndex, 1);
        Pengaturan::setJson('landing_reusable_blocks', $all);

        $this->auditService->catat(
            tipe: TipeAuditEnum::Aksi,
            tabel: 'pengaturan',
            idData: 0,
            detail: [
                'aksi' => "Menghapus Reusable Block '{$targetItem['title']}' ({$id})",
                'superadmin_id' => $user?->id,
                'reusable_id' => $id,
            ]
        );

        return [
            'success' => true,
            'message' => "Reusable Block '{$targetItem['title']}' berhasil dihapus.",
        ];
    }

    /**
     * Memeriksa keterpakaian (Usage Tracking) Reusable Block pada dokumen Draf dan Live.
     *
     * @return array<int, array{scope: string, location: string, block_id: string, order: int}>
     */
    public function getUsage(string $reusableId): array
    {
        $usages = [];
        $docService = app(LandingPageDocumentService::class);

        // 1. Cek di Dokumen Draf
        try {
            $draftDoc = $docService->getDocument(draft: true);
            $blocks = $draftDoc['blocks'] ?? [];
            foreach ($blocks as $b) {
                if (($b['reusable_id'] ?? null) === $reusableId) {
                    $bTitle = $b['title'] ?? ucfirst((string) ($b['type'] ?? 'Blok'));
                    $usages[] = [
                        'scope' => 'draft',
                        'location' => "Draf Sandbox: {$bTitle}",
                        'block_id' => (string) ($b['id'] ?? ''),
                        'order' => (int) ($b['order'] ?? 1),
                    ];
                }
            }
        } catch (\Throwable $e) {
        }

        // 2. Cek di Dokumen Live
        try {
            $liveDoc = $docService->getDocument(draft: false);
            $blocks = $liveDoc['blocks'] ?? [];
            foreach ($blocks as $b) {
                if (($b['reusable_id'] ?? null) === $reusableId) {
                    $bTitle = $b['title'] ?? ucfirst((string) ($b['type'] ?? 'Blok'));
                    $usages[] = [
                        'scope' => 'live',
                        'location' => "Live Publik: {$bTitle}",
                        'block_id' => (string) ($b['id'] ?? ''),
                        'order' => (int) ($b['order'] ?? 1),
                    ];
                }
            }
        } catch (\Throwable $e) {
        }

        return $usages;
    }

    /**
     * Memastikan tidak terjadi Circular Reference (reusable block memuat referensi ke dirinya sendiri).
     *
     * @param  array<string, mixed>  $content
     *
     * @throws ValidationException
     */
    protected function assertNoCircularReference(string $reusableId, array $content): void
    {
        $contentJson = json_encode($content);
        if ($contentJson && str_contains($contentJson, $reusableId)) {
            throw ValidationException::withMessages([
                'circular_reference' => "Terdeteksi Circular Reference: Reusable Block '{$reusableId}' tidak boleh merujuk ke dirinya sendiri.",
            ]);
        }
    }
}
