> Diperbarui: 2026-10-13 | commit: 1553e56 | mode: A

# Inventaris Komponen & Hubungan UI (COMPONENTS.md)

Dokumen ini memetakan seluruh komponen tampilan (Blade layouts, components, dan view partials) dalam Portal MagKom, mencakup hierarki pewarisan, tingkat penggunaan ulang, dan status dependensinya.

---

## 1. Diagram Hierarki Layout & Komponen

```mermaid
graph TD
    subgraph LayoutWrappers ["1. Master Layout Wrappers"]
        L_App["layouts/app.blade.php (Landing & Public)"]
        L_Auth["layouts/auth.blade.php (Login & Register)"]
        L_Dash["layouts/dashboard.blade.php (Admin, Pembimbing, SuperAdmin)"]
        L_Peserta["layouts/peserta.blade.php (Khusus Peserta)"]
    end

    subgraph UIPrimitives ["2. Reusable UI Primitives (resources/views/components)"]
        C_Alert["components/alert.blade.php"]
        C_Badge["components/badge.blade.php"]
        C_Stat["components/stat-card.blade.php"]
        C_Empty["components/empty-state.blade.php"]
        C_SSE["components/sse-toast-listener.blade.php"]
    end

    subgraph DomainViews ["3. Domain Pages"]
        V_Landing["public/landing.blade.php"]
        V_AdminPages["admin/* (Dashboard, Kuota, Penempatan, Laporan)"]
        V_PembimbingPages["pembimbing/* (Logbook, Tugas, Penilaian)"]
        V_PesertaPages["peserta/* (Pendaftaran, Logbook, Nilai)"]
        V_SuperAdminPages["superadmin/* (Monitoring, Audit, Backup, Master)"]
    end

    L_App --> V_Landing
    L_Dash --> V_AdminPages
    L_Dash --> V_PembimbingPages
    L_Dash --> V_SuperAdminPages
    L_Peserta --> V_PesertaPages

    V_AdminPages --> C_Alert
    V_AdminPages --> C_Badge
    V_AdminPages --> C_Stat
    V_AdminPages --> C_Empty
    L_Dash --> C_SSE
    L_Peserta --> C_SSE
```

---

## 2. Inventaris Komponen Berdasarkan Kategori

### A. Layout Templates (`resources/views/layouts/`)
1. **`app.blade.php`** (18 KB):
   - **Fungsi**: Layout publik utama untuk landing page, kebijakan privasi, dan halaman verifikasi dokumen.
   - **Fitur Khusus**: Menyematkan CSS Variables lengkap DS-MagKom (Blue-600, Neutral, Status tokens), Google Fonts Open Sans, Meta OG Tags, Header navbar sticky (76px), dan Footer resmi Kominfo Sleman.
2. **`auth.blade.php`** (20 KB):
   - **Fungsi**: Layout terisolasi untuk alur otentikasi (`/login`, `/register`, `/forgot-password`, `/reset-password`, ganti password wajib staf).
   - **Fitur Khusus**: Split layout (hero ilustrasi kiri + form kanan), penanganan flash messages, dan styling kartu otentikasi.
3. **`dashboard.blade.php`** (101 KB):
   - **Fungsi**: Master dashboard terpadu untuk peran **Admin**, **Pembimbing**, dan **SuperAdmin**.
   - **Fitur Khusus**: Sidebar adaptif berdasarkan role aktif, header profil interaktif, breadcrumbs otomatis, modal preview sertifikat, dan integrasi komponen SSE toast listener.
4. **`peserta.blade.php`** (53 KB):
   - **Fungsi**: Master workspace portal khusus untuk peserta magang.
   - **Fitur Khusus**: Navigasi bertahap (stepper alur pendaftaran s/d penyelesaian tugas), banner status penerimaan, widget progress logbook, dan floating notification drawer.

### B. UI Primitives (`resources/views/components/`)
1. **`alert.blade.php`** (2.6 KB):
   - **Props**: `type` (`info`, `success`, `warning`, `error`), `title`, `dismissible`.
   - **Tingkat Penggunaan**: Sangat Tinggi (dipanggil di hampir semua form submit dan flash session).
2. **`badge.blade.php`** (2.2 KB):
   - **Props**: `status` (`diterima`, `ditolak`, `waiting_list`, `pending`, `aktif`, `selesai`), `size`.
   - **Tingkat Penggunaan**: Sangat Tinggi (tabel pendaftaran, status kuota, list logbook).
3. **`stat-card.blade.php`** (3.4 KB):
   - **Props**: `title`, `value`, `icon`, `growth`, `variant`.
   - **Tingkat Penggunaan**: Tinggi (dashboard Admin, Pembimbing, SuperAdmin).
4. **`empty-state.blade.php`** (1.3 KB):
   - **Props**: `title`, `description`, `icon`, `actionUrl`, `actionText`.
   - **Tingkat Penggunaan**: Sedang (tabel tanpa data, daftar tugas kosong).
5. **`sse-toast-listener.blade.php`** (0.5 KB):
   - **Props**: None (mendengarkan stream event via JavaScript `EventSource('/realtime/stream')`).
   - **Tingkat Penggunaan**: Dimuat otomatis di semua layout dashboard terautentikasi.

### C. Komponen Cetak & Ekspor PDF (`resources/views/pdf/`)
1. **`surat-diterima.blade.php`**: Format resmi surat penetapan penerimaan magang lengkap dengan barcode/QR verifikasi.
2. **`lembar-nilai.blade.php`**: Format transkrip evaluasi akhir peserta magang sesuai rubrik penilaian.
3. **`sertifikat.blade.php`**: Desain piagam kelulusan lanskap dengan nomor registrasi unik.

---

## 3. Komponen Paling Sering Digunakan (Core Components)
1. **`<x-badge>`**: Digunakan di 18 file view yang berbeda untuk standardisasi warna dan teks status pendaftaran/kuota.
2. **`<x-alert>`**: Digunakan di semua view form (`auth`, `pendaftaran`, `logbook`, `profil`).
3. **`<x-stat-card>`**: Komponen modular utama yang merender matriks eksekutif pada ketiga dashboard peran internal.

---

## 4. Komponen & Template Orphan (Tidak Terpakai)
| File | Ukuran | Status Temuan | Rekomendasi |
| :--- | :--- | :--- | :--- |
| `resources/views/welcome.blade.php` | 28.6 KB | **ORPHAN** (Bawaan installer Laravel default). Tidak ada route atau controller yang merender view ini (halaman root memanggil `LandingController` yang merender `public.landing`). | Aman untuk dihapus atau diarsipkan agar tidak membingungkan developer baru. |
