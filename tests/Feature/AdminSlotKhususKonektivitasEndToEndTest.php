<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JalurEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusPenempatanEnum;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\Penempatan;
use App\Models\Pengaturan;
use App\Models\SlotKhusus;
use App\Models\User;
use App\Services\PlacementService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminSlotKhususKonektivitasEndToEndTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $superAdmin;

    protected User $pembimbing;

    protected Bidang $bidang;

    protected Jurusan $jurusan;

    protected BulanMagang $bulanMagang;

    protected SlotKhusus $slotKhusus;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bidang = Bidang::create([
            'nama_bidang' => 'Aplikasi Informatika & E-Government',
            'kode_bidang' => 'EGOV',
            'aktif' => true,
        ]);

        $this->jurusan = Jurusan::create([
            'nama_jurusan' => 'Teknik Informatika',
            'kategori' => 'mahasiswa',
            'aktif' => true,
        ]);

        $this->admin = User::factory()->create([
            'nama' => 'Admin Verifikator',
            'email' => 'admin.verif@magkom.sleman.go.id',
            'role' => RoleEnum::Admin,
            'status_aktif' => true,
            'must_change_password' => false,
        ]);

        $this->superAdmin = User::factory()->create([
            'nama' => 'Super Admin Sleman',
            'email' => 'superadmin@magkom.sleman.go.id',
            'role' => RoleEnum::SuperAdmin,
            'status_aktif' => true,
            'must_change_password' => false,
        ]);

        $this->pembimbing = User::factory()->create([
            'nama' => 'Pembimbing Senior',
            'email' => 'pembimbing@magkom.sleman.go.id',
            'role' => RoleEnum::Pembimbing,
            'status_aktif' => true,
            'bidang_id' => $this->bidang->id,
            'must_change_password' => false,
        ]);

        $this->bulanMagang = BulanMagang::create([
            'nama' => 'Magang November 2026',
            'bulan_mulai' => '2026-11-01',
            'tgl_buka' => '2026-10-01',
            'tgl_tutup' => '2026-10-25',
            'status' => 'dibuka',
            'aktif' => true,
        ]);

        $this->slotKhusus = SlotKhusus::create([
            'bulan_mulai' => '2026-11-01',
            'jumlah' => 5,
            'terisi' => 1,
            'aktif' => true,
            'dibuat_oleh' => $this->admin->id,
        ]);
    }

    public function test_admin_dapat_melihat_halaman_manajemen_slot_khusus_dan_metriknya(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.slot-khusus.index'));

        $response->assertOk();
        $response->assertSee('Manajemen Slot Khusus Internal');
        $response->assertSee('TOTAL ALOKASI');
        $response->assertSee('SLOT TERPAKAI');
        $response->assertSee('SISA SLOT');
        $response->assertSee('5'); // Total alokasi
        $response->assertSee('1'); // Terpakai
        $response->assertSee('4'); // Sisa
        $response->assertSee('Daftar Alokasi Slot Khusus Mitra');
        $response->assertSee('Daftar Peserta Magang Jalur Khusus');
    }

    public function test_admin_dapat_menambah_alokasi_slot_khusus_dengan_normalisasi_tanggal(): void
    {
        $payload = [
            'nama_instansi' => 'Politeknik Siber dan Sandi Negara',
            'bulan_mulai' => '2026-12-18', // Tanggal sembarang di bulan Desember 2026
            'jumlah' => 4,
            'keterangan' => 'MoU Kerjasama Keamanan Siber 2026',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.slot-khusus.store'), $payload);

        $response->assertRedirect(route('admin.slot-khusus.index'));
        $response->assertSessionHas('success');

        // Pastikan tersimpan dengan normalisasi tanggal 1 (2026-12-01)
        $this->assertDatabaseHas('slot_khusus', [
            'bulan_mulai' => '2026-12-01',
            'jumlah' => 4,
            'terisi' => 0,
            'aktif' => true,
            'dibuat_oleh' => $this->admin->id,
        ]);

        // Verifikasi audit log mencatat aksi dan detail mitra
        $this->assertDatabaseHas('audit_log', [
            'entitas' => 'slot_khusus',
            'user_id' => $this->admin->id,
        ]);
    }

    public function test_validasi_menolak_duplikasi_slot_khusus_pada_bulan_yang_sama(): void
    {
        // November 2026 sudah ada di setUp
        $payload = [
            'nama_instansi' => 'Mitra Lain',
            'bulan_mulai' => '2026-11-20',
            'jumlah' => 3,
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.slot-khusus.store'), $payload);

        $response->assertSessionHasErrors(['bulan_mulai']);
    }

    public function test_admin_dapat_toggle_aktif_nonaktif_slot_khusus_tanpa_peserta(): void
    {
        $slotBaru = SlotKhusus::create([
            'bulan_mulai' => '2027-01-01',
            'jumlah' => 3,
            'terisi' => 0,
            'aktif' => true,
            'dibuat_oleh' => $this->admin->id,
        ]);

        // Nonaktifkan
        $response = $this->actingAs($this->admin)->patch(route('admin.slot-khusus.toggle', $slotBaru));
        $response->assertRedirect(route('admin.slot-khusus.index'));
        $response->assertSessionHas('success');
        $this->assertFalse($slotBaru->fresh()->aktif);

        // Aktifkan kembali
        $response2 = $this->actingAs($this->admin)->patch(route('admin.slot-khusus.toggle', $slotBaru));
        $response2->assertRedirect(route('admin.slot-khusus.index'));
        $response2->assertSessionHas('success');
        $this->assertTrue($slotBaru->fresh()->aktif);
    }

    public function test_penonaktifan_slot_khusus_ditolak_jika_ada_peserta_magang_aktif_br_12(): void
    {
        $peserta = User::factory()->create([
            'role' => RoleEnum::Peserta,
            'status_aktif' => true,
        ]);

        Penempatan::create([
            'peserta_id' => $peserta->id,
            'slot_khusus_id' => $this->slotKhusus->id,
            'pembimbing_id' => $this->pembimbing->id,
            'bidang_id' => $this->bidang->id,
            'jalur' => JalurEnum::Khusus,
            'sumber' => 'manual',
            'dibuat_oleh' => $this->admin->id,
            'tgl_mulai' => '2026-11-01',
            'tgl_selesai' => '2027-01-31',
            'status' => StatusPenempatanEnum::Aktif,
        ]);

        // Mencoba menonaktifkan slot yang memiliki peserta aktif
        $response = $this->actingAs($this->admin)->patch(route('admin.slot-khusus.toggle', $this->slotKhusus));

        $response->assertSessionHas('error', 'Penonaktifan ditolak: Masih terdapat peserta magang aktif pada slot khusus ini.');
        $this->assertTrue($this->slotKhusus->fresh()->aktif);
    }

    public function test_admin_dapat_mendaftarkan_peserta_khusus_langsung_aktif_dan_memotong_kuota(): void
    {
        $awalTerisi = $this->slotKhusus->terisi;

        $payload = [
            'nama' => 'Zulfikar Ali Mahasiswa Khusus',
            'email' => 'zulfikar.ali@ugm.ac.id',
            'password' => 'SandiKuat2026!',
            'nim_nisn' => '21/478901/TK/52100',
            'institusi' => 'Universitas Gadjah Mada',
            'jurusan_id' => $this->jurusan->id,
            'jenjang' => 's1',
            'no_hp' => '081298765432',
            'bidang_id' => $this->bidang->id,
            'pembimbing_id' => $this->pembimbing->id,
            'bulan_mulai' => '2026-11',
            'durasi_bulan' => 3,
            'slot_khusus_id' => $this->slotKhusus->id,
            'catatan' => 'Penugasan Khusus Smart City Sleman',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.peserta-khusus.store'), $payload);

        $response->assertRedirect(route('admin.slot-khusus.index'));
        $response->assertSessionHas('success');
        $response->assertSessionHas('akun_baru_dibuat', true);
        $response->assertSessionHas('email_peserta', 'zulfikar.ali@ugm.ac.id');
        $response->assertSessionHas('password_baru', 'SandiKuat2026!');

        // Akun peserta terbentuk dan berstatus aktif
        $user = User::where('email', 'zulfikar.ali@ugm.ac.id')->first();
        $this->assertNotNull($user);
        $this->assertSame(RoleEnum::Peserta, $user->role);
        $this->assertTrue($user->status_aktif);
        $this->assertTrue($user->must_change_password);
        $this->assertTrue(Hash::check('SandiKuat2026!', $user->password));

        // Profil tersimpan
        $this->assertDatabaseHas('peserta_profil', [
            'user_id' => $user->id,
            'nim_nisn' => '21/478901/TK/52100',
            'institusi' => 'Universitas Gadjah Mada',
        ]);

        // Penempatan terbentuk langsung aktif
        $this->assertDatabaseHas('penempatan', [
            'peserta_id' => $user->id,
            'slot_khusus_id' => $this->slotKhusus->id,
            'pembimbing_id' => $this->pembimbing->id,
            'bidang_id' => $this->bidang->id,
            'jalur' => JalurEnum::Khusus->value,
            'status' => StatusPenempatanEnum::Aktif->value,
        ]);

        // Kuota slot khusus terpotong secara atomik (+1)
        $this->assertSame($awalTerisi + 1, $this->slotKhusus->fresh()->terisi);
    }

    public function test_pendaftaran_peserta_khusus_ditolak_jika_slot_khusus_penuh(): void
    {
        // Jadikan slot khusus penuh
        $this->slotKhusus->update([
            'jumlah' => 3,
            'terisi' => 3,
        ]);

        $payload = [
            'nama' => 'Peserta Melebihi Kuota',
            'email' => 'overquota@mitra.org',
            'password' => 'SandiKuat2026!',
            'nim_nisn' => '123123123',
            'institusi' => 'Institut Mitra',
            'jurusan_id' => $this->jurusan->id,
            'jenjang' => 's1',
            'pembimbing_id' => $this->pembimbing->id,
            'bulan_mulai' => '2026-11',
            'durasi_bulan' => 3,
            'slot_khusus_id' => $this->slotKhusus->id,
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.peserta-khusus.store'), $payload);

        $response->assertSessionHas('error');
        $this->assertStringContainsString('terisi penuh', session('error'));

        // Pastikan tidak ada akun user baru yang dibuat (rollback atomik)
        $this->assertDatabaseMissing('users', ['email' => 'overquota@mitra.org']);
    }

    public function test_pendaftaran_peserta_khusus_ditolak_jika_slot_khusus_nonaktif(): void
    {
        $this->slotKhusus->update(['aktif' => false]);

        $payload = [
            'nama' => 'Peserta Slot Nonaktif',
            'email' => 'nonaktif@mitra.org',
            'password' => 'SandiKuat2026!',
            'nim_nisn' => '456456456',
            'institusi' => 'Institut Mitra',
            'jurusan_id' => $this->jurusan->id,
            'jenjang' => 's1',
            'pembimbing_id' => $this->pembimbing->id,
            'bulan_mulai' => '2026-11',
            'durasi_bulan' => 3,
            'slot_khusus_id' => $this->slotKhusus->id,
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.peserta-khusus.store'), $payload);

        $response->assertSessionHas('error');
        $this->assertStringContainsString('dinonaktifkan', session('error'));
        $this->assertDatabaseMissing('users', ['email' => 'nonaktif@mitra.org']);
    }

    public function test_pendaftaran_peserta_khusus_ditolak_jika_beban_pembimbing_penuh_br_13(): void
    {
        // Set konfigurasi maks bimbingan per mentor = 1
        Pengaturan::updateOrCreate(
            ['kunci' => 'maks_peserta_aktif_per_pembimbing'],
            ['nilai' => '1']
        );
        Cache::flush();

        // Pasang 1 peserta aktif pada pembimbing
        $menteeLama = User::factory()->create(['role' => RoleEnum::Peserta, 'status_aktif' => true]);
        Penempatan::create([
            'peserta_id' => $menteeLama->id,
            'pembimbing_id' => $this->pembimbing->id,
            'bidang_id' => $this->bidang->id,
            'jalur' => JalurEnum::Khusus,
            'sumber' => 'manual',
            'dibuat_oleh' => $this->admin->id,
            'tgl_mulai' => '2026-11-01',
            'tgl_selesai' => '2027-01-31',
            'status' => StatusPenempatanEnum::Aktif,
        ]);

        $payload = [
            'nama' => 'Peserta Gagal Beban Mentor',
            'email' => 'beban.penuh@mitra.org',
            'password' => 'SandiKuat2026!',
            'nim_nisn' => '789789789',
            'institusi' => 'Kampus Sleman',
            'jurusan_id' => $this->jurusan->id,
            'jenjang' => 's1',
            'pembimbing_id' => $this->pembimbing->id,
            'bulan_mulai' => '2026-11',
            'durasi_bulan' => 3,
            'slot_khusus_id' => $this->slotKhusus->id,
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.peserta-khusus.store'), $payload);

        $response->assertSessionHas('error');
        $this->assertStringContainsString('batas beban maksimum', session('error'));
        $this->assertDatabaseMissing('users', ['email' => 'beban.penuh@mitra.org']);
    }

    public function test_pembatalan_penempatan_peserta_khusus_mengembalikan_kuota_slot_br_07(): void
    {
        Carbon::setTestNow('2026-10-15 10:00:00');

        $peserta = User::factory()->create(['role' => RoleEnum::Peserta, 'status_aktif' => true]);
        $penempatan = Penempatan::create([
            'peserta_id' => $peserta->id,
            'slot_khusus_id' => $this->slotKhusus->id,
            'pembimbing_id' => $this->pembimbing->id,
            'bidang_id' => $this->bidang->id,
            'jalur' => JalurEnum::Khusus,
            'sumber' => 'manual',
            'dibuat_oleh' => $this->admin->id,
            'tgl_mulai' => '2026-11-01', // Mulai di masa depan
            'tgl_selesai' => '2027-01-31',
            'status' => StatusPenempatanEnum::Aktif,
        ]);

        $terisiSebelumBatal = $this->slotKhusus->fresh()->terisi;

        /** @var PlacementService $placementService */
        $placementService = app(PlacementService::class);
        $placementService->batalkanPenempatan($penempatan, $this->admin, 'Permohonan institusi dibatalkan');

        // Kuota dikembalikan (-1)
        $this->assertSame($terisiSebelumBatal - 1, $this->slotKhusus->fresh()->terisi);
        $this->assertSame(StatusPenempatanEnum::MengundurkanDiri, $penempatan->fresh()->status);

        Carbon::setTestNow();
    }

    public function test_superadmin_hanya_mode_lihat_saja_dan_dilarang_menambah_slot(): void
    {
        // Mode lihat saja: ada banner read-only
        $response = $this->actingAs($this->superAdmin)->get(route('admin.slot-khusus.index'));
        $response->assertOk();
        $response->assertSee('Mode Lihat Saja (Read-Only)');

        // Dilarang melakukan aksi penambahan slot (403 Forbidden)
        $responsePost = $this->actingAs($this->superAdmin)->post(route('admin.slot-khusus.store'), [
            'bulan_mulai' => '2027-02-01',
            'jumlah' => 3,
        ]);

        $responsePost->assertForbidden();
    }
}
