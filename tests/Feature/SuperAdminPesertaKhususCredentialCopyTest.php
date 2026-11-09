<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Models\Bidang;
use App\Models\Jurusan;
use App\Models\SlotKhusus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SuperAdminPesertaKhususCredentialCopyTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected User $pembimbing;

    protected Bidang $bidang;

    protected Jurusan $jurusan;

    protected SlotKhusus $slotKhusus;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superAdmin = User::factory()->create([
            'role' => RoleEnum::SuperAdmin,
            'status_aktif' => true,
        ]);

        $this->bidang = Bidang::create([
            'nama_bidang' => 'Aplikasi Informatika & E-Government (EGOV)',
            'aktif' => true,
        ]);

        $this->pembimbing = User::factory()->create([
            'role' => RoleEnum::Pembimbing,
            'bidang_id' => $this->bidang->id,
            'status_aktif' => true,
        ]);

        $this->jurusan = Jurusan::create([
            'nama_jurusan' => 'Teknik Informatika',
            'kategori' => 'mahasiswa',
            'aktif' => true,
        ]);

        $this->slotKhusus = SlotKhusus::create([
            'bulan_mulai' => now()->startOfMonth()->toDateString(),
            'jumlah' => 5,
            'terisi' => 0,
            'aktif' => true,
            'dibuat_oleh' => $this->superAdmin->id,
        ]);
    }

    public function test_superadmin_can_register_peserta_jalur_khusus_and_password_awal_is_saved_encrypted(): void
    {
        $plainPassword = 'Mgk-Secret9988!';

        $response = $this->actingAs($this->superAdmin)->post(route('superadmin.peserta-khusus.store'), [
            'nama' => 'Rian Kurniawan',
            'email' => 'rian.kurniawan@ugm.ac.id',
            'password' => $plainPassword,
            'nim_nisn' => '22/499888/TK/55123',
            'institusi' => 'Universitas Gadjah Mada',
            'jurusan_id' => $this->jurusan->id,
            'jenjang' => 's1',
            'no_hp' => '081234567890',
            'bidang_id' => $this->bidang->id,
            'pembimbing_id' => $this->pembimbing->id,
            'bulan_mulai' => now()->format('Y-m'),
            'durasi_bulan' => 3,
            'slot_khusus_id' => $this->slotKhusus->id,
            'catatan' => 'Peserta Kemitraan Kampus Merdeka Mandiri',
        ]);

        $response->assertRedirect(route('superadmin.slot-khusus.index'));
        $response->assertSessionHas('success');
        $response->assertSessionHas('akun_baru_dibuat', true);
        $response->assertSessionHas('password_baru', $plainPassword);

        $peserta = User::where('email', 'rian.kurniawan@ugm.ac.id')->first();
        $this->assertNotNull($peserta);
        $this->assertTrue($peserta->must_change_password);

        // Verifikasi bahwa data di database mentah tersimpan dalam bentuk terenkripsi (bukan plaintext)
        $rawPasswordAwal = DB::table('users')->where('id', $peserta->id)->value('password_awal');
        $this->assertNotEmpty($rawPasswordAwal);
        $this->assertNotEquals($plainPassword, $rawPasswordAwal);

        // Verifikasi bahwa Eloquent attribute casting 'encrypted' mendekripsi kembali ke plaintext asli
        $this->assertSame($plainPassword, $peserta->fresh()->password_awal);
    }

    public function test_initial_password_can_be_used_for_first_login_and_user_is_forced_to_change_password(): void
    {
        $plainPassword = 'Mgk-AwalLogin123!';

        $this->actingAs($this->superAdmin)->post(route('superadmin.peserta-khusus.store'), [
            'nama' => 'Siti Aisyah',
            'email' => 'siti.aisyah@kampus.ac.id',
            'password' => $plainPassword,
            'nim_nisn' => '22/499777/TK/55456',
            'institusi' => 'Universitas Gadjah Mada',
            'jurusan_id' => $this->jurusan->id,
            'jenjang' => 's1',
            'no_hp' => '081234567891',
            'bidang_id' => $this->bidang->id,
            'pembimbing_id' => $this->pembimbing->id,
            'bulan_mulai' => now()->format('Y-m'),
            'durasi_bulan' => 3,
            'slot_khusus_id' => $this->slotKhusus->id,
        ]);

        // Peserta login dengan kata sandi awal
        $loginResp = $this->post(route('login'), [
            'email' => 'siti.aisyah@kampus.ac.id',
            'password' => $plainPassword,
        ]);

        $this->assertAuthenticated();

        $peserta = User::where('email', 'siti.aisyah@kampus.ac.id')->first();

        // Mengakses dashboard diarahkan wajib ganti password (BR-15)
        $dashboardResp = $this->actingAs($peserta)->get(route('peserta.dashboard'));
        $dashboardResp->assertRedirect(route('password.ganti'));
    }

    public function test_after_password_is_changed_initial_password_cannot_be_used_to_login(): void
    {
        $initialPassword = 'Mgk-InitialPass123!';
        $newPassword = 'SandiBaruPribadi2026!';

        $this->actingAs($this->superAdmin)->post(route('superadmin.peserta-khusus.store'), [
            'nama' => 'Bagus Prasetyo',
            'email' => 'bagus.prasetyo@kampus.ac.id',
            'password' => $initialPassword,
            'nim_nisn' => '22/499666/TK/55789',
            'institusi' => 'Universitas Gadjah Mada',
            'jurusan_id' => $this->jurusan->id,
            'jenjang' => 's1',
            'no_hp' => '081234567892',
            'bidang_id' => $this->bidang->id,
            'pembimbing_id' => $this->pembimbing->id,
            'bulan_mulai' => now()->format('Y-m'),
            'durasi_bulan' => 3,
            'slot_khusus_id' => $this->slotKhusus->id,
        ]);

        $peserta = User::where('email', 'bagus.prasetyo@kampus.ac.id')->first();

        // Peserta mengganti kata sandi secara mandiri
        $changeResp = $this->actingAs($peserta)->post(route('password.ganti.update'), [
            'current_password' => $initialPassword,
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ]);

        $changeResp->assertSessionHas('success');
        $this->assertFalse($peserta->fresh()->must_change_password);

        // Logout
        $this->post(route('logout'));
        $this->assertGuest();

        // 1. Percobaan login menggunakan kata sandi awal LAMA -> DITOLAK
        $failedLogin = $this->post(route('login'), [
            'email' => 'bagus.prasetyo@kampus.ac.id',
            'password' => $initialPassword,
        ]);

        $this->assertGuest();
        $failedLogin->assertSessionHasErrors();

        // 2. Percobaan login menggunakan kata sandi BARU -> BERHASIL
        $successLogin = $this->post(route('login'), [
            'email' => 'bagus.prasetyo@kampus.ac.id',
            'password' => $newPassword,
        ]);

        $this->assertAuthenticatedAs($peserta->fresh());
    }

    public function test_slot_khusus_index_renders_credential_copy_data_and_status(): void
    {
        $plainPassword = 'Mgk-TestingCopy123!';

        $this->actingAs($this->superAdmin)->post(route('superadmin.peserta-khusus.store'), [
            'nama' => 'Dewi Lestari',
            'email' => 'dewi.lestari@kampus.ac.id',
            'password' => $plainPassword,
            'nim_nisn' => '22/499555/TK/55999',
            'institusi' => 'Universitas Gadjah Mada',
            'jurusan_id' => $this->jurusan->id,
            'jenjang' => 's1',
            'no_hp' => '081234567893',
            'bidang_id' => $this->bidang->id,
            'pembimbing_id' => $this->pembimbing->id,
            'bulan_mulai' => now()->format('Y-m'),
            'durasi_bulan' => 3,
            'slot_khusus_id' => $this->slotKhusus->id,
        ]);

        $indexResp = $this->actingAs($this->superAdmin)->get(route('superadmin.slot-khusus.index'));
        $indexResp->assertOk();

        // Memastikan tombol salin kredensial awal dirender dengan data attribute yang benar
        $indexResp->assertSee('sa-btn-copy-kredensial', false);
        $indexResp->assertSee('Dewi Lestari');
        $indexResp->assertSee('dewi.lestari@kampus.ac.id');
        $indexResp->assertSee('Salin Akun');
        $indexResp->assertSee('Sandi Awal Aktif');
    }
}
