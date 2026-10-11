<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JalurEnum;
use App\Enums\JenjangEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusPendaftaranEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\SumberPenempatanEnum;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\Kuota;
use App\Models\Pendaftaran;
use App\Models\Penempatan;
use App\Models\PesertaProfil;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminDashboardEnhancementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $pembimbing;
    protected User $peserta;
    protected BulanMagang $bulanAktif;
    protected BulanMagang $bulanDepan;
    protected Jurusan $jurusan;
    protected Kuota $kuota;
    protected Bidang $bidang;

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

    protected function setUp(): void
    {
        parent::setUp();

        $this->bidang = Bidang::create(['nama_bidang' => 'Aplikasi Informatika']);

        $this->jurusan = Jurusan::create([
            'nama_jurusan' => 'Teknik Informatika',
            'kategori' => 'mahasiswa',
            'aktif' => true,
        ]);

        $this->bulanAktif = BulanMagang::create([
            'bulan_mulai' => now()->startOfMonth(),
            'tgl_buka' => now()->subDays(10)->toDateString(),
            'tgl_tutup' => now()->addDays(10)->toDateString(),
            'aktif' => true,
        ]);

        $this->bulanDepan = BulanMagang::create([
            'bulan_mulai' => now()->addMonth()->startOfMonth(),
            'tgl_buka' => now()->addDays(5)->toDateString(),
            'tgl_tutup' => now()->addDays(25)->toDateString(),
            'aktif' => true,
        ]);

        $this->kuota = Kuota::create([
            'bulan_magang_id' => $this->bulanAktif->id,
            'jurusan_id' => $this->jurusan->id,
            'jumlah' => 12,
            'terisi' => 2,
            'aktif' => true,
        ]);

        $this->admin = $this->createUser('Admin Magang', 'admin.magkom@slemankab.go.id', RoleEnum::Admin);
        $this->pembimbing = $this->createUser('Pembimbing Aptika', 'pembimbing@slemankab.go.id', RoleEnum::Pembimbing, $this->bidang->id);
        $this->peserta = $this->createUser('Peserta Satu', 'peserta.satu@test.com', RoleEnum::Peserta);

        PesertaProfil::create([
            'user_id' => $this->peserta->id,
            'jurusan_id' => $this->jurusan->id,
            'institusi' => 'Universitas Gadjah Mada',
            'jenjang' => JenjangEnum::S1,
            'nim_nisn' => '2110001',
            'no_hp' => '081234567890',
        ]);
    }

    public function test_admin_bisa_mengakses_dashboard_dengan_metrik_lengkap_dan_command_center(): void
    {
        // 1. Buat pendaftaran biasa (baru)
        $p1 = Pendaftaran::create([
            'user_id' => $this->peserta->id,
            'bulan_magang_id' => $this->bulanAktif->id,
            'kuota_id' => $this->kuota->id,
            'durasi_bulan' => 3,
            'status' => StatusPendaftaranEnum::Diajukan,
            'created_at' => now(),
        ]);

        // 2. Buat pendaftaran kritis SLA (> 3 hari)
        $peserta2 = $this->createUser('Peserta Dua', 'peserta.dua@test.com', RoleEnum::Peserta);
        PesertaProfil::create([
            'user_id' => $peserta2->id,
            'jurusan_id' => $this->jurusan->id,
            'institusi' => 'UNY',
            'jenjang' => JenjangEnum::S1,
            'nim_nisn' => '2110002',
            'no_hp' => '081234567891',
        ]);

        $p2 = Pendaftaran::create([
            'user_id' => $peserta2->id,
            'bulan_magang_id' => $this->bulanAktif->id,
            'kuota_id' => $this->kuota->id,
            'durasi_bulan' => 3,
            'status' => StatusPendaftaranEnum::Diajukan,
            'diajukan_at' => now()->subDays(4),
            'created_at' => now()->subDays(4),
        ]);

        // 3. Buat penempatan aktif pembimbing
        Penempatan::create([
            'peserta_id' => $this->peserta->id,
            'pembimbing_id' => $this->pembimbing->id,
            'bidang_id' => $this->bidang->id,
            'bulan_magang_id' => $this->bulanAktif->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'tgl_mulai' => Carbon::today(),
            'tgl_selesai' => Carbon::today()->addMonths(3),
            'status' => StatusPenempatanEnum::Aktif,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertViewIs('admin.dashboard');

        // Pastikan assert string warisan dari test eksisting tetap ada
        $response->assertSee('Dashboard Operasional Administrasi');
        $response->assertSee('Perlu Verifikasi Segera');
        $response->assertSee('Peserta Magang Aktif');
        $response->assertSee('Keterisian Kuota Magang Periode');

        // Pastikan fitur baru ada di view
        $response->assertSee($p1->nomor_pendaftaran);
        $response->assertSee($p2->nomor_pendaftaran);
        $response->assertSee('Pembimbing Aptika');
        $response->assertSee('Teknik Informatika');
        $response->assertSee('Mendekati Batas SLA');
    }

    public function test_admin_bisa_memfilter_dashboard_berdasarkan_periode_bulan_magang(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard', ['bulan_id' => $this->bulanDepan->id]));

        $response->assertOk();
        $response->assertSee($this->bulanDepan->bulan_mulai->translatedFormat('F Y'));
    }

    public function test_peserta_dan_pembimbing_ditolak_mengakses_dashboard_admin(): void
    {
        $resPeserta = $this->actingAs($this->peserta)->get(route('admin.dashboard'));
        $resPeserta->assertForbidden();

        $resPembimbing = $this->actingAs($this->pembimbing)->get(route('admin.dashboard'));
        $resPembimbing->assertForbidden();
    }
}
