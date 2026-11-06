# Rencana Kerja: Penambahan Filter Nama Peserta pada Halaman Tugas & Progres Magang Pembimbing

## 1. Tujuan
Menambahkan komponen kartu **"Filter Nama Peserta"** pada halaman **Tugas & Progres Magang** (`resources/views/pembimbing/tugas/index.blade.php`), ditempatkan secara terstruktur di antara baris kartu statistik (*4 Stat Tiles*) dan panel utama *Daftar Penugasan & Progres Mentee*, sehingga memiliki hierarki visual, tata letak, dan perilaku yang 100% selaras dengan halaman **Logbook Peserta** sebagaimana ditunjukkan pada tangkapan layar referensi pengguna (`media_1793937702769_401858cb.png`, `media_1793937714627_c780de60.jpg`, dan `media_1793937724253_6c179ad7.jpg`).

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi Kerja:
1. **Tata Letak & Posisi**:
   Kartu filter ditempatkan di antara penutup grid statistik (`.tiles-grid`) dan pembuka panel utama (`#tugasMainPanel`), identik dengan posisi kartu filter pada modul Logbook (`.logbook-filter-card`).
2. **Kesesuaian Fungsional Form GET**:
   Form filter menggunakan method `GET` dengan action menuju `route('pembimbing.tugas.index')`, membawa parameter `penempatan_id`.
   - Menggunakan event `onchange="this.form.submit()"` untuk pergantian instan saat memilih peserta dari dropdown.
   - Mempertahankan tombol **Filter** (dengan ikon funnel) untuk pengiriman eksplisit.
   - Menampilkan tautan **Reset** apabila filter peserta sedang aktif (`request()->filled('penempatan_id')`).
3. **Data Peserta Bimbingan**:
   Opsi dropdown (`<option>`) menyajikan seluruh mentee bimbingan aktif milik pembimbing yang sedang login (`{{ $pen->peserta?->nama }} ({{ $pen->peserta?->profil?->institusi }})`), dengan opsi default *"Semua Peserta Bimbingan"*.
4. **Sinkronisasi Client-Side**:
   Saat pembimbing mengklik kartu mentee secara langsung di dalam panel stream, nilai dropdown `<select name="penempatan_id">` disinkronkan secara otomatis melalui JavaScript tanpa reload halaman.

---

## 3. Pendekatan Teknis

Kami membandingkan dua pendekatan arsitektur antarmuka:

- **Opsi A (Terpilih: Unified Synchronized Form Filter Selaras Logbook)**:
  - Menyematkan kartu filter `.tugas-filter-card` di Blade menggunakan markup yang identik dengan modul Logbook.
  - Memanfaatkan backend controller `TugasController@index` yang sudah mendukung parameter `penempatan_id` (menyaring metrik dan penugasan serta mengaktifkan mode fokus mentee).
  - Menyelaraskan styling CSS di `resources/css/pages/pembimbing/tugas-index.css` agar token warna, border, padding, dan efek hover sama persis dengan kartu filter Logbook.
  - Menghubungkan JavaScript di `resources/js/pages/pembimbing/tugas-index.js` agar dropdown ikut ter-update saat kartu mentee diklik secara client-side, dan kembali ke default saat tombol *"Semua Mentee"* diklik.
  - *Kelebihan*: Konsistensi UI/UX 100% antarhalaman pembimbing, ramah navigasi bookmark/refresh browser, responsif di mobile dan desktop.
  - *Kekurangan*: Memerlukan sedikit penyesuaian CSS dan JS untuk sinkronisasi dwiarah (two-way sync).

- **Opsi B (Client-Side Filter Only tanpa Parameter URL)**:
  - Filter hanya memanipulasi elemen DOM via JS tanpa form submission atau query parameter.
  - *Kelebihan*: Tidak ada request HTTP tambahan.
  - *Kekurangan*: Bertentangan dengan pola halaman Logbook yang sudah berjalan, URL tidak dapat di-refresh tanpa kehilangan status filter, dan tombol submit form menjadi tidak berguna.

-> **Rekomendasi**: Mengadopsi **Opsi A**.

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :---: | :--- |
| `app/Http/Controllers/Pembimbing/TugasController.php` | Modify | Memastikan daftar penempatan untuk opsi filter dropdown (`$penempatanList` / `$penempatanOptions`) memuat seluruh mentee aktif pembimbing tanpa terpotong paginasi. |
| `resources/views/pembimbing/tugas/index.blade.php` | Modify | Menambahkan komponen kartu `tugas-filter-card` berisi label *"Filter Nama Peserta"*, dropdown `<select name="penempatan_id">`, tombol *Filter*, dan tombol *Reset* di antara baris statistik dan panel utama. |
| `resources/css/pages/pembimbing/tugas-index.css` | Modify | Menambahkan aturan styling untuk `.tugas-filter-card`, `.tugas-filter-form`, `.filter-group`, `.filter-control`, `.filter-actions`, `.btn-filter-apply`, dan `.btn-filter-reset` yang identik dengan Logbook. |
| `resources/js/pages/pembimbing/tugas-index.js` | Modify | Menambahkan sinkronisasi nilai `<select id="selectPenempatan">` saat pembimbing mengklik kartu mentee secara client-side atau saat tombol kembali ke semua mentee ditekan. |
| `tests/Feature/PembimbingFocusedMenteeTugasViewTest.php` | Modify | Menambahkan assertion test bahwa kartu Filter Nama Peserta ter-render, opsi peserta bimbingan tersedia, dan submit filter berfungsi dengan benar. |

---

## 5. Langkah Implementasi

### Tahap 1: Backend Controller (`app/Http/Controllers/Pembimbing/TugasController.php`)
- [x] Buka `app/Http/Controllers/Pembimbing/TugasController.php`.
- [x] Pastikan query `$penempatanList` untuk stream kartu atau `$allPenempatan` untuk dropdown filter memuat seluruh penempatan aktif milik pembimbing terkait.
- [x] Teruskan variabel penempatan ke view `pembimbing.tugas.index`.

### Tahap 2: Markup Blade (`resources/views/pembimbing/tugas/index.blade.php`)
- [x] Buka `resources/views/pembimbing/tugas/index.blade.php`.
- [x] Tepat setelah penutup div baris statistik (`</div>` line 109) dan sebelum `@php $isFocusActive ...` (line 112):
  - Sisipkan blok kartu filter:
    ```blade
    {{-- 2.5 Filter Bar: Sederhana (HANYA Filter Nama Peserta) --}}
    <div class="tugas-filter-card">
        <form method="GET" action="{{ route('pembimbing.tugas.index') }}" class="tugas-filter-form">
            <div class="filter-group" style="flex: 1;">
                <label for="selectPenempatan">Filter Nama Peserta</label>
                <select name="penempatan_id" id="selectPenempatan" class="filter-control" onchange="this.form.submit()">
                    <option value="">Semua Peserta Bimbingan</option>
                    @foreach($penempatanList as $pen)
                        <option value="{{ $pen->id }}" {{ request('penempatan_id') == $pen->id ? 'selected' : '' }}>
                            {{ $pen->peserta?->nama }} ({{ $pen->peserta?->profil?->institusi ?? 'Institusi' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn-filter-apply">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
                    <span>Filter</span>
                </button>
                @if(request()->filled('penempatan_id'))
                    <a href="{{ route('pembimbing.tugas.index', request()->except('penempatan_id')) }}" class="btn-filter-reset">Reset</a>
                @endif
            </div>
        </form>
    </div>
    ```

### Tahap 3: Styling CSS (`resources/css/pages/pembimbing/tugas-index.css`)
- [x] Buka `resources/css/pages/pembimbing/tugas-index.css`.
- [x] Tambahkan styling terisolasi untuk kartu filter Tugas:
  - `.tugas-filter-card`: background putih, border `#E2E8F0`, radius `14px`, padding `1.25rem`, shadow `0 1px 3px rgba(15, 23, 42, 0.04)`, margin-bottom `24px`.
  - `.tugas-filter-form`: layout flex, gap `16px`, flex-wrap, align-items flex-end.
  - `.filter-group label`: font size `0.8125rem`, font-weight `600`, color `#475569`.
  - `.filter-control`: height `38px`, border `#CBD5E1`, radius `8px`, background `#F8FAFC`.
  - `.btn-filter-apply`: background `#01347C`, color putih, radius `8px`, hover background `#002244`.
  - `.btn-filter-reset`: background `#E2E8F0`, color `#475569`, radius `8px`, hover background `#CBD5E1`.
  - Dukungan mode gelap: `[data-theme="dark"] .tugas-filter-card`, `.filter-control`.

### Tahap 4: Interaktivitas JavaScript (`resources/js/pages/pembimbing/tugas-index.js`)
- [x] Buka `resources/js/pages/pembimbing/tugas-index.js`.
- [x] Pada fungsi `focusOnMentee(menteeId, ...)`:
  - Sinkronkan dropdown: `const sel = document.getElementById('selectPenempatan'); if (sel) sel.value = String(menteeId);`.
- [x] Pada fungsi `resetToAllMentees()`:
  - Kembalikan dropdown: `const sel = document.getElementById('selectPenempatan'); if (sel) sel.value = '';`.

### Tahap 5: Build Asset & Quality Gate
- [x] Jalankan `npm run build` untuk mengompilasi Vite bundle.
- [x] Jalankan `php vendor/bin/pint --test` untuk memastikan kebersihan kode PHP.
- [x] Jalankan feature tests: `php artisan test tests/Feature/PembimbingFocusedMenteeTugasViewTest.php tests/Feature/PembimbingFocusedMenteeLogbookViewTest.php`.

---

## 6. Strategi Testing

### 1. Test Otomasi (`tests/Feature/PembimbingFocusedMenteeTugasViewTest.php`)
- **Kasus 1: Tampilan Default Halaman Tugas**:
  - Memverifikasi teks `"Filter Nama Peserta"` ter-render di dalam respons.
  - Memverifikasi elemen `<select name="penempatan_id"` dan opsi `"Semua Peserta Bimbingan"` ada di DOM.
  - Memverifikasi tombol `"Filter"` muncul.
  - Memverifikasi opsi mentee milik pembimbing muncul (misal `"Naufal Pratama"`, `"Dimas Pratama"`).
  - Memverifikasi mentee pembimbing lain **TIDAK MUNCUL** di opsi filter (Anti-IDOR).
- **Kasus 2: Filter Aktif via Query Parameter**:
  - Mengakses `route('pembimbing.tugas.index', ['penempatan_id' => $penempatan1->id])`.
  - Memverifikasi opsi mentee 1 memiliki atribut `selected`.
  - Memverifikasi tombol/link `"Reset"` muncul.
  - Memverifikasi halaman berada dalam mode fokus pada mentee tersebut.

### 2. Pengujian Manual di Browser (Laragon `http://magkom.test`)
- Buka `http://magkom.test/pembimbing/tugas`:
  - Periksa kartu Filter Nama Peserta berada di antara 4 Stat Tiles dan panel utama.
  - Pilih salah satu nama peserta dari dropdown: halaman memuat ulang dengan URL `?penempatan_id=X` dan menampilkan tugas peserta tersebut.
  - Klik tombol "Reset": halaman kembali menampilkan semua peserta dan dropdown kembali ke "Semua Peserta Bimbingan".

---

## 7. Risiko & Mitigasi

- **Risiko**: Tab status tugas (`?status=belum` / `?status=selesai`) hilang saat melakukan filter nama peserta.
  - *Mitigasi*: Sertakan `<input type="hidden" name="status" value="{{ request('status') }}">` di dalam form filter jika ada parameter status yang aktif, sehingga filter nama peserta dapat dikombinasikan secara aman dengan tab status tugas.

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Kartu "Filter Nama Peserta" tampil konsisten di antara kartu statistik dan panel utama pada halaman Tugas.
- [x] Desain dan token tampilan 100% identik dengan kartu filter pada modul Logbook.
- [x] Dropdown memuat seluruh mentee aktif pembimbing dan mematuhi isolasi Anti-IDOR.
- [x] Tombol Reset muncul saat filter aktif dan dapat mengembalikan tampilan ke seluruh peserta.
- [x] Kompilasi Vite (`npm run build`) berhasil 100% tanpa error.
- [x] Formatting PHP lolos audit `php vendor/bin/pint --test`.
- [x] Automated test suite `PembimbingFocusedMenteeTugasViewTest` lulus 100% (hijau).
- [x] Tidak ada CSS inline (`style="..."`) atau JS inline di Blade.

