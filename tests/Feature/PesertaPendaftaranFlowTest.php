<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JenjangEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusPendaftaranEnum;
use App\Enums\StatusPenempatanEnum;
use App\Models\BulanMagang;
use App\Models\Dokumen;
use App\Models\JenisDokumen;
use App\Models\Jurusan;
use App\Models\Kuota;
use App\Models\Pendaftaran;
use App\Models\Penempatan;
use App\Models\PesertaProfil;
use App\Models\User;
use Database\Seeders\JenisDokumenSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PesertaPendaftaranFlowTest extends TestCase
{
    use RefreshDatabase;

    protected User $peserta;
    protected BulanMagang $bulan;
    protected Kuota $kuota;
    protected Jurusan $jurusan;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('private');
        $this->seed(JenisDokumenSeeder::class);

        $this->peserta = new User();
        $this->peserta->nama = 'Ahmad Fajar';
        $this->peserta->email = 'ahmad.fajar@example.com';
        $this->peserta->password = 'Password123!';
        $this->peserta->role = RoleEnum::Peserta;
        $this->peserta->email_verified_at = now();
        $this->peserta->status_aktif = true;
        $this->peserta->must_change_password = false;
        $this->peserta->save();

        $this->jurusan = Jurusan::create([
            'nama_jurusan' => 'Teknik Komputer dan Jaringan',
            'kategori' => 'smk',
            'aktif' => true,
        ]);

        PesertaProfil::create([
            'user_id' => $this->peserta->id,
            'nim_nisn' => '1234567890',
            'institusi' => 'SMK Negeri 1 Sleman',
            'jurusan_id' => $this->jurusan->id,
            'jenjang' => JenjangEnum::Smk,
            'no_hp' => '081234567890',
        ]);

        $this->bulan = BulanMagang::create([
            'bulan_mulai' => '2026-11-01',
            'tgl_buka' => '2026-10-01',
            'tgl_tutup' => '2026-10-25',
            'aktif' => true,
        ]);

        $this->kuota = Kuota::create([
            'bulan_magang_id' => $this->bulan->id,
            'jurusan_id' => $this->jurusan->id,
            'jumlah' => 10,
            'terisi' => 0,
            'aktif' => true,
        ]);
    }

    public function test_peserta_can_view_pendaftaran_index_and_available_periods(): void
    {
        $response = $this->actingAs($this->peserta)->get(route('peserta.pendaftaran.index'));

        $response->assertOk();
        $response->assertSee('Riwayat Pendaftaran Magang');
        $response->assertSee('Buat Pengajuan Baru');
        $response->assertSee('November 2026');
        $response->assertSee('Teknik Komputer dan Jaringan');
    }

    public function test_peserta_can_create_new_draft_pendaftaran(): void
    {
        $response = $this->actingAs($this->peserta)->post(route('peserta.pendaftaran.store'), [
            'bulan_magang_id' => $this->bulan->id,
            'kuota_id' => $this->kuota->id,
            'durasi_bulan' => 3,
        ]);

        $pendaftaran = Pendaftaran::where('user_id', $this->peserta->id)->first();
        $this->assertNotNull($pendaftaran);
        $this->assertEquals(StatusPendaftaranEnum::BerkasBelumLengkap, $pendaftaran->status);

        $response->assertRedirect(route('peserta.pendaftaran.detail', $pendaftaran));
    }

    public function test_peserta_can_view_detail_with_stepper_and_upload_cards(): void
    {
        $pendaftaran = Pendaftaran::create([
            'user_id' => $this->peserta->id,
            'bulan_magang_id' => $this->bulan->id,
            'kuota_id' => $this->kuota->id,
            'durasi_bulan' => 3,
            'status' => StatusPendaftaranEnum::BerkasBelumLengkap,
        ]);

        $response = $this->actingAs($this->peserta)->get(route('peserta.pendaftaran.detail', $pendaftaran));

        $response->assertOk();
        $response->assertSee('Detail & Berkas Pendaftaran');
        $response->assertSee('1. Berkas Persyaratan');
        $response->assertSee('2. Verifikasi Berkas');
        $response->assertSee('3. Keputusan');
        $response->assertSee('Surat Permohonan PKL/Magang dari Kepala Sekolah');
        $response->assertSee('Curriculum Vitae (CV) & Portofolio Keahlian');
        $response->assertSee('Salinan Kartu Pelajar');
    }

    public function test_peserta_can_upload_document_to_private_disk(): void
    {
        $pendaftaran = Pendaftaran::create([
            'user_id' => $this->peserta->id,
            'bulan_magang_id' => $this->bulan->id,
            'kuota_id' => $this->kuota->id,
            'durasi_bulan' => 3,
            'status' => StatusPendaftaranEnum::BerkasBelumLengkap,
        ]);

        $jenisDokumen = JenisDokumen::where('jenjang', 'smk')->where('kode', 'cv')->firstOrFail();

        $file = UploadedFile::fake()->create('cv_ahmad.pdf', 500, 'application/pdf');

        $response = $this->actingAs($this->peserta)->post(route('peserta.berkas.unggah', $pendaftaran), [
            'jenis_dokumen_id' => $jenisDokumen->id,
            'file' => $file,
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('dokumen', [
            'pendaftaran_id' => $pendaftaran->id,
            'jenis_dokumen_id' => $jenisDokumen->id,
            'nama_asli' => 'cv_ahmad.pdf',
        ]);
    }

    public function test_peserta_cannot_submit_if_required_documents_are_missing(): void
    {
        $pendaftaran = Pendaftaran::create([
            'user_id' => $this->peserta->id,
            'bulan_magang_id' => $this->bulan->id,
            'kuota_id' => $this->kuota->id,
            'durasi_bulan' => 3,
            'status' => StatusPendaftaranEnum::BerkasBelumLengkap,
        ]);

        $response = $this->actingAs($this->peserta)->post(route('peserta.pendaftaran.ajukan', $pendaftaran));

        $response->assertSessionHas('error');
        $this->assertEquals(StatusPendaftaranEnum::BerkasBelumLengkap, $pendaftaran->fresh()->status);
    }

    public function test_peserta_can_submit_application_when_all_required_documents_uploaded(): void
    {
        $pendaftaran = Pendaftaran::create([
            'user_id' => $this->peserta->id,
            'bulan_magang_id' => $this->bulan->id,
            'kuota_id' => $this->kuota->id,
            'durasi_bulan' => 3,
            'status' => StatusPendaftaranEnum::BerkasBelumLengkap,
        ]);

        $wajibDokumen = JenisDokumen::where('jenjang', 'smk')->where('wajib', true)->get();

        foreach ($wajibDokumen as $jenis) {
            Dokumen::create([
                'pendaftaran_id' => $pendaftaran->id,
                'jenis_dokumen_id' => $jenis->id,
                'path' => 'dokumen/test_' . $jenis->kode . '.pdf',
                'nama_asli' => 'test_' . $jenis->kode . '.pdf',
                'mime' => 'application/pdf',
                'ukuran' => 204800,
                'hash' => md5($jenis->kode),
            ]);
        }

        $response = $this->actingAs($this->peserta)->post(route('peserta.pendaftaran.ajukan', $pendaftaran));

        $response->assertRedirect(route('peserta.pendaftaran.detail', $pendaftaran));
        $response->assertSessionHas('success');

        $this->assertEquals(StatusPendaftaranEnum::Diajukan, $pendaftaran->fresh()->status);
    }

    public function test_accepted_peserta_shows_download_surat_diterima_pdf(): void
    {
        $pendaftaran = Pendaftaran::create([
            'user_id' => $this->peserta->id,
            'bulan_magang_id' => $this->bulan->id,
            'kuota_id' => $this->kuota->id,
            'durasi_bulan' => 3,
            'status' => StatusPendaftaranEnum::Diterima,
            'diputuskan_at' => now(),
        ]);

        $pembimbing = new User();
        $pembimbing->nama = 'Bambang Pembimbing';
        $pembimbing->email = 'bambang@sleman.go.id';
        $pembimbing->password = 'Password123!';
        $pembimbing->role = RoleEnum::Pembimbing;
        $pembimbing->email_verified_at = now();
        $pembimbing->status_aktif = true;
        $pembimbing->must_change_password = false;
        $pembimbing->save();

        $penempatan = Penempatan::create([
            'pendaftaran_id' => $pendaftaran->id,
            'peserta_id' => $this->peserta->id,
            'pembimbing_id' => $pembimbing->id,
            'kuota_id' => $this->kuota->id,
            'jalur' => 'umum',
            'sumber' => 'online',
            'tgl_mulai' => '2026-11-01',
            'tgl_selesai' => '2027-01-31',
            'status' => StatusPenempatanEnum::Aktif,
        ]);

        $response = $this->actingAs($this->peserta)->get(route('peserta.pendaftaran.detail', $pendaftaran));

        $response->assertOk();
        $response->assertSee('Selamat! Permohonan Magang Anda Telah Disetujui');
        $response->assertSee('Unduh Surat Diterima (PDF)');
        $response->assertSee('Lihat Surat Diterima');
        $response->assertSee(route('peserta.penempatan.surat-diterima', $penempatan));
        $response->assertSee(route('peserta.penempatan.surat-diterima.pratinjau', $penempatan));

        // Test unduh file PDF
        $downloadResponse = $this->actingAs($this->peserta)->get(route('peserta.penempatan.surat-diterima', $penempatan));
        $downloadResponse->assertOk();
        $this->assertStringContainsString('application/pdf', $downloadResponse->headers->get('content-type'));

        // Test pratinjau inline stream PDF
        $previewResponse = $this->actingAs($this->peserta)->get(route('peserta.penempatan.surat-diterima.pratinjau', $penempatan));
        $previewResponse->assertOk();
        $this->assertStringContainsString('application/pdf', $previewResponse->headers->get('content-type'));
        $this->assertStringContainsString('inline', $previewResponse->headers->get('content-disposition'));
    }

    public function test_peserta_can_cancel_draft_or_submitted_pendaftaran(): void
    {
        $pendaftaran = Pendaftaran::create([
            'user_id' => $this->peserta->id,
            'bulan_magang_id' => $this->bulan->id,
            'kuota_id' => $this->kuota->id,
            'durasi_bulan' => 3,
            'status' => StatusPendaftaranEnum::BerkasBelumLengkap,
        ]);

        $response = $this->actingAs($this->peserta)->post(route('peserta.pendaftaran.batalkan', $pendaftaran));

        $response->assertRedirect(route('peserta.pendaftaran.index'));
        $response->assertSessionHas('success', 'Pengajuan pendaftaran magang berhasil dibatalkan.');

        $this->assertEquals(StatusPendaftaranEnum::Dibatalkan, $pendaftaran->fresh()->status);
        $this->assertDatabaseHas('audit_log', [
            'entitas' => 'pendaftaran',
            'entitas_id' => $pendaftaran->id,
            'aksi' => 'Peserta membatalkan pengajuan pendaftaran magang',
        ]);
    }

    public function test_peserta_cannot_cancel_accepted_pendaftaran(): void
    {
        $pendaftaran = Pendaftaran::create([
            'user_id' => $this->peserta->id,
            'bulan_magang_id' => $this->bulan->id,
            'kuota_id' => $this->kuota->id,
            'durasi_bulan' => 3,
            'status' => StatusPendaftaranEnum::Diterima,
            'diputuskan_at' => now(),
        ]);

        $response = $this->actingAs($this->peserta)->post(route('peserta.pendaftaran.batalkan', $pendaftaran));

        $response->assertForbidden();
        $this->assertEquals(StatusPendaftaranEnum::Diterima, $pendaftaran->fresh()->status);
    }

    public function test_peserta_cannot_cancel_other_users_pendaftaran(): void
    {
        $otherPeserta = User::create([
            'nama' => 'Peserta Lain',
            'email' => 'lain@example.com',
            'password' => 'Password123!',
            'role' => RoleEnum::Peserta,
            'email_verified_at' => now(),
            'status_aktif' => true,
            'must_change_password' => false,
        ]);

        $pendaftaran = Pendaftaran::create([
            'user_id' => $otherPeserta->id,
            'bulan_magang_id' => $this->bulan->id,
            'kuota_id' => $this->kuota->id,
            'durasi_bulan' => 3,
            'status' => StatusPendaftaranEnum::Diajukan,
        ]);

        $response = $this->actingAs($this->peserta)->post(route('peserta.pendaftaran.batalkan', $pendaftaran));

        $response->assertForbidden();
        $this->assertEquals(StatusPendaftaranEnum::Diajukan, $pendaftaran->fresh()->status);
    }

    public function test_submitting_application_sets_status_to_diajukan_even_if_quota_full(): void
    {
        // Set kuota penuh
        $this->kuota->update(['terisi' => 10]);

        $pendaftaran = Pendaftaran::create([
            'user_id' => $this->peserta->id,
            'bulan_magang_id' => $this->bulan->id,
            'kuota_id' => $this->kuota->id,
            'durasi_bulan' => 3,
            'status' => StatusPendaftaranEnum::BerkasBelumLengkap,
        ]);

        $wajibDokumen = JenisDokumen::where('jenjang', 'smk')->where('wajib', true)->get();

        foreach ($wajibDokumen as $jenis) {
            Dokumen::create([
                'pendaftaran_id' => $pendaftaran->id,
                'jenis_dokumen_id' => $jenis->id,
                'path' => 'dokumen/test_' . $jenis->kode . '.pdf',
                'nama_asli' => 'test_' . $jenis->kode . '.pdf',
                'mime' => 'application/pdf',
                'ukuran' => 204800,
                'hash' => md5($jenis->kode),
            ]);
        }

        $response = $this->actingAs($this->peserta)->post(route('peserta.pendaftaran.ajukan', $pendaftaran));

        $response->assertRedirect(route('peserta.pendaftaran.detail', $pendaftaran));
        $response->assertSessionHas('success');

        // Status harus tetap Diajukan (verifikasi admin) per AC-03-6 & BR-06
        $this->assertEquals(StatusPendaftaranEnum::Diajukan, $pendaftaran->fresh()->status);
        $this->assertNotNull($pendaftaran->fresh()->diajukan_at);
    }

    public function test_peserta_blocked_from_creating_multiple_active_pendaftaran(): void
    {
        // Pendaftaran 1 yang aktif
        Pendaftaran::create([
            'user_id' => $this->peserta->id,
            'bulan_magang_id' => $this->bulan->id,
            'kuota_id' => $this->kuota->id,
            'durasi_bulan' => 3,
            'status' => StatusPendaftaranEnum::Diajukan,
        ]);

        // Bulan ke-2
        $bulan2 = BulanMagang::create([
            'bulan_mulai' => '2026-12-01',
            'tgl_buka' => '2026-11-01',
            'tgl_tutup' => '2026-11-25',
            'aktif' => true,
        ]);
        $kuota2 = Kuota::create([
            'bulan_magang_id' => $bulan2->id,
            'jurusan_id' => $this->jurusan->id,
            'jumlah' => 10,
            'terisi' => 0,
            'aktif' => true,
        ]);

        $response = $this->actingAs($this->peserta)->post(route('peserta.pendaftaran.store'), [
            'bulan_magang_id' => $bulan2->id,
            'kuota_id' => $kuota2->id,
            'durasi_bulan' => 3,
        ]);

        $response->assertSessionHasErrors('pendaftaran');
        $this->assertCount(1, Pendaftaran::where('user_id', $this->peserta->id)->get());
    }

    public function test_peserta_can_preview_uploaded_document_inline_with_signed_url(): void
    {
        Storage::fake('private');

        $pendaftaran = Pendaftaran::create([
            'user_id' => $this->peserta->id,
            'bulan_magang_id' => $this->bulan->id,
            'kuota_id' => $this->kuota->id,
            'durasi_bulan' => 3,
            'status' => StatusPendaftaranEnum::BerkasBelumLengkap,
        ]);

        $filePath = "documents/{$pendaftaran->id}/proposal_magang.pdf";
        Storage::disk('private')->put($filePath, '%PDF-1.4 dummy content');

        $dokumen = Dokumen::create([
            'pendaftaran_id' => $pendaftaran->id,
            'jenis_dokumen_id' => JenisDokumen::first()->id,
            'path' => $filePath,
            'nama_asli' => 'proposal_magang.pdf',
            'mime' => 'application/pdf',
            'ukuran' => 1024,
            'hash' => hash('sha256', '%PDF-1.4 dummy content'),
        ]);

        $signedPreviewUrl = $dokumen->getSignedPreviewUrl(5);

        // 1. Pemilik pendaftaran dapat melihat pratinjau inline
        $previewRes = $this->actingAs($this->peserta)->get($signedPreviewUrl);
        $previewRes->assertOk();
        $this->assertStringContainsString('application/pdf', $previewRes->headers->get('content-type'));
        $this->assertStringContainsString('inline', $previewRes->headers->get('content-disposition'));
        $this->assertEquals('nosniff', $previewRes->headers->get('x-content-type-options'));

        // 2. Akses tanpa signature valid ditolak (403)
        $unsignedUrl = route('berkas.pratinjau', $dokumen);
        $unsignedRes = $this->actingAs($this->peserta)->get($unsignedUrl);
        $unsignedRes->assertStatus(403);

        // 3. Peserta lain ditolak mengakses berkas privat orang lain (403 IDOR prevention)
        $pesertaLain = new User();
        $pesertaLain->nama = 'Peserta Lain';
        $pesertaLain->email = 'peserta.lain@example.com';
        $pesertaLain->password = 'Password123!';
        $pesertaLain->role = RoleEnum::Peserta;
        $pesertaLain->email_verified_at = now();
        $pesertaLain->status_aktif = true;
        $pesertaLain->must_change_password = false;
        $pesertaLain->save();
        $pesertaLainRes = $this->actingAs($pesertaLain)->get($signedPreviewUrl);
        $pesertaLainRes->assertStatus(403);
    }
}
