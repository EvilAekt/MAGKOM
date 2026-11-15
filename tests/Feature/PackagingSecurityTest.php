<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

class PackagingSecurityTest extends TestCase
{
    /**
     * Memastikan berkas konfigurasi .gitignore mengecualikan seluruh berkas kredensial dan temporer.
     */
    public function test_gitignore_contains_critical_exclusions(): void
    {
        $gitignorePath = base_path('.gitignore');
        $this->assertFileExists($gitignorePath);

        $content = file_get_contents($gitignorePath);
        $this->assertNotFalse($content);

        $this->assertStringContainsString('.env', $content);
        $this->assertStringContainsString('.mcp.json', $content);
        $this->assertStringContainsString('/scratch', $content);
        $this->assertStringContainsString('/dist', $content);
        $this->assertStringContainsString('.phpunit.result.cache', $content);
    }

    /**
     * Memastikan .gitattributes mengecualikan berkas non-produksi saat git archive dijalankan.
     */
    public function test_gitattributes_contains_export_ignores(): void
    {
        $gitattributesPath = base_path('.gitattributes');
        $this->assertFileExists($gitattributesPath);

        $content = file_get_contents($gitattributesPath);
        $this->assertNotFalse($content);

        $this->assertStringContainsString('.mcp.json export-ignore', $content);
        $this->assertStringContainsString('scratch/ export-ignore', $content);
        $this->assertStringContainsString('tests/ export-ignore', $content);
        $this->assertStringContainsString('.gitleaks.toml export-ignore', $content);
    }

    /**
     * Memastikan template .env.example tersedia dan tidak memuat nilai rahasia aktif.
     */
    public function test_env_example_exists_and_contains_no_secrets(): void
    {
        $envExamplePath = base_path('.env.example');
        $this->assertFileExists($envExamplePath);

        $content = file_get_contents($envExamplePath);
        $this->assertNotFalse($content);

        // Nilai APP_KEY harus kosong di .env.example
        $this->assertMatchesRegularExpression('/^APP_KEY=\s*$/m', $content);

        // Kredensial eksternal tidak boleh terisi secara hardcoded
        $this->assertMatchesRegularExpression('/^GOOGLE_CLIENT_ID=\s*$/m', $content);
        $this->assertMatchesRegularExpression('/^GOOGLE_CLIENT_SECRET=\s*$/m', $content);
    }

    /**
     * Memastikan skrip packaging rilis aman tersedia di repositori.
     */
    public function test_package_release_scripts_exist(): void
    {
        $this->assertFileExists(base_path('scripts/package-release.ps1'));
        $this->assertFileExists(base_path('scripts/package-release.sh'));
    }
}
