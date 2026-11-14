<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JalurEnum;
use App\Enums\KompleksitasEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\StatusReviewProgresEnum;
use App\Enums\StatusTugasEnum;
use App\Enums\SumberPenempatanEnum;
use App\Models\Bidang;
use App\Models\Penempatan;
use App\Models\ProgresLog;
use App\Models\Tugas;
use App\Models\User;
use Database\Seeders\JenisDokumenSeeder;
use Database\Seeders\RubrikSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PesertaTugasClassroomUploadTest extends TestCase
{
    use RefreshDatabase;

    protected User $peserta;

    protected User $otherPeserta;

    protected User $pembimbing;

    protected Bidang $bidang;

    protected Penempatan $penempatan;

    protected Tugas $tugas;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(JenisDokumenSeeder::class);
        $this->seed(RubrikSeeder::class);
        Storage::fake('private');

        $this->bidang = Bidang::create([
            'nama_bidang' => 'Bidang Aplikasi Informatika (Aptika)',
            'deskripsi' => 'Pengembangan aplikasi kedinasan',
            'aktif' => true,
        ]);

        $this->pembimbing = new User([
            'nama' => 'Budi Santoso, S.Kom',
            'email' => 'budi.santoso@magkom.test',
            'password' => bcrypt('Password123!'),
            'status_aktif' => true,
            'must_change_password' => false,
        ]);
        $this->pembimbing->role = RoleEnum::Pembimbing;
        $this->pembimbing->email_verified_at = now();
        $this->pembimbing->save();

        $this->peserta = new User([
            'nama' => 'Naufal Pratama',
            'email' => 'naufal.peserta@magkom.test',
            'password' => bcrypt('Password123!'),
            'status_aktif' => true,
            'must_change_password' => false,
        ]);
        $this->peserta->role = RoleEnum::Peserta;
        $this->peserta->email_verified_at = now();
        $this->peserta->save();

        $this->otherPeserta = new User([
            'nama' => 'Peserta Lain',
            'email' => 'lain.peserta@magkom.test',
            'password' => bcrypt('Password123!'),
            'status_aktif' => true,
            'must_change_password' => false,
        ]);
        $this->otherPeserta->role = RoleEnum::Peserta;
        $this->otherPeserta->email_verified_at = now();
        $this->otherPeserta->save();

        $this->penempatan = Penempatan::create([
            'peserta_id' => $this->peserta->id,
            'bidang_id' => $this->bidang->id,
            'pembimbing_id' => $this->pembimbing->id,
            'tgl_mulai' => now()->subDays(10)->toDateString(),
            'tgl_selesai' => now()->addDays(80)->toDateString(),
            'status' => StatusPenempatanEnum::Aktif,
            'sumber' => SumberPenempatanEnum::Online,
            'jalur' => JalurEnum::Umum,
        ]);

        $this->tugas = Tugas::create([
            'penempatan_id' => $this->penempatan->id,
            'judul' => 'Penyusunan Draf Laporan Akhir Magang & Portofolio',
            'deskripsi' => 'Menyusun laporan akhir PKL/Magang Bab 1 s/d Bab 4.',
            'kategori' => 'Laporan Akhir',
            'kompleksitas' => KompleksitasEnum::Sedang,
            'deadline' => now()->addDays(7),
            'status' => StatusTugasEnum::Belum,
        ]);
    }

    public function test_peserta_can_view_tugas_detail_with_classroom_components(): void
    {
        $response = $this->actingAs($this->peserta)->get(route('peserta.tugas.show', $this->tugas));

        $response->assertOk();
        $response->assertSee('Tugas Anda');
        $response->assertSee('+ Tambah berkas');
        $response->assertSee('menuTambahBerkas');
        $response->assertSee('data-action="open-modal-link"', false);
        $response->assertSee('data-action="open-modal-file"', false);
        $response->assertSee('modalTambahLink');
        $response->assertSee('modalSisipkanBerkas');
        $response->assertSee('tabBtnUpload');
        $response->assertSee('tabBtnRecent');
        $response->assertDontSee('Komentar pribadi');
        $response->assertSee('Pastikan berkas atau tautan sudah benar sebelum diserahkan');
    }

    public function test_peserta_can_submit_tugas_with_uploaded_file(): void
    {
        $file = UploadedFile::fake()->create('draf-laporan.pdf', 800, 'application/pdf');

        $response = $this->actingAs($this->peserta)->post(route('peserta.tugas.progres', $this->tugas), [
            'file' => $file,
            'catatan' => 'Draf bab 1-3 sudah selesai.',
        ]);

        $response->assertRedirect(route('peserta.tugas.show', $this->tugas));
        $response->assertSessionHas('success');

        $this->tugas->refresh();
        $this->assertEquals(StatusTugasEnum::Proses, $this->tugas->status);

        $progres = ProgresLog::where('tugas_id', $this->tugas->id)->latest()->first();
        $this->assertNotNull($progres);
        $this->assertNotNull($progres->file);
        Storage::disk('private')->assertExists($progres->file);
    }

    public function test_peserta_can_submit_tugas_with_link(): void
    {
        $response = $this->actingAs($this->peserta)->post(route('peserta.tugas.progres', $this->tugas), [
            'link' => 'https://github.com/magkom/portofolio-magang',
            'catatan' => 'Tautan repositori hasil kerja akhir.',
        ]);

        $response->assertRedirect(route('peserta.tugas.show', $this->tugas));
        $response->assertSessionHas('success');

        $progres = ProgresLog::where('tugas_id', $this->tugas->id)->latest()->first();
        $this->assertNotNull($progres);
        $this->assertEquals('https://github.com/magkom/portofolio-magang', $progres->link);
        $this->assertNull($progres->file);
    }

    public function test_peserta_can_submit_tugas_with_recent_file_reference(): void
    {
        // 1. Buat berkas lama milik peserta di storage private
        $oldFilePath = "progres/{$this->tugas->id}/old-report.pdf";
        Storage::disk('private')->put($oldFilePath, 'sample-pdf-content');

        ProgresLog::create([
            'tugas_id' => $this->tugas->id,
            'catatan' => 'Laporan awal',
            'file' => $oldFilePath,
            'tgl_lapor' => now()->subDays(2),
        ]);

        // 2. Buat tugas kedua
        $tugas2 = Tugas::create([
            'penempatan_id' => $this->penempatan->id,
            'judul' => 'Revisi Laporan Akhir',
            'deskripsi' => 'Perbaiki lampiran',
            'kategori' => 'Laporan',
            'kompleksitas' => KompleksitasEnum::Sedang,
            'deadline' => now()->addDays(5),
            'status' => StatusTugasEnum::Belum,
        ]);

        // 3. Submit tugas kedua menggunakan recent_file_path dari berkas sebelumnya
        $response = $this->actingAs($this->peserta)->post(route('peserta.tugas.progres', $tugas2), [
            'recent_file_path' => $oldFilePath,
            'catatan' => 'Menggunakan berkas draf laporan yang sudah pernah diunggah.',
        ]);

        $response->assertRedirect(route('peserta.tugas.show', $tugas2));

        $newProgres = ProgresLog::where('tugas_id', $tugas2->id)->latest()->first();
        $this->assertNotNull($newProgres);
        $this->assertNotNull($newProgres->file);
        $this->assertNotEquals($oldFilePath, $newProgres->file); // Harus disalin aman ke folder tugas baru
        Storage::disk('private')->assertExists($newProgres->file);
    }

    public function test_anti_idor_prevents_submitting_other_user_recent_file(): void
    {
        // 1. Penempatan dan tugas milik peserta lain
        $otherPenempatan = Penempatan::create([
            'peserta_id' => $this->otherPeserta->id,
            'bidang_id' => $this->bidang->id,
            'pembimbing_id' => $this->pembimbing->id,
            'tgl_mulai' => now()->subDays(10)->toDateString(),
            'tgl_selesai' => now()->addDays(80)->toDateString(),
            'status' => StatusPenempatanEnum::Aktif,
            'sumber' => SumberPenempatanEnum::Online,
            'jalur' => JalurEnum::Umum,
        ]);

        $otherTugas = Tugas::create([
            'penempatan_id' => $otherPenempatan->id,
            'judul' => 'Tugas Rahasia Peserta Lain',
            'deskripsi' => 'File rahasia',
            'kategori' => 'Rahasia',
            'kompleksitas' => KompleksitasEnum::Tinggi,
            'deadline' => now()->addDays(5),
            'status' => StatusTugasEnum::Belum,
        ]);

        $secretFilePath = "progres/{$otherTugas->id}/secret-other-user.pdf";
        Storage::disk('private')->put($secretFilePath, 'secret-data');

        ProgresLog::create([
            'tugas_id' => $otherTugas->id,
            'catatan' => 'Berkas rahasia peserta lain',
            'file' => $secretFilePath,
            'tgl_lapor' => now()->subDay(),
        ]);

        // 2. Peserta pertama mencoba menyusupkan secretFilePath milik orang lain
        $response = $this->actingAs($this->peserta)->post(route('peserta.tugas.progres', $this->tugas), [
            'recent_file_path' => $secretFilePath,
            'catatan' => 'Mencoba ambil file orang lain.',
        ]);

        $response->assertRedirect(route('peserta.tugas.show', $this->tugas));

        $progres = ProgresLog::where('tugas_id', $this->tugas->id)->latest()->first();
        $this->assertNotNull($progres);
        // Anti-IDOR: file harus tetap null karena berkas tersebut bukan milik peserta yang login
        $this->assertNull($progres->file);
    }

    public function test_validation_requires_at_least_file_or_link_or_recent(): void
    {
        $response = $this->actingAs($this->peserta)->post(route('peserta.tugas.progres', $this->tugas), []);

        $response->assertSessionHasErrors(['catatan']);
    }

    public function test_peserta_tidak_dapat_mengumpulkan_tugas_dua_kali_jika_sedang_menunggu_review(): void
    {
        // Pengumpulan pertama
        $this->actingAs($this->peserta)->post(route('peserta.tugas.progres', $this->tugas), [
            'link' => 'https://github.com/magkom/submission-1',
            'catatan' => 'Pengumpulan draf pertama.',
        ]);

        // Coba pengumpulan kedua sebelum direview pembimbing
        $response = $this->actingAs($this->peserta)->post(route('peserta.tugas.progres', $this->tugas), [
            'link' => 'https://github.com/magkom/submission-2',
            'catatan' => 'Mencoba submit kedua kali.',
        ]);

        $response->assertSessionHas('error');

        // Pastikan di halaman detail tombol kumpul terkunci dan muncul banner diserahkan
        $showResponse = $this->actingAs($this->peserta)->get(route('peserta.tugas.show', $this->tugas));
        $showResponse->assertOk();
        $showResponse->assertSee('Tugas Telah Diserahkan');
        $showResponse->assertDontSee('+ Tambah berkas');
    }

    public function test_peserta_dapat_mengumpulkan_ulang_jika_berstatus_perlu_revisi(): void
    {
        // Buat progres pertama dengan status perlu revisi
        ProgresLog::create([
            'tugas_id' => $this->tugas->id,
            'catatan' => 'Draf pertama perlu revisi',
            'link' => 'https://github.com/magkom/submission-1',
            'status_review' => StatusReviewProgresEnum::PerluRevisi,
            'feedback' => 'Perbaiki link repository ke branch main.',
            'tgl_lapor' => now(),
        ]);

        // Peserta membuka halaman detail
        $showResponse = $this->actingAs($this->peserta)->get(route('peserta.tugas.show', $this->tugas));
        $showResponse->assertOk();
        $showResponse->assertSee('Perlu Revisi');
        $showResponse->assertSee('Perbaiki link repository ke branch main.');
        $showResponse->assertSee('+ Tambah berkas');
        $showResponse->assertSee('Serahkan Ulang Revisi');

        // Peserta berhasil menyerahkan ulang
        $response = $this->actingAs($this->peserta)->post(route('peserta.tugas.progres', $this->tugas), [
            'link' => 'https://github.com/magkom/submission-revised',
            'catatan' => 'Sudah diperbaiki ke branch main.',
        ]);

        $response->assertRedirect(route('peserta.tugas.show', $this->tugas));
    }

    public function test_peserta_tetap_dapat_mengumpulkan_tugas_yang_melewati_tenggat_dan_berstatus_diserahkan_terlambat(): void
    {
        // 1. Buat tugas yang sudah lewat tenggat
        $tugasTerlambat = Tugas::create([
            'penempatan_id' => $this->penempatan->id,
            'judul' => 'Tugas yang Sudah Melewati Tenggat',
            'deskripsi' => 'Pengumpulan terlambat harus tetap diizinkan.',
            'kategori' => 'Tugas Tambahan',
            'kompleksitas' => KompleksitasEnum::Sedang,
            'deadline' => now()->subDays(2),
            'status' => StatusTugasEnum::Belum,
        ]);

        // 2. Peserta melihat detail tugas terlambat: tombol tambah berkas tetap ada dan muncul info terlambat
        $showResponse = $this->actingAs($this->peserta)->get(route('peserta.tugas.show', $tugasTerlambat));
        $showResponse->assertOk();
        $showResponse->assertSee('Terlambat');
        $showResponse->assertSee('+ Tambah berkas');
        $showResponse->assertSee('Tenggat waktu telah berakhir. Tugas tetap dapat diserahkan dan akan dicatat sebagai diserahkan terlambat.');

        // 3. Peserta menyerahkan tugas yang terlambat
        $file = UploadedFile::fake()->create('laporan-susulan.pdf', 500, 'application/pdf');
        $submitResponse = $this->actingAs($this->peserta)->post(route('peserta.tugas.progres', $tugasTerlambat), [
            'file' => $file,
            'catatan' => 'Mohon maaf mengumpulkan terlambat.',
        ]);

        $submitResponse->assertRedirect(route('peserta.tugas.show', $tugasTerlambat));
        $submitResponse->assertSessionHas('success');

        // 4. Periksa halaman detail setelah diserahkan: status berubah menjadi "Diserahkan terlambat" dan box berstatus terlambat
        $afterResponse = $this->actingAs($this->peserta)->get(route('peserta.tugas.show', $tugasTerlambat));
        $afterResponse->assertOk();
        $afterResponse->assertSee('Diserahkan terlambat');
        $afterResponse->assertSee('Tugas Telah Diserahkan (Terlambat)');
        $afterResponse->assertSee('melewati tenggat');
        $afterResponse->assertSee('Batalkan pengiriman');
    }
}
