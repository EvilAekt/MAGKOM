/**
 * Universal 3-Segment Password Strength Meter (Informative Indicator)
 * 
 * Segments:
 * - 1 Segment Merah  = Lemah  (Informasi visual: belum memenuhi standar)
 * - 2 Segment Kuning = Sedang (Kombinasi huruf & angka, min. 8 karakter)
 * - 3 Segment Hijau  = Kuat   (Kombinasi huruf besar/kecil, angka, simbol)
 * 
 * Aturan Bisnis:
 * - Berfungsi murni sebagai PEMBERITAHUAN (Non-blocking: submit tetap aktif).
 */

export function initPasswordStrengthMeter() {
  const wrappers = document.querySelectorAll('.pw-strength-wrapper');

  wrappers.forEach((wrapper) => {
    if (wrapper.dataset.initialized === 'true') return;
    wrapper.dataset.initialized = 'true';

    // Inject struktur DOM 3 segmen batang secara otomatis jika belum ada di template Blade
    if (!wrapper.querySelector('.pw-strength-bars')) {
      wrapper.innerHTML = `
        <div class="pw-strength-bars" aria-hidden="true">
          <div class="pw-strength-bar pw-bar-1"></div>
          <div class="pw-strength-bar pw-bar-2"></div>
          <div class="pw-strength-bar pw-bar-3"></div>
        </div>
        <div class="pw-strength-label-row">
          <span class="pw-strength-text" aria-live="polite"></span>
        </div>
      `;
    }

    const targetInputId = wrapper.getAttribute('data-target-input') || 'password';
    let input = document.getElementById(targetInputId);

    // Fallback: cari input password terdekat jika ID berbeda
    if (!input) {
      input = wrapper.previousElementSibling?.querySelector('input[type="password"]') ||
              wrapper.closest('.form-group, .form-group-password, div')?.querySelector('input[type="password"]');
    }
    if (!input) return;

    const textSpan = wrapper.querySelector('.pw-strength-text');

    const evaluate = () => {
      const val = input.value || '';

      if (val.length === 0) {
        wrapper.removeAttribute('data-level');
        if (textSpan) textSpan.textContent = '';
        return;
      }

      const hasMinLength = val.length >= 8;
      const hasLetters = /[a-zA-Z]/.test(val);
      const hasNumbers = /[0-9]/.test(val);
      const hasSpecial = /[^a-zA-Z0-9]/.test(val);
      const hasMixedCase = /[a-z]/.test(val) && /[A-Z]/.test(val);

      let level = 'weak';
      let label = 'Lemah';

      // Evaluasi Level
      if (hasMinLength && hasLetters && hasNumbers) {
        if (hasMixedCase && hasSpecial) {
          level = 'strong';
          label = 'Kuat';
        } else {
          level = 'medium';
          label = 'Sedang';
        }
      }

      wrapper.setAttribute('data-level', level);
      if (textSpan) textSpan.textContent = label;

      // Submit TIDAK didisable - murni sebagai pemberitahuan visual sesuai permintaan klien
    };

    input.addEventListener('input', evaluate);
    evaluate();
  });
}
