<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JalurEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusPenempatanEnum;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\Penempatan;
use App\Models\SlotKhusus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminPenempatanJalurKhususTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $pembimbing;

    protected Bidang $bidang;

    protected SlotKhusus $slotKhusus;

    protected BulanMagang $bulanMagang;

    protected Jurusan $jurusan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bidang = Bidang::create([
            'nama_bidang' => 'Layanan E-Government',
            'kode_bidang' => 'EGOV',
            'aktif' => true,
        ]);

        $this->admin = User::factory()->create([
            'nama' => 'Admin Magang Sleman',
            'email' => 'admin@magkom.go.id',
            'role' => RoleEnum::Admin,
            'status_aktif' => true,
            'must_change_password' => false,
        ]);

        $this->pembimbing = User::factory()->create([
            'nama' => 'Budi Mentor',
            'role' => RoleEnum::Pembimbing,
            'status_aktif' => true,
            'bidang_id' => $this->bidang->id,
            'must_change_password' => false,
        ]);

        $this->jurusan = Jurusan::create([
            'nama_jurusan' => 'Teknik Informatika',
            'kategori' => 'mahasiswa',
            'aktif' => true,
        ]);

        $this->bulanMagang = BulanMagang::create([
            'nama' => 'Magang November 2026',
            'bulan_mulai' => '2026-11-01',
            'tgl_buka' => '2026-10-01',
            'tgl_tutup' => '2026-10-25',
            'status' => 'dibuka',
        ]);

        $this->slotKhusus = SlotKhusus::create([
            'bulan_mulai' => '2026-11-01',
            'jumlah' => 5,
            'terisi' => 0,
            'aktif' => true,
            'dibuat_oleh' => $this->admin->id,
        ]);
    }

    public function test_admin_can_place_new_participant_via_jalur_khusus_using_dates(): void
    {
        $payload = [
            'mode' => 'baru',
            'jalur' => 'khusus',
            'nama' => 'Ahmad Siswa Khusus',
            'email' => 'ahmad.khusus@smk.sch.id',
            'nim_nisn' => '1234567890',
            'institusi' => 'SMK Negeri 2 Depok Sleman',
            'jurusan_id' => $this->jurusan->id,
            'jenjang' => 'smk',
            'slot_khusus_id' => $this->slotKhusus->id,
            'pembimbing_id' => $this->pembimbing->id,
            'bidang_id' => $this->bidang->id,
            'tgl_mulai' => '2026-11-01',
            'tgl_selesai' => '2027-01-31',
            'password' => 'Magkom2026!',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.penempatan.manual'), $payload);

        $response->assertRedirect(route('admin.penempatan.index'));
        $response->assertSessionHas('success');
        $response->assertSessionHas('akun_baru_dibuat', true);
        $response->assertSessionHas('password_baru', 'Magkom2026!');
        $response->assertSessionHas('email_peserta', 'ahmad.khusus@smk.sch.id');

        // Akun peserta baru tersimpan
        $user = User::where('email', 'ahmad.khusus@smk.sch.id')->first();
        $this->assertNotNull($user);
        $this->assertSame('Ahmad Siswa Khusus', $user->nama);
        $this->assertSame(RoleEnum::Peserta, $user->role);
        $this->assertTrue($user->status_aktif);
        $this->assertTrue($user->must_change_password);
        $this->assertTrue(Hash::check('Magkom2026!', $user->password));

        // Profil peserta terbentuk
        $this->assertDatabaseHas('peserta_profil', [
            'user_id' => $user->id,
            'nim_nisn' => '1234567890',
            'institusi' => 'SMK Negeri 2 Depok Sleman',
        ]);

        // Entri penempatan tersimpan
        $this->assertDatabaseHas('penempatan', [
            'peserta_id' => $user->id,
            'pembimbing_id' => $this->pembimbing->id,
            'bidang_id' => $this->bidang->id,
            'slot_khusus_id' => $this->slotKhusus->id,
            'jalur' => JalurEnum::Khusus->value,
            'status' => StatusPenempatanEnum::Aktif->value,
        ]);

        // Kapasitas slot khusus bertambah
        $this->assertSame(1, $this->slotKhusus->fresh()->terisi);
    }

    public function test_new_participant_can_login_with_default_password_and_is_forced_to_change_password(): void
    {
        // Tempatkan peserta baru
        $resp = $this->actingAs($this->admin)->post(route('admin.penempatan.manual'), [
            'mode' => 'baru',
            'jalur' => 'khusus',
            'nama' => 'Bintang Peserta',
            'email' => 'bintang@kampus.ac.id',
            'nim_nisn' => '99887766',
            'institusi' => 'Universitas Negeri Yogyakarta',
            'jurusan_id' => $this->jurusan->id,
            'jenjang' => 's1',
            'slot_khusus_id' => $this->slotKhusus->id,
            'pembimbing_id' => $this->pembimbing->id,
            'tgl_mulai' => '2026-11-01',
            'tgl_selesai' => '2027-01-31',
        ]);
        $resp->assertSessionHasNoErrors();
        $resp->assertRedirect(route('admin.penempatan.index'));

        // Peserta login dengan password default Magkom2026!
        $loginResponse = $this->post(route('login'), [
            'email' => 'bintang@kampus.ac.id',
            'password' => 'Magkom2026!',
        ]);

        $this->assertAuthenticated();

        $peserta = User::where('email', 'bintang@kampus.ac.id')->first();

        // Mengakses dashboard diarahkan wajib ganti password
        $accessResponse = $this->actingAs($peserta)->get(route('peserta.dashboard'));
        $accessResponse->assertRedirect(route('password.ganti'));

        // Peserta berhasil mengganti password
        $changePasswordResponse = $this->actingAs($peserta)->post(route('password.ganti.update'), [
            'current_password' => 'Magkom2026!',
            'password' => 'RahasiaBaru2026!',
            'password_confirmation' => 'RahasiaBaru2026!',
        ]);

        $changePasswordResponse->assertSessionHas('success');
        $this->assertFalse($peserta->fresh()->must_change_password);
        $this->assertTrue(Hash::check('RahasiaBaru2026!', $peserta->fresh()->password));
    }

    public function test_jalur_khusus_appears_and_can_be_filtered_by_bulan_magang(): void
    {
        $peserta = User::factory()->create([
            'nama' => 'Peserta Khusus November',
            'email' => 'khusus.nov@gmail.com',
            'role' => RoleEnum::Peserta,
            'status_aktif' => true,
        ]);

        Penempatan::create([
            'pendaftaran_id' => null,
            'peserta_id' => $peserta->id,
            'kuota_id' => null,
            'slot_khusus_id' => $this->slotKhusus->id,
            'bidang_id' => $this->bidang->id,
            'pembimbing_id' => $this->pembimbing->id,
            'jalur' => JalurEnum::Khusus,
            'sumber' => 'manual',
            'tgl_mulai' => '2026-11-01',
            'tgl_selesai' => '2027-01-31',
            'status' => StatusPenempatanEnum::Aktif,
        ]);

        // Bulan lain Desember
        $bulanDesember = BulanMagang::create([
            'nama' => 'Magang Desember 2026',
            'bulan_mulai' => '2026-12-01',
            'tgl_buka' => '2026-11-01',
            'tgl_tutup' => '2026-11-25',
            'status' => 'dibuka',
        ]);

        // Filter November: harus muncul
        $responseNov = $this->actingAs($this->admin)
            ->get(route('admin.penempatan.index', ['bulan_magang_id' => $this->bulanMagang->id]));
        $responseNov->assertOk();
        $responseNov->assertSee('Peserta Khusus November');
        $responseNov->assertSee('Jalur Khusus');

        // Filter Desember: tidak boleh muncul
        $responseDes = $this->actingAs($this->admin)
            ->get(route('admin.penempatan.index', ['bulan_magang_id' => $bulanDesember->id]));
        $responseDes->assertOk();
        $responseDes->assertDontSee('Peserta Khusus November');
    }

    public function test_validation_rejects_duplicate_email_for_new_participant(): void
    {
        User::factory()->create([
            'email' => 'duplikat@example.com',
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.penempatan.manual'), [
            'mode' => 'baru',
            'nama' => 'Peserta Ganda',
            'email' => 'duplikat@example.com',
            'nim_nisn' => '11223344',
            'institusi' => 'UGM',
            'jenjang' => 's1',
            'slot_khusus_id' => $this->slotKhusus->id,
            'pembimbing_id' => $this->pembimbing->id,
            'tgl_mulai' => '2026-11-01',
            'tgl_selesai' => '2027-01-31',
        ]);

        $response->assertSessionHasErrors(['email']);
    }
}
