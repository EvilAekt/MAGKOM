@if(config('captcha.enabled', true))
    <div class="form-group captcha-group" style="margin-bottom: 1.25rem;">
        <label for="captcha" class="form-label" style="display: block; font-size: 0.875rem; font-weight: 600; color: var(--neutral-700); margin-bottom: 0.5rem;">
            Kode Verifikasi Keamanan <span style="color: var(--red-500);">*</span>
        </label>
        
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.625rem;">
            <div style="flex-shrink: 0; border: 1.5px solid var(--neutral-300); border-radius: 8px; overflow: hidden; background-color: var(--neutral-50); box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); display: flex; align-items: center; justify-content: center;">
                <img 
                    id="captcha-img" 
                    src="{{ route('captcha.image') }}" 
                    alt="Kode Keamanan Captcha" 
                    width="160" 
                    height="50" 
                    style="display: block; width: 160px; height: 50px; cursor: pointer; user-select: none;"
                    title="Klik gambar untuk mengganti kode"
                    onclick="refreshCaptcha()"
                >
            </div>
            
            <button 
                type="button" 
                id="btn-refresh-captcha" 
                onclick="refreshCaptcha()" 
                class="btn-refresh-captcha"
                style="padding: 0.625rem; border-radius: 8px; border: 1px solid var(--neutral-300); background-color: var(--neutral-0); color: var(--neutral-600); cursor: pointer; display: inline-flex; align-items: center; justify-content: center; transition: all 0.2s ease;"
                title="Muat ulang kode keamanan"
                aria-label="Muat ulang kode keamanan baru"
            >
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="refresh-icon">
                    <polyline points="23 4 23 10 17 10"></polyline>
                    <polyline points="1 20 1 14 7 14"></polyline>
                    <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
                </svg>
            </button>
        </div>

        <div class="input-wrapper">
            <span class="input-icon-left" aria-hidden="true">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                </svg>
            </span>
            <input 
                type="text" 
                id="captcha" 
                name="captcha" 
                maxlength="4" 
                required 
                class="form-input captcha-input @error('captcha') form-input-error @enderror" 
                placeholder="4 digit kode di atas"
                autocomplete="off"
                aria-label="Kode Verifikasi Keamanan 4 Karakter"
            >
        </div>
        <span class="form-hint" style="font-size: 0.75rem; color: var(--neutral-500); margin-top: 0.25rem; display: block;">
            Masukkan 4 karakter yang tampil pada gambar (tidak sensitif huruf besar/kecil).
        </span>
    </div>

    <style>
        .captcha-input {
            font-family: ui-monospace, 'SF Mono', Menlo, Consolas, 'Liberation Mono', monospace !important;
            font-size: 1.125rem !important;
            font-weight: 700 !important;
            letter-spacing: 0.2em !important;
            text-transform: uppercase !important;
        }
        .captcha-input::placeholder {
            font-family: 'Open Sans', system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
            font-size: 0.875rem !important;
            font-weight: 400 !important;
            letter-spacing: normal !important;
            text-transform: none !important;
            color: var(--neutral-400) !important;
            opacity: 1;
        }
        .btn-refresh-captcha:hover {
            background-color: var(--neutral-100);
            color: var(--blue-600);
            border-color: var(--blue-600);
        }
        @keyframes spinCaptchaIcon {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .btn-refresh-captcha.spinning .refresh-icon {
            animation: spinCaptchaIcon 0.5s ease-in-out;
        }

        /* Dark mode overrides for Captcha Component */
        [data-theme="dark"] .captcha-input::placeholder {
            color: #64748B !important;
        }
        [data-theme="dark"] .btn-refresh-captcha {
            background-color: var(--neutral-100, #0F172A) !important;
            border-color: var(--neutral-200, #26354D) !important;
            color: var(--neutral-600, #CBD5E1) !important;
        }
        [data-theme="dark"] .btn-refresh-captcha:hover {
            background-color: var(--neutral-0, #182234) !important;
            color: #60A5FA !important;
            border-color: #3B82F6 !important;
        }
        [data-theme="dark"] .captcha-group .form-hint {
            color: var(--neutral-500, #94A3B8) !important;
        }
    </style>

    <script>
        function refreshCaptcha() {
            const img = document.getElementById('captcha-img');
            const btn = document.getElementById('btn-refresh-captcha');
            const input = document.getElementById('captcha');
            if (btn) {
                btn.classList.add('spinning');
                setTimeout(() => btn.classList.remove('spinning'), 500);
            }
            if (img) {
                img.src = '{{ route("captcha.image") }}?' + Date.now();
            }
            if (input) {
                input.value = '';
                input.focus();
            }
        }
    </script>
@endif
