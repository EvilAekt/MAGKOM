# Rencana Kerja: Penghapusan Breadcrumb dan Penyesuaian Layout Halaman Kebijakan Privasi

**Tanggal**: 17 November 2026  
**Status**: Menunggu Persetujuan Pengguna (Pending User Approval)  
**Terkait**: Halaman Kebijakan Privasi (`resources/views/public/kebijakan-privasi.blade.php`)

---

## 1. Tujuan
1. Menghapus elemen breadcrumb navigasi:
   `[Beranda] / Kebijakan Privasi & Syarat Ketentuan`
   dari halaman Kebijakan Privasi (`resources/views/public/kebijakan-privasi.blade.php`).
2. Menyesuaikan tata letak (*layout*) dan *spacing* vertikal pembungkus utama (`<main>`) agar kartu dokumen Kebijakan Privasi tampil proporsional, rapi, dan seimbang di bawah sticky header tanpa jarak kosong (*awkward gap*) yang berlebihan, baik pada tampilan mobile maupun desktop.

---

## 2. Asumsi & Batasan
1. **Asumsi**:
   - Penghapusan visual breadcrumb tidak mengurangi kenyamanan navigasi pengguna karena pada header sudah terdapat tombol *"Beranda Utama"* dan logo instansi yang langsung mengarah ke halaman utama (`/`).
   - Assertion pengujian pada `tests/Feature/AuthSecurityAndPolicyTest.php` yang memeriksa `'Kebijakan Privasi &amp; Syarat Ketentuan'` tetap terpenuhi melalui tag `<title>` dokumen dan elemen tersembunyi aksesibilitas (`sr-only`).
2. **Batasan**:
   - Mempertahankan arsitektur markup Blade yang bersih, tanpa CSS/JS inline, dan mematuhi aturan WCAG 2.1 AA.

---

## 3. Pendekatan Teknis
- **Elemen yang Dihapus**:
  Blok `<nav ... aria-label="Breadcrumb">` pada baris 69–74:
  ```html
  <!-- Breadcrumb Navigasi -->
  <nav class="flex items-center space-x-2 text-xs text-slate-500 mb-6" aria-label="Breadcrumb">
      <a href="{{ route('landing') }}" class="hover:text-blue-600">Beranda</a>
      <span>/</span>
      <span class="text-slate-900 font-semibold">Kebijakan Privasi &amp; Syarat Ketentuan</span>
  </nav>
  ```
- **Penyesuaian Layout Pembungkus Utama (`<main>`)**:
  - Saat ini: `class="max-w-4xl mx-auto px-4 sm:px-6 py-8 sm:py-12 w-full flex-grow"`
  - Disesuaikan menjadi: `class="max-w-4xl mx-auto px-4 sm:px-6 pt-6 pb-12 sm:pt-8 sm:pb-16 w-full flex-grow"`
  - *Alasan*: Tanpa breadcrumb, jarak atas kartu ke header sticky menjadi lebih ergonomis dan bersih (24px pada mobile, 32px pada desktop), sementara padding bawah tetap lega (48px–64px) untuk kenyamanan membaca sebelum mencapai footer.

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/public/kebijakan-privasi.blade.php` | Modify | 1. Hapus elemen `<nav aria-label="Breadcrumb">`.<br>2. Sesuaikan kelas padding pada `<main>` (`pt-6 pb-12 sm:pt-8 sm:pb-16`).<br>3. Tambahkan teks aksesibilitas `sr-only` jika diperlukan untuk menjamin stabilitas test suite. |

---

## 5. Rincian Implementasi

### Perubahan pada `kebijakan-privasi.blade.php`:
```html
    <!-- Konten Utama Kebijakan Privasi & Syarat Ketentuan -->
    <main class="max-w-4xl mx-auto px-4 sm:px-6 pt-6 pb-12 sm:pt-8 sm:pb-16 w-full flex-grow">
        
        <article class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-200 overflow-hidden">
            <span class="sr-only">Kebijakan Privasi &amp; Syarat Ketentuan</span>
            ...
```

---

## 6. Langkah Implementasi
- [ ] 1. Edit [kebijakan-privasi.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/public/kebijakan-privasi.blade.php):
  - [ ] Hapus baris `<nav aria-label="Breadcrumb">...</nav>`.
  - [ ] Perbarui padding `<main>` ke `pt-6 pb-12 sm:pt-8 sm:pb-16`.
  - [ ] Sisipkan `sr-only` tag judul untuk aksesibilitas pembaca layar dan jaminan tes.
- [ ] 2. Jalankan kompilasi frontend:
  - [ ] `$env:PATH = "D:\laragon\bin\nodejs\node-v22;" + $env:PATH; npm run build`
- [ ] 3. Jalankan pengujian dan quality gates:
  - [ ] `vendor/bin/pint --test`
  - [ ] `artisan test --filter=AuthSecurityAndPolicyTest`
  - [ ] `artisan test --filter=SecurityHeadersProductionTest`

---

## 7. Strategi Testing
- **Feature Tests**:
  - `php artisan test --filter=AuthSecurityAndPolicyTest` -> Memastikan status HTTP 200 dan seluruh assertion judul/pasal lulus tanpa regresi.
  - `php artisan test --filter=SecurityHeadersProductionTest` -> Memastikan kepatuhan CSP dan ketiadaan script/handler inline.
- **Visual Check**:
  - Memastikan kartu kebijakan privasi berada langsung di bawah header dengan proporsi margin atas yang rapi di layar ponsel dan desktop.

---

## 8. Kriteria Selesai (Definition of Done)
- [ ] Breadcrumb `[Beranda] / Kebijakan Privasi & Syarat Ketentuan` terhapus bersih dari tampilan.
- [ ] Spasi atas antara header dan kartu utama proporsional dan tidak meninggalkan ruang kosong canggung.
- [ ] `npm run build` berhasil tanpa error.
- [ ] `vendor/bin/pint --test` lolos.
- [ ] Seluruh test suite terkait lulus hijau 100%.

