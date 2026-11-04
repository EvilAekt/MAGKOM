# PRD & Senior Developer Implementation Plan: Redesign Header Mentee, Task Row Polish, & Edit Tugas Classroom Modern

**Target Pelaksana**: Junior Developer / Gemini 3.8 Flash  
**Kode Dokumen**: `PRD-TASK-2026-11-04-PEMBIMBING-TUGAS-POLISH`  
**Status**: Ready for Review & Implementation  
**Tingkat Kerumitan**: Sedang (Frontend Blade, CSS Scoped, Vanilla JS ES Modules, Feature Testing)  

---

## 1. Latar Belakang & Ringkasan Perubahan

Berdasarkan tinjauan visual dan feedback pengguna terhadap antarmuka Manajemen Penugasan Pembimbing, terdapat 5 penyempurnaan utama yang harus dikerjakan:

1. **Pemindahan Tombol Kembali ke Kanan**: Tombol `[← Semua Mentee]` dipindahkan dari posisi kiri ke posisi **ujung kanan** header panel.
2. **Penyelarasan Identitas Mentee (Sesuai Foto)**:
   - Avatar bulat inisial (`[NP]`) berada di sisi kiri.
   - Di samping kanan avatar terdapat teks dua baris (vertikal):
     - **Baris 1 (Atas)**: Nama Mentee (**Naufal Pratama**) — font tebal 800, ukuran 1.125rem.
     - **Baris 2 (Bawah)**: Nama Institusi (*SMK Negeri 2 Depok Sleman*) — font reguler/medium 500, warna abu-abu/muted 0.8125rem.
3. **Pembersihan Teks/Badge Kategori Tugas**:
   - Menghapus badge kategori (seperti `Laporan Akhir`, `Orientasi Teknis`, dll.) dari baris tugas kartu mentee agar tampilan ringkas dan tidak padat.
4. **Perapian Tata Letak Baris Kartu Tugas (Flexbox Alignment)**:
   - Merapikan penataan tanggal tenggat (`Tenggat: 24 Jan 2027`), pill status (`Ditugaskan`, `Selesai`, `Reviu`), dan tombol `Detail >` agar tersusun lurus vertikal (aligned) dan tidak berantakan pada berbagai ukuran layar.
5. **Penghapusan Kolom Kompleksitas di Mode Tabel**:
   - Menghapus kolom `Kompleksitas` (`<th>` dan `<td>`) dari mode tampilan tabel (`#classroomTableContainer`).
6. **Modernisasi Halaman Edit Tugas Selaras Tampilan Buat Tugas**:
   - Mengubah halaman `pembimbing/tugas/edit.blade.php` yang sebelumnya menggunakan layout lama 2-kolom Gentelella menjadi antarmuka **Google Classroom Workspace** modern (selaras 1:1 dengan `pembimbing/tugas/create.blade.php`).
   - Menghilangkan seluruh tag `<style>` dan `<script>` inline di file Blade sesuai aturan wajib `AGENTS.md`.

---

## 2. Acuan Visual (Visual Reference Mapping)

Berdasarkan 5 tangkapan layar yang diunggah pengguna:

| Gambar | Komponen / Lokasi | Instruksi Desain |
| :--- | :--- | :--- |
| **Foto 1** (`media_1793767955976_303f0ed3.png`) | Header Panel Terpadu | Tombol `[← Semua Mentee]` dipindahkan ke **sisi kanan** panel, bukan di sisi kiri. |
| **Foto 2** (`media_1793768027014_ca7fd461.png`) | Identitas Mentee di Header | Avatar `[NP]` di kiri, di samping kanannya: Baris 1 `Naufal Pratama` (bold), Baris 2 `SMK Negeri 2 Depok Sleman` di bawah nama. |
| **Foto 3** (`media_1793768200623_167465d0.png`) | Kolom Kanan Baris Tugas | Tenggat waktu dan badge status (`Ditugaskan`, `Selesai`, `Reviu`) dirapikan dengan flexbox agar sejajar sempurna antar baris kartu. |
| **Foto 4** (`media_1793768255035_997321cf.png`) | Kolom Tabel Mode | Kolom `KOMPLEKSITAS` di tabel dihapus seluruhnya. |
| **Foto 5** (`media_1793768316102_bdbdd728.png`) | Formulir Edit Tugas | Formulir edit tugas lama di-redesign total menyerupai layout bersih `create.blade.php`. |

---

## 3. Spesifikasi Teknis & Rincian Pekerjaan

### Bagian A: Header Terpadu & Navigasi Kanan (`index.blade.php` & `tugas-index.css`)

1. **Markup HTML di `resources/views/pembimbing/tugas/index.blade.php`**:
   - Ubah urutan anak elemen di `#menteeUnifiedHeader`:
     ```html
     <div id="menteeUnifiedHeader" class="mentee-unified-header {{ $isFocusActive ? '' : 'd-none' }}">
         {{-- Kiri: Avatar + Nama (Atas) & Institusi (Bawah) --}}
         <div class="header-mentee-col">
             <div id="focusedMenteeAvatar" class="header-mentee-avatar" aria-hidden="true">
                 {{ $focusedInitials }}
             </div>
             <div class="header-mentee-text">
                 <span id="focusedMenteeName" class="header-mentee-name">{{ $focusedNama }}</span>
                 <span id="focusedMenteeInst" class="header-mentee-inst">{{ $focusedInst }}</span>
             </div>
         </div>

         {{-- Kanan: Tombol Kembali ke Semua Mentee --}}
         <a href="{{ route('pembimbing.tugas.index', request()->except('penempatan_id')) }}" 
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
     ```
2. **Styling CSS di `resources/css/pages/pembimbing/tugas-index.css`**:
   - Atur `.mentee-unified-header` agar membentang penuh dengan `justify-content: space-between`:
     ```css
     .mentee-unified-header {
         display: flex;
         align-items: center;
         justify-content: space-between;
         width: 100%;
         gap: 16px;
     }
     ```
   - Atur `.header-mentee-text` menjadi flex-column:
     ```css
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
   - Hapus separator vertikal `.header-v-divider` karena tombol kini terpisah di sisi kanan yang berlawanan.

---

### Bagian B: Pembersihan Baris Tugas & Flex Alignment (`index.blade.php` & `tugas-index.css`)

1. **Hapus Badge Kategori**:
   - Di `resources/views/pembimbing/tugas/index.blade.php` (sekitar baris 254-256):
     ```blade
     {{-- Hapus bagian ini --}}
     @if($tugas->kategori)
         <span class="task-row-category">{{ $tugas->kategori }}</span>
     @endif
     ```
2. **Struktur & Flex Alignment Baris Tugas**:
   - Pastikan `.classroom-task-row` dan `.task-row-right` memiliki styling lengkap di `resources/css/pages/pembimbing/tugas-index.css`:
     ```css
     .classroom-tasks-list {
         display: flex;
         flex-direction: column;
         gap: 10px;
         padding: 12px 0;
     }

     .classroom-task-row {
         display: flex;
         align-items: center;
         justify-content: space-between;
         gap: 16px;
         padding: 14px 18px;
         background: #FFFFFF;
         border: 1px solid #E2E8F0;
         border-radius: 12px;
         transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
     }

     .classroom-task-row:hover {
         border-color: #CBD5E1;
         box-shadow: 0 3px 12px rgba(15, 23, 42, 0.05);
         transform: translateY(-1px);
     }

     .task-row-left {
         display: flex;
         align-items: center;
         gap: 14px;
         flex: 1 1 auto;
         min-width: 0;
     }

     .task-row-icon {
         width: 38px;
         height: 38px;
         border-radius: 50%;
         display: flex;
         align-items: center;
         justify-content: center;
         flex-shrink: 0;
         background: #EFF6FF;
         color: #01347C;
     }

     .task-row-icon.icon-selesai {
         background: #ECFDF5;
         color: #059669;
     }

     .task-row-icon.icon-pending {
         background: #FFFBEB;
         color: #D97706;
     }

     .task-row-title-wrap {
         flex: 1 1 auto;
         min-width: 0;
     }

     .task-row-title {
         font-size: 0.9375rem;
         font-weight: 600;
         color: #0F172A;
         text-decoration: none;
         display: block;
         white-space: nowrap;
         overflow: hidden;
         text-overflow: ellipsis;
     }

     .task-row-title:hover {
         color: #01347C;
     }

     /* Sisi Kanan: Tenggat, Badge Status, Tombol Aksi */
     .task-row-right {
         display: flex;
         align-items: center;
         gap: 16px;
         flex-shrink: 0;
     }

     .task-row-due {
         font-size: 0.8125rem;
         font-weight: 500;
         color: #64748B;
         white-space: nowrap;
         min-width: 145px;
         text-align: right;
     }

     .task-row-due.overdue {
         color: #DC2626;
         font-weight: 600;
     }

     .task-status-pill {
         min-width: 90px;
         text-align: center;
         display: inline-flex;
         justify-content: center;
     }

     .btn-tugas-action {
         display: inline-flex;
         align-items: center;
         gap: 6px;
         padding: 6px 14px;
         border-radius: 8px;
         font-size: 0.8125rem;
         font-weight: 700;
         color: #01347C;
         border: 1.5px solid #01347C;
         background: transparent;
         text-decoration: none;
         white-space: nowrap;
         transition: all 0.2s ease;
     }

     .btn-tugas-action:hover {
         background: #01347C;
         color: #FFFFFF;
     }
     ```

---

### Bagian C: Penghapusan Kolom Kompleksitas di Mode Tabel (`index.blade.php`)

1. **Header Tabel (`<thead>`)**:
   Hapus `<th>Kompleksitas</th>` sehingga tersisa 5 kolom:
   - Tugas & Kategori
   - Peserta Magang
   - Tenggat Waktu
   - Status
   - Aksi
2. **Body Tabel (`<tbody>`)**:
   Hapus sel data kompleksitas:
   ```blade
   {{-- Hapus <td> ini --}}
   <td>
       <span class="assignment-tag">
           {{ ucfirst($tugas->kompleksitas?->value ?? 'Sedang') }}
       </span>
   </td>
   ```

---

### Bagian D: Modernisasi Halaman Edit Tugas (`edit.blade.php`, `tugas-edit.css`, `tugas-edit.js`)

1. **Struktur Halaman `resources/views/pembimbing/tugas/edit.blade.php`**:
   - Terapkan pola Google Classroom Workspace yang sudah terbukti di `create.blade.php`:
     - **Topbar**:
       - Icon Clipboard + Judul Halaman `"Edit Tugas Mentee"`.
       - Breadcrumb tersembunyi/semantik.
       - Tombol Utama: `"Simpan Perubahan"` (tipe submit mengarah ke form).
       - Tombol Batal: Mengarah ke halaman detail tugas (`pembimbing.tugas.show`).
     - **Formulir**:
       - Menggunakan `@method('PUT')` dan `action="{{ route('pembimbing.tugas.update', $tugas) }}"`.
       - Hidden input `kompleksitas`: `<input type="hidden" name="kompleksitas" value="{{ old('kompleksitas', $tugas->kompleksitas?->value ?? 'sedang') }}">` (menjaga validitas request tanpa membebani UI).
       - Hidden input `penempatan_id`: ID penempatan mentee terkunci.
     - **Kolom Kiri (Canvas Penugasan Utama)**:
       - Card 1: Input Judul Tugas (clean input dengan floating label Material) + Visual Editor Petunjuk (contenteditable dengan formatting toolbar Bold, Italic, Underline, List, Clear).
       - Card 2: Panel Lampiran (Upload Berkas baru & Tambah/Edit Tautan Referensi eksternal) dengan preview berkas yang saat ini terpasang (`$tugas->nama_file_lampiran`) dan opsi ganti.
     - **Kolom Kanan (Parameter Penugasan)**:
       - Penerima Tugas (Terkunci / Readonly): Menampilkan avatar inisial, nama mentee, dan bidang bimbingan.
       - Kategori Bidang: Menggunakan custom dropdown listbox Google Classroom (7 opsi kategori standar).
       - Tenggat Waktu (Deadline): Trigger modal kalender / date picker presisi.
2. **Asset CSS & JS**:
   - Buat `resources/css/pages/pembimbing/tugas-edit.css` yang mengimpor/mengadaptasi `tugas-create.css`.
   - Buat `resources/js/pages/pembimbing/tugas-edit.js` untuk inisialisasi floating label, sinkronisasi teks visual editor ke textarea hidden, dialog deadline, dan preview lampiran.
   - Daftarkan entry point ke `vite.config.js`:
     - `'resources/css/pages/pembimbing/tugas-edit.css'`
     - `'resources/js/pages/pembimbing/tugas-edit.js'`
   - **PENTING**: Hapus seluruh tag `<style>` dan `<script>` yang sebelumnya ada di `edit.blade.php`.

---

## 4. Checklist Tugas Langkah demi Langkah (Junior Dev Task)

```markdown
- [ ] Task 1: Update Header Terpadu di `resources/views/pembimbing/tugas/index.blade.php`
  - [ ] Pindahkan `#btnBackToMentees` ke sisi kanan di dalam `#menteeUnifiedHeader`.
  - [ ] Ubah struktur `.header-mentee-text` menjadi 2 baris (Nama di atas, Institusi di bawah).
  - [ ] Hapus separator `.header-v-divider`.

- [ ] Task 2: Update CSS Header & Task Row di `resources/css/pages/pembimbing/tugas-index.css`
  - [ ] Tambahkan flex layout `justify-content: space-between` pada `.mentee-unified-header`.
  - [ ] Tambahkan flex column pada `.header-mentee-text`.
  - [ ] Tambahkan styling lengkap untuk `.classroom-tasks-list`, `.classroom-task-row`, `.task-row-left`, `.task-row-right`, `.task-row-due`, `.task-status-pill`, `.btn-tugas-action`.
  - [ ] Pastikan dark mode styles untuk seluruh elemen di atas terdefinisi rapi.

- [ ] Task 3: Bersihkan Elemen di `resources/views/pembimbing/tugas/index.blade.php`
  - [ ] Hapus `<span class="task-row-category">{{ $tugas->kategori }}</span>` pada baris kartu tugas.
  - [ ] Hapus `<th>Kompleksitas</th>` pada header tabel `#classroomTableContainer`.
  - [ ] Hapus `<td>` kompleksitas pada body tabel `#classroomTableContainer`.

- [ ] Task 4: Re-architect `resources/views/pembimbing/tugas/edit.blade.php`
  - [ ] Adopsi layout Google Classroom Workspace dari `create.blade.php`.
  - [ ] Hapus semua tag `<style>` dan `<script>` inline.
  - [ ] Pasang `@push('styles')` dan `@push('scripts')` Vite untuk `tugas-edit.css` dan `tugas-edit.js`.
  - [ ] Tambahkan hidden input `kompleksitas` agar backend request tetap valid.
  - [ ] Hubungkan tombol "Simpan Perubahan" di topbar dengan form update.

- [ ] Task 5: Buat Asset Vite `tugas-edit.css` & `tugas-edit.js`
  - [ ] Buat `resources/css/pages/pembimbing/tugas-edit.css`.
  - [ ] Buat `resources/js/pages/pembimbing/tugas-edit.js`.
  - [ ] Daftarkan kedua file di `vite.config.js`.

- [ ] Task 6: Verifikasi Quality Gate
  - [ ] Jalankan `php artisan test --filter=Tugas` (Pastikan seluruh 55+ test hijau).
  - [ ] Jalankan `.\vendor\bin\pint --test`.
  - [ ] Jalankan `.\vendor\bin\phpstan analyse app/Http/Controllers/Pembimbing/TugasController.php --memory-limit=1G`.
  - [ ] Jalankan `npm run build` (Pastikan bundle berhasil dikompilasi).
  - [ ] Jalankan `python -m graphify update .`.
```

---

## 5. Rencana Pengujian Otomatis (Testing Plan)

1. **Test Header Posisi Kanan & Identitas Dua Baris**:
   - Memastikan saat mode fokus aktif, response memuat `#btnBackToMentees` dan `#focusedMenteeName` serta `#focusedMenteeInst`.
2. **Test Penghapusan Kategori & Kompleksitas**:
   - Memastikan tidak ada lagi chip tag kategori pada baris tugas.
   - Memastikan header tabel tidak lagi memuat kata `'Kompleksitas'`.
3. **Test Halaman Edit Tugas Modern**:
   - Memastikan route `pembimbing.tugas.edit` merender elemen workspace modern (`tugas-workspace`, `tugas-topbar`, tombol `Simpan Perubahan`).
   - Memastikan form update tugas mengirim data dengan sukses dan tersimpan di database.
   - Memastikan validasi anti-IDOR tetap aktif jika pembimbing mencoba mengedit tugas milik mentee pembimbing lain.

