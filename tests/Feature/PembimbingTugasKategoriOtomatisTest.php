<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JalurEnum;
use App\Enums\JenjangEnum;
use App\Enums\KompleksitasEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusPenempatanEnum;
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
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PembimbingTugasKategoriOtomatisTest extends TestCase
{
    use RefreshDatabase;

    protected User $pembimbing;

    protected User $peserta;

    protected Bidang $bidang;

    protected Penempatan $penempatan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bidang = Bidang::create([
            'nama_bidang' => 'Aplikasi Informatika',
            'aktif' => true,
        ]);

        $this->pembimbing = new User;
        $this->pembimbing->nama = 'Pembimbing ASN Magang';
        $this->pembimbing->email = 'pembimbing@slemankab.go.id';
        $this->pembimbing->password = Hash::make('Password123!');
        $this->pembimbing->role = RoleEnum::Pembimbing;
        $this->pembimbing->bidang_id = $this->bidang->id;
        $this->pembimbing->status_aktif = true;
        $this->pembimbing->email_verified_at = now();
        $this->pembimbing->must_change_password = false;
        $this->pembimbing->save();

        $this->peserta = new User;
        $this->peserta->nama = 'Peserta Magang Terdaftar';
        $this->peserta->email = 'peserta@gmail.com';
        $this->peserta->password = Hash::make('Password123!');
        $this->peserta->role = RoleEnum::Peserta;
        $this->peserta->status_aktif = true;
        $this->peserta->email_verified_at = now();
        $this->peserta->must_change_password = false;
        $this->peserta->save();

        $jurusan = Jurusan::create([
            'nama_jurusan' => 'Teknik Informatika',
            'kategori' => 'mahasiswa',
            'aktif' => true,
        ]);

        PesertaProfil::create([
            'user_id' => $this->peserta->id,
            'nim_nisn' => '12345678',
            'institusi' => 'Universitas Gadjah Mada',
            'jurusan_id' => $jurusan->id,
            'jenjang' => JenjangEnum::S1,
            'no_hp' => '081234567890',
            'alamat' => 'Sleman, DIY',
        ]);

        $bulan = BulanMagang::create([
            'bulan_mulai' => now()->startOfMonth(),
            'tgl_buka' => now()->subDays(5)->toDateString(),
            'tgl_tutup' => now()->addDays(20)->toDateString(),
            'aktif' => true,
        ]);

        $this->penempatan = Penempatan::create([
            'peserta_id' => $this->peserta->id,
            'pembimbing_id' => $this->pembimbing->id,
            'bidang_id' => $this->bidang->id,
            'bulan_magang_id' => $bulan->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'tgl_mulai' => Carbon::today()->subDays(10),
            'tgl_selesai' => Carbon::today()->addDays(20),
            'status' => StatusPenempatanEnum::Aktif,
        ]);
    }

    public function test_halaman_create_dan_edit_tugas_tidak_memuat_dropdown_kategori_bidang(): void
    {
        $responseCreate = $this->actingAs($this->pembimbing)->get(route('pembimbing.tugas.create'));
        $responseCreate->assertOk();
        $responseCreate->assertDontSee('id="groupKategoriSelect"', false);
        $responseCreate->assertDontSee('name="kategori"', false);
        $responseCreate->assertDontSee('Pilih kategori bidang', false);

        $tugas = Tugas::create([
            'batch_id' => 'BATCH-TEST-01',
            'penempatan_id' => $this->penempatan->id,
            'judul' => 'Tugas Awal Magang',
            'deskripsi' => '<p>Deskripsi tugas awal</p>',
            'kategori' => 'Aplikasi Informatika',
            'kompleksitas' => KompleksitasEnum::Sedang,
            'deadline' => Carbon::parse('2026-12-25'),
            'status' => 'belum',
            'status_publikasi' => 'terbit',
        ]);

        $responseEdit = $this->actingAs($this->pembimbing)->get(route('pembimbing.tugas.edit', $tugas));
        $responseEdit->assertOk();
        $responseEdit->assertDontSee('id="groupKategoriSelect"', false);
        $responseEdit->assertDontSee('name="kategori"', false);
    }

    public function test_pembimbing_bisa_membuat_tugas_tanpa_input_kategori_dan_otomatis_mewarisi_bidang_peserta(): void
    {
        $payload = [
            'penempatan_ids' => [$this->penempatan->id],
            'judul' => 'Pembuatan Modul Integrasi API Sleman',
            'deskripsi' => '<p>Silakan buat endpoint integrasi API dengan dokumentasi OpenAPI.</p>',
            'kompleksitas' => KompleksitasEnum::Tinggi->value,
            'deadline' => Carbon::today()->addDays(5)->format('Y-m-d'),
            'aksi_publikasi' => 'sekarang',
        ];

        $response = $this->actingAs($this->pembimbing)->post(route('pembimbing.tugas.store'), $payload);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('pembimbing.tugas.index'));

        $this->assertDatabaseHas('tugas', [
            'judul' => 'Pembuatan Modul Integrasi API Sleman',
            'penempatan_id' => $this->penempatan->id,
            'kategori' => 'Aplikasi Informatika',
        ]);
    }

    public function test_pembimbing_bisa_mengupdate_tugas_tanpa_input_kategori(): void
    {
        $tugas = Tugas::create([
            'batch_id' => 'BATCH-TEST-UPDATE',
            'penempatan_id' => $this->penempatan->id,
            'judul' => 'Tugas Revisi Modul',
            'deskripsi' => '<p>Deskripsi awal</p>',
            'kategori' => 'Aplikasi Informatika',
            'kompleksitas' => KompleksitasEnum::Rendah,
            'deadline' => Carbon::today()->addDays(5),
            'status' => 'proses',
            'status_publikasi' => 'terbit',
        ]);

        $updatePayload = [
            'penempatan_id' => $this->penempatan->id,
            'judul' => 'Tugas Revisi Modul (Updated)',
            'deskripsi' => '<p>Deskripsi baru yang telah diperbarui.</p>',
            'kompleksitas' => KompleksitasEnum::Sedang->value,
            'deadline' => Carbon::today()->addDays(7)->format('Y-m-d'),
        ];

        $response = $this->actingAs($this->pembimbing)
            ->from(route('pembimbing.tugas.edit', $tugas))
            ->put(route('pembimbing.tugas.update', $tugas), $updatePayload);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('pembimbing.tugas.edit', $tugas));

        $this->assertDatabaseHas('tugas', [
            'id' => $tugas->id,
            'judul' => 'Tugas Revisi Modul (Updated)',
            'kategori' => 'Aplikasi Informatika',
        ]);
    }
}
