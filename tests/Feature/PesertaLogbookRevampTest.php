<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JalurEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusLogbookEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\SumberPenempatanEnum;
use App\Models\Bidang;
use App\Models\Logbook;
use App\Models\Penempatan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PesertaLogbookRevampTest extends TestCase
{
    use RefreshDatabase;

    protected User $peserta;

    protected User $pembimbing;

    protected Bidang $bidang;

    protected Penempatan $penempatan;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('private');

        $this->bidang = Bidang::create([
            'nama_bidang' => 'Bidang Aplikasi Informatika (Aptika)',
            'deskripsi' => 'Pengembangan aplikasi dan layanan e-government',
            'aktif' => true,
        ]);

        $this->pembimbing = new User([
            'nama' => 'Budi Santoso, S.Kom',
            'email' => 'pembimbing.logbook@magkom.test',
            'password' => bcrypt('Password123!'),
            'status_aktif' => true,
            'must_change_password' => false,
        ]);
        $this->pembimbing->role = RoleEnum::Pembimbing;
        $this->pembimbing->email_verified_at = now();
        $this->pembimbing->save();

        $this->peserta = new User([
            'nama' => 'Rina Peserta Logbook',
            'email' => 'rina.logbook@magkom.test',
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
            'tgl_mulai' => now()->subDays(14)->toDateString(),
            'tgl_selesai' => now()->addDays(76)->toDateString(),
            'status' => StatusPenempatanEnum::Aktif,
            'sumber' => SumberPenempatanEnum::Online,
            'jalur' => JalurEnum::Umum,
        ]);
    }

    public function test_peserta_melihat_halaman_logbook_dengan_riwayat_dan_modal_compact(): void
    {
        Logbook::create([
            'penempatan_id' => $this->penempatan->id,
            'minggu_ke' => 1,
            'tanggal' => now()->subDays(7),
            'kegiatan' => 'Orientasi lingkungan kerja dinas dan persiapan integrasi sistem.',
            'status' => StatusLogbookEnum::Disetujui,
        ]);

        $response = $this->actingAs($this->peserta)->get(route('peserta.logbook.index'));

        $response->assertOk();

        // 1. Verifikasi judul dan tombol riwayat utama
        $response->assertSee('Logbook & Jurnal Mingguan Peserta');
        $response->assertSee('Riwayat Jurnal Mingguan');
        $response->assertSee('+ Isi Logbook Mingguan');

        // 2. Verifikasi tombol Buka/Tutup Semua telah dihapus
        $response->assertDontSee('Buka Semua');
        $response->assertDontSee('Tutup Semua');

        // 3. Verifikasi tombol duplikat di header page title tidak ada
        $response->assertDontSee('<span>+ Isi Logbook</span>', false);

        // 4. Verifikasi modal logbook dan input langsung lampiran (tanpa sub-modal bertumpuk / dropdown)
        $response->assertSee('modalTambahLogbook', false);
        $response->assertSee('modalEditLogbook', false);
        $response->assertSee('tambah_file_laporan', false);
        $response->assertSee('tambah_link_laporan', false);
        $response->assertSee('edit_file_laporan', false);
        $response->assertSee('edit_link_laporan', false);
        $response->assertDontSee('modalLogbookLink', false);
        $response->assertDontSee('modalLogbookFile', false);
        $response->assertDontSee('btnTambahToggleDropdown', false);
        $response->assertDontSee('btnEditToggleDropdown', false);

        // 5. Verifikasi box panduan tips panjang di modal telah dihapus
        $response->assertDontSee('Panduan Penulisan Logbook Mingguan:', false);
    }

    public function test_peserta_dapat_menyimpan_logbook_dengan_file_dan_link(): void
    {
        $file = UploadedFile::fake()->create('laporan_minggu_2.pdf', 500, 'application/pdf');

        $response = $this->actingAs($this->peserta)->post(route('peserta.logbook.store'), [
            'minggu_ke' => 2,
            'tanggal' => now()->toDateString(),
            'kegiatan' => 'Mengerjakan perbaikan frontend dan dokumentasi API.',
            'file_laporan' => $file,
            'link_laporan' => 'https://github.com/magkom/demo-repo',
        ]);

        $response->assertRedirect(route('peserta.logbook.index'));

        $this->assertDatabaseHas('logbook', [
            'penempatan_id' => $this->penempatan->id,
            'minggu_ke' => 2,
            'status' => StatusLogbookEnum::Terkirim->value,
            'link_laporan' => 'https://github.com/magkom/demo-repo',
        ]);
    }
}
