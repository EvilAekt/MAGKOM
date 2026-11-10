# Rencana Harmonisasi Mode Gelap (Dark Mode) Dashboard Peserta

Dokumen ini merinci rencana audit visual dan harmonisasi palet warna mode gelap (*dark mode*) pada Dashboard Peserta (`resources/views/peserta/dashboard.blade.php`) beserta komponen layout dasarnya (`resources/views/layouts/peserta.blade.php`) untuk mengeliminasi anomali visual, ketidaksesuaian kontras (WCAG 2.1 AA), dan elemen *hardcoded light color*.

---

## 1. Tujuan
Menghadirkan pengalaman tampilan mode gelap yang terpadu, elegan, dan konsisten (*seamless dark theme*) di seluruh 4 fase Dashboard Peserta (Onboarding, Seleksi Pendaftaran, Magang Aktif, dan Selesai Magang), sehingga tidak ada:
- Card atau banner berlatar belakang putih/terang yang silau (*flash/glare*).
- Teks yang tidak terbaca karena warna gelap bertemu latar belakang gelap.
- Border atau garis pembatas yang hilang atau terlalu kontras.
- Micro-progress bar dan badge status yang kusam atau tidak proporsional.

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi Teknis
1. Mode gelap dikendalikan melalui atribut root HTML `[data-theme="dark"]` yang tersinkronisasi dengan `localStorage('magkom_theme')` dan preferensi sistem browser.
2. Palet warna dasar mengikuti Design System DS-MagKom Bab 2.1 dengan basis Slate/Navy:
   - Background Utama Kanvas: `--neutral-150` (`#0A0F1D`)
   - Container/Card Surface: `--neutral-0` (`#182234`)
   - Inner Well / Secondary Container: `--neutral-100` (`#0F172A`)
   - Border & Divider: `--neutral-200` (`#26354D`)
   - Input Border: `--neutral-400` (`#3E516E`)
   - Muted/Subtitle Text: `--neutral-500` (`#8FA2BF` / `#94A3B8`)
   - Body Text: `--neutral-700` (`#E2E8F0`)
   - Heading / High-Contrast Text: `--neutral-800` (`#F8FAFC`)
3. Seluruh pengubahan bersifat murni CSS Blade presentational tanpa mengubah logika backend Controller atau schema database.

### Pertanyaan Terbuka
- Tidak ada *blocker* kritis. Variasi fase peserta (Onboarding vs Seleksi vs Aktif vs Selesai) telah dicakup menyeluruh.

---

## 3. Pendekatan Desain

### Perbandingan Pendekatan:

| Kriteria | Opsi A: Pure Inline Overrides & Ad-hoc CSS (Tidak Direkomendasikan) | Opsi B: CSS Semantic Tokenized & Scoped `[data-theme="dark"]` (Direkomendasikan) |
|---|---|---|
| **Struktur** | Menambahkan inline `style` kondisional di Blade (`style="{{ $dark ? '...' : '...' }}"`) | Memanfaatkan selector `[data-theme="dark"]` tersentralisasi di `@push('styles')` dan layout induk |
| **Maintainability** | Buruk, sulit di-*refactor*, kode Blade menjadi kotor dan bercampur logika tampilan | Sangat bersih, deklaratif, mudah diperiksa ulang, mengikuti standar CSS Laravel Blade |
| **Performa & SSR** | Berisiko *layout shift* atau inkonsistensi saat render | Bebas *flicker* karena CSS dievaluasi langsung oleh browser engine |
| **Konsistensi** | Warna rawan meleset antar komponen | 100% konsisten dengan tema Slate/Navy pada menu Tugas & Logbook |

**Keputusan Desain**: Menggunakan **Opsi B**. Semua elemen *hardcoded style* pada Blade dibersihkan menjadi class semantik, kemudian didefinisikan rule warnanya untuk light mode dan dark mode (`[data-theme="dark"]`).

---

## 4. Rincian Perubahan File

| File Path | Aksi | Ringkasan Perubahan |
|---|---|---|
| `resources/views/peserta/dashboard.blade.php` | Ubah | Menyelaraskan seluruh 4 fase dashboard: Hero Banner, Attention Banner, KPI Strip Card & Micro Progress, Dua Kolom (Tugas Mendesak & Logbook), Onboarding Stepper, Seleksi Status Tracker, dan Card Kelulusan (Fase 4). Menghilangkan inline style hardcoded putih/kuning/hijau terang. |
| `resources/views/layouts/peserta.blade.php` | Ubah | Menambahkan rule global `[data-theme="dark"] .alert-warning` dan penyelarasan badge status agar konsisten lintas halaman peserta. |

---

## 5. Rincian Anomali dan Langkah Implementasi

### Tahap 1: Hero Greeting Banner & Attention Banner
- [x] **Hero Role Pill**: Ubah `.hero-role-pill` di dark mode agar tidak menggunakan background `#ffffff` pekat, melainkan transparan elegan `background: rgba(255, 255, 255, 0.2); border: 1px solid rgba(255, 255, 255, 0.35); color: #ffffff;`.
- [x] **Hero Gradient Ambient**: Tambahkan penyesuaian gradien hero pada dark mode agar menyatu dengan palet kanvas `#0A0F1D` tanpa menghilangkan aksen biru resmi Sleman.
- [x] **Attention Banner (Logbook Perlu Revisi)**:
  - Bersihkan inline style hardcoded `color: #92400E` dan `color: #78350F`.
  - Pada dark mode gunakan: `background: rgba(120, 53, 15, 0.35); border-color: rgba(245, 158, 11, 0.4); border-left-color: #F59E0B;`.
  - Teks judul menggunakan `#FCD34D` dan teks deskripsi menggunakan `#FEF3C7`.
  - Tombol `.btn-attention-action` diberikan hover glow amber `#F59E0B`.

### Tahap 2: Panel Metrik Terpadu (Unified KPI Strip Card)
- [x] **Micro-Progress Bar Colors**:
  - Ganti warna hardcoded ungu pekat `#7E22CE` pada status evaluasi menjadi `#A855F7` di dark mode agar tidak redup.
  - Ganti warna hardcoded amber pekat `#D97706` pada sisa hari magang menjadi `#F59E0B` di dark mode.
- [x] **KPI Item & Borders**: Pastikan garis vertikal pembatas item (`::after`) menggunakan `#26354D` dan teks nilai `#F8FAFC` dengan kontras tinggi.
- [x] **Quick Action Bar**: Pastikan tombol `.btn-secondary` (+ Lihat Daftar Tugas, Cek Lembar Nilai) memiliki background `#1E293B`, border `#334155`, dan teks `#CBD5E1`.

### Tahap 3: Dua Kolom (Tugas Mendesak & Logbook Mingguan Terkini)
- [x] **Deadline Pills**:
  - `is-urgent`: background `rgba(239, 68, 68, 0.2)`, border `rgba(239, 68, 68, 0.4)`, text `#F87171`.
  - `is-warning`: background `rgba(245, 158, 11, 0.2)`, border `rgba(245, 158, 11, 0.4)`, text `#FBBF24`.
  - reguler: background `#1E293B`, border `#334155`, text `#CBD5E1`.
- [x] **Week Mini Badge (Logbook)**: background `rgba(6, 65, 204, 0.35)`, border `rgba(7, 81, 255, 0.4)`, text `#60A5FA`.
- [x] **Empty States**: Selaraskan wadah dan teks kosong saat tidak ada tugas / belum ada logbook: heading `#F8FAFC`, icon hijau/abu transparan, dan teks pendukung `#94A3B8`.

### Tahap 4: Fase 1 (Onboarding) & Fase 2 (Seleksi Pendaftaran)
- [x] **Onboarding Stepper**:
  - `.step-title` dan `.step-desc` diselaraskan kontras warnanya saat `active` (`#93C5FD`), `completed` (`#6EE7B7`), dan default (`#F8FAFC` & `#94A3B8`).
- [x] **Status Tracker Card**:
  - Tambahkan style dark mode eksplisit: `background: #182234; border-color: #26354D;`.
  - Selaraskan `.info-strip`: `background: #0F172A; border: 1px solid #26354D;` dengan label `#94A3B8` dan nilai `#F8FAFC`.
  - Selaraskan alert catatan revisi admin verifikator (`.alert-warning`).

### Tahap 5: Fase 4 (Selesai Magang / Kelulusan)
- [x] **Completion Card**:
  - Hapus hardcoded inline `background: #FAFDFB` yang menyilaukan.
  - Ganti dengan class `.completion-card`.
  - Pada dark mode: `background: rgba(6, 78, 59, 0.2); border: 2px solid rgba(52, 211, 153, 0.5); box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);`.
- [x] **Box Predikat Kelulusan**:
  - Ganti inline `background: var(--neutral-0)` menjadi class `.grade-box`.
  - Pada dark mode: `background: #182234; border-color: #26354D;`, teks predikat `#34D399`.
- [x] **Catatan Evaluasi Pembimbing**:
  - Pada dark mode: `background: #0F172A; border: 1px solid #26354D; border-left: 4px solid #34D399;`, teks `#F8FAFC`.

### Tahap 6: Perbaikan Global Layout (`resources/views/layouts/peserta.blade.php`)
- [x] Tambahkan deklarasi `[data-theme="dark"] .alert-warning`:
  ```css
  [data-theme="dark"] .alert-warning {
      background-color: rgba(120, 53, 15, 0.35) !important;
      border-color: rgba(245, 158, 11, 0.4) !important;
      color: #FCD34D !important;
  }
  ```

---

## 6. Strategi Testing
1. **Manual Inspection via DevTools & Dark Mode Toggle**:
   - Uji perpindahan tema Terang -> Gelap -> Sistem melalui dropdown profil peserta.
   - Uji tampilan dashboard dengan akun peserta diterima (`peserta@magkom.go.id` - Fase Magang Aktif).
   - Uji tampilan dengan akun calon peserta pendaftar (`pendaftar@magkom.go.id` - Fase Onboarding / Seleksi).
2. **Kontras Aksesibilitas (WCAG 2.1 AA)**:
   - Verifikasi kontras rasio minimal 4.5:1 untuk semua teks body dan minimal 3:1 untuk label besar / border komponen.
3. **Automated Test Run**:
   - Jalankan test suite relevan: `tests/Feature/PesertaAksesFiturMagangTest.php` dan `tests/Feature/PesertaClassroomTugasLogbookTest.php` untuk memastikan tidak ada Blade syntax error atau regresi rute.

---

## 7. Risiko & Mitigasi
- **Risiko**: Tampilan mode terang (*light mode*) terpengaruh perubahan CSS.
  - **Mitigasi**: Semua rule dark mode dibungkus ketat di dalam selector `[data-theme="dark"]`, sehingga sama sekali tidak menyentuh tampilan mode terang yang sudah berjalan stabil.
- **Risiko**: Perbedaan rendering browser pada transparansi `rgba` dan `backdrop-filter`.
  - **Mitigasi**: Menggunakan fallback warna solid Slate/Navy sebelum deklarasi translucent.

---

## 8. Kriteria Selesai (Definition of Done)
- [x] Tidak ada background putih/terang yang *leak* di mode gelap pada dashboard peserta di seluruh 4 fase.
- [x] Tidak ada teks berwarna gelap/cokelat tua yang menabrak background gelap.
- [x] Semua kartu KPI, stepper onboarding, tracker seleksi, kolom tugas & logbook, serta kartu kelulusan tampil harmonis dengan palet Navy/Slate.
- [x] Toggle tema berfungsi responsif dan langsung menerapkan perubahan tanpa *page refresh reload bug*.
- [x] Seluruh unit/feature test terkait peserta tetap passing (100% green).
