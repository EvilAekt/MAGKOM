<?php

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\Kuota;
use App\Models\User;
use App\Queries\Admin\Kuota\KuotaIndexQuery;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminKuotaRevampTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

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

    private function createAdminUser(): User
    {
        $admin = new User;
        $admin->nama = 'Admin Kuota Test';
        $admin->email = 'admin.kuota.revamp@slemankab.go.id';
        $admin->password = 'Password123!';
        $admin->role = RoleEnum::Admin;
        $admin->status_aktif = true;
        $admin->must_change_password = false;
        $admin->email_verified_at = now();
        $admin->save();

        return $admin;
    }

    /**
     * Memastikan header baru memuat judul Bulan & kuota serta tombol aksi Ubah bulan dan Buat bulan baru.
     */
    public function test_kuota_page_displays_redesigned_header_and_hero_banner(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)->get(route('admin.kuota.index'));
        $response->assertStatus(200);

        // Header halaman
        $response->assertSee('Bulan &amp; kuota', false);
        $response->assertSee('Atur bulan pendaftaran dan kuota magang per bidang.', false);
        $response->assertSee('Ubah bulan', false);
        $response->assertSee('Buat bulan baru', false);

        // Hero Banner
        $response->assertDontSee('Bulan Aktif', false);
        $response->assertSee('Tanggal buka', false);
        $response->assertSee('Tanggal tutup', false);
        $response->assertSee('Mulai magang', false);
        $response->assertSee('Kuota terisi', false);
    }

    /**
     * Memastikan tabel menampilkan agregasi Kuota per Bidang dengan badge jenjang dan tombol Ubah.
     */
    public function test_kuota_page_displays_quota_per_bidang_table(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)->get(route('admin.kuota.index'));
        $response->assertStatus(200);

        // Section header & x-panel card container
        $response->assertSee('x_panel', false);
        $response->assertSee('x_title', false);
        $response->assertSee('Kuota per bidang', false);
        $response->assertSee('Tambah kuota', false);
        $response->assertSee('btn-tambah-kuota-header', false);

        // Kolom Thead
        $response->assertSee('Bidang', false);
        $response->assertSee('Jurusan yang Dibutuhkan', false);
        $response->assertSee('Jenjang', false);
        $response->assertSee('Durasi', false);
        $response->assertSee('Kuota Terisi', false);
        $response->assertSee('Kapasitas', false);

        // Memastikan tombol Ubah di baris data
        $response->assertSee('Ubah', false);

        // Memastikan 4 bidang alias dan inisial tampil
        $response->assertSee('Aplikasi Informatika', false);
        $response->assertSee('Statistik', false);
        $response->assertSee('Persandian', false);
        $response->assertSee('Sekretariat', false);
    }

    /**
     * Memastikan banner info validasi kapasitas pembimbing tidak lagi ditampilkan di bawah tabel.
     */
    public function test_kuota_page_does_not_display_mentor_capacity_validation_banner(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)->get(route('admin.kuota.index'));
        $response->assertStatus(200);

        $response->assertDontSee('Validasi Kapasitas Pembimbing', false);
        $response->assertDontSee('Sistem otomatis memvalidasi agar total kuota tidak melebihi kapasitas bimbingan, yaitu jumlah Pembimbing aktif di setiap bidang.', false);
    }

    /**
     * Memastikan modal-modal interaktif ter-render di dokumen dengan elemen-elemen desain baru.
     */
    public function test_kuota_modals_exist_in_dom(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)->get(route('admin.kuota.index'));
        $response->assertStatus(200);

        $response->assertSee('id="modalUbahBulan"', false);
        $response->assertSee('id="modalBuatBulan"', false);
        $response->assertSee('id="modalTambahKuota"', false);
        $response->assertSee('id="modalEditBidang"', false);

        // Memastikan isi modal Buat Bulan Baru sesuai dengan desain referensi
        $response->assertSee('Buat bulan baru', false);
        $response->assertSee('Tentukan jadwal pendaftaran dan mulai magang.', false);
        $response->assertSee('Nama bulan', false);
        $response->assertSee('Contoh: Gelombang 3', false);
        $response->assertSee('Tanggal buka', false);
        $response->assertSee('Tanggal tutup', false);
        $response->assertSee('Tanggal mulai magang', false);
        $response->assertSee('Harus setelah tanggal tutup pendaftaran.', false);
        $response->assertSee('Status awal', false);
        $response->assertSee('Draft', false);
        $response->assertSee('Dibuka', false);
        $response->assertSee('Setelah bulan magang disimpan, atur kuota tiap bidang lewat tombol Tambah kuota.', false);
        $response->assertSee('Simpan bulan', false);
    }

    /**
     * Memastikan admin dapat membuat bulan magang baru berstatus Draft via storeBulan.
     */
    public function test_admin_can_store_new_bulan_magang_as_draft(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)->post(route('admin.kuota.bulan.store'), [
            'nama_bulan' => 'Gelombang 3',
            'bulan_mulai' => '2027-01-01',
            'tgl_buka' => '2026-12-01',
            'tgl_tutup' => '2026-12-20',
            'status_awal' => 'draft',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('bulan_magang', [
            'nama_bulan' => 'Gelombang 3',
            'bulan_mulai' => '2027-01-01 00:00:00',
            'tgl_buka' => '2026-12-01 00:00:00',
            'tgl_tutup' => '2026-12-20 00:00:00',
            'aktif' => false,
        ]);
    }

    /**
     * Memastikan admin dapat membuat bulan magang baru berstatus Dibuka via storeBulan.
     */
    public function test_admin_can_store_new_bulan_magang_as_dibuka(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)->post(route('admin.kuota.bulan.store'), [
            'nama_bulan' => 'Gelombang 4',
            'bulan_mulai' => '2027-02-01',
            'tgl_buka' => '2027-01-01',
            'tgl_tutup' => '2027-01-20',
            'status_awal' => 'dibuka',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('bulan_magang', [
            'nama_bulan' => 'Gelombang 4',
            'bulan_mulai' => '2027-02-01 00:00:00',
            'tgl_buka' => '2027-01-01 00:00:00',
            'tgl_tutup' => '2027-01-20 00:00:00',
            'aktif' => true,
        ]);
    }

    /**
     * Memastikan validasi menolak jika tanggal mulai magang tidak setelah tanggal tutup pendaftaran.
     */
    public function test_validation_rejects_bulan_mulai_not_after_tgl_tutup(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)->post(route('admin.kuota.bulan.store'), [
            'nama_bulan' => 'Gelombang Invalid',
            'bulan_mulai' => '2026-12-15',
            'tgl_buka' => '2026-12-01',
            'tgl_tutup' => '2026-12-20',
            'status_awal' => 'draft',
        ]);

        $response->assertSessionHasErrors('bulan_mulai');
    }

    /**
     * Memastikan admin dapat memperbarui jadwal bulan magang aktif via updateBulan.
     */
    public function test_admin_can_update_existing_bulan_magang(): void
    {
        $admin = $this->createAdminUser();
        $bulan = BulanMagang::first();

        $response = $this->actingAs($admin)->put(route('admin.kuota.bulan.update', $bulan->id), [
            'nama_bulan' => 'Gelombang 1 Diperbarui',
            'bulan_mulai' => $bulan->bulan_mulai->format('Y-m-d'),
            'tgl_buka' => '2026-10-05',
            'tgl_tutup' => '2026-10-30',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('bulan_magang', [
            'id' => $bulan->id,
            'nama_bulan' => 'Gelombang 1 Diperbarui',
            'tgl_buka' => '2026-10-05 00:00:00',
            'tgl_tutup' => '2026-10-30 00:00:00',
        ]);
    }

    /**
     * Memastikan modal Ubah Bulan memiliki tata letak dan elemen visual yang selaras dengan Buat Bulan Baru.
     */
    public function test_modal_ubah_bulan_matches_buat_bulan_design(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)->get(route('admin.kuota.index'));
        $response->assertStatus(200);

        // Header & Judul
        $response->assertSee('id="modalUbahBulan"', false);
        $response->assertSee('Ubah bulan', false);
        $response->assertSee('Tentukan jadwal pendaftaran dan mulai magang.', false);

        // Form fields
        $response->assertSee('id="ubahNamaBulan"', false);
        $response->assertSee('id="ubahBulanTglBuka"', false);
        $response->assertSee('id="ubahBulanTglTutup"', false);
        $response->assertSee('id="ubahBulanMulai"', false);
        $response->assertSee('Harus setelah tanggal tutup pendaftaran.', false);

        // Status Bulan (Pills)
        $response->assertSee('Status bulan', false);
        $response->assertSee('id="btnUbahStatusDraft"', false);
        $response->assertSee('id="btnUbahStatusDibuka"', false);

        // Callout Info & Submit
        $response->assertSee('Perubahan jadwal pendaftaran dan mulai magang akan langsung disinkronkan ke portal publik.', false);
        $response->assertSee('id="btnSubmitUbahBulan"', false);
        $response->assertSee('Simpan perubahan', false);
    }

    /**
     * Memastikan modal Tambah Kuota memuat seluruh elemen dan kontrol desain sesuai spesifikasi.
     */
    public function test_modal_tambah_kuota_displays_all_elements_from_design(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)->get(route('admin.kuota.index'));
        $response->assertStatus(200);

        // Header
        $response->assertSee('id="modalTambahKuota"', false);
        $response->assertSee('Tambah kuota', false);
        $response->assertSee('Atur kuota, jurusan, jenjang, dan durasi untuk satu bidang.', false);

        // Baris 1: Bulan (Read-Only) & Bidang (Dropdown)
        $response->assertSee('Bulan', false);
        $response->assertSee('id="tambahKuotaBidangSelect"', false);

        // Baris 2: Jurusan yang Dibutuhkan & Tombol + Jurusan
        $response->assertSee('Jurusan yang dibutuhkan', false);
        $response->assertSee('id="jurusanChipsContainer"', false);
        $response->assertSee('+ Jurusan', false);
        $response->assertSee('id="extraJurusanSelect"', false);

        // Baris 3: Jenjang Pills
        $response->assertSee('Jenjang', false);
        $response->assertSee('id="pillJenjangSMK"', false);
        $response->assertSee('id="pillJenjangD3"', false);
        $response->assertSee('id="pillJenjangS1"', false);

        // Baris 4: Jumlah kuota, Durasi min, Durasi max
        $response->assertSee('Jumlah kuota', false);
        $response->assertSee('id="tambahKuotaJumlahInput"', false);
        $response->assertSee('Durasi minimum', false);
        $response->assertSee('Durasi maksimum', false);

        // Baris 5: Warning Box Kapasitas
        $response->assertSee('id="boxWarningKapasitas"', false);
        $response->assertSee('Melebihi kapasitas pembimbing', false);

        // Footer
        $response->assertSee('id="btnSubmitTambahKuota"', false);
        $response->assertSee('Simpan kuota', false);
    }

    /**
     * Memastikan admin dapat memperbarui nama bulan dan status buka/draft via updateBulan.
     */
    public function test_admin_can_update_bulan_with_status_and_name(): void
    {
        $admin = $this->createAdminUser();
        $bulan = BulanMagang::first();

        // Update menjadi draft
        $response = $this->actingAs($admin)->put(route('admin.kuota.bulan.update', $bulan->id), [
            'nama_bulan' => 'Gelombang 1 Draft',
            'bulan_mulai' => $bulan->bulan_mulai->format('Y-m-d'),
            'tgl_buka' => '2026-10-01',
            'tgl_tutup' => '2026-10-25',
            'status_awal' => 'draft',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('bulan_magang', [
            'id' => $bulan->id,
            'nama_bulan' => 'Gelombang 1 Draft',
            'aktif' => false,
        ]);

        // Update kembali menjadi dibuka
        $response2 = $this->actingAs($admin)->put(route('admin.kuota.bulan.update', $bulan->id), [
            'nama_bulan' => 'Gelombang 1 Dibuka',
            'bulan_mulai' => $bulan->bulan_mulai->format('Y-m-d'),
            'tgl_buka' => '2026-10-01',
            'tgl_tutup' => '2026-10-25',
            'status_awal' => 'dibuka',
        ]);

        $response2->assertSessionHas('success');
        $this->assertDatabaseHas('bulan_magang', [
            'id' => $bulan->id,
            'nama_bulan' => 'Gelombang 1 Dibuka',
            'aktif' => true,
        ]);
    }

    /**
     * Memastikan admin dapat menyimpan kuota jamak melalui modal baru dengan payload jurusan_ids[].
     */
    public function test_admin_can_store_kuota_with_jurusan_ids_array(): void
    {
        $admin = $this->createAdminUser();
        $bulan = BulanMagang::create([
            'nama_bulan' => 'Bulan Uji Kuota Jamak',
            'bulan_mulai' => '2027-05-01',
            'tgl_buka' => '2027-04-01',
            'tgl_tutup' => '2027-04-20',
            'aktif' => true,
        ]);
        $jurusans = Jurusan::take(2)->get();
        $bidang = Bidang::first();

        $response = $this->actingAs($admin)->post(route('admin.kuota.store'), [
            'bulan_magang_id' => $bulan->id,
            'bidang_id' => $bidang->id,
            'jurusan_ids' => $jurusans->pluck('id')->toArray(),
            'jumlah' => 4,
            'durasi_min' => '3 bulan',
            'durasi_max' => '6 bulan',
            'jenjang' => ['S1'],
        ]);

        $response->assertSessionHas('success');

        foreach ($jurusans as $j) {
            $this->assertDatabaseHas('kuota', [
                'bulan_magang_id' => $bulan->id,
                'jurusan_id' => $j->id,
                'jumlah' => 4,
                'aktif' => true,
            ]);
        }
    }

    /**
     * Memastikan badge status Dibuka / Ditutup tampil pada kolom kapasitas tabel kuota per bidang.
     */
    public function test_kuota_page_displays_bidang_status_badges(): void
    {
        $admin = $this->createAdminUser();
        $bulan = BulanMagang::first();
        $bidangTutup = Bidang::with('jurusan')->orderBy('id', 'desc')->first();

        // Kosongkan kuota bidang terakhir agar berstatus Ditutup (0 peserta)
        if ($bidangTutup) {
            foreach ($bidangTutup->jurusan as $j) {
                Kuota::updateOrCreate(
                    ['bulan_magang_id' => $bulan->id, 'jurusan_id' => $j->id],
                    ['jumlah' => 0, 'terisi' => 0, 'aktif' => false]
                );
            }
        }

        $response = $this->actingAs($admin)->get(route('admin.kuota.index', ['bulan_magang_id' => $bulan->id]));
        $response->assertStatus(200);

        // Memastikan elemen badge status bidang ada di HTML
        $response->assertSee('badge-status-bidang-buka', false);
        $response->assertSee('badge-status-bidang-tutup', false);
        $response->assertSee('Dibuka', false);
        $response->assertSee('Ditutup', false);
        $response->assertSee('id="modalEditBidang"', false);
        $response->assertSee('Status Penempatan Bidang', false);
        $response->assertSee('Total Kapasitas Masuk', false);
    }

    /**
     * Memastikan admin dapat menutup kuota bidang melalui updateBidangKuota dengan status_bidang tutup.
     */
    public function test_admin_can_close_bidang_kuota(): void
    {
        $admin = $this->createAdminUser();
        $bulan = BulanMagang::first();
        $bidang = Bidang::with('jurusan')->first();

        // Pastikan ada kuota awal
        foreach ($bidang->jurusan as $j) {
            Kuota::updateOrCreate(
                ['bulan_magang_id' => $bulan->id, 'jurusan_id' => $j->id],
                ['jumlah' => 5, 'terisi' => 0, 'aktif' => true]
            );
        }

        // Eksekusi penutupan bidang
        $response = $this->actingAs($admin)->put(route('admin.kuota.bidang.update', $bidang->id), [
            'bulan_magang_id' => $bulan->id,
            'status_bidang' => 'tutup',
        ]);

        $response->assertSessionHas('success');

        foreach ($bidang->jurusan as $j) {
            $this->assertDatabaseHas('kuota', [
                'bulan_magang_id' => $bulan->id,
                'jurusan_id' => $j->id,
                'jumlah' => 0,
                'aktif' => false,
            ]);
        }
    }

    /**
     * Memastikan admin dapat memperbarui alokasi kuota dan total kapasitas bidang melalui updateBidangKuota.
     */
    public function test_admin_can_update_bidang_total_capacity(): void
    {
        $admin = $this->createAdminUser();
        $bulan = BulanMagang::first();
        $bidang = Bidang::with('jurusan')->first();

        $alokasiData = [];
        foreach ($bidang->jurusan as $idx => $j) {
            $alokasiData[$j->id] = 3 + $idx;
        }

        $response = $this->actingAs($admin)->put(route('admin.kuota.bidang.update', $bidang->id), [
            'bulan_magang_id' => $bulan->id,
            'status_bidang' => 'buka',
            'alokasi' => $alokasiData,
        ]);

        $response->assertSessionHas('success');

        foreach ($alokasiData as $jurusanId => $expectedJumlah) {
            $this->assertDatabaseHas('kuota', [
                'bulan_magang_id' => $bulan->id,
                'jurusan_id' => $jurusanId,
                'jumlah' => $expectedJumlah,
                'aktif' => true,
            ]);
        }
    }

    private function createSuperAdminUser(): User
    {
        $superAdmin = new User;
        $superAdmin->nama = 'SuperAdmin Kuota Test';
        $superAdmin->email = 'superadmin.kuota.revamp@slemankab.go.id';
        $superAdmin->password = 'Password123!';
        $superAdmin->role = RoleEnum::SuperAdmin;
        $superAdmin->status_aktif = true;
        $superAdmin->must_change_password = false;
        $superAdmin->email_verified_at = now();
        $superAdmin->save();

        return $superAdmin;
    }

    /**
     * Ensures stored Draft bulan stays visible in dropdown with (Draft) label and active month remains default.
     */
    public function test_draft_bulan_remains_visible_in_dropdown_while_active_bulan_stays_default(): void
    {
        $admin = $this->createAdminUser();
        $activeBulan = BulanMagang::where('aktif', true)->orderBy('bulan_mulai', 'asc')->firstOrFail();

        // Store an earlier-dated Draft month to confirm active month still wins default selection
        $storeResponse = $this->actingAs($admin)->post(route('admin.kuota.bulan.store'), [
            'nama_bulan' => 'Draft Awal',
            'bulan_mulai' => '2025-02-01',
            'tgl_buka' => '2025-01-01',
            'tgl_tutup' => '2025-01-20',
            'status_awal' => 'draft',
        ]);

        $draftBulan = BulanMagang::where('nama_bulan', 'Draft Awal')->firstOrFail();
        $storeResponse->assertRedirect(route('admin.kuota.index', ['bulan_magang_id' => $draftBulan->id]));

        // Default page without query param must prioritize the first active month, while listing the Draft option
        $defaultResponse = $this->actingAs($admin)->get(route('admin.kuota.index'));
        $defaultResponse->assertStatus(200);
        $defaultResponse->assertViewHas('selectedBulan', fn ($selected) => $selected && $selected->id === $activeBulan->id);
        $defaultResponse->assertSee('(Draft)', false);
        $defaultResponse->assertSee('Draft Awal', false);
    }

    /**
     * Ensures default kuota index falls back to Draft bulan when no active bulan exists.
     */
    public function test_default_kuota_index_falls_back_to_draft_when_no_active_bulan_exists(): void
    {
        $admin = $this->createAdminUser();
        BulanMagang::query()->update(['aktif' => false]);
        $firstDraft = BulanMagang::orderBy('bulan_mulai', 'asc')->firstOrFail();

        $response = $this->actingAs($admin)->get(route('admin.kuota.index'));

        $response->assertStatus(200);
        $response->assertViewHas('selectedBulan', fn ($selected) => $selected && $selected->id === $firstDraft->id);
        $response->assertSee('hero-status-draft', false);
        $response->assertSee('Status Draft', false);
        $response->assertSee('Buka bulan', false);
    }

    /**
     * Ensures Draft bulan renders Draft status (never Dibuka) even if today is inside open-close date range.
     */
    public function test_draft_bulan_displays_draft_status_even_inside_open_date_range(): void
    {
        $admin = $this->createAdminUser();
        $draftInRange = BulanMagang::create([
            'nama_bulan' => 'Febuari Draft',
            'bulan_mulai' => '2026-11-05',
            'tgl_buka' => '2026-10-01',
            'tgl_tutup' => '2026-10-31',
            'aktif' => false,
        ]);

        $queryData = app(KuotaIndexQuery::class)->getIndexData($draftInRange->id);
        $this->assertTrue($queryData['isDraft']);
        $this->assertFalse($queryData['isPendaftaranBuka']);
        $this->assertFalse($queryData['isTerjadwal']);

        $response = $this->actingAs($admin)->get(route('admin.kuota.index', ['bulan_magang_id' => $draftInRange->id]));
        $response->assertStatus(200);
        $response->assertSee('<span class="hero-status-pill hero-status-draft">', false);
        $response->assertSee('Status Draft — belum dibuka di portal publik', false);
        $response->assertDontSee('<span class="hero-status-pill hero-status-open">', false);
        $response->assertDontSee('<span class="hero-status-pill hero-status-scheduled">', false);
        $response->assertDontSee('<span class="hero-status-pill hero-status-closed">', false);
        $response->assertSee('data-kuota-action="publish-draft"', false);
    }

    /**
     * Ensures Admin sees Buka bulan on Draft, can publish it via updateBulan, and future open dates show Terjadwal.
     */
    public function test_admin_can_publish_draft_bulan_and_future_period_shows_terjadwal_status(): void
    {
        $admin = $this->createAdminUser();
        $draftBulan = BulanMagang::create([
            'nama_bulan' => 'Febuari',
            'bulan_mulai' => '2027-04-01',
            'tgl_buka' => '2027-03-12',
            'tgl_tutup' => '2027-03-31',
            'aktif' => false,
        ]);

        $draftPage = $this->actingAs($admin)->get(route('admin.kuota.index', ['bulan_magang_id' => $draftBulan->id]));
        $draftPage->assertStatus(200);
        $draftPage->assertSee('Buka bulan', false);
        $draftPage->assertSee('data-kuota-action="publish-draft"', false);

        $publishResponse = $this->actingAs($admin)->put(route('admin.kuota.bulan.update', $draftBulan->id), [
            'nama_bulan' => 'Febuari',
            'bulan_mulai' => '2027-04-01',
            'tgl_buka' => '2027-03-12',
            'tgl_tutup' => '2027-03-31',
            'status_awal' => 'dibuka',
        ]);

        $publishResponse->assertSessionHas('success');
        $this->assertDatabaseHas('bulan_magang', [
            'id' => $draftBulan->id,
            'aktif' => true,
        ]);

        $publishedPage = $this->actingAs($admin)->get(route('admin.kuota.index', ['bulan_magang_id' => $draftBulan->id]));
        $publishedPage->assertStatus(200);
        $publishedPage->assertSee('hero-status-scheduled', false);
        $publishedPage->assertSee('Terjadwal', false);
        $publishedPage->assertSee('Pendaftaran dibuka mulai '.Carbon::parse('2027-03-12')->translatedFormat('d F Y'), false);
        $publishedPage->assertDontSee('data-kuota-action="publish-draft"', false);
        $publishedPage->assertSee('Ubah bulan', false);
    }

    /**
     * Ensures SuperAdmin viewing Draft bulan only sees read-only badge without Buka bulan or Ubah bulan buttons.
     */
    public function test_superadmin_viewing_draft_bulan_remains_strictly_read_only(): void
    {
        $superAdmin = $this->createSuperAdminUser();
        $draftBulan = BulanMagang::create([
            'nama_bulan' => 'Draft ReadOnly',
            'bulan_mulai' => '2027-05-01',
            'tgl_buka' => '2027-04-01',
            'tgl_tutup' => '2027-04-20',
            'aktif' => false,
        ]);

        $response = $this->actingAs($superAdmin)->get(route('admin.kuota.index', ['bulan_magang_id' => $draftBulan->id]));
        $response->assertStatus(200);
        $response->assertSee('<span class="hero-status-pill hero-status-draft">', false);
        $response->assertSee('Mode Lihat Saja', false);
        $response->assertDontSee('data-kuota-action="publish-draft"', false);
        $response->assertDontSee('onclick="openUbahBulanModal()"', false);
        $response->assertDontSee('onclick="openBuatBulanModal()"', false);
        $response->assertDontSee('data-kuota-action="open-buat-bulan"', false);

        $mutationResponse = $this->actingAs($superAdmin)->put(route('admin.kuota.bulan.update', $draftBulan->id), [
            'nama_bulan' => 'Hack Draft',
            'bulan_mulai' => '2027-05-01',
            'tgl_buka' => '2027-04-01',
            'tgl_tutup' => '2027-04-20',
            'status_awal' => 'dibuka',
        ]);

        $mutationResponse->assertStatus(403);
        $this->assertDatabaseHas('bulan_magang', [
            'id' => $draftBulan->id,
            'aktif' => false,
        ]);
    }
}
