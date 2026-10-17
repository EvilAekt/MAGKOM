<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Enums\TipeAuditEnum;
use App\Http\Controllers\Controller;
use App\Models\ModulDinamis;
use App\Models\ModulDinamisField;
use App\Services\AuditService;
use App\Services\DatabaseInspectorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ModulBuilderController extends Controller
{
    public const AVAILABLE_ICONS = [
        // 💻 TI & Perangkat Jaringan
        'laptop'       => 'Laptop / Komputer',
        'server'       => 'Server / Jaringan',
        'cpu'          => 'Perangkat Keras / IoT',
        'hard-drive'   => 'Hard Disk / Media Simpan',
        'wifi'         => 'Koneksi WiFi / Hotspot',
        'terminal'     => 'Terminal / Konsol',
        'smartphone'   => 'Smartphone / Ponsel',
        'monitor'      => 'Monitor / Layar Kerja',
        'printer'      => 'Printer / Percetakan',
        'shield'       => 'Keamanan Siber / Proteksi',

        // 📁 Data & Administrasi Arsip
        'database'     => 'Basis Data / SQL',
        'table'        => 'Tabel Data / Spreadsheet',
        'file-text'    => 'Dokumen / Formulir',
        'folder'       => 'Folder / Direktori',
        'book'         => 'Buku / Pedoman',
        'clipboard'    => 'Papan Klip / Catatan',
        'archive'      => 'Arsip / Kotak Penyimpanan',
        'layers'       => 'Lapisan / Modul Dinamis',
        'file-plus'    => 'Tambah Dokumen / Berkas Baru',

        // 👥 Manajemen, Tim & Institusi
        'users'        => 'Grup Pengguna / Mitra',
        'user-check'   => 'Verifikasi User / Peserta',
        'user-plus'    => 'Registrasi User / Anggota',
        'briefcase'    => 'Unit Kerja / Portofolio',
        'award'        => 'Sertifikat / Prestasi',
        'building'     => 'Gedung / OPD Kominfo',
        'compass'      => 'Arah / Kebijakan Strategis',
        'flag'         => 'Prioritas / Bendera Tugas',

        // ⚙️ Layanan Publik & Operasional
        'calendar'     => 'Kalender / Jadwal Kegiatan',
        'clock'        => 'Waktu / Presensi / Jam',
        'check-circle' => 'Selesai / Terverifikasi',
        'activity'     => 'Aktivitas / Log Dinamis',
        'bell'         => 'Notifikasi / Pengumuman',
        'mail'         => 'Surat Masuk / Undangan',
        'phone'        => 'Kontak Telepon / Hotline',
        'map-pin'      => 'Lokasi / Wilayah Sleman',
        'tool'         => 'Peralatan / Maintenance',
        'sliders'      => 'Pengaturan / Konfigurasi',

        // 📦 Inventaris & Aset Logistik
        'box'          => 'Kotak Aset / Inventaris',
        'package'      => 'Paket / Pengiriman Barang',
        'tag'          => 'Label / Klasifikasi Barang',
        'credit-card'  => 'Anggaran / Kartu Pembayaran',
        'qr-code'      => 'Kode QR / Autentikasi',
    ];

    public function __construct(
        protected AuditService $auditService,
        protected DatabaseInspectorService $inspectorService
    ) {}

    /**
     * Menampilkan katalog seluruh modul kustom yang telah dibuat.
     */
    public function index(): View
    {
        $modules = ModulDinamis::withCount(['fields', 'records'])
            ->orderBy('urutan')
            ->orderBy('id', 'desc')
            ->get();

        return view('superadmin.modul-builder.index', compact('modules'));
    }

    /**
     * Menampilkan antarmuka visual form builder untuk merancang modul kustom baru.
     */
    public function create(): View
    {
        $availableTables = $this->inspectorService->getAvailableTables();

        return view('superadmin.modul-builder.create', [
            'availableIcons'   => self::AVAILABLE_ICONS,
            'availableTables'  => $availableTables,
            'standardActions'  => ModulDinamis::STANDARD_ACTIONS,
        ]);
    }

    /**
     * Endpoint AJAX API: Mendapatkan daftar tabel database untuk mode Reverse-Engineering.
     */
    public function apiGetTables(): JsonResponse
    {
        $tables = $this->inspectorService->getAvailableTables();
        return response()->json([
            'success' => true,
            'tables'  => $tables,
        ]);
    }

    /**
     * Endpoint AJAX API: Menginspeksi skema kolom dari tabel fisik terpilih (Porting CrudGenerator).
     */
    public function apiInspectTable(Request $request): JsonResponse
    {
        $table = trim((string) $request->query('table', ''));
        if ($table === '') {
            return response()->json(['success' => false, 'message' => 'Nama tabel wajib disertakan.'], 400);
        }

        try {
            $schema = $this->inspectorService->inspectTable($table);
            return response()->json([
                'success' => true,
                'schema'  => $schema,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Menyimpan definisi modul dan skema kolom baru ke basis data.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_modul'       => 'required|string|max:100',
            'slug'             => 'nullable|string|max:100|unique:modul_dinamis,slug',
            'tipe_sumber'      => 'nullable|string|in:dynamic,physical_table',
            'nama_tabel_fisik' => 'nullable|required_if:tipe_sumber,physical_table|string|max:64',
            'primary_key'      => 'nullable|string|max:64',
            'deskripsi'        => 'nullable|string|max:1000',
            'ikon'             => 'required|string|max:50',
            'target_roles'     => 'required|array|min:1',
            'target_roles.*'   => 'string|in:superadmin,admin,pembimbing,peserta',
            'scope_mode'       => 'nullable|string|in:none,bidang_scope,creator_scope',
            'izin_aksi'        => 'nullable|array',
            'log_aksi'         => 'nullable|array',
            'fields'           => 'required|array|min:1',
            'fields.*.nama_kolom'          => 'required|string|max:60|regex:/^[a-zA-Z0-9_]+$/',
            'fields.*.label'               => 'required|string|max:100',
            'fields.*.tipe_data'           => 'required|string|in:text,number,textarea,select,multiselect,date,datetime,file,boolean,relation',
            'fields.*.opsi_pilihan'        => 'nullable|string|max:500',
            'fields.*.relasi_tabel'        => 'nullable|string|max:60',
            'fields.*.relasi_kolom_tampil' => 'nullable|string|max:60',
        ]);

        $slug = !empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($validated['nama_modul']);

        // Pastikan slug unik
        $baseSlug = $slug;
        $counter = 1;
        while (ModulDinamis::where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        $tipeSumber = $validated['tipe_sumber'] ?? 'dynamic';
        $primaryKey = $validated['primary_key'] ?? 'id';
        $scopeMode  = $validated['scope_mode'] ?? 'none';

        DB::transaction(function () use ($validated, $slug, $request, $tipeSumber, $primaryKey, $scopeMode) {
            $maxUrutan = (int) ModulDinamis::max('urutan');

            $modul = ModulDinamis::create([
                'nama_modul'       => $validated['nama_modul'],
                'slug'             => $slug,
                'tipe_sumber'      => $tipeSumber,
                'nama_tabel_fisik' => $tipeSumber === 'physical_table' ? ($validated['nama_tabel_fisik'] ?? null) : null,
                'primary_key'      => $primaryKey,
                'is_manual_pk'     => $request->boolean('is_manual_pk'),
                'deskripsi'        => $validated['deskripsi'] ?? null,
                'ikon'             => $validated['ikon'],
                'target_roles'     => $validated['target_roles'],
                'izin_aksi'        => $validated['izin_aksi'] ?? null,
                'log_aksi'         => $validated['log_aksi'] ?? null,
                'status_aktif'     => true,
                'is_public_read'   => $request->boolean('is_public_read'),
                'scope_mode'       => $scopeMode,
                'urutan'           => $maxUrutan + 1,
                'dibuat_oleh'      => auth()->id(),
            ]);

            foreach ($request->input('fields', []) as $idx => $f) {
                $opsi = null;
                if (!empty($f['opsi_pilihan'])) {
                    $opsi = array_values(array_filter(array_map('trim', explode(',', (string) $f['opsi_pilihan']))));
                }

                ModulDinamisField::create([
                    'modul_id'            => $modul->id,
                    'nama_kolom'          => Str::snake((string) $f['nama_kolom']),
                    'label'               => (string) $f['label'],
                    'tipe_data'           => (string) $f['tipe_data'],
                    'opsi_pilihan'        => $opsi,
                    'relasi_tabel'        => !empty($f['relasi_tabel']) ? trim((string) $f['relasi_tabel']) : null,
                    'relasi_kolom_tampil' => !empty($f['relasi_kolom_tampil']) ? trim((string) $f['relasi_kolom_tampil']) : null,
                    'wajib'               => !empty($f['wajib']),
                    'tampil_di_tabel'     => !empty($f['tampil_di_tabel']),
                    'dapat_dicari'        => !empty($f['dapat_dicari']),
                    'urutan'              => $idx + 1,
                ]);
            }

            Cache::forget('dynamic_modules_sidebar');

            $this->auditService->catat(
                tipe: TipeAuditEnum::Aksi,
                tabel: 'modul_dinamis',
                idData: $modul->id,
                detail: ['aksi' => "Membuat Modul Kustom Baru: {$modul->nama_modul} ({$modul->slug}) [Mode: {$modul->tipe_sumber}]"],
                channel: 'dynamic-module',
                severity: 'success'
            );
        });

        return redirect()->route('superadmin.modul-builder.index')
            ->with('status_keberhasilan', "Modul kustom \"{$validated['nama_modul']}\" berhasil diciptakan dan diterbitkan ke sidebar.");
    }

    /**
     * Form edit modul kustom yang sudah ada.
     */
    public function edit(ModulDinamis $modul): View
    {
        $modul->load('fields');
        $availableTables = $this->inspectorService->getAvailableTables();

        return view('superadmin.modul-builder.edit', [
            'modul'           => $modul,
            'availableIcons'  => self::AVAILABLE_ICONS,
            'availableTables' => $availableTables,
            'standardActions' => ModulDinamis::STANDARD_ACTIONS,
        ]);
    }

    /**
     * Memperbarui definisi dan skema kolom modul kustom.
     */
    public function update(Request $request, ModulDinamis $modul): RedirectResponse
    {
        $validated = $request->validate([
            'nama_modul'       => 'required|string|max:100',
            'tipe_sumber'      => 'nullable|string|in:dynamic,physical_table',
            'nama_tabel_fisik' => 'nullable|required_if:tipe_sumber,physical_table|string|max:64',
            'primary_key'      => 'nullable|string|max:64',
            'deskripsi'        => 'nullable|string|max:1000',
            'ikon'             => 'required|string|max:50',
            'target_roles'     => 'required|array|min:1',
            'target_roles.*'   => 'string|in:superadmin,admin,pembimbing,peserta',
            'scope_mode'       => 'nullable|string|in:none,bidang_scope,creator_scope',
            'izin_aksi'        => 'nullable|array',
            'log_aksi'         => 'nullable|array',
            'fields'           => 'required|array|min:1',
            'fields.*.nama_kolom'          => 'required|string|max:60|regex:/^[a-zA-Z0-9_]+$/',
            'fields.*.label'               => 'required|string|max:100',
            'fields.*.tipe_data'           => 'required|string|in:text,number,textarea,select,multiselect,date,datetime,file,boolean,relation',
            'fields.*.opsi_pilihan'        => 'nullable|string|max:500',
            'fields.*.relasi_tabel'        => 'nullable|string|max:60',
            'fields.*.relasi_kolom_tampil' => 'nullable|string|max:60',
        ]);

        $tipeSumber = $validated['tipe_sumber'] ?? ($modul->tipe_sumber ?? 'dynamic');
        $primaryKey = $validated['primary_key'] ?? ($modul->primary_key ?? 'id');
        $scopeMode  = $validated['scope_mode'] ?? ($modul->scope_mode ?? 'none');

        DB::transaction(function () use ($validated, $modul, $request, $tipeSumber, $primaryKey, $scopeMode) {
            $modul->update([
                'nama_modul'       => $validated['nama_modul'],
                'tipe_sumber'      => $tipeSumber,
                'nama_tabel_fisik' => $tipeSumber === 'physical_table' ? ($validated['nama_tabel_fisik'] ?? null) : null,
                'primary_key'      => $primaryKey,
                'is_manual_pk'     => $request->boolean('is_manual_pk'),
                'deskripsi'        => $validated['deskripsi'] ?? null,
                'ikon'             => $validated['ikon'],
                'target_roles'     => $validated['target_roles'],
                'izin_aksi'        => $validated['izin_aksi'] ?? null,
                'log_aksi'         => $validated['log_aksi'] ?? null,
                'is_public_read'   => $request->boolean('is_public_read'),
                'scope_mode'       => $scopeMode,
            ]);

            // Re-sync fields
            $modul->fields()->delete();

            foreach ($request->input('fields', []) as $idx => $f) {
                $opsi = null;
                if (!empty($f['opsi_pilihan'])) {
                    $opsi = array_values(array_filter(array_map('trim', explode(',', (string) $f['opsi_pilihan']))));
                }

                ModulDinamisField::create([
                    'modul_id'            => $modul->id,
                    'nama_kolom'          => Str::snake((string) $f['nama_kolom']),
                    'label'               => (string) $f['label'],
                    'tipe_data'           => (string) $f['tipe_data'],
                    'opsi_pilihan'        => $opsi,
                    'relasi_tabel'        => !empty($f['relasi_tabel']) ? trim((string) $f['relasi_tabel']) : null,
                    'relasi_kolom_tampil' => !empty($f['relasi_kolom_tampil']) ? trim((string) $f['relasi_kolom_tampil']) : null,
                    'wajib'               => !empty($f['wajib']),
                    'tampil_di_tabel'     => !empty($f['tampil_di_tabel']),
                    'dapat_dicari'        => !empty($f['dapat_dicari']),
                    'urutan'              => $idx + 1,
                ]);
            }

            Cache::forget('dynamic_modules_sidebar');

            $this->auditService->catat(
                tipe: TipeAuditEnum::Aksi,
                tabel: 'modul_dinamis',
                idData: $modul->id,
                detail: ['aksi' => "Memperbarui Definisi Modul Kustom: {$modul->nama_modul} [Mode: {$modul->tipe_sumber}]"],
                channel: 'dynamic-module',
                severity: 'info'
            );
        });

        return redirect()->route('superadmin.modul-builder.index')
            ->with('status_keberhasilan', "Skema modul \"{$modul->nama_modul}\" berhasil diperbarui.");
    }

    /**
     * Toggle status aktif modul (muncul/sembunyi di sidebar).
     */
    public function toggleAktif(ModulDinamis $modul): RedirectResponse
    {
        $newStatus = !$modul->status_aktif;
        $modul->update(['status_aktif' => $newStatus]);
        Cache::forget('dynamic_modules_sidebar');

        $this->auditService->catat(
            tipe: TipeAuditEnum::Aksi,
            tabel: 'modul_dinamis',
            idData: $modul->id,
            detail: ['aksi' => "Toggle Status Modul {$modul->nama_modul} menjadi " . ($newStatus ? 'Aktif' : 'Nonaktif')],
            channel: 'dynamic-module',
            severity: 'info'
        );

        return back()->with('status_keberhasilan', "Status modul \"{$modul->nama_modul}\" berhasil diubah menjadi " . ($newStatus ? 'Aktif' : 'Nonaktif') . '.');
    }

    /**
     * Menghapus modul kustom beserta skema dan seluruh baris datanya.
     */
    public function destroy(ModulDinamis $modul): RedirectResponse
    {
        $nama = $modul->nama_modul;
        $modulId = $modul->id;
        $modul->delete();

        Cache::forget('dynamic_modules_sidebar');

        $this->auditService->catat(
            tipe: TipeAuditEnum::Aksi,
            tabel: 'modul_dinamis',
            idData: $modulId,
            detail: ['aksi' => "Menghapus Modul Kustom: {$nama}"],
            channel: 'dynamic-module',
            severity: 'warning'
        );

        return redirect()->route('superadmin.modul-builder.index')
            ->with('status_keberhasilan', "Modul \"{$nama}\" dan seluruh datanya berhasil dihapus dari sistem.");
    }
}
