# Plan: Sinkronisasi Dialog Tugaskan ke, Menu Opsi Tugaskan, dan Animasi Floating Label Google Classroom

- **Tanggal**: 2026-11-04
- **Author**: Antigravity (Senior Full-Stack Architect / web-planning)
- **Target Pembaca**: Junior Developer & Autonomous Coding AI (seperti Gemini 1.5/3.0 Flash)
- **Status**: Menunggu Persetujuan Pengguna (Awaiting User Approval)
- **Referensi Desain**:
  - `Designnnn/Dialog · Tugaskan ke.png`
  - `Designnnn/Menu · Opsi Tugaskan@1x.png`
  - `Designnnn/Redesign_Tugas_Magang_Semua_Tampilan.html` (iframe `f1` dan `f4`)
  - Tangkapan layar referensi interaksi Google Classroom dari pengguna (`media_1793755399962_21b760a5.png`, `media_1793755416800_4aef31c4.png`, `media_1793755425378_7b460e62.png`)

---

## 1. Tujuan & Latar Belakang

Pengguna meminta penyempurnaan menyeluruh pada halaman **Buat Tugas Baru Pembimbing** agar:
1. **Animasi Floating Label Mulus (Material Design Filled Text Field ala Google Classroom)**:
   - Saat input **Judul** masih kosong dan belum fokus: label `Judul*` berada di tengah kotak pengetikan dengan font normal (`16px`, abu-abu `#5f6368`).
   - Saat kursor masuk (**focus**) atau ketika sudah ada teks yang diisi (**has-value**): label meluncur mulus ke atas (*translateY*) dan mengecil (*scale 0.75 / 12px*), bertransisi warna menjadi biru Sleman Google `#1a73e8` / `#1d4ed8` dengan animasi lengkung `cubic-bezier(0.4, 0, 0.2, 1)`. Teks label tidak pernah hilang.
   - Pada area **Petunjuk (opsional)**: label berada di bagian atas dan bertransisi warna halus ke biru `#1a73e8` saat kursor masuk tanpa ada placeholder panjang yang menimpa kursor pengetikan.
2. **Sinkronisasi Tampilan Dialog `Tugaskan ke` Sesuai Mockup `Designnnn`**:
   - Memastikan dialog modal pemilih mentee bimbingan memiliki struktur, padding, warna header, avatar bulat hijau, checkbox 18×18px, tombol teks Batal, dan tombol pill `Selesai` (lebar 78px, tinggi 34px, warna biru `#1d4ed8`) dengan footer `#e9eef7` persis seperti pada mockup `f1` di `Designnnn`.
3. **Sinkronisasi Dropdown `Menu · Opsi Tugaskan` Sesuai Mockup `Designnnn`**:
   - Mengubah tampilan dropdown tombol split di topbar menjadi panel bersudut halus (lebar 210px, latar `#eef2f9`, border-radius 6px, bayangan `0 4px 16px rgba(0,0,0,0.3)`) yang memuat opsi: *Tugaskan Sekarang*, *Jadwalkan*, *Simpan draf*, garis pemisah `#c9d0de`, dan *Hapus draf*.

---

## 2. Asumsi Teknis & Pertanyaan Terbuka

### Asumsi Teknis:
1. **Kompatibilitas Form & Testing**: Kontrak ID input, nama atribut form (`penempatan_ids[]`, `judul`, `deskripsi`, `kategori`, `kompleksitas`, `deadline`, `aksi_publikasi`), dan assertions test suite (`ClassroomAssignmentWorkspaceTest` dan `PembimbingMultiMenteeTugasSchedulingTest`) tetap terjaga 100%.
2. **Tidak Ada Dependensi Eksternal**: Implementasi floating label dan modal styling murni menggunakan Vanilla CSS dan Vanilla JavaScript (ES Modules) tanpa menambahkan library framework UI pihak ketiga (menegakkan aturan `AGENTS.md`).
3. **State Preserved**: Jika pengguna me-refresh halaman dengan `old('judul')` dari validasi gagal atau data template tugas terpilih, state label otomatis mendeteksi class `.has-value` sehingga label tetap berada di posisi mengambang di atas.

---

## 3. Evaluasi Opsi Pendekatan Teknis

### Opsi A: Murni CSS `:placeholder-shown` (CSS-Only Floating Label)
- **Kelebihan**: Tidak memerlukan JavaScript tambahan untuk mendeteksi isi form.
- **Kekurangan**: Contenteditable div pada area Petunjuk tidak mendukung pseudo-class `:placeholder-shown` sehingga perilakunya tidak konsisten antara input Judul dan editor Petunjuk. Selain itu, sinkronisasi saat autofill browser terkadang gagal mendeteksi `:placeholder-shown`.

### Opsi B (Direkomendasikan): CSS Modern Transition + JavaScript State Sync (`.has-value` & `.is-focused`)
- **Kelebihan**: 
  - Konsisten 100% untuk `<input>` biasa maupun `[contenteditable]` visual editor.
  - Sangat mulus (*smootless*) karena transisi menggunakan akselerasi GPU (`transform: translateY() scale()` dan `transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1)`).
  - Mendukung rehidrasi otomatis dari Blade `old()` atau pemilihan template penugasan secara instan.
- **Keputusan**: **Memilih Opsi B**.

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| [`resources/views/pembimbing/tugas/create.blade.php`](file:///g:/MAGKOM/resources/views/pembimbing/tugas/create.blade.php) | **Modify** | 1. Sesuaikan markup container judul untuk floating label (`.has-value`).<br>2. Sesuaikan modal `modalMenteeDialog` (tombol pill `Selesai`, styling avatar & baris mentee).<br>3. Sesuaikan markup dropdown `menuSplitOptions` (latar `#eef2f9`, item Hapus draf/Batal). |
| [`resources/css/pages/pembimbing/tugas-create.css`](file:///g:/MAGKOM/resources/css/pages/pembimbing/tugas-create.css) | **Modify** | 1. Aturan CSS Floating Label Material: posisi resting (tengah/besar) -> floating (atas/kecil/biru).<br>2. Styling dialog `Tugaskan ke` (lebar 600px, footer `#e9eef7`, tombol Selesai 78x34px).<br>3. Styling dropdown `Opsi Tugaskan` (lebar 210px, latar `#eef2f9`, divider `#c9d0de`). |
| [`resources/js/pages/pembimbing/tugas-create.js`](file:///g:/MAGKOM/resources/js/pages/pembimbing/tugas-create.js) | **Modify** | 1. Logic pendeteksi `has-value` pada `inputJudul` dan `editorPetunjukVisual`.<br>2. Event listener `focus`, `blur`, `input` untuk floating label.<br>3. Handler modal mentee dan aksi menu dropdown yang disesuaikan. |

---

## 5. Rencana Langkah Kerja Bertahap (Step-by-Step Implementation)

### Tahap 1: Implementasi Floating Label Animasi Halus (Material Design)
- [ ] **1.1 Markup Blade**:
  - Berikan class kondisional `has-value` pada `.tugas-field-filled` jika `old('judul')` atau `$selectedTemplate?->judul` bernilai tidak kosong.
  - Pastikan `<label for="inputJudul" class="tugas-field-filled-label">Judul Penugasan*</label>` terposisikan di dalam container relatif.
- [ ] **1.2 CSS Floating Label**:
  - Konfigurasikan `.tugas-field-filled`: `position: relative; height: 56px; padding: 20px 16px 6px;`.
  - Konfigurasikan `.tugas-field-filled-label`: `position: absolute; left: 16px; top: 18px; font-size: 16px; color: #5f6368; pointer-events: none; transition: transform 0.25s cubic-bezier(0.4,0,0.2,1), color 0.25s, font-size 0.25s;`.
  - State Aktif (`:focus-within`, `.is-focused`, `.has-value`): `transform: translateY(-12px) scale(0.75); color: #1a73e8; font-weight: 600;`.
  - Konfigurasikan garis bawah ink-line Material: `::after` dengan `height: 2px; background: #1a73e8; transform: scaleX(0) -> scaleX(1);`.
- [ ] **1.3 JavaScript Floating Label Handler**:
  - Buat fungsi `initFloatingLabels()` di `tugas-create.js`:
    - Cek nilai awal input: jika ada teks, tambahkan class `has-value`.
    - Pasang listener `input` pada `#inputJudul`: toggle `has-value` berdasarkan `input.value.trim().length > 0`.
    - Pasang listener `focus` dan `blur` untuk animasi warna & ink-line.

### Tahap 2: Penyelarasan Dialog "Tugaskan ke" Sesuai Mockup `Designnnn`
- [ ] **2.1 Struktur HTML Dialog**:
  - Modal container: `width: 600px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.3); overflow: hidden;`.
  - Header: tinggi `46px`, padding `0 16px 10px`, font `15px`, border-bottom `1px solid #dadce0`, judul `"Tugaskan ke"`.
  - Baris "Semua peserta": tinggi `44px`, checkbox custom `18×18px` warna `#1d4ed8`, teks `"Semua peserta"`.
  - Baris Setiap Mentee: tinggi `44px`, checkbox `18×18px`, avatar bulat `26×26px` inisial huruf dengan latar warna hijau `#5f9a32` (atau palet bervariasi), nama mentee font `13px` warna `#111827`.
  - Footer: tinggi `50px`, latar `#e9eef7`, padding `0 20px`, flexbox rata kanan.
  - Tombol Batal: teks polos tanpa border warna `#1d4ed8`, font `13px`, font-weight `500`.
  - Tombol Konfirmasi: ganti label menjadi **`Selesai`**, bentuk pill `height: 34px; width: 78px; background: #1d4ed8; border-radius: 17px; color: #fff; font-size: 13px; font-weight: 700;`.
- [ ] **2.2 CSS Dialog Tugaskan ke**:
  - Terapkan styling exact CSS di `tugas-create.css` mengacu pada baris 254 file `Designnnn/Redesign_Tugas_Magang_Semua_Tampilan.html`.
  - Dukung responsivitas layar kecil (`max-width: 92vw`).

### Tahap 3: Penyelarasan Dropdown "Menu · Opsi Tugaskan" Sesuai Mockup `Designnnn`
- [ ] **3.1 Struktur HTML & CSS Dropdown**:
  - Container `.tugas-split-menu`: lebar `210px`, latar `#eef2f9`, border-radius `6px`, bayangan `0 4px 16px rgba(0,0,0,0.3)`, padding `8px 0`.
  - Item menu:
    1. `Tugaskan Sekarang` (`padding: 12px 20px; font-size: 14px; color: #111827; cursor: pointer;`)
    2. `Jadwalkan` (`padding: 12px 20px; font-size: 14px; color: #111827; cursor: pointer;`)
    3. `Simpan draf` (`padding: 12px 20px; font-size: 14px; color: #111827; cursor: pointer;`)
    4. Pembatas: `border-top: 1px solid #c9d0de; margin-top: 6px; padding-top: 12px;`
    5. `Hapus draf` (`padding: 12px 20px; font-size: 14px; color: #b3261e; cursor: pointer;`)
  - Efek hover item: `background: #dbe4f3; color: #0c2a6b;`.

### Tahap 4: Quality Gate & Verifikasi
- [ ] **4.1 Kompilasi Vite**: Jalankan `npm run build` dan pastikan 0 error.
- [ ] **4.2 Code Styling**: Jalankan `php vendor/bin/pint --test`.
- [ ] **4.3 Static Analysis**: Jalankan `./vendor/bin/phpstan analyse app/Http/Requests/TugasRequest.php app/Http/Controllers/Pembimbing/TugasController.php --memory-limit=1G`.
- [ ] **4.4 Feature Tests**: Jalankan:
  - `php artisan test --filter ClassroomAssignmentWorkspaceTest`
  - `php artisan test tests/Feature/PembimbingMultiMenteeTugasSchedulingTest.php`
- [ ] **4.5 Visual Audit**: Uji interaksi di browser: ketik pada judul, hapus kembali teks, periksa floating label, buka dialog mentee, buka dropdown opsi tugaskan.

---

## 6. Strategi Testing

1. **PHPUnit Automation**:
   - Memastikan test `ClassroomAssignmentWorkspaceTest` (6 tes, 37 assertions) dan `PembimbingMultiMenteeTugasSchedulingTest` (9 tes, 43 assertions) tetap **100% HIJAU (PASS)** tanpa ada assertion yang patah akibat perubahan teks atau markup dialog.
2. **Frontend Interaction Tests**:
   - Memastikan event klik pada tombol `Selesai` di dialog mentee memperbarui data peserta terpilih ke form utama dan menutup dialog secara rapi.
   - Memastikan menu dropdown opsi tugaskan membuka modal penjadwalan saat opsi "Jadwalkan" dipilih.
   - Memastikan floating label judul mengapung mulus saat ada nilai dan turun kembali ke tengah saat seluruh teks dihapus.

---

## 7. Risiko & Mitigasi

| Potensi Risiko | Tingkat | Mitigasi |
| :--- | :--- | :--- |
| Label Judul menutupi teks yang diketik pengguna | Rendah | Menggunakan padding atas `20px` pada container dan `transform: translateY(-12px) scale(0.75)` dengan pointer-events: none. |
| Pengguna dengan autofill browser tidak memicu animasi | Sedang | JavaScript menambahkan handler event `animationstart` atau `input` event listener untuk mendeteksi autofill dan menambahkan `.has-value`. |
| Tombol Selesai pada modal mentee tidak menyimpan ID peserta | Tinggi | Mempertahankan ID tombol `btnConfirmMentee` dan logic multi-select yang sudah teruji aman (anti-IDOR) pada `tugas-create.js`. |

---

## 8. Kriteria Selesai (Definition of Done)

- [ ] Label `Judul*` memiliki animasi floating label mulus (Material Design) yang mengecil ke atas saat fokus atau ada isinya, dan kembali besar ke tengah saat kosong.
- [ ] Teks label `Petunjuk (opsional)` bertransisi warna biru halus saat kursor masuk tanpa ada placeholder yang menabrak kursor pengetikan.
- [ ] Dialog `Tugaskan ke` tampil identik dengan mockup `Designnnn` (lebar 600px, avatar bulat inisial, footer `#e9eef7`, tombol Selesai pill 78×34px).
- [ ] Menu `Opsi Tugaskan` tampil identik dengan mockup `Designnnn` (lebar 210px, background `#eef2f9`, item bersih dengan garis pemisah).
- [ ] `npm run build` sukses tanpa warning kritis.
- [ ] Seluruh automated test PHPUnit lulus 100%.
- [ ] Kode mematuhi standar formatting Pint dan analisa PHPStan.

