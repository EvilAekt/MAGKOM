# Rencana Kerja: Relokasi Tombol Navigasi dan Batal Pengajuan ke Atas Hero Banner

## 1. Konteks & Permasalahan
Pada tampilan saat ini di halaman **Detail & Berkas Pendaftaran Magang** (`resources/views/peserta/pendaftaran/detail.blade.php`), tombol aksi:
1. **"← Kembali ke Riwayat Pendaftaran"** (navigasi breadcrumb)
2. **"⊗ Batalkan Pengajuan"** (tindakan aksi pendaftaran)

diletakkan di baris paling atas di dalam kotak **Hero Banner Biru** (`.hero-top-nav`).

### Masalah Desain & Ergonomi UX:
- **Pencampuran Peran UI**: Tombol navigasi rute halaman (*breadcrumb*) dan tombol pembatalan aksi seharusnya berada di level bar navigasi halaman (*page action bar*), bukan terperangkap di dalam kartu hero yang berlatar belakang gambar gedung.
- **Kepadatan Hero Banner**: Adanya tombol di dalam banner membuat hero banner menjadi terlalu tinggi vertikal dan memakan ruang sebelum pengguna melihat nomor registrasi dan periode magang.
- **Visibilitas Tombol Batal**: Aksi pembatalan pengajuan memiliki warna merah transparan kaca (*glassmorphism*) yang kurang kontras di atas gradient biru hero banner.

---

## 2. Solusi Desain yang Diusulkan

### A. Memindahkan Kedua Tombol ke Luar (Di Atas Hero Banner)
Membuat baris navigasi atas bersih (`.detail-top-bar`) tepat sebelum elemen `.detail-hero`:
```html
<div class="detail-top-bar">
    <a href="{{ route('peserta.pendaftaran.index') }}" class="btn-top-back">
        <svg>...</svg> Kembali ke Riwayat Pendaftaran
    </a>

    @if($bisaBatal)
        <button type="button" class="btn-top-cancel" onclick="openCancelModal()">
            <svg>...</svg> Batalkan Pengajuan
        </button>
    @endif
</div>
```

### B. Styling Sesuai Desain Sistem Modern MagKom
1. **Tombol Kembali (`.btn-top-back`)**:
   - Bentuk kapsul pill bersih (`rounded-full`).
   - Latar belakang putih dengan border abu-abu netral halus (`#E2E8F0`) dan shadow mikro (`0 1px 2px rgba(0,0,0,0.04)`).
   - Efek hover: latar biru muda (`#EFF6FF`), teks biru MagKom (`var(--blue-600)`), dan animasi geser halus ke kiri `-2px`.
2. **Tombol Batalkan Pengajuan (`.btn-top-cancel`)**:
   - Bentuk kapsul pill elegan beraksen merah lembut (`#FEF2F2` dengan border `#FECACA` dan teks `#DC2626`).
   - Efek hover: latar `#FEE2E2` dengan aksen border merah tegas, jelas sebagai aksi penting tanpa mengotori hero banner.
3. **Hero Banner Menjadi Lebih Bersih**:
   - Hapus `.hero-top-nav` dari dalam `.detail-hero`.
   - Hero banner langsung menampilkan:
     - Nomor Registrasi (`#REG-xxxxx`)
     - Judul Periode Magang
     - Meta Pills (Jurusan, Durasi, Tanggal Dibuat)
     - Status Badge di sebelah kanan.
   - Tinggi hero banner menjadi lebih ramping, elegan, dan estetik.

---

## 3. File yang Dimodifikasi
1. `resources/views/peserta/pendaftaran/detail.blade.php`:
   - Penambahan style CSS `.detail-top-bar`, `.btn-top-back`, `.btn-top-cancel`.
   - Pemindahan markup tombol ke atas elemen `.detail-hero`.
   - Pembersihan markup `.hero-top-nav` yang lama dari dalam `.detail-hero`.

---

## 4. Pengujian & Verifikasi
1. **Verifikasi Tampilan Responsif**:
   - Di layar desktop: Tombol kembali di pojok kiri atas dan tombol batal di pojok kanan atas, sejajar di atas hero banner.
   - Di layar mobile: Tetap rapi dengan `flex-wrap: wrap` dan jarak yang proporsional.
2. **Verifikasi Fungsionalitas**:
   - Klik "Kembali ke Riwayat Pendaftaran" mengarah ke `/peserta/pendaftaran`.
   - Klik "Batalkan Pengajuan" tetap membuka modal konfirmasi pembatalan pendaftaran dengan normal.
   - Tombol batal hanya muncul jika status pendaftaran memenuhi syarat `$bisaBatal` (`diajukan`, `berkas_belum_lengkap`, dll.).
3. **Automated Test Check**:
   - Jalankan `tests/Feature/PesertaPendaftaranFlowTest.php` untuk memastikan seluruh alur pendaftaran (termasuk pembatalan) tetap 100% PASS.
