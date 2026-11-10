# PRD & Implementation Plan: Fitur Deselect / Double-Click Toggle Border Kalender (Senior Dev Handover)

**Target Audience:** Junior Developers & Lightweight AI (Gemini 3.8 Flash, Claude Instant, etc.)  
**Document Type:** Senior Developer Task Brief / Technical PRD & Implementation Plan  
**Repository:** MagKom (Magang Kominfo Sleman)  
**Baseline Stack:** PHP 8.3 | Laravel 10 | Blade | Vanilla CSS | Vanilla JS (ES Modules) | Vite  
**File Target:**  
1. `resources/js/pages/pembimbing/dashboard.js` (Kalender Bento Pembimbing)  
2. `resources/js/pages/superadmin/dashboard.js` (Kalender Aside SuperAdmin)  
**Modifikasi Blade & CSS:** 0% (Tidak ada perubahan file Blade atau CSS)  
**Status:** Completed & Verified  

---

## 1. Executive Summary & Problem Context

Pada antarmuka kalender mini sebelumnya (Dashboard Pembimbing & Dashboard SuperAdmin), ketika pengguna mengklik salah satu sel tanggal (misalnya tanggal **11** pada tangkapan layar `media_1794039573016_c2246537.png`), elemen tanggal tersebut menerima kelas `.date-selected`.

Kelas `.date-selected` menerapkan cincin batas luar (*2px solid navy outline ring*) dengan bayangan halus (*box-shadow ring*):
```css
.cal-date.date-selected:not(.date-today) {
  background: #E8EFFB !important;
  border: 2px solid #003566 !important;
  color: #003566 !important;
  box-shadow: 0 0 0 2px rgba(0, 53, 102, 0.15) !important;
}
```

### Masalah yang Ditemukan (User Pain Point):
Logika kalender sebelumnya hanya menyediakan mekanisme **memilih tanggal baru**, tetapi **TIDAK menyediakan cara untuk membatalkan pilihan (deselect)**. Sekali tanggal diklik, garis border navy tebal tersebut terus menempel dan tidak bisa dihilangkan.

### Target Solusi yang Diimplementasikan:
Menyediakan kemampuan di mana **ketika tanggal yang sedang aktif di-klik dua kali (*double click*) atau di-klik ulang (*toggle re-click*)**, garis border pada angka tersebut **langsung hilang seketika** (kembali ke tampilan sel kapsul normal tanpa border seleksi).

---

## 2. Visual Anatomy (Before vs After)

```
[ KONDISI SEBELUMNYA ]
Klik tanggal 11   --> Tanggal 11 terpilih [ ( 11 ) ] dengan border tebal 2px navy.
Klik tanggal 11 lagi / Double-click --> TIDAK TERJADI APA-APA. Border tetap menempel permanen.

[ KONDISI TERVERIFIKASI SETELAH IMPLEMENTASI ]
Klik tanggal 11   --> Tanggal 11 terpilih [ ( 11 ) ] dengan border tebal 2px navy.
Klik tanggal 11 lagi / Double-click --> Border seketika HILANG! Tanggal 11 kembali menjadi kapsul polos [ 11 ].
```

---

## 3. Detail Arsitektur Logika (Untuk Junior Dev & Budget AI)

### A. Konsep State
Di dalam fungsi `initBentoCalendar()` (dan `initSuperAdminCalendar()`), state tanggal terpilih disimpan dalam variabel:
```javascript
let selectedDate = null; // null jika tidak ada tanggal yang dipilih, atau { year, month, date }
```

### B. Mekanisme Dual-Trigger (Click-Toggle + DblClick Event)
1. **Single Click pada tanggal yang SAMA (Toggle Re-click):**  
   Jika pengguna mengklik tanggal yang saat itu sudah berstatus `selectedDate`, sistem mendeteksi `isAlreadySelected === true` lalu menghapus seleksi (`selectedDate = null`) dan melepas kelas `date-selected`.
2. **Double Click Event (`dblclick`):**  
   Menambahkan event listener eksplisit `dblclick` pada tombol tanggal. Ketika di-klik dua kali secara cepat, seleksi langsung dibatalkan (`selectedDate = null`), kelas `date-selected` dicopot, dan `e.preventDefault()` dipanggil agar browser tidak memblok/menyorot teks angka.

---

## 4. Spesifikasi Perubahan Kode Terperinci (Telah Terpasang)

### Task 1: Kalender Bento Pembimbing
* **File Target:** `resources/js/pages/pembimbing/dashboard.js`
* **Lokasi Baris:** Fungsi `renderGrid()` di dalam `initBentoCalendar()`

```javascript
            // Logika Klik Tunggal: Pilih Tanggal atau Batalkan Pilihan (Toggle) jika diklik ulang
            btn.addEventListener('click', () => {
                const isAlreadySelected = selectedDate &&
                    selectedDate.year === viewYear &&
                    selectedDate.month === viewMonth &&
                    selectedDate.date === d;

                if (isAlreadySelected) {
                    selectedDate = null;
                    btn.classList.remove('date-selected', 'sa-date-pop');
                } else {
                    selectedDate = { year: viewYear, month: viewMonth, date: d };
                    calendarGrid.querySelectorAll('.cal-date').forEach((el) => {
                        el.classList.remove('date-selected', 'sa-date-pop');
                    });
                    btn.classList.add('date-selected', 'sa-date-pop');
                }
            });

            // Logika Dobel Klik (Double-Click): Pastikan border seleksi langsung hilang seketika
            btn.addEventListener('dblclick', (e) => {
                e.preventDefault();
                selectedDate = null;
                calendarGrid.querySelectorAll('.cal-date').forEach((el) => {
                    el.classList.remove('date-selected', 'sa-date-pop');
                });
            });
```

---

### Task 2: Kalender Aside SuperAdmin
* **File Target:** `resources/js/pages/superadmin/dashboard.js`
* **Lokasi Baris:** Fungsi `renderGrid()` di dalam `initSuperAdminCalendar()`

```javascript
        btn.addEventListener('click', (function (dayNum) {
          return function () {
            var isAlreadySelected = selectedDate &&
              selectedDate.year === viewYear &&
              selectedDate.month === viewMonth &&
              selectedDate.date === dayNum;

            if (isAlreadySelected) {
              selectedDate = null;
              this.classList.remove('date-selected', 'sa-date-pop');
            } else {
              selectedDate = { year: viewYear, month: viewMonth, date: dayNum };
              grid.querySelectorAll('.sa-cal-date').forEach(function (el) {
                el.classList.remove('date-selected', 'sa-date-pop');
              });
              this.classList.add('date-selected', 'sa-date-pop');
            }
          };
        })(d));

        btn.addEventListener('dblclick', function (e) {
          e.preventDefault();
          selectedDate = null;
          grid.querySelectorAll('.sa-cal-date').forEach(function (el) {
            el.classList.remove('date-selected', 'sa-date-pop');
          });
        });
```

---

## 5. Langkah Implementasi Bertahap (Checklist Terverifikasi)

- [x] **Langkah 1**: Perbarui event listener pada [pembimbing/dashboard.js](file:///g:/MAGKOM/resources/js/pages/pembimbing/dashboard.js) dengan menyematkan logika toggle klik dan event `dblclick`.
- [x] **Langkah 2**: Perbarui event listener pada [superadmin/dashboard.js](file:///g:/MAGKOM/resources/js/pages/superadmin/dashboard.js) dengan pola yang identik.
- [x] **Langkah 3**: Jalankan `npm run build` untuk memverifikasi bahwa bundling Vite sukses tanpa syntax error (Selesai dalam 631ms).
- [x] **Langkah 4**: Jalankan test otomatis `php artisan test --filter=PembimbingDashboardEnhancementTest` (7 passed) dan `php artisan test --filter=AdminDashboardEnhancementTest` (8 passed).
- [x] **Langkah 5**: Lakukan verifikasi manual pada browser:
  - Klik tanggal 11 $\rightarrow$ Border navy tebal muncul.
  - Dobel klik / klik ulang tanggal 11 $\rightarrow$ Border navy seketika hilang.
  - Klik tanggal hari ini $\rightarrow$ Border emas muncul.
  - Dobel klik / klik ulang tanggal hari ini $\rightarrow$ Border emas hilang dan kembali ke kapsul hari ini normal.

---

## 6. Kriteria Selesai (Definition of Done)

- [x] Sel tanggal kalender dapat dipilih dengan 1 kali klik (border muncul).
- [x] Sel tanggal kalender dapat dibatalkan pilihannya dengan 2 kali klik / dobel klik (border hilang).
- [x] State `selectedDate` di-reset menjadi `null` saat seleksi dibatalkan.
- [x] Berfungsi konsisten pada Dashboard Pembimbing dan Dashboard SuperAdmin.
- [x] Nol perubahan pada file Blade dan file CSS.
- [x] `npm run build` sukses 100%.
- [x] Seluruh test `php artisan test` tetap hijau (passing).
