> Diperbarui: 2026-10-13 | commit: 1553e56 | mode: A

# Panduan Desain & Token Styling (STYLING.md)

Portal MagKom mengadopsi sistem desain resmi **DS-MagKom** (merujuk pada berkas spesifikasi `docs/07-DESIGN-SYSTEM-MAGKOM.md` dan Figma node `562-54980`). Sistem ini memadukan CSS Custom Properties native yang dideklarasikan di layout Blade induk bersama utility classes Tailwind CSS v3.4.

---

## 1. Design Tokens & Palet Warna (CSS Variables)

Semua token warna didefinisikan secara sentral di `:root` pada master layout (`layouts/app.blade.php`, `layouts/dashboard.blade.php`, `layouts/peserta.blade.php`):

### A. Palet Brand Utama (Kominfo Sleman Blue)
| CSS Variable | Nilai Hex | Fungsi / Implementasi |
| :--- | :--- | :--- |
| `--blue-600` | `#0751FF` | Warna primer tombol utama, active states, link penting |
| `--blue-700` | `#0641CC` | Hover state tombol primer, aksen fokus |
| `--blue-tint` | `#E6EEFF` | Background badge info, highlight baris tabel terpilih |
| `--blue-tint-line` | `#B3C8FF` | Border kartu aksen biru, divider kontras |

### B. Palet Netral (Grayscale & Kontras)
| CSS Variable | Nilai Hex | Fungsi / Implementasi |
| :--- | :--- | :--- |
| `--neutral-0` | `#FFFFFF` | Background dasar kartu, modal, dan navbar |
| `--neutral-100` | `#F8FAFC` | Background body dashboard (kanvas aplikasi) |
| `--neutral-150` | `#F5F7FB` | Background baris selang-seling tabel (zebra stripe) |
| `--neutral-200` | `#EEF0F5` | Border sekunder, pembatas header |
| `--neutral-400` | `#D0D3D9` | Border input form, garis divider utama |
| `--neutral-500` | `#7A8494` | Teks sekunder, label hint form, caption |
| `--neutral-700` | `#333333` | Teks tubuh utama (high legibility) |
| `--neutral-800` | `#1D2530` | Judul section, header tabel, icon aktif |
| `--neutral-900` | `#05070B` | Heading halaman `<h1>`, teks paling kontras |

### C. Status Tokens (WCAG 2.1 AA Compliant)
| Status | Background | Border | Teks Kontras | Penggunaan |
| :--- | :--- | :--- | :--- | :--- |
| **Success** | `--green-100` (`#E8F7EE`) | `--green-600` (`#16A34A`) | `--green-700` (`#166534`) | Diterima, Selesai, Disetujui |
| **Warning** | `--amber-100` (`#FFFBEB`) | `--amber-300` (`#FDE68A`) | `--amber-700` (`#92400E`) | Waiting List, Perlu Review, Pending |
| **Danger** | `--red-100` (`#FEF2F2`) | `--red-300` (`#FECACA`) | `--red-700` (`#991B1B`) | Ditolak, Dibatalkan, Terkunci |

---

## 2. Tipografi

Sistem menggunakan font keluarga tunggal sesuai pedoman korporat:
- **Font Utama**: `'Open Sans', Arial, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif`
- **Dimuat dari**: Google Fonts (`preconnect` ke `fonts.googleapis.com` dan `fonts.gstatic.com`).
- **Weight yang Dipakai**:
  - `400` (Regular): Teks tubuh, paragraf deskripsi
  - `600` (Semi-bold): Label input, judul tombol, badge
  - `700` (Bold): Heading `<h1>` s/d `<h3>`, metrik stat-card
  - `800` (Extra-bold): Angka kuota utama di landing page

---

## 3. Breakpoints & Standar Layout

| Dimensi | Lebar Layar | Konvensi Layout |
| :--- | :--- | :--- |
| **Mobile** | `375px` s/d `640px` | Single column flow, hamburger menu nav, modal full-screen sheet |
| **Tablet** | `768px` s/d `1024px` | 2-column grid, sidebar dapat diciutkan (*collapsible*) |
| **Desktop Standar** | `1200px` | `container` max-width 1200px pada landing page |
| **Figma Canvas Native**| `1440px` | Lebar kanvas utama desain, navbar max-width `1360px` dengan padding 2.5rem |

---

## 4. Konvensi Dark Mode & Tema
Saat ini Portal MagKom berfokus penuh pada **Light Corporate Government Theme** demi memenuhi kepatuhan regulasi aksesibilitas instansi pemerintah dan konsistensi pencetakan dokumen PDF. Desain dipastikan memiliki rasio kontras warna minimal 4.5:1 untuk teks normal dan 3:1 untuk teks besar sesuai standar WCAG 2.1 level AA.
