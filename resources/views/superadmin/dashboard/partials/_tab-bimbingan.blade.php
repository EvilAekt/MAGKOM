<div id="tab-bimbingan" class="tab-content-panel" role="tabpanel" aria-labelledby="btn-tab-bimbingan">
    
    <!-- 4 Kartu Metrik Bimbingan -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
        <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 10px; padding: 1rem 1.25rem; border-left: 4px solid #2563EB;">
            <span style="font-size: 0.75rem; color: var(--neutral-600); font-weight: 600; display: block;">Total Tugas Aktif</span>
            <span style="font-size: 1.5rem; font-weight: 800; color: var(--neutral-900); display: block; margin-top: 0.25rem;">{{ number_format($totalTugasAktif) }}</span>
            <span style="font-size: 0.6875rem; color: #2563EB; font-weight: 600;">Sedang & belum dikerjakan</span>
        </div>

        <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 10px; padding: 1rem 1.25rem; border-left: 4px solid #D97706;">
            <span style="font-size: 0.75rem; color: var(--neutral-600); font-weight: 600; display: block;">Logbook Pending Reviu</span>
            <span style="font-size: 1.5rem; font-weight: 800; color: var(--neutral-900); display: block; margin-top: 0.25rem;">{{ number_format($totalLogbookPending) }}</span>
            <span style="font-size: 0.6875rem; color: #D97706; font-weight: 600;">Menunggu catatan pembimbing</span>
        </div>

        <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 10px; padding: 1rem 1.25rem; border-left: 4px solid #16A34A;">
            <span style="font-size: 0.75rem; color: var(--neutral-600); font-weight: 600; display: block;">Penilaian Final Selesai</span>
            <span style="font-size: 1.5rem; font-weight: 800; color: var(--neutral-900); display: block; margin-top: 0.25rem;">{{ number_format($totalPenilaianSelesai) }}</span>
            <span style="font-size: 0.6875rem; color: #16A34A; font-weight: 600;">Lembar nilai siap diunduh</span>
        </div>

        <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 10px; padding: 1rem 1.25rem; border-left: 4px solid #7C3AED;">
            <span style="font-size: 0.75rem; color: var(--neutral-600); font-weight: 600; display: block;">Staf Pembimbing Aktif</span>
            <span style="font-size: 1.5rem; font-weight: 800; color: var(--neutral-900); display: block; margin-top: 0.25rem;">{{ number_format($totalPembimbing) }}</span>
            <span style="font-size: 0.6875rem; color: #7C3AED; font-weight: 600;">Tersedia di Diskominfo</span>
        </div>
    </div>

    <!-- BARIS KARTU PENGAWASAN KEDISIPLINAN TUGAS (PRD F-08 & BR-16) -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem;">
        <!-- Tugas Terlambat -->
        <div style="background: var(--neutral-0); border: 1px solid {{ $totalTugasTerlambat > 0 ? '#FECACA' : 'var(--neutral-400)' }}; border-radius: 10px; padding: 1rem 1.25rem; display: flex; align-items: center; justify-content: space-between; border-left: 4px solid #DC2626;">
            <div>
                <span style="font-size: 0.75rem; color: var(--neutral-600); font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em;">Tugas Terlambat (Overdue)</span>
                <span style="font-size: 1.5rem; font-weight: 800; color: {{ $totalTugasTerlambat > 0 ? '#DC2626' : 'var(--neutral-900)' }}; display: block; margin-top: 0.25rem;">
                    {{ $totalTugasTerlambat }} Tugas
                </span>
                <span style="font-size: 0.6875rem; color: {{ $totalTugasTerlambat > 0 ? '#DC2626' : '#16A34A' }}; font-weight: 600;">
                    {{ $totalTugasTerlambat > 0 ? 'Melewati deadline tanpa status selesai' : 'Semua tugas berjalan tepat waktu' }}
                </span>
            </div>
            <div style="width: 40px; height: 40px; border-radius: 8px; background: #FEE2E2; color: #DC2626; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
            </div>
        </div>

        <!-- Tugas Mendekati Tenggat -->
        <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 10px; padding: 1rem 1.25rem; display: flex; align-items: center; justify-content: space-between; border-left: 4px solid #D97706;">
            <div>
                <span style="font-size: 0.75rem; color: var(--neutral-600); font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em;">Mendekati Deadline (≤ 3 Hari)</span>
                <span style="font-size: 1.5rem; font-weight: 800; color: var(--neutral-900); display: block; margin-top: 0.25rem;">
                    {{ $totalTugasMendekatiDeadline }} Tugas
                </span>
                <span style="font-size: 0.6875rem; color: #D97706; font-weight: 600;">
                    Tenggat mendekati sesuai aturan BR-16
                </span>
            </div>
            <div style="width: 40px; height: 40px; border-radius: 8px; background: #FEF3C7; color: #D97706; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
            </div>
        </div>
    </div>

    <!-- Matriks Beban Pembimbing Lapangan (BR-13) -->
    <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.25rem 1.5rem; box-shadow: var(--shadow-subtle);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.125rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--neutral-300); flex-wrap: wrap; gap: 0.5rem;">
            <div>
                <h3 style="font-size: 1rem; font-weight: 700; color: var(--neutral-900); margin: 0;">
                    Matriks Utilisasi Beban Pembimbing Lapangan (BR-13)
                </h3>
                <p style="font-size: 0.75rem; color: var(--neutral-600); margin: 0.125rem 0 0 0;">
                    Batas maksimal beban mentee per pembimbing lapangan: <strong>{{ $maksBeban ? $maksBeban . ' Orang' : 'Tanpa Batas' }}</strong>
                </p>
            </div>
            <div style="display: flex; gap: 0.5rem;">
                <a href="{{ route('admin.penempatan.index') }}" style="font-size: 0.75rem; font-weight: 700; color: var(--blue-600); text-decoration: underline;">
                    Kelola Penempatan Peserta &rarr;
                </a>
                <span style="color: var(--neutral-400);">&bull;</span>
                <a href="{{ route('superadmin.monitoring.index') }}" style="font-size: 0.75rem; font-weight: 700; color: var(--blue-600); text-decoration: underline;">
                    Monitoring Peserta &rarr;
                </a>
            </div>
        </div>

        @if($pembimbingList->isEmpty())
            <p style="font-size: 0.8125rem; color: var(--neutral-600); margin: 0;">Belum ada staf pembimbing aktif yang terdaftar di basis data.</p>
        @else
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem;">
                @foreach($pembimbingList as $p)
                    @php
                        $menteeCount = $p->mentee_aktif_count;
                        $maks = $maksBeban ?: 10;
                        $persenBeban = min(100, round(($menteeCount / $maks) * 100));
                        $isOverload = $maksBeban && $menteeCount >= $maksBeban;
                    @endphp
                    <div style="background: var(--neutral-150); border: 1px solid var(--neutral-300); border-radius: 8px; padding: 0.875rem 1rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.25rem;">
                            <span style="font-weight: 700; font-size: 0.875rem; color: var(--neutral-900);">{{ $p->nama }}</span>
                            <span style="font-size: 0.75rem; font-weight: 700; color: {{ $isOverload ? '#DC2626' : '#2563EB' }};">
                                {{ $menteeCount }} Mentee
                            </span>
                        </div>
                        <span style="font-size: 0.6875rem; color: var(--neutral-600); display: block; margin-bottom: 0.5rem;">{{ $p->email }}</span>
                        <div style="width: 100%; height: 6px; background: var(--neutral-300); border-radius: 999px; overflow: hidden;">
                            <div style="width: {{ $persenBeban }}%; height: 100%; background: {{ $isOverload ? '#DC2626' : '#2563EB' }}; border-radius: 999px;"></div>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.375rem; font-size: 0.6875rem;">
                            <span style="color: {{ $isOverload ? '#DC2626' : '#166534' }}; font-weight: 600;">
                                {{ $isOverload ? 'Kapasitas Penuh' : 'Kapasitas Tersedia' }}
                            </span>
                            <span style="color: var(--neutral-500);">Kapasitas: {{ $maksBeban ?: '∞' }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- PELACAK PESERTA MENDEKATI BATAS SELESAI (OFF-BOARDING TRACKER <= 14 HARI) -->
    <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.25rem 1.5rem; box-shadow: var(--shadow-subtle);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.875rem; flex-wrap: wrap; gap: 0.5rem;">
            <div>
                <h4 style="font-size: 0.9375rem; font-weight: 700; color: var(--neutral-900); margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                    <span>Pelacak Penyelesaian Magang Terdekat (Off-boarding Tracker)</span>
                    <span style="font-size: 0.6875rem; font-weight: 700; background: #DBEAFE; color: #1E40AF; padding: 0.15rem 0.5rem; border-radius: 999px;">
                        ≤ 14 Hari
                    </span>
                </h4>
                <p style="font-size: 0.75rem; color: var(--neutral-600); margin: 0.125rem 0 0 0;">
                    Peserta yang akan menyelesaikan masa dinas dalam 2 pekan ke depan untuk persiapan penilaian akhir dan Lembar Nilai
                </p>
            </div>
            <a href="{{ route('superadmin.monitoring.index') }}" style="font-size: 0.75rem; font-weight: 700; color: var(--blue-600); text-decoration: underline;">
                Lihat Semua Peserta Aktif &rarr;
            </a>
        </div>

        @if($pesertaMendekatiSelesai->isEmpty())
            <div style="padding: 1.5rem 1rem; text-align: center; color: var(--neutral-600); background: #F8FAFC; border-radius: 8px; border: 1px dashed var(--neutral-300);">
                <p style="margin: 0; font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800);">Tidak ada peserta yang mendekati akhir masa magang dalam 14 hari ke depan.</p>
            </div>
        @else
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 0.8125rem; text-align: left;">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--neutral-300); color: var(--neutral-600); font-size: 0.75rem; text-transform: uppercase;">
                            <th style="padding: 0.5rem 0.75rem;">Nama Peserta</th>
                            <th style="padding: 0.5rem 0.75rem;">Bidang Penempatan</th>
                            <th style="padding: 0.5rem 0.75rem;">Pembimbing</th>
                            <th style="padding: 0.5rem 0.75rem;">Tanggal Selesai</th>
                            <th style="padding: 0.5rem 0.75rem; text-align: right;">Sisa Hari</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pesertaMendekatiSelesai as $pen)
                            @php
                                $sisaHari = (int) now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($pen->tgl_selesai)->startOfDay(), false);
                            @endphp
                            <tr style="border-bottom: 1px solid var(--neutral-200);">
                                <td style="padding: 0.5rem 0.75rem; font-weight: 700; color: var(--neutral-900);">
                                    {{ $pen->peserta?->nama ?? 'Peserta' }}
                                </td>
                                <td style="padding: 0.5rem 0.75rem; color: var(--neutral-700);">
                                    {{ $pen->bidang?->nama_bidang ?? 'Diskominfo' }}
                                </td>
                                <td style="padding: 0.5rem 0.75rem; color: var(--neutral-700);">
                                    {{ $pen->pembimbing?->nama ?? '-' }}
                                </td>
                                <td style="padding: 0.5rem 0.75rem; color: var(--neutral-800); font-weight: 600;">
                                    {{ \Carbon\Carbon::parse($pen->tgl_selesai)->format('d M Y') }}
                                </td>
                                <td style="padding: 0.5rem 0.75rem; text-align: right;">
                                    <span style="font-weight: 700; font-size: 0.75rem; padding: 0.15rem 0.5rem; border-radius: 999px; {{ $sisaHari <= 7 ? 'background: #FEE2E2; color: #DC2626;' : 'background: #FEF3C7; color: #D97706;' }}">
                                        {{ $sisaHari > 0 ? $sisaHari . ' Hari Lagi' : 'Hari Ini' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- Ringkasan Kepatuhan Kebijakan MagKom -->
    <div style="background: #F8FAFC; border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.25rem 1.5rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
        <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
            <div style="padding: 0.5rem 0.75rem; background: #FFFFFF; border: 1px solid var(--neutral-300); border-radius: 8px;">
                <span style="font-size: 0.6875rem; color: var(--neutral-600); display: block;">Toleransi Logbook Mundur</span>
                <span style="font-size: 0.875rem; font-weight: 700; color: var(--neutral-900);">Maks. {{ $batasHariLogbookMundur }} Hari (BR-09)</span>
            </div>
            <div style="padding: 0.5rem 0.75rem; background: #FFFFFF; border: 1px solid var(--neutral-300); border-radius: 8px;">
                <span style="font-size: 0.6875rem; color: var(--neutral-600); display: block;">Masa Tenggang Lembar Nilai</span>
                <span style="font-size: 0.875rem; font-weight: 700; color: var(--neutral-900);">{{ $masaTenggangUnduhNilaiHari }} Hari Pasca Nonaktif (BR-12)</span>
            </div>
            <div style="padding: 0.5rem 0.75rem; background: #FFFFFF; border: 1px solid var(--neutral-300); border-radius: 8px;">
                <span style="font-size: 0.6875rem; color: var(--neutral-600); display: block;">Passing Grade Predikat</span>
                <span style="font-size: 0.875rem; font-weight: 700; color: var(--neutral-900);">Minimal Nilai {{ $passingGradeNilai }} (BR-11)</span>
            </div>
        </div>
        <a href="{{ route('superadmin.pengaturan.index') }}" style="font-size: 0.75rem; font-weight: 700; color: var(--blue-600); text-decoration: underline;">
            Sesuaikan Parameter Sistem &rarr;
        </a>
    </div>

</div>
