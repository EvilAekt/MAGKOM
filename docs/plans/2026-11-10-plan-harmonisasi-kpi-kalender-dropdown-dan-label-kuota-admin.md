# Rencana Kerja: Penghapusan Garis Bawah KPI, Penambahan Dropdown Bulan & Tahun Kalender Admin, dan Penyesuaian Label Kuota Peserta

**Tanggal:** 10 November 2026  
**Status:** Selesai (Completed & Verified)  
**Tautan Dokumen Rencana:** `docs/plans/2026-11-10-plan-harmonisasi-kpi-kalender-dropdown-dan-label-kuota-admin.md`  
**Referensi:**
- Permintaan Pengguna: 3 butir penyempurnaan UI/UX administrasi berdasarkan 5 gambar tangkapan layar (penghapusan garis warna bawah kartu KPI, penambahan dropdown klik bulan dan tahun interaktif pada kalender admin selaras dengan kalender pembimbing, serta perubahan teks banner dari "KUOTA MAGANG DINAS" menjadi "KUOTA MAGANG PESERTA").
- Aturan Repositori: `AGENTS.md` (PHP 8.3, Laravel 10, Vanilla CSS di `resources/css/`, Vanilla JS di `resources/js/`, Zero Test Regression).

---

## 1. Analisis Kebutuhan & Identifikasi Masalah

Berdasarkan 5 tangkapan layar yang diunggah oleh pengguna:

1. **Butir 1 (SS Gambar 1 & 2) — Penghapusan Garis Warna Bawah Kartu KPI**:
   - *Kondisi Saat Ini:* Pada baris kartu metrik KPI dashboard admin (`.adm-kpi-card`), terdapat garis strip warna tebal di sisi bawah masing-masing kartu:
     - Verifikasi Tertunda: Navy Sleman (`border-bottom-color: var(--adm-navy-main)`)
     - Kuota Terisi: Indigo (`border-bottom-color: var(--adm-indigo)`)
     - Peserta Aktif: Kuning Amber (`border-bottom-color: var(--adm-yellow-main)`)
     - Kecepatan Verifikasi: Cyan Biru Muda (`border-bottom-color: #38BDF8`)
   - *Target:* Menghilangkan garis strip warna bawah tersebut sehingga kartu KPI tampak bersih, modern, dan seragam tanpa garis bawah berwarna.
2. **Butir 2 (SS Gambar 3 & 4) — Dropdown Klik Bulan dan Tahun pada Kalender Admin**:
   - *Kondisi Saat Ini (SS Gambar 3):* Header kalender admin hanya menampilkan teks statis `< November 2026 >` yang hanya bisa digeser satu per satu dengan tombol panah kiri-kanan.
   - *Target (SS Gambar 4 — Pembimbing Dashboard):* Menyelaraskan header kalender admin dengan pembimbing sehingga memiliki tombol kapsul dropdown interaktif:
     - **Pilih Bulan:** Tombol kapsul `[ November v ]` yang ketika diklik membuka popover 12 bulan (Jan–Des).
     - **Pilih Tahun:** Tombol kapsul `[ 2026 v ]` yang ketika diklik membuka popover grid tahun disertai tombol navigasi lompat tahun `<<` dan `>>`.
3. **Butir 3 (SS Gambar 5) — Penyesuaian Teks Label Kuota Menjadi "KUOTA MAGANG PESERTA"**:
   - *Kondisi Saat Ini:* Pada banner highlight kuning (`.adm-highlight-card`), teks tertulis `16 /30 KUOTA MAGANG DINAS`.
   - *Target:* Mengubah teks menjadi **`KUOTA MAGANG PESERTA`** karena kuota tersebut secara logis diperuntukkan bagi peserta magang dinas, bukan kuota dinas itu sendiri.

---

## 2. Rencana Solusi & Rekomendasi Teknis

### A. Butir 1 — Menghilangkan Garis Bawah Kartu KPI (`dashboard.css`)
- **Solusi Teknis:**
  - Pada `resources/css/pages/admin/dashboard.css`:
    - Hapus properti `border-bottom: 3.5px solid transparent;` dari rule `.adm-kpi-card`.
    - Hapus deklarasi `.kpi-card-verifikasi { border-bottom-color: ... }`, `.kpi-card-kuota`, `.kpi-card-peserta`, dan `.kpi-card-speed`.
  - Hasil: Kartu KPI tampil borderless di bagian bawah dengan radius sudut lengkung yang mulus (`border-radius: 20px`), bersih, dan elegan.

### B. Butir 2 — Dropdown Bulan & Tahun Kalender Admin (`dashboard.blade.php`, `dashboard.css`, & `dashboard.js`)
- **Solusi Teknis:**
  1. **Blade Markup (`resources/views/admin/dashboard.blade.php`)**:
     - Gantikan judul statis `#admCalTitle` dengan struktur judul container dropdown pembimbing:
       - Tombol trigger Bulan `#admCalMonthTriggerBtn` + Popover `#admCalMonthPopover` (grid 12 bulan).
       - Tombol trigger Tahun `#admCalYearTriggerBtn` + Popover `#admCalYearPopover` (grid tahun + tombol jump `<<` & `>>`).
  2. **Stylesheet (`resources/css/pages/admin/dashboard.css`)**:
     - Tambahkan aturan styling untuk `.cal-title-container`, `.cal-month-trigger-btn`, `.cal-year-trigger-btn`, `.cal-month-popover`, `.cal-year-popover`, `.cal-month-grid`, `.cal-year-grid`, `.cal-month-option-btn`, `.cal-year-option-btn`, dsb., konsisten dengan styling pembimbing.
  3. **JavaScript (`resources/js/pages/admin/dashboard.js`)**:
     - Lengkapi fungsi `initAdminDashboardCalendar()` dengan logika manajemen popover:
       - `renderMonthPopover()` & `renderYearPopover()`
       - `toggleMonthPopover()` & `toggleYearPopover()`
       - Pemilihan bulan dan tahun instan yang langsung me-render ulang tanggal dan menutup popover.
       - Penutupan popover otomatis saat pengguna mengklik di luar area kalender (*click outside listener*).

### C. Butir 3 — Mengubah Teks Label Kuota Menjadi "KUOTA MAGANG PESERTA" (`dashboard.blade.php`)
- **Solusi Teknis:**
  - Pada `resources/views/admin/dashboard.blade.php` baris 272:
    - Dari: `<span class="roster-caps-label">KUOTA MAGANG DINAS</span>`
    - Menjadi: `<span class="roster-caps-label">KUOTA MAGANG PESERTA</span>`

---

## 3. Rencana Berkas yang Disentuh

| Berkas | Aksi | Ringkasan Perubahan |
| :--- | :---: | :--- |
| `resources/css/pages/admin/dashboard.css` | **Modify** | 1. Menghapus garis warna bawah strip pada 4 kartu KPI (`border-bottom-color`).<br>2. Menambahkan aturan CSS dropdown dan popover bulan/tahun kalender admin. |
| `resources/views/admin/dashboard.blade.php` | **Modify** | 1. Memperbarui markup header kalender dengan tombol trigger popover Bulan dan Tahun.<br>2. Mengubah teks label banner menjadi `KUOTA MAGANG PESERTA`. |
| `resources/js/pages/admin/dashboard.js` | **Modify** | Menambahkan logika popover pemilihan bulan & tahun interaktif pada fungsi `initAdminDashboardCalendar()`. |

---

## 4. Langkah Implementasi (Checklist)

- [x] **Langkah 1**: Edit `resources/css/pages/admin/dashboard.css`:
  - Hapus border bawah strip warna pada `.adm-kpi-card` dan kelas warnanya (`.kpi-card-verifikasi`, `.kpi-card-kuota`, `.kpi-card-peserta`, `.kpi-card-speed`).
  - Tambahkan stylesheet `.cal-title-container`, `.cal-month-trigger-btn`, `.cal-year-trigger-btn`, `.cal-month-popover`, `.cal-year-popover`, dsb.
- [x] **Langkah 2**: Edit `resources/views/admin/dashboard.blade.php`:
  - Perbarui header kalender dengan container dropdown bulan & tahun.
  - Ubah teks label `KUOTA MAGANG DINAS` menjadi `KUOTA MAGANG PESERTA`.
- [x] **Langkah 3**: Edit `resources/js/pages/admin/dashboard.js`:
  - Implementasikan popover bulan (12 bulan) dan tahun dengan navigasi loncat tahun di `initAdminDashboardCalendar()`.
- [x] **Langkah 4**: Kompilasi Asset:
  - Jalankan `npm run build` untuk mengompilasi CSS dan JS ke bundle Vite.
- [x] **Langkah 5**: Pengujian Otomatis & Quality Gate:
  - Jalankan test suite PHPUnit:
    `vendor/bin/phpunit tests/Feature/AdminChildPagesVisualTest.php tests/Feature/AdminKuotaRevampTest.php tests/Feature/AdminDashboardEnhancementTest.php tests/Feature/AdminAdministrasiExportTest.php`
  - Jalankan Laravel Pint: `vendor/bin/pint --test`.

---

## 5. Strategi Pengujian (Testing Strategy)

1. **Pengujian Tampilan (Visual)**:
   - Memastikan tidak ada lagi garis warna di bagian bawah kartu KPI (tampak bersih dan mulus).
   - Memastikan header kalender admin menampilkan tombol dropdown `[ Bulan v ]` dan `[ Tahun v ]`.
   - Menguji interaksi klik pada dropdown bulan dan tahun: popover muncul dengan rapi, pilihan bulan/tahun dapat diklik dan memperbarui kalender, serta popover menutup saat diklik di luar.
   - Memastikan banner kuning menampilkan teks `KUOTA MAGANG PESERTA`.
2. **Pengujian Regresi Fungsional**:
   - Memastikan seluruh assertion automated test pada dashboard admin tetap lulus 100%.

---

## 6. Kriteria Selesai (Definition of Done)

- [x] Garis warna bawah pada 4 kartu KPI dashboard admin telah dihapus.
- [x] Kalender admin memiliki dropdown interaktif untuk memilih Bulan dan Tahun persis seperti kalender pembimbing.
- [x] Teks banner kuota telah disesuaikan menjadi `KUOTA MAGANG PESERTA`.
- [x] `npm run build` berhasil tanpa error.
- [x] Seluruh test suite PHPUnit lulus 100% tanpa regresi.

