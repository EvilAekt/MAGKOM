# TASK / PRD: Penyeragaman Header Mentee dan Penyembunyian Kartu Filter pada Mode Fokus Pembimbing
## Dokumen Spesifikasi Teknis & Rencana Kerja (Senior Developer ke Junior Developer / AI Agent)
- **Target Modul**: Dashboard Pembimbing — Modul Tugas & Progres Magang serta Modul Logbook Peserta
- **Role Pengembang**: Junior Developer / AI Assistant
- **Pemberi Task**: Senior Developer MagKom Sleman
- **Prioritas**: UI/UX Consistency, Layout Polish & State Management

---

# 1. LATAR BELAKANG & TUJUAN (OBJECTIVES)

Halo Junior Dev / Agent,

Berdasarkan evaluasi terhadap antarmuka portal pembimbing di lingkungan staging/development (lihat referensi tangkapan layar `media_1793938693658_ff549f8e.jpg` dan `media_1793938711827_218908c5.jpg`), terdapat dua ketidaksesuaian visual dan alur kerja (inconsistency) saat pembimbing masuk ke dalam detail tugas maupun detail logbook peserta:

1. **Kartu Filter Masih Muncul di Mode Fokus**:
   Ketika pembimbing sudah memilih/mengklik salah satu peserta (masuk ke dalam detail penugasan atau entri logbook mentee tersebut), kartu *"Filter Nama Peserta"* di atas panel masih tetap muncul. Hal ini memakan ruang layar (*vertical space*) dan membingungkan pengguna, karena pembimbing sudah berada di dalam lingkup kerja satu mentee khusus.
   - **Solusi yang Diinginkan**: Sembunyikan kartu filter (`.tugas-filter-card` dan `.logbook-filter-card`) saat pembimbing berada dalam mode fokus (*focused mentee view*). Kartu filter HANYA boleh tampil di direktori awal saat menampilkan semua mentee.
2. **Header Panel Logbook Belum Seragam dengan Modul Tugas**:
   Pada modul Tugas (Gambar 1), ketika pembimbing fokus pada mentee (misal Rian Ardiansyah), judul default panel digantikan oleh **Header Identitas Mentee** yang profesional di sisi kiri:
   - Avatar lingkaran inisial (misal `RA`)
   - Nama lengkap mentee (misal `Rian Ardiansyah`)
   - Nama institusi/sekolah (misal `UPN Veteran Yogyakarta`)
   - Serta tombol navigasi **`← Semua Mentee`** di sisi kanan.
   Sebaliknya, pada modul Logbook (Gambar 2), saat masuk ke isi logbook mentee, header kiri masih kaku menampilkan judul umum *"Daftar Logbook & Catatan Pembimbing"* tanpa identitas mentee.
   - **Solusi yang Diinginkan**: Samakan struktur dan tampilan header Logbook saat mode fokus agar 100% identik dengan modul Tugas (avatar, nama peserta, institusi di kiri + tombol kembali di kanan).

---

# 2. ASUMSI & ATURAN KERJA TEKNIS (WORKING ASSUMPTIONS)

1. **State Mode Fokus (`$isFocusActive`)**:
   - Di Controller/Blade, mode fokus aktif ditandai dengan adanya model `$selectedPenempatan` (atau query parameter `?penempatan_id=X`).
   - Di sisi client (JavaScript), mode fokus aktif ditandai dengan kelas `.is-focused` pada container kartu dan kelas `.is-active-focus` pada kartu mentee terpilih.
2. **Penyembunyian Kartu Filter**:
   - Di Blade (server-side render): Jika `$isFocusActive` bernilai `true`, tambahkan kelas utility `d-none` pada elemen wrapper kartu filter.
   - Di JavaScript (client-side transition): Saat fungsi `focusOnMentee()` dipanggil, sembunyikan elemen kartu filter dengan `filterCard.classList.add('d-none')`. Saat tombol `resetToAllMentees()` diklik, kembalikan dengan `filterCard.classList.remove('d-none')`.
3. **Penyelarasan Header Panel Logbook**:
   - Di Blade Logbook, implementasikan dual-mode header yang sama persis seperti pada Blade Tugas:
     - **Mode 1 (Semua Mentee)**: Tampilkan `<span id="defaultTitleText" class="default-panel-title {{ $isFocusActive ? 'd-none' : '' }}">Daftar Logbook &amp; Catatan Pembimbing</span>`.
     - **Mode 2 (Fokus Mentee)**: Tampilkan `<div id="menteeUnifiedHeader" class="mentee-unified-header {{ $isFocusActive ? '' : 'd-none' }}">` yang berisi `.header-mentee-col` (avatar inisial, nama peserta, institusi) di kiri dan tombol `#btnBackToMentees` (`← Semua Mentee`) di kanan.
4. **Pencegahan Bug CSS `.d-none`**:
   - Pastikan selector `.d-none { display: none !important; }` didefinisikan secara eksplisit di `resources/css/pages/pembimbing/logbook-index.css` (sebagaimana sudah ada di `tugas-index.css:1460`) agar tidak terjadi kebocoran tampilan ganda.

---

# 3. PENDEKATAN TEKNIS & ARSITEKTUR

Kami mengevaluasi dua opsi pendekatan implementasi:

- **Opsi A (Terpilih: Unified Blade Slot & CSS Utility Synchronous Switching)**:
  - Menggunakan struktur template Blade dan kelas CSS yang identik antara `tugas/index.blade.php` dan `logbook/index.blade.php`.
  - Mengandalkan utility `.d-none` terstandarisasi untuk switching instan baik saat page load pertama (SSR) maupun navigasi instan via JavaScript (SPA-like).
  - *Kelebihan*: Kode sangat rapi, konsisten, maintainable, waktu transisi 0ms, dan tidak ada duplikasi komponen baru.
  - *Kekurangan*: Membutuhkan pembaruan assertion pada unit test yang sebelumnya memeriksa ketiadaan avatar di header logbook.
- **Opsi B (Render Kondisional Blade `@if (!$isFocusActive)` murni)**:
  - Menghilangkan kartu filter secara permanen dari DOM jika URL memuat `penempatan_id`.
  - *Kelebihan*: Elemen tidak ada di DOM saat mode fokus.
  - *Kekurangan*: Merusak alur client-side transition (jika user mengklik kartu mentee secara langsung di browser tanpa full-page reload, elemen filter tidak bisa disembunyikan/dimunculkan kembali secara dinamis).

-> **Keputusan Senior Dev**: Wajib gunakan **Opsi A**.

---

# 4. PEMETAAN BERKAS YANG DIUBAH (FILE IMPACT TABLE)

| Berkas | Aksi | Ringkasan Pekerjaan |
| :--- | :---: | :--- |
| `resources/views/pembimbing/tugas/index.blade.php` | Modify | Tambahkan `id="tugasFilterCard"` dan kelas dinamis `{{ $isFocusActive ? 'd-none' : '' }}` pada kartu filter. |
| `resources/views/pembimbing/logbook/index.blade.php` | Modify | 1. Tambahkan `id="logbookFilterCard"` dan kelas dinamis `{{ $isFocusActive ? 'd-none' : '' }}` pada kartu filter.<br>2. Kembalikan blok `.header-mentee-col` (`focusedMenteeAvatar`, `focusedMenteeName`, `focusedMenteeInst`) ke dalam `#menteeUnifiedHeader`.<br>3. Pasang toggle `.d-none` antara `#defaultTitleText` dan `#menteeUnifiedHeader`. |
| `resources/css/pages/pembimbing/logbook-index.css` | Modify | 1. Tambahkan aturan utility `.d-none { display: none !important; }`.<br>2. Pulihkan styling `.header-mentee-col`, `.header-mentee-avatar`, `.header-mentee-text`, `.header-mentee-name`, `.header-mentee-inst` agar presisi 1:1 dengan `tugas-index.css`. |
| `resources/js/pages/pembimbing/tugas-index.js` | Modify | Pada `focusOnMentee()` tambahkan `tugasFilterCard.classList.add('d-none')`; pada `resetToAllMentees()` tambahkan `tugasFilterCard.classList.remove('d-none')`. |
| `resources/js/pages/pembimbing/logbook-index.js` | Modify | 1. Pada `focusOnMentee()`: sembunyikan filter card, sembunyikan default title, tampilkan unified header, dan isi konten avatar, nama mentee, serta institusi.<br>2. Pada `resetToAllMentees()`: munculkan filter card, munculkan default title, sembunyikan unified header. |
| `tests/Feature/PembimbingFocusedMenteeLogbookViewTest.php` | Modify | Perbarui test suite agar memvalidasi header profil mentee muncul di mode fokus dan kartu filter tersembunyi di mode fokus. |
| `tests/Feature/PembimbingFocusedMenteeTugasViewTest.php` | Modify | Perbarui test suite agar memvalidasi kartu filter tersembunyi di mode fokus pada modul Tugas. |

---

# 5. PANDUAN LANGKAH IMPLEMENTASI BERTINGKAT (STEP-BY-STEP RUNBOOK)

Junior Developer / Agent diwajibkan mengeksekusi langkah-langkah di bawah ini secara berurutan:

### Tahap 1: Modul Tugas & Progres Magang (Blade & JS)
- [x] **1.1 Update Blade Tugas (`resources/views/pembimbing/tugas/index.blade.php`)**:
  - Cari elemen `<div class="tugas-filter-card">`.
  - Ubah menjadi:
    ```blade
    <div id="tugasFilterCard" class="tugas-filter-card {{ $isFocusActive ? 'd-none' : '' }}">
    ```
- [x] **1.2 Update JS Tugas (`resources/js/pages/pembimbing/tugas-index.js`)**:
  - Di awal handler `DOMContentLoaded`, deklarasikan:
    ```javascript
    const filterCard = document.getElementById('tugasFilterCard');
    ```
  - Di dalam fungsi `focusOnMentee(...)`:
    ```javascript
    if (filterCard) filterCard.classList.add('d-none');
    ```
  - Di dalam fungsi `resetToAllMentees(...)`:
    ```javascript
    if (filterCard) filterCard.classList.remove('d-none');
    ```

---

### Tahap 2: Modul Logbook Peserta (Blade, CSS, & JS)
- [x] **2.1 Update CSS Logbook (`resources/css/pages/pembimbing/logbook-index.css`)**:
  - Pastikan utility class `.d-none` tersedia:
    ```css
    .d-none {
        display: none !important;
    }
    ```
  - Tambahkan kembali styling `.header-mentee-col` dan elemennya (ambil referensi dari `tugas-index.css:1240-1264`):
    ```css
    .header-mentee-col {
        display: inline-flex;
        align-items: center;
        gap: 12px;
    }

    .header-mentee-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #EFF6FF;
        border: 1.5px solid #BFDBFE;
        color: #01347C;
        font-size: 0.9375rem;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .header-mentee-text {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 2px;
    }

    .header-mentee-name {
        font-size: 1.125rem;
        font-weight: 800;
        color: #0F172A;
        line-height: 1.25;
    }

    .header-mentee-inst {
        font-size: 0.8125rem;
        font-weight: 500;
        color: #64748B;
        line-height: 1.2;
    }
    ```
  - Di dark theme overrides (`[data-theme="dark"]`), tambahkan warna teks putih untuk `.header-mentee-name` dan abu-abu untuk `.header-mentee-inst`.

- [x] **2.2 Update Blade Logbook (`resources/views/pembimbing/logbook/index.blade.php`)**:
  - Cari elemen `<div class="logbook-filter-card">`.
  - Ubah menjadi:
    ```blade
    <div id="logbookFilterCard" class="logbook-filter-card {{ $isFocusActive ? 'd-none' : '' }}">
    ```
  - Di bagian `@php` panel utama:
    ```blade
    @php
        $isFocusActive = $selectedPenempatan !== null;
        $focusedNama = $selectedPenempatan?->peserta?->nama ?? '';
        $focusedInst = $selectedPenempatan?->peserta?->profil?->institusi ?? '';
        $focusedInitials = collect(explode(' ', $focusedNama))->map(fn($w) => mb_substr($w, 0, 1))->take(2)->implode('');
    @endphp
    ```
  - Di dalam `<x-slot:title>`:
    ```blade
    <x-slot:title>
        {{-- Mode 1: Default Title saat Semua Mentee Tampil --}}
        <span id="defaultTitleText" class="default-panel-title {{ $isFocusActive ? 'd-none' : '' }}">
            Daftar Logbook &amp; Catatan Pembimbing
        </span>

        {{-- Mode 2: Header Terpadu saat Mentee Difokuskan (Avatar, Nama, Inst di Kiri, Tombol Kembali di Kanan Sesuai Gambar 1 Tugas) --}}
        <div id="menteeUnifiedHeader" class="mentee-unified-header {{ $isFocusActive ? '' : 'd-none' }}">
            <div class="header-mentee-col">
                <div id="focusedMenteeAvatar" class="header-mentee-avatar" aria-hidden="true">
                    {{ strtoupper($focusedInitials) }}
                </div>
                <div class="header-mentee-text">
                    <span id="focusedMenteeName" class="header-mentee-name">{{ $focusedNama }}</span>
                    <span id="focusedMenteeInst" class="header-mentee-inst">{{ $focusedInst }}</span>
                </div>
            </div>

            <a href="{{ route('pembimbing.logbook.index', request()->except('penempatan_id')) }}" 
               id="btnBackToMentees" 
               class="btn-back-mentee-modern" 
               title="Kembali ke Daftar Semua Mentee">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                <span>Semua Mentee</span>
            </a>
        </div>
    </x-slot:title>
    ```

- [x] **2.3 Update JS Logbook (`resources/js/pages/pembimbing/logbook-index.js`)**:
  - Ambil referensi DOM:
    ```javascript
    const filterCard = document.getElementById('logbookFilterCard');
    const defaultTitleText = document.getElementById('defaultTitleText');
    const menteeUnifiedHeader = document.getElementById('menteeUnifiedHeader');
    const focusedMenteeAvatar = document.getElementById('focusedMenteeAvatar');
    const focusedMenteeName = document.getElementById('focusedMenteeName');
    const focusedMenteeInst = document.getElementById('focusedMenteeInst');
    ```
  - Sertakan fungsi helper `getInitials(name)`.
  - Di dalam `focusOnMentee(menteeId, menteeName, menteeInst, updateHistory)`:
    - Sembunyikan filter card: `if (filterCard) filterCard.classList.add('d-none');`
    - Sembunyikan default title: `if (defaultTitleText) defaultTitleText.classList.add('d-none');`
    - Tampilkan unified header: `if (menteeUnifiedHeader) menteeUnifiedHeader.classList.remove('d-none');`
    - Update teks avatar & nama:
      ```javascript
      if (focusedMenteeAvatar) focusedMenteeAvatar.textContent = getInitials(menteeName);
      if (focusedMenteeName) focusedMenteeName.textContent = menteeName;
      if (focusedMenteeInst) focusedMenteeInst.textContent = menteeInst || '';
      ```
  - Di dalam `resetToAllMentees(updateHistory)`:
    - Tampilkan filter card: `if (filterCard) filterCard.classList.remove('d-none');`
    - Tampilkan default title: `if (defaultTitleText) defaultTitleText.classList.remove('d-none');`
    - Sembunyikan unified header: `if (menteeUnifiedHeader) menteeUnifiedHeader.classList.add('d-none');`

---

### Tahap 3: Build Asset & Quality Gates
- [x] Jalankan kompilasi aset: `npm.cmd run build` (pastikan tidak ada error kompilasi Vite).
- [x] Jalankan linter formatting PHP: `php vendor/bin/pint --test`.
- [x] Perbarui automated feature test pada `PembimbingFocusedMenteeLogbookViewTest.php` dan `PembimbingFocusedMenteeTugasViewTest.php`.
- [x] Jalankan seluruh test suite pembimbing: `php artisan test tests/Feature/PembimbingFocusedMenteeTugasViewTest.php tests/Feature/PembimbingFocusedMenteeLogbookViewTest.php tests/Feature/PembimbingReviewClassroomTest.php tests/Feature/PembimbingPortalStandardizationTest.php`.

---

# 6. SKENARIO PENGUJIAN & VERIFIKASI (TESTING STRATEGY)

### Skenario 1: Halaman Default (Semua Mentee)
- **Modul Tugas** (`/pembimbing/tugas`):
  - Kartu Filter Nama Peserta **TERLIHAT** (`assertDontSee('id="tugasFilterCard" class="tugas-filter-card d-none"')`).
  - Judul panel "Daftar Penugasan & Progres Mentee" **TERLIHAT**.
  - Unified header mentee **TERSEMBUNYI** (`d-none`).
- **Modul Logbook** (`/pembimbing/logbook`):
  - Kartu Filter Nama Peserta **TERLIHAT** (`assertDontSee('id="logbookFilterCard" class="logbook-filter-card d-none"')`).
  - Judul panel "Daftar Logbook & Catatan Pembimbing" **TERLIHAT**.
  - Unified header mentee **TERSEMBUNYI** (`d-none`).

### Skenario 2: Mode Fokus Mentee (`?penempatan_id=X`)
- **Modul Tugas** (`/pembimbing/tugas?penempatan_id=X`):
  - Kartu Filter Nama Peserta **TERSEMBUNYI** (`assertSee('id="tugasFilterCard" class="tugas-filter-card d-none"')`).
  - Judul default tersembunyi.
  - Unified header mentee **TERLIHAT** dengan Avatar inisial, nama mentee, nama institusi, dan tombol `← Semua Mentee`.
- **Modul Logbook** (`/pembimbing/logbook?penempatan_id=X`):
  - Kartu Filter Nama Peserta **TERSEMBUNYI** (`assertSee('id="logbookFilterCard" class="logbook-filter-card d-none"')`).
  - Judul default tersembunyi.
  - Unified header mentee **TERLIHAT** dengan Avatar inisial, nama mentee, nama institusi, dan tombol `← Semua Mentee` (persis seperti Gambar 1).

---

# 7. RISIKO, EDGE CASES, & MITIGASI

1. **Edge Case: Tombol Back Browser (`popstate`)**:
   - Jika pengguna menekan tombol Back di browser saat berada di mode fokus, listener `window.addEventListener('popstate')` harus memanggil `resetToAllMentees()` yang otomatis menampilkan kembali kartu filter dan mengembalikan judul default panel.
2. **Edge Case: Layar Mobile (<768px)**:
   - Pastikan `.header-mentee-col` dan tombol `#btnBackToMentees` tidak bertumpukan secara aneh di layar kecil (terapkan flex-wrap bila lebar layar < 576px).
3. **Risiko Breaking Test**:
   - Test sebelumnya yang assert ketiadaan nama di header logbook harus disesuaikan secara presisi agar memeriksa nama di header saat mode fokus aktif.

---

# 8. KRITERIA SELESAI (DEFINITION OF DONE)

- [x] Kartu filter tersembunyi otomatis di modul Tugas saat masuk ke detail tugas mentee.
- [x] Kartu filter tersembunyi otomatis di modul Logbook saat masuk ke detail logbook mentee.
- [x] Header panel Logbook pada mode fokus menampilkan Avatar inisial, Nama Peserta, dan Institusi di kiri, serta tombol `← Semua Mentee` di kanan (100% seragam dengan modul Tugas).
- [x] Navigasi client-side (klik kartu & klik tombol kembali) serta browser back button berjalan mulus tanpa reload halaman.
- [x] Kompilasi Vite (`npm run build`) berhasil 100% tanpa error.
- [x] Linter styling PHP `php vendor/bin/pint --test` lulus 100%.
- [x] Seluruh automated test pembimbing (19 tests) lulus hijau 100%.
- [x] Tidak ada inline CSS (`style="..."`) atau inline JS di Blade.

