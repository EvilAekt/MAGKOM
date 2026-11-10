<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\TipeAuditEnum;
use App\Models\Pendaftaran;
use App\Models\Penempatan;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportReportService
{
    public function __construct(
        protected AuditService $auditService,
        protected XlsxExportService $xlsxExportService
    ) {}

    /**
     * Sanitasi formula injection pada sel CSV/Spreadsheet (AC-17-2).
     * Mencegah eksekusi ekspresi dinamis jika diawali '=', '+', '-', atau '@'.
     */
    public function sanitizeCell(mixed $value): string
    {
        if ($value === null) {
            return '';
        }

        $str = (string) $value;
        if ($str === '') {
            return '';
        }

        $karakterBerbahaya = ['=', '+', '-', '@', "\t", "\r"];
        if (in_array($str[0], $karakterBerbahaya, true)) {
            return "'".$str;
        }

        return $str;
    }

    /**
     * Ekspor rekapitulasi pengajuan pendaftaran (AC-17-1).
     * Mendukung format XLSX (Tabel terformat & rapi) dan CSV Sanitasi (sep=,).
     *
     * @param  array<string, mixed>  $filters
     */
    public function exportPendaftaran(User $user, array $filters = [], string $format = 'csv'): Response
    {
        $query = Pendaftaran::with(['user.profil.jurusan', 'bulanMagang', 'kuota.jurusan']);

        if (! empty($filters['bulan_magang_id'])) {
            $query->where('bulan_magang_id', $filters['bulan_magang_id']);
        }
        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (! empty($filters['jurusan_id'])) {
            $jurusanId = (int) $filters['jurusan_id'];
            $query->where(function ($q) use ($jurusanId) {
                $q->whereHas('kuota', fn ($qk) => $qk->where('jurusan_id', $jurusanId))
                    ->orWhereHas('user.profil', fn ($qp) => $qp->where('jurusan_id', $jurusanId));
            });
        }
        if (! empty($filters['dari_tanggal'])) {
            $query->where('created_at', '>=', $filters['dari_tanggal'].' 00:00:00');
        }
        if (! empty($filters['sampai_tanggal'])) {
            $query->where('created_at', '<=', $filters['sampai_tanggal'].' 23:59:59');
        }
        if (! empty($filters['tipe'])) {
            $tipe = strtolower(trim((string) $filters['tipe']));
            if ($tipe === 'individu' || $tipe === 'mandiri') {
                $query->where(function ($q) {
                    $q->where('tipe', 'mandiri')
                        ->orWhere('tipe', 'individu')
                        ->orWhereNull('tipe');
                });
            } elseif ($tipe === 'kelompok') {
                $query->where('tipe', 'kelompok');
            }
        }
        if (! empty($filters['q'])) {
            $search = '%'.trim((string) $filters['q']).'%';
            $query->where(function ($qu) use ($search) {
                $qu->whereHas('user', function ($uq) use ($search) {
                    $uq->where('nama', 'like', $search)
                        ->orWhere('email', 'like', $search);
                })->orWhereHas('user.profil', function ($pq) use ($search) {
                    $pq->where('nim_nisn', 'like', $search)
                        ->orWhere('institusi', 'like', $search);
                });
            });
        }

        $records = $query->latest('created_at')->get();

        // Audit Trail akses ekspor data (AC-13-4, AC-17-2)
        $this->auditService->catat(
            tipe: TipeAuditEnum::AksesData,
            tabel: 'pendaftaran',
            idData: null,
            nilaiLama: null,
            nilaiBaru: ['filter' => $filters, 'total_baris' => $records->count(), 'format' => $format],
            detail: [
                'aksi' => match ($format) {
                    'xlsx' => 'ekspor_pendaftaran_xlsx',
                    'pdf' => 'ekspor_pendaftaran_pdf',
                    default => 'ekspor_pendaftaran_csv',
                },
                'entitas' => 'pendaftaran',
                'jumlah_baris' => $records->count(),
                'filter' => $filters,
            ]
        );

        $headers = [
            'ID', 'Nama Peserta', 'Email', 'NIM/NISN', 'Institusi',
            'Jurusan', 'Bulan Mulai', 'Durasi (Bulan)', 'Status', 'Tanggal Diajukan',
        ];

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('pdf.rekap-pendaftaran', [
                'records' => $records,
                'generatedAt' => now(),
                'filters' => $filters,
            ])->setPaper('a4', 'landscape');

            return $pdf->download('rekap-pendaftaran-'.date('YmdHis').'.pdf');
        }

        if ($format === 'xlsx') {
            $rows = [];
            foreach ($records as $p) {
                $jurusanNama = $p->kuota?->jurusan?->nama_jurusan
                    ?? ($p->user?->profil?->jurusan?->nama_jurusan ?? '-');

                $rows[] = [
                    $p->id,
                    $p->user?->nama ?? '-',
                    $p->user?->email ?? '-',
                    $p->user?->profil?->nim_nisn ?? '-',
                    $p->user?->profil?->institusi ?? '-',
                    $jurusanNama,
                    $p->bulanMagang?->bulan_mulai?->translatedFormat('F Y') ?? ($p->bulanMagang?->bulan_mulai?->format('F Y') ?? '-'),
                    $p->durasi_bulan ?? 0,
                    $p->status?->label() ?? (string) ($p->status?->value ?? '-'),
                    $p->diajukan_at?->toDateTimeString() ?? $p->created_at->toDateTimeString(),
                ];
            }

            return $this->xlsxExportService->downloadXlsx(
                'rekap-pendaftaran-'.date('YmdHis').'.xlsx',
                'Rekap Pendaftaran',
                $headers,
                $rows
            );
        }

        return response()->streamDownload(function () use ($headers, $records) {
            $handle = fopen('php://output', 'w');
            // UTF-8 BOM + direktif sep=, untuk kompatibilitas Microsoft Excel (AC-17-2)
            fwrite($handle, "\xEF\xBB\xBFsep=,\r\n");
            fputcsv($handle, $headers);

            foreach ($records as $p) {
                $jurusanNama = $p->kuota?->jurusan?->nama_jurusan
                    ?? ($p->user?->profil?->jurusan?->nama_jurusan ?? '-');

                fputcsv($handle, [
                    $this->sanitizeCell($p->id),
                    $this->sanitizeCell($p->user?->nama),
                    $this->sanitizeCell($p->user?->email),
                    $this->sanitizeCell($p->user?->profil?->nim_nisn),
                    $this->sanitizeCell($p->user?->profil?->institusi),
                    $this->sanitizeCell($jurusanNama),
                    $this->sanitizeCell($p->bulanMagang?->bulan_mulai?->format('F Y')),
                    $this->sanitizeCell($p->durasi_bulan),
                    $this->sanitizeCell($p->status?->value),
                    $this->sanitizeCell($p->diajukan_at?->toDateTimeString() ?? $p->created_at->toDateTimeString()),
                ]);
            }

            fclose($handle);
        }, 'rekap-pendaftaran-'.date('YmdHis').'.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Ekspor rekapitulasi data penempatan peserta (AC-17-1).
     *
     * @param  array<string, mixed>  $filters
     */
    public function exportPenempatan(User $user, array $filters = [], string $format = 'csv'): Response
    {
        $query = Penempatan::with(['peserta.profil.jurusan', 'pembimbing', 'bidang', 'kuota.jurusan', 'slotKhusus']);

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (! empty($filters['jalur'])) {
            $query->where('jalur', $filters['jalur']);
        }
        if (! empty($filters['jurusan_id'])) {
            $jurusanId = (int) $filters['jurusan_id'];
            $query->where(function ($q) use ($jurusanId) {
                $q->whereHas('kuota', fn ($qk) => $qk->where('jurusan_id', $jurusanId))
                    ->orWhereHas('peserta.profil', fn ($qp) => $qp->where('jurusan_id', $jurusanId));
            });
        }
        if (! empty($filters['dari_tanggal'])) {
            $query->where('tgl_mulai', '>=', $filters['dari_tanggal']);
        }
        if (! empty($filters['sampai_tanggal'])) {
            $query->where('tgl_mulai', '<=', $filters['sampai_tanggal']);
        }
        if (! empty($filters['pembimbing_id'])) {
            $query->where('pembimbing_id', (int) $filters['pembimbing_id']);
        }
        if (! empty($filters['bidang_id'])) {
            $query->where('bidang_id', (int) $filters['bidang_id']);
        }
        if (! empty($filters['bulan_magang_id'])) {
            $bulanId = (int) $filters['bulan_magang_id'];
            $query->where(function ($q) use ($bulanId) {
                $q->whereHas('pendaftaran', fn ($qp) => $qp->where('bulan_magang_id', $bulanId))
                    ->orWhereHas('kuota', fn ($qk) => $qk->where('bulan_magang_id', $bulanId));
            });
        }
        if (! empty($filters['q'])) {
            $search = '%'.trim((string) $filters['q']).'%';
            $query->whereHas('peserta', function ($qu) use ($search) {
                $qu->where('nama', 'like', $search)
                    ->orWhere('email', 'like', $search)
                    ->orWhereHas('profil', fn ($qp) => $qp->where('institusi', 'like', $search));
            });
        }

        $records = $query->latest('tgl_mulai')->get();

        $this->auditService->catat(
            tipe: TipeAuditEnum::AksesData,
            tabel: 'penempatan',
            idData: null,
            nilaiLama: null,
            nilaiBaru: ['filter' => $filters, 'total_baris' => $records->count(), 'format' => $format],
            detail: [
                'aksi' => $format === 'xlsx' ? 'ekspor_penempatan_xlsx' : ($format === 'pdf' ? 'ekspor_penempatan_pdf' : 'ekspor_penempatan_csv'),
                'entitas' => 'penempatan',
                'jumlah_baris' => $records->count(),
                'filter' => $filters,
            ]
        );

        $headers = [
            'ID Penempatan', 'Nama Peserta', 'NIM/NISN', 'Institusi',
            'Jurusan', 'Jalur', 'Pembimbing', 'Bidang Unit',
            'Tanggal Mulai', 'Tanggal Selesai', 'Status Penempatan',
        ];

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('pdf.rekap-penempatan', [
                'records' => $records,
                'generatedAt' => now(),
                'filters' => $filters,
            ])->setPaper('a4', 'landscape');

            return $pdf->download('rekap-penempatan-'.date('YmdHis').'.pdf');
        }

        if ($format === 'xlsx') {
            $rows = [];
            foreach ($records as $pen) {
                $jurusanNama = $pen->kuota?->jurusan?->nama_jurusan
                    ?? ($pen->peserta?->profil?->jurusan?->nama_jurusan ?? '-');

                $rows[] = [
                    $pen->id,
                    $pen->peserta?->nama ?? '-',
                    $pen->peserta?->profil?->nim_nisn ?? '-',
                    $pen->peserta?->profil?->institusi ?? '-',
                    $jurusanNama,
                    $pen->jalur?->label() ?? (string) ($pen->jalur?->value ?? '-'),
                    $pen->pembimbing?->nama ?? '-',
                    $pen->bidang?->nama_bidang ?? '-',
                    $pen->tgl_mulai?->toDateString() ?? '-',
                    $pen->tgl_selesai?->toDateString() ?? '-',
                    $pen->status?->label() ?? (string) ($pen->status?->value ?? '-'),
                ];
            }

            return $this->xlsxExportService->downloadXlsx(
                'rekap-penempatan-'.date('YmdHis').'.xlsx',
                'Rekap Penempatan',
                $headers,
                $rows
            );
        }

        return response()->streamDownload(function () use ($headers, $records) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBFsep=,\r\n"); // UTF-8 BOM + sep=, directive
            fputcsv($handle, $headers);

            foreach ($records as $pen) {
                $jurusanNama = $pen->kuota?->jurusan?->nama_jurusan
                    ?? ($pen->peserta?->profil?->jurusan?->nama_jurusan ?? '-');

                fputcsv($handle, [
                    $this->sanitizeCell($pen->id),
                    $this->sanitizeCell($pen->peserta?->nama),
                    $this->sanitizeCell($pen->peserta?->profil?->nim_nisn),
                    $this->sanitizeCell($pen->peserta?->profil?->institusi),
                    $this->sanitizeCell($jurusanNama),
                    $this->sanitizeCell($pen->jalur?->label() ?? $pen->jalur?->value),
                    $this->sanitizeCell($pen->pembimbing?->nama),
                    $this->sanitizeCell($pen->bidang?->nama_bidang),
                    $this->sanitizeCell($pen->tgl_mulai?->toDateString()),
                    $this->sanitizeCell($pen->tgl_selesai?->toDateString()),
                    $this->sanitizeCell($pen->status?->label() ?? $pen->status?->value),
                ]);
            }

            fclose($handle);
        }, 'rekap-penempatan-'.date('YmdHis').'.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Helper umum unduh streamed CSV dengan UTF-8 BOM dan sanitasi formula injection (AC-17-2).
     *
     * @param  array<int, string>  $headers
     * @param  array<int, array<int, mixed>>  $rows
     */
    public function downloadCsvStream(string $filename, array $headers, array $rows): StreamedResponse
    {
        return response()->streamDownload(function () use ($headers, $rows) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBFsep=,\r\n"); // UTF-8 BOM + sep=, directive

            $sanitizedHeaders = array_map([$this, 'sanitizeCell'], $headers);
            fputcsv($handle, $sanitizedHeaders);

            foreach ($rows as $row) {
                $sanitizedRow = array_map([$this, 'sanitizeCell'], $row);
                fputcsv($handle, $sanitizedRow);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
