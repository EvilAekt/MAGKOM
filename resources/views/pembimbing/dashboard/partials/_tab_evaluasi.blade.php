{{-- ==========================================================================
   Tab 4: Evaluasi Rubrik & Capaian Magang (Kelulusan) - Dashboard Pembimbing
   ========================================================================== --}}
<!-- Matriks Status Penilaian Rubrik Seluruh Penempatan -->
<x-gentelella.x-panel
    title="Matriks Evaluasi Rubrik & Penetapan Selesai ({{ $allPenempatanList->count() }} Mentee)"
    subtitle="Pemberian nilai rubrik kompetensi, predikat huruf (A–E), dan pemantauan masa tenggang akun (BR-10, BR-11)."
>
    <x-slot:actions>
        <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
            <input type="text" onkeyup="filterTableByInput(this, 'table-evaluasi-penilaian')" placeholder="Cari nama mentee..." style="padding: 0.45rem 0.85rem; border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; font-size: 0.8rem; outline: none; min-width: 190px;">
            <a href="{{ route('pembimbing.penilaian.index') }}" style="font-size: 0.8rem; font-weight: 600; color: #0641CC; text-decoration: none;">
                Buka Modul Penilaian Penuh &rarr;
            </a>
        </div>
    </x-slot:actions>

    <div style="overflow-x: auto;">
        <table id="table-evaluasi-penilaian" style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.875rem;">
            <thead>
                <tr style="border-bottom: 1px solid var(--neutral-200, #E2E8F0); background: #f8fafc; color: var(--neutral-600, #475569); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">
                    <th style="padding: 0.875rem 1.5rem;">Peserta Magang</th>
                    <th style="padding: 0.875rem 1rem;">Status Magang</th>
                    <th style="padding: 0.875rem 1rem;">Status Nilai</th>
                    <th style="padding: 0.875rem 1rem;">Skor & Predikat</th>
                    <th style="padding: 0.875rem 1rem;">Masa Tenggang Akun</th>
                    <th style="padding: 0.875rem 1.5rem; text-align: right;">Lembar Nilai PDF</th>
                </tr>
            </thead>
            <tbody>
                @forelse($allPenempatanList as $penempatan)
                    @php
                        $penilaian = $penempatan->penilaian;
                        $statusNilai = $penilaian?->status->value ?? 'belum';
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
                        <td style="padding: 1rem 1rem;">
                            <span style="font-size: 0.75rem; font-weight: 600; padding: 0.2rem 0.6rem; border-radius: 999px; background: {{ $isSelesai ? '#f1f5f9' : '#e0e7ff' }}; color: {{ $isSelesai ? '#475569' : '#3730a3' }};">
                                {{ $isSelesai ? 'Selesai Magang' : 'Aktif Bimbingan' }}
                            </span>
                        </td>
                        <td style="padding: 1rem 1rem;">
                            <span style="font-size: 0.75rem; font-weight: 600; padding: 0.2rem 0.6rem; border-radius: 999px; background: {{ $statusNilai === 'final' ? '#dcfce7' : ($statusNilai === 'draft' ? '#fef3c7' : '#fee2e2') }}; color: {{ $statusNilai === 'final' ? '#166534' : ($statusNilai === 'draft' ? '#92400e' : '#b91c1c') }};">
                                {{ ucfirst($statusNilai) }}
                            </span>
                        </td>
                        <td style="padding: 1rem 1rem;">
                            @if($penilaian && $penilaian->nilai_akhir !== null)
                                <span style="font-weight: 700; color: var(--neutral-900, #0F172A); font-size: 0.9rem;">
                                    {{ number_format((float) $penilaian->nilai_akhir, 2) }}
                                </span>
                                <span style="font-weight: 700; color: #0641CC; margin-left: 0.25rem;">
                                    ({{ $penilaian->predikat ?? '-' }})
                                </span>
                            @else
                                <span style="font-size: 0.75rem; color: var(--neutral-400, #94A3B8);">Belum Dinilai</span>
                            @endif
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
                                <a href="{{ route('pembimbing.penilaian.show', $penempatan->id) }}" style="padding: 0.35rem 0.65rem; background: #0641CC; color: #ffffff; font-size: 0.75rem; font-weight: 600; border-radius: 6px; text-decoration: none;">
                                    Form Nilai
                                </a>
                                @if($statusNilai === 'final')
                                    <a href="{{ route('pembimbing.penilaian.unduh', $penempatan->id) }}" style="padding: 0.35rem 0.65rem; background: rgba(6, 65, 204, 0.08); color: #0641CC; font-size: 0.75rem; font-weight: 600; border-radius: 6px; text-decoration: none;">
                                        Unduh PDF
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="padding: 2rem; text-align: center; color: var(--neutral-500, #64748B);">
                            Belum ada data penempatan mentee.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-gentelella.x-panel>

<!-- Riwayat Revisi Nilai Final (AC-10-3) -->
@if($recentNilaiRevisions->count() > 0)
    <x-gentelella.x-panel
        title="Jejak Audit Revisi Nilai Final (AC-10-3)"
        subtitle="Setiap perubahan pada penilaian yang sudah berstatus Final tercatat dengan alasan resmi dan nilai sebelum vs sesudah."
    >
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.8125rem;">
                <thead>
                    <tr style="border-bottom: 1px solid var(--neutral-200, #E2E8F0); background: #f8fafc; color: var(--neutral-600, #475569); font-size: 0.75rem; text-transform: uppercase;">
                        <th style="padding: 0.75rem 1.5rem;">Waktu</th>
                        <th style="padding: 0.75rem 1rem;">Peserta</th>
                        <th style="padding: 0.75rem 1rem;">Pengubah</th>
                        <th style="padding: 0.75rem 1rem;">Nilai Lama &rarr; Baru</th>
                        <th style="padding: 0.75rem 1.5rem;">Alasan Perubahan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentNilaiRevisions as $rev)
                        @php
                            $lamaAkhir = is_array($rev->nilai_lama) ? ($rev->nilai_lama['nilai_akhir'] ?? '-') : $rev->nilai_lama;
                            $baruAkhir = is_array($rev->nilai_baru) ? ($rev->nilai_baru['nilai_akhir'] ?? '-') : $rev->nilai_baru;
                        @endphp
                        <tr style="border-bottom: 1px solid var(--neutral-100, #F1F5F9);">
                            <td style="padding: 0.85rem 1.5rem; color: var(--neutral-500, #64748B); white-space: nowrap;">
                                {{ $rev->waktu?->format('d/m/Y H:i') ?? '-' }} WIB
                            </td>
                            <td style="padding: 0.85rem 1rem; font-weight: 600; color: var(--neutral-900, #0F172A);">
                                {{ $rev->penilaian?->penempatan?->peserta?->nama ?? 'Peserta' }}
                            </td>
                            <td style="padding: 0.85rem 1rem; color: var(--neutral-700, #334155);">
                                {{ $rev->pengubah?->nama ?? 'Pembimbing' }}
                            </td>
                            <td style="padding: 0.85rem 1rem; font-weight: 600; white-space: nowrap;">
                                <span style="color: #ef4444;">{{ is_numeric($lamaAkhir) ? number_format((float) $lamaAkhir, 2) : $lamaAkhir }}</span>
                                &rarr;
                                <span style="color: #10b981;">{{ is_numeric($baruAkhir) ? number_format((float) $baruAkhir, 2) : $baruAkhir }}</span>
                            </td>
                            <td style="padding: 0.85rem 1.5rem; color: var(--neutral-700, #334155);">
                                {{ $rev->alasan }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-gentelella.x-panel>
@endif
