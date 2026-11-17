# PLAN PERANCANGAN & IMPLEMENTASI
## Penerapan Animasi Material Floating Card Box pada Catatan Verifikasi Admin (`admin.pendaftaran.show`) Selaras dengan Portal Pembimbing

- **Dokumen Referensi:** PRD MagKom v1.2, DS-MagKom (`docs/design/design-system.md`), Pola Interaksi Pembimbing (`docs/plans/2026-11-06-senior-dev-task-catatan-umpan-balik-floating-card-prd.md`)
- **Tanggal Rencana:** 17 November 2026
- **Status Rencana:** Menunggu Persetujuan Pengguna (*Pending Approval*)
- **Target Modul:** Kolom Input Catatan Verifikasi Berkas Pendaftaran Admin (`resources/views/admin/pendaftaran/show.blade.php`)

---

## 1. Latar Belakang & Analisis Bukti Visual

Berdasarkan 4 tangkapan layar yang dilampirkan oleh pengguna:

| Tangkapan Layar | Konteks Halaman | Karakteristik Visual & Masalah |
|---|---|---|
| **Foto 1** (`media_..._88f9d79e.png`) | Admin: *Catatan Penerimaan (Opsional)* | Menggunakan `<textarea>` konvensional dengan *outline border* statis 1px, label kaku di luar box, dan placeholder panjang. Tidak ada feedback visual interaktif saat kursor fokus. |
| **Foto 2** (`media_..._80ad27b4.png`) | Admin: *Alasan Penolakan \** | Bentuk input yang sama kaku dengan border abu-abu standar, tidak seragam dengan estetika modern portal. |
| **Foto 3** (`media_..._fd923b79.png`) | Pembimbing: *Catatan Umpan Balik* (Default) | **Model Acuan Target**: Kotak *filled background* abu-abu lembut (`#F1F5F9`), sudut melengkung atas (`8px 8px 0 0`), garis bawah tipis (`#94A3B8`), label berada di dalam card atas, dan textarea bersih tanpa border ganda. |
| **Foto 4** (`media_..._0671aa56.png`) | Pembimbing: *Catatan Umpan Balik* (Fokus Aktif) | **Model Acuan Animasi**: Saat kursor masuk/menulis, label bertransisi halus mengecil (*floating* ke atas), teks judul berubah menjadi **biru Sleman** (*bold*), dan garis aksen bawah melebar secara horizontal dari tengah (*smooth Material active indicator*). |

### 🎯 Tujuan Utama (Objective)
Menerapkan pola **Material Floating Card Box** yang identik dengan halaman Pembimbing pada dua field input keputusan Admin:
1. **Catatan Penerimaan (Opsional)** pada Tab *Terima* (Aksen Utama: Biru Sleman `#0641CC`).
2. **Alasan Penolakan \*** pada Tab *Tolak* (Aksen Utama: Merah Bahaya `#DC2626`).

---

## 2. Kepatuhan Aturan Repositori (Strict AGENTS.md Rules)

Sesuai aturan baku MAGKOM:
1. **Rule 3.3 (Pengecualian Perubahan Blade)**:
   - *Alasan Perubahan Markup*: Struktur form lama membungkus `<label>` di luar dan `<textarea>` mandiri di dalam `<div class="adm-form-field">`. Untuk mencapai animasi floating box ala Pembimbing, `<label>` dan `<textarea>` wajib berada di dalam satu container induk `.adm-floating-field` berposisi `relative` agar label dapat melayang (*floating*) secara absolut di atas teks textarea tanpa menumpuk.
   - *Minimal Changes*: Perubahan markup hanya menyentuh blok field textarea catatan pada tab *Terima* dan *Tolak* (sekitar baris 347-364 di `resources/views/admin/pendaftaran/show.blade.php`).
   - *Zero Inline CSS/JS*: Tidak ada style atau script yang disisipkan ke dalam Blade.
2. **Rule 4 & 5 (Satu Sumber Kebenaran CSS & JS)**:
   - Seluruh styling ditempatkan di `resources/css/pages/admin/pendaftaran-show.css`.
   - Seluruh interaksi kelas dinamis (`is-focused`, `has-value`) ditangani di `resources/js/pages/admin/pendaftaran-show.js`.
3. **Kepatuhan Aksesibilitas & Dark Mode**:
   - Dukungan penuh `[data-theme="dark"]` selaras dengan Bento Navy.
   - Tetap mempertahankan atribut standar HTML5 (`for`, `id`, `name="catatan"`, `rows`, `spellcheck="false"`).

---

## 3. Pemetaan Berkas & Dampak Perubahan

| Path Berkas | Tanggung Jawab | Rencana Modifikasi |
|---|---|---|
| `resources/views/admin/pendaftaran/show.blade.php` | Markup Keputusan Admin | Ganti pembungkus field lama menjadi `.adm-floating-field` dengan struktur floating label di dalam card. |
| `resources/css/pages/admin/pendaftaran-show.css` | Styling Halaman Show Admin | Tambahkan selector `.adm-floating-field`, animasi garis `::after`, floating label, textarea bersih, varian bahaya (`.adm-floating-field-danger`), dan dukungan *Dark Mode*. |
| `resources/js/pages/admin/pendaftaran-show.js` | Logika Interaktivitas Frontend | Tambahkan fungsi helper listener fokus/blur/input untuk kedua textarea (`catatan_terima` dan `catatan_tolak`). |
| `tests/Feature/AdminPendaftaranPerbaikanFlowTest.php` | Pengujian Otomatis | Jalankan regresi test untuk memastikan pengiriman form keputusan tetap lolos 100%. |

---

## 4. Panduan Implementasi Langkah demi Langkah (Step-by-Step)

### Langkah 1: Penyesuaian Markup Blade (`resources/views/admin/pendaftaran/show.blade.php`)

Ganti baris 347-365 pada `resources/views/admin/pendaftaran/show.blade.php`:

#### 1.1 Field Catatan Penerimaan (Tab Terima)
```blade
{{-- Sebelum: --}}
<div class="adm-form-field">
    <label for="catatan_terima">{{ __('Catatan Penerimaan (Opsional)') }}</label>
    <textarea id="catatan_terima" name="catatan" rows="2" placeholder="{{ __('Tambahkan catatan verifikasi berkas jika diperlukan...') }}"></textarea>
</div>

{{-- Menjadi (Material Floating Card Box): --}}
<div class="adm-floating-field" id="box_catatan_terima" style="margin-bottom: 1.25rem;">
    <label for="catatan_terima" class="adm-floating-label">
        <span class="adm-label-title">{{ __('Catatan Penerimaan (Opsional)') }}</span>
    </label>
    <textarea id="catatan_terima" 
              name="catatan" 
              class="adm-floating-textarea" 
              rows="2" 
              spellcheck="false">{{ old('catatan') }}</textarea>
</div>
```

#### 1.2 Field Alasan Penolakan (Tab Tolak)
```blade
{{-- Sebelum: --}}
<div class="adm-form-field">
    <label for="catatan_tolak">
        {{ __('Alasan Penolakan') }} <span style="color: #DC2626;">*</span>
    </label>
    <textarea id="catatan_tolak" name="catatan" rows="3" placeholder="{{ __('Tuliskan alasan penolakan pengajuan (min. 10 karakter)...') }}"></textarea>
</div>

{{-- Menjadi (Material Floating Card Box Varian Bahaya / Merah): --}}
<div class="adm-floating-field adm-floating-field-danger" id="box_catatan_tolak" style="margin-bottom: 1.25rem;">
    <label for="catatan_tolak" class="adm-floating-label">
        <span class="adm-label-title">{{ __('Alasan Penolakan') }}</span>
        <span class="adm-label-badge-req" style="color: #DC2626; font-weight: 700;">*</span>
    </label>
    <textarea id="catatan_tolak" 
              name="catatan" 
              class="adm-floating-textarea" 
              rows="3" 
              spellcheck="false">{{ old('catatan') }}</textarea>
</div>
```

---

### Langkah 2: Penambahan Aturan CSS (`resources/css/pages/admin/pendaftaran-show.css`)

Tambahkan blok CSS di `resources/css/pages/admin/pendaftaran-show.css`:

```css
/* ==========================================================================
   Material Floating Card Box: Catatan Keputusan Admin (Paritas Pembimbing)
   ========================================================================== */
.adm-floating-field {
    position: relative;
    background: #F1F5F9;
    border-bottom: 1px solid #94A3B8;
    border-radius: 8px 8px 0 0;
    padding: 24px 14px 8px 14px;
    box-sizing: border-box;
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
    overflow: hidden;
    transition: background-color 0.25s cubic-bezier(0.4, 0, 0.2, 1),
                border-color 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}

.adm-floating-field:hover {
    background: #E8EDF5;
    border-bottom-color: #64748B;
}

.adm-floating-field:focus-within,
.adm-floating-field.is-focused {
    background: #E8EDF5;
    border-bottom-color: transparent;
}

/* Animasi Garis Aksen Bawah (Material Active Indicator) */
.adm-floating-field::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 2px;
    background: #0641CC; /* Biru Sleman untuk Terima */
    transform: scaleX(0);
    transform-origin: center;
    transition: transform 0.28s cubic-bezier(0.4, 0, 0.2, 1);
    pointer-events: none;
    z-index: 10;
}

.adm-floating-field:focus-within::after,
.adm-floating-field.is-focused::after {
    transform: scaleX(1);
}

/* Varian Khusus Penolakan (Aksen Merah) */
.adm-floating-field-danger::after {
    background: #DC2626;
}

.adm-floating-field.has-error {
    border-bottom-color: #DC2626;
    background: #FEF2F2;
}

.adm-floating-field.has-error::after {
    background: #DC2626;
    transform: scaleX(1);
}

/* Floating Label di Dalam Box */
.adm-floating-label {
    position: absolute;
    top: 14px;
    left: 14px;
    right: 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    pointer-events: none;
    font-size: 0.8125rem;
    font-weight: 500;
    color: #64748B;
    transform-origin: top left;
    transition: transform 0.22s cubic-bezier(0.4, 0, 0.2, 1),
                color 0.22s cubic-bezier(0.4, 0, 0.2, 1),
                font-weight 0.22s cubic-bezier(0.4, 0, 0.2, 1);
    user-select: none;
    z-index: 2;
}

.adm-floating-label .adm-label-title {
    font-size: inherit;
    font-weight: inherit;
    color: inherit;
}

/* Transisi Melayang (Floating) saat Fokus ATAU Memiliki Teks Nilai */
.adm-floating-field:focus-within .adm-floating-label,
.adm-floating-field.is-focused .adm-floating-label,
.adm-floating-field.has-value .adm-floating-label {
    transform: translateY(-8px) scale(0.85);
}

/* Pewarnaan Label saat Fokus */
.adm-floating-field:focus-within .adm-floating-label .adm-label-title,
.adm-floating-field.is-focused .adm-floating-label .adm-label-title {
    color: #0641CC;
    font-weight: 700;
}

.adm-floating-field-danger:focus-within .adm-floating-label .adm-label-title,
.adm-floating-field-danger.is-focused .adm-floating-label .adm-label-title {
    color: #DC2626;
    font-weight: 700;
}

.adm-floating-field.has-value:not(:focus-within):not(.is-focused) .adm-floating-label .adm-label-title {
    color: #475569;
    font-weight: 600;
}

/* Textarea Bersih Tanpa Border Tambahan di Dalam Card Box */
.adm-floating-textarea {
    width: 100%;
    min-height: 52px;
    box-sizing: border-box;
    border: 0;
    background: transparent;
    outline: none;
    font-size: 0.8125rem;
    line-height: 1.5;
    font-family: inherit;
    color: #1D2530;
    resize: none;
    padding: 0;
    margin: 0;
    z-index: 1;
}

/* Dukungan Penuh Dark Mode */
[data-theme="dark"] .adm-floating-field {
    background: #1E293B;
    border-bottom-color: #475569;
}

[data-theme="dark"] .adm-floating-field:hover {
    background: #25334D;
    border-bottom-color: #64748B;
}

[data-theme="dark"] .adm-floating-field:focus-within,
[data-theme="dark"] .adm-floating-field.is-focused {
    background: #1E293B;
}

[data-theme="dark"] .adm-floating-label {
    color: #94A3B8;
}

[data-theme="dark"] .adm-floating-textarea {
    color: #F8FAFC;
}

[data-theme="dark"] .adm-floating-field.has-value:not(:focus-within):not(.is-focused) .adm-floating-label .adm-label-title {
    color: #CBD5E1;
}
```

---

### Langkah 3: Penambahan Interaktivitas JavaScript (`resources/js/pages/admin/pendaftaran-show.js`)

Tambahkan fungsi inisialisasi floating field di dalam `DOMContentLoaded`:

```javascript
// Helper Animasi Material Floating Label untuk Field Catatan (Paritas Pembimbing)
function initFloatingBox(textareaId, boxId) {
    const textarea = document.getElementById(textareaId);
    const box = document.getElementById(boxId) || textarea?.closest('.adm-floating-field');
    if (!textarea || !box) return;

    function updateState() {
        const hasValue = textarea.value.trim().length > 0;
        box.classList.toggle('has-value', hasValue);
    }

    textarea.addEventListener('focus', () => {
        box.classList.add('is-focused');
        box.classList.remove('has-error');
    });

    textarea.addEventListener('blur', () => {
        box.classList.remove('is-focused');
        updateState();
    });

    textarea.addEventListener('input', () => {
        updateState();
        if (textarea.value.trim().length > 0) {
            box.classList.remove('has-error');
        }
    });

    // Inisialisasi awal (jika terdapat old value dari server)
    updateState();
}

// Pasang pada kedua kolom catatan
initFloatingBox('catatan_terima', 'box_catatan_terima');
initFloatingBox('catatan_tolak', 'box_catatan_tolak');
```

---

## 5. Rencana Pengujian & Quality Gate

1. **Pengujian Visual & Interaksi Browser**:
   - Membuka halaman `http://localhost:8000/admin/pendaftaran/64` (Akun Bagas Saputra - S1).
   - Menguji fokus pada *Catatan Penerimaan (Opsional)*: Memastikan garis biru Sleman melebar dari tengah ke samping dan label mengecil melayang ke atas dengan mulus.
   - Menguji tab *Tolak*: Memastikan form berganti ke *Alasan Penolakan*, garis aksen berwarna merah kirmisi `#DC2626`, dan label tanda bintang `*` terlihat jelas.
   - Mengetik teks & blur kursor: Memastikan label tetap mengambang di atas (*has-value state*) sehingga teks catatan tidak tertutup.
2. **Pengujian Dark Mode (`[data-theme="dark"]`)**:
   - Mengaktifkan toggle dark mode: Memastikan latar card box berubah menjadi `#1E293B`, teks ketikan menjadi `#F8FAFC`, dan kontras memenuhi rasio minimum 4.5:1 (WCAG 2.1 AA).
3. **Pengujian Integritas Fungsional & Regresi Form**:
   - Validasi submit Tolak dengan teks < 10 karakter: Memastikan browser menampilkan validasi native / error box.
   - Konfirmasi modal: Memastikan nilai catatan tetap terbawa sempurna saat form dikirim dan modal konfirmasi terbuka.
4. **Automated Test & Linter Suite**:
   - Menjalankan `php artisan test tests/Feature/AdminPendaftaranPerbaikanFlowTest.php`.
   - Menjalankan `./vendor/bin/pint --test` untuk memastikan kebersihan kode.
   - Menjalankan `npm run build` untuk memverifikasi kompilasi Vite tanpa peringatan.

---

## 6. Permintaan Persetujuan Perubahan Blade (Strict Rule 3.3)

Sesuai aturan permanen pengembang AI pada berkas `.agents/rules/AGENTS.md` Bagian 3.3, modifikasi file Blade memerlukan izin eksplisit:

> **File yang diajukan untuk dimodifikasi:**  
> `resources/views/admin/pendaftaran/show.blade.php` (baris 347–365)
>
> **Alasan:**  
> Menghubungkan elemen `<label>` dan `<textarea>` ke dalam satu pembungkus kartu berposisi relatif (`.adm-floating-field`) agar label dapat melayang (*floating animation*) di dalam batas kotak input tanpa menutup teks isi, persis seperti implementasi pada halaman Pembimbing.

Mohon konfirmasi persetujuan Anda:
**Apakah rencana implementasi ini sudah disetujui untuk dieksekusi?**
