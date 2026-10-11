# Plan: Revisi Accordion Interaktif Sidebar SuperAdmin (Collapsible Submenu Konfigurasi & Grup)

- **Tanggal**: 2026-10-10
- **Status**: Selesai (Completed & Verified)
- **Author**: Antigravity (web-planning & web-implementation)
- **Target File**: `resources/views/layouts/dashboard.blade.php`
- **Terkait Desain**: Figma Sleman MagKom (Node `560-24892`)

---

## 1. Tujuan

Memperbaiki dan meningkatkan interaktivitas menu sidebar pada layout aplikasi (`layouts.dashboard`), khususnya grup **Konfigurasi** (serta grup **Administrasi** dan **Peserta**), agar:
1. Dapat **diklik untuk membuka (*expand*) dan menutup (*collapse*)** daftar submenu secara fleksibel sehingga sidebar tidak terlalu panjang dan memakan ruang vertikal.
2. Memperbaiki *bug* sintaks JavaScript inline eksisting yang menyebabkan fungsi `toggleNavGroup` gagal dimuat di browser.
3. Menghadirkan transisi animasi buka-tutup yang halus (*smooth transition*) dan rotasi ikon chevron yang presisi sesuai standar desain Figma MagKom Sleman.

---

## 2. Asumsi & Investigasi Teknis

### Akar Masalah (Root Cause Investigation)
Saat ini pada [`resources/views/layouts/dashboard.blade.php`](file:///g:/MAGKOM/resources/views/layouts/dashboard.blade.php) baris 1546–1565:
```javascript
function toggleSidebar(forceState) {
    ...
    if (shouldOpen) {
        ...
    } else {
        ...
    }
// <-- KURUNG KURAWAL PENUTUP '}' TIDAK ADA DI SINI
function toggleNavGroup(btn) {
    ...
}
```
Fungsi `toggleSidebar()` **tidak memiliki kurung kurawal penutup `}`**, sehingga browser mendeteksi `SyntaxError: Unexpected token 'function'`. Akibatnya:
- Seluruh blok `<script>` gagal dieksekusi oleh mesin JavaScript browser.
- Saat pengguna mengklik menu **Konfigurasi** (`onclick="toggleNavGroup(this)"`), terjadi galat `Uncaught ReferenceError: toggleNavGroup is not defined`.
- Menu submenu tetap terbuka permanen (*static/stuck*) dan tidak bisa ditutup.

### Asumsi
- Pengelompokan menu berlaku untuk tiga grup utama SuperAdmin:
  - **Administrasi** (Verifikasi Berkas, Kuota Magang, Waiting List, Penempatan, Laporan & Ekspor)
  - **Peserta** (Monitoring Peserta, Arsip Peserta)
  - **Konfigurasi** (Pengaturan Sistem, Bulan Magang, Slot Khusus, Master Jurusan, Bidang Dinas)
- Secara *default*, jika pengguna sedang membuka halaman di dalam grup tersebut (misal halaman Slot Khusus), grup tersebut otomatis terbuka (*expanded*) saat halaman pertama kali dimuat. Namun, pengguna **bebas mengkliknya kapan saja untuk menutup (*collapse*)** agar sidebar lebih ringkas dan fleksibel.

---

## 3. Pendekatan Desain & Arsitektur

Kami mengevaluasi 2 pendekatan teknis untuk animasi dan interaksi accordion:

| Parameter | Opsi A: CSS Grid Transition (`grid-template-rows: 0fr -> 1fr`) *(Direkomendasikan)* | Opsi B: Max-Height Animation (`max-height: 0 -> 400px`) |
| :--- | :--- | :--- |
| **Kehalusan Animasi** | **Sangat Halus & Alami** (tanpa jeda waktu statis). | Sering terjadi *lag* atau percepatan aneh karena estimasi tinggi statis. |
| **Dukungan Konten Dinamis** | Menyesuaikan tinggi elemen secara otomatis tanpa hardcoded pixel. | Terbatas pada angka `max-height` tertentu; risiko terpotong jika item bertambah. |
| **Aksesibilitas (a11y)** | Mendukung `aria-expanded="true/false"` dan transisi opacity mulus. | Standar. |
| **Kinerja Browser** | Menggunakan akselerasi compositing modern (Chrome, Edge, Firefox, Safari). | Cenderung memicu layout recalculation saat transisi. |

### Rekomendasi
Memilih **Opsi A**, yaitu membungkus submenu menggunakan teknik CSS Grid (`0fr` ke `1fr`) dikombinasikan dengan perbaikan logika JavaScript Vanilla yang bersih, bebas dependensi, serta sinkronisasi atribut `aria-expanded` dan rotasi chevron.

---

## 4. Perubahan File

| File Path | Aksi | Ringkasan Perubahan |
| :--- | :---: | :--- |
| [`resources/views/layouts/dashboard.blade.php`](file:///g:/MAGKOM/resources/views/layouts/dashboard.blade.php) | **Modify** | 1. Memperbaiki syntax error JavaScript di penutup `toggleSidebar`.<br>2. Menyempurnakan fungsi `toggleNavGroup(btn)` agar menangani buka/tutup dengan handal.<br>3. Memperbarui CSS `.nav-sub-menu` dengan transisi halus (`grid-template-rows` atau height transition yang mulus).<br>4. Memastikan chevron berotasi 180° / 90° dengan rapi saat status terbuka/tertutup. |
| [`tests/Feature/SuperAdminFigmaDesignAlignmentTest.php`](file:///g:/MAGKOM/tests/Feature/SuperAdminFigmaDesignAlignmentTest.php) | **Modify** | Menambahkan pengujian otomatis (*feature test*) untuk memastikan markup nav-group, tombol konfigurasi ber-`aria-expanded`, dan pemanggilan `toggleNavGroup` ter-render sempurna di DOM. |

---

## 5. Langkah Implementasi

- [x] **Langkah 1**: Perbaiki sintaks JavaScript pada `resources/views/layouts/dashboard.blade.php` dengan menambahkan penutup blok fungsi `toggleSidebar(forceState)`.
- [x] **Langkah 2**: Perbaiki dan tingkatkan fungsi `toggleNavGroup(btn)` di dalam script layout agar:
  - Mengambil elemen kontainer `.nav-group-container` dan submenu `.nav-sub-menu`.
  - Mengubah atribut `aria-expanded` antara `'true'` dan `'false'`.
  - Mengaktifkan/menonaktifkan class `.show` pada submenu.
- [x] **Langkah 3**: Perbarui styling CSS untuk `.nav-sub-menu` dan `.nav-group-chevron`:
  - Gunakan transisi halus (*smooth transition*) pada submenu sehingga saat dibuka/ditutup tidak meloncat kasar (*jumpy*).
  - Pastikan rotasi chevron SVG (`transform: rotate(90deg)` atau `180deg`) berputar mulus dengan `transition: transform 0.2s ease-in-out`.
  - Pertahankan visual active pill (seperti pada "Slot Khusus" dengan background putih dan teks biru 700) saat submenu terbuka.
- [x] **Langkah 4**: Tambahkan test assertion pada `tests/Feature/SuperAdminFigmaDesignAlignmentTest.php` untuk memvalidasi atribut interaktivitas sidebar.
- [x] **Langkah 5**: Jalankan automated test suite untuk memastikan *zero regression*.
- [x] **Langkah 6**: Lakukan verifikasi visual di browser nyata menggunakan `browser_subagent` untuk menguji interaksi klik buka/tutup menu Konfigurasi, Administrasi, dan Peserta.

---

## 6. Strategi Testing

1. **Automated Feature Tests**:
   - Menjalankan `php artisan test tests/Feature/SuperAdminFigmaDesignAlignmentTest.php`.
   - Menjalankan `php artisan test tests/Feature/DashboardModulesRenderingTest.php`.
2. **Browser Interactive Testing**:
   - Membuka halaman `/superadmin/pengaturan` di browser.
   - Mengklik tombol grup "Konfigurasi".
   - Memastikan submenu menutup dengan mulus dan chevron berputar kembali.
   - Mengklik kembali tombol grup "Konfigurasi" untuk memastikan submenu terbuka kembali.
   - Memeriksa konsol developer tools browser untuk memastikan **0 JavaScript error/warning**.

---

## 7. Risiko & Mitigasi

| Potensi Risiko | Tingkat Dampak | Strategi Mitigasi |
| :--- | :---: | :--- |
| **Submenu tertutup saat refresh halaman** | Rendah | Pertahankan blade directive `@php $isConfigActive = ... @endphp` sehingga halaman aktif tetap otomatis membuka submenu saat pertama kali dimuat, namun tetap dapat ditutup dengan klik. |
| **Menu mobile drawer terpengaruh** | Rendah | Menguji fungsi `toggleSidebar` pada resolusi desktop (>1024px) dan mobile (<1024px) untuk menjamin drawer buka-tutup tetap bekerja optimal. |

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Menu "Konfigurasi" dapat diklik untuk membuka dan menutup sub-itemnya.
- [x] Menu "Administrasi" dan "Peserta" juga memiliki perilaku accordion interaktif yang sama.
- [x] Tidak ada lagi galat JavaScript di console browser (`0 syntax errors`, `0 runtime exceptions`).
- [x] Transisi buka-tutup terasa halus, modern, dan nyaman digunakan.
- [x] 100% automated test suite SuperAdmin tetap lulus (*PASS*).

---

## 9. Catatan Perubahan & Saran Lanjutan

- **Perubahan selama pengerjaan**:
  - Penutup fungsi `toggleSidebar` berhasil ditambahkan dan fungsi `toggleNavGroup` dilengkapi penjaga parameter `if (!btn) return;`.
  - CSS submenu `.nav-sub-menu` diperbarui dengan `max-height: 0` -> `max-height: 500px` dan `opacity` serta `visibility` transition agar tidak terjadi pergeseran layout kasar.
  - Pengujian browser otomatis (`browser_subagent`) membuktikan bahwa menu Konfigurasi, Administrasi, dan Peserta dapat dibuka-tutup berkali-kali tanpa error.
- **Saran Lanjutan (Out of Scope)**:
  - Menyimpan preferensi collapse pengguna ke dalam `localStorage` jika di kemudian hari diinginkan sidebar mengingat status buka/tutup antar sesi browser.
