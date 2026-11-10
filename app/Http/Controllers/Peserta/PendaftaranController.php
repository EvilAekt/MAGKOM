<?php

declare(strict_types=1);

namespace App\Http\Controllers\Peserta;

use App\Actions\Peserta\Pendaftaran\AjukanPendaftaranAction;
use App\Actions\Peserta\Pendaftaran\BatalkanPendaftaranAction;
use App\Actions\Peserta\Pendaftaran\CreatePendaftaranAction;
use App\Enums\JenjangEnum;
use App\Exceptions\Domain\PendaftaranAktifGandaException;
use App\Http\Controllers\Controller;
use App\Http\Requests\PendaftaranRequest;
use App\Models\JenisDokumen;
use App\Models\Pendaftaran;
use App\Models\Penempatan;
use App\Queries\Peserta\Pendaftaran\PendaftaranIndexQuery;
use App\Services\SuratDiterimaPdfService;
use App\Support\Pengaturan;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class PendaftaranController extends Controller
{
    public function __construct(
        protected PendaftaranIndexQuery $indexQuery,
        protected CreatePendaftaranAction $createAction,
        protected AjukanPendaftaranAction $ajukanAction,
        protected BatalkanPendaftaranAction $batalkanAction
    ) {}

    /**
     * Menampilkan daftar riwayat dan formulir pemilihan periode/kuota magang peserta.
     */
    public function index(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        $indexData = $this->indexQuery->getIndexData($user);

        if ($indexData['profilIncomplete']) {
            return redirect()->route('peserta.profil.show')
                ->with('warning', "Biodata dan profil peserta wajib 100% lengkap (saat ini {$indexData['persentaseProfil']}%) sebelum Anda dapat memilih periode dan kuota magang.");
        }

        return view('peserta.pendaftaran.index', [
            'pendaftaranList' => $indexData['pendaftaranList'],
            'pendaftaranAktif' => $indexData['pendaftaranAktif'],
            'bulanAktifList' => $indexData['bulanAktifList'],
            'profil' => $indexData['profil'],
            'kuotaList' => $indexData['kuotaList'],
        ]);
    }

    /**
     * Membuat draf pendaftaran magang baru dengan perlindungan pessimistic lock (BR-17).
     */
    public function store(PendaftaranRequest $request): RedirectResponse
    {
        $user = $request->user();

        try {
            $pendaftaran = $this->createAction->execute($user, $request->validated());

            return redirect()->route('peserta.pendaftaran.detail', $pendaftaran)
                ->with('success', 'Pengajuan berhasil dibuat. Silakan lengkapi berkas persyaratan.');
        } catch (PendaftaranAktifGandaException $e) {
            return redirect()->route('peserta.pendaftaran.index')
                ->withErrors(['pendaftaran' => $e->getMessage()]);
        }
    }

    /**
     * Menampilkan detail pendaftaran dan checklist unggah dokumen persyaratan.
     */
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

        try {
            $this->ajukanAction->execute($request->user(), $pendaftaran);

            return redirect()->route('peserta.pendaftaran.detail', $pendaftaran)
                ->with('success', 'Pendaftaran Anda berhasil diajukan dan sedang menunggu verifikasi berkas oleh admin.');
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Membatalkan pengajuan pendaftaran mandiri oleh peserta (AC-03-9).
     */
    public function batalkan(Request $request, Pendaftaran $pendaftaran): RedirectResponse
    {
        $this->authorize('cancel', $pendaftaran);

        try {
            $this->batalkanAction->execute($request->user(), $pendaftaran);

            return redirect()->route('peserta.pendaftaran.index')
                ->with('success', 'Pengajuan pendaftaran magang berhasil dibatalkan.');
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Mengunduh Surat Keterangan Diterima PDF resmi (AC-16-1, F-16).
     */
    public function unduhSuratDiterima(Penempatan $penempatan, SuratDiterimaPdfService $pdfService): Response|BinaryFileResponse
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
    public function pratinjauSuratDiterima(Penempatan $penempatan, SuratDiterimaPdfService $pdfService): Response|BinaryFileResponse
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
    public function unduhSuratDiterimaPendaftaran(Pendaftaran $pendaftaran, SuratDiterimaPdfService $pdfService): Response|BinaryFileResponse
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
    public function pratinjauSuratDiterimaPendaftaran(Pendaftaran $pendaftaran, SuratDiterimaPdfService $pdfService): Response|BinaryFileResponse
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
