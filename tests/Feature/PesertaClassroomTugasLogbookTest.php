<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JalurEnum;
use App\Enums\KompleksitasEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusLogbookEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\StatusTugasEnum;
use App\Enums\SumberPenempatanEnum;
use App\Models\Bidang;
use App\Models\Logbook;
use App\Models\Penempatan;
use App\Models\ProgresLog;
use App\Models\Tugas;
use App\Models\User;
use Database\Seeders\JenisDokumenSeeder;
use Database\Seeders\RubrikSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PesertaClassroomTugasLogbookTest extends TestCase
{
    use RefreshDatabase;

    protected User $peserta;

    protected User $pembimbing;

    protected Bidang $bidang;

    protected Penempatan $penempatan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(JenisDokumenSeeder::class);
        $this->seed(RubrikSeeder::class);
        Storage::fake('private');

        $this->bidang = Bidang::create([
            'nama_bidang' => 'Bidang Aplikasi Informatika (Aptika)',
            'deskripsi' => 'Pengembangan aplikasi dan layanan e-government',
            'aktif' => true,
        ]);

        $this->pembimbing = new User([
            'nama' => 'Budi Santoso, S.Kom',
            'email' => 'pembimbing.classroom@magkom.test',
            'password' => bcrypt('Password123!'),
            'status_aktif' => true,
            'must_change_password' => false,
        ]);
        $this->pembimbing->role = RoleEnum::Pembimbing;
        $this->pembimbing->email_verified_at = now();
        $this->pembimbing->save();

        $this->peserta = new User([
            'nama' => 'Rina Maharani',
            'email' => 'rina.peserta@magkom.test',
            'password' => bcrypt('Password123!'),
            'status_aktif' => true,
            'must_change_password' => false,
        ]);
        $this->peserta->role = RoleEnum::Peserta;
        $this->peserta->email_verified_at = now();
        $this->peserta->save();

        $this->penempatan = Penempatan::create([
            'peserta_id' => $this->peserta->id,
            'bidang_id' => $this->bidang->id,
            'pembimbing_id' => $this->pembimbing->id,
            'tgl_mulai' => now()->subDays(5)->toDateString(),
            'tgl_selesai' => now()->addDays(85)->toDateString(),
            'status' => StatusPenempatanEnum::Aktif,
            'sumber' => SumberPenempatanEnum::Online,
            'jalur' => JalurEnum::Umum,
        ]);
    }

    public function test_peserta_dapat_melihat_daftar_tugas_gaya_google_classroom(): void
    {
        Tugas::create([
            'penempatan_id' => $this->penempatan->id,
            'judul' => 'Pembuatan Mockup Dashboard Peserta',
            'deskripsi' => 'Rancang wireframe Figma dengan pola Google Classroom.',
            'kategori' => 'Desain Antarmuka',
            'kompleksitas' => KompleksitasEnum::Sedang,
            'deadline' => now()->addDays(3),
            'status' => StatusTugasEnum::Belum,
        ]);

        Tugas::create([
            'penempatan_id' => $this->penempatan->id,
            'judul' => 'Analisis Kebutuhan Sistem MagKom',
            'deskripsi' => 'Dokumentasikan use case dan business logic.',
            'kategori' => 'Analisis Sistem',
            'kompleksitas' => KompleksitasEnum::Tinggi,
            'deadline' => now()->subDay(),
            'status' => StatusTugasEnum::Proses,
        ]);

        $response = $this->actingAs($this->peserta)->get(route('peserta.tugas.index'));

        $response->assertOk();
        $response->assertSee('Tugas & Proyek Magang', false);
        $response->assertSee('Semua Tugas');
        $response->assertSee('Ditugaskan');
        $response->assertSee('Sedang Dikerjakan');
        $response->assertSee('Pembuatan Mockup Dashboard Peserta');
        $response->assertSee('Analisis Kebutuhan Sistem MagKom');
        $response->assertSee('Tenggat: '.now()->addDays(3)->translatedFormat('d M Y'));
    }

    public function test_peserta_dapat_melihat_detail_tugas_layout_dua_kolom(): void
    {
        $tugas = Tugas::create([
            'penempatan_id' => $this->penempatan->id,
            'judul' => 'Implementasi Modul Logbook Magang',
            'deskripsi' => 'Kerjakan modul logbook harian dengan blade dan vanilla CSS.',
            'kategori' => 'Pemrograman Web',
            'kompleksitas' => KompleksitasEnum::Sedang,
            'deadline' => now()->addDays(5),
            'status' => StatusTugasEnum::Belum,
        ]);

        $response = $this->actingAs($this->peserta)->get(route('peserta.tugas.show', $tugas));

        $response->assertOk();
        $response->assertSee('Implementasi Modul Logbook Magang');
        $response->assertSee('Tugas Anda');
        $response->assertSee('Panduan Pengerjaan');
        $response->assertSee('Serahkan / Kirim Progres', false);
        $response->assertSee('Budi Santoso, S.Kom');
    }

    public function test_peserta_dapat_mengirim_progres_tugas(): void
    {
        $tugas = Tugas::create([
            'penempatan_id' => $this->penempatan->id,
            'judul' => 'Slicing Desain Komponen',
            'deskripsi' => 'Kerjakan slicing CSS untuk halaman detail tugas.',
            'kategori' => 'Pemrograman Web',
            'kompleksitas' => KompleksitasEnum::Rendah,
            'deadline' => now()->addDays(4),
            'status' => StatusTugasEnum::Belum,
        ]);

        $file = UploadedFile::fake()->create('laporan-progres.pdf', 500, 'application/pdf');

        $response = $this->actingAs($this->peserta)->post(route('peserta.tugas.progres', $tugas), [
            'file' => $file,
            'link' => 'https://github.com/magkom/demo-progres',
            'catatan' => 'Berikut draf halaman detail tugas yang sudah disesuaikan.',
        ]);

        $response->assertRedirect(route('peserta.tugas.show', $tugas));
        $response->assertSessionHas('success');

        $tugas->refresh();
        $this->assertEquals(StatusTugasEnum::Proses, $tugas->status);

        $progres = ProgresLog::where('tugas_id', $tugas->id)->latest()->first();
        $this->assertNotNull($progres);
        $this->assertNotNull($progres->file);
        $this->assertEquals('https://github.com/magkom/demo-progres', $progres->link);
        $this->assertEquals('Berikut draf halaman detail tugas yang sudah disesuaikan.', $progres->catatan);

        // Verifikasi link muncul di halaman detail tugas peserta
        $viewResponse = $this->actingAs($this->peserta)->get(route('peserta.tugas.show', $tugas));
        $viewResponse->assertSee('Buka Tautan Tugas ↗');
        $viewResponse->assertSee('https://github.com/magkom/demo-progres');
    }

    public function test_peserta_dapat_mengirim_progres_tugas_hanya_dengan_link_tanpa_file(): void
    {
        $tugas = Tugas::create([
            'penempatan_id' => $this->penempatan->id,
            'judul' => 'Pembuatan Desain Mockup Figma',
            'deskripsi' => 'Kerjakan mockup desain halaman profil.',
            'kategori' => 'UI/UX Design',
            'kompleksitas' => KompleksitasEnum::Rendah,
            'deadline' => now()->addDays(3),
            'status' => StatusTugasEnum::Belum,
        ]);

        $response = $this->actingAs($this->peserta)->post(route('peserta.tugas.progres', $tugas), [
            'link' => 'https://www.figma.com/file/contoh-link-mockup',
            'catatan' => 'Tautan mockup UI/UX di Figma siap ditinjau mentor.',
        ]);

        $response->assertRedirect(route('peserta.tugas.show', $tugas));
        $response->assertSessionHas('success');

        $progres = ProgresLog::where('tugas_id', $tugas->id)->latest()->first();
        $this->assertNotNull($progres);
        $this->assertNull($progres->file);
        $this->assertEquals('https://www.figma.com/file/contoh-link-mockup', $progres->link);
    }

    public function test_peserta_dapat_melihat_timeline_feed_logbook(): void
    {
        Logbook::create([
            'penempatan_id' => $this->penempatan->id,
            'tanggal' => now()->subDay()->toDateString(),
            'kegiatan' => 'Menghadiri briefing harian bersama mentor di bidang Aptika.',
            'status' => StatusLogbookEnum::Terkirim,
        ]);

        Logbook::create([
            'penempatan_id' => $this->penempatan->id,
            'tanggal' => now()->toDateString(),
            'kegiatan' => 'Mengerjakan perbaikan antarmuka dashboard peserta.',
            'status' => StatusLogbookEnum::PerluRevisi,
            'catatan_pembimbing' => 'Mohon tambahkan rincian branch git yang dikerjakan.',
        ]);

        $response = $this->actingAs($this->peserta)->get(route('peserta.logbook.index'));

        $response->assertOk();
        $response->assertSee('Isi Logbook Mingguan');
        $response->assertSee('Riwayat Jurnal Mingguan');
        $response->assertSee('Progres Pelaporan Logbook Mingguan');
        $response->assertSee('Menghadiri briefing harian');
        $response->assertSee('Mengerjakan perbaikan antarmuka');
        $response->assertSee('Mohon tambahkan rincian branch git');
        $response->assertSee('Perlu Revisi');
        $response->assertSee('Terkirim');
    }

    public function test_peserta_dapat_menyimpan_dan_memperbarui_entri_logbook(): void
    {
        $responseStore = $this->actingAs($this->peserta)->post(route('peserta.logbook.store'), [
            'tanggal' => now()->toDateString(),
            'kegiatan' => 'Mengembangkan pengujian otomatis feature test portal peserta.',
            'link_laporan' => 'https://github.com/magkom/repo-magang',
        ]);

        $responseStore->assertRedirect(route('peserta.logbook.index'));
        $responseStore->assertSessionHas('success');

        $logbook = Logbook::where('penempatan_id', $this->penempatan->id)
            ->whereDate('tanggal', now()->toDateString())
            ->first();

        $this->assertNotNull($logbook);
        $this->assertEquals(StatusLogbookEnum::Terkirim, $logbook->status);

        // Uji Update Entri Logbook
        $responseUpdate = $this->actingAs($this->peserta)->put(route('peserta.logbook.update', $logbook), [
            'tanggal' => now()->toDateString(),
            'kegiatan' => 'Revisi deskripsi: Telah menyelesaikan 5 skenario test otomatis.',
            'link_laporan' => 'https://github.com/magkom/repo-magang/pull/1',
        ]);

        $responseUpdate->assertRedirect(route('peserta.logbook.index'));
        $responseUpdate->assertSessionHas('success');

        $logbook->refresh();
        $this->assertStringContainsString('Revisi deskripsi', $logbook->kegiatan);
    }

    public function test_peserta_dapat_mengisi_logbook_mingguan_dan_dicegah_duplikasi_minggu_sama(): void
    {
        // 1. Simpan logbook untuk Minggu ke-1
        $responseMinggu1 = $this->actingAs($this->peserta)->post(route('peserta.logbook.store'), [
            'minggu_ke' => 1,
            'tanggal' => now()->toDateString(),
            'kegiatan' => 'Menyelesaikan setup environment pengembangan dan instalasi tools.',
        ]);

        $responseMinggu1->assertRedirect(route('peserta.logbook.index'));
        $this->assertDatabaseHas('logbook', [
            'penempatan_id' => $this->penempatan->id,
            'minggu_ke' => 1,
        ]);

        // 2. Mencoba menyimpan logbook lagi untuk Minggu ke-1 -> Ditolak / Dilarang duplikat
        $responseDuplikat = $this->actingAs($this->peserta)->post(route('peserta.logbook.store'), [
            'minggu_ke' => 1,
            'tanggal' => now()->toDateString(),
            'kegiatan' => 'Mencoba mengisi kembali laporan untuk minggu pertama.',
        ]);

        $responseDuplikat->assertSessionHas('error');
    }
}
