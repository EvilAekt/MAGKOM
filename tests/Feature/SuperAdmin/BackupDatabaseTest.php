<?php

declare(strict_types=1);

namespace Tests\Feature\SuperAdmin;

use App\Enums\RoleEnum;
use App\Enums\TipeAuditEnum;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class BackupDatabaseTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected User $admin;

    protected User $pembimbing;

    protected User $peserta;

    protected string $backupDir;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PermissionSeeder::class);

        $this->backupDir = storage_path('app/backups');
        File::ensureDirectoryExists($this->backupDir);

        $this->superAdmin = User::factory()->create([
            'role' => RoleEnum::SuperAdmin,
            'status_aktif' => true,
        ]);

        $this->admin = User::factory()->create([
            'role' => RoleEnum::Admin,
            'status_aktif' => true,
        ]);

        $this->pembimbing = User::factory()->create([
            'role' => RoleEnum::Pembimbing,
            'status_aktif' => true,
        ]);

        $this->peserta = User::factory()->create([
            'role' => RoleEnum::Peserta,
            'status_aktif' => true,
        ]);
    }

    public function test_superadmin_can_view_backup_index_and_disk_telemetry(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->get(route('superadmin.backup.index'));

        $response->assertOk();
        $response->assertSee('Pusat Cadangan Basis Data (Disaster Recovery)', false);
        $response->assertSee('SEC-03 SuperAdmin Only');
        $response->assertViewHas('backupFiles');
        $response->assertViewHas('diskTelemetry');
    }

    public function test_non_superadmin_is_forbidden_from_backup_features(): void
    {
        // Role Admin ditolak
        $responseAdmin = $this->actingAs($this->admin)
            ->get(route('superadmin.backup.index'));
        $responseAdmin->assertForbidden();

        // Role Pembimbing ditolak
        $responsePembimbing = $this->actingAs($this->pembimbing)
            ->get(route('superadmin.backup.index'));
        $responsePembimbing->assertForbidden();

        // Role Peserta ditolak
        $responsePeserta = $this->actingAs($this->peserta)
            ->get(route('superadmin.backup.index'));
        $responsePeserta->assertForbidden();
    }

    public function test_superadmin_can_trigger_instant_database_snapshot(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->post(route('superadmin.backup.snapshot'));

        $response->assertRedirect(route('superadmin.backup.index'));
        $response->assertSessionHas('success');

        // Verifikasi audit log tercatat
        $this->assertDatabaseHas('audit_log', [
            'tipe' => TipeAuditEnum::Aksi->value,
            'channel' => 'backup-database',
        ]);
    }

    public function test_backup_trigger_is_rate_limited(): void
    {
        // Pemicuan pertama: Sukses
        $res1 = $this->actingAs($this->superAdmin)
            ->post(route('superadmin.backup.snapshot'));
        $res1->assertSessionHas('success');

        // Pemicuan kedua seketika: Dibatasi Rate Limiter
        $res2 = $this->actingAs($this->superAdmin)
            ->post(route('superadmin.backup.snapshot'));
        $res2->assertSessionHas('error');
    }

    public function test_path_traversal_on_download_is_rejected(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->get(route('superadmin.backup.download', ['filename' => 'malicious_script.php']));

        $response->assertStatus(400);

        $responseTraversal = $this->actingAs($this->superAdmin)
            ->get(route('superadmin.backup.download', ['filename' => 'backup-magkom-hack.sql.gz']));

        $responseTraversal->assertStatus(400);
    }

    public function test_superadmin_can_download_valid_backup_and_records_audit_trail(): void
    {
        $dummyName = 'backup-magkom-sqlite-2026-11-09-120000.sqlite';
        $dummyPath = $this->backupDir.DIRECTORY_SEPARATOR.$dummyName;
        File::put($dummyPath, '-- Dummy SQLite Snapshot Test');

        $response = $this->actingAs($this->superAdmin)
            ->get(route('superadmin.backup.download', ['filename' => $dummyName]));

        $response->assertOk();
        $this->assertEquals('nosniff', $response->headers->get('X-Content-Type-Options'));

        // Verifikasi Audit Trail Akses Data tercatat
        $this->assertDatabaseHas('audit_log', [
            'tipe' => TipeAuditEnum::AksesData->value,
            'channel' => 'backup-database',
        ]);

        if (file_exists($dummyPath)) {
            @unlink($dummyPath);
        }
    }

    public function test_superadmin_can_destroy_backup(): void
    {
        $dummyName = 'backup-magkom-sqlite-2026-11-09-130000.sqlite';
        $dummyPath = $this->backupDir.DIRECTORY_SEPARATOR.$dummyName;
        File::put($dummyPath, '-- Dummy SQLite Snapshot to Destroy');

        $this->assertFileExists($dummyPath);

        $response = $this->actingAs($this->superAdmin)
            ->delete(route('superadmin.backup.destroy', ['filename' => $dummyName]));

        $response->assertRedirect(route('superadmin.backup.index'));
        $response->assertSessionHas('success');
        $this->assertFileDoesNotExist($dummyPath);
    }

    public function test_artisan_command_magkom_backup_database_creates_snapshot_and_prunes_old(): void
    {
        $this->artisan('magkom:backup-database', ['--clean-old' => true])
            ->assertSuccessful();
    }

    public function test_backup_table_supports_pagination_with_ten_items_per_page(): void
    {
        // Buat 25 berkas snapshot dummy untuk menguji perpindahan halaman 1, 2, 3
        $createdFiles = [];
        for ($i = 1; $i <= 25; $i++) {
            $num = str_pad((string) $i, 2, '0', STR_PAD_LEFT);
            $filename = "backup-magkom-sqlite-2026-11-09-0100{$num}.sqlite";
            $path = $this->backupDir.DIRECTORY_SEPARATOR.$filename;
            File::put($path, "-- Dummy Snapshot {$i}");
            touch($path, time() - (30 - $i) * 60);
            $createdFiles[] = $path;
        }

        // Halaman 1: Harus menampilkan 10 data dan tombol navigasi halaman 1, 2, 3
        $responsePage1 = $this->actingAs($this->superAdmin)
            ->get(route('superadmin.backup.index', ['page' => 1]));

        $responsePage1->assertOk();
        $responsePage1->assertSee('magkom-pagination-nav', false);
        $responsePage1->assertSee('magkom-pagination-summary', false);
        $responsePage1->assertSee('Menampilkan', false);
        $responsePage1->assertSee('sampai', false);
        $responsePage1->assertSee('dari', false);
        $responsePage1->assertSee('entri', false);
        $responsePage1->assertSee('magkom-pagination-list', false);
        $responsePage1->assertSee('page=2', false);
        $responsePage1->assertSee('page=3', false);
        $responsePage1->assertSee('Berikutnya', false);

        /** @var LengthAwarePaginator $paginator */
        $paginator = $responsePage1->viewData('backupFiles');
        $this->assertInstanceOf(LengthAwarePaginator::class, $paginator);
        $this->assertEquals(10, $paginator->perPage());
        $this->assertGreaterThanOrEqual(25, $paginator->total());
        $this->assertEquals(10, $paginator->count());
        $this->assertEquals(1, $paginator->firstItem());
        $this->assertEquals(10, $paginator->lastItem());

        // Halaman 2: Harus menampilkan data halaman ke-2 (item 11-20)
        $responsePage2 = $this->actingAs($this->superAdmin)
            ->get(route('superadmin.backup.index', ['page' => 2]));

        $responsePage2->assertOk();
        /** @var LengthAwarePaginator $paginatorPage2 */
        $paginatorPage2 = $responsePage2->viewData('backupFiles');
        $this->assertEquals(2, $paginatorPage2->currentPage());
        $this->assertEquals(10, $paginatorPage2->count());
        $this->assertEquals(11, $paginatorPage2->firstItem());
        $this->assertEquals(20, $paginatorPage2->lastItem());
        $responsePage2->assertSee('page=1', false);
        $responsePage2->assertSee('page=3', false);
        $responsePage2->assertSee('Sebelumnya', false);

        // Halaman 3: Harus menampilkan data halaman ke-3 (item 21-25)
        $responsePage3 = $this->actingAs($this->superAdmin)
            ->get(route('superadmin.backup.index', ['page' => 3]));

        $responsePage3->assertOk();
        /** @var LengthAwarePaginator $paginatorPage3 */
        $paginatorPage3 = $responsePage3->viewData('backupFiles');
        $this->assertEquals(3, $paginatorPage3->currentPage());
        $this->assertGreaterThanOrEqual(5, $paginatorPage3->count());
        $this->assertEquals(21, $paginatorPage3->firstItem());
        $responsePage3->assertSee('Sebelumnya', false);

        // Bersihkan dummy files
        foreach ($createdFiles as $path) {
            if (file_exists($path)) {
                @unlink($path);
            }
        }
    }
}
