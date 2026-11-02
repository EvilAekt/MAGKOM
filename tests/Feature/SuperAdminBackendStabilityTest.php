<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\Kuota;
use App\Models\Pengaturan;
use App\Models\User;
use Database\Seeders\JenisDokumenSeeder;
use Database\Seeders\RubrikSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class SuperAdminBackendStabilityTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected BulanMagang $bulan;

    protected Jurusan $jurusan;

    protected Kuota $kuota;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(JenisDokumenSeeder::class);
        $this->seed(RubrikSeeder::class);

        $this->superAdmin = User::factory()->create([
            'role' => RoleEnum::SuperAdmin,
            'status_aktif' => true,
            'email_verified_at' => now(),
            'must_change_password' => false,
        ]);

        $this->bulan = BulanMagang::create([
            'bulan_mulai' => now()->startOfMonth(),
            'tgl_buka' => now()->subDays(5)->toDateString(),
            'tgl_tutup' => now()->addDays(20)->toDateString(),
            'aktif' => true,
        ]);

        $this->jurusan = Jurusan::create([
            'nama_jurusan' => 'Teknik Komputer Realistis',
            'kategori' => 'mahasiswa',
            'aktif' => true,
        ]);

        $this->kuota = Kuota::create([
            'bulan_magang_id' => $this->bulan->id,
            'jurusan_id' => $this->jurusan->id,
            'jumlah' => 8,
            'terisi' => 3,
            'aktif' => true,
        ]);
    }

    /**
     * Memastikan kuota per jurusan di Tab Ringkasan mengambil data riil dari database.
     */
    public function test_dashboard_renders_real_database_kuota_and_not_dummy_array(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('superadmin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Teknik Komputer Realistis');
        $response->assertSee('3/8');
        // Memastikan sample kuota statik lama tidak lagi muncul
        $response->assertDontSee('DKV / Multimedia');
    }

    /**
     * Memastikan metrik nol tidak ter-fallback ke angka dummy seperti 4 atau 2.
     */
    public function test_dashboard_metrics_do_not_falsely_fallback_zero_to_dummy_numbers(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('superadmin.dashboard'));

        $response->assertStatus(200);
        // Antrean lewat SLA adalah 0, tidak boleh muncul '4 berkas lewat SLA'
        $response->assertSee('0 berkas lewat SLA');
        $response->assertDontSee('4 berkas lewat SLA');

        // Staf pending ganti password adalah 0, tidak boleh muncul '2 staf belum ganti sandi'
        $response->assertSee('0 staf belum ganti sandi');
        $response->assertDontSee('2 staf belum ganti sandi');
    }

    /**
     * Memastikan pembacaan waktu backup mendeteksi file backup nyata.
     */
    public function test_dashboard_detects_real_backup_file_timestamp(): void
    {
        $backupDir = storage_path('app/backups');
        File::ensureDirectoryExists($backupDir);

        $testFile = $backupDir.DIRECTORY_SEPARATOR.'backup-test-marker.sqlite';
        File::put($testFile, 'test snapshot content');
        touch($testFile, time());

        $expectedTime = date('H:i');

        $response = $this->actingAs($this->superAdmin)->get(route('superadmin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee($expectedTime);
        $response->assertDontSee('09:47');

        // Cleanup test file
        if (File::exists($testFile)) {
            File::delete($testFile);
        }
    }

    /**
     * Memastikan update pengaturan sistem dijalankan dalam DB transaction secara aman.
     */
    public function test_pengaturan_update_is_atomic_and_successful(): void
    {
        $payload = [
            'masa_tenggang_bulan' => 2,
            'kuota_umum_maks_per_bulan' => 20,
            'logbook_maks_mundur_hari' => 5,
            'berkas_maks_mb' => 4,
            'deadline_mendekati_hari' => 3,
            'retensi_audit_akses_data_hari' => 180,
            'batas_predikat_a' => 88,
            'batas_predikat_b' => 75,
            'batas_predikat_c' => 60,
            'batas_predikat_d' => 45,
            'fitur_sertifikat_aktif' => 1,
            'fitur_survei_aktif' => 1,
            'fitur_analitik_aktif' => 1,
        ];

        $response = $this->actingAs($this->superAdmin)->put(route('superadmin.pengaturan.update'), $payload);

        $response->assertRedirect(route('superadmin.pengaturan.index'));
        $response->assertSessionHas('success');

        $this->assertEquals('2', Pengaturan::get('masa_tenggang_bulan'));
        $this->assertEquals('20', Pengaturan::get('kuota_umum_maks_per_bulan'));
        $this->assertEquals('5', Pengaturan::get('logbook_maks_mundur_hari'));
        $this->assertEquals('4', Pengaturan::get('berkas_maks_mb'));
    }

    /**
     * Memastikan atribut data drawer dan data tren SVG hadir di view dashboard.
     */
    public function test_dashboard_renders_drawer_and_trend_dataset_attributes(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('superadmin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('data-trend-datasets=', false);
        $response->assertSee('id="drawerAuditDetail"', false);
        $response->assertSee('cv-dyn-user', false);
        $response->assertSee('cv-dyn-action', false);
    }
}
