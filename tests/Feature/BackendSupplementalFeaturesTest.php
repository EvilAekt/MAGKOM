<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JalurEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusPendaftaranEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\SumberPenempatanEnum;
use App\Models\AuditLog;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\Kuota;
use App\Models\Pendaftaran;
use App\Models\Penempatan;
use App\Models\Pengaturan;
use App\Models\Rubrik;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class BackendSupplementalFeaturesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        // Akun SuperAdmin dari seeder memiliki must_change_password = true,
        // matikan flag ini agar SuperAdmin dapat menguji fungsionalitas lain.
        $superadmin = User::where('role', RoleEnum::SuperAdmin)->first();
        if ($superadmin) {
            $superadmin->update(['must_change_password' => false]);
        }
    }

    protected function createUser(string $nama, string $email, RoleEnum $role, bool $mustChangePassword = false): User
    {
        $user = new User();
        $user->nama = $nama;
        $user->email = $email;
        $user->password = 'PasswordRahasia123!';
        $user->role = $role;
        $user->email_verified_at = now();
        $user->status_aktif = true;
        $user->must_change_password = $mustChangePassword;
        $user->save();

        return $user;
    }

    /**
     * Uji F-11: SuperAdmin membuat, mereset password, dan menonaktifkan akun staf.
     */
    public function test_superadmin_manage_staf_and_force_change_password(): void
    {
        $superadmin = User::where('role', RoleEnum::SuperAdmin)->first();

        // 1. SuperAdmin membuat akun Admin baru (AC-11-3)
        $response = $this->actingAs($superadmin)->post(route('superadmin.staf.store'), [
            'nama' => 'Admin Baru',
            'email' => 'admin.baru@magkom.go.id',
            'role' => RoleEnum::Admin->value,
            'password' => 'AdminPassword123!',
        ]);

        $response->assertRedirect(route('superadmin.staf.index'));
        $adminBaru = User::where('email', 'admin.baru@magkom.go.id')->firstOrFail();

        $this->assertEquals(RoleEnum::Admin, $adminBaru->role);
        $this->assertTrue($adminBaru->must_change_password);
        $this->assertNotNull($adminBaru->email_verified_at);
        $this->assertTrue($adminBaru->status_aktif);

        // 2. Staf baru login dan tertahan oleh middleware EnsurePasswordChanged (BR-15, AC-11-5)
        $this->actingAs($adminBaru)
            ->get(route('admin.pendaftaran.index'))
            ->assertRedirect(route('password.ganti'));

        // 3. Staf mencoba mengganti password dengan kata sandi lama yang sama persis -> Ditolak (AC-11-5)
        $this->actingAs($adminBaru)
            ->post(route('password.ganti.update'), [
                'current_password' => 'AdminPassword123!',
                'password' => 'AdminPassword123!',
                'password_confirmation' => 'AdminPassword123!',
            ])->assertSessionHasErrors('password');

        // 4. Staf mengganti dengan kata sandi baru yang valid
        $this->actingAs($adminBaru)
            ->post(route('password.ganti.update'), [
                'current_password' => 'AdminPassword123!',
                'password' => 'PasswordBaruSuperAman999!',
                'password_confirmation' => 'PasswordBaruSuperAman999!',
            ])->assertRedirect(route('admin.pendaftaran.index'));

        $adminBaru->refresh();
        $this->assertFalse($adminBaru->must_change_password);

        // Sekarang staf dapat mengakses halaman admin
        $this->actingAs($adminBaru)
            ->get(route('admin.pendaftaran.index'))
            ->assertOk();

        // 5. SuperAdmin mereset password staf (AC-11-4)
        $this->actingAs($superadmin)
            ->post(route('superadmin.staf.reset-password', $adminBaru), [
                'password_baru' => 'PasswordResetAdmin888!',
            ])->assertRedirect(route('superadmin.staf.index'));

        $adminBaru->refresh();
        $this->assertTrue($adminBaru->must_change_password);
        $this->assertTrue(Hash::check('PasswordResetAdmin888!', $adminBaru->password));

        // 6. SuperAdmin menonaktifkan staf
        $this->actingAs($superadmin)
            ->patch(route('superadmin.staf.toggle', $adminBaru))
            ->assertRedirect(route('superadmin.staf.index'));

        $adminBaru->refresh();
        $this->assertFalse($adminBaru->status_aktif);
    }

    /**
     * Uji F-11 & BR-12: Master Data Jurusan, Bidang, Rubrik (Bobot 100), dan Pengaturan.
     */
    public function test_master_data_management_and_rubrik_weight_constraint(): void
    {
        $superadmin = User::where('role', RoleEnum::SuperAdmin)->first();

        // 1. Master Bidang
        $this->actingAs($superadmin)
            ->post(route('superadmin.bidang.store'), [
                'nama_bidang' => 'Bidang Siber dan Sandi',
                'deskripsi' => 'Pengelolaan keamanan siber instansi',
            ])->assertRedirect(route('superadmin.bidang.index'));

        $bidang = Bidang::where('nama_bidang', 'Bidang Siber dan Sandi')->firstOrFail();
        $this->assertTrue($bidang->aktif);

        // 2. Master Jurusan & Aturan BR-12 (Tolak nonaktifkan jika ada kuota aktif)
        $jurusan = Jurusan::first();
        $bulan = BulanMagang::first();

        // Buat kuota aktif
        Kuota::updateOrCreate(
            ['bulan_magang_id' => $bulan->id, 'jurusan_id' => $jurusan->id],
            ['jumlah' => 5, 'terisi' => 0, 'aktif' => true]
        );

        // Coba nonaktifkan jurusan -> Ditolak (BR-12)
        $this->actingAs($superadmin)
            ->patch(route('superadmin.jurusan.toggle', $jurusan))
            ->assertSessionHas('error');

        $jurusan->refresh();
        $this->assertTrue($jurusan->aktif);

        // 3. Rubrik Penilaian: Tolak ubah bobot jika total bobot tidak 100 (AC-10-4)
        $rubrik = Rubrik::first();
        $this->actingAs($superadmin)
            ->put(route('superadmin.rubrik.update', $rubrik), [
                'kriteria' => $rubrik->kriteria,
                'bobot' => 50, // Akan merusak total bobot menjadi 130
            ])->assertSessionHas('error');

        // 4. Pengaturan Sistem: Update konfigurasi (AC-11-7)
        $this->actingAs($superadmin)
            ->put(route('superadmin.pengaturan.update'), [
                'masa_tenggang_bulan' => 2,
                'kuota_umum_maks_per_bulan' => 15,
                'slot_khusus_default' => 4,
                'maks_peserta_aktif_per_pembimbing' => 6,
                'logbook_maks_mundur_hari' => 5,
                'berkas_maks_mb' => 3,
                'deadline_mendekati_hari' => 4,
            ])->assertRedirect(route('superadmin.pengaturan.index'));

        $this->assertEquals(2, Pengaturan::getInt('masa_tenggang_bulan'));
        $this->assertEquals(15, Pengaturan::getInt('kuota_umum_maks_per_bulan'));
    }

    /**
     * Uji F-13: Audit Log Viewer untuk SuperAdmin (Strictly Read-Only, AC-13-6).
     */
    public function test_superadmin_audit_log_viewer_and_security_gate(): void
    {
        $superadmin = User::where('role', RoleEnum::SuperAdmin)->first();
        $admin = $this->createUser('Admin Uji', 'admin.uji@kominfo.test', RoleEnum::Admin);

        // Buat aktivitas log
        AuditLog::create([
            'request_id' => 'req-test-123',
            'user_id' => $superadmin->id,
            'tipe' => \App\Enums\TipeAuditEnum::Aksi,
            'aksi' => 'test.aksi',
            'entitas' => 'users',
            'entitas_id' => $superadmin->id,
            'ip' => '127.0.0.1',
            'waktu' => now(),
        ]);

        // SuperAdmin dapat mengakses audit log
        $this->actingAs($superadmin)
            ->get(route('superadmin.audit-log.index', ['entitas' => 'users']))
            ->assertOk()
            ->assertSee('test.aksi');

        // Admin biasa ditolak mengakses audit log (403 Forbidden)
        $this->actingAs($admin)
            ->get(route('superadmin.audit-log.index'))
            ->assertForbidden();
    }

    /**
     * Uji F-04 & F-07: Pindah Pembimbing, Batalkan Penempatan (BR-07), dan Tutup Antrean Waiting List (AC-04-7).
     */
    public function test_placement_operations_and_waiting_list_closure(): void
    {
        $admin = $this->createUser('Admin Verifikator', 'admin.verif@kominfo.test', RoleEnum::Admin);
        $pembimbing1 = $this->createUser('Pembimbing Satu', 'pembimbing1@kominfo.test', RoleEnum::Pembimbing);
        $pembimbing2 = $this->createUser('Pembimbing Dua', 'pembimbing2@kominfo.test', RoleEnum::Pembimbing);
        $peserta = $this->createUser('Peserta Satu', 'peserta1@magang.test', RoleEnum::Peserta);

        $bulan = BulanMagang::first();
        $jurusan = Jurusan::first();
        $kuota = Kuota::updateOrCreate(
            ['bulan_magang_id' => $bulan->id, 'jurusan_id' => $jurusan->id],
            ['jumlah' => 10, 'terisi' => 1, 'aktif' => true]
        );


        $pendaftaran = Pendaftaran::create([
            'user_id' => $peserta->id,
            'bulan_magang_id' => $bulan->id,
            'kuota_id' => $kuota->id,
            'durasi_bulan' => 3,
            'status' => StatusPendaftaranEnum::Diterima,
            'diajukan_at' => now(),
        ]);

        $penempatan = Penempatan::create([
            'pendaftaran_id' => $pendaftaran->id,
            'peserta_id' => $peserta->id,
            'kuota_id' => $kuota->id,
            'pembimbing_id' => $pembimbing1->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'dibuat_oleh' => $admin->id,
            'tgl_mulai' => now()->addDays(5)->toDateString(), // Mulai 5 hari ke depan
            'tgl_selesai' => now()->addMonths(3)->toDateString(),
            'status' => StatusPenempatanEnum::Aktif,
        ]);

        // 1. Pindah Pembimbing (AC-07-4)
        $this->actingAs($admin)
            ->post(route('admin.penempatan.pindah-pembimbing', $penempatan), [
                'pembimbing_id' => $pembimbing2->id,
            ])->assertRedirect(route('admin.penempatan.index'));

        $penempatan->refresh();
        $this->assertEquals($pembimbing2->id, $penempatan->pembimbing_id);

        // 2. Batalkan penempatan sebelum tanggal mulai (BR-07) -> Kuota kembali terisi - 1, akun peserta tetap aktif
        $this->actingAs($admin)
            ->post(route('admin.penempatan.batalkan', $penempatan), [
                'alasan' => 'Peserta berhalangan hadir karena alasan perkuliahan mendesak.',
            ])->assertRedirect(route('admin.penempatan.index'));

        $penempatan->refresh();
        $kuota->refresh();
        $peserta->refresh();

        $this->assertEquals(StatusPenempatanEnum::MengundurkanDiri, $penempatan->status);
        $this->assertEquals(0, $kuota->terisi); // Kuota dikembalikan
        $this->assertTrue($peserta->status_aktif); // Akun tetap aktif

        // 3. Tutup Antrean Waiting List massal (AC-04-7)
        // Set bulan magang telah dimulai (misal bulan_mulai = bulan lalu)
        $bulanLalu = BulanMagang::create([
            'bulan_mulai' => now()->subMonth()->format('Y-m'),
            'tgl_buka' => now()->subMonths(2)->toDateString(),
            'tgl_tutup' => now()->subMonth()->startOfMonth()->toDateString(),
            'aktif' => true,
        ]);

        $pesertaWaiting = $this->createUser('Peserta Waiting', 'waiting@magang.test', RoleEnum::Peserta);
        $pendaftarWaiting = Pendaftaran::create([
            'user_id' => $pesertaWaiting->id,
            'bulan_magang_id' => $bulanLalu->id,
            'kuota_id' => $kuota->id,
            'durasi_bulan' => 3,
            'status' => StatusPendaftaranEnum::WaitingList,
            'diajukan_at' => now()->subMonth(),
        ]);

        $this->actingAs($admin)
            ->post(route('admin.waiting-list.tutup', $bulanLalu))
            ->assertRedirect(route('admin.waiting-list.index'));

        $pendaftarWaiting->refresh();
        $this->assertEquals(StatusPendaftaranEnum::Ditolak, $pendaftarWaiting->status);
    }

    /**
     * Uji NFR-O1: Healthcheck Endpoint.
     */
    public function test_healthcheck_endpoint(): void
    {
        $response = $this->get(route('health'));

        $response->assertOk()
            ->assertJson([
                'status' => 'ok',
                'database' => true,
                'storage' => true,
            ]);
    }
}
