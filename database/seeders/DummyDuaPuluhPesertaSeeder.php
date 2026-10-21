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
use App\Enums\StatusPenilaianEnum;
use App\Enums\StatusReviewProgresEnum;
use App\Enums\StatusTugasEnum;
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
use App\Models\Penilaian;
use App\Models\PenilaianDetail;
use App\Models\PesertaProfil;
use App\Models\ProgresLog;
use App\Models\Rubrik;
use App\Models\SlotKhusus;
use App\Models\Tugas;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DummyDuaPuluhPesertaSeeder extends Seeder
{
    /**
     * Jalankan seeder 20 variasi kasus peserta magang komprehensif.
     */
    public function run(): void
    {
        $defaultPassword = Hash::make('Password123!');

        // Ambil master data pendukung
        $admin = User::where('role', RoleEnum::Admin)->first()
            ?? User::where('role', RoleEnum::SuperAdmin)->first();

        $pembimbing = User::where('role', RoleEnum::Pembimbing)->first();
        if (! $pembimbing) {
            $pembimbing = User::create([
                'nama' => 'Budi Pembimbing Lapangan',
                'email' => 'pembimbing@magkom.go.id',
                'password' => $defaultPassword,
                'role' => RoleEnum::Pembimbing,
                'status_aktif' => true,
                'email_verified_at' => now(),
            ]);
        }

        $bidang = Bidang::first() ?? Bidang::create(['nama_bidang' => 'E-Government & Informatika', 'aktif' => true]);
        $jurusanTI = Jurusan::firstOrCreate(['nama_jurusan' => 'Teknik Informatika'], ['kategori' => 'mahasiswa', 'aktif' => true]);
        $jurusanSI = Jurusan::firstOrCreate(['nama_jurusan' => 'Sistem Informasi'], ['kategori' => 'mahasiswa', 'aktif' => true]);
        $jenisDokumenList = JenisDokumen::all();
        $rubrikList = Rubrik::where('aktif', true)->get();

        // Bulan magang aktif dan masa lalu
        $bulanSekarang = BulanMagang::firstOrCreate(
            ['bulan_mulai' => now('Asia/Jakarta')->startOfMonth()->toDateString()],
            [
                'tgl_buka' => now('Asia/Jakarta')->subDays(20)->toDateString(),
                'tgl_tutup' => now('Asia/Jakarta')->addDays(10)->toDateString(),
                'aktif' => true,
            ]
        );

        $bulanLalu = BulanMagang::firstOrCreate(
            ['bulan_mulai' => now('Asia/Jakarta')->subMonths(3)->startOfMonth()->toDateString()],
            [
                'tgl_buka' => now('Asia/Jakarta')->subMonths(4)->toDateString(),
                'tgl_tutup' => now('Asia/Jakarta')->subMonths(3)->subDays(5)->toDateString(),
                'aktif' => false,
            ]
        );

        $kuota = Kuota::firstOrCreate(
            ['bulan_magang_id' => $bulanSekarang->id, 'jurusan_id' => $jurusanTI->id],
            ['jumlah' => 25, 'terisi' => 10, 'aktif' => true]
        );

        $kuotaLalu = Kuota::firstOrCreate(
            ['bulan_magang_id' => $bulanLalu->id, 'jurusan_id' => $jurusanTI->id],
            ['jumlah' => 20, 'terisi' => 20, 'aktif' => false]
        );

        // Helper membuat peserta & profil
        $createPeserta = function (string $nama, string $email, string $institusi, Jurusan $jurusan, string $nim, bool $aktif = true) use ($defaultPassword): User {
            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'nama' => $nama,
                    'password' => $defaultPassword,
                    'role' => RoleEnum::Peserta,
                    'status_aktif' => $aktif,
                    'email_verified_at' => now(),
                    'must_change_password' => false,
                ]
            );

            PesertaProfil::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nim_nisn' => $nim,
                    'institusi' => $institusi,
                    'jurusan_id' => $jurusan->id,
                    'jenjang' => JenjangEnum::S1,
                    'no_hp' => '0812'.rand(10000000, 99999999),
                    'nama_kontak_darurat' => 'Orang Tua / Wali '.$nama,
                    'no_kontak_darurat' => '0813'.rand(10000000, 99999999),
                    'hubungan_kontak_darurat' => 'Orang Tua',
                ]
            );

            return $user;
        };

        // Helper membuat dokumen pendaftaran dummy
        $lampirkanDokumen = function (Pendaftaran $pendaftaran) use ($jenisDokumenList) {
            foreach ($jenisDokumenList as $jd) {
                Dokumen::updateOrCreate(
                    ['pendaftaran_id' => $pendaftaran->id, 'jenis_dokumen_id' => $jd->id],
                    [
                        'nama_asli' => strtolower(str_replace(' ', '_', $jd->nama)).'.pdf',
                        'path' => 'dokumen/sample_'.$jd->kode.'.pdf',
                        'ukuran' => 250000, // 250 KB
                        'mime' => 'application/pdf',
                        'hash' => sha1('sample_'.$jd->kode.'_'.$pendaftaran->id),
                    ]
                );
            }
        };

        // =========================================================================
        // KELOMPOK 1: PENDAFTARAN & VERIFIKASI (KASUS 1 - 4)
        // =========================================================================

        // Kasus 1: Pendaftaran Baru Lengkap (< 3 Hari / Dalam SLA)
        $p1 = $createPeserta('Aditya Pratama', 'aditya.pratama@mail.ugm.ac.id', 'Universitas Gadjah Mada', $jurusanTI, '22/491001/TK/54001');
        $reg1 = Pendaftaran::updateOrCreate(
            ['user_id' => $p1->id],
            [
                'bulan_magang_id' => $bulanSekarang->id,
                'kuota_id' => $kuota->id,
                'durasi_bulan' => 3,
                'status' => StatusPendaftaranEnum::Diajukan,
                'diajukan_at' => now()->subDay(),
            ]
        );
        $lampirkanDokumen($reg1);

        // Kasus 2: Pendaftaran Baru SLA Breached (> 3 Hari Belum Ditinjau)
        $p2 = $createPeserta('Bunga Citra Lestari', 'bunga.citra@student.uny.ac.id', 'Universitas Negeri Yogyakarta', $jurusanTI, '22537144002');
        $reg2 = Pendaftaran::updateOrCreate(
            ['user_id' => $p2->id],
            [
                'bulan_magang_id' => $bulanSekarang->id,
                'kuota_id' => $kuota->id,
                'durasi_bulan' => 3,
                'status' => StatusPendaftaranEnum::Diajukan,
                'diajukan_at' => now()->subDays(6), // Terlewat SLA (>3 hari)
            ]
        );
        $lampirkanDokumen($reg2);

        // Kasus 3: Pendaftaran Perlu Perbaikan (Proposal Salah Format)
        $p3 = $createPeserta('Chandra Wijaya', 'chandra.wijaya@upnyk.ac.id', 'UPN Veteran Yogyakarta', $jurusanSI, '123220045');
        Pendaftaran::updateOrCreate(
            ['user_id' => $p3->id],
            [
                'bulan_magang_id' => $bulanSekarang->id,
                'kuota_id' => $kuota->id,
                'durasi_bulan' => 3,
                'status' => StatusPendaftaranEnum::PerluPerbaikan,
                'catatan' => 'Format proposal magang belum mencantumkan tanda tangan Kaprodi. Mohon revisi dan unggah ulang proposal resmi bertanda tangan.',
                'verifikator_id' => $admin?->id,
                'diajukan_at' => now()->subDays(2),
                'diputuskan_at' => now()->subHours(12),
            ]
        );

        // Kasus 4: Pendaftaran Baru Draft (Berkas Belum Lengkap)
        $p4 = $createPeserta('Dinda Kirana', 'dinda.kirana@uin-suka.ac.id', 'UIN Sunan Kalijaga', $jurusanTI, '22106050012');
        Pendaftaran::updateOrCreate(
            ['user_id' => $p4->id],
            [
                'bulan_magang_id' => $bulanSekarang->id,
                'kuota_id' => $kuota->id,
                'durasi_bulan' => 3,
                'status' => StatusPendaftaranEnum::BerkasBelumLengkap,
                'diajukan_at' => null,
            ]
        );

        // =========================================================================
        // KELOMPOK 2: ANTREAN WAITING LIST & PENOLAKAN (KASUS 5 - 9)
        // =========================================================================

        // Kasus 5: Waiting List Prioritas #1 (Siap Dipromosikan)
        $p5 = $createPeserta('Eko Prasetyo', 'eko.prasetyo@students.amikom.ac.id', 'Universitas Amikom Yogyakarta', $jurusanTI, '22.11.4580');
        $reg5 = Pendaftaran::updateOrCreate(
            ['user_id' => $p5->id],
            [
                'bulan_magang_id' => $bulanSekarang->id,
                'kuota_id' => $kuota->id,
                'durasi_bulan' => 3,
                'status' => StatusPendaftaranEnum::WaitingList,
                'catatan' => 'Kuota jurusan penuh saat verifikasi, dialihkan ke antrean cadangan (Waiting List) prioritas 1.',
                'verifikator_id' => $admin?->id,
                'diajukan_at' => now()->subDays(10),
                'diputuskan_at' => now()->subDays(7),
            ]
        );
        $lampirkanDokumen($reg5);

        // Kasus 6: Waiting List Prioritas #2
        $p6 = $createPeserta('Fani Rahmawati', 'fani.rahma@uii.ac.id', 'Universitas Islam Indonesia', $jurusanTI, '22523098');
        $reg6 = Pendaftaran::updateOrCreate(
            ['user_id' => $p6->id],
            [
                'bulan_magang_id' => $bulanSekarang->id,
                'kuota_id' => $kuota->id,
                'durasi_bulan' => 3,
                'status' => StatusPendaftaranEnum::WaitingList,
                'catatan' => 'Antrean waiting list prioritas 2.',
                'verifikator_id' => $admin?->id,
                'diajukan_at' => now()->subDays(8),
                'diputuskan_at' => now()->subDays(5),
            ]
        );
        $lampirkanDokumen($reg6);

        // Kasus 7: Waiting List Tertutup Otomatis (Awal Bulan Magang Dimulai AC-04-7)
        $p7 = $createPeserta('Gilang Ramadhan', 'gilang.ramadhan@usd.ac.id', 'Universitas Sanata Dharma', $jurusanTI, '225314050');
        Pendaftaran::updateOrCreate(
            ['user_id' => $p7->id],
            [
                'bulan_magang_id' => $bulanLalu->id,
                'kuota_id' => $kuotaLalu->id,
                'durasi_bulan' => 3,
                'status' => StatusPendaftaranEnum::Ditolak,
                'catatan' => 'Periode pelaksanaan magang telah dimulai. Antrean waiting list ditutup secara otomatis (AC-04-7).',
                'verifikator_id' => $admin?->id,
                'diajukan_at' => now()->subMonths(3)->subDays(10),
                'diputuskan_at' => now()->subMonths(3),
            ]
        );

        // Kasus 8: Pendaftaran Ditolak Verifikasi
        $p8 = $createPeserta('Hana Safitri', 'hana.safitri@mail.uajy.ac.id', 'Universitas Atma Jaya Yogyakarta', $jurusanSI, '220711904');
        Pendaftaran::updateOrCreate(
            ['user_id' => $p8->id],
            [
                'bulan_magang_id' => $bulanSekarang->id,
                'kuota_id' => $kuota->id,
                'durasi_bulan' => 3,
                'status' => StatusPendaftaranEnum::Ditolak,
                'catatan' => 'Surat permohonan resmi dari kampus tidak mencantumkan stempel basah fakultas dan jurusan tidak sesuai formasi dinas.',
                'verifikator_id' => $admin?->id,
                'diajukan_at' => now()->subDays(5),
                'diputuskan_at' => now()->subDays(3),
            ]
        );

        // Kasus 9: Penempatan Dibatalkan Sebelum Tanggal Mulai (Kuota Kembali, AC-05-4)
        $p9 = $createPeserta('Indra Kurniawan', 'indra.kurniawan@upnyk.ac.id', 'UPN Veteran Yogyakarta', $jurusanTI, '123220088');
        $reg9 = Pendaftaran::updateOrCreate(
            ['user_id' => $p9->id],
            [
                'bulan_magang_id' => $bulanSekarang->id,
                'kuota_id' => $kuota->id,
                'durasi_bulan' => 3,
                'status' => StatusPendaftaranEnum::Dibatalkan,
                'catatan' => 'Pembatalan mandiri sebelum periode magang dimulai karena diterima magang bersertifikat BUMN.',
                'diajukan_at' => now()->subDays(12),
                'diputuskan_at' => now()->subDays(9),
            ]
        );
        Penempatan::updateOrCreate(
            ['peserta_id' => $p9->id],
            [
                'pendaftaran_id' => $reg9->id,
                'kuota_id' => $kuota->id,
                'bidang_id' => $bidang->id,
                'pembimbing_id' => $pembimbing->id,
                'jalur' => JalurEnum::Umum,
                'sumber' => SumberPenempatanEnum::Online,
                'tgl_mulai' => now()->addDays(5),
                'tgl_selesai' => now()->addMonths(3),
                'status' => StatusPenempatanEnum::Nonaktif,
            ]
        );

        // =========================================================================
        // KELOMPOK 3: PENEMPATAN AKTIF & BIMBINGAN OPERASIONAL (KASUS 10 - 15)
        // =========================================================================

        // Kasus 10: Penempatan Aktif Baru Mulai (Minggu Pertama)
        $p10 = $createPeserta('Joko Susilo', 'joko.susilo@ugm.ac.id', 'Universitas Gadjah Mada', $jurusanTI, '22/492110/TK/54110');
        $reg10 = Pendaftaran::updateOrCreate(
            ['user_id' => $p10->id],
            [
                'bulan_magang_id' => $bulanSekarang->id,
                'kuota_id' => $kuota->id,
                'durasi_bulan' => 3,
                'status' => StatusPendaftaranEnum::Diterima,
                'diajukan_at' => now()->subDays(20),
                'diputuskan_at' => now()->subDays(15),
            ]
        );
        $penempatan10 = Penempatan::updateOrCreate(
            ['peserta_id' => $p10->id],
            [
                'pendaftaran_id' => $reg10->id,
                'kuota_id' => $kuota->id,
                'bidang_id' => $bidang->id,
                'pembimbing_id' => $pembimbing->id,
                'jalur' => JalurEnum::Umum,
                'sumber' => SumberPenempatanEnum::Online,
                'tgl_mulai' => now()->subDays(4),
                'tgl_selesai' => now()->addMonths(3)->subDays(4),
                'status' => StatusPenempatanEnum::Aktif,
            ]
        );
        // Tugas minggu ke-1
        Tugas::updateOrCreate(
            ['penempatan_id' => $penempatan10->id, 'judul' => 'Orientasi Sistem Informasi Sleman'],
            [
                'deskripsi' => 'Pelajari arsitektur portal Sleman Satu Data dan buat ringkasan alur integrasi API dinas.',
                'kategori' => 'Analisis Sistem',
                'kompleksitas' => KompleksitasEnum::Rendah,
                'deadline' => now()->addDays(5),
                'status' => StatusTugasEnum::Proses,
            ]
        );
        // Logbook hari ke-1 dan 2
        Logbook::updateOrCreate(
            ['penempatan_id' => $penempatan10->id, 'tanggal' => now()->subDays(2)->toDateString()],
            [
                'minggu_ke' => 1,
                'kegiatan' => 'Mengikuti pengarahan staf Kominfo dan pengenalan lingkungan server Sleman.',
                'status' => StatusLogbookEnum::Terkirim,
            ]
        );

        // Kasus 11: Penempatan Aktif Teladan (Tugas Berjalan & Logbook Disetujui)
        $p11 = $createPeserta('Kartika Putri', 'kartika.putri@uny.ac.id', 'Universitas Negeri Yogyakarta', $jurusanTI, '22537144018');
        $reg11 = Pendaftaran::updateOrCreate(
            ['user_id' => $p11->id],
            [
                'bulan_magang_id' => $bulanSekarang->id,
                'kuota_id' => $kuota->id,
                'durasi_bulan' => 3,
                'status' => StatusPendaftaranEnum::Diterima,
                'diajukan_at' => now()->subDays(35),
                'diputuskan_at' => now()->subDays(30),
            ]
        );
        $penempatan11 = Penempatan::updateOrCreate(
            ['peserta_id' => $p11->id],
            [
                'pendaftaran_id' => $reg11->id,
                'kuota_id' => $kuota->id,
                'bidang_id' => $bidang->id,
                'pembimbing_id' => $pembimbing->id,
                'jalur' => JalurEnum::Umum,
                'sumber' => SumberPenempatanEnum::Online,
                'tgl_mulai' => now()->subMonth(),
                'tgl_selesai' => now()->addMonths(2),
                'status' => StatusPenempatanEnum::Aktif,
            ]
        );
        // Tugas selesai
        Tugas::updateOrCreate(
            ['penempatan_id' => $penempatan11->id, 'judul' => 'Redesain Antarmuka Dashboard Publik'],
            [
                'deskripsi' => 'Membangun komponen Blade reusable berbasis 30 token Figma Sleman.',
                'kategori' => 'Frontend UI/UX',
                'kompleksitas' => KompleksitasEnum::Sedang,
                'deadline' => now()->subDays(5),
                'status' => StatusTugasEnum::Selesai,
            ]
        );
        // Logbook disetujui
        Logbook::updateOrCreate(
            ['penempatan_id' => $penempatan11->id, 'tanggal' => now()->subDays(3)->toDateString()],
            [
                'minggu_ke' => 4,
                'kegiatan' => 'Implementasi halaman analitik kunjungan portal dan pengujian aksesibilitas WCAG AA.',
                'status' => StatusLogbookEnum::Disetujui,
                'catatan_pembimbing' => 'Kerja sangat baik, dokumentasi kode rapi dan patuh pada pedoman dinas.',
            ]
        );

        // Kasus 12: Penempatan Aktif dengan Logbook SLA Breached (>3 Hari Belum Direviu)
        $p12 = $createPeserta('Lukman Hakim', 'lukman.hakim@upnyk.ac.id', 'UPN Veteran Yogyakarta', $jurusanSI, '123220101');
        $penempatan12 = Penempatan::updateOrCreate(
            ['peserta_id' => $p12->id],
            [
                'kuota_id' => $kuota->id,
                'bidang_id' => $bidang->id,
                'pembimbing_id' => $pembimbing->id,
                'jalur' => JalurEnum::Umum,
                'sumber' => SumberPenempatanEnum::Online,
                'tgl_mulai' => now()->subDays(20),
                'tgl_selesai' => now()->addMonths(2)->addDays(10),
                'status' => StatusPenempatanEnum::Aktif,
            ]
        );
        Logbook::updateOrCreate(
            ['penempatan_id' => $penempatan12->id, 'tanggal' => now()->subDays(5)->toDateString()],
            [
                'minggu_ke' => 3,
                'kegiatan' => 'Audit keandalan basis data MySQL dan penataan indeks tabel log.',
                'status' => StatusLogbookEnum::Terkirim, // Belum direview 5 hari
            ]
        );

        // Kasus 13: Penempatan Aktif dengan Tugas Terlambat (Overdue BR-16)
        $p13 = $createPeserta('Maya Anggraini', 'maya.anggraini@amikom.ac.id', 'Universitas Amikom Yogyakarta', $jurusanTI, '22.11.4900');
        $penempatan13 = Penempatan::updateOrCreate(
            ['peserta_id' => $p13->id],
            [
                'kuota_id' => $kuota->id,
                'bidang_id' => $bidang->id,
                'pembimbing_id' => $pembimbing->id,
                'jalur' => JalurEnum::Umum,
                'sumber' => SumberPenempatanEnum::Online,
                'tgl_mulai' => now()->subDays(25),
                'tgl_selesai' => now()->addMonths(2)->addDays(5),
                'status' => StatusPenempatanEnum::Aktif,
            ]
        );
        Tugas::updateOrCreate(
            ['penempatan_id' => $penempatan13->id, 'judul' => 'Migrasi Skema Modul Audit Forensik'],
            [
                'deskripsi' => 'Penyusunan trigger audit log dan optimasi query reporting.',
                'kategori' => 'Backend Database',
                'kompleksitas' => KompleksitasEnum::Tinggi,
                'deadline' => now()->subDays(2), // Deadline kemarin (TERLAMBAT)
                'status' => StatusTugasEnum::Proses,
            ]
        );

        // Kasus 14: Penempatan Aktif dengan Umpan Balik Revisi Tugas
        $p14 = $createPeserta('Naufal Rizky', 'naufal.rizky@uii.ac.id', 'Universitas Islam Indonesia', $jurusanTI, '22523120');
        $penempatan14 = Penempatan::updateOrCreate(
            ['peserta_id' => $p14->id],
            [
                'kuota_id' => $kuota->id,
                'bidang_id' => $bidang->id,
                'pembimbing_id' => $pembimbing->id,
                'jalur' => JalurEnum::Umum,
                'sumber' => SumberPenempatanEnum::Online,
                'tgl_mulai' => now()->subDays(15),
                'tgl_selesai' => now()->addMonths(2)->addDays(15),
                'status' => StatusPenempatanEnum::Aktif,
            ]
        );
        $tugas14 = Tugas::updateOrCreate(
            ['penempatan_id' => $penempatan14->id, 'judul' => 'Integrasi Modul Ekspor CSV'],
            [
                'deskripsi' => 'Buat generator CSV dengan sanitasi formula injection.',
                'kategori' => 'Keamanan Data',
                'kompleksitas' => KompleksitasEnum::Sedang,
                'deadline' => now()->addDays(4),
                'status' => StatusTugasEnum::Proses,
            ]
        );
        ProgresLog::updateOrCreate(
            ['tugas_id' => $tugas14->id],
            [
                'catatan' => 'Telah dibuat export CSV namun belum memfilter karakter formula = dan +.',
                'tgl_lapor' => now()->subDay(),
                'status_review' => StatusReviewProgresEnum::PerluRevisi,
                'feedback' => 'Harap tambahkan escape kutip tunggal untuk karakter formula sesuai OWASP CSV injection.',
            ]
        );

        // Kasus 15: Penempatan Jalur Slot Khusus Internal
        $slotKhusus = SlotKhusus::firstOrCreate(
            ['bulan_mulai' => now('Asia/Jakarta')->startOfMonth()->toDateString()],
            ['jumlah' => 5, 'terisi' => 1, 'aktif' => true, 'dibuat_oleh' => $admin?->id ?? 1]
        );
        $p15 = $createPeserta('Olivia Tan', 'olivia.tan@komdigi.go.id', 'Politeknik Siber dan Sandi Negara', $jurusanTI, '20220199');
        Penempatan::updateOrCreate(
            ['peserta_id' => $p15->id],
            [
                'slot_khusus_id' => $slotKhusus->id,
                'bidang_id' => $bidang->id,
                'pembimbing_id' => $pembimbing->id,
                'jalur' => JalurEnum::Khusus,
                'sumber' => SumberPenempatanEnum::Manual,
                'tgl_mulai' => now()->subDays(10),
                'tgl_selesai' => now()->addMonths(3),
                'status' => StatusPenempatanEnum::Aktif,
            ]
        );

        // =========================================================================
        // KELOMPOK 4: EVALUASI NILai & KELULUSAN / OFFBOARDING (KASUS 16 - 20)
        // =========================================================================

        // Kasus 16: Penempatan Menjelang Selesai (Menunggu Penilaian Akhir)
        $p16 = $createPeserta('Panji Nugroho', 'panji.nugroho@ugm.ac.id', 'Universitas Gadjah Mada', $jurusanTI, '21/481900/TK/53900');
        Penempatan::updateOrCreate(
            ['peserta_id' => $p16->id],
            [
                'kuota_id' => $kuota->id,
                'bidang_id' => $bidang->id,
                'pembimbing_id' => $pembimbing->id,
                'jalur' => JalurEnum::Umum,
                'sumber' => SumberPenempatanEnum::Online,
                'tgl_mulai' => now()->subMonths(3),
                'tgl_selesai' => now()->addDays(2), // Berakhir 2 hari lagi
                'status' => StatusPenempatanEnum::Aktif,
            ]
        );

        // Kasus 17: Penempatan dengan Draft Penilaian
        $p17 = $createPeserta('Qori Amanda', 'qori.amanda@uny.ac.id', 'Universitas Negeri Yogyakarta', $jurusanTI, '21537144055');
        $penempatan17 = Penempatan::updateOrCreate(
            ['peserta_id' => $p17->id],
            [
                'kuota_id' => $kuota->id,
                'bidang_id' => $bidang->id,
                'pembimbing_id' => $pembimbing->id,
                'jalur' => JalurEnum::Umum,
                'sumber' => SumberPenempatanEnum::Online,
                'tgl_mulai' => now()->subMonths(3),
                'tgl_selesai' => now()->addDay(),
                'status' => StatusPenempatanEnum::Aktif,
            ]
        );
        $penilaian17 = Penilaian::updateOrCreate(
            ['penempatan_id' => $penempatan17->id],
            [
                'pembimbing_id' => $pembimbing->id,
                'nilai_akhir' => 78.50,
                'predikat' => 'B',
                'catatan' => 'Draft penilaian awal bimbingan, masih menunggu reviu laporan akhir.',
                'status' => StatusPenilaianEnum::Draft,
            ]
        );
        foreach ($rubrikList as $rubrik) {
            PenilaianDetail::updateOrCreate(
                ['penilaian_id' => $penilaian17->id, 'rubrik_id' => $rubrik->id],
                [
                    'skor' => 80,
                    'bobot_snapshot' => $rubrik->bobot,
                ]
            );
        }

        // Kasus 18: Penempatan dengan Nilai Final Sempurna (Predikat A, Lembar Nilai Terbit)
        $p18 = $createPeserta('Rian Ardiansyah', 'rian.ardiansyah@upnyk.ac.id', 'UPN Veteran Yogyakarta', $jurusanTI, '123210012');
        $penempatan18 = Penempatan::updateOrCreate(
            ['peserta_id' => $p18->id],
            [
                'kuota_id' => $kuota->id,
                'bidang_id' => $bidang->id,
                'pembimbing_id' => $pembimbing->id,
                'jalur' => JalurEnum::Umum,
                'sumber' => SumberPenempatanEnum::Online,
                'tgl_mulai' => now()->subMonths(3),
                'tgl_selesai' => now(),
                'status' => StatusPenempatanEnum::Aktif,
            ]
        );
        $penilaian18 = Penilaian::updateOrCreate(
            ['penempatan_id' => $penempatan18->id],
            [
                'pembimbing_id' => $pembimbing->id,
                'nilai_akhir' => 92.00,
                'predikat' => 'A',
                'catatan' => 'Kinerja luar biasa dalam membangun modul integrasi data dinas.',
                'status' => StatusPenilaianEnum::Final,
            ]
        );
        foreach ($rubrikList as $rubrik) {
            PenilaianDetail::updateOrCreate(
                ['penilaian_id' => $penilaian18->id, 'rubrik_id' => $rubrik->id],
                [
                    'skor' => 92,
                    'bobot_snapshot' => $rubrik->bobot,
                ]
            );
        }

        // Kasus 19: Penempatan Selesai Penuh (Alumni Magang / Masuk Arsip SuperAdmin BR-07)
        $p19 = $createPeserta('Siti Nurhaliza', 'siti.nurhaliza@amikom.ac.id', 'Universitas Amikom Yogyakarta', $jurusanTI, '21.11.3999', false);
        $penempatan19 = Penempatan::updateOrCreate(
            ['peserta_id' => $p19->id],
            [
                'kuota_id' => $kuotaLalu->id,
                'bidang_id' => $bidang->id,
                'pembimbing_id' => $pembimbing->id,
                'jalur' => JalurEnum::Umum,
                'sumber' => SumberPenempatanEnum::Online,
                'tgl_mulai' => now()->subMonths(4),
                'tgl_selesai' => now()->subMonth(),
                'tgl_ditetapkan_selesai' => now()->subMonth(),
                'tgl_nonaktif' => now()->subMonth(),
                'status' => StatusPenempatanEnum::Selesai,
            ]
        );
        $penilaian19 = Penilaian::updateOrCreate(
            ['penempatan_id' => $penempatan19->id],
            [
                'pembimbing_id' => $pembimbing->id,
                'nilai_akhir' => 88.00,
                'predikat' => 'A',
                'catatan' => 'Lulus program magang dengan predikat Sangat Baik. Sertifikat dan lembar nilai telah terbit.',
                'status' => StatusPenilaianEnum::Final,
            ]
        );
        foreach ($rubrikList as $rubrik) {
            PenilaianDetail::updateOrCreate(
                ['penilaian_id' => $penilaian19->id, 'rubrik_id' => $rubrik->id],
                [
                    'skor' => 88,
                    'bobot_snapshot' => $rubrik->bobot,
                ]
            );
        }

        // Kasus 20: Penempatan Mengundurkan Diri di Tengah Periode (Kuota Hangus, Akun Nonaktif)
        $p20 = $createPeserta('Taufik Hidayat', 'taufik.hidayat@uii.ac.id', 'Universitas Islam Indonesia', $jurusanTI, '22523299', false);
        Penempatan::updateOrCreate(
            ['peserta_id' => $p20->id],
            [
                'kuota_id' => $kuota->id,
                'bidang_id' => $bidang->id,
                'pembimbing_id' => $pembimbing->id,
                'jalur' => JalurEnum::Umum,
                'sumber' => SumberPenempatanEnum::Online,
                'tgl_mulai' => now()->subMonths(2),
                'tgl_selesai' => now()->addMonth(),
                'tgl_nonaktif' => now()->subDays(10),
                'status' => StatusPenempatanEnum::MengundurkanDiri,
            ]
        );
    }
}
