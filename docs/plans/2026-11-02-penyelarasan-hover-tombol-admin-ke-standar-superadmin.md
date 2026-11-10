# Rencana Kerja: Penyelarasan Efek Hover dan Warna Tombol Modul Administrasi ke Standar SuperAdmin

> **Dokumen Perencanaan Resmi Web Planning**  
> **Target**: Seluruh Halaman dan Komponen Modul Administrasi (`admin/*`)  
> **Tanggal**: 2 November 2026  
> **Status**: Menunggu Persetujuan Pengguna (*Pending Approval*)

---

## 1. Tujuan
Menyelaraskan efek visual, interaksi kursor (*hover states*), dan hierarki warna seluruh tombol navigasi maupun tombol aksi di modul administrasi (`admin/*`) agar konsisten dengan standar visual portal **SuperAdmin** MagKom Sleman (palet Deep Navy `#01347C`, Kominfo Bright Blue `#0641CC` / `#0751FF`, Soft Tint `#EFF6FF`, dan elevasi `translateY(-1px)` / `translateY(-2px)` dengan bayangan halus).

Perbaikan mencakup 4 fokus utama dari tangkapan layar pengguna:
1. **Quick Action Grid Buttons** pada panel aside (`Penempatan`, `Waiting List`, `Rekap Laporan`, `Hub Sertifikat`).
2. **Action Link Buttons & Arrow Links** (`Atur →`, `Kelola →`, `Sertifikat →`, `Tinjau →`, `Buka →`).
3. **Outline / Ghost Buttons** (`Lihat Semua →`, tombol filter outline, tombol aksi tabel).
4. **Segmented Switcher Pills** (`[10] 20 30`, filter tabs, status pills).
5. **Standarisasi Menyeluruh di Semua Halaman Administrasi** (`pendaftaran`, `penempatan`, `waiting-list`, `kuota`, `sertifikat`, `laporan`, `pengaturan`, `profil`).

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi Kerja Logis:
1. **Strictly No Blade CSS**: Sesuai aturan paten `AGENTS.md`, seluruh styling hover harus didefinisikan secara terpusat pada file stylesheet `resources/css/` (`resources/css/layouts/gentelella.css` dan `resources/css/pages/admin/dashboard.css`) serta disinkronkan ke `public/css/gentelella-theme.css` untuk kompatibilitas offline.
2. **Zero-Regression Kontrak & Route**: Tidak ada pengubahan atribut `href`, nama route, method POST/GET, maupun selector JavaScript yang sedang berjalan.
3. **Dual-Theme Support**: Semua hover state wajib mendukung mode terang (*Light Mode*) dan mode gelap (*Dark Mode* `[data-theme="dark"]`) sesuai standar token SuperAdmin.

### Pertanyaan Terbuka:
- Tidak ada ambiguitas arsitektural yang memblokir. Seluruh 4 tipe tombol dan halaman administrasi terkait sudah dipetakan dengan tepat berdasarkan referensi visual pengguna.

---

## 3. Pendekatan Teknis & Evaluasi Arsitektur

### Perbandingan Opsi:

| Kriteria | Opsi 1: Kelas CSS Terpusat di `resources/css/` (Direkomendasikan) | Opsi 2: Inline Styles `<style>` di View Blade |
| :--- | :--- | :--- |
| **Kepatuhan Aturan `AGENTS.md`** | **100% Patuh** (Satu sumber kebenaran di `resources/css/`) | **Melanggar Aturan** (Blade bukan tempat CSS) |
| **Konsistensi Antar-Halaman** | **Sangat Tinggi**: Tombol di seluruh view admin otomatis seragam | **Rendah**: Harus ditulis berulang di tiap file Blade |
| **Dukungan Dark Mode** | **Otomatis**: Dikelola via token CSS & data-theme selector | **Rentan Cacat**: Harus menulis override manual berulang |
| **Performa & Cache Asset** | **Tinggi**: Terkompilasi via Vite bundle | **Beban Tambahan**: Menambah ukuran file HTML respons |

> **Rekomendasi**: **Opsi 1**. Mengonsolidasikan seluruh selector hover tombol administrasi ke dalam `resources/css/layouts/gentelella.css` dan `resources/css/pages/admin/dashboard.css`, lalu disinkronkan ke `public/css/gentelella-theme.css`.

---

## 4. Spesifikasi Token Desain & Hover State (Standar SuperAdmin)

| Tipe Tombol | Kondisi Normal (*Default*) | Kondisi Arah Kursor (*Hover*) | Kondisi Mode Gelap (*Dark Mode Hover*) |
| :--- | :--- | :--- | :--- |
| **1. Quick Action Grid** (`.cv-quick-btn`) | `bg: #F8FAFC`, `border: #CBD5E1`, `text: #1E293B`, `radius: 8px` | `bg: #EFF6FF`, `border: #0641CC`, `text: #01347C`, `translateY(-2px)`, `box-shadow: 0 4px 12px rgba(6,65,204,0.15)` | `bg: rgba(59,130,246,0.2)`, `border: #3B82F6`, `text: #93C5FD`, `box-shadow: 0 4px 12px rgba(0,0,0,0.4)` |
| **Quick Action Primary** (`.cv-quick-btn.primary`) | `bg: #01347C`, `border: #01347C`, `text: #FFFFFF` | `bg: #0751FF`, `border: #0751FF`, `translateY(-2px)`, `box-shadow: 0 4px 14px rgba(7,81,255,0.3)` | `bg: #2563EB`, `border: #3B82F6`, `text: #FFFFFF` |
| **2. Action Link** (`Atur →`, `Kelola →`, `.cv-link-action`) | `text: #0641CC`, `font-weight: 700`, `padding: 0.25rem 0.55rem`, `radius: 6px` | `bg: #EFF6FF`, `color: #01347C`, `border: 1px solid rgba(6,65,204,0.2)`, panah `translateX(2px)` | `bg: rgba(59,130,246,0.18)`, `color: #93C5FD`, `border: rgba(147,197,253,0.3)` |
| **3. Outline / View All** (`.cv-btn-view-all`, `.btn-outline`) | `bg: #FFFFFF`, `border: #CBD5E1`, `text: #334155`, `radius: 6px` | `bg: #EFF6FF`, `border: #01347C`, `text: #01347C`, `translateY(-1px)`, `box-shadow: 0 2px 8px rgba(6,65,204,0.12)` | `bg: rgba(255,255,255,0.06)`, `border: #3B82F6`, `text: #93C5FD` |
| **4. Inactive Pills** (`.kuota-per-page-pill`, `.adm-pill-btn`) | `bg: transparent`, `text: #64748B`, `border: none` | `bg: #E2E8F0`, `color: #0F172A`, `scale(1.02)` | `bg: #334155`, `color: #F8FAFC` |
| **Active Pills** (`.kuota-per-page-pill.active`) | `bg: #01347C`, `text: #FFFFFF`, `shadow: 0 1px 2px rgba(6,65,204,0.2)` | `bg: #0751FF`, `text: #FFFFFF` | `bg: #3B82F6`, `text: #FFFFFF` |
| **5. Table Row Review** (`.adm-btn-tinjau`, `.btn-table-action`) | `bg: #EFF6FF`, `border: rgba(6,65,204,0.25)`, `text: #0641CC` | `bg: #0641CC`, `border: #0641CC`, `text: #FFFFFF`, `translateY(-1px)`, `box-shadow: 0 4px 10px rgba(6,65,204,0.25)` | `bg: #2563EB`, `border: #3B82F6`, `text: #FFFFFF` |
| **6. Primary Button** (`.btn-primary`, `.btn-toolbar-primary`) | `bg: #01347C`, `border: #01347C`, `text: #FFFFFF` | `bg: #0751FF`, `border: #0751FF`, `translateY(-1px)`, `box-shadow: 0 4px 12px rgba(6,65,204,0.25)` | `bg: #3F51B5`, `border: #5C6BC0`, `text: #FFFFFF` |

---

## 5. Rencana Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/css/pages/admin/dashboard.css` | Modify | Perbarui hover `.cv-quick-btn`, `.cv-link-action`, `.cv-btn-view-all`, `.adm-pill-btn`, dan `.cv-todo-item` agar identik SuperAdmin |
| `resources/css/layouts/gentelella.css` | Modify | Standarisasi aturan hover global untuk `.btn-table-action`, `.btn-action-gentelella`, `.kuota-per-page-pill`, `.adm-btn-tinjau`, dan segmented pills di semua modul admin |
| `public/css/gentelella-theme.css` | Modify | Sinkronisasi aturan CSS hover terbaru agar tampilan tetap bekerja sempurna secara offline |
| `resources/views/admin/dashboard/partials/_aside.blade.php` | Modify | Menambahkan kelas semantic `.cv-link-action` pada link `Atur →`, `Kelola →`, dan `Sertifikat →` menggantikan style inline statis |

---

## 6. Langkah Implementasi Bertahap

### Tahap 1: Penguatan Aturan CSS di `resources/css/pages/admin/dashboard.css`
- [x] Perbarui kelas `.cv-quick-btn`:
  - Hover state: background `#EFF6FF`, border `#0641CC`, color `#01347C`, `transform: translateY(-2px)`, `box-shadow: 0 4px 12px rgba(6, 65, 204, 0.15)`.
  - Icon SVG: otomatis mewarisi `stroke: #01347C`.
  - Primary button hover: background `#0751FF`, `transform: translateY(-2px)`, shadow `0 4px 14px rgba(7, 81, 255, 0.3)`.
  - Dark mode selector `[data-theme="dark"]` hover matching.
- [x] Perbarui kelas `.cv-btn-view-all` (Lihat Semua):
  - Hover state: background `#EFF6FF`, border `#01347C`, color `#01347C`, `transform: translateY(-1px)`, `box-shadow: 0 2px 8px rgba(6, 65, 204, 0.12)`.
- [x] Tambahkan kelas utilitas `.cv-link-action`:
  - Default: padding `0.25rem 0.6rem`, border-radius `6px`, font-weight `700`, color `var(--cv-blue-700, #0641CC)`, transition `all 0.15s ease`.
  - Hover: background `#EFF6FF`, border `1px solid rgba(6, 65, 204, 0.25)`, color `#01347C`, translasi panah `translateX(2px)`.

### Tahap 2: Standardisasi Hover Global di `resources/css/layouts/gentelella.css`
- [x] Perbarui switcher paginasi kuota `.kuota-per-page-pill`:
  - Hover inactive: background `#E2E8F0`, color `#0F172A`, `transform: scale(1.02)`.
  - Active hover: background `#0751FF`, color `#FFFFFF`.
  - Dark mode inactive hover: background `#334155`, color `#F8FAFC`.
- [x] Perbarui tombol aksi tabel `.btn-table-action` dan `.adm-btn-tinjau`:
  - Hover: background `#0641CC`, border `#0641CC`, color `#FFFFFF`, shadow `0 4px 10px rgba(6, 65, 204, 0.25)`, `transform: translateY(-1px)`.
- [x] Perbarui tombol filter tabs dan segmented pills (`.adm-status-tab`, `.adm-pill-btn`, `.btn-status-pill`):
  - Inactive hover: background `#EFF6FF`, color `#01347C`, border-color `#BFDBFE`.
- [x] Perbarui tombol navigasi kembali dan ekspor di modul administrasi:
  - `.cert-btn-back`, `.btn-toolbar-secondary`, `.btn-profile-shortcut`, `.btn-outline-action`: Hover background `#EFF6FF`, border `#01347C`, color `#01347C`.
  - `.btn-action-primary`, `.btn-primary-action`, `.btn-toolbar-primary`: Hover background `#0751FF`, border `#0751FF`, color `#FFFFFF`, `transform: translateY(-1px)`.
  - Tombol ekspor Excel: Hover background `#15803D`, `transform: translateY(-1px)`, shadow lembut.

### Tahap 3: Pembersihan Markup Inline di Partial Aside
- [x] Buka `resources/views/admin/dashboard/partials/_aside.blade.php`.
- [x] Ganti style inline statis pada link navigasi kartu:
  - Link `Atur →` (Jadwal Pendaftaran) diberi kelas `class="cv-link-action"`.
  - Link `Kelola →` (Pembimbing Lapangan) diberi kelas `class="cv-link-action"`.
  - Link `Sertifikat →` (Offboarding Watchdog) diberi kelas `class="cv-link-action cv-link-action--warning"`.

### Tahap 4: Sinkronisasi dan Kompilasi Aset
- [x] Sinkronkan seluruh selector baru ke `public/css/gentelella-theme.css`.
- [x] Jalankan kompilasi produksi: `npm run build`.
- [x] Pastikan tidak ada file `public/hot` yang tertinggal.

---

## 7. Strategi Testing & Quality Gates

1. **Linting & Code Style**:
   - Jalankan `./vendor/bin/pint --test` untuk memastikan integritas format PHP.
2. **Automated Testing Suite**:
   - Jalankan test suite admin: `php artisan test --filter Admin` (memastikan minimal 240 test lulus tanpa regresi).
3. **Visual & Interaction Check**:
   - Uji hover pada 4 tombol Quick Action (`Penempatan`, `Waiting List`, `Rekap Laporan`, `Hub Sertifikat`) -> Verifikasi background biru tint `#EFF6FF`, border navy `#0641CC`, dan elevasi `translateY(-2px)`.
   - Uji hover pada `Atur →`, `Kelola →`, `Sertifikat →` -> Verifikasi kemunculan background badge lembut dan pergeseran panah.
   - Uji hover pada `Lihat Semua →` -> Verifikasi border navy dan text navy dengan depth.
   - Uji hover pada tombol pill paginasi `[10] 20 30` -> Verifikasi tombol 20 dan 30 bereaksi saat diarahkan kursor.
   - Uji tombol-tombol pada modul `admin/pendaftaran`, `admin/penempatan`, `admin/waiting-list`, `admin/kuota`, `admin/sertifikat`, dan `admin/laporan`.

---

## 8. Risiko & Mitigasi

- **Risiko 1: Konflik CSS specificity dengan framework Gentelella legacy.**
  - *Mitigasi*: Gunakan selector terarah (`.gentelella-dashboard-admin .cv-quick-btn:hover`, dsb.) dengan penegasan bila diperlukan agar tidak tertimpa stylesheet legacy.
- **Risiko 2: Tampilan Dark Mode tidak terbaca jika hanya mengubah warna hover tema terang.**
  - *Mitigasi*: Selalu sertakan aturan pasangan `[data-theme="dark"]` untuk setiap hover state tombol.

---

## 9. Kriteria Selesai (*Definition of Done*)

- [x] Seluruh tombol menuju halaman pada dashboard aside memiliki efek hover selaras SuperAdmin (biru muda `#EFF6FF`, border `#0641CC`, teks `#01347C`, elevasi `translateY(-2px)`).
- [x] Tautan `Atur →`, `Kelola →`, `Sertifikat →` memiliki visual hover interaktif dengan translasi panah.
- [x] Tombol `Lihat Semua →` memiliki hover dengan aksen border biru primer MagKom.
- [x] Tombol pill per-page switcher (`10`, `20`, `30`) memiliki efek hover jelas pada angka yang tidak aktif.
- [x] Tombol pada seluruh halaman modul administrasi (`pendaftaran`, `penempatan`, `waiting-list`, `kuota`, `sertifikat`, `laporan`) konsisten menggunakan palet warna dan interaksi hover SuperAdmin.
- [x] `npm run build` berhasil tanpa error.
- [x] Seluruh test `php artisan test --filter Admin` lulus (240 passed).

