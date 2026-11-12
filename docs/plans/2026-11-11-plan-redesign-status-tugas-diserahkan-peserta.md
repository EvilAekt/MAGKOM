# Rencana Kerja: Redesain Status Tugas Selesai/Diserahkan (Google Classroom Simple Style)

**Tanggal**: 11 November 2026  
**Status**: Menunggu Konfirmasi Persetujuan Pengguna  
**Dokumen Referensi**: `docs/plans/2026-11-11-plan-redesign-status-tugas-diserahkan-peserta.md`  
**Fitur**: Portal Peserta — Status Penyerahan & Selesai Tugas (`resources/views/peserta/tugas/show.blade.php`)  
**Konsep Desain**: *Google Classroom Minimalist Parity* (Simpel, bersih, tidak kaku, tanpa kotak tebal yang mencolok)

---

## 1. Analisis & Arahan Pengguna ("Kaya Classroom Simple")

Berdasarkan masukan pengguna:
- Kotak hijau lama dinilai **masih kaku** karena menggunakan kotak tebal bergaya alert Bootstrap yang terisolasi dan mencolok.
- Pengguna menginginkan tampilan **simpel persis seperti Google Classroom**:
  - **Bersih & Ringan (*Clean & Lightweight*)**: Tidak menggunakan kotak hijau pekat yang tebal dan kaku.
  - **Tipografi Alami**: Status waktu penyerahan dan informasi tugas ditampilkan secara tenang, ringkas, dan proporsional.
  - **Ikonografi Halus**: Mikro-ikon centang melingkar kecil yang lembut (`20px x 20px`) menggantikan teks mentah `✓`.
  - **Tombol & Catatan Rapi**: Tombol *"Batalkan pengiriman"* (Unsubmit) bergaya Google Classroom outline pill dengan font weight 600 dan teks petunjuk halus di bawahnya.
  - **Harmonisasi Kondisi Selesai (`$isSelesai`)**: Menghilangkan `style="..."` inline lama dan menyelaraskannya dengan strip Google Classroom yang simpel dan bersih.

---

## 2. Mockup Desain Sederhana (Classroom Simple)

```
┌────────────────────────────────────────────────────────┐
│ Tugas Anda                                  Diserahkan │
├────────────────────────────────────────────────────────┤
│ ┌───────────────────────────────────────────────┬────┐ │
│ │ Buka Tautan Tugas ↗                           │ 🔗 │ │
│ │ https://www.youtube.com/watch?...             │    │ │
│ └───────────────────────────────────────────────┴────┘ │
│                                                        │
│ ┌────────────────────────────────────────────────────┐ │
│ │  (✓) Tugas Telah Diserahkan                        │ │
│ │      Diserahkan pada 11 Nov 2026, 10.44 WIB        │ │
│ └────────────────────────────────────────────────────┘ │
│                                                        │
│ ┌────────────────────────────────────────────────────┐ │
│ │                Batalkan pengiriman                 │ │
│ └────────────────────────────────────────────────────┘ │
│    Batalkan pengiriman untuk menambah/ubah lampiran    │
└────────────────────────────────────────────────────────┘
```
*Catatan: Strip status memiliki latar netral lembut (`#F8FAFC` dengan aksen border tipis `#E2E8F0` atau tint emerald sangat halus `#F0FDF4`), sudut lengkung `8px-10px`, padding ringkas `10px 14px`, tidak ada warna hijau mencolok yang kaku.*

---

## 3. Rincian File yang Diubah

| File | Aksi | Rincian Perubahan |
| :--- | :--- | :--- |
| `resources/views/peserta/tugas/show.blade.php` | Modify | 1. Memperbarui markup `.submitted-waiting-box` dengan struktur simpel: mikro-ikon centang SVG + teks `Tugas Telah Diserahkan` + timestamp bersih.<br>2. Membersihkan inline `style="..."` pada kondisi `$isSelesai` dan menggantinya dengan kelas CSS terkelola `.submitted-completed-box`.<br>3. Mempertahankan 100% teks kontrak test (`Tugas Telah Diserahkan` dan `Batalkan pengiriman`). |
| `resources/css/pages/peserta/tugas-detail.css` | Modify | 1. Menata ulang `.submitted-waiting-box` dan `.submitted-completed-box` agar ramping, simpel, tidak kaku, dengan padding `10px 14px`, latar netral/lembut, dan border halus.<br>2. Menata tipografi judul status (`0.8125rem`, semi-bold) dan waktu lapor (`0.75rem`, netral).<br>3. Menyesuaikan styling tema gelap `[data-theme="dark"]`. |

---

## 4. Langkah Implementasi (Checklist)

- [ ] **Langkah 1**: Perbarui markup Blade pada `resources/views/peserta/tugas/show.blade.php`:
  - Format status penyerahan dengan icon centang melingkar halus dan teks bersih.
  - Hapus inline `style` pada blok `$isSelesai`.
- [ ] **Langkah 2**: Tulis styling Google Classroom simple pada `resources/css/pages/peserta/tugas-detail.css`:
  - Buat strip status minimalis, padding proporsional, border tipis `#E2E8F0` / `#DCFCE7`, border radius `10px`.
  - Harmonisasikan tombol `.btn-batalkan-pengiriman` dan `.work-caption-note`.
  - Tambahkan penanganan tema gelap `[data-theme="dark"]`.
- [ ] **Langkah 3**: Kompilasi aset Vite dengan `npm run build`.
- [ ] **Langkah 4**: Bersihkan view cache dengan `php artisan view:clear`.
- [ ] **Langkah 5**: Jalankan test suite otomatis:
  - `php artisan test tests/Feature/PesertaTugasClassroomUploadTest.php tests/Feature/PesertaTugasBatalKirimTest.php`
- [ ] **Langkah 6**: Jalankan verifikasi formatting Pint (`./vendor/bin/pint --test`).

---

## 5. Pertanyaan Konfirmasi

Apakah Anda menyetujui rencana konsep minimalis ala Google Classroom ini? Silakan tekan tombol **Proceed** pada artefak atau ketik **"gas"** / **"lanjutkan"**.
