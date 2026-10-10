<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JalurEnum;
use App\Enums\JenjangEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\SumberPenempatanEnum;
use App\Models\AuditLog;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\Kuota;
use App\Models\Penempatan;
use App\Models\Pengaturan;
use App\Models\PesertaProfil;
use App\Models\User;
use Database\Seeders\JenisDokumenSeeder;
use Database\Seeders\RubrikSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminLaporanDanArsipTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $admin;
    protected User $pembimbing;
    protected User $peserta;
    protected Bidang $bidang;
    protected Jurusan $jurusan;
    protected BulanMagang $bulan;
    protected Kuota $kuota;
    protected Penempatan $penempatanSelesai;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(JenisDokumenSeeder::class);
        $this->seed(RubrikSeeder::class);

        // 1. Master Bidang & Jurusan
        $this->bidang = Bidang::create([
            'nama_bidang' => 'Bidang Aplikasi Informatika (Aptika)',
            'deskripsi' => 'Pengembangan sistem informasi dan smart city',
            'aktif' => true,
        ]);

        $this->jurusan = Jurusan::create([
            'nama_jurusan' => 'Teknik Informatika',
            'kategori' => 'mahasiswa',
            'aktif' => true,
        ]);

        // 2. Bulan & Kuota
        $this->bulan = BulanMagang::create([
            'bulan_mulai' => now()->startOfMonth(),
            'tgl_buka' => now()->subDays(10)->toDateString(),
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

        // 3. User Accounts
        $this->superAdmin = new User();
        $this->superAdmin->nama = 'Super Administrator';
        $this->superAdmin->email = 'superadmin@slemankab.go.id';
        $this->superAdmin->password = 'Password123!';
        $this->superAdmin->role = RoleEnum::SuperAdmin;
        $this->superAdmin->status_aktif = true;
        $this->superAdmin->must_change_password = false;
        $this->superAdmin->email_verified_at = now();
        $this->superAdmin->save();

        $this->admin = new User();
        $this->admin->nama = 'Admin Verifikator';
        $this->admin->email = 'admin@slemankab.go.id';
        $this->admin->password = 'Password123!';
        $this->admin->role = RoleEnum::Admin;
        $this->admin->status_aktif = true;
        $this->admin->must_change_password = false;
        $this->admin->email_verified_at = now();
        $this->admin->save();

        $this->pembimbing = new User();
        $this->pembimbing->nama = 'Pembimbing Lapangan Aptika';
        $this->pembimbing->email = 'pembimbing@slemankab.go.id';
        $this->pembimbing->password = 'Password123!';
        $this->pembimbing->role = RoleEnum::Pembimbing;
        $this->pembimbing->bidang_id = $this->bidang->id;
        $this->pembimbing->status_aktif = true;
        $this->pembimbing->must_change_password = false;
        $this->pembimbing->email_verified_at = now();
        $this->pembimbing->save();

        $this->peserta = new User();
        $this->peserta->nama = 'Alumni Magang Sleman';
        $this->peserta->email = 'alumni@example.com';
        $this->peserta->password = 'Password123!';
        $this->peserta->role = RoleEnum::Peserta;
        $this->peserta->status_aktif = true;
        $this->peserta->must_change_password = false;
        $this->peserta->email_verified_at = now();
        $this->peserta->save();

        PesertaProfil::create([
            'user_id' => $this->peserta->id,
            'nim_nisn' => '12345678',
            'institusi' => 'Universitas Negeri Yogyakarta',
            'jurusan_id' => $this->jurusan->id,
            'jenjang' => JenjangEnum::S1,
            'no_hp' => '081299887766',
            'alamat' => 'Sleman, D.I. Yogyakarta',
        ]);

        $this->penempatanSelesai = Penempatan::create([
            'peserta_id' => $this->peserta->id,
            'kuota_id' => $this->kuota->id,
            'pembimbing_id' => $this->pembimbing->id,
            'bidang_id' => $this->bidang->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'dibuat_oleh' => $this->admin->id,
            'tgl_mulai' => now()->subMonths(3)->toDateString(),
            'tgl_selesai' => now()->subDay()->toDateString(),
            'status' => StatusPenempatanEnum::Selesai,
        ]);
    }

    public function test_admin_and_superadmin_can_access_laporan_hub(): void
    {
        // 1. Admin Verifikator dapat mengakses hub laporan
        $responseAdmin = $this->actingAs($this->admin)->get(route('admin.laporan.index'));
        $responseAdmin->assertStatus(200);
        $responseAdmin->assertSee('Hub Laporan &amp; Ekspor Data', false);
        $responseAdmin->assertSee('Rekap Pendaftaran');
        $responseAdmin->assertSee('Data Penempatan');
        $responseAdmin->assertSee('Realisasi Kuota');
        // Admin tidak melihat tab rekap nilai
        $responseAdmin->assertDontSee('tab=nilai');
        $responseAdmin->assertDontSee('Rekap Nilai (SuperAdmin)');

        // 2. SuperAdmin melihat tab rekap nilai
        $responseSuper = $this->actingAs($this->superAdmin)->get(route('admin.laporan.index'));
        $responseSuper->assertStatus(200);
        $responseSuper->assertSee('tab=nilai');
        $responseSuper->assertSee('Rekap Nilai (SuperAdmin)');
    }

    public function test_admin_forbidden_from_exporting_rekap_nilai(): void
    {
        // Admin verifikator dilarang mengakses export rekap nilai (PRD 3.3, A-20)
        $response = $this->actingAs($this->admin)->get(route('admin.laporan.nilai'));
        $response->assertStatus(403);
    }

    public function test_superadmin_can_export_all_reports_with_sanitized_csv(): void
    {
        // 1. Export Pendaftaran
        $resPendaftaran = $this->actingAs($this->superAdmin)->get(route('admin.laporan.pendaftaran'));
        $resPendaftaran->assertStatus(200);
        $resPendaftaran->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        // 2. Export Penempatan
        $resPenempatan = $this->actingAs($this->superAdmin)->get(route('admin.laporan.penempatan'));
        $resPenempatan->assertStatus(200);
        $resPenempatan->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        // 3. Export Kuota
        $resKuota = $this->actingAs($this->superAdmin)->get(route('admin.laporan.kuota'));
        $resKuota->assertStatus(200);
        $resKuota->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        // 4. Export Nilai
        $resNilai = $this->actingAs($this->superAdmin)->get(route('admin.laporan.nilai'));
        $resNilai->assertStatus(200);
        $resNilai->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }

    public function test_superadmin_can_access_arsip_peserta_index_and_detail(): void
    {
        // 1. Admin biasa dilarang mengakses arsip superadmin
        $resAdminForbidden = $this->actingAs($this->admin)->get(route('superadmin.arsip.index'));
        $resAdminForbidden->assertStatus(403);

        // 2. SuperAdmin dapat melihat daftar arsip alumni
        $resIndex = $this->actingAs($this->superAdmin)->get(route('superadmin.arsip.index'));
        $resIndex->assertStatus(200);
        $resIndex->assertSee('Arsip Peserta');
        $resIndex->assertSee('Alumni Magang Sleman');
        $resIndex->assertSee('Universitas Negeri Yogyakarta');

        // 3. SuperAdmin dapat membuka detail arsip
        \App\Models\Penilaian::create([
            'penempatan_id' => $this->penempatanSelesai->id,
            'pembimbing_id' => $this->pembimbing->id,
            'nilai_akhir' => 88.5,
            'predikat' => 'A',
            'catatan' => 'Sangat memuaskan dan berdedikasi tinggi.',
            'status' => \App\Enums\StatusPenilaianEnum::Final,
        ]);

        $resDetail = $this->actingAs($this->superAdmin)->get(route('superadmin.arsip.show', $this->penempatanSelesai));
        $resDetail->assertStatus(200);
        $resDetail->assertSee('Detail Arsip Alumni Magang');
        $resDetail->assertSee('Alumni Magang Sleman');
        $resDetail->assertSee('12345678');
        $resDetail->assertSee('Pembimbing Lapangan Aptika');
        $resDetail->assertSee('Unduh Lembar Nilai Resmi');
        $resDetail->assertSee('Predikat A');
    }

    public function test_admin_can_place_new_participant_manually(): void
    {
        $newEmail = 'peserta.baru.manual@example.com';

        $payload = [
            'mode' => 'baru',
            'nama' => 'Rina Wijayanti',
            'email' => $newEmail,
            'nim_nisn' => '9988776655',
            'institusi' => 'Universitas Sanata Dharma',
            'jurusan_id' => $this->jurusan->id,
            'jenjang' => JenjangEnum::S1->value,
            'no_hp' => '081234567890',
            'jalur' => 'umum',
            'kuota_id' => $this->kuota->id,
            'pembimbing_id' => $this->pembimbing->id,
            'bidang_id' => $this->bidang->id,
            'bulan_mulai' => now()->startOfMonth()->toDateString(),
            'durasi_bulan' => 3,
        ];

        $response = $this->actingAs($this->admin)
            ->from(route('admin.penempatan.index'))
            ->post(route('admin.penempatan.manual'), $payload);

        $response->assertRedirect(route('admin.penempatan.index'));
        $this->assertNull(session('error'));

        // Pastikan akun user dibuat
        $newUser = User::where('email', $newEmail)->first();
        $this->assertNotNull($newUser);
        $this->assertEquals(RoleEnum::Peserta, $newUser->role);
        $this->assertTrue($newUser->must_change_password);
        $this->assertNotNull($newUser->email_verified_at);

        // Pastikan profil dibuat
        $this->assertDatabaseHas('peserta_profil', [
            'user_id' => $newUser->id,
            'nim_nisn' => '9988776655',
            'institusi' => 'Universitas Sanata Dharma',
        ]);

        // Pastikan penempatan aktif dibuat
        $this->assertDatabaseHas('penempatan', [
            'peserta_id' => $newUser->id,
            'pembimbing_id' => $this->pembimbing->id,
            'bidang_id' => $this->bidang->id,
            'status' => StatusPenempatanEnum::Aktif->value,
        ]);
    }

    public function test_superadmin_can_update_predikat_thresholds(): void
    {
        $payload = [
            'masa_tenggang_bulan' => 3,
            'kuota_umum_maks_per_bulan' => 20,
            'slot_khusus_default' => 5,
            'maks_peserta_aktif_per_pembimbing' => 5,
            'logbook_maks_mundur_hari' => 3,
            'berkas_maks_mb' => 5,
            'deadline_mendekati_hari' => 7,
            'batas_predikat_a' => 88,
            'batas_predikat_b' => 74,
            'batas_predikat_c' => 58,
            'batas_predikat_d' => 42,
        ];

        $response = $this->actingAs($this->superAdmin)->put(route('superadmin.pengaturan.update'), $payload);
        $response->assertRedirect(route('superadmin.pengaturan.index'));

        // Cek pengaturan di database
        $pengaturan = Pengaturan::where('kunci', 'predikat_batas')->first();
        $this->assertNotNull($pengaturan);
        $decoded = json_decode($pengaturan->nilai, true);
        $this->assertEquals(88, $decoded['A']);
        $this->assertEquals(74, $decoded['B']);
        $this->assertEquals(58, $decoded['C']);
        $this->assertEquals(42, $decoded['D']);

        // Uji validasi urutan salah: batas_predikat_a < batas_predikat_b
        $invalidPayload = array_merge($payload, [
            'batas_predikat_a' => 60,
            'batas_predikat_b' => 75,
            'batas_predikat_c' => 50,
            'batas_predikat_d' => 30,
        ]);

        $invalidResponse = $this->actingAs($this->superAdmin)->put(route('superadmin.pengaturan.update'), $invalidPayload);
        $invalidResponse->assertSessionHasErrors(['batas_predikat_a']);
    }

    public function test_superadmin_can_filter_audit_logs(): void
    {
        // Buat rekaman audit log
        AuditLog::create([
            'user_id' => $this->superAdmin->id,
            'tipe' => \App\Enums\TipeAuditEnum::Aksi,
            'aksi' => 'Update Pengaturan',
            'entitas' => 'pengaturan',
            'id_data' => 1,
            'request_id' => 'req-test-trace-999',
            'ip' => '127.0.0.1',
            'metode' => 'PUT',
            'rute' => 'superadmin/pengaturan',
            'user_agent' => 'PHPUnit',
            'detail' => ['action' => 'update_predikat'],
            'waktu' => now(),
        ]);

        $response = $this->actingAs($this->superAdmin)->get(route('superadmin.audit-log.index', [
            'request_id' => 'req-test-trace-999',
            'tipe' => 'aksi',
        ]));

        $response->assertStatus(200);
        $response->assertSee('req-test-trace-999');
        $response->assertSee('pengaturan');
    }
}
