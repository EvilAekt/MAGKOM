<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JalurEnum;
use App\Enums\JenjangEnum;
use App\Enums\KompleksitasEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\StatusReviewProgresEnum;
use App\Enums\StatusTugasEnum;
use App\Enums\SumberPenempatanEnum;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\KelompokAnggota;
use App\Models\KelompokMagang;
use App\Models\Kuota;
use App\Models\Pendaftaran;
use App\Models\Penempatan;
use App\Models\PesertaProfil;
use App\Models\ProgresLog;
use App\Models\Tugas;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TugasTimDanIndividuTest extends TestCase
{
    use RefreshDatabase;

    protected User $pembimbing;

    protected User $pembimbingLain;

    protected Bidang $bidang;

    protected KelompokMagang $kelompok;

    protected User $ketua;

    protected User $anggota1;

    protected User $anggota2;

    protected User $pesertaLuar;

    protected Penempatan $penempatanKetua;

    protected Penempatan $penempatanAnggota1;

    protected Penempatan $penempatanAnggota2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bidang = Bidang::create([
            'nama_bidang' => 'Aplikasi Informatika & E-Government',
            'kode' => 'EGOV',
            'kuota' => 20,
        ]);

        $this->pembimbing = User::factory()->create([
            'role' => RoleEnum::Pembimbing->value,
            'bidang_id' => $this->bidang->id,
            'nama' => 'Budi Pembimbing',
            'email' => 'pembimbing@magkom.test',
        ]);

        $this->pembimbingLain = User::factory()->create([
            'role' => RoleEnum::Pembimbing->value,
            'bidang_id' => $this->bidang->id,
            'nama' => 'Pembimbing Lain',
            'email' => 'pembimbing-lain@magkom.test',
        ]);

        $jurusan = Jurusan::create([
            'nama_jurusan' => 'Administrasi Perkantoran',
            'kategori' => 'mahasiswa',
            'aktif' => true,
        ]);

        $bulanMagang = BulanMagang::create([
            'bulan_mulai' => now()->startOfMonth()->toDateString(),
            'tgl_buka' => '2026-01-01',
            'tgl_tutup' => '2026-12-31',
            'aktif' => true,
        ]);

        $this->ketua = User::factory()->create(['role' => RoleEnum::Peserta->value, 'nama' => 'Nadia Kusuma', 'email' => 'nadia@magkom.test']);
        PesertaProfil::create(['user_id' => $this->ketua->id, 'jurusan_id' => $jurusan->id, 'institusi' => 'UNY', 'nim_nisn' => '2001', 'jenjang' => JenjangEnum::S1, 'no_hp' => '08120000001']);

        $this->anggota1 = User::factory()->create(['role' => RoleEnum::Peserta->value, 'nama' => 'Rizky Ramadhan', 'email' => 'rizky@magkom.test']);
        PesertaProfil::create(['user_id' => $this->anggota1->id, 'jurusan_id' => $jurusan->id, 'institusi' => 'UNY', 'nim_nisn' => '2002', 'jenjang' => JenjangEnum::S1, 'no_hp' => '08120000002']);

        $this->anggota2 = User::factory()->create(['role' => RoleEnum::Peserta->value, 'nama' => 'Siti Aisyah', 'email' => 'siti@magkom.test']);
        PesertaProfil::create(['user_id' => $this->anggota2->id, 'jurusan_id' => $jurusan->id, 'institusi' => 'UNY', 'nim_nisn' => '2003', 'jenjang' => JenjangEnum::S1, 'no_hp' => '08120000003']);

        $this->pesertaLuar = User::factory()->create(['role' => RoleEnum::Peserta->value, 'nama' => 'Peserta Luar Tim', 'email' => 'luar@magkom.test']);
        PesertaProfil::create(['user_id' => $this->pesertaLuar->id, 'jurusan_id' => $jurusan->id, 'institusi' => 'UGM', 'nim_nisn' => '2099', 'jenjang' => JenjangEnum::S1, 'no_hp' => '08120000099']);

        $this->kelompok = KelompokMagang::create([
            'nama_kelompok' => 'Tim Digitalisasi Layanan Sleman',
            'ketua_user_id' => $this->ketua->id,
        ]);

        KelompokAnggota::create(['kelompok_magang_id' => $this->kelompok->id, 'user_id' => $this->anggota1->id, 'nama' => $this->anggota1->nama, 'email' => $this->anggota1->email]);
        KelompokAnggota::create(['kelompok_magang_id' => $this->kelompok->id, 'user_id' => $this->anggota2->id, 'nama' => $this->anggota2->nama, 'email' => $this->anggota2->email]);

        $kuota = Kuota::create([
            'bulan_magang_id' => $bulanMagang->id,
            'jurusan_id' => $jurusan->id,
            'jumlah' => 10,
            'terisi' => 3,
            'aktif' => true,
        ]);

        $pendaftaran = Pendaftaran::create([
            'user_id' => $this->ketua->id,
            'bidang_id' => $this->bidang->id,
            'bulan_magang_id' => $bulanMagang->id,
            'kuota_id' => $kuota->id,
            'kelompok_magang_id' => $this->kelompok->id,
            'durasi_bulan' => 3,
            'tipe_pendaftaran' => 'kelompok',
            'status' => 'diterima',
            'tgl_mulai' => now()->subDays(10)->toDateString(),
            'tgl_selesai' => now()->addMonths(2)->toDateString(),
        ]);

        $tglMulai = now()->subDays(10)->toDateString();
        $tglSelesai = now()->addMonths(2)->toDateString();

        $this->penempatanKetua = Penempatan::create([
            'pendaftaran_id' => $pendaftaran->id,
            'peserta_id' => $this->ketua->id,
            'bidang_id' => $this->bidang->id,
            'pembimbing_id' => $this->pembimbing->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'status' => StatusPenempatanEnum::Aktif,
            'tgl_mulai' => $tglMulai,
            'tgl_selesai' => $tglSelesai,
        ]);

        $this->penempatanAnggota1 = Penempatan::create([
            'pendaftaran_id' => $pendaftaran->id,
            'peserta_id' => $this->anggota1->id,
            'bidang_id' => $this->bidang->id,
            'pembimbing_id' => $this->pembimbing->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'status' => StatusPenempatanEnum::Aktif,
            'tgl_mulai' => $tglMulai,
            'tgl_selesai' => $tglSelesai,
        ]);

        $this->penempatanAnggota2 = Penempatan::create([
            'pendaftaran_id' => $pendaftaran->id,
            'peserta_id' => $this->anggota2->id,
            'bidang_id' => $this->bidang->id,
            'pembimbing_id' => $this->pembimbing->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'status' => StatusPenempatanEnum::Aktif,
            'tgl_mulai' => $tglMulai,
            'tgl_selesai' => $tglSelesai,
        ]);

        // Penempatan individu aktif untuk peserta luar tim (agar lolos middleware peserta.diterima dan diuji policy 403)
        $pendaftaranLuar = Pendaftaran::create([
            'user_id' => $this->pesertaLuar->id,
            'bidang_id' => $this->bidang->id,
            'bulan_magang_id' => $bulanMagang->id,
            'kuota_id' => $kuota->id,
            'durasi_bulan' => 3,
            'tipe_pendaftaran' => 'individu',
            'status' => 'diterima',
            'tgl_mulai' => $tglMulai,
            'tgl_selesai' => $tglSelesai,
        ]);

        Penempatan::create([
            'pendaftaran_id' => $pendaftaranLuar->id,
            'peserta_id' => $this->pesertaLuar->id,
            'bidang_id' => $this->bidang->id,
            'pembimbing_id' => $this->pembimbingLain->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'status' => StatusPenempatanEnum::Aktif,
            'tgl_mulai' => $tglMulai,
            'tgl_selesai' => $tglSelesai,
        ]);
    }

    public function test_pembimbing_can_create_tugas_tim_for_team_and_all_members_can_see_it(): void
    {
        $response = $this->actingAs($this->pembimbing)
            ->post(route('pembimbing.tugas.store'), [
                'tipe' => 'kelompok',
                'kelompok_magang_ids' => [$this->kelompok->id],
                'judul' => 'Bangun Modul Portal Tim',
                'deskripsi' => 'Dikerjakan bersama seluruh tim.',
                'kompleksitas' => 'sedang',
                'deadline' => now()->addDays(7)->toDateString(),
                'aksi_publikasi' => 'sekarang',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $tugasTim = Tugas::where('judul', 'Bangun Modul Portal Tim')->first();
        $this->assertNotNull($tugasTim);
        $this->assertSame('kelompok', $tugasTim->tipe);
        $this->assertNull($tugasTim->penempatan_id);
        $this->assertSame($this->kelompok->id, $tugasTim->kelompok_magang_id);

        // Semua anggota tim (ketua + 2 anggota) otomatis masuk ke tugas_peserta
        $this->assertDatabaseHas('tugas_peserta', ['tugas_id' => $tugasTim->id, 'user_id' => $this->ketua->id]);
        $this->assertDatabaseHas('tugas_peserta', ['tugas_id' => $tugasTim->id, 'user_id' => $this->anggota1->id]);
        $this->assertDatabaseHas('tugas_peserta', ['tugas_id' => $tugasTim->id, 'user_id' => $this->anggota2->id]);

        // Ketua dan anggota biasa sama-sama melihat Tugas Tim di dashboard peserta
        $this->actingAs($this->ketua)
            ->get(route('peserta.tugas.index'))
            ->assertOk()
            ->assertSee('Bangun Modul Portal Tim')
            ->assertSee('Tugas Tim');

        $this->actingAs($this->anggota1)
            ->get(route('peserta.tugas.index'))
            ->assertOk()
            ->assertSee('Bangun Modul Portal Tim')
            ->assertSee('Tugas Tim');

        // Pembimbing melihat tugas tim di tab Tugas Tim kelompok (bukan ke penempatan ketua)
        $this->actingAs($this->pembimbing)
            ->get(route('pembimbing.tugas.index', [
                'masuk_kelompok' => $this->kelompok->id,
                'jenis_tugas' => 'kelompok',
                'tab_tim' => 'tugas_tim',
            ]))
            ->assertOk()
            ->assertSee('Bangun Modul Portal Tim')
            ->assertSee('Buat Tugas Tim');
    }

    public function test_member_with_individual_task_still_sees_team_task_and_can_filter(): void
    {
        // 1. Tugas Tim untuk seluruh tim
        $this->actingAs($this->pembimbing)->post(route('pembimbing.tugas.store'), [
            'tipe' => 'kelompok',
            'kelompok_magang_ids' => [$this->kelompok->id],
            'judul' => 'Proyek Utama Tim Sleman',
            'kompleksitas' => 'tinggi',
            'deadline' => now()->addDays(10)->toDateString(),
        ]);

        // 2. Tugas Individu khusus untuk Anggota 1 (Rizky) saja
        $this->actingAs($this->pembimbing)->post(route('pembimbing.tugas.store'), [
            'tipe' => 'individu',
            'penempatan_ids' => [$this->penempatanAnggota1->id],
            'judul' => 'Riset API Khusus Rizky',
            'kompleksitas' => 'sedang',
            'deadline' => now()->addDays(5)->toDateString(),
        ]);

        // Rizky melihat KEDUANYA (Tugas Tim + Tugas Individu)
        $this->actingAs($this->anggota1)
            ->get(route('peserta.tugas.index'))
            ->assertOk()
            ->assertSee('Proyek Utama Tim Sleman')
            ->assertSee('Riset API Khusus Rizky');

        // Filter khusus Tugas Tim hanya menampilkan Tugas Tim
        $this->actingAs($this->anggota1)
            ->get(route('peserta.tugas.index', ['tipe' => 'kelompok']))
            ->assertOk()
            ->assertSee('Proyek Utama Tim Sleman')
            ->assertDontSee('Riset API Khusus Rizky');

        // Filter khusus Tugas Individu hanya menampilkan Tugas Individu
        $this->actingAs($this->anggota1)
            ->get(route('peserta.tugas.index', ['tipe' => 'individu']))
            ->assertOk()
            ->assertSee('Riset API Khusus Rizky')
            ->assertDontSee('Proyek Utama Tim Sleman');

        // Ketua & Anggota 2 TIDAK melihat Tugas Individu milik Rizky, tapi tetap melihat Tugas Tim
        $this->actingAs($this->ketua)
            ->get(route('peserta.tugas.index'))
            ->assertOk()
            ->assertSee('Proyek Utama Tim Sleman')
            ->assertDontSee('Riset API Khusus Rizky');
    }

    public function test_team_task_submission_and_review_works_by_team(): void
    {
        $this->actingAs($this->pembimbing)->post(route('pembimbing.tugas.store'), [
            'tipe' => 'kelompok',
            'kelompok_magang_ids' => [$this->kelompok->id],
            'judul' => 'Integrasi SSO Tim',
            'kompleksitas' => 'sedang',
            'deadline' => now()->addDays(7)->toDateString(),
        ]);

        $tugasTim = Tugas::where('judul', 'Integrasi SSO Tim')->firstOrFail();

        // Anggota biasa mengunggah progres tugas tim
        $this->actingAs($this->anggota1)
            ->post(route('peserta.tugas.progres', $tugasTim), [
                'catatan' => 'Sudah selesai integrasi endpoint login.',
                'link' => 'https://github.com/magkom/sso-tim',
            ])
            ->assertRedirect();

        $progres = ProgresLog::where('tugas_id', $tugasTim->id)->latest('id')->firstOrFail();
        $this->assertSame($this->anggota1->id, $progres->user_id);

        // Pembimbing membuka halaman review tugas tim: tampil nama Tim & nama anggota pengirim
        $this->actingAs($this->pembimbing)
            ->get(route('pembimbing.tugas.show', $tugasTim))
            ->assertOk()
            ->assertSee('Tim Digitalisasi Layanan Sleman')
            ->assertSee('TUGAS TIM')
            ->assertSee('Dikirim oleh:')
            ->assertSee($this->anggota1->nama);

        // Pembimbing melakukan review progres (diterima) dan menandai selesai untuk satu tim
        $this->actingAs($this->pembimbing)
            ->post(route('pembimbing.progres.review', $progres), [
                'status_review' => StatusReviewProgresEnum::Diterima->value,
                'feedback' => 'Mantap, lanjutkan!',
            ])
            ->assertRedirect();

        $this->actingAs($this->pembimbing)
            ->post(route('pembimbing.tugas.selesai', $tugasTim))
            ->assertRedirect();

        $tugasTim->refresh();
        $this->assertSame(StatusTugasEnum::Selesai, $tugasTim->status);
    }

    public function test_anti_idor_and_superadmin_readonly_on_team_tasks(): void
    {
        // 1. Pembimbing lain tidak boleh membuat tugas untuk tim bukan binaannya
        $this->actingAs($this->pembimbingLain)
            ->post(route('pembimbing.tugas.store'), [
                'tipe' => 'kelompok',
                'kelompok_magang_ids' => [$this->kelompok->id],
                'judul' => 'Tugas Ilegal Tim Lain',
                'kompleksitas' => 'rendah',
                'deadline' => now()->addDays(3)->toDateString(),
            ])
            ->assertSessionHasErrors('kelompok_magang_ids');

        // 2. Buat tugas tim oleh pembimbing sah
        $this->actingAs($this->pembimbing)->post(route('pembimbing.tugas.store'), [
            'tipe' => 'kelompok',
            'kelompok_magang_ids' => [$this->kelompok->id],
            'judul' => 'Tugas Rahasia Tim',
            'kompleksitas' => 'rendah',
            'deadline' => now()->addDays(5)->toDateString(),
        ]);

        $tugasTim = Tugas::where('judul', 'Tugas Rahasia Tim')->firstOrFail();

        // Peserta luar tim tidak boleh mengakses detail tugas tim
        $this->actingAs($this->pesertaLuar)
            ->get(route('peserta.tugas.show', $tugasTim))
            ->assertForbidden();

        // SuperAdmin tidak boleh menandai tugas tim selesai (read-only)
        $superAdmin = User::factory()->create([
            'role' => RoleEnum::SuperAdmin->value,
            'email' => 'superadmin@magkom.test',
        ]);

        $this->actingAs($superAdmin)
            ->post(route('pembimbing.tugas.selesai', $tugasTim))
            ->assertForbidden();
    }

    public function test_all_team_members_can_see_unsubmit_and_revise_each_others_team_submission(): void
    {
        $this->actingAs($this->pembimbing)->post(route('pembimbing.tugas.store'), [
            'tipe' => 'kelompok',
            'kelompok_magang_ids' => [$this->kelompok->id],
            'judul' => 'Kolaborasi Dokumen Tim',
            'kompleksitas' => 'sedang',
            'deadline' => now()->addDays(7)->toDateString(),
        ]);

        $tugasTim = Tugas::where('judul', 'Kolaborasi Dokumen Tim')->firstOrFail();

        // 1. Anggota 1 (bukan ketua) mengunggah progres tim
        $this->actingAs($this->anggota1)
            ->post(route('peserta.tugas.progres', $tugasTim), [
                'catatan' => 'Draft awal dari Rizky.',
                'link' => 'https://github.com/magkom/draft-v1',
            ])
            ->assertRedirect();

        // 2. Ketua dan Anggota 2 langsung melihat perubahan & nama pengunggah
        $this->actingAs($this->ketua)
            ->get(route('peserta.tugas.show', $tugasTim))
            ->assertOk()
            ->assertSee('Diserahkan')
            ->assertSee('Diunggah oleh:')
            ->assertSee($this->anggota1->nama)
            ->assertSee('https://github.com/magkom/draft-v1');

        $this->actingAs($this->anggota2)
            ->get(route('peserta.tugas.show', $tugasTim))
            ->assertOk()
            ->assertSee('Diserahkan')
            ->assertSee('Diunggah oleh:')
            ->assertSee($this->anggota1->nama)
            ->assertSee('https://github.com/magkom/draft-v1');

        // 3. Anggota 2 membatalkan pengiriman milik Anggota 1 (hak aksi yang sama dalam tim)
        $this->actingAs($this->anggota2)
            ->post(route('peserta.tugas.batal-kirim', $tugasTim))
            ->assertRedirect();

        $this->assertDatabaseCount('progres_log', 0);

        // 4. Anggota 2 mengunggah revisi baru untuk tim
        $this->actingAs($this->anggota2)
            ->post(route('peserta.tugas.progres', $tugasTim), [
                'catatan' => 'Revisi final oleh Siti.',
                'link' => 'https://github.com/magkom/final-v2',
            ])
            ->assertRedirect();

        // 5. Anggota 1 & Ketua melihat versi terbaru dari Anggota 2
        $this->actingAs($this->anggota1)
            ->get(route('peserta.tugas.show', $tugasTim))
            ->assertOk()
            ->assertSee($this->anggota2->nama)
            ->assertSee('https://github.com/magkom/final-v2')
            ->assertDontSee('https://github.com/magkom/draft-v1');
    }

    public function test_individual_participant_without_team_does_not_see_tipe_filter_tabs(): void
    {
        $pesertaIndividu = User::factory()->create([
            'role' => RoleEnum::Peserta,
            'status_aktif' => true,
        ]);

        $penempatanIndividu = Penempatan::create([
            'peserta_id' => $pesertaIndividu->id,
            'bidang_id' => $this->bidang->id,
            'pembimbing_id' => $this->pembimbing->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'tgl_mulai' => now()->subDays(5),
            'tgl_selesai' => now()->addDays(25),
            'status' => StatusPenempatanEnum::Aktif,
        ]);

        Tugas::create([
            'penempatan_id' => $penempatanIndividu->id,
            'tipe' => 'individu',
            'judul' => 'Tugas Mandiri Khusus Individu',
            'deskripsi' => 'Deskripsi tugas individu.',
            'kategori' => 'Umum',
            'kompleksitas' => KompleksitasEnum::Sedang,
            'deadline' => now()->addDays(5),
            'status' => StatusTugasEnum::Belum,
            'is_published' => true,
        ]);

        $this->actingAs($pesertaIndividu)
            ->get(route('peserta.tugas.index'))
            ->assertOk()
            ->assertSee('Tugas Mandiri Khusus Individu')
            ->assertDontSee('classroom-tipe-tabs')
            ->assertDontSee('data-tipe-filter="kelompok"', false)
            ->assertDontSee('data-tipe-filter="individu"', false)
            ->assertDontSee('Tugas Tim');

        // Query manual ?tipe=kelompok tetap diabaikan dan tugas individu tetap tampil
        $this->actingAs($pesertaIndividu)
            ->get(route('peserta.tugas.index', ['tipe' => 'kelompok']))
            ->assertOk()
            ->assertSee('Tugas Mandiri Khusus Individu');

        // Peserta yang tergabung dalam tim tetap melihat ketiga tab filter
        $this->actingAs($this->ketua)
            ->get(route('peserta.tugas.index'))
            ->assertOk()
            ->assertSee('classroom-tipe-tabs')
            ->assertSee('Semua Tugas')
            ->assertSee('Tugas Tim')
            ->assertSee('Tugas Individu');
    }
}
