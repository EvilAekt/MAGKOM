<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Pendaftaran;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class GenerateSampleDocumentsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'magkom:generate-sample-docs {pendaftaran_id=2}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menghasilkan berkas PDF sampel valid dan realistis untuk dokumen pendaftaran peserta';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $pendaftaranId = (int) $this->argument('pendaftaran_id');

        $pendaftaran = Pendaftaran::with(['user.profil.jurusan', 'bulanMagang', 'kuota.jurusan', 'dokumen.jenisDokumen'])
            ->find($pendaftaranId);

        if (! $pendaftaran) {
            $this->error("Pendaftaran dengan ID {$pendaftaranId} tidak ditemukan.");

            return Command::FAILURE;
        }

        $this->info("Memproses dokumen untuk Pendaftaran ID: {$pendaftaran->id} (#REG-".str_pad((string) $pendaftaran->id, 5, '0', STR_PAD_LEFT).')...');

        $peserta = $pendaftaran->user;
        $profil = $peserta?->profil;
        $tglDokumen = Carbon::now('Asia/Jakarta')->translatedFormat('d F Y');

        $count = 0;
        foreach ($pendaftaran->dokumen as $dokumen) {
            $jenis = $dokumen->jenisDokumen;
            if (! $jenis) {
                continue;
            }

            $nomorDokumen = sprintf('421.3/%03d/%s/2026', $dokumen->id, strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $jenis->nama), 0, 3)));

            // 1. Render PDF menggunakan DomPDF
            $pdf = Pdf::loadView('pdf.sample-document', [
                'pendaftaran' => $pendaftaran,
                'dokumen' => $dokumen,
                'jenis' => $jenis,
                'peserta' => $peserta,
                'profil' => $profil,
                'tglDokumen' => $tglDokumen,
                'nomorDokumen' => $nomorDokumen,
            ])->setPaper('a4', 'portrait');

            $pdfBinary = $pdf->output();
            $ukuran = strlen($pdfBinary);
            $hash = hash('sha256', $pdfBinary);

            // 2. Simpan fisik ke storage private
            Storage::disk('private')->put($dokumen->path, $pdfBinary);

            // 3. Update database
            $dokumen->update([
                'ukuran' => $ukuran,
                'hash' => $hash,
                'mime' => 'application/pdf',
            ]);

            $count++;
            $this->line(" [✓] Dokumen #{$dokumen->id} ({$jenis->nama}) -> ".number_format($ukuran / 1024, 1)." KB (Path: {$dokumen->path})");
        }

        $this->info("Berhasil men-generate {$count} berkas PDF valid untuk Pendaftaran ID {$pendaftaran->id}!");

        return Command::SUCCESS;
    }
}
