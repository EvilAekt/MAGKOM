<?php

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\Kuota;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminKuotaRevampTest extends TestCase
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

    private function createAdminUser(): User
    {
        $admin = new User();
        $admin->nama = 'Admin Kuota Test';
        $admin->email = 'admin.kuota.revamp@slemankab.go.id';
        $admin->password = 'Password123!';
        $admin->role = RoleEnum::Admin;
        $admin->status_aktif = true;
        $admin->must_change_password = false;
        $admin->email_verified_at = now();
        $admin->save();

        return $admin;
    }

    /**
     * Memastikan header baru memuat judul Bulan & kuota serta tombol aksi Ubah bulan dan Buat bulan baru.
     */
    public function test_kuota_page_displays_redesigned_header_and_hero_banner(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)->get(route('admin.kuota.index'));
        $response->assertStatus(200);

        // Header halaman
        $response->assertSee('Bulan &amp; kuota', false);
        $response->assertSee('Atur bulan pendaftaran dan kuota magang per bidang.', false);
        $response->assertSee('Ubah bulan', false);
        $response->assertSee('Buat bulan baru', false);

        // Hero Banner
        $response->assertSee('Bulan Aktif', false);
        $response->assertSee('Tanggal buka', false);
        $response->assertSee('Tanggal tutup', false);
        $response->assertSee('Mulai magang', false);
        $response->assertSee('Kuota terisi', false);
    }

    /**
     * Memastikan tabel menampilkan agregasi Kuota per Bidang dengan badge jenjang dan tombol Ubah.
     */
    public function test_kuota_page_displays_quota_per_bidang_table(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)->get(route('admin.kuota.index'));
        $response->assertStatus(200);

        // Section header
        $response->assertSee('Kuota per bidang', false);
        $response->assertSee('Tambah kuota', false);

        // Kolom Thead
        $response->assertSee('Bidang', false);
        $response->assertSee('Jurusan yang Dibutuhkan', false);
        $response->assertSee('Jenjang', false);
        $response->assertSee('Durasi', false);
        $response->assertSee('Kuota Terisi', false);
        $response->assertSee('Kapasitas', false);

        // Memastikan tombol Ubah di baris data
        $response->assertSee('Ubah', false);

        // Memastikan 4 bidang alias dan inisial tampil
        $response->assertSee('Aplikasi Informatika', false);
        $response->assertSee('Statistik', false);
        $response->assertSee('Persandian', false);
        $response->assertSee('Sekretariat', false);
    }

    /**
     * Memastikan info validasi kapasitas pembimbing tampil di bawah tabel.
     */
    public function test_kuota_page_displays_mentor_capacity_validation_banner(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)->get(route('admin.kuota.index'));
        $response->assertStatus(200);

        $response->assertSee('Sistem otomatis memvalidasi agar total kuota tidak melebihi kapasitas bimbingan, yaitu jumlah Pembimbing aktif di setiap bidang.', false);
    }

    /**
     * Memastikan modal-modal interaktif ter-render di dokumen.
     */
    public function test_kuota_modals_exist_in_dom(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)->get(route('admin.kuota.index'));
        $response->assertStatus(200);

        $response->assertSee('id="modalUbahBulan"', false);
        $response->assertSee('id="modalBuatBulan"', false);
        $response->assertSee('id="modalTambahKuota"', false);
        $response->assertSee('id="modalEditBidang"', false);
    }

    /**
     * Memastikan admin dapat membuat bulan magang baru via storeBulan.
     */
    public function test_admin_can_store_new_bulan_magang(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)->post(route('admin.kuota.bulan.store'), [
            'bulan_mulai' => '2027-01-01',
            'tgl_buka' => '2026-12-01',
            'tgl_tutup' => '2026-12-20',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('bulan_magang', [
            'bulan_mulai' => '2027-01-01 00:00:00',
            'tgl_buka' => '2026-12-01 00:00:00',
            'tgl_tutup' => '2026-12-20 00:00:00',
        ]);
    }

    /**
     * Memastikan admin dapat memperbarui jadwal bulan magang aktif via updateBulan.
     */
    public function test_admin_can_update_existing_bulan_magang(): void
    {
        $admin = $this->createAdminUser();
        $bulan = BulanMagang::first();

        $response = $this->actingAs($admin)->put(route('admin.kuota.bulan.update', $bulan->id), [
            'bulan_mulai' => $bulan->bulan_mulai->format('Y-m-d'),
            'tgl_buka' => '2026-10-05',
            'tgl_tutup' => '2026-10-30',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('bulan_magang', [
            'id' => $bulan->id,
            'tgl_buka' => '2026-10-05 00:00:00',
            'tgl_tutup' => '2026-10-30 00:00:00',
        ]);
    }
}
