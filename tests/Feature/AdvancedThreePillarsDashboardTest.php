<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JalurEnum;
use App\Enums\JenjangEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusPendaftaranEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\SumberPenempatanEnum;
use App\Models\AgendaBimbingan;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\Kuota;
use App\Models\Pendaftaran;
use App\Models\Penempatan;
use App\Models\PesertaProfil;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

final class AdvancedThreePillarsDashboardTest extends TestCase
{
    use RefreshDatabase;

    private User $superAdmin;

    private User $admin;

    private User $pembimbing1;

    private User $pembimbing2;

    private Bidang $bidang;

    private Jurusan $jurusan;

    private BulanMagang $bulanMagang;

    private Kuota $kuota;

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

        $this->bulanMagang = BulanMagang::create([
            'bulan_mulai' => now()->startOfMonth()->toDateString(),
            'tgl_buka' => now()->subDays(10)->toDateString(),
            'tgl_tutup' => now()->addDays(20)->toDateString(),
            'aktif' => true,
        ]);

        $this->kuota = Kuota::create([
            'bulan_magang_id' => $this->bulanMagang->id,
            'jurusan_id' => $this->jurusan->id,
            'jumlah' => 10,
            'terisi' => 0,
            'aktif' => true,
        ]);

        $this->superAdmin = $this->createUser('Super Admin MagKom', 'superadmin@slemankab.go.id', RoleEnum::SuperAdmin);
        $this->admin = $this->createUser('Admin Dinas Magang', 'admin@slemankab.go.id', RoleEnum::Admin);
        $this->pembimbing1 = $this->createUser('Pembimbing Aptika Satu', 'pembimbing1@slemankab.go.id', RoleEnum::Pembimbing, $this->bidang->id);
        $this->pembimbing2 = $this->createUser('Pembimbing Aptika Dua', 'pembimbing2@slemankab.go.id', RoleEnum::Pembimbing, $this->bidang->id);
    }

    private function createUser(string $nama, string $email, RoleEnum $role, ?int $bidangId = null): User
    {
        $user = new User;
        $user->nama = $nama;
        $user->email = $email;
        $user->password = Hash::make('Password123!');
        $user->role = $role;
        $user->status_aktif = true;
        $user->must_change_password = false;
        $user->email_verified_at = now();
        $user->bidang_id = $bidangId;
        $user->save();

        return $user;
    }

    private function createPeserta(string $nama, string $email): User
    {
        $peserta = $this->createUser($nama, $email, RoleEnum::Peserta);

        PesertaProfil::create([
            'user_id' => $peserta->id,
            'nim_nisn' => '2210511001',
            'institusi' => 'Universitas Gadjah Mada',
            'jurusan_id' => $this->jurusan->id,
            'jenjang' => JenjangEnum::S1,
            'no_hp' => '081234567890',
        ]);

        return $peserta;
    }

    // =========================================================================
    // PILAR 1: DASHBOARD ADMINISTRASI & HUB SERTIFIKAT KELULUSAN
    // =========================================================================

    public function test_admin_dashboard_displays_sla_breached_and_upcoming_offboarding(): void
    {
        $peserta1 = $this->createPeserta('Calon Peserta Tertunda', 'tertunda@ugm.ac.id');
        Pendaftaran::create([
            'user_id' => $peserta1->id,
            'bulan_magang_id' => $this->bulanMagang->id,
            'kuota_id' => $this->kuota->id,
            'nomor_pendaftaran' => 'REG-SLA-001',
            'status' => StatusPendaftaranEnum::Diajukan,
            'durasi_bulan' => 3,
            'diajukan_at' => now()->subDays(4),
        ]);

        $peserta2 = $this->createPeserta('Peserta Selesai Segera', 'selesai@ugm.ac.id');
        Penempatan::create([
            'peserta_id' => $peserta2->id,
            'pembimbing_id' => $this->pembimbing1->id,
            'bidang_id' => $this->bidang->id,
            'kuota_id' => $this->kuota->id,
            'sumber' => SumberPenempatanEnum::Online->value,
            'jalur' => JalurEnum::Umum,
            'tgl_mulai' => now()->subMonths(3),
            'tgl_selesai' => now()->addDays(5),
            'status' => StatusPenempatanEnum::Aktif,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee('Mendekati Batas SLA');
        $response->assertSee('Offboarding Watchdog');
        $response->assertSee('Peserta Selesai Segera');
        $response->assertSee('Timeline Gelombang Magang');
        $response->assertSee('Daftar Peserta');
    }

    // =========================================================================
    // PILAR 2: DASHBOARD PEMBIMBING DENGAN RADAR CAPAIAN & MONITORING
    // =========================================================================

    public function test_pembimbing_dashboard_displays_competency_radar_and_mentee_agenda(): void
    {
        $peserta = $this->createPeserta('Mentee Radar', 'radar@ugm.ac.id');
        $penempatan = Penempatan::create([
            'peserta_id' => $peserta->id,
            'pembimbing_id' => $this->pembimbing1->id,
            'bidang_id' => $this->bidang->id,
            'kuota_id' => $this->kuota->id,
            'sumber' => SumberPenempatanEnum::Online->value,
            'jalur' => JalurEnum::Umum,
            'tgl_mulai' => now()->subMonths(1),
            'tgl_selesai' => now()->addMonths(2),
            'status' => StatusPenempatanEnum::Aktif,
        ]);

        AgendaBimbingan::create([
            'penempatan_id' => $penempatan->id,
            'pembimbing_id' => $this->pembimbing1->id,
            'judul_kegiatan' => 'Mentoring Rutin Hari Jumat',
            'tipe' => 'tatap_muka',
            'tgl_bimbingan' => now()->addDay()->toDateString(),
            'jam_mulai' => '08:30',
            'status' => 'terjadwal',
        ]);

        $response = $this->actingAs($this->pembimbing1)->get(route('pembimbing.dashboard'));

        $response->assertOk();
        $response->assertSee('Radar Capaian Kompetensi Mentee Bidang');
        $response->assertSee('Agenda Mentoring');
        $response->assertSee('Mentoring Rutin Hari Jumat');
    }

    // =========================================================================
    // PILAR 3: SUPERADMIN DISASTER RECOVERY & BACKUP HUB
    // =========================================================================

    public function test_superadmin_can_trigger_database_backup_and_download(): void
    {
        // 1. Memicu snapshot backup
        $response = $this->actingAs($this->superAdmin)->post(route('superadmin.backup.snapshot'));

        $response->assertRedirect(route('superadmin.backup.index'));

        // Cek file dibuat di direktori backups
        $backupDir = storage_path('app/backups');
        $files = File::files($backupDir);
        $this->assertNotEmpty($files);

        $latestFile = $files[0]->getFilename();

        // Cek Audit Log terekam
        $this->assertDatabaseHas('audit_log', [
            'user_id' => $this->superAdmin->id,
            'aksi' => 'backup.snapshot_generated',
        ]);

        // 2. Unduh berkas backup
        $downloadResp = $this->actingAs($this->superAdmin)->get(route('superadmin.backup.download', $latestFile));
        $downloadResp->assertOk();

        // 3. Uji proteksi path traversal
        $traversalResp = $this->actingAs($this->superAdmin)->get(route('superadmin.backup.download', '..%2F..%2F.env'));
        $this->assertTrue(in_array($traversalResp->status(), [400, 404]));

        // 4. Hapus berkas backup
        $deleteResp = $this->actingAs($this->superAdmin)->delete(route('superadmin.backup.destroy', $latestFile));
        $deleteResp->assertRedirect(route('superadmin.backup.index'));
        $this->assertFileDoesNotExist($backupDir.DIRECTORY_SEPARATOR.$latestFile);
    }
}
