# Rencana Kerja: Pembatasan Akses Fitur Magang (Logbook, Tugas, Lembar Nilai) untuk Peserta Belum Diterima

## 1. Latar Belakang & Masalah
Saat ini, peserta yang baru mendaftar atau masih dalam tahap seleksi (misalnya akun POV pendaftar `pendaftar@magkom.go.id` dengan status pendaftaran `diajukan` dan belum memiliki penempatan magang aktif):
- Masih dapat mengklik dan membuka rute `/peserta/logbook`, `/peserta/tugas`, dan `/peserta/nilai`.
- Meskipun data di dalamnya kosong, calon peserta yang belum resmi diterima seharusnya tidak dapat mengakses modul pelaksanaan magang (Logbook Mingguan, Tugas & Progres, serta Lembar Nilai).
- Di menu navigasi sidebar, menu tersebut masih tampil aktif seolah-olah dapat digunakan, sehingga membingungkan calon peserta.

## 2. Sasaran & Ruang Lingkup
1. **Perlindungan Rute (Route Guard & Middleware)**:
   - Membuat middleware `EnsurePesertaDiterima` (`peserta.diterima`) untuk mengamankan seluruh rute pelaksanaan magang:
     - `peserta.tugas.*` (Daftar tugas, detail tugas, unggah progres, unduh lampiran)
     - `peserta.logbook.*` (Daftar logbook, tambah logbook, ubah logbook, unduh lampiran)
     - `peserta.nilai.*` (Lihat lembar nilai, unduh PDF nilai)
   - Jika peserta belum memiliki penempatan magang yang sah (aktif/selesai):
     - Permintaan web dialihkan (*redirect*) ke `/peserta/dashboard` disertai flash notification bertipe peringatan (*warning*): *"Fitur ini terkunci. Logbook, Tugas, dan Lembar Nilai hanya dapat diakses setelah permohonan magang Anda disetujui dan ditempatkan ke unit kerja."*
     - Permintaan API/JSON mengembalikan respon HTTP 403 Forbidden.

2. **Penyempurnaan Navigasi Sidebar (UI & UX)**:
   - Pada `resources/views/layouts/peserta.blade.php`:
     - Memeriksa status penerimaan peserta (`$user->hasPenempatanMagang()` atau pengecekan penempatan).
     - Jika peserta **belum diterima**:
       - Menu **Logbook Mingguan**, **Tugas & Progres**, dan **Lembar Nilai** ditampilkan dengan state terkunci (*disabled / locked*):
         - Ikon gembok kecil (🔒) atau badge `"Terkunci"`.
         - Teks dan ikon dengan kontras redup (*muted opacity*).
         - Link dinonaktifkan (`href="javascript:void(0)"` atau cursor `not-allowed`) untuk mencegah navigasi sia-sia.
     - Jika peserta **sudah diterima**:
       - Navigasi berfungsi normal seperti biasa.

3. **Helper Model & Optimasi Relasi**:
   - Menambahkan method pembantu pada model `User`:
     - `public function hasPenempatanMagang(): bool`
     - Memeriksa apakah peserta memiliki minimal 1 penempatan dengan status `aktif` atau `selesai`.

4. **Pengujian Terotomasi (Automated Feature Testing)**:
   - Membuat test suite `tests/Feature/PesertaAksesFiturMagangTest.php`:
     - Memastikan peserta berstatus seleksi/pendaftar diblokir dari `peserta/tugas`, `peserta/logbook`, dan `peserta/nilai`.
     - Memastikan peserta yang sudah diterima tetap dapat mengakses ketiga fitur tersebut tanpa hambatan.
     - Memastikan seluruh test existing (seperti `PesertaFullPortalFeatureTest` dan `PesertaClassroomTugasLogbookTest`) tetap lulus 100%.

---

## 3. Rencana Langkah Implementasi

### Langkah 1: Tambahkan Helper pada Model `User`
- File: `app/Models/User.php`
- Menambahkan method `hasPenempatanMagang(): bool` yang memeriksa keberadaan relasi penempatan aktif atau selesai.

### Langkah 2: Buat Middleware `EnsurePesertaDiterima`
- File: `app/Http/Middleware/EnsurePesertaDiterima.php`
- Logika:
  - Cek apakah `$request->user()` adalah peserta.
  - Jika belum memiliki penempatan magang aktif/selesai:
    - Jika request JSON: `return response()->json(['message' => '...'], 403);`
    - Jika request biasa: `return redirect()->route('peserta.dashboard')->with('warning', 'Fitur ini terkunci. Logbook, Tugas, dan Lembar Nilai hanya dapat diakses setelah permohonan magang Anda disetujui dan ditempatkan ke unit kerja.');`

### Langkah 3: Daftarkan Middleware di `Kernel.php` & Terapkan di `routes/web.php`
- File: `app/Http/Kernel.php`
  - Tambahkan alias `'peserta.diterima' => \App\Http\Middleware\EnsurePesertaDiterima::class`.
- File: `routes/web.php`
  - Kelompokkan rute `tugas`, `logbook`, dan `nilai` di bawah middleware `peserta.diterima`.

### Langkah 4: Perbarui Tampilan Sidebar Peserta
- File: `resources/views/layouts/peserta.blade.php`
  - Periksa `$isDiterima = auth()->user()?->hasPenempatanMagang();`
  - Berikan styling kelas `nav-item-disabled` / `locked` dengan badge gembok halus jika belum diterima.

### Langkah 5: Buat Automated Tests & Verifikasi
- File: `tests/Feature/PesertaAksesFiturMagangTest.php`
- Jalankan PHPUnit untuk menguji proteksi rute dan memastikan semua rute terproteksi dengan baik.

---

## 4. Evaluasi & Pertimbangan
- **Apakah mengganggu fitur pendaftaran?** Tidak, rute pendaftaran (`peserta.pendaftaran.*`), profil, dan dashboard tetap dapat diakses penuh oleh calon peserta.
- **Bagaimana jika peserta baru saja diterima oleh admin?** Begitu admin menyetujui dan membuat penempatan, status otomatis terbuka tanpa perlu re-login.
