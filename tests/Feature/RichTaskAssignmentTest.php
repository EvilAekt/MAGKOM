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

class RichTaskAssignmentTest extends TestCase
{
    use RefreshDatabase;

    protected User $pembimbing;

    protected User $peserta;

    protected User $pesertaLain;

    protected Penempatan $penempatan;

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

        $bidang = Bidang::create(['nama_bidang' => 'Aplikasi Informatika']);
        $jurusan = Jurusan::create([
            'nama_jurusan' => 'Teknik Informatika',
            'kategori' => 'mahasiswa',
            'aktif' => true,
        ]);
        $bulan = BulanMagang::create([
            'bulan_mulai' => now()->startOfMonth(),
            'tgl_buka' => now()->subDays(5)->toDateString(),
            'tgl_tutup' => now()->addDays(20)->toDateString(),
            'aktif' => true,
        ]);

        $this->pembimbing = $this->createUser(
            'Pembimbing Lapangan Utama',
            'pembimbing@magkom.go.id',
            RoleEnum::Pembimbing,
            $bidang->id
        );

        $this->peserta = $this->createUser(
            'Peserta Magang Satu',
            'peserta@magkom.go.id',
            RoleEnum::Peserta
        );

        PesertaProfil::create([
            'user_id' => $this->peserta->id,
            'jurusan_id' => $jurusan->id,
            'institusi' => 'Universitas Gadjah Mada',
            'jenjang' => JenjangEnum::S1,
            'nim_nisn' => '12345678',
            'no_hp' => '081234567890',
        ]);

        $this->pesertaLain = $this->createUser(
            'Peserta Lain',
            'peserta.lain@magkom.go.id',
            RoleEnum::Peserta
        );

        PesertaProfil::create([
            'user_id' => $this->pesertaLain->id,
            'jurusan_id' => $jurusan->id,
            'institusi' => 'Universitas Negeri Yogyakarta',
            'jenjang' => JenjangEnum::S1,
            'nim_nisn' => '87654321',
            'no_hp' => '089876543210',
        ]);

        $this->penempatan = Penempatan::create([
            'peserta_id' => $this->peserta->id,
            'pembimbing_id' => $this->pembimbing->id,
            'bidang_id' => $bidang->id,
            'bulan_magang_id' => $bulan->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'tgl_mulai' => Carbon::today()->subDays(10),
            'tgl_selesai' => Carbon::today()->addDays(20),
            'status' => StatusPenempatanEnum::Aktif,
        ]);
    }

    public function test_pembimbing_bisa_membuat_tugas_dengan_link_dan_file_lampiran(): void
    {
        $file = UploadedFile::fake()->create('panduan_magkom.pdf', 1024, 'application/pdf');

        $response = $this->actingAs($this->pembimbing)
            ->post(route('pembimbing.tugas.store'), [
                'penempatan_id' => $this->penempatan->id,
                'judul' => 'Pembuatan Modul Rich Task Assignment',
                'deskripsi' => 'Pelajari dokumen panduan terlampir dan ikuti alur pengerjaan.',
                'link_referensi' => 'https://github.com/EvilAekt/MAGKOM',
                'file_lampiran' => $file,
                'kategori' => 'Pengembangan Web',
                'kompleksitas' => 'sedang',
                'deadline' => Carbon::today()->addDays(5)->toDateString(),
            ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $tugas = Tugas::where('judul', 'Pembuatan Modul Rich Task Assignment')->first();
        $this->assertNotNull($tugas);
        $this->assertEquals('https://github.com/EvilAekt/MAGKOM', $tugas->link_referensi);
        $this->assertEquals('panduan_magkom.pdf', $tugas->nama_file_lampiran);
        $this->assertNotNull($tugas->file_lampiran);
        $this->assertEquals('pdf', $tugas->ekstensi_lampiran);
        $this->assertEquals('1.0 MB', $tugas->ukuran_format);

        Storage::disk('private')->assertExists($tugas->file_lampiran);
    }

    public function test_validasi_menolak_file_berbahaya_dan_ukuran_melebihi_10mb(): void
    {
        // 1. Uji file tidak valid (executable script)
        $maliciousFile = UploadedFile::fake()->create('exploit.exe', 100, 'application/x-msdownload');
        $response = $this->actingAs($this->pembimbing)
            ->post(route('pembimbing.tugas.store'), [
                'penempatan_id' => $this->penempatan->id,
                'judul' => 'Tugas Uji Keamanan',
                'file_lampiran' => $maliciousFile,
                'kategori' => 'Security',
                'kompleksitas' => 'tinggi',
                'deadline' => Carbon::today()->addDays(5)->toDateString(),
            ]);

        $response->assertSessionHasErrors('file_lampiran');

        // 2. Uji file melebihi 10 MB (10.241 KB)
        $oversizedFile = UploadedFile::fake()->create('huge_file.pdf', 10500, 'application/pdf');
        $response = $this->actingAs($this->pembimbing)
            ->post(route('pembimbing.tugas.store'), [
                'penempatan_id' => $this->penempatan->id,
                'judul' => 'Tugas File Raksasa',
                'file_lampiran' => $oversizedFile,
                'kategori' => 'Security',
                'kompleksitas' => 'tinggi',
                'deadline' => Carbon::today()->addDays(5)->toDateString(),
            ]);

        $response->assertSessionHasErrors('file_lampiran');

        // 3. Uji URL tidak valid
        $response = $this->actingAs($this->pembimbing)
            ->post(route('pembimbing.tugas.store'), [
                'penempatan_id' => $this->penempatan->id,
                'judul' => 'Tugas Link Rusak',
                'link_referensi' => 'bukan-url-valid',
                'kategori' => 'Testing',
                'kompleksitas' => 'rendah',
                'deadline' => Carbon::today()->addDays(5)->toDateString(),
            ]);

        $response->assertSessionHasErrors('link_referensi');
    }

    public function test_pembimbing_dan_peserta_bisa_mengunduh_lampiran_tugas(): void
    {
        $file = UploadedFile::fake()->create('template_laporan.docx', 256, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');

        $this->actingAs($this->pembimbing)
            ->post(route('pembimbing.tugas.store'), [
                'penempatan_id' => $this->penempatan->id,
                'judul' => 'Format Laporan Resmi',
                'file_lampiran' => $file,
                'kategori' => 'Dokumentasi',
                'kompleksitas' => 'rendah',
                'deadline' => Carbon::today()->addDays(7)->toDateString(),
            ]);

        $tugas = Tugas::where('judul', 'Format Laporan Resmi')->firstOrFail();

        // 1. Pembimbing mengunduh lampiran
        $respPembimbing = $this->actingAs($this->pembimbing)
            ->get(route('pembimbing.tugas.lampiran', $tugas->id));
        $respPembimbing->assertOk();

        // 2. Peserta penerima tugas mengunduh lampiran
        $respPeserta = $this->actingAs($this->peserta)
            ->get(route('peserta.tugas.lampiran', $tugas->id));
        $respPeserta->assertOk();
    }

    public function test_peserta_lain_ditolak_mengunduh_lampiran_tugas_idor_protection(): void
    {
        $file = UploadedFile::fake()->create('dokumen_rahasia.pdf', 300, 'application/pdf');

        $this->actingAs($this->pembimbing)
            ->post(route('pembimbing.tugas.store'), [
                'penempatan_id' => $this->penempatan->id,
                'judul' => 'Tugas Terproteksi',
                'file_lampiran' => $file,
                'kategori' => 'Security',
                'kompleksitas' => 'tinggi',
                'deadline' => Carbon::today()->addDays(3)->toDateString(),
            ]);

        $tugas = Tugas::where('judul', 'Tugas Terproteksi')->firstOrFail();

        // Peserta lain mencoba mengunduh lampiran peserta ini
        $response = $this->actingAs($this->pesertaLain)
            ->get(route('peserta.tugas.lampiran', $tugas->id));

        $response->assertForbidden();
    }

    public function test_dashboard_pembimbing_merender_link_tugas_create_dan_kartu_sumber_daya(): void
    {
        // Buat tugas dengan link dan file
        $tugas = Tugas::create([
            'penempatan_id' => $this->penempatan->id,
            'judul' => 'Tugas Dashboard Visual',
            'deskripsi' => 'Rincian tugas untuk visual dashboard.',
            'link_referensi' => 'https://figma.com/file/magkom-mockup',
            'file_lampiran' => 'tugas/1/dummy.xlsx',
            'nama_file_lampiran' => 'data_analisis.xlsx',
            'ukuran_file_lampiran' => 512000,
            'kategori' => 'Data Science',
            'kompleksitas' => 'sedang',
            'deadline' => Carbon::today()->addDays(2),
            'status' => StatusTugasEnum::Belum,
        ]);

        $response = $this->actingAs($this->pembimbing)
            ->get(route('pembimbing.dashboard'));

        $response->assertOk();
        $response->assertSee(route('pembimbing.tugas.create'));
        $response->assertSee('+ Beri Tugas Baru');
        $response->assertSee('Tugas Dashboard Visual');
        $response->assertSee('EXCEL');
        $response->assertSee('Tautan Acuan');
    }
}
