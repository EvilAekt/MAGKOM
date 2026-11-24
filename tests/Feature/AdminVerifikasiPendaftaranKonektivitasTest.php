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
use App\Queries\Admin\Penempatan\PenempatanAntreanQuery;
use Database\Seeders\JenisDokumenSeeder;
use Database\Seeders\RubrikSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminVerifikasiPendaftaranKonektivitasTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $peserta;

    protected Bidang $bidang;

    protected Jurusan $jurusan;

    protected BulanMagang $bulan;

    protected Kuota $kuota;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(JenisDokumenSeeder::class);
        $this->seed(RubrikSeeder::class);

        $this->bidang = Bidang::create([
            'nama_bidang' => 'Aplikasi Informatika (EGOV)',
            'deskripsi' => 'Bidang Layanan EGOV',
            'aktif' => true,
        ]);

        $this->jurusan = Jurusan::create([
            'nama_jurusan' => 'Teknik Informatika',
            'kategori' => 's1',
            'aktif' => true,
        ]);

        $this->bulan = BulanMagang::create([
            'bulan_mulai' => now()->addMonth()->startOfMonth(),
            'tgl_buka' => now()->subDays(5)->toDateString(),
            'tgl_tutup' => now()->addDays(20)->toDateString(),
            'aktif' => true,
        ]);

        $this->kuota = Kuota::create([
            'bulan_magang_id' => $this->bulan->id,
            'jurusan_id' => $this->jurusan->id,
            'jumlah' => 10,
            'terisi' => 0,
            'aktif' => true,
        ]);

        $this->admin = new User;
        $this->admin->nama = 'Admin Verifikator MagKom';
        $this->admin->email = 'admin.verifikator@slemankab.go.id';
        $this->admin->password = 'Password123!';
        $this->admin->role = RoleEnum::Admin;
        $this->admin->status_aktif = true;
        $this->admin->email_verified_at = now();
        $this->admin->save();

        $this->peserta = new User;
        $this->peserta->nama = 'Aditya Pratama';
        $this->peserta->email = 'aditya.peserta@test.com';
        $this->peserta->password = 'Password123!';
        $this->peserta->role = RoleEnum::Peserta;
        $this->peserta->status_aktif = true;
        $this->peserta->email_verified_at = now();
        $this->peserta->save();

        PesertaProfil::create([
            'user_id' => $this->peserta->id,
            'nim_nisn' => '2201010011',
            'institusi' => 'Universitas Gadjah Mada',
            'jurusan_id' => $this->jurusan->id,
            'jenjang' => JenjangEnum::S1,
            'no_hp' => '081234567890',
            'nama_kontak_darurat' => 'Siti Wali',
            'no_kontak_darurat' => '089876543210',
            'hubungan_kontak_darurat' => 'Orang Tua',
        ]);
    }

    public function test_admin_menerima_pendaftaran_memotong_kuota_dan_masuk_antrean_penempatan(): void
    {
        $pendaftaran = Pendaftaran::create([
            'user_id' => $this->peserta->id,
            'kuota_id' => $this->kuota->id,
            'bulan_magang_id' => $this->bulan->id,
            'status' => StatusPendaftaranEnum::Diajukan,
            'tipe' => 'mandiri',
            'durasi_bulan' => 3,
            'diajukan_at' => now(),
        ]);

        $this->assertSame(0, $this->kuota->fresh()->terisi);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.pendaftaran.putuskan', $pendaftaran), [
                'keputusan' => 'terima',
                'catatan' => 'Berkas lengkap dan sesuai kualifikasi bidang.',
            ]);

        $response->assertRedirect(route('admin.pendaftaran.show', $pendaftaran));
        $response->assertSessionHas('success');

        // 1. Verifikasi status pendaftaran berubah menjadi Diterima
        $p = $pendaftaran->fresh();
        $this->assertSame(StatusPendaftaranEnum::Diterima, $p->status);
        $this->assertSame($this->admin->id, $p->verifikator_id);
        $this->assertNotNull($p->diputuskan_at);
        $this->assertSame('Berkas lengkap dan sesuai kualifikasi bidang.', $p->catatan);

        // 2. Verifikasi kuota terpotong secara atomik (+1)
        $this->assertSame(1, $this->kuota->fresh()->terisi);

        // 3. Verifikasi otomatis masuk ke antrean Penempatan Belum Ditempatkan
        /** @var PenempatanAntreanQuery $antreanQuery */
        $antreanQuery = app(PenempatanAntreanQuery::class);
        $this->assertSame(1, $antreanQuery->getTotalBelumDitempatkan());
        $antreanList = $antreanQuery->getAntrean(request());
        $this->assertTrue($antreanList->contains('id', $p->id));

        // 4. Verifikasi jejak audit tercatat
        $this->assertDatabaseHas('audit_log', [
            'entitas' => 'pendaftaran',
            'entitas_id' => $p->id,
        ]);
    }

    public function test_admin_menerima_pendaftaran_kelompok_memotong_kuota_sejumlah_rombongan(): void
    {
        $kelompok = KelompokMagang::create([
            'nama_kelompok' => 'Tim Garuda Sleman',
            'asal_institusi' => 'Universitas Gadjah Mada',
            'ketua_user_id' => $this->peserta->id,
        ]);

        KelompokAnggota::create([
            'kelompok_magang_id' => $kelompok->id,
            'nama' => 'Anggota Satu',
            'nim_nisn' => '2201010012',
            'no_hp' => '081234567891',
        ]);

        KelompokAnggota::create([
            'kelompok_magang_id' => $kelompok->id,
            'nama' => 'Anggota Dua',
            'nim_nisn' => '2201010013',
            'no_hp' => '081234567892',
        ]);

        $pendaftaranKelompok = Pendaftaran::create([
            'user_id' => $this->peserta->id,
            'kuota_id' => $this->kuota->id,
            'bulan_magang_id' => $this->bulan->id,
            'status' => StatusPendaftaranEnum::Diajukan,
            'tipe' => 'kelompok',
            'kelompok_magang_id' => $kelompok->id,
            'durasi_bulan' => 3,
            'diajukan_at' => now(),
        ]);

        $this->assertSame(0, $this->kuota->fresh()->terisi);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.pendaftaran.putuskan', $pendaftaranKelompok), [
                'keputusan' => 'terima',
                'catatan' => 'Berkas kelompok disetujui.',
            ]);

        $response->assertRedirect(route('admin.pendaftaran.show', $pendaftaranKelompok));
        $response->assertSessionHas('success');

        // Kuota harus terpotong 3 (1 ketua + 2 anggota)
        $this->assertSame(3, $this->kuota->fresh()->terisi);
        $this->assertSame(StatusPendaftaranEnum::Diterima, $pendaftaranKelompok->fresh()->status);
    }

    public function test_admin_menolak_pendaftaran_dengan_alasan_wajib_dan_kuota_tidak_terpotong(): void
    {
        $pendaftaran = Pendaftaran::create([
            'user_id' => $this->peserta->id,
            'kuota_id' => $this->kuota->id,
            'bulan_magang_id' => $this->bulan->id,
            'status' => StatusPendaftaranEnum::Diajukan,
            'tipe' => 'mandiri',
            'durasi_bulan' => 3,
            'diajukan_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.pendaftaran.putuskan', $pendaftaran), [
                'keputusan' => 'tolak',
                'catatan' => 'Berkas transkrip nilai tidak memenuhi syarat minimum kedinasan.',
            ]);

        $response->assertRedirect(route('admin.pendaftaran.show', $pendaftaran));
        $response->assertSessionHas('success');

        // Status Ditolak, catatan tersimpan, kuota tetap 0
        $p = $pendaftaran->fresh();
        $this->assertSame(StatusPendaftaranEnum::Ditolak, $p->status);
        $this->assertSame('Berkas transkrip nilai tidak memenuhi syarat minimum kedinasan.', $p->catatan);
        $this->assertSame(0, $this->kuota->fresh()->terisi);

        // Tidak masuk ke antrean penempatan
        /** @var PenempatanAntreanQuery $antreanQuery */
        $antreanQuery = app(PenempatanAntreanQuery::class);
        $this->assertSame(0, $antreanQuery->getTotalBelumDitempatkan());
    }

    public function test_admin_dapat_mencari_pendaftaran_berdasarkan_nama_kelompok_dan_anggota(): void
    {
        $kelompok = KelompokMagang::create([
            'nama_kelompok' => 'Tim Khusus Sleman Digital',
            'asal_institusi' => 'Universitas Gadjah Mada',
            'ketua_user_id' => $this->peserta->id,
        ]);

        KelompokAnggota::create([
            'kelompok_magang_id' => $kelompok->id,
            'nama' => 'Bambang Sudarmanto',
            'nim_nisn' => '2201019999',
            'no_hp' => '081234567899',
        ]);

        $pendaftaran = Pendaftaran::create([
            'user_id' => $this->peserta->id,
            'kuota_id' => $this->kuota->id,
            'bulan_magang_id' => $this->bulan->id,
            'status' => StatusPendaftaranEnum::Diajukan,
            'tipe' => 'kelompok',
            'kelompok_magang_id' => $kelompok->id,
            'durasi_bulan' => 3,
            'diajukan_at' => now(),
        ]);

        // 1. Cari berdasarkan nama kelompok
        $responseKelompok = $this->actingAs($this->admin)
            ->get(route('admin.pendaftaran.index', ['q' => 'Sleman Digital']));
        $responseKelompok->assertOk();
        $responseKelompok->assertSee($pendaftaran->nomor_pendaftaran);

        // 2. Cari berdasarkan nama anggota kelompok
        $responseAnggota = $this->actingAs($this->admin)
            ->get(route('admin.pendaftaran.index', ['q' => 'Bambang Sudarmanto']));
        $responseAnggota->assertOk();
        $responseAnggota->assertSee($pendaftaran->nomor_pendaftaran);
    }

    public function test_penerimaan_pendaftaran_gagal_ramah_jika_kuota_penuh(): void
    {
        // Set kuota penuh: jumlah 1, terisi 1
        $this->kuota->update([
            'jumlah' => 1,
            'terisi' => 1,
        ]);

        $pendaftaran = Pendaftaran::create([
            'user_id' => $this->peserta->id,
            'kuota_id' => $this->kuota->id,
            'bulan_magang_id' => $this->bulan->id,
            'status' => StatusPendaftaranEnum::Diajukan,
            'tipe' => 'mandiri',
            'durasi_bulan' => 3,
            'diajukan_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)
            ->from(route('admin.pendaftaran.show', $pendaftaran))
            ->post(route('admin.pendaftaran.putuskan', $pendaftaran), [
                'keputusan' => 'terima',
                'catatan' => 'Coba terima saat kuota penuh.',
            ]);

        $response->assertRedirect(route('admin.pendaftaran.show', $pendaftaran));
        $response->assertSessionHas('error', 'Kuota umum telah terisi penuh. Pengajuan tidak dapat diterima.');

        // Status pendaftaran tetap Diajukan
        $this->assertSame(StatusPendaftaranEnum::Diajukan, $pendaftaran->fresh()->status);
        $this->assertSame(1, $this->kuota->fresh()->terisi);
    }

    public function test_admin_bisa_menolak_berkas_berstatus_perlu_perbaikan_secara_simetris(): void
    {
        $pendaftaran = Pendaftaran::create([
            'user_id' => $this->peserta->id,
            'kuota_id' => $this->kuota->id,
            'bulan_magang_id' => $this->bulan->id,
            'status' => StatusPendaftaranEnum::PerluPerbaikan,
            'tipe' => 'mandiri',
            'durasi_bulan' => 3,
            'diajukan_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.pendaftaran.putuskan', $pendaftaran), [
                'keputusan' => 'tolak',
                'catatan' => 'Berkas perbaikan tetap tidak memenuhi spesifikasi dinas.',
            ]);

        $response->assertRedirect(route('admin.pendaftaran.show', $pendaftaran));
        $response->assertSessionHas('success');

        $this->assertSame(StatusPendaftaranEnum::Ditolak, $pendaftaran->fresh()->status);
        $this->assertSame('Berkas perbaikan tetap tidak memenuhi spesifikasi dinas.', $pendaftaran->fresh()->catatan);
    }
}
