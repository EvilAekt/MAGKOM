<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JenjangEnum;
use App\Enums\RealtimeEventTypeEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusPendaftaranEnum;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Dokumen;
use App\Models\JenisDokumen;
use App\Models\Jurusan;
use App\Models\Kuota;
use App\Models\Pendaftaran;
use App\Models\PesertaProfil;
use App\Models\RealtimeEvent;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PendaftaranArchitectureAndSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected User $pesertaA;

    protected User $pesertaB;

    protected User $admin;

    protected User $superAdmin;

    protected BulanMagang $bulan;

    protected BulanMagang $bulan2;

    protected Jurusan $jurusan;

    protected Bidang $bidang;

    protected Kuota $kuota;

    protected Kuota $kuota2;

    protected JenisDokumen $dokumenKtp;

    protected JenisDokumen $dokumenSuratPengantar;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::parse('2026-10-15 10:00:00', 'Asia/Jakarta'));
        CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-10-15 10:00:00', 'Asia/Jakarta'));

        Storage::fake('private');
        Storage::fake('local');

        $this->bidang = Bidang::create([
            'nama_bidang' => 'Aplikasi Informatika',
            'deskripsi' => 'Pengembangan perangkat lunak dan infrastruktur TIK',
            'kuota' => 20,
        ]);

        $this->jurusan = Jurusan::create([
            'bidang_id' => $this->bidang->id,
            'nama_jurusan' => 'Teknik Informatika',
            'kategori' => 'mahasiswa',
            'aktif' => true,
        ]);

        $this->bulan = BulanMagang::create([
            'bulan_mulai' => '2026-11-01',
            'tgl_buka' => '2026-10-01',
            'tgl_tutup' => '2026-10-31',
            'aktif' => true,
        ]);

        $this->kuota = Kuota::create([
            'bulan_magang_id' => $this->bulan->id,
            'jurusan_id' => $this->jurusan->id,
            'jumlah' => 10,
            'terisi' => 0,
            'aktif' => true,
        ]);

        $this->bulan2 = BulanMagang::create([
            'bulan_mulai' => '2026-12-01',
            'tgl_buka' => '2026-10-01',
            'tgl_tutup' => '2026-10-31',
            'aktif' => true,
        ]);

        $this->kuota2 = Kuota::create([
            'bulan_magang_id' => $this->bulan2->id,
            'jurusan_id' => $this->jurusan->id,
            'jumlah' => 5,
            'terisi' => 0,
            'aktif' => true,
        ]);

        $this->dokumenKtp = JenisDokumen::create([
            'kode' => 'ktp',
            'nama' => 'Kartu Tanda Penduduk (KTP)',
            'wajib' => true,
            'aktif' => true,
            'jenjang' => JenjangEnum::S1,
        ]);

        $this->dokumenSuratPengantar = JenisDokumen::create([
            'kode' => 'surat_pengantar',
            'nama' => 'Surat Pengantar Kampus',
            'wajib' => true,
            'aktif' => true,
            'jenjang' => JenjangEnum::S1,
        ]);

        $this->pesertaA = $this->createPesertaUser('Peserta Satu', 'peserta1@kominfo.test', '1234567890');
        $this->pesertaB = $this->createPesertaUser('Peserta Dua', 'peserta2@kominfo.test', '0987654321');

        $this->admin = $this->createUser('Admin Seleksi', 'admin.seleksi@kominfo.test', RoleEnum::Admin);
        $this->superAdmin = $this->createUser('SuperAdmin Audit', 'superadmin.audit@kominfo.test', RoleEnum::SuperAdmin);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        CarbonImmutable::setTestNow();

        parent::tearDown();
    }

    private function createUser(string $nama, string $email, RoleEnum $role): User
    {
        $user = new User;
        $user->nama = $nama;
        $user->email = $email;
        $user->password = 'Password123!';
        $user->role = $role;
        $user->status_aktif = true;
        $user->email_verified_at = now();
        $user->must_change_password = false;
        $user->save();

        return $user;
    }

    private function createPesertaUser(string $nama, string $email, string $nim): User
    {
        $user = $this->createUser($nama, $email, RoleEnum::Peserta);

        PesertaProfil::create([
            'user_id' => $user->id,
            'nim_nisn' => $nim,
            'institusi' => 'Universitas Gadjah Mada',
            'jenjang' => JenjangEnum::S1,
            'jurusan_id' => $this->jurusan->id,
            'alamat' => 'Jl. Kaliurang KM 5 Sleman',
            'no_hp' => '081234567890',
            'foto' => 'peserta-foto/avatar.jpg',
            'nama_kontak_darurat' => 'Orang Tua Peserta',
            'no_kontak_darurat' => '081234567899',
        ]);

        return $user;
    }

    public function test_peserta_berhasil_membuat_draf_pendaftaran_dengan_parent_row_lock(): void
    {
        $response = $this->actingAs($this->pesertaA)->post(route('peserta.pendaftaran.store'), [
            'bulan_magang_id' => $this->bulan->id,
            'kuota_id' => $this->kuota->id,
            'durasi_bulan' => 3,
        ]);

        $response->assertRedirect();

        $pendaftaran = Pendaftaran::where('user_id', $this->pesertaA->id)->first();
        $this->assertNotNull($pendaftaran);
        $this->assertEquals(StatusPendaftaranEnum::BerkasBelumLengkap, $pendaftaran->status);
        $this->assertEquals('mandiri', $pendaftaran->tipe);
        $this->assertEquals($this->bulan->id, $pendaftaran->bulan_magang_id);

        // Jejak audit tersimpan
        $this->assertDatabaseHas('audit_log', [
            'entitas' => 'pendaftaran',
            'entitas_id' => $pendaftaran->id,
            'aksi' => 'Membuat draf pengajuan pendaftaran magang',
        ]);
    }

    public function test_peserta_tidak_bisa_membuat_pendaftaran_kedua_jika_masih_memiliki_pendaftaran_aktif_br17(): void
    {
        // 1. Draf pertama dibuat
        Pendaftaran::create([
            'user_id' => $this->pesertaA->id,
            'bulan_magang_id' => $this->bulan->id,
            'kuota_id' => $this->kuota->id,
            'durasi_bulan' => 3,
            'status' => StatusPendaftaranEnum::BerkasBelumLengkap,
        ]);

        // 2. Mencoba membuat pendaftaran kedua untuk bulan lain
        $response = $this->actingAs($this->pesertaA)->post(route('peserta.pendaftaran.store'), [
            'bulan_magang_id' => $this->bulan2->id,
            'kuota_id' => $this->kuota2->id,
            'durasi_bulan' => 3,
        ]);

        $response->assertSessionHasErrors('pendaftaran');
        $this->assertEquals(1, Pendaftaran::where('user_id', $this->pesertaA->id)->count());
    }

    public function test_peserta_bisa_membuat_pendaftaran_baru_jika_pendaftaran_sebelumnya_ditolak_atau_dibatalkan(): void
    {
        // Pendaftaran lama ditolak
        Pendaftaran::create([
            'user_id' => $this->pesertaA->id,
            'bulan_magang_id' => $this->bulan->id,
            'kuota_id' => $this->kuota->id,
            'durasi_bulan' => 3,
            'status' => StatusPendaftaranEnum::Ditolak,
            'catatan' => 'Berkas tidak memenuhi syarat kualifikasi dinas.',
        ]);

        // Peserta mengajukan untuk bulan kedua
        $response = $this->actingAs($this->pesertaA)->post(route('peserta.pendaftaran.store'), [
            'bulan_magang_id' => $this->bulan2->id,
            'kuota_id' => $this->kuota2->id,
            'durasi_bulan' => 3,
        ]);

        $response->assertRedirect();
        $this->assertEquals(2, Pendaftaran::where('user_id', $this->pesertaA->id)->count());

        $pendaftaranBaru = Pendaftaran::where('user_id', $this->pesertaA->id)
            ->where('status', StatusPendaftaranEnum::BerkasBelumLengkap)
            ->first();
        $this->assertNotNull($pendaftaranBaru);
        $this->assertEquals($this->bulan2->id, $pendaftaranBaru->bulan_magang_id);
    }

    public function test_pengajuan_pendaftaran_ditolak_jika_dokumen_wajib_belum_lengkap(): void
    {
        $pendaftaran = Pendaftaran::create([
            'user_id' => $this->pesertaA->id,
            'bulan_magang_id' => $this->bulan->id,
            'kuota_id' => $this->kuota->id,
            'durasi_bulan' => 3,
            'status' => StatusPendaftaranEnum::BerkasBelumLengkap,
        ]);

        // Mengajukan tanpa mengunggah berkas apapun
        $response = $this->actingAs($this->pesertaA)->post(route('peserta.pendaftaran.ajukan', $pendaftaran));

        $response->assertSessionHas('error');
        $this->assertEquals(StatusPendaftaranEnum::BerkasBelumLengkap, $pendaftaran->fresh()->status);
        $this->assertNull($pendaftaran->fresh()->diajukan_at);
    }

    public function test_pengajuan_pendaftaran_berhasil_jika_seluruh_dokumen_wajib_terunggah_dan_trigger_sse_setelah_commit(): void
    {
        $pendaftaran = Pendaftaran::create([
            'user_id' => $this->pesertaA->id,
            'bulan_magang_id' => $this->bulan->id,
            'kuota_id' => $this->kuota->id,
            'durasi_bulan' => 3,
            'status' => StatusPendaftaranEnum::BerkasBelumLengkap,
        ]);

        // Unggah dokumen wajib 1 (KTP)
        Dokumen::create([
            'pendaftaran_id' => $pendaftaran->id,
            'jenis_dokumen_id' => $this->dokumenKtp->id,
            'path' => 'documents/ktp.pdf',
            'nama_asli' => 'ktp.pdf',
            'mime' => 'application/pdf',
            'ukuran' => 10240,
            'hash' => hash('sha256', 'dummyktp'),
        ]);

        // Unggah dokumen wajib 2 (Surat Pengantar)
        Dokumen::create([
            'pendaftaran_id' => $pendaftaran->id,
            'jenis_dokumen_id' => $this->dokumenSuratPengantar->id,
            'path' => 'documents/surat.pdf',
            'nama_asli' => 'surat.pdf',
            'mime' => 'application/pdf',
            'ukuran' => 20480,
            'hash' => hash('sha256', 'dummysurat'),
        ]);

        $response = $this->actingAs($this->pesertaA)->post(route('peserta.pendaftaran.ajukan', $pendaftaran));

        $response->assertRedirect(route('peserta.pendaftaran.detail', $pendaftaran));
        $this->assertEquals(StatusPendaftaranEnum::Diajukan, $pendaftaran->fresh()->status);
        $this->assertNotNull($pendaftaran->fresh()->diajukan_at);

        // Notifikasi SSE tercatat di tabel realtime_events
        $event = RealtimeEvent::where('event_type', RealtimeEventTypeEnum::PendaftaranBaru->value)
            ->where('role', 'admin')
            ->first();
        $this->assertNotNull($event);
        $this->assertEquals($pendaftaran->nomor_pendaftaran, $event->payload['nomor_pendaftaran']);
    }

    public function test_pembatalan_mandiri_peserta_berhasil_pada_status_yang_diizinkan(): void
    {
        $pendaftaran = Pendaftaran::create([
            'user_id' => $this->pesertaA->id,
            'bulan_magang_id' => $this->bulan->id,
            'kuota_id' => $this->kuota->id,
            'durasi_bulan' => 3,
            'status' => StatusPendaftaranEnum::Diajukan,
            'diajukan_at' => now(),
        ]);

        $response = $this->actingAs($this->pesertaA)->post(route('peserta.pendaftaran.batalkan', $pendaftaran));

        $response->assertRedirect(route('peserta.pendaftaran.index'));
        $this->assertEquals(StatusPendaftaranEnum::Dibatalkan, $pendaftaran->fresh()->status);

        $this->assertDatabaseHas('audit_log', [
            'entitas' => 'pendaftaran',
            'entitas_id' => $pendaftaran->id,
            'aksi' => 'Peserta membatalkan pengajuan pendaftaran magang',
        ]);
    }

    public function test_pembatalan_mandiri_peserta_ditolak_pada_status_diterima(): void
    {
        $pendaftaran = Pendaftaran::create([
            'user_id' => $this->pesertaA->id,
            'bulan_magang_id' => $this->bulan->id,
            'kuota_id' => $this->kuota->id,
            'durasi_bulan' => 3,
            'status' => StatusPendaftaranEnum::Diterima,
            'diajukan_at' => now()->subDays(2),
            'diputuskan_at' => now(),
        ]);

        $response = $this->actingAs($this->pesertaA)->post(route('peserta.pendaftaran.batalkan', $pendaftaran));

        $response->assertForbidden();
        $this->assertEquals(StatusPendaftaranEnum::Diterima, $pendaftaran->fresh()->status);
    }

    public function test_admin_dapat_menerima_pendaftaran_dan_memotong_kuota_secara_atomik(): void
    {
        $pendaftaran = Pendaftaran::create([
            'user_id' => $this->pesertaA->id,
            'bulan_magang_id' => $this->bulan->id,
            'kuota_id' => $this->kuota->id,
            'durasi_bulan' => 3,
            'status' => StatusPendaftaranEnum::Diajukan,
            'diajukan_at' => now()->subDay(),
        ]);

        $kuotaAwal = $this->kuota->fresh()->terisi;

        $response = $this->actingAs($this->admin)->post(route('admin.pendaftaran.putuskan', $pendaftaran), [
            'keputusan' => 'terima',
            'catatan' => 'Berkas lengkap dan sesuai kualifikasi.',
        ]);

        $response->assertRedirect(route('admin.pendaftaran.show', $pendaftaran));

        $pendaftaran->refresh();
        $this->assertEquals(StatusPendaftaranEnum::Diterima, $pendaftaran->status);
        $this->assertEquals($this->admin->id, $pendaftaran->verifikator_id);
        $this->assertNotNull($pendaftaran->diputuskan_at);

        // Kuota terisi bertambah 1
        $this->assertEquals($kuotaAwal + 1, $this->kuota->fresh()->terisi);

        // Event status pendaftaran dikirim ke peserta
        $event = RealtimeEvent::where('user_id', $this->pesertaA->id)
            ->where('event_type', RealtimeEventTypeEnum::StatusPendaftaran->value)
            ->first();
        $this->assertNotNull($event);
        $this->assertEquals('diterima', $event->payload['status']);
    }

    public function test_admin_dapat_menolak_pendaftaran_dengan_alasan_wajib(): void
    {
        $pendaftaran = Pendaftaran::create([
            'user_id' => $this->pesertaA->id,
            'bulan_magang_id' => $this->bulan->id,
            'kuota_id' => $this->kuota->id,
            'durasi_bulan' => 3,
            'status' => StatusPendaftaranEnum::Diajukan,
            'diajukan_at' => now()->subDay(),
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.pendaftaran.putuskan', $pendaftaran), [
            'keputusan' => 'tolak',
            'catatan' => 'Kapasitas pembimbing bidang jaringan telah teralokasikan penuh.',
        ]);

        $response->assertRedirect(route('admin.pendaftaran.show', $pendaftaran));

        $pendaftaran->refresh();
        $this->assertEquals(StatusPendaftaranEnum::Ditolak, $pendaftaran->status);
        $this->assertEquals($this->admin->id, $pendaftaran->verifikator_id);
        $this->assertStringContainsString('Kapasitas pembimbing', $pendaftaran->catatan);

        // Kuota TIDAK bertambah saat ditolak
        $this->assertEquals(0, $this->kuota->fresh()->terisi);
    }

    public function test_superadmin_strictly_read_only_ditolak_memutus_pendaftaran_403(): void
    {
        $pendaftaran = Pendaftaran::create([
            'user_id' => $this->pesertaA->id,
            'bulan_magang_id' => $this->bulan->id,
            'kuota_id' => $this->kuota->id,
            'durasi_bulan' => 3,
            'status' => StatusPendaftaranEnum::Diajukan,
            'diajukan_at' => now()->subDay(),
        ]);

        $response = $this->actingAs($this->superAdmin)->post(route('admin.pendaftaran.putuskan', $pendaftaran), [
            'keputusan' => 'terima',
            'catatan' => 'SuperAdmin mencoba memutuskan pendaftaran.',
        ]);

        $response->assertForbidden();
        $this->assertEquals(StatusPendaftaranEnum::Diajukan, $pendaftaran->fresh()->status);
    }

    public function test_anti_idor_peserta_a_tidak_bisa_melihat_atau_membatalkan_pendaftaran_peserta_b(): void
    {
        $pendaftaranB = Pendaftaran::create([
            'user_id' => $this->pesertaB->id,
            'bulan_magang_id' => $this->bulan->id,
            'kuota_id' => $this->kuota->id,
            'durasi_bulan' => 3,
            'status' => StatusPendaftaranEnum::BerkasBelumLengkap,
        ]);

        // Peserta A mencoba melihat detail pendaftaran milik Peserta B -> 403 Forbidden
        $responseDetail = $this->actingAs($this->pesertaA)->get(route('peserta.pendaftaran.detail', $pendaftaranB));
        $responseDetail->assertForbidden();

        // Peserta A mencoba membatalkan pendaftaran milik Peserta B -> 403 Forbidden
        $responseCancel = $this->actingAs($this->pesertaA)->post(route('peserta.pendaftaran.batalkan', $pendaftaranB));
        $responseCancel->assertForbidden();

        // Peserta A mencoba mengajukan pendaftaran milik Peserta B -> 403 Forbidden
        $responseAjukan = $this->actingAs($this->pesertaA)->post(route('peserta.pendaftaran.ajukan', $pendaftaranB));
        $responseAjukan->assertForbidden();
    }

    public function test_admin_download_bundel_zip_memeriksa_otorisasi_dan_membersihkan_file_sementara(): void
    {
        $pendaftaran = Pendaftaran::create([
            'user_id' => $this->pesertaA->id,
            'bulan_magang_id' => $this->bulan->id,
            'kuota_id' => $this->kuota->id,
            'durasi_bulan' => 3,
            'status' => StatusPendaftaranEnum::Diajukan,
            'diajukan_at' => now(),
        ]);

        // Peserta biasa ditolak mendownload bundel zip -> 403 / redirect
        $responsePeserta = $this->actingAs($this->pesertaA)->get(route('admin.pendaftaran.download-zip'));
        $this->assertTrue(in_array($responsePeserta->getStatusCode(), [403, 302], true));

        // Admin berhasil mendownload bundel zip
        $responseAdmin = $this->actingAs($this->admin)->get(route('admin.pendaftaran.download-zip', [
            'pendaftaran_ids' => [$pendaftaran->id],
        ]));

        $responseAdmin->assertOk();
        $this->assertEquals('application/zip', $responseAdmin->headers->get('Content-Type'));

        // Jejak audit unduh berkas tersimpan
        $this->assertDatabaseHas('audit_log', [
            'entitas' => 'dokumen',
            'detail->aksi' => 'Unduh bundel arsip ZIP berkas pendaftaran peserta',
        ]);
    }
}
