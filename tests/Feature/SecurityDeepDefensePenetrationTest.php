<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JalurEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\StatusTugasEnum;
use App\Enums\SumberPenempatanEnum;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\Kuota;
use App\Models\Pendaftaran;
use App\Models\Penempatan;
use App\Models\Tugas;
use App\Models\User;
use Database\Seeders\PengaturanSeeder;
use Database\Seeders\PeriodeKuotaSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityDeepDefensePenetrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PengaturanSeeder::class);
        $this->seed(PeriodeKuotaSeeder::class);
    }

    /**
     * OWASP A08: Mass Assignment Attack Prevention
     * Attacker attempts to escalate role to 'superadmin' or set 'status_aktif' during registration.
     */
    public function test_mass_assignment_cannot_override_protected_role_or_status(): void
    {
        $response = $this->post(route('register'), [
            'nama' => 'Attacker Account',
            'email' => 'attacker@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role' => 'superadmin',
            'status_aktif' => true,
        ]);

        $createdUser = User::where('email', 'attacker@example.com')->first();
        $this->assertNotNull($createdUser);
        // Role HARUS selalu Peserta, bukan SuperAdmin!
        $this->assertEquals(RoleEnum::Peserta, $createdUser->role);
    }

    /**
     * OWASP A07: Session Invalidation on Logout
     * Attacker tries to reuse session cookie after logout.
     */
    public function test_session_is_completely_invalidated_upon_logout(): void
    {
        $user = User::factory()->create([
            'role' => RoleEnum::Peserta,
            'status_aktif' => true,
        ]);

        $this->actingAs($user);
        $sessionIdBeforeLogout = session()->getId();

        $response = $this->post(route('logout'));
        $response->assertRedirect('/');

        $this->assertGuest();
        $this->assertNotEquals($sessionIdBeforeLogout, session()->getId());
    }

    /**
     * OWASP A01: Cross-Tenant Mentor IDOR Isolation
     * Pembimbing A is strictly forbidden from viewing, updating, or reviewing tasks belonging to Pembimbing B's mentee.
     */
    public function test_mentor_cannot_tamper_with_other_mentors_tasks_or_logbooks(): void
    {
        $bidang = Bidang::create(['nama_bidang' => 'Informatika', 'aktif' => true]);
        $jurusan = Jurusan::create(['nama_jurusan' => 'Teknik Komputer', 'kategori' => 'siswa', 'aktif' => true]);
        $bulan = BulanMagang::first() ?? BulanMagang::create([
            'nama_bulan' => 'Nov 2026',
            'bulan_mulai' => now()->startOfMonth(),
            'tahun' => 2026,
            'bulan' => 11,
            'aktif' => true,
        ]);

        $kuota = Kuota::create([
            'bidang_id' => $bidang->id,
            'jurusan_id' => $jurusan->id,
            'bulan_magang_id' => $bulan->id,
            'jumlah' => 10,
            'terisi' => 2,
            'aktif' => true,
        ]);

        $mentorA = User::factory()->create(['role' => RoleEnum::Pembimbing, 'status_aktif' => true]);
        $mentorB = User::factory()->create(['role' => RoleEnum::Pembimbing, 'status_aktif' => true]);

        $menteeB = User::factory()->create(['role' => RoleEnum::Peserta, 'status_aktif' => true]);

        $pendaftaranB = Pendaftaran::create([
            'user_id' => $menteeB->id,
            'kuota_id' => $kuota->id,
            'bulan_magang_id' => $bulan->id,
            'durasi_bulan' => 3,
            'status' => 'diterima',
            'no_pendaftaran' => 'REG-MENTEE-B',
        ]);

        $penempatanB = Penempatan::create([
            'peserta_id' => $menteeB->id,
            'pembimbing_id' => $mentorB->id,
            'pendaftaran_id' => $pendaftaranB->id,
            'bidang_id' => $bidang->id,
            'kuota_id' => $kuota->id,
            'tgl_mulai' => now()->subDays(5),
            'tgl_selesai' => now()->addDays(25),
            'status' => StatusPenempatanEnum::Aktif,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
        ]);

        $tugasB = Tugas::create([
            'penempatan_id' => $penempatanB->id,
            'judul' => 'Tugas Rahasia Tim B',
            'deskripsi' => 'Deskripsi tugas internal tim B',
            'kategori' => 'Teknis',
            'kompleksitas' => 'sedang',
            'deadline' => now()->addDays(7)->toDateString(),
            'status' => StatusTugasEnum::Belum,
        ]);

        // Mentor A mencoba mengakses detail tugas Mentee B -> HARUS 403 Forbidden
        $this->actingAs($mentorA)
            ->get(route('pembimbing.tugas.show', $tugasB))
            ->assertForbidden();

        // Mentor A mencoba mengubah tugas Mentee B -> HARUS 403 Forbidden
        $this->actingAs($mentorA)
            ->put(route('pembimbing.tugas.update', $tugasB), [
                'judul' => 'Tugas Diacak Oleh Mentor A',
                'deskripsi' => 'Perubahan ilegal',
                'tenggat' => now()->addDays(14)->toDateString(),
            ])
            ->assertForbidden();
    }

    /**
     * OWASP A01: State Access Control - Peserta non-aktif / belum diterima dilarang akses fitur magang aktif.
     */
    public function test_participant_without_accepted_placement_cannot_access_logbook_or_tasks(): void
    {
        $unacceptedParticipant = User::factory()->create([
            'role' => RoleEnum::Peserta,
            'status_aktif' => true,
        ]);

        // Akses via browser (HTML) dialihkan ke dashboard peserta dengan notifikasi peringatan
        $this->actingAs($unacceptedParticipant)
            ->get(route('peserta.logbook.index'))
            ->assertRedirect(route('peserta.dashboard'));

        $this->actingAs($unacceptedParticipant)
            ->get(route('peserta.tugas.index'))
            ->assertRedirect(route('peserta.dashboard'));

        // Akses via API / AJAX (JSON) secara ketat diblokir dengan 403 Forbidden
        $this->actingAs($unacceptedParticipant)
            ->getJson(route('peserta.logbook.index'))
            ->assertForbidden();

        $this->actingAs($unacceptedParticipant)
            ->getJson(route('peserta.tugas.index'))
            ->assertForbidden();
    }
}
