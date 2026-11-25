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
use App\Models\Logbook;
use App\Models\Penempatan;
use App\Models\PesertaProfil;
use App\Models\Tugas;
use App\Models\User;
use Database\Seeders\JenisDokumenSeeder;
use Database\Seeders\RubrikSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminArsipPesertaKonektivitasEndToEndTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $superAdmin;

    protected User $pembimbing;

    protected User $pesertaSelesai;

    protected User $pesertaNonaktif;

    protected User $pesertaMundur;

    protected Bidang $bidang;

    protected Jurusan $jurusan;

    protected BulanMagang $bulanMagang;

    protected Kuota $kuota;

    protected Penempatan $penempatanSelesai;

    protected Penempatan $penempatanNonaktif;

    protected Penempatan $penempatanMundur;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(JenisDokumenSeeder::class);
        $this->seed(RubrikSeeder::class);

        $this->admin = User::factory()->create([
            'nama' => 'Admin Verifikator Sleman',
            'email' => 'admin.arsip@magkom.sleman.go.id',
            'role' => RoleEnum::Admin,
            'status_aktif' => true,
            'must_change_password' => false,
        ]);

        $this->superAdmin = User::factory()->create([
            'nama' => 'Super Administrator Sleman',
            'email' => 'superadmin.arsip@magkom.sleman.go.id',
            'role' => RoleEnum::SuperAdmin,
            'status_aktif' => true,
            'must_change_password' => false,
        ]);

        $this->bidang = Bidang::create([
            'nama_bidang' => 'Aplikasi Informatika & E-Government (EGOV)',
            'deskripsi' => 'Pengembangan ekosistem sistem digital',
            'aktif' => true,
        ]);

        $this->jurusan = Jurusan::create([
            'nama_jurusan' => 'Teknik Informatika',
            'kategori' => 'mahasiswa',
            'aktif' => true,
        ]);

        $this->bulanMagang = BulanMagang::create([
            'nama' => 'Magang Periode 2026',
            'bulan_mulai' => '2026-08-01',
            'tgl_buka' => '2026-07-01',
            'tgl_tutup' => '2026-07-25',
            'status' => 'dibuka',
            'aktif' => true,
        ]);

        $this->kuota = Kuota::create([
            'bulan_magang_id' => $this->bulanMagang->id,
            'jurusan_id' => $this->jurusan->id,
            'jumlah' => 10,
            'terpakai' => 3,
            'aktif' => true,
        ]);

        $this->pembimbing = User::factory()->create([
            'nama' => 'Pembimbing Lapangan EGOV',
            'email' => 'mentor.egov@slemankab.go.id',
            'role' => RoleEnum::Pembimbing,
            'bidang_id' => $this->bidang->id,
            'status_aktif' => true,
            'must_change_password' => false,
        ]);

        // 1. Alumni Selesai Magang
        $this->pesertaSelesai = User::factory()->create([
            'nama' => 'Siti Nurhaliza',
            'email' => 'siti.nurhaliza@amikom.ac.id',
            'role' => RoleEnum::Peserta,
            'status_aktif' => true,
        ]);
        PesertaProfil::create([
            'user_id' => $this->pesertaSelesai->id,
            'jurusan_id' => $this->jurusan->id,
            'institusi' => 'Universitas Amikom Yogyakarta',
            'nim_nisn' => '21.11.3999',
            'jenjang' => JenjangEnum::S1,
            'no_hp' => '081299988877',
        ]);
        $this->penempatanSelesai = Penempatan::create([
            'peserta_id' => $this->pesertaSelesai->id,
            'kuota_id' => $this->kuota->id,
            'bidang_id' => $this->bidang->id,
            'pembimbing_id' => $this->pembimbing->id,
            'status' => StatusPenempatanEnum::Selesai,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'dibuat_oleh' => $this->admin->id,
            'tgl_mulai' => '2026-07-17',
            'tgl_selesai' => '2026-10-17',
        ]);

        // 2. Alumni Nonaktif (Purna Retensi)
        $this->pesertaNonaktif = User::factory()->create([
            'nama' => 'Indra Kurniawan',
            'email' => 'indra.kurniawan@upnyk.ac.id',
            'role' => RoleEnum::Peserta,
            'status_aktif' => false,
        ]);
        PesertaProfil::create([
            'user_id' => $this->pesertaNonaktif->id,
            'jurusan_id' => $this->jurusan->id,
            'institusi' => 'UPN Veteran Yogyakarta',
            'nim_nisn' => '123220088',
            'jenjang' => JenjangEnum::S1,
            'no_hp' => '081211122233',
        ]);
        $this->penempatanNonaktif = Penempatan::create([
            'peserta_id' => $this->pesertaNonaktif->id,
            'kuota_id' => $this->kuota->id,
            'bidang_id' => $this->bidang->id,
            'pembimbing_id' => $this->pembimbing->id,
            'status' => StatusPenempatanEnum::Nonaktif,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'dibuat_oleh' => $this->admin->id,
            'tgl_mulai' => '2026-05-17',
            'tgl_selesai' => '2026-08-17',
        ]);

        // 3. Peserta Mengundurkan Diri (Purna Dini)
        $this->pesertaMundur = User::factory()->create([
            'nama' => 'Taufik Hidayat',
            'email' => 'taufik.hidayat@uii.ac.id',
            'role' => RoleEnum::Peserta,
            'status_aktif' => false,
        ]);
        PesertaProfil::create([
            'user_id' => $this->pesertaMundur->id,
            'jurusan_id' => $this->jurusan->id,
            'institusi' => 'Universitas Islam Indonesia',
            'nim_nisn' => '22523299',
            'jenjang' => JenjangEnum::S1,
            'no_hp' => '081233344455',
        ]);
        $this->penempatanMundur = Penempatan::create([
            'peserta_id' => $this->pesertaMundur->id,
            'kuota_id' => $this->kuota->id,
            'bidang_id' => $this->bidang->id,
            'pembimbing_id' => $this->pembimbing->id,
            'status' => StatusPenempatanEnum::MengundurkanDiri,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'dibuat_oleh' => $this->admin->id,
            'tgl_mulai' => '2026-06-01',
            'tgl_selesai' => '2026-07-15',
        ]);
    }

    public function test_admin_melihat_halaman_index_arsip_peserta_dan_kartu_statistik(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.arsip.index'));

        $response->assertOk();
        $response->assertSee('Arsip &amp; Rekam Jejak Alumni Magang', false);
        $response->assertSee('TOTAL ARSIP');
        $response->assertSee('SELESAI MAGANG');
        $response->assertSee('AKUN NONAKTIF');
        $response->assertSee('MENGUNDURKAN DIRI');
        $response->assertSee('Strictly Read-Only Bank Data');
        $response->assertSee('Ekspor Laporan');

        // Memastikan 3 alumni terdaftar muncul di tabel
        $response->assertSee('Siti Nurhaliza');
        $response->assertSee('Indra Kurniawan');
        $response->assertSee('Taufik Hidayat');
        $response->assertSee('Universitas Amikom Yogyakarta');
        $response->assertSee('UPN Veteran Yogyakarta');
        $response->assertSee('Universitas Islam Indonesia');

        // Memastikan link detail arsip tersedia
        $response->assertSee(route('admin.arsip.show', $this->penempatanSelesai));
        $response->assertSee(route('admin.arsip.show', $this->penempatanNonaktif));
        $response->assertSee(route('admin.arsip.show', $this->penempatanMundur));
    }

    public function test_admin_dapat_mencari_alumni_dengan_sanitasi_spasi(): void
    {
        // 1. Pencarian nama dengan spasi ekstra di depan dan belakang
        $responseSearch = $this->actingAs($this->admin)->get(route('admin.arsip.index', [
            'search' => '   Siti Nurhaliza   ',
        ]));
        $responseSearch->assertOk();
        $responseSearch->assertSee('Siti Nurhaliza');
        $responseSearch->assertDontSee('Indra Kurniawan');
        $responseSearch->assertDontSee('Taufik Hidayat');

        // 2. Pencarian berdasarkan institusi
        $responseInst = $this->actingAs($this->admin)->get(route('admin.arsip.index', [
            'search' => 'UPN Veteran',
        ]));
        $responseInst->assertOk();
        $responseInst->assertSee('Indra Kurniawan');
        $responseInst->assertDontSee('Siti Nurhaliza');
    }

    public function test_admin_dapat_memfilter_arsip_berdasarkan_status_dan_tahun(): void
    {
        // 1. Filter status selesai
        $resSelesai = $this->actingAs($this->admin)->get(route('admin.arsip.index', ['status' => 'selesai']));
        $resSelesai->assertOk();
        $resSelesai->assertSee('Siti Nurhaliza');
        $resSelesai->assertDontSee('Indra Kurniawan');
        $resSelesai->assertDontSee('Taufik Hidayat');

        // 2. Filter status nonaktif
        $resNonaktif = $this->actingAs($this->admin)->get(route('admin.arsip.index', ['status' => 'nonaktif']));
        $resNonaktif->assertOk();
        $resNonaktif->assertSee('Indra Kurniawan');
        $resNonaktif->assertDontSee('Siti Nurhaliza');

        // 3. Filter status mengundurkan diri
        $resMundur = $this->actingAs($this->admin)->get(route('admin.arsip.index', ['status' => 'mengundurkan_diri']));
        $resMundur->assertOk();
        $resMundur->assertSee('Taufik Hidayat');
        $resMundur->assertDontSee('Siti Nurhaliza');

        // 4. Filter tahun 2026
        $resTahun = $this->actingAs($this->admin)->get(route('admin.arsip.index', ['tahun' => 2026]));
        $resTahun->assertOk();
        $resTahun->assertSee('Siti Nurhaliza');
        $resTahun->assertSee('Indra Kurniawan');
        $resTahun->assertSee('Taufik Hidayat');
    }

    public function test_admin_dapat_melihat_detail_arsip_peserta_dan_mencatat_audit_log(): void
    {
        // Berikan contoh penugasan dan logbook pada alumni
        Tugas::create([
            'penempatan_id' => $this->penempatanSelesai->id,
            'judul' => 'Pengembangan Modul Pelaporan SPBE',
            'deskripsi' => 'Rancang arsitektur modul pelaporan',
            'kategori' => 'Teknologi Informasi',
            'kompleksitas' => 'sedang',
            'deadline' => '2026-08-15',
            'status' => 'selesai',
        ]);

        Logbook::create([
            'penempatan_id' => $this->penempatanSelesai->id,
            'peserta_id' => $this->pesertaSelesai->id,
            'pembimbing_id' => $this->pembimbing->id,
            'tanggal' => '2026-08-02',
            'kegiatan' => 'Analisis kebutuhan sistem SPBE Kominfo',
            'status' => 'disetujui',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.arsip.show', $this->penempatanSelesai));

        $response->assertOk();
        $response->assertSee('Detail Arsip Alumni Magang');
        $response->assertSee('Siti Nurhaliza');
        $response->assertSee('Universitas Amikom Yogyakarta');
        $response->assertSee('21.11.3999');
        $response->assertSee('Pembimbing Lapangan EGOV');
        $response->assertSee('Pengembangan Modul Pelaporan SPBE');
        $response->assertSee('Analisis kebutuhan sistem SPBE Kominfo');
        $response->assertSee(route('admin.penempatan.surat-diterima', $this->penempatanSelesai));
        $response->assertSee(route('admin.arsip.index'));

        // Pastikan akses data tercatat di audit log
        $this->assertDatabaseHas('audit_log', [
            'tipe' => TipeAuditEnum::AksesData->value,
            'entitas' => 'penempatan',
            'entitas_id' => $this->penempatanSelesai->id,
            'user_id' => $this->admin->id,
        ]);
    }

    public function test_detail_arsip_menolak_penempatan_yang_masih_aktif_dengan_404(): void
    {
        $penempatanAktif = Penempatan::create([
            'peserta_id' => $this->pesertaSelesai->id,
            'kuota_id' => $this->kuota->id,
            'bidang_id' => $this->bidang->id,
            'pembimbing_id' => $this->pembimbing->id,
            'status' => StatusPenempatanEnum::Aktif,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'dibuat_oleh' => $this->admin->id,
            'tgl_mulai' => '2026-11-01',
            'tgl_selesai' => '2027-01-31',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.arsip.show', $penempatanAktif));
        $response->assertNotFound();
    }

    public function test_role_non_admin_peserta_dan_pembimbing_dilarang_mengakses_arsip_admin(): void
    {
        $this->actingAs($this->pesertaSelesai)
            ->get(route('admin.arsip.index'))
            ->assertForbidden();

        $this->actingAs($this->pembimbing)
            ->get(route('admin.arsip.index'))
            ->assertForbidden();
    }

    public function test_paginasi_arsip_membatasi_sepuluh_entri_per_halaman(): void
    {
        // Tambahkan 8 penempatan lagi agar total menjadi 11
        for ($i = 1; $i <= 8; $i++) {
            $user = User::factory()->create(['role' => RoleEnum::Peserta]);
            Penempatan::create([
                'peserta_id' => $user->id,
                'kuota_id' => $this->kuota->id,
                'bidang_id' => $this->bidang->id,
                'pembimbing_id' => $this->pembimbing->id,
                'status' => StatusPenempatanEnum::Selesai,
                'jalur' => JalurEnum::Umum,
                'sumber' => SumberPenempatanEnum::Online,
                'dibuat_oleh' => $this->admin->id,
                'tgl_mulai' => '2026-08-01',
                'tgl_selesai' => '2026-10-31',
            ]);
        }

        $response = $this->actingAs($this->admin)->get(route('admin.arsip.index'));
        $response->assertOk();
        $response->assertViewHas('arsipList', function ($list) {
            return $list->count() === 10 && $list->total() === 11;
        });
    }
}
