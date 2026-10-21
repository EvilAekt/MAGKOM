---
name: web-implementation
description: >-
  Gunakan skill ini untuk mengeksekusi rencana kerja yang SUDAH disetujui dari docs/plans/.
  Dipicu oleh kata kunci: implementasikan, jalankan plan, lanjut, kerjakan, eksekusi plan,
  atau saat user menyetujui hasil rencana dari skill web-planning.
  JANGAN digunakan jika belum ada dokumen rencana yang disetujui untuk fitur atau refactor besar
  (arahkan user ke web-planning terlebih dahulu).
---

# Web Implementation Workflow

Skill ini bertujuan **mengeksekusi rencana kerja yang telah disetujui user** secara bertahap, disiplin, dan terverifikasi otomatis.

---

## Alur Eksekusi

### 1. Verifikasi Prasyarat Plan
- Cari dokumen rencana kerja di `docs/plans/` (pilih file terbaru atau dokumen spesifik yang disebutkan oleh user).
- **Quality Gate**: Jika belum ada dokumen rencana dan pekerjaan menyentuh lebih dari 2 file atau arsitektur penting, **HENTIKAN PROSES** dan arahkan user untuk menjalankan skill `web-planning` terlebih dahulu.
- Pastikan user telah memberikan konfirmasi persetujuan sebelum penulisan kode dimulai.

### 2. Persiapan Lingkungan Kerja (Pre-flight)
- Periksa status Git (`git status`). Jika terdapat uncommitted changes yang tidak relevan dengan tugas, informasikan kepada user.
- Periksa branch aktif. Jika berada di branch `main` atau `master`, rekomendasikan pembuatan branch baru sesuai konvensi (`feat/<slug>` atau `fix/<slug>`).
- Jalankan verifikasi awal (*baseline*):
  - Linter: `./vendor/bin/pint --test` atau `npm run lint`
  - Automated Tests: `php artisan test`
  - Catat jika ada test atau linter yang memang sudah gagal sebelum pekerjaan dimulai agar tidak tercampur.

### 3. Eksekusi Bertahap (Step-by-Step Execution)
Eksekusi setiap butir checklist `- [ ]` pada dokumen plan secara berurutan:
1. **Baca Sebelum Menulis**: Periksa file target dan file tetangganya (`view_file`, `grep_search`). Pahami konteks dan ikuti pola yang ada di `code-style.md` dan `backend-security.md`.
2. **Perubahan Terfokus**: Ubah kode sekecil dan sefokus mungkin sesuai instruksi butir plan.
3. **Verifikasi Langsung**: Segera jalankan linter atau test unit/feature terkait begitu sebuah langkah selesai diimplementasikan.
4. **Centang Progres**: Perbarui dokumen plan di `docs/plans/` dengan menandai butir yang selesai menjadi `- [x]` **hanya setelah terverifikasi sukses**.
5. **Circuit Breaker**: Jika terjadi error dan perbaikan gagal sebanyak **2 kali berturut-turut** dengan pendekatan yang sama, **STOP**. Jelaskan akar masalah (Root Cause Analysis) kepada user dan ajukan revisi teknis pada dokumen plan sebelum mencoba lagi.

### 4. Penanganan Deviasi & Penyimpangan
- Jika saat pengerjaan ditemukan kendala teknis yang membuat rencana tidak lagi sesuai, catat alasan dan perubahannya di bagian *"Catatan Perubahan (Deviations)"* pada dokumen plan, lalu infokan ke user.
- Ide atau optimasi tambahan di luar cakupan plan yang disetujui **tidak boleh dikerjakan langsung**. Catat sebagai saran lanjutan pada laporan akhir.

### 5. Verifikasi Akhir & Quality Gates
Setelah seluruh checklist plan selesai:
- **Linting & Code Style**: Jalankan `./vendor/bin/pint` dan pastikan format kode bersih.
- **Automated Tests**: Jalankan seluruh test suite (`php artisan test`) dan pastikan 100% *passed* tanpa regresi.
- **Asset Build**: Jalankan kompilasi aset produksi (`npm run build`).
- **Audit Antarmuka (Jika Mengubah UI)**:
  - Gunakan browser automation (**Playwright MCP** atau **Chrome DevTools MCP**).
  - Periksa console logs browser (pastikan nol JavaScript error).
  - Verifikasi tampilan pada resolusi mobile (375px) dan desktop (1440px).
  - Ambil screenshot verifikasi.
- **Checklist Kriteria Selesai**: Pastikan seluruh butir di bagian *Kriteria Selesai (Definition of Done)* pada dokumen plan sudah terpenuhi.

### 6. Laporan Akhir & Penutupan
Berikan laporan penutupan yang memuat:
1. Daftar file yang dibuat/dimodifikasi beserta ringkasan fungsinya.
2. Hasil verifikasi akhir (status linting, passing test count, build status).
3. Butir plan yang belum selesai atau mengalami penyesuaian (beserta alasannya).
4. Langkah manual yang masih perlu dilakukan user (misal: pengisian variabel `.env`, eksekusi migrasi database, atau restart dev server).
5. Usulan pesan commit mengikuti format **Conventional Commits** (`feat:`, `fix:`, `refactor:`, dll).
> ⚠️ **JANGAN** mengeksekusi `git commit` atau `git push` secara mandiri kecuali diminta secara eksplisit oleh user.

---

## Aturan Ketat Implementasi
- **Dilarang Mengarang API**: Gunakan API resmi framework/library. Jika ragu terhadap sintaks versi tertentu, periksa via **Context7 MCP**.
- **Zero Secrets in Code**: Dilarang menulis credentials/token/password langsung ke dalam file kode. Selalu gunakan `.env` dan sediakan contohnya di `.env.example`.
- **Tidak Menambah Dependency Sembarangan**: Dilarang menginstal package baru via Composer/NPM kecuali sudah tercantum dalam dokumen plan yang disetujui.
- **Dilarang Melemahkan Test**: Dilarang mengubah ekspektasi assertion atau menghapus test yang ada hanya agar build berhasil lolos.
- **Izin Aksi Destruktif**: Wajib meminta izin konfirmasi eksplisit sebelum menjalankan perintah destruktif (migrasi database, truncate data, penghapusan file penting).
- **Batasan Ruang Lingkup**: Dilarang mengedit atau menyentuh file di luar direktori proyek kerja.
