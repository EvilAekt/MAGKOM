# Plan Perbaikan Teks & Tampilan Pop-up Konfirmasi Aktivasi / Penonaktifan Bulan Magang

**Tanggal:** 2026-11-25  
**Target Branch:** `test-2`  
**Status:** Menunggu Persetujuan Pengguna

---

## 1. Tujuan
Memperbaiki pop-up konfirmasi status bulan magang pada halaman Manajemen Kuota Admin (`/admin/kuota`) agar:
1. Saat **mengaktifkan bulan** (`isAktif === false`), tombol konfirmasi di kanan bawah bertuliskan **"Ya, Aktifkan Bulan"** (bukan `"Ya, Nonaktifkan Bulan"`).
2. Saat **menonaktifkan bulan** (`isAktif === true`), tombol konfirmasi di kanan bawah bertuliskan **"Ya, Nonaktifkan Bulan"**.
3. Warna border tombol konfirmasi dan warna teks di dalam kotak pesan pop-up selaras dengan status aksinya (hijau saat mengaktifkan, merah saat menonaktifkan) sehingga tidak muncul border/teks merah pada dialog pengaktifan hijau.

---

## 2. Analisis Akar Masalah (Root Cause)
Berdasarkan inspeksi pada `resources/js/pages/admin/kuota-index.js` (baris 248–282) dan `resources/views/admin/kuota/index.blade.php` (baris 1289–1318):
1. Pada `resources/views/admin/kuota/index.blade.php`, tombol submit di modal `#modalKonfirmasiToggleBulan` memiliki elemen `<span id="btnSubmitToggleBulanText">Ya, Nonaktifkan Bulan</span>` sebagai teks bawaan serta inline `border-color: #DC2626`.
2. Fungsi `openToggleBulanModal(nama, isAktif, actionUrl)` di `resources/js/pages/admin/kuota-index.js` hanya mengubah `submitBtn.style.background` (`#DC2626` atau `#16A34A`), **tetapi tidak mengambil maupun memperbarui `#btnSubmitToggleBulanText`** dan **`submitBtn.style.borderColor`**. Akibatnya, saat mengaktifkan bulan, tombol berwarna hijau tetapi tetap bertuliskan `"Ya, Nonaktifkan Bulan"` dan masih memiliki garis tepi merah.
3. Selain itu, `#toggleBulanMessage` (`color: #9F1239`) dan `#toggleBulanConsequence` (`color: #BE123C`) di kotak pesan modal tidak diubah ke palet hijau (`#166534` / `#15803D`) saat mode pengaktifan, sehingga teks di dalam kotak hijau tampak berwarna merah.

---

## 3. Asumsi & Pertanyaan Terbuka
- **Asumsi Kerja Logis**:
  - Tidak ada perubahan pada file Blade `resources/views/admin/kuota/index.blade.php` karena seluruh elemen target (`#btnSubmitToggleBulanText`, `#btnSubmitToggleBulan`, `#toggleBulanMessage`, `#toggleBulanConsequence`) sudah memiliki `id` masing-masing dan dapat dikendalikan sepenuhnya dari `resources/js/pages/admin/kuota-index.js`.
  - Modul referensi lain (`resources/js/pages/admin/bulan-index.js` dan `resources/js/pages/superadmin/bulan-index.js`) sudah memperbarui `submitText.textContent`, sehingga tidak perlu diubah.
- **Pertanyaan Terbuka**: Tidak ada ambiguitas kritis.

---

## 4. Evaluasi Opsi Arsitektur

| Kriteria | Opsi A: Perbarui DOM text, borderColor, dan alert text color langsung di `openToggleBulanModal()` pada `kuota-index.js` **(Rekomendasi)** | Opsi B: Refaktor modal ke class modifier CSS baru dan ubah markup Blade `admin/kuota/index.blade.php` |
| :--- | :--- | :--- |
| **Kepatuhan `AGENTS.md`** | Blade tetap *read-only* (tidak menyentuh Blade sama sekali) | Memerlukan modifikasi file Blade dan persetujuan markup tambahan |
| **Cakupan Perubahan** | Sangat minimal, terfokus 1 file JS (`kuota-index.js`) | Menyentuh Blade + CSS + JS |
| **Risiko Regresi** | Sangat rendah | Rendah–sedang |

**Keputusan**: Memilih **Opsi A** (perubahan terfokus pada `resources/js/pages/admin/kuota-index.js`).

---

## 5. Daftar Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/js/pages/admin/kuota-index.js` | Modify | Di dalam `openToggleBulanModal()`: ambil `#btnSubmitToggleBulanText`, set `submitText.textContent = 'Ya, Nonaktifkan Bulan'` saat `isAktif` dan `'Ya, Aktifkan Bulan'` saat `!isAktif`; selaraskan `submitBtn.style.borderColor` (`#DC2626` vs `#16A34A`), serta warna teks `msgEl` (`#9F1239` vs `#166534`) dan `consequenceEl` (`#BE123C` vs `#15803D`). |

---

## 6. Langkah Implementasi
- [x] **Langkah 1 (JS):** Perbarui `openToggleBulanModal()` di `resources/js/pages/admin/kuota-index.js`:
  - Ambil `const submitText = document.getElementById('btnSubmitToggleBulanText');`
  - Pada cabang `if (isAktif)` (Penonaktifan):
    - `submitText.textContent = 'Ya, Nonaktifkan Bulan';`
    - `submitBtn.style.background = '#DC2626';` dan `submitBtn.style.borderColor = '#DC2626';`
    - `msgEl.style.color = '#9F1239';` dan `consequenceEl.style.color = '#BE123C';`
  - Pada cabang `else` (Pengaktifan):
    - `submitText.textContent = 'Ya, Aktifkan Bulan';`
    - `submitBtn.style.background = '#16A34A';` dan `submitBtn.style.borderColor = '#16A34A';`
    - `msgEl.style.color = '#166534';` dan `consequenceEl.style.color = '#15803D';`
- [x] **Langkah 2 (Build Aset):** Jalankan `npm run build` menggunakan Node.js v22 (`D:\laragon\bin\nodejs\node-v22`) untuk mengompilasi `resources/js/pages/admin/kuota-index.js`.
- [x] **Langkah 3 (Testing & Verifikasi):** Jalankan feature test manajemen kuota/bulan (`php artisan test tests/Feature/AdminKuota*`) dan pastikan tidak ada error JS.

---

## 7. Strategi Testing
- **Happy Path**:
  - Klik tombol toggle pada bulan berstatus **Nonaktif** → pop-up menampilkan judul `"Konfirmasi Pengaktifan Bulan"`, teks hijau, dan tombol kanan bertuliskan `"Ya, Aktifkan Bulan"` dengan warna & border hijau (`#16A34A`).
  - Klik tombol toggle pada bulan berstatus **Aktif** → pop-up menampilkan judul `"Konfirmasi Penonaktifan Bulan"`, teks merah, dan tombol kanan bertuliskan `"Ya, Nonaktifkan Bulan"` dengan warna & border merah (`#DC2626`).
- **Regression**:
  - Submit form aktivasi/penonaktifan bulan tetap mengarah ke endpoint `PATCH` yang benar dan memperbarui status bulan di database.

---

## 8. Risiko & Mitigasi
- **Risiko**: Aset JS lama masih ter-cache di browser setelah build.
  - **Mitigasi**: `npm run build` menghasilkan hash file Vite baru di `public/build/manifest.json` sehingga browser otomatis memuat versi terbaru.

---

## 9. Kriteria Selesai (Definition of Done)
- [ ] Tombol konfirmasi saat mengaktifkan bulan bertuliskan **"Ya, Aktifkan Bulan"** (hijau, border hijau).
- [ ] Tombol konfirmasi saat menonaktifkan bulan bertuliskan **"Ya, Nonaktifkan Bulan"** (merah, border merah).
- [ ] `npm run build` selesai tanpa error dan test PHPUnit terkait tetap lulus.

