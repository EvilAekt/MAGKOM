# Senior Developer Task Brief (PRD & Implementation Plan)
## Perbaikan Indikator Progress Bar Kosong pada Kartu "PERIODE MAGANG" (Profil 360° Peserta)

- **Target Persona Pelaksana**: Junior Developer / AI Model (Gemini 3.8 Flash / Claude Haiku / Cursor)
- **Modul**: Pembimbing Portal — Profil 360° Peserta Bimbingan (`/pembimbing/peserta/{id}`)
- **Dokumen Referensi**: Tangkapan Layar `media_1794222415445_62786632.png` (Crop Periode Magang Bar Kosong), PRD MagKom v1.1
- **Tanggal Dibuat**: 09 November 2026
- **Status**: Ready for Implementation (Awaiting Confirmation)

---

## 1. Executive Summary & Root Cause Analysis (RCA)

### 1.1 Deskripsi Masalah
Pada kartu **PERIODE MAGANG** (`.kpi-card-yellow`) di halaman Profil 360° Peserta Bimbingan, elemen bilah kemajuan (*progress bar*) tampak sepenuhnya kosong tanpa ada warna pengisi (*fill*), meskipun teks di atasnya dengan jelas menyatakan bahwa magang sedang aktif berjalan:
- **Metrik Utama**: `83`
- **Subjudul**: `hari tersisa dari 91`
- **Rentang Tanggal**: `01 Nov 2026` s/d `31 Jan 2027`
- **Status Bar**: Jalur rel abu-abu (`.periode-progress-track`) tampak kosong melompong (0% fill).

### 1.2 Temuan Akar Penyebab (Root Cause)
Setelah dilakukan penelusuran pada `app/Http/Controllers/Pembimbing/PesertaController.php` (baris 160–175), ditemukan **kesalahan logika polaritas perbandingan tanggal Carbon**:

```php
// KODE BUG SAAT INI (PesertaController.php):
$totalHariMagang = max(1, $penempatan->tgl_mulai->diffInDays($penempatan->tgl_selesai));
$hariBerjalan = Carbon::today()->diffInDays($penempatan->tgl_mulai, false); // <-- BUG POLARITAS DI SINI!
if ($hariBerjalan < 0) {
    $progresHari = 0;
} else {
    $progresHari = min(100, (int) round(($hariBerjalan / $totalHariMagang) * 100));
}
$sisaHari = Carbon::today()->diffInDays($penempatan->tgl_selesai, false);
```

**Mekanisme Bug:**
1. Metode `$dateA->diffInDays($dateB, false)` dalam Carbon:
   - Jika `$dateA` adalah `today()` (`09 Nov 2026`) dan `$dateB` adalah `$tgl_mulai` (`01 Nov 2026`), karena `$tgl_mulai` berada di **masa lalu**, Carbon mengembalikan bilangan **negatif**: `-8`.
2. Akibatnya, kondisi `if ($hariBerjalan < 0)` selalu bernilai **TRUE** (`-8 < 0`) untuk setiap peserta yang magangnya sedang berjalan!
3. Variabel `$progresHari` dipaksa menjadi **`0`**.
4. Di file Blade (`resources/views/pembimbing/peserta/show.blade.php`), markup membaca:
   ```blade
   <div class="periode-progress-track">
       <div class="periode-progress-fill" style="width: {{ min(100, $progresHari) }}%;"></div>
   </div>
   ```
   Karena `$progresHari = 0`, elemen dirender sebagai `style="width: 0%;"`, sehingga fill tidak memiliki lebar sama sekali (0 pixel) dan bar terlihat kosong melompong.

---

## 2. Solusi Teknis & Rencana Perbaikan

### 2.1 Perbaikan Logika Controller (`PesertaController.php`)
Logika kalkulasi durasi magang diperbaiki dengan memperhitungkan 3 kondisi waktu nyata secara tegas:
1. **Belum Mulai** (`today < tgl_mulai`):
   - `$hariBerjalan = 0`
   - `$progresHari = 0`
   - `$sisaHari = $totalHariMagang`
2. **Sedang Berjalan** (`tgl_mulai <= today < tgl_selesai`):
   - `$hariBerjalan = $mulai->diffInDays($today)` (selalu positif)
   - `$progresHari = min(100, (int) round(($hariBerjalan / $totalHariMagang) * 100))`
   - `$sisaHari = max(0, $today->diffInDays($selesai))`
3. **Sudah Selesai / Lampau** (`today >= tgl_selesai`):
   - `$hariBerjalan = $totalHariMagang`
   - `$progresHari = 100`
   - `$sisaHari = 0`

### 2.2 Penyempurnaan Visual Bar (`peserta-show.css`)
- Meningkatkan ketebalan track `.periode-progress-track` dari `6px` menjadi **`8px`** agar sejajar dan konsisten dengan ketebalan bar segmented kanban (`.kanban-segmented-bar`), serta memberikan visibilitas yang lebih tegas di atas kartu kuning.
- Memastikan warna fill `.periode-progress-fill` tetap menggunakan navy pekat `var(--mentee-text-navy)` (`#0F172A`) dengan kontras tinggi terhadap latar belakang kartu amber `#F59E0B`.

---

## 3. Matriks Hasil Simulasi Tanggal

| Skenario Tanggal | tgl_mulai | tgl_selesai | Today | Hari Berjalan | Total Hari | Sisa Hari | Progres Hari (%) | Tampilan Bar |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| **Kasus User (Aktif)** | 01 Nov 2026 | 31 Jan 2027 | 09 Nov 2026 | 8 hari | 91 hari | 83 hari | **9%** | Bar terisi 9% (navy) |
| **Pertengahan Magang** | 01 Nov 2026 | 31 Jan 2027 | 15 Des 2026 | 44 hari | 91 hari | 47 hari | **48%** | Bar terisi 48% (navy) |
| **Belum Dimulai** | 01 Des 2026 | 28 Feb 2027 | 09 Nov 2026 | 0 hari | 89 hari | 89 hari | **0%** | Bar kosong (0%) |
| **Selesai / Lampau** | 01 Ags 2026 | 31 Okt 2026 | 09 Nov 2026 | 92 hari | 92 hari | 0 hari | **100%** | Bar terisi penuh (100%) |

---

## 4. Instruksi Langkah Demi Langkah untuk Junior Developer / AI

### Langkah 1: Perbarui Logika di `PesertaController.php`
Buka file `app/Http/Controllers/Pembimbing/PesertaController.php` pada method `show()` (sekitar baris 160–178), ganti blok kalkulasi lama dengan kode berikut:

```php
        // Hitung durasi dan progres waktu magang (Beban & Kalender MagKom)
        $progresHari = 0;
        $totalHariMagang = 1;
        $sisaHari = 0;

        if ($penempatan->tgl_mulai && $penempatan->tgl_selesai) {
            $mulai = $penempatan->tgl_mulai->copy()->startOfDay();
            $selesai = $penempatan->tgl_selesai->copy()->startOfDay();
            $today = Carbon::today();

            $totalHariMagang = max(1, $mulai->diffInDays($selesai));

            if ($today->lt($mulai)) {
                // Magang belum dimulai
                $hariBerjalan = 0;
                $progresHari = 0;
                $sisaHari = $totalHariMagang;
            } elseif ($today->gte($selesai)) {
                // Magang telah selesai
                $hariBerjalan = $totalHariMagang;
                $progresHari = 100;
                $sisaHari = 0;
            } else {
                // Magang sedang berjalan aktif
                $hariBerjalan = $mulai->diffInDays($today);
                $progresHari = min(100, (int) round(($hariBerjalan / $totalHariMagang) * 100));
                $sisaHari = max(0, $today->diffInDays($selesai));
            }
        }
```

### Langkah 2: Tingkatkan Keterbacaan Track Bar di `peserta-show.css`
Buka file `resources/css/pages/pembimbing/peserta-show.css` sekitar baris 240, perbarui rule `.periode-progress-track`:

```css
.periode-progress-track {
    width: 100%;
    height: 8px; /* Ditingkatkan dari 6px menjadi 8px */
    border-radius: 9999px;
    background-color: rgba(15, 23, 42, 0.16);
    overflow: hidden;
    margin-bottom: 0.75rem;
}

.periode-progress-fill {
    height: 100%;
    background-color: var(--mentee-text-navy);
    border-radius: 9999px;
    transition: width 0.3s ease;
}
```

### Langkah 3: Kompilasi Asset Vite
Jalankan perintah build frontend:
```bash
npm run build
```

### Langkah 4: Tambahkan Pengujian Unit / Feature Test
Buka `tests/Feature/PembimbingPortalStandardizationTest.php`, pastikan ada assertion spesifik untuk memastikan bar periode terisi proporsional:
```php
$response->assertSee('class="periode-progress-fill"', false);
$response->assertDontSee('style="width: 0%;"'); // Pada mentee aktif yang sudah berjalan 10 hari
```

Jalankan test suite menggunakan runtime PHP 8.3:
```bash
& "F:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe" artisan test tests/Feature/PembimbingPortalStandardizationTest.php
```

---

## 5. Kriteria Penerimaan (Acceptance Criteria)

- [ ] Pada kasus mentee aktif (seperti Naufal Pratama), `$progresHari` bernilai positif (misal 9%–11%), bukan 0%.
- [ ] Elemen `.periode-progress-fill` dirender dengan atribut `style="width: X%;"` di mana $X > 0$.
- [ ] Bilah pengisi berwarna navy `#0F172A` terlihat jelas mengisi bagian kiri rel progress bar.
- [ ] Ketebalan bar ditingkatkan menjadi `8px` untuk keterbacaan optimal.
- [ ] File Blade `show.blade.php` tidak mengalami penambahan inline style baru atau tag `<script>` (Read-Only Blade rule terpenuhi).
- [ ] `npm run build` berhasil 100%.
- [ ] Seluruh unit dan feature test PHPUnit lolos 100%.

