# Plan: Restorasi Efek Interaktif Tahapan Pendaftaran pada Tampilan Mobile

Dokumen rencana kerja ini disusun untuk mempertahankan dan mengaktifkan kembali seluruh efek interaktif di blok **Tahapan Pendaftaran Magang** (`#tahapan`) pada tampilan *mobile* sesuai permintaan user.

---

## 1. Tujuan
Mengaktifkan dan mempertahankan efek interaktif yang sebelumnya hanya optimal di desktop agar berjalan mulus di perangkat *mobile*:
1. **Mobil Animasi Berjalan Mengikuti Scroll (*Scroll-Driven Car on Spline*)**: Mobil mini meluncur mulus menyusuri lintasan jalan berkelok saat pengguna menggulir layar ponsel melewati seksi tahapan.
2. **Jalur Gradient Menyala (*Active Trail Glow*)**: Garis warna gradien aspal menyala seiring laju mobil menuruni rute.
3. **Aktivasi Checkpoint Dinamis (*Glowing Halos & Color Fill*)**: Saat mobil tiba di setiap pos, lingkaran checkpoint menyala (*halo glow*) dan bulatan tengah terisi warna identitas pos (Pos 01 Merah, Pos 02 Ungu, Pos 03 Biru, Pos 04 Kuning).
4. **Efek Geser & Penonjolan Kartu (*Card Slide & Focus Highlight*)**:
   - Kartu yang sedang aktif bergeser ke kiri mendekati jalan (`transform: translateX(0)`), memiliki bayangan tebal, dan kontras 100% cerah.
   - Kartu yang belum/sudah dilewati sedikit bergeser ke kanan dan meredup secara halus (`opacity: 0.45`), memberikan fokus visual tajam pada pos yang sedang dibaca.
5. **Navigasi Sentuh (*Touch-to-Scroll*)**: Mengetuk lingkaran checkpoint atau kartu tahapan di layar ponsel akan menggeser layar dan melajukan mobil langsung ke pos tersebut.

---

## 2. Analisis Masalah: Mengapa Efek Hilang di Mobile Sebelumnya?

1. **Efek CSS Dimatikan Secara Paksa di Mobile**:
   Di `resources/css/pages/public/landing.css` pada `@media (max-width: 768px)` (baris 2088-2105):
   ```css
   .tahapan-milestone-card {
       transform: none !important;
       opacity: 0.92 !important;
   }
   .tahapan-milestone-card.inactive {
       transform: none !important;
       opacity: 0.85 !important;
   }
   .tahapan-milestone-card.active {
       transform: none !important;
       opacity: 1 !important;
   }
   ```
   Aturan `transform: none !important;` mematikan semua animasi geser kartu, dan perbedaan opacity antara kartu tidak aktif (0.85) dan aktif (1.0) terlalu tipis sehingga kartu tampak datar dan statis.

2. **Kalkulasi Scroll JavaScript Hanya Dirancang untuk Kolom Sticky Desktop**:
   Di `resources/js/pages/public/landing.js` (baris 600-612):
   ```javascript
   const scrolled = startOffset - rect.top;
   const travelRange = Math.max(400, rect.height - 240);
   ```
   Pada desktop, kolom kiri bersifat *sticky* sehingga seksi tahapan tertahan di layar saat user scroll. Namun pada *mobile*, kolom bersifat susun vertikal biasa (*block stack*). Akibatnya:
   - Saat pengguna melihat seksi tahapan di tengah layar ponsel (`rect.top` ~250px - 350px), nilai `scrolled` bernilai negatif sehingga `prog = 0`.
   - Mobil selalu tertahan di titik `START` (seperti yang terlihat pada tangkapan layar user), garis gradien tidak menyala, dan tidak ada kartu yang aktif.
   - Mobil baru mulai bergerak saat seksi tahapan sudah hampir tergulir keluar dari bagian atas layar.

---

## 3. Pendekatan Teknis

### 3.1 Penyesuaian Kalkulasi Scroll Dinamis di `landing.js`
Menambahkan deteksi viewport mobile (`isMobile = window.innerWidth < 1024`):
- Pada mobile, progres scroll dihitung relatif terhadap zona baca tengah layar ponsel (`triggerPoint = window.innerHeight * 0.55`).
- Saat seksi tahapan masuk ke area baca tengah layar, mobil mulai meluncur dari `START`.
- Setiap kali kartu tahapan ke-`i` berada di posisi baca tengah layar ponsel, mobil tiba tepat di checkpoint ke-`i`, checkpoint menyala, dan kartu tersebut aktif bersinar.
- Begitu juga pada fungsi `scrollToStep(index)` dan klik pada `START` node, target scroll disesuaikan agar pos yang diklik langsung berada di tengah layar ponsel.

### 3.2 Restorasi Animasi Kartu di `landing.css`
Mengembalikan efek transisi dan kontras pada media query mobile `@media (max-width: 768px)`:
- `.tahapan-milestone-card`: `transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1) !important;`
- `.tahapan-milestone-card.inactive`: `transform: translateX(0.6rem) scale(0.97) !important; opacity: 0.45 !important; box-shadow: none !important;`
- `.tahapan-milestone-card.active`: `transform: translateX(0) scale(1) !important; opacity: 1 !important; background-color: #ffffff !important; box-shadow: 0 10px 24px -4px rgba(15, 23, 42, 0.14) !important;`

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/js/pages/public/landing.js` | `Modify` | Menyesuaikan perhitungan `updateRoadAnimation()`, `scrollToStep()`, dan event klik node START pada mode mobile agar mobil meluncur selaras dengan scroll ponsel. |
| `resources/css/pages/public/landing.css` | `Modify` | Mengembalikan animasi `transform: translateX(...)` dan kontras *active vs inactive* pada kartu tahapan di `@media (max-width: 768px)`. |
| `public/css/landing-modern.css` | `Modify` | Sinkronisasi CSS mobile agar fallback statis memiliki efek yang sama. |

---

## 5. Langkah Implementasi Terperinci

- [ ] **Langkah 1**: Edit `resources/js/pages/public/landing.js`:
  - Perbaiki fungsi `updateRoadAnimation()` agar menggunakan titik pemicu tengah viewport (`triggerPoint = window.innerHeight * 0.55`) pada layar `< 1024px`.
  - Sesuaikan `scrollToStep(index)` untuk layar mobile.
  - Sesuaikan event listener klik `START` node untuk layar mobile.
- [ ] **Langkah 2**: Edit `resources/css/pages/public/landing.css`:
  - Perbarui blok `@media (max-width: 768px)` pada selector `.tahapan-milestone-card`, `.inactive`, dan `.active`.
  - Berikan efek slide masuk `translateX(0)` saat aktif dan `translateX(0.6rem)` saat inaktif dengan `opacity: 0.45`.
- [ ] **Langkah 3**: Sinkronisasi perubahan ke `public/css/landing-modern.css`.
- [ ] **Langkah 4**: Jalankan `npm run build` untuk mengemas ulang aset Vite.
- [ ] **Langkah 5**: Jalankan `vendor/bin/pint --test` dan verifikasi bahwa pengujian feature landing tetap hijau.

---

## 6. Kriteria Selesai (Definition of Done)

- [ ] Di tampilan mobile, mobil mini meluncur di atas aspal saat pengguna menggulir layar melewati blok tahapan.
- [ ] Garis gradien warna jalan menyala mengikuti posisi mobil.
- [ ] Titik checkpoint menyala (*halo*) dan bulatan tengah terisi warna saat mobil melintasinya.
- [ ] Kartu tahapan aktif bergeser ke kiri mendekati jalan dengan shadow menonjol, sementara kartu lainnya meredup halus.
- [ ] Mengetuk salah satu checkpoint atau kartu di mobile akan melajukan mobil dan menggulir layar ke pos tersebut.
- [ ] `npm run build` dan `vendor/bin/pint --test` sukses tanpa error.

