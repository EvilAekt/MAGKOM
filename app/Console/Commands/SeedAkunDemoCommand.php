<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Database\Seeders\AkunDemoLengkapSeeder;
use Illuminate\Console\Command;

class SeedAkunDemoCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'magkom:seed-akun-demo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Men-generate akun demo resmi (SuperAdmin, Admin, Pembimbing, dan Peserta Magang) sesuai spesifikasi';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (app()->isProduction()) {
            $this->error('PERINTAH SEEDER INI DILARANG DI LINGKUNGAN PRODUCTION!');

            return Command::FAILURE;
        }

        $this->info('Memulai seeding akun demo...');

        $seeder = new AkunDemoLengkapSeeder;
        $seeder->run();

        $this->newLine();
        $this->info('========================================================================');
        $this->info(' BERHASIL! Seluruh Akun Demo Sesuai Spesifikasi Telah Dibuat');
        $this->info(' Kata sandi semua akun: Password123!');
        $this->info('========================================================================');
        $this->newLine();

        $this->info('1. AKUN STAF UTAMA (Tepat 1 Akun Per Peran):');
        $this->table(
            ['Role', 'Email Login', 'Password', 'Redirect Dashboard'],
            [
                ['SuperAdmin', 'superadmin@gmail.com', 'Password123!', '/superadmin/dashboard'],
                ['Admin Verifikator', 'admin@gmail.com', 'Password123!', '/admin/dashboard'],
                ['Pembimbing Lapangan', 'pembimbing@gmail.com', 'Password123!', '/pembimbing/dashboard'],
                ['Peserta Magang', 'peserta@gmail.com', 'Password123!', '/peserta/dashboard'],
            ]
        );

        $this->newLine();
        $this->info('2. 10 AKUN PESERTA MAGANG DENGAN STATUS ACAK & BERVARIASI:');
        $this->table(
            ['No', 'Nama Peserta', 'Email', 'Institusi', 'Kondisi & Status Magang'],
            [
                ['1', 'Bagas Saputra', 'peserta1@gmail.com', 'UGM', 'Belum Mengajukan (Draft Pendaftaran)'],
                ['2', 'Anisa Rahmawati', 'peserta2@gmail.com', 'SMK N 1 Sleman', 'Belum Mengajukan (Baru Daftar / Onboarding)'],
                ['3', 'Chandra Wijaya', 'peserta3@gmail.com', 'UPN Veteran', 'Sudah Diajukan (Menunggu Verifikasi Admin)'],
                ['4', 'Dinda Kirana', 'peserta4@gmail.com', 'UIN Sunan Kalijaga', 'Sudah Diajukan (Perlu Perbaikan Dokumen)'],
                ['5', 'Eko Prasetyo', 'peserta5@gmail.com', 'Amikom', 'Sudah Diterima (Pra-Magang / Menunggu Mulai)'],
                ['6', 'Fani Rahmawati', 'peserta6@gmail.com', 'UII', 'Sudah Aktif (Belum Kerjakan Tugas & Belum Logbook)'],
                ['7', 'Gilang Ramadhan', 'peserta7@gmail.com', 'Sanata Dharma', 'Sudah Aktif (Sudah Tugas, Belum Isi Logbook)'],
                ['8', 'Hana Safitri', 'peserta8@gmail.com', 'Atma Jaya', 'Sudah Aktif (Tugas Proses & Logbook Revisi)'],
                ['9', 'Indra Kurniawan', 'peserta9@gmail.com', 'UNY', 'Sudah Aktif (Sudah Tugas & Logbook Rutin Disetujui)'],
                ['10', 'Joko Susilo', 'peserta10@gmail.com', 'SMK N 2 Depok', 'Selesai Magang (Alumni)'],
            ]
        );

        return Command::SUCCESS;
    }
}
