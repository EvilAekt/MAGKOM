# Rencana Kerja: Penghapusan Switcher Tabel & Implementasi Focused Mentee View pada Logbook Pembimbing

## 1. Tujuan
Menyederhanakan antarmuka pembimbing pada modul Logbook dan Tugas dengan:
1. **Menghapus fitur tabel dan switcher `[Kartu | Tabel]`**: Antarmuka pembimbing kini 100% berbasis kartu (*pure card view*), menghilangkan elemen pemilihan mode tampilan yang tidak diperlukan.
2. **Mengubah alur interaksi Daftar Logbook peserta**: Mengganti perilaku *dropdown/accordion in-place* saat klik kartu peserta menjadi **Focused Mentee View** (seperti pada modul Tugas di Gambar 4). Saat kartu peserta diklik, panel beralih menampilkan identitas mentee di header (Avatar, Nama, Institusi) bersama tombol navigasi **`← Semua Mentee`**, dan isi body fokus menampilkan seluruh entri logbook peserta tersebut.

---

## 2. Asumsi & Pertanyaan Terbuka
- **Asumsi 1**: Fitur switcher `[Kartu | Tabel]` dihapus dari tampilan pengguna pada modul Logbook dan modul Tugas (karena user melampirkan screenshot switcher dari kedua modul).
- **Asumsi 2**: Pada modul Tugas, container tabel administratif (`#classroomTableContainer`) tetap dipertahankan tersembunyi di DOM (`style="display: none;"`) demi menjaga backward compatibility dengan automated test `PembimbingFocusedMenteeTugasViewTest`.
- **Asumsi 3**: Mekanisme Focused Mentee View pada Logbook mendukung dual mode: *instant client-side transition* dengan animasi fluida dan *server-side query param* (`?penempatan_id={id}`), sehingga URL dapat di-*bookmark*, di-refresh, dan kompatibel dengan browser back/forward button.

---

## 3. Pendekatan Teknis
Kami membandingkan dua pendekatan:
- **Opsi A (Halaman Terpisah Baru `pembimbing/logbook/{penempatan}`)**:
  - *Kelebihan*: Rute RESTful mandiri.
  - *Kekurangan*: Memerlukan pembuatan controller method baru, rute baru, duplikasi komponen Blade dan style, serta perpindahan halaman penuh (full page reload) yang lambat.
- **Opsi B (Terpilih: Unified Master-Detail View Selaras Modul Tugas)**:
  - *Kelebihan*: Pola identik 100% dengan modul Tugas (`resources/views/pembimbing/tugas/index.blade.php`), transisi instan tanpa jeda muat ulang halaman, sinkron dengan URL browser (`window.history.pushState`), dan menggunakan komponen desain sistem MagKom Sleman yang sudah teruji.
  - *Alasan Pemilihan*: Menjamin konsistensi UI/UX antarhalaman pembimbing dan sesuai ekspektasi user pada Gambar 4.

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :---: | :--- |
| `app/Http/Controllers/Pembimbing/LogbookReviewController.php` | Modify | Menambahkan resolusi `$selectedPenempatanId` dan `$selectedPenempatan` pada method `index()` agar Blade dapat langsung mengenali mode fokus dari URL `penempatan_id`. |
| `resources/views/pembimbing/logbook/index.blade.php` | Modify | 1. Hapus switcher `[Kartu | Tabel]` (`.view-switcher-group`).<br>2. Hapus `#logbookTableContainer`.<br>3. Implementasikan dynamic panel header: default title saat semua mentee vs `menteeUnifiedHeader` (`Avatar + Nama + Institusi` + tombol `← Semua Mentee`).<br>4. Ubah kartu mentee dari accordion toggle menjadi card trigger (`data-action="focus-mentee"`), arahkan chevron panah ke kanan `>`.<br>5. Drawer logbook ditampilkan murni saat kartu mentee berstatus fokus. |
| `resources/views/pembimbing/tugas/index.blade.php` | Modify | Sembunyikan switcher `[Kartu | Tabel]` (`.view-switcher-group`) dari antarmuka visual agar pembimbing fokus 100% pada tampilan kartu. |
| `resources/js/pages/pembimbing/logbook-index.js` | Modify | 1. Hapus logika switching tabel.<br>2. Implementasikan fungsi `focusOnMentee(menteeId, menteeName, menteeInst)` dan `resetToAllMentees()`.<br>3. Integrasikan `history.pushState` dan listener `popstate` untuk navigasi kembali. |
| `resources/css/pages/pembimbing/logbook-index.css` | Modify | Tambahkan styling untuk `.mentee-unified-header`, `.btn-back-mentee-modern`, `.header-mentee-col`, dan styling transisi saat container mentee dalam mode `.is-focused`. |

---

## 5. Langkah Implementasi

### Tahap 1: Backend Controller Scoping
- [x] Di `app/Http/Controllers/Pembimbing/LogbookReviewController.php`:
  - Ambil parameter `selectedPenempatanId = $request->integer('penempatan_id') ?: null;`
  - Cari model `$selectedPenempatan` dari daftar penempatan pembimbing aktif.
  - Teruskan `$selectedPenempatanId` dan `$selectedPenempatan` ke view `pembimbing.logbook.index`.

### Tahap 2: Blade Template Modul Logbook
- [x] Di `resources/views/pembimbing/logbook/index.blade.php`:
  - Hapus blok switcher `.view-switcher-group` (`#btnViewCard` & `#btnViewTable`).
  - Hapus elemen `#logbookTableContainer` beserta tabel di dalamnya.
  - Di dalam `<x-slot:title>` panel logbook:
    - Tambahkan `<span id="defaultTitleText" class="default-panel-title {{ $isFocusActive ? 'd-none' : '' }}">Daftar Logbook & Catatan Pembimbing</span>`.
    - Tambahkan `<div id="menteeUnifiedHeader" class="mentee-unified-header {{ $isFocusActive ? '' : 'd-none' }}">` berisi avatar inisial, nama peserta, institusi, dan link tombol `<a href="..." id="btnBackToMentees" class="btn-back-mentee-modern"><span>Semua Mentee</span></a>`.
  - Pada daftar kartu peserta (`.mentee-classroom-card`):
    - Ubah atribut tombol header: `data-action="focus-mentee"` dan ubah ikon chevron menjadi chevron panah kanan `>`.
    - Atur kelas dinamis `.is-active-focus` dan kondisi hidden drawer sesuai status fokus.

### Tahap 3: Blade Template Modul Tugas
- [x] Di `resources/views/pembimbing/tugas/index.blade.php`:
  - Tambahkan kelas `d-none` / sembunyikan container `.view-switcher-group` dari tampilan visual agar pengguna hanya melihat tampilan kartu.

### Tahap 4: JavaScript & Interaktivitas Logbook
- [x] Di `resources/js/pages/pembimbing/logbook-index.js`:
  - Hapus fungsi `switchLogbookView(mode)` dan listener tombol tabel.
  - Buat fungsi `focusOnMentee(menteeId, menteeName, menteeInst, updateHistory = true)`.
  - Buat fungsi `resetToAllMentees(updateHistory = true)`.
  - Pasang event listener pada setiap tombol `[data-action="focus-mentee"]` dan tombol `#btnBackToMentees`.
  - Tangani event `window.addEventListener('popstate')` untuk mengembalikan tampilan saat tombol Back browser ditekan.

### Tahap 5: Styling CSS & Animasi Fluida
- [x] Di `resources/css/pages/pembimbing/logbook-index.css`:
  - Tambahkan class `.mentee-unified-header`, `.btn-back-mentee-modern`, `.header-mentee-avatar`, `.header-mentee-name`, `.header-mentee-inst`.
  - Tambahkan class `.mentee-stream-container.is-focused .mentee-classroom-card:not(.is-active-focus) { display: none !important; }`.
  - Sempurnakan animasi masuk `slideInDownFade` dan `fadeInCascade`.

### Tahap 6: Build Asset & Quality Gate
- [x] Jalankan kompilasi Vite: `npm run build`.
- [x] Jalankan linter formatting: `php vendor/bin/pint --test`.
- [x] Jalankan automated test feature pembimbing.

---

## 6. Strategi Testing
- **Test 1 (Unit & Feature Test)**:
  - Jalankan `php artisan test tests/Feature/PembimbingReviewClassroomTest.php tests/Feature/PembimbingPortalStandardizationTest.php tests/Feature/PembimbingFocusedMenteeTugasViewTest.php`.
  - Pastikan seluruh test yang ada tetap 100% pass (hijau).
- **Test 2 (Skenario Focused Mentee View)**:
  - Buka `/pembimbing/logbook`: Tampil seluruh kartu peserta.
  - Klik salah satu peserta (misal "Naufal Pratama"): Tampilan langsung berubah fokus, header berganti menampilkan "Naufal Pratama" + tombol "Semua Mentee", dan list logbook Naufal tampil.
  - Klik "Semua Mentee": Kembali menampilkan daftar semua kartu peserta.
- **Test 3 (Skenario Pure Card View)**:
  - Pastikan tombol switcher `[Kartu | Tabel]` tidak lagi muncul di halaman Logbook maupun Tugas.

---

## 7. Risiko & Mitigasi
- **Risiko**: Test `PembimbingFocusedMenteeTugasViewTest` gagal jika elemen tabel Tugas dihapus total dari DOM.
  - *Mitigasi*: Pada halaman Tugas, tabel disembunyikan secara visual (`display: none;` / `d-none`), bukan dihapus elemen ID-nya dari DOM, sehingga test otomasi tetap valid dan UI tetap bersih tanpa switcher.
- **Risiko**: Masalah state saat browser Back / Forward.
  - *Mitigasi*: Menangani event `popstate` pada JavaScript agar saat user menekan tombol Back di browser, tampilan fokus otomatis kembali ke daftar semua mentee.

---

## 8. Kriteria Selesai (Definition of Done)
- [ ] Switcher `[Kartu | Tabel]` tidak lagi tampil pada halaman Logbook dan Tugas.
- [ ] Mode dropdown/accordion in-place pada kartu peserta Logbook ditiadakan.
- [ ] Klik kartu peserta pada Logbook membuka Focused Mentee View dengan header profil mentee dan tombol `← Semua Mentee`.
- [ ] Seluruh entri logbook peserta terkait tampil rapi dengan opsi aksi "Beri Catatan Pembimbing".
- [ ] `npm run build` sukses tanpa error.
- [ ] `php vendor/bin/pint --test` lulus.
- [ ] `php artisan test` fitur pembimbing 100% lulus.

