<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JalurEnum;
use App\Enums\KompleksitasEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\StatusReviewProgresEnum;
use App\Enums\StatusTugasEnum;
use App\Enums\SumberPenempatanEnum;
use App\Models\Bidang;
use App\Models\Penempatan;
use App\Models\ProgresLog;
use App\Models\Tugas;
use App\Models\User;
use Database\Seeders\JenisDokumenSeeder;
use Database\Seeders\RubrikSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MultiLinkTugasTest extends TestCase
{
    use RefreshDatabase;

    protected User $pembimbing;

    protected User $peserta;

    protected Bidang $bidang;

    protected Penempatan $penempatan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(JenisDokumenSeeder::class);
        $this->seed(RubrikSeeder::class);

        $this->bidang = Bidang::create([
            'nama_bidang' => 'Bidang Aplikasi Informatika',
            'deskripsi' => 'Pengembangan aplikasi',
            'aktif' => true,
        ]);

        $this->pembimbing = new User([
            'nama' => 'Budi Pembimbing, S.Kom',
            'email' => 'pembimbing.multilink@magkom.test',
            'password' => bcrypt('Password123!'),
            'status_aktif' => true,
            'must_change_password' => false,
        ]);
        $this->pembimbing->role = RoleEnum::Pembimbing;
        $this->pembimbing->email_verified_at = now();
        $this->pembimbing->save();

        $this->peserta = new User([
            'nama' => 'Naufal Peserta',
            'email' => 'peserta.multilink@magkom.test',
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
            'tgl_mulai' => now()->subDays(10)->toDateString(),
            'tgl_selesai' => now()->addDays(80)->toDateString(),
            'status' => StatusPenempatanEnum::Aktif,
            'sumber' => SumberPenempatanEnum::Online,
            'jalur' => JalurEnum::Umum,
        ]);
    }

    public function test_pembimbing_can_create_tugas_with_multiple_links(): void
    {
        $links = [
            'https://github.com/magkom/sample-repo',
            'https://docs.google.com/document/d/sample123',
            'https://figma.com/file/sample456',
        ];

        $response = $this->actingAs($this->pembimbing)->post(route('pembimbing.tugas.store'), [
            'penempatan_ids' => [$this->penempatan->id],
            'judul' => 'Tugas Riset Multi Tautan',
            'deskripsi' => 'Silakan pelajari tautan-tautan acuan berikut.',
            'kategori' => 'Riset',
            'kompleksitas' => 'sedang',
            'deadline' => now()->addDays(7)->toDateString(),
            'link_referensi' => json_encode($links),
            'aksi_publikasi' => 'sekarang',
        ]);

        $response->assertRedirect(route('pembimbing.tugas.index'));

        $tugas = Tugas::where('penempatan_id', $this->penempatan->id)->latest('id')->first();
        $this->assertNotNull($tugas);
        $this->assertCount(3, $tugas->links);
        $this->assertEquals($links, $tugas->links);

        // Periksa tampilan peserta memuat ketiga tautan
        $viewPeserta = $this->actingAs($this->peserta)->get(route('peserta.tugas.show', $tugas));
        $viewPeserta->assertOk();
        foreach ($links as $link) {
            $viewPeserta->assertSee($link);
        }

        // Periksa tampilan review pembimbing memuat ketiga tautan
        $viewPembimbing = $this->actingAs($this->pembimbing)->get(route('pembimbing.tugas.show', $tugas));
        $viewPembimbing->assertOk();
        foreach ($links as $link) {
            $viewPembimbing->assertSee($link);
        }
    }

    public function test_peserta_can_submit_tugas_with_multiple_links(): void
    {
        $tugas = Tugas::create([
            'penempatan_id' => $this->penempatan->id,
            'judul' => 'Pembuatan Landing Page',
            'deskripsi' => 'Kumpulkan tautan github dan deployment demo.',
            'kategori' => 'Frontend',
            'kompleksitas' => KompleksitasEnum::Sedang,
            'deadline' => now()->addDays(5),
            'status' => StatusTugasEnum::Belum,
        ]);

        $submissionLinks = [
            'https://github.com/peserta/landing-page',
            'https://landing-page-demo.vercel.app',
        ];

        $response = $this->actingAs($this->peserta)->post(route('peserta.tugas.progres', $tugas), [
            'link' => json_encode($submissionLinks),
            'catatan' => 'Berikut repositori dan demo web tugas saya.',
        ]);

        $response->assertRedirect(route('peserta.tugas.show', $tugas));

        $progres = ProgresLog::where('tugas_id', $tugas->id)->latest('id')->first();
        $this->assertNotNull($progres);
        $this->assertCount(2, $progres->links);
        $this->assertEquals($submissionLinks, $progres->links);

        // Halaman peserta menampilkan link pengumpulan
        $showPeserta = $this->actingAs($this->peserta)->get(route('peserta.tugas.show', $tugas));
        $showPeserta->assertOk();
        foreach ($submissionLinks as $link) {
            $showPeserta->assertSee($link);
        }

        // Halaman pembimbing menampilkan seluruh link yang dikumpulkan
        $showPembimbing = $this->actingAs($this->pembimbing)->get(route('pembimbing.tugas.show', $tugas));
        $showPembimbing->assertOk();
        foreach ($submissionLinks as $link) {
            $showPembimbing->assertSee($link);
        }
    }

    public function test_peserta_can_resubmit_revision_with_multiple_new_links_preserving_previous_history(): void
    {
        $tugas = Tugas::create([
            'penempatan_id' => $this->penempatan->id,
            'judul' => 'Tugas API Backend',
            'deskripsi' => 'Kerjakan endpoint autentikasi.',
            'kategori' => 'Backend',
            'kompleksitas' => KompleksitasEnum::Sedang,
            'deadline' => now()->addDays(5),
            'status' => StatusTugasEnum::Proses,
        ]);

        // 1. Kiriman pertama yang ditandai Perlu Revisi oleh pembimbing
        $firstLog = ProgresLog::create([
            'tugas_id' => $tugas->id,
            'link' => 'https://github.com/peserta/api-v1-broken',
            'catatan' => 'Kiriman pertama',
            'tgl_lapor' => now()->subDays(2),
            'status_review' => StatusReviewProgresEnum::PerluRevisi,
            'feedback' => 'Tolong perbaiki endpoint register dan tambahkan dokumentasi postman.',
        ]);

        // Peserta melihat kiriman pertama yang berstatus perlu revisi
        $viewInitial = $this->actingAs($this->peserta)->get(route('peserta.tugas.show', $tugas));
        $viewInitial->assertOk();
        $viewInitial->assertSee('Perlu Revisi');
        $viewInitial->assertSee('https://github.com/peserta/api-v1-broken');
        $viewInitial->assertSee('Tolong perbaiki endpoint register');

        // 2. Peserta mengunggah revisi dengan dua link baru
        $revisionLinks = [
            'https://github.com/peserta/api-v2-fixed',
            'https://documenter.getpostman.com/view/sample-docs',
        ];

        $resubmitResponse = $this->actingAs($this->peserta)->post(route('peserta.tugas.progres', $tugas), [
            'link' => json_encode($revisionLinks),
            'catatan' => 'Sudah diperbaiki dan ditambahkan dokumentasi postman.',
        ]);

        $resubmitResponse->assertRedirect(route('peserta.tugas.show', $tugas));

        // Verifikasi log progres baru tercipta dan log lama tetap ada
        $allLogs = ProgresLog::where('tugas_id', $tugas->id)->orderBy('id')->get();
        $this->assertCount(2, $allLogs);

        $newLog = $allLogs->last();
        $this->assertEquals(StatusReviewProgresEnum::Menunggu, $newLog->status_review);
        $this->assertEquals($revisionLinks, $newLog->links);

        // Halaman peserta sekarang menampilkan link revisi terbaru
        $viewAfter = $this->actingAs($this->peserta)->get(route('peserta.tugas.show', $tugas));
        $viewAfter->assertOk();
        $viewAfter->assertSee('https://github.com/peserta/api-v2-fixed');
        $viewAfter->assertSee('https://documenter.getpostman.com/view/sample-docs');
    }

    public function test_validation_rejects_insecure_url_inside_multiple_links(): void
    {
        $invalidLinks = [
            'https://github.com/valid/repo',
            'http://insecure-http-not-allowed.test', // non-https
        ];

        $response = $this->actingAs($this->pembimbing)->post(route('pembimbing.tugas.store'), [
            'penempatan_ids' => [$this->penempatan->id],
            'judul' => 'Tugas Insecure Link Test',
            'deskripsi' => 'Deskripsi tugas',
            'kategori' => 'Testing',
            'kompleksitas' => 'sedang',
            'deadline' => now()->addDays(7)->toDateString(),
            'link_referensi' => json_encode($invalidLinks),
            'aksi_publikasi' => 'sekarang',
        ]);

        $response->assertSessionHasErrors(['link_referensi']);
    }
}
