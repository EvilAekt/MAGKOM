<?php

declare(strict_types=1);

namespace App\Queries\Admin\Penempatan;

use App\Models\BulanMagang;
use App\Models\Penempatan;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class PenempatanIndexQuery
{
    /**
     * Membangun query dasar penempatan dengan filter dinamis.
     */
    public function buildQuery(Request $request): Builder
    {
        $query = Penempatan::with(['peserta.profil.jurusan', 'pembimbing.bidang', 'bidang', 'kuota.jurusan', 'slotKhusus'])
            ->orderBy('tgl_mulai', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('pembimbing_id')) {
            $query->where('pembimbing_id', $request->integer('pembimbing_id'));
        }

        if ($request->filled('bidang_id')) {
            $query->where('bidang_id', $request->integer('bidang_id'));
        }

        if ($request->filled('bulan_magang_id')) {
            $bulanId = $request->integer('bulan_magang_id');
            $bulanObj = BulanMagang::find($bulanId);
            $query->where(function ($q) use ($bulanId, $bulanObj) {
                $q->whereHas('pendaftaran', fn ($qp) => $qp->where('bulan_magang_id', $bulanId))
                    ->orWhereHas('kuota', fn ($qk) => $qk->where('bulan_magang_id', $bulanId));

                if ($bulanObj && $bulanObj->bulan_mulai) {
                    $q->orWhere(function ($qk) use ($bulanObj) {
                        $qk->whereYear('tgl_mulai', $bulanObj->bulan_mulai->year)
                            ->whereMonth('tgl_mulai', $bulanObj->bulan_mulai->month);
                    });
                }
            });
        }

        if ($request->filled('q')) {
            $rawSearch = trim((string) $request->input('q'));
            $cleanId = ltrim(str_ireplace(['#', 'REG-'], '', $rawSearch), '0');
            $escapedSearch = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $rawSearch);
            $search = '%'.$escapedSearch.'%';

            $query->where(function ($q) use ($search, $cleanId) {
                if ($cleanId !== '' && is_numeric($cleanId)) {
                    $q->orWhere('id', (int) $cleanId)
                        ->orWhere('pendaftaran_id', (int) $cleanId);
                }

                $q->orWhereHas('peserta', function ($qu) use ($search) {
                    $qu->where('nama', 'like', $search)
                        ->orWhere('email', 'like', $search)
                        ->orWhereHas('profil', function ($qp) use ($search) {
                            $qp->where('institusi', 'like', $search)
                                ->orWhere('nim_nisn', 'like', $search);
                        });
                })->orWhereHas('pembimbing', function ($qpb) use ($search) {
                    $qpb->where('nama', 'like', $search)
                        ->orWhere('email', 'like', $search);
                })->orWhereHas('pendaftaran.kelompokMagang', function ($qk) use ($search) {
                    $qk->where('nama_kelompok', 'like', $search);
                });
            });
        }

        return $query;
    }

    /**
     * Mengambil daftar penempatan berpaginasi.
     */
    public function getPaginated(Request $request, int $defaultPerPage = 5): LengthAwarePaginator
    {
        $perPage = (int) $request->input('per_page', $defaultPerPage);
        if (! in_array($perPage, [5, 10, 25, 50], true)) {
            $perPage = $defaultPerPage;
        }

        return $this->buildQuery($request)->paginate($perPage)->withQueryString();
    }

    /**
     * Menghitung statistik ringkasan penempatan peserta dalam 1 query agregasi terkonsolidasi.
     *
     * @return array{total: int, aktif: int, selesai: int, khusus: int, belum_ditempatkan: int}
     */
    public function getStatistics(int $totalBelumDitempatkan): array
    {
        $stats = Penempatan::selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN status = 'aktif' THEN 1 ELSE 0 END) as aktif,
            SUM(CASE WHEN status = 'selesai' THEN 1 ELSE 0 END) as selesai,
            SUM(CASE WHEN slot_khusus_id IS NOT NULL THEN 1 ELSE 0 END) as khusus
        ")->first();

        return [
            'total' => (int) ($stats->total ?? 0),
            'aktif' => (int) ($stats->aktif ?? 0),
            'selesai' => (int) ($stats->selesai ?? 0),
            'khusus' => (int) ($stats->khusus ?? 0),
            'belum_ditempatkan' => $totalBelumDitempatkan,
        ];
    }
}
