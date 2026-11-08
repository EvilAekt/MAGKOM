# Rencana Kerja: Penghapusan Card Empty State & Penyederhanaan Tab Antrean Penempatan

> **Dokumen Rencana**: `docs/plans/2026-11-08-penyederhanaan-empty-state-antrean-penempatan.md`  
> **Status**: *Draft Proposal Menunggu Persetujuan Pengguna*  
> **Target Branch**: `test-2`  
> **File Utama**: `resources/views/admin/penempatan/index.blade.php`, `tests/Feature/AdminChildPagesVisualTest.php`

---

## 1. Analisis Kritis & Sudut Pandang Senior Developer

### Masalah yang Dikeluhkan Pengguna:
Pengguna mengirimkan tangkapan layar kartu kosong di tab **"Belum Ditempatkan"** yang bertuliskan:
> *"Semua Peserta Sudah Ditempatkan"*  
> *"Tidak ada calon peserta lolos berkas yang menunggu penetapan pembimbing lapangan saat ini..."*  
> Beserta tombol raksasa selebar layar: `← Lihat Data Penempatan Selesai/Aktif`

Pengguna mengkritisi:
> *"hapus tampilan & teks ini (karena sangat tidak masuk akal, aneh, dan jadi rame dan tidak tertata. Masa iya di 'Belum Ditempatkan' juga ada tulisan seperti itu kan ga logis, menurut kamu gimana?)"*

---

### Pendapat & Evaluasi Senior Developer:
**Pemikiran Pengguna 100% TEPAT, LOGIS, dan SANGAT RASIONAL.**

Berikut alasan teknis & UX mengapa tampilan tersebut memang buruk dan harus dihapus:

1. **Inkoneksi Logika Konsep (Cognitive Dissonance)**:
   - Admin sedang sengaja mengklik tab **"Belum Ditempatkan"** untuk mengecek antrean calon peserta yang belum dapat pembimbing.
   - Menampilkan tulisan besar *"Semua Peserta Sudah Ditempatkan"* di dalam tab *"Belum Ditempatkan"* terasa membingungkan secara psikologis. Admin datang untuk melihat antrean, bukan untuk membaca banner promosi status tab sebelah.
2. **Redundansi Navigasi (Tombol Mubazir)**:
   - Tombol selebar layar `← Lihat Data Penempatan Selesai/Aktif` sangat redundan. Tepat di atasnya (hanya berjarak 30px) sudah ada tombol navigasi tab **"Sudah Ditempatkan (3)"**.
   - Admin tidak membutuhkan tombol tambahan raksasa untuk berpindah tab.
3. **Tata Letak Tidak Konsisten & Visual Rusak (Layout Break)**:
   - Ketika ada antrean, tampilan berupa **Tabel Rapi**. Namun ketika antrean kosong (0 data), struktur tabel mendadak hilang dan berganti menjadi kartu raksasa dengan tombol membentang penuh 100% selebar layar karena adanya sisa tag duplikat.
   - Hal ini membuat antarmuka terasa "ramai", melompat (*layout shift*), dan tidak profesional.

---

## 2. Solusi Desain yang Diusulkan

### A. Konsep Solusi: Konsistensi Struktur Tabel Bersih (Clean Table Layout)
Alih-alih menghapus tabel dan menggantinya dengan kartu raksasa bertombol lebar:
1. **Tabel Antrean Tetap Tampil Secara Terstruktur**:
   - Header tabel tetap tampil rapi:
     - `Peserta Magang`
     - `Jurusan & Kampus / Sekolah`
     - `Bulan Magang`
     - `Diterima Pada`
     - `Aksi`
2. **Baris Empty State yang Tenang dan Elegan (Single Clean Row)**:
   - Jika tidak ada peserta dalam antrean (`count === 0`), `<tbody>` menampilkan baris kosong yang bersih:
     ```html
     <tr>
         <td colspan="5" style="text-align: center; padding: 3rem 1.5rem; color: #64748B;">
             <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 0.5rem;">
                 <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#94A3B8" stroke-width="1.75">
                     <circle cx="12" cy="12" r="10"></circle>
                     <polyline points="12 6 12 12 16 14"></polyline>
                 </svg>
                 <span style="font-weight: 600; font-size: 0.9375rem; color: #475569;">Tidak ada antrean peserta belum ditempatkan</span>
                 <span style="font-size: 0.8125rem; color: #94A3B8;">Seluruh peserta yang lolos seleksi berkas telah memiliki pembimbing lapangan.</span>
             </div>
         </td>
     </tr>
     ```
3. **Manfaat Desain Ini**:
   - ✅ **Layout Tetap Kokoh**: Tinggi dan lebar halaman tidak melompat (*zero layout shift*).
   - ✅ **Tidak Berisik**: Tidak ada kartu raksasa atau tombol biru yang membentang aneh selebar layar.
   - ✅ **Logis & Profesional**: Tab *Belum Ditempatkan* tetap memiliki struktur antrean yang siap menerima data pendaftar baru kapan saja.

---

## 3. Rincian File yang Dimodifikasi

1. **`resources/views/admin/penempatan/index.blade.php`**:
   - Menghapus blok kartu `@else ... <div class="admin-card"> Semua Peserta Sudah Ditempatkan ...` beserta duplikat HTML dan tombol raksasa.
   - Mengubah struktur antrean sehingga container card antrean (`Antrean Peserta Lolos Berkas (Menunggu Penempatan)`) selalu merender tabel dengan header konsisten.
   - Pada `<tbody>`, jika `$antreanBelumDitempatkan->isEmpty()`, render satu baris empty state yang tenang dan bersih di dalam tabel.
2. **`tests/Feature/AdminChildPagesVisualTest.php`**:
   - Menyesuaikan assertion pada `test_admin_penempatan_belum_tab_renders_empty_state_when_all_placed` untuk memeriksa teks empty state baru di dalam tabel dan memastikan tidak ada tombol raksasa lama.

---

## 4. Rencana Verifikasi & Quality Gates

1. **Pengujian Visual**:
   - Buka tab "Belum Ditempatkan" saat antrean 0: Pastikan tabel tampil rapi, header sejajar dengan filter, tidak ada tombol biru raksasa selebar layar, dan tidak ada teks "Semua Peserta Sudah Ditempatkan" yang mencolok.
   - Buka tab "Belum Ditempatkan" saat ada antrean: Pastikan daftar calon peserta tampil dan tombol *Tempatkan* berfungsi normal.
2. **Quality Gates Otomatis**:
   - Jalankan `vendor\bin\pint.bat --test` (100% PSR-12 clean).
   - Jalankan `php artisan test tests/Feature/AdminChildPagesVisualTest.php` (Pass).
   - Jalankan seluruh suite test penempatan `AdminPenempatan` (Pass).
   - Jalankan `npm run build` (Pass).

