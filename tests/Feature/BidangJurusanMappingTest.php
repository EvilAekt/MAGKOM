<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Models\Bidang;
use App\Models\Jurusan;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BidangJurusanMappingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::parse('2026-10-08 10:00:00', 'Asia/Jakarta'));
        CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-10-08 10:00:00', 'Asia/Jakarta'));

        $this->seed();
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        CarbonImmutable::setTestNow();
        parent::tearDown();
    }

    private function createAdminUser(): User
    {
        $admin = new User;
        $admin->nama = 'Admin Bidang Jurusan Test';
        $admin->email = 'admin.mapping.test@slemankab.go.id';
        $admin->password = 'Password123!';
        $admin->role = RoleEnum::Admin;
        $admin->status_aktif = true;
        $admin->must_change_password = false;
        $admin->email_verified_at = now();
        $admin->save();

        return $admin;
    }

    /**
     * Memastikan IKP terhubung tepat dengan 4 jurusan yang dibutuhkan.
     */
    public function test_ikp_has_exact_required_jurusans(): void
    {
        $ikp = Bidang::where('nama_bidang', 'like', '%IKP%')->firstOrFail();
        $jurusans = $ikp->jurusan->pluck('nama_jurusan')->toArray();

        $expected = [
            'Statistik',
            'Ilmu Komunikasi',
            'Desain Grafis',
            'Komunikasi Penyiaran Islam',
        ];

        $this->assertCount(4, $jurusans);
        foreach ($expected as $exp) {
            $this->assertContains($exp, $jurusans);
        }
    }

    /**
     * Memastikan EGOV terhubung tepat dengan 4 jurusan yang dibutuhkan.
     */
    public function test_egov_has_exact_required_jurusans(): void
    {
        $egov = Bidang::where('nama_bidang', 'like', '%EGOV%')->firstOrFail();
        $jurusans = $egov->jurusan->pluck('nama_jurusan')->toArray();

        $expected = [
            'Informatika',
            'Manajemen Informatika',
            'Sistem Informasi',
            'Teknologi Informasi',
        ];

        $this->assertCount(4, $jurusans);
        foreach ($expected as $exp) {
            $this->assertContains($exp, $jurusans);
        }
    }

    /**
     * Memastikan ITI terhubung tepat dengan 3 jurusan yang dibutuhkan.
     */
    public function test_iti_has_exact_required_jurusans(): void
    {
        $iti = Bidang::where('nama_bidang', 'like', '%ITI%')->firstOrFail();
        $jurusans = $iti->jurusan->pluck('nama_jurusan')->toArray();

        $this->assertCount(3, $jurusans);
        $this->assertContains('Informatika', $jurusans);
        $this->assertContains('Teknik Komputer dan Jaringan (TKJ)', $jurusans);
        $this->assertContains('Teknologi Informasi', $jurusans);
    }

    /**
     * Memastikan Sekretariat terhubung tepat dengan 8 jurusan yang dibutuhkan.
     */
    public function test_sekretariat_has_exact_required_jurusans(): void
    {
        $sekretariat = Bidang::where('nama_bidang', 'like', '%Sekretariat%')->firstOrFail();
        $jurusans = $sekretariat->jurusan->pluck('nama_jurusan')->toArray();

        $expected = [
            'Ilmu Komunikasi',
            'Komunikasi Penyiaran Islam',
            'Desain Grafis',
            'Ekonomi dan Sosial (KPE)',
            'Administrasi Perkantoran',
            'Manajemen Perkantoran',
            'Ilmu Kearsipan',
            'Manajemen Rekod',
        ];

        $this->assertCount(8, $jurusans);
        foreach ($expected as $exp) {
            $this->assertContains($exp, $jurusans);
        }
    }

    /**
     * Memastikan halaman manajemen kuota merender bidangList dan bidangJsonData secara akurat.
     */
    public function test_admin_kuota_index_renders_bidang_and_jurusans_correctly(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)->get(route('admin.kuota.index'));
        $response->assertStatus(200);

        // Memastikan 4 nama bidang tampil di tabel agregasi kuota
        $response->assertSee('Aplikasi Informatika (EGOV)', false);
        $response->assertSee('Informasi &amp; Komunikasi Publik (IKP)', false);
        $response->assertSee('Infrastruktur TI &amp; Persandian (ITI)', false);
        $response->assertSee('Sekretariat', false);

        // Memastikan jurusan-jurusan tampil di modal atau baris data
        $response->assertSee('Statistik', false);
        $response->assertSee('Ilmu Komunikasi', false);
        $response->assertSee('Informatika', false);
        $response->assertSee('Teknologi Informasi', false);
        $response->assertSee('Ilmu Kearsipan', false);
        $response->assertSee('Manajemen Rekod', false);
        $response->assertSee('Administrasi Perkantoran', false);
        $response->assertSee('Manajemen Perkantoran', false);
    }

    /**
     * Memastikan portal publik / landing page merender badge bidang penempatan pada jurusan terkait.
     */
    public function test_landing_page_renders_bidang_badges_for_majors(): void
    {
        $response = $this->get(route('landing'));
        $response->assertStatus(200);

        // Memastikan chip bidang ada di kartu-kartu kejuruan landing page
        $response->assertSee('bidang-chip-ikp', false);
        $response->assertSee('bidang-chip-egov', false);
        $response->assertSee('bidang-chip-iti', false);
        $response->assertSee('bidang-chip-sekretariat', false);
    }
}
