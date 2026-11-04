<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JalurEnum;
use App\Enums\JenjangEnum;
use App\Enums\KompleksitasEnum;
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
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PembimbingClassroomMenteeFirstTugasIndexTest extends TestCase
{
    use RefreshDatabase;

    protected User $pembimbing;

    protected User $peserta1;

    protected User $peserta2;

    protected Penempatan $penempatan1;

    protected Penempatan $penempatan2;

    protected Bidang $bidang;

    protected Jurusan $jurusan;

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

        $this->bidang = Bidang::create([
            'nama_bidang' => 'Aplikasi Informatika & E-Government',
            'deskripsi' => 'Bidang Aptika',
            'is_active' => true,
        ]);

        $this->jurusan = Jurusan::create([
            'nama_jurusan' => 'Teknik Informatika',
            'kategori' => 'mahasiswa',
            'aktif' => true,
        ]);

        $this->pembimbing = $this->createUser(
            'Budi Santoso, S.Kom',
            'pembimbing@slemankab.go.id',
            RoleEnum::Pembimbing,
            $this->bidang->id
        );

        $this->peserta1 = $this->createUser(
            'Naufal Pratama',
            'naufal@example.com',
            RoleEnum::Peserta
        );

        PesertaProfil::create([
            'user_id' => $this->peserta1->id,
            'institusi' => 'SMK Negeri 2 Depok Sleman',
            'nim_nisn' => '12345678',
            'jurusan_id' => $this->jurusan->id,
            'jenjang' => JenjangEnum::Smk,
            'no_hp' => '081234567891',
            'tanggal_mulai' => Carbon::today()->subDays(10),
            'tanggal_selesai' => Carbon::today()->addDays(50),
        ]);

        $this->peserta2 = $this->createUser(
            'Dimas Pratama',
            'dimas@example.com',
            RoleEnum::Peserta
        );

        PesertaProfil::create([
            'user_id' => $this->peserta2->id,
            'institusi' => 'Universitas Gadjah Mada',
            'nim_nisn' => '87654321',
            'jurusan_id' => $this->jurusan->id,
            'jenjang' => JenjangEnum::S1,
            'no_hp' => '081234567892',
            'tanggal_mulai' => Carbon::today()->subDays(10),
            'tanggal_selesai' => Carbon::today()->addDays(50),
        ]);

        $bulanMagang = BulanMagang::create([
            'bulan_mulai' => now()->startOfMonth(),
            'tgl_buka' => now()->subDays(5)->toDateString(),
            'tgl_tutup' => now()->addDays(20)->toDateString(),
            'aktif' => true,
        ]);

        $this->penempatan1 = Penempatan::create([
            'peserta_id' => $this->peserta1->id,
            'pembimbing_id' => $this->pembimbing->id,
            'bidang_id' => $this->bidang->id,
            'bulan_magang_id' => $bulanMagang->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'tgl_mulai' => Carbon::today()->subDays(10),
            'tgl_selesai' => Carbon::today()->addDays(30),
            'status' => StatusPenempatanEnum::Aktif,
        ]);

        $this->penempatan2 = Penempatan::create([
            'peserta_id' => $this->peserta2->id,
            'pembimbing_id' => $this->pembimbing->id,
            'bidang_id' => $this->bidang->id,
            'bulan_magang_id' => $bulanMagang->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'tgl_mulai' => Carbon::today()->subDays(10),
            'tgl_selesai' => Carbon::today()->addDays(30),
            'status' => StatusPenempatanEnum::Aktif,
        ]);

        // Tugas untuk Peserta 1
        Tugas::create([
            'penempatan_id' => $this->penempatan1->id,
            'pembimbing_id' => $this->pembimbing->id,
            'judul' => 'Pembuatan Wireframe UI Portal Magang',
            'deskripsi' => 'Desain arsitektur wireframe halaman antarmuka.',
            'kategori' => 'Desain & UI',
            'kompleksitas' => KompleksitasEnum::Sedang,
            'status' => StatusTugasEnum::Belum,
            'deadline' => Carbon::today()->addDays(5),
            'waktu_mulai' => Carbon::now(),
        ]);

        // Tugas untuk Peserta 2
        Tugas::create([
            'penempatan_id' => $this->penempatan2->id,
            'pembimbing_id' => $this->pembimbing->id,
            'judul' => 'Implementasi API Autentikasi',
            'deskripsi' => 'Setup controller dan unit test auth.',
            'kategori' => 'Backend API',
            'kompleksitas' => KompleksitasEnum::Tinggi,
            'status' => StatusTugasEnum::Selesai,
            'deadline' => Carbon::today()->addDays(7),
            'waktu_mulai' => Carbon::now(),
        ]);
    }

    public function test_pembimbing_dapat_melihat_daftar_kartu_nama_mentee_gaya_google_classroom(): void
    {
        $response = $this->actingAs($this->pembimbing)->get(route('pembimbing.tugas.index'));

        $response->assertOk();
        // Memastikan container stream kartu mentee tersedia
        $response->assertSee('menteeStreamContainer');
        $response->assertSee('mentee-classroom-card');

        // Memastikan nama mentee muncul sebagai kartu utama
        $response->assertSee('Naufal Pratama');
        $response->assertSee('SMK Negeri 2 Depok Sleman');
        $response->assertSee('Dimas Pratama');
        $response->assertSee('Universitas Gadjah Mada');

        // Memastikan drawer tugas mentee ada di dalam DOM
        $response->assertSee('tasks-mentee-'.$this->penempatan1->id);
        $response->assertSee('Pembuatan Wireframe UI Portal Magang');
        $response->assertSee('tasks-mentee-'.$this->penempatan2->id);
        $response->assertSee('Implementasi API Autentikasi');

        // Memastikan filter toolbar lama telah dihapus
        $response->assertDontSee('task-filter-toolbar');
        $response->assertDontSee('Cari judul tugas, topik, atau nama mentee...');
    }

    public function test_filter_tab_status_menampilkan_tugas_yang_sesuai_di_drawer_mentee(): void
    {
        $response = $this->actingAs($this->pembimbing)->get(route('pembimbing.tugas.index', ['status' => 'selesai']));

        $response->assertOk();
        // Tugas selesai pada peserta 2 harus muncul
        $response->assertSee('Implementasi API Autentikasi');

        // Peserta 1 tidak punya tugas selesai pada filter ini, harus menampilkan empty state per mentee
        $response->assertSee('Belum ada tugas untuk peserta ini pada filter ini');
    }
}
