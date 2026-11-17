# Rencana Kerja: Indikator & Affordance Geser Kuota Carousel Mobile

Dokumen ini merancang solusi UX/UI mobile untuk seksi **Pilih Penempatan & Ketersediaan Kuota Magang** (`#penempatan` / `#kuota`). Saat ini, pada layar ponsel (mobile $\le 768\text{px}$), kartu bidang magang tampil satu layar penuh tanpa petunjuk visual (tanpa titik pagination, tanpa kartu berikutnya yang mengintip, dan tombol panah di-hide), sehingga calon peserta baru tidak menyadari bahwa kartu bidang dapat digeser (*swipeable*) untuk melihat bidang lainnya (Statistik, Persandian, Sekretariat, dll.).

---

## 1. Pahami Permintaan & Kebutuhan

- **Masalah Pengguna**: Calon peserta baru tidak tahu bahwa kartu bidang pada seksi kuota dapat digeser ke samping (*horizontal swipe/scroll*).
- **Tujuan Akhir**: Memberikan petunjuk visual (*visual affordance*) yang jelas, elegan, dan intuitif di mobile tanpa merusak tampilan desktop yang sudah rapi.
- **Asumsi Kerja**:
  - Desktop ($\ge 768\text{px}$) sudah memiliki tombol panah kiri-kanan (`.kuota-side-arrow-btn`) dan tampilan grid/carousel yang jelas, sehingga perubahan difokuskan pada mobile tanpa regresi ke desktop.
  - Jumlah kartu bidang bervariasi tergantung jenjang (SMK vs Mahasiswa) dan bulan yang dipilih (misal 3 atau 4 bidang). Indikator harus bereaksi secara dinamis saat filter jenjang/bulan diubah.

---

## 2. Investigasi Codebase (Read-Only)

1. **Markup** ([kuota.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/public/blocks/kuota.blade.php)):
   - Memiliki `#kuotaCarouselTrack` di dalam `.kuota-carousel-wrapper`.
   - Memiliki tombol `#btnCarouselPrev` dan `#btnCarouselNext` dengan kelas `.kuota-side-arrow-btn`.
   - Belum memiliki elemen kontainer dots pagination di bawah track kuota.
2. **CSS** ([landing.css](file:///d:/MAGKOM/MAGKOM/resources/css/pages/public/landing.css)):
   - Baris 1742: `.kuota-side-arrow-btn { display: none !important; }` di mobile.
   - Baris 1753: `.kuota-carousel-track` memiliki `gap: 0 !important;` dan `scrollbar-width: none;`.
   - Baris 1761: `.kuota-card` memiliki `flex: 0 0 100% !important;` sehingga kartu kedua 100% tertutup di luar layar.
3. **JavaScript** ([landing.js](file:///d:/MAGKOM/MAGKOM/resources/js/pages/public/landing.js)):
   - Fungsi `initKuotaExplorer()` menangani filter kartu berdasarkan `currentJenjang` dan `currentBulanId`.
   - Fungsi `updateKuotaView()` menampilkan kartu yang cocok dan menyembunyikan yang lain (`display: none`).
   - Fungsi `checkCarouselButtons()` mengatur status `disabled` tombol prev/next saat scroll.

---

## 3. Evaluasi Opsi Pendekatan UX

| Aspek | Opsi A: Hanya Pagination Dots (Titik-Titik di Bawah) | Opsi B: Dots + Peek Effect (Kartu Mengintip ~12%) | Opsi C (Rekomendasi): Sinergi Dots + Floating Arrows + Micro Swipe Hint |
| :--- | :--- | :--- | :--- |
| **Deskripsi** | Menambahkan 4 titik indikator di bawah kartu kuota (seperti di Hero). | Mengubah lebar kartu jadi 88% sehingga kartu ke-2 mengintip + Dots di bawah. | Kombinasi lengkap: **Dots Dinamis** + **Tombol Panah Melayang (< dan >)** + **Badge Petunjuk "Geser untuk bidang lain"** (atau Counter "1 / 4 Bidang"). |
| **Kejelasan Affordance** | Sedang (peserta melihat ada 4 titik, tapi mungkin tidak langsung mencoba swipe). | Baik (kartu yang terpotong memberi sinyal alami ada konten di samping). | **Sangat Tinggi & Aksesibel** (peserta yang terbiasa tap bisa klik panah, yang suka swipe melihat titik bergerak & hint teks). |
| **Kenyamanan Membaca** | Tinggi (kartu tetap 100% lebar). | Sedikit berkurang karena kartu lebih sempit di layar kecil ($<360\text{px}$). | **Maksimal** (kartu tetap 100% lebar penuh dan nyaman dibaca, tidak ada elemen kepotong yang sempit). |
| **Rekomendasi** | Cukup baik | Alternatif | **Direkomendasikan (Opsi C)** |

### Detail Solusi Terpilih (Opsi C):
1. **Dinamis Pagination Dots** (`#kuotaCarouselDots`):
   - Muncul di bawah track kartu kuota.
   - Menghitung otomatis jumlah kartu yang aktif (misal 4 dot untuk SMK, atau sesuai filter).
   - Dot aktif bergerak saat kartu digeser (`scroll` event listener).
   - Dot dapat di-tap untuk langsung melompat ke bidang yang dipilih.
2. **Tombol Panah Navigasi Mobile yang Jelas**:
   - Aktifkan tombol `<` dan `>` melayang bulat di mobile (seperti Hero section) dengan z-index dan posisi ergonomis, atau tempatkan tombol panah mini di samping dots indicator (`< • • • • >`).
3. **Micro-Hint Badge / Counter**:
   - Di atas kartu (atau tepat di bawah ring gauge), sertakan badge/kicker:
     `Bidang 1 dari 4 · Geser untuk melihat bidang lainnya ➔`
   - Memberikan kepastian instan kepada pengguna pemula tanpa membingungkan.

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/public/blocks/kuota.blade.php` | Modify | 1. Tambahkan baris micro-hint badge sebelum carousel track.<br>2. Tambahkan kontainer `#kuotaCarouselDots` di bawah track kartu kuota.<br>3. Tambahkan tombol navigasi mobile / sesuaikan penempatan panah. |
| `resources/css/pages/public/landing.css` | Modify | 1. Styling untuk `.kuota-carousel-dots` dan `.kuota-dot-btn`.<br>2. Styling responsif tombol panah melayang di mobile.<br>3. Styling untuk `.kuota-swipe-hint-badge` (kicker geser elegan). |
| `public/css/landing-modern.css` | Modify | Sinkronisasi aturan CSS fallback landing modern. |
| `resources/js/pages/public/landing.js` | Modify | 1. Di dalam `initKuotaExplorer()`, tambahkan fungsi `renderKuotaDots()` yang dipanggil di `updateKuotaView()`.<br>2. Tambahkan listener `scroll` pada `carouselTrack` untuk mengupdate active dot dan status counter.<br>3. Tambahkan click handler pada dot untuk scrolling ke kartu target.<br>4. Tambahkan touch swipe handling jika diperlukan. |

---

## 5. Langkah Implementasi Terstruktur

- [ ] **Langkah 1: Markup di `kuota.blade.php`**
  - [ ] Tambahkan elemen petunjuk swipe di atas carousel:
    ```html
    <div class="kuota-swipe-hint-bar" id="kuotaSwipeHintBar">
        <span class="kuota-counter-badge" id="kuotaCounterBadge">Bidang 1 dari 4</span>
        <span class="kuota-swipe-hint-text">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
            Geser untuk pilihan bidang lain
        </span>
    </div>
    ```
  - [ ] Tambahkan kontainer dots di bawah carousel track:
    ```html
    <div class="kuota-carousel-dots" id="kuotaCarouselDots"></div>
    ```

- [ ] **Langkah 2: Styling CSS di `landing.css` & `landing-modern.css`**
  - [ ] Styling `.kuota-swipe-hint-bar`: Tampil hanya di mobile ($\le 768\text{px}$), flex row rapi dengan warna teks abu-abu netral `#64748B` dan font ukuran `0.8125rem`.
  - [ ] Buka kembali tombol panah samping `.kuota-side-arrow-btn` di mobile dengan styling bulat elegan (38×38px, latar putih berbayang halus, posisi melayang vertikal center di kiri dan kanan track).
  - [ ] Styling `.kuota-carousel-dots`: Susunan titik-titik horizontal rapi, dot aktif memanjang (*pill pill* 24px) dengan warna primer `#003566`, dot inaktif 8px warna `#CBD5E1`.

- [ ] **Langkah 3: Logika JavaScript di `landing.js`**
  - [ ] Pada `initKuotaExplorer()`:
    - Buat fungsi `updateKuotaDotsAndCounter()` yang membaca kartu dengan `card.style.display !== 'none'`.
    - Render tombol-tombol dot sesuai indeks kartu visible.
    - Pasang event scroll dengan debounce/throttle untuk mencocokkan `scrollLeft` terhadap lebar kartu visible dan mengaktifkan dot yang sesuai.
    - Pasang event klik pada dot untuk melakukan `carouselTrack.scrollTo({ left: targetLeft, behavior: 'smooth' })`.
    - Perbarui teks counter (misal: "Bidang 2 dari 4").

- [ ] **Langkah 4: Kompilasi & Quality Gate**
  - [ ] Jalankan `npm run build` untuk mengompilasi CSS dan JS Vite.
  - [ ] Jalankan `php artisan test --filter="LandingPageTest"` untuk memastikan tes fungsional hijau.
  - [ ] Jalankan `./vendor/bin/pint --test` untuk verifikasi PSR-12.

- [ ] **Langkah 5: Verifikasi Visual Browser Headless (CDP)**
  - [ ] Uji tangkapan layar mobile ($390 \times 844\text{px}$) saat kartu pertama tampil (memastikan ada panah, dots, dan hint counter).
  - [ ] Uji saat digeser ke kartu kedua dan seterusnya.
  - [ ] Uji pada layar desktop ($1280 \times 800\text{px}$) untuk memastikan tidak ada perubahan/gangguan tampilan desktop.

---

## 6. Strategi Testing

1. **Uji Fungsional & Unit/Feature Test**:
   - `LandingPageTest`: Memastikan semua 9 pengujian tetap lolos (`PASS`).
2. **Uji Responsif & Interaksi**:
   - Ganti jenjang dari SMK ke Mahasiswa: Memastikan jumlah dots dan counter otomatis menyesuaikan dengan jumlah bidang mahasiswa.
   - Ganti bulan: Memastikan scroll track kembali ke posisi awal (`scrollLeft = 0`) dan dot pertama kembali aktif.
   - Klik panah Next / Prev: Memastikan kartu bergeser mulus dan dot aktif berganti.
   - Swipe layar sentuh di mobile: Memastikan pergeseran alami dan dot terupdate akurat.

---

## 7. Risiko & Mitigasi

- **Risiko Potensial**: Bila kartu di-filter (misal dari SMK 4 bidang ke Mahasiswa dengan jumlah bidang berbeda), dot lama bisa tertinggal.
  - *Mitigasi*: Fungsi `updateKuotaDotsAndCounter()` membersihkan kontainer dots (`dotsContainer.innerHTML = ''`) setiap kali `updateKuotaView()` dipanggil, lalu membangun ulang dots sesuai kartu yang lolos filter.
- **Risiko Lebar Layar**: Tombol panah melayang di kiri dan kanan kartu jangan sampai menutupi teks penting pada layar ultra-kecil ($360\text{px}$).
  - *Mitigasi*: Atur posisi tombol panah dengan margin proporsional dan `pointer-events: auto`, sementara track tetap nyaman di-swipe.

---

## 8. Kriteria Selesai (Definition of Done)

- [ ] Terdapat indikator pagination dots di bawah kartu kuota pada mobile.
- [ ] Terdapat tombol panah navigasi yang jelas dan mudah di-tap di mobile.
- [ ] Terdapat teks/badge petunjuk geser (*swipe hint*) yang memberitahukan pengguna ada bidang lain yang tersedia.
- [ ] Dots dan counter terupdate secara reaktif saat berganti jenjang, bulan, maupun saat digeser.
- [ ] Tampilan desktop tetap normal dan tidak terpengaruh.
- [ ] Seluruh automated test (`LandingPageTest` & `Pint`) lulus 100%.

