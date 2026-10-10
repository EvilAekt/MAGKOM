<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JenjangEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusKetersediaanEnum;
use App\Enums\StatusPendaftaranEnum;
use App\Enums\StatusPenempatanEnum;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Dokumen;
use App\Models\JenisDokumen;
use App\Models\Jurusan;
use App\Models\Kuota;
use App\Models\Pendaftaran;
use App\Models\Penempatan;
use App\Models\PesertaProfil;
use App\Models\SlotKhusus;
use App\Models\User;
use Database\Seeders\JenisDokumenSeeder;
use Database\Seeders\RubrikSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSuperAdminPortalTest extends TestCase
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
    protected Pendaftaran $pendaftaran;
    protected Penempatan $penempatan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(JenisDokumenSeeder::class);
        $this->seed(RubrikSeeder::class);

        // 1. Master Bidang & Jurusan
        $this->bidang = Bidang::create([
            'nama_bidang' => 'Bidang Aplikasi Informatika (Aptika)',
            'deskripsi' => 'Pengembangan dan tata kelola sistem aplikasi',
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

        // 3. Users
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
        $this->peserta->nama = 'Budi Prakoso';
        $this->peserta->email = 'budi@example.com';
        $this->peserta->password = 'Password123!';
        $this->peserta->role = RoleEnum::Peserta;
        $this->peserta->status_aktif = true;
        $this->peserta->must_change_password = false;
        $this->peserta->email_verified_at = now();
        $this->peserta->save();

        PesertaProfil::create([
            'user_id' => $this->peserta->id,
            'nim_nisn' => '22110001',
            'institusi' => 'Universitas Gadjah Mada',
            'jurusan_id' => $this->jurusan->id,
            'jenjang' => JenjangEnum::S1,
            'no_hp' => '081234567890',
            'alamat' => 'Sleman, D.I. Yogyakarta',
        ]);

        // 4. Pendaftaran Eksisting
        $this->pendaftaran = Pendaftaran::create([
            'user_id' => $this->peserta->id,
            'bulan_magang_id' => $this->bulan->id,
            'kuota_id' => $this->kuota->id,
            'nomor_pendaftaran' => 'REG-202610-0001',
            'asal_instansi' => 'Universitas Gadjah Mada',
            'durasi_bulan' => 3,
            'status' => StatusPendaftaranEnum::Diajukan,
            'diajukan_at' => now()->subDay(),
        ]);

        $jenisCv = JenisDokumen::where('kode', 'cv')->first();
        if ($jenisCv) {
            Dokumen::create([
                'pendaftaran_id' => $this->pendaftaran->id,
                'jenis_dokumen_id' => $jenisCv->id,
                'path' => 'dokumen/test_cv.pdf',
                'nama_asli' => 'Curriculum_Vitae_Budi.pdf',
                'mime' => 'application/pdf',
                'ukuran' => 102400,
                'hash' => hash('sha256', 'dummy_content'),
            ]);
        }

        // 5. Penempatan Eksisting
        $this->penempatan = Penempatan::create([
            'peserta_id' => $this->peserta->id,
            'pendaftaran_id' => $this->pendaftaran->id,
            'kuota_id' => $this->kuota->id,
            'pembimbing_id' => $this->pembimbing->id,
            'bidang_id' => $this->bidang->id,
            'jalur' => \App\Enums\JalurEnum::Umum,
            'sumber' => \App\Enums\SumberPenempatanEnum::Online,
            'dibuat_oleh' => $this->admin->id,
            'tgl_mulai' => now()->toDateString(),
            'tgl_selesai' => now()->addMonths(3)->toDateString(),
            'status' => StatusPenempatanEnum::Aktif,
        ]);
    }

    public function test_admin_can_access_pendaftaran_index_with_search_and_filter(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.pendaftaran.index', [
            'q' => 'Budi',
            'status' => StatusPendaftaranEnum::Diajukan->value,
            'bulan_magang_id' => $this->bulan->id,
        ]));

        $response->assertStatus(200);
        $response->assertSee($this->pendaftaran->nomor_pendaftaran);
        $response->assertSee('Budi Prakoso');
        $response->assertSee('Tinjau Berkas');
    }

    public function test_admin_can_view_pendaftaran_detail_with_in_app_preview_modal(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.pendaftaran.show', $this->pendaftaran));

        $response->assertStatus(200);
        $response->assertSee($this->pendaftaran->nomor_pendaftaran);
        $response->assertSee('docPreviewModal');
        $response->assertSee('openDocPreview');
        $response->assertSee('Mentee Aktif');
    }

    public function test_admin_decision_flow_terima_places_candidate_atomically(): void
    {
        // Buat calon peserta baru yang statusnya diajukan
        $calon = new User();
        $calon->nama = 'Siti Rahma';
        $calon->email = 'siti@example.com';
        $calon->password = 'Password123!';
        $calon->role = RoleEnum::Peserta;
        $calon->status_aktif = true;
        $calon->must_change_password = false;
        $calon->save();

        $pendaftaranBaru = Pendaftaran::create([
            'user_id' => $calon->id,
            'bulan_magang_id' => $this->bulan->id,
            'kuota_id' => $this->kuota->id,
            'nomor_pendaftaran' => 'REG-202610-0002',
            'asal_instansi' => 'UNY',
            'durasi_bulan' => 3,
            'status' => StatusPendaftaranEnum::Diajukan,
            'diajukan_at' => now(),
        ]);

        $initialTerisi = $this->kuota->fresh()->terisi;

        $response = $this->actingAs($this->admin)->post(route('admin.pendaftaran.putuskan', $pendaftaranBaru), [
            'keputusan' => 'terima',
            'pembimbing_id' => $this->pembimbing->id,
            'bidang_id' => $this->bidang->id,
        ]);

        $response->assertRedirect(route('admin.pendaftaran.show', $pendaftaranBaru));
        $this->assertEquals(StatusPendaftaranEnum::Diterima, $pendaftaranBaru->fresh()->status);
        $this->assertEquals($initialTerisi + 1, $this->kuota->fresh()->terisi);
        $this->assertDatabaseHas('penempatan', [
            'peserta_id' => $calon->id,
            'pembimbing_id' => $this->pembimbing->id,
            'status' => StatusPenempatanEnum::Aktif->value,
        ]);
    }

    public function test_admin_decision_flow_minta_perbaikan_and_tolak(): void
    {
        // 1. Minta perbaikan pada pengajuan diajukan
        $responseRevisi = $this->actingAs($this->admin)->post(route('admin.pendaftaran.putuskan', $this->pendaftaran), [
            'keputusan' => 'perlu_perbaikan',
            'catatan' => 'Proposal magang belum ditandatangani oleh dosen pembimbing kampus.',
        ]);

        $responseRevisi->assertRedirect(route('admin.pendaftaran.show', $this->pendaftaran));
        $this->assertEquals(StatusPendaftaranEnum::PerluPerbaikan, $this->pendaftaran->fresh()->status);
        $this->assertStringContainsString('Proposal', $this->pendaftaran->fresh()->catatan);

        // 2. Tolak pada pengajuan lain yang berstatus Diajukan
        $calonLain = new User();
        $calonLain->nama = 'Agus Prasetyo';
        $calonLain->email = 'agus@example.com';
        $calonLain->password = 'Password123!';
        $calonLain->role = RoleEnum::Peserta;
        $calonLain->status_aktif = true;
        $calonLain->save();

        $pendaftaranTolak = Pendaftaran::create([
            'user_id' => $calonLain->id,
            'bulan_magang_id' => $this->bulan->id,
            'kuota_id' => $this->kuota->id,
            'asal_instansi' => 'Amikom',
            'durasi_bulan' => 3,
            'status' => StatusPendaftaranEnum::Diajukan,
            'diajukan_at' => now(),
        ]);

        $responseTolak = $this->actingAs($this->admin)->post(route('admin.pendaftaran.putuskan', $pendaftaranTolak), [
            'keputusan' => 'tolak',
            'catatan' => 'Berkas tidak sesuai dengan kriteria penerimaan instansi.',
        ]);

        $responseTolak->assertRedirect(route('admin.pendaftaran.show', $pendaftaranTolak));
        $this->assertEquals(StatusPendaftaranEnum::Ditolak, $pendaftaranTolak->fresh()->status);
    }

    public function test_admin_quota_management_index_renders_occupancy_bars(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.kuota.index', [
            'bulan_magang_id' => $this->bulan->id,
        ]));

        $response->assertStatus(200);
        $response->assertSee('Teknik Informatika');
        $response->assertSee('Okupansi:');
        $response->assertSee('modal-tambah-kuota');
        $response->assertSee('modal-edit-kuota');
    }

    public function test_admin_waiting_list_promotion_and_bulk_closure(): void
    {
        // Buat calon peserta waiting list
        $wlUser = new User();
        $wlUser->nama = 'Doni Kusuma';
        $wlUser->email = 'doni@example.com';
        $wlUser->password = 'Password123!';
        $wlUser->role = RoleEnum::Peserta;
        $wlUser->status_aktif = true;
        $wlUser->save();

        $wlPendaftaran = Pendaftaran::create([
            'user_id' => $wlUser->id,
            'bulan_magang_id' => $this->bulan->id,
            'kuota_id' => $this->kuota->id,
            'asal_instansi' => 'UPN Veteran',
            'durasi_bulan' => 3,
            'status' => StatusPendaftaranEnum::WaitingList,
            'diajukan_at' => now()->subHours(2),
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.waiting-list.index'));
        $response->assertStatus(200);
        $response->assertSee($wlPendaftaran->nomor_pendaftaran);
        $response->assertSee('Promosikan');

        // Test bulk closure saat periode sudah mulai
        $responseTutup = $this->actingAs($this->admin)->post(route('admin.waiting-list.tutup', $this->bulan));
        $responseTutup->assertRedirect(route('admin.waiting-list.index'));
        $this->assertEquals(StatusPendaftaranEnum::Ditolak, $wlPendaftaran->fresh()->status);
        $this->assertStringContainsString('Antrean waiting list ditutup secara otomatis', $wlPendaftaran->fresh()->catatan);
    }

    public function test_admin_can_manage_penempatan_and_preview_surat_diterima(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.penempatan.index'));
        $response->assertStatus(200);
        $response->assertSee('modal-surat-preview');
        $response->assertSee('openSuratPreview');

        // Test streaming pratinjau surat diterima
        $responseStream = $this->actingAs($this->admin)->get(route('admin.penempatan.surat-diterima.pratinjau', $this->penempatan));
        $responseStream->assertStatus(200);
        $responseStream->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_superadmin_can_create_and_update_bulan_magang(): void
    {
        $bulanMulaiBaru = now()->addMonths(2)->startOfMonth()->toDateString();
        $tglBuka = now()->addMonth()->startOfMonth()->toDateString();
        $tglTutup = now()->addMonth()->endOfMonth()->toDateString();

        // 1. Create bulan magang baru
        $responseStore = $this->actingAs($this->superAdmin)->post(route('superadmin.bulan.store'), [
            'bulan_mulai' => $bulanMulaiBaru,
            'tgl_buka' => $tglBuka,
            'tgl_tutup' => $tglTutup,
            'override_status' => StatusKetersediaanEnum::Segera->value,
        ]);

        $responseStore->assertRedirect(route('superadmin.bulan.index'));
        $this->assertDatabaseHas('bulan_magang', [
            'bulan_mulai' => $bulanMulaiBaru . ' 00:00:00',
            'override_status' => StatusKetersediaanEnum::Segera->value,
        ]);

        $createdBulan = BulanMagang::where('bulan_mulai', 'like', substr($bulanMulaiBaru, 0, 7) . '%')->first();
        $this->assertNotNull($createdBulan);

        // 2. Update override status
        $responseUpdate = $this->actingAs($this->superAdmin)->put(route('superadmin.bulan.update', $createdBulan), [
            'bulan_mulai' => $bulanMulaiBaru,
            'tgl_buka' => $tglBuka,
            'tgl_tutup' => $tglTutup,
            'override_status' => StatusKetersediaanEnum::Tersedia->value,
        ]);

        $responseUpdate->assertRedirect(route('superadmin.bulan.index'));
        $this->assertEquals(StatusKetersediaanEnum::Tersedia, $createdBulan->fresh()->override_status);

        // 3. Toggle aktif
        $responseToggle = $this->actingAs($this->superAdmin)->patch(route('superadmin.bulan.toggle', $createdBulan));
        $responseToggle->assertRedirect(route('superadmin.bulan.index'));
        $this->assertFalse($createdBulan->fresh()->aktif);
    }

    public function test_superadmin_can_view_audit_logs_and_modal_diff(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('superadmin.audit-log.index'));

        $response->assertStatus(200);
        $response->assertSee('modalLogDetail');
        $response->assertSee('Nilai Lama (Sebelum Mutasi)');
        $response->assertSee('Nilai Baru (Setelah Mutasi)');
    }
}
