<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JalurEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\SumberPenempatanEnum;
use App\Models\Bidang;
use App\Models\Jurusan;
use App\Models\Penempatan;
use App\Models\PesertaProfil;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlacementCompletionTest extends TestCase
{
    use RefreshDatabase;

    protected User $pembimbing;

    protected User $peserta;

    protected User $superAdmin;

    protected User $admin;

    protected Penempatan $penempatan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pembimbing = new User;
        $this->pembimbing->nama = 'Pembimbing Lapangan';
        $this->pembimbing->email = 'pembimbing.lapangan@gmail.com';
        $this->pembimbing->password = 'secret123';
        $this->pembimbing->role = RoleEnum::Pembimbing;
        $this->pembimbing->email_verified_at = now();
        $this->pembimbing->status_aktif = true;
        $this->pembimbing->must_change_password = false;
        $this->pembimbing->save();

        $this->peserta = new User;
        $this->peserta->nama = 'Peserta Magang';
        $this->peserta->email = 'peserta.magang@gmail.com';
        $this->peserta->password = 'secret123';
        $this->peserta->role = RoleEnum::Peserta;
        $this->peserta->email_verified_at = now();
        $this->peserta->status_aktif = true;
        $this->peserta->must_change_password = false;
        $this->peserta->save();

        $jurusan = Jurusan::firstOrCreate(
            ['nama_jurusan' => 'Rekayasa Perangkat Lunak'],
            ['kategori' => 'smk', 'aktif' => true]
        );

        PesertaProfil::create([
            'user_id' => $this->peserta->id,
            'nim_nisn' => '1234567890',
            'institusi' => 'SMK Negeri 1 Sleman',
            'jenjang' => 'smk',
            'jurusan_id' => $jurusan->id,
            'no_hp' => '08123456789',
        ]);

        $this->superAdmin = new User;
        $this->superAdmin->nama = 'Super Administrator';
        $this->superAdmin->email = 'superadmin.audit@gmail.com';
        $this->superAdmin->password = 'secret123';
        $this->superAdmin->role = RoleEnum::SuperAdmin;
        $this->superAdmin->email_verified_at = now();
        $this->superAdmin->status_aktif = true;
        $this->superAdmin->must_change_password = false;
        $this->superAdmin->save();

        $this->admin = new User;
        $this->admin->nama = 'Admin Verifikator';
        $this->admin->email = 'admin.verifikator@gmail.com';
        $this->admin->password = 'secret123';
        $this->admin->role = RoleEnum::Admin;
        $this->admin->email_verified_at = now();
        $this->admin->status_aktif = true;
        $this->admin->must_change_password = false;
        $this->admin->save();

        $bidang = Bidang::firstOrCreate(
            ['nama_bidang' => 'Aplikasi Informatika'],
            ['aktif' => true]
        );

        $this->penempatan = Penempatan::create([
            'peserta_id' => $this->peserta->id,
            'pembimbing_id' => $this->pembimbing->id,
            'bidang_id' => $bidang->id,
            'jalur' => JalurEnum::Khusus,
            'sumber' => SumberPenempatanEnum::Manual,
            'tgl_mulai' => Carbon::today()->startOfMonth(),
            'tgl_selesai' => Carbon::today()->addMonths(3)->endOfMonth(),
            'status' => StatusPenempatanEnum::Aktif,
        ]);
    }

    public function test_pembimbing_bisa_menyelesaikan_magang_langsung_tanpa_syarat_penilaian(): void
    {
        $response = $this->actingAs($this->pembimbing)
            ->postJson(route('pembimbing.penempatan.selesai', $this->penempatan));

        $response->assertStatus(200);
        $this->penempatan->refresh();
        $this->assertEquals(StatusPenempatanEnum::Selesai, $this->penempatan->status);
        $this->assertNotNull($this->penempatan->tgl_ditetapkan_selesai);
        $this->assertNotNull($this->penempatan->tgl_nonaktif);
        $this->assertTrue(
            $this->penempatan->tgl_nonaktif->gt(Carbon::today()),
            'Tanggal nonaktif harus di masa depan sesuai masa tenggang'
        );
    }

    public function test_superadmin_read_only_dilarang_mengubah_status_penempatan(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->postJson(route('pembimbing.penempatan.selesai', $this->penempatan));

        $response->assertStatus(403);
    }

    public function test_scheduler_nonaktifkan_peserta_selesai_berjalan_idempotent_br_07(): void
    {
        $this->penempatan->status = StatusPenempatanEnum::Selesai;
        $this->penempatan->tgl_ditetapkan_selesai = Carbon::today()->subMonths(2);
        $this->penempatan->tgl_nonaktif = Carbon::today()->subDays(1);
        $this->penempatan->save();

        $this->assertTrue($this->peserta->status_aktif);

        $exitCode = $this->artisan('magkom:nonaktifkan-peserta')->run();
        $this->assertEquals(0, $exitCode);

        $this->penempatan->refresh();
        $this->peserta->refresh();

        $this->assertEquals(StatusPenempatanEnum::Nonaktif, $this->penempatan->status);
        $this->assertFalse($this->peserta->status_aktif);

        $exitCode2 = $this->artisan('magkom:nonaktifkan-peserta')->run();
        $this->assertEquals(0, $exitCode2);
    }
}
