{{-- ==========================================================================
   Aside Widget - Dashboard Peserta Magang
   Desain Modern Clean SaaS (Linear / Shadcn UI Inspired)
   ========================================================================== --}}

@if($fase === 'aktif' && $penempatan)
    {{-- CARD 1: PEMBIMBING LAPANGAN --}}
    <div class="saas-card" style="padding: 20px;">
        <div class="saas-aside-section-title" style="font-size: 0.8125rem; font-weight: 700; color: var(--neutral-900, #0f172a); letter-spacing: 0.02em; margin-bottom: 14px; display: flex; align-items: center; justify-content: space-between;">
            <span style="display: flex; align-items: center; gap: 8px;">
                <span style="color: #2563eb;">👤</span>
                <span>Pembimbing Lapangan</span>
            </span>
            <span class="saas-badge-success" style="font-size: 0.725rem; font-weight: 600; color: #16a34a; background: #f0fdf4; padding: 2px 8px; border-radius: 9999px;">Aktif</span>
        </div>

        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 14px;">
            <div class="saas-aside-avatar" style="width: 44px; height: 44px; border-radius: 12px; background: #eff6ff; color: #1b3860; display: flex; align-items: center; justify-content: center; font-family: var(--font-display); font-weight: 700; font-size: 1rem; border: 1px solid #bfdbfe; flex-shrink: 0;">
                {{ strtoupper(substr($penempatan->pembimbing->nama ?? 'PL', 0, 2)) }}
            </div>
            <div style="min-width: 0;">
                <h4 class="saas-aside-title" style="font-family: var(--font-display); font-size: 0.875rem; font-weight: 700; color: var(--neutral-900, #0f172a); margin: 0 0 2px 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                    {{ $penempatan->pembimbing->nama ?? 'Pembimbing Diskominfo' }}
                </h4>
                <p class="saas-aside-subtitle" style="font-size: 0.75rem; color: var(--neutral-500, #64748b); margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                    {{ $penempatan->bidang->nama_bidang ?? 'Dinas Komunikasi dan Informatika' }}
                </p>
            </div>
        </div>

        @php
            $waUrl = !empty($penempatan->pembimbing->no_hp) 
                ? 'https://wa.me/' . preg_replace('/[^0-9]/', '', $penempatan->pembimbing->no_hp)
                : 'https://wa.me/628122788990?text=' . urlencode('Halo Admin MagKom Diskominfo Sleman, saya peserta ' . ($user->nama ?? $user->name ?? ''));
            $waLabel = !empty($penempatan->pembimbing->no_hp) ? 'Hubungi via WhatsApp' : 'Hubungi Helpdesk Magang';
        @endphp
        <a href="{{ $waUrl }}" target="_blank" class="saas-btn-whatsapp" style="display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%; padding: 8px 14px; border-radius: 8px; background: #f8fafc; border: 1px solid var(--neutral-200, #e2e8f0); color: #1b3860; font-size: 0.8125rem; font-weight: 600; text-decoration: none; transition: all 0.15s ease;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
            <span>{{ $waLabel }}</span>
        </a>
    </div>

    {{-- CARD 2: DOKUMEN & BERKAS RESMI --}}
    <div class="saas-card" style="padding: 20px;">
        <div class="saas-aside-section-title" style="font-size: 0.8125rem; font-weight: 700; color: var(--neutral-900, #0f172a); letter-spacing: 0.02em; margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
            <span style="color: #f59e0b;">📁</span>
            <span>Berkas & Panduan</span>
        </div>

        <div style="display: flex; flex-direction: column; gap: 8px;">
            <a href="{{ route('peserta.penempatan.surat-diterima.pratinjau', $penempatan) }}" target="_blank" class="saas-doc-row" style="display: flex; align-items: center; justify-content: space-between; padding: 10px 12px; border-radius: 8px; border: 1px solid var(--neutral-200, #e2e8f0); background: #ffffff; text-decoration: none; font-size: 0.8125rem; color: var(--neutral-800, #1e293b); font-weight: 600; transition: all 0.15s ease;">
                <span style="display: flex; align-items: center; gap: 8px;">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: #2563eb;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                    <span>Surat Keterangan Magang</span>
                </span>
                <span style="font-size: 0.75rem; color: #2563eb; font-weight: 700;">PDF &rarr;</span>
            </a>

            <a href="{{ route('panduan.pdf') }}" target="_blank" class="saas-doc-row" style="display: flex; align-items: center; justify-content: space-between; padding: 10px 12px; border-radius: 8px; border: 1px solid var(--neutral-200, #e2e8f0); background: #ffffff; text-decoration: none; font-size: 0.8125rem; color: var(--neutral-800, #1e293b); font-weight: 600; transition: all 0.15s ease;">
                <span style="display: flex; align-items: center; gap: 8px;">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: #f59e0b;"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                    <span>Buku Panduan Magang</span>
                </span>
                <span style="font-size: 0.75rem; color: #f59e0b; font-weight: 700;">Unduh &rarr;</span>
            </a>
        </div>
    </div>

@else
    {{-- ONBOARDING / SELEKSI ASIDE --}}
    <div class="saas-card" style="padding: 20px;">
        <div class="saas-aside-section-title" style="font-size: 0.8125rem; font-weight: 700; color: var(--neutral-900, #0f172a); letter-spacing: 0.02em; margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
            <span style="color: #2563eb;">ℹ️</span>
            <span>Layanan Magang Sleman</span>
        </div>

        <p class="saas-aside-subtitle" style="font-size: 0.8125rem; color: var(--neutral-600, #475569); line-height: 1.5; margin: 0 0 12px 0;">
            Verifikasi berkas persyaratan magang diproses oleh tim Diskominfo Sleman pada hari kerja.
        </p>

        <div class="saas-aside-info-box" style="background: var(--neutral-100, #f8fafc); border-radius: 8px; padding: 12px 14px; font-size: 0.8125rem; color: var(--neutral-700, #334155); display: flex; flex-direction: column; gap: 6px; border: 1px solid var(--neutral-200, #e2e8f0); margin-bottom: 14px;">
            <div><strong>Hari Kerja:</strong> Senin &ndash; Jumat</div>
            <div><strong>Jam Layanan:</strong> 08:00 &ndash; 15:30 WIB</div>
            <div><strong>Lokasi:</strong> Diskominfo Kab. Sleman</div>
        </div>

        <a href="{{ route('panduan.pdf') }}" target="_blank" class="saas-btn-guide" style="display: flex; align-items: center; justify-content: space-between; padding: 10px 12px; border-radius: 8px; border: 1px solid #bfdbfe; background: #eff6ff; text-decoration: none; font-size: 0.8125rem; color: #1e40af; font-weight: 600;">
            <span>📖 Unduh Buku Panduan PDF</span>
            <span>&rarr;</span>
        </a>
    </div>
@endif
