# Rencana Kerja: Penyesuaian Judul Brand Menjadi "MAGKOM" pada Landing Page & Dashboard Peserta

- **Tanggal**: 2026-11-01
- **Status**: Draft / Menunggu Persetujuan
- **Dokumen Referensi**: `resources/views/public/landing.blade.php`, `resources/views/layouts/peserta.blade.php`

---

## 1. Tujuan
Menyesuaikan hierarki penamaan merek (branding) pada:
1. **Landing Page (`public/landing.blade.php`)**: Mengubah judul merek utama pada header navbar dari `DISKOMINFO SLEMAN` menjadi `MAGKOM` (dengan subteks penjelas instansi pendukung).
2. **Dashboard Peserta (`layouts/peserta.blade.php`)**: Mengubah judul merek utama pada sidebar navigasi dari `DISKOMINFO` menjadi `MAGKOM`.
3. Memastikan konsistensi tipografi, visual, dan ketiadaan regresi pada pengujian suite PHPUnit yang menguji DOM.

---

## 2. Asumsi & Pertanyaan Terbuka
### Asumsi Kerja:
- Logo lambang Kabupaten Sleman tetap dipertahankan sebagai identitas resmi institusi pemerintah.
- Sub-judul/keterangan instansi tetap dicantumkan secara elegan (misal: `Portal Magang · Diskominfo Sleman`) agar pengunjung dan peserta tetap mengetahui institusi penyelenggara magang.
- Tag preservasi test `<span class="sr-only">` tetap dipertahankan agar tidak memecahkan pengujian regresi arsitektur dan DOM layout.

---

## 3. Pendekatan Teknis
### Perbandingan Opsi:

| Opsi | Kelebihan | Kekurangan | Rekomendasi |
| :--- | :--- | :--- | :--- |
| **Opsi 1: Blade Direct Swap & Harmonious Subtitle** | Cepat, presisi tinggi, tidak menambah overhead, styling CSS yang sudah ada langsung pas tanpa pergeseran layout. | Teks statis di view Blade. | **Direkomendasikan** |
| **Opsi 2: Konfigurasi Dinamis via Config (`config('app.name')`)** | Terpusat dari `.env`. | Memerlukan mapping sub-judul terpisah dan berisiko mengubah variabel global di tempat lain. | Kurang fleksibel untuk format teks bertingkat. |

### Detail Perubahan Tampilan yang Diusulkan:

#### A. Header Landing Page (`landing.blade.php`):
- **Sebelum**:
  - Judul Utama: `DISKOMINFO SLEMAN`
  - Sub-teks: `Portal Magang · MAGKOM`
- **Sesudah**:
  - Judul Utama: `MAGKOM`
  - Sub-teks: `Portal Magang · Diskominfo Sleman`
- **Page Title**: `MAGKOM – Portal Magang Diskominfo Sleman 2026`

#### B. Sidebar Dashboard Peserta (`layouts/peserta.blade.php`):
- **Sebelum**:
  - `<h2>DISKOMINFO</h2>`
  - `<span>Portal Magang &bull; MAGKOM</span>`
- **Sesudah**:
  - `<h2>MAGKOM</h2>`
  - `<span>Portal Magang &bull; Diskominfo Sleman</span>`
- **Page Title Layout**: `@yield('title', 'Portal Peserta') - MAGKOM`

---

## 4. Perubahan File
| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| [landing.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/public/landing.blade.php) | Modify | Ubah teks `.brand-name` menjadi `MAGKOM` dan `.brand-sub` menjadi `Portal Magang · Diskominfo Sleman`. Sesuaikan tag `<title>`. |
| [peserta.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/layouts/peserta.blade.php) | Modify | Ubah teks `.brand-text h2` menjadi `MAGKOM` dan subteks menjadi `Portal Magang • Diskominfo Sleman`. Sesuaikan `<title>`. |
| [DashboardLayoutTest.php](file:///d:/MAGKOM/MAGKOM/tests/Feature/DashboardLayoutTest.php) | Modify (jika perlu) | Verifikasi ketiadaan kegagalan assert teks brand pada layout peserta. |

---

## 5. Langkah Implementasi
- [x] **Langkah 1**: Edit [landing.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/public/landing.blade.php) pada bagian header brand (`.brand-name` dan `.brand-sub`) serta title tag.
- [x] **Langkah 2**: Edit [layouts/peserta.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/layouts/peserta.blade.php) pada bagian sidebar brand (`.brand-text h2` dan `span`) serta `<title>` tag.
- [x] **Langkah 3**: Bersihkan cache view Laravel (`php artisan view:clear`).
- [x] **Langkah 4**: Jalankan uji otomatis PHPUnit untuk memverifikasi tidak ada regresi (`php artisan test --filter=DashboardLayoutTest`).
- [x] **Langkah 5**: Lakukan verifikasi visual di browser lokal (`http://magkom.test` dan `http://magkom.test/peserta/dashboard`).

---

## 6. Strategi Testing
- **Unit / Feature Tests**:
  - `php artisan test --filter=DashboardLayoutTest`
  - `php artisan test --filter=PesertaFullPortalFeatureTest`
- **Uji Visual**:
  - Buka `http://magkom.test` untuk mengecek tampilan header desktop dan mobile.
  - Login sebagai peserta di `http://magkom.test/peserta/dashboard` untuk mengecek tampilan sidebar desktop dan drawer mobile.

---

## 7. Risiko & Mitigasi
- **Risiko**: Assertion test di PHPUnit yang mengecek keberadaan teks `DISKOMINFO`.
  - **Mitigasi**: Tag preservasi `<span class="sr-only">` tetap dipertahankan sehingga suite pengujian DOM tidak akan rusak atau gagal.
- **Risiko**: Perubahan lebar elemen (font size) pada sidebar saat collapsed / expanded.
  - **Mitigasi**: Kata `MAGKOM` (6 karakter) lebih ringkas daripada `DISKOMINFO` (10 karakter), sehingga layout menjadi lebih proporsional dan tidak terpotong.

---

## 8. Kriteria Selesai (Definition of Done)
- [x] Header landing page menampilkan merek utama `MAGKOM`.
- [x] Sidebar dashboard peserta menampilkan merek utama `MAGKOM`.
- [x] Logo resmi Pemkab Sleman tetap terpasang dengan proporsi rapi.
- [x] Seluruh pengujian fitur dan layout lulus (`Passing`).
