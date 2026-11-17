# Rencana Kerja: Penyelarasan Tipografi Modern Profesional & Penghapusan Garis Samping Modal Konfirmasi

| Informasi Dokumen | Nilai |
| :--- | :--- |
| **Tanggal Pembuatan** | 17 November 2026 |
| **Status Dokumen** | Draf Rencana (Menunggu Persetujuan Pengguna) |
| **Target Fitur** | Modal Konfirmasi Keputusan Pendaftaran (`resources/css/components/confirm-modal.css`) |
| **Branch Target** | `test-2` |
| **Referensi Masukan** | Gambar SS 1 (`media_1794924469448_58993eda.png`) & SS 2 (`media_1794924549240_1850b9b1.png`) |

---

## 1. Analisis Kritis Kebutuhan Pengguna

Pengguna memberikan dua arahan penyempurnaan spesifik untuk pop-up konfirmasi pendaftaran:
1. **Penggantian Font Menjadi Font Web Modern & Profesional**:
   - Font saat ini masih mewarisi tipografi standar lama (`Open Sans`), yang terasa sedikit kaku untuk komponen modal antarmuka modern.
   - Standar emas tipografi web enterprise dan produk digital saat ini (seperti Vercel, Linear, Stripe, Tailwind UI, GovTech modern) mengandalkan **Plus Jakarta Sans** atau **Inter** dipadukan dengan system fallbacks (`system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif`).
   - Font **Plus Jakarta Sans** sudah tersedia dan dimuat secara resmi pada layout dashboard MAGKOM (`resources/views/layouts/dashboard.blade.php`), sehingga dapat langsung diaktifkan dengan performa 0ms latency tanpa beban download tambahan.
2. **Penghapusan Garis Biru Samping (Impact Banner Left Accent)**:
   - Pada tangkapan layar kedua (`media_1794924549240_1850b9b1.png`), pengguna menandai garis vertikal biru tebal di sisi kiri banner informasi konsekuensi (`border-left: 3px solid #01347C`).
   - Garis vertikal ini memberi kesan kaku (*harsh divider*) dan memotong kelembutan kartu.
   - Solusi: Menghapus `border-left` tersebut sepenuhnya dan mengubah kartu informasi menjadi **Clean Soft Tint Container** dengan border halus sekeliling (`border: 1px solid rgba(1, 52, 124, 0.08)`) dan sudut membulat 12px yang elegan.

---

## 2. Rincian Rencana Perubahan Teknis

Pekerjaan ini 100% berada pada lapisan styling CSS dan mematuhi aturan `.agents/rules/AGENTS.md` (tidak mengubah Blade tanpa kebutuhan markup baru, tidak menulis inline style):

### A. Penerapan Font Modern Profesional (`Plus Jakarta Sans`)
Pada file [`resources/css/components/confirm-modal.css`](file:///d:/laragon/www/MAGKOM/resources/css/components/confirm-modal.css):
* Menetapkan tipografi modern secara menyeluruh pada root dialog:
  ```css
  .magkom-modal-dialog {
      font-family: 'Plus Jakarta Sans', 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      /* ... styling dialog ... */
  }
  ```
* Mengatur hierarki bobot (*font-weight*) dan *letter-spacing* yang tajam:
  - **Judul Modal (`.magkom-modal-title`)**: `font-weight: 700; letter-spacing: -0.015em; font-size: 1.125rem;`
  - **Subjudul (`.magkom-modal-subtitle`)**: `font-weight: 400; font-size: 0.8125rem; color: var(--neutral-500, #64748B);`
  - **Pertanyaan Konfirmasi (`.magkom-modal-question`)**: `font-weight: 500; letter-spacing: -0.01em; color: var(--neutral-700, #334155);`
  - **Nama Calon Peserta (`.magkom-modal-target-name`)**: `font-weight: 700; letter-spacing: -0.01em; font-size: 0.95rem;`
  - **Chip Nomor Registrasi (`.magkom-modal-reg-badge`)**: `font-family: 'JetBrains Mono', monospace; font-weight: 600; font-size: 0.725rem;`
  - **Institusi/Sekolah (`.magkom-modal-target-sub`)**: `font-weight: 400; font-size: 0.8rem;`
  - **Teks Konsekuensi (`.magkom-modal-impact-text`)**: `font-weight: 500; font-size: 0.8125rem; line-height: 1.5;`
  - **Tombol Aksi (`.magkom-modal-btn-cancel`, `.magkom-modal-btn-confirm`)**: `font-weight: 600 / 700; letter-spacing: 0.01em;`

### B. Penghapusan Garis Samping Biru (Border-Left Elimination)
Pada komponen `.magkom-modal-impact-banner` dan seluruh varian warnanya:
* **Hapus**: `border-left: 3px solid ...;`
* **Ganti dengan Soft Rounded Surface**:
  ```css
  .magkom-modal-impact-banner {
      display: flex;
      align-items: flex-start;
      gap: 0.75rem;
      padding: 0.875rem 1rem;
      border-radius: 12px;
      border: 1px solid transparent;
      transition: all 0.2s ease;
  }

  /* Varian Primary (Terima Berkas) */
  .magkom-modal-impact-banner.variant-primary {
      background: #F0F6FF;
      border-color: rgba(1, 52, 124, 0.08);
      color: #1E40AF;
  }

  /* Varian Danger (Tolak Berkas) */
  .magkom-modal-impact-banner.variant-danger {
      background: #FEF2F2;
      border-color: rgba(220, 38, 38, 0.08);
      color: #991B1B;
  }

  /* Dark Mode */
  [data-theme="dark"] .magkom-modal-impact-banner.variant-primary {
      background: rgba(1, 52, 124, 0.16);
      border-color: rgba(59, 130, 246, 0.2);
      color: #93C5FD;
  }
  ```

---

## 3. Komparasi Visual Sebelum vs Sesudah

| Aspek | Kondisi Saat Ini (SS 1 & 2) | Rancangan Baru (Sesudah Diperbaiki) |
| :--- | :--- | :--- |
| **Keluarga Font** | Mewarisi `Open Sans` lama (terkesan standar/kaku). | **Plus Jakarta Sans / Inter** (font modern standar web terpopuler, bersih dan berkelas). |
| **Garis Samping Banner** | Terdapat garis vertikal biru tebal di sisi kiri (`border-left: 3px`). | **Garis samping dihilangkan 100%**. Menjadi container rounded 12px dengan border halus sekeliling. |
| **Keterbacaan Teks** | Kontras standar. | Tipografi tajam dengan sedikit *negative letter-spacing* khas antarmuka modern. |
| **Dark Mode** | Menggunakan garis samping terang. | Container navy halus tanpa garis samping yang memotong layout. |

---

## 4. File Target & Kepatuhan Aturan
* **File yang Dimodifikasi**: Hanya 1 file styling:
  - [`resources/css/components/confirm-modal.css`](file:///d:/laragon/www/MAGKOM/resources/css/components/confirm-modal.css)
* **Blade View & Backend**: Tetap read-only (*zero modification*), menjaga stabilitas markup dan PHP logic.

---

## 5. Rencana Pengujian & Quality Gate

1. `npm run build` untuk memverifikasi kompilasi Vite bundle CSS.
2. Visual check pada browser:
   - Memastikan font berubah menjadi Plus Jakarta Sans yang halus dan modern.
   - Memastikan garis biru vertikal di sisi kiri impact banner telah hilang sepenuhnya.
   - Memastikan varian tolak dan dark mode tetap harmonis.
3. Menjalankan test suite regresi:
   - `php artisan test tests/Feature/AdminPendaftaranPerbaikanFlowTest.php`
   - `vendor/bin/pint --test`

