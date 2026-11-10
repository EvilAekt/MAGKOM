---
name: web-planning
description: >-
  Gunakan skill ini untuk merencanakan fitur baru, refactor besar, perbaikan bug kompleks,
  migrasi arsitektur, atau perubahan yang menyentuh lebih dari 2 file sebelum menulis kode.
  Dipicu oleh kata kunci: plan, rencana, rancang, desain fitur, buat rencana, analisis perubahan.
  JANGAN digunakan untuk perubahan kecil satu file, perbaikan typo/teks, atau ketika user meminta
  langsung implementasi rencana yang sudah disetujui (gunakan web-implementation).
---

# Web Planning Workflow

Skill ini bertujuan **hanya untuk membuat rencana kerja terstruktur**. 
> ⚠️ **DILARANG KERAS** mengubah atau menulis kode aplikasi sebelum user secara eksplisit menyetujui dokumen rencana kerja ini.

---

## Alur Eksekusi

### 1. Pahami Permintaan & Kebutuhan
- Ringkas ulang tujuan utama dari permintaan user untuk menyamakan persepsi.
- Ajukan **maksimal 3 pertanyaan klarifikasi** HANYA jika terdapat ambiguitas kritis yang dapat mengubah keputusan arsitektur/desain.
- Jika ambiguitas bersifat minor atau tidak mengubah arsitektur, catat langsung sebagai **asumsi kerja logis**.

### 2. Investigasi Codebase (Read-Only)
- Lakukan inspeksi file terkait tanpa mengubah kode (`view_file`, `grep_search`).
- Periksa `composer.json`, `package.json`, konfigurasi proyek, dan struktur folder untuk memahami konvensi yang sudah ada.
- Gunakan **Context7 MCP** untuk membaca dokumentasi resmi library/framework versi terpasang guna mencegah kesalahan API.
- Jika permintaan melibatkan UI/tampilan dan terdapat aset desain (misal Figma/Pencil atau tangkapan layar), telaah spesifikasi tata letak, warna, dan token desain yang relevan.

### 3. Evaluasi Opsi Arsitektur
- Untuk keputusan arsitektur atau desain penting, bandingkan minimal **2 pendekatan alternatif**:
  - Jelaskan kelebihan dan kekurangan masing-masing opsi.
  - Berikan rekomendasi 1 opsi terbaik beserta alasan teknisnya (kinerja, kemudahan pemeliharaan, keamanan).
- Gunakan alat bantu **Sequential Thinking** untuk mengurai penalaran pada alur yang bercabang atau rumit.

### 4. Tulis Dokumen Rencana Kerja
Buat file rencana baru di: `docs/plans/YYYY-MM-DD-<slug>.md` (contoh: `docs/plans/2026-10-14-pendaftaran-magang.md`).
Pastikan dokumen memuat bagian-bagian wajib berikut:

1. **Tujuan**: Deskripsi ringkas hasil akhir yang ingin dicapai.
2. **Asumsi & Pertanyaan Terbuka**: Asumsi teknis/bisnis yang digunakan dan pertanyaan lanjutan (jika ada).
3. **Pendekatan Teknis**: Opsi arsitektur terpilih beserta alasan pemilihan.
4. **Perubahan File**:
   | File | Aksi (`Create`/`Modify`/`Delete`) | Ringkasan Perubahan |
   | :--- | :--- | :--- |
   | `app/Models/Internship.php` | Modify | Tambah relasi dan casting status |
5. **Langkah Implementasi**:
   - Tulis dalam format checklist bertahap `- [ ]`.
   - Urutkan secara ketat berdasarkan dependensi (misal: Migration/Model -> Service/Action -> Controller -> Route -> Blade View).
   - Setiap butir harus atomic, spesifik, dan dapat diverifikasi langsung.
6. **Strategi Testing**:
   - Skenario Unit Test dan Feature Test yang harus dibuat atau diperbarui (`php artisan test`).
   - Skenario *happy path* dan *sad path* (validasi gagal, unauthorized 403, error handling).
7. **Risiko & Mitigasi**:
   - Analisis potensi *breaking changes*, migrasi database berisiko, dampak performa (N+1 query), keamanan (OWASP), dan aksesibilitas (a11y).
8. **Kriteria Selesai (Definition of Done)**:
   - Checklist checklist akhir `- [ ]` untuk menyatakan bahwa tugas telah tuntas secara menyeluruh.

### 5. Konfirmasi & Hentikan Eksekusi
- Tampilkan ringkasan rencana dan cantumkan path lengkap file rencana (`docs/plans/...`).
- Ajukan pertanyaan konfirmasi kepada user:
  > *"Apakah Anda menyetujui rencana kerja ini, atau ada bagian yang ingin diubah?"*
- **BERHENTI** dan tunggu persetujuan resmi dari user sebelum mengambil langkah teknis apa pun.

---

## Aturan Ketat Perencanaan
- **Dilarang Menulis Langkah Samar**: Hindari instruksi umum tanpa konteks teknis seperti "perbaiki UI" atau "optimalkan query". Tuliskan fungsi/file spesifik.
- **Zero Scope Creep**: Ide atau fitur tambahan di luar permintaan awal wajib dimasukkan ke bagian khusus *"Saran Lanjutan"*, bukan ke langkah kerja utama.
- **Zero Secrets**: Dilarang mencatat kata sandi, token, atau secret langsung di dokumen plan.
- **Tindakan Destruktif**: Beri tanda peringatan jelas **⚠️ TINDAKAN DESTRUKTIF** pada langkah seperti migrasi database, penghapusan data, atau perubahan struktural berisiko tinggi.
