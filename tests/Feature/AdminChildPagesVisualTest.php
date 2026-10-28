<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JenjangEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusPendaftaranEnum;
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

class AdminChildPagesVisualTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $peserta;

    protected BulanMagang $bulan;

    protected Jurusan $jurusan;

    protected Kuota $kuota;

    protected Pendaftaran $pendaftaran;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(JenisDokumenSeeder::class);
        $this->seed(RubrikSeeder::class);

        $this->admin = new User;
        $this->admin->nama = 'Admin Pengelola';
        $this->admin->email = 'admin.operasional@slemankab.go.id';
        $this->admin->password = 'Password123!';
        $this->admin->role = RoleEnum::Admin;
        $this->admin->status_aktif = true;
        $this->admin->email_verified_at = now();
        $this->admin->save();

        $this->jurusan = Jurusan::create([
            'nama_jurusan' => 'Sistem Informasi',
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
            'jumlah' => 8,
            'terisi' => 2,
            'aktif' => true,
        ]);

        $this->peserta = new User;
        $this->peserta->nama = 'Citra Lestari';
        $this->peserta->email = 'citra@example.com';
        $this->peserta->password = 'Password123!';
        $this->peserta->role = RoleEnum::Peserta;
        $this->peserta->status_aktif = true;
        $this->peserta->email_verified_at = now();
        $this->peserta->save();

        PesertaProfil::create([
            'user_id' => $this->peserta->id,
            'nim_nisn' => '22110099',
            'institusi' => 'Universitas Negeri Yogyakarta',
            'jurusan_id' => $this->jurusan->id,
            'jenjang' => JenjangEnum::S1,
            'no_hp' => '081299887766',
            'alamat' => 'Sleman, DIY',
        ]);

        $this->pendaftaran = Pendaftaran::create([
            'user_id' => $this->peserta->id,
            'bulan_magang_id' => $this->bulan->id,
            'kuota_id' => $this->kuota->id,
            'nomor_pendaftaran' => 'REG-202610-0088',
            'asal_instansi' => 'Universitas Negeri Yogyakarta',
            'durasi_bulan' => 3,
            'status' => StatusPendaftaranEnum::Diajukan,
            'diajukan_at' => now()->subDays(4), // Menstimulasi SLA peringatan
        ]);
    }

    public function test_admin_can_view_pendaftaran_index_with_queue_metrics_and_filters(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.pendaftaran.index'));

        $response->assertStatus(200);
        $response->assertSee('Verifikasi Berkas Pendaftaran Peserta');
        $response->assertDontSee('Seleksi &amp; Verifikasi Berkas');
        $response->assertDontSee('Seleksi & Verifikasi Berkas');
        $response->assertSee('Citra Lestari');
        $response->assertSee($this->pendaftaran->nomor_pendaftaran);
        $response->assertSee('Tinjau Berkas');
        // Memastikan quick filter dan filter form ada
        $response->assertSee('Diajukan');
        $response->assertSee('Semua Status');

        // Memastikan halaman show menggunakan breadcrumb Verifikasi Berkas Pendaftaran Peserta yang konsisten
        $showResponse = $this->actingAs($this->admin)->get(route('admin.pendaftaran.show', $this->pendaftaran));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('Verifikasi Berkas Pendaftaran Peserta');
        $showResponse->assertDontSee('Antrean Verifikasi');
    }

    public function test_admin_can_view_kuota_index_with_hero_metrics_and_modals(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.kuota.index', [
            'bulan_magang_id' => $this->bulan->id,
        ]));

        $response->assertStatus(200);
        $response->assertSee('Manajemen Kuota Magang');
        $response->assertSee('Sistem Informasi');
        $response->assertSee('Okupansi:');
        $response->assertSee('modal-tambah-kuota');
        $response->assertSee('modal-edit-kuota');
        $response->assertSee('Alokasikan Kuota Jurusan');
    }
}
