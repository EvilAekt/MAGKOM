{{-- ==========================================================================
   4-Metric Dock & Komposisi Jenjang Pendidikan - Dashboard Pembimbing
   ========================================================================== --}}
<!-- Gentelella Top Tile Count (4 Metrik Utama Pembimbing) -->
<div class="row tile_count">
    <!-- Tile 1: Beban Mentee Aktif -->
    <x-gentelella.tile-stat
        title="Beban Mentee Aktif"
        value="{{ $totalMenteeAktif }} / {{ $maxBebanKapasitas }}"
        icon="users"
        trendText="{{ $persentaseKapasitas }}% Kapasitas"
        trendType="{{ $persentaseKapasitas >= 90 ? 'danger' : ($persentaseKapasitas >= 70 ? 'warning' : 'success') }}"
        subText="Beban aktif / batas kapasitas dinas"
        col="col-md-3 col-sm-6"
    />

    <!-- Tile 2: Reviu Logbook -->
    <x-gentelella.tile-stat
        title="Reviu Logbook"
        value="{{ $logbookPendingCount }}"
        icon="book-open"
        trendText="{{ $logbookPendingCount > 0 ? $logbookPendingCount . ' Perlu Tindak Lanjut' : 'Tuntas' }}"
        trendType="{{ $logbookPendingCount > 0 ? 'warning' : 'success' }}"
        subText="Logbook terkirim menunggu konfirmasi"
        col="col-md-3 col-sm-6"
    />

    <!-- Tile 3: Progres Tugas -->
    <x-gentelella.tile-stat
        title="Progres Tugas"
        value="{{ $progresPendingCount }}"
        icon="clipboard-check"
        trendText="{{ $progresPendingCount > 0 ? $progresPendingCount . ' Perlu Evaluasi' : 'Tuntas' }}"
        trendType="{{ $progresPendingCount > 0 ? 'warning' : 'success' }}"
        subText="Laporan progres menunggu evaluasi"
        col="col-md-3 col-sm-6"
    />

    <!-- Tile 4: Tugas Berjalan -->
    <x-gentelella.tile-stat
        title="Tugas Berjalan"
        value="{{ $tugasAktifCount }}"
        icon="activity"
        trendText="{{ $tugasOverdueCount > 0 ? $tugasOverdueCount . ' Overdue' : 'Jadwal terkendali' }}"
        trendType="{{ $tugasOverdueCount > 0 ? 'danger' : 'success' }}"
        subText="{{ $persenTugasKumulatif }}% tuntas &bull; {{ $tugasSelesaiKumulatif }}/{{ $totalTugasKumulatif }} tugas"
        col="col-md-3 col-sm-6"
    >
        <div class="micro-progress-bar" style="margin-top: 6px;">
            <div class="micro-progress-fill" style="width: {{ $persenTugasKumulatif }}%;"></div>
        </div>
    </x-gentelella.tile-stat>
</div>

<!-- Komposisi Jenjang Pendidikan Mentee Aktif -->
<div style="background: #ffffff; border-radius: 12px; border: 1px solid var(--neutral-200, #E2E8F0); padding: 0.85rem 1.25rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; box-shadow: 0 1px 2px rgba(0,0,0,0.03);">
    <div style="display: flex; align-items: center; gap: 0.6rem;">
        <span style="font-size: 0.8rem; font-weight: 700; color: var(--neutral-700, #334155); text-transform: uppercase; letter-spacing: 0.025em;">
            Komposisi Jenjang Pendidikan:
        </span>
        <span style="font-size: 0.8rem; color: var(--neutral-500, #64748B);">
            Total {{ $totalMenteeAktif }} peserta aktif bimbingan
        </span>
    </div>
    <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
        <div style="display: inline-flex; align-items: center; gap: 0.4rem; background: #eff6ff; color: #1e40af; padding: 0.3rem 0.75rem; border-radius: 999px; font-size: 0.75rem; font-weight: 600;">
            <span style="width: 8px; height: 8px; border-radius: 50%; background: #2563eb;"></span>
            SMK: {{ $jenjangCounts['smk'] ?? 0 }} Siswa
        </div>
        <div style="display: inline-flex; align-items: center; gap: 0.4rem; background: #f5f3ff; color: #5b21b6; padding: 0.3rem 0.75rem; border-radius: 999px; font-size: 0.75rem; font-weight: 600;">
            <span style="width: 8px; height: 8px; border-radius: 50%; background: #7c3aed;"></span>
            D3: {{ $jenjangCounts['d3'] ?? 0 }} Mahasiswa
        </div>
        <div style="display: inline-flex; align-items: center; gap: 0.4rem; background: #ecfdf5; color: #065f46; padding: 0.3rem 0.75rem; border-radius: 999px; font-size: 0.75rem; font-weight: 600;">
            <span style="width: 8px; height: 8px; border-radius: 50%; background: #059669;"></span>
            S1: {{ $jenjangCounts['s1'] ?? 0 }} Mahasiswa
        </div>
    </div>
</div>
