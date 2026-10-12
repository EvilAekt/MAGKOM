<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Enums\StatusPenempatanEnum;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\Kuota;
use App\Models\Pendaftaran;
use App\Models\Penempatan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PesertaAksesFiturMagangTest extends TestCase
{
    use RefreshDatabase;

    private function createPeserta(string $email = 'calon@magkom.test'): User
    {
        $user = new User([
            'nama' => 'Calon Peserta Magang',
            'email' => $email,
            'password' => 'Password123!',
            'status_aktif' => true,
            'must_change_password' => false,
        ]);
        $user->role = RoleEnum::Peserta;
        $user->email_verified_at = now();
        $user->save();

        return $user;
    }

    /**
     * Memastikan peserta yang belum diterima dilarang mengakses modul logbook, tugas, dan nilai.
     */
    public function test_peserta_belum_diterima_dilarang_akses_fitur_magang(): void
    {
        $calonPeserta = $this->createPeserta('calon.akses@magkom.test');

        // Cek akses logbook
        $responseLogbook = $this->actingAs($calonPeserta)->get(route('peserta.logbook.index'));
        $responseLogbook->assertRedirect(route('peserta.dashboard'));
        $responseLogbook->assertSessionHas('warning');

        // Cek akses tugas
        $responseTugas = $this->actingAs($calonPeserta)->get(route('peserta.tugas.index'));
        $responseTugas->assertRedirect(route('peserta.dashboard'));
        $responseTugas->assertSessionHas('warning');

        // Cek akses lembar nilai
        $responseNilai = $this->actingAs($calonPeserta)->get(route('peserta.nilai.show'));
        $responseNilai->assertRedirect(route('peserta.dashboard'));
        $responseNilai->assertSessionHas('warning');
    }

    /**
     * Memastikan permintaan JSON / API ditolak dengan status 403 Forbidden.
     */
    public function test_peserta_belum_diterima_mendapat_403_saat_request_json(): void
    {
        $calonPeserta = $this->createPeserta('calon.json@magkom.test');

        $response = $this->actingAs($calonPeserta)->getJson(route('peserta.logbook.index'));
        $response->assertStatus(403);
    }

    /**
     * Memastikan peserta yang sudah diterima dan memiliki penempatan aktif dapat mengakses fitur magang.
     */
    public function test_peserta_diterima_dapat_mengakses_fitur_magang(): void
    {
        $pesertaDiterima = $this->createPeserta('peserta.aktif@magkom.test');

        $pembimbing = new User([
            'nama' => 'Pembimbing Lapangan',
            'email' => 'pembimbing.test@magkom.test',
            'password' => 'Password123!',
            'status_aktif' => true,
            'must_change_password' => false,
        ]);
        $pembimbing->role = RoleEnum::Pembimbing;
        $pembimbing->email_verified_at = now();
        $pembimbing->save();

        $bidang = Bidang::create([
            'nama_bidang' => 'Aptika',
            'deskripsi' => 'Bidang Aptika',
            'aktif' => true,
        ]);

        $jurusan = Jurusan::create([
            'nama_jurusan' => 'Informatika',
            'kategori' => 'perguruan_tinggi',
            'aktif' => true,
        ]);

        $bulan = BulanMagang::create([
            'bulan_mulai' => '2027-01-01',
            'tgl_buka' => '2026-11-01',
            'tgl_tutup' => '2026-11-30',
            'aktif' => true,
        ]);

        $kuota = Kuota::create([
            'bulan_magang_id' => $bulan->id,
            'jurusan_id' => $jurusan->id,
            'jumlah' => 5,
            'terisi' => 1,
            'aktif' => true,
        ]);

        $pendaftaran = Pendaftaran::create([
            'user_id' => $pesertaDiterima->id,
            'kuota_id' => $kuota->id,
            'bulan_magang_id' => $bulan->id,
            'jalur' => \App\Enums\JalurEnum::Umum,
            'durasi_bulan' => 3,
            'status' => \App\Enums\StatusPendaftaranEnum::Diterima,
        ]);

        Penempatan::create([
            'pendaftaran_id' => $pendaftaran->id,
            'peserta_id' => $pesertaDiterima->id,
            'pembimbing_id' => $pembimbing->id,
            'bidang_id' => $bidang->id,
            'jalur' => \App\Enums\JalurEnum::Umum,
            'sumber' => \App\Enums\SumberPenempatanEnum::Online,
            'tgl_mulai' => now()->subDay()->format('Y-m-d'),
            'tgl_selesai' => now()->addMonths(3)->format('Y-m-d'),
            'status' => StatusPenempatanEnum::Aktif,
        ]);

        $this->actingAs($pesertaDiterima)
            ->get(route('peserta.logbook.index'))
            ->assertStatus(200);

        $this->actingAs($pesertaDiterima)
            ->get(route('peserta.tugas.index'))
            ->assertStatus(200);

        $this->actingAs($pesertaDiterima)
            ->get(route('peserta.nilai.show'))
            ->assertStatus(200);
    }

    /**
     * Memastikan sidebar menampilkan indikator status terkunci bagi peserta belum diterima.
     */
    public function test_sidebar_menampilkan_menu_terkunci_bagi_peserta_belum_diterima(): void
    {
        $calonPeserta = $this->createPeserta('calon.sidebar@magkom.test');

        $response = $this->actingAs($calonPeserta)->get(route('peserta.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('badge-locked');
        $response->assertSee('Terkunci');
    }
}
