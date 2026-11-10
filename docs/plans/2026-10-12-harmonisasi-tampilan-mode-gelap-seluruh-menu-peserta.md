# Rencana Kerja: Harmonisasi Tampilan Mode Gelap (Dark Mode) Seluruh Menu Portal Peserta

**Tanggal:** 12 Oktober 2026  
**Status:** Draf Perencanaan (Menunggu Persetujuan Pengguna)  
**Dokumen Referensi:** Screenshot Menu Tugas & Proyek Magang (Mode Gelap), Design System DS-MagKom  

---

## 1. Latar Belakang & Tujuan
Pengguna sangat menyukai tampilan visual pada menu **Tugas & Proyek Magang** saat dalam Mode Gelap (*Dark Mode*), di mana perpaduan warna latar belakang malam (*deep navy/slate* `#0A0F1D`), kartu penugasan dengan border tegas (`#26354D` / `#2563EB`), ikon bulat berwarna dengan aksen berpendar, status badges kontras tinggi, dan teks yang mudah dibaca memberikan kesan yang sangat premium dan profesional.

**Tujuan**:
Menyelaraskan dan menerapkan estetika visual mode gelap yang identik dengan menu Tugas ke seluruh menu lainnya di portal peserta:
1. **Menu Dashboard Peserta** (`resources/views/peserta/dashboard.blade.php`)
2. **Menu Pengaturan / Akun Saya** (`resources/views/peserta/pengaturan/index.blade.php`)
3. **Menu Profil & Biodata** (`resources/views/peserta/profil/show.blade.php`)
4. **Menu Pendaftaran Magang** (`resources/views/peserta/pendaftaran/index.blade.php` & `detail.blade.php`)
5. **Menu Logbook Mingguan** (`resources/views/peserta/logbook/index.blade.php`)
6. **Menu Lembar Nilai** (`resources/views/peserta/nilai/show.blade.php`)
7. **Komponen Global di Layout Peserta** (`resources/views/layouts/peserta.blade.php`)

---

## 2. Palet Desain & Standar Estetika Mode Gelap Ala Menu Tugas

Mengacu langsung pada visual menu Tugas:
1. **Background Halaman Utama**:
   - `background: #0A0F1D` (Midnight Slate).
2. **Kartu Kontainer (Cards)**:
   - Background: `#182234` (Deep Slate Navy).
   - Border Standar: `1px solid #26354D` (Border halus namun tegas).
   - Border Highlight/Aktif: `1.5px solid #2563EB` dengan soft glow `box-shadow: 0 4px 20px rgba(37, 99, 235, 0.15)`.
   - Border Radius: `14px` - `16px`.
3. **Ikon Bulat Miniatur**:
   - Varian Biru (Umum/Tugas): background `rgba(6, 65, 204, 0.35)`, border `1px solid rgba(7, 81, 255, 0.5)`, icon `#60A5FA`.
   - Varian Kuning/Amber (Proses/Menunggu): background `rgba(217, 119, 6, 0.25)`, border `1px solid rgba(245, 158, 11, 0.4)`, icon `#FBBF24`.
   - Varian Hijau (Selesai/Aktif): background `rgba(16, 185, 129, 0.25)`, border `1px solid rgba(34, 197, 94, 0.4)`, icon `#34D399`.
4. **Status Badges & Chips**:
   - Ditugaskan / Info: background `rgba(30, 58, 138, 0.6)`, border `1px solid rgba(96, 165, 250, 0.4)`, teks `#93C5FD`.
   - Sedang Dikerjakan / Proses: background `rgba(120, 53, 15, 0.6)`, border `1px solid rgba(251, 191, 36, 0.4)`, teks `#FCD34D`.
   - Selesai / Disetujui: background `rgba(6, 78, 59, 0.6)`, border `1px solid rgba(52, 211, 153, 0.4)`, teks `#6EE7B7`.
5. **Tipografi & Teks**:
   - Judul & Heading: `#F8FAFC` (Putih lembut cerah, tidak menyilaukan).
   - Teks Deskripsi & Paragraf: `#94A3B8` (Slate muted terstruktur).
   - Tautan Aksi: `#38BDF8` / `#60A5FA` dengan efek hover berpendar.
6. **Form Inputs, Select & Textarea**:
   - Background: `#0F172A`.
   - Border: `#3E516E`.
   - Teks: `#F8FAFC`.
   - Focus: `border-color: #2563EB; box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.25)`.
7. **Tabel & List**:
   - Header tabel: background `#111827`, teks `#94A3B8`.
   - Border baris: `#26354D`.
   - Row hover: `#1E293B`.

---

## 3. Rincian Penyelarasan Menu per Halaman

### A. Komponen Global di `layouts/peserta.blade.php`
- Memperkaya definisi global `[data-theme="dark"]` agar secara otomatis mencakup:
  - Form control, modal popup, badge status, tabel riwayat, dan alert box.
  - Dropdown topbar, notifikasi, dan tombol logout.

### B. Dashboard Peserta (`peserta/dashboard.blade.php`)
- Kartu Metrik KPI (Status Magang, Kuota, Logbook, Tugas): diselaraskan dengan kartu ala menu tugas (border navy tegas, ikon bulat berwarna, teks kontras tinggi).
- Kartu Quick Actions & Hero Banner info.
- Timeline Aktivitas & Kotak Pengumuman.

### C. Pengaturan / Akun Saya (`peserta/pengaturan/index.blade.php`)
- Kartu Informasi Akun (Avatar, chip role peserta magang, tabel data key-value).
- Kartu Ganti Kata Sandi (Input form berlatar gelap, tombol intip kata sandi, tombol primary biru).
- Kartu Preferensi (Kotak bahasa, kartu opsi Terang/Gelap/Sistem dengan border solid biru).

### D. Profil & Biodata Peserta (`peserta/profil/show.blade.php`)
- Hero Banner Profil & Progress Tracker kelengkapan data diri.
- Kartu Informasi Pribadi, Akademik, Kontak Darurat.
- Input fields, custom select jurusan/jenjang, dan tombol aksi simpan profil.

### E. Pendaftaran Magang (`peserta/pendaftaran/index.blade.php` & `detail.blade.php`)
- Kartu & Tabel Riwayat Pendaftaran.
- Status badge (Menunggu Verifikasi, Terverifikasi, Diterima, Ditolak).
- Modal unggah berkas dan pratinjau dokumen.

### F. Logbook Mingguan (`peserta/logbook/index.blade.php`)
- Timeline navigasi minggu logbook.
- Kartu logbook harian, status review pembimbing, dan modal input aktivitas harian.

### G. Lembar Nilai (`peserta/nilai/show.blade.php`)
- Kartu ringkasan capaian nilai, rubrik evaluasi, dan tombol unduh sertifikat PDF.

---

## 4. Checklist Rencana Kerja Bertahap

- [ ] **Langkah 1**: Perbarui dan standarisasi token CSS Dark Mode di `resources/views/layouts/peserta.blade.php` agar mencakup seluruh token visual kartu ala menu tugas.
- [ ] **Langkah 2**: Selaraskan Dark Mode pada **Menu Dashboard Peserta** (`resources/views/peserta/dashboard.blade.php`).
- [ ] **Langkah 3**: Selaraskan Dark Mode pada **Menu Pengaturan / Akun Saya** (`resources/views/peserta/pengaturan/index.blade.php`).
- [ ] **Langkah 4**: Selaraskan Dark Mode pada **Menu Profil & Biodata** (`resources/views/peserta/profil/show.blade.php`).
- [ ] **Langkah 5**: Selaraskan Dark Mode pada **Menu Pendaftaran Magang** (`resources/views/peserta/pendaftaran/index.blade.php` & `detail.blade.php`).
- [ ] **Langkah 6**: Selaraskan Dark Mode pada **Menu Logbook Mingguan** (`resources/views/peserta/logbook/index.blade.php`) dan **Lembar Nilai** (`resources/views/peserta/nilai/show.blade.php`).
- [ ] **Langkah 7**: Lakukan pengujian otomatis dan verifikasi render di browser untuk memastikan seluruh halaman konsisten dan seindah menu Tugas.

---

## 5. Kriteria Keberhasilan (Definition of Done)
1. Seluruh menu di portal peserta memiliki konsistensi visual warna latar belakang, kartu, border, badge status, ikon bulat, dan tipografi yang identik dengan estetika menu Tugas saat dalam Mode Gelap.
2. Mode Terang tetap bekerja normal 100% tanpa regresi visual.
3. Seluruh teks, label, dan tombol di mode gelap memiliki kontras tinggi yang nyaman dibaca (WCAG 2.1 AA).
4. Tidak ada error console atau masalah fungsionalitas saat beralih antar mode tema.
