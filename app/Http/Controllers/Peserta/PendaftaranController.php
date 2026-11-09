<?php

declare(strict_types=1);

namespace App\Http\Controllers\Peserta;

use App\Enums\JenjangEnum;
use App\Enums\RealtimeEventTypeEnum;
use App\Enums\StatusKetersediaanEnum;
use App\Enums\StatusPendaftaranEnum;
use App\Enums\TipeAuditEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\PendaftaranRequest;
use App\Models\BulanMagang;
use App\Models\JenisDokumen;
use App\Models\Kuota;
use App\Models\Pendaftaran;
use App\Models\Penempatan;
use App\Services\AuditService;
use App\Services\BulanStatusService;
use App\Services\RealtimeEventService;
use App\Services\SuratDiterimaPdfService;
use App\Support\Pengaturan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PendaftaranController extends Controller
{
    public function __construct(
        protected AuditService $auditService,
        protected BulanStatusService $bulanStatusService
    ) {}

    public function index(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        $profil = $user->profil;
        $profil?->loadMissing(['jurusan.bidang']);

        // Proteksi Alur: Wajib Biodata 100% Lengkap sebelum Memilih Periode & Kuota Magang
        $hasPendaftaran = Pendaftaran::where('user_id', $user->id)->exists();
        if (! $hasPendaftaran && (! $profil || ! $profil->isLengkap())) {
            $persentase = $profil ? $profil->hitungKelengkapan()['persentase'] : 0;

            return redirect()->route('peserta.profil.show')
                ->with('warning', "Biodata dan profil peserta wajib 100% lengkap (saat ini {$persentase}%) sebelum Anda dapat memilih periode dan kuota magang.");
        }

        $pendaftaranList = Pendaftaran::with(['bulanMagang', 'kuota.jurusan', 'penempatan'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $pendaftaranAktif = $pendaftaranList->first(function ($p) {
            return in_array($p->status->value, [
                StatusPendaftaranEnum::BerkasBelumLengkap->value,
                StatusPendaftaranEnum::Diajukan->value,
                StatusPendaftaranEnum::PerluPerbaikan->value,
                StatusPendaftaranEnum::WaitingList->value,
                StatusPendaftaranEnum::Diterima->value,
            ], true);
        });

        $kategoriJurusan = match ($profil?->jenjang) {
            JenjangEnum::Smk => 'smk',
            JenjangEnum::D3, JenjangEnum::S1 => 'mahasiswa',
            default => null,
        };

        $bulanAktifList = BulanMagang::with(['kuota' => function ($q) use ($kategoriJurusan) {
            $q->where('aktif', true)->with('jurusan');
            if ($kategoriJurusan) {
                $q->whereHas('jurusan', function ($jq) use ($kategoriJurusan) {
                    $jq->where('kategori', $kategoriJurusan);
                });
            }
        }])
            ->where('aktif', true)
            ->orderBy('bulan_mulai', 'asc')
            ->get()
            ->filter(function ($bulan) {
                return $this->bulanStatusService->status($bulan) === StatusKetersediaanEnum::Tersedia;
            });

        $kuotaList = collect();
        if ($profil && $profil->jurusan_id) {
            $kuotaList = Kuota::with(['bulanMagang', 'jurusan.bidang'])
                ->where('jurusan_id', $profil->jurusan_id)
                ->where('aktif', true)
                ->whereHas('bulanMagang', function ($q) {
                    $q->where('aktif', true);
                })
                ->get()
                ->filter(function ($kuota) {
                    return $kuota->bulanMagang && $this->bulanStatusService->status($kuota->bulanMagang, $kuota) === StatusKetersediaanEnum::Tersedia;
                })
                ->values();
        }

        return view('peserta.pendaftaran.index', [
            'pendaftaranList' => $pendaftaranList,
            'pendaftaranAktif' => $pendaftaranAktif,
            'bulanAktifList' => $bulanAktifList,
            'profil' => $profil,
            'kuotaList' => $kuotaList,
        ]);
    }

    public function store(PendaftaranRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();
        $validated['user_id'] = $user->id;
        $validated['status'] = StatusPendaftaranEnum::BerkasBelumLengkap;

        if ($user->kelompokMagang) {
            $validated['tipe'] = 'kelompok';
            $validated['kelompok_magang_id'] = $user->kelompokMagang->id;
        } else {
            $validated['tipe'] = 'mandiri';
            $validated['kelompok_magang_id'] = null;
        }

        $pendaftaran = Pendaftaran::create($validated);

        $this->auditService->catat(
            tipe: TipeAuditEnum::Aksi,
            tabel: 'pendaftaran',
            idData: $pendaftaran->id,
            nilaiBaru: $pendaftaran->toArray(),
            detail: ['aksi' => 'Membuat draf pengajuan pendaftaran magang']
        );

        return redirect()->route('peserta.pendaftaran.detail', $pendaftaran)
            ->with('success', 'Pengajuan berhasil dibuat. Silakan lengkapi berkas persyaratan.');
    }

    public function detail(Request $request, Pendaftaran $pendaftaran): View
    {
        $this->authorize('view', $pendaftaran);

        $user = $request->user();
        $pendaftaran->loadMissing([
            'bulanMagang',
            'kuota.jurusan',
            'penempatan.pembimbing',
            'penempatan.bidang',
            'verifikator',
            'dokumen.jenisDokumen',
            'kelompokMagang.anggota',
        ]);

        $profil = $user->profil;
        $jenjang = $profil?->jenjang ?? JenjangEnum::Smk;

        $jenisDokumenList = JenisDokumen::where('aktif', true)
            ->where('jenjang', $jenjang->value)
            ->orderBy('wajib', 'desc')
            ->get();

        $semuaDokumen = $pendaftaran->dokumen;
        $dokumenTerunggah = $semuaDokumen->whereNull('kelompok_anggota_id')->keyBy('jenis_dokumen_id');
        $dokumenAnggotaGroup = $semuaDokumen->whereNotNull('kelompok_anggota_id')->groupBy('kelompok_anggota_id');

        return view('peserta.pendaftaran.detail', [
            'pendaftaran' => $pendaftaran,
            'jenisDokumenList' => $jenisDokumenList,
            'dokumenTerunggah' => $dokumenTerunggah,
            'dokumenAnggotaGroup' => $dokumenAnggotaGroup,
            'profil' => $profil,
            'maksMb' => Pengaturan::int('berkas_maks_mb', 2),
        ]);
    }

    /**
     * Mengajukan pendaftaran setelah seluruh dokumen wajib terunggah.
     */
    public function ajukan(Request $request, Pendaftaran $pendaftaran): RedirectResponse
    {
        $this->authorize('update', $pendaftaran);

        $user = $request->user();
        $profil = $user->profil;
        $profil?->loadMissing(['jurusan.bidang']);
        $jenjang = $profil?->jenjang ?? JenjangEnum::Smk;

        // Validasi kelengkapan seluruh dokumen wajib
        $dokumenWajibList = JenisDokumen::where('aktif', true)
            ->where('jenjang', $jenjang->value)
            ->where('wajib', true)
            ->get();

        $dokumenWajibIds = $dokumenWajibList->pluck('id');
        $isKelompok = ($pendaftaran->tipe === 'kelompok' || $pendaftaran->kelompok_magang_id !== null);

        if (! $isKelompok) {
            $terunggahIds = $pendaftaran->dokumen()->whereNull('kelompok_anggota_id')->pluck('jenis_dokumen_id');
            $belumLengkap = $dokumenWajibIds->diff($terunggahIds);

            if ($belumLengkap->isNotEmpty()) {
                return back()->with('error', 'Semua berkas persyaratan wajib harus diunggah sebelum pengajuan dapat dikirim.');
            }
        } else {
            // 1. Dokumen kolektif dan dokumen ketua
            $terunggahKetuaIds = $pendaftaran->dokumen()->whereNull('kelompok_anggota_id')->pluck('jenis_dokumen_id');
            if ($dokumenWajibIds->diff($terunggahKetuaIds)->isNotEmpty()) {
                return back()->with('error', 'Berkas persyaratan umum dan berkas Ketua Tim harus dilengkapi terlebih dahulu.');
            }

            // 2. Dokumen individu untuk seluruh anggota tim (CV & Transkrip / Kartu Pelajar)
            $pendaftaran->loadMissing('kelompokMagang.anggota');
            $jenisIndividuWajibIds = $dokumenWajibList
                ->whereIn('kode', ['cv', 'transkrip', 'kartu_pelajar'])
                ->pluck('id');

            foreach ($pendaftaran->kelompokMagang?->anggota ?? [] as $anggota) {
                $terunggahAnggotaIds = $pendaftaran->dokumen()
                    ->where('kelompok_anggota_id', $anggota->id)
                    ->pluck('jenis_dokumen_id');

                if ($jenisIndividuWajibIds->diff($terunggahAnggotaIds)->isNotEmpty()) {
                    return back()->with('error', "Berkas persyaratan untuk anggota kelompok '{$anggota->nama}' belum lengkap (CV & Transkrip Nilai wajib diunggah).");
                }
            }
        }

        $nilaiLama = ['status' => $pendaftaran->status->value];

        $pendaftaran->update([
            'status' => StatusPendaftaranEnum::Diajukan,
            'diajukan_at' => now(),
        ]);

        $this->auditService->catat(
            tipe: TipeAuditEnum::Aksi,
            tabel: 'pendaftaran',
            idData: $pendaftaran->id,
            nilaiLama: $nilaiLama,
            nilaiBaru: ['status' => StatusPendaftaranEnum::Diajukan->value],
            detail: ['aksi' => 'Peserta mengajukan berkas lengkap pendaftaran']
        );

        // Notifikasi Real-time SSE ke Admin (TASK-SSE-05)
        app(RealtimeEventService::class)->publish(
            RealtimeEventTypeEnum::PendaftaranBaru,
            [
                'title' => 'Pendaftaran Baru Masuk',
                'message' => "Berkas dari {$user->nama} siap diverifikasi.",
                'nomor_pendaftaran' => $pendaftaran->nomor_pendaftaran,
                'action_url' => route('admin.pendaftaran.show', $pendaftaran->id),
                'badge_target' => '#badge-antrean-menunggu',
            ],
            null,
            'admin'
        );

        return redirect()->route('peserta.pendaftaran.detail', $pendaftaran)
            ->with('success', 'Pendaftaran Anda berhasil diajukan dan sedang menunggu verifikasi berkas oleh admin.');

    }

    /**
     * Membatalkan pengajuan pendaftaran mandiri oleh peserta (AC-03-9).
     */
    public function batalkan(Request $request, Pendaftaran $pendaftaran): RedirectResponse
    {
        $this->authorize('cancel', $pendaftaran);

        $nilaiLama = ['status' => $pendaftaran->status->value];

        $pendaftaran->update([
            'status' => StatusPendaftaranEnum::Dibatalkan,
        ]);

        $this->auditService->catat(
            tipe: TipeAuditEnum::Aksi,
            tabel: 'pendaftaran',
            idData: $pendaftaran->id,
            nilaiLama: $nilaiLama,
            nilaiBaru: ['status' => StatusPendaftaranEnum::Dibatalkan->value],
            detail: ['aksi' => 'Peserta membatalkan pengajuan pendaftaran magang']
        );

        return redirect()->route('peserta.pendaftaran.index')
            ->with('success', 'Pengajuan pendaftaran magang berhasil dibatalkan.');
    }

    /**
     * Mengunduh Surat Keterangan Diterima PDF resmi (AC-16-1, F-16).
     */
    public function unduhSuratDiterima(Penempatan $penempatan, SuratDiterimaPdfService $pdfService)
    {
        $this->authorize('view', $penempatan);

        $filePath = $penempatan->file_surat_diterima ?: $penempatan->pendaftaran?->file_surat_diterima;
        if ($filePath && Storage::disk('local')->exists($filePath)) {
            $pesertaNama = Str::slug($penempatan->peserta?->nama ?? 'peserta');

            return Storage::disk('local')->download($filePath, "surat-keterangan-diterima-{$pesertaNama}.pdf");
        }

        return $pdfService->generate($penempatan);
    }

    /**
     * Menyajikan pratinjau inline stream Surat Keterangan Diterima PDF resmi (AC-16-1, F-16).
     */
    public function pratinjauSuratDiterima(Penempatan $penempatan, SuratDiterimaPdfService $pdfService)
    {
        $this->authorize('view', $penempatan);

        $filePath = $penempatan->file_surat_diterima ?: $penempatan->pendaftaran?->file_surat_diterima;
        if ($filePath && Storage::disk('local')->exists($filePath)) {
            return response()->file(Storage::disk('local')->path($filePath), [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="surat-keterangan-diterima.pdf"',
            ]);
        }

        return $pdfService->stream($penempatan);
    }

    /**
     * Mengunduh Surat Keterangan Diterima dari pendaftaran langsung.
     */
    public function unduhSuratDiterimaPendaftaran(Pendaftaran $pendaftaran, SuratDiterimaPdfService $pdfService)
    {
        $this->authorize('view', $pendaftaran);

        if ($pendaftaran->file_surat_diterima && Storage::disk('local')->exists($pendaftaran->file_surat_diterima)) {
            $pesertaNama = Str::slug($pendaftaran->user?->nama ?? 'peserta');

            return Storage::disk('local')->download($pendaftaran->file_surat_diterima, "surat-keterangan-diterima-{$pesertaNama}.pdf");
        }

        if ($pendaftaran->penempatan) {
            return $pdfService->generate($pendaftaran->penempatan);
        }

        abort(404, 'Berkas surat keterangan diterima belum tersedia.');
    }

    /**
     * Menyajikan pratinjau inline stream Surat Keterangan Diterima dari pendaftaran langsung.
     */
    public function pratinjauSuratDiterimaPendaftaran(Pendaftaran $pendaftaran, SuratDiterimaPdfService $pdfService)
    {
        $this->authorize('view', $pendaftaran);

        if ($pendaftaran->file_surat_diterima && Storage::disk('local')->exists($pendaftaran->file_surat_diterima)) {
            return response()->file(Storage::disk('local')->path($pendaftaran->file_surat_diterima), [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="surat-keterangan-diterima.pdf"',
            ]);
        }

        if ($pendaftaran->penempatan) {
            return $pdfService->stream($pendaftaran->penempatan);
        }

        abort(404, 'Berkas surat keterangan diterima belum tersedia.');
    }
}
