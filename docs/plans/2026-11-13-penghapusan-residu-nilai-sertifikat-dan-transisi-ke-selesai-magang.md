# SENIOR DEVELOPER TASK BRIEF & PRD PLAN: PENGHAPUSAN MENYELURUH RESIDU NILAI & SERTIFIKAT SERTA TRANSISI TOTAL KE "SELESAI MAGANG"

> **Dari:** Senior Software Architect / Lead Developer  
> **Kepada:** Junior Developer / AI Agent (Gemini 3.8 Flash / Antigravity Agent)  
> **Target Repositori:** MAGKOM (Portal Manajemen Magang Dinas Kominfo Sleman)  
> **Status:** PROPOSED & READY FOR IMPLEMENTATION APPROVAL  
> **Tingkat Kesulitan:** Menengah (Multi-Layer Cleanup: CMS, Interactive SVG Roadmaps, Dashboards, PDF, Database Sanitization, Automated Tests)  
> **Dokumentasi Terkait:** `AGENTS.md`, `docs/01-PRD-MagKom.md`, `docs/04-SKEMA-DATABASE-DAN-DATA-DICTIONARY.md`

---

## 1. 📌 RINGKASAN EKSEKUTIF & LATAR BELAKANG ARSITEKTUR

### 1.1 Arahan Pengguna (User Directives)
1. **Landing Page & Peta Alur Interaktif**:
   - Seluruh teks, kartu, plang halte, balon webtoon, dan grafis bertuliskan **"Nilai & Sertifikat"** harus dihilangkan sepenuhnya.
   - Pos/Tahap 04 diganti menjadi **"Selesai Magang"** (Penyelesaian program magang, pengumpulan laporan akhir, dan pelepasan peserta secara resmi oleh dinas).
   - Teks deskripsi dan summary alur (langkah ke-4) harus diselaraskan secara konsisten.
2. **Pembersihan Residu Global (Zero Residue di Seluruh Dashboard & Portal)**:
   - Pengguna menegaskan: *"dan dimana pun berada udah ga ada lagi nilai dan sertifikat beneran udah di hapus fitur itu jadi klo masi ada nilai dan sertifikat di project ini dihapus yg klo masih ada fitur itu di dashboard manapun"*.
   - Fitur penilaian dan sertifikat sejatinya telah di-decommissioning pada level database tabel (`Schema::dropIfExists('penilaian')`), namun masih meninggalkan **residu visual, teks label, placeholder, opsi dropdown, dan default array CMS** pada dashboard Admin, Pembimbing, SuperAdmin, Dokumen Panduan PDF, serta data tersimpan di tabel `pengaturan` MySQL.
3. **Standar Penyampaian Rencana**:
   - Disusun dengan instruksi operasional yang sangat presisi (*foolproof*), berformat task senior developer agar junior developer atau model AI (seperti Gemini 3.8 Flash) dapat mengeksekusinya secara mekanis, akurat, dan tanpa halusinasi (*zero regressions*).

---

## 2. 🗺️ AUDIT MAP: DI MANA SAJA RESIDU FITUR INI MASIH TERSISA?

Berdasarkan investigasi menyeluruh pada codebase, berikut adalah peta sebaran residu yang wajib dibersihkan:

```
┌────────────────────────────────────────────────────────────────────────────────────────┐
│               PETA AUDIT RESIDU NILAI & SERTIFIKAT DI REPOSITORI MAGKOM                │
├─────────────────────────┬──────────────────────────────────────────────────────────────┤
│ 1. Landing Page & CMS   │ • app/Services/LandingPage/BlockRegistry.php (Tahapan & Fitur)│
│                         │ • app/Services/LandingPage/PatternRegistry.php               │
│                         │ • app/Services/LandingCmsService.php (Default array & FAQ)   │
│                         │ • app/Http/Controllers/SuperAdmin/LandingCmsSiteEditor...    │
│                         │ • resources/views/public/blocks/tahapan.blade.php            │
│                         │ • resources/views/public/blocks/features.blade.php           │
│                         │ • resources/views/public/blocks/faq.blade.php                │
│                         │ • resources/views/superadmin/landing/index.blade.php         │
├─────────────────────────┼──────────────────────────────────────────────────────────────┤
│ 2. Peta Alur & Roadmap  │ • resources/views/public/peta-alur-interaktif.blade.php      │
│                         │ • resources/views/public/partials/_peta_alur_modal.blade.php │
│                         │ • resources/views/public/panduan-alur.blade.php              │
│                         │ • resources/js/pages/public/peta-alur.js                     │
│                         │ • resources/js/pages/public/landing.js & public/js/...       │
├─────────────────────────┼──────────────────────────────────────────────────────────────┤
│ 3. Dashboard Admin      │ • app/Http/Controllers/Admin/PenempatanController.php ($nilaiAkhir)
│                         │ • resources/views/admin/penempatan/show.blade.php (Stat 3)   │
│                         │ • resources/views/admin/penempatan/index.blade.php (subText) │
├─────────────────────────┼──────────────────────────────────────────────────────────────┤
│ 4. Dashboard Pembimbing │ • resources/views/pembimbing/dashboard.blade.php (Anchor link)│
│                         │ • resources/views/pembimbing/dashboard/partials/_tab_mentee  │
│                         │ • resources/views/pembimbing/dashboard/partials/_tab_evaluasi│
├─────────────────────────┼──────────────────────────────────────────────────────────────┤
│ 5. Dashboard SuperAdmin │ • app/Services/SuperAdminExecutiveMetricsService.php         │
│                         │ • resources/views/superadmin/dashboard.blade.php             │
│                         │ • resources/views/superadmin/dashboard/partials/_tab-bimbingan
│                         │ • resources/views/superadmin/dashboard/partials/_tab-ringkasan
│                         │ • resources/views/superadmin/audit-log/partials/_stream_table│
│                         │ • resources/views/superadmin/role/index.blade.php            │
│                         │ • resources/views/superadmin/pengaturan/index.blade.php      │
├─────────────────────────┼──────────────────────────────────────────────────────────────┤
│ 6. Dokumen PDF & Legal  │ • resources/views/pdf/buku-panduan-magang.blade.php          │
│                         │ • resources/views/auth/partials/_modal_privasi_syarat.blade  │
│                         │ • resources/views/public/kebijakan-privasi.blade.php         │
├─────────────────────────┼──────────────────────────────────────────────────────────────┤
│ 7. Database Runtime     │ • Tabel pengaturan: landing_tahapan_summary, landing_tahapan_steps,
│                         │   draft_landing_tahapan_summary, draft_landing_tahapan_steps,
│                         │   draft_landing_page_document, landing_page_document         │
├─────────────────────────┼──────────────────────────────────────────────────────────────┤
│ 8. Automated Tests      │ • tests/Feature/PetaAlurPageTest.php                         │
│                         │ • tests/Feature/AlurMagangPageTest.php                       │
│                         │ • tests/Feature/LandingCmsVisualBuilderAndThemeTest.php      │
│                         │ • tests/Feature/SuperAdminModuleEnhancementTest.php          │
└─────────────────────────┴──────────────────────────────────────────────────────────────┘
```

---

## 3. 🎯 TARGET STATUS: SEBELUM vs SESUDAH (BEFORE VS AFTER)

| Komponen | Status Lama (Before) | Status Baru (After) |
|---|---|---|
| **Pos 4 Landing Page** | `04 NILAI & SERTIFIKAT` &bull; Dapatkan evaluasi kinerja, lembar nilai resmi, dan sertifikat ber-barcode yang sah. | `04 SELESAI MAGANG` &bull; Penyelesaian program magang, pengumpulan laporan akhir, dan pelepasan resmi peserta oleh dinas. |
| **Peta Alur Interaktif** | Balon Pos 4: `KELULUSAN & SERTIFIKAT` &bull; Ikon mengangkat sertifikat. | Balon Pos 4: `SELESAI MAGANG` &bull; Pelepasan magang resmi & laporan akhir tuntas disetujui. |
| **Modal Peta Alur** | Balon: `E-Sertifikat Terbit! 🎓` & Info Card Nilai/Sertifikat. | Balon: `Magang Selesai! 🎓` & Info Card Penyelesaian Program Magang. |
| **Summary Tahapan (Keempat)**| "...setelah magang selesai, peserta mendapatkan lembar nilai dan sertifikat yang dapat diunduh." | "...setelah magang selesai, peserta menyelesaikan program magang, mengunggah laporan akhir, dan dinyatakan lulus secara resmi." |
| **Admin Penempatan Show** | Stat Card 3: `Nilai Akhir` (menampilkan "Belum Ada", "Evaluasi pembimbing"). | Stat Card 3: `Status Magang` (menampilkan status kelulusan / penempatan aktif, tuntas & selesai). |
| **Admin Penempatan Index**| Sub-text: `Telah tuntas & dinilai`. | Sub-text: `Telah tuntas magang`. |
| **Pembimbing Dashboard** | Link tersembunyi `Kelola Nilai Magang` & kolom `Status Nilai`. | Link `Kelola Progres Mentee` & kolom `Status Magang` (Aktif / Selesai). |
| **SuperAdmin Bimbingan** | Metrik `Penilaian Final Selesai`, Policy `Masa Tenggang Lembar Nilai`. | Metrik `Peserta Selesai Magang`, Policy `Masa Tenggang Akun Pasca Magang`. |
| **Database `pengaturan`** | Menyimpan JSON lama berisi teks `Nilai & Sertifikat`. | Disanitasi melalui migration otomatis menjadi teks `Selesai Magang`. |

---

## 4. 🛠️ PANDUAN IMPLEMENTASI TEKNIS LANGKAH DEMI LANGKAH (SENIOR TASK CHECKLIST)

---

### 🔹 TASK 1: Penyempurnaan Block Registry, Pattern Registry, & CMS Service (Backend CMS Engine)

**File yang Dimodifikasi:**
- [`app/Services/LandingPage/BlockRegistry.php`](file:///g:/MAGKOM/app/Services/LandingPage/BlockRegistry.php)
- [`app/Services/LandingPage/PatternRegistry.php`](file:///g:/MAGKOM/app/Services/LandingPage/PatternRegistry.php)
- [`app/Services/LandingCmsService.php`](file:///g:/MAGKOM/app/Services/LandingCmsService.php)
- [`app/Http/Controllers/SuperAdmin/LandingCmsSiteEditorController.php`](file:///g:/MAGKOM/app/Http/Controllers/SuperAdmin/LandingCmsSiteEditorController.php)

**Langkah-langkah Eksekusi:**
- [ ] **1.1 Update `BlockRegistry.php` (Blok `tahapan` & `features`)**:
  - Pada baris 172, ubah string `summary`:
    ```php
    // GANTI baris terakhir summary menjadi:
    "Keempat setelah magang selesai, peserta menyelesaikan program magang, mengunggah laporan akhir, dan dinyatakan lulus secara resmi."
    ```
  - Pada baris 179, ubah konfigurasi Step 04:
    ```php
    // SEBELUM:
    ['step' => '04', 'title' => 'Nilai & Sertifikat', 'badge' => 'KELULUSAN', 'desc' => 'Dapatkan evaluasi kinerja, lembar nilai resmi, dan sertifikat ber-barcode yang sah.'],
    // SESUDAH:
    ['step' => '04', 'title' => 'Selesai Magang', 'badge' => 'KELULUSAN', 'desc' => 'Penyelesaian program magang, pengumpulan laporan akhir, dan pelepasan resmi peserta oleh dinas.'],
    ```
  - Pada baris 266 (Blok `features`), ubah kartu keunggulan 3:
    ```php
    // SEBELUM:
    ['title' => 'Sertifikat & Nilai Resmi', 'desc' => 'Dokumen kelulusan resmi ber-barcode untuk menunjang portofolio karir.', 'icon' => '📜'],
    // SESUDAH:
    ['title' => 'Penyelesaian & Portofolio', 'desc' => 'Pengalaman kerja riil dan portofolio proyek pemerintahan untuk karir profesional.', 'icon' => '🏆'],
    ```

- [ ] **1.2 Update `PatternRegistry.php`**:
  - Pada baris 258, ubah kartu fitur `Sertifikat & Penilaian Sah` menjadi:
    ```php
    ['title' => 'Penyelesaian & Portofolio', 'desc' => 'Portofolio proyek nyata dan pengalaman kedinasan yang diakui instansi pemerintah.', 'icon' => '🏆'],
    ```
  - Pada baris 452, ubah summary tahapan agar tidak menyebut penerbitan sertifikat digital.
  - Pada baris 459, ubah Step 04 dari `Evaluasi & Sertifikat` menjadi `Selesai Magang`.

- [ ] **1.3 Update `LandingCmsService.php`**:
  - Pada baris 82 & 106, bersihkan jawaban default FAQ dari klaim `sertifikat resmi ber-QR code sah` atau `riwayat sertifikat`.
  - Pada baris 134, ubah `$defaultTahapanSteps[3]`:
    ```php
    ['step' => '04', 'title' => 'Selesai Magang', 'badge' => 'KELULUSAN', 'desc' => 'Penyelesaian program magang, pengesahan laporan akhir, dan penetapan status kelulusan resmi.'],
    ```
  - Pada baris 236, ubah teks default `tahapan_summary`.

- [ ] **1.4 Update `LandingCmsSiteEditorController.php`**:
  - Pada baris 88, ubah array `$defaultTahapanSteps[3]` ke `Selesai Magang`.
  - Pada baris 111 & 186, selaraskan summary default.

---

### 🔹 TASK 2: Restrukturisasi Tampilan Publik Landing Page

**File yang Dimodifikasi:**
- [`resources/views/public/blocks/tahapan.blade.php`](file:///g:/MAGKOM/resources/views/public/blocks/tahapan.blade.php)
- [`resources/views/public/blocks/features.blade.php`](file:///g:/MAGKOM/resources/views/public/blocks/features.blade.php)
- [`resources/views/public/blocks/faq.blade.php`](file:///g:/MAGKOM/resources/views/public/blocks/faq.blade.php)
- [`resources/js/pages/public/landing.js`](file:///g:/MAGKOM/resources/js/pages/public/landing.js)
- [`public/js/landing-interactive.js`](file:///g:/MAGKOM/public/js/landing-interactive.js)

**Langkah-langkah Eksekusi:**
- [ ] **2.1 Update `tahapan.blade.php`**:
  - Ubah default fallback baris 3 (`$tahapanSummary`) kalimat Keempat.
  - Ubah default fallback baris 10 (`$defaultTahapanSteps[3]`):
    ```php
    ['step' => '04', 'title' => 'Selesai Magang', 'badge' => 'KELULUSAN', 'desc' => 'Penyelesaian program magang, pengumpulan laporan akhir, dan penetapan status kelulusan resmi.'],
    ```
- [ ] **2.2 Update `features.blade.php`**:
  - Ubah kartu 3 baris 6 menjadi `Penyelesaian & Portofolio` dengan deskripsi pengalaman kerja profesional dan ikon `🏆`.
- [ ] **2.3 Update `faq.blade.php`**:
  - Ganti jawaban baris 33 & 57 yang menyebut kata sertifikat/penilaian menjadi kelulusan magang dan validasi status peserta.
- [ ] **2.4 Update Script Fallback `landing.js` & `landing-interactive.js`**:
  - Ganti item ke-4 array tahapan dan jawaban FAQ agar sinkron dengan template Blade.

---

### 🔹 TASK 3: Transformasi Peta Alur Interaktif & Modal Simulator

**File yang Dimodifikasi:**
- [`resources/views/public/peta-alur-interaktif.blade.php`](file:///g:/MAGKOM/resources/views/public/peta-alur-interaktif.blade.php)
- [`resources/views/public/partials/_peta_alur_modal.blade.php`](file:///g:/MAGKOM/resources/views/public/partials/_peta_alur_modal.blade.php)
- [`resources/views/public/panduan-alur.blade.php`](file:///g:/MAGKOM/resources/views/public/panduan-alur.blade.php)
- [`resources/js/pages/public/peta-alur.js`](file:///g:/MAGKOM/resources/js/pages/public/peta-alur.js)
- [`resources/js/pages/public/panduan-alur.js`](file:///g:/MAGKOM/resources/js/pages/public/panduan-alur.js)

**Langkah-langkah Eksekusi:**
- [ ] **3.1 Update `peta-alur-interaktif.blade.php`**:
  - Pada baris 561, ubah `$defaultTahapanSteps[3]`:
    ```php
    ['step' => '04', 'title' => 'Selesai Magang', 'badge' => 'SELESAI MAGANG', 'desc' => 'Penyelesaian program magang, pengumpulan laporan akhir, dan pelepasan resmi peserta oleh Diskominfo Sleman.'],
    ```
  - Pada baris 684, ubah teks label plang halte:
    ```html
    <text x="0" y="21" text-anchor="middle" font-size="7.5" font-weight="900" fill="#ffffff" font-family="'Lexend', sans-serif">04 {{ \Illuminate\Support\Str::limit(strtoupper($stepsData[3]['title'] ?? 'SELESAI MAGANG'), 14, '') }}</text>
    ```
  - Pada baris 691, **HAPUS** logika string replace yang memaksakan kata `SERTIFIKAT`:
    ```html
    <!-- SEBELUM: -->
    <text x="65" y="-119" text-anchor="start" font-size="11" font-weight="900" fill="#0f172a" font-family="'Lexend', sans-serif">{!! !str_contains(strtoupper($stepsData[3]['title'] ?? ''), 'SERTIFIKAT') && strtoupper($stepsData[3]['title'] ?? '') === 'KELULUSAN' ? 'KELULUSAN & SERTIFIKAT' : strtoupper($stepsData[3]['title'] ?? 'KELULUSAN & SERTIFIKAT') !!}</text>
    
    <!-- SESUDAH: -->
    <text x="65" y="-119" text-anchor="start" font-size="11" font-weight="900" fill="#0f172a" font-family="'Lexend', sans-serif">{!! strtoupper($stepsData[3]['title'] ?? 'SELESAI MAGANG') !!}</text>
    ```
  - Pada baris 823 & 844, sesuaikan komentar dan elemen grafis karakter Pos 4 menjadi figur mahasiswa yang merayakan kelulusan/selesai magang membawa map laporan akhir dinas.

- [ ] **3.2 Update `_peta_alur_modal.blade.php`**:
  - Pada baris 344, ubah teks gelembung karakter:
    ```html
    <!-- SEBELUM: -->
    <text x="31" y="-20" text-anchor="middle" font-size="9.5" font-weight="700" fill="#ffffff">E-Sertifikat Terbit! 🎓</text>
    <!-- SESUDAH: -->
    <text x="31" y="-20" text-anchor="middle" font-size="9.5" font-weight="700" fill="#ffffff">Magang Selesai! 🎓</text>
    ```
  - Pada baris 581, perbarui aturan laporan akhir:
    ```javascript
    rule: "Wajib disetujui Pembimbing Lapangan untuk menyelesaikan program magang."
    ```
  - Pada baris 583–593, hapus entri `"Lembar Penilaian Pembimbing"` dan `"E-Sertifikat Resmi Diskominfo"`, ganti dengan entri tunggal yang bersih:
    ```javascript
    "Penyelesaian Program & Pelepasan": {
        format: "Status Kelulusan Resmi di Portal MAGKOM",
        note: "Pelepasan resmi peserta magang oleh Dinas Kominfo Kabupaten Sleman.",
        rule: "Penetapan status tuntas magang oleh mentor ASN."
    }
    ```

- [ ] **3.3 Update `panduan-alur.blade.php` & `panduan-alur.js`**:
  - Pada baris 1854, ubah kalimat pengantar: "...hingga penyelesaian program magang secara komprehensif."
  - Pada baris 1895, ganti label KPI `Sertifikat Terverifikasi` menjadi `Peserta Lulus Magang`.
  - Pada baris 2012, 2075, dan 2229, ubah `Tahap 07 Nilai & Sertifikat` menjadi `Tahap 07 Selesai Magang`.
  - Pada baris 2245, ubah terminator node:
    ```html
    <span class="terminator-title">FINISH &bull; Selesai Magang &amp; Pelepasan</span>
    ```
  - Pada baris 2443–2506, hapus pertanyaan FAQ tentang cara mengunduh nilai dan sertifikat ber-barcode, ganti dengan FAQ mengenai masa tenggang akun 30 hari pasca selesai magang.

- [ ] **3.4 Update `peta-alur.js`**:
  - Pada baris 28–34, ubah objek konfigurasi Pos 4:
    ```javascript
    4: {
        num: "04",
        title: "SELESAI MAGANG",
        desc: "Penyelesaian program magang, pengumpulan laporan akhir, dan penetapan status kelulusan resmi.",
        numColor: "#f59e0b",
        btnColor: "#16a34a",
        nextText: "Daftar Sekarang 🚀"
    }
    ```

---

### 🔹 TASK 4: Pembersihan Residu Dashboard Admin

**File yang Dimodifikasi:**
- [`app/Http/Controllers/Admin/PenempatanController.php`](file:///g:/MAGKOM/app/Http/Controllers/Admin/PenempatanController.php)
- [`resources/views/admin/penempatan/show.blade.php`](file:///g:/MAGKOM/resources/views/admin/penempatan/show.blade.php)
- [`resources/views/admin/penempatan/index.blade.php`](file:///g:/MAGKOM/resources/views/admin/penempatan/index.blade.php)

**Langkah-langkah Eksekusi:**
- [ ] **4.1 Bersihkan `PenempatanController.php`**:
  - Pada method `show()` (baris 182 & 190): Hapus `$nilaiAkhir = null;` dan pengiriman `'nilaiAkhir' => $nilaiAkhir` ke view.
- [ ] **4.2 Redesain Stat 3 di `resources/views/admin/penempatan/show.blade.php`**:
  - Pada baris 191–207, ganti tile `Nilai Akhir` dengan **Status Magang / Kelulusan**:
    ```blade
    {{-- Stat 3: Status Magang / Kelulusan --}}
    <div class="metric-tile-card">
        <div class="metric-tile-top">
            <span class="metric-tile-title">Status Magang</span>
            <div class="metric-tile-icon-box" style="background: #ECFDF5; color: #059669;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="8" r="7"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline></svg>
            </div>
        </div>
        <div class="metric-tile-value" style="font-size: 1.125rem;">
            {{ $penempatan->status->label() }}
        </div>
        <div class="metric-tile-sub">
            <span style="font-weight: 700; color: {{ $penempatan->status === \App\Enums\StatusPenempatanEnum::Selesai ? '#059669' : '#4F46E5' }};">
                {{ $penempatan->status === \App\Enums\StatusPenempatanEnum::Selesai ? 'Program Magang Tuntas' : 'Sedang Bimbingan Aktif' }}
            </span>
        </div>
    </div>
    ```
- [ ] **4.3 Update `resources/views/admin/penempatan/index.blade.php`**:
  - Pada baris 822: Ubah `subText="Telah tuntas & dinilai"` menjadi `subText="Telah tuntas magang"`.

---

### 🔹 TASK 5: Pembersihan Residu Dashboard Pembimbing

**File yang Dimodifikasi:**
- [`resources/views/pembimbing/dashboard.blade.php`](file:///g:/MAGKOM/resources/views/pembimbing/dashboard.blade.php)
- [`resources/views/pembimbing/dashboard/partials/_tab_mentee.blade.php`](file:///g:/MAGKOM/resources/views/pembimbing/dashboard/partials/_tab_mentee.blade.php)
- [`resources/views/pembimbing/dashboard/partials/_tab_evaluasi.blade.php`](file:///g:/MAGKOM/resources/views/pembimbing/dashboard/partials/_tab_evaluasi.blade.php)

**Langkah-langkah Eksekusi:**
- [ ] **5.1 Update `pembimbing/dashboard.blade.php`**:
  - Pada baris 80 (bagian accessibility/headless anchor layer):
    ```blade
    <!-- SEBELUM: -->
    <a href="{{ route('pembimbing.peserta.index') }}">Kelola Nilai Magang</a>
    <!-- SESUDAH: -->
    <a href="{{ route('pembimbing.peserta.index') }}">Kelola Progres Mentee</a>
    ```
- [ ] **5.2 Update `_tab_mentee.blade.php`**:
  - Pada baris 25, ubah `<th>Status Nilai</th>` menjadi `<th>Status Magang</th>`.
  - Pada baris 35 & 68–69, ganti badge status nilai dengan badge status kelulusan/penempatan (`Selesai Magang` / `Aktif Bimbingan`).
- [ ] **5.3 Update `_tab_evaluasi.blade.php`**:
  - Pada baris 11 & 19, ganti referensi ID `table-evaluasi-penilaian` menjadi `table-kelulusan-mentee`.

---

### 🔹 TASK 6: Pembersihan Residu Dashboard SuperAdmin

**File yang Dimodifikasi:**
- [`app/Services/SuperAdminExecutiveMetricsService.php`](file:///g:/MAGKOM/app/Services/SuperAdminExecutiveMetricsService.php)
- [`resources/views/superadmin/dashboard.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/dashboard.blade.php)
- [`resources/views/superadmin/dashboard/partials/_tab-bimbingan.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/dashboard/partials/_tab-bimbingan.blade.php)
- [`resources/views/superadmin/dashboard/partials/_tab-ringkasan.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/dashboard/partials/_tab-ringkasan.blade.php)
- [`resources/views/superadmin/audit-log/partials/_stream_table.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/audit-log/partials/_stream_table.blade.php)
- [`resources/views/superadmin/role/index.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/role/index.blade.php)
- [`resources/views/superadmin/pengaturan/index.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/pengaturan/index.blade.php)
- [`resources/views/superadmin/landing/index.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/landing/index.blade.php)

**Langkah-langkah Eksekusi:**
- [ ] **6.1 Refactor Metrik di `SuperAdminExecutiveMetricsService.php`**:
  - Hapus variabel dead-code: `$totalPenilaianSelesai`, `$passingGradeNilai`, `$masaTenggangUnduhNilaiHari`, `$penempatanSelesaiDenganNilai`, `$kepatuhanNilaiSelesaiPersen`.
  - Pertahankan/gunakan:
    - `'penempatanSelesaiTotal' => $penempatanSelesaiTotal`
    - `'masaTenggangBulan' => $masaTenggangBulan`
- [ ] **6.2 Update `superadmin/dashboard.blade.php`**:
  - Pada baris 179: Ganti `<span>Lembar Nilai Alumni (Target 100%)</span>` dengan `<span>Penyelesaian Magang Alumni (Target 100%)</span>`.
  - Pada baris 268: Ganti `<span>Penilaian Final Selesai</span>` dengan `<span>Peserta Selesai Magang</span>`.
- [ ] **6.3 Update `_tab-bimbingan.blade.php`**:
  - Pada baris 36–40: Ubah label metrik 3 dari `Penilaian Final Selesai` menjadi `Peserta Selesai Magang`, nilai: `{{ number_format($penempatanSelesaiTotal) }}`, hint: `Program magang tuntas`.
  - Pada baris 269–274: Ubah card policy `Masa Tenggang Lembar Nilai` menjadi `Masa Tenggang Akun Pasca Magang` (`{{ $masaTenggangBulan }} Bulan`). Hapus policy box `Minimal Nilai`.
- [ ] **6.4 Update `_tab-ringkasan.blade.php`**:
  - Pada baris 165–166: Ubah teks `Lembar nilai lengkap` dan `Lembar Nilai Alumni (Target 100%)` menjadi `Penyelesaian Magang Alumni`.
- [ ] **6.5 Update `_stream_table.blade.php` (Filter Audit Log)**:
  - Pada baris 131: Hapus atau ganti opsi `<option value="nilai_magang">Nilai &amp; Sertifikat</option>` dengan `<option value="penempatan">Penyelesaian Magang</option>`.
- [ ] **6.6 Update `role/index.blade.php` & `pengaturan/index.blade.php`**:
  - Pada baris 321 & 332 `role/index.blade.php`: Hapus placeholder referensi sertifikat (ganti contoh dengan: `Manajemen Penempatan Peserta` dan `penempatan.manajemen`).
  - Pada baris 342 & 440 `role/index.blade.php`: Hapus pilihan `<option value="Penilaian">Penilaian</option>`.
  - Pada baris 229 `pengaturan/index.blade.php`: Ubah komentar `KARTU 3: KEBIJAKAN RETENSI AKUN, SERTIFIKAT & AUDIT TRAIL` menjadi `KARTU 3: KEBIJAKAN RETENSI AKUN & AUDIT TRAIL`.
- [ ] **6.7 Update `landing/index.blade.php` (Form CMS SuperAdmin)**:
  - Pada baris 655: Ubah fallback input `step4_desc` menjadi `Penyelesaian program magang, pengumpulan laporan akhir, dan pelepasan resmi peserta oleh dinas.`.

---

### 🔹 TASK 7: Dokumen PDF Panduan, Modal Privasi & Kebijakan

**File yang Dimodifikasi:**
- [`resources/views/pdf/buku-panduan-magang.blade.php`](file:///g:/MAGKOM/resources/views/pdf/buku-panduan-magang.blade.php)
- [`resources/views/auth/partials/_modal_privasi_syarat.blade.php`](file:///g:/MAGKOM/resources/views/auth/partials/_modal_privasi_syarat.blade.php)
- [`resources/views/public/kebijakan-privasi.blade.php`](file:///g:/MAGKOM/resources/views/public/kebijakan-privasi.blade.php)

**Langkah-langkah Eksekusi:**
- [ ] **7.1 Update `buku-panduan-magang.blade.php`**:
  - Pada baris 188: Hapus kalimat "...hingga penerbitan sertifikat", ganti "...hingga penyelesaian masa magang".
  - Pada baris 280–282: Ubah judul langkah 4 menjadi:
    ```html
    <div class="step-header">Langkah 4: Penyelesaian Program Magang & Pelepasan Resmi</div>
    <p>Pada akhir periode magang, pembimbing lapangan memvalidasi kelengkapan logbook dan laporan akhir. Peserta yang telah menuntaskan seluruh kewajiban akan dinyatakan resmi lulus program magang Diskominfo Sleman.</p>
    ```
- [ ] **7.2 Update `_modal_privasi_syarat.blade.php`**:
  - Pada baris 79–80: Ubah poin 4 menjadi `4. Kelulusan & Penyelesaian Magang` dengan deskripsi penetapan kelulusan resmi bagi peserta berintegritas.
- [ ] **7.3 Update `kebijakan-privasi.blade.php`**:
  - Pada baris 113, 159, dan 168–169: Hapus frasa `penerbitan sertifikat resmi bertanda tangan elektronik (TTE)` dan ganti dengan penyelesaian administrasi dan penetapan status kelulusan magang.

---

### 🔹 TASK 8: Sanitasi Database Runtime (One-Time Migration / Seeder Script)

**Konteks Kritis:**
Data di database lokal MySQL pada tabel `pengaturan` saat ini sudah terisi dengan key:
- `landing_tahapan_summary`
- `draft_landing_tahapan_summary`
- `landing_tahapan_steps` (jika ada)
- `draft_landing_tahapan_steps`
- `draft_landing_page_document` (JSON WordPress Site Editor)
- `landing_page_document`

Jika database tidak disanitasi, landing page akan terus merender teks lama dari database meskipun file PHP sudah diubah.

**File Baru:**
- `database/migrations/2026_11_13_000001_sanitize_nilai_dan_sertifikat_residues_in_pengaturan.php`

**Langkah-langkah Eksekusi:**
- [ ] **8.1 Buat Migration Baru**:
  - Jalankan logika update pada method `up()`:
    1. Ambil `landing_tahapan_summary` dan `draft_landing_tahapan_summary`. Ganti kalimat Keempat yang mengandung kata `sertifikat` atau `nilai` dengan:
       `"Keempat setelah magang selesai, peserta menyelesaikan program magang, mengunggah laporan akhir, dan dinyatakan lulus secara resmi."`
    2. Periksa key `landing_tahapan_steps` dan `draft_landing_tahapan_steps`: jika berupa JSON array, update item index 3 (`step === '04'`) dengan title `"Selesai Magang"` dan deskripsi baru.
    3. Periksa JSON document pada `draft_landing_page_document` dan `landing_page_document`: telusuri blok bertipe `"tahapan"` (`id === 'block-tahapan-cff5e299'`), ubah `content.summary` dan `content.steps[3]` (`title: 'Selesai Magang'`, `desc: 'Penyelesaian program magang, pengumpulan laporan akhir, dan pelepasan resmi peserta oleh dinas.'`).
    4. Bersihkan dead keys dari tabel `pengaturan` jika masih ada: `passing_grade_sertifikat`, `masa_tenggang_unduh_nilai_hari`.
- [ ] **8.2 Jalankan Migration**:
  - Eksekusi `php artisan migrate --force`.

---

### 🔹 TASK 9: Penyelarasan Automated Test Suite (Zero Regression)

**File yang Dimodifikasi:**
- [`tests/Feature/PetaAlurPageTest.php`](file:///g:/MAGKOM/tests/Feature/PetaAlurPageTest.php)
- [`tests/Feature/AlurMagangPageTest.php`](file:///g:/MAGKOM/tests/Feature/AlurMagangPageTest.php)
- [`tests/Feature/LandingCmsVisualBuilderAndThemeTest.php`](file:///g:/MAGKOM/tests/Feature/LandingCmsVisualBuilderAndThemeTest.php)
- [`tests/Feature/SuperAdminModuleEnhancementTest.php`](file:///g:/MAGKOM/tests/Feature/SuperAdminModuleEnhancementTest.php)

**Langkah-langkah Eksekusi:**
- [ ] **9.1 Update `PetaAlurPageTest.php`**:
  - Baris 136: Ubah assertion `$response->assertSee('KELULUSAN & SERTIFIKAT', false);` menjadi `$response->assertSee('SELESAI MAGANG', false);`.
- [ ] **9.2 Update `AlurMagangPageTest.php`**:
  - Baris 80: Ubah assertion `$response->assertSee('FINISH &bull; Sertifikat QR &amp; Lembar Nilai', false);` menjadi `$response->assertSee('FINISH &bull; Selesai Magang &amp; Pelepasan', false);`.
- [ ] **9.3 Update `LandingCmsVisualBuilderAndThemeTest.php`**:
  - Baris 89 & 116: Ubah payload kategori FAQ uji coba dari `Sertifikat & Penilaian` menjadi `Kelulusan & Selesai Magang`.
- [ ] **9.4 Update `SuperAdminModuleEnhancementTest.php`**:
  - Baris 533 & 540: Ubah payload uji coba dari `E-Sertifikat` menjadi `Selesai Magang`.

---

## 5. 🛡️ RISK ASSESSMENT & SECURITY MITIGATIONS

1. **Risiko Regresi CMS WordPress Site Editor**:
   - *Risiko:* Perubahan manual pada JSON `draft_landing_page_document` dapat merusak format skema validator jika tidak mematuhi `BlockSchemaValidator`.
   - *Mitigasi:* Migration menggunakan manipulasi associative array PHP yang di-decode dan di-encode kembali secara aman dengan `JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES`.
2. **Risiko Regresi View Blade (Larangan CSS/JS di Blade)**:
   - *Risiko:* Menambahkan inline `<style>` atau `<script>` baru saat memodifikasi tampilan.
   - *Mitigasi:* Sesuai aturan `AGENTS.md` Bagian 3, dilarang menyisipkan tag `<style>` atau `<script>` baru pada file Blade. Seluruh perubahan gaya visual menggunakan class CSS existing di `resources/css/`.
3. **Risiko N+1 atau Performa Query**:
   - *Risiko:* Pengambilan status kelulusan di dashboard memicu query berulang.
   - *Mitigasi:* Menggunakan relasi dan accessor status yang sudah di-eager load pada controller.

---

## 6. ✅ DEFINITION OF DONE (DOD) CHECKLIST

Junior Developer / AI Agent dinyatakan **SELESAI** jika dan hanya jika seluruh kriteria berikut terpenuhi 100%:

- [ ] Tidak ada lagi kata `"Nilai & Sertifikat"` pada landing page publik (blok Tahapan dan blok Fitur).
- [ ] Plang halte, tombol pos, dan balon webtoon Pos 04 di Peta Alur Interaktif menampilkan `"SELESAI MAGANG"`.
- [ ] Modal Peta Alur menampilkan `"Magang Selesai! 🎓"` dan rincian alur bebas dari kata nilai/sertifikat.
- [ ] Kalimat keempat pada summary tahapan telah diganti dengan narasi penyelesaian magang dan pengumpulan laporan akhir.
- [ ] Dashboard Admin (Penempatan Show & Index) bersih dari kolom/stat Nilai Akhir dan menampilkan Status Magang.
- [ ] Dashboard Pembimbing bersih dari anchor link `Kelola Nilai Magang` dan kolom `Status Nilai`.
- [ ] Dashboard SuperAdmin bersih dari metrik penilaian dan policy minimal nilai.
- [ ] Buku panduan PDF, modal persetujuan pendaftaran, dan kebijakan privasi telah diselaraskan.
- [ ] Database runtime pada tabel `pengaturan` telah bersih dari teks residu lama.
- [ ] Seluruh test suite pada `tests/Feature/PetaAlurPageTest.php`, `tests/Feature/AlurMagangPageTest.php`, dan fitur terkait lulus 100% hijau (`PASS`).
- [ ] Tidak ada file Blade yang disisipi inline `<script>` atau `<style>` baru.

---

## 7. 🚀 PERTANYAAN KONFIRMASI (APPROVAL GATE)

Rencana kerja terperinci di atas telah didokumentasikan pada:
**`docs/plans/2026-11-13-penghapusan-residu-nilai-sertifikat-dan-transisi-ke-selesai-magang.md`**

Apakah Anda menyetujui rencana kerja ini, atau ada bagian yang ingin disesuaikan sebelum eksekusi dimulai?

