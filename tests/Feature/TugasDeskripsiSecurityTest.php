<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\JalurEnum;
use App\Enums\KompleksitasEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\StatusTugasEnum;
use App\Enums\SumberPenempatanEnum;
use App\Models\Bidang;
use App\Models\Jurusan;
use App\Models\Penempatan;
use App\Models\Tugas;
use App\Models\TugasTemplate;
use App\Models\User;
use App\Support\SafeHtmlSanitizer;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class TugasDeskripsiSecurityTest extends TestCase
{
    use RefreshDatabase;

    private User $pembimbing;

    private User $peserta;

    private Penempatan $penempatan;

    protected function setUp(): void
    {
        parent::setUp();

        $bidang = Bidang::create([
            'nama_bidang' => 'Bidang Aplikasi Informatika',
            'deskripsi' => 'Pengembangan Software SPBE',
            'aktif' => true,
        ]);

        $jurusan = Jurusan::create([
            'nama_jurusan' => 'Teknik Informatika',
            'kategori' => 'perguruan_tinggi',
            'aktif' => true,
        ]);

        $this->pembimbing = User::factory()->create([
            'nama' => 'Budi Pembimbing',
            'email' => 'pembimbing@magkom.go.id',
            'role' => RoleEnum::Pembimbing,
            'bidang_id' => $bidang->id,
            'status_aktif' => true,
        ]);

        $this->peserta = User::factory()->create([
            'nama' => 'Ahmad Peserta',
            'email' => 'peserta@magkom.go.id',
            'role' => RoleEnum::Peserta,
            'status_aktif' => true,
        ]);

        $this->penempatan = Penempatan::create([
            'pembimbing_id' => $this->pembimbing->id,
            'peserta_id' => $this->peserta->id,
            'bidang_id' => $bidang->id,
            'jurusan_id' => $jurusan->id,
            'jalur' => JalurEnum::Umum,
            'sumber' => SumberPenempatanEnum::Online,
            'status' => StatusPenempatanEnum::Aktif,
            'tgl_mulai' => Carbon::now()->subDays(5)->toDateString(),
            'tgl_selesai' => Carbon::now()->addMonths(2)->toDateString(),
        ]);
    }

    public function test_safe_html_sanitizer_removes_dangerous_tags_and_attributes(): void
    {
        $payload = '<p>Instruksi tugas:</p><script>alert("XSS")</script><img src="x" onerror="alert(1)"><b onclick="steal()">Tebal</b><a href="javascript:void(0)">Link</a>';
        $cleaned = SafeHtmlSanitizer::sanitize($payload);

        $this->assertStringNotContainsString('<script', $cleaned);
        $this->assertStringNotContainsString('alert', $cleaned);
        $this->assertStringNotContainsString('<img', $cleaned);
        $this->assertStringNotContainsString('onerror', $cleaned);
        $this->assertStringNotContainsString('onclick', $cleaned);
        $this->assertStringNotContainsString('javascript:', $cleaned);

        $this->assertStringContainsString('<p>Instruksi tugas:</p>', $cleaned);
        $this->assertStringContainsString('<b>Tebal</b>', $cleaned);
        $this->assertStringContainsString('Link', $cleaned);
    }

    public function test_safe_html_sanitizer_preserves_valid_rich_formatting(): void
    {
        $validHtml = '<p>Silakan kerjakan poin berikut:</p><ol><li><b>Tahap 1:</b> Analisis</li><li><i>Tahap 2:</i> Implementasi</li></ol><br>';
        $cleaned = SafeHtmlSanitizer::sanitize($validHtml);

        $this->assertSame($validHtml, $cleaned);
    }

    public function test_safe_html_sanitizer_cleans_nested_unwrapped_xss_payloads(): void
    {
        // 1. Kasus wajib PRD: <div><img src=x onerror=alert(1)></div>
        $nestedImg = '<div><img src=x onerror=alert(1)></div>';
        $cleanedImg = SafeHtmlSanitizer::sanitize($nestedImg);
        $this->assertStringNotContainsString('img', $cleanedImg);
        $this->assertStringNotContainsString('onerror', $cleanedImg);
        $this->assertStringNotContainsString('alert', $cleanedImg);
        $this->assertSame('', $cleanedImg);

        // 2. Bersarang ganda: <div><span><img src="x" onerror="alert(1)"></span></div>
        $doubleNested = '<div><span><img src="x" onerror="alert(1)"></span></div>';
        $cleanedDouble = SafeHtmlSanitizer::sanitize($doubleNested);
        $this->assertSame('', $cleanedDouble);

        // 3. Script di dalam wrapper div yang tidak diizinkan: <div><script>alert(1)</script>Petunjuk Resmi</div>
        $nestedScript = '<div><script>alert(1)</script>Petunjuk Resmi</div>';
        $cleanedScript = SafeHtmlSanitizer::sanitize($nestedScript);
        $this->assertStringNotContainsString('<script', $cleanedScript);
        $this->assertStringNotContainsString('alert', $cleanedScript);
        $this->assertSame('Petunjuk Resmi', $cleanedScript);

        // 4. Link javascript bersarang gambar berbahaya
        $linkPayload = '<a href="javascript:alert(1)"><img src="x" onerror="alert(2)">Tautan Aman</a>';
        $cleanedLink = SafeHtmlSanitizer::sanitize($linkPayload);
        $this->assertStringNotContainsString('javascript', $cleanedLink);
        $this->assertStringNotContainsString('alert', $cleanedLink);
        $this->assertStringNotContainsString('<a', $cleanedLink);
        $this->assertStringNotContainsString('<img', $cleanedLink);
        $this->assertSame('Tautan Aman', $cleanedLink);

        // 5. SVG / MathML bersarang
        $svgPayload = '<div><svg><g onload="alert(1)"></g></svg><p>Paragraf</p></div>';
        $cleanedSvg = SafeHtmlSanitizer::sanitize($svgPayload);
        $this->assertStringNotContainsString('svg', $cleanedSvg);
        $this->assertStringNotContainsString('onload', $cleanedSvg);
        $this->assertSame('<p>Paragraf</p>', $cleanedSvg);

        // 6. Komentar HTML dan atribut berbahaya pada tag aman
        $attrPayload = '<!-- comment --><p onclick="steal()" style="color:red" class="injected"><b>Format Valid</b></p>';
        $cleanedAttr = SafeHtmlSanitizer::sanitize($attrPayload);
        $this->assertStringNotContainsString('<!--', $cleanedAttr);
        $this->assertStringNotContainsString('onclick', $cleanedAttr);
        $this->assertStringNotContainsString('style', $cleanedAttr);
        $this->assertStringNotContainsString('class', $cleanedAttr);
        $this->assertSame('<p><b>Format Valid</b></p>', $cleanedAttr);
    }

    public function test_tugas_creation_sanitizes_html_injection_in_deskripsi(): void
    {
        $response = $this->actingAs($this->pembimbing)->post(route('pembimbing.tugas.store'), [
            'penempatan_ids' => [$this->penempatan->id],
            'judul' => 'Tugas Keamanan Data',
            'deskripsi' => '<p>Uji petunjuk</p><script>alert("pwned")</script><b onmouseover="alert(1)">Penting</b>',
            'kategori' => 'Keamanan Informasi',
            'kompleksitas' => 'sedang',
            'deadline' => Carbon::tomorrow()->toDateString(),
            'aksi_publikasi' => 'sekarang',
        ]);

        $response->assertRedirect();

        $tugas = Tugas::where('judul', 'Tugas Keamanan Data')->first();
        $this->assertNotNull($tugas);
        $this->assertStringNotContainsString('<script', $tugas->deskripsi);
        $this->assertStringNotContainsString('alert', $tugas->deskripsi);
        $this->assertStringNotContainsString('onmouseover', $tugas->deskripsi);
        $this->assertStringContainsString('<p>Uji petunjuk</p>', $tugas->deskripsi);
        $this->assertStringContainsString('<b>Penting</b>', $tugas->deskripsi);
    }

    public function test_tugas_creation_rejects_deskripsi_exceeding_max_characters(): void
    {
        $tooLong = Str::repeat('A', 10001);

        $response = $this->actingAs($this->pembimbing)->post(route('pembimbing.tugas.store'), [
            'penempatan_ids' => [$this->penempatan->id],
            'judul' => 'Tugas Deskripsi Terlalu Panjang',
            'deskripsi' => $tooLong,
            'kategori' => 'Pemrograman Web',
            'kompleksitas' => 'sedang',
            'deadline' => Carbon::tomorrow()->toDateString(),
        ]);

        $response->assertSessionHasErrors(['deskripsi']);
    }

    public function test_historical_malicious_deskripsi_is_sanitized_in_edit_and_show_views(): void
    {
        $tugas = Tugas::create([
            'penempatan_id' => $this->penempatan->id,
            'pembimbing_id' => $this->pembimbing->id,
            'judul' => 'Tugas Historis Payload',
            'deskripsi' => '<p>Rincian:</p><script>alert("historical")</script><img src=x onerror=alert("img")>',
            'kategori' => 'Audit',
            'kompleksitas' => KompleksitasEnum::Sedang,
            'deadline' => Carbon::tomorrow()->toDateString(),
            'status' => StatusTugasEnum::Belum,
        ]);

        // 1. Tampilan Edit Pembimbing
        $editResponse = $this->actingAs($this->pembimbing)->get(route('pembimbing.tugas.edit', $tugas));
        $editResponse->assertOk();
        $editContent = $editResponse->getContent();
        $this->assertStringNotContainsString('<script>alert("historical")</script>', $editContent);
        $this->assertStringNotContainsString('onerror=alert', $editContent);

        // 2. Tampilan Show Pembimbing
        $showPembimbingResponse = $this->actingAs($this->pembimbing)->get(route('pembimbing.tugas.show', $tugas));
        $showPembimbingResponse->assertOk();
        $showContent = $showPembimbingResponse->getContent();
        $this->assertStringNotContainsString('<script>alert("historical")</script>', $showContent);
        $this->assertStringNotContainsString('onerror=alert', $showContent);

        // 3. Tampilan Show Peserta
        $showPesertaResponse = $this->actingAs($this->peserta)->get(route('peserta.tugas.show', $tugas));
        $showPesertaResponse->assertOk();
        $pesertaContent = $showPesertaResponse->getContent();
        $this->assertStringNotContainsString('<script>alert("historical")</script>', $pesertaContent);
        $this->assertStringNotContainsString('onerror=alert', $pesertaContent);
    }

    public function test_tugas_template_sanitizes_deskripsi(): void
    {
        $response = $this->actingAs($this->pembimbing)->post(route('pembimbing.tugas.template.store'), [
            'judul' => 'Template Tugas Bersih',
            'deskripsi' => '<p>Template instruksi:</p><script>alert("template")</script><b onclick="alert(1)">Format</b>',
            'kategori' => 'Pemrograman Web',
            'kompleksitas' => 'rendah',
        ]);

        $response->assertRedirect();

        $template = TugasTemplate::where('judul', 'Template Tugas Bersih')->first();
        $this->assertNotNull($template);
        $this->assertStringNotContainsString('<script', $template->deskripsi);
        $this->assertStringNotContainsString('onclick', $template->deskripsi);
        $this->assertStringContainsString('<b>Format</b>', $template->deskripsi);
    }
}
