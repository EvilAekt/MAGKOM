<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Enums\RoleEnum;
use App\Http\Controllers\Controller;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\SlotKhusus;
use App\Models\User;
use App\Services\PlacementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PesertaJalurKhususController extends Controller
{
    public function __construct(
        protected PlacementService $placementService
    ) {}

    /**
     * Menampilkan formulir pendaftaran peserta magang jalur khusus (Task D).
     */
    public function create(): View
    {
        $jurusanList = Jurusan::aktif()->orderBy('kategori')->orderBy('nama_jurusan')->get();
        $bidangList = Bidang::aktif()->orderBy('nama_bidang')->get();
        $pembimbingList = User::where('role', RoleEnum::Pembimbing)
            ->where('status_aktif', true)
            ->orderBy('nama')
            ->get();
        $slotKhususList = SlotKhusus::where('aktif', true)->orderBy('bulan_mulai', 'desc')->get();
        $bulanMagangList = BulanMagang::where('aktif', true)->orderBy('bulan_mulai', 'asc')->get();

        return view('superadmin.peserta-khusus.create', [
            'jurusanList' => $jurusanList,
            'bidangList' => $bidangList,
            'pembimbingList' => $pembimbingList,
            'slotKhususList' => $slotKhususList,
            'bulanMagangList' => $bulanMagangList,
        ]);
    }

    /**
     * Menyimpan data peserta magang jalur khusus secara langsung aktif (Task D).
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['nullable', 'string', 'min:8'],
            'nim_nisn' => ['required', 'string', 'max:30', Rule::unique('peserta_profil', 'nim_nisn')],
            'institusi' => ['required', 'string', 'max:150'],
            'jurusan_id' => ['required', 'exists:jurusan,id'],
            'jenjang' => ['required', Rule::in(['smk', 'd3', 's1'])],
            'no_hp' => ['nullable', 'string', 'max:25'],
            'bidang_id' => ['nullable', 'exists:bidang,id'],
            'pembimbing_id' => [
                'required',
                Rule::exists('users', 'id')->where(function ($query) {
                    $query->where('role', RoleEnum::Pembimbing->value)
                        ->where('status_aktif', true);
                }),
            ],
            'bulan_mulai' => ['required', 'string', 'regex:/^\d{4}-\d{2}$/'],
            'durasi_bulan' => ['required', 'integer', 'min:1', 'max:12'],
            'slot_khusus_id' => ['nullable', 'exists:slot_khusus,id'],
            'catatan' => ['nullable', 'string', 'max:500'],
        ], [
            'nim_nisn.unique' => 'NIM / NISN ini sudah terdaftar dalam sistem MAGKOM.',
            'pembimbing_id.exists' => 'Pembimbing yang dipilih tidak valid atau sedang tidak aktif.',
            'bulan_mulai.regex' => 'Format bulan mulai harus YYYY-MM (contoh: 2026-11).',
            'pembimbing_id.required' => 'Pembimbing Lapangan wajib dipilih untuk penempatan peserta.',
        ]);

        $admin = $request->user();
        $pembimbing = User::where('role', RoleEnum::Pembimbing)->findOrFail((int) $validated['pembimbing_id']);
        $bidang = ! empty($validated['bidang_id']) ? Bidang::find((int) $validated['bidang_id']) : null;

        try {
            $penempatan = $this->placementService->tempatkanPesertaBaruManual(
                userData: [
                    'nama' => $validated['nama'],
                    'email' => $validated['email'],
                    'password' => $validated['password'] ?? null,
                ],
                profilData: [
                    'nim_nisn' => $validated['nim_nisn'],
                    'institusi' => $validated['institusi'],
                    'jurusan_id' => (int) $validated['jurusan_id'],
                    'jenjang' => $validated['jenjang'],
                    'no_hp' => $validated['no_hp'] ?? '-',
                ],
                pembimbing: $pembimbing,
                bidang: $bidang,
                bulanMulai: $validated['bulan_mulai'],
                durasiBulan: (int) $validated['durasi_bulan'],
                jalur: 'khusus',
                slotKhususId: ! empty($validated['slot_khusus_id']) ? (int) $validated['slot_khusus_id'] : null,
                kuotaId: null,
                admin: $admin
            );

            $plainPassword = $penempatan->plain_password ?? ($validated['password'] ?? 'Mgk-'.substr(md5($validated['email']), 0, 8).'!1');

            return redirect()->route('superadmin.slot-khusus.index')
                ->with('success', "Akun peserta jalur khusus {$penempatan->peserta?->nama} berhasil dibuat dan langsung berstatus aktif.")
                ->with('akun_baru_dibuat', true)
                ->with('nama_peserta', $penempatan->peserta?->nama)
                ->with('email_peserta', $penempatan->peserta?->email)
                ->with('password_baru', $plainPassword);
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Gagal mendaftarkan peserta jalur khusus: '.$e->getMessage());
        }
    }
}
