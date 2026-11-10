# Rencana Kerja: Redesain Pop-Up Peta Alur Interaktif & Penghapusan Tautan Halaman Penuh

Dokumen ini merinci rencana penghapusan tautan *"Buka Peta Lengkap"* pada modal peta alur serta transformasi menyeluruh modal tersebut menjadi pengalaman eksplorasi yang benar-benar **interaktif, edukatif, dan menarik** bagi calon peserta magang (Siswa SMK dan Mahasiswa).

---

## 1. Tujuan
1. **Pengalaman Tunggal Tanpa Mengalihkan Pengguna**: Menghilangkan tombol/link *"Buka Peta Lengkap"* (`btn-open-full-page`) di modal `_peta_alur_modal.blade.php` agar pengguna tetap berada di landing page dan menikmati seluruh informasi alur langsung dalam pop-up modal.
2. **Transformasi Konsep Interaktif Sejati**: Mengubah modal dari sekadar pembaca teks/tab statis menjadi **Mini-Simulator Alur Magang Interaktif** yang memiliki:
   - **Interactive Stepper & Visual Connector Path**: Jalur alur bernyawa dengan indikator progres dan tombol navigasi *Next/Prev Step*.
   - **Checklist Kesiapan Mandiri (Interactive Readiness Meter)**: Calon peserta dapat mencentang syarat-syarat yang sudah mereka miliki dan melihat persentase kesiapan pendaftaran mereka secara *real-time*.
   - **Kalkulator & Estimator Timeline Magang**: Pengguna dapat memilih target bulan magang (misal Juli 2026) dan modal otomatis menghitung tanggal penting (pendaftaran akun, batas berkas, pengumuman, hingga hari H).
   - **Pratinjau Syarat Dokumen (Interactive Document Inspector)**: Kartu dokumen dapat diklik/di-hover untuk melihat tips resmi, format berkas yang diterima, dan contoh standar Diskominfo Sleman.

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi:
- **Asumsi 1**: Rute `/panduan-alur` di backend tetap dipertahankan sebagai fallback atau SEO sitemap, namun seluruh pemicu navigasi di landing page (navbar, tombol tahapan, dan modal) diarahkan 100% membuka modal pop-up interaktif tanpa membuka tab baru.
- **Asumsi 2**: Interaktivitas modal (checklist kesiapan dan kalkulator jadwal) berjalan secara *client-side* murni (Vanilla JavaScript) dengan dukungan `localStorage` opsional, sehingga respons instan tanpa *latency network* dan tetap ringan.
- **Asumsi 3**: Desain visual konsisten dengan Design System MAGKOM (`DS-MagKom`: warna Biru Sleman `#0751FF`, latar belakang netral yang bersih, dan kompatibilitas penuh tema gelap `[data-theme="dark"]`).

### Pertanyaan Terbuka:
- *(Tidak ada pertanyaan pemblokir; konsep dirancang modular sehingga fitur kalkulator dan checklist kesiapan dapat langsung diimplementasikan).*

---

## 3. Pendekatan Teknis & Perbandingan Arsitektur

| Kriteria | Opsi A: In-Modal Client-Side Rich Simulator (Rekomendasi) | Opsi B: Micro Web Component / Iframe Canvas |
| :--- | :--- | :--- |
| **Deskripsi** | Mengembangkan interaktivitas langsung di dalam [public/partials/_peta_alur_modal.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/public/partials/_peta_alur_modal.blade.php) dan [public/css/landing-peta-alur-modal.css](file:///d:/MAGKOM/MAGKOM/public/css/landing-peta-alur-modal.css) menggunakan Vanilla JS murni. | Memuat mini canvas terisolasi atau iframe grafis DAG di dalam modal. |
| **Kinerja & Kecepatan** | **Sangat Cepat (<5ms)**, zero network overhead, rendering instan saat modal dibuka. | Lebih berat, memakan memori, risiko lag pada perangkat mobile. |
| **Aksesibilitas (WCAG)** | **Tinggi (AA)**; elemen menggunakan native button, checkbox, ARIA tablist, dan screen-reader friendly. | Rendah; sulit dinavigasi menggunakan keyboard dan screen reader. |
| **Pemeliharaan** | **Mudah & Terpusat**; konten alur, tips berkas, dan logika kalkulasi berada di satu partial template. | Rumit; memerlukan sinkronisasi state antara iframe/parent window. |
| **Rekomendasi** | **Dipilih Opsi A**. Sangat responsif, intuitif bagi pengguna awam, dan estetis sesuai standar portal instansi pemerintah. | Tidak dipilih. |

---

## 4. Konsep Rinci Fitur Interaktif Baru

### A. Penghapusan Header *"Buka Peta Lengkap"*
- Menghapus elemen `<a class="btn-open-full-page">Buka Peta Lengkap</a>` dari header modal.
- Menggantikannya dengan **Badge Status Interaktif** (misal: *"Mode Simulasi Alur Mandiri"*) atau tombol bantuan cepat.

### B. Interactive Stepper with Animated Path Connector
- Di sebelah kiri, daftar 4 tahapan dilengkapi dengan garis progres bercahaya (*animated pulse line*).
- Setiap langkah memiliki status visual:
  - `Sudah Ditelusuri` (Ikon centang biru)
  - `Sedang Aktif` (Glow ring beranimasi)
  - `Berikutnya` (Badge nomor)
- Di bagian bawah panel kanan ditambahkan kontrol aksi:
  - Tombol **"← Tahap Sebelumnya"** (dinonaktifkan di tahap 1)
  - Tombol **"Tahap Berikutnya →"** (berpindah ke tahap 2, 3, 4)
  - Di tahap 4: Tombol berganti menjadi **"Mulai Pendaftaran Sekarang 🚀"**

### C. Checklist Kesiapan Mandiri (Interactive Readiness Meter)
- Pada bagian *"Ketentuan & Langkah Wajib"*, setiap butir dijadikan checkbox interaktif:
  - Contoh: `[ ] NIK & Data Diri Siap`, `[ ] Pas Foto Formal Tersedia`, `[ ] Surat Pengantar Kampus/Sekolah`.
- Di bagian atas panel kanan muncul **Meteran Kesiapan**:
  - `Progres Kesiapan: 3 dari 6 syarat terpenuhi (50%)` disertai visual progress bar dinamis.
  - Saat mencapai 100%, muncul animasi konfeti mikro atau pesan apresiasi *"Berkas Anda sudah lengkap untuk tahap ini!"*.

### D. Live Timeline & Estimator Kalender Magang
- Di bagian filter (atas), ditambahkan pemilih target: **"Bulan Masuk Magang"** (mengambil data bulan magang yang tersedia, misal: *Juli 2026*).
- Saat bulan dipilih, panel alur menampilkan perkiraan tanggal realistis:
  - 🗓️ *Pendaftaran Akun*: 1–10 Juni 2026
  - 🗓️ *Verifikasi Berkas*: 11–20 Juni 2026
  - 🗓️ *Pengumuman Penempatan*: 25 Juni 2026
  - 🚀 *Mulai Bertugas*: 1 Juli 2026

### E. Interactive Document Inspector (Pop-up Card Syarat)
- Mengklik badge dokumen (seperti *Surat Pengantar*, *Proposal*, *CV*) akan membuka kartu popover interaktif yang menampilkan:
  - Persyaratan teknis (format PDF, maks 2MB, harus ber-kop surat).
  - Kesalahan umum yang sering ditolak admin.
  - Tombol aksi: *"Unduh Buku Panduan Resmi (PDF)"*.

---

## 5. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/public/partials/_peta_alur_modal.blade.php` | Modify | Hapus tombol "Buka Peta Lengkap", implementasikan markup Stepper dinamis, Checklist Kesiapan interaktif, Timeline Estimator, Document Inspector drawer, dan navigasi Next/Prev. |
| `public/css/landing-peta-alur-modal.css` | Modify | Tambahkan styling untuk Readiness Progress Bar, interactive checkboxes, step navigation buttons (prev/next), document tooltip/card popover, dan animasi transisi. |
| `tests/Feature/LandingPagePetaAlurModalTest.php` | Modify | Perbarui test suite agar memverifikasi ketiadaan tombol "Buka Peta Lengkap" dan memvalidasi komponen interaktif baru (Readiness meter, tombol navigasi alur, filter interaktif). |

---

## 6. Langkah Implementasi Terstruktur

### Tahap 1: Pembersihan Header Modal & Navigasi
- [x] Buka [resources/views/public/partials/_peta_alur_modal.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/public/partials/_peta_alur_modal.blade.php) dan hapus elemen tautan `.btn-open-full-page` ("Buka Peta Lengkap").
- [x] Perbarui header modal agar lebih ringkas dan fokus pada judul serta tombol tutup (`closePetaAlurModal`).

### Tahap 2: Implementasi State & Logika Interaktif (JavaScript)
- [x] Tambahkan state checklist kesiapan pada dataset `stepData` di dalam script modal.
- [x] Buat fungsi `toggleRequirementCheck(step, index)` untuk menghitung persentase kesiapan secara dinamis.
- [x] Buat fungsi `navigateStep(direction)` untuk memfasilitasi navigasi *Previous* / *Next* antar tahapan dengan transisi halus.
- [x] Tambahkan kalkulator tanggal sederhana berdasarkan bulan pelaksanaan yang dipilih pendaftar.
- [x] Buat modul *Document Inspector* interaktif untuk menampilkan tips detail saat badge dokumen diklik.

### Tahap 3: Pembaruan Styling & Desain UI (CSS)
- [x] Buka [public/css/landing-peta-alur-modal.css](file:///d:/MAGKOM/MAGKOM/public/css/landing-peta-alur-modal.css).
- [x] Hapus style `.btn-open-full-page` yang sudah tidak terpakai.
- [x] Tambahkan styling untuk:
  - `.readiness-meter-bar` & `.readiness-percentage` dengan warna biru Sleman.
  - `.interactive-checklist-item` (checkbox custom ramah sentuhan dengan efek centang mulus).
  - `.step-navigation-footer` (tombol *Kembali* dan *Langkah Selanjutnya*).
  - `.doc-detail-popover` (kartu panduan berkas interaktif).
  - Dukungan warna adaptif mode gelap `[data-theme="dark"]`.

### Tahap 4: Verifikasi & Pembaruan Test Suite
- [x] Perbarui file [tests/Feature/LandingPagePetaAlurModalTest.php](file:///d:/MAGKOM/MAGKOM/tests/Feature/LandingPagePetaAlurModalTest.php):
  - Ubah `assertSee('Buka Peta Lengkap')` menjadi `assertDontSee('Buka Peta Lengkap')`.
  - Tambahkan assertion untuk elemen checklist kesiapan dan navigasi interaktif.
- [x] Jalankan pengujian test suite via `php artisan test --filter=LandingPagePetaAlurModalTest`.
- [x] Uji responsivitas pada viewport desktop (1440px), tablet (768px), dan mobile (375px).

---

## 7. Strategi Testing

1. **Automated Feature Testing**:
   - `LandingPagePetaAlurModalTest`: Memastikan modal ter-render di landing page, tombol "Buka Peta Lengkap" **TIDAK ADA**, dan struktur interaktif terpasang dengan atribut ARIA yang valid.
   - `LandingPageTest`: Memastikan landing page tetap berstatus `HTTP 200 OK` dan tombol pemicu alur membuka modal secara tepat.
2. **Manual & Interactive Functional Testing**:
   - Klik navigasi tab 1–4 di sisi kiri -> Konten dan indikator progres berganti dengan transisi mulus.
   - Klik tombol *"Langkah Berikutnya"* -> Berpindah ke tahap berikutnya secara otomatis.
   - Klik checkbox pada daftar persyaratan -> Meteran kesiapan bergerak dan persentase bertambah.
   - Klik badge dokumen -> Kartu tips format berkas muncul.
   - Klik tombol tutup (ESC, tombol X, atau area luar backdrop) -> Modal menutup secara mulus tanpa mengunci scroll halaman landing.

---

## 8. Risiko & Mitigasi

| Risiko | Dampak | Mitigasi |
| :--- | :--- | :--- |
| **Kelebihan Konten di Layar Mobile** | Panel modal terpotong pada layar HP kecil | Terapkan `overflow-y: auto`, tata letak stack vertikal di layar `< 768px`, dan ukuran sentuh ramah jari (*min-height: 44px*). |
| **Regresi Test Suite Eksisting** | Test yang mengasumsikan keberadaan tombol "Buka Peta Lengkap" akan gagal | Selaraskan assertion pada `LandingPagePetaAlurModalTest.php` agar mencerminkan UX baru. |
| **Aksesibilitas Keyboard** | Navigasi sulit diakses dengan tombol Tab/Enter | Gunakan elemen HTML `<button>` dan `<input type="checkbox">` asli dengan atribut ARIA lengkap (`role="tab"`, `aria-selected`, `aria-controls`). |

---

## 9. Kriteria Selesai (Definition of Done)
- [x] Tombol *"Buka Peta Lengkap"* telah dihapus seutuhnya dari modal.
- [x] Peta alur pada landing page dapat diakses secara utuh melalui pop-up modal tanpa berpindah halaman.
- [x] Fitur **Checklist Kesiapan Interaktif** dengan meteran progres berfungsi secara dinamis.
- [x] Navigasi bertahap (*Next / Previous Step*) bekerja dengan lancar.
- [x] Fitur **Document Inspector** (tips & format berkas) dapat diinteraksikan oleh pengguna.
- [x] Tampilan konsisten pada tema terang (*light*) dan gelap (*dark*).
- [x] Seluruh pengujian fitur lulus (`php artisan test` berstatus HIJAU/PASSED).
