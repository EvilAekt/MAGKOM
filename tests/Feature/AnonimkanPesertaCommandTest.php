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
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AnonimkanPesertaCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_cannot_anonymize_non_existent_user(): void
    {
        $this->artisan('magkom:anonimkan-peserta 999999')
            ->assertFailed()
            ->expectsOutputToContain('tidak ditemukan dalam database');
    }

    public function test_cannot_anonymize_non_peserta_staff(): void
    {
        $admin = new User;
        $admin->nama = 'Admin Test';
        $admin->email = 'admin.anon@test.com';
        $admin->password = 'secret123';
        $admin->role = RoleEnum::Admin;
        $admin->email_verified_at = now();
        $admin->status_aktif = true;
        $admin->save();

        $this->artisan("magkom:anonimkan-peserta {$admin->id}")
            ->assertFailed()
            ->expectsOutputToContain('bukan peserta magang');
    }

    public function test_cannot_anonymize_actively_placed_participant(): void
    {
        $peserta = User::create([
            'nama' => 'Peserta Aktif',
            'email' => 'peserta.aktif@test.com',
            'password' => 'secret123',
            'role' => RoleEnum::Peserta,
            'email_verified_at' => now(),
            'status_aktif' => true,
        ]);

        $bidang = Bidang::create(['nama_bidang' => 'Informatika', 'aktif' => true]);
        $pembimbing = new User;
        $pembimbing->nama = 'Pembimbing Test';
        $pembimbing->email = 'pembimbing@test.com';
        $pembimbing->password = 'secret123';
        $pembimbing->role = RoleEnum::Pembimbing;
        $pembimbing->email_verified_at = now();
        $pembimbing->status_aktif = true;
        $pembimbing->save();

        Penempatan::create([
            'peserta_id' => $peserta->id,
            'pembimbing_id' => $pembimbing->id,
            'bidang_id' => $bidang->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'status' => StatusPenempatanEnum::Aktif,
            'tgl_mulai' => now()->subDays(10)->toDateString(),
            'tgl_selesai' => now()->addDays(20)->toDateString(),
            'nomor_surat' => 'NOMOR/01/2026',
        ]);

        $this->artisan("magkom:anonimkan-peserta {$peserta->id}")
            ->assertFailed()
            ->expectsOutputToContain('sedang aktif menjalani magang');
    }

    public function test_successfully_anonymizes_alumni_peserta_pii_and_records_audit(): void
    {
        Storage::fake('public');
        Storage::fake('private');

        $jurusan = Jurusan::create([
            'nama_jurusan' => 'Teknik Komputer',
            'kategori' => 'siswa',
            'aktif' => true,
        ]);

        $peserta = User::create([
            'nama' => 'Ahmad Fulan',
            'email' => 'ahmad.fulan@test.com',
            'password' => 'secret123',
            'role' => RoleEnum::Peserta,
            'email_verified_at' => now(),
            'status_aktif' => true,
        ]);

        $profil = PesertaProfil::create([
            'user_id' => $peserta->id,
            'nim_nisn' => '1234567890',
            'institusi' => 'SMKN 1 Bantul',
            'jurusan_id' => $jurusan->id,
            'jenjang' => 'smk',
            'no_hp' => '08123456789',
            'nama_kontak_darurat' => 'Bapak Fulan',
            'no_kontak_darurat' => '08987654321',
        ]);

        $this->artisan("magkom:anonimkan-peserta {$peserta->id} --alasan=\"Permintaan Hak Lupa Alumni\"")
            ->assertSuccessful()
            ->expectsOutputToContain("Berhasil melakukan anonimisasi akun peserta #{$peserta->id}");

        $pesertaFresh = $peserta->fresh();
        $this->assertSame("Peserta Teranonimisasi #{$peserta->id}", $pesertaFresh->nama);
        $this->assertSame("anonymized_{$peserta->id}@magkom.internal", $pesertaFresh->email);
        $this->assertFalse($pesertaFresh->status_aktif);

        $profilFresh = $profil->fresh();
        $this->assertSame("ANON-{$peserta->id}", $profilFresh->nim_nisn);
        $this->assertSame('080000000000', $profilFresh->no_hp);
        $this->assertNull($profilFresh->nama_kontak_darurat);

        $this->assertDatabaseHas('audit_log', [
            'aksi' => 'Anonimisasi Data Peserta UU PDP',
            'entitas_id' => $peserta->id,
        ]);
    }
}
