<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JalurEnum;
use App\Enums\JenjangEnum;
use App\Enums\KompleksitasEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusLogbookEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\StatusReviewProgresEnum;
use App\Enums\StatusTugasEnum;
use App\Enums\SumberPenempatanEnum;
use App\Models\Bidang;
use App\Models\Jurusan;
use App\Models\Logbook;
use App\Models\Penempatan;
use App\Models\Pengaturan;
use App\Models\PesertaProfil;
use App\Models\ProgresLog;
use App\Models\Tugas;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

final class PembimbingDashboardEnhancementTest extends TestCase
{
    use RefreshDatabase;

    private User $pembimbing;
    private Bidang $bidang;
    private Jurusan $jurusan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bidang = Bidang::create([
            'nama_bidang' => 'Bidang Aplikasi Informatika',
            'deskripsi' => 'Bidang Aptika Kominfo Sleman',
            'aktif' => true,
        ]);

        $this->jurusan = Jurusan::create([
            'nama_jurusan' => 'Teknik Informatika',
            'kategori' => 'mahasiswa',
            'aktif' => true,
        ]);

        $this->pembimbing = $this->createUser(
            'Pembimbing Lapangan Utama',
            'pembimbing@magkom.go.id',
            RoleEnum::Pembimbing,
            $this->bidang->id
        );

        Pengaturan::updateOrCreate(
            ['kunci' => 'maks_peserta_aktif_per_pembimbing'],
            ['nilai' => '10']
        );
    }

    private function createUser(string $nama, string $email, RoleEnum $role, ?int $bidangId = null): User
    {
        $user = new User();
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

    public function test_pembimbing_bisa_mengakses_dashboard_dan_melihat_seluruh_elemen_4_tab(): void
    {
        $pesertaUser = $this->createUser('Budi Santoso', 'budi@ugm.ac.id', RoleEnum::Peserta);

        PesertaProfil::create([
            'user_id' => $pesertaUser->id,
            'jurusan_id' => $this->jurusan->id,
            'jenjang' => JenjangEnum::S1,
            'institusi' => 'Universitas Gadjah Mada',
            'nim_nisn' => '12345678',
            'no_hp' => '081234567890',
        ]);

        Penempatan::create([
            'peserta_id' => $pesertaUser->id,
            'pembimbing_id' => $this->pembimbing->id,
            'bidang_id' => $this->bidang->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'status' => StatusPenempatanEnum::Aktif,
            'tgl_mulai' => Carbon::today()->subDays(10),
            'tgl_selesai' => Carbon::today()->addDays(50),
        ]);

        $response = $this->actingAs($this->pembimbing)
            ->get(route('pembimbing.dashboard'));

        $response->assertOk();
        $response->assertSee('Dashboard Pembimbing');
        $response->assertSee('1. Ringkasan Utama');
        $response->assertSee('2. Pusat Antrean Reviu');
        $response->assertSee('3. Monitoring Mentee 360°');
        $response->assertSee('4. Evaluasi Rubrik');
        $response->assertSee('Beban Mentee Aktif');
        $response->assertSee('1 / 10');
        $response->assertSee('Budi Santoso');
        $response->assertSee('Universitas Gadjah Mada');
    }

    public function test_pembimbing_melihat_antrean_logbook_dan_progres_pada_tab_action_hub(): void
    {
        $pesertaUser = $this->createUser('Siti Nurhaliza', 'siti@smk2depok.sch.id', RoleEnum::Peserta);

        PesertaProfil::create([
            'user_id' => $pesertaUser->id,
            'jurusan_id' => $this->jurusan->id,
            'jenjang' => JenjangEnum::Smk,
            'institusi' => 'SMK Negeri 2 Depok Sleman',
            'nim_nisn' => '87654321',
            'no_hp' => '081234567891',
        ]);

        $penempatan = Penempatan::create([
            'peserta_id' => $pesertaUser->id,
            'pembimbing_id' => $this->pembimbing->id,
            'bidang_id' => $this->bidang->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'status' => StatusPenempatanEnum::Aktif,
            'tgl_mulai' => Carbon::today()->subDays(5),
            'tgl_selesai' => Carbon::today()->addDays(25),
        ]);

        Logbook::create([
            'penempatan_id' => $penempatan->id,
            'tanggal' => Carbon::today(),
            'kegiatan' => 'Menyusun dokumentasi modul dashboard pembimbing dinas.',
            'status' => StatusLogbookEnum::Terkirim,
        ]);

        $tugas = Tugas::create([
            'penempatan_id' => $penempatan->id,
            'judul' => 'Perancangan Antarmuka MagKom',
            'kategori' => 'Pengembangan Sistem',
            'kompleksitas' => KompleksitasEnum::Sedang,
            'deadline' => Carbon::today()->addDays(5),
            'status' => StatusTugasEnum::Proses,
        ]);

        ProgresLog::create([
            'tugas_id' => $tugas->id,
            'catatan' => 'Implementasi mock-up Figma selesai 80 persen.',
            'tgl_lapor' => Carbon::today(),
            'status_review' => StatusReviewProgresEnum::Menunggu,
        ]);

        $response = $this->actingAs($this->pembimbing)
            ->get(route('pembimbing.dashboard'));

        $response->assertOk();
        $response->assertSee('Menyusun dokumentasi modul dashboard');
        $response->assertSee('Perancangan Antarmuka MagKom');
        $response->assertSee('Implementasi mock-up Figma selesai 80 persen.');
    }

    public function test_penegakan_sec_03_sesi_diputus_jika_status_aktif_false(): void
    {
        $this->pembimbing->status_aktif = false;
        $this->pembimbing->save();

        $response = $this->actingAs($this->pembimbing)
            ->get(route('pembimbing.dashboard'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_penegakan_sec_12_superadmin_strictly_read_only_via_middleware(): void
    {
        $superadmin = $this->createUser('Super Administrator', 'superadmin@magkom.go.id', RoleEnum::SuperAdmin);

        // 1. GET Dashboard diizinkan untuk SuperAdmin (Read-Only)
        $responseGet = $this->actingAs($superadmin)
            ->get(route('pembimbing.dashboard'));
        $responseGet->assertOk();

        // 2. POST membuat tugas DITOLAK 403 oleh EnsureSuperAdminReadOnly
        $responsePost = $this->actingAs($superadmin)
            ->post(route('pembimbing.tugas.store'), [
                'penempatan_id' => 1,
                'judul' => 'Tugas Ilegal SuperAdmin',
                'deadline' => Carbon::tomorrow()->toDateString(),
            ]);
        $responsePost->assertForbidden();
    }

    public function test_proteksi_idor_antrean_dan_mentee_terisolasi_per_pembimbing(): void
    {
        $pembimbingLain = $this->createUser('Pembimbing Lain', 'pembimbing.lain@magkom.go.id', RoleEnum::Pembimbing);
        $pesertaLain = $this->createUser('Mentee Orang Lain', 'mentee.lain@gmail.com', RoleEnum::Peserta);

        PesertaProfil::create([
            'user_id' => $pesertaLain->id,
            'jurusan_id' => $this->jurusan->id,
            'jenjang' => JenjangEnum::S1,
            'institusi' => 'Kampus Lain',
            'nim_nisn' => '99887766',
            'no_hp' => '081234567899',
        ]);

        $penempatanLain = Penempatan::create([
            'peserta_id' => $pesertaLain->id,
            'pembimbing_id' => $pembimbingLain->id,
            'bidang_id' => $this->bidang->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'status' => StatusPenempatanEnum::Aktif,
            'tgl_mulai' => Carbon::today()->subDays(2),
            'tgl_selesai' => Carbon::today()->addDays(28),
        ]);

        Logbook::create([
            'penempatan_id' => $penempatanLain->id,
            'tanggal' => Carbon::today(),
            'kegiatan' => 'Kegiatan rahasia bimbingan pembimbing lain.',
            'status' => StatusLogbookEnum::Terkirim,
        ]);

        $response = $this->actingAs($this->pembimbing)
            ->get(route('pembimbing.dashboard'));

        $response->assertOk();
        $response->assertDontSee('Mentee Orang Lain');
        $response->assertDontSee('Kegiatan rahasia bimbingan pembimbing lain.');
    }

    public function test_dashboard_menampilkan_upcoming_deadlines_logbook_alert_dan_quick_action(): void
    {
        $pesertaUser = $this->createUser('Ahmad Dahlan', 'ahmad@smk1sleman.sch.id', RoleEnum::Peserta);

        PesertaProfil::create([
            'user_id' => $pesertaUser->id,
            'jurusan_id' => $this->jurusan->id,
            'jenjang' => JenjangEnum::Smk,
            'institusi' => 'SMK Negeri 1 Sleman',
            'nim_nisn' => '11223344',
            'no_hp' => '081234567895',
        ]);

        $penempatan = Penempatan::create([
            'peserta_id' => $pesertaUser->id,
            'pembimbing_id' => $this->pembimbing->id,
            'bidang_id' => $this->bidang->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'status' => StatusPenempatanEnum::Aktif,
            'tgl_mulai' => Carbon::today()->subDays(4),
            'tgl_selesai' => Carbon::today()->addDays(26),
        ]);

        // Tugas dengan deadline 2 hari lagi (Upcoming Deadline)
        Tugas::create([
            'penempatan_id' => $penempatan->id,
            'judul' => 'Instalasi Server Jaringan Sleman',
            'kategori' => 'Infrastruktur',
            'kompleksitas' => KompleksitasEnum::Sedang,
            'deadline' => Carbon::today()->addDays(2),
            'status' => StatusTugasEnum::Proses,
        ]);

        $response = $this->actingAs($this->pembimbing)
            ->get(route('pembimbing.dashboard'));

        $response->assertOk();
        // 1. Quick Action Hero
        $response->assertSee('Beri Tugas Baru');
        $response->assertSee('Reviu Antrean Hari Ini');
        $response->assertSee('Kelola Nilai Magang');

        // 2. Komposisi Jenjang Mentee
        $response->assertSee('SMK: 1 Siswa');

        // 3. Logbook Compliance Alert (karena sudah aktif 4 hari dan belum pernah isi)
        $response->assertSee('Peringatan: 1 Mentee Belum Mengisi Logbook');
        $response->assertSee('Ahmad Dahlan');

        // 4. Upcoming Deadlines Tracker
        $response->assertSee('Tenggat Waktu Tugas Mendekati Batas Akhir');
        $response->assertSee('Instalasi Server Jaringan Sleman');
    }

    public function test_dashboard_pembimbing_menampilkan_komponen_tugas_terpadu_dengan_avatar_chip_dan_micro_progress(): void
    {
        $pesertaUser = $this->createUser('Citra Kirana', 'citra@ugm.ac.id', RoleEnum::Peserta);

        PesertaProfil::create([
            'user_id' => $pesertaUser->id,
            'jurusan_id' => $this->jurusan->id,
            'jenjang' => JenjangEnum::S1,
            'institusi' => 'Universitas Gadjah Mada',
            'nim_nisn' => '22334455',
            'no_hp' => '081234567891',
        ]);

        $penempatan = Penempatan::create([
            'peserta_id' => $pesertaUser->id,
            'pembimbing_id' => $this->pembimbing->id,
            'bidang_id' => $this->bidang->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'status' => StatusPenempatanEnum::Aktif,
            'tgl_mulai' => Carbon::today()->subDays(10),
            'tgl_selesai' => Carbon::today()->addDays(20),
        ]);

        // Tugas 1: Selesai
        Tugas::create([
            'penempatan_id' => $penempatan->id,
            'judul' => 'Perancangan Basis Data MagKom',
            'kategori' => 'Database',
            'kompleksitas' => KompleksitasEnum::Sedang,
            'deadline' => Carbon::today()->subDays(2),
            'status' => StatusTugasEnum::Selesai,
        ]);

        // Tugas 2: Proses dan memiliki ProgresLog yang menunggu evaluasi
        $tugas2 = Tugas::create([
            'penempatan_id' => $penempatan->id,
            'judul' => 'Pembuatan Endpoint API Mobile Sleman',
            'kategori' => 'Backend',
            'kompleksitas' => KompleksitasEnum::Tinggi,
            'deadline' => Carbon::today()->addDay(),
            'status' => StatusTugasEnum::Proses,
        ]);

        ProgresLog::create([
            'tugas_id' => $tugas2->id,
            'catatan' => 'Modul autentikasi dan rute API telah selesai diuji.',
            'tgl_lapor' => Carbon::today(),
            'status_review' => StatusReviewProgresEnum::Menunggu,
        ]);

        $response = $this->actingAs($this->pembimbing)
            ->get(route('pembimbing.dashboard'));

        $response->assertOk();

        // 1. Validasi keberadaan elemen list-card terpadu
        $response->assertSee('Tenggat Waktu Tugas Mendekati Batas Akhir');
        $response->assertSee('Penugasan Mentee');
        $response->assertSee('Pembuatan Endpoint API Mobile Sleman');
        $response->assertSee('Citra Kirana');
        $response->assertSee('Universitas Gadjah Mada');

        // 2. Validasi avatar chip inisial
        $response->assertSee('CK');

        // 3. Validasi status laporan progres menunggu evaluasi & tombol aksi
        $response->assertSee('Laporan Progres Menunggu Evaluasi');
        $response->assertSee('Evaluasi Progres');

        // 4. Validasi micro progress bar pada KPI tugas
        $response->assertSee('micro-progress-bar');
        $response->assertSee('50% tuntas');
    }
}
