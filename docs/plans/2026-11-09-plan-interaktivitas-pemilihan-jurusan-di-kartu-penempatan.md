# Rencana Kerja: Interaktivitas Pemilihan & Klik Jurusan pada Kartu Penempatan Landing Page

**Tanggal**: 9 November 2026  
**Dokumen**: `docs/plans/2026-11-09-plan-interaktivitas-pemilihan-jurusan-di-kartu-penempatan.md`  
**Status**: Menunggu Persetujuan User (Drafting Plan - Read-Only)  
**Terkait**:
- Blok Kuota & Penempatan: `resources/views/public/blocks/kuota.blade.php`
- Modul JavaScript Interaktif: `resources/js/pages/public/landing.js`
- Stylesheet Desain Token: `resources/css/pages/public/landing.css`
- Backend Matriks Controller: `app/Http/Controllers/Public/LandingController.php`, `app/Http/Controllers/SuperAdmin/LandingCmsController.php`, `app/Http/Controllers/SuperAdmin/LandingCmsSiteEditorController.php`
- Form Pendaftaran & Dashboard: `resources/views/auth/register.blade.php`, `app/Http/Controllers/Auth/AuthController.php`

---

## 1. Latar Belakang & Kebutuhan Pengguna

Pada implementasi kartu **Penempatan / Bidang** saat ini:
- Kartu utama menampilkan nama Bidang Penempatan (contoh: *Sekretariat Dinas*, *Infrastruktur Teknologi Informasi (ITI)*, *Aplikasi Informatika & E-Government (EGOV)*, *Informasi dan Komunikasi Publik (IKP)*).
- Jurusan-jurusan yang sesuai di dalam kartu saat ini masih berupa badge teks pasif (`<span class="jurusan-tag">`).
- Tombol *Daftar Sekarang* masih menggunakan default jurusan pertama dari daftar jurusan bidang tersebut.

**Permintaan Pengguna**:
> *"logicnya brarti di landing page harus memilih dan klik jurusan nya dong soalnya di landing page dan dashboard menampilkan bidang dan jurusan kan buat planya"*

Pengguna menginginkan agar di dalam setiap kartu penempatan bidang:
1. Calon peserta magang dapat **memilih dan mengklik langsung jurusan yang relevan** dengan latar belakang pendidikannya.
2. Ketika salah satu jurusan di-klik / dipilih:
   - Badge jurusan tersebut berubah menjadi status aktif terpilih (*highlight active state*).
   - Angka sisa kuota, kapasitas kursi, progress bar, dan badge status kartu (*Tersedia*, *Tinggal sedikit*, *Kuota Penuh*) **otomatis bereaksi memperbarui nilainya secara real-time** sesuai kuota spesifik jurusan yang diklik tersebut pada periode bulan yang aktif.
   - Tombol aksi **Daftar Sekarang ➔** langsung memperbarui tautannya dengan parameter jurusan yang diklik:  
     `/register?bulan={bulan_id}&bidang={bidang_id}&jurusan={jurusan_id_yang_diklik}&jenjang={smk/s1}&tipe={mandiri/kelompok}&jumlah={n}`.
   - Jika jurusan yang dipilih kuotanya sudah habis (0 kursi), tombol secara otomatis berubah menjadi tombol nonaktif (*Kuota Penuh*). Jika kuota tersedia, tombol kembali aktif.
3. Alur ini sinkron 100% dengan formulir pendaftaran `/register` dan dashboard peserta, di mana bidang penempatan dan jurusan pilihan langsung terkunci dan ditampilkan.

---

## 2. Analisis Logika & Struktur Data

### 2.1 Backend Data Payload (Matriks Detail per Jurusan)
Di dalam `LandingController.php` (serta preview CMS):
Setiap elemen `$matriksBidang[$bidang->id][$jenjang][$bulan->id]` akan dilengkapi dengan rincian data kuota untuk setiap jurusan di bawah bidang tersebut:
```php
'jurusan_detail' => [
    $jurusanId => [
        'id' => $jurusan->id,
        'nama' => $jurusan->nama_jurusan,
        'total' => $totalKuotaJurusan,
        'terisi' => $terisiKuotaJurusan,
        'sisa' => $sisaKuotaJurusan,
        'persen' => $persenTerisiJurusan,
        'is_available' => $isAvailable,
        'status_label' => $statusLabel, // 'Tersedia', 'Tinggal sedikit', atau 'Penuh'
        'badge_class' => $badgeClass,
        'bar_class' => $barClass,
    ],
    // ...
]
```

### 2.2 Komponen Interaktif di Blade (`kuota.blade.php`)
Di dalam setiap kartu bidang:
- Tag jurusan pasif diubah menjadi tombol interaktif beraksesibilitas tinggi:
  ```blade
  <button type="button" 
          class="jurusan-pill-btn {{ $loop->first ? 'active' : '' }}"
          data-jurusan-id="{{ $jur->id }}"
          data-jurusan-name="{{ $jur->nama_jurusan }}"
          data-sisa="{{ $jData['sisa'] }}"
          data-total="{{ $jData['total'] }}"
          data-persen="{{ $jData['persen'] }}"
          data-status="{{ $jData['status_label'] }}"
          data-badge-class="{{ $jData['badge_class'] }}"
          data-bar-class="{{ $jData['bar_class'] }}"
          role="radio"
          aria-checked="{{ $loop->first ? 'true' : 'false' }}">
      <span class="pill-check-icon" aria-hidden="true">✓</span>
      {{ $jur->nama_jurusan }}
  </button>
  ```
- Kartu bidang memiliki atribut inisial:
  - `data-selected-jurusan-id="{{ $firstJurusanId }}"`
  - Angka awal menampilkan kuota jurusan pertama yang aktif terpilih.

### 2.3 Handler Interaksi JavaScript (`landing.js`)
Fungsi `initJurusanPillSelector()`:
- Mendengarkan event `click` pada `.jurusan-pill-btn`.
- Mengalihkan kelas `.active` dan atribut `aria-checked` ke tombol yang diklik.
- Mengambil dataset `sisa`, `total`, `persen`, `badgeClass`, `barClass`.
- Memperbarui elemen di dalam kartu terkait:
  - `.kuota-big-number` diisi nilai `sisa`.
  - `.kuota-stat-total` diisi `dari {total} kursi`.
  - `.kuota-progress-bar-fill` disetel lebarnya ke `{persen}%` dan class warnanya diperbarui.
  - `.kuota-status-pill` diperbarui label dan class-nya.
  - Memanggil `updateCardActionButtons()` untuk memperbarui link CTA tombol `Daftar Sekarang` dengan `jurusanId` yang baru diklik.

---

## 3. Desain Visual & UX State

```
+---------------------------------------------------------------+
| [🏢]  Sekretariat Dinas                            [Tersedia] |
|                                                               |
| 3 kursi tersisa dari 5 kursi                                  |
| [==================== Progress Bar 40% =====================] |
|                                                               |
| Mengelola kesekretariatan dinas, administrasi kepegawaian,    |
| keuangan, tata persuratan, dan kearsipan terpadu.             |
|                                                               |
| Pilih Jurusan yang Sesuai:                                    |
| (•) ✓ Administrasi Perkantoran   ( ) Desain Grafis            |
| ( ) Manajemen Perkantoran        ( ) Ilmu Komunikasi          |
|                                                               |
| [                   Daftar Sekarang ➔                       ] |
|                   (Jurusan: Administrasi Perkantoran)         |
+---------------------------------------------------------------+
```

### 3.1 Styling Token CSS (`landing.css`)
- `.jurusan-pill-btn`:
  - Bentuk pill tombol rounded modern (`border-radius: 9999px` atau `6px`).
  - Latar belakang netral halus (`rgba(1, 52, 124, 0.05)`), border `1px solid rgba(1, 52, 124, 0.18)`, warna teks `--navy`.
  - Transisi mulus (`all 0.2s ease`), kursor pointer.
- `.jurusan-pill-btn:hover`:
  - Background lebih tegas (`rgba(1, 52, 124, 0.12)`), sedikit elevasi.
- `.jurusan-pill-btn.active`:
  - Background solid `--navy` (`#1B3860`), warna teks `#ffffff`, border `--navy`.
  - Menampilkan ikon centang halus (`✓`) di sisi kiri teks jurusan.
  - Bayangan lembut `box-shadow: 0 2px 8px rgba(27, 56, 96, 0.25)`.
- Fokus aksesibilitas (Keyboard Tab & Space/Enter navigation):
  - Focus ring `outline: 2px solid var(--gold-bright); outline-offset: 2px;`.

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :---: | :--- |
| [`app/Http/Controllers/Public/LandingController.php`](file:///d:/MAGKOM/MAGKOM/app/Http/Controllers/Public/LandingController.php) | **Modify** | Menambahkan kalkulasi kuota detail per jurusan ke dalam matriks `$matriksBidang`. |
| [`app/Http/Controllers/SuperAdmin/LandingCmsController.php`](file:///d:/MAGKOM/MAGKOM/app/Http/Controllers/SuperAdmin/LandingCmsController.php) | **Modify** | Menyelaraskan rincian kuota jurusan di preview staging CMS. |
| [`app/Http/Controllers/SuperAdmin/LandingCmsSiteEditorController.php`](file:///d:/MAGKOM/MAGKOM/app/Http/Controllers/SuperAdmin/LandingCmsSiteEditorController.php) | **Modify** | Menyelaraskan rincian kuota jurusan di revision preview Site Editor. |
| [`resources/views/public/blocks/kuota.blade.php`](file:///d:/MAGKOM/MAGKOM/resources/views/public/blocks/kuota.blade.php) | **Modify** | Mengubah tag jurusan pasif menjadi tombol interaktif `.jurusan-pill-btn` dengan dataset lengkap per jurusan. |
| [`resources/js/pages/public/landing.js`](file:///d:/MAGKOM/MAGKOM/resources/js/pages/public/landing.js) | **Modify** | Menambahkan listener klik jurusan pada kartu bidang, memperbarui kuota kartu dinamis, dan memperbarui URL CTA registrasi. |
| [`resources/css/pages/public/landing.css`](file:///d:/MAGKOM/MAGKOM/resources/css/pages/public/landing.css) | **Modify** | Menambahkan styling `.jurusan-pill-btn`, status `.active`, hover, icon check, dan focus state. |
| [`public/css/landing-modern.css`](file:///d:/MAGKOM/MAGKOM/public/css/landing-modern.css) & [`public/js/landing-interactive.js`](file:///d:/MAGKOM/MAGKOM/public/js/landing-interactive.js) | **Sync** | Sinkronisasi asset statis hasil build. |

---

## 5. Langkah-Langkah Implementasi Bertahap

- [x] **Langkah 1**: Backend Data Detail Jurusan di `LandingController.php`, `LandingCmsController.php`, dan `LandingCmsSiteEditorController.php`:
  - Hitung kuota individual setiap jurusan di dalam bidang pada bulan terkait (`total`, `terisi`, `sisa`, `persen`, `status_label`, `badge_class`, `bar_class`).
  - Masukkan array `'jurusan_detail'` ke dalam struktur matriks ketersediaan bidang.
- [x] **Langkah 2**: Perbarui Markup di `resources/views/public/blocks/kuota.blade.php`:
  - Render deretan jurusan sebagai tombol `<button type="button" class="jurusan-pill-btn ...">`.
  - Berikan data attributes per jurusan dan set default `.active` pada jurusan pertama.
  - Tampilkan instruksi: `Penempatan: Jurusan yang Sesuai (Klik jurusan Anda)`.
  - Pertahankan class `jurusan-tag` pada inner span untuk kompatibilitas test mapping.
- [x] **Langkah 3**: Tambahkan Styling CSS di `resources/css/pages/public/landing.css`:
  - Buat style `.jurusan-pill-btn`, `.jurusan-pill-btn:hover`, `.jurusan-pill-btn.active`, dan `.pill-check-icon`.
- [x] **Langkah 4**: Tambahkan Interaktivitas JS di `resources/js/pages/public/landing.js`:
  - Pasang event listener klik pada `.jurusan-pill-btn` di dalam `initKuotaExplorer()`.
  - Update realtime kartu: sisa kursi, total kursi, progress bar, badge status, dan status penuh (`is-penuh`).
  - Sinkronkan URL CTA `Daftar Sekarang` agar membawa `jurusan={id}` yang dipilih.
  - Sinkronkan gauge ring meter ketersediaan total.
- [x] **Langkah 5**: Sinkronisasi Aset & Build:
  - Copy CSS dan JS ke folder `public/`.
  - Jalankan `npm run build` Vite.
  - Jalankan `artisan view:clear`.
- [x] **Langkah 6**: Automated Verification:
  - Jalankan 72 Feature Tests (`artisan test ...`): 100% lulus (507 assertions).
  - Jalankan formatting check `vendor/bin/pint --test`: passed.

---

## 6. Kriteria Selesai (Definition of Done)

1. Di setiap kartu bidang pada landing page, calon peserta dapat melihat dan **mengklik pilihan jurusan** yang relevan.
2. Ketika jurusan diklik:
   - Tombol jurusan langsung berstatus aktif (warna biru solid & tanda centang).
   - Angka kuota, progress bar, dan status kartu otomatis menyesuaikan kuota spesifik jurusan tersebut.
   - Tombol CTA *Daftar Sekarang* langsung membawa parameter jurusan yang diklik ke halaman pendaftaran `/register`.
3. Pendaftar yang masuk ke `/register` otomatis terisi bidang dan jurusan pilihannya.
4. Seluruh rangkaian automated test suite lulus 100% tanpa regresi.
5. Kode memenuhi standar PSR-12 dan build Vite terkompilasi bersih.

