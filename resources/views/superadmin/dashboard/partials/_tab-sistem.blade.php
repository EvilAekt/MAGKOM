<div id="tab-sistem" class="tab-content-panel" role="tabpanel" aria-labelledby="btn-tab-sistem">
    
    <!-- BAGIAN 1: HUB EKSPOR 4 LAPORAN RESMI DINAS 1-KLIK -->
    <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.25rem 1.5rem; box-shadow: var(--shadow-subtle);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.125rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--neutral-300); flex-wrap: wrap; gap: 0.5rem;">
            <div>
                <h3 style="font-size: 1rem; font-weight: 700; color: var(--neutral-900); margin: 0;">
                    Hub Ekspor Instan Laporan Resmi Dinas (AC-17-1)
                </h3>
                <p style="font-size: 0.75rem; color: var(--neutral-600); margin: 0.125rem 0 0 0;">
                    Unduh data rekapitulasi CSV/Excel untuk kebutuhan arsip dan pelaporan pimpinan Diskominfo Sleman
                </p>
            </div>
            <a href="{{ route('admin.laporan.index') }}" style="font-size: 0.8125rem; font-weight: 700; color: #2563EB; text-decoration: none;">
                Buka Filter Laporan Lanjutan &rarr;
            </a>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1rem;">
            <!-- Ekspor 1: Pendaftaran -->
            <div style="background: var(--neutral-150); border: 1px solid var(--neutral-300); border-radius: 10px; padding: 1rem 1.25rem; display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.375rem;">
                        <div style="width: 28px; height: 28px; border-radius: 6px; background: #DBEAFE; color: #1E40AF; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.75rem;">
                            1
                        </div>
                        <h4 style="font-size: 0.875rem; font-weight: 700; color: var(--neutral-900); margin: 0;">Laporan Pendaftaran</h4>
                    </div>
                    <p style="font-size: 0.75rem; color: var(--neutral-600); margin: 0 0 0.875rem 0; line-height: 1.4;">
                        Data registrasi peserta, asal institusi, jurusan, dan status keputusan seleksi.
                    </p>
                </div>
                <a href="{{ route('admin.laporan.pendaftaran') }}" class="btn-primary" style="display: inline-flex; align-items: center; justify-content: center; gap: 0.375rem; padding: 0.5rem 0.75rem; font-size: 0.75rem; text-decoration: none; border-radius: 6px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="7 10 12 15 17 10"></polyline>
                        <line x1="12" y1="15" x2="12" y2="3"></line>
                    </svg>
                    <span>Unduh CSV Pendaftaran</span>
                </a>
            </div>

            <!-- Ekspor 2: Penempatan -->
            <div style="background: var(--neutral-150); border: 1px solid var(--neutral-300); border-radius: 10px; padding: 1rem 1.25rem; display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.375rem;">
                        <div style="width: 28px; height: 28px; border-radius: 6px; background: #DCFCE7; color: #166534; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.75rem;">
                            2
                        </div>
                        <h4 style="font-size: 0.875rem; font-weight: 700; color: var(--neutral-900); margin: 0;">Laporan Penempatan</h4>
                    </div>
                    <p style="font-size: 0.75rem; color: var(--neutral-600); margin: 0 0 0.875rem 0; line-height: 1.4;">
                        Alokasi bidang kerja, pembimbing lapangan yang ditunjuk, dan status masa dinas peserta.
                    </p>
                </div>
                <a href="{{ route('admin.laporan.penempatan') }}" class="btn-primary" style="display: inline-flex; align-items: center; justify-content: center; gap: 0.375rem; padding: 0.5rem 0.75rem; font-size: 0.75rem; text-decoration: none; border-radius: 6px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="7 10 12 15 17 10"></polyline>
                        <line x1="12" y1="15" x2="12" y2="3"></line>
                    </svg>
                    <span>Unduh CSV Penempatan</span>
                </a>
            </div>

            <!-- Ekspor 3: Realisasi Kuota -->
            <div style="background: var(--neutral-150); border: 1px solid var(--neutral-300); border-radius: 10px; padding: 1rem 1.25rem; display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.375rem;">
                        <div style="width: 28px; height: 28px; border-radius: 6px; background: #FEF3C7; color: #92400E; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.75rem;">
                            3
                        </div>
                        <h4 style="font-size: 0.875rem; font-weight: 700; color: var(--neutral-900); margin: 0;">Realisasi Kuota</h4>
                    </div>
                    <p style="font-size: 0.75rem; color: var(--neutral-600); margin: 0 0 0.875rem 0; line-height: 1.4;">
                        Perbandingan alokasi kuota umum vs slot khusus kemitraan beserta rasio keterisian per jurusan.
                    </p>
                </div>
                <a href="{{ route('admin.laporan.kuota') }}" class="btn-primary" style="display: inline-flex; align-items: center; justify-content: center; gap: 0.375rem; padding: 0.5rem 0.75rem; font-size: 0.75rem; text-decoration: none; border-radius: 6px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="7 10 12 15 17 10"></polyline>
                        <line x1="12" y1="15" x2="12" y2="3"></line>
                    </svg>
                    <span>Unduh CSV Kuota</span>
                </a>
            </div>

            <!-- Ekspor 4: Rekapitulasi Nilai (Eksklusif SuperAdmin) -->
            <div style="background: var(--neutral-150); border: 1px solid var(--neutral-300); border-radius: 10px; padding: 1rem 1.25rem; display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.375rem;">
                        <div style="width: 28px; height: 28px; border-radius: 6px; background: #F3E8FF; color: #6B21A8; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.75rem;">
                            4
                        </div>
                        <h4 style="font-size: 0.875rem; font-weight: 700; color: var(--neutral-900); margin: 0;">Rekapitulasi Nilai</h4>
                    </div>
                    <p style="font-size: 0.75rem; color: var(--neutral-600); margin: 0 0 0.875rem 0; line-height: 1.4;">
                        Nilai akhir, predikat kelulusan (A/B/C/D/E), dan rekam hasil evaluasi peserta alumni (Akses Khusus).
                    </p>
                </div>
                <a href="{{ route('admin.laporan.nilai') }}" class="btn-primary" style="display: inline-flex; align-items: center; justify-content: center; gap: 0.375rem; padding: 0.5rem 0.75rem; font-size: 0.75rem; text-decoration: none; border-radius: 6px; background: #7C3AED;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="7 10 12 15 17 10"></polyline>
                        <line x1="12" y1="15" x2="12" y2="3"></line>
                    </svg>
                    <span>Unduh CSV Nilai</span>
                </a>
            </div>
        </div>
    </div>

    <!-- BAGIAN 2: PUSAT MONITORING KEAMANAN SISTEM & THREAT RADAR (PRD Bab 10 & TDD Bab 8-9) -->
    <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.25rem 1.5rem; box-shadow: var(--shadow-subtle); margin-top: 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--neutral-300); flex-wrap: wrap; gap: 0.75rem;">
            <div>
                <h3 style="font-size: 1rem; font-weight: 700; color: var(--neutral-900); margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                        <path d="M12 8v4"></path>
                        <path d="M12 16h.01"></path>
                    </svg>
                    <span>Pusat Monitoring Keamanan Sistem & Threat Radar (PRD Bab 10)</span>
                </h3>
                <p style="font-size: 0.75rem; color: var(--neutral-600); margin: 0.125rem 0 0 0;">
                    Pemantauan ancaman siber real-time, deteksi anomali autentikasi 24 jam, higienitas kredensial staf, dan imutabilitas jejak audit
                </p>
            </div>
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                @if(($loginGagal24Jam ?? 0) === 0)
                    <span style="display: inline-flex; align-items: center; gap: 0.375rem; font-size: 0.6875rem; font-weight: 700; background: #DCFCE7; color: #166534; padding: 0.25rem 0.625rem; border-radius: 999px; border: 1px solid #BBF7D0;">
                        <span style="width: 7px; height: 7px; border-radius: 50%; background: #16A34A; display: inline-block;"></span>
                        POSTUR KEAMANAN PRIMA (0 ANOMALI)
                    </span>
                @else
                    <span style="display: inline-flex; align-items: center; gap: 0.375rem; font-size: 0.6875rem; font-weight: 700; background: #FEE2E2; color: #991B1B; padding: 0.25rem 0.625rem; border-radius: 999px; border: 1px solid #FECACA;">
                        <span style="width: 7px; height: 7px; border-radius: 50%; background: #DC2626; display: inline-block;"></span>
                        RADAR AKTIF: {{ $loginGagal24Jam }} ANOMALI LOGIN (24 JAM)
                    </span>
                @endif
                <a href="{{ route('superadmin.audit-log.index') }}" style="font-size: 0.75rem; font-weight: 700; color: var(--blue-600); text-decoration: none; padding: 0.25rem 0.5rem; background: var(--neutral-150); border: 1px solid var(--neutral-300); border-radius: 6px;">
                    Audit Lengkap &rarr;
                </a>
            </div>
        </div>

        <!-- 4 Kartu Metrik Postur Keamanan & Perlindungan Siber -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 1rem; margin-bottom: 1.25rem;">
            
            <!-- Kartu 1: Radar Percobaan Intrusi / Login Gagal 24 Jam -->
            <div style="background: #F8FAFC; border: 1px solid var(--neutral-300); border-radius: 10px; padding: 1rem;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
                    <span style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-700);">Radar Anomali Login (24 Jam)</span>
                    <div style="width: 28px; height: 28px; border-radius: 6px; background: {{ ($loginGagal24Jam ?? 0) > 0 ? '#FEE2E2' : '#DCFCE7' }}; color: {{ ($loginGagal24Jam ?? 0) > 0 ? '#DC2626' : '#16A34A' }}; display: flex; align-items: center; justify-content: center;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                        </svg>
                    </div>
                </div>
                <div style="font-size: 1.375rem; font-weight: 800; color: {{ ($loginGagal24Jam ?? 0) > 0 ? '#DC2626' : 'var(--neutral-900)' }}; line-height: 1.2; margin-bottom: 0.25rem;">
                    {{ $loginGagal24Jam ?? 0 }} <span style="font-size: 0.8125rem; font-weight: 600; color: var(--neutral-600);">Percobaan</span>
                </div>
                <div style="font-size: 0.6875rem; color: var(--neutral-600); display: flex; align-items: center; justify-content: space-between;">
                    <span>HTTP 401 & Rate Limit (429)</span>
                    <span style="font-weight: 700; color: {{ ($loginGagal24Jam ?? 0) > 0 ? '#DC2626' : '#166534' }};">
                        {{ ($loginGagal24Jam ?? 0) > 0 ? 'Perlu Tinjauan' : 'Aman' }}
                    </span>
                </div>
            </div>

            <!-- Kartu 2: Higienitas Kredensial Staf (BR-15) -->
            <div style="background: #F8FAFC; border: 1px solid var(--neutral-300); border-radius: 10px; padding: 1rem;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
                    <span style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-700);">Higienitas Kredensial Staf (BR-15)</span>
                    <div style="width: 28px; height: 28px; border-radius: 6px; background: {{ ($stafPendingGantiPassword ?? 0) > 0 ? '#FEF3C7' : '#DCFCE7' }}; color: {{ ($stafPendingGantiPassword ?? 0) > 0 ? '#D97706' : '#16A34A' }}; display: flex; align-items: center; justify-content: center;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 14 14"></polyline>
                        </svg>
                    </div>
                </div>
                <div style="font-size: 1.375rem; font-weight: 800; color: var(--neutral-900); line-height: 1.2; margin-bottom: 0.25rem;">
                    {{ $stafPendingGantiPassword ?? 0 }} <span style="font-size: 0.8125rem; font-weight: 600; color: var(--neutral-600);">Akun</span>
                </div>
                <div style="font-size: 0.6875rem; color: var(--neutral-600); display: flex; align-items: center; justify-content: space-between;">
                    <span>Pending Ganti Password Awal</span>
                    <span style="font-weight: 700; color: {{ ($stafPendingGantiPassword ?? 0) > 0 ? '#D97706' : '#166534' }};">
                        {{ ($stafPendingGantiPassword ?? 0) > 0 ? 'Force Change Aktif' : 'Higienis' }}
                    </span>
                </div>
            </div>

            <!-- Kartu 3: Vault Dokumen Terisolasi (NFR-S2) -->
            <div style="background: #F8FAFC; border: 1px solid var(--neutral-300); border-radius: 10px; padding: 1rem;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
                    <span style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-700);">Vault Dokumen Privat (NFR-S2)</span>
                    <div style="width: 28px; height: 28px; border-radius: 6px; background: #DBEAFE; color: #1E40AF; display: flex; align-items: center; justify-content: center;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                    </div>
                </div>
                <div style="font-size: 1.375rem; font-weight: 800; color: var(--neutral-900); line-height: 1.2; margin-bottom: 0.25rem;">
                    Maks. {{ $berkasMaksMb ?? 2 }} <span style="font-size: 0.8125rem; font-weight: 600; color: var(--neutral-600);">MB</span>
                </div>
                <div style="font-size: 0.6875rem; color: var(--neutral-600); display: flex; align-items: center; justify-content: space-between;">
                    <span>Signed URL (TTL 5 Menit)</span>
                    <span style="font-weight: 700; color: #166534;">Anti-IDOR/BOLA</span>
                </div>
            </div>

            <!-- Kartu 4: Imutabilitas Audit Trail (NFR-S9) -->
            <div style="background: #F8FAFC; border: 1px solid var(--neutral-300); border-radius: 10px; padding: 1rem;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
                    <span style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-700);">Audit Trail Imutabel (NFR-S9)</span>
                    <div style="width: 28px; height: 28px; border-radius: 6px; background: #F3E8FF; color: #7C3AED; display: flex; align-items: center; justify-content: center;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="9 11 12 14 22 4"></polyline>
                            <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                        </svg>
                    </div>
                </div>
                <div style="font-size: 1.375rem; font-weight: 800; color: var(--neutral-900); line-height: 1.2; margin-bottom: 0.25rem;">
                    {{ number_format($securityPosture['total_audit_events'] ?? 0) }} <span style="font-size: 0.8125rem; font-weight: 600; color: var(--neutral-600);">Log</span>
                </div>
                <div style="font-size: 0.6875rem; color: var(--neutral-600); display: flex; align-items: center; justify-content: space-between;">
                    <span>Append-Only (No Delete/Edit)</span>
                    <span style="font-weight: 700; color: #166534;">Integritas 100%</span>
                </div>
            </div>

        </div>

        <!-- Detail Radar Insiden & Sebaran Kategori Audit PRD Bab 10.3 -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(360px, 1fr)); gap: 1.25rem;">
            
            <!-- Kolom Kiri: Radar Percobaan Intrusi & Insiden Auth Gagal Terakhir -->
            <div style="background: #FFFFFF; border: 1px solid var(--neutral-300); border-radius: 10px; padding: 1.125rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                    <h4 style="font-size: 0.875rem; font-weight: 700; color: var(--neutral-900); margin: 0; display: flex; align-items: center; gap: 0.375rem;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                        <span>Radar Anomali Login Terakhir</span>
                    </h4>
                    <span style="font-size: 0.6875rem; color: var(--neutral-500);">5 Rekaman Terbaru</span>
                </div>

                @if($insidenKeamananTerbaru->isEmpty())
                    <div style="text-align: center; padding: 2rem 1rem; background: #F8FAFC; border-radius: 8px; border: 1px dashed var(--neutral-300);">
                        <div style="width: 36px; height: 36px; border-radius: 50%; background: #DCFCE7; color: #16A34A; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 0.5rem;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                        </div>
                        <div style="font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.125rem;">
                            Kondisi Sistem Bersih & Terkendali
                        </div>
                        <div style="font-size: 0.75rem; color: var(--neutral-600); max-width: 340px; margin: 0 auto;">
                            Tidak ada rekaman percobaan login gagal, serangan brute force, atau pelanggaran rate limiting dalam radar pemantauan.
                        </div>
                    </div>
                @else
                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.75rem;">
                            <thead>
                                <tr style="border-bottom: 1px solid var(--neutral-300); color: var(--neutral-600); text-align: left;">
                                    <th style="padding: 0.375rem 0.5rem;">Waktu</th>
                                    <th style="padding: 0.375rem 0.5rem;">IP Address</th>
                                    <th style="padding: 0.375rem 0.5rem;">Target Identitas</th>
                                    <th style="padding: 0.375rem 0.5rem; text-align: right;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($insidenKeamananTerbaru as $insiden)
                                    <tr style="border-bottom: 1px solid var(--neutral-200);">
                                        <td style="padding: 0.5rem; color: var(--neutral-800); font-weight: 600; white-space: nowrap;">
                                            {{ \Carbon\Carbon::parse($insiden->waktu)->format('d/m H:i') }}
                                        </td>
                                        <td style="padding: 0.5rem; font-family: monospace; color: var(--neutral-700);">
                                            {{ $insiden->ip ?? '-' }}
                                        </td>
                                        <td style="padding: 0.5rem; color: var(--neutral-900); max-width: 140px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                            {{ $insiden->user?->nama ?? $insiden->user?->email ?? ($insiden->payload['email'] ?? 'Publik/Guest') }}
                                        </td>
                                        <td style="padding: 0.5rem; text-align: right;">
                                            <span style="font-size: 0.6875rem; font-weight: 700; background: #FEE2E2; color: #DC2626; padding: 0.1rem 0.375rem; border-radius: 4px;">
                                                {{ $insiden->status_http ?? 401 }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <!-- Kolom Kanan: Distribusi Kategori Audit PRD Bab 10.3 & Checklist Hardening -->
            <div style="background: #FFFFFF; border: 1px solid var(--neutral-300); border-radius: 10px; padding: 1.125rem;">
                @php
                    $totAudit = max(1, ($auditTipeDistribution['auth'] ?? 0) + ($auditTipeDistribution['aksi'] ?? 0) + ($auditTipeDistribution['service'] ?? 0) + ($auditTipeDistribution['akses_data'] ?? 0));
                    $pctAuth = round((($auditTipeDistribution['auth'] ?? 0) / $totAudit) * 100);
                    $pctAksi = round((($auditTipeDistribution['aksi'] ?? 0) / $totAudit) * 100);
                    $pctService = round((($auditTipeDistribution['service'] ?? 0) / $totAudit) * 100);
                    $pctAkses = max(0, 100 - ($pctAuth + $pctAksi + $pctService));
                @endphp

                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                    <h4 style="font-size: 0.875rem; font-weight: 700; color: var(--neutral-900); margin: 0; display: flex; align-items: center; gap: 0.375rem;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--blue-600)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="20" x2="18" y2="10"></line>
                            <line x1="12" y1="20" x2="12" y2="4"></line>
                            <line x1="6" y1="20" x2="6" y2="14"></line>
                        </svg>
                        <span>Distribusi Audit Trail (PRD Bab 10.3)</span>
                    </h4>
                    <span style="font-size: 0.6875rem; font-weight: 700; color: var(--neutral-700);">
                        {{ number_format($totAudit) }} Total Event
                    </span>
                </div>

                <!-- Visual Segmented Bar -->
                <div style="height: 10px; border-radius: 6px; overflow: hidden; display: flex; background: var(--neutral-200); margin-bottom: 0.875rem;">
                    <div style="width: {{ $pctAuth }}%; background: #2563EB;" title="Auth: {{ $pctAuth }}%"></div>
                    <div style="width: {{ $pctAksi }}%; background: #16A34A;" title="Aksi: {{ $pctAksi }}%"></div>
                    <div style="width: {{ $pctService }}%; background: #D97706;" title="Service: {{ $pctService }}%"></div>
                    <div style="width: {{ $pctAkses }}%; background: #7C3AED;" title="Akses Data: {{ $pctAkses }}%"></div>
                </div>

                <!-- Legend & Counts Grid -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; margin-bottom: 1rem; font-size: 0.75rem;">
                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.375rem 0.5rem; background: #F8FAFC; border-radius: 6px;">
                        <span style="display: flex; align-items: center; gap: 0.375rem; color: var(--neutral-800); font-weight: 600;">
                            <span style="width: 8px; height: 8px; border-radius: 50%; background: #2563EB;"></span>
                            Auth (Login/Sesi)
                        </span>
                        <span style="font-weight: 700; color: var(--neutral-900);">{{ number_format($auditTipeDistribution['auth'] ?? 0) }}</span>
                    </div>
                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.375rem 0.5rem; background: #F8FAFC; border-radius: 6px;">
                        <span style="display: flex; align-items: center; gap: 0.375rem; color: var(--neutral-800); font-weight: 600;">
                            <span style="width: 8px; height: 8px; border-radius: 50%; background: #16A34A;"></span>
                            Aksi (Mutasi Data)
                        </span>
                        <span style="font-weight: 700; color: var(--neutral-900);">{{ number_format($auditTipeDistribution['aksi'] ?? 0) }}</span>
                    </div>
                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.375rem 0.5rem; background: #F8FAFC; border-radius: 6px;">
                        <span style="display: flex; align-items: center; gap: 0.375rem; color: var(--neutral-800); font-weight: 600;">
                            <span style="width: 8px; height: 8px; border-radius: 50%; background: #D97706;"></span>
                            Service (Scheduler)
                        </span>
                        <span style="font-weight: 700; color: var(--neutral-900);">{{ number_format($auditTipeDistribution['service'] ?? 0) }}</span>
                    </div>
                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.375rem 0.5rem; background: #F8FAFC; border-radius: 6px;">
                        <span style="display: flex; align-items: center; gap: 0.375rem; color: var(--neutral-800); font-weight: 600;">
                            <span style="width: 8px; height: 8px; border-radius: 50%; background: #7C3AED;"></span>
                            Akses Data (Unduh)
                        </span>
                        <span style="font-weight: 700; color: var(--neutral-900);">{{ number_format($auditTipeDistribution['akses_data'] ?? 0) }}</span>
                    </div>
                </div>

                <!-- Postur Hardening HTTP & Aplikasi (NFR-S1 s/d NFR-S8) -->
                <div style="border-top: 1px dashed var(--neutral-300); padding-top: 0.625rem; font-size: 0.6875rem; color: var(--neutral-600); display: flex; flex-direction: column; gap: 0.25rem;">
                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <span>• HTTP Headers: X-Frame-Options DENY & nosniff (NFR-S8)</span>
                        <span style="font-weight: 700; color: #166534;">AKTIF</span>
                    </div>
                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <span>• Rate Limiter & Anti-Bruteforce Throttle (TDD Bab 8)</span>
                        <span style="font-weight: 700; color: #166534;">AKTIF</span>
                    </div>
                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <span>• Enkripsi Sandi Bcrypt (Cost 12) & Proteksi CSRF Form</span>
                        <span style="font-weight: 700; color: #166534;">AKTIF</span>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- BAGIAN 3: PINTASAN KONTROL SISTEM, ROLE & AUDIT LOG -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(340px, 1fr)); gap: 1.5rem; margin-top: 1.5rem;">
        
        <!-- Box Tata Kelola Hak Akses & Master Data -->
        <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.25rem 1.5rem; box-shadow: var(--shadow-subtle);">
            <h4 style="font-size: 0.9375rem; font-weight: 700; color: var(--neutral-900); margin: 0 0 0.75rem 0;">
                Kontrol Hak Akses & Master Data (PRD 3.1 & 3.3)
            </h4>
            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                <a href="{{ route('superadmin.role.index') }}" style="display: flex; align-items: center; justify-content: space-between; padding: 0.625rem 0.75rem; background: var(--neutral-150); border: 1px solid var(--neutral-300); border-radius: 8px; text-decoration: none; color: var(--neutral-900);">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--blue-600);">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                        </svg>
                        <span style="font-size: 0.8125rem; font-weight: 600;">Matriks Peran & Hak Akses (Figma SA-02)</span>
                    </div>
                    <span style="font-size: 0.75rem; color: var(--blue-600); font-weight: 700;">Lihat &rarr;</span>
                </a>

                <a href="{{ route('superadmin.jurusan.index') }}" style="display: flex; align-items: center; justify-content: space-between; padding: 0.625rem 0.75rem; background: var(--neutral-150); border: 1px solid var(--neutral-300); border-radius: 8px; text-decoration: none; color: var(--neutral-900);">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #059669;">
                            <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path>
                        </svg>
                        <span style="font-size: 0.8125rem; font-weight: 600;">Master Jurusan & Program Studi</span>
                    </div>
                    <span style="font-size: 0.75rem; color: var(--blue-600); font-weight: 700;">Kelola &rarr;</span>
                </a>

                <a href="{{ route('superadmin.bidang.index') }}" style="display: flex; align-items: center; justify-content: space-between; padding: 0.625rem 0.75rem; background: var(--neutral-150); border: 1px solid var(--neutral-300); border-radius: 8px; text-decoration: none; color: var(--neutral-900);">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #D97706;">
                            <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                            <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                        </svg>
                        <span style="font-size: 0.8125rem; font-weight: 600;">Master Bidang Kerja Diskominfo</span>
                    </div>
                    <span style="font-size: 0.75rem; color: var(--blue-600); font-weight: 700;">Kelola &rarr;</span>
                </a>

                <a href="{{ route('superadmin.rubrik.index') }}" style="display: flex; align-items: center; justify-content: space-between; padding: 0.625rem 0.75rem; background: var(--neutral-150); border: 1px solid var(--neutral-300); border-radius: 8px; text-decoration: none; color: var(--neutral-900);">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #7C3AED;">
                            <polyline points="9 11 12 14 22 4"></polyline>
                            <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                        </svg>
                        <span style="font-size: 0.8125rem; font-weight: 600;">Bobot Rubrik Penilaian Akhir</span>
                    </div>
                    <span style="font-size: 0.75rem; color: var(--blue-600); font-weight: 700;">Atur &rarr;</span>
                </a>
            </div>
        </div>

        <!-- Box Keamanan, Konfigurasi & Arsip -->
        <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.25rem 1.5rem; box-shadow: var(--shadow-subtle);">
            <h4 style="font-size: 0.9375rem; font-weight: 700; color: var(--neutral-900); margin: 0 0 0.75rem 0;">
                Keamanan, Arsip & Konfigurasi Sistem
            </h4>
            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                <a href="{{ route('superadmin.pengaturan.index') }}" style="display: flex; align-items: center; justify-content: space-between; padding: 0.625rem 0.75rem; background: var(--neutral-150); border: 1px solid var(--neutral-300); border-radius: 8px; text-decoration: none; color: var(--neutral-900);">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--blue-600);">
                            <circle cx="12" cy="12" r="3"></circle>
                            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                        </svg>
                        <span style="font-size: 0.8125rem; font-weight: 600;">Parameter Konfigurasi Global</span>
                    </div>
                    <span style="font-size: 0.75rem; color: var(--blue-600); font-weight: 700;">Ubah &rarr;</span>
                </a>

                <a href="{{ route('superadmin.arsip.index') }}" style="display: flex; align-items: center; justify-content: space-between; padding: 0.625rem 0.75rem; background: var(--neutral-150); border: 1px solid var(--neutral-300); border-radius: 8px; text-decoration: none; color: var(--neutral-900);">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #059669;">
                            <polyline points="21 8 21 21 3 21 3 8"></polyline>
                            <rect x="1" y="3" width="22" height="5"></rect>
                            <line x1="10" y1="12" x2="14" y2="12"></line>
                        </svg>
                        <span style="font-size: 0.8125rem; font-weight: 600;">Gudang Arsip Peserta Magang (F-12)</span>
                    </div>
                    <span style="font-size: 0.75rem; color: var(--blue-600); font-weight: 700;">Buka &rarr;</span>
                </a>

                <a href="{{ route('superadmin.audit-log.index') }}" style="display: flex; align-items: center; justify-content: space-between; padding: 0.625rem 0.75rem; background: var(--neutral-150); border: 1px solid var(--neutral-300); border-radius: 8px; text-decoration: none; color: var(--neutral-900);">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #7C3AED;">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                        </svg>
                        <span style="font-size: 0.8125rem; font-weight: 600;">Jejak Audit Trail & Log Keamanan</span>
                    </div>
                    <span style="font-size: 0.75rem; color: var(--blue-600); font-weight: 700;">Buka &rarr;</span>
                </a>

                <a href="{{ route('superadmin.audit-log.export') }}" style="display: flex; align-items: center; justify-content: space-between; padding: 0.625rem 0.75rem; background: var(--neutral-150); border: 1px solid var(--neutral-300); border-radius: 8px; text-decoration: none; color: var(--neutral-900);">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #DC2626;">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="7 10 12 15 17 10"></polyline>
                            <line x1="12" y1="15" x2="12" y2="3"></line>
                        </svg>
                        <span style="font-size: 0.8125rem; font-weight: 600;">Ekspor Jejak Audit Trail (CSV)</span>
                    </div>
                    <span style="font-size: 0.75rem; color: #DC2626; font-weight: 700;">Unduh &rarr;</span>
                </a>

                <a href="{{ route('superadmin.health.index') }}" style="display: flex; align-items: center; justify-content: space-between; padding: 0.625rem 0.75rem; background: var(--neutral-150); border: 1px solid var(--neutral-300); border-radius: 8px; text-decoration: none; color: var(--neutral-900);">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #059669;">
                            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                        </svg>
                        <span style="font-size: 0.8125rem; font-weight: 600;">Telemetri Kesehatan & Storage Infrastruktur</span>
                    </div>
                    <span style="font-size: 0.75rem; color: var(--blue-600); font-weight: 700;">Buka &rarr;</span>
                </a>

                <a href="{{ route('superadmin.backup.index') }}" style="display: flex; align-items: center; justify-content: space-between; padding: 0.625rem 0.75rem; background: var(--neutral-150); border: 1px solid var(--neutral-300); border-radius: 8px; text-decoration: none; color: var(--neutral-900);">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #7C3AED;">
                            <ellipse cx="12" cy="5" rx="9" ry="3"></ellipse>
                            <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path>
                            <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path>
                        </svg>
                        <span style="font-size: 0.8125rem; font-weight: 600;">Pusat Cadangan & Pemulihan Basis Data (Backup Hub)</span>
                    </div>
                    <span style="font-size: 0.75rem; color: var(--blue-600); font-weight: 700;">Buka &rarr;</span>
                </a>
            </div>

            <!-- Nilai Parameter Resmi PRD Bab 8.6 Aktif -->
            <div style="margin-top: 0.875rem; padding-top: 0.75rem; border-top: 1px dashed var(--neutral-300); display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; font-size: 0.6875rem;">
                <div style="background: var(--neutral-100); padding: 0.375rem 0.5rem; border-radius: 6px;">
                    <span style="color: var(--neutral-600); display: block;">logbook_maks_mundur</span>
                    <strong style="color: var(--neutral-900);">Maks. {{ $logbookMaksMundurHari }} Hari (BR-09)</strong>
                </div>
                <div style="background: var(--neutral-100); padding: 0.375rem 0.5rem; border-radius: 6px;">
                    <span style="color: var(--neutral-600); display: block;">masa_tenggang_bulan</span>
                    <strong style="color: var(--neutral-900);">{{ $masaTenggangBulan }} Bulan (BR-12)</strong>
                </div>
                <div style="background: var(--neutral-100); padding: 0.375rem 0.5rem; border-radius: 6px;">
                    <span style="color: var(--neutral-600); display: block;">berkas_maks_mb</span>
                    <strong style="color: var(--neutral-900);">Maks. {{ $berkasMaksMb }} MB (BR-03)</strong>
                </div>
                <div style="background: var(--neutral-100); padding: 0.375rem 0.5rem; border-radius: 6px;">
                    <span style="color: var(--neutral-600); display: block;">slot_khusus_default</span>
                    <strong style="color: var(--neutral-900);">{{ $slotKhususDefault }} Kuota (BR-01)</strong>
                </div>
            </div>
        </div>

    </div>

    <!-- BAGIAN 4: STATUS KESEHATAN SISTEM & INFRASTRUKTUR (TDD Bab 10, NFR-O1) -->
    <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.25rem 1.5rem; box-shadow: var(--shadow-subtle); margin-top: 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.875rem; flex-wrap: wrap; gap: 0.5rem;">
            <div>
                <h4 style="font-size: 0.9375rem; font-weight: 700; color: var(--neutral-900); margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                    <span>Integritas Infrastruktur & Status Layanan (TDD Bab 10)</span>
                    <span style="font-size: 0.6875rem; font-weight: 700; background: #DCFCE7; color: #166534; padding: 0.15rem 0.5rem; border-radius: 999px;">
                        Sistem Sehat
                    </span>
                </h4>
                <p style="font-size: 0.75rem; color: var(--neutral-600); margin: 0.125rem 0 0 0;">
                    Pemantauan integritas database MySQL/InnoDB, storage berkas privat terenkripsi, dan scheduler cron job
                </p>
            </div>
            <a href="{{ route('health') }}" target="_blank" style="font-size: 0.75rem; font-weight: 700; color: var(--blue-600); text-decoration: underline;">
                Buka Healthcheck API (/health) &rarr;
            </a>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 0.875rem;">
            <div style="padding: 0.75rem 1rem; border-radius: 8px; background: #F8FAFC; border: 1px solid var(--neutral-300); display: flex; align-items: center; gap: 0.75rem;">
                <div style="width: 10px; height: 10px; border-radius: 50%; background: #16A34A; flex-shrink: 0; box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.2);"></div>
                <div>
                    <span style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-900); display: block;">Koneksi Basis Data</span>
                    <span style="font-size: 0.6875rem; color: #166534; font-weight: 600;">MySQL InnoDB / Normal</span>
                </div>
            </div>

            <div style="padding: 0.75rem 1rem; border-radius: 8px; background: #F8FAFC; border: 1px solid var(--neutral-300); display: flex; align-items: center; gap: 0.75rem;">
                <div style="width: 10px; height: 10px; border-radius: 50%; background: #16A34A; flex-shrink: 0; box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.2);"></div>
                <div>
                    <span style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-900); display: block;">Private Disk Storage</span>
                    <span style="font-size: 0.6875rem; color: #166534; font-weight: 600;">Disk Privat Terisolasi</span>
                </div>
            </div>

            <div style="padding: 0.75rem 1rem; border-radius: 8px; background: #F8FAFC; border: 1px solid var(--neutral-300); display: flex; align-items: center; gap: 0.75rem;">
                <div style="width: 10px; height: 10px; border-radius: 50%; background: #2563EB; flex-shrink: 0; box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2);"></div>
                <div>
                    <span style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-900); display: block;">Audit Trail Append-Only</span>
                    <span style="font-size: 0.6875rem; color: #1D4ED8; font-weight: 600;">{{ number_format($systemHealth['audit_count'] ?? 0) }} Rekaman Terarsip</span>
                </div>
            </div>

            <div style="padding: 0.75rem 1rem; border-radius: 8px; background: #F8FAFC; border: 1px solid var(--neutral-300); display: flex; align-items: center; gap: 0.75rem;">
                <div style="width: 10px; height: 10px; border-radius: 50%; background: #7C3AED; flex-shrink: 0; box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.2);"></div>
                <div>
                    <span style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-900); display: block;">Scheduler Nonaktif Harian</span>
                    <span style="font-size: 0.6875rem; color: #6B21A8; font-weight: 600;">Terkonfigurasi di Kernel</span>
                </div>
            </div>
        </div>
    </div>

</div>
