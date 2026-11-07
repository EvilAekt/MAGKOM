# Rencana Kerja: Penataan Kolom Nomor Registrasi & Pembersihan Bilah Meta Tabel Verifikasi Berkas

Dokumen ini berisi rencana teknis dan analisis desain antarmuka (*UI/UX*) untuk menyederhanakan kolom **No. Registrasi** agar tidak padat (*cluttered*) dan sesuai standar kedinasan, serta menghapus teks redundan pada bilah meta atas tabel.

---

## 1. Tujuan
1. **Menghapus Teks Redundan**: Menghilangkan teks `"Menampilkan 1–4 dari 4 berkas"` dari bilah meta atas tabel (`.adm-table-meta-bar`) karena informasi ini sudah disajikan secara lengkap pada bilah paginasi (*pagination bar*) di footer bawah tabel.
2. **Merestrukturisasi Kolom No. Registrasi**: Menyederhanakan penyajian data pada kolom pertama tabel agar tidak terlihat padat (*rame bgt*), profesional sesuai standar portal kedinasan (Dinas Kominfo/BKD), dan nyaman dibaca (*scannable*).

---

## 2. Analisis Masalah & Kebutuhan Dinas

### A. Masalah 1: Teks Redundan pada Bilah Meta Atas (Gambar 1)
- **Kondisi Saat Ini**: Di bilah atas tabel (`.adm-table-meta-bar`), di samping pill status terdapat teks `Menampilkan 1–4 dari 4 berkas`. Sementara di footer bawah tabel sudah ada teks: `Menampilkan 1 - 4 dari 4 berkas pendaftaran` beserta kontrol paginasi (`5`, `10`, `25`).
- **Dampak**: Teks di atas memakan ruang horizontal dan menimbulkan redundansi visual.
- **Solusi**: Hapus teks tersebut dari bilah atas, sehingga sisi kanan bilah atas hanya menampilkan tautan `Reset Filter` (jika filter aktif), menjaga fokus penuh verifikator pada pill filter status.

### B. Masalah 2: Kolom No. Registrasi Terlalu Penuh / Rame (Gambar 2)
- **Kondisi Saat Ini**: Dalam satu sel vertikal terdapat 3 elemen bertumpuk:
  1. Box badge biru: `#REG-00006`
  2. Teks tanggal: `06 Nov 2026`
  3. Box badge biru muda dengan ikon: `[Icon] Individu`
- **Akar Masalah**:
  1. Terlalu banyak elemen berkotak (*boxed badges*) dalam satu sel sempit.
  2. Badge `Individu` diulang di setiap baris, padahal di bagian atas tabel **sudah ada Tab Navigasi Kategori**: `[Semua Kategori]` `[Individu]` `[Kelompok]`.
  3. Dalam alur pendaftaran instansi pemerintah, status pendaftar secara *default* (mayoritas 85%+) adalah **Individu**. Menampilkan badge kotak tebal untuk setiap individu menciptakan *visual fatigue*.
  4. Yang merupakan **pengecualian administratif** yang perlu diwaspadai verifikator adalah pendaftaran **Kelompok** (karena memiliki banyak anggota, ketua tim, dan berkas kolektif).

---

## 3. Evaluasi Opsi Pendekatan Arsitektur UI

### Opsi 1: Standar Kedinasan Bersih (Clean Administrative Standard) — ⭐ SANGAT DIREKOMENDASIKAN
- **Desain Kolom 1**:
  - **Baris 1**: Nomor Registrasi ditampilkan dengan tipografi monospace tajam dan elegan tanpa kotak border tebal yang kaku (misal: `#REG-00006` dengan soft pill background tipis).
  - **Baris 2**: Tanggal masuk berkas dengan format formal: `06 Nov 2026`.
  - **Kategori**:
    - **Individu**: Tidak perlu badge kotak tebal. Cukup teks netral abu-abu halus (misal `Individu`) atau dihilangkan badge-nya karena sudah disaring oleh Tab Kategori di atas.
    - **Kelompok**: Diberi penanda khusus beraksen ungu lembut (`👥 Kelompok (3 Org)`), atau diposisikan mendampingi nama tim di Kolom Calon Peserta.
- **Kelebihan**:
  - Kolom pertama murni menjadi identitas arsip persuratan kedinasan (Nomor Agenda & Tanggal Masuk).
  - Mengurangi beban visual hingga 50%, tabel terlihat jauh lebih lega dan elegan.
  - Tetap mempertahankan kejelasan status kelompok sebagai pengecualian penting.

### Opsi 2: Compact Inline Metadata (2 Baris Ringkas)
- **Desain Kolom 1**:
  - **Baris 1**: `#REG-00006`
  - **Baris 2**: `06 Nov 2026 • Individu` (menggunakan titik pemisah/bullet sebagai teks biasa tanpa kotak).
  - Jika kelompok: `06 Nov 2026 • ` badge mini ungu `Kelompok (3)`.
- **Kelebihan**:
  - Seluruh informasi tetap ada tanpa menghilangkan kata "Individu".
  - Menghemat tinggi baris secara vertikal karena badge kotak baris ketiga dihilangkan.
- **Kekurangan**:
  - Teks baris kedua menjadi lebih panjang menyamping.

### Opsi 3: Relokasi Kategori ke Kolom Calon Peserta
- **Desain**:
  - Kolom 1 murni bernama `NO. REGISTRASI & TANGGAL` (hanya nomor dan tanggal pengajuan).
  - Kolom 2 (`CALON PESERTA`) menampilkan badge `Individu` atau `Kelompok` tepat di bawah nama peserta / email.
- **Kelebihan**:
  - Pemisahan tanggung jawab (*separation of concerns*): Kolom 1 urusan sistem pendaftaran, Kolom 2 urusan profil dan status keanggotaan pendaftar.
- **Kekurangan**:
  - Kolom 2 menjadi sedikit lebih tinggi jika pendaftar memiliki data NIM dan nama institusi yang panjang.

---

## 4. Rincian Rekomendasi Opsi Terpilih (Opsi 1)

Visual mockup yang diusulkan untuk Kolom **No. Registrasi**:

```
┌──────────────────────────────────────────────┐
│ NO. REGISTRASI                               │
├──────────────────────────────────────────────┤
│ #REG-00006                                   │ <- Monospace clean font (soft blue text)
│ 06 Nov 2026                                  │ <- Muted gray date
│                                              │ <- (Jika individu: bersih tanpa badge kotak)
├──────────────────────────────────────────────┤
│ #REG-00005                                   │
│ 04 Nov 2026                                  │
│ 👥 Kelompok (3 Org)                          │ <- (Hanya jika kelompok: badge ungu kontras)
└──────────────────────────────────────────────┘
```

Dan untuk **Bilah Meta Atas**:
```
┌────────────────────────────────────────────────────────────────────────┐
│ (Semua Status 4) (Diajukan 1) (Diterima 2) (Ditolak 0)     [Reset x]   │
└────────────────────────────────────────────────────────────────────────┘
```
*(Teks "Menampilkan 1–4 dari 4 berkas" dihapus dari atas; informasi jumlah tetap tersedia di bilah bawah tabel).*

---

## 5. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :---: | :--- |
| `resources/views/admin/pendaftaran/index.blade.php` | Modify | 1. Hapus elemen hitungan berkas pada `.adm-table-meta-bar`.<br>2. Restrukturisasi sel `No. Registrasi` agar hanya merender Nomor Registrasi + Tanggal secara bersih, dengan badge khusus hanya jika tipe pendaftaran adalah `kelompok`. |

---

## 6. Langkah Implementasi Terstruktur

- [ ] **Langkah 1**: Edit `resources/views/admin/pendaftaran/index.blade.php`:
  - Hapus blok container teks `Menampilkan ... berkas` dari `.adm-table-meta-bar`.
  - Pertahankan link `Reset Filter` di sisi kanan jika `$hasActiveFilters` bernilai `true`.
- [ ] **Langkah 2**: Refaktor sel `<td>` Kolom No. Registrasi:
  - Format nomor pendaftaran: `<span class="adm-reg-number">{{ $item->nomor_pendaftaran }}</span>`
  - Format tanggal pengajuan: `<div class="adm-reg-date">{{ $item->diajukan_at ? ... }}</div>`
  - Kondisional kategori: Hanya render badge jika `$item->tipe === 'kelompok' || $item->kelompokMagang`. Untuk individu, tidak merender badge kotak repetitif.
- [ ] **Langkah 3**: Penyesuaian CSS:
  - Pastikan styling `.adm-reg-number` dan `.adm-reg-date` memiliki kontras warna yang nyaman untuk tema *light* dan *dark mode*.
- [ ] **Langkah 4**: Quality Gate Testing:
  - Jalankan pengujian feature test: `php artisan test --filter "AdminVerifikasiKategoriDanUploadSuratTest|AdminChildPagesVisualTest|AdminTablePaginationStandardTest"`.
  - Jalankan linter Pint: `vendor\bin\pint.bat --test`.
  - Jalankan Vite build: `npm run build`.

---

## 7. Strategi Testing

1. **Feature Tests**:
   - Memastikan seluruh assertion di `AdminVerifikasiKategoriDanUploadSuratTest` tetap lulus 100% (pendaftaran individu dan kelompok tetap dapat disaring dengan tepat).
   - Memastikan `AdminTablePaginationStandardTest` tetap lulus (informasi paginasi di footer bawah tetap valid).
2. **Visual Inspection**:
   - Memastikan tampilan bilah meta atas tabel tidak lagi bertumpuk teks hitungan.
   - Memastikan baris tabel menjadi ramping, bersih, dan serasi dengan gaya kedinasan Dinas Kominfo.

---

## 8. Kriteria Selesai (Definition of Done)

- [ ] Teks `Menampilkan 1–4 dari 4 berkas` di bilah meta atas telah dihapus bersih.
- [ ] Kolom No. Registrasi tidak lagi menampilkan badge berkotak ganda untuk pendaftar individu.
- [ ] Badge kelompok tetap tampil jelas sebagai pembeda pendaftaran tim.
- [ ] Seluruh pengujian otomatis (PHPUnit) lulus 100%.
- [ ] Laravel Pint dan Vite build bebas error.

