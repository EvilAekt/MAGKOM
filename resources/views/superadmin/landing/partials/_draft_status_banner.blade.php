{{-- DRAFT SANDBOX ALERT & GLOBAL WORKSPACE ACTIONS --}}
@if(isset($draftMeta) && $draftMeta['active'])
    <div style="background: #FFFBEB; border: 1.5px solid #F59E0B; border-radius: 10px; padding: 1.25rem 1.5rem; color: #92400E; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; box-shadow: 0 2px 8px rgba(245, 158, 11, 0.1);">
        <div style="display: flex; align-items: flex-start; gap: 0.875rem; flex: 1; min-width: 300px;">
            <div style="background: #F59E0B; color: #FFFFFF; width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 1.125rem; font-weight: bold;">
                &#9888;
            </div>
            <div>
                <h4 style="margin: 0 0 0.25rem 0; font-size: 0.9375rem; font-weight: 800; color: #78350F;">
                    Ada Perubahan Draf Belum Terpublikasi (Kontainer Sandbox Aktif)
                </h4>
                <p style="margin: 0; font-size: 0.8125rem; color: #B45309; line-height: 1.4;">
                    Terdapat <strong>{{ $draftMeta['total_draft_keys'] }} elemen draf</strong> yang terakhir disimpan pada <strong>{{ $draftMeta['updated_at'] }}</strong> oleh <strong>{{ $draftMeta['author_name'] ?? 'Super Admin' }}</strong>. Pengunjung umum di luar sana masih melihat versi stabil sebelumnya.
                </p>
            </div>
        </div>

        <div style="display: flex; align-items: center; gap: 0.625rem; flex-wrap: wrap;">
            <a href="{{ route('superadmin.landing.preview') }}" target="_blank" style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.55rem 1rem; background: #0284C7; color: #FFFFFF; border-radius: 6px; font-weight: 700; font-size: 0.8125rem; text-decoration: none; box-shadow: 0 1px 2px rgba(2, 132, 199, 0.3);">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                <span>Buka Pratinjau Interaktif</span>
            </a>

            <form action="{{ route('superadmin.landing.discard') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin MEMBUANG seluruh draf perubahan? Editor akan dikembalikan ke versi live saat ini.')" style="margin: 0;">
                @csrf
                @method('DELETE')
                <button type="submit" style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.55rem 1rem; background: #FEF2F2; color: #DC2626; border: 1px solid #FCA5A5; border-radius: 6px; font-weight: 700; font-size: 0.8125rem; cursor: pointer; transition: all 0.2s ease;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/></svg>
                    <span>Buang Draf</span>
                </button>
            </form>

            <form action="{{ route('superadmin.landing.publish') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin MEMPUBLIKASIKAN seluruh draf ini ke publik sekarang? Seluruh pengunjung umum akan langsung melihat perubahannya.')" style="margin: 0;">
                @csrf
                <button type="submit" style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.55rem 1.15rem; background: #16A34A; color: #FFFFFF; border: none; border-radius: 6px; font-weight: 800; font-size: 0.8125rem; cursor: pointer; box-shadow: 0 1px 3px rgba(22, 163, 74, 0.4);">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/></svg>
                    <span>Publikasikan ke Publik</span>
                </button>
            </form>
        </div>
    </div>
@else
    <div style="background: #F0FDF4; border: 1.5px solid #86EFAC; border-radius: 10px; padding: 1rem 1.25rem; color: #166534; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem;">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <span style="display: inline-block; width: 10px; height: 10px; border-radius: 50%; background: #22C55E;"></span>
            <span style="font-size: 0.875rem; font-weight: 700; color: #15803D;">
                Versi Live Terpublikasi &amp; Tersinkronisasi
            </span>
            <span style="font-size: 0.8125rem; color: #166534;">
                &bull; Seluruh konten publik saat ini stabil. Perubahan yang Anda simpan sebagai draf akan masuk ke Sandbox sebelum diterbitkan.
            </span>
        </div>
        <div>
            <a href="{{ route('superadmin.landing.preview') }}" target="_blank" style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.4rem 0.85rem; background: #FFFFFF; border: 1px solid #BBF7D0; color: #15803D; border-radius: 6px; font-weight: 700; font-size: 0.75rem; text-decoration: none;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                <span>Pratinjau Live Saat Ini</span>
            </a>
        </div>
    </div>
@endif

