<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pembimbing;

use App\Enums\KompleksitasEnum;
use App\Enums\TipeAuditEnum;
use App\Http\Controllers\Controller;
use App\Models\TugasTemplate;
use App\Services\AuditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TugasTemplateController extends Controller
{
    public function __construct(
        protected AuditService $auditService
    ) {}

    /**
     * Menampilkan daftar koleksi template penugasan pembimbing (PRD F-08).
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $query = TugasTemplate::with('pembimbing')
            ->accessibleBy($user->id)
            ->latest();

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->input('kategori'));
        }

        if ($request->filled('kompleksitas')) {
            $query->where('kompleksitas', $request->input('kompleksitas'));
        }

        if ($request->filled('q')) {
            $search = '%' . trim((string) $request->input('q')) . '%';
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', $search)
                    ->orWhere('deskripsi', 'like', $search)
                    ->orWhere('kategori', 'like', $search);
            });
        }

        $templates = $query->paginate(12)->withQueryString();

        $kategoriList = TugasTemplate::accessibleBy($user->id)
            ->distinct()
            ->pluck('kategori')
            ->filter()
            ->values();

        return view('pembimbing.tugas.template.index', [
            'templates' => $templates,
            'kategoriList' => $kategoriList,
            'kompleksitasCases' => KompleksitasEnum::cases(),
        ]);
    }

    /**
     * Menyimpan template penugasan baru.
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'judul' => ['required', 'string', 'min:3', 'max:150'],
            'deskripsi' => ['nullable', 'string', 'max:2000'],
            'kategori' => ['required', 'string', 'max:50'],
            'kompleksitas' => ['required', Rule::enum(KompleksitasEnum::class)],
            'link_referensi' => ['nullable', 'url', 'max:255'],
            'is_global' => ['nullable', 'boolean'],
        ]);

        $isGlobal = false;
        if ($user->isSuperAdmin() && ! empty($validated['is_global'])) {
            $isGlobal = true;
        }

        $template = TugasTemplate::create([
            'pembimbing_id' => $user->id,
            'judul' => $validated['judul'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'kategori' => $validated['kategori'],
            'kompleksitas' => $validated['kompleksitas'],
            'link_referensi' => $validated['link_referensi'] ?? null,
            'is_global' => $isGlobal,
        ]);

        $this->auditService->catat(
            tipe: TipeAuditEnum::Aksi,
            tabel: 'tugas_template',
            idData: $template->id,
            nilaiLama: null,
            nilaiBaru: $template->toArray(),
            detail: ['aksi' => 'buat_template_tugas']
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Template tugas berhasil disimpan.',
                'data' => $template,
            ], 201);
        }

        return redirect()->route('pembimbing.tugas.template.index')
            ->with('success', "Template tugas '{$template->judul}' berhasil disimpan ke bank penugasan.");
    }

    /**
     * Memperbarui template tugas.
     */
    public function update(Request $request, TugasTemplate $template): RedirectResponse|JsonResponse
    {
        $user = $request->user();

        if ($template->pembimbing_id !== $user->id && ! $user->isSuperAdmin()) {
            abort(403, 'Akses ditolak: Anda hanya dapat mengubah template buatan Anda sendiri.');
        }

        $validated = $request->validate([
            'judul' => ['required', 'string', 'min:3', 'max:150'],
            'deskripsi' => ['nullable', 'string', 'max:2000'],
            'kategori' => ['required', 'string', 'max:50'],
            'kompleksitas' => ['required', Rule::enum(KompleksitasEnum::class)],
            'link_referensi' => ['nullable', 'url', 'max:255'],
        ]);

        $nilaiLama = $template->only(['judul', 'deskripsi', 'kategori', 'kompleksitas', 'link_referensi']);

        $template->update($validated);

        $this->auditService->catat(
            tipe: TipeAuditEnum::Aksi,
            tabel: 'tugas_template',
            idData: $template->id,
            nilaiLama: $nilaiLama,
            nilaiBaru: $validated,
            detail: ['aksi' => 'ubah_template_tugas']
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Template tugas berhasil diperbarui.',
                'data' => $template,
            ]);
        }

        return redirect()->route('pembimbing.tugas.template.index')
            ->with('success', "Template tugas '{$template->judul}' berhasil diperbarui.");
    }

    /**
     * Menghapus template tugas dari pustaka.
     */
    public function destroy(Request $request, TugasTemplate $template): RedirectResponse|JsonResponse
    {
        $user = $request->user();

        if ($template->pembimbing_id !== $user->id && ! $user->isSuperAdmin()) {
            abort(403, 'Akses ditolak: Anda hanya dapat menghapus template buatan Anda sendiri.');
        }

        $nilaiLama = $template->toArray();
        $template->delete();

        $this->auditService->catat(
            tipe: TipeAuditEnum::Aksi,
            tabel: 'tugas_template',
            idData: $template->id,
            nilaiLama: $nilaiLama,
            nilaiBaru: null,
            detail: ['aksi' => 'hapus_template_tugas']
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Template tugas berhasil dihapus.',
            ]);
        }

        return redirect()->route('pembimbing.tugas.template.index')
            ->with('success', 'Template tugas berhasil dihapus.');
    }

    /**
     * Endpoint API JSON untuk picker template pada modal form buat tugas (create assignment).
     */
    public function pickerJson(Request $request): JsonResponse
    {
        $user = $request->user();

        $templates = TugasTemplate::accessibleBy($user->id)
            ->latest()
            ->get(['id', 'judul', 'deskripsi', 'kategori', 'kompleksitas', 'link_referensi', 'is_global']);

        return response()->json([
            'success' => true,
            'data' => $templates,
        ]);
    }
}
