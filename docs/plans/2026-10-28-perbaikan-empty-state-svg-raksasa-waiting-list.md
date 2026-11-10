# Rencana Kerja: Perbaikan Bug Ikon Raksasa (Oversized SVG Empty State) pada Halaman Waiting List

> **Tanggal Dokumen**: 28 Oktober 2026  
> **Status**: Menunggu Persetujuan User  
> **Area Terdampak**: Admin Waiting List (`/admin/waiting-list`), Komponen Blade `x-empty-state`, Global CSS Guardrail  

---

## 1. Tujuan
Memperbaiki permasalahan tampilan ikon raksasa (*oversized SVG empty state*) pada modul **Antrean Waiting List Admin** (`/admin/waiting-list`), menstandarkan komponen Blade `<x-empty-state>` dengan atribut dimensi eksplisit dan *inline style fallbacks*, serta meremajakan tampilan *empty state* antrean tunggu agar selaras dengan tema khas Sleman Signature Theme (Dinas Kominfo Sleman).

---

## 2. Analisis Akar Masalah (Root Cause)
1. **Ketidakcocokan Ekosistem CSS (Tailwind vs Gentelella)**:
   Komponen `resources/views/components/empty-state.blade.php` dibangun menggunakan kelas utilitas Tailwind CSS murni (`w-16 h-16`, `w-8 h-8`, `flex flex-col`, `rounded-2xl`). Sementara itu, halaman Admin Waiting List mewarisi layout `layouts.dashboard` (Gentelella/Bootstrap) yang **tidak** mengompilasi pustaka Tailwind CSS tersebut.
2. **Ketiadaan Atribut Dimensi Eksplisit pada SVG**:
   Elemen `<svg class="w-8 h-8" viewBox="0 0 24 24" ...>` tidak memiliki atribut `width` dan `height` numerik. Karena parent container berstatus `display: block` selebar kartu tabel (~1000px+), browser secara otomatis mengekspansi SVG sesuai rasio 1:1 dari `viewBox`, menyebabkan ikon inbox membengkak hingga memenuhi layar dan mendorong seluruh teks informasi ke bawah.
3. **Prop Mismatch Subtitle pada Panel**:
   Di file `resources/views/admin/waiting-list/index.blade.php` baris 771, komponen dipanggil dengan atribut `sub="..."`, padahal komponen `x-gentelella.x-panel` memerlukan prop `subtitle="..."`.

---

## 3. Asumsi Kerja Logis
- Kondisi antrean kosong (*empty state*) terjadi ketika tidak ada calon peserta magang berstatus `waiting_list`, atau saat kriteria filter pencarian menghasilkan 0 baris data.
- Komponen `empty-state.blade.php` harus tetap kompatibel dengan halaman lain jika kelak digunakan di layout berbasis Tailwind, tanpa merusak fungsionalitas aslinya.
- Desain antrean kosong harus konsisten dengan halaman verifikasi pendaftaran (`admin/pendaftaran/index.blade.php`) yang telah terstandardisasi dengan Sleman Signature Theme.

---

## 4. Evaluasi Pendekatan Teknis

| Pendekatan | Kelebihan | Kekurangan | Rekomendasi |
| :--- | :--- | :--- | :--- |
| **Opsi A: Perbaikan Lokal di `waiting-list/index.blade.php` Saja** | Sangat cepat dan terisolasi pada halaman waiting list. | Masalah pada `empty-state.blade.php` tidak tertangani, sehingga dapat terulang di halaman lain. | Tidak Direkomendasikan |
| **Opsi B: Pertahanan Berlapis (Defense-in-Depth) Komponen + Halaman + CSS Guardrail** | Menuntaskan akar masalah permanen di level komponen, mempercantik UI/UX waiting list, dan memasang guardrail CSS global. | Menyentuh 3-4 file terarah. | **Sangat Direkomendasikan (Pilihan Utama)** |

---

## 5. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/components/empty-state.blade.php` | Modify | Menambahkan atribut dimensi eksplisit (`width="32" height="32"`), inline style fallback pada container dan SVG agar tidak membengkak tanpa Tailwind. |
| `resources/views/admin/waiting-list/index.blade.php` | Modify | Menyelaraskan tampilan *empty state* dengan Sleman Signature Theme, membedakan kondisi kosong vs filter nihil, menambahkan tombol reset filter, serta memperbaiki prop `subtitle` pada `<x-gentelella.x-panel>`. |
| `public/css/dashboard-cockpit-unified.css` | Modify | Menambahkan aturan pengaman CSS (*guardrail*) global untuk mencegah SVG tak berdimensi membengkak di dalam area admin. |
| `tests/Feature/AdminChildPagesExtendedVisualTest.php` | Modify | Memperkaya pengujian untuk memastikan halaman waiting list saat kosong menampilkan pesan dan ikon proporsional tanpa error. |

---

## 6. Langkah Implementasi Terperinci

### Fase 1: Perbaikan Komponen Blade `components/empty-state.blade.php`
- [x] Tambahkan atribut `width="32" height="32"` dan `style="width: 32px; height: 32px; max-width: 32px; max-height: 32px;"` pada tag `<svg>` bawaan.
- [x] Tambahkan inline CSS fallback pada pembungkus ikon: `style="width: 56px; height: 56px; display: inline-flex; align-items: center; justify-content: center; border-radius: 16px; margin: 0 auto 1rem auto; background: #F8FAFC; border: 1px solid #E2E8F0;"`.
- [x] Tambahkan inline CSS fallback pada container utama agar terpusat rapi: `style="text-align: center; padding: 3rem 1.5rem; display: flex; flex-direction: column; align-items: center; justify-content: center;"`.

### Fase 2: Peremajaan Halaman `admin/waiting-list/index.blade.php`
- [x] Perbaiki pemanggilan panel pada baris 771: ubah `sub="..."` menjadi `subtitle="Penegakan transparansi dan objektivitas penempatan magang"`.
- [x] Rancang ulang blok `@if($waitingList->isEmpty())` dengan layout responsif Sleman Signature:
  - Ikon jam/antrean yang proporsional dan elegan (`width="28" height="28"`, latar `#EFF6FF`, warna `#0641CC`).
  - Judul kontekstual:
    - Jika filter aktif: *"Tidak Ada Calon Peserta yang Cocok"*
    - Jika antrean kosong murni: *"Tidak Ada Antrean Waiting List"*
  - Deskripsi informatif mengenai kuota dan sistem antrean FIFO (BR-05).
  - Tombol interaktif *"Reset Filter"* yang hanya muncul saat pengguna sedang melakukan filter/pencarian.
- [x] Pastikan styling dark mode (`[data-theme="dark"]`) mendukung kontras warna yang nyaman dan memenuhi standar WCAG.

### Fase 3: Pengamanan CSS Global di `dashboard-cockpit-unified.css`
- [x] Tambahkan CSS guardrail:
  ```css
  /* GUARDRAIL: Cegah SVG tak berdimensi membengkak di lingkungan non-Tailwind */
  .adm-table-card svg:not([width]),
  .x_content svg:not([width]) {
      max-width: 48px;
      max-height: 48px;
  }
  ```

### Fase 4: Pengujian & Verifikasi
- [x] Eksekusi automated test:
  ```powershell
  php artisan test --filter=AdminChildPagesExtendedVisualTest
  php artisan test --filter=AdminChildPagesVisualTest
  ```
- [x] Verifikasi halaman `/admin/waiting-list` di browser: ikon tampil proporsional, rapi, dan tidak ada elemen yang membengkak.

---

## 7. Strategi Pengujian (Testing Strategy)
1. **Automated Feature Tests**:
   - `AdminChildPagesExtendedVisualTest`: Memastikan `route('admin.waiting-list.index')` merespons HTTP 200, menampilkan teks judul, deskripsi antrean kosong, dan struktur tabel.
   - `AdminTerminologyDesignSystemTest`: Memastikan terminologi dan tata letak tetap memenuhi standar desain MagKom.
2. **Visual & UI Verification**:
   - Uji kondisi antrean kosong (0 data).
   - Uji kondisi filter pencarian tanpa hasil (menampilkan tombol reset).
   - Uji tema terang (*light mode*) dan tema gelap (*dark mode*).

---

## 8. Risiko & Mitigasi
- **Risiko**: Perubahan pada `components/empty-state.blade.php` memengaruhi komponen lain.
  - **Mitigasi**: Inline styles yang ditambahkan bersifat protektif/fallback tanpa menghapus kelas Tailwind yang sudah ada, sehingga komponen tetap bekerja sempurna baik di lingkungan Tailwind maupun lingkungan non-Tailwind.
- **Risiko**: Inkonsistensi gaya dengan halaman verifikasi pendaftaran.
  - **Mitigasi**: Mengadopsi token warna dan pola layout yang identik dengan `admin/pendaftaran/index.blade.php`.

---

## 9. Kriteria Selesai (Definition of Done)
- [x] Ikon pada halaman Waiting List tampil dalam dimensi proporsional (28–48px), tidak lagi berukuran raksasa.
- [x] Komponen `x-empty-state` terlindungi dengan atribut dimensi eksplisit dan inline fallback.
- [x] Panel Waiting List menampilkan subtitle dengan tepat.
- [x] Pesan antrean kosong tampil informatif dan memiliki tombol reset saat terfilter.
- [x] Seluruh pengujian otomatis (`php artisan test`) berhasil 100% tanpa regresi.

