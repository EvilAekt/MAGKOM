# Rencana Revisi Komprehensif Mobile UX MagKom (Berdasarkan Audit 6 Screenshot)

Dokumen rencana ini disusun berdasarkan analisis visual terhadap 6 tangkapan layar aktual yang dikirimkan oleh pengguna. Rencana ini menargetkan perbaikan tuntas pada anomali mobile di seluruh halaman utama.

---

## 1. Analisis Masalah & Solusi per Screenshot

### A. Screenshot 1: Tombol "Daftar Sekarang" Duplikat di Hero Mobile
- **Gejala Visual**: Pada kartu Hero di mobile, tombol "Daftar Sekarang" muncul dua kali (satu di bagian atas sebelum Persyaratan, dan satu lagi di bagian bawah setelah Dokumen).
- **Akar Masalah (RCA)**:
  Pada CSS mobile (`@media (max-width: 767.98px)`), terdapat aturan `.btn-hero-cta-desktop { display: none !important; }`, namun beberapa baris setelahnya terdapat deklarasi `.btn-hero-cta { display: inline-flex !important; }`. Karena kedua selector memiliki spesifisitas kelas yang sama, deklarasi terakhir menimpa display none sehingga tombol desktop tetap muncul.
- **Rencana Tindakan**:
  1. Tingkatkan spesifisitas selector: `.btn-hero-cta.btn-hero-cta-desktop { display: none !important; }`.
  2. Pastikan hanya `.hero-mobile-cta-wrap .btn-hero-cta-mobile` yang berstatus `display: inline-flex !important;` pada mobile.
  3. Pastikan penataan teks header, kicker, dan tahun memiliki margin yang seimbang tanpa ada tombol yang bertumpuk.

---

### B. Screenshot 2: Alur & Tahapan (Tahapan Road) Teks Terpotong & Bertumpuk
- **Gejala Visual**:
  Teks deskripsi pada kartu langkah ("Penyelesaian program magang, pengumpulan laporan akhir...") terpotong di tepi kanan layar dan sebagian buram/redup. Selain itu, ilustrasi bagian FAQ di bawahnya bertumpuk secara canggung dengan jalur jalan SVG.
- **Akar Masalah (RCA)**:
  1. `.tahapan-cards-list` memiliki `padding: 80px 0 80px 175px;` statis untuk mengakomodasi SVG jalan selebar 170px. Di layar ponsel berlebar 360px–390px, ruang tersisa untuk teks kartu hanya ~185px.
  2. Kelas `.tahapan-milestone-card.inactive` menambahkan `transform: translateX(1rem); opacity: 0.42;`, yang menggeser kartu 16px lebih jauh ke kanan hingga keluar layar.
  3. Section tahapan tidak memiliki pemisah vertikal (*clearance*) yang memadai terhadap section berikutnya di mobile.
- **Rencana Tindakan**:
  1. Pada `@media (max-width: 768px)`, ubah tata letak Alur & Tahapan menjadi kartu vertikal yang adaptif:
     - Perkecil offset jalan atau sesuaikan padding list menjadi `padding: 40px 10px 40px 110px;`.
     - Nonaktifkan `transform: translateX(1rem);` pada mobile agar kartu tidak terdorong keluar layar.
     - Tambahkan background dan border yang jelas pada kartu tahapan aktif/inaktif agar teks tetap terbaca dengan kontras tinggi (`opacity: 0.85` bukan `0.42`).
  2. Berikan `margin-bottom: 3rem; clear: both;` pada wrapper jalan agar tidak tumpang tindih dengan ilustrasi FAQ.

---

### C. Screenshot 3: Slide 2 Hero Carousel Memiliki Area Putih Kosong Raksasa
- **Gejala Visual**:
  Saat berpindah ke Slide 2 (Dokumentasi Foto Kantor), foto tampil di bagian atas tetapi di bawahnya terdapat area putih kosong raksasa sepanjang ~470px sebelum dot indikator di bagian bawah.
- **Akar Masalah (RCA)**:
  `.hero-slider-track` menggunakan `align-items: stretch;`. Karena Slide 1 memiliki banyak teks dan sub-kartu, tingginya mencapai ~750px. Slide 2 otomatis dipaksa setinggi 750px. Karena fotonya dibatasi `max-height: 280px;`, sisa tinggi 470px menjadi ruang kosong putih yang hampa.
- **Rencana Tindakan**:
  1. Lengkapi Slide 2 pada tampilan mobile agar menjadi kartu informasi dokumentasi yang utuh dan bernilai estetis:
     - Foto dokumentasi di bagian atas dengan rasio proporsional `16:9` (`border-radius: 12px`).
     - Di bawah foto, tambahkan judul dokumentasi dan deskripsi resmi: *"Dokumentasi Resmi Kegiatan & Inovasi Mahasiswa Magang di Dinas Komunikasi dan Informatika Kabupaten Sleman"*.
     - Tambahkan tombol aksi penjelajahan langsung: *"Lihat Ketersediaan Kuota Magang ↓"*.
  2. Dengan demikian, Slide 2 memiliki bobot visual yang seimbang dengan Slide 1, menghilangkan void kosong, dan memberikan pengalaman visual profesional seperti portal Lentera Sleman.

---

### D. Screenshot 4: Tombol Panah Kuota Menutupi Teks & Tag Jurusan
- **Gejala Visual**:
  Tombol panah navigasi kiri (`<`) pada carousel kuota mengambang persis di atas teks *"Penempatan: Jurusan yang Sesuai"* dan tag jurusan, sehingga menghalangi keterbacaan dan mempersulit sentuhan pengguna.
- **Akar Masalah (RCA)**:
  `.kuota-side-arrow-btn.prev` diposisikan secara absolut dengan `left: 0.25rem; top: 50%;`. Pada kartu yang rapat di mobile, tombol bundar ini mendarat tepat di atas konten dalam kartu.
- **Rencana Tindakan**:
  1. Di layar mobile ($\le 768\text{px}$):
     - Pindahkan tombol navigasi panah ke bagian kontrol atas (`.kuota-carousel-header`) di samping filter jurusan, ATAU
     - Sembunyikan tombol panah samping yang menutupi kartu (`display: none !important;`) dan aktifkan *touch-swipe* alami dengan `scroll-snap-type: x mandatory; -webkit-overflow-scrolling: touch;` disertai indikator dot atau peeking card di tepi kanan agar pengguna tahu kartu dapat digeser.
  2. Ini menjamin isi kartu, teks kuota, dan tombol *"Daftar Sekarang ➔"* tidak pernah terhalang oleh tombol panah.

---

### E. Screenshot 5: Simulator Peta Alur Balon Teks Pos Terpotong
- **Gejala Visual**:
  Pada `/peta-alur` di HP, balon dialog pos 1 (Registrasi Akun) terpotong di sisi kiri layar (hanya terlihat *"serta membuat akun...", "ngkap, dan menunggu..."*).
- **Akar Masalah (RCA)**:
  Elemen balon komik SVG digambar dengan offset negatif ($x = -20$) pada titik koordinat pos ($x = 230$). Di layar HP portrait yang sempit, kamera SVG tidak memperhitungkan lebar balon sehingga sisi kiri balon berada di luar batas layar kiri.
- **Rencana Tindakan**:
  1. Pada viewport mobile di `peta-alur.js` dan CSS:
     - Sesuaikan centering kamera saat pos aktif agar memberikan bantalan aman (*safe margin*) di sisi kiri (minimal 40px dari tepi layar).
     - Berikan penyesuaian viewBox mobile agar seluruh balon komik dan tombol *"Lanjut Pos"* masuk sepenuhnya ke dalam area pandang.
     - Sediakan opsi HUD sheet di bagian bawah pada layar ultra-kecil ($<360\text{px}$) jika resolusi layar tidak memungkinkan SVG melebar.

---

### F. Screenshot 6: Halaman Login & Register Mengambang Aneh dengan Latar Gelap Kosong
- **Gejala Visual**:
  Pada tampilan mobile, kartu putih login mengambang di bagian atas seperti modal desktop yang terdampar, dengan latar belakang biru gelap berbintang yang kosong melompong di bagian bawah.
- **Akar Masalah (RCA)**:
  Container auth memiliki `min-height: 100vh; align-items: flex-start; padding: 20px 14px;` dengan kartu berukuran dinamis. Ketika formulir login selesai, sisa layar ke bawah tetap berupa canvas gelap kosong tanpa fungsi.
- **Rencana Tindakan**:
  1. Pada `@media (max-width: 768px)`:
     - Buat tata letak mobile auth menjadi *full-bleed app-like experience*: kartu putih mengisi tinggi layar secara alami (`min-height: 100vh; border-radius: 0;` atau kartu edge-to-edge dengan margin minimal).
     - Selaraskan background canvas pada mobile agar tidak ada kontras void gelap yang terputus di bawah formulir.
     - Pastikan navigasi `← Beranda` dan branding MagKom Sleman di bagian atas tetap rapi, serta tombol login dan Google SSO mudah dijangkau satu jempol.

---

## 2. Tahapan Eksekusi Bertahap

1. **Fase 1**: Perbaikan duplikasi CTA Hero & konten seimbang Slide 2 pada `hero.blade.php` & `landing.css` / `landing-modern.css`.
2. **Fase 2**: Penataan ulang panah navigasi Kuota pada `kuota.blade.php` & `landing.css` agar tidak menutupi isi kartu.
3. **Fase 3**: Penyesuaian layout responsive Alur & Tahapan road pada `tahapan.blade.php` & `landing.css` agar teks tidak keluar layar.
4. **Fase 4**: Penataan ulang mobile auth layout pada `auth-depth.css` dan `auth-depth.blade.php`.
5. **Fase 5**: Penyesuaian batas kamera dan koordinat balon pada `peta-alur.css` dan `peta-alur.js`.
6. **Fase 6**: Kompilasi Vite asset (`npm run build`), format check (`pint --test`), dan eksekusi test suite PHPUnit komprehensif.

