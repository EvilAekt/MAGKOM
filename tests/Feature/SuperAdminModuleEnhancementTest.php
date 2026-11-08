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
use App\Models\SlotKhusus;
use App\Models\User;
use App\Support\Pengaturan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SuperAdminModuleEnhancementTest extends TestCase
{
    use RefreshDatabase;

    private User $superAdmin;

    private User $admin;

    private User $pembimbing;

    private Bidang $bidang;

    private Jurusan $jurusan;

    private BulanMagang $bulanMagang;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bidang = Bidang::create([
            'nama_bidang' => 'Bidang Aplikasi Informatika',
            'kode_bidang' => 'APTIKA',
            'deskripsi' => 'Pengembangan aplikasi dan tata kelola e-gov',
            'aktif' => true,
        ]);

        $this->jurusan = Jurusan::create([
            'nama_jurusan' => 'Teknik Informatika',
            'kategori' => 'mahasiswa',
            'bidang_id' => $this->bidang->id,
            'aktif' => true,
        ]);

        $this->superAdmin = User::factory()->create([
            'nama' => 'Super Administrator MagKom',
            'email' => 'superadmin@slemankab.go.id',
            'role' => RoleEnum::SuperAdmin,
            'email_verified_at' => now(),
            'status_aktif' => true,
            'must_change_password' => false,
        ]);

        $this->admin = User::factory()->create([
            'nama' => 'Admin Verifikator Dinas',
            'email' => 'admin@slemankab.go.id',
            'role' => RoleEnum::Admin,
            'email_verified_at' => now(),
            'status_aktif' => true,
            'must_change_password' => false,
        ]);

        $this->pembimbing = User::factory()->create([
            'nama' => 'Pembimbing ASN Kominfo',
            'email' => 'pembimbing@slemankab.go.id',
            'role' => RoleEnum::Pembimbing,
            'bidang_id' => $this->bidang->id,
            'email_verified_at' => now(),
            'status_aktif' => true,
            'must_change_password' => false,
        ]);

        $this->bulanMagang = BulanMagang::create([
            'bulan_mulai' => '2026-11',
            'nama_bulan' => 'November 2026',
            'tgl_buka' => '2026-10-01',
            'tgl_tutup' => '2026-10-25',
            'aktif' => true,
        ]);
    }

    /**
     * TASK A: Super Admin harus strictly read-only pada modul operasional Admin (HTTP 403 untuk POST/PUT/DELETE).
     */
    public function test_superadmin_forbidden_to_execute_mutation_actions_on_admin_module(): void
    {
        $pesertaUser = User::factory()->create([
            'nama' => 'Calon Peserta Magang',
            'email' => 'peserta@kampus.ac.id',
            'role' => RoleEnum::Peserta,
            'email_verified_at' => now(),
            'status_aktif' => true,
            'must_change_password' => false,
        ]);

        $kuota = Kuota::create([
            'bulan_magang_id' => $this->bulanMagang->id,
            'jurusan_id' => $this->jurusan->id,
            'jumlah' => 5,
            'terisi' => 0,
            'aktif' => true,
        ]);

        $pendaftaran = Pendaftaran::create([
            'user_id' => $pesertaUser->id,
            'bulan_magang_id' => $this->bulanMagang->id,
            'kuota_id' => $kuota->id,
            'jalur' => 'umum',
            'status' => 'diajukan',
            'durasi_bulan' => 3,
        ]);

        // 1. SuperAdmin mencoba memutuskan verifikasi pendaftaran -> 403
        $responsePutuskan = $this->actingAs($this->superAdmin)
            ->post(route('admin.pendaftaran.putuskan', $pendaftaran), [
                'keputusan' => 'terima',
                'catatan' => 'Verifikasi oleh superadmin tidak diizinkan',
            ]);
        $responsePutuskan->assertStatus(403);

        // 2. SuperAdmin mencoba menambahkan kuota -> 403
        $responseKuota = $this->actingAs($this->superAdmin)
            ->post(route('admin.kuota.store'), [
                'bulan_magang_id' => $this->bulanMagang->id,
                'jurusan_id' => $this->jurusan->id,
                'jumlah' => 10,
            ]);
        $responseKuota->assertStatus(403);

        // 3. SuperAdmin tetap dapat melihat (read-only) halaman operasional Admin -> 200 OK
        $this->actingAs($this->superAdmin)->get(route('admin.pendaftaran.index'))->assertOk();
        $this->actingAs($this->superAdmin)->get(route('admin.pendaftaran.show', $pendaftaran))->assertOk();
        $this->actingAs($this->superAdmin)->get(route('admin.kuota.index'))->assertOk();
        $this->actingAs($this->superAdmin)->get(route('admin.penempatan.index'))->assertOk();
    }

    /**
     * TASK B: Form tambah staf baru - Admin Verifikator TIDAK memerlukan bidang unit kerja (nullable).
     */
    public function test_superadmin_can_create_admin_verifikator_without_bidang_unit_kerja(): void
    {
        // 1. Buat Admin Verifikator tanpa bidang_id -> Berhasil
        $responseAdmin = $this->actingAs($this->superAdmin)
            ->post(route('superadmin.staf.store'), [
                'nama' => 'Staf Verifikator Baru',
                'email' => 'verifikator.baru@slemankab.go.id',
                'role' => 'admin',
                'bidang_id' => '', // kosong / unselected
                'password' => 'SandiKuat123!',
            ]);

        $responseAdmin->assertRedirect(route('superadmin.staf.index'));
        $responseAdmin->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'email' => 'verifikator.baru@slemankab.go.id',
            'role' => RoleEnum::Admin->value,
            'bidang_id' => null,
        ]);

        // 2. Coba buat Pembimbing tanpa bidang_id -> Ditolak validasi
        $responsePembimbingFail = $this->actingAs($this->superAdmin)
            ->post(route('superadmin.staf.store'), [
                'nama' => 'Staf Pembimbing Gagal',
                'email' => 'pembimbing.gagal@slemankab.go.id',
                'role' => 'pembimbing',
                'bidang_id' => '',
                'password' => 'SandiKuat123!',
            ]);

        $responsePembimbingFail->assertSessionHasErrors('bidang_id');

        // 3. Buat Pembimbing dengan bidang_id -> Berhasil
        $responsePembimbingSuccess = $this->actingAs($this->superAdmin)
            ->post(route('superadmin.staf.store'), [
                'nama' => 'Staf Pembimbing Sukses',
                'email' => 'pembimbing.sukses@slemankab.go.id',
                'role' => 'pembimbing',
                'bidang_id' => $this->bidang->id,
                'password' => 'SandiKuat123!',
            ]);

        $responsePembimbingSuccess->assertRedirect(route('superadmin.staf.index'));
        $this->assertDatabaseHas('users', [
            'email' => 'pembimbing.sukses@slemankab.go.id',
            'role' => RoleEnum::Pembimbing->value,
            'bidang_id' => $this->bidang->id,
        ]);
    }

    /**
     * TASK C: Fitur rubrik nilai dinonaktifkan dari panel Super Admin, tanpa merusak histori penilaian.
     */
    public function test_rubrik_crud_route_is_removed_and_dashboard_does_not_show_rubrik_menu(): void
    {
        // 1. Rute superadmin rubrik tidak lagi ditemukan (404)
        $response = $this->actingAs($this->superAdmin)->get('/superadmin/rubrik');
        $response->assertStatus(404);

        // 2. Navigasi dashboard SuperAdmin tidak menampilkan tautan Rubrik Nilai
        $dashboardResponse = $this->actingAs($this->superAdmin)->get(route('superadmin.dashboard'));
        $dashboardResponse->assertOk();
        $dashboardResponse->assertDontSee('/superadmin/rubrik');
    }

    /**
     * TASK D: Super Admin dapat mendaftarkan peserta magang jalur khusus secara langsung aktif.
     */
    public function test_superadmin_can_create_peserta_jalur_khusus_directly_active(): void
    {
        $slotKhusus = SlotKhusus::create([
            'nama_slot' => 'Slot Kemitraan Kampus Merdeka',
            'kapasitas' => 5,
            'bulan_mulai' => '2026-11',
            'deskripsi' => 'Alokasi khusus batch November',
            'aktif' => true,
            'dibuat_oleh' => $this->superAdmin->id,
        ]);

        $createForm = $this->actingAs($this->superAdmin)->get(route('superadmin.peserta-khusus.create'));
        $createForm->assertOk();
        $createForm->assertSee('Pendaftaran Peserta Jalur Khusus');

        $responseStore = $this->actingAs($this->superAdmin)
            ->post(route('superadmin.peserta-khusus.store'), [
                'nama' => 'Arya Wiguna Putra',
                'email' => 'arya.wiguna@ugm.ac.id',
                'password' => 'Magkom2026!',
                'nim_nisn' => '22/492100/TK/54321',
                'institusi' => 'Universitas Gadjah Mada',
                'jurusan_id' => $this->jurusan->id,
                'jenjang' => 's1',
                'no_hp' => '08122334455',
                'bidang_id' => $this->bidang->id,
                'pembimbing_id' => $this->pembimbing->id,
                'bulan_mulai' => '2026-11',
                'durasi_bulan' => 3,
                'slot_khusus_id' => $slotKhusus->id,
                'catatan' => 'Peserta Rekomendasi Khusus Kampus',
            ]);

        $responseStore->assertRedirect(route('superadmin.monitoring.index'));
        $responseStore->assertSessionHas('success');

        // Pastikan akun user dibuat dengan role Peserta
        $this->assertDatabaseHas('users', [
            'email' => 'arya.wiguna@ugm.ac.id',
            'role' => RoleEnum::Peserta->value,
            'status_aktif' => true,
        ]);

        $createdUser = User::where('email', 'arya.wiguna@ugm.ac.id')->firstOrFail();

        // Pastikan profil peserta tersimpan lengkap
        $this->assertDatabaseHas('peserta_profil', [
            'user_id' => $createdUser->id,
            'nim_nisn' => '22/492100/TK/54321',
            'institusi' => 'Universitas Gadjah Mada',
            'jurusan_id' => $this->jurusan->id,
            'jenjang' => 's1',
        ]);

        // Pastikan penempatan berstatus AKTIF dan berjalur KHUSUS
        $this->assertDatabaseHas('penempatan', [
            'peserta_id' => $createdUser->id,
            'pembimbing_id' => $this->pembimbing->id,
            'bidang_id' => $this->bidang->id,
            'slot_khusus_id' => $slotKhusus->id,
            'jalur' => 'khusus',
            'sumber' => 'manual',
            'status' => StatusPenempatanEnum::Aktif->value,
        ]);
    }

    /**
     * TASK E: CMS Pengelolaan Landing Page (Teks, Banner Foto, dan File PDF Panduan).
     */
    public function test_superadmin_can_manage_landing_page_cms_and_sync_with_public_page(): void
    {
        Storage::fake('public');

        $indexResponse = $this->actingAs($this->superAdmin)->get(route('superadmin.landing.index'));
        $indexResponse->assertOk();
        $indexResponse->assertSee('CMS Pengelolaan Landing Page');

        $fakeImage = UploadedFile::fake()->image('banner_hero_kominfo.png', 1200, 600);
        $fakePdf = UploadedFile::fake()->create('Panduan_Resmi_Magang_2026.pdf', 1024, 'application/pdf');

        $updateResponse = $this->actingAs($this->superAdmin)
            ->put(route('superadmin.landing.update'), [
                'hero_kicker' => 'Dinas Kominfo Sleman Unggul',
                'hero_title' => 'Portal Magang Kominfo 2026',
                'hero_period_info' => 'Gelombang Baru Terbuka',
                'hero_cta_text' => 'Daftar Jalur Resmi',
                'persyaratan_umum' => "Syarat 1: Mahasiswa aktif\nSyarat 2: Berkelakuan baik",
                'dokumen_pendaftaran' => "Dokumen 1: Surat Pengantar Kampus\nDokumen 2: Portofolio",
                'hero_image' => $fakeImage,
                'panduan_pdf' => $fakePdf,
            ]);

        $updateResponse->assertRedirect(route('superadmin.landing.index', ['tab' => 'hero']));
        $updateResponse->assertSessionHas('success');

        // Pastikan nilai tersimpan di Pengaturan
        $this->assertEquals('Dinas Kominfo Sleman Unggul', Pengaturan::get('landing_hero_kicker'));
        $this->assertEquals('Portal Magang Kominfo 2026', Pengaturan::get('landing_hero_title'));
        $this->assertEquals('Gelombang Baru Terbuka', Pengaturan::get('landing_hero_period_info'));

        $savedImagePath = Pengaturan::get('landing_hero_image');
        $this->assertNotEmpty($savedImagePath);
        Storage::disk('public')->assertExists($savedImagePath);

        $savedPdfPath = Pengaturan::get('landing_panduan_pdf');
        $this->assertNotEmpty($savedPdfPath);
        Storage::disk('public')->assertExists($savedPdfPath);

        // Periksa Halaman Depan Publik merender teks dan foto CMS
        $publicLanding = $this->get(route('landing'));
        $publicLanding->assertOk();
        $publicLanding->assertSee('Dinas Kominfo Sleman Unggul');
        $publicLanding->assertSee('Portal Magang Kominfo 2026');
        $publicLanding->assertSee('Gelombang Baru Terbuka');
        $publicLanding->assertSee('Daftar Jalur Resmi');

        // Periksa Endpoint Download Panduan PDF menyajikan file PDF kustom
        $pdfResponse = $this->get(route('panduan.pdf'));
        $pdfResponse->assertOk();
        $pdfResponse->assertHeader('content-type', 'application/pdf');
    }

    /**
     * FAILURE LOGIC AUDIT - TASK A: Mutasi via JSON / AJAX oleh Super Admin wajib ditolak 403 dengan format JSON.
     */
    public function test_superadmin_forbidden_json_mutation_returns_403_json(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->postJson(route('admin.kuota.store'), [
                'bulan_magang_id' => $this->bulanMagang->id,
                'jurusan_id' => $this->jurusan->id,
                'jumlah' => 10,
            ]);

        $response->assertStatus(403);
        $response->assertJson([
            'status' => 'error',
            'message' => 'SuperAdmin hanya memiliki hak akses lihat (read-only) pada modul operasional role lain.',
        ]);
    }

    /**
     * FAILURE LOGIC AUDIT - TASK D: Super Admin dapat membuat peserta jalur khusus tanpa slot khusus spesifik (slot_khusus_id = null).
     */
    public function test_superadmin_can_create_peserta_jalur_khusus_without_slot_khusus(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->post(route('superadmin.peserta-khusus.store'), [
                'nama' => 'Budi Santoso Jalur Bebas',
                'email' => 'budi.bebas@ugm.ac.id',
                'nim_nisn' => '22/999888/SV/00001',
                'institusi' => 'Sekolah Vokasi UGM',
                'jurusan_id' => $this->jurusan->id,
                'jenjang' => 'd3',
                'no_hp' => '081234567899',
                'bidang_id' => $this->bidang->id,
                'pembimbing_id' => $this->pembimbing->id,
                'bulan_mulai' => '2026-11',
                'durasi_bulan' => 3,
                'slot_khusus_id' => '', // Empty / null
                'catatan' => 'Tanpa slot khusus spesifik',
            ]);

        $response->assertRedirect(route('superadmin.monitoring.index'));
        $response->assertSessionHas('success');

        $createdUser = User::where('email', 'budi.bebas@ugm.ac.id')->firstOrFail();
        $this->assertDatabaseHas('penempatan', [
            'peserta_id' => $createdUser->id,
            'slot_khusus_id' => null,
            'jalur' => 'khusus',
            'status' => StatusPenempatanEnum::Aktif->value,
        ]);
    }

    /**
     * FAILURE LOGIC AUDIT - TASK D: Validasi menolak pembuatan peserta jika NIM/NISN sudah ada di sistem.
     */
    public function test_superadmin_cannot_create_peserta_with_duplicate_nim(): void
    {
        // Daftarkan peserta pertama dengan NIM tertentu
        $existingPeserta = User::factory()->create([
            'email' => 'existing.peserta@ugm.ac.id',
            'role' => RoleEnum::Peserta,
            'status_aktif' => true,
        ]);
        $existingPeserta->profil()->create([
            'nim_nisn' => '1234567890',
            'institusi' => 'Universitas Indonesia',
            'jurusan_id' => $this->jurusan->id,
            'jenjang' => 's1',
            'no_hp' => '0811111111',
        ]);

        // Coba daftarkan peserta baru dengan NIM yang sama
        $response = $this->actingAs($this->superAdmin)
            ->post(route('superadmin.peserta-khusus.store'), [
                'nama' => 'Peserta Kloning NIM',
                'email' => 'peserta.kloning@ugm.ac.id',
                'nim_nisn' => '1234567890', // Duplikat
                'institusi' => 'Universitas Gadjah Mada',
                'jurusan_id' => $this->jurusan->id,
                'jenjang' => 's1',
                'pembimbing_id' => $this->pembimbing->id,
                'bulan_mulai' => '2026-11',
                'durasi_bulan' => 3,
            ]);

        $response->assertSessionHasErrors(['nim_nisn']);
        $this->assertDatabaseMissing('users', ['email' => 'peserta.kloning@ugm.ac.id']);
    }

    /**
     * FAILURE LOGIC AUDIT - TASK D: Validasi menolak jika pembimbing yang dipilih tidak aktif atau bukan role pembimbing.
     */
    public function test_superadmin_cannot_create_peserta_with_inactive_or_non_pembimbing(): void
    {
        $inactivePembimbing = User::factory()->create([
            'role' => RoleEnum::Pembimbing,
            'status_aktif' => false, // Non-aktif
        ]);

        $pesertaUser = User::factory()->create([
            'role' => RoleEnum::Peserta,
            'status_aktif' => true,
        ]);

        // Coba pilih pembimbing non-aktif
        $responseInactive = $this->actingAs($this->superAdmin)
            ->post(route('superadmin.peserta-khusus.store'), [
                'nama' => 'Tes Pembimbing Non-Aktif',
                'email' => 'tes.inactive@example.com',
                'nim_nisn' => 'NIM-INACTIVE-01',
                'institusi' => 'Kampus Sleman',
                'jurusan_id' => $this->jurusan->id,
                'jenjang' => 's1',
                'pembimbing_id' => $inactivePembimbing->id,
                'bulan_mulai' => '2026-11',
                'durasi_bulan' => 3,
            ]);
        $responseInactive->assertSessionHasErrors(['pembimbing_id']);

        // Coba pilih user yang bukan role pembimbing
        $responseWrongRole = $this->actingAs($this->superAdmin)
            ->post(route('superadmin.peserta-khusus.store'), [
                'nama' => 'Tes Role Salah',
                'email' => 'tes.wrongrole@example.com',
                'nim_nisn' => 'NIM-WRONG-01',
                'institusi' => 'Kampus Sleman',
                'jurusan_id' => $this->jurusan->id,
                'jenjang' => 's1',
                'pembimbing_id' => $pesertaUser->id,
                'bulan_mulai' => '2026-11',
                'durasi_bulan' => 3,
            ]);
        $responseWrongRole->assertSessionHasErrors(['pembimbing_id']);
    }

    /**
     * FAILURE LOGIC AUDIT - TASK B: Validasi menolak pembuatan staf baru dengan email duplikat atau password lemah.
     */
    public function test_superadmin_cannot_create_staf_with_weak_password_or_duplicate_email(): void
    {
        // Email duplikat
        $responseDuplicate = $this->actingAs($this->superAdmin)
            ->post(route('superadmin.staf.store'), [
                'nama' => 'Duplikat Admin',
                'email' => $this->admin->email, // Duplikat
                'role' => 'admin',
            ]);
        $responseDuplicate->assertSessionHasErrors(['email']);

        // Password lemah (< 8 karakter atau tanpa angka)
        $responseWeakPassword = $this->actingAs($this->superAdmin)
            ->post(route('superadmin.staf.store'), [
                'nama' => 'Staf Password Lemah',
                'email' => 'weak.password@slemankab.go.id',
                'role' => 'admin',
                'password' => 'hanyahuruf', // Tanpa angka
            ]);
        $responseWeakPassword->assertSessionHasErrors(['password']);
    }

    /**
     * FAILURE LOGIC AUDIT - TASK E: Validasi CMS Landing Page menolak berkas non-gambar atau dokumen non-PDF.
     */
    public function test_landing_cms_rejects_invalid_file_mimes_and_sizes(): void
    {
        Storage::fake('public');

        $fakeExeAsPdf = UploadedFile::fake()->create('malicious.exe', 500, 'application/x-msdownload');
        $fakeLargeImage = UploadedFile::fake()->image('huge_photo.jpg')->size(3000); // 3 MB > batas 2 MB

        $response = $this->actingAs($this->superAdmin)
            ->put(route('superadmin.landing.update'), [
                'hero_title' => 'Judul Valid',
                'hero_image' => $fakeLargeImage,
                'panduan_pdf' => $fakeExeAsPdf,
            ]);

        $response->assertSessionHasErrors(['hero_image', 'panduan_pdf']);
    }

    /**
     * TASK E.2: SuperAdmin dapat memperbarui konten Tahapan Alur Magang dan tersinkronisasi ke Landing & Peta Alur.
     */
    public function test_superadmin_can_update_tahapan_magang_and_it_reflects_on_landing_and_peta_alur(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->put(route('superadmin.landing.update'), [
                'section' => 'tahapan',
                'tahapan_heading' => 'Tahapan Pendaftaran Magang Sleman Keren',
                'tahapan_summary' => "Pertama buat akun resmi.\nKedua unggah berkas seleksi.\nKetiga laksanakan magang dinas.\nKeempat terima sertifikat kelulusan.",
                'step1_title' => 'Buat Akun Portal',
                'step1_desc' => 'Calon pendaftar wajib membuat akun portal resmi Diskominfo.',
                'step2_title' => 'Kurasi Berkas',
                'step2_desc' => 'Admin Diskominfo menyeleksi berkas dalam 1-2 hari kerja.',
                'step3_title' => 'Dinas Lapangan',
                'step3_desc' => 'Bekerja bersama tim ASN profesional Sleman.',
                'step4_title' => 'E-Sertifikat',
                'step4_desc' => 'Unduh sertifikat kelulusan bertanda tangan elektronik sah.',
            ]);

        $response->assertRedirect(route('superadmin.landing.index', ['tab' => 'tahapan']));
        $response->assertSessionHas('success');

        // Verifikasi di Halaman Publik Landing Page
        $landingResponse = $this->get(route('landing'));
        $landingResponse->assertOk();
        $landingResponse->assertSee('Tahapan Pendaftaran Magang');
        $landingResponse->assertSee('Sleman Keren');
        $landingResponse->assertSee('Buat Akun Portal');
        $landingResponse->assertSee('Calon pendaftar wajib membuat akun portal resmi Diskominfo.');

        // Verifikasi di Halaman Publik Peta Alur Interaktif
        $petaResponse = $this->get(route('peta-alur'));
        $petaResponse->assertOk();
        $petaResponse->assertSee('BUAT AKUN PORTAL');
        $petaResponse->assertSee('Calon pendaftar wajib membuat akun portal resmi');
        $petaResponse->assertSee('Diskominfo.');
    }

    /**
     * TASK E.3: SuperAdmin dapat memperbarui FAQ dan tersinkronisasi ke Landing Page.
     */
    public function test_superadmin_can_update_faq_and_it_reflects_on_landing_page(): void
    {
        $customFaq = [
            [
                'id' => 'tanya_khusus',
                'nama' => 'Pertanyaan Khusus Sleman',
                'items' => [
                    [
                        'q' => 'Apakah kuota magang di Diskominfo Sleman selalu diperbarui?',
                        'a' => 'Ya, kuota di portal MAGKOM diperbarui secara otomatis dan realtime setiap detik.',
                    ],
                ],
            ],
        ];

        $response = $this->actingAs($this->superAdmin)
            ->put(route('superadmin.landing.update'), [
                'section' => 'faq',
                'faq_heading' => 'Pertanyaan Populer Seputar Magang Sleman',
                'faq_json' => json_encode($customFaq),
            ]);

        $response->assertRedirect(route('superadmin.landing.index', ['tab' => 'faq']));
        $response->assertSessionHas('success');

        // Verifikasi di Landing Page
        $landingResponse = $this->get(route('landing'));
        $landingResponse->assertOk();
        $landingResponse->assertSee('Pertanyaan Populer Seputar Magang Sleman');
        $landingResponse->assertSee('Pertanyaan Khusus Sleman');
        $landingResponse->assertSee('Apakah kuota magang di Diskominfo Sleman selalu diperbarui?');
        $landingResponse->assertSee('Ya, kuota di portal MAGKOM diperbarui secara otomatis dan realtime setiap detik.');
    }

    /**
     * TASK E.4: SuperAdmin dapat memperbarui Footer Kontak dan tersinkronisasi ke Landing Page.
     */
    public function test_superadmin_can_update_footer_contacts_and_it_reflects_on_landing_page(): void
    {
        $customMedsos = [
            ['platform' => 'Instagram', 'label' => 'diskominfo_custom_ig', 'url' => 'https://instagram.com/diskominfo_custom_ig'],
        ];

        $customTautan = [
            ['label' => 'Portal Layanan Terpadu Sleman', 'url' => 'https://layanan.slemankab.go.id'],
        ];

        $response = $this->actingAs($this->superAdmin)
            ->put(route('superadmin.landing.update'), [
                'section' => 'footer',
                'footer_alamat' => 'Gedung Smart City Lantai 3, Sleman, DI Yogyakarta 55511',
                'footer_telepon' => '(0274) 1234567',
                'footer_email' => 'smartcity@slemankab.go.id',
                'footer_jam_layanan' => 'Senin - Jumat 08.00 - 16.00 WIB',
                'footer_copyright' => 'Dinas Kominfo Sleman Smart City Hub 2026.',
                'footer_medsos_json' => json_encode($customMedsos),
                'footer_tautan_json' => json_encode($customTautan),
            ]);

        $response->assertRedirect(route('superadmin.landing.index', ['tab' => 'footer']));
        $response->assertSessionHas('success');

        // Verifikasi di Landing Page
        $landingResponse = $this->get(route('landing'));
        $landingResponse->assertOk();
        $landingResponse->assertSee('Gedung Smart City Lantai 3, Sleman, DI Yogyakarta 55511');
        $landingResponse->assertSee('(0274) 1234567');
        $landingResponse->assertSee('smartcity@slemankab.go.id');
        $landingResponse->assertSee('diskominfo_custom_ig');
        $landingResponse->assertSee('Portal Layanan Terpadu Sleman');
        $landingResponse->assertSee('Dinas Kominfo Sleman Smart City Hub 2026.');
    }

    /**
     * TASK E.5: SuperAdmin dapat memperbarui halaman Kebijakan Privasi dan tersinkronisasi ke /kebijakan-privasi.
     */
    public function test_superadmin_can_update_kebijakan_privasi_and_it_reflects_on_public_page(): void
    {
        $customSections = [
            [
                'nomor' => 1,
                'judul' => 'Prinsip Kedaulatan Data Pemkab Sleman',
                'konten' => 'Data pendaftar magang dikelola dengan standar keamanan kementerian dan tidak dialirkan keluar negeri.',
                'items' => [
                    'Penyimpanan data lokal di Data Center Pemerintah Kabupaten Sleman.',
                    'Akses berbasis token satu kali pakai untuk pembimbing.',
                ],
            ],
        ];

        $response = $this->actingAs($this->superAdmin)
            ->put(route('superadmin.landing.update'), [
                'section' => 'kebijakan',
                'kebijakan_badge' => 'STANDAR KEAMANAN SPBE NASIONAL',
                'kebijakan_title' => 'Prinsip Privasi Magang Sleman 2026',
                'kebijakan_updated_at' => 'Diperbarui November 2026 oleh Diskominfo',
                'kebijakan_sections_json' => json_encode($customSections),
            ]);

        $response->assertRedirect(route('superadmin.landing.index', ['tab' => 'kebijakan']));
        $response->assertSessionHas('success');

        // Verifikasi di Halaman Publik Kebijakan Privasi
        $kebijakanResponse = $this->get(route('kebijakan-privasi'));
        $kebijakanResponse->assertOk();
        $kebijakanResponse->assertSee('STANDAR KEAMANAN SPBE NASIONAL');
        $kebijakanResponse->assertSee('Prinsip Privasi Magang Sleman 2026');
        $kebijakanResponse->assertSee('Prinsip Kedaulatan Data Pemkab Sleman');
        $kebijakanResponse->assertSee('Data Center Pemerintah Kabupaten Sleman');
    }

    /**
     * FAILURE LOGIC AUDIT - TASK E: Validasi CMS menolak JSON cacat/malformed untuk FAQ dan Kebijakan.
     */
    public function test_landing_cms_rejects_malformed_json_for_faq_and_kebijakan(): void
    {
        // Malformed JSON pada FAQ
        $responseFaq = $this->actingAs($this->superAdmin)
            ->put(route('superadmin.landing.update'), [
                'section' => 'faq',
                'faq_heading' => 'Heading Valid',
                'faq_json' => '{ini bukan json yang valid',
            ]);
        $responseFaq->assertSessionHas('error');

        // Malformed JSON pada Kebijakan
        $responseKebijakan = $this->actingAs($this->superAdmin)
            ->put(route('superadmin.landing.update'), [
                'section' => 'kebijakan',
                'kebijakan_badge' => 'Badge Valid',
                'kebijakan_title' => 'Title Valid',
                'kebijakan_updated_at' => 'Time Valid',
                'kebijakan_sections_json' => '["tidak tertutup',
            ]);
        $responseKebijakan->assertSessionHas('error');
    }

    /**
     * TASK E.6: SuperAdmin dapat memperbarui Header & SEO Meta dan tersinkronisasi ke Landing Page (Zero Hardcode).
     */
    public function test_superadmin_can_update_header_and_seo_meta_and_it_reflects_on_landing_page(): void
    {
        $customNav = [
            ['label' => 'Beranda Utama Sleman', 'url' => '#beranda'],
            ['label' => 'Pilihan Formasi', 'url' => '#kuota'],
        ];

        $response = $this->actingAs($this->superAdmin)
            ->put(route('superadmin.landing.update'), [
                'section' => 'header_seo',
                'meta_title' => 'Portal Magang Sleman Juara 2026',
                'meta_desc' => 'Daftar magang instansi pemerintah Diskominfo Sleman secara cepat dan terintegrasi.',
                'header_brand_name' => 'MAGKOM-SLEMAN',
                'header_brand_sub' => 'Smart Government Internship Hub',
                'header_nav_json' => json_encode($customNav),
            ]);

        $response->assertRedirect(route('superadmin.landing.index', ['tab' => 'header_seo']));
        $response->assertSessionHas('success');

        // Verifikasi di Landing Page
        $landingResponse = $this->get(route('landing'));
        $landingResponse->assertOk();
        $landingResponse->assertSee('Portal Magang Sleman Juara 2026');
        $landingResponse->assertSee('Daftar magang instansi pemerintah Diskominfo Sleman secara cepat dan terintegrasi.');
        $landingResponse->assertSee('MAGKOM-SLEMAN');
        $landingResponse->assertSee('Smart Government Internship Hub');
        $landingResponse->assertSee('Beranda Utama Sleman');
        $landingResponse->assertSee('Pilihan Formasi');
    }

    /**
     * TASK E.7: SuperAdmin dapat memperbarui teks interaktif Kuota & Formasi (Zero Hardcode).
     */
    public function test_superadmin_can_update_kuota_interactive_copy_and_it_reflects_on_landing_page(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->put(route('superadmin.landing.update'), [
                'section' => 'kuota',
                'kuota_heading' => 'Alokasi Kursi dan Formasi Magang Sleman',
                'kuota_subheading' => 'Tentukan jenjang pendidikan serta bulan pelaksanaan magang yang diinginkan.',
                'kuota_q1_label' => '1. Status Jenjang Anda?',
                'kuota_pill_smk' => 'Pelajar SMK Hebat',
                'kuota_pill_mhs' => 'Mahasiswa PTN/PTS',
                'kuota_q2_label' => '2. Rencana Bulan Penempatan?',
                'kuota_empty_note' => 'Periode pendaftaran belum dibuka.',
                'kuota_q3_label' => '3. Formasi Mandiri atau Rombongan?',
                'kuota_pill_mandiri' => 'Sendiri (Individu)',
                'kuota_pill_tim' => 'Beregu (Tim Kolaborasi)',
                'kuota_team_label' => 'Jumlah Partner Satu Tim:',
                'kuota_team_note' => 'Hanya perwakilan ketua yang mendaftarkan data anggota tim.',
                'kuota_gauge_desc' => 'Menghitung realtime ketersediaan formasi...',
                'kuota_legend_tersedia' => 'Kapasitas Longgar',
                'kuota_legend_hampir' => 'Sisa Terbatas',
                'kuota_legend_penuh' => 'Antrean Waiting List',
                'kuota_btn_tersedia' => 'Klaim Formasi ➔',
                'kuota_btn_waiting' => 'Masuk Cadangan ➔',
                'kuota_btn_tutup' => 'Formasi Ditutup',
            ]);

        $response->assertRedirect(route('superadmin.landing.index', ['tab' => 'kuota']));
        $response->assertSessionHas('success');

        // Verifikasi di Landing Page
        $landingResponse = $this->get(route('landing'));
        $landingResponse->assertOk();
        $landingResponse->assertSee('Alokasi Kursi dan Formasi Magang Sleman');
        $landingResponse->assertSee('Tentukan jenjang pendidikan serta bulan pelaksanaan magang yang diinginkan.');
        $landingResponse->assertSee('1. Status Jenjang Anda?');
        $landingResponse->assertSee('Pelajar SMK Hebat');
        $landingResponse->assertSee('Mahasiswa PTN/PTS');
        $landingResponse->assertSee('2. Rencana Bulan Penempatan?');
        $landingResponse->assertSee('3. Formasi Mandiri atau Rombongan?');
        $landingResponse->assertSee('Sendiri (Individu)');
        $landingResponse->assertSee('Beregu (Tim Kolaborasi)');
        $landingResponse->assertSee('Jumlah Partner Satu Tim:');
        $landingResponse->assertSee('Hanya perwakilan ketua yang mendaftarkan data anggota tim.');
        $landingResponse->assertSee('Menghitung realtime ketersediaan formasi...');
        $landingResponse->assertSee('Kapasitas Longgar');
        $landingResponse->assertSee('Sisa Terbatas');
        $landingResponse->assertSee('Antrean Waiting List');
    }
}
