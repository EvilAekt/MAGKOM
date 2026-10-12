<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JenjangEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusLogbookEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\StatusPenilaianEnum;
use App\Enums\StatusTugasEnum;
use App\Models\BulanMagang;
use App\Models\JenisDokumen;
use App\Models\Jurusan;
use App\Models\Kuota;
use App\Models\Logbook;
use App\Models\Pendaftaran;
use App\Models\Penempatan;
use App\Models\Penilaian;
use App\Models\PesertaProfil;
use App\Models\Tugas;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PesertaPortalWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected User $peserta;
    protected Jurusan $jurusan;

    protected function setUp(): void
    {
        parent::setUp();

        if (Jurusan::count() === 0 || BulanMagang::count() === 0 || JenisDokumen::count() === 0) {
            $this->seed();
        }

        $this->jurusan = Jurusan::firstOrFail();

        $this->peserta = new User([
            'nama' => 'Budi Peserta Test',
            'email' => 'peserta.workflow@kominfo.test',
            'password' => bcrypt('Secret#2026'),
            'status_aktif' => true,
            'must_change_password' => false,
        ]);
        $this->peserta->role = RoleEnum::Peserta;
        $this->peserta->email_verified_at = now();
        $this->peserta->save();
    }

    public function test_halaman_profil_dapat_dibuka_dan_biodata_tersimpan_dengan_kontak_darurat(): void
    {
        // 1. Buka halaman profil
        $response = $this->actingAs($this->peserta)->get(route('peserta.profil.show'));
        $response->assertStatus(200);
        $response->assertSee('Biodata & Profil Peserta');

        // 2. Simpan profil lengkap
        $postData = [
            'institusi' => 'SMK Negeri 2 Depok Sleman',
            'nim_nisn' => '1234567890',
            'jenjang' => 'smk',
            'jurusan_id' => $this->jurusan->id,
            'no_hp' => '081234567890',
            'nama_kontak_darurat' => 'Siti Wali',
            'no_kontak_darurat' => '089876543210',
            'hubungan_kontak_darurat' => 'Ibu Kandung',
        ];

        $postResponse = $this->actingAs($this->peserta)->post(route('peserta.profil.simpan'), $postData);
        $postResponse->assertRedirect(route('peserta.profil.show'));
        $postResponse->assertSessionHas('success');

        $this->assertDatabaseHas('peserta_profil', [
            'user_id' => $this->peserta->id,
            'nim_nisn' => '1234567890',
            'institusi' => 'SMK Negeri 2 Depok Sleman',
            'nama_kontak_darurat' => 'Siti Wali',
            'no_kontak_darurat' => '089876543210',
        ]);
    }

    public function test_peserta_dapat_unggah_dan_hapus_foto_profil(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');

        // 1. Simpan profil beserta upload berkas foto (valid 1x1 PNG bytes tanpa ketergantungan GD extension)
        $pngContent = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');
        $file = \Illuminate\Http\UploadedFile::fake()->createWithContent('pasfoto_formal.png', $pngContent);

        $postData = [
            'institusi' => 'Universitas Gadjah Mada',
            'nim_nisn' => '22/123456/TK/54321',
            'jenjang' => 's1',
            'jurusan_id' => $this->jurusan->id,
            'no_hp' => '081298765432',
            'foto' => $file,
        ];

        $response = $this->actingAs($this->peserta)->post(route('peserta.profil.simpan'), $postData);
        $response->assertRedirect(route('peserta.profil.show'));
        $response->assertSessionHas('success');

        $profil = PesertaProfil::where('user_id', $this->peserta->id)->firstOrFail();
        $this->assertNotNull($profil->foto);
        $this->assertNotNull($profil->foto_url);
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($profil->foto);

        // 2. Buka halaman profil dan pastikan url foto dirender
        $this->peserta->refresh();
        $pageResponse = $this->actingAs($this->peserta)->get(route('peserta.profil.show'));
        $pageResponse->assertStatus(200);
        $pageResponse->assertSee($profil->foto);

        // 3. Hapus foto profil dengan flag hapus_foto
        $deleteData = [
            'institusi' => 'Universitas Gadjah Mada',
            'nim_nisn' => '22/123456/TK/54321',
            'jenjang' => 's1',
            'jurusan_id' => $this->jurusan->id,
            'no_hp' => '081298765432',
            'hapus_foto' => '1',
        ];

        $delResponse = $this->actingAs($this->peserta)->post(route('peserta.profil.simpan'), $deleteData);
        $delResponse->assertRedirect(route('peserta.profil.show'));

        $profilFresh = $profil->fresh();
        $this->assertNull($profilFresh->foto);
        $this->assertNull($profilFresh->foto_url);
    }

    public function test_halaman_pendaftaran_dapat_dibuka_dan_menampilkan_view(): void
    {
        $response = $this->actingAs($this->peserta)->get(route('peserta.pendaftaran.index'));
        $response->assertStatus(200);
        $response->assertSee('Riwayat Pendaftaran Magang');
    }

    public function test_halaman_detail_pendaftaran_dapat_dibuka(): void
    {
        $bulan = BulanMagang::firstOrFail();
        $kuota = Kuota::firstOrCreate(
            ['bulan_magang_id' => $bulan->id, 'jurusan_id' => $this->jurusan->id],
            ['jumlah' => 10, 'terisi' => 0, 'aktif' => true]
        );

        $pendaftaran = Pendaftaran::create([
            'user_id' => $this->peserta->id,
            'bulan_magang_id' => $bulan->id,
            'kuota_id' => $kuota->id,
            'durasi_bulan' => 3,
            'status' => \App\Enums\StatusPendaftaranEnum::BerkasBelumLengkap,
        ]);

        $response = $this->actingAs($this->peserta)->get(route('peserta.pendaftaran.detail', $pendaftaran));
        $response->assertStatus(200);
        $response->assertSee('Detail & Berkas Pendaftaran');
        $response->assertSee('Berkas Persyaratan Magang');
    }

    protected function ensurePenempatanAktif(): Penempatan
    {
        $pembimbing = User::where('role', RoleEnum::Pembimbing)->first();
        if (! $pembimbing) {
            $pembimbing = new User([
                'nama' => 'Pembimbing Test',
                'email' => 'pembimbing.workflow@kominfo.test',
                'password' => bcrypt('Secret#2026'),
                'status_aktif' => true,
                'must_change_password' => false,
            ]);
            $pembimbing->role = RoleEnum::Pembimbing;
            $pembimbing->email_verified_at = now();
            $pembimbing->save();
        }

        $bidang = \App\Models\Bidang::firstOrCreate([
            'nama_bidang' => 'Aptika',
        ], [
            'deskripsi' => 'Bidang Aptika',
            'aktif' => true,
        ]);

        return Penempatan::firstOrCreate([
            'peserta_id' => $this->peserta->id,
        ], [
            'pembimbing_id' => $pembimbing->id,
            'bidang_id' => $bidang->id,
            'jalur' => \App\Enums\JalurEnum::Umum,
            'sumber' => \App\Enums\SumberPenempatanEnum::Online,
            'tgl_mulai' => now()->subDay()->format('Y-m-d'),
            'tgl_selesai' => now()->addMonths(3)->format('Y-m-d'),
            'status' => StatusPenempatanEnum::Aktif,
        ]);
    }

    public function test_halaman_logbook_dapat_dibuka_dan_menampilkan_view_peserta(): void
    {
        $this->ensurePenempatanAktif();
        $response = $this->actingAs($this->peserta)->get(route('peserta.logbook.index'));
        $response->assertStatus(200);
        $response->assertSee('Logbook Mingguan');
    }

    public function test_halaman_tugas_dapat_dibuka(): void
    {
        $this->ensurePenempatanAktif();
        $response = $this->actingAs($this->peserta)->get(route('peserta.tugas.index'));
        $response->assertStatus(200);
        $response->assertSee('Tugas & Progres Magang');
    }

    public function test_halaman_nilai_dapat_dibuka_dengan_graceful_handling_saat_belum_final(): void
    {
        $this->ensurePenempatanAktif();
        $response = $this->actingAs($this->peserta)->get(route('peserta.nilai.show'));
        $response->assertStatus(200);
        $response->assertSee('Lembar Nilai Belum Tersedia');
    }

    public function test_topbar_menampilkan_nama_peserta_dan_dropdown_profil(): void
    {
        $response = $this->actingAs($this->peserta)->get(route('peserta.profil.show'));
        $response->assertStatus(200);
        $response->assertSee('Budi Peserta Test');
        $response->assertSee('userProfileBtn');
        $response->assertSee('userProfileMenu');
        $response->assertSee('Biodata &amp; Profil', false);
        $response->assertSee('Riwayat Pendaftaran');
        $response->assertSee('Ganti Kata Sandi');
        $response->assertSee('Keluar Akun');
    }
}
