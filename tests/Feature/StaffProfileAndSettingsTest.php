<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StaffProfileAndSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $pembimbing;

    protected User $superAdmin;

    protected User $peserta;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        $this->admin = new User;
        $this->admin->nama = 'Staf Administrasi Operasional';
        $this->admin->email = 'admin.operasional@slemankab.go.id';
        $this->admin->password = 'PasswordAdmin123!';
        $this->admin->role = RoleEnum::Admin;
        $this->admin->status_aktif = true;
        $this->admin->no_telp = '081234567890';
        $this->admin->email_verified_at = now();
        $this->admin->save();

        $this->pembimbing = new User;
        $this->pembimbing->nama = 'Pembimbing Lapangan Informatika';
        $this->pembimbing->email = 'pembimbing.aptika@slemankab.go.id';
        $this->pembimbing->password = 'PasswordPembimbing123!';
        $this->pembimbing->role = RoleEnum::Pembimbing;
        $this->pembimbing->status_aktif = true;
        $this->pembimbing->no_telp = '082345678901';
        $this->pembimbing->email_verified_at = now();
        $this->pembimbing->save();

        $this->superAdmin = new User;
        $this->superAdmin->nama = 'Super Administrator Induk';
        $this->superAdmin->email = 'superadmin@slemankab.go.id';
        $this->superAdmin->password = 'PasswordSuperAdmin123!';
        $this->superAdmin->role = RoleEnum::SuperAdmin;
        $this->superAdmin->status_aktif = true;
        $this->superAdmin->no_telp = '083456789012';
        $this->superAdmin->email_verified_at = now();
        $this->superAdmin->save();

        $this->peserta = new User;
        $this->peserta->nama = 'Peserta Magang MagKom';
        $this->peserta->email = 'peserta.magang@univ.ac.id';
        $this->peserta->password = 'PasswordPeserta123!';
        $this->peserta->role = RoleEnum::Peserta;
        $this->peserta->status_aktif = true;
        $this->peserta->email_verified_at = now();
        $this->peserta->save();
    }

    /* =========================================================================
       ADMIN TESTS
       ========================================================================= */

    public function test_admin_dapat_melihat_halaman_profil_dan_pengaturan(): void
    {
        $responseProfil = $this->actingAs($this->admin)->get(route('admin.profil.show'));
        $responseProfil->assertOk();
        $responseProfil->assertSee('Data Staf Administrasi');
        $responseProfil->assertSee($this->admin->nama);
        $responseProfil->assertSee($this->admin->email);

        $responsePengaturan = $this->actingAs($this->admin)->get(route('admin.pengaturan.index'));
        $responsePengaturan->assertOk();
        $responsePengaturan->assertSee('Akun');
        $responsePengaturan->assertSee('Ganti Kata Sandi');
        $responsePengaturan->assertSee('Preferensi Antarmuka');
    }

    public function test_admin_dapat_memperbarui_biodata_dan_kontak(): void
    {
        $response = $this->actingAs($this->admin)->put(route('admin.profil.update'), [
            'nama' => 'Staf Administrasi Baru S.Kom',
            'no_telp' => '089988776655',
            'email' => 'hacked.email@fake.com', // Mencoba manipulasi email
        ]);

        $response->assertRedirect(route('admin.profil.show'));
        $response->assertSessionHas('success');

        $this->admin->refresh();
        $this->assertEquals('Staf Administrasi Baru S.Kom', $this->admin->nama);
        $this->assertEquals('089988776655', $this->admin->no_telp);
        $this->assertEquals('admin.operasional@slemankab.go.id', $this->admin->email); // Email tidak boleh berubah
    }

    public function test_admin_dapat_mengunggah_dan_menghapus_foto_profil(): void
    {
        $foto = UploadedFile::fake()->create('pasfoto_admin.jpg', 300, 'image/jpeg');

        $responseUpload = $this->actingAs($this->admin)->put(route('admin.profil.update'), [
            'nama' => $this->admin->nama,
            'no_telp' => $this->admin->no_telp,
            'foto' => $foto,
        ]);

        $responseUpload->assertRedirect(route('admin.profil.show'));
        $responseUpload->assertSessionHas('success');

        $this->admin->refresh();
        $this->assertNotNull($this->admin->foto);
        $this->assertStringStartsWith('profil-staf/', $this->admin->foto);
        Storage::disk('public')->assertExists($this->admin->foto);
        $this->assertStringContainsString('/storage/profil-staf/', $this->admin->foto_url);

        // Uji Hapus Foto
        $oldPath = $this->admin->foto;
        $responseDelete = $this->actingAs($this->admin)->put(route('admin.profil.update'), [
            'nama' => $this->admin->nama,
            'no_telp' => $this->admin->no_telp,
            'hapus_foto' => '1',
        ]);

        $responseDelete->assertRedirect(route('admin.profil.show'));
        $this->admin->refresh();
        $this->assertNull($this->admin->foto);
        Storage::disk('public')->assertMissing($oldPath);
    }

    public function test_admin_dapat_mengganti_kata_sandi_dengan_sukses(): void
    {
        $response = $this->actingAs($this->admin)->put(route('admin.pengaturan.password.update'), [
            'current_password' => 'PasswordAdmin123!',
            'password' => 'SandiBaruAdmin2026#',
            'password_confirmation' => 'SandiBaruAdmin2026#',
        ]);

        $response->assertRedirect(route('admin.pengaturan.index'));
        $response->assertSessionHas('success');

        $this->admin->refresh();
        $this->assertTrue(Hash::check('SandiBaruAdmin2026#', $this->admin->password));

        // Verifikasi tercatat di Audit Log
        $this->assertDatabaseHas('audit_log', [
            'user_id' => $this->admin->id,
            'aksi' => 'ganti_password_mandiri',
        ]);
    }

    public function test_admin_gagal_ganti_kata_sandi_jika_sandi_lama_salah_atau_sandi_baru_sama(): void
    {
        // 1. Sandi lama salah
        $responseWrongCurrent = $this->actingAs($this->admin)->put(route('admin.pengaturan.password.update'), [
            'current_password' => 'KataSandiSalah!',
            'password' => 'SandiBaruAdmin2026#',
            'password_confirmation' => 'SandiBaruAdmin2026#',
        ]);
        $responseWrongCurrent->assertSessionHasErrors('current_password');

        // 2. Sandi baru sama persis dengan yang lama
        $responseSame = $this->actingAs($this->admin)->put(route('admin.pengaturan.password.update'), [
            'current_password' => 'PasswordAdmin123!',
            'password' => 'PasswordAdmin123!',
            'password_confirmation' => 'PasswordAdmin123!',
        ]);
        $responseSame->assertSessionHasErrors('password');
    }

    public function test_admin_dapat_mengubah_preferensi_tema_dan_bahasa(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.pengaturan.preferensi.update'), [
            'tema' => 'gelap',
            'bahasa' => 'en',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('tema_tampilan', 'gelap');
        $response->assertSessionHas('bahasa_portal', 'en');
        $response->assertCookie('tema_tampilan', 'gelap');
        $response->assertCookie('bahasa_portal', 'en');
    }

    /* =========================================================================
       PEMBIMBING TESTS
       ========================================================================= */

    public function test_pembimbing_dapat_melihat_halaman_profil_dan_pengaturan(): void
    {
        $responseProfil = $this->actingAs($this->pembimbing)->get(route('pembimbing.profil.show'));
        $responseProfil->assertOk();
        $responseProfil->assertSee('Data Pembimbing Lapangan');
        $responseProfil->assertSee($this->pembimbing->nama);

        $responsePengaturan = $this->actingAs($this->pembimbing)->get(route('pembimbing.pengaturan.index'));
        $responsePengaturan->assertOk();
        $responsePengaturan->assertSee('Akun');
        $responsePengaturan->assertSee('Pembimbing Lapangan');
    }

    public function test_pembimbing_dapat_memperbarui_biodata_dan_foto(): void
    {
        $foto = UploadedFile::fake()->create('pasfoto_pembimbing.png', 300, 'image/png');

        $response = $this->actingAs($this->pembimbing)->put(route('pembimbing.profil.update'), [
            'nama' => 'Ir. Pembimbing Lapangan M.Eng',
            'no_telp' => '087711223344',
            'foto' => $foto,
        ]);

        $response->assertRedirect(route('pembimbing.profil.show'));
        $response->assertSessionHas('success');

        $this->pembimbing->refresh();
        $this->assertEquals('Ir. Pembimbing Lapangan M.Eng', $this->pembimbing->nama);
        $this->assertEquals('087711223344', $this->pembimbing->no_telp);
        $this->assertNotNull($this->pembimbing->foto);
        Storage::disk('public')->assertExists($this->pembimbing->foto);
    }

    public function test_pembimbing_dapat_mengganti_kata_sandi_dan_preferensi(): void
    {
        $responsePassword = $this->actingAs($this->pembimbing)->put(route('pembimbing.pengaturan.password.update'), [
            'current_password' => 'PasswordPembimbing123!',
            'password' => 'PasswordBaruPembimbing2026$',
            'password_confirmation' => 'PasswordBaruPembimbing2026$',
        ]);

        $responsePassword->assertRedirect(route('pembimbing.pengaturan.index'));
        $responsePassword->assertSessionHas('success');

        $this->pembimbing->refresh();
        $this->assertTrue(Hash::check('PasswordBaruPembimbing2026$', $this->pembimbing->password));

        $responsePref = $this->actingAs($this->pembimbing)->post(route('pembimbing.pengaturan.preferensi.update'), [
            'tema' => 'sistem',
            'bahasa' => 'id',
        ]);
        $responsePref->assertSessionHas('tema_tampilan', 'sistem');
        $responsePref->assertSessionHas('bahasa_portal', 'id');
    }

    /* =========================================================================
       SUPER ADMIN TESTS
       ========================================================================= */

    public function test_superadmin_dapat_melihat_halaman_profil_dan_akun(): void
    {
        $responseProfil = $this->actingAs($this->superAdmin)->get(route('superadmin.profil.show'));
        $responseProfil->assertOk();
        $responseProfil->assertSee('Data Super Administrator');
        $responseProfil->assertSee($this->superAdmin->nama);

        $responseAkun = $this->actingAs($this->superAdmin)->get(route('superadmin.akun.index'));
        $responseAkun->assertOk();
        $responseAkun->assertSee('Akun');
        $responseAkun->assertSee('Ganti Kata Sandi');
    }

    public function test_superadmin_dapat_memperbarui_biodata_dan_foto(): void
    {
        $foto = UploadedFile::fake()->create('pasfoto_superadmin.webp', 250, 'image/webp');

        $response = $this->actingAs($this->superAdmin)->put(route('superadmin.profil.update'), [
            'nama' => 'Super Administrator Utama Sleman',
            'no_telp' => '088899990000',
            'foto' => $foto,
        ]);

        $response->assertRedirect(route('superadmin.profil.show'));
        $response->assertSessionHas('success');

        $this->superAdmin->refresh();
        $this->assertEquals('Super Administrator Utama Sleman', $this->superAdmin->nama);
        $this->assertEquals('088899990000', $this->superAdmin->no_telp);
        $this->assertNotNull($this->superAdmin->foto);
        Storage::disk('public')->assertExists($this->superAdmin->foto);
    }

    public function test_superadmin_dapat_mengganti_kata_sandi_dan_preferensi(): void
    {
        $responsePassword = $this->actingAs($this->superAdmin)->put(route('superadmin.akun.password.update'), [
            'current_password' => 'PasswordSuperAdmin123!',
            'password' => 'SuperPasswordBaru2026#!!',
            'password_confirmation' => 'SuperPasswordBaru2026#!!',
        ]);

        $responsePassword->assertRedirect(route('superadmin.akun.index'));
        $responsePassword->assertSessionHas('success');

        $this->superAdmin->refresh();
        $this->assertTrue(Hash::check('SuperPasswordBaru2026#!!', $this->superAdmin->password));

        $this->assertDatabaseHas('audit_log', [
            'user_id' => $this->superAdmin->id,
            'aksi' => 'ganti_password_mandiri',
        ]);

        $responsePref = $this->actingAs($this->superAdmin)->post(route('superadmin.akun.preferensi.update'), [
            'tema' => 'gelap',
            'bahasa' => 'id',
        ]);
        $responsePref->assertSessionHas('tema_tampilan', 'gelap');
    }

    /* =========================================================================
       AUTHORIZATION & SECURITY TESTS
       ========================================================================= */

    public function test_peserta_dilarang_mengakses_route_staff_profil_dan_pengaturan(): void
    {
        $this->actingAs($this->peserta)->get(route('admin.profil.show'))->assertForbidden();
        $this->actingAs($this->peserta)->get(route('admin.pengaturan.index'))->assertForbidden();
        $this->actingAs($this->peserta)->get(route('pembimbing.profil.show'))->assertForbidden();
        $this->actingAs($this->peserta)->get(route('pembimbing.pengaturan.index'))->assertForbidden();
        $this->actingAs($this->peserta)->get(route('superadmin.profil.show'))->assertForbidden();
        $this->actingAs($this->peserta)->get(route('superadmin.akun.index'))->assertForbidden();
    }

    public function test_tamu_diarahkan_ke_halaman_login_saat_mengakses_halaman_staf(): void
    {
        $this->get(route('admin.profil.show'))->assertRedirect(route('login'));
        $this->get(route('pembimbing.profil.show'))->assertRedirect(route('login'));
        $this->get(route('superadmin.profil.show'))->assertRedirect(route('login'));
    }
}
