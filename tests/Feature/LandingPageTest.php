<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Enums\StatusKetersediaanEnum;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\Kuota;
use App\Models\SlotKhusus;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LandingPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Kunci waktu pengujian ke 8 Oktober 2026 pada Carbon dan CarbonImmutable
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

    public function test_landing_page_bisa_diakses_tanpa_login_dan_memiliki_metadata_lengkap(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Portal MagKom - Informasi Kuota & Pendaftaran Magang Kominfo');
        $response->assertSee('meta name="description"', false);
        $response->assertSee('property="og:title"', false);
    }

    public function test_landing_page_mengelompokkan_jurusan_smk_dan_mahasiswa_dengan_status_akurat(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        // Memastikan tab jurusan SMK dan Mahasiswa tampil (AC-01-1)
        $response->assertSee('Jalur SMK');
        $response->assertSee('Mahasiswa');
        $response->assertSee('D3 / S1');

        // Memastikan master jurusan tampil
        $response->assertSee('Teknik Komputer dan Jaringan (TKJ)');
        $response->assertSee('Informatika');

        // Memastikan status ketersediaan tampil
        $response->assertSee('Tersedia');
        $response->assertSee('Segera Dibuka');
    }

    public function test_landing_page_menampilkan_label_waiting_list_jika_kuota_habis_dan_tombol_tetap_aktif(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        // Jurusan TKJ diisi penuh 5 dari 5 (AC-01-3)
        $response->assertSee('Kuota Penuh (Waiting List)');
        $response->assertSee('Pendaftaran masuk antrean tunggu');
        $response->assertSee('Daftar Waiting List');
    }

    public function test_slot_khusus_tidak_pernah_bocor_di_response_publik_ac_01_5(): void
    {
        // Pastikan di database memang ada slot khusus yang aktif
        $slotKhusus = SlotKhusus::where('jumlah', 3)->firstOrFail();
        $this->assertEquals('2026-11-01', $slotKhusus->bulan_mulai->format('Y-m-d'));

        $response = $this->get('/');

        $response->assertStatus(200);
        // Validasi AC-01-5: HTML, JSON, maupun atribut apapun tidak boleh memuat kata kunci slot khusus
        $response->assertDontSee('slot_khusus');
        $response->assertDontSee('Slot Khusus');
        $response->assertDontSee('slot khusus');
        $response->assertDontSee('jalur internal');
    }

    public function test_override_status_superadmin_langsung_mengubah_status_landing_page(): void
    {
        $bulanNov = BulanMagang::whereDate('bulan_mulai', '2026-11-01')->firstOrFail();

        // Awalnya override_status null -> Tersedia
        $responseAwal = $this->get('/');
        $responseAwal->assertSee('Tersedia');

        // SuperAdmin meng-override status menjadi Tutup
        $bulanNov->update([
            'override_status' => StatusKetersediaanEnum::Tutup,
        ]);

        // Landing page langsung mencerminkan perubahan
        $responseBaru = $this->get('/');
        $responseBaru->assertStatus(200);
        $responseBaru->assertSee('Tutup');
        $responseBaru->assertSee('Pendaftaran Ditutup');
    }

    public function test_admin_tidak_dapat_menurunkan_jumlah_kuota_di_bawah_terisi_ac_05_3(): void
    {
        $admin = User::firstOrNew(['email' => 'admin.test@kominfo.go.id']);
        $admin->nama = 'Admin Penguji';
        $admin->password = bcrypt('PasswordAdmin#2026');
        $admin->role = RoleEnum::Admin;
        $admin->email_verified_at = now();
        $admin->status_aktif = true;
        $admin->save();

        $kuota = Kuota::where('terisi', '>', 0)->firstOrFail();
        $terisiSaatIni = $kuota->terisi;

        // Coba turunkan kuota lebih kecil dari terisi
        $jumlahTidakValid = $terisiSaatIni - 1;

        $response = $this->actingAs($admin)
            ->from('/admin/kuota')
            ->put(route('admin.kuota.update', $kuota), [
                'bulan_magang_id' => $kuota->bulan_magang_id,
                'jurusan_id' => $kuota->jurusan_id,
                'jumlah' => $jumlahTidakValid,
            ]);

        // Harus gagal validasi dan redirect back dengan session error pada field 'jumlah'
        $response->assertSessionHasErrors(['jumlah']);

        // Data di database tidak berubah
        $kuota->refresh();
        $this->assertNotEquals($jumlahTidakValid, $kuota->jumlah);
    }

    public function test_landing_page_menampilkan_menu_akun_dan_dropdown_saat_sudah_login_sebagai_peserta(): void
    {
        $peserta = User::firstOrNew(['email' => 'peserta.auth@gmail.com']);
        $peserta->nama = 'Ahmad Santoso';
        $peserta->password = bcrypt('Password#123');
        $peserta->role = RoleEnum::Peserta;
        $peserta->email_verified_at = now();
        $peserta->status_aktif = true;
        $peserta->save();

        $response = $this->actingAs($peserta)->get('/');

        $response->assertStatus(200);
        $response->assertSee('Ahmad Santoso');
        $response->assertSee('Peserta');
        $response->assertSee('id="landingUserDropdown"', false);
        $response->assertSee('class="landing-dropdown-menu"', false);
        $response->assertSee('Status pendaftaran, tugas & logbook', false);
        $response->assertSee('Kelola biodata diri & riwayat berkas', false);
        $response->assertSee('Keamanan kata sandi & preferensi', false);
        $response->assertSee('Akhiri sesi akun pada perangkat ini', false);
        $response->assertSee(route('peserta.dashboard'));
        $response->assertSee(route('peserta.profil.show'));
        $response->assertSee(route('peserta.pengaturan.index'));
        $response->assertSee(route('logout'));
    }

    public function test_landing_page_memuat_tautan_eksplorasi_peta_alur_interaktif(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        // Memastikan tombol eksplorasi alur interaktif menautkan ke rute publik panduan-alur
        $response->assertSee(route('panduan-alur'));
        $response->assertSee('Eksplorasi Peta Alur Interaktif');
    }

    public function test_landing_page_menampilkan_menu_akun_staf_saat_login_sebagai_admin(): void
    {
        $admin = User::firstOrNew(['email' => 'admin.landing@kominfo.go.id']);
        $admin->nama = 'Siti Administrator';
        $admin->password = bcrypt('Password#123');
        $admin->role = RoleEnum::Admin;
        $admin->email_verified_at = now();
        $admin->status_aktif = true;
        $admin->save();

        $response = $this->actingAs($admin)->get('/');

        $response->assertStatus(200);
        $response->assertSee('Siti Administrator');
        $response->assertSee('Admin');
        $response->assertSee(route('admin.dashboard'));
        $response->assertSee(route('admin.profil.show'));
        $response->assertSee(route('admin.pengaturan.index'));
    }
}

