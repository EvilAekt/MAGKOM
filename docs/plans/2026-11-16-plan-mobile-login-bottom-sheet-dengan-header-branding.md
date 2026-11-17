# Rencana Kerja: Tampilan Mobile Login & Register Native App Sesuai Gambar Referensi

Berdasarkan gambar referensi yang diunggah (`media_1794834826368_8d63fc47.png`) dan instruksi pengguna:
> *"untuk menu login seperti itu tapi logo teks tetep di pertahankan yang sekarang bahkan isinya tanpa mengubah apapun hanya tampilan mobile nya buat plan nya dlu"*

Tampilan mobile halaman Login (dan Register) akan ditransformasi menjadi gaya **Native Mobile App Bottom Sheet dengan Top Header Branding**, tanpa mengubah konten, teks, logo Sleman, maupun logika autentikasi yang sudah ada.

---

## 1. Analisis Visual Berdasarkan Gambar Referensi

| Bagian | Tampilan di Gambar Referensi | Implementasi Teknis (CSS Scoped) |
| :--- | :--- | :--- |
| **Header Atas (Navy)** | Latar belakang biru gelap (`#003566` ke `#01183A`) dengan animasi cincin radar/orbital di pojok kanan atas. | Menampilkan `section:has(svg.ring)` di mobile sebagai banner header atas dengan posisi cincin SVG di kanan atas (`top: -40px; right: -50px; opacity: 0.65`). |
| **Identitas / Logo** | Ikon/Lambang, Teks **MAGKOM** (Putih), dan **DINAS KOMINFO KAB. SLEMAN** (Kuning Emas). | **Mempertahankan 100%** logo resmi Kabupaten Sleman, tipografi `MAGKOM`, dan teks emas `DINAS KOMINFO KAB. SLEMAN` yang sudah ada di `_left_panel.blade.php`. Tidak ada teks atau logo yang diubah. |
| **Tagline Headline** | **Daftar magang lebih mudah** (Putih, tebal, rata kiri). | Judul slide di header mobile diset rata kiri (`text-align: left !important; font-size: 1.35rem; font-weight: 800`). |
| **Tagline Deskripsi** | *Cek kuota, ajukan pendaftaran, dan ikuti prosesnya dari satu platform.* | Paragraf subtitle diset rata kiri (`text-align: left; color: rgba(255, 255, 255, 0.8)`). |
| **Lembaran Form (Bottom Sheet)** | Kartu putih solid dengan sudut melengkung modern di bagian atas (`border-radius: 28px 28px 0 0`) yang menutupi bagian bawah header dan memanjang ke bawah. | `section.auth-panel-scroll` / `section:has(form)` diset berlatar putih `#FFFFFF`, radius `28px 28px 0 0`, tumpukan z-index di atas header, dan bayangan elevasi halus. |
| **Isi Formulir Login** | "Selamat datang", Email, Kata sandi, Ingat saya & Lupa kata sandi?, CAPTCHA & Refresh, Masuk, Google SSO, dan Tautan Daftar. | **Semua isi, field, validasi, dan tombol formulir dipertahankan 100% tanpa perubahan apapun.** |

---

## 2. Rincian Teknis Implementasi

Semua penyesuaian ini dapat diselesaikan **100% melalui CSS** di [resources/css/pages/auth/auth-depth.css](file:///d:/MAGKOM/MAGKOM/resources/css/pages/auth/auth-depth.css), **tanpa perlu mengubah berkas Blade (`.blade.php`) maupun backend Controller**, sesuai dengan aturan baku AGENTS.md (Blade read-only):

1. **Aktifkan `_left_panel` sebagai Header di Mobile**:
   - Di `@media (max-width: 768px)`, ubah `section:has(svg.ring)` dari `display: none` menjadi `display: flex !important; flex-direction: column !important; width: 100% !important; min-height: auto !important; height: auto !important;`.
   - Berikan padding yang nyaman (`padding: 24px 20px 32px 20px`).
2. **Posisi Grafis Radar Orbital**:
   - Sembunyikan elemen latar yang berat, posisikan cincin radar `svg` di sudut kanan atas header secara proporsional (`width: 320px; height: 320px; right: -50px; top: -35px; opacity: 0.6`).
3. **Tipografi Header Rata Kiri**:
   - Atur logo, `h2`, dan `p` di header mobile agar tersusun rapi rata kiri (`text-align: left`), persis seperti pada gambar referensi.
4. **Form Sheet Putih Melengkung Modern**:
   - Atur container formulir putih dimulai langsung di bawah header dengan `border-radius: 28px 28px 0 0; margin-top: -16px; min-height: calc(100dvh - 180px); background: #FFFFFF;`.
   - Mengalir mulus ke bawah tanpa menyisakan ruang gelap kosong.
5. **Desktop Tetap Utuh**:
   - Tampilan desktop ($\ge 992\text{px}$) tetap menggunakan tata letak dua kolom berdampingan (*dual-panel split modal*) dengan kanvas partikel 3D.

---

## 3. Tahapan Eksekusi

1. **Pembaruan CSS**:
   - Modifikasi [resources/css/pages/auth/auth-depth.css](file:///d:/MAGKOM/MAGKOM/resources/css/pages/auth/auth-depth.css) pada blok media query `@media (max-width: 768px)`.
2. **Quality Gate**:
   - `npm run build` (kompilasi Vite aset frontend).
   - `./vendor/bin/pint --test` (verifikasi kode).
   - `php artisan test --filter="AuthDepthVisualParityTest"` (memastikan semua elemen dan teks login/register tetap lulus 100%).

