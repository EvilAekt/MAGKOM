<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JenjangEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusPendaftaranEnum;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\KelompokAnggota;
use App\Models\KelompokMagang;
use App\Models\Kuota;
use App\Models\Pendaftaran;
use App\Models\PesertaProfil;
use App\Models\User;
use App\Services\PlacementService;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PesertaReapplyAfterRejectionTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected BulanMagang $bulan;

    protected BulanMagang $bulanDua;

    protected Jurusan $jurusan;

    protected Kuota $kuota;

    protected Kuota $kuotaDua;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::parse('2026-10-15 10:00:00', 'Asia/Jakarta'));
        CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-10-15 10:00:00', 'Asia/Jakarta'));

        $this->admin = User::firstOrCreate(
            ['email' => 'admin.verifikator@kominfo.go.id'],
            [
                'nama' => 'Admin Verifikator MagKom',
                'password' => bcrypt('Password123!'),
                'role' => RoleEnum::Admin,
                'email_verified_at' => now(),
                'status_aktif' => true,
            ]
        );

        $bidang = Bidang::firstOrCreate(
            ['nama_bidang' => 'Bidang IKP'],
            ['deskripsi' => 'Informasi dan Komunikasi Publik']
        );

        $this->jurusan = Jurusan::firstOrCreate(
            ['nama_jurusan' => 'Informatika'],
            ['kategori' => 'mahasiswa', 'bidang_id' => $bidang->id]
        );

        // Bulan 1: Status Tersedia
        $this->bulan = BulanMagang::firstOrCreate(
            ['bulan_mulai' => '2026-11-01'],
            [
                'tgl_buka' => '2026-10-01',
                'tgl_tutup' => '2026-10-25',
                'aktif' => true,
            ]
        );

        // Bulan 2: Status Tersedia
        $this->bulanDua = BulanMagang::firstOrCreate(
            ['bulan_mulai' => '2026-12-01'],
            [
                'tgl_buka' => '2026-10-01',
                'tgl_tutup' => '2026-11-25',
                'aktif' => true,
            ]
        );

        $this->kuota = Kuota::firstOrCreate(
            ['bulan_magang_id' => $this->bulan->id, 'jurusan_id' => $this->jurusan->id],
            ['jumlah' => 10, 'terisi' => 0, 'aktif' => true]
        );

        $this->kuotaDua = Kuota::firstOrCreate(
            ['bulan_magang_id' => $this->bulanDua->id, 'jurusan_id' => $this->jurusan->id],
            ['jumlah' => 10, 'terisi' => 0, 'aktif' => true]
        );
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        CarbonImmutable::setTestNow();
        parent::tearDown();
    }

    private function createPesertaLengkap(string $email, string $nama = 'Calon Mahasiswa'): User
    {
        $user = new User([
            'nama' => $nama,
            'email' => $email,
            'password' => bcrypt('Password123!'),
            'status_aktif' => true,
        ]);
        $user->role = RoleEnum::Peserta;
        $user->email_verified_at = now();
        $user->save();

        PesertaProfil::create([
            'user_id' => $user->id,
            'jenjang' => JenjangEnum::S1,
            'jurusan_id' => $this->jurusan->id,
            'institusi' => 'Universitas Gadjah Mada',
            'nim_nisn' => '20/456789/TK/50000',
            'no_hp' => '081234567890',
            'alamat' => 'Sleman, D.I. Yogyakarta',
            'foto' => 'avatars/default.png',
            'nama_kontak_darurat' => 'Bapak Mahasiswa',
            'no_kontak_darurat' => '081298765432',
        ]);

        return $user;
    }

    public function test_peserta_yang_ditolak_dapat_mengajukan_kembali_pada_bulan_yang_sama_tanpa_duplicate_error(): void
    {
        $peserta = $this->createPesertaLengkap('reapply.same@test.com', 'Budi Reapply');

        // 1. Pendaftaran pertama pada Bulan 1
        $pendaftaranAwal = Pendaftaran::create([
            'user_id' => $peserta->id,
            'bulan_magang_id' => $this->bulan->id,
            'kuota_id' => $this->kuota->id,
            'durasi_bulan' => 3,
            'tipe' => 'mandiri',
            'status' => StatusPendaftaranEnum::Diajukan,
            'diajukan_at' => now()->subDay(),
        ]);

        // 2. Admin menolak pendaftaran awal
        $placementService = app(PlacementService::class);
        $placementService->tolak($pendaftaranAwal, $this->admin, 'Kualifikasi berkas belum sesuai.');

        $pendaftaranAwal->refresh();
        $this->assertEquals(StatusPendaftaranEnum::Ditolak, $pendaftaranAwal->status);

        // 3. Peserta mengajukan pendaftaran kembali pada Bulan 1 yang sama
        $response = $this->actingAs($peserta)->post(route('peserta.pendaftaran.store'), [
            'bulan_magang_id' => $this->bulan->id,
            'kuota_id' => $this->kuota->id,
            'durasi_bulan' => 3,
        ]);

        // 4. Verifikasi tidak ada error 500 / UniqueConstraintViolationException
        $response->assertSessionHasNoErrors();

        // 5. Verifikasi pendaftaran baru berhasil dibuat
        $pendaftaranBaru = Pendaftaran::where('user_id', $peserta->id)
            ->where('id', '!=', $pendaftaranAwal->id)
            ->first();

        $this->assertNotNull($pendaftaranBaru, 'Pendaftaran baru harus berhasil dibuat setelah ditolak.');
        $response->assertRedirect(route('peserta.pendaftaran.detail', $pendaftaranBaru));

        $this->assertEquals(StatusPendaftaranEnum::BerkasBelumLengkap, $pendaftaranBaru->status);
        $this->assertEquals($this->bulan->id, $pendaftaranBaru->bulan_magang_id);
        $this->assertGreaterThan($pendaftaranAwal->id, $pendaftaranBaru->id);

        // 6. Verifikasi pendaftaran lama tetap ada sebagai jejak audit
        $this->assertDatabaseHas('pendaftaran', [
            'id' => $pendaftaranAwal->id,
            'status' => StatusPendaftaranEnum::Ditolak->value,
            'catatan' => 'Kualifikasi berkas belum sesuai.',
        ]);

        // Total rekaman pendaftaran untuk peserta ini sekarang berjumlah 2
        $this->assertEquals(2, Pendaftaran::where('user_id', $peserta->id)->count());
    }

    public function test_peserta_yang_ditolak_dapat_mengajukan_kembali_pada_bulan_berbeda(): void
    {
        $peserta = $this->createPesertaLengkap('reapply.diff@test.com', 'Siti Reapply');

        // Pendaftaran pertama pada Bulan 1 ditolak
        $pendaftaranAwal = Pendaftaran::create([
            'user_id' => $peserta->id,
            'bulan_magang_id' => $this->bulan->id,
            'kuota_id' => $this->kuota->id,
            'durasi_bulan' => 3,
            'tipe' => 'mandiri',
            'status' => StatusPendaftaranEnum::Ditolak,
            'catatan' => 'Formasi periode ini sudah penuh.',
            'diputuskan_at' => now()->subDay(),
        ]);

        // Peserta mengajukan pada Bulan 2
        $response = $this->actingAs($peserta)->post(route('peserta.pendaftaran.store'), [
            'bulan_magang_id' => $this->bulanDua->id,
            'kuota_id' => $this->kuotaDua->id,
            'durasi_bulan' => 3,
        ]);

        $response->assertSessionHasNoErrors();

        $pendaftaranBaru = Pendaftaran::where('user_id', $peserta->id)
            ->where('id', '!=', $pendaftaranAwal->id)
            ->first();

        $this->assertNotNull($pendaftaranBaru);
        $this->assertEquals($this->bulanDua->id, $pendaftaranBaru->bulan_magang_id);
        $this->assertEquals(StatusPendaftaranEnum::BerkasBelumLengkap, $pendaftaranBaru->status);
    }

    public function test_peserta_yang_dibatalkan_dapat_mengajukan_kembali(): void
    {
        $peserta = $this->createPesertaLengkap('reapply.batal@test.com', 'Andi Batal');

        // Pendaftaran pertama pada Bulan 1 berstatus dibatalkan
        $pendaftaranAwal = Pendaftaran::create([
            'user_id' => $peserta->id,
            'bulan_magang_id' => $this->bulan->id,
            'kuota_id' => $this->kuota->id,
            'durasi_bulan' => 3,
            'tipe' => 'mandiri',
            'status' => StatusPendaftaranEnum::Dibatalkan,
            'catatan' => 'Peserta membatalkan pengajuan.',
            'diputuskan_at' => now()->subDay(),
        ]);

        // Peserta mengajukan pendaftaran baru pada Bulan 1 yang sama
        $response = $this->actingAs($peserta)->post(route('peserta.pendaftaran.store'), [
            'bulan_magang_id' => $this->bulan->id,
            'kuota_id' => $this->kuota->id,
            'durasi_bulan' => 3,
        ]);

        $response->assertSessionHasNoErrors();

        $pendaftaranBaru = Pendaftaran::where('user_id', $peserta->id)
            ->where('id', '!=', $pendaftaranAwal->id)
            ->first();

        $this->assertNotNull($pendaftaranBaru);
        $this->assertEquals($this->bulan->id, $pendaftaranBaru->bulan_magang_id);
        $this->assertEquals(StatusPendaftaranEnum::BerkasBelumLengkap, $pendaftaranBaru->status);
    }

    public function test_peserta_dengan_pendaftaran_aktif_tetap_dilarang_membuat_pendaftaran_kedua_br17(): void
    {
        $peserta = $this->createPesertaLengkap('active.guard@test.com', 'Rudi Active');

        // Pendaftaran berstatus Diajukan (Aktif)
        Pendaftaran::create([
            'user_id' => $peserta->id,
            'bulan_magang_id' => $this->bulan->id,
            'kuota_id' => $this->kuota->id,
            'durasi_bulan' => 3,
            'tipe' => 'mandiri',
            'status' => StatusPendaftaranEnum::Diajukan,
            'diajukan_at' => now(),
        ]);

        // Percobaan membuat pendaftaran kedua wajib ditolak oleh validasi BR-17
        $response = $this->actingAs($peserta)->post(route('peserta.pendaftaran.store'), [
            'bulan_magang_id' => $this->bulanDua->id,
            'kuota_id' => $this->kuotaDua->id,
            'durasi_bulan' => 3,
        ]);

        $response->assertSessionHasErrors(['pendaftaran']);
        $this->assertEquals(1, Pendaftaran::where('user_id', $peserta->id)->count());
    }

    public function test_ketua_kelompok_yang_ditolak_dapat_mengajukan_kembali_sebagai_kelompok(): void
    {
        $ketua = $this->createPesertaLengkap('ketua.reapply@test.com', 'Ketua Kelompok Alpha');

        $kelompok = KelompokMagang::create([
            'ketua_user_id' => $ketua->id,
            'nama_kelompok' => 'Tim Riset Alpha',
        ]);

        KelompokAnggota::create([
            'kelompok_magang_id' => $kelompok->id,
            'nama' => 'Anggota Tim 1',
            'nim_nisn' => '20/123456/TK/11111',
            'no_hp' => '081234567899',
            'email' => 'anggota1.alpha@test.com',
        ]);

        // Pendaftaran kelompok lama ditolak
        $pendaftaranAwal = Pendaftaran::create([
            'user_id' => $ketua->id,
            'bulan_magang_id' => $this->bulan->id,
            'kuota_id' => $this->kuota->id,
            'durasi_bulan' => 3,
            'tipe' => 'kelompok',
            'kelompok_magang_id' => $kelompok->id,
            'status' => StatusPendaftaranEnum::Ditolak,
            'catatan' => 'Kelompok ditolak karena kuota terbatas.',
            'diputuskan_at' => now()->subDay(),
        ]);

        // Ketua mengajukan pendaftaran baru untuk kelompok
        $response = $this->actingAs($ketua)->post(route('peserta.pendaftaran.store'), [
            'bulan_magang_id' => $this->bulan->id,
            'kuota_id' => $this->kuota->id,
            'durasi_bulan' => 3,
        ]);

        $response->assertSessionHasNoErrors();

        $pendaftaranBaru = Pendaftaran::where('user_id', $ketua->id)
            ->where('id', '!=', $pendaftaranAwal->id)
            ->first();

        $this->assertNotNull($pendaftaranBaru);
        $this->assertEquals('kelompok', $pendaftaranBaru->tipe);
        $this->assertEquals($kelompok->id, $pendaftaranBaru->kelompok_magang_id);
        $this->assertEquals(StatusPendaftaranEnum::BerkasBelumLengkap, $pendaftaranBaru->status);
    }
}
