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

        // 2. Lengkapi Profil Biodata Peserta
        $responseProfil = $this->actingAs($peserta)->post(route('peserta.profil.simpan'), [
            'nim_nisn' => '1234567890',
            'institusi' => 'SMK Negeri 1 Kominfo',
            'jurusan_id' => $jurusan->id,
            'jenjang' => 'smk',
            'no_hp' => '081234567890',
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
}
