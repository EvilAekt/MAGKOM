@if(isset($isPreview) && $isPreview && !request()->boolean('editor_mode'))
<aside id="cms-preview-floating-bar" class="magkom-preview-bar" aria-label="Panel Kontrol Pratinjau CMS">
    <div class="magkom-preview-bar__container">
        <div class="magkom-preview-bar__info">
            <span class="magkom-preview-badge">
                <span class="magkom-preview-badge__dot"></span>
                Pratinjau Draf
            </span>
            <p class="magkom-preview-desc">
                Halaman ini menampilkan pratinjau draf yang belum diterbitkan ke publik.
            </p>
        </div>
        <div class="magkom-preview-bar__actions">
            <a href="{{ route('superadmin.site-editor.index') }}" class="btn-preview-back">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
                Kembali ke Editor
            </a>
            
            <form action="{{ route('superadmin.site-editor.discard') }}" method="POST" class="preview-action-form" data-confirm="Apakah Anda yakin ingin membuang semua draf perubahan? Konten akan dikembalikan ke versi live saat ini.">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-preview-discard">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                    Buang Draf
                </button>
            </form>

            <form action="{{ route('superadmin.site-editor.publish') }}" method="POST" class="preview-action-form" data-confirm="Apakah Anda yakin ingin MEMPUBLIKASIKAN perubahan ini ke publik? Seluruh pengunjung umum akan langsung melihat versi baru ini.">
                @csrf
                <button type="submit" class="btn-preview-publish">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/></svg>
                    Publikasikan ke Publik
                </button>
            </form>
        </div>
    </div>
</aside>
@endif

