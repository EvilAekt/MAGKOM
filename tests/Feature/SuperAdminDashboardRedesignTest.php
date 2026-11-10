<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JalurEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusPendaftaranEnum;
use App\Enums\SumberPenempatanEnum;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\Kuota;
use App\Models\Pendaftaran;
use App\Models\PesertaProfil;
use App\Models\User;
use Database\Seeders\JenisDokumenSeeder;
use Database\Seeders\RubrikSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperAdminDashboardRedesignTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected User $admin;

    protected User $pembimbing;

    protected User $peserta;

    protected BulanMagang $bulan;

    protected Jurusan $jurusan;

    protected Kuota $kuota;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(JenisDokumenSeeder::class);
        $this->seed(RubrikSeeder::class);

        Bidang::create([
            'nama_bidang' => 'Bidang Aplikasi Informatika',
            'aktif' => true,
        ]);

        $this->jurusan = Jurusan::create([
            'nama_jurusan' => 'Teknik Informatika',
            'kategori' => 'mahasiswa',
            'aktif' => true,
        ]);

        $this->bulan = BulanMagang::create([
            'bulan_mulai' => now()->startOfMonth(),
            'tgl_buka' => now()->subDays(5)->toDateString(),
            'tgl_tutup' => now()->addDays(20)->toDateString(),
            'aktif' => true,
        ]);

        $this->kuota = Kuota::create([
            'bulan_magang_id' => $this->bulan->id,
            'jurusan_id' => $this->jurusan->id,
            'jumlah' => 10,
            'terisi' => 0,
            'aktif' => true,
        ]);

        $this->superAdmin = new User;
        $this->superAdmin->nama = 'SuperAdmin Eksekutif';
        $this->superAdmin->email = 'superadmin.redesign@slemankab.go.id';
        $this->superAdmin->password = bcrypt('Password123!');
        $this->superAdmin->role = RoleEnum::SuperAdmin;
        $this->superAdmin->status_aktif = true;
        $this->superAdmin->email_verified_at = now();
        $this->superAdmin->must_change_password = false;
        $this->superAdmin->save();

        $this->admin = new User;
        $this->admin->nama = 'Admin Operasional';
        $this->admin->email = 'admin.redesign@slemankab.go.id';
        $this->admin->password = bcrypt('Password123!');
        $this->admin->role = RoleEnum::Admin;
        $this->admin->status_aktif = true;
        $this->admin->email_verified_at = now();
        $this->admin->must_change_password = false;
        $this->admin->save();

        $this->pembimbing = new User;
        $this->pembimbing->nama = 'Pembimbing Redesign';
        $this->pembimbing->email = 'pembimbing.redesign@slemankab.go.id';
        $this->pembimbing->password = bcrypt('Password123!');
        $this->pembimbing->role = RoleEnum::Pembimbing;
        $this->pembimbing->status_aktif = true;
        $this->pembimbing->email_verified_at = now();
        $this->pembimbing->must_change_password = false;
        $this->pembimbing->save();

        $this->peserta = new User;
        $this->peserta->nama = 'Peserta Redesign';
        $this->peserta->email = 'peserta.redesign@student.ac.id';
        $this->peserta->password = bcrypt('Password123!');
        $this->peserta->role = RoleEnum::Peserta;
        $this->peserta->status_aktif = true;
        $this->peserta->email_verified_at = now();
        $this->peserta->must_change_password = false;
        $this->peserta->save();

        PesertaProfil::create([
            'user_id' => $this->peserta->id,
            'nim_nisn' => '21000999',
            'institusi' => 'Universitas Gadjah Mada',
            'jurusan_id' => $this->jurusan->id,
            'jenjang' => 's1',
            'no_hp' => '081234567899',
        ]);
    }

    public function test_dashboard_has_no_inline_event_handlers(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('superadmin.dashboard'));
        $response->assertOk();

        // Tidak boleh ada inline onclick atau onchange di dalam markup dashboard SuperAdmin
        $content = $response->getContent();
        $this->assertStringContainsString('class="sa-page"', $content);

        if (preg_match('/<div class="sa-page">([\s\S]*?)<\/div>\s*<\/main>/', $content, $matches)) {
            $saPageContent = $matches[1];
            $this->assertStringNotContainsString('onclick=', $saPageContent);
            $this->assertStringNotContainsString('onchange=', $saPageContent);
        }

        // Global onchange bernilai 0 di seluruh halaman
        $response->assertDontSee('onchange=', false);
    }

    public function test_dashboard_loads_external_javascript_without_inline_dashboard_script(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('superadmin.dashboard'));
        $response->assertOk();

        // Memuat file external superadmin-dashboard.js
        $response->assertSee('superadmin-dashboard.js', false);
        $response->assertSee('superadmin-dashboard.css', false);
    }

    public function test_dashboard_renders_complete_aria_tab_attributes(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('superadmin.dashboard'));
        $response->assertOk();

        // Roving tabindex & ARIA semantics
        $response->assertSee('role="tablist"', false);
        $response->assertSee('role="tabpanel"', false);
        $response->assertSee('aria-selected="true"', false);
        $response->assertSee('aria-selected="false"', false);
        $response->assertSee('id="btn-tab-ringkasan"', false);
        $response->assertSee('id="tab-ringkasan"', false);
        $response->assertSee('aria-controls="tab-ringkasan"', false);
    }

    public function test_inactive_tabpanels_have_hidden_attribute(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('superadmin.dashboard'));
        $response->assertOk();

        $content = $response->getContent();

        // Panel selain tab-ringkasan wajib memiliki atribut hidden
        $this->assertMatchesRegularExpression('/id="tab-seleksi"[^>]*hidden/', $content);
        $this->assertMatchesRegularExpression('/id="tab-bimbingan"[^>]*hidden/', $content);
        $this->assertMatchesRegularExpression('/id="tab-sistem"[^>]*hidden/', $content);

        // Panel aktif awal (tab-ringkasan) tidak memiliki hidden
        $this->assertDoesNotMatchRegularExpression('/id="tab-ringkasan"[^>]*hidden/', $content);
    }

    public function test_attention_banner_renders_clean_state_when_no_issues(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('superadmin.dashboard'));
        $response->assertOk();

        $response->assertSee('Tidak ada yang perlu ditindak saat ini');
    }

    public function test_attention_banner_renders_warning_when_queue_exists(): void
    {
        Pendaftaran::create([
            'user_id' => $this->peserta->id,
            'bulan_magang_id' => $this->bulan->id,
            'kuota_id' => $this->kuota->id,
            'durasi_bulan' => 3,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'status' => StatusPendaftaranEnum::Diajukan,
            'diajukan_at' => now(),
        ]);

        $response = $this->actingAs($this->superAdmin)->get(route('superadmin.dashboard'));
        $response->assertOk();

        $response->assertSee('Perhatian Antrean Pendaftaran');
        $response->assertDontSee('Tidak ada yang perlu ditindak saat ini');
    }

    public function test_honest_security_status_without_arbitrary_claims(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('superadmin.dashboard'));
        $response->assertOk();

        // Tidak boleh ada teks statis "Keamanan: Prima"
        $response->assertDontSee('Keamanan: Prima');
        $response->assertSee('Login gagal 24 jam');
    }

    public function test_guests_cannot_access_superadmin_dashboard(): void
    {
        $this->get(route('superadmin.dashboard'))->assertRedirect(route('login'));
    }

    public function test_unauthorized_roles_are_forbidden_from_superadmin_dashboard(): void
    {
        $this->actingAs($this->admin)->get(route('superadmin.dashboard'))->assertForbidden();
        $this->actingAs($this->pembimbing)->get(route('superadmin.dashboard'))->assertForbidden();
        $this->actingAs($this->peserta)->get(route('superadmin.dashboard'))->assertForbidden();
    }
}
