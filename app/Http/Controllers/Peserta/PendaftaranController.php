<?php

declare(strict_types=1);

namespace App\Http\Controllers\Peserta;

use App\Enums\StatusPendaftaranEnum;
use App\Enums\TipeAuditEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\PendaftaranRequest;
use App\Models\BulanMagang;
use App\Models\JenisDokumen;
use App\Models\Pendaftaran;
use App\Services\AuditService;
use App\Services\BulanStatusService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PendaftaranController extends Controller
{
    public function __construct(
        protected AuditService $auditService,
        protected BulanStatusService $bulanStatusService
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        $profil = $user->profil;

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
            \App\Enums\JenjangEnum::Smk => 'smk',
            \App\Enums\JenjangEnum::D3, \App\Enums\JenjangEnum::S1 => 'mahasiswa',
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
            return $this->bulanStatusService->status($bulan) === \App\Enums\StatusKetersediaanEnum::Tersedia;
        });

        $kuotaList = collect();
        if ($profil && $profil->jurusan_id) {
            $kuotaList = \App\Models\Kuota::with(['bulanMagang', 'jurusan'])
                ->where('jurusan_id', $profil->jurusan_id)
                ->where('aktif', true)
                ->whereHas('bulanMagang', function ($q) {
                    $q->where('aktif', true);
                })
                ->get()
                ->filter(function ($kuota) {
                    return $kuota->bulanMagang && $this->bulanStatusService->status($kuota->bulanMagang, $kuota) === \App\Enums\StatusKetersediaanEnum::Tersedia;
                })
                ->values();
        }

        return view('peserta.pendaftaran.index', [
            'pendaftaranList'  => $pendaftaranList,
            'pendaftaranAktif' => $pendaftaranAktif,
            'bulanAktifList'   => $bulanAktifList,
            'profil'           => $profil,
            'kuotaList'        => $kuotaList,
        ]);
    }

    public function store(PendaftaranRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();
        $validated['user_id'] = $user->id;
        $validated['status'] = StatusPendaftaranEnum::BerkasBelumLengkap;

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
        ]);

        $profil = $user->profil;
        $jenjang = $profil?->jenjang ?? \App\Enums\JenjangEnum::Smk;

        $jenisDokumenList = JenisDokumen::where('aktif', true)
            ->where('jenjang', $jenjang->value)
            ->orderBy('wajib', 'desc')
            ->get();

        $dokumenTerunggah = $pendaftaran->dokumen->keyBy('jenis_dokumen_id');

        return view('peserta.pendaftaran.detail', [
            'pendaftaran' => $pendaftaran,
            'jenisDokumenList' => $jenisDokumenList,
            'dokumenTerunggah' => $dokumenTerunggah,
            'profil' => $profil,
            'maksMb' => \App\Support\Pengaturan::int('berkas_maks_mb', 2),
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
        $jenjang = $profil?->jenjang ?? \App\Enums\JenjangEnum::Smk;

        // Validasi kelengkapan seluruh dokumen wajib
        $dokumenWajibIds = JenisDokumen::where('aktif', true)
            ->where('jenjang', $jenjang->value)
            ->where('wajib', true)
            ->pluck('id');

        $terunggahIds = $pendaftaran->dokumen()->pluck('jenis_dokumen_id');
        $belumLengkap = $dokumenWajibIds->diff($terunggahIds);

        if ($belumLengkap->isNotEmpty()) {
            return back()->with('error', 'Semua berkas persyaratan wajib harus diunggah sebelum pengajuan dapat dikirim.');
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
        app(\App\Services\RealtimeEventService::class)->publish(
            \App\Enums\RealtimeEventTypeEnum::PendaftaranBaru,
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
    public function unduhSuratDiterima(\App\Models\Penempatan $penempatan, \App\Services\SuratDiterimaPdfService $pdfService)
    {
        $this->authorize('view', $penempatan);

        return $pdfService->generate($penempatan);
    }

    /**
     * Menyajikan pratinjau inline stream Surat Keterangan Diterima PDF resmi (AC-16-1, F-16).
     */
    public function pratinjauSuratDiterima(\App\Models\Penempatan $penempatan, \App\Services\SuratDiterimaPdfService $pdfService)
    {
        $this->authorize('view', $penempatan);

        return $pdfService->stream($penempatan);
    }
}
