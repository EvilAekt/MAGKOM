# Rencana Kerja: Harmonisasi Desain Halaman Kebijakan Privasi & Pembersihan Footer Navigasi

**Tanggal**: 17 November 2026  
**Status**: Menunggu Persetujuan Pengguna (Pending User Approval)  
**Terkait**: Penyelarasan Tema Header, Footer, Card, dan Tombol Halaman Kebijakan Privasi dengan Tema Utama MagKom

---

## 1. Tujuan
1. Menghapus 4 tautan navigasi di footer halaman Kebijakan Privasi (`resources/views/public/kebijakan-privasi.blade.php`), yaitu:
   - `Beranda` (`/`)
   - `Peta Alur` (`/panduan-alur`)
   - `Verifikasi Dokumen` (`/verifikasi-dokumen/cek`)
   - `Login` (`/login`)
   sehingga footer hanya menampilkan informasi hak cipta yang bersih dan terpusat.
2. Menyelaraskan skema warna **Header** dan **Footer** halaman Kebijakan Privasi agar identik dengan Landing Page MagKom:
   - **Header**: Menggunakan latar belakang Navy MagKom (`#1B3860`), teks putih/slate-300, logo Sleman berbingkai halus, tombol "Beranda Utama" translucent, dan tombol CTA "Lihat Kuota" beraksen emas MagKom.
   - **Footer**: Menggunakan latar belakang Navy Gelap (`#122846`), border atas tipis `rgba(255, 255, 255, 0.08)`, dan teks copyright `text-slate-400`.
3. Menyesuaikan warna **Card Utama Kebijakan Privasi** dan elemen pendukungnya:
   - **Header Card**: Mempertegas gradasi Navy MagKom (`#122846` ke `#1B3860` ke `#003566`) dengan badge UU PDP beraksen emas (`bg-amber-400/20 text-amber-300 border-amber-400/30`).
   - **Tab Switcher**: Tombol tab aktif menggunakan teks primer `#003566` / navy tebal, tab non-aktif dengan hover navy lembut.
   - **Tombol Navigasi Cepat (CTA Box Bawah)**: Tombol "Unduh PDF Panduan" dan "Pilih Kuota Magang" disesuaikan dengan token warna tema MagKom (Navy `#003566` dan Emas `#F59E0B`).

---

## 2. Asumsi & Batasan
1. **Asumsi**:
   - Penghapusan 4 tautan navigasi pada footer kebijakan privasi tidak memengaruhi SEO maupun routing utama karena halaman ini adalah halaman syarat & ketentuan statis yang selalu memiliki tombol kembali ke beranda di header dan breadcrumb di atas card.
   - Tidak ada pengujian otomatis yang bergantung pada 4 link footer di halaman kebijakan privasi (diverifikasi melalui `tests/Feature/AuthSecurityAndPolicyTest.php`).
2. **Batasan**:
   - Menjaga seluruh teks isi pasal kebijakan privasi dan syarat ketentuan UU PDP tetap utuh tanpa modifikasi konten hukum.
   - Menggunakan utilitas Tailwind CSS yang dikelola via `public-tailwind.css` dan Vite asset pipeline.

---

## 3. Pendekatan Desain & Arsitektur
Dua alternatif pendekatan visual:

- **Opsi A (Rekomendasi - Keselarasan Penuh dengan Landing Page MagKom)**:
  - Header halaman menggunakan warna Navy `#1B3860` dengan teks putih dan tombol aksen emas/translucent.
  - Card header banner menggunakan gradasi Navy MagKom konsisten dengan badge keemasan Sleman.
  - Footer halaman menggunakan Navy Gelap `#122846` dengan copyright centered dan 4 link dihapus.
  - Tombol-tombol interaktif (tab switcher dan CTA box) menggunakan palet Navy `#003566` dan Emas `#F59E0B`.
  - *Kelebihan*: Konsistensi visual 100% antara Landing Page dan halaman Kebijakan Privasi. Identitas instansi Diskominfo Sleman terasa menyatu.

- **Opsi B (Light Theme Minimalis)**:
  - Mempertahankan header putih tetapi hanya mengubah card header dan tombol menjadi navy.
  - *Kekurangan*: Tidak memenuhi instruksi pengguna yang secara eksplisit meminta warna header dan footer disamakan dengan landing page.

**Rekomendasi**: Memilih **Opsi A**.

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/public/kebijakan-privasi.blade.php` | Modify | 1. Ubah header ke tema Navy `#1B3860` & tombol aksen emas/translucent.<br>2. Harmonisasi card header banner, badge emas, tab switcher, dan bottom CTA.<br>3. Hapus 4 link navigasi footer (`[Beranda]`, `[Peta Alur]`, `[Verifikasi Dokumen]`, `[Login]`) dan ubah latar footer ke Navy Gelap `#122846`. |
| `resources/js/pages/public/kebijakan-privasi.js` | Modify | Sinkronisasi class active/inactive tab switcher agar menggunakan warna primer Navy MagKom (`text-[#003566]`). |

---

## 5. Rincian Perubahan Kode

### A. Header Halaman (`<header>`):
```html
<header class="bg-[#1B3860] border-b border-white/10 shadow-md sticky top-0 z-30 transition-colors">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 py-3.5 flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <a href="{{ route('landing') }}" class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center p-1 border border-white/15 shadow-sm hover:bg-white/20 transition-all">
                @include('partials.logo-sleman', ['class' => 'w-full h-full object-contain', 'width' => '32', 'height' => '32'])
            </a>
            <div>
                <a href="{{ route('landing') }}" class="font-bold text-sm sm:text-base text-white leading-tight hover:text-amber-300 transition-colors">
                    Dinas Komunikasi dan Informatika
                </a>
                <p class="text-xs text-slate-300 font-medium">Pemerintah Kabupaten Sleman &bull; Portal MagKom</p>
            </div>
        </div>
        
        <div class="flex items-center space-x-2">
            <a href="{{ route('landing') }}" class="inline-flex items-center text-xs font-semibold text-slate-200 hover:text-white bg-white/10 hover:bg-white/20 px-3 py-2 rounded-xl border border-white/15 transition-all">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                <span>Beranda Utama</span>
            </a>
            <a href="{{ route('landing') }}#kuota" class="hidden sm:inline-flex items-center text-xs font-bold text-slate-900 bg-amber-400 hover:bg-amber-500 px-3.5 py-2 rounded-xl shadow-sm transition-all">
                <span>Lihat Kuota</span>
                <svg class="w-3.5 h-3.5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"></polyline></svg>
            </a>
        </div>
    </div>
</header>
```

### B. Header Card Kebijakan Privasi:
```html
<div class="bg-gradient-to-r from-[#122846] via-[#1B3860] to-[#003566] px-6 sm:px-10 py-8 text-white relative overflow-hidden">
    <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-amber-400/20 border border-amber-400/30 text-amber-300 text-xs font-semibold uppercase tracking-wider mb-3">
        <svg class="w-3.5 h-3.5 text-amber-400" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
        </svg>
        <span>{{ $kebijakanBadge }}</span>
    </div>
    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white">{{ $kebijakanTitle }}</h1>
    <p class="text-xs sm:text-sm text-slate-300 mt-2">
        {{ $kebijakanUpdatedAt }}
    </p>
</div>
```

### C. Tab Switcher & CTA Box Bawah:
- Tab aktif: `bg-white text-[#003566] shadow-sm border border-slate-200 font-bold`
- CTA Box Bawah:
  - Tombol "Unduh PDF Panduan": `px-3.5 py-2 text-xs font-semibold text-[#003566] bg-white border border-slate-300 rounded-xl hover:bg-slate-50 transition-colors`
  - Tombol "Pilih Kuota Magang →": `px-4 py-2 text-xs font-bold text-slate-900 bg-amber-400 hover:bg-amber-500 rounded-xl shadow-md transition-all`

### D. Footer Halaman (`<footer>`):
```html
<footer class="border-t border-white/10 bg-[#122846] py-6 text-center text-xs text-slate-400">
    <div class="max-w-5xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-center">
        <p>&copy; {{ date('Y') }} Dinas Komunikasi dan Informatika Kabupaten Sleman. Seluruh hak cipta dilindungi.</p>
    </div>
</footer>
```
*(4 link `[Beranda]`, `[Peta Alur]`, `[Verifikasi Dokumen]`, `[Login]` telah dihapus secara menyeluruh).*

---

## 6. Langkah Implementasi
- [ ] 1. Edit [kebijakan-privasi.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/public/kebijakan-privasi.blade.php):
  - [ ] Perbarui warna dan styling `<header>` ke Navy MagKom (`#1B3860`) dengan tombol Beranda & Kuota yang harmonis.
  - [ ] Perbarui banner card header dengan gradasi Navy MagKom dan badge emas.
  - [ ] Perbarui styling tab switcher dan CTA box bawah ke token tema MagKom.
  - [ ] Hapus 4 link footer dan ubah warna `<footer>` ke Navy Gelap (`#122846`) dengan teks copyright centered.
- [ ] 2. Edit [kebijakan-privasi.js](file:///d:/MAGKOM/MAGKOM/resources/js/pages/public/kebijakan-privasi.js):
  - [ ] Sinkronisasi class JS untuk active tab state (`text-[#003566]`).
- [ ] 3. Kompilasi aset frontend via Vite:
  - [ ] Jalankan `npm run build` untuk memproses class Tailwind dan JS bundle baru.
- [ ] 4. Verifikasi dan Quality Gates:
  - [ ] Jalankan `vendor/bin/pint --test` untuk memastikan kepatuhan gaya kode.
  - [ ] Jalankan `artisan test --filter=AuthSecurityAndPolicyTest` untuk memastikan halaman kebijakan privasi tetap lulus tes.
  - [ ] Periksa respons visual pada viewport desktop dan mobile.

---

## 7. Strategi Testing
- **Feature Tests**:
  - `php artisan test --filter=AuthSecurityAndPolicyTest` -> Memastikan status HTTP 200, view name, dan pasal-pasal utama tetap dapat diakses publik.
  - `php artisan test --filter=SecurityHeadersProductionTest` -> Memastikan security headers pada `/kebijakan-privasi` tetap aktif.
- **Visual Check**:
  - Memastikan warna Header `#1B3860` dan Footer `#122846` selaras persis dengan Landing Page.
  - Memastikan 4 link footer sudah hilang dan teks hak cipta terpusat rapi.
  - Memastikan tombol-tombol memiliki kontras tinggi (WCAG AA).

---

## 8. Kriteria Selesai (Definition of Done)
- [ ] 4 tautan navigasi di footer halaman kebijakan privasi terhapus tanpa sisa.
- [ ] Warna header dan footer halaman kebijakan privasi selaras dengan tema Landing Page MagKom.
- [ ] Card header dan tombol-tombol menggunakan token warna utama MagKom (Navy `#003566` / `#1B3860` dan Emas `#F59E0B`).
- [ ] `npm run build` berhasil tanpa error.
- [ ] `vendor/bin/pint --test` lulus 100%.
- [ ] Seluruh test suite terkait lulus hijau.

