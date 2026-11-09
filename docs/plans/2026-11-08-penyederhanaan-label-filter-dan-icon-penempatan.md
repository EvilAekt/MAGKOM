# Rencana Kerja: Penyederhanaan Label Filter, Header Tabel, Ikon Dinas, dan Pencarian Nama

**Dokumen Rencana**: `docs/plans/2026-11-08-penyederhanaan-label-filter-dan-icon-penempatan.md`  
**Tanggal**: 8 November 2026  
**Status**: Draf Perencanaan (Menunggu Persetujuan Pengguna)  
**Target Modul**: `resources/views/admin/penempatan/index.blade.php`  

---

## 1. Ringkasan Kebutuhan Pengguna (5 Poin)

Berdasarkan 5 tangkapan layar dan instruksi yang diberikan:
1. **SS ke-1 (Label Filter)**: Mengubah `Jurusan / Bidang Keahlian` menjadi **`Jurusan`** (placeholder: `Semua Jurusan`).
2. **SS ke-2 (Header Tabel)**: Mengubah `JURUSAN & ASAL INSTITUSI` menjadi **`JURUSAN`** saja, dengan tetap menampilkan universitas/sekolah di baris bawah sel tabel.
3. **SS ke-3 (Badge Counter)**: Menghapus badge pill `1 Menunggu Penempatan` pada header card antrean karena redundant dengan tab navigasi di atasnya.
4. **SS ke-4 (Ikon Antrean)**: Mengganti ikon jam bundar generik dengan ikon resmi dan representatif untuk **peserta magang di instansi dinas pemerintahan (Dinas Kominfo)**.
5. **SS ke-5 (Pencarian Nama)**: Mengubah label dan placeholder input pencarian menjadi lebih simpel dan *to the point*: **`Cari Nama`** dengan placeholder **`Cari nama...`**.

---

## 2. Analisis & Spesifikasi Teknis

### 2.1 Poin 1: Label & Placeholder Filter Jurusan (SS ke-1)
- **Label**: `<label>Jurusan</label>` (sebelumnya `Jurusan / Bidang Keahlian`).
- **Placeholder**: `<option value="">Semua Jurusan</option>` (sebelumnya `Semua Jurusan / Keahlian`).
- *Dampak*: Jauh lebih ringkas, natural, dan konsisten dengan database (`jurusans`) serta modul Kuota.

### 2.2 Poin 2: Header Kolom Tabel (SS ke-2)
- **Header Kolom**: `<th>Jurusan</th>` (sebelumnya `<th>Jurusan &amp; Asal Institusi</th>`).
- **Isi Sel (`<td>`)**: Tetap dipertahankan struktur 2 barisnya:
  ```html
  <td>
      <div style="font-weight: 600; color: #1E293B;">
          {{ $antrean->kuota?->jurusan?->nama_jurusan ?? '-' }}
      </div>
      <div style="font-size: 0.75rem; color: #64748B;">
          {{ $antrean->user?->profil?->institusi ?? '-' }}
      </div>
  </td>
  ```
- *Dampak*: Header tabel menjadi bersih dan proporsional tanpa teks yang panjang atau simbol `& /`.

### 2.3 Poin 3: Hapus Badge Pill Menunggu Penempatan (SS ke-3)
- **Elemen yang Dihapus**:
  ```html
  <span style="padding: 0.35rem 0.85rem; border-radius: 9999px; font-size: 0.8125rem; font-weight: 700; background: #EFF6FF; color: #1D4ED8; border: 1px solid #DBEAFE;">
      {{ $antreanBelumDitempatkan->count() }} Menunggu Penempatan
  </span>
  ```
- *Dampak*: Menghilangkan redundansi visual (karena counter antrean sudah terpampang jelas di tab pill `Belum Ditempatkan`). Container kartu header menjadi lebih lega dan elegan.

### 2.4 Poin 4: Pemilihan Ikon Representatif Magang di Dinas (SS ke-4)
Ikon saat ini adalah lingkaran jam (`clock`) yang terlalu generik.
Untuk merepresentasikan **Peserta Magang di Dinas Pemerintahan**, diajukan beberapa alternatif ikon SVG profesional:

#### Opsi 4.A (Rekomendasi Utama: ID Card / Badge Tanda Pengenal Magang Dinas)
Di instansi dinas pemerintah, identitas resmi peserta magang ditandai dengan **ID Card / Tanda Pengenal Magang (Lanyard)** yang dikalungkan saat penempatan tugas dinas:
```html
<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
    <rect width="18" height="14" x="3" y="7" rx="2"></rect>
    <path d="M9 5h6"></path>
    <path d="M12 2v3"></path>
    <circle cx="12" cy="13" r="2.5"></circle>
    <path d="M8 18h8"></path>
</svg>
```

#### Opsi 4.B (Alternatif: User Assignment / Penetapan Peserta Magang)
Siluet peserta magang dengan tanda penugasan/penetapan dinas:
```html
<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
    <circle cx="9" cy="7" r="4"></circle>
    <polyline points="16 11 18 13 22 9"></polyline>
</svg>
```

#### Opsi 4.C (Alternatif: Topi Akademik Pelajar/Mahasiswa & Dinas)
Simbol peserta didik (SMK/Mahasiswa) yang menjalani program magang:
```html
<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
    <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
    <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
</svg>
```

> **Rekomendasi**: Menggunakan **Opsi 4.A (ID Card / Tanda Pengenal Magang)** atau **Opsi 4.B (Penetapan Peserta Magang)** yang sangat identik dengan proses penempatan di kantor dinas.

---

### 2.5 Poin 5: Penyederhanaan Input Pencarian (SS ke-5)
- **Label Input**: Ubah dari `Pencarian Peserta` / `Pencarian Antrean` menjadi **`Cari Nama`**.
- **Placeholder**: Ubah dari `Cari nama peserta, email, kampus...` menjadi **`Cari nama...`**.
- Diterapkan pada kedua tab (*Sudah Ditempatkan* dan *Belum Ditempatkan*) agar konsisten dan tidak bertele-tele.

---

## 3. Rencana Perubahan Berkas

| Berkas | Aksi | Ringkasan Perubahan |
|---|:---:|---|
| `resources/views/admin/penempatan/index.blade.php` | Modify | 1. Ubah label filter jadi `Jurusan` & placeholder `Semua Jurusan`.<br>2. Ubah header kolom tabel jadi `Jurusan`.<br>3. Hapus badge counter `1 Menunggu Penempatan`.<br>4. Ganti ikon lingkaran jam dengan ikon ID Card / Penugasan Magang Dinas.<br>5. Ubah label jadi `Cari Nama` & placeholder jadi `Cari nama...`. |
| `tests/Feature/AdminChildPagesVisualTest.php` | Modify | Sesuaikan assertion string visual filter (`Cari Nama`, `Jurusan`, `Semua Jurusan`) agar test tetap 100% hijau. |

---

## 4. Langkah Implementasi Terstruktur

- [ ] **Langkah 1**: Perbarui input pencarian (label `Cari Nama` dan placeholder `Cari nama...`) pada tab *Sudah Ditempatkan* dan tab *Belum Ditempatkan*.
- [ ] **Langkah 2**: Perbarui dropdown filter jurusan (label `Jurusan`, option default `Semua Jurusan`).
- [ ] **Langkah 3**: Ganti ikon SVG pada header kartu antrean dengan ikon **ID Card / Tanda Pengenal Magang Dinas** yang representatif.
- [ ] **Langkah 4**: Hapus elemen `<span>` badge counter `Menunggu Penempatan` dari header kartu antrean.
- [ ] **Langkah 5**: Ubah teks `<th>` kolom ke-2 tabel antrean dari `Jurusan & Asal Institusi` menjadi `Jurusan` (isi sel tetap menampilkan jurusan dan universitas).
- [ ] **Langkah 6**: Perbarui visual test di `tests/Feature/AdminChildPagesVisualTest.php` agar mencerminkan string teks baru.
- [ ] **Langkah 7**: Jalankan quality gate:
  - `php artisan test --filter AdminChildPagesVisualTest`
  - `php artisan test --filter AdminPenempatanDuaTahapTest`
  - `vendor\bin\pint.bat --test`
  - `npm run build`

