{{-- ==========================================================================
   Tab 4: Kelulusan & Penyelesaian Magang - Dashboard Pembimbing
   ========================================================================== --}}
<!-- Matriks Status Kelulusan Seluruh Penempatan -->
<x-gentelella.x-panel
    title="Matriks Kelulusan & Penetapan Selesai ({{ $allPenempatanList->count() }} Mentee)"
    subtitle="Pemantauan status magang, penetapan selesai masa bimbingan, dan masa tenggang akun peserta."
>
    <x-slot:actions>
        <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
            <input type="text" onkeyup="filterTableByInput(this, 'table-evaluasi-penilaian')" placeholder="Cari nama mentee..." style="padding: 0.45rem 0.85rem; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; font-size: 0.8rem; outline: none; min-width: 190px;">
            <a href="{{ route('pembimbing.peserta.index') }}" style="font-size: 0.8rem; font-weight: 600; color: #0641CC; text-decoration: none;">
                Kelola Daftar Mentee &rarr;
            </a>
        </div>
    </x-slot:actions>

    <div style="overflow-x: auto;">
        <table id="table-evaluasi-penilaian" style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.875rem;">
            <thead>
                <tr style="border-bottom: 1px solid var(--neutral-200, #E2E8F0); background: #f8fafc; color: var(--neutral-600, #475569); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">
                    <th style="padding: 0.875rem 1.5rem;">Peserta Magang</th>
                    <th style="padding: 0.875rem 1rem;">Periode Magang</th>
                    <th style="padding: 0.875rem 1rem;">Status Magang</th>
                    <th style="padding: 0.875rem 1rem;">Masa Tenggang Akun</th>
                    <th style="padding: 0.875rem 1.5rem; text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($allPenempatanList as $penempatan)
                    @php
                        $isSelesai = $penempatan->status === \App\Enums\StatusPenempatanEnum::Selesai;
                    @endphp
                    <tr style="border-bottom: 1px solid var(--neutral-100, #F1F5F9);">
                        <td style="padding: 1rem 1.5rem;">
                            <div style="font-weight: 600; color: var(--neutral-900, #0F172A);">
                                {{ $penempatan->peserta?->nama }}
                            </div>
                            <div style="font-size: 0.75rem; color: var(--neutral-500, #64748B);">
                                {{ $penempatan->peserta?->profil?->institusi ?? '-' }}
                            </div>
                        </td>
                        <td style="padding: 1rem 1rem; font-size: 0.8125rem; color: var(--neutral-700, #334155);">
                            {{ $penempatan->tgl_mulai?->format('d/m/Y') }} &ndash; {{ $penempatan->tgl_selesai?->format('d/m/Y') }}
                        </td>
                        <td style="padding: 1rem 1rem;">
                            <span style="font-size: 0.75rem; font-weight: 600; padding: 0.2rem 0.6rem; border-radius: 999px; background: {{ $isSelesai ? '#dcfce7' : '#e0e7ff' }}; color: {{ $isSelesai ? '#166534' : '#3730a3' }};">
                                {{ $isSelesai ? 'Selesai Magang' : 'Aktif Bimbingan' }}
                            </span>
                        </td>
                        <td style="padding: 1rem 1rem; font-size: 0.8rem; color: var(--neutral-600, #475569); white-space: nowrap;">
                            @if($penempatan->tgl_nonaktif)
                                <span style="color: #d97706; font-weight: 600;">
                                    Aktif s/d {{ \Carbon\Carbon::parse($penempatan->tgl_nonaktif)->format('d M Y') }}
                                </span>
                            @else
                                <span style="color: var(--neutral-400, #94A3B8);">-</span>
                            @endif
                        </td>
                        <td style="padding: 1rem 1.5rem; text-align: right; white-space: nowrap;">
                            <div style="display: flex; gap: 0.4rem; justify-content: flex-end;">
                                <a href="{{ route('pembimbing.peserta.show', $penempatan->id) }}" style="padding: 0.35rem 0.65rem; background: #0641CC; color: #ffffff; font-size: 0.75rem; font-weight: 600; border-radius: 6px; text-decoration: none;">
                                    Detail Mentee
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="padding: 2rem; text-align: center; color: var(--neutral-500, #64748B);">
                            Belum ada data penempatan mentee.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-gentelella.x-panel>
