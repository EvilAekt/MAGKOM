<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Database\Seeders\DummyDuaPuluhPesertaSeeder;
use Illuminate\Console\Command;

class SeedDummyPesertaCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'magkom:seed-20-peserta';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Men-generate 20 akun dan skenario peserta magang komprehensif (berbagai status, kondisi, dan kasus)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Memulai seeding 20 peserta magang dengan berbagai kasus dan kondisi...');

        $seeder = new DummyDuaPuluhPesertaSeeder;
        $seeder->run();

        $this->newLine();
        $this->info('================================================================');
        $this->info(' BERHASIL! 20 Peserta Magang dengan Berbagai Kasus Telah Dibuat');
        $this->info(' Password seluruh akun peserta dummy: Password123!');
        $this->info('================================================================');
        $this->newLine();

        $this->table(
            ['No', 'Nama Peserta', 'Email', 'Institusi', 'Kasus & Kondisi'],
            [
                ['1', 'Aditya Pratama', 'aditya.pratama@mail.ugm.ac.id', 'UGM', 'Pendaftaran Baru (< 3 Hari / Dalam SLA)'],
                ['2', 'Bunga Citra Lestari', 'bunga.citra@student.uny.ac.id', 'UNY', 'Pendaftaran SLA Breached (> 3 Hari)'],
                ['3', 'Chandra Wijaya', 'chandra.wijaya@upnyk.ac.id', 'UPN Veteran', 'Perlu Perbaikan (Proposal Belum Sah)'],
                ['4', 'Dinda Kirana', 'dinda.kirana@uin-suka.ac.id', 'UIN Sunan Kalijaga', 'Draft (Berkas Belum Lengkap)'],
                ['5', 'Eko Prasetyo', 'eko.prasetyo@students.amikom.ac.id', 'Amikom', 'Waiting List Prioritas #1 (Siap Naik)'],
                ['6', 'Fani Rahmawati', 'fani.rahma@uii.ac.id', 'UII', 'Waiting List Prioritas #2'],
                ['7', 'Gilang Ramadhan', 'gilang.ramadhan@usd.ac.id', 'Sanata Dharma', 'Waiting List Ditutup Otomatis (AC-04-7)'],
                ['8', 'Hana Safitri', 'hana.safitri@mail.uajy.ac.id', 'Atma Jaya', 'Pendaftaran Ditolak Verifikasi'],
                ['9', 'Indra Kurniawan', 'indra.kurniawan@upnyk.ac.id', 'UPN Veteran', 'Penempatan Batal Pra-Mulai (Kuota Balik)'],
                ['10', 'Joko Susilo', 'joko.susilo@ugm.ac.id', 'UGM', 'Penempatan Aktif (Minggu Pertama)'],
                ['11', 'Kartika Putri', 'kartika.putri@uny.ac.id', 'UNY', 'Penempatan Aktif Teladan (Tugas & Logbook OK)'],
                ['12', 'Lukman Hakim', 'lukman.hakim@upnyk.ac.id', 'UPN Veteran', 'Logbook SLA Breached (>3 Hari Belum Reviu)'],
                ['13', 'Maya Anggraini', 'maya.anggraini@amikom.ac.id', 'Amikom', 'Tugas Terlambat (Overdue BR-16)'],
                ['14', 'Naufal Rizky', 'naufal.rizky@uii.ac.id', 'UII', 'Tugas Mendapat Catatan Revisi'],
                ['15', 'Olivia Tan', 'olivia.tan@komdigi.go.id', 'Poltek SSN', 'Penempatan Jalur Slot Khusus Mitra'],
                ['16', 'Panji Nugroho', 'panji.nugroho@ugm.ac.id', 'UGM', 'Masa Magang Habis (Menunggu Nilai)'],
                ['17', 'Qori Amanda', 'qori.amanda@uny.ac.id', 'UNY', 'Penilaian Masih Draft'],
                ['18', 'Rian Ardiansyah', 'rian.ardiansyah@upnyk.ac.id', 'UPN Veteran', 'Nilai Final Terbit (Predikat A)'],
                ['19', 'Siti Nurhaliza', 'siti.nurhaliza@amikom.ac.id', 'Amikom', 'Selesai Magang / Arsip Alumni (BR-07)'],
                ['20', 'Taufik Hidayat', 'taufik.hidayat@uii.ac.id', 'UII', 'Mengundurkan Diri Tengah Periode (Hangus)'],
            ]
        );

        return Command::SUCCESS;
    }
}
