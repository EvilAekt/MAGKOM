<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JalurEnum;
use App\Enums\JenjangEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\StatusTugasEnum;
use App\Enums\SumberPenempatanEnum;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\Penempatan;
use App\Models\PesertaProfil;
use App\Models\Tugas;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PembimbingMultiMenteeTugasSchedulingTest extends TestCase
{
    use RefreshDatabase;

    protected User $pembimbing1;

    protected User $pembimbing2;

    protected User $peserta1;

    protected User $peserta2;

    protected User $pesertaLain;

    protected Penempatan $penempatan1;

    protected Penempatan $penempatan2;

    protected Penempatan $penempatanLain;

    protected Bidang $bidang;

    private function createUser(string $nama, string $email, RoleEnum $role, ?int $bidangId = null): User
    {
        $user = new User;
        $user->nama = $nama;
        $user->email = $email;
        $user->password = Hash::make('Password123!');
        $user->role = $role;
        $user->bidang_id = $bidangId;
        $user->status_aktif = true;
        $user->email_verified_at = now();
        $user->must_change_password = false;
        $user->save();

        return $user;
    }

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('private');

        $this->bidang = Bidang::create(['nama_bidang' => 'Aplikasi Informatika']);
        $jurusan = Jurusan::create([
            'nama_jurusan' => 'Informatika',
            'kategori' => 'mahasiswa',
            'aktif' => true,
        ]);
        $bulan = BulanMagang::create([
            'bulan_mulai' => now()->startOfMonth(),
            'tgl_buka' => now()->subDays(5)->toDateString(),
            'tgl_tutup' => now()->addDays(20)->toDateString(),
            'aktif' => true,
        ]);

        $this->pembimbing1 = $this->createUser(
            'Budi Santoso, S.Kom',
            'budi.santoso@magkom.go.id',
            RoleEnum::Pembimbing,
            $this->bidang->id
        );

        $this->pembimbing2 = $this->createUser(
            'Pembimbing Lain',
            'pembimbing.lain@magkom.go.id',
            RoleEnum::Pembimbing,
            $this->bidang->id
        );

        $this->peserta1 = $this->createUser('Naufal Pratama', 'naufal@magkom.go.id', RoleEnum::Peserta);
        $this->peserta2 = $this->createUser('Rina Wijayanti', 'rina@magkom.go.id', RoleEnum::Peserta);
        $this->pesertaLain = $this->createUser('Peserta Asing', 'asing@magkom.go.id', RoleEnum::Peserta);

        PesertaProfil::create([
            'user_id' => $this->peserta1->id,
            'jurusan_id' => $jurusan->id,
            'institusi' => 'UGM Yogyakarta',
            'jenjang' => JenjangEnum::S1,
            'nim_nisn' => '20110001',
            'no_hp' => '081234567891',
        ]);

        PesertaProfil::create([
            'user_id' => $this->peserta2->id,
            'jurusan_id' => $jurusan->id,
            'institusi' => 'UNY Yogyakarta',
            'jenjang' => JenjangEnum::S1,
            'nim_nisn' => '20110002',
            'no_hp' => '081234567892',
        ]);

        PesertaProfil::create([
            'user_id' => $this->pesertaLain->id,
            'jurusan_id' => $jurusan->id,
            'institusi' => 'Institusi Luar',
            'jenjang' => JenjangEnum::S1,
            'nim_nisn' => '20110003',
            'no_hp' => '081234567893',
        ]);

        $this->penempatan1 = Penempatan::create([
            'peserta_id' => $this->peserta1->id,
            'pembimbing_id' => $this->pembimbing1->id,
            'bidang_id' => $this->bidang->id,
            'bulan_magang_id' => $bulan->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'tgl_mulai' => Carbon::today()->subDays(10),
            'tgl_selesai' => Carbon::today()->addDays(30),
            'status' => StatusPenempatanEnum::Aktif,
        ]);

        $this->penempatan2 = Penempatan::create([
            'peserta_id' => $this->peserta2->id,
            'pembimbing_id' => $this->pembimbing1->id,
            'bidang_id' => $this->bidang->id,
            'bulan_magang_id' => $bulan->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'tgl_mulai' => Carbon::today()->subDays(10),
            'tgl_selesai' => Carbon::today()->addDays(20),
            'status' => StatusPenempatanEnum::Aktif,
        ]);

        $this->penempatanLain = Penempatan::create([
            'peserta_id' => $this->pesertaLain->id,
            'pembimbing_id' => $this->pembimbing2->id,
            'bidang_id' => $this->bidang->id,
            'bulan_magang_id' => $bulan->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'tgl_mulai' => Carbon::today()->subDays(10),
            'tgl_selesai' => Carbon::today()->addDays(30),
            'status' => StatusPenempatanEnum::Aktif,
        ]);
    }

    public function test_pembimbing_berhasil_membuat_tugas_langsung_untuk_banyak_mentee(): void
    {
        $file = UploadedFile::fake()->create('panduan_arsitektur.pdf', 500, 'application/pdf');

        $response = $this->actingAs($this->pembimbing1)->post(route('pembimbing.tugas.store'), [
            'penempatan_ids' => [$this->penempatan1->id, $this->penempatan2->id],
            'judul' => 'Perancangan API Endpoint Layanan Publik',
            'deskripsi' => 'Susun spesifikasi OpenAPI 3.0 dan relasi skema database.',
            'link_referensi' => 'https://github.com/magkom-sleman/api-spec',
            'file_lampiran' => $file,
            'kategori' => 'Pengembangan Web',
            'kompleksitas' => 'sedang',
            'deadline' => Carbon::today()->addDays(14)->toDateString(),
            'aksi_publikasi' => 'sekarang',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('pembimbing.tugas.index'));

        // Pastikan kedua record tugas terbuat dengan batch_id yang sama
        $tugasList = Tugas::where('judul', 'Perancangan API Endpoint Layanan Publik')->get();
        $this->assertCount(2, $tugasList);

        $tugas1 = $tugasList->firstWhere('penempatan_id', $this->penempatan1->id);
        $tugas2 = $tugasList->firstWhere('penempatan_id', $this->penempatan2->id);

        $this->assertNotNull($tugas1);
        $this->assertNotNull($tugas2);
        $this->assertEquals($tugas1->batch_id, $tugas2->batch_id);
        $this->assertEquals('dipublikasikan', $tugas1->status_publikasi);
        $this->assertEquals('dipublikasikan', $tugas2->status_publikasi);
        $this->assertEquals('panduan_arsitektur.pdf', $tugas1->nama_file_lampiran);
        $this->assertEquals('https://github.com/magkom-sleman/api-spec', $tugas1->link_referensi);

        // Berkas fisik tersimpan 1 kali di storage
        Storage::disk('private')->assertExists($tugas1->file_lampiran);
        $this->assertEquals($tugas1->file_lampiran, $tugas2->file_lampiran);
    }

    public function test_pembimbing_ditolak_jika_memilih_peserta_di_luar_kewenangannya_anti_idor(): void
    {
        $response = $this->actingAs($this->pembimbing1)->post(route('pembimbing.tugas.store'), [
            'penempatan_ids' => [$this->penempatan1->id, $this->penempatanLain->id],
            'judul' => 'Tugas Percobaan IDOR',
            'deskripsi' => 'Mencoba menugaskan peserta dari pembimbing lain',
            'kategori' => 'Pengembangan Web',
            'kompleksitas' => 'rendah',
            'deadline' => Carbon::today()->addDays(5)->toDateString(),
        ]);

        $response->assertSessionHasErrors(['penempatan_ids']);
        $this->assertDatabaseMissing('tugas', ['judul' => 'Tugas Percobaan IDOR']);
    }

    public function test_validasi_deadline_menolak_tanggal_melampaui_selesai_magang_salah_satu_mentee(): void
    {
        // penempatan2 selesai dalam 20 hari, sedangkan penempatan1 selesai dalam 30 hari.
        // Jika deadline dibuat 25 hari ke depan, penempatan2 melampaui batas akhir magangnya.
        $response = $this->actingAs($this->pembimbing1)->post(route('pembimbing.tugas.store'), [
            'penempatan_ids' => [$this->penempatan1->id, $this->penempatan2->id],
            'judul' => 'Tugas Melebihi Deadline',
            'deskripsi' => 'Deskripsi penugasan',
            'kategori' => 'Analisis Data',
            'kompleksitas' => 'tinggi',
            'deadline' => Carbon::today()->addDays(25)->toDateString(),
        ]);

        $response->assertSessionHasErrors(['deadline']);
        $this->assertDatabaseMissing('tugas', ['judul' => 'Tugas Melebihi Deadline']);
    }

    public function test_validasi_upload_menolak_format_berkas_dan_ukuran_tidak_valid(): void
    {
        $fileExe = UploadedFile::fake()->create('malicious.exe', 100, 'application/x-msdownload');

        $response = $this->actingAs($this->pembimbing1)->post(route('pembimbing.tugas.store'), [
            'penempatan_ids' => [$this->penempatan1->id],
            'judul' => 'Tugas File Tidak Valid',
            'deskripsi' => 'Deskripsi',
            'file_lampiran' => $fileExe,
            'kategori' => 'Keamanan',
            'kompleksitas' => 'sedang',
            'deadline' => Carbon::today()->addDays(5)->toDateString(),
        ]);

        $response->assertSessionHasErrors(['file_lampiran']);
    }

    public function test_validasi_link_menolak_url_tidak_valid(): void
    {
        $response = $this->actingAs($this->pembimbing1)->post(route('pembimbing.tugas.store'), [
            'penempatan_ids' => [$this->penempatan1->id],
            'judul' => 'Tugas Link Tidak Valid',
            'deskripsi' => 'Deskripsi',
            'link_referensi' => 'bukan-url-resmi',
            'kategori' => 'Pengembangan Web',
            'kompleksitas' => 'sedang',
            'deadline' => Carbon::today()->addDays(5)->toDateString(),
        ]);

        $response->assertSessionHasErrors(['link_referensi']);
    }

    public function test_pembimbing_bisa_menjadwalkan_tugas_dan_peserta_tidak_bisa_melihat_sebelum_jadwal(): void
    {
        $jadwalPublikasiDate = Carbon::today()->addDays(3)->toDateString();
        $jadwalPublikasiTime = '08:00';
        $deadlineDate = Carbon::today()->addDays(10)->toDateString();

        $response = $this->actingAs($this->pembimbing1)->post(route('pembimbing.tugas.store'), [
            'penempatan_ids' => [$this->penempatan1->id],
            'judul' => 'Tugas Terjadwal Masa Depan',
            'deskripsi' => 'Tugas ini dijadwalkan 3 hari lagi',
            'kategori' => 'Dokumentasi Teknis',
            'kompleksitas' => 'sedang',
            'deadline' => $deadlineDate,
            'aksi_publikasi' => 'terjadwal',
            'tgl_publikasi' => $jadwalPublikasiDate,
            'waktu_publikasi' => $jadwalPublikasiTime,
        ]);

        $response->assertSessionHasNoErrors();
        $tugas = Tugas::where('judul', 'Tugas Terjadwal Masa Depan')->first();
        $this->assertNotNull($tugas);
        $this->assertEquals('terjadwal', $tugas->status_publikasi);
        $this->assertTrue($tugas->isTerjadwal());

        // 1. Peserta memeriksa daftar tugas: tugas terjadwal TIDAK muncul
        $respList = $this->actingAs($this->peserta1)->get(route('peserta.tugas.index'));
        $respList->assertOk();
        $respList->assertDontSee('Tugas Terjadwal Masa Depan');

        // 2. Peserta mencoba mengakses detail tugas via ID: ditolak 403 Forbidden
        $respDetail = $this->actingAs($this->peserta1)->get(route('peserta.tugas.show', $tugas));
        $respDetail->assertForbidden();

        // 3. Peserta mencoba mengunggah progres: ditolak 403 Forbidden
        $respProgres = $this->actingAs($this->peserta1)->post(route('peserta.tugas.progres', $tugas), [
            'catatan' => 'Mencoba bypass submit progres sebelum publikasi',
        ]);
        $respProgres->assertForbidden();
    }

    public function test_command_scheduler_otomatis_mempublikasikan_tugas_terjadwal_yang_waktunya_tiba(): void
    {
        // Buat tugas terjadwal dengan waktu publikasi di masa lampau (misal: 1 menit yang lalu)
        $tugas = Tugas::create([
            'penempatan_id' => $this->penempatan1->id,
            'judul' => 'Tugas Siap Publikasi Otomatis',
            'deskripsi' => 'Harus terbit otomatis saat scheduler berjalan',
            'kategori' => 'Pengembangan Web',
            'kompleksitas' => 'sedang',
            'deadline' => Carbon::today()->addDays(7),
            'status' => StatusTugasEnum::Belum,
            'status_publikasi' => 'terjadwal',
            'jadwal_publikasi' => now('Asia/Jakarta')->subMinutes(2),
        ]);

        $this->assertEquals('terjadwal', $tugas->status_publikasi);

        // Jalankan Artisan Command scheduler
        $this->artisan('magkom:publikasikan-tugas-terjadwal')
            ->expectsOutputToContain('1 tugas terjadwal berhasil dipublikasikan.')
            ->assertExitCode(0);

        // Verifikasi status berubah menjadi dipublikasikan
        $tugas->refresh();
        $this->assertEquals('dipublikasikan', $tugas->status_publikasi);
        $this->assertTrue($tugas->isDipublikasikan());

        // Sekarang peserta sudah dapat melihat dan mengakses tugas
        $respList = $this->actingAs($this->peserta1)->get(route('peserta.tugas.index'));
        $respList->assertOk();
        $respList->assertSee('Tugas Siap Publikasi Otomatis');

        $respDetail = $this->actingAs($this->peserta1)->get(route('peserta.tugas.show', $tugas));
        $respDetail->assertOk();
    }

    public function test_command_scheduler_idempoten_dan_tidak_mempublikasikan_berulang(): void
    {
        // Jalankan command ketika tidak ada tugas terjadwal
        $this->artisan('magkom:publikasikan-tugas-terjadwal')
            ->expectsOutputToContain('Tidak ada tugas terjadwal yang siap dipublikasikan.')
            ->assertExitCode(0);
    }

    public function test_peserta_yang_tidak_dipilih_dilarang_mengakses_tugas(): void
    {
        $tugas = Tugas::create([
            'penempatan_id' => $this->penempatan1->id,
            'judul' => 'Tugas Khusus Peserta 1',
            'deskripsi' => 'Hanya untuk peserta 1',
            'kategori' => 'Pengembangan Web',
            'kompleksitas' => 'sedang',
            'deadline' => Carbon::today()->addDays(7),
            'status' => StatusTugasEnum::Belum,
            'status_publikasi' => 'dipublikasikan',
        ]);

        // Peserta 2 mencoba mengakses tugas milik Peserta 1: ditolak 403
        $resp = $this->actingAs($this->peserta2)->get(route('peserta.tugas.show', $tugas));
        $resp->assertForbidden();

        // Peserta asing mencoba mengakses: ditolak 403
        $respAsing = $this->actingAs($this->pesertaLain)->get(route('peserta.tugas.show', $tugas));
        $respAsing->assertForbidden();
    }
}
