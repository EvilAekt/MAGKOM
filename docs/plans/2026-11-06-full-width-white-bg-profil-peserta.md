# Rencana Kerja: Penyesuaian Layout Full-Width & Background Putih Bersih Halaman Profil Peserta

> **Dokumen Rencana**: `docs/plans/2026-11-06-full-width-white-bg-profil-peserta.md`  
> **Tanggal**: 06 November 2026  
> **Status**: Menunggu Persetujuan Pengguna (Draft)

---

## 1. Tujuan
Menyesuaikan tata letak halaman **Biodata & Profil Peserta** (`/peserta/profil`) sesuai instruksi pengguna:
1. **Menghilangkan area abu-abu/kebiruan di belakang**: Mengubah latar belakang luar menjadi putih bersih penuh (*full white*) yang menyatu secara harmonis dengan area dashboard utama.
2. **Layout Rata Kanan-Kiri (Fluid Full-Width)**: Mengubah batas kaku `max-width: 820px` pada container profil agar melebar proporsional memenuhi area kerja konten (`width: 100%`), sehingga di berbagai ukuran layar perangkat (laptop, monitor widescreen, tablet) tampilannya tidak menyisakan ruang kosong yang canggung di sisi kanan dan kiri.

---

## 2. Asumsi & Evaluasi Teknis

### Asumsi:
1. **Harmonisasi dengan `.content-area` Dashboard**:
   - Area konten utama dashboard peserta dibungkus oleh `<main class="content-area">` dengan padding bawaan `32px` (desktop) dan `16px` (mobile).
   - Container profil akan memanfaatkan 100% lebar `.content-area` ini sehingga sejajar (*flush*) dengan header, footer, dan elemen halaman lain pada dashboard.
2. **Proporsi Grid Input & Checklist Saat Layar Lebar**:
   - Jika card melebar memenuhi layar desktop, tata letak form 2-kolom dan banner identitas navy harus tetap terlihat seimbang dan nyaman dibaca (tidak renggang berlebihan).
   - Checklist 5 indikator kelengkapan akan menggunakan grid fleksibel responsif (`repeat(auto-fit, minmax(220px, 1fr))`), sehingga di layar lebar menyebar rapi 3-5 kolom, dan di layar sempit otomatis menyesuaikan tanpa saling bertumpuk.
3. **Pemberian Border/Shadow Halus**:
   - Untuk mempertahankan batas struktural yang rapi pada latar putih, kartu profil utama diberi border tipis (`1px solid #E2E8F0`) dan bayangan lembut (`box-shadow: 0 1px 3px rgba(15, 23, 42, 0.05)`), menjaga nuansa modern & enterprise clean.

---

## 3. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/css/pages/peserta/profil-show.css` | Modify | 1. Hapus background `#EEF3FA` pada `.profil-page-wrapper` (diubah menjadi transparan/putih).<br>2. Ubah `max-width: 820px` pada `.profil-main-card` menjadi `max-width: 100%; width: 100%`.<br>3. Optimalkan padding dan margin container agar rata kanan-kiri memenuhi `.content-area`.<br>4. Atur grid checklist kelengkapan agar adaptif di layar lebar (`auto-fit`). |
| `resources/views/peserta/profil/show.blade.php` | Modify | Menjaga markup tetap bersih dan memastikan container terhubung langsung tanpa wrapper ganda yang tidak diperlukan. |

---

## 4. Langkah Implementasi Terstruktur

### Fase 1: Modifikasi Stylesheet (`profil-show.css`)
- [ ] Ubah `.profil-page-wrapper`:
  - `background-color`: ubah dari `#EEF3FA` menjadi `transparent` (atau `#ffffff`).
  - `padding`: sesuaikan dari `32px 16px 48px` menjadi `0` atau `4px 0 24px` agar menyatu langsung dengan `.content-area`.
- [ ] Ubah `.profil-main-card`:
  - `max-width`: ubah dari `820px` menjadi `100%`.
  - `width`: `100%`.
  - `box-shadow` & `border`: sesuaikan agar tetap terdefinisi elegan di atas kanvas putih.
  - `padding`: sesuaikan menjadi `28px 32px` (desktop) dan `18px 16px` (mobile).
- [ ] Optimalkan Grid & Spacing Internal:
  - Banner identitas navy: tetap `width: 100%` dengan border-radius rapi.
  - Card kelengkapan biodata: `width: 100%`, checklist menggunakan grid adaptif responsif.
  - Card seksi form: `width: 100%`, input fields terdistribusi proporsional rata kanan-kiri.
  - Bottom action bar: rata kiri untuk checkbox persetujuan dan rata kanan untuk tombol simpan.

### Fase 2: Kompilasi Asset & Build
- [ ] Jalankan kompilasi `npm run build` menggunakan Node v22.
- [ ] Bersihkan cache template view (`php artisan view:clear`).

### Fase 3: Verifikasi Visual & Fungsional
- [ ] Uji responsivitas pada resolusi:
  - Desktop / Widescreen (1920px & 1440px): Tampilan rata kanan-kiri penuh, tidak ada ruang abu-abu di pinggir.
  - Laptop Standard (1366px & 1280px): Komposisi proporsional dan presisi.
  - Tablet (768px - 1024px): Menyesuaikan secara dinamis.
  - Mobile (375px - 480px): Stack 1 kolom rapi tanpa horizontal scrollbar.
- [ ] Jalankan automated test regression (`php artisan test tests/Feature/PesertaPortalWorkflowTest.php`).

---

## 5. Kriteria Selesai (Definition of Done)
- [ ] Area abu-abu/biru muda di luar card sepenuhnya hilang; latar belakang tampil putih bersih.
- [ ] Container biodata rata kanan-kiri memenuhi area dashboard secara penuh dan proporsional di berbagai device.
- [ ] Tidak ada regresi fungsional (upload foto, preview, kalkulasi kelengkapan, tombol edit, dan simpan tetap berjalan normal).
- [ ] Seluruh test otomatis tetap berstatus hijau (PASS).

