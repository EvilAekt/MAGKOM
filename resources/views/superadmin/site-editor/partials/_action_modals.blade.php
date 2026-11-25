{{-- MODAL AKSI PUBLIKASI DAN ROLLBACK (IN-PAGE MAGKOM CONFIRMATION MODALS - DS-MAGKOM STANDARDS) --}}

{{-- 1. MODAL KONFIRMASI TERBITKAN KE LIVE (REFERENSI GAMBAR 1) --}}
<div id="wp-publish-modal" class="magkom-modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="publish-modal-title" tabindex="-1">
    <div class="magkom-modal-dialog">
        <div class="magkom-modal-header">
            <div class="magkom-modal-header-left">
                <div class="magkom-modal-icon-badge variant-primary">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                </div>
                <div>
                    <h3 id="publish-modal-title" class="magkom-modal-title">
                        {{ __('Konfirmasi Publikasi Halaman') }}
                    </h3>
                    <span class="magkom-modal-subtitle">
                        {{ __('Perubahan Draf Siap Dipublikasikan ke Portal Publik') }}
                    </span>
                </div>
            </div>
            <button type="button" 
                    id="btn-close-publish-modal" 
                    class="magkom-modal-close" 
                    aria-label="{{ __('Tutup dialog') }}" 
                    title="{{ __('Tutup dialog') }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>

        <div class="magkom-modal-body">
            <p class="magkom-modal-question">
                {{ __('Apakah Anda yakin ingin mempublikasikan seluruh perubahan draf ini ke halaman publik?') }}
            </p>

            <div class="magkom-modal-identity-strip">
                <div class="magkom-modal-avatar variant-primary">
                    <span>CMS</span>
                </div>
                <div class="magkom-modal-identity-info">
                    <div class="magkom-modal-identity-top">
                        <span class="magkom-modal-target-name">
                            {{ __('Landing Page Portal MAGKOM') }}
                        </span>
                        <span class="magkom-modal-reg-badge">
                            #LIVE-VERSION
                        </span>
                    </div>
                    <div class="magkom-modal-target-sub">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="magkom-modal-sub-icon">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                            <polyline points="9 22 9 12 15 12 15 22"></polyline>
                        </svg>
                        <span>{{ __('Dinas Komunikasi dan Informatika (7 Seksi Terkelola)') }}</span>
                    </div>
                </div>
            </div>

            <div class="magkom-modal-impact-banner variant-primary">
                <div class="magkom-modal-impact-icon-wrap">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="16" x2="12" y2="12"></line>
                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                    </svg>
                </div>
                <div class="magkom-modal-impact-text">
                    {{ __('Seluruh pengunjung umum akan langsung melihat tata letak, banner, dan konten terbaru. Sistem otomatis membuat salinan cadangan (backup snapshot) sebelum penerbitan.') }}
                </div>
            </div>
        </div>

        <div class="magkom-modal-footer">
            <button type="button" 
                    id="btn-cancel-publish-modal" 
                    class="magkom-modal-btn-cancel">
                {{ __('Batal') }}
            </button>
            <button type="button" 
                    id="btn-confirm-publish-modal" 
                    class="magkom-modal-btn-confirm variant-primary">
                <span class="btn-spinner" style="display: none;"></span>
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 12h14"></path>
                    <path d="m12 5 7 7-7 7"></path>
                </svg>
                <span>{{ __('Ya, Terbitkan Halaman') }}</span>
            </button>
        </div>
    </div>
</div>

{{-- 2. MODAL KONFIRMASI ROLLBACK KE BACKUP SEBELUMNYA (REFERENSI GAMBAR 1) --}}
<div id="wp-rollback-modal" class="magkom-modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="rollback-modal-title" tabindex="-1">
    <div class="magkom-modal-dialog">
        <div class="magkom-modal-header">
            <div class="magkom-modal-header-left">
                <div class="magkom-modal-icon-badge variant-danger">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path>
                        <path d="M3 3v5h5"></path>
                    </svg>
                </div>
                <div>
                    <h3 id="rollback-modal-title" class="magkom-modal-title">
                        {{ __('Konfirmasi Rollback Halaman') }}
                    </h3>
                    <span class="magkom-modal-subtitle">
                        {{ __('Kembalikan Tampilan Publik ke Versi Sebelum Terbit Terakhir') }}
                    </span>
                </div>
            </div>
            <button type="button" 
                    id="btn-close-rollback-modal" 
                    class="magkom-modal-close" 
                    aria-label="{{ __('Tutup dialog') }}" 
                    title="{{ __('Tutup dialog') }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>

        <div class="magkom-modal-body">
            <p class="magkom-modal-question">
                {{ __('Apakah Anda yakin ingin membatalkan perubahan draf dan mengembalikan ke versi sebelum publikasi terakhir?') }}
            </p>

            <div class="magkom-modal-identity-strip">
                <div class="magkom-modal-avatar variant-danger">
                    <span>BCK</span>
                </div>
                <div class="magkom-modal-identity-info">
                    <div class="magkom-modal-identity-top">
                        <span class="magkom-modal-target-name">
                            {{ __('Cadangan Snapshot Otomatis') }}
                        </span>
                        <span class="magkom-modal-reg-badge" style="background: #fef2f2; color: #dc2626; border-color: #fecaca;">
                            #RESTORE-BACKUP
                        </span>
                    </div>
                    <div class="magkom-modal-target-sub">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="magkom-modal-sub-icon">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                        <span>{{ __('Snapshot Versi Publik Terakhir di Server') }}</span>
                    </div>
                </div>
            </div>

            <div class="magkom-modal-impact-banner variant-danger">
                <div class="magkom-modal-impact-icon-wrap">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"></path>
                        <line x1="12" y1="9" x2="12" y2="13"></line>
                        <line x1="12" y1="17" x2="12.01" y2="17"></line>
                    </svg>
                </div>
                <div class="magkom-modal-impact-text">
                    {{ __('PERINGATAN: Perubahan draf saat ini yang belum dipublikasikan akan digantikan oleh versi cadangan terakhir. Halaman publik akan langsung kembali ke versi sebelumnya.') }}
                </div>
            </div>
        </div>

        <div class="magkom-modal-footer">
            <button type="button" 
                    id="btn-cancel-rollback-modal" 
                    class="magkom-modal-btn-cancel">
                {{ __('Batal') }}
            </button>
            <button type="button" 
                    id="btn-confirm-rollback-modal" 
                    class="magkom-modal-btn-confirm variant-danger">
                <span class="btn-spinner" style="display: none;"></span>
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path>
                    <path d="M3 3v5h5"></path>
                </svg>
                <span>{{ __('Ya, Rollback Versi') }}</span>
            </button>
        </div>
    </div>
</div>
