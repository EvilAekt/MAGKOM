# Rencana Kerja: Penyatuan 4 KPI Metrics Menjadi Single Unified Card (Gaya Dashboard Peserta)

Dokumen ini merinci rencana perubahan 4 kartu metrik operasional yang saat ini masih terpisah-pisah menjadi **1 Panel Kartu Metrik Terpadu (*Unified KPI Strip Card*)** persis seperti yang digunakan pada Dashboard Peserta MagKom.

---

## 1. Analisis Kebutuhan

- **Permasalahan Saat Ini**: 4 kartu metrik ditampilkan sebagai 4 kotak terpisah yang mengambang dengan aksen garis warna di atasnya. Hal ini membuat halaman terasa ramai oleh sekat kotak-kotak.
- **Solusi yang Diinginkan**: Menyatukan ke-4 metrik ke dalam **satu panel kontainer putih tunggal** (`.kpi-unified-card`) dengan pembatas garis vertikal halus di antara metrik, latar hover lembut, dan tipografi yang proporsional, persis seperti modul metrik di dashboard peserta.

---

## 2. Struktur Komponen yang Direncanakan

```
+-------------------------------------------------------------------------------------------------------------+
|                                        PANEL METRIK TERPADU (UNIFIED CARD)                                  |
|                                                                                                             |
|  [ICON AMBER]                  | [ICON BLUE]                   | [ICON PURPLE]                 | [ICON EMERALD]     |
|  MENUNGGU VERIFIKASI           | REVISI DOKUMEN                | ANTREAN CADANGAN              | PESERTA AKTIF      |
|  1                             | 0                             | 0                             | 1                  |
|  Berkas baru masuk             | Menunggu revisi pemohon       | Daftar tunggu antrean         | Sedang bertugas    |
|                                |                               |                               |                    |
+-------------------------------------------------------------------------------------------------------------+
```

### Detail Visual:
1. **Kontainer Tunggal (`.adm-kpi-unified-card`)**:
   - Background `#FFFFFF`, border halus `#E2E8F0`, rounded corners `14px`, bayangan lembut `0 1px 3px rgba(15, 23, 42, 0.05)`.
   - Grid layout `grid-template-columns: repeat(4, 1fr)`.
2. **Item Metrik (`.adm-kpi-unified-item`)**:
   - Layout horizontal ergonomis: Icon box di sebelah kiri (dengan background pastel lembut), teks label, nilai angka, dan subteks di sebelah kanan.
   - Efek hover halus pada masing-masing segmen item (`#F8FAFC`).
3. **Pemisah Garis Vertikal Halus (`::after`)**:
   - Garis pembatas tipis `1px` berwarna `#E2E8F0` dengan tinggi `65%` di antara kolom ke-1, 2, dan 3 (kolom terakhir tanpa garis).
4. **Responsivitas**:
   - Layar lebar (>900px): 4 kolom sejajar dalam 1 panel.
   - Layar tablet (600px - 900px): 2 baris x 2 kolom dengan pembatas garis yang rapi.
   - Layar mobile (<600px): 1 kolom vertikal yang mulus.

---

## 3. File yang Terdampak

| No | File | Deskripsi Perubahan |
|----|------|---------------------|
| 1 | [`resources/views/admin/dashboard.blade.php`](file:///d:/MAGKOM/MAGKOM/resources/views/admin/dashboard.blade.php) | Mengganti styling `.adm-kpi-bar` dan `.adm-kpi-item` menjadi `.adm-kpi-unified-card` dan `.adm-kpi-unified-item` dengan pembatas garis vertikal ala dashboard peserta. |

---

## 4. Definition of Done (DoD)

1. Keempat metrik menyatu dalam 1 kontainer kartu putih elegan tanpa celah antar-kotak.
2. Terdapat garis pemisah vertikal tipis yang rapi di antara metrik.
3. Nilai data dinamis dan penanda SLA tetap berfungsi normal 100%.
4. Responsif di seluruh resolusi layar (desktop, tablet, mobile).
