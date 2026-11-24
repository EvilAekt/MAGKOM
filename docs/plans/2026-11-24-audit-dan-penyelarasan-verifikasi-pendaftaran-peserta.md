# Rencana Kerja: Audit Menyeluruh, Verifikasi Keterhubungan Logika Sistem, dan Penyelarasan Fitur Verifikasi Berkas Pendaftaran Peserta (Admin)

**Dokumen**: `docs/plans/2026-11-24-audit-dan-penyelarasan-verifikasi-pendaftaran-peserta.md`  
**Tanggal**: 24 November 2026  
**Status**: Menunggu Persetujuan Pengguna (Pending Approval)  
**Terkait**: Halaman `/admin/pendaftaran` (Verifikasi Berkas Pendaftaran Peserta), PRD F-03 & F-04, BR-02, BR-05, BR-06, BR-07, BR-24  
**Referensi Aturan**: [`AGENTS.md`](file:///d:/laragon/www/MAGKOM/.agents/rules/AGENTS.md), [`web-planning`](file:///d:/laragon/www/MAGKOM/.agents/skills/web-planning/SKILL.md)

---

## 1. Tujuan

Melakukan audit mendalam dan menyeluruh terhadap fitur **Verifikasi Berkas Pendaftaran Peserta** (`/admin/pendaftaran` dan `/admin/pendaftaran/{id}`):
1. **Verifikasi Keterhubungan Lintas Modul**: Memastikan seluruh alur ketika Admin Verifikator menjalankan tugasnya (menerima atau menolak berkas pendaftaran calon peserta magang mandiri maupun kelompok) terhubung secara utuh, konsisten, dan atomik ke modul lain:
   - **Modul Kuota & Formasi Magang** (`/admin/kuota`): Pemotongan kuota secara atomik dengan pessimistic locking (`lockForUpdate`), penanganan kapasitas penuh (`KuotaPenuhException`), dan pelepasan kuota saat pembatalan penempatan.
   - **Modul Penempatan Peserta** (`/admin/penempatan`): Pendaftar yang diterima otomatis diteruskan ke antrean *Belum Ditempatkan* (`PenempatanAntreanQuery`) dan siap dialokasikan pembimbing lapangan serta bidang dinas.
   - **Modul Portal Peserta** (`/peserta/dashboard` & `/pendaftaran`): Pembaruan status realtime melalui Server-Sent Events (SSE), ketersediaan unduh berkas Surat Keterangan Diterima (baik unggahan berkas fisik dinas maupun generator PDF resmi `SuratDiterimaPdfService`).
   - **Modul Audit Trail** (`audit_logs`): Pencatatan jejak forensik setiap mutasi status pendaftaran dan pengunduhan bundel dokumen ZIP.
   - **Modul Laporan & Ekspor** (`/admin/laporan/pendaftaran`): Rekapitulasi berkas terfilter dengan proteksi formula injection spreadsheet.
2. **Pembersihan Celah & Bug**: Mengatasi potensi N+1 query pada pemuatan jurusan profil, memperbaiki potensi bug rendering objek jurusan model pada tabel antrean, menyempurnakan kemampuan pencarian pendaftar kelompok/tim, dan menyelaraskan validasi status domain.
3. **Pengujian & Quality Gates**: Mengembangkan Feature Test keterhubungan baru dan memverifikasi seluruh pemeriksaan mutu (PHPUnit, PHPStan, Pint, Vite Build).

---

## 2. Asumsi & Investigasi Codebase

### Asumsi Kerja:
1. **Fitur Waiting List Telah Dihapus Total**: Sesuai komitmen dan instruksi pengguna, alur waiting list sudah tidak aktif lagi sejak 9 November 2026. Seluruh pendaftar baru yang diajukan langsung berada di antrean `diajukan` untuk diverifikasi admin tanpa penampungan waiting list aktif.
2. **Keputusan Verifikasi 2 Opsi Simetris (BR-24)**: Panel keputusan admin pada `show.blade.php` memiliki 2 opsi resmi: **Terima** dan **Tolak** (dengan alasan penolakan wajib minimal 10 karakter).
3. **Pemberian Surat Diterima**:
   - Jika admin mengunggah dokumen fisik bertanda tangan dinas (`file_surat_diterima`), berkas tersebut disimpan di disk privat dan disajikan kepada peserta.
   - Jika admin tidak mengunggah berkas fisik, sistem menggunakan fallback ke generator PDF kedinasan otomatis (`SuratDiterimaPdfService`) setelah peserta ditempatkan.
4. **Keamanan Berkas (SEC-04 & SEC-15)**: Dokumen persyaratan peserta tersimpan di disk lokal privat dan hanya dapat diakses melalui signed URL berbatas waktu 5 menit (`getSignedPreviewUrl(5)` dan `getSignedDownloadUrl(5)`). Deteksi duplikasi NIM/NISN lintas akun peserta tetap aktif di panel biodata.

---

## 3. Temuan Investigasi & Evaluasi Opsi Arsitektur

Berdasarkan audit mendalam pada codebase saat ini, ditemukan 4 area penyelarasan teknis:

### Temuan 1: Potensi N+1 Query dan Bug Rendering Nama Jurusan di Baris Antrean Pendaftaran
- **Lokasi**: [`app/Queries/Admin/Pendaftaran/PendaftaranIndexQuery.php`](file:///d:/laragon/www/MAGKOM/app/Queries/Admin/Pendaftaran/PendaftaranIndexQuery.php) baris 28 dan [`resources/views/admin/pendaftaran/index.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/pendaftaran/index.blade.php) baris 434.
- **Masalah**:
  1. `PendaftaranIndexQuery::paginate()` hanya memuat relasi `with(['user.profil', ...])` tanpa mengikutsertakan relasi `jurusan` pada profil (`user.profil.jurusan`).
  2. Di Blade baris 434 tertulis: `{{ $item->kuota?->jurusan?->nama_jurusan ?? ($item->user?->profil?->jurusan ?? '-') }}`. Karena `jurusan` pada `PesertaProfil` adalah relasi Eloquent (`BelongsTo`), jika pendaftaran belum memiliki kuota/jurusan eksplisit, Blade akan merender objek `Jurusan` menjadi string JSON mentah (`{"id":..., "nama_jurusan":"..."}`) serta memicu N+1 query.
- **Solusi**:
  1. Eager load `'user.profil.jurusan'` pada `PendaftaranIndexQuery::paginate()`.
  2. Perbaiki Blade baris 434 menjadi `{{ $item->kuota?->jurusan?->nama_jurusan ?? ($item->user?->profil?->jurusan?->nama_jurusan ?? '-') }}`.

### Temuan 2: Cakupan Pencarian Calon Peserta untuk Jalur Kelompok (Tim)
- **Lokasi**: [`app/Queries/Admin/Pendaftaran/PendaftaranIndexQuery.php`](file:///d:/laragon/www/MAGKOM/app/Queries/Admin/Pendaftaran/PendaftaranIndexQuery.php) baris 51–69.
- **Masalah**: Pencarian saat ini mencakup ID pendaftaran (`cleanId`), nama ketua, email, institusi, dan NIM/NISN ketua. Jika verifikator mencari berdasarkan nama kelompok (contoh: "Tim Alpha Sleman") atau nama anggota kelompok, query pencarian belum menjangkaunya.
- **Solusi**: Tambahkan klausul `orWhereHas('kelompokMagang', ...)` untuk mencocokkan `nama_kelompok` serta nama dan NIM/NISN seluruh anggota kelompok.

### Temuan 3: Asimetri Guard Status pada Penolakan Berkas Revisi (`PlacementService::tolak()`)
- **Lokasi**: [`app/Services/PlacementService.php`](file:///d:/laragon/www/MAGKOM/app/Services/PlacementService.php) baris 187.
- **Masalah**: `terimaBerkas()` mengizinkan status `[Diajukan, WaitingList, PerluPerbaikan]`, sedangkan `tolak()` hanya mengizinkan `[Diajukan, WaitingList]`. Jika ada pendaftar dengan status `perlu_perbaikan` yang setelah ditinjau ulang ternyata berkasnya tetap tidak memenuhi kriteria dan harus ditolak, sistem melempar `StatusSudahBerubahException`.
- **Solusi**: Tambahkan `StatusPendaftaranEnum::PerluPerbaikan` ke dalam guard status `tolak()` sehingga alur penerimaan dan penolakan berkas revisi berjalan simetris 100%.

### Temuan 4: Pembersihan 2 Baris Residu `waiting_list` pada Database Lokal
- **Lokasi**: Database MySQL / SQLite lokal (`id` 348 dan 349).
- **Masalah**: Masih terdapat 2 entri historis berstatus `waiting_list` dari seeder masa lampau, sehingga metrik "Semua Status" menampilkan 15 baris sementara tab aktif (Diajukan 2, Diterima 6, Ditolak 2) berjumlah 10.
- **Solusi**: Selaraskan status kedua entri tersebut ke status valid yang aktif (`diajukan` atau `ditolak`) agar data antrean bersih dan konsisten tanpa residual waiting list.

---

## 4. Rincian Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `app/Queries/Admin/Pendaftaran/PendaftaranIndexQuery.php` | Modify | 1. Eager load `'user.profil.jurusan'` untuk eliminasi query N+1.<br>2. Perluas klausul pencarian `q` untuk mencari nama kelompok dan anggota kelompok. |
| `app/Services/PlacementService.php` | Modify | Tambahkan `StatusPendaftaranEnum::PerluPerbaikan` pada guard status metode `tolak()` agar simetris dengan `terimaBerkas()`. |
| `resources/views/admin/pendaftaran/index.blade.php` | Modify | Perbaiki pembacaan nama jurusan fallback pada baris 434 (`$item->user?->profil?->jurusan?->nama_jurusan ?? '-'`) demi mencegah pencetakan string JSON model. |
| `tests/Feature/AdminVerifikasiPendaftaranKonektivitasTest.php` | Create | Feature Test baru yang menguji seluruh rantai integrasi pendaftaran:<br>1. Alur pendaftaran diterima -> pemotongan kuota atomik -> masuk antrean penempatan belum ditempatkan -> SSE broadcast -> audit trail.<br>2. Alur pendaftaran ditolak -> kuota tidak terpotong -> tidak masuk penempatan -> SSE broadcast alasan penolakan.<br>3. Alur pendaftaran kelompok diterima -> pemotongan kuota atomik sejumlah (1 + N anggota) -> verifikasi data kelompok.<br>4. Pencarian pendaftar berdasarkan nama kelompok dan anggota kelompok.<br>5. Proteksi konkruensi pendaftaran dan kuota penuh (`KuotaPenuhException`). |

---

## 5. Justifikasi Modifikasi File Blade (`AGENTS.md` Pasal 3.3)

Sesuai aturan tata kelola [`AGENTS.md`](file:///d:/laragon/www/MAGKOM/.agents/rules/AGENTS.md), modifikasi file Blade memerlukan izin dan penjelasan spesifik:
- **File**: `resources/views/admin/pendaftaran/index.blade.php` baris 434.
- **Alasan**: Perbaikan typo atribut model pada Blade template dari `$item->user?->profil?->jurusan` menjadi `$item->user?->profil?->jurusan?->nama_jurusan`. Tidak ada penambahan tag `<style>`, tag `<script>`, maupun penambahan inline event handler.

---

## 6. Langkah Implementasi Terstruktur

### Fase 1: Perbaikan Logika Query & Domain Backend
- [ ] **Langkah 1.1**: Perbarui `app/Queries/Admin/Pendaftaran/PendaftaranIndexQuery.php`:
  - Tambahkan `'user.profil.jurusan'` ke array eager loading `with()`.
  - Tambahkan pencarian nama kelompok dan anggota pada filter `q`.
- [ ] **Langkah 1.2**: Perbarui `app/Services/PlacementService.php`:
  - Masukkan `StatusPendaftaranEnum::PerluPerbaikan` ke guard status metode `tolak()`.

### Fase 2: Penyelarasan Tampilan Blade
- [ ] **Langkah 2.1**: Perbarui `resources/views/admin/pendaftaran/index.blade.php` baris 434 agar membaca properti `nama_jurusan`.

### Fase 3: Pembersihan Residu Data Database Lokal
- [ ] **Langkah 3.1**: Jalankan skrip penyelarasan status pendaftaran lokal untuk mengubah 2 baris residu `waiting_list` menjadi status valid (`diajukan`).

### Fase 4: Pengujian Komprehensif & Quality Gates
- [ ] **Langkah 4.1**: Buat test suite baru `tests/Feature/AdminVerifikasiPendaftaranKonektivitasTest.php` yang mencakup 5 skenario integrasi end-to-end.
- [ ] **Langkah 4.2**: Jalankan seluruh feature test pendaftaran (`php artisan test`).
- [ ] **Langkah 4.3**: Jalankan pemeriksaan linter formatting (`vendor/bin/pint --test`).
- [ ] **Langkah 4.4**: Jalankan analisis statis PHPStan (`vendor/bin/phpstan analyse --memory-limit=1G`).
- [ ] **Langkah 4.5**: Jalankan build aset frontend (`npm run build`).

---

## 7. Strategi Testing

1. **Feature Test Otomatis**:
   - `tests/Feature/AdminVerifikasiPendaftaranKonektivitasTest.php`:
     - `test_admin_menerima_pendaftaran_memotong_kuota_dan_masuk_antrean_penempatan()`: Memastikan kuota terisi bertambah 1, pendaftaran berstatus `diterima`, dan query `PenempatanAntreanQuery` menemukan peserta di antrean belum ditempatkan.
     - `test_admin_menerima_pendaftaran_kelompok_memotong_kuota_sesuai_jumlah_anggota()`: Memastikan jika kelompok memiliki 1 ketua + 2 anggota, kuota terisi bertambah 3 secara atomik.
     - `test_admin_menolak_pendaftaran_dengan_alasan_wajib_dan_kuota_tidak_terpotong()`: Memastikan kuota tetap utuh dan peserta tidak masuk ke antrean penempatan.
     - `test_admin_dapat_mencari_pendaftaran_berdasarkan_nama_kelompok_dan_anggota()`: Memverifikasi pencarian nama tim/anggota pada index.
     - `test_penerimaan_gagal_ramah_jika_kuota_penuh()`: Memastikan `KuotaPenuhException` ditangani dengan pesan flash error ramah.
2. **Regression Test Suite**:
   - `tests/Feature/AdminPendaftaranPerbaikanFlowTest.php`
   - `tests/Feature/PendaftaranArchitectureAndSecurityTest.php`
   - `tests/Feature/PendaftaranKelompokTest.php`
   - `tests/Feature/PendaftaranPesertaTest.php`
   - `tests/Feature/AdminEksporLaporanPendaftaranPopoverTest.php`
   - `tests/Feature/PesertaPendaftaranFlowTest.php`

---

## 8. Analisis Risiko & Mitigasi

| Potensi Risiko | Tingkat | Mitigasi |
|---|---|---|
| Kontensi konkruensi database saat banyak admin verifikasi bersamaan | Sedang | Tetap menggunakan `lockForUpdate()` di dalam transaksi database dan penanganan `StatusSudahBerubahException`. |
| Inkonsistensi hitungan kuota kelompok | Rendah | Menggunakan formula pasti `1 + $p->kelompokMagang->anggota()->count()` dan validasi kapasitas kuota sebelum pemotongan. |
| Potensi breaking change pada tampilan | Rendah | Tidak ada perubahan CSS/layout; perbaikan Blade hanya pada penyesuaian pemanggilan nama field relasi. |

---

## 9. Kriteria Selesai (Definition of Done)

- [ ] Seluruh alur kerja verifikasi berkas (terima & tolak) terbukti terhubung ke kuota, penempatan, realtime SSE, dan audit log.
- [ ] Pencarian pendaftar kelompok di halaman index mencakup nama kelompok dan anggota.
- [ ] Query N+1 pada profil jurusan teratasi melalui eager loading.
- [ ] Residu `waiting_list` pada data lokal telah bersih dan selaras.
- [ ] Seluruh test suite pendaftaran (termasuk test baru) lulus 100%.
- [ ] PHPStan 0 error, Laravel Pint lulus, dan Vite build sukses.

