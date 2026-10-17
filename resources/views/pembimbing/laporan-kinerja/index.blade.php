@extends('layouts.dashboard')

@section('title', 'Laporan Kinerja Mentoring ASN - Portal Pembimbing')

@section('content')
<div class="dashboard-content" style="display: flex; flex-direction: column; gap: 1.5rem;">

    <!-- Breadcrumb & Header Action -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <nav style="font-size: 0.8rem; color: var(--neutral-500); margin-bottom: 0.35rem; display: flex; align-items: center; gap: 0.4rem;">
                <a href="{{ route('pembimbing.dashboard') }}" style="color: var(--neutral-600); text-decoration: none;">Dashboard</a>
                <span>/</span>
                <span style="color: var(--neutral-900); font-weight: 600;">Laporan Kinerja Mentoring</span>
            </nav>
            <h1 style="font-size: 1.5rem; font-weight: 700; color: var(--neutral-900); margin: 0;">
                Laporan Kinerja Pembimbing Lapangan ASN (F-09, F-10)
            </h1>
            <p style="font-size: 0.875rem; color: var(--neutral-600); margin: 0.25rem 0 0 0;">
                Rekapitulasi portofolio bimbingan, jam mentoring kedinasan, dan capaian kompetensi seluruh mentee.
            </p>
        </div>
        <div style="display: flex; gap: 0.75rem;">
            <a href="{{ route('pembimbing.laporan-kinerja.cetak') }}" target="_blank" style="display: inline-flex; align-items: center; gap: 0.5rem; height: 40px; padding: 0 1.25rem; background: #0641CC; color: #ffffff; font-weight: 600; font-size: 0.8125rem; border-radius: 8px; text-decoration: none; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                <span>Cetak Lembar Rekapitulasi (LPJ)</span>
            </a>
        </div>
    </div>

    <!-- 4 KPI Summary Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem;">
        
        <!-- Total Mentee -->
        <div style="background: #ffffff; border-radius: 12px; border: 1px solid var(--neutral-200); padding: 1.25rem; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
                <span style="font-size: 0.8rem; font-weight: 600; color: var(--neutral-600);">Total Mentee Dibimbing</span>
                <div style="width: 32px; height: 32px; border-radius: 8px; background: #eff6ff; color: #0641CC; display: flex; align-items: center; justify-content: center;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                </div>
            </div>
            <div style="font-size: 1.75rem; font-weight: 800; color: var(--neutral-900);">
                {{ $totalMentee }}
            </div>
            <div style="font-size: 0.75rem; color: var(--neutral-500); margin-top: 0.25rem;">
                {{ $totalMenteeAktif }} aktif berjalan &bull; {{ $totalMenteeSelesai }} selesai
            </div>
        </div>

        <!-- Logbook Reviu -->
        <div style="background: #ffffff; border-radius: 12px; border: 1px solid var(--neutral-200); padding: 1.25rem; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
                <span style="font-size: 0.8rem; font-weight: 600; color: var(--neutral-600);">Logbook Direview</span>
                <div style="width: 32px; height: 32px; border-radius: 8px; background: #ecfdf5; color: #059669; display: flex; align-items: center; justify-content: center;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                </div>
            </div>
            <div style="font-size: 1.75rem; font-weight: 800; color: var(--neutral-900);">
                {{ $totalLogbookReviewed }}
            </div>
            <div style="font-size: 0.75rem; color: var(--neutral-500); margin-top: 0.25rem;">
                dari {{ $totalLogbookSubmitted }} catatan logbook terkirim
            </div>
        </div>

        <!-- Tugas Diselesaikan -->
        <div style="background: #ffffff; border-radius: 12px; border: 1px solid var(--neutral-200); padding: 1.25rem; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
                <span style="font-size: 0.8rem; font-weight: 600; color: var(--neutral-600);">Tugas Diselesaikan</span>
                <div style="width: 32px; height: 32px; border-radius: 8px; background: #fef3c7; color: #d97706; display: flex; align-items: center; justify-content: center;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                </div>
            </div>
            <div style="font-size: 1.75rem; font-weight: 800; color: var(--neutral-900);">
                {{ $rasioTugasSelesai }}%
            </div>
            <div style="font-size: 0.75rem; color: var(--neutral-500); margin-top: 0.25rem;">
                {{ $totalTugasSelesai }} selesai dari {{ $totalTugasDiberikan }} total tugas
            </div>
        </div>

        <!-- Rata-rata Nilai -->
        <div style="background: #ffffff; border-radius: 12px; border: 1px solid var(--neutral-200); padding: 1.25rem; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
                <span style="font-size: 0.8rem; font-weight: 600; color: var(--neutral-600);">Rata-rata Nilai Akhir</span>
                <div style="width: 32px; height: 32px; border-radius: 8px; background: #f3e8ff; color: #7e22ce; display: flex; align-items: center; justify-content: center;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                </div>
            </div>
            <div style="font-size: 1.75rem; font-weight: 800; color: var(--neutral-900);">
                {{ $rataRataNilai > 0 ? number_format($rataRataNilai, 2) : '-' }}
            </div>
            <div style="font-size: 0.75rem; color: var(--neutral-500); margin-top: 0.25rem;">
                Kompetensi rubrik resmi Diskominfo
            </div>
        </div>
    </div>

    <!-- Tabel Rekapitulasi Portofolio Bimbingan -->
    <div style="background: #ffffff; border-radius: 14px; border: 1px solid var(--neutral-200); box-shadow: 0 1px 3px rgba(0,0,0,0.05); overflow: hidden;">
        <div style="padding: 1.25rem; border-bottom: 1px solid var(--neutral-200); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
            <div>
                <h3 style="font-size: 1.05rem; font-weight: 700; color: var(--neutral-900); margin: 0;">Rekapitulasi Portofolio Bimbingan</h3>
                <p style="font-size: 0.8rem; color: var(--neutral-600); margin: 0.15rem 0 0 0;">Daftar lengkap capaian dan rekam jejak setiap peserta yang dibimbing.</p>
            </div>
        </div>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.875rem;">
                <thead>
                    <tr style="border-bottom: 1px solid var(--neutral-200); background: #f8fafc; color: var(--neutral-600); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">
                        <th style="padding: 1rem 1.25rem;">Nama Mentee</th>
                        <th style="padding: 1rem 1rem;">Institusi & Jurusan</th>
                        <th style="padding: 1rem 1rem;">Bidang Magang</th>
                        <th style="padding: 1rem 1rem; text-align: center;">Tugas Selesai</th>
                        <th style="padding: 1rem 1rem; text-align: center;">Logbook</th>
                        <th style="padding: 1rem 1rem; text-align: center;">Nilai Akhir</th>
                        <th style="padding: 1rem 1.25rem; text-align: center;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rekapMentees as $item)
                        @php $p = $item['penempatan']; @endphp
                        <tr style="border-bottom: 1px solid var(--neutral-100); transition: background 0.15s ease;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                            <td style="padding: 1rem 1.25rem;">
                                <div style="font-weight: 600; color: var(--neutral-900);">{{ $p->peserta?->nama }}</div>
                                <div style="font-size: 0.75rem; color: var(--neutral-500);">NIM/NISN: {{ $p->peserta?->profil?->nim_nisn ?? '-' }}</div>
                            </td>
                            <td style="padding: 1rem 1rem;">
                                <div style="font-weight: 500; color: var(--neutral-800);">{{ $p->peserta?->profil?->institusi ?? '-' }}</div>
                                <div style="font-size: 0.75rem; color: var(--neutral-500);">{{ $p->peserta?->profil?->jurusan?->nama_jurusan ?? '-' }}</div>
                            </td>
                            <td style="padding: 1rem 1rem;">
                                <span style="font-size: 0.8rem; font-weight: 600; color: #0641CC;">{{ $p->bidang?->nama_bidang ?? 'Umum' }}</span>
                            </td>
                            <td style="padding: 1rem 1rem; text-align: center;">
                                <span style="font-weight: 700; color: var(--neutral-800);">{{ $item['tugas_selesai'] }}</span>
                                <span style="font-size: 0.75rem; color: var(--neutral-500);">/ {{ $item['total_tugas'] }}</span>
                            </td>
                            <td style="padding: 1rem 1rem; text-align: center;">
                                <span style="font-weight: 700; color: #059669;">{{ $item['logbook_reviewed'] }}</span>
                                <span style="font-size: 0.75rem; color: var(--neutral-500);">/ {{ $item['total_logbook'] }}</span>
                            </td>
                            <td style="padding: 1rem 1rem; text-align: center;">
                                @if($item['nilai_akhir'] !== null)
                                    <div style="font-weight: 700; color: #0641CC; font-size: 0.95rem;">
                                        {{ number_format((float) $item['nilai_akhir'], 2) }}
                                    </div>
                                    <span style="font-size: 0.7rem; font-weight: 700; padding: 0.1rem 0.4rem; border-radius: 4px; background: #e0e7ff; color: #0641CC;">
                                        {{ $item['predikat'] }}
                                    </span>
                                @else
                                    <span style="font-size: 0.8rem; color: var(--neutral-400);">-</span>
                                @endif
                            </td>
                            <td style="padding: 1rem 1.25rem; text-align: center;">
                                @if($p->status->value === 'aktif')
                                    <span style="padding: 0.2rem 0.6rem; border-radius: 999px; font-size: 0.75rem; font-weight: 600; background: #ecfdf5; color: #059669;">
                                        Aktif
                                    </span>
                                @elseif($p->status->value === 'selesai')
                                    <span style="padding: 0.2rem 0.6rem; border-radius: 999px; font-size: 0.75rem; font-weight: 600; background: #eff6ff; color: #0641CC;">
                                        Selesai
                                    </span>
                                @else
                                    <span style="padding: 0.2rem 0.6rem; border-radius: 999px; font-size: 0.75rem; font-weight: 600; background: var(--neutral-200); color: var(--neutral-700);">
                                        {{ ucfirst($p->status->value) }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="padding: 2.5rem; text-align: center; color: var(--neutral-500);">
                                Belum ada catatan rekapitulasi peserta bimbingan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
