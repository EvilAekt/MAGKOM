<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JalurEnum;
use App\Enums\KompleksitasEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusLogbookEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\StatusReviewProgresEnum;
use App\Enums\StatusTugasEnum;
use App\Enums\SumberPenempatanEnum;
use App\Models\Bidang;
use App\Models\Logbook;
use App\Models\Penempatan;
use App\Models\Pengaturan;
use App\Models\ProgresLog;
use App\Models\Tugas;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PembimbingPortalFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected User $pembimbingA;

    protected User $pembimbingB;

    protected User $pesertaA;

    protected User $superAdmin;

    protected Bidang $bidang;

    protected Penempatan $penempatanA;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bidang = Bidang::create([
            'nama_bidang' => 'Aplikasi Informatika',
            'deskripsi' => 'Bidang Aptika Kominfo Sleman',
            'aktif' => true,
        ]);

        $this->pembimbingA = new User;
        $this->pembimbingA->nama = 'Budi Santoso (Pembimbing A)';
        $this->pembimbingA->email = 'pembimbing.a@slemankab.go.id';
        $this->pembimbingA->password = bcrypt('password123');
        $this->pembimbingA->role = RoleEnum::Pembimbing;
        $this->pembimbingA->bidang_id = $this->bidang->id;
        $this->pembimbingA->status_aktif = true;
        $this->pembimbingA->email_verified_at = now();
        $this->pembimbingA->must_change_password = false;
        $this->pembimbingA->save();

        $this->pembimbingB = new User;
        $this->pembimbingB->nama = 'Siti Rahma (Pembimbing B)';
        $this->pembimbingB->email = 'pembimbing.b@slemankab.go.id';
        $this->pembimbingB->password = bcrypt('password123');
        $this->pembimbingB->role = RoleEnum::Pembimbing;
        $this->pembimbingB->bidang_id = $this->bidang->id;
        $this->pembimbingB->status_aktif = true;
        $this->pembimbingB->email_verified_at = now();
        $this->pembimbingB->must_change_password = false;
        $this->pembimbingB->save();

        $this->pesertaA = new User;
        $this->pesertaA->nama = 'Ahmad Mentee';
        $this->pesertaA->email = 'ahmad.mentee@kampus.ac.id';
        $this->pesertaA->password = bcrypt('password123');
        $this->pesertaA->role = RoleEnum::Peserta;
        $this->pesertaA->status_aktif = true;
        $this->pesertaA->email_verified_at = now();
        $this->pesertaA->must_change_password = false;
        $this->pesertaA->save();

        $this->superAdmin = new User;
        $this->superAdmin->nama = 'Super Administrator';
        $this->superAdmin->email = 'superadmin@slemankab.go.id';
        $this->superAdmin->password = bcrypt('password123');
        $this->superAdmin->role = RoleEnum::SuperAdmin;
        $this->superAdmin->status_aktif = true;
        $this->superAdmin->email_verified_at = now();
        $this->superAdmin->must_change_password = false;
        $this->superAdmin->save();

        $this->penempatanA = Penempatan::create([
            'peserta_id' => $this->pesertaA->id,
            'pembimbing_id' => $this->pembimbingA->id,
            'bidang_id' => $this->bidang->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'tgl_mulai' => Carbon::today()->subDays(10),
            'tgl_selesai' => Carbon::today()->addDays(20),
            'status' => StatusPenempatanEnum::Aktif,
        ]);

        Pengaturan::create([
            'kunci' => 'maks_peserta_aktif_per_pembimbing',
            'nilai' => '10',
            'tipe' => 'integer',
        ]);
    }

    public function test_pembimbing_bisa_mengakses_dashboard_dan_melihat_metrik_kapasitas(): void
    {
        $response = $this->actingAs($this->pembimbingA)
            ->get(route('pembimbing.dashboard'));

        $response->assertOk();
        $response->assertViewIs('pembimbing.dashboard');
        $response->assertSee('Beban Mentee Aktif');
        $response->assertSee('1 / 10');
        $response->assertSee('Ahmad Mentee');
    }

    public function test_pembimbing_bisa_mengakses_direktori_peserta_dan_detail_profil_360(): void
    {
        // 1. Indeks direktori peserta
        $indexResponse = $this->actingAs($this->pembimbingA)
            ->get(route('pembimbing.peserta.index'));

        $indexResponse->assertOk();
        $indexResponse->assertViewIs('pembimbing.peserta.index');
        $indexResponse->assertSee('Ahmad Mentee');

        // 2. Profil 360 mentee
        $showResponse = $this->actingAs($this->pembimbingA)
            ->get(route('pembimbing.peserta.show', $this->penempatanA));

        $showResponse->assertOk();
        $showResponse->assertViewIs('pembimbing.peserta.show');
        $showResponse->assertSee('Profil 360° Mentee Bimbingan');
        $showResponse->assertSee('ahmad.mentee@kampus.ac.id');
    }

    public function test_proteksi_idor_lintas_pembimbing_pada_profil_peserta(): void
    {
        // Pembimbing B mencoba membuka mentee milik Pembimbing A
        $response = $this->actingAs($this->pembimbingB)
            ->get(route('pembimbing.peserta.show', $this->penempatanA));

        $response->assertStatus(403);
    }

    public function test_validasi_deadline_tugas_menolak_tanggal_lampau_dan_melebihi_akhir_magang(): void
    {
        // 1. Deadline kemarin -> ditolak
        $pastResponse = $this->actingAs($this->pembimbingA)
            ->postJson(route('pembimbing.tugas.store'), [
                'penempatan_id' => $this->penempatanA->id,
                'judul' => 'Tugas Lampau',
                'deskripsi' => 'Deskripsi tugas',
                'kategori' => 'Pengembangan Sistem',
                'kompleksitas' => KompleksitasEnum::Sedang->value,
                'deadline' => Carbon::yesterday()->toDateString(),
            ]);

        $pastResponse->assertStatus(422);
        $pastResponse->assertJsonValidationErrors(['deadline']);

        // 2. Deadline melebihi tgl_selesai magang (penempatanA selesai +20 hari, kita coba +25 hari) -> ditolak AC-08-1
        $exceedResponse = $this->actingAs($this->pembimbingA)
            ->postJson(route('pembimbing.tugas.store'), [
                'penempatan_id' => $this->penempatanA->id,
                'judul' => 'Tugas Melebihi Magang',
                'deskripsi' => 'Deskripsi tugas',
                'kategori' => 'Pengembangan Sistem',
                'kompleksitas' => KompleksitasEnum::Sedang->value,
                'deadline' => Carbon::today()->addDays(25)->toDateString(),
            ]);

        $exceedResponse->assertStatus(422);
        $exceedResponse->assertJsonValidationErrors(['deadline']);

        // 3. Deadline valid (dalam rentang magang) -> sukses dibuat
        $validDeadline = Carbon::today()->addDays(5)->toDateString();
        $validResponse = $this->actingAs($this->pembimbingA)
            ->postJson(route('pembimbing.tugas.store'), [
                'penempatan_id' => $this->penempatanA->id,
                'judul' => 'Tugas Valid Magang',
                'deskripsi' => 'Deskripsi tugas valid',
                'kategori' => 'Pengembangan Sistem',
                'kompleksitas' => KompleksitasEnum::Sedang->value,
                'deadline' => $validDeadline,
            ]);

        $validResponse->assertStatus(201);
        $this->assertDatabaseHas('tugas', [
            'penempatan_id' => $this->penempatanA->id,
            'judul' => 'Tugas Valid Magang',
        ]);
    }

    public function test_pembimbing_bisa_menandai_tugas_selesai(): void
    {
        $tugas = Tugas::create([
            'penempatan_id' => $this->penempatanA->id,
            'judul' => 'Perancangan API',
            'deskripsi' => 'Buat REST API',
            'kategori' => 'Backend',
            'kompleksitas' => KompleksitasEnum::Sedang,
            'deadline' => Carbon::today()->addDays(5),
            'status' => StatusTugasEnum::Proses,
        ]);

        $response = $this->actingAs($this->pembimbingA)
            ->post(route('pembimbing.tugas.selesai', $tugas));

        $response->assertRedirect();
        $this->assertEquals(StatusTugasEnum::Selesai, $tugas->fresh()->status);
    }

    public function test_review_progres_wajib_feedback_jika_status_perlu_revisi(): void
    {
        $tugas = Tugas::create([
            'penempatan_id' => $this->penempatanA->id,
            'judul' => 'Implementasi UI',
            'deskripsi' => 'Buat tampilan dashboard',
            'kategori' => 'Frontend',
            'kompleksitas' => KompleksitasEnum::Sedang,
            'deadline' => Carbon::today()->addDays(7),
            'status' => StatusTugasEnum::Proses,
        ]);

        $progres = ProgresLog::create([
            'tugas_id' => $tugas->id,
            'catatan' => 'Sudah menyelesaikan 50% slicing UI',
            'tgl_lapor' => Carbon::today(),
            'status_review' => StatusReviewProgresEnum::Menunggu,
        ]);

        // Status perlu_revisi tanpa feedback -> ditolak AC-08-4
        $failResponse = $this->actingAs($this->pembimbingA)
            ->postJson(route('pembimbing.progres.review', $progres), [
                'status_review' => StatusReviewProgresEnum::PerluRevisi->value,
                'feedback' => null,
            ]);

        $failResponse->assertStatus(422);
        $failResponse->assertJsonValidationErrors(['feedback']);

        // Status perlu_revisi dengan feedback -> sukses
        $successResponse = $this->actingAs($this->pembimbingA)
            ->postJson(route('pembimbing.progres.review', $progres), [
                'status_review' => StatusReviewProgresEnum::PerluRevisi->value,
                'feedback' => 'Mohon perbaiki warna tombol sesuai panduan Sleman Blue.',
            ]);

        $successResponse->assertOk();
        $this->assertEquals(StatusReviewProgresEnum::PerluRevisi, $progres->fresh()->status_review);
        $this->assertEquals('Mohon perbaiki warna tombol sesuai panduan Sleman Blue.', $progres->fresh()->feedback);
    }

    public function test_review_logbook_wajib_catatan_jika_status_perlu_revisi(): void
    {
        $logbook = Logbook::create([
            'penempatan_id' => $this->penempatanA->id,
            'tanggal' => Carbon::today(),
            'kegiatan' => 'Mengerjakan fitur otentikasi login',
            'status' => StatusLogbookEnum::Terkirim,
        ]);

        // Status perlu_revisi tanpa catatan pembimbing -> ditolak AC-09-4
        $failResponse = $this->actingAs($this->pembimbingA)
            ->postJson(route('pembimbing.logbook.review', $logbook), [
                'status' => StatusLogbookEnum::PerluRevisi->value,
                'catatan_pembimbing' => '',
            ]);

        $failResponse->assertStatus(422);
        $failResponse->assertJsonValidationErrors(['catatan_pembimbing']);

        // Status perlu_revisi dengan catatan -> sukses
        $successResponse = $this->actingAs($this->pembimbingA)
            ->postJson(route('pembimbing.logbook.review', $logbook), [
                'status' => StatusLogbookEnum::PerluRevisi->value,
                'catatan_pembimbing' => 'Uraikan hasil pengerjaan secara lebih detail beserta kendala.',
            ]);

        $successResponse->assertOk();
        $this->assertEquals(StatusLogbookEnum::PerluRevisi, $logbook->fresh()->status);
        $this->assertEquals('Uraikan hasil pengerjaan secara lebih detail beserta kendala.', $logbook->fresh()->catatan_pembimbing);
    }

    public function test_superadmin_read_only_ditolak_melakukan_aksi_tulis_pada_pembimbing(): void
    {
        // SuperAdmin mencoba POST membuat tugas -> ditolak 403 Forbidden
        $response = $this->actingAs($this->superAdmin)
            ->postJson(route('pembimbing.tugas.store'), [
                'penempatan_id' => $this->penempatanA->id,
                'judul' => 'Tugas SuperAdmin Ilegal',
                'kategori' => 'General',
                'kompleksitas' => KompleksitasEnum::Rendah->value,
                'deadline' => Carbon::today()->addDays(3)->toDateString(),
            ]);

        $response->assertStatus(403);
    }
}
