# Master Engineering Plan: Logika Cascading Jenjang-Bidang-Jurusan & Koreksi Teks Logbook Mingguan

**Tanggal Dokumen**: 2026-11-11  
**Status**: DRAFT FOR APPROVAL (Menunggu Persetujuan Pengguna)  
**Tingkat Prioritas**: P1 (Penyelarasan Logika Pendaftaran & Terminologi Siklus Logbook)  
**Lampiran Bukti Pengguna**:
- [Screenshot 1: Urutan 3 Pilihan Jenjang, Bidang, dan Jurusan](file:///C:/Users/galan/.gemini/antigravity/brain/6450cbfa-233f-47e6-9528-354e10ad9741/.user_uploaded/media_1794395331712_effb7d9b.png)
- [Screenshot 2: Teks Panel Kiri "Logbook harian lebih rapi"](file:///C:/Users/galan/.gemini/antigravity/brain/6450cbfa-233f-47e6-9528-354e10ad9741/.user_uploaded/media_1794395340236_f392c33b.png)

---

## 1. Tujuan
1. **Implementasi Logika Filter Bertingkat (*Cascading Dropdown*)**:
   - Menghubungkan logika pilihan antara **Jenjang Pendidikan** (`select#jenjang`), **Bidang Penempatan** (`select#bidang`), dan **Jurusan / Program Studi** (`select#jurusan`).
   - Ketika calon peserta memilih jenjang (`SMK` vs `Mahasiswa`) dan bidang tugas Dinas Kominfo, daftar jurusan pada dropdown ketiga otomatis disaring secara dinamis sehingga hanya jurusan yang relevan dengan jenjang dan bidang tersebut yang dapat dipilih.
   - Menyediakan status transisi yang jelas (misalnya: jika jenjang atau bidang belum dipilih, dropdown jurusan memberi panduan atau dinonaktifkan sementara hingga kriteria induk terpenuhi).
2. **Koreksi Teks Carousel Panel Kiri (*Logbook Mingguan*)**:
   - Mengubah judul carousel slide ke-2 pada panel kiri bersama dari *"Logbook harian lebih rapi"* menjadi **"Logbook mingguan lebih rapi"**.
   - Menyesuaikan deskripsi pendukung dari *"Catat kegiatan setiap hari..."* menjadi **"Catat kegiatan setiap minggu, kirim ke pembimbing, dan lihat statusnya langsung dari satu tempat."** agar selaras dengan aturan bisnis siklus logbook berkala MagKom.

---

## 2. Asumsi & Pertanyaan Terbuka
- **Asumsi 1 (Format Nilai Jenjang)**: Jenjang pendidikan dipetakan ke dua kategori database:
  - `SMK` $\rightarrow$ kategori `smk`.
  - `Mahasiswa (D3 / D4 / S1)` $\rightarrow$ kategori `mahasiswa`.
- **Asumsi 2 (Data Relasi Bidang & Jurusan)**: Data bidang dan jurusan telah tersedia melalui `$bidangList` (yang memuat relasi `$bidang->jurusan`) dan `$jurusanList` di `AuthController@showRegister`. Seluruh pemetaan relasi dapat ditransformasikan ke dataset client-side yang aman.
- **Asumsi 3 (Fallback Pilihan Jurusan)**: Jika suatu bidang tidak memiliki pembatasan spesifik untuk kategori jenjang tertentu, atau jika kombinasi tidak menghasilkan opsi khusus, sistem akan menampilkan seluruh jurusan aktif pada jenjang tersebut agar calon peserta tidak terblokir.

---

## 3. Evaluasi Pendekatan Teknis

### Opsi A (Rekomendasi Utama): Client-Side Cascading via Data Attributes / JSON Dataset
- **Mekanisme**:
  - Blade me-render data relasi secara aman pada DOM (via dataset JSON aman atau data-attributes pada opsi select).
  - Skrip ES Module di `resources/js/pages/auth/auth-depth.js` menangani event `change` pada `select#jenjang` dan `select#bidang`.
  - Logika filter menyaring array jurusan berdasarkan `kategori === selectedJenjang` dan asosiasi `bidang_id === selectedBidang`.
- **Kelebihan**:
  - Respon seketika (zero latency, zero network roundtrip).
  - Bekerja tanpa perlu endpoint API baru dan tidak membebani server database.
  - Mematuhi aturan strict CSP dan pemisahan arsitektur `AGENTS.md` (tidak ada inline handler `onchange` di Blade).
- **Kekurangan**: Tidak ada.

### Opsi B: Server-Side Dynamic AJAX Filtering via Fetch API
- **Mekanisme**:
  - Membuat route API baru `/api/jurusan/filter?jenjang=...&bidang_id=...`.
  - Setiap perubahan dropdown memicu HTTP request ke backend untuk mengambil opsi HTML/JSON baru.
- **Kekurangan**:
  - Memerlukan rute baru, middleware otorisasi, penanganan loading spinner, dan rawan gagal jika koneksi pengguna lambat/terputus.
  - Bertentangan dengan prinsip kesederhanaan frontend auth depth.

**Keputusan**: Menggunakan **Opsi A**.

---

## 4. Rincian Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/auth/partials/_left_panel.blade.php` | Modify | Koreksi teks slide ke-2 carousel dari "Logbook harian" menjadi "Logbook mingguan lebih rapi" dan deskripsi per minggu. |
| `resources/views/auth/register.blade.php` | Modify | 1. Berikan value value semantik pada `select#jenjang` (`smk` dan `mahasiswa`).<br>2. Tambahkan embedding dataset relasi bidang-jurusan via JSON terstruktur di elemen `<script id="cascading-jurusan-data" type="application/json">` atau data attributes.<br>3. Siapkan placeholder dinamis pada `select#jurusan`. |
| `resources/js/pages/auth/auth-depth.js` | Modify | Tambahkan fungsi `initCascadingEducationFields()` yang mendengarkan perubahan `jenjang` dan `bidang`, lalu secara reaktif memperbarui opsi pada `jurusan`. |
| `tests/Feature/AuthDepthVisualParityTest.php` | Modify | Perbarui assertion teks "Logbook mingguan lebih rapi" dan pastikan test suite memvalidasi logika dataset cascading. |

---

## 5. Langkah-Langkah Implementasi

- [ ] **Langkah 1: Perbarui Teks Carousel di `resources/views/auth/partials/_left_panel.blade.php`**
  - Ganti judul slide 2:
    ```html
    <h2 style="margin: 0; font-size: 30px; line-height: 1.2; font-weight: 800; letter-spacing: -0.01em">Logbook mingguan lebih rapi</h2>
    <p style="margin: 0; max-width: 380px; font-size: 15px; line-height: 1.6; color: rgba(255,255,255,.85)">Catat kegiatan setiap minggu, kirim ke pembimbing, dan lihat statusnya langsung dari satu tempat.</p>
    ```

- [ ] **Langkah 2: Perbarui Markup Form di `resources/views/auth/register.blade.php`**
  - Tetapkan atribut `value` pada `select#jenjang`:
    ```html
    <option value="">-- Pilih Jenjang Pendidikan --</option>
    <option value="smk">SMK</option>
    <option value="mahasiswa">Mahasiswa (D3 / D4 / S1)</option>
    ```
  - Tetapkan atribut `value` pada `select#bidang` (`$b->id` atau nama bidang).
  - Sematkan kamus data relasi yang aman via JSON payload di Blade:
    ```html
    <script id="register-jurusan-master" type="application/json">
      {!! json_encode($jurusanListPayload, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}
    </script>
    ```
  - Sediakan opsi default pada `select#jurusan`:
    ```html
    <option value="">-- Pilih Jurusan / Program Studi --</option>
    ```

- [ ] **Langkah 3: Tambahkan Logika Reaktif di `resources/js/pages/auth/auth-depth.js`**
  - Implementasikan fungsi `initCascadingEducationFields()`:
    1. Ambil referensi elemen `jenjangSelect`, `bidangSelect`, `jurusanSelect`, dan payload data master jurusan.
    2. Saat `jenjangSelect` atau `bidangSelect` berubah:
       - Baca nilai jenjang (`smk` / `mahasiswa`) dan id bidang terpilih.
       - Saring master list jurusan berdasarkan kecocokan `kategori` dan keterhubungan dengan `bidang_id`.
       - Render ulang elemen `<option>` di `jurusanSelect`.
       - Tampilkan teks bantuan status yang kontekstual (misal: "Menampilkan 4 jurusan relevan untuk Bidang Aptika pada jenjang SMK").

- [ ] **Langkah 4: Perbarui Pengujian Otomatis di `tests/Feature/AuthDepthVisualParityTest.php`**
  - Perbarui assertion dari `assertSee('Logbook harian lebih rapi')` menjadi `assertSee('Logbook mingguan lebih rapi')`.
  - Tambahkan assertion untuk memvalidasi keberadaan master payload data jurusan di halaman register.

- [ ] **Langkah 5: Kompilasi Aset & Verifikasi Quality Gates**
  - `npm run build`
  - `./vendor/bin/pint --test`
  - `./vendor/bin/phpstan analyse --memory-limit=1G`
  - `php artisan test`

---

## 6. Strategi Testing
- **Unit & Feature Test**:
  - `AuthDepthVisualParityTest`: Memverifikasi kemunculan teks "Logbook mingguan lebih rapi" dan struktur cascading dropdown.
  - `PendaftaranPesertaTest`: Memastikan tidak ada regresi pada endpoint pendaftaran.
- **Interactivity Test**:
  - Pilih SMK $\rightarrow$ Bidang IKP $\rightarrow$ Jurusan yang muncul hanya jurusan SMK yang terhubung ke IKP (misal: Desain Komunikasi Visual, TKJ, Multimedia).
  - Pilih Mahasiswa $\rightarrow$ Bidang Aptika $\rightarrow$ Jurusan yang muncul adalah Teknik Informatika, Sistem Informasi, Rekayasa Perangkat Lunak.
  - Reset pilihan jenjang $\rightarrow$ Opsi jurusan ter-reset secara bersih.

---

## 7. Risiko & Mitigasi
- **Risiko**: Database lokal atau seeder mungkin memiliki bidang yang belum dipetakan ke jurusan di tabel pivot `bidang_jurusan`.
- **Mitigasi**: Jika kombinasi jenjang & bidang tidak memiliki jurusan di pivot, algoritma filter memiliki *fallback* cerdas yang menampilkan seluruh jurusan pada jenjang tersebut dengan catatan kelompok umum, sehingga pengguna tidak akan menemui dropdown kosong yang membingungkan.

---

## 8. Kriteria Selesai (Definition of Done)
- [ ] Teks carousel panel kiri slide 2 berubah menjadi "Logbook mingguan lebih rapi" dan deskripsi per minggu.
- [ ] Dropdown Jenjang, Bidang, dan Jurusan terhubung secara dinamis via JavaScript murni.
- [ ] Dropdown Jurusan menyaring data secara presisi berdasarkan jenjang dan bidang yang dipilih.
- [ ] Kompilasi `npm run build` berhasil tanpa warning/error.
- [ ] Seluruh PHPUnit Feature Test lulus 100%.

