# Plan: Fitur Perubahan Bahasa Antarmuka (Indonesia & Inggris)

- **Tanggal**: 2026-10-14
- **Status**: Draft (Menunggu Persetujuan)
- **Author**: Antigravity (web-planning)

---

## 1. Tujuan
Menyediakan fitur pergantian bahasa (*localization / i18n*) dwibahasa (**Bahasa Indonesia** sebagai default dan **English (US)**) yang terintegrasi secara menyeluruh dengan menu pengaturan preferensi akun pada semua role (SuperAdmin, Admin Bidang, Pembimbing Lapangan, dan Peserta Magang). Sistem akan mendeteksi preferensi bahasa pengguna dari session dan cookie, mengatur locale aplikasi (`app()->setLocale()`) secara konsisten pada setiap request melalui dedicated middleware, dan merender antarmuka dalam bahasa Inggris secara akurat dan profesional.

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi:
1. **Bahasa Utama & Fallback**: Bahasa Indonesia (`id`) tetap menjadi locale bawaan (*default*) dan *fallback*, sedangkan English (`en`) adalah bahasa alternatif internasional.
2. **Kamus Terjemahan Berbasis JSON**: Menggunakan format kamus JSON resmi Laravel (`lang/en.json` dan `lang/id.json`) dengan teks Bahasa Indonesia sebagai kunci (*translation key*), sehingga implementasi bersifat aman, *backward-compatible*, dan tidak merusak tampilan jika ada string yang belum terpetakan.
3. **Cakupan Terjemahan Fase Ini**:
   - Shell antarmuka utama (Sidebar navigasi, Topbar header, User menu dropdown, Footer, dan Breadcrumb).
   - Halaman pengaturan preferensi akun di semua portal (SuperAdmin, Admin, Pembimbing, Peserta).
   - Komponen pesan flash alert (`<x-flash-alert />`) dan dialog umum.
   - Tag `<html lang="{{ app()->getLocale() }}">` di seluruh template layout dasar.
   - Konten dinamis database (seperti nama instansi spesifik, deskripsi tugas dinas, atau log audit historis) tetap mempertahankan teks aslinya sesuai input pengguna.

### Pertanyaan Terbuka:
- Tidak ada pertanyaan pemblokir (*blocking questions*). Struktur form dan pengontrol preferensi bahasa (`updatePreferensi`) sudah tersedia di codebase; hanya middleware pendeteksi, kamus terjemahan, dan pembungkusan string Blade yang perlu disempurnakan.

---

## 3. Pendekatan Desain & Arsitektur

### Evaluasi Opsi:

| Aspek | Opsi A: Laravel JSON Translation (`lang/en.json`) - **(Dipilih)** | Opsi B: PHP File Array Translation (`lang/en/messages.php`) |
| :--- | :--- | :--- |
| **Bentuk Kunci** | Kunci adalah string alami, misal: `{{ __('Pengaturan Akun') }}` | Kunci berupa dot-notation, misal: `{{ __('nav.settings_account') }}` |
| **Backward Compatibility** | Sangat tinggi. Jika kunci belum diterjemahkan di `en.json`, teks bahasa aslinya tetap tampil wajar tanpa eror atau kebocoran nama variabel. | Rendah. Jika kunci lupa didefinisikan, antarmuka akan menampilkan teks mentah `nav.settings_account`. |
| **Kemudahan Maintenance** | Sangat mudah. Cukup menambahkan pasangan `"Kunci Indo": "English Translation"` ke dalam satu file kamus terpusat. | Lebih rumit karena harus mengelola banyak berkas PHP terpisah (`nav.php`, `auth.php`, `settings.php`). |
| **Kesesuaian Codebase** | Sempurna untuk Blade template MagKom yang saat ini sudah memiliki ribuan baris teks Bahasa Indonesia siap pakai. | Memerlukan refactoring masif pada seluruh nama kunci di puluhan file Blade. |

### Arsitektur Alur Bahasa (Locale Flow):
```
[User Browser]
      │
      ▼ (HTTP Request)
[App\Http\Middleware\SetLocale]
      ├── 1. Baca session('bahasa_portal')
      ├── 2. Jika kosong, baca cookie('bahasa_portal')
      ├── 3. Validasi ke daftar whitelist ['id', 'en'] (fallback: config('app.locale', 'id'))
      ├── 4. Eksekusi App::setLocale($locale) & Carbon::setLocale($locale)
      └── 5. Bagikan $bahasaAktif ke semua View (view()->share('bahasaAktif', $locale))
      │
      ▼
[Blade Engine & Controllers]
      ├── <html lang="{{ app()->getLocale() }}">
      └── {{ __('Teks Antarmuka') }} ──> Resolusi ke lang/en.json atau fallback
```

---

## 4. Perubahan File

| File Path | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `app/Http/Middleware/SetLocale.php` | **Create** | Middleware baru untuk mengekstrak preferensi bahasa dari session/cookie, memvalidasi whitelist (`id`, `en`), memanggil `App::setLocale()`, dan menyinkronkan Carbon locale. |
| `app/Http/Kernel.php` | **Modify** | Mendaftarkan `SetLocale::class` ke dalam middleware group `'web'` agar otomatis berjalan di semua rute web. |
| `app/Http/Middleware/EncryptCookies.php` | **Modify** | Menambahkan `'bahasa_portal'` dan `'tema_tampilan'` ke array `$except` agar cookie preferensi dapat dibaca secara transparan oleh middleware dan browser. |
| `lang/en.json` | **Create** | Kamus terjemahan bahasa Inggris komprehensif mencakup navigasi dashboard, preferensi pengaturan, tombol aksi, notifikasi flash, dan istilah MagKom. |
| `lang/id.json` | **Create** | Kamus rujukan istilah bahasa Indonesia standar. |
| `resources/views/layouts/dashboard.blade.php` | **Modify** | Mengganti tag statis `<html lang="id">` menjadi dinamis, serta membungkus string navigasi (Sidebar, Topbar, User Menu) dengan helper `__()`. |
| `resources/views/layouts/peserta.blade.php` | **Modify** | Mengganti tag statis `<html lang="id">` dan membungkus string navigasi portal peserta dengan helper `__()`. |
| `resources/views/superadmin/akun/index.blade.php` | **Modify** | Menginternasionalkan label kartu preferensi bahasa & tema, form password, dan tombol simpan. |
| `resources/views/admin/pengaturan/index.blade.php` | **Modify** | Menginternasionalkan label kartu preferensi bahasa & tema di portal Admin. |
| `resources/views/pembimbing/pengaturan/index.blade.php` | **Modify** | Menginternasionalkan label kartu preferensi bahasa & tema di portal Pembimbing. |
| `resources/views/peserta/pengaturan/index.blade.php` | **Modify** | Menginternasionalkan label kartu preferensi bahasa & tema di portal Peserta. |
| `resources/views/components/flash-alert.blade.php` | **Modify** | Membungkus judul dan tombol tutup alert (`Berhasil`, `Peringatan`, dll.) dengan `__()`. |
| `tests/Feature/LanguageLocalizationTest.php` | **Create** | Feature test baru untuk memverifikasi middleware pergantian bahasa, persistensi session/cookie, dan rendering teks bahasa Inggris. |

---

## 5. Langkah Implementasi

- [x] **Langkah 1**: Buat middleware baru [`app/Http/Middleware/SetLocale.php`](file:///g:/MAGKOM/app/Http/Middleware/SetLocale.php) yang menangani penentuan locale aktif dari session atau cookie (`bahasa_portal`) dan memanggil `app()->setLocale()` serta `Carbon::setLocale()`.
- [x] **Langkah 2**: Daftarkan `SetLocale` pada grup `'web'` di [`app/Http/Kernel.php`](file:///g:/MAGKOM/app/Http/Kernel.php) setelah `ShareErrorsFromSession`.
- [x] **Langkah 3**: Buat berkas kamus terjemahan [`lang/en.json`](file:///g:/MAGKOM/lang/en.json) dan [`lang/id.json`](file:///g:/MAGKOM/lang/id.json) yang mencakup seluruh string antarmuka portal (navigasi, modul, tombol, profil, pengaturan, dan pesan sistem).
- [x] **Langkah 4**: Perbarui [`resources/views/layouts/dashboard.blade.php`](file:///g:/MAGKOM/resources/views/layouts/dashboard.blade.php) dan [`resources/views/layouts/peserta.blade.php`](file:///g:/MAGKOM/resources/views/layouts/peserta.blade.php) agar atribut `<html lang="...">` bersifat dinamis dan seluruh item navigasi/header menggunakan `__('...')`.
- [x] **Langkah 5**: Perbarui halaman pengaturan akun di keempat portal ([`superadmin/akun/index.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/akun/index.blade.php), [`admin/pengaturan/index.blade.php`](file:///g:/MAGKOM/resources/views/admin/pengaturan/index.blade.php), [`pembimbing/pengaturan/index.blade.php`](file:///g:/MAGKOM/resources/views/pembimbing/pengaturan/index.blade.php), [`peserta/pengaturan/index.blade.php`](file:///g:/MAGKOM/resources/views/peserta/pengaturan/index.blade.php)) serta komponen [`flash-alert.blade.php`](file:///g:/MAGKOM/resources/views/components/flash-alert.blade.php) dengan pembungkus `__()`.
- [x] **Langkah 6**: Buat automated test suite [`tests/Feature/LanguageLocalizationTest.php`](file:///g:/MAGKOM/tests/Feature/LanguageLocalizationTest.php) untuk menguji skenario perubahan bahasa ke Inggris dan kembali ke Indonesia untuk semua role.
- [x] **Langkah 7**: Jalankan full regression test suite (`php artisan test`) dan sinkronkan knowledge graph proyek via `graphify update .`.

---

## 6. Strategi Testing

### Automated Feature Tests (`tests/Feature/LanguageLocalizationTest.php`):
1. **Locale Default**: Memastikan akses tamu maupun pengguna login tanpa pengaturan preferensi menghasilkan `app()->getLocale() === 'id'` dan tag `<html lang="id">`.
2. **Perubahan ke English**: Pengguna mengirimkan POST ke endpoint preferensi dengan `bahasa = 'en'`, memverifikasi session `bahasa_portal = 'en'` dan cookie `bahasa_portal = 'en'`.
3. **Verifikasi Terjemahan UI**: Memastikan halaman dashboard dan pengaturan yang diakses saat locale `en` merender teks bahasa Inggris (misalnya: `"Account Settings"`, `"My Profile"`, `"Participant Assessment"`, `"Save Changes"`).
4. **Keamanan Input Locale**: Mengirimkan nilai bahasa aneh/ilegal (misalnya `bahasa = 'xx'` atau payload berbahaya) dan memastikan sistem menolaknya atau otomatis fallback ke `'id'`.

### Manual & UI Verification:
- Buka halaman Pengaturan di browser (`http://127.0.0.1:8000`), ubah bahasa menjadi "English (US)", dan klik "Terapkan Perubahan".
- Verifikasi menu sidebar, header, dan kartu pengaturan berubah ke bahasa Inggris secara mulus.
- Ubah kembali ke "Indonesia (Default)" dan pastikan teks kembali ke bahasa Indonesia.

---

## 7. Risiko & Mitigasi

- **Risiko String Terlewat**: Teks tertentu di blade yang belum terbungkus `__()` tetap tampil dalam bahasa Indonesia.
  - *Mitigasi*: Pendekatan JSON translation menjamin tidak ada error atau broken layout. String yang belum terbungkus tetap tampil rapi dalam bahasa aslinya tanpa mengganggu fungsionalitas.
- **Risiko Session Expiry**: Ketika session kedaluwarsa, pengguna kehilangan preferensi bahasa yang dipilih.
  - *Mitigasi*: Nilai bahasa disimpan ganda di Session DAN Cookie permanen (`cookie()->forever('bahasa_portal', $bahasa)`). Middleware `SetLocale` akan membaca cookie jika session pengguna baru atau belum terbentuk.
- **Performa Parsing Bahasa**:
  - *Mitigasi*: Laravel meng-cache berkas JSON localization dalam memory saat request berlangsung, overhead eksekusi < 1 milidetik.

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Berkas middleware `SetLocale` aktif di grup `'web'`.
- [x] Berkas kamus `lang/en.json` dan `lang/id.json` terbuat dan terisi istilah resmi antarmuka.
- [x] Seluruh menu navigasi sidebar SuperAdmin, Admin, Pembimbing, dan Peserta berubah saat bahasa Inggris dipilih.
- [x] Halaman preferensi menampilkan status bahasa yang aktif secara konsisten.
- [x] Tes otomatis `LanguageLocalizationTest` dan seluruh 363 tes sistem lulus 100% tanpa regresi.
- [x] Repositori tetap bersih dan terdokumentasi.

---

## 9. Catatan Perubahan & Hasil Eksekusi

- **Hasil Pengujian**:
  - `LanguageLocalizationTest.php`: 6 passed (39 assertions).
  - Full Test Suite (`php artisan test`): 363 passed (1992 assertions), 0 failures.
  - Knowledge graph diperbarui melalui `graphify update .` (AST-only, 6108 nodes, 497 communities).
- **Saran Lanjutan (Out of Scope)**:
  - Penambahan tombol cepat pemilih bahasa (*language switcher icon*) di bilah navigasi atas (topbar) di samping tombol notifikasi, sehingga pengguna dapat berganti bahasa secara instan dari halaman mana pun tanpa harus masuk ke menu pengaturan.
