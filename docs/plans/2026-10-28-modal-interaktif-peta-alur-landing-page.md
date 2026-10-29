# Rencana Kerja: Solusi Hybrid Peta Alur Magang (Quick Modal di Landing Page + Tautan Halaman Penuh)

Dokumen ini merinci rancangan dan langkah kerja untuk menerapkan **Opsi 2: Solusi Hybrid** pada fitur **"Peta Alur Interaktif"** Landing Page MAGKOM:
1. Tombol di Landing Page membuka **Modal Pratinjau Interaktif Cepat (Quick Preview Modal)** tanpa berpindah halaman, menyajikan 4 pilar tahapan utama yang responsif, interaktif, dan to-the-point.
2. Di dalam header/footer modal disediakan tombol aksi jelas **"Buka Simulasi & Peta Lengkap"** (`target="_blank"`) yang mengarahkan pengguna ke halaman penuh [panduan-alur](file:///d:/MAGKOM/MAGKOM/resources/views/public/panduan-alur.blade.php) bagi yang ingin eksplorasi simulator DAG mendalam.

---

## 1. Tujuan
1. Mengubah interaksi tombol **"Peta Alur Interaktif"** di Landing Page (`landing.blade.php`) agar membuka **Modal Interaktif Cepat (*In-Page*)** yang elegan, ringan, dan modern.
2. Menyajikan visualisasi interaktif alur 4 tahapan magang (Registrasi Akun -> Seleksi & Berkas -> Pelaksanaan & Logbook -> Penilaian & Sertifikat) lengkap dengan filter jenjang (Semua, SMK, Mahasiswa), filter tipe (Mandiri, Kelompok), serta panel inspeksi detail persyaratan & aturan yang berubah dinamis saat tahapan diklik.
3. Menyediakan tombol aksi terkemuka **"Buka Simulasi & Peta Lengkap"** yang membuka route `/panduan-alur` di tab baru (`target="_blank"`) sehingga pengguna tidak kehilangan tab landing page.
4. Menjaga aksesibilitas penuh (a11y, ARIA dialog, ESC key, focus trapping, backdrop click), dark/light mode compatibility, serta performa kilat tanpa layout shift.

---

## 2. Asumsi & Pertanyaan Terbuka
### Asumsi Kerja Logis:
- **Asumsi 1:** Tombol pemicu modal adalah tombol *"Peta Alur Interaktif"* pada section tahapan pendaftaran (`resources/views/public/landing.blade.php#L528-L534`). Link navigasi di navbar tetap dapat diarahkan membuka modal ini atau memiliki atribut pemicu yang sama.
- **Asumsi 2:** Konten di dalam modal mengambil sumber data alur yang konsisten dengan pedoman resmi Diskominfo yang sudah ada di `panduan-alur.blade.php` (4 pilar tahapan pendaftaran, persyaratan berkas, timeline durasi, dan output sertifikat).
- **Asumsi 3:** Modal menggunakan Vanilla JavaScript dan CSS modern (DS-MAGKOM tokens) tanpa dependensi eksternal berat agar landing page tetap ringan dan memiliki skor First Contentful Paint (FCP) yang optimal.

### Pertanyaan Terbuka:
- *Tidak ada ambiguitas kritis tersisa karena opsi modal in-page telah dikonfirmasi dan disetujui langsung oleh pengguna.*

---

## 3. Pendekatan Teknis

### Perbandingan Alternatif Arsitektur

| Kriteria | Opsi A: In-Page Blade Partial Component (Rekomendasi) | Opsi B: Async AJAX / Fetch Template Fragment | Opsi C: Iframe Embed `/panduan-alur` |
| :--- | :--- | :--- | :--- |
| **Deskripsi** | Komponen modal di-render langsung di Blade sebagai partial terpisah `_peta_alur_modal.blade.php`. | Modal mengambil potongan HTML dari endpoint controller saat tombol diklik. | Modal menampilkan tag `<iframe>` yang memuat `/panduan-alur`. |
| **Kecepatan Buka** | **Instant (0ms)** — tidak ada latency jaringan. | Membutuhkan waktu tunggu request HTTP (100–300ms). | Lambat (beban render full page layout, CSS, script duplikat). |
| **Aksesibilitas** | **Tinggi** — ARIA attributes, keyboard escape, focus trap native. | Sedang — butuh loading indicator & state management. | Rendah — navigasi dan fokus di dalam iframe sering bermasalah. |
| **Responsivitas & Tema** | **Sangat Baik** — otomatis mewarisi tema gelap/terang dan token CSS landing page. | Baik — perlu sync state tema. | Buruk pada mobile screen. |

**Keputusan Teknis Terpilih:**  
Memilih **Opsi A (Blade Partial Component)** karena memberikan responsivitas tercepat (*zero latency*), tampilan yang serasi dengan desain landing page MagKom, serta kemudahan pemeliharaan kode secara modular.

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/public/partials/_peta_alur_modal.blade.php` | **Create** | Komponen modal interaktif alur magang lengkap dengan backdrop, switcher jenjang/tipe, timeline tahapan klik, kartu detail dinamis, dan kontrol keyboard `Esc`. |
| `resources/views/public/landing.blade.php` | **Modify** | Mengubah link tombol "Peta Alur Interaktif" menjadi `button`/trigger modal `openPetaAlurModal()`, menyertakan include partial modal, dan menambahkan micro-script pembuka. |
| `public/css/landing-peta-alur-modal.css` | **Create** | Style modal bertema modern (glassmorphism backdrop, layout 2 kolom responsif, animasi pop-in, dark mode adaptif via `[data-theme="dark"]`). |
| `tests/Feature/LandingPagePetaAlurModalTest.php` | **Create** | Feature test untuk memvalidasi markup modal, tombol pemicu, aksesibilitas ARIA, dan atribut interaksi di landing page. |

---

## 5. Langkah Implementasi

### Fase 1: Pembuatan Komponen Modal Blade & Style CSS
- [x] **Langkah 1.1**: Buat stylesheet modular [landing-peta-alur-modal.css](file:///d:/MAGKOM/MAGKOM/public/css/landing-peta-alur-modal.css):
  - Definisi backdrop overlay dengan `backdrop-filter: blur(8px)` dan z-index tinggi (`z-index: 9999`).
  - Container dialog dengan border-radius modern, border lembut, dan animasi entrance (*fade-in + subtle scale*).
  - Header modal: badge "Peta Alur Interaktif MagKom", judul, tombol "Buka Halaman Penuh", dan tombol close (X).
  - Sub-toolbar kontrol: Pill filter jenjang (Semua, SMK, Mahasiswa) dan tipe (Mandiri, Kelompok).
  - Grid interaktif: 
    - Sisi Kiri: 4 Step card interaktif dengan nomor langkah, badge status, ikon, dan indikator aktif.
    - Sisi Kanan: Panel inspeksi detail berisi rincian berkas, ketentuan seleksi, checklist alur, dan tombol aksi "Daftar Akun".
  - Dukungan penuh mode gelap menggunakan CSS variabel token `var(--background)`, `var(--foreground)`, `var(--card)`.
  - Aturan responsif untuk perangkat mobile (<768px: stack vertikal).

- [x] **Langkah 1.2**: Buat Blade view partial [resources/views/public/partials/_peta_alur_modal.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/public/partials/_peta_alur_modal.blade.php):
  - Atribut aksesibilitas: `id="petaAlurModal"`, `role="dialog"`, `aria-modal="true"`, `aria-labelledby="modalPetaAlurTitle"`, `hidden`.
  - Dataset interaktif JSON/JavaScript tersemat untuk 4 tahapan magang:
    1. **Tahap 1: Registrasi & Akun Peserta** (Verifikasi email, kelengkapan biodata diri, persetujuan wali/instansi).
    2. **Tahap 2: Pengajuan Bidang & Seleksi Berkas** (Unggah proposal, CV, surat pengantar, verifikasi admin, kuota real-time).
    3. **Tahap 3: Pelaksanaan & Logbook Mingguan** (Orientasi, pengerjaan tugas classroom, pembimbingan, validasi mingguan).
    4. **Tahap 4: Evaluasi Akhir & E-Sertifikat** (Penilaian angka & predikat, survei kepuasan, unduh sertifikat resmi bertanda tangan).
  - Skrip pengatur interaksi client-side:
    - Fungsi `openPetaAlurModal()` dan `closePetaAlurModal()`.
    - Event listener penekanan tombol `Escape` dan klik di luar area dialog (backdrop click).
    - Switcher interaktif untuk berpindah step aktif dan memperbarui panel detail secara instan.
    - Kunci scroll window (`document.body.style.overflow = 'hidden'`) saat modal aktif.

### Fase 2: Integrasi ke Landing Page
- [x] **Langkah 2.1**: Update [resources/views/public/landing.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/public/landing.blade.php):
  - Include file stylesheet `public/css/landing-peta-alur-modal.css` di bagian `@push('styles')` atau head.
  - Ubah elemen tombol pemicu di baris 528-535 menjadi tombol interaktif dengan atribut `onclick="openPetaAlurModal()"` dan atribut `aria-haspopup="dialog"`.
  - Berikan hook juga pada link navbar "Peta Alur" agar dapat memicu modal jika diinginkan, atau menyediakan menu pilihan (Buka Popup Cepat / Buka Halaman Penuh).
  - Include partial Blade modal `@include('public.partials._peta_alur_modal')` tepat sebelum penutup section atau sebelum tag `</body>`.

### Fase 3: Pengujian Otomatis & Visual
- [x] **Langkah 3.1**: Buat file test [tests/Feature/LandingPagePetaAlurModalTest.php](file:///d:/MAGKOM/MAGKOM/tests/Feature/LandingPagePetaAlurModalTest.php):
  - Uji response `GET /` berhasil HTTP 200.
  - Assert halaman memuat trigger button `openPetaAlurModal` dan dialog container `id="petaAlurModal"`.
  - Assert modal memuat atribut ARIA (`role="dialog"`, `aria-modal="true"`).
  - Assert terdapat tombol rujukan *"Buka Halaman Penuh"* menuju route `panduan-alur`.
- [x] **Langkah 3.2**: Jalankan pengujian test suite dengan perintah `php artisan test --filter=LandingPagePetaAlurModalTest`.
- [x] **Langkah 3.3**: Verifikasi visual di browser (responsivitas layar HP, tablet, desktop, dan interaksi buka-tutup modal).

---

## 6. Strategi Testing

1. **Feature Testing (PHPUnit/Pest)**:
   - Validasi respon route `/` menghasilkan kode 200.
   - Pengecekan keberadaan struktur HTML modal, class CSS, dan komponen tombol aksi.
2. **Interactive & Usability Testing**:
   - Klik tombol "Peta Alur Interaktif" -> Modal muncul dengan animasi halus tanpa reload halaman.
   - Klik setiap tahapan (Step 1 - 4) -> Card detail di sebelah kanan berganti isi teks dan persyaratan dokumen secara responsif.
   - Filter Jenjang / Tipe -> Konten deskripsi menyesuaikan kebutuhan siswa SMK vs Mahasiswa.
   - Tekan tombol `Escape` pada keyboard -> Modal tertutup seketika dan scroll body pulih normal.
   - Klik tombol "X" atau area di luar modal -> Modal tertutup seketika.
   - Klik tombol *"Buka Halaman Penuh"* -> Membuka route `/panduan-alur` di tab yang sama/baru.

---

## 7. Risiko & Mitigasi

| Risiko | Dampak | Mitigasi |
| :--- | :--- | :--- |
| **Z-index conflict** dengan navbar sticky di landing page | Modal tertutup sebagian oleh navbar | Tetapkan `z-index: 99999` pada modal backdrop dan dialog container. |
| **Scroll jump / page jumping** saat modal dibuka | Kenyamanan pengguna terganggu | Gunakan `type="button"` pada trigger dan jangan gunakan tag anchor dengan `href="#"` kosong. Gunakan `e.preventDefault()`. |
| **Tampilan sempit pada layar mobile (<480px)** | Teks atau card terpotong | Terapkan media query flex/grid column wrap, max-height `90vh`, dan scroll internal container dengan `-webkit-overflow-scrolling: touch`. |

---

## 8. Kriteria Selesai (Definition of Done)
- [x] Dokumen rencana disetujui secara eksplisit oleh pengguna.
- [x] File stylesheet `public/css/landing-peta-alur-modal.css` selesai dibuat dan terpasang rapi.
- [x] File partial Blade `_peta_alur_modal.blade.php` berhasil dibuat dengan 4 tahapan interaktif dan panel detail.
- [x] Tombol "Peta Alur Interaktif" di `landing.blade.php` berhasil dikaitkan dan membuka modal secara instan.
- [x] Navigasi Escape, close backdrop, dan body lock scroll berfungsi tanpa cacat.
- [x] Seluruh automated feature test (`LandingPagePetaAlurModalTest`) berhasil lolos (*green/pass*).
- [x] Tidak ada breaking changes pada route `/panduan-alur` yang sudah ada.
