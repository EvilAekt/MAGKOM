<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Enums\StatusPenempatanEnum;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\Kuota;
use App\Models\Pendaftaran;
use App\Models\Penempatan;
use App\Models\SlotKhusus;
use App\Models\User;
use App\Services\PermissionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperAdminModuleEnhancementTest extends TestCase
{
    use RefreshDatabase;

    private User $superAdmin;

    private User $admin;

    private User $pembimbing;

    private Bidang $bidang;

    private Jurusan $jurusan;

    private BulanMagang $bulanMagang;

    protected function setUp(): void
    {
        parent::setUp();

        app(PermissionService::class)->provisionDefaultModules();

        $this->bidang = Bidang::create([
            'nama_bidang' => 'Bidang Aplikasi Informatika',
            'kode_bidang' => 'APTIKA',
            'deskripsi' => 'Pengembangan aplikasi dan tata kelola e-gov',
            'aktif' => true,
        ]);

        $this->jurusan = Jurusan::create([
            'nama_jurusan' => 'Teknik Informatika',
            'kategori' => 'mahasiswa',
            'bidang_id' => $this->bidang->id,
            'aktif' => true,
        ]);

        $this->superAdmin = User::factory()->create([
            'nama' => 'Super Administrator MagKom',
            'email' => 'superadmin@slemankab.go.id',
            'role' => RoleEnum::SuperAdmin,
            'email_verified_at' => now(),
            'status_aktif' => true,
            'must_change_password' => false,
        ]);

        $this->admin = User::factory()->create([
            'nama' => 'Admin Verifikator Dinas',
            'email' => 'admin@slemankab.go.id',
            'role' => RoleEnum::Admin,
            'email_verified_at' => now(),
            'status_aktif' => true,
            'must_change_password' => false,
        ]);

        $this->pembimbing = User::factory()->create([
            'nama' => 'Pembimbing ASN Kominfo',
            'email' => 'pembimbing@slemankab.go.id',
            'role' => RoleEnum::Pembimbing,
            'bidang_id' => $this->bidang->id,
            'email_verified_at' => now(),
            'status_aktif' => true,
            'must_change_password' => false,
        ]);

        $this->bulanMagang = BulanMagang::create([
            'bulan_mulai' => '2026-11',
            'nama_bulan' => 'November 2026',
            'tgl_buka' => '2026-10-01',
            'tgl_tutup' => '2026-10-25',
            'aktif' => true,
        ]);
    }

    /**
     * TASK A: Super Admin harus strictly read-only pada modul operasional Admin (HTTP 403 untuk POST/PUT/DELETE).
     */
    public function test_superadmin_forbidden_to_execute_mutation_actions_on_admin_module(): void
    {
        $pesertaUser = User::factory()->create([
            'nama' => 'Calon Peserta Magang',
            'email' => 'peserta@kampus.ac.id',
            'role' => RoleEnum::Peserta,
            'email_verified_at' => now(),
            'status_aktif' => true,
            'must_change_password' => false,
        ]);

        $kuota = Kuota::create([
            'bulan_magang_id' => $this->bulanMagang->id,
            'jurusan_id' => $this->jurusan->id,
            'jumlah' => 5,
            'terisi' => 0,
            'aktif' => true,
        ]);

        $pendaftaran = Pendaftaran::create([
            'user_id' => $pesertaUser->id,
            'bulan_magang_id' => $this->bulanMagang->id,
            'kuota_id' => $kuota->id,
            'jalur' => 'umum',
            'status' => 'diajukan',
            'durasi_bulan' => 3,
        ]);

        // 1. SuperAdmin mencoba memutuskan verifikasi pendaftaran -> 403
        $responsePutuskan = $this->actingAs($this->superAdmin)
            ->post(route('admin.pendaftaran.putuskan', $pendaftaran), [
                'keputusan' => 'terima',
                'catatan' => 'Verifikasi oleh superadmin tidak diizinkan',
            ]);
        $responsePutuskan->assertStatus(403);

        // 2. SuperAdmin mencoba menambahkan kuota -> 403
        $responseKuota = $this->actingAs($this->superAdmin)
            ->post(route('admin.kuota.store'), [
                'bulan_magang_id' => $this->bulanMagang->id,
                'jurusan_id' => $this->jurusan->id,
                'jumlah' => 10,
            ]);
        $responseKuota->assertStatus(403);

        // 3. SuperAdmin tetap dapat melihat (read-only) halaman operasional Admin -> 200 OK
        $this->actingAs($this->superAdmin)->get(route('admin.pendaftaran.index'))->assertOk();
        $this->actingAs($this->superAdmin)->get(route('admin.pendaftaran.show', $pendaftaran))->assertOk();
        $this->actingAs($this->superAdmin)->get(route('admin.kuota.index'))->assertOk();
        $this->actingAs($this->superAdmin)->get(route('admin.penempatan.index'))->assertOk();
    }

    /**
     * TASK B: Form tambah staf baru - Admin Verifikator TIDAK memerlukan bidang unit kerja (nullable).
     */
    public function test_superadmin_can_create_admin_verifikator_without_bidang_unit_kerja(): void
    {
        // 1. Buat Admin Verifikator tanpa bidang_id -> Berhasil
        $responseAdmin = $this->actingAs($this->superAdmin)
            ->post(route('superadmin.staf.store'), [
                'nama' => 'Staf Verifikator Baru',
                'email' => 'verifikator.baru@slemankab.go.id',
                'role' => 'admin',
                'bidang_id' => '', // kosong / unselected
                'password' => 'SandiKuat123!',
            ]);

        $responseAdmin->assertRedirect(route('superadmin.staf.index'));
        $responseAdmin->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'email' => 'verifikator.baru@slemankab.go.id',
            'role' => RoleEnum::Admin->value,
            'bidang_id' => null,
        ]);

        // 2. Coba buat Pembimbing tanpa bidang_id -> Ditolak validasi
        $responsePembimbingFail = $this->actingAs($this->superAdmin)
            ->post(route('superadmin.staf.store'), [
                'nama' => 'Staf Pembimbing Gagal',
                'email' => 'pembimbing.gagal@slemankab.go.id',
                'role' => 'pembimbing',
                'bidang_id' => '',
                'password' => 'SandiKuat123!',
            ]);

        $responsePembimbingFail->assertSessionHasErrors('bidang_id');

        // 3. Buat Pembimbing dengan bidang_id -> Berhasil
        $responsePembimbingSuccess = $this->actingAs($this->superAdmin)
            ->post(route('superadmin.staf.store'), [
                'nama' => 'Staf Pembimbing Sukses',
                'email' => 'pembimbing.sukses@slemankab.go.id',
                'role' => 'pembimbing',
                'bidang_id' => $this->bidang->id,
                'password' => 'SandiKuat123!',
            ]);

        $responsePembimbingSuccess->assertRedirect(route('superadmin.staf.index'));
        $this->assertDatabaseHas('users', [
            'email' => 'pembimbing.sukses@slemankab.go.id',
            'role' => RoleEnum::Pembimbing->value,
            'bidang_id' => $this->bidang->id,
        ]);
    }

    /**
     * TASK C: Fitur rubrik nilai dinonaktifkan dari panel Super Admin, tanpa merusak histori penilaian.
     */
    public function test_rubrik_crud_route_is_removed_and_dashboard_does_not_show_rubrik_menu(): void
    {
        // 1. Rute superadmin rubrik tidak lagi ditemukan (404)
        $response = $this->actingAs($this->superAdmin)->get('/superadmin/rubrik');
        $response->assertStatus(404);

        // 2. Navigasi dashboard SuperAdmin tidak menampilkan tautan Rubrik Nilai
        $dashboardResponse = $this->actingAs($this->superAdmin)->get(route('superadmin.dashboard'));
        $dashboardResponse->assertOk();
        $dashboardResponse->assertDontSee('/superadmin/rubrik');
    }

    /**
     * TASK D: Super Admin dapat mendaftarkan peserta magang jalur khusus secara langsung aktif.
     */
    public function test_superadmin_can_create_peserta_jalur_khusus_directly_active(): void
    {
        $slotKhusus = SlotKhusus::create([
            'nama_slot' => 'Slot Kemitraan Kampus Merdeka',
            'kapasitas' => 5,
            'bulan_mulai' => '2026-11',
            'deskripsi' => 'Alokasi khusus batch November',
            'aktif' => true,
            'dibuat_oleh' => $this->superAdmin->id,
        ]);

        $createForm = $this->actingAs($this->admin)->get(route('admin.peserta-khusus.create'));
        $createForm->assertOk();
        $createForm->assertSee('Pendaftaran Peserta Jalur Khusus');

        $responseStore = $this->actingAs($this->admin)
            ->post(route('admin.peserta-khusus.store'), [
                'nama' => 'Arya Wiguna Putra',
                'email' => 'arya.wiguna@ugm.ac.id',
                'password' => 'Magkom2026!',
                'nim_nisn' => '22/492100/TK/54321',
                'institusi' => 'Universitas Gadjah Mada',
                'jurusan_id' => $this->jurusan->id,
                'jenjang' => 's1',
                'no_hp' => '08122334455',
                'bidang_id' => $this->bidang->id,
                'pembimbing_id' => $this->pembimbing->id,
                'bulan_mulai' => '2026-11',
                'durasi_bulan' => 3,
                'slot_khusus_id' => $slotKhusus->id,
                'catatan' => 'Peserta Rekomendasi Khusus Kampus',
            ]);

        $responseStore->assertRedirect(route('admin.slot-khusus.index'));
        $responseStore->assertSessionHas('success');

        // Pastikan akun user dibuat dengan role Peserta
        $this->assertDatabaseHas('users', [
            'email' => 'arya.wiguna@ugm.ac.id',
            'role' => RoleEnum::Peserta->value,
            'status_aktif' => true,
        ]);

        $createdUser = User::where('email', 'arya.wiguna@ugm.ac.id')->firstOrFail();

        // Pastikan profil peserta tersimpan lengkap
        $this->assertDatabaseHas('peserta_profil', [
            'user_id' => $createdUser->id,
            'nim_nisn' => '22/492100/TK/54321',
            'institusi' => 'Universitas Gadjah Mada',
            'jurusan_id' => $this->jurusan->id,
            'jenjang' => 's1',
        ]);

        // Pastikan penempatan berstatus AKTIF dan berjalur KHUSUS
        $this->assertDatabaseHas('penempatan', [
            'peserta_id' => $createdUser->id,
            'pembimbing_id' => $this->pembimbing->id,
            'bidang_id' => $this->bidang->id,
            'slot_khusus_id' => $slotKhusus->id,
            'jalur' => 'khusus',
            'sumber' => 'manual',
            'status' => StatusPenempatanEnum::Aktif->value,
        ]);
    }

    /**
     * TASK E: CMS Pengelolaan Landing Page (Teks, Banner Foto, dan File PDF Panduan).
     */
    public function test_superadmin_landing_cms_index_redirects_to_canvas_site_editor(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('superadmin.landing.index'));
        $response->assertRedirect(route('superadmin.site-editor.index'));
        $response->assertSessionHas('info');
    }

    /**
     * FAILURE LOGIC AUDIT - TASK A: Mutasi via JSON / AJAX oleh Super Admin wajib ditolak 403 dengan format JSON.
     */
    public function test_superadmin_forbidden_json_mutation_returns_403_json(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->postJson(route('admin.kuota.store'), [
                'bulan_magang_id' => $this->bulanMagang->id,
                'jurusan_id' => $this->jurusan->id,
                'jumlah' => 10,
            ]);

        $response->assertStatus(403);
        $response->assertJson([
            'status' => 'error',
            'message' => 'SuperAdmin hanya memiliki hak akses lihat (read-only) pada modul operasional role lain.',
        ]);
    }

    /**
     * FAILURE LOGIC AUDIT - TASK D: Super Admin dapat membuat peserta jalur khusus tanpa slot khusus spesifik (slot_khusus_id = null).
     */
    public function test_superadmin_can_create_peserta_jalur_khusus_without_slot_khusus(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.peserta-khusus.store'), [
                'nama' => 'Budi Santoso Jalur Bebas',
                'email' => 'budi.bebas@ugm.ac.id',
                'nim_nisn' => '22/999888/SV/00001',
                'institusi' => 'Sekolah Vokasi UGM',
                'jurusan_id' => $this->jurusan->id,
                'jenjang' => 'd3',
                'no_hp' => '081234567899',
                'bidang_id' => $this->bidang->id,
                'pembimbing_id' => $this->pembimbing->id,
                'bulan_mulai' => '2026-11',
                'durasi_bulan' => 3,
                'slot_khusus_id' => '', // Empty / null
                'catatan' => 'Tanpa slot khusus spesifik',
            ]);

        $response->assertRedirect(route('admin.slot-khusus.index'));
        $response->assertSessionHas('success');

        $createdUser = User::where('email', 'budi.bebas@ugm.ac.id')->firstOrFail();
        $this->assertDatabaseHas('penempatan', [
            'peserta_id' => $createdUser->id,
            'slot_khusus_id' => null,
            'jalur' => 'khusus',
            'status' => StatusPenempatanEnum::Aktif->value,
        ]);
    }

    /**
     * FAILURE LOGIC AUDIT - TASK D: Validasi menolak pembuatan peserta jika NIM/NISN sudah ada di sistem.
     */
    public function test_superadmin_cannot_create_peserta_with_duplicate_nim(): void
    {
        // Daftarkan peserta pertama dengan NIM tertentu
        $existingPeserta = User::factory()->create([
            'email' => 'existing.peserta@ugm.ac.id',
            'role' => RoleEnum::Peserta,
            'status_aktif' => true,
        ]);
        $existingPeserta->profil()->create([
            'nim_nisn' => '1234567890',
            'institusi' => 'Universitas Indonesia',
            'jurusan_id' => $this->jurusan->id,
            'jenjang' => 's1',
            'no_hp' => '0811111111',
        ]);

        // Coba daftarkan peserta baru dengan NIM yang sama
        $response = $this->actingAs($this->admin)
            ->post(route('admin.peserta-khusus.store'), [
                'nama' => 'Peserta Kloning NIM',
                'email' => 'peserta.kloning@ugm.ac.id',
                'nim_nisn' => '1234567890', // Duplikat
                'institusi' => 'Universitas Gadjah Mada',
                'jurusan_id' => $this->jurusan->id,
                'jenjang' => 's1',
                'pembimbing_id' => $this->pembimbing->id,
                'bulan_mulai' => '2026-11',
                'durasi_bulan' => 3,
            ]);

        $response->assertSessionHasErrors(['nim_nisn']);
        $this->assertDatabaseMissing('users', ['email' => 'peserta.kloning@ugm.ac.id']);
    }

    /**
     * FAILURE LOGIC AUDIT - TASK D: Validasi menolak jika pembimbing yang dipilih tidak aktif atau bukan role pembimbing.
     */
    public function test_superadmin_cannot_create_peserta_with_inactive_or_non_pembimbing(): void
    {
        $inactivePembimbing = User::factory()->create([
            'role' => RoleEnum::Pembimbing,
            'status_aktif' => false, // Non-aktif
        ]);

        $pesertaUser = User::factory()->create([
            'role' => RoleEnum::Peserta,
            'status_aktif' => true,
        ]);

        // Coba pilih pembimbing non-aktif
        $responseInactive = $this->actingAs($this->admin)
            ->post(route('admin.peserta-khusus.store'), [
                'nama' => 'Tes Pembimbing Non-Aktif',
                'email' => 'tes.inactive@example.com',
                'nim_nisn' => 'NIM-INACTIVE-01',
                'institusi' => 'Kampus Sleman',
                'jurusan_id' => $this->jurusan->id,
                'jenjang' => 's1',
                'pembimbing_id' => $inactivePembimbing->id,
                'bulan_mulai' => '2026-11',
                'durasi_bulan' => 3,
            ]);
        $responseInactive->assertSessionHasErrors(['pembimbing_id']);

        // Coba pilih user yang bukan role pembimbing
        $responseWrongRole = $this->actingAs($this->admin)
            ->post(route('admin.peserta-khusus.store'), [
                'nama' => 'Tes Role Salah',
                'email' => 'tes.wrongrole@example.com',
                'nim_nisn' => 'NIM-WRONG-01',
                'institusi' => 'Kampus Sleman',
                'jurusan_id' => $this->jurusan->id,
                'jenjang' => 's1',
                'pembimbing_id' => $pesertaUser->id,
                'bulan_mulai' => '2026-11',
                'durasi_bulan' => 3,
            ]);
        $responseWrongRole->assertSessionHasErrors(['pembimbing_id']);
    }

    /**
     * FAILURE LOGIC AUDIT - TASK B: Validasi menolak pembuatan staf baru dengan email duplikat atau password lemah.
     */
    public function test_superadmin_cannot_create_staf_with_weak_password_or_duplicate_email(): void
    {
        // Email duplikat
        $responseDuplicate = $this->actingAs($this->superAdmin)
            ->post(route('superadmin.staf.store'), [
                'nama' => 'Duplikat Admin',
                'email' => $this->admin->email, // Duplikat
                'role' => 'admin',
            ]);
        $responseDuplicate->assertSessionHasErrors(['email']);

        // Password lemah (< 8 karakter atau tanpa angka)
        $responseWeakPassword = $this->actingAs($this->superAdmin)
            ->post(route('superadmin.staf.store'), [
                'nama' => 'Staf Password Lemah',
                'email' => 'weak.password@slemankab.go.id',
                'role' => 'admin',
                'password' => 'hanyahuruf', // Tanpa angka
            ]);
        $responseWeakPassword->assertSessionHasErrors(['password']);
    }
}
