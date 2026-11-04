# Rencana Kerja: Standardisasi Paginasi & Pengatur Jumlah Data Per Halaman (Per-Page Selector) Tabel Admin

**Tanggal Dokumen**: 2026-11-04  
**Modul**: Administrasi MagKom — Pendaftaran, Waiting List, Penempatan, dan Laporan  
**Status**: ✅ Selesai Diimplementasikan & Terverifikasi (Siap Review)  

---

## 1. Tujuan & Analisis Kebutuhan

Berdasarkan permintaan pengguna dan tangkapan layar referensi (`media_1793766035232_9f6e5ca2.png`), kita akan mengintegrasikan sistem paginasi 2 baris terstandarisasi yang dilengkapi **pengatur jumlah baris per halaman (*per-page selector*)** dan **navigasi lembar halaman (*sheet navigation*)** pada 4 kartu/tabel operasional Admin:

1. **Card "Daftar Permohonan Magang Masuk"** (`resources/views/admin/pendaftaran/index.blade.php`):
   - Mengubah kuota baris default menjadi **per 5** (sebelumnya hardcoded 25).
   - Menambahkan tombol pengatur per halaman: `[ 5 ] [ 10 ] [ 25 ]` (dengan nilai 5 aktif secara default).
2. **Card "Daftar Antrean Waiting List Calon Peserta"** (`resources/views/admin/waiting-list/index.blade.php`):
   - Mengubah kuota baris default menjadi **per 10** (sebelumnya hardcoded 20).
   - Menambahkan tombol pengatur per halaman: `[ 10 ] [ 20 ] [ 50 ]` (dengan nilai 10 aktif secara default).
3. **Card "Data Penempatan Peserta"** (`resources/views/admin/penempatan/index.blade.php`):
   - Mengubah kuota baris default menjadi **per 5** (sebelumnya hardcoded 15).
   - Menambahkan tombol pengatur per halaman: `[ 5 ] [ 10 ] [ 25 ]` (dengan nilai 5 aktif secara default).
4. **Card "Laporan Rekapitulasi Pendaftaran"** (`resources/views/admin/laporan/index.blade.php`):
   - Mengubah kuota baris default menjadi **per 5** (sebelumnya hardcoded 10).
   - Menambahkan tombol pengatur per halaman: `[ 5 ] [ 10 ] [ 25 ]` (dengan nilai 5 aktif secara default).

**Prinsip Desain**: Menyesuaikan dan mengonsistensikan seluruh elemen agar 100% selaras dengan standar visual Gentelella / Design System MagKom Sleman (identik dengan referensi Gambar 2).

---

## 2. Asumsi Kerja & Penegasan Arsitektur

1. **Server-Side Pagination Berbasis Eloquent**:
   - Seluruh paginasi berjalan di lapisan database MySQL melalui metode Eloquent `paginate($perPage)->withQueryString()`.
   - Ini menjaga efisiensi konsumsi memori PHP dan kecepatan respon web, terutama saat jumlah pelamar mencapai ratusan atau ribuan data.
2. **Whitelist Sanitasi Parameter `per_page`**:
   - Nilai `per_page` yang diterima dari URL divalidasi terhadap array whitelist ketat (misal `[5, 10, 25, 50]`) guna mencegah serangan *resource exhaustion* / query abuse. Jika nilai tidak valid, otomatis kembali ke nilai default.
3. **Preservasi Filter Saat Berpindah Halaman/Ukuran**:
   - Menggunakan `request()->fullUrlWithQuery(['per_page' => $size, 'page' => 1])` sehingga seluruh parameter pencarian dan filter aktif (`bulan_magang_id`, `jurusan_id`, `status`, `q`, dll.) tetap terjaga utuh.
4. **Komponen Reusable `<x-gentelella.pagination-bar>`**:
   - Menghindari duplikasi markup HTML dan CSS ad-hoc di 4 view Blade yang berbeda, menjamin *single source of truth*.

---

## 3. Evaluasi Pendekatan Teknis (Perbandingan Opsi)

| Parameter | Opsi A: Reusable Blade Component + Server-Side Paginate (Sangat Direkomendasikan) | Opsi B: Duplikasi Markup Manual di Setiap File Blade | Opsi C: Client-Side JavaScript Slicing (DOM Table Paging) |
| :--- | :--- | :--- | :--- |
| **Deskripsi** | Membuat komponen `<x-gentelella.pagination-bar>` yang menerima instance paginator Laravel dan merender footer 2 baris terstandarisasi. | Menyalin blok HTML dan script paginasi ke 4 file view berbeda. | Merender seluruh data ke tabel HTML lalu memotong tampilan per baris via JS. |
| **Konsistensi Visual** | **100% Identik**: Semua kartu menggunakan kelas CSS dan struktur grid yang sama persis. | **Rentan Inkonsisten**: Rentan terjadi perbedaan styling atau lupa update saat ada revisi. | Sama seperti Opsi A, namun berat di client. |
| **Kinerja & Skalabilitas** | **Tinggi (O(1) payload)**: Hanya data yang ditampilkan yang ditransfer dari database. | Sama seperti Opsi A. | **Buruk**: Membengkakkan ukuran HTML dan memperlambat browser pada data besar. |
| **Kepatuhan AGENTS.md** | **Sangat Patuh**: Perubahan di Blade minimalis (hanya 1 baris pemanggilan komponen). | Melanggar aturan pemeliharaan markup bersih. | Memerlukan JavaScript kompleks di luar standar. |

> **Rekomendasi**: Mengadopsi **Opsi A**.

---

## 4. Rincian Desain Komponen `<x-gentelella.pagination-bar>`

Struktur visual komponen mengikuti referensi Gambar 2 (`media_1793766035232_9f6e5ca2.png`):

```blade
<div class="kuota-pagination-wrapper adm-pagination-wrapper">
    {{-- BARIS 1: Info Counter (Kiri) & Tombol Pilihan Menampilkan (Kanan) --}}
    <div class="kuota-pagination-row kuota-pagination-top">
        <div class="kuota-pagination-summary">
            <span class="kuota-pagination-counter">
                Menampilkan <strong>{{ $paginator->firstItem() ?? 0 }}</strong> - <strong>{{ $paginator->lastItem() ?? 0 }}</strong> dari <strong>{{ number_format($paginator->total()) }}</strong> {{ $entity }}
            </span>
            @if($badgeText)
                <span class="kuota-immutable-pill">
                    <x-sa.icon name="check-circle" :size="13" />
                    <span>{{ $badgeText }}</span>
                </span>
            @endif
        </div>

        <div class="kuota-per-page-container">
            <span class="kuota-per-page-label">Menampilkan:</span>
            <div class="kuota-per-page-group" role="group" aria-label="Pilih jumlah data per halaman">
                @foreach($perPageOptions as $size)
                    <a href="{{ request()->fullUrlWithQuery(['per_page' => $size, 'page' => 1]) }}"
                       class="kuota-per-page-pill {{ $currentPerPage == $size ? 'active' : '' }}"
                       title="Tampilkan {{ $size }} {{ $entity }} per halaman">
                        {{ $size }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    {{-- BARIS 2: Info Lembar Halaman (Kiri) & Navigasi Prev/Next/Numbers (Kanan) --}}
    <div class="kuota-pagination-row kuota-pagination-bottom">
        <div class="kuota-pagination-lembar-info">
            Lembar Halaman <strong>{{ $currentPage }}</strong> dari <strong>{{ $lastPage }}</strong>
        </div>

        <div class="kuota-pagination-right">
            <div class="kuota-page-controls" role="navigation" aria-label="Navigasi Lembar Halaman">
                {{-- Tombol Sebelumnya --}}
                @if($paginator->onFirstPage())
                    <button type="button" class="kuota-page-btn-nav disabled" disabled aria-label="Lembar Sebelumnya">&laquo; Prev</button>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" class="kuota-page-btn-nav" aria-label="Lembar Sebelumnya">&laquo; Prev</a>
                @endif

                {{-- Angka Halaman (Sliding Window) --}}
                <div class="kuota-page-numbers">
                    @for($page = $startPage; $page <= $endPage; $page++)
                        @if($page == $currentPage)
                            <span class="kuota-page-btn-num active" aria-current="page">{{ $page }}</span>
                        @else
                            <a href="{{ $paginator->url($page) }}" class="kuota-page-btn-num">{{ $page }}</a>
                        @endif
                    @endfor
                </div>

                {{-- Tombol Selanjutnya --}}
                @if($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" class="kuota-page-btn-nav" aria-label="Lembar Selanjutnya">Next &raquo;</a>
                @else
                    <button type="button" class="kuota-page-btn-nav disabled" disabled aria-label="Lembar Selanjutnya">Next &raquo;</button>
                @endif
            </div>
        </div>
    </div>
</div>
```

---

## 5. Rencana Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :---: | :--- |
| `resources/views/components/gentelella/pagination-bar.blade.php` | **Create** | Komponen Blade reusable untuk pagination bar 2 baris dengan per-page selector pills & navigasi lembar halaman. |
| `app/Http/Controllers/Admin/PendaftaranController.php` | **Modify** | Parameter `per_page` whitelist `[5, 10, 25, 50]` dengan nilai default **5**. |
| `app/Http/Controllers/Admin/WaitingListController.php` | **Modify** | Parameter `per_page` whitelist `[10, 20, 50]` dengan nilai default **10**. |
| `app/Http/Controllers/Admin/PenempatanController.php` | **Modify** | Parameter `per_page` whitelist `[5, 10, 25, 50]` dengan nilai default **5**. |
| `app/Http/Controllers/Admin/LaporanController.php` | **Modify** | Parameter `per_page` whitelist `[5, 10, 25, 50]` dengan nilai default **5** pada tab pendaftaran. |
| `resources/views/admin/pendaftaran/index.blade.php` | **Modify** | Ganti `{{ $pendaftaranList->links() }}` dengan `<x-gentelella.pagination-bar :paginator="$pendaftaranList" :perPageOptions="[5, 10, 25]" entity="berkas pendaftaran" badgeText="Pendaftaran Terverifikasi" />`. |
| `resources/views/admin/waiting-list/index.blade.php` | **Modify** | Ganti blok paginasi lama dengan `<x-gentelella.pagination-bar :paginator="$waitingList" :perPageOptions="[10, 20, 50]" entity="antrean peserta" badgeText="Antrean FIFO" />`. |
| `resources/views/admin/penempatan/index.blade.php` | **Modify** | Ganti `{{ $penempatanList->links() }}` dengan `<x-gentelella.pagination-bar :paginator="$penempatanList" :perPageOptions="[5, 10, 25]" entity="peserta magang" badgeText="Penempatan Aktif" />`. |
| `resources/views/admin/laporan/index.blade.php` | **Modify** | Ganti `{{ $pendaftaranData->links() }}` dengan `<x-gentelella.pagination-bar :paginator="$pendaftaranData" :perPageOptions="[5, 10, 25]" entity="data rekapitulasi" badgeText="Rekapitulasi Berkas" />`. |
| `resources/css/components/pagination.css` | **Modify** | Menambahkan dukungan styling elemen link `<a>` pada `.kuota-per-page-pill` dan `.kuota-page-btn-num` agar serasi dengan button native. |
| `tests/Feature/AdminTablePaginationStandardTest.php` | **Create** | Automated Feature Test untuk memverifikasi default per_page dan fungsionalitas paginasi di 4 tabel. |

---

## 6. Permohonan Izin Perubahan Markup Blade (Sesuai AGENTS.md Bagian 3.3)

> [!IMPORTANT]
> **Alasan Perubahan Blade**:  
> Perubahan ini menggantikan link paginasi mentah bawaan Laravel (`{{ $paginator->links() }}`) yang tidak memiliki kontrol per-halaman menjadi komponen terstruktur `<x-gentelella.pagination-bar>`.
> - **File yang Disentuh**:
>   - `resources/views/components/gentelella/pagination-bar.blade.php` (Komponen baru)
>   - `resources/views/admin/pendaftaran/index.blade.php`
>   - `resources/views/admin/waiting-list/index.blade.php`
>   - `resources/views/admin/penempatan/index.blade.php`
>   - `resources/views/admin/laporan/index.blade.php`
> - **Scope Perubahan**: Hanya mengganti bagian wrapper pagination footer di bawah tabel masing-masing modul. Tidak ada perubahan pada tabel, kolom, form filter, modal, ataupun struktur routing.

---

## 7. Langkah Implementasi (Checklist Bertahap)

- [x] **Langkah 1**: Buat komponen reusable `resources/views/components/gentelella/pagination-bar.blade.php`.
- [x] **Langkah 2**: Perbarui `resources/css/components/pagination.css` untuk memastikan dukungan penuh elemen tautan `<a>` pada pill per-page dan page controls (termasuk Dark Mode).
- [x] **Langkah 3**: Perbarui controller backend:
  - `app/Http/Controllers/Admin/PendaftaranController.php` (default 5, whitelist `[5, 10, 25, 50]`)
  - `app/Http/Controllers/Admin/WaitingListController.php` (default 10, whitelist `[10, 20, 50]`)
  - `app/Http/Controllers/Admin/PenempatanController.php` (default 5, whitelist `[5, 10, 25, 50]`)
  - `app/Http/Controllers/Admin/LaporanController.php` (default 5, whitelist `[5, 10, 25, 50]`)
- [x] **Langkah 4**: Terapkan komponen `<x-gentelella.pagination-bar>` pada 4 file view:
  - `resources/views/admin/pendaftaran/index.blade.php`
  - `resources/views/admin/waiting-list/index.blade.php`
  - `resources/views/admin/penempatan/index.blade.php`
  - `resources/views/admin/laporan/index.blade.php`
- [x] **Langkah 5**: Compile aset frontend melalui `npm run build`.
- [x] **Langkah 6**: Buat automated test `tests/Feature/AdminTablePaginationStandardTest.php` untuk memvalidasi:
  - Pendaftaran memuat per 5 secara default dan merespons `per_page=10`.
  - Waiting List memuat per 10 secara default dan merespons `per_page=20`.
  - Penempatan memuat per 5 secara default dan merespons `per_page=10`.
  - Laporan Rekapitulasi pendaftaran memuat per 5 secara default dan merespons `per_page=10`.
- [x] **Langkah 7**: Jalankan seluruh pengujian:
  - `php artisan test tests/Feature/AdminTablePaginationStandardTest.php tests/Feature/AdminChildPagesVisualTest.php tests/Feature/AdminDashboardEnhancementTest.php`
  - `./vendor/bin/pint --test`

---

## 8. Strategi Testing

1. **Automated Feature Tests**:
   - Memastikan request default tanpa parameter `per_page` menghasilkan paginasi dengan batas yang diminta pengguna (5 atau 10).
   - Memastikan saat parameter `?per_page=25` dikirim, paginator menyesuaikan jumlah per halaman sesuai request.
   - Memastikan saat parameter `per_page` di luar whitelist dikirim (misal `per_page=99999`), sistem fallback secara aman ke default tanpa error 500.
   - Memastikan teks info lembar halaman ("Lembar Halaman 1 dari X") dan tombol navigasi tercetak di respon HTML.
2. **Quality Gate**:
   - `npm run build`: Kompilasi aset Vite 100% sukses.
   - `./vendor/bin/pint --test`: 100% bersih tanpa pelanggaran format kode.

---

## 9. Kriteria Selesai (Definition of Done)

- [x] Tabel **Daftar Permohonan Magang Masuk** memiliki tombol pengatur `[ 5 ] [ 10 ] [ 25 ]` dengan default **5**.
- [x] Tabel **Daftar Antrean Waiting List Calon Peserta** memiliki tombol pengatur `[ 10 ] [ 20 ] [ 50 ]` dengan default **10**.
- [x] Tabel **Data Penempatan Peserta** memiliki tombol pengatur `[ 5 ] [ 10 ] [ 25 ]` dengan default **5**.
- [x] Tabel **Laporan Rekapitulasi Pendaftaran** memiliki tombol pengatur `[ 5 ] [ 10 ] [ 25 ]` dengan default **5**.
- [x] Tampilan paginasi konsisten 100% di keempat tabel, mengikuti struktur 2 baris sesuai Gambar 2.
- [x] Seluruh pengujian otomatis (`php artisan test`) dan formatting (`pint`) lulus 100% hijau.

