<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class LanguageLocalizationTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $admin;
    protected User $pembimbing;
    protected User $peserta;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superAdmin = new User();
        $this->superAdmin->nama = 'Super Admin MagKom';
        $this->superAdmin->email = 'superadmin.loc@slemankab.go.id';
        $this->superAdmin->password = 'PasswordSuperAdmin123!';
        $this->superAdmin->role = RoleEnum::SuperAdmin;
        $this->superAdmin->status_aktif = true;
        $this->superAdmin->email_verified_at = now();
        $this->superAdmin->save();

        $this->admin = new User();
        $this->admin->nama = 'Admin Bidang Aptika';
        $this->admin->email = 'admin.loc@slemankab.go.id';
        $this->admin->password = 'PasswordAdmin123!';
        $this->admin->role = RoleEnum::Admin;
        $this->admin->status_aktif = true;
        $this->admin->email_verified_at = now();
        $this->admin->save();

        $this->pembimbing = new User();
        $this->pembimbing->nama = 'Pembimbing Lapangan';
        $this->pembimbing->email = 'pembimbing.loc@slemankab.go.id';
        $this->pembimbing->password = 'PasswordPembimbing123!';
        $this->pembimbing->role = RoleEnum::Pembimbing;
        $this->pembimbing->status_aktif = true;
        $this->pembimbing->email_verified_at = now();
        $this->pembimbing->save();

        $this->peserta = new User();
        $this->peserta->nama = 'Peserta Magang Riset';
        $this->peserta->email = 'peserta.loc@univ.ac.id';
        $this->peserta->password = 'PasswordPeserta123!';
        $this->peserta->role = RoleEnum::Peserta;
        $this->peserta->status_aktif = true;
        $this->peserta->email_verified_at = now();
        $this->peserta->save();
    }

    public function test_default_locale_is_indonesian(): void
    {
        $response = $this->get(route('login'));

        $response->assertStatus(200);
        $this->assertEquals('id', App::getLocale());
        $response->assertSee('lang="id"', false);
    }

    public function test_superadmin_can_switch_language_to_english_and_render_english_sidebar(): void
    {
        $this->actingAs($this->superAdmin);

        $response = $this->post(route('superadmin.akun.preferensi.update'), [
            'bahasa' => 'en',
            'tema' => 'gelap',
        ]);

        $response->assertSessionHas('bahasa_portal', 'en');
        $response->assertCookie('bahasa_portal', 'en');

        // Buka dashboard superadmin dengan locale en di session
        $dashboardResponse = $this->withSession(['bahasa_portal' => 'en'])
            ->get(route('superadmin.dashboard'));

        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('lang="en"', false);
        $dashboardResponse->assertSee('OPERATIONAL MODULE');
        $dashboardResponse->assertSee('SYSTEM');
        $dashboardResponse->assertSee('Configuration');
    }

    public function test_admin_and_pembimbing_can_switch_language_to_english(): void
    {
        // 1. Admin Bidang
        $this->actingAs($this->admin);

        $adminPrefResponse = $this->post(route('admin.pengaturan.preferensi.update'), [
            'bahasa' => 'en',
            'tema' => 'terang',
        ]);

        $adminPrefResponse->assertSessionHas('bahasa_portal', 'en');
        $adminPrefResponse->assertCookie('bahasa_portal', 'en');

        $adminDash = $this->withSession(['bahasa_portal' => 'en'])
            ->get(route('admin.dashboard'));

        $adminDash->assertStatus(200);
        $adminDash->assertSee('lang="en"', false);
        $adminDash->assertSee('Document Verification');
        $adminDash->assertSee('Placement');

        // 2. Pembimbing Lapangan
        $this->actingAs($this->pembimbing);

        $pembimbingPrefResponse = $this->post(route('pembimbing.pengaturan.preferensi.update'), [
            'bahasa' => 'en',
            'tema' => 'sistem',
        ]);

        $pembimbingPrefResponse->assertSessionHas('bahasa_portal', 'en');
        $pembimbingPrefResponse->assertCookie('bahasa_portal', 'en');

        $pembimbingDash = $this->withSession(['bahasa_portal' => 'en'])
            ->get(route('pembimbing.dashboard'));

        $pembimbingDash->assertStatus(200);
        $pembimbingDash->assertSee('lang="en"', false);
        $pembimbingDash->assertSee('Mentored Participants');
        $pembimbingDash->assertSee('Logbook Review');
    }

    public function test_peserta_can_switch_language_to_english_and_render_english_navigation(): void
    {
        $this->actingAs($this->peserta);

        $prefResponse = $this->post(route('peserta.pengaturan.preferensi.update'), [
            'bahasa' => 'en',
            'tema' => 'terang',
        ]);

        $prefResponse->assertSessionHas('bahasa_portal', 'en');
        $prefResponse->assertCookie('bahasa_portal', 'en');

        $dashboard = $this->withSession(['bahasa_portal' => 'en'])
            ->get(route('peserta.dashboard'));

        $dashboard->assertStatus(200);
        $dashboard->assertSee('lang="en"', false);
        $dashboard->assertSee('Main Menu');
        $dashboard->assertSee('Profile & Biodata');
        $dashboard->assertSee('Internship Registration');
    }

    public function test_locale_middleware_falls_back_to_id_for_invalid_language(): void
    {
        $this->actingAs($this->peserta);

        // Kirim request dengan session bahasa yang tidak valid
        $response = $this->withSession(['bahasa_portal' => 'french'])
            ->get(route('peserta.dashboard'));

        $response->assertStatus(200);
        $this->assertEquals('id', App::getLocale());
        $response->assertSee('lang="id"', false);
    }

    public function test_cookie_restores_english_locale_without_prior_session(): void
    {
        $response = $this->withCookies(['bahasa_portal' => 'en'])
            ->get(route('login'));

        $response->assertStatus(200);
        $this->assertEquals('en', App::getLocale());
        $response->assertSee('lang="en"', false);
    }
}
