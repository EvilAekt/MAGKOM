<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Support\SafeHtmlSanitizer;
use PHPUnit\Framework\TestCase;

class SafeHtmlSanitizerPhp83Test extends TestCase
{
    public function test_normal_and_nested_html_formatting_preserved(): void
    {
        $input = '<p>Deskripsi tugas <b>tebal</b>, <i>miring</i>, dan <u>garis bawah</u>.</p>';
        $cleaned = SafeHtmlSanitizer::sanitize($input);

        $this->assertStringContainsString('<b>tebal</b>', $cleaned);
        $this->assertStringContainsString('<i>miring</i>', $cleaned);
        $this->assertStringContainsString('<u>garis bawah</u>', $cleaned);
    }

    public function test_bahasa_indonesia_dan_karakter_aksen_tidak_rusak(): void
    {
        $input = '<p>Instruksi magang: Mempelajari tata kelola e-Government & kominfo. Menghadiri rapat café “Sleman”.</p>';
        $cleaned = SafeHtmlSanitizer::sanitize($input);

        $this->assertStringContainsString('tata kelola e-Government &amp; kominfo', $cleaned);
        $this->assertStringContainsString('café', $cleaned);
    }

    public function test_unicode_dan_emoji_tetap_utuh(): void
    {
        $input = '<p>Semangat magang! 🚀🇮🇩 Selamat bekerja ✨ 日本語テスト</p>';
        $cleaned = SafeHtmlSanitizer::sanitize($input);

        $this->assertStringContainsString('🚀🇮🇩', $cleaned);
        $this->assertStringContainsString('✨', $cleaned);
        $this->assertStringContainsString('日本語テスト', $cleaned);
    }

    public function test_malformed_html_is_safely_normalized(): void
    {
        $input = '<p>Paragraf belum tertutup <b>tebal tanpa penutup';
        $cleaned = SafeHtmlSanitizer::sanitize($input);

        $this->assertNotEmpty($cleaned);
        $this->assertStringContainsString('<b>tebal tanpa penutup</b>', $cleaned);
    }

    public function test_strips_malicious_script_and_event_handlers(): void
    {
        $input = '<p>Teks biasa <script>alert("XSS")</script><img src="x" onerror="alert(1)"> Selesai</p>';
        $cleaned = SafeHtmlSanitizer::sanitize($input);

        $this->assertStringNotContainsString('<script>', $cleaned);
        $this->assertStringNotContainsString('alert(', $cleaned);
        $this->assertStringNotContainsString('onerror', $cleaned);
        $this->assertStringContainsString('Teks biasa', $cleaned);
        $this->assertStringContainsString('Selesai', $cleaned);
    }

    public function test_disallowed_javascript_uri_is_neutralized(): void
    {
        $input = '<a href="javascript:alert(document.cookie)">Klik saya</a>';
        $cleaned = SafeHtmlSanitizer::sanitize($input);

        $this->assertStringNotContainsString('javascript:', $cleaned);
    }
}
