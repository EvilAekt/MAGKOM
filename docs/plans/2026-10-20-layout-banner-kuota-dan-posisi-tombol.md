# Rencana Kerja: Restrukturisasi Tata Letak Banner Informasi Bulan & Penempatan Tombol di Bawah Banner

**Tanggal Pembuatan**: 20 Oktober 2026  
**Status**: ✅ Selesai Diimplementasikan  
**Branch**: `test-1`  
**Referensi Desain**:
1. **Gambar 1**: Tampilan Manajemen Kuota saat ini (Header tombol & selector bulan berada di atas kotakan biru, background kotakan biru berupa gradien biru polos).
2. **Gambar 2**: Tampilan Dashboard Admin resmi MagKom (Kotakan biru hero banner berada di atas dengan background ilustrasi resmi Diskominfo `dashboard_hero_bg.jpg`, lalu di bawahnya terdapat baris sub-bar kontrol pilihan bulan).
3. **Gambar 3**: Potongan elemen kontrol Gambar 1 (Judul "Bulan & kuota", tombol "Ubah bulan" & "+ Buat bulan baru", dan card dropdown "Bulan Magang").

---

## 1. Tujuan
Menata ulang susunan layout halaman **Manajemen Kuota** (`resources/views/admin/kuota/index.blade.php`) agar selaras dengan pola layout **Dashboard Admin** (Gambar 2):
1. **Hero Banner (Kotakan Biru) Berada di Atas**:
   - Menempatkan kotakan biru informasi bulan magang aktif pada posisi teratas sebagai visual utama halaman.
   - Mengganti background gradien polos pada kotakan biru dengan **background ilustrasi/pattern resmi** yang sama dengan Dashboard Admin Gambar 2 (`images/dashboard_hero_bg.jpg` dipadukan dengan gradien biru signature MagKom).
2. **Memindahkan Tombol & Kontrol ke Bawah Kotakan Biru**:
   - Memindahkan elemen-elemen dari Gambar 3 (tombol **`Ubah bulan`**, tombol **`+ Buat bulan baru`**, dan dropdown **`Bulan Magang:`**) ke posisi **di bawah kotakan biru**, sejajar dalam satu baris kontrol (*control bar*) yang rapi dan terpadu seperti pada Gambar 2.

---

## 2. Asumsi & Rujukan Desain

1. **Konsistensi Aset Background Gambar**:
   - Kotakan biru akan menggunakan aset resmi Diskominfo Sleman yang sudah ada di proyek:
     ```css
     background: linear-gradient(95deg, rgba(6, 65, 204, 0.96) 0%, rgba(6, 65, 204, 0.88) 45%, rgba(6, 65, 204, 0.38) 100%),
                 url('{{ asset('images/dashboard_hero_bg.jpg') }}') right center / cover no-repeat;
     ```
   - Dilengkapi dengan efek pencahayaan halus (`box-shadow` dan pseudo-element radial highlight) yang sama persis dengan Dashboard Admin.
2. **Baris Kontrol Terpadu di Bawah Kotakan Biru**:
   - Mengadopsi pola `.adm-control-bar` seperti di Dashboard Admin:
     - Sisi Kiri: Ikon kalender, label `Bulan Magang:`, dan dropdown pilihan gelombang/bulan magang.
     - Sisi Kanan: Tombol `Ubah bulan` (outline) dan tombol `+ Buat bulan baru` (primary blue).
3. **Penempatan Judul Halaman "Bulan & kuota"**:
   - Judul halaman dan subtitle tetap dipertahankan agar pengguna tetap memiliki konteks halaman yang jelas dan seluruh pengujian otomatis (`AdminKuotaRevampTest`) tetap lulus 100%.

---

## 3. Evaluasi Opsi Arsitektur Tata Letak

| Parameter | Opsi 1: Unified Sub-Bar di Bawah Banner (Rekomendasi - Persis Gambar 2) | Opsi 2: Dua Baris Terpisah di Bawah Banner |
| :--- | :--- | :--- |
| **Deskripsi** | Menempatkan Hero Banner kotakan biru di atas. Tepat di bawahnya dibuat satu baris kartu kontrol terpadu (`adm-control-bar`): sisi kiri memuat selector dropdown bulan magang, sisi kanan memuat tombol `Ubah bulan` dan `+ Buat bulan baru`. | Menempatkan Hero Banner di atas. Di bawahnya baris 1 memuat selector bulan magang penuh, lalu baris 2 memuat header judul dan tombol-tombol. |
| **Kelebihan** | 1. **100% selaras dengan pola Dashboard Gambar 2**.<br>2. Sangat hemat ruang vertikal dan tidak membuat halaman terlalu panjang.<br>3. Alur interaksi admin sangat intuitif: melihat ringkasan bulan aktif di banner, lalu mengganti bulan atau mengklik aksi tombol tepat di bawahnya. | Mempertahankan struktur 2 baris lama secara harfiah. |
| **Kekurangan** | Memerlukan sedikit penyesuaian flexbox agar responsif di layar mobile. | Tampilan menjadi terlalu berjarak dan boros ruang vertikal dibandingkan Gambar 2. |
| **Keputusan** | **DIPILIH (Opsi 1)**. | Ditolak karena kurang bersih dan tidak mencerminkan efisiensi Gambar 2. |

---

## 4. Rincian Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/admin/kuota/index.blade.php` | `Modify` | 1. Perbarui background `.hero-banner-blue` dengan gambar `dashboard_hero_bg.jpg` dan gradien signature Gambar 2.<br>2. Pindahkan susunan HTML sehingga Hero Banner kotakan biru berada di atas.<br>3. Satukan selector dropdown bulan dan tombol `Ubah bulan` & `+ Buat bulan baru` ke dalam satu baris sub-bar kontrol di bawah kotakan biru. |
| `tests/Feature/AdminKuotaRevampTest.php` | `Modify` | Pastikan seluruh assertions untuk elemen judul, hero banner, selector bulan, dan tombol aksi tetap lulus 100%. |

---

## 5. Ilustrasi Perubahan Tata Letak (Mockup ASCII)

### Sebelum (Tampilan Saat Ini - Gambar 1):
```
┌────────────────────────────────────────────────────────────────────────┐
│ Bulan & kuota                               [ Ubah bulan ] [ + Buat ]  │
│ Atur bulan pendaftaran dan kuota magang...                             │
├────────────────────────────────────────────────────────────────────────┤
│ 📅 Bulan Magang: [ November 2026 (Aktif)                           v ] │
├────────────────────────────────────────────────────────────────────────┤
│ [ KOTAKAN BIRU POLOS (Hero Banner) ]                                   │
│  - Bulan Aktif: November 2026 • Dibuka                                 │
│  - Tanggal buka / tutup / mulai              [ Kuota 15/30 ]           │
├────────────────────────────────────────────────────────────────────────┤
│ Kuota per bidang                                    [ + Tambah kuota ] │
│ [ Tabel Kuota per Bidang... ]                                          │
└────────────────────────────────────────────────────────────────────────┘
```

### Sesudah (Tata Letak Baru - Sesuai Gambar 2):
```
┌────────────────────────────────────────────────────────────────────────┐
│ Bulan & kuota                                                          │
│ Atur bulan pendaftaran dan kuota magang per bidang.                    │
├────────────────────────────────────────────────────────────────────────┤
│ ┌────────────────────────────────────────────────────────────────────┐ │
│ │ 🎨 KOTAKAN BIRU DENGAN BACKGROUND GAMBAR RESMI (Gambar 2)          │ │
│ │  BULAN AKTIF                                                       │ │
│ │  November 2026  • Dibuka                                           │ │
│ │  📅 Buka: 01 Okt 2026   📅 Tutup: 25 Okt 2026   👥 Mulai: 01 Nov   │ │
│ │                                             ┌────────────────────┐ │ │
│ │                                             │ Kuota terisi       │ │ │
│ │                                             │ 15 / 30 peserta    │ │ │
│ │                                             │ ── Ditutup 5 hari  │ │ │
│ │                                             └────────────────────┘ │ │
│ └────────────────────────────────────────────────────────────────────┘ │
├────────────────────────────────────────────────────────────────────────┤
│ ┌────────────────────────────────────────────────────────────────────┐ │
│ │ 📅 Bulan Magang: [ Nov 2026 v ]     [ ✏️ Ubah bulan ]  [ ➕ Buat ]  │ │
│ └────────────────────────────────────────────────────────────────────┘ │
├────────────────────────────────────────────────────────────────────────┤
│ Kuota per bidang                                    [ + Tambah kuota ] │
│ [ Tabel Kuota per Bidang... ]                                          │
└────────────────────────────────────────────────────────────────────────┘
```

---

## 6. Langkah Implementasi Terstruktur

- [x] **Fase 1: Update Styling CSS Kotakan Biru (`.hero-banner-blue`)**
  - [x] Ubah properti `background` pada kelas `.hero-banner-blue` di `resources/views/admin/kuota/index.blade.php`:
    ```css
    background: linear-gradient(95deg, rgba(6, 65, 204, 0.96) 0%, rgba(6, 65, 204, 0.88) 45%, rgba(6, 65, 204, 0.38) 100%),
                url('{{ asset('images/dashboard_hero_bg.jpg') }}') right center / cover no-repeat;
    ```
  - [x] Tambahkan efek shadow halus dan pseudo-element radial highlight agar identik dengan kelas `.adm-hero` pada Dashboard Gambar 2.
- [x] **Fase 2: Restrukturisasi Urutan Elemen Blade View**
  - [x] Pindahkan blok Hero Banner (`.hero-banner-blue`) ke atas, langsung setelah header halaman (atau sebagai elemen teratas).
  - [x] Rancang baris kontrol terpadu (`.adm-control-bar` / `.month-switch-strip`) di bawah kotakan biru yang menggabungkan:
    - Sisi Kiri: Dropdown pemilihan bulan magang lengkap dengan ikon kalender.
    - Sisi Kanan: Tombol aksi `Ubah bulan` (dengan `openUbahBulanModal()`) dan `+ Buat bulan baru` (dengan `openBuatBulanModal()`).
  - [x] Pastikan penataan tombol responsif pada tampilan mobile (otomatis membungkus rapi dengan flex-wrap).
- [x] **Fase 3: Verifikasi Pengujian & Tampilan**
  - [x] Jalankan pengujian otomatis:
    `php artisan test --filter=AdminKuotaRevampTest`
  - [x] Jalankan pengujian regresi:
    `php artisan test --filter=AdminTerminologyDesignSystemTest`
  - [x] Format kode menggunakan Pint: `vendor\bin\pint`.

---

## 7. Strategi Testing

1. **Feature Test (`AdminKuotaRevampTest`)**:
   - `test_kuota_page_displays_redesigned_header_and_hero_banner`: Memverifikasi judul "Bulan & kuota", teks "Bulan Aktif", tanggal buka/tutup/mulai, tombol "Ubah bulan", dan "Buat bulan baru" tetap ter-render secara utuh.
   - `test_modal_ubah_bulan_matches_buat_bulan_design`: Memastikan interaksi pembukaan modal Ubah Bulan dari tombol baru tetap berjalan normal.
   - `test_kuota_modals_exist_in_dom`: Memastikan seluruh modal popup tetap hadir di DOM.
2. **Visual Inspection**:
   - Memeriksa tampilan di browser memastikan background gambar ter-load dengan baik, teks terbaca jelas (kontras tinggi), dan posisi tombol sejajar rapi di bawah kotakan biru.

---

## 8. Risiko & Mitigasi

- **Risiko**: Background gambar tidak muncul jika path aset salah.
  - **Mitigasi**: Gunakan helper Laravel resmi `asset('images/dashboard_hero_bg.jpg')` yang sudah terbukti bekerja di Dashboard Admin.
- **Risiko**: Penumpukan tombol pada layar sempit/mobile.
  - **Mitigasi**: Terapkan `display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem;` pada baris kontrol sub-bar.

---

## 9. Kriteria Selesai (Definition of Done)

- [x] Kotakan biru (Hero Banner) berada di atas informasi kuota per bidang dengan background gambar pattern resmi Diskominfo yang sama persis dengan Gambar 2.
- [x] Tombol `Ubah bulan` dan `+ Buat bulan baru` beserta dropdown selector `Bulan Magang:` berada di bawah kotakan biru dalam satu baris kontrol yang rapi.
- [x] Seluruh fungsi interaktif (modal ubah bulan, modal buat bulan baru, switch bulan dropdown) tetap berjalan 100% normal.
- [x] Seluruh tes otomatis di `AdminKuotaRevampTest` dan `AdminTerminologyDesignSystemTest` lulus 100%.
- [x] Tidak ada perubahan istilah terlarang (tetap menggunakan "Bulan", bebas dari kata "Periode").
