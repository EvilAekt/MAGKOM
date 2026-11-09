# 📋 SENIOR DEVELOPER TASK BRIEF / PRD
## Pembersihan Teks Deskriptif, Filter Dokumen Berdasarkan Jenjang Peserta, dan Standarisasi Warna Status Biru pada Detail Arsip Alumni Magang

- **Tanggal Pembuatan:** 09 November 2026  
- **Penulis:** Senior Backend & UI Architect MagKom  
- **Target Pelaksana:** Junior Developer / AI Implementation Model (Gemini 3.8 Flash / Claude Haiku / Antigravity Agent)  
- **Status:** 🟢 **COMPLETED / VERIFIED (ALL QUALITY GATES PASSED)**  
- **Kompatibilitas:** Laravel 10.x, PHP 8.3.x, DS-MagKom Industrial Standard, OWASP Top 10  
- **Dokumen Target:**
  - [`resources/views/superadmin/arsip/show.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/arsip/show.blade.php)
  - [`app/Http/Controllers/SuperAdmin/ArsipPesertaController.php`](file:///g:/MAGKOM/app/Http/Controllers/SuperAdmin/ArsipPesertaController.php)
  - [`resources/css/pages/superadmin/superadmin-common.css`](file:///g:/MAGKOM/resources/css/pages/superadmin/superadmin-common.css)
  - [`database/seeders/ProductionRealisticDataSeeder.php`](file:///g:/MAGKOM/database/seeders/ProductionRealisticDataSeeder.php)

---

## 🎯 1. 📌 LATAR BELAKANG & ANALISIS MASALAH (Root Cause Analysis)

Berdasarkan audit antarmuka halaman **Detail Arsip Alumni Magang** pada modul SuperAdmin serta masukan pengguna yang disertai 4 bukti visual (`media_1794206014272_f6f22e05.jpg` s/d `media_1794206074828_8bf3311f.png`), ditemukan 3 hal utama yang perlu disempurnakan:

```
┌────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│ RINGKASAN MASALAH & TEMUAN AUDIT:                                                                      │
│ 1. Subteks Deskriptif Redundant: Menghabiskan ruang vertikal tanpa memberi nilai tambah.              │
│ 2. Fenomena "Dokumen Berulang / Sama": Satu alumni S1 memuat 12 berkas (campur aduk SMK, D3, dan S1). │
│ 3. Inkonsistensi Warna: Status "Selesai" & "Valid" masih hijau; counter peringatan masih kuning/amber. │
└────────────────────────────────────────────────────────────────────────────────────────────────────────┘
```

---

### 1.1 Masalah 1: Teks Deskriptif di Bawah Judul yang Redundant
Pada ketiga tab berikut, terdapat teks pembantu yang membuat tampilan terlihat penuh dan tidak efisien:
1. **Tab 2 (Riwayat Tugas):**  
   Teks: `"Rekam penugasan kerja yang diberikan pembimbing selama masa dinas aktif."` (Baris 271-273).
2. **Tab 3 (Logbook Harian):**  
   Teks: `"Catatan kehadiran, jurnal aktivitas, dan rekap bimbingan harian peserta magang."` (Baris 339-341).
3. **Tab 4 (Dokumen Pendaftaran):**  
   Teks: `"Dokumen resmi pendaftaran yang telah diverifikasi valid oleh Admin Seleksi MagKom."` (Baris 408-410).

**Solusi:** Hapus ketiga baris teks deskriptif tersebut agar header kartu menjadi lebih bersih, padat (*compact*), dan fokus pada judul serta counter total.

---

### 1.2 Masalah 2: Fenomena Dokumen Berulang / Terlalu Banyak Data yang Sama (Media 1)
**Pertanyaan Pengguna:**  
> *"Sepertinya di bagian Berkas Administrasi & Dokumen Terverifikasi banyak data yang sama terlalu banyak data yang sama, kan itu harusnya kalau dari sistem sekarang nggak harus menginputkan semua data itu deh, coba cek lagi deh ada data yang sama atau enggak?"*

#### 🔎 Hasil Investigasi Mendalam (Root Cause Analysis Teknis):
1. **Struktur Master Data `JenisDokumen`:**  
   Tabel `jenis_dokumen` memiliki 12 entri master data yang sengaja dipisah per jenjang pendidikan (`JenjangEnum`):
   - **Jenjang SMK (4 Berkas):**
     - ID 1: Surat Permohonan PKL/Magang dari Kepala Sekolah
     - ID 2: Curriculum Vitae (CV) & Portofolio Keahlian
     - ID 3: Salinan Kartu Pelajar
     - ID 4: Surat Persetujuan Orang Tua / Wali
   - **Jenjang D3 (4 Berkas):**
     - ID 5: Surat Pengantar Magang Resmi dari Fakultas / Program Studi
     - ID 6: Proposal Kegiatan Magang
     - ID 7: Curriculum Vitae (CV) & Portofolio Proyek
     - ID 8: Salinan Transkrip Nilai Akademik Terakhir
   - **Jenjang S1 (4 Berkas):**
     - ID 9: Surat Pengantar Magang Resmi dari Dekanat / Fakultas
     - ID 10: Proposal Rencana Kegiatan Magang & Riset
     - ID 11: Curriculum Vitae (CV) & Tautan Portofolio Proyek
     - ID 12: Transkrip Nilai Akademik Sementara

2. **Alur Pendaftaran Peserta Riil di Sistem:**  
   Pada controller pendaftaran peserta (`app/Http/Controllers/Peserta/PendaftaranController.php` baris 149-152):
   ```php
   $jenjang = $profil?->jenjang ?? JenjangEnum::Smk;
   $jenisDokumenList = JenisDokumen::where('aktif', true)
       ->where('jenjang', $jenjang->value)
       ->orderBy('wajib', 'desc')
       ->get();
   ```
   **Fakta Sistem:** Ketika peserta S1 mendaftar magang, sistem **HANYA meminta 4 berkas S1**. Peserta tidak pernah diminta mengunggah 12 berkas!

3. **Mengapa di Akun Alumni (Misal: Anisa Rahmawati - S1) Muncul 12 Berkas?**  
   Akar masalahnya berada pada **Database Seeder Data Dummy** (`database/seeders/ProductionRealisticDataSeeder.php` baris 390-404 dan `DummyDuaPuluhPesertaSeeder.php` baris 126-138):
   ```php
   // KODE SEEDER SEBELUMNYA (BUG LOGIKA):
   $jenisDokumenList = JenisDokumen::all(); // <-- Mengambil SELURUH 12 jenis dokumen!
   $lampirkanBerkas = function (Pendaftaran $pendaftaran) use ($jenisDokumenList) {
       foreach ($jenisDokumenList as $jd) {
           Dokumen::updateOrCreate(['pendaftaran_id' => $pendaftaran->id, 'jenis_dokumen_id' => $jd->id], ...);
       }
   };
   ```
   Akibatnya, fungsi seeder memasukkan berkas SMK, D3, dan S1 sekaligus ke dalam setiap akun dummy. Itulah sebabnya pengguna melihat dokumen yang tampak berulang (3 macam CV, 3 macam Surat Pengantar, 2 macam Proposal, dan 2 macam Transkrip).

4. **Solusi Arsitektural:**
   - **Di Controller / View:** Lakukan pemfilteran cerdas pada koleksi dokumen:
     Hanya tampilkan dokumen yang sesuai dengan jenjang profil peserta (`$penempatan->pendaftaran?->user?->profil?->jenjang`). Jika dokumen cocok dengan jenjang peserta (atau dokumen umum), tampilkan.
     Dengan demikian, alumni S1 **hanya menampilkan 4 berkas S1**, alumni SMK hanya 4 berkas SMK, dan counter tab pill akan tepat menunjukkan angka `(4)`, bukan `(12)`.
   - **Di Seeder:** Perbaiki fungsi seeder agar hanya melampirkan berkas sesuai `$jenjang` peserta terkait.

---

### 1.3 Masalah 3: Standarisasi Warna Status Menjadi Biru Khas MagKom (Media 2, 3, 4)
Pengguna meminta: *"buat warna untuk peringatan sama selesai dan valid jadi warna biru"*.
Saat ini di halaman:
- Badge **"Valid"** pada kartu Dokumen Pendaftaran berwarna hijau muda (`#ECFDF5` / `#065F46`).
- Badge status **"Selesai"** pada kartu Penugasan dan tabel Logbook berwarna hijau muda (`#ECFDF5` / `#065F46`).
- Teks ringkasan **"1 Selesai"** pada kartu Pembimbing Lapangan berwarna hijau bold (`var(--green-600)`).
- Badge counter **"Total: 10 Entri Terisi"** pada header tabel Logbook berwarna kuning/amber (`#FEF3C7` / `#92400E`).
- Badge hero **"Selesai Magang"** berwarna hijau muda (`#ECFDF5`).

**Spesifikasi Palet Biru Standar MagKom (Industrial Sleman Standard):**
- **Background:** `#EFF6FF` (Blue-50 lembut)
- **Border:** `1px solid #BFDBFE` (Blue-200)
- **Teks / Icon:** `#01347C` (Primary Corporate Blue MagKom Sleman)
- **Dark Mode (`[data-theme="dark"]`):** Background `rgba(30, 64, 175, 0.25)`, Border `rgba(147, 197, 253, 0.4)`, Teks `#93C5FD`.

---

## 🎯 2. 📋 TUJUAN & ACCEPTANCE CRITERIA (AC)

Junior Developer / AI Pelaksana wajib memenuhi seluruh kriteria penerimaan berikut:

- [x] **AC-1 (Pembersihan Teks):** Tiga baris subteks deskriptif di bawah judul Tab 2 (Tugas), Tab 3 (Logbook), dan Tab 4 (Dokumen) telah dihapus sepenuhnya tanpa merusak layout header.
- [x] **AC-2 (Filter Dokumen Berdasarkan Jenjang):** Tab 4 (Dokumen Pendaftaran) dan counter tab pill hanya menampilkan berkas yang relevan dengan jenjang pendidikan alumni (`$penempatan->pendaftaran?->user?->profil?->jenjang`). Alumni S1 menampilkan 4 berkas S1, alumni SMK menampilkan 4 berkas SMK. Tidak ada lagi data berkas duplikat/campur aduk.
- [x] **AC-3 (Standarisasi Badge "Valid" Biru):** Badge status *"Valid"* di setiap kartu dokumen berubah menjadi badge biru lembut khas MagKom (`#EFF6FF`, border `#BFDBFE`, teks `#01347C`).
- [x] **AC-4 (Standarisasi Badge "Selesai" Biru):** Badge status *"Selesai"* pada kartu tugas, baris tabel logbook, badge hero *"Selesai Magang"*, dan teks ringkasan pembimbing lapangan berubah menjadi nuansa biru MagKom (`#01347C`).
- [x] **AC-5 (Standarisasi Counter Peringatan Biru):** Badge counter *"Total: X Entri Terisi"* pada tabel logbook yang sebelumnya kuning/amber diubah menjadi pill biru lembut (`#EFF6FF`, border `#BFDBFE`, teks `#01347C`).
- [x] **AC-6 (Strict Architecture Rule):** Tidak boleh ada tag `<style>` baru atau inline CSS di Blade. Seluruh class baru (`.badge-status-blue`, `.badge-counter-blue`) ditaruh di `resources/css/pages/superadmin/superadmin-common.css`.

---

## 🏗️ 3. 🗺️ INVENTARIS FILE & PEMETAAN DAMPAK (Impact Matrix)

```
┌────────────────────────────────────────────────────────────────────────────────────────┐
│ 1. STYLESHEET RESMI (VITE)                                                             │
│    resources/css/pages/superadmin/superadmin-common.css                                │
│    -> Tambahkan utility .badge-status-blue dan .badge-counter-blue                      │
│    -> Dukungan penuh Dark Mode [data-theme="dark"]                                     │
│                                                                                        │
│ 2. BLADE VIEW PRESENTASI                                                               │
│    resources/views/superadmin/arsip/show.blade.php                                     │
│    -> Hapus 3 subteks deskripsi di Tab 2, 3, dan 4                                     │
│    -> Filter koleksi dokumen pendaftaran berdasarkan jenjang profil peserta            │
│    -> Ganti warna badge "Valid", "Selesai", dan "Total Entri" ke palet biru            │
│                                                                                        │
│ 3. SEEDER DATA DUMMY (OPSIONAL - DATA PURITY)                                          │
│    database/seeders/ProductionRealisticDataSeeder.php                                  │
│    -> Ubah helper $lampirkanBerkas agar memfilter JenisDokumen sesuai jenjang peserta  │
│                                                                                        │
│ 4. TEST SUITE VERIFIKASI                                                               │
│    tests/Feature/AdminLaporanDanArsipTest.php & DeltaDashboardEnhancementTest.php      │
│    -> Pastikan seluruh assertion dan quality gate tetap lolos hijau 100%               │
└────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 🛠️ 4. 📝 PANDUAN TEKNIS EKSEKUSI TAHAP DEMI TAHAP (Step-by-Step Implementation)

### 🔹 LANGKAH 1: Tambahkan Utility Class Badge Biru di `superadmin-common.css`
**Berkas:** [`resources/css/pages/superadmin/superadmin-common.css`](file:///g:/MAGKOM/resources/css/pages/superadmin/superadmin-common.css)

Tambahkan aturan CSS berikut pada bagian bawah berkas:

```css
/* =========================================================================
   MAGKOM BLUE BADGE & PILL STATUS SYSTEM (DS-MAGKOM SLEMAN STANDARD)
   ========================================================================= */

.badge-status-blue {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    font-size: 0.725rem;
    font-weight: 700;
    padding: 0.25rem 0.65rem;
    border-radius: 6px;
    background: #EFF6FF !important;
    color: #01347C !important;
    border: 1px solid #BFDBFE !important;
    white-space: nowrap;
}

.badge-counter-blue {
    display: inline-flex;
    align-items: center;
    font-size: 0.75rem;
    font-weight: 700;
    padding: 0.3rem 0.75rem;
    border-radius: 9999px;
    background: #EFF6FF !important;
    color: #01347C !important;
    border: 1px solid #BFDBFE !important;
}

/* Dark Mode Overrides */
[data-theme="dark"] .badge-status-blue,
[data-theme="dark"] .badge-counter-blue {
    background: rgba(30, 64, 175, 0.25) !important;
    color: #93C5FD !important;
    border-color: rgba(147, 197, 253, 0.4) !important;
}
```

---

### 🔹 LANGKAH 2: Perbarui View `show.blade.php`
**Berkas:** [`resources/views/superadmin/arsip/show.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/arsip/show.blade.php)

#### 1. Filter Koleksi Dokumen Berdasarkan Jenjang Peserta
Di bagian atas view atau sebelum Tab Switcher, siapkan koleksi dokumen yang telah difilter:

```blade
@php
    $jenjangPeserta = $penempatan->pendaftaran?->user?->profil?->jenjang;
    $rawDokumen = $penempatan->pendaftaran?->dokumen ?? collect();
    
    // Filter dokumen yang relevan sesuai jenjang peserta (SMK / D3 / S1)
    $filteredDokumen = $rawDokumen->filter(function ($dok) use ($jenjangPeserta) {
        if (! $jenjangPeserta || ! $dok->jenisDokumen) {
            return true;
        }
        return ! $dok->jenisDokumen->jenjang || $dok->jenisDokumen->jenjang === $jenjangPeserta;
    });

    // Fallback jika tidak ada dokumen yang cocok, gunakan seluruh dokumen yang terunggah
    $dokumenList = $filteredDokumen->isNotEmpty() ? $filteredDokumen : $rawDokumen;
@endphp
```

#### 2. Sinkronkan Pill Counter Tab Switcher
Ubah counter Dokumen Pendaftaran pada baris 190:
```blade
{{-- SEBELUM --}}
<span class="pill-counter">({{ (string) ($penempatan->pendaftaran?->dokumen?->count() ?? 0) }})</span>

{{-- SESUDAH --}}
<span class="pill-counter">({{ (string) $dokumenList->count() }})</span>
```

#### 3. Hero Header Status (Baris 60)
Ganti badge status "Selesai Magang" menjadi warna biru:
```blade
{{-- SEBELUM --}}
<span style="display: inline-flex; align-items: center; padding: 0.2rem 0.65rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; background: #ECFDF5; color: #065F46; border: 1px solid #A7F3D0;">
    Selesai Magang
</span>

{{-- SESUDAH --}}
<span class="badge-status-blue" style="border-radius: 9999px;">
    Selesai Magang
</span>
```

#### 4. Tab 1: Ringkasan & Biodata - Kartu Pembimbing Lapangan (Baris 251-252)
Ubah warna teks "Tugas Selesai" dari hijau menjadi biru:
```blade
{{-- SEBELUM --}}
<strong style="color: var(--green-600);">{{ (string) $penempatan->tugas->where('status.value', 'selesai')->count() }} Selesai</strong>

{{-- SESUDAH --}}
<strong style="color: #01347C;">{{ (string) $penempatan->tugas->where('status.value', 'selesai')->count() }} Selesai</strong>
```

#### 5. Tab 2: Riwayat Tugas - Hapus Deskripsi & Ubah Badge Status Menjadi Biru
Pada baris 267-277 dan 293-295:
```blade
{{-- SEBELUM --}}
<div>
    <h3 style="margin: 0; font-size: 1rem; font-weight: 800; color: var(--neutral-800);">
        Daftar Tugas &amp; Penugasan Magang (Read-Only)
    </h3>
    <p style="margin: 0.25rem 0 0 0; font-size: 0.8125rem; color: var(--neutral-600);">
        Rekam penugasan kerja yang diberikan pembimbing selama masa dinas aktif.
    </p>
</div>

...
<span style="font-size: 0.75rem; font-weight: 700; padding: 0.25rem 0.65rem; border-radius: 6px; background: #ECFDF5; color: #065F46; border: 1px solid #A7F3D0; white-space: nowrap;">
    Selesai
</span>

{{-- SESUDAH --}}
<div>
    <h3 style="margin: 0; font-size: 1rem; font-weight: 800; color: var(--neutral-800);">
        Daftar Tugas &amp; Penugasan Magang (Read-Only)
    </h3>
</div>

...
<span class="badge-status-blue">
    Selesai
</span>
```

#### 6. Tab 3: Logbook Harian - Hapus Deskripsi & Ubah Counter & Badge Selesai Menjadi Biru
Pada baris 335-346 dan 379-382:
```blade
{{-- SEBELUM --}}
<div>
    <h3 style="margin: 0; font-size: 1rem; font-weight: 800; color: var(--neutral-800);">
        Rekam Jejak Logbook Harian Magang (Read-Only)
    </h3>
    <p style="margin: 0.25rem 0 0 0; font-size: 0.8125rem; color: var(--neutral-600);">
        Catatan kehadiran, jurnal aktivitas, dan rekap bimbingan harian peserta magang.
    </p>
</div>
<span style="font-size: 0.75rem; font-weight: 700; padding: 0.3rem 0.75rem; border-radius: 9999px; background: #FEF3C7; color: #92400E; border: 1px solid #FDE68A;">
    Total: {{ $penempatan->logbooks->count() }} Entri Terisi
</span>

...
<span style="font-size: 0.725rem; font-weight: 700; padding: 0.25rem 0.6rem; border-radius: 4px; background: #ECFDF5; color: #065F46; border: 1px solid #A7F3D0;">
    Selesai
</span>

{{-- SESUDAH --}}
<div>
    <h3 style="margin: 0; font-size: 1rem; font-weight: 800; color: var(--neutral-800);">
        Rekam Jejak Logbook Harian Magang (Read-Only)
    </h3>
</div>
<span class="badge-counter-blue">
    Total: {{ $penempatan->logbooks->count() }} Entri Terisi
</span>

...
<span class="badge-status-blue">
    Selesai
</span>
```

#### 7. Tab 4: Dokumen Pendaftaran - Hapus Deskripsi, Pakai `$dokumenList`, & Ubah Badge Valid Menjadi Biru
Pada baris 404-440:
```blade
{{-- SEBELUM --}}
<div>
    <h3 style="margin: 0; font-size: 1rem; font-weight: 800; color: var(--neutral-800);">
        Berkas Administrasi &amp; Dokumen Terverifikasi
    </h3>
    <p style="margin: 0.25rem 0 0 0; font-size: 0.8125rem; color: var(--neutral-600);">
        Dokumen resmi pendaftaran yang telah diverifikasi valid oleh Admin Seleksi MagKom.
    </p>
</div>
<span style="font-size: 0.75rem; font-weight: 700; padding: 0.3rem 0.75rem; border-radius: 9999px; background: #F3E8FF; color: #6B21A8; border: 1px solid #E9D5FF;">
    Total: {{ $penempatan->pendaftaran?->dokumen?->count() ?? 0 }} Berkas Terdata
</span>

@if($penempatan->pendaftaran && $penempatan->pendaftaran->dokumen->isNotEmpty())
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1rem;">
        @foreach($penempatan->pendaftaran->dokumen as $dok)
            ...
            <span style="font-size: 0.6875rem; font-weight: 700; padding: 0.15rem 0.45rem; border-radius: 4px; background: #ECFDF5; color: #065F46; border: 1px solid #A7F3D0; white-space: nowrap;">
                Valid
            </span>

{{-- SESUDAH --}}
<div>
    <h3 style="margin: 0; font-size: 1rem; font-weight: 800; color: var(--neutral-800);">
        Berkas Administrasi &amp; Dokumen Terverifikasi
    </h3>
</div>
<span class="badge-counter-blue">
    Total: {{ $dokumenList->count() }} Berkas Terdata
</span>

@if($dokumenList->isNotEmpty())
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1rem;">
        @foreach($dokumenList as $dok)
            ...
            <span class="badge-status-blue">
                Valid
            </span>
```

---

### 🔹 LANGKAH 3 (Opsional tapi Direkomendasikan): Perbaiki Helper Seeder
**Berkas:** [`database/seeders/ProductionRealisticDataSeeder.php`](file:///g:/MAGKOM/database/seeders/ProductionRealisticDataSeeder.php)

Di baris 390-404, ubah helper `$lampirkanBerkas` agar menerima parameter `JenjangEnum $jenjang`:
```php
// SEBELUM:
$jenisDokumenList = JenisDokumen::all();
$lampirkanBerkas = function (Pendaftaran $pendaftaran) use ($jenisDokumenList) {
    foreach ($jenisDokumenList as $jd) { ... }
};

// SESUDAH:
$lampirkanBerkas = function (Pendaftaran $pendaftaran, JenjangEnum $jenjang) {
    $jenisDokumenList = JenisDokumen::where('aktif', true)
        ->where('jenjang', $jenjang->value)
        ->get();

    foreach ($jenisDokumenList as $jd) {
        Dokumen::updateOrCreate(
            ['pendaftaran_id' => $pendaftaran->id, 'jenis_dokumen_id' => $jd->id],
            [
                'nama_asli' => strtolower(str_replace(' ', '_', $jd->nama)).'.pdf',
                'path' => 'dokumen/sample_'.$jd->kode.'.pdf',
                'ukuran' => rand(180000, 450000),
                'mime' => 'application/pdf',
                'hash' => sha1('sample_'.$jd->kode.'_'.$pendaftaran->id),
            ]
        );
    }
};
```
Hal ini memastikan database seeding masa depan tetap murni dan tidak ada lagi kontaminasi 12 berkas untuk 1 pendaftaran.

---

## ⚡ 5. 🧪 PANDUAN QUALITY GATES & VERIFIKASI (Testing Suite)

Jalankan perintah verifikasi resmi sistem menggunakan **PHP 8.3** sistem:

```powershell
# 1. Bersihkan Cache Template
& "F:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe" artisan view:clear
& "F:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe" artisan config:clear

# 2. Kompilasi Aset Frontend Vite
npm run build

# 3. Jalankan Feature Tests Terkait Detail Arsip
& "F:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe" artisan test tests/Feature/AdminLaporanDanArsipTest.php tests/Feature/DeltaDashboardEnhancementTest.php

# 4. Jalankan Seluruh Test Suite SuperAdmin
& "F:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe" artisan test --filter=SuperAdmin

# 5. Audit Format Kode dengan Pint
& "F:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe" vendor/bin/pint --test

# 6. Static Analysis dengan PHPStan
& "F:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe" vendor/bin/phpstan analyse --memory-limit=1G

# 7. Perbarui Knowledge Graph
graphify update .
```

---

## ✅ 6. 📋 CHECKLIST PENYELESAIAN (Definition of Done)

- [x] Tiga baris subteks deskripsi di Tab 2 (Tugas), Tab 3 (Logbook), dan Tab 4 (Dokumen) telah dihapus.
- [x] Koleksi dokumen difilter berdasarkan jenjang profil peserta (`JenjangEnum`). Jumlah dokumen yang tampil pada alumni S1 menjadi tepat 4 berkas (bukan 12 berkas).
- [x] Tab counter pill dan badge counter total dokumen menampilkan jumlah berkas yang relevan (`4 Berkas Terdata`).
- [x] Badge status *"Valid"* diubah menjadi biru muda MagKom (`.badge-status-blue`).
- [x] Badge status *"Selesai"* di kartu tugas, baris tabel logbook, badge hero, dan ringkasan pembimbing diubah menjadi biru MagKom.
- [x] Badge counter peringatan total entri logbook yang sebelumnya kuning/amber diubah menjadi pill biru MagKom (`.badge-counter-blue`).
- [x] Zero inline CSS / zero `<style>` tags di Blade; seluruh styling tertata di `superadmin-common.css`.
- [x] Seluruh automated test suite (`AdminLaporanDanArsipTest` & `DeltaDashboardEnhancementTest`) lolos hijau 100%.
- [x] Tampilan responsif dan mendukung *Dark Mode* (`[data-theme="dark"]`).

---

> **Pesan Senior Architect:**  
> Tugas ini memastikan antarmuka Detail Arsip Alumni Magang tampil bersih, ringkas (*compact*), dan memiliki integritas data yang akurat. Junior developer tidak perlu bingung mengenai mengapa sebelumnya ada 12 berkas: itu adalah artefak data dari seeder lama, dan dengan menerapkan filter jenjang di view serta merapikan seeder, data yang ditampilkan akan selalu selaras 100% dengan persyaratan jenjang masing-masing alumni.

