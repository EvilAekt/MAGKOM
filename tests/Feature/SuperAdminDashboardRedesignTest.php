<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Models\BulanMagang;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperAdminDashboardRedesignTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(RoleEnum $role, array $extra = []): User
    {
        $user = new User;
        $user->nama = 'Pengguna '.$role->value;
        $user->email = $role->value.'.redesign@slemankab.go.id';
        $user->password = bcrypt('Password123!');
        $user->role = $role;
        $user->status_aktif = true;
        $user->email_verified_at = now();
        $user->must_change_password = false;
        foreach ($extra as $key => $value) {
            $user->{$key} = $value;
        }
        $user->save();

        return $user;
    }

    public function test_dashboard_renders_four_kpi_cards_and_four_tabs(): void
    {
        $response = $this->actingAs($this->makeUser(RoleEnum::SuperAdmin))->get(route('superadmin.dashboard'));

        $response->assertOk();
        foreach (['Total pengguna', 'Menunggu verifikasi', 'Peserta aktif', 'Akun nonaktif'] as $label) {
            $response->assertSee($label);
        }
        foreach (['Ringkasan Utama', 'Seleksi & Kuota', 'Pelaksanaan & Bimbingan', 'Tata Kelola Sistem & Laporan'] as $tab) {
            $response->assertSee($tab, false);
        }
        foreach (['ringkasan', 'seleksi', 'bimbingan', 'sistem'] as $name) {
            $response->assertSee('data-tab="'.$name.'"', false);
            $response->assertSee('id="tab-'.$name.'"', false);
        }
    }

    public function test_perlu_tindakan_menampilkan_hanya_item_dengan_jumlah_lebih_dari_nol(): void
    {
        $this->makeUser(RoleEnum::Admin, ['must_change_password' => true]);
        $response = $this->actingAs($this->makeUser(RoleEnum::SuperAdmin))->get(route('superadmin.dashboard'));

        $response->assertOk();
        $items = $response->viewData('perluTindakan');
        $this->assertSame(['Staf belum ganti sandi'], $items->pluck('label')->all());
        $response->assertSee('Staf belum ganti sandi');
        $response->assertDontSee('Antrean verifikasi');
        $response->assertDontSee('Waiting list');
    }

    public function test_perlu_tindakan_kosong_menampilkan_status_aman(): void
    {
        $response = $this->actingAs($this->makeUser(RoleEnum::SuperAdmin))->get(route('superadmin.dashboard'));

        $this->assertTrue($response->viewData('perluTindakan')->isEmpty());
        $response->assertSee('Tidak ada yang perlu ditindaklanjuti');
    }

    public function test_dashboard_tanpa_bulan_aktif_tidak_error(): void
    {
        $this->assertSame(0, BulanMagang::count());

        $this->actingAs($this->makeUser(RoleEnum::SuperAdmin))
            ->get(route('superadmin.dashboard'))
            ->assertOk()
            ->assertSee('Belum ada periode bulan magang aktif.');
    }

    public function test_dashboard_memuat_js_eksternal_dan_partial_bebas_handler_inline(): void
    {
        $html = $this->actingAs($this->makeUser(RoleEnum::SuperAdmin))
            ->get(route('superadmin.dashboard'))
            ->assertOk()
            ->assertSee('js/superadmin-dashboard.js', false)
            ->getContent();

        $this->assertStringNotContainsString('switchDashboardTab', $html);

        // Layout punya handler sendiri (di luar lingkup); yang diperiksa adalah file dashboard.
        $files = array_merge(
            [resource_path('views/superadmin/dashboard.blade.php')],
            glob(resource_path('views/superadmin/dashboard/partials/*.blade.php'))
        );
        foreach ($files as $file) {
            $this->assertDoesNotMatchRegularExpression(
                '/\son(click|change|submit)=|javascript:|<script>/i',
                (string) file_get_contents($file),
                basename($file).' memakai script/handler inline (diblokir CSP produksi)'
            );
        }
    }

    public function test_non_superadmin_tetap_dilarang(): void
    {
        $this->actingAs($this->makeUser(RoleEnum::Admin))
            ->get(route('superadmin.dashboard'))
            ->assertForbidden();
    }
}
