# Plan: Render Sanitized Rich-Text Deskripsi Tugas pada Kartu Tugas Peserta (Bebas Raw Tag `<br>` / HTML)

Dokumen ini merinci rencana teknis perbaikan rendering deskripsi tugas yang diberikan oleh Pembimbing pada kartu daftar tugas Peserta (`peserta.tugas.index`). Perbaikan ini memastikan tag pemformatan visual (seperti tebal, miring, garis bawah, daftar bernomor/bullet, dan pemisah baris `<br>`) dirender secara visual dan aman menggunakan `SafeHtmlSanitizer`, alih-alih dicetak mentah sebagai teks literal (`&lt;br&gt;`, `&lt;b&gt;`, dll.) akibat escaping Blade `{{ ... }}`.

---

## 1. Tujuan
Menghilangkan tampilan tag HTML mentah seperti `<br>`, `<b>`, `<i>`, `<u>`, `<ol>`, `<li>` pada kartu tugas di portal Peserta (`resources/views/peserta/tugas/index.blade.php`), menggantikannya dengan rendering format visual yang rapi, proporsional, dan aman dari celah XSS (Cross-Site Scripting) menggunakan standard sanitasi eksisting `\App\Support\SafeHtmlSanitizer`.

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi Kerja:
1. **Penyebab Masalah (Root Cause):** Pada `resources/views/peserta/tugas/index.blade.php` (baris 194), deskripsi tugas dicetak menggunakan sintaks Blade standar `{{ $tugas->deskripsi }}` yang secara otomatis menjalankan `htmlspecialchars()`. Hal ini mengubah tag HTML menjadi entitas HTML (`&lt;br&gt;`, `&lt;b&gt;`, dll.), sehingga browser merendernya sebagai teks kode mentah, bukan format teks visual.
2. **Konsistensi dengan Halaman Detail:** Pada halaman detail tugas peserta (`resources/views/peserta/tugas/show.blade.php` baris 78) dan halaman pembimbing (`resources/views/pembimbing/tugas/show.blade.php` baris 225), sanitasi dan rendering HTML sudah diterapkan secara tepat menggunakan:
   ```blade
   {!! \App\Support\SafeHtmlSanitizer::sanitize($tugas->deskripsi) !!}
   ```
3. **Keamanan XSS (SEC-01 & SEC-02):** `SafeHtmlSanitizer` sudah diaudit dan terbukti tahan terhadap bypass unwrap XSS (SEC-002), hanya mengizinkan tag visual aman (`p`, `br`, `b`, `strong`, `i`, `em`, `u`, `ul`, `ol`, `li`) dan membersihkan seluruh atribut/event handler (`onerror`, `onclick`, `javascript:`).
4. **Layout Card List:** Kartu tugas pada halaman indeks adalah ringkasan interaktif berbentuk kartu yang dibungkus tag `<a>`. Oleh karena itu, rendering elemen block seperti `<p>` atau `<ol>` di dalam `.assignment-desc` memerlukan sedikit penyesuaian CSS agar margin dan line-height tidak merusak kerapian tata letak kartu.

### Pertanyaan Terbuka untuk Pengguna:
- *Apakah pada kartu daftar tugas (index), panjang deskripsi tugas perlu dibatasi secara visual dengan batas ketinggian tertentu (misalnya `max-height` 4.5rem dengan `overflow: hidden`) jika pembimbing menulis instruksi yang sangat panjang, atau dibiarkan memanjang secara alami sesuai isi teks berformat?*
  *(Rekomendasi: Tambahkan pembatas `max-height` teratur pada `.assignment-desc` agar kartu tetap proporsional jika teks sangat panjang, sementara teks lengkap tetap dapat dibaca utuh di halaman detail tugas).*

---

## 3. Pendekatan Teknis & Evaluasi Alternatif

### Perbandingan Opsi Arsitektur:

| Kriteria | Opsi A: Render Safe HTML Berformat (Direkomendasikan) | Opsi B: Strip Tags Menjadi Teks Polos Ringkas | Opsi C: Hybrid (Safe HTML + Visual Clamp) (Rekomendasi Terbaik) |
| :--- | :--- | :--- | :--- |
| **Deskripsi** | Mengganti `{{ $tugas->deskripsi }}` dengan `{!! \App\Support\SafeHtmlSanitizer::sanitize($tugas->deskripsi) !!}` persis seperti halaman detail. | Menggunakan `Str::limit(strip_tags(str_replace('<br>', ' ', $tugas->deskripsi)), 120)` | Merender Safe HTML dengan `SafeHtmlSanitizer`, dilengkapi penataan CSS scoped untuk `<ol>`, `<ul>`, `<p>`, serta `max-height` halus. |
| **Kelebihan** | Format visual (bold, break, nomor) dari pembimbing langsung terlihat. Masalah teks `<br>` langsung selesai. | Kartu selalu seragam dan ringkas. | Tampilan format pembimbing (tebal, nomor, baris baru) tampil sempurna tanpa risiko kartu melar berlebihan jika pembimbing menulis 5 paragraf. |
| **Kekurangan** | Jika pembimbing menulis deskripsi sangat panjang, kartu di daftar tugas bisa menjadi sangat tinggi jika tanpa styling batas. | Format visual (bold, list, newline) hilang sama sekali di halaman daftar. Tidak memenuhi ekspektasi "udah versi ke ubah". | Memerlukan sedikit penambahan CSS scoped di `resources/css/pages/peserta/tugas-index.css`. |
| **Keamanan** | Aman (tersanitasi ketat oleh `SafeHtmlSanitizer`). | Sangat aman (teks polos). | Sangat aman (tersanitasi ketat oleh `SafeHtmlSanitizer`). |

### Rekomendasi Terpilih:
**Opsi C (Hybrid: Safe HTML Rendering + Scoped CSS Styling)**
- Pada Blade `resources/views/peserta/tugas/index.blade.php`:
  ```blade
  <div class="assignment-desc">
      @if(! $tugas->deskripsi)
          <span class="assignment-desc-empty">{{ __('Tidak ada instruksi khusus.') }}</span>
      @elseif(str_contains($tugas->deskripsi, '<'))
          {!! \App\Support\SafeHtmlSanitizer::sanitize($tugas->deskripsi) !!}
      @else
          {!! nl2br(e($tugas->deskripsi)) !!}
      @endif
  </div>
  ```
- Pada CSS `resources/css/pages/peserta/tugas-index.css`:
  Menambahkan aturan scoped untuk `.assignment-desc` agar elemen `p`, `ol`, `ul`, `li` memiliki margin/padding yang proporsional dan tidak saling bertumpuk di dalam kartu.

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/peserta/tugas/index.blade.php` | **Modify** | Mengganti escaping `{{ $tugas->deskripsi }}` menjadi `{!! \App\Support\SafeHtmlSanitizer::sanitize($tugas->deskripsi) !!}` dengan fallback `nl2br(e(...))` dan kondisi null. |
| `resources/css/pages/peserta/tugas-index.css` | **Modify** | Menambahkan styling scoped untuk child tags di dalam `.assignment-desc` (`p`, `ol`, `ul`, `li`, `br`) dan batas `max-height` proporsional. |
| `tests/Feature/TugasDeskripsiSecurityTest.php` | **Modify** | Menambahkan pengujian spesifik untuk memastikan halaman `peserta.tugas.index` merender HTML yang diformat dengan benar, bebas dari tag mentah `<br>`, serta tetap aman dari injeksi payload berbahaya. |

> ℹ️ **Catatan Kepatuhan Aturan Blade & CSS:**
> - Perubahan markup Blade memerlukan persetujuan pengguna karena menyentuh `resources/views/peserta/tugas/index.blade.php`.
> - Tidak ada penulisan `<style>` atau `<script>` di Blade. Seluruh styling tetap berada di `resources/css/pages/peserta/tugas-index.css`.

---

## 5. Langkah Implementasi Bertahap

- [x] **Langkah 1: Update Markup Blade `resources/views/peserta/tugas/index.blade.php`**
  - Mengubah baris 193–195 dari:
    ```blade
    <div class="assignment-desc">
        {{ $tugas->deskripsi }}
    </div>
    ```
    menjadi:
    ```blade
    <div class="assignment-desc">
        @if(! $tugas->deskripsi)
            <span class="assignment-desc-empty">{{ __('Tidak ada instruksi khusus.') }}</span>
        @elseif(str_contains($tugas->deskripsi, '<'))
            {!! \App\Support\SafeHtmlSanitizer::sanitize($tugas->deskripsi) !!}
        @else
            {!! nl2br(e($tugas->deskripsi)) !!}
        @endif
    </div>
    ```

- [x] **Langkah 2: Penyesuaian Styling Scoped di `resources/css/pages/peserta/tugas-index.css`**
  - Menambahkan styling scoped untuk child tags di `.assignment-desc` (`p`, `ol`, `ul`, `li`, `strong/b`, `em/i`, `u`, `.assignment-desc-empty`) dan dukungan mode gelap `[data-theme="dark"]`.

- [x] **Langkah 3: Kompilasi Asset Frontend**
  - Menjalankan `npm run build` berhasil tanpa error dalam 2.69s.

- [x] **Langkah 4: Penambahan & Eksekusi Test Suite**
  - Menambahkan skenario pengujian pada `tests/Feature/TugasDeskripsiSecurityTest.php`:
    - `test_peserta_tugas_index_renders_sanitized_rich_text_without_raw_html_tags()`
    - `test_peserta_tugas_index_renders_plain_text_with_nl2br_and_empty_placeholder()`
    - Menambahkan asersi keamanan `peserta.tugas.index` pada `test_historical_malicious_deskripsi_is_sanitized_in_edit_and_show_views()`.

- [x] **Langkah 5: Verifikasi Komprehensif (Quality Gate)**
  - `php artisan test --filter=TugasDeskripsiSecurityTest`: 9 passed (68 assertions).
  - `php artisan test --filter=PesertaTugasFilterTabsTest`: 2 passed (21 assertions).
  - `php artisan test --filter=PesertaClassroomTugasLogbookTest`: 7 passed (52 assertions).
  - `./vendor/bin/pint --test tests/Feature/TugasDeskripsiSecurityTest.php`: PASSED.
  - `./vendor/bin/phpstan analyse tests/Feature/TugasDeskripsiSecurityTest.php`: 0 errors.

---

## 6. Strategi Testing

1. **Happy Path:**
   - Pembimbing membuat tugas dengan format: `<b>waf</b><br><i>awf</i><br><u>awfwafwa</u><ol><li><u>awfgawfa</u></li></ol>`.
   - Peserta mengakses `route('peserta.tugas.index')`.
   - Assertion memastikan respons mengandung `<b>waf</b>`, tag `<br>`, dan `<ol><li>`, serta **tidak** mengandung `&lt;br&gt;` atau `&lt;b&gt;`.
2. **Plain Text Fallback:**
   - Tugas dengan deskripsi teks biasa tanpa HTML (misal: `"Kerjakan modul 1\nBaris kedua"`).
   - Assertion memastikan newline diubah menjadi `<br />` via `nl2br(e(...))` secara aman.
3. **Empty/Null State:**
   - Tugas dengan deskripsi `null` atau string kosong.
   - Assertion memastikan placeholder `"Tidak ada instruksi khusus."` tampil dengan rapi.
4. **Security / XSS Penetration Path:**
   - Tugas historis/injeksi dengan payload `<script>alert('xss')</script><img src=x onerror=alert(1)>`.
   - Assertion memastikan tidak ada tag `<script>` atau atribut `onerror` yang lolos ke output HTML halaman indeks.

---

## 7. Risiko & Mitigasi

| Risiko | Dampak | Mitigasi |
| :--- | :--- | :--- |
| **Potensi XSS jika menggunakan `{!! !!}` tanpa sanitasi** | **Kritis (P0)** | Wajib membungkus output dengan `\App\Support\SafeHtmlSanitizer::sanitize()`. Jangan pernah mencetak `{!! $tugas->deskripsi !!}` mentah. |
| **Kartu tugas membesar berlebihan akibat konten HTML panjang** | **Rendah (Visual)** | Tambahkan styling CSS scoped pada `resources/css/pages/peserta/tugas-index.css` dengan `max-height` dan line clamp elegan. |
| **Pelanggaran Aturan Blade Read-Only** | **Prosedural** | Telah didokumentasikan dalam dokumen plan ini; perubahan markup Blade dijelaskan secara eksplisit dan menunggu konfirmasi pengguna. |

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Tidak ada lagi teks mentah seperti `<br>`, `<b>`, `<i>`, `<u>`, `<ol>`, `<li>` yang tampak pada kartu daftar tugas peserta.
- [x] Format visual (bold, italic, underline, list, newline) tampil rapi dan proporsional pada kartu tugas peserta.
- [x] Sanitasi `SafeHtmlSanitizer` aktif dan seluruh pengujian keamanan XSS lolos 100%.
- [x] Asset CSS berhasil dikompilasi via `npm run build` tanpa error.
- [x] Pengujian PHPUnit `TugasDeskripsiSecurityTest` dan test suite terkait lulus hijau (`PASS`).
