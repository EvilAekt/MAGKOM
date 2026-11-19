<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Models\Bidang;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SuperAdminStafCredentialCopyTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected Bidang $bidang;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superAdmin = User::factory()->create([
            'role' => RoleEnum::SuperAdmin,
            'status_aktif' => true,
        ]);

        $this->bidang = Bidang::create([
            'nama_bidang' => 'Sistem Informasi & Keamanan Siber',
            'aktif' => true,
        ]);
    }

    public function test_superadmin_can_create_staff_and_initial_password_is_saved_encrypted(): void
    {
        $plainPassword = 'PasswordStaf123!';

        $response = $this->actingAs($this->superAdmin)->post(route('superadmin.staf.store'), [
            'nama' => 'Drs. Hendro Wibowo',
            'email' => 'hendro.wibowo@sleman.go.id',
            'role' => RoleEnum::Pembimbing->value,
            'bidang_id' => $this->bidang->id,
            'password' => $plainPassword,
        ]);

        $response->assertRedirect(route('superadmin.staf.index'));
        $response->assertSessionHas('success');

        $staf = User::where('email', 'hendro.wibowo@sleman.go.id')->first();
        $this->assertNotNull($staf);
        $this->assertTrue($staf->must_change_password);

        // Verifikasi enkripsi di DB mentah
        $rawPasswordAwal = DB::table('users')->where('id', $staf->id)->value('password_awal');
        $this->assertNotEmpty($rawPasswordAwal);
        $this->assertNotEquals($plainPassword, $rawPasswordAwal);

        // Verifikasi transparansi Eloquent decrypt
        $this->assertSame($plainPassword, $staf->fresh()->password_awal);
    }

    public function test_regenerating_staff_password_updates_password_awal(): void
    {
        $staf = User::factory()->create([
            'role' => RoleEnum::Admin,
            'status_aktif' => true,
            'password_awal' => 'Lama12345!',
            'must_change_password' => false,
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->post(route('superadmin.staf.regenerate-password', $staf));

        $response->assertRedirect(route('superadmin.staf.index'));
        $response->assertSessionHas('success');

        $stafFresh = $staf->fresh();
        $this->assertTrue($stafFresh->must_change_password);
        $this->assertNotEmpty($stafFresh->password_awal);
        $this->assertNotEquals('Lama12345!', $stafFresh->password_awal);
    }

    public function test_manual_password_reset_updates_password_awal(): void
    {
        $staf = User::factory()->create([
            'role' => RoleEnum::Pembimbing,
            'bidang_id' => $this->bidang->id,
            'status_aktif' => true,
            'password_awal' => null,
            'must_change_password' => false,
        ]);

        $newPassword = 'ResetSandiBaru2026!';

        $response = $this->actingAs($this->superAdmin)
            ->post(route('superadmin.staf.reset-password', $staf), [
                'password_baru' => $newPassword,
            ]);

        $response->assertRedirect(route('superadmin.staf.index'));
        $stafFresh = $staf->fresh();

        $this->assertTrue($stafFresh->must_change_password);
        $this->assertSame($newPassword, $stafFresh->password_awal);
    }

    public function test_staf_table_renders_copy_button_and_status_badges(): void
    {
        // 1. Staf dengan sandi awal aktif
        $stafAktif = User::factory()->create([
            'nama' => 'Agus Purnomo',
            'email' => 'agus.purnomo@sleman.go.id',
            'role' => RoleEnum::Admin,
            'password_awal' => 'AgusPass123!',
            'must_change_password' => true,
        ]);

        // 2. Staf yang sudah ganti sandi
        $stafChanged = User::factory()->create([
            'nama' => 'Ratna Dewi',
            'email' => 'ratna.dewi@sleman.go.id',
            'role' => RoleEnum::Pembimbing,
            'bidang_id' => $this->bidang->id,
            'password_awal' => null,
            'must_change_password' => false,
        ]);

        $response = $this->actingAs($this->superAdmin)->get(route('superadmin.staf.index'));
        $response->assertOk();

        // Verifikasi tombol salin kredensial awal
        $response->assertSee('sa-btn-copy-kredensial', false);
        $response->assertSee('Salin Akun');
        $response->assertSee('Kredensial Awal');

        // Verifikasi badge status
        $response->assertSee('Sandi Awal Aktif');
        $response->assertSee('Sandi Diubah Staf');

        // Verifikasi tombol toggle password mata di modal
        $response->assertSee('toggle-password-btn', false);
        $response->assertSee('tambahPassword', false);
        $response->assertSee('resetPasswordBaru', false);
    }
}
