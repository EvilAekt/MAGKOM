<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Enums\TipeAuditEnum;
use App\Models\AuditLog;
use App\Models\ModulDinamis;
use App\Models\ModulDinamisData;
use App\Models\ModulDinamisField;
use App\Models\User;
use Database\Seeders\JenisDokumenSeeder;
use Database\Seeders\RubrikSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SuperAdminMasterArchitectureTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $admin;
    protected User $pembimbing;
    protected User $peserta;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(JenisDokumenSeeder::class);
        $this->seed(RubrikSeeder::class);

        // 1. Setup User SuperAdmin
        $this->superAdmin = new User();
        $this->superAdmin->nama = 'Super Admin Sleman';
        $this->superAdmin->email = 'superadmin.master@slemankab.go.id';
        $this->superAdmin->password = 'Password123!';
        $this->superAdmin->role = RoleEnum::SuperAdmin;
        $this->superAdmin->status_aktif = true;
        $this->superAdmin->must_change_password = false;
        $this->superAdmin->email_verified_at = now();
        $this->superAdmin->save();

        // 2. Setup User Admin
        $this->admin = new User();
        $this->admin->nama = 'Admin Dinas MagKom';
        $this->admin->email = 'admin.master@slemankab.go.id';
        $this->admin->password = 'Password123!';
        $this->admin->role = RoleEnum::Admin;
        $this->admin->status_aktif = true;
        $this->admin->must_change_password = false;
        $this->admin->email_verified_at = now();
        $this->admin->save();

        // 3. Setup User Pembimbing
        $this->pembimbing = new User();
        $this->pembimbing->nama = 'Pembimbing Master';
        $this->pembimbing->email = 'pembimbing.master@slemankab.go.id';
        $this->pembimbing->password = 'Password123!';
        $this->pembimbing->role = RoleEnum::Pembimbing;
        $this->pembimbing->status_aktif = true;
        $this->pembimbing->must_change_password = false;
        $this->pembimbing->email_verified_at = now();
        $this->pembimbing->save();

        // 4. Setup User Peserta
        $this->peserta = new User();
        $this->peserta->nama = 'Peserta Magang Master';
        $this->peserta->email = 'peserta.master@universitas.ac.id';
        $this->peserta->password = 'Password123!';
        $this->peserta->role = RoleEnum::Peserta;
        $this->peserta->status_aktif = true;
        $this->peserta->must_change_password = false;
        $this->peserta->email_verified_at = now();
        $this->peserta->save();
    }

    /**
     * TEST 1: SuperAdmin dapat mengakses Audit Log Multi-Kanal dan melihat 15 kanal.
     */
    public function test_superadmin_can_access_multi_channel_audit_log_and_view_15_channels(): void
    {
        $this->actingAs($this->superAdmin);

        AuditLog::create([
            'user_id' => $this->superAdmin->id,
            'tipe' => TipeAuditEnum::Auth,
            'channel' => 'auth-session',
            'severity' => 'info',
            'aksi' => 'User login berhasil',
            'waktu' => now(),
        ]);

        $response = $this->get(route('superadmin.audit-log.index'));

        $response->assertStatus(200);
        $response->assertSee('KANAL PERISTIWA');
        $response->assertSee('Semua Aktivitas Stream');
        $response->assertSee('Autentikasi & Sesi');
        $response->assertSee('Pendaftaran & Berkas');
        $response->assertSee('Verifikasi Admin');
        $response->assertSee('Penempatan & Mentor');
        $response->assertSee('Kuota & Periode');
        $response->assertSee('Tugas & Progres');
        $response->assertSee('Logbook & Presensi');
        $response->assertSee('Penilaian & Sertifikat');
        $response->assertSee('Survei & Mutu Alumni');
        $response->assertSee('Manajemen Pengguna');
        $response->assertSee('Konfigurasi & Master');
        $response->assertSee('Cadangan Basis Data');
        $response->assertSee('Peringatan Keamanan');
        $response->assertSee('Modul Kustom Dinamis');
    }

    /**
     * TEST 2: Filter Audit Log berdasarkan channel spesifik dan severity.
     */
    public function test_superadmin_can_filter_audit_log_by_specific_channel_and_severity(): void
    {
        $this->actingAs($this->superAdmin);

        AuditLog::create([
            'user_id' => $this->superAdmin->id,
            'tipe' => TipeAuditEnum::Aksi,
            'channel' => 'security-alert',
            'severity' => 'danger',
            'aksi' => 'Percobaan serangan brute-force terdeteksi',
            'waktu' => now(),
        ]);

        AuditLog::create([
            'user_id' => $this->superAdmin->id,
            'tipe' => TipeAuditEnum::Auth,
            'channel' => 'auth-session',
            'severity' => 'info',
            'aksi' => 'User normal login',
            'waktu' => now(),
        ]);

        $response = $this->get(route('superadmin.audit-log.channel', [
            'channel' => 'security-alert',
            'severity' => 'danger',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Percobaan serangan brute-force terdeteksi');
        $response->assertDontSee('User normal login');
    }

    /**
     * TEST 3: Ekspor Audit Log CSV streaming tersanitasi.
     */
    public function test_superadmin_can_export_audit_log_by_channel_to_csv(): void
    {
        $this->actingAs($this->superAdmin);

        AuditLog::create([
            'user_id' => $this->superAdmin->id,
            'tipe' => TipeAuditEnum::Auth,
            'channel' => 'auth-session',
            'severity' => 'info',
            'aksi' => 'Login SuperAdmin Ekspor',
            'waktu' => now(),
        ]);

        $response = $this->get(route('superadmin.audit-log.export', ['channel' => 'auth-session']));

        $response->assertStatus(200);
        $this->assertStringContainsString('text/csv', (string) $response->headers->get('Content-Type'));
    }

    /**
     * TEST 4: SuperAdmin dapat mengakses Telemetri Kesehatan Sistem dan melihat SLA Uptime banner.
     */
    public function test_superadmin_can_access_system_health_monitor_and_see_sla_banner(): void
    {
        $this->actingAs($this->superAdmin);

        $response = $this->get(route('superadmin.health.index'));

        $response->assertStatus(200);
        $response->assertSee('Pusat Telemetri Infrastruktur Portal MagKom');
        $response->assertSee('ALL SYSTEMS OPERATIONAL');
        $response->assertSee('Koneksi Basis Data');
        $response->assertSee('Storage & Kapasitas Disk');
        $response->assertSee('Suite Pemeliharaan Cepat Proaktif');
    }

    /**
     * TEST 5: Menjalankan ke-4 aksi diagnostik 1-klik di Telemetri Kesehatan.
     */
    public function test_superadmin_can_execute_all_four_diagnostic_actions_successfully(): void
    {
        $this->actingAs($this->superAdmin);

        // Aksi 1: Security Sweep
        $resp1 = $this->post(route('superadmin.health.security-sweep'));
        $resp1->assertRedirect();
        $resp1->assertSessionHas('status_keberhasilan');

        // Aksi 2: Optimasi Basis Data
        $resp2 = $this->post(route('superadmin.health.optimize-db'));
        $resp2->assertRedirect();
        $resp2->assertSessionHas('status_keberhasilan');

        // Aksi 3: Bersihkan Cache Sistem
        $resp3 = $this->post(route('superadmin.health.clear-cache'));
        $resp3->assertRedirect();
        $resp3->assertSessionHas('status_keberhasilan');

        // Aksi 4: Uji Izin Tulis Storage Disk
        $resp4 = $this->post(route('superadmin.health.test-storage'));
        $resp4->assertRedirect();
        $resp4->assertSessionHas('status_keberhasilan');
    }

    /**
     * TEST 6: SuperAdmin dapat merancang dan menyimpan modul dinamis baru (Modul Builder).
     */
    public function test_superadmin_can_create_custom_dynamic_module_with_various_field_types(): void
    {
        $this->actingAs($this->superAdmin);

        $payload = [
            'nama_modul' => 'Inventaris Laptop Magang',
            'slug' => 'inventaris-laptop',
            'deskripsi' => 'Pencatatan aset peminjaman laptop dinas untuk peserta magang.',
            'ikon' => 'laptop',
            'target_roles' => ['superadmin', 'admin', 'pembimbing'],
            'fields' => [
                [
                    'nama_kolom' => 'nomor_seri',
                    'label' => 'Nomor Seri / SN',
                    'tipe_data' => 'text',
                    'wajib' => '1',
                    'tampil_di_tabel' => '1',
                    'dapat_dicari' => '1',
                ],
                [
                    'nama_kolom' => 'jumlah_unit',
                    'label' => 'Jumlah Unit',
                    'tipe_data' => 'number',
                    'wajib' => '1',
                    'tampil_di_tabel' => '1',
                    'dapat_dicari' => '0',
                ],
                [
                    'nama_kolom' => 'kondisi_fisik',
                    'label' => 'Kondisi Fisik',
                    'tipe_data' => 'select',
                    'opsi_pilihan' => 'Baik, Rusak Ringan, Rusak Berat',
                    'wajib' => '1',
                    'tampil_di_tabel' => '1',
                    'dapat_dicari' => '1',
                ],
                [
                    'nama_kolom' => 'catatan_khusus',
                    'label' => 'Catatan Khusus',
                    'tipe_data' => 'textarea',
                    'wajib' => '0',
                    'tampil_di_tabel' => '0',
                    'dapat_dicari' => '0',
                ],
            ],
        ];

        $response = $this->post(route('superadmin.modul-builder.store'), $payload);

        $response->assertRedirect(route('superadmin.modul-builder.index'));
        $this->assertDatabaseHas('modul_dinamis', [
            'slug' => 'inventaris-laptop',
            'nama_modul' => 'Inventaris Laptop Magang',
        ]);

        $this->assertDatabaseHas('modul_dinamis_field', [
            'nama_kolom' => 'nomor_seri',
            'tipe_data' => 'text',
        ]);
        $this->assertDatabaseHas('modul_dinamis_field', [
            'nama_kolom' => 'kondisi_fisik',
            'tipe_data' => 'select',
        ]);
    }

    /**
     * TEST 7: CRUD Universal Data Baris pada Modul Kustom Dinamis.
     */
    public function test_superadmin_can_perform_full_crud_on_dynamic_custom_module_data(): void
    {
        $this->actingAs($this->superAdmin);

        // 1. Buat Modul Dinamis
        $modul = ModulDinamis::create([
            'nama_modul' => 'Aset Tablet',
            'slug' => 'aset-tablet',
            'deskripsi' => 'Pengelolaan tablet operasional dinas',
            'ikon' => 'box',
            'target_roles' => ['superadmin', 'admin'],
            'status_aktif' => true,
            'dibuat_oleh' => $this->superAdmin->id,
        ]);

        ModulDinamisField::create([
            'modul_id' => $modul->id,
            'nama_kolom' => 'kode_aset',
            'label' => 'Kode Aset',
            'tipe_data' => 'text',
            'wajib' => true,
            'tampil_di_tabel' => true,
            'dapat_dicari' => true,
            'urutan' => 1,
        ]);

        ModulDinamisField::create([
            'modul_id' => $modul->id,
            'nama_kolom' => 'jumlah',
            'label' => 'Jumlah Unit',
            'tipe_data' => 'number',
            'wajib' => true,
            'tampil_di_tabel' => true,
            'dapat_dicari' => false,
            'urutan' => 2,
        ]);

        // 2. Akses index modul data
        $indexResponse = $this->get(route('superadmin.modul.index', ['slug' => 'aset-tablet']));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('Aset Tablet');

        // 3. Tambah baris data baru
        $storeResponse = $this->post(route('superadmin.modul.store', ['slug' => 'aset-tablet']), [
            'kode_aset' => 'TAB-SLM-001',
            'jumlah' => 5,
        ]);
        $storeResponse->assertRedirect();

        $this->assertDatabaseHas('modul_dinamis_data', [
            'modul_id' => $modul->id,
        ]);

        $record = ModulDinamisData::where('modul_id', $modul->id)->firstOrFail();
        $this->assertEquals('TAB-SLM-001', $record->data['kode_aset']);
        $this->assertEquals(5, $record->data['jumlah']);

        // 4. Update baris data
        $updateResponse = $this->put(route('superadmin.modul.update', [
            'slug' => 'aset-tablet',
            'id' => $record->id,
        ]), [
            'kode_aset' => 'TAB-SLM-001-REV',
            'jumlah' => 7,
        ]);
        $updateResponse->assertRedirect();

        $record->refresh();
        $this->assertEquals('TAB-SLM-001-REV', $record->data['kode_aset']);
        $this->assertEquals(7, $record->data['jumlah']);

        // 5. Ekspor data ke CSV
        $exportResponse = $this->get(route('superadmin.modul.export', ['slug' => 'aset-tablet']));
        $exportResponse->assertStatus(200);
        $this->assertStringContainsString('text/csv', (string) $exportResponse->headers->get('Content-Type'));

        // 6. Hapus baris data (Soft Delete)
        $deleteResponse = $this->delete(route('superadmin.modul.destroy', [
            'slug' => 'aset-tablet',
            'id' => $record->id,
        ]));
        $deleteResponse->assertRedirect();

        $this->assertSoftDeleted('modul_dinamis_data', ['id' => $record->id]);
    }

    /**
     * TEST 8: Toggle status keaktifan modul dinamis.
     */
    public function test_superadmin_can_toggle_module_active_status_and_delete_module(): void
    {
        $this->actingAs($this->superAdmin);

        $modul = ModulDinamis::create([
            'nama_modul' => 'Kunjungan Tamu',
            'slug' => 'kunjungan-tamu',
            'ikon' => 'users',
            'target_roles' => ['superadmin'],
            'status_aktif' => true,
            'dibuat_oleh' => $this->superAdmin->id,
        ]);

        // Toggle nonaktif
        $toggleResponse = $this->patch(route('superadmin.modul-builder.toggle', $modul));
        $toggleResponse->assertRedirect();
        $modul->refresh();
        $this->assertFalse((bool) $modul->status_aktif);

        // Hapus modul
        $deleteResponse = $this->delete(route('superadmin.modul-builder.destroy', $modul));
        $deleteResponse->assertRedirect(route('superadmin.modul-builder.index'));
        $this->assertDatabaseMissing('modul_dinamis', ['id' => $modul->id]);
    }

    /**
     * TEST 9: RBAC menolak role non-SuperAdmin saat mengakses Modul Builder.
     */
    public function test_rbac_restricts_non_superadmin_from_accessing_modul_builder(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('superadmin.modul-builder.index'));
        // Non-superadmin should be rejected (403 or redirect)
        $this->assertTrue(in_array($response->status(), [403, 302], true));
    }
}
