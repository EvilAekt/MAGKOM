<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JalurEnum;
use App\Enums\JenjangEnum;
use App\Enums\KompleksitasEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusPendaftaranEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\StatusPenilaianEnum;
use App\Enums\StatusTugasEnum;
use App\Enums\SumberPenempatanEnum;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\Kuota;
use App\Models\Pendaftaran;
use App\Models\Penempatan;
use App\Models\Penilaian;
use App\Models\PenilaianDetail;
use App\Models\PesertaProfil;
use App\Models\Rubrik;
use App\Models\Tugas;
use App\Models\User;
use Database\Seeders\JenisDokumenSeeder;
use Database\Seeders\RubrikSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardModulesRenderingTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected User $admin;

    protected User $pembimbing;

    protected User $peserta;

    protected Bidang $bidang;

    protected Jurusan $jurusan;

    protected BulanMagang $bulan;

    protected Kuota $kuota;

    protected Pendaftaran $pendaftaran;

    protected Penempatan $penempatan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(JenisDokumenSeeder::class);
        $this->seed(RubrikSeeder::class);

        // 1. Bidang & Jurusan
        $this->bidang = Bidang::create([
            'nama_bidang' => 'Bidang Aplikasi Informatika (Aptika)',
            'deskripsi' => 'Pengembangan dan tata kelola sistem aplikasi',
            'aktif' => true,
        ]);

        $this->jurusan = Jurusan::create([
            'nama_jurusan' => 'Informatika',
            'kategori' => 'mahasiswa',
            'aktif' => true,
        ]);

        // 2. Bulan & Kuota
        $this->bulan = BulanMagang::create([
            'bulan_mulai' => now()->startOfMonth(),
            'tgl_buka' => now()->subDays(5)->toDateString(),
            'tgl_tutup' => now()->addDays(10)->toDateString(),
            'aktif' => true,
        ]);

        $this->kuota = Kuota::create([
            'bulan_magang_id' => $this->bulan->id,
            'jurusan_id' => $this->jurusan->id,
            'jumlah' => 10,
            'terisi' => 1,
            'aktif' => true,
        ]);

        // 3. Users
        $this->superAdmin = new User;
        $this->superAdmin->nama = 'Super Administrator';
        $this->superAdmin->email = 'superadmin@slemankab.go.id';
        $this->superAdmin->password = 'Password123!';
        $this->superAdmin->role = RoleEnum::SuperAdmin;
        $this->superAdmin->status_aktif = true;
        $this->superAdmin->must_change_password = false;
        $this->superAdmin->email_verified_at = now();
        $this->superAdmin->save();

        $this->admin = new User;
        $this->admin->nama = 'Admin Verifikator';
        $this->admin->email = 'admin@slemankab.go.id';
        $this->admin->password = 'Password123!';
        $this->admin->role = RoleEnum::Admin;
        $this->admin->status_aktif = true;
        $this->admin->must_change_password = false;
        $this->admin->email_verified_at = now();
        $this->admin->save();

        $this->pembimbing = new User;
        $this->pembimbing->nama = 'Pembimbing Lapangan';
        $this->pembimbing->email = 'pembimbing@slemankab.go.id';
        $this->pembimbing->password = 'Password123!';
        $this->pembimbing->role = RoleEnum::Pembimbing;
        $this->pembimbing->bidang_id = $this->bidang->id;
        $this->pembimbing->status_aktif = true;
        $this->pembimbing->must_change_password = false;
        $this->pembimbing->email_verified_at = now();
        $this->pembimbing->save();

        $this->peserta = new User;
        $this->peserta->nama = 'Peserta Uji';
        $this->peserta->email = 'peserta@example.com';
        $this->peserta->password = 'Password123!';
        $this->peserta->role = RoleEnum::Peserta;
        $this->peserta->status_aktif = true;
        $this->peserta->must_change_password = false;
        $this->peserta->email_verified_at = now();
        $this->peserta->save();

        PesertaProfil::create([
            'user_id' => $this->peserta->id,
            'nim_nisn' => '12345678',
            'institusi' => 'Universitas Gadjah Mada',
            'jurusan_id' => $this->jurusan->id,
            'jenjang' => JenjangEnum::S1,
            'no_hp' => '081234567890',
        ]);

        // 4. Pendaftaran & Penempatan
        $this->pendaftaran = Pendaftaran::create([
            'user_id' => $this->peserta->id,
            'bulan_magang_id' => $this->bulan->id,
            'kuota_id' => $this->kuota->id,
            'durasi_bulan' => 1,
            'status' => StatusPendaftaranEnum::Diterima,
            'diajukan_at' => now(),
        ]);

        $this->penempatan = Penempatan::create([
            'pendaftaran_id' => $this->pendaftaran->id,
            'peserta_id' => $this->peserta->id,
            'kuota_id' => $this->kuota->id,
            'bidang_id' => $this->bidang->id,
            'pembimbing_id' => $this->pembimbing->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'dibuat_oleh' => $this->admin->id,
            'tgl_mulai' => now()->toDateString(),
            'tgl_selesai' => now()->addMonth()->toDateString(),
            'status' => StatusPenempatanEnum::Aktif,
        ]);
    }

    /** @test */
    public function admin_can_render_all_jalur_b_dashboard_views(): void
    {
        $responseShow = $this->actingAs($this->admin)->get(route('admin.pendaftaran.show', $this->pendaftaran));
        $responseShow->assertStatus(200);
        $responseShow->assertViewIs('admin.pendaftaran.show');
        $responseShow->assertSee('Verifikasi Berkas Calon Peserta');

        $responseWaiting = $this->actingAs($this->admin)->get(route('admin.waiting-list.index'));
        $responseWaiting->assertStatus(200);
        $responseWaiting->assertViewIs('admin.waiting-list.index');
        $responseWaiting->assertSee('Antrean Waiting List');

        $responsePenempatan = $this->actingAs($this->admin)->get(route('admin.penempatan.index'));
        $responsePenempatan->assertStatus(200);
        $responsePenempatan->assertViewIs('admin.penempatan.index');
        $responsePenempatan->assertSee('Manajemen Penempatan');

        $responseKuota = $this->actingAs($this->admin)->get(route('admin.kuota.index'));
        $responseKuota->assertStatus(200);
        $responseKuota->assertViewIs('admin.kuota.index');
        $responseKuota->assertSee('Manajemen Kuota Magang');
    }

    /** @test */
    public function peserta_and_pembimbing_can_render_all_jalur_c_views(): void
    {
        $this->withoutExceptionHandling();

        // 1. Peserta Logbook
        $resLogbookPeserta = $this->actingAs($this->peserta)->get(route('peserta.logbook.index'));
        $resLogbookPeserta->assertStatus(200);
        $resLogbookPeserta->assertViewIs('peserta.logbook.index');

        // 2. Pembimbing Logbook
        $resLogbookPembimbing = $this->actingAs($this->pembimbing)->get(route('pembimbing.logbook.index'));
        $resLogbookPembimbing->assertStatus(200);
        $resLogbookPembimbing->assertViewIs('pembimbing.logbook.index');

        // 3. Tugas
        $tugas = Tugas::create([
            'penempatan_id' => $this->penempatan->id,
            'judul' => 'Pengembangan Modul MagKom',
            'deskripsi' => 'Rancang implementasi Blade UI dashboard',
            'kategori' => 'Project',
            'kompleksitas' => KompleksitasEnum::Sedang,
            'deadline' => now()->addDays(7)->toDateString(),
            'status' => StatusTugasEnum::Belum,
        ]);

        $resTugasPesertaIndex = $this->actingAs($this->peserta)->get(route('peserta.tugas.index'));
        $resTugasPesertaIndex->assertStatus(200);
        $resTugasPesertaIndex->assertViewIs('peserta.tugas.index');

        $resTugasPesertaShow = $this->actingAs($this->peserta)->get(route('peserta.tugas.show', $tugas));
        $resTugasPesertaShow->assertStatus(200);
        $resTugasPesertaShow->assertViewIs('peserta.tugas.show');

        $resTugasPembimbingIndex = $this->actingAs($this->pembimbing)->get(route('pembimbing.tugas.index'));
        $resTugasPembimbingIndex->assertStatus(200);
        $resTugasPembimbingIndex->assertViewIs('pembimbing.tugas.index');

        $resTugasPembimbingShow = $this->actingAs($this->pembimbing)->get(route('pembimbing.tugas.show', $tugas));
        $resTugasPembimbingShow->assertStatus(200);
        $resTugasPembimbingShow->assertViewIs('pembimbing.tugas.show');

        // 4. Pembimbing Penilaian
        $resPenilaianIndex = $this->actingAs($this->pembimbing)->get(route('pembimbing.penilaian.index'));
        $resPenilaianIndex->assertStatus(200);
        $resPenilaianIndex->assertViewIs('pembimbing.penilaian.index');

        $resPenilaianShow = $this->actingAs($this->pembimbing)->get(route('pembimbing.penilaian.show', $this->penempatan));
        $resPenilaianShow->assertStatus(200);
        $resPenilaianShow->assertViewIs('pembimbing.penilaian.show');

        // 5. Peserta Nilai Final
        $penilaian = Penilaian::create([
            'penempatan_id' => $this->penempatan->id,
            'pembimbing_id' => $this->pembimbing->id,
            'nilai_akhir' => 90.0,
            'predikat' => 'A',
            'status' => StatusPenilaianEnum::Final,
            'catatan' => 'Sangat memuaskan dalam pengerjaan proyek.',
        ]);

        foreach (Rubrik::where('aktif', true)->get() as $rubrik) {
            PenilaianDetail::create([
                'penilaian_id' => $penilaian->id,
                'rubrik_id' => $rubrik->id,
                'skor' => 90.0,
                'bobot_snapshot' => $rubrik->bobot,
            ]);
        }

        $resNilaiPeserta = $this->actingAs($this->peserta)->get(route('peserta.nilai.show'));
        $resNilaiPeserta->assertStatus(200);
        $resNilaiPeserta->assertViewIs('peserta.nilai.show');
        $resNilaiPeserta->assertSee('90.00');
    }

    /** @test */
    public function superadmin_can_render_all_jalur_d_master_data_views(): void
    {
        $this->withoutExceptionHandling();

        $this->actingAs($this->superAdmin)->get(route('superadmin.staf.index'))->assertOk();
        $this->actingAs($this->superAdmin)->get(route('superadmin.slot-khusus.index'))->assertOk();
        $this->actingAs($this->superAdmin)->get(route('superadmin.jurusan.index'))->assertOk();
        $this->actingAs($this->superAdmin)->get(route('superadmin.bidang.index'))->assertOk();
        $this->actingAs($this->superAdmin)->get(route('superadmin.rubrik.index'))->assertOk();
        $this->actingAs($this->superAdmin)->get(route('superadmin.pengaturan.index'))->assertOk();
        $this->actingAs($this->superAdmin)->get(route('superadmin.audit-log.index'))->assertOk();
    }
}
