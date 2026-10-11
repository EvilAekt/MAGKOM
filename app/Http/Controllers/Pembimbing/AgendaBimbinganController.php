<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pembimbing;

use App\Enums\StatusPenempatanEnum;
use App\Http\Controllers\Controller;
use App\Models\AgendaBimbingan;
use App\Models\Penempatan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AgendaBimbinganController extends Controller
{
    /**
     * Menampilkan daftar jadwal agenda mentoring dan notulensi bimbingan.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        // Ambil daftar mentee aktif di bawah bimbingan pembimbing untuk opsi penugasan
        $menteeQuery = Penempatan::with(['peserta.profil', 'bidang'])
            ->where('status', StatusPenempatanEnum::Aktif);

        if (! $user->isAdmin() && ! $user->isSuperAdmin()) {
            $menteeQuery->where('pembimbing_id', $user->id);
        }
        $daftarMentee = $menteeQuery->get();

        // Ambil agenda bimbingan
        $agendaQuery = AgendaBimbingan::with(['penempatan.peserta.profil', 'pembimbing']);

        if (! $user->isAdmin() && ! $user->isSuperAdmin()) {
            $agendaQuery->where('pembimbing_id', $user->id);
        }

        $agendaList = $agendaQuery->orderBy('tgl_bimbingan', 'desc')
            ->orderBy('jam_mulai', 'desc')
            ->paginate(15);

        // Ringkasan metrik agenda
        $baseQuery = AgendaBimbingan::query();
        if (! $user->isAdmin() && ! $user->isSuperAdmin()) {
            $baseQuery->where('pembimbing_id', $user->id);
        }
        $totalTerjadwal = (clone $baseQuery)->where('status', 'terjadwal')->count();
        $totalSelesai = (clone $baseQuery)->where('status', 'selesai')->count();

        return view('pembimbing.agenda.index', [
            'agendaList' => $agendaList,
            'daftarMentee' => $daftarMentee,
            'totalTerjadwal' => $totalTerjadwal,
            'totalSelesai' => $totalSelesai,
        ]);
    }

    /**
     * Menyimpan jadwal bimbingan baru dengan proteksi Anti-IDOR (SEC-03, SEC-11).
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'penempatan_id' => ['required', 'exists:penempatan,id'],
            'judul_kegiatan' => ['required', 'string', 'max:150'],
            'tipe' => ['required', 'in:tatap_muka,daring'],
            'tgl_bimbingan' => ['required', 'date'],
            'jam_mulai' => ['required', 'string', 'max:10'],
            'lokasi_ruang' => ['nullable', 'string', 'max:150'],
            'notulensi_arahan' => ['nullable', 'string'],
        ]);

        // Verifikasi Anti-IDOR: Pembimbing hanya boleh menjadwalkan mentee miliknya
        $penempatan = Penempatan::findOrFail($validated['penempatan_id']);
        if (! $user->isAdmin() && ! $user->isSuperAdmin()) {
            if ($penempatan->pembimbing_id !== $user->id) {
                abort(403, 'Akses Ditolak: Peserta ini bukan merupakan mentee di bawah bimbingan Anda.');
            }
        }

        AgendaBimbingan::create([
            'penempatan_id' => $penempatan->id,
            'pembimbing_id' => $penempatan->pembimbing_id ?? $user->id,
            'judul_kegiatan' => $validated['judul_kegiatan'],
            'tipe' => $validated['tipe'],
            'tgl_bimbingan' => $validated['tgl_bimbingan'],
            'jam_mulai' => $validated['jam_mulai'],
            'lokasi_ruang' => $validated['lokasi_ruang'] ?? ($validated['tipe'] === 'daring' ? 'Google Meet / Zoom' : 'Ruang Kerja Bidang'),
            'notulensi_arahan' => $validated['notulensi_arahan'],
            'status' => 'terjadwal',
        ]);

        return redirect()->route('pembimbing.agenda.index')->with('success', 'Agenda bimbingan mentoring berhasil dijadwalkan.');
    }

    /**
     * Memperbarui notulensi atau status agenda bimbingan.
     */
    public function update(Request $request, AgendaBimbingan $agenda): RedirectResponse
    {
        $user = $request->user();

        // Verifikasi Anti-IDOR
        if (! $user->isAdmin() && ! $user->isSuperAdmin()) {
            if ($agenda->pembimbing_id !== $user->id) {
                abort(403, 'Akses Ditolak: Anda tidak memiliki otoritas atas agenda bimbingan ini.');
            }
        }

        $validated = $request->validate([
            'judul_kegiatan' => ['sometimes', 'required', 'string', 'max:150'],
            'tipe' => ['sometimes', 'required', 'in:tatap_muka,daring'],
            'tgl_bimbingan' => ['sometimes', 'required', 'date'],
            'jam_mulai' => ['sometimes', 'required', 'string', 'max:10'],
            'lokasi_ruang' => ['nullable', 'string', 'max:150'],
            'notulensi_arahan' => ['nullable', 'string'],
            'status' => ['required', 'in:terjadwal,selesai,dibatalkan'],
        ]);

        $agenda->update($validated);

        return redirect()->route('pembimbing.agenda.index')->with('success', 'Data agenda dan notulensi bimbingan berhasil diperbarui.');
    }

    /**
     * Membatalkan atau menghapus agenda bimbingan.
     */
    public function destroy(Request $request, AgendaBimbingan $agenda): RedirectResponse
    {
        $user = $request->user();

        // Verifikasi Anti-IDOR
        if (! $user->isAdmin() && ! $user->isSuperAdmin()) {
            if ($agenda->pembimbing_id !== $user->id) {
                abort(403, 'Akses Ditolak: Anda tidak memiliki otoritas atas agenda bimbingan ini.');
            }
        }

        $agenda->delete();

        return redirect()->route('pembimbing.agenda.index')->with('success', 'Agenda bimbingan berhasil dihapus.');
    }
}
