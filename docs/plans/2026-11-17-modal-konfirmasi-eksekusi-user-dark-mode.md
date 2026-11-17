# PLAN PERANCANGAN & IMPLEMENTASI
## Standarisasi Modal Dialog Konfirmasi Interaktif (Eliminasi `window.confirm` Native Browser) Berbasis DS-MagKom & Kompatibilitas Penuh Dark Mode

- **Dokumen Referensi:** PRD MagKom v1.2, DS-MagKom (`docs/design/design-system.md`), WCAG 2.1 AA
- **Tanggal Rencana:** 17 November 2026
- **Status Rencana:** Menunggu Persetujuan Pengguna (*Pending Approval*)
- **Target Utama:** Aksi Eksekusi Pengguna pada Verifikasi Berkas Pendaftaran Admin (`resources/views/admin/pendaftaran/show.blade.php`), serta standarisasi token CSS modal konfirmasi lintas modul MagKom (termasuk Dark Mode).

---

## 1. Tujuan

1. **Eliminasi Dialog Native Browser**: Menggantikan dialog native browser (`window.confirm()`) pada aksi eksekusi pengguna (seperti pada tangkapan layar pertama: `127.0.0.1:8000 says: Apakah Anda yakin menerima berkas calon peserta ini?`) dengan **Custom Modal Dialog UI** berstandar DS-MagKom (seperti pada tangkapan layar kedua: Modal Konfirmasi Berbasis Kartu Aksen).
2. **Kepatuhan Penuh Dark Mode (`[data-theme="dark"]`)**: Menghilangkan warna terang/pink neon statis (`#FFF1F2`, dsb.) yang menyilaukan mata saat mode gelap aktif. Memetakan seluruh warna latar, garis batas, teks pertanyaan, kotak entitas sasaran, dan tombol ke variabel tema Bento Navy (`--dm-surface`, `--dm-border`, `--dm-text-title`, `--dm-text-muted`) serta *tint* transparan yang elegan.
3. **Pemisahan Lapisan Arsitektur (Strict AGENTS.md Rules 3, 4, 5)**:
   - **Zero CSS/JS di Blade**: Seluruh styling modal dialokasikan ke `resources/css/components/confirm-modal.css` yang diimpor ke `resources/css/app.css` (Vite).
   - **Modular ES Modules**: Interaksi pembukaan, penutupan, pembatalan, dan eksekusi form ditangani secara modular di `resources/js/pages/admin/pendaftaran-show.js`.
   - **Aksesibilitas (WCAG 2.1 AA)**: Dilengkapi atribut semantik `role="dialog"`, `aria-modal="true"`, penanganan tombol `Escape`, klik di luar backdrop, dan *focus management*.

---

## 2. Asumsi & Analisis Kebutuhan

### 2.1 Asumsi Kerja Logis
1. **Pemicu Utama Kasus**: Pada halaman `admin/pendaftaran/show.blade.php`, terdapat formulir keputusan verifikasi dengan 3 tombol aksi:
   - **Diterima** (`keputusan=terima`) &rarr; Varian *Success / Primary* (Biru/Hijau)
   - **Minta Perbaikan Berkas** (`keputusan=perlu_perbaikan`) &rarr; Varian *Warning* (Amber/Emas)
   - **Tolak Pengajuan Pendaftaran** (`keputusan=tolak`) &rarr; Varian *Danger* (Merah Kirmisi)
   Saat ini tombol-tombol tersebut memiliki atribut `data-confirm="..."` yang diproses oleh `resources/js/pages/admin/pendaftaran-show.js:229` menggunakan fungsi bawaan `confirm(message)`.
2. **Kebutuhan Validasi Form Sebelum Modal**:
   Untuk opsi "Perlu Perbaikan" dan "Tolak", field `catatan` wajib diisi minimal 10 karakter. Validasi form sisi klien harus dipastikan valid terlebih dahulu sebelum modal konfirmasi terbuka, sehingga pengguna tidak mengonfirmasi form yang tidak lengkap.
3. **Dukungan Dark Mode**:
   Berdasarkan `docs/design/design-system.md`, kanvas dark mode MagKom menggunakan Bento Navy (`#061630` dan `#0E2548`). Modal konfirmasi wajib menggunakan token `--dm-surface` sebagai background kartu dialog dan latar semi-transparan untuk callout alert box agar tidak kontras menyilaukan.

### 2.2 Pertanyaan Terbuka / Opsional
- *Tidak ada ambiguitas kritis*: Spesifikasi visual sudah sangat jelas dari foto referensi (Image 2) dan konteks tombol verifikasi (Image 1).

---

## 3. Pendekatan Teknis & Evaluasi Arsitektur

### Perbandingan Opsi Arsitektur:

| Kriteria Evaluasi | Opsi A: Hybrid CSS Component + Blade Dialog Container + Scoped JS (Direkomendasikan) | Opsi B: Pure Dynamic DOM Injection via JavaScript Runtime |
|---|---|---|
| **Struktur Markup** | Dialog ditulis secara deklaratif di Blade (`admin/pendaftaran/show.blade.php`), markup bersih tanpa inline CSS/JS. | Elemen dialog dibuat dinamis saat runtime via `document.createElement()`. |
| **Aksesibilitas (a11y)** | Sangat baik: Elemen modal sudah ada di DOM, ramah screen reader, atribut ARIA statis dan terprediksi. | Rentan race condition pada pembacaan DOM virtual screen reader jika tidak di-trap dengan teliti. |
| **Separasi Kode (AGENTS.md)** | **100% Patuh**: Styling di `resources/css/components/confirm-modal.css`, logika di `resources/js/pages/admin/pendaftaran-show.js`. | Logika JS menjadi gemuk karena harus menyusun DOM HTML, SVG string, dan kelas elemen di runtime. |
| **Dark Mode Rendering** | **Instan & Konsisten**: Berjalan native melalui CSS selector `[data-theme="dark"]` tanpa komputasi JS tambahan. | Membutuhkan pengecekan status tema di JavaScript untuk pewarnaan inline dinamis. |
| **Dukungan Reusabilitas** | CSS class `.magkom-modal-*` dapat langsung dipakai ulang oleh modal konfirmasi SuperAdmin (Bidang, Staf, Jurusan, dll.). | Terisolasi pada script generator JS. |

> **Rekomendasi Arsitektur:** **Opsi A (Hybrid CSS Component + Blade Dialog Container + Scoped JS)** dipilih karena menjamin kepatuhan ketat terhadap *Permanent AI Development Rules*, performa 60 FPS tanpa overhead manipulasi DOM berulang, dan keselarasan penuh dengan Design System MagKom.

---

## 4. Spesifikasi Desain & Dark Mode

### 4.1 Struktur Anatomi Modal (Sesuai Foto Referensi 2)
```text
┌─────────────────────────────────────────────────────────────┐
│  [Icon Badge] Judul Dialog                               [X]│  <-- Header
│               Subjudul Dialog                               │
├─────────────────────────────────────────────────────────────┤
│  ┌───────────────────────────────────────────────────────┐  │
│  │ [Pesan Pertanyaan Konfirmasi]                         │  │  <-- Alert Callout Box
│  │ ┌───────────────────────────────────────────────────┐ │  │
│  │ │ Identitas Sasaran (Nama Peserta / No. Registrasi) │ │  │  <-- Target Entity Box
│  │ └───────────────────────────────────────────────────┘ │  │
│  │ • Rincian konsekuensi operasional tindakan            │  │  <-- Consequence Bullet
│  └───────────────────────────────────────────────────────┘  │
├─────────────────────────────────────────────────────────────┤
│                                      [ Batal ]  [ Ya, ... ] │  <-- Footer Aksi
└─────────────────────────────────────────────────────────────┘
```

### 4.2 Matriks Warna: Light Mode vs Dark Mode

| Elemen Modal | Varian | Tampilan Light Mode | Tampilan Dark Mode (`[data-theme="dark"]`) |
|---|---|---|---|
| **Backdrop** | Universal | `rgba(15, 23, 42, 0.6)` + `blur(4px)` | `rgba(3, 27, 64, 0.75)` + `blur(4px)` |
| **Dialog Card** | Universal | `#FFFFFF`, border `#CBD5E1`, shadow | `var(--dm-surface, #0E2548)`, border `rgba(255,255,255,0.08)` |
| **Judul Heading** | Universal | `var(--neutral-900, #0F172A)` | `var(--dm-text-title, #EAF0FA)` |
| **Subjudul** | Universal | `var(--neutral-500, #64748B)` | `var(--dm-text-muted, #9FB0CC)` |
| **Tombol Close (X)** | Universal | `#94A3B8` (hover: `#334155`) | `#5E7194` (hover: `#FFFFFF`) |
| **Tombol Batal** | Universal | Latar `#F1F5F9`, border `#CBD5E1`, teks `#334155` | Latar `rgba(255,255,255,0.06)`, border `rgba(255,255,255,0.14)`, teks `#EAF0FA` |
| **Target Entity Box** | Universal | Latar `#FFFFFF` / `rgba(255,255,255,0.85)`, border halus | Latar `rgba(6, 22, 48, 0.65)`, border `rgba(255,255,255,0.12)`, teks `#EAF0FA` |
| **Alert Callout** | **Danger (Tolak)** | Latar `#FFF1F2`, border `#FECDD3`, teks `#9F1239` | Latar `rgba(220, 38, 38, 0.12)`, border `rgba(239, 68, 68, 0.25)`, teks `#FCA5A5` |
| **Alert Callout** | **Warning (Revisi)** | Latar `#FFFBEB`, border `#FDE68A`, teks `#92400E` | Latar `rgba(245, 158, 11, 0.12)`, border `rgba(245, 158, 11, 0.25)`, teks `#FCD34D` |
| **Alert Callout** | **Success (Terima)** | Latar `#F0FDF4`, border `#BBF7D0`, teks `#166534` | Latar `rgba(16, 185, 129, 0.12)`, border `rgba(16, 185, 129, 0.25)`, teks `#6EE7B7` |
| **Tombol Submit** | **Danger** | `#DC2626` (hover: `#B91C1C`), teks `#FFFFFF` | `#DC2626` (hover: `#EF4444`), teks `#FFFFFF` |
| **Tombol Submit** | **Warning** | `#D97706` (hover: `#B45309`), teks `#FFFFFF` | `#D97706` (hover: `#F59E0B`), teks `#FFFFFF` |
| **Tombol Submit** | **Success/Primary**| `#0641CC` / `#16A34A`, teks `#FFFFFF` | `#2563EB` / `#10B981`, teks `#FFFFFF` |

---

## 5. Rencana Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/css/components/confirm-modal.css` | **Create** | Definisi kelas komponen modal konfirmasi (`.magkom-modal-*`) lengkap dengan varian *danger*, *warning*, *success*, dan adaptasi token `[data-theme="dark"]`. |
| `resources/css/app.css` | **Modify** | Registrasi `@import './components/confirm-modal.css';` ke dalam pipeline CSS utama Vite. |
| `resources/views/admin/pendaftaran/show.blade.php` | **Modify** | Penambahan struktur semantik kontainer modal konfirmasi keputusan pendaftaran (`#modalKonfirmasiKeputusanAdmin`) sebelum penutup `@endsection` (Zero inline styling / inline script). |
| `resources/js/pages/admin/pendaftaran-show.js` | **Modify** | Menghapus pemanggilan `confirm(message)` bawaan browser; mengintegrasikan event listener interseptor form keputusan pendaftaran untuk memvalidasi input, mengisi konten modal secara kontekstual, membuka modal custom, dan mengeksekusi submit form saat disetujui. |

> ⚠️ **Catatan Aturan Blade (Rule 3.3)**: Modifikasi pada `resources/views/admin/pendaftaran/show.blade.php` hanya berupa penambahan kerangka modal HTML semantik di bagian bawah halaman. Tidak ada CSS `<style>`, tidak ada JavaScript `<script>`, dan tidak ada inline event handler (`onclick`/`onsubmit`).

---

## 6. Langkah-Langkah Implementasi Bertahap

- [x] **Langkah 1: Pembuatan Stylesheet Komponen Modal (`resources/css/components/confirm-modal.css`)**
  - Buat file CSS baru `resources/css/components/confirm-modal.css`.
  - Implementasikan styling untuk:
    - `.magkom-modal-backdrop` (flex centering, fixed full inset, backdrop-filter blur).
    - `.magkom-modal-dialog` (max-width 480px, border-radius 16px, shadow modal, transisi fade/scale).
    - `.magkom-modal-header`, `.magkom-modal-icon-badge`, `.magkom-modal-title`, `.magkom-modal-subtitle`, `.magkom-modal-close`.
    - `.magkom-modal-callout` beserta varian modifier: `.variant-danger`, `.variant-warning`, `.variant-success`, `.variant-primary`.
    - `.magkom-modal-target-box` (kotak highlight nama peserta/entitas).
    - `.magkom-modal-consequence` (catatan rincian efek aksi berbutir bullet).
    - `.magkom-modal-footer` dan varian tombol `.magkom-modal-btn-cancel`, `.magkom-modal-btn-confirm`.
  - Implementasikan blok selector `[data-theme="dark"]` yang memetakan seluruh kelas di atas ke palet Bento Navy `--dm-*`.

- [x] **Langkah 2: Integrasi ke Pipeline Vite (`resources/css/app.css`)**
  - Tambahkan `@import './components/confirm-modal.css';` di bawah section `/* Shared Industrial Components */`.
  - Jalankan `npm run build` untuk memverifikasi tidak ada error sintaks CSS atau kegagalan bundling Vite.

- [x] **Langkah 3: Penambahan Markup Modal Semantik (`resources/views/admin/pendaftaran/show.blade.php`)**
  - Tempatkan markup `<div id="modalKonfirmasiKeputusanAdmin" class="magkom-modal-backdrop" role="dialog" aria-modal="true" style="display: none;">` di akhir view.
  - Sediakan elemen target dinamis dengan ID terstruktur:
    - `#confirmModalIconWrap` (kontainer ikon badge)
    - `#confirmModalTitle` (judul dialog)
    - `#confirmModalSubtitle` (subjudul informasi)
    - `#confirmModalCallout` (kotak alert callout)
    - `#confirmModalQuestion` (teks pertanyaan konfirmasi)
    - `#confirmModalTargetName` (nama kandidat & nomor registrasi)
    - `#confirmModalConsequence` (rincian efek tindakan)
    - `#confirmModalCancelBtn` (tombol batal)
    - `#confirmModalSubmitBtn` & `#confirmModalSubmitText` (tombol konfirmasi eksekusi)
  - Bersihkan sisa atribut `data-confirm` pada tombol form keputusan agar tidak memicu fallback browser.

- [x] **Langkah 4: Pembaruan Logika Interaksi (`resources/js/pages/admin/pendaftaran-show.js`)**
  - Buat fungsi pembantu penanganan modal: `openConfirmModal(config)`, `closeConfirmModal()`.
  - Modifikasi event handler submit formulir keputusan (`#decision-form`):
    - Intersep submit default dengan `e.preventDefault()`.
    - Jika keputusan adalah `perlu_perbaikan` atau `tolak`, periksa validitas textarea `catatan` (&ge; 10 karakter). Jika belum valid, fokuskan ke textarea dan hentikan pembukaan modal.
    - Konfigurasi isi modal berdasarkan keputusan yang aktif:
      - **Terima**: Judul "Konfirmasi Penerimaan Berkas", Ikon centang, Varian success/primary, Catatan konsekuensi pemindahan ke alokasi pembimbing.
      - **Perlu Perbaikan**: Judul "Konfirmasi Permintaan Perbaikan", Ikon peringatan, Varian warning, Menampilkan ringkasan catatan koreksi.
      - **Tolak**: Judul "Konfirmasi Penolakan Pendaftaran", Ikon bahaya segitiga, Varian danger, Peringatan bahwa penolakan bersifat permanen.
    - Tampilkan modal dengan animasi bersih.
  - Pasang event listener pada tombol Batal, tombol Close (X), klik pada backdrop di luar dialog, dan tombol keyboard `Escape`.
  - Saat tombol konfirmasi di dalam modal diklik, lakukan submit aktual form melalui `form.submit()`.

- [x] **Langkah 5: Quality Gate & Verifikasi Frontend**
  - Jalankan `npm run build` untuk menguji kelancaran aset Vite.
  - Jalankan `./vendor/bin/pint --test` untuk memastikan kebersihan format kode.
  - Jalankan automated test suite `php artisan test --filter=AdminPendaftaranTest` untuk memastikan seluruh kontrak backend tidak terganggu.

---

## 7. Strategi Testing

1. **Uji Fungsionalitas Browser (Playwright / Manual Inspection)**:
   - Skenario A: Mengklik tombol "Diterima" &rarr; Dialog konfirmasi custom muncul dengan aksen biru/hijau, memuat nama kandidat & nomor registrasi. Menekan "Batal" menutup dialog tanpa submit. Menekan "Ya, Diterima" mengeksekusi form penerimaan.
   - Skenario B: Memilih tab "Perlu Perbaikan" &rarr; Membiarkan catatan kosong lalu klik tombol submit &rarr; Modal tidak muncul, field catatan mendapatkan peringatan validasi. Mengisi catatan valid (&ge; 10 karakter) lalu klik submit &rarr; Modal muncul dengan aksen kuning/amber.
   - Skenario C: Memilih tab "Tolak" &rarr; Klik submit dengan alasan valid &rarr; Modal muncul dengan aksen merah kirmisi dan teks penegasan konsekuensi penolakan permanen.
2. **Uji Mode Gelap (Dark Mode Switch Verification)**:
   - Aktifkan toggle dark mode (`[data-theme="dark"]`).
   - Buka modal konfirmasi pada ketiga varian (Terima, Perlu Perbaikan, Tolak).
   - Verifikasi rasio kontras teks (WCAG 2.1 AA &ge; 4.5:1), pastikan callout box tidak berlatar belakang putih atau merah muda terang yang menyilaukan.
3. **Uji Keyboard & Aksesibilitas**:
   - Tekan tombol `Escape` saat modal terbuka &rarr; Modal tertutup secara aman.
   - Klik di luar area dialog (backdrop) &rarr; Modal tertutup secara aman.

---

## 8. Risiko & Mitigasi

| Potensi Risiko | Tingkat | Mitigasi |
|---|---|---|
| **Form Gagal Submit Setelah Konfirmasi** | Menengah | Gunakan flag konfirmasi internal atau pemanggilan `HTMLFormElement.prototype.submit.call(form)` untuk memastikan form terkirim tanpa terjebak dalam loop `preventDefault()`. |
| **Kelelahan Visual pada Dark Mode** | Rendah | Menggunakan warna berbasis `rgba(...)` dengan opasitas rendah (12%&ndash;15%) untuk latar belakang callout, dipadukan dengan teks berbobot semibold dengan kontras tinggi. |
| **Pelanggaran Aturan Blade (Inline CSS/JS)** | Rendah | Semua selektor dan animasi disimpan di file CSS mandiri (`confirm-modal.css`), tidak ada atribut `style="..."` statis baru di Blade. |

---

## 9. Kriteria Selesai (Definition of Done)

- [x] Dialog native `window.confirm()` bawaan browser pada aksi penerimaan, permintaan perbaikan, dan penolakan berkas telah 100% dieliminasi.
- [x] Tampilan modal konfirmasi memiliki tata letak, icon badge, callout box, kotak target entitas, dan tombol yang presisi seperti pada foto referensi (Image 2).
- [x] Modal sepenuhnya adaptif dan nyaman dipandang pada tema **Dark Mode** (`[data-theme="dark"]`) tanpa elemen warna neon atau teks yang bertabrakan.
- [x] Tidak ada penulisan CSS statis maupun JavaScript di dalam file Blade (patuh AGENTS.md).
- [x] Aset Vite terkompilasi sukses via `npm run build`.
- [x] Seluruh automated test `php artisan test` tetap lulus (*green*).
