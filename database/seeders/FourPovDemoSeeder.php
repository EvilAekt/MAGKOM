<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\JalurEnum;
use App\Enums\JenjangEnum;
use App\Enums\KompleksitasEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusLogbookEnum;
use App\Enums\StatusPendaftaranEnum;
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
use App\Models\Logbook;
use App\Models\Pendaftaran;
use App\Models\Penempatan;
use App\Models\PesertaProfil;
use App\Models\ProgresLog;
use App\Models\Tugas;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class FourPovDemoSeeder extends Seeder
{
    /**
     * Seed 4 akun POV demo:
     * 1. pov.tim.aktif@gmail.com (Tim Aktif, 3 anggota termasuk ketua, tugas lengkap semua status)
     * 2. pov.individu.aktif@gmail.com (Individu Aktif, tugas lengkap semua status)
     * 3. pov.individu.selesai@gmail.com (Individu Selesai Magang + Surat Selesai)
     * 4. pov.tim.selesai@gmail.com (Tim Selesai Magang, 3 anggota termasuk ketua + Surat Selesai)
     * Password semua akun: Password123!
     */
    public function run(): void
    {
        if (app()->isProduction()) {
            throw new RuntimeException('Cannot run FourPovDemoSeeder in production environment.');
        }

        $now = Carbon::now('Asia/Jakarta');
        $password = Hash::make('Password123!');

        Storage::disk('local')->makeDirectory('surat-selesai');
        $suratPdfPath = 'surat-selesai/surat_selesai_pov_demo.pdf';
        $samplePdfContent = "%PDF-1.4\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj\n2 0 obj<</Type/Pages/Kids[3 0 R]/Count 1>>endobj\n3 0 obj<</Type/Page/MediaBox[0 0 595 842]/Parent 2 0 R/Resources<<>>>>endobj\nxref\n0 4\n0000000000 65535 f\n0000000009 00000 n\n0000000058 00000 n\n0000000115 00000 n\ntrailer<</Size 4/Root 1 0 R>>\nstartxref\n206\n%%EOF";

        if (! Storage::disk('local')->exists($suratPdfPath)) {
            Storage::disk('local')->put($suratPdfPath, $samplePdfContent);
        }

        $bidang = Bidang::where('aktif', true)->first() ?? Bidang::firstOrFail();
        $pembimbing = User::where('role', RoleEnum::Pembimbing)->where('status_aktif', true)->first()
            ?? User::where('role', RoleEnum::Pembimbing)->firstOrFail();
        $jurusan = Jurusan::where('aktif', true)->first() ?? Jurusan::firstOrFail();
        $bulanAktif = BulanMagang::where('aktif', true)->first() ?? BulanMagang::firstOrFail();
        $bulanSelesai = BulanMagang::where('aktif', false)->first() ?? $bulanAktif;

        $kuotaAktif = Kuota::firstOrCreate(
            ['bulan_magang_id' => $bulanAktif->id, 'jurusan_id' => $jurusan->id],
            ['jumlah' => 20, 'terisi' => 0, 'aktif' => true]
        );
        $kuotaSelesai = Kuota::firstOrCreate(
            ['bulan_magang_id' => $bulanSelesai->id, 'jurusan_id' => $jurusan->id],
            ['jumlah' => 20, 'terisi' => 0, 'aktif' => true]
        );

        DB::transaction(function () use (
            $now,
            $password,
            $suratPdfPath,
            $bidang,
            $pembimbing,
            $jurusan,
            $bulanAktif,
            $bulanSelesai,
            $kuotaAktif,
            $kuotaSelesai
        ) {
            // =========================================================================
            // 1. POV TIM AKTIF (3 ANGGOTA TERMASUK KETUA, TUGAS LENGKAP SEMUA STATUS)
            // =========================================================================
            $ketuaAktif = $this->upsertPeserta(
                email: 'pov.tim.aktif@gmail.com',
                nama: 'Rendra Wijaya',
                nim: '22/510001/TK/55001',
                institusi: 'Universitas Gadjah Mada',
                jurusanId: $jurusan->id,
                noHp: '081210001001',
                password: $password,
                now: $now
            );
            $a1Aktif = $this->upsertPeserta(
                email: 'pov.tim.aktif.a1@gmail.com',
                nama: 'Nadia Larasati',
                nim: '22/510002/TK/55002',
                institusi: 'Universitas Gadjah Mada',
                jurusanId: $jurusan->id,
                noHp: '081210001002',
                password: $password,
                now: $now
            );
            $a2Aktif = $this->upsertPeserta(
                email: 'pov.tim.aktif.a2@gmail.com',
                nama: 'Bagus Firmansyah',
                nim: '22/510003/TK/55003',
                institusi: 'Universitas Gadjah Mada',
                jurusanId: $jurusan->id,
                noHp: '081210001003',
                password: $password,
                now: $now
            );

            $timAktif = KelompokMagang::updateOrCreate(
                ['ketua_user_id' => $ketuaAktif->id],
                ['nama_kelompok' => 'Tim Arsitektur SPBE Sleman']
            );
            $timAktif->anggota()->delete();
            KelompokAnggota::create([
                'kelompok_magang_id' => $timAktif->id,
                'user_id' => $a1Aktif->id,
                'nama' => $a1Aktif->nama,
                'nim_nisn' => '22/510002/TK/55002',
                'no_hp' => '081210001002',
                'email' => $a1Aktif->email,
            ]);
            KelompokAnggota::create([
                'kelompok_magang_id' => $timAktif->id,
                'user_id' => $a2Aktif->id,
                'nama' => $a2Aktif->nama,
                'nim_nisn' => '22/510003/TK/55003',
                'no_hp' => '081210001003',
                'email' => $a2Aktif->email,
            ]);

            $pendaftaranTimAktif = Pendaftaran::updateOrCreate(
                ['user_id' => $ketuaAktif->id, 'bulan_magang_id' => $bulanAktif->id],
                [
                    'kuota_id' => $kuotaAktif->id,
                    'tipe' => 'kelompok',
                    'kelompok_magang_id' => $timAktif->id,
                    'durasi_bulan' => 3,
                    'status' => StatusPendaftaranEnum::Diterima,
                    'diajukan_at' => $now->copy()->subDays(25),
                    'diputuskan_at' => $now->copy()->subDays(20),
                    'catatan' => 'Permohonan tim disetujui untuk penempatan Bidang E-Government.',
                ]
            );

            $penTimKetua = $this->upsertPenempatan(
                peserta: $ketuaAktif,
                pendaftaran: $pendaftaranTimAktif,
                kuotaId: $kuotaAktif->id,
                bidangId: $bidang->id,
                pembimbingId: $pembimbing->id,
                tglMulai: $now->copy()->subDays(18),
                tglSelesai: $now->copy()->addDays(72),
                status: StatusPenempatanEnum::Aktif
            );
            $penTimA1 = $this->upsertPenempatan(
                peserta: $a1Aktif,
                pendaftaran: $pendaftaranTimAktif,
                kuotaId: $kuotaAktif->id,
                bidangId: $bidang->id,
                pembimbingId: $pembimbing->id,
                tglMulai: $now->copy()->subDays(18),
                tglSelesai: $now->copy()->addDays(72),
                status: StatusPenempatanEnum::Aktif
            );
            $penTimA2 = $this->upsertPenempatan(
                peserta: $a2Aktif,
                pendaftaran: $pendaftaranTimAktif,
                kuotaId: $kuotaAktif->id,
                bidangId: $bidang->id,
                pembimbingId: $pembimbing->id,
                tglMulai: $now->copy()->subDays(18),
                tglSelesai: $now->copy()->addDays(72),
                status: StatusPenempatanEnum::Aktif
            );

            $timMembers = [
                [$ketuaAktif->id, $penTimKetua->id],
                [$a1Aktif->id, $penTimA1->id],
                [$a2Aktif->id, $penTimA2->id],
            ];

            // Bersihkan tugas & logbook lama milik penempatan POV Tim Aktif agar idempoten
            Tugas::where('kelompok_magang_id', $timAktif->id)->delete();
            Tugas::where('penempatan_id', $penTimKetua->id)->delete();
            Logbook::where('penempatan_id', $penTimKetua->id)->delete();

            // 4 Tugas Tim (Selesai, Proses/Belum Lewat Tenggat, Baru Ditugaskan, Lewat Tenggat)
            $this->createTimTask(
                tim: $timAktif,
                members: $timMembers,
                judul: 'Pemetaan Arsitektur Layanan Publik Terpadu',
                deskripsi: 'Susun diagram alur integrasi antar-API dinas dan dokumen spesifikasi endpoint tim.',
                kategori: 'Proyek Tim',
                kompleksitas: KompleksitasEnum::Sedang,
                deadline: $now->copy()->addDays(4),
                status: StatusTugasEnum::Selesai,
                reviewStatus: StatusReviewProgresEnum::Diterima,
                pelaporId: $ketuaAktif->id,
                catatanProgres: 'Diagram arsitektur dan dokumentasi API tim telah selesai diunggah.',
                feedback: 'Struktur integrasi sangat rapi dan sudah memenuhi standar SPBE.',
                link: 'https://github.com/magkom-demo/spbe-architecture'
            );

            $this->createTimTask(
                tim: $timAktif,
                members: $timMembers,
                judul: 'Pengembangan Modul Dashboard Monitoring Kuota',
                deskripsi: 'Implementasikan komponen monitoring kuota real-time dan sinkronkan dengan rekan tim.',
                kategori: 'Proyek Tim',
                kompleksitas: KompleksitasEnum::Tinggi,
                deadline: $now->copy()->addDays(6),
                status: StatusTugasEnum::Proses,
                reviewStatus: StatusReviewProgresEnum::Menunggu,
                pelaporId: $a1Aktif->id,
                catatanProgres: 'Progres frontend dan API endpoint sudah 80%, mohon review pembimbing.',
                feedback: null,
                link: 'https://github.com/magkom-demo/quota-dashboard-pr'
            );

            $this->createTimTask(
                tim: $timAktif,
                members: $timMembers,
                judul: 'Audit Keamanan & Pengujian CSP Portal Layanan',
                deskripsi: 'Lakukan verifikasi header keamanan dan pengujian alur otorisasi multi-role bersama tim.',
                kategori: 'Proyek Tim',
                kompleksitas: KompleksitasEnum::Sedang,
                deadline: $now->copy()->addDays(10),
                status: StatusTugasEnum::Belum,
                reviewStatus: null,
                pelaporId: null,
                catatanProgres: null,
                feedback: null,
                link: null
            );

            $this->createTimTask(
                tim: $timAktif,
                members: $timMembers,
                judul: 'Sinkronisasi Data Master Kecamatan & Instansi',
                deskripsi: 'Lengkapi pembersihan data master wilayah untuk modul pelaporan dinas.',
                kategori: 'Proyek Tim',
                kompleksitas: KompleksitasEnum::Rendah,
                deadline: $now->copy()->subDays(3),
                status: StatusTugasEnum::Belum,
                reviewStatus: null,
                pelaporId: null,
                catatanProgres: null,
                feedback: null,
                link: null
            );

            // 4 Tugas Individu untuk Ketua Tim Aktif (Selesai, Proses, Baru Ditugaskan, Lewat Tenggat)
            $this->createIndividuTask(
                penempatanId: $penTimKetua->id,
                userId: $ketuaAktif->id,
                judul: 'Penyusunan Rencana Kerja & Pembagian Peran Tim',
                deskripsi: 'Buat dokumen pembagian peran teknis masing-masing anggota tim selama 3 bulan.',
                kompleksitas: KompleksitasEnum::Rendah,
                deadline: $now->copy()->addDays(3),
                status: StatusTugasEnum::Selesai,
                reviewStatus: StatusReviewProgresEnum::Diterima,
                catatanProgres: 'Dokumen pembagian peran tim telah diselesaikan.',
                feedback: 'Pembagian peran jelas dan terukur.',
                link: 'https://github.com/magkom-demo/team-charter'
            );

            $this->createIndividuTask(
                penempatanId: $penTimKetua->id,
                userId: $ketuaAktif->id,
                judul: 'Optimasi Query Laporan Bulanan Admin',
                deskripsi: 'Lakukan profiling query Eloquent dan hilangkan potensi N+1 pada halaman laporan.',
                kompleksitas: KompleksitasEnum::Sedang,
                deadline: $now->copy()->addDays(5),
                status: StatusTugasEnum::Proses,
                reviewStatus: StatusReviewProgresEnum::Menunggu,
                catatanProgres: 'Eager loading sudah ditambahkan, sedang menunggu review.',
                feedback: null,
                link: 'https://github.com/magkom-demo/query-optimization'
            );

            $this->createIndividuTask(
                penempatanId: $penTimKetua->id,
                userId: $ketuaAktif->id,
                judul: 'Dokumentasi Panduan Deployment Staging',
                deskripsi: 'Susun panduan langkah-langkah deployment aplikasi Laravel ke server staging.',
                kompleksitas: KompleksitasEnum::Rendah,
                deadline: $now->copy()->addDays(9),
                status: StatusTugasEnum::Belum,
                reviewStatus: null,
                catatanProgres: null,
                feedback: null,
                link: null
            );

            $this->createIndividuTask(
                penempatanId: $penTimKetua->id,
                userId: $ketuaAktif->id,
                judul: 'Rekapitulasi Catatan Rapat Koordinasi Mingguan',
                deskripsi: 'Kumpulkan notulensi rapat teknis mingguan bersama pembimbing lapangan.',
                kompleksitas: KompleksitasEnum::Rendah,
                deadline: $now->copy()->subDays(2),
                status: StatusTugasEnum::Belum,
                reviewStatus: null,
                catatanProgres: null,
                feedback: null,
                link: null
            );

            $this->seedSampleLogbooks($penTimKetua->id, $now);

            // =========================================================================
            // 2. POV INDIVIDU AKTIF (MURNI INDIVIDU, TUGAS LENGKAP SEMUA STATUS)
            // =========================================================================
            $individuAktif = $this->upsertPeserta(
                email: 'pov.individu.aktif@gmail.com',
                nama: 'Dina Maharani',
                nim: '22/520011/SV/22011',
                institusi: 'Universitas Negeri Yogyakarta',
                jurusanId: $jurusan->id,
                noHp: '081220002001',
                password: $password,
                now: $now
            );

            $pendaftaranIndividuAktif = Pendaftaran::updateOrCreate(
                ['user_id' => $individuAktif->id, 'bulan_magang_id' => $bulanAktif->id],
                [
                    'kuota_id' => $kuotaAktif->id,
                    'tipe' => 'mandiri',
                    'kelompok_magang_id' => null,
                    'durasi_bulan' => 3,
                    'status' => StatusPendaftaranEnum::Diterima,
                    'diajukan_at' => $now->copy()->subDays(22),
                    'diputuskan_at' => $now->copy()->subDays(18),
                    'catatan' => 'Permohonan magang individu disetujui.',
                ]
            );

            $penIndividuAktif = $this->upsertPenempatan(
                peserta: $individuAktif,
                pendaftaran: $pendaftaranIndividuAktif,
                kuotaId: $kuotaAktif->id,
                bidangId: $bidang->id,
                pembimbingId: $pembimbing->id,
                tglMulai: $now->copy()->subDays(16),
                tglSelesai: $now->copy()->addDays(74),
                status: StatusPenempatanEnum::Aktif
            );

            Tugas::where('penempatan_id', $penIndividuAktif->id)->delete();
            Logbook::where('penempatan_id', $penIndividuAktif->id)->delete();

            $this->createIndividuTask(
                penempatanId: $penIndividuAktif->id,
                userId: $individuAktif->id,
                judul: 'Perancangan Antarmuka Portal Layanan Publik',
                deskripsi: 'Buat wireframe dan komponen antarmuka responsif sesuai standar desain sistem.',
                kompleksitas: KompleksitasEnum::Sedang,
                deadline: $now->copy()->addDays(3),
                status: StatusTugasEnum::Selesai,
                reviewStatus: StatusReviewProgresEnum::Diterima,
                catatanProgres: 'Desain antarmuka responsif sudah selesai dan diuji.',
                feedback: 'Konsistensi komponen dan aksesibilitas sudah sangat baik.',
                link: 'https://github.com/magkom-demo/ui-portal-individu'
            );

            $this->createIndividuTask(
                penempatanId: $penIndividuAktif->id,
                userId: $individuAktif->id,
                judul: 'Integrasi Formulir Pengaduan Masyarakat',
                deskripsi: 'Implementasikan validasi input dan notifikasi status pengaduan secara real-time.',
                kompleksitas: KompleksitasEnum::Tinggi,
                deadline: $now->copy()->addDays(5),
                status: StatusTugasEnum::Proses,
                reviewStatus: StatusReviewProgresEnum::Menunggu,
                catatanProgres: 'Validasi backend dan frontend sudah selesai, menunggu verifikasi pembimbing.',
                feedback: null,
                link: 'https://github.com/magkom-demo/pengaduan-form'
            );

            $this->createIndividuTask(
                penempatanId: $penIndividuAktif->id,
                userId: $individuAktif->id,
                judul: 'Penyusunan Dokumentasi Teknis API',
                deskripsi: 'Dokumentasikan seluruh endpoint REST internal beserta contoh payload respons.',
                kompleksitas: KompleksitasEnum::Rendah,
                deadline: $now->copy()->addDays(8),
                status: StatusTugasEnum::Belum,
                reviewStatus: null,
                catatanProgres: null,
                feedback: null,
                link: null
            );

            $this->createIndividuTask(
                penempatanId: $penIndividuAktif->id,
                userId: $individuAktif->id,
                judul: 'Uji Fungsional & Audit Aksesibilitas WCAG',
                deskripsi: 'Lakukan pengujian kontras warna dan navigasi keyboard pada halaman utama.',
                kompleksitas: KompleksitasEnum::Sedang,
                deadline: $now->copy()->subDays(2),
                status: StatusTugasEnum::Belum,
                reviewStatus: null,
                catatanProgres: null,
                feedback: null,
                link: null
            );

            $this->seedSampleLogbooks($penIndividuAktif->id, $now);

            // =========================================================================
            // 3. POV INDIVIDU SELESAI MAGANG (+ SURAT SELESAI)
            // =========================================================================
            $individuSelesai = $this->upsertPeserta(
                email: 'pov.individu.selesai@gmail.com',
                nama: 'Farel Ramadhan',
                nim: '21/480021/TK/53021',
                institusi: 'Universitas Islam Indonesia',
                jurusanId: $jurusan->id,
                noHp: '081230003001',
                password: $password,
                now: $now
            );

            $pendaftaranIndividuSelesai = Pendaftaran::updateOrCreate(
                ['user_id' => $individuSelesai->id, 'bulan_magang_id' => $bulanSelesai->id],
                [
                    'kuota_id' => $kuotaSelesai->id,
                    'tipe' => 'mandiri',
                    'kelompok_magang_id' => null,
                    'durasi_bulan' => 3,
                    'status' => StatusPendaftaranEnum::Diterima,
                    'diajukan_at' => $now->copy()->subDays(100),
                    'diputuskan_at' => $now->copy()->subDays(95),
                    'catatan' => 'Permohonan magang individu disetujui.',
                ]
            );

            $penIndividuSelesai = $this->upsertPenempatan(
                peserta: $individuSelesai,
                pendaftaran: $pendaftaranIndividuSelesai,
                kuotaId: $kuotaSelesai->id,
                bidangId: $bidang->id,
                pembimbingId: $pembimbing->id,
                tglMulai: $now->copy()->subDays(90),
                tglSelesai: $now->copy()->subDays(5),
                status: StatusPenempatanEnum::Selesai,
                tglDitetapkanSelesai: $now->copy()->subDays(4),
                fileSuratSelesai: $suratPdfPath,
                tglUploadSuratSelesai: $now->copy()->subDays(3)
            );

            Tugas::where('penempatan_id', $penIndividuSelesai->id)->delete();
            Logbook::where('penempatan_id', $penIndividuSelesai->id)->delete();

            $this->createIndividuTask(
                penempatanId: $penIndividuSelesai->id,
                userId: $individuSelesai->id,
                judul: 'Pengembangan Sistem Rekap Presensi Magang',
                deskripsi: 'Bangun fitur rekap presensi bulanan dan ekspor laporan PDF.',
                kompleksitas: KompleksitasEnum::Tinggi,
                deadline: $now->copy()->subDays(20),
                status: StatusTugasEnum::Selesai,
                reviewStatus: StatusReviewProgresEnum::Diterima,
                catatanProgres: 'Fitur rekap dan ekspor PDF telah selesai 100%.',
                feedback: 'Hasil sangat memuaskan dan siap digunakan.',
                link: 'https://github.com/magkom-demo/rekap-presensi-final'
            );

            $this->createIndividuTask(
                penempatanId: $penIndividuSelesai->id,
                userId: $individuSelesai->id,
                judul: 'Laporan Akhir & Serah Terima Source Code',
                deskripsi: 'Lengkapi laporan akhir magang dan dokumentasi teknis aplikasi.',
                kompleksitas: KompleksitasEnum::Rendah,
                deadline: $now->copy()->subDays(7),
                status: StatusTugasEnum::Selesai,
                reviewStatus: StatusReviewProgresEnum::Diterima,
                catatanProgres: 'Laporan akhir dan dokumentasi serah terima telah diunggah.',
                feedback: 'Terima kasih atas kontribusinya selama magang di Diskominfo Sleman.',
                link: 'https://github.com/magkom-demo/laporan-akhir-farel'
            );

            $this->seedSampleLogbooks($penIndividuSelesai->id, $now->copy()->subDays(6));

            // =========================================================================
            // 4. POV TIM SELESAI MAGANG (3 ANGGOTA TERMASUK KETUA + SURAT SELESAI)
            // =========================================================================
            $ketuaSelesai = $this->upsertPeserta(
                email: 'pov.tim.selesai@gmail.com',
                nama: 'Gilang Kusuma',
                nim: '21/490031/SV/20031',
                institusi: 'UPN "Veteran" Yogyakarta',
                jurusanId: $jurusan->id,
                noHp: '081240004001',
                password: $password,
                now: $now
            );
            $a1Selesai = $this->upsertPeserta(
                email: 'pov.tim.selesai.a1@gmail.com',
                nama: 'Tania Putri',
                nim: '21/490032/SV/20032',
                institusi: 'UPN "Veteran" Yogyakarta',
                jurusanId: $jurusan->id,
                noHp: '081240004002',
                password: $password,
                now: $now
            );
            $a2Selesai = $this->upsertPeserta(
                email: 'pov.tim.selesai.a2@gmail.com',
                nama: 'Hafiz Nugroho',
                nim: '21/490033/SV/20033',
                institusi: 'UPN "Veteran" Yogyakarta',
                jurusanId: $jurusan->id,
                noHp: '081240004003',
                password: $password,
                now: $now
            );

            $timSelesai = KelompokMagang::updateOrCreate(
                ['ketua_user_id' => $ketuaSelesai->id],
                ['nama_kelompok' => 'Tim Portal Data Terbuka Sleman']
            );
            $timSelesai->anggota()->delete();
            KelompokAnggota::create([
                'kelompok_magang_id' => $timSelesai->id,
                'user_id' => $a1Selesai->id,
                'nama' => $a1Selesai->nama,
                'nim_nisn' => '21/490032/SV/20032',
                'no_hp' => '081240004002',
                'email' => $a1Selesai->email,
            ]);
            KelompokAnggota::create([
                'kelompok_magang_id' => $timSelesai->id,
                'user_id' => $a2Selesai->id,
                'nama' => $a2Selesai->nama,
                'nim_nisn' => '21/490033/SV/20033',
                'no_hp' => '081240004003',
                'email' => $a2Selesai->email,
            ]);

            $pendaftaranTimSelesai = Pendaftaran::updateOrCreate(
                ['user_id' => $ketuaSelesai->id, 'bulan_magang_id' => $bulanSelesai->id],
                [
                    'kuota_id' => $kuotaSelesai->id,
                    'tipe' => 'kelompok',
                    'kelompok_magang_id' => $timSelesai->id,
                    'durasi_bulan' => 3,
                    'status' => StatusPendaftaranEnum::Diterima,
                    'diajukan_at' => $now->copy()->subDays(105),
                    'diputuskan_at' => $now->copy()->subDays(98),
                    'catatan' => 'Permohonan tim disetujui untuk Bidang E-Government.',
                ]
            );

            $penTimSelesaiKetua = $this->upsertPenempatan(
                peserta: $ketuaSelesai,
                pendaftaran: $pendaftaranTimSelesai,
                kuotaId: $kuotaSelesai->id,
                bidangId: $bidang->id,
                pembimbingId: $pembimbing->id,
                tglMulai: $now->copy()->subDays(92),
                tglSelesai: $now->copy()->subDays(6),
                status: StatusPenempatanEnum::Selesai,
                tglDitetapkanSelesai: $now->copy()->subDays(5),
                fileSuratSelesai: $suratPdfPath,
                tglUploadSuratSelesai: $now->copy()->subDays(3)
            );
            $penTimSelesaiA1 = $this->upsertPenempatan(
                peserta: $a1Selesai,
                pendaftaran: $pendaftaranTimSelesai,
                kuotaId: $kuotaSelesai->id,
                bidangId: $bidang->id,
                pembimbingId: $pembimbing->id,
                tglMulai: $now->copy()->subDays(92),
                tglSelesai: $now->copy()->subDays(6),
                status: StatusPenempatanEnum::Selesai,
                tglDitetapkanSelesai: $now->copy()->subDays(5),
                fileSuratSelesai: $suratPdfPath,
                tglUploadSuratSelesai: $now->copy()->subDays(3)
            );
            $penTimSelesaiA2 = $this->upsertPenempatan(
                peserta: $a2Selesai,
                pendaftaran: $pendaftaranTimSelesai,
                kuotaId: $kuotaSelesai->id,
                bidangId: $bidang->id,
                pembimbingId: $pembimbing->id,
                tglMulai: $now->copy()->subDays(92),
                tglSelesai: $now->copy()->subDays(6),
                status: StatusPenempatanEnum::Selesai,
                tglDitetapkanSelesai: $now->copy()->subDays(5),
                fileSuratSelesai: $suratPdfPath,
                tglUploadSuratSelesai: $now->copy()->subDays(3)
            );

            $timSelesaiMembers = [
                [$ketuaSelesai->id, $penTimSelesaiKetua->id],
                [$a1Selesai->id, $penTimSelesaiA1->id],
                [$a2Selesai->id, $penTimSelesaiA2->id],
            ];

            Tugas::where('kelompok_magang_id', $timSelesai->id)->delete();
            Tugas::where('penempatan_id', $penTimSelesaiKetua->id)->delete();
            Logbook::where('penempatan_id', $penTimSelesaiKetua->id)->delete();

            $this->createTimTask(
                tim: $timSelesai,
                members: $timSelesaiMembers,
                judul: 'Rilis Portal Satu Data Kabupaten Sleman v1.0',
                deskripsi: 'Selesaikan seluruh fitur katalog dataset publik beserta dokumentasi teknis tim.',
                kategori: 'Proyek Tim',
                kompleksitas: KompleksitasEnum::Tinggi,
                deadline: $now->copy()->subDays(18),
                status: StatusTugasEnum::Selesai,
                reviewStatus: StatusReviewProgresEnum::Diterima,
                pelaporId: $ketuaSelesai->id,
                catatanProgres: 'Seluruh modul portal dataset telah dirilis dan terverifikasi.',
                feedback: 'Kolaborasi tim sangat solid, seluruh target terpenuhi.',
                link: 'https://github.com/magkom-demo/satu-data-sleman'
            );

            $this->createIndividuTask(
                penempatanId: $penTimSelesaiKetua->id,
                userId: $ketuaSelesai->id,
                judul: 'Presentasi Akhir & Serah Terima Dokumentasi Tim',
                deskripsi: 'Unggah materi paparan akhir serta ringkasan kontribusi tim.',
                kompleksitas: KompleksitasEnum::Rendah,
                deadline: $now->copy()->subDays(8),
                status: StatusTugasEnum::Selesai,
                reviewStatus: StatusReviewProgresEnum::Diterima,
                catatanProgres: 'Materi paparan dan arsip dokumentasi telah diserahkan.',
                feedback: 'Presentasi akhir berjalan lancar dan sangat baik.',
                link: 'https://github.com/magkom-demo/paparan-akhir-tim'
            );

            $this->seedSampleLogbooks($penTimSelesaiKetua->id, $now->copy()->subDays(7));
        });
    }

    private function upsertPeserta(
        string $email,
        string $nama,
        string $nim,
        string $institusi,
        int $jurusanId,
        string $noHp,
        string $password,
        Carbon $now
    ): User {
        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'nama' => $nama,
                'password' => $password,
                'role' => RoleEnum::Peserta,
                'status_aktif' => true,
                'must_change_password' => false,
                'email_verified_at' => $now,
            ]
        );

        PesertaProfil::updateOrCreate(
            ['user_id' => $user->id],
            [
                'institusi' => $institusi,
                'nim_nisn' => $nim,
                'jenjang' => JenjangEnum::S1,
                'jurusan_id' => $jurusanId,
                'no_hp' => $noHp,
                'alamat' => 'Sleman, D.I. Yogyakarta',
                'nama_kontak_darurat' => 'Keluarga '.$nama,
                'no_kontak_darurat' => '081900009988',
                'hubungan_kontak_darurat' => 'Orang Tua / Wali',
            ]
        );

        return $user;
    }

    private function upsertPenempatan(
        User $peserta,
        Pendaftaran $pendaftaran,
        int $kuotaId,
        int $bidangId,
        int $pembimbingId,
        Carbon $tglMulai,
        Carbon $tglSelesai,
        StatusPenempatanEnum $status,
        ?Carbon $tglDitetapkanSelesai = null,
        ?string $fileSuratSelesai = null,
        ?Carbon $tglUploadSuratSelesai = null
    ): Penempatan {
        return Penempatan::updateOrCreate(
            [
                'peserta_id' => $peserta->id,
                'pendaftaran_id' => $pendaftaran->id,
            ],
            [
                'kuota_id' => $kuotaId,
                'bidang_id' => $bidangId,
                'pembimbing_id' => $pembimbingId,
                'jalur' => JalurEnum::Umum,
                'sumber' => SumberPenempatanEnum::Online,
                'tgl_mulai' => $tglMulai->toDateString(),
                'tgl_selesai' => $tglSelesai->toDateString(),
                'tgl_ditetapkan_selesai' => $tglDitetapkanSelesai?->toDateString(),
                'file_surat_selesai' => $fileSuratSelesai,
                'tgl_upload_surat_selesai' => $tglUploadSuratSelesai,
                'status' => $status,
            ]
        );
    }

    /**
     * @param  array<int, array{0: int, 1: int}>  $members
     */
    private function createTimTask(
        KelompokMagang $tim,
        array $members,
        string $judul,
        string $deskripsi,
        string $kategori,
        KompleksitasEnum $kompleksitas,
        Carbon $deadline,
        StatusTugasEnum $status,
        ?StatusReviewProgresEnum $reviewStatus,
        ?int $pelaporId,
        ?string $catatanProgres,
        ?string $feedback,
        ?string $link
    ): void {
        $tugas = Tugas::create([
            'penempatan_id' => $members[0][1],
            'tipe' => 'kelompok',
            'kelompok_magang_id' => $tim->id,
            'judul' => $judul,
            'deskripsi' => $deskripsi,
            'kategori' => $kategori,
            'kompleksitas' => $kompleksitas,
            'deadline' => $deadline->toDateString(),
            'status' => $status,
            'status_publikasi' => 'dipublikasikan',
        ]);

        foreach ($members as [$userId, $penempatanId]) {
            $tugas->tugasPeserta()->create([
                'user_id' => $userId,
                'penempatan_id' => $penempatanId,
            ]);
        }

        if ($reviewStatus !== null && $pelaporId !== null) {
            ProgresLog::create([
                'tugas_id' => $tugas->id,
                'user_id' => $pelaporId,
                'catatan' => $catatanProgres,
                'link' => $link,
                'tgl_lapor' => now()->toDateString(),
                'status_review' => $reviewStatus,
                'feedback' => $feedback,
            ]);
        }
    }

    private function createIndividuTask(
        int $penempatanId,
        int $userId,
        string $judul,
        string $deskripsi,
        KompleksitasEnum $kompleksitas,
        Carbon $deadline,
        StatusTugasEnum $status,
        ?StatusReviewProgresEnum $reviewStatus,
        ?string $catatanProgres,
        ?string $feedback,
        ?string $link
    ): void {
        $tugas = Tugas::create([
            'penempatan_id' => $penempatanId,
            'tipe' => 'individu',
            'kelompok_magang_id' => null,
            'judul' => $judul,
            'deskripsi' => $deskripsi,
            'kategori' => 'Individu',
            'kompleksitas' => $kompleksitas,
            'deadline' => $deadline->toDateString(),
            'status' => $status,
            'status_publikasi' => 'dipublikasikan',
        ]);

        if ($reviewStatus !== null) {
            ProgresLog::create([
                'tugas_id' => $tugas->id,
                'user_id' => $userId,
                'catatan' => $catatanProgres,
                'link' => $link,
                'tgl_lapor' => now()->toDateString(),
                'status_review' => $reviewStatus,
                'feedback' => $feedback,
            ]);
        }
    }

    private function seedSampleLogbooks(int $penempatanId, Carbon $baseDate): void
    {
        Logbook::create([
            'penempatan_id' => $penempatanId,
            'minggu_ke' => 1,
            'tanggal' => $baseDate->copy()->subDays(2)->toDateString(),
            'kegiatan' => 'Koordinasi teknis dan pengembangan modul layanan sesuai target mingguan.',
            'status' => StatusLogbookEnum::Disetujui,
            'catatan_pembimbing' => 'Aktivitas tercatat dengan baik.',
        ]);

        Logbook::create([
            'penempatan_id' => $penempatanId,
            'minggu_ke' => 1,
            'tanggal' => $baseDate->copy()->subDay()->toDateString(),
            'kegiatan' => 'Pengujian fitur dan penyusunan dokumentasi progres harian.',
            'status' => StatusLogbookEnum::Terkirim,
        ]);
    }
}
