<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JalurEnum;
use App\Enums\KompleksitasEnum;
use App\Enums\RealtimeEventTypeEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusLogbookEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\StatusReviewProgresEnum;
use App\Enums\StatusTugasEnum;
use App\Enums\SumberPenempatanEnum;
use App\Models\Bidang;
use App\Models\Logbook;
use App\Models\Penempatan;
use App\Models\ProgresLog;
use App\Models\RealtimeEvent;
use App\Models\Tugas;
use App\Models\User;
use App\Rules\SafeExternalUrlRule;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RealtimeTugasLogbookFeedbackTest extends TestCase
{
    use RefreshDatabase;

    protected User $pembimbing;

    protected User $peserta;

    protected Penempatan $penempatan;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('private');

        $this->pembimbing = new User;
        $this->pembimbing->nama = 'Pak Pembimbing Realtime';
        $this->pembimbing->email = 'pembimbing.realtime@kominfo.test';
        $this->pembimbing->password = 'secret123';
        $this->pembimbing->role = RoleEnum::Pembimbing;
        $this->pembimbing->email_verified_at = now();
        $this->pembimbing->status_aktif = true;
        $this->pembimbing->must_change_password = false;
        $this->pembimbing->save();

        $this->peserta = new User;
        $this->peserta->nama = 'Mentee Realtime';
        $this->peserta->email = 'mentee.realtime@kominfo.test';
        $this->peserta->password = 'secret123';
        $this->peserta->role = RoleEnum::Peserta;
        $this->peserta->email_verified_at = now();
        $this->peserta->status_aktif = true;
        $this->peserta->must_change_password = false;
        $this->peserta->save();

        $bidang = Bidang::firstOrCreate(
            ['nama_bidang' => 'Sistem Informasi'],
            ['aktif' => true]
        );

        $this->penempatan = Penempatan::create([
            'peserta_id' => $this->peserta->id,
            'pembimbing_id' => $this->pembimbing->id,
            'bidang_id' => $bidang->id,
            'jalur' => JalurEnum::Khusus,
            'sumber' => SumberPenempatanEnum::Manual,
            'tgl_mulai' => Carbon::today()->startOfMonth(),
            'tgl_selesai' => Carbon::today()->addMonths(2)->endOfMonth(),
            'status' => StatusPenempatanEnum::Aktif,
        ]);
    }

    public function test_pembimbing_review_progres_menerbitkan_event_realtime_progres_direviu_ke_peserta(): void
    {
        $tugas = Tugas::create([
            'penempatan_id' => $this->penempatan->id,
            'judul' => 'Pembuatan Modul SSE Dashboard',
            'deskripsi' => 'Rancang arsitektur real-time dengan SSE.',
            'deadline' => Carbon::today()->addDays(5),
            'status' => StatusTugasEnum::Proses,
            'kategori' => 'Backend Development',
            'kompleksitas' => KompleksitasEnum::Sedang,
        ]);

        $progres = ProgresLog::create([
            'tugas_id' => $tugas->id,
            'tgl_lapor' => Carbon::today(),
            'catatan' => 'Draf awal implementasi sudah diunggah.',
            'status_review' => StatusReviewProgresEnum::Menunggu,
        ]);

        // Pembimbing memberikan catatan revisi
        $response = $this->actingAs($this->pembimbing)
            ->postJson(route('pembimbing.progres.review', $progres->id), [
                'status_review' => StatusReviewProgresEnum::PerluRevisi->value,
                'feedback' => 'Mohon tambahkan dokumentasi diagram arsitektur.',
            ]);

        $response->assertOk();

        // Verifikasi event real-time terbit ke peserta yang tepat
        $event = RealtimeEvent::where('user_id', $this->peserta->id)
            ->where('event_type', RealtimeEventTypeEnum::ProgresDireviu->value)
            ->latest('id')
            ->first();

        $this->assertNotNull($event, 'Event SSE progres_direviu harus terbit ke peserta.');
        $this->assertSame('Evaluasi Progres Tugas: Perlu Revisi', $event->payload['title']);
        $this->assertSame('perlu_revisi', $event->payload['status_review']);
        $this->assertSame('Mohon tambahkan dokumentasi diagram arsitektur.', $event->payload['feedback']);
        $this->assertSame($tugas->id, $event->payload['tugas_id']);
    }

    public function test_pembimbing_review_logbook_menerbitkan_event_berisi_catatan_revisi(): void
    {
        $logbook = Logbook::create([
            'penempatan_id' => $this->penempatan->id,
            'minggu_ke' => 1,
            'tanggal' => Carbon::today(),
            'kegiatan' => 'Mengerjakan instalasi server dan dokumentasi.',
            'status' => StatusLogbookEnum::Terkirim,
        ]);

        // Pembimbing mereview logbook dengan status perlu revisi
        $response = $this->actingAs($this->pembimbing)
            ->postJson(route('pembimbing.logbook.review', $logbook->id), [
                'status' => StatusLogbookEnum::PerluRevisi->value,
                'catatan_pembimbing' => 'Uraian kegiatan hari Rabu masih kurang detail.',
            ]);

        $response->assertOk();

        $event = RealtimeEvent::where('user_id', $this->peserta->id)
            ->where('event_type', RealtimeEventTypeEnum::LogbookDireviu->value)
            ->latest('id')
            ->first();

        $this->assertNotNull($event, 'Event SSE logbook_direviu harus terbit ke peserta.');
        $this->assertSame('Logbook Memerlukan Revisi', $event->payload['title']);
        $this->assertSame('perlu_revisi', $event->payload['status']);
        $this->assertSame('Uraian kegiatan hari Rabu masih kurang detail.', $event->payload['catatan_pembimbing']);
        $this->assertSame($logbook->id, $event->payload['logbook_id']);
    }

    public function test_pembimbing_bulk_review_logbook_menerbitkan_event_realtime_ke_peserta(): void
    {
        $lb1 = Logbook::create([
            'penempatan_id' => $this->penempatan->id,
            'minggu_ke' => 1,
            'tanggal' => Carbon::today()->subDay(),
            'kegiatan' => 'Aktivitas hari pertama magang.',
            'status' => StatusLogbookEnum::Terkirim,
        ]);

        $lb2 = Logbook::create([
            'penempatan_id' => $this->penempatan->id,
            'minggu_ke' => 1,
            'tanggal' => Carbon::today(),
            'kegiatan' => 'Aktivitas hari kedua magang.',
            'status' => StatusLogbookEnum::Terkirim,
        ]);

        $response = $this->actingAs($this->pembimbing)
            ->postJson(route('pembimbing.logbook.bulk-review'), [
                'logbook_ids' => [$lb1->id, $lb2->id],
                'catatan_pembimbing' => 'Disetujui serentak oleh pembimbing.',
            ]);

        $response->assertOk();

        $event = RealtimeEvent::where('user_id', $this->peserta->id)
            ->where('event_type', RealtimeEventTypeEnum::LogbookDireviu->value)
            ->latest('id')
            ->first();

        $this->assertNotNull($event);
        $this->assertSame('Logbook Harian Disetujui', $event->payload['title']);
        $this->assertSame(StatusLogbookEnum::Disetujui->value, $event->payload['status']);
    }

    public function test_peserta_update_logbook_revisi_menerbitkan_event_ke_pembimbing(): void
    {
        $logbook = Logbook::create([
            'penempatan_id' => $this->penempatan->id,
            'minggu_ke' => 1,
            'tanggal' => Carbon::today(),
            'kegiatan' => 'Kegiatan draf awal sebelum revisi.',
            'status' => StatusLogbookEnum::PerluRevisi,
        ]);

        $response = $this->actingAs($this->peserta)
            ->putJson(route('peserta.logbook.update', $logbook->id), [
                'tanggal' => Carbon::today()->toDateString(),
                'kegiatan' => 'Kegiatan revisi telah diperbaiki dan dilengkapi dengan foto.',
                'link_laporan' => 'https://drive.google.com/file/d/sample-logbook-doc/view',
            ]);

        $response->assertOk();

        $event = RealtimeEvent::where('user_id', $this->pembimbing->id)
            ->where('event_type', RealtimeEventTypeEnum::LogbookBaru->value)
            ->latest('id')
            ->first();

        $this->assertNotNull($event, 'Event SSE logbook_baru harus terbit ke pembimbing saat revisi dikirim.');
        $this->assertSame('Revisi Logbook Masuk', $event->payload['title']);
        $this->assertStringContainsString('Mentee Realtime telah memperbarui logbook', $event->payload['message']);
    }

    public function test_validasi_tautan_aman_menolak_http_biasa_dan_ip_privat_ssrf(): void
    {
        $rule = new SafeExternalUrlRule;

        // 1. Tautan aman HTTPS publik -> Lolos
        $failed = false;
        $rule->validate('link', 'https://drive.google.com/file/d/123/view', function () use (&$failed) {
            $failed = true;
        });
        $this->assertFalse($failed, 'HTTPS publik valid harus lolos.');

        // 2. Tautan HTTP tidak aman -> Gagal
        $errorMessage = '';
        $rule->validate('link', 'http://contoh.com/dokumen', function ($msg) use (&$errorMessage) {
            $errorMessage = $msg;
        });
        $this->assertStringContainsString('HTTPS', $errorMessage);

        // 3. Tautan internal localhost -> Gagal (Anti-SSRF)
        $errorMessage = '';
        $rule->validate('link', 'https://localhost/exploit', function ($msg) use (&$errorMessage) {
            $errorMessage = $msg;
        });
        $this->assertStringContainsString('Anti-SSRF', $errorMessage);

        // 4. Tautan loopback 127.0.0.1 -> Gagal (Anti-SSRF)
        $errorMessage = '';
        $rule->validate('link', 'https://127.0.0.1:8080/internal', function ($msg) use (&$errorMessage) {
            $errorMessage = $msg;
        });
        $this->assertStringContainsString('Anti-SSRF', $errorMessage);

        // 5. Tautan cloud metadata 169.254.169.254 -> Gagal (Anti-SSRF)
        $errorMessage = '';
        $rule->validate('link', 'https://169.254.169.254/latest/meta-data', function ($msg) use (&$errorMessage) {
            $errorMessage = $msg;
        });
        $this->assertStringContainsString('Anti-SSRF', $errorMessage);

        // 6. Skema javascript berbahaya -> Gagal
        $errorMessage = '';
        $rule->validate('link', 'javascript:alert(1)', function ($msg) use (&$errorMessage) {
            $errorMessage = $msg;
        });
        $this->assertNotEmpty($errorMessage);
    }
}
