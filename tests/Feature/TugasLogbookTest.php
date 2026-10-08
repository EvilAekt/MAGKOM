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
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TugasLogbookTest extends TestCase
{
    use DatabaseTransactions;

    protected User $pembimbing;
    protected User $peserta;
    protected User $superAdmin;
    protected Penempatan $penempatan;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('private');

        $this->pembimbing = new User();
        $this->pembimbing->nama = 'Budi Pembimbing';
        $this->pembimbing->email = 'pembimbing@kominfo.test';
        $this->pembimbing->password = 'secret123';
        $this->pembimbing->role = RoleEnum::Pembimbing;
        $this->pembimbing->email_verified_at = now();
        $this->pembimbing->status_aktif = true;
        $this->pembimbing->save();

        $this->peserta = new User();
        $this->peserta->nama = 'Siti Peserta';
        $this->peserta->email = 'peserta@kominfo.test';
        $this->peserta->password = 'secret123';
        $this->peserta->role = RoleEnum::Peserta;
        $this->peserta->email_verified_at = now();
        $this->peserta->status_aktif = true;
        $this->peserta->save();

        $this->superAdmin = new User();
        $this->superAdmin->nama = 'Super Administrator';
        $this->superAdmin->email = 'superadmin@kominfo.test';
        $this->superAdmin->password = 'secret123';
        $this->superAdmin->role = RoleEnum::SuperAdmin;
        $this->superAdmin->email_verified_at = now();
        $this->superAdmin->status_aktif = true;
        $this->superAdmin->save();

        $bidang = Bidang::firstOrCreate(
            ['nama_bidang' => 'Teknologi Informasi'],
            ['aktif' => true]
        );

        $this->penempatan = Penempatan::create([
            'peserta_id' => $this->peserta->id,
            'pembimbing_id' => $this->pembimbing->id,
            'bidang_id' => $bidang->id,
            'jalur' => JalurEnum::Khusus,
            'sumber' => SumberPenempatanEnum::Manual,
            'tgl_mulai' => Carbon::today()->startOfMonth(),
            'tgl_selesai' => Carbon::today()->addMonths(2)->endOfMonth(),
            'status' => StatusPenempatanEnum::Aktif,
        ]);
    }

    public function test_pembimbing_bisa_membuat_tugas_dan_verifikasi_accessor_terlambat_runtime_br_16(): void
    {
        // 1. Pastikan kolom 'terlambat' TIDAK ADA di tabel basis data 'tugas'
        $this->assertFalse(Schema::hasColumn('tugas', 'terlambat'), 'Kolom terlambat tidak boleh ada di tabel database (BR-16).');

        // 2. Pembimbing membuat tugas deadline masa depan
        $response = $this->actingAs($this->pembimbing)->postJson(route('pembimbing.tugas.store'), [
            'penempatan_id' => $this->penempatan->id,
            'judul' => 'Membuat Modul Landing Page',
            'deskripsi' => 'Kerjakan modul landing page dengan Blade',
            'kategori' => 'Frontend',
            'kompleksitas' => 'sedang',
            'deadline' => Carbon::today()->addDays(5)->toDateString(),
        ]);

        $response->assertStatus(201);
        $tugasId = $response->json('data.id');

        $tugas = Tugas::find($tugasId);
        $this->assertNotNull($tugas);
        $this->assertEquals(StatusTugasEnum::Belum, $tugas->status);
        $this->assertFalse($tugas->terlambat, 'Tugas dengan deadline mendatang tidak boleh bernilai terlambat');

        // 3. Tugas dengan deadline kemarin tapi status belum selesai -> terlambat = true
        $tugas->deadline = Carbon::today()->subDays(2);
        $tugas->save();
        $tugas->refresh();
        $this->assertTrue($tugas->terlambat, 'Tugas dengan deadline lampau dan status belum/proses harus terlambat');

        // 4. Query scope terlambat menemukan tugas tersebut
        $tugasTerlambatList = Tugas::terlambat()->pluck('id')->toArray();
        $this->assertContains($tugas->id, $tugasTerlambatList);

        // 5. Jika tugas telah ditandai selesai -> terlambat = false meskipun deadline lampau
        $tugas->status = StatusTugasEnum::Selesai;
        $tugas->save();
        $tugas->refresh();
        $this->assertFalse($tugas->terlambat, 'Tugas berstatus selesai tidak boleh dianggap terlambat');

        $tugasTerlambatSetelahSelesai = Tugas::terlambat()->pluck('id')->toArray();
        $this->assertNotContains($tugas->id, $tugasTerlambatSetelahSelesai);
    }

    public function test_superadmin_ditolak_membuat_tugas_ac_08_5(): void
    {
        $response = $this->actingAs($this->superAdmin)->postJson(route('pembimbing.tugas.store'), [
            'penempatan_id' => $this->penempatan->id,
            'judul' => 'Tugas oleh SuperAdmin',
            'kategori' => 'Admin',
            'kompleksitas' => 'rendah',
            'deadline' => Carbon::today()->addDays(3)->toDateString(),
        ]);

        $response->assertStatus(403);
    }

    public function test_peserta_unggah_progres_dan_pembimbing_review_umpan_balik(): void
    {
        $tugas = Tugas::create([
            'penempatan_id' => $this->penempatan->id,
            'judul' => 'Slicing Desain UI',
            'kategori' => 'UI/UX',
            'kompleksitas' => KompleksitasEnum::Sedang,
            'deadline' => Carbon::today()->addDays(4),
            'status' => StatusTugasEnum::Belum,
        ]);

        $fileMock = UploadedFile::fake()->create('laporan-slicing.pdf', 300, 'application/pdf');

        // Peserta mengunggah progres
        $response = $this->actingAs($this->peserta)->postJson(route('peserta.tugas.progres', $tugas), [
            'catatan' => 'Telah menyelesaikan layout header dan hero section.',
            'file' => $fileMock,
        ]);

        $response->assertStatus(201);
        $progresId = $response->json('data.id');

        $tugas->refresh();
        $this->assertEquals(StatusTugasEnum::Proses, $tugas->status, 'Status tugas otomatis bergeser ke proses setelah progres diunggah');

        $progres = ProgresLog::find($progresId);
        $this->assertNotNull($progres);
        $this->assertEquals(StatusReviewProgresEnum::Menunggu, $progres->status_review);
        $this->assertNotNull($progres->file);
        Storage::disk('private')->assertExists($progres->file);

        // Pembimbing mereview progres
        $reviewResponse = $this->actingAs($this->pembimbing)->postJson(route('pembimbing.progres.review', $progres), [
            'status_review' => 'diterima',
            'feedback' => 'Hasil pengerjaan rapi dan sesuai acuan desain.',
        ]);

        $reviewResponse->assertStatus(200);
        $progres->refresh();
        $this->assertEquals(StatusReviewProgresEnum::Diterima, $progres->status_review);
        $this->assertEquals('Hasil pengerjaan rapi dan sesuai acuan desain.', $progres->feedback);
    }

    public function test_peserta_isi_logbook_harian_validasi_batas_mundur_br_09_dan_constraint_unik(): void
    {
        // 1. Peserta mengisi logbook hari ini -> Sukses
        $today = Carbon::today()->toDateString();
        $responseHariIni = $this->actingAs($this->peserta)->postJson(route('peserta.logbook.store'), [
            'tanggal' => $today,
            'kegiatan' => 'Mengikuti pengarahan pagi dan melanjutkan perbaikan bug formulir pendaftaran.',
        ]);

        $responseHariIni->assertStatus(201);
        $this->assertDatabaseHas('logbook', [
            'penempatan_id' => $this->penempatan->id,
            'tanggal' => $today,
            'status' => StatusLogbookEnum::Terkirim->value,
        ]);

        // 2. Mengisi logbook kedua di tanggal yang sama -> Ditolak (Constraint unik)
        $responseDuplikat = $this->actingAs($this->peserta)->postJson(route('peserta.logbook.store'), [
            'tanggal' => $today,
            'kegiatan' => 'Mencoba mengisi logbook ganda pada hari yang sama.',
        ]);

        $responseDuplikat->assertStatus(422);

        // 3. Mengisi logbook tanggal masa depan (besok) -> Ditolak
        $besok = Carbon::today()->addDay()->toDateString();
        $responseMasaDepan = $this->actingAs($this->peserta)->postJson(route('peserta.logbook.store'), [
            'tanggal' => $besok,
            'kegiatan' => 'Kegiatan yang belum terjadi di masa depan.',
        ]);

        $responseMasaDepan->assertStatus(422);

        // 4. Mengisi logbook mundur melebihi toleransi pengaturan (default: 3 hari, coba H-4) -> Ditolak (BR-09)
        $maksMundur = (int) (Pengaturan::get('logbook_maks_mundur_hari') ?? 3);
        $terlaluLampau = Carbon::today()->subDays($maksMundur + 1)->toDateString();

        $responseTerlaluLampau = $this->actingAs($this->peserta)->postJson(route('peserta.logbook.store'), [
            'tanggal' => $terlaluLampau,
            'kegiatan' => 'Mencoba mengisi aktivitas yang sudah lewat jauh dari batas toleransi.',
        ]);

        $responseTerlaluLampau->assertStatus(422);

        // 5. Pembimbing mereview logbook
        $logbook = Logbook::where('penempatan_id', $this->penempatan->id)->first();
        $reviewResponse = $this->actingAs($this->pembimbing)->postJson(route('pembimbing.logbook.review', $logbook), [
            'status' => 'perlu_revisi',
            'catatan_pembimbing' => 'Mohon tambahkan rincian nomor tiket bug yang diperbaiki.',
        ]);

        $reviewResponse->assertStatus(200);
        $logbook->refresh();
        $this->assertEquals(StatusLogbookEnum::PerluRevisi, $logbook->status);
        $this->assertEquals('Mohon tambahkan rincian nomor tiket bug yang diperbaiki.', $logbook->catatan_pembimbing);
    }
}
