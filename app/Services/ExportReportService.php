<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\RoleEnum;
use App\Enums\TipeAuditEnum;
use App\Models\BulanMagang;
use App\Models\Kuota;
use App\Models\Pendaftaran;
use App\Models\Penempatan;
use App\Models\Penilaian;
use App\Models\SlotKhusus;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportReportService
{
    public function __construct(
        protected AuditService $auditService
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
            return "'" . $str;
        }

        return $str;
    }

    /**
     * Ekspor rekapitulasi pengajuan pendaftaran (AC-17-1).
     *
     * @param array<string, mixed> $filters
     */
    public function exportPendaftaran(User $user, array $filters = []): StreamedResponse
    {
        $query = Pendaftaran::with(['user.profil', 'bulanMagang', 'kuota.jurusan']);

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
            $query->where('created_at', '>=', $filters['dari_tanggal'] . ' 00:00:00');
        }
        if (! empty($filters['sampai_tanggal'])) {
            $query->where('created_at', '<=', $filters['sampai_tanggal'] . ' 23:59:59');
        }

        $records = $query->latest('created_at')->get();

        // Audit Trail akses ekspor data (AC-13-4, AC-17-2)
        $this->auditService->catat(
            tipe: TipeAuditEnum::AksesData,
            tabel: 'pendaftaran',
            idData: null,
            nilaiLama: null,
            nilaiBaru: ['filter' => $filters, 'total_baris' => $records->count()],
            detail: [
                'aksi' => 'ekspor_pendaftaran_csv',
                'entitas' => 'pendaftaran',
                'jumlah_baris' => $records->count(),
                'filter' => $filters,
            ]
        );

        $headers = [
            'ID', 'Nama Peserta', 'Email', 'NIM/NISN', 'Institusi',
            'Jurusan', 'Bulan Mulai', 'Durasi (Bulan)', 'Status', 'Tanggal Diajukan',
        ];

        return response()->streamDownload(function () use ($headers, $records) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF"); // UTF-8 BOM for Microsoft Excel compatibility
            fputcsv($handle, $headers);

            foreach ($records as $p) {
                fputcsv($handle, [
                    $this->sanitizeCell($p->id),
                    $this->sanitizeCell($p->user?->nama),
                    $this->sanitizeCell($p->user?->email),
                    $this->sanitizeCell($p->user?->profil?->nim_nisn),
                    $this->sanitizeCell($p->user?->profil?->institusi),
                    $this->sanitizeCell($p->kuota?->jurusan?->nama_jurusan),
                    $this->sanitizeCell($p->bulanMagang?->bulan_mulai?->format('F Y')),
                    $this->sanitizeCell($p->durasi_bulan),
                    $this->sanitizeCell($p->status?->value),
                    $this->sanitizeCell($p->diajukan_at?->toDateTimeString() ?? $p->created_at->toDateTimeString()),
                ]);
            }

            fclose($handle);
        }, 'rekap-pendaftaran-' . date('YmdHis') . '.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Ekspor rekapitulasi data penempatan peserta (AC-17-1).
     *
     * @param array<string, mixed> $filters
     */
    public function exportPenempatan(User $user, array $filters = []): StreamedResponse
    {
        $query = Penempatan::with(['peserta.profil', 'pembimbing', 'bidang', 'kuota.jurusan']);

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

        $records = $query->latest('tgl_mulai')->get();

        $this->auditService->catat(
            tipe: TipeAuditEnum::AksesData,
            tabel: 'penempatan',
            idData: null,
            nilaiLama: null,
            nilaiBaru: ['filter' => $filters, 'total_baris' => $records->count()],
            detail: [
                'aksi' => 'ekspor_penempatan_csv',
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

        return response()->streamDownload(function () use ($headers, $records) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF"); // UTF-8 BOM for Microsoft Excel compatibility
            fputcsv($handle, $headers);

            foreach ($records as $pen) {
                fputcsv($handle, [
                    $this->sanitizeCell($pen->id),
                    $this->sanitizeCell($pen->peserta?->nama),
                    $this->sanitizeCell($pen->peserta?->profil?->nim_nisn),
                    $this->sanitizeCell($pen->peserta?->profil?->institusi),
                    $this->sanitizeCell($pen->kuota?->jurusan?->nama_jurusan),
                    $this->sanitizeCell($pen->jalur?->value),
                    $this->sanitizeCell($pen->pembimbing?->nama),
                    $this->sanitizeCell($pen->bidang?->nama_bidang),
                    $this->sanitizeCell($pen->tgl_mulai?->toDateString()),
                    $this->sanitizeCell($pen->tgl_selesai?->toDateString()),
                    $this->sanitizeCell($pen->status?->value),
                ]);
            }

            fclose($handle);
        }, 'rekap-penempatan-' . date('YmdHis') . '.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Ekspor realisasi kuota umum vs slot khusus (AC-17-1).
     */
    public function exportRealisasiKuota(User $user): StreamedResponse
    {
        $kuotaList = Kuota::with(['bulanMagang', 'jurusan'])->get();
        $slotKhususList = SlotKhusus::all();

        $this->auditService->catat(
            tipe: TipeAuditEnum::AksesData,
            tabel: 'kuota',
            idData: null,
            nilaiLama: null,
            nilaiBaru: ['kuota_count' => $kuotaList->count(), 'slot_count' => $slotKhususList->count()],
            detail: [
                'aksi' => 'ekspor_realisasi_kuota_csv',
                'entitas' => 'kuota_dan_slot',
                'jumlah_baris' => $kuotaList->count() + $slotKhususList->count(),
            ]
        );

        $headers = ['Kategori Kuota', 'Periode Bulan', 'Target/Jurusan', 'Alokasi Jumlah', 'Terisi', 'Sisa'];

        return response()->streamDownload(function () use ($headers, $kuotaList, $slotKhususList) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF"); // UTF-8 BOM for Microsoft Excel compatibility
            fputcsv($handle, $headers);

            foreach ($kuotaList as $k) {
                $sisa = max(0, $k->jumlah - $k->terisi);
                fputcsv($handle, [
                    'Kuota Umum',
                    $this->sanitizeCell($k->bulanMagang?->bulan_mulai?->format('F Y')),
                    $this->sanitizeCell($k->jurusan?->nama_jurusan),
                    $this->sanitizeCell($k->jumlah),
                    $this->sanitizeCell($k->terisi),
                    $this->sanitizeCell($sisa),
                ]);
            }

            foreach ($slotKhususList as $s) {
                $sisaSlot = max(0, $s->jumlah - $s->terisi);
                fputcsv($handle, [
                    'Slot Khusus',
                    $this->sanitizeCell($s->bulan_mulai?->format('F Y')),
                    'Internal / Kerjasama',
                    $this->sanitizeCell($s->jumlah),
                    $this->sanitizeCell($s->terisi),
                    $this->sanitizeCell($sisaSlot),
                ]);
            }

            fclose($handle);
        }, 'realisasi-kuota-' . date('YmdHis') . '.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Ekspor rekapitulasi penilaian peserta - HANYA KHUSUS SuperAdmin (AC-17-1, A-20).
     */
    public function exportNilai(User $user): StreamedResponse
    {
        if (! $user->isSuperAdmin()) {
            throw new AuthorizationException('Rekapitulasi nilai hanya dapat diekspor oleh SuperAdmin (AC-17-1).');
        }

        $records = Penilaian::with(['penempatan.peserta.profil', 'pembimbing', 'details.rubrik'])
            ->latest()
            ->get();

        $this->auditService->catat(
            tipe: TipeAuditEnum::AksesData,
            tabel: 'penilaian',
            idData: null,
            nilaiLama: null,
            nilaiBaru: ['total_baris' => $records->count()],
            detail: [
                'aksi' => 'ekspor_nilai_superadmin_csv',
                'entitas' => 'penilaian',
                'jumlah_baris' => $records->count(),
            ]
        );

        $headers = [
            'ID Penilaian', 'Nama Peserta', 'NIM/NISN', 'Institusi',
            'Pembimbing Lapangan', 'Nilai Akhir', 'Predikat', 'Status', 'Catatan Evaluasi',
        ];

        return response()->streamDownload(function () use ($headers, $records) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF"); // UTF-8 BOM for Microsoft Excel compatibility
            fputcsv($handle, $headers);

            foreach ($records as $nilai) {
                fputcsv($handle, [
                    $this->sanitizeCell($nilai->id),
                    $this->sanitizeCell($nilai->penempatan?->peserta?->nama),
                    $this->sanitizeCell($nilai->penempatan?->peserta?->profil?->nim_nisn),
                    $this->sanitizeCell($nilai->penempatan?->peserta?->profil?->institusi),
                    $this->sanitizeCell($nilai->pembimbing?->nama),
                    $this->sanitizeCell($nilai->nilai_akhir),
                    $this->sanitizeCell($nilai->predikat),
                    $this->sanitizeCell($nilai->status?->value),
                    $this->sanitizeCell($nilai->catatan),
                ]);
            }

            fclose($handle);
        }, 'rekap-nilai-magkom-' . date('YmdHis') . '.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Helper umum unduh streamed CSV dengan UTF-8 BOM dan sanitasi formula injection (AC-17-2).
     *
     * @param array<int, string> $headers
     * @param array<int, array<int, mixed>> $rows
     */
    public function downloadCsvStream(string $filename, array $headers, array $rows): StreamedResponse
    {
        return response()->streamDownload(function () use ($headers, $rows) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF"); // UTF-8 BOM for Excel

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
