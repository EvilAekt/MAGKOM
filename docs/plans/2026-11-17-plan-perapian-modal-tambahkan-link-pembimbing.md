# Rencana Kerja: Perapian Modal Dialog "Tambahkan Link" Pembimbing

Dokumen ini merinci rencana perbaikan antarmuka pada dialog pop-up **"Tambahkan link"** di modul Pembimbing (Pembuatan & Pengeditan Tugas) sesuai gambar referensi yang diunggah pengguna.

---

## 1. Analisis Masalah (Perbandingan Gambar 1 vs Gambar 2)

| Bagian | Kondisi Saat Ini (Gambar 1) | Target Perbaikan (Gambar 2) |
| :--- | :--- | :--- |
| **Header Dialog** | Terdapat garis pembatas bawah (`border-bottom`) dan tidak memiliki tombol close (X). | Header bersih tanpa garis pembatas, memuat judul *"Tambahkan link"* di kiri dan tombol close *(X)* di kanan atas. |
| **Pesan Error Awal** | Teks `*Wajib diisi` berwarna merah langsung muncul saat modal pertama kali dibuka. | Bersih tanpa teks merah saat awal dibuka. Teks error hanya muncul jika user mengetik URL yang tidak valid (`http://` / `https://`). |
| **Tombol Aksi "Tambahkan link"** | Teks terbungkus menjadi 2 baris (*"Tambahkan"* dan *"link"*) di dalam tombol berbentuk oval sempit karena `width: 78px; padding: 0;`. | Tombol pill proporsional dengan `width: auto; padding: 0 20px; white-space: nowrap;` sehingga teks tetap satu baris rapi dan proporsional. |
| **Warna State Disabled** | Teks tombol abu-abu pucat di dalam pill kecil. | Background pill lembut (`#cbd5e1` / dark mode `#334155`) dengan teks kontras proporsional (`#64748b`). |

---

## 2. Alasan Kebutuhan Perubahan Markup Blade (Sesuai Aturan AGENTS.md 3.3)

Perubahan markup Blade mutlak diperlukan karena:
1. Tombol close (X) di kanan atas header modal belum ada pada struktur HTML sebelumnya.
2. Atribut `style="display: none;"` pada `#linkErrorHint` perlu dipasang agar tidak memicu layout shift atau teks merah saat awal render modal.

**File Blade yang disentuh**:
- `resources/views/pembimbing/tugas/create.blade.php` (Baris 463-479: Modal Dialog Tambahkan Link)
- `resources/views/pembimbing/tugas/edit.blade.php` (Baris 353-369: Modal Dialog Kelola Tautan Referensi untuk konsistensi)

---

## 3. Rencana Eksekusi Bertahap

### Tahap 1: Pembaruan Markup Blade (`resources/views/pembimbing/tugas/`)
1. Menambahkan tombol close `btnCloseModalLink` di dalam `tugas-dialog-header`:
   ```html
   <div class="tugas-dialog-header" id="ttlModalLink">
       <span>{{ __('Tambahkan link') }}</span>
       <button type="button" class="tugas-dialog-close-btn" id="btnCloseModalLink" aria-label="{{ __('Tutup dialog') }}">
           <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
               <line x1="18" y1="6" x2="6" y2="18"></line>
               <line x1="6" y1="6" x2="18" y2="18"></line>
           </svg>
       </button>
   </div>
   ```
2. Mengatur elemen `#linkErrorHint` default dalam keadaan tersembunyi (`display: none;` dan teks kosong).

### Tahap 2: Pembaruan Styling CSS (`resources/css/pages/pembimbing/tugas-create.css`)
1. Reset `border-bottom: none;` dan aktifkan flexbox `justify-content: space-between; align-items: center;` pada `.tugas-dialog--link .tugas-dialog-header`.
2. Tambahkan styling `.tugas-dialog-close-btn` dengan state hover yang halus.
3. Ubah `.tugas-dialog-btn-pill` dari `width: 78px; padding: 0;` menjadi:
   - `width: auto;`
   - `min-width: 80px;`
   - `padding: 0 20px;`
   - `white-space: nowrap;`
   - `border-radius: 18px;`
4. Tambahkan varian dark mode `[data-theme="dark"]` agar tetap harmonis dengan tema pembimbing.

### Tahap 3: Pembaruan Logika JavaScript (`resources/js/pages/pembimbing/tugas-create.js` & `tugas-edit.js`)
1. Tambahkan event listener click pada `btnCloseModalLink` untuk memicu `closeModal()`.
2. Perbarui fungsi `openModal()` agar membersihkan `#linkErrorHint` dan menyembunyikannya saat dialog dibuka.
3. Perbarui `validateUrl()` agar hanya memunculkan hint jika input tidak kosong tapi format URL salah.

### Tahap 4: Pengujian & Quality Gates
1. Jalankan `npm run build` untuk mengompilasi CSS/JS melalui Vite.
2. Jalankan `./vendor/bin/pint --test` untuk verifikasi format kode.
3. Jalankan `php artisan test --filter="Tugas"` untuk memastikan seluruh feature test tugas pembimbing lulus.
4. Lakukan verifikasi visual (CDP browser screenshot) pada dialog modal untuk memastikan tampilan 100% identik dengan Gambar 2.

---

## 4. Konfirmasi Pengguna
Mohon persetujuan untuk mengeksekusi rencana di atas dengan membalas pesan ini (misalnya: *"gas"* atau *"lanjut"*).

