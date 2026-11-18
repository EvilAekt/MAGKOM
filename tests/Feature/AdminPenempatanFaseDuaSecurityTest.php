<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Actions\Admin\Penempatan\AssignPesertaAction;
use App\Actions\Admin\Penempatan\CancelPenempatanAction;
use App\Actions\Admin\Penempatan\CreateManualPenempatanAction;
use App\Actions\Admin\Penempatan\ReassignPembimbingAction;
use App\Enums\JalurEnum;
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
use App\Models\Pengaturan;
use App\Models\SlotKhusus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminPenempatanFaseDuaSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $pembimbing;

    protected Bidang $bidang;

    protected BulanMagang $bulan;

    protected Jurusan $jurusan;

    protected Kuota $kuota;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bidang = Bidang::create(['nama_bidang' => 'Bidang IKP', 'aktif' => true]);
        $this->jurusan = Jurusan::create(['nama_jurusan' => 'Informatika', 'kategori' => 'mahasiswa', 'aktif' => true]);

        $this->bulan = BulanMagang::create([
            'nama_bulan' => 'Januari 2027',
            'bulan_mulai' => '2027-01-01',
            'tgl_buka' => '2026-12-01',
            'tgl_tutup' => '2026-12-25',
            'aktif' => true,
        ]);

        $this->kuota = Kuota::create([
            'bulan_magang_id' => $this->bulan->id,
            'jurusan_id' => $this->jurusan->id,
            'jumlah' => 10,
            'terisi' => 0,
        ]);

        $this->admin = User::forceCreate([
            'nama' => 'Admin Test',
            'email' => 'admin_test@magkom.test',
            'password' => Hash::make('Password123!'),
            'role' => RoleEnum::Admin,
            'status_aktif' => true,
            'email_verified_at' => now(),
        ]);

        $this->pembimbing = User::forceCreate([
            'nama' => 'Pembimbing Test',
            'email' => 'pembimbing_test@magkom.test',
            'password' => Hash::make('Password123!'),
            'role' => RoleEnum::Pembimbing,
            'bidang_id' => $this->bidang->id,
            'status_aktif' => true,
            'email_verified_at' => now(),
        ]);
    }

    public function test_assign_peserta_action_enforces_mentor_capacity_limit(): void
    {
        Pengaturan::setNilai('maks_peserta_aktif_per_pembimbing', '1');

        $peserta1 = User::forceCreate([
            'nama' => 'Peserta Satu',
            'email' => 'peserta1@magkom.test',
            'password' => Hash::make('Password123!'),
            'role' => RoleEnum::Peserta,
            'status_aktif' => true,
            'email_verified_at' => now(),
        ]);

        $pendaftaran1 = Pendaftaran::create([
            'nomor_pendaftaran' => 'REG-001',
            'user_id' => $peserta1->id,
            'kuota_id' => $this->kuota->id,
            'bulan_magang_id' => $this->bulan->id,
            'durasi_bulan' => 1,
            'status' => StatusPendaftaranEnum::Diterima,
            'tgl_daftar' => now(),
        ]);

        $action = app(AssignPesertaAction::class);
        $penempatan1 = $action->execute($pendaftaran1, $this->admin, $this->pembimbing, $this->bidang);
        $this->assertInstanceOf(Penempatan::class, $penempatan1);
        $this->assertEquals(StatusPenempatanEnum::Aktif, $penempatan1->status);

        // Now mentor has 1 active mentee (reaches max limit of 1)
        $peserta2 = User::forceCreate([
            'nama' => 'Peserta Dua',
            'email' => 'peserta2@magkom.test',
            'password' => Hash::make('Password123!'),
            'role' => RoleEnum::Peserta,
            'status_aktif' => true,
            'email_verified_at' => now(),
        ]);

        $pendaftaran2 = Pendaftaran::create([
            'nomor_pendaftaran' => 'REG-002',
            'user_id' => $peserta2->id,
            'kuota_id' => $this->kuota->id,
            'bulan_magang_id' => $this->bulan->id,
            'durasi_bulan' => 1,
            'status' => StatusPendaftaranEnum::Diterima,
            'tgl_daftar' => now(),
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("Pembimbing {$this->pembimbing->nama} telah mencapai batas beban maksimum (1 peserta aktif).");

        $action->execute($pendaftaran2, $this->admin, $this->pembimbing, $this->bidang);
    }

    public function test_reassign_pembimbing_action_enforces_mentor_capacity_limit(): void
    {
        Pengaturan::setNilai('maks_peserta_aktif_per_pembimbing', '1');

        $pembimbingBaru = User::forceCreate([
            'nama' => 'Pembimbing Baru',
            'email' => 'pembimbing_baru@magkom.test',
            'password' => Hash::make('Password123!'),
            'role' => RoleEnum::Pembimbing,
            'bidang_id' => $this->bidang->id,
            'status_aktif' => true,
            'email_verified_at' => now(),
        ]);

        // Berikan pembimbingBaru sudah memiliki 1 mentee aktif
        $pesertaLain = User::forceCreate([
            'nama' => 'Peserta Mentor Lain',
            'email' => 'peserta_lain@magkom.test',
            'password' => Hash::make('Password123!'),
            'role' => RoleEnum::Peserta,
            'status_aktif' => true,
            'email_verified_at' => now(),
        ]);

        Penempatan::create([
            'peserta_id' => $pesertaLain->id,
            'kuota_id' => $this->kuota->id,
            'bidang_id' => $this->bidang->id,
            'pembimbing_id' => $pembimbingBaru->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'dibuat_oleh' => $this->admin->id,
            'tgl_mulai' => '2027-01-01',
            'tgl_selesai' => '2027-01-31',
            'status' => StatusPenempatanEnum::Aktif,
        ]);

        // Buat penempatan di pembimbing lama
        $peserta = User::forceCreate([
            'nama' => 'Peserta Reassign',
            'email' => 'peserta_reassign@magkom.test',
            'password' => Hash::make('Password123!'),
            'role' => RoleEnum::Peserta,
            'status_aktif' => true,
            'email_verified_at' => now(),
        ]);

        $penempatan = Penempatan::create([
            'peserta_id' => $peserta->id,
            'kuota_id' => $this->kuota->id,
            'bidang_id' => $this->bidang->id,
            'pembimbing_id' => $this->pembimbing->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'dibuat_oleh' => $this->admin->id,
            'tgl_mulai' => '2027-01-01',
            'tgl_selesai' => '2027-01-31',
            'status' => StatusPenempatanEnum::Aktif,
        ]);

        $action = app(ReassignPembimbingAction::class);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("Pembimbing {$pembimbingBaru->nama} telah mencapai batas beban maksimum (1 peserta aktif).");

        $action->execute($penempatan, $pembimbingBaru, $this->admin);
    }

    public function test_manual_penempatan_action_enforces_mentor_capacity_limit(): void
    {
        Pengaturan::setNilai('maks_peserta_aktif_per_pembimbing', '1');

        // Buat slot khusus
        $slotKhusus = SlotKhusus::create([
            'bulan_mulai' => '2027-01-01',
            'jumlah' => 5,
            'terisi' => 0,
            'aktif' => true,
            'dibuat_oleh' => $this->admin->id,
        ]);

        // Berikan 1 penempatan aktif ke pembimbing
        $peserta1 = User::forceCreate([
            'nama' => 'Peserta Eksisting',
            'email' => 'peserta_eksisting@magkom.test',
            'password' => Hash::make('Password123!'),
            'role' => RoleEnum::Peserta,
            'status_aktif' => true,
            'email_verified_at' => now(),
        ]);

        Penempatan::create([
            'peserta_id' => $peserta1->id,
            'kuota_id' => $this->kuota->id,
            'bidang_id' => $this->bidang->id,
            'pembimbing_id' => $this->pembimbing->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'dibuat_oleh' => $this->admin->id,
            'tgl_mulai' => '2027-01-01',
            'tgl_selesai' => '2027-01-31',
            'status' => StatusPenempatanEnum::Aktif,
        ]);

        $action = app(CreateManualPenempatanAction::class);

        // Uji executeBaru
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("Pembimbing {$this->pembimbing->nama} telah mencapai batas beban maksimum (1 peserta aktif).");

        $action->executeBaru(
            userData: [
                'nama' => 'Peserta Baru Jalur Khusus',
                'email' => 'baru_khusus@magkom.test',
            ],
            profilData: [
                'nim_nisn' => '12345678',
                'institusi' => 'UGM',
                'jurusan_id' => $this->jurusan->id,
                'jenjang' => 's1',
            ],
            pembimbing: $this->pembimbing,
            bidang: $this->bidang,
            bulanMulai: '2027-01-01',
            durasiBulan: 1,
            jalur: 'khusus',
            slotKhususId: $slotKhusus->id,
            kuotaId: null,
            admin: $this->admin
        );
    }

    public function test_cancel_penempatan_before_start_date_releases_quota_and_keeps_user_active(): void
    {
        $this->kuota->update(['terisi' => 1]);

        $peserta = User::forceCreate([
            'nama' => 'Peserta Batal',
            'email' => 'peserta_batal@magkom.test',
            'password' => Hash::make('Password123!'),
            'role' => RoleEnum::Peserta,
            'status_aktif' => true,
            'email_verified_at' => now(),
        ]);

        $pendaftaran = Pendaftaran::create([
            'nomor_pendaftaran' => 'REG-BATAL-1',
            'user_id' => $peserta->id,
            'kuota_id' => $this->kuota->id,
            'bulan_magang_id' => $this->bulan->id,
            'durasi_bulan' => 1,
            'status' => StatusPendaftaranEnum::Diterima,
            'tgl_daftar' => now(),
        ]);

        $penempatan = Penempatan::create([
            'pendaftaran_id' => $pendaftaran->id,
            'peserta_id' => $peserta->id,
            'kuota_id' => $this->kuota->id,
            'bidang_id' => $this->bidang->id,
            'pembimbing_id' => $this->pembimbing->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'dibuat_oleh' => $this->admin->id,
            'tgl_mulai' => now()->addDays(10)->toDateString(), // Mendatang
            'tgl_selesai' => now()->addDays(40)->toDateString(),
            'status' => StatusPenempatanEnum::Aktif,
        ]);

        $action = app(CancelPenempatanAction::class);
        $result = $action->execute($penempatan, $this->admin, 'Permohonan mandiri');

        $this->assertEquals(StatusPenempatanEnum::MengundurkanDiri, $result->fresh()->status);
        $this->assertEquals(StatusPendaftaranEnum::Dibatalkan, $pendaftaran->fresh()->status);
        $this->assertTrue($peserta->fresh()->status_aktif); // Akun tetap aktif
        $this->assertEquals(0, $this->kuota->fresh()->terisi); // Kuota dikembalikan
    }

    public function test_cancel_penempatan_after_start_date_burns_quota_and_deactivates_user(): void
    {
        $this->kuota->update(['terisi' => 1]);

        $peserta = User::forceCreate([
            'nama' => 'Peserta Drop',
            'email' => 'peserta_drop@magkom.test',
            'password' => Hash::make('Password123!'),
            'role' => RoleEnum::Peserta,
            'status_aktif' => true,
            'email_verified_at' => now(),
        ]);

        $penempatan = Penempatan::create([
            'pendaftaran_id' => null,
            'peserta_id' => $peserta->id,
            'kuota_id' => $this->kuota->id,
            'bidang_id' => $this->bidang->id,
            'pembimbing_id' => $this->pembimbing->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Manual,
            'dibuat_oleh' => $this->admin->id,
            'tgl_mulai' => now()->subDays(5)->toDateString(), // Sudah berjalan
            'tgl_selesai' => now()->addDays(25)->toDateString(),
            'status' => StatusPenempatanEnum::Aktif,
        ]);

        $action = app(CancelPenempatanAction::class);
        $result = $action->execute($penempatan, $this->admin, 'Mengundurkan diri setelah magang dimulai');

        $this->assertEquals(StatusPenempatanEnum::MengundurkanDiri, $result->fresh()->status);
        $this->assertFalse($peserta->fresh()->status_aktif); // Akun dinonaktifkan
        $this->assertEquals(1, $this->kuota->fresh()->terisi); // Kuota hangus (tidak berkurang)
    }

    public function test_password_update_clears_password_awal_field(): void
    {
        $user = User::forceCreate([
            'nama' => 'Staf Baru',
            'email' => 'staf_baru@magkom.test',
            'password' => Hash::make('PasswordAwal123!'),
            'password_awal' => 'PasswordAwal123!',
            'role' => RoleEnum::Admin,
            'status_aktif' => true,
            'must_change_password' => true,
            'email_verified_at' => now(),
        ]);

        $this->actingAs($user);

        $response = $this->post(route('password.ganti.update'), [
            'current_password' => 'PasswordAwal123!',
            'password' => 'PasswordBaru987!',
            'password_confirmation' => 'PasswordBaru987!',
        ]);

        $response->assertRedirect(route('admin.pendaftaran.index'));
        $user->refresh();

        $this->assertFalse($user->must_change_password);
        $this->assertNull($user->password_awal);
        $this->assertTrue(Hash::check('PasswordBaru987!', $user->password));
    }
}
