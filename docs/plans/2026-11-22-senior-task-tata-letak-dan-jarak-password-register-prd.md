# Senior Developer Task Specification & PRD: Optimalisasi Tata Letak & Spasi Antara Kata Sandi dan Konfirmasi Kata Sandi

**Target Audience**: Junior Developer / Autonomous AI Implementer (e.g., Gemini 3.8 Flash, Claude Haiku, OpenCode)  
**Author**: Senior Fullstack Web Architect & Lead Engineer  
**Date**: 22 November 2026  
**Document Code**: `PRD-AUTH-2026-11-004` (Spasi & Tata Letak Register Form)  
**Status**: ✅ Selesai Diimplementasikan & Terverifikasi (Verified)  
**Referensi Desain**: Gambar referensi user (`media_1795340924566_c4d33619.png`) — Jarak vertikal antara kotak input `password` dan kotak input `ulangi kata sandi` terlihat terlalu renggang/jauh (*awkward whitespace gap*).

---

## 1. Analisis Akar Masalah (Root Cause Analysis / RCA)

Berdasarkan keluhan pengguna:
> *"buat plan untuk jaarak nya jangan terlalu jauh kayak aneh antara pw dan ulangi pw"*

Setelah menginspeksi berkas `resources/views/auth/register.blade.php` dan `resources/css/components/password-strength.css`, teridentifikasi akumulasi jarak vertikal sebesar **~74px** antara bagian bawah kotak input `password` dan bagian atas kotak input `ulangi kata sandi`:

```
+-------------------------------------------------------------+
| [🔒] password                                          [👁️] |
+-------------------------------------------------------------+
  ↕ (margin-top: 8px pada .pw-strength-wrapper)
  [=== Batang 1 ===] [=== Batang 2 ===] [=== Batang 3 ===] (tinggi 6px)
  ↕ (gap: 6px di dalam wrapper)
  [     RUANG KOSONG 18px (min-height: 18px pada label row)  ]  <-- PENYEBAB UTAMA DEAD SPACE!
  ↕ (margin-top: 6px pada #hint-sandi)
  Minimal 8 karakter kombinasi huruf dan angka. (tinggi teks ~18px)
  ↕ (gap: 12px pada container flex form)
+-------------------------------------------------------------+
| [✓]  ulangi kata sandi                                 [👁️] |
+-------------------------------------------------------------+
Total Jarak Vertikal: 8 + 6 + 6 + 18 + 6 + 18 + 12 = ~74px (Terlalu Jauh!)
```

### Rincian Penyebab:
1. **Dead Space 18px pada `.pw-strength-label-row`**:
   Pada `password-strength.css`, class `.pw-strength-label-row` memiliki properti `min-height: 18px;`. Ketika pengguna belum mengetik apa pun (kondisi awal), wadah ini tetap memakan tinggi 18px secara kosong melompong di antara 3 batang abu-abu dan teks hint *"Minimal 8 karakter..."*.
2. **Spasi Margin & Tinggi Batang yang Terlalu Tebal**:
   - `margin-top: 8px` dan `gap: 6px` pada `.pw-strength-wrapper` membuat indikator terlalu renggang dari input.
   - Tinggi batang 6px dapat dirampingkan menjadi 4px agar tampak lebih proporsional dan modern (*sleek & subtle*).
3. **Pemisahan Elemen dalam Container Terpisah**:
   - Field `password` dan field `ulangi kata sandi` saat ini merupakan dua anak langsung terpisah di dalam `<div style="display: flex; flex-direction: column; gap: 12px">`.
   - Hal ini menambahkan `gap: 12px` penuh di antara kedua field yang semestinya merupakan satu pasangan kredensial erat (*credential pair*).

---

## 2. Solusi Desain & Spesifikasi Antarmuka yang Diinginkan

### 2.1 Perbandingan Visual (Before vs After)

```
KONDISI AWAL (SEBELUM)             TARGET OPTIMAL (SESUDAH)
Jarak: ~74px (Terlalu Jauh)         Jarak: ~36px (Ramping & Proporsional)

+-----------------------+           +-----------------------+
| [🔒] password    [👁️] |           | [🔒] password    [👁️] |
+-----------------------+           +-----------------------+
   [---] [---] [---]                   [---] [---] [---] (tinggi 4px, margin 5px)
                                       Minimal 8 karakter kombinasi huruf dan angka.
   (Ruang kosong 18px)                 (Label muncul hanya saat diketik)
   Minimal 8 karakter...
                                    +-----------------------+
+-----------------------+           | [✓]  ulangi kata...[👁️]|
| [✓]  ulangi kata...[👁️]|           +-----------------------+
+-----------------------+           Masukkan ulang kata sandi...
```

### 2.2 Prinsip Penyesuaian:
1. **Sembunyikan Label Row saat Kosong (`display: none`)**:
   Ketika input belum memiliki nilai (belum ada atribut `data-level`), baris status label ("Lemah/Sedang/Kuat") disembunyikan sepenuhnya sehingga tidak ada ruang kosong melompong.
2. **Rampingkan Dimensi Indikator**:
   - Tinggi batang meter: dari `6px` menjadi `4px`.
   - Jarak atas meter: dari `margin-top: 8px` menjadi `margin-top: 5px`.
   - Gap internal meter: dari `gap: 6px` menjadi `gap: 4px`.
3. **Kompakkan Teks Petunjuk (*Hint Spacing*)**:
   - `#hint-sandi`: ubah `margin: 6px 0 0` menjadi `margin: 4px 0 0; font-size: 12px; line-height: 1.35;`.
   - `#hint-sandi-confirmation`: ubah `margin: 6px 0 0` menjadi `margin: 4px 0 0; font-size: 12px; line-height: 1.35;`.
4. **Kelompokkan Kredensial Sandi secara Harmonis**:
   - Satukan field kata sandi dan konfirmasi kata sandi dalam satu kelompok kredensial dengan `gap: 8px` (bukan `gap: 12px`).
   - Hasil akhir: Total jarak vertikal terpangkas hingga **50%** (dari ~74px menjadi ~36px), menghasilkan tampilan antarmuka yang padat, rapi, dan nyaman dipandang.

---

## 3. Spesifikasi Perubahan Kode Teknis

### 3.1 Perubahan CSS ([resources/css/components/password-strength.css](file:///g:/MAGKOM/resources/css/components/password-strength.css))

Perbarui deklarasi CSS untuk merampingkan dimensi dan menghilangkan dead space:

```css
/* Rampingkan wrapper */
.pw-strength-wrapper {
  display: flex;
  flex-direction: column;
  gap: 4px;              /* Dari 6px -> 4px */
  margin-top: 5px;        /* Dari 8px -> 5px */
  width: 100%;
  box-sizing: border-box;
}

/* Rampingkan batang meter */
.pw-strength-bars {
  display: flex;
  align-items: center;
  gap: 5px;
  width: 100%;
}

.pw-strength-bar {
  flex: 1;
  height: 4px;           /* Dari 6px -> 4px */
  border-radius: 9999px;
  background-color: #E2E8F0;
  transition: background-color 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}

/* HILANGKAN DEAD SPACE SAAT KOSONG */
.pw-strength-wrapper:not([data-level]) .pw-strength-label-row {
  display: none;
}

.pw-strength-label-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  font-size: 12px;        /* Dari 12.5px -> 12px */
  font-weight: 600;
  min-height: 0;          /* Hilangkan min-height kaku 18px */
  line-height: 1.25;
  margin-top: 1px;
}
```

### 3.2 Perubahan Markup Blade ([resources/views/auth/register.blade.php](file:///g:/MAGKOM/resources/views/auth/register.blade.php))

Satukan kedua field dalam kelompok kredensial dengan jarak rapat (`gap: 8px`), serta rapatkan margin teks petunjuk:

```blade
        {{-- Kelompok Kredensial Terpadu (Password & Konfirmasi) --}}
        <div style="display: flex; flex-direction: column; gap: 8px">
          {{-- Field 1: Kata Sandi --}}
          <div>
            <div style="position: relative">
              <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#5F6B76" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 16px; top: 16px"><rect x="5" y="11" width="14" height="9" rx="2"></rect><path d="M8 11V8a4 4 0 0 1 8 0v3"></path></svg>
              <input id="sandi" name="password" class="fld" type="password" autocomplete="new-password" aria-label="Kata sandi" aria-describedby="hint-sandi" placeholder="password" style="width: 100%; box-sizing: border-box; height: 50px; padding: 0 54px 0 46px; border: 1px solid #AAAAAA; border-radius: 10px; font-family: inherit; font-size: 15px; color: #212529; background: #FFFFFF">
              <button type="button" class="btn-ico" data-action="toggle-password" data-target="sandi" aria-label="Tampilkan kata sandi" style="position: absolute; right: 3px; top: 3px; width: 44px; height: 44px; border: none; border-radius: 8px; background: transparent; cursor: pointer; display: flex; align-items: center; justify-content: center">
                <svg class="pw-icon-show" aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#5F6B76" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                <svg class="pw-icon-hide" aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#5F6B76" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z"></path><circle cx="12" cy="12" r="3"></circle><path d="M4 4l16 16"></path></svg>
              </button>
            </div>
            <div class="pw-strength-wrapper" data-target-input="sandi"></div>
            <p id="hint-sandi" style="margin: 4px 0 0; font-size: 12px; line-height: 1.35; color: #5F6B76">Minimal 8 karakter kombinasi huruf dan angka.</p>
          </div>

          {{-- Field 2: Konfirmasi Kata Sandi --}}
          <div>
            <div style="position: relative">
              <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#5F6B76" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 16px; top: 16px">
                <polyline points="20 6 9 17 4 12"></polyline>
              </svg>
              <input id="sandi_confirmation" name="password_confirmation" class="fld" type="password" autocomplete="new-password" aria-label="Konfirmasi kata sandi" aria-describedby="hint-sandi-confirmation" placeholder="ulangi kata sandi" style="width: 100%; box-sizing: border-box; height: 50px; padding: 0 54px 0 46px; border: 1px solid #AAAAAA; border-radius: 10px; font-family: inherit; font-size: 15px; color: #212529; background: #FFFFFF">
              <button type="button" class="btn-ico" data-action="toggle-password" data-target="sandi_confirmation" aria-label="Tampilkan konfirmasi kata sandi" style="position: absolute; right: 3px; top: 3px; width: 44px; height: 44px; border: none; border-radius: 8px; background: transparent; cursor: pointer; display: flex; align-items: center; justify-content: center">
                <svg class="pw-icon-show" aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#5F6B76" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                <svg class="pw-icon-hide" aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#5F6B76" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z"></path><circle cx="12" cy="12" r="3"></circle><path d="M4 4l16 16"></path></svg>
              </button>
            </div>
            <p id="hint-sandi-confirmation" class="pw-match-hint" style="margin: 4px 0 0; font-size: 12px; line-height: 1.35; color: #5F6B76">Masukkan ulang kata sandi yang sama untuk konfirmasi.</p>
          </div>
        </div>
```

---

## 4. Kepatuhan Aturan Repositori MAGKOM (Rules Compliance)

1. **Rule 3 (Blade Read-Only)**:
   - Penyesuaian di Blade murni penataan container pembungkus semantik dan penyelarasan margin teks.
   - **0 baris** tag `<script>` atau `<style>` di Blade.
   - **0** inline event handler.
2. **Rule 4 (CSS di resources/css)**:
   - Seluruh aturan CSS dead space dan dimensi batang diletakkan di `resources/css/components/password-strength.css`.
   - Menguntungkan semua halaman lain (Reset Password, Ganti Password, SuperAdmin Modal, Pengaturan) karena menghilangkan dead space 18px saat input kosong di semua form.
3. **Rule 6 & 11 (Vite & Quality Gate)**:
   - Jalankan `npm run build` untuk memverifikasi kompilasi aset.
   - Jalankan automated tests `RegisterPasswordConfirmationTest` untuk memastikan tidak ada perubahan id/name yang memengaruhi pengujian.

---

## 5. Rencana Langkah Kerja Eksekusi (Checklist)

- [x] **Langkah 1: Perbarui CSS Spasi & Dead Space Removal**
  - Buka [resources/css/components/password-strength.css](file:///g:/MAGKOM/resources/css/components/password-strength.css).
  - Tambahkan rule `.pw-strength-wrapper:not([data-level]) .pw-strength-label-row { display: none; }`.
  - Ubah tinggi bar menjadi `4px`, margin-top menjadi `5px`, gap menjadi `4px`, dan min-height label row menjadi `0`.
- [x] **Langkah 2: Penyesuaian Spasi di Blade Form Register**
  - Buka [resources/views/auth/register.blade.php](file:///g:/MAGKOM/resources/views/auth/register.blade.php).
  - Bungkus `#sandi` dan `#sandi_confirmation` dalam container fleksibel dengan `gap: 8px`.
  - Sesuaikan margin teks petunjuk `#hint-sandi` dan `#hint-sandi-confirmation` menjadi `margin: 4px 0 0; font-size: 12px; line-height: 1.35;`.
- [x] **Langkah 3: Kompilasi Aset Frontend (Vite Build)**
  - Jalankan `npm run build` untuk memverifikasi bundle CSS & JS terkompilasi bersih.
- [x] **Langkah 4: Jalankan Automated Test Suite QA**
  - Jalankan `php artisan test --filter=RegisterPasswordConfirmationTest`.
  - Jalankan `php artisan test --filter=AjaxLoginAndStrengthMeterTest`.
  - Pastikan seluruh assertion lulus 100% (PASS).

---

## 6. Kriteria Selesai (Definition of Done)

- [x] Jarak vertikal antara kotak input `password` dan `ulangi kata sandi` terpangkas sekitar 50% (~36px vs ~74px sebelumnya).
- [x] Tidak ada ruang kosong (*dead space*) di bawah 3 batang abu-abu saat password belum diketik.
- [x] Saat pengguna mulai mengetik, label indikator kekuatan kata sandi ("Lemah" / "Sedang" / "Kuat") tampil secara mulus tanpa membuat form menjadi terlalu renggang.
- [x] Teks petunjuk konfirmasi kata sandi dan status pencocokan realtime tetap berfungsi normal.
- [x] `npm run build` sukses 100% dan seluruh automated feature test lulus (PASS).
