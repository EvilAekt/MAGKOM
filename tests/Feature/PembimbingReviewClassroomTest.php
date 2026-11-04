<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JalurEnum;
use App\Enums\JenjangEnum;
use App\Enums\KompleksitasEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\StatusReviewProgresEnum;
use App\Enums\StatusTugasEnum;
use App\Enums\SumberPenempatanEnum;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\Penempatan;
use App\Models\PesertaProfil;
use App\Models\ProgresLog;
use App\Models\Tugas;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PembimbingReviewClassroomTest extends TestCase
{
    use RefreshDatabase;

    protected User $pembimbing;

    protected User $pembimbingLain;

    protected User $peserta;

    protected Penempatan $penempatan;

    protected Penempatan $penempatanLain;

    protected Bidang $bidang;

    protected Jurusan $jurusan;

    protected Tugas $tugas;

    protected Tugas $tugasLain;

    protected ProgresLog $progresLog;

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
            'deskripsi' => 'Bidang Aptika Diskominfo Sleman',
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

        $this->pembimbingLain = $this->createUser(
            'Pembimbing Lain, M.Kom',
            'pembimbing.lain@slemankab.go.id',
            RoleEnum::Pembimbing,
            $this->bidang->id
        );

        $this->peserta = $this->createUser(
            'Naufal Pratama',
            'naufal@example.com',
            RoleEnum::Peserta
        );

        $userLain = $this->createUser(
            'Peserta Pembimbing Lain',
            'peserta.lain@example.com',
            RoleEnum::Peserta
        );

        PesertaProfil::create([
            'user_id' => $this->peserta->id,
            'institusi' => 'SMK Negeri 2 Depok Sleman',
            'nim_nisn' => '12345678',
            'jurusan_id' => $this->jurusan->id,
            'jenjang' => JenjangEnum::Smk,
            'no_hp' => '081234567891',
            'tanggal_mulai' => Carbon::today()->subDays(10),
            'tanggal_selesai' => Carbon::today()->addDays(50),
        ]);

        PesertaProfil::create([
            'user_id' => $userLain->id,
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

        $this->penempatan = Penempatan::create([
            'peserta_id' => $this->peserta->id,
            'pembimbing_id' => $this->pembimbing->id,
            'bidang_id' => $this->bidang->id,
            'bulan_magang_id' => $bulanMagang->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'tgl_mulai' => Carbon::today()->subDays(10),
            'tgl_selesai' => Carbon::today()->addDays(30),
            'status' => StatusPenempatanEnum::Aktif,
        ]);

        $this->penempatanLain = Penempatan::create([
            'peserta_id' => $userLain->id,
            'pembimbing_id' => $this->pembimbingLain->id,
            'bidang_id' => $this->bidang->id,
            'bulan_magang_id' => $bulanMagang->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'tgl_mulai' => Carbon::today()->subDays(10),
            'tgl_selesai' => Carbon::today()->addDays(30),
            'status' => StatusPenempatanEnum::Aktif,
        ]);

        $this->tugas = Tugas::create([
            'penempatan_id' => $this->penempatan->id,
            'pembimbing_id' => $this->pembimbing->id,
            'judul' => 'Penyusunan Draf Laporan Akhir Magang & Portofolio',
            'deskripsi' => 'Menyusun laporan akhir PKL/Magang Bab 1 s/d Bab 4 dan lembar pengesahan.',
            'kategori' => 'Laporan Akhir',
            'kompleksitas' => KompleksitasEnum::Sedang,
            'status' => StatusTugasEnum::Belum,
            'deadline' => Carbon::today()->addDays(5),
            'waktu_mulai' => Carbon::now(),
        ]);

        $this->tugasLain = Tugas::create([
            'penempatan_id' => $this->penempatanLain->id,
            'pembimbing_id' => $this->pembimbingLain->id,
            'judul' => 'Tugas Rahasia Pembimbing Lain',
            'deskripsi' => 'Instruksi tugas privat.',
            'kategori' => 'Internal',
            'kompleksitas' => KompleksitasEnum::Tinggi,
            'status' => StatusTugasEnum::Belum,
            'deadline' => Carbon::today()->addDays(5),
            'waktu_mulai' => Carbon::now(),
        ]);

        $this->progresLog = ProgresLog::create([
            'tugas_id' => $this->tugas->id,
            'tgl_lapor' => Carbon::today()->subDay(),
            'catatan' => 'Saya telah menyelesaikan draft Bab 1 dan Bab 2 serta menyematkan link Figma.',
            'link' => 'https://figma.com/file/sample-project-magkom',
            'status_review' => StatusReviewProgresEnum::Menunggu,
        ]);
    }

    public function test_halaman_review_tugas_menampilkan_komponen_classroom_workspace(): void
    {
        $response = $this->actingAs($this->pembimbing)->get(route('pembimbing.tugas.show', $this->tugas));

        $response->assertOk();
        // Memastikan komponen classroom review workspace ada di DOM
        $response->assertSee('review-workspace');
        $response->assertSee('review-topbar');
        $response->assertSee('review-main-canvas');
        $response->assertSee('review-sidebar');
        $response->assertSee('Evaluasi');
        $response->assertSee('Umpan Balik');

        // Memastikan kiriman mentee tampil (link Figma dan catatan)
        $response->assertSee('https://figma.com/file/sample-project-magkom');
        $response->assertSee('Buka di Jendela Baru');
        $response->assertSee('Saya telah menyelesaikan draft Bab 1 dan Bab 2');

        // Memastikan TIDAK ADA input nilai numerik (/100)
        $response->assertDontSee('/100');
        $response->assertDontSee('Masukkan nilai');
    }

    public function test_pembimbing_bisa_mengembalikan_tugas_dengan_umpan_balik_revisi(): void
    {
        $response = $this->actingAs($this->pembimbing)->post(route('pembimbing.progres.review', $this->progresLog), [
            'status_review' => 'perlu_revisi',
            'feedback' => 'Tolong lengkapi daftar pustaka dan lampirkan diagram alur sistem Bab 3.',
        ]);

        $response->assertRedirect();
        $this->assertEquals(StatusReviewProgresEnum::PerluRevisi, $this->progresLog->fresh()->status_review);
        $this->assertEquals(
            'Tolong lengkapi daftar pustaka dan lampirkan diagram alur sistem Bab 3.',
            $this->progresLog->fresh()->feedback
        );
    }

    public function test_validasi_menolak_pengembalian_revisi_tanpa_catatan_umpan_balik(): void
    {
        $response = $this->actingAs($this->pembimbing)->post(route('pembimbing.progres.review', $this->progresLog), [
            'status_review' => 'perlu_revisi',
            'feedback' => '',
        ]);

        $response->assertSessionHasErrors('feedback');
        $this->assertEquals(StatusReviewProgresEnum::Menunggu, $this->progresLog->fresh()->status_review);
    }

    public function test_pembimbing_bisa_menerima_progres_tugas(): void
    {
        $response = $this->actingAs($this->pembimbing)->post(route('pembimbing.progres.review', $this->progresLog), [
            'status_review' => 'diterima',
            'feedback' => 'Hasil pengerjaan sangat baik dan rapi.',
        ]);

        $response->assertRedirect();
        $this->assertEquals(StatusReviewProgresEnum::Diterima, $this->progresLog->fresh()->status_review);
    }

    public function test_pembimbing_bisa_menandai_tugas_selesai(): void
    {
        $response = $this->actingAs($this->pembimbing)->post(route('pembimbing.tugas.selesai', $this->tugas));

        $response->assertRedirect();
        $this->assertEquals(StatusTugasEnum::Selesai, $this->tugas->fresh()->status);
    }

    public function test_anti_idor_pembimbing_dilarang_melihat_atau_mereview_tugas_mentee_pembimbing_lain(): void
    {
        // View tugas orang lain ditolak 403
        $responseView = $this->actingAs($this->pembimbing)->get(route('pembimbing.tugas.show', $this->tugasLain));
        $responseView->assertForbidden();

        // Selesai tugas orang lain ditolak 403
        $responseSelesai = $this->actingAs($this->pembimbing)->post(route('pembimbing.tugas.selesai', $this->tugasLain));
        $responseSelesai->assertForbidden();
    }
}
