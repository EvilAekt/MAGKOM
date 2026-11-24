# Plan: Audit Komprehensif, Penyelarasan Alur, dan Verifikasi Manajemen Jalur Khusus (Admin)

Dokumen ini disusun untuk merealisasikan instruksi pengujian dan verifikasi logika sistem pada fitur **Manajemen Jalur Khusus** (`/admin/slot-khusus` dan `/admin/peserta-khusus/create`).

---

## 1. Analisis Alur Penggunaan (User Journey) Admin Jalur Khusus

### 1.1 Alur 1: Manajemen Alokasi Slot Khusus Internal
1. **Melihat Ringkasan**:
   - Admin membuka `/admin/slot-khusus`.
   - Admin melihat 3 indikator utama: Total Alokasi Slot, Slot Terpakai, dan Sisa Slot Tersedia.
   - Admin melihat daftar entri alokasi slot per bulan magang (`slot_khusus`), lengkap dengan kuota, terpakai, dan status aktif.
2. **Menambah Alokasi Slot**:
   - Admin mengklik `+ Tambah Alokasi Slot`.
   - Modal dialog terbuka (`modalTambahSlot`).
   - Admin menginput bulan mulai dan kapasitas jumlah slot (dengan nilai default dari pengaturan sistem `slot_khusus_default`, default 3).
   - Sistem menormalisasi bulan mulai ke tanggal 1 (`YYYY-MM-01`) dan memvalidasi keunikan bulan.
   - Entri tersimpan, audit log dicatat (`TipeAuditEnum::Aksi`, tabel `slot_khusus`).
3. **Mengubah Status Aktif / Nonaktif (Toggle Aktif)**:
   - Admin mengklik tombol `Nonaktifkan` atau `Aktifkan`.
   - Modal konfirmasi muncul dengan rincian slot yang akan diubah.
   - **Penegakan Aturan Bisnis BR-12**: Jika slot memiliki peserta magang aktif (`Penempatan::status = 'aktif'`), sistem menolak penonaktifan dengan pesan error ramah.
   - Jika valid, status berganti dan audit log dicatat.

### 1.2 Alur 2: Pendaftaran Peserta Jalur Khusus Langsung Aktif
1. **Membuka Formulir**:
   - Admin mengklik `+ Tambah Peserta Khusus` yang mengarahkan ke `/admin/peserta-khusus/create`.
   - Formulir terbagi menjadi 3 seksi: Data Pribadi & Kredensial, Data Akademik & Institusi, dan Penempatan & Pembimbing.
2. **Validasi & Pembuatan Akun**:
   - Email dan NIM/NISN dipastikan unik di seluruh database.
   - Pembimbing lapangan dipastikan aktif dan memiliki peran pembimbing.
   - **Penegakan Aturan Bisnis BR-13**: Kapasitas beban bimbingan pembimbing dicek (`maks_peserta_aktif_per_pembimbing`). Jika penuh, penempatan ditolak.
3. **Alokasi Slot Khusus**:
   - Jika admin memilih slot khusus tertentu, sistem mengunci baris slot (`lockForUpdate`) dan memotong kuota (`terisi` bertambah 1).
   - Jika slot penuh atau nonaktif, sistem membatalkan transaksi secara atomik dan menampilkan pesan error.
4. **Pembentukan Penempatan & Kredensial**:
   - Akun User baru terbentuk dengan role `peserta`, email terverifikasi, status aktif, dan `must_change_password = true`.
   - Profil peserta terbentuk di `peserta_profil`.
   - Penempatan terbentuk dengan `status = 'aktif'`, `jalur = 'khusus'`, `sumber = 'manual'`.
   - Password awal sementara disimpan di `users.password_awal` dan di-flash ke session untuk langsung disalin admin via tombol salin kredensial.
   - Audit log penempatan terekam lengkap.

### 1.3 Alur 3: Integrasi Lintas Fitur
1. **Dashboard Pembimbing**: Peserta langsung tercatat di daftar mentee aktif pembimbing terkait.
2. **Hub Laporan & Ekspor**: Peserta muncul di tab data penempatan dengan badge ungu `Khusus (Manual)` dan tercakup di ekspor data.
3. **Pembatalan Penempatan (BR-07)**: Jika admin membatalkan penempatan peserta khusus sebelum tanggal mulai, `CancelPenempatanAction` memanggil `lepasKuotaKhusus` sehingga `terisi` berkurang 1 dan sisa kuota pulih.

---

## 2. Rincian Temuan & Perbaikan

1. **Aksesor `nama_instansi` pada Model `SlotKhusus`**:
   - Tambahkan aksesor dinamis di `app/Models/SlotKhusus.php` agar pemanggilan `$slot->nama_instansi` selalu menghasilkan string informatif seperti `"Slot Khusus November 2026"` alih-alih null.
2. **Normalisasi `bulan_mulai` & Pengayaan Validasi di `SlotKhususRequest`**:
   - Tambahkan `prepareForValidation` untuk memastikan input tanggal selalu dinormalisasi ke awal bulan (`YYYY-MM-01`).
   - Sertakan `nama_instansi` dan `keterangan` dalam safe input validation untuk dicatat ke audit log tanpa mengubah skema database.
3. **Penyempurnaan Pesan Exception di `QuotaAllocationService::ambilKuotaKhusus`**:
   - Bedakan pesan exception antara slot yang dinonaktifkan (`aktif = false`) dengan slot yang kuotanya penuh (`terisi >= jumlah`).
4. **Penyempurnaan Dropdown Slot di Formulir Pendaftaran**:
   - Pastikan informasi kuota dan status slot di dropdown terbaca jelas.

---

## 3. Rencana Pengujian Otomatis (Test Suite)

Buat test suite komprehensif `tests/Feature/AdminSlotKhususKonektivitasEndToEndTest.php` yang mencakup:
1. `test_admin_dapat_melihat_halaman_manajemen_slot_khusus_dan_metriknya()`
2. `test_admin_dapat_menambah_alokasi_slot_khusus_dengan_normalisasi_tanggal()`
3. `test_validasi_menolak_duplikasi_slot_khusus_pada_bulan_yang_sama()`
4. `test_admin_dapat_toggle_aktif_nonaktif_slot_khusus()`
5. `test_penonaktifan_slot_khusus_ditolak_jika_ada_peserta_magang_aktif_br_12()`
6. `test_admin_dapat_mendaftarkan_peserta_khusus_langsung_aktif_dan_memotong_kuota()`
7. `test_pendaftaran_peserta_khusus_ditolak_jika_slot_khusus_penuh()`
8. `test_pendaftaran_peserta_khusus_ditolak_jika_slot_khusus_nonaktif()`
9. `test_pendaftaran_peserta_khusus_ditolak_jika_beban_pembimbing_penuh_br_13()`
10. `test_pembatalan_penempatan_peserta_khusus_mengembalikan_kuota_slot_br_07()`
11. `test_superadmin_hanya_mode_lihat_saja_dan_dilarang_menambah_slot()`

---

## 4. Quality Gates
1. Jalankan PHPUnit test suite baru dan suite penempatan jalur khusus yang ada.
2. Jalankan `vendor/bin/pint --test` pada file yang diubah.
3. Jalankan `vendor/bin/phpstan analyse --memory-limit=1G` pada file yang diubah.
4. Jalankan `npm run build` untuk memverifikasi asset frontend.

