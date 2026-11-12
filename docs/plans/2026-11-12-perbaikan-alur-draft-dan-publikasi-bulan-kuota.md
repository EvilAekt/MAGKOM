# Rencana Kerja: Klarifikasi Arsitektur & Perbaikan Alur Draft Bulan Magang (`/admin/kuota`)

**Tanggal:** 12 November 2026  
**Status:** Implementasi Selesai & Terverifikasi Test Suite Penuh (`891 passed`, menunggu verifikasi visual browser manual akibat CAPTCHA pada `/login`)  
**Branch Saat Ini:** `test-2`  
**Referensi Visual Pengguna:**
- Gambar 1: Modal **Buat bulan baru** (`#modalBuatBulan`) dengan Status awal **Draft** (`media_1794466390016_8c0d2750.png`)
- Gambar 2: Modal **Ubah bulan** (`#modalUbahBulan`) menampilkan data `Febuari` berstatus **Draft** (`media_1794466631133_f9b3a4e0.png`)
**Aturan Repositori:** `.agents/rules/AGENTS.md` (PHP 8.3, Laravel 10, Vanilla CSS di `resources/css/`, Vanilla JS ES Modules di `resources/js/`, tidak menambah CSS/JS inline di Blade, dan persetujuan eksplisit sebelum modifikasi Blade)

---

## 1. Tujuan & Jawaban Konsultasi Senior Developer

### A. Jawaban Logika Bisnis & Arsitektur Web
Secara standar arsitektur web (**CRUD State Lifecycle**), perilaku di mana bulan berstatus **Draft** yang sudah disimpan dikelola dan dibuka melalui **`Ubah bulan`** (Gambar 2) **SUDAH BENAR** — **bukan** lewat menu **`Buat bulan baru`** (Gambar 1):
1. **`Buat bulan baru` (`POST /admin/kuota/bulan`)** adalah operasi **Create** untuk membuat periode *record* baru di tabel `bulan_magang`. Jika membuka Draft diletakkan di menu ini, fungsi *Create* dan *Edit/Publish* akan bercampur serta rawan memicu data ganda.
2. **`Ubah bulan` (`PUT /admin/kuota/bulan/{bulanMagang}`)** adalah operasi **Update/Publish** terhadap periode yang sudah tersimpan di database (sudah memiliki `id`, nama `Febuari`, tanggal buka/tutup/mulai, dan flag `aktif = false`). Mengubahnya dari **Draft → Dibuka** memang merupakan transisi status pada entitas eksisting.

### B. Akar Masalah Mengapa Alur Saat Ini Membingungkan (Temuan Codebase)
Hasil inspeksi terhadap [`KuotaIndexQuery.php`](file:///d:/laragon/www/MAGKOM/app/Queries/Admin/Kuota/KuotaIndexQuery.php) dan [`resources/views/admin/kuota/index.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/kuota/index.blade.php) menemukan **4 celah nyata**:
1. **Bug Visibilitas Dropdown**: [`KuotaIndexQuery.php`](file:///d:/laragon/www/MAGKOM/app/Queries/Admin/Kuota/KuotaIndexQuery.php#L34-L36) memfilter `BulanMagang::where('aktif', true)`. Akibatnya, bulan `Draft` (`aktif = false`) **hilang dari dropdown `Bulan Magang:`** begitu admin pindah bulan atau membuka ulang `/admin/kuota` (padahal [baris 1057 Blade](file:///d:/laragon/www/MAGKOM/resources/views/admin/kuota/index.blade.php#L1057) sebenarnya menyiapkan `{{ $b->aktif ? '(Aktif)' : '' }}`). Draft hanya kebetulan terlihat sesaat setelah simpan karena redirect [`KuotaController::storeBulan()`](file:///d:/laragon/www/MAGKOM/app/Http/Controllers/Admin/KuotaController.php#L114) mengirim `?bulan_magang_id={id}`.
2. **Bug Logika Status di Hero Banner**: [`KuotaIndexQuery.php`](file:///d:/laragon/www/MAGKOM/app/Queries/Admin/Kuota/KuotaIndexQuery.php#L171-L181) menghitung `$isPendaftaranBuka` hanya dengan `$now->between($tglBuka, $tglTutup)` **tanpa mengecek `$selectedBulan->aktif`**, sementara Blade ([baris 929–939](file:///d:/laragon/www/MAGKOM/resources/views/admin/kuota/index.blade.php#L929-L939)) hanya punya badge **`Dibuka`** dan **`Ditutup`**. Akibatnya, bulan `Draft` bisa tampil salah sebagai **`Ditutup`** (jika `tgl_buka` di masa depan seperti `12/03/2027`) atau keliru tampil **`Dibuka`** (jika hari ini di antara `tgl_buka..tgl_tutup`).
3. **Label Dropdown & Empty State**: Dropdown `Bulan Magang:` belum memberi label **`(Draft)`** pada bulan non-aktif, dan jika seluruh bulan di database masih berstatus `Draft`, blok `.month-switch-strip` ([baris 1041](file:///d:/laragon/www/MAGKOM/resources/views/admin/kuota/index.blade.php#L1041)) beserta tombol **`Buat bulan baru`** ikut hilang.
4. **Hambatan UX untuk Publikasi Draft**: Saat melihat bulan `Draft`, admin hanya melihat tombol **`Ubah bulan`** yang berkonotasi "mengedit data yang salah", tanpa indikator/jalur cepat **`Buka bulan`** untuk mempublikasikan Draft tersebut ke portal publik.

### C. Hasil Akhir yang Diharapkan
1. Bulan berstatus **Draft** tetap tersimpan dan **selalu tampil** pada dropdown **`Bulan Magang:`** dengan label jelas **`(Draft)`** agar mudah ditemukan kembali kapan saja.
2. Saat halaman `/admin/kuota` dibuka tanpa query parameter, sistem memprioritaskan bulan **`aktif = true`** lebih dulu (dan fallback ke Draft jika belum ada bulan aktif).
3. **Hero Banner** menampilkan status yang jujur dan akurat: **`Draft`**, **`Terjadwal`** (aktif & `tgl_buka` belum tiba), **`Dibuka`** (aktif & dalam jadwal buka–tutup), atau **`Ditutup`** (aktif & sudah lewat `tgl_tutup`).
4. Ketika periode yang dipilih berstatus **`Draft`**, admin mendapat tombol kontekstual **`Buka bulan`** di samping **`Ubah bulan`** yang langsung membuka modal `#modalUbahBulan` dengan opsi **`Dibuka`** sudah terpilih sehingga jadwal dapat diperiksa sekilas sebelum disimpan.

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi Kerja Logis
1. **Pemisahan Create vs Update Tetap Dijaga**: `Buat bulan baru` (`POST /admin/kuota/bulan`) hanya membuat periode baru; mengaktifkan/mengubah Draft menggunakan endpoint eksisting `PUT /admin/kuota/bulan/{bulanMagang}` (`KuotaController::updateBulan` + `UpdateBulanMagangAction`) beserta audit trail `AuditService`.
2. **Portal Publik & Pendaftaran Tidak Berubah**: `BulanStatusService`, `LandingController`, dan `AuthController` sudah memblokir `aktif = false` dari landing page dan registrasi; perilaku tersebut dipertahankan 100%.
3. **Isolasi CSS/JS Baru (`AGENTS.md` Pasal 3–5)**: Tidak menambahkan `<style>`, `style="..."`, `<script>`, maupun `onclick="..."` baru di file Blade. Styling pill/tombol baru ditaruh di `resources/css/pages/admin/kuota-draft.css` dan interaksi JS ditaruh di `resources/js/pages/admin/kuota-draft.js` (di-import melalui `app.css` & `app.js` tanpa membuat entry point Vite baru).
4. **Kompatibilitas Key View Data**: Seluruh 10 key eksisting pada return array `KuotaIndexQuery::getIndexData()` tetap dipertahankan agar test eksisting (`AdminSuperAdminArchitectureAndSecurityTest`) tetap hijau.

### Pertanyaan Konfirmasi (Membutuhkan Persetujuan Sebelum Implementasi)
1. **Izin Perubahan Blade (`AGENTS.md` Pasal 3.3)**: Apakah Anda menyetujui perubahan markup minimal pada [`resources/views/admin/kuota/index.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/kuota/index.blade.php) untuk menampilkan label `(Draft)` di dropdown, badge status `Draft`/`Terjadwal` di Hero Banner, tombol `Buka bulan`, dan fallback CTA di empty state?
2. **Alur Klik Tombol `Buka bulan`**: Apakah Anda setuju tombol **`Buka bulan`** membuka modal **`Ubah bulan`** dengan pill **`✓ Dibuka`** sudah terpilih otomatis agar Anda bisa memverifikasi tanggal (`tgl_buka`, `tgl_tutup`, `bulan_mulai`) sebelum klik `Simpan perubahan` (**direkomendasikan**), ataukah Anda ingin tombol 1-klik langsung submit publikasi tanpa konfirmasi modal?

---

## 3. Evaluasi Opsi Arsitektur & Pendekatan Teknis

| Aspek | Opsi A: Perbaiki Visibilitas & Status Draft di `KuotaIndexQuery` + Pill Status + Tombol Kontekstual `Buka bulan` via `#modalUbahBulan` *(Direkomendasikan)* | Opsi B: Tambah Endpoint Baru (`PATCH /publish`) + Section/Tabel Khusus "Daftar Draft" Terpisah di `/admin/kuota` |
| :--- | :--- | :--- |
| **Kepatuhan Arsitektur** | Menggunakan route `admin.kuota.bulan.update`, validasi, `lockForUpdate()`, dan `AuditService` eksisting. Nol perubahan skema DB/route contract. | Menambah route, controller method, dan section UI baru di luar baseline `AGENTS.md`. |
| **Pengalaman Pengguna (UX)** | Admin melihat status `Draft` dengan jelas di dropdown & hero, dan tinggal klik `Buka bulan` -> cek jadwal sekilas -> `Simpan perubahan`. | Daftar draft terpisah dari tabel kuota bidang, dan 1-klik publish berisiko mengaktifkan tanggal/kuota yang belum selesai dicek. |
| **Keamanan & Aksesibilitas** | Aman (tetap melewati validasi tanggal dan RBAC non-SuperAdmin); badge & tombol aksesibel. | Risiko salah publikasi tanpa review tanggal + memperluas permukaan perubahan UI. |
| **Dampak Perubahan** | Sangat terfokus: 1 query class, 1 view Blade (minimal), 1 modul CSS + 1 modul JS via Vite entry existing, dan test. | Perubahan besar pada layout, controller, dan route. |

**Rekomendasi:** Pilih **Opsi A**, karena memperbaiki akar masalah hilangnya Draft serta inkonsistensi status Hero Banner secara menyeluruh, menjaga alur review tanggal sebelum publish ke publik, dan meminimalkan perubahan sesuai `AGENTS.md`.

---

## 4. Perubahan File

| File | Aksi (`Create`/`Modify`/`Delete`) | Ringkasan Perubahan |
| :--- | :--- | :--- |
| [`app/Queries/Admin/Kuota/KuotaIndexQuery.php`](file:///d:/laragon/www/MAGKOM/app/Queries/Admin/Kuota/KuotaIndexQuery.php) | Modify | 1. Hapus filter `where('aktif', true)` pada `$bulanList` agar periode `Draft` (`aktif = false`) tetap muncul di dropdown.<br>2. Saat `$selectedBulanId === 0`, prioritaskan memilih bulan `aktif = true` pertama (`$bulanList->firstWhere('aktif', true) ?? $bulanList->first()`).<br>3. Hitung `$isDraft`, `$isTerjadwal`, dan perketat `$isPendaftaranBuka` agar wajib bernilai `false` jika `!$selectedBulan->aktif`. |
| [`resources/views/admin/kuota/index.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/kuota/index.blade.php) | Modify | 1. Tampilkan pill status **`Draft`** / **`Terjadwal`** / **`Dibuka`** / **`Ditutup`** beserta keterangan waktu/draft di Hero Banner.<br>2. Tampilkan penanda `(Draft)` untuk item `aktif = false` di dropdown `Bulan Magang:`.<br>3. Tambahkan tombol **`Buka bulan`** (`data-kuota-action="publish-draft"`) di sebelah **`Ubah bulan`** saat `$selectedBulan` berstatus `Draft` dan user bukan SuperAdmin.<br>4. Tambahkan tombol **`Buat bulan baru`** (`data-kuota-action="open-buat-bulan"`) pada empty state hero ketika belum ada bulan sama sekali. |
| [`resources/css/pages/admin/kuota-draft.css`](file:///d:/laragon/www/MAGKOM/resources/css/pages/admin/kuota-draft.css) | Create | Styling scoped untuk `.hero-status-draft`, `.hero-status-scheduled`, dot indikator status, dan tombol `.btn-publish-draft-action` menggunakan design token `resources/css/base/variables.css` (termasuk dukungan dark mode). |
| [`resources/css/app.css`](file:///d:/laragon/www/MAGKOM/resources/css/app.css) | Modify | Impor `./pages/admin/kuota-draft.css`. |
| [`resources/js/pages/admin/kuota-draft.js`](file:///d:/laragon/www/MAGKOM/resources/js/pages/admin/kuota-draft.js) | Create | ES module terstruktur (guarded agar tidak double-bind) untuk menangani `[data-kuota-action="publish-draft"]` (buka `#modalUbahBulan` & otomatis aktifkan status **`Dibuka`**) serta `[data-kuota-action="open-buat-bulan"]`. |
| [`resources/js/app.js`](file:///d:/laragon/www/MAGKOM/resources/js/app.js) | Modify | Impor `./pages/admin/kuota-draft`. |
| [`tests/Feature/AdminKuotaRevampTest.php`](file:///d:/laragon/www/MAGKOM/tests/Feature/AdminKuotaRevampTest.php) | Modify | Tambah feature test: Draft tetap muncul di dropdown dengan label `(Draft)`, default memilih bulan aktif, Hero Banner menampilkan status `Draft` (bukan `Dibuka`) meski tanggal masuk rentang buka–tutup, tombol `Buka bulan` muncul untuk Draft (dan tidak untuk SuperAdmin/bulan aktif), serta alur mengubah Draft menjadi `Dibuka` via `admin.kuota.bulan.update`. |

---

## 5. Langkah Implementasi

- [x] **Langkah 1 — Konfirmasi & Persetujuan Pengguna**: Pastikan persetujuan eksplisit untuk perubahan markup Blade (`resources/views/admin/kuota/index.blade.php`) dan opsi UX tombol `Buka bulan`.
- [x] **Langkah 2 — Perbaiki Query & Kalkulasi Status di [`app/Queries/Admin/Kuota/KuotaIndexQuery.php`](file:///d:/laragon/www/MAGKOM/app/Queries/Admin/Kuota/KuotaIndexQuery.php)**:
  - Ubah query `$bulanList` menjadi `BulanMagang::orderBy('bulan_mulai', 'asc')->get();`.
  - Ubah fallback `$selectedBulanId === 0` menjadi:
    `$selectedBulanId = (int) (($bulanList->firstWhere('aktif', true) ?? $bulanList->first())?->id ?? 0);`.
  - Hitung `$isDraft = $selectedBulan ? ! $selectedBulan->aktif : false;`.
  - Pastikan `$isPendaftaranBuka` hanya bernilai `true` apabila `$selectedBulan && $selectedBulan->aktif && $now->between($tglBuka, $tglTutup)`.
  - Hitung `$isTerjadwal = $selectedBulan && $selectedBulan->aktif && ! $isPendaftaranBuka && $tglBuka->isFuture()`.
  - Kembalikan `isDraft` dan `isTerjadwal` di array hasil `getIndexData()` beserta pembaruan PHPDoc return shape (pertahankan semua 10 key lama).
- [x] **Langkah 3 — Tambah Styling Scoped di [`resources/css/pages/admin/kuota-draft.css`](file:///d:/laragon/www/MAGKOM/resources/css/pages/admin/kuota-draft.css) & [`resources/css/app.css`](file:///d:/laragon/www/MAGKOM/resources/css/app.css)**:
  - Definisikan style `.hero-status-draft` (aksen amber/gold `--amber-200`/`--navy-900`), `.hero-status-scheduled`, `.hero-status-dot`, `.hero-dot-draft`, `.hero-dot-scheduled`, `.hero-empty-actions`, dan `.btn-publish-draft-action`.
  - Impor file baru pada `resources/css/app.css`.
- [x] **Langkah 4 — Tambah Modul JS ES di [`resources/js/pages/admin/kuota-draft.js`](file:///d:/laragon/www/MAGKOM/resources/js/pages/admin/kuota-draft.js) & [`resources/js/app.js`](file:///d:/laragon/www/MAGKOM/resources/js/app.js)**:
  - Implementasikan fungsi `initKuotaDraftControls()` dengan flag `data-kuota-draft-bound` agar listener tidak terpasang ganda.
  - Saat `[data-kuota-action="publish-draft"]` diklik: panggil pembuka modal `#modalUbahBulan` (atau tampilkan `#modalUbahBulan` + kunci scroll `document.body.style.overflow = 'hidden'`), aktifkan state **`Dibuka`** pada `#statusUbahInput`, `#btnUbahStatusDraft`, `#btnUbahStatusDibuka`, `#checkIconUbahDraft`, `#checkIconUbahDibuka`, dan arahkan fokus ke `#btnSubmitUbahBulan`.
  - Saat `[data-kuota-action="open-buat-bulan"]` diklik: buka `#modalBuatBulan`.
  - Impor modul di `resources/js/app.js`.
- [x] **Langkah 5 — Perbarui Markup Minimal pada [`resources/views/admin/kuota/index.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/kuota/index.blade.php)**:
  - Perbarui kondisi status pill di Hero Banner (`$isDraft` → `Draft`, `$isPendaftaranBuka` → `Dibuka`, `$isTerjadwal` → `Terjadwal`, else → `Ditutup`) beserta teks status countdown/keterangan.
  - Tambahkan tombol fallback `Buat bulan baru` (`data-kuota-action="open-buat-bulan"`) di blok `@else` hero saat `$selectedBulan` null.
  - Perbarui label option di dropdown `Bulan Magang:` menjadi `{{ $b->aktif ? '(Aktif)' : '(Draft)' }}`.
  - Tambahkan tombol `<button type="button" class="btn-publish-draft-action" data-kuota-action="publish-draft">Buka bulan</button>` ketika `$selectedBulan && ! $selectedBulan->aktif && ! auth()->user()?->isSuperAdmin()`.
- [x] **Langkah 6 — Tambah & Perbarui Feature Test di [`tests/Feature/AdminKuotaRevampTest.php`](file:///d:/laragon/www/MAGKOM/tests/Feature/AdminKuotaRevampTest.php)**:
  - Tambah test visibilitas Draft di dropdown, prioritas default bulan aktif, akurasi badge `Draft`/`Terjadwal` di Hero Banner, kemunculan tombol `Buka bulan` hanya saat status Draft untuk Admin, serta alur publikasi Draft -> Dibuka.
- [x] **Langkah 7 — Jalankan Quality Gate (PHP 8.3 & Vite)**:
  - [x] `npm run build` (`144 modules transformed`) & `node --check resources/js/pages/admin/kuota-draft.js`
  - [x] `php artisan view:clear` & `./vendor/bin/pint --test` (`passed`)
  - [x] `./vendor/bin/phpstan analyse --memory-limit=1G app/Queries/Admin/Kuota/KuotaIndexQuery.php` dengan `phpstan.neon` aktif (`[OK] No errors`; analisis paralel penuh seluruh 247 file sempat macet di `0/247` pada Windows)
  - [x] `php artisan test` full suite (`891 passed, 5894 assertions`)
- [ ] **Langkah 8 — Verifikasi UI / Browser**:
  - Terhalang CAPTCHA pada `/login`; menunggu verifikasi manual oleh pengguna di `http://127.0.0.1:8000/admin/kuota`.

---

## 6. Strategi Testing

1. **Happy Path (`AdminKuotaRevampTest`)**:
   - Admin membuat bulan baru `Febuari` berstatus `draft` -> redirect ke `/admin/kuota?bulan_magang_id={id}` menampilkan pill **`Draft`** dan tombol **`Buka bulan`**.
   - Admin membuka `/admin/kuota` tanpa parameter -> default memilih bulan `aktif = true`, namun `<option>` untuk `Febuari ... (Draft)` tetap ada di dropdown.
   - Admin memilih `Febuari` berstatus `draft` meski `tgl_buka <= today <= tgl_tutup` -> Hero Banner tetap menampilkan **`Draft`** (tidak menampilkan `Dibuka`).
   - Admin mempublikasikan `Febuari` lewat `PUT /admin/kuota/bulan/{id}` dengan `status_awal = dibuka` -> DB `aktif = true`; jika `tgl_buka` masa depan (`12/03/2027`), Hero Banner menampilkan **`Terjadwal`** beserta tanggal buka; jika `tgl_buka..tgl_tutup` mencakup hari ini, menampilkan **`Dibuka`**.
2. **Sad Path / Authorization**:
   - SuperAdmin mengakses `/admin/kuota?bulan_magang_id={draftId}` -> tetap dapat melihat indikator `Draft` dengan mode read-only, tombol **`Buka bulan`** & **`Ubah bulan`** tidak dirender, serta `PUT /admin/kuota/bulan/{id}` tetap ditolak `403`.
3. **Regression Suite**:
   - Seluruh `AdminKuotaRevampTest`, `AdminSuperAdminArchitectureAndSecurityTest`, `AdminChildPagesVisualTest`, `AdminSuperAdminPortalTest`, dan full `php artisan test` lulus 100%.

---

## 7. Risiko & Mitigasi

| Kategori | Risiko | Mitigasi |
| :--- | :--- | :--- |
| **Regression Test / Default Bulan** | Jika skenario test lain membuat bulan `Draft` dengan `bulan_mulai` lebih awal daripada bulan `Aktif`, default halaman bisa ikut bergeser bila query memakai `first()` saja. | Fallback default secara eksplisit memakai `$bulanList->firstWhere('aktif', true) ?? $bulanList->first()`. |
| **Bug Status Publik vs Admin** | `$isPendaftaranBuka` pada `AdminSuperAdminArchitectureAndSecurityTest` diuji bernilai `true` pada bulan aktif `2026-10-01..2026-10-31`. | Pastikan pengecekan `$selectedBulan->aktif` tetap menghasilkan `true` untuk bulan aktif yang sedang berada dalam periode pendaftaran. |
| **Performa & N+1 Query** | Penambahan bulan Draft ke `$bulanList`. | `BulanMagang::orderBy('bulan_mulai', 'asc')->get()` hanya 1 query ringan tanpa relasi tambahan untuk list dropdown. |
| **Keamanan (OWASP / CSRF / XSS)** | Pembuatan tombol & JS baru berpotensi memicu XSS jika menggunakan `innerHTML` atau melewati CSRF. | Tombol **`Buka bulan`** tetap memicu form modal `#modalUbahBulan` yang sudah dilindungi `@csrf` + `@method('PUT')` + middleware `role:admin,superadmin` & `superadmin.readonly`; JS tidak memakai `innerHTML`. |
| **Aksesibilitas (a11y)** | Pill status `Draft` di atas hero gradient biru berisiko kontras rendah. | Gunakan latar solid kontras tinggi (mis. `--yellow-100` dengan teks `--navy-900`) memenuhi WCAG AA dan fokus keyboard pada modal. |

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Bulan yang disimpan sebagai **Draft** tetap tampil di dropdown `Bulan Magang:` dengan label `(Draft)` dan dapat dibuka kembali kapan saja.
- [x] Default `/admin/kuota` tanpa parameter tetap menampilkan bulan aktif bila tersedia.
- [x] Hero Banner menampilkan badge & pesan status akurat (`Draft`, `Terjadwal`, `Dibuka`, `Ditutup`).
- [x] Tombol kontekstual `Buka bulan` muncul untuk Admin saat melihat bulan `Draft` dan mempermudah transisi ke `Dibuka` via modal `Ubah bulan`.
- [x] Tidak ada CSS/JS inline baru di Blade; seluruh CSS/JS baru dikelola melalui Vite (`npm run build` sukses).
- [x] Pint (`passed`), PHPStan pada `KuotaIndexQuery.php` (`phpstan.neon` aktif, `[OK] No errors`), dan seluruh test suite PHPUnit PHP 8.3 (`891 passed, 5894 assertions`) lulus 100%.

---

## Saran Lanjutan (Di Luar Scope Utama)

- Jika jumlah bulan magang nantinya bertambah banyak (tahunan), pertimbangkan menambahkan opsi *Archive* atau filter tahun pada dropdown `Bulan Magang:` agar daftar tidak terlalu panjang.
- Konsolidasikan inline `<style>` dan `<script>` legacy yang masih ada di [`resources/views/admin/kuota/index.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/kuota/index.blade.php) ke `resources/css/pages/admin/` & `resources/js/pages/admin/` sebagai pekerjaan terpisah apabila user meminta migrasi modul kuota.

