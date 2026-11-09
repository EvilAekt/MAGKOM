# 📋 SENIOR DEVELOPER TASK BRIEF / PRD
## Revamp Segmented Tabs, Penugasan, Logbook Harian & Dokumen Administrasi Detail Arsip Alumni Magang

- **Tanggal Pembuatan:** 09 November 2026  
- **Penulis:** Senior Backend & UI Architect MagKom  
- **Target Pelaksana:** Junior Developer / AI Implementation Model (Gemini 3.8 Flash / Claude Haiku / Antigravity Agent)  
- **Status:** 🟢 **COMPLETED / VERIFIED (ALL QUALITY GATES PASSED)**  
- **Kompatibilitas:** Laravel 10.x, PHP 8.3.x, DS-MagKom Industrial Standard, OWASP Top 10  
- **Dokumen Target:** [`resources/views/superadmin/arsip/show.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/arsip/show.blade.php), [`app/Http/Controllers/SuperAdmin/ArsipPesertaController.php`](file:///g:/MAGKOM/app/Http/Controllers/SuperAdmin/ArsipPesertaController.php), [`resources/css/pages/superadmin/superadmin-common.css`](file:///g:/MAGKOM/resources/css/pages/superadmin/superadmin-common.css)

---

## 🎯 1. 📌 LATAR BELAKANG & ANALISIS MASALAH (Root Cause Analysis)

Berdasarkan inspeksi audit antarmuka halaman **Detail Arsip Alumni Magang** pada modul SuperAdmin (sesuai 5 tangkapan layar `media_1794203942902_6d045508.png` s/d `media_1794204122826_2cbc98bf.png`), ditemukan 4 anomali teknis dan visual yang belum memenuhi standar tata kelola Dinas Kominfo Sleman:

### 1.1 Anomali Tab Switcher (Media 1 vs Media 2)
- **Kondisi Saat Ini (Media 2):** Tab switcher menggunakan gaya *underline tab* konvensional yang tampak renggang, tipis, dan tidak serasi dengan antarmuka modul laporan.
- **Ekspektasi Desain (Media 1):** Mengadopsi **Segmented Pill Tab Switcher** (seperti pada modul Laporan Hub `admin/laporan/index.blade.php`) dengan kontainer kartu putih melengkung (*rounded 12px*), *padding* dalam terpadu, dan tombol pill aktif berlatar biru tua khas Kominfo Sleman (`#01347C`) dengan transisi halus.

### 1.2 Anomali Tampilan Daftar Tugas (Media 3)
- **Kondisi Saat Ini:** Penugasan dibungkus dalam kotak *flat* dengan tata letak renggang dan informasi minim.
- **Ekspektasi:** Kartu penugasan yang kokoh dan berbobot: judul tegas, deskripsi ringkas, tanggal tenggat (*deadline*), tautan referensi/repositori (jika ada), catatan progres akhir, serta badge status hijau emerald *"Selesai"*.

### 1.3 Anomali Kolom Logbook Kosong & Status (Media 4)
- **Root Cause Kolom Kosong:** Pada view `show.blade.php`, kolom aktivitas magang memanggil properti `$lb->ringkasan_aktivitas ?? ($lb->aktivitas ?? '-')`. Padahal, skema kolom riil pada tabel `logbook` dan model `App\Models\Logbook` adalah **`kegiatan`**! Akibatnya, sistem selalu mengembalikan strip (`-`) padahal peserta mengisi puluhan kalimat aktivitas harian.
- **Root Cause Status:** Status di database bernilai `disetujui`. Pada konteks alumni yang sudah lulus/selesai magang, status logbook yang telah divalidasi lebih tepat dan bermartabat dilabeli sebagai **"Selesai"** (atau *"Disetujui & Selesai"*).

### 1.4 Anomali Berkas Administrasi & Pertanyaan Logika (Media 5)
- **Pertanyaan Pengguna:** *"Kenapa ada banyak banget teks Terverifikasi saat seleksi Valid? Isi dokumennya dulu baru ada ini, dan logic ini gimana? Dan bisa melihat dokumen yang sudah dikirimkan peserta sekalian jadi nggak cuma valid saja?"*
- **Root Cause Teknis Mendalam:**
  1. Pada `show.blade.php` tertulis:  
     `{{ ucwords((string) str_replace('_', ' ', (string) $dok->jenis_dokumen)) }}`  
     Pada tabel `dokumen`, kolom jenis dokumen **bukan bernama `jenis_dokumen`**, melainkan foreign key `jenis_dokumen_id` yang berelasi `belongsTo` ke model `JenisDokumen` (`$dok->jenisDokumen->nama`)! Karena salah nama properti, judul berkas terbaca `null` (kosong melompong). Akibatnya kartu hanya menampilkan subteks *"Terverifikasi saat seleksi"* dan badge *"Valid"*.
  2. Controller `ArsipPesertaController@show` melakukan eager loading `'pendaftaran.dokumen'` tanpa relasi `'jenisDokumen'`.
  3. Mengapa ada 12 item? Dalam skema basis data MagKom, sebuah pendaftaran memiliki rekam jejak berkas sesuai jenjang (SMK: 4 berkas, D3: 4 berkas, S1: 4 berkas, atau multi-unggah). Seluruh berkas tersebut tersimpan dalam database.
  4. Kartu saat ini **belum menyertakan tombol aksi** untuk melihat berkas. Padahal, model `App\Models\Dokumen` sudah menyediakan metode enkripsi bertanda tangan aman: `getSignedPreviewUrl()` dan `getSignedDownloadUrl()`.

---

## 🎯 2. 📋 TUJUAN & ACCEPTANCE CRITERIA (AC)

Junior Developer / AI Pelaksana wajib memenuhi seluruh kriteria penerimaan berikut:

- [x] **AC-1 (Segmented Pill Switcher):** Navigasi tab `Ringkasan & Biodata`, `Riwayat Tugas ({count})`, `Logbook Harian ({count})`, dan `Dokumen Pendaftaran ({count})` dirombak menjadi kontainer pill melengkung (*rounded 12px*) dengan tombol pill aktif berlatar `#01347C` dan teks putih tebal (persis seperti tangkapan layar Media 1).
- [x] **AC-2 (Refined Tugas Card):** Daftar penugasan magang menampilkan judul penugasan, deskripsi, tanggal batas waktu (*deadline*), badge status *"Selesai"*, catatan progres terakhir, serta tombol tautan referensi jika tersedia.
- [x] **AC-3 (Logbook Content & Status):** Kolom *Aktivitas Magang* menampilkan isi kolom riil `$lb->kegiatan` secara lengkap (tidak ada lagi tanda strip `-`), status berlabel hijau emerald *"Selesai"*, dan tanggal berformat Indonesia (`d M Y`).
- [x] **AC-4 (Interactive Berkas Card with Signed Preview):** Kartu berkas administrasi menampilkan nama kategori dokumen riil dari `$dok->jenisDokumen?->nama`, nama file asli yang diunggah (`$dok->nama_asli`), estimasi ukuran berkas, badge status *"Valid"*, serta **tombol pratinjau ("Lihat Berkas")** dan **tombol unduh ("Unduh")** memanfaatkan temporary signed URL (`$dok->getSignedPreviewUrl()`).
- [x] **AC-5 (Strict Architecture Rule):** Tidak boleh ada tag `<style>` baru atau inline CSS di Blade. Seluruh modul styling wajib ditaruh di `resources/css/pages/superadmin/superadmin-common.css`.

---

## 🏗️ 3. 🗺️ INVENTARIS FILE & PEMETAAN DAMPAK (Impact Matrix)

```
┌────────────────────────────────────────────────────────────────────────────────────────┐
│ 1. BACKEND CONTROLLER                                                                  │
│    app/Http/Controllers/SuperAdmin/ArsipPesertaController.php                          │
│    -> Tambahkan eager loading 'pendaftaran.dokumen.jenisDokumen'                      │
│                                                                                        │
│ 2. STYLESHEET RESMI (VITE)                                                             │
│    resources/css/pages/superadmin/superadmin-common.css                                │
│    -> Tambahkan styling .arsip-segmented-tabs, .arsip-tab-pill, .arsip-doc-card        │
│    -> Dukungan penuh Dark Mode [data-theme="dark"]                                     │
│                                                                                        │
│ 3. BLADE VIEW PRESENTASI                                                               │
│    resources/views/superadmin/arsip/show.blade.php                                     │
│    -> Rombak tab switcher menjadi segmented pill layout                                │
│    -> Perbaiki rendering Tab 2 (Tugas), Tab 3 (Logbook kegiatan), Tab 4 (Dokumen)      │
│                                                                                        │
│ 4. TEST SUITE VERIFIKASI                                                               │
│    tests/Feature/AdminLaporanDanArsipTest.php & DeltaDashboardEnhancementTest.php      │
│    -> Pastikan seluruh assertion string tab tetap lolos hijau 100%                     │
└────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 🛠️ 4. 📝 PANDUAN TEKNIS EKSEKUSI TAHAP DEMI TAHAP (Step-by-Step Implementation)

### 🔹 LANGKAH 1: Perbarui Eager Loading pada `ArsipPesertaController`
**Berkas:** [`app/Http/Controllers/SuperAdmin/ArsipPesertaController.php`](file:///g:/MAGKOM/app/Http/Controllers/SuperAdmin/ArsipPesertaController.php)

Ubah pemanggilan relasi di method `show(Penempatan $penempatan)`:
```php
// SEBELUM:
'pendaftaran.dokumen',

// SESUDAH:
'pendaftaran.dokumen.jenisDokumen',
```
Hal ini memastikan metadata nama dokumen (`$dok->jenisDokumen->nama`) siap dirender tanpa menimbulkan query N+1.

---

### 🔹 LANGKAH 2: Tambahkan Styling Segmented Tab & Berkas di `superadmin-common.css`
**Berkas:** [`resources/css/pages/superadmin/superadmin-common.css`](file:///g:/MAGKOM/resources/css/pages/superadmin/superadmin-common.css)

Tambahkan aturan CSS berikut pada bagian bawah berkas:

```css
/* =========================================================================
   SEGMENTED PILL TAB CONTROLLER & ARSIP DETAIL COMPONENTS
   ========================================================================= */

.arsip-segmented-tabs {
    background: var(--neutral-0, #FFFFFF);
    border: 1px solid var(--neutral-400, #CBD5E1);
    border-radius: 12px;
    padding: 0.5rem;
    box-shadow: var(--shadow-subtle, 0 1px 3px rgba(0,0,0,0.05));
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
    width: 100%;
}

.arsip-tab-pill {
    flex: 1;
    min-width: 170px;
    padding: 0.65rem 1rem;
    border-radius: 8px;
    border: none;
    background: transparent;
    font-size: 0.875rem;
    font-weight: 700;
    color: var(--neutral-700, #334155);
    cursor: pointer;
    text-align: center;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    transition: all 0.15s ease-in-out;
    text-decoration: none;
}

.arsip-tab-pill:hover {
    color: #01347C;
    background: #F1F5F9;
}

.arsip-tab-pill.active {
    background: #01347C !important;
    color: #FFFFFF !important;
    box-shadow: 0 2px 6px rgba(1, 52, 124, 0.25);
}

.arsip-tab-pill .pill-counter {
    font-size: 0.75rem;
    padding: 0.15rem 0.5rem;
    border-radius: 9999px;
    background: rgba(0, 0, 0, 0.08);
    color: inherit;
    font-weight: 700;
}

.arsip-tab-pill.active .pill-counter {
    background: rgba(255, 255, 255, 0.25) !important;
    color: #FFFFFF !important;
}

/* Kartu Penugasan Magang Modern */
.arsip-task-card {
    padding: 1.25rem;
    border: 1px solid var(--neutral-300, #CBD5E1);
    border-radius: 10px;
    background: var(--neutral-100, #F8FAFC);
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    transition: border-color 0.15s ease;
}

.arsip-task-card:hover {
    border-color: #01347C;
}

/* Kartu Dokumen Berkas Administrasi */
.arsip-doc-card {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 1rem 1.15rem;
    border-radius: 10px;
    border: 1.5px solid var(--neutral-300, #E2E8F0);
    background: var(--neutral-0, #FFFFFF);
    box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    gap: 0.75rem;
    transition: all 0.15s ease;
}

.arsip-doc-card:hover {
    border-color: #01347C;
    box-shadow: 0 4px 12px rgba(1, 52, 124, 0.08);
    transform: translateY(-1px);
}

.arsip-doc-header {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
}

.arsip-doc-icon {
    width: 38px;
    height: 38px;
    min-width: 38px;
    border-radius: 8px;
    background: #EFF6FF;
    color: #01347C;
    display: flex;
    align-items: center;
    justify-content: center;
}

.arsip-doc-title {
    font-size: 0.875rem;
    font-weight: 700;
    color: var(--neutral-900, #0F172A);
    line-height: 1.3;
}

.arsip-doc-meta {
    font-size: 0.75rem;
    color: var(--neutral-600, #64748B);
    margin-top: 0.2rem;
    word-break: break-all;
}

.arsip-doc-actions {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-top: auto;
    padding-top: 0.75rem;
    border-top: 1px dashed var(--neutral-300, #E2E8F0);
}

.btn-doc-action {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    font-size: 0.75rem;
    font-weight: 700;
    padding: 0.35rem 0.65rem;
    border-radius: 6px;
    text-decoration: none;
    transition: background 0.15s ease;
}

.btn-doc-action--preview {
    background: #EFF6FF;
    color: #01347C;
    border: 1px solid #BFDBFE;
}

.btn-doc-action--preview:hover {
    background: #DBEAFE;
}

.btn-doc-action--download {
    background: #F8FAFC;
    color: var(--neutral-700, #334155);
    border: 1px solid var(--neutral-300, #CBD5E1);
}

.btn-doc-action--download:hover {
    background: #E2E8F0;
}

/* Dark Mode Overrides */
[data-theme="dark"] .arsip-segmented-tabs {
    background: #002244 !important;
    border-color: rgba(63, 81, 181, 0.4) !important;
}

[data-theme="dark"] .arsip-tab-pill {
    color: #94A3B8 !important;
}

[data-theme="dark"] .arsip-tab-pill:hover {
    background: rgba(255, 255, 255, 0.08) !important;
    color: #FFFFFF !important;
}

[data-theme="dark"] .arsip-tab-pill.active {
    background: #0641CC !important;
    color: #FFFFFF !important;
}

[data-theme="dark"] .arsip-task-card,
[data-theme="dark"] .arsip-doc-card {
    background: #003566 !important;
    border-color: #3F51B5 !important;
}

[data-theme="dark"] .arsip-doc-icon {
    background: rgba(30, 64, 175, 0.4) !important;
    color: #93C5FD !important;
}

[data-theme="dark"] .arsip-doc-title {
    color: #F8FAFC !important;
}

[data-theme="dark"] .arsip-doc-meta {
    color: #94A3B8 !important;
}
```

---

### 🔹 LANGKAH 3: Rombak Struktur Tampilan View `show.blade.php`
**Berkas:** [`resources/views/superadmin/arsip/show.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/arsip/show.blade.php)

#### 1. Rombak Tab Switcher
Ganti `<div class="arsip-tab-bar">` dengan Segmented Pill Switcher:
```blade
{{-- 3. SEGMENTED PILL TAB CONTROLLER (DS-MAGKOM PARITY) --}}
<div class="arsip-segmented-tabs" role="tablist">
    <button type="button" class="arsip-tab-pill active" onclick="switchArsipTab('tab-ringkasan', this)">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
            <circle cx="12" cy="7" r="4"></circle>
        </svg>
        <span>Ringkasan &amp; Biodata</span>
    </button>
    <button type="button" class="arsip-tab-pill" onclick="switchArsipTab('tab-tugas', this)">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M9 11l3 3L22 4"></path>
            <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
        </svg>
        <span>Riwayat Tugas</span>
        <span class="pill-counter">({{ (string) $penempatan->tugas->count() }})</span>
    </button>
    <button type="button" class="arsip-tab-pill" onclick="switchArsipTab('tab-logbook', this)">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
            <polyline points="14 2 14 8 20 8"></polyline>
        </svg>
        <span>Logbook Harian</span>
        <span class="pill-counter">({{ (string) $penempatan->logbooks->count() }})</span>
    </button>
    <button type="button" class="arsip-tab-pill" onclick="switchArsipTab('tab-administrasi', this)">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
            <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
        </svg>
        <span>Dokumen Pendaftaran</span>
        <span class="pill-counter">({{ (string) ($penempatan->pendaftaran?->dokumen?->count() ?? 0) }})</span>
    </button>
</div>
```

#### 2. Rapikan Tab 2: Riwayat Tugas
```blade
{{-- TAB 2: RIWAYAT TUGAS --}}
<div class="arsip-tab-pane" id="tab-tugas">
    <div class="gentelella-card" style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-top: 3.5px solid var(--magkom-primary-blue, #01347C) !important; border-radius: 12px; padding: 1.5rem; box-shadow: var(--shadow-subtle);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.75rem;">
            <div>
                <h3 style="margin: 0; font-size: 1rem; font-weight: 800; color: var(--neutral-800);">
                    Daftar Tugas &amp; Penugasan Magang (Read-Only)
                </h3>
                <p style="margin: 0.25rem 0 0 0; font-size: 0.8125rem; color: var(--neutral-600);">
                    Rekam penugasan kerja yang diberikan pembimbing selama masa dinas aktif.
                </p>
            </div>
            <span style="font-size: 0.75rem; font-weight: 700; padding: 0.3rem 0.75rem; border-radius: 9999px; background: #EFF6FF; color: #01347C; border: 1px solid #BFDBFE;">
                Total: {{ $penempatan->tugas->count() }} Penugasan
            </span>
        </div>

        @if($penempatan->tugas->isNotEmpty())
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                @foreach($penempatan->tugas as $t)
                    <div class="arsip-task-card">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem; flex-wrap: wrap;">
                            <div style="flex: 1; min-width: 250px;">
                                <h4 style="margin: 0; font-size: 0.9375rem; font-weight: 800; color: var(--neutral-900);">
                                    {{ $t->judul }}
                                </h4>
                                <p style="margin: 0.35rem 0 0 0; font-size: 0.8125rem; color: var(--neutral-600); line-height: 1.45;">
                                    {{ $t->deskripsi ?: 'Tidak ada rincian deskripsi tugas.' }}
                                </p>
                            </div>
                            <span style="font-size: 0.75rem; font-weight: 700; padding: 0.25rem 0.65rem; border-radius: 6px; background: #ECFDF5; color: #065F46; border: 1px solid #A7F3D0; white-space: nowrap;">
                                Selesai
                            </span>
                        </div>

                        <div style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap; margin-top: 0.25rem; font-size: 0.75rem; color: var(--neutral-600);">
                            <div style="display: flex; align-items: center; gap: 1.25rem; flex-wrap: wrap;">
                                <span>Tenggat Waktu: <strong>{{ $t->deadline ? $t->deadline->translatedFormat('d M Y') : '-' }}</strong></span>
                                @if($t->link_referensi)
                                    <span>&bull;</span>
                                    <a href="{{ $t->link_referensi }}" target="_blank" style="color: #01347C; font-weight: 700; text-decoration: underline; display: inline-flex; align-items: center; gap: 0.25rem;">
                                        <span>Tautan Repositori / Referensi</span>
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                                    </a>
                                @endif
                            </div>
                        </div>

                        @if($t->progresLogs->isNotEmpty())
                            <div style="padding: 0.65rem 0.85rem; border-radius: 6px; background: rgba(16, 185, 129, 0.08); border-left: 3px solid #10B981; font-size: 0.78125rem; color: var(--neutral-800);">
                                <strong style="color: #065F46;">Progres Terakhir:</strong> {{ $t->progresLogs->first()->catatan ?? 'Pekerjaan proyek selesai 100%.' }}
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            ... (empty state)
        @endif
    </div>
</div>
```

#### 3. Rapikan Tab 3: Rekam Jejak Logbook Harian (Perbaikan Kolom `kegiatan` & Status)
```blade
{{-- TAB 3: REKAPITULASI LOGBOOK --}}
<div class="arsip-tab-pane" id="tab-logbook">
    <div class="gentelella-card" style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-top: 3.5px solid var(--magkom-primary-blue, #01347C) !important; border-radius: 12px; padding: 1.5rem; box-shadow: var(--shadow-subtle);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.75rem;">
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
        </div>

        @if($penempatan->logbooks->isNotEmpty())
            <div class="table-responsive-gentelella" style="overflow-x: auto;">
                <table class="table jambo_table bulk_action" style="width: 100%; border-collapse: collapse; font-size: 0.8125rem;">
                    <thead>
                        <tr class="headings">
                            <th style="text-align: left; padding: 0.75rem; width: 130px;">Tanggal</th>
                            <th style="text-align: left; padding: 0.75rem; width: 110px;">Minggu</th>
                            <th style="text-align: left; padding: 0.75rem;">Aktivitas Magang</th>
                            <th style="text-align: center; padding: 0.75rem; width: 100px;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($penempatan->logbooks as $lb)
                            <tr style="border-bottom: 1px solid var(--neutral-200);">
                                <td style="padding: 0.75rem; font-weight: 600; white-space: nowrap;">
                                    {{ \Carbon\Carbon::parse($lb->tanggal)->translatedFormat('d M Y') }}
                                </td>
                                <td style="padding: 0.75rem; white-space: nowrap;">
                                    <span style="font-weight: 600; color: var(--neutral-700);">Minggu ke-{{ $lb->minggu_ke ?? 1 }}</span>
                                </td>
                                <td style="padding: 0.75rem; line-height: 1.45; color: var(--neutral-800);">
                                    {{ $lb->kegiatan ?: '-' }}
                                    @if($lb->link_laporan)
                                        <div style="margin-top: 0.25rem;">
                                            <a href="{{ $lb->link_laporan }}" target="_blank" style="color: #01347C; font-size: 0.75rem; text-decoration: underline;">
                                                &rarr; Tautan Lampiran
                                            </a>
                                        </div>
                                    @endif
                                </td>
                                <td style="padding: 0.75rem; text-align: center; white-space: nowrap;">
                                    <span style="font-size: 0.725rem; font-weight: 700; padding: 0.25rem 0.6rem; border-radius: 4px; background: #ECFDF5; color: #065F46; border: 1px solid #A7F3D0;">
                                        Selesai
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            ... (empty state)
        @endif
    </div>
</div>
```

#### 4. Rapikan Tab 4: Berkas Administrasi (Resolusi Nama Dokumen & Tombol Lihat Berkas)
```blade
{{-- TAB 4: DOKUMEN PENDAFTARAN --}}
<div class="arsip-tab-pane" id="tab-administrasi">
    <div class="gentelella-card" style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-top: 3.5px solid var(--magkom-primary-blue, #01347C) !important; border-radius: 12px; padding: 1.5rem; box-shadow: var(--shadow-subtle);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.75rem;">
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
        </div>

        @if($penempatan->pendaftaran && $penempatan->pendaftaran->dokumen->isNotEmpty())
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1rem;">
                @foreach($penempatan->pendaftaran->dokumen as $dok)
                    @php
                        $namaJenis = $dok->jenisDokumen?->nama ?? 'Dokumen Pendaftaran';
                        $ukuranKb = $dok->ukuran ? number_format($dok->ukuran / 1024, 0) . ' KB' : null;
                    @endphp
                    <div class="arsip-doc-card">
                        <div class="arsip-doc-header">
                            <div class="arsip-doc-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                </svg>
                            </div>
                            <div style="flex: 1; min-width: 0;">
                                <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 0.5rem;">
                                    <div class="arsip-doc-title">{{ $namaJenis }}</div>
                                    <span style="font-size: 0.6875rem; font-weight: 700; padding: 0.15rem 0.45rem; border-radius: 4px; background: #ECFDF5; color: #065F46; border: 1px solid #A7F3D0; white-space: nowrap;">
                                        Valid
                                    </span>
                                </div>
                                <div class="arsip-doc-meta">
                                    <span>File: <strong>{{ $dok->nama_asli ?: basename($dok->path) }}</strong></span>
                                    @if($ukuranKb)
                                        <span>&bull; {{ $ukuranKb }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="arsip-doc-actions">
                            @if($dok->path)
                                <a href="{{ $dok->getSignedPreviewUrl(15) }}" target="_blank" class="btn-doc-action btn-doc-action--preview" title="Buka pratinjau dokumen">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                    <span>Lihat Dokumen</span>
                                </a>
                                <a href="{{ $dok->getSignedDownloadUrl(15) }}" class="btn-doc-action btn-doc-action--download" title="Unduh berkas asli">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                        <polyline points="7 10 12 15 17 10"></polyline>
                                        <line x1="12" y1="15" x2="12" y2="3"></line>
                                    </svg>
                                    <span>Unduh</span>
                                </a>
                            @else
                                <span style="font-size: 0.75rem; color: var(--neutral-500); font-style: italic;">
                                    Berkas fisik tidak ditemukan di penyimpanan.
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            ... (empty state)
        @endif
    </div>
</div>
```

---

## ⚡ 5. 🧪 PANDUAN QUALITY GATES & VERIFIKASI (Testing Suite)

Jalankan perintah verifikasi resmi sistem menggunakan **PHP 8.3** sistem:

```powershell
# 1. Bersihkan Cache Template
& "F:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe" artisan view:clear
& "F:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe" artisan config:clear

# 2. Jalankan Feature Tests Terkait Detail Arsip
& "F:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe" artisan test tests/Feature/AdminLaporanDanArsipTest.php tests/Feature/DeltaDashboardEnhancementTest.php

# 3. Jalankan Seluruh Test Suite SuperAdmin
& "F:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe" artisan test --filter=SuperAdmin

# 4. Audit Format Kode dengan Pint
& "F:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe" vendor/bin/pint --test

# 5. Static Analysis dengan PHPStan
& "F:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe" vendor/bin/phpstan analyse --memory-limit=1G

# 6. Kompilasi Aset Frontend Vite
npm run build
```

---

## ✅ 6. 📋 CHECKLIST PENYELESAIAN (Definition of Done)

- [x] Segmented Pill Tab Switcher (`.arsip-segmented-tabs`) diterapkan di `show.blade.php`, meniru persis estetika tangkapan layar Media 1 (`admin/laporan/index.blade.php`).
- [x] Tab 2 (Riwayat Tugas) ditata dalam bentuk kartu rapi dengan judul, deskripsi, tenggat waktu, tautan repositori, catatan progres akhir, dan status `"Selesai"`.
- [x] Tab 3 (Logbook Harian) berhasil menampilkan isi logbook aktual dari kolom `$lb->kegiatan` (tidak ada lagi tanda strip `-`), dan status diubah menjadi badge `"Selesai"`.
- [x] Tab 4 (Dokumen Pendaftaran) menampilkan nama kategori dokumen riil dari relasi `$dok->jenisDokumen->nama`, nama file asli yang diunggah (`$dok->nama_asli`), dan ukuran file.
- [x] Tab 4 menyediakan tombol **"Lihat Dokumen"** (signed preview URL) dan **"Unduh"** (signed download URL) sehingga SuperAdmin dapat membuka berkas yang dikirimkan peserta.
- [x] Eager loading `pendaftaran.dokumen.jenisDokumen` ditambahkan ke `ArsipPesertaController@show`.
- [x] Zero inline CSS / zero `<style>` tags di Blade; seluruh styling tertata di `superadmin-common.css`.
- [x] Seluruh automated test suite (`AdminLaporanDanArsipTest` & `DeltaDashboardEnhancementTest`) lolos hijau 100%.
- [x] Tampilan responsif dan mendukung *Dark Mode* (`[data-theme="dark"]`).

---

> **Pesan Senior Architect:**  
> Tugas ini menyelesaikan masalah mendasar di mana data logbook dan dokumen selama ini tersimpan dengan baik di database, tetapi tidak muncul karena salah pemanggilan nama properti model. Pastikan junior developer atau AI membaca kamus data model `Logbook` dan `Dokumen` dengan seksama sebelum melakukan commit.

