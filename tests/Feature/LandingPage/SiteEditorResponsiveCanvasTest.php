<?php

declare(strict_types=1);

namespace Tests\Feature\LandingPage;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiteEditorResponsiveCanvasTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected User $admin;

    protected User $peserta;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superAdmin = User::factory()->create([
            'email' => 'superadmin.canvas@magkom.test',
            'role' => RoleEnum::SuperAdmin,
            'status_aktif' => true,
        ]);

        $this->admin = User::factory()->create([
            'email' => 'admin.canvas@magkom.test',
            'role' => RoleEnum::Admin,
            'status_aktif' => true,
        ]);

        $this->peserta = User::factory()->create([
            'email' => 'peserta.canvas@magkom.test',
            'role' => RoleEnum::Peserta,
            'status_aktif' => true,
        ]);
    }

    public function test_superadmin_can_access_site_editor_canvas(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->get(route('superadmin.site-editor.index'));

        $response->assertOk();
        $response->assertSee('wp-canvas-frame-container', false);
        $response->assertSee('wp-preview-iframe', false);
        $response->assertSee('data-device="desktop"', false);
        $response->assertSee('data-device="tablet"', false);
        $response->assertSee('data-device="mobile"', false);
    }

    public function test_non_superadmin_cannot_access_site_editor(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('superadmin.site-editor.index'));

        $response->assertForbidden();

        $responsePeserta = $this->actingAs($this->peserta)
            ->get(route('superadmin.site-editor.index'));

        $responsePeserta->assertForbidden();
    }

    public function test_site_editor_preview_serves_kebijakan_privasi_in_editor_mode(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->get(route('superadmin.landing.preview', [
                'editor_mode' => 1,
                'page' => 'kebijakan-privasi',
            ]));

        $response->assertOk();
        $response->assertSee('data-wp-block="kebijakan"', false);
        $response->assertSee('Kebijakan Privasi &amp; Perlindungan Data', false);
        $response->assertSee('data-wp-editable="kebijakan_title"', false);
    }

    public function test_public_kebijakan_privasi_renders_cleanly_with_responsive_classes(): void
    {
        $response = $this->get(route('kebijakan-privasi'));

        $response->assertOk();
        $response->assertSee('max-w-4xl mx-auto px-3 sm:px-6', false);
        $response->assertSee('panePrivasi', false);
        $response->assertSee('paneSyarat', false);
        $response->assertSee('viewport', false);
    }
}
