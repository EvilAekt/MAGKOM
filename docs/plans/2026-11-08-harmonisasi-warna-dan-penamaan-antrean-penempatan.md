# Rencana Kerja: Harmonisasi Warna & Penyempurnaan Nomenklatur Tab Belum Ditempatkan

**Dokumen Rencana**: `docs/plans/2026-11-08-harmonisasi-warna-dan-penamaan-antrean-penempatan.md`  
**Tanggal**: 8 November 2026  
**Status**: Draf Perencanaan (Menunggu Persetujuan User)  
**Target Modul**: `resources/views/admin/penempatan/index.blade.php` (Tab `belum`)  

---

## 1. Latar Belakang & Tujuan

Berdasarkan tinjauan antarmuka pada tangkapan layar yang diunggah pengguna:
1. **Warna Belum Selaras (SS ke-1)**: Tab *Belum Ditempatkan* saat ini didominasi warna oranye/kuning/amber mustard (tombol filter oranye `#D97706`, header kartu kuning `#FFFBEB`, header tabel mustard `#FEF9C3`, avatar kuning). Tampilan ini tidak selaras (*unaligned*) dengan tab *Sudah Ditempatkan* yang mengusung tema signature Sleman Navy Blue (`#01347C` / `#0641CC`) dan slate netral yang bersih.
2. **Penamaan Label Filter Kaku (SS ke-2)**: Label `Jurusan / Program Studi` dirasa kaku dan memiliki tanda garis miring yang canggung untuk merepresentasikan peserta yang berasal dari kalangan **pelajar SMK** (Jurusan / Konsentrasi Keahlian) maupun **mahasiswa Perguruan Tinggi** (Program Studi / Jurusan).
3. **Penamaan Header Kolom Tabel Tidak Baku (SS ke-3)**: Label header `JURUSAN & KAMPUS / SEKOLAH` menggunakan penggabungan tanda baca `&` dan `/` sekaligus, serta kata "Kampus / Sekolah" terasa kurang formal dibandingkan standar portal pemerintahan.

Tujuan utama rencana ini adalah:
- Menyelaraskan seluruh palet warna tab *Belum Ditempatkan* agar konsisten, bersih, dan profesional setara dengan tab *Sudah Ditempatkan*.
- Menyempurnakan penamaan filter dan kolom tabel agar inklusif, formal, dan nyaman dibaca untuk profil pelajar maupun mahasiswa.

---

## 2. Analisis & Evaluasi Opsi Nomenklatur

### 2.1 Penamaan Label Filter (SS ke-2)
Dalam konteks pendidikan vokasi SMK dan pendidikan tinggi (Kemendikbudristek):
- **Opsi A (Rekomendasi Utama)**: **`Jurusan / Bidang Keahlian`** (Placeholder: `Semua Jurusan / Keahlian`)
  - *Kelebihan*: Kata "Jurusan" sudah sangat umum dipahami mahasiswa dan siswa; frasa "Bidang Keahlian" adalah nomenklatur resmi kurikulum SMK (misal: Bidang Keahlian Teknologi Informasi) sekaligus merefleksikan rumpun keahlian mahasiswa. Terasa alami dan profesional.
- **Opsi B**: **`Bidang Studi`** (Placeholder: `Semua Bidang Studi`)
  - *Kelebihan*: Sangat ringkas, bersih, tanpa tanda garis miring, universal mencakup SMK maupun kampus.
  - *Kekurangan*: Sedikit lebih formal/akademis.
- **Opsi C**: **`Program Studi / Kejuruan`** (Placeholder: `Semua Program / Kejuruan`)
  - *Kelebihan*: Eksplisit membedakan Program Studi (kampus) dan Kejuruan (SMK).
  - *Kekurangan*: Masih menggunakan garis miring.

> **Rekomendasi**: Menggunakan **`Jurusan / Bidang Keahlian`** (atau **`Bidang Studi`** jika ingin 100% minimalis).

---

### 2.2 Penamaan Header Kolom Tabel (SS ke-3)
Kolom ini menampilkan 2 tingkat data:
- Baris 1: Nama jurusan/kompetensi magang (misal: *Teknik Informatika*, *Rekayasa Perangkat Lunak*).
- Baris 2: Nama sekolah atau universitas (misal: *Universitas Gadjah Mada*, *SMK N 2 Depok Sleman*).

Opsi Header Kolom:
- **Opsi A (Rekomendasi Utama)**: **`Jurusan & Asal Institusi`**
  - *Kelebihan*: Mengganti kata informal *"Kampus / Sekolah"* menjadi kata baku *"Institusi"*. Kata "Institusi" adalah istilah payung resmi di Indonesia yang mencakup perguruan tinggi maupun sekolah menengah. Urutannya mencerminkan data: Jurusan di atas, Asal Institusi di bawah.
- **Opsi B**: **`Institusi & Bidang Studi`**
  - *Kelebihan*: Konsisten dengan pola tabel antrean verifikasi pendaftaran di modul Dashboard MagKom (`Institusi & Jurusan`).
- **Opsi C**: **`Pendidikan & Bidang Keahlian`**
  - *Kelebihan*: Terasa komprehensif, namun urutannya terbalik dari data baris tabel.

> **Rekomendasi**: Menggunakan **`Jurusan & Asal Institusi`** (atau **`Institusi & Bidang Studi`**).

---

### 2.3 Penyelarasan Warna Sesuai Tab "Sudah Ditempatkan" (Point 1)

| Komponen Antarmuka | Kondisi Saat Ini (Kuning/Oranye) | Kondisi Baru (Selaras Sleman Navy/Slate) |
|---|---|---|
| **Tombol Terapkan Filter** | Background `#D97706` (oranye) | Background `#0641CC` (Sleman Royal Blue), hover `#003566`, shadow lembut biru — **sama persis dengan tombol tab Sudah Ditempatkan**. |
| **Top Border Card Tabel** | `border-top: 3.5px solid #F59E0B` (kuning menyala) | `border-top: 3px solid #01347C` (signature Sleman Navy) atau border netral standar `var(--neutral-200, #E2E8F0)`. |
| **Header Card Antrean** | Background `#FFFBEB` (kuning pucat), garis bawah `#FDE68A` | Background `#FFFFFF` (putih bersih), garis bawah `#E2E8F0`. |
| **Ikon Header Card** | Background `#FEF3C7`, ikon oranye `#D97706` | Background `#EFF6FF`, ikon biru Sleman `#0641CC`. |
| **Teks Judul & Subtitle Card** | Judul cokelat `#92400E`, Subtitle `#B45309` | Judul teks utama `#0F172A` (tegas), Subtitle `#64748B` (tenang & netral). |
| **Badge Counter Antrean** | Background `#FEF3C7`, teks `#B45309`, border `#FCD34D` | Pill badge elegan: background `#EFF6FF`, teks `#1D4ED8`, border `#DBEAFE`. |
| **Header Tabel (`<thead>`)** | Background `#FEF9C3` (kuning pekat), teks cokelat `#854D0E` | Background `#F8FAFC` (clean slate), border `#E2E8F0`, teks `#475569` font-weight 700 — **sama persis dengan tabel Sudah Ditempatkan**. |
| **Inisial Avatar Peserta** | Background `#FDE68A`, teks `#92400E` | Gradient Sleman Navy `linear-gradient(135deg, #01347C 0%, #0641CC 100%)` teks putih `#FFFFFF` — **sama persis dengan avatar Sudah Ditempatkan**. |

---

## 3. Rencana Perubahan Berkas

| Berkas | Aksi | Deskripsi Perubahan |
|---|:---:|---|
| `resources/views/admin/penempatan/index.blade.php` | Modify | 1. Update styling tombol filter ke `#0641CC`.<br>2. Harmonisasi warna card container, thead tabel, badge, dan avatar dari kuning/mustard ke Sleman Navy/Slate.<br>3. Perbarui label filter dropdown menjadi `Jurusan / Bidang Keahlian` (atau `Bidang Studi`).<br>4. Perbarui header kolom tabel menjadi `Jurusan & Asal Institusi`. |
| `tests/Feature/AdminChildPagesVisualTest.php` | Modify | Sesuaikan assertion string visual filter untuk memastikan pengujian tetap 100% hijau. |

---

## 4. Langkah Implementasi Terstruktur

- [ ] **Langkah 1**: Perbarui tombol aksi filter pada tab `belum` di `resources/views/admin/penempatan/index.blade.php` dari oranye `#D97706` menjadi Sleman Royal Blue `#0641CC`.
- [ ] **Langkah 2**: Perbarui label filter dropdown dan placeholder opsi menjadi nomenklatur yang disepakati (`Jurusan / Bidang Keahlian` / `Semua Jurusan / Keahlian`).
- [ ] **Langkah 3**: Harmonisasi warna container tabel antrean:
  - Hilangkan background kuning muda `#FFFBEB` dan border kuning `#FDE68A`.
  - Jadikan header card putih bersih dengan judul `#0F172A` dan subtitle `#64748B`.
  - Ubah badge counter antrean menjadi soft blue pill (`#EFF6FF` / `#1D4ED8`).
- [ ] **Langkah 4**: Harmonisasi thead tabel dan avatar inisial:
  - Ubah header tabel `<tr>` dari kuning `#FEF9C3` menjadi `#F8FAFC` dengan warna teks netral `#475569`.
  - Ubah avatar inisial menjadi gradient Sleman Navy `#01347C` -> `#0641CC`.
- [ ] **Langkah 5**: Perbarui label header kolom tabel (SS ke-3) menjadi `Jurusan & Asal Institusi`.
- [ ] **Langkah 6**: Perbarui test assertion pada `tests/Feature/AdminChildPagesVisualTest.php`.
- [ ] **Langkah 7**: Jalankan seluruh quality gates:
  - `artisan test --filter AdminChildPagesVisualTest`
  - `artisan test --filter AdminPenempatanDuaTahapTest`
  - `vendor\bin\pint.bat --test`
  - `npm run build`

---

## 5. Strategi Pengujian & Mitigasi Risiko

1. **Aksesibilitas (WCAG 2.1 AA)**:
   - Warna teks judul `#0F172A` di atas putih memiliki rasio kontras 16.5:1 (melebihi standar minimum 4.5:1).
   - Warna header tabel `#475569` di atas `#F8FAFC` memiliki rasio kontras 7.2:1 (lulus AAA).
   - Menghilangkan warna kuning/cokelat yang sebelumnya memiliki kontras rendah pada beberapa monitor.
2. **Zero Functional Breaking**:
   - Query parameter form (`tab=belum`, `q`, `bulan_magang_id`, `jurusan_id`, `bidang_id`) tetap 100% utuh tanpa modifikasi name attribute.
3. **Pemberitahuan Read-Only**:
   - Tidak ada perubahan kode yang dieksekusi sebelum rencana ini disetujui pengguna.

