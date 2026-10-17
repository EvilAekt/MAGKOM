<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JenjangEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusPendaftaranEnum;
use App\Enums\StatusPenempatanEnum;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\Kuota;
use App\Models\Pendaftaran;
use App\Models\Penempatan;
use App\Models\Pengaturan;
use App\Models\PesertaProfil;
use App\Models\User;
use Database\Seeders\JenisDokumenSeeder;
use Database\Seeders\RubrikSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPenempatanLifecycleTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $pembimbing1;
    protected User $pembimbing2;
    protected User $peserta;
    protected Jurusan $jurusan;
    protected BulanMagang $bulan;
    protected Kuota $kuota;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(JenisDokumenSeeder::class);
        $this->seed(RubrikSeeder::class);

        $this->admin = new User();
        $this->admin->nama = 'Admin Magang';
        $this->admin->email = 'admin@slemankab.go.id';
        $this->admin->password = 'Password123!';
        $this->admin->role = RoleEnum::Admin;
        $this->admin->status_aktif = true;
        $this->admin->email_verified_at = now();
        $this->admin->save();

        $this->pembimbing1 = new User();
        $this->pembimbing1->nama = 'Pembimbing Satu';
        $this->pembimbing1->email = 'pembimbing1@slemankab.go.id';
        $this->pembimbing1->password = 'Password123!';
        $this->pembimbing1->role = RoleEnum::Pembimbing;
        $this->pembimbing1->status_aktif = true;
        $this->pembimbing1->email_verified_at = now();
        $this->pembimbing1->save();

        $this->pembimbing2 = new User();
        $this->pembimbing2->nama = 'Pembimbing Dua';
        $this->pembimbing2->email = 'pembimbing2@slemankab.go.id';
        $this->pembimbing2->password = 'Password123!';
        $this->pembimbing2->role = RoleEnum::Pembimbing;
        $this->pembimbing2->status_aktif = true;
        $this->pembimbing2->email_verified_at = now();
        $this->pembimbing2->save();

        $this->jurusan = Jurusan::create([
            'nama_jurusan' => 'Teknik Informatika',
            'kategori' => 'mahasiswa',
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
            'terisi' => 1,
            'aktif' => true,
        ]);

        $this->peserta = new User();
        $this->peserta->nama = 'Peserta Uji';
        $this->peserta->email = 'peserta.uji@example.com';
        $this->peserta->password = 'Password123!';
        $this->peserta->role = RoleEnum::Peserta;
        $this->peserta->status_aktif = true;
        $this->peserta->email_verified_at = now();
        $this->peserta->save();

        PesertaProfil::create([
            'user_id' => $this->peserta->id,
            'nim_nisn' => '12345678',
            'institusi' => 'Universitas Sleman',
            'jurusan_id' => $this->jurusan->id,
            'jenjang' => JenjangEnum::S1,
            'no_hp' => '081234567890',
        ]);
    }

    public function test_pembatalan_sebelum_tanggal_mulai_mengembalikan_kuota_dan_akun_peserta_tetap_aktif(): void
    {
        $pendaftaran = Pendaftaran::create([
            'user_id' => $this->peserta->id,
            'bulan_magang_id' => $this->bulan->id,
            'kuota_id' => $this->kuota->id,
            'nomor_pendaftaran' => 'REG-TEST-001',
            'asal_instansi' => 'Universitas Sleman',
            'durasi_bulan' => 3,
            'status' => StatusPendaftaranEnum::Diterima,
            'diajukan_at' => now()->subDays(10),
        ]);

        $penempatan = Penempatan::create([
            'pendaftaran_id' => $pendaftaran->id,
            'peserta_id' => $this->peserta->id,
            'pembimbing_id' => $this->pembimbing1->id,
            'kuota_id' => $this->kuota->id,
            'jalur' => \App\Enums\JalurEnum::Umum,
            'sumber' => \App\Enums\SumberPenempatanEnum::Online,
            'tgl_mulai' => now()->addDays(14)->toDateString(), // sebelum mulai
            'tgl_selesai' => now()->addDays(104)->toDateString(),
            'status' => StatusPenempatanEnum::Aktif,
        ]);

        $this->kuota->update(['terisi' => 5]);

        $response = $this->actingAs($this->admin)->post(route('admin.penempatan.batalkan', $penempatan), [
            'alasan' => 'Peserta mengajukan surat permohonan pembatalan karena sakit berkepanjangan.',
        ]);

        $response->assertRedirect(route('admin.penempatan.index'));
        $response->assertSessionHas('success');

        $penempatan->refresh();
        $pendaftaran->refresh();
        $this->kuota->refresh();
        $this->peserta->refresh();

        $this->assertSame(StatusPenempatanEnum::MengundurkanDiri, $penempatan->status);
        $this->assertSame(StatusPendaftaranEnum::Dibatalkan, $pendaftaran->status);
        $this->assertSame(4, $this->kuota->terisi); // Kuota dikembalikan (5 -> 4)
        $this->assertTrue((bool) $this->peserta->status_aktif); // Akun tetap aktif
    }

    public function test_pengunduran_diri_setelah_tanggal_mulai_kuota_tidak_kembali_dan_akun_nonaktif(): void
    {
        $pendaftaran = Pendaftaran::create([
            'user_id' => $this->peserta->id,
            'bulan_magang_id' => $this->bulan->id,
            'kuota_id' => $this->kuota->id,
            'nomor_pendaftaran' => 'REG-TEST-002',
            'asal_instansi' => 'Universitas Sleman',
            'durasi_bulan' => 3,
            'status' => StatusPendaftaranEnum::Diterima,
            'diajukan_at' => now()->subDays(30),
        ]);

        $penempatan = Penempatan::create([
            'pendaftaran_id' => $pendaftaran->id,
            'peserta_id' => $this->peserta->id,
            'pembimbing_id' => $this->pembimbing1->id,
            'kuota_id' => $this->kuota->id,
            'jalur' => \App\Enums\JalurEnum::Umum,
            'sumber' => \App\Enums\SumberPenempatanEnum::Online,
            'tgl_mulai' => now()->subDays(5)->toDateString(), // sudah dimulai
            'tgl_selesai' => now()->addDays(85)->toDateString(),
            'status' => StatusPenempatanEnum::Aktif,
        ]);

        $this->kuota->update(['terisi' => 5]);

        $response = $this->actingAs($this->admin)->post(route('admin.penempatan.batalkan', $penempatan), [
            'alasan' => 'Peserta mengundurkan diri di tengah program magang karena diterima kerja.',
        ]);

        $response->assertRedirect(route('admin.penempatan.index'));

        $penempatan->refresh();
        $pendaftaran->refresh();
        $this->kuota->refresh();
        $this->peserta->refresh();

        $this->assertSame(StatusPenempatanEnum::MengundurkanDiri, $penempatan->status);
        $this->assertSame(StatusPendaftaranEnum::Diterima, $pendaftaran->status); // Status pendaftaran tetap diterima
        $this->assertSame(5, $this->kuota->terisi); // Kuota hangus / tidak kembali
        $this->assertFalse((bool) $this->peserta->status_aktif); // Akun peserta dinonaktifkan
    }

    public function test_pindah_pembimbing_berhasil_dan_terekam_audit(): void
    {
        $penempatan = Penempatan::create([
            'peserta_id' => $this->peserta->id,
            'pembimbing_id' => $this->pembimbing1->id,
            'kuota_id' => $this->kuota->id,
            'jalur' => \App\Enums\JalurEnum::Umum,
            'sumber' => \App\Enums\SumberPenempatanEnum::Online,
            'tgl_mulai' => now()->toDateString(),
            'tgl_selesai' => now()->addDays(90)->toDateString(),
            'status' => StatusPenempatanEnum::Aktif,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.penempatan.pindah-pembimbing', $penempatan), [
            'pembimbing_id' => $this->pembimbing2->id,
        ]);

        $response->assertRedirect(route('admin.penempatan.index'));
        $penempatan->refresh();
        $this->assertSame($this->pembimbing2->id, $penempatan->pembimbing_id);
    }

    public function test_pindah_pembimbing_ditolak_jika_kapasitas_beban_pembimbing_tujuan_penuh(): void
    {
        Pengaturan::updateOrCreate(
            ['kunci' => 'maks_peserta_aktif_per_pembimbing'],
            ['nilai' => '1', 'tipe' => 'integer']
        );

        // Buat mentee yang sudah ada untuk pembimbing 2
        $menteeLain = new User();
        $menteeLain->nama = 'Mentee Lain';
        $menteeLain->email = 'mentee.lain@example.com';
        $menteeLain->password = 'Password123!';
        $menteeLain->role = RoleEnum::Peserta;
        $menteeLain->status_aktif = true;
        $menteeLain->email_verified_at = now();
        $menteeLain->save();

        Penempatan::create([
            'peserta_id' => $menteeLain->id,
            'pembimbing_id' => $this->pembimbing2->id,
            'jalur' => \App\Enums\JalurEnum::Umum,
            'sumber' => \App\Enums\SumberPenempatanEnum::Online,
            'tgl_mulai' => now()->toDateString(),
            'tgl_selesai' => now()->addDays(90)->toDateString(),
            'status' => StatusPenempatanEnum::Aktif,
        ]);

        // Penempatan yang akan dipindahkan
        $penempatan = Penempatan::create([
            'peserta_id' => $this->peserta->id,
            'pembimbing_id' => $this->pembimbing1->id,
            'jalur' => \App\Enums\JalurEnum::Umum,
            'sumber' => \App\Enums\SumberPenempatanEnum::Online,
            'tgl_mulai' => now()->toDateString(),
            'tgl_selesai' => now()->addDays(90)->toDateString(),
            'status' => StatusPenempatanEnum::Aktif,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.penempatan.pindah-pembimbing', $penempatan), [
            'pembimbing_id' => $this->pembimbing2->id,
        ]);

        $response->assertSessionHas('error');
        $penempatan->refresh();
        $this->assertSame($this->pembimbing1->id, $penempatan->pembimbing_id); // Tidak berubah
    }
}
