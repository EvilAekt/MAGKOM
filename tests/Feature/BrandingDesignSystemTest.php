<?php

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BrandingDesignSystemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::parse('2026-10-08 10:00:00', 'Asia/Jakarta'));
        CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-10-08 10:00:00', 'Asia/Jakarta'));

        if (Jurusan::count() === 0 || BulanMagang::count() === 0) {
            $this->seed();
        }
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        CarbonImmutable::setTestNow();
        parent::tearDown();
    }

    /**
     * Memastikan seluruh aset visual logo dan favicon tersedia di direktori public.
     */
    public function test_brand_assets_exist_in_public_directory(): void
    {
        $this->assertFileExists(public_path('images/logo-sleman.png'));
        $this->assertFileExists(public_path('images/logo-magkom.png'));
        $this->assertFileExists(public_path('favicon.png'));
        $this->assertFileExists(public_path('favicon.ico'));
    }

    /**
     * Memastikan halaman landing memuat logo Sleman, teks MAGANG KOMINFO, dan tidak ada teks keliru.
     */
    public function test_landing_page_displays_new_logo_and_magang_kominfo_branding(): void
    {
        $response = $this->get(route('landing'));
        $response->assertStatus(200);

        // Memuat logo Sleman
        $response->assertSee('images/logo-sleman.png', false);

        // Memuat branding baru
        $response->assertSee('MAGANG KOMINFO', false);
        $response->assertSee('Magang Kominfo Sleman', false);

        // Memastikan teks lama / terbalik tidak muncul
        $response->assertDontSee('KOMINFO PORTAL', false);
        $response->assertDontSee('KOMINFO MAGANG', false);
    }

    /**
     * Memastikan halaman login memuat logo Sleman dan teks MAGANG KOMINFO SLEMAN.
     */
    public function test_auth_pages_display_new_branding_and_logo(): void
    {
        $response = $this->get(route('login'));
        $response->assertStatus(200);

        $response->assertSee('images/logo-sleman.png', false);
        $response->assertSee('MAGANG KOMINFO SLEMAN', false);
        $response->assertSee('Magang Kominfo Sleman', false);
        $response->assertDontSee('KOMINFO PORTAL', false);
        $response->assertDontSee('KOMINFO MAGANG SLEMAN', false);
    }

    /**
     * Memastikan sidebar dashboard internal menampilkan logo Sleman dan subtitle MAGANG KOMINFO.
     */
    public function test_internal_dashboard_sidebar_displays_magang_kominfo_subtitle(): void
    {
        $admin = new User();
        $admin->nama = 'Administrator MagKom';
        $admin->email = 'admin.brand.test@slemankab.go.id';
        $admin->password = 'Password123!';
        $admin->role = RoleEnum::Admin;
        $admin->status_aktif = true;
        $admin->must_change_password = false;
        $admin->email_verified_at = now();
        $admin->save();

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));
        $response->assertStatus(200);

        $response->assertSee('images/logo-sleman.png', false);
        $response->assertSee('MAGANG KOMINFO', false);
        $response->assertDontSee('KOMINFO PORTAL', false);
        $response->assertDontSee('KOMINFO MAGANG', false);
    }

    /**
     * Memastikan sidebar portal peserta menampilkan logo Sleman dan subtitle Magang Kominfo.
     */
    public function test_peserta_portal_sidebar_displays_magang_kominfo_subtitle(): void
    {
        $peserta = new User();
        $peserta->nama = 'Peserta Magang Aktif';
        $peserta->email = 'peserta.brand.test@example.com';
        $peserta->password = 'Password123!';
        $peserta->role = RoleEnum::Peserta;
        $peserta->status_aktif = true;
        $peserta->must_change_password = false;
        $peserta->email_verified_at = now();
        $peserta->save();

        $response = $this->actingAs($peserta)->get(route('peserta.dashboard'));
        $response->assertStatus(200);

        $response->assertSee('images/logo-sleman.png', false);
        $response->assertSee('Magang Kominfo', false);
        $response->assertDontSee('KOMINFO PORTAL', false);
        $response->assertDontSee('KOMINFO MAGANG', false);
    }
}
