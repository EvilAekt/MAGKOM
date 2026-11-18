# Rencana Kerja: Penghapusan Kartu Sakelar Modul Fitur Sistem (Feature Toggles) dari SuperAdmin Pengaturan

| Informasi Dokumen | Nilai |
| :--- | :--- |
| **Tanggal Pembuatan** | 18 November 2026 |
| **Status Dokumen** | Selesai Diimplementasikan & Lolos Verifikasi |
| **Target Fitur** | Modul SuperAdmin Konfigurasi Sistem (`superadmin.pengaturan.index`) |
| **Target Branch** | `test-2` |
| **Referensi Aturan** | `.agents/rules/AGENTS.md` (Poin 1, 3.3, 4, 7, 9, 10, 11) & `web-planning` |

---

## 1. Analisis Kritis & Latar Belakang Masalah

### A. Konteks Antarmuka SuperAdmin
Berdasarkan tangkapan layar pengguna:
- Pada halaman **Parameter Konfigurasi Sistem SuperAdmin** (`resources/views/superadmin/pengaturan/index.blade.php`), saat ini terdapat kontainer:
  - **Header Kartu**: *Sakelar Modul Fitur Sistem (Feature Toggles)*
  - **Elemen Baris**: *Modul Pendaftaran Kelompok / Tim* dengan sakelar sakelar aktif/nonaktif (`fitur_pendaftaran_kelompok_aktif`).
- Di sidebar navigasi / footer akun, SuperAdmin mengakses menu ini melalui tautan *Pengaturan* / *Pengaturan Akun* (`route('superadmin.pengaturan.index')`).
- Sebelumnya, kartu ini sempat memuat beberapa sakelar modul fitur, namun modul lain (seperti survei kepuasan dan sertifikat) telah didekomisi total. Saat ini Kartu 4 hanya menyisakan satu-satunya toggle yaitu *Modul Pendaftaran Kelompok / Tim*.

### B. Masalah & Kebutuhan Pengguna
- Pengguna meminta agar bagian ini dihapus secara bersih dari SuperAdmin ("bagian pengaturan akun/sistem"), sehingga tampilan SuperAdmin tidak lagi menampilkan kartu Feature Toggle tersebut dan parameter operasional menjadi lebih ramping, relevan, serta tidak menyisakan residu markup atau CSS usang.
- Perlu dipastikan bahwa penghapusan ini dilakukan secara terukur (*zero regression*) tanpa merusak fungsi pendaftaran magang yang telah berjalan pada portal publik.

---

## 2. Evaluasi Opsi Arsitektur

| Aspek Evaluasi | Opsi 1: Pembersihan Khusus SuperAdmin (Rekomendasi) | Opsi 2: Dekomisi Total Pendaftaran Kelompok End-to-End |
| :--- | :--- | :--- |
| **Deskripsi** | Menghapus Kartu Feature Toggle, handler controller SuperAdmin, CSS toggle, dan test SuperAdmin. Fitur pendaftaran kelompok publik tetap berjalan normal dengan status bawaan *aktif* (`true`). | Menghapus toggle SuperAdmin SEKALIGUS menghapus seluruh fitur pendaftaran kelompok dari sistem (auth register, dynamic team roster card, validasi, database, dsb.). |
| **Kelebihan** | 1. Sesuai langsung dengan instruksi pengguna (*"hapus fitur ini dari superadmin bagian pengaturan akun... hilang dari super admin"*).<br>2. *Zero regression* pada peserta yang mendaftar atau pembimbing yang membina tim.<br>3. Perubahan minimal dan terisolasi (*low risk*). | Menghilangkan total konsep pendaftaran kelompok jika dinas memang tidak lagi menerima kelompok sama sekali. |
| **Kekurangan** | Parameter `fitur_pendaftaran_kelompok_aktif` di database atau fallback kode tetap ada dengan nilai default `1`. | Perubahan sangat masif dan destruktif, merusak tabel `kelompok_magang`, membatalkan fitur tim yang baru saja dibangun di Wave 4, serta bertentangan dengan prinsip *minimal change*. |
| **Rekomendasi** | **Sangat Direkomendasikan (Opsi 1)**. | *Tidak Direkomendasikan* kecuali pengguna secara eksplisit meminta pembongkaran total sistem pendaftaran tim. |

---

## 3. Asumsi Kerja Logis & Pertanyaan Konfirmasi

### Asumsi Kerja:
1. **Pendaftaran Kelompok Tetap Diizinkan**: Di sisi publik (`AuthController` & `PendaftaranRequest`), jika parameter database tidak lagi diatur oleh SuperAdmin, sistem tetap mengizinkan pendaftaran tim (fallback `true` / nilai database default `'1'`).
2. **Kartu 4 Dihapus Utuh**: Karena *Modul Pendaftaran Kelompok / Tim* adalah satu-satunya sakelar di dalam *Kartu 4: Sakelar Modul Fitur Sistem*, maka seluruh Kartu 4 di Blade dihilangkan agar tidak menyisakan kartu kosong.
3. **Indikator Total Parameter Berkurang 1**: Counter `TOTAL PARAMETER` pada tile stat atas akan otomatis berubah dari 12 menjadi 11.

---

## 4. Ruang Lingkup Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :---: | :--- |
| `resources/views/superadmin/pengaturan/index.blade.php` | **Modify** | Menghapus blok Kartu 4 (*Sakelar Modul Fitur Sistem (Feature Toggles)*, baris 286–327). |
| `app/Http/Controllers/SuperAdmin/PengaturanController.php` | **Modify** | 1. Hapus `'fitur_pendaftaran_kelompok_aktif'` dari array `$kunciOperasional` (baris 44).<br>2. Hapus validasi `'fitur_pendaftaran_kelompok_aktif' => ['nullable', 'boolean']` dari method `update()` (baris 80).<br>3. Hapus blok penyimpanan setting `$settingsToSave['fitur_pendaftaran_kelompok_aktif']` (baris 109–111). |
| `resources/css/pages/superadmin/pengaturan.css` | **Modify** | Membersihkan selector kelas toggle `.cfg-toggle-*` dan styling dark mode-nya yang tidak lagi digunakan halaman mana pun. |
| `tests/Feature/PredikatBatasValidationTest.php` | **Modify** | 1. Update assertion `totalParameter` dari 12 menjadi 11.<br>2. Ganti `assertSee('Modul Pendaftaran Kelompok / Tim')` menjadi `assertDontSee('Modul Pendaftaran Kelompok / Tim')` dan `assertDontSee('Sakelar Modul Fitur Sistem')`.<br>3. Hapus `'fitur_pendaftaran_kelompok_aktif' => 1` dari payload test `test_accepts_valid_operational_settings_and_logs_audit`. |
| `tests/Feature/SuperAdminFeatureToggleTest.php` | **Modify** | Sesuaikan skenario pengujian SuperAdmin agar fokus menguji parameter operasional yang tersisa tanpa bergantung pada toggle kelompok yang sudah dihapus dari antarmuka SuperAdmin. |

---

## 5. Langkah Implementasi Terstruktur (Atomic Checklist)

### Fase 1: Perubahan Backend & Controller SuperAdmin
- [x] **Langkah 1.1**: Buka [`app/Http/Controllers/SuperAdmin/PengaturanController.php`](file:///g:/MAGKOM/app/Http/Controllers/SuperAdmin/PengaturanController.php).
- [x] **Langkah 1.2**: Hapus `'fitur_pendaftaran_kelompok_aktif'` dari daftar `$kunciOperasional` di method `index()`.
- [x] **Langkah 1.3**: Hapus baris validasi `'fitur_pendaftaran_kelompok_aktif'` di method `update()`.
- [x] **Langkah 1.4**: Hapus blok kondisional `$request->has('fitur_pendaftaran_kelompok_aktif')` saat menyusun `$settingsToSave`.

### Fase 2: Perubahan Tampilan Blade (Sesuai Aturan AGENTS.md Poin 3.3)
- [x] **Langkah 2.1**: Buka [`resources/views/superadmin/pengaturan/index.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/pengaturan/index.blade.php).
- [x] **Langkah 2.2**: Hapus blok `<!-- KARTU 4: SAKELAR MODUL FITUR SISTEM (FEATURE TOGGLES) -->` (baris 286 s/d 327) secara bersih.

### Fase 3: Pembersihan Gaya Visual (CSS) & Kompilasi Aset
- [x] **Langkah 3.1**: Buka [`resources/css/pages/superadmin/pengaturan.css`](file:///g:/MAGKOM/resources/css/pages/superadmin/pengaturan.css).
- [x] **Langkah 3.2**: Hapus deklarasi styling `.cfg-toggle-list`, `.cfg-toggle-row`, `.cfg-toggle-info`, `.cfg-toggle-icon`, `.cfg-toggle-title`, `.cfg-toggle-switch`, `.cfg-toggle-checkbox`, `.cfg-toggle-slider`, `.cfg-toggle-status-text`, serta blok `[data-theme="dark"]` yang relevan.
- [x] **Langkah 3.3**: Jalankan `npm run build` untuk memvalidasi kompilasi bundle Vite.

### Fase 4: Penyelarasan Automated Test Suite
- [x] **Langkah 4.1**: Perbarui [`tests/Feature/PredikatBatasValidationTest.php`](file:///g:/MAGKOM/tests/Feature/PredikatBatasValidationTest.php):
  - Ubah `assertViewHas('totalParameter', 12)` menjadi `11`.
  - Tambahkan assertion `assertDontSee('Sakelar Modul Fitur Sistem')` dan `assertDontSee('Modul Pendaftaran Kelompok / Tim')`.
- [x] **Langkah 4.2**: Perbarui [`tests/Feature/SuperAdminFeatureToggleTest.php`](file:///g:/MAGKOM/tests/Feature/SuperAdminFeatureToggleTest.php) agar selaras dengan skema formulir SuperAdmin baru.
- [x] **Langkah 4.3**: Jalankan `php artisan test` untuk memverifikasi bahwa seluruh test suite (termasuk `PendaftaranKelompokTest.php`) lulus 100%.

---

## 6. Strategi Pengujian & Quality Gate

1. **Uji Antarmuka SuperAdmin (Feature Test)**:
   - `php artisan test tests/Feature/PredikatBatasValidationTest.php`
   - Verifikasi respon status HTTP 200 OK.
   - Verifikasi bahwa teks "Sakelar Modul Fitur Sistem" dan "Modul Pendaftaran Kelompok / Tim" **TIDAK ADA** (`assertDontSee`).
   - Verifikasi total parameter operasional bernilai `11`.
2. **Uji Pengiriman Formulir Update SuperAdmin**:
   - `php artisan test tests/Feature/SuperAdminFeatureToggleTest.php`
   - Memastikan SuperAdmin dapat menyimpan parameter kuota, batas pembimbing, retensi akun, dan SLA tanpa ada error validasi.
3. **Uji Regresi Pendaftaran Kelompok**:
   - `php artisan test tests/Feature/PendaftaranKelompokTest.php`
   - Memastikan 15 pengujian alur pendaftaran mandiri & kelompok tetap `PASS` 100%.
4. **Analisis Statis & Standar Kode**:
   - `./vendor/bin/pint --test` (Format PSR-12).
   - `./vendor/bin/phpstan analyse --memory-limit=1G` (Analisis statis bebas error).
   - `npm run build` (Kompilasi frontend Vite aman).

---

## 7. Analisis Risiko & Mitigasi

| Potensi Risiko | Tingkat | Dampak | Mitigasi |
| :--- | :---: | :--- | :--- |
| **Pendaftaran kelompok publik ikut mati** | Rendah | Calon peserta magang tidak bisa memilih opsi kelompok. | Jangan hapus nilai `'fitur_pendaftaran_kelompok_aktif'` dari database seeder dan pertahankan fallback `true` pada `AuthController` & `PendaftaranRequest`. |
| **Test suite gagal karena perubahan counter** | Sedang | CI pipeline merah pada counter parameter sistem. | Perbarui test assertion dari 12 menjadi 11 secara terkoordinasi di `PredikatBatasValidationTest`. |
| **Sisa CSS tak terpakai (*dead code*)** | Rendah | Ukuran bundle stylesheet bertambah tanpa fungsi. | Bersihkan kelas `.cfg-toggle-*` di `pengaturan.css` dan jalankan `npm run build`. |

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Kartu "Sakelar Modul Fitur Sistem (Feature Toggles)" dan switch "Modul Pendaftaran Kelompok / Tim" bersih 100% dan tidak lagi muncul di halaman `superadmin.pengaturan.index`.
- [x] Controller `PengaturanController.php` tidak lagi memproses atau mengekspos field `fitur_pendaftaran_kelompok_aktif`.
- [x] Counter `TOTAL PARAMETER` menampilkan angka 11 yang akurat.
- [x] CSS toggle yang usang telah dibersihkan dan `npm run build` berhasil tanpa warning.
- [x] Seluruh pengujian fitur (`PredikatBatasValidationTest`, `SuperAdminFeatureToggleTest`, `PendaftaranKelompokTest`) berstatus **PASS** hijau.
- [x] Formatting kode memenuhi standar Pint dan PHPStan lolos.
