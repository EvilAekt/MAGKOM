<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JalurEnum;
use App\Enums\KompleksitasEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusLogbookEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\StatusTugasEnum;
use App\Enums\SumberPenempatanEnum;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\Kuota;
use App\Models\Logbook;
use App\Models\Penempatan;
use App\Models\Tugas;
use App\Models\User;
use Database\Seeders\JenisDokumenSeeder;
use Database\Seeders\RubrikSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PembimbingIdorProtectionTest extends TestCase
{
    use RefreshDatabase;

    protected User $pembimbing1;

    protected User $pembimbing2;

    protected User $superAdmin;

    protected User $peserta1;

    protected User $peserta2;

    protected Penempatan $penempatan1;

    protected Penempatan $penempatan2;

    protected Tugas $tugas1;

    protected Tugas $tugas2;

    protected Logbook $logbook2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(JenisDokumenSeeder::class);
        $this->seed(RubrikSeeder::class);

        $this->pembimbing1 = new User;
        $this->pembimbing1->nama = 'Pembimbing Alpha';
        $this->pembimbing1->email = 'pembimbing.alpha@slemankab.go.id';
        $this->pembimbing1->password = 'Password123!';
        $this->pembimbing1->role = RoleEnum::Pembimbing;
        $this->pembimbing1->status_aktif = true;
        $this->pembimbing1->email_verified_at = now();
        $this->pembimbing1->save();

        $this->pembimbing2 = new User;
        $this->pembimbing2->nama = 'Pembimbing Beta';
        $this->pembimbing2->email = 'pembimbing.beta@slemankab.go.id';
        $this->pembimbing2->password = 'Password123!';
        $this->pembimbing2->role = RoleEnum::Pembimbing;
        $this->pembimbing2->status_aktif = true;
        $this->pembimbing2->email_verified_at = now();
        $this->pembimbing2->save();

        $this->superAdmin = new User;
        $this->superAdmin->nama = 'Super Admin';
        $this->superAdmin->email = 'superadmin@slemankab.go.id';
        $this->superAdmin->password = 'Password123!';
        $this->superAdmin->role = RoleEnum::SuperAdmin;
        $this->superAdmin->status_aktif = true;
        $this->superAdmin->email_verified_at = now();
        $this->superAdmin->save();

        $this->peserta1 = new User;
        $this->peserta1->nama = 'Peserta Alpha';
        $this->peserta1->email = 'peserta.alpha@example.com';
        $this->peserta1->password = 'Password123!';
        $this->peserta1->role = RoleEnum::Peserta;
        $this->peserta1->status_aktif = true;
        $this->peserta1->email_verified_at = now();
        $this->peserta1->save();

        $this->peserta2 = new User;
        $this->peserta2->nama = 'Peserta Beta';
        $this->peserta2->email = 'peserta.beta@example.com';
        $this->peserta2->password = 'Password123!';
        $this->peserta2->role = RoleEnum::Peserta;
        $this->peserta2->status_aktif = true;
        $this->peserta2->email_verified_at = now();
        $this->peserta2->save();

        $jurusan = Jurusan::create([
            'nama_jurusan' => 'Informatika',
            'kategori' => 'mahasiswa',
            'aktif' => true,
        ]);

        $bulan = BulanMagang::create([
            'bulan_mulai' => now()->startOfMonth(),
            'tgl_buka' => now()->subDays(10)->toDateString(),
            'tgl_tutup' => now()->addDays(20)->toDateString(),
            'aktif' => true,
        ]);

        $kuota = Kuota::create([
            'bulan_magang_id' => $bulan->id,
            'jurusan_id' => $jurusan->id,
            'jumlah' => 10,
            'terisi' => 2,
            'aktif' => true,
        ]);

        $this->penempatan1 = Penempatan::create([
            'peserta_id' => $this->peserta1->id,
            'pembimbing_id' => $this->pembimbing1->id,
            'kuota_id' => $kuota->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'tgl_mulai' => now()->toDateString(),
            'tgl_selesai' => now()->addDays(90)->toDateString(),
            'status' => StatusPenempatanEnum::Aktif,
        ]);

        $this->penempatan2 = Penempatan::create([
            'peserta_id' => $this->peserta2->id,
            'pembimbing_id' => $this->pembimbing2->id,
            'kuota_id' => $kuota->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'tgl_mulai' => now()->toDateString(),
            'tgl_selesai' => now()->addDays(90)->toDateString(),
            'status' => StatusPenempatanEnum::Aktif,
        ]);

        $this->tugas1 = Tugas::create([
            'penempatan_id' => $this->penempatan1->id,
            'judul' => 'Tugas Khusus Alpha',
            'deskripsi' => 'Deskripsi tugas alpha',
            'deadline' => now()->addDays(14)->toDateString(),
            'kategori' => 'Teknis',
            'kompleksitas' => KompleksitasEnum::Sedang,
            'status' => StatusTugasEnum::Belum,
        ]);

        $this->tugas2 = Tugas::create([
            'penempatan_id' => $this->penempatan2->id,
            'judul' => 'Tugas Khusus Beta',
            'deskripsi' => 'Deskripsi tugas beta',
            'deadline' => now()->addDays(14)->toDateString(),
            'kategori' => 'Teknis',
            'kompleksitas' => KompleksitasEnum::Sedang,
            'status' => StatusTugasEnum::Belum,
        ]);

        $this->logbook2 = Logbook::create([
            'penempatan_id' => $this->penempatan2->id,
            'minggu_ke' => 1,
            'tanggal' => now()->toDateString(),
            'kegiatan' => 'Mengerjakan modul sistem bimbingan beta',
            'status' => StatusLogbookEnum::Terkirim,
        ]);
    }

    public function test_pembimbing_dilarang_melihat_detail_tugas_mentee_pembimbing_lain(): void
    {
        // Pembimbing 1 mengakses tugas 2 (milik mentee pembimbing 2)
        $response = $this->actingAs($this->pembimbing1)->get(route('pembimbing.tugas.show', $this->tugas2));
        $response->assertStatus(403);
    }

    public function test_pembimbing_dilarang_mengupdate_tugas_mentee_pembimbing_lain(): void
    {
        $response = $this->actingAs($this->pembimbing1)->put(route('pembimbing.tugas.update', $this->tugas2), [
            'penempatan_id' => $this->penempatan2->id,
            'judul' => 'Perubahan Ilegal Oleh Pembimbing Lain',
            'deskripsi' => 'Deskripsi baru',
            'deadline' => now()->addDays(20)->toDateString(),
        ]);
        $response->assertStatus(403);
    }

    public function test_pembimbing_dilarang_mereview_logbook_mentee_pembimbing_lain(): void
    {
        $response = $this->actingAs($this->pembimbing1)->post(route('pembimbing.logbook.review', $this->logbook2), [
            'status' => 'disetujui',
            'catatan' => 'Persetujuan ilegal',
        ]);
        $response->assertStatus(403);
    }

    public function test_pembimbing_dilarang_mengisi_penilaian_mentee_pembimbing_lain(): void
    {
        $response = $this->actingAs($this->pembimbing1)->post(route('pembimbing.penilaian.store', $this->penempatan2), [
            'nilai' => [1 => 85, 2 => 90],
            'status' => 'draf',
        ]);
        $response->assertStatus(403);
    }

    public function test_superadmin_read_only_dilarang_membuat_tugas_atau_mereview(): void
    {
        // Superadmin mencoba membuat tugas untuk penempatan 1
        $response = $this->actingAs($this->superAdmin)->post(route('pembimbing.tugas.store'), [
            'penempatan_id' => $this->penempatan1->id,
            'judul' => 'Tugas Dibuat SuperAdmin',
            'deskripsi' => 'Deskripsi tugas superadmin',
            'deadline' => now()->addDays(7)->toDateString(),
        ]);
        $response->assertStatus(403);

        // Superadmin mencoba review logbook
        $responseLogbook = $this->actingAs($this->superAdmin)->post(route('pembimbing.logbook.review', $this->logbook2), [
            'status' => 'disetujui',
        ]);
        $responseLogbook->assertStatus(403);
    }

    public function test_pembimbing_bisa_mengakses_form_edit_dan_mengupdate_tugas_belum_selesai(): void
    {
        $responseGet = $this->actingAs($this->pembimbing1)->get(route('pembimbing.tugas.edit', $this->tugas1));
        $responseGet->assertOk();
        $responseGet->assertViewIs('pembimbing.tugas.edit');
        $responseGet->assertSee('Edit Tugas Mentee');
        $responseGet->assertSee($this->tugas1->judul);

        $responsePut = $this->actingAs($this->pembimbing1)->put(route('pembimbing.tugas.update', $this->tugas1), [
            'judul' => 'Tugas Khusus Alpha Diperbarui',
            'deskripsi' => 'Deskripsi tugas alpha setelah pembaruan',
            'kategori' => 'Teknis',
            'kompleksitas' => 'tinggi',
            'deadline' => now()->addDays(20)->toDateString(),
        ]);
        $responsePut->assertSessionHasNoErrors();

        $this->tugas1->refresh();
        $this->assertSame('Tugas Khusus Alpha Diperbarui', $this->tugas1->judul);
        $this->assertSame('tinggi', $this->tugas1->kompleksitas->value);
    }

    public function test_pembimbing_dilarang_mengedit_tugas_yang_sudah_selesai(): void
    {
        $this->tugas1->update(['status' => StatusTugasEnum::Selesai]);

        $responseGet = $this->actingAs($this->pembimbing1)->get(route('pembimbing.tugas.edit', $this->tugas1));
        $responseGet->assertRedirect(route('pembimbing.tugas.show', $this->tugas1));
        $responseGet->assertSessionHasErrors(['error']);

        $responsePut = $this->actingAs($this->pembimbing1)->put(route('pembimbing.tugas.update', $this->tugas1), [
            'judul' => 'Coba Ubah Tugas Selesai',
            'kategori' => 'Teknis',
            'kompleksitas' => 'sedang',
            'deadline' => now()->addDays(5)->toDateString(),
        ]);
        $responsePut->assertSessionHasErrors(['error']);
    }

    public function test_pembimbing_dilarang_mengakses_form_edit_tugas_milik_pembimbing_lain(): void
    {
        $response = $this->actingAs($this->pembimbing1)->get(route('pembimbing.tugas.edit', $this->tugas2));
        $response->assertStatus(403);
    }
}
