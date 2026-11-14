<?php

declare(strict_types=1);

use App\Models\Pengaturan;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $newSummary = "Pertama calon peserta membuat akun, mengisi data pribadi, dan menunggu verifikasi akun oleh admin.\nKedua calon peserta memilih bidang dan mengunggah berkas, lalu admin melakukan verifikasi dan seleksi. Hasilnya diumumkan di portal MAGKOM.\nKetiga peserta melaksanakan magang, mengerjakan tugas, dan mengisi logbook sesuai durasi yang ditentukan.\nKeempat setelah magang selesai, peserta menyelesaikan program magang, mengunggah laporan akhir, dan dinyatakan lulus secara resmi.";

        $sanitizeSummary = function (?string $text) use ($newSummary): string {
            if ($text === null || $text === '') {
                return $newSummary;
            }
            if (str_contains($text, 'sertifikat') || str_contains($text, 'lembar nilai')) {
                return $newSummary;
            }

            return $text;
        };

        // 1. Sanitasi key summary
        foreach (['landing_tahapan_summary', 'draft_landing_tahapan_summary'] as $key) {
            $val = Pengaturan::getNilai($key);
            if ($val !== null) {
                Pengaturan::setNilai($key, $sanitizeSummary($val));
            }
        }

        // 2. Sanitasi steps array
        $sanitizeSteps = function (?array $steps): array {
            if (! is_array($steps)) {
                return [];
            }
            foreach ($steps as &$step) {
                if (($step['step'] ?? '') === '04' || str_contains(strtolower($step['title'] ?? ''), 'nilai') || str_contains(strtolower($step['title'] ?? ''), 'sertifikat')) {
                    $step['title'] = 'Selesai Magang';
                    $step['badge'] = 'KELULUSAN';
                    $step['desc'] = 'Penyelesaian program magang, pengumpulan laporan akhir, dan pelepasan resmi peserta oleh dinas.';
                }
            }
            unset($step);

            return $steps;
        };

        foreach (['landing_tahapan_steps', 'draft_landing_tahapan_steps'] as $key) {
            $raw = Pengaturan::getNilai($key);
            if ($raw !== null && $raw !== '') {
                $decoded = json_decode($raw, true);
                if (is_array($decoded)) {
                    $cleaned = $sanitizeSteps($decoded);
                    Pengaturan::setNilai($key, json_encode($cleaned, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
                }
            }
        }

        // 3. Sanitasi WordPress-style site editor documents & blocks layout
        $sanitizeBlocks = function (array $blocks) use ($newSummary): array {
            foreach ($blocks as &$block) {
                if (($block['type'] ?? '') === 'tahapan' && isset($block['content']) && is_array($block['content'])) {
                    if (isset($block['content']['summary']) && (str_contains((string) $block['content']['summary'], 'sertifikat') || str_contains((string) $block['content']['summary'], 'lembar nilai'))) {
                        $block['content']['summary'] = $newSummary;
                    }
                    if (isset($block['content']['steps']) && is_array($block['content']['steps'])) {
                        foreach ($block['content']['steps'] as &$st) {
                            if (($st['step'] ?? '') === '04' || str_contains(strtolower($st['title'] ?? ''), 'nilai') || str_contains(strtolower($st['title'] ?? ''), 'sertifikat')) {
                                $st['title'] = 'Selesai Magang';
                                $st['badge'] = 'KELULUSAN';
                                $st['desc'] = 'Penyelesaian program magang, pengumpulan laporan akhir, dan pelepasan resmi peserta oleh dinas.';
                            }
                        }
                        unset($st);
                    }
                }

                if (($block['type'] ?? '') === 'features' && isset($block['content']['items']) && is_array($block['content']['items'])) {
                    foreach ($block['content']['items'] as &$it) {
                        if (str_contains(strtolower($it['title'] ?? ''), 'sertifikat') || str_contains(strtolower($it['title'] ?? ''), 'nilai')) {
                            $it['title'] = 'Penyelesaian & Portofolio';
                            $it['desc'] = 'Pengalaman kerja riil dan portofolio proyek pemerintahan untuk karir profesional.';
                            $it['icon'] = '🏆';
                        }
                    }
                    unset($it);
                }
            }
            unset($block);

            return $blocks;
        };

        $sanitizeDocument = function (string $jsonString) use ($sanitizeBlocks): string {
            $doc = json_decode($jsonString, true);
            if (! is_array($doc) || ! isset($doc['blocks']) || ! is_array($doc['blocks'])) {
                return $jsonString;
            }

            $doc['blocks'] = $sanitizeBlocks($doc['blocks']);

            return (string) json_encode($doc, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        };

        foreach (['draft_landing_page_document', 'landing_page_document'] as $docKey) {
            $docRaw = Pengaturan::getNilai($docKey);
            if ($docRaw !== null && $docRaw !== '') {
                $cleanedDoc = $sanitizeDocument($docRaw);
                Pengaturan::setNilai($docKey, $cleanedDoc);
            }
        }

        foreach (['draft_landing_blocks_layout', 'landing_blocks_layout'] as $layoutKey) {
            $rawLayout = Pengaturan::getNilai($layoutKey);
            if ($rawLayout !== null && $rawLayout !== '') {
                $decoded = json_decode($rawLayout, true);
                if (is_array($decoded)) {
                    $cleaned = $sanitizeBlocks($decoded);
                    Pengaturan::setNilai($layoutKey, json_encode($cleaned, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
                }
            }
        }

        $rawRevisions = Pengaturan::getNilai('landing_revisions');
        if ($rawRevisions !== null && $rawRevisions !== '') {
            $revisions = json_decode($rawRevisions, true);
            if (is_array($revisions)) {
                foreach ($revisions as &$rev) {
                    if (isset($rev['blocks']) && is_array($rev['blocks'])) {
                        $rev['blocks'] = $sanitizeBlocks($rev['blocks']);
                    }
                    if (isset($rev['document']) && is_string($rev['document'])) {
                        $rev['document'] = $sanitizeDocument($rev['document']);
                    }
                    if (isset($rev['snapshot']) && is_array($rev['snapshot'])) {
                        foreach (['landing_tahapan_summary', 'draft_landing_tahapan_summary'] as $k) {
                            if (isset($rev['snapshot'][$k])) {
                                $rev['snapshot'][$k] = $sanitizeSummary((string) $rev['snapshot'][$k]);
                            }
                        }
                        foreach (['landing_tahapan_steps', 'draft_landing_tahapan_steps'] as $k) {
                            if (isset($rev['snapshot'][$k]) && is_string($rev['snapshot'][$k])) {
                                $dec = json_decode($rev['snapshot'][$k], true);
                                if (is_array($dec)) {
                                    $rev['snapshot'][$k] = json_encode($sanitizeSteps($dec), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                }
                            }
                        }
                        foreach (['landing_blocks_layout', 'draft_landing_blocks_layout'] as $k) {
                            if (isset($rev['snapshot'][$k]) && is_string($rev['snapshot'][$k])) {
                                $dec = json_decode($rev['snapshot'][$k], true);
                                if (is_array($dec)) {
                                    $rev['snapshot'][$k] = json_encode($sanitizeBlocks($dec), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                }
                            }
                        }
                        foreach (['landing_page_document', 'draft_landing_page_document'] as $k) {
                            if (isset($rev['snapshot'][$k]) && is_string($rev['snapshot'][$k])) {
                                $rev['snapshot'][$k] = $sanitizeDocument($rev['snapshot'][$k]);
                            }
                        }
                    }
                }
                unset($rev);
                Pengaturan::setNilai('landing_revisions', json_encode($revisions, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            }
        }

        // 4. Bersihkan dead keys jika masih ada
        Pengaturan::deleteKunci('passing_grade_sertifikat');
        Pengaturan::deleteKunci('masa_tenggang_unduh_nilai_hari');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Migrasi ini bersifat sanitasi teks satu arah (irreversible clean-up).
    }
};
