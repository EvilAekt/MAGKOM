# Plan: Penataan Ulang Layout Footer & Penambahan Kolom Pengembang (Mobile & Desktop)

Dokumen rencana kerja ini disusun berdasarkan panduan `web-planning` dan aturan repositori `AGENTS.md` untuk merestrukturisasi tata letak kolom navigasi footer pada tampilan **Mobile** dan **Desktop**, serta menambahkan seksi kolom baru **Pengembang**.

---

## 1. Analisis Kebutuhan Pengguna

Pengguna meminta penataan ulang tata letak kolom footer dan penambahan kolom profil tim pengembang dengan spesifikasi:

### A. Konten Kolom Pengembang
Menambahkan kolom baru berlabel **Pengembang** dengan 3 tautan media sosial Instagram:
1. **aldiartha.m**: `https://www.instagram.com/aldiartha.m?dlrf=MXJwdmNjbjUwb2puag==`
2. **naufalfebrian_62**: `https://www.instagram.com/naufalfebrian_62?rpxt=MWRxMG80czlqZjN3cA==`
3. **galangadhityaram**: `https://www.instagram.com/galangadhityaram/`

### B. Tata Letak Tampilan Mobile (`@media (max-width: 768px)`)
Mengubah urutan kolom navigasi di bawah Profil Dinas/Alamat menjadi format **Grid 2x2** (2 kolom, 2 baris):
- **Baris 1**: **Tautan Terkait** (kiri atas) & **Akun** (kanan atas, di samping Tautan Terkait).
- **Baris 2**: **Media Sosial** (kiri bawah, di bawah Tautan Terkait) & **Pengembang** (kanan bawah, di samping Media Sosial).

```
+-------------------------------------------------------+
|  Profil Dinas & Alamat (Full Width di bagian atas)     |
+---------------------------+---------------------------+
|      Tautan Terkait       |           Akun            |
|                           |                           |
+---------------------------+---------------------------+
|       Media Sosial        |        Pengembang         |
|                           |                           |
+---------------------------+---------------------------+
```

### C. Tata Letak Tampilan Desktop (`@media (min-width: 1024px)`)
Mengatur kolom tautan secara horizontal berjejer 4 kolom dari kiri ke kanan:
1. **Tautan Terkait** (paling kiri)
2. **Media Sosial** (di kanannya)
3. **Akun** (kanannya lagi)
4. **Pengembang** (paling kanan)

Profil Dinas & Alamat di desktop akan ditempatkan di sisi paling kiri sebagai kolom pengenal instansi utama (Total 5 kolom proporsional: 1.5fr untuk Profil Dinas, dan masing-masing 1fr untuk Tautan Terkait, Media Sosial, Akun, dan Pengembang).

```
+---------------+----------------+----------------+------------+--------------+
| Profil Dinas  | Tautan Terkait |  Media Sosial  |    Akun    |  Pengembang  |
|   & Alamat    |  (Paling Kiri) |  (Di Kanannya) | (Kanan Lagi| (Paling Kanan|
+---------------+----------------+----------------+------------+--------------+
```

---

## 2. Pemberitahuan Perubahan Blade (Kepatuhan `AGENTS.md`)

Sesuai aturan ketat `AGENTS.md` Bagian 3 (*Blade read-only secara default*), modifikasi markup Blade memerlukan persetujuan eksplisit pengguna:

1. **Alasan Perubahan**:
   - Penambahan kolom "Pengembang" dengan tautan Instagram eksternal dan perubahan posisi kolom tautan memerlukan struktur elemen HTML semantik baru di Blade. Hal ini tidak dapat diselesaikan hanya melalui CSS/JS tanpa adanya elemen markup kolom tersebut.
2. **File Blade yang Akan Diubah**:
   - [resources/views/public/blocks/footer.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/public/blocks/footer.blade.php):
     - Menambahkan array `$defaultFooterPengembang` dan data kolom Pengembang.
     - Mengatur markup kolom dengan class identifikasi (`footer-col-tautan`, `footer-col-medsos`, `footer-col-akun`, `footer-col-pengembang`).
   - [resources/views/layouts/app.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/layouts/app.blade.php):
     - Menambahkan kolom Pengembang pada footer layout utama aplikasi agar selaras di seluruh halaman.

---

## 3. Pendekatan Teknis & Perubahan File

| Berkas | Aksi | Ringkasan Modifikasi |
| :--- | :--- | :--- |
| `resources/views/public/blocks/footer.blade.php` | `Modify` | - Tambahkan kolom **Pengembang** dengan 3 link Instagram.<br>- Susun urutan tag HTML atau tambahkan class helper (`footer-col-tautan`, `footer-col-akun`, `footer-col-medsos`, `footer-col-pengembang`). |
| `resources/views/layouts/app.blade.php` | `Modify` | Tambahkan kolom Pengembang dan susun urutan yang sama pada footer layout aplikasi. |
| `resources/css/pages/public/landing.css` | `Modify` | - Pada mobile (`@media (max-width: 768px)`): atur grid tautan menjadi 2 kolom x 2 baris sesuai spesifikasi user.<br>- Pada desktop (`@media (min-width: 1024px)`): atur grid 5 kolom (Profil Dinas + 4 kolom tautan urutan: Tautan Terkait, Media Sosial, Akun, Pengembang). |
| `public/css/landing-modern.css` | `Modify` | Sinkronisasi stylesheet landing fallback untuk grid footer baru. |

---

## 4. Langkah-Langkah Implementasi Terperinci

### Tahap 1: Penambahan Konten Kolom Pengembang di Blade
- [ ] Buka `resources/views/public/blocks/footer.blade.php`:
  - Tambahkan array `$defaultFooterPengembang`:
    ```php
    $defaultFooterPengembang = [
        ['label' => 'aldiartha.m', 'url' => 'https://www.instagram.com/aldiartha.m?dlrf=MXJwdmNjbjUwb2puag=='],
        ['label' => 'naufalfebrian_62', 'url' => 'https://www.instagram.com/naufalfebrian_62?rpxt=MWRxMG80czlqZjN3cA=='],
        ['label' => 'galangadhityaram', 'url' => 'https://www.instagram.com/galangadhityaram/'],
    ];
    ```
  - Tambahkan render blok kolom Pengembang:
    ```blade
    <div class="footer-col-pengembang">
        <h3 class="footer-col-title">Pengembang</h3>
        <ul class="footer-links-clean">
            @foreach($footerPengembang as $itemPengembang)
                <li>
                    <a href="{{ $itemPengembang['url'] }}" target="_blank" rel="noopener noreferrer">
                        {{ $itemPengembang['label'] }}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
    ```

### Tahap 2: Penataan Grid CSS Mobile & Desktop
- [ ] Buka `resources/css/pages/public/landing.css`:
  - Pada tampilan **Mobile** (`@media (max-width: 768px)`):
    - Kontainer tautan diatur dengan CSS Grid 2 kolom: `grid-template-columns: repeat(2, 1fr); gap: 1.75rem 1.25rem;`
    - Kolom Profil Dinas menempati baris penuh atas (`grid-column: span 2;`).
    - Atur urutan (`order`):
      - `order: 1` -> Profil Dinas & Alamat (`grid-column: span 2`)
      - `order: 2` -> Tautan Terkait (Baris 1 Kolom 1)
      - `order: 3` -> Akun (Baris 1 Kolom 2, di samping Tautan Terkait)
      - `order: 4` -> Media Sosial (Baris 2 Kolom 1, di bawah Tautan Terkait)
      - `order: 5` -> Pengembang (Baris 2 Kolom 2, di samping Media Sosial)
  - Pada tampilan **Desktop** (`@media (min-width: 1024px)`):
    - `grid-template-columns: 1.5fr 1fr 1fr 1fr 1fr; gap: 2rem;`
    - Atur urutan (`order`):
      - `order: 1` -> Profil Dinas & Alamat (paling kiri)
      - `order: 2` -> Tautan Terkait (di kanannya / kolom tautan paling kiri)
      - `order: 3` -> Media Sosial (di kanannya)
      - `order: 4` -> Akun (kanannya lagi)
      - `order: 5` -> Pengembang (paling kanan)
- [ ] Terapkan penyesuaian yang sama pada `public/css/landing-modern.css`.

### Tahap 3: Quality Gate & Pengujian
- [ ] Jalankan build aset Vite:
  ```powershell
  $env:PATH = "D:\laragon\bin\nodejs\node-v22;" + $env:PATH; npm run build
  ```
- [ ] Jalankan pemeriksaan format kode Pint:
  ```powershell
  & "D:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe" vendor/bin/pint --test
  ```
- [ ] Jalankan automated test suite terkait landing:
  ```powershell
  & "D:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe" artisan test --filter=Landing
  ```

---

## 5. Kriteria Selesai (Definition of Done)

- [ ] Kolom **Pengembang** tampil di footer dengan 3 link Instagram aktif (`aldiartha.m`, `naufalfebrian_62`, `galangadhityaram`) yang membuka tab baru (`target="_blank" rel="noopener noreferrer"`).
- [ ] Di tampilan **Mobile**:
  - Tautan Terkait berada di baris atas sebelah kiri.
  - Akun berada di baris atas sebelah kanan (di samping Tautan Terkait).
  - Media Sosial berada di baris bawah sebelah kiri (di bawah Tautan Terkait).
  - Pengembang berada di baris bawah sebelah kanan (di samping Media Sosial).
- [ ] Di tampilan **Desktop**:
  - Kolom tautan berurutan dari kiri ke kanan: Tautan Terkait, Media Sosial, Akun, Pengembang.
- [ ] `npm run build` dan `vendor/bin/pint --test` lulus sukses.
- [ ] Seluruh unit & feature tests tetap 100% hijau (*pass*).

