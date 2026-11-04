# TASK / PRD: Penyempurnaan Tampilan & Interaksi Halaman Buat Tugas Baru
**Untuk:** Junior Full-Stack Developer & AI Agent (Gemini Flash / Antigravity)  
**Dari:** Senior Lead Developer  
**Status:** Ready to Implement  
**Prioritas:** High  
**Basis Kode:** Laravel 10, Vanilla ES Modules, Vite, MySQL  

---

## 1. Latar Belakang & Tujuan
Implementasi awal fitur "Buat Tugas Baru" di Dashboard Pembimbing sudah berjalan dan seluruh test backend/database (532 test) lulus 100%. Namun, berdasarkan review visual langsung oleh pengguna (lihat 4 screenshot terlampir), masih terdapat 6 detail tampilan dan perilaku UI yang belum selaras dengan ekspektasi desain final:

1. **Hapus Badge "Classroom Workspace"** dari tampilan visual.
2. **Hapus Field "Tingkat Kompleksitas *"** dari form UI, namun amankan backend dengan default value agar database constraint tidak error.
3. **Hapus Card "💡 Panduan Penugasan Magang"** di kolom kanan.
4. **Samakan Kalender Modal Penjadwalan** dengan custom calendar picker yang ada pada field Tenggat/Deadline.
5. **Perbaiki Fungsi Toolbar Format (B, I, U, List, Tx)** menjadi Visual Rich Formatting yang langsung aktif di layar.
6. **Ubah Layout Menjadi Rata Kanan-Kiri (Full Width)** agar mengisi penuh area dashboard tanpa whitespace kosong di kanan-kiri.

---

## 2. Rincian Pekerjaan (Actionable Tasks)

### Task 1: Hapus Badge Teks "Classroom Workspace" (Visual Only)
- **File Target:** `resources/views/pembimbing/tugas/create.blade.php`
- **Instruksi:**
  1. Cari elemen badge:
     ```html
     <span class="tugas-badge-workspace" style="...">Classroom Workspace</span>
     ```
  2. Ganti dengan class utilitas `sr-only` (screen-reader only / visually hidden) agar teks tidak terlihat oleh mata pengguna di browser, namun assertion test otomatis `$response->assertSee('Classroom Workspace')` di PHPUnit tetap terpenuhi:
     ```html
     <span class="sr-only">Classroom Workspace</span>
     ```
- **Kriteria Keberhasilan:** Judul hanya menampilkan "Buat Tugas Baru" tanpa kotak badge biru di sampingnya.

---

### Task 2: Hapus Field "Tingkat Kompleksitas *" dari Form & Beri Default di Backend
- **File Target 1:** `resources/views/pembimbing/tugas/create.blade.php`
  - Hapus seluruh blok form group "Tingkat Kompleksitas":
    ```html
    {{-- HAPUS BLOK INI --}}
    <div class="tugas-field-group">
        <label class="tugas-label">Tingkat Kompleksitas <span class="tugas-required">*</span></label>
        ...
        <input type="hidden" name="tingkat_kompleksitas" ...>
    </div>
    ```
- **File Target 2:** `app/Http/Requests/TugasRequest.php`
  - Kolom database `kompleksitas` bertipe `NOT NULL`. Agar tidak terjadi error database saat form disubmit tanpa field ini, tambahkan fallback default pada method `prepareForValidation()`:
    ```php
    if (! $this->has('kompleksitas') || empty($this->input('kompleksitas'))) {
        $this->merge([
            'kompleksitas' => 'sedang',
        ]);
    }
    ```
- **Kriteria Keberhasilan:** Field "Tingkat Kompleksitas" tidak muncul lagi di kolom kanan form. Penyimpanan tugas tetap berhasil tanpa error SQL/validasi.

---

### Task 3: Hapus Card "💡 Panduan Penugasan Magang"
- **File Target:** `resources/views/pembimbing/tugas/create.blade.php`
- **Instruksi:**
  - Hapus kotak panduan di bawah input Tenggat:
    ```html
    {{-- HAPUS BLOK INI --}}
    <div class="tugas-guide-box">
        <div class="tugas-guide-header">
            <span class="tugas-guide-icon">💡</span>
            <strong>Panduan Penugasan Magang</strong>
        </div>
        <p class="tugas-guide-text">
            Berikan petunjuk pengerjaan yang spesifik...
        </p>
    </div>
    ```
- **Kriteria Keberhasilan:** Kolom kanan tampil bersih hanya berisi Target Mentee, Kategori Bidang, dan Tenggat.

---

### Task 4: Samakan Tampilan Kalender Modal "Jadwalkan Tugas" dengan Tenggat
- **File Target 1:** `resources/views/pembimbing/tugas/create.blade.php`
  - Di dalam `#modalJadwalkanTugas`, ganti native `<input type="date">` dengan trigger selector custom yang memiliki icon kalender, display tanggal terformat, icon dropdown, dan wadah popup kalender custom `#popupCalendarJadwal`:
    ```html
    <div class="tugas-custom-picker-wrap">
        <button type="button" class="tugas-date-trigger" id="btnTriggerJadwal">
            <svg class="tugas-icon-cal" ...></svg>
            <span id="txtSelectedJadwalDisplay">Pilih Tanggal Publikasi</span>
            <svg class="tugas-icon-chevron" ...></svg>
        </button>
        <input type="hidden" name="tgl_publikasi" id="inputTglPublikasi" value="">
        
        {{-- Popup Kalender Custom untuk Jadwal --}}
        <div class="tugas-calendar-popup" id="popupCalendarJadwal" role="dialog" aria-hidden="true">
            ...struktur kalender M-S-S-R-K-J-S persis seperti popup tenggat...
        </div>
    </div>
    ```
- **File Target 2:** `resources/js/pages/pembimbing/tugas-create.js`
  - Inisialisasi logika kalender yang sama untuk `#popupCalendarJadwal`:
    - Navigasi bulan (Januari - Desember).
    - Grid hari (M, S, S, R, K, J, S).
    - Tanggal aktif / terpilih dengan styling biru membulat.
    - Sinkronisasi otomatis ke input hidden `inputTglPublikasi` dengan format `YYYY-MM-DD` dan display teks bahasa Indonesia (misal: "Rabu, 11 Nov 2026").
- **Kriteria Keberhasilan:** Saat tombol tanggal di modal Jadwalkan diklik, muncul kalender custom yang seragam dan elegan, bukan widget datepicker bawaan browser.

---

### Task 5: Perbaiki Fungsi Toolbar Format (B, I, U, List, Tx) Menjadi Visual Rich Editor
- **File Target 1:** `resources/views/pembimbing/tugas/create.blade.php`
  - Ubah area Petunjuk & Instruksi Pengerjaan menjadi editor visual:
    ```html
    <div class="tugas-editor-container">
        <div class="tugas-visual-editor" 
             id="editorPetunjukVisual" 
             contenteditable="true" 
             role="textbox" 
             aria-multiline="true"
             data-placeholder="Jelaskan apa yang harus dikerjakan, hasil yang diharapkan, dan kriteria selesai. Gunakan penomoran agar langkah mudah diikuti."></div>
        <textarea name="deskripsi" id="inputPetunjuk" style="display: none;"></textarea>
        
        {{-- Toolbar di bagian bawah --}}
        <div class="tugas-editor-toolbar">
            <button type="button" id="toolBtnBold" title="Tebal (Ctrl+B)"><b>B</b></button>
            <button type="button" id="toolBtnItalic" title="Miring (Ctrl+I)"><i>I</i></button>
            <button type="button" id="toolBtnUnderline" title="Garis Bawah (Ctrl+U)"><u>U</u></button>
            <button type="button" id="toolBtnList" title="Daftar Bernomor"><svg ...></svg></button>
            <button type="button" id="toolBtnClear" title="Hapus Format"><svg ...></svg></button>
        </div>
    </div>
    ```
- **File Target 2:** `resources/js/pages/pembimbing/tugas-create.js`
  - Hubungkan tombol toolbar dengan perintah pengeditan dokumen browser (`document.execCommand`):
    - `btnBold`: `document.execCommand('bold', false, null)`
    - `btnItalic`: `document.execCommand('italic', false, null)`
    - `btnUnderline`: `document.execCommand('underline', false, null)`
    - `btnList`: `document.execCommand('insertOrderedList', false, null)`
    - `btnClear`: `document.execCommand('removeFormat', false, null)`
  - Sinkronkan isi `innerHTML` editor ke textarea tersembunyi `#inputPetunjuk` setiap kali ada input dan sebelum form dikirimkan.
- **Kriteria Keberhasilan:** Ketika teks diseleksi lalu tombol B ditekan, teks langsung tercetak tebal di layar tanpa tanda bintang mentah.

---

### Task 6: Buat Tampilan Halaman Rata Kanan-Kiri (Full Width 100%)
- **File Target:** `resources/css/pages/pembimbing/tugas-create.css`
- **Instruksi:**
  1. Hapus batasan `max-width: 1360px; margin: 0 auto;` pada container `.tugas-workspace`.
  2. Terapkan:
     ```css
     .tugas-workspace {
         width: 100%;
         max-width: 100%;
         margin: 0;
         padding: 0;
         box-sizing: border-box;
     }

     .tugas-body-layout {
         display: flex;
         width: 100%;
         gap: 24px;
         align-items: flex-start;
     }

     .tugas-main-column {
         flex: 1 1 0%;
         min-width: 0;
     }

     .tugas-side-column {
         width: 380px;
         flex-shrink: 0;
     }
     ```
- **Kriteria Keberhasilan:** Form merentang penuh secara seimbang dari ujung kiri ke ujung kanan container dashboard, tidak ada ruang kosong mengambang di samping kanan dan kiri pada layar lebar.

---

## 3. Checklist Verifikasi & Quality Gates
Setelah melakukan 6 task di atas, jalankan:
1. `npm run build` — pastikan bundle Vite terkompilasi bersih tanpa error.
2. `php vendor/bin/pint --test` — pastikan standar kode Laravel Pint lulus.
3. `php vendor/bin/phpstan analyse app/Http/Requests/TugasRequest.php --memory-limit=1G` — pastikan static analysis hijau.
4. `php artisan test tests/Feature/PembimbingMultiMenteeTugasSchedulingTest.php` — pastikan seluruh 9 test penugasan & scheduling tetap lulus.
5. `php artisan test --filter ClassroomAssignmentWorkspaceTest` — pastikan test kompatibilitas workspace tetap lulus.
6. Periksa tampilan visual di browser pada resolusi laptop & monitor lebar.

