# TASK BRIEF SENIOR DEVELOPER: ROADMAP REMEDIASI & PENYEMPURNAAN ARSITEKTUR SUPERADMIN MAGKOM (PRD & STEP-BY-STEP PLAYBOOK)

> **Dari:** Senior Software Architect / Lead Developer  
> **Kepada:** Junior Developer / AI Agent Pelaksana (Gemini 3.8 Flash / Coding Assistant)  
> **Subjek:** Eksekusi 4 Prioritas Hasil Audit Teknis Modul SuperAdmin  
> **Status Dokumen:** 🟢 **ROADMAP SELESAI & TERVERIFIKASI PENUH (ALL 4 PRIORITIES IMPLEMENTED & PASSING 100%)**  
> **Target Repositori:** MAGKOM (Portal Manajemen Magang Diskominfo Sleman)  
> **Baseline Stack:** Laravel 10.x | PHP 8.3.28 | Blade Views | Vanilla JS (ES Modules) | Vite 6.x | MySQL 8.0  
> **Dokumen Terkait:** `docs/plans/2026-11-09-senior-dev-task-audit-komprehensif-seluruh-fitur-superadmin-prd.md`, `.agents/rules/AGENTS.md`

---

## 🎯 INSTRUKSI AWAL DARI SENIOR DEVELOPER UNTUK JUNIOR / AI

Halo Junior Developer! Dokumen ini adalah **panduan eksekusi teknis tingkat lanjut (*step-by-step playbook*)** yang dirancang secara detail, presisi, dan bertahap agar kamu atau AI agent pelaksana tidak mengalami kebingungan (*zero ambiguity*) saat mengeksekusi 4 prioritas perbaikan hasil audit SuperAdmin kita.

### ⚠️ 4 HUKUM UTAMA YANG WAJIB KAMU PATUHI:
1. **Dilarang Merusak Kontrak Bisnis & Route Existing:** Jangan mengubah nama route, response HTTP, atau logika bisnis pendaftaran/kuota/audit trail yang sudah ada.
2. **Kepatuhan Aturan Blade & CSP:** Blade **HANYA** untuk presentasi markup HTML. Jangan pernah menambahkan `<script>` atau `<style>` inline baru di dalam Blade.
3. **Eksekusi Bertahap Per-Prioritas:** Kerjakan **Prioritas 1 dahulu**, verifikasi dengan build & test, baru lanjut ke Prioritas 2, 3, dan 4. Jangan mencampuradukkan perubahan.
4. **Verifikasi Nyata (Evidence Before Assertion):** Setiap selesai satu tahap, jalankan `./vendor/bin/pint --test`, `npm run build`, dan `php artisan test`. Jangan pernah berasumsi kode sudah benar tanpa bukti terminal yang hijau.

---

## 🗺️ IKHTISAR ROADMAP 4 PRIORITAS

```
┌────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│                                 ROADMAP REMEDIASI TEKNIS SUPERADMIN                                     │
├────────────────────────────────────────────────────────────────────────────────────────────────────────┤
│ [PRIORITAS 1: FUNGSIONALITAS]                                                                          │
│ Menghubungkan 4 aksi diagnostik server (Clear Cache, Optimize DB, Test Storage, Security Sweep) ke      │
│ antarmuka health/index.blade.php agar dapat diklik oleh SuperAdmin.                                    │
├────────────────────────────────────────────────────────────────────────────────────────────────────────┤
│ [PRIORITAS 2: STABILITAS UI & ANTI-CRASH]                                                              │
│ Mengeliminasi pemanggilan inline onclick="openModal...('{{ addslashes(...) }}')" pada Bidang,          │
│ Jurusan, dan Staf. Menggantinya dengan atribut HTML5 data-* dan event delegation anti-error newline.  │
├────────────────────────────────────────────────────────────────────────────────────────────────────────┤
│ [PRIORITAS 3: KEPATUHAN CSP & STANDAR VITE]                                                            │
│ Memindahkan tag <script> inline dari 8 file Blade ke modul JavaScript modular terpisah di              │
│ resources/js/pages/superadmin/ yang didaftarkan pada vite.config.js.                                   │
├────────────────────────────────────────────────────────────────────────────────────────────────────────┤
│ [PRIORITAS 4: REFACTORING ARSITEKTUR BACKEND]                                                          │
│ Memecah Fat Controller LandingCmsController (866 baris) menjadi Skinny Controller dengan membuat       │
│ LandingCmsService dan Form Request UpdateLandingCmsRequest terpisah.                                   │
└────────────────────────────────────────────────────────────────────────────────────────────────────────┘
```

---

# 🚀 PRIORITAS 1 (FUNGSIONALITAS): MENGHUBUNGKAN 4 AKSI DIAGNOSTIK SERVER PADA HEALTH MONITOR

### 1.1 Latar Belakang Masalah
Backend Laravel telah memiliki 4 controller method dan rute POST yang berfungsi penuh di [`HealthMonitorController.php`](file:///g:/MAGKOM/app/Http/Controllers/SuperAdmin/HealthMonitorController.php), namun antarmuka [`health/index.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/health/index.blade.php) saat ini hanya menampilkan grafik dan tidak memiliki tombol pemicu untuk aksi-aksi ini.

### 1.2 Spesifikasi 4 Aksi Diagnostik Backend
1. **Pembersihan Cache (`clear-cache`):**
   - Rute: `POST superadmin/health-monitor/clear-cache` (`superadmin.health.clear-cache`)
   - Fungsi: Menjalankan `Artisan::call('cache:clear')` dan `view:clear`, mencatat Audit Log.
2. **Optimasi Basis Data (`optimize-db`):**
   - Rute: `POST superadmin/health-monitor/optimize-db` (`superadmin.health.optimize-db`)
   - Fungsi: Menjalankan `VACUUM` (SQLite) atau `OPTIMIZE TABLE` (MySQL) dan prune metrik rute lama.
3. **Uji Penyimpanan Disk Storage (`test-storage`):**
   - Rute: `POST superadmin/health-monitor/test-storage` (`superadmin.health.test-storage`)
   - Fungsi: Menulis berkas probe sementara, membaca integritas payload, dan mengukurnya dalam milidetik.
4. **Security Sweep (`security-sweep`):**
   - Rute: `POST superadmin/health-monitor/security-sweep` (`superadmin.health.security-sweep`)
   - Fungsi: Memindai izin direktori kritis (.env, storage, bootstrap/cache) dan status APP_DEBUG.

### 1.3 Blueprint Desain Kartu Aksi Diagnostik di Antarmuka
Tambahkan komponen kartu panel aksi terstruktur (*Diagnostic Control Center*) pada `resources/views/superadmin/health/index.blade.php`, tepat di bawah 4 Kartu Tile-Stat (`.tiles-grid`):

```
┌────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│ [PUSAT TINDAKAN & DIAGNOSTIK INFRASTRUKTUR SISTEM]                                                    │
│ Operasi pemeliharaan berkala, optimasi performa basis data, dan validasi integritas keamanan server. │
├───────────────────────────────┬───────────────────────────────┬────────────────────────────────────────┤
│ [⚡ Bersihkan Cache]          │ [🗄️ Optimasi Basis Data]      │ [💾 Uji Tulis Storage]                 │
│ Form POST + @csrf             │ Form POST + @csrf             │ Form POST + @csrf                      │
│ Route: health.clear-cache     │ Route: health.optimize-db     │ Route: health.test-storage             │
│ Tombol: "Clear Cache Sistem"  │ Tombol: "Jalankan Optimasi"   │ Tombol: "Jalankan Uji I/O"             │
├───────────────────────────────┴───────────────────────────────┴────────────────────────────────────────┤
│ [🛡️ Pindai Keamanan Server (Security Sweep)]                                                          │
│ Form POST + @csrf | Route: health.security-sweep | Tombol: "Mulai Security Sweep"                     │
└────────────────────────────────────────────────────────────────────────────────────────────────────────┘
```

### 1.4 Langkah Pengerjaan Detail (Checklist Junior)
- [x] Buka berkas `resources/views/superadmin/health/index.blade.php`.
- [x] Pastikan terdapat penanganan alert flash message di bagian atas halaman untuk menampilkan notifikasi hasil aksi:
  ```blade
  @if(session('status_keberhasilan'))
      <div class="sa-alert sa-alert-success" style="margin-bottom: 1.25rem;">
          <x-sa.icon name="check-circle" :size="18" />
          <span>{{ session('status_keberhasilan') }}</span>
      </div>
  @endif
  @if(session('status_kesalahan'))
      <div class="sa-alert sa-alert-danger" style="margin-bottom: 1.25rem;">
          <x-sa.icon name="alert-triangle" :size="18" />
          <span>{{ session('status_kesalahan') }}</span>
      </div>
  @endif
  ```
- [x] Buat panel kartu aksi dengan 4 form mandiri berstandar Gentelella (`x-panel` atau `.sa-card`).
- [x] Setiap form wajib menyertakan:
  1. `<form action="{{ route('superadmin.health.<nama-rute>') }}" method="POST">`
  2. Direktif `@csrf`
  3. Konfirmasi aksi pada tombol submit (misal: `onsubmit="return confirm('Jalankan pembersihan cache?')"` atau modal dialog konfirmasi).
- [x] Verifikasi dengan menjalankan tes otomatis:
  ```powershell
  php artisan test --filter=SuperAdminMasterArchitectureTest
  ```
  *Pastikan test `superadmin can execute all four diagnostic actions successfully` tetap 100% passed.*

---

# 🛡️ PRIORITAS 2 (STABILITAS UI): ELIMINASI `addslashes` & MIGRASI KE HTML5 DATA ATTRIBUTES

### 2.1 Latar Belakang Masalah
Di file Blade `bidang`, `jurusan`, dan `staf`, tombol Edit memanggil fungsi JavaScript melalui parameter inline string:
```blade
onclick="openModalEditBidang('{{ $bidang->id }}', '{{ addslashes($bidang->nama_bidang) }}', '{{ addslashes($bidang->deskripsi ?? '') }}')"
```
**Akar Masalah (Bug Mekanisme):**  
`addslashes()` di PHP **TIDAK PERNAH** meng-escape karakter newline (`\n` atau `\r`). Jika deskripsi bidang atau biodata staf diisi lebih dari 1 baris di textarea, browser akan membaca newline di dalam string literal JavaScript `'...'`, yang langsung memicu:
`Uncaught SyntaxError: Invalid or unexpected token`
Akibatnya: Tombol Edit macet dan dialog modal tidak pernah terbuka!

### 2.2 Target Berkas yang Wajib Direfaktor
1. [`resources/views/superadmin/bidang/index.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/bidang/index.blade.php) (baris 178)
2. [`resources/views/superadmin/jurusan/index.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/jurusan/index.blade.php) (baris 182)
3. [`resources/views/superadmin/staf/index.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/staf/index.blade.php) (baris 258, 264, 273)

### 2.3 Standar Perubahan (Solusi Senior Architect)
Ganti seluruh passing parameter inline dengan atribut HTML5 `data-*` yang aman dan otomatis di-encode oleh Blade engine (`{{ ... }}`):

#### Pola Baru pada Blade View (`bidang/index.blade.php`):
```blade
{{-- SEBELUM (BERBAHAYA & RENTAN CRASH): --}}
<button type="button" onclick="openModalEditBidang('{{ $bidang->id }}', '{{ addslashes($bidang->nama_bidang) }}', '{{ addslashes($bidang->deskripsi ?? '') }}')" class="sa-btn-action-outline">

{{-- SESUDAH (STANDAR INDUSTRI & 100% AMAN): --}}
<button type="button" 
        class="sa-btn-action-outline btn-trigger-edit-bidang"
        data-id="{{ $bidang->id }}"
        data-nama="{{ $bidang->nama_bidang }}"
        data-deskripsi="{{ $bidang->deskripsi }}"
        data-action-url="{{ route('superadmin.bidang.update', $bidang) }}">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
    Edit
</button>
```

#### Pola Penanganan di JavaScript (`resources/js/pages/superadmin/bidang-index.js`):
Gunakan *Event Delegation* pada tabel/dokumen:
```javascript
document.addEventListener('click', (event) => {
    const editBtn = event.target.closest('.btn-trigger-edit-bidang');
    if (!editBtn) return;

    event.preventDefault();
    const id = editBtn.dataset.id;
    const nama = editBtn.dataset.nama || '';
    const deskripsi = editBtn.dataset.deskripsi || '';
    const actionUrl = editBtn.dataset.actionUrl || `/superadmin/bidang/${id}`;

    const modal = document.getElementById('modalEditBidang');
    const inputNama = document.getElementById('editNamaBidang');
    const inputDeskripsi = document.getElementById('editDeskripsiBidang');
    const form = document.getElementById('formEditBidang');

    if (inputNama) inputNama.value = nama;
    if (inputDeskripsi) inputDeskripsi.value = deskripsi;
    if (form) form.action = actionUrl;
    if (modal) modal.style.display = 'flex';
});
```

### 2.4 Langkah Pengerjaan Detail (Checklist Junior)
- [x] Ubah markup tombol Edit di `bidang/index.blade.php`: Hapus atribut `onclick`, pasang class `btn-trigger-edit-bidang` dan atribut `data-id`, `data-nama`, `data-deskripsi`, `data-action-url`.
- [x] Lakukan hal identik pada `jurusan/index.blade.php`: Pasang class `btn-trigger-edit-jurusan` dan atribut `data-id`, `data-nama`, `data-kategori`.
- [x] Lakukan hal identik pada `staf/index.blade.php`:
  - Tombol Edit Staf: `data-id`, `data-nama`, `data-email`, `data-role`, `data-bidang-id`.
  - Tombol Reset Sandi: `data-id`, `data-nama`.
  - Tombol Buka Modal Tambah: Ganti `onclick="document.getElementById(...).style.display='flex'"` dengan class trigger `btn-trigger-tambah-staf`.
- [x] Verifikasi: Isi data dengan teks panjang yang memuat Enter / newline, lalu klik tombol Edit. Pastikan modal terbuka dengan mulus tanpa error di console browser.

---

# 📦 PRIORITAS 3 (KEPATUHAN CSP & VITE): EKSTRAKSI TAG `<script>` INLINE KE MODUL JS

### 3.1 Latar Belakang Masalah
Terdapat 8 file Blade SuperAdmin yang masih memuat script inline `<script>` di dalam file Blade. Ini melanggar aturan arsitektur repositori dan berisiko diblokir oleh header Content Security Policy (CSP).

### 3.2 Pemetaan 8 Berkas Blade ke 8 Modul JavaScript Baru

| No | Berkas Blade Asal | Berkas Modul JS Target (Baru) | Isi Logika yang Dipindahkan |
| :---: | :--- | :--- | :--- |
| **1** | `resources/views/superadmin/akun/index.blade.php` | `resources/js/pages/superadmin/akun-index.js` | Fungsi toggle visibility password & pemilihan kartu tema tampilan. |
| **2** | `resources/views/superadmin/arsip/show.blade.php` | `resources/js/pages/superadmin/arsip-show.js` | Fungsi `switchArsipTab()` untuk beralih tab segmented pill. |
| **3** | `resources/views/superadmin/bidang/index.blade.php` | `resources/js/pages/superadmin/bidang-index.js` | Modal tambah bidang, modal edit bidang, dan backdrop handler. |
| **4** | `resources/views/superadmin/bulan/index.blade.php` | `resources/js/pages/superadmin/bulan-index.js` | Modal buka periode magang & modal edit periode. |
| **5** | `resources/views/superadmin/health/index.blade.php` | `resources/js/pages/superadmin/health-monitor.js` | Seluruh rendering Canvas 2D latensi, filter interval, dan live chart. |
| **6** | `resources/views/superadmin/jurusan/index.blade.php` | `resources/js/pages/superadmin/jurusan-index.js` | Modal tambah jurusan & modal edit jurusan. |
| **7** | `resources/views/superadmin/profil/show.blade.php` | `resources/js/pages/superadmin/profil-show.js` | Tab controller profil eksekutif SuperAdmin. |
| **8** | `resources/views/superadmin/staf/index.blade.php` | `resources/js/pages/superadmin/staf-index.js` | Modal tambah staf, modal edit staf, modal reset kata sandi staf. |

### 3.3 Konfigurasi `vite.config.js`
Setelah membuat berkas JavaScript baru di `resources/js/pages/superadmin/`, daftarkan seluruh berkas tersebut pada array `input` di `vite.config.js`:
```javascript
laravel({
    input: [
        // Entry points existing...
        'resources/js/pages/superadmin/akun-index.js',
        'resources/js/pages/superadmin/arsip-show.js',
        'resources/js/pages/superadmin/bidang-index.js',
        'resources/js/pages/superadmin/bulan-index.js',
        'resources/js/pages/superadmin/health-monitor.js',
        'resources/js/pages/superadmin/jurusan-index.js',
        'resources/js/pages/superadmin/profil-show.js',
        'resources/js/pages/superadmin/staf-index.js',
    ],
    refresh: true,
}),
```

### 3.4 Pemuatan pada File Blade
Pada setiap file Blade terkait, hapus tag `<script>...</script>` inline dan muat file JS terkompilasi melalui `@vite`:
```blade
@push('scripts')
    @vite(['resources/js/pages/superadmin/bidang-index.js'])
@endpush
```

### 3.5 Langkah Pengerjaan Detail (Checklist Junior)
- [x] Buat berkas JavaScript satu per satu di direktori `resources/js/pages/superadmin/`.
- [x] Pindahkan isi fungsi JavaScript dari Blade ke berkas JS tersebut.
- [x] Bungkus logika dalam `document.addEventListener('DOMContentLoaded', () => { ... });`.
- [x] Daftarkan pada `vite.config.js`.
- [x] Ganti tag `<script>` inline di Blade dengan `@push('scripts') @vite(...) @endpush`.
- [x] Jalankan kompilasi aset: `npm run build` dan pastikan build berhasil 100% tanpa error.

---

# 🏗️ PRIORITAS 4 (REFACTORING ARSITEKTUR): MEMECAH FAT CONTROLLER CMS MENJADI SERVICE LAYER

### 4.1 Latar Belakang Masalah
`LandingCmsController.php` saat ini mencapai **866 baris kode**, dengan satu method `update()` sepanjang hampir 500 baris. Ini melanggar prinsip *Single Responsibility Principle (SRP)* dan membuat kode sulit diuji secara terisolasi.

### 4.2 Blueprint Arsitektur Bersih (Clean Architecture)

```
[REQUEST PENGGUNA]
       │
       ▼
[UpdateLandingCmsRequest] ──► (Validasi aturan header_seo, hero, faq, kontak, dll)
       │
       ▼
[LandingCmsController] (Skinny: ~120 Baris)
       │
       ├─► Memanggil LandingCmsService->updateSection($section, $validatedData, $uploadedFiles)
       │
       ▼
[LandingCmsService] (Domain Business Logic)
       │
       ├─► Manipulasi Storage File / Gambar
       ├─► JSON Encoding & Sanitasi Konten
       ├─► Manajemen Draft & Publish Status
       └─► Audit Trail Logging (AuditService)
```

### 4.3 Spesifikasi Berkas yang Dibuat & Dirombak
1. **Buat Service Baru: `app/Services/LandingCmsService.php`**
   - Method yang dipindahkan dari controller:
     - `getCmsData(): array` (pembacaan konten publik + draft preview)
     - `updateSection(string $section, array $validated, ?UploadedFile $file = null): void`
     - `publishDraft(User $user): void`
     - `discardDraft(User $user): void`
     - `deleteFoto(string $key, User $user): void`
     - `deletePanduanPdf(User $user): void`
2. **Buat Form Request Baru: `app/Http/Requests/SuperAdmin/UpdateLandingCmsRequest.php`**
   - Memindahkan seluruh rules validasi dari method `update()` ke method `rules()` berbasis switch-case `$this->input('section')`.
3. **Rampingkan `LandingCmsController.php`:**
   - Inject `LandingCmsService` pada constructor.
   - Method `update()` cukup memanggil:
     ```php
     public function update(UpdateLandingCmsRequest $request): RedirectResponse
     {
         $this->cmsService->updateSection(
             section: $request->validated('section'),
             data: $request->validated(),
             user: $request->user(),
             files: $request->allFiles()
         );

         return redirect()->route('superadmin.landing.index', ['tab' => $request->input('section')])
             ->with('success', 'Perubahan konten berhasil disimpan sebagai draf.');
     }
     ```

### 4.4 Langkah Pengerjaan Detail (Checklist Junior)
- [x] Buat berkas `app/Services/LandingCmsService.php` dengan `declare(strict_types=1);`.
- [x] Buat berkas `app/Http/Requests/SuperAdmin/UpdateLandingCmsRequest.php`.
- [x] Pindahkan logika penyimpanan file dan update draft dari controller ke service.
- [x] Sederhanakan method-method di `LandingCmsController.php` hingga controller menjadi ramping (< 190 baris).
- [x] Jalankan pengujian statis dan pengujian unit/fitur:
  ```powershell
  ./vendor/bin/phpstan analyse app/Http/Controllers/SuperAdmin app/Services/LandingCmsService.php --memory-limit=1G
  php artisan test --filter=LandingCms
  ```
  *Pastikan seluruh test CMS tetap hijau (passing 100%).*

---

## 🚦 QUALITY GATES & VERIFIKASI AKHIR ROADMAP

Setelah seluruh langkah pada Prioritas 1 s/d 4 diselesaikan oleh Junior Developer / AI, wajib menjalankan 4 gerbang kualitas berikut sebelum pekerjaan dinyatakan selesai:

1. **Linter Formatting (Laravel Pint):**
   ```powershell
   & "F:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe" vendor/bin/pint --test
   ```
   *Ekspektasi: 0 formatting violation.*

2. **Static Analysis (PHPStan):**
   ```powershell
   & "F:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe" vendor/bin/phpstan analyse app/Http/Controllers/SuperAdmin app/Services --memory-limit=1G
   ```
   *Ekspektasi: `[OK] No errors`.*

3. **Asset Pipeline Build (Vite):**
   ```powershell
   npm run build
   ```
   *Ekspektasi: Build berhasil dengan status exit code 0 dan manifest terupdate.*

4. **Automated Feature Test Suite (PHPUnit / Pest):**
   ```powershell
   & "F:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe" artisan test --filter=SuperAdmin
   ```
   *Ekspektasi: Seluruh 231 test SuperAdmin tetap lolos (100% green).*

---

## ✅ KRITERIA SELESAI (DEFINITION OF DONE)

Tugas ini dinyatakan selesai sepenuhnya apabila:
- [x] Empat tombol diagnostik server pada `health/index.blade.php` berhasil terhubung ke backend dan dapat dieksekusi dengan feedback alert yang jelas.
- [x] Tidak ada lagi pemanggilan `addslashes` pada atribut `onclick` di `bidang`, `jurusan`, dan `staf` (seluruhnya telah bermigrasi ke `data-*` attributes).
- [x] Delapan berkas Blade SuperAdmin bebas dari tag `<script>` inline dan telah menggunakan modul JS mandiri yang dikompilasi oleh Vite.
- [x] `LandingCmsController` telah menjadi *Skinny Controller* (< 190 baris) dengan logika domain yang terisolasi rapi di `LandingCmsService`.
- [x] Seluruh perintah Quality Gate (Pint, PHPStan, Vite Build, dan PHPUnit Test) berhasil 100% tanpa ada regresi.

