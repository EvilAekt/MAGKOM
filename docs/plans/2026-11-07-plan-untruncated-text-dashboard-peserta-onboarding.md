# Rencana Kerja: Visual Tanpa Pemotongan Teks (Untruncated Text) pada Dashboard Peserta POV Belum Diterima

## 1. Konteks & Latar Belakang
Pada dashboard peserta untuk **POV belum diterima** (fase *Onboarding* dan *Seleksi*):
- Pengguna melaporkan bahwa tulisan pada statusline bar terpotong elipsis (`...`), khususnya pada item **PILIHAN BIDANG** yang menampilkan `Informasi dan Komunikasi Publik (IKP), Se...` (seperti yang terlihat pada tangkapan layar `media_1794042374152_ea16cba7.png`).
- Pemotongan tersebut diakibatkan oleh styling inline kaku `white-space: nowrap; overflow: hidden; text-overflow: ellipsis;` serta pembagian kolom grid `repeat(auto-fit, minmax(13.5rem, 1fr))` yang membatasi lebar kolom tengah sama persis dengan kolom teks pendek.
- Selain itu, perlu diaudit seluruh komponen teks pada POV belum diterima (*Atmospheric Hero Banner*, *Statusline Ribbon*, *Panduan Alur 3 Langkah*, dan *Kartu Status Seleksi Berkas*) agar seluruh teks tampil utuh, membungkus secara natural (*natural text wrap*), dan responsif di berbagai resolusi layar tanpa ada satu pun kata atau huruf yang terpotong.

---

## 2. Analisis Masalah & Titik Pemotongan

| Lokasi Komponen | Teks / Konten | Penyebab Pemotongan | Solusi Perbaikan |
|---|---|---|---|
| **Statusline Bar** (`saas-statusline`) — Item Pilihan Bidang | Nama bidang magang (misal: *"Informasi dan Komunikasi Publik (IKP), Sekretariat Dinas"*) | Properti CSS inline `white-space: nowrap; overflow: hidden; text-overflow: ellipsis;` dan kolom sempit | Hapus `nowrap` dan `text-overflow: ellipsis`. Terapkan `white-space: normal; word-break: break-word; line-height: 1.4;`. Alokasikan lebar kolom proporsional lebih luas untuk bidang. |
| **Statusline Bar** — Item Status Magang & Tipe Pendaftaran | Status (misal: *"Seleksi / Onboarding"*) & Tipe (misal: *"Kelompok: Tim Inovasi"* atau *"Mandiri (Individu)"*) | Risiko terpotong jika nama kelompok panjang atau pada viewport mobile | Pastikan `white-space: normal; word-break: break-word;` dengan flex flow adaptif. |
| **Grid Statusline** (`.saas-statusline`) | Tata letak 3 kolom statusline | `grid-template-columns: repeat(auto-fit, minmax(13.5rem, 1fr))` membatasi kolom kedua | Ubah tata letak menjadi grid adaptif dengan bobot fleksibel: Desktop `grid-template-columns: minmax(12rem, 1fr) minmax(18rem, 2fr) minmax(12rem, 1fr)` dengan `align-items: center`, atau `flex: 1 1 auto` sehingga kolom bidang dapat memanfaatkan ruang kosong. |
| **Alur Pendaftaran** (`_phase_onboarding.blade.php`) | Judul 3 langkah & status badge (`✓ Selesai`, `✓ Terpilih`, `✓ Diajukan`) | Potensi overflow pada judul panjang atau badge di layar resolusi menengah | Pastikan `.saas-step-title` membungkus baris secara rapi (`flex-wrap: wrap; gap: 8px; line-height: 1.3;`) tanpa clipping. |
| **Detail Seleksi** (`_phase_seleksi.blade.php`) | Jurusan terdaftar, periode, durasi, kelengkapan berkas, catatan revisi | Potensi pemotongan nama jurusan panjang di `.saas-info-item strong` | Pastikan `word-break: break-word; white-space: normal;` di `.saas-info-item`. |

---

## 3. Rencana Implementasi Bertahap

### Tahap 1: Refactor Statusline Bar di `dashboard.blade.php`
1. **Hapus properti pemotong teks**:
   - Hapus `white-space: nowrap; overflow: hidden; text-overflow: ellipsis;` dari elemen nilai `Pilihan Bidang`.
   - Pasang `white-space: normal; word-break: break-word; line-height: 1.4; font-size: 0.875rem; color: #0f172a; font-weight: 600;`.
2. **Optimasi Grid Statusline**:
   - Perbarui inline style / class `.saas-statusline`:
     ```html
     <div class="saas-statusline" role="region" aria-label="Statusline MagKom Peserta" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(15rem, 1fr)); gap: 14px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px 18px; box-shadow: 0 1px 4px rgba(15, 23, 42, 0.05); align-items: center;">
     ```
   - Berikan item tengah (Pilihan Bidang) fleksibilitas penuh agar jika berada dalam satu baris bersama kolom 1 dan 3, ia mendapatkan porsi ruang yang memadai.
3. **Format Badge / Separator Bidang**:
   - Jika peserta memiliki lebih dari 1 bidang rekomendasi/jurusan (terpisah koma), tampilkan dengan pemisah yang rapi atau badge inline yang mudah dibaca tanpa saling tumpang tindih.

### Tahap 2: Audit Teks pada Stepper Alur 3 Langkah (`_phase_onboarding.blade.php`)
1. Periksa class `.saas-step-item`, `.saas-step-title`, dan `.saas-step-state`:
   - Pastikan teks tidak pernah menggunakan `nowrap` yang memotong nama langkah:
     - `1. Lengkapi Biodata & Profil Peserta`
     - `2. Pilih Periode & Kuota Magang`
     - `3. Unggah Berkas & Ajukan Pendaftaran`
   - Pastikan badge status (`✓ Selesai`, `✓ Terpilih`, `✓ Diajukan`) tetap utuh di samping judul atau berada di baris baru yang rapi saat ruang sempit.

### Tahap 3: Audit Teks pada Kartu Fase Seleksi (`_phase_seleksi.blade.php`)
1. Periksa strip info status pendaftaran:
   - Jurusan Terdaftar: pastikan nama jurusan panjang (misal: "Administrasi Perkantoran / Rekayasa Perangkat Lunak") membungkus secara estetik tanpa elipsis.
   - Catatan revisi verifikator: pastikan box catatan revisi memiliki wrapping teks alami (`overflow-wrap: anywhere`).

### Tahap 4: Verifikasi & Quality Gate
1. Bersihkan compiled view cache:
   ```powershell
   php artisan view:clear
   ```
2. Jalankan rangkaian Feature Test:
   ```powershell
   php artisan test tests/Feature/PesertaFullPortalFeatureTest.php tests/Feature/GentelellaDashboardRevampTest.php tests/Feature/PendaftaranPesertaTest.php
   ```
3. Lakukan pengecekan formatting:
   ```powershell
   ./vendor/bin/pint --test
   ```
4. Pastikan zero regression terhadap business logic, routing, dan kontrak tes yang sudah ada.

---

## 4. Pertanyaan / Konfirmasi Sebelum Eksekusi
- Apakah Anda setuju teks nama bidang panjang ditampilkan penuh dengan pembungkusan baris alami (*word-wrapping*) ke baris kedua, atau lebih disukai format pemisah berupa chip/badge per bidang?

