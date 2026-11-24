<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JalurEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusPendaftaranEnum;
use App\Enums\StatusPenempatanEnum;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\Kuota;
use App\Models\Pendaftaran;
use App\Models\Penempatan;
use App\Models\PesertaProfil;
use App\Models\SlotKhusus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminJurusanKonektivitasEndToEndTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $superAdmin;

    protected Jurusan $jurusanMahasiswa;

    protected Jurusan $jurusanSmk;

    protected BulanMagang $bulanMagang;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'nama' => 'Admin Verifikator',
            'email' => 'admin.jurusan@magkom.sleman.go.id',
            'role' => RoleEnum::Admin,
            'status_aktif' => true,
            'must_change_password' => false,
        ]);

        $this->superAdmin = User::factory()->create([
            'nama' => 'Super Admin Sleman',
            'email' => 'superadmin.jurusan@magkom.sleman.go.id',
            'role' => RoleEnum::SuperAdmin,
            'status_aktif' => true,
            'must_change_password' => false,
        ]);

        $this->jurusanMahasiswa = Jurusan::create([
            'nama_jurusan' => 'Teknik Informatika',
            'kategori' => 'mahasiswa',
            'aktif' => true,
        ]);

        $this->jurusanSmk = Jurusan::create([
            'nama_jurusan' => 'Rekayasa Perangkat Lunak',
            'kategori' => 'smk',
            'aktif' => true,
        ]);

        $this->bulanMagang = BulanMagang::create([
            'nama' => 'Magang November 2026',
            'bulan_mulai' => '2026-11-01',
            'tgl_buka' => '2026-10-01',
            'tgl_tutup' => '2026-10-25',
            'status' => 'dibuka',
            'aktif' => true,
        ]);
    }

    public function test_admin_dapat_melihat_halaman_master_jurusan_dan_metriknya(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.jurusan.index'));

        $response->assertOk();
        $response->assertSee('Master Data Jurusan');
        $response->assertSee('TOTAL JURUSAN');
        $response->assertSee('MAHASISWA / PT');
        $response->assertSee('SMK / VOKASI');
        $response->assertSee('STATUS AKTIF');
        $response->assertSee('Teknik Informatika');
        $response->assertSee('Rekayasa Perangkat Lunak');
    }

    public function test_admin_dapat_mencari_dan_memfilter_data_jurusan(): void
    {
        Jurusan::create([
            'nama_jurusan' => 'Ilmu Komunikasi Massa',
            'kategori' => 'mahasiswa',
            'aktif' => true,
        ]);

        Jurusan::create([
            'nama_jurusan' => 'Teknik Komputer Nonaktif',
            'kategori' => 'smk',
            'aktif' => false,
        ]);

        // 1. Filter keyword pencarian
        $responseSearch = $this->actingAs($this->admin)->get(route('admin.jurusan.index', ['q' => 'Komunikasi']));
        $responseSearch->assertOk();
        $responseSearch->assertSee('Ilmu Komunikasi Massa');
        $responseSearch->assertDontSee('Teknik Informatika');

        // 2. Filter kategori SMK
        $responseSmk = $this->actingAs($this->admin)->get(route('admin.jurusan.index', ['kategori' => 'smk']));
        $responseSmk->assertOk();
        $responseSmk->assertSee('Rekayasa Perangkat Lunak');
        $responseSmk->assertDontSee('Teknik Informatika');

        // 3. Filter status nonaktif
        $responseNonaktif = $this->actingAs($this->admin)->get(route('admin.jurusan.index', ['status' => '0']));
        $responseNonaktif->assertOk();
        $responseNonaktif->assertSee('Teknik Komputer Nonaktif');
        $responseNonaktif->assertDontSee('Teknik Informatika');
    }

    public function test_admin_dapat_menambah_jurusan_baru(): void
    {
        $payload = [
            'nama_jurusan' => ' Desain Komunikasi Visual ',
            'kategori' => 'mahasiswa',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.jurusan.store'), $payload);

        $response->assertRedirect(route('admin.jurusan.index'));
        $response->assertSessionHas('success');

        // Verifikasi tersimpan rapi dan ter-trim
        $this->assertDatabaseHas('jurusan', [
            'nama_jurusan' => 'Desain Komunikasi Visual',
            'kategori' => 'mahasiswa',
            'aktif' => true,
        ]);

        // Verifikasi tercatat di audit log
        $this->assertDatabaseHas('audit_log', [
            'entitas' => 'jurusan',
            'user_id' => $this->admin->id,
        ]);
    }

    public function test_validasi_menolak_nama_jurusan_duplikat(): void
    {
        $payload = [
            'nama_jurusan' => 'Teknik Informatika', // Sudah ada
            'kategori' => 'mahasiswa',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.jurusan.store'), $payload);

        $response->assertSessionHasErrors(['nama_jurusan']);
    }

    public function test_validasi_menolak_kategori_jurusan_tidak_valid(): void
    {
        $payload = [
            'nama_jurusan' => 'Jurusan Tidak Valid',
            'kategori' => 'doktoral', // Tidak diizinkan
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.jurusan.store'), $payload);

        $response->assertSessionHasErrors(['kategori']);
    }

    public function test_admin_dapat_mengubah_nama_dan_kategori_jurusan(): void
    {
        $payload = [
            'nama_jurusan' => 'Rekayasa Perangkat Lunak & Gim',
            'kategori' => 'smk',
        ];

        $response = $this->actingAs($this->admin)->put(route('admin.jurusan.update', $this->jurusanSmk), $payload);

        $response->assertRedirect(route('admin.jurusan.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('jurusan', [
            'id' => $this->jurusanSmk->id,
            'nama_jurusan' => 'Rekayasa Perangkat Lunak & Gim',
            'kategori' => 'smk',
        ]);

        $this->assertDatabaseHas('audit_log', [
            'entitas' => 'jurusan',
            'entitas_id' => $this->jurusanSmk->id,
            'user_id' => $this->admin->id,
        ]);
    }

    public function test_admin_dapat_toggle_aktif_nonaktif_jurusan_tanpa_keterkaitan(): void
    {
        $jurusanBebas = Jurusan::create([
            'nama_jurusan' => 'Sistem Informasi Geografis',
            'kategori' => 'mahasiswa',
            'aktif' => true,
        ]);

        // 1. Nonaktifkan
        $response = $this->actingAs($this->admin)->patch(route('admin.jurusan.toggle', $jurusanBebas));
        $response->assertRedirect(route('admin.jurusan.index'));
        $response->assertSessionHas('success');
        $this->assertFalse($jurusanBebas->fresh()->aktif);

        // 2. Aktifkan kembali
        $response2 = $this->actingAs($this->admin)->patch(route('admin.jurusan.toggle', $jurusanBebas));
        $response2->assertRedirect(route('admin.jurusan.index'));
        $response2->assertSessionHas('success');
        $this->assertTrue($jurusanBebas->fresh()->aktif);
    }

    public function test_penonaktifan_jurusan_ditolak_jika_ada_alokasi_kuota_aktif_br_12(): void
    {
        Kuota::create([
            'bulan_magang_id' => $this->bulanMagang->id,
            'jurusan_id' => $this->jurusanMahasiswa->id,
            'jumlah' => 5,
            'terisi' => 0,
            'aktif' => true,
        ]);

        $response = $this->actingAs($this->admin)->patch(route('admin.jurusan.toggle', $this->jurusanMahasiswa));

        $response->assertSessionHas('error', 'Penonaktifan ditolak: Masih terdapat alokasi kuota aktif untuk jurusan ini.');
        $this->assertTrue($this->jurusanMahasiswa->fresh()->aktif);
    }

    public function test_penonaktifan_jurusan_ditolak_jika_ada_pendaftaran_aktif_br_12(): void
    {
        $kuota = Kuota::create([
            'bulan_magang_id' => $this->bulanMagang->id,
            'jurusan_id' => $this->jurusanMahasiswa->id,
            'jumlah' => 5,
            'terisi' => 1,
            'aktif' => false, // Nonaktifkan kuota agar tidak tertahan di guard kuota
        ]);

        $peserta = User::factory()->create(['role' => RoleEnum::Peserta, 'status_aktif' => true]);
        PesertaProfil::create([
            'user_id' => $peserta->id,
            'nim_nisn' => '12345678',
            'institusi' => 'UGM',
            'jurusan_id' => $this->jurusanMahasiswa->id,
            'jenjang' => 's1',
            'no_hp' => '081234567890',
        ]);

        Pendaftaran::create([
            'user_id' => $peserta->id,
            'bulan_magang_id' => $this->bulanMagang->id,
            'kuota_id' => $kuota->id,
            'durasi_bulan' => 3,
            'status' => StatusPendaftaranEnum::Diajukan,
            'tanggal_daftar' => now(),
        ]);

        $response = $this->actingAs($this->admin)->patch(route('admin.jurusan.toggle', $this->jurusanMahasiswa));

        $response->assertSessionHas('error', 'Penonaktifan ditolak: Masih terdapat pendaftaran aktif pada jurusan ini.');
        $this->assertTrue($this->jurusanMahasiswa->fresh()->aktif);
    }

    public function test_penonaktifan_jurusan_ditolak_jika_ada_penempatan_aktif_reguler_maupun_khusus_br_12(): void
    {
        $pembimbing = User::factory()->create(['role' => RoleEnum::Pembimbing, 'status_aktif' => true]);
        $bidang = Bidang::create(['nama_bidang' => 'Aplikasi Informatika', 'aktif' => true]);
        $slotKhusus = SlotKhusus::create([
            'bulan_mulai' => '2026-11-01',
            'jumlah' => 5,
            'terisi' => 1,
            'aktif' => true,
            'dibuat_oleh' => $this->admin->id,
        ]);

        // Peserta Magang Jalur Khusus dengan jurusan Mahasiswa (kuota_id = null)
        $pesertaKhusus = User::factory()->create(['role' => RoleEnum::Peserta, 'status_aktif' => true]);
        PesertaProfil::create([
            'user_id' => $pesertaKhusus->id,
            'nim_nisn' => '9988776655',
            'institusi' => 'Politeknik Siber',
            'jurusan_id' => $this->jurusanMahasiswa->id,
            'jenjang' => 's1',
            'no_hp' => '081299887766',
        ]);

        Penempatan::create([
            'peserta_id' => $pesertaKhusus->id,
            'slot_khusus_id' => $slotKhusus->id,
            'kuota_id' => null,
            'pembimbing_id' => $pembimbing->id,
            'bidang_id' => $bidang->id,
            'jalur' => JalurEnum::Khusus,
            'sumber' => 'manual',
            'dibuat_oleh' => $this->admin->id,
            'tgl_mulai' => '2026-11-01',
            'tgl_selesai' => '2027-01-31',
            'status' => StatusPenempatanEnum::Aktif,
        ]);

        $response = $this->actingAs($this->admin)->patch(route('admin.jurusan.toggle', $this->jurusanMahasiswa));

        $response->assertSessionHas('error', 'Penonaktifan ditolak: Masih terdapat peserta magang aktif pada jurusan ini.');
        $this->assertTrue($this->jurusanMahasiswa->fresh()->aktif);
    }

    public function test_superadmin_hanya_mode_lihat_saja_dan_dilarang_menambah_atau_mengubah_jurusan_sec_12(): void
    {
        // 1. Mode Lihat Saja di view
        $responseView = $this->actingAs($this->superAdmin)->get(route('admin.jurusan.index'));
        $responseView->assertOk();
        $responseView->assertSee('Mode Lihat Saja (Read-Only)');

        // 2. Dilarang menambah jurusan (403 Forbidden)
        $responseStore = $this->actingAs($this->superAdmin)->post(route('admin.jurusan.store'), [
            'nama_jurusan' => 'Jurusan Terlarang SuperAdmin',
            'kategori' => 'mahasiswa',
        ]);
        $responseStore->assertForbidden();

        // 3. Dilarang mengubah jurusan (403 Forbidden)
        $responseUpdate = $this->actingAs($this->superAdmin)->put(route('admin.jurusan.update', $this->jurusanMahasiswa), [
            'nama_jurusan' => 'Perubahan Terlarang',
            'kategori' => 'mahasiswa',
        ]);
        $responseUpdate->assertForbidden();

        // 4. Dilarang toggle aktif jurusan (403 Forbidden)
        $responseToggle = $this->actingAs($this->superAdmin)->patch(route('admin.jurusan.toggle', $this->jurusanMahasiswa));
        $responseToggle->assertForbidden();
    }
}
