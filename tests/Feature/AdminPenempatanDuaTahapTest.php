<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Enums\StatusPendaftaranEnum;
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

class AdminPenempatanDuaTahapTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $pembimbing;

    protected Bidang $bidangEgov;

    protected Bidang $bidangIti;

    protected BulanMagang $bulanAktif;

    protected BulanMagang $bulanLain;

    protected Jurusan $jurusan;

    protected Kuota $kuota;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bidangEgov = Bidang::create([
            'nama_bidang' => 'Layanan E-Government',
            'kode_bidang' => 'EGOV',
            'aktif' => true,
        ]);

        $this->bidangIti = Bidang::create([
            'nama_bidang' => 'Infrastruktur dan TI',
            'kode_bidang' => 'ITI',
            'aktif' => true,
        ]);

        $this->admin = User::factory()->create([
            'role' => RoleEnum::Admin,
            'status_aktif' => true,
        ]);

        $this->pembimbing = User::factory()->create([
            'nama' => 'Budi Mentor Egov',
            'role' => RoleEnum::Pembimbing,
            'bidang_id' => $this->bidangEgov->id,
            'status_aktif' => true,
        ]);

        $this->bulanAktif = BulanMagang::create([
            'nama_bulan' => 'November 2026',
            'bulan_mulai' => '2026-11-01',
            'tgl_buka' => '2026-10-01',
            'tgl_tutup' => '2026-10-25',
            'aktif' => true,
        ]);

        $this->bulanLain = BulanMagang::create([
            'nama_bulan' => 'Desember 2026',
            'bulan_mulai' => '2026-12-01',
            'tgl_buka' => '2026-11-01',
            'tgl_tutup' => '2026-11-25',
            'aktif' => true,
        ]);

        $this->jurusan = Jurusan::create([
            'nama_jurusan' => 'Informatika',
            'kategori' => 'mahasiswa',
            'bidang_id' => $this->bidangEgov->id,
            'aktif' => true,
        ]);

        $this->kuota = Kuota::create([
            'bulan_magang_id' => $this->bulanAktif->id,
            'jurusan_id' => $this->jurusan->id,
            'jumlah' => 10,
            'terisi' => 0,
            'aktif' => true,
        ]);
    }

    public function test_admin_can_accept_application_without_selecting_pembimbing(): void
    {
        $peserta = User::factory()->create([
            'role' => RoleEnum::Peserta,
            'status_aktif' => true,
        ]);

        $pendaftaran = Pendaftaran::create([
            'user_id' => $peserta->id,
            'kuota_id' => $this->kuota->id,
            'bulan_magang_id' => $this->bulanAktif->id,
            'status' => StatusPendaftaranEnum::Diajukan,
            'durasi_bulan' => 3,
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.pendaftaran.putuskan', $pendaftaran), [
                'keputusan' => 'terima',
                'catatan' => 'Berkas lengkap dan sesuai kualifikasi.',
            ]);

        $response->assertRedirect(route('admin.pendaftaran.show', $pendaftaran));
        $response->assertSessionHas('success');

        // Pendaftaran berstatus diterima & kuota teralokasi
        $this->assertDatabaseHas('pendaftaran', [
            'id' => $pendaftaran->id,
            'status' => StatusPendaftaranEnum::Diterima->value,
        ]);

        $this->assertDatabaseHas('kuota', [
            'id' => $this->kuota->id,
            'terisi' => 1,
        ]);

        // Belum ada entri penempatan (karena dua tahap mandiri)
        $this->assertDatabaseMissing('penempatan', [
            'pendaftaran_id' => $pendaftaran->id,
        ]);
    }

    public function test_accepted_application_appears_in_unassigned_queue_on_penempatan_page(): void
    {
        $peserta = User::factory()->create([
            'nama' => 'Calon Peserta Antrean',
            'role' => RoleEnum::Peserta,
            'status_aktif' => true,
        ]);

        $pendaftaran = Pendaftaran::create([
            'user_id' => $peserta->id,
            'kuota_id' => $this->kuota->id,
            'bulan_magang_id' => $this->bulanAktif->id,
            'status' => StatusPendaftaranEnum::Diterima,
            'durasi_bulan' => 3,
            'diputuskan_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.penempatan.index'));

        $response->assertOk();
        $response->assertSee('Penempatan Peserta');
        $response->assertSee('Antrean Peserta Lolos Berkas (Menunggu Penempatan)');
        $response->assertSee('Calon Peserta Antrean');
        $response->assertSee('Tempatkan');
    }

    public function test_admin_can_assign_pembimbing_and_bidang_in_stage_two(): void
    {
        $peserta = User::factory()->create([
            'nama' => 'Peserta Siap Ditempatkan',
            'role' => RoleEnum::Peserta,
            'status_aktif' => true,
        ]);

        $pendaftaran = Pendaftaran::create([
            'user_id' => $peserta->id,
            'kuota_id' => $this->kuota->id,
            'bulan_magang_id' => $this->bulanAktif->id,
            'status' => StatusPendaftaranEnum::Diterima,
            'durasi_bulan' => 3,
            'diputuskan_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.penempatan.tetapkan'), [
                'pendaftaran_id' => $pendaftaran->id,
                'bidang_id' => $this->bidangEgov->id,
                'pembimbing_id' => $this->pembimbing->id,
            ]);

        $response->assertRedirect(route('admin.penempatan.index'));
        $response->assertSessionHas('success');

        // Entri penempatan resmi terbentuk
        $this->assertDatabaseHas('penempatan', [
            'pendaftaran_id' => $pendaftaran->id,
            'peserta_id' => $peserta->id,
            'bidang_id' => $this->bidangEgov->id,
            'pembimbing_id' => $this->pembimbing->id,
            'status' => StatusPenempatanEnum::Aktif->value,
        ]);
    }

    public function test_penempatan_page_filters_by_bulan_magang_correctly(): void
    {
        $peserta1 = User::factory()->create(['nama' => 'Peserta November']);
        $pendaftaran1 = Pendaftaran::create([
            'user_id' => $peserta1->id,
            'kuota_id' => $this->kuota->id,
            'bulan_magang_id' => $this->bulanAktif->id,
            'status' => StatusPendaftaranEnum::Diterima,
            'durasi_bulan' => 3,
        ]);
        Penempatan::create([
            'pendaftaran_id' => $pendaftaran1->id,
            'peserta_id' => $peserta1->id,
            'kuota_id' => $this->kuota->id,
            'bidang_id' => $this->bidangEgov->id,
            'pembimbing_id' => $this->pembimbing->id,
            'jalur' => 'umum',
            'sumber' => 'online',
            'tgl_mulai' => '2026-11-01',
            'tgl_selesai' => '2027-01-31',
            'status' => StatusPenempatanEnum::Aktif,
        ]);

        $kuotaDesember = Kuota::create([
            'bulan_magang_id' => $this->bulanLain->id,
            'jurusan_id' => $this->jurusan->id,
            'jumlah' => 5,
            'terisi' => 1,
            'aktif' => true,
        ]);
        $peserta2 = User::factory()->create(['nama' => 'Peserta Desember']);
        $pendaftaran2 = Pendaftaran::create([
            'user_id' => $peserta2->id,
            'kuota_id' => $kuotaDesember->id,
            'bulan_magang_id' => $this->bulanLain->id,
            'status' => StatusPendaftaranEnum::Diterima,
            'durasi_bulan' => 3,
        ]);
        Penempatan::create([
            'pendaftaran_id' => $pendaftaran2->id,
            'peserta_id' => $peserta2->id,
            'kuota_id' => $kuotaDesember->id,
            'bidang_id' => $this->bidangEgov->id,
            'pembimbing_id' => $this->pembimbing->id,
            'jalur' => 'umum',
            'sumber' => 'online',
            'tgl_mulai' => '2026-12-01',
            'tgl_selesai' => '2027-02-28',
            'status' => StatusPenempatanEnum::Aktif,
        ]);

        // Filter November
        $responseNov = $this->actingAs($this->admin)
            ->get(route('admin.penempatan.index', ['bulan_magang_id' => $this->bulanAktif->id]));
        $responseNov->assertOk();
        $responseNov->assertSee('Peserta November');
        $responseNov->assertDontSee('Peserta Desember');

        // Filter Desember
        $responseDes = $this->actingAs($this->admin)
            ->get(route('admin.penempatan.index', ['bulan_magang_id' => $this->bulanLain->id]));
        $responseDes->assertOk();
        $responseDes->assertSee('Peserta Desember');
        $responseDes->assertDontSee('Peserta November');
    }

    public function test_pembimbing_dropdown_displays_bidang_label(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.penempatan.index'));
        $response->assertOk();
        $response->assertSee('Budi Mentor Egov [Layanan E-Government]');
    }
}
