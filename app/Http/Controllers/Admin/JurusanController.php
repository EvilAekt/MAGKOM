<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\StatusPendaftaranEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\TipeAuditEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\JurusanRequest;
use App\Models\Jurusan;
use App\Models\Kuota;
use App\Models\Pendaftaran;
use App\Models\Penempatan;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JurusanController extends Controller
{
    public function __construct(
        protected AuditService $auditService
    ) {}

    /**
     * Menampilkan daftar master jurusan (AC-11-1).
     */
    public function index(Request $request): View
    {
        $query = Jurusan::query();

        if ($request->filled('q')) {
            $keyword = trim((string) $request->input('q'));
            $query->where('nama_jurusan', 'like', "%{$keyword}%");
        }

        if ($request->filled('kategori') && in_array($request->input('kategori'), ['smk', 'mahasiswa'], true)) {
            $query->where('kategori', $request->input('kategori'));
        }

        if ($request->has('status') && $request->input('status') !== '' && $request->input('status') !== null) {
            $query->where('aktif', filter_var($request->input('status'), FILTER_VALIDATE_BOOLEAN));
        }

        $jurusanList = $query->orderBy('kategori', 'asc')
            ->orderBy('nama_jurusan', 'asc')
            ->paginate(10)
            ->withQueryString();

        $totalJurusan = Jurusan::count();
        $jurusanMahasiswa = Jurusan::where('kategori', 'mahasiswa')->count();
        $jurusanSmk = Jurusan::where('kategori', 'smk')->count();
        $jurusanAktif = Jurusan::where('aktif', true)->count();

        return view('admin.jurusan.index', [
            'jurusanList' => $jurusanList,
            'totalJurusan' => $totalJurusan,
            'jurusanMahasiswa' => $jurusanMahasiswa,
            'jurusanSmk' => $jurusanSmk,
            'jurusanAktif' => $jurusanAktif,
        ]);
    }

    /**
     * Menyimpan jurusan baru (AC-11-1).
     */
    public function store(JurusanRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $jurusan = Jurusan::create([
            'nama_jurusan' => $validated['nama_jurusan'],
            'kategori' => $validated['kategori'],
            'aktif' => true,
        ]);

        $this->auditService->catat(
            tipe: TipeAuditEnum::Aksi,
            tabel: 'jurusan',
            idData: $jurusan->id,
            nilaiBaru: $jurusan->toArray(),
            detail: [
                'aksi' => 'Menambahkan master data jurusan baru',
                'admin_id' => $request->user()->id,
            ]
        );

        return redirect()->route('admin.jurusan.index')
            ->with('success', "Jurusan {$jurusan->nama_jurusan} berhasil ditambahkan.");
    }

    /**
     * Memperbarui data jurusan (AC-11-1).
     */
    public function update(Jurusan $jurusan, JurusanRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $nilaiLama = $jurusan->toArray();
        $jurusan->update($validated);

        $this->auditService->catat(
            tipe: TipeAuditEnum::Aksi,
            tabel: 'jurusan',
            idData: $jurusan->id,
            nilaiLama: $nilaiLama,
            nilaiBaru: $jurusan->toArray(),
            detail: [
                'aksi' => 'Memperbarui master data jurusan',
                'admin_id' => $request->user()->id,
            ]
        );

        return redirect()->route('admin.jurusan.index')
            ->with('success', "Data jurusan {$jurusan->nama_jurusan} berhasil diperbarui.");
    }

    /**
     * Mengaktifkan/menonaktifkan jurusan dengan validasi penegakan BR-12 (AC-11-2).
     */
    public function toggleAktif(Jurusan $jurusan, Request $request): RedirectResponse
    {
        abort_if(! $request->user()?->isAdmin(), 403, 'Aksi ini hanya dapat dilakukan oleh Administrator.');

        $targetAktif = ! $jurusan->aktif;

        // Jika hendak menonaktifkan, periksa keterkaitan aktif (BR-12)
        if (! $targetAktif) {
            // 1. Cek apakah ada kuota aktif yang terikat jurusan ini
            $hasActiveKuota = Kuota::where('jurusan_id', $jurusan->id)
                ->where('aktif', true)
                ->where('jumlah', '>', 0)
                ->exists();

            if ($hasActiveKuota) {
                return back()->with('error', 'Penonaktifan ditolak: Masih terdapat alokasi kuota aktif untuk jurusan ini.');
            }

            // 2. Cek apakah ada pendaftaran aktif pada jurusan ini (BR-12)
            $hasActivePendaftaran = Pendaftaran::where(function ($q) use ($jurusan) {
                $q->whereHas('kuota', fn ($qk) => $qk->where('jurusan_id', $jurusan->id))
                    ->orWhereHas('user.profil', fn ($qp) => $qp->where('jurusan_id', $jurusan->id));
            })
                ->whereIn('status', [
                    StatusPendaftaranEnum::Diajukan,
                    StatusPendaftaranEnum::WaitingList,
                    StatusPendaftaranEnum::PerluPerbaikan,
                    StatusPendaftaranEnum::BerkasBelumLengkap,
                    StatusPendaftaranEnum::Diterima,
                ])
                ->exists();

            if ($hasActivePendaftaran) {
                return back()->with('error', 'Penonaktifan ditolak: Masih terdapat pendaftaran aktif pada jurusan ini.');
            }

            // 3. Cek apakah ada penempatan magang aktif (baik kuota reguler maupun peserta jalur khusus)
            $hasActivePenempatan = Penempatan::where('status', StatusPenempatanEnum::Aktif)
                ->where(function ($q) use ($jurusan) {
                    $q->whereHas('kuota', fn ($qk) => $qk->where('jurusan_id', $jurusan->id))
                        ->orWhereHas('peserta.profil', fn ($qp) => $qp->where('jurusan_id', $jurusan->id));
                })
                ->exists();

            if ($hasActivePenempatan) {
                return back()->with('error', 'Penonaktifan ditolak: Masih terdapat peserta magang aktif pada jurusan ini.');
            }
        }

        $nilaiLama = $jurusan->toArray();
        $jurusan->update(['aktif' => $targetAktif]);

        $this->auditService->catat(
            tipe: TipeAuditEnum::Aksi,
            tabel: 'jurusan',
            idData: $jurusan->id,
            nilaiLama: $nilaiLama,
            nilaiBaru: $jurusan->toArray(),
            detail: [
                'aksi' => $targetAktif ? 'Mengaktifkan kembali jurusan' : 'Menonaktifkan jurusan',
                'admin_id' => $request->user()->id,
            ]
        );

        $statusLabel = $targetAktif ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('admin.jurusan.index')
            ->with('success', "Jurusan {$jurusan->nama_jurusan} berhasil {$statusLabel}.");
    }
}
