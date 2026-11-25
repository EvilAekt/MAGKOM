<?php

declare(strict_types=1);

namespace Tests\Feature;

use Database\Seeders\JenisDokumenSeeder;
use Database\Seeders\PengaturanSeeder;
use Database\Seeders\PeriodeKuotaSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityHeadersProductionTest extends TestCase
{
    use RefreshDatabase;

    private string $originalEnv;

    protected function setUp(): void
    {
        parent::setUp();

        $this->originalEnv = (string) $this->app['env'];

        $this->seed(PengaturanSeeder::class);
        $this->seed(PeriodeKuotaSeeder::class);
        $this->seed(JenisDokumenSeeder::class);
    }

    protected function tearDown(): void
    {
        $this->app['env'] = $this->originalEnv;
        config(['app.env' => $this->originalEnv]);

        parent::tearDown();
    }

    public function test_production_csp_header_is_present_and_clean_on_public_routes(): void
    {
        $this->app['env'] = 'production';
        config(['app.env' => 'production']);

        $routes = [
            '/',
            '/login',
            '/register',
            '/forgot-password',
            '/panduan-alur',
            '/kebijakan-privasi',
        ];

        foreach ($routes as $route) {
            $response = $this->get($route);
            $response->assertOk();

            $csp = (string) $response->headers->get('Content-Security-Policy');
            $this->assertNotEmpty($csp, "CSP header missing on route {$route} in production");

            // Extract script-src directive
            preg_match('/script-src\s+([^;]+)/', $csp, $matches);
            $scriptSrc = $matches[1] ?? '';

            $this->assertStringNotContainsString('cdn.tailwindcss.com', $csp, "cdn.tailwindcss.com must not be present in CSP on {$route}");
            $this->assertStringNotContainsString("'unsafe-inline'", $scriptSrc, "script-src must not contain 'unsafe-inline' on {$route}");
            $this->assertStringNotContainsString("'unsafe-eval'", $scriptSrc, "script-src must not contain 'unsafe-eval' on {$route}");
            $this->assertStringContainsString("'self'", $scriptSrc, "script-src must contain 'self' on {$route}");
        }
    }

    public function test_public_pages_conform_to_csp_inline_script_and_handler_allowlist(): void
    {
        $this->app['env'] = 'production';
        config(['app.env' => 'production']);

        // Routes monitored for CSP compliance
        $routes = [
            '/' => 'Landing Page',
            '/login' => 'Login',
            '/register' => 'Register',
            '/forgot-password' => 'Lupa Password',
            '/panduan-alur' => 'Panduan Alur',
            '/kebijakan-privasi' => 'Kebijakan Privasi',
        ];

        // TASK-03a: Seluruh halaman publik (termasuk landing, panduan-alur, peta-alur, kebijakan-privasi)
        // telah dibersihkan secara penuh dari inline script dan inline event handler.
        $violationAllowlist = [];

        foreach ($routes as $route => $name) {
            $response = $this->get($route);
            $response->assertOk();
            $content = $response->getContent();

            // Deteksi tag inline script tanpa src (kecuali type="application/json" atau "application/ld+json")
            preg_match_all('/<script(?![^>]*\bsrc=)(?![^>]*type=["\']application\/(?:ld\+)?json["\'])[^>]*>(.*?)<\/script>/is', $content, $scriptMatches);
            $inlineScripts = array_filter($scriptMatches[0] ?? []);

            // Deteksi inline event handler: on*=
            preg_match_all('/\son(?:click|change|submit|input|error|load|keyup|keydown)=["\'][^"\']*["\']/i', $content, $handlerMatches);
            $inlineHandlers = array_filter($handlerMatches[0] ?? []);

            $totalViolations = count($inlineScripts) + count($inlineHandlers);

            if (in_array($route, $violationAllowlist, true)) {
                // Diizinkan sementara pada TASK-02, akan dihilangkan pada TASK-03
                $this->assertGreaterThanOrEqual(0, $totalViolations);
            } else {
                // Halaman di luar allowlist (login, register, forgot-password) wajib 0 pelanggaran
                $this->assertSame(0, count($inlineScripts), "Route {$name} ({$route}) has unexpected inline scripts: ".implode(', ', $inlineScripts));
                $this->assertSame(0, count($inlineHandlers), "Route {$name} ({$route}) has unexpected inline handlers: ".implode(', ', $inlineHandlers));
            }
        }
    }

    public function test_cdn_tailwindcss_is_not_referenced_in_blade_views(): void
    {
        $viewFiles = glob(resource_path('views/**/*.blade.php'));
        $found = [];

        foreach ($viewFiles as $file) {
            $content = file_get_contents($file);
            if ($content !== false && str_contains($content, 'cdn.tailwindcss.com')) {
                $found[] = $file;
            }
        }

        $this->assertEmpty($found, 'Found cdn.tailwindcss.com references in: '.implode(', ', $found));
    }

    public function test_all_blade_views_have_zero_inline_scripts_and_zero_inline_handlers(): void
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(resource_path('views'))
        );

        $scriptViolations = [];
        $handlerViolations = [];
        $totalScanned = 0;

        foreach ($iterator as $file) {
            if ($file->isFile() && str_ends_with($file->getFilename(), '.blade.php')) {
                $totalScanned++;
                $content = file_get_contents($file->getPathname()) ?: '';
                $relPath = str_replace(resource_path('views').DIRECTORY_SEPARATOR, '', $file->getPathname());

                // Inline script detection (excluding src= and JSON type)
                if (preg_match_all('/<script(?![^>]*\bsrc=)(?![^>]*type=["\']application\/(?:ld\+)?json["\'])[^>]*>/i', $content, $scriptMatches)) {
                    $scriptViolations[$relPath] = count($scriptMatches[0]);
                }

                // Inline event handler detection
                if (preg_match_all('/\son[a-zA-Z]+=/i', $content, $handlerMatches)) {
                    $handlerViolations[$relPath] = count($handlerMatches[0]);
                }
            }
        }

        $this->assertGreaterThan(100, $totalScanned, 'Sanity check: expected more than 100 Blade view files to be scanned.');
        $this->assertEmpty($scriptViolations, 'Found inline <script> tags in views: '.json_encode($scriptViolations));
        $this->assertEmpty($handlerViolations, 'Found inline on*= event handlers in views: '.json_encode($handlerViolations));
    }
}
