# Rencana Kerja: Penyederhanaan Tampilan Empty State "Tidak Ada Berkas"

**Tanggal:** 7 November 2026  
**Target File:** `resources/views/admin/pendaftaran/index.blade.php`  
**Tujuan:** Menyederhanakan tampilan keadaan kosong (*empty state*) ketika data berkas pendaftaran tidak ditemukan / kosong, agar tidak terlalu ramai dan lebih ringkas (*clean & distraction-free*) sesuai screenshot permintaan pengguna.

---

## 1. Analisis Kebutuhan Pengguna

### Berdasarkan 3 Gambar yang Diunggah Pengguna:
1. **Gambar 1 (`media_1794047166971_e1b452a0.png`)**:
   Menampilkan kondisi *empty state* saat ini yang memuat:
   - Ikon kaca pembesar di dalam lingkaran biru
   - Judul: `Tidak Ada Berkas yang Cocok`
   - Paragraf deskripsi: `Tidak ditemukan berkas pendaftaran calon peserta magang yang sesuai dengan kriteria filter atau pencarian saat ini.`
   - Tombol: `Reset Semua Filter`
2. **Gambar 2 (`media_1794047198250_c3e7a00d.png`)**:
   Potongan teks paragraf:
   `Tidak ditemukan berkas pendaftaran calon peserta magang yang sesuai dengan kriteria filter atau pencarian saat ini.`
   *(Pengguna meminta teks ini **dihapus** agar tampilan tidak ramai).*
3. **Gambar 3 (`media_1794047219817_b27e1b22.png`)**:
   Potongan teks judul:
   `Tidak Ada Berkas yang Cocok`
   *(Pengguna meminta teks ini **diubah menjadi: `Tidak Ada Berkas`**).*

---

## 2. Rencana Perubahan Kode

### Lokasi Target
[`resources/views/admin/pendaftaran/index.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/pendaftaran/index.blade.php) pada blok `@if($pendaftaranList->isEmpty())`.

### Markup Sebelum:
```blade
            @if($pendaftaranList->isEmpty())
                <div style="padding: 4rem 1.5rem; text-align: center; color: #64748B;">
                    <div style="width: 56px; height: 56px; background: #EFF6FF; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem auto; color: #0641CC;">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    </div>
                    <h3 style="font-size: 1.1rem; font-weight: 800; color: #0F172A; margin: 0 0 0.35rem 0;">
                        Tidak Ada Berkas yang Cocok
                    </h3>
                    <p style="font-size: 0.85rem; max-width: 480px; margin: 0 auto 1.25rem auto; line-height: 1.5;">
                        Tidak ditemukan berkas pendaftaran calon peserta magang yang sesuai dengan kriteria filter atau pencarian saat ini.
                    </p>
                    <a href="{{ route('admin.pendaftaran.index') }}" class="adm-btn-tinjau" style="display: inline-flex;">
                        Reset Semua Filter
                    </a>
                </div>
            @else
```

### Markup Sesudah:
```blade
            @if($pendaftaranList->isEmpty())
                <div style="padding: 3.5rem 1.5rem; text-align: center; color: #64748B;">
                    <div style="width: 52px; height: 52px; background: #EFF6FF; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem auto; color: #0641CC;">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    </div>
                    <h3 style="font-size: 1.1rem; font-weight: 800; color: #0F172A; margin: 0 0 1.25rem 0;">
                        Tidak Ada Berkas
                    </h3>
                    <a href="{{ route('admin.pendaftaran.index') }}" class="adm-btn-tinjau" style="display: inline-flex;">
                        Reset Semua Filter
                    </a>
                </div>
            @else
```

---

## 3. Analisis Dampak & Verifikasi

1. **Dampak UI / UX**:
   - Area kosong menjadi lebih bersih (*clean*), padat, dan tidak bertele-tele.
   - Tetap menyediakan tombol aksi jelas `Reset Semua Filter` agar verifikator dapat mengembalikan tabel ke kondisi awal dengan satu klik.
2. **Dampak Backend & Database**:
   - Tidak ada perubahan backend atau query.
3. **Kepatuhan CI & Testing**:
   - Tidak ada feature test yang bergantung pada teks deskripsi yang dihapus.
   - Seluruh test suite (600 test) dipastikan tetap 100% hijau (*pass*).

---

## 4. Rencana Eksekusi & Quality Gates

1. **Implementasi Perubahan**: Mengedit blok empty state di `resources/views/admin/pendaftaran/index.blade.php`.
2. **Linting Code Style**:
   ```bash
   vendor\bin\pint.bat --test
   ```
3. **PHPUnit Feature Tests**:
   ```bash
   d:\laragon\bin\php\php-8.3.33-Win32-vs16-x64\php.exe artisan test --filter "AdminVerifikasi|AdminChildPages|DashboardModulesRenderingTest"
   ```
4. **Vite Build**:
   ```bash
   npm run build
   ```
5. **Dev Server Verification**:
   ```bash
   curl.exe -I -s http://127.0.0.1:8000/admin/pendaftaran
   ```

