/**
 * MAGKOM INDUSTRIAL DESIGN SYSTEM: PENGATURAN AKUN & PREFERENSI
 * Modular ES Module for Account & Settings Pages across all Roles
 */

import { initPengaturan2faModal } from './pengaturan-2fa-modal.js';
import { initPasswordStrengthMeter } from './password-strength.js';

export function initPengaturanAkun() {
    initPasswordToggles();
    initThemeSelection();
    initPengaturan2faModal();
    initPasswordStrengthMeter();
    initAjaxPasswordUpdateForm();
}

/**
 * Handle password show/hide eye toggle button
 */
function initPasswordToggles() {
    const toggleButtons = document.querySelectorAll('.toggle-eye-btn, [data-toggle-target]');

    toggleButtons.forEach((btn) => {
        // Prevent duplicate listener attachments
        if (btn.dataset.toggleInitialized === 'true') {
            return;
        }
        btn.dataset.toggleInitialized = 'true';

        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const targetId = btn.getAttribute('data-toggle-target');
            if (!targetId) return;

            const input = document.getElementById(targetId);
            if (!input) return;

            const isPassword = input.getAttribute('type') === 'password';
            input.setAttribute('type', isPassword ? 'text' : 'password');
            btn.setAttribute('aria-label', isPassword ? 'Sembunyikan kata sandi' : 'Lihat kata sandi');

            // Toggle SVG icon between open eye and closed eye
            if (isPassword) {
                // Eye with slash (hide)
                btn.innerHTML = `
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"></path>
                    </svg>
                `;
            } else {
                // Eye open (show)
                btn.innerHTML = `
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                `;
            }
        });
    });
}

/**
 * Handle theme radio cards visual selection synchronization
 */
function initThemeSelection() {
    const themeCards = document.querySelectorAll('.theme-preview-card');
    if (!themeCards.length) return;

    themeCards.forEach((card) => {
        const radio = card.querySelector('input[type="radio"]');

        // Sync initial state
        if (radio && radio.checked) {
            card.classList.add('selected');
        }

        card.addEventListener('click', () => {
            if (!radio) return;
            radio.checked = true;
            radio.dispatchEvent(new Event('change', { bubbles: true }));

            // Update UI class
            themeCards.forEach((c) => c.classList.remove('selected'));
            card.classList.add('selected');
        });

        if (radio) {
            radio.addEventListener('change', () => {
                if (radio.checked) {
                    themeCards.forEach((c) => c.classList.remove('selected'));
                    card.classList.add('selected');
                }
            });
        }
    });
}

/**
 * Zero-Reload AJAX Submit for Role Settings Password Form (#passwordForm)
 * Retains all input fields on error, displays inline field validation, and provides button loading state.
 */
function initAjaxPasswordUpdateForm() {
    const passwordForm = document.getElementById('passwordForm');
    if (!passwordForm) return;

    if (passwordForm.dataset.ajaxInitialized === 'true') {
        return;
    }
    passwordForm.dataset.ajaxInitialized = 'true';

    const submitBtn = passwordForm.querySelector('button[type="submit"]') || document.getElementById('btnSubmitPassword');

    passwordForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        // 1. Bersihkan error sebelumnya
        passwordForm.querySelectorAll('.is-invalid').forEach((el) => {
            el.classList.remove('is-invalid');
            el.style.borderColor = '';
        });
        passwordForm.querySelectorAll('.form-error-feedback').forEach((el) => el.remove());
        const existingAlert = passwordForm.querySelector('.password-ajax-alert');
        if (existingAlert) existingAlert.remove();

        // 2. Loading state pada tombol submit
        const originalBtnText = submitBtn ? submitBtn.innerText : 'Perbarui Kata Sandi';
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerText = 'Menyimpan...';
        }

        const formData = new FormData(passwordForm);
        const actionUrl = passwordForm.getAttribute('action') || window.location.href;
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                          passwordForm.querySelector('input[name="_token"]')?.value || '';

        try {
            const response = await fetch(actionUrl, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: formData,
            });

            const data = await response.json().catch(() => ({}));

            if (response.ok && data.success) {
                // Tampilkan pesan sukses
                const alertSuccess = document.createElement('div');
                alertSuccess.className = 'password-ajax-alert';
                alertSuccess.style.cssText = 'margin-bottom: 20px; padding: 12px 16px; border-radius: 10px; background: #ECFDF5; border: 1px solid #6EE7B7; color: #065F46; font-size: 13.5px; line-height: 1.5; display: flex; align-items: center; gap: 8px';
                alertSuccess.innerHTML = `
                    <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                    <span>${data.message || 'Kata sandi berhasil diperbarui.'}</span>
                `;
                passwordForm.prepend(alertSuccess);

                // Reset field HANYA saat berhasil
                passwordForm.reset();

                // Trigger toast jika ada
                if (window.MagKomToast && typeof window.MagKomToast.show === 'function') {
                    window.MagKomToast.show({
                        type: 'success',
                        title: 'Berhasil',
                        message: data.message || 'Kata sandi berhasil diperbarui.'
                    });
                }
                return;
            }

            // GAGAL: JANGAN KOSONGKAN INPUT!
            // Tampilkan error inline per field
            let hasFocused = false;
            if (data.errors && typeof data.errors === 'object') {
                for (const [fieldName, messages] of Object.entries(data.errors)) {
                    const input = passwordForm.querySelector(`[name="${fieldName}"]`);
                    const msgText = Array.isArray(messages) ? messages[0] : messages;
                    if (input) {
                        input.classList.add('is-invalid');
                        input.style.borderColor = '#EF4444';
                        const wrapper = input.closest('.form-group-password') || input.parentElement;
                        const errorDiv = document.createElement('div');
                        errorDiv.className = 'form-error-feedback';
                        errorDiv.style.cssText = 'font-size: 0.75rem; color: #EF4444; margin-top: 4px;';
                        errorDiv.textContent = msgText;
                        wrapper.appendChild(errorDiv);

                        if (!hasFocused) {
                            input.focus();
                            hasFocused = true;
                        }
                    }
                }
            }

            // Tampilkan alert ringkasan error
            const alertError = document.createElement('div');
            alertError.className = 'password-ajax-alert';
            alertError.style.cssText = 'margin-bottom: 20px; padding: 12px 16px; border-radius: 10px; background: #FEE2E2; border: 1px solid #FCA5A5; color: #991B1B; font-size: 13.5px; line-height: 1.5; display: flex; align-items: center; gap: 8px';
            const generalMsg = data.message || 'Gagal memperbarui kata sandi. Silakan periksa kembali data yang dimasukkan.';
            alertError.innerHTML = `
                <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                <span>${generalMsg}</span>
            `;
            passwordForm.prepend(alertError);

        } catch (err) {
            // Fallback non-AJAX jika koneksi putus
            passwordForm.submit();
        } finally {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerText = originalBtnText;
            }
        }
    });
}

// Auto-run if loaded directly in browser
if (typeof document !== 'undefined') {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initPengaturanAkun);
    } else {
        initPengaturanAkun();
    }
}

