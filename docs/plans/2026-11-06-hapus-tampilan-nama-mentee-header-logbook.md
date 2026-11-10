# Rencana Kerja: Penghapusan Tampilan Identitas Mentee (Nama & Avatar) pada Header Panel Logbook Pembimbing

## 1. Tujuan
Menghapus komponen tampilan identitas mentee (`Avatar [QA] + Nama Peserta + Institusi`) yang saat ini muncul di bawah / di dalam judul panel utama (`Daftar Logbook & Catatan Pembimbing`) pada halaman Logbook Pembimbing (`resources/views/pembimbing/logbook/index.blade.php`), sesuai dengan tangkapan layar yang dilampirkan oleh pengguna (`media_1793936765081_c0f9dc96.png` dan `media_1793936799657_d4ef6271.png`).

Setelah perubahan:
- Judul panel utama tetap bersih dan konsisten: **"Daftar Logbook & Catatan Pembimbing"**.
- Tampilan avatar dan nama mentee (`header-mentee-col`: `#focusedMenteeAvatar`, `#focusedMenteeName`, `#focusedMenteeInst`) dihapus sepenuhnya dari area header panel.
- Tombol navigasi **`← Semua Mentee`** tetap dipertahankan di sisi kanan header panel untuk memfasilitasi navigasi kembali saat pembimbing sedang melihat daftar logbook satu peserta tertentu (mode fokus).

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi Kerja:
1. **Lokasi Elemen yang Diminta Dihapus**:
   Berdasarkan tangkapan layar `media_1793936799657_d4ef6271.png` dan close-up `media_1793936765081_c0f9dc96.png`, elemen yang dimaksud adalah blok identitas mentee yang berada langsung di bawah/di dalam header panel "Daftar Logbook & Catatan Pembimbing" (`.header-mentee-col` yang berisi `#focusedMenteeAvatar`, `#focusedMenteeName`, dan `#focusedMenteeInst`).
2. **Navigasi Kembali Tetap Dipertahankan**:
   Tombol **`← Semua Mentee`** tetap diperlukan agar pembimbing memiliki kendali UX untuk keluar dari mode fokus dan kembali melihat seluruh peserta bimbingan.
3. **Identitas Mentee di Konten**:
   Identitas mentee tetap tersedia pada kartu peserta dan filter bar di bagian atas, sehingga penghapusan nama di header panel tidak menghilangkan konteks kepemilikan data logbook.

---

## 3. Pendekatan Teknis

Kami mengevaluasi 2 pendekatan arsitektur antarmuka:

- **Opsi A (Terpilih: Clean Header dengan Navigasi Terpadu)**:
  - Hapus blok `.header-mentee-col` (`#focusedMenteeAvatar`, `#focusedMenteeName`, `#focusedMenteeInst`) dari slot title pada `resources/views/pembimbing/logbook/index.blade.php`.
  - Jadikan judul panel tunggal dan bersih: "Daftar Logbook & Catatan Pembimbing".
  - Tempatkan tombol `#btnBackToMentees` (`← Semua Mentee`) di sisi kanan baris header panel, dengan visibilitas dinamis (tersembunyi saat melihat semua mentee, tampil saat fokus pada satu mentee).
  - Di JavaScript (`resources/js/pages/pembimbing/logbook-index.js`), sederhanakan fungsi `focusOnMentee()` dan `resetToAllMentees()` agar hanya mengatur status visibilitas tombol kembali dan kartu tanpa memanipulasi avatar/nama di header.
  - *Kelebihan*: Tampilan header rapi, elegan, tidak berantakan, bebas tumpang tindih teks, dan 100% mematuhi permintaan visual pengguna.
  - *Kekurangan*: Tidak ada nama peserta pada baris judul panel (konteks peserta tetap terlihat pada filter aktif dan stream logbook).

- **Opsi B (Dynamic Text Replacement tanpa Avatar)**:
  - Mengubah judul "Daftar Logbook & Catatan Pembimbing" menjadi "Logbook: [Nama Mentee]" secara teks biasa tanpa avatar.
  - *Kelebihan*: Nama peserta masih tercantum di judul.
  - *Kekurangan*: Bertentangan dengan instruksi eksplisit pengguna ("tampilan nama disini di hapus aja") dan merusak konsistensi desain sistem Gentelella.

-> **Rekomendasi**: Mengadopsi **Opsi A**.

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :---: | :--- |
| `resources/views/pembimbing/logbook/index.blade.php` | Modify | 1. Hapus elemen `.header-mentee-col` (`#focusedMenteeAvatar`, `#focusedMenteeName`, `#focusedMenteeInst`).<br>2. Sederhanakan `<x-slot:title>` menjadi judul tetap "Daftar Logbook & Catatan Pembimbing" bersama tombol `#btnBackToMentees` di sisi kanan.<br>3. Pastikan tombol kembali hanya memiliki class dinamis sesuai status fokus. |
| `resources/js/pages/pembimbing/logbook-index.js` | Modify | 1. Hapus referensi dan pembaruan DOM untuk `focusedMenteeAvatar`, `focusedMenteeName`, dan `focusedMenteeInst`.<br>2. Kelola visibilitas tombol `#btnBackToMentees` (`display: inline-flex` vs `display: none`) saat berpindah antara semua mentee dan fokus mentee. |
| `resources/css/pages/pembimbing/logbook-index.css` | Modify | 1. Hapus aturan CSS yang tidak lagi digunakan untuk `.header-mentee-col`, `.header-mentee-avatar`, `.header-mentee-name`, `.header-mentee-inst`.<br>2. Sesuaikan layout container header panel (`.mentee-unified-header` / `.logbook-panel-header-wrap`) agar judul di kiri dan tombol kembali di kanan dengan rapi dan responsif. |
| `tests/Feature/PembimbingFocusedMenteeLogbookViewTest.php` | Modify | Perbarui assertion test agar memvalidasi bahwa nama mentee tidak lagi berada di dalam header panel (`#menteeUnifiedHeader`), namun tombol `Semua Mentee` tetap muncul saat mode fokus aktif. |

---

## 5. Langkah Implementasi

### Tahap 1: Markup Blade (`resources/views/pembimbing/logbook/index.blade.php`)
- [x] Buka `resources/views/pembimbing/logbook/index.blade.php`.
- [x] Di dalam `<x-slot:title>` pada `#logbookMainPanel`:
  - Hapus container `.header-mentee-col` beserta seluruh elemen anak di dalamnya (`#focusedMenteeAvatar`, `#focusedMenteeName`, `#focusedMenteeInst`).
  - Satukan header panel menjadi satu baris terstruktur:
    - Judul: `<span class="default-panel-title">Daftar Logbook &amp; Catatan Pembimbing</span>`
    - Tombol kembali: `<a href="..." id="btnBackToMentees" class="btn-back-mentee-modern {{ $isFocusActive ? '' : 'is-hidden' }}"><span>Semua Mentee</span></a>`
  - Hapus variabel kalkulasi yang tidak lagi dipakai (`$focusedInitials`).

### Tahap 2: Interaksi JavaScript (`resources/js/pages/pembimbing/logbook-index.js`)
- [x] Buka `resources/js/pages/pembimbing/logbook-index.js`.
- [x] Hapus variabel DOM:
  - `focusedMenteeAvatar`
  - `focusedMenteeName`
  - `focusedMenteeInst`
  - `defaultTitleText`
- [x] Pada fungsi `focusOnMentee(menteeId, menteeName, menteeInst, updateHistory)`:
  - Hapus pembaruan teks untuk avatar, nama, dan institusi.
  - Tampilkan tombol `btnBack` dengan menghapus class `.is-hidden` atau `btnBack.style.display = 'inline-flex'`.
- [x] Pada fungsi `resetToAllMentees(updateHistory)`:
  - Sembunyikan tombol `btnBack` dengan menambahkan class `.is-hidden` atau `btnBack.style.display = 'none'`.

### Tahap 3: Styling CSS (`resources/css/pages/pembimbing/logbook-index.css`)
- [x] Buka `resources/css/pages/pembimbing/logbook-index.css`.
- [x] Hapus selector CSS yang sudah usang (`.header-mentee-col`, `.header-mentee-avatar`, `.header-mentee-name`, `.header-mentee-inst`).
- [x] Pastikan `.logbook-panel-header-wrap` atau `.mentee-unified-header` memiliki `display: flex; align-items: center; justify-content: space-between; width: 100%;`.
- [x] Tambahkan utility class `.is-hidden { display: none !important; }` khusus pada scope modul pembimbing logbook agar tombol kembali tersembunyi sempurna saat tidak dalam mode fokus.

### Tahap 4: Build Asset & Quality Gate
- [x] Jalankan `npm run build` untuk mengompilasi CSS dan JS Vite yang telah diubah.
- [x] Jalankan `php vendor/bin/pint --test` untuk memastikan kepatuhan formatting PHP.
- [x] Perbarui dan jalankan `tests/Feature/PembimbingFocusedMenteeLogbookViewTest.php`.
- [x] Jalankan seluruh suite test pembimbing: `php artisan test tests/Feature/PembimbingFocusedMenteeLogbookViewTest.php tests/Feature/PembimbingFocusedMenteeTugasViewTest.php tests/Feature/PembimbingReviewClassroomTest.php tests/Feature/PembimbingPortalStandardizationTest.php`.

---

## 6. Strategi Testing

### 1. Test Otomasi (`tests/Feature/PembimbingFocusedMenteeLogbookViewTest.php`)
- **Kasus 1**: Pada halaman default (`/pembimbing/logbook`):
  - Memastikan judul "Daftar Logbook &amp; Catatan Pembimbing" muncul.
  - Memastikan `#focusedMenteeName` dan `.header-mentee-col` **TIDAK ADA** di DOM.
  - Memastikan tombol `#btnBackToMentees` memiliki status `.is-hidden`.
- **Kasus 2**: Pada mode fokus mentee (`/pembimbing/logbook?penempatan_id=X`):
  - Memastikan judul "Daftar Logbook &amp; Catatan Pembimbing" tetap tampil.
  - Memastikan tidak ada duplikasi avatar/nama mentee di header panel.
  - Memastikan tombol `Semua Mentee` aktif dan dapat diklik.
  - Memastikan entri logbook mentee terkait tetap dirender.

### 2. Pengujian Manual di Browser (Laragon `http://magkom.test`)
- Buka halaman `/pembimbing/logbook`.
- Pastikan pada bagian header panel "Daftar Logbook & Catatan Pembimbing" tidak lagi menampilkan `[QA] Qori Amanda / Universitas Negeri Yogyakarta`.
- Klik nama peserta untuk masuk ke mode fokus:
  - Header tetap bersih menampilkan "Daftar Logbook & Catatan Pembimbing".
  - Di kanan muncul tombol `← Semua Mentee`.
  - Body hanya menampilkan stream logbook peserta tersebut.
- Klik tombol `← Semua Mentee`:
  - Tombol kembali menghilang, dan seluruh daftar kartu mentee kembali tampil.

---

## 7. Risiko & Mitigasi

- **Risiko**: Test otomasi yang sebelumnya memeriksa `assertSee('Naufal Pratama')` di dalam header gagal.
  - *Mitigasi*: Sesuaikan assertion test agar memeriksa bahwa nama peserta tetap ada di dalam elemen kartu peserta, bukan di dalam elemen header panel.
- **Risiko**: Tombol `← Semua Mentee` tidak sejajar dengan judul panel pada layar mobile.
  - *Mitigasi*: Berikan styling flex-wrap atau media query responsif yang rapi pada container header panel.

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Blok avatar, nama mentee, dan institusi (`.header-mentee-col`) dihapus sepenuhnya dari header panel "Daftar Logbook & Catatan Pembimbing".
- [x] Judul panel tetap konsisten "Daftar Logbook & Catatan Pembimbing".
- [x] Tombol `← Semua Mentee` muncul di sisi kanan header saat fokus pada satu peserta dan tersembunyi saat menampilkan semua peserta.
- [x] Asset Vite berhasil dikompilasi dengan `npm run build` tanpa peringatan/error.
- [x] Kode PHP lolos audit styling `php vendor/bin/pint --test`.
- [x] Seluruh automated test pembimbing lulus 100% (hijau).
- [x] Tidak ada inline CSS (`style="..."`) atau inline JS di Blade.

