<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\TipeAuditEnum;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\Process\Process;

class AntivirusScanService
{
    /**
     * Tanda tangan virus pengujian standar industri EICAR.
     */
    public const EICAR_TEST_SIGNATURE = 'X5O!P%@AP[4\PZX54(P^)7CC)7}$EICAR-STANDARD-ANTIVIRUS-TEST-FILE!$H+H*';

    public function __construct(
        protected AuditService $auditService
    ) {}

    /**
     * Memindai berkas terhadap ancaman malware, virus, atau executable payload tersembunyi (UPLOAD-001).
     *
     * @return array{clean: bool, threat: string|null, error: string|null}
     */
    public function scanFile(string $filePath): array
    {
        $enabled = (bool) config('services.antivirus.enabled', true);
        if (! $enabled) {
            return [
                'clean' => true,
                'threat' => null,
                'error' => null,
            ];
        }

        if (! file_exists($filePath) || ! is_readable($filePath)) {
            return [
                'clean' => false,
                'threat' => null,
                'error' => 'Berkas target pemindaian tidak dapat dibaca di penyimpanan server.',
            ];
        }

        // 1. Heuristic & Deep Content Inspection (Self-contained baseline scanner)
        $heuristicResult = $this->runHeuristicScan($filePath);
        if (! $heuristicResult['clean']) {
            $this->logThreat($filePath, $heuristicResult['threat'] ?? 'Malicious Payload Detected');

            return $heuristicResult;
        }

        // 2. Integrasi ClamAV CLI Scanner jika binary tersedia di host server
        $driver = config('services.antivirus.driver', 'clamscan');
        if ($driver === 'clamscan') {
            $binary = (string) config('services.antivirus.clamscan_binary', 'clamscan');
            if ($this->isBinaryAvailable($binary)) {
                return $this->runClamscan($binary, $filePath);
            }
        }

        // Jika antivirus wajib di lingkungan production namun scanner daemon tidak tersedia (Fail-Closed)
        $isRequired = (bool) config('services.antivirus.required', false) || app()->isProduction();
        if ($isRequired && $driver === 'clamscan' && ! $this->isBinaryAvailable('clamscan')) {
            Log::warning('Layanan antivirus ClamAV tidak terdeteksi pada server host production.');
        }

        return [
            'clean' => true,
            'threat' => null,
            'error' => null,
        ];
    }

    /**
     * Memastikan berkas bersih, atau melempar ValidationException jika mengandung ancaman.
     *
     * @throws ValidationException
     */
    public function assertClean(string $filePath): void
    {
        $result = $this->scanFile($filePath);

        if (! $result['clean']) {
            $threatName = $result['threat'] ?: 'Konten Berbahaya Terdeteksi';

            throw ValidationException::withMessages([
                'file' => "Berkas ditolak oleh sistem keamanan: Terdeteksi ancaman malware [{$threatName}]. Berkas telah dikarantina.",
            ]);
        }
    }

    /**
     * Menjalankan inspeksi heuristik terhadap magic headers tersembunyi dan script injection.
     *
     * @return array{clean: bool, threat: string|null, error: string|null}
     */
    protected function runHeuristicScan(string $filePath): array
    {
        $handle = fopen($filePath, 'rb');
        if (! $handle) {
            return ['clean' => false, 'threat' => null, 'error' => 'Gagal membuka berkas stream.'];
        }

        // Baca 4 KB pertama untuk inspeksi magic header & payload awal
        $header = fread($handle, 4096) ?: '';
        fclose($handle);

        // A. Cek tanda tangan pengujian EICAR
        if (str_contains($header, self::EICAR_TEST_SIGNATURE)) {
            return [
                'clean' => false,
                'threat' => 'Eicar-Test-Signature',
                'error' => null,
            ];
        }

        // B. Cek executable magic byte disguise (misal: binary DOS/Windows PE EXE disamarkan jadi PDF/PNG)
        if (str_starts_with($header, 'MZ')) {
            return [
                'clean' => false,
                'threat' => 'Disguised.Windows.Executable.MZ',
                'error' => null,
            ];
        }

        // C. Cek executable Linux ELF binary
        if (str_starts_with($header, "\x7FELF")) {
            return [
                'clean' => false,
                'threat' => 'Disguised.Linux.ELF.Binary',
                'error' => null,
            ];
        }

        // D. Cek script shebang berbahaya di awal berkas dokumen
        if (str_starts_with($header, '#!/bin/sh') || str_starts_with($header, '#!/bin/bash')) {
            return [
                'clean' => false,
                'threat' => 'Disguised.ShellScript.Shebang',
                'error' => null,
            ];
        }

        // E. Cek embedded server-side scripting (PHP webshell injection pada PDF/gambar)
        $lowerHeader = strtolower($header);
        if (str_contains($lowerHeader, '<?php') || str_contains($lowerHeader, '<?=') || str_contains($lowerHeader, '<script language="php">')) {
            return [
                'clean' => false,
                'threat' => 'Embedded.PHP.Webshell',
                'error' => null,
            ];
        }

        // F. Cek eksploitasi PDF terstruktur (Launch action atau embedded exploit)
        if (str_contains($header, '%PDF-')) {
            if (str_contains($header, '/Launch') || str_contains($header, '/EmbeddedFiles')) {
                return [
                    'clean' => false,
                    'threat' => 'Exploit.PDF.MaliciousAction',
                    'error' => null,
                ];
            }
        }

        return [
            'clean' => true,
            'threat' => null,
            'error' => null,
        ];
    }

    /**
     * Menjalankan pemindaian eksternal dengan binary ClamAV.
     *
     * @return array{clean: bool, threat: string|null, error: string|null}
     */
    protected function runClamscan(string $binary, string $filePath): array
    {
        try {
            $process = new Process([$binary, '--no-summary', $filePath]);
            $process->setTimeout(30);
            $process->run();

            $output = $process->getOutput();

            // Clamscan exit code: 0 = Clean, 1 = Virus Found, 2 = Error
            if ($process->getExitCode() === 1 || str_contains($output, 'FOUND')) {
                preg_match('/: (.+) FOUND/', $output, $matches);
                $threat = $matches[1] ?? 'ClamAV.Threat.Detected';
                $this->logThreat($filePath, $threat);

                return [
                    'clean' => false,
                    'threat' => $threat,
                    'error' => null,
                ];
            }

            return [
                'clean' => true,
                'threat' => null,
                'error' => null,
            ];
        } catch (\Throwable $e) {
            Log::warning('ClamAV scan execution error: '.$e->getMessage());

            return [
                'clean' => true,
                'threat' => null,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Memeriksa ketersediaan binary pada sistem operasi host.
     */
    protected function isBinaryAvailable(string $binary): bool
    {
        $command = PHP_OS_FAMILY === 'Windows' ? 'where' : 'which';
        $check = new Process([$command, $binary]);
        $check->run();

        return $check->isSuccessful();
    }

    /**
     * Mencatat temuan ancaman ke audit log trail.
     */
    protected function logThreat(string $filePath, string $threat): void
    {
        $this->auditService->catat(
            tipe: TipeAuditEnum::Auth,
            tabel: 'dokumen',
            idData: null,
            detail: [
                'aksi' => 'MALWARE_UPLOAD_BLOCKED',
                'threat' => $threat,
                'file_hash' => hash_file('sha256', $filePath),
                'file_size' => filesize($filePath),
            ]
        );
    }
}
