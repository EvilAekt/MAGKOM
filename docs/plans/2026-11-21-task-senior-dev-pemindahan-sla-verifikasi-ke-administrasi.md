# TASK & IMPLEMENTATION PLAN: PEMINDAHAN FITUR SLA VERIFIKASI BERKAS KE ADMINISTRASI

> **Dari:** Lead Software Architect / Senior Developer  
> **Kepada:** Junior Developer / AI Implementation Engineer (Gemini 3.8 Flash Compatible)  
> **Target Repositori:** `MAGKOM` (Portal Manajemen Magang Dinas Kominfo Sleman)  
> **Status:** READY FOR REVIEW / MENUNGGU PERSETUJUAN PENGGUNA  
> **Tingkat Prioritas:** High (Domain Integrity, Business Logic Alignment, Strict RBAC)  
> **Dokumen Terkait:** `docs/01-PRD-MagKom.md`, `docs/02-TDD-MagKom.md`, `resources/views/admin/kebijakan/index.blade.php`, `resources/views/pembimbing/aturan/index.blade.php`  
> **Tanggal Dokumen:** 21 November 2026  

---

## 1. 🎯 RINGKASAN EKSEKUTIF & LATAR BELAKANG

### 1.1 Masalah Saat Ini (Current State & Domain Misalignment)
1. **Ketidaksesuaian Domain Bisnis**: Parameter **Target Batas Waktu SLA Verifikasi Berkas (Hari Kerja)** (`sla_verifikasi_hari`) saat ini berada di menu **Aturan Pelaksanaan, Logbook & Dokumen** milik Pembimbing (`/pembimbing/aturan`).
2. **Fakta Tupoksi Staf**:
   - Pembimbing Lapangan bertugas membimbing mentee, mengoreksi logbook, dan menilai tugas. Pembimbing sama sekali **tidak pernah melakukan verifikasi berkas pendaftaran calon peserta**.
   - Verifikasi berkas administrasi dan pendaftaran calon peserta magang adalah tugas pokok dan fungsi mutlak dari **Staf Administrasi (`admin`)** pada modul `/admin/pendaftaran`.
   - Menaruh parameter SLA verifikasi berkas pada portal Pembimbing adalah anomali pemisahan peran (*separation of concerns*) yang membingungkan staf.

### 1.2 Target Akhir yang Diinginkan (Target State)
1. **Pindahkan Parameter `sla_verifikasi_hari` ke Menu Administrasi**:
   - Parameter disematkan ke dalam **Kebijakan Kuota, Pendaftaran & Kapasitas Magang** (`/admin/kebijakan`).
   - Seluruh validasi, otorisasi (`role:admin`), controller logic, form markup, dan audit logging menjadi tanggung jawab Administrasi.
   - Total parameter kebijakan di Administrasi menjadi **6 parameter**.
2. **Bersihkan Menu Pembimbing dari `sla_verifikasi_hari`**:
   - Hapus parameter `sla_verifikasi_hari` dari form request, controller, dan antarmuka Blade Pembimbing (`/pembimbing/aturan`).
   - Pembimbing kini murni hanya mengelola 2 parameter teknis bimbingan lapangan:
     1. `berkas_maks_mb` (Batas Ukuran Maksimal Berkas Upload)
     2. `deadline_mendekati_hari` (Peringatan Mendekati Deadline Tugas)

---

## 2. 📊 PERBANDINGAN PERUBAHAN MATRIKS DATA (BEFORE VS AFTER)

| Komponen / Parameter | Kondisi Saat Ini | Kondisi Target Baru | Role Pengelola Baru | Validasi Batasan |
| :--- | :--- | :--- | :--- | :--- |
| **Batas Kuota Umum Publik Per Bulan** | Administrasi | **Administrasi** | `role:admin` | Required, Integer, Min: 1, Max: 100 |
| **Default Alokasi Slot Khusus** | Administrasi | **Administrasi** | `role:admin` | Required, Integer, Min: 0, Max: 50 |
| **Beban Maksimal Mentee Per Pembimbing** | Administrasi | **Administrasi** | `role:admin` | Nullable, Integer, Min: 1, Max: 50 |
| **Batas Maksimal Anggota Kelompok** | Administrasi | **Administrasi** | `role:admin` | Required, Integer, Min: 1, Max: 10 |
| **Durasi Magang Maksimal yang Diizinkan** | Administrasi | **Administrasi** | `role:admin` | Required, Integer, Min: 1, Max: 12 |
| **Target Batas Waktu SLA Verifikasi Berkas** | ⚠️ Pembimbing | 🎯 **ADMINISTRASI** | `role:admin` | Required, Integer, Min: 1, Max: 30 |
| **Batas Ukuran Maksimal Berkas Upload** | Pembimbing | **Pembimbing** | `role:pembimbing` | Required, Integer, Min: 1, Max: 20 |
| **Peringatan Mendekati Deadline Tugas** | Pembimbing | **Pembimbing** | `role:pembimbing` | Required, Integer, Min: 1, Max: 14 |
| **Batas Mundur Pengisian Logbook** | ❌ Dihapus dari UI | ❌ **Tetap Dihapus dari UI** | None | Tidak ada input di form manapun |

---

## 3. 🛡️ ATURAN & STANDAR KODE YANG HARUS DIPATUHI

1. **Aturan Khusus Blade (STRICT - NO JS/CSS IN BLADE)**:
   - Dilarang menulis tag `<style>` dan tag `<script>` pada file Blade.
   - Dilarang menggunakan inline style atau inline event handler (`onclick`, `onchange`, dll.).
   - Gunakan kelas Gentelella CSS standar yang sudah ada: `.cfg-form-grid`, `.cfg-form-group`, `.cfg-label`, `.cfg-input`, `.cfg-required-star`.
2. **Aturan Backend & Otorisasi**:
   - `UpdateKebijakanMagangRequest` hanya mengizinkan `RoleEnum::Admin`.
   - `UpdateAturanBimbinganRequest` hanya mengizinkan `RoleEnum::Pembimbing`.
   - Setiap mutasi database wajib dibungkus dalam `DB::transaction()`.
   - Setiap mutasi wajib mencatat rekam jejak audit via `AuditService::catat()`.
   - Wajib memanggil `Pengaturan::clearCache()` segera setelah transaksi berhasil commit.
3. **Quality Gates Wajib Hijau**:
   - Pint: `./vendor/bin/pint --test`
   - PHPStan: `./vendor/bin/phpstan analyse --memory-limit=1G`
   - Test Suite: `php artisan test`

---

## 4. 📝 RINCIAN LANGKAH KERJA TEKNIS (STEP-BY-STEP IMPLEMENTATION)

### Langkah 1: Penyesuaian Backend Administrasi (Admin)

#### 1.1 Form Request `app/Http/Requests/UpdateKebijakanMagangRequest.php`
- Tambahkan aturan validasi:
  ```php
  'sla_verifikasi_hari' => ['required', 'integer', 'min:1', 'max:30'],
  ```
- Tambahkan pesan validasi:
  ```php
  'sla_verifikasi_hari.required' => 'Target batas waktu SLA verifikasi berkas wajib diisi.',
  'sla_verifikasi_hari.integer' => 'Target batas waktu SLA verifikasi berkas harus berupa angka.',
  'sla_verifikasi_hari.min' => 'Target batas waktu SLA verifikasi berkas minimal 1 hari kerja.',
  'sla_verifikasi_hari.max' => 'Target batas waktu SLA verifikasi berkas maksimal 30 hari kerja.',
  ```

#### 1.2 Controller `app/Http/Controllers/Admin/KebijakanMagangController.php`
- Pada method `index()`:
  - Masukkan `'sla_verifikasi_hari'` ke dalam array `$kunciKebijakan`.
  - Teruskan `'slaVerifikasiHari' => (int) ($pengaturan['sla_verifikasi_hari'] ?? 5)` ke view.
- Pada method `update()`:
  - Masukkan `'sla_verifikasi_hari' => (string) $validated['sla_verifikasi_hari']` ke dalam array `$settingsToSave`.

#### 1.3 View `resources/views/admin/kebijakan/index.blade.php`
- Sisipkan elemen input SLA Verifikasi Berkas ke dalam grid form kartu (menjadi elemen ke-6):
  ```blade
  <div class="cfg-form-group">
      <label class="cfg-label" for="sla_verifikasi_hari">
          {{ __('Target Batas Waktu SLA Verifikasi Berkas (Hari Kerja)') }} <span class="cfg-required-star">*</span>
      </label>
      <input type="number" id="sla_verifikasi_hari" name="sla_verifikasi_hari" min="1" max="30" required value="{{ old('sla_verifikasi_hari', $slaVerifikasiHari) }}" class="cfg-input">
  </div>
  ```

---

### Langkah 2: Pembersihan Backend Pembimbing

#### 2.1 Form Request `app/Http/Requests/UpdateAturanBimbinganRequest.php`
- Hapus rule `'sla_verifikasi_hari'` dari method `rules()`.
- Hapus custom message `'sla_verifikasi_hari.*'` dari method `messages()`.

#### 2.2 Controller `app/Http/Controllers/Pembimbing/AturanBimbinganController.php`
- Pada method `index()`:
  - Hapus `'sla_verifikasi_hari'` dari array `$kunciAturan`.
  - Hapus `'slaVerifikasiHari'` dari payload view.
- Pada method `update()`:
  - Hapus `'sla_verifikasi_hari'` dari array `$settingsToSave`.

#### 2.3 View `resources/views/pembimbing/aturan/index.blade.php`
- Hapus blok form-group input `sla_verifikasi_hari` dari tampilan kartu bimbingan.
- Kartu kini hanya menyisakan input `berkas_maks_mb` dan `deadline_mendekati_hari`.

---

### Langkah 3: Penyelarasan Automated Test Suites

#### 3.1 `tests/Feature/AdminKebijakanMagangTest.php`
- Uji bahwa Admin dapat melihat label dan input `sla_verifikasi_hari`.
- Uji bahwa Admin dapat memperbarui nilai `sla_verifikasi_hari` (misal dari 5 menjadi 7) dan tercatat di database serta audit log.
- Uji bahwa validasi gagal jika `sla_verifikasi_hari` bernilai 0 atau melebihi 30.

#### 3.2 `tests/Feature/PembimbingAturanBimbinganTest.php`
- Uji bahwa Pembimbing **tidak lagi melihat** teks `Target Batas Waktu SLA Verifikasi Berkas`.
- Uji bahwa Pembimbing dapat memperbarui aturan hanya dengan mengirimkan `berkas_maks_mb` dan `deadline_mendekati_hari` tanpa error.
- Uji bahwa request pembaruan oleh Pembimbing tidak lagi memodifikasi `sla_verifikasi_hari`.

---

## 5. ✅ CHECKLIST VERIFIKASI SEBELUM TUGAS DINYATAKAN SELESAI

- [ ] `php artisan test tests/Feature/AdminKebijakanMagangTest.php` -> PASSED
- [ ] `php artisan test tests/Feature/PembimbingAturanBimbinganTest.php` -> PASSED
- [ ] `./vendor/bin/pint --test` -> PASSED (0 error)
- [ ] `./vendor/bin/phpstan analyse --memory-limit=1G` -> PASSED (0 error)
- [ ] `npm run build` -> PASSED
- [ ] `php artisan test` -> Seluruh 1.123 test tetap PASSED (100% Green)
- [ ] Tidak ada inline CSS/JS yang disisipkan ke file Blade
- [ ] Audit log dan cache invalidation berfungsi normal
