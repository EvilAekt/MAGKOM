# 📋 SENIOR DEVELOPER TECHNICAL TASK & PRD
## Task: Implementasi Pilihan Tipe Pendaftaran & Roster Anggota Tim Dinamis (Maksimal N Orang Sesuai Pengaturan Sistem)

- **Author**: Lead Senior Software Architect (Antigravity Architecture Team)
- **Target Assignee**: Junior Fullstack Developer / Autonomous AI Agent (Gemini 3.8 Flash)
- **Dokumen Referensi Bukti**: 
  - `media_1794891880588_d97ecce7.png` (Tampilan Tipe Pendaftaran, Form Nama Kelompok, dan Kartu Daftar Rekan Anggota Tim)
- **Target File yang Perlu Dimodifikasi**:
  1. [`app/Http/Controllers/Auth/AuthController.php`](file:///g:/MAGKOM/app/Http/Controllers/Auth/AuthController.php) (Injeksi Variabel Pengaturan Sistem ke View)
  2. [`resources/views/auth/register.blade.php`](file:///g:/MAGKOM/resources/views/auth/register.blade.php) (Markup Kontrol Tambah/Hapus Anggota & Dinamisasi Batas)
  3. [`resources/css/pages/auth/auth-depth.css`](file:///g:/MAGKOM/resources/css/pages/auth/auth-depth.css) (Styling Tombol Tambah, Tombol Hapus, dan Kartu Anggota)
  4. [`resources/js/pages/auth/auth-depth.js`](file:///g:/MAGKOM/resources/js/pages/auth/auth-depth.js) (Logika Dynamic Roster Manager, Re-indexing, dan Limit State)
  5. [`tests/Feature/PendaftaranKelompokTest.php`](file:///g:/MAGKOM/tests/Feature/PendaftaranKelompokTest.php) (Unit/Feature Test Verifikasi Alur Dinamis)
- **Status Dokumen**: ⏳ **DRAFT PLAN (Menunggu Persetujuan User Sebelum Eksekusi)**
- **Tanggal**: 17 November 2026
- **Klasifikasi**: New Feature & UX Precision Enhancement (Pendaftaran Kelompok Dinamis)

---

## 🎯 1. Ringkasan Eksekutif (TL;DR untuk Junior Developer / AI)

Dokumen ini adalah **Petunjuk Teknis & Rencana Kerja (Senior Developer Task Plan)** terperinci untuk membuat fitur **Pilihan Tipe Pendaftaran (Individu vs Kelompok)** beserta **Kartu Roster Rekan Anggota Tim yang Dinamis** pada halaman pendaftaran calon peserta magang (`/register`).

Berdasarkan referensi desain pada `media_1794891880588_d97ecce7.png`:
1. Pengguna dapat memilih antara **Individu (1 Orang)** atau **Kelompok (2–N Orang)**.
2. Ketika memilih **Kelompok**, muncul kartu container **Data Tim & Rombongan Magang**:
   - Field **Nama Kelompok** (misal: "Tim Inovasi Smart Sleman").
   - Sub-bagian **Daftar Rekan Anggota Tim** (dengan status "Anda mendaftar sebagai Ketua Tim").
   - Kartu pertama (**Rekan Anggota #1**) berisi 4 field: `Nama Lengkap`, `NIM / NISN`, `No. WhatsApp`, `Email Aktif`.
3. **Kebutuhan Inti**: Pengguna dapat **menambah rekan anggota** secara interaktif dengan tombol **"+ Tambah Rekan Anggota"** hingga batas maksimal yang ditentukan oleh sistem (`Pengaturan::int('maks_anggota_kelompok', 4)`).
4. Setiap kartu anggota tambahan memiliki tombol **"Hapus"** untuk membatalkan rekan tersebut (minimal 1 rekan anggota tersisa, karena kelompok = 1 ketua + min 1 rekan).
5. Semua angka batas (2 s.d. 4 atau N) **TIDAK BOLEH di-hardcode**, melainkan mengambil nilai riil dari tabel `pengaturan` di database.

---

## 🔬 2. Analisis Kondisi Eksisting vs Target (Gap Analysis)

| Aspek | Kondisi Saat Ini | Target Perbaikan (PRD Ini) |
| :--- | :--- | :--- |
| **Batas Anggota** | Teks di-hardcode `(2–4 Orang)` dan `(Maks. 4)` di file Blade. | Mengambil nilai dinamis dari sistem: `Pengaturan::int('maks_anggota_kelompok', 4)`. |
| **Tombol Tambah Anggota** | Tidak ada tombol interaktif. Anggota hanya bertambah jika ada query URL `?jumlah=...` dari beranda. | Tersedia tombol interaktif **"+ Tambah Rekan Anggota"** langsung di form pendaftaran. |
| **Tombol Hapus Anggota** | Tidak ada tombol hapus pada kartu anggota. | Ada tombol **"Hapus"** (ikon tempat sampah / silang) pada kartu anggota #2 ke atas. |
| **Validasi & Re-index** | Jika input disembunyikan/dihapus, atribut `name="anggota[i]"` tidak ter-reindex otomatis. | Re-indexing otomatis pada sisi JavaScript (`anggota[0]`, `anggota[1]`) agar payload array ke backend tetap rapi tanpa indeks bolong. |
| **State Limit Reached** | Tidak ada feedback saat mencapai batas maksimal rombongan. | Tombol tambah di-disable dan muncul badge informatif *"Batas maksimal N anggota tim telah tercapai"*. |
| **Preservasi `old()` Form** | Saat validasi backend gagal, jumlah kartu anggota yang sempat diisi bisa kembali ke default. | Menghitung jumlah `old('anggota')` di Blade untuk mempertahankan kartu yang sudah sempat diisi peserta. |

---

## 🏛️ 3. Evaluasi Opsi Arsitektur Teknis

### Opsi A: Progressive Enhancement via Pre-rendered Multi-Card Slots (Direkomendasikan)
- **Cara Kerja**: Server Blade merender slot kartu sejumlah `maksAnggotaTambahan` (`maks_anggota_kelompok - 1`). JavaScript mengatur visibilitas (`display: flex/none`), mengaktifkan/menonaktifkan attribute `disabled`, serta melakukan penomoran ulang label dan atribut `name`.
- **Kelebihan**: 
  - Sangat cepat dan tidak membutuhkan runtime templating kompleks.
  - Nilai `old('anggota.*')` dari Laravel langsung otomatis terisi di HTML jika ada error validasi.
  - Bebas risiko XSS dari manipulasi string DOM.
- **Kekurangan**: Membutuhkan loop hingga batas maksimal di Blade (hanya 3-5 iterasi, overhead 0.01ms).

### Opsi B: Pure Client-Side Dynamic DOM Template Injection
- **Cara Kerja**: Blade hanya merender 1 elemen `<template id="tmpl-kartu-anggota">`. JavaScript membuat node DOM baru via `cloneNode(true)` dan memasukkannya ke container saat tombol tambah diklik.
- **Kelebihan**: DOM awal sangat bersih.
- **Kekurangan**: Lebih rumit untuk me-restore nilai `old()` saat validasi form gagal, memerlukan logika replikasi nilai old dari JSON session ke DOM input secara manual.

> **Keputusan Arsitektur**: **Pilih Opsi A (Progressive Enhancement)** karena menjamin keandalan form submission Laravel, 100% aman dari XSS, dan menjaga konsistensi data saat terjadi validasi error pada captcha atau password.

---

## 📁 4. Rincian Perubahan File (File Impact Table)

| File | Aksi | Ringkasan Perubahan |
| :--- | :---: | :--- |
| `app/Http/Controllers/Auth/AuthController.php` | **Modify** | Mengambil `maks_anggota_kelompok` & `fitur_pendaftaran_kelompok_aktif` dari `Pengaturan` dan mengirimkannya ke view `auth.register`. |
| `resources/views/auth/register.blade.php` | **Modify** | Mengganti label statis dengan variabel `$maksAnggotaKelompok`, menambahkan tombol "+ Tambah Rekan Anggota", menambahkan tombol hapus pada kartu anggota, dan sinkronisasi data attribute. |
| `resources/css/pages/auth/auth-depth.css` | **Modify** | Menambahkan class CSS untuk tombol tambah anggota (`.btn-add-anggota`), tombol hapus kartu (`.btn-remove-anggota`), dan badge limit (`.anggota-limit-badge`). |
| `resources/js/pages/auth/auth-depth.js` | **Modify** | Menambahkan modul interaktif `initGroupRosterManager` untuk mengelola penambahan/penghapusan kartu, re-indexing atribut input, dan update hidden input `redirect_jumlah`. |
| `tests/Feature/PendaftaranKelompokTest.php` | **Modify** | Menambahkan pengujian registrasi kelompok dinamis dengan batas dari sistem `Pengaturan`. |

---

## 📋 5. Langkah-Langkah Pengerjaan Bertahap (Senior Dev Tasks)

### Task 1: Backend Controller Data Provisioning
**File**: [`app/Http/Controllers/Auth/AuthController.php`](file:///g:/MAGKOM/app/Http/Controllers/Auth/AuthController.php)
- [x] Buka method `showRegister(Request $request)`.
- [x] Ambil nilai pengaturan sistem:
  ```php
  $maksAnggotaKelompok = \App\Models\Pengaturan::int('maks_anggota_kelompok', 4);
  $fiturKelompokAktif = \App\Models\Pengaturan::bool('fitur_pendaftaran_kelompok_aktif', true);
  ```
- [x] Kirim `$maksAnggotaKelompok` dan `$fiturKelompokAktif` ke dalam array `compact(...)` pada `view('auth.register', ...)`.
- [x] Pada method `register(Request $request)`, pastikan validasi `$maxAnggotaTotal = Pengaturan::int('maks_anggota_kelompok', 4);` tetap sinkron dan tidak ada hardcoded angka 4.

---

### Task 2: Blade Markup Structure & Accessible Controls
**File**: [`resources/views/auth/register.blade.php`](file:///g:/MAGKOM/resources/views/auth/register.blade.php)
- [x] Di bagian atas Blade, definisikan fallback aman:
  ```blade
  $maksAnggota = $maksAnggotaKelompok ?? 4;
  $maksRekanTambahan = max(1, $maksAnggota - 1);
  ```
- [x] Ubah label tombol toggle Kelompok (baris ~97):
  ```blade
  Kelompok (2–{{ $maksAnggota }} Orang)
  ```
- [x] Ubah teks header Data Tim (baris ~106):
  ```blade
  Ketua + Anggota (Maks. {{ $maksAnggota }})
  ```
- [x] Perbarui inisialisasi `$anggotaCount` agar menghormati `old('anggota')`:
  ```blade
  $oldAnggotaCount = is_array(old('anggota')) ? count(old('anggota')) : 0;
  $anggotaCount = max(1, min($maksRekanTambahan, $oldAnggotaCount ?: ($initialJumlah - 1)));
  ```
- [x] Konfigurasi container `#roster-anggota-container`:
  ```blade
  <div id="roster-anggota-container" 
       data-target-anggota="{{ $anggotaCount }}"
       data-max-rekan="{{ $maksRekanTambahan }}"
       data-min-rekan="1"
       data-max-total="{{ $maksAnggota }}"
       style="display: flex; flex-direction: column; gap: 10px">
  ```
- [x] Buat loop kartu anggota hingga `$maksRekanTambahan`:
  - Di setiap kartu `card-anggota-item`, tambahkan header kartu yang memuat:
    - Label judul: `<span class="card-anggota-title">Rekan Anggota #{{ $i + 1 }}</span>`
    - Tombol hapus: 
      ```blade
      <button type="button" class="btn-remove-anggota" data-action="remove-anggota" title="Hapus rekan ini" aria-label="Hapus rekan anggota ini" style="{{ $i === 0 ? 'display: none;' : '' }}">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
      </button>
      ```
- [x] Di bawah kontainer kartu anggota, tambahkan wadah kontrol aksi penambahan:
  ```blade
  {{-- Kontrol Aksi Tambah Anggota --}}
  <div class="roster-actions-wrap" style="display: flex; flex-direction: column; gap: 8px; margin-top: 4px;">
    <button type="button" id="btn-add-anggota" class="btn-add-anggota" style="display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%; height: 42px; border: 1.5px dashed #003566; border-radius: 9px; background: rgba(0, 53, 102, 0.04); color: #003566; font-size: 13.5px; font-weight: 700; cursor: pointer; transition: all 0.2s ease;">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
      <span>Tambah Rekan Anggota</span>
    </button>
    <div id="roster-max-hint" style="display: none; padding: 8px 12px; border-radius: 8px; background: #EEF2F6; border: 1px solid #CBD5E1; color: #475569; font-size: 12px; font-weight: 600; text-align: center;">
      Maksimal {{ $maksAnggota }} anggota tim (1 Ketua + {{ $maksRekanTambahan }} Rekan) telah tercapai.
    </div>
  </div>
  ```

---

### Task 3: CSS Visual Polish & Design System Tokens
**File**: [`resources/css/pages/auth/auth-depth.css`](file:///g:/MAGKOM/resources/css/pages/auth/auth-depth.css)
- [x] Tambahkan style interaktif untuk tombol tambah:
  ```css
  .btn-add-anggota {
    transition: background-color 0.2s ease, border-color 0.2s ease, transform 0.1s ease;
  }
  .btn-add-anggota:hover:not(:disabled) {
    background: rgba(0, 53, 102, 0.08) !important;
    border-color: #002244 !important;
  }
  .btn-add-anggota:active:not(:disabled) {
    transform: scale(0.99);
  }
  .btn-add-anggota:disabled {
    opacity: 0.5;
    cursor: not-allowed !important;
    border-style: solid !important;
    border-color: #CBD5E1 !important;
    background: #F1F5F9 !important;
    color: #94A3B8 !important;
  }
  ```
- [x] Tambahkan style untuk tombol hapus anggota:
  ```css
  .card-anggota-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
  }
  .btn-remove-anggota {
    background: transparent;
    border: none;
    color: #94A3B8;
    cursor: pointer;
    padding: 4px;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.15s ease;
  }
  .btn-remove-anggota:hover {
    color: #DC2626;
    background: #FEE2E2;
  }
  ```
- [x] Tambahkan animasi fade-in halus saat kartu baru muncul:
  ```css
  .card-anggota-item.is-entering {
    animation: fadeInSlideDown 0.25s cubic-bezier(0.16, 1, 0.3, 1) forwards;
  }
  @keyframes fadeInSlideDown {
    from {
      opacity: 0;
      transform: translateY(-8px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }
  ```

---

### Task 4: JavaScript Event Handling & State Synchronization
**File**: [`resources/js/pages/auth/auth-depth.js`](file:///g:/MAGKOM/resources/js/pages/auth/auth-depth.js)
- [x] Buat fungsi modular `initGroupRosterManager()` di dalam `auth-depth.js` dan panggil pada inisialisasi dokumen.
- [x] Logika yang di-handle oleh manager:
  1. **Baca batas konfigurasi**:
     - `maxRekan = parseInt(rosterContainer.getAttribute('data-max-rekan') || '3', 10)`
     - `minRekan = parseInt(rosterContainer.getAttribute('data-min-rekan') || '1', 10)`
     - `maxTotal = parseInt(rosterContainer.getAttribute('data-max-total') || '4', 10)`
  2. **Fungsi `updateRosterState()`**:
     - Hitung berapa kartu yang sedang aktif (`isVis = card.style.display !== 'none'`).
     - Loop seluruh kartu yang aktif:
       - Update judul label menjadi: `Rekan Anggota #${index + 1}`.
       - Update atribut input: `name="anggota[${index}][nama]"`, `name="anggota[${index}][nim_nisn]"`, dst.
       - Tampilkan tombol hapus hanya jika kartu aktif > 1 (kartu pertama tidak dapat dihapus jika hanya tersisa 1 rekan).
       - Pastikan input pada kartu aktif berstatus `disabled = false`, dan kartu yang tidak aktif berstatus `disabled = true`.
     - Update hidden input `redirect_jumlah` dan `jumlah_kelompok` menjadi `jumlahAktif + 1` (karena 1 orang adalah Ketua Tim).
     - Jika `jumlahAktif >= maxRekan`:
       - Disable tombol `#btn-add-anggota` (`disabled = true`).
       - Tampilkan `#roster-max-hint` (`style.display = 'block'`).
     - Jika `jumlahAktif < maxRekan`:
       - Enable tombol `#btn-add-anggota` (`disabled = false`).
       - Sembunyikan `#roster-max-hint` (`style.display = 'none'`).
     - Picu event kustom `roster:changed` untuk memperbarui live quota indicator jika ada.
  3. **Event Listener Tambah Anggota**:
     - Saat `#btn-add-anggota` diklik:
       - Cari kartu pertama yang saat ini tersembunyi (`style.display === 'none'`).
       - Tampilkan kartu tersebut (`style.display = 'flex'`), tambahkan class `is-entering`.
       - Panggil `updateRosterState()`.
       - Berikan fokus otomatis ke input `nama` pada kartu yang baru dibuka untuk kenyamanan pengguna (*accessibility*).
  4. **Event Listener Hapus Anggota (Event Delegation)**:
     - Gunakan event delegation pada `#roster-anggota-container`:
       ```js
       rosterContainer.addEventListener('click', (e) => {
         const removeBtn = e.target.closest('[data-action="remove-anggota"]');
         if (!removeBtn) return;
         const card = removeBtn.closest('.card-anggota-item');
         if (card) {
           // Kosongkan seluruh input pada kartu ini
           card.querySelectorAll('input').forEach(inp => { inp.value = ''; });
           card.style.display = 'none';
           // Susun ulang posisi kartu agar kartu aktif selalu di atas
           rosterContainer.appendChild(card);
           updateRosterState();
         }
       });
       ```

---

### Task 5: Automated Testing & Verification
**File**: [`tests/Feature/PendaftaranKelompokTest.php`](file:///g:/MAGKOM/tests/Feature/PendaftaranKelompokTest.php)
- [x] Tambahkan skenario pengujian:
  1. `test_registrasi_kelompok_sukses_dengan_maksimal_anggota_dari_pengaturan_sistem()`:
     - Ubah `Pengaturan::setNilai('maks_anggota_kelompok', '3');`
     - Kirim pendaftaran dengan 2 rekan (total 3 orang) -> Berhasil.
  2. `test_registrasi_kelompok_ditolak_jika_melebihi_maksimal_anggota_dari_pengaturan_sistem()`:
     - Kirim pendaftaran dengan 3 rekan (total 4 orang) -> Ditolak validasi backend (`max:2` rekan).
  3. `test_halaman_register_merender_batas_maksimal_sesuai_pengaturan_sistem()`:
     - `Pengaturan::setNilai('maks_anggota_kelompok', '5');`
     - Akses `GET /register?tipe=kelompok`
     - Pastikan respons view memuat teks `Kelompok (2–5 Orang)` dan data attribute `data-max-rekan="4"`.

---

### Task 6: Quality Gate Verification
Jalankan perintah verifikasi wajib sebelum pekerjaan dinyatakan selesai:
- [x] Test Backend: `php artisan test tests/Feature/PendaftaranKelompokTest.php`
- [x] Static Analysis: `./vendor/bin/phpstan analyse app/Http/Controllers/Auth/AuthController.php app/Models/Pengaturan.php tests/Feature/PendaftaranKelompokTest.php --memory-limit=1G`
- [x] Code Formatting: `./vendor/bin/pint --test app/Http/Controllers/Auth/AuthController.php app/Models/Pengaturan.php tests/Feature/PendaftaranKelompokTest.php`
- [x] Frontend Asset Compile: `npm run build`
- [x] Browser/Console Check: Pastikan tidak ada error JavaScript di console saat menambah/menghapus anggota.

---

## 🛡️ 6. Matriks Risiko & Mitigasi (Risk & Safety Measures)

1. **Risiko N+1 atau Cache Stale pada Pengaturan**:
   - *Mitigasi*: Model `Pengaturan::getInt(...)` sudah menggunakan `Cache::rememberForever` dengan key prefix otomatis. `Pengaturan::setNilai` memanggil `self::clearCache($kunci)` sehingga aman dan reaktif.
2. **Risiko Array Index Gap saat Submit HTTP POST**:
   - *Mitigasi*: Fungsi `updateRosterState()` secara otomatis menata ulang atribut `name="anggota[0]"`, `name="anggota[1]"` dari 0 s.d. k secara berurutan sebelum form disubmit, dan backend `AuthController::register` sudah memiliki sanitasi `array_values(array_filter(...))`.
3. **Risiko Aksesibilitas (WCAG 2.1 AA)**:
   - *Mitigasi*: Tombol hapus dan tombol tambah memiliki `aria-label`, state disabled yang tepat, dan tidak menggunakan inline event handler di Blade.
4. **Risiko Pelanggaran Aturan AGENTS.md**:
   - *Mitigasi*: Tidak menulis `<script>` atau `<style>` baru di dalam Blade. Seluruh interaksi berada di `resources/js/pages/auth/auth-depth.js` dan styling berada di `resources/css/pages/auth/auth-depth.css`.

---

## ✅ 7. Kriteria Selesai (Definition of Done)

- [x] Tipe Pendaftaran `Individu (1 Orang)` dan `Kelompok (2–N Orang)` tampil presisi sesuai screenshot.
- [x] Batas anggota `N` secara dinamis mengambil data dari `pengaturan` sistem (bukan hardcoded).
- [x] Kartu "Data Tim & Rombongan Magang" dan "Daftar Rekan Anggota Tim" tampil saat mode Kelompok aktif.
- [x] Pengguna dapat menambah rekan anggota hingga batas maksimal dengan tombol `+ Tambah Rekan Anggota`.
- [x] Pengguna dapat menghapus kartu rekan anggota dengan tombol `Hapus`.
- [x] Saat batas tercapai, tombol tambah di-disable dan muncul pesan batasan kuota rombongan.
- [x] Data `old('anggota')` tetap aman dan tidak hilang jika terjadi error validasi (misal salah captcha).
- [x] Seluruh unit & feature test hijau (`php artisan test`).
- [x] Build asset Vite sukses tanpa warning/error (`npm run build`).
- [x] Formatting Pint & PHPStan lulus 100%.

---
*Dokumen rencana kerja ini siap dieksekusi setelah mendapatkan konfirmasi persetujuan dari pengguna.*
