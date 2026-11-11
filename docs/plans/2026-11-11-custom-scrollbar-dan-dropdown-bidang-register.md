# Master Plan: Kustomisasi Scrollbar Tema & Penambahan Dropdown Bidang pada Halaman Register

**Tanggal Rilis Dokumen**: 2026-11-11  
**Status**: DRAFT FOR APPROVAL (Menunggu Persetujuan Pengguna)  
**Tingkat Prioritas**: P1 (Penyelarasan Desain UI & Alur Pilihan Peminatan Magang)  
**Terkait Isu / Bukti**:
- [Screenshot 1: Scrollbar Panel Form Register Default](file:///C:/Users/galan/.gemini/antigravity/brain/6450cbfa-233f-47e6-9528-354e10ad9741/.user_uploaded/media_1794394562148_be9841ab.png)
- [Screenshot 2: Scrollbar Halaman / Window Latar Gelap Default](file:///C:/Users/galan/.gemini/antigravity/brain/6450cbfa-233f-47e6-9528-354e10ad9741/.user_uploaded/media_1794394578760_bc2f0139.png)
- [Screenshot 3: Urutan Dropdown Jenjang Langsung ke Jurusan](file:///C:/Users/galan/.gemini/antigravity/brain/6450cbfa-233f-47e6-9528-354e10ad9741/.user_uploaded/media_1794394618016_993fff86.png)

---

## 1. Tujuan
1. **Kustomisasi Scrollbar Tema (Anti-Default Browser)**:
   - Mengubah scrollbar panel kanan Register (`.auth-panel-scroll`) agar memiliki estetika ramping, thumb `#AAB6C8` dengan efek hover `#003566` yang elegan, border-radius pill (`999px`), serta track transparan tanpa panah kaku.
   - Mengubah scrollbar jendela utama browser (`html`, `body`) pada halaman auth depth agar serasi dengan latar belakang radial gradient gelap (`#003566` ke `#01183A`) dengan aksen emas halus (`#FFC300` / rgba) dan track navy gelap.
2. **Penyisipan Dropdown Bidang**:
   - Menambahkan field pemilihan **Bidang Peminatan / Penempatan** tepat di antara dropdown **Jenjang Pendidikan** dan **Jurusan / Program Studi** di Blok C formulir Register.
   - Menggunakan ikon SVG Briefcase (bidang tugas) yang selaras dengan seluruh ikon form lainnya.
   - Mendukung opsi dinamis dari `$bidangList` (disediakan controller `AuthController@showRegister`) dengan fallback opsi resmi Kominfo Sleman (Aptika, IKP, E-Government, Jaringan TIK).

---

## 2. Asumsi & Pertanyaan Terbuka
- **Asumsi 1**: Kustomisasi scrollbar browser window hanya diterapkan secara ter-scoped pada halaman bertema Auth Depth agar tidak merusak scrollbar cockpit/dashboard admin lainnya yang sudah stabil.
- **Asumsi 2**: Dropdown Bidang diisi dengan daftar bidang dinamis dari controller jika ada, serta memiliki fallback opsi representatif Kominfo jika view dipanggil tanpa data relasi database.
- **Asumsi 3**: Filter dinamis jurusan berbasis bidang adalah peningkatan UI visual bertahap; jika bidang dipilih, jurusan tetap dapat dipilih dengan fleksibel tanpa mengunci form jika peserta ingin memilih jurusan umum.

---

## 3. Pendekatan Teknis

### Opsi A (Rekomendasi Utama): Scoped CSS Variables + Hybrid Blade Dropdown
- **Kelebihan**:
  - Scrollbar diatur melalui CSS murni di `resources/css/pages/auth/auth-depth.css` (mematuhi aturan `AGENTS.md` bahwa styling tidak boleh ditulis di tag `<style>` Blade).
  - Menggunakan standar modern `scrollbar-width` dan `scrollbar-color` untuk Firefox, serta pseudo-class `::-webkit-scrollbar`, `::-webkit-scrollbar-track`, dan `::-webkit-scrollbar-thumb` untuk Chromium (Chrome, Edge, Opera, Brave).
  - Dropdown bidang menggunakan markup semantik Blade dengan kelas `.fld` yang mewarisi tinggi 50px, border `#AAAAAA`, radius 10px, chevron kanan, dan ikon SVG kiri 18px stroke `#5F6B76`.
- **Kekurangan**: Tidak ada.

### Opsi B: Custom JavaScript Virtual Scrollbar Library (misal OverlayScrollbars / SimpleBar)
- **Kelebihan**: Scrollbar tampak identik pixel-perfect di semua browser lama.
- **Kekurangan**: Menambah bundle library eksternal (bertentangan dengan prinsip vanilla stack dan performa instan proyek MagKom).

**Pilihan**: **Opsi A**.

---

## 4. Rincian Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/css/pages/auth/auth-depth.css` | Modify | 1. Tambahkan styling scrollbar window (`html, body`) tema dark radial navy & aksen emas.<br>2. Sempurnakan styling scrollbar `.auth-panel-scroll` (thumb pill `#AAB6C8`, hover `#003566`, track margin). |
| `resources/views/auth/register.blade.php` | Modify | Sisipkan blok `<div class="fld-group">` untuk `select#bidang` lengkap dengan ikon SVG briefcase, opsi dinamis/fallback, dan teks bantuan di antara `jenjang` dan `jurusan`. |
| `tests/Feature/AuthDepthVisualParityTest.php` | Modify | Perbarui test assertion untuk memverifikasi keberadaan field `bidang`, label, dan urutannya. |

---

## 5. Langkah-Langkah Implementasi

- [ ] **Langkah 1: Perbarui CSS Scrollbar di `resources/css/pages/auth/auth-depth.css`**
  - Definisikan kustomisasi scrollbar untuk `html, body` khusus halaman auth:
    ```css
    html, body {
      scrollbar-width: thin;
      scrollbar-color: rgba(255, 195, 0, 0.4) #01183A;
    }
    html::-webkit-scrollbar, body::-webkit-scrollbar {
      width: 8px;
    }
    html::-webkit-scrollbar-track, body::-webkit-scrollbar-track {
      background: #01183A;
    }
    html::-webkit-scrollbar-thumb, body::-webkit-scrollbar-thumb {
      background: rgba(255, 195, 0, 0.35);
      border-radius: 999px;
      border: 2px solid #01183A;
    }
    html::-webkit-scrollbar-thumb:hover, body::-webkit-scrollbar-thumb:hover {
      background: #FFC300;
    }
    ```
  - Pertegas selector `.auth-panel-scroll` untuk panel Register:
    ```css
    .auth-panel-scroll {
      scrollbar-width: thin;
      scrollbar-color: #AAB6C8 transparent;
    }
    .auth-panel-scroll::-webkit-scrollbar {
      width: 6px;
    }
    .auth-panel-scroll::-webkit-scrollbar-track {
      background: rgba(0, 53, 102, 0.04);
      border-radius: 999px;
      margin: 6px 0;
    }
    .auth-panel-scroll::-webkit-scrollbar-thumb {
      background-color: #AAB6C8;
      border-radius: 999px;
    }
    .auth-panel-scroll::-webkit-scrollbar-thumb:hover {
      background-color: #003566;
    }
    ```

- [ ] **Langkah 2: Sisipkan Dropdown Bidang di `resources/views/auth/register.blade.php`**
  - Tepat setelah blok `select#jenjang` dan sebelum blok `select#jurusan`:
    ```html
    <div>
      <div style="position: relative">
        <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#5F6B76" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 16px; top: 16px; pointer-events: none">
          <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
          <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
        </svg>
        <select id="bidang" class="fld" aria-label="Bidang penempatan atau peminatan" aria-describedby="hint-bidang" style="width: 100%; box-sizing: border-box; height: 50px; padding: 0 44px 0 46px; border: 1px solid #AAAAAA; border-radius: 10px; font-family: inherit; font-size: 15px; color: #212529; background: #FFFFFF; appearance: none; -webkit-appearance: none; cursor: pointer">
          <option value="">-- Pilih Bidang Peminatan / Penempatan --</option>
          @if(isset($bidangList) && count($bidangList) > 0)
            @foreach($bidangList as $b)
              <option value="{{ $b->id }}">{{ $b->nama_bidang }}</option>
            @endforeach
          @else
            <option>Bidang Aplikasi Informatika (Aptika)</option>
            <option>Bidang Informasi dan Komunikasi Publik (IKP)</option>
            <option>Bidang Tata Kelola E-Government</option>
            <option>Bidang Jaringan dan Infrastruktur TIK</option>
          @endif
        </select>
        <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#212529" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; right: 16px; top: 16px; pointer-events: none">
          <path d="M6 9l6 6 6-6"></path>
        </svg>
      </div>
      <p id="hint-bidang" style="margin: 6px 0 0; font-size: 12.5px; line-height: 1.4; color: #5F6B76">Pilih bidang penempatan di Dinas Kominfo yang sesuai dengan peminatan magang Anda.</p>
    </div>
    ```

- [ ] **Langkah 3: Perbarui Assertion Test di `tests/Feature/AuthDepthVisualParityTest.php`**
  - Tambahkan assertion bahwa teks `-- Pilih Bidang Peminatan / Penempatan --` dan field `select#bidang` terdeteksi di view register.

- [ ] **Langkah 4: Jalankan Kompilasi & Quality Gates**
  - Jalankan `npm run build`.
  - Jalankan `vendor/bin/pint --test`.
  - Jalankan `phpstan analyse`.
  - Jalankan `php artisan test tests/Feature/AuthDepthVisualParityTest.php`.

---

## 6. Strategi Testing
- **Visual Inspection**: Memastikan scrollbar panel form dan scrollbar window tidak lagi berwarna abu-abu kaku default OS.
- **Form Layout**: Memastikan urutan vertikal: Jenjang -> Bidang -> Jurusan -> Sekolah -> NIM tersusun rapi dengan gap 12px yang presisi.
- **Automated Tests**:
  - `php artisan test tests/Feature/AuthDepthVisualParityTest.php`
  - `php artisan test tests/Feature/PendaftaranPesertaTest.php`

---

## 7. Risiko & Mitigasi
- **Risiko**: Nilai tinggi panel register tetap `800px` dan penambahan 1 field dropdown baru menambah konten vertikal sekitar 80px.
- **Mitigasi**: Panel kanan register sudah memiliki `overflow-y: auto`, sehingga penambahan dropdown bidang ini langsung tertampung dengan sangat mulus oleh scrollbar kustom yang baru!

---

## 8. Kriteria Selesai (Definition of Done)
- [ ] Scrollbar panel kanan register berpenampilan ramping (#AAB6C8 / hover #003566) tanpa panah default OS.
- [ ] Scrollbar window halaman auth depth bertema dark navy-gold terintegrasi dengan background kanvas.
- [ ] Field dropdown Bidang terpasang di antara Jenjang dan Jurusan dengan ikon briefcase dan teks hint informatif.
- [ ] Kompilasi `npm run build` sukses.
- [ ] Seluruh PHPUnit Feature Test lolos 100%.

