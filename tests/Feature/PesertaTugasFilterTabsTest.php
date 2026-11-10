<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JalurEnum;
use App\Enums\KompleksitasEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\StatusTugasEnum;
use App\Enums\SumberPenempatanEnum;
use App\Models\Bidang;
use App\Models\Penempatan;
use App\Models\Tugas;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PesertaTugasFilterTabsTest extends TestCase
{
    use RefreshDatabase;

    protected User $peserta;

    protected User $pembimbing;

    protected Bidang $bidang;

    protected Penempatan $penempatan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bidang = Bidang::create([
            'nama_bidang' => 'Bidang Aplikasi Informatika (Aptika)',
            'deskripsi' => 'Pengembangan aplikasi dan layanan e-government',
            'aktif' => true,
        ]);

        $this->pembimbing = new User([
            'nama' => 'Budi Santoso, S.Kom',
            'email' => 'pembimbing.tab@magkom.test',
            'password' => bcrypt('Password123!'),
            'status_aktif' => true,
            'must_change_password' => false,
        ]);
        $this->pembimbing->role = RoleEnum::Pembimbing;
        $this->pembimbing->email_verified_at = now();
        $this->pembimbing->save();

        $this->peserta = new User([
            'nama' => 'Peserta Uji Tab',
            'email' => 'peserta.tab@magkom.test',
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

    public function test_peserta_melihat_tiga_tab_filter_semua_selesai_dan_lewat_tenggat(): void
    {
        // 1. Tugas Selesai
        Tugas::create([
            'penempatan_id' => $this->penempatan->id,
            'judul' => 'Analisis Kebutuhan Sistem MagKom',
            'deskripsi' => 'Riset kebutuhan modul portal.',
            'kategori' => 'Analisis & Riset',
            'kompleksitas' => KompleksitasEnum::Sedang,
            'deadline' => Carbon::today()->subDays(5),
            'status' => StatusTugasEnum::Selesai,
        ]);

        // 2. Tugas Aktif (Masa Depan)
        Tugas::create([
            'penempatan_id' => $this->penempatan->id,
            'judul' => 'Slicing & Integrasi Frontend Peserta',
            'deskripsi' => 'Pengerjaan antarmuka responsif.',
            'kategori' => 'Frontend Development',
            'kompleksitas' => KompleksitasEnum::Tinggi,
            'deadline' => Carbon::today()->addDays(7),
            'status' => StatusTugasEnum::Proses,
        ]);

        // 3. Tugas Terlambat / Lewat Tenggat
        Tugas::create([
            'penempatan_id' => $this->penempatan->id,
            'judul' => 'Uji Fungsional & Penyusunan Dokumen',
            'deskripsi' => 'Pengujian antarmuka dan laporan.',
            'kategori' => 'Testing & Laporan',
            'kompleksitas' => KompleksitasEnum::Rendah,
            'deadline' => Carbon::today()->subDays(2),
            'status' => StatusTugasEnum::Belum,
        ]);

        $response = $this->actingAs($this->peserta)->get(route('peserta.tugas.index'));

        $response->assertOk();

        // Verifikasi 3 Tab Utama
        $response->assertSee('Semua Tugas');
        $response->assertSee('Selesai');
        $response->assertSee('Lewat Tenggat');

        // Verifikasi Atribut Filter Tab
        $response->assertSee('data-filter="all"', false);
        $response->assertSee('data-filter="selesai"', false);
        $response->assertSee('data-filter="terlambat"', false);

        // Verifikasi Tombol Buka Tugas Sleman Action Button
        $response->assertSee('btn-tugas-action', false);
        $response->assertSee('Buka Tugas');

        // Verifikasi Data Atribut Kartu
        $response->assertSee('data-is-overdue="1"', false);
        $response->assertSee('data-status="selesai"', false);
    }

    public function test_peserta_json_response_memuat_meta_agregat_tugas(): void
    {
        Tugas::create([
            'penempatan_id' => $this->penempatan->id,
            'judul' => 'Tugas Uji Metrik 1',
            'deskripsi' => 'Deskripsi tugas uji.',
            'kategori' => 'Pengembangan',
            'kompleksitas' => KompleksitasEnum::Sedang,
            'deadline' => Carbon::today()->addDays(3),
            'status' => StatusTugasEnum::Belum,
        ]);

        $response = $this->actingAs($this->peserta)->getJson(route('peserta.tugas.index'));

        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'data',
            'meta' => [
                'totalSemua',
                'totalBelum',
                'totalProses',
                'totalSelesai',
                'totalTerlambat',
            ],
        ]);
        $response->assertJson([
            'success' => true,
            'meta' => [
                'totalSemua' => 1,
                'totalBelum' => 1,
                'totalProses' => 0,
                'totalSelesai' => 0,
                'totalTerlambat' => 0,
            ],
        ]);
    }
}
