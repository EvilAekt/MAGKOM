<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JalurEnum;
use App\Enums\JenjangEnum;
use App\Enums\KompleksitasEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusPendaftaranEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\StatusTugasEnum;
use App\Enums\SumberPenempatanEnum;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\Kuota;
use App\Models\Pendaftaran;
use App\Models\Penempatan;
use App\Models\PesertaProfil;
use App\Models\Tugas;
use App\Models\User;
use Database\Seeders\JenisDokumenSeeder;
use Database\Seeders\RubrikSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSuperAdminPhaseTwoTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected User $admin;

    protected User $pembimbing;

    protected User $peserta;

    protected BulanMagang $bulan;

    protected Jurusan $jurusan;

    protected Kuota $kuota;

    protected Penempatan $penempatan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(JenisDokumenSeeder::class);
        $this->seed(RubrikSeeder::class);

        $this->jurusan = Jurusan::create([
            'nama_jurusan' => 'Teknik Komputer dan Jaringan',
            'kategori' => 'smk',
            'aktif' => true,
        ]);

        $this->bulan = BulanMagang::create([
            'bulan_mulai' => now()->startOfMonth(),
            'tgl_buka' => now()->subDays(5)->toDateString(),
            'tgl_tutup' => now()->addDays(15)->toDateString(),
            'aktif' => true,
        ]);

        $this->kuota = Kuota::create([
            'bulan_magang_id' => $this->bulan->id,
            'jurusan_id' => $this->jurusan->id,
            'jumlah' => 10,
            'terisi' => 1,
            'aktif' => true,
        ]);

        // Users
        $this->superAdmin = new User;
        $this->superAdmin->nama = 'Super Administrator Sleman';
        $this->superAdmin->email = 'superadmin@slemankab.go.id';
        $this->superAdmin->password = 'Password123!';
        $this->superAdmin->role = RoleEnum::SuperAdmin;
        $this->superAdmin->status_aktif = true;
        $this->superAdmin->must_change_password = false;
        $this->superAdmin->email_verified_at = now();
        $this->superAdmin->save();

        $this->admin = new User;
        $this->admin->nama = 'Admin Verifikator Sleman';
        $this->admin->email = 'admin@slemankab.go.id';
        $this->admin->password = 'Password123!';
        $this->admin->role = RoleEnum::Admin;
        $this->admin->status_aktif = true;
        $this->admin->must_change_password = false;
        $this->admin->email_verified_at = now();
        $this->admin->save();

        $this->pembimbing = new User;
        $this->pembimbing->nama = 'Pembimbing Lapangan Aptika';
        $this->pembimbing->email = 'pembimbing@slemankab.go.id';
        $this->pembimbing->password = 'Password123!';
        $this->pembimbing->role = RoleEnum::Pembimbing;
        $this->pembimbing->status_aktif = true;
        $this->pembimbing->must_change_password = false;
        $this->pembimbing->email_verified_at = now();
        $this->pembimbing->save();

        $this->peserta = new User;
        $this->peserta->nama = 'Budi Santoso';
        $this->peserta->email = 'budi@example.com';
        $this->peserta->password = 'Password123!';
        $this->peserta->role = RoleEnum::Peserta;
        $this->peserta->status_aktif = true;
        $this->peserta->must_change_password = false;
        $this->peserta->email_verified_at = now();
        $this->peserta->save();

        PesertaProfil::create([
            'user_id' => $this->peserta->id,
            'nim_nisn' => '220199201',
            'institusi' => 'SMK Negeri 2 Depok Sleman',
            'jurusan_id' => $this->jurusan->id,
            'jenjang' => JenjangEnum::Smk,
            'no_hp' => '081234567890',
        ]);

        $this->penempatan = Penempatan::create([
            'peserta_id' => $this->peserta->id,
            'kuota_id' => $this->kuota->id,
            'pembimbing_id' => $this->pembimbing->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'dibuat_oleh' => $this->admin->id,
            'tgl_mulai' => now()->toDateString(),
            'tgl_selesai' => now()->addMonths(2)->toDateString(),
            'status' => StatusPenempatanEnum::Aktif,
        ]);
    }

    /** @test */
    public function admin_can_access_dashboard_with_operational_metrics(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.dashboard');
        $response->assertSee('Dashboard Operasional Administrasi');
        $response->assertSee('Perlu Verifikasi Segera');
        $response->assertSee('Peserta Magang Aktif');
        $response->assertSee('Keterisian Kuota Magang Periode');
    }

    /** @test */
    public function superadmin_can_access_dashboard_with_executive_metrics(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('superadmin.dashboard'));

        $response->assertStatus(200);
        $response->assertViewIs('superadmin.dashboard');
        $response->assertSee('Dashboard Eksekutif Tata Kelola MagKom');
        $response->assertSee('Beban Pembimbing Lapangan');
        $response->assertSee('Demografi Asal Institusi');
        $response->assertSee('SMK Negeri 2 Depok Sleman');
    }

    /** @test */
    public function superadmin_can_view_monitoring_peserta_aktif_index_and_detail_read_only(): void
    {
        // 1. Tambahkan tugas untuk peserta
        Tugas::create([
            'penempatan_id' => $this->penempatan->id,
            'judul' => 'Instalasi Jaringan Server Diskominfo',
            'deskripsi' => 'Konfigurasi switch VLAN dan gateway',
            'kategori' => 'Infrastruktur',
            'kompleksitas' => KompleksitasEnum::Sedang,
            'deadline' => now()->addDays(5)->toDateString(),
            'status' => StatusTugasEnum::Proses,
        ]);

        // 2. Akses index monitoring
        $resIndex = $this->actingAs($this->superAdmin)->get(route('superadmin.monitoring.index'));
        $resIndex->assertStatus(200);
        $resIndex->assertViewIs('superadmin.monitoring.index');
        $resIndex->assertSee('Budi Santoso');
        $resIndex->assertSee('SMK Negeri 2 Depok Sleman');

        // 3. Akses detail monitoring baca-saja
        $resShow = $this->actingAs($this->superAdmin)->get(route('superadmin.monitoring.show', $this->penempatan));
        $resShow->assertStatus(200);
        $resShow->assertViewIs('superadmin.monitoring.show');
        $resShow->assertSee('Mode Pemantauan Baca-Saja (SuperAdmin)');
        $resShow->assertSee('Instalasi Jaringan Server Diskominfo');
    }

    /** @test */
    public function superadmin_forbidden_from_mutating_pembimbing_duties(): void
    {
        // AC-08-5: SuperAdmin ditolak dengan status HTTP 403 saat mencoba membuat tugas
        $resTugas = $this->actingAs($this->superAdmin)->post(route('pembimbing.tugas.store'), [
            'penempatan_id' => $this->penempatan->id,
            'judul' => 'Tugas Ilegal SuperAdmin',
            'deadline' => now()->addDays(5)->toDateString(),
        ]);

        $resTugas->assertStatus(403);
    }

    /** @test */
    public function superadmin_cannot_deactivate_pembimbing_with_active_mentees(): void
    {
        // Pembimbing ini memiliki $this->penempatan (status: aktif).
        // Sesuai BR-12, sistem harus menolak penonaktifan.
        $response = $this->actingAs($this->superAdmin)->patch(route('superadmin.staf.toggle', $this->pembimbing));

        $response->assertSessionHas('error', function ($msg) {
            return str_contains($msg, 'Penonaktifan ditolak (BR-12)') && str_contains($msg, 'peserta aktif');
        });

        $this->assertTrue($this->pembimbing->fresh()->status_aktif);
    }

    /** @test */
    public function superadmin_cannot_deactivate_sole_active_pembimbing(): void
    {
        // Selesaikan penempatan agar pembimbing tidak punya mentee aktif
        $this->penempatan->update(['status' => StatusPenempatanEnum::Selesai]);

        // Karena $this->pembimbing adalah satu-satunya pembimbing aktif di sistem, penonaktifan tetap harus ditolak (BR-12)
        $response = $this->actingAs($this->superAdmin)->patch(route('superadmin.staf.toggle', $this->pembimbing));

        $response->assertSessionHas('error', function ($msg) {
            return str_contains($msg, 'Penonaktifan ditolak (BR-12)') && str_contains($msg, 'satu-satunya pembimbing aktif');
        });

        $this->assertTrue($this->pembimbing->fresh()->status_aktif);
    }

    /** @test */
    public function cannot_deactivate_bulan_or_kuota_with_active_registrations_or_placements(): void
    {
        // 1. Coba nonaktifkan kuota yang masih memiliki penempatan aktif
        $resKuota = $this->actingAs($this->admin)->patch(route('admin.kuota.toggle', $this->kuota));
        $resKuota->assertSessionHas('error', function ($msg) {
            return str_contains($msg, 'Penonaktifan ditolak (BR-12');
        });
        $this->assertTrue($this->kuota->fresh()->aktif);

        // 2. Coba nonaktifkan bulan yang masih memiliki pendaftaran aktif
        Pendaftaran::create([
            'user_id' => $this->peserta->id,
            'bulan_magang_id' => $this->bulan->id,
            'kuota_id' => $this->kuota->id,
            'durasi_bulan' => 1,
            'status' => StatusPendaftaranEnum::Diajukan,
            'diajukan_at' => now(),
        ]);

        $resBulan = $this->actingAs($this->superAdmin)->patch(route('superadmin.bulan.toggle', $this->bulan));
        $resBulan->assertSessionHas('error', function ($msg) {
            return str_contains($msg, 'Penonaktifan ditolak (BR-12)');
        });
        $this->assertTrue($this->bulan->fresh()->aktif);
    }

    /** @test */
    public function login_redirects_admin_and_superadmin_to_respective_dashboards(): void
    {
        // 1. Admin login
        $resAdmin = $this->post(route('login'), [
            'email' => $this->admin->email,
            'password' => 'Password123!',
        ]);
        $resAdmin->assertRedirect(route('admin.dashboard'));

        $this->post(route('logout'));

        // 2. SuperAdmin login
        $resSuperAdmin = $this->post(route('login'), [
            'email' => $this->superAdmin->email,
            'password' => 'Password123!',
        ]);
        $resSuperAdmin->assertRedirect(route('superadmin.dashboard'));
    }

    /** @test */
    public function waiting_list_index_supports_filtering_by_jurusan_and_bulan(): void
    {
        Pendaftaran::create([
            'user_id' => $this->peserta->id,
            'bulan_magang_id' => $this->bulan->id,
            'kuota_id' => $this->kuota->id,
            'durasi_bulan' => 1,
            'status' => StatusPendaftaranEnum::WaitingList,
            'diajukan_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.waiting-list.index', [
            'bulan_magang_id' => $this->bulan->id,
            'jurusan_id' => $this->jurusan->id,
        ]));

        $response->assertStatus(200);
        $response->assertViewIs('admin.waiting-list.index');
        $response->assertSee('Budi Santoso');
    }
}
