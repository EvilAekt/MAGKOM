<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JalurEnum;
use App\Enums\JenjangEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusPendaftaranEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\SumberPenempatanEnum;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Dokumen;
use App\Models\JenisDokumen;
use App\Models\Jurusan;
use App\Models\Kuota;
use App\Models\Penempatan;
use App\Models\Pendaftaran;
use App\Models\PesertaProfil;
use App\Models\User;
use Database\Seeders\JenisDokumenSeeder;
use Database\Seeders\RubrikSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminChildPagesExtendedVisualTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $pembimbing;
    protected User $peserta;
    protected Bidang $bidang;
    protected BulanMagang $bulan;
    protected Jurusan $jurusan;
    protected Kuota $kuota;
    protected Pendaftaran $pendaftaran;
    protected Penempatan $penempatan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(JenisDokumenSeeder::class);
        $this->seed(RubrikSeeder::class);

        $this->bidang = Bidang::create([
            'nama_bidang' => 'Bidang Aplikasi Informatika (Aptika)',
            'deskripsi' => 'Pengembangan sistem informasi',
            'aktif' => true,
        ]);

        $this->admin = new User();
        $this->admin->nama = 'Admin Pengelola';
        $this->admin->email = 'admin.operasional@slemankab.go.id';
        $this->admin->password = 'Password123!';
        $this->admin->role = RoleEnum::Admin;
        $this->admin->status_aktif = true;
        $this->admin->email_verified_at = now();
        $this->admin->save();

        $this->pembimbing = new User();
        $this->pembimbing->nama = 'Pembimbing Lapangan';
        $this->pembimbing->email = 'pembimbing@slemankab.go.id';
        $this->pembimbing->password = 'Password123!';
        $this->pembimbing->role = RoleEnum::Pembimbing;
        $this->pembimbing->bidang_id = $this->bidang->id;
        $this->pembimbing->status_aktif = true;
        $this->pembimbing->email_verified_at = now();
        $this->pembimbing->save();

        $this->jurusan = Jurusan::create([
            'nama_jurusan' => 'Informatika',
            'kategori' => 'mahasiswa',
            'aktif' => true,
        ]);

        $this->bulan = BulanMagang::create([
            'bulan_mulai' => now()->startOfMonth(),
            'tgl_buka' => now()->subDays(10)->toDateString(),
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

        $this->peserta = new User();
        $this->peserta->nama = 'Rian Nugraha';
        $this->peserta->email = 'rian@example.com';
        $this->peserta->password = 'Password123!';
        $this->peserta->role = RoleEnum::Peserta;
        $this->peserta->status_aktif = true;
        $this->peserta->email_verified_at = now();
        $this->peserta->save();

        PesertaProfil::create([
            'user_id' => $this->peserta->id,
            'nim_nisn' => '22110555',
            'institusi' => 'Universitas Gadjah Mada',
            'jurusan_id' => $this->jurusan->id,
            'jenjang' => JenjangEnum::S1,
            'no_hp' => '081234567890',
            'alamat' => 'Sleman, DIY',
        ]);

        $this->pendaftaran = Pendaftaran::create([
            'user_id' => $this->peserta->id,
            'bulan_magang_id' => $this->bulan->id,
            'kuota_id' => $this->kuota->id,
            'nomor_pendaftaran' => 'REG-202610-0099',
            'asal_instansi' => 'Universitas Gadjah Mada',
            'durasi_bulan' => 3,
            'status' => StatusPendaftaranEnum::Diajukan,
            'diajukan_at' => now()->subDays(2),
        ]);

        $jenisCv = JenisDokumen::where('kode', 'cv')->first();
        if ($jenisCv) {
            Dokumen::create([
                'pendaftaran_id' => $this->pendaftaran->id,
                'jenis_dokumen_id' => $jenisCv->id,
                'path' => 'dokumen/test_cv.pdf',
                'nama_asli' => 'CV_Rian_Nugraha.pdf',
                'mime' => 'application/pdf',
                'ukuran' => 102400,
                'hash' => hash('sha256', 'dummy_rian'),
            ]);
        }

        $this->penempatan = Penempatan::create([
            'peserta_id' => $this->peserta->id,
            'pendaftaran_id' => $this->pendaftaran->id,
            'kuota_id' => $this->kuota->id,
            'pembimbing_id' => $this->pembimbing->id,
            'bidang_id' => $this->bidang->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'dibuat_oleh' => $this->admin->id,
            'tgl_mulai' => now()->toDateString(),
            'tgl_selesai' => now()->addMonths(3)->toDateString(),
            'status' => StatusPenempatanEnum::Aktif,
        ]);
    }

    public function test_admin_can_view_pendaftaran_show_detail_page(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.pendaftaran.show', $this->pendaftaran));

        $response->assertStatus(200);
        $response->assertSee($this->pendaftaran->nomor_pendaftaran);
        $response->assertSee('Rian Nugraha');
        $response->assertSee('docPreviewModal');
        $response->assertSee('openDocPreview');
        $response->assertSee('Keputusan Verifikasi');
    }

    public function test_admin_can_view_penempatan_index_page(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.penempatan.index'));

        $response->assertStatus(200);
        $response->assertSee('Rian Nugraha');
        $response->assertSee('modal-surat-preview');
        $response->assertSee('openSuratPreview');
        $response->assertSee('Penempatan');
    }

    public function test_admin_can_view_waiting_list_index_page(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.waiting-list.index'));

        $response->assertStatus(200);
        $response->assertSee('Antrean Waiting List');
        $response->assertSee('FIFO');
    }
}
