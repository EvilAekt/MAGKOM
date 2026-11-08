# PRD & Implementation Plan: Audit & Penguatan Logic Gagal (Failure Logic) Modul Super Admin MAGKOM

**Target Audience:** Junior Developers & Budget AI Agents (Gemini 3.8 Flash, Claude Haiku, dsb.)  
**Document Type:** Senior Developer Task Brief / Technical Audit & Implementation Plan  
**Repository:** MagKom (Portal Manajemen Magang Kominfo Sleman)  
**Author:** Senior Developer / Engineering Lead  
**Stack Baseline:** PHP 8.3 | Laravel 10 | Blade | Vanilla CSS | Vanilla JS | Vite | MySQL / SQLite  
**Modul Target:** Modul Super Admin (Task A, Task B, Task C, Task D, Task E)  
**Tanggal:** 7 November 2026  

---

## 1. Konteks Proyek & Mengapa "Logic Gagal" Sangat Penting

Halo Junior Developer / AI Agent!

Sebelumnya kita telah berhasil mengimplementasikan 5 kelompok tugas untuk **Modul Super Admin MAGKOM**:
- **Task A**: Pembatasan akses Super Admin (Strict Read-Only pada peran lain).
- **Task B**: Form akun staf baru (Admin Verifikator tanpa unit kerja).
- **Task C**: Penghapusan fitur Rubrik Nilai tanpa merusak data riwayat penilaian.
- **Task D**: Pembuatan akun peserta magang jalur khusus langsung aktif.
- **Task E**: CMS Landing Page (teks hero, foto banner, dan PDF buku panduan).

Seluruh *Happy Path* (skenario normal saat input benar) sudah berjalan dan lulus 605 automated tests.

**TETAPI SEBAGAI SENIOR DEVELOPER, SAYA HARUS MENGINGATKAN:**
> *"Software yang tangguh (resilient) tidak diukur dari seberapa mulus ia berjalan saat input benar, melainkan seberapa aman, konsisten, dan informatif ia bereaksi saat terjadi kegagalan (Failure Path & Edge Cases)."*

### Apa itu "Logic Gagal" (Failure Logic)?
Logic gagal adalah penanganan sistem ketika sesuatu **tidak berjalan sesuai rencana**, antara lain:
1. **Validation Failure**: User mengosongkan field wajib, format salah, ukuran file kebesaran, atau mengirim data duplikat.
2. **Authorization Failure**: User mencoba mengakses atau mengubah resource yang bukan wewenangnya (misal: Super Admin mencoba menyetujui berkas pendaftaran atau menembak endpoint mutasi secara ilegal).
3. **Resource Exhaustion / Concurrency**: Kuota atau kapasitas habis, slot khusus penuh, atau pembimbing sudah mencapai batas maksimal bimbingan.
4. **Data Inconsistency / Atomicity Failure**: Terjadi error di tengah proses多-tabel (misal: user tersimpan tapi data penempatan gagal), yang mewajibkan **Database Rollback** agar tidak meninggalkan data sampah / yatim (*orphaned data*).
5. **Physical Storage / File Handling Failure**: Berkas PDF atau foto fisik hilang dari disk server, berkas upload korup/berbahaya, atau penyimpanan server penuh.
6. **Graceful Degradation / Fallback**: Ketika data kustom tidak ditemukan, sistem tidak boleh melempar HTTP 500 (*Server Crash*), melainkan harus beralih secara mulus ke nilai default yang aman.

Dokumen ini adalah **panduan kerja teknis rinci** bagimu untuk mengaudit, menemukan celah, dan melengkapi seluruh logic gagal pada Modul Super Admin.

---

## 2. Matriks Hasil Audit Logic Gagal (Existing vs Gap Analysis)

Berikut adalah hasil audit mendalam terhadap kode yang telah diimplementasikan:

| Task | Fitur | Logic Gagal yang Sudah Ada (Existing) | Celah / Logic Gagal yang Kurang (Gaps Found) | Tingkat Risiko |
| :--- | :--- | :--- | :--- | :--- |
| **Task A** | Super Admin Read-Only | • Middleware `EnsureSuperAdminReadOnly` menolak `POST/PUT/PATCH/DELETE` dengan HTTP 403.<br>• Policy & Form Request membatasi mutasi hanya untuk `admin`.<br>• Tombol mutasi di Blade disembunyikan. | • Belum ada pengujian eksplisit untuk format respons JSON (jika request datang via AJAX/fetch dengan header `Accept: application/json`).<br>• Perlu memastikan pesan 403 tidak membocorkan informasi sistem sensitif. | **Sedang** |
| **Task B** | Form Tambah Staf Baru | • Validasi email unik (`unique:users,email`).<br>• Validasi regex password minimal 8 char & kombinasi angka.<br>• `Rule::requiredIf` mewajibkan bidang untuk Pembimbing dan nullable untuk Admin. | • Belum menggunakan `DB::transaction()` pada `StafController::store()`. Jika pencatatan `auditService->catat()` gagal, user staf baru sudah terlanjur tersimpan tanpa rekam jejak audit (pelanggaran audit trail).<br>• Belum ada pencegahan jika SuperAdmin secara tidak sengaja menonaktifkan akunnya sendiri di `toggleStatus`. | **Tinggi** |
| **Task C** | Penghapusan Rubrik Nilai | • Route dicabut dari `web.php`.<br>• `RubrikController` mengembalikan `abort(404)`.<br>• View Blade dihapus.<br>• Tabel DB & histori nilai dipertahankan. | • Sudah sangat baik dan aman. Perlu dipastikan tidak ada pemanggilan asinkron/AJAX legacy di latar belakang yang memicu unhandled console error. | **Rendah** |
| **Task D** | Peserta Jalur Khusus | • Validasi email unik (`unique:users,email`).<br>• Validasi regex periode `YYYY-MM`.<br>• Validasi field wajib via Form Request.<br>• Blok `try-catch` menangkap `\Throwable` dan me-redirect back dengan pesan error flash. | • **CRITICAL GAP**: Jika input `slot_khusus_id` dikosongkan (pilihan *"-- Tanpa Slot Khusus Spesifik --"*), `PlacementService::tempatkanPesertaBaruManual` memanggil `ambilKuotaKhusus(0)` yang memicu `ModelNotFoundException` fatal!<br>• Belum ada validasi duplikasi `nim_nisn` pada `peserta_profil`.<br>• Validasi `pembimbing_id` baru sebatas `exists:users,id`, belum memastikan bahwa user tersebut ber-role `pembimbing` dan berstatus aktif (`status_aktif = true`). | **Kritis (Blocker)** |
| **Task E** | CMS Landing Page | • Validasi MIME type berkas (image: jpg/png/webp max 2MB, pdf: max 5MB).<br>• Auto-delete file lama di disk sebelum menimpa berkas baru.<br>• Fallback otomatis ke DomPDF generator dinamis jika file fisik PDF kustom tidak ditemukan di disk storage. | • Pada `LandingCmsController::update()`, teks disimpan ke DB **sebelum** proses upload berkas. Jika upload berkas gagal (misal disk IO error), teks sudah terlanjur berubah (inkonsistensi parsial).<br>• Nama berkas asli (`landing_panduan_pdf_name`) belum disanitasi dari potensi *HTTP Header Injection* saat di-stream via `Content-Disposition`. | **Sedang** |

---

## 3. Rencana Kerja Perbaikan Logic Gagal (Action Items)

Sebagai Junior Developer / AI Agent, tugasmu adalah mengimplementasikan perbaikan logic gagal berikut langkah demi langkah:

```
[AUDIT CHECKLIST]
├── Task 1: Perbaikan Critical Gap di Task D (Placement Jalur Khusus Nullable Slot)
├── Task 2: Penguatan Validasi Backend di Task D (NIM Unik, Pembimbing Valid & Aktif)
├── Task 3: Atomisitas Transaksi & Error Handling di Task B (Staf Controller DB Transaction)
├── Task 4: Atomisitas Penyimpanan & Sanitasi Berkas di Task E (CMS Landing Page)
├── Task 5: Penegakan Format Respons Negatif di Task A (JSON vs HTML Error Negotiation)
└── Task 6: Pembuatan Automated Negative Test Suite (Pengujian Seluruh Skenario Gagal)
```

---

## 4. Rincian Teknis Implementasi Per Task

### TASK 1 — Perbaikan Critical Gap Penempatan Jalur Khusus (Task D)

**Masalah:**  
Pada form pendaftaran peserta jalur khusus (`resources/views/superadmin/peserta-khusus/create.blade.php`), dropdown `slot_khusus_id` bersifat opsional:
```html
<option value="">-- Tanpa Slot Khusus Spesifik --</option>
```
Namun pada `app/Services/PlacementService.php` baris 444:
```php
$slot = $this->quotaAllocationService->ambilKuotaKhusus((int) $slotKhususId);
```
Jika user memilih tanpa slot khusus (`$slotKhususId` bernilai `null`), kode meng-cast `(int) null` menjadi `0`, lalu mencari `SlotKhusus::whereKey(0)->firstOrFail()`, yang langsung melempar exception:
`ModelNotFoundException: No query results for model [App\Models\SlotKhusus] 0`.

**Solusi yang Harus Diterapkan:**
Buka `app/Services/PlacementService.php`, cari method `tempatkanPesertaBaruManual()`, ubah logika alokasi kapasitas menjadi:

```php
// 3. Alokasikan kapasitas (slot khusus atau kuota umum)
$assignedSlotId = null;
$assignedKuotaId = null;
$jalurEnum = JalurEnum::Khusus;

if ($jalur === 'umum' && $kuotaId) {
    $kuota = $this->quotaAllocationService->ambilKuotaUmum((int) $kuotaId);
    $assignedKuotaId = $kuota->id;
    $jalurEnum = JalurEnum::Umum;
} elseif ($slotKhususId) {
    // Alokasikan ke slot khusus jika ID slot disediakan
    $slot = $this->quotaAllocationService->ambilKuotaKhusus((int) $slotKhususId);
    $assignedSlotId = $slot->id;
    $jalurEnum = JalurEnum::Khusus;
} else {
    // Penempatan jalur khusus diskresi tanpa memotong slot kuota spesifik
    $assignedSlotId = null;
    $jalurEnum = JalurEnum::Khusus;
}
```

**Verifikasi:**
Jika Super Admin membuat peserta khusus dengan opsi *"-- Tanpa Slot Khusus Spesifik --"*, proses harus berhasil dengan `slot_khusus_id = null` tanpa melempar exception!

---

### TASK 2 — Penguatan Validasi Backend Peserta Jalur Khusus (Task D)

**Masalah:**  
1. NIM/NISN belum dicek keunikannya di `PesertaJalurKhususController`. Jika Super Admin memasukkan NIM yang sudah aktif digunakan siswa lain, data profil akan bertabrakan.
2. `pembimbing_id` hanya divalidasi `exists:users,id`. Seorang penyerang bisa mengirimkan ID user yang bukan Pembimbing (misal user Peserta) atau Pembimbing yang sudah dinonaktifkan (`status_aktif = false`).

**Solusi yang Harus Diterapkan:**
Buka `app/Http/Controllers/SuperAdmin/PesertaJalurKhususController.php`, perbarui aturan validasi pada method `store()`:

```php
$validated = $request->validate([
    'nama' => ['required', 'string', 'max:255'],
    'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
    'password' => ['nullable', 'string', 'min:8'],
    'nim_nisn' => [
        'required', 
        'string', 
        'max:30',
        Rule::unique('peserta_profil', 'nim_nisn'),
    ],
    'institusi' => ['required', 'string', 'max:150'],
    'jurusan_id' => ['required', 'exists:jurusan,id'],
    'jenjang' => ['required', Rule::in(['smk', 'd3', 's1'])],
    'no_hp' => ['nullable', 'string', 'max:25'],
    'bidang_id' => ['nullable', 'exists:bidang,id'],
    'pembimbing_id' => [
        'required',
        Rule::exists('users', 'id')->where(function ($query) {
            $query->where('role', RoleEnum::Pembimbing->value)
                  ->where('status_aktif', true);
        }),
    ],
    'bulan_mulai' => ['required', 'string', 'regex:/^\d{4}-\d{2}$/'],
    'durasi_bulan' => ['required', 'integer', 'min:1', 'max:12'],
    'slot_khusus_id' => ['nullable', 'exists:slot_khusus,id'],
    'catatan' => ['nullable', 'string', 'max:500'],
], [
    'nim_nisn.unique' => 'NIM / NISN ini sudah terdaftar dalam sistem MAGKOM.',
    'pembimbing_id.exists' => 'Pembimbing yang dipilih tidak valid atau sedang tidak aktif.',
    'bulan_mulai.regex' => 'Format bulan mulai harus YYYY-MM (contoh: 2026-11).',
]);
```

---

### TASK 3 — Penjaminan Atomisitas Transaksi Pembuatan Akun Staf (Task B)

**Masalah:**  
Pada `app/Http/Controllers/SuperAdmin/StafController.php`, method `store()` menyimpan data `$staf->save()` lalu memanggil `$this->auditService->catat()`. Kedua operasi ini belum dibungkus dalam `DB::transaction()`. Jika sistem database gagal saat mencatat audit trail, akun user staf baru tetap terbuat tanpa tercatat di audit log.

**Solusi yang Harus Diterapkan:**
Bungkus proses `store()` dan `update()` pada `StafController.php` dengan `DB::transaction()`:

```php
use Illuminate\Support\Facades\DB;

// Di dalam method store():
$staf = DB::transaction(function () use ($validated, $plainPassword, $roleEnum, $bidangId, $request) {
    $staf = new User;
    $staf->nama = $validated['nama'];
    $staf->email = $validated['email'];
    $staf->password = Hash::make($plainPassword);
    $staf->role = $roleEnum;
    $staf->bidang_id = $bidangId;
    $staf->email_verified_at = now();
    $staf->must_change_password = true;
    $staf->status_aktif = true;
    $staf->dibuat_oleh = $request->user()->id;
    $staf->save();

    $this->auditService->catat(
        tipe: TipeAuditEnum::Aksi,
        tabel: 'users',
        idData: $staf->id,
        nilaiBaru: [
            'nama' => $staf->nama,
            'email' => $staf->email,
            'role' => $staf->role->value,
            'status_aktif' => true,
            'must_change_password' => true,
        ],
        detail: [
            'aksi' => 'Membuat akun staf baru ('.$staf->role->label().') (SEC-02)',
            'superadmin_id' => $request->user()->id,
        ]
    );

    return $staf;
});
```

---

### TASK 4 — Pengamanan Upload Berkas & Sanitasi Header (Task E)

**Masalah:**  
1. Pada `LandingCmsController::update()`, teks disimpan ke DB sebelum upload file diproses. Jika berkas upload gagal disimpan ke disk karena error IO, teks sudah terlanjur tersimpan di DB.
2. Nama berkas asli (`landing_panduan_pdf_name`) yang diunggah user digunakan langsung pada header HTTP:
   `'Content-Disposition' => 'inline; filename="'.$fileName.'"'`
   Jika user mengunggah nama berkas yang mengandung karakter kutip dua (`"`) atau baris baru (`\r\n`), hal ini memicu kerentanan *HTTP Response Splitting*.

**Solusi yang Harus Diterapkan:**
1. Di `LandingCmsController::update()`, simpan berkas fisik terlebih dahulu, lalu bungkus penyimpanan konfigurasi dalam `DB::transaction()`. Jika ada kegagalan, hapus berkas fisik yang baru saja terunggah.
2. Sanitasi nama berkas:
   ```php
   $safeOriginalName = preg_replace('/[^a-zA-Z0-9_\-\. ]/', '_', basename($request->file('panduan_pdf')->getClientOriginalName()));
   ```
3. Di `PanduanPdfController::download()`, gunakan `$safeOriginalName` yang sudah bersih dari karakter kontrol.

---

### TASK 5 — Penanganan Respons Negatif Otorisasi Super Admin (Task A)

**Masalah:**  
Memastikan bahwa penolakan mutasi operasional oleh Super Admin bekerja konsisten baik untuk request berbasis Browser Form (HTML redirect dengan error toast/halaman 403) maupun request berbasis API/AJAX (`Accept: application/json`).

**Solusi yang Harus Diterapkan:**
Periksa `EnsureSuperAdminReadOnly.php`:
```php
public function handle(Request $request, Closure $next): Response
{
    if ($request->user()?->role === RoleEnum::SuperAdmin && ! $request->isMethodSafe()) {
        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'error',
                'message' => 'SuperAdmin hanya memiliki hak akses lihat (read-only) pada modul operasional role lain.',
            ], 403);
        }

        abort(403, 'SuperAdmin hanya memiliki hak akses lihat (read-only) pada modul operasional role lain.');
    }

    return $next($request);
}
```

---

## 5. Matriks Pengujian Negatif (Negative Test Suite)

Junior Developer / AI Agent wajib menambahkan test case berikut pada `tests/Feature/SuperAdminModuleEnhancementTest.php`:

### Test Case 1: Penolakan AJAX Mutasi Super Admin (Task A)
- **Aksi:** Kirim request `POST` via AJAX (`headers: ['Accept' => 'application/json']`) dari Super Admin ke `/admin/kuota`.
- **Ekspektasi:** Status HTTP `403 Forbidden`, response JSON memuat key `'status' => 'error'`.

### Test Case 2: Validasi Duplikasi Email & Password Lemah Staf (Task B)
- **Aksi:** Super Admin mencoba membuat staf dengan email yang sudah ada atau password kurang dari 8 karakter.
- **Ekspektasi:** Session error pada field `'email'` dan `'password'`. Tidak ada user baru tersimpan.

### Test Case 3: Pembuatan Peserta Jalur Khusus Tanpa Slot Khusus (Task D)
- **Aksi:** Super Admin membuat peserta khusus dengan `slot_khusus_id = null`.
- **Ekspektasi:** Status `302 Redirect`, peserta berhasil dibuat, `penempatan.slot_khusus_id` bernilai `null`, `status = aktif`.

### Test Case 4: Penolakan Duplikasi NIM/NISN Peserta Khusus (Task D)
- **Aksi:** Super Admin membuat peserta khusus dengan `nim_nisn` yang sudah dimiliki oleh peserta lain.
- **Ekspektasi:** Request ditolak dengan session error pada field `'nim_nisn'`.

### Test Case 5: Penolakan Pembimbing Non-Aktif (Task D)
- **Aksi:** Super Admin mengirim request pembuatan peserta khusus dengan `pembimbing_id` milik user non-aktif atau ber-role `peserta`.
- **Ekspektasi:** Request ditolak dengan validation error pada field `'pembimbing_id'`.

### Test Case 6: Penolakan Ekstensi Palsu & Berkas Terlalu Besar (Task E)
- **Aksi:** Super Admin mengunggah berkas `.exe` yang di-rename menjadi `.pdf` atau berkas gambar melebihi 2MB.
- **Ekspektasi:** Validasi backend gagal, berkas lama tidak terhapus, konfigurasi DB tidak berubah.

---

## 6. Acceptance Criteria (Kriteria Kelulusan)

Pekerjaan dinyatakan selesai dan dapat diterima oleh Senior Developer jika:
1. [ ] Seluruh celah kritis pada Task D (`ambilKuotaKhusus(0)`) telah diperbaiki dan diverifikasi.
2. [ ] Validasi NIM unik dan pembimbing aktif pada pembuatan peserta khusus berfungsi 100%.
3. [ ] Transaksi database (`DB::transaction`) melindungi pembuatan staf dan penempatan peserta dari data korup.
4. [ ] Seluruh skenario negatif pada Matriks Pengujian telah dibuatkan feature test-nya.
5. [ ] Perintah `php artisan test` menghasilkan **100% PASS** tanpa satupun kegagalan.
6. [ ] Perintah `./vendor/bin/pint --test` lulus tanpa error styling.
7. [ ] Perintah `npm run build` sukses mengompilasi seluruh aset.

