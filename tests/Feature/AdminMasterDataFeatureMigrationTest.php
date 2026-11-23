<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\SlotKhusus;
use App\Models\User;
use App\Services\PermissionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminMasterDataFeatureMigrationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        app(PermissionService::class)->provisionDefaultModules();

        $this->admin = new User;
        $this->admin->nama = 'Petugas Administrasi';
        $this->admin->email = 'admin.magkom@slemankab.go.id';
        $this->admin->password = 'Password123!';
        $this->admin->role = RoleEnum::Admin;
        $this->admin->status_aktif = true;
        $this->admin->email_verified_at = now();
        $this->admin->save();

        $this->superAdmin = new User;
        $this->superAdmin->nama = 'Super Administrator';
        $this->superAdmin->email = 'superadmin.magkom@slemankab.go.id';
        $this->superAdmin->password = 'Password123!';
        $this->superAdmin->role = RoleEnum::SuperAdmin;
        $this->superAdmin->status_aktif = true;
        $this->superAdmin->email_verified_at = now();
        $this->superAdmin->save();
    }

    /*
     |--------------------------------------------------------------------------
     | 1. Periode Bulan Magang (BulanMagang) Tests
     |--------------------------------------------------------------------------
     */

    public function test_admin_can_access_bulan_index_and_full_crud(): void
    {
        // 1. Index redirects to kuota index with tab=bulan
        $response = $this->actingAs($this->admin)->get(route('admin.bulan.index'));
        $response->assertRedirect(route('admin.kuota.index', ['tab' => 'bulan']));

        $target = $this->actingAs($this->admin)->get(route('admin.kuota.index', ['tab' => 'bulan']));
        $target->assertOk();
        $target->assertSee('Daftar Bulan Magang');
        $target->assertDontSee('Mode Lihat Saja (Read-Only)');

        // 2. Store
        $bulanDate = now()->addMonths(3)->startOfMonth();
        $storeResponse = $this->actingAs($this->admin)->post(route('admin.bulan.store'), [
            'bulan_mulai' => $bulanDate->format('Y-m-d'),
            'tgl_buka' => $bulanDate->copy()->subMonth()->startOfMonth()->format('Y-m-d'),
            'tgl_tutup' => $bulanDate->copy()->subMonth()->endOfMonth()->format('Y-m-d'),
            'override_status' => null,
        ]);
        $storeResponse->assertRedirect(route('admin.kuota.index', ['tab' => 'bulan']));
        $this->assertDatabaseHas('bulan_magang', [
            'aktif' => 1,
        ]);

        $bulan = BulanMagang::firstOrFail();

        // 3. Update
        $updateResponse = $this->actingAs($this->admin)->put(route('admin.bulan.update', $bulan), [
            'bulan_mulai' => $bulanDate->format('Y-m-d'),
            'tgl_buka' => $bulanDate->copy()->subMonth()->startOfMonth()->format('Y-m-d'),
            'tgl_tutup' => $bulanDate->copy()->subMonth()->startOfMonth()->addDays(15)->format('Y-m-d'),
            'override_status' => 'tutup',
        ]);
        $updateResponse->assertRedirect(route('admin.kuota.index', ['tab' => 'bulan']));
        $this->assertDatabaseHas('bulan_magang', [
            'id' => $bulan->id,
            'override_status' => 'tutup',
        ]);

        // 4. Toggle
        $toggleResponse = $this->actingAs($this->admin)->patch(route('admin.bulan.toggle', $bulan));
        $toggleResponse->assertRedirect(route('admin.kuota.index', ['tab' => 'bulan']));
        $this->assertDatabaseHas('bulan_magang', [
            'id' => $bulan->id,
            'aktif' => 0,
        ]);
    }

    public function test_superadmin_has_read_only_access_on_bulan_and_mutations_are_forbidden(): void
    {
        $bulan = BulanMagang::create([
            'bulan_mulai' => now()->addMonth()->startOfMonth()->toDateString(),
            'tgl_buka' => now()->toDateString(),
            'tgl_tutup' => now()->addDays(10)->toDateString(),
            'aktif' => true,
        ]);

        // Index redirects to kuota index with tab=bulan where superadmin has Read-Only access
        $response = $this->actingAs($this->superAdmin)->get(route('admin.bulan.index'));
        $response->assertRedirect(route('admin.kuota.index', ['tab' => 'bulan']));

        $target = $this->actingAs($this->superAdmin)->get(route('admin.kuota.index', ['tab' => 'bulan']));
        $target->assertOk();
        $target->assertSee('Read-Only');

        // Store: 403 Forbidden
        $storeResponse = $this->actingAs($this->superAdmin)->post(route('admin.bulan.store'), [
            'bulan_mulai' => now()->addMonths(3)->startOfMonth()->toDateString(),
            'tgl_buka' => now()->toDateString(),
            'tgl_tutup' => now()->addDays(10)->toDateString(),
            'override_status' => 'otomatis',
        ]);
        $storeResponse->assertForbidden();

        // Update: 403 Forbidden
        $updateResponse = $this->actingAs($this->superAdmin)->put(route('admin.bulan.update', $bulan), [
            'bulan_mulai' => $bulan->bulan_mulai->toDateString(),
            'tgl_buka' => now()->toDateString(),
            'tgl_tutup' => now()->addDays(15)->toDateString(),
            'override_status' => 'tutup_manual',
        ]);
        $updateResponse->assertForbidden();

        // Toggle: 403 Forbidden
        $toggleResponse = $this->actingAs($this->superAdmin)->patch(route('admin.bulan.toggle', $bulan));
        $toggleResponse->assertForbidden();
    }

    /*
     |--------------------------------------------------------------------------
     | 2. Jalur Khusus (SlotKhusus & PesertaKhusus) Tests
     |--------------------------------------------------------------------------
     */

    public function test_admin_can_access_slot_khusus_and_peserta_khusus_crud(): void
    {
        $bulanDate = now()->addMonths(4)->startOfMonth();

        $bidang = Bidang::create([
            'nama_bidang' => 'Bidang Aptika',
            'deskripsi' => 'Aplikasi Informatika',
            'aktif' => true,
        ]);

        $jurusan = Jurusan::create([
            'nama_jurusan' => 'Teknik Informatika',
            'kategori' => 'mahasiswa',
            'aktif' => true,
        ]);

        $pembimbing = new User;
        $pembimbing->nama = 'Pembimbing Magang';
        $pembimbing->email = 'pembimbing.test@slemankab.go.id';
        $pembimbing->password = 'Password123!';
        $pembimbing->role = RoleEnum::Pembimbing;
        $pembimbing->status_aktif = true;
        $pembimbing->email_verified_at = now();
        $pembimbing->save();

        // 1. Index Slot Khusus
        $response = $this->actingAs($this->admin)->get(route('admin.slot-khusus.index'));
        $response->assertOk();
        $response->assertViewIs('admin.slot-khusus.index');
        $response->assertDontSee('Mode Lihat Saja (Read-Only)');

        // 2. Store Slot Khusus
        $storeResponse = $this->actingAs($this->admin)->post(route('admin.slot-khusus.store'), [
            'bulan_mulai' => $bulanDate->format('Y-m-d'),
            'jumlah' => 3,
        ]);
        $storeResponse->assertRedirect(route('admin.slot-khusus.index'));
        $this->assertDatabaseHas('slot_khusus', [
            'jumlah' => 3,
            'terisi' => 0,
        ]);

        $slot = SlotKhusus::firstOrFail();

        // 3. Toggle Slot Khusus (Nonaktifkan lalu Aktifkan kembali)
        $toggleResponse = $this->actingAs($this->admin)->patch(route('admin.slot-khusus.toggle', $slot));
        $toggleResponse->assertRedirect(route('admin.slot-khusus.index'));
        $this->assertDatabaseHas('slot_khusus', [
            'id' => $slot->id,
            'aktif' => 0,
        ]);

        $reToggleResponse = $this->actingAs($this->admin)->patch(route('admin.slot-khusus.toggle', $slot));
        $reToggleResponse->assertRedirect(route('admin.slot-khusus.index'));
        $this->assertDatabaseHas('slot_khusus', [
            'id' => $slot->id,
            'aktif' => 1,
        ]);

        // 4. Form Registrasi Peserta Khusus
        $createPesertaResponse = $this->actingAs($this->admin)->get(route('admin.peserta-khusus.create'));
        $createPesertaResponse->assertOk();
        $createPesertaResponse->assertViewIs('admin.peserta-khusus.create');

        // 5. Store Peserta Khusus
        $storePesertaResponse = $this->actingAs($this->admin)->post(route('admin.peserta-khusus.store'), [
            'slot_khusus_id' => $slot->id,
            'nama' => 'Budi Utomo Khusus',
            'email' => 'budi.khusus@example.com',
            'password' => 'Magkom2026!',
            'nim_nisn' => '1234567890',
            'institusi' => 'Politeknik Negeri Sleman',
            'jurusan_id' => $jurusan->id,
            'jenjang' => 's1',
            'bidang_id' => $bidang->id,
            'pembimbing_id' => $pembimbing->id,
            'bulan_mulai' => $bulanDate->format('Y-m'),
            'durasi_bulan' => 3,
        ]);
        $storePesertaResponse->assertRedirect(route('admin.slot-khusus.index'));
        $this->assertDatabaseHas('users', [
            'email' => 'budi.khusus@example.com',
            'role' => RoleEnum::Peserta->value,
        ]);
    }

    public function test_superadmin_has_read_only_access_on_slot_khusus_and_mutations_are_forbidden(): void
    {
        $slot = SlotKhusus::create([
            'bulan_mulai' => now()->addMonths(5)->startOfMonth()->format('Y-m-d'),
            'jumlah' => 2,
            'terisi' => 0,
            'aktif' => true,
            'dibuat_oleh' => $this->admin->id,
        ]);

        // Index: 200 OK
        $response = $this->actingAs($this->superAdmin)->get(route('admin.slot-khusus.index'));
        $response->assertOk();
        $response->assertSee('Mode Lihat Saja (Read-Only)');

        // Store Slot: 403 Forbidden
        $storeResponse = $this->actingAs($this->superAdmin)->post(route('admin.slot-khusus.store'), [
            'bulan_mulai' => now()->addMonths(6)->startOfMonth()->format('Y-m-d'),
            'jumlah' => 5,
        ]);
        $storeResponse->assertForbidden();

        // Toggle Slot: 403 Forbidden
        $toggleResponse = $this->actingAs($this->superAdmin)->patch(route('admin.slot-khusus.toggle', $slot));
        $toggleResponse->assertForbidden();

        // Store Peserta Khusus: 403 Forbidden
        $storePesertaResponse = $this->actingAs($this->superAdmin)->post(route('admin.peserta-khusus.store'), [
            'slot_khusus_id' => $slot->id,
            'nama' => 'Hacker Khusus',
            'email' => 'hacker@example.com',
        ]);
        $storePesertaResponse->assertForbidden();
    }

    /*
     |--------------------------------------------------------------------------
     | 3. Data Jurusan (Jurusan) Tests
     |--------------------------------------------------------------------------
     */

    public function test_admin_can_access_jurusan_crud(): void
    {
        // 1. Index
        $response = $this->actingAs($this->admin)->get(route('admin.jurusan.index'));
        $response->assertOk();
        $response->assertViewIs('admin.jurusan.index');
        $response->assertDontSee('Mode Lihat Saja (Read-Only)');

        // 2. Store
        $storeResponse = $this->actingAs($this->admin)->post(route('admin.jurusan.store'), [
            'nama_jurusan' => 'Sistem Informasi Spasial',
            'kategori' => 'mahasiswa',
        ]);
        $storeResponse->assertRedirect(route('admin.jurusan.index'));
        $this->assertDatabaseHas('jurusan', [
            'nama_jurusan' => 'Sistem Informasi Spasial',
            'kategori' => 'mahasiswa',
            'aktif' => true,
        ]);

        $jurusan = Jurusan::where('nama_jurusan', 'Sistem Informasi Spasial')->firstOrFail();

        // 3. Update
        $updateResponse = $this->actingAs($this->admin)->put(route('admin.jurusan.update', $jurusan), [
            'nama_jurusan' => 'Sistem Informasi Geografis & Spasial',
            'kategori' => 'mahasiswa',
        ]);
        $updateResponse->assertRedirect(route('admin.jurusan.index'));
        $this->assertDatabaseHas('jurusan', [
            'id' => $jurusan->id,
            'nama_jurusan' => 'Sistem Informasi Geografis & Spasial',
        ]);

        // 4. Toggle
        $toggleResponse = $this->actingAs($this->admin)->patch(route('admin.jurusan.toggle', $jurusan));
        $toggleResponse->assertRedirect(route('admin.jurusan.index'));
        $this->assertDatabaseHas('jurusan', [
            'id' => $jurusan->id,
            'aktif' => false,
        ]);
    }

    public function test_superadmin_has_read_only_access_on_jurusan_and_mutations_are_forbidden(): void
    {
        $jurusan = Jurusan::create([
            'nama_jurusan' => 'Rekayasa Perangkat Lunak',
            'kategori' => 'smk',
            'aktif' => true,
        ]);

        // Index: 200 OK
        $response = $this->actingAs($this->superAdmin)->get(route('admin.jurusan.index'));
        $response->assertOk();
        $response->assertSee('Mode Lihat Saja (Read-Only)');

        // Store: 403 Forbidden
        $storeResponse = $this->actingAs($this->superAdmin)->post(route('admin.jurusan.store'), [
            'nama_jurusan' => 'Desain Komunikasi Visual',
            'kategori' => 'smk',
        ]);
        $storeResponse->assertForbidden();

        // Update: 403 Forbidden
        $updateResponse = $this->actingAs($this->superAdmin)->put(route('admin.jurusan.update', $jurusan), [
            'nama_jurusan' => 'RPL Modern',
            'kategori' => 'smk',
        ]);
        $updateResponse->assertForbidden();

        // Toggle: 403 Forbidden
        $toggleResponse = $this->actingAs($this->superAdmin)->patch(route('admin.jurusan.toggle', $jurusan));
        $toggleResponse->assertForbidden();
    }

    /*
     |--------------------------------------------------------------------------
     | 4. Unit Kerja (Bidang) Tests
     |--------------------------------------------------------------------------
     */

    public function test_admin_can_access_bidang_crud(): void
    {
        // 1. Index
        $response = $this->actingAs($this->admin)->get(route('admin.bidang.index'));
        $response->assertOk();
        $response->assertViewIs('admin.bidang.index');
        $response->assertDontSee('Mode Lihat Saja (Read-Only)');

        // 2. Store
        $storeResponse = $this->actingAs($this->admin)->post(route('admin.bidang.store'), [
            'nama_bidang' => 'Bidang Statistik dan Persandian',
            'deskripsi' => 'Pengelolaan data statistik sektoral dan keamanan siber',
        ]);
        $storeResponse->assertRedirect(route('admin.bidang.index'));
        $this->assertDatabaseHas('bidang', [
            'nama_bidang' => 'Bidang Statistik dan Persandian',
            'aktif' => true,
        ]);

        $bidang = Bidang::where('nama_bidang', 'Bidang Statistik dan Persandian')->firstOrFail();

        // 3. Update
        $updateResponse = $this->actingAs($this->admin)->put(route('admin.bidang.update', $bidang), [
            'nama_bidang' => 'Bidang Statistik, Persandian dan Keamanan Informasi',
            'deskripsi' => 'Pengelolaan data statistik dan keamanan sistem informasi daerah',
        ]);
        $updateResponse->assertRedirect(route('admin.bidang.index'));
        $this->assertDatabaseHas('bidang', [
            'id' => $bidang->id,
            'nama_bidang' => 'Bidang Statistik, Persandian dan Keamanan Informasi',
        ]);

        // 4. Toggle
        $toggleResponse = $this->actingAs($this->admin)->patch(route('admin.bidang.toggle', $bidang));
        $toggleResponse->assertRedirect(route('admin.bidang.index'));
        $this->assertDatabaseHas('bidang', [
            'id' => $bidang->id,
            'aktif' => false,
        ]);
    }

    public function test_superadmin_has_read_only_access_on_bidang_and_mutations_are_forbidden(): void
    {
        $bidang = Bidang::create([
            'nama_bidang' => 'Sekretariat Dinas',
            'deskripsi' => 'Pengelolaan umum dan kepegawaian',
            'aktif' => true,
        ]);

        // Index: 200 OK
        $response = $this->actingAs($this->superAdmin)->get(route('admin.bidang.index'));
        $response->assertOk();
        $response->assertSee('Mode Lihat Saja (Read-Only)');

        // Store: 403 Forbidden
        $storeResponse = $this->actingAs($this->superAdmin)->post(route('admin.bidang.store'), [
            'nama_bidang' => 'Bidang Baru Ilegal',
            'deskripsi' => 'Percobaan mutasi oleh SuperAdmin',
        ]);
        $storeResponse->assertForbidden();

        // Update: 403 Forbidden
        $updateResponse = $this->actingAs($this->superAdmin)->put(route('admin.bidang.update', $bidang), [
            'nama_bidang' => 'Sekretariat Perubahan Ilegal',
            'deskripsi' => 'Deskripsi',
        ]);
        $updateResponse->assertForbidden();

        // Toggle: 403 Forbidden
        $toggleResponse = $this->actingAs($this->superAdmin)->patch(route('admin.bidang.toggle', $bidang));
        $toggleResponse->assertForbidden();
    }

    /*
     |--------------------------------------------------------------------------
     | 5. Backward Compatibility Redirects Tests
     |--------------------------------------------------------------------------
     */

    public function test_legacy_superadmin_routes_redirect_to_admin_routes(): void
    {
        $this->actingAs($this->superAdmin)->get('/superadmin/bulan')
            ->assertRedirect('/admin/bulan');

        $this->actingAs($this->superAdmin)->get('/superadmin/slot-khusus')
            ->assertRedirect('/admin/slot-khusus');

        $this->actingAs($this->superAdmin)->get('/superadmin/peserta-khusus/create')
            ->assertRedirect('/admin/peserta-khusus/create');

        $this->actingAs($this->superAdmin)->get('/superadmin/jurusan')
            ->assertRedirect('/admin/jurusan');

        $this->actingAs($this->superAdmin)->get('/superadmin/bidang')
            ->assertRedirect('/admin/bidang');
    }
}
