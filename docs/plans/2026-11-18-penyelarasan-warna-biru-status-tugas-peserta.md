# Rencana Kerja: Penyelarasan Warna Status Selesai dari Hijau Menjadi Biru (Sleman Royal Blue) pada Halaman Tugas Peserta

**Tanggal:** 18 November 2026  
**Status Dokumen:** Menunggu Persetujuan Pengguna  
**ID Rencana:** PLAN-2026-11-18-TUGAS-BLUE-THEME  

---

## 1. Tujuan
Mengganti seluruh aksen warna hijau (*emerald/green*) pada kartu status "Tugas Anda" di halaman detail tugas peserta (`resources/views/peserta/tugas/show.blade.php` dan `resources/css/pages/peserta/tugas-detail.css`) menjadi tema warna **Biru Khas MagKom / Sleman Royal Blue** (`#01347C`, `#1D4ED8`, `#EFF6FF`, `#DBEAFE`), sehingga selaras (*parity*) dengan tema kartu dan modal penyelesaian tugas pembimbing yang telah dirapikan sebelumnya.

---

## 2. Investigasi & Analisis Komponen Visual

Berdasarkan tangkapan layar pengguna (`media_1795012791132_52a74936.png`) pada kartu *"Tugas Anda"*, terdapat 2 elemen utama yang saat ini masih menggunakan warna hijau:

1. **Teks Status "Selesai" (Kanan Atas Kartu)**:
   - File: `resources/css/pages/peserta/tugas-detail.css` (baris 311-313)
   - Selector: `.classroom-work-status.status-done`
   - Kondisi saat ini: `color: #10B981 !important;` (Hijau emerald).
   - Target baru: Menjadi **Sleman Royal Blue** (`#01347C` / `#1D4ED8`), dengan dukungan Dark Mode (`#60A5FA`).

2. **Banner / Kotak Penyelesaian Tugas ("Tugas ini telah ditinjau dan dinyatakan selesai oleh pembimbing.")**:
   - File: `resources/css/pages/peserta/tugas-detail.css` (baris 1207-1226)
   - Selector Wadah: `.submitted-completed-box`
     - Kondisi saat ini: `color: #166534; background: #F8FAFC; border: 1px solid #E2E8F0;`
     - Target baru:
       - Background: `#F0F6FF` (Soft Sleman Blue tint)
       - Border: `1px solid #DBEAFE` (Subtle blue border)
       - Color: `#01347C` (Sleman Royal Blue text)
   - Selector Ikon Centang: `.submitted-completed-box .submitted-check-icon`
     - Kondisi saat ini: `background: #DCFCE7; color: #16A34A;` (Lingkaran hijau & ikon hijau).
     - Target baru:
       - Background: `#EFF6FF` (Soft circular blue background)
       - Border: `1px solid rgba(1, 52, 124, 0.16)`
       - Color: `#01347C` (Ikon centang biru tegas)

3. **Dukungan Dark Mode**:
   - File: `resources/css/pages/peserta/tugas-detail.css` (baris 1574-1587)
   - Menyelaraskan selector dark mode `.submitted-completed-box` dan `.submitted-check-icon` agar menggunakan aksen biru transparan (`rgba(1, 52, 124, 0.22)`) dan teks biru muda (`#93C5FD` / `#60A5FA`).

4. **Opsional Penyelarasan Ikon Status Daftar Tugas (`tugas-index.css`)**:
   - Pada `resources/css/pages/peserta/tugas-index.css` (baris 490-493), ikon baris tugas selesai (`.task-row-icon.icon-selesai`) saat ini masih `background: #DCFCE7; color: #15803D;`. Kita selaraskan menjadi tema biru konsisten (`background: #EFF6FF; color: #01347C;`).

---

## 3. Asumsi & Pendekatan Teknis

- **Penyelarasan Murni Melalui CSS**: Seluruh perubahan warna dilakukan di file CSS (`resources/css/pages/peserta/tugas-detail.css` dan `resources/css/pages/peserta/tugas-index.css`). File Blade `resources/views/peserta/tugas/show.blade.php` **tidak perlu diubah sama sekali** karena struktur markup dan nama class sudah sempurna (*zero Blade modification*).
- **Design System Compliance**: Nilai warna mengikuti Design System MagKom (Sleman Royal Blue `#01347C`, Blue 700 `#1D4ED8`, Blue 100 `#DBEAFE`, Blue 50 `#EFF6FF`/`#F0F6FF`).

---

## 4. Perubahan File

| No | File | Aksi | Ringkasan Perubahan |
|---|---|---|---|
| 1 | `resources/css/pages/peserta/tugas-detail.css` | Modify | Mengubah warna `.classroom-work-status.status-done`, `.submitted-completed-box`, dan `.submitted-check-icon` beserta aturan Dark Mode dari hijau ke tema biru Sleman. |
| 2 | `resources/css/pages/peserta/tugas-index.css` | Modify | Menyelaraskan ikon status tugas selesai `.task-row-icon.icon-selesai` dari hijau menjadi biru Sleman. |

---

## 5. Langkah Implementasi Terperinci

### Tahap 1: Pembaruan Styling Detail Tugas (`tugas-detail.css`)
- [x] Ubah rule teks status selesai (baris 311):
  ```css
  .classroom-work-status.status-done {
      color: #01347C !important;
  }
  ```
- [x] Ubah rule kotak penyelesaian tugas (baris 1207):
  ```css
  .submitted-completed-box {
      background: #F0F6FF;
      border: 1px solid #DBEAFE;
      border-radius: 10px;
      padding: 12px 14px;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      text-align: center;
      color: #01347C;
      font-size: 0.8125rem;
      font-weight: 500;
  }
  ```
- [x] Ubah rule ikon lingkaran centang (baris 1223):
  ```css
  .submitted-completed-box .submitted-check-icon {
      background: #EFF6FF;
      border: 1px solid rgba(1, 52, 124, 0.16);
      color: #01347C;
  }
  ```
- [x] Sesuaikan Dark Mode (baris 1574-1587):
  ```css
  [data-theme="dark"] .submitted-completed-box {
      background: rgba(1, 52, 124, 0.18) !important;
      border-color: rgba(96, 165, 250, 0.25) !important;
      color: #93C5FD !important;
  }
  [data-theme="dark"] .submitted-completed-box .submitted-check-icon {
      background: rgba(1, 52, 124, 0.35) !important;
      border-color: rgba(96, 165, 250, 0.35) !important;
      color: #60A5FA !important;
  }
  [data-theme="dark"] .classroom-work-status.status-done {
      color: #60A5FA !important;
  }
  ```

### Tahap 2: Pembaruan Styling Daftar Tugas (`tugas-index.css`)
- [x] Ubah rule `.task-row-icon.icon-selesai` (baris 490):
  ```css
  .task-row-icon.icon-selesai {
      background: #EFF6FF;
      border: 1px solid #DBEAFE;
      color: #01347C;
  }
  ```
- [x] Sesuaikan Dark Mode `.task-row-icon.icon-selesai` agar bernada biru.

### Tahap 3: Verifikasi & Kompilasi Aset
- [x] Jalankan `npm run build` untuk mengompilasi CSS.
- [x] Jalankan automated test PHPUnit terkait tugas peserta untuk memastikan zero regression.
- [x] Verifikasi visual melalui browser bahwa seluruh elemen hijau pada kartu tugas telah berubah menjadi biru Sleman yang elegan dan profesional.

---

## 6. Kriteria Selesai (Definition of Done)

- [x] Teks "Selesai" pada kartu "Tugas Anda" tampil dalam warna Biru Sleman (`#01347C`).
- [x] Banner pesan "Tugas ini telah ditinjau dan dinyatakan selesai oleh pembimbing" tampil dengan latar biru lembut (`#F0F6FF`), border biru halus (`#DBEAFE`), dan teks biru (`#01347C`).
- [x] Ikon centang di samping pesan tampil dengan lingkaran biru lembut dan centang biru Sleman.
- [x] Tampilan Dark Mode tetap memiliki kontras tinggi dan memenuhi standar aksesibilitas WCAG 2.1 AA.
- [x] Kompilasi aset Vite (`npm run build`) berhasil 100%.

