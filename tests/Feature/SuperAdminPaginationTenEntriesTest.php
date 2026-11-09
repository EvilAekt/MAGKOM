<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JalurEnum;
use App\Enums\KompleksitasEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusLogbookEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\StatusTugasEnum;
use App\Enums\SumberPenempatanEnum;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\Logbook;
use App\Models\Penempatan;
use App\Models\Permission;
use App\Models\RolePermission;
use App\Models\SlotKhusus;
use App\Models\Tugas;
use App\Models\User;
use Database\Seeders\JenisDokumenSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class SuperAdminPaginationTenEntriesTest extends TestCase
{
    use RefreshDatabase;

    protected User $superadmin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(JenisDokumenSeeder::class);

        $this->superadmin = User::factory()->create([
            'role' => RoleEnum::SuperAdmin,
            'status_aktif' => true,
        ]);
    }

    /**
     * AC-01: Master Program Studi & Jurusan membatasi 10 entri per halaman.
     */
    public function test_master_jurusan_paginates_ten_items_per_page(): void
    {
        for ($i = 1; $i <= 15; $i++) {
            Jurusan::create([
                'nama_jurusan' => 'Jurusan '.$i,
                'kategori' => 'mahasiswa',
                'aktif' => true,
            ]);
        }

        $response = $this->actingAs($this->superadmin)
            ->get(route('superadmin.jurusan.index'));

        $response->assertOk();
        $response->assertViewHas('jurusanList', function ($list) {
            return $list instanceof LengthAwarePaginator
                && $list->perPage() === 10
                && $list->count() === 10;
        });
        $response->assertSee('Menampilkan');
    }

    /**
     * AC-02: Daftar Bidang & Unit Kerja Diskominfo membatasi 10 entri per halaman.
     */
    public function test_master_bidang_paginates_ten_items_per_page(): void
    {
        for ($i = 1; $i <= 14; $i++) {
            Bidang::create([
                'nama_bidang' => 'Bidang '.$i,
                'deskripsi' => 'Deskripsi bidang '.$i,
                'aktif' => true,
            ]);
        }

        $response = $this->actingAs($this->superadmin)
            ->get(route('superadmin.bidang.index'));

        $response->assertOk();
        $response->assertViewHas('bidangList', function ($list) {
            return $list instanceof LengthAwarePaginator
                && $list->perPage() === 10
                && $list->count() === 10;
        });
        $response->assertSee('Menampilkan');
    }

    /**
     * AC-03 & AC-04: Slot Khusus Mitra & Peserta Jalur Khusus masing-masing membatasi 10 entri.
     */
    public function test_slot_khusus_and_peserta_khusus_paginate_ten_items_per_page(): void
    {
        for ($i = 1; $i <= 12; $i++) {
            SlotKhusus::create([
                'bulan_mulai' => now()->addMonths($i)->startOfMonth()->toDateString(),
                'jumlah' => 5,
                'terisi' => 0,
                'nama_instansi' => 'Mitra '.$i,
                'aktif' => true,
                'dibuat_oleh' => $this->superadmin->id,
            ]);
        }

        $response = $this->actingAs($this->superadmin)
            ->get(route('superadmin.slot-khusus.index'));

        $response->assertOk();
        $response->assertViewHas('slotList', function ($list) {
            return $list instanceof LengthAwarePaginator
                && $list->perPage() === 10
                && $list->getPageName() === 'slot_page';
        });
        $response->assertViewHas('pesertaKhususList', function ($list) {
            return $list instanceof LengthAwarePaginator
                && $list->perPage() === 10
                && $list->getPageName() === 'peserta_page';
        });
        $response->assertSee('Menampilkan');
    }

    /**
     * AC-05: Daftar Bulan Magang membatasi 10 entri per halaman.
     */
    public function test_bulan_magang_paginates_ten_items_per_page(): void
    {
        for ($i = 1; $i <= 13; $i++) {
            BulanMagang::create([
                'bulan_mulai' => now()->addMonths($i)->startOfMonth()->toDateString(),
                'tgl_buka' => now()->toDateString(),
                'tgl_tutup' => now()->addDays(20)->toDateString(),
                'aktif' => true,
            ]);
        }

        $response = $this->actingAs($this->superadmin)
            ->get(route('superadmin.bulan.index'));

        $response->assertOk();
        $response->assertViewHas('bulanList', function ($list) {
            return $list instanceof LengthAwarePaginator
                && $list->perPage() === 10
                && $list->count() === 10;
        });
        $response->assertSee('Menampilkan');
    }

    /**
     * AC-06: Matriks Wewenang & Hak Akses membatasi 10 entri dan tidak menghapus hak akses modul di halaman lain.
     */
    public function test_role_matrix_paginates_ten_and_preserves_other_pages_on_update(): void
    {
        $perms = [];
        for ($i = 1; $i <= 15; $i++) {
            $p = Permission::create([
                'nama_modul' => 'Modul '.$i,
                'slug' => 'modul.'.$i,
                'kategori' => 'Master',
                'urutan' => $i,
                'is_system' => false,
            ]);
            $perms[] = $p;
            RolePermission::create([
                'role' => RoleEnum::Admin->value,
                'permission_id' => $p->id,
            ]);
        }

        // Cek halaman index
        $response = $this->actingAs($this->superadmin)
            ->get(route('superadmin.role.index'));

        $response->assertOk();
        $response->assertViewHas('permissions', function ($list) {
            return $list instanceof LengthAwarePaginator
                && $list->perPage() === 10
                && $list->count() === 10;
        });
        $response->assertSee('page_permission_ids');

        // Uji updateMatrix hanya untuk page 1 (5 perm pertama)
        $pageOnePerms = array_slice($perms, 0, 5);
        $pageTwoPerm = $perms[11]; // modul ke-12 di halaman 2

        $matrixPayload = [];
        $pageIds = [];
        foreach ($pageOnePerms as $p) {
            $pageIds[] = $p->id;
            $matrixPayload[$p->id] = ['admin' => '1'];
        }

        $updateResponse = $this->actingAs($this->superadmin)
            ->put(route('superadmin.role.matrix.update'), [
                'matrix' => $matrixPayload,
                'page_permission_ids' => $pageIds,
            ]);

        $updateResponse->assertRedirect(route('superadmin.role.index'));

        // Pastikan permission di halaman 2 TIDAK terhapus
        $this->assertDatabaseHas('role_permissions', [
            'role' => RoleEnum::Admin->value,
            'permission_id' => $pageTwoPerm->id,
        ]);
    }

    /**
     * AC-07: Akun Pengguna Internal membatasi 10 entri per halaman.
     */
    public function test_staf_management_paginates_ten_items_per_page(): void
    {
        User::factory()->count(15)->create([
            'role' => RoleEnum::Pembimbing,
            'status_aktif' => true,
        ]);

        $response = $this->actingAs($this->superadmin)
            ->get(route('superadmin.staf.index'));

        $response->assertOk();
        $response->assertViewHas('stafList', function ($list) {
            return $list instanceof LengthAwarePaginator
                && $list->perPage() === 10
                && $list->count() === 10;
        });
        $response->assertSee('Menampilkan');
    }

    /**
     * AC-08: Bank Data Arsip membatasi 10 entri per halaman.
     */
    public function test_arsip_peserta_paginates_ten_items_per_page(): void
    {
        $bidang = Bidang::create(['nama_bidang' => 'Bidang TI', 'aktif' => true]);
        $pembimbing = User::factory()->create(['role' => RoleEnum::Pembimbing]);

        for ($i = 1; $i <= 15; $i++) {
            $p = User::factory()->create(['role' => RoleEnum::Peserta]);
            Penempatan::create([
                'peserta_id' => $p->id,
                'bidang_id' => $bidang->id,
                'pembimbing_id' => $pembimbing->id,
                'status' => StatusPenempatanEnum::Selesai,
                'tgl_mulai' => now()->subMonths(3)->toDateString(),
                'tgl_selesai' => now()->subDays(10)->toDateString(),
                'jalur' => JalurEnum::Umum,
                'sumber' => SumberPenempatanEnum::Online,
            ]);
        }

        $response = $this->actingAs($this->superadmin)
            ->get(route('superadmin.arsip.index'));

        $response->assertOk();
        $response->assertViewHas('arsipList', function ($list) {
            return $list instanceof LengthAwarePaginator
                && $list->perPage() === 10
                && $list->count() === 10;
        });
        $response->assertSee('Menampilkan');
    }

    /**
     * AC-09 & AC-10: Detail Arsip memuat kontainer pagination interaktif untuk tugas dan logbook.
     */
    public function test_arsip_show_renders_interactive_pagination_containers(): void
    {
        $bidang = Bidang::create(['nama_bidang' => 'Bidang TI', 'aktif' => true]);
        $pembimbing = User::factory()->create(['role' => RoleEnum::Pembimbing]);
        $peserta = User::factory()->create(['role' => RoleEnum::Peserta]);

        $penempatan = Penempatan::create([
            'peserta_id' => $peserta->id,
            'bidang_id' => $bidang->id,
            'pembimbing_id' => $pembimbing->id,
            'status' => StatusPenempatanEnum::Selesai,
            'tgl_mulai' => now()->subMonths(3)->toDateString(),
            'tgl_selesai' => now()->subDays(10)->toDateString(),
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
        ]);

        for ($i = 1; $i <= 12; $i++) {
            Tugas::create([
                'penempatan_id' => $penempatan->id,
                'judul' => 'Tugas '.$i,
                'kategori' => 'Umum',
                'kompleksitas' => KompleksitasEnum::Sedang,
                'deadline' => now()->addDays(7)->toDateString(),
                'status' => StatusTugasEnum::Selesai,
            ]);
        }

        for ($i = 1; $i <= 15; $i++) {
            Logbook::create([
                'penempatan_id' => $penempatan->id,
                'peserta_id' => $peserta->id,
                'tanggal' => now()->subDays($i)->toDateString(),
                'kegiatan' => 'Aktivitas '.$i,
                'status' => StatusLogbookEnum::Disetujui,
            ]);
        }

        $response = $this->actingAs($this->superadmin)
            ->get(route('superadmin.arsip.show', $penempatan));

        $response->assertOk();
        $response->assertSee('id="arsipTugasList"', false);
        $response->assertSee('id="arsipTugasPagination"', false);
        $response->assertSee('id="arsipLogbookTableBody"', false);
        $response->assertSee('id="arsipLogbookPagination"', false);
    }
}
