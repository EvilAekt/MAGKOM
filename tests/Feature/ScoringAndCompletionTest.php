<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JalurEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\StatusPenilaianEnum;
use App\Enums\SumberPenempatanEnum;
use App\Models\Bidang;
use App\Models\Jurusan;
use App\Models\Penempatan;
use App\Models\Penilaian;
use App\Models\PenilaianRiwayat;
use App\Models\PesertaProfil;
use App\Models\Rubrik;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScoringAndCompletionTest extends TestCase
{
    use RefreshDatabase;

    protected User $pembimbing;

    protected User $peserta;

    protected User $superAdmin;

    protected Penempatan $penempatan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pembimbing = new User;
        $this->pembimbing->nama = 'Pembimbing Lapangan';
        $this->pembimbing->email = 'pembimbing.lapangan@kominfo.test';
        $this->pembimbing->password = 'secret123';
        $this->pembimbing->role = RoleEnum::Pembimbing;
        $this->pembimbing->email_verified_at = now();
        $this->pembimbing->status_aktif = true;
        $this->pembimbing->must_change_password = false;
        $this->pembimbing->save();

        $this->peserta = new User;
        $this->peserta->nama = 'Peserta Magang';
        $this->peserta->email = 'peserta.magang@kominfo.test';
        $this->peserta->password = 'secret123';
        $this->peserta->role = RoleEnum::Peserta;
        $this->peserta->email_verified_at = now();
        $this->peserta->status_aktif = true;
        $this->peserta->must_change_password = false;
        $this->peserta->save();

        $jurusan = Jurusan::firstOrCreate(
            ['nama_jurusan' => 'Rekayasa Perangkat Lunak'],
            ['kategori' => 'smk', 'aktif' => true]
        );

        PesertaProfil::create([
            'user_id' => $this->peserta->id,
            'nim_nisn' => '1234567890',
            'institusi' => 'SMK Negeri 1 Kominfo',
            'jenjang' => 'smk',
            'jurusan_id' => $jurusan->id,
            'no_hp' => '08123456789',
        ]);

        $this->superAdmin = new User;
        $this->superAdmin->nama = 'Super Administrator';
        $this->superAdmin->email = 'superadmin.audit@kominfo.test';
        $this->superAdmin->password = 'secret123';
        $this->superAdmin->role = RoleEnum::SuperAdmin;
        $this->superAdmin->email_verified_at = now();
        $this->superAdmin->status_aktif = true;
        $this->superAdmin->must_change_password = false;
        $this->superAdmin->save();

        $bidang = Bidang::firstOrCreate(
            ['nama_bidang' => 'Aplikasi Informatika'],
            ['aktif' => true]
        );

        if (Rubrik::count() === 0) {
            Rubrik::create(['kriteria' => 'Kualitas hasil kerja', 'bobot' => 30, 'aktif' => true]);
            Rubrik::create(['kriteria' => 'Ketepatan waktu dan penyelesaian tugas', 'bobot' => 20, 'aktif' => true]);
            Rubrik::create(['kriteria' => 'Inisiatif dan kemampuan belajar', 'bobot' => 20, 'aktif' => true]);
            Rubrik::create(['kriteria' => 'Komunikasi dan kerja sama', 'bobot' => 15, 'aktif' => true]);
            Rubrik::create(['kriteria' => 'Etika, disiplin, dan tanggung jawab', 'bobot' => 15, 'aktif' => true]);
        }

        $this->penempatan = Penempatan::create([
            'peserta_id' => $this->peserta->id,
            'pembimbing_id' => $this->pembimbing->id,
            'bidang_id' => $bidang->id,
            'jalur' => JalurEnum::Khusus,
            'sumber' => SumberPenempatanEnum::Manual,
            'tgl_mulai' => Carbon::today()->startOfMonth(),
            'tgl_selesai' => Carbon::today()->addMonths(3)->endOfMonth(),
            'status' => StatusPenempatanEnum::Aktif,
        ]);
    }

    public function test_pembimbing_bisa_simpan_draft_penilaian_dan_kalkulasi_nilai(): void
    {
        $rubrikPertama = Rubrik::where('aktif', true)->firstOrFail();

        $response = $this->actingAs($this->pembimbing)->postJson(route('pembimbing.penilaian.store', $this->penempatan), [
            'skor' => [
                $rubrikPertama->id => 90,
            ],
            'catatan' => 'Draf evaluasi tengah periode.',
            'is_final' => false,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('penilaian', [
            'penempatan_id' => $this->penempatan->id,
            'pembimbing_id' => $this->pembimbing->id,
            'status' => StatusPenilaianEnum::Draft->value,
        ]);
    }

    public function test_pembimbing_bisa_finalisasi_nilai_dan_validasi_seluruh_rubrik_aktif_harus_terisi(): void
    {
        $rubrikAktif = Rubrik::where('aktif', true)->get();
        $this->assertGreaterThan(0, $rubrikAktif->count());

        // 1. Coba finalisasi tapi skor tidak lengkap -> Ditolak
        $skorParsial = [$rubrikAktif->first()->id => 85];
        $responseGagal = $this->actingAs($this->pembimbing)->postJson(route('pembimbing.penilaian.store', $this->penempatan), [
            'skor' => $skorParsial,
            'is_final' => true,
        ]);
        $responseGagal->assertStatus(500); // InvalidArgumentException: Skor kriteria wajib diisi

        // 2. Finalisasi dengan seluruh rubrik aktif
        $skorLengkap = [];
        foreach ($rubrikAktif as $r) {
            $skorLengkap[$r->id] = 88; // Nilai rata-rata 88 -> Predikat A (>= 85)
        }

        $responseSukses = $this->actingAs($this->pembimbing)->postJson(route('pembimbing.penilaian.store', $this->penempatan), [
            'skor' => $skorLengkap,
            'catatan' => 'Kinerja magang sangat memuaskan dan berdedikasi tinggi.',
            'is_final' => true,
        ]);

        $responseSukses->assertStatus(200);
        $penilaian = Penilaian::where('penempatan_id', $this->penempatan->id)->firstOrFail();
        $this->assertEquals(StatusPenilaianEnum::Final, $penilaian->status);
        $this->assertEquals(88.0, (float) $penilaian->nilai_akhir);
        $this->assertEquals('A', $penilaian->predikat);
        $this->assertCount($rubrikAktif->count(), $penilaian->details);
    }

    public function test_revisi_nilai_final_merekam_audit_trail_riwayat_dengan_alasan_min_10_karakter(): void
    {
        $rubrikAktif = Rubrik::where('aktif', true)->get();
        $skorLengkap = [];
        foreach ($rubrikAktif as $r) {
            $skorLengkap[$r->id] = 80;
        }

        // Finalisasi awal
        $this->actingAs($this->pembimbing)->postJson(route('pembimbing.penilaian.store', $this->penempatan), [
            'skor' => $skorLengkap,
            'is_final' => true,
        ]);

        // Coba revisi tanpa alasan atau alasan terlalu pendek (< 10 karakter) -> Ditolak form request
        $responseTanpaAlasan = $this->actingAs($this->pembimbing)->postJson(route('pembimbing.penilaian.store', $this->penempatan), [
            'skor' => $skorLengkap,
            'is_final' => true,
            'alasan' => 'pendek',
        ]);
        $responseTanpaAlasan->assertStatus(422);

        // Revisi dengan alasan valid (min 10 karakter)
        foreach ($rubrikAktif as $r) {
            $skorLengkap[$r->id] = 95; // Naikkan ke 95
        }

        $alasanValid = 'Koreksi penambahan poin atas penyelesaian proyek mandiri peserta.';
        $responseRevisi = $this->actingAs($this->pembimbing)->postJson(route('pembimbing.penilaian.store', $this->penempatan), [
            'skor' => $skorLengkap,
            'is_final' => true,
            'alasan' => $alasanValid,
        ]);

        $responseRevisi->assertStatus(200);
        $penilaian = Penilaian::where('penempatan_id', $this->penempatan->id)->firstOrFail();
        $this->assertEquals(95.0, (float) $penilaian->nilai_akhir);

        // Cek rekam jejak di penilaian_riwayat
        $riwayat = PenilaianRiwayat::where('penilaian_id', $penilaian->id)->latest('waktu')->first();
        $this->assertNotNull($riwayat);
        $this->assertEquals($alasanValid, $riwayat->alasan);
        $this->assertEquals(80.0, (float) $riwayat->nilai_lama['nilai_akhir']);
        $this->assertEquals(95.0, (float) $riwayat->nilai_baru['nilai_akhir']);
    }

    public function test_penegakan_br_10_penempatan_tidak_dapat_diselesaikan_jika_belum_ada_nilai_final(): void
    {
        // 1. Sebelum penilaian final -> ditolak
        $responseGagal = $this->actingAs($this->pembimbing)
            ->postJson(route('pembimbing.penempatan.selesai', $this->penempatan));

        $responseGagal->assertStatus(422); // InvalidArgumentException BR-10 ditangani secara elegan (422)

        // 2. Buat nilai final
        $rubrikAktif = Rubrik::where('aktif', true)->get();
        $skorLengkap = [];
        foreach ($rubrikAktif as $r) {
            $skorLengkap[$r->id] = 85;
        }
        $this->actingAs($this->pembimbing)->postJson(route('pembimbing.penilaian.store', $this->penempatan), [
            'skor' => $skorLengkap,
            'is_final' => true,
        ]);

        // 3. Selesaikan penempatan -> Sukses
        $responseSukses = $this->actingAs($this->pembimbing)
            ->postJson(route('pembimbing.penempatan.selesai', $this->penempatan));

        $responseSukses->assertStatus(200);
        $this->penempatan->refresh();
        $this->assertEquals(StatusPenempatanEnum::Selesai, $this->penempatan->status);
        $this->assertNotNull($this->penempatan->tgl_ditetapkan_selesai);
        $this->assertNotNull($this->penempatan->tgl_nonaktif);
        $this->assertTrue($this->penempatan->tgl_nonaktif->gt(Carbon::today()), 'Tanggal nonaktif harus di masa depan sesuai masa tenggang BR-07');
    }

    public function test_ekspor_lembar_nilai_pdf_resmi_on_the_fly_ac_12(): void
    {
        // 1. Peserta belum punya nilai final -> Ditolak (403)
        $responseBelumFinal = $this->actingAs($this->peserta)->get(route('peserta.nilai.unduh'));
        $responseBelumFinal->assertStatus(403);

        // 2. Finalisasi nilai
        $rubrikAktif = Rubrik::where('aktif', true)->get();
        $skorLengkap = [];
        foreach ($rubrikAktif as $r) {
            $skorLengkap[$r->id] = 90;
        }
        $this->actingAs($this->pembimbing)->postJson(route('pembimbing.penilaian.store', $this->penempatan), [
            'skor' => $skorLengkap,
            'catatan' => 'Sangat kompeten.',
            'is_final' => true,
        ]);

        // 3. Unduh PDF oleh peserta -> Sukses berformat PDF A4
        $responseDownload = $this->actingAs($this->peserta)->get(route('peserta.nilai.unduh'));
        $responseDownload->assertStatus(200);
        $this->assertEquals('application/pdf', $responseDownload->headers->get('content-type'));
    }

    public function test_superadmin_strictly_read_only_ditolak_mengubah_nilai_ac_10_7(): void
    {
        $rubrikAktif = Rubrik::where('aktif', true)->get();
        $skor = [$rubrikAktif->first()->id => 90];

        $response = $this->actingAs($this->superAdmin)->postJson(route('pembimbing.penilaian.store', $this->penempatan), [
            'skor' => $skor,
            'is_final' => false,
        ]);

        $response->assertStatus(403);
    }

    public function test_scheduler_nonaktifkan_peserta_selesai_berjalan_idempotent_br_07(): void
    {
        // Siapkan penempatan selesai yang masa tenggangnya sudah lewat
        $this->penempatan->status = StatusPenempatanEnum::Selesai;
        $this->penempatan->tgl_ditetapkan_selesai = Carbon::today()->subMonths(2);
        $this->penempatan->tgl_nonaktif = Carbon::today()->subDays(1); // Lewat kemarin
        $this->penempatan->save();

        $this->assertTrue($this->peserta->status_aktif);

        // Eksekusi artisan command
        $exitCode = $this->artisan('magkom:nonaktifkan-peserta')->run();
        $this->assertEquals(0, $exitCode);

        $this->penempatan->refresh();
        $this->peserta->refresh();

        $this->assertEquals(StatusPenempatanEnum::Nonaktif, $this->penempatan->status);
        $this->assertFalse($this->peserta->status_aktif, 'Akun peserta dinonaktifkan setelah melewati masa tenggang');

        // Uji idempotency: Jalankan lagi, tidak boleh error dan tidak ada perubahan ekstra
        $exitCode2 = $this->artisan('magkom:nonaktifkan-peserta')->run();
        $this->assertEquals(0, $exitCode2);
    }
}
