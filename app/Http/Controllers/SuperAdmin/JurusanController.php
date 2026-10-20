<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Enums\StatusPendaftaranEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\TipeAuditEnum;
use App\Http\Controllers\Controller;
use App\Models\Jurusan;
use App\Models\Kuota;
use App\Models\Pendaftaran;
use App\Models\Penempatan;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class JurusanController extends Controller
{
    public function __construct(
        protected AuditService $auditService
    ) {}

    /**
     * Menampilkan daftar master jurusan (AC-11-1).
     */
    public function index(): View
    {
        $jurusanList = Jurusan::orderBy('kategori', 'asc')
            ->orderBy('nama_jurusan', 'asc')
            ->paginate(20);

        return view('superadmin.jurusan.index', [
            'jurusanList' => $jurusanList,
        ]);
    }

    /**
     * Menyimpan jurusan baru (AC-11-1).
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_jurusan' => ['required', 'string', 'max:255', 'unique:jurusan,nama_jurusan'],
            'kategori' => ['required', Rule::in(['smk', 'mahasiswa'])],
        ]);

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
                'superadmin_id' => $request->user()->id,
            ]
        );

        return redirect()->route('superadmin.jurusan.index')
            ->with('success', "Jurusan {$jurusan->nama_jurusan} berhasil ditambahkan.");
    }

    /**
     * Memperbarui data jurusan (AC-11-1).
     */
    public function update(Jurusan $jurusan, Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_jurusan' => ['required', 'string', 'max:255', Rule::unique('jurusan', 'nama_jurusan')->ignore($jurusan->id)],
            'kategori' => ['required', Rule::in(['smk', 'mahasiswa'])],
        ]);

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
                'superadmin_id' => $request->user()->id,
            ]
        );

        return redirect()->route('superadmin.jurusan.index')
            ->with('success', "Data jurusan {$jurusan->nama_jurusan} berhasil diperbarui.");
    }

    /**
     * Mengaktifkan/menonaktifkan jurusan dengan validasi penegakan BR-12 (AC-11-2).
     */
    public function toggleAktif(Jurusan $jurusan, Request $request): RedirectResponse
    {
        $targetAktif = ! $jurusan->aktif;

        // Jika hendak menonaktifkan, periksa keterkaitan aktif (BR-12)
        if (! $targetAktif) {
            // 1. Cek apakah ada kuota aktif yang terikat jurusan ini
            $hasActiveKuota = Kuota::where('jurusan_id', $jurusan->id)
                ->where('aktif', true)
                ->where('jumlah', '>', 0)
                ->exists();

            if ($hasActiveKuota) {
                return back()->with('error', 'Penonaktifan ditolak (BR-12): Masih terdapat alokasi kuota aktif untuk jurusan ini.');
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
                return back()->with('error', 'Penonaktifan ditolak (BR-12): Masih terdapat pendaftaran aktif pada jurusan ini.');
            }

            // 3. Cek apakah ada penempatan magang aktif
            $hasActivePenempatan = Penempatan::whereHas('kuota', function ($q) use ($jurusan) {
                $q->where('jurusan_id', $jurusan->id);
            })->where('status', StatusPenempatanEnum::Aktif)->exists();

            if ($hasActivePenempatan) {
                return back()->with('error', 'Penonaktifan ditolak (BR-12): Masih terdapat peserta magang aktif pada jurusan ini.');
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
                'superadmin_id' => $request->user()->id,
            ]
        );

        $statusLabel = $targetAktif ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('superadmin.jurusan.index')
            ->with('success', "Jurusan {$jurusan->nama_jurusan} berhasil {$statusLabel}.");
    }
}
