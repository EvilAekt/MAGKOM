# Rencana Kerja: Penyederhanaan Kolom Status Tabel Antrean Pendaftaran

**Tanggal:** 7 November 2026  
**Target File:** `resources/views/admin/pendaftaran/index.blade.php`  
**Tujuan:** Menyederhanakan tampilan kolom "Status" pada tabel antrean pendaftaran peserta magang verifikator/admin agar hanya menampilkan badge status (seperti *Diajukan*, *Diterima*, *Dibatalkan*, dll.) secara mandiri dan bersih tanpa teks keterangan tanggal sekunder di bawahnya (`Masuk: 17 Okt 2026`, `Diterima 20 Okt 2026`, dsb.) sesuai instruksi dan screenshot pengguna.

---

## 1. Analisis Kebutuhan Pengguna

### Berdasarkan Gambar yang Diunggah Pengguna (`media_1794049395990_cb2389ac.png`):
Kolom **STATUS** saat ini menampilkan:
1. Baris 1: Badge `• Diajukan`, dengan teks di bawahnya: `06 Nov 2026 • Hari ini`
2. Baris 2: Badge `• Dibatalkan`, dengan teks di bawahnya: `Masuk: 19 Okt 2026`
3. Baris 3: Badge `• Diterima`, dengan teks di bawahnya:
   - `Masuk: 17 Okt 2026`
   - `Diterima 20 Okt 2026` (hijau)
4. Baris 4: Badge `• Diterima`, dengan teks di bawahnya:
   - `Masuk: 26 Sep 2026`
   - `Diterima 02 Okt 2026` (hijau)

### Permintaan Khusus Pengguna:
1. **Hanya Menampilkan Status**: Kolom Status hanya menampilkan badge status saja (*Diajukan*, *Diterima*, *Dibatalkan*, serta status permohonan lainnya) tanpa teks anak/subtitle di bawahnya.
2. **Hapus Teks Tanggal**: Menghapus seluruh baris keterangan tanggal (`Masuk: ...`, `Diterima ...`, `Ditolak ...`, dan `... • Hari ini`).

---

## 2. Asumsi Kerja & Penyelarasan Desain

1. **Kelengkapan Data Tetap Terjamin**: Seluruh riwayat tanggal (tanggal diajukan, tanggal verifikasi keputusan, tanggal perbaikan) tetap tersimpan aman di database dan tampil lengkap pada halaman rincian berkas (`admin.pendaftaran.show`) melalui timeline status dan kartu informasi.
2. **Dampak Estetika Baris Tabel**: Penghapusan baris tanggal sekunder membuat tinggi tiap baris tabel (*row height*) menjadi seragam, vertikal seimbang (*aligned*), dan tidak memakan banyak ruang vertikal pada layar admin/verifikator (*compact & clean layout*).
3. **Kepatuhan Status Enum**: Badge tetap menggunakan komponen design system `cv-status-dot-badge` dengan indikator dot berwarna sesuai status (`diajukan` = kuning/amber, `diterima` = hijau/emerald, `dibatalkan`/`ditolak` = merah/rose, dll.).

---

## 3. Evaluasi Opsi Teknis

| Pendekatan | Kelebihan | Kekurangan | Rekomendasi |
| :--- | :--- | :--- | :---: |
| **Opsi A: Hapus markup `<div style="margin-top: 0.35rem;">` di Blade** | DOM lebih ramping, tidak ada kalkulasi tanggal redundan di perulangan `@foreach`, clean code PSR & Blade guidelines. | Membutuhkan sentuhan markup Blade minimal. | **Pilihan Terbaik (Rekomendasi)** |
| **Opsi B: Sembunyikan via CSS `display: none`** | Markup tetap ada di DOM. | Kode DOM menjadi membengkak (*bloat*) dan kalkulasi tanggal tetap berjalan sia-sia di backend render. | Tidak Direkomendasikan |

---

## 4. Rencana Perubahan Kode

### Lokasi Target
[`resources/views/admin/pendaftaran/index.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/pendaftaran/index.blade.php) pada blok `<td>` Status (sekitar baris 1127–1169).

### Markup Saat Ini:
```blade
                                    {{-- Status Permohonan & Tanggal Masuk --}}
                                    <td>
                                        <span class="cv-status-dot-badge {{ $statusDotType }}">
                                            <span class="dot"></span>
                                            <span>{{ $item->status->label() }}</span>
                                        </span>
                                        @php
                                            $tglMasuk = $item->diajukan_at ? $item->diajukan_at->translatedFormat('d M Y') : ($item->created_at?->translatedFormat('d M Y') ?? '-');
                                        @endphp
                                        <div style="margin-top: 0.35rem;">
                                            @if($val === 'diajukan')
                                                <div style="font-size: 0.725rem; color: #64748B; display: flex; align-items: center; gap: 0.35rem; flex-wrap: wrap;">
                                                    <span>{{ $tglMasuk }}</span>
                                                    <span style="color: #CBD5E1;">•</span>
                                                    @if($slaDays !== null && $slaDays >= 5)
                                                        <span style="font-weight: 700; color: #DC2626;">⚠️ SLA ({{ $slaDays }} hari)</span>
                                                    @elseif($slaDays !== null && $slaDays >= 3)
                                                        <span style="font-weight: 700; color: #D97706;">⏳ {{ $slaDays }} hari lalu</span>
                                                    @else
                                                        <span>{{ $slaDays === 0 ? 'Hari ini' : ($slaDays ? $slaDays . ' hari lalu' : 'Hari ini') }}</span>
                                                    @endif
                                                </div>
                                            @elseif($val === 'diterima' && $item->verifikasi_at)
                                                <div style="font-size: 0.725rem; color: #64748B;">
                                                    <div>Masuk: {{ $tglMasuk }}</div>
                                                    <div style="color: #16A34A; font-weight: 600; margin-top: 0.1rem;">
                                                        Diterima {{ $item->verifikasi_at->translatedFormat('d M Y') }}
                                                    </div>
                                                </div>
                                            @elseif($val === 'ditolak' && $item->verifikasi_at)
                                                <div style="font-size: 0.725rem; color: #64748B;">
                                                    <div>Masuk: {{ $tglMasuk }}</div>
                                                    <div style="color: #DC2626; font-weight: 600; margin-top: 0.1rem;">
                                                        Ditolak {{ $item->verifikasi_at->translatedFormat('d M Y') }}
                                                    </div>
                                                </div>
                                            @else
                                                <div style="font-size: 0.725rem; color: #64748B;">
                                                    Masuk: {{ $tglMasuk }}
                                                </div>
                                            @endif
                                        </div>
                                    </td>
```

### Markup Sesudah (Sederhana & Ringkas):
```blade
                                    {{-- Status Permohonan --}}
                                    <td>
                                        <span class="cv-status-dot-badge {{ $statusDotType }}">
                                            <span class="dot"></span>
                                            <span>{{ $item->status->label() }}</span>
                                        </span>
                                    </td>
```
*(Blok `$tglMasuk` dan kalkulasi `$slaDays` di atasnya yang tidak lagi digunakan akan dibersihkan agar kode efisien dan rapi).*

---

## 5. Rencana Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :---: | :--- |
| `resources/views/admin/pendaftaran/index.blade.php` | Modify | Menghapus container teks tanggal di kolom Status tabel antrean pendaftaran. |
| `tests/Feature/AdminChildPagesVisualTest.php` | Modify | Menambahkan assertion untuk memastikan teks `Masuk:` dan tanggal sekunder tidak lagi dirender pada kolom status antrean pendaftaran. |

---

## 6. Langkah Implementasi Bertahap

- [ ] **Langkah 1**: Edit file `resources/views/admin/pendaftaran/index.blade.php`:
  - Bersihkan inisialisasi `$slaDays` (baris 1049–1053).
  - Hapus container tanggal di bawah `cv-status-dot-badge` (baris 1133–1168).
- [ ] **Langkah 2**: Perbarui automated test di `tests/Feature/AdminChildPagesVisualTest.php`:
  - Tambahkan assertion `$response->assertDontSee('Masuk:');`.
  - Pastikan badge status seperti `Diajukan`, `Diterima`, dll. tetap muncul dengan benar.
- [ ] **Langkah 3**: Jalankan pemeriksaan linter:
  ```bash
  vendor\bin\pint.bat --test
  ```
- [ ] **Langkah 4**: Jalankan pengujian visual & feature tests:
  ```bash
  php artisan test --filter "AdminChildPagesVisualTest|AdminVerifikasi|DashboardModulesRenderingTest"
  ```
- [ ] **Langkah 5**: Kompilasi aset Vite:
  ```bash
  npm run build
  ```
- [ ] **Langkah 6**: Lakukan verifikasi manual dev server lokal:
  ```bash
  curl.exe -I -s http://127.0.0.1:8000/admin/pendaftaran
  ```

---

## 7. Strategi Pengujian (Quality Gates)

1. **Pemeriksaan Lolos Test Existing**:
   - Seluruh 595 test harus tetap lolos 100% tanpa regresi.
2. **Pemeriksaan Negatif (*assertDontSee*)**:
   - Memastikan tidak ada kata kunci `Masuk:` pada tabel antrean.
3. **Pemeriksaan Positif (*assertSee*)**:
   - Memastikan badge `cv-status-dot-badge` dan teks status (`Diajukan`, `Diterima`, `Dibatalkan`, dll.) tetap tampil sempurna dengan dot warna yang sesuai.

---

## 8. Kriteria Selesai (Definition of Done)

- [ ] Kolom "Status" pada tabel antrean pendaftaran hanya memuat badge status.
- [ ] Teks `Masuk: [tanggal]`, `Diterima [tanggal]`, `Ditolak [tanggal]`, dan SLA hari telah bersih terhapus.
- [ ] Tampilan baris tabel lebih kompak, rapi, dan seimbang.
- [ ] Pint code style lulus 100%.
- [ ] PHPUnit Feature Test lulus 100%.
- [ ] Vite build sukses tanpa error.
- [ ] Working tree git terkelola dengan baik.

