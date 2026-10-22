{{-- ==========================================================================
   Header & Atmospheric Hero - Dashboard Pembimbing Lapangan (MagKom Sleman)
   ========================================================================== --}}
<!-- 1. Gentelella Page Title Bar -->
<x-gentelella.page-title
    title="Dashboard Pembimbing"
    description="Pusat komando pemantauan bimbingan magang, reviu tugas & logbook, serta evaluasi rubrik dinas."
>
    <x-slot:actions>
        <a href="{{ route('pembimbing.tugas.create') }}" class="btn btn-sm" style="display: inline-flex; align-items: center; gap: 0.4rem; background: #0641CC; color: #ffffff; border-radius: 8px; font-weight: 700; padding: 0.5rem 1rem; text-decoration: none; box-shadow: 0 2px 6px rgba(6, 65, 204, 0.2);">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            + Beri Tugas Baru
        </a>
        <a href="{{ route('pembimbing.agenda.index') }}" class="btn btn-sm" style="display: inline-flex; align-items: center; gap: 0.4rem; background: #ffffff; color: var(--neutral-700, #334155); border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; font-weight: 600; padding: 0.5rem 1rem; text-decoration: none;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
            Agenda Mentoring
        </a>
    </x-slot:actions>
</x-gentelella.page-title>

<!-- 2. Hero Header Eksekutif Pembimbing Sleman Blue (Clearview Atmospheric DNA) -->
<div style="background: var(--cv-contour, url('data:image/svg+xml,...')) right -40px top -20px / 720px auto no-repeat, radial-gradient(80% 120% at 100% 0%, rgba(7, 81, 255, 0.4), transparent 60%), linear-gradient(160deg, #06318C 0%, #061E5B 100%); border-radius: 14px; padding: 1.75rem 2rem; color: #ffffff; box-shadow: 0 8px 24px -4px rgba(6, 49, 140, 0.35); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1.5rem; position: relative; overflow: hidden;">
    <!-- Visual Accent Circles -->
    <div style="position: absolute; right: -40px; top: -40px; width: 220px; height: 220px; background: rgba(255, 255, 255, 0.06); border-radius: 50%; pointer-events: none;"></div>
    <div style="position: absolute; right: 120px; bottom: -60px; width: 160px; height: 160px; background: rgba(255, 255, 255, 0.04); border-radius: 50%; pointer-events: none;"></div>

    <div style="display: flex; flex-direction: column; gap: 0.5rem; z-index: 1; max-width: 650px;">
        <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
            <span style="background: rgba(255, 255, 255, 0.18); backdrop-filter: blur(8px); padding: 0.3rem 0.85rem; border-radius: 999px; font-size: 0.75rem; font-weight: 600; letter-spacing: 0.05em; text-transform: uppercase;">
                {{ $pembimbingBidang }}
            </span>
            <span style="font-size: 0.8rem; opacity: 0.85;">Diskominfo Kabupaten Sleman</span>
        </div>
        <h1 style="font-size: 1.5rem; font-weight: 700; margin: 0; line-height: 1.25; color: #ffffff;">
            Selamat Datang, {{ auth()->user()->nama }}!
        </h1>
        <p style="font-size: 0.875rem; opacity: 0.9; margin: 0; line-height: 1.5;">
            Pusat kendali operasional bimbingan magang dinas. Pantau kemajuan peserta, evaluasi antrean tugas & logbook harian, serta tetapkan penilaian rubrik secara real-time.
        </p>
        <!-- Quick Action Shortcuts -->
        <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap; margin-top: 0.5rem;">
            <a href="{{ route('pembimbing.tugas.create') }}" style="display: inline-flex; align-items: center; gap: 0.45rem; background: #ffffff; color: #0641CC; padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.8125rem; font-weight: 700; border: none; text-decoration: none; cursor: pointer; box-shadow: 0 2px 6px rgba(0,0,0,0.1); transition: transform 0.2s ease;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                + Beri Tugas Baru
            </a>
            <button type="button" onclick="switchPembimbingTab('tab-antrean')" style="display: inline-flex; align-items: center; gap: 0.45rem; background: rgba(255, 255, 255, 0.18); backdrop-filter: blur(8px); border: 1px solid rgba(255, 255, 255, 0.3); color: #ffffff; padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.8125rem; font-weight: 600; cursor: pointer; transition: background 0.2s ease;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line></svg>
                Reviu Antrean Hari Ini
                @if($logbookPendingCount + $progresPendingCount > 0)
                    <span style="background: #f59e0b; color: #78350f; font-size: 0.7rem; font-weight: 800; padding: 0.1rem 0.45rem; border-radius: 999px;">
                        {{ $logbookPendingCount + $progresPendingCount }}
                    </span>
                @endif
            </button>
            <button type="button" onclick="switchPembimbingTab('tab-evaluasi')" style="display: inline-flex; align-items: center; gap: 0.45rem; background: rgba(255, 255, 255, 0.12); backdrop-filter: blur(8px); border: 1px solid rgba(255, 255, 255, 0.2); color: #ffffff; padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.8125rem; font-weight: 600; cursor: pointer; transition: background 0.2s ease;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                Kelola Nilai Magang
            </button>
            <a href="{{ route('pembimbing.agenda.index') }}" style="display: inline-flex; align-items: center; gap: 0.45rem; background: rgba(255, 255, 255, 0.18); backdrop-filter: blur(8px); border: 1px solid rgba(255, 255, 255, 0.3); color: #ffffff; padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.8125rem; font-weight: 600; text-decoration: none; cursor: pointer; transition: background 0.2s ease;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                Agenda Mentoring
                @if($upcomingAgendaBimbingan->count() > 0)
                    <span style="background: #ffffff; color: #0641CC; font-size: 0.7rem; font-weight: 800; padding: 0.1rem 0.45rem; border-radius: 999px;">
                        {{ $upcomingAgendaBimbingan->count() }}
                    </span>
                @endif
            </a>
        </div>
    </div>

    <!-- Indikator Beban Bimbingan (BR-13) -->
    <div style="background: rgba(255, 255, 255, 0.12); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 12px; padding: 1.25rem; min-width: 260px; z-index: 1;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
            <span style="font-size: 0.8rem; font-weight: 600; opacity: 0.9;">Beban Mentee Aktif</span>
            <span style="font-size: 0.8rem; font-weight: 700; background: rgba(255, 255, 255, 0.25); padding: 0.15rem 0.5rem; border-radius: 6px;">
                {{ $totalMenteeAktif }} / {{ $maxBebanKapasitas }}
            </span>
        </div>
        <!-- Progress Bar Beban -->
        <div style="height: 8px; width: 100%; background: rgba(255, 255, 255, 0.2); border-radius: 999px; overflow: hidden; margin-bottom: 0.5rem;">
            <div style="height: 100%; width: {{ $persentaseKapasitas }}%; background: {{ $persentaseKapasitas >= 90 ? '#ef4444' : ($persentaseKapasitas >= 70 ? '#f59e0b' : '#10b981') }}; border-radius: 999px; transition: width 0.4s ease;"></div>
        </div>
        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.75rem; opacity: 0.85;">
            <span>Kapasitas Terpakai: {{ $persentaseKapasitas }}%</span>
            <button type="button" onclick="switchPembimbingTab('tab-mentee')" style="background: transparent; border: none; padding: 0; color: #ffffff; text-decoration: underline; font-weight: 600; cursor: pointer;">
                Lihat Mentee &rarr;
            </button>
        </div>
    </div>
</div>
