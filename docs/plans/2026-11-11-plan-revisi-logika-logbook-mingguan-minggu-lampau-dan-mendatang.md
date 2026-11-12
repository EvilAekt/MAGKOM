# Rencana Kerja: Revisi Logika Pengisian Logbook Mingguan (Minggu Lampau & Minggu Mendatang)

## 1. Tujuan
Merevisi logika bisnis dan antarmuka pelaporan **Logbook Mingguan Peserta** (`/peserta/logbook`) agar:
1. **Minggu Sebelumnya (Lampau) Tetap Dapat Diisi**: Peserta magang yang belum mengisi logbook untuk minggu-minggu lampau (`minggu_ke < minggu_aktif`) diizinkan untuk melengkapi/mengisi logbook minggu lampau tersebut tanpa terhalang batas toleransi mundur harian (`logbook_maks_mundur_hari` 3 hari).
2. **Minggu Berjalan Dapat Diisi**: Peserta magang dapat mengisi logbook untuk minggu magang yang sedang aktif berjalan (`minggu_ke == minggu_aktif`).
3. **Minggu Selanjutnya (Mendatang) Dilarang Diisi**: Peserta magang **dilarang keras** dan diblokir secara sistem untuk mengisi logbook minggu depan/mendatang (`minggu_ke > minggu_aktif`). Pada antarmuka pengguna (UI), opsi minggu mendatang berstatus terkunci/nonaktif (*disabled*), dan pada backend divalidasi dengan pesan penolakan yang tegas (HTTP 422 / DomainException).

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi Kerja Logis:
1. **Satu Laporan per Minggu (Keunikan)**: Aturan keunikan satu catatan logbook per minggu per penempatan (`minggu_ke`, `penempatan_id`) tetap berlaku mutlak. Jika minggu lampau sudah pernah diisi, statusnya adalah "Sudah Diisi" dan hanya dapat diperbarui melalui fitur Edit Logbook (jika status logbook belum disetujui / dalam status revisi).
2. **Penentuan Minggu Aktif**: Minggu aktif penempatan (`minggu_aktif`) dihitung dari `tgl_mulai` penempatan peserta hingga tanggal hari ini (`today`), dengan rumus: `floor(diffInDays / 7) + 1`.
3. **Validasi Tanggal Pelaporan**:
   - Untuk minggu lampau (`minggu_ke < minggu_aktif`), tanggal pelaporan (`tanggal`) harus berada di dalam rentang periode minggu terkait (`tgl_awal` s/d `tgl_akhir` minggu tersebut) dan tidak boleh melebihi hari ini.
   - Untuk minggu berjalan (`minggu_ke == minggu_aktif`), tanggal pelaporan harus berada di antara `tgl_awal` minggu berjalan s/d `hari ini` (`today`).
   - Tanggal di masa depan (`tanggal > today`) tetap dilarang keras.
4. **Backward Compatibility Mode Fallback**: Jika request API/form tidak menyertakan `minggu_ke` (misal endpoint legacy/integrasi tertentu), sistem tetap mempertahankan fallback batas toleransi mundur `logbook_maks_mundur_hari` (3 hari) agar tidak merusak kontrak yang sudah ada.

---

## 3. Pendekatan Teknis (Evaluasi Opsi)

### Perbandingan Opsi:
- **Opsi A (Direkomendasikan) - Validasi Berbasis State Minggu Magang Terintegrasi (Defense-in-Depth: Backend + Frontend Dynamic Constraint)**:
  - **Backend (`LogbookRequest` & `CreateLogbookAction`)**:
    - Memeriksa relasi `penempatan` aktif peserta.
    - Jika `minggu_ke` dikirim:
      - Validasi `minggu_ke`: harus bernilai antara `1` sampai `penempatan->minggu_aktif`. Jika `minggu_ke > penempatan->minggu_aktif`, gagalkan validasi dengan pesan: *"Logbook untuk Minggu ke-{$val} belum dapat diisi karena periode minggu tersebut belum berjalan."*.
      - Validasi `tanggal`: harus berada dalam rentang magang (`>= penempatan->tgl_mulai` dan `<= today`). Batas mundur 3 hari (`after_or_equal:today - 3`) **tidak dikenakan** jika `minggu_ke <= penempatan->minggu_aktif`.
    - Di `CreateLogbookAction`: Tambahkan pemeriksaan pengaman di dalam transaksi terkunci (`lockForUpdate`): `if ($mingguKe && $mingguKe > $lockedPenempatan->minggu_aktif) throw new DomainException(...)`.
  - **Frontend (`index.blade.php` & `logbook-index.js`)**:
    - Dropdown `<select id="tambah_minggu_ke">`:
      - Minggu yang sudah diisi: `disabled` dengan label *"Minggu ke-X — Sudah Diisi"*.
      - Minggu lampau belum diisi: `enabled` dengan label *"Minggu ke-X (Periode) — Belum Diisi (Bisa Diisi)"*.
      - Minggu berjalan: `enabled` dengan label *"Minggu ke-X (Periode) — Minggu Berjalan"*.
      - Minggu mendatang (`minggu_ke > mingguAktif`): `disabled` dengan label *"Minggu ke-X (Periode) — Mendatang (Belum Dibuka)"*.
    - Input tanggal `<input type="date" id="tambah_tanggal">`:
      - Saat pengguna memilih minggu tertentu, JavaScript `onMingguChange()` secara dinamis mengatur atribut `min` ke tanggal awal minggu tersebut dan `max` ke tanggal akhir minggu tersebut (atau hari ini).
    - Stepper Chips (`weekly-stepper-wrap`):
      - Chip minggu lampau yang belum terisi dapat diklik langsung untuk memicu modal pengisian dengan minggu tersebut terpasang otomatis (`openTambahModal(mingguKe)`).
      - Chip minggu mendatang berstatus `is-upcoming disabled` dengan kursor `not-allowed` dan tidak memicu formulir.
  - *Kelebihan*: Sangat aman (tidak bisa di-bypass lewat API/cURL), pengalaman pengguna intuitif, transparan, dan mengatasi akar masalah secara menyeluruh.
  - *Kekurangan*: Memerlukan penyesuaian di Form Request, Action, Blade view, dan JavaScript.

- **Opsi B - Validasi Hanya pada Tampilan Frontend (Client-side Only)**:
  - Hanya menambahkan atribut `disabled` pada elemen `<option>` di Blade tanpa mengubah aturan validasi `LogbookRequest` atau `CreateLogbookAction`.
  - *Kelebihan*: Perubahan kode lebih sedikit.
  - *Kekurangan*: **Cacat fatal**. Peserta yang memilih minggu lampau akan tetap ditolak oleh `LogbookRequest` (karena aturan `after_or_equal:today - 3`), dan pengguna masih bisa mem-bypass form untuk mengisi minggu depan via Developer Tools atau API.

**Pilihan**: Menggunakan **Opsi A**.

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :---: | :--- |
| `app/Http/Requests/LogbookRequest.php` | **Modify** | 1. Izinkan pengisian tanggal lampau jika `minggu_ke <= penempatan->minggu_aktif` (lepaskan aturan kaku `subDays(3)` untuk mode mingguan).<br>2. Tambahkan aturan validasi `minggu_ke`: dilarang melebihi `penempatan->minggu_aktif`.<br>3. Pastikan `tanggal` tidak boleh di masa depan (`before_or_equal:today`). |
| `app/Actions/Peserta/Logbook/CreateLogbookAction.php` | **Modify** | Tambahkan validasi domain di dalam transaksi terkunci: lempar `DomainException` jika `minggu_ke > lockedPenempatan->minggu_aktif`. |
| `resources/views/peserta/logbook/index.blade.php` | **Modify** | 1. Pada dropdown `tambah_minggu_ke`: nonaktifkan (`disabled`) opsi minggu ke > `mingguAktif` dengan label informatif.<br>2. Pada stepper chip minggu: aktifkan klik pada minggu lampau belum terisi (`openTambahModal(mingguKe)`), nonaktifkan klik pada minggu mendatang.<br>3. Tambahkan atribut data `data-tgl-awal` dan `data-tgl-akhir` pada setiap option. |
| `resources/js/pages/peserta/logbook-index.js` | **Modify** | Perbarui `onMingguChange()` untuk memperbarui batas `min` dan `max` tanggal input secara dinamis sesuai minggu yang dipilih, serta mencegah pembukaan modal untuk minggu mendatang. |
| `tests/Feature/LogbookArchitectureAndSecurityTest.php` | **Modify** | Tambahkan unit & feature test untuk memastikan: minggu lampau belum terisi sukses disimpan, minggu berjalan sukses disimpan, dan minggu mendatang ditolak 422. |

---

## 5. Langkah Implementasi Terstruktur

### Tahap 1: Validasi Backend & Aturan Domain
- [ ] 1.1 Perbarui `app/Http/Requests/LogbookRequest.php`:
  - Ambil objek `penempatan` aktif milik peserta yang sedang login.
  - Jika `minggu_ke` ada dalam input:
    - Tambahkan custom validation atau rule: `minggu_ke` harus integer `>= 1` dan `<= $penempatan->minggu_aktif`.
    - Pesan error kustom jika `minggu_ke > minggu_aktif`: *"Logbook untuk Minggu ke-{$inputMinggu} belum dapat diisi karena periode minggu tersebut belum berjalan."*.
    - Tanggal pelaporan: `before_or_equal:today` dan `after_or_equal:{$penempatan->tgl_mulai->toDateString()}`.
  - Jika `minggu_ke` tidak ada dalam input (mode fallback):
    - Pertahankan batas toleransi `after_or_equal:today - maksMundurHari` untuk kompatibilitas mundur.
- [ ] 1.2 Perbarui `app/Actions/Peserta/Logbook/CreateLogbookAction.php`:
  - Di dalam `DB::transaction()` setelah pessimistic lock:
    - Cek: jika `$mingguKe > $lockedPenempatan->minggu_aktif`, lempar `DomainException("Logbook untuk Minggu ke-{$mingguKe} belum dapat diisi karena periode minggu tersebut belum berjalan.")`.

### Tahap 2: Penyesuaian Antarmuka Blade & Interaksi JavaScript
- [ ] 2.1 Perbarui `resources/views/peserta/logbook/index.blade.php`:
  - Pada `<select id="tambah_minggu_ke">`:
    - Beri atribut `disabled` untuk `$m['minggu_ke'] > $mingguAktif`.
    - Tambahkan data attributes `data-tgl-awal="{{ $m['tgl_awal']->toDateString() }}"` dan `data-tgl-akhir="{{ $m['tgl_akhir']->toDateString() }}"`.
  - Pada `.weekly-stepper-wrap`:
    - Untuk chip `$m['minggu_ke'] < $mingguAktif && !$hasLog`: ubah `href` atau `onclick="openTambahModal({{ $m['minggu_ke'] }})"` agar peserta langsung bisa mengklik chip minggu yang bolong/tertinggal.
    - Untuk chip `$m['minggu_ke'] > $mingguAktif`: pastikan non-interaktif (`tabindex="-1"`, class `is-upcoming disabled`, title *"Minggu ke-X belum dibuka"*).
- [ ] 2.2 Perbarui `resources/js/pages/peserta/logbook-index.js`:
  - Di fungsi `onMingguChange(mingguVal)`:
    - Ambil data atribut tanggal awal dan akhir dari opsi terpilih.
    - Setel nilai `inputTanggal.min` ke `data-tgl-awal` dan `inputTanggal.max` ke `min(today, data-tgl-akhir)`.
    - Setel default value `inputTanggal.value` ke nilai maksimal tanggal minggu tersebut.
  - Di fungsi `openTambahModal(preSelectMinggu)`:
    - Cegah pemilihan jika `preSelectMinggu > mingguAktif`.

### Tahap 3: Automated Testing & Verifikasi Quality Gate
- [ ] 3.1 Buat skenario pengujian di `tests/Feature/LogbookArchitectureAndSecurityTest.php`:
  - Test 1: Peserta pada Minggu ke-3 berhasil mengisi logbook Minggu ke-1 yang belum terisi (retrospektif minggu lampau sukses).
  - Test 2: Peserta pada Minggu ke-3 berhasil mengisi logbook Minggu ke-3 (minggu berjalan sukses).
  - Test 3: Peserta pada Minggu ke-3 ditolak saat mencoba mengirim logbook Minggu ke-4 atau ke-5 (minggu mendatang gagal 422).
- [ ] 3.2 Jalankan pengujian seluruh test suite logbook:
  - `php artisan test tests/Feature/LogbookArchitectureAndSecurityTest.php`
  - `php artisan test tests/Feature/TugasLogbookTest.php`
  - `php artisan test tests/Feature/PesertaLogbookRevampTest.php`
- [ ] 3.3 Jalankan `./vendor/bin/pint --test` dan kompilasi aset `npm run build`.

---

## 6. Strategi Testing

1. **Skenario Happy Path**:
   - **Kasus A (Minggu Berjalan)**: Peserta di Minggu ke-2 mengisi logbook Minggu ke-2 dengan tanggal hari ini &rarr; Status 201 / Redirect Success.
   - **Kasus B (Minggu Lampau Tertinggal)**: Peserta di Minggu ke-3 mengisi logbook Minggu ke-1 dengan tanggal minggu pertama (14 hari lalu) &rarr; Status 201 / Redirect Success, data tersimpan dengan `minggu_ke = 1`.
2. **Skenario Sad Path**:
   - **Kasus C (Minggu Mendatang / Masa Depan)**: Peserta di Minggu ke-2 mencoba mengisi logbook Minggu ke-3 atau ke-4 &rarr; Ditolak Status 422 / pesan validasi *"belum dapat diisi karena periode minggu tersebut belum berjalan"*.
   - **Kasus D (Tanggal di Masa Depan)**: Peserta mencoba mengirimkan tanggal esok hari &rarr; Ditolak Status 422.
   - **Kasus E (Duplikasi Minggu)**: Peserta mencoba mengisi Minggu ke-1 yang sudah pernah diisi sebelumnya &rarr; Ditolak dengan pesan duplikasi.

---

## 7. Risiko & Mitigasi

| Risiko | Tingkat | Mitigasi |
| :--- | :---: | :--- |
| **Regresi pada pengujian lama `TugasLogbookTest.php` yang tidak mengirimkan `minggu_ke`** | Sedang | Pertahankan fallback: jika `minggu_ke` tidak disertakan di payload request, sistem tetap menerapkan validasi batas mundur harian `logbook_maks_mundur_hari` (3 hari). |
| **Peserta memilih tanggal di luar rentang minggu terkait** | Rendah | Input tanggal secara otomatis dibatasi oleh `min` dan `max` JavaScript sesuai rentang tanggal minggu yang dipilih, serta divalidasi ulang di backend. |

---

## 8. Kriteria Selesai (Definition of Done)

- [ ] Minggu lampau yang belum pernah diisi dapat dipilih dan berhasil disimpan oleh peserta.
- [ ] Minggu berjalan dapat dipilih dan berhasil disimpan oleh peserta.
- [ ] Minggu mendatang terkunci (*disabled*) di form, chip tidak dapat diklik, dan request backend ditolak status 422.
- [ ] Seluruh automated feature tests (`LogbookArchitectureAndSecurityTest`, `TugasLogbookTest`, `PesertaLogbookRevampTest`) lulus 100% hijau.
- [ ] Aset Vite dikompilasi sukses tanpa error.
- [ ] Format kode PHP mematuhi PSR-12 via Laravel Pint.

