# Rencana Kerja: Relokasi Tombol Navigasi Carousel Kuota ke Samping Kiri & Kanan (Floating Side Nav) & Pembersihan Scrollbar Native

**Tanggal**: 27 Oktober 2026  
**Status**: Selesai Diimplementasikan & Terverifikasi (Production Ready)  
**Dokumen**: `docs/plans/2026-10-27-relokasi-tombol-scroll-kuota-ke-samping.md`  
**Area Terkait**: Section Kuota Jurusan (`#kuota` pada `resources/views/public/landing.blade.php`, `public/css/landing-modern.css`, `public/js/landing-interactive.js`)

---

## 1. Masalah & Kebutuhan Pengguna
Berdasarkan tangkapan layar dan instruksi pengguna:
1. **Posisi Tombol Navigasi Kurang Intuitif**:
   - Saat ini, tombol panah scroll `<` dan `>` berada di pojok kanan atas di luar track kartu (`.carousel-controls`).
   - Pengguna mengusulkan dan menanyakan: *"di bagian ini tombol scrollnya di taruh samping ga sih coba buat plan nya"*.
   - Meletakkan tombol di samping kiri dan kanan kartu jauh lebih intuitif dan ergonomis secara standar UX e-commerce/portal modern (seperti Netflix, Tokopedia, atau Figma UI), karena pengguna secara alami mencari kontrol navigasi tepat di sisi kiri untuk geser mundur dan di sisi kanan untuk geser maju.
2. **Scrollbar Horizontal Bawaan Browser Terlihat Mengganggu**:
   - Pada tangkapan layar, terlihat bilah scrollbar native browser Windows yang tebal (`< [========] >`) tepat di bawah kartu jurusan.
   - Hal ini mengurangi estetika tampilan bersih dan elegan portal.
   - Scrollbar native perlu disembunyikan (*hidden scrollbar with preserved swipe/scrollability*), sehingga navigasi horizontal dipandu oleh tombol samping melayang yang elegan dan gestur geser sentuh.

---

## 2. Solusi & Pendekatan Teknis

### A. Arsitektur Tata Letak Wrapper & Tombol Samping (Floating Side Nav)
1. **Wrapper Relatif**:
   - Bungkus track carousel kartu kuota ke dalam sebuah kontainer relatif:
     ```html
     <div class="kuota-carousel-wrapper">
         <!-- Tombol Panah Kiri Melayang -->
         <button type="button" id="btnCarouselPrev" class="kuota-side-arrow-btn prev" aria-label="Geser ke kiri" title="Geser ke kiri" disabled>
             <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                 <polyline points="15 18 9 12 15 6"></polyline>
             </svg>
         </button>

         <!-- Track Kartu Kuota -->
         <div id="kuotaCarouselTrack" class="kuota-carousel-track">
             ... kartu jurusan ...
         </div>

         <!-- Tombol Panah Kanan Melayang -->
         <button type="button" id="btnCarouselNext" class="kuota-side-arrow-btn next" aria-label="Geser ke kanan" title="Geser ke kanan">
             <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                 <polyline points="9 18 15 12 9 6"></polyline>
             </svg>
         </button>
     </div>
     ```
2. **Styling Tombol Panah Samping**:
   - `position: absolute; top: 50%; transform: translateY(-50%); z-index: 10;`
   - Ukuran: `42px x 42px`, bentuk lingkaran sempurna (`border-radius: 9999px;`).
   - Warna latar: Putih bersih (`background: #ffffff;`) dengan border halus (`border: 1px solid var(--border);`) dan bayangan elevasi modern (`box-shadow: 0 4px 14px rgba(0, 0, 0, 0.1);`).
   - Sisi Kiri: `left: -1rem;` (pada desktop), sedikit menjorok keluar agar tidak menutupi isi kartu.
   - Sisi Kanan: `right: -1rem;` (pada desktop).
   - Di perangkat layar kecil (mobile): tombol bergeser masuk aman (`left: 0.25rem; right: 0.25rem;`) dengan backdrop blur agar tetap terlihat jelas tanpa terpotong batas layar.
   - Efek disabled: Tombol redup (`opacity: 0.25; cursor: not-allowed;`) saat scroll mencapai batas awal atau akhir.
   - Bebas animasi hover jitter (hanya perubahan warna background/border halus).

### B. Pembersihan Scrollbar Horizontal Native
1. **Sembunyikan Scrollbar di Seluruh Mesin Browser**:
   ```css
   .kuota-carousel-track {
       display: flex;
       gap: 1.15rem;
       overflow-x: auto;
       scroll-behavior: smooth;
       scroll-snap-type: x mandatory;
       padding: 0.5rem 0.25rem 1rem;
       scrollbar-width: none; /* Firefox */
       -ms-overflow-style: none; /* IE / Edge */
   }
   .kuota-carousel-track::-webkit-scrollbar {
       display: none; /* Chrome, Safari, Edge Webkit */
       width: 0;
       height: 0;
   }
   ```
2. Gestur swipe di perangkat sentuh tetap berjalan 100% mulus berkat `overflow-x: auto` dan `scroll-snap-type`.

### C. Sinkronisasi Interaktivitas JavaScript
1. Selektor tombol di `landing-interactive.js` tetap menggunakan ID `btnCarouselPrev` dan `btnCarouselNext` sehingga logika scroll step `carouselTrack.scrollBy(...)` dan disable state `checkCarouselButtons()` langsung berfungsi otomatis tanpa regresi.
2. Saat tombol diklik:
   - Tombol Prev menggeser ke kiri sebesar lebar 1 kartu + gap (~`card.offsetWidth + 18px`).
   - Tombol Next menggeser ke kanan sebesar lebar 1 kartu + gap.

---

## 3. Rencana Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/public/landing.blade.php` | Modify | 1) Menghapus kontainer lama `.carousel-controls` yang ada di atas track. 2) Membungkus `#kuotaCarouselTrack` dengan `.kuota-carousel-wrapper`. 3) Menempatkan tombol panah `btnCarouselPrev` dan `btnCarouselNext` di dalam wrapper sebagai kontrol samping. |
| `public/css/landing-modern.css` | Modify | 1) Menambahkan CSS `.kuota-carousel-wrapper` (relative, full-width). 2) Menambahkan styling `.kuota-side-arrow-btn` (posisi absolute vertikal center di samping kiri & kanan, bentuk lingkaran, bayangan elegan, transisi halus). 3) Menyembunyikan native scrollbar pada `.kuota-carousel-track` (`scrollbar-width: none` dan `::-webkit-scrollbar { display: none }`). 4) Menghapus styling usang `.carousel-controls` dan `.carousel-arrow-btn`. |
| `public/js/landing-interactive.js` | Maintain | Memastikan logika disable saat `scrollLeft <= 4` dan `scrollLeft >= maxScroll - 4` tetap sinkron dan mulus. |

---

## 4. Langkah-Langkah Implementasi Bertahap

- [x] **Langkah 1**: Perbarui struktur HTML di `resources/views/public/landing.blade.php` (pindahkan tombol panah ke dalam wrapper samping kartu kuota dan hapus kontrol lama di atas).
- [x] **Langkah 2**: Tambahkan styling CSS di `public/css/landing-modern.css` untuk `.kuota-carousel-wrapper`, `.kuota-side-arrow-btn.prev`, `.kuota-side-arrow-btn.next`, dan hilangkan native scrollbar pada `.kuota-carousel-track`.
- [x] **Langkah 3**: Bersihkan cache view Laravel (`php artisan view:clear`) dan verifikasi tombol scroll samping dan penyembunyian scrollbar di peramban.

---

## 5. Kriteria Selesai (Definition of Done)
1. **Tombol di Samping**: Tombol panah navigasi `<` berada di samping kiri kartu dan tombol `>` berada di samping kanan kartu secara simetris dan vertikal center.
2. **Scrollbar Bersih**: Scrollbar abu-abu tebal native browser tidak lagi terlihat, menghasilkan antarmuka yang rapi dan profesional.
3. **Fungsionalitas Sempurna**: Mengklik tombol samping kiri/kanan menggeser kartu dengan animasi mulus (*smooth scrolling*).
4. **State Disabled Tepat**: Tombol kiri disabled saat di ujung kiri, dan tombol kanan disabled saat di ujung kanan.
5. **Responsif**: Bekerja baik di resolusi mobile, tablet, dan desktop tanpa overlap isi konten.
