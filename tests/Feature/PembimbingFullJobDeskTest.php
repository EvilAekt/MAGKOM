<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JalurEnum;
use App\Enums\JenjangEnum;
use App\Enums\KompleksitasEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusLogbookEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\StatusReviewProgresEnum;
use App\Enums\StatusTugasEnum;
use App\Enums\SumberPenempatanEnum;
use App\Models\Bidang;
use App\Models\Jurusan;
use App\Models\Logbook;
use App\Models\Penempatan;
use App\Models\Penilaian;
use App\Models\PenilaianRiwayat;
use App\Models\PesertaProfil;
use App\Models\ProgresLog;
use App\Models\Rubrik;
use App\Models\Tugas;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

final class PembimbingFullJobDeskTest extends TestCase
{
    use RefreshDatabase;

    private User $pembimbing;
    private Bidang $bidang;
    private Jurusan $jurusan;
    private Rubrik $rubrik1;
    private Rubrik $rubrik2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bidang = Bidang::create([
            'nama_bidang' => 'Bidang Aplikasi Informatika',
            'deskripsi' => 'Bidang Aptika Kominfo Sleman',
            'aktif' => true,
        ]);

        $this->jurusan = Jurusan::create([
            'nama_jurusan' => 'Teknik Informatika',
            'kategori' => 'mahasiswa',
            'aktif' => true,
        ]);

        $this->rubrik1 = Rubrik::create([
            'kriteria' => 'Kualitas Hasil Kerja',
            'bobot' => 50,
            'aktif' => true,
        ]);

        $this->rubrik2 = Rubrik::create([
            'kriteria' => 'Kedisiplinan & Sikap Kerja',
            'bobot' => 50,
            'aktif' => true,
        ]);

        $this->pembimbing = $this->createUser(
            'Pembimbing Lapangan',
            'pembimbing@magkom.go.id',
            RoleEnum::Pembimbing,
            $this->bidang->id
        );
    }

    private function createUser(string $nama, string $email, RoleEnum $role, ?int $bidangId = null): User
    {
        $user = new User();
        $user->nama = $nama;
        $user->email = $email;
        $user->password = Hash::make('Password123!');
        $user->role = $role;
        $user->bidang_id = $bidangId;
        $user->status_aktif = true;
        $user->email_verified_at = now();
        $user->must_change_password = false;
        $user->save();

        return $user;
    }

    private function createPenempatan(string $namaPeserta, string $emailPeserta): Penempatan
    {
        $peserta = $this->createUser($namaPeserta, $emailPeserta, RoleEnum::Peserta);

        PesertaProfil::create([
            'user_id' => $peserta->id,
            'jurusan_id' => $this->jurusan->id,
            'jenjang' => JenjangEnum::S1,
            'institusi' => 'Universitas Negeri Sleman',
            'nim_nisn' => '20240101',
            'no_hp' => '081234567890',
        ]);

        return Penempatan::create([
            'peserta_id' => $peserta->id,
            'pembimbing_id' => $this->pembimbing->id,
            'bidang_id' => $this->bidang->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'status' => StatusPenempatanEnum::Aktif,
            'tgl_mulai' => Carbon::today()->subDays(10),
            'tgl_selesai' => Carbon::today()->addDays(50),
        ]);
    }

    public function test_pembimbing_bisa_melihat_direktori_peserta_dan_profil_360_dengan_nama_jurusan(): void
    {
        $penempatan = $this->createPenempatan('Fajar Nugroho', 'fajar@uns.ac.id');

        // 1. Direktori Peserta
        $responseIndex = $this->actingAs($this->pembimbing)
            ->get(route('pembimbing.peserta.index'));

        $responseIndex->assertOk();
        $responseIndex->assertSee('Fajar Nugroho');
        $responseIndex->assertSee('Universitas Negeri Sleman');
        $responseIndex->assertSee('Teknik Informatika');

        // 2. Profil 360° Mentee
        $responseShow = $this->actingAs($this->pembimbing)
            ->get(route('pembimbing.peserta.show', $penempatan->id));

        $responseShow->assertOk();
        $responseShow->assertSee('Profil 360° Mentee Bimbingan');
        $responseShow->assertSee('Fajar Nugroho');
        $responseShow->assertSee('Teknik Informatika');
    }

    public function test_pembimbing_bisa_membuat_tugas_baru_dengan_validasi_deadline_sukses(): void
    {
        $penempatan = $this->createPenempatan('Anisa Rahma', 'anisa@uns.ac.id');

        $response = $this->actingAs($this->pembimbing)
            ->post(route('pembimbing.tugas.store'), [
                'penempatan_id' => $penempatan->id,
                'judul' => 'Pengembangan Modul Notifikasi',
                'kategori' => 'Backend Development',
                'kompleksitas' => 'sedang',
                'deadline' => Carbon::today()->addDays(14)->toDateString(),
                'deskripsi' => 'Implementasi REST endpoint untuk notifikasi dinas.',
            ]);

        $response->assertRedirect(route('pembimbing.tugas.index'));
        $this->assertDatabaseHas('tugas', [
            'penempatan_id' => $penempatan->id,
            'judul' => 'Pengembangan Modul Notifikasi',
            'kategori' => 'Backend Development',
        ]);
    }

    public function test_pembimbing_ditolak_membuat_tugas_dengan_deadline_melampaui_selesai_magang(): void
    {
        $penempatan = $this->createPenempatan('Bima Sakti', 'bima@uns.ac.id');

        // Deadline setelah tgl_selesai penempatan (+60 hari, padahal selesai +50 hari)
        $response = $this->actingAs($this->pembimbing)
            ->post(route('pembimbing.tugas.store'), [
                'penempatan_id' => $penempatan->id,
                'judul' => 'Tugas Melebihi Masa Magang',
                'kategori' => 'Backend',
                'kompleksitas' => 'sedang',
                'deadline' => Carbon::today()->addDays(60)->toDateString(),
            ]);

        $response->assertSessionHasErrors('deadline');
    }

    public function test_pembimbing_bisa_review_progres_dan_wajib_catatan_jika_perlu_revisi(): void
    {
        $penempatan = $this->createPenempatan('Cahyo Utomo', 'cahyo@uns.ac.id');

        $tugas = Tugas::create([
            'penempatan_id' => $penempatan->id,
            'judul' => 'Riset Komparasi Database',
            'kategori' => 'Database',
            'kompleksitas' => KompleksitasEnum::Sedang,
            'deadline' => Carbon::today()->addDays(10),
            'status' => StatusTugasEnum::Proses,
        ]);

        $progres = ProgresLog::create([
            'tugas_id' => $tugas->id,
            'catatan' => 'Draf dokumen riset telah selesai disusun.',
            'tgl_lapor' => Carbon::today(),
            'status_review' => StatusReviewProgresEnum::Menunggu,
        ]);

        // 1. Gagal jika memilih perlu_revisi tanpa feedback
        $responseGagal = $this->actingAs($this->pembimbing)
            ->post(route('pembimbing.progres.review', $progres->id), [
                'status_review' => 'perlu_revisi',
                'feedback' => '',
            ]);
        $responseGagal->assertSessionHasErrors('feedback');

        // 2. Sukses jika feedback diisi
        $responseSukses = $this->actingAs($this->pembimbing)
            ->post(route('pembimbing.progres.review', $progres->id), [
                'status_review' => 'perlu_revisi',
                'feedback' => 'Tolong tambahkan perbandingan performa query latency PostgreSQL vs MySQL.',
            ]);
        $responseSukses->assertSessionHasNoErrors();
        $this->assertEquals(StatusReviewProgresEnum::PerluRevisi, $progres->fresh()->status_review);
    }

    public function test_pembimbing_bisa_menandai_tugas_selesai(): void
    {
        $penempatan = $this->createPenempatan('Dian Permata', 'dian@uns.ac.id');

        $tugas = Tugas::create([
            'penempatan_id' => $penempatan->id,
            'judul' => 'Audit Keamanan Sesi',
            'kategori' => 'Cyber Security',
            'kompleksitas' => KompleksitasEnum::Tinggi,
            'deadline' => Carbon::today()->addDays(5),
            'status' => StatusTugasEnum::Proses,
        ]);

        $response = $this->actingAs($this->pembimbing)
            ->post(route('pembimbing.tugas.selesai', $tugas->id));

        $response->assertSessionHasNoErrors();
        $this->assertEquals(StatusTugasEnum::Selesai, $tugas->fresh()->status);
    }

    public function test_pembimbing_bisa_review_logbook_dan_wajib_catatan_jika_perlu_revisi(): void
    {
        $penempatan = $this->createPenempatan('Eko Prasetyo', 'eko@uns.ac.id');

        $logbook = Logbook::create([
            'penempatan_id' => $penempatan->id,
            'tanggal' => Carbon::today(),
            'kegiatan' => 'Mengikuti koordinasi internal tim programmer dinas.',
            'status' => StatusLogbookEnum::Terkirim,
        ]);

        // 1. Gagal jika perlu_revisi tanpa catatan_pembimbing
        $responseGagal = $this->actingAs($this->pembimbing)
            ->post(route('pembimbing.logbook.review', $logbook->id), [
                'status' => 'perlu_revisi',
                'catatan_pembimbing' => '',
            ]);
        $responseGagal->assertSessionHasErrors('catatan_pembimbing');

        // 2. Sukses jika catatan_pembimbing disertakan
        $responseSukses = $this->actingAs($this->pembimbing)
            ->post(route('pembimbing.logbook.review', $logbook->id), [
                'status' => 'perlu_revisi',
                'catatan_pembimbing' => 'Tolong lengkapi poin hasil keputusan rapat koordinasi.',
            ]);
        $responseSukses->assertSessionHasNoErrors();
        $this->assertEquals(StatusLogbookEnum::PerluRevisi, $logbook->fresh()->status);
    }

    public function test_pembimbing_bisa_simpan_draf_dan_finalisasi_nilai_rubrik(): void
    {
        $penempatan = $this->createPenempatan('Gita Savitri', 'gita@uns.ac.id');

        // 1. Simpan Draf
        $responseDraft = $this->actingAs($this->pembimbing)
            ->post(route('pembimbing.penilaian.store', $penempatan->id), [
                'is_final' => 0,
                'skor' => [
                    $this->rubrik1->id => 80,
                    $this->rubrik2->id => 90,
                ],
                'catatan' => 'Draf nilai tengah magang.',
            ]);
        $responseDraft->assertSessionHasNoErrors();

        $penilaian = Penilaian::where('penempatan_id', $penempatan->id)->first();
        $this->assertNotNull($penilaian);
        $this->assertEquals(85.0, (float) $penilaian->nilai_akhir);

        // 2. Finalisasi Nilai
        $responseFinal = $this->actingAs($this->pembimbing)
            ->post(route('pembimbing.penilaian.store', $penempatan->id), [
                'is_final' => 1,
                'skor' => [
                    $this->rubrik1->id => 88,
                    $this->rubrik2->id => 92,
                ],
                'catatan' => 'Penilaian akhir magang resmi.',
            ]);
        $responseFinal->assertSessionHasNoErrors();
        $this->assertEquals('final', $penilaian->fresh()->status->value);
        $this->assertEquals(90.0, (float) $penilaian->fresh()->nilai_akhir);
    }

    public function test_pembimbing_bisa_revisi_nilai_final_dengan_alasan_min_10_karakter_dan_tercatat_riwayat(): void
    {
        $penempatan = $this->createPenempatan('Hendra Kurnia', 'hendra@uns.ac.id');

        // Finalisasi awal
        $this->actingAs($this->pembimbing)
            ->post(route('pembimbing.penilaian.store', $penempatan->id), [
                'is_final' => 1,
                'skor' => [
                    $this->rubrik1->id => 80,
                    $this->rubrik2->id => 80,
                ],
            ]);

        // Revisi nilai final tanpa alasan (harus error)
        $responseGagal = $this->actingAs($this->pembimbing)
            ->post(route('pembimbing.penilaian.store', $penempatan->id), [
                'is_final' => 1,
                'skor' => [
                    $this->rubrik1->id => 90,
                    $this->rubrik2->id => 90,
                ],
                'alasan' => 'pendek',
            ]);
        $responseGagal->assertSessionHasErrors('alasan');

        // Revisi nilai final dengan alasan valid (min 10 karakter)
        $responseSukses = $this->actingAs($this->pembimbing)
            ->post(route('pembimbing.penilaian.store', $penempatan->id), [
                'is_final' => 1,
                'skor' => [
                    $this->rubrik1->id => 90,
                    $this->rubrik2->id => 90,
                ],
                'alasan' => 'Penambahan nilai atas kontribusi proyek dinas tambahan.',
            ]);
        $responseSukses->assertSessionHasNoErrors();

        $riwayat = PenilaianRiwayat::where('penilaian_id', $penempatan->penilaian->id)->first();
        $this->assertNotNull($riwayat);
        $this->assertStringContainsString('Penambahan nilai', $riwayat->alasan);

        // Verifikasi tampilan halaman detail penilaian tidak crash saat menampilkan audit trail
        $responseShow = $this->actingAs($this->pembimbing)
            ->get(route('pembimbing.penilaian.show', $penempatan->id));
        $responseShow->assertOk();
        $responseShow->assertSee('Riwayat Jejak Perubahan Nilai Final');
        $responseShow->assertSee('Penambahan nilai atas kontribusi proyek dinas tambahan.');
    }

    public function test_pembimbing_bisa_menetapkan_selesai_magang_jika_nilai_final(): void
    {
        $penempatan = $this->createPenempatan('Indah Lestari', 'indah@uns.ac.id');

        // 1. Coba selesaikan sebelum dinilai (harus gagal)
        $responseGagal = $this->actingAs($this->pembimbing)
            ->post(route('pembimbing.penempatan.selesai', $penempatan->id));
        $responseGagal->assertSessionHasErrors();

        // 2. Beri nilai final
        $this->actingAs($this->pembimbing)
            ->post(route('pembimbing.penilaian.store', $penempatan->id), [
                'is_final' => 1,
                'skor' => [
                    $this->rubrik1->id => 85,
                    $this->rubrik2->id => 85,
                ],
            ]);

        // 3. Selesaikan magang (harus sukses)
        $responseSukses = $this->actingAs($this->pembimbing)
            ->post(route('pembimbing.penempatan.selesai', $penempatan->id));
        $responseSukses->assertSessionHasNoErrors();
        $this->assertEquals(StatusPenempatanEnum::Selesai, $penempatan->fresh()->status);
        $this->assertNotNull($penempatan->fresh()->tgl_nonaktif);
    }

    public function test_superadmin_read_only_ditolak_melakukan_mutasi_pada_jobdesk_pembimbing(): void
    {
        $penempatan = $this->createPenempatan('Joko Susilo', 'joko@uns.ac.id');
        $superadmin = $this->createUser('Super Admin', 'superadmin@magkom.go.id', RoleEnum::SuperAdmin);

        // POST store tugas ditolak 403
        $this->actingAs($superadmin)
            ->post(route('pembimbing.tugas.store'), [
                'penempatan_id' => $penempatan->id,
                'judul' => 'Tugas Ilegal',
                'deadline' => Carbon::tomorrow()->toDateString(),
            ])
            ->assertForbidden();

        // POST review logbook ditolak 403
        $logbook = Logbook::create([
            'penempatan_id' => $penempatan->id,
            'tanggal' => Carbon::today(),
            'kegiatan' => 'Kegiatan',
            'status' => StatusLogbookEnum::Terkirim,
        ]);

        $this->actingAs($superadmin)
            ->post(route('pembimbing.logbook.review', $logbook->id), [
                'status' => 'perlu_revisi',
                'catatan_pembimbing' => 'Catatan',
            ])
            ->assertForbidden();

        // POST simpan nilai ditolak 403
        $this->actingAs($superadmin)
            ->post(route('pembimbing.penilaian.store', $penempatan->id), [
                'is_final' => 1,
                'skor' => [$this->rubrik1->id => 90],
            ])
            ->assertForbidden();
    }
}
