<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Enums\TipeAuditEnum;
use App\Models\AuditLog;
use App\Models\User;
use Database\Seeders\JenisDokumenSeeder;
use Database\Seeders\RubrikSeeder;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class SuperAdminAuditLogDetailTest extends TestCase
{
    use RefreshDatabase;

    public function createApplication(): Application
    {
        $basePath = dirname(__DIR__, 2);
        putenv("APP_BASE_PATH={$basePath}");
        $_ENV['APP_BASE_PATH'] = $basePath;
        $_SERVER['APP_BASE_PATH'] = $basePath;

        $app = require $basePath.'/bootstrap/app.php';
        $app->make(Kernel::class)->bootstrap();

        return $app;
    }

    protected User $superAdmin;

    protected User $admin;

    protected User $pembimbing;

    protected User $peserta;

    protected AuditLog $sampleLog;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(JenisDokumenSeeder::class);
        $this->seed(RubrikSeeder::class);

        // 1. Setup User SuperAdmin
        $this->superAdmin = new User;
        $this->superAdmin->nama = 'Master SuperAdmin';
        $this->superAdmin->email = 'superadmin@magkom.test';
        $this->superAdmin->password = bcrypt('SuperAdminSecure123!');
        $this->superAdmin->role = RoleEnum::SuperAdmin;
        $this->superAdmin->status_aktif = true;
        $this->superAdmin->email_verified_at = now();
        $this->superAdmin->must_change_password = false;
        $this->superAdmin->save();

        // 2. Setup User Admin
        $this->admin = new User;
        $this->admin->nama = 'Staff Admin';
        $this->admin->email = 'admin@magkom.test';
        $this->admin->password = bcrypt('AdminSecure123!');
        $this->admin->role = RoleEnum::Admin;
        $this->admin->status_aktif = true;
        $this->admin->email_verified_at = now();
        $this->admin->must_change_password = false;
        $this->admin->save();

        // 3. Setup User Pembimbing
        $this->pembimbing = new User;
        $this->pembimbing->nama = 'Mentor Pembimbing';
        $this->pembimbing->email = 'pembimbing@magkom.test';
        $this->pembimbing->password = bcrypt('PembimbingSecure123!');
        $this->pembimbing->role = RoleEnum::Pembimbing;
        $this->pembimbing->status_aktif = true;
        $this->pembimbing->email_verified_at = now();
        $this->pembimbing->must_change_password = false;
        $this->pembimbing->save();

        // 4. Setup User Peserta
        $this->peserta = new User;
        $this->peserta->nama = 'Peserta Magang';
        $this->peserta->email = 'peserta@magkom.test';
        $this->peserta->password = bcrypt('PesertaSecure123!');
        $this->peserta->role = RoleEnum::Peserta;
        $this->peserta->status_aktif = true;
        $this->peserta->email_verified_at = now();
        $this->peserta->must_change_password = false;
        $this->peserta->save();

        // 5. Sample Audit Log
        $this->sampleLog = AuditLog::create([
            'user_id' => $this->superAdmin->id,
            'tipe' => TipeAuditEnum::Aksi,
            'aksi' => 'Update Pengaturan Sistem',
            'entitas' => 'Pengaturan',
            'entitas_id' => 1,
            'channel' => 'konfigurasi-master',
            'severity' => 'info',
            'request_id' => 'req_test12345',
            'ip' => '127.0.0.1',
            'user_agent' => 'Mozilla/5.0 Test Agent',
            'metode' => 'POST',
            'rute' => '/superadmin/pengaturan',
            'status_http' => 200,
            'durasi_ms' => 12,
            'detail' => [
                'kunci' => 'kuota_global',
                'password' => 'secret_plain_text',
            ],
            'nilai_lama' => ['kuota' => 50, 'token' => 'old_secret_token'],
            'nilai_baru' => ['kuota' => 100, 'token' => 'new_secret_token'],
            'waktu' => Carbon::now(),
        ]);
    }

    public function test_superadmin_can_fetch_audit_log_detail_via_json_endpoint(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->getJson(route('superadmin.audit-log.show', ['id' => $this->sampleLog->id]));

        $response->assertOk()
            ->assertHeader('Cache-Control', 'must-revalidate, no-store, private')
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.id', $this->sampleLog->id)
            ->assertJsonPath('data.aksi', 'Update Pengaturan Sistem')
            ->assertJsonPath('data.channel_key', 'konfigurasi-master')
            ->assertJsonPath('data.entitas', 'Pengaturan')
            ->assertJsonPath('data.entitas_id', 1)
            ->assertJsonPath('data.status_http', 200)
            ->assertJsonPath('data.detail.kunci', 'kuota_global')
            ->assertJsonPath('data.nilai_lama.kuota', 50)
            ->assertJsonPath('data.nilai_baru.kuota', 100);

        // Sanitize check: password & token fields must be masked by AuditService::sanitasi
        $this->assertEquals('[DISAMARKAN]', $response->json('data.detail.password'));
        $this->assertEquals('[DISAMARKAN]', $response->json('data.nilai_lama.token'));
        $this->assertEquals('[DISAMARKAN]', $response->json('data.nilai_baru.token'));
    }

    public function test_superadmin_gets_404_for_nonexistent_audit_log(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->getJson(route('superadmin.audit-log.show', ['id' => 999999]));

        $response->assertNotFound()
            ->assertJsonPath('status', 'error');
    }

    public function test_non_superadmin_cannot_access_audit_log_detail_endpoint(): void
    {
        // Admin
        $responseAdmin = $this->actingAs($this->admin)
            ->getJson(route('superadmin.audit-log.show', ['id' => $this->sampleLog->id]));
        $responseAdmin->assertForbidden();

        // Pembimbing
        $responsePembimbing = $this->actingAs($this->pembimbing)
            ->getJson(route('superadmin.audit-log.show', ['id' => $this->sampleLog->id]));
        $responsePembimbing->assertForbidden();

        // Peserta
        $responsePeserta = $this->actingAs($this->peserta)
            ->getJson(route('superadmin.audit-log.show', ['id' => $this->sampleLog->id]));
        $responsePeserta->assertForbidden();
    }

    public function test_unauthenticated_guest_cannot_access_audit_log_detail(): void
    {
        $response = $this->getJson(route('superadmin.audit-log.show', ['id' => $this->sampleLog->id]));
        $response->assertUnauthorized();
    }
}
