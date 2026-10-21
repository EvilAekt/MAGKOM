<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AlurMagangPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_halaman_panduan_alur_bisa_diakses_publik_tanpa_login(): void
    {
        $response = $this->get(route('panduan-alur'));

        $response->assertStatus(200);
        $response->assertSee('Peta Interaktif Alur &amp; Siklus Magang', false);
        $response->assertSee('Diskominfo Sleman');

        // Memastikan SEO meta tags terpasang
        $response->assertSee('meta name="description"', false);
        $response->assertSee('property="og:title"', false);
    }

    public function test_halaman_panduan_alur_memuat_kpi_strip_dan_simulator_interaktif(): void
    {
        $response = $this->get(route('panduan-alur'));

        $response->assertStatus(200);
        // KPI Strip PRD
        $response->assertSee('4 Fase');
        $response->assertSee('1–3 Hari Kerja');
        $response->assertSee('100 Poin');
        $response->assertSee('QR Code Resmi');

        // Simulator controls
        $response->assertSee('Semua Jenjang');
        $response->assertSee('Siswa SMK (PKL)');
        $response->assertSee('Mahasiswa (D3/S1)');
        $response->assertSee('Mandiri (1 Orang)');
        $response->assertSee('Kelompok (2–5 Orang)');

        // Toggle Graph vs Roadmap View
        $response->assertSee('id="btn-view-graph"', false);
        $response->assertSee('id="btn-view-roadmap"', false);
        $response->assertSee('Diagram Graf');
        $response->assertSee('Roadmap Linier');
    }

    public function test_halaman_panduan_alur_memuat_svg_flow_graph_dan_inspector(): void
    {
        $response = $this->get(route('panduan-alur'));

        $response->assertStatus(200);
        // DAG Simulation Toolbar & Stepper
        $response->assertSee('dag-sim-toolbar');
        $response->assertSee('Mulai Simulasi Alur');
        $response->assertSee('Sebelumnya');
        $response->assertSee('Selanjutnya');
        $response->assertSee('Reset');
        $response->assertSee('journey-stepper-track');
        $response->assertSee('dagProgressBar');

        // Flowchart Canvas & Zoom Controls
        $response->assertSee('flowchart-canvas-wrapper');
        $response->assertSee('flowchart-canvas-inner');
        $response->assertSee('flowchart-zoom-group');
        $response->assertSee('id="btnZoomIn"', false);
        $response->assertSee('id="btnZoomOut"', false);
        $response->assertSee('id="btnZoomReset"', false);

        // Terminator Nodes
        $response->assertSee('id="flow-node-start"', false);
        $response->assertSee('START &bull; Akses Portal MagKom', false);
        $response->assertSee('id="flow-node-finish"', false);
        $response->assertSee('FINISH &bull; Sertifikat QR &amp; Lembar Nilai', false);

        // Journey Board container & nodes
        $response->assertSee('journey-board-container');
        $response->assertSee('dag-canvas-container');
        $response->assertSee('id="node-1"', false);
        $response->assertSee('id="node-2"', false);
        $response->assertSee('id="node-3"', false);
        $response->assertSee('id="loopbackTargetAnchor"', false);
        $response->assertSee('id="node-4"', false);
        $response->assertSee('decision-rhombus-gateway');
        $response->assertSee('Gerbang Keputusan Evaluasi Dokumen Admin');
        $response->assertSee('id="node-waiting"', false);
        $response->assertSee('id="node-diterima"', false);
        $response->assertSee('id="node-perbaikan"', false);
        $response->assertSee('Loopback ke Tahap 03');
        $response->assertSee('id="node-5"', false);
        $response->assertSee('id="node-6"', false);
        $response->assertSee('id="node-7"', false);

        // Side Inspector Card
        $response->assertSee('id="inspectorCard"', false);
        $response->assertSee('id="inspectorTitle"', false);
        $response->assertSee('id="inspectorDesc"', false);
    }

    public function test_halaman_panduan_alur_memuat_aturan_bisnis_prd_dan_faq(): void
    {
        $response = $this->get(route('panduan-alur'));

        $response->assertStatus(200);
        // Aturan bisnis PRD (BR-01, BR-09, BR-10, AC-12)
        $response->assertSee('Pertanyaan Seputar Alur &amp; Aturan Magang', false);
        $response->assertSee('Aturan Anti-Monopoli BR-01');
        $response->assertSee('First-Come First-Served (FIFO)');
        $response->assertSee('BR-09');
        $response->assertSee('Masa Tenggang Baca-Saja selama 30 Hari Kalender (BR-10)');
        $response->assertSee('QR Code Keabsahan Dokumen (AC-12)');

        // CTA Banner
        $response->assertSee('Siap Bergabung dengan Tim Diskominfo?');
        $response->assertSee('Lihat Kuota &amp; Jadwal Magang', false);
    }

    public function test_halaman_panduan_alur_dapat_diakses_oleh_user_yang_telah_login(): void
    {
        $peserta = User::factory()->create([
            'role' => RoleEnum::Peserta,
            'status_aktif' => true,
        ]);

        $response = $this->actingAs($peserta)->get(route('panduan-alur'));

        $response->assertStatus(200);
        $response->assertSee($peserta->nama);
        $response->assertSee('Peserta');
    }
}
