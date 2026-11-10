<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JalurEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusLogbookEnum;
use App\Enums\StatusPendaftaranEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\SumberPenempatanEnum;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Dokumen;
use App\Models\JenisDokumen;
use App\Models\Jurusan;
use App\Models\Kuota;
use App\Models\Logbook;
use App\Models\Pendaftaran;
use App\Models\Penempatan;
use App\Models\PesertaProfil;
use App\Models\RealtimeEvent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RealtimeEventDispatchIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('private');
    }

    public function test_peserta_ajukan_pendaftaran_dispatches_realtime_event_to_admin(): void
    {
        $bulan = BulanMagang::create([
            'bulan_mulai' => now()->addMonth()->startOfMonth(),
            'tgl_buka' => now()->subDay(),
            'tgl_tutup' => now()->addDays(20),
            'aktif' => true,
        ]);

        $bidang = Bidang::create(['nama_bidang' => 'Teknologi Informasi', 'aktif' => true]);
        $jurusan = Jurusan::create(['nama_jurusan' => 'Informatika', 'kategori' => 'smk', 'aktif' => true]);
        $kuota = Kuota::create(['bulan_magang_id' => $bulan->id, 'jurusan_id' => $jurusan->id, 'jumlah' => 10, 'terisi' => 0, 'aktif' => true]);

        $peserta = User::factory()->create([
            'role' => RoleEnum::Peserta,
            'email_verified_at' => now(),
            'status_aktif' => true,
            'must_change_password' => false,
        ]);

        PesertaProfil::create([
            'user_id' => $peserta->id,
            'nim_nisn' => '1234567890',
            'institusi' => 'SMK 1',
            'jurusan_id' => $jurusan->id,
            'jenjang' => 'smk',
            'no_hp' => '08123456789',
        ]);

        $pendaftaran = Pendaftaran::create([
            'user_id' => $peserta->id,
            'bulan_magang_id' => $bulan->id,
            'kuota_id' => $kuota->id,
            'durasi_bulan' => 3,
            'status' => StatusPendaftaranEnum::BerkasBelumLengkap,
        ]);

        $jenisDokumen = JenisDokumen::create([
            'nama' => 'Surat Pengantar',
            'kode' => 'SURAT_PENGANTAR',
            'jenjang' => 'smk',
            'wajib' => true,
            'aktif' => true,
        ]);

        Dokumen::create([
            'pendaftaran_id' => $pendaftaran->id,
            'jenis_dokumen_id' => $jenisDokumen->id,
            'path' => 'dokumen/test.pdf',
            'nama_asli' => 'test.pdf',
            'mime' => 'application/pdf',
            'ukuran' => 1024,
            'hash' => hash('sha256', 'test'),
        ]);

        // Peserta mengirim pengajuan berkas
        $response = $this->actingAs($peserta)->post("/peserta/pendaftaran/{$pendaftaran->id}/ajukan");
        $response->assertSessionHas('success');

        // Pastikan entri event SSE tercipta untuk role admin
        $this->assertDatabaseHas('realtime_events', [
            'role' => 'admin',
            'event_type' => 'pendaftaran_baru',
        ]);

        $event = RealtimeEvent::where('role', 'admin')->where('event_type', 'pendaftaran_baru')->first();
        $this->assertNotNull($event);
        $this->assertStringContainsString('Pendaftaran Baru Masuk', $event->payload['title']);
        $this->assertStringContainsString($peserta->nama, $event->payload['message']);
    }

    public function test_admin_terima_pendaftaran_dispatches_realtime_events(): void
    {
        $bulan = BulanMagang::create([
            'bulan_mulai' => now()->addMonth()->startOfMonth(),
            'tgl_buka' => now()->subDay(),
            'tgl_tutup' => now()->addDays(20),
            'aktif' => true,
        ]);

        $bidang = Bidang::create(['nama_bidang' => 'Teknologi Informasi', 'aktif' => true]);
        $jurusan = Jurusan::create(['nama_jurusan' => 'Informatika', 'kategori' => 'smk', 'aktif' => true]);
        $kuota = Kuota::create(['bulan_magang_id' => $bulan->id, 'jurusan_id' => $jurusan->id, 'jumlah' => 10, 'terisi' => 0, 'aktif' => true]);

        $admin = User::factory()->create(['role' => RoleEnum::Admin, 'status_aktif' => true, 'must_change_password' => false]);
        $pembimbing = User::factory()->create(['role' => RoleEnum::Pembimbing, 'status_aktif' => true, 'must_change_password' => false]);
        $peserta = User::factory()->create(['role' => RoleEnum::Peserta, 'status_aktif' => true, 'must_change_password' => false]);

        $pendaftaran = Pendaftaran::create([
            'user_id' => $peserta->id,
            'bulan_magang_id' => $bulan->id,
            'kuota_id' => $kuota->id,
            'durasi_bulan' => 3,
            'status' => StatusPendaftaranEnum::Diajukan,
            'diajukan_at' => now(),
        ]);

        // Admin memutuskan menerima pendaftaran
        $response = $this->actingAs($admin)->post(route('admin.pendaftaran.putuskan', $pendaftaran), [
            'keputusan' => 'terima',
            'pembimbing_id' => $pembimbing->id,
            'bidang_id' => $bidang->id,
        ]);

        $response->assertSessionHas('success');

        // Pastikan event SSE berstatus Diterima sampai ke peserta
        $this->assertDatabaseHas('realtime_events', [
            'user_id' => $peserta->id,
            'event_type' => 'status_pendaftaran',
        ]);

        // Pastikan event perubahan kuota sampai ke admin
        $this->assertDatabaseHas('realtime_events', [
            'role' => 'admin',
            'event_type' => 'kuota_berubah',
        ]);
    }

    public function test_peserta_submit_logbook_dispatches_realtime_event_to_pembimbing(): void
    {
        $bulan = BulanMagang::create([
            'bulan_mulai' => now()->startOfMonth(),
            'tgl_buka' => now()->subMonth(),
            'tgl_tutup' => now()->endOfMonth(),
            'aktif' => true,
        ]);

        $bidang = Bidang::create(['nama_bidang' => 'Teknologi Informasi', 'aktif' => true]);
        $jurusan = Jurusan::create(['nama_jurusan' => 'Informatika', 'kategori' => 'smk', 'aktif' => true]);
        $kuota = Kuota::create(['bulan_magang_id' => $bulan->id, 'jurusan_id' => $jurusan->id, 'jumlah' => 10, 'terisi' => 1, 'aktif' => true]);

        $pembimbing = User::factory()->create(['role' => RoleEnum::Pembimbing, 'status_aktif' => true, 'must_change_password' => false]);
        $peserta = User::factory()->create(['role' => RoleEnum::Peserta, 'status_aktif' => true, 'must_change_password' => false]);

        $pendaftaran = Pendaftaran::create([
            'user_id' => $peserta->id,
            'bulan_magang_id' => $bulan->id,
            'kuota_id' => $kuota->id,
            'durasi_bulan' => 3,
            'status' => StatusPendaftaranEnum::Diterima,
        ]);

        $penempatan = Penempatan::create([
            'pendaftaran_id' => $pendaftaran->id,
            'peserta_id' => $peserta->id,
            'kuota_id' => $kuota->id,
            'bidang_id' => $bidang->id,
            'pembimbing_id' => $pembimbing->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'tgl_mulai' => now()->startOfMonth(),
            'tgl_selesai' => now()->addMonths(3)->endOfMonth(),
            'status' => StatusPenempatanEnum::Aktif,
        ]);

        // Peserta mengisi logbook
        $response = $this->actingAs($peserta)->post('/peserta/logbook', [
            'minggu_ke' => 1,
            'tanggal' => now()->toDateString(),
            'kegiatan' => 'Mengembangkan modul SSE real-time untuk instansi.',
        ]);

        $response->assertSessionHas('success');

        // Pastikan event SSE LogbookBaru dikirim ke ID pembimbing
        $this->assertDatabaseHas('realtime_events', [
            'user_id' => $pembimbing->id,
            'event_type' => 'logbook_baru',
        ]);
    }

    public function test_pembimbing_review_logbook_dispatches_realtime_event_to_peserta(): void
    {
        $bulan = BulanMagang::create([
            'bulan_mulai' => now()->startOfMonth(),
            'tgl_buka' => now()->subMonth(),
            'tgl_tutup' => now()->endOfMonth(),
            'aktif' => true,
        ]);

        $bidang = Bidang::create(['nama_bidang' => 'Teknologi Informasi', 'aktif' => true]);
        $jurusan = Jurusan::create(['nama_jurusan' => 'Informatika', 'kategori' => 'smk', 'aktif' => true]);
        $kuota = Kuota::create(['bulan_magang_id' => $bulan->id, 'jurusan_id' => $jurusan->id, 'jumlah' => 10, 'terisi' => 1, 'aktif' => true]);

        $pembimbing = User::factory()->create(['role' => RoleEnum::Pembimbing, 'status_aktif' => true, 'must_change_password' => false]);
        $peserta = User::factory()->create(['role' => RoleEnum::Peserta, 'status_aktif' => true, 'must_change_password' => false]);

        $pendaftaran = Pendaftaran::create([
            'user_id' => $peserta->id,
            'bulan_magang_id' => $bulan->id,
            'kuota_id' => $kuota->id,
            'durasi_bulan' => 3,
            'status' => StatusPendaftaranEnum::Diterima,
        ]);

        $penempatan = Penempatan::create([
            'pendaftaran_id' => $pendaftaran->id,
            'peserta_id' => $peserta->id,
            'kuota_id' => $kuota->id,
            'bidang_id' => $bidang->id,
            'pembimbing_id' => $pembimbing->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'tgl_mulai' => now()->startOfMonth(),
            'tgl_selesai' => now()->addMonths(3)->endOfMonth(),
            'status' => StatusPenempatanEnum::Aktif,
        ]);

        $logbook = Logbook::create([
            'penempatan_id' => $penempatan->id,
            'minggu_ke' => 1,
            'tanggal' => now(),
            'kegiatan' => 'Mengerjakan tugas harian magang.',
            'status' => StatusLogbookEnum::Terkirim,
        ]);

        // Pembimbing mereviu logbook
        $response = $this->actingAs($pembimbing)->post("/pembimbing/logbook/{$logbook->id}/review", [
            'status' => 'disetujui',
            'catatan_pembimbing' => 'Laporan kerja sangat jelas dan terstruktur dengan baik.',
        ]);

        $response->assertSessionHas('success');

        // Pastikan event SSE LogbookDireviu terkirim ke peserta
        $this->assertDatabaseHas('realtime_events', [
            'user_id' => $peserta->id,
            'event_type' => 'logbook_direviu',
        ]);
    }
}
