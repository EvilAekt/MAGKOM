<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\Kuota\StoreBulanMagangAction;
use App\Actions\Admin\Kuota\StoreKuotaAction;
use App\Actions\Admin\Kuota\ToggleKuotaAction;
use App\Actions\Admin\Kuota\UpdateBidangKuotaAction;
use App\Actions\Admin\Kuota\UpdateBulanMagangAction;
use App\Actions\Admin\Kuota\UpdateKuotaAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\KuotaRequest;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Kuota;
use App\Queries\Admin\Kuota\KuotaIndexQuery;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KuotaController extends Controller
{
    public function __construct(
        protected KuotaIndexQuery $indexQuery,
        protected StoreKuotaAction $storeKuotaAction,
        protected UpdateKuotaAction $updateKuotaAction,
        protected ToggleKuotaAction $toggleKuotaAction,
        protected UpdateBidangKuotaAction $updateBidangKuotaAction,
        protected StoreBulanMagangAction $storeBulanAction,
        protected UpdateBulanMagangAction $updateBulanAction
    ) {}

    /**
     * Menampilkan halaman kontrol kuota dan agregasi bidang magang.
     */
    public function index(Request $request): View
    {
        $selectedBulanId = $request->integer('bulan_magang_id', 0);
        $viewData = $this->indexQuery->getIndexData($selectedBulanId);

        return view('admin.kuota.index', $viewData);
    }

    /**
     * Menyimpan kuota jurusan baru atau memperbarui kuota eksisting.
     */
    public function store(KuotaRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $bulanMagangId = (int) $validated['bulan_magang_id'];
        $jumlah = (int) $validated['jumlah'];

        $jurusanIds = ! empty($validated['jurusan_ids'])
            ? array_map('intval', (array) $validated['jurusan_ids'])
            : [(int) $validated['jurusan_id']];

        $this->storeKuotaAction->execute($bulanMagangId, $jumlah, $jurusanIds);

        return redirect()->route('admin.kuota.index', ['bulan_magang_id' => $bulanMagangId])
            ->with('success', 'Kuota jurusan berhasil ditambahkan.');
    }

    /**
     * Memperbarui kuota jurusan spesifik secara transaksional.
     */
    public function update(KuotaRequest $request, Kuota $kuota): RedirectResponse
    {
        $this->updateKuotaAction->execute($kuota, $request->validated());

        return redirect()->route('admin.kuota.index', ['bulan_magang_id' => $kuota->bulan_magang_id])
            ->with('success', 'Alokasi kuota jurusan berhasil diperbarui.');
    }

    /**
     * Mengaktifkan atau menonaktifkan kuota jurusan dengan validasi peserta aktif (BR-12).
     */
    public function toggleAktif(Request $request, Kuota $kuota): RedirectResponse
    {
        $result = $this->toggleKuotaAction->execute($kuota);

        if (! $result['success']) {
            return back()->with('error', $result['message']);
        }

        return redirect()->route('admin.kuota.index', ['bulan_magang_id' => $kuota->bulan_magang_id])
            ->with('success', $result['message']);
    }

    /**
     * Membuat bulan magang baru langsung dari modal halaman kuota.
     */
    public function storeBulan(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_bulan' => ['nullable', 'string', 'max:100'],
            'tgl_buka' => ['required', 'date'],
            'tgl_tutup' => ['required', 'date', 'after_or_equal:tgl_buka'],
            'bulan_mulai' => ['required', 'date', 'after:tgl_tutup'],
            'status_awal' => ['sometimes', 'in:draft,dibuka'],
        ], [
            'nama_bulan.max' => 'Nama bulan maksimal 100 karakter.',
            'bulan_mulai.required' => 'Tanggal mulai magang wajib diisi.',
            'bulan_mulai.after' => 'Tanggal mulai magang harus setelah tanggal tutup pendaftaran.',
            'tgl_buka.required' => 'Tanggal buka pendaftaran wajib diisi.',
            'tgl_tutup.required' => 'Tanggal tutup pendaftaran wajib diisi.',
            'tgl_tutup.after_or_equal' => 'Tanggal tutup harus setelah atau sama dengan tanggal buka.',
        ]);

        $statusAwal = $request->input('status_awal', 'draft');
        $bulan = $this->storeBulanAction->execute($validated, $statusAwal);

        return redirect()->route('admin.kuota.index', ['bulan_magang_id' => $bulan->id])
            ->with('success', 'Bulan magang baru berhasil dibuat.');
    }

    /**
     * Memperbarui jadwal bulan magang aktif langsung dari modal halaman kuota.
     */
    public function updateBulan(Request $request, BulanMagang $bulanMagang): RedirectResponse
    {
        $validated = $request->validate([
            'nama_bulan' => ['nullable', 'string', 'max:100'],
            'bulan_mulai' => ['required', 'date'],
            'tgl_buka' => ['required', 'date'],
            'tgl_tutup' => ['required', 'date', 'after_or_equal:tgl_buka'],
            'status_awal' => ['sometimes', 'in:draft,dibuka'],
        ], [
            'nama_bulan.max' => 'Nama bulan maksimal 100 karakter.',
            'bulan_mulai.required' => 'Tanggal mulai magang wajib diisi.',
            'tgl_buka.required' => 'Tanggal buka pendaftaran wajib diisi.',
            'tgl_tutup.required' => 'Tanggal tutup pendaftaran wajib diisi.',
            'tgl_tutup.after_or_equal' => 'Tanggal tutup harus setelah atau sama dengan tanggal buka.',
        ]);

        $statusAwal = $request->input('status_awal');
        $this->updateBulanAction->execute($bulanMagang, $validated, $statusAwal);

        return redirect()->route('admin.kuota.index', ['bulan_magang_id' => $bulanMagang->id])
            ->with('success', 'Jadwal bulan magang berhasil diperbarui.');
    }

    /**
     * Memperbarui alokasi kuota per bidang serta membuka/menutup kuota bidang.
     */
    public function updateBidangKuota(Request $request, Bidang $bidang): RedirectResponse
    {
        $bulanMagangId = $request->integer('bulan_magang_id');
        $alokasi = $request->input('alokasi', []);
        $statusBidang = $request->input('status_bidang', 'buka');

        if (! $bulanMagangId) {
            return back()->with('error', 'Bulan magang tidak valid.');
        }

        $result = $this->updateBidangKuotaAction->execute($bidang, $bulanMagangId, (array) $alokasi, $statusBidang);

        if (! $result['success']) {
            return back()->with('error', $result['message']);
        }

        return redirect()->route('admin.kuota.index', ['bulan_magang_id' => $bulanMagangId])
            ->with('success', $result['message']);
    }
}
