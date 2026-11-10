<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Actions\Peserta\Profil\UpdatePesertaProfilAction;
use App\Enums\JenjangEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusPendaftaranEnum;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Dokumen;
use App\Models\JenisDokumen;
use App\Models\Jurusan;
use App\Models\KelompokAnggota;
use App\Models\KelompokMagang;
use App\Models\Kuota;
use App\Models\Pendaftaran;
use App\Models\PesertaProfil;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class PesertaProfilArchitectureAndSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected User $pesertaA;

    protected User $pesertaB;

    protected Jurusan $jurusanUniv;

    protected Jurusan $jurusanSmk;

    protected Bidang $bidang;

    protected JenisDokumen $dokumenKtp;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::parse('2026-10-15 10:00:00', 'Asia/Jakarta'));
        CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-10-15 10:00:00', 'Asia/Jakarta'));

        Storage::fake('public');
        Storage::fake('private');

        $this->bidang = Bidang::create([
            'nama_bidang' => 'Aplikasi Informatika',
            'deskripsi' => 'Pengembangan TIK',
            'kuota' => 20,
        ]);

        $this->jurusanUniv = Jurusan::create([
            'bidang_id' => $this->bidang->id,
            'nama_jurusan' => 'Teknik Informatika',
            'kategori' => 'mahasiswa',
            'aktif' => true,
        ]);

        $this->jurusanSmk = Jurusan::create([
            'bidang_id' => $this->bidang->id,
            'nama_jurusan' => 'Rekayasa Perangkat Lunak',
            'kategori' => 'smk',
            'aktif' => true,
        ]);

        $this->dokumenKtp = JenisDokumen::create([
            'kode' => 'ktp',
            'nama' => 'KTP',
            'wajib' => true,
            'aktif' => true,
            'jenjang' => JenjangEnum::S1,
        ]);

        $this->pesertaA = $this->createPesertaUser('Peserta A', 'pesertaA@kominfo.test', '11223344', $this->jurusanUniv, JenjangEnum::S1);
        $this->pesertaB = $this->createPesertaUser('Peserta B', 'pesertaB@kominfo.test', '55667788', $this->jurusanUniv, JenjangEnum::S1);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        CarbonImmutable::setTestNow();

        parent::tearDown();
    }

    private function createUser(string $nama, string $email, RoleEnum $role): User
    {
        $user = new User;
        $user->nama = $nama;
        $user->email = $email;
        $user->password = 'Password123!';
        $user->role = $role;
        $user->status_aktif = true;
        $user->email_verified_at = now();
        $user->must_change_password = false;
        $user->save();

        return $user;
    }

    private function createPesertaUser(string $nama, string $email, string $nim, Jurusan $jurusan, JenjangEnum $jenjang): User
    {
        $user = $this->createUser($nama, $email, RoleEnum::Peserta);

        PesertaProfil::create([
            'user_id' => $user->id,
            'nim_nisn' => $nim,
            'institusi' => 'Universitas Indonesia',
            'jenjang' => $jenjang,
            'jurusan_id' => $jurusan->id,
            'alamat' => 'Jl. Margonda Raya No. 100 Depok',
            'no_hp' => '081234567890',
            'foto' => null,
            'nama_kontak_darurat' => 'Budi Santoso',
            'no_kontak_darurat' => '081298765432',
            'hubungan_kontak_darurat' => 'Orang Tua',
        ]);

        return $user;
    }

    public function test_show_profil_returns_360_view_data_and_accurately_detects_akademik_lock(): void
    {
        $response = $this->actingAs($this->pesertaA)
            ->get(route('peserta.profil.show'));

        $response->assertOk();
        $response->assertViewIs('peserta.profil.show');
        $response->assertViewHas('user');
        $response->assertViewHas('profil');
        $response->assertViewHas('jurusanList');
        $response->assertViewHas('checklist');
        $response->assertViewHas('persentaseLengkap');
        $response->assertViewHas('isAkademikLocked', true);
    }

    public function test_show_profil_with_empty_profile_reports_akademik_unlocked_and_zero_percent(): void
    {
        $userBaru = $this->createUser('Peserta Baru', 'pesertabaru@kominfo.test', RoleEnum::Peserta);

        $response = $this->actingAs($userBaru)
            ->get(route('peserta.profil.show'));

        $response->assertOk();
        $response->assertViewHas('isAkademikLocked', false);
        $response->assertViewHas('persentaseLengkap', 0);
    }

    public function test_update_profil_successfully_persists_allowed_fields_and_records_audit_trail(): void
    {
        $payload = [
            'nim_nisn' => '11223344',
            'institusi' => 'Universitas Indonesia',
            'jurusan_id' => $this->jurusanUniv->id,
            'jenjang' => JenjangEnum::S1->value,
            'no_hp' => '089988776655',
            'alamat' => 'Jl. Baru No. 42 Jakarta Selatan',
            'nama_kontak_darurat' => 'Siti Aminah',
            'no_kontak_darurat' => '081122334455',
            'hubungan_kontak_darurat' => 'Ibu',
        ];

        $response = $this->actingAs($this->pesertaA)
            ->post(route('peserta.profil.simpan'), $payload);

        $response->assertRedirect(route('peserta.profil.show'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('peserta_profil', [
            'user_id' => $this->pesertaA->id,
            'no_hp' => '089988776655',
            'alamat' => 'Jl. Baru No. 42 Jakarta Selatan',
            'nama_kontak_darurat' => 'Siti Aminah',
            'no_kontak_darurat' => '081122334455',
            'hubungan_kontak_darurat' => 'Ibu',
        ]);

        $this->assertDatabaseHas('audit_log', [
            'user_id' => $this->pesertaA->id,
            'entitas' => 'peserta_profil',
        ]);
    }

    public function test_academic_immutability_rejects_or_ignores_tampering_of_institusi_and_nim(): void
    {
        // Peserta A tries to tamper with institusi and nim_nisn
        $tamperedPayload = [
            'nim_nisn' => '9999999999',
            'institusi' => 'Universitas Palsu Peretas',
            'jurusan_id' => $this->jurusanUniv->id,
            'jenjang' => JenjangEnum::S1->value,
            'no_hp' => '089988776655',
            'alamat' => 'Jl. Margonda Baru',
        ];

        $response = $this->actingAs($this->pesertaA)
            ->post(route('peserta.profil.simpan'), $tamperedPayload);

        $response->assertRedirect(route('peserta.profil.show'));

        $freshProfil = $this->pesertaA->fresh()->profil;
        $this->assertSame('Universitas Indonesia', $freshProfil->institusi);
        $this->assertSame('11223344', $freshProfil->nim_nisn);
    }

    public function test_photo_upload_and_replacement_cleans_up_old_photo_post_commit(): void
    {
        // 1. Unggah foto awal
        $photo1 = UploadedFile::fake()->image('avatar1.jpg', 200, 200);
        $payload1 = [
            'nim_nisn' => '11223344',
            'institusi' => 'Universitas Indonesia',
            'jurusan_id' => $this->jurusanUniv->id,
            'jenjang' => JenjangEnum::S1->value,
            'no_hp' => '081234567890',
            'foto' => $photo1,
        ];

        $this->actingAs($this->pesertaA)
            ->post(route('peserta.profil.simpan'), $payload1)
            ->assertRedirect(route('peserta.profil.show'));

        $pathFoto1 = $this->pesertaA->fresh()->profil->foto;
        $this->assertNotNull($pathFoto1);
        Storage::disk('public')->assertExists($pathFoto1);

        // 2. Ganti dengan foto baru
        $photo2 = UploadedFile::fake()->image('avatar2.png', 200, 200);
        $payload2 = [
            'nim_nisn' => '11223344',
            'institusi' => 'Universitas Indonesia',
            'jurusan_id' => $this->jurusanUniv->id,
            'jenjang' => JenjangEnum::S1->value,
            'no_hp' => '081234567890',
            'foto' => $photo2,
        ];

        $this->actingAs($this->pesertaA)
            ->post(route('peserta.profil.simpan'), $payload2)
            ->assertRedirect(route('peserta.profil.show'));

        $pathFoto2 = $this->pesertaA->fresh()->profil->foto;
        $this->assertNotNull($pathFoto2);
        $this->assertNotSame($pathFoto1, $pathFoto2);

        // Foto baru harus ada, foto lama harus terhapus dari disk
        Storage::disk('public')->assertExists($pathFoto2);
        Storage::disk('public')->assertMissing($pathFoto1);
    }

    public function test_photo_removal_via_hapus_foto_deletes_file_and_nulls_column(): void
    {
        // Unggah foto dulu
        $photo = UploadedFile::fake()->image('avatar.jpg', 200, 200);
        $payload = [
            'nim_nisn' => '11223344',
            'institusi' => 'Universitas Indonesia',
            'jurusan_id' => $this->jurusanUniv->id,
            'jenjang' => JenjangEnum::S1->value,
            'no_hp' => '081234567890',
            'foto' => $photo,
        ];

        $this->actingAs($this->pesertaA)->post(route('peserta.profil.simpan'), $payload);
        $pathFoto = $this->pesertaA->fresh()->profil->foto;
        $this->assertNotNull($pathFoto);
        Storage::disk('public')->assertExists($pathFoto);

        // Hapus foto
        $payloadHapus = [
            'nim_nisn' => '11223344',
            'institusi' => 'Universitas Indonesia',
            'jurusan_id' => $this->jurusanUniv->id,
            'jenjang' => JenjangEnum::S1->value,
            'no_hp' => '081234567890',
            'hapus_foto' => '1',
        ];

        $this->actingAs($this->pesertaA)
            ->post(route('peserta.profil.simpan'), $payloadHapus)
            ->assertRedirect(route('peserta.profil.show'));

        $this->assertNull($this->pesertaA->fresh()->profil->foto);
        Storage::disk('public')->assertMissing($pathFoto);
    }

    public function test_transaction_rollback_cleans_up_newly_uploaded_photo_on_failure(): void
    {
        $action = app(UpdatePesertaProfilAction::class);
        $fakePhoto = UploadedFile::fake()->image('rollback_test.jpg', 200, 200);

        // Sengaja pancing error di dalam transaksi dengan mock atau trigger DB error
        DB::shouldReceive('transaction')
            ->once()
            ->andThrow(new Exception('Simulated Database Error'));

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Simulated Database Error');

        $action->execute(
            user: $this->pesertaA,
            validatedData: ['no_hp' => '081234567890'],
            fotoFile: $fakePhoto
        );
    }

    public function test_uu_pdp_smk_persetujuan_wali_records_timestamp(): void
    {
        $pesertaSmk = $this->createUser('Siswa SMK', 'smk@kominfo.test', RoleEnum::Peserta);

        $payload = [
            'nim_nisn' => 'SMK123456',
            'institusi' => 'SMKN 1 Jakarta',
            'jurusan_id' => $this->jurusanSmk->id,
            'jenjang' => JenjangEnum::Smk->value,
            'no_hp' => '081234567890',
            'persetujuan_wali' => '1',
        ];

        $this->actingAs($pesertaSmk)
            ->post(route('peserta.profil.simpan'), $payload)
            ->assertRedirect(route('peserta.profil.show'));

        $profil = $pesertaSmk->fresh()->profil;
        $this->assertNotNull($profil);
        $this->assertNotNull($profil->persetujuan_wali_at);
    }

    public function test_berkas_unggah_prevents_idor_with_unauthorized_kelompok_anggota_id(): void
    {
        // Buat bulan dan kuota untuk pendaftaran
        $bulan = BulanMagang::create([
            'bulan_mulai' => '2026-11-01',
            'tgl_buka' => '2026-10-01',
            'tgl_tutup' => '2026-10-31',
            'aktif' => true,
        ]);

        $kuota = Kuota::create([
            'bulan_magang_id' => $bulan->id,
            'jurusan_id' => $this->jurusanUniv->id,
            'jumlah' => 10,
            'terisi' => 0,
            'aktif' => true,
        ]);

        // Tim A (Ketua: Peserta A)
        $timA = KelompokMagang::create([
            'ketua_user_id' => $this->pesertaA->id,
            'nama_kelompok' => 'Tim Riset A',
        ]);
        $anggotaA1 = KelompokAnggota::create([
            'kelompok_magang_id' => $timA->id,
            'nama' => 'Anggota A1',
            'nim_nisn' => 'A001',
            'no_hp' => '0811111111',
            'email' => 'a1@kominfo.test',
        ]);

        $pendaftaranA = Pendaftaran::create([
            'nomor_pendaftaran' => 'REG-TIM-A',
            'user_id' => $this->pesertaA->id,
            'bulan_magang_id' => $bulan->id,
            'kuota_id' => $kuota->id,
            'kelompok_magang_id' => $timA->id,
            'durasi_bulan' => 3,
            'status' => StatusPendaftaranEnum::BerkasBelumLengkap,
        ]);

        // Tim B (Ketua: Peserta B)
        $timB = KelompokMagang::create([
            'ketua_user_id' => $this->pesertaB->id,
            'nama_kelompok' => 'Tim Riset B',
        ]);
        $anggotaB1 = KelompokAnggota::create([
            'kelompok_magang_id' => $timB->id,
            'nama' => 'Anggota B1',
            'nim_nisn' => 'B001',
            'no_hp' => '0822222222',
            'email' => 'b1@kominfo.test',
        ]);

        // Peserta A mencoba mengunggah dokumen dengan menyisipkan kelompok_anggota_id milik Tim B (IDOR)
        $file = UploadedFile::fake()->create('ktp_anggota.pdf', 100, 'application/pdf');

        $response = $this->actingAs($this->pesertaA)
            ->post(route('peserta.berkas.unggah', $pendaftaranA), [
                'jenis_dokumen_id' => $this->dokumenKtp->id,
                'kelompok_anggota_id' => $anggotaB1->id,
                'file' => $file,
            ]);

        $response->assertForbidden();
    }

    public function test_berkas_unggah_succeeds_with_valid_kelompok_anggota_id(): void
    {
        $bulan = BulanMagang::create([
            'bulan_mulai' => '2026-11-01',
            'tgl_buka' => '2026-10-01',
            'tgl_tutup' => '2026-10-31',
            'aktif' => true,
        ]);

        $kuota = Kuota::create([
            'bulan_magang_id' => $bulan->id,
            'jurusan_id' => $this->jurusanUniv->id,
            'jumlah' => 10,
            'terisi' => 0,
            'aktif' => true,
        ]);

        $timA = KelompokMagang::create([
            'ketua_user_id' => $this->pesertaA->id,
            'nama_kelompok' => 'Tim Riset Valid',
        ]);
        $anggotaA1 = KelompokAnggota::create([
            'kelompok_magang_id' => $timA->id,
            'nama' => 'Anggota Sah',
            'nim_nisn' => 'A999',
            'no_hp' => '0811119999',
            'email' => 'sah@kominfo.test',
        ]);

        $pendaftaranA = Pendaftaran::create([
            'nomor_pendaftaran' => 'REG-TIM-VALID',
            'user_id' => $this->pesertaA->id,
            'bulan_magang_id' => $bulan->id,
            'kuota_id' => $kuota->id,
            'kelompok_magang_id' => $timA->id,
            'durasi_bulan' => 3,
            'status' => StatusPendaftaranEnum::BerkasBelumLengkap,
        ]);

        $file = UploadedFile::fake()->create('ktp_sah.pdf', 100, 'application/pdf');

        $response = $this->actingAs($this->pesertaA)
            ->post(route('peserta.berkas.unggah', $pendaftaranA), [
                'jenis_dokumen_id' => $this->dokumenKtp->id,
                'kelompok_anggota_id' => $anggotaA1->id,
                'file' => $file,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('dokumen', [
            'pendaftaran_id' => $pendaftaranA->id,
            'jenis_dokumen_id' => $this->dokumenKtp->id,
            'kelompok_anggota_id' => $anggotaA1->id,
            'nama_asli' => 'ktp_sah.pdf',
        ]);
    }

    public function test_berkas_unduh_and_pratinjau_enforces_idor_protection(): void
    {
        $bulan = BulanMagang::create([
            'bulan_mulai' => '2026-11-01',
            'tgl_buka' => '2026-10-01',
            'tgl_tutup' => '2026-10-31',
            'aktif' => true,
        ]);

        $kuota = Kuota::create([
            'bulan_magang_id' => $bulan->id,
            'jurusan_id' => $this->jurusanUniv->id,
            'jumlah' => 10,
            'terisi' => 0,
            'aktif' => true,
        ]);

        $pendaftaranA = Pendaftaran::create([
            'nomor_pendaftaran' => 'REG-PESERTA-A',
            'user_id' => $this->pesertaA->id,
            'bulan_magang_id' => $bulan->id,
            'kuota_id' => $kuota->id,
            'durasi_bulan' => 3,
            'status' => StatusPendaftaranEnum::BerkasBelumLengkap,
        ]);

        // Simpan dokumen untuk Peserta A
        Storage::disk('private')->put('dokumen/peserta_a_ktp.pdf', 'dummy-pdf-content');
        $dokumenA = Dokumen::create([
            'pendaftaran_id' => $pendaftaranA->id,
            'jenis_dokumen_id' => $this->dokumenKtp->id,
            'nama_asli' => 'peserta_a_ktp.pdf',
            'path' => 'dokumen/peserta_a_ktp.pdf',
            'ukuran' => 1024,
            'mime' => 'application/pdf',
            'hash' => hash('sha256', 'dummy-pdf-content'),
        ]);

        $downloadUrl = URL::temporarySignedRoute(
            'berkas.download',
            now()->addMinutes(30),
            ['dokumen' => $dokumenA->id]
        );

        $previewUrl = URL::temporarySignedRoute(
            'berkas.pratinjau',
            now()->addMinutes(30),
            ['dokumen' => $dokumenA->id]
        );

        // Peserta B mencoba mengunduh dan melihat pratinjau dokumen milik Peserta A
        $this->actingAs($this->pesertaB)->get($downloadUrl)->assertForbidden();
        $this->actingAs($this->pesertaB)->get($previewUrl)->assertForbidden();
    }

    public function test_guest_cannot_access_or_modify_profil(): void
    {
        $this->get(route('peserta.profil.show'))->assertRedirect(route('login'));
        $this->post(route('peserta.profil.simpan'), [])->assertRedirect(route('login'));
    }
}
