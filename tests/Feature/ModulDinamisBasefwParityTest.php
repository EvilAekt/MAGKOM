<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Models\AuditLog;
use App\Models\ModulDinamis;
use App\Models\ModulDinamisData;
use App\Models\ModulDinamisField;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModulDinamisBasefwParityTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $admin;
    protected User $peserta;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Setup User SuperAdmin
        $this->superAdmin = new User();
        $this->superAdmin->nama = 'Super Admin Sleman';
        $this->superAdmin->email = 'superadmin.parity@slemankab.go.id';
        $this->superAdmin->password = 'Password123!';
        $this->superAdmin->role = RoleEnum::SuperAdmin;
        $this->superAdmin->status_aktif = true;
        $this->superAdmin->must_change_password = false;
        $this->superAdmin->email_verified_at = now();
        $this->superAdmin->save();

        // 2. Setup User Admin
        $this->admin = new User();
        $this->admin->nama = 'Admin Dinas MagKom';
        $this->admin->email = 'admin.parity@slemankab.go.id';
        $this->admin->password = 'Password123!';
        $this->admin->role = RoleEnum::Admin;
        $this->admin->status_aktif = true;
        $this->admin->must_change_password = false;
        $this->admin->email_verified_at = now();
        $this->admin->save();

        // 3. Setup User Peserta
        $this->peserta = new User();
        $this->peserta->nama = 'Peserta Magang';
        $this->peserta->email = 'peserta.parity@gmail.com';
        $this->peserta->password = 'Password123!';
        $this->peserta->role = RoleEnum::Peserta;
        $this->peserta->status_aktif = true;
        $this->peserta->must_change_password = false;
        $this->peserta->email_verified_at = now();
        $this->peserta->save();
    }

    /**
     * TEST 1: SuperAdmin dapat memanggil API untuk daftar tabel basis data (Reverse-Engineering API).
     */
    public function test_superadmin_can_get_database_tables_via_api(): void
    {
        $this->actingAs($this->superAdmin);

        $response = $this->getJson(route('superadmin.modul-builder.api.tables'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'tables',
        ]);
        $this->assertTrue($response->json('success'));
        $this->assertNotEmpty($response->json('tables'));
    }

    /**
     * TEST 2: SuperAdmin dapat menginspeksi kolom tabel basis data spesifik (Reverse-Engineering Inspector).
     */
    public function test_superadmin_can_inspect_columns_of_specific_table(): void
    {
        $this->actingAs($this->superAdmin);

        $response = $this->getJson(route('superadmin.modul-builder.api.inspect', ['table' => 'users']));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'schema' => [
                'table',
                'primary_key',
                'is_manual_pk',
                'has_unit_scope',
                'columns',
            ],
        ]);
        $this->assertEquals('users', $response->json('schema.table'));
        $this->assertEquals('id', $response->json('schema.primary_key'));
    }

    /**
     * TEST 3: Validasi keunikan Primary Key manual mencegah duplikasi data (Porting ensurePrimaryKeyAvailable Basefw).
     */
    public function test_manual_primary_key_duplicate_validation(): void
    {
        $this->actingAs($this->superAdmin);

        $modul = ModulDinamis::create([
            'nama_modul'       => 'Master Aset Dinas',
            'slug'             => 'master-aset-dinas',
            'tipe_sumber'      => 'dynamic',
            'primary_key'      => 'kode_aset',
            'is_manual_pk'     => true,
            'ikon'             => 'box',
            'target_roles'     => ['superadmin', 'admin'],
            'scope_mode'       => 'none',
            'status_aktif'     => true,
            'dibuat_oleh'      => $this->superAdmin->id,
        ]);

        ModulDinamisField::create([
            'modul_id'        => $modul->id,
            'nama_kolom'      => 'kode_aset',
            'label'           => 'Kode Aset',
            'tipe_data'       => 'text',
            'wajib'           => true,
            'tampil_di_tabel' => true,
            'dapat_dicari'    => true,
            'urutan'          => 1,
        ]);

        // 1. Simpan data pertama dengan kode_aset: AST-001
        $response1 = $this->post(route('superadmin.modul.store', ['slug' => 'master-aset-dinas']), [
            'kode_aset' => 'AST-001',
        ]);
        $response1->assertRedirect();
        $this->assertDatabaseHas('modul_dinamis_data', ['modul_id' => $modul->id]);

        // 2. Coba simpan data kedua dengan kode_aset yang sama persis: AST-001
        $response2 = $this->post(route('superadmin.modul.store', ['slug' => 'master-aset-dinas']), [
            'kode_aset' => 'AST-001',
        ]);

        // Harus gagal validasi dan redirect kembali dengan error pada field kode_aset
        $response2->assertSessionHasErrors('kode_aset');
    }

    /**
     * TEST 4: Matriks izin sub-aksi granular (CodeModule RBAC) memblokir peran yang tidak diizinkan.
     */
    public function test_granular_rbac_matrix_authorization(): void
    {
        $modul = ModulDinamis::create([
            'nama_modul'   => 'Laporan Rahasia Bidang',
            'slug'         => 'laporan-rahasia-bidang',
            'tipe_sumber'  => 'dynamic',
            'primary_key'  => 'id',
            'is_manual_pk' => false,
            'ikon'         => 'shield',
            'target_roles' => ['admin'],
            'izin_aksi'    => [
                'list'   => ['admin'],
                'create' => ['superadmin'], // Admin dilarang menambah data
                'delete' => ['superadmin'], // Admin dilarang menghapus data
            ],
            'status_aktif' => true,
            'dibuat_oleh'  => $this->superAdmin->id,
        ]);

        ModulDinamisField::create([
            'modul_id'        => $modul->id,
            'nama_kolom'      => 'judul',
            'label'           => 'Judul Laporan',
            'tipe_data'       => 'text',
            'wajib'           => true,
            'tampil_di_tabel' => true,
            'dapat_dicari'    => true,
            'urutan'          => 1,
        ]);

        $this->actingAs($this->admin);

        // Aksi List diizinkan (200) melalui rute kustom-modul
        $listResponse = $this->get(route('kustom-modul.index', ['slug' => 'laporan-rahasia-bidang']));
        $listResponse->assertStatus(200);

        // Aksi Create ditolak dengan 403 Forbidden
        $createResponse = $this->post(route('kustom-modul.store', ['slug' => 'laporan-rahasia-bidang']), [
            'judul' => 'Uji Coba Laporan Ilegal',
        ]);
        $createResponse->assertStatus(403);
    }

    /**
     * TEST 5: Toggle audit logging per-aksi menghormati preferensi log_aksi (CodeModule actions.log Parity).
     */
    public function test_conditional_audit_logging_toggle(): void
    {
        $this->actingAs($this->superAdmin);

        $modul = ModulDinamis::create([
            'nama_modul'   => 'Catatan Ringan',
            'slug'         => 'catatan-ringan',
            'tipe_sumber'  => 'dynamic',
            'primary_key'  => 'id',
            'is_manual_pk' => false,
            'ikon'         => 'file-text',
            'target_roles' => ['superadmin'],
            'log_aksi'     => [
                'create' => false, // Dilarang catat log saat create
                'delete' => true,  // Wajib catat log saat delete
            ],
            'status_aktif' => true,
            'dibuat_oleh'  => $this->superAdmin->id,
        ]);

        ModulDinamisField::create([
            'modul_id'        => $modul->id,
            'nama_kolom'      => 'catatan',
            'label'           => 'Catatan',
            'tipe_data'       => 'text',
            'wajib'           => true,
            'tampil_di_tabel' => true,
            'dapat_dicari'    => true,
            'urutan'          => 1,
        ]);

        $initialLogCount = AuditLog::count();

        // 1. Simpan data (create = false)
        $storeResponse = $this->post(route('superadmin.modul.store', ['slug' => 'catatan-ringan']), [
            'catatan' => 'Isi catatan tanpa log',
        ]);
        $storeResponse->assertRedirect();

        // Verifikasi log tidak bertambah untuk create
        $this->assertEquals($initialLogCount, AuditLog::count());

        $record = ModulDinamisData::where('modul_id', $modul->id)->firstOrFail();

        // 2. Hapus data (delete = true)
        $deleteResponse = $this->delete(route('superadmin.modul.destroy', [
            'slug' => 'catatan-ringan',
            'id'   => $record->id,
        ]));
        $deleteResponse->assertRedirect();

        // Verifikasi log bertambah karena delete = true
        $this->assertGreaterThan($initialLogCount, AuditLog::count());
    }

    /**
     * TEST 6: Endpoint publik read-only dapat diakses tanpa autentikasi jika diaktifkan (Basefw PUBLIC_list Parity).
     */
    public function test_public_read_endpoints_accessible_without_auth(): void
    {
        $modul = ModulDinamis::create([
            'nama_modul'     => 'Katalog Pengumuman Magang',
            'slug'           => 'katalog-pengumuman-magang',
            'tipe_sumber'    => 'dynamic',
            'primary_key'    => 'id',
            'is_manual_pk'   => false,
            'is_public_read' => true,
            'ikon'           => 'book',
            'target_roles'   => ['superadmin'],
            'status_aktif'   => true,
            'dibuat_oleh'    => $this->superAdmin->id,
        ]);

        ModulDinamisField::create([
            'modul_id'        => $modul->id,
            'nama_kolom'      => 'judul',
            'label'           => 'Judul Pengumuman',
            'tipe_data'       => 'text',
            'wajib'           => true,
            'tampil_di_tabel' => true,
            'dapat_dicari'    => true,
            'urutan'          => 1,
        ]);

        $record = ModulDinamisData::create([
            'modul_id'   => $modul->id,
            'data'       => ['judul' => 'Pembukaan Gelombang II 2026'],
            'created_by' => $this->superAdmin->id,
        ]);

        // Akses sebagai Guest (tanpa login)
        $publicListResponse = $this->get(route('public.modul.index', ['slug' => 'katalog-pengumuman-magang']));
        $publicListResponse->assertStatus(200);
        $publicListResponse->assertSee('Katalog Pengumuman Magang');
        $publicListResponse->assertSee('Pembukaan Gelombang II 2026');

        $publicDetailResponse = $this->get(route('public.modul.show', [
            'slug' => 'katalog-pengumuman-magang',
            'id'   => $record->id,
        ]));
        $publicDetailResponse->assertStatus(200);
        $publicDetailResponse->assertSee('Pembukaan Gelombang II 2026');
    }

    /**
     * TEST 7: Ekspor Dokumen Cetak Formal / PDF dan Print Preview (Porting admin_pdf & tpl_print Basefw).
     */
    public function test_export_pdf_and_print_preview_endpoints(): void
    {
        $this->actingAs($this->superAdmin);

        $modul = ModulDinamis::create([
            'nama_modul'   => 'Daftar Mitra Kampus',
            'slug'         => 'daftar-mitra-kampus',
            'tipe_sumber'  => 'dynamic',
            'primary_key'  => 'id',
            'is_manual_pk' => false,
            'ikon'         => 'users',
            'target_roles' => ['superadmin'],
            'status_aktif' => true,
            'dibuat_oleh'  => $this->superAdmin->id,
        ]);

        ModulDinamisField::create([
            'modul_id'        => $modul->id,
            'nama_kolom'      => 'nama_kampus',
            'label'           => 'Nama Perguruan Tinggi',
            'tipe_data'       => 'text',
            'wajib'           => true,
            'tampil_di_tabel' => true,
            'dapat_dicari'    => true,
            'urutan'          => 1,
        ]);

        ModulDinamisData::create([
            'modul_id'   => $modul->id,
            'data'       => ['nama_kampus' => 'Universitas Gadjah Mada'],
            'created_by' => $this->superAdmin->id,
        ]);

        // 1. Ekspor PDF
        $pdfResponse = $this->get(route('superadmin.modul.export-pdf', ['slug' => 'daftar-mitra-kampus']));
        $pdfResponse->assertStatus(200);
        $pdfResponse->assertSee('Pemerintah Kabupaten Sleman');
        $pdfResponse->assertSee('Universitas Gadjah Mada');

        // 2. Print Pratinjau
        $printResponse = $this->get(route('superadmin.modul.print', ['slug' => 'daftar-mitra-kampus']));
        $printResponse->assertStatus(200);
        $printResponse->assertSee('Laporan Data Daftar Mitra Kampus');
        $printResponse->assertSee('Universitas Gadjah Mada');
    }
}
