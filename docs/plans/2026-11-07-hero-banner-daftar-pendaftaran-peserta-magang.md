# Rencana Kerja: Implementasi Hero Banner "Daftar Pendaftaran Peserta Magang" & Pembersihan Header Panel

Dokumen perencanaan teknis ini disusun untuk merancang penggantian judul halaman biasa (*plain page title*) menjadi banner kartu navy (*hero banner*) khas Sleman MagKom, serta menghapus judul panel yang redundan pada halaman verifikasi pendaftaran admin (`admin.pendaftaran.index`).

---

## 1. Tujuan
1. **Mengubah Judul Halaman Menjadi Hero Banner Khas Sleman**: Mengganti komponen judul polos `<x-gentelella.page-title>` (Gambar 2) menjadi banner navy elegan (*hero banner*) bergradien biru tua dengan tipografi tajam, metadata resmi kedinasan, dan tombol aksi terintegrasi (seperti Gambar 1).
2. **Menetapkan Judul Baru**: Memberi judul utama banner: **`Daftar Pendaftaran Peserta Magang`** sesuai instruksi pengguna.
3. **Menghapus Judul Panel Redundan (Gambar 3)**: Menghapus header panel `<div class="x_title">` bertuliskan *"Daftar Permohonan Magang Masuk"* pada kartu tabel utama, sehingga kartu tabel langsung diawali oleh Tab Navigasi Kategori (*Modern Underline Tabs*).

---

## 2. Analisis Komparasi 3 Gambar Pengguna

### A. Gambar 2 (Tampilan Saat Ini yang Akan Diganti)
```
Verifikasi Berkas Pendaftaran Peserta
[Kembali ke Dashboard] [Unduh Bundel ZIP]
```
- **Kondisi**: Hanya berupa teks hitam biasa di atas latar belakang abu-abu terang dengan tombol aksi terpisah. Kurang memiliki wibawa (*prestige*) dan daya tarik visual khas portal MagKom.

### B. Gambar 1 (Desain Acuan Hero Banner yang Diinginkan)
```
┌────────────────────────────────────────────────────────────────────────┐
│ Direktori Peserta Bimbingan                                            │
│ Bidang: EGOV • Pembimbing: Budi Santoso • 2 Mentee   [+ Beri Tugas]    │
└────────────────────────────────────────────────────────────────────────┘
```
- **Karakteristik Desain**:
  - Background: Gradien radial & linier navy khas Sleman (`#01347C` ke `#003566`).
  - Radius: `16px` dengan bayangan lembut (*box-shadow*).
  - Teks: Putih bersih, judul tebal (weight 800), subtitle dengan bullet separator (`•`).
  - Tombol Aksi Kanan: Tombol kapsul elegan (latar putih dengan teks biru tua).

### C. Gambar 3 (Header Panel yang Diminta Dihapus)
```
Daftar Permohonan Magang Masuk
-------------------------------------------------------------------------
[Semua Kategori] [Individu] [Kelompok]
```
- **Kondisi**: Judul ini berada di dalam kartu `<x-gentelella.x-panel title="Daftar Permohonan Magang Masuk">`.
- **Dampak Redundansi**: Karena judul utama di atas sudah dinaikkan ke Hero Banner (*Daftar Pendaftaran Peserta Magang*), maka judul panel di dalam kartu ini menjadi berulang/redundant.
- **Solusi**: Nonaktifkan header panel (`:toolbox="false"` dan hilangkan atribut `title`), sehingga kartu tabel langsung menampilkan tab kategori secara bersih dan lapang.

---

## 3. Desain Komponen Baru: Sleman Hero Banner

```html
<div class="adm-hero-banner">
    <div class="adm-hero-content">
        <div class="adm-hero-text">
            <h1 class="adm-hero-title">Daftar Pendaftaran Peserta Magang</h1>
            <div class="adm-hero-subtitle">
                <span>Meja Verifikasi Berkas Pendaftaran Peserta</span>
                <span class="sep">&bull;</span>
                <span>Dinas Komunikasi dan Informatika Kabupaten Sleman</span>
                <span class="sep">&bull;</span>
                <span><strong>{{ number_format($queueStats['total'] ?? $pendaftaranList->total()) }} Total Berkas</strong></span>
            </div>
        </div>
        <div class="adm-hero-actions">
            <a href="{{ route('admin.dashboard') }}" class="adm-hero-btn-secondary">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"></polyline></svg>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('admin.pendaftaran.download-zip', request()->query()) }}" class="adm-hero-btn-primary">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                <span>Unduh Bundel ZIP</span>
            </a>
        </div>
    </div>
</div>
```

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :---: | :--- |
| `resources/views/admin/pendaftaran/index.blade.php` | Modify | 1. Hapus `<x-gentelella.page-title>` (Gambar 2).<br>2. Sisipkan `.adm-hero-banner` baru (Gambar 1) dengan judul *"Daftar Pendaftaran Peserta Magang"* dan tombol aksi terintegrasi.<br>3. Ubah `<x-gentelella.x-panel title="Daftar Permohonan Magang Masuk">` menjadi `<x-gentelella.x-panel :toolbox="false">` tanpa atribut title untuk menghapus judul panel lama (Gambar 3).<br>4. Tambahkan styling scoped untuk `.adm-hero-banner` lengkap dengan dukungan *dark mode* dan responsivitas mobile. |

---

## 5. Rencana Langkah Implementasi Terstruktur

- [ ] **Langkah 1**: Tambahkan styling CSS scoped pada blok `<style>` di `resources/views/admin/pendaftaran/index.blade.php`:
  - `.adm-hero-banner`: Gradien navy `#01347C` s/d `#003566`, border-radius 16px, padding lapang, bayangan modern.
  - `.adm-hero-title`: Font Lexend/Inter, size 1.5rem, weight 800, putih.
  - `.adm-hero-subtitle`: Warna `rgba(255, 255, 255, 0.9)`, bullet separator, font size 0.875rem.
  - `.adm-hero-btn-primary`: Background `#ffffff`, color `#01347C`, font weight 700, border-radius 10px.
  - `.adm-hero-btn-secondary`: Background transparan beraksen putih tipis, border putih halus.
  - Aturan responsif mobile: stacking vertikal saat layar di bawah 768px.
  - Aturan dark mode: penyesuaian gradien agar tetap menyatu dengan latar belakang gelap.
- [ ] **Langkah 2**: Ganti komponen judul di baris 667:
  - Hapus `<x-gentelella.page-title title="Verifikasi Berkas Pendaftaran Peserta"> ... </x-gentelella.page-title>`.
  - Pasang komponen `.adm-hero-banner` baru.
- [ ] **Langkah 3**: Bersihkan judul panel di baris 726:
  - Ubah `<x-gentelella.x-panel title="Daftar Permohonan Magang Masuk">` menjadi `<x-gentelella.x-panel :toolbox="false">`.
- [ ] **Langkah 4**: Jalankan Quality Gates:
  - Feature & visual tests: `php artisan test --filter "AdminVerifikasiKategoriDanUploadSuratTest|AdminChildPagesVisualTest|AdminTablePaginationStandardTest"`.
  - Laravel Pint: `vendor\bin\pint.bat --test`.
  - Vite build: `npm run build`.

---

## 6. Kriteria Selesai (Definition of Done)

- [ ] Halaman verifikasi pendaftaran menampilkan Hero Banner navy khas Sleman MagKom bertuliskan *"Daftar Pendaftaran Peserta Magang"*.
- [ ] Tombol *Dashboard* dan *Unduh Bundel ZIP* terintegrasi rapi di sisi kanan banner.
- [ ] Judul panel *Daftar Permohonan Magang Masuk* di dalam kartu tabel telah dihapus bersih.
- [ ] Seluruh pengujian fitur dan linter tetap 100% hijau.

