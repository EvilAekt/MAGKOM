<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\StatusLogbookEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\StatusReviewProgresEnum;
use App\Enums\StatusTugasEnum;
use App\Models\AgendaBimbingan;
use App\Models\KelompokMagang;
use App\Models\Logbook;
use App\Models\Penempatan;
use App\Models\ProgresLog;
use App\Models\Tugas;
use App\Models\User;
use Database\Seeders\PesertaSelesaiMagangDemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PesertaSelesaiMagangSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_creates_complete_intern_history_and_awaits_certificate(): void
    {
        $this->seed(PesertaSelesaiMagangDemoSeeder::class);

        // 1. Verifikasi Peserta Individu: Rian Pratama
        $rian = User::where('email', 'rian.selesai@magkom.test')->first();
        $this->assertNotNull($rian);
        $this->assertTrue(Hash::check('Password123!', $rian->password));
        $this->assertEquals('Rian Pratama', $rian->nama);

        $penempatanRian = Penempatan::where('peserta_id', $rian->id)->latest('id')->first();
        $this->assertNotNull($penempatanRian);
        $this->assertEquals(StatusPenempatanEnum::Selesai, $penempatanRian->status);
        $this->assertNull($penempatanRian->file_surat_selesai);

        // Verifikasi tugas Rian: 4 tugas selesai dengan progres log diterima
        $tugasRian = Tugas::where('penempatan_id', $penempatanRian->id)->get();
        $this->assertCount(4, $tugasRian);
        foreach ($tugasRian as $tugas) {
            $this->assertEquals(StatusTugasEnum::Selesai, $tugas->status);
            $this->assertEquals('dipublikasikan', $tugas->status_publikasi);

            $progres = ProgresLog::where('tugas_id', $tugas->id)->where('user_id', $rian->id)->first();
            $this->assertNotNull($progres);
            $this->assertEquals(StatusReviewProgresEnum::Diterima, $progres->status_review);
            $this->assertNotEmpty($progres->feedback);
        }

        // Verifikasi logbook Rian: 12 minggu terisi dan disetujui
        $logbookRian = Logbook::where('penempatan_id', $penempatanRian->id)->get();
        $this->assertCount(12, $logbookRian);
        foreach ($logbookRian as $lb) {
            $this->assertEquals(StatusLogbookEnum::Disetujui, $lb->status);
            $this->assertNotEmpty($lb->catatan_pembimbing);
        }

        // Verifikasi agenda bimbingan Rian: 3 sesi selesai
        $agendaRian = AgendaBimbingan::where('penempatan_id', $penempatanRian->id)->get();
        $this->assertCount(3, $agendaRian);

        // 2. Verifikasi Tim / Kelompok: Tim Cyber Security & CSIRT Sleman
        $kelompok = KelompokMagang::where('nama_kelompok', 'Tim Cyber Security & CSIRT Sleman')->first();
        $this->assertNotNull($kelompok);

        $fajar = User::where('email', 'fajar.ketua@magkom.test')->first();
        $siti = User::where('email', 'siti.anggota@magkom.test')->first();
        $bagas = User::where('email', 'bagas.anggota@magkom.test')->first();
        $this->assertNotNull($fajar);
        $this->assertNotNull($siti);
        $this->assertNotNull($bagas);

        // Verifikasi tugas kelompok: 2 project bersama di-assign ke ke-3 anggota
        $tugasKelompok = Tugas::where('kelompok_magang_id', $kelompok->id)->get();
        $this->assertCount(2, $tugasKelompok);
        foreach ($tugasKelompok as $tk) {
            $this->assertEquals(StatusTugasEnum::Selesai, $tk->status);
            $this->assertEquals('kelompok', $tk->tipe);
            $this->assertCount(3, $tk->pesertaDitugaskan);
            $this->assertTrue($tk->pesertaDitugaskan->contains('id', $fajar->id));
            $this->assertTrue($tk->pesertaDitugaskan->contains('id', $siti->id));
            $this->assertTrue($tk->pesertaDitugaskan->contains('id', $bagas->id));
        }

        // 3. Verifikasi Halaman Peserta: Dashboard, Tugas, Logbook, Selesai Magang
        // Rian (Individu)
        $respDashboard = $this->actingAs($rian)->get(route('peserta.dashboard'));
        $respDashboard->assertOk();

        $respTugas = $this->actingAs($rian)->get(route('peserta.tugas.index'));
        $respTugas->assertOk();
        $respTugas->assertSee('Analisis Kebutuhan Sistem');

        $respLogbook = $this->actingAs($rian)->get(route('peserta.logbook.index'));
        $respLogbook->assertOk();

        $respSelesai = $this->actingAs($rian)->get(route('peserta.selesai-magang.index'));
        $respSelesai->assertOk();
        $respSelesai->assertSee('Dokumen Sedang Dalam Proses Penerbitan');

        // Fajar (Ketua Tim)
        $respTugasTim = $this->actingAs($fajar)->get(route('peserta.tugas.index'));
        $respTugasTim->assertOk();
        $respTugasTim->assertSee('Vulnerability Assessment');

        $respSelesaiTim = $this->actingAs($fajar)->get(route('peserta.selesai-magang.index'));
        $respSelesaiTim->assertOk();
        $respSelesaiTim->assertSee('Dokumen Sedang Dalam Proses Penerbitan');
    }
}
