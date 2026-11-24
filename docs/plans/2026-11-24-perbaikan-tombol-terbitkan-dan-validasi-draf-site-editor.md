# Rencana Kerja: Perbaikan Tombol Terbitkan & Validasi Penyimpanan Draf Site Editor

- **Dokumen**: `docs/plans/2026-11-24-perbaikan-tombol-terbitkan-dan-validasi-draf-site-editor.md`
- **Tanggal**: 24 November 2026
- **Status**: Menunggu Konfirmasi Pengguna
- **Target Sub-Sistem**: Form Request Validation (`SaveLandingSiteEditorRequest`), `EditorState.saveDraft()`, `editor-app.js` Publish Handler, & Staging Service

---

## 1. Latar Belakang & Masalah (Problem Statement)
Pengguna melaporkan bahwa setelah berhasil menambahkan Slide #2 dan memilih foto dari Media Library, ketika menekan tombol **`-> Terbitkan`**:
- Proses penerbitan **gagal / tidak bisa**.
- Perubahan yang telah dilakukan di editor tidak terbit ke live landing page atau hilang saat halaman termuat ulang.

---

## 2. Investigasi & Akar Masalah (Root Cause Analysis)

Melalui penelusuran mendalam terhadap payload dan eksekusi backend Laravel, ditemukan **2 Akar Masalah Utama**:

### Akar Masalah 1: Kegagalan Validasi Terselubung (Silent 422 Unprocessable Entity)
Saat tombol `Terbitkan` ditekan, sistem terlebih dahulu memanggil `saveDraft()` via AJAX POST ke `/superadmin/site-editor/save-draft`. Permintaan ini **ditolak oleh Laravel dengan status HTTP 422** karena:
1. **`landing_hero_padding_y_px`**:
   - Di database dan formulir, nilainya adalah `0` (jarak vertikal padding hero 0 px untuk banner penuh).
   - Namun aturan validasi di `SaveLandingSiteEditorRequest.php` mengharuskan `between:20,200`. Angka `0` langsung ditolak!
2. **`landing_theme_font_body`**:
   - Nilai font body di database adalah `Plus Jakarta Sans`.
   - Namun aturan validasi di `SaveLandingSiteEditorRequest.php` hanya mengizinkan `in:Source Sans 3,Inter,Roboto,Open Sans,Nunito Sans`. `Plus Jakarta Sans` tidak ada di dalam whitelist, sehingga langsung ditolak!

### Akar Masalah 2: Cacat Alur di JavaScript Frontend (`editor-state.js` & `editor-app.js`)
1. Ketika endpoint `save-draft` mengembalikan HTTP 422 (gagal):
   - Metode `saveDraft()` di `editor-state.js` **tidak melempar Error (throw)** dan **tidak mengembalikan status `false`**.
   - Metode tersebut hanya mencatat log dan selesai begitu saja (`return undefined`).
2. Di `editor-app.js`:
   - Blok `try { await editorState.saveDraft(false, true); } catch (e) { ... }` mengira penyimpanan berhasil karena tidak ada error yang di-throw!
   - JavaScript langsung mengeksekusi langkah berikutnya: `fetch('/superadmin/site-editor/publish', ...)`.
3. Di sisi Backend Publish:
   - Karena `save-draft` gagal di langkah 1, **tidak ada data draf baru yang tersimpan di tabel pengaturan (`draft_%`)**.
   - `publishDraft()` mendapati 0 draf, lalu mengembalikan: *"Berhasil mempublikasikan 0 elemen draf"*.
   - Frontend kemudian memanggil `setTimeout(() => window.location.reload(), 600)`.
   - **Halaman ter-reload dan seluruh perubahan di memori terhapus**, kembali ke kondisi sebelum diedit!

---

## 3. Pendekatan Teknis & Solusi (Senior Developer Solution)

### A. Perbaikan Validasi Backend (`SaveLandingSiteEditorRequest.php`)
1. Perluas batas toleransi `landing_hero_padding_y_px` menjadi `between:0,200` (mengizinkan 0 px saat padding dimatikan).
2. Tambahkan `Plus Jakarta Sans` dan `Lexend` ke dalam whitelist font body (`landing_theme_font_body`).
3. Tambahkan pesan validasi deskriptif dalam Bahasa Indonesia.

### B. Perbaikan Alur Penyimpanan Draf di Frontend (`editor-state.js`)
1. Ubah `saveDraft(isAutosave, force)` agar mengembalikan nilai Boolean yang tegas:
   - Mengembalikan `true` hanya jika HTTP response status 200 dan `data.success === true`.
   - Mengembalikan `false` jika HTTP response 4xx/5xx, conflict 409, atau gagal.
2. Jika server mengembalikan HTTP 422:
   - Ekstrak rincian field yang gagal dari `data.errors` (misal: "Jarak vertikal padding hero harus antara 0 px hingga 200 px").
   - Tampilkan pesan error spesifik tersebut di toast notifikasi agar SuperAdmin mengetahui secara persis field mana yang tidak valid.

### C. Penguncian Alur Penerbitan di Frontend (`editor-app.js`)
1. Pada handler klik tombol `btn-publish`:
   ```javascript
   const saveSuccess = await editorState.saveDraft(false, true);
   if (!saveSuccess) {
       showToast('Gagal menyimpan perubahan draf sebelum publikasi. Penerbitan dibatalkan.', false);
       publishBtn?.removeAttribute('disabled');
       if (spinner) spinner.style.display = 'none';
       return; // STOP! JANGAN PANGGIL /publish JIKA DRAF GAGAL DISIMPAN!
   }
   ```
2. Pada respons `/publish`:
   - Jika `$count === 0` dan tidak ada perubahan draf, tampilkan informasi peringatan bahwa tidak ada draf baru untuk diterbitkan.

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `app/Http/Requests/SuperAdmin/SaveLandingSiteEditorRequest.php` | Modify | Perbaiki aturan `landing_hero_padding_y_px` (`between:0,200`) dan tambahkan `Plus Jakarta Sans` ke whitelist `landing_theme_font_body`. |
| `resources/js/pages/superadmin/site-editor/editor-state.js` | Modify | `saveDraft()` mengembalikan boolean `true/false`, mengekstrak rincian error dari `data.errors`, dan tidak menelan kegagalan. |
| `resources/js/pages/superadmin/site-editor/editor-app.js` | Modify | Tombol `btn-publish` mengecek nilai kembalian `saveDraft()`. Batalkan publikasi jika penyimpanan draf gagal. |
| `tests/Feature/SuperAdmin/LandingCmsSiteEditorTest.php` | Modify | Tambahkan pengujian otomatis penyimpanan draf dengan padding 0 px dan font body `Plus Jakarta Sans` hingga tahap publikasi. |

---

## 5. Rencana Langkah Implementasi

- [x] **Langkah 1**: Perbarui aturan validasi di `app/Http/Requests/SuperAdmin/SaveLandingSiteEditorRequest.php`.
- [x] **Langkah 2**: Refactor `saveDraft()` di `resources/js/pages/superadmin/site-editor/editor-state.js` agar mengembalikan status boolean dan menampilkan detail pesan error 422.
- [x] **Langkah 3**: Perbarui handler klik `publishBtn` di `resources/js/pages/superadmin/site-editor/editor-app.js` untuk memvalidasi keberhasilan draf sebelum memanggil endpoint `/publish`.
- [x] **Langkah 4**: Tambahkan test case di `tests/Feature/SuperAdmin/LandingCmsSiteEditorTest.php`.
- [x] **Langkah 5**: Kompilasi aset dengan `npm run build`.
- [x] **Langkah 6**: Jalankan formatting Pint `./vendor/bin/pint --test` dan pengujian PHPUnit dengan PHP 8.3.

---

## 6. Kriteria Selesai (Definition of Done)

- [x] Validasi `save-draft` 100% lolos untuk nilai eksisting database (`padding 0 px` dan font `Plus Jakarta Sans`).
- [x] Tombol `Terbitkan` berhasil menyimpan draf terbaru dan mempublikasikannya ke live tanpa kegagalan.
- [x] Jika terjadi kesalahan validasi di masa depan, sistem tidak memaksakan publish kosong dan menampilkan rincian error ke pengguna.
- [x] Asset Vite terkompilasi bersih dan seluruh test suite lulus 100%.

