<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\StatusPendaftaranEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\StatusTugasEnum;
use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

class IndustrialBladeComponentsTest extends TestCase
{
    /** @test */
    public function badge_component_renders_correct_tokens_for_various_domain_statuses(): void
    {
        // 1. Status Diterima (Hijau/Emerald)
        $renderedDiterima = Blade::render('<x-badge :status="$status" />', [
            'status' => StatusPendaftaranEnum::Diterima,
        ]);
        $this->assertStringContainsString('bg-emerald-50', $renderedDiterima);
        $this->assertStringContainsString('Diterima', $renderedDiterima);

        // 2. Status Menunggu (Amber)
        $renderedMenunggu = Blade::render('<x-badge :status="$status" />', [
            'status' => StatusPendaftaranEnum::Diajukan,
        ]);
        $this->assertStringContainsString('bg-amber-50', $renderedMenunggu);

        // 3. Status Ditolak (Rose/Red)
        $renderedDitolak = Blade::render('<x-badge :status="$status" />', [
            'status' => StatusPendaftaranEnum::Ditolak,
        ]);
        $this->assertStringContainsString('bg-rose-50', $renderedDitolak);
        $this->assertStringContainsString('Ditolak', $renderedDitolak);

        // 4. Status Aktif Penempatan
        $renderedAktif = Blade::render('<x-badge :status="$status" />', [
            'status' => StatusPenempatanEnum::Aktif,
        ]);
        $this->assertStringContainsString('bg-emerald-50', $renderedAktif);

        // 5. Status Selesai Tugas
        $renderedSelesai = Blade::render('<x-badge :status="$status" />', [
            'status' => StatusTugasEnum::Selesai,
        ]);
        $this->assertStringContainsString('bg-emerald-50', $renderedSelesai);
    }

    /** @test */
    public function alert_component_renders_with_proper_semantic_roles(): void
    {
        $renderedAlert = Blade::render(
            '<x-alert type="success" title="Berhasil">Data penempatan berhasil diperbarui.</x-alert>'
        );
        $this->assertStringContainsString('role="alert"', $renderedAlert);
        $this->assertStringContainsString('bg-emerald-50', $renderedAlert);
        $this->assertStringContainsString('Berhasil', $renderedAlert);
        $this->assertStringContainsString('Data penempatan berhasil diperbarui.', $renderedAlert);

        $renderedDanger = Blade::render(
            '<x-alert type="danger" title="Gagal">Terjadi kendala sistem.</x-alert>'
        );
        $this->assertStringContainsString('bg-rose-50', $renderedDanger);
        $this->assertStringContainsString('Gagal', $renderedDanger);
    }

    /** @test */
    public function stat_card_component_renders_accessible_kpi_information(): void
    {
        $renderedStat = Blade::render(
            '<x-stat-card title="Total Pendaftar" value="128" subtitle="Tahun 2026" color="blue" trend="+12%" />'
        );
        $this->assertStringContainsString('Total Pendaftar', $renderedStat);
        $this->assertStringContainsString('128', $renderedStat);
        $this->assertStringContainsString('Tahun 2026', $renderedStat);
        $this->assertStringContainsString('+12%', $renderedStat);
    }

    /** @test */
    public function empty_state_component_renders_cleanly_with_action_slot(): void
    {
        $renderedEmpty = Blade::render(
            '<x-empty-state title="Belum Ada Berkas" description="Silakan unggah berkas pendaftaran."><a href="#">Unggah Sekarang</a></x-empty-state>'
        );
        $this->assertStringContainsString('Belum Ada Berkas', $renderedEmpty);
        $this->assertStringContainsString('Silakan unggah berkas pendaftaran.', $renderedEmpty);
        $this->assertStringContainsString('Unggah Sekarang', $renderedEmpty);
    }
}
