# Rencana Kerja: Penataan Kolom No. Registrasi, Standarisasi Nama Kolom Status Kedinasan, dan Personalisasi Label NIM/NISN

Dokumen perencanaan arsitektur dan penataan antarmuka ini disusun untuk memenuhi 4 poin kebutuhan penataan data pada meja verifikasi berkas pendaftaran admin (`admin.pendaftaran.index`).

---

## 1. Tujuan
1. **Memindahkan Tanggal Pengajuan**: Memindahkan tanggal pendaftaran dari kolom *No. Registrasi* ke kolom status, sehingga kolom *No. Registrasi* menjadi sangat ringkas (hanya nomor ID berkas & badge kelompok jika ada).
2. **Standarisasi Penamaan Kolom Kedinasan**: Mengganti penamaan *"Status & Usia Berkas"* yang kurang lazim di birokrasi pemerintahan menjadi istilah formal tata naskah dinas yang tepat dan representatif.
3. **Personalisasi Identitas Peserta (NIM vs NISN)**: Menampilkan label spesifik `NIM:` untuk Mahasiswa dan `NISN:` untuk Siswa SMK secara otomatis berdasarkan jenjang/kategori jurusan pendaftar, menggantikan label gabungan `NIM/NISN:`.
4. **Penyelarasan & Konsistensi UI**: Menjaga kejelasan hierarki, proporsi ruang tabel, dan kompatibilitas tema *light/dark mode*.

---

## 2. Analisis & Usulan Solusi Senior Developer

### A. Poin 1: Pemindahan Tanggal ke Kolom Status (Logika Administratif)
- **Kondisi Saat Ini**:
  - Kolom 1 (*No. Registrasi*): Menampilkan `#REG-00006` dan `📅 06 Nov 2026`.
  - Kolom 5 (*Status & Usia Berkas*): Menampilkan badge `Diajukan` dan usia `Hari ini` / `X hari lalu`.
- **Evaluasi & Rekomendasi**:
  - Usulan pengguna sangat tepat dan logis secara alur persuratan dinas.
  - Tanggal pendaftaran (`diajukan_at`) adalah atribut waktu siklus hidup (*lifecycle timestamp*) berkas, yang secara alami berpasangan dengan status permohonan dan durasi tunggu (SLA).
  - Ketika tanggal dipindah ke kolom status:
    - **Kolom 1 (*No. Registrasi*)**: Menjadi sangat bersih dan elegan. Hanya menampilkan nomor registrasi monospace `#REG-00006`, plus badge `👥 Kelompok (X Org)` jika merupakan pendaftaran tim.
    - **Kolom Status**: Menampilkan status berkas (`[Dot] Diajukan`) dan di bawahnya tanggal masuk serta usia tunggu: `06 Nov 2026 • Hari ini` (atau `06 Nov 2026 • 2 hari lalu`).

### B. Poin 2: Standarisasi Penamaan Kolom Status untuk Kedinasan
Istilah *"Status & Usia Berkas"* terdengar kaku dan tidak umum dalam administrasi instansi pemerintah. Berikut perbandingan opsi nama resmi:

| Opsi | Penamaan Kolom | Kesesuaian Kedinasan | Alasan Analisis |
|:---:|:---|:---:|:---|
| **A** | **`STATUS PERMOHONAN`** ⭐ *(Rekomendasi Utama)* | **Sangat Tinggi (99%)** | Dalam tata naskah dinas pemerintah (PermenPAN-RB / Kemendagri / Perbup), berkas layanan publik resmi disebut **"Permohonan"** (selaras dengan judul halaman: *Daftar Permohonan Magang Masuk*). |
| **B** | **`STATUS & TANGGAL MASUK`** | Tinggi (90%) | Mengadopsi nomenklatur persuratan resmi dinas (*Surat Masuk / Buku Agenda Masuk*). Sangat akrab bagi staf TU/Sekretariat. |
| **C** | **`STATUS VERIFIKASI`** | Tinggi (88%) | Menggambarkan proses kerja teknis verifikator pendaftaran. |

> **Rekomendasi Senior Developer**: Gunakan **`STATUS PERMOHONAN`** (atau **`STATUS & TANGGAL MASUK`**). Keduanya terdengar formal, berwibawa, dan tepat guna bagi portal pemerintahan.

### C. Poin 3: Personalisasi Dinamis Label NIM vs NISN
- **Kondisi Saat Ini**: Menampilkan teks statis `NIM/NISN: 24.02.7689`.
- **Akar Masalah**: Pendaftar universitas merasa aneh melihat label NISN, dan siswa SMK merasa tidak relevan dengan label NIM.
- **Logika Deteksi Sistem**:
  Model data `PesertaProfil` memiliki field `jenjang` (enum: `smk`, `d3`, `s1`) dan relasi `kuota.jurusan` memiliki `kategori` (`smk` atau `mahasiswa`).
  ```php
  $isSmk = ($item->user?->profil?->jenjang?->value ?? (string)($item->user?->profil?->jenjang ?? '')) === 'smk'
      || ($item->kuota?->jurusan?->kategori ?? '') === 'smk';
  $idLabel = $isSmk ? 'NISN' : 'NIM';
  ```
- **Hasil Tampilan**:
  - Pendaftar SMK: `NISN: 0054321987`
  - Pendaftar Mahasiswa (D3/S1): `NIM: 24.02.7689`

---

## 3. Visual Mockup Perbandingan

### Sebelum (Current):
```
[NO. REGISTRASI]          [CALON PESERTA]               [STATUS & USIA BERKAS]
#REG-00006                Galang Adhitya (GA)           (• Diajukan)
📅 06 Nov 2026            galangcs@gmail.com            Hari ini
                          NIM/NISN: 24.02.7689
```

### Sesudah (Proposed):
```
[NO. REGISTRASI]          [CALON PESERTA]               [STATUS PERMOHONAN]
#REG-00006                Galang Adhitya (GA)           (• Diajukan)
                          galangcs@gmail.com            06 Nov 2026 • Hari ini
                          NIM: 24.02.7689               (atau NISN: 1234567890 jika SMK)
```

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :---: | :--- |
| `resources/views/admin/pendaftaran/index.blade.php` | Modify | 1. Kolom 1 `No. Registrasi`: Hapus tanggal pengajuan sehingga murni ID registrasi (+ badge kelompok jika ada).<br>2. Kolom 2 `Calon Peserta`: Ganti label `NIM/NISN:` menjadi dinamis `NIM:` atau `NISN:` sesuai jenjang/kategori.<br>3. Kolom 5: Ganti nama header menjadi `Status Permohonan` (atau `Status & Tanggal Masuk`). Gabungkan tanggal pengajuan formal dengan durasi usia berkas (`06 Nov 2026 • Hari ini`). |

---

## 5. Rencana Langkah Implementasi

- [ ] **Langkah 1**: Edit `resources/views/admin/pendaftaran/index.blade.php`:
  - Ubah header kolom ke-5: `<th>Status Permohonan</th>` (atau `Status & Tanggal Masuk`).
- [ ] **Langkah 2**: Refaktor Kolom 1 (`<td>` No. Registrasi):
  - Hapus `.adm-reg-date` dari sel ini.
  - Pertahankan nomor pendaftaran `#REG-xxxxx` dan kondisional badge kelompok.
- [ ] **Langkah 3**: Refaktor Kolom 2 (`<td>` Calon Peserta):
  - Tambahkan penentuan label identitas siswa/mahasiswa:
    ```blade
    @php
        $isSmk = ($item->user?->profil?->jenjang?->value ?? (string)($item->user?->profil?->jenjang ?? '')) === 'smk'
            || ($item->kuota?->jurusan?->kategori ?? '') === 'smk';
        $nimNisnLabel = $isSmk ? 'NISN' : 'NIM';
    @endphp
    @if($item->user?->profil?->nim_nisn)
        <div style="font-size: 0.725rem; color: #94A3B8;">
            {{ $nimNisnLabel }}: {{ $item->user->profil->nim_nisn }}
        </div>
    @endif
    ```
- [ ] **Langkah 4**: Refaktor Kolom 5 (`<td>` Status Permohonan):
  - Tampilkan tanggal pengajuan berdampingan dengan SLA/usia berkas:
    - Status Diajukan: `{{ $tglMasuk }} • {{ $slaText }}`
    - Status Diterima: `Diajukan {{ $tglMasuk }} • Diterima {{ $tglVerif }}`
    - Status Ditolak: `Diajukan {{ $tglMasuk }} • Ditolak {{ $tglVerif }}`
- [ ] **Langkah 5**: Quality Gate & Pengujian:
  - Jalankan test suite: `AdminVerifikasiKategoriDanUploadSuratTest`, `AdminChildPagesVisualTest`, `AdminTablePaginationStandardTest`.
  - Jalankan `vendor\bin\pint.bat --test`.
  - Jalankan `npm run build`.

---

## 6. Kriteria Selesai (Definition of Done)

- [ ] Kolom No. Registrasi hanya memuat nomor registrasi dan badge tim jika ada.
- [ ] Tanggal pengajuan tampil terpadu di kolom status bersama indikator usia berkas.
- [ ] Header kolom status menggunakan istilah kedinasan yang resmi (`Status Permohonan`).
- [ ] Label identitas peserta secara cerdas menampilkan `NIM:` untuk Mahasiswa dan `NISN:` untuk Siswa SMK.
- [ ] Seluruh pengujian fitur dan linter lulus 100%.

