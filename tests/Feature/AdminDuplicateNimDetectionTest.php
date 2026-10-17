<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Enums\StatusPendaftaranEnum;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\Kuota;
use App\Models\Pendaftaran;
use App\Models\PesertaProfil;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDuplicateNimDetectionTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected BulanMagang $bulan;
    protected Jurusan $jurusan;
    protected Kuota $kuota;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = new User();
        $this->admin->nama = 'Admin Verifikator';
        $this->admin->email = 'admin.verif@magkom.test';
        $this->admin->password = 'secret123';
        $this->admin->role = RoleEnum::Admin;
        $this->admin->email_verified_at = now();
        $this->admin->status_aktif = true;
        $this->admin->save();

        $this->bulan = BulanMagang::create([
            'bulan_mulai' => '2026-11-01',
            'tgl_buka' => '2026-10-01',
            'tgl_tutup' => '2026-10-25',
            'aktif' => true,
        ]);

        $this->jurusan = Jurusan::create([
            'nama_jurusan' => 'Informatika',
            'kategori' => 'mahasiswa',
            'aktif' => true,
        ]);

        $this->kuota = Kuota::create([
            'bulan_magang_id' => $this->bulan->id,
            'jurusan_id' => $this->jurusan->id,
            'jumlah' => 10,
            'terisi' => 0,
        ]);
    }

    public function test_no_warning_when_nim_is_unique(): void
    {
        $user1 = new User();
        $user1->nama = 'Peserta Tunggal';
        $user1->email = 'peserta1@magkom.test';
        $user1->password = 'secret123';
        $user1->role = RoleEnum::Peserta;
        $user1->email_verified_at = now();
        $user1->status_aktif = true;
        $user1->save();

        PesertaProfil::create([
            'user_id' => $user1->id,
            'nim_nisn' => 'UNIQUE-998877',
            'institusi' => 'UGM',
            'jurusan_id' => $this->jurusan->id,
            'jenjang' => 's1',
            'no_hp' => '08123456789',
        ]);

        $pendaftaran = Pendaftaran::create([
            'user_id' => $user1->id,
            'bulan_magang_id' => $this->bulan->id,
            'kuota_id' => $this->kuota->id,
            'nomor_pendaftaran' => 'REG-2026-001',
            'durasi_bulan' => 1,
            'status' => StatusPendaftaranEnum::Diajukan,
            'diajukan_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.pendaftaran.show', $pendaftaran));

        $response->assertStatus(200);
        $response->assertDontSee('Peringatan Keamanan', false);
    }

    public function test_displays_warning_alert_when_duplicate_nim_detected_across_accounts(): void
    {
        $sharedNim = '2100018999';

        // Akun peserta 1
        $user1 = new User();
        $user1->nama = 'Budi Satu';
        $user1->email = 'budi1@magkom.test';
        $user1->password = 'secret123';
        $user1->role = RoleEnum::Peserta;
        $user1->email_verified_at = now();
        $user1->status_aktif = true;
        $user1->save();

        PesertaProfil::create([
            'user_id' => $user1->id,
            'nim_nisn' => $sharedNim,
            'institusi' => 'UGM',
            'jurusan_id' => $this->jurusan->id,
            'jenjang' => 's1',
            'no_hp' => '08123456789',
        ]);

        $pendaftaran = Pendaftaran::create([
            'user_id' => $user1->id,
            'bulan_magang_id' => $this->bulan->id,
            'kuota_id' => $this->kuota->id,
            'nomor_pendaftaran' => 'REG-2026-002',
            'durasi_bulan' => 1,
            'status' => StatusPendaftaranEnum::Diajukan,
            'diajukan_at' => now(),
        ]);

        // Akun peserta 2 dengan NIM sama
        $user2 = new User();
        $user2->nama = 'Budi Dua';
        $user2->email = 'budi2@magkom.test';
        $user2->password = 'secret123';
        $user2->role = RoleEnum::Peserta;
        $user2->email_verified_at = now();
        $user2->status_aktif = true;
        $user2->save();

        PesertaProfil::create([
            'user_id' => $user2->id,
            'nim_nisn' => $sharedNim,
            'institusi' => 'UGM',
            'jurusan_id' => $this->jurusan->id,
            'jenjang' => 's1',
            'no_hp' => '08999999999',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.pendaftaran.show', $pendaftaran));

        $response->assertStatus(200);
        $response->assertSee('Peringatan Keamanan', false);
        $response->assertSee('1 akun peserta lainnya', false);
        $response->assertSee($sharedNim, false);
    }
}
