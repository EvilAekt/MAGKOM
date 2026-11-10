<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Enums\StatusPendaftaranEnum;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\Kuota;
use App\Models\Pendaftaran;
use App\Models\Pengaturan;
use App\Models\User;
use App\Services\SuperAdminExecutiveMetricsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PredikatBatasValidationTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superAdmin = new User;
        $this->superAdmin->nama = 'SuperAdmin Test';
        $this->superAdmin->email = 'superadmin.pengaturan@test.com';
        $this->superAdmin->password = 'secret123';
        $this->superAdmin->role = RoleEnum::SuperAdmin;
        $this->superAdmin->email_verified_at = now();
        $this->superAdmin->must_change_password = false;
        $this->superAdmin->status_aktif = true;
        $this->superAdmin->save();
    }

    public function test_superadmin_can_view_operational_settings_with_accurate_counter(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('superadmin.pengaturan.index'));

        $response->assertOk();
        $response->assertViewHas('totalParameter', 12);
        $response->assertSee('Parameter Operasional Aktif');
        $response->assertSee('Batas Maksimal Anggota Kelompok');
        $response->assertSee('Durasi Magang Maksimal yang Diizinkan');
        $response->assertSee('Target Batas Waktu SLA Verifikasi Berkas');
        $response->assertSee('Modul Pendaftaran Kelompok / Tim');
    }

    public function test_validates_operational_settings_boundaries(): void
    {
        // Validasi batasan minimal & batas melampaui maksimum
        $response = $this->actingAs($this->superAdmin)->put(route('superadmin.pengaturan.update'), [
            'masa_tenggang_bulan' => 0, // min 1
            'kuota_umum_maks_per_bulan' => 0, // min 1
            'logbook_maks_mundur_hari' => -1, // min 1
            'berkas_maks_mb' => 0, // min 1
            'deadline_mendekati_hari' => 0, // min 1
            'maks_anggota_kelompok' => 0, // min 1
            'durasi_magang_maks_bulan' => 15, // max 12
            'sla_verifikasi_hari' => 0, // min 1
        ]);

        $response->assertSessionHasErrors([
            'masa_tenggang_bulan',
            'kuota_umum_maks_per_bulan',
            'logbook_maks_mundur_hari',
            'berkas_maks_mb',
            'deadline_mendekati_hari',
            'maks_anggota_kelompok',
            'durasi_magang_maks_bulan',
            'sla_verifikasi_hari',
        ]);
    }

    public function test_accepts_valid_operational_settings_and_logs_audit(): void
    {
        $payload = [
            'kuota_umum_maks_per_bulan' => 25,
            'slot_khusus_default' => 5,
            'maks_peserta_aktif_per_pembimbing' => 15,
            'maks_anggota_kelompok' => 5,
            'durasi_magang_maks_bulan' => 8,
            'logbook_maks_mundur_hari' => 4,
            'berkas_maks_mb' => 12,
            'deadline_mendekati_hari' => 5,
            'sla_verifikasi_hari' => 7,
            'masa_tenggang_bulan' => 3,
            'retensi_audit_akses_data_hari' => 180,
            'fitur_pendaftaran_kelompok_aktif' => 1,
        ];

        $response = $this->actingAs($this->superAdmin)->put(route('superadmin.pengaturan.update'), $payload);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('superadmin.pengaturan.index'));

        $this->assertDatabaseHas('pengaturan', [
            'kunci' => 'kuota_umum_maks_per_bulan',
            'nilai' => '25',
        ]);
        $this->assertDatabaseHas('pengaturan', [
            'kunci' => 'maks_anggota_kelompok',
            'nilai' => '5',
        ]);
        $this->assertDatabaseHas('pengaturan', [
            'kunci' => 'durasi_magang_maks_bulan',
            'nilai' => '8',
        ]);
        $this->assertDatabaseHas('pengaturan', [
            'kunci' => 'sla_verifikasi_hari',
            'nilai' => '7',
        ]);
        $this->assertDatabaseHas('pengaturan', [
            'kunci' => 'fitur_pendaftaran_kelompok_aktif',
            'nilai' => '1',
        ]);

        $this->assertDatabaseHas('audit_log', [
            'entitas' => 'pengaturan',
            'user_id' => $this->superAdmin->id,
        ]);
    }

    public function test_sla_setting_affects_executive_metrics_watchdog(): void
    {
        // Set SLA jadi 3 hari
        Pengaturan::setNilai('sla_verifikasi_hari', '3');
        Pengaturan::clearCache();

        $peserta = new User;
        $peserta->nama = 'Peserta Test SLA';
        $peserta->email = 'peserta.sla@test.com';
        $peserta->password = 'secret123';
        $peserta->role = RoleEnum::Peserta;
        $peserta->email_verified_at = now();
        $peserta->must_change_password = false;
        $peserta->status_aktif = true;
        $peserta->save();

        $bulan = BulanMagang::create([
            'bulan_mulai' => now()->startOfMonth(),
            'tgl_buka' => now()->subMonth(),
            'tgl_tutup' => now()->addMonth(),
            'aktif' => true,
        ]);

        $jurusan = Jurusan::create([
            'nama_jurusan' => 'Teknik Informatika',
            'kategori' => 's1',
            'aktif' => true,
        ]);

        $kuota = Kuota::create([
            'bulan_magang_id' => $bulan->id,
            'jurusan_id' => $jurusan->id,
            'jumlah' => 10,
            'terisi' => 0,
            'aktif' => true,
        ]);

        // Pendaftaran diajukan 4 hari yang lalu (melebihi SLA 3 hari)
        $pendaftaran = new Pendaftaran;
        $pendaftaran->user_id = $peserta->id;
        $pendaftaran->bulan_magang_id = $bulan->id;
        $pendaftaran->kuota_id = $kuota->id;
        $pendaftaran->durasi_bulan = 3;
        $pendaftaran->status = StatusPendaftaranEnum::Diajukan;
        $pendaftaran->diajukan_at = now()->subDays(4);
        $pendaftaran->save();

        $service = app(SuperAdminExecutiveMetricsService::class);
        $data = $service->buildDashboardData();

        $this->assertArrayHasKey('antreanMelebihiSla', $data);
        $this->assertGreaterThanOrEqual(1, $data['antreanMelebihiSla']);
    }

    public function test_unauthorized_users_cannot_access_or_update_settings(): void
    {
        $peserta = new User;
        $peserta->nama = 'Peserta Test';
        $peserta->email = 'peserta.biasa@test.com';
        $peserta->password = 'secret123';
        $peserta->role = RoleEnum::Peserta;
        $peserta->email_verified_at = now();
        $peserta->must_change_password = false;
        $peserta->status_aktif = true;
        $peserta->save();

        $responseGet = $this->actingAs($peserta)->get(route('superadmin.pengaturan.index'));
        $responseGet->assertForbidden();

        $responsePut = $this->actingAs($peserta)->put(route('superadmin.pengaturan.update'), [
            'kuota_umum_maks_per_bulan' => 50,
        ]);
        $responsePut->assertForbidden();
    }

    public function test_group_registration_disabled_when_toggle_inactive(): void
    {
        Pengaturan::setNilai('fitur_pendaftaran_kelompok_aktif', '0');
        Pengaturan::clearCache();

        $response = $this->post(route('register'), [
            'nama' => 'Ketua Tim',
            'email' => 'ketua.tim@gmail.com',
            'password' => 'P@ssword123',
            'password_confirmation' => 'P@ssword123',
            'tipe_pendaftaran' => 'kelompok',
            'nama_kelompok' => 'Tim Alpha',
        ]);

        $response->assertSessionHasErrors(['tipe_pendaftaran']);
    }

    public function test_group_registration_respects_max_members_setting(): void
    {
        Pengaturan::setNilai('fitur_pendaftaran_kelompok_aktif', '1');
        Pengaturan::setNilai('maks_anggota_kelompok', '3'); // max 1 ketua + 2 anggota
        Pengaturan::clearCache();

        $response = $this->post(route('register'), [
            'nama' => 'Ketua Tim',
            'email' => 'ketua.tim.3@gmail.com',
            'password' => 'P@ssword123',
            'password_confirmation' => 'P@ssword123',
            'tipe_pendaftaran' => 'kelompok',
            'nama_kelompok' => 'Tim Alpha',
            'anggota' => [
                ['nama' => 'Anggota 1', 'nim_nisn' => '12345', 'no_hp' => '081234567891', 'email' => 'a1@gmail.com'],
                ['nama' => 'Anggota 2', 'nim_nisn' => '12346', 'no_hp' => '081234567892', 'email' => 'a2@gmail.com'],
                ['nama' => 'Anggota 3', 'nim_nisn' => '12347', 'no_hp' => '081234567893', 'email' => 'a3@gmail.com'],
            ],
        ]);

        $response->assertSessionHasErrors(['anggota']);
    }
}
