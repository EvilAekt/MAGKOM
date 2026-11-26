# Rencana Kerja: Penambahan Filter "Tugas" (Tugas, Selesai, Lewat Tenggat) pada Daftar Penugasan & Proyek Magang

**Dokumen Rencana:** `docs/plans/2026-11-26-filter-tugas-selesai-lewat-tenggat-peserta.md`  
**Tanggal:** 26 November 2026  
**Status:** Selesai Diimplementasikan (Completed & Verified)  
**Terkait:** Halaman Tugas Peserta (`resources/views/peserta/tugas/index.blade.php`, `resources/js/pages/peserta/tugas-index.js`, `app/Http/Controllers/Peserta/TugasController.php`)

---

## 1. Tujuan
Menambahkan tombol filter **"Tugas"** di dalam kontainer kartu *"Daftar Penugasan & Proyek Magang"* pada portal peserta, sehingga tab navigasi status memiliki 3 pilihan lengkap:
1. **Tugas** (dengan badge counter)
2. **Selesai** (dengan badge counter)
3. **Lewat Tenggat** (dengan badge counter)

Serta menyelaraskan logika penyaringan (*filtering logic*), pembaruan badge, dan penanganan status kosong (*empty state*).

---

## 2. Asumsi & Investigasi Codebase

### Analisis Kondisi Saat Ini:
1. Di atas kartu *"Daftar Penugasan & Proyek Magang"*, sudah ada filter jenis tugas (`.classroom-tipe-tabs`):
   - `Semua Tugas`
   - `Tugas Tim`
   - `Tugas Individu`
2. Di dalam kartu *"Daftar Penugasan & Proyek Magang"*, saat ini baru terdapat 2 filter status (`.classroom-tabs`):
   - `Selesai` (`data-filter="selesai"`, badge `$totalSelesai`)
   - `Lewat Tenggat` (`data-filter="terlambat"`, badge `$totalTerlambat`)
3. Pengontrol `app/Http/Controllers/Peserta/TugasController.php` telah menghitung metrik:
   - `$totalSemua`: total seluruh tugas
   - `$totalBelum`: tugas berstatus `belum`
   - `$totalProses`: tugas berstatus `proses`
   - `$totalSelesai`: tugas berstatus `selesai`
   - `$totalTerlambat`: tugas belum selesai yang deadline-nya telah lewat (`isPast()`)

---

## 3. Pendekatan Teknis & Evaluasi Logika Filter "Tugas"

Terdapat 2 opsi penafsiran logika untuk tombol filter **"Tugas"**:

### Opsi 1: "Tugas" sebagai Tugas Aktif / Belum Selesai (Direkomendasikan)
- **Definisi**: Menampilkan tugas yang masih harus dikerjakan oleh peserta (berstatus `belum` atau `proses`, yaitu `$status !== 'selesai'`).
- **Badge Counter**: `$totalTugas = $totalBelum + $totalProses` (atau `$totalSemua - $totalSelesai`).
- **Logika JavaScript**:
  ```javascript
  } else if (activeStatusFilter === 'tugas') {
      matchStatus = (status !== 'selesai');
  }
  ```
- **Kelebihan**:
  - Memberikan 3 status yang saling melengkapi dan tidak tumpang tindih secara operasional:
    - **Tugas**: apa yang sedang / perlu dikerjakan saat ini.
    - **Selesai**: apa yang sudah tuntas dan terverifikasi.
    - **Lewat Tenggat**: apa yang melewati deadline dan harus segera diprioritaskan.
  - Sangat intuitif bagi peserta magang karena di atas kartu sudah ada tombol *"Semua Tugas"*.
- **Kekurangan**: Jika peserta ingin melihat seluruh tugas tanpa memfilter status apa pun, mereka cukup mengklik ulang tombol yang sedang aktif (toggle uncheck) atau mengklik tab *"Semua Tugas"* di atas.

### Opsi 2: "Tugas" sebagai Reset / Menampilkan Semua Tugas (Status 'All')
- **Definisi**: Menampilkan semua tugas tanpa membedakan status (tugas berjalan, selesai, maupun lewat tenggat).
- **Badge Counter**: `$totalSemua`.
- **Logika JavaScript**:
  ```javascript
  } else if (activeStatusFilter === 'tugas' || activeStatusFilter === 'all') {
      matchStatus = true;
  }
  ```
- **Kelebihan**: Berfungsi sebagai tombol reset status filter instan di dalam kartu.
- **Kekurangan**: Sedikit redundan dengan tab *"Semua Tugas"* yang posisinya tepat di atas kartu.

**Rekomendasi:** **Opsi 1 (Tugas Aktif / Belum Selesai)**, karena memberikan nilai tambah fungsional yang nyata bagi peserta untuk fokus pada pekerjaan yang belum selesai.

---

## 4. Analisis Kebutuhan Perubahan Blade (Patuhi AGENTS.md Bagian 3.3)

Sesuai aturan **AGENTS.md Section 3.3**:
1. **Alasan Perubahan**: Diperlukan satu elemen tombol semantik `<button type="button" class="tab-pill" data-filter="tugas">` di dalam `.classroom-tabs` pada file Blade agar tombol filter *"Tugas"* tampil berdampingan dengan *"Selesai"* dan *"Lewat Tenggat"*.
2. **File yang Perlu Diubah**: `resources/views/peserta/tugas/index.blade.php` (baris 121–123).
3. **Pemberitahuan & Persetujuan**: Penambahan ini diajukan secara transparan kepada pengguna untuk mendapatkan persetujuan sebelum eksekusi.

---

## 5. Rencana Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/peserta/tugas/index.blade.php` | Modify | Menambahkan tombol `<button type="button" class="tab-pill" data-filter="tugas">` dengan label `Tugas` dan badge `$totalTugas` sebelum tombol `Selesai`. |
| `app/Http/Controllers/Peserta/TugasController.php` | Modify | Menghitung `$totalTugas = $totalBelum + $totalProses` dan mengoperkannya ke view serta meta JSON response. |
| `resources/js/pages/peserta/tugas-index.js` | Modify | 1. Menambahkan penanganan `activeStatusFilter === 'tugas'` pada `applyCombinedFilters()`.<br>2. Menambahkan pesan *empty state* khusus saat filter `tugas` tidak memiliki data (misal: "Hebat! Seluruh tugas telah selesai dikerjakan"). |
| `resources/css/pages/peserta/tugas-index.css` | Modify | Memastikan styling `.tab-pill[data-filter="tugas"]` konsisten dalam light mode dan dark mode. |
| `tests/Feature/PesertaTugasFilterTabsTest.php` | Modify | Memperbarui test assertion untuk memastikan ketiga filter (`Tugas`, `Selesai`, `Lewat Tenggat`) dirender dan berfungsi dengan tepat. |

---

## 6. Langkah Implementasi Terperinci

- [x] **Langkah 1: Perbarui Controller di `app/Http/Controllers/Peserta/TugasController.php`**
  - Hitung `$totalTugas = $totalBelum + $totalProses;`.
  - Operkan variabel `'totalTugas' => $totalTugas` ke `view('peserta.tugas.index')`.
  - Tambahkan `'totalTugas' => $totalTugas` pada meta JSON response.
- [x] **Langkah 2: Perbarui Markup di `resources/views/peserta/tugas/index.blade.php` (Setelah Izin)**
  - Di dalam `<div class="classroom-tabs" ...>`, sisipkan tombol pertama:
    ```html
    <button type="button" class="tab-pill" data-filter="tugas" role="tab" aria-selected="false">
        <span>{{ __('Tugas') }}</span>
        <span class="tab-pill-badge">{{ $totalTugas }}</span>
    </button>
    ```
- [x] **Langkah 3: Perbarui Logika JavaScript di `resources/js/pages/peserta/tugas-index.js`**
  - Pada `applyCombinedFilters()`:
    ```javascript
    if (activeStatusFilter === 'all') {
        matchStatus = true;
    } else if (activeStatusFilter === 'tugas') {
        matchStatus = (status !== 'selesai');
    } else if (activeStatusFilter === 'selesai') {
        matchStatus = (status === 'selesai');
    } else if (activeStatusFilter === 'terlambat') {
        matchStatus = isOverdue;
    }
    ```
  - Pada penanganan `emptyState`:
    ```javascript
    if (activeStatusFilter === 'tugas') {
        if (emptyTitle) emptyTitle.textContent = 'Tidak Ada Tugas Aktif';
        if (emptyDesc) emptyDesc.textContent = 'Hebat! Anda tidak memiliki tugas aktif yang perlu dikerjakan saat ini.';
    }
    ```
- [x] **Langkah 4: Periksa & Pastikan CSS di `resources/css/pages/peserta/tugas-index.css`**
  - Pastikan `.tab-pill` memiliki layout yang rapi saat 3 pill bersanding, termasuk pada layar mobile (`overflow-x: auto; gap: 10px;`).
- [x] **Langkah 5: Kompilasi Aset Vite (`npm run build`)**
  - Jalankan `npm run build` dan verifikasi keberhasilan kompilasi tanpa error.
- [x] **Langkah 6: Jalankan Pengujian PHPUnit & Linter Pint**
  - Perbarui assertion di `tests/Feature/PesertaTugasFilterTabsTest.php` untuk memvalidasi keberadaan `data-filter="tugas"`.
  - Jalankan `php artisan test tests/Feature/PesertaTugasFilterTabsTest.php`.
  - Jalankan `./vendor/bin/pint --test`.
- [x] **Langkah 7: Verifikasi Interaksi Browser**
  - Uji klik tombol `Tugas`: hanya tugas yang belum selesai yang tampil.
  - Uji klik tombol `Selesai`: hanya tugas selesai yang tampil.
  - Uji klik tombol `Lewat Tenggat`: hanya tugas lewat tenggat yang tampil.
  - Uji kombinasi dengan tab atas (`Tugas Tim` dan `Tugas Individu`).

---

## 7. Strategi Testing

1. **Feature Testing (PHPUnit 10)**:
   - `test_peserta_melihat_tiga_tab_filter_semua_selesai_dan_lewat_tenggat`: diperbarui untuk memvalidasi keberadaan tombol `data-filter="tugas"` beserta badge `$totalTugas`.
   - Menguji kebenaran meta JSON response yang memuat `totalTugas`.
2. **Quality Gate Checks**:
   - `npm run build`: kompilasi 100% lulus.
   - `./vendor/bin/pint --test`: formatting PSR-12 bersih.

---

## 8. Risiko & Mitigasi

| Risiko | Dampak | Mitigasi |
| :--- | :--- | :--- |
| **Kombinasi filter jenis dan filter status kosong** | Pengguna bingung jika kartu kosong. | Menampilkan pesan *empty state* yang kontekstual dan jelas. |
| **Tampilan meluap di mobile** | Tab bertumpuk atau keluar layar pada layar kecil. | Menggunakan container flex dengan `-webkit-overflow-scrolling: touch` dan `overflow-x: auto` yang sudah terpasang. |

---

## 9. Kriteria Selesai (Definition of Done)

- [x] Dokumen rencana kerja disetujui pengguna.
- [x] Terdapat 3 tombol pill di dalam kartu *Daftar Penugasan & Proyek Magang*: `Tugas`, `Selesai`, dan `Lewat Tenggat`.
- [x] Masing-masing pill menampilkan badge angka yang akurat dari backend.
- [x] Klik pill `Tugas` menyaring tugas yang masih aktif / belum selesai.
- [x] Klik pill `Selesai` menyaring tugas yang sudah tuntas.
- [x] Klik pill `Lewat Tenggat` menyaring tugas yang melewati batas waktu.
- [x] `npm run build` sukses.
- [x] PHPUnit test lulus 100%.
