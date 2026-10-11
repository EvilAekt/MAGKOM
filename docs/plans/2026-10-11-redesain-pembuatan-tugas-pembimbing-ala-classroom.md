# Plan: Redesain Halaman Pembuatan Tugas Pembimbing (Dedicated Assignment Workspace ala Google Classroom)

- **Tanggal**: 2026-10-11
- **Status**: Selesai (Completed)
- **Author**: Antigravity (Senior Full-Stack Architect / web-implementation)
- **Target Pembaca / Pelaksana**: Junior Developer & Autonomous Coding AI (seperti Gemini 1.5/3.0 Flash)
- **Target File**:
  - Routing: [`routes/web.php`](file:///g:/MAGKOM/routes/web.php)
  - Controller: [`app/Http/Controllers/Pembimbing/TugasController.php`](file:///g:/MAGKOM/app/Http/Controllers/Pembimbing/TugasController.php)
  - Blade View Baru: [`resources/views/pembimbing/tugas/create.blade.php`](file:///g:/MAGKOM/resources/views/pembimbing/tugas/create.blade.php)
  - Integrasi Tombol & Pembersihan Modal Lama:
    - [`resources/views/pembimbing/dashboard.blade.php`](file:///g:/MAGKOM/resources/views/pembimbing/dashboard.blade.php)
    - [`resources/views/pembimbing/tugas/index.blade.php`](file:///g:/MAGKOM/resources/views/pembimbing/tugas/index.blade.php)
  - Test Suite: [`tests/Feature/ClassroomAssignmentWorkspaceTest.php`](file:///g:/MAGKOM/tests/Feature/ClassroomAssignmentWorkspaceTest.php)

---

## 1. Tujuan & Latar Belakang (Executive Summary)

### Masalah Pengguna (User Problem):
Sebelumnya, antarmuka pembuatan tugas pembimbing disajikan dalam bentuk jendela sembul (**Modal Pop-up**). Dari hasil evaluasi pengalaman pengguna (*User Experience*):
1. **Ruang Kerja Terlalu Sempit**: Area modal terasa sesak ketika pembimbing ingin menyusun petunjuk tugas yang panjang, menyertakan beberapa kriteria output, dan melampirkan berkas panduan.
2. **Keterbatasan Penglihatan Kontekstual**: Modal menutupi latar belakang dashboard namun tetap tidak memberikan ruang yang leluasa untuk menulis instruksi terstruktur secara fokus.
3. **Standar Aplikasi Edukasi Modern**: Pembimbing terbiasa dengan antarmuka penugasan terdedikasi berlayar penuh (*full-page distraction-free workspace*) seperti pada **Google Classroom** atau LMS modern.

### Solusi yang Dirancang (Proposed Solution):
Mengganti modal pop-up pembuatan tugas dengan **Dedicated Full-Page Assignment Workspace** (`/pembimbing/tugas/create`):
1. **Top Header Bar**: Tombol Navigasi Kembali (`← Kembali`), Indikator Status Workspace, dan Tombol Eksekusi Utama (`Tugaskan Sekarang` dengan palet Sleman Blue `#0641CC`).
2. **Tata Letak Dua Kolom (Two-Column Classroom Grid)**:
   - **Kolom Kiri (Main Assignment Canvas - 70%)**:
     - Input Judul Tugas yang besar dan elegan (Notion/Classroom style).
     - Textarea Petunjuk & Deskripsi Tugas yang lapang dengan tinggi nyaman ($\ge 260\text{ px}$).
     - Kotak Materi & Sumber Daya Acuan (Attachment Hub): Input tautan referensi eksternal (GitHub/Figma/Docs) dan Dropzone berkas multi-format (PDF, Word, Excel, PPT, ZIP hingga 10 MB) dengan kartu pratinjau interaktif.
   - **Kolom Kanan (Assignment Settings Sidebar - 30%)**:
     - Kartu Target Mentee (dropdown mentee aktif dengan avatar, jurusan/institusi, dan batas tanggal selesai magang).
     - Kategori Penugasan & Segmented Control Tingkat Kompleksitas (Rendah, Sedang, Tinggi).
     - Batas Waktu (Deadline picker dengan auto-constraint tanggal maksimal sesuai masa magang mentee terpilih).
3. **Pembersihan & Refactor**:
   - Seluruh tombol `+ Beri Tugas Baru` (baik di Dashboard maupun di Daftar Tugas) langsung diarahkan (*redirect/link*) ke halaman `route('pembimbing.tugas.create')`.
   - Kode modal pop-up lama di [`pembimbing/dashboard.blade.php`](file:///g:/MAGKOM/resources/views/pembimbing/dashboard.blade.php) dan [`pembimbing/tugas/index.blade.php`](file:///g:/MAGKOM/resources/views/pembimbing/tugas/index.blade.php) dihapus agar tidak terjadi redundansi (*single source of truth*).

---

## 2. Asumsi Teknis & Pertanyaan Terbuka

### Asumsi Teknis:
1. **Skema Database Tetap Kompatibel**: Tidak memerlukan migrasi database baru. Skema tabel `tugas` yang sudah ada (`judul`, `deskripsi`, `kategori`, `kompleksitas`, `deadline`, `link_referensi`, `file_lampiran`, `nama_file_lampiran`, `ukuran_file_lampiran`) sudah 100% mendukung kebutuhan ini.
2. **Validasi Request Eksisting**: [`app/Http/Requests/TugasRequest.php`](file:///g:/MAGKOM/app/Http/Requests/TugasRequest.php) tetap menjadi gerbang validasi server-side utama (aturan deadline tidak boleh lampau, deadline tidak boleh melebihi masa magang mentee, MIME whitelist dokumen, dan batas 10 MB).
3. **Responsif Desktop & Mobile**: Di layar lebar ($\ge 1024\text{ px}$) menggunakan grid 2 kolom (8:4). Di layar tablet/ponsel ($< 1024\text{ px}$) otomatis tersusun vertikal (*stacked*) dengan urutan: Canvas Utama $\rightarrow$ Sidebar Pengaturan $\rightarrow$ Tombol Submit.
4. **Parameter Mentee Otomatis (`?penempatan_id=X`)**: Jika pembimbing mengklik "Beri Tugas" dari kartu mentee tertentu, halaman `create` dapat menerima query parameter `penempatan_id` dan secara otomatis memilih mentee tersebut pada dropdown.

### Pertanyaan Terbuka:
*Tidak ada ambiguitas kritis. Seluruh kebutuhan fungsional dan estetika telah selaras dengan arsitektur MagKom dan desain Figma Diskominfo Sleman.*

---

## 3. Pendekatan Desain & Arsitektur

### Evaluasi Opsi Teknis:

| Kriteria Evaluasi | Opsi A: Side Drawer / Slide-Over Modal | Opsi B: Dedicated Full-Page Workspace (Rekomendasi ala Google Classroom) |
| :--- | :--- | :--- |
| **Kenyamanan Mengetik (Ergonomi)** | Kurang maksimal, lebar drawer terbatas (biasanya maks 500–600px). | **Sangat Nyaman**: Layar penuh memberikan ruang lega untuk instruksi mendalam dan pratinjau lampiran. |
| **Dukungan URL Langsung (Deep Linking)** | Sulit dibagikan atau di-*bookmark*, rentan hilang jika tombol *back* browser tertekan. | **Sangat Baik**: Memiliki rute URL resmi (`/pembimbing/tugas/create`), mendukung tombol navigasi browser secara alami. |
| **Kesesuaian dengan Google Classroom** | Berbeda dengan Classroom. | **100% Mengadopsi Pola Classroom**: Kolom kiri untuk konten tugas & materi, kolom kanan untuk pengaturan penerima/deadline. |
| **Kompleksitas Kode & Pemeliharaan** | Memerlukan JavaScript state manager untuk buka/tutup overlay drawer. | **Sederhana & Bersih**: Standar Laravel MVC Blade page murni tanpa overhead modal backdrop/z-index bugs. |

> **Keputusan Arsitektur**: Memilih **Opsi B** (Dedicated Full-Page Workspace). Pola ini terbukti paling stabil, mudah diuji, dan memberikan pengalaman penulisan tugas terbaik bagi pembimbing lapangan.

---

## 4. Perubahan File (File Impact Table)

| File Path | Aksi | Ringkasan Perubahan Spesifik |
| :--- | :--- | :--- |
| [`routes/web.php`](file:///g:/MAGKOM/routes/web.php) | **Modify** | Daftarkan route GET `/pembimbing/tugas/create` (`pembimbing.tugas.create`) tepat sebelum route wildcard `/pembimbing/tugas/{tugas}`. |
| [`app/Http/Controllers/Pembimbing/TugasController.php`](file:///g:/MAGKOM/app/Http/Controllers/Pembimbing/TugasController.php) | **Modify** | Tambahkan method `create(Request $request)` yang mengambil daftar `$penempatanList` aktif dan me-render view `pembimbing.tugas.create`. |
| [`resources/views/pembimbing/tugas/create.blade.php`](file:///g:/MAGKOM/resources/views/pembimbing/tugas/create.blade.php) | **Create** | Halaman utama full-page assignment workspace ala Google Classroom (Top Bar, 2-Column Canvas, File Picker, Settings Sidebar). |
| [`resources/views/pembimbing/dashboard.blade.php`](file:///g:/MAGKOM/resources/views/pembimbing/dashboard.blade.php) | **Modify** | Ganti event onclick tombol hero `+ Beri Tugas Baru` menjadi tag hyperlink `<a>` menuju `route('pembimbing.tugas.create')`. Hapus markup modal pop-up lama dari footer dashboard. |
| [`resources/views/pembimbing/tugas/index.blade.php`](file:///g:/MAGKOM/resources/views/pembimbing/tugas/index.blade.php) | **Modify** | Ganti tombol `+ Buat Tugas Baru` menjadi link ke `route('pembimbing.tugas.create')`. Hapus markup modal pop-up lama. |
| [`tests/Feature/ClassroomAssignmentWorkspaceTest.php`](file:///g:/MAGKOM/tests/Feature/ClassroomAssignmentWorkspaceTest.php) | **Create** | Test suite otomatis untuk verifikasi akses halaman `create`, pre-selection mentee via query parameter, penyimpanan tugas sukses, dan proteksi otorisasi. |

---

## 5. Panduan Implementasi Rinci (Senior Dev Briefing for Junior Dev / AI)

> 💡 **Instruksi Khusus untuk Junior Developer / AI Pelaksana**:
> Ikuti urutan fase di bawah ini secara disiplin. Jangan melompati langkah. Pastikan setiap blok kode mengikuti standar kode yang sudah dicantumkan.

### Fase 1: Routing & Controller Method
1. **Langkah 1.1: Daftarkan Route di [`routes/web.php`](file:///g:/MAGKOM/routes/web.php)**:
   Cari grup prefix `pembimbing` dan tambahkan rute `create` **sebelum** rute `{tugas}` agar tidak bentrok dengan parameter model binding:
   ```php
   Route::get('/tugas', [PembimbingTugasController::class, 'index'])->name('tugas.index');
   Route::get('/tugas/create', [PembimbingTugasController::class, 'create'])->name('tugas.create'); // <-- Tambahkan di sini
   Route::post('/tugas', [PembimbingTugasController::class, 'store'])->name('tugas.store');
   Route::get('/tugas/{tugas}', [PembimbingTugasController::class, 'show'])->name('tugas.show');
   ```

2. **Langkah 1.2: Implementasikan Method `create()` di [`app/Http/Controllers/Pembimbing/TugasController.php`](file:///g:/MAGKOM/app/Http/Controllers/Pembimbing/TugasController.php)**:
   ```php
   /**
    * Menampilkan form layar penuh pembuatan tugas baru (Assignment Workspace ala Classroom).
    */
   public function create(Request $request): View
   {
       $user = $request->user();

       $penempatanList = Penempatan::with(['peserta.profil.jurusan', 'bidang'])
           ->where(function ($q) use ($user) {
               if (! $user->isAdmin() && ! $user->isSuperAdmin()) {
                   $q->where('pembimbing_id', $user->id);
               }
           })
           ->where('status', StatusPenempatanEnum::Aktif)
           ->get();

       $selectedPenempatanId = $request->integer('penempatan_id', 0);

       return view('pembimbing.tugas.create', [
           'penempatanList' => $penempatanList,
           'selectedPenempatanId' => $selectedPenempatanId > 0 ? $selectedPenempatanId : null,
       ]);
   }
   ```

3. **Langkah 1.3: Sesuaikan Arah Redirect di Method `store()`**:
   Di [`app/Http/Controllers/Pembimbing/TugasController.php`](file:///g:/MAGKOM/app/Http/Controllers/Pembimbing/TugasController.php) pada method `store()`:
   Setelah berhasil membuat tugas baru, arahkan pembimbing kembali ke rute `pembimbing.tugas.index` atau `pembimbing.tugas.show` dengan pesan sukses:
   ```php
   return redirect()->route('pembimbing.tugas.show', $tugas->id)
       ->with('success', 'Tugas baru berhasil dibuat dan ditugaskan kepada mentee.');
   ```

---

### Fase 2: Pembangunan Antarmuka Blade Workspace (`resources/views/pembimbing/tugas/create.blade.php`)

Buat berkas baru [`resources/views/pembimbing/tugas/create.blade.php`](file:///g:/MAGKOM/resources/views/pembimbing/tugas/create.blade.php) dengan spesifikasi desain:

1. **Top Header Bar (Sticky Navigation & Action)**:
   - Tombol kembali bergaya pills: `← Kembali ke Daftar Tugas` menuju `route('pembimbing.tugas.index')`.
   - Title header: `Buat Tugas Baru` dengan sub-teks instruksi.
   - Action bar di pojok kanan atas:
     - Tombol `Batal` (secondary button netral).
     - Tombol `Tugaskan Sekarang` (warna `#0641CC` Sleman Blue, icon paper airplane / check, `type="submit"` yang terhubung dengan form).

2. **Form Tag Utama**:
   - `action="{{ route('pembimbing.tugas.store') }}"`
   - `method="POST"`
   - `enctype="multipart/form-data"`
   - `@csrf`
   - Berikan `id="formBuatTugasClassroom"`.

3. **Kolom Kiri: Canvas Penugasan (Main Canvas - 68%)**:
   - **Kartu 1: Konten Tugas**:
     - **Input Judul**:
       - Label: *"Judul Penugasan"*
       - Elemen: `<input type="text" name="judul" required placeholder="Judul tugas (contoh: Analisis Skema Basis Data Layanan Publik)...">`
       - Styling: Ukuran font $1.25\text{rem}$ ($20\text{px}$), font-weight $700$, padding lega, border halus dengan focus ring Sleman Blue.
     - **Textarea Deskripsi & Petunjuk Pengerjaan**:
       - Label: *"Petunjuk & Arahan Pengerjaan"*
       - Elemen: `<textarea name="deskripsi" rows="8" placeholder="Jelaskan secara detail ekspektasi luaran, langkah-langkah kerja, format laporan, atau catatan khusus untuk mentee..."></textarea>`
       - Styling: Textarea luas, border-radius $0.5\text{rem}$, resize vertikal, font $0.95\text{rem}$.

   - **Kartu 2: Materi Acuan & Lampiran Sumber Daya (Attachment Hub ala Classroom)**:
     - Header: Ikon paperclip `📎` + *"Lampirkan Sumber Daya & Berkas Acuan"*.
     - Deskripsi singkat: *"Berikan panduan teknis, TOR, tautan dokumen kerja (Docs/Figma/GitHub), atau berkas acuan kerja untuk mentee."*
     - **Input Tautan Referensi Eksternal**:
       - Input group dengan prefix icon `🔗`.
       - Name: `link_referensi`.
       - Placeholder: `https://github.com/... atau https://docs.google.com/...`.
     - **Dropzone Unggah Berkas Acuan**:
       - Area dropzone modern dengan dashed border, latar belakang `#F8FAFC`, icon dokumen awan.
       - Teks panduan format: *"Pilih berkas atau tarik & lepas ke sini"*
       - Sub-teks: *"Format: PDF, Word (DOCX), Excel (XLSX), PPTX, ZIP, Gambar (Maks 10 MB)"*.
       - `<input type="file" name="file_lampiran" id="file_lampiran" class="hidden" accept=".pdf,.doc,.docx,.xls,.xlsx,.csv,.ppt,.pptx,.zip,.png,.jpg,.jpeg">`.
       - **Pratinjau Berkas Terpilih (Interactive File Card)**:
         - Saat file dipilih via JS, tampilkan box kartu file: badge format warna (Merah PDF, Biru Word, Hijau Excel, Ungu ZIP), nama file, ukuran format, dan tombol hapus file (`✕`).

4. **Kolom Kanan: Panel Pengaturan Penugasan (Settings Sidebar - 32%)**:
   - Container `sticky top-6`.
   - **Bidang 1: Target Mentee (Penerima Tugas)**:
     - Dropdown `<select name="penempatan_id" id="select_penempatan" required>`
     - Setiap opsi menampilkan: `Nama Mentee — Institusi/Jurusan`.
     - Data attributes pada setiap option: `data-selesai="{{ $penempatan->tgl_selesai->toDateString() }}"`, `data-kampus="{{ $penempatan->peserta->profil->institusi ?? '' }}"`.
     - **Live Info Box Mentee**: Kotak informasi kecil di bawah dropdown yang otomatis menampilkan periode magang mentee terpilih dan sisa durasi hari magang.
   - **Bidang 2: Kategori Tugas**:
     - Input `<input type="text" name="kategori" list="kategori_suggestions" required placeholder="Contoh: Pengembangan Web">`
     - `<datalist id="kategori_suggestions">` berisi opsi umum: *Pengembangan Web, UI/UX Design, Jaringan & Keamanan, Analisis Data, Administrasi TI, Dokumentasi Teknis*.
   - **Bidang 3: Tingkat Kompleksitas**:
     - Segmented control 3 pilihan (Radio pills):
       - `rendah`: Badge hijau (Rendah)
       - `sedang`: Badge kuning/amber (Sedang)
       - `tinggi`: Badge ungu/merah (Tinggi)
   - **Bidang 4: Tenggat Waktu (Deadline)**:
     - Input `<input type="date" name="deadline" id="input_deadline" required min="{{ date('Y-m-d') }}">`
     - Peringatan dinamis jika tanggal yang dipilih melampaui `tgl_selesai` magang mentee terpilih.
   - **Kartu Tips Pembimbing Diskominfo**:
     - Box edukasi bertema Sleman Blue muda: *"💡 Tips Pembimbing: Pastikan deadline penugasan tidak melebihi tanggal selesai magang mentee agar proses penilaian akhir dapat berjalan tepat waktu."*

---

### Fase 3: Integrasi Link Tombol & Pembersihan Modal Lama

1. **Pembaruan di [`resources/views/pembimbing/dashboard.blade.php`](file:///g:/MAGKOM/resources/views/pembimbing/dashboard.blade.php)**:
   - Ganti tombol hero `+ Beri Tugas Baru` (line 166):
     ```blade
     {{-- SEBELUMNYA (onclick modal): --}}
     {{-- <button onclick="openModalBuatTugasDashboard()" class="...">+ Beri Tugas Baru</button> --}}

     {{-- SESUDAHNYA (link classroom page): --}}
     <a href="{{ route('pembimbing.tugas.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold text-white shadow-sm transition-all duration-200" style="background: rgba(255,255,255,0.18); border: 1px solid rgba(255,255,255,0.35); backdrop-filter: blur(8px);">
         <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
         <span>+ Beri Tugas Baru</span>
     </a>
     ```
   - Hapus komponen modal `modalBuatTugasDashboard` di bagian bawah file dashboard beserta script `openModalBuatTugasDashboard()` dan `closeModalBuatTugasDashboard()`.

2. **Pembaruan di [`resources/views/pembimbing/tugas/index.blade.php`](file:///g:/MAGKOM/resources/views/pembimbing/tugas/index.blade.php)**:
   - Ganti tombol `+ Buat Tugas Baru` menjadi hyperlink `<a href="{{ route('pembimbing.tugas.create') }}">`.
   - Hapus komponen modal pop-up lama `modalBuatTugas` dan skrip modalnya.

---

### Fase 4: Otomatisasi Pengujian (Automated Test Suite)

Buat test file baru [`tests/Feature/ClassroomAssignmentWorkspaceTest.php`](file:///g:/MAGKOM/tests/Feature/ClassroomAssignmentWorkspaceTest.php):
1. `test_pembimbing_bisa_mengakses_halaman_classroom_workspace_create`:
   - Pembimbing melakukan `GET /pembimbing/tugas/create`.
   - Assert status 200 (OK).
   - Assert see teks judul "Buat Tugas Baru" dan form elemen utama.
2. `test_halaman_create_menerima_preselected_penempatan_id_via_query_string`:
   - `GET /pembimbing/tugas/create?penempatan_id={$penempatan->id}`.
   - Assert option mentee tersebut berstatus `selected`.
3. `test_pembimbing_bisa_submit_tugas_dari_halaman_classroom_workspace`:
   - Kirim POST request dengan judul, deskripsi, link referensi, dan berkas lampiran PDF.
   - Assert tugas tersimpan di basis data dengan metadata lampiran lengkap.
   - Assert redirect ke detail penugasan `pembimbing.tugas.show`.
4. `test_peserta_ditolak_mengakses_halaman_tugas_create`:
   - Peserta magang mencoba `GET /pembimbing/tugas/create`.
   - Assert status 403 Forbidden.

---

## 6. Strategi Pengujian & QA Verifikasi

### Pengujian Otomatis (Command Line):
1. Jalankan unit test spesifik:
   ```bash
   php artisan test --filter="ClassroomAssignmentWorkspaceTest"
   ```
2. Jalankan test regression pembimbing untuk memastikan 0 regresi:
   ```bash
   php artisan test --filter="RichTaskAssignmentTest|PembimbingDashboardEnhancementTest|PembimbingFullJobDeskTest|PembimbingPortalFeatureTest|TugasLogbookTest"
   ```

### Verifikasi Visual Browser (Chrome Subagent):
1. Navigasi ke `/pembimbing/dashboard`, klik tombol `+ Beri Tugas Baru`. Pastikan peramban berpindah secara mulus ke `/pembimbing/tugas/create`.
2. Verifikasi layout 2 kolom (Canvas utama di kiri, Panel pengaturan di kanan).
3. Uji interaksi dropzone berkas: pilih berkas dummy PDF/Word, verifikasi muncul kartu pratinjau nama & ukuran berkas.
4. Uji submit tugas baru, pastikan berhasil tersimpan dan dialihkan ke halaman detail tugas dengan alert sukses.

---

## 7. Arsitektur Keamanan, Mitigasi Risiko & Aksesibilitas

| Kategori | Pertimbangan Teknis & Solusi Mitigasi |
| :--- | :--- |
| **Keamanan Upload (Anti-RCE)** | File disimpan di disk privat (`storage/app/private/tugas/{id}/`), bukan di `public/`. Whitelist MIME-type biner ketat (`pdf, doc, docx, xls, xlsx, ppt, pptx, zip, gambar`). |
| **Proteksi IDOR** | Pembimbing hanya dapat menugaskan mentee yang berada di bawah bimbingannya (`pembimbing_id === auth()->id()`). Gate authorization menolak manipulasi ID penempatan mentee lain. |
| **Sanitasi Tautan Eksternal** | Validasi aturan `url` Laravel memastikan skema valid (`http://` atau `https://`). Render link di sisi mentee menggunakan relasi `target="_blank" rel="noopener noreferrer"`. |
| **Aksesibilitas (a11y)** | Seluruh elemen `<input>` dan `<select>` memiliki `<label>` eksplisit dengan atribut `for`, fokus keyboard terlihat jelas (*focus ring* biru), dan kontras teks memenuhi WCAG 2.1 AA. |

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Route `pembimbing.tugas.create` terdaftar dengan benar di `routes/web.php`.
- [x] Method `create()` di `Pembimbing\TugasController` mengambil mentee aktif dan me-render view.
- [x] File view `resources/views/pembimbing/tugas/create.blade.php` selesai dibuat dengan tata letak 2 kolom ala Google Classroom.
- [x] Drag-and-drop file picker berfungsi lancar dengan indikator badge nama & ukuran dokumen.
- [x] Auto-constraint batas deadline mentee berjalan interaktif di sisi klien dan tervalidasi di sisi server.
- [x] Tombol `+ Beri Tugas Baru` di Dashboard dan Index Tugas berhasil diarahkan ke halaman `create`.
- [x] Seluruh sisa modal pop-up lama telah dibersihkan dari kode dashboard dan index tugas.
- [x] Test suite baru `ClassroomAssignmentWorkspaceTest` lulus 100% (**Green**, 4 passed).
- [x] Seluruh test regression modul pembimbing eksisting lulus 100% (**0 Regresi**, 44 passed total).
- [x] Verifikasi visual di peramban menunjukkan antarmuka bersih, luas, responsif, dan elegan.

---

## 9. Catatan Perubahan & Saran Lanjutan (Out of Scope)

- **Catatan Pengerjaan**: 
  - Halaman pembuatan tugas berhasil dirombak total dari modal sempit menjadi halaman penuh ala Google Classroom (`/pembimbing/tugas/create`).
  - Layout dua kolom responsif: Kolom kiri untuk Judul tugas besar, Petunjuk/Deskripsi textarea luas, Attachment Hub (link eksternal & drag-and-drop file picker hingga 10MB). Kolom kanan untuk Pengaturan Penugasan (Target mentee, Kategori dengan autocomplete, Segmented control kompleksitas, Datepicker deadline dengan batasan masa magang, dan Tips pembimbing).
  - Modal pop-up lama berhasil dibersihkan dari `pembimbing/dashboard.blade.php` dan `pembimbing/tugas/index.blade.php`.
  - Tombol `+ Beri Tugas Baru` pada Dashboard dan Daftar Tugas terhubung langsung ke rute `pembimbing.tugas.create`.
  - 44 automated tests lulus (219 assertions).
  - Browser verification mengonfirmasi navigasi instan dan visual elegan.
- **Saran Lanjutan (Out of Scope)**:
  - Fitur simpan draf otomatis (*autosave draft*) ke local storage jika peramban tidak sengaja tertutup saat pembimbing sedang mengetik petunjuk tugas.
  - Dukungan multiple attachments (lebih dari 1 file) jika pembimbing di masa depan meminta bundel berkas lampiran terpisah.
