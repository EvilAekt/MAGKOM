<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JalurEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\SumberPenempatanEnum;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\Kuota;
use App\Models\Penempatan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminBidangKonektivitasEndToEndTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $superAdmin;

    protected Bidang $bidangAptika;

    protected Bidang $bidangIkp;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'nama' => 'Admin Verifikator Sleman',
            'email' => 'admin.bidang@magkom.sleman.go.id',
            'role' => RoleEnum::Admin,
            'status_aktif' => true,
            'must_change_password' => false,
        ]);

        $this->superAdmin = User::factory()->create([
            'nama' => 'Super Admin Sleman',
            'email' => 'superadmin.bidang@magkom.sleman.go.id',
            'role' => RoleEnum::SuperAdmin,
            'status_aktif' => true,
            'must_change_password' => false,
        ]);

        $this->bidangAptika = Bidang::create([
            'nama_bidang' => 'Aplikasi Informatika & E-Government (EGOV)',
            'deskripsi' => 'Pengembangan ekosistem sistem digital dan SPBE.',
            'aktif' => true,
        ]);

        $this->bidangIkp = Bidang::create([
            'nama_bidang' => 'Informasi dan Komunikasi Publik (IKP)',
            'deskripsi' => 'Pengelolaan kanal komunikasi resmi dan peliputan berita.',
            'aktif' => true,
        ]);
    }

    public function test_admin_dapat_melihat_halaman_master_bidang_dan_kartu_metrik(): void
    {
        // Daftarkan satu staf pembimbing di bidang Aptika
        User::factory()->create([
            'nama' => 'Pembimbing Aptika',
            'email' => 'mentor.aptika@slemankab.go.id',
            'role' => RoleEnum::Pembimbing,
            'bidang_id' => $this->bidangAptika->id,
            'status_aktif' => true,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.bidang.index'));

        $response->assertOk();
        $response->assertSee('Master Bidang Dinas Kominfo');
        $response->assertSee('TOTAL BIDANG');
        $response->assertSee('BIDANG AKTIF');
        $response->assertSee('TOTAL PEMBIMBING');
        $response->assertSee('BIDANG ARSIP');
        $response->assertSee('Aplikasi Informatika &amp; E-Government (EGOV)', false);
        $response->assertSee('Informasi dan Komunikasi Publik (IKP)');
        $response->assertSee('1 Staf');
    }

    public function test_admin_dapat_mencari_dan_memfilter_data_bidang(): void
    {
        $bidangNonaktif = Bidang::create([
            'nama_bidang' => 'Bidang Arsip Lama',
            'deskripsi' => 'Unit kerja terdahulu yang sudah dinonaktifkan',
            'aktif' => false,
        ]);

        // 1. Uji Pencarian Keyword 'SPBE'
        $responseSearch = $this->actingAs($this->admin)->get(route('admin.bidang.index', ['q' => 'SPBE']));
        $responseSearch->assertOk();
        $responseSearch->assertSee('Aplikasi Informatika &amp; E-Government (EGOV)', false);
        $responseSearch->assertDontSee('Informasi dan Komunikasi Publik (IKP)');

        // 2. Uji Filter Status Nonaktif
        $responseStatus = $this->actingAs($this->admin)->get(route('admin.bidang.index', ['status' => 'nonaktif']));
        $responseStatus->assertOk();
        $responseStatus->assertSee('Bidang Arsip Lama');
        $responseStatus->assertDontSee('Aplikasi Informatika &amp; E-Government (EGOV)', false);
    }

    public function test_admin_dapat_menambahkan_bidang_baru_dengan_sanitasi_spasi(): void
    {
        $payload = [
            'nama_bidang' => '   Infrastruktur Teknologi Informasi (ITI)   ',
            'deskripsi' => '   Pengelolaan jaringan intra pemerintah daerah dan data center.   ',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.bidang.store'), $payload);

        $response->assertRedirect(route('admin.bidang.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('bidang', [
            'nama_bidang' => 'Infrastruktur Teknologi Informasi (ITI)',
            'deskripsi' => 'Pengelolaan jaringan intra pemerintah daerah dan data center.',
            'aktif' => true,
        ]);

        $this->assertDatabaseHas('audit_log', [
            'entitas' => 'bidang',
            'user_id' => $this->admin->id,
        ]);
    }

    public function test_admin_gagal_menambahkan_bidang_dengan_nama_duplikat(): void
    {
        $payload = [
            'nama_bidang' => 'Aplikasi Informatika & E-Government (EGOV)',
            'deskripsi' => 'Duplikasi nama bidang.',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.bidang.store'), $payload);

        $response->assertSessionHasErrors(['nama_bidang']);
        $this->assertEquals(2, Bidang::count());
    }

    public function test_admin_dapat_memperbarui_data_bidang(): void
    {
        $payload = [
            'nama_bidang' => 'Aplikasi Informatika & Transformasi Digital SPBE',
            'deskripsi' => 'Perluasan cakupan tugas dan fungsi digitalisasi SPBE.',
        ];

        $response = $this->actingAs($this->admin)->put(route('admin.bidang.update', $this->bidangAptika), $payload);

        $response->assertRedirect(route('admin.bidang.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('bidang', [
            'id' => $this->bidangAptika->id,
            'nama_bidang' => 'Aplikasi Informatika & Transformasi Digital SPBE',
            'deskripsi' => 'Perluasan cakupan tugas dan fungsi digitalisasi SPBE.',
        ]);

        $this->assertDatabaseHas('audit_log', [
            'entitas' => 'bidang',
            'entitas_id' => $this->bidangAptika->id,
            'user_id' => $this->admin->id,
        ]);
    }

    public function test_admin_dapat_toggle_aktif_dan_nonaktif_bidang_yang_tidak_memiliki_keterkaitan(): void
    {
        $bidangBaru = Bidang::create([
            'nama_bidang' => 'Bidang Sementara',
            'deskripsi' => 'Unit uji coba tanpa keterkaitan',
            'aktif' => true,
        ]);

        // 1. Nonaktifkan bidang
        $responseToggle = $this->actingAs($this->admin)->patch(route('admin.bidang.toggle', $bidangBaru));
        $responseToggle->assertRedirect(route('admin.bidang.index'));
        $responseToggle->assertSessionHas('success');
        $this->assertFalse($bidangBaru->fresh()->aktif);

        // 2. Aktifkan kembali bidang
        $responseAktifkan = $this->actingAs($this->admin)->patch(route('admin.bidang.toggle', $bidangBaru));
        $responseAktifkan->assertRedirect(route('admin.bidang.index'));
        $responseAktifkan->assertSessionHas('success');
        $this->assertTrue($bidangBaru->fresh()->aktif);
    }

    public function test_penonaktifan_bidang_ditolak_jika_masih_ada_staf_aktif_bertugas(): void
    {
        // Daftarkan staf aktif di bidang Aptika
        User::factory()->create([
            'nama' => 'Staf Bidang Aktif',
            'email' => 'staf.aktif@slemankab.go.id',
            'role' => RoleEnum::Pembimbing,
            'bidang_id' => $this->bidangAptika->id,
            'status_aktif' => true,
        ]);

        $response = $this->actingAs($this->admin)->patch(route('admin.bidang.toggle', $this->bidangAptika));

        $response->assertSessionHas('error', 'Penonaktifan ditolak: Masih terdapat staf aktif yang bertugas di bidang dinas ini.');
        $this->assertTrue($this->bidangAptika->fresh()->aktif);
    }

    public function test_penonaktifan_bidang_ditolak_jika_masih_ada_peserta_magang_aktif(): void
    {
        $jurusan = Jurusan::create([
            'nama_jurusan' => 'Teknik Komputer',
            'kategori' => 'mahasiswa',
            'aktif' => true,
        ]);

        $bulanMagang = BulanMagang::create([
            'nama' => 'Magang November 2026',
            'bulan_mulai' => '2026-11-01',
            'tgl_buka' => '2026-10-01',
            'tgl_tutup' => '2026-10-25',
            'status' => 'dibuka',
            'aktif' => true,
        ]);

        $kuota = Kuota::create([
            'bulan_magang_id' => $bulanMagang->id,
            'jurusan_id' => $jurusan->id,
            'jumlah' => 5,
            'terpakai' => 1,
            'aktif' => true,
        ]);

        $peserta = User::factory()->create([
            'role' => RoleEnum::Peserta,
            'status_aktif' => true,
        ]);

        $pembimbing = User::factory()->create([
            'role' => RoleEnum::Pembimbing,
            'bidang_id' => $this->bidangIkp->id,
            'status_aktif' => true,
        ]);

        Penempatan::create([
            'peserta_id' => $peserta->id,
            'kuota_id' => $kuota->id,
            'bidang_id' => $this->bidangIkp->id,
            'pembimbing_id' => $pembimbing->id,
            'status' => StatusPenempatanEnum::Aktif,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'tgl_mulai' => '2026-11-01',
            'tgl_selesai' => '2026-12-31',
        ]);

        $response = $this->actingAs($this->admin)->patch(route('admin.bidang.toggle', $this->bidangIkp));

        $response->assertSessionHas('error');
        $this->assertTrue($this->bidangIkp->fresh()->aktif);
    }

    public function test_superadmin_hanya_mode_lihat_dan_dilarang_melakukan_mutasi_bidang(): void
    {
        // 1. Akses GET: Berhasil dengan tampilan Read-Only Alert
        $responseGet = $this->actingAs($this->superAdmin)->get(route('admin.bidang.index'));
        $responseGet->assertOk();
        $responseGet->assertSee('Mode Lihat Saja (Read-Only)');

        // 2. Akses POST: Ditolak HTTP 403
        $responsePost = $this->actingAs($this->superAdmin)->post(route('admin.bidang.store'), [
            'nama_bidang' => 'Bidang Ilegal SuperAdmin',
        ]);
        $responsePost->assertForbidden();

        // 3. Akses PUT: Ditolak HTTP 403
        $responsePut = $this->actingAs($this->superAdmin)->put(route('admin.bidang.update', $this->bidangAptika), [
            'nama_bidang' => 'Upaya Modifikasi SuperAdmin',
        ]);
        $responsePut->assertForbidden();

        // 4. Akses PATCH Toggle: Ditolak HTTP 403
        $responseToggle = $this->actingAs($this->superAdmin)->patch(route('admin.bidang.toggle', $this->bidangAptika));
        $responseToggle->assertForbidden();
    }
}
