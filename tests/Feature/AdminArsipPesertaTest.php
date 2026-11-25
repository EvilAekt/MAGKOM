<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JalurEnum;
use App\Enums\JenjangEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\SumberPenempatanEnum;
use App\Enums\TipeAuditEnum;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\Kuota;
use App\Models\Penempatan;
use App\Models\PesertaProfil;
use App\Models\User;
use Database\Seeders\JenisDokumenSeeder;
use Database\Seeders\RubrikSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminArsipPesertaTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected User $admin;

    protected User $pembimbing;

    protected User $peserta;

    protected Bidang $bidang;

    protected Jurusan $jurusan;

    protected BulanMagang $bulan;

    protected Kuota $kuota;

    protected Penempatan $penempatanSelesai;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(JenisDokumenSeeder::class);
        $this->seed(RubrikSeeder::class);

        // 1. Master Data
        $this->bidang = Bidang::create([
            'nama_bidang' => 'Bidang Aplikasi Informatika (Aptika)',
            'deskripsi' => 'Pengembangan sistem informasi dan smart city',
            'aktif' => true,
        ]);

        $this->jurusan = Jurusan::create([
            'nama_jurusan' => 'Teknik Informatika',
            'kategori' => 'mahasiswa',
            'aktif' => true,
        ]);

        $this->bulan = BulanMagang::create([
            'bulan_mulai' => now()->startOfMonth(),
            'tgl_buka' => now()->subDays(10)->toDateString(),
            'tgl_tutup' => now()->addDays(15)->toDateString(),
            'aktif' => true,
        ]);

        $this->kuota = Kuota::create([
            'bulan_magang_id' => $this->bulan->id,
            'jurusan_id' => $this->jurusan->id,
            'jumlah' => 20,
            'terisi' => 1,
            'aktif' => true,
        ]);

        // 2. Akun Pengguna
        $this->superAdmin = User::factory()->create([
            'nama' => 'Super Administrator',
            'email' => 'superadmin@slemankab.go.id',
            'role' => RoleEnum::SuperAdmin,
            'status_aktif' => true,
            'must_change_password' => false,
            'email_verified_at' => now(),
        ]);

        $this->admin = User::factory()->create([
            'nama' => 'Admin Verifikator',
            'email' => 'admin@slemankab.go.id',
            'role' => RoleEnum::Admin,
            'status_aktif' => true,
            'must_change_password' => false,
            'email_verified_at' => now(),
        ]);

        $this->pembimbing = User::factory()->create([
            'nama' => 'Pembimbing Lapangan Aptika',
            'email' => 'pembimbing@slemankab.go.id',
            'role' => RoleEnum::Pembimbing,
            'bidang_id' => $this->bidang->id,
            'status_aktif' => true,
            'must_change_password' => false,
            'email_verified_at' => now(),
        ]);

        $this->peserta = User::factory()->create([
            'nama' => 'Budi Santoso Alumni',
            'email' => 'budi.santoso@example.com',
            'role' => RoleEnum::Peserta,
            'status_aktif' => true,
            'must_change_password' => false,
            'email_verified_at' => now(),
        ]);

        PesertaProfil::create([
            'user_id' => $this->peserta->id,
            'nim_nisn' => '1234567890',
            'institusi' => 'Universitas Gadjah Mada',
            'jurusan_id' => $this->jurusan->id,
            'jenjang' => JenjangEnum::S1,
            'no_hp' => '081234567891',
            'alamat' => 'Kabupaten Sleman, D.I. Yogyakarta',
        ]);

        $this->penempatanSelesai = Penempatan::create([
            'peserta_id' => $this->peserta->id,
            'kuota_id' => $this->kuota->id,
            'pembimbing_id' => $this->pembimbing->id,
            'bidang_id' => $this->bidang->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'dibuat_oleh' => $this->admin->id,
            'tgl_mulai' => now()->subMonths(3)->toDateString(),
            'tgl_selesai' => now()->subDay()->toDateString(),
            'status' => StatusPenempatanEnum::Selesai,
        ]);
    }

    public function test_admin_dapat_mengakses_halaman_index_arsip_peserta(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.arsip.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.arsip.index');
        $response->assertSee('Arsip &amp; Rekam Jejak Alumni Magang', false);
        $response->assertSee('TOTAL ARSIP');
        $response->assertSee('SELESAI MAGANG');
        $response->assertSee('Budi Santoso Alumni');
        $response->assertSee('Universitas Gadjah Mada');
        $response->assertSee(route('admin.arsip.show', $this->penempatanSelesai));
    }

    public function test_admin_dapat_mencari_dan_memfilter_arsip_peserta(): void
    {
        // Buat peserta kedua dengan status nonaktif
        $pesertaNonaktif = User::factory()->create([
            'nama' => 'Siti Aminah Nonaktif',
            'email' => 'siti.aminah@example.com',
            'role' => RoleEnum::Peserta,
            'status_aktif' => false,
            'must_change_password' => false,
            'email_verified_at' => now(),
        ]);

        PesertaProfil::create([
            'user_id' => $pesertaNonaktif->id,
            'nim_nisn' => '9988776655',
            'institusi' => 'Universitas Negeri Yogyakarta',
            'jurusan_id' => $this->jurusan->id,
            'jenjang' => JenjangEnum::S1,
            'no_hp' => '081299887711',
            'alamat' => 'Kabupaten Sleman, D.I. Yogyakarta',
        ]);

        $penempatanNonaktif = Penempatan::create([
            'peserta_id' => $pesertaNonaktif->id,
            'kuota_id' => $this->kuota->id,
            'pembimbing_id' => $this->pembimbing->id,
            'bidang_id' => $this->bidang->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'dibuat_oleh' => $this->admin->id,
            'tgl_mulai' => now()->subMonths(4)->toDateString(),
            'tgl_selesai' => now()->subMonths(1)->toDateString(),
            'status' => StatusPenempatanEnum::Nonaktif,
        ]);

        // Filter berdasarkan status nonaktif
        $resFilterStatus = $this->actingAs($this->admin)->get(route('admin.arsip.index', ['status' => 'nonaktif']));
        $resFilterStatus->assertStatus(200);
        $resFilterStatus->assertSee('Siti Aminah Nonaktif');
        $resFilterStatus->assertDontSee('Budi Santoso Alumni');

        // Filter berdasarkan kata kunci nama
        $resFilterSearch = $this->actingAs($this->admin)->get(route('admin.arsip.index', ['search' => 'Budi']));
        $resFilterSearch->assertStatus(200);
        $resFilterSearch->assertSee('Budi Santoso Alumni');
        $resFilterSearch->assertDontSee('Siti Aminah Nonaktif');
    }

    public function test_admin_dapat_melihat_detail_arsip_peserta_dan_mencatat_audit_log(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.arsip.show', $this->penempatanSelesai));

        $response->assertStatus(200);
        $response->assertViewIs('admin.arsip.show');
        $response->assertSee('Detail Arsip Alumni Magang');
        $response->assertSee('Budi Santoso Alumni');
        $response->assertSee('1234567890');
        $response->assertSee('Pembimbing Lapangan Aptika');
        $response->assertSee(route('admin.penempatan.surat-diterima', $this->penempatanSelesai));
        $response->assertSee(route('admin.arsip.index'));

        // Pastikan jejak audit trail tercatat
        $this->assertDatabaseHas('audit_log', [
            'tipe' => TipeAuditEnum::AksesData->value,
            'entitas' => 'penempatan',
            'entitas_id' => $this->penempatanSelesai->id,
            'user_id' => $this->admin->id,
        ]);
    }

    public function test_arsip_peserta_admin_membatasi_sepuluh_entri_per_halaman(): void
    {
        // Buat 12 penempatan selesai tambahan (total 13)
        for ($i = 1; $i <= 12; $i++) {
            $userDummy = User::factory()->create([
                'nama' => "Peserta Dummy {$i}",
                'email' => "peserta.dummy{$i}@example.com",
                'role' => RoleEnum::Peserta,
                'status_aktif' => true,
                'must_change_password' => false,
                'email_verified_at' => now(),
            ]);

            Penempatan::create([
                'peserta_id' => $userDummy->id,
                'kuota_id' => $this->kuota->id,
                'pembimbing_id' => $this->pembimbing->id,
                'bidang_id' => $this->bidang->id,
                'jalur' => JalurEnum::Umum,
                'sumber' => SumberPenempatanEnum::Online,
                'dibuat_oleh' => $this->admin->id,
                'tgl_mulai' => now()->subMonths(3)->toDateString(),
                'tgl_selesai' => now()->subDay()->toDateString(),
                'status' => StatusPenempatanEnum::Selesai,
            ]);
        }

        $response = $this->actingAs($this->admin)->get(route('admin.arsip.index'));
        $response->assertStatus(200);
        $response->assertViewHas('arsipList', function ($list) {
            return $list->count() === 10 && $list->total() === 13;
        });
    }

    public function test_role_peserta_dan_pembimbing_dilarang_mengakses_arsip_admin(): void
    {
        // Peserta dilarang
        $resPeserta = $this->actingAs($this->peserta)->get(route('admin.arsip.index'));
        $resPeserta->assertStatus(403);

        // Pembimbing dilarang
        $resPembimbing = $this->actingAs($this->pembimbing)->get(route('admin.arsip.index'));
        $resPembimbing->assertStatus(403);
    }

    public function test_detail_arsip_admin_mengembalikan_404_jika_penempatan_belum_selesai(): void
    {
        $penempatanAktif = Penempatan::create([
            'peserta_id' => $this->peserta->id,
            'kuota_id' => $this->kuota->id,
            'pembimbing_id' => $this->pembimbing->id,
            'bidang_id' => $this->bidang->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'dibuat_oleh' => $this->admin->id,
            'tgl_mulai' => now()->toDateString(),
            'tgl_selesai' => now()->addMonths(2)->toDateString(),
            'status' => StatusPenempatanEnum::Aktif,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.arsip.show', $penempatanAktif));
        $response->assertStatus(404);
    }
}
