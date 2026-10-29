<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PetaAlurPageTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Uji bahwa halaman mandiri peta alur interaktif dapat diakses publik dan memuat seluruh scene diorama.
     */
    public function test_peta_alur_page_renders_standalone_fullscreen_interactive_diorama(): void
    {
        $response = $this->get(route('peta-alur'));

        $response->assertStatus(200);

        // Verifikasi stylesheet fullscreen
        $response->assertSee('css/peta-alur-fullscreen.css');

        // Verifikasi navigasi kembali ke beranda
        $response->assertSee(route('landing'));
        $response->assertSee('Simulator Magang Diskominfo Sleman');

        // Verifikasi kontrol audio toggle dan tour otomatis telah dihilangkan
        $response->assertSee('id="btnModalAudioToggle"', false);
        $response->assertDontSee('id="btnSimAutoplay"', false);

        // Verifikasi kanvas panorama 16:9
        $response->assertSee('id="modalRoadCanvasWrap"', false);
        $response->assertSee('id="modalRoadCanvas"', false);
        $response->assertSee('panoramic-svg-canvas', false);

        // Verifikasi elemen visual lanskap Sleman & Diskominfo
        $response->assertSee('id="modalMerapiBackdrop"', false);
        $response->assertSee('merapi-steam-1', false);
        $response->assertSee('merapi-steam-2', false);
        $response->assertSee('road-marking-animated', false);
        $response->assertSee('tree-swaying', false);
        $response->assertSee('flag-waving', false);
        $response->assertSee('id="diskominfoHubScene"', false);
        $response->assertSee('class="car-headlight-beam"', false);
        $response->assertSee('id="modalRoadCarGroup"', false);

        // Verifikasi pin GPS raksasa dan tag di atap mobil telah dihapus agar bodi mobil utuh
        $response->assertDontSee('id="modalRoadGpsPin"', false);
        $response->assertDontSee('id="modalRoadCarTag"', false);

        // Verifikasi anomali bola-bola biru mengembang (cp-pulse-halo) telah dihapus
        $response->assertDontSee('cp-pulse-halo', false);

        // Verifikasi Cockpit Bar lama telah dihapus agar ruang visual lapang dan bersih
        $response->assertDontSee('id="driveCockpitBar"', false);
        $response->assertDontSee('id="btnDrivePrev"', false);
        $response->assertDontSee('id="btnDriveNext"', false);
        $response->assertDontSee('id="roadDriveScrubber"', false);
        $response->assertDontSee('id="cockpitPosText"', false);

        // Verifikasi Kontrol Kemudi Virtual Analog Joystick & Animasi 2D
        $response->assertSee('id="gameAnalogContainer"', false);
        $response->assertSee('id="analogBase"', false);
        $response->assertSee('id="analogThumb"', false);
        $response->assertSee('id="carWheelRear"', false);
        $response->assertSee('id="carWheelFront"', false);
        $response->assertSee('pine-forest-group', false);
        $response->assertSee('wildflowers-group', false);
        $response->assertSee('animated-pedestrian', false);

        // Verifikasi 4 karakter animasi hidup
        $response->assertSee('id="modalCharacterScene"', false);
        $response->assertSee('id="charScene1"', false);
        $response->assertSee('id="charScene2"', false);
        $response->assertSee('id="charScene3"', false);
        $response->assertSee('id="charScene4"', false);

        // Verifikasi Pop-Up Speech Bubble Gaya Webtoon di atas Pos Checkpoint
        $response->assertSee('webtoon-speech-bubble', false);
        $response->assertSee('id="webtoonBubble1"', false);
        $response->assertSee('id="webtoonBubble2"', false);
        $response->assertSee('id="webtoonBubble3"', false);
        $response->assertSee('id="webtoonBubble4"', false);

        // Verifikasi Kartu HUD Samping Lama dan Tab Bawah Telah Dihapus
        $response->assertDontSee('id="missionHudOverlay"', false);
        $response->assertDontSee('class="in-scene-stage-hud"', false);
        $response->assertDontSee('class="peta-alur-bottom-bar"', false);
        $response->assertDontSee('id="roadTab1"', false);

        // Verifikasi Footer Minimalis Selaras Landing Page Utama
        $response->assertSee('class="peta-alur-mini-footer"', false);
        $response->assertSee('MAGKOM — Sistem Penerimaan Mahasiswa Magang.');

        // Verifikasi kontrol zoom telah dihilangkan agar kanvas bersih
        $response->assertDontSee('id="btnRoadZoomIn"', false);
        $response->assertDontSee('id="btnRoadZoomOut"', false);
        $response->assertDontSee('id="btnRoadZoomReset"', false);

        // Verifikasi pengayaan visual ujung kanvas (Gapura Barat, Gedung Pemkab Timur, dan Lampu Jalan)
        $response->assertSee('id="westSlemanGateScene"', false);
        $response->assertSee('id="eastCivicPlazaScene"', false);
        $response->assertSee('id="streetLampsTrack"', false);
        $response->assertSee('class="street-lamp-glow"', false);

        // Verifikasi filter bulan dan tanggal sudah dihapus dari tampilan
        $response->assertDontSee('id="modalFilterBulan"', false);
        $response->assertDontSee('15 Mei – 31 Mei 2026');

        // Verifikasi gedung tematik tiap pos tetap ada sebagai ilustrasi bersih tanpa plang/teks penjelasan
        $response->assertSee('id="gedungPos1Scene"', false);
        $response->assertDontSee('REGISTRATION CENTER', false);
        $response->assertSee('id="gedungPos2Scene"', false);
        $response->assertDontSee('LAYANAN SELEKSI &amp; PTSP', false);
        $response->assertSee('id="gedungPos3Scene"', false);
        $response->assertDontSee('CYBER INNOVATION LAB', false);
        $response->assertDontSee('DISKOMINFO SLEMAN', false);

        // Verifikasi karakter manusia animasi utuh (full body) dan rumput segar bertekstur
        $response->assertSee('char-wave-arm', false);
        $response->assertSee('grass-tufts-group', false);

        // Verifikasi tombol klakson dan efek gelombang visual klakson mobil
        $response->assertSee('id="btnSteerHorn"', false);
        $response->assertSee('id="carHornRipples"', false);
        $response->assertSee('triggerCarHorn', false);
        $response->assertSee('playCarHorn', false);

        // Verifikasi konten tahapan dalam Webtoon Bubble
        $response->assertSee('REGISTRASI AKUN', false);
        $response->assertSee('ADMINISTRASI & SELEKSI', false);
        $response->assertSee('PELAKSANAAN MAGANG', false);
        $response->assertSee('KELULUSAN & SERTIFIKAT', false);
    }
}
