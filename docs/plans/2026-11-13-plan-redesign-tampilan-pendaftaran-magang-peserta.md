# Rencana Kerja: Redesign Tampilan Pendaftaran Magang Peserta (Visual Parity)

**Dokumen Rencana:** `docs/plans/2026-11-13-plan-redesign-tampilan-pendaftaran-magang-peserta.md`  
**Referensi Desain:** Screenshot Desain Resmi Sleman Navy 3D Depth (`media_1794540146427_fed618de.jpg`)  
**Halaman Sasaran:** Portal Peserta - Pendaftaran Magang (`peserta.pendaftaran.index` / `resources/views/peserta/pendaftaran/index.blade.php`)  
**Status:** **DRAFT - MENUNGGU PERSETUJUAN PENGGUNA**

---

### 1. Ringkasan Kebutuhan & Tujuan

Pengguna meminta implementasi redesign antarmuka halaman **Pendaftaran Magang Peserta** agar memiliki **100% presisi visual (*visual parity*)** sesuai dengan gambar acuan yang diunggah.

Halaman ini terdiri dari 3 blok komponen visual utama:
1. **Hero Header Banner (`pendaftaran-hero`)**: Banner gradient Sleman Navy modern, badge identitas portal emas, judul tebal elegan, deskripsi informatif, serta 3 pill meta (Institusi/Sekolah, Jurusan, Jenjang) yang terstruktur rapi.
2. **Spotlight Card Pengajuan Magang Aktif (`pendaftaran-active-card`)**:
   - **Header Atas (Navy Band)**: Nomor registrasi (`#REG-00002`), judul status `"Pengajuan Magang Aktif Sedang Diproses"`, timestamp pengajuan, serta badge status pill emas `"✓ Selamat! Diterima Magang"`.
   - **Body Kartu (White Crisp Card)**: Grid 4 metrik ringkas:
     - 1. *Bulan Magang* (November 2026) dengan icon kalender biru lembut.
     - 2. *Jurusan Kuota Terdaftar* (Teknik Komputer dan Jaringan) dengan icon stack layers.
     - 3. *Rencana Durasi Magang* (3 Bulan) dengan icon jam/clock.
     - 4. *Status Penempatan (Highlighted Amber/Gold Card)*: Latar krem lembut `#FFFBEB`, border kuning `#FCD34D`, icon instansi emas `#FFC300`, dan nama bidang penempatan.
   - **Dashed Divider & Center CTA Button**: Garis putus-putus halus dan tombol tengah emas berikon berkas `"Buka Berkas & Detail Pengajuan"`.
3. **Accordion Card Riwayat Pendaftaran (`riwayat-card-container`)**:
   - Header putih dengan aksen **garis tegak emas (`|`)** di sebelah kiri judul `"Riwayat Pendaftaran Magang"` dan tombol panah lipat (*accordion chevron*) di sebelah kanan.
   - Baris riwayat bergaris tepi lembut dengan icon centang navy, kode reg + bulan, ringkasan jurusan & durasi, pill status `"Diterima"` emas, serta tombol outline `"Detail >"`.

---

### 2. Analisis Kesenjangan (Gap Analysis) Antara Kode Saat Ini vs Gambar

| Elemen Antarmuka | Kondisi Saat Ini | Target Sesuai Gambar Acuan |
|---|---|---|
| **Hero Ornament** | Ada SVG lingkaran konsentris besar (`.hero-rings-ornament`) di latar belakang yang berpotensi ramai | Dihilangkan/disederhanakan sehingga tampilan hero bersih, fokus pada teks, dan persis dengan acuan |
| **Hero Meta Pills** | Padding dan icon standar, border kadang tidak seragam | Pill transparan elegan `rgba(0,0,0,0.22)` dengan border halus `rgba(255,255,255,0.18)`, icon warna `#FFC300` |
| **Active Card Header** | Reg badge menggunakan font JetBrains Mono dengan icon file | Reg badge pill rapi `#REG-XXXXX` border `#FFC300` warna teks emas bersih tanpa icon pengganggu |
| **Badge Diterima Magang** | Ukuran padding dan rounded pill perlu penyelarasan | Pill kuning cerah `#FFC300` dengan teks navy pekat `#002B54` dan ikon centang tebal `✓` |
| **Grid 4 Metrik Aktif** | Card penempatan memiliki styling highlight standar | Card penempatan dibuat menonjol dengan latar `#FEF9C3` / `#FFFBEB`, border `#FCD34D`, dan box icon kotak emas solid `#FFC300` |
| **CTA Button Buka Berkas** | Terkadang terpengaruh lebar penuh atau alignment kiri | Tombol berada di tengah (*center aligned*) dengan latar kuning emas solid `#FFC300`, teks gelap bold, dan border-radius 8-10px |
| **Riwayat Title Aksen** | Judul riwayat polos tanpa aksen kuning | Menambahkan batang aksen vertikal kuning emas (`#FFC300`) persis di sisi kiri judul `Riwayat Pendaftaran Magang` |
| **Riwayat Row Item** | Checkmark circle / icon berbeda | Kotak navy rounded (`#003566`) dengan ikon centang `✓` kontras, badge `#REG` soft, tanggal, dan tombol outline `Detail >` |

---

### 3. Rencana Perubahan Berkas

Sesuai aturan pengembangan repositori (`.agents/rules/AGENTS.md`):
- Seluruh styling ditempatkan di `resources/css/pages/peserta/pendaftaran-index.css`.
- File Blade `resources/views/peserta/pendaftaran/index.blade.php` hanya mengalami penyesuaian markup semantik minimal (tanpa inline style/script).
- Aset frontend dikompilasi melalui Vite (`npm run build`).

#### Berkas 1: `resources/views/peserta/pendaftaran/index.blade.php`
- Menghapus ornamen SVG lingkaran konsentris pada hero banner agar bersih dan tidak ramai.
- Menyelaraskan struktur HTML pada header riwayat dengan menambahkan elemen aksen batang vertikal kuning:
  ```html
  <div class="riwayat-header">
      <div class="riwayat-header-title-wrap">
          <span class="riwayat-accent-bar" aria-hidden="true"></span>
          <h2 class="riwayat-title">Riwayat Pendaftaran Magang</h2>
      </div>
      <button type="button" class="riwayat-chevron-btn" id="btnToggleRiwayat" ...>
          ...
      </button>
  </div>
  ```
- Menyelaraskan reg badge pada Active Card Topbar agar menampilkan `#REG-XXXXX` yang bersih.
- Memastikan 4 kartu metrik memiliki struktur label/value dan icon container yang presisi.

#### Berkas 2: `resources/css/pages/peserta/pendaftaran-index.css`
- **Hero Banner**:
  - Penyesuaian gradien navy 3D: `linear-gradient(135deg, #003566 0%, #01347C 100%)`.
  - Radius sudut `16px`, padding yang proporsional (`28px 32px`).
  - Badge tag `• PORTAL PENDAFTARAN MAGANG DISKOMINFOSANDI SLEMAN` dengan titik emas bercahaya.
  - Penyelarasan meta pills: Sekolah/Institusi, Jurusan, Jenjang.
- **Spotlight Active Card**:
  - Header navy topbar dengan background `#002B54` s/d `#003566`.
  - Badge nomor registrasi dengan border tipis emas `#FFC300`.
  - Badge status `"Selamat! Diterima Magang"` dengan background `#FFC300` solid, teks `#002B54`, font weight 800, padding `8px 20px`, rounded full.
  - Kartu metrik:
    - Card 1, 2, 3: background `#FFFFFF`, border `#E2E8F0`, icon container `#EFF6FF` dengan icon navy.
    - Card 4 (Penempatan): background `#FFFBEB`, border `#FCD34D`, icon container `#FFC300` solid dengan icon `#002B54`.
  - Tombol CTA Tengah: background `#FFC300`, padding `10px 24px`, border-radius `8px`, teks navy bold.
- **Accordion Riwayat Card**:
  - Batang aksen vertikal `.riwayat-accent-bar`: `width: 3.5px; height: 20px; background: #FFC300; border-radius: 2px;`.
  - Tombol accordion chevron yang bersih.
  - Baris riwayat: kotak navy `#003566` dengan centang emas/putih di sisi kiri, layout flexbox yang teratur, badge status pill emas `"Diterima"`, dan tombol outline `"Detail >"`.
  - Responsivitas mobile: grid 4 kolom bertransisi rapi ke 2 kolom (tablet) dan 1 kolom (smartphone).

---

### 4. Rencana Pengujian & Quality Gate

1. **Vite Compilation**:
   - Menjalankan `npm run build` untuk memverifikasi seluruh stylesheet CSS terkumpul tanpa error.
2. **Feature Tests**:
   - Menjalankan `tests/Feature/PendaftaranPesertaTest.php` (6 tests).
   - Menjalankan `tests/Feature/PesertaPendaftaranFlowTest.php` (13 tests).
   - Menjalankan `tests/Feature/LandingPageToRegistrationPrefillTest.php` (5 tests).
   - Memastikan seluruh assertion tetap `100% PASS` tanpa regresi.
3. **Format & Code Standards**:
   - `vendor/bin/pint --test` untuk memastikan standar PSR-12 dan gaya penulisan kode tetap bersih.

---

### 5. Konfirmasi Eksekusi

Rencana ini siap dieksekusi segera setelah Anda memberikan konfirmasi persetujuan (misal: ketik **"gas"**, **"lanjut"**, atau **"ok"**).

