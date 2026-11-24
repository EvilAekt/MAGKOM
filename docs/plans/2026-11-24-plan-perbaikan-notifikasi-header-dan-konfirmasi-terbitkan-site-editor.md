# Rencana Kerja: Perbaikan Notifikasi Header & Konfirmasi Publikasi Canvas Site Editor

**Tanggal Dokumen**: 24 November 2026  
**Status**: Menunggu Persetujuan Pengguna  
**Arsitektur Target**: Laravel 10, PHP 8.3, Vanilla ES Modules, Vite, Tailwind/Vanilla CSS  
**Kepatuhan Aturan**: [AGENTS.md](file:///d:/laragon/www/MAGKOM/.agents/rules/AGENTS.md) (Aturan 3.3 terkait Permintaan Izin Perubahan Blade, Aturan 4 CSS Scoped, Aturan 5 JS Modular, Aturan 11 Quality Gate PHP 8.3)

---

## 1. Analisis Akar Masalah (Root Cause Analysis)

Berdasarkan keluhan pengguna dan penelusuran commit repositori:
1. **Hilangnya Kapsul Status di Header Topbar**:
   Pada commit sebelumnya (`05737fdc8665e46879b93a2c6fdba8f72fcda66a`), elemen `#wp-save-status` (`<span id="wp-save-status" class="wp-save-status-pill">`) dihapus dari file Blade `_topbar.blade.php`. Akibatnya, area header di sebelah kiri tombol aksi penerbitan (`[ Simpan Draf ]`, `[ Terbitkan ]`, `[ Rollback ]`) menjadi kosong tanpa ada teks/indikator lampu status yang memberi tahu SuperAdmin apakah perubahan di pengaturan umum (*general*) atau kanvas sudah tersimpan atau belum.
2. **Lifecycle Inisialisasi JavaScript yang Tidak Konsisten**:
   Pemuatan ES Module Vite (`<script type="module">`) bersifat *deferred*. Penggunaan `document.addEventListener('DOMContentLoaded', ...)` dapat melewatkan event jika dokumen telah berstatus `interactive` atau `complete`, sehingga listener tombol tidak terpasang dan pop-up konfirmasi peramban (`confirm`) tidak muncul saat diklik.
3. **Ketiadaan Konfirmasi Pop-up dan Keterangan Status yang Sinkron**:
   SuperAdmin memerlukan alur kerja yang jelas dan profesional seperti standar CMS modern:
   - Ada keterangan indikator di header yang langsung bereaksi saat ada perubahan.
   - Ada keterangan di header saat draf berhasil disimpan.
   - Ada pop-up konfirmasi dialog *"Apakah Anda yakin ingin mempublikasikan..."* saat tombol **Terbitkan** ditekan, serta dialog peringatan saat **Rollback** ditekan.

---

## 2. Permohonan Persetujuan Perubahan Markup Blade (Sesuai AGENTS.md Aturan 3.3)

Sesuai aturan ketat proyek **AGENTS.md Pasal 3.3 (Pengecualian perubahan Blade)**:
> *"Perubahan markup Blade hanya boleh dilakukan apabila perubahan struktur HTML memang mutlak diperlukan, misalnya menambahkan elemen semantik, form, field, slot, atau container yang belum tersedia. Dalam kondisi tersebut: jelaskan mengapa perubahan tidak dapat diselesaikan tanpa markup baru, sebutkan file Blade dan bagian yang perlu diubah, serta minta persetujuan eksplisit pengguna sebelum mengubah Blade."*

### Penjelasan Kebutuhan Markup Baru:
- **File Blade**: [`resources/views/superadmin/site-editor/partials/_topbar.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/superadmin/site-editor/partials/_topbar.blade.php#L65-L85)
- **Bagian yang Diubah**: Di dalam kontainer `.wp-header-right`, tepat sebelum `<div class="wp-publish-group">`.
- **Elemen yang Ditambahkan**:
  ```html
  {{-- Status Penyimpanan Draf Kompak di Header Topbar --}}
  <div class="wp-status-container">
      <span id="wp-save-status" 
            class="wp-save-status-pill {{ $draftMeta['active'] ? 'status-dirty' : 'status-clean' }}" 
            title="{{ $draftMeta['active'] ? 'Ada perubahan draf sandbox yang belum dipublikasikan' : 'Seluruh data tersinkronisasi dengan versi live' }}">
          {{ $draftMeta['active'] ? 'Belum Disimpan' : 'Tersinkronisasi' }}
      </span>
  </div>
  ```
- **Alasan Mutlak**:  
  Elemen ini harus dirender langsung dari server saat halaman pertama kali dimuat (*server-rendered*) agar tidak mengalami *layout shift* (kedipan) atau ketergantungan rapuh pada DOM insertion JavaScript sisi klien, sehingga status draf langsung tampil sejak frame pertama halaman dibuka.

---

## 3. Rencana Solusi Teknis & Arsitektur

### A. Indikator Status di Header Topbar (`editor-state.js` & `_topbar.blade.php`)
Kapsul status `#wp-save-status` di header akan memiliki mesin status (*state machine*) visual yang konsisten:
1. **Bersih / Sinkron**: 
   - Kelas: `.status-clean`
   - Titik Lampu: Hijau tenang
   - Teks: `Tersimpan (jam:menit)` atau `Tersinkronisasi`
2. **Perubahan Terdeteksi (User Mengubah Input/General)**:
   - Kelas: `.status-dirty`
   - Titik Lampu: Kuning/Amber berdenyut (*pulsing dot animation*)
   - Teks: `Belum Disimpan`
3. **Sedang Menyimpan Draf (Autosave atau Manual)**:
   - Kelas: `.status-saving`
   - Titik Lampu: Biru berputar/berdenyut
   - Teks: `Menyimpan...`
4. **Berhasil Disimpan**:
   - Kelas: `.status-saved`
   - Titik Lampu: Hijau solid
   - Teks: `Tersimpan (14:25)`
   - Disertai toast di pojok kanan: `✓ Draf perubahan berhasil disimpan.`
5. **Gagal / Konflik**:
   - Kelas: `.status-error`
   - Titik Lampu: Merah
   - Teks: `Gagal Menyimpan`

### B. Alur Konfirmasi Pop-up Tombol Terbitkan (`editor-app.js`)
1. Saat SuperAdmin menekan tombol **`[ -> Terbitkan ]`**:
   - Sistem memunculkan dialog pop-up konfirmasi:
     > *"Apakah Anda yakin ingin MEMPUBLIKASIKAN seluruh perubahan ini ke versi live publik? Sistem akan otomatis mencadangkan versi sebelumnya untuk opsi Rollback."*
   - Jika dibatalkan (*Cancel*): Proses berhenti tanpa dampak apa pun.
   - Jika disetujui (*OK*):
     - Kapsul status header berubah menjadi: `Menyimpan & Menerbitkan...`
     - Menjalankan penyimpanan draf final di memori via `editorState.saveDraft(false, true)`.
     - Mengirim request publikasi ke `/superadmin/site-editor/publish`.
     - Saat respons server sukses:
       - Kapsul status header berubah menjadi: `Terpublikasi!`
       - Toast notifikasi muncul: `✓ Berhasil mempublikasikan seluruh perubahan ke versi live publik!`
       - Jeda 1.5 detik (1500ms) agar pengguna sempat membaca konfirmasi, lalu halaman me-reload versi live baru.

### C. Alur Konfirmasi Tombol Rollback (`editor-app.js`)
1. Saat SuperAdmin menekan tombol **`[ ⟲ Rollback ]`**:
   - Sistem memunculkan dialog pop-up konfirmasi:
     > *"PERINGATAN: Apakah Anda yakin ingin mengembalikan tampilan Landing Page publik ke versi sebelum publikasi terakhir? Aksi ini akan langsung memulihkan backup terakhir di sisi pengunjung publik."*
   - Jika disetujui, kapsul status header berubah menjadi `Memulihkan...`, mengirim request rollback, menampilkan toast sukses, lalu me-reload halaman.

### D. Alur Tombol Simpan Draf Manual (`editor-app.js`)
1. Saat tombol **`[ Simpan Draf ]`** ditekan:
   - Kapsul status header langsung berubah menjadi `Menyimpan...`
   - Toast notifikasi muncul: `Menyimpan draf perubahan...`
   - Menyimpan draf dan memperbarui waktu tersimpan pada kapsul header.

---

## 4. Daftar File yang Akan Diubah

| File | Aksi | Alasan & Ringkasan Perubahan |
| :--- | :--- | :--- |
| [`resources/views/superadmin/site-editor/partials/_topbar.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/superadmin/site-editor/partials/_topbar.blade.php) | Modify | Menambahkan markup `#wp-save-status` di samping tombol penerbitan header. *(Memerlukan izin user sesuai AGENTS.md 3.3)* |
| [`resources/js/pages/superadmin/site-editor/editor-app.js`](file:///d:/laragon/www/MAGKOM/resources/js/pages/superadmin/site-editor/editor-app.js) | Modify | Memastikan listener `readyState` tangguh, menyinkronkan event klik ketiga tombol dengan konfirmasi pop-up dan update status header. |
| [`resources/js/pages/superadmin/site-editor/editor-state.js`](file:///d:/laragon/www/MAGKOM/resources/js/pages/superadmin/site-editor/editor-state.js) | Modify | Menyelaraskan teks status kapsul topbar (`Belum Disimpan`, `Menyimpan...`, `Tersimpan (jam:menit)`) dan pemicu toast. |
| [`resources/css/pages/superadmin/site-editor.css`](file:///d:/laragon/www/MAGKOM/resources/css/pages/superadmin/site-editor.css) | Modify | Memastikan tata letak `.wp-status-container` dan `.wp-save-status-pill` sejajar rapi dan kontras tinggi di topbar. |
| [`tests/Feature/SuperAdmin/LandingCmsSiteEditorTest.php`](file:///d:/laragon/www/MAGKOM/tests/Feature/SuperAdmin/LandingCmsSiteEditorTest.php) | Modify | Menambahkan assertion otomatis bahwa `#wp-save-status` terpasang di header topbar dan berstatus benar. |

---

## 5. Rencana Langkah Implementasi

- [ ] **Langkah 1**: Minta persetujuan pengguna untuk perubahan markup di `_topbar.blade.php` sesuai aturan `AGENTS.md`.
- [ ] **Langkah 2**: Pasang markup `#wp-save-status` di `resources/views/superadmin/site-editor/partials/_topbar.blade.php`.
- [ ] **Langkah 3**: Perbarui logika mesin status dan formatting teks pada `resources/js/pages/superadmin/site-editor/editor-state.js`.
- [ ] **Langkah 4**: Perbarui handler klik tombol Simpan Draf, Terbitkan, dan Rollback di `resources/js/pages/superadmin/site-editor/editor-app.js` agar pop-up konfirmasi dan keterangan header selalu tersinkronisasi.
- [ ] **Langkah 5**: Periksa dan sesuaikan CSS di `resources/css/pages/superadmin/site-editor.css`.
- [ ] **Langkah 6**: Kompilasi aset frontend dengan `npm run build`.
- [ ] **Langkah 7**: Jalankan formatting Pint `./vendor/bin/pint --test` dan pengujian PHPUnit dengan PHP 8.3.
- [ ] **Langkah 8**: Verifikasi menyeluruh dan laporkan hasil kepada pengguna.

---

## 6. Kriteria Selesai (Definition of Done)

1. Indikator keterangan draf di header (`#wp-save-status`) selalu tampil dan langsung berubah statusnya saat SuperAdmin mengedit pengaturan atau kanvas.
2. Ketika draf otomatis tersimpan (setelah jeda 1.5 detik) atau disimpan manual, header menampilkan `"Tersimpan (jam:menit)"` dan toast notifikasi muncul.
3. Saat tombol **Terbitkan** diklik, dialog konfirmasi *"Apakah Anda yakin ingin mempublikasikan..."* pasti muncul. Jika dikonfirmasi, proses penerbitan berjalan dengan status header yang jelas hingga berhasil.
4. Saat tombol **Rollback** diklik, dialog peringatan konfirmasi pasti muncul.
5. Seluruh pengujian fitur PHP 8.3 lulus 100% dan kode terformat rapi sesuai PSR-12/Pint.

