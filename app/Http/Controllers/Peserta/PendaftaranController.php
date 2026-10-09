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

        $profil = $user->profil;
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

        return view('peserta.pendaftaran.index', [
            'pendaftaranList' => $pendaftaranList,
            'pendaftaranAktif' => $pendaftaranAktif,
            'bulanAktifList' => $bulanAktifList,
            'profil' => $profil,
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

        // Tentukan apakah masuk antrean reguler atau Waiting List (AC-01-3 / BR-05)
        $kuota = $pendaftaran->kuota;
        $statusTujuan = ($kuota && $kuota->terisi >= $kuota->jumlah)
            ? StatusPendaftaranEnum::WaitingList
            : StatusPendaftaranEnum::Diajukan;

        $nilaiLama = ['status' => $pendaftaran->status->value];

        $pendaftaran->update([
            'status' => $statusTujuan,
            'diajukan_at' => now(),
        ]);

        $this->auditService->catat(
            tipe: TipeAuditEnum::Aksi,
            tabel: 'pendaftaran',
            idData: $pendaftaran->id,
            nilaiLama: $nilaiLama,
            nilaiBaru: ['status' => $statusTujuan->value],
            detail: ['aksi' => 'Peserta mengajukan berkas lengkap pendaftaran']
        );

        $pesan = $statusTujuan === StatusPendaftaranEnum::WaitingList
            ? 'Pendaftaran Anda berhasil diajukan dan masuk ke antrean Waiting List karena kuota reguler penuh.'
            : 'Pendaftaran Anda berhasil diajukan dan sedang menunggu verifikasi berkas oleh admin.';

        return redirect()->route('peserta.pendaftaran.detail', $pendaftaran)->with('success', $pesan);
    }

    /**
     * Mengunduh Surat Keterangan Diterima PDF resmi (AC-16-1, F-16).
     */
    public function unduhSuratDiterima(\App\Models\Penempatan $penempatan, \App\Services\SuratDiterimaPdfService $pdfService)
    {
        $this->authorize('view', $penempatan);

        return $pdfService->generate($penempatan);
    }
}
