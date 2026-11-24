# TASK & PRD IMPLEMENTATION PLAN: PARITY GRID 2-KOLOM SURAT SELESAI KELOMPOK & ADAPTASI IDENTITAS KELOMPOK PADA DETAIL PENEMPATAN PESERTA

> **Dari:** Senior Software Architect / Lead Developer  
> **Kepada:** Junior Developer / AI Implementation Engineer (Gemini 3.8 Flash Compatible)  
> **Target Repositori:** `MAGKOM` (Portal Manajemen Magang Diskominfo Kabupaten Sleman)  
> **Status Dokumen:** READY FOR REVIEW & USER APPROVAL  
> **Tingkat Prioritas:** High (Core UI Consistency & Group Experience Polish)  
> **Tanggal Dokumen:** 24 November 2026  
> **Referensi Gambar User:**
> 1. Gambar 1: `media_1795509325718_b99fe04b.jpg` (Halaman *Detail Penempatan Peserta* saat ini yang belum menampilkan identitas kelompok secara khusus)
> 2. Gambar 2: `media_1795509348646_320fac93.jpg` (Layout Grid 2-Kolom Acuan pada Surat Selesai Individu: Kolom Kiri Info Penempatan, Kolom Kanan Formulir Unggah)
> 3. Gambar 3: `media_1795509455287_8ff2f3df.png` (Detail Card Formulir Unggah Surat Selesai dengan Custom File Picker modern)

---

## 1. 🎯 RINGKASAN EKSEKUTIF & LATAR BELAKANG PRD

### 1.1 Latar Belakang Masalah (Problem Statement)
Berdasarkan evaluasi user dan perbandingan langsung terhadap ketiga gambar referensi:
1. **Ketidaksamaan Layout Workspace Surat Selesai (Kelompok vs Individu)**:
   - Pada mode individu (Gambar 2 & Gambar 3), tampilan sangat rapi dan proporsional dengan **Grid 2-Kolom Seimbang**:
     - Kolom Kiri: *Informasi Penempatan Magang* & Box Status Surat Resmi.
     - Kolom Kanan: Card *Formulir Unggah Surat Selesai* yang elegan dengan custom file picker.
   - Pada mode kelompok sebelumnya, kita membuat format horizontal satu baris yang berbeda gayanya dengan individu. User menginginkan agar **kelompok mengadopsi susunan 2-kolom persis seperti individu** (Gambar 2 & 3), lalu tabel anggota kelompok ditempatkan rapi di bawahnya.
2. **Ketiadaan Identitas Kelompok pada Halaman Detail Penempatan Peserta (Gambar 1)**:
   - Ketika staf admin membuka halaman *Detail Penempatan Peserta* (`/admin/penempatan/{id}`) untuk peserta kelompok (seperti Farhan Aditya Pratama - Gambar 1), halamannya tampak seperti peserta individu mandiri biasa.
   - Tidak ada badge identitas nama kelompok di profil banner atas, tidak ada indikator peran (Ketua Kelompok vs Anggota), dan tidak ada kartu yang menampilkan daftar rekan satu timnya.
   - Admin kesulitan mengidentifikasi konteks kelompok dan harus bolak-balik memeriksa data.

### 1.2 Target Solusi (Objectives & Key Results)
1. **Parity Grid 2-Kolom Surat Selesai Kelompok**:
   - Menerapkan susunan 2-kolom seimbang pada mode kelompok di `/admin/surat-selesai?tab=kelompok&masuk_kelompok={id}`:
     - **Kolom Kiri**: Card *Informasi Penempatan Kelompok* (Bidang, Pembimbing, Periode, Tanggal Selesai, dan Box Status Surat Tim).
     - **Kolom Kanan**: Card *Formulir Unggah Surat Selesai Tim* (persis format Gambar 3 dengan custom file picker dan tombol submit *Unggah Surat Tim*).
     - **Di Bawah Grid**: *Tabel Anggota Kelompok Mandiri* (tetap terisolasi khusus anggota tim ini saja).
2. **Identitas Kelompok Khusus pada Halaman Detail Penempatan (Gambar 1)**:
   - Pada [admin/penempatan/show.blade.php](file:///G:/MAGKOM/resources/views/admin/penempatan/show.blade.php):
     - Tambahkan **Badge Rombongan Kelompok** di samping badge status pada Profile Banner: `[ 👥 Kelompok: Tim CSIRT & Keamanan Siber Sleman ]` dan badge peran `[ 👑 Ketua Kelompok ]` atau `[ 👤 Anggota Tim ]`.
     - Tambahkan Card Khusus: **Informasi Rombongan Kelompok & Rekan Tim** di bawah metrik magang, menampilkan daftar seluruh rekan satu tim, asal kampus, status mereka, dan tombol link cepat ke detail penempatan masing-masing.
     - Modernisasi formulir unggah berkas surat selesai di area Berkas & Dokumen Magang agar senada dengan gaya Gambar 3.

---

## 2. 🛡️ ATURAN ARSITEKTUR & KEAMANAN (SENIOR GUARDRAILS)

1. **JANGAN MERUSAK STRUKTUR DATABASE (Zero-Schema Change)**:
   - Menggunakan relasi Eloquent yang sudah ada: `$penempatan->isKelompok()`, `$penempatan->pendaftaran->kelompokMagang`, dan `$penempatan->getRekanKelompokPenempatan()`.
2. **Kepatuhan Aturan Blade (STRICT NO INLINE CSS/JS)**:
   - Tidak menulis tag `<style>` atau `<script>` di dalam Blade.
   - Styling disatukan pada CSS scoped terstruktur:
     - [resources/css/pages/admin/surat-selesai-index.css](file:///G:/MAGKOM/resources/css/pages/admin/surat-selesai-index.css)
     - [resources/css/pages/admin/penempatan-detail.css](file:///G:/MAGKOM/resources/css/pages/admin/penempatan-detail.css)
3. **Standar Runtime & Testing**:
   - Seluruh command verifikasi dan artisan dijalankan menggunakan PHP 8.3 aktual repositori: `F:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe`.

---

## 3. 🎨 WIREFRAME VISUAL PERUBAHAN (BEFORE VS AFTER)

### 3.1 Halaman Surat Selesai Magang (Mode Kelompok)
Menyelaraskan struktur dengan Gambar 2 & Gambar 3:
```
+---------------------------------------------------------------------------------------------------+
| [Hero Banner Sleman Navy - Unggah Surat Selesai: Tim CSIRT & Keamanan Siber Sleman]   [< Kembali] |
+---------------------------------------------------------------------------------------------------+
| [Icon] Tim CSIRT & Keamanan Siber Sleman • UNY • 3 Anggota • Bidang ITI      [ ⚠️ BELUM DIUNGGAH ] |
+--------------------------------------------------+------------------------------------------------+
| INFORMASI PENEMPATAN KELOMPOK                    | FORMULIR UNGGAH SURAT SELESAI                  |
| (Kolom Kiri - Persis Gaya Gambar 2)              | (Kolom Kanan - Persis Gaya Gambar 3)           |
|                                                  |                                                |
| BIDANG PENEMPATAN                                | Unggah berkas resmi Surat Keterangan Selesai   |
| Infrastruktur Teknologi Informasi (ITI)          | Magang Tim format PDF ukuran maksimal 2MB.     |
|                                                  |                                                |
| PEMBIMBING LAPANGAN                              | [ 📎 Pilih Berkas PDF ] [ Belum ada file... ]   |
| Sri Wahyuni, S.I.Kom                             |                                                |
|                                                  | +--------------------------------------------+ |
| PERIODE & TANGGAL SELESAI                        | | 📤 Unggah Surat Tim (Broadcast Otomatis)   | |
| 24 Agt 2026 s/d 22 Nov 2026                      | +--------------------------------------------+ |
|                                                  |                                                |
| [ Box Status Surat Tim: Menunggu Pengunggahan ]  |                                                |
+--------------------------------------------------+------------------------------------------------+
| DAFTAR ANGGOTA KELOMPOK: TIM CSIRT & KEAMANAN SIBER SLEMAN (3 ORANG)                              |
| +----+-----------------------+------------------+-------------------+-------------------+-----+   |
| | NO | NAMA ANGGOTA          | PERAN TIM        | NIM / INSTITUSI   | STATUS SURAT TIM  | AKSI|   |
| +----+-----------------------+------------------+-------------------+-------------------+-----+   |
| | 1  | Farhan Aditya Pratama | [Ketua Kelompok] | 21520244001 • UNY | Belum Diterbitkan | [...]   |
| | 2  | Maya Safira Kusuma    | [Anggota]        | 21520244002 • UNY | Belum Diterbitkan | [...]   |
| | 3  | Gilang Pamungkas      | [Anggota]        | 21520244003 • UNY | Belum Diterbitkan | [...]   |
+----+-----------------------+------------------+-------------------+-------------------+-----+   |
```

---

### 3.2 Halaman Detail Penempatan Peserta (`admin.penempatan.show` - Gambar 1)
Ketika peserta berasal dari rombongan kelompok:
```
+---------------------------------------------------------------------------------------------------+
| Detail Penempatan Peserta                                          [< Kembali] [Pratinjau] [Unduh]|
+---------------------------------------------------------------------------------------------------+
| [Foto] FARHAN ADITYA PRATAMA   [ Selesai Magang ]   [ Jalur Reguler ]                             |
|        [ 👥 Kelompok: Tim CSIRT & Keamanan Siber Sleman ]   [ 👑 Ketua Kelompok ]                 |
|        NIM/NISN: 21520244001 • Institusi: Universitas Negeri Yogyakarta • Jurusan: TI            |
+---------------------------------------------------------------------------------------------------+
| [Tugas Selesai 0/0]   [Total Logbook 0 Mg]   [Status: Selesai Magang]   [Durasi: 13 Minggu]       |
+--------------------------------------------------+------------------------------------------------+
| INFORMASI PENEMPATAN & SURAT KEPUTUSAN           | PEMBIMBING LAPANGAN                            |
| Nomor SK: 500.12/0068/DISKOMINFO/2026            | Sri Wahyuni, S.I.Kom                           |
| Bidang: Infrastruktur Teknologi Informasi (ITI)  |                                                |
| Tgl Mulai: 24 Agt 2026 • Tgl Selesai: 22 Nov 2026| ---------------------------------------------- |
|                                                  | BERKAS & DOKUMEN MAGANG                        |
| ------------------------------------------------ | Surat Diterima Magang [Lihat] [Unduh]          |
| 👥 INFORMASI ROMBONGAN KELOMPOK & REKAN TIM      |                                                |
| Kelompok: Tim CSIRT & Keamanan Siber Sleman      | Formulir Unggah Surat Selesai Tim:             |
| Total: 3 Peserta (1 Ketua + 2 Anggota)           | [ 📎 Pilih PDF ] [ Belum ada file... ]         |
| 1. Farhan Aditya Pratama (Anda / Ketua)          | [ 📤 Unggah Surat Tim (1x untuk semua) ]       |
| 2. Maya Safira Kusuma [Lihat Penempatan ->]      |                                                |
| 3. Gilang Pamungkas   [Lihat Penempatan ->]      |                                                |
+--------------------------------------------------+------------------------------------------------+
```

---

## 4. 📁 MATRIKS PERUBAHAN TEKNIS BERKAS

| No | File Path | Aksi | Tanggung Jawab & Detail Perubahan |
| :--- | :--- | :---: | :--- |
| 1 | `resources/views/admin/surat-selesai/index.blade.php` | **Modify** | Pada Section `3.B (Mode Kelompok)`: Susun ulang menjadi Grid 2-Kolom (`.focused-content-grid`) berisi card Info Penempatan Kelompok (kiri) dan Formulir Unggah Surat Tim ala Gambar 3 (kanan). Di bawahnya tetap tampil Tabel Anggota Kelompok mandiri. |
| 2 | `resources/css/pages/admin/surat-selesai-index.css` | **Modify** | Pastikan style `.focused-content-grid`, `.focused-card`, `.info-card`, `.upload-card`, dan custom file picker berlaku harmonis baik untuk tab Individu maupun Kelompok. |
| 3 | `resources/views/admin/penempatan/show.blade.php` | **Modify** | Tambahkan badge identitas kelompok & peran tim di Profile Banner. Tambahkan Card Informasi Rombongan Kelompok & Rekan Tim di kolom kiri. Perbarui form unggah surat selesai di card Berkas Dokumen dengan custom file picker. |
| 4 | `resources/css/pages/admin/penempatan-detail.css` | **Modify** | Tambahkan styling badge kelompok `.kelompok-badge-pill`, peran `.role-badge-pill`, card rekan kelompok `.team-rekan-box`, dan styling form upload modern di detail penempatan. |
| 5 | `resources/js/pages/admin/penempatan-show.js` | **Modify** | Tambahkan client-side preview file input untuk form upload surat selesai di halaman detail penempatan. |
| 6 | `tests/Feature/AdminSuratSelesaiIndividuKelompokTest.php` | **Modify** | Verifikasi bahwa grid 2-kolom kelompok me-render informasi penempatan kelompok dan formulir unggah surat selesai. |

---

## 5. 🛠️ RENCANA TAHAPAN EKSEKUSI (STEP-BY-STEP)

### Tahap 1: Implementasi Parity 2-Kolom di Surat Selesai Kelompok (`index.blade.php`)
1. Buka `resources/views/admin/surat-selesai/index.blade.php` pada section `3.B` (Mode Masuk Kelompok).
2. Buat container `<div class="focused-content-grid">`:
   - **Card Kiri (`.focused-card.info-card`)**:
     - Judul: *Informasi Penempatan Kelompok*
     - Rincian: Bidang Penempatan, Pembimbing Lapangan, Periode Magang, Tanggal Selesai Tim, Total Anggota.
     - Box Status Surat Tim: Jika sudah terbit tampilkan tanggal upload + tombol Unduh Salinan Surat Tim; jika belum terbit tampilkan alert kuning *Menunggu Pengunggahan Berkas Tim*.
   - **Card Kanan (`.focused-card.upload-card`)**:
     - Judul: *Formulir Unggah Surat Selesai* (Persis seperti Gambar 3)
     - Deskripsi: *Unggah berkas resmi Surat Keterangan Selesai Magang format PDF dengan ukuran maksimal 2MB.*
     - File input control: `[ 📎 Pilih Berkas PDF ]` + `[ Belum ada berkas PDF dipilih (Maks 2.0 MB) ]`
     - Tombol submit: `[ 📤 Unggah Surat Tim ]` (Full-width Sleman Navy).
3. Di bawah grid 2-kolom, tempatkan container `.dedicated-members-panel` (Tabel Anggota Kelompok).

### Tahap 2: Implementasi Khusus Kelompok di Detail Penempatan (`show.blade.php`)
1. Buka `resources/views/admin/penempatan/show.blade.php`.
2. Di bagian Profile Banner (baris status & jalur):
   - Tambahkan pengecekan `@if($penempatan->isKelompok())`:
     ```blade
     @php
         $kelompokData = $penempatan->pendaftaran?->kelompokMagang;
         $isKetua = $kelompokData && (int)$kelompokData->ketua_user_id === (int)$peserta?->id;
     @endphp
     <span class="kelompok-badge-pill">
         <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
         <span>{{ $kelompokData?->nama_kelompok ?? __('Rombongan Kelompok') }}</span>
     </span>
     <span class="role-badge-pill {{ $isKetua ? 'ketua' : 'anggota' }}">
         {{ $isKetua ? __('Ketua Kelompok') : __('Anggota Tim') }}
     </span>
     ```
3. Di bawah Card Informasi Penempatan & Surat Keputusan:
   - Tambahkan Card Baru: **Informasi Rombongan Kelompok & Rekan Tim**:
     - Tampilkan nama kelompok, kampus asal, dan daftar rekan satu tim yang diperoleh dari `$penempatan->getRekanKelompokPenempatan()`.
     - Setiap baris rekan menampilkan avatar inisial, nama, peran (Ketua/Anggota), dan link ke halaman detail penempatan rekan tersebut.
4. Di bagian form unggah surat selesai pada Card Berkas & Dokumen:
   - Gantikan file input bawaan browser dengan custom file picker bergaya Sleman Navy (`[ 📎 Pilih Berkas PDF ]` + preview text + tombol submit).

### Tahap 3: Styling CSS di `penempatan-detail.css` & `surat-selesai-index.css`
1. Di `penempatan-detail.css`:
   - Tambahkan styling untuk `.kelompok-badge-pill`, `.role-badge-pill`, `.team-rekan-box`, `.rekan-item-row`, dan `.btn-doc-custom-picker`.
2. Di `surat-selesai-index.css`:
   - Pastikan styling `.focused-content-grid` dan `.focused-card` di tab kelompok memiliki margin, gap, dan padding yang identik dengan tab individu.

### Tahap 4: Interaktivitas JavaScript di `penempatan-show.js` & `surat-selesai-index.js`
- Pasang listener `change` pada input file di halaman detail penempatan agar nama file terpilih muncul di preview bar inline.

### Tahap 5: Quality Gate & Automated Testing
- Jalankan test suite:
  `& "F:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe" artisan test --filter=SuratSelesai`
  `& "F:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe" artisan test --filter=Penempatan`
- Jalankan Pint:
  `& "F:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe" vendor/bin/pint --test`
- Jalankan Vite build:
  `npm run build`

---

## 6. ✅ DEFINITION OF DONE (DOD) & CHECKLIST

- [ ] Halaman Surat Selesai Magang Kelompok memiliki layout Grid 2-Kolom persis seperti Individu (Gambar 2 & 3).
- [ ] Formulir Unggah Surat Selesai Tim di halaman Surat Selesai Kelompok identik tampilannya dengan Gambar 3.
- [ ] Tabel Anggota Kelompok tetap berada di bawah Grid 2-Kolom dan terisolasi mandiri.
- [ ] Halaman Detail Penempatan Peserta (`admin.penempatan.show` - Gambar 1) memiliki badge identitas kelompok & peran tim di Profile Banner.
- [ ] Halaman Detail Penempatan Peserta memiliki Card Rincian Kelompok & Rekan Satu Tim untuk peserta kelompok.
- [ ] Formulir unggah surat di halaman detail penempatan menggunakan custom file picker modern (bukan button default browser).
- [ ] Zero inline CSS/JS di Blade.
- [ ] Seluruh automated test PHPUnit lolos 100% dengan PHP 8.3.
- [ ] Vite asset compilation sukses tanpa error.
