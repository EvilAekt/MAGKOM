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
use App\Enums\StatusTugasEnum;
use App\Enums\SumberPenempatanEnum;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\Kuota;
use App\Models\Logbook;
use App\Models\Pendaftaran;
use App\Models\Penempatan;
use App\Models\PesertaProfil;
use App\Models\Tugas;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AcceptedPesertaSeeder extends Seeder
{
    /**
     * Seed data peserta berstatus 'Diterima' dan aktif magang untuk demonstrasi POV Peserta Diterima.
     */
    public function run(): void
    {
        // 1. Pastikan Bidang Aptika & IKP tersedia
        $bidangAptika = Bidang::firstOrCreate(
            ['nama_bidang' => 'Bidang Aplikasi Informatika (Aptika)'],
            [
                'deskripsi' => 'Pengembangan ekosistem digital, integrasi aplikasi, dan layanan e-government Sleman.',
                'aktif' => true,
            ]
        );

        $bidangIkp = Bidang::firstOrCreate(
            ['nama_bidang' => 'Bidang Informasi dan Komunikasi Publik (IKP)'],
            [
                'deskripsi' => 'Pengelolaan kanal komunikasi resmi, dokumentasi multimedia, dan relasi publik.',
                'aktif' => true,
            ]
        );

        // 2. Ambil atau buat Jurusan dan Periode Bulan Magang
        $jurusan = Jurusan::where('kategori', 'smk')->first() ?? Jurusan::create([
            'nama_jurusan' => 'Teknik Komputer dan Jaringan',
            'kategori' => 'smk',
            'aktif' => true,
        ]);

        $bulanMagang = BulanMagang::first() ?? BulanMagang::create([
            'bulan_mulai' => Carbon::now()->startOfMonth()->toDateString(),
            'tgl_buka' => Carbon::now()->subMonth()->toDateString(),
            'tgl_tutup' => Carbon::now()->subDays(5)->toDateString(),
            'aktif' => true,
        ]);

        $kuota = Kuota::firstOrCreate(
            [
                'bulan_magang_id' => $bulanMagang->id,
                'jurusan_id' => $jurusan->id,
            ],
            [
                'jumlah' => 10,
                'terisi' => 1,
                'aktif' => true,
            ]
        );

        // 3. Pastikan Akun Pembimbing
        $pembimbing = User::firstOrNew(['email' => 'pembimbing@magkom.go.id']);
        $pembimbing->nama = 'Budi Santoso, S.Kom (Pembimbing Lapangan)';
        $pembimbing->password = Hash::make('Password123!');
        $pembimbing->role = RoleEnum::Pembimbing;
        $pembimbing->bidang_id = $bidangAptika->id;
        $pembimbing->email_verified_at = now();
        $pembimbing->status_aktif = true;
        $pembimbing->must_change_password = false;
        $pembimbing->save();

        // 4. Update Akun Peserta: peserta@magkom.go.id
        $peserta = User::firstOrNew(['email' => 'peserta@magkom.go.id']);
        $peserta->nama = 'Naufal Pratama (Peserta Magang)';
        $peserta->password = Hash::make('Password123!');
        $peserta->role = RoleEnum::Peserta;
        $peserta->email_verified_at = now();
        $peserta->status_aktif = true;
        $peserta->must_change_password = false;
        $peserta->save();

        // 5. Update / Buat Profil Lengkap Peserta
        $profil = PesertaProfil::updateOrCreate(
            ['user_id' => $peserta->id],
            [
                'institusi' => 'SMK Negeri 2 Depok Sleman',
                'nim_nisn' => '0054321987',
                'jenjang' => JenjangEnum::Smk,
                'jurusan_id' => $jurusan->id,
                'no_hp' => '081234567890',
                'nama_kontak_darurat' => 'Sudarsono (Orang Tua)',
                'no_kontak_darurat' => '089876543210',
                'hubungan_kontak_darurat' => 'Orang Tua / Wali',
            ]
        );

        // 6. Buat / Update Pengajuan Pendaftaran menjadi DITERIMA
        $pendaftaran = Pendaftaran::where('user_id', $peserta->id)->latest()->first();
        if (! $pendaftaran) {
            $pendaftaran = new Pendaftaran;
            $pendaftaran->user_id = $peserta->id;
        }

        $pendaftaran->bulan_magang_id = $bulanMagang->id;
        $pendaftaran->kuota_id = $kuota->id;
        $pendaftaran->durasi_bulan = 3;
        $pendaftaran->status = StatusPendaftaranEnum::Diterima;
        $pendaftaran->diajukan_at = Carbon::now()->subDays(20);
        $pendaftaran->diputuskan_at = Carbon::now()->subDays(14);
        $pendaftaran->catatan = 'Selamat! Berkas dan kualifikasi Anda memenuhi syarat penempatan Bidang Aptika Diskominfo Sleman.';
        $pendaftaran->save();

        // 7. Buat / Update Penempatan Aktif
        $penempatan = Penempatan::where('peserta_id', $peserta->id)->first();
        if (! $penempatan) {
            $penempatan = new Penempatan;
            $penempatan->peserta_id = $peserta->id;
        }

        $penempatan->pendaftaran_id = $pendaftaran->id;
        $penempatan->kuota_id = $kuota->id;
        $penempatan->bidang_id = $bidangAptika->id;
        $penempatan->pembimbing_id = $pembimbing->id;
        $penempatan->jalur = JalurEnum::Umum;
        $penempatan->sumber = SumberPenempatanEnum::Online;
        $penempatan->tgl_mulai = Carbon::now()->subDays(14)->toDateString();
        $penempatan->tgl_selesai = Carbon::now()->addDays(76)->toDateString();
        $penempatan->status = StatusPenempatanEnum::Aktif;
        $penempatan->save();

        // 8. Tambahkan Tugas-Tugas Bimbingan Realistis
        Tugas::where('penempatan_id', $penempatan->id)->delete();

        Tugas::create([
            'penempatan_id' => $penempatan->id,
            'judul' => 'Analisis Kebutuhan Sistem & Pemetaan API MagKom',
            'deskripsi' => 'Pelajari dokumen PRD dan buat pemetaan rancangan endpoint serta arsitektur data.',
            'kategori' => 'Analisis & Riset',
            'kompleksitas' => KompleksitasEnum::Sedang,
            'deadline' => Carbon::now()->subDays(3)->toDateString(),
            'status' => StatusTugasEnum::Selesai,
        ]);

        Tugas::create([
            'penempatan_id' => $penempatan->id,
            'judul' => 'Slicing & Integrasi Frontend Dashboard Peserta',
            'deskripsi' => 'Rancang antarmuka dashboard responsif dengan Design System MagKom dan hubungkan dengan data backend.',
            'kategori' => 'Frontend Development',
            'kompleksitas' => KompleksitasEnum::Tinggi,
            'deadline' => Carbon::now()->addDays(4)->toDateString(),
            'status' => StatusTugasEnum::Proses,
        ]);

        Tugas::create([
            'penempatan_id' => $penempatan->id,
            'judul' => 'Uji Fungsional & Penyusunan Dokumen Laporan Magang',
            'deskripsi' => 'Lakukan pengujian antarmuka dan mulai susun draf bab 3 laporan magang terkait kegiatan praktis.',
            'kategori' => 'Testing & Laporan',
            'kompleksitas' => KompleksitasEnum::Sedang,
            'deadline' => Carbon::now()->addDays(14)->toDateString(),
            'status' => StatusTugasEnum::Belum,
        ]);

        // 9. Tambahkan Jurnal Logbook Harian
        Logbook::where('penempatan_id', $penempatan->id)->delete();

        Logbook::create([
            'penempatan_id' => $penempatan->id,
            'minggu_ke' => 1,
            'tanggal' => Carbon::parse($penempatan->tgl_mulai)->addDays(5)->toDateString(),
            'kegiatan' => 'Orientasi lingkungan kerja di Bidang Aplikasi Informatika dan pengenalan alur kerja sistem informasi Kominfo Sleman bersama pembimbing lapangan.',
            'status' => StatusLogbookEnum::Terkirim,
            'catatan_pembimbing' => 'Bagus, teruskan mempelajari spesifikasi arsitektur proyek.',
        ]);

        Logbook::create([
            'penempatan_id' => $penempatan->id,
            'minggu_ke' => 2,
            'tanggal' => Carbon::parse($penempatan->tgl_mulai)->addDays(12)->toDateString(),
            'kegiatan' => 'Merancang komponen visual Blade UI dan penyesuaian token warna Design System MagKom untuk seluruh modul portal peserta.',
            'status' => StatusLogbookEnum::Terkirim,
            'catatan_pembimbing' => 'Desain rapi dan telah memenuhi standar pedoman warna Sleman.',
        ]);

        Logbook::create([
            'penempatan_id' => $penempatan->id,
            'minggu_ke' => 3,
            'tanggal' => Carbon::parse($penempatan->tgl_mulai)->addDays(15)->toDateString(),
            'kegiatan' => 'Menjalankan pengujian integrasi fungsional menyeluruh pada modul dashboard, logbook, tugas, dan verifikasi status penempatan magang.',
            'status' => StatusLogbookEnum::Terkirim,
        ]);
    }
}
