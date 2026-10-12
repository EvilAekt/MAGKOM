<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\RoleEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\TipeAuditEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\PenempatanManualRequest;
use App\Models\Bidang;
use App\Models\Penempatan;
use App\Models\SlotKhusus;
use App\Models\User;
use App\Services\AuditService;
use App\Services\PlacementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PenempatanController extends Controller
{
    public function __construct(
        protected PlacementService $placementService,
        protected AuditService $auditService
    ) {}

    public function index(Request $request): View
    {
        $query = Penempatan::with(['peserta.profil', 'pembimbing', 'bidang', 'kuota.jurusan', 'slotKhusus'])
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

        if ($request->filled('q')) {
            $search = '%' . trim((string) $request->input('q')) . '%';
            $query->whereHas('peserta', function ($qu) use ($search) {
                $qu->where('nama', 'like', $search)
                    ->orWhere('email', 'like', $search)
                    ->orWhereHas('profil', fn ($qp) => $qp->where('institusi', 'like', $search));
            });
        }

        $penempatanList = $query->paginate(15)->withQueryString();
        $pembimbingList = User::where('role', RoleEnum::Pembimbing)
            ->where('status_aktif', true)
            ->withCount(['penempatanBimbingan as mentee_aktif_count' => function ($q) {
                $q->where('status', StatusPenempatanEnum::Aktif);
            }])
            ->orderBy('nama')
            ->get();

        $slotKhususList = SlotKhusus::where('aktif', true)->orderBy('bulan_mulai')->get();
        $bidangList = Bidang::where('aktif', true)->orderBy('nama_bidang')->get();
        $jurusanList = \App\Models\Jurusan::where('aktif', true)->orderBy('nama_jurusan')->get();
        $kuotaList = \App\Models\Kuota::with(['bulanMagang', 'jurusan'])->where('aktif', true)->get();
        $pesertaList = User::where('role', RoleEnum::Peserta)
            ->where('status_aktif', true)
            ->whereDoesntHave('penempatanPeserta', function ($q) {
                $q->where('status', StatusPenempatanEnum::Aktif);
            })
            ->orderBy('nama')
            ->get();

        $penempatanStats = [
            'total' => Penempatan::count(),
            'aktif' => Penempatan::where('status', StatusPenempatanEnum::Aktif)->count(),
            'selesai' => Penempatan::where('status', StatusPenempatanEnum::Selesai)->count(),
            'khusus' => Penempatan::whereNotNull('slot_khusus_id')->count(),
        ];

        return view('admin.penempatan.index', [
            'penempatanList' => $penempatanList,
            'statusEnum' => StatusPenempatanEnum::cases(),
            'pembimbingList' => $pembimbingList,
            'slotKhususList' => $slotKhususList,
            'bidangList' => $bidangList,
            'jurusanList' => $jurusanList,
            'kuotaList' => $kuotaList,
            'pesertaList' => $pesertaList,
            'penempatanStats' => $penempatanStats,
        ]);
    }

    /**
     * Menampilkan detail lengkap penempatan peserta individual (A9).
     */
    public function show(Penempatan $penempatan): View
    {
        $penempatan->load([
            'peserta.profil.jurusan',
            'pembimbing',
            'bidang',
            'slotKhusus',
            'kuota.jurusan',
            'tugas.progresLogs',
            'logbooks',
            'penilaian.details.rubrik',
            'pendaftaran.dokumen',
        ]);

        $this->auditService->catat(
            tipe: TipeAuditEnum::AksesData,
            tabel: 'penempatan',
            idData: $penempatan->id,
            nilaiLama: null,
            nilaiBaru: null,
            detail: [
                'aksi' => 'Melihat detail individual penempatan peserta (A9)',
                'peserta_id' => $penempatan->peserta_id,
            ],
            user: request()->user()?->id
        );

        $pembimbingList = User::where('role', RoleEnum::Pembimbing)
            ->where('status_aktif', true)
            ->withCount(['penempatanBimbingan as mentee_aktif_count' => function ($q) {
                $q->where('status', StatusPenempatanEnum::Aktif);
            }])
            ->orderBy('nama')
            ->get();

        $tugasCount = $penempatan->tugas->count();
        $tugasSelesaiCount = $penempatan->tugas->filter(function ($t) {
            return $t->status === \App\Enums\StatusTugasEnum::Selesai;
        })->count();

        $logbookCount = $penempatan->logbooks->count();
        $nilaiAkhir = $penempatan->penilaian?->nilai_akhir;

        return view('admin.penempatan.show', [
            'penempatan' => $penempatan,
            'pembimbingList' => $pembimbingList,
            'tugasCount' => $tugasCount,
            'tugasSelesaiCount' => $tugasSelesaiCount,
            'logbookCount' => $logbookCount,
            'nilaiAkhir' => $nilaiAkhir,
        ]);
    }

    /**
     * Alur penempatan manual peserta (SuperAdmin & Admin) (F-06, F-07, AC-07-3).
     */
    public function storeManual(PenempatanManualRequest $request): RedirectResponse
    {
        $admin = $request->user();
        $mode = $request->input('mode', 'terdaftar');
        $jalur = $request->input('jalur', 'khusus');
        $pembimbing = User::findOrFail($request->integer('pembimbing_id'));
        $bidang = $request->filled('bidang_id') ? Bidang::find($request->integer('bidang_id')) : null;

        try {
            if ($mode === 'baru') {
                $penempatan = $this->placementService->tempatkanPesertaBaruManual(
                    userData: [
                        'nama' => (string) $request->input('nama'),
                        'email' => (string) $request->input('email'),
                    ],
                    profilData: [
                        'nim_nisn' => (string) $request->input('nim_nisn'),
                        'institusi' => (string) $request->input('institusi'),
                        'jurusan_id' => $request->filled('jurusan_id') ? $request->integer('jurusan_id') : null,
                        'jenjang' => (string) $request->input('jenjang'),
                        'no_hp' => $request->input('no_hp'),
                    ],
                    pembimbing: $pembimbing,
                    bidang: $bidang,
                    bulanMulai: (string) $request->input('bulan_mulai'),
                    durasiBulan: $request->integer('durasi_bulan'),
                    jalur: (string) $jalur,
                    slotKhususId: $request->filled('slot_khusus_id') ? $request->integer('slot_khusus_id') : null,
                    kuotaId: $request->filled('kuota_id') ? $request->integer('kuota_id') : null,
                    admin: $admin
                );
            } else {
                $peserta = User::findOrFail($request->integer('peserta_id'));
                $slotKhusus = SlotKhusus::findOrFail($request->integer('slot_khusus_id'));

                $penempatan = $this->placementService->tempatkanManual(
                    peserta: $peserta,
                    slotKhusus: $slotKhusus,
                    pembimbing: $pembimbing,
                    bidang: $bidang,
                    bulanMulai: (string) $request->input('bulan_mulai'),
                    durasiBulan: $request->integer('durasi_bulan'),
                    admin: $admin
                );
            }

            return redirect()->route('admin.penempatan.index')
                ->with('success', 'Penempatan peserta berhasil diproses secara manual.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memproses penempatan manual: ' . $e->getMessage());
        }
    }

    /**
     * Mengunduh dokumen resmi Surat Keterangan Diterima Magang PDF (AC-16-1, F-16).
     */
    public function unduhSuratDiterima(Penempatan $penempatan, \App\Services\SuratDiterimaPdfService $pdfService)
    {
        $this->authorize('view', $penempatan);

        return $pdfService->generate($penempatan);
    }

    /**
     * Menyajikan pratinjau dokumen resmi Surat Keterangan Diterima Magang PDF secara inline stream (AC-16-1, F-16).
     */
    public function pratinjauSuratDiterima(Penempatan $penempatan, \App\Services\SuratDiterimaPdfService $pdfService)
    {
        $this->authorize('view', $penempatan);

        return $pdfService->stream($penempatan);
    }

    /**
     * Memindahkan pembimbing peserta magang (AC-07-4).
     */
    public function pindahPembimbing(Penempatan $penempatan, Request $request): RedirectResponse
    {
        $request->validate([
            'pembimbing_id' => ['required', 'exists:users,id'],
        ]);

        $pembimbingBaru = User::findOrFail($request->integer('pembimbing_id'));

        try {
            $this->placementService->pindahPembimbing(
                penempatan: $penempatan,
                pembimbingBaru: $pembimbingBaru,
                admin: $request->user()
            );

            return redirect()->route('admin.penempatan.index')
                ->with('success', "Pembimbing berhasil dialihkan ke {$pembimbingBaru->nama}.");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Memindahkan pembimbing untuk banyak penempatan peserta sekaligus (Bulk Reassign) (AC-07-4, BR-13, A-12).
     */
    public function bulkReassignPembimbing(Request $request): RedirectResponse
    {
        $request->validate([
            'penempatan_ids' => ['required', 'array', 'min:1'],
            'penempatan_ids.*' => ['required', 'integer', 'exists:penempatan,id'],
            'pembimbing_id' => ['required', 'integer', 'exists:users,id'],
        ], [
            'penempatan_ids.required' => 'Pilih minimal satu penempatan peserta untuk dialihkan.',
            'pembimbing_id.required' => 'Pilih pembimbing baru tujuan.',
        ]);

        $admin = $request->user();
        $pembimbingBaru = User::where('id', $request->integer('pembimbing_id'))
            ->where('role', RoleEnum::Pembimbing)
            ->where('status_aktif', true)
            ->firstOrFail();

        $ids = $request->input('penempatan_ids');

        // Validasi kuota kapasitas maksimal pembimbing (BR-13)
        $maxBebanSetting = \App\Models\Pengaturan::where('kunci', 'maks_peserta_aktif_per_pembimbing')->value('nilai');
        if ($maxBebanSetting !== null && $maxBebanSetting !== '') {
            $maxBeban = (int) $maxBebanSetting;
            $currentAktif = Penempatan::where('pembimbing_id', $pembimbingBaru->id)
                ->where('status', StatusPenempatanEnum::Aktif)
                ->count();

            $penambahanAktif = Penempatan::whereIn('id', $ids)
                ->where('status', StatusPenempatanEnum::Aktif)
                ->where('pembimbing_id', '!=', $pembimbingBaru->id)
                ->count();

            if (($currentAktif + $penambahanAktif) > $maxBeban) {
                return back()->with('error', "Kapasitas pembimbing {$pembimbingBaru->nama} terlampaui (Batas maksimal: {$maxBeban} mentee aktif, saat ini: {$currentAktif}, penambahan: {$penambahanAktif}).");
            }
        }

        $count = \Illuminate\Support\Facades\DB::transaction(function () use ($ids, $pembimbingBaru, $admin) {
            $penempatans = Penempatan::whereIn('id', $ids)->get();
            $updated = 0;

            foreach ($penempatans as $p) {
                if ($p->pembimbing_id === $pembimbingBaru->id) {
                    continue;
                }

                $pembimbingLamaId = $p->pembimbing_id;
                $p->update(['pembimbing_id' => $pembimbingBaru->id]);

                $this->auditService->catat(
                    tipe: TipeAuditEnum::Aksi,
                    tabel: 'penempatan',
                    idData: $p->id,
                    nilaiLama: ['pembimbing_id' => $pembimbingLamaId],
                    nilaiBaru: ['pembimbing_id' => $pembimbingBaru->id],
                    detail: [
                        'aksi' => 'Pemindahan pembimbing massal (bulk reassign)',
                        'admin_id' => $admin->id,
                        'pembimbing_baru_id' => $pembimbingBaru->id,
                    ]
                );

                $updated++;
            }

            return $updated;
        });

        return redirect()->route('admin.penempatan.index')
            ->with('success', "Sebanyak {$count} penempatan peserta berhasil dialihkan ke {$pembimbingBaru->nama}.");
    }

    /**
     * Membatalkan penerimaan atau mencatat pengunduran diri peserta magang (AC-07-5, BR-07).
     */
    public function batalkan(Penempatan $penempatan, Request $request): RedirectResponse
    {
        $request->validate([
            'alasan' => ['required', 'string', 'min:10', 'max:1000'],
        ], [
            'alasan.min' => 'Alasan pembatalan/pengunduran diri minimal 10 karakter.',
        ]);

        try {
            $this->placementService->batalkanPenempatan(
                penempatan: $penempatan,
                admin: $request->user(),
                alasan: $request->input('alasan')
            );

            return redirect()->route('admin.penempatan.index')
                ->with('success', 'Status penerimaan/penempatan peserta berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Ekspor rekapitulasi data surat keterangan diterima magang ke format CSV (F-16).
     */
    public function exportRekapSuratDiterima(Request $request): StreamedResponse
    {
        $query = Penempatan::with(['peserta.profil', 'pembimbing', 'bidang', 'kuota.jurusan', 'slotKhusus'])
            ->orderBy('tgl_mulai', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->filled('bidang_id')) {
            $query->where('bidang_id', $request->integer('bidang_id'));
        }

        $records = $query->get();

        $this->auditService->catat(
            tipe: TipeAuditEnum::AksesData,
            tabel: 'penempatan',
            idData: null,
            nilaiLama: null,
            nilaiBaru: null,
            detail: [
                'aksi' => 'Ekspor rekapitulasi surat diterima magang (F-16)',
                'jumlah_baris' => $records->count(),
            ],
            user: $request->user()->id
        );

        $sanitize = function (mixed $val): string {
            if ($val === null) {
                return '';
            }
            $s = (string) $val;
            if ($s !== '' && in_array($s[0], ['=', '+', '-', '@'], true)) {
                return "'" . $s;
            }
            return $s;
        };

        $fileName = 'rekap-surat-diterima-' . date('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($records, $sanitize) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, [
                'No Penempatan',
                'Nama Peserta',
                'NIM/NISN',
                'Institusi/Kampus',
                'Jurusan',
                'Bidang Penempatan',
                'Pembimbing Lapangan',
                'Jalur',
                'Tanggal Mulai',
                'Tanggal Selesai',
                'Status',
                'No Surat Diterima',
            ]);

            foreach ($records as $item) {
                fputcsv($handle, [
                    $sanitize($item->id),
                    $sanitize($item->peserta?->nama ?? '-'),
                    $sanitize($item->peserta?->profil?->nim_nisn ?? '-'),
                    $sanitize($item->peserta?->profil?->institusi ?? '-'),
                    $sanitize($item->kuota?->jurusan?->nama_jurusan ?? ($item->peserta?->profil?->jurusan?->nama_jurusan ?? '-')),
                    $sanitize($item->bidang?->nama_bidang ?? '-'),
                    $sanitize($item->pembimbing?->nama ?? '-'),
                    $sanitize(strtoupper($item->jalur instanceof \App\Enums\JalurEnum ? $item->jalur->value : (string) ($item->jalur ?? 'REGULER'))),
                    $sanitize($item->tgl_mulai?->format('d/m/Y') ?? '-'),
                    $sanitize($item->tgl_selesai?->format('d/m/Y') ?? '-'),
                    $sanitize(ucfirst($item->status?->value ?? '-')),
                    $sanitize($item->no_surat ?? '500.12/' . str_pad((string) $item->id, 4, '0', STR_PAD_LEFT) . '/DISKOMINFO/' . date('Y')),
                ]);
            }

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}


