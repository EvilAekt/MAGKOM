{{-- ==========================================================================
   Tab 3: Monitoring Mentee 360° & Progres Magang - Dashboard Pembimbing
   ========================================================================== --}}
<x-gentelella.x-panel
    title="Direktori Pemantauan Mentee 360° ({{ $totalMenteeAktif }} Peserta Aktif)"
    subtitle="Pantau riwayat penugasan, kedisiplinan logbook harian, dan masa dinas seluruh mentee bimbingan."
>
    <x-slot:actions>
        <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
            <input type="text" onkeyup="filterTableByInput(this, 'table-monitoring-mentee')" placeholder="Cari nama, NIM, atau kampus..." style="padding: 0.45rem 0.85rem; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; font-size: 0.8rem; outline: none; min-width: 210px;">
            <a href="{{ route('pembimbing.peserta.index') }}" style="font-size: 0.8rem; font-weight: 600; color: #0641CC; text-decoration: none;">
                Buka Direktori Penuh &rarr;
            </a>
        </div>
    </x-slot:actions>

    <div style="overflow-x: auto;">
        <table id="table-monitoring-mentee" style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.875rem;">
            <thead>
                <tr style="border-bottom: 1px solid var(--neutral-200, #E2E8F0); background: #f8fafc; color: var(--neutral-600, #475569); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">
                    <th style="padding: 0.875rem 1.5rem;">Peserta</th>
                    <th style="padding: 0.875rem 1rem;">Kontak & Kampus</th>
                    <th style="padding: 0.875rem 1rem;">Timeline Dinas</th>
                    <th style="padding: 0.875rem 1rem;">Ketuntasan Tugas</th>
                    <th style="padding: 0.875rem 1rem;">Status Nilai</th>
                    <th style="padding: 0.875rem 1.5rem; text-align: right;">Aksi Cepat</th>
                </tr>
            </thead>
            <tbody>
                @forelse($menteeAktifList as $mentee)
                    @php
                        $totalTugas = $mentee->tugas->count();
                        $selesaiTugas = $mentee->tugas->where('status', \App\Enums\StatusTugasEnum::Selesai)->count();
                        $persenTugas = $totalTugas > 0 ? (int) round(($selesaiTugas / $totalTugas) * 100) : 0;
                        $statusNilai = $mentee->penilaian?->status->value ?? 'belum';
                    @endphp
                    <tr style="border-bottom: 1px solid var(--neutral-100, #F1F5F9);">
                        <td style="padding: 1rem 1.5rem;">
                            <div style="font-weight: 600; color: var(--neutral-900, #0F172A);">
                                {{ $mentee->peserta?->nama }}
                            </div>
                            <div style="font-size: 0.75rem; color: var(--neutral-500, #64748B);">
                                NIM/NISN: {{ $mentee->peserta?->profil?->nim_nisn ?? '-' }}
                            </div>
                        </td>
                        <td style="padding: 1rem 1rem;">
                            <div style="font-size: 0.8rem; font-weight: 500; color: var(--neutral-800, #1E293B);">
                                {{ $mentee->peserta?->profil?->institusi ?? '-' }}
                            </div>
                            <div style="font-size: 0.75rem; color: var(--neutral-500, #64748B);">
                                {{ $mentee->peserta?->profil?->no_hp ?? $mentee->peserta?->email }}
                            </div>
                        </td>
                        <td style="padding: 1rem 1rem; font-size: 0.8rem; color: var(--neutral-700, #334155); white-space: nowrap;">
                            {{ $mentee->tgl_mulai?->format('d M Y') }} s/d {{ $mentee->tgl_selesai?->format('d M Y') }}
                        </td>
                        <td style="padding: 1rem 1rem;">
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <div style="flex: 1; height: 6px; background: var(--neutral-200, #E2E8F0); border-radius: 999px; overflow: hidden; min-width: 70px;">
                                    <div style="height: 100%; width: {{ $persenTugas }}%; background: #10b981; border-radius: 999px;"></div>
                                </div>
                                <span style="font-size: 0.75rem; font-weight: 600; color: var(--neutral-700, #334155);">
                                    {{ $selesaiTugas }}/{{ $totalTugas }} ({{ $persenTugas }}%)
                                </span>
                            </div>
                        </td>
                        <td style="padding: 1rem 1rem;">
                            <span style="font-size: 0.75rem; font-weight: 600; padding: 0.2rem 0.6rem; border-radius: 999px; background: {{ $statusNilai === 'final' ? '#dcfce7' : ($statusNilai === 'draft' ? '#fef3c7' : '#f1f5f9') }}; color: {{ $statusNilai === 'final' ? '#166534' : ($statusNilai === 'draft' ? '#92400e' : '#475569') }};">
                                {{ ucfirst($statusNilai) }}
                            </span>
                        </td>
                        <td style="padding: 1rem 1.5rem; text-align: right; white-space: nowrap;">
                            <div style="display: flex; gap: 0.4rem; justify-content: flex-end;">
                                <a href="{{ route('pembimbing.peserta.show', $mentee->id) }}" style="padding: 0.35rem 0.65rem; background: rgba(6, 65, 204, 0.08); color: #0641CC; font-size: 0.75rem; font-weight: 600; border-radius: 6px; text-decoration: none;">
                                    Profil 360°
                                </a>
                                <a href="{{ route('pembimbing.tugas.index') }}" style="padding: 0.35rem 0.65rem; background: rgba(16, 185, 129, 0.1); color: #10b981; font-size: 0.75rem; font-weight: 600; border-radius: 6px; text-decoration: none;">
                                    Beri Tugas
                                </a>
                                <a href="{{ route('pembimbing.penilaian.show', $mentee->id) }}" style="padding: 0.35rem 0.65rem; background: #0641CC; color: #ffffff; font-size: 0.75rem; font-weight: 600; border-radius: 6px; text-decoration: none;">
                                    Nilai
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="padding: 2rem; text-align: center; color: var(--neutral-500, #64748B);">
                            Belum ada peserta magang aktif.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-gentelella.x-panel>
