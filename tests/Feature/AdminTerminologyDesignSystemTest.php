<?php

namespace Tests\Feature;

use App\Enums\JenjangEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusPendaftaranEnum;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\Kuota;
use App\Models\Pendaftaran;
use App\Models\PesertaProfil;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTerminologyDesignSystemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::parse('2026-10-08 10:00:00', 'Asia/Jakarta'));
        CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-10-08 10:00:00', 'Asia/Jakarta'));

        if (Jurusan::count() === 0 || BulanMagang::count() === 0) {
            $this->seed();
        }
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        CarbonImmutable::setTestNow();
        parent::tearDown();
    }

    private function createAdminUser(): User
    {
        $admin = new User;
        $admin->nama = 'Administrator Test';
        $admin->email = 'admin.term.test@slemankab.go.id';
        $admin->password = 'Password123!';
        $admin->role = RoleEnum::Admin;
        $admin->status_aktif = true;
        $admin->must_change_password = false;
        $admin->email_verified_at = now();
        $admin->save();

        return $admin;
    }

    private function createSuperAdminUser(): User
    {
        $superadmin = new User;
        $superadmin->nama = 'Super Administrator Test';
        $superadmin->email = 'superadmin.term.test@slemankab.go.id';
        $superadmin->password = 'Password123!';
        $superadmin->role = RoleEnum::SuperAdmin;
        $superadmin->status_aktif = true;
        $superadmin->must_change_password = false;
        $superadmin->email_verified_at = now();
        $superadmin->save();

        return $superadmin;
    }

    private function createPendingPendaftaran(): Pendaftaran
    {
        $peserta = new User;
        $peserta->nama = 'Peserta Testing Queue';
        $peserta->email = 'peserta.queue.test@example.com';
        $peserta->password = 'Password123!';
        $peserta->role = RoleEnum::Peserta;
        $peserta->status_aktif = true;
        $peserta->must_change_password = false;
        $peserta->email_verified_at = now();
        $peserta->save();

        $profil = new PesertaProfil;
        $profil->user_id = $peserta->id;
        $profil->nim_nisn = '1234567890';
        $profil->institusi = 'Universitas Gadjah Mada';
        $profil->jenjang = JenjangEnum::S1;
        $profil->no_hp = '081234567890';
        $profil->jurusan_id = Jurusan::first()?->id ?? 1;
        $profil->save();

        $bulan = BulanMagang::where('aktif', true)->first() ?? BulanMagang::first();
        $kuota = Kuota::where('bulan_magang_id', $bulan->id)->first() ?? Kuota::first();

        $pendaftaran = new Pendaftaran;
        $pendaftaran->user_id = $peserta->id;
        $pendaftaran->bulan_magang_id = $bulan->id;
        $pendaftaran->kuota_id = $kuota->id;
        $pendaftaran->durasi_bulan = 1;
        $pendaftaran->status = StatusPendaftaranEnum::Diajukan;
        $pendaftaran->diajukan_at = now();
        $pendaftaran->save();

        return $pendaftaran;
    }

    /**
     * Memastikan dashboard admin menggunakan istilah Bulan dan Pilih Bulan.
     */
    public function test_admin_dashboard_displays_bulan_terminology(): void
    {
        $admin = $this->createAdminUser();
        $this->createPendingPendaftaran();

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));
        $response->assertStatus(200);

        // Memastikan terminologi baru tampil
        $response->assertSee('Pilih Bulan:', false);
        $response->assertSee('Bulan Magang', false);
        $response->assertSee('Bulan:', false);

        // Memastikan terminologi lama tidak muncul
        $response->assertDontSee('Pilih Periode:', false);
        $response->assertDontSee('Periode Magang', false);
        $response->assertDontSee('Jadwal Pendaftaran Periode Ini', false);
    }

    /**
     * Memastikan modul kuota dan pendaftaran admin menggunakan istilah Bulan Magang.
     */
    public function test_admin_kuota_and_pendaftaran_display_bulan_terminology(): void
    {
        $admin = $this->createAdminUser();

        // Modul Kuota
        $kuotaResponse = $this->actingAs($admin)->get(route('admin.kuota.index'));
        $kuotaResponse->assertStatus(200);
        $kuotaResponse->assertSee('Bulan Magang:', false);
        $kuotaResponse->assertDontSee('Semua Periode', false);
        $kuotaResponse->assertDontSee('Periode Magang:', false);

        // Modul Pendaftaran
        $pendaftaranResponse = $this->actingAs($admin)->get(route('admin.pendaftaran.index'));
        $pendaftaranResponse->assertStatus(200);
        $pendaftaranResponse->assertSee('Verifikasi Berkas Pendaftaran Peserta', false);
        $pendaftaranResponse->assertDontSee('Seleksi &amp; Verifikasi Berkas', false);
        $pendaftaranResponse->assertDontSee('Seleksi & Verifikasi Berkas', false);
        $pendaftaranResponse->assertSee('Bulan Magang', false);
        $pendaftaranResponse->assertSee('Semua Bulan', false);
        $pendaftaranResponse->assertDontSee('Periode Magang', false);
        $pendaftaranResponse->assertDontSee('Semua Periode', false);
    }

    /**
     * Memastikan modul waiting list admin menggunakan istilah Bulan Magang.
     */
    public function test_admin_waiting_list_displays_bulan_terminology(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)->get(route('admin.waiting-list.index'));
        $response->assertStatus(200);

        $response->assertSee('Bulan Magang Terdampak', false);
        $response->assertSee('Semua Bulan Magang', false);
        $response->assertDontSee('Periode Magang Terdampak', false);
        $response->assertDontSee('Semua Periode Bulan', false);
    }

    /**
     * Memastikan portal superadmin menampilkan terminologi Bulan Magang secara konsisten.
     */
    public function test_superadmin_portal_displays_bulan_terminology(): void
    {
        $superadmin = $this->createSuperAdminUser();

        // Dashboard Superadmin
        $dashResponse = $this->actingAs($superadmin)->get(route('superadmin.dashboard'));
        $dashResponse->assertStatus(200);
        $dashResponse->assertSee('+ Bulan Magang', false);
        $dashResponse->assertSee('Bulan:', false);
        $dashResponse->assertDontSee('+ Periode Magang', false);
        $dashResponse->assertDontSee('Periode: <strong', false);

        // Manajemen Bulan
        $bulanResponse = $this->actingAs($superadmin)->get(route('superadmin.bulan.index'));
        $bulanResponse->assertStatus(200);
        $bulanResponse->assertSee('Tambah Bulan Magang', false);
        $bulanResponse->assertSee('Daftar Bulan Magang', false);
        $bulanResponse->assertDontSee('Tambah Periode Bulan', false);
        $bulanResponse->assertDontSee('Daftar Periode Bulan Magang', false);

        // Slot Khusus
        $slotResponse = $this->actingAs($superadmin)->get(route('superadmin.slot-khusus.index'));
        $slotResponse->assertStatus(200);
        $slotResponse->assertSee('Bulan Magang', false);
        $slotResponse->assertDontSee('Bulan Periode', false);
    }
}
