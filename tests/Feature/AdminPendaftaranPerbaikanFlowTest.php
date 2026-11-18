<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JenjangEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusPendaftaranEnum;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\Kuota;
use App\Models\Pendaftaran;
use App\Models\PesertaProfil;
use App\Models\User;
use Database\Seeders\JenisDokumenSeeder;
use Database\Seeders\RubrikSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPendaftaranPerbaikanFlowTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $superAdmin;

    protected User $peserta;

    protected Bidang $bidang;

    protected Jurusan $jurusan;

    protected BulanMagang $bulan;

    protected Kuota $kuota;

    protected Pendaftaran $pendaftaran;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(JenisDokumenSeeder::class);
        $this->seed(RubrikSeeder::class);

        $this->bidang = Bidang::create([
            'nama_bidang' => 'Aplikasi Informatika (EGOV)',
            'deskripsi' => 'Bidang Layanan EGOV',
            'aktif' => true,
        ]);

        $this->jurusan = Jurusan::create([
            'nama_jurusan' => 'Teknik Informatika',
            'kategori' => 's1',
            'aktif' => true,
        ]);

        $this->bulan = BulanMagang::create([
            'bulan_mulai' => now()->addMonth()->startOfMonth(),
            'tgl_buka' => now()->subDays(5)->toDateString(),
            'tgl_tutup' => now()->addDays(20)->toDateString(),
            'aktif' => true,
        ]);

        $this->kuota = Kuota::create([
            'bulan_magang_id' => $this->bulan->id,
            'jurusan_id' => $this->jurusan->id,
            'jumlah' => 10,
            'terisi' => 1,
            'aktif' => true,
        ]);

        $this->admin = new User;
        $this->admin->nama = 'Admin Verifikator';
        $this->admin->email = 'admin.verifikator@slemankab.go.id';
        $this->admin->password = 'Password123!';
        $this->admin->role = RoleEnum::Admin;
        $this->admin->status_aktif = true;
        $this->admin->email_verified_at = now();
        $this->admin->save();

        $this->superAdmin = new User;
        $this->superAdmin->nama = 'Super Admin';
        $this->superAdmin->email = 'superadmin.utama@slemankab.go.id';
        $this->superAdmin->password = 'Password123!';
        $this->superAdmin->role = RoleEnum::SuperAdmin;
        $this->superAdmin->status_aktif = true;
        $this->superAdmin->email_verified_at = now();
        $this->superAdmin->save();

        $this->peserta = new User;
        $this->peserta->nama = 'Budi Mahasiswa';
        $this->peserta->email = 'budi.peserta@test.com';
        $this->peserta->password = 'Password123!';
        $this->peserta->role = RoleEnum::Peserta;
        $this->peserta->status_aktif = true;
        $this->peserta->email_verified_at = now();
        $this->peserta->save();

        PesertaProfil::create([
            'user_id' => $this->peserta->id,
            'nim_nisn' => '2201010011',
            'institusi' => 'Universitas Gadjah Mada',
            'jurusan_id' => $this->jurusan->id,
            'jenjang' => JenjangEnum::S1,
            'no_hp' => '081234567890',
            'nama_kontak_darurat' => 'Siti Wali',
            'no_kontak_darurat' => '089876543210',
            'hubungan_kontak_darurat' => 'Orang Tua',
        ]);

        $this->pendaftaran = Pendaftaran::create([
            'user_id' => $this->peserta->id,
            'kuota_id' => $this->kuota->id,
            'bulan_magang_id' => $this->bulan->id,
            'status' => StatusPendaftaranEnum::Diajukan,
            'tipe' => 'mandiri',
            'durasi_bulan' => 3,
            'diajukan_at' => now(),
        ]);
    }

    public function test_halaman_show_pendaftaran_hanya_menampilkan_opsi_terima_dan_tolak(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.pendaftaran.show', $this->pendaftaran));

        $response->assertOk();
        $response->assertSee('tab-btn-terima');
        $response->assertSee('tab-btn-tolak');
        $response->assertDontSee('tab-btn-perlu_perbaikan');
        $response->assertDontSee('action-panel-perlu_perbaikan');
        $response->assertDontSee('catatan_perbaikan');
        $response->assertDontSee('Minta Perbaikan Berkas');

        // Verifikasi Modal Konfirmasi Berstandar DS-MagKom (Bebas Native window.confirm)
        $response->assertSee('modalKonfirmasiKeputusanAdmin');
        $response->assertSee('confirmModalTitle');
        $response->assertSee('confirmModalCallout');
        $response->assertSee('confirmModalCancelBtn');
        $response->assertSee('confirmModalExecuteBtn');
        $response->assertDontSee('data-confirm');

        // Verifikasi Material Floating Card Box (Paritas Pembimbing)
        $response->assertSee('adm-floating-field');
        $response->assertSee('box_catatan_terima');
        $response->assertSee('box_catatan_tolak');
        $response->assertSee('adm-floating-label');
        $response->assertSee('adm-floating-textarea');
    }

    public function test_keputusan_perlu_perbaikan_ditolak_oleh_validasi(): void
    {
        $response = $this->actingAs($this->admin)
            ->from(route('admin.pendaftaran.show', $this->pendaftaran))
            ->post(route('admin.pendaftaran.putuskan', $this->pendaftaran), [
                'keputusan' => 'perlu_perbaikan',
                'catatan' => 'Mohon unggah ulang proposal magang yang telah bertanda tangan dekan fakultas.',
            ]);

        $response->assertRedirect(route('admin.pendaftaran.show', $this->pendaftaran));
        $response->assertSessionHasErrors('keputusan');

        $this->pendaftaran->refresh();
        $this->assertSame(StatusPendaftaranEnum::Diajukan, $this->pendaftaran->status);
    }

    public function test_admin_berhasil_menolak_berkas_dengan_catatan_valid(): void
    {
        $catatanTolak = 'Berkas persyaratan tidak memenuhi kriteria penerimaan dinas.';

        $response = $this->actingAs($this->admin)
            ->post(route('admin.pendaftaran.putuskan', $this->pendaftaran), [
                'keputusan' => 'tolak',
                'catatan' => $catatanTolak,
            ]);

        $response->assertRedirect(route('admin.pendaftaran.show', $this->pendaftaran));
        $response->assertSessionHas('success');

        $this->pendaftaran->refresh();
        $this->assertSame(StatusPendaftaranEnum::Ditolak, $this->pendaftaran->status);
        $this->assertSame($catatanTolak, $this->pendaftaran->catatan);
    }

    public function test_menolak_berkas_tanpa_catatan_memicu_validasi_error(): void
    {
        $response = $this->actingAs($this->admin)->from(route('admin.pendaftaran.show', $this->pendaftaran))->post(route('admin.pendaftaran.putuskan', $this->pendaftaran), [
            'keputusan' => 'tolak',
            'catatan' => '',
        ]);

        $response->assertRedirect(route('admin.pendaftaran.show', $this->pendaftaran));
        $response->assertSessionHasErrors('catatan');

        $this->pendaftaran->refresh();
        $this->assertSame(StatusPendaftaranEnum::Diajukan, $this->pendaftaran->status);
    }

    public function test_menolak_berkas_dengan_catatan_kurang_dari_10_karakter_memicu_error(): void
    {
        $response = $this->actingAs($this->admin)->from(route('admin.pendaftaran.show', $this->pendaftaran))->post(route('admin.pendaftaran.putuskan', $this->pendaftaran), [
            'keputusan' => 'tolak',
            'catatan' => 'Pendek',
        ]);

        $response->assertRedirect(route('admin.pendaftaran.show', $this->pendaftaran));
        $response->assertSessionHasErrors('catatan');

        $this->pendaftaran->refresh();
        $this->assertSame(StatusPendaftaranEnum::Diajukan, $this->pendaftaran->status);
    }

    public function test_superadmin_read_only_tidak_dapat_memutuskan_pendaftaran(): void
    {
        $response = $this->actingAs($this->superAdmin)->post(route('admin.pendaftaran.putuskan', $this->pendaftaran), [
            'keputusan' => 'tolak',
            'catatan' => 'Catatan penolakan dari superadmin resmi.',
        ]);

        $response->assertForbidden();

        $this->pendaftaran->refresh();
        $this->assertSame(StatusPendaftaranEnum::Diajukan, $this->pendaftaran->status);
    }

    public function test_filter_status_perlu_perbaikan_pada_index_antrean(): void
    {
        $this->pendaftaran->update([
            'status' => StatusPendaftaranEnum::PerluPerbaikan,
            'catatan' => 'Perbaiki berkas KTM.',
            'verifikator_id' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.pendaftaran.index', ['status' => 'perlu_perbaikan']));

        $response->assertOk();
        $response->assertSee($this->peserta->nama);
    }

    public function test_halaman_show_pendaftaran_diterima_menampilkan_kartu_keputusan_biru(): void
    {
        $this->pendaftaran->update([
            'status' => StatusPendaftaranEnum::Diterima,
            'catatan' => 'Selamat, Anda dinyatakan Diterima di Bidang E-Gov.',
            'diputuskan_at' => now(),
            'verifikator_id' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.pendaftaran.show', $this->pendaftaran));

        $response->assertOk();
        $response->assertSee('adm-decision-result-card is-diterima');
        $response->assertSee('Pengajuan Telah Selesai Diputuskan');
        $response->assertSee('Selamat, Anda dinyatakan Diterima di Bidang E-Gov.');
        $response->assertSee('adm-decision-result-note');
    }

    public function test_halaman_show_pendaftaran_ditolak_menampilkan_kartu_keputusan_merah(): void
    {
        $this->pendaftaran->update([
            'status' => StatusPendaftaranEnum::Ditolak,
            'catatan' => 'Maaf, berkas tidak memenuhi syarat administratif minimal.',
            'diputuskan_at' => now(),
            'verifikator_id' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.pendaftaran.show', $this->pendaftaran));

        $response->assertOk();
        $response->assertSee('adm-decision-result-card is-ditolak');
        $response->assertSee('Pengajuan Telah Selesai Diputuskan');
        $response->assertSee('Maaf, berkas tidak memenuhi syarat administratif minimal.');
        $response->assertSee('adm-decision-result-note');
    }
}
