{{-- ==========================================================================
   Tab 5: Agenda Mentoring & Radar Capaian Kompetensi - Dashboard Pembimbing
   ========================================================================== --}}
<!-- 1. RADAR CAPAIAN KOMPETENSI MENTEE BIDANG -->
<x-gentelella.x-panel
    title="Radar Capaian Kompetensi Mentee Bidang"
    subtitle="Indeks performa dan penguasaan kompetensi rata-rata seluruh mentee berdasarkan rubrik penilaian dinas."
>
    <x-slot:actions>
        <a href="{{ route('pembimbing.penilaian.index') }}" style="font-size: 0.825rem; font-weight: 700; color: #0641CC; text-decoration: none; display: inline-flex; align-items: center; gap: 0.35rem;">
            <span>Kelola Lembar Nilai</span>
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
        </a>
    </x-slot:actions>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem;">
        @foreach($competencyRadar as $radar)
            @php
                $skor = $radar['skor_rata'];
                $color = $skor >= 85 ? '#10B981' : ($skor >= 75 ? '#0641CC' : '#F59E0B');
            @endphp
            <div style="background: #F8FAFC; border: 1px solid var(--neutral-200, #E2E8F0); border-radius: 10px; padding: 1rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem;">
                    <span style="font-size: 0.85rem; font-weight: 700; color: var(--neutral-800, #1E293B);">{{ $radar['kriteria'] }}</span>
                    <span style="font-size: 0.85rem; font-weight: 800; color: {{ $color }};">{{ number_format($skor, 1) }} / 100</span>
                </div>
                <div style="font-size: 0.72rem; color: var(--neutral-500, #64748B); margin-bottom: 0.5rem;">Bobot Rubrik: {{ $radar['bobot'] }}%</div>
                <div style="width: 100%; height: 8px; background: #E2E8F0; border-radius: 9999px; overflow: hidden;">
                    <div style="height: 100%; width: {{ min(100, $skor) }}%; background: {{ $color }}; border-radius: 9999px; transition: width 0.4s ease;"></div>
                </div>
            </div>
        @endforeach
    </div>
</x-gentelella.x-panel>

<!-- 2. EARLY WARNING: MENTEE BUTUH PERHATIAN KHUSUS (AT-RISK) -->
<x-gentelella.x-panel
    title="Deteksi Dini Mentee Butuh Perhatian (Early Warning At-Risk)"
    subtitle="Mentee aktif dengan tugas tertunda (overdue) atau belum mengisi logbook kegiatan harian."
>
    @if($menteeAtRiskList->isEmpty())
        <div style="padding: 2.5rem 1.5rem; text-align: center; color: var(--neutral-600, #475569);">
            <div style="width: 44px; height: 44px; border-radius: 50%; background: #ECFDF5; color: #10B981; display: flex; align-items: center; justify-content: center; margin: 0 auto 0.75rem auto;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
            </div>
            <h4 style="font-size: 0.95rem; font-weight: 700; color: var(--neutral-900, #0F172A); margin: 0 0 0.25rem 0;">Seluruh Mentee Sesuai Target</h4>
            <p style="font-size: 0.8rem; margin: 0;">Tidak ada mentee yang mengalami keterlambatan tugas kritis atau absen logbook.</p>
        </div>
    @else
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.85rem;">
                <thead>
                    <tr style="border-bottom: 1px solid #E2E8F0; background: #F8FAFC;">
                        <th style="padding: 0.75rem 1rem; font-weight: 700; color: #475569;">Nama Mentee</th>
                        <th style="padding: 0.75rem 1rem; font-weight: 700; color: #475569;">Institusi</th>
                        <th style="padding: 0.75rem 1rem; font-weight: 700; color: #475569;">Tugas Overdue</th>
                        <th style="padding: 0.75rem 1rem; font-weight: 700; color: #475569;">Logbook Terhenti</th>
                        <th style="padding: 0.75rem 1rem; font-weight: 700; color: #475569;">Tingkat Risiko</th>
                        <th style="padding: 0.75rem 1rem; font-weight: 700; color: #475569; text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($menteeAtRiskList as $risk)
                        @php
                            $m = $risk['mentee'];
                            $badgeBg = $risk['risk_level'] === 'Tinggi' ? '#FEF2F2' : '#FFFBEB';
                            $badgeColor = $risk['risk_level'] === 'Tinggi' ? '#DC2626' : '#D97706';
                            $badgeBorder = $risk['risk_level'] === 'Tinggi' ? '#FECACA' : '#FDE68A';
                        @endphp
                        <tr style="border-bottom: 1px solid #E2E8F0;">
                            <td style="padding: 0.75rem 1rem; font-weight: 700; color: #0F172A;">
                                {{ $m->peserta?->nama }}
                            </td>
                            <td style="padding: 0.75rem 1rem; color: #475569;">
                                {{ $m->peserta?->profil?->institusi ?? '-' }}
                            </td>
                            <td style="padding: 0.75rem 1rem;">
                                @if($risk['overdue_count'] > 0)
                                    <span style="font-weight: 700; color: #DC2626;">{{ $risk['overdue_count'] }} Tugas Terlambat</span>
                                @else
                                    <span style="color: #10B981;">0 Overdue</span>
                                @endif
                            </td>
                            <td style="padding: 0.75rem 1rem;">
                                @if($risk['days_no_logbook'] >= 2)
                                    <span style="font-weight: 700; color: #D97706;">{{ $risk['days_no_logbook'] }} Hari Belum Isi</span>
                                @else
                                    <span style="color: #10B981;">Aktif Mengisi</span>
                                @endif
                            </td>
                            <td style="padding: 0.75rem 1rem;">
                                <span style="padding: 0.2rem 0.6rem; border-radius: 9999px; font-size: 0.72rem; font-weight: 700; background: {{ $badgeBg }}; color: {{ $badgeColor }}; border: 1px solid {{ $badgeBorder }};">
                                    Risiko {{ $risk['risk_level'] }}
                                </span>
                            </td>
                            <td style="padding: 0.75rem 1rem; text-align: right;">
                                <a href="{{ route('pembimbing.peserta.show', $m->id) }}" style="padding: 0.35rem 0.75rem; background: #ffffff; border: 1px solid #CBD5E1; color: #334155; border-radius: 6px; font-size: 0.75rem; font-weight: 600; text-decoration: none;">
                                    Buka Profil &rarr;
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</x-gentelella.x-panel>

<!-- 3. AGENDA MENTORING MENDATANG -->
<x-gentelella.x-panel
    title="Sesi Mentoring & Notulensi Terjadwal"
    subtitle="Jadwal agenda pendampingan terdekat bersama mentee bidang."
>
    <x-slot:actions>
        <a href="{{ route('pembimbing.agenda.index') }}" style="height: 36px; padding: 0 1rem; background: #0641CC; color: #ffffff; border-radius: 8px; font-size: 0.8125rem; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 0.35rem;">
            <span>Kelola Semua Jadwal</span>
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
        </a>
    </x-slot:actions>

    @if($upcomingAgendaBimbingan->isEmpty())
        <div style="padding: 2.5rem 1.5rem; text-align: center; color: var(--neutral-600, #475569);">
            <p style="font-size: 0.85rem; margin: 0 0 0.75rem 0;">Belum ada agenda bimbingan yang dijadwalkan dalam waktu dekat.</p>
            <a href="{{ route('pembimbing.agenda.index') }}" style="font-size: 0.825rem; color: #0641CC; font-weight: 700; text-decoration: underline;">
                + Jadwalkan Sesi Mentoring Baru
            </a>
        </div>
    @else
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem;">
            @foreach($upcomingAgendaBimbingan as $agenda)
                <div style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 10px; padding: 1.25rem;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
                        <span style="font-size: 0.75rem; font-weight: 700; padding: 0.15rem 0.5rem; border-radius: 9999px; background: {{ $agenda->tipe === 'tatap_muka' ? '#F1F5F9' : '#EFF6FF' }}; color: {{ $agenda->tipe === 'tatap_muka' ? '#334155' : '#1E40AF' }};">
                            {{ $agenda->tipe === 'tatap_muka' ? '🏢 Tatap Muka' : '💻 Daring' }}
                        </span>
                        <span style="font-size: 0.75rem; font-weight: 700; color: #0641CC;">
                            {{ $agenda->jam_mulai }} WIB
                        </span>
                    </div>
                    <h4 style="font-size: 0.95rem; font-weight: 700; color: #0F172A; margin: 0 0 0.35rem 0;">{{ $agenda->judul_kegiatan }}</h4>
                    <div style="font-size: 0.8rem; color: #475569; margin-bottom: 0.25rem;">Mentee: <strong>{{ $agenda->penempatan?->peserta?->nama }}</strong></div>
                    <div style="font-size: 0.75rem; color: #64748B;">Tanggal: {{ $agenda->tgl_bimbingan?->translatedFormat('d M Y') }} &bull; {{ $agenda->lokasi_ruang ?? '-' }}</div>
                </div>
            @endforeach
        </div>
    @endif
</x-gentelella.x-panel>
