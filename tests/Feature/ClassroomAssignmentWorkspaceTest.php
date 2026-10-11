<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JalurEnum;
use App\Enums\JenjangEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\SumberPenempatanEnum;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\Penempatan;
use App\Models\PesertaProfil;
use App\Models\Tugas;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ClassroomAssignmentWorkspaceTest extends TestCase
{
    use RefreshDatabase;

    protected User $pembimbing;
    protected User $peserta;
    protected Penempatan $penempatan;
    protected Bidang $bidang;

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

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('private');

        $this->bidang = Bidang::create(['nama_bidang' => 'Aplikasi Informatika']);
        $jurusan = Jurusan::create([
            'nama_jurusan' => 'Sistem Informasi',
            'kategori' => 'mahasiswa',
            'aktif' => true,
        ]);
        $bulan = BulanMagang::create([
            'bulan_mulai' => now()->startOfMonth(),
            'tgl_buka' => now()->subDays(5)->toDateString(),
            'tgl_tutup' => now()->addDays(20)->toDateString(),
            'aktif' => true,
        ]);

        $this->pembimbing = $this->createUser(
            'Pembimbing Aptika',
            'pembimbing.aptika@magkom.go.id',
            RoleEnum::Pembimbing,
            $this->bidang->id
        );

        $this->peserta = $this->createUser(
            'Mentee Satu',
            'mentee.satu@magkom.go.id',
            RoleEnum::Peserta
        );

        PesertaProfil::create([
            'user_id' => $this->peserta->id,
            'jurusan_id' => $jurusan->id,
            'institusi' => 'Universitas Amikom Yogyakarta',
            'jenjang' => JenjangEnum::S1,
            'nim_nisn' => '20110001',
            'no_hp' => '081234567890',
        ]);

        $this->penempatan = Penempatan::create([
            'peserta_id' => $this->peserta->id,
            'pembimbing_id' => $this->pembimbing->id,
            'bidang_id' => $this->bidang->id,
            'bulan_magang_id' => $bulan->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'tgl_mulai' => Carbon::today()->subDays(5),
            'tgl_selesai' => Carbon::today()->addDays(25),
            'status' => StatusPenempatanEnum::Aktif,
        ]);
    }

    public function test_pembimbing_bisa_mengakses_halaman_classroom_workspace_create(): void
    {
        $response = $this->actingAs($this->pembimbing)
            ->get(route('pembimbing.tugas.create'));

        $response->assertOk();
        $response->assertSee('Buat Tugas Baru');
        $response->assertSee('Classroom Workspace');
        $response->assertSee('formBuatTugasClassroom');
        $response->assertSee('Judul Penugasan');
        $response->assertSee('Materi Acuan');
        $response->assertSee('Parameter Penugasan');
        $response->assertSee('Tugaskan Sekarang');
        $response->assertSee('Mentee Satu');
    }

    public function test_halaman_create_menerima_preselected_penempatan_id_via_query_string(): void
    {
        $response = $this->actingAs($this->pembimbing)
            ->get(route('pembimbing.tugas.create', ['penempatan_id' => $this->penempatan->id]));

        $response->assertOk();
        $response->assertSee('value="' . $this->penempatan->id . '"', false);
        $response->assertSee('selected', false);
    }

    public function test_pembimbing_bisa_submit_tugas_dari_halaman_classroom_workspace(): void
    {
        $file = UploadedFile::fake()->create('panduan_slicing.pdf', 800, 'application/pdf');

        $response = $this->actingAs($this->pembimbing)
            ->post(route('pembimbing.tugas.store'), [
                'penempatan_id' => $this->penempatan->id,
                'judul' => 'Slicing UI Dashboard Antrean Layanan',
                'deskripsi' => 'Kerjakan halaman antrean mengacu pada design system dan dokumen terlampir.',
                'link_referensi' => 'https://figma.com/file/magkom-mockup-sleman',
                'file_lampiran' => $file,
                'kategori' => 'Frontend Web',
                'kompleksitas' => 'sedang',
                'deadline' => Carbon::today()->addDays(10)->toDateString(),
            ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('pembimbing.tugas.index'));
        $response->assertSessionHas('success');

        $tugas = Tugas::where('judul', 'Slicing UI Dashboard Antrean Layanan')->first();
        $this->assertNotNull($tugas);
        $this->assertEquals('https://figma.com/file/magkom-mockup-sleman', $tugas->link_referensi);
        $this->assertEquals('panduan_slicing.pdf', $tugas->nama_file_lampiran);
        $this->assertEquals('pdf', $tugas->ekstensi_lampiran);
        $this->assertNotNull($tugas->file_lampiran);

        Storage::disk('private')->assertExists($tugas->file_lampiran);
    }

    public function test_peserta_ditolak_mengakses_halaman_tugas_create(): void
    {
        $response = $this->actingAs($this->peserta)
            ->get(route('pembimbing.tugas.create'));

        $response->assertForbidden();
    }
}
