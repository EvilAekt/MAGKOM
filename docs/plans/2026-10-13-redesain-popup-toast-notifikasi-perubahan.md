# Plan: Redesain Popup Toast Notifikasi Perubahan / Pengaturan Tersimpan

Dokumen rencana teknis untuk memperbarui komponen pop-up flash notification / toast alert di seluruh portal MagKom agar sesuai persis dengan referensi visual yang diberikan (icon badge circular mint lembut dengan shield-check, judul bold, dan subteks deskripsi abu-abu).

---

## 1. Tujuan

Mengganti gaya tampilan flash alert / toast notification bawaan yang kaku (blok alert warna solid/pastel penuh) menjadi komponen **Toast Notification Card** modern, minimalis, dan elegan persis seperti gambar referensi:
- **Badge Ikon Bulat (Circular Badge)**: Latar hijau mint lembut (`#ECFDF5`), ikon perisai dengan centang (*shield-check*) berwarna hijau emerald (`#10B981` / `#059669`).
- **Dua Tingkat Hirarki Teks**:
  - **Judul Notifikasi (Bold/Semi-bold 700)**: Warna gelap pekat (`#0F172A` di light mode, `#F8FAFC` di dark mode), misal *"Pengaturan tersimpan"*, *"Perubahan Berhasil Disimpan"*.
  - **Deskripsi/Subteks (Normal 400)**: Warna abu-abu netral (`#64748B` di light mode, `#94A3B8` di dark mode), misal *"Perubahan berhasil diterapkan ke sistem."*.
- **Container Card**: Sudut membulat modern (`border-radius: 12px`), border halus (`1px solid #E2E8F0` / dark: `#26354D`), background putih bersih (`#FFFFFF` / dark: `#182234`), bayangan halus (`box-shadow`), serta tombol tutup (&times;) opsional yang rapi.
- **Dukungan Multivariant yang Selaras**:
  - `success`: Shield-check hijau mint (`#ECFDF5` / `#10B981`).
  - `error`: Shield-alert / circle-x merah muda (`#FEF2F2` / `#EF4444`).
  - `warning`: Triangle-alert amber muda (`#FFFBEB` / `#F59E0B`).
  - `info`: Info-circle biru muda (`#EFF6FF` / `#3B82F6`).

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi Teknis
1. Portal MagKom memiliki dua layout master utama:
   - [`resources/views/layouts/peserta.blade.php`](file:///d:/MAGKOM/MAGKOM/resources/views/layouts/peserta.blade.php) (Layout portal Peserta Magang).
   - [`resources/views/layouts/dashboard.blade.php`](file:///d:/MAGKOM/MAGKOM/resources/views/layouts/dashboard.blade.php) (Layout portal Admin, Pembimbing, dan Superadmin).
2. Beberapa halaman memiliki container alert inline lokal (seperti halaman Pengaturan/Profil dan Survei). Komponen baru akan dibuat sebagai Blade component atau partial layout terstandar (`resources/views/components/toast-notification.blade.php` atau layout flash alert terpadu) agar otomatis seragam di seluruh portal.
3. Notifikasi mendukung transisi otomatis (auto-dismiss setelah 5 detik) dengan opsi klik tombol close (&times;) manual jika pengguna ingin langsung menutupnya.

### Pertanyaan Terbuka
- Apakah penempatan notifikasi diinginkan sebagai **Inline Banner** di atas konten (seperti posisi saat ini) atau sebagai **Floating Toast** (melayang di pojok kanan atas layar)?
  - *Rekomendasi default*: Mendukung mode floating toast di pojok kanan atas/tengah atas dengan fallback inline banner yang rapi, sehingga tidak mendorong layout konten utama secara agresif.

---

## 3. Pendekatan Desain

### Perbandingan Pendekatan

| Aspek | Opsi A: Modifikasi CSS Inline Class Eksisting | Opsi B: Dedicated Toast Component + CSS Terpadu (Direkomendasikan) |
|---|---|---|
| **Struktur Markup** | Mengubah styling `.alert` dan `.app-alert` saja tanpa mengubah struktur DOM | Menggunakan struktur semantik baru dengan circular icon badge, title container, message, dan close button |
| **Kemiripan dengan Desain** | Kurang fleksibel karena markup lama hanya 1 baris teks tanpa circular badge | **100% Presisi** sesuai gambar (circular badge + judul bold + subteks) |
| **Konsistensi Antar Portal** | Rentan inkonsistensi antara `layouts/peserta` dan `layouts/dashboard` | Konsisten di seluruh portal Peserta, Admin, Pembimbing, Superadmin |
| **Dukungan Dark Mode** | Perlu patching berulang di banyak file | Otomatis terintegrasi dengan variabel `[data-theme="dark"]` |

**Keputusan**: Menggunakan **Opsi B**, yaitu standardisasi markup dan styling flash alert/toast di kedua layout master (`layouts/peserta.blade.php` dan `layouts/dashboard.blade.php`), serta mendukung session format baik string tunggal maupun array/custom title (`session('success')` atau `session('status')`).

---

## 4. Perubahan File

| File Path | Aksi | Ringkasan Perubahan |
|---|---|---|
| `resources/views/layouts/peserta.blade.php` | Ubah | Perbarui markup flash message section dan tambahkan styling `.toast-notification` / `.app-toast` dengan circular icon badge, title, subtitle, dan dark mode. |
| `resources/views/layouts/dashboard.blade.php` | Ubah | Selaraskan markup `.alerts-container` / `.app-alert` menjadi gaya toast circular badge dengan warna mint, judul bold, dan deskripsi sesuai gambar. |
| `resources/views/peserta/profil/show.blade.php` | Ubah | Sesuaikan alert sukses lokal profil agar seragam dengan toast standar baru. |
| `resources/views/admin/pengaturan/index.blade.php` | Ubah | Pastikan alert sukses pengaturan tersimpan menggunakan format toast visual baru. |
| `resources/views/pembimbing/pengaturan/index.blade.php` | Ubah | Pastikan alert sukses pengaturan pembimbing menggunakan format toast visual baru. |

---

## 5. Langkah Implementasi

### Fase 1: Desain Token CSS & Komponen Toast
- [x] Buat CSS rule `.app-toast` / `.toast-alert`:
  - Card: `display: flex; align-items: flex-start; gap: 14px; padding: 14px 18px; border-radius: 14px; background: #FFFFFF; border: 1px solid #E2E8F0; box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06);`.
  - Icon Badge: `width: 42px; height: 42px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;`.
  - Sukses: `background: #ECFDF5; color: #10B981;` dengan icon *shield-check*.
  - Teks: Judul `.toast-title` (`font-weight: 700; color: #0F172A; font-size: 0.9375rem;`) dan `.toast-desc` (`font-weight: 400; color: #64748B; font-size: 0.84375rem; line-height: 1.45;`).
  - Dark Mode: Background `#182234`, border `#26354D`, title `#F8FAFC`, desc `#94A3B8`, badge `rgba(16, 185, 129, 0.18)`.

### Fase 2: Integrasi ke Layout Peserta
- [x] Perbarui section flash messages di `resources/views/layouts/peserta.blade.php`:
  - Deteksi pesan dan parsing otomatis: Jika pesan berasal dari pengaturan/profil, jadikan judul *"Pengaturan tersimpan"* dan isi deskripsi pesan detail.
  - Tambahkan tombol dismiss (&times;).

### Fase 3: Integrasi ke Layout Dashboard (Admin, Pembimbing, Superadmin)
- [x] Perbarui section flash messages di `resources/views/layouts/dashboard.blade.php`:
  - Terapkan struktur circular badge mint shield-check yang sama persis agar seluruh peran memiliki pengalaman visual seragam.

### Fase 4: Pengujian & Polish
- [x] Uji responsivitas pada layar desktop, tablet, dan smartphone.
- [x] Uji transisi pergantian mode terang dan mode gelap.
- [x] Jalankan automated feature tests: `artisan test tests/Feature/PesertaPortalWorkflowTest.php` dan `AdminChildPagesVisualTest` serta `StaffProfileAndSettingsTest`.

---

## 6. Strategi Testing

1. **Automated Tests**:
   - Jalankan seluruh portal workflow test untuk memastikan assert flash message (`assertSessionHas`, `assertSee`) tetap lulus 100%.
2. **Manual Visual QA**:
   - Simpan form pengaturan / profil peserta & periksa munculnya pop-up toast:
     - Apakah terdapat icon perisai centang di lingkaran hijau mint lembut?
     - Apakah teks judul tebal dan deskripsi abu-abu terpasang rapi?
     - Apakah tampilan di mode gelap terpadu tanpa glare putih?

---

## 7. Risiko & Mitigasi

- **Risiko**: Pesan flash dari session Laravel yang hanya berupa satu string teks sederhana (misal: `"Data berhasil diperbarui."`).
  - **Mitigasi**: Komponen akan memisahkan secara cerdas: judul default dinamis (misal: *"Perubahan Berhasil Disimpan"* atau *"Pengaturan Tersimpan"* jika di menu pengaturan), dan isi session ditampilkan sebagai teks deskripsi di baris kedua.
- **Risiko**: Notifikasi menutupi tombol penting pada layar mobile.
  - **Mitigasi**: Posisi toast diberi z-index proporsional (`z-index: 1050`), max-width adaptif (`max-width: 480px`), dan tombol close (&times;) mudah diklik di ponsel.

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Seluruh notifikasi pop-up / flash alert sukses menampilkan ikon perisai berlingkaran hijau mint lembut sesuai gambar referensi.
- [x] Memiliki struktur dua baris: Judul bold pekat dan deskripsi abu-abu netral.
- [x] Mendukung varian error, warning, dan info dengan bahasa visual yang setara.
- [x] Tampil elegan dan harmonis pada Light Mode maupun Dark Mode.
- [x] Seluruh automated test suite Laravel lulus tanpa regresi.
