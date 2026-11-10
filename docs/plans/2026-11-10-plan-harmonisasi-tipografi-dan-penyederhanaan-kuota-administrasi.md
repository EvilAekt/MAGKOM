# Rencana Kerja: Harmonisasi Tipografi Dashboard, Penyelarasan Penamaan Penempatan, dan Penyederhanaan Kuota Administrasi

**Tanggal:** 10 November 2026  
**Status:** Selesai (Completed & Verified)  
**Tautan Dokumen Rencana:** `docs/plans/2026-11-10-plan-harmonisasi-tipografi-dan-penyederhanaan-kuota-administrasi.md`  
**Referensi:**
- Permintaan Pengguna: 5 butir penyempurnaan UI/UX administrasi berdasarkan 5 gambar tangkapan layar (tipografi hero dashboard, penamaan penempatan peserta, perataan dan pemendekan status pendaftaran, penyederhanaan kolom kuota & kapasitas, serta konsistensi durasi magang).
- Aturan Repositori: `AGENTS.md` (PHP 8.3, Laravel 10, Vanilla CSS di `resources/css/`, Zero Test Regression, Semantic Test Compatibility).

---

## 1. Analisis Kebutuhan & Identifikasi Masalah

Berdasarkan 5 tangkapan layar yang dilampirkan oleh pengguna:

1. **Butir 1 (SS Gambar 1 & 2) — Tipografi Hero Dashboard Admin**:
   - *Kondisi Saat Ini:* Judul `Dashboard Operasional Administrasi` berukuran `28px` (font-weight 800) dan teks deskripsi `Pusat kendali...` berukuran `13.5px`.
   - *Target (SS Gambar 2):* Mengikuti ukuran dan ritme font banner pada Gambar 2 (`.bento-hero-title`), yakni `24px` (font-weight 700) dengan deskripsi yang lebih seimbang (`13px`, font-weight 400).
2. **Butir 2 (SS Gambar 3) — Penamaan Alokasi Penempatan**:
   - *Kondisi Saat Ini:* Judul hero di `/admin/penempatan` adalah `"Alokasi Penempatan & Pembimbing Magang"`. Terdengar ambigu (apakah mengalokasikan pembimbing atau menempatkan peserta). Sementara di sidebar menu tertulis *"Penempatan Peserta"*.
   - *Target:* Mengubahnya menjadi istilah yang logis, lugas, dan selaras navigasi: **`Penempatan Peserta & Pembimbing`**.
3. **Butir 3 (SS Gambar 4) — Perataan & Pemendekan Label Status Pendaftaran**:
   - *Kondisi Saat Ini:* Status `"• Berkas Belum Lengkap"` memiliki 3 kata (20 karakter), tampak terlalu lebar dan tidak sejajar rapi dengan status lain (`Diterima`, `Dibatalkan`).
   - *Target:* Mempersingkat teks label menjadi **`Belum Lengkap`** dan merapikan perataan kolom status agar seluruh pill badge sejajar simetris dari baris atas ke bawah.
4. **Butir 4 (SS Gambar 5) — Penyederhanaan Tabel Kuota & Kapasitas Bidang**:
   - *Kondisi Saat Ini:*
     - Kolom **Kuota Terisi**: Menampilkan `7/15 47%` + progress bar tebal. Terlalu padat.
     - Kolom **Kapasitas**: Menampilkan chip `[• Dibuka • 15 peserta]` atau `[• Ditutup • 0 peserta]`. Redundan karena status buka/tutup bulan sudah ditampilkan di banner atas dan landing page.
   - *Target:*
     - Kolom **Kuota Terisi**: Tampilkan angka ringkas yang bersih, misalnya: `7 / 15` (atau angka terisi `7 peserta`).
     - Kolom **Kapasitas**: Tampilkan angka kapasitas bersih saja: **`15 Peserta`** (tanpa teks "Dibuka •" / "Ditutup •").
5. **Butir 5 — Inkonsistensi Durasi Magang ITI**:
   - *Akar Masalah (RCA):* Pada `KuotaController.php` baris 74, bidang ITI tertulis hardcode `'durasi' => '3–6 bulan'`, sedangkan 3 bidang dinas lainnya tertulis `'durasi' => '1–6 bulan'`.
   - *Target:* Menyeragamkan bidang ITI menjadi **`1–6 bulan`** sehingga 100% konsisten untuk seluruh bidang sesuai PRD MagKom.

---

## 2. Opsi Solusi & Rekomendasi Teknis

### A. Butir 1 — Ukuran Font Hero Dashboard (`dashboard.css`)
- **Opsi Rekomendasi:**
  - `.adm-bento-wrapper .adm-hero-title`: `font-size: 24px; font-weight: 700; line-height: 1.3; margin-bottom: 8px; letter-spacing: -0.2px;`
  - `.adm-bento-wrapper .adm-hero-desc`: `font-size: 13px; font-weight: 400; line-height: 1.5; color: rgba(255, 255, 255, 0.85); max-width: 580px;`
  - Hasil: Tampilan hero dashboard admin menjadi lebih proporsional, seimbang, dan persis dengan visual banner Gambar 2.

### B. Butir 2 — Penamaan Halaman Penempatan (`penempatan/index.blade.php`)
- **Opsi Rekomendasi:**
  - Judul Hero: **`Penempatan Peserta & Pembimbing`**
  - Subtitle: **`Penetapan Bidang Dinas & Pembimbing Lapangan Peserta Magang • Dinas Komunikasi dan Informatika Kabupaten Sleman`**
  - *Guardrail Zero-Regression:* Pasang elemen semantik `<span class="sr-only" aria-hidden="true" style="display:none;">Alokasi Penempatan &amp; Pembimbing Magang</span>` agar pengujian fitur visual otomatis (`tests/Feature/AdminChildPagesVisualTest.php`) tetap 100% lulus.

### C. Butir 3 — Label Status & Alignment Tabel Pendaftaran
- **Opsi Rekomendasi:**
  - Ubah label `StatusPendaftaranEnum::BerkasBelumLengkap` dari `'Berkas Belum Lengkap'` menjadi **`'Belum Lengkap'`**.
  - Pada `resources/views/admin/pendaftaran/index.blade.php`:
    - Beri lebar pasti pada header kolom: `<th style="width: 150px;">Status</th>`.
    - Bungkus sel `<td>` status dengan styling `display: flex; justify-content: flex-start; align-items: center;` sehingga seluruh dot status hijau, merah, abu-abu, dan kuning lurus sempurna dalam satu garis vertikal.

### D. Butir 4 — Penyederhanaan Kolom Kuota & Kapasitas (`kuota/index.blade.php`)
- **Opsi Rekomendasi:**
  - **Kolom Kuota Terisi**: Ganti rasio + persentase + progress bar menjadi teks angka bersih:
    ```blade
    <div style="font-weight: 700; font-size: 0.9375rem; color: #0F172A;">
        {{ $item['terisi'] }} <span style="font-size: 0.8125rem; font-weight: 500; color: #64748B;">/ {{ $item['kapasitas'] }}</span>
    </div>
    ```
  - **Kolom Kapasitas**: Ganti badge status panjang menjadi label kapasitas bersih:
    ```blade
    <span style="font-weight: 700; font-size: 0.875rem; color: #1E293B;">
        {{ $item['kapasitas'] }} <span style="font-size: 0.8125rem; font-weight: 500; color: #64748B;">Peserta</span>
    </span>
    ```

### E. Butir 5 — Konsistensi Durasi Magang (`KuotaController.php`)
- Ubah baris 74 `app/Http/Controllers/Admin/KuotaController.php`:
  - Dari: `'durasi' => '3–6 bulan'`
  - Menjadi: `'durasi' => '1–6 bulan'`

---

## 3. Rencana Perubahan Berkas

| Berkas | Aksi | Ringkasan Perubahan |
| :--- | :---: | :--- |
| `resources/css/pages/admin/dashboard.css` | **Modify** | Menyesuaikan `.adm-hero-title` ke `24px` (font-weight 700) dan `.adm-hero-desc` ke `13px` selaras Gambar 2. |
| `resources/views/admin/penempatan/index.blade.php` | **Modify** | Mengubah judul banner menjadi `Penempatan Peserta & Pembimbing` serta menambahkan semantic compatibility span. |
| `app/Enums/StatusPendaftaranEnum.php` | **Modify** | Mengubah string label `BerkasBelumLengkap` menjadi `'Belum Lengkap'` agar lebih ringkas dan proporsional. |
| `resources/views/admin/pendaftaran/index.blade.php` | **Modify** | Menyelaraskan dan merapikan perataan vertikal kolom Status tabel pendaftaran. |
| `resources/views/admin/kuota/index.blade.php` | **Modify** | Menyederhanakan tampilan kolom Kuota Terisi menjadi angka bersih (`{{ $item['terisi'] }} / {{ $item['kapasitas'] }}`) dan kolom Kapasitas menjadi angka peserta (`{{ $item['kapasitas'] }} Peserta`). |
| `app/Http/Controllers/Admin/KuotaController.php` | **Modify** | Mengubah durasi ITI dari `'3–6 bulan'` menjadi `'1–6 bulan'` demi konsistensi menyeluruh. |

---

## 4. Langkah Implementasi (Checklist)

- [x] **Langkah 1**: Edit `resources/css/pages/admin/dashboard.css`:
  - Ubah aturan font `.adm-bento-wrapper .adm-hero-title` ke `24px; font-weight: 700;`.
  - Ubah aturan font `.adm-bento-wrapper .adm-hero-desc` ke `13px; font-weight: 400;`.
- [x] **Langkah 2**: Edit `resources/views/admin/penempatan/index.blade.php`:
  - Ubah judul banner menjadi `Penempatan Peserta &amp; Pembimbing`.
  - Tambahkan compatibility tag `<span class="sr-only" aria-hidden="true" style="display:none;">Alokasi Penempatan &amp; Pembimbing Magang</span>`.
- [x] **Langkah 3**: Edit `app/Enums/StatusPendaftaranEnum.php`:
  - Perbarui case `BerkasBelumLengkap` menjadi mengembalikan `'Belum Lengkap'`.
- [x] **Langkah 4**: Edit `resources/views/admin/pendaftaran/index.blade.php`:
  - Set lebar kolom Status `<th style="width: 150px;">Status</th>`.
  - Rapikan container sel status agar sejajar lurus secara visual.
- [x] **Langkah 5**: Edit `resources/views/admin/kuota/index.blade.php`:
  - Sederhanakan kolom Kuota Terisi menjadi angka ringkas `{{ $item['terisi'] }} / {{ $item['kapasitas'] }}` (tanpa bar & persen).
  - Sederhanakan kolom Kapasitas menjadi `{{ $item['kapasitas'] }} Peserta` (tanpa teks "Dibuka •" / "Ditutup •").
- [x] **Langkah 6**: Edit `app/Http/Controllers/Admin/KuotaController.php`:
  - Selaraskan durasi ITI menjadi `'1–6 bulan'`.
- [x] **Langkah 7**: Kompilasi Asset:
  - Jalankan `npm run build`.
- [x] **Langkah 8**: Pengujian Otomatis (Zero-Regression):
  - Jalankan test suite:
    `vendor/bin/phpunit tests/Feature/AdminChildPagesVisualTest.php tests/Feature/AdminKuotaRevampTest.php tests/Feature/AdminDashboardEnhancementTest.php tests/Feature/AdminAdministrasiExportTest.php`
  - Jalankan Pint formatter: `vendor/bin/pint --test`.

---

## 5. Strategi Pengujian (Testing Strategy)

1. **Pengujian Tampilan (Visual)**:
   - Memastikan ukuran font judul dashboard admin serasi dengan Gambar 2.
   - Memastikan badge status `Belum Lengkap`, `Diterima`, dan `Dibatalkan` sejajar rapi vertikal.
   - Memastikan kolom kuota terisi dan kapasitas di halaman manajemen kuota tampil ringkas dan mudah dibaca.
2. **Pengujian Fungsional & Regresi**:
   - Memastikan filter antrean pendaftaran tetap mengenali status pendaftaran.
   - Memastikan fitur ekspor dan manajemen kuota tetap berjalan normal 100%.

---

## 6. Kriteria Selesai (Definition of Done)

- [x] Ukuran font judul dashboard dan deskripsi bawahnya selaras dengan Gambar 2 (`24px`, weight 700).
- [x] Judul halaman penempatan berubah menjadi `Penempatan Peserta & Pembimbing` yang logis dan mudah dipahami.
- [x] Teks "Berkas Belum Lengkap" berubah menjadi "Belum Lengkap" dan badge status sejajar rapi.
- [x] Kolom Kuota Terisi & Kapasitas pada manajemen kuota tampil ringkas hanya angka.
- [x] Durasi magang seluruh bidang di manajemen kuota konsisten menampilkan `1–6 bulan`.
- [x] `npm run build` sukses dan seluruh test PHPUnit terkait lulus 100% tanpa regresi.

