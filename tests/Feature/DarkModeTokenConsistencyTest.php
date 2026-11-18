<?php

namespace Tests\Feature;

use Tests\TestCase;

class DarkModeTokenConsistencyTest extends TestCase
{
    /**
     * Memverifikasi token semantik --dm-* ada di resources/css/base/variables.css.
     */
    public function test_dark_mode_semantic_tokens_exist_in_variables_css(): void
    {
        $variablesPath = resource_path('css/base/variables.css');
        $this->assertFileExists($variablesPath);

        $content = file_get_contents($variablesPath);

        $expectedTokens = [
            '--dm-canvas: #061630;',
            '--dm-surface: #0E2548;',
            '--dm-border: rgba(255, 255, 255, 0.07);',
            '--dm-shadow: 0 4px 20px rgba(0, 0, 0, 0.50);',
            '--dm-text-title: #EAF0FA;',
            '--dm-text-main: #EAF0FA;',
            '--dm-text-muted: #9FB0CC;',
            '--dm-text-subtle: #5E7194;',
            '--dm-hero-gradient: linear-gradient(135deg, #003566 0%, #01347C 100%);',
            '--dm-navy-deep: #031B40;',
            '--dm-danger: #d64545;',
            '--dm-warning: #FFC300;',
        ];

        foreach ($expectedTokens as $token) {
            $this->assertStringContainsString(
                $token,
                $content,
                "Token semantik {$token} harus ada di resources/css/base/variables.css"
            );
        }

        // Pastikan sidebar konsisten ke token --dm-navy-deep (#031B40)
        $this->assertStringContainsString('--sidebar-bg: var(--dm-navy-deep);', $content);
    }

    /**
     * Memverifikasi tiga file CSS pembimbing prioritas tidak mengandung kode hex dalam [data-theme="dark"].
     */
    public function test_priority_pembimbing_css_files_have_no_raw_hex_in_dark_mode(): void
    {
        $priorityFiles = [
            resource_path('css/pages/pembimbing/logbook-index.css'),
            resource_path('css/pages/pembimbing/tugas-index.css'),
            resource_path('css/pages/pembimbing/laporan-kinerja-index.css'),
        ];

        foreach ($priorityFiles as $filePath) {
            $this->assertFileExists($filePath);
            $fileName = basename($filePath);
            $content = file_get_contents($filePath);

            $this->assertStringContainsString(
                '[data-theme="dark"]',
                $content,
                "File {$fileName} harus memiliki blok selector [data-theme=\"dark\"]"
            );

            // Periksa setiap blok aturan di bawah [data-theme="dark"]
            // Split berdasarkan baris dan cari aturan yang aktif saat di dalam blok [data-theme="dark"]
            $lines = explode("\n", $content);
            $inDarkBlock = false;
            $hexViolations = [];

            foreach ($lines as $lineIndex => $line) {
                $trimmed = trim($line);

                if (str_contains($trimmed, '[data-theme="dark"]')) {
                    $inDarkBlock = true;
                }

                if ($inDarkBlock) {
                    // Cari pola hex #123 atau #123456 atau #12345678 tapi abaikan variabel nama seperti #id
                    if (preg_match('/:\s*[^;]*#[0-9a-fA-F]{3,8}/', $trimmed, $matches)) {
                        $hexViolations[] = 'Baris '.($lineIndex + 1).": {$trimmed}";
                    }

                    if (str_starts_with($trimmed, '}')) {
                        $inDarkBlock = false;
                    }
                }
            }

            $this->assertEmpty(
                $hexViolations,
                "File {$fileName} tidak boleh mengandung kode hex di dalam selector [data-theme=\"dark\"]. Ditemukan:\n".implode("\n", $hexViolations)
            );
        }
    }
}
