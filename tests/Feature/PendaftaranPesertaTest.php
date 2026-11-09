<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JenjangEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusPendaftaranEnum;
use App\Enums\StatusPenempatanEnum;
use App\Models\BulanMagang;
use App\Models\JenisDokumen;
use App\Models\Jurusan;
use App\Models\KelompokMagang;
use App\Models\Kuota;
use App\Models\Pendaftaran;
use App\Models\Penempatan;
use App\Models\PesertaProfil;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PendaftaranPesertaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('private');

        Carbon::setTestNow(Carbon::parse('2026-10-08 10:00:00', 'Asia/Jakarta'));
        CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-10-08 10:00:00', 'Asia/Jakarta'));

        if (Jurusan::count() === 0 || BulanMagang::count() === 0 || JenisDokumen::count() === 0) {
            $this->seed();
        }
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        CarbonImmutable::setTestNow();
        parent::tearDown();
    }

    public function test_alur_lengkap_pendaftaran_peserta_upload_berkas_dan_verifikasi_admin(): void
    {
        // 1. Siapkan Aktor: Peserta, Admin, dan Pembimbing
        $jurusan = Jurusan::where('kategori', 'smk')->firstOrFail();

        $peserta = User::firstOrNew(['email' => 'peserta.smk@kominfo.test']);
        $peserta->nama = 'Ahmad Siswa';
        $peserta->password = bcrypt('Password#2026');
        $peserta->role = RoleEnum::Peserta;
        $peserta->email_verified_at = now();
        $peserta->status_aktif = true;
        $peserta->save();

        $admin = User::firstOrNew(['email' => 'admin.verif@kominfo.test']);
        $admin->nama = 'Admin Verifikator';
        $admin->password = bcrypt('Password#2026');
        $admin->role = RoleEnum::Admin;
        $admin->email_verified_at = now();
        $admin->status_aktif = true;
        $admin->save();

        $pembimbing = User::firstOrNew(['email' => 'pembimbing.teknis@kominfo.test']);
        $pembimbing->nama = 'Budi Pembimbing';
        $pembimbing->password = bcrypt('Password#2026');
        $pembimbing->role = RoleEnum::Pembimbing;
        $pembimbing->email_verified_at = now();
        $pembimbing->status_aktif = true;
        $pembimbing->save();

        // 2. Lengkapi Profil Biodata Peserta (100% Lengkap)
        Storage::fake('public');
        $fakeFoto = UploadedFile::fake()->image('pasfoto.jpg', 300, 400);

        $responseProfil = $this->actingAs($peserta)->post(route('peserta.profil.simpan'), [
            'nim_nisn' => '1234567890',
            'institusi' => 'SMK Negeri 1 Kominfo',
            'jurusan_id' => $jurusan->id,
            'jenjang' => 'smk',
            'no_hp' => '081234567890',
            'nama_kontak_darurat' => 'Budi Santoso',
            'no_kontak_darurat' => '089876543210',
            'hubungan_kontak_darurat' => 'Orang Tua',
            'alamat' => 'Sleman, Yogyakarta',
            'foto' => $fakeFoto,
        ]);
        $responseProfil->assertRedirect(route('peserta.profil.show'));
        $this->assertDatabaseHas('peserta_profil', ['user_id' => $peserta->id, 'nim_nisn' => '1234567890']);

        // 3. Buat Pengajuan Pendaftaran Magang
        $bulanNov = BulanMagang::whereDate('bulan_mulai', '2026-11-01')->firstOrFail();
        $kuota = Kuota::where('bulan_magang_id', $bulanNov->id)
            ->where('jurusan_id', $jurusan->id)
            ->firstOrFail();
        $kuota->update(['jumlah' => 10, 'terisi' => 2]);

        $responsePendaftaran = $this->actingAs($peserta)->post(route('peserta.pendaftaran.store'), [
            'bulan_magang_id' => $bulanNov->id,
            'kuota_id' => $kuota->id,
            'durasi_bulan' => 3,
        ]);

        $pendaftaran = Pendaftaran::where('user_id', $peserta->id)->firstOrFail();
        $responsePendaftaran->assertRedirect(route('peserta.pendaftaran.detail', $pendaftaran));
        $this->assertEquals(StatusPendaftaranEnum::BerkasBelumLengkap, $pendaftaran->status);

        // 4. Unggah Dokumen Wajib Persyaratan SMK
        $dokumenWajib = JenisDokumen::where('jenjang', JenjangEnum::Smk->value)
            ->where('wajib', true)
            ->get();

        foreach ($dokumenWajib as $jenis) {
            $fakePdf = UploadedFile::fake()->create("{$jenis->kode}.pdf", 100, 'application/pdf');

            $resUpload = $this->actingAs($peserta)->post(
                route('peserta.berkas.unggah', $pendaftaran),
                [
                    'jenis_dokumen_id' => $jenis->id,
                    'file' => $fakePdf,
                ]
            );
            $resUpload->assertSessionHas('success');
        }

        // Pastikan berkas fisik tercatat di database dan tersimpan di storage fake privat
        $this->assertEquals($dokumenWajib->count(), $pendaftaran->dokumen()->count());
        $dokumenPertama = $pendaftaran->dokumen()->first();
        Storage::disk('private')->assertExists($dokumenPertama->path);
        $this->assertNotEmpty($dokumenPertama->hash);

        // 5. Ajukan Berkas Pendaftaran
        $resAjukan = $this->actingAs($peserta)->post(route('peserta.pendaftaran.ajukan', $pendaftaran));
        $resAjukan->assertSessionHas('success');
        $pendaftaran->refresh();
        $this->assertEquals(StatusPendaftaranEnum::Diajukan, $pendaftaran->status);

        // 6. Penegakan BR-17: Percobaan membuat pendaftaran aktif kedua DITOLAK
        $resPendaftaranKedua = $this->actingAs($peserta)->post(route('peserta.pendaftaran.store'), [
            'bulan_magang_id' => $bulanNov->id,
            'kuota_id' => $kuota->id,
            'durasi_bulan' => 3,
        ]);
        $resPendaftaranKedua->assertSessionHasErrors(['pendaftaran']);

        // 7. Admin Memverifikasi & Menerima Pendaftaran
        $terisiAwal = $kuota->terisi;

        $resPutusan = $this->actingAs($admin)->post(
            route('admin.pendaftaran.putuskan', $pendaftaran),
            [
                'keputusan' => 'terima',
                'pembimbing_id' => $pembimbing->id,
            ]
        );
        $resPutusan->assertSessionHas('success');

        // Verifikasi pendaftaran menjadi Diterima dan kuota bertambah
        $pendaftaran->refresh();
        $this->assertEquals(StatusPendaftaranEnum::Diterima, $pendaftaran->status);
        $this->assertEquals($admin->id, $pendaftaran->verifikator_id);

        $kuota->refresh();
        $this->assertEquals($terisiAwal + 1, $kuota->terisi);

        // Verifikasi entri Penempatan terbentuk resmi
        $penempatan = Penempatan::where('pendaftaran_id', $pendaftaran->id)->firstOrFail();
        $this->assertEquals($peserta->id, $penempatan->peserta_id);
        $this->assertEquals($pembimbing->id, $penempatan->pembimbing_id);
        $this->assertEquals(StatusPenempatanEnum::Aktif, $penempatan->status);
        $this->assertEquals('2026-11-01', $penempatan->tgl_mulai->format('Y-m-d'));
        $this->assertEquals('2027-01-31', $penempatan->tgl_selesai->format('Y-m-d'));

        // 8. Uji Unduh Berkas Privat dengan Temporary Signed URL (AC-03-9)
        $signedUrl = $dokumenPertama->getSignedDownloadUrl(5);

        // Peserta pemilik berhasil mengunduh
        $resDownloadPemilik = $this->actingAs($peserta)->get($signedUrl);
        $resDownloadPemilik->assertStatus(200);

        // Akses tanpa signed URL valid ditolak (403 Invalid Signature)
        $unsignedUrl = route('berkas.download', $dokumenPertama);
        $resDownloadUnsigned = $this->actingAs($peserta)->get($unsignedUrl);
        $resDownloadUnsigned->assertStatus(403);

        // Peserta lain yang mencoba mengunduh URL bertanda tangan ditolak oleh DokumenPolicy (403)
        $pesertaLain = User::firstOrNew(['email' => 'peserta.asing@test.com']);
        $pesertaLain->nama = 'Peserta Lain';
        $pesertaLain->password = bcrypt('Password#2026');
        $pesertaLain->role = RoleEnum::Peserta;
        $pesertaLain->email_verified_at = now();
        $pesertaLain->status_aktif = true;
        $pesertaLain->save();

        $resDownloadPesertaLain = $this->actingAs($pesertaLain)->get($signedUrl);
        $resDownloadPesertaLain->assertStatus(403);
    }

    public function test_show_register_menyediakan_data_jurusan_dan_bidang(): void
    {
        $response = $this->get(route('register'));

        $response->assertStatus(200);
        $response->assertViewHas('jurusanList');
        $response->assertViewHas('bidangList');
    }

    public function test_registrasi_dengan_jenjang_dan_jurusan_otomatis_membuat_peserta_profil(): void
    {
        $jurusan = Jurusan::where('kategori', 'mahasiswa')->firstOrFail();

        $response = $this->post(route('register'), [
            'username' => 'Mahasiswa Pendaftar Baru',
            'email' => 'mahasiswa.baru@magkom.test',
            'no_telp' => '081234567899',
            'password' => 'Password123!',
            'jenjang' => 's1',
            'jurusan_id' => $jurusan->id,
            'institusi' => 'Universitas Gadjah Mada',
            'nim_nisn' => '21/475890/TK/52000',
        ]);

        $response->assertRedirect(route('peserta.dashboard'));

        $this->assertDatabaseHas('users', [
            'email' => 'mahasiswa.baru@magkom.test',
            'nama' => 'Mahasiswa Pendaftar Baru',
        ]);

        $user = User::where('email', 'mahasiswa.baru@magkom.test')->firstOrFail();
        $this->assertNotNull($user->profil);
        $this->assertEquals(JenjangEnum::S1, $user->profil->jenjang);
        $this->assertEquals($jurusan->id, $user->profil->jurusan_id);
        $this->assertEquals('Universitas Gadjah Mada', $user->profil->institusi);
        $this->assertEquals('21/475890/TK/52000', $user->profil->nim_nisn);
    }

    public function test_registrasi_smk_dengan_persetujuan_wali_mencatat_timestamp_uu_pdp(): void
    {
        $jurusan = Jurusan::where('kategori', 'smk')->firstOrFail();

        $response = $this->post(route('register'), [
            'username' => 'Siswa SMK Mandiri',
            'email' => 'siswa.smk@magkom.test',
            'no_telp' => '081234567888',
            'password' => 'Password123!',
            'jenjang' => 'smk',
            'jurusan_id' => $jurusan->id,
            'institusi' => 'SMKN 2 Depok Sleman',
            'nim_nisn' => '1234567890',
            'persetujuan_wali' => '1',
        ]);

        $response->assertRedirect(route('peserta.dashboard'));

        $user = User::where('email', 'siswa.smk@magkom.test')->firstOrFail();
        $this->assertNotNull($user->profil);
        $this->assertEquals(JenjangEnum::Smk, $user->profil->jenjang);
        $this->assertEquals($jurusan->id, $user->profil->jurusan_id);
        $this->assertNotNull($user->profil->persetujuan_wali_at);
    }

    public function test_peserta_dengan_profil_belum_100_persen_tidak_bisa_memilih_kuota_atau_mendaftar(): void
    {
        $peserta = new User([
            'nama' => 'Peserta Incomplete',
            'email' => 'peserta.incomplete@magkom.test',
            'password' => bcrypt('Password123!'),
            'status_aktif' => true,
        ]);
        $peserta->role = RoleEnum::Peserta;
        $peserta->email_verified_at = now();
        $peserta->save();

        $jurusan = Jurusan::firstOrFail();
        $profil = PesertaProfil::create([
            'user_id' => $peserta->id,
            'jenjang' => JenjangEnum::S1,
            'jurusan_id' => $jurusan->id,
            'institusi' => 'Universitas Sleman',
            'nim_nisn' => '12345678',
            'no_hp' => '081234567890',
            // sengaja foto dan kontak darurat belum diisi (belum 100%)
            'foto' => null,
            'nama_kontak_darurat' => null,
            'no_kontak_darurat' => null,
        ]);

        $this->assertFalse($profil->isLengkap());
        $this->assertLessThan(100, $profil->hitungKelengkapan()['persentase']);

        // 1. Coba akses halaman pemilihan kuota langsung -> harus di-redirect ke profil
        $responseIndex = $this->actingAs($peserta)->get(route('peserta.pendaftaran.index'));
        $responseIndex->assertRedirect(route('peserta.profil.show'));
        $responseIndex->assertSessionHas('warning');

        // 2. Coba kirim form pendaftaran -> harus gagal validasi profil
        $bulan = BulanMagang::firstOrFail();
        $kuota = Kuota::where('bulan_magang_id', $bulan->id)->where('jurusan_id', $jurusan->id)->firstOrFail();

        $responsePost = $this->actingAs($peserta)->post(route('peserta.pendaftaran.store'), [
            'bulan_magang_id' => $bulan->id,
            'kuota_id' => $kuota->id,
            'durasi_bulan' => 3,
        ]);

        $responsePost->assertSessionHasErrors(['profil']);
        $this->assertDatabaseMissing('pendaftaran', ['user_id' => $peserta->id]);
    }

    public function test_pengunggahan_berkas_kelompok_dan_verifikasi_kelengkapan_anggota(): void
    {
        Storage::fake('private');

        $jurusan = Jurusan::where('kategori', 'mahasiswa')->firstOrFail();
        $bulan = BulanMagang::firstOrFail();
        $kuota = Kuota::where('bulan_magang_id', $bulan->id)->where('jurusan_id', $jurusan->id)->firstOrFail();

        // 1. Buat user ketua tim
        $ketua = new User([
            'nama' => 'Ketua Kelompok Alpha',
            'email' => 'ketua.alpha@magkom.test',
            'password' => bcrypt('Password123!'),
            'status_aktif' => true,
        ]);
        $ketua->role = RoleEnum::Peserta;
        $ketua->email_verified_at = now();
        $ketua->save();

        PesertaProfil::create([
            'user_id' => $ketua->id,
            'jenjang' => JenjangEnum::S1,
            'jurusan_id' => $jurusan->id,
            'institusi' => 'Universitas Gadjah Mada',
            'nim_nisn' => '21/111111/TK/00001',
            'no_hp' => '081211111111',
            'foto' => 'photos/ketua.jpg',
            'nama_kontak_darurat' => 'Bapak Ketua',
            'no_kontak_darurat' => '081299999999',
            'alamat_domisili' => 'Sleman',
            'alamat_ktp' => 'Sleman',
        ]);

        // Buat kelompok magang & anggota
        $kelompok = KelompokMagang::create([
            'ketua_user_id' => $ketua->id,
            'nama_kelompok' => 'Tim Riset Alpha',
        ]);

        $anggota = $kelompok->anggota()->create([
            'nama' => 'Budi Anggota Alpha',
            'nim_nisn' => '21/111112/TK/00002',
            'no_hp' => '081211111112',
            'email' => 'budi.alpha@magkom.test',
        ]);

        // Buat pendaftaran kelompok
        $pendaftaran = Pendaftaran::create([
            'user_id' => $ketua->id,
            'bulan_magang_id' => $bulan->id,
            'kuota_id' => $kuota->id,
            'durasi_bulan' => 3,
            'tipe' => 'kelompok',
            'kelompok_magang_id' => $kelompok->id,
            'status' => StatusPendaftaranEnum::BerkasBelumLengkap,
        ]);

        $dokS1List = JenisDokumen::where('jenjang', 's1')->where('aktif', true)->get();
        $dokSurat = $dokS1List->where('kode', 'surat_pengantar')->firstOrFail();
        $dokProposal = $dokS1List->where('kode', 'proposal')->firstOrFail();
        $dokCv = $dokS1List->where('kode', 'cv')->firstOrFail();
        $dokTranskrip = $dokS1List->where('kode', 'transkrip')->firstOrFail();

        // Unggah berkas umum
        $fileDummy = UploadedFile::fake()->create('surat.pdf', 200, 'application/pdf');
        $this->actingAs($ketua)->post(route('peserta.berkas.unggah', $pendaftaran), [
            'jenis_dokumen_id' => $dokSurat->id,
            'file' => $fileDummy,
        ])->assertSessionHas('success');

        $this->actingAs($ketua)->post(route('peserta.berkas.unggah', $pendaftaran), [
            'jenis_dokumen_id' => $dokProposal->id,
            'file' => $fileDummy,
        ])->assertSessionHas('success');

        // Unggah berkas ketua
        $this->actingAs($ketua)->post(route('peserta.berkas.unggah', $pendaftaran), [
            'jenis_dokumen_id' => $dokCv->id,
            'file' => $fileDummy,
        ])->assertSessionHas('success');

        $this->actingAs($ketua)->post(route('peserta.berkas.unggah', $pendaftaran), [
            'jenis_dokumen_id' => $dokTranskrip->id,
            'file' => $fileDummy,
        ])->assertSessionHas('success');

        // Sebelum berkas anggota diunggah, ajukan harus gagal
        $resGagal = $this->actingAs($ketua)->post(route('peserta.pendaftaran.ajukan', $pendaftaran));
        $resGagal->assertSessionHas('error');

        // Unggah berkas anggota
        $this->actingAs($ketua)->post(route('peserta.berkas.unggah', $pendaftaran), [
            'jenis_dokumen_id' => $dokCv->id,
            'kelompok_anggota_id' => $anggota->id,
            'file' => $fileDummy,
        ])->assertSessionHas('success');

        $this->actingAs($ketua)->post(route('peserta.berkas.unggah', $pendaftaran), [
            'jenis_dokumen_id' => $dokTranskrip->id,
            'kelompok_anggota_id' => $anggota->id,
            'file' => $fileDummy,
        ])->assertSessionHas('success');

        // Sekarang ajukan harus sukses
        $resSukses = $this->actingAs($ketua)->post(route('peserta.pendaftaran.ajukan', $pendaftaran));
        $resSukses->assertRedirect();
        $pendaftaran->refresh();
        $this->assertEquals(StatusPendaftaranEnum::Diajukan, $pendaftaran->status);
    }
}
