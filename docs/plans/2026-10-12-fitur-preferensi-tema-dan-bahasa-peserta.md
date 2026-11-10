# Rencana Kerja: Implementasi Fungsionalitas Fitur Preferensi (Tema & Bahasa) Peserta

**Tanggal:** 12 Oktober 2026  
**Status:** Draf Perencanaan (Menunggu Persetujuan Pengguna)  
**Dokumen Referensi:** Screenshot Card "Preferensi" Pengguna, Design System DS-MagKom  

---

## 1. Analisis Kebutuhan & Masalah Saat Ini
Pada kartu **Preferensi** di menu `Akun saya` (`/peserta/pengaturan`):
- Opsi tema (*Terang*, *Gelap*, *Ikuti sistem*) dan pilihan bahasa saat ini baru berupa tampilan statis visual dan belum mengubah tema portal secara menyeluruh.
- **Permintaan Pengguna**: Mengaktifkan seluruh fungsionalitas pada menu Preferensi tersebut agar dapat dipakai secara nyata:
  1. **Mode Gelap (Dark Mode)**: Mengubah seluruh antarmuka portal peserta (body, sidebar, topbar, cards, forms, tabel, teks, modal) menjadi mode gelap modern bernuansa slate/navy malam yang elegan.
  2. **Mode Terang (Light Mode)**: Mengembalikan tema portal ke mode default terang.
  3. **Ikuti Sistem (System Adaptive)**: Menyesuaikan tema secara otomatis berdasarkan preferensi tema sistem operasi / browser pengguna (`prefers-color-scheme: dark`).
  4. **Penyimpanan Permanen (Zero-Flicker Persistence)**: Tema disimpan di `localStorage` dan Session/Cookie sehingga ketika pengguna membuka halaman lain atau me-refresh halaman, tema tidak berkedip (*no white flash/FOUC*).
  5. **Live Preview Instan**: Saat salah satu kartu tema diklik, tema langsung berganti secara real-time dan kartu yang dipilih mendapatkan outline biru tegas seperti pada tangkapan layar pengguna.
  6. **Pilihan Bahasa Interaktif**: Dropdown pilihan bahasa (Bahasa Indonesia & English) yang interaktif dan dapat dipilih.

---

## 2. Arsitektur Teknis

### A. Sistem Tema CSS Variabel Terpusat (`resources/views/layouts/peserta.blade.php`)
1. **Definisi Token Dark Mode**:
   Menambahkan CSS selector `[data-theme="dark"]` yang mendefinisikan ulang token variabel warna dasar:
   - `--neutral-0`: `#1E293B` (Latar belakang cards & dropdown)
   - `--neutral-100`: `#0F172A` (Secondary container & hover state)
   - `--neutral-150`: `#0B1120` (Latar belakang utama halaman portal)
   - `--neutral-200`: `#334155` (Border & divider kartu)
   - `--neutral-400`: `#475569` (Border input & border non-aktif)
   - `--neutral-500`: `#94A3B8` (Teks sekunder / muted label)
   - `--neutral-600`: `#CBD5E1` (Teks bantuan)
   - `--neutral-700`: `#E2E8F0` (Teks reguler)
   - `--neutral-800`: `#F8FAFC` (Judul & heading teks terang)
   - `--neutral-900`: `#FFFFFF`
   - Penyesuaian warna sidebar, topbar, dan form input agar menyatu dalam mode gelap.

2. **Skrip Anti-Flash (FOUC Prevention)**:
   Menyisipkan inline script mini di `<head>` sebelum rendering elemen DOM:
   - Membaca preferensi dari `localStorage` atau cookie session.
   - Jika `sistem`, mengevaluasi `window.matchMedia('(prefers-color-scheme: dark)').matches`.
   - Mengatur atribut `data-theme="dark"` atau `data-theme="light"` ke elemen `<html>` seketika.

3. **Listener Perubahan Tema OS**:
   Mendengarkan event `change` pada `matchMedia('(prefers-color-scheme: dark)')` ketika preferensi pengguna berada dalam mode "Ikuti sistem".

### B. Halaman Pengaturan & Komponen Preferensi (`resources/views/peserta/pengaturan/index.blade.php`)
1. **Visual Kartu Opsi Tema**:
   - Menyelaraskan kartu pilihan tema (*Terang*, *Gelap*, *Ikuti sistem*) agar identik dengan tangkapan layar pengguna:
     - Border rounded yang rapi.
     - State terpilih: Border biru solid `2px solid #0641CC` dengan sudut membulat.
     - Preview visual baris yang presisi.
2. **Interaktivitas JavaScript**:
   - Event klik pada opsi kartu langsung memanggil fungsi `applyThemePreview(themeValue)`.
   - Menyimpan nilai terpilih ke `localStorage` dan mengirimkan form secara mulus atau via submit tombol `Simpan preferensi`.
3. **Komponen Pilihan Bahasa**:
   - Mengubah elemen bahasa menjadi select/dropdown interaktif yang fungsional dengan pilihan `Bahasa Indonesia` (aktif) dan `English` dengan penanda status.

### C. Backend Controller (`app/Http/Controllers/Peserta/PengaturanController.php`)
- Memperbarui method `updatePreferensi()` untuk memvalidasi pilihan tema (`terang`, `gelap`, `sistem`) dan bahasa (`id`, `en`).
- Menyimpan preferensi ke Session dan Cookie browser berdurasi panjang (*forever cookie*).
- Menghasilkan respon sukses dengan flash message.

---

## 3. Checklist Rencana Kerja Bertahap

- [ ] **Langkah 1**: Buat arsitektur CSS Dark Mode (`[data-theme="dark"]`) dan script anti-flash di `resources/views/layouts/peserta.blade.php`.
- [ ] **Langkah 2**: Perbaiki styling komponen sidebar, topbar, konten, kartu, dan input agar adaptif penuh terhadap mode gelap.
- [ ] **Langkah 3**: Perbarui view `resources/views/peserta/pengaturan/index.blade.php`:
  - Tambahkan interaktivitas live preview tema (Terang, Gelap, Ikuti sistem).
  - Terapkan visual outline kartu terpilih sesuai tangkapan layar pengguna.
  - Tambahkan pilihan bahasa interaktif (Indonesia & English).
- [ ] **Langkah 4**: Perbarui controller `app/Http/Controllers/Peserta/PengaturanController.php` untuk mendukung penyimpanan tema & bahasa secara persisten.
- [ ] **Langkah 5**: Lakukan pengujian otomatis (CLI render test & functional test).
- [ ] **Langkah 6**: Validasi menyeluruh bahwa peralihan tema berjalan instan tanpa kedip dan seluruh dashboard peserta nyaman dibaca dalam mode gelap maupun terang.

---

## 4. Kriteria Keberhasilan (Definition of Done)
1. Memilih opsi **Gelap** langsung mengubah seluruh tampilan dashboard menjadi Dark Mode modern, terbaca jelas, dan nyaman di mata.
2. Memilih opsi **Terang** mengembalikan tampilan ke tema default terang.
3. Memilih opsi **Ikuti sistem** mendeteksi tema OS secara otomatis.
4. Tema tetap bertahan saat halaman di-refresh atau saat pengguna berpindah menu ke Dashboard, Profil, Pendaftaran, Tugas, dll.
5. Tampilan kartu preferensi identik dengan tangkapan layar pengguna.
