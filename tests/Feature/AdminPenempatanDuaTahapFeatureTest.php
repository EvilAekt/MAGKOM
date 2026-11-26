<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JenjangEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusPendaftaranEnum;
use App\Enums\StatusPenempatanEnum;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\KelompokMagang;
use App\Models\Kuota;
use App\Models\Pendaftaran;
use App\Models\Penempatan;
use App\Models\PesertaProfil;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPenempatanDuaTahapFeatureTest extends TestCase
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

        $response = $this->actingAs($this->admin)->get(route('admin.penempatan.index', ['tipe' => 'individu', 'tab' => 'belum']));

        $response->assertOk();
        $response->assertSee('Penempatan Peserta');
        $response->assertSee('Antrean Peserta Lolos Berkas (Menunggu Penempatan)');
        $response->assertSee('Calon Peserta Antrean');
        $response->assertSee(route('admin.penempatan.antrean.show', $pendaftaran->id), false);
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

    public function test_admin_can_assign_pembimbing_without_bidang_and_bidang_is_automatically_resolved_from_registration(): void
    {
        $peserta = User::factory()->create([
            'nama' => 'Peserta Otomatis Bidang',
            'role' => RoleEnum::Peserta,
            'status_aktif' => true,
        ]);

        $this->jurusan->bidang()->syncWithoutDetaching([$this->bidangEgov->id]);

        $pendaftaran = Pendaftaran::create([
            'user_id' => $peserta->id,
            'kuota_id' => $this->kuota->id,
            'bulan_magang_id' => $this->bulanAktif->id,
            'status' => StatusPendaftaranEnum::Diterima,
            'durasi_bulan' => 3,
            'diputuskan_at' => now(),
        ]);

        // Admin hanya memilih pembimbing_id tanpa bidang_id
        $response = $this->actingAs($this->admin)
            ->post(route('admin.penempatan.tetapkan'), [
                'pendaftaran_id' => $pendaftaran->id,
                'pembimbing_id' => $this->pembimbing->id,
            ]);

        $response->assertRedirect(route('admin.penempatan.index'));
        $response->assertSessionHas('success');

        // Bidang dinas otomatis terisi dari kuota jurusan registrasi
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

    public function test_antrean_belum_ditempatkan_filters_by_jurusan_and_bidang(): void
    {
        $this->jurusan->bidang()->syncWithoutDetaching([$this->bidangEgov->id]);

        $jurusanDesain = Jurusan::create([
            'nama_jurusan' => 'Teknologi Informasi',
            'kategori' => 'mahasiswa',
            'aktif' => true,
        ]);
        $jurusanDesain->bidang()->attach($this->bidangIti->id);

        $kuotaDesain = Kuota::create([
            'bulan_magang_id' => $this->bulanAktif->id,
            'jurusan_id' => $jurusanDesain->id,
            'jumlah' => 5,
            'terisi' => 1,
            'aktif' => true,
        ]);

        $pesertaInformatika = User::factory()->create(['nama' => 'Calon Informatika']);
        Pendaftaran::create([
            'user_id' => $pesertaInformatika->id,
            'kuota_id' => $this->kuota->id,
            'bulan_magang_id' => $this->bulanAktif->id,
            'status' => StatusPendaftaranEnum::Diterima,
            'durasi_bulan' => 3,
            'diputuskan_at' => now(),
        ]);

        $pesertaTi = User::factory()->create(['nama' => 'Calon TI']);
        Pendaftaran::create([
            'user_id' => $pesertaTi->id,
            'kuota_id' => $kuotaDesain->id,
            'bulan_magang_id' => $this->bulanAktif->id,
            'status' => StatusPendaftaranEnum::Diterima,
            'durasi_bulan' => 3,
            'diputuskan_at' => now(),
        ]);

        // Filter Jurusan Informatika di tab belum
        $responseJurusan = $this->actingAs($this->admin)
            ->get(route('admin.penempatan.index', [
                'tab' => 'belum',
                'jurusan_id' => $this->jurusan->id,
            ]));
        $responseJurusan->assertOk();
        $this->assertCount(1, $responseJurusan->viewData('antreanBelumDitempatkan'));
        $this->assertEquals('Calon Informatika', $responseJurusan->viewData('antreanBelumDitempatkan')->first()->user->nama);

        // Filter Bidang ITI di tab belum
        $responseBidang = $this->actingAs($this->admin)
            ->get(route('admin.penempatan.index', [
                'tab' => 'belum',
                'bidang_id' => $this->bidangIti->id,
            ]));
        $responseBidang->assertOk();
        $this->assertCount(1, $responseBidang->viewData('antreanBelumDitempatkan'));
        $this->assertEquals('Calon TI', $responseBidang->viewData('antreanBelumDitempatkan')->first()->user->nama);
    }

    public function test_penempatan_page_renders_three_category_tabs_and_filters_individu_vs_tim(): void
    {
        $pesertaIndSudah = User::factory()->create(['nama' => 'Ind Sudah']);
        $pendIndSudah = Pendaftaran::create([
            'user_id' => $pesertaIndSudah->id,
            'kuota_id' => $this->kuota->id,
            'bulan_magang_id' => $this->bulanAktif->id,
            'tipe' => 'mandiri',
            'status' => StatusPendaftaranEnum::Diterima,
            'durasi_bulan' => 3,
        ]);
        $penIndSudah = Penempatan::create([
            'pendaftaran_id' => $pendIndSudah->id,
            'peserta_id' => $pesertaIndSudah->id,
            'kuota_id' => $this->kuota->id,
            'bidang_id' => $this->bidangEgov->id,
            'pembimbing_id' => $this->pembimbing->id,
            'jalur' => 'umum',
            'sumber' => 'online',
            'tgl_mulai' => now(),
            'tgl_selesai' => now()->addMonths(3),
            'status' => StatusPenempatanEnum::Aktif,
        ]);

        $ketuaTimSudah = User::factory()->create(['nama' => 'Ketua Tim Sudah']);
        $timSudah = KelompokMagang::create([
            'ketua_user_id' => $ketuaTimSudah->id,
            'nama_kelompok' => 'Tim Penempatan Alpha',
        ]);
        $pendTimSudah = Pendaftaran::create([
            'user_id' => $ketuaTimSudah->id,
            'kuota_id' => $this->kuota->id,
            'bulan_magang_id' => $this->bulanAktif->id,
            'tipe' => 'kelompok',
            'kelompok_magang_id' => $timSudah->id,
            'status' => StatusPendaftaranEnum::Diterima,
            'durasi_bulan' => 3,
        ]);
        $penTimSudah = Penempatan::create([
            'pendaftaran_id' => $pendTimSudah->id,
            'peserta_id' => $ketuaTimSudah->id,
            'kuota_id' => $this->kuota->id,
            'bidang_id' => $this->bidangEgov->id,
            'pembimbing_id' => $this->pembimbing->id,
            'jalur' => 'umum',
            'sumber' => 'online',
            'tgl_mulai' => now(),
            'tgl_selesai' => now()->addMonths(3),
            'status' => StatusPenempatanEnum::Aktif,
        ]);

        $pesertaIndBelum = User::factory()->create(['nama' => 'Ind Belum']);
        Pendaftaran::create([
            'user_id' => $pesertaIndBelum->id,
            'kuota_id' => $this->kuota->id,
            'bulan_magang_id' => $this->bulanAktif->id,
            'tipe' => 'mandiri',
            'status' => StatusPendaftaranEnum::Diterima,
            'durasi_bulan' => 3,
            'diputuskan_at' => now(),
        ]);

        $ketuaTimBelum = User::factory()->create(['nama' => 'Ketua Tim Belum']);
        $timBelum = KelompokMagang::create([
            'ketua_user_id' => $ketuaTimBelum->id,
            'nama_kelompok' => 'Tim Antrean Beta',
        ]);
        Pendaftaran::create([
            'user_id' => $ketuaTimBelum->id,
            'kuota_id' => $this->kuota->id,
            'bulan_magang_id' => $this->bulanAktif->id,
            'tipe' => 'kelompok',
            'kelompok_magang_id' => $timBelum->id,
            'status' => StatusPendaftaranEnum::Diterima,
            'durasi_bulan' => 3,
            'diputuskan_at' => now(),
        ]);

        // 1. Tab Semua Penempatan terkunci ke Sudah Ditempatkan (gabungan Individu & Tim), tanpa switcher Belum Ditempatkan & tanpa tombol Detail
        $resAll = $this->actingAs($this->admin)->get(route('admin.penempatan.index'));
        $resAll->assertOk();
        $resAll->assertSee('Semua Penempatan');
        $resAll->assertSee('Individu');
        $resAll->assertSee('Tim');
        $resAll->assertDontSee('penempatan-status-tabs-wrap', false);
        $resAll->assertDontSee('panel-penempatan-belum', false);
        $resAll->assertSee('Tim Penempatan Alpha');
        $resAll->assertSee('Ind Sudah');
        $resAll->assertDontSee(route('admin.penempatan.show', $penIndSudah->id), false);
        $resAll->assertDontSee(route('admin.penempatan.show', $penTimSudah->id), false);

        // 1b. Mengakses tab=belum tanpa tipe=individu/kelompok tetap diarahkan ke Sudah Ditempatkan
        $resAllBelum = $this->actingAs($this->admin)->get(route('admin.penempatan.index', ['tab' => 'belum']));
        $resAllBelum->assertOk();
        $this->assertEquals('sudah', $resAllBelum->viewData('activeTab'));
        $resAllBelum->assertDontSee('panel-penempatan-belum', false);

        // 2. Filter tipe=kelompok pada Sudah Ditempatkan menampilkan Nama Tim (bukan ketua saja), switcher tab muncul, & tombol Detail tidak ada
        $resTimSudah = $this->actingAs($this->admin)->get(route('admin.penempatan.index', ['tab' => 'sudah', 'tipe' => 'kelompok']));
        $resTimSudah->assertOk();
        $this->assertCount(1, $resTimSudah->viewData('penempatanList'));
        $timPenempatan = $resTimSudah->viewData('penempatanList')->first();
        $resTimSudah->assertSee('penempatan-status-tabs-wrap', false);
        $resTimSudah->assertSee('Tim Penempatan Alpha');
        $resTimSudah->assertSee('Ketua: Ketua Tim Sudah');
        $resTimSudah->assertDontSee(route('admin.penempatan.show', $timPenempatan->id), false);

        // 2b. Filter tipe=individu pada Sudah Ditempatkan menampilkan switcher & tidak ada tombol Detail Sudah Ditempatkan
        $resIndSudah = $this->actingAs($this->admin)->get(route('admin.penempatan.index', ['tab' => 'sudah', 'tipe' => 'individu']));
        $resIndSudah->assertOk();
        $this->assertCount(1, $resIndSudah->viewData('penempatanList'));
        $indPenempatan = $resIndSudah->viewData('penempatanList')->first();
        $resIndSudah->assertSee('penempatan-status-tabs-wrap', false);
        $resIndSudah->assertDontSee(route('admin.penempatan.show', $indPenempatan->id), false);

        // 3. Filter tipe=individu pada Belum Ditempatkan + tombol Detail antrean tersedia
        $resIndBelum = $this->actingAs($this->admin)->get(route('admin.penempatan.index', ['tab' => 'belum', 'tipe' => 'individu']));
        $resIndBelum->assertOk();
        $this->assertCount(1, $resIndBelum->viewData('antreanBelumDitempatkan'));
        $indAntrean = $resIndBelum->viewData('antreanBelumDitempatkan')->first();
        $this->assertEquals('Ind Belum', $indAntrean->user->nama);
        $resIndBelum->assertSee(route('admin.penempatan.antrean.show', $indAntrean->id), false);

        // 3b. Filter tipe=kelompok pada Belum Ditempatkan menampilkan Nama Tim & tombol Detail antrean
        $resTimBelum = $this->actingAs($this->admin)->get(route('admin.penempatan.index', ['tab' => 'belum', 'tipe' => 'kelompok']));
        $resTimBelum->assertOk();
        $this->assertCount(1, $resTimBelum->viewData('antreanBelumDitempatkan'));
        $timAntrean = $resTimBelum->viewData('antreanBelumDitempatkan')->first();
        $resTimBelum->assertSee('Tim Antrean Beta');
        $resTimBelum->assertSee('Ketua: Ketua Tim Belum');
        $resTimBelum->assertSee(route('admin.penempatan.antrean.show', $timAntrean->id), false);
    }

    public function test_admin_dapat_melihat_detail_peserta_antrean_dan_menetapkan_pembimbing(): void
    {
        $this->jurusan->bidang()->syncWithoutDetaching([$this->bidangEgov->id]);

        $peserta = User::factory()->create([
            'nama' => 'Siti Antrean Detail',
            'email' => 'siti.detail@example.test',
            'role' => RoleEnum::Peserta,
            'status_aktif' => true,
        ]);

        PesertaProfil::create([
            'user_id' => $peserta->id,
            'nim_nisn' => '2200112233',
            'institusi' => 'Universitas Gadjah Mada',
            'jurusan_id' => $this->jurusan->id,
            'jenjang' => JenjangEnum::S1,
            'no_hp' => '081234567890',
            'alamat' => 'Jl. Kaliurang KM 5, Sleman',
            'foto' => 'foto-peserta/siti.jpg',
        ]);

        $pendaftaran = Pendaftaran::create([
            'user_id' => $peserta->id,
            'kuota_id' => $this->kuota->id,
            'bulan_magang_id' => $this->bulanAktif->id,
            'tipe' => 'mandiri',
            'status' => StatusPendaftaranEnum::Diterima,
            'durasi_bulan' => 3,
            'diputuskan_at' => now(),
        ]);

        $pembimbingIti = User::factory()->create([
            'nama' => 'Sari Mentor Iti',
            'role' => RoleEnum::Pembimbing,
            'bidang_id' => $this->bidangIti->id,
            'status_aktif' => true,
        ]);

        // Tombol Detail muncul pada tabel antrean individu dan tombol Tempatkan tidak ditampilkan
        $indexRes = $this->actingAs($this->admin)->get(route('admin.penempatan.index', ['tipe' => 'individu', 'tab' => 'belum']));
        $indexRes->assertOk();
        $indexRes->assertSee(route('admin.penempatan.antrean.show', $pendaftaran->id), false);
        $indexRes->assertDontSee('data-action="open-tetapkan-modal"', false);

        // Halaman detail menampilkan biodata, foto, bidang peserta (tanpa select bidang_id), dan hanya pembimbing di bidang terkait
        $detailRes = $this->actingAs($this->admin)->get(route('admin.penempatan.antrean.show', $pendaftaran));
        $detailRes->assertOk();
        $detailRes->assertSee('Siti Antrean Detail');
        $detailRes->assertSee('2200112233');
        $detailRes->assertSee('Universitas Gadjah Mada');
        $detailRes->assertSee('foto-peserta/siti.jpg');
        $detailRes->assertSee('Budi Mentor Egov');
        $detailRes->assertDontSee($pembimbingIti->nama);
        $detailRes->assertDontSee('id="antrean-bidang-id"', false);
        $detailRes->assertSee(route('admin.penempatan.tetapkan'), false);
    }

    public function test_superadmin_read_only_dan_non_admin_ditolak_pada_detail_antrean(): void
    {
        $peserta = User::factory()->create(['role' => RoleEnum::Peserta, 'status_aktif' => true]);
        $superAdmin = User::factory()->create(['role' => RoleEnum::SuperAdmin, 'status_aktif' => true]);

        $pendaftaran = Pendaftaran::create([
            'user_id' => $peserta->id,
            'kuota_id' => $this->kuota->id,
            'bulan_magang_id' => $this->bulanAktif->id,
            'tipe' => 'mandiri',
            'status' => StatusPendaftaranEnum::Diterima,
            'durasi_bulan' => 3,
            'diputuskan_at' => now(),
        ]);

        // SuperAdmin bisa melihat detail namun mode read-only (tanpa form tetapkan)
        $saRes = $this->actingAs($superAdmin)->get(route('admin.penempatan.antrean.show', $pendaftaran));
        $saRes->assertOk();
        $saRes->assertSee('Mode Read-Only');
        $saRes->assertDontSee(route('admin.penempatan.tetapkan'), false);

        // Peserta biasa ditolak 403
        $pesertaRes = $this->actingAs($peserta)->get(route('admin.penempatan.antrean.show', $pendaftaran));
        $pesertaRes->assertForbidden();

        // Pendaftaran yang belum diterima diarahkan kembali ke antrean
        $pending = Pendaftaran::create([
            'user_id' => $peserta->id,
            'kuota_id' => $this->kuota->id,
            'bulan_magang_id' => $this->bulanLain->id,
            'tipe' => 'mandiri',
            'status' => StatusPendaftaranEnum::Diajukan,
            'durasi_bulan' => 3,
        ]);

        $pendingRes = $this->actingAs($this->admin)->get(route('admin.penempatan.antrean.show', $pending));
        $pendingRes->assertRedirect(route('admin.penempatan.index', ['tab' => 'belum']));
    }
}
