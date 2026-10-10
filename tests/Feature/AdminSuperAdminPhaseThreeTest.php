<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JenjangEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusPendaftaranEnum;
use App\Enums\StatusPenempatanEnum;
use App\Models\Bidang;
use App\Models\BulanMagang;
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

class AdminSuperAdminPhaseThreeTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $admin;
    protected User $pembimbing;
    protected User $peserta;
    protected BulanMagang $bulan;
    protected Jurusan $jurusan;
    protected Kuota $kuota;

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
            'jumlah' => 5,
            'terisi' => 0,
            'aktif' => true,
        ]);

        // Users
        $this->superAdmin = new User();
        $this->superAdmin->nama = 'Super Administrator Sleman';
        $this->superAdmin->email = 'superadmin@slemankab.go.id';
        $this->superAdmin->password = 'Password123!';
        $this->superAdmin->role = RoleEnum::SuperAdmin;
        $this->superAdmin->status_aktif = true;
        $this->superAdmin->must_change_password = false;
        $this->superAdmin->email_verified_at = now();
        $this->superAdmin->save();

        $this->admin = new User();
        $this->admin->nama = 'Admin Verifikator Sleman';
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
        $this->pembimbing->status_aktif = true;
        $this->pembimbing->must_change_password = false;
        $this->pembimbing->email_verified_at = now();
        $this->pembimbing->save();

        $this->peserta = new User();
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
    }

    /**
     * AC-04-4: Admin tidak dapat memindahkan pendaftaran ke Waiting List jika kuota umum masih ada.
     */
    public function test_admin_cannot_assign_waiting_list_if_quota_available(): void
    {
        $pendaftaran = Pendaftaran::create([
            'user_id' => $this->peserta->id,
            'kuota_id' => $this->kuota->id,
            'bulan_magang_id' => $this->bulan->id,
            'nomor_pendaftaran' => 'REG-2026-0001',
            'durasi_bulan' => 1,
            'status' => StatusPendaftaranEnum::Diajukan,
            'waktu_submit' => now(),
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.pendaftaran.putuskan', $pendaftaran), [
                'keputusan' => 'waiting_list',
                'catatan' => 'Pendaftaran dimasukkan ke antrean cadangan.',
            ]);

        $response->assertSessionHas('error');
        $this->assertStringContainsString('AC-04-4', session('error'));
        $this->assertEquals(StatusPendaftaranEnum::Diajukan, $pendaftaran->fresh()->status);
    }

    /**
     * AC-04-4 & AC-04-6: Admin dapat memindahkan pendaftaran ke Waiting List saat kuota umum penuh.
     */
    public function test_admin_can_assign_waiting_list_when_quota_full(): void
    {
        // Penuhkan kuota (5 dari 5)
        $this->kuota->update([
            'jumlah' => 5,
            'terisi' => 5,
        ]);

        $pendaftaran = Pendaftaran::create([
            'user_id' => $this->peserta->id,
            'kuota_id' => $this->kuota->id,
            'bulan_magang_id' => $this->bulan->id,
            'nomor_pendaftaran' => 'REG-2026-0002',
            'durasi_bulan' => 1,
            'status' => StatusPendaftaranEnum::Diajukan,
            'waktu_submit' => now(),
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.pendaftaran.putuskan', $pendaftaran), [
                'keputusan' => 'waiting_list',
                'catatan' => 'Pendaftaran dialihkan ke antrean tunggu karena kuota penuh.',
            ]);

        $response->assertRedirect(route('admin.pendaftaran.show', $pendaftaran));
        $response->assertSessionHas('success', 'Pendaftaran berhasil dialihkan ke antrean Waiting List.');
        $this->assertEquals(StatusPendaftaranEnum::WaitingList, $pendaftaran->fresh()->status);
        $this->assertEquals('Pendaftaran dialihkan ke antrean tunggu karena kuota penuh.', $pendaftaran->fresh()->catatan);
    }

    /**
     * AC-04-5: Bentrok verifikasi ganda menangani StatusSudahBerubahException dengan pesan ramah.
     */
    public function test_decision_handles_concurrency_gracefully(): void
    {
        // Status sudah berubah menjadi Diterima oleh verifikator lain
        $pendaftaran = Pendaftaran::create([
            'user_id' => $this->peserta->id,
            'kuota_id' => $this->kuota->id,
            'bulan_magang_id' => $this->bulan->id,
            'nomor_pendaftaran' => 'REG-2026-0003',
            'durasi_bulan' => 1,
            'status' => StatusPendaftaranEnum::Diterima,
            'waktu_submit' => now(),
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.pendaftaran.putuskan', $pendaftaran), [
                'keputusan' => 'tolak',
                'catatan' => 'Alasan penolakan karena dokumen tidak sesuai format resmi.',
            ]);

        $response->assertSessionHas('error', 'Status pendaftaran sudah berubah, muat ulang halaman (AC-04-5).');
    }

    /**
     * AC-04-3: Catatan penolakan dan perbaikan wajib minimal 10 karakter.
     */
    public function test_notes_min_10_chars_enforced_for_rejection_and_revision(): void
    {
        $pendaftaran = Pendaftaran::create([
            'user_id' => $this->peserta->id,
            'kuota_id' => $this->kuota->id,
            'bulan_magang_id' => $this->bulan->id,
            'nomor_pendaftaran' => 'REG-2026-0004',
            'durasi_bulan' => 1,
            'status' => StatusPendaftaranEnum::Diajukan,
            'waktu_submit' => now(),
        ]);

        // 1. Tolak dengan catatan < 10 karakter
        $responseTolakKurang = $this->actingAs($this->admin)
            ->post(route('admin.pendaftaran.putuskan', $pendaftaran), [
                'keputusan' => 'tolak',
                'catatan' => 'pendek',
            ]);
        $responseTolakKurang->assertSessionHasErrors(['catatan']);

        // 2. Perlu perbaikan dengan catatan < 10 karakter
        $responseRevisiKurang = $this->actingAs($this->admin)
            ->post(route('admin.pendaftaran.putuskan', $pendaftaran), [
                'keputusan' => 'perlu_perbaikan',
                'catatan' => 'revisi',
            ]);
        $responseRevisiKurang->assertSessionHasErrors(['catatan']);

        // 3. Tolak dengan catatan >= 10 karakter lolos validasi
        $responseTolakLolos = $this->actingAs($this->admin)
            ->post(route('admin.pendaftaran.putuskan', $pendaftaran), [
                'keputusan' => 'tolak',
                'catatan' => 'Alasan penolakan resmi karena kuota spesifik telah terpenuhi penuh.',
            ]);
        $responseTolakLolos->assertSessionDoesntHaveErrors(['catatan']);
        $this->assertEquals(StatusPendaftaranEnum::Ditolak, $pendaftaran->fresh()->status);
    }

    /**
     * BR-01 & AC-05-2: Total kuota seluruh jurusan dalam satu bulan tidak boleh melebihi kuota_umum_maks_per_bulan.
     */
    public function test_quota_request_rejects_exceeding_monthly_maximum(): void
    {
        $jurusanDua = Jurusan::create([
            'nama_jurusan' => 'Rekayasa Perangkat Lunak',
            'kategori' => 'smk',
            'aktif' => true,
        ]);

        // Kuota 1 sudah ada senilai 5
        // Coba buat kuota 2 senilai 8 (5 + 8 = 13 > batas 12)
        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.kuota.store'), [
                'bulan_magang_id' => $this->bulan->id,
                'jurusan_id' => $jurusanDua->id,
                'jumlah' => 8,
            ]);

        $response->assertSessionHasErrors(['jumlah']);

        // Coba buat kuota 2 senilai 7 (5 + 7 = 12 <= batas 12)
        $responseLolos = $this->actingAs($this->superAdmin)
            ->post(route('admin.kuota.store'), [
                'bulan_magang_id' => $this->bulan->id,
                'jurusan_id' => $jurusanDua->id,
                'jumlah' => 7,
            ]);

        $responseLolos->assertSessionDoesntHaveErrors(['jumlah']);
        $responseLolos->assertRedirect(route('admin.kuota.index', ['bulan_magang_id' => $this->bulan->id]));
    }

    /**
     * F-11 & AC-11-1: SuperAdmin dapat memperbarui data profil staf internal.
     */
    public function test_superadmin_can_update_staff_profile(): void
    {
        $bidang = Bidang::create([
            'nama_bidang' => 'Layanan Komunikasi dan Informatika',
            'aktif' => true,
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->put(route('superadmin.staf.update', $this->pembimbing), [
                'nama' => 'Pembimbing Lapangan Senior',
                'email' => 'pembimbing.senior@slemankab.go.id',
                'role' => RoleEnum::Pembimbing->value,
                'bidang_id' => $bidang->id,
            ]);

        $response->assertRedirect(route('superadmin.staf.index'));
        $response->assertSessionHas('success');

        $pembimbingFresh = $this->pembimbing->fresh();
        $this->assertEquals('Pembimbing Lapangan Senior', $pembimbingFresh->nama);
        $this->assertEquals('pembimbing.senior@slemankab.go.id', $pembimbingFresh->email);
        $this->assertEquals($bidang->id, $pembimbingFresh->bidang_id);
    }

    /**
     * BR-12: Penegakan integritas saat menonaktifkan master data (Bulan, Jurusan, Bidang, Slot Khusus).
     */
    public function test_toggle_aktif_master_data_enforces_br12(): void
    {
        // 1. Bulan Magang tanpa penempatan / pendaftaran aktif dapat dinonaktifkan tanpa error database
        $bulanKosong = BulanMagang::create([
            'bulan_mulai' => now()->addMonths(3)->startOfMonth(),
            'tgl_buka' => now()->addMonths(2)->toDateString(),
            'tgl_tutup' => now()->addMonths(2)->addDays(10)->toDateString(),
            'aktif' => true,
        ]);

        $responseBulan = $this->actingAs($this->superAdmin)
            ->patch(route('superadmin.bulan.toggle', $bulanKosong));

        $responseBulan->assertRedirect(route('superadmin.bulan.index'));
        $this->assertFalse($bulanKosong->fresh()->aktif);

        // 2. Jurusan dengan pendaftaran aktif ditolak dinonaktifkan (BR-12)
        Pendaftaran::create([
            'user_id' => $this->peserta->id,
            'kuota_id' => $this->kuota->id,
            'bulan_magang_id' => $this->bulan->id,
            'nomor_pendaftaran' => 'REG-2026-BR12',
            'durasi_bulan' => 1,
            'status' => StatusPendaftaranEnum::Diajukan,
            'waktu_submit' => now(),
        ]);

        $responseJurusan = $this->actingAs($this->superAdmin)
            ->patch(route('superadmin.jurusan.toggle', $this->jurusan));

        $responseJurusan->assertSessionHas('error');
        $this->assertStringContainsString('BR-12', session('error'));
        $this->assertTrue($this->jurusan->fresh()->aktif);

        // 3. Bidang dengan staf aktif ditolak dinonaktifkan (BR-12)
        $bidangAktif = Bidang::create([
            'nama_bidang' => 'Bidang Aptika Diskominfo',
            'aktif' => true,
        ]);
        $this->pembimbing->update(['bidang_id' => $bidangAktif->id]);

        $responseBidang = $this->actingAs($this->superAdmin)
            ->patch(route('superadmin.bidang.toggle', $bidangAktif));

        $responseBidang->assertSessionHas('error');
        $this->assertStringContainsString('BR-12', session('error'));
        $this->assertTrue($bidangAktif->fresh()->aktif);

        // 4. Slot khusus dengan penempatan aktif ditolak dinonaktifkan (BR-12)
        $slotKhusus = SlotKhusus::create([
            'bulan_mulai' => now()->startOfMonth()->toDateString(),
            'nama_kemitraan' => 'Kemitraan SMK Sleman Juara',
            'deskripsi' => 'Slot khusus kerjasama',
            'jumlah' => 3,
            'terisi' => 1,
            'aktif' => true,
            'dibuat_oleh' => $this->superAdmin->id,
        ]);

        Penempatan::create([
            'peserta_id' => $this->peserta->id,
            'slot_khusus_id' => $slotKhusus->id,
            'pembimbing_id' => $this->pembimbing->id,
            'bidang_id' => $bidangAktif->id,
            'jalur' => \App\Enums\JalurEnum::Khusus,
            'sumber' => \App\Enums\SumberPenempatanEnum::Manual,
            'status' => StatusPenempatanEnum::Aktif,
            'dibuat_oleh' => $this->admin->id,
            'tgl_mulai' => now()->toDateString(),
            'tgl_selesai' => now()->addMonths(2)->toDateString(),
        ]);

        $responseSlot = $this->actingAs($this->superAdmin)
            ->patch(route('superadmin.slot-khusus.toggle', $slotKhusus));

        $responseSlot->assertSessionHas('error');
        $this->assertStringContainsString('BR-12', session('error'));
        $this->assertTrue($slotKhusus->fresh()->aktif);
    }

    /**
     * F-17 & AC-17-1, AC-17-2: Hub Laporan dan ekspor CSV mendukung filter rentang waktu dan jurusan.
     */
    public function test_laporan_and_export_supports_date_range_and_jurusan_filter(): void
    {
        $responseIndex = $this->actingAs($this->admin)
            ->get(route('admin.laporan.index', [
                'tab' => 'pendaftaran',
                'dari_tanggal' => now()->subDays(10)->toDateString(),
                'sampai_tanggal' => now()->addDays(10)->toDateString(),
                'jurusan_id' => $this->jurusan->id,
            ]));

        $responseIndex->assertOk();
        $responseIndex->assertViewHas('pendaftaranData');
        $responseIndex->assertViewHas('jurusanList');

        // Ekspor CSV Pendaftaran
        $responseExportPendaftaran = $this->actingAs($this->admin)
            ->get(route('admin.laporan.pendaftaran', [
                'dari_tanggal' => now()->subDays(10)->toDateString(),
                'sampai_tanggal' => now()->addDays(10)->toDateString(),
                'jurusan_id' => $this->jurusan->id,
            ]));

        $responseExportPendaftaran->assertOk();
        $this->assertStringContainsString('text/csv', $responseExportPendaftaran->headers->get('content-type') ?? '');

        // Ekspor CSV Penempatan
        $responseExportPenempatan = $this->actingAs($this->admin)
            ->get(route('admin.laporan.penempatan', [
                'dari_tanggal' => now()->subDays(10)->toDateString(),
                'sampai_tanggal' => now()->addDays(10)->toDateString(),
                'jurusan_id' => $this->jurusan->id,
            ]));

        $responseExportPenempatan->assertOk();
        $this->assertStringContainsString('text/csv', $responseExportPenempatan->headers->get('content-type') ?? '');
    }
}
