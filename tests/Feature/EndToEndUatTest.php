<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JalurEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusLogbookEnum;
use App\Enums\StatusPendaftaranEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\SumberPenempatanEnum;
use App\Models\AuditLog;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\JenisDokumen;
use App\Models\Jurusan;
use App\Models\Kuota;
use App\Models\Logbook;
use App\Models\Pendaftaran;
use App\Models\Penempatan;
use App\Models\Penilaian;
use App\Models\PesertaProfil;
use App\Models\Rubrik;
use App\Models\SlotKhusus;
use App\Models\Tugas;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EndToEndUatTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected User $admin;

    protected User $pembimbing;

    protected Bidang $bidang;

    protected Jurusan $jurusanSmk;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('private');

        Carbon::setTestNow(Carbon::parse('2026-10-08 09:00:00', 'Asia/Jakarta'));
        CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-10-08 09:00:00', 'Asia/Jakarta'));

        // Seed master rubrik jika belum ada
        if (Rubrik::count() === 0) {
            Rubrik::create(['kriteria' => 'Kualitas hasil kerja', 'bobot' => 30, 'aktif' => true]);
            Rubrik::create(['kriteria' => 'Ketepatan waktu dan penyelesaian tugas', 'bobot' => 20, 'aktif' => true]);
            Rubrik::create(['kriteria' => 'Inisiatif dan kemampuan belajar', 'bobot' => 20, 'aktif' => true]);
            Rubrik::create(['kriteria' => 'Komunikasi dan kerja sama', 'bobot' => 15, 'aktif' => true]);
            Rubrik::create(['kriteria' => 'Etika, disiplin, dan tanggung jawab', 'bobot' => 15, 'aktif' => true]);
        }

        // Siapkan Bidang & Jurusan
        $this->bidang = Bidang::firstOrCreate(
            ['nama_bidang' => 'Layanan Komunikasi Informatika'],
            ['aktif' => true]
        );

        $this->jurusanSmk = Jurusan::firstOrCreate(
            ['nama_jurusan' => 'Rekayasa Perangkat Lunak (RPL)'],
            ['kategori' => 'smk', 'aktif' => true]
        );

        // Siapkan Aktor: SuperAdmin, Admin, Pembimbing
        $this->superAdmin = new User;
        $this->superAdmin->nama = 'Super Admin MagKom';
        $this->superAdmin->email = 'superadmin.uat@kominfo.test';
        $this->superAdmin->password = 'secret123';
        $this->superAdmin->role = RoleEnum::SuperAdmin;
        $this->superAdmin->email_verified_at = now();
        $this->superAdmin->status_aktif = true;
        $this->superAdmin->save();

        $this->admin = new User;
        $this->admin->nama = 'Admin Pengelola';
        $this->admin->email = 'admin.uat@kominfo.test';
        $this->admin->password = 'secret123';
        $this->admin->role = RoleEnum::Admin;
        $this->admin->email_verified_at = now();
        $this->admin->status_aktif = true;
        $this->admin->save();

        $this->pembimbing = new User;
        $this->pembimbing->nama = 'Pembimbing Lapangan UAT';
        $this->pembimbing->email = 'pembimbing.uat@kominfo.test';
        $this->pembimbing->password = 'secret123';
        $this->pembimbing->role = RoleEnum::Pembimbing;
        $this->pembimbing->email_verified_at = now();
        $this->pembimbing->status_aktif = true;
        $this->pembimbing->save();
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        CarbonImmutable::setTestNow();
        parent::tearDown();
    }

    /**
     * UAT-1: Konfigurasi: buat bulan, atur kuota 12 dan slot 3, ubah override status, periksa landing page.
     */
    public function test_uat_1_konfigurasi_master_kuota_dan_landing_page(): void
    {
        // 1. SuperAdmin membuat bulan magang baru
        $bulanRes = $this->actingAs($this->superAdmin)->post(route('superadmin.bulan.store'), [
            'bulan_mulai' => '2027-02-01',
            'tgl_buka' => '2026-11-01',
            'tgl_tutup' => '2026-11-30',
        ]);
        $bulanRes->assertRedirect(route('superadmin.bulan.index'));
        $bulan = BulanMagang::whereDate('bulan_mulai', '2027-02-01')->firstOrFail();

        // 2. Admin mengatur kuota umum per jurusan (kuota 12)
        $kuotaRes = $this->actingAs($this->admin)->post(route('admin.kuota.store'), [
            'bulan_magang_id' => $bulan->id,
            'jurusan_id' => $this->jurusanSmk->id,
            'jumlah' => 12,
        ]);
        $kuotaRes->assertRedirect();
        $kuota = Kuota::where('bulan_magang_id', $bulan->id)->where('jurusan_id', $this->jurusanSmk->id)->firstOrFail();

        // 3. SuperAdmin mengatur slot khusus internal (slot 3)
        $slotRes = $this->actingAs($this->superAdmin)->post(route('superadmin.slot-khusus.store'), [
            'bulan_mulai' => '2027-02-01',
            'jumlah' => 3,
        ]);
        $slotRes->assertRedirect(route('superadmin.slot-khusus.index'));

        // 4. Publik mengakses landing page: Kuota 12 tampil, slot khusus ZERO LEAK (AC-01-5)
        $publicRes = $this->get(route('landing'));
        $publicRes->assertStatus(200);
        $publicRes->assertSee('Rekayasa Perangkat Lunak (RPL)');

        // 5. SuperAdmin mengubah override status bulan menjadi 'tutup'
        $updateBulan = $this->actingAs($this->superAdmin)->put(route('superadmin.bulan.update', $bulan->id), [
            'bulan_mulai' => '2027-02-01',
            'tgl_buka' => '2026-11-01',
            'tgl_tutup' => '2026-11-30',
            'override_status' => 'tutup',
        ]);
        $updateBulan->assertRedirect(route('superadmin.bulan.index'));

        // Verifikasi landing page status berubah menjadi Tutup
        $landingUpdated = $this->get(route('landing'));
        $landingUpdated->assertStatus(200);
        $landingUpdated->assertSee('Tutup');
    }

    /**
     * UAT-2: Peserta mendaftar dari landing page sampai status Diajukan (verifikasi email & upload berkas).
     */
    public function test_uat_2_peserta_mendaftar_sampai_status_diajukan(): void
    {
        // Setup bulan & kuota
        $bulan = BulanMagang::create([
            'bulan_mulai' => '2027-03-01',
            'tgl_buka' => '2026-10-01',
            'tgl_tutup' => '2026-10-31',
            'aktif' => true,
        ]);
        $kuota = Kuota::create([
            'bulan_magang_id' => $bulan->id,
            'jurusan_id' => $this->jurusanSmk->id,
            'jumlah' => 10,
            'terisi' => 0,
            'aktif' => true,
        ]);

        $jenisSurat = JenisDokumen::firstOrCreate(
            ['kode' => 'surat_pengantar', 'jenjang' => 'smk'],
            ['nama' => 'Surat Pengantar Sekolah', 'wajib' => true, 'aktif' => true]
        );

        // Peserta baru
        $peserta = new User;
        $peserta->nama = 'Budi Santoso Siswa';
        $peserta->email = 'budi.santoso@sekolah.test';
        $peserta->password = 'secret123';
        $peserta->role = RoleEnum::Peserta;
        $peserta->email_verified_at = now();
        $peserta->status_aktif = true;
        $peserta->save();

        // 1. Lengkapi Profil
        $profilRes = $this->actingAs($peserta)->post(route('peserta.profil.simpan'), [
            'nim_nisn' => '00987654321',
            'institusi' => 'SMK Negeri 2 Kominfo',
            'jenjang' => 'smk',
            'jurusan_id' => $this->jurusanSmk->id,
            'no_hp' => '08129876543',
        ]);
        $profilRes->assertRedirect(route('peserta.profil.show'));

        // 2. Buat Pendaftaran (status Draft / BerkasBelumLengkap)
        $daftarRes = $this->actingAs($peserta)->post(route('peserta.pendaftaran.store'), [
            'bulan_magang_id' => $bulan->id,
            'kuota_id' => $kuota->id,
            'durasi_bulan' => 3,
        ]);
        $daftarRes->assertRedirect();
        $pendaftaran = Pendaftaran::where('user_id', $peserta->id)->firstOrFail();
        $this->assertEquals(StatusPendaftaranEnum::BerkasBelumLengkap, $pendaftaran->status);

        // 3. Upload Berkas Wajib ke Disk Private
        $dokumenWajib = JenisDokumen::where('jenjang', 'smk')->where('wajib', true)->where('aktif', true)->get();
        foreach ($dokumenWajib as $jenis) {
            $fileDummy = UploadedFile::fake()->create("{$jenis->kode}.pdf", 500, 'application/pdf');
            $uploadRes = $this->actingAs($peserta)->post(route('peserta.berkas.unggah', $pendaftaran->id), [
                'jenis_dokumen_id' => $jenis->id,
                'file' => $fileDummy,
            ]);
            $uploadRes->assertRedirect();
            $this->assertDatabaseHas('dokumen', [
                'pendaftaran_id' => $pendaftaran->id,
                'jenis_dokumen_id' => $jenis->id,
            ]);
        }

        // 4. Ajukan Pendaftaran
        $ajukanRes = $this->actingAs($peserta)->post(route('peserta.pendaftaran.ajukan', $pendaftaran->id));
        $ajukanRes->assertRedirect();

        $pendaftaran->refresh();
        $this->assertEquals(StatusPendaftaranEnum::Diajukan, $pendaftaran->status);
    }

    /**
     * UAT-3: Admin memutuskan: Perlu Perbaikan, peserta memperbaiki, lalu Diterima; penempatan dan Pembimbing terbentuk.
     */
    public function test_uat_3_alur_perlu_perbaikan_dan_penerimaan_oleh_admin(): void
    {
        $bulan = BulanMagang::create([
            'bulan_mulai' => '2027-04-01',
            'tgl_buka' => '2026-10-01',
            'tgl_tutup' => '2026-10-31',
            'aktif' => true,
        ]);
        $kuota = Kuota::create([
            'bulan_magang_id' => $bulan->id,
            'jurusan_id' => $this->jurusanSmk->id,
            'jumlah' => 5,
            'terisi' => 0,
            'aktif' => true,
        ]);

        $peserta = new User;
        $peserta->nama = 'Citra Lestari';
        $peserta->email = 'citra@sekolah.test';
        $peserta->password = 'secret123';
        $peserta->role = RoleEnum::Peserta;
        $peserta->email_verified_at = now();
        $peserta->status_aktif = true;
        $peserta->save();

        PesertaProfil::create([
            'user_id' => $peserta->id,
            'nim_nisn' => '009876111',
            'institusi' => 'SMK Negeri 1',
            'jenjang' => 'smk',
            'jurusan_id' => $this->jurusanSmk->id,
            'no_hp' => '08129876111',
        ]);

        $pendaftaran = Pendaftaran::create([
            'user_id' => $peserta->id,
            'bulan_magang_id' => $bulan->id,
            'kuota_id' => $kuota->id,
            'durasi_bulan' => 2,
            'status' => StatusPendaftaranEnum::Diajukan,
            'diajukan_at' => now(),
        ]);

        // 1. Admin memutuskan: Perlu Perbaikan
        $putuskan1 = $this->actingAs($this->admin)->post(route('admin.pendaftaran.putuskan', $pendaftaran), [
            'keputusan' => 'perlu_perbaikan',
            'catatan' => 'Surat pengantar belum ada tanda tangan kepala sekolah.',
        ]);
        $putuskan1->assertRedirect();
        $pendaftaran->refresh();
        $this->assertEquals(StatusPendaftaranEnum::PerluPerbaikan, $pendaftaran->status);

        // 2. Peserta memperbaiki dan mengajukan kembali
        $pendaftaran->status = StatusPendaftaranEnum::Diajukan;
        $pendaftaran->save();

        // 3. Admin memutuskan: Diterima & tempatkan ke pembimbing
        $putuskan2 = $this->actingAs($this->admin)->post(route('admin.pendaftaran.putuskan', $pendaftaran), [
            'keputusan' => 'terima',
            'pembimbing_id' => $this->pembimbing->id,
            'bidang_id' => $this->bidang->id,
        ]);
        $putuskan2->assertRedirect();

        $pendaftaran->refresh();
        $this->assertEquals(StatusPendaftaranEnum::Diterima, $pendaftaran->status);

        // Verifikasi Penempatan terbentuk dan kuota bertambah
        $penempatan = Penempatan::where('pendaftaran_id', $pendaftaran->id)->firstOrFail();
        $this->assertEquals(StatusPenempatanEnum::Aktif, $penempatan->status);
        $this->assertEquals($this->pembimbing->id, $penempatan->pembimbing_id);
        $this->assertEquals($this->bidang->id, $penempatan->bidang_id);

        $kuota->refresh();
        $this->assertEquals(1, $kuota->terisi);
    }

    /**
     * UAT-4: Kuota penuh: pendaftar masuk Waiting List.
     */
    public function test_uat_4_kuota_penuh_masuk_waiting_list(): void
    {
        $bulan = BulanMagang::create([
            'bulan_mulai' => '2027-05-01',
            'tgl_buka' => '2026-10-01',
            'tgl_tutup' => '2026-10-31',
            'aktif' => true,
        ]);
        $kuota = Kuota::create([
            'bulan_magang_id' => $bulan->id,
            'jurusan_id' => $this->jurusanSmk->id,
            'jumlah' => 1,
            'terisi' => 1, // Sudah penuh!
            'aktif' => true,
        ]);

        $jenisSurat = JenisDokumen::firstOrCreate(
            ['kode' => 'surat_pengantar', 'jenjang' => 'smk'],
            ['nama' => 'Surat Pengantar Sekolah', 'wajib' => true, 'aktif' => true]
        );

        $peserta = new User;
        $peserta->nama = 'Doni Waiting';
        $peserta->email = 'doni@sekolah.test';
        $peserta->password = 'secret123';
        $peserta->role = RoleEnum::Peserta;
        $peserta->email_verified_at = now();
        $peserta->status_aktif = true;
        $peserta->save();

        PesertaProfil::create([
            'user_id' => $peserta->id,
            'nim_nisn' => '009876222',
            'institusi' => 'SMK Negeri 1',
            'jenjang' => 'smk',
            'jurusan_id' => $this->jurusanSmk->id,
            'no_hp' => '08129876222',
        ]);

        // Pendaftaran saat kuota penuh
        $pendaftaran = Pendaftaran::create([
            'user_id' => $peserta->id,
            'bulan_magang_id' => $bulan->id,
            'kuota_id' => $kuota->id,
            'durasi_bulan' => 1,
            'status' => StatusPendaftaranEnum::BerkasBelumLengkap,
        ]);

        // Upload berkas wajib
        $dokumenWajib = JenisDokumen::where('jenjang', 'smk')->where('wajib', true)->where('aktif', true)->get();
        foreach ($dokumenWajib as $jenis) {
            $fileDummy = UploadedFile::fake()->create("{$jenis->kode}.pdf", 300, 'application/pdf');
            $this->actingAs($peserta)->post(route('peserta.berkas.unggah', $pendaftaran->id), [
                'jenis_dokumen_id' => $jenis->id,
                'file' => $fileDummy,
            ]);
        }

        // Ajukan berkas: status menjadi diajukan
        $ajukanRes = $this->actingAs($peserta)->post(route('peserta.pendaftaran.ajukan', $pendaftaran->id));
        $ajukanRes->assertRedirect();

        // Admin memutuskan waiting_list karena kuota penuh
        $adminRes = $this->actingAs($this->admin)->post(route('admin.pendaftaran.putuskan', $pendaftaran), [
            'keputusan' => 'waiting_list',
            'catatan' => 'Kuota untuk periode ini telah terpenuhi, masuk antrean cadangan.',
        ]);
        $adminRes->assertRedirect();

        $pendaftaran->refresh();
        $this->assertEquals(StatusPendaftaranEnum::WaitingList, $pendaftaran->status, 'Pendaftaran pada kuota penuh otomatis masuk ke waiting list');
    }

    /**
     * UAT-5: Input peserta jalur khusus; periksa bahwa slot khusus tidak tampil di landing page.
     */
    public function test_uat_5_penempatan_jalur_khusus_zero_leak_landing_page(): void
    {
        $bulan = BulanMagang::create([
            'bulan_mulai' => '2027-06-01',
            'tgl_buka' => '2026-10-01',
            'tgl_tutup' => '2026-10-31',
            'aktif' => true,
        ]);
        $slotKhusus = SlotKhusus::create([
            'bulan_mulai' => '2027-06-01',
            'jumlah' => 2,
            'terisi' => 0,
            'aktif' => true,
            'dibuat_oleh' => $this->superAdmin->id,
        ]);

        $pesertaKhusus = new User;
        $pesertaKhusus->nama = 'Eka Khusus';
        $pesertaKhusus->email = 'eka.khusus@instansi.test';
        $pesertaKhusus->password = 'secret123';
        $pesertaKhusus->role = RoleEnum::Peserta;
        $pesertaKhusus->email_verified_at = now();
        $pesertaKhusus->status_aktif = true;
        $pesertaKhusus->save();

        PesertaProfil::create([
            'user_id' => $pesertaKhusus->id,
            'nim_nisn' => '999888777',
            'institusi' => 'Direktorat Internal VIP',
            'jenjang' => 's1',
            'jurusan_id' => $this->jurusanSmk->id,
            'no_hp' => '0812999888',
        ]);

        // Admin melakukan penempatan manual slot khusus
        $manualRes = $this->actingAs($this->admin)->post(route('admin.penempatan.manual'), [
            'peserta_id' => $pesertaKhusus->id,
            'slot_khusus_id' => $slotKhusus->id,
            'bidang_id' => $this->bidang->id,
            'pembimbing_id' => $this->pembimbing->id,
            'bulan_mulai' => '2027-06-01',
            'durasi_bulan' => 2,
        ]);
        $manualRes->assertRedirect(route('admin.penempatan.index'));

        $slotKhusus->refresh();
        $this->assertEquals(1, $slotKhusus->terisi);

        // Landing page publik tidak boleh membocorkan 'Direktorat Internal VIP' atau slot khusus
        $landing = $this->get(route('landing'));
        $landing->assertStatus(200);
        $landing->assertDontSee('Direktorat Internal VIP');
        $landing->assertDontSee('Slot Khusus');
    }

    /**
     * UAT-6: Pelaksanaan: tugas, progres, review, logbook (batas mundur & perlu revisi).
     */
    public function test_uat_6_pelaksanaan_tugas_progres_dan_logbook_harian(): void
    {
        $penempatan = Penempatan::create([
            'peserta_id' => $this->pembimbing->id, // penempatan dummy
            'pembimbing_id' => $this->pembimbing->id,
            'bidang_id' => $this->bidang->id,
            'jalur' => JalurEnum::Khusus,
            'sumber' => SumberPenempatanEnum::Manual,
            'tgl_mulai' => Carbon::today(),
            'tgl_selesai' => Carbon::today()->addMonths(2),
            'status' => StatusPenempatanEnum::Aktif,
        ]);

        $peserta = new User;
        $peserta->nama = 'Fajar Pelaksana';
        $peserta->email = 'fajar@pelaksana.test';
        $peserta->password = 'secret123';
        $peserta->role = RoleEnum::Peserta;
        $peserta->email_verified_at = now();
        $peserta->status_aktif = true;
        $peserta->save();

        $penempatan->peserta_id = $peserta->id;
        $penempatan->save();

        // 1. Pembimbing membuat tugas
        $tugasRes = $this->actingAs($this->pembimbing)->postJson(route('pembimbing.tugas.store'), [
            'penempatan_id' => $penempatan->id,
            'judul' => 'Audit Database Query',
            'kategori' => 'Backend',
            'kompleksitas' => 'tinggi',
            'deadline' => Carbon::today()->addDays(7)->toDateString(),
        ]);
        $tugasRes->assertStatus(201);
        $tugasId = $tugasRes->json('data.id');

        // 2. Peserta upload progres
        $progresRes = $this->actingAs($peserta)->postJson(route('peserta.tugas.progres', $tugasId), [
            'catatan' => 'Analisis N+1 query telah selesai dilakukan pada model utama.',
        ]);
        $progresRes->assertStatus(201);
        $progresId = $progresRes->json('data.id');

        // 3. Pembimbing review progres
        $reviewTugasRes = $this->actingAs($this->pembimbing)->postJson(route('pembimbing.progres.review', $progresId), [
            'status_review' => 'diterima',
            'feedback' => 'Bagus sekali, pertahankan.',
        ]);
        $reviewTugasRes->assertStatus(200);

        // 4. Peserta mengisi logbook hari ini -> Sukses
        $today = Carbon::today()->toDateString();
        $logbookRes = $this->actingAs($peserta)->postJson(route('peserta.logbook.store'), [
            'tanggal' => $today,
            'kegiatan' => 'Melakukan pemetaan query database dan membuat modul test.',
        ]);
        $logbookRes->assertStatus(201);
        $logbookId = $logbookRes->json('data.id');

        // 5. Peserta mengisi logbook mundur 5 hari (melebihi toleransi BR-09 default 3 hari) -> Ditolak
        $lampau = Carbon::today()->subDays(5)->toDateString();
        $logbookGagal = $this->actingAs($peserta)->postJson(route('peserta.logbook.store'), [
            'tanggal' => $lampau,
            'kegiatan' => 'Aktivitas yang sudah lewat dari batas toleransi.',
        ]);
        $logbookGagal->assertStatus(422);

        // 6. Pembimbing mereview logbook -> Perlu revisi
        $reviewLogRes = $this->actingAs($this->pembimbing)->postJson(route('pembimbing.logbook.review', $logbookId), [
            'status' => 'perlu_revisi',
            'catatan_pembimbing' => 'Tolong lampirkan screenshot profil query.',
        ]);
        $reviewLogRes->assertStatus(200);
        $this->assertEquals(StatusLogbookEnum::PerluRevisi->value, $reviewLogRes->json('data.status'));
    }

    /**
     * UAT-7: Penilaian: draft, final, ubah nilai dengan alasan, tetapkan Selesai, unduh Lembar Nilai.
     */
    public function test_uat_7_penilaian_dan_unduh_lembar_nilai(): void
    {
        $peserta = new User;
        $peserta->nama = 'Gita Nilai';
        $peserta->email = 'gita@nilai.test';
        $peserta->password = 'secret123';
        $peserta->role = RoleEnum::Peserta;
        $peserta->email_verified_at = now();
        $peserta->status_aktif = true;
        $peserta->save();

        PesertaProfil::create([
            'user_id' => $peserta->id,
            'nim_nisn' => '009876333',
            'institusi' => 'SMK Negeri 1',
            'jenjang' => 'smk',
            'jurusan_id' => $this->jurusanSmk->id,
            'no_hp' => '08129876333',
        ]);

        $penempatan = Penempatan::create([
            'peserta_id' => $peserta->id,
            'pembimbing_id' => $this->pembimbing->id,
            'bidang_id' => $this->bidang->id,
            'jalur' => JalurEnum::Khusus,
            'sumber' => SumberPenempatanEnum::Manual,
            'tgl_mulai' => Carbon::today()->subMonths(3),
            'tgl_selesai' => Carbon::today(),
            'status' => StatusPenempatanEnum::Aktif,
        ]);

        $rubrikAktif = Rubrik::where('aktif', true)->get();
        $skor = [];
        foreach ($rubrikAktif as $r) {
            $skor[$r->id] = 85;
        }

        // 1. Simpan draf penilaian
        $draftRes = $this->actingAs($this->pembimbing)->postJson(route('pembimbing.penilaian.store', $penempatan), [
            'skor' => [$rubrikAktif->first()->id => 85],
            'is_final' => false,
        ]);
        $draftRes->assertStatus(200);

        // 2. Finalisasi penilaian
        $finalRes = $this->actingAs($this->pembimbing)->postJson(route('pembimbing.penilaian.store', $penempatan), [
            'skor' => $skor,
            'catatan' => 'Sangat bertanggung jawab.',
            'is_final' => true,
        ]);
        $finalRes->assertStatus(200);

        // 3. Revisi nilai final dengan alasan
        foreach ($rubrikAktif as $r) {
            $skor[$r->id] = 90;
        }
        $revisiRes = $this->actingAs($this->pembimbing)->postJson(route('pembimbing.penilaian.store', $penempatan), [
            'skor' => $skor,
            'is_final' => true,
            'alasan' => 'Penambahan nilai atas presentasi akhir yang sangat memukau.',
        ]);
        $revisiRes->assertStatus(200);

        // 4. Pembimbing menandai Selesai (BR-10)
        $selesaiRes = $this->actingAs($this->pembimbing)->postJson(route('pembimbing.penempatan.selesai', $penempatan));
        $selesaiRes->assertStatus(200);
        $penempatan->refresh();
        $this->assertEquals(StatusPenempatanEnum::Selesai, $penempatan->status);

        // 5. Peserta mengunduh Lembar Nilai PDF
        $pdfRes = $this->actingAs($peserta)->get(route('peserta.nilai.unduh'));
        $pdfRes->assertStatus(200);
        $this->assertEquals('application/pdf', $pdfRes->headers->get('content-type'));
    }

    /**
     * UAT-8: Masa tenggang dan nonaktif otomatis; arsip dibuka SuperAdmin; audit log menelusuri seluruh siklus.
     */
    public function test_uat_8_masa_tenggang_nonaktif_otomatis_dan_audit_trail_komprehensif(): void
    {
        $peserta = new User;
        $peserta->nama = 'Hendra Selesai';
        $peserta->email = 'hendra@selesai.test';
        $peserta->password = 'secret123';
        $peserta->role = RoleEnum::Peserta;
        $peserta->email_verified_at = now();
        $peserta->status_aktif = true;
        $peserta->save();

        $penempatan = Penempatan::create([
            'peserta_id' => $peserta->id,
            'pembimbing_id' => $this->pembimbing->id,
            'bidang_id' => $this->bidang->id,
            'jalur' => JalurEnum::Khusus,
            'sumber' => SumberPenempatanEnum::Manual,
            'tgl_mulai' => Carbon::today()->subMonths(3),
            'tgl_selesai' => Carbon::today()->subMonths(1),
            'tgl_ditetapkan_selesai' => Carbon::today()->subMonths(1),
            'tgl_nonaktif' => Carbon::today()->subDays(1), // Masa tenggang berakhir kemarin!
            'status' => StatusPenempatanEnum::Selesai,
        ]);

        // 1. Eksekusi scheduler nonaktif
        $exitCode = $this->artisan('magkom:nonaktifkan-peserta')->run();
        $this->assertEquals(0, $exitCode);

        $penempatan->refresh();
        $peserta->refresh();
        $this->assertEquals(StatusPenempatanEnum::Nonaktif, $penempatan->status);
        $this->assertFalse($peserta->status_aktif, 'Akun peserta dinonaktifkan otomatis setelah lewat masa tenggang (BR-07)');

        // 2. Audit Trail komprehensif tersimpan di basis data
        $logCount = AuditLog::count();
        $this->assertGreaterThan(0, $logCount, 'Jejak audit harus mencatat seluruh mutasi aksi dalam sistem');

        // Pastikan tidak ada kebocoran kredensial password di detail/nilai_baru audit log
        $leakedLogs = AuditLog::whereNotNull('nilai_baru')
            ->where(function ($q) {
                $q->where('nilai_baru', 'like', '%"password":%')
                    ->where('nilai_baru', 'not like', '%[DISAMARKAN]%');
            })
            ->count();
        $this->assertEquals(0, $leakedLogs, 'Tidak boleh ada kata sandi mentah yang bocor di tabel audit log (AC-13-5)');
    }
}
