# Plan: Implementasi Tutup Sidebar & Pemindahan Menu Pengaturan ke Profil Kanan Atas Dashboard Peserta

**Tanggal:** 27 Oktober 2026  
**Target File:** `resources/views/layouts/peserta.blade.php`  
**Tujuan:**
1. Mengimplementasikan fitur **Tutup Sidebar (Collapse/Expand Sidebar)** pada Dashboard Peserta seperti yang sudah ada pada dashboard Admin, Pembimbing, dan SuperAdmin.
2. Memindahkan menu **Pengaturan Akun** dari navigasi sidebar kiri ke dalam menu dropdown profil kanan atas.

---

## 1. Analisis Kebutuhan & Perilaku Saat Ini

### A. Kondisi Saat Ini
1. **Sidebar Peserta**:
   - Sidebar desktop memiliki lebar tetap (`256px`), tanpa tombol toggle untuk menyusutkan/menutup sidebar.
   - Tombol toggle saat ini (`.mobile-menu-btn`) hanya muncul pada layar mobile (`< 992px`) untuk membuka/menutup drawer overlay.
   - Menu sidebar masih memuat blok:
     - Label: `Pengaturan`
     - Menu link: `Pengaturan Akun` (`peserta.pengaturan.index`)
2. **Dropdown Profil Kanan Atas**:
   - Berisi informasi profil singkat (nama, email, role `Peserta Magang`).
   - Berisi link: `Dashboard`, `Biodata & Profil`, `Riwayat Pendaftaran`, `Ganti Kata Sandi` (mengarah ke `peserta.pengaturan.index`), dan `Keluar Akun`.
3. **Pola Referensi di Dashboard Admin/Pembimbing/SuperAdmin (`dashboard.blade.php`)**:
   - Memiliki tombol `#menu_toggle` dengan ikon burger hamburger di sisi kiri topbar.
   - Pada desktop: mengecilkan sidebar menjadi *mini icon-only* atau toggle collapse dengan class body/sidebar, dan menyimpan preferensi ke `localStorage` (`magkom_sidebar_state`) agar tetap persisten antar-halaman.
   - Pada mobile: membuka drawer offcanvas penuh dengan backdrop.

---

## 2. Rencana Solusi & Desain Teknis

### A. Fitur Tutup Sidebar (Desktop & Mobile)
1. **Tombol Toggle di Topbar**:
   - Menambahkan tombol toggle hamburger seragam di samping judul topbar:
     ```html
     <button type="button" 
             id="sidebarToggleBtn" 
             class="sidebar-toggle-btn" 
             onclick="toggleSidebarPeserta()" 
             aria-label="Toggle Sidebar Menu" 
             title="Tutup / Buka Sidebar">
         <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
             <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"></path>
         </svg>
     </button>
     ```
   - Tombol ini aktif baik di Desktop (collapse/expand) maupun di Mobile (open/close drawer offcanvas).
2. **Mekanisme Collapse di Desktop (CSS & JS)**:
   - Tambahkan class `.sidebar-collapsed` pada elemen `body` atau root layout.
   - **Saat `.sidebar-collapsed` aktif di Desktop (>= 992px)**:
     - Lebar sidebar menyusut dari `256px` menjadi `72px` (mini sidebar icon-only).
     - Logo teks (`.brand-text`) disembunyikan secara mulus (`display: none` / `opacity: 0`), hanya ikon logo Sleman (`.brand-icon`) yang ditampilkan terpusat.
     - Label grup menu (`.nav-label`) disembunyikan.
     - Teks label navigasi (`span` / teks di dalam `.nav-item`) disembunyikan, ikon SVG ditampilkan terpusat di tengah dengan padding presisi.
     - Badge lock pada menu terkunci diatur agar tetap ringkas atau hanya ikon gembok kecil di sudut.
     - Tombol logout di footer disesuaikan menjadi ikon power/logout terpusat dengan tooltip title.
     - Konten utama (`.main-wrapper`) otomatis menyesuaikan margin kiri dari `256px` ke `72px` dengan transisi CSS `cubic-bezier(0.4, 0, 0.2, 1)`.
   - **Penyimpanan State (Persistensi)**:
     - Disimpan di `localStorage.getItem('magkom_peserta_sidebar_collapsed')`.
     - Script inline anti-flicker di `<head>` membaca state ini seketika saat halaman dimuat sehingga tidak terjadi loncatan layout (layout shift / CLS).
3. **Mekanisme Mobile (< 992px)**:
   - Tetap berfungsi sebagai offcanvas drawer overlay dengan backdrop gelap (`.drawer-backdrop`).

### B. Pemindahan Fitur Pengaturan ke Profil Kanan Atas
1. **Pembersihan Sidebar**:
   - Menghapus blok label `Pengaturan` dan link `Pengaturan Akun` (`peserta.pengaturan.index`) dari `nav.sidebar-nav`.
   - Sidebar kini murni fokus pada aktivitas magang:
     - `Dashboard`
     - `Permohonan Magang`
     - `Logbook Mingguan`
     - `Tugas & Progres`
     - `Lembar Nilai`
2. **Penyempurnaan Dropdown Profil Kanan Atas**:
   - Mengubah item `Ganti Kata Sandi` menjadi **`Pengaturan Akun`** yang lengkap:
     - Menggunakan ikon gear/settings yang profesional dan standar.
     - Teks: `Pengaturan Akun`.
     - URL: `{{ route('peserta.pengaturan.index') }}` (halaman ini memuat pengaturan keamanan/password serta preferensi tema).
   - Susunan menu dropdown profil menjadi sangat rapi dan intuitif:
     1. Profil Ringkas (Avatar, Nama, Email, Badge Peserta Magang)
     2. Divider
     3. `Biodata & Profil` (`peserta.profil.show`)
     4. `Riwayat Pendaftaran` (`peserta.pendaftaran.index`)
     5. `Pengaturan Akun` (`peserta.pengaturan.index`)
     6. Divider
     7. `Keluar Akun` (Logout)

---

## 3. Detail File yang Diubah

| File | Bagian yang Diubah | Deskripsi Perubahan |
|---|---|---|
| `resources/views/layouts/peserta.blade.php` | `<head>` | Tambahkan script pencegah layout flicker untuk membaca state `localStorage`. |
| `resources/views/layouts/peserta.blade.php` | `<style>` | Tambahkan rules styling untuk `.sidebar-collapsed`, mini-sidebar (72px), transisi, icon centering, dan responsif. |
| `resources/views/layouts/peserta.blade.php` | `.topbar-left` | Ganti `.mobile-menu-btn` dengan tombol toggle universal (`.sidebar-toggle-btn`) yang aktif di desktop dan mobile. |
| `resources/views/layouts/peserta.blade.php` | `nav.sidebar-nav` | Hapus label dan link `Pengaturan Akun`. Tambahkan tooltip `title` pada setiap nav item untuk mode mini sidebar. |
| `resources/views/layouts/peserta.blade.php` | `.profile-dropdown-menu` | Perbarui item menu menjadi `Pengaturan Akun` dengan ikon roda gigi SVG. |
| `resources/views/layouts/peserta.blade.php` | `<script>` | Tambahkan fungsi `toggleSidebarPeserta()` yang mengelola state desktop collapse (`localStorage`) dan mobile drawer. |

---

## 4. Rencana Langkah Kerja (Checklist)

- [ ] **Langkah 1**: Modifikasi `<head>` di `layouts/peserta.blade.php` dengan script inisialisasi state sidebar sebelum render (mencegah flicker).
- [ ] **Langkah 2**: Tambahkan styling CSS untuk `.sidebar-collapsed` (width 72px, hide label/text, center icon, auto adjust `.main-wrapper`).
- [ ] **Langkah 3**: Perbarui markup tombol toggle di topbar agar bisa diklik di desktop maupun mobile.
- [ ] **Langkah 4**: Hapus grup menu Pengaturan dari sidebar dan tambahkan atribut `title` ke setiap menu item untuk kenyamanan mode mini sidebar.
- [ ] **Langkah 5**: Perbarui item di dropdown profil kanan atas menjadi `Pengaturan Akun` dengan ikon gear SVG.
- [ ] **Langkah 6**: Implementasikan fungsi JavaScript `toggleSidebarPeserta()` dengan dukungan `localStorage`.
- [ ] **Langkah 7**: Jalankan `php artisan view:clear` dan verifikasi bahwa tampilan render sempurna di browser baik desktop normal, desktop collapsed, maupun mobile drawer.
