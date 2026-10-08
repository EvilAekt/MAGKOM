<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Enums\TipeAuditEnum;
use App\Models\AuditLog;
use App\Models\Jurusan;
use App\Models\Pengaturan;
use App\Models\Rubrik;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Route;
use RuntimeException;
use Tests\TestCase;

class CoreFoundationTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        // Daftarkan route testing untuk uji middleware role
        Route::get('/test/superadmin-only', function () {
            return response()->json(['status' => 'ok']);
        })->middleware(['web', 'auth', 'role:superadmin']);

        Route::get('/test/pembimbing-only', function () {
            return response()->json(['status' => 'ok']);
        })->middleware(['web', 'auth', 'role:pembimbing']);
    }

    public function test_user_model_role_enum_casting_dan_security_fillable(): void
    {
        $user = new User([
            'nama' => 'Test User',
            'email' => 'test@magkom.local',
            'role' => RoleEnum::SuperAdmin->value, // Harus diabaikan karena dilarang di fillable!
        ]);

        $this->assertNull($user->role); // Role dilarang di mass assignment

        $user->role = RoleEnum::SuperAdmin;
        $user->save();

        $this->assertInstanceOf(RoleEnum::class, $user->fresh()->role);
        $this->assertTrue($user->isSuperAdmin());
        $this->assertFalse($user->isPeserta());
    }

    public function test_middleware_role_melarang_akses_yang_tidak_berhak(): void
    {
        $peserta = new User([
            'nama' => 'Peserta Satu',
            'email' => 'peserta@magkom.local',
        ]);
        $peserta->role = RoleEnum::Peserta;
        $peserta->save();

        // Peserta mencoba akses route superadmin
        $response = $this->actingAs($peserta)->get('/test/superadmin-only');
        $response->assertStatus(403);

        // Superadmin akses route superadmin
        $superadmin = new User([
            'nama' => 'Super Admin',
            'email' => 'admin@magkom.local',
        ]);
        $superadmin->role = RoleEnum::SuperAdmin;
        $superadmin->save();

        $responseAdmin = $this->actingAs($superadmin)->get('/test/superadmin-only');
        $responseAdmin->assertStatus(200);
    }

    public function test_audit_service_mencatat_dan_menyamarkan_kredensial_rahasia(): void
    {
        $auditService = app(AuditService::class);

        $log = $auditService->auth(null, 'test.login', [
            'email' => 'user@magkom.local',
            'password' => 'RahasiaSuper123!',
            'token' => 'jwt_secret_token_abc',
            'browser' => 'Chrome',
        ]);

        $this->assertNotNull($log);
        $this->assertEquals(TipeAuditEnum::Auth, $log->tipe);
        $this->assertEquals('[DISAMARKAN]', $log->detail['password']);
        $this->assertEquals('[DISAMARKAN]', $log->detail['token']);
        $this->assertEquals('Chrome', $log->detail['browser']);
    }

    public function test_audit_log_bersifat_append_only_dan_menolak_update_serta_delete(): void
    {
        $log = AuditLog::create([
            'request_id' => 'req-12345',
            'tipe' => TipeAuditEnum::Aksi,
            'aksi' => 'test.aksi',
            'ip' => '127.0.0.1',
        ]);

        $this->expectException(RuntimeException::class);
        $log->update(['aksi' => 'ubah.aksi']);
    }
}
