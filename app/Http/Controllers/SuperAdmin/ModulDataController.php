<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Enums\TipeAuditEnum;
use App\Http\Controllers\Controller;
use App\Models\ModulDinamis;
use App\Models\ModulDinamisData;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ModulDataController extends Controller
{
    public function __construct(protected AuditService $auditService) {}

    /**
     * Menampilkan daftar baris data modul terpilih (Mendukung Dynamic JSON & Physical Table Direct).
     */
    public function index(Request $request, string $slug): View
    {
        $modul = ModulDinamis::with('fields')->where('slug', $slug)->firstOrFail();
        $this->authorizeAction($modul, 'list');

        $search = trim((string) $request->input('q'));
        $searchableFields = $modul->fields->where('dapat_dicari', true)->pluck('nama_kolom');

        if ($modul->tipe_sumber === 'physical_table' && ! empty($modul->nama_tabel_fisik)) {
            $table = $modul->nama_tabel_fisik;
            $pk = $modul->primary_key ?: 'id';
            $query = DB::table($table);

            $this->applyScopeFilter($query, $modul, $table);

            if ($search !== '' && $searchableFields->isNotEmpty()) {
                $query->where(function ($sub) use ($searchableFields, $search) {
                    foreach ($searchableFields as $col) {
                        $sub->orWhere($col, 'LIKE', "%{$search}%");
                    }
                });
            }

            $records = $query->orderBy($pk, 'desc')->paginate(25)->withQueryString();
        } else {
            $query = ModulDinamisData::where('modul_id', $modul->id)
                ->with(['creator', 'updater'])
                ->orderBy('id', 'desc');

            $this->applyDynamicScopeFilter($query, $modul);

            if ($search !== '' && $searchableFields->isNotEmpty()) {
                $query->where(function ($sub) use ($searchableFields, $search) {
                    foreach ($searchableFields as $col) {
                        $sub->orWhere("data->{$col}", 'LIKE', "%{$search}%");
                    }
                });
            }

            $records = $query->paginate(25)->withQueryString();
        }

        $relationalOptions = $this->resolveRelationalOptions($modul);

        return view('superadmin.modul-data.index', compact('modul', 'records', 'relationalOptions'));
    }

    /**
     * Menyimpan baris data baru ke dalam modul dinamis atau tabel fisik.
     */
    public function store(Request $request, string $slug): RedirectResponse
    {
        $modul = ModulDinamis::with('fields')->where('slug', $slug)->firstOrFail();
        $this->authorizeAction($modul, 'create');

        $rules = $this->buildValidationRules($modul);
        $validated = $request->validate($rules);

        if ($modul->tipe_sumber === 'physical_table' && ! empty($modul->nama_tabel_fisik)) {
            $table = $modul->nama_tabel_fisik;
            $pk = $modul->primary_key ?: 'id';

            if ($modul->is_manual_pk) {
                $this->ensurePrimaryKeyAvailable($modul, $request->input($pk));
            }

            $payload = [];
            foreach ($modul->fields as $field) {
                $col = $field->nama_kolom;
                if ($col === $pk && ! $modul->is_manual_pk) {
                    continue; // Skip auto-increment primary key
                }

                if ($field->tipe_data === 'file' && $request->hasFile($col)) {
                    $file = $request->file($col);
                    $path = $file->storeAs("public/modul-dinamis/{$modul->slug}/".date('Y/m'), Str::random(24).'.'.$file->getClientOriginalExtension());
                    $payload[$col] = Storage::url($path);
                } elseif ($field->tipe_data === 'boolean') {
                    $payload[$col] = $request->boolean($col) ? 1 : 0;
                } else {
                    $payload[$col] = $validated[$col] ?? null;
                }
            }

            $this->injectPhysicalScopeData($payload, $modul, $table);

            if (Schema::hasColumn($table, 'created_at')) {
                $payload['created_at'] = now();
            }
            if (Schema::hasColumn($table, 'updated_at')) {
                $payload['updated_at'] = now();
            }

            $insertId = DB::table($table)->insertGetId($payload, $pk);
            $newId = $modul->is_manual_pk ? ($payload[$pk] ?? $insertId) : $insertId;

            if ($modul->shouldLogAction('create')) {
                $this->auditService->catat(
                    tipe: TipeAuditEnum::Aksi,
                    tabel: $table,
                    idData: is_numeric($newId) ? (int) $newId : 0,
                    nilaiBaru: $payload,
                    detail: ['aksi' => "Menambah data baru pada tabel fisik {$table} #{$newId}"],
                    channel: 'dynamic-module',
                    severity: 'success'
                );
            }

            return back()->with('status_keberhasilan', 'Data baru berhasil disimpan ke tabel basis data.');
        }

        // Mode Dynamic (JSON EAV)
        $pk = $modul->primary_key ?: 'id';
        if ($modul->is_manual_pk && $pk !== 'id') {
            $this->ensurePrimaryKeyAvailable($modul, $request->input($pk));
        }

        $dataPayload = [];
        $filesMetadata = [];

        foreach ($modul->fields as $field) {
            $col = $field->nama_kolom;

            if ($field->tipe_data === 'file' && $request->hasFile($col)) {
                $file = $request->file($col);
                $ext = $file->getClientOriginalExtension();
                $safeName = Str::random(24).'.'.$ext;
                $path = $file->storeAs("public/modul-dinamis/{$modul->slug}/".date('Y/m'), $safeName);

                $dataPayload[$col] = Storage::url($path);
                $filesMetadata[$col] = [
                    'original_name' => $file->getClientOriginalName(),
                    'mime' => $file->getClientMimeType(),
                    'size' => $file->getSize(),
                    'path' => $path,
                ];
            } elseif ($field->tipe_data === 'boolean') {
                $dataPayload[$col] = $request->boolean($col);
            } elseif ($field->tipe_data === 'multiselect') {
                $dataPayload[$col] = (array) ($request->input($col, []));
            } else {
                $dataPayload[$col] = $validated[$col] ?? null;
            }
        }

        $record = ModulDinamisData::create([
            'modul_id' => $modul->id,
            'data' => $dataPayload,
            'files_metadata' => $filesMetadata,
            'created_by' => auth()->id(),
        ]);

        if ($modul->shouldLogAction('create')) {
            $this->auditService->catat(
                tipe: TipeAuditEnum::Aksi,
                tabel: "modul_data_{$modul->slug}",
                idData: $record->id,
                nilaiBaru: $dataPayload,
                detail: ['aksi' => "Menambah data baris baru pada modul {$modul->nama_modul} #{$record->id}"],
                channel: 'dynamic-module',
                severity: 'success'
            );
        }

        return back()->with('status_keberhasilan', 'Data baru berhasil disimpan ke modul.');
    }

    /**
     * Memperbarui baris data modul (Dynamic atau Physical Table).
     */
    public function update(Request $request, string $slug, string|int $id): RedirectResponse
    {
        $modul = ModulDinamis::with('fields')->where('slug', $slug)->firstOrFail();
        $this->authorizeAction($modul, 'edit');

        $rules = $this->buildValidationRules($modul, isUpdate: true);
        $validated = $request->validate($rules);

        if ($modul->tipe_sumber === 'physical_table' && ! empty($modul->nama_tabel_fisik)) {
            $table = $modul->nama_tabel_fisik;
            $pk = $modul->primary_key ?: 'id';

            $existing = DB::table($table)->where($pk, $id)->first();
            if (! $existing) {
                abort(404, 'Baris data tidak ditemukan pada tabel.');
            }

            if ($modul->is_manual_pk && $request->has($pk) && (string) $request->input($pk) !== (string) $id) {
                $this->ensurePrimaryKeyAvailable($modul, $request->input($pk), $id);
            }

            $payload = [];
            foreach ($modul->fields as $field) {
                $col = $field->nama_kolom;

                if ($field->tipe_data === 'file' && $request->hasFile($col)) {
                    $file = $request->file($col);
                    $path = $file->storeAs("public/modul-dinamis/{$modul->slug}/".date('Y/m'), Str::random(24).'.'.$file->getClientOriginalExtension());
                    $payload[$col] = Storage::url($path);
                } elseif ($field->tipe_data === 'boolean') {
                    $payload[$col] = $request->boolean($col) ? 1 : 0;
                } elseif ($request->has($col)) {
                    $payload[$col] = $validated[$col] ?? null;
                }
            }

            if (Schema::hasColumn($table, 'updated_at')) {
                $payload['updated_at'] = now();
            }

            DB::table($table)->where($pk, $id)->update($payload);

            if ($modul->shouldLogAction('edit')) {
                $this->auditService->catat(
                    tipe: TipeAuditEnum::Aksi,
                    tabel: $table,
                    idData: is_numeric($id) ? (int) $id : 0,
                    nilaiLama: (array) $existing,
                    nilaiBaru: $payload,
                    detail: ['aksi' => "Memperbarui baris data pada tabel fisik {$table} #{$id}"],
                    channel: 'dynamic-module',
                    severity: 'info'
                );
            }

            return back()->with('status_keberhasilan', 'Data baris berhasil diperbarui.');
        }

        // Mode Dynamic JSON
        $record = ModulDinamisData::where('modul_id', $modul->id)->findOrFail($id);
        $dataPayload = $record->data ?? [];
        $filesMetadata = $record->files_metadata ?? [];

        foreach ($modul->fields as $field) {
            $col = $field->nama_kolom;

            if ($field->tipe_data === 'file' && $request->hasFile($col)) {
                if (! empty($filesMetadata[$col]['path'])) {
                    Storage::delete($filesMetadata[$col]['path']);
                }

                $file = $request->file($col);
                $ext = $file->getClientOriginalExtension();
                $safeName = Str::random(24).'.'.$ext;
                $path = $file->storeAs("public/modul-dinamis/{$modul->slug}/".date('Y/m'), $safeName);

                $dataPayload[$col] = Storage::url($path);
                $filesMetadata[$col] = [
                    'original_name' => $file->getClientOriginalName(),
                    'mime' => $file->getClientMimeType(),
                    'size' => $file->getSize(),
                    'path' => $path,
                ];
            } elseif ($field->tipe_data === 'boolean') {
                $dataPayload[$col] = $request->boolean($col);
            } elseif ($field->tipe_data === 'multiselect') {
                $dataPayload[$col] = (array) ($request->input($col, []));
            } elseif ($request->has($col)) {
                $dataPayload[$col] = $validated[$col] ?? null;
            }
        }

        $oldData = $record->data;
        $record->update([
            'data' => $dataPayload,
            'files_metadata' => $filesMetadata,
            'updated_by' => auth()->id(),
        ]);

        if ($modul->shouldLogAction('edit')) {
            $this->auditService->catat(
                tipe: TipeAuditEnum::Aksi,
                tabel: "modul_data_{$modul->slug}",
                idData: $record->id,
                nilaiLama: $oldData,
                nilaiBaru: $dataPayload,
                detail: ['aksi' => "Memperbarui data baris modul {$modul->nama_modul} #{$record->id}"],
                channel: 'dynamic-module',
                severity: 'info'
            );
        }

        return back()->with('status_keberhasilan', 'Data baris berhasil diperbarui.');
    }

    /**
     * Menghapus baris data (Direct Delete atau Soft Delete).
     */
    public function destroy(string $slug, string|int $id): RedirectResponse
    {
        $modul = ModulDinamis::where('slug', $slug)->firstOrFail();
        $this->authorizeAction($modul, 'delete');

        if ($modul->tipe_sumber === 'physical_table' && ! empty($modul->nama_tabel_fisik)) {
            $table = $modul->nama_tabel_fisik;
            $pk = $modul->primary_key ?: 'id';

            $existing = DB::table($table)->where($pk, $id)->first();
            DB::table($table)->where($pk, $id)->delete();

            if ($modul->shouldLogAction('delete')) {
                $this->auditService->catat(
                    tipe: TipeAuditEnum::Aksi,
                    tabel: $table,
                    idData: is_numeric($id) ? (int) $id : 0,
                    nilaiLama: (array) $existing,
                    detail: ['aksi' => "Menghapus data baris dari tabel fisik {$table} #{$id}"],
                    channel: 'dynamic-module',
                    severity: 'warning'
                );
            }

            return back()->with('status_keberhasilan', 'Data baris berhasil dihapus dari tabel basis data.');
        }

        $record = ModulDinamisData::where('modul_id', $modul->id)->findOrFail($id);
        $oldData = $record->data;
        $record->delete();

        if ($modul->shouldLogAction('delete')) {
            $this->auditService->catat(
                tipe: TipeAuditEnum::Aksi,
                tabel: "modul_data_{$modul->slug}",
                idData: is_numeric($id) ? (int) $id : 0,
                nilaiLama: $oldData,
                detail: ['aksi' => "Menghapus data baris modul {$modul->nama_modul} #{$id}"],
                channel: 'dynamic-module',
                severity: 'warning'
            );
        }

        return back()->with('status_keberhasilan', 'Data baris berhasil dihapus.');
    }

    /**
     * Ekspor seluruh data modul kustom ke format CSV streaming.
     */
    public function export(string $slug): StreamedResponse
    {
        $modul = ModulDinamis::with('fields')->where('slug', $slug)->firstOrFail();
        $this->authorizeAction($modul, 'export_csv');

        $fileName = "ekspor-modul-{$modul->slug}-".now()->format('Ymd-His').'.csv';

        if ($modul->shouldLogAction('export_csv')) {
            $this->auditService->catat(
                tipe: TipeAuditEnum::Aksi,
                tabel: 'modul_dinamis',
                idData: $modul->id,
                detail: ['aksi' => "Ekspor CSV Data Modul: {$modul->nama_modul}"],
                channel: 'dynamic-module',
                severity: 'info'
            );
        }

        return response()->streamDownload(function () use ($modul) {
            $handle = fopen('php://output', 'wb');
            fwrite($handle, "\xEF\xBB\xBF"); // UTF-8 BOM

            $headers = ['ID'];
            foreach ($modul->fields as $f) {
                $headers[] = $f->label;
            }
            fputcsv($handle, $headers);

            if ($modul->tipe_sumber === 'physical_table' && ! empty($modul->nama_tabel_fisik)) {
                $pk = $modul->primary_key ?: 'id';
                $query = DB::table($modul->nama_tabel_fisik);
                $this->applyScopeFilter($query, $modul, $modul->nama_tabel_fisik);

                $query->orderBy($pk, 'asc')->chunk(250, function ($rows) use ($handle, $modul, $pk) {
                    foreach ($rows as $row) {
                        $rowArr = (array) $row;
                        $line = [$this->sanitizeCsvCell($rowArr[$pk] ?? '')];
                        foreach ($modul->fields as $f) {
                            $val = $rowArr[$f->nama_kolom] ?? '';
                            $line[] = $this->sanitizeCsvCell($val);
                        }
                        fputcsv($handle, $line);
                    }
                });
            } else {
                $query = ModulDinamisData::where('modul_id', $modul->id);
                $this->applyDynamicScopeFilter($query, $modul);

                $query->chunk(250, function ($rows) use ($handle, $modul) {
                    foreach ($rows as $row) {
                        $line = [$this->sanitizeCsvCell($row->id)];
                        foreach ($modul->fields as $f) {
                            $val = $row->data[$f->nama_kolom] ?? '';
                            if (is_array($val)) {
                                $val = implode(', ', $val);
                            } elseif (is_bool($val)) {
                                $val = $val ? 'Ya' : 'Tidak';
                            }
                            $line[] = $this->sanitizeCsvCell($val);
                        }
                        fputcsv($handle, $line);
                    }
                });
            }

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
        ]);
    }

    /**
     * Ekspor Dokumen Cetak Formal / PDF (Porting admin_pdf & tpl_pdf.html Basefw).
     */
    public function exportPdf(string $slug): Response
    {
        $modul = ModulDinamis::with('fields')->where('slug', $slug)->firstOrFail();
        $this->authorizeAction($modul, 'export_pdf');

        if ($modul->shouldLogAction('export_pdf')) {
            $this->auditService->catat(
                tipe: TipeAuditEnum::Aksi,
                tabel: 'modul_dinamis',
                idData: $modul->id,
                detail: ['aksi' => "Cetak / Ekspor PDF Modul: {$modul->nama_modul}"],
                channel: 'dynamic-module',
                severity: 'info'
            );
        }

        $records = $this->getAllRecordsForPrint($modul);

        $html = view('superadmin.modul-data.print', [
            'modul' => $modul,
            'records' => $records,
            'isPdfMode' => true,
        ])->render();

        return response($html, 200, [
            'Content-Type' => 'text/html; charset=UTF-8',
            'Content-Disposition' => "inline; filename=\"laporan-{$modul->slug}.html\"",
        ]);
    }

    /**
     * Pratinjau Cetak Standar Kertas Dinas (Porting tpl-print.html Basefw).
     */
    public function printPreview(string $slug): View
    {
        $modul = ModulDinamis::with('fields')->where('slug', $slug)->firstOrFail();
        $this->authorizeAction($modul, 'print');

        $records = $this->getAllRecordsForPrint($modul);

        return view('superadmin.modul-data.print', [
            'modul' => $modul,
            'records' => $records,
            'isPdfMode' => false,
        ]);
    }

    /**
     * Mengunduh berkas lampiran yang diunggah ke modul kustom secara terotorisasi.
     */
    public function downloadFile(string $slug, int|string $id, string $field): BinaryFileResponse
    {
        $modul = ModulDinamis::where('slug', $slug)->firstOrFail();
        $this->authorizeAction($modul, 'show');

        $record = ModulDinamisData::where('modul_id', $modul->id)->findOrFail($id);
        $metadata = $record->files_metadata[$field] ?? null;

        if (! $metadata || empty($metadata['path']) || ! Storage::exists($metadata['path'])) {
            abort(404, 'Berkas lampiran tidak ditemukan atau telah dihapus.');
        }

        $absolutePath = Storage::path($metadata['path']);
        $downloadName = $metadata['original_name'] ?? basename($absolutePath);

        return response()->download($absolutePath, $downloadName);
    }

    /**
     * Memeriksa otorisasi sub-aksi spesifik pengguna.
     */
    protected function authorizeAction(ModulDinamis $modul, string $action): void
    {
        $user = auth()->user();
        if (! $user) {
            abort(401);
        }

        $userRole = is_object($user->role) && property_exists($user->role, 'value') ? $user->role->value : (string) $user->role;
        if (! $modul->canRolePerformAction($userRole, $action)) {
            abort(403, "Akses Ditolak: Role Anda ({$userRole}) tidak memiliki izin untuk aksi [{$action}] pada modul {$modul->nama_modul}.");
        }
    }

    /**
     * Validasi keunikan Primary Key manual (Porting ensurePrimaryKeyAvailable Basefw).
     */
    protected function ensurePrimaryKeyAvailable(ModulDinamis $modul, mixed $pkValue, mixed $currentId = null): void
    {
        $pkCol = $modul->primary_key ?: 'id';

        if (empty($pkValue) || trim((string) $pkValue) === '') {
            throw ValidationException::withMessages([
                $pkCol => "Primary key [{$pkCol}] wajib diisi.",
            ]);
        }

        if ($modul->tipe_sumber === 'physical_table' && ! empty($modul->nama_tabel_fisik)) {
            $query = DB::table($modul->nama_tabel_fisik)->where($pkCol, $pkValue);
            if ($currentId !== null) {
                $query->where($pkCol, '!=', $currentId);
            }
            if ($query->exists()) {
                throw ValidationException::withMessages([
                    $pkCol => "Primary key \"{$pkValue}\" sudah terdaftar pada basis data.",
                ]);
            }
        } else {
            $query = ModulDinamisData::where('modul_id', $modul->id)->where("data->{$pkCol}", (string) $pkValue);
            if ($currentId !== null) {
                $query->where('id', '!=', $currentId);
            }
            if ($query->exists()) {
                throw ValidationException::withMessages([
                    $pkCol => "Primary key \"{$pkValue}\" sudah digunakan.",
                ]);
            }
        }
    }

    /**
     * Terapkan filter isolasi multi-tenancy (Porting applyUnitScope Basefw).
     */
    protected function applyScopeFilter(mixed $query, ModulDinamis $modul, string $table): void
    {
        $user = auth()->user();
        if (! $user) {
            return;
        }

        $userRole = is_object($user->role) && property_exists($user->role, 'value') ? $user->role->value : (string) $user->role;
        if (strtolower($userRole) === 'superadmin') {
            return;
        }

        if ($modul->scope_mode === 'bidang_scope') {
            $bidangId = $user->pembimbing?->bidang_id ?? $user->bidang_id ?? null;
            if ($bidangId) {
                if (Schema::hasColumn($table, 'bidang_id')) {
                    $query->where('bidang_id', $bidangId);
                } elseif (Schema::hasColumn($table, 'id_unit_kerja')) {
                    $query->where('id_unit_kerja', $bidangId);
                }
            }
        } elseif ($modul->scope_mode === 'creator_scope' && Schema::hasColumn($table, 'created_by')) {
            $query->where('created_by', $user->id);
        }
    }

    /**
     * Terapkan filter isolasi multi-tenancy pada mode Dynamic JSON.
     */
    protected function applyDynamicScopeFilter(mixed $query, ModulDinamis $modul): void
    {
        $user = auth()->user();
        if (! $user) {
            return;
        }

        $userRole = is_object($user->role) && property_exists($user->role, 'value') ? $user->role->value : (string) $user->role;
        if (strtolower($userRole) === 'superadmin') {
            return;
        }

        if ($modul->scope_mode === 'creator_scope') {
            $query->where('created_by', $user->id);
        }
    }

    /**
     * Suntikkan data scope otomatis saat insert ke tabel fisik (Porting fillUnitScopeData Basefw).
     */
    protected function injectPhysicalScopeData(array &$payload, ModulDinamis $modul, string $table): void
    {
        $user = auth()->user();
        if (! $user) {
            return;
        }

        if ($modul->scope_mode === 'bidang_scope') {
            $bidangId = $user->pembimbing?->bidang_id ?? $user->bidang_id ?? null;
            if ($bidangId) {
                if (Schema::hasColumn($table, 'bidang_id') && empty($payload['bidang_id'])) {
                    $payload['bidang_id'] = $bidangId;
                } elseif (Schema::hasColumn($table, 'id_unit_kerja') && empty($payload['id_unit_kerja'])) {
                    $payload['id_unit_kerja'] = $bidangId;
                }
            }
        } elseif ($modul->scope_mode === 'creator_scope' && Schema::hasColumn($table, 'created_by') && empty($payload['created_by'])) {
            $payload['created_by'] = $user->id;
        }
    }

    /**
     * Mengambil seluruh data untuk cetak dokumen print / PDF.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function getAllRecordsForPrint(ModulDinamis $modul): array
    {
        $results = [];

        if ($modul->tipe_sumber === 'physical_table' && ! empty($modul->nama_tabel_fisik)) {
            $pk = $modul->primary_key ?: 'id';
            $query = DB::table($modul->nama_tabel_fisik);
            $this->applyScopeFilter($query, $modul, $modul->nama_tabel_fisik);
            $rows = $query->orderBy($pk, 'asc')->limit(1000)->get();

            foreach ($rows as $row) {
                $results[] = (array) $row;
            }
        } else {
            $query = ModulDinamisData::where('modul_id', $modul->id)->with('creator');
            $this->applyDynamicScopeFilter($query, $modul);
            $rows = $query->latest()->limit(1000)->get();

            foreach ($rows as $row) {
                $item = $row->data ?? [];
                $item['id'] = $row->id;
                $item['created_at'] = $row->created_at?->format('d/m/Y H:i') ?? '-';
                $results[] = $item;
            }
        }

        return $results;
    }

    /**
     * Membangun aturan validasi Laravel otomatis berdasarkan skema kolom modul.
     *
     * @return array<string, mixed>
     */
    protected function buildValidationRules(ModulDinamis $modul, bool $isUpdate = false): array
    {
        $rules = [];

        foreach ($modul->fields as $field) {
            $rule = [];
            $col = $field->nama_kolom;

            $rule[] = ($field->wajib && ! $isUpdate) ? 'required' : 'nullable';

            switch ($field->tipe_data) {
                case 'number':
                    $rule[] = 'numeric';
                    break;
                case 'textarea':
                    $rule[] = 'string';
                    $rule[] = 'max:5000';
                    break;
                case 'date':
                    $rule[] = 'date_format:Y-m-d';
                    break;
                case 'datetime':
                    $rule[] = 'date';
                    break;
                case 'boolean':
                    $rule[] = 'boolean';
                    break;
                case 'file':
                    $rule[] = 'file';
                    $rule[] = 'mimes:pdf,jpg,jpeg,png,webp,docx,xlsx';
                    $rule[] = 'max:5120'; // 5MB
                    break;
                case 'select':
                    if (! empty($field->opsi_pilihan) && is_array($field->opsi_pilihan)) {
                        $rule[] = Rule::in($field->opsi_pilihan);
                    } else {
                        $rule[] = 'string';
                        $rule[] = 'max:255';
                    }
                    break;
                case 'multiselect':
                    $rule[] = 'array';
                    break;
                case 'relation':
                    if (! empty($field->relasi_tabel) && Schema::hasTable($field->relasi_tabel)) {
                        $rule[] = "exists:{$field->relasi_tabel},id";
                    }
                    break;
                default:
                    $rule[] = 'string';
                    $rule[] = 'max:255';
                    break;
            }

            $rules[$col] = $rule;
        }

        return $rules;
    }

    /**
     * Resolusi opsi relational lookup dari database MagKom jika modul memiliki field relation.
     *
     * @return array<string, array<int, array{id: int|string, label: string}>>
     */
    protected function resolveRelationalOptions(ModulDinamis $modul): array
    {
        $options = [];

        foreach ($modul->fields as $field) {
            if ($field->tipe_data === 'relation' && ! empty($field->relasi_tabel)) {
                $table = $field->relasi_tabel;
                $displayCol = $field->relasi_kolom_tampil ?: 'nama';

                if (Schema::hasTable($table)) {
                    try {
                        $rows = DB::table($table)
                            ->select(['id', DB::raw("`{$displayCol}` as label")])
                            ->limit(100)
                            ->get();

                        $options[$field->nama_kolom] = $rows->map(fn ($r) => [
                            'id' => $r->id,
                            'label' => (string) $r->label,
                        ])->all();
                    } catch (\Throwable $e) {
                        $options[$field->nama_kolom] = [];
                    }
                }
            }
        }

        return $options;
    }

    /**
     * Sanitasi sel CSV untuk mencegah Formula Injection (OWASP).
     */
    private function sanitizeCsvCell(mixed $value): string
    {
        if ($value === null) {
            return '';
        }

        $str = (string) $value;

        if ($str !== '' && in_array($str[0], ['=', '+', '-', '@', "\t", "\r"], true)) {
            return "'".$str;
        }

        return $str;
    }
}
