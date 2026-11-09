# Rencana Implementasi: Penyempurnaan Biodata & Profil Peserta, Edit Mode Lock, dan Prasyarat Pengajuan Magang

## 1. Latar Belakang & Kebutuhan Pengguna
Berdasarkan permintaan pengguna dan evaluasi antarmuka pada menu Dashboard Peserta:
1. **Hapus Judul Teks Hitam di Atas Profil (`media_1793982551720_3cd41ffe.png`)**:
   - Menghapus elemen `<h1 class="profil-page-title">Biodata & Profil Peserta</h1>` yang berwarna hitam pekat di atas banner identitas, sehingga halaman langsung dimulai dengan banner identitas biru navy yang bersih dan elegan sesuai acuan desain.
2. **Hapus Kotak Pernyataan Kepatuhan UU PDP (`media_1793982578210_3c95e5e0.png`)**:
   - Menghilangkan tampilan kotak persetujuan UU PDP khusus jenjang SMK dari form visual.
   - Tetap menjamin kepatuhan backend UU PDP (SEC-41) dengan menyertakan input tersembunyi `persetujuan_wali` bernilai `1` otomatis saat persetujuan umum dicentang, sehingga seluruh test suite otomatis tetap hijau tanpa regression.
3. **Mekanisme Kunci Edit (Edit Lock & Unlock)**:
   - Field pada card **"Informasi Akademik & Pendidikan"** dan **"Kontak Utama & Jalur Darurat"** secara default berada dalam mode **terkunci / read-only** (tidak dapat diketik atau dipilih).
   - Ketika tombol **"Edit"** diklik:
     - Field-field pada section tersebut terbuka (aktif/editable) dan kursor langsung fokus ke input pertama.
     - Tombol berubah teks menjadi **"Batal"** (dengan ikon batal/undo) dan gaya visual tombol berubah.
     - Jika pengguna mengklik **"Batal"**, seluruh nilai input dikembalikan ke kondisi awal dan terkunci kembali.
   - Saat form disubmit (`onsubmit`), JavaScript memastikan seluruh field di-unlock sementara agar seluruh nilai terkirim ke backend Laravel.
4. **Prasyarat Mutlak Pengajuan Magang (Profil Lengkap 100%)**:
   - Calon peserta magang **tidak dapat** memilih kuota ataupun membuka modal pendaftaran sebelum biodata profil dan kontak terisi lengkap (100% lengkap / seluruh field wajib tuntas).
   - Di halaman `/peserta/pendaftaran`:
     - Jika profil belum lengkap, tampilkan kartu peringatan interaktif yang jelas mengenai status kelengkapan profil dan arahkan langsung ke halaman biodata profil.
     - Bagian katalog kuota dan tombol "Daftar Sekarang" dikunci dengan aman.
   - Di backend (`PendaftaranRequest` & `PendaftaranController::store`):
     - Validasi server-side memastikan profil peserta terdaftar dan lengkap sebelum draf pendaftaran dapat dibuat.
5. **Efek Hover Interaktif & Modern**:
   - Memberikan transisi hover halus pada seluruh kartu section biodata (elevasi halus `translateY(-2px)`, bayangan lembut, dan highlight border).
   - Efek hover pada tombol "Edit" (warna aksen, rotasi halus ikon pensil).
   - Efek hover pada checklist kelengkapan dan input fields.
   - Efek hover pada avatar profil dan tombol upload foto.

---

## 2. Analisis File & Perubahan yang Diperlukan

### A. Frontend Blade Markup
1. **`resources/views/peserta/profil/show.blade.php`**:
   - Hapus `<h1 class="profil-page-title">Biodata & Profil Peserta</h1>`.
   - Hapus elemen `#wrapperPersetujuanWali`. Ganti dengan input hidden atau integrasikan ke checkbox persetujuan akhir.
   - Tambahkan atribut `readonly` / `data-locked="true"` dan class terkunci pada input dan select di `#sectionAkademik` dan `#sectionKontak`.
   - Perbarui tombol `#btnEditAkademik` dan `#btnEditKontak` dengan penanda status yang jelas.

### B. Frontend CSS
2. **`resources/css/pages/peserta/profil-show.css`**:
   - Hapus / sesuaikan styling `.profil-page-title`.
   - Tambahkan class state locked: `.profil-input:read-only`, `.profil-select.is-locked`, `.profil-textarea:read-only` dengan latar `#F8FAFC`, cursor `not-allowed`, dan border subtle.
   - Tambahkan class state active edit: highlight border saat sedang diedit.
   - Tambahkan efek hover interaktif pada `.profil-section-card`, `.profil-completeness-card`, `.profil-identity-banner`, `.profil-btn-edit`, `.profil-checklist-item`, dan `.profil-btn-upload-photo`.

### C. Frontend JavaScript
3. **`resources/js/pages/peserta/profil-show.js`**:
   - Implementasikan fungsi `initSectionEditLock()`:
     - Mengunci input secara default saat halaman dimuat.
     - Menyimpan snapshot nilai awal (`data-original-value`).
     - Menangani toggle tombol "Edit" menjadi "Batal" dan sebaliknya.
     - Membuka akses field dan memfokuskan input saat tombol Edit diklik.
     - Mengembalikan nilai jika Batal diklik.
     - Memastikan sebelum form disubmit, seluruh field diaktifkan agar nilai payload terkirim utuh ke server.

### D. Pendaftaran & Validasi Kelengkapan Profil
4. **`resources/views/peserta/pendaftaran/index.blade.php`**:
   - Perketat definisi `$profilLengkap` agar mencakup seluruh data wajib (`nim_nisn`, `institusi`, `jurusan_id`, `jenjang`, `no_hp`, serta data pendukung).
   - Tampilkan kartu peringatan kelengkapan yang informatif jika profil belum lengkap.
5. **`app/Http/Requests/PendaftaranRequest.php`**:
   - Tambahkan validasi kelengkapan profil pada validator server-side agar peserta yang belum melengkapi profil dicegah membuat pendaftaran di level backend.

---

## 3. Rencana Pengujian (Test & Quality Gates)

1. **Unit & Feature Testing (PHPUnit / Pest)**:
   - `tests/Feature/PesertaPendaftaranFlowTest.php`: Memastikan seluruh 13 test flow pendaftaran tetap lolos 100%.
   - `tests/Feature/PesertaUuPdpPersetujuanWaliTest.php`: Memastikan aturan UU PDP tetap hijau.
   - `tests/Feature/PesertaFullPortalFeatureTest.php`: Memastikan portal peserta tetap berfungsi optimal.
2. **Quality Gate Tooling**:
   - `php artisan view:clear`
   - `npm run build`
   - `./vendor/bin/pint --test`
3. **Manual Verification di Browser**:
   - Membuka `http://127.0.0.1:8000/peserta/profil` dan memeriksa:
     - Teks judul hitam sudah hilang.
     - Kotak kepatuhan sudah tidak ada.
     - Field akademik & kontak terkunci tidak bisa diketik sebelum klik Edit.
     - Setelah klik Edit, field bisa diisi dan tombol berubah jadi Batal.
     - Efek hover halus pada seluruh kartu dan tombol.
   - Membuka `http://127.0.0.1:8000/peserta/pendaftaran` untuk memverifikasi proteksi kelengkapan profil.

