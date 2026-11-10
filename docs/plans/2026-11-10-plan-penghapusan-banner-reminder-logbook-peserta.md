# Rencana Kerja (Plan): Penghapusan Banner Pengingat Pengisian Logbook Peserta

**Tanggal Dibuat:** 10 November 2026  
**Status:** Selesai Diimplementasikan (Verified)  
**Tujuan Dokumen:** Perencanaan terstruktur untuk menghapus tampilan banner pengingat (*Smart Reminder Banner*) *"Saatnya Mengisi Logbook Minggu ke-X"* pada halaman Logbook Peserta (`/peserta/logbook`) sesuai tangkapan layar pengguna (`media_1794311087790_2f98f8a1.png`).

---

## 1. Analisis Komponen Sasaran

Berdasarkan investigasi pada basis kode:
- **Komponen**: *Smart Reminder Banner* pengingat logbook mingguan.
- **Lokasi Berkas**: [`resources/views/peserta/logbook/index.blade.php`](file:///d:/MAGKOM/MAGKOM/resources/views/peserta/logbook/index.blade.php#L112-L136) (baris 112 s/d 136).
- **Struktur Markup**:
  ```blade
  {{-- Smart Reminder Banner --}}
  @if(!$mingguAktifSudahDiisi && $penempatan)
      <div class="card-modern" style="background: #EFF6FF; border: 1px solid #BFDBFE; border-left: 4px solid #01347C; margin-bottom: 24px; padding: 18px 24px;">
          <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
              <div style="display: flex; align-items: flex-start; gap: 14px; min-width: 0; flex: 1;">
                  <div style="width: 40px; height: 40px; border-radius: 10px; background: #DBEAFE; color: #01347C; ...">
                      ... (Ikon Jam) ...
                  </div>
                  <div>
                      <strong>Saatnya Mengisi Logbook Minggu ke-{{ $mingguAktif }}</strong>
                      <p>Laporkan ringkasan kegiatan dan capaian magang Anda pekan ini...</p>
                  </div>
              </div>
              <button type="button" class="btn btn-primary" onclick="openTambahModal({{ $mingguAktif }})">
                  + Isi Logbook Minggu Ini
              </button>
          </div>
      </div>
  @endif
  ```
- **Alasan Penghapusan**:
  - Halaman telah memiliki navigasi **Stepper Mingguan (M1 s/d M13)** yang interaktif di bagian atas.
  - Kartu **Riwayat Jurnal Mingguan** tepat di bawahnya sudah memiliki tombol aksi utama yang sangat jelas dan menonjol: **`+ Isi Logbook Mingguan`** berlatar emas (`.btn-tambah-logbook-gold`).
  - Banner biru ini menjadi redundan, memakan ruang vertikal, dan membuat tampilan terkesan penuh (*cluttered*).

---

## 2. Tujuan & Sasaran Perubahan

1. Menghapus banner pengingat *"Saatnya Mengisi Logbook Minggu ke-X"* secara bersih dari tampilan `/peserta/logbook`.
2. Menyajikan antarmuka yang lebih ringkas, fokus, dan lapang langsung ke tabel/daftar Riwayat Jurnal Mingguan.
3. Memastikan fungsi pengisian logbook tetap 100% normal melalui tombol **`+ Isi Logbook Mingguan`** maupun klik pada Stepper Mingguan.
4. Memastikan tidak ada pengujian fitur (*feature test*) yang terganggu atau rusak.

---

## 3. Asumsi & Kepatuhan Aturan Blade

Sesuai aturan repositori pada `AGENTS.md` Pasal 3.3 (*Pengecualian Perubahan Blade*):
- Penghapusan elemen antarmuka yang dirender langsung di file Blade memerlukan konfirmasi eksplisit pengguna sebelum memodifikasi file Blade.
- Perubahan dibatasi secara ketat hanya pada penghapusan blok baris 112–136 di [`resources/views/peserta/logbook/index.blade.php`](file:///d:/MAGKOM/MAGKOM/resources/views/peserta/logbook/index.blade.php).
- Tidak ada penambahan inline script atau style baru.

---

## 4. Evaluasi Opsi Pendekatan Teknis

### Opsi A (Direkomendasikan): Penghapusan Bersih Blok Markup Blade
- **Deskripsi**: Menghapus blok `@if(!$mingguAktifSudahDiisi && $penempatan) ... @endif` dari berkas Blade.
- **Kelebihan**:
  - Bersih, tidak meninggalkan *dead code* atau sisa markup inline style di DOM HTML.
  - Mengurangi beban render dan ukuran payload HTML server.
  - Langsung menyelesaikan permintaan pengguna secara permanen.
- **Kekurangan**: Memerlukan modifikasi file Blade (memerlukan persetujuan eksplisit).

### Opsi B: Menyembunyikan via Aturan CSS (`display: none;`)
- **Deskripsi**: Menambahkan aturan CSS scoped untuk menyembunyikan banner tersebut.
- **Kelebihan**: Tidak menyentuh file Blade.
- **Kekurangan**: Elemen HTML dan inline styles tetap di-render dan dikirim dari server, meninggalkan markup mubazir (*dirty DOM*).

> **Rekomendasi**: Dipilih **Opsi A** untuk kebersihan kode jangka panjang dan integritas rendering antarmuka.

---

## 5. Rincian Perubahan Berkas

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| [`resources/views/peserta/logbook/index.blade.php`](file:///d:/MAGKOM/MAGKOM/resources/views/peserta/logbook/index.blade.php) | Modify | Menghapus blok *Smart Reminder Banner* (baris 112–136) yang memuat teks *"Saatnya Mengisi Logbook Minggu ke-..."* dan tombol *"+ Isi Logbook Minggu Ini"*. |

---

## 6. Langkah Implementasi Terstruktur

- [x] **Langkah 1**: Backup & verifikasi baris sasaran di `resources/views/peserta/logbook/index.blade.php`.
- [x] **Langkah 2**: Hapus blok `@if(!$mingguAktifSudahDiisi && $penempatan) ... @endif` secara presisi.
- [x] **Langkah 3**: Bersihkan cache view Laravel (`php artisan view:clear`).
- [x] **Langkah 4**: Jalankan linter format Laravel Pint (`./vendor/bin/pint --test`).
- [x] **Langkah 5**: Jalankan automated test feature terkait logbook peserta (`php artisan test tests/Feature/PesertaLogbookRevampTest.php` dan `tests/Feature/PesertaClassroomTugasLogbookTest.php`).

---

## 7. Strategi Testing

1. **Automated Feature Test**:
   - `php artisan test tests/Feature/PesertaLogbookRevampTest.php`
   - `php artisan test tests/Feature/PesertaClassroomTugasLogbookTest.php`
   - Memastikan hak akses, rendering halaman, dan flow penyimpanan logbook peserta tetap 100% lulus (PASS).
2. **Visual Inspection**:
   - Membuka halaman `/peserta/logbook` di browser dan memverifikasi banner pengingat biru sudah tidak muncul.
   - Memastikan tombol emas `+ Isi Logbook Mingguan` dan Stepper Mingguan tetap berfungsi normal membuka modal pengisian.

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Banner pengingat *"Saatnya Mengisi Logbook Minggu ke-X"* terhapus secara menyeluruh dari halaman logbook peserta.
- [x] Tata letak langsung menyambung dari Stepper Mingguan ke Card Riwayat Jurnal Mingguan dengan rapi.
- [x] Cache view dibersihkan dan aset tetap berfungsi normal.
- [x] Seluruh automated test logbook lulus hijau tanpa regresi.
- [ ] Pengguna mengonfirmasi tampilan bersih telah sesuai kebutuhan.

