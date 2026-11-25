<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\JalurEnum;
use App\Enums\JenjangEnum;
use App\Enums\KompleksitasEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusPendaftaranEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\StatusTugasEnum;
use App\Enums\SumberPenempatanEnum;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\KelompokAnggota;
use App\Models\KelompokMagang;
use App\Models\Kuota;
use App\Models\Pendaftaran;
use App\Models\Penempatan;
use App\Models\PesertaProfil;
use App\Models\Tugas;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SeedPesertaKelompokDiterimaCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'magkom:seed-kelompok-diterima';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Membuat dan mengaktifkan akun peserta kelompok yang sudah diterima beserta seluruh anggotanya agar bisa login ke dashboard';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $defaultPassword = Hash::make('Password123!');
        $now = Carbon::now('Asia/Jakarta');

        $this->info('Memulai penyiapan akun kelompok yang diterima dan aktif...');

        DB::transaction(function () use ($defaultPassword, $now) {
            // 1. Ambil atau buat data master pendukung
            $bidang = Bidang::first() ?? Bidang::create([
                'nama_bidang' => 'Bidang Aplikasi Informatika (Aptika)',
                'deskripsi' => 'Pengembangan ekosistem digital Sleman.',
                'aktif' => true,
            ]);

            $pembimbing = User::where('role', RoleEnum::Pembimbing->value)
                ->where('email', 'pembimbing@gmail.com')
                ->first()
                ?? User::where('role', RoleEnum::Pembimbing->value)->first();

            if (! $pembimbing) {
                $pembimbing = User::create([
                    'nama' => 'Budi Pembimbing',
                    'email' => 'pembimbing@gmail.com',
                    'password' => $defaultPassword,
                    'role' => RoleEnum::Pembimbing,
                    'bidang_id' => $bidang->id,
                    'email_verified_at' => $now,
                    'status_aktif' => true,
                ]);
            }

            $jurusan = Jurusan::first() ?? Jurusan::create([
                'nama_jurusan' => 'Teknologi Informasi',
                'kategori' => 'perguruan_tinggi',
                'aktif' => true,
            ]);

            $bulanAktif = BulanMagang::where('aktif', true)->first() ?? BulanMagang::create([
                'nama_bulan' => $now->translatedFormat('F Y'),
                'bulan_mulai' => $now->copy()->startOfMonth(),
                'tgl_buka' => $now->copy()->subMonth()->startOfMonth(),
                'tgl_tutup' => $now->copy()->subMonth()->endOfMonth(),
                'aktif' => true,
            ]);

            $kuota = Kuota::firstOrCreate(
                [
                    'bulan_magang_id' => $bulanAktif->id,
                    'jurusan_id' => $jurusan->id,
                ],
                [
                    'jumlah' => 20,
                    'terisi' => 5,
                    'aktif' => true,
                ]
            );

            // =========================================================================
            // 2. Buat / Perbarui Kelompok Demo Utama: "Tim Inovasi Sleman Digital"
            // =========================================================================
            $ketuaUser = User::updateOrCreate(
                ['email' => 'ketua.tim@gmail.com'],
                [
                    'nama' => 'Arya Pratama',
                    'password' => $defaultPassword,
                    'role' => RoleEnum::Peserta,
                    'email_verified_at' => $now,
                    'status_aktif' => true,
                ]
            );

            PesertaProfil::updateOrCreate(
                ['user_id' => $ketuaUser->id],
                [
                    'institusi' => 'Universitas Gadjah Mada',
                    'jurusan_id' => $jurusan->id,
                    'jenjang' => JenjangEnum::S1,
                    'nim_nisn' => '22/498111/SV/21001',
                    'no_hp' => '081234567890',
                    'nama_kontak_darurat' => 'Bambang Pratama',
                    'no_kontak_darurat' => '081234567899',
                    'hubungan_kontak_darurat' => 'Orang Tua',
                ]
            );

            $kelompok = KelompokMagang::updateOrCreate(
                ['ketua_user_id' => $ketuaUser->id],
                ['nama_kelompok' => 'Tim Inovasi Sleman Digital']
            );

            // Pendaftaran Kelompok (Status: Diterima)
            $pendaftaran = Pendaftaran::updateOrCreate(
                ['user_id' => $ketuaUser->id, 'kelompok_magang_id' => $kelompok->id],
                [
                    'bulan_magang_id' => $bulanAktif->id,
                    'kuota_id' => $kuota->id,
                    'tipe' => 'kelompok',
                    'durasi_bulan' => 3,
                    'status' => StatusPendaftaranEnum::Diterima,
                    'diajukan_at' => $now->copy()->subDays(15),
                    'diputuskan_at' => $now->copy()->subDays(10),
                    'catatan' => 'Selamat! Permohonan magang kelompok Anda telah resmi diterima di Diskominfo Sleman.',
                ]
            );

            // Anggota 1 & Anggota 2
            $anggotaData = [
                [
                    'email' => 'anggota1.tim@gmail.com',
                    'nama' => 'Citra Dewi',
                    'nim' => '22/498112/SV/21002',
                    'no_hp' => '081234567891',
                ],
                [
                    'email' => 'anggota2.tim@gmail.com',
                    'nama' => 'Dimas Wahyu',
                    'nim' => '22/498113/SV/21003',
                    'no_hp' => '081234567892',
                ],
            ];

            $allTeamUsers = collect([$ketuaUser]);

            foreach ($anggotaData as $m) {
                $mUser = User::updateOrCreate(
                    ['email' => $m['email']],
                    [
                        'nama' => $m['nama'],
                        'password' => $defaultPassword,
                        'role' => RoleEnum::Peserta,
                        'email_verified_at' => $now,
                        'status_aktif' => true,
                    ]
                );

                PesertaProfil::updateOrCreate(
                    ['user_id' => $mUser->id],
                    [
                        'institusi' => 'Universitas Gadjah Mada',
                        'jurusan_id' => $jurusan->id,
                        'jenjang' => JenjangEnum::S1,
                        'nim_nisn' => $m['nim'],
                        'no_hp' => $m['no_hp'],
                        'nama_kontak_darurat' => 'Keluarga '.$m['nama'],
                        'no_kontak_darurat' => '081234567888',
                        'hubungan_kontak_darurat' => 'Orang Tua',
                    ]
                );

                KelompokAnggota::updateOrCreate(
                    [
                        'kelompok_magang_id' => $kelompok->id,
                        'email' => $m['email'],
                    ],
                    [
                        'user_id' => $mUser->id,
                        'nama' => $m['nama'],
                        'nim_nisn' => $m['nim'],
                        'no_hp' => $m['no_hp'],
                    ]
                );

                $allTeamUsers->push($mUser);
            }

            // Buat Penempatan Aktif untuk SETIAP anggota tim (Ketua + Anggota)
            foreach ($allTeamUsers as $tUser) {
                $penempatan = Penempatan::updateOrCreate(
                    [
                        'peserta_id' => $tUser->id,
                        'pendaftaran_id' => $pendaftaran->id,
                    ],
                    [
                        'kuota_id' => $kuota->id,
                        'bidang_id' => $bidang->id,
                        'pembimbing_id' => $pembimbing->id,
                        'jalur' => JalurEnum::Umum,
                        'sumber' => SumberPenempatanEnum::Online,
                        'tgl_mulai' => $now->copy()->subDays(10)->toDateString(),
                        'tgl_selesai' => $now->copy()->addDays(80)->toDateString(),
                        'status' => StatusPenempatanEnum::Aktif,
                    ]
                );

                // Tugas Mandiri untuk peserta ini jika belum ada
                Tugas::firstOrCreate(
                    [
                        'penempatan_id' => $penempatan->id,
                        'judul' => 'Eksplorasi Arsitektur Modul Sleman Pintar',
                    ],
                    [
                        'tipe' => 'individu',
                        'deskripsi' => 'Pelajari dan susun dokumentasi modul arsitektur penugasan kelompok Anda.',
                        'kategori' => 'Riset & Perancangan',
                        'kompleksitas' => KompleksitasEnum::Sedang,
                        'deadline' => $now->copy()->addDays(7),
                        'status' => StatusTugasEnum::Proses,
                        'status_publikasi' => 'dipublikasikan',
                    ]
                );
            }

            // =========================================================================
            // 3. Sinkronkan juga anggota kelompok eksisting yang ada di DB
            // =========================================================================
            $existingKelompokList = KelompokMagang::with(['ketua', 'anggota', 'pendaftaran'])->get();
            foreach ($existingKelompokList as $exKel) {
                $exPendaftaran = $exKel->pendaftaran()->where('status', StatusPendaftaranEnum::Diterima)->first()
                    ?? $exKel->pendaftaran()->latest()->first();

                if (! $exPendaftaran) {
                    continue;
                }

                $exPenempatanKetua = Penempatan::where('peserta_id', $exKel->ketua_user_id)->first();
                $exPembimbingId = $exPenempatanKetua?->pembimbing_id ?? $pembimbing->id;
                $exBidangId = $exPenempatanKetua?->bidang_id ?? $bidang->id;

                foreach ($exKel->anggota as $exAnggota) {
                    if (! $exAnggota->email) {
                        continue;
                    }

                    $exUser = User::firstOrCreate(
                        ['email' => $exAnggota->email],
                        [
                            'nama' => $exAnggota->nama,
                            'password' => $defaultPassword,
                            'role' => RoleEnum::Peserta,
                            'email_verified_at' => $now,
                            'status_aktif' => true,
                        ]
                    );

                    $exAnggota->update(['user_id' => $exUser->id]);

                    PesertaProfil::firstOrCreate(
                        ['user_id' => $exUser->id],
                        [
                            'institusi' => $exKel->institusi ?? 'Universitas Gadjah Mada',
                            'jurusan_id' => $jurusan->id,
                            'jenjang' => JenjangEnum::S1,
                            'nim_nisn' => $exAnggota->nim_nisn ?? '12345678',
                            'no_hp' => $exAnggota->no_hp ?? '081234567899',
                            'nama_kontak_darurat' => 'Orang Tua '.$exAnggota->nama,
                            'no_kontak_darurat' => '081234567888',
                            'hubungan_kontak_darurat' => 'Orang Tua',
                        ]
                    );

                    Penempatan::firstOrCreate(
                        [
                            'peserta_id' => $exUser->id,
                            'pendaftaran_id' => $exPendaftaran->id,
                        ],
                        [
                            'kuota_id' => $exPendaftaran->kuota_id ?? $kuota->id,
                            'bidang_id' => $exBidangId,
                            'pembimbing_id' => $exPembimbingId,
                            'jalur' => JalurEnum::Umum,
                            'sumber' => SumberPenempatanEnum::Online,
                            'tgl_mulai' => $now->copy()->subDays(10)->toDateString(),
                            'tgl_selesai' => $now->copy()->addDays(80)->toDateString(),
                            'status' => StatusPenempatanEnum::Aktif,
                        ]
                    );
                }
            }
        });

        $this->newLine();
        $this->info('========================================================================');
        $this->info(' BERHASIL! Akun Kelompok Diterima & Seluruh Anggota Siap Login');
        $this->info(' Kata sandi semua akun: Password123!');
        $this->info('========================================================================');
        $this->newLine();

        $this->table(
            ['Peran dalam Tim', 'Nama Akun', 'Email Login', 'Status Magang', 'Tujuan Redirect'],
            [
                ['Ketua Kelompok', 'Arya Pratama', 'ketua.tim@gmail.com', 'Diterima & Aktif', '/peserta/dashboard'],
                ['Anggota 1', 'Citra Dewi', 'anggota1.tim@gmail.com', 'Diterima & Aktif', '/peserta/dashboard'],
                ['Anggota 2', 'Dimas Wahyu', 'anggota2.tim@gmail.com', 'Diterima & Aktif', '/peserta/dashboard'],
                ['Anggota (SUPER)', 'nono', 'nono@gmail.com', 'Diterima & Aktif', '/peserta/dashboard'],
                ['Anggota (SUPER)', 'jokoway', 'jokoway@gmail.com', 'Diterima & Aktif', '/peserta/dashboard'],
                ['Anggota (Smart Sleman)', 'Rian Hidayat', 'rian@mail.ugm.ac.id', 'Diterima & Aktif', '/peserta/dashboard'],
                ['Anggota (Smart Sleman)', 'Siti Rahmawati', 'siti@mail.ugm.ac.id', 'Diterima & Aktif', '/peserta/dashboard'],
            ]
        );

        return Command::SUCCESS;
    }
}
