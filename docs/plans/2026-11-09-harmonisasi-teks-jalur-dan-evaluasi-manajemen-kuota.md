# Rencana Kerja: Penyelarasan Label Jalur Khusus & Penyempurnaan Kontrol Manajemen Kuota Magang

Dokumen ini merinci rencana implementasi untuk dua kebutuhan yang diminta oleh pengguna:
1. **Penyelarasan Warna Label Jalur Khusus** pada tabel Penempatan Peserta (`/admin/penempatan`) agar bersih, senada, dan konsisten dengan label Umum tanpa kotak/border oranye.
2. **Penyempurnaan Fitur Manajemen Kuota Magang** (`/admin/kuota`) yang mencakup:
   - Pengeditan tanggal buka dan tanggal tutup bulan magang.
   - Kontrol menutup atau membuka penempatan kuota per bidang dinas.
   - Pengeditan total kapasitas peserta yang bisa masuk per bidang penempatan secara logis dan terintegrasi.

---

## 1. Analisis Kebutuhan & Kondisi Saat Ini

### A. Gambar SS 1: Label Jalur Khusus di Tabel Penempatan
- **Lokasi Berkas**: `resources/views/admin/penempatan/index.blade.php` (baris 1075–1083)
- **Kondisi Eksisting**:
  Label `Khusus` saat ini menggunakan styling badge kotak oranye:
  ```html
  <span style="font-size: 0.7rem; font-weight: 700; padding: 0.15rem 0.5rem; border-radius: 4px; background: #FEF3C7; color: #B45309; border: 1px solid #FCD34D;">
      Khusus
  </span>
  ```
  Sedangkan label `Umum` menggunakan teks netral tanpa kotak border:
  ```html
  <span style="font-size: 0.75rem; font-weight: 600; color: var(--neutral-500, #64748B);">
      Umum
  </span>
  ```
- **Tindakan**:
  Menghilangkan styling kotak oranye (`background: #FEF3C7`, `border: 1px solid #FCD34D`) dan menyamakan styling teks `Khusus` agar serasi dengan `Umum` (`font-size: 0.75rem; font-weight: 600; color: var(--neutral-500, #64748B);`).

---

### B. Gambar SS 2: Menu Manajemen Kuota Magang
- **Lokasi Berkas Utama**:
  - View Blade: `resources/views/admin/kuota/index.blade.php`
  - Controller: `app/Http/Controllers/Admin/KuotaController.php`
  - Model: `app/Models/BulanMagang.php`, `app/Models/Kuota.php`, `app/Models/Bidang.php`
- **Tiga Kebutuhan Fungsional**:
  1. **Mengedit bulan kapan magang dibuka dan tutup**:
     - Modal `#modalUbahBulan` sudah ada, namun perlu dipastikan sinkronisasi data tanggal buka (`tgl_buka`), tanggal tutup (`tgl_tutup`), dan tanggal mulai magang (`bulan_mulai`) berjalan mulus, validasi tanggal logis (`tgl_tutup >= tgl_buka`, `bulan_mulai > tgl_tutup`), dan status pendaftaran ter-update seketika di hero banner.
  2. **Menutup atau membuka penempatan kuota bidang**:
     - Saat ini tabel "Kuota per bidang" hanya menampilkan angka kapasitas (misal: IKP 0 peserta, Sekretariat 0 peserta) tanpa indikator status eksplisit apakah bidang tersebut sedang "Dibuka" atau "Ditutup".
     - Solusi: Sediakan indikator status kuota bidang ("Dibuka" / "Ditutup") pada tabel dan kontrol toggle/status penempatan di modal ubah bidang untuk mengaktifkan atau menonaktifkan kuota bidang tersebut di periode bulan magang yang dipilih.
  3. **Mengedit total kapasitas yang bisa masuk per bidang penempatan**:
     - Di modal ubah kuota bidang (`#modalEditBidang`), tambahkan ringkasan total kapasitas bidang yang menghitung alokasi peserta secara live, validasi terhadap kapasitas maksimal bimbingan pembimbing aktif (`pembimbing_count * 6`), dan perlindungan agar kuota tidak diturunkan di bawah jumlah peserta yang sudah diterima.

---

## 2. Rincian Rencana Implementasi

### Bagian 1: Perapihan Label Jalur Khusus di Tabel Penempatan
1. **File Target**: `resources/views/admin/penempatan/index.blade.php`
2. **Perubahan Markup**:
   - Ganti styling badge oranye pada baris 1076–1078 menjadi styling teks netral yang identik dengan label Umum:
     ```html
     @if($p->slotKhusus)
         <span style="font-size: 0.75rem; font-weight: 600; color: var(--neutral-500, #64748B);" title="{{ $p->slotKhusus->nama_mitra ? 'Mitra: '.$p->slotKhusus->nama_mitra : 'Jalur Khusus' }}">
             Khusus
         </span>
     @else
         <span style="font-size: 0.75rem; font-weight: 600; color: var(--neutral-500, #64748B);">
             Umum
         </span>
     @endif
     ```

---

### Bagian 2: Penyelarasan & Penyempurnaan Manajemen Kuota

#### 2.1 Edit Buka/Tutup Bulan Magang
- **File Target**: `resources/views/admin/kuota/index.blade.php` & `app/Http/Controllers/Admin/KuotaController.php`
- **Tindakan**:
  - Pastikan modal `#modalUbahBulan` memuat field:
    1. Tanggal Buka Pendaftaran (`tgl_buka`)
    2. Tanggal Tutup Pendaftaran (`tgl_tutup`)
    3. Tanggal Mulai Magang (`bulan_mulai`)
    4. Status Bulan (Pills: Draft / Dibuka)
  - Tambahkan interaksi validasi dinamis (event listener `change`) pada modal Ubah Bulan agar admin tidak bisa memilih tanggal tutup sebelum tanggal buka, atau tanggal mulai magang sebelum/sama dengan tanggal tutup.
  - Tampilkan banner feedback status pendaftaran yang jelas di bagian hero atas: badge **"Dibuka"** (hijau jika aktif & periode pendaftaran berjalan) atau **"Ditutup"** (abu-abu/merah jika periode berakhir/draft).

#### 2.2 Kontrol Buka / Tutup Penempatan Kuota per Bidang
- **Tindakan**:
  - **Di Tabel Kuota per Bidang**:
    - Tambahkan badge status ketersediaan bidang di kolom Kapasitas:
      - **Dibuka**: Badge hijau pastel jika bidang memiliki kapasitas > 0.
      - **Ditutup**: Badge abu-abu/merah pastel jika bidang memiliki kapasitas = 0 atau kuota dinonaktifkan.
  - **Di Modal Ubah Kuota Bidang (`#modalEditBidang`)**:
    - Tambahkan toggle/pilihan **Status Kuota Bidang**:
      - `Dibuka` (Aktifkan kuota jurusan dan isi kapasitas peserta)
      - `Ditutup` (Tutup penempatan bidang ini untuk periode bulan magang yang dipilih).
    - Jika admin memilih "Ditutup", nilai alokasi kuota jurusan diset 0 atau dinonaktifkan (selama belum ada peserta diterima, sesuai aturan BR-12 & AC-05-6).
    - Jika admin memilih "Dibuka", form input alokasi kapasitas diaktifkan sehingga admin dapat menentukan kursi magang.

#### 2.3 Edit Total Kapasitas Masuk per Bidang Penempatan
- **Tindakan**:
  - **Di Modal Ubah Kuota Bidang (`#modalEditBidang`)**:
    - Tampilkan **Live Counter Ringkasan Kapasitas**:
      `Total Kapasitas: X Peserta / Batas Maksimum: Y Peserta (Z Pembimbing)`
    - Setiap kali input kuota kejuruan diubah, counter total kapasitas langsung ter-update secara otomatis tanpa reload.
    - Berikan peringatan visual jika total kapasitas yang dimasukkan melampaui batas bimbingan pembimbing aktif di bidang tersebut (`pembimbing_count * 6`).
  - **Di Backend (`KuotaController@updateBidangKuota`)**:
    - Validasi bahwa total alokasi yang dikirimkan tidak melanggar batas kapasitas bimbingan pembimbing.
    - Lindungi integritas data: alokasi kuota per jurusan tidak boleh lebih rendah dari peserta yang sudah diterima (`terisi`).
    - Catat histori perubahan ke dalam `AuditService`.

---

## 3. Matriks Pengujian & Verifikasi (Quality Gates)

| Komponen | Metode Pengujian | Target Keberhasilan |
|---|---|---|
| **Label Jalur Khusus** | `tests/Feature/DeltaDashboardEnhancementTest.php` / Visual Check | Label `Khusus` tampil tanpa kotak oranye, teks netral `#64748B`. |
| **Edit Bulan Magang** | `AdminKuotaRevampTest::test_admin_can_update_existing_bulan_magang` | Tanggal buka, tutup, dan mulai magang tersimpan dan tervalidasi dengan benar. |
| **Buka/Tutup Bidang** | Feature test baru / method `updateBidangKuota` | Bidang dapat diset buka/tutup, status tabel merefleksikan status kuota. |
| **Total Kapasitas Bidang** | `AdminKuotaRevampTest::test_kuota_page_displays_quota_per_bidang_table` | Kapasitas bidang teragregasi tepat dan tidak melebihi kapasitas pembimbing. |
| **Code Style** | `vendor/bin/pint --test` | 100% PSR-12 compliant. |
| **Full Regression** | `vendor/bin/phpunit` | Seluruh 719 tests (4979+ assertions) 100% HIJAU. |

