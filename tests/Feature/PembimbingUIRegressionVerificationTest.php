<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JalurEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\StatusTugasEnum;
use App\Enums\SumberPenempatanEnum;
use App\Models\Bidang;
use App\Models\Jurusan;
use App\Models\Penempatan;
use App\Models\PesertaProfil;
use App\Models\Tugas;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PembimbingUIRegressionVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected User $pembimbing;

    protected User $peserta;

    protected Bidang $bidang;

    protected Jurusan $jurusan;

    protected Penempatan $penempatan;

    protected Tugas $tugas;

    private function createUser(string $nama, string $email, RoleEnum $role, ?int $bidangId = null): User
    {
        $user = new User;
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

    protected function setUp(): void
    {
        parent::setUp();

        $this->bidang = Bidang::create(['nama_bidang' => 'Bidang Komunikasi & Informatika']);
        $this->jurusan = Jurusan::create([
            'nama_jurusan' => 'Informatika Medis',
            'kategori' => 'mahasiswa',
            'aktif' => true,
        ]);

        $this->pembimbing = $this->createUser(
            'Budi UI Verifier, S.Kom',
            'verifier@magkom.go.id',
            RoleEnum::Pembimbing,
            $this->bidang->id
        );

        $this->peserta = $this->createUser(
            'Mentee UI Test',
            'mentee.ui@magkom.go.id',
            RoleEnum::Peserta
        );

        PesertaProfil::create([
            'user_id' => $this->peserta->id,
            'institusi' => 'Universitas Gadjah Mada',
            'nim_nisn' => '22/12345/TK/54321',
            'jenjang' => 's1',
            'jurusan_id' => $this->jurusan->id,
            'no_hp' => '081200001111',
        ]);

        $this->penempatan = Penempatan::create([
            'peserta_id' => $this->peserta->id,
            'bidang_id' => $this->bidang->id,
            'pembimbing_id' => $this->pembimbing->id,
            'tgl_mulai' => Carbon::today()->subDays(10),
            'tgl_selesai' => Carbon::today()->addDays(50),
            'status' => StatusPenempatanEnum::Aktif,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
        ]);

        $this->tugas = Tugas::create([
            'penempatan_id' => $this->penempatan->id,
            'judul' => 'Penugasan UI Verification Master',
            'deskripsi' => 'Instruksi pengerjaan tugas verifikasi visual',
            'kategori' => 'Front-End',
            'kompleksitas' => 'sedang',
            'deadline' => Carbon::today()->addDays(14),
            'status' => StatusTugasEnum::Proses,
            'status_publikasi' => 'dipublikasikan',
        ]);
    }

    public function test_pembimbing_tugas_index_renders_all_classroom_ui_elements(): void
    {
        $response = $this->actingAs($this->pembimbing)
            ->get(route('pembimbing.tugas.index'));

        $response->assertOk();

        // Banner classroom
        $response->assertSee('Tugas &amp; Progres Magang', false);
        $response->assertSee('Mentee Aktif Bimbingan');
        $response->assertSee('Buat Tugas Baru');

        // Tile stats
        $response->assertSee('Total Tugas');
        $response->assertSee('Ditugaskan');
        $response->assertSee('Selesai');
        $response->assertSee('Perlu Evaluasi');

        // Daftar mentee
        $response->assertSee('Mentee UI Test');
        $response->assertSee('Penugasan UI Verification Master');
    }

    public function test_pembimbing_tugas_create_renders_classroom_workspace_form(): void
    {
        $response = $this->actingAs($this->pembimbing)
            ->get(route('pembimbing.tugas.create'));

        $response->assertOk();
        $response->assertSee('Buat Tugas Baru');
        $response->assertSee('Tugaskan Sekarang');
        $response->assertSee('Judul Penugasan');
        $response->assertSee('Mentee UI Test');
    }

    public function test_pembimbing_tugas_show_renders_detail_and_sibling_navigation(): void
    {
        $response = $this->actingAs($this->pembimbing)
            ->get(route('pembimbing.tugas.show', $this->tugas->id));

        $response->assertOk();
        $response->assertSee('Penugasan UI Verification Master');
        $response->assertSee('Instruksi pengerjaan tugas verifikasi visual');
        $response->assertSee('Mentee UI Test');
    }

    public function test_pembimbing_tugas_edit_renders_edit_workspace_form(): void
    {
        $response = $this->actingAs($this->pembimbing)
            ->get(route('pembimbing.tugas.edit', $this->tugas->id));

        $response->assertOk();
        $response->assertSee('Edit Tugas Mentee');
        $response->assertSee('Simpan Perubahan');
        $response->assertSee('Penugasan UI Verification Master');
        $response->assertSee('Instruksi pengerjaan tugas verifikasi visual');
    }

    public function test_pembimbing_laporan_kinerja_index_renders_gentelella_tiles_and_export_popover(): void
    {
        $response = $this->actingAs($this->pembimbing)
            ->get(route('pembimbing.laporan-kinerja.index'));

        $response->assertOk();
        $response->assertSee('Laporan Peserta');
        $response->assertSee('Data Penempatan Peserta');
        $response->assertSee('Ekspor Rekap');
        $response->assertSee('admExportPopover');
        $response->assertSee('Mentee UI Test');
        $response->assertSee('Universitas Gadjah Mada');
    }

    public function test_pembimbing_laporan_kinerja_cetak_renders_printable_official_report(): void
    {
        $response = $this->actingAs($this->pembimbing)
            ->get(route('pembimbing.laporan-kinerja.cetak'));

        $response->assertOk();
        $response->assertSee('Pemerintah Kabupaten Sleman');
        $response->assertSee('Dinas Komunikasi dan Informatika');
        $response->assertSee('Lembar Rekapitulasi Pembimbingan Magang Mahasiswa / Siswa (LPJ)');
        $response->assertSee('Mentee UI Test');
        $response->assertSee('Budi UI Verifier, S.Kom');
    }
}
